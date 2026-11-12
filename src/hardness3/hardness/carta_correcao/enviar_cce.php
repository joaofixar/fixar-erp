<?php
namespace hardness;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');

//-- Classe da Carta de Correção
include "../class/cce.class.php";

global $confUsuario;
echo "<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 ";
echo "bgcolor='#FFFFFF' ";
echo ">";

$nT199_Id = $_GET['T199_Id'];

$cClausula  = " select ";
$cClausula .= " T199_Sequencial_Evento, ";
$cClausula .= " T199_Flag_Enviado, ";
$cClausula .= " T199_Correcao, ";
$cClausula .= " T007_C004_Id, ";
$cClausula .= " C004_Senha_Certificados, ";
$cClausula .= " C004_Localizacao_Certificados, ";
$cClausula .= " C004_Fuso_Horario, ";
$cClausula .= " T007_Ambiente_NFe, ";
$cClausula .= " T007_Flag_Contingencia, ";
$cClausula .= " T007_Flag_SCAN, ";
$cClausula .= " T007_Empresa_Usuaria_Uf, ";
$cClausula .= " T007_Empresa_Usuaria_Codigo_Estado_IBGE, ";
$cClausula .= " T007_Empresa_Usuaria_Cnpj, ";
$cClausula .= " T007_Chave_Acesso_Nfe ";
$cClausula .= " from T199 ";
$cClausula .= " left join T007 on T199_T007_Id=T007_Id ";
$cClausula .= " left join C004 on T007_C004_Id=C004_Id ";
$cClausula .= " where T199_Id='$nT199_Id' ";
$cSql_T199 = mysql_query($cClausula);
$mT199 = mysql_fetch_array($cSql_T199);
$cErro=mysql_error();
if (!empty($cErro))
{
    msg_alerta("Ocorreu um erro ao tentar incluir o registro/p/pErro:$cErro/p/pClausula:$cClausula");
    exit;
}


if ($mT199['T199_Flag_Enviado']=='S')
{
    msg_alerta("Essa Carta de Correção já foi enviada");
    echo "<script>parent.fecharTelaEnvioCCe();</script>";    
    exit;
}

$oCCe = new classeCCe();
$oCCe->setT199Id($nT199_Id);
$oCCe->setC007Id($Usuario_Atual);
$oCCe->setConfig($mConfig);
if ($mT199['T007_Flag_Contingencia']=='S')
{
    //-- Ambiente CONTINGENCIA
    $oCCe->setTipoDocAuxiliar(2);
}
elseif ($mT199['T007_Flag_SCAN']=='S')
{
    //-- Ambiente SCAN
    $oCCe->setTipoDocAuxiliar(3);
}
else
{
    //-- Ambiente NORMAL
    $oCCe->setTipoDocAuxiliar(1);
}

$Diretorio_NFe = $confUsuario['pathRaiz'] . "../hardness/dados_usuarios/".$confUsuario['dbDatabase']."/nfe";
$oCCe->setUFEmitente($mT199['T007_Empresa_Usuaria_Uf']);
$oCCe->setIdentAmbiente($mT199['T007_Ambiente_NFe']);  //--  1-Produçãoo / 2-Homologação
$oCCe->setCodigoUFEmitente($mT199['T007_Empresa_Usuaria_Codigo_Estado_IBGE']);
$oCCe->setCnpjEmitente($mT199['T007_Empresa_Usuaria_Cnpj']);
$oCCe->setSeqEvento($mT199['T199_Sequencial_Evento']); // Para cada evento da mesma chave, tem um sequencial
$oCCe->setTextoCorrecao($mT199['T199_Correcao']);
$oCCe->setChaveNFe($mT199['T007_Chave_Acesso_Nfe']); // Chave de acesso da nota fiscal eletronica
$oCCe->setPastaArquivoXML($Diretorio_NFe."/xml/cce"); // Pasta aonde será gravado os arquivos XML
$oCCe->setSenhaCertificado($mT199['C004_Senha_Certificados']);
$oCCe->setCertificadoPEM($mT199['C004_Localizacao_Certificados'].$mT199['T007_C004_Id'].'.pem');
$oCCe->carregaCertP12($mT199['C004_Localizacao_Certificados'].$mT199['T007_C004_Id'].'.pfx');
$URL_NFe = "/hardness/dados_usuarios/".$confUsuario['dbDatabase']."/nfe";
$URL_NFe = $URL_NFe."/xml/cce/assinado/";
if(!empty($mT199['C004_Fuso_Horario'])){
    $oCCe->setFusoHorario($mT199['C004_Fuso_Horario']);
} else {
    $oCCe->setFusoHorario('America/Fortaleza');
}

$oCCe->getURLXML();
$oCCe->enviarCCe();
$cArquivo = $URL_NFe . " - " .$oCCe->getURLXML();

if ($oCCe->errorStatus) 
{
    //echo 'Falhou: '.htmlspecialchars($oCCe->errorMsg);
    echo 'Informe ao suporte técnico os erros, por favor: ' . $oCCe->errorMsg;
    echo "<br /><a href=\"{$URL_NFe}{$oCCe->getURLXML()}\" target=\"_blank\">Visualizar .XML</a>";
}
else
{
    echo "CCe Enviada<script>parent.Recarregar()</script>";
}

?>