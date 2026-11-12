<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

final class CurlHttpClient implements HttpClientInterface
{
    /** @var int */ private $connectTimeoutMs; /** @var int */ private $timeoutMs; /** @var int */ private $maxResponseBytes;
    public function __construct(int $connectTimeoutMs = 5000, int $timeoutMs = 15000, int $maxResponseBytes = 65536)
    {
        if ($connectTimeoutMs < 1 || $timeoutMs < $connectTimeoutMs || $maxResponseBytes < 1) { throw new \InvalidArgumentException('Webhook HTTP timeout or response limit is invalid.'); }
        $this->connectTimeoutMs = $connectTimeoutMs; $this->timeoutMs = $timeoutMs; $this->maxResponseBytes = $maxResponseBytes;
    }
    public function post(ValidatedDestination $destination, string $rawBody, array $headers): HttpResult
    {
        if (!function_exists('curl_init')) { throw new \RuntimeException('Webhook HTTP transport requires cURL.'); }
        $handle = curl_init(); if ($handle === false) { throw new \RuntimeException('Could not initialize webhook HTTP transport.'); }
        $headerLines = array(); foreach ($headers as $name => $value) { $headerLines[] = $name . ': ' . $value; }
        // libcurl keeps one DNS cache entry per host:port.  Keep every
        // validated address in that one entry so IPv4 remains available when
        // an otherwise-public IPv6 address has no route on this host.
        $addresses = array(); foreach ($destination->getAddresses() as $address) { $addresses[] = strpos($address, ':') === false ? $address : '[' . $address . ']'; }
        $resolve = array($destination->getHost() . ':' . $destination->getPort() . ':' . implode(',', $addresses));
        $responseBytes = 0; $tooLarge = false; $limit = $this->maxResponseBytes; $responseHeaders = array();
        curl_setopt_array($handle, array(
            CURLOPT_URL => $destination->getUrl(), CURLOPT_POST => true, CURLOPT_POSTFIELDS => $rawBody, CURLOPT_HTTPHEADER => $headerLines,
            CURLOPT_RETURNTRANSFER => false, CURLOPT_FOLLOWLOCATION => false, CURLOPT_MAXREDIRS => 0, CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT_MS => $this->connectTimeoutMs, CURLOPT_TIMEOUT_MS => $this->timeoutMs, CURLOPT_NOSIGNAL => true,
            CURLOPT_RESOLVE => $resolve, CURLOPT_PROXY => '', CURLOPT_NOPROXY => '*', CURLOPT_HEADER => false,
            CURLOPT_HEADERFUNCTION => static function ($curl, string $line) use (&$responseHeaders): int {
                $parts = explode(':', $line, 2); if (count($parts) === 2 && strcasecmp(trim($parts[0]), 'Retry-After') === 0) { $responseHeaders['Retry-After'] = trim($parts[1]); } return strlen($line);
            },
            CURLOPT_WRITEFUNCTION => static function ($curl, string $data) use (&$responseBytes, &$tooLarge, $limit): int {
                $length = strlen($data); if ($responseBytes + $length > $limit) { $tooLarge = true; return 0; } $responseBytes += $length; return $length;
            },
        ));
        $started = microtime(true); $ok = curl_exec($handle); $durationMs = (int) round((microtime(true) - $started) * 1000);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE); $error = $ok === false ? ($tooLarge ? 'response_too_large' : 'curl_' . curl_errno($handle)) : null;
        curl_close($handle);
        return new HttpResult($status > 0 ? $status : null, $error, $durationMs, $responseBytes, $responseHeaders);
    }
}
