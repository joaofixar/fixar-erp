<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

/**
 * Extracts a PDF text layer first, then falls back to OCR for scanned pages.
 * Every process receives an argument array: attachment data never enters a shell.
 */
final class PdftotextPdfTextExtractor implements PdfTextExtractorInterface
{
    private bool $ocrEnabled;
    private string $ocrLockKey;

    public function __construct(bool $ocrEnabled = false, string $ocrLockKey = 'default')
    {
        $this->ocrEnabled = $ocrEnabled;
        $this->ocrLockKey = $ocrLockKey;
    }

    public function extract(string $path): ?string
    {
        if (!is_file($path) || !is_readable($path)) return null;
        $text = $this->run(['/usr/bin/pdftotext', '-layout', '-enc', 'UTF-8', $path, '-']);
        if ($text !== null && $this->hasContent($text)) return $text;
        return $this->ocrEnabled ? $this->extractWithOcr($path) : null;
    }

    private function extractWithOcr(string $path): ?string
    {
        if (!is_executable('/usr/bin/pdftoppm') || !is_executable('/usr/bin/tesseract')) return null;
        $lock = $this->acquireOcrLock();
        $temporaryFile = tempnam(sys_get_temp_dir(), 'hardness-certificate-ocr-');
        if ($temporaryFile === false) {
            $this->releaseOcrLock($lock);
            return null;
        }
        if (!@unlink($temporaryFile) || !@mkdir($temporaryFile, 0700)) {
            $this->releaseOcrLock($lock);
            return null;
        }

        try {
            $prefix = $temporaryFile . '/page';
            if ($this->run(['/usr/bin/pdftoppm', '-r', '200', '-png', $path, $prefix]) === null) return null;
            $pages = glob($prefix . '-*.png');
            if (!is_array($pages) || $pages === []) return null;
            natsort($pages);
            $text = [];
            foreach ($pages as $page) {
                $pageText = $this->run(['/usr/bin/tesseract', $page, 'stdout', '-l', 'eng+chi_sim']);
                if ($pageText !== null && $this->hasContent($pageText)) $text[] = $pageText;
            }
            $result = trim(implode("\n\f\n", $text));
            return $this->hasContent($result) ? $result : null;
        } finally {
            foreach (glob($temporaryFile . '/*') ?: [] as $file) @unlink($file);
            @rmdir($temporaryFile);
            $this->releaseOcrLock($lock);
        }
    }

    /** @return resource */
    private function acquireOcrLock()
    {
        $key = preg_replace('/[^A-Za-z0-9_-]/', '_', $this->ocrLockKey);
        $lock = @fopen(sys_get_temp_dir() . '/hardness-certificate-ocr-' . $key . '.lock', 'c');
        if (!is_resource($lock) || !@flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) fclose($lock);
            throw new CertificateOcrBusyException('Certificate OCR is already running for this tenant.');
        }
        return $lock;
    }

    /** @param resource $lock */
    private function releaseOcrLock($lock): void
    {
        @flock($lock, LOCK_UN);
        fclose($lock);
    }

    private function run(array $command): ?string
    {
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) return null;
        $text = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        if ($exitCode !== 0 || !is_string($text)) return null;
        return $text;
    }

    private function hasContent(string $text): bool
    {
        return preg_match('/\S/u', $text) === 1;
    }
}
