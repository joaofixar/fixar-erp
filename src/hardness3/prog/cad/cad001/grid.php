<?php
namespace hardness;

// Classes
require_once('bibliotecas/classes/CAD001.php');
$CAD001 = new CAD001();
// Carrega funções callback
require_once('prog/cad/cad001/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/cad/cad001/grid_func-js.php');

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad001gridPrincipal':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		$clientePorEmpresa = '';
		$colunaExibirclientePorEmpresa = false;
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
			$colunaExibirclientePorEmpresa = true;
		}

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D024_Id', $g['divId'])),
            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'checkboxImprimirCliente', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'CampoCadastro', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'mostraImagemCadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibir' => false),
            array('colunaValor' => 'campoDoc', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Doc', 'callback' => 'mostraImagemDoc', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Flag_Curva_ABC', 'colunaTitulo' => 'ABC(V)', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'D024_Flag_Curva_ABC_Qtd', 'colunaTitulo' => 'ABC(Q)', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'D024_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaExibirNivel' => 4, 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Flag_Contribuinte', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Codigo_Regime_Tributario', 'colunaTitulo' => 'CRT', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaEditarOculto' => true),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaEditarOculto' => true),
            array('colunaValor' => 'D024_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D024_CNPJ_Matriz', 'colunaTitulo' => 'CNPJ Matriz'),
            array('colunaValor' => 'D024_CPF', 'colunaTitulo' => 'CPF'),
            array('colunaValor' => 'D024_Endereco', 'colunaTitulo' => 'Endereço', 'colunaEditarOculto' => true),
            array('colunaValor' => 'D024_Numero', 'colunaTitulo' => 'Numero', 'colunaEditarOculto' => true),
            array('colunaValor' => 'D024_Bairro', 'colunaTitulo' => 'Bairro', 'colunaEditarOculto' => true),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D024_Dia_Visita', 'colunaTitulo' => 'Dia visita', 'colunaExibir' => true),
            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),			
			array('colunaValor' => 'D024_D046_Id', 'colunaTitulo' => 'Grupo', 'colunaExibir' => true, 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D046', 'D046_Id', 'D046_Nome', 'order by D046_Nome', true), 'callback' => 'mostrarGrupo', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Flag_Lista_Preco', 'colunaTitulo' => 'Lista Preço', 'callback' => 'mostrarListaPreco', 'colunaEditar' => true, 'colunaExibir' => false, 'colunaEditarTodos' => true,'colunaEditarGeraSelect' => array(
					array('title' => 'PREÇO 1', 'value' => '1'),
					array('title' => 'PREÇO 2', 'value' => '2'),
					array('title' => 'PREÇO 3', 'value' => '3'),
					array('title' => 'PREÇO 4', 'value' => '4'),
					array('title' => 'PREÇO 5', 'value' => '5'),
				),
			),
            array('colunaValor' => 'D024_RG', 'colunaTitulo' => 'RG', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'callback' => 'gCorrigeData', 'colunaEditarOculto' => true),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as VendedorInterno', 'colunaTitulo' => 'Vendedor', 'colunaExibir' => false),
            array('colunaValor' => 'D024_C007_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'WHERE C007_Ativo != "N" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInterno', 'style' => 'width:1%'),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaExibir' => true),
            array('colunaValor' => 'Externo.C007_Primeiro_Nome as VendedorExterno', 'colunaTitulo' => 'Externo', 'colunaExibir' => false),
            array('colunaValor' => 'D024_C007_Vendedor_Externo', 'colunaTitulo' => 'Externo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'WHERE C007_Ativo != "N" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorExterno', 'style' => 'width:1%'),
            array('colunaValor' => 'D085_Data', 'colunaTitulo' => 'Último Contato', 'callback' => 'mostraImagemUltimoContato', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Pre_Cadastro', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Tempo', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D084_Regiao', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) as D087_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'datediff(current_date(),D085_Data) as D085_Dias', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Comercial', 'colunaTitulo' => 'C', 'colunaExibir' => false, 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'D024_D021_Id', 'colunaTitulo' => 'Representante', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D021', 'D021_Id', 'D021_Nome_Empresa', 'order by D021_Nome_Empresa', true), 'callback' => 'mostrarRepresentante', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Percentual_Comissao', 'colunaTitulo' => '%Comiss.Repres', 'colunaExibirNivel' => 2, 'colunaEditar' => true, 'colunaEditarNivel' => 4, 'colunaEditarTodos' => false),
            array('colunaValor' => 'D024_Observacoes', 'colunaTitulo' => 'Observação', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Percentual_Desconto_Tabela', 'colunaTitulo' => '% Desc Finan', 'colunaExibir' => false, 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right', 'colunaEditarTodos' => true),
			array('colunaValor' => 'D024_C004_Id', 'colunaTitulo' => 'Empresa', 'colunaExibir' => $colunaExibirclientePorEmpresa, 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C004', 'C004_Id', 'C004_Nome_Abreviado', 'where C004_Flag_Ativo="S" order by C004_Nome_Empresa', true), 'callback' => 'nomeEmpresaC004'),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'C004_Nome_Abreviado', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D024';
		$extra = <<<EOT
		 -- JOIN
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D084 on D084_Id=D020_D084_Id
			left join D085 on D085_Id=D024_D085_Id
			left join D046 on D046_Id=D024_D046_Id
            left join D021 on D021_Id=D024_D021_Id
			left join D013 on D024_Id=D013_D024_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
            left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
			left join C004 on C004_Id=D024_C004_Id
		 -- WHERE
			where D024_Flag_Ativo='S'
			{$clientePorEmpresa}
		 -- GROUP BY
            GROUP BY D024_Id
EOT;

        /**
        * Função que define classes CSS para cada linha do grid
        */
        function gridLinha($array) {
            if ($array['todosCampos']['D024_Flag_Ativo']=='N') {
               return 'linhaCinza';
            }
            if ($array['todosCampos']['D024_Flag_Pre_Cadastro']=='S') {
               return 'linhaVermelho';
            }
        }

		/**
		* Botões (Versão Acima do GRID)
		*/
 /*       if (!personalizacaoBotoes()) {
    		echo <<<EOT
    			<div class="buttonsBottomTop">
EOT;
    		if (isset($g['c029Ids'][103])) {
    		echo <<<EOT
    				<button type="button" id="cad001Incluir">Incluir</button>
    				<button type="button" id="cad001Juntar">Juntar</button>
EOT;
    		}
            if (isset($g['c029Ids'][183])) {
            echo <<<EOT
                    <button type="button" id="cad001IncluirPreCadastro">Incluir Pré Cadastro</button>
EOT;
            }
            if (isset($g['c029Ids'][222])) {
            echo <<<EOT
                    <button type="button" id="cad001CalcularLimite">Calcular Limite</button>
EOT;
            }
        }
*/

		// Botões
        $botoes = array(
            array('titulo' => 'Incluir', 'id' => 'cad001Incluir',  'exibirCallback' => 'return isset($g["c029Ids"][103]);'),
            array('titulo' => 'Juntar', 'id' => 'cad001Juntar',  'exibirCallback' => 'return isset($g["c029Ids"][103]);'),
            array('titulo' => 'Incluir Pré Cadastro', 'id' => 'cad001IncluirPreCadastro',  'exibirCallback' => 'return isset($g["c029Ids"][183]);'),
            array('titulo' => 'Calcular Limite', 'id' => 'cad001CalcularLimite',  'exibirCallback' => 'return isset($g["c029Ids"][222]);'),
		);
        echo Botoes::processar($botoes);

		echo <<<EOT
                <!--<button type="button" id="cad001ImprimirEtiquetas">Imprimir Etiquetas</button>-->
				<!--<button type="button" id="cad001EtiquetasClientes">Etiquetas</button>-->
                <!--<button type="button" id="cad001DesmarcarTodos">Desmarcar Todos</button>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#cad001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [960,583]);
				});
                $('#cad001IncluirPreCadastro').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001OrcamentoIncluirCliente/','', [600,350]);
                });
				$('#cad001Juntar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Clientes/Fornecedores', '/cad/cad001/content/cad001ContentJuntar/','', [400,200]);
				});
				$('#cad001EtiquetasClientes').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Etiquetas', '/cad/cad001/content/cad001ContentImprimirClientes/','', [300,200]);
				});
                $('#cad001CalcularLimite').unbind('click').bind('click', function(data) {
                    var buttons = { 
                        "Confirmar": function() {
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/cad/cad001/grid_func-ajax/cad001CalcularLimite/?ajax=true&callback=?', function(request) {
                                $('#{$g['divId']}').hideLoading();
                                if(!request.code){
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('ERRO', request.data, \$buttons);
                                }else {
                                    dialogConfirm('Calcular Limite', request.data, \$buttons);
                                }
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Fechar": function() { $(this).dialog("destroy").remove(); } 
                    }
                    dialogConfirm('Calcular Limite', '<b>Atenção</b>!!!<br />Esse processo irá atualizar o valor limite dos clientes ativos de acordo com a configuração do seu sistema.', buttons);
                });

                $('#cad001ImprimirEtiquetas').unbind('click').bind('click', function(data) {
                $.getJSON('/cad/cad001/grid_func-ajax/cad001VerificaProdutosImprimir/?ajax=true&callback=?', function(request) {
                        if(request.code){
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiquetas', '/cad/cad001/content/cad001ContentImprimirEtiquetas/', '', [800,500]);
                        }else{
                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                            dialogConfirm('Erro', 'Não existem clientes selecionados para impressão.', \$buttons);
                        }
                    });
                });
                $('#cad001DesmarcarTodos').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/cad/cad001/grid_func-ajax/cad001DesmarcarImprimir/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        divRefresh('{$g['divId']}', true);
                    });
                });
			} </script></div>
EOT;


		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
            'D085_Data' => array('titulo' => 'Contato', 'tipo' => 'data'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D024_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
            'D024_CNPJ_Matriz' => array('titulo' => 'CNPJ Matriz', 'tipo' => 'texto'),
            'D024_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
            'D024_RG' => array('titulo' => 'RG', 'tipo' => 'texto'),
            'D024_Data_Cadastro' => array('titulo' => 'Cadastro', 'tipo' => 'data'),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'Externo.C007_Primeiro_Nome' => array('titulo' => 'Externo', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'D024_Flag_Pre_Cadastro' => array('titulo' => 'Pré-cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'naoGerarExtra' => ''),
            'D024_Flag_Cliente' => array('titulo' => 'Cliente?', 'tipo' => 'select', 'select' => array(
                	array('title' => 'TODOS', 'value' => '', 'selected' => true),
                	array('title' => 'Sim', 'value' => 'S'),
                	array('title' => 'Não', 'value' => 'N'),
              	),
            ),
            'D024_Flag_Fornecedor' => array('titulo' => 'Fornecedor ?', 'tipo' => 'select', 'select' => array(
                	array('title' => 'TODOS', 'value' => '', 'selected' => true),
                	array('title' => 'Sim', 'value' => 'S'),
                	array('title' => 'Não', 'value' => 'N'),
              	),
            ),
            'D024_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D024_Flag_Contribuinte' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'D024_Codigo_Regime_Tributario' => array('titulo' => 'CRT', 'tipo' => 'texto'),
            'D024_Observacoes' => array('titulo' => 'Observação', 'tipo' => 'texto'),
            'D024_Flag_Classe' => array('titulo' => 'Classe', 'tipo' => 'texto'),
            'D024_E_Mail' => array('titulo' => 'Email', 'tipo' => 'texto'),
            'D024_Dia_Visita' => array('titulo' => 'Dia visita', 'tipo' => 'texto'),
            'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Totais
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
		* Totais (Versão Acima do GRID)
		*/
		/*
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(DISTINCT D024_Id)", $from, gRetirarGroupBy($extra));

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {            
			acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + array['todosCampos']['D024_Id'], '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(array['todosCampos']['D024_Id']) + '&D024_Id=' + encodeURIComponent(array['todosCampos']['D024_Id']) + '&tabela={$from}', [930,583]);
			});
		} </script></div>
EOT;
/* 		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&D024_Id=' + encodeURIComponent(id) + '&tabela={$from}', [930,583]);
			});
		} </script></div>
EOT; */

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
        //$g['smarty']->assign('gridColunasFixo', 2);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;

	case 'cad001gridTrabalho':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D024_Id', $g['divId'])),
            array('colunaValor' => 'D024_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array(
            		array('title' => 'Sim', 'value' => 'S'),
            		array('title' => 'Não', 'value' => 'N'),
            	),
            ),
            array('colunaValor' => 'D024_Flag_Cliente', 'colunaTitulo' => 'Cliente', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array(
            		array('title' => 'Sim', 'value' => 'S'),
            		array('title' => 'Não', 'value' => 'N'),
            	),
            ),
            array('colunaValor' => 'D024_Flag_Fornecedor', 'colunaTitulo' => 'Forn', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array(
            		array('title' => 'Sim', 'value' => 'S'),
            		array('title' => 'Não', 'value' => 'N'),
            	),
            ),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaEditar' => true, 'uppercase' => '1'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'substr(D021_Nome_Empresa,1,10) as NomeEmpresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor Interno', 'colunaExibir' => false),
            array('colunaValor' => 'D024_C007_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true), 'colunaEditar' => true, 'callback' => 'mostrarVendedorInterno', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D024_Flag_Lista_Preco', 'colunaTitulo' => 'Lista Preço', 'callback' => 'mostrarListaPreco', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array(
                array('title' => '...', 'value' => '', 'selected' => true),
                array('title' => 'Preço 1', 'value' => '1'),
                array('title' => 'Preço 2', 'value' => '2'),
                array('title' => 'Preço 3', 'value' => '3'),
              ), 'colunaExibir' => false),
            array('colunaValor' => 'D024_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D024_CPF', 'colunaTitulo' => 'CPF'),
            array('colunaValor' => 'D024_Valor_Limite', 'colunaTitulo' => 'Valor Limite', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'font-weight:bold;'),
            array('colunaValor' => 'D024_E_Mail', 'colunaTitulo' => 'E-mail'),
            array('colunaValor' => 'D024_Flag_Pre_Cadastro', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Comercial', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Tempo', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D084_Regiao', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) as D087_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'datediff(current_date(),D085_Data) as D085_Dias', 'colunaTitulo' => '', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D024';
		$extra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D084 on D084_Id=D020_D084_Id
			left join D085 on D085_Id=D024_D085_Id
			left join D046 on D046_Id=D024_D046_Id
			left join D021 on D021_Id=D024_D021_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where D024_Flag_Ativo='S'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad001Incluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [950,550]);
				});
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
            'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
            'D085_Data' => array('titulo' => 'Último Contato', 'tipo' => 'data'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D024_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
            'D024_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
            'D024_RG' => array('titulo' => 'RG', 'tipo' => 'texto'),
            'D024_Data_Cadastro' => array('titulo' => 'Data cadastro', 'tipo' => 'data'),
            'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Flag_Pre_Cadastro' => array('titulo' => 'Pré-cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'naoGerarExtra' => ''),
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
				<span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [960,583]);
			});
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

	case 'cad001gridClientesInativos':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D024_Id', $g['divId'])),
            array('colunaValor' => 'validaCampoExtratoCliente', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'checkboxImprimirCliente', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'CampoCadastro', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'mostraImagemCadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibir' => false),
            array('colunaValor' => 'campoDoc', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Doc', 'callback' => 'mostraImagemDoc', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Flag_Curva_ABC', 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
            array('colunaValor' => 'D024_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaExibirNivel' => 4, 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaEditar' => true),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaEditar' => true),
            array('colunaValor' => 'D024_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D024_CPF', 'colunaTitulo' => 'CPF'),
            array('colunaValor' => 'D024_Endereco', 'colunaTitulo' => 'Endereço', 'colunaEditar' => true),
            array('colunaValor' => 'D024_Numero', 'colunaTitulo' => 'Numero', 'colunaEditar' => true),
            array('colunaValor' => 'D024_Bairro', 'colunaTitulo' => 'Bairro', 'colunaEditar' => true),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'substr(D021_Nome_Empresa,1,10) as Representante', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo', 'colunaExibir' => true),
            array('colunaValor' => 'D024_RG', 'colunaTitulo' => 'RG', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'callback' => 'gCorrigeData', 'colunaEditar' => true),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D085_Data', 'colunaTitulo' => 'Último Contato', 'callback' => 'mostraImagemUltimoContato', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Pre_Cadastro', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Tempo', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D084_Regiao', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) as D087_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'datediff(current_date(),D085_Data) as D085_Dias', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Flag_Comercial', 'colunaTitulo' => 'C', 'colunaExibir' => false, 'colunaExibirNivel' => 4),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D024';
		$extra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D084 on D084_Id=D020_D084_Id
			left join D085 on D085_Id=D024_D085_Id
			left join D046 on D046_Id=D024_D046_Id
			left join D021 on D021_Id=D024_D021_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where D024_Flag_Ativo='N'
EOT;

        /**
        * Função que define classes CSS para cada linha do grid
        */
        function gridLinha($array) {
            if ($array['todosCampos']['D024_Flag_Ativo']=='N') {
               return 'linhaCinza';
            }
            if ($array['todosCampos']['D024_Flag_Pre_Cadastro']=='S') {
               return 'linhaVermelho';
            }
        }

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* echo <<<EOT
EOT; */

		$botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
            'D085_Data' => array('titulo' => 'Contato', 'tipo' => 'data'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D024_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
            'D024_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
            'D024_RG' => array('titulo' => 'RG', 'tipo' => 'texto'),
            'D024_Data_Cadastro' => array('titulo' => 'Cadastro', 'tipo' => 'data'),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Flag_Pre_Cadastro' => array('titulo' => 'Pré-cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'naoGerarExtra' => ''),
            'D024_Flag_Cliente' => array('titulo' => 'Cliente?', 'tipo' => 'select', 'select' => array(
                	array('title' => 'TODOS', 'value' => '', 'selected' => true),
                	array('title' => 'Sim', 'value' => 'S'),
                	array('title' => 'Não', 'value' => 'N'),
              	),
            ),
            'D024_Flag_Fornecedor' => array('titulo' => 'Fornecedor ?', 'tipo' => 'select', 'select' => array(
                	array('title' => 'TODOS', 'value' => '', 'selected' => true),
                	array('title' => 'Sim', 'value' => 'S'),
                	array('title' => 'Não', 'value' => 'N'),
              	),
            ),
            'D024_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
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
		/* 
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		*/
		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,553]);
			});
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

	case 'cad001GridVeiculos':
		$D024_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D116_Id', $g['divId'])),
			array('colunaValor'=>'D116_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir'=>false),
			array('colunaValor'=>'D116_D024_Id', 'colunaTitulo'=>'Empresa', 'colunaExibir'=>false),
			array('colunaValor'=>'D116_Placa', 'colunaTitulo'=>'Placa'),
			array('colunaValor'=>'D116_D126_Id', 'colunaTitulo'=>'Tipo Veículo', 'colunaExibir'=>false),
			array('colunaValor'=>'D116_Modelo', 'colunaTitulo'=>'Modelo'),
			array('colunaValor'=>'D116_Cor', 'colunaTitulo'=>'Cor'),
			array('colunaValor'=>'D126_Descricao', 'colunaTitulo'=>'Tipo Veículo'),
			array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Empresa'),
			array('colunaValor'=>'D116_Observacao', 'colunaTitulo'=>'Observação'),
			//array('colunaValor'=>'Documento', 'colunaTipo' => 'livre', 'colunaTitulo'=>'Documento', 'callback'=>'abrirDocumento'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D116';

		$extra = <<<EOT
			left join D024 on D024_Id=D116_D024_Id
			left join D126 on D126_Id=D116_D126_Id
			WHERE D116_D024_Id='{$D024_Id}'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad043FormCadastroVeiculosEspecificacoesBtnIncluir">Incluir</button>

			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad043FormCadastroVeiculosEspecificacoesBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de Veículos', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&tab=geral' + '&D024_Id=' + ('{$D024_Id}'), [600,450]);
				});
			} </script></div>
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D116_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Veículos - ID: ' + acaoId, '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&acaoId=' + encodeURIComponent(acaoId) +
	        																																									   '&D024_Id=' + encodeURIComponent('{$D024_Id}'), [600,450]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad001Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001content02/','', [900,550]);
			});
		} </script></div>
EOT;
	break;

	case 'cad001GridDocumentosVeiculos':
		$D116_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D127_Id', $g['divId'])),
			array('colunaValor'=>'D127_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir'=>false),
			array('colunaValor'=>'D127_Nome_Arquivo', 'colunaTitulo'=>'Nome Arquivo'),
			array('colunaValor'=>'D127_Descricao', 'colunaTitulo'=>'Descrição'),
			array('colunaValor'=>'Documento', 'colunaTipo' => 'livre', 'colunaTitulo'=>'Documento', 'callback'=>'abrirDocumento'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D127';

		$extra = <<<EOT
			left join D116 on D116_Id=D127_D116_Id
			WHERE D127_D116_Id='{$D116_Id}'
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D127_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    			$('#cad001Incluir').unbind('click').bind('click', function(data) {
    				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001content02/','', [900,550]);
    			});
    		} </script></div>
EOT;
	break;

    case 'cad001gridClientesContatos':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'D013_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D013_Nome_Contato', 'colunaTitulo' => 'Contato'),
            array('colunaValor' => 'D013_E_Mail', 'colunaTitulo' => 'Email', 'style' => 'width:1%'),
            array('colunaValor' => "concat(D013_DDD_Telefone_1,'-',D013_Telefone_1) as D013_Telefone_1", 'colunaTitulo' => 'Telefone', 'style' => 'width:1%'),
            array('colunaValor' => "concat(D013_DDD_Telefone_2,'-',D013_Telefone_2) as D013_Telefone_2", 'colunaTitulo' => 'Telefone 2', 'style' => 'width:1%'),
            array('colunaValor' => "concat(D013_DDD_Celular,'-',D013_Celular) as D013_Celular", 'colunaTitulo' => 'Celular', 'style' => 'width:1%'),
            array('colunaValor' => 'D013_Data_Nascimento', 'colunaTitulo' => 'Nascimento', 'callback' => 'gCorrigeData', 'style' => 'width:1%'),
            array('colunaValor' => 'D013_Tipo_Contato', 'colunaTitulo' => 'Tipo', 'style' => 'width:1%'),
            array('colunaValor' => "IF(D013_Flag_Ativo = 'S', 'SIM', 'NÃO')", 'colunaTitulo' => 'Ativo', 'style' => 'width:1%'),
            array('colunaValor' => "D020_Nome_Cidade", 'colunaTitulo' => 'Cidade', 'style' => 'width:1%'),
            array('colunaValor' => "D018_UF", 'colunaTitulo' => 'UF', 'style' => 'width:1%'),
            array('colunaValor' => "C007_Primeiro_Nome", 'colunaTitulo' => 'Vendedor', 'style' => 'width:1%'),
			array('colunaValor' => 'D013_Id as cliente', 'colunaTitulo' => 'eMail', 'callback' => 'campoeMailSenhaCliente', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D013_Flag_Ecommerce', 'colunaTitulo' => 'Acessa e-commerce?', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array(
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'colunaAlinhar' => 'center','callback'=>'D013_Flag_Ecommerce', 'colunaExibir' => false, 'style'=>'width:1%;'),
        );

        /**
        * Definição da SQL
        */
        $from = 'D013';

        $extra = <<<EOT
            Left join D024 on D024_Id=D013_D024_Id
            left join D018 on D018_Id=D024_D018_Id
            left join D020 on D020_Id=D024_D020_Id
            left join C007 on C007_Id=D024_C007_Vendedor_Interno
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
                var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&tab=contatos', [930,583]);
            });
        } </script></div>
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
            'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
            'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'D013_Telefone_1,D013_Telefone_2, D013_Celular' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
            'D013_E_Mail' => array('titulo' => 'E-Mail', 'tipo' => 'texto'),
            'month(D013_Data_Nascimento)' => array('titulo' => 'Mês aniversário', 'tipo' => 'numero'),
            'day(D013_Data_Nascimento)' => array('titulo' => 'Dia aniversário', 'tipo' => 'numero'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D024_Flag_Pessoa_Fisica_Juridica' => array('titulo' => 'Pessoa', 'tipo' => 'select', 'select' => array(
				array('title' => 'TODOS', 'value' => '', 'selected' => true),
				array('title' => 'Jurídica', 'value' => 'J'),
				array('title' => 'Física', 'value' => 'F'),
				array('title' => 'Internacional', 'value' => 'I'),
			  ),
			),
        );
		/**
		* Botões (Versão Acima do GRID) 
		*Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid		
		*/
		/* echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		$botoes = array(
		);
		echo Botoes::processar($botoes);

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

	case 'cad001GridContatos':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D013_Id', $g['divId'])),
            array('colunaValor' => 'D013_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D013_Nome_Contato', 'colunaTitulo' => 'Nome'),
            array('colunaValor' => 'D013_E_Mail', 'colunaTitulo' => 'Email'),
            array('colunaValor' => "concat(D013_DDD_Telefone_1,'-',D013_Telefone_1) as D013_Telefone_1", 'colunaTitulo' => 'Telefone'),
            array('colunaValor' => "concat(D013_DDD_Telefone_2,'-',D013_Telefone_2) as D013_Telefone_2", 'colunaTitulo' => 'Telefone 2'),
            array('colunaValor' => "concat(D013_DDD_Celular,'-',D013_Celular) as D013_Celular", 'colunaTitulo' => 'Celular'),
            array('colunaValor' => 'D013_Data_Nascimento', 'colunaTitulo' => 'Nascimento', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D013_Tipo_Contato', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => "IF(D013_Flag_Ativo = 'S', 'SIM', 'NÃO')", 'colunaTitulo' => 'Ativo'),
			array('colunaValor' => 'D013_Flag_Ecommerce', 'colunaTitulo' => 'Acessa e-commerce?', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array(
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'colunaAlinhar' => 'center','callback'=>'D013_Flag_Ecommerce', 'colunaExibir' => false, 'style'=>'width:1%;'),
            array('colunaValor' => 'D013_Id as cliente', 'colunaTitulo' => 'eMail', 'callback' => 'campoeMailSenhaCliente', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "IF(D013A_Flag_Envio_Certificado = 'S', 'SIM', 'NÃO')", 'colunaTitulo' => 'Env. Cert'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D013';

		$extra = <<<EOT
			left join D013A on D013.D013_Id = D013A.D013A_D013_Id
			WHERE D013_D024_Id='{$r_D024_Id}'
EOT;
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D013_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
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
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001content02/','', [900,550]);
				});
			} </script></div>
EOT;
	break;

	case 'cad001GridAgenda':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
	        array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId'])),
            array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'CONCAT(D085_Data, " ", D085_Hora)', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'IF(D085_Data_Previsao_Retorno = "0000-00-00", "", CONCAT(D085_Data_Previsao_Retorno, " ", D085_Hora_Previsao_Retorno))', 'colunaTitulo' => 'Retornar', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D085_T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem'),
            array('colunaValor' => 'D085_C007_Id', 'colunaTitulo' => 'IdUsuarioAtual', 'colunaExibir' => false),
        );
		/**
		*
		* Definição da SQL
		*/
		$from = 'D085';

        if($g['C031']['exibirRegistroAgendaManual'] != 'S'){
            $extraFlagManual = " AND D085_Flag_Manual = 'N' ";
        }

		$extra = <<<EOT
			left join C007 on C007_Id=D085_C007_Id
			where D085_D024_Id = {$r_acaoId}
            {$extraFlagManual}
			order by D085_Data desc,D085_Id desc
EOT;
		if (!empty($xConsulta_Clausula_Filtro)) {
			$extra .= <<<EOT
			      and $xConsulta_Clausula_Filtro
EOT;
		}

        /**
        * Botões (Versão Acima do GRID)
        */
        echo <<<EOT
            <div class="buttonsBottomTop">
                <button type="button" id="cad001IncluirAgenda">Incluir</button> 
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#cad001IncluirAgenda').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/cad/cad001/content/cad001ContentAgenda/', '&tab=geral&D024_Id={$r_acaoId}', [700,450]);
                });
            } </script></div>
EOT;
		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)
        */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        		$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').replace(/D085_Id-/, '');
            		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Agenda', '/cad/cad001/content/cad001ContentAgenda/', '&acaoId=' + encodeURIComponent(id) + '&D024_Id={$r_acaoId}&tabela={$from}', [700,450]);
        		});
    		} </script></div>
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
            'D085_Id' => array('titulo' => 'ID Agenda', 'tipo' => 'texto', 'exibir' => false),
            'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto', 'exibir' => false),
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D085_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
            'D085_Data_Previsao_Retorno' => array('titulo' => 'Retornar', 'tipo' => 'data'),
            'D085_Data_Retorno' => array('titulo' => 'Data retorno', 'tipo' => 'data', 'exibir' => false),
            'D085_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
            'D085_Retorno' => array('titulo' => 'Retorno', 'tipo' => 'texto', 'exibir' => false),
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
		* Geração: Grid
		*/
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(DISTINCT D085_Id)", $from, gRetirarGroupBy($extra));

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Total contatos <br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;
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

	case 'cad001GridDocumentos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D087_Id', $g['divId'])),
			array('colunaValor' => 'D087_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D087_D024_Id', 'colunaTitulo' => 'IdCliente/Fornecedor', 'colunaExibir' => false),
			array('colunaValor' => 'D087_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'D087_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D087_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D087_Data_Validade', 'colunaTitulo' => 'Validade', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
            array('colunaValor' => 'visualizarArquivo', 'colunaTitulo' =>'Visualizar', 'colunaTipo'=>'livre', 'callback'=>'visualizarArquivo', 'callbackParameter' => array('D087_Id', 'documentoCliente'), 'style' => 'width: 1%'),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'D087';

		$extra = <<<EOT
			left join C007 on C007_Id=D087_C007_Id
			where 
			(D087_D024_Id = {$r_acaoId})
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
    				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
    				var id = $(this).closest('tr').attr('id').replace(/D087_Id-/, '');
    				divSetarAcaoId('{$r_divReload}', id);
    				divRefresh('{$r_divReload}');
    			});
    			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		$novoIdTelaForm = uniqid();
	break;

	case 'cad001gridEmail':
        /**
        * Definição do Grid (Tabela)
        */
	       $grid = array(
    	   	// Chave Primária
    	   	array('colunaValor' => 'T139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
    	   	array('colunaValor' => 'T139_Pasta', 'colunaTitulo' => 'P'),
    	   	array('colunaValor' => 'T139_De', 'colunaTitulo' => 'De'),
    	   	array('colunaValor' => 'T139_Para', 'colunaTitulo' => 'Para'),
    	   	array('colunaValor' => 'T139_Titulo', 'colunaTitulo' => 'Assunto'),
    	   	array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'Data/Hora', 'callback'=>'gCorrigeData'),
    	);

		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			left join D024 on D024_Id=T139_D024_Id
			where (T139_D024_Id='{$r_acaoId}')
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006EnviarEmail2">Novo Email</button>
				<button type="button" id="etc006VerificarEmailsEnviadosCad001">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D024_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosCad001').unbind('click').bind('click', function(data) {
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
		   'T139_Titulo' => array('titulo' => 'Titulo', 'tipo' => 'texto'),
		   'D024_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
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

	case 'cad001GridVeiculosEspecificacoes':
		$CAD001->insereRegistrosT165($r_acaoId);
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T165_Id', $g['divId'])),
			array('colunaValor' => 'T165_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T165_D116_Id', 'colunaTitulo' => 'Id Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo'),
			array('colunaValor' => 'T165_D131_Id', 'colunaTitulo' => 'Especificações', 'colunaExibir'=>false),
			array('colunaValor' => 'D131_Descricao', 'colunaTitulo' => 'Especificação'),
			array('colunaValor' => 'T165_Valor', 'colunaTitulo' => 'Valor', 'colunaEditar'=>true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T165';

		//left join D116 on D116_Id=D131_D116_Id
		$extra = <<<EOT
		left join D116 on D116_Id=T165_D116_Id
		left join D131 on D131_Id=T165_D131_Id
		where D116_Id={$r_acaoId}
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D131_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D131_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Especificações de Veículos - ID: ' + acaoId, '/cad/cad045/content/cad045ContentVeiculosEspecificacoesIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
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

    case 'cad001GridProdutosImprimir':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'D024_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'checkboxImprimirCliente', 'style' => 'width:1%'),
            array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'Código', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
            array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Razão Social'),
            array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Nome Fantasia', 'colunaExibir'=>false),
            array('colunaValor'=>'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade'),
            array('colunaValor'=>'D018_UF', 'colunaTitulo'=>'UF'),
            array('colunaValor'=>'D024_CNPJ', 'colunaTitulo'=>'CNPJ'),
            array('colunaValor'=>'D024_CPF', 'colunaTitulo'=>'CPF'),
            array('colunaValor'=>'D024_Flag_Pre_Cadastro', 'colunaTitulo'=>'', 'colunaExibir' => false),
            array('colunaValor'=>'D024_Flag_Comercial', 'colunaTitulo'=>'', 'colunaExibir' => false),
            array('colunaValor'=>'D024_Flag_Tempo', 'colunaTitulo'=>'', 'colunaExibir' => false),
            array('colunaValor'=>'D084_Regiao', 'colunaTitulo'=>'', 'colunaExibir' => false),
            array('colunaValor'=>'(select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) D087_Id', 'colunaTitulo'=>'', 'colunaExibir' => false),
            array('colunaValor'=>'D085_Mensagem', 'colunaTitulo'=>'', 'colunaExibir' => false),
            array('colunaValor'=>'datediff(current_date(),D085_Data) D085_Dias', 'colunaTitulo'=>'', 'colunaExibir' => false),
        );

        /**
        * Definição da SQL
        */
        $from = 'D024';
        $extra = <<<EOT
            left join D020 on D020_Id=D024_D020_Id
            left join D018 on D018_Id=D024_D018_Id
            left join D084 on D084_Id=D020_D084_Id
            left join D085 on D085_Id=D024_D085_Id
            left join D046 on D046_Id=D024_D046_Id
            left join D021 on D021_Id=D024_D021_Id
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
            where D024_Flag_Ativo='S'
            and D024_Flag_Imprimir_Etiqueta='S'
EOT;

        /*
        * Botões (Versão Acima do GRID)
        echo <<<EOT
            <div class="buttonsBottomTop">
EOT;
        if (isset($g['c029Ids'][103])) {
        echo <<<EOT
                <button type="button" id="cad001Incluir">Incluir</button>
EOT;
        }
        echo <<<EOT
                <!--<button type="button" id="cad001EtiquetasClientes">Etiquetas</button>-->
                <button type="button" id="cad001ImprimirEtiquetas">Imprimir Etiquetas</button>
                <button type="button" id="cad001DesmarcarTodos">Desmarcar Todos</button>
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#cad001Incluir').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [950,550]);
                });
                $('#cad001EtiquetasClientes').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Etiquetas', '/cad/cad001/content/cad001ContentImprimirClientes/','', [300,200]);
                });

                $('#cad001ImprimirEtiquetas').unbind('click').bind('click', function(data) {
                $.getJSON('/cad/cad001/grid_func-ajax/cad001VerificaProdutosImprimir/?ajax=true&callback=?', function(request) {
                        if(request.code){
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiquetas', '/cad/cad001/content/cad001ContentImprimirEtiquetas/', '', [800,500]);
                        }else{
                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                            dialogConfirm('Erro', 'Não existem produtos selecionados para impressão.', \$buttons);
                        }
                    });
                });
                $('#cad001DesmarcarTodos').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/cad/cad001/grid_func-ajax/cad001DesmarcarImprimir/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        divRefresh('{$g['divId']}', true);
                    });
                });
            } </script></div>
EOT;

        /**
        * Filtro

        $gridFiltro = array(
            'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
            'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
            'D085_Data' => array('titulo' => 'Último Contato', 'tipo' => 'data'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D024_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
            'D024_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
            'D024_RG' => array('titulo' => 'RG', 'tipo' => 'texto'),
            'D024_Data_Cadastro' => array('titulo' => 'Data cadastro', 'tipo' => 'data'),
            'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);


        /*
        * Botões (Versão Acima do GRID)

        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
                var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [960,583]);
            });
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

    case 'cad001GridEnderecos':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D148_Id', $g['divId'])),
			array('colunaValor'=>'D148_Id', 'colunaTitulo'=>'Código', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor'=>'D148_Nome', 'colunaTitulo'=>'Nome/Descrição', 'style' => 'width: 1%'),
			array('colunaValor'=>'D018_UF', 'colunaTitulo'=>'Estado', 'style' => 'width: 1%'),
			array('colunaValor'=>'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade', 'style' => 'width: 1%'),
			array('colunaValor'=>'D148_Rua', 'colunaTitulo'=>'Rua'),
            array('colunaValor'=>'D148_Cep', 'colunaTitulo'=>'Cep'),
			array('colunaValor'=>'D148_Flag_Tipo', 'colunaTitulo'=>'Padrão', 'callback' => 'D148_Flag_Tipo', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D148';
		$extra = <<<EOT
			left join D020 on D020_Id=D148_D020_Id
			left join D018 on D018_Id=D148_D018_Id
			left join D030 on D030_Id=D148_D030_Id
			where D148_D024_Id='{$r_acaoId}'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
EOT;
		//if (isset($g['c029Ids'][103])) {
		echo <<<EOT
				<button type="button" id="cad001IncluirEndereco">Incluir</button>
EOT;
		//}
		echo <<<EOT
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad001IncluirEndereco').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Endereço', '/cad/cad001/content/cad001contentCadastroEndereco/','&D024_Id={$r_acaoId}', [560,345]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D148_Nome' => array('titulo' => 'Nome/Descrição', 'tipo' => 'Texto'),
			'D148_Rua' => array('titulo' => 'Rua', 'tipo' => 'Texto'),
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
		* Botões (Versão Acima do GRID)
		*/
		/*
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Endereços<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D148_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastroEndereco/', '&D024_Id={$r_acaoId}&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [560,345]);
			});
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
        //$g['smarty']->assign('gridColunasFixo', 2);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
}

