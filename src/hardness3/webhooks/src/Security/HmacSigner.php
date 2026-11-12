<?php

declare(strict_types=1);

namespace HardnessWebhooks\Security;

final class HmacSigner
{
    public const SIGNATURE_VERSION = 'v1';

    public static function sign(string $secret, int $timestamp, string $rawBody): string
    {
        if ($secret === '') {
            throw new \InvalidArgumentException('Webhook HMAC secret cannot be empty.');
        }

        return self::SIGNATURE_VERSION . '=' . hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
    }

    /** @return array<string,string> */
    public static function baseHeaders(string $eventId, string $deliveryId, string $eventType, string $schemaVersion): array
    {
        return array(
            'Hardness-Event-Id' => $eventId,
            'Hardness-Delivery-Id' => $deliveryId,
            'Hardness-Event-Type' => $eventType,
            'Hardness-Schema-Version' => $schemaVersion,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Hardness-Webhooks/1.0',
        );
    }

    /** @return array<string,string> */
    public static function headers(string $eventId, string $deliveryId, string $eventType, string $schemaVersion, int $timestamp, string $signature): array
    {
        $headers = self::baseHeaders($eventId, $deliveryId, $eventType, $schemaVersion);
        $headers['Hardness-Timestamp'] = (string) $timestamp;
        $headers['Hardness-Signature'] = $signature;
        return $headers;
    }
}
