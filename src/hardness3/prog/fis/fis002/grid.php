<?php
namespace hardness;

require_once('prog/fis/fis002/grid_func-callback.php');
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'fis002GridIcms':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'D053_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D053_Aliquota_ICMS_1', 'colunaTitulo' => 'ICMS 1', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Aliquota_ICMS_2', 'colunaTitulo' => 'ICMS 2', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Aliquota_ICMS_Importado', 'colunaTitulo' => 'ICMS importado 1', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Aliquota_ICMS_Importado_2', 'colunaTitulo' => 'ICMS importado 2', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Flag_ST', 'colunaTitulo' => 'ST', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Interno', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_ST_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS Inter-Estadual', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Inscricao_Estadual_ST', 'colunaTitulo' => 'Insc. Estadual do Subst. Trib.', 'colunaEditar' => true),
            array('colunaValor' => 'D053_Percentual_FCP', 'colunaTitulo' => 'Perc. FCP', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'extra' => 'title="Fundo de Combate a Pobreza"'),
            array('colunaValor' => 'D053_Calculo_ST_Por_Dentro', 'colunaTitulo' => 'ST por dentro?', 'colunaEditar' => true, 'colunaEditarTodos' => true),
			array('colunaValor' => 'D053_Calculo_Difal_Por_Dentro', 'colunaTitulo' => 'Difal por dentro?', 'colunaEditar' => true, 'colunaEditarTodos' => true),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D018';
		$extra = <<<EOT
		left join D053 on D053_D018_Id=D018_Id
		WHERE D053_C004_Id='{$g['empresaAtual']}'
		order by D018_UF
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<div style="width:90%;border:1px solid #000;background:#fff;padding:3px;border-radius:3px;">
					<font size="2px"><b>Legenda:</b></font><br />
					<b>ICMS 1:</b> Pessoa física ou juridica sem inscrição estadual<br />
					<b>ICMS 2:</b> Pessoa física ou juridica com inscrição estadual<br />
					<b>ICMS Importado 1:</b> Pessoa física ou juridica sem inscrição estadual, produto importado e operação inter-estadual<br />
					<b>ICMS Importado 2:</b> Pessoa juridica com inscrição estadual, produto importado e operação inter-estadual<br />
					<b>ST:</b> Define se o estado possui ST<br />
					<b>ICMS Interno:</b> Cálculo de ST<br />
					<b>ICMS Inter-Estadual:</b> Cálculo de ST<br />
					<b>Insc. Estadual do Subst. Trib.:</b> Inscrição estadual do substituto tributário - Será incluída esta informação no XML da NF-e<br />
				</div>
				<div style="clear:both;"></div>
			</div>
EOT;
	
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D053_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'ICMS - ID: ' + acaoId, '/fis/fis002/content/fis002ContentICMSIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [600,400]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		$primeiraLinhaGridHead = "<th colspan=\"2\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: left; z-index: 2;\">&nbsp;</th>";
		$primeiraLinhaGridHead .= "<th colspan=\"4\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">ICMS NORMAL</th>";
		$primeiraLinhaGridHead .= "<th colspan=\"3\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">ICMS POR SUBSTITUIÇÃO TRIBUTÁRIA</th>";
		$primeiraLinhaGridHead .= "<th colspan=\"4\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">&nbsp;</th>";

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		/**
		* Botões
		* Obs.: os IDs dos botões devem possuir nomes únicos
		* Obs2.: os botões são 'movidos' automaticamente para a parte inferior do elemente TAB caso fornecido um ID (botoesId = divId)
		*/
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad015GridCentroCustosBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Nova Tela de Cadastro X', '/fis/fis003/content/fis003ContentNCMIncluir/', '&tab=geral', [500,320]);
			});
		} </script></div>
EOT;
	break;

	case 'fis002GridIcmsCompra':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'D053_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D053_Aliquota_ICMS_Compra', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Aliquota_ICMS_Importado_Compra', 'colunaTitulo' => 'ICMS importado', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Flag_ST_Compra', 'colunaTitulo' => 'ST', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_ST_ICMS_Interno_Compra', 'colunaTitulo' => 'ICMS Interno', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_ST_ICMS_Inter_Estadual_Compra', 'colunaTitulo' => 'ICMS Inter-Estadual', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
			array('colunaValor' => 'D053_Calculo_ST_Por_Dentro_Compra', 'colunaTitulo' => 'ST por dentro', 'colunaEditar' => true),
			array('colunaValor' => 'D053_Percentual_FCP_Compra', 'colunaTitulo' => 'Perc. FCP', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D018';
		$extra = <<<EOT
		left join D053 on D053_D018_Id=D018_Id
		WHERE D053_C004_Id='{$g['empresaAtual']}'
		order by D018_UF
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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
		gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		/**
		* Botões
		* Obs.: os IDs dos botões devem possuir nomes únicos
		* Obs2.: os botões são 'movidos' automaticamente para a parte inferior do elemente TAB caso fornecido um ID (botoesId = divId)
		*/
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad015GridCentroCustosBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Nova Tela de Cadastro X', '/fis/fis003/content/fis003ContentNCMIncluir/', '&tab=geral', [500,320]);
			});
		} </script></div>
EOT;
	break;

	case 'fis002GridIcmsImportado':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'D053_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D053_Aliquota_ICMS_Importacao', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Aliquota_ICMS_Presumido_Importacao', 'colunaTitulo' => 'ICMS presumido', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Percentual_Reducao_ICMS_Importacao', 'colunaTitulo' => 'Red. ICMS', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Percentual_Reducao_ICMS_Presumido_Importacao', 'colunaTitulo' => 'Red. ICMS Presumido', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D053_Aliquota_ICMS_PIS_COFINS_Importacao', 'colunaTitulo' => 'ICMS PIS/COFINS', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
			array('colunaValor' => 'D053_Aliquota_ICMS_Credito_Presumido_Importacao', 'colunaTitulo' => 'ICMS crédito presumido', 'colunaCasasDecimais' => '2', 'colunaEditar' => true),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D018';
		$extra = <<<EOT
		left join D053 on D053_D018_Id=D018_Id
		WHERE D053_C004_Id='{$g['empresaAtual']}'
		order by D018_UF
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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
		gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		/**
		* Botões
		* Obs.: os IDs dos botões devem possuir nomes únicos
		* Obs2.: os botões são 'movidos' automaticamente para a parte inferior do elemente TAB caso fornecido um ID (botoesId = divId)
		*/
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad015GridCentroCustosBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Nova Tela de Cadastro X', '/fis/fis003/content/fis003ContentNCMIncluir/', '&tab=geral', [500,320]);
			});
		} </script></div>
EOT;
	break;

	case 'fis002GridExcessoesIcms':
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D143_Id', $g['divId'])),
 			array('colunaValor' => 'D143_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
 			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
 			array('colunaValor' => 'D006_Descricao', 'colunaTitulo' => 'Descrição'),
 			array('colunaValor' => 'D143_Pessoa_Fisica_Juridica', 'colunaTitulo' => 'Pessoa', 'callback'=>'campoPessoa'),
			array('colunaValor' => 'D143_C004_Codigo_Regime_Tributario', 'colunaTitulo' => 'Reg. Tributário',  'callback'=>'campoRegimeTributario'),
 			array('colunaValor' => 'D143_D024_Flag_Contribuinte', 'colunaTitulo' => 'Tipo Contribuinte', 'callback'=>'campoTipoContribuinte'),
 			array('colunaValor' => 'D143_Dentro_Fora_Estado', 'colunaTitulo' => 'Dentro/Fora Estado', 'callback'=>'campoDentroForaEstado'),
 			array('colunaValor' => 'D143_Estados_Especificos', 'colunaTitulo' => 'Estados Específicos'),
 			array('colunaValor' => 'D143_UF_Tem_ST', 'colunaTitulo' => 'UF tem ST?', 'callback'=>'campoUFTemST'),
 			array('colunaValor' => 'D143_NCM_Tem_ST', 'colunaTitulo' => 'NCM tem ST?', 'callback'=>'campoNCMTemST'), 			
			array('colunaValor' => 'D143_Produzido_Adquirido', 'colunaTitulo' => 'Tipo Produto', 'callback'=>'campoTipo'),
			array('colunaValor' => 'D143_Flag_Cabecalho_Item', 'colunaTitulo' => 'Tipo Regra', 'callback'=>'campoTipoRegra'),
			array('colunaValor' => 'D143_Flag_Inativar', 'colunaTitulo' => 'Inativar', 'callback'=>'campoAtiva'),
			);
					
		/**
		 * Definição da SQL
		 */
		$from = "D143";
		
		$extra = <<<EOT
		left join D006 on D006_Id=D143_D006_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="fis001Incluir">Incluir</button>
			
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#fis001Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Excessão ICMS', '/fis/fis002/content/fis002ContentICMSIncluirExcessao/','', [500,700]);
			});
		} </script></div>
EOT;
					
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D006_Codigo_CFOP' => array('titulo' => 'Código CFOP', 'tipo' => 'numero'),
			'D006_Descricao' => array('titulo' => 'Descrição CFOP', 'tipo' => 'texto'),
			//'D143_Pessoa_Fisica_Juridica' => array('titulo' => 'Pessoa (F/J)', 'tipo' => 'texto'),
			'D143_Pessoa_Fisica_Juridica' => array('titulo' => 'Pessoa', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Física', 'value' => 'F'),
				array('title' => 'Jurídica', 'value' => 'J'),
			), 'naoGerarExtra' => false),
			//'D143_Flag_Cabecalho_Item' => array('titulo' => 'Tipo Regra(C/I)', 'tipo' => 'texto'),
			'D143_Flag_Cabecalho_Item' => array('titulo' => 'Tipo Regra', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Cabeçalho', 'value' => 'C'),
				array('title' => 'Item', 'value' => 'I'),
			), 'naoGerarExtra' => false),
			//'D143_Produzido_Adquirido' => array('titulo' => 'Tipo Produto (P/A)', 'tipo' => 'texto'),
			'D143_Produzido_Adquirido' => array('titulo' => 'Tipo Produto', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Produzido', 'value' => 'P'),
				array('title' => 'Adquirido', 'value' => 'A'),
			), 'naoGerarExtra' => false),
			'D143_C004_Codigo_Regime_Tributario' => array('titulo' => 'Regime Tributário', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Simples', 'value' => '1'),
				array('title' => 'Normal', 'value' => '3'),				
			), 'naoGerarExtra' => false),			
		
		
		
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
					<span class="item" style="text-align:right">Regras<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;

			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D143_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Regra ID: ' + id, '/fis/fis002/content/fis002ContentICMSIncluirExcessao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,700]);
			});
		} </script></div>
EOT;
		
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

	case 'fis002GridIcmsTransporte':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'D196_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'Origem.D018_UF', 'colunaTitulo' => 'UF Origem'),
            array('colunaValor' => 'Destino.D018_UF', 'colunaTitulo' => 'UF Destino'),
            array('colunaValor' => 'D196_Aliquota_ICMS_Interno', 'colunaTitulo' => 'ICMS Interno', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D196_Aliquota_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS Inter-Estadual', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true)
        );

		/**
		* Definição da SQL
		*/
		$from = 'D196';
		$extra = <<<EOT
		left join D018 as Origem on Origem.D018_Id=D196_D018_Id_Origem
		left join D018 as Destino on Destino.D018_Id=D196_D018_Id_Destino
		where D196_C004_Id='{$g['empresaAtual']}'		
EOT;

		/**
		 * Botões
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis002IncluirICMS_TransporteBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fis002IncluirICMS_TransporteBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir ICMS Transporte', '/fis/fis002/content/fis002ContentICMSTransporte/', '&tab=geral', [630,300]);
				});
			} </script></div>
		EOT;

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'Origem.D018_UF' => array('titulo' => 'UF Origem', 'tipo' => 'texto'),
			'Destino.D018_UF' => array('titulo' => 'UF Destino', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	
		$novoIdTelaForm = uniqid();	
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D196_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar ICMS Transporte - ID: ' + id, '/fis/fis002/content/fis002ContentICMSTransporte/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [920,590]);
				});
			} </script></div>
		EOT;
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
}

?>
