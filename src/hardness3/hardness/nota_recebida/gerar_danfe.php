<?php
namespace hardness;
global $g;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
include "../include/pasta_usuario.inc";

$nT013_Id = $_GET['T013_Id'];
$nRNE001_Id = $_GET['RNE001_Id'];

if (empty($nRNE001_Id)) {
	include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
}

if (empty($nRNE001_Id)){
	$cClausula = "SELECT 
					T013_Data_Emissao,
					T013_Arquivo_XML,
					C004_Logo_Marca,
					C004_Casas_Decimais_Preco,
					T013_Numero_Protocolo_Nfe
				FROM T013 
					LEFT JOIN C004 ON T013_C004_Id=C004_Id 
				WHERE T013_Id='{$nT013_Id}'";
} else {
	$cClausula = "SELECT 
					RNE001_Data_Emissao as T013_Data_Emissao,
					C004_Logo_Marca,
					C004_Casas_Decimais_Preco,
					RNE001_Numero_Protocolo as T013_Numero_Protocolo_Nfe
				FROM RNE001 
					LEFT JOIN C004 ON RNE001_C004_Id=C004_Id 
				WHERE RNE001_Id='{$nRNE001_Id}'";
}

$cSql_T013 = mysqli_query($cClausula);
$mT013 = mysql_fetch_array($cSql_T013);
$cErro = mysql_error();

$Diretorio_Imagens = $confUsuario['pathRaiz'] . "dados_usuarios/" . $confUsuario['dbDatabase']."/imagens/logo";
preg_match('#versao=\"([0-9.]+)\"#', $mT013['T013_Arquivo_XML'], $match);

if ($match[1]=='4.00' || !empty($nRNE001_Id))
{
 	//include('../class/php-barcode.php');
    include "../class/danfe400.php";
} else if ($match[1]=='3.10')
{
 	//include('../class/php-barcode.php');
    include "../class/danfe310.php";
} 
else if ($mT013['T013_Versao_Nfe']=='2.00')
{
    include "../class/danfe200.php";
}
else
{
    include "../class/danfe.php";
}

if (mysql_num_rows($cSql_T013)==0)
{
	echo "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>URL Inválida.</p>";
	exit;
}

if (!empty($cErro))
{
	mysqli_query("ROLLBACK");
	msg_alerta("Erro:".str_replace("'","",$cErro)."/p/pClausula: ".str_replace("'","",$cClausula));
	exit;
}

	if (empty($mT013['T013_Numero_Protocolo_Nfe']))
	{
	    echo "NF-e não possui autorização para ser impressa";
	    exit;
	}
	$cAno_Mes_NFe=substr($mT013['T013_Data_Emissao'],0,7);

	$oDanfe=new danfe();
	if (empty($nRNE001_Id)) {
		$cArquivo=$Diretorio_NFe."/xml/recebido/".$mT013['T013_Numero_Protocolo_Nfe'].".xml";
	} else {
		$cArquivo=$Diretorio_NFe."/xml/baixado/".$mT013['T013_Numero_Protocolo_Nfe'].".xml";
	}
	if (!file_exists($cArquivo))
	{
	    echo (empty($nRNE001_Id)) ? "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>Não encontrado o XML para o protocolo: {$mT013['T013_Numero_Protocolo_Nfe']} </p>" : "Sem XML";
        exit;
	}
	
    $mArq=explode(".",$mT013['C004_Logo_Marca']);
    $nExt=count($mArq)-1;
    $cExtensao_Arquivo=$mArq[$nExt];

	$ponteiro=fopen($cArquivo, "r");
	$oDanfe->xml=fread($ponteiro, filesize($cArquivo));
	//$oDanfe->setT007_Id($mT013[T013_Id]);
	$oDanfe->setLinhasPrimeiraPagina(32);
	$oDanfe->setLinhasSegundaPagina(100);
	$oDanfe->casas_decimais_preco = 5;
	$oDanfe->casas_decimais_quantidade = 3;
	$oDanfe->setNomeArquivoPDF("danfe.pdf");
	$oDanfe->setSaidaArquivo("I");
	$oDanfe->canhoto   = true;
	$oDanfe->issqn     = false;
	$oDanfe->pdf       = new \PDF_Code128("P", 'mm', 'A4');
	$oDanfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual".".$cExtensao_Arquivo";
	if($match[1] != '3.10' && $match[1] != '4.00' && empty($nRNE001_Id)){
		$oDanfe->gera();
	} else {
		$id = md5(uniqid());
	    ob_start();
	    copy($oDanfe->logomarca, "/tmp/{$id}.{$cExtensao_Arquivo}");
		$oDanfe->logomarca = "{$id}.{$cExtensao_Arquivo}";
		
	    $oDanfe->geraHtml();
	    //$oDanfe->gera();
	    $output = ob_get_contents();
	    ob_end_clean();
	    file_put_contents("/tmp/{$id}.html", $output);
	    //$cmd = "/usr/bin/htmltopdf.sh \"/tmp/{$id}.html\" --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 /tmp/{$id}.pdf";

		if (empty($nRNE001_Id)) {
			$cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
			shell_exec($cmd);
			header('Content-Type: application/pdf;');
			header('Content-Disposition: inline; filename="danfe_' . ($mT007['T007_Numero_Protocolo_Nfe'] ? $mT007['T007_Numero_Protocolo_Nfe'] : $mT007['T007_Id']) . '.pdf"');
			echo file_get_contents("/tmp/{$id}.pdf");
			unlink("/tmp/{$id}.{$cExtensao_Arquivo}");
			unlink("/tmp/{$id}.html");
			unlink("/tmp/{$id}.pdf");
		} else {
			echo $output;
		}
	}



?>
