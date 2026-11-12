<?php
namespace hardness;
require_once("bibliotecas/classes/CAD010.php");
$CAD010 = new CAD010();
// Classes
//require_once('bibliotecas/classes/VEN010.php');
//$VEN010 = new VEN010();
// Carrega funções callback
require_once('prog/ven/ven010/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven010/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	
	/**
	 * Grid Principal
	 * Chamado em: ven010tab01
	 */
	case 'ven010grid01':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
		array('colunaValor' => 'T141_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
		array('colunaValor' => 'T141_D106_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
		array('colunaValor' => 'T141_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
		array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo'),
		//array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
		array('colunaValor' => 'T141_Data_Inicio', 'colunaTitulo' => 'Data Inicio', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T141_Data_Validade', 'colunaTitulo' => 'Data Validade', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T141_Valor_Mensal', 'colunaTitulo' => 'Valor Mensal','colunaCasasDecimais' => '2'),
		array('colunaValor' => 'T141_Dia_Vencimento', 'colunaTitulo' => 'Dia Vencimento'),
		array('colunaValor' => 'T141_Flag_Status', 'colunaTitulo' => 'Status', 'callback' =>'validaCampoStatus'),
		array('colunaValor' => 'T141_D116_Id', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),		
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T141';

		$extra = <<<EOT
				left join D106 on D106_Id=T141_D106_Id
				left join D024 on D024_Id=T141_D024_Id
				left join D116 on D116_Id=T141_D116_Id
				where 
				(T141_C004_Id={$g['empresaAtual']})
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven010Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven010Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Adicionar Contrato', '/ven/ven010/content/ven010content03/', '', [520,300]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D106_Descricao' => array('titulo' => 'Bem', 'tipo' => 'texto'),
			'T141_Data_Inicio' => array('titulo' => 'Data Inicio', 'tipo' => 'data'),
			'T141_Data_Validade' => array('titulo' => 'Data Validade', 'tipo' => 'data'),
			'T141_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
			'D116_Modelo' => array('titulo' => 'Veículos', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Contratos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/T141_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar contrato de locação - ID: ' + id, '/ven/ven010/content/ven010content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}'+'&mostraGerar=true', [900,550]);
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

	break;
	
	case 'ven010grid02':
		/**
		* Definição do Grid (Tabela)
		*/			           
		$acaoId = isset($r_acaoId) ? $r_acaoId : null; 
	
		$grid = array(	
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha','callbackParameter' => array('T143_Id', $g['divId'])),
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
	    		and T141_Id = {$acaoId}
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
				 abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Atualização de Registro ID: ' + id, '/ven/ven009/content/ven009content02/',   '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&T141_Id=' + encodeURIComponent({$r_acaoId}), [500,380]);
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
        $botoesDir = '';
        $botoesDir .= '<button type="button" id="ven009Incluir">Incluir</button>';  
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';      
		$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);        
		$g['smarty']->display('buttons-floatBottom.tpl'); 

		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven009Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Registro de Transferência', '/ven/ven009/content/ven009content02/', '&T141_Id=' + encodeURIComponent({$r_acaoId}), [500,380]);
			});		
		} </script></div>      
EOT;
		
	break;
	
	case 'ven010grid03':
		/**
		* Definição do Grid (Tabela)
		*/			           
		$acaoId = isset($r_acaoId) ? $r_acaoId : false; 
	
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha','callbackParameter' => array('T002_Id', $g['divId'])),			
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			array('colunaValor'=>  "T002_T141_Id", 'colunaTitulo'=>'IdConReceber','colunaExibir'=>false),
			array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'Rm', 'colunaExibir'=>false),			
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Número'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo','colunaCasasDecimais' => '2'),
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
					left join D046 on D046_Id=D024_D046_Id
					left join T007 on T007_Id=T002_T007_Id
					left join T035 on T035_Id=T002_T035_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
					where T002_C004_Id={$g['empresaAtual']}
					and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
					and T002_T141_Id = {$acaoId}
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
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,500]);
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
		
		$botoesDir = '';
		$botoesEsq = '';
		$botoesEsq .= '<button onclick="VEN010GerarContasReceber(\''.$r_T141_Id.'\',\''.$g['divId'].'\'); return false;" class="btnFechar">Gerar Recebimentos</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
	break;
	
	case 'ven010GridBens':
			
		$acaoId =  (isset($r_acaoId)) ? $r_acaoId : false;
		$CAD010->atualizaTotalMensalGrid($acaoId);
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T158_Id', $g['divId'])),
			array('colunaValor' => 'T158_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T158_D106_Id', 'colunaTitulo' => 'FK', 'colunaExibir'=>false),
			array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
			array('colunaValor' => 'T158_Valor_Mensal', 'colunaTitulo' => 'Valor Mensal', 'colunaCasasDecimais'=>'2'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T158';
		$extra = <<<EOT
		
			left join D106 on D106_Id=T158_D106_Id
			WHERE T158_T141_Id='{$acaoId}'
EOT;

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
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T158_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: recarregar FORM (com novo acaoId)
					divSetarAcaoId('{$r_divReload}', acaoId);
					divRefresh('{$r_divReload}');
			       });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		
	break;
}
?>
