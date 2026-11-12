<?php
set_time_limit(0);

// Output A
$erros = array();
$arquivo = '/tmp/gErrorHandler';
if (file_exists($arquivo)) {
	$erros = array_reverse(unserialize(file_get_contents($arquivo)));
}
$outputA = '';
foreach ($erros as $erro) {
	if ($erro['timestamp'] < (time() - 43200)) { continue; }
	$outputA .= '<tr>';
	$outputA .= "<td style='color: gray; font-weight: bold;'>{$erro['dbDatabase']}</td>";
	switch($erro['errno']) {
		case E_ERROR:
			$erroTexto = '<span style="font-weight: bold; color: red;">FATAL</span>';
		break;
		case E_WARNING:
			$erroTexto = '<span style="font-weight: bold; color: brown;">WARNING</span>';
		break;
		default:
			$erroTexto = '<span style="font-weight: bold; color: blue;">DESCONHECIDO</span>';
		break;
	}
	$tempo = tempoDecorrido(time() - $erro['timestamp']);
	//if (isset($erro['backtrace'])) {
	//	$backtrace = var_export($erro['backtrace'], true);
	//	$backtrace = htmlentities($backtrace);
	//} else {
		$backtrace = '';
	//}
	/*$backtrace = str_replace("\n", '<br/>', $backtrace);
	$backtrace = str_replace(" ", '&nbsp;', $backtrace);
	$backtrace = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $backtrace);*/
	$id = uniqid();
	$outputA .= <<<EOT
<div id="{$id}" style="display: none;">
{$backtrace}
</div>
EOT;
	$outputA .= "<td><span style='font-size: 1.1em;'>{$erro['errstr']}</span><span style='float: right; margin-right: -2px; margin-top: -4px; cursor: pointer; color:silver;'>ocorreu $tempo atrás</span><br/>{$erroTexto}</b><span style='float: right; color: gray;''>{$erro['errfile']}:{$erro['errline']}</span></td>"; //  - <a href='#' onclick=\"alert($('#{$id}').html()); return false;\">backtrace</a>
	$outputA .= '</tr>';
}
$outputA = <<<EOT
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th style="width: 1%;">Empresa</th>
			<th>Erro</th>
		</tr>
	</thead>
	<tbody>
		{$outputA}
	</tbody>
</table>
EOT;

// Output C
$erros = array();
$arquivo = '/tmp/gSqlLentas';
if (file_exists($arquivo)) {
	$erros = array_reverse(unserialize(file_get_contents($arquivo)));
}
$outputC = '';
foreach ($erros as $erro) {
	if ($erro['timestamp'] < (time() - 43200)) { continue; }
	$tempo = tempoDecorrido(time() - $erro['timestamp']);
	$sql = $erro['sql'];
	$sqlReduzida = str_replace("\n", ' ', $sql);
	$sqlReduzida = preg_replace("/ +/", ' ', $sql);
	//$sqlReduzida = $sql;
	$sqlReduzida = substr($sqlReduzida, 0, 300) . (strlen($sqlReduzida) > 300 ? '...' : '');
	if (strlen($sql) > 5000) {
		$sql = substr($sql, 0, 5000);
	}
	//if (isset($erro['backtrace'][1])) {
	//	$backtrace = "{$erro['backtrace'][1]['file']}:{$erro['backtrace'][1]['line']}";
	//} else {
	//	$backtrace = "{$erro['backtrace'][0]['file']}:{$erro['backtrace'][0]['line']}";
	//}
	$backtrace = 0;
	$id = uniqid();
	$outputC .= '<tr onclick="$(\'#m' . $id . '\').modal(); return false;" style="cursor: pointer;">';
	$outputC .= "<td style='color: gray; font-weight: bold;'>{$erro['dbDatabase']}</td>";
	$outputC .= <<<EOT
<div id="m{$id}" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Query</h3>
	</div>
	<div class="modal-body">
		<textarea style="width: 100%; height: 400px;">{$sql}</textarea>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
	</div>
</div>
EOT;
	$outputC .= "<td><span style='font-size: 1em; color: brown;'><b>{$erro['tempo']}s</b> <span style='color: gray'>  </span></span><span style='float: right; margin-right: -2px; margin-top: -4px; cursor: pointer; color:silver;'>ocorreu $tempo atrás</span><br/>{$sqlReduzida}</b></td>"; // em {$backtrace}
	$outputC .= '</tr>';
}
$outputC = <<<EOT
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th style="width: 1%;">Empresa</th>
			<th>SQL</th>
		</tr>
	</thead>
	<tbody>
		{$outputC}
	</tbody>
</table>
EOT;

function tempoDecorrido($segundos) {
		$txt = '';
		if ($segundos > 86400) {
			$txt = $txt . ' ' . floor($segundos / 86400) . 'd';
			$segundos = $segundos % 86400; 
		}
		if ($segundos > 3600) {
			$txt = $txt . ' ' . floor($segundos / 3600) . 'h';
			$segundos = $segundos % 3600; 
		}
		if ($segundos > 60) {
			$txt = $txt . ' ' . floor($segundos / 60) . 'm';
			$segundos = $segundos % 60; 
		}
		if ($segundos) {
			$txt = $txt . ' ' . $segundos . 's';
		}
		$txt = trim($txt);
		return empty($txt) ? '0s' : $txt;
	}

if (isset($_REQUEST['output']) && $_REQUEST['output'] == 'A') {
	echo $outputA;
	die();
}

if (isset($_REQUEST['output']) && $_REQUEST['output'] == 'C') {
	echo $outputC;
	die();
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
		padding-top: 50px;
	}
	.diva:after {
		background-color: #F5F5F5;
		border: 1px solid #DDDDDD;
		border-radius: 4px 0 4px 0;
		color: #9DA0A4;
		content: "Últimos Erros PHP";
		font-size: 14px;
		font-weight: bold;
		left: -1px;
		padding: 4px 14px;
		position: absolute;
		top: -1px;
	}
	.diva {
		background-color: #FFFFFF;
		border: 1px solid #DDDDDD;
		border-radius: 4px 4px 4px 4px;
		padding: 27px 6px;
		position: relative;
	}
	.divb:after {
		background-color: #F5F5F5;
		border: 1px solid #DDDDDD;
		border-radius: 4px 0 4px 0;
		color: #9DA0A4;
		content: "Recursos";
		font-size: 14px;
		font-weight: bold;
		left: -1px;
		padding: 4px 14px;
		position: absolute;
		top: -1px;
	}
	.divb {
		background-color: #FFFFFF;
		border: 1px solid #DDDDDD;
		border-radius: 4px 4px 4px 4px;
		padding: 27px 6px;
		position: relative;
	}
	.divc:after {
		background-color: #F5F5F5;
		border: 1px solid #DDDDDD;
		border-radius: 4px 0 4px 0;
		color: #9DA0A4;
		content: "Últimos Querys Lentas";
		font-size: 14px;
		font-weight: bold;
		left: -1px;
		padding: 4px 14px;
		position: absolute;
		top: -1px;
	}
	.divc {
		background-color: #FFFFFF;
		border: 1px solid #DDDDDD;
		border-radius: 4px 4px 4px 4px;
		padding: 27px 6px;
		position: relative;
	}
	.divd:after {
		background-color: #F5F5F5;
		border: 1px solid #DDDDDD;
		border-radius: 4px 0 4px 0;
		color: #9DA0A4;
		content: "Fila NFe";
		font-size: 14px;
		font-weight: bold;
		left: -1px;
		padding: 4px 14px;
		position: absolute;
		top: -1px;
	}
	.divd {
		background-color: #FFFFFF;
		border: 1px solid #DDDDDD;
		border-radius: 4px 4px 4px 4px;
		padding: 27px 6px;
		position: relative;
	}
	</style>
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
						<li><a href="../">Empresas</a></li>
						<li class="active"><a href="./status/">Status</a></li>
						<li><a href="../nfadmin/">NF Admin</a></li>
						<li><a href="../pendencias/">Pendências</a></li>
						<li><a href="../mural/">Mural</a></li>
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

	<div class="container-fluid" style="margin: 0; padding: 0;">
		<div class="row-fluid">
			<div id="outputA" class="span6 diva" style="margin-bottom: 8px; overflow: auto; border: 1px solid #DDDDDD; box-shadow: 1px 1px 4px 1px #C5C5C5; margin: 10px; margin-right: 0; width: 100px; height: 100px;">
			</div>
			<div id="outputB" class="span6 divb" style="overflow: auto; border: 1px solid #DDDDDD; box-shadow: 1px 1px 4px 1px #C5C5C5; margin: 10px; margin-right: 0; width: 100px; height: 100px;">
				&nbsp;
			</div>
		</div>
		<div class="row-fluid">
			<div id="outputC" class="span6 divc" style="overflow: auto; border: 1px solid #DDDDDD; box-shadow: 1px 1px 4px 1px #C5C5C5; margin: 10px; margin-right: 0; margin-top: 0; width: 100px; height: 100px;">
			</div>
			<div id="outputD" class="span6 divd" style="overflow: auto; border: 1px solid #DDDDDD; box-shadow: 1px 1px 4px 1px #C5C5C5; margin: 10px; margin-right: 0; margin-top: 0; width: 100px; height: 100px;">
				<iframe style="border: 1px solid #DDDDDD;"></iframe>
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
	<script>
		var editor = false;
		$(document).ready(function() {
			var height = ($(window).height() - 30 - 50) / 2;
			var width = ($(window).width() - 12) / 2;
			$('.diva').height(height).width(width);
			$('.divb').height(height).width(width - 8);
			$('.divc').height(height).width(width);
			$('.divd').height(height).width(width - 8);
			$('.divd > iframe').height(height - 36).width(width - 23);

			$.ajax({
				url: './index.php',
				dataType: 'html',
				data: 'output=C',
				success: function(data) {
					$('#outputC').html(data);
				}
			});
			$.ajax({
				url: './index.php',
				dataType: 'html',
				data: 'output=A',
				success: function(data) {
					$('#outputA').html(data);
				}
			});
			$('#outputD iframe').attr('src', 'http://admin.hardness.com.br/sistema/funcoes/queueNF/temporealGrid/?oneWindow=true&noEmp=true&p=1&u=63&somenteGrid=true');
		});
		setTimeout(function() {
			//location.reload();
			$.ajax({
				url: './index.php',
				dataType: 'html',
				data: 'output=C',
				success: function(data) {
					$('#outputC').html(data);
				}
			});
			$.ajax({
				url: './index.php',
				dataType: 'html',
				data: 'output=A',
				success: function(data) {
					$('#outputA').html(data);
				}
			});
			$('#outputD iframe').attr('src', 'http://admin.hardness.com.br/sistema/funcoes/queueNF/temporealGrid/?oneWindow=true&noEmp=true&p=1&u=63&somenteGrid=true');
		}, 15000)
	</script>
</body>
</html>