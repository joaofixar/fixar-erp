<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'grid1':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabGrid/')
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'form1':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)

		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');	
			
		// O que será carregado em cada conteúdo?
		
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabForm1/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);

	break;
	
	case 'formXML':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabFormXML/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;	
	
	case 'form2':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';	
		
		$actions = array();
		$actions[] = array($content1, '/cad/cad002/tab/tabForm2/', '&acaoId=' . urlencode($r_acaoId). '&D001_Id=' . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id) . '&divIdMain=' . urlencode($g['divId']));
		echo gProcessaAcoes($actions, false);
		
	break;
		
	case 'detalhes':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		//$content2 = uniqid();
		//$g['smarty']->assign('content2', $content2);
		//$g['smarty']->assign('content1Height', '160px');

		
		/**
		* Código criado para adicionar manualmente a borda no grid comercial abaixo
		
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var divId = $('#{$content1}');
				var divIdWidth = divId.width()-4;
				divId.addClass('ui-tabs ui-corner-all');
				divId.width(divIdWidth);
				divId.find('.divToScroll').width(divIdWidth);
			} </script></div>
EOT;
		*/
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabGridComercial/', '&acaoId=' . urlencode($r_acaoId)),
			//array($content2, '/cad/cad002/tab/tabForm4/', '&D001_Id=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		
		echo gProcessaAcoes($actions);
		
		
	break;
	
	case 'cad002ContentEcommerce':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabEcommerce/', '&acaoId=' . urlencode($_REQUEST['acaoId']) . "&D049_Id=" . urlencode($r_D049_Id) .  "&divReload=" . urlencode($r_divReload))
		);
		echo  gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormEcommerce_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
		
	case 'certificados':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '80px;');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/certificados/', '&tab=geral&D001_Id=' . urlencode($_REQUEST['acaoId']) . '&idTabForm4=' . urlencode($content2). '&D024_Id=' . urlencode($r_D024_Id)),
            array($content2, '/cad/cad002/grid/certificados/', '&tab=geral&acaoId=' . urlencode($_REQUEST['acaoId']) . '&idTabForm4=' . urlencode($content1). '&D024_Id=' . urlencode($r_D024_Id))
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
	
	case 'cadastroMarca':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabForm5CadastroMarca/', '&tab=geral&acaoId=' . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($r_divReload))
		);
		echo  gProcessaAcoes($actions);
		
		
	break;
	
	case 'cad002ContentCadastroEmpresas':
		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabCadastroEmpresa/', '&tab=geral&D001_Id=' . urlencode($D001_Id) . '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
		
		
	break;

	case 'cad002ContentCadastroLocacao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabCadastroLocacao/', '&tab=geral&D009_Id=' . urlencode($r_D009_Id))
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002ContentCadastroLocacaoEditar':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabCadastroLocacaoEditar/', '&tab=geral&D009_Id=' . urlencode($r_D009_Id) . '&acaoId='.urlencode($acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002ContentMontagem':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$content3 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '155px');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormObservacaoMontagem/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id)),
			array($content2, '/cad/cad002/content/cad002ContentMontagemProdutos/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);

		$g['smarty']->display('buttons-floatBottom.tpl');		
	break;

	case 'cad002ContentMontagemProdutos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '30px');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormMontagem/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($content2)),
			array($content2, '/cad/cad002/grid/montagem/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002contentEditarHistorico':
		$r_D009_Id=isset($r_D009_Id) ? $r_D009_Id : false;
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
			array($content1, '/cad/cad002/tab/cad002TabEidtarHistorico/', '&acaoId=' . urlencode($r_acaoId) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormEditarHistorico_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case 'cad002ContentUploadFotoProduto':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '75px');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormUploadFotoProduto/', '&tab=geral&D001_Id=' . urlencode($r_D001_Id) . "&divReload=" . urlencode($content2)),
			array($content2, '/cad/cad002/grid/cad002GridFotoProduto/', '&acaoId=' . urlencode($r_D001_Id) . "&divReload=" . urlencode($content1)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002ContentServicos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '70px');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormServicos/', '&tab=geral&D001_Id=' . urlencode($r_D001_Id) . "&divReload=" . urlencode($content2)),
			array($content2, '/cad/cad002/grid/cad002GridServicos/', '&acaoId=' . urlencode($r_D001_Id) . "&divReload=" . urlencode($content1)),
		);
		echo  gProcessaAcoes($actions);
	break;	
	
	case 'cad002ContentPreCadastro':
		$T189_Descricao_Produto = (isset($r_T189_Descricao_Produto)) ? $r_T189_Descricao_Produto : false; 
		$r_descricaoProduto = isset($r_descricaoProduto) ? base64_decode($r_descricaoProduto) : $r_descricaoProduto;
		
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
			array($content1, '/cad/cad002/tab/cad002TabPreCadastro/', '&acaoId=' . urlencode($r_acaoId) . '&T189_Descricao_Produto='. urlencode($T189_Descricao_Produto)),
		);
		echo gProcessaAcoes($actions);
		
		/*
		* Botões
		
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="" ">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		*/
	break;
	
	case 'cad002ContentJuntarProdutos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabJuntarProdutos/','')
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002ReferenciasProdutos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/grid/referenciaProduto/','')
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentTrocarCodigos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabTrocarCodigos/','')
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002ContentCabecalhoProduto':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);


        $g['smarty']->assign('content1Height', '225px');
        //$g['smarty']->assign('content1Height', '235px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/form2/', "&tab=geral&modoFormLeve=true&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain)),
			array($content2, '/cad/cad002/content/detalhes/', "&acaoId=" . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		//$botoesDir .= '<button id="cad002AtualizarPreco">Atualizar Preços</button>';
		$botoesDir .= '<button id="cad002AtualizarEstoque">Atualizar Estoque</button>';
		$botoesDir .= '<button id="cad002JuntarMarcas">Juntar Sub-itens</button>';
		$botoesDir .= '<button id="cad002CadastrarMarca">Incluir Sub-item</button>';
		if($g['C031']['utilizaEcommerce'] == 'S'){
			$botoesDir .= '<button id="cad002Ecommerce">E-Commerce</button>';
		}
		$botoesEsq .= '<button type="submit" id="content_form2_btnEditarProduto">Enviar</button>';
		$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<script type="text/javascript">
		//Botão atualizar preço
		$('#cad002AtualizarPreco').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarPreco/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do preço?', buttons);
		});
		
		//Botão atualizar Estoque
		$('#cad002AtualizarEstoque').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoque/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		});
		
		//Juntar Marcas Cad002
		$('#cad002JuntarMarcas').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Juntar Sub-itens', '/cad/cad002/form/juntarMarcas/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}'), [428,175]);
		});	
		
		//Cadstrar Marcas
		$('#cad002CadastrarMarca').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Sub-item', '/cad/cad002/content/cadastroMarca/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&divReload=" + encodeURIComponent('{$content1}'), [500,430]);
		});
		
		//Formulário Ecommerce
		$('#cad002Ecommerce').unbind('click').bind('click', function(data) {
			//if(typeof($('#cad002Ecommerce').data('D049_Id')) == 'undefined'){
			    //dialogConfirm('Selecionar Sub-item', 'Primeiro Selecione o Sub-item');
			//}else{
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'E-Commerce', '/cad/cad002/content/cad002ContentEcommerce/', '&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&D049_Id=" + encodeURIComponent($('#cad002Ecommerce').data('D049_Id')) + "&divReload=" + encodeURIComponent('{$content1}'), [550,550]);
			//}
		});	
		</script>
EOT;
	

	break;
	
	case 'cad002ContentEditarMarca':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabEditarMarca/','&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id) . '&D009_Id=' . urlencode($r_D009_Id))
		);
		echo  gProcessaAcoes($actions);
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="" ">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
	break;
	
	case 'cad002ContentCabecalhoProdutoComum':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);


		//$g['smarty']->assign('content1Width', '450px');
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/form2Comum/', "&tab=geral&modoFormLeve=true&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain)),
			//array($content2, '/cad/cad002/tab/tabForm4/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		//$botoesDir .= '<button id="cad002AtualizarPreco">Atualizar Preços</button>';
		if(!empty($r_acaoId)){
			$botoesDir .= "<button id=\"cad002AtualizarEstoque_{$content1}\">Atualizar Estoque</button>";
			$botoesDir .= "<button id=\"cad002AtualizarEstoqueRapido_{$content1}\">Atualizar Estoque Rápido</button>";
		}
		$sql = mysql_query("SELECT * FROM D049 LEFT JOIN D001 ON D001_Id = D049_D001_Id WHERE D001_Id = '{$r_acaoId}'");
		$res = mysql_fetch_array($sql);	
		$multimarcas = $g['C031']['multimarcas'];
		if($multimarcas != 'N' && $res['D001_Flag_Multimarcas'] == 'S') {
			$botoesDir .= '<button id="cad002JuntarMarcas">Juntar Marcas</button>';
			$botoesDir .= '<button id="cad002CadastrarMarca">Incluir Marca</button>';
		}
		if($g['C031']['utilizaEcommerce'] == 'S'){
			//$botoesDir .= '<button id="cad002Ecommerce">E-Commerce</button>';
		}
		if($res['D049_Flag_Tipo'] == 'PD'){
			$engenharia = uniqid();
			$botoesDir .= "<button type='button' id='{$engenharia}'>Engenharia</button>";
		}
		$botoesEsq .= "<button type=\"submit\" id=\"content_form2_btnEditarProduto_{$content1}\">Enviar</button>";
		$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		
		if(!empty($r_acaoId)){
			// Próximo Código
			require_once("bibliotecas/classes/CAD002.php");
			$CAD002 = new CAD002();
			$proxCodigo = $CAD002->obterNovoCodigoProduto($r_acaoId);
			$proxCodigoProd = $CAD002->obterNovoCodigoProduto($r_acaoId, true);
			if ($proxCodigo) {
				$extra = '';
				if ($proxCodigoProd) {
					$extra = "&nbsp;&nbsp;&nbsp;&nbsp;Para este Produto: <span style=\"font-weight: bold; cursor: pointer;\" onclick=\"if (isempty($('#{$g['divId']} .itemForm-D001_Codigo_Produto').val())) { $('#{$g['divId']} .itemForm-D001_Codigo_Produto').val($(this).text()) } else { dialogConfirm('Aviso', 'Já existe um código, é necessário limpar ele para poder preencher.'); }\">{$proxCodigoProd}</span>";
				}
				$botoesEsq .= <<<EOT
					<span style="margin: 0 0 0 5px;">
						Próximo Código: <span style="font-weight: bold; cursor: pointer;" onclick="if (isempty($('#{$g['divId']} .itemForm-D001_Codigo_Produto').val())) { $('#{$g['divId']} .itemForm-D001_Codigo_Produto').val($(this).text()) } else { dialogConfirm('Aviso', 'Já existe um código, é necessário limpar ele para poder preencher.'); }">{$proxCodigo}</span>{$extra}
					</span>
EOT;
			} else {
				$botoesEsq .= <<<EOT
					<span style="margin: 0 0 0 5px;">
						Próximo Código: <span style="font-weight: bold;">(aguardando completar o cadastro)</span>
					</span>
EOT;
			}
			$proxEAN = $CAD002->obterNovoCodigoEAN();
			$botoesEsq .= <<<EOT
					<span style="margin: 0 0 0 5px;">
						Próximo EAN: <span style="font-weight: bold; cursor: pointer;" onclick="if (isempty($('#{$g['divId']} .itemForm-D001_Codigo_Barras').val())) { $('#{$g['divId']} .itemForm-D001_Codigo_Barras').val($(this).text()) } else { dialogConfirm('Aviso', 'Já existe um código EAN para este produto e ele não pode ser modificado.'); }">{$proxEAN}</span>
					</span>
EOT;
		}
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<script type="text/javascript">
		
		//Botão atualizar preço
		$('#cad002AtualizarPreco').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarPreco/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do preço?', buttons);
		});
		
		//Botão atualizar Estoque
		$('#cad002AtualizarEstoque_{$content1}').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoque/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		});

		//Botão atualizar Estoque Rápido
		$('#cad002AtualizarEstoqueRapido_{$content1}').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoque/?ajax=true&rapido=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		});

		//Juntar Marcas Cad002
		$('#cad002JuntarMarcas').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Juntar Marcas', '/cad/cad002/form/juntarMarcas/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}'), [428,175]);
		});	
		
		//Cadstrar Marcas
		$('#cad002CadastrarMarca').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Marcas', '/cad/cad002/content/cadastroMarca/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&divReload=" + encodeURIComponent('{$content1}'), [500,430]);
		});
		
		$('#{$engenharia}').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Engenharia do Produto', '/cad/cad052/content/cad052ContentEngenharia/', '&D001_Id=' + encodeURIComponent({$r_acaoId}), [1000,610]);
		});
		
		//Formulário Ecommerce
		$('#cad002Ecommerce').unbind('click').bind('click', function(data) {
			//if(typeof($('#cad002Ecommerce').data('D049_Id')) == 'undefined'){
			    //dialogConfirm('Selecionar Marca', 'Primeiro Selecione a Marca');
			//}else{
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'E-Commerce', '/cad/cad002/content/cad002ContentEcommerce/', '&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&D049_Id=" + encodeURIComponent($('#cad002Ecommerce').data('D049_Id')) + "&divReload=" + encodeURIComponent('{$content1}'), [550,550]);
			//}
		});	
		</script>
EOT;
	
	break;

	case 'cad002ContentNuvemShop':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		//$content2 = uniqid();
		//$g['smarty']->assign('content2', $content2);


		//$g['smarty']->assign('content1Width', '450px');
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormNuvemShop/', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain)),
			//array($content1, '/cad/cad002/form/cad002FormObservacaoMontagem/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id)),
			//array($content2, '/cad/cad002/tab/tabForm4/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		//$botoesDir .= '<button id="cad002AtualizarPreco">Atualizar Preços</button>';
		if(!empty($r_acaoId)){
			//$botoesDir .= '<button id="cad002AtualizarEstoque">Atualizar Estoque</button>';
			//$botoesDir .= '<button id="cad002AtualizarEstoqueRapido">Atualizar Estoque Rápido</button>';
		}
				
		
		$botoesEsq .= '<button type="submit" id="content_form2_btnEditarProduto">Enviar</button>';

		
		
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<script type="text/javascript">
		
		//Botão atualizar preço
		$('#cad002AtualizarPreco').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarPreco/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do preço?', buttons);
		});
		
		//Botão atualizar Estoque
		$('#cad002AtualizarEstoque').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoque/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		});

		//Botão atualizar Estoque Rápido
		$('#cad002AtualizarEstoqueRapido').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoque/?ajax=true&rapido=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
							dialogConfirm('Ok!', 'Com sucesso...!');
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		});

		//Juntar Marcas Cad002
		$('#cad002JuntarMarcas').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Juntar Marcas', '/cad/cad002/form/juntarMarcas/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}'), [428,175]);
		});	
		
		//Cadstrar Marcas
		$('#cad002CadastrarMarca').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Marcas', '/cad/cad002/content/cadastroMarca/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&divReload=" + encodeURIComponent('{$content1}'), [500,430]);
		});
		
		$('#engenharia').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Engenharia do Produto', '/cad/cad052/content/cad052ContentEngenharia/', '&D001_Id=' + encodeURIComponent({$r_acaoId}), [1000,610]);
		});
		
		//Formulário Ecommerce
		$('#cad002Ecommerce').unbind('click').bind('click', function(data) {
			//if(typeof($('#cad002Ecommerce').data('D049_Id')) == 'undefined'){
			    //dialogConfirm('Selecionar Marca', 'Primeiro Selecione a Marca');
			//}else{
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'E-Commerce', '/cad/cad002/content/cad002ContentEcommerce/', '&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&D049_Id=" + encodeURIComponent($('#cad002Ecommerce').data('D049_Id')) + "&divReload=" + encodeURIComponent('{$content1}'), [550,550]);
			//}
		});	
		</script>
EOT;
	
	break;
	
	case 'cad002contentHistóricoPrecos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		 $D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabHistoricoPrecos/', '&D009_Id=' . urlencode($D009_Id))
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad002ContentRetornarPrecos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		 $extraSql = (isset($r_extraSql)) ? $r_extraSql : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002tabRetonarPrecos/', '&extraSql=' . urlencode($extraSql))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentRetornarPrecosPromocao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$extraSql = (isset($r_extraSql)) ? $r_extraSql : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002tabRetonarPrecosPromocao/', '&extraSql=' . urlencode($extraSql))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentExportarXmlPrudotos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$extraSql = (isset($r_extraSql)) ? $r_extraSql : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabExportarXmlProdutos/', '&extraSql=' . urlencode($extraSql))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentEspecificacoesProduto':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$extraSql = (isset($r_extraSql)) ? $r_extraSql : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/outro/cad002EspecificacoesProduto/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentJuntar':
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
		array($content1, '/cad/cad002/tab/cad002TabJuntar/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentProdutosSerie':

		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height','70px;');
		$g['smarty']->assign('tipo', '2linhas');
		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormProdutosSerie/', '&tab=geral&D009_Id=' . urlencode($acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/cad/cad002/grid/cad002GridProdutosSerie/', '&D009_Id=' . urlencode($acaoId) . '&divReload=' . urlencode($content1))
			//array($content2, '/est/est008/grid/est008GridProdutos/', '&T010_Id=' . urlencode($r_T010_Id) . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);
	break;

	
	case 'cad002ContentListaFornecedorIncluir':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$extraSql = (isset($r_extraSql)) ? $r_extraSql : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabListaFornecedor/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentListaFornecedorAlterar':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$extraSql = (isset($r_extraSql)) ? $r_extraSql : false;
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabListaFornecedorAlterar/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ContentListaFornecedorAlterarProduto2rows':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
        //$g['smarty']->assign('content1Height', '190px');

		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			//array($content1, '/cad/cad002/form/cad002FormListaFornecedorProduto/', '&tab=geral&D070_Id=' . urlencode($r_acaoId) . '&divReload='.urlencode($content2)),
			//array($content1, '/cad/cad002/grid/comercial/', '&acaoId=' . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($content2)  .  "&divReload=" . urlencode($content2)),
			array($content1, '/cad/cad002/grid/cad002GridListaFornecedorProdutos/', '&D070_Id=' . urlencode($r_acaoId) . '&divReload='.urlencode($content1)),
		);
		
		echo gProcessaAcoes($actions);
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad002FormListaFornecedorBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002ContentAssociarProduto':
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
			array($content1, '/cad/cad002/tab/cad002TabAssociarProduto/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentDocumentosProduto':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabDocumentosProduto/', '&tab=geral&acaoId='.urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'contentDemonstrativoCusto':
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
			array($content1, '/cad/cad002/outro/demonstrativoCusto/', '&T001_Id=' . urlencode($r_acaoId) . '&popUp=true')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentEstoqueExterno':
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
			array($content1, '/cad/cad002/tab/cad002TabEstoqueExterno/', '&D001_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tabela':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '35px;');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormAssociarListaFornecedorProduto/', '&tab=geral&acaoId=' . urlencode($_REQUEST['acaoId']) . '&divForm=' . $content2),
            array($content2, '/cad/cad002/grid/cad002GridListaFornecedorProduto/', '&tab=geral&acaoId=' . urlencode($_REQUEST['acaoId']))
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		/*
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		*/
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;

	case 'cad002ContentListaFornecedor':
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
			array($content1, '/cad/cad002/tab/cad002TabTabelaFornecedor/', '&D001_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002contentCadastroFornecedorCompras':

		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabProdutoFornecedorCompras/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
    
	break;

	case 'cad002contentCadastroFornecedorProduto':
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
			array($content1, '/cad/cad002/form/cad002formCadastroFornecedorProduto/', '&tab=geral&D001_Id=' . urlencode($r_D001_Id))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesEsq .= '<button type="submit" id="cad002PreCadastroFornecedor">Pré-Cadastro Fornecedor</button>';
		$botoesDir .= '<button type="submit" id="cad002formCadastroFornecedorProduto_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002PreCadastroFornecedor').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Novo Fornecedor', '/cad/cad001/content/cad001CotacaoIncluirFornecedor/', '&fornecedor=true', [570,290]);
				});
		    } </script></div>
EOT;
	break;

	case 'cad002contentCadastroProdutoBase':
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
			array($content1, '/cad/cad002/form/cad002FormMontagem/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002formCadastroFornecedorProduto_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002ContentItensMontagem':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '30px');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormMontagem/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($content2)),
			array($content2, '/cad/cad002/tab/cad002TabItensMontagem/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002ContentIncluirProdutoFornecedor':
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
			array($content1, '/cad/cad002/form/cad002FormIncluirProdutoFornecedor/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormIncluirProdutoFornecedor_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002ContentIncluirProdutoTabelaFornecedor':
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
			array($content1, '/cad/cad002/form/cad002FormIncluirProdutoTabelaFornecedor/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormIncluirProdutoTabelaFornecedor_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002ContentEstoqueSimilares':
	
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
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
			array($content1, '/cad/cad002/tab/cad002TabEstoqueSimilares/', '&D001_Id=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($T003_Id) . '&T005_Id=' . urlencode($T005_Id))
		); 
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentProdutosHistoricoMontagem':
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
			array($content1, '/cad/cad002/tab/cad002TabProdutosHistoricoMontagem/', '&acaoId=' . urlencode($r_acaoId) . '&T001_Id=' . urlencode($r_T001_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentIncluirSimilar':
		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		$acao_Id = isset($r_acaoId) ? $r_acaoId : false;
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
			array($content1, '/cad/cad002/tab/cad002TabIncluirSimilar/', '&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002contentCadastroFornecedorLote':
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
			array($content1, '/cad/cad002/form/cad002formCadastroFornecedorLote/', '&tab=geral')
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesEsq .= '<button type="submit" id="cad002PreCadastroFornecedor">Pré-Cadastro Fornecedor</button>';
		$botoesDir .= '<button type="submit" id="cad002formCadastroFornecedorLote_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002PreCadastroFornecedor').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Novo Fornecedor', '/cad/cad001/content/cad001CotacaoIncluirFornecedor/', '&fornecedor=true', [570,290]);
				});
		    } </script></div>
EOT;
	break;

	case 'cad002ContentJuntarLocacao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabJuntarLocacao/', '&tab=geral&D009_Id=' . urlencode($r_D009_Id))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad002ImportarCSV':
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
           	array($content1, '/cad/cad002/form/cad002FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/cad/cad002/outro/cad002ManualCSVProdutos/','&tab=geral')
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

	case 'cad002ContentCadastroFornecedoresProdutos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividido o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabCadastroFornecedoresProdutos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentAtivosImobilizados':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividido o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabAtivosImobilizados/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ImportarCSVFornecedorProduto':
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
           	array($content1, '/cad/cad002/form/cad002FormImportarCSVFornecedorProduto/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/cad/cad002/outro/cad002ManualCSVProdutosFornecedores/','&tab=geral')
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

	case 'cad002ContentPreco':
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
			array($content1, '/cad/cad002/tab/cad002tabGridPreco/', '&D001_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad002ContentIncluirConversaoUnidade':
		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		$acao_Id = isset($r_acaoId) ? $r_acaoId : false;
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
			array($content1, '/cad/cad002/tab/cad002TabConversaoUnidade/', '&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentTabECommerceTray':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividido o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');	
		// O que será carregado em cada conteúdo?

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/cad002TabECommerceTray/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentFormECommerceTray':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividido o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');	
		// O que será carregado em cada conteúdo?

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormECommerceTray/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad002ContentFormAtributosECommerceTray':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividido o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');	
		// O que será carregado em cada conteúdo?

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/form/cad002FormAtributosECommerceTray/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'contentValidade':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabValidade/')
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'contentDocumentoValidade':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad002/tab/tabDocumentoValidade/')
		);
		echo  gProcessaAcoes($actions);
	break;
	
}
