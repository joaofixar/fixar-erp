<?php
set_time_limit(0);

// Ajuda
if (!isset($argv[1]) || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
	echo <<<EOT
Uso: {$argv[0]} -b banco [-a]
\t--help\t\testa ajuda
\t-b\t\tbanco (exemplo: e213)
\t-a\t\talterar!

EOT;
	die();
}

// Op��es
$o = getopt("b:a");
if (isset($o['b']) && !empty($o['b'])) {
	// conecta no banco de dados
	$con = mysqli_connect('127.0.0.1', 'root', 'master');
	$Error=mysqli_connect_error($con);
	if (!empty($Error)) { die($Error . "\n"); }
	$dbs = mysqli_select_db($o['b'],  $con) or die (mysqli_error($con) . "\n");
	mysqli_set_charset($con,'utf8');
	mysqli_query($con,'SET NAMES utf8;');
	
	// comandos
	$sql = "SELECT * FROM T013 WHERE T013_Arquivo_XML != '' AND T013_Arquivo_XML IS NOT NULL;";
	$res = mysqli_query($con,$sql);
	while ($row = mysqli_fetch_assoc($res)) {
		echo $row['T013_Id'] . " ...\n";
		$dom = new \DomDocument;
		@$dom->loadXML($row['T013_Arquivo_XML']);
		$det     = $dom->getElementsByTagName("det");

		//-- PEGA O TOTAL DE ITENS
		$nTotal_Itens_Nota=0;
		foreach ($det as $d)
		{
		    $nTotal_Itens_Nota++;
		}

		for ($x=0;$x<$nTotal_Itens_Nota;$x++)
		{
			$prod    = $det->item($x)->getElementsByTagName("prod")->item(0);
			$imposto = $det->item($x)->getElementsByTagName("imposto")->item(0);
			$ICMS    = $imposto->getElementsByTagName("ICMS")->item(0);

			$CodigoProd = $prod->getElementsByTagName("cProd")->item(0)->nodeValue;
			$nValor_Unitario = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
			$ClassFiscal = $prod->getElementsByTagName("NCM")->item(0)->nodeValue;

			$T014_Aliquota_ICMS_Interno = $ICMS->getElementsByTagName("pICMSST")->item(0)->nodeValue;
			$AliqICMS = $ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue;
			$T014_ICMS_Inter_Estadual = $AliqICMS;
			$T014_Aliquota_ICMS = $AliqICMS;
			$T014_MVA_Ajustada = $ICMS->getElementsByTagName("pMVAST")->item(0)->nodeValue;
			$RedICMSST= $ICMS->getElementsByTagName("pRedBCST")->item(0)->nodeValue;
			$T014_Percentual_Reducao_ICMS_ST = $RedICMSST;

			$sql = "SELECT T014_Id FROM T014 WHERE T014_T013_Id = '{$row['T013_Id']}' AND T014_Codigo_Produto = SUBSTR('{$CodigoProd}', 1, 30) AND T014_Classificacao_Fiscal = '{$ClassFiscal}'";
			$res2 = mysqli_query($con,$sql);
			if (mysqli_num_rows($res2) == 0) {
				file_put_contents('/tmp/xml', $row['T013_Arquivo_XML']);
				echo "{$row['T013_Numero_Nota_Fiscal']} - Erro produto: {$sql} - " . mysqli_num_rows($res2);
				die();
			}
			while ($row2 = mysqli_fetch_assoc($res2)) {
				$sql = <<<EOT
UPDATE 
	T014
SET
	T014_Aliquota_ICMS_Interno = '{$T014_Aliquota_ICMS_Interno}',
	T014_ICMS_Inter_Estadual = '{$T014_ICMS_Inter_Estadual}',
	T014_Aliquota_ICMS = '{$T014_Aliquota_ICMS}',
	T014_MVA_Ajustada = '{$T014_MVA_Ajustada}',
	T014_Percentual_Reducao_ICMS_ST = '{$T014_Percentual_Reducao_ICMS_ST}'
WHERE
	T014_Id = '{$row2['T014_Id']}'
EOT;
				if (isset($o['a'])) {
					mysqli_query($con,$sql);
				} else {
					echo $sql . "\n\n";
				}
			}
		}
	}
} else {
	echo "Op��es inv�lidas.\n--help para ver todos os comandos.\n";
}