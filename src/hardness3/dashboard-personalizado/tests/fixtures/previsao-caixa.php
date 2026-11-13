<?php
// Configuração fictícia para os testes; não ativa indicadores de clientes.
return array (
  'id' => 'previsao_caixa_teste',
  'habilitado' => true,
  'titulo' => 'Fluxo de caixa fictício',
  'subtitulo' => 'Configuração exclusiva dos testes locais',
  'icone' => 'wallet',
  'cor' => 'blue',
  'filtros' =>
  array (
    0 =>
    array (
      'id' => 'abrangenciaEmpresa',
      'titulo' => 'Empresas',
      'tipo' => 'selecao',
      'padrao' => 'atual',
      'obrigatorio' => true,
      'persistir' => false,
      'recarregarAoAlterar' => true,
      'opcoes' =>
      array (
        0 =>
        array (
          'valor' => 'atual',
          'titulo' => 'Empresa atual',
        ),
        1 =>
        array (
          'valor' => 'todas',
          'titulo' => 'Todas as empresas',
        ),
      ),
    ),
    1 =>
    array (
      'id' => 'dataInicio',
      'titulo' => 'Projeção inicial',
      'tipo' => 'data',
      'padrao' => 'inicio_mes',
      'obrigatorio' => true,
    ),
    2 =>
    array (
      'id' => 'dataFim',
      'titulo' => 'Projeção final',
      'tipo' => 'data',
      'padrao' => 'fim_mes',
      'obrigatorio' => true,
    ),
    3 =>
    array (
      'id' => 'statusTitulo',
      'titulo' => 'Status do título',
      'tipo' => 'selecao',
      'padrao' => 'sem_encontro',
      'opcoes' =>
      array (
        0 =>
        array (
          'valor' => 'sem_encontro',
          'titulo' => 'Todos, exceto Encontro Contas',
        ),
        1 =>
        array (
          'valor' => '',
          'titulo' => 'Todos os status',
        ),
        2 =>
        array (
          'valor' => '1',
          'titulo' => 'Normal',
        ),
        3 =>
        array (
          'valor' => '3',
          'titulo' => 'Cobrança',
        ),
        4 =>
        array (
          'valor' => '2',
          'titulo' => 'Perdido',
        ),
        5 =>
        array (
          'valor' => '4',
          'titulo' => 'Protestado',
        ),
        6 =>
        array (
          'valor' => '5',
          'titulo' => 'Judicializado',
        ),
        7 =>
        array (
          'valor' => '6',
          'titulo' => 'Encontro Contas',
        ),
        8 =>
        array (
          'valor' => '7',
          'titulo' => 'Escritório de Cobrança',
        ),
      ),
      'persistir' => false,
      'recarregarAoAlterar' => true,
    ),
    4 =>
    array (
      'id' => 'valorMinimo',
      'titulo' => 'Valor mínimo',
      'tipo' => 'numero',
      'padrao' => 0,
    ),
    5 =>
    array (
      'id' => 'emissaoPedidosInicio',
      'titulo' => 'Emissão dos pedidos desde',
      'tipo' => 'data',
      'padrao' => '2026-10-15',
      'obrigatorio' => true,
    ),
    6 =>
    array (
      'id' => 'simularPedidos',
      'titulo' => 'Simular recebimentos dos pedidos',
      'tipo' => 'selecao',
      'padrao' => 'S',
      'opcoes' =>
      array (
        0 =>
        array (
          'valor' => 'S',
          'titulo' => 'Sim',
        ),
        1 =>
        array (
          'valor' => 'N',
          'titulo' => 'Não',
        ),
      ),
    ),
  ),
  'sqlBase' =>
  array (
    0 => 'SELECT 0 AS valor FROM C004 WHERE C004_Id = :empresa AND 1 = 0',
  ),
  'card' =>
  array (
    'habilitada' => false,
    'campo' => 'valor',
    'calculo' => 'soma',
    'formato' => 'moeda',
  ),
  'grafico' =>
  array (
    'habilitado' => true,
    'tipo' => 'barras_comparativas',
    'titulo' => 'Previsão diária de entradas e saídas',
    'ajuda' => 'Fixture de projeção com dias úteis e parcelas fictícias.',
    'campoData' => 'data',
    'campoSerie' => 'serie',
    'campoValor' => 'valor',
    'campoDiasUteisAdicionais' => 'diasUteisAdicionais',
    'considerarFeriados' => true,
    'considerarFeriadosNacionais' => true,
    'limitarAoPeriodoFiltrado' => true,
    'exibirSaldoProjetado' => false,
    'detalhamento' =>
    array (
      'habilitado' => true,
      'titulo' => '{serie} em {data}',
      'rotuloSingular' => 'título encontrado',
      'rotuloPlural' => 'títulos encontrados',
      'campoValor' => 'valor',
      'formato' => 'moeda',
      'limite' => 1000,
      'colunas' =>
      array (
        0 =>
        array (
          'campo' => 'documento',
          'titulo' => 'Documento',
        ),
        1 =>
        array (
          'campo' => 'descricao',
          'titulo' => 'Pessoa',
        ),
        2 =>
        array (
          'campo' => 'situacao',
          'titulo' => 'Situação',
        ),
        3 =>
        array (
          'campo' => 'statusTituloDescricao',
          'titulo' => 'Status do título',
        ),
        4 =>
        array (
          'campo' => 'dataVencimento',
          'titulo' => 'Vencimento',
          'formato' => 'data',
        ),
        5 =>
        array (
          'campo' => 'data',
          'titulo' => 'Data-base',
          'formato' => 'data',
        ),
        6 =>
        array (
          'campo' => 'dataProjetada',
          'titulo' => 'Data projetada',
          'formato' => 'data',
        ),
        7 =>
        array (
          'campo' => 'valor',
          'titulo' => 'Valor',
          'formato' => 'moeda',
        ),
        8 =>
        array (
          'campo' => 'empresa',
          'titulo' => 'Empresa',
        ),
        9 =>
        array (
          'campo' => 'dataEmissao',
          'titulo' => 'Emissão do pedido',
          'formato' => 'data',
        ),
        10 =>
        array (
          'campo' => 'dataEntrega',
          'titulo' => 'Previsão de entrega',
          'formato' => 'data',
        ),
        11 =>
        array (
          'campo' => 'prazoDias',
          'titulo' => 'Prazo da parcela (dias)',
          'formato' => 'numero',
        ),
      ),
    ),
    'series' =>
    array (
      0 =>
      array (
        'chave' => 'receber',
        'rotulo' => 'Contas a receber',
        'cor' => '#16a477',
        'impactoSaldo' => 1,
      ),
      1 =>
      array (
        'chave' => 'pagar',
        'rotulo' => 'Contas a pagar',
        'cor' => '#e04b59',
        'impactoSaldo' => -1,
      ),
      2 =>
      array (
        'chave' => 'pedidos',
        'rotulo' => 'Previsão de recebimentos dos pedidos',
        'cor' => '#3467d6',
        'impactoSaldo' => 1,
      ),
      3 =>
      array (
        'chave' => 'previsao_pagar',
        'rotulo' => 'Previsão manual de contas a pagar',
        'cor' => '#d38a22',
        'impactoSaldo' => -1,
      ),
    ),
    'sqlSeries' =>
    array (
      0 =>
      array (
        'chave' => 'receber',
        'sqlBase' => 'SELECT \'receber\' AS serie, T002_Data_Vencimento AS data, T002_Valor_Saldo AS valor FROM T002 WHERE T002_C004_Id = :empresa',
      ),
      1 =>
      array (
        'chave' => 'pagar',
        'sqlBase' => 'SELECT \'pagar\' AS serie, T015_Data_Vencimento AS data, T015_Valor_Saldo AS valor FROM T015 WHERE T015_C004_Id = :empresa',
      ),
    ),
  ),
  'composicao' =>
  array (
    'habilitada' => false,
  ),
  'graficosPorGrupo' =>
  array (
    'habilitado' => false,
  ),
  'listagem' =>
  array (
    'habilitada' => false,
  ),
  'previsaoCaixa' =>
  array (
    'habilitada' => true,
    'empresas' =>
    array (
      0 => 1,
      1 => 2,
      2 => 4,
      3 => 5,
    ),
    'sqlPedidos' =>
    array (
      0 => 'SELECT pedido.T005_Id AS pedido, pedido.T005_C004_Id AS empresa,',
      1 => '       pedido.T005_Data_Emissao AS dataEmissao, pedido.T005_Data_Entrega AS dataEntrega,',
      2 => '       pedido.T005_Valor_Total AS valorPedido,',
      3 => '       COALESCE(NULLIF(TRIM(cliente.D024_Nome_Empresa), \'\'), \'Não informado\') AS descricao,',
      4 => '       parcela.T090_Id AS parcelaId, parcela.T090_Numero_Parcela AS numeroParcela,',
      5 => '       parcela.T090_Prazos_Dias AS prazoDias, parcela.T090_Valor_Parcela AS valorParcela,',
      6 => '       portador.D027_Id AS portadorId, IFNULL(portador.D027_Flag_Boleto, \'N\') AS boleto,',
      7 => '       EXISTS (SELECT 1 FROM T002 AS financeiro WHERE financeiro.T002_T005_Id = pedido.T005_Id',
      8 => '               AND financeiro.T002_C004_Id = pedido.T005_C004_Id',
      9 => '               AND IFNULL(financeiro.T002_Flag_Cancelada, \'N\') <> \'S\'',
      10 => '               AND IFNULL(financeiro.T002_T007_Id, 0) = 0) AS financeiroAntecipado,',
      11 => '       GREATEST(COALESCE((SELECT SUM(CASE WHEN item.T006_Quantidade > 0 THEN',
      12 => '           GREATEST(item.T006_Quantidade - COALESCE((SELECT SUM(faturado.T008_Quantidade)',
      13 => '               FROM T008 AS faturado INNER JOIN T007 AS nota ON nota.T007_Id = faturado.T008_T007_Id',
      14 => '               WHERE faturado.T008_T006_Id = item.T006_Id AND nota.T007_C004_Id = pedido.T005_C004_Id',
      15 => '               AND IFNULL(nota.T007_Flag_Cancelada, \'N\') <> \'S\'',
      16 => '               AND IFNULL(nota.T007_Flag_Inutilizado, \'N\') <> \'S\'',
      17 => '               AND IFNULL(nota.T007_Flag_Denegado, \'N\') <> \'S\'), 0), 0)',
      18 => '           / item.T006_Quantidade * GREATEST(IFNULL(item.T006_Valor_Total_Preco, 0), 0) ELSE 0 END)',
      19 => '           / NULLIF(SUM(GREATEST(IFNULL(item.T006_Valor_Total_Preco, 0), 0)), 0)',
      20 => '           FROM T006 AS item WHERE item.T006_T005_Id = pedido.T005_Id',
      21 => '           AND IFNULL(item.T006_Flag_Cancelado, \'N\') <> \'S\' AND IFNULL(item.T006_Flag_Status, \'\') <> \'3\'), 0)',
      22 => '           - COALESCE((SELECT SUM(direta.T007_Valor_Total) FROM T007 AS direta',
      23 => '               WHERE direta.T007_T005_Id = pedido.T005_Id AND direta.T007_C004_Id = pedido.T005_C004_Id',
      24 => '               AND IFNULL(direta.T007_Flag_Cancelada, \'N\') <> \'S\'',
      25 => '               AND IFNULL(direta.T007_Flag_Inutilizado, \'N\') <> \'S\'',
      26 => '               AND IFNULL(direta.T007_Flag_Denegado, \'N\') <> \'S\'',
      27 => '               AND NOT EXISTS (SELECT 1 FROM T008 AS vinculo INNER JOIN T006 AS origem ON origem.T006_Id = vinculo.T008_T006_Id',
      28 => '                   WHERE vinculo.T008_T007_Id = direta.T007_Id AND origem.T006_T005_Id = pedido.T005_Id))',
      29 => '               / NULLIF(pedido.T005_Valor_Total, 0), 0), 0) AS fatorPendente',
      30 => 'FROM T005 AS pedido',
      31 => 'INNER JOIN D006 AS operacao ON operacao.D006_Id = pedido.T005_D006_Id AND operacao.D006_Flag_Gera_Contas = \'R\'',
      32 => 'LEFT JOIN D024 AS cliente ON cliente.D024_Id = pedido.T005_D024_Id',
      33 => 'LEFT JOIN T090 AS parcela ON parcela.T090_T005_Id = pedido.T005_Id',
      34 => 'LEFT JOIN D027 AS portador ON portador.D027_Id = parcela.T090_D027_Id',
      35 => 'WHERE ((:abrangenciaEmpresa = \'atual\' AND pedido.T005_C004_Id = :empresa)',
      36 => '    OR (:abrangenciaEmpresa = \'todas\' AND pedido.T005_C004_Id IN (1, 2, 4, 5)))',
      37 => 'AND pedido.T005_Data_Emissao BETWEEN :emissaoPedidosInicio AND :dataFim',
      38 => 'AND IFNULL(pedido.T005_Flag_Status, 0) NOT IN (8, 15)',
      39 => 'AND IFNULL(pedido.T005_Flag_Devolucao, \'N\') <> \'S\'',
      40 => 'AND IFNULL(pedido.T005_Valor_Total, 0) > 0',
      41 => 'ORDER BY pedido.T005_Id, parcela.T090_Numero_Parcela, parcela.T090_Id',
      42 => 'LIMIT 5001',
    ),
  ),
);
