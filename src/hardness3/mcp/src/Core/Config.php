<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

use RuntimeException;

class Config
{
    private string $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = rtrim($configPath, '/');
    }

    /**
     * @return array<string, mixed>
     */
    public function load(string $name): array
    {
        $file = $this->configPath . '/' . $name . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('MCP configuration file not found.');
        }

        $config = require $file;
        if (!is_array($config)) {
            throw new RuntimeException('MCP configuration file is invalid.');
        }

        return $config;
    }
}
