<?php
	namespace hardness;
	global $logonfc, $confUsuario,$g;

	require_once('../../bibliotecas/confUsuario.php');
	require_once('../../bibliotecas/conexaoBanco.php');
	include_once("../../configuracoes/sysConfig.php");
	require_once('../../bibliotecas/funcoesGerais.php');
	include "../include/pasta_usuario_mdfe.inc";

	// Cria ou inicia uma sessão
	$Pular_Script='S';
	global $Diretorio_NFe;

	$Diretorio_Hardness = str_replace("hardness3","hardness",$confUsuario['pathRaiz']);
	$Diretorio_NFe = $Diretorio_Hardness."dados_usuarios/" . $confUsuario['dbDatabase'] .  "/mdfe";

	include('../class/php-barcode.php');

	$cQuery = $_GET['Query'];
	$T276_Id = $_GET['T276_Id'];
	$T276_C004_Id = $_GET['T276_C004_Id'];

	//-- VERIFICA SE A NOTA JA EXISTE
	$cClausula  = " select ";
	$cClausula .= " * ";
	$cClausula .= " from T276 ";
	$cClausula .= " left join C004 on C004_Id=T276_C004_Id ";
	if(!empty($T276_Id)){
		$cClausula .= " where T276_Id='$T276_Id' ";
		$cClausula .= " and T276_C004_Id='$T276_C004_Id' ";
	} else {
		$cClausula .= " where md5(concat(T276_C004_Id,'|',T276_Id))='$cQuery' ";
	}

	$cSql_T276 = mysqli_query($cClausula);
	$mT276 = mysql_fetch_array($cSql_T276);
	$cErro = mysql_error();

	$modelo = '58';

	if ($mT276['T276_Flag_Cancelada']=='S' and (empty($mT276['T276_Numero_Recibo_MDFe']) or empty($mT276['T276_Numero_Protocolo_MDFe']))){
	    echo "<p align=center><font class=fonte_atencao>MDFe ESTA CANCELADO! NÃO PODE SER TRANSMITIDO PARA O SEFAZ</font></p>";
	    exit;
	}

	if ($mT276['T276_Ambiente_MDFe']=='2'){
	   if (empty($mT276['T276_Numero_Recibo_MDFe']) || empty($mT276['T276_Numero_Protocolo_MDFe']) ){
	       echo "<p align=center><font class=fonte_atencao>Ambiente de Homologação</font></p>";
	   }
	}
	if ($mT276['T276_Versao_MDFe']=='3.00'){
		// personalização do danfe
		$damdfe = "../dados_usuarios/$Banco_Dados/damdfe.php";
		if (file_exists($damdfe)) {
			include $damdfe;
		} else {
			include "../class/damdfe.php";
		}
	}

	if (mysql_num_rows($cSql_T276)==0){
		echo "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>URL Inválida.</p>";
		exit;
	}

	if (!empty($cErro)){
		mysqli_query("ROLLBACK");
		msg_alerta("Erro:".str_replace("'","",$cErro)."/p/pClausula: ".str_replace("'","",$cClausula));
		exit;
	}

	if (empty($mT276['T276_Numero_Protocolo_MDFe']))
	{
	    echo "MDF-e não possui autorização para ser impressa";
	    exit;
	}
	$cAno_Mes_MDFe = substr($mT276['T276_Data_Emissao'],0,7);

	$oDamdfe=new damdfe();	
	$cArquivo=$Diretorio_MDFe."/xml/autorizado/{$cAno_Mes_MDFe}/".$mT276['T276_Numero_Protocolo_MDFe'].".xml";
	if (!file_exists($cArquivo)){
		$cArquivo=$Diretorio_MDFe."/xml/autorizado/".$mT276['T276_Numero_Protocolo_MDFe'].".xml";
		if (!file_exists($cArquivo)){
			echo "<p align=left style='font-arial,verdana,san-serif;font-size:24;font-weight:bold;'>Não encontrado o XML para o protocolo: '{$mT276['T276_Numero_Protocolo_MDFe']}'</p>";
    		exit;
		}
	}
    $mArq=explode(".",$mT276['C004_Logo_Marca']);
    $nExt=count($mArq)-1;
    $cExtensao_Arquivo=$mArq[$nExt];

	$ponteiro=fopen($cArquivo, "r");
	$oDamdfe->xml=fread($ponteiro, filesize($cArquivo));
	$oDamdfe->setT276_Id($mT276['T276_Id']);
	$oDamdfe->setT276_C004_Id($T276_C004_Id);
	$oDamdfe->setNomeArquivoPDF("damdfe.pdf");
	$oDamdfe->setSaidaArquivo("I");
	$oDamdfe->issqn     = false;
	$oDamdfe->pdf       = new \PDF_Code128("P", 'mm', 'A4');
	$oDamdfe->gerarQRCode();

	if($mT276['T276_Numero_Protocolo_MDFe'] <= 0){
        $mT276['T276_Numero_Protocolo_MDFe'] = '999999999999999';
	}
	
	$qrcode = $oDanfe->qrcode = $Diretorio_Hardness."dados_usuarios/{$confUsuario['dbDatabase']}/mdfe/qrcode/{$mT276['T276_Numero_Protocolo_MDFe']}.jpg";
	$Empresa_Atual = $mT276['C004_Id'];
	$oDamdfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual"."_nfe.$cExtensao_Arquivo";

	//die("Diretorio Imagens = " .$oDamdfe->logomarca );
    $id = md5(str_replace(array('.','-','/'), '', $mT276['T276_Emitente_CNPJ'].$mT276['T276_Id']));
    ob_start();
    copy($oDamdfe->logomarca, "/tmp/{$id}.{$cExtensao_Arquivo}");
    $oDamdfe->logomarca = "{$id}.{$cExtensao_Arquivo}";
	
	copy($qrcode, "/tmp/qrCode{$id}.jpg");
    $oDamdfe->qrcode = "qrCode{$id}.jpg";

	$oDamdfe->geraPDF();
    $output = ob_get_contents();
    ob_end_clean();
    file_put_contents("/tmp/{$id}.html", $output);

    $cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --enable-local-file-access --margin-left 6 --margin-right 6 --margin-top 6 --margin-bottom 3 \"/tmp/{$id}.html\" /tmp/{$id}.pdf";
    shell_exec($cmd);
   	header('Content-Type: application/pdf;');
    header('Content-Disposition: inline; filename="damdfe_' . ($mT276['T276_Numero_Protocolo_MDFe'] ? $mT276['T276_Numero_Protocolo_MDFe'] : $mT276['T276_Id']) . '.pdf"');
    echo file_get_contents("/tmp/{$id}.pdf");
    unlink("/tmp/{$id}.{$cExtensao_Arquivo}");
    unlink("/tmp/{$id}.html");
    unlink("/tmp/{$id}.pdf");
	unlink("/tmp/{$id}.barcode.png");
	unlink("/tmp/qrCode{$id}.jpg");

    mysqli_query("update T276 set T276_Flag_Impresso='S' where T276_Id='{$mT276['T276_Id']}'");

?>
