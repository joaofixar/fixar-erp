<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/app.php';

tablet_separacao_require_auth();

$companyId = (int) ($_POST['company_id'] ?? 0);
$redirect = (string) ($_POST['redirect'] ?? app_base_path('pedidos.php'));

if (tablet_separacao_switch_company($companyId)) {
} else {
    tablet_separacao_set_flash('error', 'Não foi possível alterar para esta empresa.');
}

if (strpos($redirect, app_base_path()) !== 0) {
    $redirect = app_base_path('pedidos.php');
}

header('Location: ' . $redirect);
exit;
