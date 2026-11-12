<?php
namespace hardness;
/**
 * Testes - Weiber - Programa Teste Grid Contas a pagar
 *
 * select pego de: hardness\transportadora\clientes.php
 */

/**
 * Area para funções locais
 */
// Formata data para exibição

function nomeEmpresa($array) {
	return substr($array['todosCampos']['D022_Nome_Empresa'],0,25);
}
function nomeBairro($array){
	return substr($array['todosCampos']['D022_Bairro'],0,20);
}
function nomeCidade($array){
	return substr($array['todosCampos']['D020_Nome_Cidade'],0,20);
}


/**
 * Ações
 */
switch($g['r'][3]){
	// PADRÃO
	case 'divPadrao':
		$g['conteudo']['jsTexto'] = <<<EOT
			$(document).ready(function() {
				// Carrega Grid
				$.ajax({
					url: '/cad/cad004/grid/transportadoras/',
					dataType: 'html',
					data: 'ajax=true',
					success: function(data) {
						$('#divPadrao').html(data);
						return false;
					}
				});
			});
EOT;
		break;

		// GRID

	case 'transportadoras':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array(
                 'D022_Id', $g['divId']),
            ),
            array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Nome empresa', 'callback' => 'nomeEmpresa'),
            array('colunaValor' => 'D022_Bairro', 'colunaTitulo' => 'Bairro', 'callback' => 'nomeBairro'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'callback' => 'nomeCidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D022_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D022_Telefone_1', 'colunaTitulo' => 'Contato Direto', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D022_Telefone_2', 'colunaTitulo' => 'Contato Coleta', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D022_Fax', 'colunaTitulo' => 'Email', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D022_Complemento', 'colunaTitulo' => 'Contato', 'colunaExibirNivel' => 4),
			array('colunaValor' => 'D022_Taxa_Frete', 'colunaTitulo' => 'Taxa Frete','colunaAlinhar' => 'right','colunaEditarOculto' => true,'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D022_Descricao_Ecommerce', 'colunaTitulo' => 'Ecommerce', 'colunaExibir' => false),
						array('colunaValor' => 'D022A_Id', 'colunaTitulo' => 'D022A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
						array('colunaValor' => 'D022A_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
        );
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D022';


		$extra = <<<EOT
				left join D020 on D020_Id=D022_D020_Id
				left join D018 on D018_Id=D022_D018_Id
				left join D084 on D084_Id=D020_D084_Id
				left join D022A on D022_Id=D022A_D022_Id
				WHERE IFNULL(D022A_Flag_Ativo,'S') = 'S'
EOT;

		/**
		 * Botões
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad004Incluir')
		);
		echo Botoes::processar($botoes);


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad004Incluir">Incluir</button>
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad004Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transportadoras', '/cad/cad004/content/form1/', '', [800,500]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D022_Nome_Empresa' => array('titulo' => 'Nome empresa', 'tipo' => 'texto'),
			'D022_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D022_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D022_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
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
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Transportadoras<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
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
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D022_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[1].replace(chavePrimaria + '-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro de Transportadoras ' + acaoId, '/cad/cad004/content/form1/', '&acaoId=' + encodeURIComponent(acaoId) + '&D022_Id=' + encodeURIComponent(acaoId), [800,500]);
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

			case 'transportadorasInativas':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array(
                 'D022_Id', $g['divId']),
            ),
            array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Nome empresa', 'callback' => 'nomeEmpresa'),
            array('colunaValor' => 'D022_Bairro', 'colunaTitulo' => 'Bairro', 'callback' => 'nomeBairro'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'callback' => 'nomeCidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D022_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D022_Telefone_1', 'colunaTitulo' => 'Contato Direto', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D022_Telefone_2', 'colunaTitulo' => 'Contato Coleta', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D022_Fax', 'colunaTitulo' => 'Email', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D022_Complemento', 'colunaTitulo' => 'Contato', 'colunaExibirNivel' => 4),
			array('colunaValor' => 'D022_Taxa_Frete', 'colunaTitulo' => 'Taxa Frete','colunaAlinhar' => 'right','colunaEditarOculto' => true,'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D022_Descricao_Ecommerce', 'colunaTitulo' => 'Ecommerce', 'colunaExibir' => false),
						array('colunaValor' => 'D022A_Id', 'colunaTitulo' => 'D022A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
						array('colunaValor' => 'D022A_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
        );
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D022';


		$extra = <<<EOT
				left join D020 on D020_Id=D022_D020_Id
				left join D018 on D018_Id=D022_D018_Id
				left join D084 on D084_Id=D020_D084_Id
				left join D022A on D022_Id=D022A_D022_Id
				WHERE D022A_Flag_Ativo = 'N'
EOT;

		/**
		 * Botões
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad004Incluir')
		);
		echo Botoes::processar($botoes);


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad004Incluir">Incluir</button>
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad004Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'transportadorasInativas', '/cad/cad004/content/form1/', '', [800,500]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D022_Nome_Empresa' => array('titulo' => 'Nome empresa', 'tipo' => 'texto'),
			'D022_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D022_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D022_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
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
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Transportadoras<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
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
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D022_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[1].replace(chavePrimaria + '-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro de transportadorasInativas ' + acaoId, '/cad/cad004/content/form1/', '&acaoId=' + encodeURIComponent(acaoId) + '&D022_Id=' + encodeURIComponent(acaoId), [800,500]);
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

		case 'transportadorasEcommerce':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array(
                 'D022_Id', $g['divId']),
            ),
            array('colunaValor' => 'D168_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D168_D022_Id', 'colunaTitulo' => 'Código', 'colunaExibir' => false),
            array('colunaValor' => 'D168_Id_Transportadora_Ecommerce', 'colunaTitulo' => 'Código ecommerce'),
            array('colunaValor' => 'D168_Nome_Transportadora_Ecommerce', 'colunaTitulo' => 'Nome ecommerce'),            
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'callback' => 'nomeEmpresa'),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'Tipo'),
            
        );
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D168';


		$extra = <<<EOT
				left join D022 on D168_D022_Id=D022_Id
				left join D036 on D168_D036_Id=D036_Id
EOT;

		/**
		 * Botões
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad004IncluirIntelipost')
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad004IncluirIntelipost">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad004IncluirIntelipost').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transportadoras', '/cad/cad004/content/form2/', '', [500,500]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D022_Nome_Empresa' => array('titulo' => 'Nome empresa', 'tipo' => 'texto'),
			'D022_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D168_Id_Transportadora_Ecommerce' => array('titulo' => 'Código Ecommerce', 'tipo' => 'texto'),
			'D168_Nome_Transportadora_Ecommerce' => array('titulo' => 'Nome Ecommerce', 'tipo' => 'texto'),
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
		* Totais
		*/
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Transportadoras<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
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
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D168_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro de Transportadoras ' + acaoId, '/cad/cad004/content/form2/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
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
