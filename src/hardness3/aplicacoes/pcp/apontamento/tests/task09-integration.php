<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/individual.php';

$schema = 'pcp_task09_validation';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
try {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    if (!mysqli_query($conn, 'CREATE DATABASE ' . $schema . ' CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, $schema);
    foreach ([
        'CREATE TABLE D109 (D109_Id int primary key,D109_Nome varchar(100),D109_Flag_Ativo char(1)) ENGINE=InnoDB',
        'CREATE TABLE T255 (T255_Id int primary key,T255_C004_Id int,T255_Flag_Status varchar(2)) ENGINE=InnoDB',
        'CREATE TABLE SMT006 (SMT006_Id int primary key,SMT006_Descricao varchar(100),SMT006_Flag_Apontamento char(1)) ENGINE=InnoDB',
        'CREATE TABLE SMT012 (SMT012_Id int primary key,SMT012_Descricao varchar(100)) ENGINE=InnoDB',
        'CREATE TABLE PCP005 (PCP005_Id int primary key,PCP005_Descricao varchar(100),PCP005_Status char(1)) ENGINE=InnoDB',
        'CREATE TABLE PCP006 (PCP006_Id int primary key,PCP006_D109_Id int,PCP006_Matricula varchar(250),PCP006_Status char(1), UNIQUE KEY (PCP006_Matricula)) ENGINE=InnoDB',
        'CREATE TABLE PCP004 (PCP004_Id int primary key,PCP004_PCP006_Id int,PCP004_SMT006_Id int,PCP004_Status char(1)) ENGINE=InnoDB',
        'CREATE TABLE PCP001 (PCP001_Id int auto_increment primary key,PCP001_T255_Id int,PCP001_SMT006_Id int,PCP001_Status char(1),PCP001_Data_Hora_Inicio datetime,PCP001_Data_Hora_Fim datetime null,PCP001_Flag_Retrabalho char(1),PCP001_PCP005_Id int null,PCP001_C007_Id int) ENGINE=InnoDB',
        'CREATE TABLE PCP002 (PCP002_Id int auto_increment primary key,PCP002_PCP001_Id int,PCP002_D109_Id int,PCP002_Status char(1),PCP002_Data_Hora_Inicio datetime,PCP002_Data_Hora_Fim datetime null, KEY (PCP002_D109_Id), KEY (PCP002_PCP001_Id)) ENGINE=InnoDB',
        'CREATE TABLE PCP003 (PCP003_Id int auto_increment primary key,PCP003_PCP001_Id int,PCP003_PCP002_Id int,PCP003_Tipo char(1),PCP003_Data_Hora datetime,PCP003_SMT012_Id int null,PCP003_C007_Id int) ENGINE=InnoDB',
    ] as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    mysqli_multi_query($conn, "INSERT INTO D109 VALUES(1,'Ana','S'),(2,'Beto','S'),(3,'Caio','S'),(4,'Outra Empresa','S'); INSERT INTO T255 VALUES(10,1,'0'),(20,1,'0'),(30,2,'0'); INSERT INTO SMT006 VALUES(40,'Corte','S'); INSERT INTO SMT012 VALUES(1,'Ajuste'); INSERT INTO PCP005 VALUES(1,'Correção','A'),(2,'Qualidade','A'); INSERT INTO PCP006 VALUES(1,1,'101','A'),(2,2,'102','A'),(3,3,'103','A'),(4,4,'201','A'); INSERT INTO PCP004 VALUES(1,1,40,'A'),(2,2,40,'A'),(3,3,40,'A'),(4,4,40,'A')");
    while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $ctx = ['company_id'=>1, 'user_id'=>9];

    $primeiro = pcp_apontamento_inicio($conn, $ctx, '101', 10, 40, false, null);
    $segundo = pcp_apontamento_inicio($conn, $ctx, '102', 10, 40, false, null);
    if ($primeiro['entrou_em_existente'] || !$segundo['entrou_em_existente'] || $primeiro['pcp001_id'] !== $segundo['pcp001_id'] || $segundo['participantes'] !== 2) throw new RuntimeException('Criação ou entrada compartilhada falhou.');
    $cabecalho = (int) $primeiro['pcp001_id'];
    mysqli_query($conn, 'UPDATE PCP002 SET PCP002_Data_Hora_Inicio=DATE_SUB(NOW(), INTERVAL PCP002_Id MINUTE)');
    $horarios = pcp_apontamento_all($conn, 'SELECT PCP002_Data_Hora_Inicio AS inicio FROM PCP002 WHERE PCP002_PCP001_Id=?', 'i', [$cabecalho]);
    if ($horarios[0]['inicio'] === $horarios[1]['inicio']) throw new RuntimeException('Participações não mantêm horários individuais.');
    try { pcp_apontamento_inicio($conn, $ctx, '101', 20, 40, false, null); throw new RuntimeException('Colaborador entrou em outra OP ainda aberto.'); } catch (DomainException $expected) { }
    pcp_apontamento_acao($conn, $ctx, '101', 'pause', 1);
    if (pcp_apontamento_aberto($conn, 1, 2)['status'] !== 'A') throw new RuntimeException('Pausa individual afetou outro participante.');
    pcp_apontamento_acao($conn, $ctx, '101', 'resume', null);
    pcp_apontamento_acao($conn, $ctx, '101', 'finish', null);
    if (pcp_apontamento_one($conn, 'SELECT PCP001_Status AS s FROM PCP001 WHERE PCP001_Id=?', 'i', [$cabecalho])['s'] !== 'A') throw new RuntimeException('Primeira finalização fechou o cabeçalho.');
    pcp_apontamento_acao($conn, $ctx, '102', 'finish', null);
    if (pcp_apontamento_one($conn, 'SELECT PCP001_Status AS s, PCP001_Data_Hora_Fim AS fim FROM PCP001 WHERE PCP001_Id=?', 'i', [$cabecalho])['s'] !== 'F') throw new RuntimeException('Última finalização não fechou o cabeçalho.');

    pcp_apontamento_inicio($conn, $ctx, '101', 10, 40, true, 1);
    pcp_apontamento_inicio($conn, $ctx, '102', 10, 40, true, 2);
    if ((int) pcp_apontamento_one($conn, "SELECT COUNT(*) AS n FROM PCP001 WHERE PCP001_Status='A' AND PCP001_T255_Id=10 AND PCP001_SMT006_Id=40")['n'] !== 2) throw new RuntimeException('Motivos de retrabalho distintos foram misturados.');
    pcp_apontamento_acao($conn, $ctx, '101', 'finish', null); pcp_apontamento_acao($conn, $ctx, '102', 'finish', null);
    if (function_exists('pcntl_fork')) {
        $pids = [];
        foreach (['101', '102'] as $matricula) {
            $pid = pcntl_fork();
            if ($pid === 0) {
                $child = mysqli_connect('127.0.0.1', 'root', 'hardness', $schema);
                try { pcp_apontamento_inicio($child, $ctx, $matricula, 20, 40, false, null); exit(0); }
                catch (Throwable $e) { exit(1); }
            }
            $pids[] = $pid;
        }
        foreach ($pids as $pid) { pcntl_waitpid($pid, $status); if (pcntl_wexitstatus($status) !== 0) throw new RuntimeException('Entrada concorrente falhou.'); }
        mysqli_close($conn);
        $conn = mysqli_connect('127.0.0.1', 'root', 'hardness', $schema);
        if (!$conn) throw new RuntimeException('Reconexão após concorrência indisponível.');
        if ((int) pcp_apontamento_one($conn, "SELECT COUNT(*) AS n FROM PCP001 WHERE PCP001_Status='A' AND PCP001_T255_Id=20 AND PCP001_SMT006_Id=40 AND PCP001_Flag_Retrabalho='N'")['n'] !== 1) throw new RuntimeException('Concorrência criou dois cabeçalhos abertos.');
        pcp_apontamento_acao($conn, $ctx, '101', 'finish', null); pcp_apontamento_acao($conn, $ctx, '102', 'finish', null);
    }
    try { pcp_apontamento_inicio($conn, $ctx, '201', 30, 40, false, null); throw new RuntimeException('Isolamento obrigatório por empresa falhou.'); } catch (DomainException $expected) { }

    // A rota antiga é tratada no front controller antes de qualquer POST/mutação.
    $front = file_get_contents(dirname(__DIR__) . '/public/index.php');
    if (strpos($front, "if (\$pagina === 'grupo'") === false || strpos($front, "require_once __DIR__ . '/../bootstrap/group.php';") !== false) throw new RuntimeException('Rota antiga de grupo ainda pode carregar mutações.');
    echo "task09 integration: OK\n";
} finally {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    mysqli_close($conn);
}
