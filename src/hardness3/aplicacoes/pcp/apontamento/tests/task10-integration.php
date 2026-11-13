<?php
declare(strict_types=1);
/* Descartável: php tests/task10-integration.php (container hardness-php7). */
require dirname(__DIR__) . '/bootstrap/app.php';
$schema = 'pcp_task10_validation';
$conn = mysqli_connect('127.0.0.1', 'root', 'hardness');
if (!$conn) throw new RuntimeException('Conexão de teste indisponível.');
try {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema);
    if (!mysqli_query($conn, 'CREATE DATABASE ' . $schema . ' CHARACTER SET latin1')) throw new RuntimeException(mysqli_error($conn));
    mysqli_select_db($conn, $schema);
    foreach (['CREATE TABLE C004 (C004_Id int primary key,C004_Nome_Empresa varchar(100),C004_Nome_Abreviado varchar(100),C004_Flag_Ativo char(1),C004_Ordem_Empresa int)','CREATE TABLE C019 (C019_C007_Id int,C019_C004_Id int,C019_C021_Id int)','CREATE TABLE C030 (C030_Id int auto_increment primary key,C030_C021_Id int,C030_C029_Id int,C030_Acesso_Liberado char(1))','CREATE TABLE C029 (C029_Id int primary key,C029_Descricao_Acesso varchar(100))','CREATE TABLE C007 (C007_Id int primary key,C007_Nome varchar(100),C007_Primeiro_Nome varchar(100),C007_Login varchar(100),C007_Senha varchar(255),C007_Ativo char(1),C007_D024_Id int,C007_Flag_Ausente char(1),C007_Dispositivo_Acesso char(1))'] as $sql) if (!mysqli_query($conn, $sql)) throw new RuntimeException(mysqli_error($conn));
    $hash = password_hash('senha-de-teste', PASSWORD_BCRYPT);
    $hashSql = mysqli_real_escape_string($conn, $hash);
    mysqli_multi_query($conn, "INSERT INTO C004 VALUES (1,'Empresa Um','Um','S',1),(2,'Empresa Dois','Dois','S',2),(3,'Bloqueada','Bloqueada','S',3),(4,'Empresa Quatro','Quatro','S',4); INSERT INTO C029 VALUES (120,'P.C.P.'),(203,'PCP'),(308,'Acessar Apontamento PCP'),(309,'Editar/Excluir Apontamentos PCP'); INSERT INTO C019 VALUES (10,1,100),(11,2,110),(11,3,111),(11,4,112),(12,1,120); INSERT INTO C030 (C030_C021_Id,C030_C029_Id,C030_Acesso_Liberado) VALUES (100,203,'S'),(100,308,'S'),(110,203,'S'),(110,308,'S'),(111,203,'S'),(111,308,'N'),(112,203,'S'),(112,308,'S'),(120,203,'S'),(120,120,'S'),(120,309,'S'); INSERT INTO C007 VALUES (10,'Usuário Elegível','Elegível','elegivel','{$hashSql}','S',NULL,'N','0'),(12,'Sem acesso','Sem','sem-acesso','{$hashSql}','S',NULL,'N','0'),(13,'Inativo','Inativo','inativo','{$hashSql}','N',NULL,'N','0')");
    while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
    $authenticated = pcp_apontamento_authenticate_with_connection($conn, 'elegivel', 'senha-de-teste');
    if (!$authenticated || (int) $authenticated['C007_Id'] !== 10) throw new RuntimeException('C007 ativo elegível não autenticou.');
    if (pcp_apontamento_authenticate_with_connection($conn, 'elegivel', 'senha-incorreta') !== null) throw new RuntimeException('Senha inválida foi aceita.');
    if (pcp_apontamento_authenticate_with_connection($conn, 'inativo', 'senha-de-teste') !== null) throw new RuntimeException('C007 inativo foi aceito.');
    if (pcp_apontamento_authenticate_with_connection($conn, 'sem-acesso', 'senha-de-teste') !== null) throw new RuntimeException('Usuário sem 203/308 foi aceito.');
    if (array_key_exists('C007_Senha', $authenticated) || strpos(serialize($authenticated), 'senha-de-teste') !== false || strpos(serialize($authenticated), $hash) !== false) throw new RuntimeException('Senha ou hash vazou no resultado da autenticação.');
    if (array_keys(pcp_apontamento_fetch_companies($conn, 10)) !== [1]) throw new RuntimeException('Usuário de uma empresa elegível incorreto.');
    if (array_keys(pcp_apontamento_fetch_companies($conn, 11)) !== [2,4]) throw new RuntimeException('Seleção múltipla ou empresa sem 308 incorreta.');
    if (pcp_apontamento_fetch_companies($conn, 12)) throw new RuntimeException('Usuário sem 203/308 foi autorizado.');
    if (pcp_apontamento_profile_permission($conn, 100, 120) || !pcp_apontamento_profile_permission($conn, 120, 120)) throw new RuntimeException('C029 120 incorreta.');
    if (pcp_apontamento_profile_permission($conn, 100, 309) || !pcp_apontamento_profile_permission($conn, 120, 309)) throw new RuntimeException('C029 309 incorreta.');
    $_SESSION = ['erp_marker'=>'preservado']; $sessionBefore = session_id(); pcp_apontamento_start_session($authenticated, $schema);
    if (session_id() === $sessionBefore || ($_SESSION['erp_marker'] ?? '') !== 'preservado' || isset($_SESSION['Usuario_Atual']) || empty($_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'])) throw new RuntimeException('Início da sessão não regenerou ou alterou contexto ERP.');
    if (array_key_exists('C007_Senha', $_SESSION[PCP_APONTAMENTO_SESSION]) || strpos(serialize($_SESSION[PCP_APONTAMENTO_SESSION]), 'senha-de-teste') !== false || strpos(serialize($_SESSION[PCP_APONTAMENTO_SESSION]), $hash) !== false) throw new RuntimeException('Senha ou hash entrou na sessão do módulo.');
    $_SESSION[PCP_APONTAMENTO_SESSION]['last_activity'] = time() - 28801;
    if (!pcp_apontamento_terminal_expired()) throw new RuntimeException('Terminal não expirou após oito horas de inatividade.');
    $_SESSION[PCP_APONTAMENTO_SESSION]['last_activity'] = time();
    if (pcp_apontamento_terminal_expired()) throw new RuntimeException('Terminal expirou antes do prazo de inatividade.');
    $_SESSION['Usuario_Atual'] = 999; pcp_apontamento_logout();
    if ((int) $_SESSION['Usuario_Atual'] !== 999 || empty($_SESSION[PCP_APONTAMENTO_SESSION]['closed'])) throw new RuntimeException('Logout alterou a sessão global ou não fechou o módulo.');
    $front = file_get_contents(dirname(__DIR__) . '/public/index.php');
    if (strpos($front, "pcp_apontamento_context()) { header('Location: ' . pcp_apontamento_url('login.php'))") === false) throw new RuntimeException('Rota sem sessão própria não é bloqueada.');
    echo "task10 integration: OK\n";
} finally { mysqli_query($conn, 'DROP DATABASE IF EXISTS ' . $schema); mysqli_close($conn); }
