<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Trava de processo liberada automaticamente pelo sistema ao fechar o descritor. */
final class MysqlRecorderRunLock
{
    private $handle;

    public function acquire(string $path): bool
    {
        if ($this->handle !== null) { throw new \LogicException('mysql_recorder_lock_already_used'); }
        if (is_link($path)) { throw new \RuntimeException('mysql_recorder_lock_symlink_refused'); }
        $oldMask = umask(0077); $handle = @fopen($path, 'c+b'); umask($oldMask);
        if ($handle === false) { throw new \RuntimeException('mysql_recorder_lock_open_failed'); }
        $pathStat = @lstat($path); $handleStat = @fstat($handle);
        if (!is_array($pathStat) || !is_array($handleStat) || $pathStat['dev'] !== $handleStat['dev'] || $pathStat['ino'] !== $handleStat['ino'] || (($handleStat['mode'] & 0170000) !== 0100000)) { fclose($handle); throw new \RuntimeException('mysql_recorder_lock_changed'); }
        @chmod($path, 0600);
        if (!flock($handle, LOCK_EX | LOCK_NB)) { fclose($handle); return false; }
        $this->handle = $handle;
        ftruncate($handle, 0); fwrite($handle, (string) getmypid() . "\n"); fflush($handle);
        return true;
    }

    public function release(): void
    {
        if ($this->handle !== null) { @flock($this->handle, LOCK_UN); fclose($this->handle); $this->handle = null; }
    }

    public function __destruct() { $this->release(); }
}
