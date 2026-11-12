<?php
namespace hardness;

switch($g['r'][3]){
	case 'content':
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
			array($content1, '/sistema/funcoes/svnPersonalizacoes/tab/', '')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'contentCommit':
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
			array($content1, '/sistema/funcoes/svnPersonalizacoes/tabCommit/', '')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'tab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/svnPersonalizacoes/grid/', 'Log', '', false, true),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabCommit':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/svnPersonalizacoes/gridCommit/', 'Arquivos', '&tab=geral'),
			array(uniqid(), '/sistema/funcoes/svnPersonalizacoes/formCommit/', 'Commit', '&tab=geral'),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'grid':
		// Impedir carregamento de mais linhas no scroll
		if (isset($r_loading)) {
			die();
		}
		$path = "{$g['pathRaiz']}{$g['pathDados']}";

		// Se não tiver nenhuma arquivos apagado para comitar, faz update
		$temRem = false;
		$output = '';
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoConf --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoForm --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoGrid --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoTab --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$output = trim($output);
		if (empty($output)) {
			$arquivos = array();
		} else {
			$arquivos = explode("\n", $output);
			foreach ($arquivos as $key => $value) {
				$tmp = preg_split('/ +/', $value);
				$tmp[] = true;
				$arquivos[$key] = $tmp;
			}
		}
		foreach ($arquivos as $key => $value) {
			$tipo = $value[0];
			if ($tipo == '!' || $tipo == 'D') { $temRem = true; }
		}


		// Sempre executados (cria o repositório se não existe)
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn info --username \"sistema\" --password \"hard3871\" --non-interactive";
		$log = shell_exec($cmd);
		if (!$temRem || strpos($log, "svn: '.'") !== false) {
			$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn mkdir http://usvn.hardness.com.br/svn/personalizacoes/branches/{$confUsuario['dbDatabase']} -m \"Repositorio das personalizacoes criado para o banco: {$confUsuario['dbDatabase']}\" --username \"sistema\" --password \"hard3871\" --non-interactive";
			shell_exec($cmd);
			$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn checkout http://usvn.hardness.com.br/svn/personalizacoes/branches/{$confUsuario['dbDatabase']} ./ --username \"sistema\" --password \"hard3871\" --non-interactive";
			shell_exec($cmd);
		}

		$grid = array(
			array('colunaValor' => 'Versao', 'colunaTitulo' => 'Revisão', 'style' => 'width: 1%; text-align: center;', 'chavePrimaria' => true),
			array('colunaValor' => 'Data', 'colunaTitulo' => 'Data', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Texto', 'colunaTitulo' => 'Texto'),
		);

		/**
		* Definição da SQL
		*/
		$from = "X000";
		$extra = <<<EOT
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="cad056Diff">Ver Alterações Pendentes</button>-->
				<button type="button" id="cad056Incluir">Commit</button>
				<!--<button type="button" id="cad056Update">Update</button>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad056Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Commit', '/sistema/funcoes/svnPersonalizacoes/contentCommit/','', [850,450]);
				});
				$('#cad056Diff').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Commit', '/sistema/funcoes/svnPersonalizacoes/verDiff/','', [910,510]);
				});
				$('#cad056Update').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Update', '/sistema/funcoes/svnPersonalizacoes/update/','', [600,400]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Texto' => array('titulo' => 'Texto', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);	
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/Versao-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Listar Alterações', '/sistema/funcoes/svnPersonalizacoes/outroLog/', '&revision=' + id, [900,500]);
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		$Detalhes = $g['ui']->getGridFiltroCampoBusca('Detalhes');
		$Texto = $g['ui']->getGridFiltroCampoBusca('Texto');
		$geradoSql = '';
		$geradoDados = array();

		// Lista
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn log -l 50 --username \"sistema\" --password \"hard3871\" --non-interactive";
		$log = shell_exec($cmd);
		$log = explode('------------------------------------------------------------------------', $log);

		foreach ($log as $key => $value) {
			$value = trim($value);
			if (empty($value)) { continue; }
			$value = explode("\n", $value);
			$detalhes = explode('|', $value[0]);
			$versao = preg_replace('/^r/', '', $detalhes[0]);
			$data = explode(' ', trim($detalhes[2]));
			$data = $data[0] . ' ' . $data[1];
			$data = gCorrigeData($data);
			$geradoDados[] = array(
				$versao,
				$data,
				$value[2]
			);
		}
		//list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'gridCommit':
		// Impedir carregamento de mais linhas no scroll
		if (isset($r_loading)) {
			die();
		}

		// Sempre executados (cria o repositório se não existe)
		$path = "{$g['pathRaiz']}{$g['pathDados']}";
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn info --username \"sistema\" --password \"hard3871\" --non-interactive";
		$log = shell_exec($cmd);
		if (strpos($log, "svn: '.'") !== false) {
			$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn mkdir http://usvn.hardness.com.br/svn/personalizacoes/branches/{$confUsuario['dbDatabase']} -m \"Repositorio das personalizacoes criado para o banco: {$confUsuario['dbDatabase']}\" --username \"sistema\" --password \"hard3871\" --non-interactive";
			shell_exec($cmd);
			$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn checkout http://usvn.hardness.com.br/svn/personalizacoes/branches/{$confUsuario['dbDatabase']} ./ --username \"sistema\" --password \"hard3871\" --non-interactive";
			shell_exec($cmd);
		}

		$output = '';
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoConf --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoForm --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoGrid --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn status personalizacaoTab --username \"sistema\" --password \"hard3871\" --non-interactive";
		$output .= shell_exec($cmd);
		$output = trim($output);

		if (empty($output)) {
			$arquivos = array();
		} else {
			$arquivos = explode("\n", $output);
			foreach ($arquivos as $key => $value) {
				$tmp = preg_split('/ +/', $value);
				$tmp[] = false;
				$arquivos[$key] = $tmp;
			}
		}
		// o arquivo é guardado toda vez que o grid for atualizado
		file_put_contents('/tmp/' . session_id(), serialize($arquivos));

		$grid = array(
			array('colunaValor' => 'Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'Acao', 'colunaTitulo' => '', 'style' => 'width: 1%; text-align: center;'),
			array('colunaValor' => 'Status', 'colunaTitulo' => 'Status', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'Data', 'colunaTitulo' => 'Data Última Alteração', 'colunaAlinhar' => 'center'),
		);

		/**
		* Definição da SQL
		*/
		$from = "X000";
		$extra = <<<EOT
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Texto' => array('titulo' => 'Texto', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Diff', '/sistema/funcoes/svnPersonalizacoes/verDiff/','&arquivo=' + encodeURIComponent(id), [910,510]);
				});

				// Desabilitar o refresh automatico deste grid
				$('#{$g['divId']} .grid').data('lastTimeUpdate', 'never');

				function checkboxArquivo(elem) {
					$.ajax({
						url: '/sistema/funcoes/svnPersonalizacoes/checkboxArquivo/',
						dataType: 'html',
						data: 'ajax=true&arquivo=' + encodeURIComponent($(elem).closest('tr').attr('id').replace(/Id-/, '')) + '&checked=' + $(elem).prop('checked'),
						type: 'get',
						success: function(data) {
							//
						}
					});
				}
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		$Detalhes = $g['ui']->getGridFiltroCampoBusca('Detalhes');
		$Texto = $g['ui']->getGridFiltroCampoBusca('Texto');
		$geradoSql = '';
		$geradoDados = array();

		// List
		foreach ($arquivos as $key => $value) {
			$tipo = $value[0];
			$tipo = ($tipo == '?' || $tipo == 'A') ? '<span style="color: blue;">Novo</span>' : $tipo;
			$tipo = ($tipo == 'M') ? '<span style="color: green;">Modificado</span>' : $tipo;
			$tipo = ($tipo == '!' || $tipo == 'D') ? '<span style="color: red;">Removido</span>' : $tipo;
			$geradoDados[] = array(
				$key,
				'<input type="checkbox" onclick="checkboxArquivo(this); event.stopPropagation();" checked />',
				$tipo,
				$value[1],
				($tipo == '<span style="color: red;">Removido</span>') ? '-' : date("d/m/Y H:i:s", filemtime($g['pathDados'] . $value[1]))
			);
		}

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'formCommit':
		// Sempre executados (add de arquivos novos)
		$path = "{$g['pathRaiz']}{$g['pathDados']}";
		/*
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoConf --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoForm --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoGrid --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoTab --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		*/

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'svnCommit';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/svnPersonalizacoes/formCommit/';

		// novo formulário
		$form = new Formulario('svnCommitForm');
		// Editor
		$form_editor = array(
			'geral' => '{"Usuario":{"top":7,"left":21,"width":120,"hidden":0},"Descricao":{"top":30,"left":9,"width":300,"hidden":0},"svnCommit":{"top":74,"left":160,"width":null,"hidden":0}}',
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Usuario' => array('name' => 'Usuario', 'label'=>'Usuario', 'type' => 'text', 'reject' => true),
					'Descricao' => array('name' => 'Descricao', 'label'=>'Descrição', 'type' => 'text', 'reject' => true, 'uppercase' => false)
				)
			)
		);
		$formArray['campos']['geral']['svnCommit'] = array('name' => 'svnCommit', 'type' => 'submit', 'reload' => false, 'reject' => true, 'value' => 'Commit');
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$Usuario = trim($form->campoValorEnviado('Usuario'));
				$Usuario = strtoupper($Usuario);
				$Descricao = $form->campoValorEnviado('Descricao');
				$Descricao = str_replace('"', '\'', $Descricao);
				$Descricao = iconv( 'UTF-8', 'ASCII//TRANSLIT', $Descricao);
				if (empty($Usuario)) {
					echo gGeraAlertMsg('Erro', 'Campo Usuário é Obrigatório!');
				} else if (empty($Descricao)) {
					echo gGeraAlertMsg('Erro', 'Campo Descrição é Obrigatório!');
				} else {
					$arquivos = unserialize(file_get_contents('/tmp/' . session_id()));
					$arquivosTxt = '';
					foreach ($arquivos as $key => $value) {
						if (!$value[2]) { continue; }
						if ($value[0] == '!' || $value[0] == 'D') {
							$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn del {$value[1]} --force --username \"sistema\" --password \"hard3871\" --non-interactive";
						} else {
							$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add {$value[1]} --force --username \"sistema\" --password \"hard3871\" --non-interactive";
						}
						shell_exec($cmd);
						$arquivosTxt .= ' ' . $value[1];
					}
					$arquivosTxt = trim($arquivosTxt);

					$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn commit -m \"({$Usuario}) {$Descricao}\" --username \"sistema\" --password \"hard3871\" --non-interactive {$arquivosTxt}";
					$log = shell_exec($cmd);
					$log = str_replace("\n", "<br/>", $log);
					$log = str_replace(" ", "&nbsp;", $log);
					$log = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $log);
					//$log .= "\n {$cmd}";
					echo gGeraAlertMsg('Retorno do Commit', "<div style=\"width: 900px; height: 500px;\">{$log}</div>");
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									//divRefresh('{$g['divId']}');
								}, 150);
								});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'outroLog': 
		$path = "{$g['pathRaiz']}{$g['pathDados']}";
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn log -r {$r_revision} --username \"sistema\" --password \"hard3871\" --non-interactive -v";
		$log = shell_exec($cmd);
		$log = str_replace("\n", '</br>', $log);
		$log = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $log);
		$log = str_replace(" ", '&nbsp;', $log);
		echo <<<EOT
<div>{$log}</div>
EOT;
	break;

	case 'verDiff':
		$arquivos = unserialize(file_get_contents('/tmp/' . session_id()));
		$arquivo = $arquivos[$r_arquivo][1];
		$path = "{$g['pathRaiz']}{$g['pathDados']}";
		/*
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoConf --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoForm --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoGrid --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn add personalizacaoTab --force --username \"sistema\" --password \"hard3871\" --non-interactive";
		shell_exec($cmd);
		*/
		if ($arquivos[$r_arquivo][0] == 'M') {
			$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn diff {$arquivo} --username \"sistema\" --password \"hard3871\" --non-interactive";
			$log = shell_exec($cmd);
		} else if ($arquivos[$r_arquivo][0] == '!' || $arquivos[$r_arquivo][0] == 'D') {
			$log = '<b>Arquivo foi removido, será atualizado este status no SVN!</b>';
		} else {
			$log = file_get_contents($g['pathDados'] . $arquivo);
		}
		$log = str_replace("\n", "<br/>", $log);
		$log = str_replace(" ", "&nbsp;", $log);
		$log = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $log);
		if (empty($log)) {
			$log = '<b>Sem alterações pendentes para fazer o commit!</b>';
		}
		$log = str_replace('===================================================================','<div style="width: 99%; height: 1px; background-color: black;"></div>',$log);
		$log = str_replace('Index:','<div class="index" style="width: 99%; height: 1px; background-color: black;"></div>',$log);
		$log = str_replace('<br/><div class="index" style="width: 99%; height: 1px; background-color: black;"></div>','<br/><br/><div style="width: 99%; height: 1px; background-color: black;"></div>',$log);
		echo "<div style='padding: 5px;'>{$log}</div>";
	break;

	case 'checkboxArquivo':
		$arquivos = unserialize(file_get_contents('/tmp/' . session_id()));
		$arquivos[$r_arquivo][2] = ($r_checked == 'true') ? true : false;
		file_put_contents('/tmp/' . session_id(), serialize($arquivos));
	break;

	case 'update':
		$path = "{$g['pathRaiz']}{$g['pathDados']}";
		$cmd = "cd {$path} ; /usr/bin/sudo -u root /usr/bin/svn up --username \"sistema\" --password \"hard3871\" --non-interactive";
		$log = shell_exec($cmd);
		$log = str_replace("\n", "<br/>", $log);
		$log = str_replace(" ", "&nbsp;", $log);
		$log = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $log);
		if (empty($log)) {
			$log = '<b>Nenhum arquivo atualizado!</b>';
		}
		echo "<div style='padding: 5px;'>{$log}</div>";
	break;
}