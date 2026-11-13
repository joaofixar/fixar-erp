<?php
declare(strict_types=1);

namespace Hardness\Observability\Auth;

final class ClientAccessPolicyLoader
{
    private const MAX_BYTES = 65536;

    public function load(): ClientAccessPolicy
    {
        $path = $this->path();
        $directory = dirname($path);
        $resolvedDirectory = realpath($directory);
        if ($resolvedDirectory !== $directory || !is_file($path) || is_link($path) || filesize($path) > self::MAX_BYTES) {
            throw new \RuntimeException('client_access_config_not_available');
        }
        $stat = stat($path);
        $mode = $stat['mode'] & 0777;
        if (($mode & 0022) !== 0 || ($mode & 0004) !== 0 || realpath($path) !== $path) {
            throw new \RuntimeException('invalid_client_access_config');
        }
        $json = file_get_contents($path);
        if (!is_string($json)) {
            throw new \RuntimeException('invalid_client_access_config');
        }
        try {
            return ClientAccessPolicy::fromJson($json);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('invalid_client_access_config');
        }
    }

    private function path(): string
    {
        $testPath = getenv('OBSERVABILITY_TEST_MODE') === '1' ? getenv('OBSERVABILITY_TEST_ACCESS_PATH') : false;
        return is_string($testPath) && $testPath !== '' ? $testPath : dirname(__DIR__, 2) . '/config/access.json';
    }
}
