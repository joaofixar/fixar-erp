<?php
namespace hardness;
/** Classes
require_once('bibliotecas/classes/CAD001.php');
$CAD001 = new CAD001();
*/


require_once('prog/est/est011/grid_func-callback.php');
require_once('prog/est/est011/grid_func-js.php');



switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est011GridPrincipalProdutos':
		$grid = array(			
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T182_Id', $g['divId'])),
 			//array('colunaValor' => "campoMarcado", 'colunaTipo' => "livre", 'colunaTitulo' => 'Rm','style'=>'width:1%', 'callback' => 'validaCampoMarcado', 'colunaAlinhar' => 'center'),
 			//array('colunaValor' => 'T182_Marcado', 'colunaExibir' => false),
 			array('colunaValor' => 'T182_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style'=>'width:1%'),
			array('colunaValor' => 'T182_Data', 'colunaTitulo' => 'Data Abertura', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T182_Data_Conclusao', 'colunaTitulo' => 'Data Conclusão', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Cli.D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor' => 'Cli.D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'contatoCli.D013_Nome_Contato', 'colunaTitulo' => 'Contato'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T182_Quantidade', 'colunaTitulo' => 'Qte'),
			array('colunaValor' => 'D164_Tipo', 'colunaTitulo' => 'Tipo'),
			//array('colunaValor' => 'T182_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'retornaTipo'),
			array('colunaValor' => 'Forn.D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'contatoForn.D013_Nome_Contato', 'colunaTitulo' => 'Contato'),
			array('colunaValor' => 'T182_Preco', 'colunaTitulo' => 'Preço un', 'tipo' => 'moeda', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T182_Preco*T182_Quantidade', 'colunaTitulo' => 'Preço total', 'tipo' => 'moeda', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'C007_Login', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T182_Hora', 'colunaTitulo' => 'Hora', 'callback'=>'gCorrigeTempo', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T182_Observacao', 'colunaTitulo' => 'Observação'),
			array('colunaValor' => 'T182_NF_Venda', 'colunaTitulo' => 'NF Venda', 'colunaEditar' => false),
			array('colunaValor' => 'T182_NF_Remessa_Cliente', 'colunaTitulo' => 'NF Rem.Cli', 'colunaEditar' => false),
			array('colunaValor' => 'T182_NF_Remessa_Fornecedor', 'colunaTitulo' => 'NF Rem.Forn', 'colunaEditar' => false),
			array('colunaValor' => 'T182_NF_Retorno_Fornecedor', 'colunaTitulo' => 'NF Ret.Forn', 'colunaEditar' => false),
			array('colunaValor' => 'T182_NF_Retorno_Cliente', 'colunaTitulo' => 'NF Ret.Cli', 'colunaEditar' => false),
			array('colunaValor' => 'D162_Status', 'colunaTitulo' => 'Status'),
			array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'T007_T005_Id', 'colunaTitulo' => 'Pedido NF'),
			array('colunaValor' => 'T182_Pedido', 'colunaTitulo' => 'Pedido', 'colunaEditar' => false),			
			array('colunaValor' => 'TransCli.D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora venda'),			
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T182";
		
		$extra = <<<EOT
			left join D009 on D009_Id=T182_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join C007 on C007_Id=T182_C007_Id
			left join D162 on D162_Id=T182_D162_Id
			left join D164 on D164_Id=T182_D164_Id
			left join D018 on D018_Id=T182_D018_Id
			left join T007 on T007_Id=T182_T007_Id
			left join D022 as TransCli on TransCli.D022_Id=T007_D022_Id
			left join D024 as Cli on Cli.D024_Id=T182_D024_Id_Cliente
			left join D024 as Forn on Forn.D024_Id=T182_D024_Id_Fornecedor
			left join D013 as contatoCli on contatoCli.D013_Id=T182_D013_Id_Cliente
			left join D013 as contatoForn on contatoForn.D013_Id=T182_D013_Id_Fornecedor			
			where D009_C004_Id = '{$g['empresaAtual']}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T182_Status']=='N'){
				return 'linhaCinza';
			}
			else if($array['todosCampos']['T182_Status']=='P'){
				return 'linhaAzul';
			}
		}
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est011Incluir'),
		);
		echo Botoes::processar($botoes);
				
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
		<div class="buttonsBottomTop">
			<!--<button type="button" id="est011Incluir">Incluir</button>-->				
			<!--<button type="button" id="est011GerarRemessa">Gerar Remessa</button>-->
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est011Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Produto para Garantia/Conserto', '/est/est011/content/est011ContentIncluirProduto/','', [580,590]);
			});
			$('#est011GerarRemessa').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Remessa', '/est/est011/content/est011ContentGerarRemessa/','', [350,170]);
			});
		} </script></div>
EOT;
			
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'Cli.D024_Nome_Empresa, Cli.D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'Forn.D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'C007_Login' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'D162_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
			'D164_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'T182_Data' => array('titulo' => 'Data Abertura', 'tipo' => 'data'),
			'T182_Data_Conclusao' => array('titulo' => 'Data Conclusão', 'tipo' => 'data'),
			'T182_Lote_Fabricacao' => array('titulo' => 'Lote Fabricação', 'tipo' => 'texto'),
			'T182_Acao' => array('titulo' => 'Ação', 'tipo' => 'texto'),
			'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'T182_NF_Venda' => array('titulo' => 'NF Venda', 'tipo' => 'texto'),
			'TransCli.D022_Nome_Empresa' => array('titulo' => 'Transp.Venda', 'tipo' => 'texto'),
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
			array('nome' => 'Quantidade_Total', 'valor' => 'T182_Quantidade', 'titulo' => 'Quantidade Total'),
			array('nome' => 'Preço_Total', 'valor' => 'T182_Quantidade*T182_Preco', 'titulo' => 'Preço Total'),
        );

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Totalizadores (Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais(false, true)) { 
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("count(*),
													  sum(T182_Quantidade),
													  sum(T182_Preco*T182_Quantidade)",
													  $from,
													  $extra);

			list($valorTotal0,
				 $valorTotal1,
				 $valortotal2) = $sqlTotal;

			$valorTotal0 = gCorrigeNumero($valorTotal0);
			$valorTotal1 = gCorrigeNumero($valorTotal1);
			$valorTotal2 = gCorrigeNumero($valorTotal2);

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal0}" class="whiteBg" id="" size="9" readonly></span>
					<span class="item" style="text-align:right">Quantidade Total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="10" readonly></span>
					<span class="item" style="text-align:right">Preço Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="10" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();*/
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T182_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Produto - ID: ' + id, '/est/est011/content/est011ContentIncluirProduto/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [580,590]);
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
  	
	case 'est011GridPrincipalRemessas':
		$grid = array(			
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T183_Id', $g['divId'])),
 			array('colunaValor' => 'T183_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style'=>'width:1%'),
			array('colunaValor' => 'T183_Numero', 'colunaTitulo' => 'Número'),
			array('colunaValor' => 'T183_Data', 'colunaTitulo' => 'Data','callback'=>'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaExibir' => false),
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T183";
		
		$extra = <<<EOT
			left join D024 on D024_Id=T183_D024_Id
EOT;

			/**
			* Botões (Versão Acima do GRID)
			*/
/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est011Incluir">Incluir</button>				
				<button type="button" id="est011GerarRemessa">Gerar Remessa</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est011Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Produto para Garantia/Conserto', '/est/est011/content/est011ContentIncluirProduto/','', [500,420]);
				});
				$('#est011GerarRemessa').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Remessa', '/est/est011/content/est011ContentGerarRemessa/','', [350,170]);
				});
			} </script></div>
EOT;
			*/
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T183_Numero' => array('titulo' => 'Número', 'tipo' => 'texto'),
		);
		
		echo <<<EOT
		   <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		         $('#{$g['divId']} .grid').css('width', '1%');
		    } </script></div>
EOT;
		
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
				var id = $(this).closest('tr').attr('id').replace(/T183_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Remessa - ID: ' + id, '/est/est011/content/est011ContentEditarRemessa/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,400]);
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
  	
	case 'est011gridItensRemessa':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T184_Id', $g['divId'])),
 			array('colunaValor' => 'T184_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style'=>'width:1%'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'T184_Quantidade', 'colunaTitulo' => 'Qte'),
			array('colunaValor' => 'T184_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'retornaTipoItemRemessa'),
			array('colunaValor' => 'T184_Preco', 'colunaTitulo' => 'Preço', 'tipo' => 'moeda'),
			array('colunaValor' => 'C007_Login', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T184_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T184";
		
		$extra = <<<EOT
			left join D009 on D009_Id=T184_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join C007 on C007_Id=T184_C007_Id
			where T184_T183_Id='{$r_acaoId}'
EOT;

			/**
			* Botões (Versão Acima do GRID)
			*/
/*		
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est011Incluir">Incluir</button>				
				<button type="button" id="est011GerarRemessa">Gerar Remessa</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est011Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Produto para Garantia/Conserto', '/est/est011/content/est011ContentIncluirProduto/','', [500,420]);
				});
				$('#est011GerarRemessa').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Remessa', '/est/est011/content/est011ContentGerarRemessa/','', [350,170]);
				});
			} </script></div>
EOT;
*/
					
		/**
		* Filtro
		*/		
		/*
		$gridFiltro = array(
			'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'C007_Login' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
		);
		
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
		*/
			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
			/**
			* Botões (Versão Acima do GRID)
			*/
			/**
			* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Países<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
*/				

			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			/*
			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T182_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Produto - ID: ' + id, '/est/est011/content/est011ContentIncluirProduto/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,420]);
			});
		} </script></div>
EOT;
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

  	case 'est011GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T271_Id', $g['divId'])),
				array('colunaValor' => 'T271_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
				array('colunaValor' => 'T271_T182_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
				array('colunaValor' => 'T271_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
				array('colunaValor' => 'T271_Descricao', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'T271_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
				array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumentoRMA'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T271';

			$extra = <<<EOT
				left join C007 on C007_Id=T271_C007_Id
				where 
				(T271_T182_Id = '{$r_T182_Id}')
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
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T271_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

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

			$novoIdTelaForm = uniqid();
		break;	
  	  	
}
?>
