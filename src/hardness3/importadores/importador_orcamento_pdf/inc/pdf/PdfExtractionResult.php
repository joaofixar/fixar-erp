<?php
namespace hardness;

class PdfExtractionResult
{
    public $success = false;
    public $status = 'ERROR';
    public $message = '';
    public $pdfPath = '';
    public $workHash = '';
    public $workDir = '';
    public $rawTextPath = '';
    public $rawText = '';
    public $stdout = '';
    public $stderr = '';
    public $command = '';
    public $exitCode = null;
    public $elapsedMs = 0;
    public $metadata = array();

    public function toArray()
    {
        return array(
            'success' => $this->success,
            'status' => $this->status,
            'message' => $this->message,
            'pdf_path' => $this->pdfPath,
            'work_hash' => $this->workHash,
            'work_dir' => $this->workDir,
            'raw_text_path' => $this->rawTextPath,
            'raw_text' => $this->rawText,
            'stdout' => $this->stdout,
            'stderr' => $this->stderr,
            'command' => $this->command,
            'exit_code' => $this->exitCode,
            'elapsed_ms' => $this->elapsedMs,
            'metadata' => $this->metadata,
        );
    }
}
