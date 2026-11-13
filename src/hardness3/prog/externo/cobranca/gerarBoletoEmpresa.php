<?php
namespace hardness;

global $g;

chdir('/var/www/html/hardness3/');

require_once('bibliotecas/confUsuario.php');
require_once('bibliotecas/conexaoBanco.php');
require_once("configuracoes/sysConfig.php");
require_once('bibliotecas/funcoesGerais.php');

$cQuery=$_GET['cQuery'];

//-- CONEXAO DIRETA COM O BANCO DE DADOS
$con =@mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
// $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
$g['conexaoBanco'] = $con;
$g['empresaAtual'] = 1;
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
$g['pathRaiz'] = $confUsuario['pathRaiz'];

$C004 = mysql_query("select C004_Nome_Empresa from C004 where C004_Id=1");
$mC004 = mysql_fetch_array($C004);

$T117 = mysql_query("select T117_Id 
                        from T117 
                        where md5(concat(T117_Id,T117_D024_Id))='$cQuery'");
$mT117 = mysql_fetch_array($T117);

if (mysql_num_rows($T117)==0)
{
    die("{$mC004['C004_Nome_Empresa']} <br /><br />Não encontrados registros para gerar o(s) boleto(s)<BR><BR>Faça as seguintes verificações:<BR><BR>. Os titulos estão pendentes ?<BR>. Não esta cancelado ?<BR>. Esta vencido ?<BR>. É Nota Fiscal ?<BR>. Foi definido Conta e Banco ?");
}

$nT117_Id = $mT117['T117_Id'];

if (empty($nT117_Id))
{
    die("Boleto não encontrado");
}

unlink($g['pathRaiz']. $dir."boleto.pdf");
$link = '/hardness3/hardness/boleto/gerar_boleto.php?T117_Id=' . $nT117_Id;					
$filename = md5('boleto_' . $nT117_Id) . '.pdf';
$link = str_replace(' ', '+', $link);
$dir  = $g['pathDados'] . 'pdf/';
is_dir($dir) or mkdir($dir, 0777, true);
$file     = $dir . $filename;
$fileFull = $g['pathRaiz'] . $file;
$fileUrl  = trim("\hardness3\ ") . $file;

$chmod = "xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --lowquality --margin-bottom 0 \"{$confUsuario['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
$retorno = shell_exec($chmod);     

echo "<head><meta http-equiv='refresh' content=1;url='$fileUrl'></head>";

//unlink($fileFull);
