<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

use HardnessWebhooks\Secrets\SecretVersionService;
use HardnessWebhooks\Exception\SecretUnavailableException;
use HardnessWebhooks\Exception\DestinationAuthenticationUnavailableException;
use HardnessWebhooks\DestinationAuthentication\DestinationAuthenticationService;
use HardnessWebhooks\Security\HmacSigner;

final class WebhookHttpDispatcher implements DeliveryDispatcherInterface
{
    /** @var DeliveryRepository */ private $repository; /** @var SecretVersionService */ private $secrets; /** @var DestinationValidator */ private $validator; /** @var HttpClientInterface */ private $client; /** @var RetryPolicy */ private $policy; /** @var DestinationAuthenticationService */ private $destinationAuthentication;
    public function __construct(DeliveryRepository $repository, SecretVersionService $secrets, DestinationValidator $validator, HttpClientInterface $client, ?RetryPolicy $policy = null, ?DestinationAuthenticationService $destinationAuthentication = null)
    { $this->repository = $repository; $this->secrets = $secrets; $this->validator = $validator; $this->client = $client; $this->policy = $policy ?: new RetryPolicy(); $this->destinationAuthentication=$destinationAuthentication; }
    /** @param array<string,mixed> $delivery */
    public function dispatch(\mysqli $connection, string $workerId, array $delivery): void
    {
        $id = (int) ($delivery['WHK004_Id'] ?? 0); $attempt = (int) ($delivery['WHK004_Attempt_Count'] ?? 0);
        if ($id < 1 || $attempt < 1 || !$this->repository->ownsLease($connection, $id, $workerId)) { return; }
        if (!$this->repository->isDispatchEligible($connection, $id, $workerId)) {
            $this->repository->cancelClaimed($connection, $id, $workerId, 'endpoint_disabled');
            return;
        }
        try {
            $destination = $this->validator->validate((string) ($delivery['WHK004_Destination_URL'] ?? ''));
            $rawBody = (string) ($delivery['WHK003_Payload'] ?? '');
            $headers = HmacSigner::baseHeaders((string) $delivery['WHK003_Event_Id'], (string) $delivery['WHK004_Delivery_Id'], (string) $delivery['WHK003_Type'], (string) $delivery['WHK003_Schema_Version']);
            if (($delivery['WHK004_WHK007_Id'] ?? null) !== null && (int) $delivery['WHK004_WHK007_Id'] > 0) {
                $timestamp = time(); $secret = $this->secrets->loadActiveSecretForDelivery($connection, $id); $signature = HmacSigner::sign($secret, $timestamp, $rawBody);
                $headers['Hardness-Timestamp'] = (string) $timestamp; $headers['Hardness-Signature'] = $signature;
            }
            $authentication = $this->destinationAuthentication()->loadForDelivery($connection, $id);
            if ($authentication['type'] === DestinationAuthenticationService::TYPE_BEARER) { $headers['Authorization'] = 'Bearer ' . $authentication['token']; }
            $result = $this->client->post($destination, $rawBody, $headers);
            $status = $result->getStatus(); $error = $result->getError() === null ? null : 'transport_error'; $classification = $this->policy->resultName($status, $error);
            $next = $classification === 'http_success' ? 'D' : ($this->policy->isRetryable($status, $error) && $this->policy->retryDelay($attempt, $status === 429 ? $result->getHeader('Retry-After') : null) !== null ? 'R' : 'X');
            $delay = $next === 'R' ? $this->policy->retryDelay($attempt, $status === 429 ? $result->getHeader('Retry-After') : null) : null;
            $this->repository->recordHttpResult($connection, $id, $workerId, $attempt, $classification, $status, $error, $result->getDurationMs(), $result->getResponseBytes(), $next, $delay);
        } catch (DestinationValidationException $exception) {
            $this->repository->recordHttpResult($connection, $id, $workerId, $attempt, 'security_error', null, 'destination_rejected', 0, 0, 'X', null);
        } catch (SecretUnavailableException $exception) {
            $this->repository->cancelClaimed($connection, $id, $workerId, 'secret_unavailable');
        } catch (DestinationAuthenticationUnavailableException $exception) {
            $this->repository->cancelClaimed($connection, $id, $workerId, 'destination_auth_unavailable');
        } catch (\Throwable $exception) {
            $delay = $this->policy->retryDelay($attempt, null);
            $this->repository->recordHttpResult($connection, $id, $workerId, $attempt, 'transport_error', null, 'transport_error', 0, 0, $delay === null ? 'X' : 'R', $delay);
        }
    }
    private function destinationAuthentication(): DestinationAuthenticationService
    { if(!$this->destinationAuthentication instanceof DestinationAuthenticationService)$this->destinationAuthentication=new DestinationAuthenticationService();return $this->destinationAuthentication; }
}
