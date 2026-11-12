<?php
namespace hardness;

class PopplerPdfTextExtractor implements PdfTextExtractorInterface
{
    protected $binary;
    protected $timeoutSeconds;

    public function __construct($binary = 'pdftotext', $timeoutSeconds = 60)
    {
        $this->binary = (string) $binary;
        $this->timeoutSeconds = (int) $timeoutSeconds;
    }

    public function extract($pdfPath, array $context = array())
    {
        $result = new PdfExtractionResult();
        $result->pdfPath = (string) $pdfPath;
        $result->workHash = isset($context['work_hash']) ? preg_replace('/[^a-f0-9]/i', '', (string) $context['work_hash']) : '';

        if ($result->workHash === '' && is_file($pdfPath)) {
            $result->workHash = hash_file('sha256', $pdfPath);
        }

        if (!is_file($pdfPath) || !is_readable($pdfPath)) {
            $result->message = 'PDF nao encontrado ou sem permissao de leitura.';
            return $result;
        }

        $workDir = isset($context['work_dir']) ? trim((string) $context['work_dir']) : '';
        if ($workDir === '') {
            $workDir = isset($context['work_dir_override']) ? trim((string) $context['work_dir_override']) : '';
        }
        if ($workDir === '') {
            $workDir = importador_orcamento_pdf_fs_work_dir($result->workHash);
        }

        if (!importador_orcamento_pdf_ensure_directory($workDir)) {
            $result->message = 'Nao foi possivel criar o diretorio de trabalho.';
            return $result;
        }

        $result->workDir = $workDir;
        $rawTextPath = $workDir . '/raw.txt';
        $result->rawTextPath = $rawTextPath;

        $binary = isset($context['binary']) && $context['binary'] !== '' ? (string) $context['binary'] : $this->binary;
        $timeout = isset($context['timeout_seconds']) ? max(1, (int) $context['timeout_seconds']) : $this->timeoutSeconds;

        $command = sprintf(
            '%s -layout -nopgbrk -enc UTF-8 %s %s',
            escapeshellcmd($binary),
            escapeshellarg($pdfPath),
            escapeshellarg($rawTextPath)
        );

        $result->command = $command;

        $descriptorSpec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );

        $start = microtime(true);
        $process = @proc_open($command, $descriptorSpec, $pipes, importador_orcamento_pdf_fs_root_dir());
        if (!is_resource($process)) {
            $result->message = 'Falha ao iniciar pdftotext.';
            return $result;
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdout = '';
        $stderr = '';
        $timedOut = false;

        while (true) {
            $status = proc_get_status($process);
            $stdout .= (string) stream_get_contents($pipes[1]);
            $stderr .= (string) stream_get_contents($pipes[2]);

            if (!$status['running']) {
                $result->exitCode = $status['exitcode'];
                break;
            }

            if ((microtime(true) - $start) > $timeout) {
                $timedOut = true;
                proc_terminate($process);
                $result->exitCode = -1;
                $stderr .= "\nTimeout excedido ao executar pdftotext.";
                break;
            }

            usleep(100000);
        }

        $stdout .= (string) stream_get_contents($pipes[1]);
        $stderr .= (string) stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $result->stdout = trim($stdout);
        $result->stderr = trim($stderr);
        $result->elapsedMs = (int) round((microtime(true) - $start) * 1000);

        if ($timedOut) {
            $result->message = 'Timeout ao ler o PDF.';
            return $result;
        }

        if (!is_file($rawTextPath)) {
            $result->message = 'pdftotext nao gerou o arquivo de texto bruto.';
            return $result;
        }

        $rawText = (string) file_get_contents($rawTextPath);
        $result->rawText = $rawText;

        if ($rawText === '') {
            $result->message = 'PDF sem texto extraivel.';
            return $result;
        }

        if ((int) $result->exitCode !== 0 && trim($result->stderr) !== '') {
            $result->message = 'pdftotext retornou erro.';
            return $result;
        }

        $result->success = true;
        $result->status = 'OK';
        $result->message = 'Texto extraido com sucesso.';
        return $result;
    }
}
