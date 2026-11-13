<?php
namespace hardness;
set_time_limit(0);
if (isset($_REQUEST['log'])) {
	echo '<a href="/hardness3/scripts/erp/">Voltar</a><br /><br />';
	$log = shell_exec("tail -n 1000 /var/log/apache2/{$_REQUEST['log']}");
	$log = explode("\n", $log);
	$log = array_reverse($log);
	echo '<table border=1>';
	foreach ($log as $linha) {
		$linha = str_replace('PHP Fatal error', "<span style='color: red; font-weight: bold;'>PHP Fatal error</span>", $linha);
		$linha = str_replace('PHP Warning', "<span style='color: gray; font-weight: bold;'>PHP Warning</span>", $linha);
		$linha = str_replace('PHP Notice', "<span style='color: green; font-weight: bold;'>PHP Notice</span>", $linha);
		/*if (strpos($linha, 'PHP Notice') !== false) {
			continue;
		}*/
		if (!empty($linha)) {
			echo "<tr><td>{$linha}</td></tr>";
		}
	}
	echo '<table border=1>';
} else {
	$logs = scandir("/var/log/apache2/");
	foreach ($logs as $log) {
		if (preg_match("/.*\-error\.log\$/", $log)) {
			echo '<a href="./errosPHP.php?log='.urlencode($log).'">'.$log.'</a><br />';
		}
	}
}