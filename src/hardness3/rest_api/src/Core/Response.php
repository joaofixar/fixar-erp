<?php

declare(strict_types=1);

namespace RestApi\Core;

use stdClass;

class Response
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     * @return array{status:int,payload:array<string,mixed>}
     */
    public static function success(array $data, array $meta = [], int $status = 200): array
    {
        return [
            'status' => $status,
            'payload' => [
                'success' => true,
                'data' => $data,
                'meta' => self::normalizeObject($meta),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $details
     * @param array<string, mixed> $meta
     * @return array{status:int,payload:array<string,mixed>}
     */
    public static function error(string $code, string $message, array $details = [], array $meta = [], int $status = 400): array
    {
        return [
            'status' => $status,
            'payload' => [
                'success' => false,
                'error' => [
                    'code' => $code,
                    'message' => $message,
                    'details' => self::normalizeObject($details),
                ],
                'meta' => self::normalizeObject($meta),
            ],
        ];
    }

    /**
     * @return array{status:int,payload:string,headers:array<string,string>,raw:bool}
     */
    public static function raw(string $content, string $contentType, int $status = 200, array $headers = []): array
    {
        return [
            'status' => $status,
            'payload' => $content,
            'headers' => array_merge([
                'Content-Type' => $contentType,
            ], $headers),
            'raw' => true,
        ];
    }

    /**
     * @param array<string, mixed>|string $payload
     */
    public static function send($payload, int $status, array $headers = []): void
    {
        http_response_code($status);

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: no-referrer');

        if (is_string($payload)) {
            foreach ($headers as $name => $value) {
                header($name . ': ' . $value);
            }

            if (!isset($headers['Content-Type'])) {
                header('Content-Type: text/plain; charset=utf-8');
            }

            echo $payload;
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array<string, mixed> $value
     * @return array<string, mixed>|stdClass
     */
    private static function normalizeObject(array $value)
    {
        return $value === [] ? new stdClass() : $value;
    }
}
