<?php
namespace hardness;
require 'scripts/queueNF/codigos.php';

function Q002_Codigo_Resposta($array) {
	global $codigos;
	if (isset($array['todosCampos']['Q002_Resposta_Extra'])) {
		$extra = $array['todosCampos']['Q002_Resposta_Extra'];
		$return = $codigos[$array['todosCampos']['Q002_Codigo_Resposta']] . ' - ' . $extra;
	} else {
		$return = $array['todosCampos']['Q002_Codigo_Resposta'] . ' - ' . $codigos[$array['todosCampos']['Q002_Codigo_Resposta']];
	}
	return $return;
}

function Q002_Codigo_Resposta_Tipo($array) {
	global $codigos, $g;
	if ($array['todosCampos']['Q002_Codigo_Resposta'] < 100) {
		return '<span style="color: green; font-weight: bold;">NORMAL</span>';
	} else if ($array['todosCampos']['Q002_Codigo_Resposta'] < 200) {
		return '<span style="color: red; font-weight: bold;">ERRO INTERNO</span>';
	} else {
		return '<span style="color: red; font-weight: bold;">ERRO SEFAZ</span>';
	}
}

function Q002_Nr_NF_Por_Empresa($array) {
	$sql = "SELECT COUNT(*) FROM Q002 WHERE Q002_C004_Nome_Abreviado = '{$array['todosCampos']['Q002_C004_Nome_Abreviado']}' AND Q002_Codigo_Resposta = 9";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		return $row[0];
	}
}

function Q002_Valor_Total_Por_Empresa($array) {
	$sql = "SELECT SUM(Q002_T007_Valor_Total) FROM Q002 WHERE Q002_C004_Nome_Abreviado = '{$array['todosCampos']['Q002_C004_Nome_Abreviado']}' AND Q002_Codigo_Resposta = 9";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		return gCorrigeNumero($row[0]);
	}
}

function Q002_Nr_Empresas_Dif($array) {
	$sql = "SELECT COUNT(DISTINCT Q002_C004_Nome_Abreviado) FROM Q002 WHERE DATE(Q002_DataHora) = '{$array['todosCampos']['Q002_DataHora']}' AND Q002_Codigo_Resposta = 9";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		return $row[0];
	}
}

function Q002_Nr_Empresas_Dif_Banco($array) {
	$sql = "SELECT COUNT(DISTINCT Q002_Database) FROM Q002 WHERE DATE(Q002_DataHora) = '{$array['todosCampos']['Q002_DataHora']}' AND Q002_Codigo_Resposta = 9";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		return $row[0];
	}
}

function Q002_Nr_Erros($array) {
	$sql = "SELECT COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id)) FROM Q002 WHERE DATE(Q002_DataHora) = '{$array['todosCampos']['Q002_DataHora']}' AND Q002_Codigo_Resposta > 99";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		return $row[0];
	}
}

function Q002_Nr_Erros_Por_Empresa($array) {
	$sql = "SELECT COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id)) FROM Q002 WHERE Q002_C004_Nome_Abreviado = '{$array['todosCampos']['Q002_C004_Nome_Abreviado']}' AND Q002_Codigo_Resposta > 99";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		return $row[0];
	}
}

function Q002_Sefaz_Caiu($array) {
	$sql = "SELECT COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id)) FROM Q002 WHERE DATE(Q002_DataHora) = '{$array['todosCampos']['Q002_DataHora']}' AND Q002_Codigo_Resposta = 202 AND (Q002_Resposta_Extra = 108 OR Q002_Resposta_Extra = 109 OR Q002_Resposta_Extra = 239)";
	$res = mysql_query($sql);
	if ($res) {
		$row = mysql_fetch_array($res);
		if ($row[0] == 0) {
			return '<span style="color: green;">Não</span>';
		} else {
			return '<span style="color: red; font-weight: bold;">Sim</span>';
		}
	}
}

function Q002_Msgs_Suporte($array) {
	$retorno = '';
	$sql = <<<EOT
SELECT * FROM Q002 
WHERE
Q002_Codigo_Resposta > 199
AND Q002_Resposta_Extra = '{$array['todosCampos']['Q002_Resposta_Extra']}' AND Q002_Resposta_Extra != ''
EOT;
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$Q003_Md5 = mysql_real_escape_string(md5($row['Q002_Database'] . $row['Q002_C004_Id'] . $row['Q002_T007_Id']));
		$sql = "SELECT Q003_Mensagem FROM Q003 WHERE Q003_Md5 = '{$Q003_Md5}';";
		$res2 = mysql_query($sql);
		$row2 = mysql_fetch_assoc($res2);
		// não duplicar mensagens iguais
		if (!empty($row2['Q003_Mensagem']) && strpos($retorno, $row2['Q003_Mensagem']) === false) {
			$retorno .= empty($retorno) ? $row2['Q003_Mensagem'] : '<br />' . $row2['Q003_Mensagem'];
		}
	}
	return trim($retorno);
}

if ($g['r'][3] != 'fechar' && $g['r'][3] != 'msg' && $g['r'][3] != 'visuXML') {
echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function fechar(elem, Q002_Id) {
			var buttons = { 
				"Sim": function() { 
					$.getJSON('/sistema/funcoes/queueNF/fechar/?ajax=true&Q002_Id=' + encodeURIComponent(Q002_Id) + '&callback=?', function(request) {
						$(elem).closest('tr').remove();
					});
					$(this).dialog("destroy").remove(); 
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
    		dialogConfirm("Fechar!", 'Deseja retirar esta NF da lista?<br /><br />Quando isso é feito: a nota pode ter sido cancelada ou inutilizada, ou aconteceu algum erro no recebimento do log.', buttons)
		}
		function msg(elem, Q003_Id) {
			var buttons = { 
				"Enviar": function() { 
					$.getJSON('/sistema/funcoes/queueNF/msg/?ajax=true&Q003_Id=' + encodeURIComponent(Q003_Id) + '&msg=' + encodeURIComponent($(this).closest('.ui-dialog').find('textarea').val()) + '&callback=?', function(request) {
						divRefresh('{$g['divId']}');
					});
					$(this).dialog("destroy").remove(); 
				},
				"Fechar": function() { $(this).dialog("destroy").remove(); } 
			};
    		dialogConfirm("Mensagem do Suporte", 'Escreva uma mensagem para ser mostrada ao cliente:<br /><br /><textarea style="width: 600px; height: 100px;"></textarea>', buttons)
		}
		function xml(elem, Q003_Id) {
			$('#{$g['divId']}').showLoading();
			$.getJSON('/sistema/funcoes/queueNF/visuXML/?ajax=true&Q003_Id=' + encodeURIComponent(Q003_Id) + '&callback=?', function(request) {
				$('#{$g['divId']}').hideLoading();
				if (!request.code) {
					dialogConfirm("Erro", 'NF sem XML gerado!');
				} else {
					dialogConfirm("Visualização do XML", '<div style="width: 800px; height: 230px;" class="visuXML"></div><br /><textarea style="width: 800px; height: 150px;" class="visuXML2"></textarea>');
					setTimeout(function() {
						$('.visuXML').html(request.table);
						$('.visuXML2').val(request.xml);
					}, 200);
				}
			});
		}
	} </script></div>
EOT;
}

function Q002_Opcoes($array) {
	global $g;
	if ($array['todosCampos']['Q002_Servidor_Id'] == 'NF') {
		$return = <<<EOT
<a href="#" onclick="nova_janela('http://nfe.hardness.com.br/hardness/principal/login_nfe_externo.php?bancosuporte={$array['todosCampos']['Q002_Database']}',0,0,1200,650,'yes','no','no','no','yes','no','Nota_Emitida');">Ver NF</a>&nbsp;&nbsp;
<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Histório de ações desta NF', '/sistema/funcoes/queueNF/log/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['Q003_Q002_Id']}'), [1000,500]);">Log</a>&nbsp;&nbsp;

EOT;
	} else {
		$return = <<<EOT
<a href="#" onclick="nova_janela('{$array['todosCampos']['Q002_Url']}/ven/ven012/grid/ven012GridFilaDeEnvioNovo/?oneWindow=true&noEmp=true&p={$array['todosCampos']['Q002_C004_Id']}&u=63',0,0,1200,650,'yes','no','no','no','yes','no','Nota_Emitida');">Ver NF</a>&nbsp;&nbsp;
<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Histório de ações desta NF', '/sistema/funcoes/queueNF/log/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['Q003_Q002_Id']}'), [1000,500]);">Log</a>&nbsp;&nbsp;

EOT;
	}
	if (isset($array['todosCampos']['Q002_Id'])) {
		$return .= <<<EOT
<a href="#" onclick="fechar(this, '{$array['todosCampos']['Q002_Id']}');">Fechar</a>&nbsp;&nbsp;
<a href="#" onclick="msg(this, '{$array['todosCampos']['Q003_Id']}');">Suporte</a>&nbsp;&nbsp;
<a href="#" onclick="xml(this, '{$array['todosCampos']['Q003_Id']}');">XML</a>
EOT;
	}
	return $return;
}

switch($g['r'][3]){
	case 'tempo-real':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/queueNF/temporealTab/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'log':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/queueNF/logTab/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'temporealTab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/queueNF/temporealGrid/', 'Agora', '', false, true),
			array(uniqid(), '/sistema/funcoes/queueNF/ultimasGrid/', 'Últimas', '', false, true),
			//array(uniqid(), '/sistema/funcoes/queueNF/totaisGrid/', 'Por Empresa', '', false, true),
			//array(uniqid(), '/sistema/funcoes/queueNF/totaisDiaGrid/', 'Por Dia', '', false, true),
			array(uniqid(), '/sistema/funcoes/queueNF/errosGrid/', 'Erros Sefaz', '', false, true),
			array(uniqid(), '/sistema/funcoes/queueNF/tabelaGrid/', 'Tabela Sefaz', '', false, true),
			array(uniqid(), '/sistema/funcoes/queueNF/errosCadGrid/', 'Erros para Cadastrar', '', false, true),
			array(uniqid(), '/sistema/funcoes/queueNF/errosOutrosGrid/', 'Erros Outros', '', false, true),

		);		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'logTab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/queueNF/logGrid/', 'Histórico Ações', '&acaoId=' . urlencode($r_acaoId)),
		);		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'temporealGrid':
		mysql_query('USE global;');
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Opcoes', 'colunaTitulo' => 'Opções', 'colunaTipo' => 'livre', 'callback' => 'Q002_Opcoes', 'style' => 'white-space: normal;min-width: 130px; max-width: 130px;'), // , 'colunaExibir' => isset($_REQUEST['somenteGrid']) ? false : true
			array('colunaValor' => 'Q002_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			array('colunaValor' => 'Q003_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			array('colunaValor' => 'Q002_Servidor_Id', 'colunaTitulo' => 'Servidor', 'style' => 'width: 1%;'),
			array('colunaValor' => "CONCAT(Q002_C004_Nome_Abreviado,' ',Q002_C004_Telefone)", 'colunaTitulo' => 'Empresa', 'style' => 'font-weight:bold;font-size:14px;'),
			array('colunaValor' => 'Q002_Database', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'Q002_Numero_NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold;font-size:14px'),
			array('colunaValor' => 'Q002_T007_Serie', 'colunaTitulo' => 'Serie'),
			array('colunaValor' => 'Q002_T007_D024_Nome_Empresa', 'colunaTitulo' => 'Para'),
			array('colunaValor' => 'Q002_T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'Q002_T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'Q002_Codigo_Resposta', 'colunaTitulo' => 'Status', 'callback' => 'Q002_Codigo_Resposta'),
			array('colunaValor' => 'concat(Q002_Retorno_Sefaz,\'<br><font style="color:black;font-weight:normal"><i>\',if(Q003_Mensagem is null and  Q002_Codigo_Resposta > 99,\'<font style="background:yellow">*** ENVIE UMA MENSAGEM PARA O CLIENTE***\',\'\')) as Q002_Retorno_Sefaz', 'colunaTitulo' => 'Retorno Sefaz', 'style' => 'color:red;font-weight:bold;white-space: normal;min-width: 250px; max-width: 250px'),
			array('colunaValor' => 'Q003_Mensagem', 'colunaTitulo' => 'Suporte (Atual)', 'colunaExibir' => isset($_REQUEST['somenteGrid']) ? false : true, 'style' => 'white-space: normal;min-width: 200px; max-width: 200px'),
			//array('colunaValor' => 'MsgsSuporte', 'colunaTitulo' => 'Suporte (Histórico)', 'callback' => 'Q002_Msgs_Suporte', 'colunaTipo' => 'livre', 'colunaExibir' => isset($_REQUEST['somenteGrid']) ? false : true),
			array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => 'Extra', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_Url', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q003_Q002_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_C004_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q003';
		$extra = <<<EOT
			left join Q002 on Q002_Id=Q003_Q002_Id
			WHERE
				Q002_Codigo_Resposta != 9
			ORDER BY Q003_Q002_Id DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_Servidor_Id' => array('titulo' => 'Servidor', 'tipo' => 'texto'),
			'Q002_C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		if (!isset($_REQUEST['somenteGrid'])) {
			echo $g['ui']->gridFiltroPrint($gridFiltro);
		}
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		//$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$valorTotal = 0;
		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		if (!isset($_REQUEST['somenteGrid'])) {
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>
				<h2 style="float: left; margin: 9px 5px 0 5px;">Scan:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>-->
				<div style="float: left; margin-left: 13px;"><span class="item" style="text-align:right">Total NFs<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span></div>
				<div style="float: left; margin-left: 13px;"><span class="item" style="text-align:right">&nbsp;<br>
					<a href="http://www.fazenda.pr.gov.br/" target="_blank">Receita Estadual</a>&nbsp;&nbsp;
					<a href="http://www.nfe.fazenda.gov.br/portal/principal.aspx" target="_blank">Receita Federal</a>&nbsp;&nbsp;
					<a href="https://www.sefaz.rs.gov.br/nfe/nfe-val.aspx" target="_blank">Sintegra RS</a>&nbsp;&nbsp;
					<a href="http://www.sintegra.gov.br/" target="_blank">Sintegra</a>&nbsp;&nbsp;
				</span></div>
				<div style="clear:both;"></div>
			</div>
EOT;
		}
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		if (!isset($_REQUEST['somenteGrid'])) {
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 120000);
			} </script></div>
EOT;
		}

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'logGrid':
		mysql_query('USE global;');
		$sql = "SELECT Q002_Database, Q002_C004_Id, Q002_T007_Id FROM Q002 WHERE Q002_Id = '{$r_acaoId}';";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Q002_Servidor_Id', 'colunaTitulo' => 'Servidor'),
			array('colunaValor' => 'Q002_C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'Q002_Database', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'Q002_Numero_NF', 'colunaTitulo' => 'NF'),
			array('colunaValor' => 'Q002_T007_Serie', 'colunaTitulo' => 'Serie'),
			array('colunaValor' => 'Q002_T007_D024_Nome_Empresa', 'colunaTitulo' => 'Para'),
			array('colunaValor' => 'Q002_T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'Q002_T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'Q002_Codigo_Resposta', 'colunaTitulo' => 'Status', 'callback' => 'Q002_Codigo_Resposta'),
			array('colunaValor' => 'Q002_Retorno_Sefaz', 'colunaTitulo' => 'Retorno Sefaz'),
			array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => 'Extra', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_Url', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q002';
		$extra = <<<EOT
			WHERE
				Q002_Database = '{$row['Q002_Database']}'
				AND Q002_C004_Id = '{$row['Q002_C004_Id']}'
				AND Q002_T007_Id = '{$row['Q002_T007_Id']}'
			ORDER BY Q002_Id ASC
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad003FormCadastroRepresentantesBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad003FormCadastroRepresentantesBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Representantes', '/cad/cad003/content/cad003ContentCadastroRepresentantes/', '&tab=geral', [500,500]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total NFs<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				/*if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 60000);*/
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'ultimasGrid':
		$filtroObrigatorio = true;
		mysql_query('USE global;');
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Opcoes', 'colunaTitulo' => 'Opções', 'colunaTipo' => 'livre', 'callback' => 'Q002_Opcoes', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Q002_Servidor_Id', 'colunaTitulo' => 'Servidor'),
			array('colunaValor' => "IF((SELECT COUNT(Q002B.Q002_Id) FROM Q002 as Q002B WHERE Q002B.Q002_Database = Q002A.Q002_Database AND Q002B.Q002_C004_Id = Q002A.Q002_C004_Id AND Q002B.Q002_T007_Id = Q002A.Q002_T007_Id AND Q002B.Q002_Codigo_Resposta > 99) > 0, '<span style=\"color: red; font-weight: bold;\">Sim</span>', '<span style=\"color: green;\">Não</span>') as Erros", 'colunaTitulo' => 'Erro', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Q002_C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'Q002_Database', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'Q002_Numero_NF', 'colunaTitulo' => 'NF'),
			array('colunaValor' => 'Q002_T007_Serie', 'colunaTitulo' => 'Serie'),
			array('colunaValor' => 'Q002_T007_D024_Nome_Empresa', 'colunaTitulo' => 'Para'),
			array('colunaValor' => 'Q002_T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'Q002_T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'Q002_Codigo_Resposta', 'colunaTitulo' => 'Status', 'callback' => 'Q002_Codigo_Resposta'),
			array('colunaValor' => 'Q002_Retorno_Sefaz', 'colunaTitulo' => 'Retorno Sefaz'),
			array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => 'Extra', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_Url', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q003_Q002_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q003';
		$extra = <<<EOT
			left join Q002 as Q002A on Q002_Id=Q003_Q002_Id
			WHERE
				Q002_Codigo_Resposta = 9
			ORDER BY Q003_Q002_Id DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_Servidor_Id' => array('titulo' => 'Servidor', 'tipo' => 'texto'),
			'Q002_T007_Data_Emissao' => array('titulo' => 'Data de Emissão', 'tipo' => 'data'),
			'Q002_C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			"IF((SELECT COUNT(Q002B.Q002_Id) FROM Q002 as Q002B WHERE Q002B.Q002_Database = Q002A.Q002_Database AND Q002B.Q002_C004_Id = Q002A.Q002_C004_Id AND Q002B.Q002_T007_Id = Q002A.Q002_T007_Id AND Q002B.Q002_Codigo_Resposta > 99) > 0[[virgula]] '<span style=\"color: red; font-weight: bold;\">Sim</span>'[[virgula]] '<span style=\"color: green;\">Não</span>')" => array('titulo' => 'Teve Erro', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 250);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		echo <<<EOT
			<div class="buttonsBottomTop">
				<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>
				<h2 style="float: left; margin: 9px 5px 0 5px;">Scan:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>
				<div style="float: left; margin-left: 13px;"><span class="item" style="text-align:right">Total NFs<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span></div>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				/*if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 60000);*/
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'totaisGrid':
		$filtroObrigatorio = true;
		mysql_query('USE global;');
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Q002_Servidor_Id', 'colunaTitulo' => 'Servidor', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Q002_C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Total', 'colunaTitulo' => 'Notas Emitidas', 'colunaTipo' => 'livre', 'callback' => 'Q002_Nr_NF_Por_Empresa', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Erros', 'colunaTitulo' => 'Erros', 'colunaTipo' => 'livre', 'callback' => 'Q002_Nr_Erros_Por_Empresa', 'style' => 'width: 1%;'),
			array('colunaValor' => 'ValorTotal', 'colunaTitulo' => 'Valor Total', 'colunaTipo' => 'livre', 'callback' => 'Q002_Valor_Total_Por_Empresa'),
			array('colunaValor' => 'Q003_Q002_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q003';
		$extra = <<<EOT
			left join Q002 as Q002B on Q002_Id=Q003_Q002_Id
			WHERE
				Q002_Codigo_Resposta = 9
			GROUP BY Q002_C004_Nome_Abreviado
			ORDER BY Q002_C004_Nome_Abreviado ASC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_Servidor_Id' => array('titulo' => 'Servidor', 'tipo' => 'texto'),
			'Q002_C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		echo <<<EOT
			<div class="buttonsBottomTop">
				<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>
				<h2 style="float: left; margin: 9px 5px 0 5px;">Scan:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'totaisDiaGrid':
		$filtroObrigatorio = true;
		mysql_query('USE global;');
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'DATE(Q002_DataHora) as Q002_DataHora', 'colunaTitulo' => 'Data', 'style' => 'width: 1%;', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'COUNT(*) as Total', 'colunaTitulo' => 'Notas Emitidas', 'style' => 'width: 1%;'),
			array('colunaValor' => 'TotalEmp', 'colunaTitulo' => 'Empresas', 'colunaTipo' => 'livre', 'callback' => 'Q002_Nr_Empresas_Dif', 'style' => 'width: 1%;'),
			array('colunaValor' => 'TotalBanco', 'colunaTitulo' => 'Empresas (banco)', 'colunaTipo' => 'livre', 'callback' => 'Q002_Nr_Empresas_Dif_Banco', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Erros', 'colunaTitulo' => 'Erros', 'colunaTipo' => 'livre', 'callback' => 'Q002_Nr_Erros', 'style' => 'width: 1%;'),
			array('colunaValor' => 'sefazOut', 'colunaTitulo' => 'Sefaz caiu?', 'colunaTipo' => 'livre', 'callback' => 'Q002_Sefaz_Caiu', 'style' => 'width: 1%;'),
			array('colunaValor' => 'AVG(UNIX_TIMESTAMP((SELECT Q002B.Q002_DataHora FROM Q002 AS Q002B WHERE Q002B.Q002_Database = Q002_Database AND Q002B.Q002_C004_Id = Q002_C004_Id AND Q002B.Q002_T007_Id = Q002_T007_Id AND Q002B.Q002_Codigo_Resposta = 9 LIMIT 1)) - UNIX_TIMESTAMP(Q002_DataHora)) as Média', 'colunaTitulo' => 'Tempo médio (s)', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'SUM(Q002_T007_Valor_Total) as ValorTotal', 'colunaTitulo' => 'Valor Total', 'callback' => 'gCorrigeNumero'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q003';
		$extra = <<<EOT
			left join Q002 as Q002B on Q002_Id=Q003_Q002_Id
			WHERE
				Q002_Codigo_Resposta = 9
			GROUP BY DATE(Q002_DataHora)
			ORDER BY DATE(Q002_DataHora) DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_Servidor_Id' => array('titulo' => 'Servidor', 'tipo' => 'texto'),
			'Q002_C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		echo <<<EOT
			<div class="buttonsBottomTop">
				<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>
				<h2 style="float: left; margin: 9px 5px 0 5px;">Scan:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				/*if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 60000);*/
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'errosGrid':	
		mysql_query('USE global;');
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id, Q002_Codigo_Resposta)) as Total', 'colunaTitulo' => 'Total', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => 'Código Erro', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Q002_Retorno_Sefaz', 'colunaTitulo' => 'Erro - Retorno Sefaz'),
			array('colunaValor' => 'MsgsSuporte', 'colunaTitulo' => 'Suporte (Histórico)', 'callback' => 'Q002_Msgs_Suporte', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'Q002_Database', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_C004_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_T007_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q002';
		$extra = <<<EOT
			WHERE Q002_Codigo_Resposta = 202
			GROUP BY Q002_Resposta_Extra
			ORDER BY COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id, Q002_Codigo_Resposta)) DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_Servidor_Id' => array('titulo' => 'Servidor', 'tipo' => 'texto'),
			'Q002_C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		echo <<<EOT
			<div class="buttonsBottomTop">
				<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>
				<h2 style="float: left; margin: 9px 5px 0 5px;">Scan:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				/*if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 60000);*/
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'errosOutrosGrid':
		mysql_query('USE global;');
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Q002_Codigo_Resposta', 'colunaTitulo' => 'Código', 'callback' => 'Q002_Codigo_Resposta', 'style' => 'width: 1%;'),
			array('colunaValor' => 'COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id, Q002_Codigo_Resposta)) as Total', 'colunaTitulo' => 'Total', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => 'Código Erro', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'GROUP_CONCAT(DISTINCT Q002_Resposta_Extra SEPARATOR \'<br />\')', 'colunaTitulo' => 'Erros'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q002';
		$extra = <<<EOT
			WHERE Q002_Codigo_Resposta > 99 AND Q002_Codigo_Resposta < 200
			GROUP BY Q002_Codigo_Resposta
			ORDER BY COUNT(DISTINCT CONCAT(Q002_Database, Q002_C004_Id, Q002_T007_Id, Q002_Codigo_Resposta)) DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Q002_Codigo_Resposta' => array('titulo' => 'Código', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		echo <<<EOT
			<div class="buttonsBottomTop">
				<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>
				<h2 style="float: left; margin: 9px 5px 0 5px;">Scan:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				/*if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 60000);*/
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'fechar':
		$resposta = array('code' => true, 'data' => array());
		
		$sql = "UPDATE global.Q002 SET Q002_Codigo_Resposta = 9, Q002_Retorno_Sefaz = 'Fechado Manualmente!' WHERE Q002_Id = '{$r_Q002_Id}';";
		$result = mysql_query($sql);
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'msg':
		$resposta = array('code' => true, 'data' => array());
		
		$r_msg = mysql_real_escape_string($r_msg);
		$sql = "UPDATE global.Q003 SET Q003_Mensagem = '{$r_msg}' WHERE Q003_Id = '{$r_Q003_Id}';";
		$result = mysql_query($sql);
		
		$sql = "SELECT * FROM global.Q003 LEFT JOIN global.Q002 ON Q002_Id = Q003_Q002_Id WHERE Q003_Id = '{$r_Q003_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		if ($row['Q002_Servidor_Id'] == 'ERP') {
			$link = 'http://fixar.hardness.com.br/';
		} else {
			$link = 'http://erp.hardness.com.br/';
		}
		$db = urlencode($row['Q002_Database']);
		$T007_TX_Mensagem = urlencode($row['Q003_Mensagem']);
		$T007_Id = urlencode($row['Q002_T007_Id']);
		$link .= "hardness3/scripts/queueNF/servidorMsg.php?db={$db}&T007_TX_Mensagem={$T007_TX_Mensagem}&T007_Id={$T007_Id}";
		echo $link . "\n";
		file_get_contents($link);
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'visuXML':
		$resposta = array('code' => true, 'data' => array());
		
		$sql = "SELECT * FROM global.Q003 WHERE Q003_Id = '{$r_Q003_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		if (empty($row['Q003_XML'])) {
			$resposta['code'] = false;
		} else {
			$resposta['xml'] = $row['Q003_XML'];
			
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, 'https://www.sefaz.rs.gov.br/nfe/nfe-val.aspx');
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, 'txtxml=' . urlencode($row['Q003_XML']));
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$body = curl_exec($ch);
			curl_close($ch);
			preg_match_all('/<span id="resultado">(.*)<\/table>/isU', $body, $matches);
			$r = $matches[1][0];
			$r = str_replace('../Imagens/valid.png', '/hardness3/static/img/sefaz/valid.png', $r);
			$r = str_replace('../Imagens/ico_menos.gif', '/hardness3/static/img/sefaz/ico_menos.gif', $r);
			$r = str_replace('../Imagens/bullet_black.png', '/hardness3/static/img/sefaz/bullet_black.png', $r);
			$r = str_replace('../Imagens/erro.png', '/hardness3/static/img/sefaz/erro.png', $r);
			$resposta['table'] = $r . '</table>';
		}
				
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'tabelaGrid':
		mysql_query('USE global;');
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T136_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T136_Numero_Erro', 'colunaTitulo' => 'Nr. Erro Sefaz', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T136_Numero_Erro as Q002_Resposta_Extra', 'colunaTitulo' => '', 'colunaExibir' => ''),
			array('colunaValor' => 'T136_Descricao_Erro', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T136_Procedimento', 'colunaTitulo' => 'Procedimento', 'colunaEditar' => 'true'),
			array('colunaValor' => 'T136_Flag_Refazer_Chave_Acesso', 'colunaTitulo' => 'Refaz Chave de Acesso', 'colunaEditar' => 'true', 'colunaEditarGeraSelect' => array(array('title' => 'S', 'value' => 'S'), array('title' => 'N', 'value' => 'N'))),
			array('colunaValor' => 'MsgsSuporte', 'colunaTitulo' => 'Suporte (Histórico)', 'callback' => 'Q002_Msgs_Suporte', 'colunaTipo' => 'livre'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T136';
		$extra = <<<EOT
			ORDER BY T136_Numero_Erro
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T136_Numero_Erro' => array('titulo' => 'Nr. Erro Sefaz', 'tipo' => 'numero'),
			'T136_Descricao_Erro' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T136_Procedimento' => array('titulo' => 'Procedimento', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'errosCadGrid':
		mysql_query('USE global;');
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'DATE(Q002_DataHora) as Data', 'colunaTitulo' => 'Data', 'style' => 'width: 1%;', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Q002_Resposta_Extra', 'colunaTitulo' => 'Código Erro', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "IF((SELECT COUNT(*) FROM T136 WHERE T136_Numero_Erro = Q002_Resposta_Extra) > 0, '<span style=\"color: green;\">Sim</span>', '<span style=\"font-weight: bold; color: red;\">Não</span>') as CadSimNao", 'colunaTitulo' => 'Já foi Cadastrado?', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Q002_Retorno_Sefaz', 'colunaTitulo' => 'Retorno Sefaz'),
			array('colunaValor' => 'MsgsSuporte', 'colunaTitulo' => 'Suporte (Histórico)', 'callback' => 'Q002_Msgs_Suporte', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'Q002_Database', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_C004_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'Q002_T007_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'Q002';
		$extra = <<<EOT
WHERE		
Q002_Codigo_Resposta = 200
AND Q002_Resposta_Extra != ''
GROUP BY Q002_Resposta_Extra
ORDER BY Q002_Resposta_Extra ASC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'DATE(Q002_DataHora)' => array('titulo' => 'Data', 'tipo' => 'date')
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
}
?>
