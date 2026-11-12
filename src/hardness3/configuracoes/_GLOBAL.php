<?php
/**
* Restrições globais
* São restrições que não são associadas automaticamente a algum campo de um tabela
*/
$conf['restricaoIncluirUpdate']['agruparNovoVencimento'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
	if ($retorno === true){
		$retorno = rDataMenorTrintaDias($valor); 
	}		
EOT;
$conf['restricaoIncluirUpdate']['numeroDuplicata'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['globalNaoVazio'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['globalNaoSelecionado'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['Portador_D027_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['globalDataMaiorIgualAtual'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
	if ($retorno === true){
		$retorno = rDataMaiorIgualAtual($valor);
	}		
EOT;
$conf['restricaoIncluirUpdate']['globalData'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['DiasDuplicar'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
	if($retorno === true){
		$retorno = rNumeroMaiorQueZero($valor);
	}
EOT;

/**
* camposNaoUsar
* 
* Lista de campos que não serão utilizados (será removido do FORM e do GRID).
*/
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'ven002Veiculo',
	'ven002FormVeiculos',
	'ven001FormVeiculosBtnNew',
	'ven001FormVeiculosProdutoPedidoBtnNew',
	'idVeiculoPedido',
	'idVeiculoOrcamento',
	'ven001FormVeiculosProdutoOrcamentoBtnNew',
	'ven001FormVeiculos'
));

/**
* tabsNaoUsar
* 
* Lista de tabs que não serão utilizados.
*/
$conf['tabsNaoUsar'] = array_merge($conf['tabsNaoUsar'], array(
	'est002TabPrincipal' => array('Geral'),
	'cad001tabPrincipal' => array('Trabalho')
));

$conf['camposValorPadrao']['primeiroVencimentoNum'] = '5';
$conf['camposValorPadrao']['IntervalorDias'] = '28';
$conf['camposValorPadrao']['DataInicio'] = date('d/m/Y', mktime(1, 1, 1, date('n'), 1, date('Y')));
$conf['camposValorPadrao']['DataFim'] = date('t/m/Y', mktime(1, 1, 1, date('n'), 1, date('Y')));

/**
* .ini (variaveis de configurações do sistema
*/
$conf['ini']['exibirLimitesOrcamentoPedido'] = true;       //Define se é ou não para exibir os Limite do cliente e o saldo atual no orçamento e pedido.
$conf['ini']['senhaDataMarcoExpiracao'] = '';              // Marco YYYY-MM-DD para ativar a expiração de senha neste banco. Vazio desativa a política.
$conf['ini']['senhaPrazoVencimentoDias'] = 120;            // Prazo normal de validade da senha.
$conf['ini']['senhaPrazoInicialMarcoDias'] = 7;            // Prazo para senhas anteriores ao marco de ativação.
$conf['ini']['senhaDiasAvisoVencimento'] = 7;              // Dias antes do vencimento para exibir o aviso.
$conf['ini']['precoTabelado'] = true;                      //se true executa trigger para setar valor default (D009_Flag_Preço_Tabelado = 'S') na coluna Fixo em Produtos - Custos, true -> Eurosul
$conf['ini']['numeroServicosFuncionarioExecSimult'] = 3;
$conf['ini']['diasMaxOrcamentoPendente'] = 10; 				// número máximo para que a venda seja efetivada
$conf['ini']['siteTema'] = 'black1'; 						// tema padrão do site (statis/css/tema/*)
$conf['ini']['utilizaEcommerce'] = true;
$conf['ini']['mensagemLiberacaoDeCredito'] = true;			// se true irá aparecer mensagens de liberação de crédito para o pessoal do financeiro
$conf['ini']['orcamentoCarregarPrimeiroContato'] = false;	// ao selecionar o cliente, por padrão carrega o primeiro contado
$conf['ini']['usarDescontoProdutos'] = true;				// afeta os updates das triggers da T004  e T006
$conf['ini']['usarAlcada'] = true;                         // Define se a empresa usa o sistema de alçadas para o orçamento ou não
$conf['ini']['extraProdutosTablet'] = '';                  // Clausula extra na exibição de produtos no tablet

// CONFIGURAÇÃO DA ALÇADA POR PRODUTO
$conf['ini']['usarAlcadaPorProduto'] = 'N';                 	// Define se a empresa usa o sistema de alçadas por produto ou não
$conf['ini']['alcadaDescontoVendedorPorProduto'] = '0,01';		// Caso seja para considerar a alçada por produto, o sistema considera também essas variáveis. Deesconto máximo em um item que o vendedor pode dar
$conf['ini']['alcadaDescontoSupervisorPorProduto'] = '3,00';	// Caso seja para considerar a alçada por produto, o sistema considera também essas variáveis. Deesconto máximo em um item que o vendedor pode dar

$conf['ini']['usarAlcadaTipo'] = 1;							// Define qual tipo de alçada será usada para o orçamento.
$conf['ini']['alcadaQuantidadeMinimaEstoque'] = '0.00';    // Define a quantidade mínima que a empresa deve possuir de produtos em estoque
//Tipo alçada 1
$conf['ini']['alcadaDescontoVendedor'] = '44,00';			// Alçada de desconto que o vendedor pode conceder (%). Este campo deve possuir no mínimo duas casas decimais
$conf['ini']['alcadaDescontoSupervisor'] = '44,00';			// Alçada de desconto que o supervisor pode conceder (%). Este campo deve possuir no mínimo duas casas decimais
$conf['ini']['regrasVendedor'] = array(						// parâmetros do vendedor 
	array('valor' =>99, 'pm' => 999),
	array('valor' =>299, 'pm' => 999),
	array('valor' =>999, 'pm' => 999),
	array('valor' =>9999999999, 'pm' => 999),
);
$conf['ini']['regrasSupervisor'] = array(					// parâmetros do supervisor 
	array('valor' =>99, 'pm' => 999),
	array('valor' =>299, 'pm' => 999),
	array('valor' =>999, 'pm' => 999),
	array('valor' =>9999999999, 'pm' => 999),
);

//Tipo alçada 2
$conf['ini']['regrasVendedorPorParcelas'] = array(						// parâmetros do vendedor 
	array('parcelas' =>1, 'pm' => 10, 'desconto' => '50,00'),
	array('parcelas' =>6, 'pm' => 75, 'desconto' => '50,00'),
	array('parcelas' =>10, 'pm' => 120, 'desconto' => '50,00'),
	array('parcelas' =>999999999, 'pm' => 999999999, 'desconto' => '50,00'),
);
$conf['ini']['regrasSupervisorPorParcelas'] = array(					// parâmetros do supervisor 
	array('parcelas' =>1, 'pm' => 10, 'desconto' => '6,00'),
	array('parcelas' =>6, 'pm' => 75, 'desconto' => '2,00'),
	array('parcelas' =>10, 'pm' => 120, 'desconto' => '0,00'),
	array('parcelas' =>999999999, 'pm' => 999999999, 'desconto' => '0,00'),
);

$conf['ini']['usarAlcadaCompra'] = false;					// Define se a empresa usa o sistema de alçadas para compras ou não
$conf['ini']['alcadaCompraComprador'] = '3000,00';			// Alçada que define o valor máximo para que um comprador possa gerar uma OC na cotação de compra
$conf['ini']['alcadaCompraSupervisor'] = '5000,00';			// Alçada que define o valor máximo para que um supervisor possa gerar uma OC na cotação de compra
$conf['ini']['botaoAplicarDesconto'] = 1;					// 1 ou 2 (1 é padrão) 2 é eurosul
$conf['ini']['usarOrdemCompra'] = false;					// Define se a empresa utiliza o campo Ordem de compra do pedido como Obrigatório
$conf['ini']['tabletHeaderTexto'] = 'MOBILE';				// texto utilizado no cabeçalho da interface tablet
$conf['ini']['tabletLoginLogo'] = '';						// logo na tela de login na interfacce tablet

$conf['ini']['faturamentoMultiEmpresa'] = false;			// utiliza faturamento multi-empresa (parâmetro configurado em cada empresa)
$conf['ini']['faturamentoMultiEmpresaIdMatriz'] = 1;		// ID da empresa matriz para o faturamento multi-empresa (parâmetro configurado em cada empresa)
$conf['ini']['faturamentoMultiEmpresaCFOP'] = array(array('destino' => '113', 'origem' => '114'));	// (Origem = nota emitida Destino = nota recebida) matriz de relacionamento de CFOP (entrada/saida) no faturamento multi-empresa (parâmetro configurado em cada empresa)
$conf['ini']['faturamentoMultiEmpresaPercentual'] = '0,00'; // percentual do preço de custo de cada produto a ser utilizado como preço na geração de pedido filial->matriz

$conf['ini']['retiradaInternaUtilizarLeitorBiometrico'] = false; // utiliza o leitor biometrico para a retirada de mercadoria interna

$conf['ini']['permitePedidoProdutoPreCadastro'] = true;    // Permite ou nao gerar pedido com produtos pré-cadastro
$conf['ini']['dataSaidaDefault'] = '';                   	// Valor padrão para saída da Nota


/**
* Pedidos
*/
$conf['ini']['statusInicialPedido'] = 1;					// Definição do "caminho" do pedido após faturá-lo (expedição) - o padrão é 1 (impressão)
$conf['ini']['pedidoGerarNFEmQualquerStatus'] = true;		// permite a geração da NF durante a separção

/**
* Modo de separação
*/
$conf['ini']['expedicaoDeMercadoria'] = 'normal';					// 'normal' / 'coletor'
$conf['ini']['modeloColetor'] = 'pedidoProduto'; 							// Define o padrão de busca: 'produto': não é pedido locação, somente código do produto | 'locacao': é pedido locação e então decidido pelo tipo dela se pede o cod. do produto
$conf['ini']['informarBoxNaSeparacao'] = false;						// Ao fim da separação, é necessário informar o box? (somente coletor)

/**
* Impressoras e Etiquetas
*/
$conf['ini']['etiquetasMetodoDeImpressao'] = array('windows', '');		// metodos de impressão: array('windows', '') ou array('lpr', 'nome_da_impressora')



// Define o valor padrão do IPV para cotação
$conf['ini']['T174IpvPadrao'] = '2.625';					// Valor padrão do IPV da cotação

/**
* Coletor de dados
*/
$conf['ini']['usarLoginColetor'] = true;					// true: usar o login normalmente no coletor. false: ir direto para o menu e pedir o crachá apenas para se logar.

/**
* Debug
*/
$conf['ini']['alertasSqlLentos'] = 2;						// 0 - desabilita ou x segundos

$conf['ini']['diasMedioPrazo'] = '30';                		// Define o numero de dias que uma OC (DI) deve ser considerada a médio prazo, acima disso é longo prazo. Sempre que mudar esta variável, caso o grid/filtro esteja customizado, deve-se também mudar o valor no grid/filtro. Esta variável DEVE ser no mínimo 0, NÃO pode ser false, ou string, ou vazia.
$conf['ini']['D009ObrigatorioGerarOrdemCompra'] = true;     // Na cotação, define se é obrigatório existir o D009 no produto para gerar o Ordem de compra ou não (poder gerar OC sem D009)
$conf['ini']['CRMNomeTab'] = 'D024_Nome_Fantasia';  		// Define qual campo da D024 será exibido como titulo da Tab do Cliente no CRM
$conf['ini']['SRMNomeTab'] = 'D024_Nome_Fantasia';  		// Define qual campo da D024 será exibido como titulo da Tab do Cliente no SRM

$conf['ini']['AbrirEdicaoClienteAposInsercao'] = 2;     // Define se é para abrir para edição o cliente após a inserção do mesmo (1 = abrir / 2 = não abrir)

$conf['ini']['precopadraoVendaProduto'] = 'liquido';    // bruto ou liquido (valor padrão do preço utilizado na venda)
$conf['ini']['permissaoFaturarEmergencia'] = 'N';       // S/N para empresas que utilizam o botão Faturar Emergência
$conf['ini']['multimarcas'] = 'N';						// S/N/ambas - define se a empresa utiliza uma ou mais marcas para cada produto (D001)
$conf['camposValorPadrao']['T005_Flag_Frete'] = '0';	// valor padrão do campo frete do cabeçalho do pedido
$conf['ini']['usarFretePrimeiraParcela'] = 'N';			// ADICIONA O VALOR DO FRETE NO PEDIDO/ORÇAMENTO NA PRIMEIRA PARCELA - S = SIM / N = NÃO 
$conf['ini']['permitirVendaSemEstoque'] = 'S';          // Define se é possível faturar pedido com itens sem estoque. S/N
$conf['ini']['DIUsarBotaoEntradaEstoque'] = 'S';        // Define se é para exibir o botão "Entrada estoque" na DI. S/N. DEFAULT N
$conf['ini']['diasVencPedido'] = '999';                   // Define quantos dias é necessário para mudar o status do pedido para vencido, recalculando assim o estoque líquido dos produtos.
$conf['ini']['usarICFeIPV'] = 'S';              		// Define se é para exibir os campos de ICF e IPV na tab preços dos produtos
$conf['ini']['verificarSaldoConta'] = 'N';         		// Define se é para veficiar o saldo da conta selecionada na inclusão de um título a pagar
$conf['ini']['campoNomeEmpresa'] = 'razaosocial';       // Define qual o campo utilizado para exibir o nome da empresa - fantasia/razaosocial
$conf['ini']['controleProdutoPedido'] = false;         	// Define se ao incluir um produto no pedido, irá gerar registro na tabela de controle de produtos (T207) true/false
$conf['ini']['usarSenhaRootPadrao'] = true;				// Permite o acesso root somente com a senha padrão interna
$conf['ini']['usarBotaoIncluirPedido'] = "N";			// Mostrar ou não o botão incluir pedido

$conf['ini']['permissaoFaturarSemPreco'] = 'N'; 		// Define se é possível faturar com produtos sem preço definido
$conf['ini']['usarTransportadoraPedido'] = 'S'; 		// Define se a empresa utiliza transportadora no pedido
$conf['ini']['usarControleCredito'] = 'S'; 				// Define se a empresa utiliza controle de crédito por cliente
$conf['ini']['exibirValorNFDANFE'] = 'N'; 				// Define se será exibido o valor da NF no canhoto do DANFE 

$conf['ini']['FaturarPedidoAposAnalisePedido'] = 'N'; 	// Todo pedido após passsar pela análise de pedido é faturado automaticamente.
$conf['ini']['diasMaxClientePreCadastro'] = 0; 			// 0 nunca altera os status / >0 define o prazo maximo em pré cadastro
$conf['ini']['diasMaxProdutoPreCadastro'] = 0; 			// 0 nunca altera os status / >0 define o prazo maximo em pré cadastro

// Tudo ligado com emails
$conf['ini']['formatoOrcamentoEmail'] = 'pdf';		// 'html' ou 'pdf' ou 'ambos'
$conf['ini']['formatoPedidoEmail'] = 'pdf';			// 'html' ou 'pdf' ou 'ambos'
$conf['ini']['layoutOrcamentoEmailPdf'] = 1;			// 1 ou 2 (1 é padrão) 2 é eurosul
$conf['ini']['liberarEmailImpressao'] = 'N';            // Define se a empresa permite o envio de e-mail e impressão de orçamentos sem forma de pagamento.
$conf['ini']['obrigatorioEnvioDanfeViaEmail'] = false;	// A NF só irá sair da fila (botão Fechar) se for enviada via e-mail
$conf['ini']['alertaEmailRecebido'] = 'auto'; 			// '' vazio não recebe os avisos! Para receber os aviso, coloque o tamanho da janela: array(990,550) ou 'auto' (70%/70%) ou 'full' (tela inteira)
$conf['ini']['EmailEnviadosAddContatosEmpresa'] = 'N'; 	// Todo email enviado de um orçamento/pedido/nfe para uma empresa (D024), terá os emails digitados cadastrados automanticamente em 'Contatos' (D013)
$conf['ini']['NFeEnviarEmailAutomatico'] = 'N'; 		// Se 'S', é enviado um e-mail automaticamente para o cliente ao enviar uma NF-e
$conf['ini']['NFeEnviarEmailIncluirContatosCC'] = 'N';	// Se 'S', todos os email dos contatos vão como CC
$conf['ini']['exibirNoDanfeValorTribTransp'] = 'S';		// Define se é para exibir ou não os trubutos da lei da transparencia no DANFE
$conf['ini']['CFOPPadraoOrdemServico'] = 115;			// Define a CFOP PAdrão da Ordem de serviço para nota fiscal
$conf['ini']['atualizarNossoNumeroBoleto'] = 'S';		// (S/N) - Se 'S', ao atualizar o boleto é criado um novo registro T117 e incrementado Nosso Numero, se 'N', o mesmo registro T117 é apenas atualizado

$conf['ini']['NFeEnviarEmailTransportadora'] = 'N'; 	// Se 'S', é enviado um e-mail automaticamente para a transportadora ao enviar uma NF-e
$conf['ini']['NFeEnviarEmailVendedorInterno'] = 'N'; 	// Se 'S', é enviado um e-mail automaticamente para o vendedor ao enviar uma NF-e
$conf['ini']['NFeEnviarEmailPessoaProposta'] = 'N'; 	// Se 'S', é enviado um e-mail automaticamente para a pessoa da proposta ao enviar uma NF-e
$conf['ini']['NFeEnviarEmailRepresentante'] = 'N'; 		// Se 'S', é enviado um e-mail automaticamente para o representante ao enviar uma NF-e



$conf['ini']['layoutImpEtiquetaProdutosTipoCodBarra'] 		= 'D001';
$conf['ini']['usarDescontoProdutosCompras'] = false;		// Preco unitario de compra vai usar preco tabela, desconto e preco liquido?

// Definido aqui o extra das querys que exibem os clientes.
$conf['ini']['extraSqlPadraoVisualizacaoClientes'] = <<<EOT
	and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V' AND '{$g['C031']['vendedorPodeVerTodosClientes']}' = 'N',
			Vendedor.C007_Id='{$g['usuarioAtual']}' or Externo.C007_Id='{$g['usuarioAtual']}' or Substituto.C007_Id='{$g['usuarioAtual']}' OR (SELECT T291_Id FROM T291 WHERE T291_C007_Id='{$g['usuarioAtual']}' AND T291_D021_Id=D021_Id LIMIT 1),
		IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S' AND '{$g['C031']['supervidorPodeVerTodosClientes']}' = 'N', 
			(Supervisor.C007_Id='{$g['usuarioAtual']}' or Supervisor2.C007_Id='{$g['usuarioAtual']}' or Vendedor.C007_Id='{$g['usuarioAtual']}' OR (SELECT T291_Id FROM T291 WHERE T291_C007_Id='{$g['usuarioAtual']}' AND T291_D021_Id=D021_Id LIMIT 1)) ,
		'1=1'))
EOT;
/**
* Define se os campos de alçada no form totais de orçamento e pedido irão aparecer, de acordo com a configuraçã da variável globla $g['C031']['usarAlcada'] 
* 
* ------------------------> Sempre que utilizar o metodo $g['conf']->ini(), coloque ele no final deste arquivo <------------------------
*/
if($g['C031']['usarAlcada'] == 'N'){
	$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
		'ven001AlcadaOrcamento',
		'ven001AlcadaDesconto',
		'ven001AlcadaPrazoMedio',
        'ven001AlcadaMotivoLiberacao',
		'ven002AlcadaPedido',
		'ven002AlcadaDesconto',
		'ven002AlcadaPrazoMedio',
        'ven002AlcadaMotivoLiberacao'
	));
}
/**** NÂO COLOCAR CÓDIGO DAQUIE PARA BAIXO ****/
