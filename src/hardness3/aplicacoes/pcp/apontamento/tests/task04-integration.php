<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/individual.php';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
mysqli_query($conn, 'DROP DATABASE IF EXISTS pcp_task04_validation');
try {
    if (!mysqli_query($conn, 'CREATE DATABASE pcp_task04_validation CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, 'pcp_task04_validation');
    $ddl = [
        'CREATE TABLE D109 (D109_Id int unsigned auto_increment primary key,D109_Nome varchar(150),D109_Flag_Ativo char(1)) ENGINE=InnoDB', 'CREATE TABLE T255 (T255_Id int unsigned primary key,T255_C004_Id int,T255_Flag_Status varchar(2)) ENGINE=InnoDB', 'CREATE TABLE SMT006 (SMT006_Id int unsigned primary key,SMT006_Descricao varchar(100),SMT006_Flag_Apontamento char(1)) ENGINE=InnoDB', 'CREATE TABLE SMT012 (SMT012_Id int unsigned primary key,SMT012_Descricao varchar(100)) ENGINE=InnoDB', 'CREATE TABLE PCP001 (PCP001_Id int unsigned auto_increment primary key,PCP001_T255_Id int,PCP001_SMT006_Id int,PCP001_Status char(1),PCP001_Data_Hora_Inicio datetime,PCP001_Data_Hora_Fim datetime null,PCP001_Flag_Retrabalho char(1),PCP001_PCP005_Id int null,PCP001_C007_Id int) ENGINE=InnoDB', 'CREATE TABLE PCP002 (PCP002_Id int unsigned auto_increment primary key,PCP002_PCP001_Id int,PCP002_D109_Id int,PCP002_Status char(1),PCP002_Data_Hora_Inicio datetime,PCP002_Data_Hora_Fim datetime null,KEY(PCP002_D109_Id)) ENGINE=InnoDB', 'CREATE TABLE PCP003 (PCP003_Id int unsigned auto_increment primary key,PCP003_PCP001_Id int,PCP003_PCP002_Id int null,PCP003_Tipo char(1),PCP003_Data_Hora datetime,PCP003_SMT012_Id int null,PCP003_C007_Id int) ENGINE=InnoDB', 'CREATE TABLE PCP006 (PCP006_Id int unsigned auto_increment primary key,PCP006_D109_Id int,PCP006_Matricula varchar(250),PCP006_Status char(1),UNIQUE KEY(PCP006_D109_Id),UNIQUE KEY(PCP006_Matricula)) ENGINE=InnoDB', 'CREATE TABLE PCP004 (PCP004_Id int unsigned auto_increment primary key,PCP004_PCP006_Id int,PCP004_SMT006_Id int,PCP004_Status char(1)) ENGINE=InnoDB', 'CREATE TABLE PCP005 (PCP005_Id int unsigned primary key,PCP005_Descricao varchar(100),PCP005_Status char(1)) ENGINE=InnoDB'
    ];
    foreach ($ddl as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    mysqli_multi_query($conn, "INSERT INTO D109 VALUES(1,'Ana','S'); INSERT INTO T255 VALUES(10,1,'0'); INSERT INTO SMT006 VALUES(40,'Corte','S'); INSERT INTO SMT012 VALUES(1,'Ajuste'); INSERT INTO PCP006 VALUES(1,1,'101','A'); INSERT INTO PCP004 VALUES(1,1,40,'A'); INSERT INTO PCP005 VALUES(1,'Correção','A')"); while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $context = ['company_id' => 1, 'user_id' => 9];
    if (function_exists('pcntl_fork')) {
        $children = [];
        for ($i = 0; $i < 2; $i++) {
            $pid = pcntl_fork();
            if ($pid === 0) {
                $child = mysqli_connect('127.0.0.1', 'root', 'hardness', 'pcp_task04_validation');
                try { pcp_apontamento_inicio($child, $context, '101', 10, 40, false, null); exit(0); }
                catch (DomainException $expected) { exit(1); }
                catch (Throwable $unexpected) { exit(2); }
            }
            $children[] = $pid;
        }
        $codes = [];
        foreach ($children as $pid) { pcntl_waitpid($pid, $status); $codes[] = pcntl_wexitstatus($status); }
        sort($codes);
        if ($codes !== [0, 1]) throw new RuntimeException('Concorrência não retornou exatamente um início e uma DomainException.');
        mysqli_close($conn);
        $conn = mysqli_connect('127.0.0.1', 'root', 'hardness', 'pcp_task04_validation');
    } else {
        pcp_apontamento_inicio($conn, $context, '101', 10, 40, false, null);
    }
    if (!pcp_apontamento_colaborador($conn, '101') || !pcp_apontamento_aberto($conn, 1, 1)) throw new RuntimeException('Início falhou ou a matrícula foi indevidamente filtrada pela empresa.');
    pcp_apontamento_stmt($conn, 'UPDATE PCP002 SET PCP002_Data_Hora_Inicio=DATE_SUB(NOW(), INTERVAL 100 SECOND)'); pcp_apontamento_stmt($conn, "UPDATE PCP003 SET PCP003_Data_Hora=DATE_SUB(NOW(), INTERVAL 100 SECOND) WHERE PCP003_Tipo='I'");
    pcp_apontamento_acao($conn, $context, '101', 'pause', 1); pcp_apontamento_stmt($conn, "UPDATE PCP003 SET PCP003_Data_Hora=DATE_SUB(NOW(), INTERVAL 60 SECOND) WHERE PCP003_Tipo='P'");
    $pause = pcp_apontamento_one($conn, "SELECT PCP003_SMT012_Id AS motivo FROM PCP003 WHERE PCP003_Tipo='P' LIMIT 1");
    if ((int) $pause['motivo'] !== 1) throw new RuntimeException('Evento de pausa ou motivo SMT012 ausente.');
    $paused = (int) pcp_apontamento_aberto($conn, 1, 1)['segundos']; usleep(1200000);
    if ((int) pcp_apontamento_aberto($conn, 1, 1)['segundos'] !== $paused) throw new RuntimeException('Tempo cresceu durante a pausa.');
    pcp_apontamento_acao($conn, $context, '101', 'resume', null);
    if (!pcp_apontamento_one($conn, "SELECT PCP003_Id FROM PCP003 WHERE PCP003_Tipo='R' LIMIT 1")) throw new RuntimeException('Evento de retorno ausente.');
    pcp_apontamento_acao($conn, $context, '101', 'finish', null);
    $finished = pcp_apontamento_one($conn, "SELECT PCP002.PCP002_Status AS participante, PCP002.PCP002_Data_Hora_Fim AS participante_fim, PCP001.PCP001_Status AS grupo, PCP001.PCP001_Data_Hora_Fim AS grupo_fim FROM PCP002 INNER JOIN PCP001 ON PCP001.PCP001_Id=PCP002.PCP002_PCP001_Id ORDER BY PCP002.PCP002_Id LIMIT 1");
    if ($finished['participante'] !== 'F' || !$finished['participante_fim'] || $finished['grupo'] !== 'F' || !$finished['grupo_fim'] || !pcp_apontamento_one($conn, "SELECT PCP003_Id FROM PCP003 WHERE PCP003_Tipo='F' LIMIT 1")) throw new RuntimeException('Finalização não persistiu os eventos esperados.');
    pcp_apontamento_inicio($conn, $context, '101', 10, 40, false, 1);
    $normal = pcp_apontamento_one($conn, 'SELECT PCP001_Flag_Retrabalho AS flag, PCP001_PCP005_Id AS motivo FROM PCP001 ORDER BY PCP001_Id DESC LIMIT 1');
    if ($normal['flag'] !== 'N' || $normal['motivo'] !== null) throw new RuntimeException('Motivo foi mantido sem retrabalho.');
    pcp_apontamento_acao($conn, $context, '101', 'finish', null);
    try { pcp_apontamento_inicio($conn, $context, '101', 10, 40, true, null); throw new RuntimeException('Retrabalho sem motivo foi aceito.'); } catch (DomainException $expected) { }
    pcp_apontamento_inicio($conn, $context, '101', 10, 40, true, 1);
    $rework = pcp_apontamento_one($conn, 'SELECT PCP001_Flag_Retrabalho AS flag, PCP001_PCP005_Id AS motivo FROM PCP001 ORDER BY PCP001_Id DESC LIMIT 1');
    if ($rework['flag'] !== 'S' || (int) $rework['motivo'] !== 1) throw new RuntimeException('Retrabalho ativo não gravou flag e motivo.');
    echo "task04 integration: OK\n";
} finally {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS pcp_task04_validation');
    mysqli_close($conn);
}
