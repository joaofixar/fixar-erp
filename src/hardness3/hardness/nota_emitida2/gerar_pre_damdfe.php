<?php
namespace hardness;
global $g;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
include "../include/pasta_usuario_mdfe.inc";


$cArquivo_Cabecalho_Relatorio = "../cabecalho_impressoes.php";
$caminhoHardness = str_replace("hardness3","hardness",$confUsuario['pathRaiz']);
$Diretorio_MDFe=$caminhoHardness."dados_usuarios/{$confUsuario['dbDatabase']}/mdfe";

include('../class/php-barcode.php'); 

set_time_limit(0);

$T276_Id       = $_GET['T276_Id']; //PEGA O ID DO MDF-E
$T276_C004_Id  = $_GET['T276_C004_Id']; 

if (empty($T276_Id)){
    msg_erro_mdfe("Programa que gera o xml nao recebeu o parametro: T276_Id");
    exit;
}
//nesse select devo pegar os seguintes valores: 
$cClausula  = " select ";
$cClausula .= " T276_Versao_MDFe, ";
$cClausula .= " T276_Ambiente_MDFe, ";
$cClausula .= " T276_Emitente_UF, ";
$cClausula .= " T276_Emitente_UF_IBGE, ";
$cClausula .= " T276_Emitente_CNPJ, ";
$cClausula .= " T276_Numero_Protocolo_MDFe, ";
$cClausula .= " T276_Id, ";
$cClausula .= " C004_Id, ";
$cClausula .= " C004_Logo_Marca, ";
$cClausula .= " T276_Numero_MDFe ";
$cClausula .= " from T276 ";
$cClausula .= " left join C004 on C004_Id=T276_C004_Id";
$cClausula .= " left join T283 on T276_Id=T283_T276_Id";
$cClausula .= " left join T284 on T276_Id=T284_T276_Id";
$cClausula .= " left join T278 on T276_Id=T278_T276_Id";
$cClausula .= " left join T279 on T276_Id=T279_T276_Id";
$cClausula .= " where T276_Id='".$T276_Id."'";
$cClausula .= " and T276_C004_Id='".$T276_C004_Id."'";
$cSql_T276=mysqli_query($cClausula);
$mT276=mysql_fetch_array($cSql_T276);

if($mT276['T276_Versao_MDFe'] == 3.00){
    //--  Class para gerar o arquivo do MDFe
    include "../class/mdfe3.class.php";

    // personalização do damdfe
    $damdfe = "../dados_usuarios/{$confUsuario['dbDatabase']}/damdfe.php";
    if (file_exists($damdfe)) {
        include $damdfe;
    } else {
        include "../class/damdfe.php";
    }
} 
$oMDFE = new classeMDFe();
$oMDFE->setT276Id($T276_Id);
$oMDFE->setC004Id($T276_C004_Id);
//-- $Diretorio_MDFe é definido no arquivo include/pasta_usuario.inc
$oMDFE->setPastaArquivoXML("$Diretorio_MDFe/xml/");

//sempre vai valer (1) pois não teremos mdf-e em contingência
$oMDFE->setTipoDocAuxiliar(1);


$oMDFE->setIdentAmbiente($mT276['T276_Ambiente_MDFe']);  //--  1-Produção / 2-Homologação
$oMDFE->setUFEmitente($mT276['T276_Emitente_UF']);
$oMDFE->setWebService();
$oMDFE->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);

//-- SO CRIA O XML SE NAO EXISTIR CHAVE DE ACESSO
$oMDFE->criarArquivoXML();
ob_clean();
//echo $oMDFE->getNomeArquivoXML();
$cChave_Acesso = $oMDFE->getChaveAcessoMDFE();
$cChave_Acesso = str_replace("MDFe","",$cChave_Acesso);
$cArquivo_Origem  = $oMDFE->getNomeArquivoXML();
$cArquivo_Destino = "$Diretorio_MDFe/xml/pre/".basename($oMDFE->getNomeArquivoXML());

if (!copy($cArquivo_Origem, $cArquivo_Destino)){
    msg_erro_mdfe("Falha ao copiar: <b>$cArquivo_Origem</b>  para: <b>$cArquivo_Destino</b>");
    exit;
}

$oDamdfe=new damdfe();

$mArq=explode(".",$mT276['C004_Logo_Marca']);
$nExt=count($mArq)-1;
$cExtensao_Arquivo=$mArq[$nExt];

$ponteiro=fopen($cArquivo_Destino, "r");
$oDamdfe->xml=fread($ponteiro, filesize($cArquivo_Destino));
$oDamdfe->setT276_Id($mT276['T276_Id']);
$oDamdfe->setT276_C004_Id($T276_C004_Id);
$oDamdfe->setNomeArquivoPDF("damdfe.pdf");
$oDamdfe->setSaidaArquivo("I");
$oDamdfe->canhoto   = false;
$oDamdfe->issqn     = false;
$oDamdfe->visualizacao = 'S';
$oDamdfe->pdf       = new \PDF_Code128("P", 'mm', 'A4');
$oDamdfe->gerarQRCode();

if($mT276['T276_Numero_Protocolo_MDFe'] <= 0){
    $mT276['T276_Numero_Protocolo_MDFe'] = '999999999999999';
}
$qrcode = $oDanfe->qrcode = "../../hardness/dados_usuarios/{$confUsuario['dbDatabase']}/mdfe/qrcode/{$mT276['T276_Numero_Protocolo_MDFe']}.jpg";
$qrcode = $oDanfe->qrcode = $caminhoHardness."dados_usuarios/{$confUsuario['dbDatabase']}/mdfe/qrcode/{$mT276['T276_Numero_Protocolo_MDFe']}.jpg";
$Empresa_Atual = $mT276['C004_Id'];
$oDamdfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual"."_nfe.$cExtensao_Arquivo";

// Novo formato HTML -> PDF
$id = md5(str_replace(array('.','-','/'), '', $mT276['T276_Emitente_CNPJ'].$mT276['T276_Id']));
ob_start();
copy($oDamdfe->logomarca, "/tmp/{$id}.{$cExtensao_Arquivo}");
$oDamdfe->logomarca = "{$id}.{$cExtensao_Arquivo}";

copy($qrcode, "/tmp/qrCode{$id}.jpg");
$oDamdfe->qrcode = "qrCode{$id}.jpg";

$oDamdfe->geraHtml();
$output = ob_get_contents();
ob_end_clean();
file_put_contents("/tmp/{$id}.html", $output);
$cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 --encoding utf-8 \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
shell_exec($cmd);
header('Content-Type: application/pdf;');
header('Content-Disposition: inline; filename="damdfe_' . ($mT276['T276_Numero_Protocolo_MDFe'] ? $mT276['T276_Numero_Protocolo_MDFe'] : $mT276['T276_Id']) . '.pdf"');
echo file_get_contents("/tmp/{$id}.pdf");
unlink("/tmp/{$id}.{$cExtensao_Arquivo}");
unlink("/tmp/{$id}.html");
unlink("/tmp/{$id}.pdf");
unlink("/tmp/{$id}.barcode.png");
unlink("/tmp/qrCode{$id}.jpg");

function msg_erro_mdfe($cErro){
    echo "<script>document.getElementById('Div_Transmitindo').style.display='none';</script>";
    echo "<table width=100% height=100% border=0 bgcolor=#F4F4F4 >";
         echo "<tr>";
              echo "<td align=center>";
                  echo "<table border=0 bgcolor=#000080>";
                      echo "<tr>";
                          echo "<td align=center>";
                             echo "<fieldset>";

                                echo "<table border=0>";
                                     echo "<tr>";
                                          echo "<td align=left>";
                                               echo "<font style='font-size:14px;font-family:arial,verdana;color:white;font-weight:bold;'>";
                                               echo $cErro;
                                               echo "</font>";
                                          echo "</td>";
                                     echo "</tr>";
                                 echo "</table>";

                              echo "</fieldset>";
                          echo "</td>";
                     echo "</tr>";
                echo "</table>";
             echo "</td>";
         echo "</tr>";
    echo "</table>";
}
?>
