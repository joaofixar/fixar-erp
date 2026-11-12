<?php
namespace hardness;
  /**
 * Weiberlan
 * 07/12/2011
 */
//--Carrega funções callback
require_once('prog/cad/cad040/grid_func-callback.php');
require_once('prog/cad/cad040/grid_func-js.php');

require_once('bibliotecas/classes/CAD040.php');
$CAD040 = new CAD040();

switch ($g['r'][3]) {
	
	case 'cad040CertificadosConformidade':
		$D106_Id = (isset($r_D106_Id)) ? $r_D106_Id : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D125_Id', $g['divId'])),
			//array('colunaValor' => "campoImpressão", 'colunaTipo' => "livre",'colunaTitulo' => 'Imprimir', 'callback' => 'campoImpressao'),
			array('colunaValor' => 'D125_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D125_Numero', 'colunaTitulo' => 'Número'),
			array('colunaValor' => 'D125_D106_Id', 'colunaTitulo' => 'Bem', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
			array('colunaValor' => 'D125_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D125_Validade', 'colunaTitulo' => 'Validade', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D125_Observacao', 'colunaTitulo' => 'Observação'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D125';
		$extra = <<<EOT
		left join D106 on D106_Id=D125_D106_Id
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad040GridCertificadoDeConformidadeBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad040GridCertificadoDeConformidadeBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Certificado Conformidade', '/cad/cad040/content/cad040ContentCadastroCertificadoConformidade/', '&tab=geral' + '&D106_Id=' + encodeURIComponent('{$D106_Id}'), [500,300]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D125_Numero' => array('titulo' => 'Numero', 'tipo' => 'numero'),
			'D106_Descricao' => array('titulo' => 'Bem', 'tipo' => 'texto'),
			'D125_Emissao' => array('titulo' => 'Emissao', 'tipo' => 'data'),
			'D125_Validade' => array('titulo' => 'Validade', 'tipo' => 'data'),
			'D125_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

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
					var chavePrimaria = 'D125_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					var D106_Id = id[1].replace('D125_D106_Id' + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Checklist - ID: ' + acaoId, '/cad/cad040/content/cad040ContentCadastroCertificadoConformidade/', '&acaoId=' + encodeURIComponent(acaoId) + '&D106_Id=' + encodeURIComponent(D106_Id), [900,550]);
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
	
	case 'cad040GridChecklist':
		$D106_Id = (isset($r_D106_Id)) ? $r_D106_Id : false;
		$D125_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		/**
		* Executa o script de inserção na tabela T159 (n:m)
		*/
		$CAD040->insereRegistrosT159($D106_Id, $D125_Id);
		 		
		 /**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D120_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T159_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D120_Descricao', 'colunaTitulo' => 'Descrição Português'),
			array('colunaValor' => 'D120_D128_Id', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
			array('colunaValor' => 'D120_D037_Id', 'colunaTitulo' => 'IdUnidade', 'colunaExibir' => false),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
			array('colunaValor' => 'T159_Flag_Ok', 'colunaTitulo' => 'Ok', 'callback' => 'campoCheckboxChecklist'),
			array('colunaValor' => 'T159_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' =>'2', 'colunaEditar' => true),
			array('colunaValor' => 'T159_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaEditar' => true),
			array('colunaValor' => 'D120_Descricao_Ingles', 'colunaTitulo' => 'Descrição Inglês'),
		);

		/**
		* Definição da SQL
		*/
		$sql="SELECT D106_D128_Id FROM D106 WHERE D106_Id='{$D106_Id}'";
		$resultado=mysql_query($sql);
		$row=mysql_fetch_assoc($resultado);
		$from = 'D120';
		$extra = <<<EOT
			left join T159 on (T159_D120_Id=D120_Id and T159_D125_Id={$D125_Id}) 
			left join D037 on D037_Id=D120_D037_Id
			where D120_D128_Id = '{$row['D106_D128_Id']}'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D120_Descricao' => array('titulo' => 'Descrição Português', 'tipo' => 'texto'),
			'D120_Descricao_Ingles' => array('titulo' => 'Descrição Inglês', 'tipo' => 'texto'),
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
		
		case 'cad040GridAcessorios':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T164_Id', $g['divId'])),
				array('colunaValor' => 'T164_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
				array('colunaValor' => 'T164_D106_Id', 'colunaTitulo' => 'D106_Id', 'colunaExibir' => false),
				array('colunaValor' => 'D128_Tipo', 'colunaTitulo' => 'Tipo'),
				array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T164';

			$extra = <<<EOT
			LEFT JOIN D106 ON D106_Id=T164_D106_Id
			LEFT JOIN D128 ON D128_Id=D106_D128_Id
			WHERE T164_D125_Id = {$r_acaoId}
EOT;
			if (!empty($xConsulta_Clausula_Filtro)) {
				$extra .= <<<EOT
				      and $xConsulta_Clausula_Filtro
EOT;
			}

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/cad/cad001/content/cad001content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,260]);
			});
			} </script></div>
			EOT;
			*/
			
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

			$novoIdTelaForm = uniqid();
			break;
}

?>
