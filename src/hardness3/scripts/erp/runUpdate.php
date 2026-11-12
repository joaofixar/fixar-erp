<?php
namespace hardness;
set_time_limit(0);
$arq = '/var/www/admin.hardness.com.br/hardness3/dados_usuarios/script.erp.serialized';
$scriptErp = unserialize(file_get_contents($arq));
if (isset($_REQUEST['empresa'])) {
	$cmd = $_REQUEST['cmd'];
	if (strpos($cmd, '&') !== false || strpos($cmd, ';') !== false || strpos($cmd, '|') !== false) { die('Tentativa de executar um outro comando após o SVN UP!'); }
	echo '<a href="/hardness3/scripts/erp/">Voltar</a> - <a href="/hardness3/scripts/erp/runAtualizacaoBase.php?empresa=' . urlencode($_REQUEST['empresa']) . '&atualizar=' . urlencode($_REQUEST['atualizar']) . '">' . utf8_decode('Atualização Base') . '</a><br /><br />';
	if ($_REQUEST['empresa'] == 'todas') {
		$empresas = scandir("/var/www");
		foreach ($empresas as $empresa) {
			if ($empresa == '.' || $empresa == '..' || $empresa == '.htaccess' || $empresa == 'hardness' || $empresa == 'hardness3' || $empresa == 'index.html' || $empresa == 'index.php' || $empresa == 'erp.hardness.com.br' || $empresa == '.subversion') { continue; }
			if (empty($empresa)) { continue; }
			$log = shell_exec("ionice -c 3 sudo -u root /var/www/admin.hardness.com.br/hardness3/scripts/erp/runUpdate.sh {$empresa} {$cmd}");
			$log = str_replace("\n", "<br />", $log);
			//$log = $empresa . "<br />";
			echo $log;
			$scriptErp['update'][$empresa] = date('d/m/Y H:i:s');
		}
	} else {
		$log = shell_exec("ionice -c 3 sudo -u root /var/www/admin.hardness.com.br/hardness3/scripts/erp/runUpdate.sh {$_REQUEST['empresa']} {$cmd}");
		//echo "<h2>{$_REQUEST['empresa']}</h2>";
		$log = str_replace("\n", "<br />", $log);
		echo $log;
		$scriptErp['update'][$_REQUEST['empresa']] = date('d/m/Y h:i:s');
	}
}
file_put_contents($arq, serialize($scriptErp));