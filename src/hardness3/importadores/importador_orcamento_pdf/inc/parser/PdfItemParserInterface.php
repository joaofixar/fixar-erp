<?php
namespace hardness;

interface PdfItemParserInterface
{
    /**
     * @return ParsedItem[]
     */
    public function parse(PdfExtractionResult $result, array $context = array());

    /**
     * @return array
     */
    public function getDiagnostics();
}
