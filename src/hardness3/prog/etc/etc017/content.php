<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc017ContentTarefas':
		global $g;
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
			array($content1, '/etc/etc017/tab/etc017TabTarefas/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentTarefasCliente':
		global $g;
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
			array($content1, '/etc/etc017/tab/etc017TabTarefasCliente/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentTarefa':
		$sql = mysql_query("SELECT D085_Id, D085_Data_Previsao_Retorno, D085_Hora_Previsao_Retorno FROM D085 WHERE D085_T259_Id = '{$r_acaoId}' AND D085_Data_Previsao_Retorno != '0000-00-00' ORDER BY D085_Data_Previsao_Retorno DESC, D085_Hora_Previsao_Retorno DESC LIMIT 1");
		if(mysql_num_rows($sql) > 0){
			$row = mysql_fetch_assoc($sql);
			if(strtotime($row['D085_Data_Previsao_Retorno'].' '.$row['D085_Hora_Previsao_Retorno']) < strtotime(date("Y-m-d H:i:s"))){
				$sqlD085 = mysql_query("SELECT D085_Id FROM D085 WHERE D085_T259_Id = '{$r_acaoId}'");
				while($rowD085 = mysql_fetch_assoc($sqlD085)){
					mysql_query("UPDATE D085 SET D085_Flag_Lido = 'S' WHERE D085_Id = '{$rowD085['D085_Id']}'");
				}
			}
		}

		if(empty($r_notificacao)){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$g['divId']}").closest('.ui-dialog-content').bind("dialogclose", function(data, ui) {
						$.getJSON('/etc/etc017/grid_func-ajax/atualizarAssuntoTarefa/?ajax=true&T259_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {});
						// divRefresh('{$g['divIdParent']}', true, false, false, '{$r_acaoId}', 'T259_Id'); // grid
					});
				} </script></div>
EOT;
		}
		global $g;
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
			array($content1, '/etc/etc017/tab/etc017TabTarefa/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentAtividades':
		global $g;
		
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$T182_Id = isset($r_T182_Id) ? $r_T182_Id : false;

		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '350px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc017/grid/etc017GridTarefa/', '&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($T003_Id) . '&T005_Id=' . urlencode($T005_Id) . '&T182_Id=' . urlencode($T182_Id) .'&divForm=' . urlencode($content2)),
			array($content2, '/etc/etc017/form/etc017FormInserirAgendaTarefa/', '&tab=geral&T259_Id=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&T003_Id=' . urlencode($T003_Id) . '&T005_Id=' . urlencode($T005_Id) . '&T182_Id=' . urlencode($T182_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentAtividadesT003':
		global $g;
		
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T259_Id = mysql_query("SELECT D085_T259_Id From D085 Where D085_T003_Id={$T003_Id}");
		$T259_Id = mysql_fetch_array($T259_Id);
		$r_acaoId = $T259_Id[0];
		
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '350px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc017/grid/etc017GridTarefa/', '&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($T003_Id) .'&divForm=' . urlencode($content2)),
			array($content2, '/etc/etc017/form/etc017FormInserirAgendaTarefaT003/', '&tab=geral&T259_Id=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&T003_Id=' . urlencode($T003_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentAtividadesT005':
		global $g;
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		$T259_Id = mysql_query("SELECT D085_T259_Id From D085 Where D085_T005_Id={$T005_Id}");
		$T259_Id = mysql_fetch_array($T259_Id);
		$r_acaoId = $T259_Id[0];
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '350px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc017/grid/etc017GridTarefa/', '&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($T005_Id) .'&divForm=' . urlencode($content2)),
			array($content2, '/etc/etc017/form/etc017FormInserirAgendaTarefaT005/', '&tab=geral&T259_Id=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&T005_Id=' . urlencode($T005_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentAtividadesT007':
		global $g;
		
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

		$T259_Id = mysql_query("SELECT D085_T259_Id From D085 Where D085_T007_Id={$T007_Id}");
		$T259_Id = mysql_fetch_array($T259_Id);
		$r_acaoId = $T259_Id[0];
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '350px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc017/grid/etc017GridTarefa/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($T007_Id) .'&divForm=' . urlencode($content2)),
			array($content2, '/etc/etc017/form/etc017FormInserirAgendaTarefaT007/', '&tab=geral&T259_Id=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&T007_Id=' . urlencode($T007_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentAtividadesT182':
		global $g;
		
		$T182_Id = isset($r_T182_Id) ? $r_T182_Id : false;

		$T259_Id = mysql_query("SELECT D085_T259_Id From D085 Where D085_T182_Id={$T182_Id}");
		$T259_Id = mysql_fetch_array($T259_Id);
		$r_acaoId = $T259_Id[0];

		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '350px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc017/grid/etc017GridTarefa/', '&acaoId=' . urlencode($r_acaoId) . '&T182_Id=' . urlencode($T182_Id) .'&divForm=' . urlencode($content2)),
			array($content2, '/etc/etc017/form/etc017FormInserirAgendaTarefaT182/', '&tab=geral&T259_Id=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&T182_Id=' . urlencode($T182_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentOrcamentoAgenda':
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '240px');
		$g['smarty']->display('content-2rowsInverso.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content2, '/etc/etc017/tab/etc017TabLeituraAgenda/', "&acaoId=" . urlencode($r_acaoId)),
			array($content1, '/etc/etc017/content/etc017ContentOrcamento/', ""),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc017ContentOrcamento':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Width', '300px');
		$g['smarty']->display('content-2columnsOrcamento.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven001/form/ven001FormProdutos/', '&tab=produto&T003_Id=' . urlencode($r_T003_Id) . '&divReload=' . urlencode($r_divReload) . "&divOutro=" . urldecode($content2)),
			array($content2, '/ven/ven001/tab/ven001TabProdutosSimples/', '&acaoId=' . urlencode($r_T003_Id)),
		);
		echo gProcessaAcoes($actions);

		// Botões
		$botoesEsq = "<button id='etc017AbrirOrcamento'>Orçamento {$r_T003_Id}</button>";
		$botoesMid = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc017AbrirOrcamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$r_T003_Id}') + '&tabela=T003', [1000,600]);
				});
			} </script></div>
EOT;
	break;
}