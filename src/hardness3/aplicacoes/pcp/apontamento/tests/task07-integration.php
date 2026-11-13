<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/individual.php';
require dirname(__DIR__) . '/bootstrap/consultation.php';

$schema = 'pcp_task07_validation';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
try {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    if (!mysqli_query($conn, 'CREATE DATABASE ' . $schema . ' CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, $schema);
    foreach ([
        'CREATE TABLE T255 (T255_Id int primary key,T255_C004_Id int)', 'CREATE TABLE SMT006 (SMT006_Id int primary key,SMT006_Descricao varchar(100))', 'CREATE TABLE D109 (D109_Id int primary key,D109_Nome varchar(100))', 'CREATE TABLE PCP006 (PCP006_Id int primary key,PCP006_D109_Id int,PCP006_Matricula varchar(250))', 'CREATE TABLE PCP005 (PCP005_Id int primary key,PCP005_Descricao varchar(100))', 'CREATE TABLE SMT012 (SMT012_Id int primary key,SMT012_Descricao varchar(100))',
        'CREATE TABLE PCP001 (PCP001_Id int primary key,PCP001_T255_Id int,PCP001_SMT006_Id int,PCP001_Status char(1),PCP001_Data_Hora_Inicio datetime,PCP001_Data_Hora_Fim datetime null,PCP001_Flag_Retrabalho char(1),PCP001_PCP005_Id int null)',
        'CREATE TABLE PCP002 (PCP002_Id int primary key,PCP002_PCP001_Id int,PCP002_D109_Id int,PCP002_Status char(1),PCP002_Data_Hora_Inicio datetime,PCP002_Data_Hora_Fim datetime null)', 'CREATE TABLE PCP003 (PCP003_Id int primary key,PCP003_PCP001_Id int,PCP003_PCP002_Id int null,PCP003_Tipo char(1),PCP003_Data_Hora datetime,PCP003_SMT012_Id int null)'
    ] as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    $dia = date('Y-m-d');
    $sql = "INSERT INTO T255 VALUES(10,1),(20,2); INSERT INTO SMT006 VALUES(40,'Corte'),(41,'Dobra'); INSERT INTO D109 VALUES(1,'Ana'),(2,'Beto'),(3,'Outra empresa'); INSERT INTO PCP006 VALUES(1,1,'101'),(2,2,'102'),(3,3,'103'); INSERT INTO PCP005 VALUES(1,'Correção'); INSERT INTO SMT012 VALUES(1,'Ferramenta')"
        . "; INSERT INTO PCP001 VALUES(1,10,40,'F','$dia 10:00:00','$dia 11:00:00','S',1),(2,10,41,'A','$dia 12:00:00',NULL,'N',NULL),(3,20,40,'F','$dia 10:00:00','$dia 11:00:00','N',NULL)"
        . "; INSERT INTO PCP002 VALUES(1,1,1,'F','$dia 10:00:00','$dia 11:00:00'),(2,1,2,'F','$dia 10:00:00','$dia 11:00:00'),(3,2,1,'P','$dia 12:00:00',NULL),(4,3,3,'F','$dia 10:00:00','$dia 11:00:00')"
        . "; INSERT INTO PCP003 VALUES(1,1,1,'I','$dia 10:00:00',NULL),(2,1,1,'P','$dia 10:20:00',1),(3,1,1,'R','$dia 10:30:00',NULL),(4,1,1,'F','$dia 11:00:00',NULL),(5,1,2,'I','$dia 10:00:00',NULL),(6,1,2,'F','$dia 11:00:00',NULL),(7,2,3,'I','$dia 12:00:00',NULL),(8,2,3,'P','$dia 12:10:00',1),(9,3,4,'I','$dia 10:00:00',NULL),(10,3,4,'F','$dia 11:00:00',NULL)";
    if (!mysqli_multi_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn)); while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $f = pcp_apontamento_consulta_filtros(['inicio_de'=>$dia, 'inicio_ate'=>$dia, 'status'=>'finalizado', 'retrabalho'=>'sim']);
    $grupos = pcp_apontamento_consulta_grupos($conn, 1, $f);
    if (count($grupos) !== 1 || (int) $grupos[0]['id'] !== 1) throw new RuntimeException('Isolamento por empresa, status ou retrabalho falhou.');
    $f = pcp_apontamento_consulta_filtros(['inicio_de'=>$dia, 'inicio_ate'=>$dia, 'processo_id'=>41, 'colaborador_id'=>1, 'status'=>'pausado']);
    $grupos = pcp_apontamento_consulta_grupos($conn, 1, $f);
    if (count($grupos) !== 1 || $grupos[0]['status_operacional'] !== 'P') throw new RuntimeException('Filtros básicos ou status pausado falhou.');
    $f = pcp_apontamento_consulta_filtros(['inicio_de'=>$dia, 'inicio_ate'=>$dia, 'matricula'=>'102', 'status'=>'todos']);
    if (count(pcp_apontamento_consulta_grupos($conn, 1, $f)) !== 1) throw new RuntimeException('Filtro por matrícula falhou.');
    $detalhe = pcp_apontamento_consulta_detalhe($conn, 1, 1);
    if (!$detalhe || count($detalhe['participantes']) !== 2 || $detalhe['participantes'][0]['matricula'] !== '101' || count($detalhe['eventos']) !== 6) throw new RuntimeException('Detalhe não trouxe participantes, matrículas e eventos.');
    if ((int) $detalhe['participantes'][0]['tempo_liquido'] !== 3000 || (int) $detalhe['grupo']['tempo_liquido'] !== 3600) throw new RuntimeException('Tempo líquido não descontou P→R corretamente.');
    if (pcp_apontamento_consulta_detalhe($conn, 1, 3) !== null) throw new RuntimeException('Detalhe permitiu cruzar empresa ativa.');
    $pausado = pcp_apontamento_tempo_liquido("$dia 12:00:00", null, 'P', [['tipo'=>'P','data_hora'=>"$dia 12:10:00"]], strtotime("$dia 13:00:00"));
    if ($pausado !== 600) throw new RuntimeException('Tempo cresceu durante pausa.');
    echo "task07 integration: OK\n";
} finally { mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema); mysqli_close($conn); }
