<?php
namespace hardness;
global $g;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../sistema/apps.php');
require_once('../../sistema/personalizacoes.php');
include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
include "../include/pasta_usuario.inc";

// $caminhoHardness = str_replace("hardness3","hardness",$confUsuario['pathRaiz']);
// $Diretorio_NFe=$caminhoHardness."dados_usuarios/{$confUsuario['dbDatabase']}/nfe";

set_time_limit(0);
if (isset($usarUTF8) && $usarUTF8) {
	mysql_set_charset('utf8');
	mysqli_query('SET NAMES utf8;');
}

$nT007_Id       = $_GET['T007_Id'];
$cFase          = $_GET['Fase'];

if (empty($nT007_Id))
{
    msg_erro_nfe("Programa que gera o xml nao recebeu o parametro: T007_Id");
    exit;
}

$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T007 ";
$cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
$cClausula .= " left join $cBd_Configuracao.C004 on C004_Id=T007_C004_Id ";
$cClausula .= " left join $cBd_Configuracao.C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
$cClausula .= " where T007_Id='$nT007_Id' ";
$cSql_T007=mysqli_query($cClausula);
$mT007=mysql_fetch_array($cSql_T007);

if ($mConfig['C020_NFe_Treinamento']=='S' and $mT007['T007_Ambiente_NFe']!='2')
{
    msg_erro_nfe("NFe esta definida como treinamento e o ambiente n�o � homologacao, verifique!");
    exit;
}

if($mT007['T007_Versao_NFe'] == '3.10'){

    //--  Class para gerar o arquivo da NFe
    include "../class/nfe3.class.php";
    if($mT007['T007_Flag_ACP']==8){
        // personaliza��o do danfe
        $danfe = "../../dados_usuarios/{$confUsuario['dbDatabase']}/nfc310.php";
        if (file_exists($danfe)) {
            include $danfe;
        } else {
            include "../class/nfc310.php";
        }
    }else{
        // personaliza��o do danfe
        $danfe = "../../dados_usuarios/{$confUsuario['dbDatabase']}/danfe310.php";
        if (file_exists($danfe)) {
          include $danfe;
        } else {
          include "../class/danfe310.php";
        }
    }

} else if($mT007['T007_Versao_NFe'] == '4.00'){

    //--  Class para gerar o arquivo da NFe
    include "../class/nfe4.class.php";
    if($mT007['T007_Flag_ACP']==8){
        // personaliza��o do danfe
        $danfe = "../../dados_usuarios/{$confUsuario['dbDatabase']}/nfc400.php";
        if (file_exists($danfe)) {
            include $danfe;
        } else {
        include "../class/nfc400.php";
        }
    }else{
        // personaliza��o do danfe
        $danfe = "../../dados_usuarios/{$confUsuario['dbDatabase']}/danfe400.php";
        if (file_exists($danfe)) {
          include $danfe;
        } else {
          include "../class/danfe400.php";
        }
    }

} else {
    //--  Class para gerar o arquivo da NFe
    include "../class/nfe2.class.php";

    // personaliza��o do danfe
    $danfe = "../../dados_usuarios/{$confUsuario['dbDatabase']}/danfe200.php";
    if (file_exists($danfe)) {
      include $danfe;
    } else {
      include "../class/danfe200.php";
    }
}

// include('../class/php-barcode.php');

$cClausula  = " select ";
$cClausula .= " T008_Id ";
$cClausula .= " from T008 ";
$cClausula .= " where T008_T007_Id='$nT007_Id' ";
$cSql_T008=mysqli_query($cClausula);
if (mysql_num_rows($cSql_T008)==0)
{
   msg_erro_nfe("Não existem itens nesta nota fiscal");
   exit;
}

$oNFE = new classeNFe();
$oNFE->setT007Id($nT007_Id);

//-- $Diretorio_NFe é definido no arquivo include/pasta_usuario.inc
$oNFE->setPastaArquivoXML("$Diretorio_NFe/xml/");

if ($mT007['T007_Flag_SCAN']=='S')
{
    $oNFE->setTipoDocAuxiliar(3);
}
else
{
    $oNFE->setTipoDocAuxiliar(1);
}

$oNFE->setIdentAmbiente($mT007['T007_Ambiente_NFe']);  //--  1-Produ��o / 2-Homologa��o
$oNFE->setUFEmitente($mT007['T007_Empresa_Usuaria_Uf']);
$oNFE->setWebService();
$oNFE->setModuloNFe($_SESSION['Modulo_NFe']);
$oNFE->setCodigoUFEmitente($mT007['T007_Empresa_Usuaria_Codigo_Estado_IBGE']);

//-- SO CRIA O XML SE NAO EXISTIR CHAVE DE ACESSO
$oNFE->criarArquivoXML();
//echo $oNFE->getNomeArquivoXML();
$cChave_Acesso = $oNFE->getChaveAcessoNFe();
$cChave_Acesso = str_replace("NFe","",$cChave_Acesso);

$cArquivo_Origem  = $oNFE->getNomeArquivoXML();
$cArquivo_Destino = "$Diretorio_NFe/xml/pre/".basename($oNFE->getNomeArquivoXML());

if (!copy($cArquivo_Origem, $cArquivo_Destino))
{
   msg_erro_nfe("Falha ao copiar: <b>$cArquivo_Origem</b>  para: <b>$cArquivo_Destino</b>");
   exit;
}
if (empty($cFase))
{
   echo "<script>location.replace('gerar_pre_danfe.php?T007_Id=$nT007_Id&Fase=2');</script>";
}
elseif ($cFase==2)
{
    if ($mT007['T007_Flag_ACP']=="6"){
        $oDanfe=new danfe();

        $mArq=explode(".",$mT007['C004_Logo_Marca']);
        $nExt=count($mArq)-1;
        $cExtensao_Arquivo=$mArq[$nExt];

        $ponteiro=fopen($cArquivo_Destino, "r");
        $oDanfe->xml=fread($ponteiro, filesize($cArquivo_Destino));
        $oDanfe->setT007_Id($mT007['T007_Id']);
        $oDanfe->setLinhasPrimeiraPagina(32);
        $oDanfe->setLinhasSegundaPagina(100);
        $oDanfe->setNomeArquivoPDF("danfe.pdf");
        $oDanfe->setSaidaArquivo("I");
        $oDanfe->canhoto   = true;
    	$oDanfe->casas_decimais_preco=$mT007['T007_Casas_Decimais_Preco'];
    	$oDanfe->casas_decimais_quantidade=$mT007['T007_Casas_Decimais_Quantidade'];
        $oDanfe->issqn     = false;
        $oDanfe->visualizacao = 'S';
            $oDanfe->pdf                       = new \PDF_Code128("P", 'mm', 'A4');
        $Empresa_Atual = $mT007['C004_Id'];
        $oDanfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual"."_nfe.$cExtensao_Arquivo";

        if (false) {
         $oDanfe->gera();
        } else {
            // Novo formato HTML -> PDF
            $id = md5(str_replace(array('.','-','/'), '', $mT007['T007_Empresa_Usuaria_Cnpj'].$mT007['T007_Id']));
            ob_start();
            copy($oDanfe->logomarca, "/tmp/{$id}.{$cExtensao_Arquivo}");
            $oDanfe->logomarca = "{$id}.{$cExtensao_Arquivo}";
            $oDanfe->geraHtml();
            $output = ob_get_contents();
            ob_end_clean();
            file_put_contents("/tmp/{$id}.html", $output);
            $cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
            shell_exec($cmd);
            header('Content-Type: application/pdf;');
            header('Content-Disposition: inline; filename="danfe_' . ($mT007['T007_Numero_Protocolo_Nfe'] ? $mT007['T007_Numero_Protocolo_Nfe'] : $mT007['T007_Id']) . '.pdf"');
            echo file_get_contents("/tmp/{$id}.pdf");
            unlink("/tmp/{$id}.{$cExtensao_Arquivo}");
            unlink("/tmp/{$id}.html");
            unlink("/tmp/{$id}.pdf");
            unlink("/tmp/{$id}.barcode.png");
            if(file_exists($oNFE->getNomeArquivoXML())){
                // Tambem cria o arquivo no assinado, e tem casos que quando tenta transmitir de fato o arquivo não está com permissão e trava o processo por não conseguir alterar o arquivo para assinado
                chmod($oNFE->getNomeArquivoXML(), 0777);
            }
        }

    }else if($mT007['T007_Flag_ACP']=="8"){
        
        $cAno_Mes_NFe=substr($mT007['T007_Data_Emissao'],0,7);

        $oDanfe=new danfe();    
        
        $mArq=explode(".",$mT007['C004_Logo_Marca']);
        $nExt=count($mArq)-1;
        $cExtensao_Arquivo=$mArq[$nExt];

        $ponteiro=fopen($cArquivo_Destino, "r");
        $oDanfe->xml=fread($ponteiro, filesize($cArquivo_Destino));
        $oDanfe->setT007_Id($mT007['T007_Id']);
        $oDanfe->setLinhasPrimeiraPagina(32);
        $oDanfe->setLinhasSegundaPagina(100);
        $oDanfe->setNomeArquivoPDF("danfe.pdf");
        $oDanfe->setSaidaArquivo("I");
        $oDanfe->canhoto   = true;
        $oDanfe->casas_decimais_preco=$mT007['T007_Casas_Decimais_Preco'];
        $oDanfe->casas_decimais_quantidade=$mT007['T007_Casas_Decimais_Quantidade'];
        $oDanfe->issqn     = false;
        $oDanfe->visualizacao = 'S';
        $oDanfe->pdf                       = new \PDF_Code128("P", 'mm', 'A4');
        $oDanfe->geraQRCode2();
        $Empresa_Atual = $mT007['C004_Id'];
        $qrcode = $oDanfe->qrcode = "../../hardness/dados_usuarios/e229/nfce/qrcode/{$mT007['T007_Numero_Protocolo_Nfe']}.jpg";
        $oDanfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual"."_nfe.$cExtensao_Arquivo";
        if (false) {
            $oDanfe->gera();
        } else {
            $id = md5(str_replace(array('.','-','/'), '', $mT007['T007_Empresa_Usuaria_Cnpj'].$mT007['T007_Id']));
            ob_start();
            copy($qrcode, "/tmp/{$id}.{$cExtensao_Arquivo}");
            copy($logomarca, "/tmp/nfc.{$cExtensao_Arquivo}");
            $oDanfe->qrcode = "{$id}.{$cExtensao_Arquivo}";
            $oDanfe->logomarca = "nfc.{$cExtensao_Arquivo}";

            $impressaoHtml = 'true';
            if($impressaoHtml == 'true'){
                $conteudo = $oDanfe->geraHtml();
                die($conteudo);
            }
            $conteudo = $oDanfe->geraPDF();
            $output = ob_get_contents();
            ob_end_clean();
            file_put_contents("/tmp/{$id}.html", $output);
            //$cmd = "/usr/bin/htmltopdf.sh \"/tmp/{$id}.html\" --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 /tmp/{$id}.pdf";

            $cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 --encoding utf-8 \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
            //$cmd = "xvfb-run --server-num ".mt_rand(0,98)." wkhtmltopdf -T 0 -R 0 -B 0 -L 0 --page-width '2.89in' --page-height '15in' \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
            shell_exec($cmd);
            header('Content-Type: application/pdf;');
            header('Content-Disposition: inline; filename="danfe_' . ($mT007['T007_Numero_Protocolo_Nfe'] ? $mT007['T007_Numero_Protocolo_Nfe'] : $mT007['T007_Id']) . '.pdf"');
            echo file_get_contents("/tmp/{$id}.pdf");
            unlink("/tmp/{$id}.{$cExtensao_Arquivo}");
            unlink("/tmp/{$id}.html");
            unlink("/tmp/{$id}.pdf");
            unlink("/tmp/{$id}.barcode.png");
        }
    }

}

function msg_erro_nfe($cErro)
{
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
