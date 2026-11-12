<?php
namespace hardness;

function importador_orcamento_pdf_json($payload, $statusCode = 200)
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8', true, $statusCode);
    }

    echo json_encode($payload);
    exit;
}

