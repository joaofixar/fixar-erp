<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'cad020ContentPrincipal':
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
			array($content1, '/cad/cad020/tab/cad020tabGrid/')
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="button" id="cad020Incluir">Incluir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<script>
			$('#cad020Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Novo Cadastro', '/cad/cad020/content/cad020form1/', '', [550,550]);
			});			
		</script>
EOT;
	break;
	
	case 'cad020form1':
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
			array($content1, '/cad/cad020/tab/cad020tabForm1/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad020form2':
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
			array($content1, '/cad/cad020/tab/cad020tabForm2/', '&acaoId=' . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id)),
		);
		echo  gProcessaAcoes($actions);
		
	break;
		
	case 'cad020detalhes':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad020/grid/cad020comercial/', '&acaoId=' . urlencode($_REQUEST['acaoId']) . '&idTabForm4=' . urlencode($content2)),
			array($content2, '/cad/cad020/tab/cad020tabForm4/'),
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesEsq .= '<button id="cad020AtualizarPreco">Atualizar Preços</button>';
		$botoesEsq .= '<button id="cad020AtualizarEstoque">Atualizar Estoque</button>';
		$botoesEsq .= '<button id="cad020JuntarMarcas">Juntar Marcas</button>';
		$botoesEsq .= '<button id="cad020CadastrarMarca">Cadastrar Marca</button>';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
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
		$('#cad020AtualizarPreco').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad020/grid_func-ajax/atualizarPreco/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var buttons = {
								"Ok": function() { $(this).dialog("destroy").remove(); }
							}
							dialogConfirm('Ok!', request.data, buttons);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do preço?', buttons);
		});
		
		//Botão atualizar Estoque
		$('#cad020AtualizarEstoque').unbind('click').bind('click', function() {
			var buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad020/grid_func-ajax/atualizarEstoque/?ajax=true&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var buttons = {
								"Ok": function() { $(this).dialog("destroy").remove(); }
							}
							dialogConfirm('Ok!', request.data, buttons);
						}
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			}
			dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		});
		
		//Juntar Marcas cad020
		$('#cad020JuntarMarcas').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Juntar Marcas', '/cad/cad020/form/juntarMarcas/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}'), [428,175]);
		});	
		
		//Cadstrar Marcas
		$('#cad020CadastrarMarca').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Marcas', '/cad/cad020/content/cadastroMarca/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}'), [460,400]);
		});	
		</script>
EOT;
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
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad020/form/certificados/', '&tab=geral&D001_Id=' . urlencode($_REQUEST['acaoId']) . '&idTabForm4=' . urlencode($content2). '&D024_Id=' . urlencode($r_D024_Id))
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
			array($content1, '/cad/cad020/tab/tabForm5CadastroMarca/', '&tab=geral&acaoId=' . urlencode($_REQUEST['acaoId']))
		);
		echo  gProcessaAcoes($actions);
		
		
	break;
    
    case 'cad020ContentImprimirEtiquetas':
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
            array($content1, '/cad/cad051/form/cad051SelecionarModelo/', '&tab=geral&D009_Id=true'),
            array($content2, '/cad/cad020/grid/cad020GridProdutosImprimir/'),
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

    case 'cad020form2Comum':
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
			array($content1, '/cad/cad020/tab/cad020tabForm2Comum/', '&acaoId=' . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id)),
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'cad020ContentCabecalhoProdutoComum':
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
			array($content1, '/cad/cad020/form/form2Comum/', "&tab=geral&modoFormLeve=true&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain)),
			//array($content2, '/cad/cad002/tab/tabForm4/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		//$botoesDir .= '<button id="cad002AtualizarPreco">Atualizar Preços</button>';
		$botoesDir .= '<button id="cad020AtualizarEstoque">Atualizar Estoque</button>';
		$sql = mysql_query("SELECT * FROM D049 LEFT JOIN D001 ON D001_Id = D049_D001_Id WHERE D001_Id = '{$r_acaoId}'");
		$res = mysql_fetch_array($sql);	
		$multimarcas = $g['C031']['multimarcas'];
		if($multimarcas != 'N' && $res['D001_Flag_Multimarcas'] == 'S') {
			$botoesDir .= '<button id="cad020JuntarMarcas">Juntar Marcas</button>';
			$botoesDir .= '<button id="cad020CadastrarMarca">Incluir Marca</button>';
		}
		if($g['C031']['utilizaEcommerce'] == 'S'){
			//$botoesDir .= '<button id="cad020Ecommerce">E-Commerce</button>';
		}
		if($res['D001_Flag_Tipo'] == 'PRODUZIDO'){
			//$botoesDir .= '<button type="button" id="engenharia">Engenharia</button>';
		}
		$botoesEsq .= '<button type="submit" id="content_form2_btnEditarProduto">Enviar</button>';
		$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		
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
		$('#cad020AtualizarPreco').unbind('click').bind('click', function() {
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
		$('#cad020AtualizarEstoque').unbind('click').bind('click', function() {
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
		$('#cad020JuntarMarcas').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Juntar Marcas', '/cad/cad002/form/juntarMarcas/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}'), [428,175]);
		});	
		
		//Cadstrar Marcas
		$('#cad020CadastrarMarca').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Marcas', '/cad/cad002/content/cadastroMarca/', '&tab=geral&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&divReload=" + encodeURIComponent('{$content1}'), [500,430]);
		});
		
		$('#engenharia').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Engenharia do Produto', '/cad/cad052/content/cad052ContentEngenharia/', '&D001_Id=' + encodeURIComponent({$r_acaoId}), [1000,610]);
		});
		
		//Formulário Ecommerce
		$('#cad020Ecommerce').unbind('click').bind('click', function(data) {
			//if(typeof($('#cad002Ecommerce').data('D049_Id')) == 'undefined'){
			    //dialogConfirm('Selecionar Marca', 'Primeiro Selecione a Marca');
			//}else{
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'E-Commerce', '/cad/cad002/content/cad002ContentEcommerce/', '&acaoId=' + encodeURIComponent('{$_REQUEST['acaoId']}') + "&D049_Id=" + encodeURIComponent($('#cad002Ecommerce').data('D049_Id')) + "&divReload=" + encodeURIComponent('{$content1}'), [550,550]);
			//}
		});	
		</script>
EOT;
	break;
}
