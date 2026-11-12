<?php
namespace hardness;

$confUsuarioClickHouse = array(
    // Banco de dados
    'dbDatabase' => $confUsuario['dbDatabase'] . '_' . preg_replace('/[^a-z0-9]/i', '_', gethostname()),
    'dbHost' => file_exists('/.dockerenv') ? '44.195.102.20' : '172.31.8.128', // se estiver dentro do docker, utiliza o IP externo, senão o interno da AWS
    'dbUser' => 'default',
    'dbPass' => 'clickH@rd#1',
    'dbPort' => 9004
);

// Conexão com o banco ClickHouse
$g['conexaoBancoClickHouse'] = mysqli_connect(
    $confUsuarioClickHouse['dbHost'],
    $confUsuarioClickHouse['dbUser'],
    $confUsuarioClickHouse['dbPass'],
    $confUsuarioClickHouse['dbDatabase'],
    $confUsuarioClickHouse['dbPort'],
);