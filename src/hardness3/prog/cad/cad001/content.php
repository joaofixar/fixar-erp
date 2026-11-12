<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad001ContentPrincipal':
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
		array($content1, '/cad/cad001/tab/cad001tabPrincipal/')
		);
		echo gProcessaAcoes($actions);

		break;
		
	case 'cad001contentCadastro':
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
			array($content1, '/cad/cad001/tab/cad001tabCadastro/', '&acaoId=' . urlencode($r_acaoId) . '&tab=' . @urlencode($r_tab)),
		);
		echo gProcessaAcoes($actions, false);
		
		$sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$r_acaoId}'");
		$rowD024 = mysql_fetch_array($sqlD024);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if($g['nivelAlcada'] == '4' || $rowD024['D024_C007_Vendedor_Interno'] == $g['usuarioAtual']){
			$botoesEsq .= '<button type="submit" id="cad001ExtratoCliente_btn">Extrato</button>';
		}
		$botoesDir .= '<button type="submit" id="cad001FormCadastro_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		echo <<<EOT
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            $('#cad001ExtratoCliente_btn').unbind('click').bind('click', function(data) {
	                abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Extrato Cliente', '/rel/relatorios/extratoCliente/contentRelatorio/', '&acaoId={$r_acaoId}&D024_Id={$r_acaoId}&Empresa={$g['empresaAtual']}', [1000,800]);
	            });
	        } </script></div>
EOT;
	break;

	case 'cad001contentClienteCRMVendedor':
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
			array($content1, '/cad/cad001/tab/cad001tabClienteCRMVendedor/', '&acaoId=' . urlencode($r_acaoId) . '&tab=' . @urlencode($r_tab)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormCadastro_btn_CRM_Vendedor">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case'cad001contentVeiculos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		// $content2 = uniqid();
		// $g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', 'unico');	
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
			//array($content1, '/cad/cad001/form/cad001FormVeiculos/', '&tab=geral&D024_Id=' . urlencode($r_D024_Id) . '&divReload=' . urlencode($content2)),
			array($content1, '/cad/cad001/grid/cad001GridVeiculos/', '&tab=geral&acaoId=' . urlencode($r_D024_Id) . '&D024_Id=' . urlencode($r_D024_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

	break;
	
	case 'cad001ContentVeiculosIncluir':
		/*
		* Divisão da Area em Conteúdos
		*/
		$r_naoRefreshParent = (isset($r_naoRefreshParent)) ? $r_naoRefreshParent : '';
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad001/tab/cad045TabCadastroVeiculos/', '&acaoId=' . urlencode($r_acaoId). '&naoRefreshParent=' . urlencode($r_naoRefreshParent). '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case'cad001contentContatos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '165px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');	
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad001/form/cad001FormContatos/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id)  . '&divReload=' . urlencode($content2)),
			array($content2, '/cad/cad001/grid/cad001GridContatos/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id)  . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

	break;
	
	case 'cad001ContentAbreAgenda':
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
		array($content1, '/cad/cad001/tab/cad001TabAgenda/', '&D024_Id=' . urlencode($r_D024_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
		
	case 'cad001ContentAgenda':
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
			array($content1, '/cad/cad001/tab/cad001TabAgenda/', "&acaoId=" . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = '';
		if($r_acaoId > 0){
			$botoesEsq = "<button onclick=\"fecharJanela('".$g['divId']."'); abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/cad/cad001/content/cad001ContentAgenda/', '&tab=geral&D024_Id={$r_D024_Id}', [700,450]);\" class=\"btnFechar\">Incluir Agenda</button>";
		} else {
			$botoesEsq = '';
		}
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormAgenda_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
		
	case 'cad001ContentAbreDocumentos':
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
		array($content1, '/cad/cad001/tab/cad001TabDocumentos/', '&D024_Id=' . urlencode($r_D024_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
		
	case 'cad001ContentDocumentos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
    	$g['smarty']->assign('content1Height', '165px');
		$g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '160px;');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/cad/cad001/form/cad001FormDocumentos/', "&tab=geral&D024_Id=" . urlencode($r_D024_Id) .  "&divReload=" . urlencode($content2)),
		array($content2, '/cad/cad001/grid/cad001GridDocumentos/', '&acaoId=' . urlencode($r_D024_Id) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'cad001ContentDocumentosVeiculos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '100px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/cad/cad001/form/cad001FormDocumentosVeiculos/', "&tab=geral&D116_Id=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),
		array($content2, '/cad/cad001/grid/cad001GridDocumentosVeiculos/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'cad001contentEmail':
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
		array($content1, '/etc/etc006/grid/etc006GridTodos/', '&D024_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'cad001ContentReferencias':
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
			array($content1, '/cad/cad001/form/cad001FormReferencias/', '&tab=geral&D024_Id='. urlencode($r_D024_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'cad001ContentPreCadastrarCliente':
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
			array($content1, '/cad/cad001/tab/cad001TabPreCadastrarCliente/', '&acaoId='. urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormPreCadastrarCliente_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cad001ContentAlterarContato':
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
			array($content1, '/cad/cad001/tab/cad001TabAlterarContato/', '&acaoId='. urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '';
		$botoesDir .= '';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;


	case 'cad001ContentImprimirClientes':
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
		array($content1, '/cad/cad001/tab/cad001TabEtiquetasClientes/')
		);
		echo gProcessaAcoes($actions);
	break;
        
	case 'cad001ContentImprimirEtiquetas':
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('content1Height', '60px');
        $content2 = uniqid();
        $g['smarty']->assign('content2', $content2);
        echo  $g['smarty']->fetch('content-2rows.tpl');        
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
        	array($content1, '/cad/cad051/form/cad051SelecionarModelo/', '&tab=geral&D024_Id=true'),
        	array($content2, '/cad/cad001/grid/cad001GridProdutosImprimir/'),
        	);
        echo  gProcessaAcoes($actions);
        
        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
        $novoIdTelaForm = uniqid();
    break;

    case 'cad001ContentJuntar':
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
		array($content1, '/cad/cad001/tab/cad001TabJuntar/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad001OrcamentoIncluirCliente':
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
		array($content1, '/cad/cad001/tab/cad001OrcamentoIncluirCliente/')
		);
		echo gProcessaAcoes($actions, false);

	break;

	case 'cad001PedidoIncluirCliente':
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
		array($content1, '/cad/cad001/tab/cad001PedidoIncluirCliente/')
		);
		echo gProcessaAcoes($actions, false);

	break;

	case 'cad001contentCadastroEndereco':
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
			array($content1, '/cad/cad001/tab/cad001tabCadastroEndereco/', '&acaoId=' . urlencode($r_acaoId) . '&tab=' . @urlencode($r_tab)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormCadastroEndereco_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cad001CotacaoIncluirFornecedor':
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
		array($content1, '/cad/cad001/tab/cad001CotacaoIncluirFornecedor/')
		);
		echo gProcessaAcoes($actions);

	break;

	case 'cad001ImportarCSV':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '40px;');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);        
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
           	array($content1, '/cad/cad001/form/cad001FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/cad/cad001/outro/cad001ManualCSVCliente/','&tab=geral')
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;


}
