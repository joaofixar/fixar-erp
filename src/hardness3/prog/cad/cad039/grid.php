<?php
namespace hardness;

//--Carrega funções callback
require_once('prog/cad/cad039/grid_func-callback.php');
// Classes
require_once('bibliotecas/classes/CAD039.php');
$CAD039 = new CAD039();

switch ($g['r'][3]) {
 /**
 * Weiberlan
 * 07/12/2011
 */
	case 'cad039GridCertificadosDeInspecao':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D124_Id', $g['divId'])),
			//array('colunaValor' => "campoImpressão", 'colunaTipo' => "livre",'colunaTitulo' => 'Imprimir', 'callback' => 'campoImpressao'),
			array('colunaValor' => 'D124_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D124_Numero', 'colunaTitulo' => 'Número'),
			array('colunaValor' => 'D124_Emissao', 'colunaTitulo' => 'Emissão','callback' => 'gCorrigeData'),
			array('colunaValor' => 'D124_D106_Id', 'colunaTitulo' => 'IdBem', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
			array('colunaValor' => 'D124_Observacao', 'colunaTitulo' => 'Observações'),
		);
		 		
		/**
		* Definição da SQL
		*/
		$from = 'D124';
		$extra = <<<EOT
			LEFT JOIN D106 ON D106_Id=D124_D106_Id
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad039GridCertificadosInspecaoBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad039GridCertificadosInspecaoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Certificado de Inspeção', '/cad/cad039/content/cad039ContentCertificadosInspecaoIncluir/', '&tab=geral', [500,320]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D124_Numero' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'D124_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D106_Descricao' => array('titulo' => 'Bem', 'tipo' => 'texto'),
			'D124_Observacao' => array('titulo' => 'Observações', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Certificados de Inspeção<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'D124_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					var D124_D106_Id = id[1].replace('D124_D106_Id' + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Certificado de Inspeção - ID: ' + acaoId, '/cad/cad039/content/cad039ContentCertificadosInspecaoIncluir/', '&acaoId=' + encodeURIComponent(acaoId) + '&D124_D106_Id=' + encodeURIComponent(D124_D106_Id), [700,500]);
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
	
	case 'cad039GridEspecificacoes';
		$D106_Id = (isset($r_D124_D106_Id)) ? $r_D124_D106_Id : false;	  	
		$D124_Id = (isset($r_acaoId)) ? $r_acaoId : false;

		/**
		* Executa o script de inserção na tabela T160 (n:m)
		*/
		$CAD039->insereRegistrosT160($D106_Id,$D124_Id);
		
	    /**
		* Definição do Grid (Tabela)
		*/				
		
		$grid = array(
			array('colunaValor' => 'D121_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T160_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D121_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T160_Valor', 'colunaTitulo' => 'Valor', 'colunaEditar' => true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D121';
			
		$sql="SELECT D106_D128_Id FROM D106 WHERE D106_Id='{$D106_Id}'";
        $resultado=mysql_query($sql);
		$row=mysql_fetch_assoc($resultado);
               
       $extra = <<<EOT
       left join T160 on (T160_D121_Id=D121_Id and T160_D124_Id='{$D124_Id}')
       left join D124 on D124_Id=T160_D124_Id              
       where D121_D128_Id = '{$row['D106_D128_Id']}'
EOT;

		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D121_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T160_Valor' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
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
				var dbclickPrevent = false;
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
	
	case 'cad039GridAcessorios':

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
			WHERE T164_D124_Id = {$r_acaoId}
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
