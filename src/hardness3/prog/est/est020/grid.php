<?php
namespace hardness;
// Classes
require_once('prog/est/est020/grid_func-callback.php');
require_once('prog/est/est020/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est020GridConhecimentos':		
		global $g;
		
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirCTe'),
			array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'font-size:10px', 'colunaExibir' => true),
			array('colunaValor' => 'T084_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T084_C007_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T084_D022_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T084_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T084_D006_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T085_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'CT-e', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T084_Serie', 'colunaTitulo' => 'Serie', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),												
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),			
			array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),			
			array('colunaValor' => 'T084_Valor_ICMS', 'colunaTitulo' => 'Valor ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),            
			array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'colunaTipo' => 'livre', 'callback' => 'validaCampoXML'),						
			array('colunaValor' => 'DACTE', 'colunaTitulo' => 'DACTE', 'colunaTipo' => 'livre', 'callback' => 'imprimirDACTE'),						
			array('colunaValor' => 'T084_Flag_Cancelado', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar'),			
			array('colunaValor' => 'T084_Flag_Denegado', 'colunaTitulo' => 'Denegado', 'colunaExibir' => false),			
			array('colunaValor' => 'T084_Numero_Protocolo_Cte', 'colunaTitulo' => 'Protocolo'),
			array('colunaValor' => 'T084_Chave_Acesso_Cte', 'colunaTitulo' => 'Chave de Acesso'),
			array('colunaValor' => 'T084_Numero_Protocolo_Cancelamento_Cte', 'colunaTitulo' => 'Prot. Cancelamento'),
		);
		

		function gridLinha($array) {
			if($array['todosCampos']['T084_Flag_Cancelado']=='S' || $array['todosCampos']['T084_Flag_Denegado'] == 'S'){
				return 'linhaVermelho';
			}
		}
		
		/**
		 * Definição da SQL
		 */
		$from = "T084";
		
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id=T084_C007_Id
			LEFT JOIN D022 ON D022_Id=T084_D022_Id
			LEFT JOIN D024 ON D024_Id=T084_D024_Id
			LEFT JOIN D020 ON D020_Id=D024_D020_Id
			LEFT JOIN D018 ON D018_Id=D020_D018_Id			
			LEFT JOIN D006 ON D006_Id=T084_D006_Id
			LEFT JOIN T085 ON T084_Id=T085_T084_Id
			LEFT JOIN T013 ON T013_Id=T085_T013_Id
			LEFT JOIN T007 ON T007_Id=T085_T007_Id
			WHERE (T084_C004_Id='{$g['empresaAtual']}')   		
			AND T084_Flag_Entrada_Saida = 'S'		
			GROUP BY T084_Id
EOT;

		/**
		 * Botões Almir
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est020IncluirConhecimentoBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est020IncluirConhecimentoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Conhecimento', '/est/est020/content/est020ContentIncluirConhecimento/', '&tab=geral&acao=incluir', [800,500]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T084_Numero_Conhecimento' => array('titulo' => 'Numero CT-e', 'tipo' => 'numero'),
			'T085_Chave_Acesso_Nfe' => array('titulo' => 'Chave Acesso NFe', 'tipo' => 'texto'),			
			'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),		
			'T084_Flag_Cancelado' => array('titulo' => 'Cancelado','tipo' => 'select',
				'select' => array(	
								array('title' => 'TODOS','value' => ''),
								array('title' => 'SIM','value' => 'S'),
								array('title' => 'NÃO','value' => 'N'))),	
			'T084_Flag_Denegado' => array('titulo' => 'Denegado','tipo' => 'select',
				'select' => array(								
							array('title' => 'TODOS','value' => ''),
							array('title' => 'SIM','value' => 'S'),
							array('title' => 'NÃO','value' => 'N'))),	
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total', 'valor' => 'T084_Valor_Conhecimento', 'titulo' => 'Valor total'),
			array('nome' => 'Valor_ICMS', 'valor' => 'T084_Valor_ICMS', 'titulo' => 'Valor ICMS'),			
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
				var id = $(this).closest('tr').attr('id').replace(/T084_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Conhecimento - ID: ' + id, '/est/est020/content/est020ContentIncluirConhecimento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [920,590]);
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
	
	case 'est020GridDocumentos':		
		global $g;		

		$T084_Id = isset($r_acaoId) ? $r_acaoId : false;
		$campoParent = 'T084';
		$valorParent = $T084_Id;

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaExcluirRegistro', 'callbackParameter' => array('T085_Id', $g['divId'], $campoParent, $valorParent), 'colunaExibir' => true),
            array('colunaValor' => 'T085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T085_C004_Origem_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T085_C004_Origem_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T085_Flag_Tipo_Documento', 'colunaTitulo' => 'Tipo Documento', 'callback' => 'validaCampoTipoDocumento'),
            array('colunaValor' => 'T085_Numero_Nota_Fiscal', 'colunaTitulo' => 'Documento'),
            array('colunaValor' => 'T085_Data_Emissao', 'colunaTitulo' => 'Data Emissão', 'callback'=> 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T085_Data_Prevista_Entrega', 'colunaTitulo' => 'Previsão Entrega', 'callback'=> 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T085_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave NF-e'),
            array('colunaValor' => 'T084_Flag_Travar', 'colunaExibir' => false),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T085";
		
		$extra = <<<EOT
		LEFT JOIN T007 ON T085_T007_Id=T007_Id
		LEFT JOIN T013 ON T085_T013_Id=T013_Id
		LEFT JOIN T084 ON T085_T084_Id=T084_Id
		WHERE T085_T084_Id='{$T084_Id}'
EOT;

		/**
		 * Botões Almir
		 */
		$botoes = array(
			array('titulo' => 'Incluir Documento', 'id' => 'est020IncluirDocumentoBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est020IncluirDocumentoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Documento - NF-e', '/est/est020/content/est020ContentIncluirDocumento/', '&tab=geral' + '&T084_Id=' + encodeURIComponent('{$r_acaoId}') + '&T085_C004_Origem_Id=' + encodeURIComponent('0'), [446,130]);
				});

			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array();
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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
			acaoClickLinha('{$g['divId']}', function(data, array) {
				if(array['todosCampos']['T085_Flag_Tipo_Documento'] == '2') {
					abrirJanela(data, array['divIdRoot'],  array['divId'] ,unique(), '', 'Editar Documento - NF-e', '/est/est020/content/est020ContentIncluirNfeCte/', '&acaoId='	+ encodeURIComponent(array['todosCampos']['T085_Id']) + '&T084_Id=' + encodeURIComponent('{$r_acaoId}')
																																																										+ '&T085_C004_Origem_Id=' + encodeURIComponent(array['todosCampos']['T085_C004_Origem_Id']), [500,300]);
				} else if(array['todosCampos']['T085_Flag_Tipo_Documento'] == '3') {
					abrirJanela(data, array['divIdRoot'],  array['divId'] ,unique(), '', 'Editar Documento - Outros', '/est/est020/content/est020ContentIncluirOutroDocumento/', '&acaoId='	+ encodeURIComponent(array['todosCampos']['T085_Id']) + '&T084_Id=' + encodeURIComponent('{$r_acaoId}'), [500,300]);
				}

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
	case 'est020GridQuantidadeCarga':		
		global $g;		

		$T084_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		$campoParent = 'T084';
		$valorParent = $acaoId;		

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaExcluirRegistro', 'callbackParameter' => array('T305_Id', $g['divId'])),
            array('colunaValor' => 'T305_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),            			            
            array('colunaValor' => 'T305_Codigo_Unidade', 'colunaTitulo' => 'Unidade Medida', 'callback' => 'campoUnidade'),            
            array('colunaValor' => 'T305_Tipo_Medida', 'colunaTitulo' => 'Tipo da Medida'),            
            array('colunaValor' => 'T305_Quantidade', 'colunaTitulo' => 'Quantidade da Carga','colunaCasasDecimais' => 4),            
			array('colunaValor' => 'T084_Flag_Travar', 'colunaExibir' => false),                
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T305";
		
		$extra = <<<EOT
		LEFT JOIN T084 ON T084_Id=T305_T084_Id
		WHERE T305_T084_Id='{$T084_Id}'
EOT;

		/**
		 * Botões Almir
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est020IncluirQuantidadeCargaBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est020IncluirQuantidadeCargaBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Quantidade da Carga', '/est/est020/content/est020ContentIncluirQuantidadeCarga/', '&tab=geral' + '&T084_Id=' + encodeURIComponent('{$r_acaoId}') , [400,320]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array();
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T305_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');					
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Quantidade Carga - ID: ' + acaoId, '/est/est020/content/est020ContentIncluirQuantidadeCarga/', '&acaoId=' + encodeURIComponent(acaoId) + '&T084_Id=' + encodeURIComponent('{$r_acaoId}'), [500,250]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} 
			</script></div>
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

	case 'est020GridComponentePrestacao':		
		global $g;		

		$T084_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaExcluirRegistro', 'callbackParameter' => array('T304_Id', $g['divId'])),
            array('colunaValor' => 'T304_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),            			                        
            array('colunaValor' => 'T304_Nome_Componente', 'colunaTitulo' => 'Componente'),            
            array('colunaValor' => 'T304_Valor_Componente', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => 2),  
			array('colunaValor' => 'T084_Flag_Travar', 'colunaExibir' => false),                
          
		);
		   			            
		/**
		 * Definição da SQL
		 */
		$from = "T304";
		
		$extra = <<<EOT
		LEFT JOIN T084 ON T084_Id=T304_T084_Id
		WHERE T304_T084_Id='{$T084_Id}'
EOT;

		/**
		 * Botões Almir
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est020IncluirComponenteBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est020IncluirComponenteBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Componente da Prestação', '/est/est020/content/est020ContentIncluirComponente/', '&tab=geral' + '&T084_Id=' + encodeURIComponent('{$r_acaoId}') , [400,200]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array();
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T304_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');					
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Quantidade Carga - ID: ' + acaoId, '/est/est020/content/est020ContentIncluirComponente/', '&acaoId=' + encodeURIComponent(acaoId) + '&T084_Id=' + encodeURIComponent('{$r_acaoId}'), [400,200]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} 
			</script></div>
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

	case 'est020GridObservacoesContribuinte':		
		global $g;		

		$T084_Id = (isset($r_acaoId)) ? $r_acaoId : false;		

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaExcluirRegistro', 'callbackParameter' => array('T306_Id', $g['divId'])),
            array('colunaValor' => 'T306_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),            			                        
            array('colunaValor' => 'T306_Identificador', 'colunaTitulo' => 'Identificador'),        
            array('colunaValor' => 'T306_Conteudo', 'colunaTitulo' => 'Conteúdo'),   
			array('colunaValor' => 'T084_Flag_Travar', 'colunaExibir' => false),                     
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T306";
		
		$extra = <<<EOT
		LEFT JOIN T084 ON T084_Id=T306_T084_Id
		WHERE T306_T084_Id='{$r_acaoId}'
EOT;

		/**
		 * Botões Almir
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est020IncluirObservacaoContribuinteBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est020IncluirObservacaoContribuinteBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Observação Contribuinte', '/est/est020/content/est020ContentIncluirObservacaoContribuinte/', '&tab=geral' + '&T084_Id=' + encodeURIComponent('{$r_acaoId}') , [630,300]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array();
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T306_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');					
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Observação Contribuinte - ID: ' + acaoId, '/est/est020/content/est020ContentIncluirObservacaoContribuinte/', '&acaoId=' + encodeURIComponent(acaoId) + '&T084_Id=' + encodeURIComponent('{$r_acaoId}'), [630,300]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} 
			</script></div>
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
	
	case 'est020GridPagamentos':
		$acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		$sqlT084 = "SELECT T084_Pgto_Data, T084_Pgto_C007_Id, C007_Login FROM T007 LEFT JOIN C007 ON C007_Id=T007_Pgto_C007_Id WHERE T084_Id='$r_acaoId'";
		$resultT084 = mysql_query($sqlT084);
		$linhaT084 = mysql_fetch_array($resultT084);
		$dataGerada = gCorrigeData($linhaT084['T007_Pgto_Data']);
		$user = strtoupper($linhaT084['C007_Login']);
		if(($user != "") || (!empty($user))){
			echo "Parcelas Geradas por <b>".$user."</b> em <b>".$dataGerada."</b>";
		}
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T307_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor' => 'T307_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
			array('colunaValor' => 'T307_Prazos_Dias', 'colunaTitulo' => 'Prazos' ),
			array('colunaValor' => 'T307_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData'),			
			array('colunaValor' => 'ven003_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "diaSemana"),
			array('colunaValor' => 'T307_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela','colunaCasasDecimais' => 2),				
			array('colunaValor' => 'T307_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'order by D027_Portador', true), 'callback' => 'T307_D027_Id_Portador'),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
			array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),			
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T307';

		$extra = <<<EOT
			left join D027 on D027_Id=T307_D027_Id
			WHERE T307_T084_Id = {$acao_Id}
EOT;

	
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		
		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		$novoIdTelaForm = uniqid();
	break;

	case 'est020GridPagamentosTitulos':

		$acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		/**
		 * Definição do Grid (Tabela)
		 */		
			
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoT002'),
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => "T002_Numero_Documento", 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'T002_T035_Id',  'colunaExibir'=>false),
			array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'validaCampoExtratoCliente', 'colunaExibir'=>false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
			array('colunaValor' => "concat(T002_Numero_Parcela,'/',T002_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => true),
			array('colunaValor' => "datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo", 'colunaTitulo' => 'Prz'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => true),
			array('colunaValor' => "datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso", 'colunaTitulo' => 'Ats'),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'colunaMetrica' => true),
			array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Juros','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desconto','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Recebido", 'colunaTitulo' => 'Recebido','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'Portador'),
			array('colunaValor' => "T117_Nosso_Numero", 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'Observação'),
			array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta", 'colunaTitulo' => 'Conta'),
			array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'Grupo'),
			array('colunaValor' => "T002_T045_Id", 'colunaTitulo' => 'Lote'),	            
			array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
			array('colunaValor' => "T002_Agrupamento", 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Enviar_Banco", 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor' => "T117_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Reparcelar", 'colunaTitulo' => 'Reparcelar', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Cancelada", 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
			array('colunaValor' => "T035_Data_Processamento", 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
			array('colunaValor' => "T084_Id", 'colunaTitulo' => 'T084_Id', 'colunaExibir' => false)
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			left join D024 on D024_Id=T002_D024_Id
			left join D027 on D027_Id=T002_D027_Id
			left join T117 on T117_Id=T002_T117_Id
			left join D014 on D014_Id=T002_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T084 on T084_Id=T002_T084_Id
			left join T035 on T035_Id=T002_T035_Id
			left join C007 on C007_Id=T084_C007_Id
			left join T103 on T002_Id=T103_T002_Id
			WHERE T002_T084_Id='{$r_acaoId}'
			AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		*/ 
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
			});
		} </script></div>
EOT;
 		echo '<br><br>';
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

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
         
	break;
	case 'est020GridEnviarCTe':
		global $g;
		
		// EXIBE VENCIMENTO DO CERTIFICADO DIGITAL
		$cSql_Empresa=@mysql_query("SELECT C004_Certificado_Digital, C004_Senha_Certificados, C004_Localizacao_Certificados FROM C004 LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=@mysql_fetch_array($cSql_Empresa);
		log("Empresa:");
		log($mEmpresa);
		if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S') {
            if (!empty($mEmpresa['C004_Certificado_Digital'])) {	
				log("entrou 1");
	            $mkt_Hoje=time();
	            $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {	
				log("entrou 2");
                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
	            if (file_exists($cArquivo_Certificado_Digital)) {
					log("entrou 3");
	                $mkt_Hoje=time();
	                $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
	            }
            }	  
               
            if (empty($mktVencimentoCertificado)) {
				log("entrou 4");
            	$cert = "<span style='color: var(--BGVermelho);font-size:18px;'>Não foi possivel ler o certificado digital</span>";
            } else {
				log("entrou 5");
                $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
                if ($mktVencimentoCertificado>$mkt_Hoje) {
					log("entrou 6");
                    $isCertificadoValido=true; 
                    echo "<span style='padding-left:600px;'>
                                  			<span style='font-size:11px;'>Seu certificado digital vence em:<BR></span>
                                  			<span style='padding-left:615px;color:blue;font-size:14px;'>{$VencimentoCertificado}</span>
                                  		</span>"; 
                } else {
					log("entrou 7");
					echo "<span style='padding-left:600px;'>
							<span style='color:#fc5a5a;font-size:11px;'>CERTIFICADO DIGITAL VENCIDO EM:<BR></span>
							<span style='padding-left:615px;color:yellow;font-size:14px;'>{$VencimentoCertificado}</span>
						</span>";
                }
            }
        }
	
		$grid = array(
 			array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'colunaAlinhar' => 'center','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'T084_Flag_Enviar', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoEnviar','colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N','center','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Número CT-e', 'colunaAlinhar' => 'center','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center','style' => 'width:1%;font-size:12px'),						
			array('colunaValor' => 'T084_Ambiente_CTe', 'colunaTitulo' => 'Ambiente CT-e', 'colunaExibir' => false),
			array('colunaValor' => 'T084_Flag_Travar', 'colunaTitulo' => 'Finalizada', 'colunaExibir' => false),
			array('colunaValor' => 'OrigemD018.D018_UF', 'colunaTitulo' => 'UF Origem','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'OrigemD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Origem','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'DestinoD018.D018_UF', 'colunaTitulo' => 'UF Destino','style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'DestinoD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Destino','style' => 'width:1%;font-size:12px'),			
			array('colunaValor' => 'T084_Codigo_CFOP', 'colunaTitulo' => 'CFOP','style' => 'width:1%;font-size:12px'),			
			array('colunaValor' => 'T084_C007_Id_Marcou_Enviar', 'colunaTitulo' => 'T084_C007_Id_Marcou_Enviar','colunaExibir' => false),			
			array('colunaValor' => 'txSefazCTe', 'colunaTitulo' => 'Transmissão CT-e Sefaz', 'callback' => 'txSefazCTe', 'colunaTipo' => 'livre', 'style' => 'width: 50%; vertical-align: top;'),						
		);
		
				
		function gridLinha($array) {
			global $g;

			if ($array['todosCampos']['T276_TX_Resposta_Codigo'] < 100) {
				if ($array['todosCampos']['T276_TX_Resposta_Codigo'] == 8) {
					return 'linhaSuccess';
				} 
			} else {
				return 'linhaErrorNoColor';
			}
		}

		/**
		 * Definição da SQL
		 */
		$from = "T084";
		
		$extra = <<<EOT
			LEFT JOIN D018 as OrigemD018 ON OrigemD018.D018_Id=T084_D018_Id_Origem 			
			LEFT JOIN D020 as OrigemD020 on OrigemD020.D020_Id=T084_D020_Id_Origem			
			LEFT JOIN D018 as DestinoD018 ON DestinoD018.D018_Id=T084_D018_Id_Destino 			
			LEFT JOIN D020 as DestinoD020 on DestinoD020.D020_Id=T084_D020_Id_Destino					
			WHERE T084_C004_Id='{$g['empresaAtual']}' 			
				AND T084_TX_Flag_Finalizada!=1	
				AND T084_Flag_Entrada_Saida = 'S'
				AND IFNULL(T084_Numero_Protocolo_Cte, '')=''				
			GROUP BY T084_Id
			ORDER BY T084_Numero_Conhecimento
EOT;

		$exibirBotao = ($g['C004']['C004_Versao_CTe'] == '3.00') ? 'S' : 'N' ;
		// Botões
		$botoes = array(
			array('titulo' => 'Enviar Lote', 'id' => 'est020EnviarLote', 'exibirCallback' => "return ('{$exibirBotao}' == 'S');"),	
		);
		echo Botoes::processar($botoes);	

		$novoIdTelaForm = uniqid();
		//define o clique da linha
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est020EnviarLote').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est020/grid_func-ajax/est020EnviarLote/?ajax=true&&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);									
								} else {									
									divRefresh('{$g['divId']}', true);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Você tem certeza que deseja enviar o lote?', \$buttons);
				});
				$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T084_Id = id[0].replace(/T084_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Editar CTE - ID: ' + T084_Id, '/est/est020/content/est020ContentIncluirConhecimento/', '&acaoId=' + encodeURIComponent(T084_Id) + '&tabela={$from}', [990,605]);
				});		
				if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 150000);		
			} </script></div>
		EOT;
	
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T084_Numero_Conhecimento' => array('titulo' => 'CT-e', 'tipo' => 'numero'),
            'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T084_Flag_Travar' => array('titulo' => 'Finalizada?(S/N)', 'tipo' => 'texto'),
        );
		

		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		//$sqlGridFiltro = base64_encode($geradoSql);
			
		/**
		* Totais (Versão Acima do GRID)
		*/		

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

	case 'est020GridEnviadas':	
	 /**
	 * Definição do Grid (Tabela)
	 */
	 $grid = array(
		array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Número CT-e', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center'),			
		array('colunaValor' => 'T084_Ambiente_CTe', 'colunaTitulo' => 'Ambiente CT-e', 'colunaExibir' => false),
		array('colunaValor' => 'OrigemD018.D018_UF', 'colunaTitulo' => 'UF Origem'),
		array('colunaValor' => 'OrigemD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Origem'),
		array('colunaValor' => 'DestinoD018.D018_UF', 'colunaTitulo' => 'UF Destino'),
		array('colunaValor' => 'DestinoD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Destino'),							 		 		 		  		 
		 array('colunaValor' => 'T084_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
		 array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),		 
		 array('colunaValor' => 'T084_Numero_Recibo_Cte', 'colunaTitulo' => 'Recibo', 'colunaExibir' => false),		 
		 array('colunaValor' => 'T084_TX_Resposta_Codigo_Interno', 'colunaExibir' => false),
		 array('colunaValor' => 'T084_TX_Retorno_Codigo', 'colunaExibir' => false),
		 array('colunaValor' => 'T084_TX_Retorno_Motivo', 'colunaExibir' => false),		 		 		 
		 array('colunaValor' => 'T084_Data_Emissao', 'colunaExibir' => false),		 		 		 
		 array('colunaValor' => 'T084_Chave_Acesso_Cte', 'colunaExibir' => false),		 		 		 
		 array('colunaValor' => 'T084_Numero_Protocolo_Cte', 'colunaExibir' => false),		 		 		 
		 array('colunaValor' => 'T084_Flag_Denegado', 'colunaExibir' => false),		 		 		 
		 array('colunaValor' => 'T084_Flag_Cancelado', 'colunaExibir' => false),		 		 		 
		 array('colunaValor' => 'T084_Numero_Protocolo_Cte', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Numero_Protocolo_Nfe'),		 
		 array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'colunaTipo' => 'livre', 'callback' => 'validaCampoXML'),						
		 array('colunaValor' => 'txSefazCTe', 'colunaTitulo' => 'Transmissão CT-e Sefaz', 'callback' => 'txSefazCTe', 'colunaTipo' => 'livre', 'style' => 'width: 50%; vertical-align: top;'),						
	 );

	 function gridLinha($array) {
		 global $g;
		 if($array['todosCampos']['T084_Flag_Denegado'] == 'S' || $array['todosCampos']['T084_Flag_Cancelado'] == 'S')  {
			return 'linhaVermelho';
		 }

		 if ($array['todosCampos']['T084_TX_Resposta_Codigo_Interno'] < 100) {
			 if ($array['todosCampos']['T084_TX_Resposta_Codigo_Interno'] == 8) {
				 return 'linhaSuccess';
			 } else {
				 return '';
			 }
		 } else {
			 return 'linhaErrorNoColor';
		 }
	 }
	 /**
	 *
	 * Definição da SQL
	 */
	 $from = 'T084';
	 
	 $extra = <<<EOT
	 		LEFT JOIN D018 as OrigemD018 ON OrigemD018.D018_Id=T084_D018_Id_Origem 			
			LEFT JOIN D020 as OrigemD020 on OrigemD020.D020_Id=T084_D020_Id_Origem			
			LEFT JOIN D018 as DestinoD018 ON DestinoD018.D018_Id=T084_D018_Id_Destino 			
			LEFT JOIN D020 as DestinoD020 on DestinoD020.D020_Id=T084_D020_Id_Destino	
			WHERE T084_C004_Id='{$g['empresaAtual']}' 
				AND T084_Flag_Entrada_Saida = 'S'	 		
				AND T084_TX_Flag_Finalizada!=1	 		 		
		 		AND IFNULL(T084_Numero_Protocolo_Cte,'') != ''
		 	ORDER BY T084_Numero_Conhecimento
EOT;

	$novoIdTelaForm = uniqid();
	echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').split('|');
			var T084_Id = id[0].replace(/T084_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Editar CTE - ID: ' + T084_Id, '/est/est020/content/est020ContentIncluirConhecimento/', '&acaoId=' + encodeURIComponent(T084_Id) + '&tabela={$from}', [990,605]);
		});
	} </script></div>
	EOT;
	 /**
	 * Filtro
	 */
	 $gridFiltro = array(
		 'T084_Numero_Conhecimento' => array('titulo' => 'CT-e', 'tipo' => 'numero'),		 
		 'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),		 
		 'T084_Flag_Travar' => array('titulo' => 'Finalizadas?(S/N)', 'tipo' => 'texto'),		 
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
	 $g['smarty']->assign('class', 'naoProcessarThead');
	 $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	 gBotaoAuditoria($from);
	 if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
 break;

 case 'est020GridDocumentosAnterior':		
	global $g;
	
	$grid = array(
		array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T308_Id', $g['divId'])),		
		array('colunaValor' => 'T308_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),				
		array('colunaValor' => 'T308_Chave_Acesso', 'colunaTitulo' => 'Chave'),								
	);
		
	/**
	 * Definição da SQL
	 */
	$from = "T308";	
	$extra = <<<EOT
		WHERE  T308_T084_Id = '{$r_acaoId}'									
EOT;

	/**
	 * Botões Almir
	 */
	$botoes = array(
		array('titulo' => 'Incluir', 'id' => 'est020IncluirDocumentoAnterior'),
	);
	echo Botoes::processar($botoes);

	/**
	* Botões (Versão Acima do GRID)
	*/
	echo <<<EOT

		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est020IncluirDocumentoAnterior').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Documento Anterior', '/est/est020/content/est020ContentIncluirDocumentoAnterior/', '&tab=geral' + '&T084_Id=' + encodeURIComponent('{$r_acaoId}'), [500,300]);												
			});
		} </script></div>
EOT;

	/**
	* Filtro
	*/
	$gridFiltro = array();
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);	

	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

	$novoIdTelaForm = uniqid();	
	echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			// Previne duplo click (abrir duas janelas)
			if (dbclickPrevent) { return false; }
			dbclickPrevent = true;
			setTimeout(function() { dbclickPrevent = false; }, 500);
			// (substituir pelo mesmo ID da chave primária do grid)
			var chavePrimaria = 'T308_Id';	
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').split('|');
			var acaoId = id[0].replace(chavePrimaria + '-', '');			
			// Ação: abrir nova janela
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Documento Anterior - ID: ' + acaoId, '/est/est020/content/est020ContentIncluirDocumentoAnterior/', '&acaoId=' + encodeURIComponent(acaoId) + '&T084_Id=' + encodeURIComponent('{$r_acaoId}'), [500,300]);			
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