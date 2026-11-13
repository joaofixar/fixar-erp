<?php
namespace hardness;
set_time_limit(0);

require_once('bibliotecas/classes/VEN001.php');
$VEN001 = new VEN001();
require_once('bibliotecas/classes/VEN003.php');
$VEN003 = new VEN003();  	

// vars
$vars = '';
foreach ($_REQUEST as $key => $value) {
	if ($key == 'r' || $key == 'ajax' || $key == 'divId' || $key == 'divIdRoot' || $key == 'divIdParent' || is_array($value)) { continue; }
	$vars .= "&{$key}=" . urlencode($value);
}

switch ($g['r'][3]) {
	/* GRID PRINCIPAL */

	case 'content':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/inclusaoMultiProdutos/tab/', '&tab=geral' . $vars),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/sistema/funcoes/inclusaoMultiProdutos/grid/', 'Importar produtos', $vars);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;

	 case 'grid':
	 	foreach ($_REQUEST as $key => $value) {
	 		if(substr($key, 0, 4) == 'T003'){
	 			$tabela = 'T003';
	 		} else if(substr($key, 0, 4) == 'T005'){
	 			$tabela = 'T005';
	 		} else if(substr($key, 0, 4) == 'T007'){
	 			$tabela = 'T007';
	 		} else if(substr($key, 0, 4) == 'T013'){
	 			$tabela = 'T013';
	 		} else if(substr($key, 0, 4) == 'T218'){
	 			$tabela = 'T218';
	 		} else if(substr($key, 0, 4) == 'T224'){
	 			$tabela = 'T224';
	 		}
	 	}
	 	/**
		 * Definição do grid tabela
		 */
		$grid = array(
		    //array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;'),
		    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Incluir_Produto', 'callback' => 'gCheckbox', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'callbackParameter' => array('D009_Flag_Incluir_Produto', 'N|S'), 'colunaEditarTodos' => true),
		    array('colunaValor' => 'D049_Flag_Curva_ABC', 'colunaTitulo' => 'C', 'colunaAlinhar' => 'center', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '720')),
		    array('colunaValor' => 'D001_C008_Id', 'colunaTitulo' => 'Depto', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D001_D060_Id', 'colunaTitulo' => 'Setor', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN Int', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D001_Codigo_Barra1', 'colunaTitulo' => 'EAN Fab', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referência', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D069_D070_Id', 'colunaTitulo' => 'Tb', 'style' => 'font-weight:normal', 'colunaAlinhar' => 'center'),
		    array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D049_Codigo_Produto', 'colunaTitulo' => 'Descrição inglês...', 'colunaExibir' => false),
		    array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Linha', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
		    array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Depto', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
		    array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Setor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
		    array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'Sub-Grupo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
			array('colunaValor' => 'D049_D082_Id', 'colunaTitulo' => 'Marca', 'callback' => 'campoMostraValor', 'callbackParameter' => 'D082_Marca'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Origem_Mercadoria', 'colunaTitulo' => 'Origem', 'colunaAlinhar' => 'center'),
		    array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as NCM', 'colunaTitulo' => 'NCM', 'colunaExibir' => false),
			array('colunaValor' => 'D001_D005_Id', 'colunaTitulo' => 'NCM', 'callback' => 'campoMostraValor', 'callbackParameter' => 'NCM'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => false),
			array('colunaValor' => 'D001_D037_Id', 'colunaTitulo' => 'Un', 'callback' => 'campoMostraValor', 'callbackParameter' => 'D037_Unidade'),
		    array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq.Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque', 'colunaCasasDecimais' => 2),
		    array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq.Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
		    array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq.Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
		    array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
		    array('colunaValor' => "if(D149_Flag_ST='S','ST','') as ST", 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    //array('colunaValor' => "(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
		    array('colunaValor' => 'D009_IPV_1', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
		    array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaExibir' => false),
			array('colunaValor' => 'D009_C007_Id_Comprador', 'colunaTitulo' => 'Comprador', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
		    array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'chavePrimaria' => true, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => true, 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => 'Ativo'),
		);
		$from = 'D001';
		$extra = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join C007 on C007_Id=D009_C007_Id_Comprador
			left join D069 on D069_D009_Id=D009_Id
			left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			GROUP BY D009_Id
EOT;
	
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
		       return 'linhaCinza';
		    }
		    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
		       return 'linhaVermelho';
		    }
		}	

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Codigo_Barras,D001_Codigo_Barra1' => array('titulo' => 'EAN', 'tipo' => 'texto'),
            'D001_Aplicacao' => array('titulo' => 'Referência', 'tipo' => 'texto'),
            "ifnull(D082_Marca[[VIRGULA]]'')" => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto'),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto'),
            'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estoq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estoq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estoq.Loja', 'tipo' => 'numero'),
            'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'D009_Data_Ultima_Entrada' => array('titulo' => 'Últ.Entrada', 'tipo' => 'data'),
            'D009_Data_Ultimo_Movimento' => array('titulo' => 'Últ.Mov', 'tipo' => 'data'),
            'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D069_D070_Id' => array('titulo' => 'Tabela Fornecedor', 'tipo' => 'texto', 'exibir' => false),
            'D049_Modelo' => array('titulo' => 'Sub-item', 'tipo' => 'texto'),
            'D001_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
            'D049_Origem_Mercadoria' => array('titulo' => 'Origem', 'tipo' => 'numero'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D009_Flag_Incluir_Produto' => array('titulo' => 'Marcado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
        );
		// Gera o filtro e acrescenta ao $extra
		$g['cacheCount'] = true;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
				
		/**
		* Geração: Monta o SQL e retorna os dados
		*/
		$geradoSql = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$sql_ = urlencode(base64_encode($geradoSql));

		$opcaoPreco = '<form id="opcaoPreco" class="radioButton" align="center"><input type="radio" id="radio0" name="radioButtonA" value="0" checked="true"><label for="radio0">Valor Zero</label><input type="radio" id="radio1" name="radioButtonA" value="1"><label for="radio1">Custo Final</label><input type="radio" id="radio2" name="radioButtonA" value="2"><label for="radio2">Preço Loja</label><input type="radio" id="radio3" name="radioButtonA" value="3"><label for="radio3">Preço Venda</label></form>';
		$opcaoQuantidade = '<form id="opcaoQuantidade" class="radioButton" align="center"><input type="radio" id="radio4" name="radioButtonB" value="0" checked="true"><label for="radio4">Quantidade Zero</label><input type="radio" id="radio5" name="radioButtonB" value="1"><label for="radio5">Estoque Líquido</label><input type="radio" id="radio6" name="radioButtonB" value="2"><label for="radio6">Estoque Loja</label></form>';

		echo <<<EOT
		    <div class="buttonsBottomTop">
		        <button type="button" id="incluirProdutosBtn">Incluir</button>
		    </div>
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		        $('#incluirProdutosBtn').unbind('click').bind('click', function() {
		            var buttons = {
		                "Sim": function() {
		                    $('#{$g['divId']}').showLoading();
		                    var opcaoPreco = $('input[name=radioButtonA]:checked', '#opcaoPreco').val();
		                    var opcaoQuantidade = $('input[name=radioButtonB]:checked', '#opcaoQuantidade').val();
		                    $.getJSON('/sistema/funcoes/inclusaoMultiProdutos/incluir/?opcaoPreco=' + encodeURIComponent(opcaoPreco) + '&opcaoQuantidade=' + encodeURIComponent(opcaoQuantidade) + '&tabela={$tabela}&acaoId={$r_acaoId}&divId={$g['divId']}&divIdRoot={$g['divIdRoot']}&ajax=true&callback=?', function(request) {
		                        $('#{$g['divId']}').hideLoading();
		                        if (request.code) {
		                            dialogConfirm('Ok!', request.data);
		                            divRefresh('{$g['divId']}', true);
		                            divRefresh('{$g['divIdParent']}', true);
		                        }
		                    });
		                    $(this).dialog("destroy").remove();
		                },
		                "Não": function() { $(this).dialog("destroy").remove(); }
		            }
		            var dialogo = dialogConfirm('Finalizar Nota', 'Por favor, confirme o número.<br /><br />Preço do Produto:<a></a><br />Quantidade:<b></b>', buttons);
					$('{$opcaoPreco}').attr('id', 'opcaoPreco').appendTo($("#dc-" + dialogo).find('a')).focus();
					$('{$opcaoQuantidade}').attr('id', 'opcaoQuantidade').appendTo($("#dc-" + dialogo).find('b')).focus();
					$(function() {
					    $(".radioButton").buttonset();
					});
		        });
		    } </script></div>
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				   var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
					    // somente executa a ação ao clicar na linha ou na coluna
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						// pega o ID da linha
						var id = $(this).closest('tr').attr('id').split('|');
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&tabela=$from&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
				   });
				   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
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
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;

	 case 'incluir':
	 	global $g;
	 	$resposta = array('code' => true, 'data' => array());
	 	$count = 0;
	 	if($r_opcaoQuantidade != 0){
	 		$extra = ($r_opcaoQuantidade == 1) ? "AND UPPER(D146_Descricao) != 'LOJA'" : "AND UPPER(D146_Descricao) = 'LOJA'";
	 		$locacao = ($r_opcaoQuantidade == 1) ? 'atacado' : 'loja';
	 	} else {
	 		$extra = "GROUP BY D009_Id";
	 	}

	 	$sqlD009 = mysql_query("SELECT D009_Id, T066_Id, D146_Descricao FROM D009 LEFT JOIN T066 ON T066_D009_Id = D009_Id LEFT JOIN D004 ON D004_Id = T066_D004_Id LEFT JOIN D146 ON D146_Id=D004_D146_Id JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Flag_Incluir_Produto = 'S' AND D009_C004_Id = '{$g['empresaAtual']}' {$extra}");
	 	while($D009 = mysql_fetch_array($sqlD009)){
	 		if($r_tabela == 'T003'){
		        $retorno = incluirProdutoOrcamento($r_acaoId, $D009['D009_Id'], $r_opcaoPreco, $r_opcaoQuantidade);
		        if($retorno == true){
		        	$count++;

		        }
			} else if($r_tabela == 'T007'){
		        $retorno = incluirProdutoNotaVenda($r_acaoId, $D009['D009_Id'], $r_opcaoPreco, $r_opcaoQuantidade, $D009['T066_Id']);
		        if($retorno == true){
		        	$count++;
		        	mysql_query("UPDATE D009 SET D009_Flag_Incluir_Produto = 'N' WHERE D009_Id = '{$D009['D009_Id']}'");
		        }
			}
		}

		switch($r_tabela){
			case 'T007':
				$VEN003->ven003atualizarCFOP($r_acaoId);
			break;
		}

		if($count <= 0){
			$resposta['data'] = 'Nenhum produto foi incluído!<br /><br />';
		} else if($count > 0){
			if($count > 1) {
				$resposta['data'] = $count.' produtos foram incluídos com sucesso!<br /><br />';
			} else {
				$resposta['data'] = 'O produto foi incluído com sucesso!<br /><br />';
			}
		}

		//Verifica se algum produto não foi incluído por não ter armazém
		$arrayProdutosOk = array();
		$arrayProdutosNaoOk = array();
		$sqlD009 = mysql_query("SELECT D009_Id, D001_Codigo_Produto, D001_Descricao_Produto, D146_Id, D146_Descricao, D004_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN T066 ON T066_D009_Id=D009_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id LEFT JOIN D146 ON D146_Id=D004_D146_Id WHERE D009_Flag_Incluir_Produto = 'S' AND D009_C004_Id = '{$g['empresaAtual']}'");
		while($resD009 = mysql_fetch_array($sqlD009)){
			if(in_array($resD009['D009_Id'], $arrayProdutosOk)) { continue; }
			
			if($resD009['D146_Id'] == $r_opcaoQuantidade && $r_opcaoQuantidade != 0){
				$arrayProdutosOk[$resD009['D009_Id']] = true;
				unset($arrayProdutosNaoOk[$resD009['D009_Id']]);
			} else if($resD009['D004_Id'] <= 0 || empty($resD009['D004_Id']) || $resD009['D146_Id'] != $r_opcaoQuantidade && !isset($arrayProdutosOk[$resD009['D009_Id']])){
				$arrayProdutosNaoOk[$resD009['D009_Id']] = "O produto {$resD009['D001_Codigo_Produto']} não possuí locação no armazém {$locacao}.";
			}
		}
		if (!empty($arrayProdutosNaoOk)) {
			$resposta['data'] .= implode('<br/>', $arrayProdutosNaoOk);
		}
	 	$resposta['code'] = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	 break;
}

function incluirProdutoOrcamento($T003_Id, $T004_D009_Id, $preco, $quantidade) {
    global $g;
	require_once('bibliotecas/classes/CAD002.php');
    $CAD002 = new CAD002();
    require_once('bibliotecas/classes/VEN001.php');
    $VEN001 = new VEN001();
    
    $resultado = mysql_query("SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$T003_Id}'");
    $rowT003   = mysql_fetch_assoc($resultado);
    
    $sql = <<<EOT
        SELECT * FROM D001
            LEFT JOIN D049 ON D049_D001_Id=D001_Id
            LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id = '{$rowT003['T003_D024_Id']}'
            LEFT JOIN D009 ON D009_D049_Id=D049_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D082 ON D082_Id=D049_D082_Id
            LEFT JOIN D041 ON D041_D049_Id=D049_Id
            LEFT JOIN D005 ON D005_Id=D001_D005_Id
                WHERE D009_Id = '{$T004_D009_Id}'
EOT;
            
    $resultado = mysql_query($sql);
    $row       = mysql_fetch_assoc($resultado);
    
    $cClausula = " SELECT * ";
    $cClausula .= " FROM T003 ";
    $cClausula .= " LEFT JOIN D024 ON D024_Id=T003_D024_Id ";
    $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
    $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T003_C004_Id ";
    $cClausula .= " LEFT JOIN C004 ON C004_Id=T003_C004_Id ";
    $cClausula .= " WHERE T003_Id='{$T003_Id}'";
    $T003  = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro)) {
        return "$cErro<br />$cClausula";
    }
    $mT003 = mysql_fetch_array($T003);
    
    /**
     * Os campos que estão atribuidos o valor zero que é não são selecionados nos selects acima.
     */
    $T004_D037_Id              	  = $row['D001_D037_Id'];
    $T004_Descricao_Produto    	  = mysql_real_escape_string($row['D001_Descricao_Produto']);
    $T004_Codigo_Produto       	  = $row['D001_Codigo_Produto'];
    $T004_IPV                  	  = "";
    $T004_Codigo_Substituto       = $row['D083_Codigo_Produto_Fornecedor'];
    $T004_Numero_Pedido_Compra    = '';
    $T004_Item_Pedido_Compra      = 'NULL';
    $T004_Observacao              = '';
    $T004_Observacao_Entrega      = '';
    $T004_Aliquota_IPI            = $row['Aliquota_IPI'];
    $idVeiculoOrcamento           = 0;
    $T004_Percentual_Desconto     = 0;
    $T004_Valor_Desconto_Unitario = 0;

    $D001  = mysql_query("SELECT D001_Id,
                              D082_Id,
                              D001_Codigo_Produto,
                              D049_Origem_Mercadoria,
                              D001_Flag_Tipo,
                              D049_Flag_Nacional_Importado,
                              D001_Cubagem_Unitaria,
                              T001_Id
                         FROM D001
                    LEFT JOIN D049 ON D049_D001_Id=D001_Id
                    LEFT JOIN D009 ON D009_D049_Id=D049_Id
                    LEFT JOIN T001 ON T001_Id=D009_T001_Id
                    LEFT JOIN D082 ON D082_Id=D049_D082_Id
                        WHERE D009_Id='$T004_D009_Id'");
    $mD001 = mysql_fetch_array($D001);
    
    $T004_D001_Id        = $mD001['D001_Id'];
    $T004_Codigo_Produto = $mD001['D001_Codigo_Produto'];
    
    $C007  = mysql_query("SELECT * FROM C007 WHERE C007_Id={$g['usuarioAtual']}");
    $mC007 = mysql_fetch_array($C007);
    
    $T004  = mysql_query("SELECT T004_Item FROM T004 WHERE T004_T003_Id='$T003_Id' ORDER BY T004_Item DESC LIMIT 1");
    $mT004 = mysql_fetch_array($T004);
    
    $T004_Item = $mT004['T004_Item'] + 1;
    
    $cClausula = " SELECT ";
    $cClausula .= " D009_Id, ";
    $cClausula .= " D049_Id, ";
    $cClausula .= " D001_Id, ";
    $cClausula .= " D005_Flag_ST, ";
    $cClausula .= " D009_Valor_Custo_Unitario, ";
    $cClausula .= " D009_Flag_Preco_Tabelado, ";
    $cClausula .= " D049_Origem_Mercadoria, ";
    $cClausula .= " D001_Flag_Pre_Cadastro ";
    $cClausula .= " FROM D001 ";
    $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
    $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
    $cClausula .= " LEFT JOIN D005 ON D005_Id=D001_D005_Id ";
    $cClausula .= " WHERE D009_Id='$T004_D009_Id' ";
    $T004  = mysql_query($cClausula);
    $mT004 = mysql_fetch_array($T004);
    
    $erro = mysql_error();
    if (!empty($erro) && !$T004_D009_Id) {
        return "Erro consulta custo produto: $erro";
    }
    
    if (($mT004['D009_Id']) <= 0) {
        return "Erro: Produto não encontrado";
    }
    
    // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
    $listPreco    = mysql_query("SELECT T003_Flag_Lista_Preco, T003_D024_Id, T003_C004_Id, T003_Flag_ACP, T003_C004_Id_Estoque FROM T003 WHERE T003_Id='{$T003_Id}'");
    $mLista_Preco = mysql_fetch_array($listPreco);
    $D024_Id      = $mLista_Preco['T003_D024_Id'];
    
    $cfop = $VEN001->retornaCFOP($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $rowT003['T003_Flag_Destino_Produto'], false, false, false, $mLista_Preco['T003_C004_Id_Estoque']);
    
    $T004_D006_Id = $cfop;
    
    $Aliquotas = $VEN001->retornaAliquotas($T004_D009_Id, $T003_Id, flase, false, false, false, false, $cfop);
    
    $dadosPrecoVenda = $CAD002->retornaPrecoVenda($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, false, $cfop, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
    $precoVenda      = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
    $demonstrativo   = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
    $custoProduto    = $dadosPrecoVenda[4];

    /*return array(
        0 $preco,
        1 $demonstrativo,
        2 $custoMedio,
        3 $quantidadeEstoque,
        4 $custoProduto,
        5 $origemMercadoria,
        6 $possuiNFEntrada
    );*/
    
    $demonstrativoT001 = $mD001['T001_Id'];
    
    if ($T004_Aliquota_IPI > 0 || $Aliquotas['Aliquota_IPI']) {
        $D088_Codigo_ST = '50';
    } else {
        $D088_Codigo_ST = '53';
    }
    
    // Buscando o percentual de tributos (lei transparências)           
    $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
    $mD149 = mysql_fetch_array($D149);
    
    $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$T004_D006_Id}'");
    $mD006 = mysql_fetch_array($D006);
    
    if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
        $T004_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
    } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
        $T004_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
    } else {
        $T004_Percentual_Trib_Transparencia = 0;
    }

    switch ($quantidade) {
 		case 0:
 			$T004_Quantidade = 0;
 		break;
 		case 1:
 			$T004_Quantidade = $row['D009_Quantidade_Estoque_Liquido'];
 		break;
 		case 2:
 			$T004_Quantidade = $row['D009_Quantidade_Estoque_Loja'];
 		break;
 	}

    switch ($preco) {
 		case 0:
 			$T004_Valor_Preco_Unitario = 0;
 		break;
 		case 1:
 			$T004_Valor_Preco_Unitario = $row['D009_Valor_Custo_Unitario'];
 		break;
 		case 2:
 			$T004_Valor_Preco_Unitario = $row['D009_Preco_Loja'];
 		break;
 		case 3:
 			$T004_Valor_Preco_Unitario = $precoVenda;
 		break;
 	}
    
    $cClausula = " insert into T004 (";
    $cClausula .= " T004_C004_Id, ";
    $cClausula .= " T004_Item, ";
    $cClausula .= " T004_Codigo_Produto, ";
    $cClausula .= " T004_Codigo_Substituto, ";
    $cClausula .= " T004_Descricao_Produto, ";
    $cClausula .= " T004_T003_Id, ";
    $cClausula .= " T004_D024_Id, ";
    $cClausula .= " T004_D009_Id, ";
    $cClausula .= " T004_D037_Id, ";
    $cClausula .= " T004_D082_Id, ";
    $cClausula .= " T004_Quantidade, ";
    $cClausula .= " T004_Valor_Custo_Unitario, ";
    $cClausula .= " T004_Valor_Preco_Unitario, ";
    $cClausula .= " T004_Valor_Preco_Sem_Desconto_Unitario, ";
    $cClausula .= " T004_Valor_Preco_Original, ";
    $cClausula .= " T004_Valor_Preco_Original_Bruto, ";
    $cClausula .= " T004_Percentual_Desconto, ";
    $cClausula .= " T004_Valor_Desconto_Unitario, ";
    $cClausula .= " T004_Percentual_Desconto_Bruto, ";
    $cClausula .= " T004_Valor_Desconto_Unitario_Bruto, ";
    $cClausula .= " T004_IPV, ";
    $cClausula .= " T004_Peso_Unitario, ";
    $cClausula .= " T004_Peso_Bruto, ";
    $cClausula .= " T004_D005_Id,";
    $cClausula .= " T004_Classificacao_Fiscal,";
    $cClausula .= " T004_D006_Id,";
    $cClausula .= " T004_Aliquota_IPI,";
    $cClausula .= " T004_Aliquota_ICMS,";
    $cClausula .= " T004_Percentual_Reducao_ICMS,";
    $cClausula .= " T004_Percentual_Reducao_ICMS_ST,";
    $cClausula .= " T004_ST_VA,";
    $cClausula .= " T004_Flag_ST,";
    $cClausula .= " T004_Flag_Situacao_Tributaria,";
    $cClausula .= " T004_ICMS_Inter_Estadual,";
    $cClausula .= " T004_ST_ICMS_Interno,";
    $cClausula .= " T004_ICMS_Inter_Estadual_MVA,";
    $cClausula .= " T004_ST_ICMS_Interno_MVA,";
    $cClausula .= " T004_Flag_Isento_PIS,";
    $cClausula .= " T004_Aliquota_PIS,";
    $cClausula .= " T004_Situacao_Tributaria_PIS,";
    $cClausula .= " T004_Flag_Isento_COFINS,";
    $cClausula .= " T004_Aliquota_COFINS, ";
    $cClausula .= " T004_Flag_Preco_Especial, ";
    $cClausula .= " T004_Situacao_Tributaria_COFINS, ";
    $cClausula .= " T004_Situacao_Tributaria_IPI, ";
    $cClausula .= " T004_D059_Id, ";
    $cClausula .= " T004_Observacao, ";
    $cClausula .= " T004_Numero_Pedido_Compra, ";
    
    if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
        $cClausula .= " T004_Item_Pedido_Compra, ";
        $cClausula .= " T004_Valor_Preco_Bruto, ";
    } else {
        $cClausula .= " T004_Item_Pedido_Compra, ";
    }
    $cClausula .= " T004_Cubagem_Unitaria, ";
    $cClausula .= " T004_Percentual_Trib_Transparencia, ";
    $cClausula .= " T004_Flag_Pre_Cadastro, ";
    $cClausula .= " T004_D144_Id, ";
    $cClausula .= " T004_Flag_Origem_Produto, ";
    $cClausula .= " T004_Demonstrativo_Preco, ";
    $cClausula .= " T004_T001_Id, ";
    $cClausula .= " T004_Observacao_Entrega, ";
    $cClausula .= " T004_Flag_Destino_Produto ";
    $cClausula .= " ) VALUES ( ";
    $cClausula .= " '{$g['empresaAtual']}', ";
    $cClausula .= " '{$T004_Item}', ";
    $cClausula .= " '{$T004_Codigo_Produto}', ";
    $cClausula .= " '{$T004_Codigo_Substituto}', ";
    $cClausula .= " '{$T004_Descricao_Produto}', ";
    $cClausula .= " '{$T003_Id}', ";
    $cClausula .= " '{$D024_Id}', ";
    $cClausula .= " '{$T004_D009_Id}', ";
    $cClausula .= " '{$T004_D037_Id}', ";
    $cClausula .= " '{$Aliquotas['D082_Id']}', ";
    $cClausula .= " '{$T004_Quantidade}', ";
    $cClausula .= " '{$custoProduto}', ";
    $cClausula .= " '{$T004_Valor_Preco_Unitario}', ";
    $cClausula .= " '{$precoVenda}', ";
    $cClausula .= " '{$precoVenda}', ";
    $cClausula .= " '{$precoVenda}', ";
    $cClausula .= " '{$T004_Percentual_Desconto}', ";
    $cClausula .= " '{$T004_Valor_Desconto_Unitario}', ";
    $cClausula .= " '{$T004_Percentual_Desconto}', ";
    $cClausula .= " '{$T004_Valor_Desconto_Unitario}', ";
    $cClausula .= " '{$T004_Valor_Preco_Unitario}/{$mT004['D009_Valor_Custo_Unitario']}', ";
    $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
    $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
    if ($Aliquotas['D005_Id'] == '') {
        $cClausula .= " '{$T004_D005_Id}',";
    } else {
        $cClausula .= " '{$Aliquotas['D005_Id']}',";
    }
    $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
    $cClausula .= " '{$T004_D006_Id}',";
    //$cClausula .= " '{$mT004['Aliquota_IPI']}',";
    if ($T004_Aliquota_IPI <= 0) {
        $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
    } else {
        $cClausula .= " '{$T004_Aliquota_IPI}',";
    }
    $cClausula .= " '{$Aliquotas['Aliquota_ICMS']}',";
    $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS']}',";
    $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
    $cClausula .= " '{$Aliquotas['ST_VA']}',";
    $cClausula .= " '{$Aliquotas['Flag_ST']}',";
    $cClausula .= " '{$Aliquotas['Flag_Situacao_Tributaria']}',";
    $cClausula .= " '{$Aliquotas['ICMS_Inter_Estadual']}',";
    $cClausula .= " '{$Aliquotas['ST_ICMS_Interno']}',";
    $cClausula .= " '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
    $cClausula .= " '{$Aliquotas['ST_ICMS_Interno_MVA']}',";
    $cClausula .= " '{$Aliquotas['Flag_Isento_PIS']}',";
    $cClausula .= " '{$Aliquotas['Aliquota_PIS']}',";
    $cClausula .= " '{$Aliquotas['Situacao_Tributaria_PIS']}',";
    $cClausula .= " '{$Aliquotas['Flag_Isento_COFINS']}',";
    $cClausula .= " '{$Aliquotas['Aliquota_COFINS']}', ";
    $cClausula .= " '',";
    $cClausula .= " '{$Aliquotas['Situacao_Tributaria_COFINS']}',";
    $cClausula .= " '{$D088_Codigo_ST}',";
    $cClausula .= " '{$Aliquotas['D059_Id']}',";
    $cClausula .= " '$T004_Observacao', ";
    $cClausula .= " '$T004_Numero_Pedido_Compra', ";
    
    if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
        $cClausula .= " '{$T004_Item_Pedido_Compra}', ";
        $cClausula .= " '{$T004_Valor_Preco_Unitario}', ";
    } else {
        $cClausula .= " '{$T004_Item_Pedido_Compra}', ";
    }
    $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
    $cClausula .= " '{$T004_Percentual_Trib_Transparencia}', ";
    $cClausula .= " '{$mT004['D001_Flag_Pre_Cadastro']}', ";
    $cClausula .= " '{$T004_D144_Id}', ";
    $cClausula .= " '{$mT004['D049_Origem_Mercadoria']}', ";
    $cClausula .= " '{$demonstrativo}', ";
    $cClausula .= " '{$demonstrativoT001}', ";
    $cClausula .= " '{$T004_Observacao_Entrega}', ";
    $cClausula .= " '{$T004_Flag_Destino_Produto}' ";
    $cClausula .= " ); ";
    mysql_query($cClausula);
    $T004_Id = $g['mysqlLastId'];

    $cErro   = mysql_error();
    if (!empty($cErro)) {
        return "Erro: $cErro<br />$cClausula";
    } else {
        if (!empty($T004_Codigo_Substituto)) {
        	/*
            $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor FROM D083 WHERE D083_D001_Id='{$mT004['D001_Id']}' AND D083_D049_Id='{$mT004['D049_Id']}' AND D083_D024_Id='$D024_Id'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mT004['D049_Id']}','{$mT004['D001_Id']}','$D024_Id',curdate(),'S','$T004_Codigo_Substituto')");
            } else {
                $mD083 = mysql_fetch_array($D083);
                mysql_query("UPDATE D083 SET D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$T004_Codigo_Substituto' WHERE D083_Id='{$mD083['D083_Id']}'");
            }
            */
            require_once("bibliotecas/classes/CAD002.php");
			$CAD002 = new CAD002();
			$CAD002->definirCodigoFornecedor($mT004['D009_Id'], $D024_Id, $T004_Codigo_Substituto, '', true, false);
        }
        //Atualiza as aliquotas com o D006 correto do T004
        $VEN001->atualizarAliquotas($T003_Id, false, $T004_Id, null);
        $cSql = mysql_query("call T004_Gravar_Totalizacao_4('{$T004_Id}');");
        $cSql = mysql_query("call T003_Gravar_Totalizacao_4('{$T003_Id}');");  
    }
    
    mysql_query("UPDATE T004 SET T004_Valor_Preco_Bruto=T004_Valor_Preco_Bruto WHERE T004_Id='$T004_Id'");
    
    return true;
}

function incluirProdutoNotaVenda($T007_Id, $T008_D009_Id, $preco, $quantidade, $T066_Id = false) {
    global $g;
    require_once('bibliotecas/classes/CAD002.php');
    $CAD002 = new CAD002();
    require_once('bibliotecas/classes/VEN001.php');
    $VEN001 = new VEN001();

    $resultado = mysql_query("SELECT * FROM T007 LEFT JOIN D024 ON D024_Id = T007_D024_Id WHERE T007_Id = '{$T007_Id}'");
    $mT007   = mysql_fetch_assoc($resultado);

 	$clausulaWhere = "";
    if($quantidade != 0){
 		$clausulaWhere = " AND T066_Id = '{$T066_Id}'";
 	}

    $sql = <<<EOT
        SELECT * FROM D001
            LEFT JOIN D049 ON D049_D001_Id=D001_Id
            LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT007['T007_D024_Id']}'
            LEFT JOIN D009 ON D009_D049_Id=D049_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D082 ON D082_Id=D049_D082_Id
            LEFT JOIN D041 ON D041_D049_Id=D049_Id
            LEFT JOIN D005 ON D005_Id=D001_D005_Id
            LEFT JOIN D037 ON D037_Id=D001_D037_Id
            LEFT JOIN T066 ON D009_Id=T066_D009_Id
            LEFT JOIN D069 on D069_Id=D009_D069_Id AND D069_Flag_Tabela_Principal='S'
            WHERE D009_Id = '{$T008_D009_Id}'
            {$clausulaWhere}
EOT;
            
    $resultado = mysql_query($sql);
    $row       = mysql_fetch_assoc($resultado);

    $Aliquotas = $VEN001->retornaAliquotas($T008_D009_Id, $T003_Id=false, $T005_Id=false, $T007_Id);
	$D006_Id = $VEN001->retornaCFOP($mT007['T007_D024_Id'], $T008_D009_Id, $mT007['T007_C004_Id'], false, false, false, false, $mT007['T007_C004_Id_Estoque']);
	$D006 = mysql_query("SELECT * FROM D006 WHERE D006_Id='{$D006_Id}'");
	$mD006 = mysql_fetch_array($D006);

 	$T008_T066_Id = $row['T066_Id'];

    if($quantidade == 0) {
 		$T008_Quantidade = 0;
 		$T008_T066_Id = 0;
 	} else {
 		$T008_Quantidade = $row['T066_Quantidade_Estoque_Liquido'];
 	}

    switch ($preco) {
 		case 0:
 			$T008_Valor_Preco_Unitario = 0;
 		break;
 		case 1:
 			$T008_Valor_Preco_Unitario = $row['D009_Valor_Custo_Unitario'];

 			if(isset($row['D009_T001_Id']) && $row['D009_T001_Id'] > 0){
 				$T008_Valor_Preco_Unitario = $T008_Valor_Preco_Unitario - $row['T001_Valor_ST'];
 			} else if(isset($row['D009_D069_Id']) && $row['D009_D069_Id']){
 				$T008_Valor_Preco_Unitario = $T008_Valor_Preco_Unitario - $row['D069_Valor_ICMS_Substituicao_Retencao'];
 			}
 		break;
 		case 2:
 			$T008_Valor_Preco_Unitario = $row['D009_Preco_Loja'];
 		break;
 		case 3:
 			$dadosPrecoVenda 		   = $CAD002->retornaPrecoVenda($T008_D009_Id, $mT007['T007_Flag_Lista_Preco'], $mT007['D024_Id'], false, $D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
 			$T008_Valor_Preco_Unitario = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
 		break;
 	}

    $cClausula = "INSERT INTO T008 (";
    $cClausula .= "T008_C004_Id,";
    $cClausula .= "T008_T007_Id,";
    $cClausula .= "T008_D006_Id,";
    $cClausula .= "T008_D037_Id,";
    $cClausula .= "T008_D059_Id,";
    $cClausula .= "T008_Unidade,";
    $cClausula .= "T008_Codigo_CFOP,";
    $cClausula .= "T008_Peso_Unitario,";
    $cClausula .= "T008_Peso_Bruto,";
    $cClausula .= "T008_D005_Id,";
    $cClausula .= "T008_Classificacao_Fiscal,";
    $cClausula .= "T008_Quantidade,";
    $cClausula .= "T008_Codigo_Produto,";
    $cClausula .= "T008_Codigo_Barras,";
    $cClausula .= "T008_Codigo_Substituto,";
    $cClausula .= "T008_D009_Id,";
    $cClausula .= "T008_Descricao_Produto,";
    $cClausula .= "T008_Valor_Custo_Unitario,";
    $cClausula .= "T008_Valor_Preco_Sem_Desconto_Unitario,";
    $cClausula .= "T008_Valor_Preco_Original,";
    $cClausula .= "T008_Flag_Calculo_Manual, ";
    $cClausula .= "T008_Cubagem_Unitaria,";
    $cClausula .= "T008_Flag_Destino_Produto,";
    $cClausula .= "T008_T066_Id";
    $cClausula .= ") VALUES (";
    $cClausula .= "'{$g['empresaAtual']}',";
    $cClausula .= "'{$T007_Id}',";
    $cClausula .= "'{$D006_Id}',";
    $cClausula .= "'{$row['D037_Id']}',";
    $cClausula .= "'{$Aliquotas['D059_Id']}',";
    $cClausula .= "'{$row['D037_Unidade']}',";
    $cClausula .= "'{$mD006['D006_Codigo_CFOP']}',";
    $cClausula .= "'{$row['D001_Peso_Unitario_Kg']}',";
    $cClausula .= "'{$row['D001_Peso_Unitario_Bruto']}',";
    $cClausula .= "'{$row['D005_Id']}',";
    $cClausula .= "'{$row['D005_Classificacao_Fiscal']}',";
    $cClausula .= "'{$T008_Quantidade}',";
    $cClausula .= "'{$row['D001_Codigo_Produto']}',";
    $cClausula .= "'{$row['D001_Codigo_Barras']}',";
    $cClausula .= "'{$T008_Codigo_Substituto}',";
    $cClausula .= "'{$T008_D009_Id}',";
    $cClausula .= "'{$row['D001_Descricao_Produto']}',";
    $cClausula .= "'{$row['D009_Valor_Custo_Unitario']}',";
    $cClausula .= "'{$T008_Valor_Preco_Unitario}',";
    $cClausula .= "'{$T008_Valor_Preco_Unitario}',";
    $cClausula .= "'N', ";
    $cClausula .= "'{$row['D001_Cubagem_Unitaria']}',";
    $cClausula .= "'{$mT007['T007_Flag_Destino_Produto']}',";
    $cClausula .= "'{$T008_T066_Id}'";
    $cClausula .= ")";
    mysql_query($cClausula);
    $cErro   = mysql_error();
    $T008_Id = $g['mysqlLastId'];
    if (!empty($cErro)) {
        return ("Erro:$cErro<br />$cClausula");
    }
    
    $cSql = mysql_query("call T008_Gravar_Totalizacao_4('$T008_Id');");
    $cSql = mysql_query("call T007_Gravar_Totalizacao_4('$T007_Id');");
    return true;
}