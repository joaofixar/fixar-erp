<?php
namespace hardness;
//--Carrega funções callback
require_once('prog/cad/cad022/grid_func-callback.php');
require_once('prog/cad/cad022/grid_func-js.php');


switch($g['r'][3]){
	/**
	* Definição do Grid (Tabela)
	*/
	case 'cad022GridPortadoresReceber':
		$grid = array(
            array('colunaValor' => 'D027_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => '', 'colunaTitulo' => 'Comercial', 'colunaTipo' => 'livre', 'callback' => 'validaCampoComercial', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D027_Id', $g['divId'])),
            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'campoTipo'),
            //array('colunaValor' => 'D027_Flag_Prazo', 'colunaTitulo' => 'Venda a prazo', 'callback' => 'campoVendaPrazo'),
            array('colunaValor' => 'D027_Flag_Boleto', 'colunaTitulo' => 'Emissão de boleto', 'callback' => 'campoBoleto'),
            array('colunaValor' => 'D027_Flag_Cupom', 'colunaTitulo' => 'PDV', 'callback' => 'campoCupom'),
            array('colunaValor' => 'IF(D027_Flag_Cupom_Gaveta = \'S\', \'Sim\', \'Nao\')', 'colunaTitulo' => 'Gaveta'),
            array('colunaValor' => 'D027_Valor_Minimo_Parcela', 'colunaTitulo' => 'Valor Mínimo Parcela', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D027_Acrescimo_IPV', 'colunaTitulo' => '% Acrescimo IPV', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D027_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'D027_Flag_Comercial', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D027_Descricao_Ecommerce', 'colunaTitulo' => 'Ecommerce', 'colunaExibir' => false),
			array('colunaValor' => 'D027_Tipo_Forma_Pagamento_NFCE', 'colunaTitulo' => 'Forma Pagamento', 'callback' => 'campoFormaPagamento'),
			array('colunaValor' => 'D027_Cnpj_Instituicao_Pagamento', 'colunaTitulo' => 'Cnpj Insti. Pagamento'),
			array('colunaValor' => 'D027_Cnpj_Intermediador_Transacao', 'colunaTitulo' => 'Cnpj Inter. Transacao'),			
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "D027";	
		$extra = <<<EOT
		LEFT JOIN D007 ON D007_Id=D027_D007_Id
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad022Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad022Incluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad022Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Portador', '/cad/cad022/content/cad022contentCadastroPortador/','', [800,410]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D027_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'D007_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto')
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D027_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Portador - ID: ' + id, '/cad/cad022/content/cad022contentCadastroPortador/', '&acaoId=' + encodeURIComponent(id) + '&D027_Id=' + encodeURIComponent(id) +'&tabela={$from}', [600,550]);
			});
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
  	break;
}
?>
