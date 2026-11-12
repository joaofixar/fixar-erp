<?php
namespace hardness;
set_time_limit(0);
$arq = '/var/www/admin.hardness.com.br/hardness3/dados_usuarios/script.erp.serialized';
$scriptErp = unserialize(file_get_contents($arq));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Atualização Base</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
if (isset($_REQUEST['empresa'])) {
	echo '<a href="/hardness3/scripts/erp/">Voltar</a><br /><br />';
	
	if ($_REQUEST['empresa'] == 'todas') {
		$atualizacaoTodasEmpresas = true;
		$empresas = scandir("/var/www");
		foreach ($empresas as $empresa) {
			if ($empresa == '.' || $empresa == '..' || $empresa == '.htaccess' || $empresa == 'hardness' || $empresa == 'hardness3' || $empresa == 'index.html' || $empresa == 'index.php' || $empresa == 'erp.hardness.com.br' || $empresa == '.subversion') { continue; }
			if (empty($empresa) || $empresa == 'admin.hardness.com.br') { continue; }
			$_REQUEST['empresa'] = $empresa;
			echo "<h2>{$_REQUEST['empresa']}</h2>";
			// confUsuário
			$dir = "/var/www/{$_REQUEST['empresa']}/";
			include("{$dir}hardness3/bibliotecas/confUsuario.php");
			// chdir
			$g['pathRaiz'] = $confUsuario['pathRaiz'];
			chdir($g['pathRaiz']);
			// conexão
			include('bibliotecas/conexaoBanco.php');
			// executa atualização base
			include("scripts/atualizacaoBase.php");
			$scriptErp['atualizacao'][$empresa] = date('d/m/Y H:i:s');
		}
	} else {
		echo "<h2>{$_REQUEST['empresa']}</h2>";
		// confUsuário
		$dir = "/var/www/{$_REQUEST['empresa']}/";
		include("{$dir}hardness3/bibliotecas/confUsuario.php");
		// chdir
		$g['pathRaiz'] = $confUsuario['pathRaiz'];
		chdir($g['pathRaiz']);
		// conexão
		include('bibliotecas/conexaoBanco.php');
		// executa atualização base
		include("scripts/atualizacaoBase.php");
		$scriptErp['atualizacao'][$_REQUEST['empresa']] = date('d/m/Y h:i:s');
	}
}
file_put_contents($arq, serialize($scriptErp));
?>
</body>
</html>