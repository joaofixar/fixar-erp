<?php

$banco = getenv('MARIADB_DATABASE') ?: 'fixar';
$usuario = getenv('MARIADB_USER') ?: 'fixar';
$senha = getenv('MARIADB_PASSWORD') ?: '';
$host = getenv('DB_HOST') ?: 'db';

$confUsuario = <<<'PHP'
<?php
namespace hardness;
$origem = $_SERVER['HTTP_HOST'] ?? 'localhost';
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$esquema = $https ? 'https' : 'http';
$confUsuario = array(
    'dbDatabase' => %s,
    'dbHost' => %s,
    'dbUser' => %s,
    'dbPass' => %s,
    'pathRaiz' => '/var/www/html/hardness3/',
    'urlRaiz' => $esquema . '://' . $origem . '/hardness3/',
);
PHP;

$gravouConf = file_put_contents(
    '/var/www/html/hardness3/bibliotecas/confUsuario.php',
    sprintf($confUsuario, var_export($banco, true), var_export($host, true), var_export($usuario, true), var_export($senha, true))
);
if ($gravouConf === false) {
    fwrite(STDERR, "Não gravou confUsuario.php\n");
    exit(1);
}

$sys = <<<'PHP'
<?php
$sys = array(
    'nomeEmpresa' => 'Fixar',
    'dominio' => 'localhost',
    'dbDatabase' => %s,
    'dbHost' => %s,
    'dbUser' => %s,
    'dbPass' => %s,
);
PHP;

$gravouSys = file_put_contents(
    '/var/www/html/hardness3/ide/sysConfig.php',
    sprintf($sys, var_export($banco, true), var_export($host, true), var_export($usuario, true), var_export($senha, true))
);
if ($gravouSys === false) {
    fwrite(STDERR, "Não gravou sysConfig.php\n");
    exit(1);
}
