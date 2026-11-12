<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/app.php';

tablet_separacao_bootstrap();

$destination = tablet_separacao_user() ? 'pedidos.php' : 'login.php';

header('Location: ' . app_base_path($destination));
exit;
