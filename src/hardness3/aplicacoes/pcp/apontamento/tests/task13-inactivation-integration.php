<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/individual.php';
require dirname(__DIR__) . '/bootstrap/management.php';

$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
mysqli_query($conn, 'DROP DATABASE IF EXISTS pcp_task13_inactivation');
try {
    if (!mysqli_query($conn, 'CREATE DATABASE pcp_task13_inactivation CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, 'pcp_task13_inactivation');
    $ddl = [
        'CREATE TABLE T255 (T255_Id int primary key,T255_C004_Id int)',
        'CREATE TABLE PCP001 (PCP001_Id int primary key,PCP001_T255_Id int,PCP001_Status char(1),PCP001_Data_Hora_Inicio datetime,PCP001_Data_Hora_Fim datetime null,PCP001_SMT006_Id int,PCP001_Data_Hora_Inativacao datetime null,PCP001_C007_Id_Inativacao int null,PCP001_Motivo_Inativacao varchar(500) null)',
        'CREATE TABLE PCP002 (PCP002_Id int primary key,PCP002_PCP001_Id int,PCP002_D109_Id int,PCP002_Status char(1),PCP002_Data_Hora_Inicio datetime,PCP002_Data_Hora_Fim datetime null,PCP002_Data_Hora_Inativacao datetime null,PCP002_C007_Id_Inativacao int null,PCP002_Motivo_Inativacao varchar(500) null)',
        'CREATE TABLE PCP007 (PCP007_Id int auto_increment primary key,PCP007_PCP001_Id int,PCP007_PCP002_Id int null,PCP007_PCP003_Id int null,PCP007_Acao varchar(50),PCP007_Justificativa varchar(500),PCP007_Dados_Anteriores longtext,PCP007_Dados_Novos longtext,PCP007_Data_Hora datetime,PCP007_C007_Id int)',
    ];
    foreach ($ddl as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    mysqli_multi_query($conn, "INSERT INTO T255 VALUES(10,1),(20,2); INSERT INTO PCP001 (PCP001_Id,PCP001_T255_Id,PCP001_Status,PCP001_Data_Hora_Inicio,PCP001_SMT006_Id) VALUES(1,10,'A',NOW(),40),(2,10,'A',NOW(),40),(3,20,'A',NOW(),40); INSERT INTO PCP002 (PCP002_Id,PCP002_PCP001_Id,PCP002_D109_Id,PCP002_Status,PCP002_Data_Hora_Inicio) VALUES(11,1,101,'A',NOW()),(12,1,102,'P',NOW()),(21,2,103,'A',NOW())");
    while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $context = ['company_id'=>1, 'user_id'=>9];

    pcp_apontamento_inativar_participante($conn, $context, 1, 11, 'Cadastro duplicado');
    $participante = pcp_apontamento_one($conn, 'SELECT PCP002_Status AS status,PCP002_C007_Id_Inativacao AS usuario,PCP002_Motivo_Inativacao AS motivo FROM PCP002 WHERE PCP002_Id=11');
    $grupo = pcp_apontamento_one($conn, 'SELECT PCP001_Status AS status FROM PCP001 WHERE PCP001_Id=1');
    if ($participante['status'] !== 'I' || (int) $participante['usuario'] !== 9 || $participante['motivo'] !== 'Cadastro duplicado' || $grupo['status'] !== 'A') throw new RuntimeException('Inativação individual alterou dados incorretos ou inativou o apontamento.');

    pcp_apontamento_inativar_apontamento($conn, $context, 1, 'Apontamento lançado em duplicidade');
    $grupo = pcp_apontamento_one($conn, 'SELECT PCP001_Status AS status,PCP001_C007_Id_Inativacao AS usuario,PCP001_Motivo_Inativacao AS motivo FROM PCP001 WHERE PCP001_Id=1');
    $ativos = pcp_apontamento_one($conn, "SELECT COUNT(*) AS total FROM PCP002 WHERE PCP002_PCP001_Id=1 AND PCP002_Status<>'I'");
    $auditoria = pcp_apontamento_all($conn, 'SELECT PCP007_Acao AS acao,PCP007_Justificativa AS justificativa FROM PCP007 ORDER BY PCP007_Id');
    if ($grupo['status'] !== 'I' || (int) $grupo['usuario'] !== 9 || $grupo['motivo'] !== 'Apontamento lançado em duplicidade' || (int) $ativos['total'] !== 0 || count($auditoria) !== 2 || $auditoria[0]['acao'] !== 'inativar_participante' || $auditoria[1]['acao'] !== 'inativar_apontamento') throw new RuntimeException('Inativação do apontamento ou auditoria falhou.');
    try { pcp_apontamento_inativar_participante($conn, $context, 3, 21, 'Empresa errada'); throw new RuntimeException('Participante de outra empresa foi alterado.'); } catch (DomainException $expected) { }
    echo "task13 inactivation integration: OK\n";
} finally {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS pcp_task13_inactivation');
    mysqli_close($conn);
}
