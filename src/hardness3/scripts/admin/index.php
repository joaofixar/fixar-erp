<?php
set_time_limit(0);
// Lista de empresas em produção:
$naoAtualizar = <<<EOT
eurosul.hardness.com.br
fixar.hardness.com.br
gp.hardness.com.br
sigma.hardness.com.br
tudo.hardness.com.br
alumifix.hardness.com.br
EOT;
$naoAtualizar_ = explode("\n", $naoAtualizar);

$buscarEmpresa = isset($_REQUEST['buscarEmpresa']) ? $_REQUEST['buscarEmpresa'] : '';

$dados = apc_fetch('dados');
if (!$dados) {
	$dados = array('atualizar' => '');
	$empresas = scandir("/var/www");
	foreach ($empresas as $empresa) {
		if ($empresa == '.' || $empresa == '..' || $empresa == '.htaccess' || $empresa == 'hardness' || $empresa == 'hardness3' || $empresa == 'index.html' || $empresa == 'index.php' || $empresa == 'erp.hardness.com.br' || $empresa == '.subversion') { continue; }
		$dir = "/var/www/{$empresa}/";
		include("{$dir}hardness3/bibliotecas/confUsuario.php");
		// 
		if (!in_array($empresa, $naoAtualizar_) && $empresa != 'wiki.hardness.com.br') {
			$dados['atualizar'] .= $empresa . "\n";
		}
		$dados[$empresa] = array();
		
		// svn version
		//$cmd = shell_exec("sudo -u root /var/www/eurosul.hardness.com.br/hardness3/scripts/erp/runVersion.sh {$dir}hardness3/");
		//$dados[$empresa]['svnVersion'] = (int) $cmd;
		
		// dbDatabase
		$dados[$empresa]['dbDatabase'] = $confUsuario['dbDatabase'];
		
		// dbSize
		/*$dbSize = shell_exec("ionice -c 3 du -s /var/lib/mysql/{$confUsuario['dbDatabase']}");
		$dbSize = explode("\t", $dbSize);
		$dbSize = $dbSize[0] - 5000;
		$dados[$empresa]['dbSize'] = round($dbSize / 1024, 2);*/
		$dados[$empresa]['dbSize'] = 0;
		
		// dataSize
		/*$dirData1 = "{$dir}/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/";
		$dataSize1 = shell_exec("ionice -c 3 du -s {$dirData1}");
		$dataSize1 = explode("\t", $dataSize1);
		$dataSize1 = $dataSize1[0];
		$dirData2 = "{$dir}/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/";
		$dataSize2 = shell_exec("ionice -c 3 du -s {$dirData2}");
		$dataSize2 = explode("\t", $dataSize2);
		$dataSize2 = $dataSize2[0];
		$dados[$empresa]['dataSize'] = round(($dataSize1 + $dataSize2) / 1024, 2);*/
		$dados[$empresa]['dataSize'] = 0;
		
		// dataList
		$dados[$empresa]['dataList'] = array();
		
		/*$dataList = trim(shell_exec("cd {$dirData1} && ionice -c 3 du -s `ls -1`"));
		$dataList = explode("\n", $dataList);
		foreach ($dataList as $value) {
			$value = explode("\t", $value);
			if ($value[0] < 100) { continue; }
			$dados[$empresa]['dataList']['hardness/'.$value[1]] = round($value[0] / 1024, 2);
		}
		
		$dataList = trim(shell_exec("cd {$dirData2} && ionice -c 3 du -s `ls -1`"));
		$dataList = explode("\n", $dataList);
		foreach ($dataList as $value) {
			$value = explode("\t", $value);
			if ($value[0] < 100) { continue; }
			$dados[$empresa]['dataList']['hardness3/'.$value[1]] = round($value[0] / 1024, 2);
		}
		arsort($dados[$empresa]['dataList']);*/
		
		// access
		$dirLogLastGz = "/var/log/apache2/{$empresa}-access.log.2.gz";
		$dirLog1 = "/var/log/apache2/{$empresa}-access.log";
		$dirLog2 = "/var/log/apache2/{$empresa}-access.log.1";
		if (file_exists($dirLogLastGz)) {
			$total1 = shell_exec("cat {$dirLog1} | wc -l");
			$total2 = shell_exec("cat {$dirLog2} | wc -l");
			$dados[$empresa]['access'] = $total1 + $total2;
			$dados[$empresa]['days'] = round((time() - filemtime($dirLogLastGz))/86400, 1);
			$dados[$empresa]['accessAvgDay'] = round($dados[$empresa]['access'] / $dados[$empresa]['days']);
		} else {
			$dados[$empresa]['access'] = 0;
			$dados[$empresa]['days'] = 0;
			$dados[$empresa]['accessAvgDay'] = 0;
		}
		
		// update e atualizacao
		$dados[$empresa]['update'] = (isset($scriptErp['update'][$empresa])) ? '<br />' . $scriptErp['update'][$empresa] : '';
		$dados[$empresa]['atualizacao'] = (isset($scriptErp['atualizacao'][$empresa])) ? '<br />' . $scriptErp['atualizacao'][$empresa] : '';
	}
	$dados['atualizar'] = trim($dados['atualizar']);
	apc_store('dados', $dados, 86400);
}

/*$tabelaDados = <<<EOT
<tr>
	<td style='text-align: center; padding: 5px; font-weight: bold; width: 10%; vertical-align: middle; padding: 0 20px;' colspan=4>Todas</td>
	<td>
		<button class='btn btn-small btn-info' type='button' onclick="runUpdate('todas'); return false;">SVN Update</button> <button class='btn btn-small btn-info' type='button' onclick="runAtualizacaoBase('todas'); return false;">Atualização Base</button>
	</td>
</td>
EOT;*/
foreach ($dados as $key => $value) {
	if (!empty($buscarEmpresa) && stripos($key, $buscarEmpresa) === false) {
		continue;
	}
	if ($key == 'atualizar') { continue; }
	if ($key == 'wiki.hardness.com.br') { continue; }

	// svn info 2
	$output = shell_exec("cd /var/www/{$key}/hardness/ && svn info");
	//print_r($output);
	preg_match("/Revision: ([0-9]+)/", $output, $matches);
	$svn2Rev = trim($matches[1]);
	preg_match('/URL: ([^\n]+)/', $output, $matches);
	$svn2Ver = trim($matches[1]);
	$svn2Ver = explode('/', $svn2Ver);
	$svn2Ver = $svn2Ver[count($svn2Ver)-1];

	// svn info 3
	$output = shell_exec("cd /var/www/{$key}/hardness3/ && svn info");
	//print_r($output);
	preg_match("/Revision: ([0-9]+)/", $output, $matches);
	$svn3Rev = trim($matches[1]);
	preg_match('/URL: ([^\n]+)/', $output, $matches);
	$svn3Ver = trim($matches[1]);
	$svn3Ver = explode('/', $svn3Ver);
	$svn3Ver = $svn3Ver[count($svn3Ver)-1];

	//$value['size'] = (float) $value['dbSize'] + (float) $value['dataSize'];
	//$value['size'] = number_format($value['size'], 2, ',', '.');
	//$value['dbSize'] = number_format($value['dbSize'], 2, ',', '.');
	//$value['dataSize'] = number_format($value['dataSize'], 2, ',', '.');
	//$value['size'] = $value['dbSize'] . ' / ' . $value['dataSize'];
	//$value['accessAvgDay'] = number_format($value['accessAvgDay'], 0, ',', '.');
	//$value['days'] = number_format($value['days'], 1, ',', '.');
	$tabelaDados .= '<tr>';
	$tabelaDados .= "<td style='text-align: center; padding: 5px; font-weight: bold; width: 10%; vertical-align: middle; padding: 0 20px;'><a href=\"http://{$key}/login\" target='blank_'>{$key}</a></td>";
	$tabelaDados .= "<td style='text-align: center; width: 10%; vertical-align: middle;'>{$value['dbDatabase']}</td>";
	$tabelaDados .= "<td style='text-align: center; width: 10%; vertical-align: middle;'>{$svn2Ver} / {$svn2Rev}</td>";
	$tabelaDados .= "<td style='text-align: center; width: 10%; vertical-align: middle;'>{$svn3Ver} / {$svn3Rev}</td>";
	//$tabelaDados .= "<td style='text-align: right; width: 10%; vertical-align: middle;'>{$value['size']} MB</td>";
	//$tabelaDados .= "<td style='text-align: right; width: 10%; vertical-align: middle;'>{$value['accessAvgDay']}</td>";
	if ($key != 'wiki.hardness.com.br') {
		$tabelaDados .= "<td>";
		//$tabelaDados .= "<button class='btn btn-small btn-primary' type='button' onclick=\"sql('{$value['dbDatabase']}'); return false;\">SQL</button> ";
		$tabelaDados .= "<button class='btn btn-small btn-danger' type='button' onclick=\"errorPhp('{$key}-error.log'); return false;\">Erros PHP</button> ";
		//$tabelaDados .= "<button class='btn btn-small btn-info' type='button' onclick=\"runUpdate('{$key}'); return false;\">SVN Update</button> ";
		//$tabelaDados .= "<button class='btn btn-small btn-info' type='button' onclick=\"runAtualizacaoBase('{$key}'); return false;\">Atualização Base</button> ";
		$tabelaDados .= "</td>";
	}
	$tabelaDados .= '</tr>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Interface Admin - Hardnes Sistemas</title>

	<!-- Le styles -->
	<link href="/hardness3/static/css/temas/empresa/jqueryui.css" rel="stylesheet">
	<link href="/hardness3/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="/hardness3/bibliotecas/codeMirror-3.02/lib/codemirror.css" rel="stylesheet">
	<style type="text/css">
	body {
		padding-top: 60px;
		padding-bottom: 10px;
	}
	.CodeMirror { 
		width: 100%; 
		height: 170px; 
		border: 1px solid #DDDDDD;
	}
	.CodeMirror-scroll {
		height: 170px; 
	}
	</style>
	<script>
		var editor = false;
	</script>
</head>

<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="#">Admin Hardness</a>
				<div class="nav-collapse collapse">
					<p class="navbar-text pull-right">
						Hardness Sistemas
					</p>
					<ul class="nav">
						<li class="active"><a href="./">Empresas</a></li>
						<li><a href="./status/">Status</a></li>
						<li><a href="./nfadmin/">NF Admin</a></li>
						<li><a href="./pendencias/">Pendências</a></li>
						<li><a href="./mural/">Mural</a></li>
						<li><a href="../agenda/">Agenda</a></li>
						<li><a href="../comercial/">Comercial</a></li>
						<li><a href="../marcelo/">Marcelo</a></li>
						<li><a href="../tiago/">Tiago</a></li>
						<!--<li><a href="#about">NF</a></li>-->
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<ul class="breadcrumb">
					<li><a href="#">ERP</a>
						<span class="divider">/</span></li>
					<li class="active">Lista de Empresas</li>
				</ul>
				<form>
					<div class="input-append">
						<input class="span2" id="buscarEmpresa" name="buscarEmpresa" type="text" placeholder="nome da empresa" value="<?php echo $buscarEmpresa; ?>">
						<button class="btn" type="submit">Filtrar</button>
						<button class="btn" type="button" onclick="$('#buscarEmpresa').val(''); return false;">Limpar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Empresa</th>
							<th>Base</th>
							<th>SVN 2</th>
							<th>SVN 3</th>
							<th>Ações</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $tabelaDados; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Placed at the end of the document so the pages load faster -->
	<script src="/hardness3/static/js/jquery-1.7.2.min.js"></script>
	<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script src="/hardness3/static/bootstrap/js/bootstrap.min.js"></script>
	<script src="/hardness3/scripts/admin/js.js"></script>
	<script src="/hardness3/bibliotecas/codeMirror-3.02/lib/codemirror.js"></script>
	<script src="/hardness3/bibliotecas/codeMirror-3.02/mode/mysql/mysql.js"></script>
</body>
</html>