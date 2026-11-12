<?php

declare(strict_types=1);

namespace HardnessWebhooks\Core;

use DateTimeZone;
use RuntimeException;

/**
 * Isolated runtime entry point for the Webhooks module.
 *
 * Persistence, tenant resolution and delivery processing are deliberately
 * introduced by their respective implementation tasks.
 */
final class Bootstrap
{
    private string $rootPath;
    private string $timezone;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/');
        $this->timezone = (string) (getenv('WEBHOOKS_TIMEZONE') ?: 'America/Sao_Paulo');
    }

    public function boot(): self
    {
        try {
            new DateTimeZone($this->timezone);
        } catch (\Exception $exception) {
            throw new RuntimeException('WEBHOOKS_TIMEZONE is invalid.', 0, $exception);
        }

        date_default_timezone_set($this->timezone);

        return $this;
    }

    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }
}
