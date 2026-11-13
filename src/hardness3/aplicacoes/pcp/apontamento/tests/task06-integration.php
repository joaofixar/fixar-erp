<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/individual.php';
require dirname(__DIR__) . '/bootstrap/management.php';

$schema = 'pcp_task06_validation';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
try {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    if (!mysqli_query($conn, 'CREATE DATABASE ' . $schema . ' CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, $schema);
    foreach ([
        'CREATE TABLE C029 (C029_Id int primary key,C029_Descricao_Acesso varchar(150))',
        'CREATE TABLE C030 (C030_Id int auto_increment primary key,C030_C021_Id int,C030_C029_Id int,C030_Acesso_Liberado char(1))',
        'CREATE TABLE D109 (D109_Id int primary key,D109_Nome varchar(150),D109_Flag_Ativo char(1))',
        'CREATE TABLE SMT006 (SMT006_Id int auto_increment primary key,SMT006_Descricao varchar(100),SMT006_Valor decimal(10,2) null,SMT006_Flag_Pintura char(1),SMT006_Flag_Apontamento char(1) default \'N\',SMT006_SMT201_Id int null)',
        'CREATE TABLE SMT012 (SMT012_Id int auto_increment primary key,SMT012_Descricao varchar(100),SMT012_Cor varchar(10),SMT012_Obriga_Apontar_Quantidade char(1))',
        'CREATE TABLE PCP006 (PCP006_Id int auto_increment primary key,PCP006_D109_Id int,PCP006_Matricula varchar(250),PCP006_Status char(1),UNIQUE KEY link (PCP006_D109_Id),UNIQUE KEY matricula (PCP006_Matricula))',
        'CREATE TABLE PCP004 (PCP004_Id int auto_increment primary key,PCP004_PCP006_Id int,PCP004_SMT006_Id int,PCP004_Status char(1),UNIQUE KEY link (PCP004_PCP006_Id,PCP004_SMT006_Id))',
        'CREATE TABLE PCP005 (PCP005_Id int auto_increment primary key,PCP005_Descricao varchar(100),PCP005_Status char(1))',
    ] as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    mysqli_multi_query($conn, "INSERT INTO C029 VALUES(120,'P.C.P.'),(308,'Acessar Apontamento PCP'); INSERT INTO C030 (C030_C021_Id,C030_C029_Id,C030_Acesso_Liberado) VALUES(1,308,'S'),(2,308,'S'),(2,120,'S'); INSERT INTO D109 VALUES(7,'Ana','S'),(8,'Beto','I'),(9,'Caio','S'); INSERT INTO SMT006 (SMT006_Descricao,SMT006_Flag_Pintura,SMT006_Flag_Apontamento) VALUES('Corte','N','S')"); while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    if (!pcp_apontamento_profile_permission($conn, 1, 308) || pcp_apontamento_profile_permission($conn, 1, 120) || !pcp_apontamento_profile_permission($conn, 2, 120)) throw new RuntimeException('Autorização operacional/gerencial não separada corretamente.');
    $_POST = ['d109_id'=>7, 'matricula'=>'007']; pcp_apontamento_management_save($conn, 'create_employee');
    try { $_POST = ['d109_id'=>7, 'matricula'=>'008']; pcp_apontamento_management_save($conn, 'create_employee'); throw new RuntimeException('D109 duplicado foi aceito em PCP006.'); } catch (DomainException $expected) { }
    try { $_POST = ['d109_id'=>9, 'matricula'=>'007']; pcp_apontamento_management_save($conn, 'create_employee'); throw new RuntimeException('Matrícula PCP duplicada foi aceita.'); } catch (DomainException $expected) { }
    $_POST = ['pcp006_id'=>1, 'matricula'=>'010']; pcp_apontamento_management_save($conn, 'update_employee');
    if (!pcp_apontamento_one($conn, "SELECT PCP006_Id FROM PCP006 WHERE PCP006_Id=1 AND PCP006_Matricula='010'")) throw new RuntimeException('Matrícula PCP não foi alterada.');
    mysqli_query($conn, "INSERT INTO PCP006 (PCP006_D109_Id,PCP006_Matricula,PCP006_Status) VALUES (9,'009','A')");
    try { $_POST = ['pcp006_id'=>1, 'matricula'=>'009']; pcp_apontamento_management_save($conn, 'update_employee'); throw new RuntimeException('Alteração para matrícula PCP duplicada foi aceita.'); } catch (DomainException $expected) { }
    $_POST = ['pcp006_id'=>1, 'smt006_id'=>1]; pcp_apontamento_management_save($conn, 'link_process');
    if (!pcp_apontamento_one($conn, "SELECT PCP004.PCP004_Id FROM PCP006 INNER JOIN PCP004 ON PCP004.PCP004_PCP006_Id=PCP006.PCP006_Id WHERE PCP006.PCP006_D109_Id=7 AND PCP006.PCP006_Status='A' AND PCP004.PCP004_SMT006_Id=1 AND PCP004.PCP004_Status='A'")) throw new RuntimeException('Gestor não vinculou PCP006/PCP004.');
    $_POST = ['pcp004_id'=>1, 'status'=>'I']; pcp_apontamento_management_save($conn, 'toggle_process');
    if (!pcp_apontamento_one($conn, "SELECT PCP004_Id FROM PCP004 WHERE PCP004_Status='I'")) throw new RuntimeException('Desvínculo removeu ou não inativou PCP004.');
    $_POST = ['pcp006_id'=>1, 'status'=>'I']; pcp_apontamento_management_save($conn, 'toggle_employee');
    if (!pcp_apontamento_one($conn, "SELECT PCP006_Id FROM PCP006 WHERE PCP006_Id=1 AND PCP006_Status='I'")) throw new RuntimeException('Colaborador PCP não foi inativado.');
    $_POST = ['pcp006_id'=>1, 'status'=>'A']; pcp_apontamento_management_save($conn, 'toggle_employee');
    if (!pcp_apontamento_one($conn, "SELECT PCP006_Id FROM PCP006 WHERE PCP006_Id=1 AND PCP006_Status='A'")) throw new RuntimeException('Colaborador PCP ativo não foi reativado.');
    mysqli_query($conn, "UPDATE D109 SET D109_Flag_Ativo='I' WHERE D109_Id=7");
    try { $_POST = ['pcp006_id'=>1, 'status'=>'A']; pcp_apontamento_management_save($conn, 'toggle_employee'); throw new RuntimeException('Funcionário mestre inativo foi ativado no PCP.'); } catch (DomainException $expected) { }
    $_POST = ['id'=>1, 'descricao'=>'Corte revisado', 'valor'=>'12,50', 'pintura'=>'S', 'etapa_id'=>'3']; pcp_apontamento_management_save($conn, 'save_process');
    if (!pcp_apontamento_one($conn, "SELECT SMT006_Id FROM SMT006 WHERE SMT006_Id=1 AND SMT006_Descricao='Corte revisado' AND SMT006_Flag_Pintura='S' AND SMT006_SMT201_Id=3")) throw new RuntimeException('Processo não foi alterado com os campos do catálogo.');
    $_POST = ['descricao'=>'Troca de ferramenta', 'cor'=>'#1A2B3C', 'obriga_quantidade'=>'S']; pcp_apontamento_management_save($conn, 'save_pause');
    if (!pcp_apontamento_one($conn, "SELECT SMT012_Id FROM SMT012 WHERE SMT012_Descricao='Troca de ferramenta' AND SMT012_Cor='#1A2B3C' AND SMT012_Obriga_Apontar_Quantidade='S'")) throw new RuntimeException('Motivo de pausa não foi incluído com os campos do catálogo.');
    $_POST = ['id'=>1, 'descricao'=>'Troca de ferramental', 'cor'=>'', 'obriga_quantidade'=>'N']; pcp_apontamento_management_save($conn, 'save_pause');
    if (!pcp_apontamento_one($conn, "SELECT SMT012_Id FROM SMT012 WHERE SMT012_Id=1 AND SMT012_Descricao='Troca de ferramental' AND SMT012_Cor IS NULL AND SMT012_Obriga_Apontar_Quantidade='N'")) throw new RuntimeException('Motivo de pausa não foi alterado com os campos do catálogo.');
    echo "task06 integration: OK\n";
} finally { mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema); mysqli_close($conn); }
