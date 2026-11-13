<?php
namespace hardness;

require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/response.php';

importador_orcamento_pdf_require_auth();

importador_orcamento_pdf_json(array(
    'code' => true,
    'data' => array(
        'status' => 'ESTRUTURA_INICIAL',
    ),
));

