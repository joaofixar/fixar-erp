<?php
declare(strict_types=1);
namespace Hardness\Observability\Config;

final class RuntimeConfiguration
{
    public const PATH = '/etc/hardness/observability/runtime.json';
    private $data;
    private function __construct(array $data) { $this->data = $data; }
    public static function load(): self
    {
        $path = self::path(); $dir = dirname($path); $stat = @lstat($path);
        $test=getenv('OBSERVABILITY_TEST_MODE')==='1'; if ($path[0] !== '/' || realpath($dir) !== $dir || $stat === false || is_link($path) || !is_file($path) || filesize($path) > 131072 || (($stat['mode'] & 0022) !== 0) || (($stat['mode'] & 0004) !== 0) || (!$test && $stat['uid'] !== 0)) { throw new \RuntimeException('invalid_runtime_config'); }
        $raw = file_get_contents($path); $data = json_decode((string) $raw, true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) { throw new \RuntimeException('invalid_runtime_config'); }
        $data['trusted_proxy_ips'] = isset($data['trusted_proxy_ips']) && is_array($data['trusted_proxy_ips']) ? $data['trusted_proxy_ips'] : array();
        return new self($data);
    }
    private static function path(): string { $test=getenv('OBSERVABILITY_TEST_MODE')==='1' ? getenv('OBSERVABILITY_TEST_RUNTIME_PATH') : false; return is_string($test)&&$test!==''?$test:self::PATH; }
    public function trustedProxies(): array { return $this->data['trusted_proxy_ips']; }
}
