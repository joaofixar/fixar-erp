<?php
namespace hardness;

interface PdfTextExtractorInterface
{
    public function extract($pdfPath, array $context = array());
}
