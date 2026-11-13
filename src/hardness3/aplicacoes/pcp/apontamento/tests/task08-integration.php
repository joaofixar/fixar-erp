<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/individual.php';
require dirname(__DIR__) . '/bootstrap/consultation.php';
require dirname(__DIR__) . '/bootstrap/overview.php';

$schema = 'pcp_task08_validation';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
try {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    if (!mysqli_query($conn, 'CREATE DATABASE ' . $schema . ' CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, $schema);
    foreach ([
        'CREATE TABLE T255 (T255_Id int primary key,T255_C004_Id int)',
        'CREATE TABLE SMT006 (SMT006_Id int primary key,SMT006_Descricao varchar(100))',
        'CREATE TABLE D109 (D109_Id int primary key,D109_Nome varchar(100))',
        'CREATE TABLE PCP006 (PCP006_Id int primary key,PCP006_D109_Id int,PCP006_Matricula varchar(250))',
        'CREATE TABLE PCP005 (PCP005_Id int primary key,PCP005_Descricao varchar(100))',
        'CREATE TABLE SMT012 (SMT012_Id int primary key,SMT012_Descricao varchar(100),SMT012_Cor varchar(10))',
        'CREATE TABLE PCP001 (PCP001_Id int primary key,PCP001_T255_Id int,PCP001_SMT006_Id int,PCP001_Status char(1),PCP001_Data_Hora_Inicio datetime,PCP001_Data_Hora_Fim datetime null,PCP001_Flag_Retrabalho char(1),PCP001_PCP005_Id int null)',
        'CREATE TABLE PCP002 (PCP002_Id int primary key,PCP002_PCP001_Id int,PCP002_D109_Id int,PCP002_Status char(1),PCP002_Data_Hora_Inicio datetime,PCP002_Data_Hora_Fim datetime null)',
        'CREATE TABLE PCP003 (PCP003_Id int primary key,PCP003_PCP001_Id int,PCP003_PCP002_Id int null,PCP003_Tipo char(1),PCP003_Data_Hora datetime,PCP003_SMT012_Id int null)',
    ] as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    $catalogos = "INSERT INTO T255 VALUES(10,1),(20,2); INSERT INTO SMT006 VALUES(40,'Corte'); INSERT INTO D109 VALUES(1,'Ana'),(2,'Beto'); INSERT INTO PCP006 VALUES(1,1,'101'),(2,2,'102'); INSERT INTO PCP005 VALUES(1,'Correção'); INSERT INTO SMT012 VALUES(1,'Ferramenta','#A23D3D')";
    if (!mysqli_multi_query($conn, $catalogos)) throw new RuntimeException(mysqli_error($conn));
    while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $today = date('Y-m-d'); $yesterday = date('Y-m-d', strtotime('-1 day'));
    $grupos = [];
    for ($id = 1; $id <= 12; ++$id) {
        $inicio = $id === 12 ? "$yesterday 01:00:00" : sprintf('%s 01:%02d:00', $today, $id);
        $grupos[] = "($id,10,40,'A','$inicio',NULL,'" . ($id === 1 ? 'S' : 'N') . "'," . ($id === 1 ? '1' : 'NULL') . ')';
    }
    $grupos[] = "(20,20,40,'A','$today 09:00:00',NULL,'N',NULL)";
    if (!mysqli_query($conn, 'INSERT INTO PCP001 VALUES ' . implode(',', $grupos))) throw new RuntimeException(mysqli_error($conn));
    $participantes = []; $eventos = []; $eventoId = 1;
    for ($id = 1; $id <= 12; ++$id) {
        $participantes[] = "($id,$id," . ($id % 2 ? 1 : 2) . ",'" . ($id === 2 ? 'P' : 'A') . "','$today 01:00:00',NULL)";
        $tipo = $id === 2 ? 'P' : 'I';
        $eventos[] = "($eventoId,$id,$id,'$tipo','$today 01:15:00'," . ($tipo === 'P' ? '1' : 'NULL') . ')'; ++$eventoId;
    }
    // Registro de outra empresa não pode influenciar nenhum card nem lista.
    $participantes[] = "(20,20,1,'A','$today 01:00:00',NULL)";
    $eventos[] = "($eventoId,20,20,'P','$today 01:15:00',1)";
    if (!mysqli_query($conn, 'INSERT INTO PCP002 VALUES ' . implode(',', $participantes))) throw new RuntimeException(mysqli_error($conn));
    if (!mysqli_query($conn, 'INSERT INTO PCP003 VALUES ' . implode(',', $eventos))) throw new RuntimeException(mysqli_error($conn));

    $resumo = pcp_apontamento_visao_geral_resumo($conn, 1);
    if ($resumo !== ['grupos_abertos'=>12, 'participantes_atividade'=>11, 'participantes_pausados'=>1, 'iniciados_hoje'=>11]) throw new RuntimeException('Resumo, eventos aplicáveis, data atual ou isolamento por empresa falhou: ' . json_encode($resumo));
    $lista = pcp_apontamento_visao_geral_grupos_abertos($conn, 1);
    if (count($lista) !== 10 || (int) $lista[0]['id'] !== 11 || (int) $lista[0]['pausados'] !== 0 || $lista[0]['modalidade'] !== 'Individual' || (int) $lista[9]['id'] !== 2 || (int) $lista[9]['pausados'] !== 1 || $lista[9]['em_execucao'] || ($lista[9]['motivo_pausa']['cor'] ?? '') !== '#A23D3D') throw new RuntimeException('Lista de grupos ativos não respeitou ordenação, modalidade, pausa, cor ou limite de 10.');
    if ((int) $lista[0]['tempo_liquido'] <= 0) throw new RuntimeException('A visão geral não reutilizou o cálculo de tempo líquido do grupo.');
    $semGestao = pcp_apontamento_visao_geral_atalhos(false); $comGestao = pcp_apontamento_visao_geral_atalhos(true);
    if (count($semGestao) !== 1 || count($comGestao) !== 3 || $comGestao[1]['pagina'] !== 'gerenciar' || $comGestao[2]['pagina'] !== 'configuracoes') throw new RuntimeException('Visibilidade dos atalhos administrativos por permissão falhou.');
    echo "task08 integration: OK\n";
} finally { mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema); mysqli_close($conn); }
