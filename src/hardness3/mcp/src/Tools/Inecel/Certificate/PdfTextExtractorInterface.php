<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

interface PdfTextExtractorInterface
{
    /** Returns null when no text can be extracted (including a scanned PDF). */
    public function extract(string $path): ?string;
}
