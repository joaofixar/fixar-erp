<?php
namespace hardness;
$confUsuario = array(
	'dbDatabase' => 'dev_e237',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');
$g['conexaoBanco'] = $con;

chdir('../');
require_once('sistema/confSistema.php');
$g['conf'] = new confSistema();
require_once('bibliotecas/funcoesGerais.php');
require_once("bibliotecas/classes/VEN003.php");
$VEN003 = new VEN003();

$sql = <<<EOT
SELECT 
	*
FROM
	T013
ORDER BY T013_Id ASC
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	//error_reporting(E_ALL);
	unset($CFOP);
	unset($natOp);
	unset($vICMS);
	unset($iCST);
	unset($vIPI);
	unset($T013_Valor_Total_PIS);
	unset($pCST);
	unset($T013_Valor_Total_COFINS);
	unset($cCST);

	echo $row['T013_Id'] . " ...\n";
	$dom = new \DomDocument;
	$row['T013_Arquivo_XML'] = preg_replace('#^\?#', '', $row['T013_Arquivo_XML']);
	$xml = $dom->loadXML($row['T013_Arquivo_XML']);
	if (!$xml) { continue; }
	
	$det = $dom->getElementsByTagName("det");
	$ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);

	$vICMS = utf8_decode($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue);
	$vIPI = utf8_decode($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue);
	$T013_Valor_Total_PIS = utf8_decode($ICMSTot->getElementsByTagName("vPIS")->item(0)->nodeValue);
	$T013_Valor_Total_COFINS = utf8_decode($ICMSTot->getElementsByTagName("vCOFINS")->item(0)->nodeValue);

	// se adicionar na VEN003, precisa estes campos
	$natOp = $dom->getElementsByTagName("natOp")->item(0)->nodeValue;
	$vICMS = utf8_decode($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue);

	//-- PEGA O TOTAL DE ITENS
	$nTotal_Itens_Nota=0;
	foreach ($det as $d)
	{
		$nTotal_Itens_Nota++;
	}

	//-- PEGA A BASE DO ICMS A CFOP E A ALIQUOTA DO ICMS
	// se adicionar na VEN003, precisa estes campos
	$iCST = 0;
	$pCST = 0;
	$cCST = 0;
	for ($x=0;$x<$nTotal_Itens_Nota;$x++)
	{
		$prod    = @$det->item($x)->getElementsByTagName("prod")->item(0);
		$imposto = @$det->item($x)->getElementsByTagName("imposto")->item(0);

		$CFOP      = @$prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
		$Red_ICMS  = @$imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("pRedBC")->item(0)->nodeValue;
		$Aliq_ICMS = @$imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("pICMS")->item(0)->nodeValue;

		$TAG_IPI   = $imposto->getElementsByTagName("IPI")->item(0);
		$vBC_IPI = 0;
		if (!empty($TAG_IPI))
		{
			$TAG_IPITrib = $imposto->getElementsByTagName("IPI")->item(0)->getElementsByTagName("IPITrib")->item(0);
			if (!empty($TAG_IPI) and !empty($TAG_IPITrib))
			{
				$vBC_IPI  = $imposto->getElementsByTagName("IPI")->item(0)->getElementsByTagName("IPITrib")->item(0)->getElementsByTagName("vBC")->item(0)->nodeValue;
			}
		}
		@$vTBC_IPI += $vBC_IPI;
		
		// se adicionar na VEN003, precisa estes campos
		$iCST = @$imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;
		$pCST = @$imposto->getElementsByTagName("PIS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;
		$cCST = @$imposto->getElementsByTagName("COFINS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;
	}

	//-- ENCONTRA A CFOP
	$D006 = mysql_query("select D006_D006_Id from D006 where replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP'");
	$mD006 = mysql_fetch_array($D006);

	if (empty($mD006['D006_D006_Id'])) {
		$mCFOP[5]=1;
		$mCFOP[6]=2;
		$CFOP=$mCFOP[substr($CFOP,0,1)].'.'.substr($CFOP,-3);
	}

	$cClausula  = " select ";
	$cClausula .= " D006_Id ";
	$cClausula .= " from D006 ";
	$cClausula .= " where D006_Id='$mD006[D006_D006_Id]' ";
	$cClausula .= "    or D006_Codigo_CFOP='$CFOP' ";
	$cSql_D006 = mysql_query($cClausula);
	$mD006 = mysql_fetch_array($cSql_D006);
	$cErro=mysql_error();
	if (!empty($cErro))
	{
		return "Erro: " . $cErro ."<br />". $cClausula;
	}
	
	if ($mD006['D006_Id']<=0)
	{
		echo "\t$CFOP, $natOp, $vICMS, $iCST, $vIPI, $T013_Valor_Total_PIS, $pCST, $T013_Valor_Total_COFINS, $cCST \n";
		//return "A CFOP " . $CFOP . " não está cadastrada";
		$D006_Id = $VEN003->cadastrarCFOP($CFOP, $natOp, $vICMS, $iCST, $vIPI, $T013_Valor_Total_PIS, $pCST, $T013_Valor_Total_COFINS, $cCST);
		$logCFOPCadastrada = array("CFOP: {$CFOP} - {$natOp}");
		$novaCFOP = true;
		$mD006['D006_Id'] = $D006_Id;
	}

	$sql = "UPDATE T013 SET T013_D006_Id = '{$mD006['D006_Id']}' WHERE T013_Id = '{$row['T013_Id']}'";
	mysqli_query($sql);
	$cErro=mysql_error();
	if (!empty($cErro))
	{
		return "Erro: " . $sql;
	}


	// PRODUTOS

	//-- FAZ A INCLUSAO DOS ITENS
	for ($x=0;$x<$nTotal_Itens_Nota;$x++)
	{
		echo "\tProduto: {$x}\n";
		unset($CFOP);
		unset($natOp);
		unset($vICMS);
		unset($iCST);
		unset($vIPI);
		unset($T013_Valor_Total_PIS);
		unset($pCST);
		unset($T013_Valor_Total_COFINS);
		unset($cCST);

		$prod    = $det->item($x)->getElementsByTagName("prod")->item(0);
	    $imposto = $det->item($x)->getElementsByTagName("imposto")->item(0);    
	    $ICMS    = $imposto->getElementsByTagName("ICMS")->item(0);
	    $PIS     = $imposto->getElementsByTagName("PIS")-> item(0);
	    $COFINS    = $imposto->getElementsByTagName("COFINS")->item(0);
	    $TAGIPI    = $imposto->getElementsByTagName("IPI")->item(0);
	    
	    $CodigoProd = $prod->getElementsByTagName("cProd")->item(0)->nodeValue;
	    $Descricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
	    $Quantidade = $prod->getElementsByTagName("qCom")->item(0)->nodeValue;
	    $nValor_Unitario = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
	    $nValor_Total_Produto = $prod->getElementsByTagName("vProd")->item(0)->nodeValue;
	    $ClassFiscal = $prod->getElementsByTagName("NCM")->item(0)->nodeValue;
	    $Unidade = $prod->getElementsByTagName("uCom")->item(0)->nodeValue;
	    $vDesc = $prod->getElementsByTagName("vDesc")->item(0)->nodeValue;    
	    
	    $AliqICMS = $ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue;
	    $RedICMS  = $ICMS->getElementsByTagName("pRedBC")->item(0)->nodeValue;
	    $vBC_ICMS = $ICMS->getElementsByTagName("vBC")->item(0)->nodeValue;    
	    $vICMS    = $ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue;
	    
	    $T014_Valor_ICMS_Substituicao  		= $ICMS->getElementsByTagName("vICMSST")->item(0)->nodeValue; 	// T014_Valor_ICMS_Substituicao
	    $T014_Flag_Situacao_Tributaria  	= $ICMS->getElementsByTagName("CST")->item(0)->nodeValue; 		// T014_Flag_Situacao_Tributaria
	    $T014_Modalidade_ICMS    			= $ICMS->getElementsByTagName("modBC")->item(0)->nodeValue; 	// T014_Modalidade_ICMS
	    $T014_Modalidade_ICMS_Substituicao  = $ICMS->getElementsByTagName("modBCST")->item(0)->nodeValue; 	// T014_Modalidade_ICMS_Substituicao
	    $T014_ICMS_Inter_Estadual  			= $ICMS->getElementsByTagName("pICMSST")->item(0)->nodeValue; 	// T014_ICMS_Inter_Estadual
	    $T014_Valor_Base_ICMS_Substituicao  = $ICMS->getElementsByTagName("vBCST")->item(0)->nodeValue; 	// T014_Valor_Base_ICMS_Substituicao
	
	    if (!empty($TAGIPI))
	    {
	       $pIPI    						= $TAGIPI->getElementsByTagName("pIPI")->item(0)->nodeValue;
	       $vBC_IPI 						= $TAGIPI->getElementsByTagName("vBC")->item(0)->nodeValue;
	       $vIPI    						= $TAGIPI->getElementsByTagName("vIPI")->item(0)->nodeValue;      
	       $T014_Situacao_Tributaria_IPI  	= $TAGIPI->getElementsByTagName("CST")->item(0)->nodeValue;     //T014_Situacao_Tributaria_IPI
	    }

	    // PIS
	    $T014_Situacao_Tributaria_PIS = $PIS->getElementsByTagName("CST")->item(0)->nodeValue; 				// T014_Situacao_Tributaria_PIS
	    $T014_Valor_Base_PIS 		  = $PIS->getElementsByTagName("vBC")->item(0)->nodeValue; 				// T014_Valor_Base_PIS
	    $T014_Aliquota_PIS   		  = $PIS->getElementsByTagName("pPIS")->item(0)->nodeValue; 			// T014_Aliquota_PIS
	    $T014_Valor_PIS   			  = $PIS->getElementsByTagName("vPIS")->item(0)->nodeValue; 			// T014_Valor_PIS

	    // COFINS
	    $T014_Situacao_Tributaria_COFINS = $COFINS->getElementsByTagName("CST")->item(0)->nodeValue; 		// T014_Situacao_Tributaria_COFINS
	    $T014_Valor_Base_COFINS 		 = $COFINS->getElementsByTagName("vBC")->item(0)->nodeValue; 		// T014_Valor_Base_COFINS
	    $T014_Aliquota_COFINS    		 = $COFINS->getElementsByTagName("pCOFINS")->item(0)->nodeValue; 	// T014_Aliquota_COFINS
	    $T014_Valor_COFINS    			 = $COFINS->getElementsByTagName("vCOFINS")->item(0)->nodeValue; 	// T014_Valor_COFINS

	    // se adicionar na VEN003, precisa estes campos
		$iCST = @$imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;
		$pCST = @$imposto->getElementsByTagName("PIS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;
		$cCST = @$imposto->getElementsByTagName("COFINS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;

	    
	    
	    $CFOP  = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
		$D006 = mysql_query("select D006_D006_Id from D006 where replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP'");
		$mD006 = mysql_fetch_array($D006);

		if (empty($mD006['D006_D006_Id'])) {
 	 		$mCFOP[5]=1;
			$mCFOP[6]=2;
			$CFOP=$mCFOP[substr($CFOP,0,1)].'.'.substr($CFOP,-3);
		}

	    $cClausula  = " select ";
	    $cClausula .= " D006_Id ";
	    $cClausula .= " from D006 ";
		$cClausula .= " where D006_Id='{$mD006['D006_D006_Id']}' ";
		$cClausula .= "    or D006_Codigo_CFOP='$CFOP' ";
	    $cSql_D006 = mysql_query($cClausula);
	    $mD006 = mysql_fetch_array($cSql_D006);
	    $cErro=mysql_error();
	    if (!empty($cErro))
	    {
	        mysql_query("ROLLBACK");
	        return "Erro: " . $cErro ."<br />". $cClausula;
	    }

	    if ($mD006['D006_Id']<=0)
		{
			echo "\t\t$CFOP, $natOp, $vICMS, $iCST, $vIPI, $T013_Valor_Total_PIS, $pCST, $T013_Valor_Total_COFINS, $cCST \n";
			//return "A CFOP " . $CFOP . " não está cadastrada";
			$D006_Id = $VEN003->cadastrarCFOP($CFOP, $natOp, $vICMS, $iCST, $vIPI, $T013_Valor_Total_PIS, $pCST, $T013_Valor_Total_COFINS, $cCST);
			$logCFOPCadastrada = array("CFOP: {$CFOP} - {$natOp}");
			$novaCFOP = true;
			$mD006['D006_Id'] = $D006_Id;
		}

		$Descricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
		$Descricao=str_replace("'","",$Descricao);
		$Descricao=mysql_real_escape_string($Descricao);
		$sql = "UPDATE T014 SET T014_D006_Id = '{$mD006['D006_Id']}' WHERE T014_T013_Id = '{$row['T013_Id']}' AND T014_Descricao_Produto = '{$Descricao}'";
		mysqli_query($sql);
		$cErro=mysql_error();
		if (!empty($cErro))
		{
			return "Erro: " . $sql;
		}

	}
}
