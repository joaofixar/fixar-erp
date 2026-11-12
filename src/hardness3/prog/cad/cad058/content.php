<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad058ContentConfGlobal':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$r_acaoId = $g['empresaAtual'];
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad058/tab/cad058TabConfGlobal/', '&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad058ContentConfGeral':
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
			array($content1, '/cad/cad058/form/cad058FormConfGeral/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfGeral_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfCadastros':
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
			array($content1, '/cad/cad058/tab/cad058TabConfCadastros/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="button" id="cad058FormConfCadastros_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad058ContentConfVendas':
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
			array($content1, '/cad/cad058/tab/cad058TabConfVendas/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="button" id="cad058FormConfVendas_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad058ContentConfEstoque':
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
			array($content1, '/cad/cad058/tab/cad058TabConfEstoque/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="button" id="cad058FormConfEstoque_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad058ContentConfFinanceiro':
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
			array($content1, '/cad/cad058/tab/cad058TabConfFinanceiro/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="button" id="cad058FormConfFinanceiro_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'xcad058ContentConfImportacao':
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
			array($content1, '/cad/cad058/form/cad058FormConfImportacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfImportacao_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfMobile':
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
			array($content1, '/cad/cad058/form/cad058FormConfMobile/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfMobile_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfECommerce':
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
			array($content1, '/cad/cad058/form/cad058FormConfECommerce/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfECommerce_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfECommerceMagento1Oauth':
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
			array($content1, '/cad/cad058/form/cad058FormConfECommerceMagento1Oauth/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfECommerceMagento1Oauth_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfECommerceMagento1Soap':
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
			array($content1, '/cad/cad058/form/cad058FormConfECommerceMagento1Soap/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfECommerceMagento1Soap_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfTrello':
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
			array($content1, '/cad/cad058/form/cad058FormConfTrello/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfTrello_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfECommerceTray':
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
			array($content1, '/cad/cad058/form/cad058ContentConfECommerceTray/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058ContentConfECommerceTray_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfMeli':
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
			array($content1, '/cad/cad058/form/cad058FormConfMeli/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfMeli_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'openIA':
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
			array($content1, '/cad/cad058/form/cad058FormConfOpenIA/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfOpenIA_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;


	case 'cad058ContentConfECommerceNuvemShop':
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
			array($content1, '/cad/cad058/form/cad058FormConfNuvemShop/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfNuvemShop_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfMagazord':
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
			array($content1, '/cad/cad058/form/cad058ContentConfMagazord/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058ContentConfMagazord_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfECommercePluggto':
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
			array($content1, '/cad/cad058/form/cad058ContentConfECommercePluggto/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058ContentConfECommercePluggto_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfCyberlog':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');

		$actions = array(
			array($content1, '/cad/cad058/form/cad058FormConfCyberlog/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);

		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfCyberlog_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';

		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq ? $botoesEsq : '');
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfDuimp':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');

		$actions = array(
			array($content1, '/cad/cad058/form/cad058FormConfDuimp/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);

		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfDuimp_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';

		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq ? $botoesEsq : '');
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfB2B':
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
			array($content1, '/cad/cad058/form/cad058FormConfB2B/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfB2B_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfTablet':
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
			array($content1, '/cad/cad058/form/cad058FormConfTablet/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfTablet_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfIntelipost':
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
			array($content1, '/cad/cad058/form/cad058FormConfIntelipost/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="submit" id="cad058FormConfIntelipost_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfMDFe':
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
			array($content1, '/cad/cad058/tab/cad058TabConfMDfeGeral/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="button" id="cad058FormConfMDFe_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentGridConfiguracoes':
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
			array($content1, '/cad/cad058/tab/cad058TabGridConfiguracoes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad058ContentConfAlcadas':
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
			array($content1, '/cad/cad058/tab/cad058TabConfAlcadas/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad058ContentConfCadastrosProdutos':
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
			array($content1, '/cad/cad058/tab/cad058TabConfCadastrosProdutos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad058ContentConfInterface':
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
			array($content1, '/cad/cad058/form/cad058FormConfInterface/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad058FormConfInterface_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfSPEDTab':
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
			array($content1, '/cad/cad058/form/cad058ContentConfSPEDForm/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad058ContentConfSPEDForm_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfJobsTab':
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
			array($content1, '/cad/cad058/form/cad058ContentConfJobsForm/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad058ContentConfJobsForm_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfNotaFiscal':
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
			array($content1, '/cad/cad058/tab/cad058TabConfNotaFiscal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad058ContentConfImportacao':
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
			array($content1, '/cad/cad058/tab/cad058TabConfImportacao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($g['usuarioAtual'] == 63) {
			$botoesEsq .= "<button type='button' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Configurações globais', '/cad/cad058/content/cad058ContentGridConfiguracoes/', '', 'auto', false, 'center');\">Grid</button>";
		}
		$botoesDir .= '<button type="button" id="cad058FormConfImportacaoDi_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad058ContentConfImportacaoDi':
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
			array($content1, '/cad/cad058/tab/cad058TabConfImportacaoDi/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad058ContentConfECommerceTab':
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
			array($content1, '/cad/cad058/tab/cad058ContentConfECommerceTab/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
