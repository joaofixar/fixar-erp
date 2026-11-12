<?php
/**
* Fila de Envio de NFs
*/
set_time_limit(30);
$home = __FILE__;
$home = str_replace('scripts/queueNF', '', dirname($home));
chdir($home);
require 'scripts/queueNF/conf.php';
require 'scripts/queueNF/codigos.php';
chdir($diretorioNFeClass);
require 'nfe3.class.php';
chdir($home);

require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');

$cClausula = " select * from C020 where C020_C004_Id='1'";
$cSql_C020 = mysql_query($cClausula);
$mC020 = mysql_fetch_array($cSql_C020);

$cSql_Empresa=@mysql_query("select * from C004 left join D018 on D018_Id=C004_D018_Id where C004_Id='1'");
$mEmpresa=@mysql_fetch_array($cSql_Empresa);

$oNFE = new classeNFe();
$oNFE->setIdentAmbiente($mEmpresa['C004_Ambiente_NFe']);
$oNFE->setUFEmitente($mEmpresa['D018_UF']);
$oNFE->setCodigoUFEmitente($mEmpresa['D018_Codigo_IBGE']);							
$oNFE->setSenhaCertificado($mEmpresa['C004_Senha_Certificados']);
$oNFE->setCertificadoPEM($mEmpresa['C004_Localizacao_Certificados']."1.pem");

$status = '';
// 1 - normal
$oNFE->setTipoDocAuxiliar(1);
$statusNormal = ((int) $oNFE->statusServico());
// 3 - scan
if ($mEmpresa['D018_UF'] == 'AC' || $mEmpresa['D018_UF'] == 'AL' || $mEmpresa['D018_UF'] == 'AP' || $mEmpresa['D018_UF'] == 'MG' || $mEmpresa['D018_UF'] == 'PB' || $mEmpresa['D018_UF'] == 'RJ' || $mEmpresa['D018_UF'] == 'RS' || $mEmpresa['D018_UF'] == 'RO' || $mEmpresa['D018_UF'] == 'RR' || $mEmpresa['D018_UF'] == 'SC' || $mEmpresa['D018_UF'] == 'SE' || $mEmpresa['D018_UF'] == 'SP' || $mEmpresa['D018_UF'] == 'TO' || $mEmpresa['D018_UF'] == 'DF') {
	$oNFE->setTipoDocAuxiliar(6);	// SVC-AN
} else {
	$oNFE->setTipoDocAuxiliar(7);	// SVC-RS
}
//$oNFE->setTipoDocAuxiliar(3);
$statusCont = ((int) $oNFE->statusServico());
if ($statusNormal) {
	$status .= $statusNormal . '|' . '0';
} else {
	$status .= $statusNormal . '|' . $statusCont;
}
file_put_contents('/tmp/status-sefaz', $status);
