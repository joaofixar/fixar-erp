<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/individual.php';
require dirname(__DIR__) . '/bootstrap/management.php';

$schema = 'pcp_task11_validation';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
try {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    mysqli_query($conn, 'CREATE DATABASE ' . $schema . ' CHARACTER SET latin1');
    mysqli_select_db($conn, $schema);
    foreach ([
        'CREATE TABLE D109 (D109_Id int primary key,D109_Nome varchar(100),D109_Flag_Ativo char(1))',
        'CREATE TABLE T255 (T255_Id int primary key,T255_C004_Id int,T255_Flag_Status char(2))',
        "CREATE TABLE SMT006 (SMT006_Id int primary key,SMT006_Descricao varchar(100),SMT006_Flag_Apontamento char(1))",
        'CREATE TABLE PCP006 (PCP006_Id int primary key,PCP006_D109_Id int,PCP006_Matricula varchar(250),PCP006_Status char(1))',
        'CREATE TABLE PCP004 (PCP004_Id int primary key,PCP004_PCP006_Id int,PCP004_SMT006_Id int,PCP004_Status char(1))',
        'CREATE TABLE PCP005 (PCP005_Id int primary key,PCP005_Descricao varchar(100),PCP005_Status char(1))',
        'CREATE TABLE PCP001 (PCP001_Id int auto_increment primary key,PCP001_T255_Id int,PCP001_SMT006_Id int,PCP001_Status char(1),PCP001_Data_Hora_Inicio datetime,PCP001_Data_Hora_Fim datetime null,PCP001_Flag_Retrabalho char(1),PCP001_PCP005_Id int null,PCP001_C007_Id int)',
        'CREATE TABLE PCP002 (PCP002_Id int auto_increment primary key,PCP002_PCP001_Id int,PCP002_D109_Id int,PCP002_Status char(1),PCP002_Data_Hora_Inicio datetime,PCP002_Data_Hora_Fim datetime null)',
        'CREATE TABLE PCP003 (PCP003_Id int auto_increment primary key,PCP003_PCP001_Id int,PCP003_PCP002_Id int,PCP003_Tipo char(1),PCP003_Data_Hora datetime,PCP003_SMT012_Id int null,PCP003_C007_Id int)',
        'CREATE TABLE SMT012 (SMT012_Id int primary key,SMT012_Descricao varchar(100))',
    ] as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    mysqli_multi_query($conn, "INSERT INTO D109 VALUES(1,'Ana','S'),(2,'Beto','S'); INSERT INTO T255 VALUES(10,1,'0'); INSERT INTO SMT006 VALUES(40,'Corte','S'),(41,'Dobra','N'); INSERT INTO PCP006 VALUES(1,1,'101','A'),(2,2,'102','A'); INSERT INTO PCP004 VALUES(1,1,40,'A'),(2,1,41,'A'),(3,2,40,'A'),(4,2,41,'A')"); while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $options = pcp_apontamento_opcoes_inicio($conn, 1, 1);
    if (count($options['processos']) !== 1 || (int) $options['processos'][0]['smt006_id'] !== 40) throw new RuntimeException('Processo não liberado apareceu no terminal.');
    $_POST = ['pcp006_id' => 1, 'smt006_id' => 41];
    try { pcp_apontamento_management_save($conn, 'link_process'); throw new RuntimeException('Vínculo com processo não liberado foi aceito.'); } catch (DomainException $expected) { }
    $context = ['company_id' => 1, 'user_id' => 9];
    pcp_apontamento_inicio($conn, $context, '101', 10, 40, false, null);
    mysqli_query($conn, "UPDATE SMT006 SET SMT006_Flag_Apontamento='N' WHERE SMT006_Id=40");
    try { pcp_apontamento_inicio($conn, $context, '102', 10, 40, false, null); throw new RuntimeException('Backend aceitou participante em processo desabilitado.'); } catch (DomainException $expected) { }
    if (!pcp_apontamento_one($conn, "SELECT PCP004_Id FROM PCP004 WHERE PCP004_PCP006_Id=1 AND PCP004_SMT006_Id=40 AND PCP004_Status='A'")) throw new RuntimeException('Vínculo existente foi alterado ao desabilitar o processo.');
    echo "task11 integration: OK\n";
} finally { mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema); mysqli_close($conn); }
