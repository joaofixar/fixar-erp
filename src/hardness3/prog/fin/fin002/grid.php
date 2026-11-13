<?php
namespace hardness;
/**
 * FIN - FIN002 - Contas a Pagar
 */
// Classes
require_once('bibliotecas/classes/FIN002.php');
$FIN002 = new FIN002();
// Carrega funções callback
require_once('prog/fin/fin002/grid_func-callback.php');
require_once('prog/fin/fin009/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/fin/fin002/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: fin002tab01
	 */
	case 'fin002grid01':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(			
            array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T015_Id', $g['divId']), 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'style' => 'width:1%', 'colunaExibir' => true),
            array('colunaValor' => 'T015_Flag_Boleto', 'colunaTitulo' => 'Ok', 'callback' => 'validaCampoFlagBoleto', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Flag_Reparcelar', 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarTodosTriggerPHP' => false, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(D024_Cnpj,D024_Cpf)', 'colunaTitulo' => 'CNPJ/CPF', 'colunaExibir' => false),
            array('colunaValor' => 'T015_D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor','colunaDimensao' => 'S'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta','colunaDimensao' => 'S','colunaExibir' => false),
			//array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ','), 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T015_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
            array('colunaValor' => 'concat(T015_Numero_Parcela,\'/\',T015_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaDimensao' => 'S'),
            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
			array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaDimensao' => 'S'),
			array('colunaValor' => 'datediff(if(T015_Data_Pagamento=\'0000-00-00\',curdate(),T015_Data_Pagamento),T015_Data_Vencimento) as Atraso', 'colunaTitulo' => 'Ats', 'style' => 'width:1%;'),
            array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaDimensao' => 'S'),
            array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S','colunaTotal' => true),
            array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%','colunaMetrica' => 'S','colunaTotal' => true),
            array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S','colunaTotal' => true),
            array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
			array('colunaValor' => 'T015_C004_Id', 'colunaTitulo' => 'T015_C004_Id', 'style' => 'width:1%', 'colunaExibir'=> false),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador','colunaDimensao' => 'S', 'colunaExibir'=> false),
			array('colunaValor' => 'T015_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador','ORDER BY D031_Portador'), 'callback' => 'mostrarPortador', 'style' => 'width:1%'),
            array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo','colunaDimensao' => 'S'),
            array('colunaValor' => 'T015_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T015_Codigo_Barras', 'colunaTitulo' => 'Boleto'),
            array('colunaValor' => 'T015_T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Observacao_Reparcelamento', 'colunaTitulo' => 'T015_Observacao_Reparcelamento', 'colunaExibir' => false),
            array('colunaValor' => 'T015_T015_Id_Agrupado', 'colunaTitulo' => 'T015_T015_Id_Agrupado', 'colunaExibir' => false),
			array('colunaValor' => 'IF(T015_Data_Pagamento = "0000-00-00" and T015_Data_Vencimento<curdate(),T015_Valor_Saldo,0) as Total_Vencido', 'colunaTitulo' => 'Vencido', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
			array('colunaValor' => 'IF(T015_Data_Pagamento = "0000-00-00" and T015_Data_Vencimento>=curdate(),T015_Valor_Saldo,0) as Total_Vencer', 'colunaTitulo' => 'A Vencer', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
        );

		if (modoModelo('Contas')) {
        }

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT
			-- JOIN
				LEFT JOIN D024 ON D024_Id=T015_D024_Id
				LEFT JOIN D073 ON D073_Id=T015_D073_Id
				LEFT JOIN D031 ON D031_Id=T015_D031_Id
				LEFT JOIN D014 ON D014_Id=T015_D014_Id
				LEFT JOIN D032 ON D032_Id=D014_D032_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				LEFT JOIN T007 ON T007_Id=T015_T007_Id
				LEFT JOIN C004 ON C004_Id=T015_C004_Id
				LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			-- WHERE
				WHERE (1=1) AND IF('{$g['C031']['contasPagarUnificado']}' != 'S', T015_C004_Id = '{$g['empresaAtual']}', '1=1')
				AND IF('{$g['C031']['exibirTitulosPagarAgrupados']}' = 'S',1=1,(T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL))
				AND T015_Flag_Cancelada != 'S'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T015_T015_Id_Agrupado'] > 0){
				return 'linhaLaranja';
			} else if($array['todosCampos']['T015_Data_Pagamento']!='0000-00-00'){
				return 'linhaCinza';
			} 
		}

		/**
		* Filtro
		*/

		$gridModelos = array(
    		'Contas' => 'Contas',
			'Vencimento' => 'Vencimento',
			'Fornecedor' => 'Fornecedor'
    	);

		if (modoModelo('Contas')) {

		    $gridFiltro = array(
				'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
	                array('title' => 'Plano de Conta', 'campoAgrupar' => 'D014_SubConta', 'campoMostrar' => 'D014_SubConta', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
	              ),
	            ),
				'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
				'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
				'D032_Descricao' => array('titulo' => 'Grupo conta', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad034/grid/cad034GridGruposPlanoContas/', 'campo' => 'D032_Descricao', 'tamanho' => 20)),
				'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
				'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
				'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
				'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
				'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
				'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda','colunaTotal' => true),
				'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda','colunaTotal' => true),
				'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
				'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
				'T015_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
				'T015_Numero_Documento' => array('titulo' => 'N.Doc.', 'tipo' => 'texto'),
				'T015_Flag_Reparcelar' => array('titulo' => 'Reparcelar?(S/N)', 'tipo' => 'texto'),
			);
	    }else if (modoModelo('Vencimento')) {

		    $gridFiltro = array(
				'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
	                array('title' => 'Vencimento', 'campoAgrupar' => 'T015_Data_Vencimento', 'campoMostrar' => 'T015_Data_Vencimento', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
	              ),
	            ),
				'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
				'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
				'D032_Descricao' => array('titulo' => 'Grupo conta', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad034/grid/cad034GridGruposPlanoContas/', 'campo' => 'D032_Descricao', 'tamanho' => 20)),
				'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
				'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
				'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
				'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
				'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
				'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda','colunaTotal' => true),
				'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda','colunaTotal' => true),
				'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
				'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
				'T015_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
				'T015_Numero_Documento' => array('titulo' => 'N.Doc.', 'tipo' => 'texto'),
				'T015_Flag_Reparcelar' => array('titulo' => 'Reparcelar?(S/N)', 'tipo' => 'texto'),
			);
	    } else if (modoModelo('Fornecedor')) {

		    $gridFiltro = array(
				'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
	                array('title' => 'Fornecedor', 'campoAgrupar' => 'T015_D024_Id', 'campoMostrar' => 'D024_Nome_Empresa', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
	              ),
	            ),
				'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
				'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
				'D032_Descricao' => array('titulo' => 'Grupo conta', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad034/grid/cad034GridGruposPlanoContas/', 'campo' => 'D032_Descricao', 'tamanho' => 20)),
				'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
				'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
				'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
				'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
				'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
				'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda','colunaTotal' => true),
				'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda','colunaTotal' => true),
				'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
				'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
				'T015_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
				'T015_Numero_Documento' => array('titulo' => 'N.Doc.', 'tipo' => 'texto'),
				'T015_Flag_Reparcelar' => array('titulo' => 'Reparcelar?(S/N)', 'tipo' => 'texto'),
			);
	    }else {
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
				'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
				'T015_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
				'T015_Numero_Documento' => array('titulo' => 'N.Doc.', 'tipo' => 'texto'),
				'T015_Flag_Reparcelar' => array('titulo' => 'Reparcelar?(S/N)', 'tipo' => 'texto'),
			);

	    }


		
		if($g['C031']['exibirTitulosPagarAgrupados'] == 'S'){
			$gridFiltro['IF(T015_T015_Id_Agrupado > 0[[virgula]]"S"[[virgula]]"N")'] = array('titulo' => 'Reparcelado', 'tipo' => 'select', 'select' => 
				array(
	                array('title' => 'TODOS', 'value' => ''),
	                array('title' => 'SIM', 'value' => 'S'),
	                array('title' => 'NÃO', 'value' => 'N'),
	            )
	        );
		}
		if($g['C031']['contasPagarUnificado'] == 'S'){
			$gridFiltro['C004_Nome_Empresa,C004_Nome_Abreviado'] = array('titulo' => 'Empresa', 'tipo' => 'texto');
		}
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);	

		/*
		echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="fin002Incluir">Incluir</button>
			<button type="button" id="fin002Baixar">Baixar</button>
			<button type="button" id="fin002Agrupar">Agrupar</button>
			<button type="button" id="fin002Reparcelar">Reparcelar</button> 
			<button type="button" id="fin002Dividir">Dividir</button> 			
		</div>
EOT;	*/

		/**
		* Botões (Versão Acima do GRID)
		*/
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fin002Incluir', 'janelaUrl' => '/fin/fin002/content/fin002content02/', 'janelaTamanho' => '[600,500]'),
			array('titulo' => 'Baixar', 'id' => 'fin002Baixar'),
			array('titulo' => 'Agrupar', 'id' => 'fin002Agrupar'),
			array('titulo' => 'Reparcelar', 'id' => 'fin002Reparcelar'),
			array('titulo' => 'Dividir', 'id' => 'fin002Dividir'),
		);
		if (isset($g['c029Ids'][111])) {
			$botoes[] = array('titulo' => 'Excluir', 'id' => 'fin002ExcluirEmMassa');
		}
		echo Botoes::processar($botoes);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#xfin002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '', [600,500]);
				});			
				$('#fin002Baixar').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin002/grid_func-ajax/verificaBaixar/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
				    		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin002/content/fin002content03/', '&extra={$extra_}', 'auto');
				    	} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Baixa de Títulos', request.data, buttons);							
						}
					});
				});
                
                $('#fin002Reparcelar').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fin/fin002/grid_func-ajax/verificaReparcelar/?ajax=true&extra={$extra_}&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code == 0) {
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Reparcelamento', '/fin/fin002/content/fin002ContentReparcelar/', '&extra={$extra_}', [1000,600]);
                        } else {
                            var buttons = {
                                    "Ok": function() { $(this).dialog("destroy").remove(); }
                                }
                            dialogConfirm('Reparcelar', request.data, buttons);
                        }
                    });
                });

				$('#fin002Dividir').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fin/fin002/grid_func-ajax/verificaDividir/?ajax=true&extra={$extra_}&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code == 0) {
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Divisão', '/fin/fin002/content/fin002ContentDividir/', '&extra={$extra_}', [1000,600]);
                        } else {
                            var buttons = {
                                    "Ok": function() { $(this).dialog("destroy").remove(); }
                                }
                            dialogConfirm('Reparcelar', request.data, buttons);
                        }
                    });
                });

				$('#fin002ExcluirEmMassa').unbind('click').bind('click', function() {
					fin002ExcluirEmMassa('{$g['divId']}');
				});
                
                $('#fin002Agrupar').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fin/fin002/grid_func-ajax/verificaAgrupar/?ajax=true&extra={$extra_}&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code==0) {
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Agrupar', '/fin/fin002/content/fin002ContentAgrupar/', '&extra={$extra_}', [700,500]);
                        } else {
                            var buttons = {
                                    "Ok": function() { $(this).dialog("destroy").remove(); }
                                }        
                            dialogConfirm('Agrupamento', request.data, buttons);                            
                        }
                    });
                });    
			} </script></div>
EOT;
        }
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
        * Totais
        */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Pago', 'valor' => 'T015_Valor_Pago', 'titulo' => 'Pago'),
			array('nome' => 'Total_Vencido', 'valor' => 'Total_Vencido', 'titulo' => 'Vencido'),
			array('nome' => 'Total_Vencer', 'valor' => 'Total_Vencer', 'titulo' => 'A vencer'),
			array('nome' => 'Total_Pagar', 'valor' => 'T015_Valor_Saldo', 'titulo' => 'Total pagar'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Totalizadores
		*/
		$T015_Data_Vencimento = $g['ui']->getGridFiltroCampoBusca('T015_Data_Vencimento');
		$T015_Data_Vencimento0 = gCorrigeDataInverte($T015_Data_Vencimento[0]);
		$T015_Data_Vencimento1 = gCorrigeDataInverte($T015_Data_Vencimento[1]);
		$extraProjetado = ''; 
		if(!empty($T015_Data_Vencimento0) && empty($T015_Data_Vencimento1)){
			$extraProjetado .= "WHERE ((STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') = '{$T015_Data_Vencimento0}') OR (D014_Dia_Semana_Vencimento = DAYOFWEEK(CURDATE()) AND T018_Ano = YEAR(CURDATE()) AND T018_Mes = MONTH(CURDATE())))";
		} else if(empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE ((STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') = '{$T015_Data_Vencimento1}') OR (D014_Dia_Semana_Vencimento = DAYOFWEEK(CURDATE()) AND T018_Ano = YEAR(CURDATE()) AND T018_Mes = MONTH(CURDATE())))";
		} else if(!empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE ((STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}' AND STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}') OR (D014_Dia_Semana_Vencimento = DAYOFWEEK(CURDATE()) AND T018_Ano = YEAR(CURDATE()) AND T018_Mes = MONTH(CURDATE())))";
		}
		if($extraProjetado != ''){
			$extraProjetado = gInsertExtraWhere($extraProjetado, "T018_C004_Id = '{$g['empresaAtual']}' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0))  AND (T018_Ano > 0 AND T018_Mes > 0 AND D014_Dia_Vencimento > 0) AND D014_Flag_Tipo='PG'");
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
		//$extra = gInsertExtraWhere($extra, "(T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL)");
		//$valorTotalPago= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Pago)", $from, $extra));
		//$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Saldo,0))", $from, $extra));
		//$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Saldo,0))", $from, $extra));
		//$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T015_Valor_Saldo)", $from, $extra) + $projetado['Saldo']);
		//$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
				
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="buttonsBottomTop">
				<!--span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span-->
				<!--span class="item" style="text-align:right">Pago<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalPago}" readonly></span-->
				<!--span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span-->
				<!--span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span-->
				<span class="item" style="text-align:right">Projetado<br><input type="text" class="whiteBg" id="inputProjetado" size="11" value="{$valorProjetado}" readonly {$valorProjetadoAttr}></span>
				<!--span class="item" style="text-align:right">Total pagar<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span-->
				<span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>
                <span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span>
                <span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar" size="11" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002ExtraBtns').data('extra', '{$extra__}');
				carregaInputBaixar();
                carregaInputReparcelar();
                carregaInputAgrupar();
			} </script></div>
EOT;
        }

			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
/* 		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [850,500]);
			});
		} </script></div>
EOT; */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {            
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + array['todosCampos']['T015_Id'], '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T015_Id']) + '&tabela={$from}', [850,500]);
				});
			} </script></div>
		EOT;
   
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('totais', $totais);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

		/**
		 * Demostrativo de Acrescimos e Descontos
		 * Chamado em: fin002form02
		 */
	case 'fin002grid02':

		echo "<br />";
		/**
		 * Definição do Grid Superior (Tabela)
		 */
		$grid = array(
		array('colunaValor' => "T033_T015_Id", 'colunaTitulo' => 'ID Acréscimo', 'colunaExibir' => false),
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoAd'),
		array('colunaValor' => "T033_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
		array('colunaValor' => "T033_Data_Lancamento", 'colunaTitulo' => 'Data','callback' => 'gCorrigeData'),
		array('colunaValor' => "T033_Historico", 'colunaTitulo' => 'Descrição'),
		array('colunaValor' => "T033_Valor", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
		array('colunaValor' => "T033_Flag_Deb_Cred", 'colunaTitulo' => 'Tipo','extra'=>'style="text-align:center"'),
		);
			
		/**
		 * Definição da SQL
		 */
		$from = 'T033';

		$extra = <<<EOT
				-- WHERE
					where T033_T015_Id={$_REQUEST['acaoId']}
				-- ORDER BY
					order by T033_Data_Lancamento
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

		/**
		 * Demonstrativo de Pagamentos
		 * Chamado em: fin002form03
		 */
	case 'fin002grid03':
			
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T019_Id', $g['divId'])),
			array('colunaValor' => "T019_Id", 'colunaTitulo' => 'ID'),
			array('colunaValor' => "T019_Data_Lancamento", 'colunaTitulo' => 'Data','callback' => 'gCorrigeData'),
			array('colunaValor' => "Descricao",'colunaTipo'=>'livre', 'colunaTitulo' => 'Descrição','callback' => 'defineDescricao'),
			array('colunaValor' => "T019_Valor_Lancamento", 'colunaTitulo' => 'Valor', 'colunacasasDecimais'=>'2'),
			array('colunaValor' => "D007_Nome_Banco", 'colunaTitulo' => 'Banco'),
			array('colunaValor' => "D031_Portador", 'colunaExibir'=>false),
		);

		/**
		 * Definição da SQL
		 */
		$from = 'T019';

		$extra = <<<EOT
					left join D031 on D031_Id=T019_D031_Id
					left join D007 on D007_Id=T019_D007_Id
					where T019_T015_Id={$_REQUEST['acaoId']}
					order by T019_Data_Lancamento
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

		/**
		 * Baixar Títulos
		 * Chamado em: fin002form?
		 */
	case 'fin002grid04':
		// Dentro desta tabela temos campos de formulário
		// Definimos um formulário aqui apenas para gerar esses campos
		$form = new Formulario('form_fin002_form1');
		$formArray = array(
			'campos' => array(
				'baixar_titulos' => array(
					'T103_Valor_Recebido' => array('name' => 'T103_Valor_Recebido', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_Valor_Desconto' => array('name' => 'T103_Valor_Desconto', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_Valor_Taxas' => array('name' => 'T103_Valor_Taxas', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_Valor_Juros' => array('name' => 'T103_Valor_Juros', 'type' => 'moeda', 'length' => array(10,20)),
					'T103_D031_Id' => array('name' => 'T103_D031_Id', 'type' => 'select', 'select' =>
						gGeraSelect('D031', 'D031_Id', 'D031_Portador', 'order by D031_Id')
					),
					'T015_Flag_Baixar_Lote' => array('name' => 'T015_Flag_Baixar_Lote', 'type' => 'checkbox')
					)
				)
			);
		$form->definirFormularioArray($formArray);

		// Tabela temporária T103
		$retorno = $FIN002->atualizaTitulosParaBaixarT103();
		if ($retorno !== true) {
			echo gGeraAlertMsg('Erro', 'Erro ao listar títulos para baixar (e inserir na T103): ' . $retorno);
		}
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'T103_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => "T015_Id", 'colunaTitulo' => 'Valor Total', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)", 'colunaTitulo' => 'Conta'),
			array('colunaValor' => "D073_Nome", 'colunaTitulo' => 'Centro Custos'),
			array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'Documento'),
			array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T015_Valor_Total", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => "T015_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => "T103_Valor_Recebido", 'colunaExibir' => false),
			array('colunaValor' => "T103_Valor_Desconto", 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
			//array('colunaValor' => "T103_Valor_Taxas", 'colunaTitulo' => 'Taxas','colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "T103_Valor_Juros", 'colunaTitulo' => 'Juros','colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "T103_Valor_Recebido_", 'colunaTitulo' => 'Valor Pago', 'callback' => 'baixarTitulosValorRecebido', 'callbackParameter' => $form, 'colunaTipo' => 'livre'),
			array('colunaValor' => "T103_Valor_Juros_", 'colunaTitulo' => 'Juros', 'callback' => 'baixarTitulosValorJuros', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
			//array('colunaValor' => "T103_Valor_Taxas_", 'colunaTitulo' => 'Taxas', 'callback' => 'baixarTitulosValorTaxas', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
			array('colunaValor' => "T103_Valor_Desconto_", 'colunaTitulo' => 'Desconto', 'callback' => 'baixarTitulosValorDesconto', 'callbackParameter' => $form, 'colunaTipo' => 'livre', 'colunaExibir' => true),
			array('colunaValor' => "T103_Valor_Total_Recebido", 'colunaTitulo' => 'Total Pago', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "T103_D031_Id", 'colunaTitulo' => 'Portador', 'callback' => 'baixarTitulosPortador', 'callbackParameter' => $form, 'style'=>'width:1%;'),
			array('colunaValor' => "T015_Flag_Baixar_Lote", 'colunaTitulo' => 'Bx', 'callback' => 'baixarTitulosBx', 'callbackParameter' => $form),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'T015_Observacao_1', 'colunaTitulo' => 'Observação Pagamento', 'colunaEditar' => true),

		);

		/**
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT

				left join T103 on T015_Id=T103_T015_Id
				left join D024 on D024_Id=T015_D024_Id
				left join D031 on D031_Id=T015_D031_Id
              	left join D014 on D014_Id=T015_D014_Id
              	left join D032 on D032_Id=D014_D032_Id 
              	left join D154 on D154_Id=D014_D154_Id
              	left join T007 on T007_Id=T015_T007_Id
              	left join D073 on D073_Id=T015_D073_Id
              	left join C004 on C004_Id=T015_C004_Id
              	left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				where 
EOT;
		$extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "T015_Flag_Baixar_Lote='S' AND T015_Flag_Cancelada != 'S' AND T015_Data_Pagamento='0000-00-00'"));
		$extra .= " group by T015_Id";
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
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Recebido', 'valor' => 'T103_Valor_Total_Recebido', 'titulo' => 'Total Pago'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		
		// inserir o total na última linha da tabela
		array_push($gridTabela['tbody'], array('dados'=>array('<b>Total</b>','','','','','','<b><span id="fin002BaixarValor"></span></b>','<b><span id="fin002BaixarValorPago"></span></b>','','')));
		

		// inserir o total na última linha da tabela
		//array_push($gridTabela['tbody'], array('dados'=>array('<b>Total</b>','','','','','','<b><span id="fin002BaixarValor">'.number_format($total1,2,',','.').'</span></b>','<b><span id="fin002BaixarValorPago">'.number_format($total2,2,',','.').'</span></b>','','')));

		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		// Ações JS
		echo <<<EOT
         	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
         	$('#{$g['divId']} input:text').bind('blur', function () {
         		baixarTitulosEditarCampo($(this));
         	});
         	$('#{$g['divId']} select').bind('change', function () {
         		baixarTitulosEditarCampo($(this));
         	});
         	$('#{$g['divId']} input:checkbox').bind('change', function () {
         		baixarTitulosEditarCampo($(this));
         	});
         	} </script></div>
EOT;
	break;
        
    /**
    * Agrupar
    * Chamado em: fin001form07
    */
    case 'fin002GridAgrupar':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(            
            array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'Título'),
            array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "T015_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2),
            array('colunaValor' => "T015_Valor_Desconto", 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => 2),
            array('colunaValor' => "T015_Valor_Juros", 'colunaTitulo' => 'Acréscimo', 'colunaCasasDecimais' => 2),
            array('colunaValor' => "T015_Valor_Total", 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => 2),
            //array('colunaValor' => "SUM(T015_Valor_Total) as soma_valor_total", 'colunaTitulo' => 'Total'),
            /*
            array('colunaValor' => "T015_Id", 'colunaTitulo' => 'ID','colunaExibir'=>false),
            array('colunaValor' => "T015_T035_Id", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),            
            array('colunaValor' => "D014_SubConta", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),            
            array('colunaValor' => "T015_Data_Recebimento", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Valor_Duplicata", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Valor_Juros", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Valor_Desconto", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Observacao", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Agrupamento", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Flag_Enviar_Banco", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),                        
            array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Flag_Baixar_Lote", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Flag_Reparcelar", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "T015_Valor_Recebido", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
            array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),            
            array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'ID', 'colunaExibir'=>false),            
            */
        );
        
        /**
        * Definição da SQL
        */
        $from = 'T015';
        
        $extra = <<<EOT
            left join D024 on D024_Id=T015_D024_Id
            left join D031 on D031_Id=T015_D031_Id
            left join D014 on D014_Id=T015_D014_Id
            left join D032 on D032_Id=D014_D032_Id 
            left join D046 on D046_Id=D024_D046_Id
           	left join D073 on D073_Id=T015_D073_Id
            left join T007 on T007_Id=T015_T007_Id
            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno    
            where
EOT;

        $extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada != 'S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}'"));
        $extra .=" order by T015_Data_Vencimento";
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
         
         // calcular o total
         $total=0;
         foreach ($gridTabela['tbody'] as $linha){
             $total+=@$linha['dados'][7];
         }
         // inserir o total na última linha da tabela
         array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','<b>Total</b>','<b>'.gCorrigeNumero($total).'</b>')));
         
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
    	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;
        
	case 'fin002GridReparcelamentos':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T196_Id', $g['divId'])),
            array('colunaValor' => 'T196_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
            array('colunaValor' => 'T196_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo'),
            array('colunaValor' => 'T196_D024_Id', 'colunaTitulo' => 'Cliente/Fornecedor', 'colunaExibir'=>false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente/Fornecedor'),
            array('colunaValor' => 'T196_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T196_Data_Emissao', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T196_Valor_Total', 'colunaTitulo' => 'Valor Total','colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T196_Titulos_Geradores', 'colunaTitulo' => 'Títulos Geradores', 'callback'=>'formataGeradores'),
            array('colunaValor' => 'T196_Titulos_Gerados', 'colunaTitulo' => 'Títulos Gerados' , 'callback'=>'formataGerados'),
        );

        /**
        * Definição da SQL
        */
        $from = 'T196';
        
        //left join D116 on D116_Id=D131_D116_Id    
        $extra = <<<EOT
            left join C007 on C007_Id=T196_C007_Id
            left join D024 on D024_Id=T196_D024_Id
            WHERE T196_C004_Id='{$g['empresaAtual']}'
EOT;
        
        // /**
        // * Botões (Versão Acima do GRID)
        // */
        // echo <<<EOT
            // <div class="buttonsBottomTop">
                // <button type="button" id="fin002EnviarEmailReparcelamento">Enviar Email</button>
//                 
            // </div>
            // <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                // $('#etc010FormUsuariosClienteBtnIncluir').unbind('click').bind('click', function(data) {
                    // abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Usuários Clientes', '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&tab=geral', [400,300]);
                // });
            // } </script></div>
// EOT;

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
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
            'T196_Data_Emissao' => array('titulo' => 'Data Reparcelamento', 'tipo' => 'data'),
            'T196_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
            'T196_Titulos_Geradores' => array('titulo' => 'Títulos Geradores', 'tipo' => 'texto'),
            'T196_Titulos_Gerados' => array('titulo' => 'Títulos Gerados', 'tipo' => 'texto'),
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
                var dbclickPrevent = false;
                $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Previne duplo click (abrir duas janelas)
                    if (dbclickPrevent) { return false; }
                    dbclickPrevent = true;
                    setTimeout(function() { dbclickPrevent = false; }, 500);
                    // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'T196_Id';    
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Reparcelamentos e Agrupamentos - ID: ' + acaoId, '/fin/fin002/content/fin002ContentReparcelarWindow/', '&acaoId=' + encodeURIComponent(acaoId), [500,400]);
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
    
	case 'fin002gridTitulosGeradores':
		
		$T196 = mysql_query("SELECT T196_Titulos_Geradores from T196 WHERE T196_Id='{$r_acaoId}'");
		$aT196 = mysql_fetch_array($T196);
		$res=explode("\n", $aT196['T196_Titulos_Geradores']);
		$indices="";
		foreach ($res as $elemento){
			if (trim($elemento)!=""){
				$part=explode(";", $elemento);
				if (isset($part[0]) && is_numeric(trim($part[0]))) {
					$indices.=trim($part[0]).",";
				}
			}
		}
		while (substr($indices,-1,1)==","){
			$indices=substr_replace($indices, '', -1);	
		}		

		$grid = array( 	
				array('colunaValor' => "T015_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
				array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
				array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
				array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),								
				array('colunaValor' => "T015_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T015";
		
		if (!empty($indices)) {
			$extra = <<<EOT
			WHERE T015_Id IN ({$indices})
EOT;
		} else {
			$extra = <<<EOT
			WHERE 1=0
EOT;
		}
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
		);		

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		// apenas para o grid não colar nas tabs
		echo <<<EOT
			<div style="margin-top:30px">			
			</div>
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
	
	case 'fin002gridTitulosGerados':
		
		$T196 = mysql_query("SELECT T196_Titulos_Gerados from T196 WHERE T196_Id='{$r_acaoId}'");
		$aT196 = mysql_fetch_array($T196);
		$res=explode("\n", $aT196['T196_Titulos_Gerados']);
		$indices="";
		foreach ($res as $elemento){
			if (trim($elemento)!=""){
				$part=explode(";", $elemento);
				if (isset($part[0]) && is_numeric(trim($part[0]))) {
					$indices.=trim($part[0]).",";
				}
			}
		}
		while (substr($indices,-1,1)==","){
			$indices=substr_replace($indices, '', -1);	
		}		

		$grid = array( 	
				array('colunaValor' => "T015_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
				array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
				array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
				array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),							
				array('colunaValor' => "T015_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T015";
		
		if(!empty($indices)){

			$extra = <<<EOT
				WHERE T015_Id IN ({$indices})
EOT;
		} else {

			$extra = <<<EOT
				WHERE 1=0
EOT;
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
		);		

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		// apenas para o grid não colar nas tabs
		echo <<<EOT
			<div style="margin-top:30px">			
			</div>
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
        
    /**
    * Reparcelar
    * Chamado em: fin001form09
    */
    case 'fin002GridReparcelar':
    
        //echo '<p style="font-weight:bold;" align="center">Duplicatas para Reparcelamento</p>';
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
        	array('colunaValor' => "T015_Numero_Documento", 'colunaTitulo' => 'Documento'),            
            array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
            array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "T015_Valor_Duplicata", 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => 2, 'colunaExibir'=>false),
            // array('colunaValor' => "T015_Valor_Desconto", 'colunaTitulo' => 'Desconto'),
            // array('colunaValor' => "T015_Valor_Juros", 'colunaTitulo' => 'Acréscimo')
            array('colunaValor' => "T015_Valor_Total", 'colunaTitulo' => 'Total', 'colunaExibir'=>false, 'colunaCasasDecimais' => 2),
            array('colunaValor' => "T015_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => "D031_Portador", 'colunaTitulo' => 'Portador'),
        );
        
        /**
        * Definição da SQL
        */
        $from = 'T015';
        
        $extra = <<<EOT
                    left join D024 on D024_Id=T015_D024_Id
                    left join D031 on D031_Id=T015_D031_Id
                    left join D014 on D014_Id=T015_D014_Id
                    left join D032 on D032_Id=D014_D032_Id 
                    left join D046 on D046_Id=D024_D046_Id
                    left join D073 on D073_Id=T015_D073_Id
                    left join T007 on T007_Id=T015_T007_Id
                    left join C004 on C004_Id=T015_C004_Id
                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno    
                    where
EOT;
        $extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada != 'S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}'"));
        $extra .= " order by T015_Data_Vencimento";
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
         
        // calcular o total
        $total=0;
        foreach ($gridTabela['tbody'] as $linha){
            $total+=@gCorrigeNumeroInverte($linha['dados'][4]);
        }
         // inserir o total na última linha da tabela
        array_push($gridTabela['tbody'], array('dados'=>array('','','','<b>Total</b>','<b>'.gCorrigeNumero($total).'</b>','')));
        
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;
    
    case 'fin002GriReparcelamento':

    	//mysql_query("DELETE FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}'");

        $queryT015 = <<<EOT
            SELECT 
            T015_Id,
            T015_Numero_Duplicata, 
            T015_Data_Emissao, 
            D024_Nome_Empresa,
            D024_Id, 
            T015_Data_Vencimento, 
            T015_Valor_Duplicata, 
            T015_Valor_Saldo 
            FROM T015 
            left join D024 on D024_Id=T015_D024_Id 
            left join D031 on D031_Id=T015_D031_Id 
            left join D014 on D014_Id=T015_D014_Id
            left join D032 on D032_Id=D014_D032_Id 
            left join D046 on D046_Id=D024_D046_Id 
            left join D073 on D073_Id=T015_D073_Id
            left join T007 on T007_Id=T015_T007_Id 
            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            LEFT JOIN C004 ON C004_Id=T015_C004_Id
            WHERE 
EOT;
        $queryT015 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada != 'S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}'"));
        $queryT015 .= " order by T015_Data_Vencimento";
        $T015 = mysql_query($queryT015);
        
        $valorSomado = 0;
        while($aT015 = mysql_fetch_array($T015)){
            $D024_Id = $aT015['D024_Id'];
            $valorSomado = ($valorSomado + $aT015['T015_Valor_Saldo']);
        }
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
        	array('colunaValor' => 'T197_Numero_Documento', 'colunaTitulo' => 'Documento', 'colunaEditar'=>true),
        	array('colunaValor' => 'T197_Numero_Duplicata', 'colunaTitulo' => 'Duplicata', 'colunaEditar'=>true),
        	array('colunaValor' => 'T197_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
        	array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T197_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            //array('colunaValor' => 'T197_Numero_Documento', 'colunaTitulo' => 'Nº Duplicata', 'colunaEditar'=>true),
            array('colunaValor' => 'T197_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
            array('colunaValor' => 'T197_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T197_Prazos_Dias"),
            array('colunaValor' => 'T197_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T197_Prazos_Dias"),
            array('colunaValor' => 'T197_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T197_Prazos_Dias"),
            array('colunaValor' => 'T197_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T197_Prazos_Dias", 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', 'order by D031_Portador', true), 'callback' => 'T197_D031_Id_Portador'),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
            array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "fin002_Dia_Semana"),
        );

        /**
        *
        * Definição da SQL
        */
        $from = 'T197';

        $extra = <<<EOT
            LEFT JOIN D031 ON D031_Id=T197_D031_Id
            LEFT JOIN D024 ON D024_Id=T197_D024_Id
            WHERE T197_C007_Id = {$g['usuarioAtual']}
            AND T197_D024_Id = '{$D024_Id}'
EOT;
        /**
        * Filtro
        
        // Gera o filtro e acrescenta ao $extra
        $gridFiltro = array(
            
        );
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint();
        */
        
        //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T197_Id-/, '');
                divSetarAcaoId('{$r_divReload}', id);
                divRefresh('{$r_divReload}');
            });
            
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
            
            // Função JS executada como callback do editar campo (colunaEditarJSCallback)
            function colunaEditarCallbackJS_T197_Prazos_Dias(T197_Id, campoEditado, valorAntigo, novoValor) {
                T197_Id = T197_Id.replace(/T197_Id-/, '');
                if (campoEditado == 'T197_Prazos_Dias') {
                    $.getJSON('/fin/fin002/grid_func-ajax/fin002atualizarParcelas/?ajax=true&T197_Id='              + encodeURIComponent(T197_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                            //alert("NFe Excluída");
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T197_Data_Vencimento') {
                    $.getJSON('/fin/fin002/grid_func-ajax/fin002atualizarParcelas/?ajax=true&T197_Id='              + encodeURIComponent(T197_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                            //alert("NFe Excluída");
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T197_Valor_Parcela') {
                    $.getJSON('/fin/fin002/grid_func-ajax/fin002atualizarParcelas/?ajax=true&T197_Id='              + encodeURIComponent(T197_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +
                                                                                              '&valorTotal='        + encodeURIComponent('{$valorSomado}') +   
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                            //alert("NFe Excluída");
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T197_D031_Id') {
                    divRefresh('{$g['divId']}');                        
                }
            }
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
            $sql2 = "SELECT T197_C007_Id FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}'AND  T197_D024_Id = '{$D024_Id}'";
            $result2 = mysql_query($sql2);
            $C007_Id='';
            while($linha3 = mysql_fetch_array($result2)){
                $C007_Id = $linha3['T197_C007_Id'];
            }
            $botoesMid = '';
            $botoesDir = '';
            $botoesEsq = '';
            $botoesEsq .= '<button type="submit" id="fin002ReparcelarFinal">Reparcelar</button>';
            $botoesEsq .= '<button type="submit" id="fin002LimparGrid">Limpar</button>';
            $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
            $g['smarty']->assign('botoesId', $g['divId']);
            $g['smarty']->assign('botoesEsq', $botoesEsq);
            $g['smarty']->assign('botoesMid', $botoesMid);
            $g['smarty']->assign('botoesDir', $botoesDir);
            $g['smarty']->display('buttons-floatBottom.tpl');
            $extra_ = urlencode($r_extra);
            // Não executar quando houver o refresh de linha
            if (empty($r_linhaGridId)) {
                echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $('#fin002ReparcelarFinal').unbind('click').bind('click', function(data) {
                    var \$buttons = {
                        "Sim": function() {
                            //var T005_Id = $('#{$g['divId']} #T005_Id').val();
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/fin/fin002/grid_func-ajax/fin002Reparcelar/?ajax=true&D024_Id=' + encodeURIComponent("{$D024_Id}") + '&C007_Id=' + encodeURIComponent("{$C007_Id}") + '&valorSomado=' + encodeURIComponent("{$valorSomado}")+ '&extra={$extra_}&callback=?', function(request) {
                                if (request.code) {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('Ok', 'Processo concluído com sucesso', \$buttons);
                                    divRefresh('{$g['divIdParent']}', true);
                                    $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                } else {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('Erro', request.data, \$buttons);
                                }
                                $('#{$g['divId']}').hideLoading();
                                divRefresh('{$g['divId']}');
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); } 
                    };
                    dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
                });
                $('#fin002LimparGrid').unbind('click').bind('click', function(data) {
                    var \$buttons = {
                        "Sim": function() {
                            //var T005_Id = $('#{$g['divId']} #T005_Id').val();
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/fin/fin002/grid_func-ajax/fin002LimparGrid/?ajax=true&D024_Id=' + encodeURIComponent("{$D024_Id}") + '&callback=?', function(request) {
                                if (request.code) {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('Ok', 'Processo concluído com sucesso', \$buttons);
                                    divRefresh('{$g['divId']}', true);
                                    //$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                } else {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('Erro', request.data, \$buttons);
                                }
                                $('#{$g['divId']}').hideLoading();
                                divRefresh('{$g['divId']}');
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); } 
                    };
                    dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
                });
            } </script></div>
                
EOT;
            }
        break;
        
        case 'fin002GridCaixaBanco':
        	$T015_Id = (isset($r_T015_Id)) ? $r_T015_Id : false;
        	if (empty($T015_Id) && !empty($r_acaoId)) {
        		$T015_Id = $r_acaoId;
        	}
			/**
			 * Definição do grid tabela
			 */
			$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T019_Id', $g['divId'])),
			array('colunaValor'=>'T019_Id', 'colunaTitulo'=>'ID','chavePrimaria' => true,),
			array('colunaValor'=>'T019_Data_Lancamento', 'colunaTitulo'=>'Data', 'callback'=>'gCorrigeData','colunaAlinhar' => 'center'),
			array('colunaValor'=>'T019_Referente', 'colunaTitulo'=>'Descrição'),
			array('colunaValor'=>'T019_Complemento', 'colunaTitulo'=>'Complemento'),
			array('colunaValor'=>'T019_Valor_Lancamento', 'colunaTitulo'=>'Valor', 'colunaCasasDecimais'=>'2','colunaAlinhar' => 'right'),
			array('colunaValor'=>'T019_Saldo', 'colunaTitulo'=>'Saldo', 'colunaCasasDecimais'=>'2','colunaAlinhar' => 'right'),
			array('colunaValor'=>'D027_Portador', 'colunaTitulo'=>'Portador'),
			array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Empresa'),
			array('colunaValor'=>'D014_SubConta', 'colunaTitulo'=>'Conta'),
			
			);
			
			$from = 'T019';
			$extra = <<<EOT
			    left join D024 on D024_Id=T019_D024_Id
				left join D027 on D027_Id=T019_D027_Id
				left join D014 on D014_Id=T019_D014_Id
				where (T019_T015_Id='$T015_Id')
				ORDER BY T019_Data_Lancamento DESC, T019_Id DESC
EOT;
			/**
			* Botões (Versão Acima do GRID)
			*/
			//echo <<<EOT
	//			<div class="buttonsBottomTop">
	//				<!-- <button type="button" id="fin003BtnInserirLancamento">Incluir Lançamento</button> -->
	//				<!-- <button type="button" id="fin003BtnAtualizarSaldos">Atualizar Saldos</button> -->
	//			</div>
	//			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	//				$('#fin003BtnInserirLancamento').unbind('click').bind('click', function(event) {
	//					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/form/fin003FormLancamentos/', '&tab=geral&D007_Id=' + encodeURIComponent('$D007_Id'), [450,400]);
	//				});
	//				$('#fin003BtnAtualizarSaldos').unbind('click').bind('click', function(event) {
	//				var buttons = {
	//					"Sim": function() {
	//							$.getJSON('/fin/fin003/grid_func-ajax/atualizarSaldos/?ajax=true&D007_Id=' + encodeURIComponent('$D007_Id') + '&callback=?', function(request) {
	//							if(request.data){
	//								dialogConfirm('OK!', request.data);
	//							}else{
	//								dialogConfirm('Erro!', request.data);
	//							}
	//							divRefresh('{$g['divId']}');
	//							divRefresh('{$r_divReload}'); 
	//							});
	//							$(this).dialog("destroy").remove();
	//					     },
	//						 "Não": function() { $(this).dialog("destroy").remove(); }
	//					}
	//					dialogConfirm('Confirmar', "ATENCAO! Deseja atualizar saldo?", buttons);
	//				});
	//			} </script></div>
	//EOT;

			 /**
			* Filtro
			*/
			$gridFiltro = array(
				'T019_Referente' => array('titulo' => 'Referente', 'tipo' => 'texto'),
				'T019_Complemento' => array('titulo' => 'Complemento', 'tipo' => 'texto'),
				'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
				'T019_Valor_Lancamento' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
				'T019_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
				'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
				'D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
				'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);

			/**
			* Botões (Versão Acima do GRID)
			*/
			$valorTotalLancamentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra));
			$valorTotalRecebimentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0))", $from, $extra));
			$valorTotalPagamentos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0))", $from, $extra));
			$valorTotalResultado = ($valorTotalRecebimentos + $valorTotalPagamentos);
			$ResultadoTotal = gCorrigeNumero($valorTotalResultado);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Lançamentos<br><input type="text" value="{$valorTotalLancamentos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Recebimentos<br><input type="text" value="{$valorTotalRecebimentos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Pagamentos<br><input type="text" value="{$valorTotalPagamentos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Resultado<br><input type="text" value="{$ResultadoTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			/**
			* Define a abertura do form
			*/	
			
			$novoIdTelaForm = uniqid();
			//echo  <<<EOT
	//		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	//				var dbclickPrevent = false;
	//				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
	//				if (dbclickPrevent) { return false; }
	//				dbclickPrevent = true;
	//				setTimeout(function() { dbclickPrevent = false; }, 500);
	//				var chavePrimaria = 'T019_Id';	
	//				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
	//					var id = $(this).closest('tr').attr('id').replace(/T019_Id-/, '');
						//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Lançamento - ID: ' + id, '/fin/fin003/content/fin003ContentEditaLancamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}&D007_Id='+{$D007_Id}, [600,500]);
	//				
					//var id = $(this).closest('tr').attr('id').split('-');
					//var acaoId = id[1].replace(chavePrimaria , '-', '');
					//divSetarAcaoId('{$r_divReload}', acaoId);
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/content/fin003ContentEditaLancamento/', '&tab=geral&T019_Id='+acaoId+'&acaoId='+acaoId+'&D007_Id='+{$D007_Id}, [450,400]);
	//			});
	//		} </script></div>
	//EOT;
				
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
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
        break;


	case 'fin002GridProjecoes':
        	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_D024_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_D031_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_D073_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => 'D014_Flag_Tipo','colunaExibir'=>false),
			array('colunaValor' => 'T018_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
            array('colunaValor' => 'D014_D032_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Dia_Vencimento', 'colunaTitulo' => 'Dia', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T018_Mes', 'colunaTitulo' => 'Mês', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T018_Ano', 'colunaTitulo' => 'Ano', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D014_Dia_Semana_Vencimento', 'colunaTitulo' => 'Dia da Semana', 'colunaAlinhar'=>'center', 'callback' => 'colunaDiaSemana', 'style' => 'color: blue'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'D014_Flag_Plano', 'colunaTitulo' => 'Tipo', 'colunaExibir'=>false),
			//array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Código','callback' => 'campoGrupoNegrito', 'style'=>'width:1%;'),
			array('colunaValor' => 'T018_Valor', 'colunaTitulo' => 'Valor projeção', 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right'),
			//array('colunaValor' => 'T018_Flag_Confirmado', 'colunaTitulo' => 'Confirmado', 'callback'=>'campoConfirmado', 'style'=>'width:1%', 'colunaAlinhar'=>'center'),
			//array('colunaValor' => 'T018_Flag_Confirmado', 'colunaTitulo'=>'Confirmado','colunaEditar' => true, 'callback' => 'campoConfirmado','style'=>'width:1%;',
//		                'colunaEditarGeraSelect' => array(
//		                    array('title' => '...', 'value' => '', 'selected' => true),
//		                    array('title' => 'Sim', 'value' => 'S'),
//		                    array('title' => 'Não', 'value' => 'N'))
//		                ),
			array('colunaValor' => 'T018_Valor_Receber', 'colunaTitulo' => 'Receber', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Valor_Pagar', 'colunaTitulo' => 'Pagar', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Pago', 'colunaTitulo' => 'Pago', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => '(+)/(-)', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'callback'=>'ValidaCampoSaldo', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'D014_Observacao', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);'),
			array('colunaValor' => 'incluirTituloProjetado', 'colunaTitulo' => '', 'callback' => 'incluirTituloProjetado', 'style' => 'width: 1%', 'colunaTipo' => 'livre'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$clausulaWhere = base64_decode($r_extra);
		
		$extra = <<<EOT
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			{$clausulaWhere}
			ORDER BY D014_Grupo
EOT;
			
		/**
		* Botões (Versão Acima do GRID)
		*/
		$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad034GridPlanoContasBtnIncluir">Incluir</button> -->
				<button type="button" id="fin009RecalcularProjecao">Recalcular</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridPlanoContasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
				});
				$('#fin009RecalcularProjecao').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin009/grid_func-ajax/fin009RecalcularProjecao/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							{$alert}
							divRefresh('{$g['divId']}', true);
						} else {
							alert(request.data);
						}
					});
				});
				function atualizaFlagConfirmado(Ids, campoEditado, valorAntigo, novoValor){
					var T018_Id_array = Ids.split('|');
					var T018_Id = T018_Id_array[1].replace('T018_Id' + '-', '');
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin009/grid_func-ajax/fin009AtualizaFlagConfirmado/?ajax=true&callback=?&T018_Id=' + encodeURIComponent(T018_Id) + '&novoValor=' + encodeURIComponent(novoValor), function(request) {
						$('#{$g['divId']}').hideLoading();
						if(novoValor == "0,00"){
							$('#{$g['divId']} input[name=T018_Flag_Confirmado]:eq(' + $('#{$g['divId']} tr[id*="T018_Id-' + T018_Id + '"]').first().index() + ')').prop('checked', '');
						}else{
							$('#{$g['divId']} input[name=T018_Flag_Confirmado]:eq(' + $('#{$g['divId']} tr[id*="T018_Id-' + T018_Id + '"]').first().index() + ')').prop('checked', 'checked');
						}
					});
				}
				
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T018_Ano' => array('titulo' => 'Ano', 'tipo' => 'texto'),
			'T018_Mes' => array('titulo' => 'Mês', 'tipo' => 'texto'),
			'D014_Dia_Vencimento' => array('titulo' => 'Dia vencimento', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D014_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Saidas', 'value' => 'PG'),
				array('title' => 'Entradas', 'value' => 'RC'),
                array('title' => 'Todos', 'value' => ''),
			), 'naoGerarExtra' => false),
			'D014_Flag_Ativo' => array('titulo' => 'Contas', 'tipo' => 'select', 'select' => array(
                array('title' => 'Ativas', 'value' => 'S'),
				array('title' => 'Inativas', 'value' => 'N'),
                array('title' => 'Todas', 'value' => ''),
			), 'naoGerarExtra' => false),
			'T018_Valor_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Grupo', 'campoAgrupar' => 'D014_D032_Id', 'campoMostrar' => 'D032_Descricao', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
            )),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Totais (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$Projecao_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor>0, T018_Valor, 0))", $from, $extra));
		$Projecao_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0))", $from, $extra));
		$T018_Valor_Receber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Receber)", $from, $extra));
		$T018_Valor_Recebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Recebido)", $from, $extra));
		$T018_Valor_Pagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pagar)", $from, $extra));
		$T018_Valor_Pago = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pago)", $from, $extra));
		$Saldo_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extra));
		$Saldo_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extra));
		$Saldo_Total = gCorrigeNumero(gCorrigeNumeroInverte($Saldo_Entrada) + 
									  gCorrigeNumeroInverte($T018_Valor_Receber) + 
									  gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($Saldo_Saida) - 
									  gCorrigeNumeroInverte($T018_Valor_Pagar) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );
        /*
        <span class="item" style="text-align:right">Projeção entradas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Receber<br><input type="text" value="{$T018_Valor_Receber}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Recebido<br><input type="text" value="{$T018_Valor_Recebido}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Entrada<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Projeção saidas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Saída<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Resultado<br><input type="text" value="{$Saldo_Total}" class="whiteBg" id="" size="11" readonly></span>
        *

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Projeção<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Saldo<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'D014_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + acaoId, '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [1000,650]);
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

    case 'fin002gridAgrupamentos':
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'Número'),
			array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo", 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T015_Valor_Total", 'colunaTitulo' => 'Total', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "T015_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'callback' => 'gCorrigeNumero'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T015';
		
		$extra = <<<EOT
			left join D024 on D024_Id=T015_D024_Id
			left join D014 on D014_Id=T015_D014_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T007 on T007_Id=T015_T007_Id
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno	
			where T015_T015_Id_Agrupado={$_REQUEST['acaoId']}
 			AND T015_Flag_Cancelada != 'S' 
 			order by T015_Numero_Duplicata
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

    case 'fin002GridDocumentos':
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T243_Id', $g['divId'])),
			array('colunaValor' => "T243_Id", 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => "T243_Data_Hora_Upload", 'colunaTitulo' => 'Upload', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => "T243_Nome_Arquivo", 'colunaTitulo' => 'Nome do Documento'),
			array('colunaValor' => "campoAbrirDocumento", 'colunaTitulo' => 'Arquivo', 'callback' => 'campoAbrirDocumento', 'colunaTipo' => 'livre', 'style' => 'width: 1%'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T243';
		
		$extra = <<<EOT
			WHERE T243_T015_Id = '{$r_T015_Id}'
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

    case 'fin002GridDividir':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
        	array('colunaValor' => "T015_Numero_Documento", 'colunaTitulo' => 'Documento'),            
            array('colunaValor' => "T015_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => "T015_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazos'),
            array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => "T015_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "T015_Valor_Duplicata", 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => 2, 'colunaExibir'=>false),
            // array('colunaValor' => "T015_Valor_Desconto", 'colunaTitulo' => 'Desconto'),
            // array('colunaValor' => "T015_Valor_Juros", 'colunaTitulo' => 'Acréscimo')
            array('colunaValor' => "T015_Valor_Total", 'colunaTitulo' => 'Total', 'colunaExibir'=>false, 'colunaCasasDecimais' => 2),
            array('colunaValor' => "T015_Valor_Saldo", 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => 2),
            array('colunaValor' => "D031_Portador", 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro de Custo'),
            
        );
        
        /**
        * Definição da SQL
        */
        $from = 'T015';
        
        $extra = <<<EOT
        	LEFT JOIN D024 ON D024_Id=T015_D024_Id
			LEFT JOIN D073 ON D073_Id=T015_D073_Id
			LEFT JOIN D031 ON D031_Id=T015_D031_Id
			LEFT JOIN D014 ON D014_Id=T015_D014_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id 
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN T007 ON T007_Id=T015_T007_Id
			LEFT JOIN C004 ON C004_Id=T015_C004_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
        	WHERE
EOT;

        $extra .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada != 'S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00'"));
        $extra .= "order by T015_Data_Vencimento";
        
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
         
        // calcular o total
        $total=0;
        foreach ($gridTabela['tbody'] as $linha){
            $total+=@gCorrigeNumeroInverte($linha['dados'][4]);
        }
        // inserir o total na última linha da tabela
        array_push($gridTabela['tbody'], array('dados'=>array('','','','<b>Total</b>','<b>'.gCorrigeNumero($total).'</b>','')));
        
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'fin002GridDivisao':

        $queryT015 = <<<EOT
            SELECT 
            T015_Id,
            T015_Numero_Documento, 
            T015_Numero_Duplicata, 
            T015_Data_Emissao, 
            D024_Nome_Empresa,
            D024_Id, 
            T015_Data_Vencimento, 
            T015_Valor_Duplicata, 
            T015_Valor_Saldo 
            FROM T015 
            left join D024 on D024_Id=T015_D024_Id 
            left join D031 on D031_Id=T015_D031_Id 
            left join D014 on D014_Id=T015_D014_Id 
            left join D032 on D032_Id=D014_D032_Id 
            left join D046 on D046_Id=D024_D046_Id
			left join D073 on D073_Id=T015_D073_Id
            left join T007 on T007_Id=T015_T007_Id 
            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            LEFT JOIN C004 ON C004_Id=T015_C004_Id 
            WHERE 
EOT;
        $queryT015 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada != 'S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00'"));

        $T015 = mysql_query($queryT015);
        
        $valorSomado = 0;
        while($aT015 = mysql_fetch_array($T015)){
            $D024_Id = $aT015['D024_Id'];
            $valorSomado = ($valorSomado + $aT015['T015_Valor_Saldo']);
            $T015_Id = $aT015['T015_Id'];
        }

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T247_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true, 'colunaExibir' => false),
           	array('colunaValor' => 'T247_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
            array('colunaValor' => 'T247_Numero_Documento', 'colunaTitulo' => 'Documento', 'colunaEditar'=>true),
            array('colunaValor' => 'T247_Numero_Duplicata', 'colunaTitulo' => 'Duplicata', 'colunaEditar'=>true),
	        array('colunaValor' => 'T247_C004_Id', 'colunaTitulo' => 'Empresa', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C004', 'C004_Id', 'C004_Nome_Abreviado, C004_Nome_Empresa', 'ORDER BY C004_Id'), 'callback' => 'campoMostraValor', 'callbackParameter' => 'C004_Nome_Abreviado', 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T247_Prazos_Dias"),
	        array('colunaValor' => 'C004_Nome_Abreviado', 'colunaExibir' => false),
	        array('colunaValor' => 'D073_Nome', 'colunaExibir' => false),
	        array('colunaValor' => 'T247_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
            array('colunaValor' => 'T247_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T247_Prazos_Dias"),
            array('colunaValor' => 'T247_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T247_Prazos_Dias", 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T247_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T247_Prazos_Dias"),
            array('colunaValor' => 'T247_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T247_Prazos_Dias", 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', 'ORDER BY D031_Portador', true), 'callback' => 'campoMostraValor', 'callbackParameter' => 'D031_Portador'),
	        array('colunaValor' => 'T247_D073_Id', 'colunaTitulo' => 'Centro de Custo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D073', 'D073_Id', 'D073_Nome', 'WHERE D073_Flag_Ativo = "S" ORDER BY D073_Nome'), 'callback' => 'campoMostraValor', 'callbackParameter' => 'D073_Nome', 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T247_Prazos_Dias"),
	        array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "fin002_Dia_Semana"),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
        );

        /**
        * Definição da SQL
        */
        $from = 'T247';

        $extra = <<<EOT
            LEFT JOIN D031 ON D031_Id=T247_D031_Id
            LEFT JOIN C004 ON C004_Id=T247_C004_Id
            LEFT JOIN D073 ON D073_Id=T247_D073_Id
            WHERE T247_C007_Id = {$g['usuarioAtual']}
            ORDER BY T247_Numero_Parcela
EOT;
        /*
        * Filtro
        
        // Gera o filtro e acrescenta ao $extra
        $gridFiltro = array(
            
        );
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint();
        */
        
        //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T247_Id-/, '');
                divSetarAcaoId('{$r_divReload}', id);
                divRefresh('{$r_divReload}');
            });
            
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
            
            // Função JS executada como callback do editar campo (colunaEditarJSCallback)
            function colunaEditarCallbackJS_T247_Prazos_Dias(T247_Id, campoEditado, valorAntigo, novoValor) {
                T247_Id = T247_Id.replace(/T247_Id-/, '');
                if (campoEditado == 'T247_Prazos_Dias') {
                    $.getJSON('/fin/fin002/grid_func-ajax/fin002atualizarParcelasDivisao/?ajax=true&T247_Id='              + encodeURIComponent(T247_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T247_Data_Vencimento') {
                    $.getJSON('/fin/fin002/grid_func-ajax/fin002atualizarParcelasDivisao/?ajax=true&T247_Id='              + encodeURIComponent(T247_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T247_Valor_Parcela') {
                    $.getJSON('/fin/fin002/grid_func-ajax/fin002atualizarParcelasDivisao/?ajax=true&T247_Id='              + encodeURIComponent(T247_Id) + 
                                                                                              '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                              '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                              '&novoValor='            + encodeURIComponent(novoValor) +
                                                                                              '&valorTotal='        + encodeURIComponent('{$valorSomado}') +   
                                                                                              '&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}');
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                } else if (campoEditado == 'T247_D031_Id' || campoEditado == 'T247_C004_Id' || campoEditado == 'T247_D073_Id') {
                    divRefresh('{$g['divId']}');                        
                }
            }
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


    case 'fin002gridReparcelamentosTitulo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T166_Id', $g['divId'])),
			array('colunaValor' => 'T196_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
			array('colunaValor' => 'T196_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo'),
			array('colunaValor' => 'T196_D024_Id', 'colunaTitulo' => 'Cliente/Fornecedor', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente/Fornecedor'),
			array('colunaValor' => 'T196_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T196_Data_Emissao', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T196_Valor_Total', 'colunaTitulo' => 'Valor Total','colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T196_Titulos_Geradores', 'colunaTitulo' => 'Títulos Geradores', 'callback'=>'formataGeradores'),
			array('colunaValor' => 'T196_Titulos_Gerados', 'colunaTitulo' => 'Títulos Gerados', 'callback'=>'formataGerados'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T196';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join C007 on C007_Id=T196_C007_Id
		    left join D024 on D024_Id=T196_D024_Id
		    left join T248 on T196_Id=T248_T196_Id
		    WHERE (T196_C004_Id='{$g['empresaAtual']}') 
		    AND (T248_T015_Id = '{$r_acaoId}')
EOT;
		
		// /**
		// * Botões (Versão Acima do GRID)
		// */
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="fin001EnviarEmailReparcelamento">Enviar Email</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#etc010FormUsuariosClienteBtnIncluir').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Usuários Clientes', '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
			'T196_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T196_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'T196_Titulos_Geradores' => array('titulo' => 'Títulos Geradores', 'tipo' => 'texto'),
			'T196_Titulos_Gerados' => array('titulo' => 'Títulos Gerados', 'tipo' => 'texto'),
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
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T196_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Reparcelamentos e Agrupamentos - ID: ' + acaoId, '/fin/fin002/content/fin002ContentReparcelarWindow/', '&acaoId=' + encodeURIComponent(acaoId), [500,400]);
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
