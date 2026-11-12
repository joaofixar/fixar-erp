<?php

$reload = 5000;

if ($_GET['reload']>0){
	$reload = $_GET['reload'] * 1000;
}

echo <<<EOT

<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta charset="UTF-8">
	<script type="text/javascript">
		function Atualizar() {
			window.location.reload();
		}
	</script>
</head>

<body onload="setTimeout('Atualizar()', $reload)">

EOT;

require_once('confConnect.php');

$con=mysqli_connect($confConnect['dbHost'], $confConnect['dbUser'], $confConnect['dbPass']);
$Error=mysqli_connect_error($con);

if ($_GET['excluir']>0){
	mysqli_query($con,"kill {$_GET['excluir']}");
	
	if ($_GET['user']=='root'){
		header ("location: show.php?user=root");
	} else {
		header ("location: show.php");
	}

	die("Entrou");

}

$empresa['e213'] = 'GP';
$empresa['e229'] = 'GUIMEPA';
$empresa['e237'] = 'CEIGON';
$empresa['e998'] = 'TESTE';
$empresa['e998'] = 'TESTE';
$empresa['e211_novo'] = 'Eurosul';
$empresa['e211'] = 'Eurosul';
$empresa['e355'] = 'Mauer';
$empresa['e207'] = 'Pegaza';
$empresa['e003'] = "AneisRCS";
$empresa['e357'] = "Vedapar";
$empresa['e126'] = "Pegaza";

$process = mysqli_query($con, "show processlist");

$erro = mysqli_error($con);
if (!empty($erro)){
	die($erro);
}

$handle = fopen("querie_lenta.txt", "a+");

$count=0;

while ($mProcess = mysqli_fetch_array($process))
{
	if ((strtoupper(trim(substr($mProcess['Info'],0,6)))=='SELECT' && $mProcess['Time']>10) || 
	   ((empty(trim($mProcess['Info'])) && $mProcess['Command']=='Sleep') && $mProcess['Time']>10 && $mProcess['User']!='rdsadmin') ||
	   ($mProcess['Time']>30)) {
		$count++;
	}
}

$process = mysqli_query($con,"show full processlist");

// Ordena
//usort($process, 'cmp');

echo <<<EOT

	<span style='font-family:arial;font-size:18px;font-weight:bold'>SHOW PROCESSLIST ($count)</span>

	<br/><br/>

	<table border=1 cellpadding=8 cellspacing=0 style='font-family:arial;font-size:16px'>
		<tr style='font-weight:bold'>
			<td></td>
			<td>Id</td>
			<td>BD</td>
			<td>Empresa</td>
			<td>Usuario</td>
			<td>Seg</td>
			<td>Status</td>
			<td>Querie</td>
		</tr>
EOT;

while ($mProcess = mysqli_fetch_array($process))
{

	if ($count <= 3) {
		$timeOut = 30;
		$timeOutLog = 25;
	} else if ($count <= 8) {
		$timeOut = 15;
		$timeOutLog = 15;
	} else if ($count <= 14) {
		$timeOut = 10;
		$timeOut = 10;
	} else  {
		$timeOut = 5;
		$timeOutLog = 5;
	}

	if ($mProcess['Time']>5){
		$fundo = 'red';
		$cor   = 'white';
		$fonte = 'bold';
	} else {
		$fundo = 'white';
		$cor   = 'black';
		$fonte = 'normal';
	}

	echo "<tr style='background:$fundo;color:$cor;font-weight:$fonte'>";

	if (((strtoupper(trim(substr($mProcess['Info'],0,6)))=='SELECT' && strpos(strtoupper(trim($mProcess['Info'])),' INTO ')<=0) || (empty(trim($mProcess['Info'])) && $mProcess['Command']=='Sleep')) && $mProcess['User']!='rdsadmin') {

		if (strpos($mProcess['Info'],'max_execution_time')){
			$posIni = strpos($mProcess['Info'],"/*+max_execution_time(");
			$posFim = strpos($mProcess['Info'],")*/");
			$timeOutNovo = substr($mProcess['Info'],$posIni,$posFim-$posIni-1);
			$timeOutNovo = str_replace("/*+max_execution_time(","",$timeOutNovo);
			$timeOutNovo = str_replace(")*/","",$timeOutNovo);
			if ($timeOut == 30){
				$timeOut = ($timeOutNovo/100);
			}
		}

		$timeKill = $timeOut;

		if ($mProcess['Time']>=$timeOutLog) {
			//Escreve no arquivo aberto.
			fwrite($handle, "\n <b>banco {$mProcess['db']} => {$mProcess['Time']} segundos</b> \n");
			fwrite($handle, $mProcess['Info'] . " \n ");
		}

		if (empty(trim($mProcess['Info'])) and $mProcess['Command']=='Sleep'){
		   $timeKill = 5;	
		}  

		if ($mProcess['Time']<=$timeKill) {

			echo "<td style='color:orange'>timeout:{$timeKill}s</td>";

		} else {

			if($confConnect['dbHost']=="hardness-prd.cqfcavbrwplk.us-east-1.rds.amazonaws.com"){
				// mysql_query("CALL mysql.rds_kill(".$mProcess['Id'].");");
			}else{
				// mysql_query("kill " . $mProcess['Id']);
			}

			$erro = mysqli_error($con);

			if (empty($erro)) {

				echo "<td>KILL</td>";

			} else {

				echo "<td>".$erro."</td>";
				
			}
		}

	} else {
	
		echo "<td>&nbsp;</td>";

	}

	echo <<<EOT
				<td>{$mProcess['Id']}</td>
				<td>{$mProcess['db']}</td>
				<td>{$empresa[$mProcess['db']]}</td>
				<td>{$mProcess['User']}</td>
				<td>{$mProcess['Time']}</td>
				<td>{$mProcess['Command']}</td>
				<td>{$mProcess['Info']}</td>
			</tr>
EOT;

}

echo "</table>";

echo "<p>";
$file = file('querie_lenta.txt');
foreach ($file as $key => $linha) {
	echo $linha . '<br>';
}
echo "</p>";

//Fecha o arquivo.
fclose($handle);


// Compara se $a é maior que $b
function cmp($a, $b) {
  return $a['Time'] < $b['Time'];
}
 
?>