<?php
namespace hardness;
/**
 * CAD - CAD010 - Bens
 */
// Classes
require_once('bibliotecas/classes/CAD010.php');
$CAD010 = new CAD010();
// Carrega funções callback
require_once('prog/cad/cad010/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/cad/cad010/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: cad010tab01
	 */
	case 'cad010grid01':
		/**
		 * Definição do Grid (Tabela)
		 */                           
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao'),
            array('colunaValor' => 'D109_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Nome'),
            array('colunaValor' => 'D109_Funcao', 'colunaTitulo' => 'Função'),
            array('colunaValor' => 'D109_Cpf', 'colunaTitulo' => 'CPF'),
            array('colunaValor' => 'D109_Data_Admissao', 'colunaTitulo' => 'Admissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D109_Data_Demissao', 'colunaTitulo' => 'Demissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D109_DDD_Telefone', 'colunaTitulo' => 'DDDTelefone', 'colunaExibir' => false),
            array('colunaValor' => 'D109_Telefone', 'colunaTitulo' => 'Telefone', 'colunaExibir' => false),
            array('colunaValor' => 'Telefone', 'colunaTitulo' => 'Telefone', 'colunaTipo' => 'livre', 'callback' => 'campoTelefone'),
            array('colunaValor' => 'D109_D018_Id', 'colunaTitulo' => 'consultaCidade', 'colunaExibir' => false),
            array('colunaValor' => 'D109_D020_Id', 'colunaTitulo' => 'consultaUF', 'colunaExibir' => false),
            array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência', 'callback' => 'validaCampoCompetencia', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D109_Flag_Ativo', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
            array('colunaValor' => 'D109_Flag_ImpDigital', 'colunaTitulo' => 'Imp. Digital', 'callback' => 'impDigital', 'colunaTipo' => 'livre', 'style' => 'width: 1%'),
        );
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D109';

		$extra = <<<EOT
		LEFT JOIN D018 ON D018_Id=D109_D018_Id
		LEFT JOIN D020 ON D020_Id=D109_D020_Id	
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad010IncluirFuncionario')
		);
		echo Botoes::processar($botoes);


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad010IncluirFuncionario">Incluir</button>
				<button type="button" id="cad010LerImpDigital">Ler Digital</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad010IncluirFuncionario').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Funcionários', '/cad/cad010/content/cad010content02/', '', [500,600]);
				});
			} </script></div>
EOT;

		/*echo <<<'EOT'
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad010LerImpDigital').unbind('click').bind('click', function() {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
					var exe = window.Components.classes['@mozilla.org/file/local;1'].createInstance(Components.interfaces.nsILocalFile);
					exe.initWithPath("C:\\hardness\\hardnessLerDigital.exe");
					var run = window.Components.classes['@mozilla.org/process/util;1'].createInstance(Components.interfaces.nsIProcess);
					run.init(exe);
					var parameters = [""];
					run.run(false, parameters, parameters.length);
				});
			} </script></div>
EOT;*/
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D109_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'D109_Funcao' => array('titulo' => 'Função', 'tipo' => 'texto'),
			'D109_Rg' => array('titulo' => 'RG', 'tipo' => 'texto'),
			'D109_Cpf' => array('titulo' => 'CPF', 'tipo' => 'texto'),
			'D109_Data_Admissao' => array('titulo' => 'Admissão', 'tipo' => 'data'),
			'D109_Data_Demissao' => array('titulo' => 'Demissão', 'tipo' => 'data'),
			'D109_Telefone' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
			'D109_Email' => array('titulo' => 'E-mail', 'tipo' => 'texto'),
			'D109_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
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
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Funcionários<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/D109_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad010/content/cad010content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,600]);
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
		
	case 'cad010grid02':
		/**
		 * Definição do Grid (Tabela)
		 */
		 
		 //$retorno = $CAD010->atualizaTotalMensalGrid($form);
		 
		$grid = array(
			array('colunaValor' => "campoExclusaoCompetencia", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D115_Id',$g['divId'])),
			array('colunaValor' => 'D115_Id', 'colunaExibir' => false ,'colunaTitulo' => 'IdRelComp', 'chavePrimaria' => true),
			array('colunaValor' => 'D115_D110_Id','colunaExibir' => false, 'colunaTitulo' => 'Competência'),
			array('colunaValor' => 'D115_D109_Id','colunaExibir' => false, 'colunaTitulo' => 'Funcionario'),
			array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência'),
			array('colunaValor' => 'D110_Descricao', 'colunaTitulo' => 'Descrição'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D115';

		$extra = <<<EOT
					left join D110 on D110_Id=D115_D110_Id
					left join D109 on D109_Id=D115_D109_Id
					where D115_D109_Id={$_REQUEST['acaoId']}
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
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<script>
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D109_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad010/content/cad010content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,400]);
			});
		</script>
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
			
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<script>		
			$('#cad010Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Funcionários', '/cad/cad010/content/cad010content02/', '', [300,200]);
			});
		</script>
EOT;


		break;
		
		case 'cad010GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D123_Id', $g['divId'])),
			array('colunaValor' => 'D123_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D123_D109_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
			array('colunaValor' => 'D123_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'D123_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D123_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D123_Data_Validade', 'colunaTitulo' => 'Validade', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'D123';

			$extra = <<<EOT
				left join C007 on C007_Id=D123_C007_Id
				where 
				(D123_D109_Id = {$r_acaoId})
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
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D123_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
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

			$novoIdTelaForm = uniqid();
			
				/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
		
		case 'cad010GridHistorico':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T169_Id', $g['divId'])),
				array('colunaValor' => 'T169_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
				array('colunaValor' => 'T169_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
				array('colunaValor' => 'T169_D109_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
				array('colunaValor' => 'T169_T147_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
				array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
				array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo'),
				array('colunaValor' => 'T169_Descricao', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'T169_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T169';

			$extra = <<<EOT
				left join T147 on T147_Id=T169_T147_Id
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D116 on D116_Id=T147_D116_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				where
				(T169_D109_Id = {$r_acaoId})
EOT;

			/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Serviço', 'tipo' => 'texto'),
			'T169_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T169_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D123_Id-/, '');
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

			$novoIdTelaForm = uniqid();
			
			/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
}
?>
