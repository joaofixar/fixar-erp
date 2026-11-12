<?php
namespace hardness;

$argv = $_SERVER['argv'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("America/Fortaleza");
ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
chdir('/var/www/html/hardness3/');
require_once('bibliotecas/funcoesGerais.php');
require_once('sistema/apps.php');
require_once('sistema/personalizacoes.php');

$empresas = scandir('/var/www/sites');
$bancosProcessados = array();
foreach($empresas as $index => $empresa)
{
    // Verifica os arquivos que o nome seja um arquivo de configuração
    if (strpos($empresa, '2.') > 0 || strpos($empresa, '3.') > 0 ) {
        echo "Pulou empresa $empresa (.2 .3)\n\n";
        continue;
    }

    if (strpos($empresa, '-confUsuario.php') !== false) {
        $dominio = explode(".",$empresa);
        include_once('/var/www/sites/'.$empresa);

        // Pular padrão database do novo FW (exemplo: erp-e001)
        if (strpos($confUsuario['dbDatabase'], '-') !== false) {
            continue;
        }

        if (array_search($confUsuario['dbDatabase'],$bancosProcessados)){
            echo "Pulou empresa $empresa banco já processado {$confUsuario['dbDatabase']}\n\n";
            continue;
        }
        $bancosProcessados[] = $confUsuario['dbDatabase'];

        echo "ATUALIZANDO DB: {$dominio[0]} " . $confUsuario['dbDatabase'] . " - " . $confUsuario['urlRaiz'] . " - " . date("d/m/Y") . "\n";

        if (!empty($argv[1]) && $argv[1]!=$confUsuario['dbDatabase']){
            echo "***** Pulou \n\n";
            continue;
        }

        unset($argv);

        // Conexão banco e001 verificar se o cliente está ativo
        if (strpos($empresa, 'chasistemas') !== false) {
            if(isset($g['conexaoBanco'])){
                mysqli_close($g['conexaoBanco']);
            }
            $con = mysqli_init();
            if (!$con) {
                die("mysqli_init failed");
            }
            $con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 240);
            $con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],'e001');
            $g['conexaoBanco'] = $con;

            $T135 = mysql_query("SELECT T135_Flag_Cancelado FROM T135 LEFT JOIN C024 ON C024_D024_Id=T135_D024_Id WHERE C024_Banco_Dados='{$confUsuario['dbDatabase']}'");
            $m135 = mysql_fetch_assoc($T135);
            if ($mT135['T135_Flag_Cancelado'] == 'S') {
                echo "****** Cliente cancelado não processa BI ***** \n\n";
                continue;
            }
        }

        // Conexão com o clickhouse
        if (isset($g['conexaoBancoClickHouse'])) {
            mysqli_close($g['conexaoBancoClickHouse']);
        }
        include('bibliotecas/conexaoBancoClickHouse.php');

        // Conexão com o banco principal
        if(isset($g['conexaoBanco'])){
            mysqli_close($g['conexaoBanco']);
        }
        $con = mysqli_init();
        if (!$con) {
            echo "Erro conexão banco \n\n";
            continue;
        }
        $con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3600);
        $con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
        $g['conexaoBanco'] = $con;
        $Error=mysqli_connect_error($con);
        if (!empty($Error)) {
            echo "Erro conexão banco \n\n";
            continue;
        }

        //$g['pathDados'] = "scripts/{$confUsuario['dbDatabase']}/";
        //$argv[1] = $dominio[0];

        include("scripts/crontab/scripts-datawareHouse.php");

        include("scripts/crontab/scripts-BI.php");

        //include("scripts/crontab/scripts-datawareHouseCache.php");

        echo "\n\n";
    }
}
