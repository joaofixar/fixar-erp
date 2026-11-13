<?php
// Configuração fictícia independente das personalizações dos clientes.
$fonte = array(
    'SELECT realizado.T257_Id AS id, realizado.T257_Data_Producao AS data,',
    'COALESCE(realizado.T257_Quantidade, 0) AS valor',
    'FROM T257 AS realizado',
    'INNER JOIN T256 AS item ON item.T256_Id = realizado.T257_T256_Id',
    'INNER JOIN T255 AS op ON op.T255_Id = item.T256_T255_Id',
    'LEFT JOIN T255A AS p ON p.T255A_Id = (SELECT MAX(px.T255A_Id) FROM T255A AS px WHERE px.T255A_T255_Id = op.T255_Id)',
    'WHERE op.T255_C004_Id = :empresa',
    "AND (:maquina = '' OR CAST(p.T255A_Flag_Maquina AS CHAR) = :maquina)",
    'AND realizado.T257_Data_Producao >= :dataInicio',
    'AND realizado.T257_Data_Producao <= :dataFim',
);
return array(
    'id' => 'comparativo_periodos_teste',
    'titulo' => 'Comparativo de produção — dados fictícios',
    'filtros' => array(
        array('id' => 'dataInicio', 'titulo' => 'Data inicial', 'tipo' => 'data', 'padrao' => '2026-10-01', 'obrigatorio' => true),
        array('id' => 'dataFim', 'titulo' => 'Data final', 'tipo' => 'data', 'padrao' => '2026-10-19', 'obrigatorio' => true),
        array('id' => 'maquina', 'titulo' => 'Máquina', 'tipo' => 'selecao', 'padrao' => '', 'opcoes' => array(
            array('valor' => '0', 'titulo' => 'Blank'), array('valor' => '1', 'titulo' => 'Slitter'),
        )),
    ),
    'sqlBase' => $fonte,
    'card' => array('titulo' => 'Produção apontada (kg)', 'campo' => 'valor', 'calculo' => 'soma', 'formato' => 'numero'),
    'grafico' => array(
        'habilitado' => true, 'tipo' => 'linhas_periodos',
        'titulo' => 'Comparativo da produção apontada (kg)',
        'campoData' => 'data', 'campoValor' => 'valor', 'sqlBase' => $fonte,
    ),
    'composicao' => array('habilitada' => false),
    'graficosPorGrupo' => array('habilitado' => false),
    'listagem' => array('habilitada' => false),
);
