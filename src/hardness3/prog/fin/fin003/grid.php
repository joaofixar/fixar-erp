<?php
namespace hardness;

require_once('bibliotecas/classes/FIN003.php');
$FIN003 = new FIN003();

// Carrega funções do ajax
require_once('prog/fin/fin003/grid_func-js.php');

switch($g['r'][3]){

	case 'fin003GridBancos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Chave Primária
			array('colunaValor' => 'D007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Selecione a conta'),
			array('colunaValor' => 'D007_Numero_Agencia', 'colunaTitulo' => 'Agência'),
			array('colunaValor' => 'D007_Numero_conta', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'D007_Valor_Saldo', 'colunaTitulo' => 'Saldo Atual', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'D007_Nome_Contato', 'colunaTitulo' => 'Contato'),
			array('colunaValor' => 'D007_Telefone', 'colunaTitulo' => 'Telefone'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D007';
		$extra = <<<EOT
		LEFT JOIN C004 ON C004_Id=D007_C004_Id
		where D007_C004_Id={$g['empresaAtual']}
		AND D007_Flag_Ativo="S"
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
					// Deixa a linha em destaque
					var grid = $('#{$g['divId']} .grid');
					var classCss = 'linhaAzul linhaNegrito linhaFundoAzulClaro';
					grid.find('tbody > tr').removeClass(classCss);
					$(this).addClass(classCss);

					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D007_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('-');
					var acaoId = id[1].replace(chavePrimaria , '-', '');
					// Ação: recarregar GRID (com novo acaoId)
					divSetarAcaoId('{$r_divReload}', acaoId);
					divRefresh('{$r_divReload}');
					carregaInputsTotais(acaoId);
			    });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
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
         * Totais Almir
         */
        /*$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );*/

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

	case 'fin003GridLancamentos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		$D007_Id = $r_D007_Id;
		if (empty($D007_Id)) { $D007_Id = 1; }	// para funcionar a personalizaçãoGrid
		if($D007_Id){
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T019_Id', $g['divId']), 'colunaExibir' => 'S'),
            array('colunaValor' => 'T019_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:11px'),
            array('colunaValor' => 'T019_Data_Lancamento', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
            array('colunaValor' => 'T019_Referente', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T019_Complemento', 'colunaTitulo' => 'Complemento'),
            array('colunaValor' => 'T019_Valor_Lancamento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T019_Saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T019_Conciliado', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoConciliado', 'colunaEditarTodos' => true, 'colunaEditarExtra' => '1|0', 'colunaEditarTipo' => 'flag'),
            array('colunaValor' => 'IF(D027_Id > 0, D027_Portador, D031_Portador)', 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'T019_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T216_T215_Id', 'colunaTitulo' => 'Extrato', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Extrato', '/fin/fin003/content/fin003ContentTransacoes/', 'T216_T215_Id', '1000,600') ),
            array('colunaValor' => 'T216_Id', 'colunaTitulo' => 'Id Extrato', 'colunaAlinhar' => 'center' ),
        );

		$from = 'T019';
		$extra = <<<EOT
		    left join D024 on D024_Id=T019_D024_Id
			left join D027 on D027_Id=T019_D027_Id
			left join D031 on D031_Id=T019_D031_Id
			left join D014 on D014_Id=T019_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join T216 on T216_T019_Id=T019_Id
			where (T019_D007_Id='$D007_Id')
			ORDER BY T019_Data_Lancamento DESC, T019_Id DESC
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir Lançamento', 'id' => 'fin003BtnInserirLancamento'),
			array('titulo' => 'Incluir Transferência', 'id' => 'fin003BtnInserirTransferencia'),
			array('titulo' => 'Atualizar Saldos', 'id' => 'fin003BtnAtualizarSaldos'),
		);
		echo Botoes::processar($botoes);


		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="fin003BtnInserirLancamento">Incluir Lançamento</button>
				<button type="button" id="fin003BtnInserirTransferencia">Incluir Transferência</button>
				<button type="button" id="fin003BtnAtualizarSaldos">Atualizar Saldos</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin003BtnInserirLancamento').unbind('click').bind('click', function(event) {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/content/fin003ContentIncluirLancamento/', '&D007_Id=' + encodeURIComponent('$D007_Id') + '&acaoId=', [600,500]);
				});
				$('#fin003BtnInserirTransferencia').unbind('click').bind('click', function(event) {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transferência' , '/fin/fin003/content/fin003ContentIncluirTransferencia/', '&D007_Id=' + encodeURIComponent('$D007_Id') + '&acaoId=', [600,500]);
				});
				$('#fin003BtnAtualizarSaldos').unbind('click').bind('click', function(event) {
				var buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/fin/fin003/grid_func-ajax/atualizarSaldos/?ajax=true&D007_Id=' + encodeURIComponent('$D007_Id') + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if(request.data){
								dialogConfirm('OK!', request.data);
							}else{
								dialogConfirm('Erro!', request.data);
							}
							divRefresh('{$g['divId']}');
							divRefresh('{$r_divReload}'); 
							});
							$(this).dialog("destroy").remove();
					     },
						 "Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmar', "ATENCAO! Deseja atualizar saldo?", buttons);
				});
			} </script></div>
EOT;

		 /**
		* Filtro
		*/
		$gridFiltro = array(
			'T019_Referente' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T019_Complemento' => array('titulo' => 'Complemento', 'tipo' => 'texto'),
			'T019_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T019_Valor_Lancamento' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T019_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'T019_Conciliado' => array('titulo' => 'Conciliado', 'tipo' => 'select', 'select' =>
				array(
					array('title' => 'Todos', 'value' => ''),
					array('title' => 'Sim', 'value' => '1'),
					array('title' => 'Não', 'value' => '0'),
				)
			),
			'D014_Grupo_DRE' => array('titulo' => 'DRE', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Recebimentos', 'valor' => 'if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0)', 'titulo' => 'Recebimentos'),
			array('nome' => 'Pagamentos', 'valor' => 'if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0)', 'titulo' => 'Pagamentos'),
			array('nome' => 'Total_Resultado','tipo'=>'totais' ,'valor' => '{Recebimentos}+{Pagamentos}', 'titulo' => 'Resultado'),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/

		/*$sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(*),
												   sum(if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0)),
												   sum(if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0))", $from, $extra);

		list($valorTotalLancamentos,$valorTotalRecebimentos,$valorTotalPagamentos) = $sqlTotal;

		$valorTotalResultado    = ($valorTotalRecebimentos + $valorTotalPagamentos);
		$valorTotalLancamentos  = $valorTotalLancamentos;
		$valorTotalRecebimentos = gCorrigeNumero($valorTotalRecebimentos);
		$valorTotalPagamentos   = gCorrigeNumero($valorTotalPagamentos);
		$ResultadoTotal         = gCorrigeNumero($valorTotalResultado);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Lançamentos<br><input type="text" value="{$valorTotalLancamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Recebimentos<br><input type="text" value="{$valorTotalRecebimentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pagamentos<br><input type="text" value="{$valorTotalPagamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Resultado<br><input type="text" value="{$ResultadoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define a abertura do form
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
				var chavePrimaria = 'T019_Id';
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T019_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Lançamento - ID: ' + id, '/fin/fin003/content/fin003ContentEditaLancamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}&D007_Id='+{$D007_Id}, [600,500]);
				//var id = $(this).closest('tr').attr('id').split('-');
				//var acaoId = id[1].replace(chavePrimaria , '-', '');
				//divSetarAcaoId('{$r_divReload}', acaoId);
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/content/fin003ContentEditaLancamento/', '&tab=geral&T019_Id='+acaoId+'&acaoId='+acaoId+'&D007_Id='+{$D007_Id}, [450,400]);
			});
		} </script></div>
EOT;
	     /**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 3000);

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
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Conta.</div>';
		}
	break;

	case 'fin003GridExtratos':
		if(empty($r_D007_Id)){
			echo  '<div align="center"><br /><br /><br />Selecione uma Conta.</div>';
			break;
		}
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
	        array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T215_Id', $g['divId'])),
			array('colunaValor' => "T215_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T215_Data", 'colunaTitulo' => 'Data Import.', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T215_Data_Processado", 'colunaTitulo' => 'Data Process.', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T215_Nome_Arquivo", 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => "T215_Data_Inicio", 'colunaTitulo' => 'Inicio Trans.', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T215_Data_Fim", 'colunaTitulo' => 'Fim Trans.', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T215_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => "T215_Data_Saldo", 'colunaTitulo' => 'Data Saldo', 'callback' => 'gCorrigeData'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T215';

		$extra = <<<EOT
			where (T215_D007_Id='{$r_D007_Id}')
			order by T215_Data DESC
EOT;
		/**
		* Define a abertura do form
		*/

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				if (dbclickPrevent) { return false; } dbclickPrevent = true; setTimeout(function() { dbclickPrevent = false; }, 500);
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T215_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Conciliação bancária', '/fin/fin003/content/fin003ContentTransacoes/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,600]);
			});
		} </script></div>
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

	case 'fin003GridTransacoes':
		/**
		* Definição do Grid (Tabela)
		*/

		mysqli_query("update T216 set T216_Flag_Importar='I' where T216_T215_Id = '{$r_acaoId}' and ifnull(T216_T019_Id,0)>0");
		mysqli_query("update T216 set T216_Flag_Importar=''  where T216_T215_Id = '{$r_acaoId}' and ifnull(T216_T019_Id,0)<=0");

		$grid = array(
	        array('colunaValor' => 'T216_Flag_Excluido', 'callback' => 'excluirTransacaoExtrato', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'campoDuplicar', 'colunaTitulo' => '', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDuplicarTransacao', 'style'=>'width:1%;'),
			array('colunaValor' => "T216_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T216_D027_Id", 'colunaTitulo' => 'D027_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "T216_D031_Id", 'colunaTitulo' => 'D031_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "T216_Data", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			array('colunaValor' => "T216_Valor", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'style' => 'width: 1%;background-color: var(--celula-amarelo-claro);font-weight:bold', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "T216_Flag_Importar", 'colunaTitulo' => 'P', 'callback' => 'campoMarcarRegistroExtrato', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			array('colunaValor' => 'campoDesmembrar', 'colunaTitulo' => '', 'colunaTipo'=>'livre', 'callback'=>'campoDesmembrarTransacao', 'style'=>'width:1%;'),
			array('colunaValor' => "T216_Descricao", 'colunaTitulo' => 'Descricao', 'colunaEditar' => true),
			array('colunaValor' => "CONCAT(IFNULL(D027_Portador,''),IFNULL(D031_Portador,''))", 'colunaTitulo' => 'Portador', 'style' => 'width: 1%'),
			array('colunaValor' => "D032_Descricao", 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%'),
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao,' - ',D014_SubConta),D014_SubConta) as D014_SubConta", 'colunaTitulo' => 'Conta', 'style' => 'width: 1%'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente / Forn', 'style' => 'width: 1%'),
			array('colunaValor' => 'T216_Historico', 'colunaTitulo' => 'Obs'),
			array('colunaValor' => 'if(T002_Numero_Documento="",T015_Numero_Duplicata,T002_Numero_Documento)', 'colunaTitulo' => 'Receber', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Título a Receber', '/fin/fin001/content/fin001content02/', 'T002_Id', '1050,540')),
			array('colunaValor' => 'if(T015_Numero_Documento="",T015_Numero_Duplicata,T015_Numero_Documento)', 'colunaTitulo' => 'Pagar', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Título a Pagar', '/fin/fin002/content/fin002content02/', 'T015_Id', '800,500')),
			array('colunaValor' => "T216_T019_Id", 'colunaTitulo' => 'Banco', 'colunaAlinhar' => 'center', 'callback' => 'desvincularLancamentoTransacao'),
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'T002_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "T015_Id", 'colunaTitulo' => 'T015_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T216';

		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id = T216_D024_Id
			LEFT JOIN D014 ON D014_Id = T216_D014_Id
			LEFT JOIN D154 ON D154_Id = D014_D154_Id
			LEFT JOIN D027 ON D027_Id = T216_D027_Id
			LEFT JOIN D032 ON D032_Id = T216_D032_Id
			LEFT JOIN D031 ON D031_Id = T216_D031_Id
			LEFT JOIN T002 ON T002_Id = T216_T002_Id
			LEFT JOIN T015 ON T015_Id = T216_T015_Id
			WHERE T216_T215_Id = '{$r_acaoId}'
			AND T216_Flag_Excluido = 'N'
			ORDER BY T216_Data, T216_Historico
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T216_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T216_Valor' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'D027_Portador,D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'T216_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'T216_Historico' => array('titulo' => 'Obs', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define a abertura do form
		*/

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				if (dbclickPrevent) { return false; } dbclickPrevent = true; setTimeout(function() { dbclickPrevent = false; }, 500);
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T216_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Transação Bancária', '/fin/fin003/content/fin003ContentEditarTransacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [550,250]);
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

	case 'fin003GridRegrasExtrato':
		$T215 = mysql_fetch_array(mysql_query("SELECT T215_D007_Id FROM T215 WHERE T215_Id = '{$r_acaoId}'"));
		$D007_Id = $T215['T215_D007_Id'];
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T217_Id', $g['divId'])),
			array('colunaValor' => "T217_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor' => "T217_Descricao", 'colunaTitulo' => 'Descricao', 'colunaEditar' => false),
			array('colunaValor' => 'CONCAT(IFNULL(D027_Portador,""),IFNULL(D031_Portador,""))', 'colunaTitulo' => 'Portador', 'style' => 'width: 1%'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta', 'colunaTitulo' => 'Conta', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente / Forn'),
			array('colunaValor' => 'T217_Historico', 'colunaTitulo' => 'Obs. Banco', 'colunaEditar' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T217';

		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id = T217_D024_Id
			LEFT JOIN D014 ON D014_Id = T217_D014_Id
			LEFT JOIN D154 ON D154_Id = D014_D154_Id
			LEFT JOIN D027 ON D027_Id = T217_D027_Id
			LEFT JOIN D032 ON D032_Id = T217_D032_Id
			LEFT JOIN D031 ON D031_Id = T217_D031_Id
			WHERE T217_D007_Id = '{$D007_Id}'
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin003incluirRegraExtrato">Incluir Regra</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin003incluirRegraExtrato').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir regra', '/fin/fin003/content/fin003ContentEditarRegra/', '&D007_Id=' + encodeURIComponent('{$D007_Id}') + '&tabela=T217', [600,200]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T217_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'T217_Historico' => array('titulo' => 'Obs. Banco', 'tipo' => 'texto')
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T217_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar regra - ID: ' + id, '/fin/fin003/content/fin003ContentEditarRegra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,200]);
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

	case 'fin003GridTransacoesExcluidas':
		/**
		* Definição do Grid (Tabela)
		*/

		$grid = array(
	        array('colunaValor' => 'T216_Flag_Excluido', 'callback' => 'excluirTransacaoExtrato', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'excluirDefinitivo', 'callback' => 'gExcluirLinha', 'colunaAlinhar' => 'center', 'style' => 'width:1%','callbackParameter' => array('T216_Id', $g['divId']), 'colunaTipo' => 'livre', 'colunaExibir' => false),
			array('colunaValor' => "T216_Id", 'colunaTitulo' => 'Id','chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T216_D027_Id", 'colunaTitulo' => 'D027_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "T216_D031_Id", 'colunaTitulo' => 'D031_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "T216_Data", 'colunaTitulo' => 'Data Trans.', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			array('colunaValor' => "T216_Valor", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'style' => 'width: 1%;background-color: var(--celula-amarelo-claro);font-weight:bold', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "T216_Descricao", 'colunaTitulo' => 'Descricao'),
			array('colunaValor' => "CONCAT(IFNULL(D027_Portador,''),IFNULL(D031_Portador,''))", 'colunaTitulo' => 'Portador', 'style' => 'width: 1%'),
			array('colunaValor' => "D032_Descricao", 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%'),
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao,' - ',D014_SubConta),D014_SubConta) as D014_SubConta", 'colunaTitulo' => 'Conta', 'style' => 'width: 1%'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente / Forn', 'style' => 'width: 1%'),
			array('colunaValor' => 'T216_Historico', 'colunaTitulo' => 'Obs'),
			array('colunaValor' => 'if(T002_Numero_Documento="",T015_Numero_Duplicata,T002_Numero_Documento)', 'colunaTitulo' => 'Tit. Receber', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Título a Receber', '/fin/fin001/content/fin001content02/', 'T002_Id', '1050,540')),
			array('colunaValor' => 'if(T015_Numero_Documento="",T015_Numero_Duplicata,T015_Numero_Documento)', 'colunaTitulo' => 'Tit. Pagar', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Título a Pagar', '/fin/fin002/content/fin002content02/', 'T015_Id', '800,500')),
			array('colunaValor' => "T216_T019_Id", 'colunaTitulo' => 'Banco', 'colunaAlinhar' => 'center', 'callback' => 'desvincularLancamentoTransacao'),
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'T002_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "T015_Id", 'colunaTitulo' => 'T015_Id','colunaAlinhar' => 'center', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T216';

		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id = T216_D024_Id
			LEFT JOIN D014 ON D014_Id = T216_D014_Id
			LEFT JOIN D154 ON D154_Id = D014_D154_Id
			LEFT JOIN D027 ON D027_Id = T216_D027_Id
			LEFT JOIN D032 ON D032_Id = T216_D032_Id
			LEFT JOIN D031 ON D031_Id = T216_D031_Id
			LEFT JOIN T002 ON T002_Id = T216_T002_Id
			LEFT JOIN T015 ON T015_Id = T216_T015_Id
			WHERE T216_T215_Id = '{$r_acaoId}'
			AND T216_Flag_Excluido = 'S'
			ORDER BY T216_Data
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define a abertura do form
		*/

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				if (dbclickPrevent) { return false; } dbclickPrevent = true; setTimeout(function() { dbclickPrevent = false; }, 500);
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T216_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Transação Bancária', '/fin/fin003/content/fin003ContentEditarTransacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [550,250]);
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

	case 'fin003GridDesmembrarTransacao':
	    /**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "T015_Flag_Marcar_Lancamento", 'colunaTitulo' => '', 'callback' => 'campoMarcarDesmembramento', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			array('colunaValor' => 'T015_Flag_Reparcelar', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor' => 'T216_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T216_T215_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli'),
			array('colunaValor' => "concat(T015_Numero_Parcela,'/',T015_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
			array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'IFNULL(T015_Data_Pagamento, "0000-00-00") as T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'colunaExibir' => false),
			array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
			array('colunaValor' => "T015_Observacao", 'colunaTitulo' => 'Observação'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT
				left join D024 on D024_Id=T015_D024_Id
				left join D073 on D073_Id=T015_D073_Id
				left join D031 on D031_Id=T015_D031_Id
				left join D014 on D014_Id=T015_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				LEFT JOIN D032 ON D032_Id=D014_D032_Id
				left join T007 on T007_Id=T015_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				left join T216 on T015_Id=T216_T015_Id
				WHERE (T015_C004_Id={$g['empresaAtual']})
	            AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) GROUP BY T015_Id
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
	        'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
	        'D032_Descricao' => array('titulo' => 'Grupo conta', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad034/grid/cad034GridGruposPlanoContas/', 'campo' => 'D032_Descricao', 'tamanho' => 20)),
	        'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
	        'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	        'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
	        'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
	        'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
	        'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda'),
	        'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
	        'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
	        'D073_Nome' => array('titulo' => 'Centro custo', 'tipo' => 'texto'),
	        'T015_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
	        'T015_Numero_Documento' => array('titulo' => 'N.Doc.', 'tipo' => 'texto'),
	        'T015_Flag_Marcar_Lancamento' => array('titulo' => 'Marcado (S/N)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin002Incluir">Incluir</button>                
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '', [600,500]);
				});	
				
			} </script></div>
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T015_Data_Pagamento']!='0000-00-00'){
				return 'linhaCinza';
	        }
		}

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		 * Totalizadores
		*/
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, str_replace('GROUP BY T015_Id', '',  $extra));
		$T015_Data_Vencimento = $g['ui']->getGridFiltroCampoBusca('T015_Data_Vencimento');
		$T015_Data_Vencimento0 = gCorrigeDataInverte($T015_Data_Vencimento[0]);
		$T015_Data_Vencimento1 = gCorrigeDataInverte($T015_Data_Vencimento[1]);
		$extraProjetado = '';
		if(!empty($T015_Data_Vencimento0) && empty($T015_Data_Vencimento1)){
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}'";
		} else if(empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}'";
		} else if(!empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}' AND STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}'";
		}
		if($extraProjetado != ''){
			$extraProjetado = gInsertExtraWhere($extraProjetado, "T018_C004_Id = '{$g['empresaAtual']}' AND T018_Valor_Saldo > 0 AND (T018_Ano > 0 AND T018_Mes > 0 AND D014_Dia_Vencimento > 0) AND IF((T018_Valor_Pagar > 0 OR T018_Valor_Pago > 0) AND D014_Flag_Pagamento_Unico = 'S', 1=2, 1=1)");
		}
		$sqlProjetado = mysql_query("SELECT SUM(T018_Valor_Saldo) as Saldo FROM T018 LEFT JOIN D014 ON T018_D014_Id=D014_Id {$extraProjetado}");
		$projetado = mysql_fetch_array($sqlProjetado);
		if($extraProjetado == ''){ $projetado['Saldo'] = 0; }
		$valorProjetado = gCorrigeNumero($projetado['Saldo']);
		$extraProjetado = base64_encode($extraProjetado);
		if($projetado['Saldo'] > 0){
			$valorProjetadoAttr = <<<EOT
				style="background-color: var(--BGVermelho); color: white; cursor: pointer;"
				onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Projeção Orçamentária', '/fin/fin002/content/fin002ContentProjecoes/', '&extra=' + encodeURIComponent('{$extraProjetado}'), [800,500]);"
EOT;
		}
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Duplicata)", $from, str_replace('GROUP BY T015_Id', '',  $extra)));
		$valorTotalSelecionado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Duplicata)", $from, gInsertExtraWhere(str_replace('GROUP BY T015_Id', '',  $extra), "T015_Flag_Marcar_Lancamento = 'S'")));
		/**
		* Botões (Versão Acima do GRID)
		*/
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="8" readonly></span>
				<span class="item" style="text-align:right">Projetado<br><input type="text" class="whiteBg" id="inputProjetado" size="10" value="{$valorProjetado}" readonly {$valorProjetadoAttr}></span>
				<span class="item" style="text-align:right">Total<br><input type="text" class="whiteBg" id="inputProjetado" size="10" value="{$valorTotal}" readonly></span>
				<span class="item" style="text-align:right">Selecionado<br><input type="text" class="whiteBg" id="inputProjetado" size="10" value="{$valorTotalSelecionado}" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        }

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
			});
		} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$extra_ = base64_encode($extra);

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

		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button id="desmembrarTransacao">Desmembrar Transação</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#desmembrarTransacao').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin003/grid_func-ajax/desmembrarTransacao/?ajax=true&T216_Id=' + encodeURIComponent("{$r_T216_Id}") + '&extra=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if(request.code == true){
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Operação Ok', 'Transação desmembrada com sucesso!', \$buttons);
						    		divRefresh('{$r_divIdRefresh}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
							});
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Desmembrar Transação', 'Deseja desmembrar a transação pelos títulos selecionados? ', \$buttons);
				});
			}</script></div>
EOT;
        }
	break;

	case 'fin003GridDesmembrarTransacaoRecebida':
	    /**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "T002_Flag_Marcar_Lancamento", 'colunaTitulo' => '', 'callback' => 'campoMarcarDesmembramentoRecebida', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor' => 'T216_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T216_T215_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli'),
			array('colunaValor' => "concat(T002_Numero_Parcela,'/',T002_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
			array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'Observação'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T002';

		$extra = <<<EOT
				left join D024 on D024_Id=T002_D024_Id
				left join D073 on D073_Id=T002_D073_Id
				left join D027 on D027_Id=T002_D027_Id
				left join D014 on D014_Id=T002_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				left join T007 on T007_Id=T002_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				left join T216 on T002_Id=T216_T002_Id
				WHERE (T002_C004_Id={$g['empresaAtual']})
	            AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T002_Data_Recebimento']!='0000-00-00'){
				return 'linhaCinza';
	        }
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
	        'T002_Numero_Duplicata,T002_Observacao_Reparcelamento' => array('titulo' => 'Número', 'tipo' => 'texto'),
	        'T002_Valor_Duplicata,T002_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
	        'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	        'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
	        'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
	        'T002_Data_Baixa' => array('titulo' => 'Baixa', 'tipo' => 'data'),
	        'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
	        'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
	        'T002_Flag_Marcar_Lancamento' => array('titulo' => 'Marcado (S/N)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = base64_encode($extra);


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="fin001Incluir">Incluir</button>
		</div>

		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#fin001Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber', '/fin/fin001/content/fin001content02/', '', [1050,500]);
				});			
		} </script></div>
EOT;

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		 * Totalizadores
		*/
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T002_Valor_Duplicata)", $from, $extra));
		$valorTotalSelecionado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T002_Valor_Duplicata)", $from, gInsertExtraWhere($extra, "T002_Flag_Marcar_Lancamento = 'S'")));
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="8" readonly></span>
				<span class="item" style="text-align:right">Total<br><input type="text" class="whiteBg" id="inputProjetado" size="10" value="{$valorTotal}" readonly></span>
				<span class="item" style="text-align:right">Selecionado<br><input type="text" class="whiteBg" id="inputProjetado" size="10" value="{$valorTotalSelecionado}" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
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
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1050,540]);
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


		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button id="desmembrarTransacaoRecebida">Desmembrar Transação</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#desmembrarTransacaoRecebida').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin003/grid_func-ajax/desmembrarTransacaoRecebida/?ajax=true&T216_Id=' + encodeURIComponent("{$r_T216_Id}") + '&extra=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if(request.code == true){
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Operação Ok', 'Transação desmembrada com sucesso!', \$buttons);
						    		divRefresh('{$r_divIdRefresh}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
							});
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Desmembrar Transação', 'Deseja desmembrar a transação pelos títulos selecionados? ', \$buttons);
				});
			}</script></div>
EOT;
        }
	break;
}
