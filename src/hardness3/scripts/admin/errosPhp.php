<?php
set_time_limit(0);
$tabelaDados = '';
if (isset($_REQUEST['log'])) {
	$log = shell_exec("tail -n 1000 /var/log/apache2/{$_REQUEST['log']}");
	$log = explode("\n", $log);
	$log = array_reverse($log);
	foreach ($log as $linha) {
		/*if (strpos($linha, 'PHP Notice') !== false) {
			continue;
		}*/
		if (!empty($linha)) {
			$tabelaDados .= '<tr>';
			if (strpos($linha, 'PHP Fatal error')) {
				$linha = explode('PHP Fatal error', $linha);
				preg_match_all('/\[([^\]]+)\]/', $linha[0], $matches);
				$linha[0] = strtotime($matches[1][0]);
				$linha[0] = date("d/m/Y H:i:s", $linha[0]);
				$linha[1] = trim($linha[1], ' :');
				$tabelaDados .= "<td>{$linha[0]}</td><td style='color: red; font-weight: bold;'>PHP Fatal error</td><td>{$linha[1]}</td>";
			} else if (strpos($linha, 'PHP Warning')) {
				$linha = explode('PHP Warning', $linha);
				preg_match_all('/\[([^\]]+)\]/', $linha[0], $matches);
				$linha[0] = strtotime($matches[1][0]);
				$linha[0] = date("d/m/Y h:i:s", $linha[0]);
				$linha[1] = trim($linha[1], ' :');
				$tabelaDados .= "<td>{$linha[0]}</td><td style='color: gray; font-weight: bold;'>PHP Warning</td><td>{$linha[1]}</td>";
			} else if (strpos($linha, 'PHP Notice')) {
				if (!empty($_REQUEST['filtro'])) { continue; }
				$linha = explode('PHP Notice', $linha);
				preg_match_all('/\[([^\]]+)\]/', $linha[0], $matches);
				$linha[0] = strtotime($matches[1][0]);
				$linha[0] = date("d/m/Y h:i:s", $linha[0]);
				$linha[1] = trim($linha[1], ' :');
				$tabelaDados .= "<td>{$linha[0]}</td><td style='color: green; font-weight: bold;'>PHP Notice</td><td>{$linha[1]}</td>";
			} else if (strpos($linha, 'PHP Parse error')) {
				$linha = explode('PHP Parse error', $linha);
				preg_match_all('/\[([^\]]+)\]/', $linha[0], $matches);
				$linha[0] = strtotime($matches[1][0]);
				$linha[0] = date("d/m/Y h:i:s", $linha[0]);
				$linha[1] = trim($linha[1], ' :');
				$tabelaDados .= "<td>{$linha[0]}</td><td style='color: red; font-weight: bold;'>PHP Parse error</td><td>{$linha[1]}</td>";
			} else if (strpos($linha, 'PHP Deprecated')) {
				if (!empty($_REQUEST['filtro'])) { continue; }
				$linha = explode('PHP Deprecated', $linha);
				preg_match_all('/\[([^\]]+)\]/', $linha[0], $matches);
				$linha[0] = strtotime($matches[1][0]);
				$linha[0] = date("d/m/Y h:i:s", $linha[0]);
				$linha[1] = trim($linha[1], ' :');
				$tabelaDados .= "<td>{$linha[0]}</td><td style='color: green; font-weight: bold;'>PHP Deprecated</td><td>{$linha[1]}</td>";
			} else {
				$tabelaDados .= "<td colspan='3'>{$linha}</td>";
			}			
			$tabelaDados .= '</tr>';
		}
	}
} else {
	$logs = scandir("/var/log/apache2/");
	foreach ($logs as $log) {
		if (preg_match("/.*\-error\.log\$/", $log)) {
			echo '<a href="./errosPHP.php?log='.urlencode($log).'">'.$log.'</a><br />';
		}
	}
}
echo <<<EOT
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Horário</th>
			<th>Tipo</th>
			<th>Erro</th>
		</tr>
	</thead>
	<tbody>
		{$tabelaDados}
	</tbody>
</table>
EOT;
