<?php
$tipo = (isset($argv[1]) && $argv[1] == 'sql') ? 'sql' : 'normal';

$conf = array(
	'servers' => array(
		array('ERP', 'http://187.45.202.72/hardness3/scripts/notifications/status.php?tipo=' . $tipo),
		array('NOTA', 'http://nfe.hardness.com.br/hardness3/scripts/notifications/status.php?tipo=' . $tipo),
	),
	'users' => array('3c8QScWAbVB09pUww7JTbSkgLJ49Lv', 'IZ4T9E3S3FRie0xHJztNN5LQ657PUu')
);

do {

foreach ($conf['servers'] as $server) {
	echo "Verificando servidor {$server[0]}...\n";
	curl_setopt_array($ch = curl_init(), array(
		CURLOPT_URL => $server[1],
		CURLOPT_TIMEOUT => "60",
		CURLOPT_RETURNTRANSFER => true
	));
	$content = curl_exec($ch);
	curl_close($ch);

	if ($tipo == 'normal' && (empty($content) || strpos($content, '***') === false)) {
		$content = "[{$server[0]}] Servidor sem Conexao|||Possiveis erros: Internet do Servidor, Apache ou PHP***";
	}

	$content = explode('***', $content);
	foreach ($content as $value) {
		if (empty($value)) { continue; }
		$value = explode('|||', $value);
		if (!isset($value[0]) || !isset($value[1])) { continue; }
		foreach ($conf['users'] as $user) {
			echo "Enviando notificacao ({$value[0]}) ({$value[1]})...\n";
			curl_setopt_array($ch = curl_init(), array(
				CURLOPT_URL => "https://api.pushover.net/1/messages.json",
				CURLOPT_TIMEOUT => "60",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS => array(
					"token" => "bIR5RM98aWsDz4pdN9o92qMGcuwPQl",
					"user" => $user,
					"title" => $value[0],
					"message" => $value[1],
			)));
			$content = curl_exec($ch);
			echo "{$content}\n\n";
			curl_close($ch);
		}
	}
}

if ($tipo == 'sql') {
	sleep(20);
}

} while($tipo == 'sql');
