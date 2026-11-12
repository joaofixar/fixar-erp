<?php

declare(strict_types=1);

namespace RestApi\Core;

use RuntimeException;

class Config
{
    private string $configPath;

    /** @var array<string, array<string, mixed>> */
    private array $items = [];

    public function __construct(string $configPath)
    {
        $this->configPath = rtrim($configPath, '/');
    }

    /**
     * @return array<string, mixed>
     */
    public function load(string $name): array
    {
        if (!isset($this->items[$name])) {
            $file = $this->configPath . '/' . $name . '.php';
            $localFile = $this->configPath . '/' . $name . '.local.php';

            if (!file_exists($file)) {
                throw new RuntimeException(sprintf('Configuration file not found: %s', $file));
            }

            $config = require $file;
            if (!is_array($config)) {
                throw new RuntimeException(sprintf('Configuration file must return an array: %s', $file));
            }

            if (file_exists($localFile)) {
                $localConfig = require $localFile;
                if (!is_array($localConfig)) {
                    throw new RuntimeException(sprintf('Configuration file must return an array: %s', $localFile));
                }

                $config = $this->mergeRecursive($config, $localConfig);
            }

            $this->items[$name] = $config;
        }

        return $this->items[$name];
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $override
     * @return array<string, mixed>
     */
    private function mergeRecursive(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (
                isset($base[$key])
                && is_array($base[$key])
                && is_array($value)
            ) {
                $base[$key] = $this->mergeRecursive($base[$key], $value);
                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }
}
