<?php
namespace hardness;

// Carrega funções callback
require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author RubiaYwata
	* @since 16/11/2011
	*/                 
	case 'cad016GridVendedores':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C007_Id', $g['divId'])),
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'C007_Flag_Vendedor', 'colunaTitulo' => 'Supervisor/Vendedor', 'callback' => 'trataFlagSupervisorVendedor'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Nome Completo'), 
			array('colunaValor' => 'C007_Email', 'colunaTitulo' => 'E-mail'), 
//			array('colunaValor' => 'C007_Msn', 'colunaTitulo' => 'MSN'), 
			array('colunaValor' => 'C007_Telefone_Direto', 'colunaTitulo' => 'Telefone direto'), 
//			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Primeiro Nome'), 
//			array('colunaValor' => 'C007_Cargo', 'colunaTitulo' => 'Cargo'), 
//			array('colunaValor' => 'C007_C007_Id', 'colunaTitulo' => 'Supervisor', 'callback' => 'selecionaSupervisor'), 
//			array('colunaValor' => 'C007_Flag_Expedicao', 'colunaTitulo' => 'Expedição', 'callback' => 'trataFlagExpedicao'), 
//			array('colunaValor' => 'C007_Rg', 'colunaTitulo' => 'RG'), 
//			array('colunaValor' => 'C007_Cpf', 'colunaTitulo' => 'CPF'),  
//			array('colunaValor' => 'C007_Endereco', 'colunaTitulo' => 'Endereço'),  
//			array('colunaValor' => 'C007_Complemento', 'colunaTitulo' => 'Complemento'),  
//			array('colunaValor' => 'C007_Bairro', 'colunaTitulo' => 'Bairro'),  
//			array('colunaValor' => 'C007_Cidade', 'colunaTitulo' => 'Cidade'),  
//			array('colunaValor' => 'C007_Estado', 'colunaTitulo' => 'Estado'), 
//			array('colunaValor' => 'C007_Cep', 'colunaTitulo' => 'CEP'),  
//			array('colunaValor' => 'C007_Telefone', 'colunaTitulo' => 'Telefone'), 
//			array('colunaValor' => 'C007_Ativo', 'colunaTitulo' => 'Ativo', 'callback' => 'trataAtivo'),  
//			array('colunaValor' => 'C007_Especial', 'colunaTitulo' => 'Especial', 'callback' => 'trataEspecial'),  
//			array('colunaValor' => 'C007_Usuario_Ancora', 'colunaTitulo' => 'Usuário Âncora', 'callback' => 'trataUsuarioAncora'),    
		);

		/**
		* Definição da SQL
		*/
		$from = 'C007';
		$extra = <<<EOT
			ORDER BY C007_Nome
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad016GridVendedoresBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad016GridVendedoresBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Supervisores/Vendedores', '/cad/cad016/content/cad016ContentVendedoresIncluir/', '&tab=geral', [400,600]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'C007_Flag_Vendedor' => array('titulo' => 'Tipo (V/S)', 'tipo' => 'texto'),
			'C007_Flag_Vendedor' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Supervisor', 'value' => 'S'),
				array('title' => 'Vendedor', 'value' => 'V'),
			), 'naoGerarExtra' => false),
			'C007_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'C007_Email' => array('titulo' => 'E-mail', 'tipo' => 'texto'),
			'C007_Telefone_Direto' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Supervisores/Vendedores<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'C007_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Supervisor/Vendedor - ID: ' + acaoId, '/cad/cad016/content/cad016ContentVendedoresIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,600]);
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
