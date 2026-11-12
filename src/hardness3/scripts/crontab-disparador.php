<?php
namespace hardness;

/*
 * Dispara o crontab em processos PHP separados, um para cada banco.
 *
 * Adicionar ao crontab do Linux:
 * * * * * php crontab-disparador.php [pathRaiz] [dbHost] [dbUser] [dbPass] [grupoExecucao] (opcional)
 */
if (count($argv) != 5 && count($argv) != 6) {
    echo "Parâmetros inválidos.\n";
    echo "php crontab-disparador.php [pathRaiz] [dbHost] [dbUser] [dbPass] [grupoExecucao] (opcional)\n";
    die();
}

$pathRaiz = $argv[1];
$dbHost = $argv[2];
$dbUser = $argv[3];
$dbPass = $argv[4];
$grupoExecucao = $argv[5] ?? '';

// Mantém o mesmo nível de ruído de avisos do executor. Os arquivos
// confUsuario podem iniciar sessão e são lidos apenas para identificar o banco.
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

$arquivosEmpresas = scandir('/var/www/sites');
$bancosProcessados = array();

foreach ($arquivosEmpresas as $arquivoEmpresa) {
    if (strpos($arquivoEmpresa, '-confUsuario.php') === false) {
        continue;
    }

    // Cada arquivo define $confUsuario. Reinicializar evita herdar campos do anterior.
    $confUsuario = array('pathRaiz' => $pathRaiz);
    require('/var/www/sites/' . $arquivoEmpresa);

    if (empty($confUsuario['dbDatabase']) || isset($bancosProcessados[$confUsuario['dbDatabase']])) {
        continue;
    }
    $bancosProcessados[$confUsuario['dbDatabase']] = true;

    $comando = implode(' ', array(
        escapeshellarg(PHP_BINARY),
        escapeshellarg(__DIR__ . '/crontab.php'),
        escapeshellarg($pathRaiz),
        escapeshellarg($dbHost),
        escapeshellarg($dbUser),
        escapeshellarg($dbPass),
        escapeshellarg($grupoExecucao),
        escapeshellarg($confUsuario['dbDatabase']),
    ));

    echo "Disparando crontab para {$confUsuario['dbDatabase']}...\n";
    passthru($comando, $codigoRetorno);
    if ($codigoRetorno !== 0) {
        echo "Crontab de {$confUsuario['dbDatabase']} finalizou com código {$codigoRetorno}.\n";
    }
}
