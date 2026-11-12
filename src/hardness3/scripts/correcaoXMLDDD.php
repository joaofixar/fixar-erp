<?php
/**
* Correção de um problema pontual nos XML na T007
*/
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

// Opções
$o = getopt("b:a");
if (isset($o['b']) && !empty($o['b'])) {
	// conecta no banco de dados
	$con = mysqli_connect('127.0.0.1', 'root', '#qos!0982',$o['b']);
	$Error=mysqli_connect_error($con);
	if (!empty($Error)) { die($Error . "\n"); }
	// $dbs = mysql_select_db($o['b'],  $con) or die (mysql_error() . "\n");
	mysqli_set_charset($con,'utf8');
	mysqli_query($con,'SET NAMES utf8;');
	
	// comandos
	$sql = "SELECT T007_Id, T007_Arquivo_XML FROM T007 WHERE T007_Arquivo_XML != '' AND T007_Arquivo_XML IS NOT NULL;";
	$res = mysqli_query($con,$sql);
	while ($row = mysqli_fetch_assoc($res)) {
		echo $row['T007_Id'] . ': ';
		$dom = new \DomDocument;
		@$dom->loadXML($row['T007_Arquivo_XML']);
		$destFone  = $dom->getElementsByTagName("dest")->item(0)->getElementsByTagName("fone")->item(0)->nodeValue;
		$cnpj = @$dom->getElementsByTagName("dest")->item(0)->getElementsByTagName("CNPJ")->item(0)->nodeValue;
		echo "CNPJ: {$cnpj}";
		if (!empty($cnpj)) {
			$sql = "SELECT * FROM D024 WHERE REPLACE(REPLACE(REPLACE(D024_Cnpj,\".\",\"\"), '/', ''), '-', '') = '{$cnpj}';";
			$resCNPJ = mysqli_query($con,$sql);
			if (mysqli_num_rows($resCNPJ) == 0) {
				echo ' (EMPRESA N�O ENCONTRADA)';
			} else {
				$rowCNPJ = mysqli_fetch_assoc($resCNPJ);
				echo " DDD: {$rowCNPJ['D024_DDD_Telefone_1']} -> " . substr($destFone, 0, 2);
				echo " Empresa: {$rowCNPJ['D024_Nome_Empresa']}";
				if (isset($o['a'])) {
					echo ' (ALTERADO)';
					$sql = "UPDATE D024 SET D024_DDD_Telefone_1 = '" . mysqli_real_escape_string($con,substr($destFone, 0, 2)) . "' WHERE D024_Id = '{$rowCNPJ['D024_Id']}';";
					mysqli_query($con,$sql);
				}
			}
		}
		echo "\n";
	}
} else {
	echo "Opções inválidas.\n--help para ver todos os comandos.\n";
}