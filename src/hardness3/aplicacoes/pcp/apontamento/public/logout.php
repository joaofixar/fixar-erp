<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . pcp_apontamento_url('login.php')); exit; }
pcp_apontamento_require_csrf();
pcp_apontamento_logout();
header('Location: ' . pcp_apontamento_url('login.php'));
exit;
