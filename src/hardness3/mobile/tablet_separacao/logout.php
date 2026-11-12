<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/app.php';

tablet_separacao_logout();

header('Location: ' . app_base_path('login.php'));
exit;
