<?php
/*

Esta pagina irá rodar pelo crontab do servidor linux

Edite o arquivo com o comando crontab -e
Adicione as instruções para execução de acordo com o padrão abaixo:

(minuto) (hora) (dia do mês) (mês) (dia da semana) (usuário) (comando/script)

0 20 * * * /usr/bin/php /var/www/html/hardness3/scripts/crontab/crontabSemanalConfigEmpresas.php 

*/

namespace hardness;

session_start();

set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
$time_start = microtime(true);
$g = array();
$g['usuarioAtual'] = 63;
$g['empresaAtual'] = 1;
$g['time'] = array();
$g['time']['sql'] = '';
$_SERVER['REMOTE_ADDR'] = '';
 
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com caminho dos arquivos!\nExemplo: /var/v3/trunk/\n";
	die();
}

$caminho  = $argv[1];
$caminhoHardness = '/var/www'.$caminho.'hardness3/';
echo "Iniciou crontab semanal:".date("Y-m-d H:i");

chdir($caminhoHardness);

ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
$empresas = scandir('/var/www/sites');

require_once($caminhoHardness.'sistema/confSistema.php');
require_once($caminhoHardness.'bibliotecas/funcoesGerais.php');
require_once($caminhoHardness.'bibliotecas/restricoes.php');

$g['conf'] = new confSistema();

$msg = '';
$dbAtualizado = array();
foreach($empresas as $index => $empresa){
    if (strpos($empresa, '-confUsuario.php') !== false) {
        include_once('/var/www/sites/'.$empresa);
        if(array_search($confUsuario['dbDatabase'],$dbAtualizado)===false){
            if (strpos($msg, $confUsuario['dbDatabase']) === false) {
                if (strpos($confUsuario['pathRaiz'], '/var/www'.$caminho) !== false){
                    $g['pathRaiz'] = $confUsuario['pathRaiz'];
                    $local = $g['pathRaiz'];
                    $g['urlRaiz'] = $confUsuario['urlRaiz'];
                    $con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
                    $Error=mysql_error();
                    echo $confUsuario['dbDatabase']." - Atulizando notificacoes em " . date("Y-m-d H:i") . "\n\n";
                    if(empty($Error)){
                        echo "\nIniciou atualizacao crontab db:".$confUsuario['dbDatabase'];
                        $g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
                        $g['conexaoBanco'] = $con;
                        require_once($caminhoHardness.'bibliotecas/conexaoBancoAuditoria.php');
                        $g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
                        $g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
                        $g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
                        $g['pathDados'] = $local . $g['pathDados'];
                        $g['pathDadosUsuario'] = $local . $g['pathDadosUsuario'];
                        $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
                        $g['c004'] = $g['C004'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}';"));

                        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
                        while($rowC031 = mysql_fetch_assoc($sqlC031)){
                            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
                            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
                        }
                        include("crontabSemanal.php");
                        $dbAtualizado[] = $confUsuario['dbDatabase'];
                    } else {
                        $msg .= "Não foi possível acesso o db:".$confUsuario['dbDatabase']." \n\n";
                    }
                }
            }
        }
    }
}
echo "\nBancos atualizados:".json_encode($dbAtualizado)."\n";
echo $msg;
echo "Finalizou crontab diario:".date("Y-m-d H:i");