<?php

$reload = 5000;
ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
$date = date("d/m/Y H:i:s");

echo <<<EOT
\n\n{$date} - SHOW PROCESSLIST\n\n
EOT;

require_once('confConnect.php');

$con=mysqli_connect($confConnect['dbHost'], $confConnect['dbUser'], $confConnect['dbPass']);
$Error=mysqli_connect_error($con);


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
$empresa['global'] = "Global";

$process = mysqli_query($con,"show processlist");

$count=0;

while ($mProcess = mysqli_fetch_array($process))
{
	if ((strtoupper(trim(substr($mProcess['Info'],0,6)))=='SELECT' && $mProcess['Time']>10) || 
	   ((empty(trim($mProcess['Info'])) && $mProcess['Command']=='Sleep') && $mProcess['Time']>10 && $mProcess['User']!='rdsadmin') ||
	   ($mProcess['Time']>30)) {
		$count++;
	}
}

$process = mysqli_query($con,"show processlist");

while ($mProcess = mysqli_fetch_array($process))
{

	if ($count <= 3) {
		$timeOut = 30;
	} else if ($count <= 8) {
		$timeOut = 15;
	} else if ($count <= 14) {
		$timeOut = 10;
	} else  {
		$timeOut = 5;
	}

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

		if (empty(trim($mProcess['Info'])) and $mProcess['Command']=='Sleep'){
		   $timeKill = 5;	
		}  
		
		if ($mProcess['Time']<=$timeKill) {
			echo "\n-------TIMEOUT:{$timeKill}s-------\n";
		} else {

			if($confConnect['dbHost']=="hardness-prd.cqfcavbrwplk.us-east-1.rds.amazonaws.com"){
				mysqli_query($con,"CALL mysql.rds_kill(".$mProcess['Id'].");");
			}else{
				mysqli_query($con,"kill " . $mProcess['Id']);
				// echo "\n-------------\n";
				// echo "MATANDO QUERY:{$mProcess['Info']}\n Status:{$mProcess['Command']}\n";
				// echo "Tempo processamento:{$mProcess['Time']}\n Tempo Kill:{$timeKill}\n";
				// echo "\n-------------\n";
			}

			$erro = mysqli_error($con);

			if (empty($erro)) {
				echo "\n----- QUERY KILL:{$mProcess['Info']}\n Status:{$mProcess['Command']}\n";
			} else {
				echo "\nErro Mysql:".$erro."";
			}
		}
	}

	$db="";
	$nomeEmpresa="";
	if(!empty($mProcess['db'])){
		$db = "DB: {$mProcess['db']}";
		if(array_key_exists($mProcess['db'], $empresa)){
			$nomeEmpresa="EMPRESA: {$empresa[$mProcess['db']]}";
		}
	}

echo <<<EOT
	{$nomeEmpresa}
	{$db}
	ID: {$mProcess['Id']}
	USUARIO: {$mProcess['User']} 
	SEG: {$mProcess['Time']} 
	STATUS: {$mProcess['Command']}
	QUERY: {$mProcess['Info']}
	---------------------------\n
EOT;

}  

?>