<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/individual.php';

$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
mysqli_query($conn, 'DROP DATABASE IF EXISTS pcp_task12_op_summary');
try {
    if (!mysqli_query($conn, 'CREATE DATABASE pcp_task12_op_summary CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, 'pcp_task12_op_summary');
    $ddl = [
        'CREATE TABLE T255 (T255_Id int primary key,T255_C004_Id int,T255_Flag_Status varchar(2),T255_Quantidade decimal(14,3))',
        'CREATE TABLE T256 (T256_Id int primary key,T256_T255_Id int,T256_D009_Id int,T256_T006_Id int)',
        'CREATE TABLE D009 (D009_Id int primary key,D009_D049_Id int)',
        'CREATE TABLE D049 (D049_Id int primary key,D049_D001_Id int)',
        'CREATE TABLE D001 (D001_Id int primary key,D001_Codigo_Produto varchar(50),D001_Descricao_Produto varchar(150))',
        'CREATE TABLE T006 (T006_Id int primary key,T006_T005_Id int)',
        'CREATE TABLE T005 (T005_Id int primary key,T005_D024_Id int)',
        'CREATE TABLE D024 (D024_Id int primary key,D024_Nome_Empresa varchar(150))',
        'CREATE TABLE T255A (T255A_Id int primary key,T255A_T255_Id int,T255A_D024_Id int)',
    ];
    foreach ($ddl as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    mysqli_multi_query($conn, "INSERT INTO T255 VALUES(359,1,'0',12.500),(360,2,'0',9.000),(361,1,'99',1.000),(362,1,'0',3.000); INSERT INTO D001 VALUES(1,'0264.0042','CUBÍCULO DE ALTA TENSÃO'),(2,'OUTRO','OUTRO PRODUTO'); INSERT INTO D049 VALUES(1,1),(2,2); INSERT INTO D009 VALUES(1,1),(2,2); INSERT INTO D024 VALUES(1,'HAVAN LOJAS DE DEPARTAMENTOS LTDA'),(2,'CLIENTE DIRETO DA OP'); INSERT INTO T005 VALUES(1,1); INSERT INTO T006 VALUES(1,1); INSERT INTO T256 VALUES(1,359,1,1),(2,359,2,NULL),(3,360,1,1),(4,362,1,NULL); INSERT INTO T255A VALUES(1,359,2),(2,362,2)");
    while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }

    $resumo = pcp_apontamento_resumo_op($conn, 1, 359);
    if (!$resumo || (int) $resumo['id'] !== 359 || (float) $resumo['quantidade_prevista'] !== 12.5 || $resumo['produto'] !== '0264.0042 - CUBÍCULO DE ALTA TENSÃO' || (int) $resumo['total_itens'] !== 2 || $resumo['cliente'] !== 'HAVAN LOJAS DE DEPARTAMENTOS LTDA') throw new RuntimeException('Resumo da OP não retornou produto, quantidade, cliente e total de itens esperados.');
    $semPedido = pcp_apontamento_resumo_op($conn, 1, 362);
    if (!$semPedido || $semPedido['cliente'] !== 'CLIENTE DIRETO DA OP') throw new RuntimeException('Cliente direto da OP não foi usado na ausência de pedido.');
    if (pcp_apontamento_resumo_op($conn, 2, 359) || pcp_apontamento_resumo_op($conn, 1, 361)) throw new RuntimeException('Resumo expôs OP de outra empresa ou finalizada.');
    echo "task12 OP summary: OK\n";
} finally {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS pcp_task12_op_summary');
    mysqli_close($conn);
}
