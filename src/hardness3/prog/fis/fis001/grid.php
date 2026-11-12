<?php
namespace hardness;

require_once('prog/fis/fis001/grid_func-callback.php');

switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'fis001GridRegras':
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D137_Id', $g['divId'])),
            array('colunaValor' => 'D137_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => "IF(D006_Flag_Inativo='S' AND D137_Flag_Inativar='N',CONCAT(D006_Descricao,'<br>*** CFOP está inativa.'),D006_Descricao)", 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D137_Quantidade_Regras(D137_Id)', 'colunaTitulo' => 'Prioridade', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D137_Pessoa_Fisica_Juridica', 'colunaTitulo' => 'Pessoa', 'callback' => 'campoPessoa'),
            array('colunaValor' => 'D137_C004_Codigo_Regime_Tributario', 'colunaTitulo' => 'Reg. Tributário', 'callback' => 'campoRegimeTributario'),
            array('colunaValor' => 'D137_D024_Flag_Contribuinte', 'colunaTitulo' => 'Tipo Contribuinte', 'callback' => 'campoTipoContribuinte'),
            array('colunaValor' => 'D137_Dentro_Fora_Estado', 'colunaTitulo' => 'Dentro/Fora Estado', 'callback' => 'campoDentroForaEstado'),
            array('colunaValor' => 'D137_Estados_Especificos', 'colunaTitulo' => 'Estados Específicos'),
            array('colunaValor' => 'D137_UF_Tem_ST', 'colunaTitulo' => 'UF tem ST?', 'callback' => 'campoUFTemST'),
            array('colunaValor' => 'D137_NCM_Tem_ST', 'colunaTitulo' => 'NCM tem ST?', 'callback' => 'campoNCMTemST'),
            array('colunaValor' => 'D137_Produzido_Adquirido', 'colunaTitulo' => 'Tipo Produto', 'callback' => 'campoTipo'),
            array('colunaValor' => 'D137_Flag_Cabecalho_Item', 'colunaTitulo' => 'Tipo Regra', 'callback' => 'campoTipoRegra', 'colunaExibir' => false),
            array('colunaValor' => 'D137_Flag_Destino_Produto', 'colunaTitulo' => 'Destino', 'callback' => 'campoRegraDestino'),
			array('colunaValor' => 'D137_Flag_Inativar', 'colunaTitulo' => 'Inativar', 'callback' => 'campoAtiva'),
			array('colunaValor' => 'D006_Flag_Inativo', 'colunaTitulo' => 'CFOP Inativa','colunaExibir' => false),
        );
					
		/**
		 * Definição da SQL
		 */
		$from = "D137";
		
		$extra = <<<EOT
		left join D006 on D006_Id=D137_D006_Id
EOT;

			/**
			* Pode ser definido uma classe para cada linha do grid
			*/
			function gridLinha($array) {
				global $g;
				if(($array['todosCampos']['D137_Flag_Inativar']=='N') && ($array['todosCampos']['D006_Flag_Inativo']=='S')){
					return 'linhaVermelho';
				}else if($array['todosCampos']['D137_Flag_Inativar']=='S'){
					return 'linhaCinza';
				}
			}

			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis001Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Regra CFOP', '/fis/fis001/content/fis001content03/','', [800,700]);
				});
			} </script></div>
EOT;
					
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'numero'),
            'D006_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D137_Pessoa_Fisica_Juridica' => array('titulo' => 'Pessoa', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Física', 'value' => 'F'),
                array('title' => 'Jurídica', 'value' => 'J'),
              ),
              'naoGerarExtra' => ''),
            'D137_Flag_Cabecalho_Item' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Cabeçalho', 'value' => 'C'),
                array('title' => 'Item', 'value' => 'I'),
              ),
              'naoGerarExtra' => ''),
            'D137_Produzido_Adquirido' => array('titulo' => 'Produto', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Produzido', 'value' => 'P'),
                array('title' => 'Adquirido', 'value' => 'A'),
              ),
              'naoGerarExtra' => ''),
            'D137_C004_Codigo_Regime_Tributario' => array('titulo' => 'Regime', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Simples', 'value' => '1'),
                array('title' => 'Normal', 'value' => '3'),
              ),
			  'naoGerarExtra' => ''),
			 'D137_Flag_Inativar' => array('titulo' => 'Inativo?(S/N)', 'tipo' => 'texto'),
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
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D137_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Regra ID: ' + id, '/fis/fis001/content/fis001content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,700]);
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

	/**
	* Definição do Grid (Tabela)
	*/
	case 'fis001GridRegrasCompras':
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D145_Id', $g['divId'])),
            array('colunaValor' => 'D145_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'CFOP Id'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => "IF(D006_Flag_Inativo='S' AND D145_Flag_Inativar='N',CONCAT(D006_Descricao,'<br>*** CFOP está inativa.'),D006_Descricao)", 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D145_Quantidade_Regras(D145_Id)', 'colunaTitulo' => 'Regras', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D145_Flag_Cabecalho_Item', 'colunaTitulo' => 'Tipo Regra', 'callback' => 'campoTipoRegraCompras', 'colunaExibir' => false),
            array('colunaValor' => 'D145_Pessoa_Fisica_Juridica', 'colunaTitulo' => 'Pessoa', 'callback' => 'campoPessoaCompras'),
            array('colunaValor' => 'D145_C004_Codigo_Regime_Tributario', 'colunaTitulo' => 'Reg. Tributário', 'callback' => 'campoRegimeTributarioCompras'),
            array('colunaValor' => 'D145_D024_Flag_Contribuinte', 'colunaTitulo' => 'Tipo Contribuinte', 'callback' => 'campoTipoContribuinteCompras'),
            array('colunaValor' => 'D145_Dentro_Fora_Estado', 'colunaTitulo' => 'Dentro/Fora Estado', 'callback' => 'campoDentroForaEstadoCompras'),
            array('colunaValor' => 'D145_Estados_Especificos', 'colunaTitulo' => 'Estados Específicos'),
            array('colunaValor' => 'D145_Origem_Mercadoria', 'colunaTitulo' => 'Origem produto'),
            array('colunaValor' => 'D145_UF_Tem_ST', 'colunaTitulo' => 'UF tem ST?', 'callback' => 'campoUFTemSTCompras'),
            array('colunaValor' => 'D145_NCM_Tem_ST', 'colunaTitulo' => 'NCM tem ST?', 'callback' => 'campoNCMTemSTCompras'),
            array('colunaValor' => 'D145_Produzido_Adquirido', 'colunaTitulo' => 'Tipo Produto', 'callback' => 'campoTipoCompras'),
			array('colunaValor' => 'D145_Flag_Inativar', 'colunaTitulo' => 'Inativar', 'callback' => 'campoAtivaCompras'),
			array('colunaValor' => 'D145_Flag_Destino_Produto', 'colunaTitulo' => 'Destino', 'callback' => 'campoRegraDestino'),
			array('colunaValor' => 'D006_Flag_Inativo', 'colunaTitulo' => 'CFOP Inativa','colunaExibir' => false),
        );
					
		/**
		 * Definição da SQL
		 */
		$from = "D145";
		
		$extra = <<<EOT
			left join D006 on D006_Id=D145_D006_Id
EOT;

			/**
			* Pode ser definido uma classe para cada linha do grid
			*/
			function gridLinha($array) {
				global $g;
				if(($array['todosCampos']['D145_Flag_Inativar']=='N') && ($array['todosCampos']['D006_Flag_Inativo']=='S')){
					return 'linhaVermelho';
				}else if($array['todosCampos']['D145_Flag_Inativar']=='S'){
					return 'linhaCinza';
				}
			}
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis001IncluirCompras">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis001IncluirCompras').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Regra CFOP para Compras', '/fis/fis001/content/fis001content03Compras/','', [600,650]);
				});
			} </script></div>
EOT;
					
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D006_Codigo_CFOP' => array('titulo' => 'Código CFOP', 'tipo' => 'numero'),
			'D006_Descricao' => array('titulo' => 'Descrição CFOP', 'tipo' => 'texto'),
			//'D145_Pessoa_Fisica_Juridica' => array('titulo' => 'Pessoa (F/J)', 'tipo' => 'texto'),
			'D145_Pessoa_Fisica_Juridica' => array('titulo' => 'Pessoa', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Física', 'value' => 'F'),
				array('title' => 'Jurídica', 'value' => 'J'),
			), 'naoGerarExtra' => false),
			//'D145_Flag_Cabecalho_Item' => array('titulo' => 'Tipo Regra(C/I)', 'tipo' => 'texto'),
			'D145_Flag_Cabecalho_Item' => array('titulo' => 'Tipo Regra', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Cabeçalho', 'value' => 'C'),
				array('title' => 'Item', 'value' => 'I'),
			), 'naoGerarExtra' => false),
			//'D145_Produzido_Adquirido' => array('titulo' => 'Tipo Produto (P/A)', 'tipo' => 'texto'),
			'D145_Produzido_Adquirido' => array('titulo' => 'Tipo Produto', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Produzido', 'value' => 'P'),
				array('title' => 'Adquirido', 'value' => 'A'),
			), 'naoGerarExtra' => false),
			'D145_C004_Codigo_Regime_Tributario' => array('titulo' => 'Regime Tributário', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Simples', 'value' => '1'),
				array('title' => 'Normal', 'value' => '3'),				
			), 'naoGerarExtra' => false),
			'D145_Flag_Inativar' => array('titulo' => 'Inativo?(S/N)', 'tipo' => 'texto'),
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
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D145_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Regra CFOP para Compra ID: ' + id, '/fis/fis001/content/fis001content03Compras/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,650]);
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
