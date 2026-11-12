<?php
namespace hardness;

switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: cad011tab01
	 */
	case 'cad013GridAgendaContatos':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D117_Id', $g['divId'])),
		array('colunaValor' => 'D117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
		array('colunaValor' => 'D117_Nome', 'colunaTitulo' => 'Nome'),
		array('colunaValor' => 'D117_Telefone', 'colunaTitulo' => 'Telefone'),
		array('colunaValor' => 'D117_Email', 'colunaTitulo' => 'E-mail'),
		array('colunaValor' => 'D117_Endereco', 'colunaTitulo' => 'Endereço'),
		array('colunaValor' => 'D117_D020_Id', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
		array('colunaValor' => 'D018_Estado', 'colunaTitulo' => 'Estado'),
		array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
		array('colunaValor' => 'D117_D018_Id', 'colunaTitulo' => 'Estado', 'colunaExibir' => false),
		array('colunaValor' => 'D117_Cep', 'colunaTitulo' => 'CEP'),
		array('colunaValor' => 'D117_Observacao', 'colunaTitulo' => 'Observacao'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D117';

		$extra = <<<EOT
		left join D020 on D020_Id=D117_D020_Id
		left join D018 on D018_Id=D117_D018_Id
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad013content02Cadastro')
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad013content02Cadastro">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad013content02Cadastro').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Contatos', '/cad/cad013/content/cad013content02Cadastro/', '', [500,500]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D117_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'D117_Telefone' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
			'D117_Email' => array('titulo' => 'Email', 'tipo' => 'texto'),
			'D117_Endereco' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
			'D018_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D117_Cep' => array('titulo' => 'CEP', 'tipo' => 'texto'),
			'D117_Observacao' => array('titulo' => 'Observacao', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Contatos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D117_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Contatos - ID: ' + id, '/cad/cad013/content/cad013content02Cadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,550]);
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
		
	case 'cad013GridEmail':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Chave Primária
			array('colunaValor' => 'T139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'Data/Hora', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T139_Pasta', 'colunaTitulo' => 'Pasta'),
			array('colunaValor' => 'T139_Titulo', 'colunaTitulo' => 'Assunto'),
			array('colunaValor' => 'T139_De', 'colunaTitulo' => 'De'),
			array('colunaValor' => 'T139_Para', 'colunaTitulo' => 'Para'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			where T139_D117_Id='{$r_acaoId}'
EOT;


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006EnviarEmail2">Novo Email</button>
				<button type="button" id="etc006VerificarEmailsEnviadosCad913">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D117_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosCad913').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/atualizarEmails/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove(); 
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
				});
			} </script></div>
EOT;
	        /**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
		    $gridFiltro = array(
			   'T139_De' => array('titulo' => 'De', 'tipo' => 'texto'),
			   'T139_Para' => array('titulo' => 'Para', 'tipo' => 'texto'),
			   'T139_Data_Hora' => array('titulo' => 'Data', 'tipo' => 'data'),
		    );
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
	
			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			echo <<<EOT
			<script type="text/javascript">
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace('T139_Id-', '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=S&acaoId=' + encodeURIComponent(id), [900,550]);
				});
			</script>
			
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
