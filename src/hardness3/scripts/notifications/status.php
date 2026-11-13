<?php
$conf = array(
	'id' => 'ERP',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => ''
);

echo "***";

$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : 'normal';

if ($tipo == 'normal') {
	// Load average - 5 minutos
	$uptime = shell_exec("uptime");
	$uptime = explode('load average:', $uptime);
	$uptime = explode(',', trim($uptime[1]));
	$uptime = (float) $uptime[1];
	if ($uptime > 4) {
		echo "[{$conf['id']}] Servidor Lento|||Load average: {$uptime}***";
	}
}

// Conexão com o mysql
$con = @mysqli_connect($conf['dbHost'], $conf['dbUser'], $conf['dbPass']);
$error = mysqli_connect_error($con);
if (!empty($error)) {
	if ($tipo == 'normal') {
		echo "[{$conf['id']}] Erro no MySQL|||Nao foi possivel conectar MySQL:\n{$error}***";
	}
} else if ($tipo == 'sql') {
	// Querys lentas
	$sql = "SHOW FULL PROCESSLIST;";
	$res = mysqli_query($con,$sql);
	while ($row = mysqli_fetch_assoc($res)) {
		if ($row['Command'] == 'Sleep' || $row['Command'] == 'Binlog Dump') { continue; }
		if ( (preg_match('/INSERT INTO /i', $row['Info']) && $row['Time'] > 3) || (preg_match('/UPDATE /i', $row['Info']) && $row['Time'] > 5) || (preg_match('/SELECT/i', $row['Info']) && $row['Time'] > 10) ) {
			$sql = str_replace("\n", '', $row['Info']);
			$sql = preg_replace("/ +/", ' ', $sql);
			if (strlen($sql) > 1000) {
				$sql = substr($sql, 0, 1000) . ' ...';
			}
			echo "[{$conf['id']}] SQL Lenta - {$row['Time']}s|||[{$row['db']}] {$sql}***";
			die();
		}
	}
}
