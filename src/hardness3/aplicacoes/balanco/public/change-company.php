<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

balanco_require_access();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . balanco_url());
    exit;
}

balanco_require_csrf();
// A troca não pode alterar o contexto por um combo: ela volta à seleção e à
// confirmação, que revalidam C019 antes de ativar a próxima empresa.
balanco_clear_operational_context();
header('Location: ' . balanco_url('select-company.php'));
exit;
