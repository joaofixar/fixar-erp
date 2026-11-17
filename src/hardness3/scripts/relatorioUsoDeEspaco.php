<?php
namespace hardness;
$confUsuarioERP = array(
	'dbDatabase' => 'global',
	'dbHost' => '187.45.202.72',
	'dbUser' => 'root',
	'dbPass' => '$$hard@@0982',
	//'dbPass' => 'hardness',
);
$conERP=@mysqli_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// mysql_select_db($confUsuarioERP['dbDatabase'], $conERP) or die (mysql_error());
mysqli_set_charset($conERP,'utf8');
mysqli_query($conERP,'SET NAMES utf8;');

$usoDisco = array();

if (false) {
$listabancos = scandir("/var/www/dados/v3/dados_usuarios/");
$total = 0;
foreach ($listabancos as $banco) {
	if ($banco == '.' || $banco == '..') { continue; }
	echo "Limpando {$banco}...\n";
	//
	$dir = "/var/www/dados/v3/dados_usuarios/{$banco}/tmp/";
	$duDir = du($dir);
	if ($duDir > 0) {
		$total += $duDir;
		echo "\t{$dir} - " . gCorrigeNumero($duDir/1024, 0) . " MB\n";
		$cmd = "ionice -c3 rm -f {$dir}*";
		//echo $cmd . "\n";
		shell_exec($cmd);
	}
	/*
	// 
	$listausuarios = scandir("/var/www/dados/v3/dados_usuarios/{$banco}/usuarios/");
	foreach ($listausuarios as $usuario) {
		if ($usuario == '.' || $usuario == '..') { continue; }
		$dir = "/var/www/dados/v3/dados_usuarios/{$banco}/usuarios/{$usuario}/arq_temp/";
		$duDir = du($dir);
		if ($duDir > 0) {
			$total += $duDir;
			echo "\t{$dir} - " . gCorrigeNumero($duDir/1024, 0) . " MB\n";
		}
	}
	*/
}
echo "\nTotal liberado: " . gCorrigeNumero($total/1024, 0) . " MB\n";
echo "\n\n";
}

$listabancos = scandir("/var/www/dados/v3/dados_usuarios/");
foreach ($listabancos as $banco) {
	if ($banco == '.' || $banco == '..') { continue; }
	echo "Analizando {$banco}...\n";
	$totalGeral = 0;
	//
	$output = du("/var/lib/mysql/{$banco}/");
	$usoDisco[$banco]['Banco de Dados'] = $output;
	$totalGeral += $output;
	//
	// du -s /var/lib/mysql/e211/* | sort -n -r | head -n 10
	$total = 0;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/anexosEmail/");
	$usoDisco[$banco]['Emails - Anexos'] = $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/certificados/");
	$usoDisco[$banco]['Documentos'] = $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/documentos/");
	$usoDisco[$banco]['Documentos'] += $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/di/");
	$usoDisco[$banco]['Documentos'] += $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/orcamento/");
	$usoDisco[$banco]['Documentos'] += $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/pedido/");
	$usoDisco[$banco]['Documentos'] += $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/nfe/");
	$usoDisco[$banco]['NFE'] = $output;
	$total += $output;
	$totalGeral += $output;
	//
	$output = du("/var/www/dados/v2/dados_usuarios/{$banco}/nfe/");
	$usoDisco[$banco]['NFE'] += $output;
	$total += $output;
	$totalGeral += $output;
	// 
	$output = du("/var/www/dados/v2/dados_usuarios/{$banco}/");
	$usoDisco[$banco]['Outros'] = $output;
	// 
	$output = du("/var/www/dados/v3/dados_usuarios/{$banco}/");
	$usoDisco[$banco]['Outros'] = $output - $total;
	//
	$usoDisco[$banco]['Total Geral'] = $totalGeral;
}

function cmp($a, $b) {
	if ($a['Total Geral'] == $b['Total Geral']) {
		return 0;
	}
	return ($a['Total Geral'] > $b['Total Geral']) ? -1 : 1;
}
uasort($usoDisco, "cmp");

echo "Relatório:\n\n";
foreach($usoDisco as $banco => $array) {
	echo "\n" . $banco . ":\n";
	foreach ($array as $key => $value) {
		$total += $value;
		echo "\t{$key}: " . gCorrigeNumero($value/1024, 0) . " MB\n";
	}
}

function du($dir) {
	$output = 0;
	if (file_exists($dir)) {
		$cmd = "ionice -c3 du -cs " . $dir;
		$output = shell_exec($cmd);
		$output = explode("\n", $output);
		$output = trim(str_replace('total', '', $output[count($output)-2]));
	}
	return $output;
}

function gCorrigeNumero($valor, $decimal = 2) {
	// para lidar com o callback do grid
	if (is_array($valor)) {
		if (empty($valor['campoAtual'])) { return ''; } 
		$valor = $valor['campoAtual'];
	}
	if (!empty($valor) && is_numeric($valor)) {
		return number_format($valor, $decimal, ',', '.');
	} else {
		return str_pad('0,', $decimal+2, "0", STR_PAD_RIGHT);
	}
}
