<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

interface HttpClientInterface
{
    /** @param array<string,string> $headers */
    public function post(ValidatedDestination $destination, string $rawBody, array $headers): HttpResult;
}
