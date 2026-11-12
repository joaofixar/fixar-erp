<?php
/*
  Programador..............: Evantoni Fuchs
  Data Criacao.............: 16/04/2010
*/

// Cria ou inicia uma sessão
$Pular_Script='S';
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

include "../include/pasta_usuario.inc";

include('../class/php-barcode.php');
global $logonfc;

$cQuery = $_GET['Query'];
$T007_Id = $_GET['T007_Id'];
$impressaoHtml = $_GET['impressaoHtml'];

//-- VERIFICA SE A NOTA JA EXISTE
$cClausula  = " select ";
$cClausula .= " *, ";
$cClausula .= " T007_Id, ";
$cClausula .= " T007_Data_Emissao, ";
$cClausula .= " T007_Versao_Nfe, ";
$cClausula .= " T007_Flag_ACP, ";
$cClausula .= " C004_Logo_Marca, ";
$cClausula .= " C004_Casas_Decimais_Preco, ";
$cClausula .= " T007_Numero_Protocolo_Nfe, ";
$cClausula .= " T007_Casas_Decimais_Preco, ";
$cClausula .= " T007_Casas_Decimais_Quantidade ";
$cClausula .= " from T007 ";
$cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
if(!empty($T007_Id)){
	$cClausula .= " where T007_Id='$T007_Id' ";
} else {
	$cClausula .= " where md5(concat(T007_D024_Id,'|',T007_Id))='$cQuery' ";
}
$cSql_T007 = mysql_query($cClausula);
$mT007 = mysql_fetch_array($cSql_T007);
$cErro = mysql_error();

$modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';

$T127 = mysql_query("SELECT * FROM T127 WHERE T127_Numero_Nota_Fiscal = '$mT007[T007_Numero_Nota_Fiscal]' AND T127_C004_Id = '$mT007[T007_C004_Id]' AND T127_Modelo='$modelo' ORDER BY T127_Id DESC LIMIT 1");
$mT127 = mysql_fetch_array($T127);
if($mT127[T127_Codigo_Retorno] != '100'){
    msg_alerta("NF-e não autorizada! Erro Sefaz: " . $mT127['T127_Retorno_Sefaz']);
    exit;
}

if($mT127[T127_Codigo_Retorno] == '301' || $mT127[T127_Codigo_Retorno] == '302'){         
    msg_alerta("Esta NF-e está DENEGADA! Não pode ser impressa");
    exit;
}

if ($mT007[T007_Flag_ACP]!='6')
{
   //msg_erro_nfe("O Tipo do documento não é NF-e. Verifique!!!<BR><BR>Tipo: $mT007[T007_Flag_ACP]");
   //exit;
}

if ($mT007[T007_Flag_Cancelada]=='S' and (empty($mT007[T007_Numero_Recibo_Nfe]) or empty($mT007[T007_Numero_Protocolo_Nfe])))
{
    echo "<p align=center><font class=fonte_atencao>NOTA FISCAL ESTA CANCELADA NAO PODE SER TRANSMITIDA PARA O SEFAZ</font></p>";
    exit;
}

if ($mT007[T007_Ambiente_NFe]=='2')
{
   if (empty($mT007[T007_Numero_Recibo_Nfe]))
   {
       echo "<p align=center><font class=fonte_atencao>Ambiente de Homologação</font></p>";
   }
   elseif (empty($mT007[T007_Numero_Protocolo_Nfe]))
   {
       echo "<p align=center><font class=fonte_atencao>Ambiente de Homologação</font></p>";
   }
}
if ($mT007[T007_Versao_Nfe]=='4.00'){
	if($mT007['T007_Flag_ACP']==8){
		// personalização do danfe
		$danfe = "../dados_usuarios/$Banco_Dados/nfc400.php";
		if (file_exists($danfe)) {
			include $danfe;
		} else {
			include "../class/nfc400.php";
		}
	}else{
	    // personalização do danfe
		$danfe = "../dados_usuarios/$Banco_Dados/danfe400.php";
		if (file_exists($danfe)) {
			include $danfe;
		} else {

			include "../class/danfe400.php";
		}
	}	
} else if ($mT007[T007_Versao_Nfe]=='3.10'){
	if($mT007['T007_Flag_ACP']==8){
		// personalização do danfe
		$danfe = "../dados_usuarios/$Banco_Dados/nfc310.php";
		if (file_exists($danfe)) {
			include $danfe;
		} else {
			include "../class/nfc310.php";
		}
	}else{
	    // personalização do danfe
		$danfe = "../dados_usuarios/$Banco_Dados/danfe310.php";
		if (file_exists($danfe)) {
			include $danfe;
		} else {
			include "../class/danfe310.php";
		}
	}
} else if ($mT007[T007_Versao_Nfe]=='2.00') {
    // personalização do danfe
	$danfe = "../dados_usuarios/$Banco_Dados/danfe200.php";
	if (file_exists($danfe)) {
	  include $danfe;
	} else {
	  include "../class/danfe200.php";
	}
}
else
{
    include "../class/danfe.php";
}
if (mysql_num_rows($cSql_T007)==0)
{
	echo "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>URL Inválida.</p>";
	exit;
}

if (!empty($cErro))
{
	mysql_query("ROLLBACK");
	msg_alerta("Erro:".str_replace("'","",$cErro)."/p/pClausula: ".str_replace("'","",$cClausula));
	exit;
}

if ($mT007[T007_Flag_ACP]=="6")
{
	if (empty($mT007[T007_Numero_Protocolo_Nfe]))
	{
	    echo "NF-e não possui autorização para ser impressa";
	    exit;
	}
	$cAno_Mes_NFe=substr($mT007[T007_Data_Emissao],0,7);

	$oDanfe=new danfe();	
	$cArquivo=$Diretorio_NFe."/xml/autorizado/{$cAno_Mes_NFe}/".$mT007[T007_Numero_Protocolo_Nfe].".xml";
	if (!file_exists($cArquivo))
	{
		$cArquivo=$Diretorio_NFe."/xml/autorizado/".$mT007[T007_Numero_Protocolo_Nfe].".xml";
		if (!file_exists($cArquivo))
		{
			echo "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>Não encontrado o XML para o protocolo: $mT007[T007_Numero_Protocolo_Nfe]</p>";
    		exit;
		}
	}
    $mArq=explode(".",$mT007[C004_Logo_Marca]);
    $nExt=count($mArq)-1;
    $cExtensao_Arquivo=$mArq[$nExt];

	$ponteiro=fopen($cArquivo, "r");
	$oDanfe->xml=fread($ponteiro, filesize($cArquivo));
	$oDanfe->setT007_Id($mT007[T007_Id]);
	$oDanfe->setLinhasPrimeiraPagina(32);
	$oDanfe->setLinhasSegundaPagina(100);
	$oDanfe->setNomeArquivoPDF("danfe.pdf");
	$oDanfe->setSaidaArquivo("I");
	$oDanfe->canhoto   = true;
	$oDanfe->casas_decimais_preco=$mT007[T007_Casas_Decimais_Preco];
	$oDanfe->casas_decimais_quantidade=$mT007[T007_Casas_Decimais_Quantidade];
	$oDanfe->issqn     = false;
	$oDanfe->pdf       = new PDF_Code128("P", 'mm', 'A4');
	$Empresa_Atual = $mT007['C004_Id'];
	$oDanfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual"."_nfe.$cExtensao_Arquivo";
	
	if ($mT007[T007_Versao_Nfe]!='2.00' && $mT007[T007_Versao_Nfe]!='3.10' &&  $mT007[T007_Versao_Nfe]!='4.00') {
	// todos já utilizam o novo modelo de pdf
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
    //$cmd = "/usr/bin/htmltopdf.sh \"/tmp/{$id}.html\" --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 /tmp/{$id}.pdf";

    $cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
    shell_exec($cmd);
   	header('Content-Type: application/pdf;');
    header('Content-Disposition: inline; filename="danfe_' . ($mT007['T007_Numero_Protocolo_Nfe'] ? $mT007['T007_Numero_Protocolo_Nfe'] : $mT007['T007_Id']) . '.pdf"');
    echo file_get_contents("/tmp/{$id}.pdf");
    unlink("/tmp/{$id}.{$cExtensao_Arquivo}");
    unlink("/tmp/{$id}.html");
    unlink("/tmp/{$id}.pdf");
    unlink("/tmp/{$id}.barcode.png");
	}

    mysql_query("update T007 set T007_Flag_Impresso='S' where T007_Id='$mT007[T007_Id]'");

}else if($mT007['T007_Flag_ACP']=="8"){
	if (empty($mT007[T007_Numero_Protocolo_Nfe]))
	{
	    echo "NF-e não possui autorização para ser impressa";
	    exit;
	}
	$cAno_Mes_NFe=substr($mT007[T007_Data_Emissao],0,7);

	$oDanfe=new danfe();	
	$cArquivo=$Diretorio_NFe."/xml/autorizado/{$cAno_Mes_NFe}/".$mT007[T007_Numero_Protocolo_Nfe].".xml";
	if (!file_exists($cArquivo))
	{
		$cArquivo=$Diretorio_NFe."/xml/autorizado/".$mT007[T007_Numero_Protocolo_Nfe].".xml";
		if (!file_exists($cArquivo))
		{
			echo "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>Não encontrado o XML para o protocolo: $mT007[T007_Numero_Protocolo_Nfe]</p>";
    		exit;
		}
	}
    $mArq=explode(".",$mT007[C004_Logo_Marca]);
    $nExt=count($mArq)-1;
    $cExtensao_Arquivo=$mArq[$nExt];

	$ponteiro=fopen($cArquivo, "r");
	$oDanfe->xml=fread($ponteiro, filesize($cArquivo));
	$oDanfe->setT007_Id($mT007[T007_Id]);
	$oDanfe->setLinhasPrimeiraPagina(32);
	$oDanfe->setLinhasSegundaPagina(100);
	$oDanfe->setNomeArquivoPDF("nfce.pdf");
	$oDanfe->setSaidaArquivo("I");
	$oDanfe->canhoto   = true;
	$oDanfe->casas_decimais_preco=$mT007[T007_Casas_Decimais_Preco];
	$oDanfe->casas_decimais_quantidade=$mT007[T007_Casas_Decimais_Quantidade];
	$oDanfe->issqn     = false;
	$oDanfe->pdf       = new PDF_Code128("P", 'mm', 'A4');
	$oDanfe->geraQRCode2();
	$Empresa_Atual = $mT007['C004_Id'];
	$qrcode = $oDanfe->qrcode = "../../hardness/dados_usuarios/e229/nfce/qrcode/{$mT007['T007_Numero_Protocolo_Nfe']}.jpg";
	$logomarca = $oDanfe->logomarca = "../../hardness/imagens/png/nfce.png";
	if ($mT007[T007_Versao_Nfe]!='2.00' && $mT007[T007_Versao_Nfe]!='3.10') {
		// todos já utilizam o novo modelo de pdf
	    $oDanfe->gera();
    } else {
		// Novo formato HTML -> PDF
	    $id = md5(str_replace(array('.','-','/'), '', $mT007['T007_Empresa_Usuaria_Cnpj'].$mT007['T007_Id']));
	    ob_start();
	    copy($qrcode, "/tmp/{$id}.{$cExtensao_Arquivo}");
	    copy($logomarca, "/tmp/nfc.{$cExtensao_Arquivo}");
	    $oDanfe->qrcode = "{$id}.{$cExtensao_Arquivo}";
	    $oDanfe->logomarca = "nfc.{$cExtensao_Arquivo}";

	    //die("teste: ".$impressaoHtml);
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

    mysql_query("update T007 set T007_Flag_Impresso='S' where T007_Id='$mT007[T007_Id]'");
}
else
{
	echo "A impressão do DANFE não pode ser gerada";
}
?>
