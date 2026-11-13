<?php
namespace hardness;
/**
 * CAD - CAD008 - Bens
 */
// Classes
require_once('bibliotecas/classes/CAD008.php');
$CAD008 = new CAD008();
// Carrega funções callback
require_once('prog/cad/cad008/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/cad/cad008/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: cad008tab01
	 */
	case 'cad008grid01':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D106_Id', $g['divId'])),
		array('colunaValor' => 'D106_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
		array('colunaValor' => 'D106_D128_Id', 'colunaTitulo' => 'Tipo', 'colunaExibir'=>false),
		array('colunaValor' => 'D128_Tipo', 'colunaTitulo' => '00Tipo'),
		array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Descrição'),
		array('colunaValor' => 'D106_Data_Compra', 'colunaTitulo' => 'Data Compra', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D106_Data_Validade', 'colunaTitulo' => 'Data Validade', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D106_Especificacoes', 'colunaTitulo' => 'Especificações Técnicas'),
		array('colunaValor' => 'D106_Flag_Status', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D106';

		$extra = <<<EOT
		left join D128 on D128_Id=D106_D128_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad008Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad008Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Bens', '/cad/cad008/content/cad008content03/', '', [500,320]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D106_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'D106_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D106_Data_Compra' => array('titulo' => 'Data Compra', 'tipo' => 'data'),
			'D106_Data_Validade' => array('titulo' => 'Data Validade', 'tipo' => 'data'),
			'D106_Especificacoes' => array('titulo' => 'Especificações Técnicas', 'tipo' => 'texto'),
			//'D106_Flag_Status' => array('titulo' => 'Status(A/I)', 'tipo' => 'texto'),
			'D106_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Ativo', 'value' => 'A'),
				array('title' => 'Inativo', 'value' => 'I'),
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
				<span class="item" style="text-align:right">Bens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D106_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Bem - ID: ' + id, '/cad/cad008/content/cad008content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,600]);
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
		 * Grid Manutenções de bem
		 */
	case 'cad008grid02':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExcluirManutencao'),
		array('colunaValor' => 'T142_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
		array('colunaValor' => 'T142_D106_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
		array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
		array('colunaValor' => 'T142_Data_Manutencao', 'colunaTitulo' => 'Data Manutenção', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T142_Descricao', 'colunaTitulo' => 'Descrição'),
		array('colunaValor' => 'T142_Valor', 'colunaTitulo' => 'Valor Manutenção', 'colunaCasasDecimais' => '2'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T142';

		$extra = <<<EOT
				left join D106 on D106_Id=T142_D106_Id
				where 
				T142_C004_Id={$g['empresaAtual']}
				AND 
				T142_D106_Id = {$r_acaoId}
EOT;
		if (!empty($xConsulta_Clausula_Filtro)) {
			$extra .= <<<EOT
				      and $xConsulta_Clausula_Filtro
EOT;
		}
		
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
		 $novoIdTelaForm = uniqid();
		 echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').replace(/T142_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/ven/ven011/content/ven011content02/',  '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,300]);
			});
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
			
				/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="button" id="cad008IncluirManutencao">Incluir</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad008IncluirManutencao').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Manutenção de Bens', '/ven/ven011/content/ven011content02/', '&D106_Id=' + encodeURIComponent({$r_acaoId}), [520,300]);
			});
		} </script></div>
EOT;
		break;
			
		/**
		 * Grid COntratos de locação de bem
		 */
	case 'cad008grid03':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => ''),
		array('colunaValor' => 'T141_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
		array('colunaValor' => 'T158_D106_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
		array('colunaValor' => 'T141_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
		array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
		array('colunaValor' => 'T141_Data_Inicio', 'colunaTitulo' => 'Data Inicio', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T141_Data_Validade', 'colunaTitulo' => 'Data Validade', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T141_Valor_Mensal', 'colunaTitulo' => 'Valor Mensal', 'colunaCasasDecimais' => '2'),
		array('colunaValor' => 'T141_Dia_Vencimento', 'colunaTitulo' => 'Dia Vencimento'),
		array('colunaValor' => 'T141_Flag_Status', 'colunaTitulo' => 'Status', 'callback' =>'validaCampoStatusContratos'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T141';

		$extra = <<<EOT
				LEFT JOIN D024 ON D024_Id=T141_D024_Id 
				LEFT JOIN T158 ON T141_Id=T158_T141_Id
				LEFT JOIN D106 ON D106_Id=T158_D106_Id
				where 
				T141_C004_Id={$g['empresaAtual']}
				AND 
				T158_D106_Id = {$r_acaoId}
EOT;
		if (!empty($xConsulta_Clausula_Filtro)) {
			$extra .= <<<EOT
				      and $xConsulta_Clausula_Filtro
EOT;
		}
		
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)

		 $novoIdTelaForm = uniqid();
		 echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').replace(/T141_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/ven/ven010/content/ven010content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,260]);
			});
			} </script></div>
			EOT;
			*/
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

		/**
		 * Grid Logística de bem
		 */
	case 'cad008grid04':
		/**
		 * Definição do Grid (Tabela)
		 */

		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExcluirLogistica'),
		array('colunaValor'=> 'T143_D106_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
		array('colunaValor'=> 'T143_Id', 'colunaTitulo'=>'ID', 'chavePrimaria'=>true),
		array('colunaValor'=> 'D106_Descricao', 'colunaTitulo'=>'Bem'),
		array('colunaValor'=> 'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade'),
		array('colunaValor'=> 'D018_Estado', 'colunaTitulo'=>'Estado'),
		array('colunaValor'=> 'D030_Nome_Pais', 'colunaTitulo'=>'País'),
		array('colunaValor'=> 'T143_Data_Transferencia', 'colunaTitulo'=>'Data Transferência', 'callback'=>'gCorrigeData'),
		array('colunaValor'=> 'T143_Data_Prev_Retorno', 'colunaTitulo'=>'Data Prev. Retorno', 'callback'=>'gCorrigeData'),
		array('colunaValor'=> 'T143_Observacao', 'colunaTitulo'=>'Observação'),
		);
			
		$from = "T143";
			
		$extra = <<<EOT
				left join D106 on D106_Id=T143_D106_Id
	    		left join T141 on T141_Id=T143_T141_Id
	    		left join D020 on D020_Id=T143_D020_Id
	    		left join D018 on D018_Id=T143_D018_Id
	    		left join D030 on D030_Id=T143_D030_Id	    		
	    		where T143_C004_Id={$g['empresaAtual']}
	    		AND
	    		T143_D106_Id = {$r_acaoId}
EOT;
		if (!empty($xConsulta_Clausula_Filtro)) {
			$extra .= <<<EOT
				      and $xConsulta_Clausula_Filtro
EOT;
		}
		
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
		 $novoIdTelaForm = uniqid();
		 echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').replace(/T143_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/ven/ven009/content/ven009content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,390]);
			});
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
		
    	/**
		* Botões
		*/
         // definição dos botões
        $botoesDir = '<button type="button" id="cad008IncluirLogistica">Incluir</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);        
		$g['smarty']->display('buttons-floatBottom.tpl'); 

		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad008IncluirLogistica').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Registro de Transferência', '/ven/ven009/content/ven009content02/',  '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,390]);
			});		
		} </script></div>      
EOT;

	break;
		
	case 'cad008GridChecklist':
		
		/**
		* Executa o script de inserção na tabela T159 (n:m)
		*/
		$CAD008->insereRegistrosT159($r_acaoId);
		 		
		 /**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D120_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T159_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D120_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D120_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
			array('colunaValor' => 'D120_D037_Id', 'colunaTitulo' => 'IdUnidade', 'colunaExibir' => false),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
			array('colunaValor' => 'T159_Flag_Ok', 'colunaTitulo' => 'Ok', 'callback' => 'campoCheckboxChecklist'),
			array('colunaValor' => 'T159_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' =>'2', 'colunaEditar' => true),
			array('colunaValor' => 'T159_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaEditar' => true),
		);

		/**
		* Definição da SQL
		*/
		$sql="SELECT D106_Tipo FROM D106 WHERE D106_Id='{$r_acaoId}'";
		$resultado=mysql_query($sql);
		$row=mysql_fetch_assoc($resultado);
		$from = 'D120';
		$extra = <<<EOT
		left join T159 on T159_D120_Id=D120_Id
		left join D037 on D037_Id=D120_D037_Id
		where D120_Tipo = '{$row['D106_Tipo']}'
EOT;

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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D120_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Checklist - ID: ' + acaoId, '/cad/cad036/content/cad036ContentCadastroChecklistDeBens/', '&acaoId=' + encodeURIComponent(acaoId), [700,500]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		
	case 'cad008GridEspecificacoes':
		
		/**
		* Executa o script de inserção na tabela T160 (n:m)
		*/
		$CAD008->insereRegistrosT163($r_acaoId);
		
	    /**
		* Definição do Grid (Tabela)
		*/				
		
		$grid = array(
			array('colunaValor' => 'T163_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T163_D129_Id', 'colunaTitulo' => 'Descrição', 'colunaExibir'=>false),
			array('colunaValor' => 'D129_Descricao', 'colunaTitulo' => 'Especificações'),
			array('colunaValor' => 'T163_Valor', 'colunaTitulo' => 'Valor', 'colunaEditar' => true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T163';
			
       $extra = <<<EOT
       left join D129 on D129_Id=T163_D129_Id
       where (T163_D106_Id="{$r_acaoId}")       
EOT;

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
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				// <div style="clear:both;"></div>
			// </div>
// EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D121_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Checklist - ID: ' + acaoId, '/cad/cad036/content/cad036ContentCadastroChecklistDeBens/', '&acaoId=' + encodeURIComponent(acaoId), [700,500]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
