<?php
/**
* Fila de Envio de MDFe
*/
namespace hardness;
set_time_limit(30);
$home = __FILE__;
$home = str_replace('scripts/queueMDFE', '', dirname($home));
chdir($home);
require 'scripts/queueMDFE/conf.php';
require 'scripts/queueMDFE/codigos.php';
chdir($diretorioMDFeClass);
require 'mdfe3.class.php';
chdir($home);
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'bibliotecas/funcoesGerais.php');

$cSql_Empresa=@mysqli_query("select C004_Ambiente_MDFe, D018_UF, D018_Codigo_IBGE, C004_Senha_Certificados, C004_Localizacao_Certificados from C004 left join D018 on D018_Id=C004_D018_Id where C004_Id='1'");
$mEmpresa=@mysql_fetch_array($cSql_Empresa);

$oMDFe = new classeMDFe();
$oMDFe->setIdentAmbiente($mEmpresa['C004_Ambiente_MDFe']);
$oMDFe->setUFEmitente($mEmpresa['D018_UF']);
$oMDFe->setCodigoUFEmitente($mEmpresa['D018_Codigo_IBGE']);							
$oMDFe->setSenhaCertificado($mEmpresa['C004_Senha_Certificados']);
$oMDFe->setCertificadoPEM($mEmpresa['C004_Localizacao_Certificados']."1.pem");

$status = '';
// 1 - normal
$oMDFe->setTipoDocAuxiliar(1);
$statusNormal = ((int) $oMDFe->statusServico());
if ($statusNormal) {
	$status .= $statusNormal . '|' . '0';
} else {
	$status .= $statusNormal . '|' . $statusCont;
}
file_put_contents('/tmp/status-sefaz', $status);
