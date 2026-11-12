<?php
namespace hardness;

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
$atualizar = '';

/**
* Gera um relatório de consumo das empresas no servidor
*/
$dados = array();
set_time_limit(0);
$arq = '/var/www/eurosul.hardness.com.br/hardness3/dados_usuarios/script.erp.serialized';
if (!file_exists($arq)) {
	file_put_contents($arq, serialize(array('update' => array(), 'atualizacao' => array())));
}
$scriptErp = unserialize(file_get_contents($arq));
$empresas = scandir("/var/www");
foreach ($empresas as $empresa) {
	if ($empresa == '.' || $empresa == '..' || $empresa == '.htaccess' || $empresa == 'hardness' || $empresa == 'hardness3' || $empresa == 'index.html' || $empresa == 'index.php' || $empresa == 'erp.hardness.com.br' || $empresa == '.subversion') { continue; }
	$dir = "/var/www/{$empresa}/";
	include("{$dir}hardness3/bibliotecas/confUsuario.php");
	// 
	if (!in_array($empresa, $naoAtualizar_) && $empresa != 'wiki.hardness.com.br') {
		$atualizar .= $empresa . "\n";
	}
	$dados[$empresa] = array();
	
	// svn version
	//$cmd = shell_exec("sudo -u root /var/www/eurosul.hardness.com.br/hardness3/scripts/erp/runVersion.sh {$dir}hardness3/");
	//$dados[$empresa]['svnVersion'] = (int) $cmd;
	
	// dbDatabase
	$dados[$empresa]['dbDatabase'] = $confUsuario['dbDatabase'];
	
	// dbSize
	$dbSize = shell_exec("du -s /var/lib/mysql/{$confUsuario['dbDatabase']}");
	$dbSize = explode("\t", $dbSize);
	$dbSize = $dbSize[0] - 5000;
	$dados[$empresa]['dbSize'] = round($dbSize / 1024, 2);
	
	// dataSize
	$dirData1 = "{$dir}/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/";
	$dataSize1 = shell_exec("du -s {$dirData1}");
	$dataSize1 = explode("\t", $dataSize1);
	$dataSize1 = $dataSize1[0];
	$dirData2 = "{$dir}/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/";
	$dataSize2 = shell_exec("du -s {$dirData2}");
	$dataSize2 = explode("\t", $dataSize2);
	$dataSize2 = $dataSize2[0];
	$dados[$empresa]['dataSize'] = round(($dataSize1 + $dataSize2) / 1024, 2);
	
	// dataList
	$dados[$empresa]['dataList'] = array();
	
	$dataList = trim(shell_exec("cd {$dirData1} && du -s `ls -1`"));
	$dataList = explode("\n", $dataList);
	foreach ($dataList as $value) {
		$value = explode("\t", $value);
		if ($value[0] < 100) { continue; }
		$dados[$empresa]['dataList']['hardness/'.$value[1]] = round($value[0] / 1024, 2);
	}
	
	$dataList = trim(shell_exec("cd {$dirData2} && du -s `ls -1`"));
	$dataList = explode("\n", $dataList);
	foreach ($dataList as $value) {
		$value = explode("\t", $value);
		if ($value[0] < 100) { continue; }
		$dados[$empresa]['dataList']['hardness3/'.$value[1]] = round($value[0] / 1024, 2);
	}
	arsort($dados[$empresa]['dataList']);
	
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
$atualizar = trim($atualizar);

echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Hardness Sistemas - Area de Trabalho</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
		<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
		<link rel=stylesheet href="/hardness3/static/css/temas/padrao/jqueryui.css" type="text/css">
		<link rel=stylesheet href="/hardness3/static/css/temas/tema_cache.css" type="text/css">
		<style>
			body {
				background: none repeat scroll 0 0 white;
			}
		</style>
		<script>
			function runUpdate(empresa) {
				if (empresa == 'todas') {
					var buttons = {
						"Sim": function() {
							window.location = '/hardness3/scripts/erp/runUpdate.php?empresa=' + encodeURIComponent(empresa) + '&cmd=' + '&atualizar=' + encodeURIComponent($('.ui-dialog-content textarea:first').val());
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atenção!', '', buttons, false, false, $('#atualizar'));
				} else {
					var cmd = prompt("Preencha o nome de um diretório ou arquivo para filtrar o update. Exemplo arquivo: prog/ven/ven001/form.php - Exemplo diretório: prog/ven/ven001");
					if (typeof(cmd) == 'string') {
  			 			window.location = '/hardness3/scripts/erp/runUpdate.php?empresa=' + encodeURIComponent(empresa) + '&cmd=' + encodeURIComponent(cmd);
  			 		}
  			 	}
  			}
  			function runAtualizacaoBase(empresa) {
  				if (empresa == 'todas') {
					var buttons = {
						"Sim": function() {
							window.location = '/hardness3/scripts/erp/runAtualizacaoBase.php?empresa=' + encodeURIComponent(empresa) + '&atualizar=' + encodeURIComponent($('.ui-dialog-content textarea:first').val());
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atenção!', '', buttons, false, false, $('#atualizar'));
				} else {
					var cmd = confirm("Executar atualização base?");
					if (cmd) {
  			 			window.location = '/hardness3/scripts/erp/runAtualizacaoBase.php?empresa=' + encodeURIComponent(empresa);
  			 		}
  			 	}
  			}
/**
* Abre um dialog de confirmação (trava o sistema até ser respondido)
*/
function dialogConfirm(titulo, texto, buttons, big, id, \$jQueryHTML) {
	var big = (typeof(big) == 'undefined') ? false : big;
	var id = (typeof(id) == 'undefined') ? false : id;
	if (typeof(buttons) == 'undefined' || !buttons) {
		var buttons = {
			"Fechar": function() { $(this).dialog("destroy").remove(); }
		};
	}
	if (typeof(\$jQueryHTML) != 'undefined') {
		texto = \$jQueryHTML.html();
	}
	var novoId = unique();
	var \$confirmacao = $("<div>")
    	.attr({'id': 'dc-' + novoId})
    	.appendTo('body');
    if (id) {
		$("<p id='"+id+"'>").html(texto).appendTo(\$confirmacao);
	} else {
		$("<p>").html(texto).appendTo(\$confirmacao);
	}
    $("#dc-" + novoId).dialog({
    	width: 'auto',
    	height: 'auto',
    	minWidth: 250,
		resizable: false,
		modal: true,
		title: titulo,
		buttons: buttons,
		zIndex: 9999, // (faz travar as vezes o conteúdo do dialog - tem que ser um número baixo)
		dialogClass: (big) ? 'ui-dialog-big' : ''
	});
	$("#dc-" + novoId).parent().find('.ui-dialog-titlebar a').remove();
	/*
	setTimeout(function() {
		$("#dc-" + novoId).parent().find('button:last').focus();
	}, 500);
	*/
	return novoId;
}

/**
 * Gera um ID (string) único
 */
function unique()
{
	var str = "";
	var chars = "0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";			
	for(var i = 0; i < 13; i++)
	{
		str += chars.substr( Math.floor(Math.random() * 62), 1 );
	}
	//var newDate = new Date; + newDate.getTime()
	return str;
}

		</script>
	</head>
<body>
<table border=1 cellpadding=8 cellspacing=5>
<tr>
	<th>Empresa</th>
	<th>Base</th>
	<th>DB</th>
	<th>Dados</th>
	<th>Dados (Lista)</th>
	<th>Média Requisições/Dia</th>
	<th>Nos X Últimos Dias</th>
	<th>Erros PHP</th>
	<th>SVN Update</th>
	<th>Atualização Base</th>
</tr>
<tr>
	<td style="text-align: center; font-weight: bold;">Todas</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td style="text-align: center;"><a href="#" onclick="runUpdate('todas'); return false;">SVN Update</a></td>
	<td style="text-align: center;"><a href="#" onclick="runAtualizacaoBase('todas'); return false;">Atualização Base</a></td>
</tr>
EOT;
foreach ($dados as $key => $value) {
	$value['dbSize'] = number_format($value['dbSize'], 2, ',', '.');
	$value['dataSize'] = number_format($value['dataSize'], 2, ',', '.');
	$value['accessAvgDay'] = number_format($value['accessAvgDay'], 0, ',', '.');
	$value['days'] = number_format($value['days'], 1, ',', '.');
	echo '<tr>';
		echo "<td style='text-align: center; padding: 5px; font-weight: bold;'><a href=\"http://{$key}/login\">{$key}</a></td>";
		echo "<td style='text-align: center;'>{$value['dbDatabase']}</td>";
		echo "<td style='text-align: right;'>{$value['dbSize']} MB</td>";
		echo "<td style='text-align: right;'>{$value['dataSize']} MB</td>";
		echo "<td style='text-align: left; vertical-align: top;'>";
			foreach ($value['dataList'] as $key2 => $value2) {
				$value2 = number_format($value2, 2, ',', '.');
				echo "{$value2} MB ({$key2})<br />";
			}
		echo '</td>';
		echo "<td style='text-align: center;'>{$value['accessAvgDay']}</td>";
		echo "<td style='text-align: center;'>{$value['days']}</td>";
		if ($key != 'wiki.hardness.com.br') {
			echo "<td style='text-align: center;'><a href='/hardness3/scripts/erp/errosPHP.php?log={$key}-error.log'>Erros PHP</a></td>";
			echo "<td style='text-align: center;'><a href='#' onclick=\"runUpdate('{$key}'); return false;\">SVN Update</a>{$value['update']}</td>";
			echo "<td style='text-align: center;'><a href='#' onclick=\"runAtualizacaoBase('{$key}'); return false;\">Atualização Base</a>{$value['atualizacao']}</td>";
		}
	echo '</tr>';
}
$gerado = date('d/m/Y h:i:s');
echo <<<EOT
</table>
<br />
Gerado em: {$gerado}
<div id="atualizar" style="display: none;">
	<h3 style="padding: 0px 6px;">Lista de empresas que será feito update/autalização:</h3>
	<textarea style="width: 500px; height: 230px;">{$atualizar}</textarea>
	<h3 style="padding: 0px 6px;">Empresas em produção (fora da lista):</h3>
	<textarea style="width: 500px; height: 150px;" readonly="readonly">{$naoAtualizar}</textarea>
</div>
</body>
</html>
EOT;
