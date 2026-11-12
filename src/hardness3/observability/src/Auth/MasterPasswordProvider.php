<?php
declare(strict_types=1);

namespace Hardness\Observability\Auth;

/** Lê somente o hash master oficial do Hardness, sem expor $sys ao restante do módulo. */
final class MasterPasswordProvider
{
    public function hash(): string
    {
        $path = $this->path();
        $directory = dirname($path);
        if (!is_file($path) || is_link($path) || realpath($directory) !== $directory || realpath($path) !== $path) { throw new \RuntimeException('official_master_config_unavailable'); }
        try { $sys = (static function (string $file): array { $sys = array(); $sysLocal = array(); require $file; return is_array($sys) ? $sys : array(); })($path); } catch (\Throwable $exception) { throw new \RuntimeException('official_master_config_invalid'); }
        $hash = isset($sys['senhaRootMaster']) ? $sys['senhaRootMaster'] : null;
        if (!is_string($hash) || preg_match('/^[a-f0-9]{32}$/Di', $hash) !== 1) { throw new \RuntimeException('official_master_config_invalid'); }
        return strtolower($hash);
    }

    private function path(): string
    {
        $testPath = getenv('OBSERVABILITY_TEST_MODE') === '1' ? getenv('OBSERVABILITY_TEST_SYSCONFIG_PATH') : false;
        return is_string($testPath) && $testPath !== '' ? $testPath : dirname(HARDNESS_OBSERVABILITY_ROOT) . '/configuracoes/sysConfig.php';
    }
}
