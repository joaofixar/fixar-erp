<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/FIN007.php');
//$FIN007 = new FIN007();
// Carrega funções callback
require_once('prog/fin/fin007/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/fin/fin007/grid_func-js.php');
require_once("old/lib/funcoes.php");
require_once('prog/crm/crm001/grid_func-callback.php');
require_once('prog/crm/crm001/grid_func-js.php');
require_once('bibliotecas/funcoesCallback/montarInteracaoTarefa.php');

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	* Grid Principal
	* Chamado em: fin007tab01
	*/
	case 'fin007grid01':
		global $g;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'Não Liberar', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoNaoLiberar', 'callbackParameter' => array('T005_Id', $g['divId'])),
            array('colunaValor' => 'Liberar', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoLiberar', 'callbackParameter' => array('T005_Id', $g['divId'])),
            array('colunaValor' => 'T005_Flag_Aguardar_Liberacao', 'colunaTitulo' => '', 'callback' => 'validaCampoAguardar', 'callbackParameter' => array('T005_Id', $g['divId'])),
            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Número<br />Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D024_Flag_Curva_ABC', 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'if(T005_Flag_ACP="1","NF",if(T005_Flag_ACP="2","Romaneio",if(T005_Flag_ACP="3","ACP",if(T005_Flag_ACP="5","D1",if(T005_Flag_ACP="6","NFe","CF"))))) as Tipo_Documento', 'colunaTitulo' => 'Doc', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor<br />Pedido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'outrosPedidos', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Outros<br />Pedidos', 'callback' => 'calculaOutrosPedidos','colunaAlinhar' => 'right'),
            array('colunaValor' => 'Vencido','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Vencido', 'colunaAlinhar' => 'right', 'callback' => 'calculaValorVencido'),
            array('colunaValor' => 'Vencer','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />a Vencer', 'colunaAlinhar' => 'right','callback' => 'calculaValorVencer'),
        );
        if($g['C031']['somarLimiteCnpjMatriz'] == 'S'){
        	$grid[] = array('colunaValor' => 'Limite','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Limite','colunaAlinhar' => 'right','callback' => 'calculaValorLimite');
        } else {
        	$grid[] = array('colunaValor' => 'D024_Valor_Limite', 'colunaCasasDecimais' => 2, 'colunaTitulo' => 'Valor<br />Limite','colunaAlinhar' => 'right', 'colunaEditar' => true);
        }
		$grid1 = array(
			array('colunaValor' => 'saldoLimite', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Saldo Limite', 'callback' => 'calculaSaldoLimite', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D024_Valor_Limite_Manual', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Limite<br />Manual', 'colunaExibir'=>false),
            array('colunaValor' => 'formaPagamento', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Forma Pagamento', 'callback' => 'formaPagamento', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T005_Motivo_Pedido_Liberacao', 'colunaTitulo' => 'Motivo pedido liberação', 'style' => 'font-weight:bold;color: var(--BGVermelho)'),
            array('colunaValor' => 'T005_Observacao_Comercial', 'colunaTitulo' => 'Observação Comercial', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D024_Observacao_Financeiro', 'colunaTitulo' => 'Observação Financeira<br/>Comercial', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D024_Observacao_Administracao', 'colunaTitulo' => 'Observação Financeira<br/>Administração', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D024_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'sum(T002_Valor_Saldo) as Receber', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaExibir' => false),
            array('colunaValor' => 'Retornar', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoRetornar', 'callbackParameter' => array('T005_Id', $g['divId'])),
        );

		$grid = array_merge($grid,$grid1);

		$from = "T005";
		
		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
	    	left join T007 on T007_T005_Id=T005_Id
	    	left join T002 on T002_D024_Id=T005_D024_Id and T002_Data_Recebimento='0000-00-00' and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
	    	left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
	    	where (T005_C004_Id={$g['empresaAtual']}
	    	and T005_Flag_Status='6')
	    	GROUP BY T005_Id
EOT;
		
			
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin007LiberacaoAtivarMsg">Ativar Mensagens</button>
				<button type="button" id="fin007LiberacaoDesativarMsg">Desativar Mensagens</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin007LiberacaoAtivarMsg').unbind('click').bind('click', function(data) {
					setCookie('liberacaoCreditoFlag', 2, 0, true);
					dialogConfirm('Notificações', 'Ativado!<br />Você irá receber notificações de análise pendente em todo o sistema.');
				});
				$('#fin007LiberacaoDesativarMsg').unbind('click').bind('click', function(data) {
					setCookie('liberacaoCreditoFlag', 1, 0, true);
					dialogConfirm('Notificações', 'Desativado!<br />Você não irá mais receber notificações de análise pendente em todo o sistema.');
				});
			} </script></div>
EOT;
		*/

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
	        'T005_Id' => array('titulo' => 'Número Pedido', 'tipo' => 'numero'),
	        'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	        'D024_Nome_Empresa' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
	        'D024_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
	        'T005_Valor_Total' => array('titulo' => 'Valor Pedido', 'tipo' => 'moeda'),
	        'D024_Valor_Limite' => array('titulo' => 'Valor Limite', 'tipo' => 'moeda'),
	        'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
	        'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
	    );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/*
		* Não precisa totais nesta tela de análise de crédito
		* 
		* Botões (Versão Acima do GRID)
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="inputValorTotal" size="11" readonly></span>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// ..
			} </script></div>
EOT;
*/
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$colunaAprovar = base64_encode('Não Liberar');
		$colunaNegar = base64_encode('Liberar');
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setTimeout(function(){
					divRefresh('{$g['divId']}');
				}, 60000);
				$('#{$g['divId']} .grid th[valor="{$colunaAprovar}"]').removeAttr('onclick').css({'cursor': 'auto'});
				$('#{$g['divId']} .grid th[valor="{$colunaNegar}"]').removeAttr('onclick').css({'cursor': 'auto'});

				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + array['todosCampos']['T005_Id'], '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T005_Id']) + '&tabela={$from}', [950,615]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').unbind('click').bind('click', function(event) { return false; });
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
		
	case 'fin007grid02':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor'=>'D024_Flag_Curva_ABC', 'colunaTitulo'=>'ABC', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor'=>'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'Código', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Razão Social'),
			array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Bairro', 'colunaTitulo'=>'Bairro'),
			array('colunaValor'=>'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade'),
			array('colunaValor'=>'D018_UF', 'colunaTitulo'=>'UF'),
			array('colunaValor'=>'D046_Nome', 'colunaTitulo'=>'Grupo'),
			array('colunaValor'=>'D024_Flag_Liberado', 'colunaTitulo'=>'Créd.liberado(S/N)?', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
			array('colunaValor'=> 'D024_Valor_Limite', 'colunaTitulo'=>'Valor Limite', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true),		    
			array('colunaValor'=> 'D024_Observacao_Financeiro', 'colunaTitulo'=>'Observação Financeira<br/>Comercial', 'style' => 'font-weight:bold', 'colunaEditar' => true),
			array('colunaValor'=> 'D024_Observacao_Administracao', 'colunaTitulo'=>'Observação Financeira<br/>Administração', 'style' => 'font-weight:bold', 'colunaEditar' => true),
			array('colunaValor'=> 'D024_Flag_Protestar', 'colunaTitulo' => 'Protestar', 'callback' => 'checkboxProtestarCliente', 'style' => 'width: 1px;', 'colunaAlinhar' => 'center'),
			//array('colunaValor'=>'D084_Regiao', 'colunaTitulo'=>'Região'),
			array('colunaValor'=>'D024_CNPJ', 'colunaTitulo'=>'CNPJ'),
			array('colunaValor'=>'D024_CPF', 'colunaTitulo'=>'CPF'),
			array('colunaValor'=>'D024_RG', 'colunaTitulo'=>'RG'),
			array('colunaValor'=>'D024_Data_Cadastro', 'colunaTitulo'=>'Cadastro', 'callback' => 'gCorrigeData'),
			array('colunaValor'=>'D024_Flag_Pre_Cadastro', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Flag_Comercial', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Flag_Tempo', 'colunaTitulo'=>'', 'colunaExibir' => false),
		);
		
		$from = "D024";
		
		$extra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id
	    	left join D046 on D046_Id=D024_D046_Id
	    	left join D018 on D018_Id=D024_D018_Id
	    	left join D021 on D021_Id=D024_D021_Id
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
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
			'D024_Valor_Limite' => array('titulo' => 'Valor Limite', 'tipo' => 'moeda'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/*
		* Não precisa totais nesta tela de análise de crédito
		* 
		* Botões (Versão Acima do GRID)
			
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="inputValorTotal" size="11" readonly></span>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// ..
			} </script></div>
EOT;
*/
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
                    var id = $(this).closest('tr').attr('id').split('|');
                    var D024_Id = id[0].replace(/D024_Id-/, '');
                    var T005_Id = id[1].replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + T005_Id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [950,615]);            
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').unbind('click').bind('click', function(event) { return false; });
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


	case 'fin007grid03':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(	
			array('colunaValor'=> 'T007_Id', 'colunaTitulo'=>'ID', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor'=> 'D024_Id', 'colunaExibir'=>false, 'chavePrimaria'=>true), 
			array('colunaValor'=> 'Não Liberar', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'validaCampoNaoLiberarNota'),
			array('colunaValor'=> 'Liberar', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'validaCampoLiberarNota'),
			array('colunaValor'=> 'T007_Numero_Nota_Fiscal', 'colunaTitulo'=>'NF'),
			array('colunaValor'=> 'Extrato_Cliente', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'validaCampoExtratoCliente', 'colunaExibir'=>false),
			array('colunaValor'=> 'T007_Data_Emissao', 'colunaTitulo'=>'Emissão', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
		    array('colunaValor'=> 'D024_Nome_Empresa', 'colunaTitulo'=>'Razão Social', 'style' => 'font-weight:bold'),
		    array('colunaValor'=> 'D024_Flag_Curva_ABC', 'colunaTitulo'=>'ABC', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
		    array('colunaValor'=> 'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
		    array('colunaValor'=> 'T007_Valor_Total', 'colunaTitulo'=>'Valor<br />Nota', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
		    // para descomentar, precisa mudar callback para T007
		    //array('colunaValor'=> 'outrosPedidos', 'colunaTipo'=>'livre', 'colunaTitulo'=>'Outros<br />Pedidos', 'callback'=>'calculaOutrosPedidos'),
		    array('colunaValor'=> 'sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Vencido', 'colunaTitulo'=>'Valor<br />Vencido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		    array('colunaValor'=> 'sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Vencer', 'colunaTitulo'=>'Valor<br />a Vencer', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		    array('colunaValor'=> 'D024_Valor_Limite', 'colunaTitulo'=>'Valor<br />Limite', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		    // para descomentar, precisa mudar callback para T007
		    //array('colunaValor'=> 'saldoLimite', 'colunaTipo'=>'livre', 'colunaTitulo'=>'Valor<br />Saldo Limite', 'callback'=>'calculaSaldoLimite', 'colunaAlinhar' => 'right'),
		    array('colunaValor'=> 'formaPagamento', 'colunaTipo'=>'livre', 'colunaTitulo'=>'Forma Pagamento', 'callback'=>'formaPagamentoNota', 'style' => 'font-weight:bold'),
            array('colunaValor'=> 'D024_Observacao_Financeiro', 'colunaTitulo'=>'Observação Financeira', 'style' => 'font-weight:bold'),
		    array('colunaValor'=> 'C007_Primeiro_Nome', 'colunaTitulo'=>'Vendedor', 'style' => 'font-weight:bold'),
		    array('colunaValor'=> 'sum(T002_Valor_Saldo) as Receber', 'colunaExibir'=>false),
		);
		
		$from = "T007";
		
		$extra = <<<EOT
			left join D024 on D024_Id=T007_D024_Id
	    	left join T002 on T007_D024_Id=T007_D024_Id and T002_Data_Recebimento='0000-00-00' and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
	    	left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
	    	where (T007_C004_Id={$g['empresaAtual']}
	    	and T007_Flag_Analise_Credito='1')
	    	GROUP BY T007_Id
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
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número NF', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
			'T007_Valor_Total' => array('titulo' => 'Valor Nota', 'tipo' => 'moeda'),
			'D024_Valor_Limite' => array('titulo' => 'Valor Limite', 'tipo' => 'moeda'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Não precisa totais nesta tela de análise de crédito
		* 
		* Botões (Versão Acima do GRID)
		*/
		/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="inputValorTotal" size="11" readonly></span>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// ..
			} </script></div>
EOT;
*/
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
        	        var T007_Id = id[0].replace(/T007_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(T007_Id) + '&tabela={$from}', [900,550]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').unbind('click').bind('click', function(event) { return false; });
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
	 *Tab Demonstrativo
	 */
	case 'DemonstrativoValorLimite':
/**
		 * Definição do grid tabela
		 */
		global $g;
		$grid = array(
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true,),
	        array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
	        array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ'),
	        array('colunaValor' => 'D024_Cnpj_Matriz', 'colunaTitulo' => 'CNPJ Matriz'),
            array('colunaValor' => 'Pedidos', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Pedidos', 'callback' => 'calculaPedidos'),
            array('colunaValor' => 'Vencido','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Vencido', 'colunaAlinhar' => 'left', 'callback' => 'ValorVencido'),
            array('colunaValor' => 'Vencer','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />a Vencer', 'colunaAlinhar' => 'left','callback' => 'ValorVencer'),
            array('colunaValor' => 'D024_Valor_Limite', 'colunaTitulo' => 'Limite', 'colunaEditar' => true, 'colunaEditarTodos' => true,'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'saldoLimite', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Saldo Limite', 'callback' => 'SaldoLimite', 'colunaAlinhar' => 'left'),		        
	    );

		$from = "D024";
		$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$r_acaoId}'");
		$dadosCliente = mysql_fetch_array($dadosCliente);
		$filtroSaldo = "D024_Id = '{$r_acaoId}'";

		if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
			$filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
		}

		$extra = <<<EOT
	    	where $filtroSaldo
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
		$g['smarty']->display('grid.tpl');
	break;

	case 'DemonstrativoSaldoLimite':
		/**
		 * Definição do grid tabela
		 */
		global $g;
		$grid = array(
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true,),
	        array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
	        array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ'),
	        array('colunaValor' => 'D024_Cnpj_Matriz', 'colunaTitulo' => 'CNPJ Matriz'),
            array('colunaValor' => 'Pedidos', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Pedidos', 'callback' => 'calculaPedidos'),
            array('colunaValor' => 'Vencido','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Vencido', 'colunaAlinhar' => 'left', 'callback' => 'ValorVencido'),
            array('colunaValor' => 'Vencer','colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />a Vencer', 'colunaAlinhar' => 'left','callback' => 'ValorVencer'),
            array('colunaValor' => 'D024_Valor_Limite', 'colunaTitulo' => 'Limite', 'colunaEditar' => true, 'colunaEditarTodos' => true,'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'saldoLimite', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor<br />Saldo Limite', 'callback' => 'SaldoLimite', 'colunaAlinhar' => 'left'),		        
	    );

		$from = "D024";
		$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$r_acaoId}'");
		$dadosCliente = mysql_fetch_array($dadosCliente);
		$filtroSaldo = "D024_Id = '{$r_acaoId}'";

		if(!empty($dadosCliente['D024_Cnpj_Matriz'])){
			$filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
		}

		$extra = <<<EOT
	    	where $filtroSaldo
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
		$g['smarty']->display('grid.tpl');
	break;
}
