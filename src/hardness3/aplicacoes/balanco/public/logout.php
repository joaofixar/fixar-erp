<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . balanco_url());
    exit;
}
balanco_require_csrf();
balanco_logout();
header('Location: ' . balanco_url('login.php'));
exit;
