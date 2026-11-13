<?php
namespace hardness;
set_time_limit(0);
ini_set('default_charset', 'utf-8');
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');

// para previnir a re-declaração das funções ao executar a atualização base para todas as empresas
if (isset($atualizacaoTodasEmpresas)) {
	require_once('/var/www/eurosul.hardness.com.br/hardness3/scripts/atualizacaoBase-funcoes.php');
} else {
	require_once('scripts/atualizacaoBase-funcoes.php');
}

// Bloqueio de execução em horário comercial (evita travar o sistema).
bloquearAtualizacaoBaseHorarioComercial();
/* INÍCIO PROJETO APONTAMENTO */

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS SMT006 (
SMT006_Id int unsigned NOT NULL AUTO_INCREMENT,
SMT006_Descricao varchar(100) DEFAULT NULL COMMENT 'Descrição',
SMT006_Valor decimal(10,2) DEFAULT NULL,
SMT006_Flag_Pintura enum('S','N') DEFAULT 'N' COMMENT 'Flag para determinar se um processo é pintura.',
SMT006_Flag_Apontamento enum('S','N') NOT NULL DEFAULT 'N' COMMENT 'Liberado para uso no Apontamento PCP.',
SMT006_SMT201_Id int DEFAULT NULL COMMENT 'Vínculo com a etapa',
PRIMARY KEY (SMT006_Id),
KEY I_SMT006_SMT201_Id (SMT006_SMT201_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Processos de engenharia';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Instalações anteriores já possuem SMT006 e precisam receber a flag sem
// qualquer migração ou alteração dos registros PCP existentes.
Criar_Campo('SMT006_Flag_Apontamento', "enum('S','N') NOT NULL DEFAULT 'N' COMMENT 'Liberado para uso no Apontamento PCP.'");

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS SMT012 (
SMT012_Id int unsigned NOT NULL AUTO_INCREMENT,
SMT012_Descricao varchar(100) DEFAULT NULL,
SMT012_Cor varchar(10) DEFAULT NULL COMMENT 'Cor para destacar o registro. Informar em HEX (Ex: #FFFFFF)',
SMT012_Obriga_Apontar_Quantidade enum('S','N') DEFAULT 'N' COMMENT 'Obriga_Apontar_Quantidade',
PRIMARY KEY (SMT012_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Cadastro de motivos de parada';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// PCP — Apontamento de Produção. D109 é o cadastro mestre; PCP006 delimita
// quem participa do apontamento e mantém sua matrícula e seus processos.
// As relações são lógicas, sem FK, conforme o padrão do ERP.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP006 (
PCP006_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP006_D109_Id int unsigned NOT NULL COMMENT 'Colaborador mestre habilitado no apontamento',
PCP006_Matricula varchar(250) NOT NULL COMMENT 'Matrícula usada pelo apontamento',
PCP006_Status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=ativo; I=inativo',
PRIMARY KEY (PCP006_Id),
UNIQUE KEY IDX_PCP006_D109_Id (PCP006_D109_Id),
UNIQUE KEY IDX_PCP006_Matricula (PCP006_Matricula),
KEY IDX_PCP006_Status (PCP006_Status)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Colaboradores habilitados no apontamento PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP001 (
PCP001_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP001_T255_Id int unsigned NOT NULL COMMENT 'OP apontada pelo grupo',
PCP001_SMT006_Id int unsigned NOT NULL COMMENT 'Processo selecionado no apontamento',
PCP001_Status enum('A','F','I') NOT NULL DEFAULT 'A' COMMENT 'A=ativo; F=finalizado; I=inativado',
PCP001_Data_Hora_Inicio datetime NOT NULL COMMENT 'Início do grupo',
PCP001_Data_Hora_Fim datetime DEFAULT NULL COMMENT 'Finalização do grupo',
PCP001_Flag_Retrabalho enum('S','N') NOT NULL DEFAULT 'N' COMMENT 'Indica retrabalho',
PCP001_PCP005_Id int unsigned DEFAULT NULL COMMENT 'Motivo de retrabalho',
PCP001_C007_Id int DEFAULT NULL COMMENT 'Usuário técnico da estação',
PRIMARY KEY (PCP001_Id),
KEY IDX_PCP001_T255_Id (PCP001_T255_Id),
KEY IDX_PCP001_SMT006_Id (PCP001_SMT006_Id),
KEY IDX_PCP001_Status (PCP001_Status),
KEY IDX_PCP001_PCP005_Id (PCP001_PCP005_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Grupos de apontamento PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP002 (
PCP002_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP002_PCP001_Id int unsigned NOT NULL COMMENT 'Grupo de apontamento',
PCP002_D109_Id int unsigned NOT NULL COMMENT 'Colaborador participante',
PCP002_Status enum('A','P','F','I') NOT NULL DEFAULT 'A' COMMENT 'A=ativo; P=pausado; F=finalizado; I=inativado',
PCP002_Data_Hora_Inicio datetime NOT NULL COMMENT 'Início da participação',
PCP002_Data_Hora_Fim datetime DEFAULT NULL COMMENT 'Finalização da participação',
PRIMARY KEY (PCP002_Id),
KEY IDX_PCP002_PCP001_Id (PCP002_PCP001_Id),
KEY IDX_PCP002_D109_Id (PCP002_D109_Id),
KEY IDX_PCP002_Status (PCP002_Status)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Participantes dos grupos de apontamento PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Evolução compatível do modelo PCP: relações diretas para OP e processo.
Criar_Campo('PCP001_T255_Id', 'int unsigned DEFAULT NULL COMMENT \'OP apontada pelo grupo\'');
Criar_Campo('PCP001_SMT006_Id', 'int unsigned DEFAULT NULL COMMENT \'Processo selecionado no apontamento\'');
Criar_Indice('PCP001_T255_Id');
Criar_Indice('PCP001_SMT006_Id');
// Inativação lógica: preserva o histórico e identifica quem a executou.
Criar_Campo('PCP001_Data_Hora_Inativacao', 'datetime DEFAULT NULL');
Criar_Campo('PCP001_C007_Id_Inativacao', 'int DEFAULT NULL');
Criar_Campo('PCP001_Motivo_Inativacao', 'varchar(500) DEFAULT NULL');
Criar_Campo('PCP002_Data_Hora_Inativacao', 'datetime DEFAULT NULL');
Criar_Campo('PCP002_C007_Id_Inativacao', 'int DEFAULT NULL');
Criar_Campo('PCP002_Motivo_Inativacao', 'varchar(500) DEFAULT NULL');
Alterar_Tipo_Campo('PCP001_Status', "enum('A','F','I') NOT NULL DEFAULT 'A'");
Alterar_Tipo_Campo('PCP002_Status', "enum('A','P','F','I') NOT NULL DEFAULT 'A'");


$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP003 (
PCP003_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP003_PCP001_Id int unsigned NOT NULL COMMENT 'Grupo relacionado ao evento',
PCP003_PCP002_Id int unsigned DEFAULT NULL COMMENT 'Participante; nulo para evento do grupo',
PCP003_Tipo enum('I','P','R','F') NOT NULL COMMENT 'I=iniciar; P=pausar; R=retomar; F=finalizar',
PCP003_Data_Hora datetime NOT NULL COMMENT 'Momento do evento',
PCP003_SMT012_Id int unsigned DEFAULT NULL COMMENT 'Motivo de parada, quando aplicável',
PCP003_C007_Id int DEFAULT NULL COMMENT 'Usuário técnico que registrou o evento',
PCP003_Status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=ativo; I=cancelado',
PCP003_Data_Hora_Inativacao datetime DEFAULT NULL,
PCP003_C007_Id_Inativacao int DEFAULT NULL,
PCP003_Motivo_Inativacao varchar(500) DEFAULT NULL,
PRIMARY KEY (PCP003_Id),
KEY IDX_PCP003_PCP001_Id (PCP003_PCP001_Id),
KEY IDX_PCP003_PCP002_Id (PCP003_PCP002_Id),
KEY IDX_PCP003_Data_Hora (PCP003_Data_Hora),
KEY IDX_PCP003_SMT012_Id (PCP003_SMT012_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Eventos de grupos e participantes PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
Criar_Campo('PCP003_Status', "enum('A','I') NOT NULL DEFAULT 'A'");
Criar_Campo('PCP003_Data_Hora_Inativacao', 'datetime DEFAULT NULL');
Criar_Campo('PCP003_C007_Id_Inativacao', 'int DEFAULT NULL');
Criar_Campo('PCP003_Motivo_Inativacao', 'varchar(500) DEFAULT NULL');
Alterar_Tipo_Campo('PCP003_Status', "enum('A','I') NOT NULL DEFAULT 'A'");


$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP004 (
PCP004_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP004_PCP006_Id int unsigned NOT NULL COMMENT 'Colaborador PCP habilitado',
PCP004_SMT006_Id int unsigned NOT NULL COMMENT 'Processo habilitado',
PCP004_Status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=ativo; I=inativo',
PRIMARY KEY (PCP004_Id),
UNIQUE KEY IDX_PCP004_PCP006_Id_PCP004_SMT006_Id (PCP004_PCP006_Id,PCP004_SMT006_Id),
KEY IDX_PCP004_PCP006_Id (PCP004_PCP006_Id),
KEY IDX_PCP004_SMT006_Id (PCP004_SMT006_Id),
KEY IDX_PCP004_Status (PCP004_Status)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Habilitação de colaborador por processo PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP005 (
PCP005_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP005_Descricao varchar(100) NOT NULL COMMENT 'Motivo de retrabalho',
PCP005_Status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=ativo; I=inativo',
PRIMARY KEY (PCP005_Id),
KEY IDX_PCP005_Status (PCP005_Status)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Motivos de retrabalho PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Auditoria das alterações administrativas de apontamentos. Os valores são
// gravados em JSON textual para preservar o estado anterior e posterior.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS PCP007 (
PCP007_Id int unsigned NOT NULL AUTO_INCREMENT,
PCP007_PCP001_Id int unsigned NOT NULL,
PCP007_PCP002_Id int unsigned DEFAULT NULL,
PCP007_PCP003_Id int unsigned DEFAULT NULL,
PCP007_Acao varchar(50) NOT NULL,
PCP007_Justificativa varchar(500) DEFAULT NULL,
PCP007_Dados_Anteriores longtext DEFAULT NULL,
PCP007_Dados_Novos longtext DEFAULT NULL,
PCP007_Data_Hora datetime NOT NULL,
PCP007_C007_Id int DEFAULT NULL,
PRIMARY KEY (PCP007_Id),
KEY IDX_PCP007_PCP001_Id (PCP007_PCP001_Id),
KEY IDX_PCP007_PCP002_Id (PCP007_PCP002_Id),
KEY IDX_PCP007_PCP003_Id (PCP007_PCP003_Id),
KEY IDX_PCP007_Data_Hora (PCP007_Data_Hora)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Auditoria de alterações administrativas do apontamento PCP';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
Criar_Campo('PCP007_PCP003_Id', 'int unsigned DEFAULT NULL');
Criar_Indice('PCP007_PCP003_Id');

/* FIM PROJETO APONTAMENTO */

/**
* TABELAS I (Internas)
*/

// criação da tabela A001 (tokens da API REST)
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS A001 (
A001_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
A001_Nome VARCHAR(100) NOT NULL,
A001_Token_Hash CHAR(64) NOT NULL,
A001_Token_Prefixo VARCHAR(12) NOT NULL,
A001_Status ENUM('active', 'inactive', 'revoked') NOT NULL DEFAULT 'active',
A001_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
A001_Data_Revogacao DATETIME NULL DEFAULT NULL,
A001_Data_Ultimo_Uso DATETIME NULL DEFAULT NULL,
A001_IP_Ultimo_Uso VARCHAR(45) NULL DEFAULT NULL,
A001_Observacao TEXT NULL,
PRIMARY KEY (A001_Id),
UNIQUE KEY U_A001_Token_Hash (A001_Token_Hash),
KEY I_A001_Status (A001_Status),
KEY I_A001_Data_Ultimo_Uso (A001_Data_Ultimo_Uso)
)
DEFAULT CHARSET=utf8mb4
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Webhooks: fundação relacional do módulo isolado. As relações são lógicas,
// deliberadamente sem FOREIGN KEY, para manter o padrão e a compatibilidade do ERP.
// A criação não habilita endpoints, subscriptions ou transmissões.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK001 (
WHK001_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK001_C004_Id BIGINT UNSIGNED NOT NULL,
WHK001_Nome VARCHAR(150) NOT NULL,
WHK001_URL VARCHAR(2048) NOT NULL,
WHK001_URL_Hash BINARY(32) NOT NULL,
WHK001_Destination_Auth_Type ENUM('N','B') NOT NULL DEFAULT 'N',
WHK001_Destination_Auth_Criptografada LONGTEXT NULL,
WHK001_WHK007_Id_Atual BIGINT UNSIGNED NULL DEFAULT NULL,
WHK001_Status ENUM('I','A') NOT NULL DEFAULT 'I',
WHK001_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
WHK001_Data_Alteracao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (WHK001_Id),
KEY I_WHK001_C004_Status (WHK001_C004_Id,WHK001_Status)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK002 (
WHK002_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK002_C004_Id BIGINT UNSIGNED NOT NULL,
WHK002_WHK001_Id BIGINT UNSIGNED NOT NULL,
WHK002_Event_Type VARCHAR(100) NOT NULL,
WHK002_Status ENUM('I','A') NOT NULL DEFAULT 'I',
WHK002_Filtro LONGTEXT NULL,
WHK002_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
WHK002_Data_Alteracao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (WHK002_Id),
UNIQUE KEY U_WHK002_C004_Endpoint_Event (WHK002_C004_Id,WHK002_WHK001_Id,WHK002_Event_Type),
KEY I_WHK002_Publisher (WHK002_C004_Id,WHK002_Event_Type,WHK002_Status)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK003 (
WHK003_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK003_Event_Id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
WHK003_C004_Id BIGINT UNSIGNED NOT NULL,
WHK003_Type VARCHAR(100) NOT NULL,
WHK003_Schema_Version VARCHAR(20) NOT NULL,
WHK003_Aggregate_Type VARCHAR(100) NOT NULL,
WHK003_Aggregate_Id VARCHAR(100) NOT NULL,
WHK003_Payload LONGTEXT NOT NULL,
WHK003_Occurred_At DATETIME NOT NULL,
WHK003_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (WHK003_Id),
UNIQUE KEY U_WHK003_Event_Id (WHK003_Event_Id)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK004 (
WHK004_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK004_Delivery_Id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
WHK004_C004_Id BIGINT UNSIGNED NOT NULL,
WHK004_WHK003_Id BIGINT UNSIGNED NOT NULL,
WHK004_WHK001_Id BIGINT UNSIGNED NOT NULL,
WHK004_WHK002_Id BIGINT UNSIGNED NOT NULL,
WHK004_WHK007_Id BIGINT UNSIGNED NULL DEFAULT NULL,
WHK004_Destination_URL VARCHAR(2048) NOT NULL,
WHK004_Status ENUM('P','L','R','D','X','C') NOT NULL DEFAULT 'P',
WHK004_Attempt_Count INT UNSIGNED NOT NULL DEFAULT 0,
WHK004_Next_Attempt_At DATETIME NULL DEFAULT NULL,
WHK004_Lease_Until DATETIME NULL DEFAULT NULL,
WHK004_Worker_Id VARCHAR(191) NULL DEFAULT NULL,
WHK004_Leased_At DATETIME NULL DEFAULT NULL,
WHK004_Delivered_At DATETIME NULL DEFAULT NULL,
WHK004_Last_Http_Status SMALLINT UNSIGNED NULL DEFAULT NULL,
WHK004_Last_Error VARCHAR(1000) NULL DEFAULT NULL,
WHK004_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
WHK004_Data_Alteracao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (WHK004_Id),
UNIQUE KEY U_WHK004_Delivery_Id (WHK004_Delivery_Id),
KEY I_WHK004_Worker_Next_Attempt (WHK004_Status,WHK004_Next_Attempt_At),
KEY I_WHK004_Worker_Lease (WHK004_Status,WHK004_Lease_Until),
KEY I_WHK004_C004_Status_Next_Attempt (WHK004_C004_Id,WHK004_Status,WHK004_Next_Attempt_At),
KEY I_WHK004_Endpoint (WHK004_WHK001_Id),
KEY I_WHK004_Event (WHK004_WHK003_Id)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK005 (
WHK005_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK005_WHK004_Id BIGINT UNSIGNED NOT NULL,
WHK005_Attempt_Number INT UNSIGNED NOT NULL,
WHK005_Worker_Id VARCHAR(191) NULL DEFAULT NULL,
WHK005_Started_At DATETIME NOT NULL,
WHK005_Finished_At DATETIME NULL DEFAULT NULL,
WHK005_Result VARCHAR(32) NOT NULL,
WHK005_Http_Status SMALLINT UNSIGNED NULL DEFAULT NULL,
WHK005_Transport_Error VARCHAR(1000) NULL DEFAULT NULL,
WHK005_Duration_Ms INT UNSIGNED NULL DEFAULT NULL,
WHK005_Response_Bytes BIGINT UNSIGNED NULL DEFAULT NULL,
PRIMARY KEY (WHK005_Id),
UNIQUE KEY U_WHK005_Delivery_Attempt (WHK005_WHK004_Id,WHK005_Attempt_Number)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK006 (
WHK006_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK006_C004_Id BIGINT UNSIGNED NOT NULL,
WHK006_Event_Type VARCHAR(100) NOT NULL,
WHK006_Status ENUM('I','A') NOT NULL DEFAULT 'I',
WHK006_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
WHK006_Data_Alteracao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (WHK006_Id),
UNIQUE KEY U_WHK006_C004_Event (WHK006_C004_Id,WHK006_Event_Type)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS WHK007 (
WHK007_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
WHK007_WHK001_Id BIGINT UNSIGNED NOT NULL,
WHK007_Versao INT UNSIGNED NOT NULL,
WHK007_Secret_Criptografado LONGTEXT NOT NULL,
WHK007_Status ENUM('I','A','R') NOT NULL DEFAULT 'I',
WHK007_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
WHK007_Data_Alteracao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (WHK007_Id),
UNIQUE KEY U_WHK007_Endpoint_Versao (WHK007_WHK001_Id,WHK007_Versao)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// MCP: namespace reservado ao módulo isolado MCP. MCP001-003 já estão em produção;
// MCP004-012 são somente a fundação relacional do OAuth 2.1. Esta migration não ativa
// endpoints, discovery ou autenticação OAuth.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP001 (
MCP001_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP001_Nome VARCHAR(100) NOT NULL,
MCP001_C004_Id BIGINT UNSIGNED NOT NULL,
MCP001_C007_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP001_Public_Id CHAR(24) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL,
MCP001_Scopes TEXT NOT NULL,
MCP001_Status ENUM('active','disabled','revoked') NOT NULL DEFAULT 'active',
MCP001_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP001_Data_Alteracao DATETIME NULL DEFAULT NULL,
MCP001_Data_Expiracao DATETIME NULL DEFAULT NULL,
MCP001_Data_Revogacao DATETIME NULL DEFAULT NULL,
MCP001_Motivo_Revogacao VARCHAR(255) NULL DEFAULT NULL,
PRIMARY KEY (MCP001_Id),
UNIQUE KEY U_MCP001_Public_Id (MCP001_Public_Id),
KEY I_MCP001_C004_Status (MCP001_C004_Id, MCP001_Status),
KEY I_MCP001_C007 (MCP001_C007_Id)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// Preços MCP (etapa 1): somente fundação relacional, sem habilitar Tools/escritas.
// Relações lógicas sem FK, seguindo o padrão das demais tabelas MCP.
// Datas em UTC. Histórico empresarial não deve ser copiado para MCP003.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP020 (
MCP020_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP020_Public_Id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
MCP020_C004_Id BIGINT UNSIGNED NOT NULL,
MCP020_MCP001_Id BIGINT UNSIGNED NOT NULL,
MCP020_C007_Id BIGINT UNSIGNED NOT NULL,
MCP020_Chave_Idempotencia VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
MCP020_Hash_Entrada BINARY(32) NOT NULL,
MCP020_Hash_Previa BINARY(32) NULL DEFAULT NULL,
MCP020_Aprovar_Parcial TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP020_Aprovar_Zero TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP020_Tipo ENUM('update','reversal') NOT NULL DEFAULT 'update',
MCP020_Origem ENUM('manual','spreadsheet') NOT NULL,
MCP020_MCP020_Id_Original BIGINT UNSIGNED NULL DEFAULT NULL,
MCP020_Status ENUM('building','prepared','approved','processing','completed','partial','cancelled','expired','failed') NOT NULL DEFAULT 'building',
MCP020_Motivo VARCHAR(500) NULL DEFAULT NULL,
MCP020_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP020_Data_Alteracao DATETIME NULL DEFAULT NULL,
MCP020_Data_Expiracao DATETIME NOT NULL,
MCP020_Data_Aprovacao DATETIME NULL DEFAULT NULL,
MCP020_C007_Id_Aprovacao BIGINT UNSIGNED NULL DEFAULT NULL,
MCP020_MCP001_Id_Aprovacao BIGINT UNSIGNED NULL DEFAULT NULL,
MCP020_Data_Inicio DATETIME NULL DEFAULT NULL,
MCP020_Data_Conclusao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP020_Id),
UNIQUE KEY U_MCP020_Public_Id (MCP020_Public_Id),
UNIQUE KEY U_MCP020_Idempotencia (MCP020_C004_Id,MCP020_MCP001_Id,MCP020_Chave_Idempotencia),
KEY I_MCP020_Empresa_Status_Data (MCP020_C004_Id,MCP020_Status,MCP020_Data_Criacao),
KEY I_MCP020_Status_Expiracao (MCP020_Status,MCP020_Data_Expiracao),
KEY I_MCP020_Original (MCP020_MCP020_Id_Original)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// Compatibilidade com bases que já receberam a fundação de preços.
Criar_Campo("MCP020_Aprovar_Parcial","TINYINT UNSIGNED NOT NULL DEFAULT 0");
Criar_Campo("MCP020_Aprovar_Zero","TINYINT UNSIGNED NOT NULL DEFAULT 0");

// Uma linha por produto alvo no lote; linhas inválidas/duplicadas ficam com D009 NULL.
// Código é texto (preserva zeros). Linha identifica a posição original da entrada.
// Flags selecionam campos: NULL em preço não significa campo não selecionado.
// DECIMAL(20,10) acompanha D009/D009A sem arredondar; NULL original é preservado.
// Preço 5 vem de D009A_Preco_5, vinculado por D009A_D009_Id = MCP021_D009_Id.
// Ausência de D009A é pendência para preço 5, nunca um preço anterior zero.
// Antes/depois são snapshots completos; campos não selecionados permanecem iguais.
// Hash/índices são a fundação de idempotência: o executor ainda deverá bloquear o item
// e gravar D009/D009A + status applied na MESMA transação/conexão InnoDB, sem DDL.
// Snapshots de itens aplicados são imutáveis; desfazer cria outro lote/item vinculado.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP021 (
MCP021_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP021_MCP020_Id BIGINT UNSIGNED NOT NULL,
MCP021_C004_Id BIGINT UNSIGNED NOT NULL,
MCP021_Linha INT UNSIGNED NOT NULL,
MCP021_Codigo_Produto VARCHAR(255) NOT NULL,
MCP021_D009_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP021_MCP021_Id_Original BIGINT UNSIGNED NULL DEFAULT NULL,
MCP021_Status ENUM('pending','ready','blocked','unchanged','applied','conflict','failed','skipped') NOT NULL DEFAULT 'pending',
MCP021_Selecionado_1 TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP021_Selecionado_2 TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP021_Selecionado_3 TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP021_Selecionado_4 TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP021_Selecionado_5 TINYINT UNSIGNED NOT NULL DEFAULT 0,
MCP021_Preco_1_Antes DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_2_Antes DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_3_Antes DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_4_Antes DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_5_Antes DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_1_Depois DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_2_Depois DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_3_Depois DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_4_Depois DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Preco_5_Depois DECIMAL(20,10) NULL DEFAULT NULL,
MCP021_Codigo_Erro VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL,
MCP021_Tentativas INT UNSIGNED NOT NULL DEFAULT 0,
MCP021_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP021_Data_Snapshot DATETIME NULL DEFAULT NULL,
MCP021_Data_Ultima_Tentativa DATETIME NULL DEFAULT NULL,
MCP021_Data_Aplicacao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP021_Id),
UNIQUE KEY U_MCP021_Lote_Linha (MCP021_MCP020_Id,MCP021_Linha),
UNIQUE KEY U_MCP021_Lote_Produto (MCP021_MCP020_Id,MCP021_D009_Id),
KEY I_MCP021_Lote_Status (MCP021_MCP020_Id,MCP021_Status),
KEY I_MCP021_Produto_Historico (MCP021_C004_Id,MCP021_D009_Id,MCP021_Data_Aplicacao),
KEY I_MCP021_Original (MCP021_MCP021_Id_Original)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// OAuth authorization transactions survive the legacy login session reset. They are
// relational and contain no browser-session state or serialized lists.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP018 (
MCP018_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP018_MCP004_Id BIGINT UNSIGNED NOT NULL,
MCP018_MCP009_Id BIGINT UNSIGNED NOT NULL,
MCP018_Selector VARCHAR(48) NOT NULL,
MCP018_Secret_Hash BINARY(32) NOT NULL,
MCP018_Pepper_Version VARCHAR(20) NOT NULL,
MCP018_Issuer VARCHAR(255) NOT NULL,
MCP018_Resource VARCHAR(255) NOT NULL,
MCP018_State VARCHAR(2048) NULL DEFAULT NULL,
MCP018_PKCE_Challenge VARCHAR(128) NOT NULL,
MCP018_PKCE_Method ENUM('S256') NOT NULL DEFAULT 'S256',
MCP018_Status ENUM('pending','authenticated','consented','cancelled','expired','consumed') NOT NULL DEFAULT 'pending',
MCP018_C007_Id INT UNSIGNED NULL DEFAULT NULL,
MCP018_MCP001_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP018_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP018_Data_Expiracao DATETIME NOT NULL,
MCP018_Data_Autenticacao DATETIME NULL DEFAULT NULL,
MCP018_Data_Consentimento DATETIME NULL DEFAULT NULL,
MCP018_Data_Cancelamento DATETIME NULL DEFAULT NULL,
MCP018_Data_Consumo DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP018_Id),
UNIQUE KEY U_MCP018_Selector (MCP018_Selector),
KEY I_MCP018_Status_Expiracao (MCP018_Status,MCP018_Data_Expiracao),
KEY I_MCP018_Client (MCP018_MCP004_Id),
KEY I_MCP018_Usuario (MCP018_C007_Id)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP019 (
MCP019_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP019_MCP018_Id BIGINT UNSIGNED NOT NULL,
MCP019_Scope VARCHAR(100) NOT NULL,
PRIMARY KEY (MCP019_Id),
UNIQUE KEY U_MCP019_Request_Scope (MCP019_MCP018_Id,MCP019_Scope)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// OAuth MVP: clients, grants, codes e tokens são relacionais para manter compatibilidade
// com versões antigas do MySQL. Segredos nunca são persistidos em texto puro.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP004 (
MCP004_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP004_Client_Id VARCHAR(191) NOT NULL,
MCP004_Origem ENUM('dcr','pre_registered') NOT NULL,
MCP004_Tipo ENUM('public','confidential') NOT NULL DEFAULT 'public',
MCP004_Nome VARCHAR(150) NOT NULL,
MCP004_Client_URI VARCHAR(1024) NULL DEFAULT NULL,
MCP004_Logo_URI VARCHAR(1024) NULL DEFAULT NULL,
MCP004_Status ENUM('active','disabled','revoked') NOT NULL DEFAULT 'active',
MCP004_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP004_Data_Alteracao DATETIME NULL DEFAULT NULL,
MCP004_Data_Revogacao DATETIME NULL DEFAULT NULL,
MCP004_Data_Ultimo_Uso DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP004_Id),
UNIQUE KEY U_MCP004_Client_Id (MCP004_Client_Id),
KEY I_MCP004_Origem_Status (MCP004_Origem,MCP004_Status)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP005 (
MCP005_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP005_MCP001_Id BIGINT UNSIGNED NOT NULL,
MCP005_MCP004_Id BIGINT UNSIGNED NOT NULL,
MCP005_C007_Id INT UNSIGNED NOT NULL,
MCP005_Chave_Ativa VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL,
MCP005_Status ENUM('active','revoked','expired') NOT NULL DEFAULT 'active',
MCP005_Data_Consentimento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP005_Data_Expiracao DATETIME NULL DEFAULT NULL,
MCP005_Data_Revogacao DATETIME NULL DEFAULT NULL,
MCP005_Motivo_Revogacao VARCHAR(255) NULL DEFAULT NULL,
MCP005_Data_Ultimo_Uso DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP005_Id),
UNIQUE KEY U_MCP005_Chave_Ativa (MCP005_Chave_Ativa),
KEY I_MCP005_Conexao_Status (MCP005_MCP001_Id,MCP005_Status),
KEY I_MCP005_Usuario_Status (MCP005_C007_Id,MCP005_Status),
KEY I_MCP005_Client_Status (MCP005_MCP004_Id,MCP005_Status)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP006 (
MCP006_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP006_MCP005_Id BIGINT UNSIGNED NOT NULL,
MCP006_MCP004_Id BIGINT UNSIGNED NOT NULL,
MCP006_MCP009_Id BIGINT UNSIGNED NOT NULL,
MCP006_Selector CHAR(16) NOT NULL,
MCP006_Secret_Hash BINARY(32) NOT NULL,
MCP006_Pepper_Version VARCHAR(20) NOT NULL,
MCP006_PKCE_Challenge VARCHAR(128) NOT NULL,
MCP006_PKCE_Method ENUM('S256') NOT NULL DEFAULT 'S256',
MCP006_Resource VARCHAR(255) NOT NULL,
MCP006_Status ENUM('active','consumed','revoked','expired') NOT NULL DEFAULT 'active',
MCP006_Data_Expiracao DATETIME NOT NULL,
MCP006_Data_Consumido DATETIME NULL DEFAULT NULL,
MCP006_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (MCP006_Id),
UNIQUE KEY U_MCP006_Selector (MCP006_Selector),
KEY I_MCP006_Status_Expiracao (MCP006_Status,MCP006_Data_Expiracao),
KEY I_MCP006_Grant (MCP006_MCP005_Id)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP007 (
MCP007_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP007_MCP005_Id BIGINT UNSIGNED NOT NULL,
MCP007_Selector CHAR(16) NOT NULL,
MCP007_Secret_Hash BINARY(32) NOT NULL,
MCP007_Pepper_Version VARCHAR(20) NOT NULL,
MCP007_Resource VARCHAR(255) NOT NULL,
MCP007_Status ENUM('active','revoked','expired') NOT NULL DEFAULT 'active',
MCP007_Data_Expiracao DATETIME NOT NULL,
MCP007_Data_Ultimo_Uso DATETIME NULL DEFAULT NULL,
MCP007_IP_Ultimo_Uso VARCHAR(45) NULL DEFAULT NULL,
MCP007_Data_Revogacao DATETIME NULL DEFAULT NULL,
MCP007_Motivo_Revogacao VARCHAR(255) NULL DEFAULT NULL,
MCP007_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (MCP007_Id),
UNIQUE KEY U_MCP007_Selector (MCP007_Selector),
KEY I_MCP007_Status_Expiracao (MCP007_Status,MCP007_Data_Expiracao),
KEY I_MCP007_Grant (MCP007_MCP005_Id)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP008 (
MCP008_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP008_MCP005_Id BIGINT UNSIGNED NOT NULL,
MCP008_Familia_Id CHAR(32) NOT NULL,
MCP008_Selector CHAR(16) NOT NULL,
MCP008_Secret_Hash BINARY(32) NOT NULL,
MCP008_Pepper_Version VARCHAR(20) NOT NULL,
MCP008_Resource VARCHAR(255) NULL DEFAULT NULL,
MCP008_Substituido_Por_MCP008_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP008_Status ENUM('active','used','revoked','expired') NOT NULL DEFAULT 'active',
MCP008_Data_Expiracao DATETIME NOT NULL,
MCP008_Data_Ultimo_Uso DATETIME NULL DEFAULT NULL,
MCP008_Data_Revogacao DATETIME NULL DEFAULT NULL,
MCP008_Motivo_Revogacao VARCHAR(255) NULL DEFAULT NULL,
MCP008_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (MCP008_Id),
UNIQUE KEY U_MCP008_Selector (MCP008_Selector),
KEY I_MCP008_Familia_Status (MCP008_Familia_Id,MCP008_Status),
KEY I_MCP008_Grant (MCP008_MCP005_Id)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP009 (
MCP009_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP009_MCP004_Id BIGINT UNSIGNED NOT NULL,
MCP009_Redirect_URI VARCHAR(1024) NOT NULL,
MCP009_URI_Hash BINARY(32) NOT NULL,
MCP009_Status ENUM('active','revoked') NOT NULL DEFAULT 'active',
MCP009_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP009_Data_Revogacao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP009_Id),
UNIQUE KEY U_MCP009_Client_URI (MCP009_MCP004_Id,MCP009_URI_Hash),
KEY I_MCP009_Client_Status (MCP009_MCP004_Id,MCP009_Status)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP010 (
MCP010_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP010_MCP005_Id BIGINT UNSIGNED NOT NULL,
MCP010_Scope VARCHAR(100) NOT NULL,
MCP010_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (MCP010_Id),
UNIQUE KEY U_MCP010_Grant_Scope (MCP010_MCP005_Id,MCP010_Scope)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP011 (
MCP011_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP011_MCP006_Id BIGINT UNSIGNED NOT NULL,
MCP011_Scope VARCHAR(100) NOT NULL,
PRIMARY KEY (MCP011_Id),
UNIQUE KEY U_MCP011_Code_Scope (MCP011_MCP006_Id,MCP011_Scope)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP012 (
MCP012_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP012_MCP007_Id BIGINT UNSIGNED NOT NULL,
MCP012_Scope VARCHAR(100) NOT NULL,
PRIMARY KEY (MCP012_Id),
UNIQUE KEY U_MCP012_Token_Scope (MCP012_MCP007_Id,MCP012_Scope)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// Elegibilidade OAuth: MCP015 é vínculo direto e MCP016 reutiliza C021/C019 do ERP.

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP015 (
MCP015_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP015_MCP001_Id BIGINT UNSIGNED NOT NULL,
MCP015_C007_Id INT UNSIGNED NOT NULL,
MCP015_Status ENUM('active','revoked') NOT NULL DEFAULT 'active',
MCP015_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP015_Data_Revogacao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP015_Id),
UNIQUE KEY U_MCP015_Conexao_Usuario (MCP015_MCP001_Id,MCP015_C007_Id),
KEY I_MCP015_Usuario_Status (MCP015_C007_Id,MCP015_Status)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP017 (
MCP017_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP017_MCP001_Id BIGINT UNSIGNED NOT NULL,
MCP017_Status ENUM('active','disabled') NOT NULL DEFAULT 'disabled',
MCP017_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP017_Data_Alteracao DATETIME NULL DEFAULT NULL,
MCP017_Data_Desativacao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP017_Id),
UNIQUE KEY U_MCP017_Conexao (MCP017_MCP001_Id),
KEY I_MCP017_Status (MCP017_Status)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP016 (
MCP016_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP016_MCP001_Id BIGINT UNSIGNED NOT NULL,
MCP016_C021_Id INT UNSIGNED NOT NULL,
MCP016_Status ENUM('active','revoked') NOT NULL DEFAULT 'active',
MCP016_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP016_Data_Revogacao DATETIME NULL DEFAULT NULL,
PRIMARY KEY (MCP016_Id),
UNIQUE KEY U_MCP016_Conexao_Perfil (MCP016_MCP001_Id,MCP016_C021_Id),
KEY I_MCP016_Perfil_Status (MCP016_C021_Id,MCP016_Status)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP002 (
MCP002_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP002_MCP001_Id BIGINT UNSIGNED NOT NULL,
MCP002_Public_Id CHAR(16) NOT NULL,
MCP002_Secret_Hash BINARY(32) NOT NULL,
MCP002_Pepper_Version VARCHAR(20) NOT NULL,
MCP002_Status ENUM('active','revoked','expired') NOT NULL DEFAULT 'active',
MCP002_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MCP002_Data_Expiracao DATETIME NULL DEFAULT NULL,
MCP002_Data_Revogacao DATETIME NULL DEFAULT NULL,
MCP002_Motivo_Revogacao VARCHAR(255) NULL DEFAULT NULL,
MCP002_Substituida_Por_MCP002_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP002_Data_Ultimo_Uso DATETIME NULL DEFAULT NULL,
MCP002_IP_Ultimo_Uso VARCHAR(45) NULL DEFAULT NULL,
PRIMARY KEY (MCP002_Id),
UNIQUE KEY U_MCP002_Public_Id (MCP002_Public_Id),
KEY I_MCP002_Conexao_Status_Expiracao (MCP002_MCP001_Id, MCP002_Status, MCP002_Data_Expiracao),
KEY I_MCP002_Data_Ultimo_Uso (MCP002_Data_Ultimo_Uso)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS MCP003 (
MCP003_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
MCP003_Request_Id VARCHAR(40) NOT NULL,
MCP003_MCP001_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP003_MCP002_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP003_Auth_Method VARCHAR(20) NULL DEFAULT NULL,
MCP003_MCP004_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP003_MCP005_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP003_MCP007_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP003_C004_Id BIGINT UNSIGNED NULL DEFAULT NULL,
MCP003_C007_Id INT UNSIGNED NULL DEFAULT NULL,
MCP003_Host VARCHAR(255) NOT NULL,
MCP003_IP VARCHAR(45) NOT NULL,
MCP003_Evento VARCHAR(60) NOT NULL,
MCP003_Status_HTTP SMALLINT UNSIGNED NOT NULL,
MCP003_Codigo_Seguro VARCHAR(60) NULL DEFAULT NULL,
MCP003_Duracao_MS INT UNSIGNED NOT NULL,
MCP003_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (MCP003_Id),
KEY I_MCP003_Data_Criacao (MCP003_Data_Criacao),
KEY I_MCP003_Conexao_Data (MCP003_MCP001_Id, MCP003_Data_Criacao),
KEY I_MCP003_C007_Data (MCP003_C007_Id, MCP003_Data_Criacao),
KEY I_MCP003_Auth_Data (MCP003_Auth_Method,MCP003_Data_Criacao),
KEY I_MCP003_Grant_Data (MCP003_MCP005_Id,MCP003_Data_Criacao),
KEY I_MCP003_Request_Id (MCP003_Request_Id)
) DEFAULT CHARSET=utf8mb4 ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cClausula<hr>$cErro"; }

// criação da tabela A002 (auditoria da API REST)
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS A002 (
A002_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
A002_A001_Id BIGINT UNSIGNED NULL DEFAULT NULL,
A002_Request_Id VARCHAR(40) NOT NULL,
A002_Host VARCHAR(255) NOT NULL,
A002_IP VARCHAR(45) NOT NULL,
A002_Metodo_HTTP VARCHAR(10) NOT NULL,
A002_Path VARCHAR(255) NOT NULL,
A002_Tabela VARCHAR(64) NULL DEFAULT NULL,
A002_Operacao VARCHAR(30) NOT NULL,
A002_Query_String TEXT NULL,
A002_Payload_Mascarado MEDIUMTEXT NULL,
A002_Response_Resumo MEDIUMTEXT NULL,
A002_Status_Code INT NOT NULL,
A002_Codigo_Erro VARCHAR(60) NULL DEFAULT NULL,
A002_Mensagem_Erro VARCHAR(255) NULL DEFAULT NULL,
A002_Linhas_Afetadas INT NULL DEFAULT NULL,
A002_Duracao_MS INT NOT NULL,
A002_Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (A002_Id),
KEY I_A002_Request_Id (A002_Request_Id),
KEY I_A002_Data_Criacao (A002_Data_Criacao),
KEY I_A002_Tabela_Data (A002_Tabela, A002_Data_Criacao),
KEY I_A002_A001_Data (A002_A001_Id, A002_Data_Criacao)
)
DEFAULT CHARSET=utf8mb4
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela I001 (análises)
$cClausula  = <<<EOT
create table if not exists I001 (
I001_Id int(10) unsigned auto_increment,
I001_C007_Id int(10) unsigned,
I001_Topico text,
I001_Descricao_Pre_Analise text,
I001_Descricao_Analise text,
I001_Previsao_Analise_Horas int(2),
I001_Previsao_Analise_Data datetime,
I001_Inicio_Analise_Data date,
I001_Inicio_Analise_Hora time,
I001_Conclusao_Analise_Data date,
I001_Conclusao_Analise_Hora time,
I001_Prioridade int(3),
I001_Status char(1) default 'P',
I001_Tipo char(1),
PRIMARY KEY (I001_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();


if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela I002 (OS)
$cClausula  = <<<EOT
create table if not exists I002 (
I002_Id int(10) unsigned auto_increment,
I002_I001_Id int(10) unsigned,
I002_C007_Id int(10) unsigned,
I002_Topico text,
I002_Descricao_OS text,
I002_Previsao_OS_Horas int(2),
I002_Previsao_OS_Data datetime,
I002_Inicio_OS_Data date,
I002_Inicio_OS_Hora time,
I002_Conclusao_OS_Data date,
I002_Conclusao_OS_Hora time,
I002_Prioridade int(3),
I002_Status char(1) default 'P',
PRIMARY KEY (I002_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D106 (bens)
$cClausula  = <<<EOT
create table if not exists D106 (
D106_Id int(10) unsigned auto_increment,
D106_Descricao varchar(1000),
D106_Data_Compra date,
D106_Data_Validade date,
D106_Especificacoes varchar(1000),
D106_Flag_Status varchar(1),
PRIMARY KEY (D106_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D107 (Catálogos)
$cClausula  = <<<EOT
create table if not exists D107 (
D107_Id int(10) unsigned auto_increment,
D107_Descricao varchar(1000),
D107_Observacao text,
PRIMARY KEY (D107_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D108 (Ítens do Catálogo)
$cClausula  = <<<EOT
create table if not exists D108 (
D108_Id int(10) unsigned auto_increment,
D108_D001_Id int(10) unsigned,
D108_D107_Id int(10) unsigned,
D108_Codigo varchar(30),
D108_Descricao_Pt varchar(1000),
D108_Descricao_En varchar(1000),
D108_Observacao text,
PRIMARY KEY (D108_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D109 (Funcionários)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D109 (
  D109_Id INT UNSIGNED auto_increment ,
  D109_Nome VARCHAR(150) NULL ,
  D109_Funcao VARCHAR(150) NULL ,
  PRIMARY KEY (D109_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D110 (Competências)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D110 (
  D110_Id INT NOT NULL AUTO_INCREMENT ,
  D110_Descricao VARCHAR(500) NULL ,
  D110_Titulo VARCHAR(200) NULL ,
  PRIMARY KEY (D110_Id) )
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D111 (Materiais)

$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D111 (
  D111_Id INT NOT NULL AUTO_INCREMENT ,
  D111_D037_Id INT UNSIGNED NOT NULL ,
  D111_Descricao VARCHAR(500) NULL ,
  D111_Qte_Estoque INT NULL ,
  PRIMARY KEY (D111_Id) 
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D112 (Serviços)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D112 (
  D112_Id INT NOT NULL AUTO_INCREMENT ,
  D112_D110_Id INT NULL ,
  D112_Titulo VARCHAR(200) NULL ,
  D112_Descricao VARCHAR(500) NULL ,
  D112_Tempo_Estimado INT NULL ,
  D112_Valor_Unitario DECIMAL(14,2) NULL ,
  PRIMARY KEY (D112_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D113 (Processos)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D113 (
  D113_Id INT NOT NULL AUTO_INCREMENT ,
  D113_D112_Id INT NOT NULL ,
  D113_D110_Id INT ,
  D113_Titulo VARCHAR(200) NULL ,
  D113_Descricao VARCHAR(500) NULL ,
  PRIMARY KEY (D113_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D114 (n:m Serviços/Materiais)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D114 (
  D114_Id INT NOT NULL AUTO_INCREMENT ,
  D114_D009_Id INT UNSIGNED NOT NULL ,
  D114_D037_Id INT UNSIGNED NOT NULL ,
  D114_D111_Id INT NOT NULL ,
  D114_D112_Id INT NOT NULL ,
  D114_Quantidade INT,
  PRIMARY KEY (D114_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D115 (n:m de competencias/funcionarios)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D115 (
  D115_Id INT NOT NULL AUTO_INCREMENT ,
  D115_D110_Id INT NOT NULL ,
  D115_D109_Id INT UNSIGNED NOT NULL ,
  PRIMARY KEY (D115_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D116 (cadastro de veículos)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D116 (
  D116_Id INT NOT NULL AUTO_INCREMENT ,
  D116_D024_Id int(10) unsigned,
  D116_Placa varchar(10) DEFAULT NULL,
  D116_Modelo varchar(100) DEFAULT NULL,
  D116_Cor varchar(100) DEFAULT NULL,
  D116_Observacao text DEFAULT NULL,
  PRIMARY KEY (D116_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D117 (agenda de contatos)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS D117 (
  D117_Id INT NOT NULL AUTO_INCREMENT ,
  D117_D018_Id int(10) unsigned DEFAULT NULL,
  D117_D020_Id int(10) unsigned DEFAULT NULL,
  D117_C004_Id int(10) unsigned DEFAULT NULL,
  D117_Estado varchar(100) DEFAULT NULL,
  D117_Cidade varchar(100) DEFAULT NULL,
  D117_Nome varchar(100) DEFAULT NULL,  
  D117_Telefone varchar(30) DEFAULT NULL,
  D117_Endereco varchar(200) DEFAULT NULL,
  D117_Email varchar(100) DEFAULT NULL,
  D117_Cep varchar(30) DEFAULT NULL,
  D117_Observacao text DEFAULT NULL,
  PRIMARY KEY (D117_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D118 (portos)
$cClausula  = <<<EOT
create table if not exists D118 (
D118_Id int(10) unsigned auto_increment,
D118_Nome varchar(100),
D118_Estado varchar (100),
D118_Cidade varchar (100),
D118_D030_Id int(10) unsigned,
D118_C004_Id int(10) unsigned DEFAULT NULL,
PRIMARY KEY (D118_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D119 (despachantes)
$cClausula  = <<<EOT
create table if not exists D119 (
D119_Id int(10) unsigned auto_increment,
D119_Nome varchar(100),
D119_D020_Id int(10) unsigned,
D119_D018_Id int(10) unsigned,
D119_C004_Id int(10) unsigned DEFAULT NULL,
D119_Telefone varchar(30) DEFAULT NULL,
D119_Email varchar(100) DEFAULT NULL,
D119_Endereco varchar(200) DEFAULT NULL,
PRIMARY KEY (D119_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D120 (check list dos equipamentos)
$cClausula  = <<<EOT
create table if not exists D120 (
D120_Id int(10) unsigned auto_increment,
D120_D037_Id int(10) unsigned,
D120_Descricao text,
PRIMARY KEY (D120_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D121 (especificações dos equipamentos)
$cClausula  = <<<EOT
create table if not exists D121 (
D121_Id int(10) unsigned auto_increment,
D121_Tipo char(1),
D121_Descricao text,
PRIMARY KEY (D121_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D122 (prcessos da DI)
$cClausula  = <<<EOT
create table if not exists D122 (
D122_Id int(10) unsigned auto_increment,
D122_Descricao text,
PRIMARY KEY (D122_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D123 (documentos dos funcionarios)
$cClausula  = <<<EOT
CREATE TABLE if not exists D123 (
  D123_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D123_D109_Id int(10) unsigned DEFAULT NULL,
  D123_C007_Id int(10) unsigned DEFAULT NULL,
  D123_Nome_Arquivo varchar(200) DEFAULT NULL,
  D123_Descricao text,
  D123_Data_Hora_UpLoad datetime DEFAULT '0000-00-00 00:00:00',
  D123_Data_Validade date DEFAULT NULL,
  PRIMARY KEY (D123_Id)
) 
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D124 (certificados de inspeção)
$cClausula  = <<<EOT
create table if not exists D124 (
D124_Id int(10) unsigned auto_increment,
D124_D106_Id int(10) unsigned,
D124_Numero varchar(20),
D124_Emissao date,
D124_Observacao text,
PRIMARY KEY (D124_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D125 (certificados de conformidade)
$cClausula  = <<<EOT
create table if not exists D125 (
D125_Id int(10) unsigned auto_increment,
D125_D106_Id int(10) unsigned,
D125_Numero varchar(20),
D125_Emissao date,
D125_Validade date,
D125_Observacao text,
PRIMARY KEY (D125_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D126 (tipos de veículos)
$cClausula  = <<<EOT
create table if not exists D126 (
D126_Id int(10) unsigned auto_increment,
D126_Descricao varchar(40),
PRIMARY KEY (D126_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D127 (documentos dos veiculos)
$cClausula  = <<<EOT
CREATE TABLE if not exists D127 (
  D127_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D127_D116_Id int(10) unsigned DEFAULT NULL,  
  D127_Nome_Arquivo varchar(200) DEFAULT NULL,
  D127_Descricao text,
  D127_Data_Hora_UpLoad datetime DEFAULT '0000-00-00 00:00:00',
  D127_Data_Validade date DEFAULT NULL,
  PRIMARY KEY (D127_Id)
) 
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D128 (tipos de bens)
$cClausula  = <<<EOT
CREATE TABLE if not exists D128 (
  D128_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D128_Tipo varchar(50),
  D128_Descricao text,
  PRIMARY KEY (D128_Id)
) 
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D129 (especificações de bens)
$cClausula  = <<<EOT
create table if not exists D129 (
D129_Id int(10) unsigned auto_increment,
D129_D128_Id int(10) unsigned DEFAULT NULL,
D129_Descricao text,
PRIMARY KEY (D129_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D130 (configurações de documentos)
$cClausula  = <<<EOT
CREATE TABLE if not exists d130 (
  D130_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D130_Grupo varchar(50) NOT NULL DEFAULT 'GERAL',
  D130_Nome varchar(255) DEFAULT NULL,
  D130_Descricao varchar(255) DEFAULT NULL,
  D130_Conteudo longtext,
  PRIMARY KEY (D130_Id),
  UNIQUE KEY I_D130_Nome (D130_Nome)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D131 (especificações de veiculos)
$cClausula  = <<<EOT
create table if not exists D131 (
D131_Id int(10) unsigned auto_increment,
D131_Descricao text,
PRIMARY KEY (D131_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D132 (Relatorios BI salvos)
$cClausula  = <<<EOT
create table if not exists `D132` (
  `D132_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D132_Descricao` varchar(70) NOT NULL,
  `D132_Salvo` blob NOT NULL,
  `D132_Filtro` blob NOT NULL,
  PRIMARY KEY (`D132_Id`)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D133 (modelos de veiculos)
$cClausula  = <<<EOT
create table if not exists D133 (
D133_Id int(10) unsigned auto_increment,
D133_Modelo varchar(30),
PRIMARY KEY (D133_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D134 (cores)
$cClausula  = <<<EOT
create table if not exists D134 (
D134_Id int(10) unsigned auto_increment,
D134_Cor varchar(30),
PRIMARY KEY (D134_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D135 (n:m produtos e serviços)
$cClausula  = <<<EOT
create table if not exists D135 (
D135_Id int(10) unsigned auto_increment,
D135_D001_Id int(10) unsigned,
D135_D112_Id int(10) unsigned,
PRIMARY KEY (D135_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D136 (e-commerce do produto)
$cClausula  = <<<EOT
create table if not exists D136 (
D136_Id int(10) unsigned auto_increment,
D136_D001_Id int(10) unsigned,
D136_D049_Id int(10) unsigned,
D136_Status_Uso char(1),
D136_link_youtube text,
D136_Mais_Informacoes text,
D136_Detalhes_Tecnicos text,
D136_Itens_Inclusos text,
D136_Conexoes text,
D136_Garantia int(10),
D136_Unidade varchar(40),
D136_Qte_Embalagem decimal(10,2),
D136_Largura decimal(10,2),
D136_Profundidade decimal(10,2),
D136_Altura decimal(10,2),
D136_Peso decimal(10,3),
D136_Estoque_Virtual decimal(10,2),
D136_Estoque_Minimo decimal(10,2),
D136_Qte_Padrao_Carrinho decimal(10,2),
D136_Faturamento_Direto char(1),
D136_Disponibilidade char(1),
D136_Disponibilidade_Dias int(10),
D136_Permite_Venda_Site char(1),
D136_Preco_Sob_Consulta char(1),
D136_Preco_Fantasia decimal(10,2),
D136_Preco_Venda decimal(10,2),
D136_Desconto_Vista int(2),
D136_Frete_Gratis char(1),
D136_Frete_Gratis_Carrinho char(1),
D136_Bloquear_Transporte char(1),
D136_Pacote_Presente char(1),
D136_Pacote_Presente_Valor decimal(10,2),
D136_Atualizar_Produtos_Opcao char(1),
D136_AMEX_Vezes_Sem_Juros int(2),
D136_AMEX_Vezes_Com_Juros int(2),
D136_AMEX_Parcela_Juros char(1),
D136_AMEX_Taxa_Juros decimal(10,2),
D136_Boleto_Vezes_Sem_Juros int(2),
D136_Boleto_Vezes_Com_Juros int(2),
D136_Boleto_Parcela_Juros char(1),
D136_Boleto_Taxa_Juros decimal(10,2),
D136_Itau_Vezes_Sem_Juros int(2),
D136_Itau_Vezes_Com_Juros int(2),
D136_Itau_Parcela_Juros char(1),
D136_Itau_Taxa_Juros decimal(10,2),
D136_Mastercard_Vezes_Sem_Juros int(2),
D136_Mastercard_Vezes_Com_Juros int(2),
D136_Mastercard_Parcela_Juros char(1),
D136_Mastercard_Taxa_Juros decimal(10,2),
D136_Visa_Vezes_Sem_Juros int(2),
D136_Visa_Vezes_Com_Juros int(2),
D136_Visa_Parcela_Juros char(1),
D136_Visa_Taxa_Juros decimal(10,2),
D136_VEBradesco_Vezes_Sem_Juros int(2),
D136_VEBradesco_Vezes_Com_Juros int(2),
D136_VEBradesco_Parcela_Juros char(1),
D136_VEBradesco_Taxa_Juros decimal(10,2),
PRIMARY KEY (D136_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D137 (regras CFOP)
$cClausula  = <<<EOT
create table if not exists D137 (
D137_Id int(10) unsigned auto_increment,
D137_D006_Id int(10) unsigned,
D137_C004_Codigo_Regime_Tributario int(1),
D137_D024_Flag_Contribuinte int(5),
D137_Dentro_Fora_Estado char(1),
D137_Estados_Especificos varchar(100),
D137_UF_Tem_ST char(1),
D137_NCM_Tem_ST char(1),
D137_Pessoa_Fisica_Juridica char(2), 
D137_Produzido_Adquirido char(1),
PRIMARY KEY (D137_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D143 (Excessões de ICMS)
$cClausula  = <<<EOT
CREATE TABLE if not exists D143 (
  D143_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D143_D006_Id int(10) DEFAULT NULL,
  D143_C004_Codigo_Regime_Tributario int(1) DEFAULT NULL,
  D143_D024_Flag_Contribuinte int(5) DEFAULT NULL,
  D143_Dentro_Fora_Estado char(1) DEFAULT NULL,
  D143_Estados_Especificos varchar(100) DEFAULT NULL,
  D143_UF_Tem_ST char(1) DEFAULT NULL,
  D143_NCM_Tem_ST char(1) DEFAULT NULL,
  D143_Pessoa_Fisica_Juridica char(2) DEFAULT NULL,
  D143_Produzido_Adquirido char(1) DEFAULT NULL,
  D143_Flag_Inativar char(1) DEFAULT NULL,
  D143_Flag_Cabecalho_Item char(1) DEFAULT NULL,
  D143_D005_Id int(10) DEFAULT NULL,
  D143_C004_Id int(10) DEFAULT NULL,
  D143_Empresas varchar(20) DEFAULT NULL,
  D143_Flag_Revenda_Consumidor varchar(20) DEFAULT NULL,
  D143_Flag_UF_Convenio_NCM_ST char(1) DEFAULT NULL,
  D143_Origem_Mercadoria varchar(20) DEFAULT NULL,
  D143_Flag_Preferencial_Sobre_D024_D006_Id char(1) DEFAULT NULL,
  D143_Peso_Minimo decimal(10,3) DEFAULT NULL,
  D143_Peso_Maximo decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (D143_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T139 (email)
$cClausula  = <<<EOT
CREATE TABLE if not exists T139 (
T139_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
T139_Pasta enum('E','S') DEFAULT NULL,
T139_Uid int(11) DEFAULT NULL,
T139_Titulo varchar(255) DEFAULT NULL,
T139_De varchar(255) DEFAULT NULL,
T139_Para varchar(255) DEFAULT NULL,
T139_Data_Hora datetime DEFAULT NULL,
T139_Estrutura longtext,
T139_Mensagem_Corpo longtext NOT NULL,
PRIMARY KEY (T139_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB
AUTO_INCREMENT=15;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T140 (certificados de produtos)
$cClausula  = <<<EOT
create table if not exists T140 (
T140_Id int(10) unsigned auto_increment,
T140_D024_Id int(10) unsigned,
T140_D001_Id int(10) unsigned,
T140_Data_Emissao date,
T140_Data_Validade date,
T140_Tipo_Certificado varchar(50),
T140_Certificadora varchar(100),
T140_Nome_Arquivo varchar(100),
PRIMARY KEY (T140_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T141 (contratos de locação de equipamentos)
$cClausula  = <<<EOT
create table if not exists T141 (
T141_Id int(10) unsigned auto_increment,
T141_D106_Id int(10) unsigned,
T141_C004_Id int(10) unsigned,
T141_D024_Id int(10) unsigned,
T141_Data_Inicio date,
T141_Data_Validade date,
T141_Valor_Mensal decimal(10,2),
T141_Dia_Vencimento int(2),
T141_Flag_Status varchar(1),
PRIMARY KEY (T141_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T142 (manutenção de equipamentos)
$cClausula  = <<<EOT
create table if not exists T142 (
T142_Id int(10) unsigned auto_increment,
T142_D106_Id int(10) unsigned,
T142_C004_Id int(10) unsigned,
T142_Data_Manutencao date,
T142_Descricao varchar(1000),
T142_Valor decimal(10,2),
PRIMARY KEY (T142_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T143 (logística de equipamentos)
$cClausula  = <<<EOT
create table if not exists T143 (
T143_Id int(10) unsigned auto_increment,
T143_D106_Id int(10) unsigned,
T143_T141_Id int(10) unsigned,
T143_D020_Id int(10) unsigned,
T143_D018_Id int(10) unsigned,
T143_D030_Id int(10) unsigned,
T143_C004_Id int(10) unsigned,
T143_Data_Transferencia date,
T143_Data_Prev_Retorno date,
T143_Observacao varchar(500),
PRIMARY KEY (T143_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T144 (links)
$cClausula  = <<<EOT
CREATE TABLE if not exists t144 (
  T144_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T144_D001_Id int(10) UNSIGNED NULL,
  T144_Url varchar(255) DEFAULT NULL,
  T144_Data_Cadastro date DEFAULT NULL,
  T144_Ultimo_Acesso datetime DEFAULT NULL,
  T144_Versao_Cache datetime DEFAULT NULL,
  T144_Cache longtext CHARACTER SET utf8,
  PRIMARY KEY (T144_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB
AUTO_INCREMENT=10;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T145 (Cabeçalho de Orçamento de Serviço)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T145 (
  T145_Id INT NOT NULL AUTO_INCREMENT ,
  T145_T003_Id INT UNSIGNED NOT NULL ,
  T145_D112_Id INT NOT NULL ,
  T145_D110_Id INT NULL ,
  T145_Titulo VARCHAR(200) NULL ,
  T145_Descricao VARCHAR(500) NULL ,
  T145_Tempo_Estimado INT NULL ,
  T145_Quantidade INT NULL ,
  T145_Quantidade_Confirmada INT NULL ,
  T145_Valor_Unitario DECIMAL(14,2) NULL ,
  T145_Valor_Total DECIMAL(14,2) NULL ,
  T145_Hora_Inicio DATETIME NULL ,
  T145_Hora_Fim DATETIME NULL ,
  PRIMARY KEY (T145_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T146 (Itens de Orçamento de Serviço)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T146 (
  T146_Id INT NOT NULL AUTO_INCREMENT ,
  T146_T145_Id INT NOT NULL ,
  T146_D110_Id INT ,
  T146_Titulo VARCHAR(200) NULL ,
  T146_Descricao VARCHAR(500) NULL ,
  PRIMARY KEY (T146_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T147 (Cabeçalho de Pedido de Serviço)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T147 (
  T147_Id INT NOT NULL AUTO_INCREMENT ,
  T147_T005_Id INT UNSIGNED NOT NULL ,
  T147_T145_Id INT ,
  T147_D110_Id INT ,
  T147_D109_Id INT UNSIGNED,
  T147_D112_Id INT ,
  T147_Titulo VARCHAR(200) NULL ,
  T147_Descricao VARCHAR(500) NULL ,
  T147_Tempo_Estimado INT NULL ,
  T147_Quantidade INT NULL ,
  T147_Valor_Unitario DECIMAL(14,2) NULL ,
  T147_Valor_Total DECIMAL(14,2) NULL ,
  T147_Hora_Inicio DATETIME NULL ,
  T147_Hora_Inicio_Atual DATETIME NULL ,
  T147_Hora_Fim DATETIME NULL ,
  T147_Status CHAR(1) NULL ,
  T147_Pausado CHAR(1) NULL ,
  PRIMARY KEY (T147_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T203 (Cabeçalho de Nota Fiscal de Serviço)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T203 (
    `T203_Id` int(11) NOT NULL AUTO_INCREMENT,
    `T203_T007_Id` int(10) unsigned NOT NULL,
    `T203_T147_Id` int(11) DEFAULT NULL,
    `T203_D110_Id` int(11) DEFAULT NULL,
    `T203_D109_Id` int(10) unsigned DEFAULT NULL,
    `T203_D112_Id` int(11) DEFAULT NULL,
    `T203_Titulo` varchar(200) DEFAULT NULL,
    `T203_Descricao` varchar(500) DEFAULT NULL,
    `T203_Tempo_Estimado` time DEFAULT NULL,
    `T203_Quantidade` int(11) DEFAULT NULL,
    `T203_Valor_Unitario` decimal(14,2) DEFAULT NULL,
    `T203_Valor_Total` decimal(14,2) DEFAULT NULL,
    `T203_Hora_Inicio` datetime DEFAULT NULL,
    `T203_Hora_Reinicio` datetime DEFAULT NULL,
    `T203_Hora_Fim` datetime DEFAULT NULL,
    `T203_Status` char(1) DEFAULT NULL,
    `T203_Pausado` char(1) DEFAULT NULL,
    `T203_Hora_Pausa` datetime DEFAULT NULL,
    `T203_Prioridade` int(5) unsigned DEFAULT '99999',
    `T203_D116_Id` int(10) unsigned DEFAULT NULL,
    `T203_Flag_Cancelado` char(1) DEFAULT 'N',
    `T203_D024_Id` int(10) unsigned DEFAULT NULL,
    `T203_Flag_Novo` char(1) DEFAULT 'S',
    `T203_Flag_Conferido` char(1) DEFAULT 'N',
    `T203_T008_Id` int(10) unsigned DEFAULT NULL,
    `T203_D109_Id_Conf` int(10) unsigned DEFAULT NULL,
    `T203_C007_Id_Conf` int(10) unsigned DEFAULT NULL,
    `T203_Observacao` varchar(500) DEFAULT NULL,
    `T203_Flag_Dividir` char(1) DEFAULT 'N',
    `T203_Flag_Comissao` char(1) DEFAULT 'N',
    PRIMARY KEY (`T203_Id`)
)

DEFAULT CHARSET=utf8
ENGINE = InnoDB
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T202 (Agregados do produto do orçamento)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T202 (
  T202_Id INT NOT NULL AUTO_INCREMENT ,
  T202_T004_Id INT NULL ,
  T202_Descricao VARCHAR(50) NULL ,
  T202_Valor_Unitario DECIMAL(14,2) NULL ,
  PRIMARY KEY (T202_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T204 (Agregados do produto do pedido)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T204 (
  T204_Id INT NOT NULL AUTO_INCREMENT ,
  T204_T006_Id INT NULL ,
  T204_Descricao VARCHAR(50) NULL ,
  T204_Valor_Unitario DECIMAL(14,2) NULL ,
  T204_Observacao text NULL,
  PRIMARY KEY (T204_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T148 (Itens de Pedido de Serviço)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T148 (
  T148_Id INT NOT NULL AUTO_INCREMENT ,
  T148_T147_Id INT NOT NULL ,
  T148_D110_Id INT ,
  T148_Titulo VARCHAR(200) NULL ,
  T148_Descricao VARCHAR(500) NULL ,
  PRIMARY KEY (T148_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T149 (Materiais de Pedido de Serviço n:m)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T149 (
  T149_Id INT NOT NULL AUTO_INCREMENT ,
  T149_D111_Id INT NOT NULL ,
  T149_D037_Id INT UNSIGNED NOT NULL ,
  T149_T147_Id INT NOT NULL ,
  T149_Quantidade INT NULL ,
  PRIMARY KEY (T149_Id)
)
ENGINE = InnoDB
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T152 (Materiais de Orçamento de Serviço n:m)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T152 (
  T152_Id INT NOT NULL AUTO_INCREMENT ,
  T152_D037_Id INT UNSIGNED NOT NULL ,
  T152_D111_Id INT NOT NULL ,
  T152_T145_Id INT NOT NULL ,
  T152_Quantidade INT,
  PRIMARY KEY (T152_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T153 (Lista de NCM)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T153 (
  T153_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T153_NCM varchar(50) DEFAULT NULL,
  T153_Descricao text,
  T153_Aliquota varchar(50) DEFAULT NULL,
  PRIMARY KEY (T153_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// inserção dos registros na tabela T153
$cSql = mysql_query("SELECT COUNT(*) FROM T153");
$res = mysql_fetch_array($cSql);
if ($res[0] == 0){
// executa o script com o dump
}

// criação da tabela T154 (Pagamentos nota recebida)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T154 (
  T154_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T154_T013_Id int(11),
  T154_D027_Id int(11),
  T154_Numero_Parcela int(3),
  T154_Prazos_Dias int(5),
  T154_Data_Vencimento date,
  T154_Valor_Parcela decimal(12,2),
  T154_D031_Id int(11),
  PRIMARY KEY (T154_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T155 (Documentos da DI - Declaracao de Importacao)
$cClausula  = <<<EOT
create table if not exists T155 (
	T155_Id int(10) unsigned auto_increment,
	T155_T075_Id int(10) unsigned,
	T155_C007_Id int(10) unsigned,
	T155_Nome_Arquivo varchar(200),
	T155_Descricao text,
	T155_Data_Hora_Upload datetime,
	PRIMARY KEY (T155_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T156 (Recibo de Locação)
$cClausula  = <<<EOT
create table if not exists T156 (
	T156_Id int(10) unsigned auto_increment,
	T156_T141_Id int(10) unsigned,
	T156_Emissao date,
	T156_Ordem_Compra varchar(30),
	T156_Comprador varchar(40),
	PRIMARY KEY (T156_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T157 (Itens do Recibo de Locação)
$cClausula  = <<<EOT
create table if not exists T157 (
	T157_Id int(10) unsigned auto_increment,
	T157_T156_Id int(10) unsigned,
	T157_Descricao text,
	T157_Quantidade int(10),
	T157_Valor_Unitario DECIMAL(14,2),
	PRIMARY KEY (T157_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T158 (Itens do Contrato de Locação)
$cClausula  = <<<EOT
create table if not exists T158 (
	T158_Id int(10) unsigned auto_increment,
	T158_T141_Id int(10) unsigned,
	T158_D106_Id int(10) unsigned,
	T158_Valor_Mensal DECIMAL(14,2),
	PRIMARY KEY (T158_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T159 (n:m equipamentos com checklist de equipamentos)
$cClausula  = <<<EOT
create table if not exists T159 (
	T159_Id int(10) unsigned auto_increment,
	T159_D106_Id int(10) unsigned,
	T159_D120_Id int(10) unsigned,
	T159_Data date,
	T159_Quantidade DECIMAL(14,2),
	T159_Flag_Ok char(1) default 'N',
	PRIMARY KEY (T159_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T160 (n:m equipamentos com especificacoes de equipamentos)
$cClausula  = <<<EOT
create table if not exists T160 (
	T160_Id int(10) unsigned auto_increment,
	T160_D106_Id int(10) unsigned,
	T160_D121_Id int(10) unsigned,	
	T160_Valor DECIMAL(14,2),
	PRIMARY KEY (T160_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T161 (n:m DI com processos da DI)
$cClausula  = <<<EOT
create table if not exists T161 (
	T161_Id int(10) unsigned auto_increment,
	T161_D122_Id int(10) unsigned,
	T161_T075_Id int(10) unsigned,
	T161_Flag_Ok char(1) default 'N',	
	T161_Data date,
	PRIMARY KEY (T161_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T162 (auditoria - logs)
$cClausula  = <<<EOT
CREATE TABLE if not exists T162 (
  T162_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T162_C007_Id int(11) DEFAULT NULL,
  T162_Acao enum('I','U','D','S'),
  T162_Query blob,
  T162_DataHora datetime,
  T162_Tabela varchar(100) DEFAULT NULL,
  T162_TempoExecucao float DEFAULT NULL,
  PRIMARY KEY (T162_Id)
)
DEFAULT CHARSET=utf8
ENGINE=MYISAM;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T163 (n:m bens com especificacoes de bens)
$cClausula  = <<<EOT
create table if not exists T163 (
	T163_Id int(10) unsigned auto_increment,
	T163_D106_Id int(10) unsigned,
	T163_D129_Id int(10) unsigned,	
	T163_Valor varchar(50),
	PRIMARY KEY (T163_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T164 (n:m certificado de inspeção com acessórios (bens)) 
$cClausula  = <<<EOT
create table if not exists T164 (
	T164_Id int(10) unsigned auto_increment,
	T164_D124_Id int(10) unsigned,
	T164_D106_Id int(10) unsigned,		
	PRIMARY KEY (T164_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T165 (n:m veiculos com especificacoes de veiculos)
$cClausula  = <<<EOT
create table if not exists T165 (
	T165_Id int(10) unsigned auto_increment,
	T165_D116_Id int(10) unsigned,
	T165_D131_Id int(10) unsigned,	
	T165_Valor varchar(50),
	PRIMARY KEY (T165_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T166 (histórico de reparcelamentos contas a receber)
$cClausula  = <<<EOT
create table if not exists T166 (
	T166_Id int(10) unsigned auto_increment,	
	T166_D024_Id int(10) unsigned,
	T166_C004_Id int(10) unsigned,
	T166_Data_Emissao date,	
	T166_Valor_Total DECIMAL(14,2),
	T166_Titulos_Geradores text,
	T166_Titulos_Gerados text,
	PRIMARY KEY (T166_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T167 (Documentos de Pedidos)
$cClausula  = <<<EOT
create table if not exists T167 (
	T167_Id int(10) unsigned auto_increment,
	T167_T005_Id int(10) unsigned,
	T167_C007_Id int(10) unsigned,
	T167_Nome_Arquivo varchar(200),
	T167_Descricao text,
	T167_Data_Hora_Upload datetime,
	PRIMARY KEY (T167_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T168 (Despesas da DI - Declaracao de Importacao)
$cClausula  = <<<EOT
create table if not exists T168 (
	T168_Id int(10) unsigned auto_increment,
	T168_T075_Id int(10) unsigned,
	T168_Data date,
	T168_Data_Pagamento date,
	T168_Descricao text,
	T168_Valor DECIMAL(14,2),
	T168_Flag_ME char(1) DEFAULT 'N',	
	PRIMARY KEY (T168_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T169 (Histórico do funcionário/serviço)
$cClausula  = <<<EOT
create table if not exists T169 (
	T169_Id int(10) unsigned auto_increment,
	T169_D109_Id int(10) unsigned DEFAULT NULL,
	T169_T147_Id int(10) unsigned DEFAULT NULL,
	T169_Data datetime,
	T169_Descricao text,
	PRIMARY KEY (T169_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T170 (Histórico do serviço **** obsoleta)
$cClausula  = <<<EOT
create table if not exists T170 (
	T170_Id int(10) unsigned auto_increment,
	T170_D109_Id int(10) unsigned DEFAULT NULL,
	T170_T147_Id int(10) unsigned DEFAULT NULL,
	T170_Data datetime,
	T170_Descricao text,
	PRIMARY KEY (T170_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T171 (veiculo e hora combinada de entrega do serviço)
$cClausula  = <<<EOT
create table if not exists T171 (
	T171_Id int(10) unsigned auto_increment,
	T171_D116_Id int(10) unsigned DEFAULT NULL,
	T171_T005_Id int(10) unsigned DEFAULT NULL,
	T171_T147_Id int(10) unsigned DEFAULT NULL,
	T171_Data_combinada date,
	T171_Hora_combinada time,
	T171_Observacao text,
	PRIMARY KEY (T171_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T172 (fotos de produtos)
$cClausula  = <<<EOT
create table if not exists T172(
T172_Id int(10) unsigned auto_increment,
T172_D001_Id int(10) unsigned,
T172_Nome_Arquivo varchar(100),
PRIMARY KEY (T172_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T173 (Empresas da Cotação)
$cClausula  = <<<EOT
create table if not exists `T173` (
  `T173_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T173_T010_Id` int(10) unsigned NOT NULL,
  `T173_D024_Id` int(10) unsigned NOT NULL,
  `T173_Frete` DECIMAL(14,5) DEFAULT NULL,
  `T173_Desconto` DECIMAL(14,5) DEFAULT NULL,
  `T173_ICMS` DECIMAL(14,5) DEFAULT NULL,
  PRIMARY KEY (T173_Id)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T174 (Ligação: Cotação/Empresa/Produto)
$cClausula  = <<<EOT
create table if not exists `T174` (
  `T174_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T174_T010_Id` int(10) unsigned NOT NULL,
  `T174_T173_Id` int(10) unsigned NOT NULL,
  `T174_T059_Id` int(10) unsigned NOT NULL,
  `T174_Valor_Custo_Unitario` DECIMAL(14,5) DEFAULT NULL,
  `T174_Flag_Gerar_OC` VARCHAR(1) DEFAULT NULL,
  `T174_IPI` DECIMAL(14,5) DEFAULT NULL,
  PRIMARY KEY (T174_Id)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T175 (empréstimos de produtos)
$cClausula  = <<<EOT
create table if not exists T175(
T175_Id int(10) unsigned auto_increment,
T175_D009_Id int(10) unsigned,
T175_C007_Id int(10) unsigned,
T175_D109_Id int(10) unsigned,
T175_Data_Saida datetime,
T175_Data_Retorno datetime,
T175_Quantidade int(10),
T175_Flag_Status char(1) default 'A',
T175_Observacao text,
PRIMARY KEY (T175_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T176 (Promissórias)
$cClausula  = <<<EOT
create table if not exists T175(
T176_Id int(10) unsigned auto_increment,
T176_C004_Id int(10) unsigned,
T176_D024_Id int(10) unsigned,
T176_Data_Emissao date,
T176_Numero varchar(10),
T176_Data_Vencimento date,
T176_Data_Pagamento date,
T176_Valor_Total DECIMAL(10,2),
T176_Percentual_Juros_Dia DECIMAL(10,2),
T176_Percentual_Multa DECIMAL(10,2),
T176_Flag_Cancelada char(1) default 'N',
T176_Data_Envio_Email datetime,
PRIMARY KEY (T176_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T177 (estatisticas de produtos)
$cClausula  = <<<EOT
create table if not exists T177(
  `T177_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T177_D009_Id` int(10) unsigned DEFAULT NULL,
  `T177_Flag_Operacao` char(1) DEFAULT 'S',
  `T177_Data` date DEFAULT NULL,
  `T177_Quantidade` int(10) DEFAULT NULL,
  `T177_Operacoes` int(10) DEFAULT NULL,
  `T177_Clientes` text,
  PRIMARY KEY (`T177_Id`),
  KEY `I_T177_D009_Id` (`T177_D009_Id`),
  KEY `I_T177_Data` (`T177_Data`)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T187 (estatisticas de produtos)
$cClausula  = <<<EOT
create table if not exists T187(
  `T187_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T187_D009_Id` int(10) unsigned DEFAULT NULL,
  `T187_Nr_Meses` int(2) unsigned,
  `T187_Nr_Meses_Count` int(2) unsigned,
  `T187_Quantidade` int(10) DEFAULT NULL,
  `T187_Quantidade_Media` decimal(10,2) DEFAULT NULL,
  `T187_Operacoes` int(10) DEFAULT NULL,
  `T187_Operacoes_Media` decimal(10,2) DEFAULT NULL,
  `T187_Clientes_Total` int(10) DEFAULT NULL,
  `T187_Custo_Media` int(10) DEFAULT NULL,
  `T187_Estoque_Media` int(10) DEFAULT NULL,
  PRIMARY KEY (`T187_Id`),
  KEY `I_T187_D009_Id` (`T187_D009_Id`),
  KEY `I_T187_Nr_Meses` (`T187_Nr_Meses`),
  KEY `I_T187_Quantidade` (`T187_Quantidade`),
  KEY `I_T187_Quantidade_Media` (`T187_Quantidade_Media`),
  KEY `I_T187_Operacoes` (`T187_Operacoes`),
  KEY `I_T187_Operacoes_Media` (`T187_Operacoes_Media`),
  KEY `I_T187_Clientes_Total` (`T187_Clientes_Total`),
  KEY `I_T187_Custo_Media` (`T187_Custo_Media`),
  KEY `I_T187_Estoque_Media` (`T187_Estoque_Media`)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T188 (Documentos de Orçamentos)
$cClausula  = <<<EOT
create table if not exists T188 (
	T188_Id int(10) unsigned auto_increment,
	T188_T003_Id int(10) unsigned,
	T188_C007_Id int(10) unsigned,
	T188_Nome_Arquivo varchar(200),
	T188_Descricao text,
	T188_Data_Hora_Upload datetime,
	PRIMARY KEY (T188_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T178 (Histórico do pedido)
$cClausula  = <<<EOT
create table if not exists T178 (
	T178_Id int(10) unsigned auto_increment,
	T178_T005_Id int(10) unsigned DEFAULT NULL,
	T178_C007_Id int(10) unsigned DEFAULT NULL,
	T178_Data datetime,
	T178_Descricao text,
	T178_Observacao text,
	PRIMARY KEY (T178_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T179 (entregas internas de produtos)
$cClausula  = <<<EOT
create table if not exists T179 (
	T179_Id int(10) unsigned auto_increment,
	T179_T006_Id int(10) unsigned DEFAULT NULL,
	T179_D109_Id_Recebeu int(10) unsigned DEFAULT NULL,
	T179_C007_Id_Entregou int(10) unsigned DEFAULT NULL,
	T179_Data datetime,
	T179_Quantidade_Entregue int(4),
	PRIMARY KEY (T179_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T180 (reparcelamentos (contas a receber))
$cClausula  = <<<EOT
create table if not exists T180 (
    T180_Id int(10) unsigned auto_increment,
    T180_C007_Id int(10) unsigned DEFAULT NULL,
    T180_D024_Id int(10) unsigned DEFAULT NULL,
    T180_D027_Id int(10) unsigned DEFAULT NULL,
    T180_Numero_Parcela int(3),
    T180_Prazo_Dias int(5),
    T180_Data_Vencimento date,
    T180_Valor_Parcela decimal(12,2),
    T180_Banco_Cheque varchar(50),
    T180_Agencia_Cheque varchar(50),
    T180_Conta_Corrente_Cheque varchar(50),
    T180_Nome_Cheque varchar(50),
    T180_Data_Cheque date,
    T180_Cidade_Cheque varchar(50),
    PRIMARY KEY (T180_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T197 (reparcelamentos (contas a pagar))
$cClausula  = <<<EOT
create table if not exists T197 (
    T197_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
    T197_C007_Id int(10) unsigned DEFAULT NULL,
    T197_D024_Id int(10) unsigned DEFAULT NULL,
    T197_D031_Id int(10) unsigned DEFAULT NULL,
    T197_Numero_Parcela int(3) DEFAULT NULL,
    T197_Prazos_Dias int(5) DEFAULT NULL,
    T197_Data_Vencimento date DEFAULT NULL,
    T197_Valor_Parcela decimal(12,2) DEFAULT NULL,
    T197_Banco_Cheque varchar(50) DEFAULT NULL,
    T197_Agencia_Cheque varchar(50) DEFAULT NULL,
    T197_Conta_Corrente_Cheque varchar(50) DEFAULT NULL,
    T197_Nome_Cheque varchar(50) DEFAULT NULL,
    T197_Data_Cheque date DEFAULT NULL,
    T197_Cidade_Cheque varchar(50) DEFAULT NULL,
    T197_Numero_Documento varchar(255) DEFAULT NULL,
    T197_Data_Emissao date DEFAULT NULL,
    T197_Numero_Duplicata varchar(255) DEFAULT NULL,
    PRIMARY KEY (T197_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T198 (Geração de orçamento via tab "católogos" do CRM)
$cClausula  = <<<EOT
create table if not exists T198 (
	T198_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
	T198_D009_Id int(10) unsigned DEFAULT NULL,
	T198_C007_Id int(10) unsigned DEFAULT NULL,
	T198_D024_Id int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (T198_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela C032 para a Hardness e os parceiros cadastrarem avisos
$cClausula  = <<<EOT
create table if not exists C032 (
  C032_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  C032_Aviso text,
  C032_Imagem varchar(100),
  C032_Flag_Ativo char(1),
  C032_C004_Id int(10) unsigned,
  PRIMARY KEY (C032_Id),
  KEY I_C032_C004_Id (C032_C004_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
// FIM

// tabela perfil para mostrar os avisos no dashboard
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS C036 
(
  C036_Id int(10) unsigned auto_increment,
  C036_C032_Id int(10) unsigned,
  C036_C021_Id int(10) unsigned,
  C036_C004_Id int(10) unsigned,
  C036_C007_Id int(10) unsigned,
  C036_Data_Inicial date,
  C036_Data_Final date,
  PRIMARY KEY (C036_Id),
  KEY IDX_C036_C032_Id (C036_C032_Id),
  KEY IDX_C036_C021_Id (C036_C021_Id),
  KEY IDX_C036_C004_Id (C036_C004_Id),
  KEY IDX_C036_C007_Id (C036_C007_Id),
  KEY IDX_C036_Data_Inicial (C036_Data_Inicial),
  KEY IDX_C036_Data_Final (C036_Data_Final)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// tabela para gravar o código PHP dos processos do Integrador API
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS C037
(
  C037_Id int(10) unsigned auto_increment,
  C037_C033_Id int(10) unsigned,
  C037_Nome varchar(100) not null,
  C037_Codigo text,
  PRIMARY KEY (C037_Id),
  KEY IDX_C037_C033_Id (C037_C033_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// NF-e RTC - T321 - Eventos da NF-e
$cClausula  = <<<EOT
create table if not exists T321 (
    T321_Id                              INT(10) UNSIGNED AUTO_INCREMENT,
    T321_T007_Id                         INT(10) UNSIGNED NOT NULL,
    T321_C007_Id                         INT(10) UNSIGNED DEFAULT NULL,
    T321_Tipo_Evento                     varchar(6) NOT NULL COMMENT 'Código do evento NF-e (tpEvento)',
    T321_Sequencial_Evento               INT(2) UNSIGNED NOT NULL COMMENT 'Sequencial do evento para a NF-e e tipo (nSeqEvento)',
    T321_Status                          varchar(30) NOT NULL DEFAULT 'RASCUNHO' COMMENT 'RASCUNHO, PENDENTE, AUTORIZADO, REJEITADO, ERRO_COMUNICACAO ou CANCELADO',
    T321_Ambiente                        INT(1) UNSIGNED NOT NULL COMMENT '1-Produção; 2-Homologação',
    T321_Chave_NFe                       char(44) NOT NULL,
    T321_Data_Hora_Inclusao              datetime NOT NULL,
    T321_Data_Hora_Ultima_Tentativa      datetime DEFAULT NULL,
    T321_Quantidade_Tentativas           INT(10) UNSIGNED NOT NULL DEFAULT 0,
    T321_Protocolo                       varchar(20) DEFAULT NULL COMMENT 'Protocolo de registro do evento (nProt)',
    T321_Data_Hora_Registro              datetime DEFAULT NULL COMMENT 'Data e hora do registro do evento (dhRegEvento)',
    T321_Codigo_Retorno                  varchar(4) DEFAULT NULL COMMENT 'Código de retorno SEFAZ (cStat)',
    T321_Motivo_Retorno                  text DEFAULT NULL COMMENT 'Motivo do retorno SEFAZ (xMotivo)',
    T321_XML_Envio                       mediumtext DEFAULT NULL COMMENT 'XML da última tentativa de envio',
    T321_XML_Retorno                     mediumtext DEFAULT NULL COMMENT 'XML de retorno da última tentativa',
    T321_XML_Processado                  mediumtext DEFAULT NULL COMMENT 'XML processado do evento autorizado (procEventoNFe)',
    T321_T321_Evento_Original_Id         INT(10) UNSIGNED DEFAULT NULL COMMENT 'Evento original, utilizado pelo cancelamento de evento 110001',
    T321_Data_Hora_Cancelamento          datetime DEFAULT NULL,
    PRIMARY KEY (T321_Id),
    UNIQUE KEY UK_T321_Chave_Tipo_Sequencial (T321_Chave_NFe, T321_Tipo_Evento, T321_Sequencial_Evento),
    KEY IDX_T321_T007_Tipo_Status (T321_T007_Id, T321_Tipo_Evento, T321_Status),
    KEY IDX_T321_Evento_Original (T321_T321_Evento_Original_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// NF-e RTC - T322 - Itens do evento 112140 - Fornecimento não realizado com pagamento antecipado
$cClausula  = <<<EOT
create table if not exists T322 (
    T322_Id                              INT(10) UNSIGNED AUTO_INCREMENT,
    T322_T321_Id                         INT(10) UNSIGNED NOT NULL,
    T322_T008_Id_Origem                  INT(10) UNSIGNED DEFAULT NULL COMMENT 'Item original da NF-e, para rastreabilidade',
    T322_NItem                           INT(3) UNSIGNED NOT NULL COMMENT 'Número do item da NF-e autorizada (nItem)',
    T322_Quantidade_Nao_Fornecida        decimal(11,4) NOT NULL COMMENT 'Quantidade não fornecida (qNaoFornecida)',
    T322_Unidade_Nao_Fornecida           varchar(6) NOT NULL COMMENT 'Unidade da quantidade não fornecida (uNaoFornecida)',
    T322_Valor_IBS                       decimal(13,2) NOT NULL COMMENT 'Valor do IBS correspondente à quantidade não fornecida (vIBS)',
    T322_Valor_CBS                       decimal(13,2) NOT NULL COMMENT 'Valor da CBS correspondente à quantidade não fornecida (vCBS)',
    PRIMARY KEY (T322_Id),
    UNIQUE KEY UK_T322_Evento_NItem (T322_T321_Id, T322_NItem),
    KEY IDX_T322_T321_Id (T322_T321_Id),
    KEY IDX_T322_T008_Id_Origem (T322_T008_Id_Origem)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}
// FIM

// tabela para gravar o código PHP dos callbacks do Integrador API
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS C038
(
  C038_Id int(10) unsigned auto_increment,
  C038_C033_Id int(10) unsigned,
  C038_Nome varchar(100) not null,
  C038_Codigo text,
  PRIMARY KEY (C038_Id),
  KEY IDX_C038_C033_Id (C038_C033_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}
// FIM

$cClausula  = <<<EOT
create table if not exists `T181` (
  `T181_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T181_Codigo` varchar(255) NOT NULL,
  `T181_Status` int(10) NOT NULL,
  `T181_Registrado` int(10) unsigned NOT NULL,
  PRIMARY KEY (`T181_Id`),
  KEY `I_T181_Codigo` (`T181_Codigo`)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T182 (itens em conserto/garantia)
$cClausula  = <<<EOT
create table if not exists T182 (
	T182_Id int(10) unsigned auto_increment,
	T182_C007_Id int(10) unsigned DEFAULT NULL,
	T182_D009_Id int(10) unsigned DEFAULT NULL,
	T182_T013_Id int(10) unsigned DEFAULT NULL,
	T182_Quantidade int(3),
	T182_Preco decimal(12,2),
	T182_Data date,
	T182_Hora time,
	T182_Observacao text,
	PRIMARY KEY (T182_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T183 (Cabeçalho de remessa para conserto/garantia )
$cClausula  = <<<EOT
create table if not exists T183 (
	T183_Id int(10) unsigned auto_increment,
	T183_D024_Id int(10) unsigned DEFAULT NULL,
	T183_Numero int(10),
	T183_Data date,
	PRIMARY KEY (T183_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T184 (itens de remessa para conserto/garantia )
$cClausula  = <<<EOT
create table if not exists T184 (
	T184_Id int(10) unsigned auto_increment,
	T184_C007_Id int(10) unsigned DEFAULT NULL,
	T184_D009_Id int(10) unsigned DEFAULT NULL,
	T184_Quantidade int(3),
	T184_Preco decimal(12,2),
	T184_Observacao text,
	PRIMARY KEY (T184_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}


// criação da tabela C027 (Itens de permissões (Documentos de Pedidos))
$cClausula  = <<<EOT
create table if not exists C027 (
	C027_Id int(10) unsigned auto_increment,
	C027_Descricao_Acesso varchar(150),
	PRIMARY KEY (C027_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}
$cClausula = "insert into c027 (`C027_Id`,`C027_Descricao_Acesso`) values (1,'Resumo diário de Vendas (por vendedor)'),(2,'Resumo diário Financeiro'),(3,'Gráfico movimentação Fluxo de Caixa mensal');";
$cSql = mysql_query($cClausula);

// criação da tabela C028 (Usuário/Perfis/Itens de permissões (de resumos/relatórios))
$cClausula  = <<<EOT
create table if not exists C028 (
	C028_Id int(10) unsigned auto_increment,
	C028_C021_Id int(10) unsigned,
	C028_C027_Id int(10) unsigned,
	C028_Id_Perfil int(10) unsigned,
	C028_Acesso_Liberado enum('S','N') NOT NULL DEFAULT 'N',
	PRIMARY KEY (C028_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T185 ( Separação Itens do Pedido (separação por locação) )
$cClausula  = <<<EOT
create table if not exists T185 (
	T185_Id int(10) unsigned auto_increment,
	T185_T006_Id int(10) unsigned DEFAULT NULL,
	T185_T066_Id int(10) unsigned DEFAULT NULL,
	T185_C007_Id_Separou int(10) unsigned DEFAULT NULL,
	T185_Qtd_Para_Separar decimal(12,5),
	T185_Qtd_Separado decimal(12,5),
	PRIMARY KEY (T185_Id),
	KEY I_T185_T006_Id (T185_T006_Id),
	KEY I_T185_T066_Id (T185_T066_Id),
	KEY I_T185_C007_Id_Separou (T185_C007_Id_Separou)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T186 ( Historico dos produtos (locação) )
$cClausula  = <<<EOT
create table if not exists T186 (
	T186_Id int(10) unsigned auto_increment,
	T186_T001_Id int(10) unsigned DEFAULT NULL,
	T186_T066_Id int(10) unsigned DEFAULT NULL,
	T186_Quantidade decimal(12,5),
	T186_Quantidade_Saldo decimal(12,5) DEFAULT 0,
	PRIMARY KEY (T186_Id),
	KEY I_T186_T001_Id (T186_T001_Id),
	KEY I_T186_T066_Id (T186_T066_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T189 ( Requisições de Cotação/Compra )
$cClausula  = <<<EOT
create table if not exists T189 (
	T189_Id int(10) unsigned auto_increment,
	T189_T004_Id int(10) unsigned DEFAULT NULL,
	T189_T006_Id int(10) unsigned DEFAULT NULL,
	T189_D009_Id int(10) unsigned DEFAULT NULL,
	T189_C007_Id int(10) unsigned DEFAULT NULL,
	T189_Data_Criado datetime DEFAULT '0000-00-00 00:00:00',
	T189_Descricao_Produto varchar(255),
	T189_Quantidade decimal(20,5),
	T189_Observacao varchar(255),
	T189_Detalhes varchar(255),
	PRIMARY KEY (T189_Id),
	KEY I_T189_T004_Id (T189_T004_Id),
	KEY I_T189_T006_Id (T189_T006_Id),
	KEY I_T189_D009_Id (T189_D009_Id),
	KEY I_T189_C007_Id (T189_C007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// Criado tabela T190 (Pagamentos na ordem de compra)

$cClausula = <<<EOT
create table if not exists T190 (
  T190_Id int(11) NOT NULL AUTO_INCREMENT,
  T190_T011_Id int(11) DEFAULT NULL,
  T190_D027_Id int(11) DEFAULT NULL,
  T190_Numero_Parcela int(3) DEFAULT NULL,
  T190_Prazos_Dias int(5) DEFAULT NULL,
  T190_Data_Vencimento date DEFAULT NULL,
  T190_Valor_Parcela decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (T190_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// Criado tabela T191 (Tabela Conversa - Envio de Mensagens)

$cClausula = <<<EOT
create table if not exists T191 (
  T191_Id int(11) NOT NULL AUTO_INCREMENT,
  T191_T005_Id int(11) DEFAULT NULL,
  T191_T003_Id int(11) DEFAULT NULL,
  T191_T189_Id int(11) DEFAULT NULL,
  T191_Flag_Status char(1) default 'P',
  T191_Assunto varchar(100) DEFAULT NULL,
  PRIMARY KEY (T191_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// Criado tabela T192 (Tabela Mensagens - Envio de Mensagens)
$cClausula = <<<EOT
create table if not exists T192 (
  T192_Id int(11) NOT NULL AUTO_INCREMENT,
  T192_T191_Id int(11) DEFAULT NULL,
  T192_C007_Id int(11) DEFAULT NULL,
  T192_Data date DEFAULT NULL,
  T192_Mensagem text DEFAULT NULL,
  PRIMARY KEY (T192_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T193 ( Entrada dos itens da NF (estoque por locação) )
$cClausula  = <<<EOT
create table if not exists T193 (
	T193_Id int(10) unsigned auto_increment,
	T193_T013_Id int(10) unsigned DEFAULT NULL,
	T193_T014_Id int(10) unsigned DEFAULT NULL,
	T193_T066_Id int(10) unsigned DEFAULT NULL,
	T193_Qtd_Estoque decimal(12,5),
	PRIMARY KEY (T193_Id),
	KEY I_T193_T013_Id (T193_T013_Id),
	KEY I_T193_T014_Id (T193_T014_Id),
	KEY I_T193_T066_Id (T193_T066_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela T195 ( Entrada/Saida dos itens da Montagem (estoque por locação) )
$cClausula  = <<<EOT
create table if not exists T195 (
	T195_Id int(10) unsigned auto_increment,
	T195_T047_Id int(10) unsigned DEFAULT NULL,
	T195_T048_Id int(10) unsigned DEFAULT NULL,
	T195_T071_Id int(10) unsigned DEFAULT NULL,
	T195_T066_Id int(10) unsigned DEFAULT NULL,
	T195_Flag_Operacao char(1) default 'S' NOT NULL,
	T195_Qtd_Estoque decimal(12,5),
	PRIMARY KEY (T195_Id),
	KEY I_T195_T047_Id (T195_T047_Id),
	KEY I_T195_T048_Id (T195_T048_Id),
	KEY I_T195_T071_Id (T195_T071_Id),
	KEY I_T195_T066_Id (T195_T066_Id),
	KEY I_T195_Flag_Operacao (T195_Flag_Operacao)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// Criado tabela R001 (Relatórios Diários)
$cClausula = <<<EOT
create table if not exists `R001` (
  `R001_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `R001_Data` date NOT NULL,
  `R001_C007_Id_Vendedor` int(11) NOT NULL DEFAULT '0',
  `R001_C007_Id_Supervisor` int(11) NOT NULL DEFAULT '0',
  `R001_D024_Id` int(11) NOT NULL DEFAULT '0',
  `R001_D049_Id` int(11) NOT NULL DEFAULT '0',
  `R001_D006_Id` int(11) NOT NULL DEFAULT '0',
  `R001_Clientes_Nr` int(11) DEFAULT NULL,
  `R001_Clientes_Contatos` int(11) DEFAULT NULL,
  `R001_Clientes_Novos` int(11) DEFAULT NULL,
  `R001_Clientes_Ativos` int(11) DEFAULT NULL,
  `R001_Clientes_Inativos` int(11) DEFAULT NULL,
  `R001_Orcamentos_Nr` int(11) DEFAULT NULL,
  `R001_Orcamentos_Clientes` int(11) DEFAULT NULL,
  `R001_Orcamentos_Itens` float DEFAULT NULL,
  `R001_Orcamentos_Itens_Diferentes` int(11) DEFAULT NULL,
  `R001_Orcamentos_Valor_Total` float DEFAULT NULL,
  `R001_Orcamentos_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Pedidos_Nr` int(11) DEFAULT NULL,
  `R001_Pedidos_Clientes` int(11) DEFAULT NULL,
  `R001_Pedidos_Itens` float DEFAULT NULL,
  `R001_Pedidos_Itens_Diferentes` int(11) DEFAULT NULL,
  `R001_Pedidos_Valor_Total` float DEFAULT NULL,
  `R001_Pedidos_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Faturados_Nr` int(11) DEFAULT NULL,
  `R001_Faturados_Clientes` int(11) DEFAULT NULL,
  `R001_Faturados_Itens` float DEFAULT NULL,
  `R001_Faturados_Itens_Diferentes` int(11) DEFAULT NULL,
  `R001_Faturados_Valor_Total` float DEFAULT NULL,
  `R001_Faturados_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Faturados_IPV` float DEFAULT NULL,
  `R001_Faturados_IPV_Real` float DEFAULT NULL,
  `R001_Faturados_Tributos_ICMS` float DEFAULT NULL,
  `R001_Faturados_Tributos_ICMS_ST` float DEFAULT NULL,
  `R001_Faturados_Tributos_IPI` float DEFAULT NULL,
  `R001_Faturados_Tributos_PIS` float DEFAULT NULL,
  `R001_Faturados_Tributos_COFINS` float DEFAULT NULL,
  `R001_Devolucoes_Nr` int(11) DEFAULT NULL,
  `R001_Devolucoes_Clientes` int(11) DEFAULT NULL,
  `R001_Devolucoes_Valor_Total` float DEFAULT NULL,
  `R001_Devolucoes_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Devolucoes_Tributos_ICMS` float DEFAULT NULL,
  `R001_Devolucoes_Tributos_ICMS_ST` float DEFAULT NULL,
  `R001_Devolucoes_Tributos_IPI` float DEFAULT NULL,
  `R001_Devolucoes_Tributos_PIS` float DEFAULT NULL,
  `R001_Devolucoes_Tributos_COFINS` float DEFAULT NULL,
  `R001_Compras_Nr` int(11) DEFAULT NULL,
  `R001_Compras_Clientes` int(11) DEFAULT NULL,
  `R001_Compras_Valor_Total` float DEFAULT NULL,
  `R001_Compras_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Compras_Tributos_ICMS` float DEFAULT NULL,
  `R001_Compras_Tributos_ICMS_ST` float DEFAULT NULL,
  `R001_Compras_Tributos_IPI` float DEFAULT NULL,
  `R001_Compras_Tributos_PIS` float DEFAULT NULL,
  `R001_Compras_Tributos_COFINS` float DEFAULT NULL,
  `R001_Entrada_Nr` int(11) DEFAULT NULL,
  `R001_Entrada_Clientes` int(11) DEFAULT NULL,
  `R001_Entrada_Itens` float DEFAULT NULL,
  `R001_Entrada_Itens_Diferentes` int(11) DEFAULT NULL,
  `R001_Entrada_Valor_Total` float DEFAULT NULL,
  `R001_Entrada_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Entrada_Tributos_ICMS` float DEFAULT NULL,
  `R001_Entrada_Tributos_ICMS_ST` float DEFAULT NULL,
  `R001_Entrada_Tributos_IPI` float DEFAULT NULL,
  `R001_Entrada_Tributos_PIS` float DEFAULT NULL,
  `R001_Entrada_Tributos_COFINS` float DEFAULT NULL,
  `R001_Saida_Nr` int(11) DEFAULT NULL,
  `R001_Saida_Clientes` int(11) DEFAULT NULL,
  `R001_Saida_Itens` float DEFAULT NULL,
  `R001_Saida_Itens_Diferentes` int(11) DEFAULT NULL,
  `R001_Saida_Valor_Total` float DEFAULT NULL,
  `R001_Saida_Valor_Total_Produtos` float DEFAULT NULL,
  `R001_Saida_Tributos_ICMS` float DEFAULT NULL,
  `R001_Saida_Tributos_ICMS_ST` float DEFAULT NULL,
  `R001_Saida_Tributos_IPI` float DEFAULT NULL,
  `R001_Saida_Tributos_PIS` float DEFAULT NULL,
  `R001_Saida_Tributos_COFINS` float DEFAULT NULL,
  PRIMARY KEY (`R001_Id`,`R001_Data`,`R001_C007_Id_Vendedor`,`R001_C007_Id_Supervisor`,`R001_D024_Id`,`R001_D049_Id`,`R001_D006_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// Criado tabela R002 (Fluxo Caixa)
$cClausula = <<<EOT
create table if not exists `R002` (
  `R002_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `R002_Data` date NOT NULL,
  `R002_C004_Id` int(11) NOT NULL DEFAULT '0',
  `R002_Valor_Receber` float DEFAULT NULL,
  `R002_Valor_Receber_Projecao` float DEFAULT NULL,
  `R002_Valor_Pagar` float DEFAULT NULL,
  `R002_Valor_Pagar_Projecao` float DEFAULT NULL,
  `R002_Valor_Recebido` float DEFAULT NULL,
  `R002_Valor_Pago` float DEFAULT NULL,
  PRIMARY KEY (`R002_Id`,`R002_Data`,`R002_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
create table if not exists `R003` (
  `R003_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `R003_Data` date NOT NULL,
  `R003_C007_Id_Vendedor` int(11) NOT NULL DEFAULT '0',
  `R003_C007_Id_Supervisor` int(11) NOT NULL DEFAULT '0',
  `R003_D024_Id` int(11) NOT NULL DEFAULT '0',
  `R003_D049_Id` int(11) NOT NULL DEFAULT '0',
  `R003_D006_Id` int(11) NOT NULL DEFAULT '0',
  `R003_Clientes_Nr` int(11) DEFAULT NULL,
  `R003_Clientes_Contatos` int(11) DEFAULT NULL,
  `R003_Clientes_Novos` int(11) DEFAULT NULL,
  `R003_Clientes_Ativos` int(11) DEFAULT NULL,
  `R003_Clientes_Inativos` int(11) DEFAULT NULL,
  `R003_Orcamentos_Nr` int(11) DEFAULT NULL,
  `R003_Orcamentos_Clientes` int(11) DEFAULT NULL,
  `R003_Orcamentos_Itens` float DEFAULT NULL,
  `R003_Orcamentos_Itens_Diferentes` int(11) DEFAULT NULL,
  `R003_Orcamentos_Valor_Total` float DEFAULT NULL,
  `R003_Orcamentos_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Pedidos_Nr` int(11) DEFAULT NULL,
  `R003_Pedidos_Clientes` int(11) DEFAULT NULL,
  `R003_Pedidos_Itens` float DEFAULT NULL,
  `R003_Pedidos_Itens_Diferentes` int(11) DEFAULT NULL,
  `R003_Pedidos_Valor_Total` float DEFAULT NULL,
  `R003_Pedidos_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Faturados_Nr` int(11) DEFAULT NULL,
  `R003_Faturados_Clientes` int(11) DEFAULT NULL,
  `R003_Faturados_Itens` float DEFAULT NULL,
  `R003_Faturados_Itens_Diferentes` int(11) DEFAULT NULL,
  `R003_Faturados_Valor_Total` float DEFAULT NULL,
  `R003_Faturados_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Faturados_IPV` float DEFAULT NULL,
  `R003_Faturados_IPV_Real` float DEFAULT NULL,
  `R003_Faturados_Tributos_ICMS` float DEFAULT NULL,
  `R003_Faturados_Tributos_ICMS_ST` float DEFAULT NULL,
  `R003_Faturados_Tributos_IPI` float DEFAULT NULL,
  `R003_Faturados_Tributos_PIS` float DEFAULT NULL,
  `R003_Faturados_Tributos_COFINS` float DEFAULT NULL,
  `R003_Devolucoes_Nr` int(11) DEFAULT NULL,
  `R003_Devolucoes_Clientes` int(11) DEFAULT NULL,
  `R003_Devolucoes_Valor_Total` float DEFAULT NULL,
  `R003_Devolucoes_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Devolucoes_Tributos_ICMS` float DEFAULT NULL,
  `R003_Devolucoes_Tributos_ICMS_ST` float DEFAULT NULL,
  `R003_Devolucoes_Tributos_IPI` float DEFAULT NULL,
  `R003_Devolucoes_Tributos_PIS` float DEFAULT NULL,
  `R003_Devolucoes_Tributos_COFINS` float DEFAULT NULL,
  `R003_Compras_Nr` int(11) DEFAULT NULL,
  `R003_Compras_Clientes` int(11) DEFAULT NULL,
  `R003_Compras_Valor_Total` float DEFAULT NULL,
  `R003_Compras_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Compras_Tributos_ICMS` float DEFAULT NULL,
  `R003_Compras_Tributos_ICMS_ST` float DEFAULT NULL,
  `R003_Compras_Tributos_IPI` float DEFAULT NULL,
  `R003_Compras_Tributos_PIS` float DEFAULT NULL,
  `R003_Compras_Tributos_COFINS` float DEFAULT NULL,
  `R003_Entrada_Nr` int(11) DEFAULT NULL,
  `R003_Entrada_Clientes` int(11) DEFAULT NULL,
  `R003_Entrada_Itens` float DEFAULT NULL,
  `R003_Entrada_Itens_Diferentes` int(11) DEFAULT NULL,
  `R003_Entrada_Valor_Total` float DEFAULT NULL,
  `R003_Entrada_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Entrada_Tributos_ICMS` float DEFAULT NULL,
  `R003_Entrada_Tributos_ICMS_ST` float DEFAULT NULL,
  `R003_Entrada_Tributos_IPI` float DEFAULT NULL,
  `R003_Entrada_Tributos_PIS` float DEFAULT NULL,
  `R003_Entrada_Tributos_COFINS` float DEFAULT NULL,
  `R003_Saida_Nr` int(11) DEFAULT NULL,
  `R003_Saida_Clientes` int(11) DEFAULT NULL,
  `R003_Saida_Itens` float DEFAULT NULL,
  `R003_Saida_Itens_Diferentes` int(11) DEFAULT NULL,
  `R003_Saida_Valor_Total` float DEFAULT NULL,
  `R003_Saida_Valor_Total_Produtos` float DEFAULT NULL,
  `R003_Saida_Tributos_ICMS` float DEFAULT NULL,
  `R003_Saida_Tributos_ICMS_ST` float DEFAULT NULL,
  `R003_Saida_Tributos_IPI` float DEFAULT NULL,
  `R003_Saida_Tributos_PIS` float DEFAULT NULL,
  `R003_Saida_Tributos_COFINS` float DEFAULT NULL,
  PRIMARY KEY (`R003_Id`,`R003_Data`,`R003_C007_Id_Vendedor`,`R003_C007_Id_Supervisor`,`R003_D024_Id`,`R003_D049_Id`,`R003_D006_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=51443 DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
create table if not exists `R004` (
  `R004_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `R004_Data` date NOT NULL,
  `R004_C007_Id_Vendedor` int(11) NOT NULL DEFAULT '0',
  `R004_C007_Id_Supervisor` int(11) NOT NULL DEFAULT '0',
  `R004_D024_Id` int(11) NOT NULL DEFAULT '0',
  `R004_D049_Id` int(11) NOT NULL DEFAULT '0',
  `R004_D006_Id` int(11) NOT NULL DEFAULT '0',
  `R004_Clientes_Nr` int(11) DEFAULT NULL,
  `R004_Clientes_Contatos` int(11) DEFAULT NULL,
  `R004_Clientes_Novos` int(11) DEFAULT NULL,
  `R004_Clientes_Ativos` int(11) DEFAULT NULL,
  `R004_Clientes_Inativos` int(11) DEFAULT NULL,
  `R004_Orcamentos_Nr` int(11) DEFAULT NULL,
  `R004_Orcamentos_Clientes` int(11) DEFAULT NULL,
  `R004_Orcamentos_Itens` float DEFAULT NULL,
  `R004_Orcamentos_Itens_Diferentes` int(11) DEFAULT NULL,
  `R004_Orcamentos_Valor_Total` float DEFAULT NULL,
  `R004_Orcamentos_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Pedidos_Nr` int(11) DEFAULT NULL,
  `R004_Pedidos_Clientes` int(11) DEFAULT NULL,
  `R004_Pedidos_Itens` float DEFAULT NULL,
  `R004_Pedidos_Itens_Diferentes` int(11) DEFAULT NULL,
  `R004_Pedidos_Valor_Total` float DEFAULT NULL,
  `R004_Pedidos_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Faturados_Nr` int(11) DEFAULT NULL,
  `R004_Faturados_Clientes` int(11) DEFAULT NULL,
  `R004_Faturados_Itens` float DEFAULT NULL,
  `R004_Faturados_Itens_Diferentes` int(11) DEFAULT NULL,
  `R004_Faturados_Valor_Total` float DEFAULT NULL,
  `R004_Faturados_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Faturados_IPV` float DEFAULT NULL,
  `R004_Faturados_IPV_Real` float DEFAULT NULL,
  `R004_Faturados_Tributos_ICMS` float DEFAULT NULL,
  `R004_Faturados_Tributos_ICMS_ST` float DEFAULT NULL,
  `R004_Faturados_Tributos_IPI` float DEFAULT NULL,
  `R004_Faturados_Tributos_PIS` float DEFAULT NULL,
  `R004_Faturados_Tributos_COFINS` float DEFAULT NULL,
  `R004_Devolucoes_Nr` int(11) DEFAULT NULL,
  `R004_Devolucoes_Clientes` int(11) DEFAULT NULL,
  `R004_Devolucoes_Valor_Total` float DEFAULT NULL,
  `R004_Devolucoes_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Devolucoes_Tributos_ICMS` float DEFAULT NULL,
  `R004_Devolucoes_Tributos_ICMS_ST` float DEFAULT NULL,
  `R004_Devolucoes_Tributos_IPI` float DEFAULT NULL,
  `R004_Devolucoes_Tributos_PIS` float DEFAULT NULL,
  `R004_Devolucoes_Tributos_COFINS` float DEFAULT NULL,
  `R004_Compras_Nr` int(11) DEFAULT NULL,
  `R004_Compras_Clientes` int(11) DEFAULT NULL,
  `R004_Compras_Valor_Total` float DEFAULT NULL,
  `R004_Compras_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Compras_Tributos_ICMS` float DEFAULT NULL,
  `R004_Compras_Tributos_ICMS_ST` float DEFAULT NULL,
  `R004_Compras_Tributos_IPI` float DEFAULT NULL,
  `R004_Compras_Tributos_PIS` float DEFAULT NULL,
  `R004_Compras_Tributos_COFINS` float DEFAULT NULL,
  `R004_Entrada_Nr` int(11) DEFAULT NULL,
  `R004_Entrada_Clientes` int(11) DEFAULT NULL,
  `R004_Entrada_Itens` float DEFAULT NULL,
  `R004_Entrada_Itens_Diferentes` int(11) DEFAULT NULL,
  `R004_Entrada_Valor_Total` float DEFAULT NULL,
  `R004_Entrada_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Entrada_Tributos_ICMS` float DEFAULT NULL,
  `R004_Entrada_Tributos_ICMS_ST` float DEFAULT NULL,
  `R004_Entrada_Tributos_IPI` float DEFAULT NULL,
  `R004_Entrada_Tributos_PIS` float DEFAULT NULL,
  `R004_Entrada_Tributos_COFINS` float DEFAULT NULL,
  `R004_Saida_Nr` int(11) DEFAULT NULL,
  `R004_Saida_Clientes` int(11) DEFAULT NULL,
  `R004_Saida_Itens` float DEFAULT NULL,
  `R004_Saida_Itens_Diferentes` int(11) DEFAULT NULL,
  `R004_Saida_Valor_Total` float DEFAULT NULL,
  `R004_Saida_Valor_Total_Produtos` float DEFAULT NULL,
  `R004_Saida_Tributos_ICMS` float DEFAULT NULL,
  `R004_Saida_Tributos_ICMS_ST` float DEFAULT NULL,
  `R004_Saida_Tributos_IPI` float DEFAULT NULL,
  `R004_Saida_Tributos_PIS` float DEFAULT NULL,
  `R004_Saida_Tributos_COFINS` float DEFAULT NULL,
  PRIMARY KEY (`R004_Id`,`R004_Data`,`R004_C007_Id_Vendedor`,`R004_C007_Id_Supervisor`,`R004_D024_Id`,`R004_D049_Id`,`R004_D006_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=51443 DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}



// C029 / C030 (novos acessos por perfil)
$cClausula  = <<<EOT
create table if not exists C029  (
  `C029_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C029_Descricao_Acesso` varchar(150) DEFAULT NULL,
  `C029_Entidade` char(4) DEFAULT NULL,
  `C029_Tela` varchar(150) DEFAULT NULL,
  `C029_Opcao` varchar(30) DEFAULT NULL,
  `C029_Grupo` varchar(50) DEFAULT NULL,
  `C029_Grupo_NFe` varchar(10) DEFAULT NULL,
  `C029_Flag_NFe` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`C029_Id`)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS `C030` (
  `C030_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C030_C021_Id` int(10) unsigned NOT NULL,
  `C030_C029_Id` int(10) unsigned NOT NULL,
  `C030_Id_Perfil` int(10) unsigned NOT NULL,
  `C030_C007_Id` int(10) DEFAULT NULL,
  `C030_Acesso_Liberado` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`C030_Id`)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}


// Criado tabela T192 (Tabela Mensagens - Envio de Mensagens)
$cClausula = <<<EOT
create table if not exists T192 (
  T192_Id int(11) NOT NULL AUTO_INCREMENT,
  T192_T191_Id int(11) DEFAULT NULL,
  T192_C007_Id int(11) DEFAULT NULL,
  T192_Data date DEFAULT NULL,
  T192_Mensagem text DEFAULT NULL,
  PRIMARY KEY (T192_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
create table if not exists T194 (
  T194_Id int(11) NOT NULL AUTO_INCREMENT,
  T194_D024_Id int(11) DEFAULT NULL,
  T194_C007_Id int(11) DEFAULT NULL,
  T194_Data date DEFAULT NULL,
  T194_Status varchar(1) DEFAULT 'A',
  PRIMARY KEY (T194_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T196 (histórico de reparcelamentos contas a pagar)
$cClausula  = <<<EOT
create table if not exists T196 (
    T196_Id int(10) unsigned auto_increment,    
    T196_D024_Id int(10) unsigned,
    T196_C004_Id int(10) unsigned,
    T196_Data_Emissao date,    
    T196_Valor_Total DECIMAL(14,2),
    T196_Titulos_Geradores text,
    T196_Titulos_Gerados text,
    PRIMARY KEY (T196_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela d074 (Itens da Composição da produção de um produto)
$cClausula  = <<<EOT
create table if not exists D074 (
    D074_Id int(10) unsigned auto_increment,    
    D074_D001_Id int(10) unsigned,
    D074_D009_Id int(10) unsigned,
    D074_Quantidade int(10),    
    PRIMARY KEY (D074_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T205 (Histórico de movimentação do produto pelo coletor)
$cClausula  = <<<EOT
create table if not exists T205 (
    T205_Id int(10) unsigned auto_increment,    
    T205_T006_Id int(10) unsigned,
    T205_C007_Id int(10) unsigned,
    T205_Data datetime,
    T205_Quantidade decimal(10,2),
    T205_Operacao varchar(1),    
    PRIMARY KEY (T205_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
// criação da tabela T208 (Cadastro de rotas)
$cClausula  = <<<EOT
create table if not exists T208 (
    T208_Id int(10) unsigned auto_increment,    
    T208_Rota varchar(30),    
    PRIMARY KEY (T208_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
// criação da tabela D076 (Materiais de Consumo de um processo de produção)
$cClausula  = <<<EOT
create table if not exists D076 (
    D076_Id int(10) unsigned auto_increment,    
    D076_D075_Id int(10) unsigned,
    D076_D009_Id int(10) unsigned,
    D076_Quantidade int(10),    
    PRIMARY KEY (D076_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T206 (Produtos com defeito)
$cClausula  = <<<EOT
create table if not exists T206 (
    T206_Id int(10) unsigned auto_increment,
    T206_D009_Id int(10) unsigned,
    T206_Data date,
    T206_Quantidade int(10),
    T206_Descricao_Defeito varchar(150) DEFAULT NULL,   
    PRIMARY KEY (T206_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T207 (Controle de Produtos do Pedido)
$cClausula  = <<<EOT
create table if not exists T207 (
    T207_Id int(10) unsigned auto_increment,        
    T207_D009_Id int(10) unsigned,
    T207_T006_Id int(10) unsigned,
    T207_C007_Id int(10) unsigned,
    T207_Data date,
    T207_Flag_Status char(1) DEFAULT 1,   
    PRIMARY KEY (T207_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

// D138 (Etiquetas)
$cClausula  = <<<EOT
create table if not exists `D138` (
  `D138_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D138_Modelo` varchar(50) NOT NULL,
  `D138_Linguagem` varchar(20) NOT NULL,
  `D138_Codigo` TEXT NOT NULL,
  PRIMARY KEY (`D138_Id`),
  KEY `I_D138_Id` (`D138_Id`)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela D140 (Criação de Relatórios/GRIDs dinamicos)
$cClausula  = <<<EOT
create table if not exists D140 (
    D140_Id int(10) unsigned auto_increment,
    D140_Nome varchar(255),
    D140_Nome_Md5 varchar(255),
    D140_SQL text,
    D140_Data_Criacao date,
    PRIMARY KEY (D140_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T037 (Header do Retorno da Remessa)
$cClausula  = <<<EOT
create table if not exists T037 (
    T037_Id int(10) unsigned auto_increment,
    T037_C004_Id int(10) unsigned,
    T037_Data_Processamento date,
    T037_Conteudo_Arquivo text,
    T037_Linha_Header text,
    T037_Linha_Trailler text,
    T037_Nome_Arquivo varchar(20),
    T037_Numero_Titulos int(10),
    T037_Codigo_Banco varchar(3),
    PRIMARY KEY (T037_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela D141 (Equipamentos de impressao de Cupom Fiscal)
$cClausula  = <<<EOT
create table if not exists D141 (
    D141_Id int(10) unsigned auto_increment,
    D141_Modelo varchar(20),
    D141_Numero_Serie varchar(20),
    D141_Numero_Caixa int(3),
    PRIMARY KEY (D141_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela D142 (Atalhos de teclas)
$cClausula  = <<<EOT
create table if not exists D142 (
    D142_Id int(10) unsigned auto_increment,
    D142_Atalho varchar(30),
    D142_Codigo text,
    D142_Nome varchar(50),
    D142_Descricao varchar(250),
    PRIMARY KEY (D142_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$registroD142 = <<<EOT
insert into d142 (`D142_Id`,`D142_Atalho`,`D142_Codigo`,`D142_Nome`,`D142_Descricao`) values (1,'F2','abrirJanela(false, divIdRootAberto(), \'\', menuId(\'cad001\'), \'Clientes e Fornecedores\', \'.: Clientes e Fornecedores :.\', \'/cad/cad001/content/cad001ContentPrincipal/\', \'\', \'forcarAuto\', false, \'center\');','Clientes e Fornecedores',NULL);
insert into d142 (`D142_Id`,`D142_Atalho`,`D142_Codigo`,`D142_Nome`,`D142_Descricao`) values (2,'F3','abrirJanela(false, divIdRootAberto(), \'\', menuId(\'cad002\'), \'Produtos\', \'.:Produtos:.\', \'/cad/cad002/content/grid1/\', \'\', \'forcarAuto\', false, \'center\');','Produtos',NULL);
insert into d142 (`D142_Id`,`D142_Atalho`,`D142_Codigo`,`D142_Nome`,`D142_Descricao`) values (3,'F4','abrirJanela(false, divIdRootAberto(), \'\', menuId(\'incOrc\'), \'\', \'Orçamento\', \'/ven/ven001/content/ven001ContentOrcamento/\', \'\', [650,500]);','Novo Orçamento',NULL);
insert into d142 (`D142_Id`,`D142_Atalho`,`D142_Codigo`,`D142_Nome`,`D142_Descricao`) values (4,'F7','$.getJSON(\'/ven/ven003/grid_func-ajax/ven003incluirNotaFiscalConsumidor/?ajax=true&callback=?\', function(request) { if (request.code) { abrirJanela(false, divIdRootAberto(), \'\', menuId(\'incPDV\'), \'\', \'Nota Fiscal Consumidor\', \'/ven/ven003/content/ven003contentIncluirNotaFiscal/\',\'&acaoId=\' + encodeURIComponent(request.data), [900,550]); } else { dialogConfirm(\'Erro\', request.data, false); }});','Nova Nota Fiscal de Consumidor',NULL);
insert into d142 (`D142_Id`,`D142_Atalho`,`D142_Codigo`,`D142_Nome`,`D142_Descricao`) values (5,'F8',"abrirJanela(event, divIdRootAberto(), '', menuId('ven015'), 'PDV', '.: PDV :.', '/ven/ven015/content/ven015contentNotaFiscalPDV/', '', '[1000,600]', false, 'center');",'PDV',NULL);
insert into d142 (`D142_Id`,`D142_Atalho`,`D142_Codigo`,`D142_Nome`,`D142_Descricao`) values (6,'F9',"abrirJanela(false, divIdRootAberto(), '', menuId('agenda'), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&tab=geral', [700,405]);",'Agenda',NULL);

EOT;
$registroD142 = explode("\n", $registroD142);
foreach ($registroD142 as $registroD142SQL) {
  $cSql = mysql_query($registroD142SQL);
}

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$updateD142 = <<<EOT
    UPDATE D142 SET D142_Codigo = 'var \$buttons = { "Sim": function() { $.getJSON(\'/ven/ven003/grid_func-ajax/ven003incluirNotaFiscalConsumidor/?ajax=true&callback=?\', function(request) { if (request.code) { abrirJanela(false, divIdRootAberto(), \'\', menuId(\'incPDV\'), \'\', \'Nota Fiscal Consumidor\', \'/ven/ven003/content/ven003contentIncluirNotaFiscal/\',\'&acaoId=\' + encodeURIComponent(request.data), [900,550]); } else { dialogConfirm(\'Erro\', request.data, false); }}); $(this).dialog("destroy").remove();},"Não": function() { $(this).dialog("destroy").remove(); } };dialogConfirm(\'Gerar nova NFC-e\', \'Você tem certeza?\', \$buttons);' WHERE D142_Atalho = 'F7';
EOT;

$cSql = mysql_query($updateD142);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela D166 (Cadastro de Grupo Suporte)
$cClausula  = <<<EOT
create table if not exists D166 (
    D166_Id int(10) unsigned auto_increment,
    D166_Grupo_Suporte varchar(30),
    D166_Flag_Processar_Feedback varchar(1) default 'N',
    PRIMARY KEY (D166_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela D167 (Cadastro de Lista)
$cClausula  = <<<EOT
create table if not exists D167 (
    D167_Id int(10) unsigned auto_increment,
    D167_D166_Id int(10) unsigned,
    D167_Nome_Lista varchar(30),
    D167_Horas_Validade int(3) DEFAULT '0',
    D167_Nivel_Prioridade int(1) DEFAULT '3',
    PRIMARY KEY (D167_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS D168(
  D168_Id int(10) NOT NULL AUTO_INCREMENT,
  D168_D022_Id int(10) unsigned,
  D168_Plataforma varchar(40),
  D168_Id_Transportadora_Intelipost int(10),
  D168_Nome_Transportadora_Intelipost varchar(150),
  PRIMARY KEY (D168_Id),
  KEY `D168_D022_Id` (`D168_D022_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela D169 (Cadastro de Outros Estoques - Tipos de Produto)
$cClausula  = <<<EOT
create table if not exists D169 (
    D169_Id int(10) unsigned auto_increment,
    D169_Descricao_Tipo_Produto varchar(100),
    D169_Sigla_Tipo_Produto varchar(3),
    PRIMARY KEY (D169_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}



// criação da tabela T209 (Tipos de Agregados)
$cClausula  = <<<EOT
create table if not exists T209 (
    T209_Id int(10) unsigned auto_increment,
    T209_Nome varchar(30),
    PRIMARY KEY (T209_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T210 (Agregados do produto da ordem de compra)
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T211 (
  T211_Id INT NOT NULL AUTO_INCREMENT,
  T211_C004_Id INT,
  T211_C007_Id INT,
  T211_Data_Hora DATETIME,
  T211_Grupo VARCHAR(100),
  T211_Mensagem VARCHAR(255),
  T211_Backtrace TEXT,
  T211_Flag_Nivel INT(1),
  PRIMARY KEY (T211_Id),
  KEY `I_T211_C004_Id` (`T211_C004_Id`),
  KEY `I_T211_C007_Id` (`T211_C007_Id`),
  KEY `I_T211_Data_Hora` (`T211_Data_Hora`),
  KEY `I_T211_Grupo` (`T211_Grupo`),
  KEY `I_T211_Flag_Nivel` (`T211_Flag_Nivel`)
)
DEFAULT CHARSET=utf8
ENGINE = MYISAM;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T210 (Agregados do produto da ordem de compra)
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS T210 (
  T210_Id INT NOT NULL AUTO_INCREMENT ,
  T210_T012_Id INT NULL,
  T210_T209_Id INT NULL,
  T210_Descricao VARCHAR(50) NULL ,
  T210_Valor_Unitario DECIMAL(14,2) NULL ,
  T210_Observacao text NULL,
  PRIMARY KEY (T210_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T212 (Tipos de Serviços)
$cClausula  = <<<EOT
create table if not exists T212 (
    T212_Id int(10) unsigned auto_increment,
    T212_Nome varchar(30),
    PRIMARY KEY (T212_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T213 (Auditoria por campo)
$cClausula  = <<<EOT
create table if not exists T213 (
    T213_Id int(10) unsigned auto_increment,
    T213_T162_Id INT NULL,
    T213_Campo varchar(50),
    T213_Antes text,
    T213_Depois text,
    PRIMARY KEY (T213_Id),
    KEY `I_T213_T162_Id` (`T213_T162_Id`),
    KEY `I_T213_Campo` (`T213_Campo`)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T214 (Histórico de Balanços)
$cClausula  = <<<EOT
create table if not exists T214 (
    T214_Id int(10) unsigned auto_increment,
    T214_C007_Id int(10),
    T214_C004_Id int(10),
    T214_Data_Hora_Balanco DATETIME,
    PRIMARY KEY (T214_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T214 (Histórico de Balanços)
$cClausula  = <<<EOT
create table if not exists D144 (
    D144_Id int(10) unsigned auto_increment,
    D144_D049_Id int(10),
    D144_T014_Id int(10),
    D144_T055_Id int(10),
    D144_Descricao_Produto varchar(100),
    D144_Quantidade_Estoque decimal(10,2),
    D144_Valor_Preco_Venda decimal(10,2),
    PRIMARY KEY (D144_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T215 (Cabeçalho dos Extratos Bancarios)
$cClausula  = <<<EOT
create table if not exists T215 (
    T215_Id int(10) unsigned auto_increment,
    T215_C004_Id int(10),
    T215_C007_Id int(10),
    T215_D007_Id int(10),
    T215_Data DATETIME,
    T215_Nome_Arquivo varchar(60),
    T215_Arquivo text,
    T215_Data_Processado DATETIME DEFAULT '0000-00-00 00:00:00',
    T215_Data_Inicio DATE,
    T215_Data_Fim DATE,
    T215_Data_Atualizacao_Conta DATE,
    T215_Data_Geracao_Banco DATE,
    T215_Data_Saldo DATE,
    T215_Valor_Saldo decimal(15,2),
    PRIMARY KEY (T215_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T216 (Itens dos Extratos Bancarios)
$cClausula  = <<<EOT
create table if not exists T216 (
    T216_Id int(10) unsigned auto_increment,
    T216_T215_Id int(10),
    T216_D027_Id int(10),
    T216_D032_Id int(10),
    T216_D014_Id int(10),
    T216_D024_Id int(10),
    T216_T019_Id int(10),
    T216_Data DATE,
    T216_Historico varchar(60),
    T216_Valor decimal(15,2),
    T216_Descricao varchar(120),
    T216_Flag_Importar char(1),
    PRIMARY KEY (T216_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T217 (Regras para Extratos Bancarios)
$cClausula  = <<<EOT
create table if not exists T217 (
    T217_Id int(10) unsigned auto_increment,
    T217_D027_Id int(10),
    T217_D032_Id int(10),
    T217_D014_Id int(10),
    T217_D024_Id int(10),
    T217_D007_Id int(10),
    T217_Historico varchar(60),
    T217_Descricao varchar(120),
    PRIMARY KEY (T217_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// **********************************************************************
// *********************** NOVO MÓDULO COTAÇÃO **************************
// **********************************************************************


// Cabeçalho cotação de compra
$cClausula  = <<<EOT
create table if not exists T218 (
T218_Id                                            int(10) unsigned     auto_increment,
T218_T010_Id                                       int(10) unsigned     DEFAULT NULL,
T218_C008_Id                                       int(11)              DEFAULT NULL,
T218_D027_Id_Entrada                               int(10) unsigned     DEFAULT NULL,
T218_D027_Id                                       int(10) unsigned     DEFAULT NULL,
T218_T064_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D047_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D018_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D013_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D022_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D036_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D021_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D024_Id                                       int(10) unsigned     DEFAULT NULL,
T218_D006_Id                                       int(10) unsigned     DEFAULT NULL,
T218_C007_Id_Vendedor_Interno                      int(10) unsigned     DEFAULT NULL,
T218_C007_Id_Vendedor_Externo                      int(10) unsigned     DEFAULT NULL,
T218_C007_Id_IPV_Liberado                          int(10) unsigned     DEFAULT NULL,
T218_C004_Id                                       int(10) unsigned     DEFAULT NULL,
T218_Numero_Orcamento_Venda                        int(10)              DEFAULT '0',
T218_Nome_Cliente                                  varchar(60)          DEFAULT NULL,
T218_Nome_Transportadora                           varchar(60)          DEFAULT NULL,
T218_Data_Emissao                                  date                 DEFAULT NULL,
T218_Flag_Frete                                    int(1)               DEFAULT '0',
T218_Prazo_Entrega                                 varchar(40)          DEFAULT NULL,
T218_Nome_Comprador                                varchar(30)          DEFAULT NULL,
T218_E_mail_Comprador                              varchar(60)          DEFAULT NULL,
T218_Observacao_1                                  text                 DEFAULT NULL,
T218_Observacao_2                                  varchar(70)          DEFAULT NULL,
T218_Fax                                           varchar(30)          DEFAULT NULL,
T218_Nome_Vendedor                                 varchar(10)          DEFAULT NULL,
T218_Valor_Desconto                                decimal(10,2)        DEFAULT '0.00',
T218_Valor_Desconto_Confirmado                     decimal(10,2)        DEFAULT '0.00',
T218_Percentual_Desconto                           decimal(30,20)       DEFAULT NULL,
T218_Percentual_Desconto_Confirmado                decimal(30,20)       DEFAULT NULL,
T218_Valor_Percentual_Desconto                     decimal(10,2)        DEFAULT '0.00',
T218_Valor_Percentual_Desconto_Confirmado          decimal(10,2)        DEFAULT '0.00',
T218_Percentual_Margem                             decimal(6,2)         DEFAULT '0.00',
T218_Mao_Obra_1                                    varchar(100)         DEFAULT NULL,
T218_Mao_Obra_2                                    varchar(100)         DEFAULT NULL,
T218_Mao_Obra_3                                    varchar(100)         DEFAULT NULL,
T218_Mao_Obra_4                                    varchar(100)         DEFAULT NULL,
T218_Valor_Mao_Obra                                decimal(10,2)        DEFAULT '0.00',
T218_Flag_Confirmar_Mao_Obra                       varchar(1)           DEFAULT 'N',
T218_Flag_Perdido                                  varchar(1)           DEFAULT 'N',
T218_Flag_Revenda_Consumidor                       varchar(1)           DEFAULT NULL,
T218_Flag_Acp                                      varchar(1)           DEFAULT NULL,
T218_Prazo_1                                       int(4)               DEFAULT '0',
T218_Prazo_2                                       int(4)               DEFAULT '0',
T218_Prazo_3                                       int(4)               DEFAULT '0',
T218_Prazo_4                                       int(4)               DEFAULT '0',
T218_Prazo_5                                       int(4)               DEFAULT '0',
T218_Prazo_6                                       int(4)               DEFAULT '0',
T218_Prazo_7                                       int(4)               DEFAULT '0',
T218_NF_Observacao_1                               text                 DEFAULT NULL,
T218_NF_Observacao_2                               text                 DEFAULT NULL,
T218_NF_Devolucao_Numero                           varchar(12)          DEFAULT NULL,
T218_NF_Devolucao_ICMS                             decimal(5,2)         DEFAULT '0.00',
T218_NF_Devolucao_Numero_Pedido                    int(10)              DEFAULT ' 0',
T218_NF_Devolucao_Emissao                          date                 DEFAULT NULL,
T218_Valor_Frete                                   decimal(19,2)        DEFAULT '0.00',
T218_Valor_Despesas_Acessorias                     decimal(10,2)        DEFAULT '0.00',
T218_Estado                                        varchar(2)           DEFAULT NULL,
T218_Codigo_Usuario                                int(4)               DEFAULT '0',
T218_Codigo_Cliente                                varchar(6)           DEFAULT NULL,
T218_Codigo_Natureza                               int(5)               DEFAULT '0',
T218_Codigo_Transportadora                         varchar(6)           DEFAULT NULL,
T218_Codigo_Representante                          varchar(6)           DEFAULT NULL,
T218_IPV                                           decimal(10,4)        DEFAULT '0.0000',
T218_IPV_Confirmado                                decimal(10,4)        DEFAULT '0.0000',
T218_IPV_Cliente                                   decimal(10,4)        DEFAULT '0.0000',
T218_IPV_Data_Liberado                             datetime             DEFAULT NULL,
T218_IPV_Liberado                                  decimal(10,4)        DEFAULT '0.0000',
T218_IPV_Normal                                    decimal(10,4)        DEFAULT '0.0000',
T218_IPV_Normal_Confirmado                         decimal(10,4)        DEFAULT '0.0000',
T218_Valor_Total_Custo                             decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Custo_Confirmado                  decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Produtos                          decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Produtos_Sem_Desconto             decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Produtos_Confirmado               decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Produtos_Sem_Desconto_Confirmado  decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total                                   decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Confirmado                        decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Custo_Final                       decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Custo_Final_Confirmado            decimal(10,2)        DEFAULT '0.00',
T218_Percentual_Comissao                           decimal(5,2)         DEFAULT '0.00',
T218_Percentual_Reducao_ICMS                       decimal(5,2)         DEFAULT '0.00',
T218_Valor_Total_Base_ICMS                         decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Base_ICMS_Confirmado              decimal(10,2)        DEFAULT '0.00',
T218_Aliquota_ICMS                                 decimal(5,2)         DEFAULT '0.00',
T218_Valor_Total_ICMS                              decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_ICMS_Confirmado                   decimal(10,2)        DEFAULT '0.00',
T218_Percentual_Reducao_IPI                        decimal(5,2)         DEFAULT '0.00',
T218_Valor_Total_Base_IPI                          decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Base_IPI_Confirmado               decimal(10,2)        DEFAULT '0.00',
T218_Aliquota_IPI                                  decimal(5,2)         DEFAULT '0.00',
T218_Valor_Total_IPI                               decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_IPI_Confirmado                    decimal(10,2)        DEFAULT '0.00',
T218_Aliquota_ISS                                  decimal(5,2)         DEFAULT '0.00',
T218_Valor_ISS                                     decimal(10,2)        DEFAULT '5.00',
T218_Peso_Total                                    decimal(10,3)        DEFAULT '0.000',
T218_Peso_Total_Confirmado                         decimal(10,3)        DEFAULT '0.000',
T218_Observacao                                    text                 DEFAULT NULL,
T218_Observacao_Orcamento_Perdido                  text                 DEFAULT NULL,
T218_Valor_Entrada                                 decimal(10,2)        DEFAULT '0.00',
T218_Numero_Parcelas                               int(2)               DEFAULT '0',
T218_Numero_Dias_Entrada                           int(3)               DEFAULT '0',
T218_Numero_Dias                                   int(3)               DEFAULT '0',
T218_Valor_Dolar                                   decimal(10,4)        DEFAULT '0.0000',
T218_Telefone                                      varchar(40)          DEFAULT NULL,
T218_Flag_Expedicao                                int(2)               DEFAULT NULL,
T218_Flag_Lista_Preco                              varchar(1)           DEFAULT '',
T218_ST_ICMS_Interno                               decimal(5,2)         DEFAULT '0.00',
T218_ST_VA                                         decimal(10,2)        DEFAULT '0.00',
T218_Valor_Base_ST                                 decimal(10,2)        DEFAULT '0.00',
T218_Valor_ST                                      decimal(10,2)        DEFAULT '0.00',
T218_Valor_Base_ST_Confirmado                      decimal(10,2)        DEFAULT '0.00',
T218_Valor_ST_Confirmado                           decimal(10,2)        DEFAULT '0.00',
T218_ICMS_Inter_Estadual                           decimal(5,2)         DEFAULT '0.00',
T218_Flag_Alcada_Vendedor                          varchar(1)           DEFAULT '',
T218_Flag_Alcada                                   varchar(50)          DEFAULT '',
T218_Liberou_Alcada_C007_Id                        int(11)              DEFAULT NULL,
T218_Liberou_Alcada_Data                           datetime             DEFAULT '0000-00-00 00:00:00',
T218_Liberou_Alcada_IPV                            decimal(10,4)        DEFAULT '0.0000',
T218_Liberou_Alcada_Observacao_Nao_Liberado        text                 DEFAULT NULL,
T218_C007_Id_Liberou_Alcada                        int(11)              DEFAULT NULL,
T218_C007_Id_Liberou_Alcada_Data                   datetime             DEFAULT '0000-00-00 00:00:00',
T218_C007_Id_Liberou_Alcada_IPV                    decimal(10,4)        DEFAULT '0.0000',
T218_Flag_Status_Liberacao                         varchar(1)           DEFAULT '0',
T218_Observacao_Comercial                          text                 DEFAULT NULL,
T218_Forma_Pagamento                               varchar(40)          DEFAULT NULL,
T218_Frete                                         varchar(40)          DEFAULT NULL,
T218_Flag_Status_Orcamento                         char(1)              DEFAULT 'P',
T218_Valor_Troca                                   decimal(10,2)        DEFAULT NULL,
T218_Validade_Proposta                             varchar(40)          DEFAULT NULL,
T218_Observacao_Pagamento                          text                 DEFAULT NULL,
T218_Pgto_Parcelas                                 varchar(10)          DEFAULT NULL,
T218_Pgto_Primeiro_Vcto_Tipo                       varchar(10)          DEFAULT NULL,
T218_Pgto_Primeiro_Vcto_Valor                      varchar(10)          DEFAULT NULL,
T218_Pgto_Dias_Vcto_Tipo                           varchar(10)          DEFAULT NULL,
T218_Pgto_Dias_Vcto_Valor                          varchar(10)          DEFAULT NULL,
T218_Pgto_Portador                                 varchar(10)          DEFAULT NULL,
T218_Pgto_Data                                     datetime             DEFAULT NULL,
T218_Pgto_C007_Id                                  int(10) unsigned     DEFAULT NULL,
T218_D116_Id                                       int(10) unsigned     DEFAULT NULL,
T218_Liberou_Alcada_Valor_Total                    decimal(10,2)        DEFAULT NULL,
T218_Liberou_Alcada_Prazo_Medio                    decimal(10,4)        DEFAULT NULL,
T218_Liberou_Alcada_Desconto                       decimal(10,2)        DEFAULT NULL,
T218_Valor_Total_Servicos                          decimal(10,2)        DEFAULT NULL,
T218_Valor_Desconto_Manual                         decimal(14,2)        DEFAULT NULL,
T218_Valor_Desconto_Manual_Confirmado              decimal(14,2)        DEFAULT NULL,
T218_Hora_Inicio_Servico                           varchar(30)          DEFAULT NULL,
T218_Hora_Fim_Servico                              varchar(30)          DEFAULT NULL,
T218_Flag_Aguardando_Entrega                       char(1)              DEFAULT 'S',
T218_Flag_PDF_Impresso                             char(1)              DEFAULT 'N',
T218_Flag_Contribuinte                             int(5)               DEFAULT NULL,
T218_Motivo_Pedido_Liberacao                       text                 DEFAULT NULL,
T218_Flag_Cortesia                                 char(1)              DEFAULT 'N',
T218_Flag_Liberou_Alcada_Cortesia                  char(1)              DEFAULT 'N',
T218_Liberou_Alcada_Cortesia_Valor_Total           decimal(10,2)        DEFAULT NULL,
T218_Valor_Total_Abertos                           decimal(10,2)        DEFAULT NULL,
T218_Valor_Total_Fechados                          decimal(10,2)        DEFAULT NULL,
T218_Valor_Total_Cancelados                        decimal(10,2)        DEFAULT NULL,
T218_Flag_Imprimiu                                 varchar(1)           DEFAULT 'N',
T218_Valor_Total_Agregados                         decimal(10,2)        DEFAULT '0.00',
T218_Valor_Total_Comissao                          decimal(14,5)        DEFAULT '0.00000',
T218_Total_Cubagem                                 decimal(10,6)        DEFAULT '0.000000',
T218_Peso_Bruto_Total                              decimal(10,3)        DEFAULT NULL,
T218_Valor_Total_Trib_Transparencia                decimal(15,6)        DEFAULT '0.000000',
T218_Flag_Alcada_Status                            char(1)              DEFAULT 'K',
T218_Flag_Alcada_Nivel                             char(1)              DEFAULT NULL,
T218_Valor_Pretendido_Alcada                       decimal(10,2)        DEFAULT NULL,
T218_Flag_EMail_Enviado                            char(1)              DEFAULT 'N',
T218_Data_Entrega                                  varchar(64)          DEFAULT NULL,
T218_Tipo_Transporte                               varchar(255)         DEFAULT NULL,
PRIMARY KEY (T218_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Itens cotação de compra
$cClausula  = <<<EOT
create table if not exists T219 (
T219_Id                                         int(10) unsigned         auto_increment,
T219_D059_Id                                    int(11) unsigned         DEFAULT NULL,
T219_D005_Id                                    int(10) unsigned         DEFAULT '0',
T219_D037_Id                                    int(10) unsigned         DEFAULT '0',
T219_D006_Id                                    int(10) unsigned         DEFAULT '0',
T219_T218_Id                                    int(10) unsigned         DEFAULT NULL,
T219_D024_Id                                    int(10) unsigned         DEFAULT '0',
T219_D009_Id                                    int(10) unsigned         DEFAULT '0',
T219_D004_Id                                    int(10) unsigned         DEFAULT '0',
T219_D040_Id                                    int(10) unsigned         DEFAULT '0',
T219_C004_Id                                    int(10) unsigned         DEFAULT '0',
T219_T189_Id                                    int(10) unsigned         DEFAULT NULL,
T219_D082_Id                                    int(10)                  DEFAULT NULL,
T219_T075_Id                                    int(10)                  DEFAULT NULL,
T219_T206_Id                                    int(10) unsigned         DEFAULT NULL,
T219_T010_Id                                    int(10) unsigned         DEFAULT NULL,
T219_T059_Id                                    int(10) unsigned         DEFAULT NULL,
T219_Item                                       int(4)                   DEFAULT '0',
T219_Quantidade                                 decimal(10,5)            DEFAULT NULL,
T219_Descricao_Produto                          varchar(250)              DEFAULT NULL,
T219_Observacao_Entrega                         varchar(30)              DEFAULT NULL,
T219_Valor_Preco_Unitario                       decimal(15,5)            DEFAULT '0.00000',
T219_Valor_Preco_Unitario_Confirmado            decimal(15,5)            DEFAULT '0.00000',
T219_Valor_Custo_Unitario                       decimal(19,2)            DEFAULT '0.00',
T219_Valor_Preco_Sem_Desconto_Unitario          decimal(15,5)            DEFAULT '0.00000',
T219_Valor_Desconto_Unitario                    decimal(19,5)            DEFAULT '0.00000',
T219_Valor_Desconto_Unitario_Confirmado         decimal(19,5)            DEFAULT '0.00000',
T219_Valor_Despesas_Unitario                    decimal(19,5)            DEFAULT '0.00000',
T219_Valor_Despesas_Unitario_Confirmado         decimal(19,5)            DEFAULT '0.00000',
T219_Valor_Custo_Final_Unitario                 decimal(19,5)            DEFAULT '0.00000',
T219_Valor_Custo_Final_Unitario_Confirmado      decimal(19,5)            DEFAULT '0.00000',
T219_Quantidade_Confirmada                      decimal(10,2)            DEFAULT NULL,
T219_Numero_Orcamento_Venda                     varchar(10)              DEFAULT NULL,
T219_Codigo_Produto                             varchar(30)              DEFAULT NULL,
T219_Codigo_Substituto                          varchar(25)              DEFAULT NULL,
T219_Codigo_Fornecedor                          varchar(6)               DEFAULT NULL,
T219_Codigo_Local                               varchar(10)              DEFAULT NULL,
T219_Valor_Total_Custo                          decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Preco_Sem_Desconto             decimal(20,2)            DEFAULT '0.00',
T219_Valor_Total_Custo_Confirmado               decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Preco                          decimal(20,2)            DEFAULT '0.00',
T219_Valor_Total_Preco_Confirmado               decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Preco_Sem_Desconto_Confirmado  decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Desconto                       decimal(20,10)           DEFAULT '0.0000000000',
T219_Valor_Total_Desconto_Confirmado            decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Despesas                       decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Despesas_Confirmado            decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Custo_Final                    decimal(10,2)            DEFAULT '0.00',
T219_Valor_Total_Custo_Final_Confirmado         decimal(10,2)            DEFAULT '0.00',
T219_IPV                                        decimal(10,4)            DEFAULT '0.0000',
T219_Percentual_Reducao_ICMS                    decimal(5,2)             DEFAULT '0.00',
T219_Valor_Base_ICMS                            decimal(10,2)            DEFAULT '0.00',
T219_Modalidade_ICMS                            int(1)                   DEFAULT '3',
T219_Valor_Base_ICMS_Confirmado                 decimal(10,2)            DEFAULT '0.00',
T219_Aliquota_ICMS                              decimal(5,2)             DEFAULT '0.00',
T219_Valor_ICMS                                 decimal(10,2)            DEFAULT '0.00',
T219_Aliquota_ICMS_Credito                      decimal(5,2)             DEFAULT '0.00',
T219_Valor_ICMS_Credito                         decimal(15,2)            DEFAULT '0.00',
T219_Percentual_Base_Operacao_Propria           decimal(5,2)             DEFAULT '0.00',
T219_Valor_ICMS_Confirmado                      decimal(10,2)            DEFAULT '0.00',
T219_Percentual_Reducao_IPI                     decimal(5,2)             DEFAULT '0.00',
T219_Valor_Base_IPI                             decimal(10,2)            DEFAULT '0.00',
T219_Valor_Base_IPI_Confirmado                  decimal(10,2)            DEFAULT '0.00',
T219_Aliquota_IPI                               decimal(5,2)             DEFAULT '0.00',
T219_Valor_IPI                                  decimal(10,2)            DEFAULT '0.00',
T219_Situacao_Tributaria_IPI                    int(2) unsigned zerofill DEFAULT NULL,
T219_Valor_IPI_Confirmado                       decimal(10,2)            DEFAULT '0.00',
T219_Marca                                      varchar(20)              DEFAULT NULL,
T219_Peso_Unitario                              decimal(10,3)            DEFAULT '0.000',
T219_Peso_Total                                 decimal(10,3)            DEFAULT '0.000',
T219_Peso_Total_Confirmado                      decimal(10,3)            DEFAULT '0.000',
T219_Ultimo_Preco                               varchar(250)             DEFAULT NULL,
T219_Ultimo_Preco_Confirmado                    varchar(250)             DEFAULT NULL,
T219_Flag_Excluir                               int(1)                   DEFAULT '2',
T219_Flag_Preco_Especial                        varchar(1)               DEFAULT NULL,
T219_Classificacao_Fiscal                       varchar(12)              DEFAULT NULL,
T219_Valor_Ultimo_Preco_Consulta                decimal(10,2)            DEFAULT '0.00',
T219_Valor_Ultimo_Preco_Confirmado              decimal(10,2)            DEFAULT '0.00',
T219_Data_Ultimo_Preco_Consulta                 date                     DEFAULT NULL,
T219_Data_Ultimo_Preco_Confirmado               date                     DEFAULT NULL,
T219_Obs_Preco                                  varchar(30)              DEFAULT NULL,
T219_Observacao                                 text                     DEFAULT NULL,
T219_Flag_Comprar                               varchar(1)               DEFAULT NULL,
T219_Flag_ST                                    varchar(1)               DEFAULT 'N',
T219_ICMS_Inter_Estadual                        decimal(5,2)             DEFAULT '0.00',
T219_ST_ICMS_Interno                            decimal(5,2)             DEFAULT '0.00',
T219_ST_VA                                      decimal(10,2)            DEFAULT '0.00',
T219_Valor_Base_ST                              decimal(10,2)            DEFAULT '0.00',
T219_Percentual_Reducao_ICMS_ST                 decimal(5,2)             DEFAULT '0.00',
T219_Modalidade_ICMS_Substituicao               int(1)                   DEFAULT '4',
T219_Valor_ST                                   decimal(10,2)            DEFAULT '0.00',
T219_Valor_Base_ST_Confirmado                   decimal(10,2)            DEFAULT '0.00',
T219_Valor_ST_Confirmado                        decimal(10,2)            DEFAULT '0.00',
T219_Valor_Base_ICMS_Substituicao_Retido        decimal(15,2)            DEFAULT '0.00',
T219_Valor_ICMS_Substituicao_Retido             decimal(15,2)            DEFAULT '0.00',
T219_Valor_Base_ICMS_Substituicao_Destino       decimal(15,2)            DEFAULT '0.00',
T219_Valor_ICMS_Substituicao_Destino            decimal(15,2)            DEFAULT '0.00',
T219_UF_ICMS_Substituicao_Devido                char(2)                  DEFAULT NULL,
T219_Flag_Situacao_Tributaria                   varchar(4)               DEFAULT NULL,
T219_Situacao_Tributaria_PIS                    varchar(2)               DEFAULT '1',
T219_Flag_Isento_PIS                            enum('S','N')            DEFAULT 'N',
T219_Valor_Base_PIS                             decimal(20,2)            DEFAULT '0.00',
T219_Aliquota_PIS                               decimal(5,2)             DEFAULT '0.00',
T219_Valor_PIS                                  decimal(10,2)            DEFAULT '0.00',
T219_Situacao_Tributaria_COFINS                 varchar(2)               DEFAULT '1',
T219_Flag_Isento_COFINS                         enum('S','N')            DEFAULT 'N',
T219_Valor_Base_COFINS                          decimal(20,2)            DEFAULT '0.00',
T219_Aliquota_COFINS                            decimal(5,2)             DEFAULT '0.00',
T219_Valor_COFINS                               decimal(10,2)            DEFAULT '0.00',
T219_Reordenar                                  int(1)                   DEFAULT '1',
T219_Flag_Cancelado                             varchar(1)               DEFAULT 'N',          
T219_Valor_Preco_Original                       decimal(10,2)            DEFAULT NULL,
T219_Percentual_Desconto                        decimal(10,5)            DEFAULT NULL,
T219_Quantidade_Estoque                         decimal(12,2)            DEFAULT NULL,
T219_Valor_Custo_Compra_Unitario                decimal(15,5)            DEFAULT NULL,
T219_Valor_Custo_Tabela_Unitario                decimal(10,5)            DEFAULT NULL,
T219_Numero_Pedido_Compra                       varchar(15)              DEFAULT NULL,
T219_Item_Pedido_Compra                         int(6)                   DEFAULT NULL,
T219_D009_Id_Loja                               int(10) unsigned         DEFAULT NULL,
T219_Flag_Dividir                               char(1)                  DEFAULT 'N',
T219_ST_ICMS_Interno_MVA                        decimal(14,2)            DEFAULT NULL,
T219_ICMS_Inter_Estadual_MVA                    decimal(14,2)            DEFAULT NULL,
T219_Valor_Total_Agregados                      decimal(10,2)            DEFAULT '0.00',
T219_Liberou_Alcada_Saldo_Futuro                decimal(10,2)            DEFAULT '0.00',
T219_Valor_Preco_Original_Bruto                 decimal(14,5)            DEFAULT '0.00000',
T219_Valor_Desconto_Unitario_Bruto              decimal(14,5)            DEFAULT '0.00000',
T219_Percentual_Desconto_Bruto                  decimal(14,5)            DEFAULT '0.00000',
T219_Valor_Comissao                             decimal(14,5)            DEFAULT '0.00000',
T219_Percentual_Comissao                        decimal(14,5)            DEFAULT '0.00000',
T219_Valor_Preco_Bruto                          decimal(10,2)            DEFAULT NULL,
T219_Valor_Frete_Unitario                       decimal(19,2)            DEFAULT NULL,
T219_Valor_Frete_Total                          decimal(19,2)            DEFAULT NULL,
T219_Valor_Total_Despesas_Acessorias            decimal(19,2)            DEFAULT NULL,
T219_Valor_Despesas_Acessorias_Unitario         decimal(19,2)            DEFAULT NULL,
T219_Cubagem_Unitaria                           decimal(10,6)            DEFAULT '0.000000',
T219_Total_Cubagem                              decimal(10,6)            DEFAULT '0.000000',
T219_Peso_Bruto                                 decimal(10,3)            DEFAULT NULL,
T219_Peso_Bruto_Total                           decimal(10,3)            DEFAULT NULL,
T219_Valor_Trib_Transparencia                   decimal(15,6)            DEFAULT '0.000000',
T219_Percentual_Trib_Transparencia              decimal(15,2)            DEFAULT '0.00',
T219_Flag_Pre_Cadastro                          varchar(1)               DEFAULT NULL,
T219_Flag_Gerar_OC                              varchar(1)               DEFAULT NULL,
T219_Quantidade_Forn                            int(10)                  DEFAULT NULL,
T219_Valor_Custo_Unitario_Forn                  decimal(14,2)            DEFAULT NULL,
T219_IPI_Forn                                   decimal(14,2)            DEFAULT NULL,
T219_Hora_Email_Forn                            datetime                 DEFAULT NULL,
T219_Prazo_Entrega_Dias                         varchar(255)             DEFAULT NULL,
T219_Flag_Indisponivel                          varchar(1)               DEFAULT 'N',
T219_Flag_Excluido                              char(1)                  DEFAULT 'N',
T219_Flag_Email_Enviado                         char(1)                  DEFAULT 'N',
T219_Flag_Origem_Produto                        char(1)                  DEFAULT 'N',
PRIMARY KEY (T219_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Pagamento Cotação de compra
$cClausula  = <<<EOT
create table if not exists T220 (
T220_Id                      int(11)        auto_increment,
T220_T218_Id                 int(11)        DEFAULT '0',
T220_D027_Id                 int(11)        DEFAULT '0',
T220_Numero_Parcela          int(3)         DEFAULT '0',
T220_Prazos_Dias             int(5)         DEFAULT '0',
T220_Data_Vencimento         date           DEFAULT NULL,
T220_Valor_Parcela           decimal(12,2)  DEFAULT '0.00',
T220_Taxa_Juros              decimal(12,2)  DEFAULT NULL,
T220_Valor_Juros             decimal(12,2)  DEFAULT NULL,
T220_Valor_Parcela_Original  decimal(12,2)  DEFAULT NULL,
PRIMARY KEY (T220_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}


// Documentos Cotação de compra
$cClausula  = <<<EOT
create table if not exists T221 (
T221_Id                int(10) unsigned auto_increment,
T221_T218_Id           int(10) unsigned DEFAULT NULL,
T221_C007_Id           int(10) unsigned DEFAULT NULL,
T221_Nome_Arquivo      varchar(200)     DEFAULT NULL,
T221_Descricao         text             DEFAULT NULL,
T221_Data_Hora_Upload  datetime         DEFAULT NULL,
PRIMARY KEY (T221_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Cotação Trocas
$cClausula  = <<<EOT
create table if not exists T222 (
T222_Id                    int(10) unsigned auto_increment,
T222_T007_Id               int(10) unsigned DEFAULT NULL,
T222_C004_Id               int(10) unsigned DEFAULT NULL,
T222_D009_Id               int(10) unsigned DEFAULT NULL,
T222_Quantidade            decimal(12,5)    DEFAULT '0.00000',
T222_Valor_Preco_Unitario  decimal(20,5)    DEFAULT '0.00000',
T222_Flag_Documento        char(1)          DEFAULT '0',
T222_Numero_Nota_Fiscal    varchar(12)      DEFAULT NULL,
T222_Observacao            varchar(200)     DEFAULT NULL,
T222_T218_Id               int(10) unsigned DEFAULT NULL,
T222_T224_Id               int(10) unsigned DEFAULT NULL,
T222_C007_Id_Inspecao      int(10) unsigned DEFAULT NULL,
T222_Operacao              char(1)          DEFAULT NULL,
T222_Data_Inspecao         datetime         DEFAULT NULL,
T222_Flag_Dividir          char(1)          DEFAULT 'N',
T222_Flag_Comissao         char(1)          DEFAULT 'N',
T222_Quantidade_Separado   decimal(10,2)    DEFAULT NULL,
PRIMARY KEY (T222_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Cotação Serviços
$cClausula  = <<<EOT
create table if not exists T223 (
T223_Id                     int(11)           auto_increment,
T223_T218_Id                int(10) unsigned  DEFAULT NULL,
T223_D112_Id                int(11)           DEFAULT NULL,
T223_D110_Id                int(11)           DEFAULT NULL,
T223_Titulo                 varchar(200)      DEFAULT NULL,
T223_Descricao              varchar(500)      DEFAULT NULL,
T223_Tempo_Estimado         time              DEFAULT NULL,
T223_Quantidade             int(11)           DEFAULT NULL,
T223_Quantidade_Confirmada  int(11)           DEFAULT NULL,
T223_Valor_Unitario         decimal(14,2)     DEFAULT NULL,
T223_Valor_Total            decimal(14,2)     DEFAULT NULL,
T223_Hora_Inicio            datetime          DEFAULT NULL,
T223_Hora_Fim               datetime          DEFAULT NULL,
T223_D116_Id                int(10) unsigned  DEFAULT NULL,
T223_T219_Id                int(10) unsigned  DEFAULT NULL,
T223_Flag_Dividir           char(1),
PRIMARY KEY (T223_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

//**************************** ORDEM DE COMPRA ****

// Cabeçalho Ordem de compra
$cClausula  = <<<EOT
create table if not exists T224 (
T224_Id                                      int(10) unsigned    auto_increment,
T224_T064_Id                                 bigint(20) unsigned DEFAULT  NULL,
T224_C008_Id                                 int(11)             DEFAULT  NULL,
T224_D027_Id_Entrada                         int(10) unsigned    DEFAULT  NULL,
T224_D027_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D047_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D024_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D021_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D022_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D036_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_T218_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D006_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_C007_Id_Vendedor_Interno                int(10) unsigned    DEFAULT  NULL,
T224_C007_Id_Vendedor_Externo                int(10) unsigned    DEFAULT  NULL,
T224_C007_Id_IPV_Liberado                    int(10) unsigned    DEFAULT  NULL,
T224_C007_Id_Devolucao                       int(5)              DEFAULT  '0' ,
T224_C004_Id                                 int(11)             DEFAULT  '0' ,
T224_Pedido_Ecommerce                        varchar(20)         DEFAULT  NULL,
T224_Numero_Pedido_Venda                     int(10)             DEFAULT  NULL,
T224_Data_Emissao                            date                DEFAULT  NULL,
T224_Data_Saida                              date                DEFAULT  '0000-00-00',
T224_Hora_Saida                              time                DEFAULT  '00:00:00',
T224_Data_Entrega                            date                DEFAULT  NULL,
T224_Valor_Desconto                          decimal(10,2)       DEFAULT  '0.00',
T224_Percentual_Desconto                     decimal(30,20)      DEFAULT  NULL,
T224_Valor_Percentual_Desconto               decimal(10,2)       DEFAULT  '0.00',
T224_Observacao_1                            text                DEFAULT  NULL,
T224_Observacao_2                            varchar(70)         DEFAULT  NULL,
T224_Observacao_3                            varchar(70)         DEFAULT  NULL,
T224_Prazos_Pagamentos                       varchar(40)         DEFAULT  NULL,
T224_Nome_Vendedor                           varchar(30)         DEFAULT  NULL,
T224_Mao_Obra_1                              text                DEFAULT  NULL,
T224_Mao_Obra_2                              text                DEFAULT  NULL,
T224_Mao_Obra_3                              text                DEFAULT  NULL,
T224_Mao_Obra_4                              text                DEFAULT  NULL,
T224_Flag_Revenda_Consumidor                 varchar(1)          DEFAULT  NULL,
T224_Valor_Mao_Obra                          decimal(10,2)       DEFAULT  '0.00',
T224_NF_ACP                                  varchar(1)          DEFAULT  NULL,
T224_Valor_Dolar                             decimal(10,4)       DEFAULT  '0.0000',
T224_Flag_ACP                                varchar(1)          DEFAULT  NULL,
T224_Prazo_1                                 int(4)              DEFAULT  '0',
T224_Prazo_2                                 int(4)              DEFAULT  '0',
T224_Prazo_3                                 int(4)              DEFAULT  '0',
T224_Prazo_4                                 int(4)              DEFAULT  '0',
T224_Prazo_5                                 int(4)              DEFAULT  '0',
T224_Prazo_6                                 int(4)              DEFAULT  '0',
T224_Prazo_7                                 int(4)              DEFAULT  '0',
T224_Flag_Frete                              int(1)              DEFAULT  NULL,
T224_NF_Observacao                           text                DEFAULT  NULL,
T224_Numero_NF_Devolucao                     varchar(12)         DEFAULT  NULL,
T224_Data_Emissao_NF_Devolucao               date                DEFAULT  NULL,
T224_Aliquota_ICMS_NF_Devolucao              decimal(5,2)        DEFAULT  '0.00',
T224_Valor_Frete                             decimal(19,2)       DEFAULT  '0.00',
T224_Flag_Situacao                           varchar(1)          DEFAULT  NULL,
T224_Valor_Despesas_Acessorias               decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Desconto_NF                       decimal(12,6)       DEFAULT  '0.000000',
T224_Valor_Encargos_NF                       decimal(12,6)       DEFAULT  '0.000000',
T224_Observacao_Nota_Fiscal                  text                DEFAULT  NULL,
T224_Numero_Orcamento                        int(10)             DEFAULT  NULL,
T224_Codigo_Cliente                          varchar(6)          DEFAULT  NULL,
T224_Codigo_Representante                    varchar(6)          DEFAULT  NULL,
T224_Codigo_Transportadora                   varchar(6)          DEFAULT  NULL,
T224_Codigo_Natureza                         int(5)              DEFAULT  '0',
T224_Codigo_Usuario                          int(4)              DEFAULT  '0',
T224_IPV                                     decimal(10,4)       DEFAULT  '0.0000',
T224_IPV_Cliente                             decimal(10,4)       DEFAULT  '0.0000',
T224_IPV_Data_Liberado                       datetime            DEFAULT  NULL,
T224_IPV_Liberado                            decimal(10,4)       DEFAULT  '0.0000',
T224_IPV_Normal                              decimal(10,4)       DEFAULT  '0.0000',
T224_Valor_Total_Custo                       decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total_Produtos                    decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total_Produtos_Sem_Desconto       decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total_Custo_Final                 decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total                             decimal(10,2)       DEFAULT  '0.00',
T224_Percentual_Comissao                     decimal(5,2)        DEFAULT  '0.00',
T224_Flag_Travar_Pedido                      int(1)              DEFAULT  '2',
T224_Flag_Almoxarifado                       varchar(1)          DEFAULT  'N',
T224_Flag_Status                             int(2)              DEFAULT  '0',
T224_Flag_Devolucao                          varchar(1)          DEFAULT  'N',
T224_Flag_Tipo_Devolucao                     varchar(10)         DEFAULT  NULL,
T224_Percentual_Reducao_ICMS                 decimal(5,2)        DEFAULT  '0.00',
T224_Valor_Total_Base_ICMS                   decimal(10,2)       DEFAULT  '0.00',
T224_Aliquota_ICMS                           decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total_ICMS                        decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Base_ICMS_Substituicao            decimal(14,2)       DEFAULT  '0.00',
T224_Valor_ICMS_Substituicao                 decimal(14,2)       DEFAULT  '0.00',
T224_Valor_ICMS_Substituicao_Retencao        decimal(14,2)       DEFAULT  '0.00',
T224_Percentual_Reducao_IPI                  decimal(5,2)        DEFAULT  '0.00',
T224_Valor_Total_Base_IPI                    decimal(10,2)       DEFAULT  '0.00',
T224_Aliquota_IPI                            decimal(5,2)        DEFAULT  '0.00',
T224_Valor_Total_IPI                         decimal(10,2)       DEFAULT  '0.00',
T224_Aliquota_ISS                            decimal(5,2)        DEFAULT  '0.00',
T224_Valor_ISS                               decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total_PIS                         decimal(10,2)       DEFAULT  '0.00',
T224_Valor_Total_COFINS                      decimal(10,2)       DEFAULT  '0.00',
T224_Data_Separacao_Pecas                    datetime            DEFAULT  NULL,
T224_Usuario_Pecas                           int(11)             DEFAULT  NULL,
T224_Liberacao_Credito_Id                    int(11)             DEFAULT  '0',
T224_Peso_Total                              decimal(10,3)       DEFAULT  '0.000',
T224_Observacao                              text                DEFAULT  NULL,
T224_Fax                                     varchar(30)         DEFAULT  NULL,
T224_Nome_Comprador                          varchar(30)         DEFAULT  NULL,
T224_E_mail_Comprador                        varchar(60)         DEFAULT  NULL,
T224_Prazo_Entrega                           varchar(40)         DEFAULT  NULL,
T224_Observacao_Pedido_Perdido               text                DEFAULT  NULL,
T224_Nome_Transportadora                     varchar(35)         DEFAULT  NULL,
T224_Transportadora_Placa_Veiculo            varchar(8)          DEFAULT  NULL,
T224_Estado_Veiculo                          char(2)             DEFAULT  NULL,
T224_Valor_Entrada                           decimal(10,2)       DEFAULT  '0.00',
T224_Numero_Parcelas                         int(2)              DEFAULT  '0',
T224_Numero_Dias_Entrada                     int(3)              DEFAULT  '0',
T224_Numero_Dias                             int(3)              DEFAULT  '0',
T224_Quantidade_Volumes                      int(15) unsigned    DEFAULT  '0',
T224_Especie                                 varchar(20)         DEFAULT  NULL,
T224_Marca                                   varchar(20)         DEFAULT  NULL,
T224_Numero_Caixas                           varchar(20)         DEFAULT  NULL,
T224_Peso_Liquido                            decimal(10,3)       DEFAULT  '0.000',
T224_Peso_Bruto                              decimal(10,3)       DEFAULT  '0.000',
T224_Flag_Considerar_Dias_Entrada            varchar(1)          DEFAULT  'S',
T224_Motivo_Nao_Liberado                     tinytext            DEFAULT  NULL,
T224_Flag_Reenviado_Almoxarifado             enum('S','N')       DEFAULT  'N',
T224_Alteracoes_Reenviado_Almoxarifado       text                DEFAULT  NULL,
T224_Data_Hora_Almoxarifado                  datetime            DEFAULT  NULL,
T224_Flag_Separacao                          varchar(1)          DEFAULT  NULL,
T224_Flag_Separacao_Liberar                  varchar(1)          DEFAULT  NULL,
T224_Numero_Nota_Manual                      varchar(20)         DEFAULT  NULL,
T224_Flag_Expedicao                          int(2)              DEFAULT  NULL,
T224_Flag_Dividido                           varchar(1)          DEFAULT  NULL,
T224_Observacao_Separacao                    varchar(100)        DEFAULT  NULL,
T224_Flag_Lista_Preco                        varchar(1),
T224_Flag_Aguardar_Liberacao                 int(1)              DEFAULT  '0',
T224_Observacao_Comercial                    text                DEFAULT  NULL,
T224_ST_VA                                   decimal(10,2)       DEFAULT  '0.00',
T224_ST_ICMS_Interno                         decimal(5,2)        DEFAULT  '0.00',
T224_ICMS_Inter_Estadual                     decimal(5,2)        DEFAULT  '0.00',
T224_Origem_UF                               char(2)             DEFAULT  NULL,
T224_Origem_Ano_Mes                          char(4)             DEFAULT  NULL,
T224_Origem_Cnpj                             varchar(14)         DEFAULT  NULL,
T224_Origem_Modelo                           char(2)             DEFAULT  NULL,
T224_Origem_Serie                            int(3)              DEFAULT  NULL,
T224_Origem_Chave_Acesso                     varchar(44)         DEFAULT  NULL,
T224_Flag_Imprimir_Romaneio                  enum('S','N')       DEFAULT  'N',
T224_Data_Impressao_Romaneio                 datetime            DEFAULT  '0000-00-00 00:00:00',
T224_Flag_Importar_Pendente                  enum('S','N')       DEFAULT  'N',
T224_Estoque_Box                             varchar(20),
T224_D018_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_D013_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_Observacao_Fatura_Emergencia            text                DEFAULT  NULL,
T224_Observacao_Pagamento                    text                DEFAULT  NULL,
T224_Valor_Troca                             decimal(10,2)       DEFAULT  NULL,
T224_D024_Id_Nfe                             int(10) unsigned    DEFAULT  NULL,
T224_D024_Id_Contas                          int(10) unsigned    DEFAULT  NULL,
T224_Ordem_Compra                            varchar(40)         DEFAULT  NULL,
T224_D022_Id_Default                         int(10) unsigned    DEFAULT  NULL,
T224_Pgto_Parcelas                           varchar(10)         DEFAULT  NULL,
T224_Pgto_Primeiro_Vcto_Tipo                 varchar(10)         DEFAULT  NULL,
T224_Pgto_Primeiro_Vcto_Valor                varchar(10)         DEFAULT  NULL,
T224_Pgto_Dias_Vcto_Tipo                     varchar(10)         DEFAULT  NULL,
T224_Pgto_Dias_Vcto_Valor                    varchar(10)         DEFAULT  NULL,
T224_Pgto_Portador                           varchar(10)         DEFAULT  NULL,
T224_Pgto_Data                               datetime            DEFAULT  NULL,
T224_Pgto_C007_Id                            int(10) unsigned    DEFAULT  NULL,
T224_D116_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_Valor_Total_Servicos                    decimal(10,2)       DEFAULT  NULL,
T224_Liberou_Alcada_C007_Id                  int(11)             DEFAULT  NULL,
T224_Liberou_Alcada_Data                     datetime            DEFAULT  NULL,
T224_Liberou_Alcada_IPV                      decimal(10,4)       DEFAULT  NULL,
T224_Liberou_Alcada_Valor_Total              decimal(10,2)       DEFAULT  NULL,
T224_Flag_Alcada                             varchar(1)          DEFAULT  NULL,
T224_Liberou_Alcada_Prazo_Medio              decimal(10,4)       DEFAULT  NULL,
T224_Liberou_Alcada_Desconto                 decimal(10,2)       DEFAULT  NULL,
T224_Liberou_Alcada_Observacao_Nao_Liberado  text                DEFAULT  NULL,
T224_Valor_Desconto_Manual                   decimal(14,2)       DEFAULT  NULL,
T224_Flag_Status_Liberacao                   varchar(1)          DEFAULT  NULL,
T224_C007_Id_Separou                         int(10) unsigned    DEFAULT  NULL,
T224_Hora_Inicio_Servico                     varchar(30)         DEFAULT  NULL,
T224_Hora_Fim_Servico                        varchar(30)         DEFAULT  NULL,
T224_Flag_Aguardando_Entrega                 char(1)             DEFAULT  'S',
T224_Flag_Contribuinte                       int(5)              DEFAULT  NULL,
T224_Flag_Esta_No_Caixa                      varchar(1)          DEFAULT  'S',
T224_Motivo_Pedido_Liberacao                 text                DEFAULT  NULL,
T224_Data_Finalizado_Caixa                   date                DEFAULT  NULL,
T224_Flag_Status_Servicos                    int(1)              DEFAULT  '1',
T224_Flag_Status_Financeiro                  int(1)              DEFAULT  '1',
T224_T134_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_Valor_Total_Agregados                   decimal(10,2)       DEFAULT  '0.00',
T224_Flag_Coleta                             char(1)             DEFAULT  'N',
T224_T224_Id_Origem                          int(10) unsigned    DEFAULT  NULL,
T224_Valor_Total_Comissao                    decimal(14,5)       DEFAULT  '0.00000',
T224_T208_Id                                 int(10) unsigned    DEFAULT  NULL,
T224_Flag_Duplicar                           char(1)             DEFAULT  'N',
T224_T224_Id_Agrupado                        int(10)             DEFAULT  '0',
T224_C007_Id_Conferiu                        int(10)             DEFAULT  NULL,
T224_T007_Id                                 int(10)             DEFAULT  NULL,
T224_Flag_Gerar_NF                           char(1)             DEFAULT  NULL,
T224_C007_Id_Embalagem                       int(10)             DEFAULT  NULL,
T224_Codigo_Rastreio                         varchar(30),
T224_Custo_Frete                             decimal(14,2)       DEFAULT  '0.00',
T224_C007_Gerar_NF                           int(10)             DEFAULT  '0',
T224_Processo_Id                             int(10)             DEFAULT  NULL,
T224_Processo_Log                            varchar(255)        DEFAULT  NULL,
T224_Total_Cubagem                           decimal(10,6)       DEFAULT  '0.000000',
T224_Peso_Bruto_Total                        decimal(10,3)       DEFAULT  NULL,
T224_Flag_Totalizar_Peso                     varchar(1)          DEFAULT  'N',
T224_Valor_Total_Trib_Transparencia          decimal(15,6)       DEFAULT  '0.000000',
PRIMARY KEY (T224_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Itens Ordem de compra
$cClausula  = <<<EOT
create table if not exists T225 (
T225_Id                                    int(10) unsigned          auto_increment,
T225_D059_Id                               int(11) unsigned          DEFAULT NULL,
T225_T008_Id                               int(10) unsigned          DEFAULT NULL,
T225_T225_Id                               int(10) unsigned          DEFAULT NULL,
T225_D005_Id                               int(10) unsigned          DEFAULT NULL,
T225_D037_Id                               int(10) unsigned          DEFAULT NULL,
T225_D006_Id                               int(10) unsigned          DEFAULT NULL,
T225_T219_Id                               int(10) unsigned          DEFAULT NULL,
T225_D024_Id                               int(10) unsigned          DEFAULT NULL,
T225_T224_Id                               int(10) unsigned          DEFAULT NULL,
T225_C007_Id_Separou                       int(11) unsigned          DEFAULT NULL,
T225_D009_Id                               int(10) unsigned          DEFAULT NULL,
T225_D004_Id                               int(10) unsigned          DEFAULT NULL,
T225_D040_Id                               int(10) unsigned          DEFAULT NULL,
T225_T014_Id                               int(10) unsigned          DEFAULT NULL,
T225_C004_Id                               int(10) unsigned          DEFAULT '0',
T225_Item                                  int(4)                    DEFAULT '0',
T225_Numero_Orcamento                      varchar(10)               DEFAULT NULL,
T225_Quantidade                            decimal(15,5)             DEFAULT NULL,
T225_Data_Entrega                          date                      DEFAULT NULL,
T225_Valor_Custo_Unitario                  decimal(19,5)             DEFAULT '0.00000',
T225_Valor_Preco_Sem_Desconto_Unitario     decimal(19,5)             DEFAULT '0.00000',
T225_Valor_Desconto_Unitario               decimal(20,10)            DEFAULT '0.0000000000',
T225_Valor_Despesas_Unitario               decimal(19,5)             DEFAULT '0.00000',
T225_Valor_Custo_Final_Unitario            decimal(19,5)             DEFAULT '0.00000',
T225_Valor_Frete_Unitario                  decimal(20,10)            DEFAULT '0.0000000000',
T225_Valor_Despesas_Acessorias_Unitario    decimal(20,10)            DEFAULT '0.0000000000',
T225_Valor_Preco_Unitario                  decimal(19,5)             DEFAULT '0.00000',
T225_Descricao_Produto                     varchar(250)               DEFAULT NULL,
T225_Codigo_Substituto                     varchar(18)               DEFAULT NULL,
T225_Flag_Status                           varchar(1),
T225_Numero_Pedido_Venda                   varchar(6)                DEFAULT NULL,
T225_Codigo_Fornecedor                     varchar(6)                DEFAULT NULL,
T225_Codigo_Local                          varchar(10)               DEFAULT NULL,
T225_Codigo_Produto                        varchar(30)               DEFAULT NULL,
T225_Valor_Total_Custo                     decimal(10,2)             DEFAULT '0.00',
T225_Valor_Total_Preco_Sem_Desconto        decimal(20,2)             DEFAULT '0.00',
T225_Valor_Total_Preco                     decimal(20,2)             DEFAULT '0.00',
T225_Valor_Total_Desconto                  decimal(20,10)            DEFAULT '0.0000000000',
T225_Valor_Total_Despesas                  decimal(20,5)             DEFAULT '0.00000',
T225_Valor_Total_Custo_Final               decimal(10,2)             DEFAULT '0.00',
T225_Valor_Total_Frete                     decimal(20,10)            DEFAULT '0.0000000000',
T225_Valor_Total_Despesas_Acessorias       decimal(20,10)            DEFAULT '0.0000000000',
T225_IPV                                   decimal(10,4)             DEFAULT NULL,
T225_Percentual_Reducao_ICMS               decimal(5,2)              DEFAULT '0.00',
T225_Valor_Base_ICMS                       decimal(10,2)             DEFAULT '0.00',
T225_Modalidade_ICMS                       int(1)                    DEFAULT '3',
T225_Aliquota_ICMS                         decimal(5,2)              DEFAULT '0.00',
T225_Valor_ICMS                            decimal(15,2)             DEFAULT '0.00',
T225_Aliquota_ICMS_Credito                 decimal(5,2)              DEFAULT '0.00',
T225_Valor_ICMS_Credito                    decimal(15,2)             DEFAULT '0.00',
T225_Percentual_Base_Operacao_Propria      decimal(5,2)              DEFAULT '0.00',
T225_Valor_Agregado                        decimal(20,2)             DEFAULT '0.00',
T225_Valor_Base_ICMS_Substituicao          decimal(20,2)             DEFAULT '0.00',
T225_Modalidade_ICMS_Substituicao          int(1)                    DEFAULT '4',
T225_Percentual_Reducao_ICMS_ST            decimal(5,2)              DEFAULT '0.00',
T225_Aliquota_ICMS_ST                      decimal(5,2)              DEFAULT '0.00',
T225_Valor_ICMS_Substituicao               decimal(20,2)             DEFAULT '0.00',
T225_Valor_ICMS_Substituicao_Retencao      decimal(20,2)             DEFAULT '0.00',
T225_Valor_Base_ICMS_Substituicao_Retido   decimal(15,2)             DEFAULT '0.00',
T225_Valor_ICMS_Substituicao_Retido        decimal(15,2)             DEFAULT '0.00',
T225_Valor_Base_ICMS_Substituicao_Destino  decimal(15,2)             DEFAULT '0.00',
T225_Valor_ICMS_Substituicao_Destino       decimal(15,2)             DEFAULT '0.00',
T225_UF_ICMS_Substituicao_Devido           char(2)                   DEFAULT NULL,
T225_Percentual_Reducao_IPI                decimal(5,2)              DEFAULT '0.00',
T225_Valor_Base_IPI                        decimal(10,2)             DEFAULT '0.00',
T225_Aliquota_IPI                          decimal(5,2)              DEFAULT '0.00',
T225_Valor_IPI                             decimal(10,2)             DEFAULT '0.00',
T225_Situacao_Tributaria_IPI               int(2) unsigned zerofill  DEFAULT NULL,
T225_Marca                                 varchar(20)               DEFAULT NULL,
T225_Peso_Unitario                         decimal(10,3)             DEFAULT '0.000',
T225_Peso_Total                            decimal(10,3)             DEFAULT '0.000',
T225_Ultimo_Preco                          varchar(50)               DEFAULT NULL,
T225_Ultimo_Preco_Confirmado               varchar(50)               DEFAULT NULL,
T225_Flag_Excluir                          int(1)                    DEFAULT '2',
T225_Flag_Preco_Especial                   varchar(1)                DEFAULT NULL,
T225_Classificacao_Fiscal                  varchar(12)               DEFAULT NULL,
T225_Flag_Comprar                          varchar(1)                DEFAULT NULL,
T225_T225_Id_Dividir                       int(10)                   DEFAULT NULL,
T225_Quantidade_Dividir                    decimal(10,2)             DEFAULT NULL,
T225_Quantidade_Separacao                  decimal(10,2)             DEFAULT NULL,
T225_Quantidade_Etiqueta                   decimal(10,3)             DEFAULT '0.000',
T225_Quantidade_Itens_Etiqueta             decimal(10,3)             DEFAULT '0.000',
T225_Quantidade_Dividido                   decimal(10,2)             DEFAULT NULL,
T225_Flag_ST                               varchar(1)                DEFAULT 'N',
T225_ICMS_Inter_Estadual                   decimal(5,2)              DEFAULT '0.00',
T225_ST_ICMS_Interno                       decimal(5,2)              DEFAULT '0.00',
T225_ST_VA                                 decimal(10,2)             DEFAULT '0.00',
T225_Situacao_Tributaria_PIS               varchar(2)                DEFAULT '01',
T225_Flag_Isento_PIS                       enum('S','N')             DEFAULT 'N',
T225_Valor_Base_PIS                        decimal(20,2)             DEFAULT '0.00',
T225_Aliquota_PIS                          decimal(5,2)              DEFAULT '0.00',
T225_Valor_PIS                             decimal(10,2)             DEFAULT '0.00',
T225_Situacao_Tributaria_COFINS            varchar(2)                DEFAULT '01',
T225_Flag_Isento_COFINS                    enum('S','N')             DEFAULT 'N',
T225_Valor_Base_COFINS                     decimal(20,2)             DEFAULT '0.00',
T225_Aliquota_COFINS                       decimal(5,2)              DEFAULT '0.00',
T225_Valor_COFINS                          decimal(10,2)             DEFAULT '0.00',
T225_Flag_Situacao_Tributaria              varchar(4)                DEFAULT NULL,
T225_T099_Id                               int(11)                   DEFAULT '0',
T225_Quantidade_Comprar                    decimal(10,3)             DEFAULT '0.000',
T225_Reordenar                             int(1)                    DEFAULT '1',
T225_Flag_Tipo_Produto                     varchar(2)                DEFAULT NULL,
T225_D049_Id                               int(11) unsigned          DEFAULT NULL,
T225_Quantidade_Estoque                    decimal(12,2)             DEFAULT NULL,
T225_Observacao_Divergencia                varchar(100),
T225_Estoque_BOX                           varchar(20) ,
T225_Flag_Faturar                          varchar(1)                DEFAULT 'N',
T225_D009_Id_Comprar                       int(10)                   DEFAULT NULL,
T225_Flag_Cotar                            varchar(1)                DEFAULT 'N',
T225_Quantidade_Estoque_Padrao             decimal(12,2)             DEFAULT NULL,
T225_Flag_Divergencia                      varchar(1)                DEFAULT 'N',
T225_Flag_Dividir                          varchar(1)                DEFAULT 'N',
T225_Observacao_Compra                     varchar(100),
T225_Data_Separacao                        datetime                  DEFAULT '0000-00-00 00:00:00',
T225_Valor_Custo_Compra_Unitario           decimal(10,5)             DEFAULT NULL,
T225_Valor_Custo_Tabela_Unitario           decimal(10,5)             DEFAULT NULL,
T225_Numero_Pedido_Compra                  varchar(15)               DEFAULT NULL,
T225_Item_Pedido_Compra                    int(6)                    DEFAULT NULL,
T225_D082_Id                               int(10) unsigned          DEFAULT NULL,
T225_Valor_Preco_Original                  decimal(14,2)             DEFAULT NULL,
T225_Percentual_Desconto                   decimal(14,2)             DEFAULT NULL,
T225_Observacao                            text                      DEFAULT NULL,
T225_Quantidade_Entregar_Temp              int(4)                    DEFAULT '0',
T225_Retirou_Qtd                           decimal(20,5)             DEFAULT NULL,
T225_Flag_Comissao                         char(1)                   DEFAULT 'N',
T225_T075_Id                               int(10) unsigned          DEFAULT NULL,
T225_Valor_Total_Agregados                 decimal(10,2)             DEFAULT '0.00',
T225_Liberou_Alcada_Saldo_Futuro           decimal(10,2)             DEFAULT '0.00',
T225_D009_Id_Kit                           int(10) unsigned          DEFAULT NULL,
T225_Valor_Preco_Bruto                     decimal(10,2)             DEFAULT NULL,
T225_ST_ICMS_Interno_MVA                   decimal(14,2)             DEFAULT NULL,
T225_ICMS_Inter_Estadual_MVA               decimal(14,2)             DEFAULT NULL,
T225_Valor_Preco_Original_Bruto            decimal(14,5)             DEFAULT '0.00000',
T225_Valor_Desconto_Unitario_Bruto         decimal(14,5)             DEFAULT '0.00000',
T225_Percentual_Desconto_Bruto             decimal(14,5)             DEFAULT '0.00000',
T225_T206_Id                               int(10) unsigned          DEFAULT NULL,
T225_Valor_Comissao                        decimal(14,5)             DEFAULT '0.00000',
T225_Percentual_Comissao                   decimal(14,5)             DEFAULT '0.00000',
T225_D022_Id                               int(10) unsigned          DEFAULT NULL,
T225_T208_Id                               int(10) unsigned          DEFAULT NULL,
T225_Hora_Entrega                          time                      DEFAULT NULL,
T225_D009_Id_Loja                          int(10) unsigned          DEFAULT NULL,
T225_Cubagem_Unitaria                      decimal(10,6)             DEFAULT '0.000000',
T225_Total_Cubagem                         decimal(10,6)             DEFAULT '0.000000',
T225_Peso_Bruto                            decimal(10,3)             DEFAULT NULL,
T225_Peso_Bruto_Total                      decimal(10,3)             DEFAULT NULL,
T225_Valor_Trib_Transparencia              decimal(15,6)             DEFAULT '0.000000',
T225_Percentual_Trib_Transparencia         decimal(15,2)             DEFAULT '0.00',
T225_Flag_Pre_Cadastro                     varchar(1)                DEFAULT NULL,
T225_Flag_Modificar                        varchar(1)                DEFAULT NULL,
T225_D144_Id                               int(10) unsigned          DEFAULT NULL,
PRIMARY KEY (T225_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}


// Pagamento ordem de compra
$cClausula  = <<<EOT
create table if not exists T226 (
T226_Id                     int(11)        auto_increment,
T226_T224_Id                int(11)        DEFAULT '0',
T226_D027_Id                int(11)        DEFAULT '0',
T226_Numero_Parcela         int(3)         DEFAULT '0',
T226_Prazos_Dias            int(5)         DEFAULT '0',
T226_Data_Vencimento        date           DEFAULT NULL,
T226_Valor_Parcela          decimal(12,2)  DEFAULT '0.00',
T226_Numero_Cheque          varchar(50)    DEFAULT NULL,
T226_Banco_Cheque           varchar(50)    DEFAULT NULL,
T226_Agencia_Cheque         varchar(50)    DEFAULT NULL,
T226_Conta_Corrente_Cheque  varchar(50)    DEFAULT NULL,
T226_Nome_Cheque            varchar(50)    DEFAULT NULL,
T226_Data_Cheque            date           DEFAULT NULL,
T226_Cidade_Cheque          varchar(50)    DEFAULT NULL,
T226_Taxa_Juros             decimal(12,2)  DEFAULT NULL,
T226_Valor_Juros            decimal(12,2)  DEFAULT NULL,
PRIMARY KEY (T226_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

//Documentos ordem de compra
$cClausula  = <<<EOT
create table if not exists T227 (
T227_Id                int(10) unsigned  auto_increment,
T227_T224_Id           int(10) unsigned  DEFAULT NULL,
T227_C007_Id           int(10) unsigned  DEFAULT NULL,
T227_Nome_Arquivo      varchar(200)      DEFAULT NULL,
T227_Descricao         text              DEFAULT NULL,
T227_Data_Hora_Upload  datetime          DEFAULT NULL,
PRIMARY KEY (T227_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}


// Histórico cotação de compra
$cClausula  = <<<EOT
create table if not exists T228 (
T228_Id          int(10) unsigned auto_increment,
T228_T224_Id     int(10) unsigned DEFAULT NULL,
T228_C007_Id     int(10) unsigned DEFAULT NULL,
T228_Data        datetime         DEFAULT NULL,
T228_Descricao   text             DEFAULT NULL,
T228_Observacao  text             DEFAULT NULL,
PRIMARY KEY (T228_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}


// Trocas ordem de compra
$cClausula  = <<<EOT
create table if not exists T229 (
T229_Id                    int(10) unsigned auto_increment,
T229_T007_Id               int(10) unsigned DEFAULT NULL,
T229_C004_Id               int(10) unsigned DEFAULT NULL,
T229_D009_Id               int(10) unsigned DEFAULT NULL,
T229_Quantidade            decimal(12,5)    DEFAULT '0.00000',
T229_Valor_Preco_Unitario  decimal(20,5)    DEFAULT '0.00000',
T229_Flag_Documento        char(1)          DEFAULT '0',
T229_Numero_Nota_Fiscal    varchar(12)      DEFAULT NULL,
T229_Observacao            varchar(200)     DEFAULT NULL,
T229_T218_Id               int(10) unsigned DEFAULT NULL,
T229_T224_Id               int(10) unsigned DEFAULT NULL,
T229_C007_Id_Inspecao      int(10) unsigned DEFAULT NULL,
T229_Operacao              char(1)          DEFAULT NULL,
T229_Data_Inspecao         datetime         DEFAULT NULL,
T229_Flag_Dividir          char(1)          DEFAULT 'N',
T229_Flag_Comissao         char(1)          DEFAULT 'N',
T229_Quantidade_Separado   decimal(10,2)    DEFAULT NULL,
PRIMARY KEY (T229_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}


// Serviços ordem de compra
$cClausula  = <<<EOT
create table if not exists T230 (
T230_Id              int(11)           auto_increment,
T230_T224_Id         int(10) unsigned  DEFAULT NULL,
T230_T223_Id         int(11)           DEFAULT NULL,
T230_D110_Id         int(11)           DEFAULT NULL,
T230_D109_Id         int(10) unsigned  DEFAULT NULL,
T230_D112_Id         int(11)           DEFAULT NULL,
T230_Titulo          varchar(200)      DEFAULT NULL,
T230_Descricao       varchar(500)      DEFAULT NULL,
T230_Tempo_Estimado  time              DEFAULT NULL,
T230_Quantidade      int(11)           DEFAULT NULL,
T230_Valor_Unitario  decimal(14,2)     DEFAULT NULL,
T230_Valor_Total     decimal(14,2)     DEFAULT NULL,
T230_Hora_Inicio     datetime          DEFAULT NULL,
T230_Hora_Reinicio   datetime          DEFAULT NULL,
T230_Hora_Fim        datetime          DEFAULT NULL,
T230_Status          char(1)           DEFAULT NULL,
T230_Pausado         char(1)           DEFAULT NULL,
T230_Hora_Pausa      datetime          DEFAULT NULL,
T230_Prioridade      int(5) unsigned   DEFAULT '99999',
T230_D116_Id         int(10) unsigned  DEFAULT NULL,
T230_Flag_Cancelado  char(1)           DEFAULT 'N',
T230_D024_Id         int(10) unsigned  DEFAULT NULL,
T230_Flag_Novo       char(1)           DEFAULT 'S',
T230_Flag_Conferido  char(1)           DEFAULT 'N',
T230_T225_Id         int(10) unsigned  DEFAULT NULL,
T230_D109_Id_Conf    int(10) unsigned  DEFAULT NULL,
T230_C007_Id_Conf    int(10) unsigned  DEFAULT NULL,
T230_Observacao      varchar(500)      DEFAULT NULL,
T230_Flag_Dividir    char(1)           DEFAULT 'N',
T230_Flag_Comissao   char(1)           DEFAULT 'N',
T230_T212_Id         int(10)           DEFAULT NULL,
PRIMARY KEY (T230_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Entrega ordem de compra
$cClausula  = <<<EOT
create table if not exists T231 (
T231_Id              int(10) unsigned auto_increment,
T231_D116_Id         int(10) unsigned DEFAULT NULL,
T231_T224_Id         int(10) unsigned DEFAULT NULL,
T231_T230_Id         int(10) unsigned DEFAULT NULL,
T231_Data_combinada  date             DEFAULT NULL,
T231_Hora_combinada  time             DEFAULT NULL,
T231_Observacao      text             DEFAULT NULL,
PRIMARY KEY (T231_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Regras CFOP para compras

$cClausula  = <<<EOT
create table if not exists D145 (
D145_Id                                    int(10) unsigned auto_increment,
D145_D006_Id                               int(10) unsigned DEFAULT NULL,
D145_C004_Codigo_Regime_Tributario         int(1)           DEFAULT NULL,
D145_D024_Flag_Contribuinte                int(5)           DEFAULT NULL,
D145_Dentro_Fora_Estado                    char(1)          DEFAULT NULL,
D145_Estados_Especificos                   varchar(100)     DEFAULT NULL,
D145_UF_Tem_ST                             char(1)          DEFAULT NULL,
D145_NCM_Tem_ST                            char(1)          DEFAULT NULL,
D145_Pessoa_Fisica_Juridica                char(2)          DEFAULT NULL,
D145_Produzido_Adquirido                   char(1)          DEFAULT NULL,
D145_Flag_Inativar                         char(1)          DEFAULT 'N',
D145_Flag_Cabecalho_Item                   char(1)          DEFAULT 'I',
D145_D005_Id                               int(10)          DEFAULT '0',
D145_C004_Id                               int(10)          DEFAULT NULL,
D145_Empresas                              varchar(20)      DEFAULT NULL,
D145_Flag_Revenda_Consumidor               varchar(20)      DEFAULT NULL,
D145_Flag_UF_Convenio_NCM_ST               varchar(1),
D145_Origem_Mercadoria                     varchar(20)      DEFAULT NULL,
D145_Flag_Preferencial_Sobre_D024_D006_Id  varchar(1)       DEFAULT 'N',
D145_Peso_Minimo                           decimal(10,3)    DEFAULT '0.000',
D145_Peso_Maximo                           decimal(10,3)    DEFAULT '0.000',
D145_NCM                                   text             DEFAULT NULL,
D145_Empresas_Destinatario                 varchar(60)      DEFAULT NULL,
PRIMARY KEY (D145_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}


// Meses para compras futuras na Compras Estoque
$cClausula  = <<<EOT
create table if not exists T232 (
T232_Id                                    int(10) unsigned NOT NULL   auto_increment,
T232_C004_Id                               int(10) unsigned NOT NULL,
T232_D009_Id                               int(10) unsigned NOT NULL,
T232_MesAno                                DATE NOT NULL,
T232_Quantidade                            decimal(10,5)    DEFAULT '0.00000',
PRIMARY KEY (T232_Id),
UNIQUE KEY I_T232_MesAno (T232_MesAno)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// Fila de impressão de etiquetas
$cClausula  = <<<EOT
create table if not exists T233 (
T233_Id                                    int(10) unsigned NOT NULL   auto_increment,
T233_C007_Id                               int(10) unsigned NOT NULL,
T233_Nome                                  varchar(100),
T233_Codigo                                longblob,
PRIMARY KEY (T233_Id),
KEY I_T233_C007_Id (T233_C007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "6. $cClausula<hr>$cErro";
    mysql_query("ROLLBACK");
}

// criação da tabela T234 (Controle de chamados)
$cClausula  = <<<EOT
create table if not exists T234 (
    T234_Id int(10) unsigned auto_increment,
    T234_D024_Id int(10),
    T234_C007_Id int(10),
    T234_T235_Id int(10),
    T234_Data_Hora_Abertura DATETIME,
    T234_Data_Hora_Finalizado DATETIME,
    T234_Descricao VARCHAR(255),
    T234_Flag_Finalizado char(1) DEFAULT 'N',
    T234_Observacao_Finalizado varchar(255),
    T234_Solicitante varchar(100),
    PRIMARY KEY (T234_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T235 (Categoria de chamados)
$cClausula  = <<<EOT
create table if not exists T235 (
    T235_Id int(10) unsigned auto_increment,
    T235_Descricao VARCHAR(50),
    PRIMARY KEY (T235_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T236 (Controle de Implementações)
$cClausula  = <<<EOT
create table if not exists T236 (
    T236_Id int(10) unsigned auto_increment,
    T236_D024_Id int(10),
    T236_C007_Id_Inserido int(10),
    T236_C007_Id_Responsavel int(10),
    T236_Data_Inserido date,
    T236_Data_Finalizado date,
    T236_Descricao text,
    T236_Solicitante varchar(100),
    PRIMARY KEY (T236_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela D146 (Registro de Armazens)
$cClausula  = <<<EOT
create table if not exists D146 (
    D146_Id int(10) unsigned auto_increment,
    D146_Descricao varchar(100),
    PRIMARY KEY (D146_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T237 ('chat' entre usuários)
$cClausula  = <<<EOT
create table if not exists T237 (
    T237_Id int(10) unsigned auto_increment,
    T237_C007_Id_Origem int(10) unsigned DEFAULT NULL,
    T237_C007_Id_Destino int(10) unsigned DEFAULT NULL,
    T237_Mensagem varchar(500),
    T237_Flag_Lido char(1) DEFAULT '0' NOT NULL,
    T237_Data_Envio datetime,
    PRIMARY KEY (T237_Id),
    KEY I_T237_C007_Id_Origem (T237_C007_Id_Origem),
    KEY I_T237_C007_Id_Destino (T237_C007_Id_Destino),
    KEY I_T237_Flag_Lido (T237_Flag_Lido)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


// Cadastro de Impressoras
$cClausula  = <<<EOT
create table if not exists D147 (
    D147_Id int(10) unsigned auto_increment,
    D147_Nome varchar(100),
    PRIMARY KEY (D147_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


// Endereços de Entrega
$cClausula  = <<<EOT
create table if not exists D148 (
    D148_Id int(10) unsigned auto_increment,
    D148_D024_Id int(10),
    D148_D018_Id int(10),
    D148_D020_Id int(10),
    D148_D030_Id int(10),
    D148_Nome varchar(100),
    D148_Cep varchar(9),
    D148_Rua varchar(60),
    D148_Numero varchar(15),
    D148_Complemento varchar(60),
    D148_Bairro varchar(25),
    D148_DDD_Telefone int(2) unsigned,
    D148_Telefone varchar(20),
    D148_DDD_Fax int(2) unsigned,
    D148_Fax varchar(20),
    D148_Email varchar(50),
    D148_Flag_Tipo char(1) DEFAULT '',
    PRIMARY KEY (D148_Id),
    KEY I_D148_D024_Id (D148_D024_Id),
    KEY I_D148_D018_Id (D148_D018_Id),
    KEY I_D148_D020_Id (D148_D020_Id),
    KEY I_D148_D030_Id (D148_D030_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Separação por Locação
$cClausula  = <<<EOT
create table if not exists T238 (
    T238_Id int(10) unsigned auto_increment,
    T238_T005_Id int(10),
    T238_T006_Id int(10),
    T238_D004_Id int(10),
    T238_Quantidade_Separacao DECIMAL(15,5),
    PRIMARY KEY (T238_Id),
    KEY I_T238_T005_Id (T238_T005_Id),
    KEY I_T238_T006_Id (T238_T006_Id),
    KEY I_T238_D004_Id (T238_D004_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Preço Loja
$cClausula  = <<<EOT
create table if not exists T239 (
    T239_Id int(10) unsigned auto_increment,
    T239_D009_Id int(10),
    T239_C007_Id int(10),
    T239_Data_Hora datetime,
    T239_Preco_Loja_Antigo DECIMAL(15,2),
    T239_Preco_Loja_Novo DECIMAL(15,2),
    T239_Processo_Id CHAR(13),
    PRIMARY KEY (T239_Id),
    KEY I_T239_D009_Id (T239_D009_Id),
    KEY I_T239_C007_Id (T239_C007_Id),
    KEY I_T239_Processo_Id (T239_Processo_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Configurações Fiscais NCM por Empresa
$cClausula  = <<<EOT
create table if not exists D149 (
    D149_Id int(10) unsigned auto_increment,
    D149_D088_Id int(10),
    D149_D005_Id int(10),
    D149_C004_Id int(10),
    D149_Flag_Isento_IPI int(1) DEFAULT '2',
    D149_Percentual_Reducao_IPI int(10) unsigned,
    D149_Aliquota_IPI decimal(5,2) DEFAULT '0.00',
    D149_Flag_Tipo_IPI varchar(1),
    D149_Aliquota_PIS decimal(5,2) DEFAULT '0.00',
    D149_Aliquota_COFINS decimal(5,2) DEFAULT '0.00',
    D149_Aliquota_Imposto_Importacao decimal(5,2) DEFAULT '0.00',
    D149_Flag_ST varchar(1),
    D149_Flag_VA varchar(1),
    D149_Valor_VA decimal(10,2),
    D149_Percentual_Trib_Transparencia decimal(15,2) DEFAULT '0.00',
    D149_Imposto_Importacao decimal(5,2),
    D149_Importacao_IPI decimal(5,2),
    D149_Importacao_PIS decimal(5,2),
    D149_Importacao_COFINS decimal(5,2),
    D149_UF_ST text,
    D149_Regras_MVA text,
    D149_Regras_MVA_Compra text,
    D149_Aliquota_ICMS_Interno_Compra decimal(10,2) DEFAULT '0.00',
    PRIMARY KEY (D149_Id),
    KEY I_D149_D088_Id (D149_D088_Id),
    KEY I_D149_C004_Id (D149_C004_Id),
    KEY I_D149_D005_Id (D149_D005_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
create table if not exists T240 (
  T240_Id int(11) NOT NULL AUTO_INCREMENT,
  T240_D024_Id int(11) DEFAULT NULL,
  T240_C007_Id int(11) DEFAULT NULL,
  T240_Data date DEFAULT NULL,
  T240_Status varchar(1) DEFAULT 'A',
  T240_Ordem int(10) DEFAULT '0',
  PRIMARY KEY (T240_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


$cClausula = <<<EOT
CREATE TABLE if not exists D070 (
  D070_Id int(10) NOT NULL AUTO_INCREMENT,
  D070_Descricao varchar(50) DEFAULT NULL,
  D070_D024_Id int(10) NOT NULL,
  D070_Desconto decimal(10,2) DEFAULT NULL,
  D070_Observacao text,
  D070_Tipo_Fator varchar(1) NOT NULL,
  D070_Data date DEFAULT '0000-00-00',
  D070_Flag_Ativo varchar(1) DEFAULT 'S',
  PRIMARY KEY (D070_Id),
  KEY IDX_D070_Id (D070_Id),
  KEY IDX_D070_D024_Id (D070_D024_Id)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE if not exists D069 (
  D069_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D069_D059_Id int(11) unsigned DEFAULT NULL,
  D069_T008_Id int(10) unsigned DEFAULT NULL,
  D069_D069_Id int(10) unsigned DEFAULT NULL,
  D069_D005_Id int(10) unsigned DEFAULT NULL,
  D069_D037_Id int(10) unsigned DEFAULT NULL,
  D069_D006_Id int(10) unsigned DEFAULT NULL,
  D069_T219_Id int(10) unsigned DEFAULT NULL,
  D069_D024_Id int(10) unsigned DEFAULT NULL,
  D069_D070_Id int(10) unsigned DEFAULT NULL,
  D069_C007_Id_Separou int(11) unsigned DEFAULT NULL,
  D069_D009_Id int(10) unsigned DEFAULT NULL,
  D069_D004_Id int(10) unsigned DEFAULT NULL,
  D069_D040_Id int(10) unsigned DEFAULT NULL,
  D069_T014_Id int(10) unsigned DEFAULT NULL,
  D069_C004_Id int(10) unsigned DEFAULT '0',
  D069_Item int(4) DEFAULT '0',
  D069_Numero_Orcamento varchar(10) DEFAULT NULL,
  D069_Quantidade decimal(10,2) DEFAULT NULL,
  D069_Data_Entrega date DEFAULT NULL,
  D069_Valor_Custo_Unitario decimal(19,5) DEFAULT '0.00000',
  D069_Valor_Preco_Sem_Desconto_Unitario decimal(19,5) DEFAULT '0.00000',
  D069_Valor_Desconto_Unitario decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Despesas_Unitario decimal(19,5) DEFAULT '0.00000',
  D069_Valor_Custo_Final_Unitario decimal(19,5) DEFAULT '0.00000',
  D069_Valor_Frete_Unitario decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Despesas_Acessorias_Unitario decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Preco_Unitario decimal(19,5) DEFAULT '0.00000',
  D069_Descricao_Produto varchar(60) DEFAULT NULL,
  D069_Codigo_Substituto varchar(18) DEFAULT NULL,
  D069_Flag_Status varchar(1) DEFAULT NULL,
  D069_Numero_Pedido_Venda varchar(6) DEFAULT NULL,
  D069_Codigo_Fornecedor varchar(6) DEFAULT NULL,
  D069_Codigo_Local varchar(10) DEFAULT NULL,
  D069_Codigo_Produto varchar(30) DEFAULT NULL,
  D069_Valor_Total_Custo decimal(14,5) DEFAULT '0.00000',
  D069_Valor_Total_Preco_Sem_Desconto decimal(20,2) DEFAULT '0.00',
  D069_Valor_Total_Preco decimal(20,5) DEFAULT '0.00000',
  D069_Valor_Total_Desconto decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Total_Despesas decimal(20,5) DEFAULT '0.00000',
  D069_Valor_Total_Custo_Final decimal(10,2) DEFAULT '0.00',
  D069_Valor_Total_Frete decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Total_Despesas_Acessorias decimal(20,10) DEFAULT '0.0000000000',
  D069_IPV decimal(10,4) DEFAULT NULL,
  D069_Percentual_Reducao_ICMS decimal(5,2) DEFAULT '0.00',
  D069_Valor_Base_ICMS decimal(10,2) DEFAULT '0.00',
  D069_Modalidade_ICMS int(1) DEFAULT '3',
  D069_Aliquota_ICMS decimal(5,2) DEFAULT '0.00',
  D069_Valor_ICMS decimal(15,2) DEFAULT '0.00',
  D069_Aliquota_ICMS_Credito decimal(5,2) DEFAULT '0.00',
  D069_Valor_ICMS_Credito decimal(15,2) DEFAULT '0.00',
  D069_Percentual_Base_Operacao_Propria decimal(5,2) DEFAULT '0.00',
  D069_Valor_Agregado decimal(20,2) DEFAULT '0.00',
  D069_Valor_Base_ICMS_Substituicao decimal(20,2) DEFAULT '0.00',
  D069_Modalidade_ICMS_Substituicao int(1) DEFAULT '4',
  D069_Percentual_Reducao_ICMS_ST decimal(5,2) DEFAULT '0.00',
  D069_Aliquota_ICMS_ST decimal(5,2) DEFAULT '0.00',
  D069_Valor_ICMS_Substituicao decimal(20,2) DEFAULT '0.00',
  D069_Valor_ICMS_Substituicao_Retencao decimal(20,2) DEFAULT '0.00',
  D069_Valor_Base_ICMS_Substituicao_Retido decimal(15,2) DEFAULT '0.00',
  D069_Valor_ICMS_Substituicao_Retido decimal(15,2) DEFAULT '0.00',
  D069_Valor_Base_ICMS_Substituicao_Destino decimal(15,2) DEFAULT '0.00',
  D069_Valor_ICMS_Substituicao_Destino decimal(15,2) DEFAULT '0.00',
  D069_UF_ICMS_Substituicao_Devido char(2) DEFAULT NULL,
  D069_Percentual_Reducao_IPI decimal(5,2) DEFAULT '0.00',
  D069_Valor_Base_IPI decimal(10,2) DEFAULT '0.00',
  D069_Aliquota_IPI decimal(5,2) DEFAULT '0.00',
  D069_Valor_IPI decimal(10,2) DEFAULT '0.00',
  D069_Situacao_Tributaria_IPI int(2) unsigned zerofill DEFAULT NULL,
  D069_Marca varchar(20) DEFAULT NULL,
  D069_Peso_Unitario decimal(10,3) DEFAULT '0.000',
  D069_Peso_Total decimal(10,3) DEFAULT '0.000',
  D069_Ultimo_Preco varchar(50) DEFAULT NULL,
  D069_Ultimo_Preco_Confirmado varchar(50) DEFAULT NULL,
  D069_Flag_Excluir int(1) DEFAULT '2',
  D069_Flag_Preco_Especial varchar(1) DEFAULT NULL,
  D069_Classificacao_Fiscal varchar(12) DEFAULT NULL,
  D069_Flag_Comprar varchar(1) DEFAULT NULL,
  D069_D069_Id_Dividir int(10) DEFAULT NULL,
  D069_Quantidade_Dividir decimal(10,2) DEFAULT NULL,
  D069_Quantidade_Separacao decimal(10,2) DEFAULT NULL,
  D069_Quantidade_Etiqueta decimal(10,3) DEFAULT '0.000',
  D069_Quantidade_Itens_Etiqueta decimal(10,3) DEFAULT '0.000',
  D069_Quantidade_Dividido decimal(10,2) DEFAULT NULL,
  D069_Flag_ST varchar(1) DEFAULT 'N',
  D069_ICMS_Inter_Estadual decimal(5,2) DEFAULT '0.00',
  D069_ST_ICMS_Interno decimal(5,2) DEFAULT '0.00',
  D069_ST_VA decimal(10,2) DEFAULT '0.00',
  D069_Situacao_Tributaria_PIS varchar(2) DEFAULT '01',
  D069_Flag_Isento_PIS enum('S','N') DEFAULT 'N',
  D069_Valor_Base_PIS decimal(20,2) DEFAULT '0.00',
  D069_Aliquota_PIS decimal(5,2) DEFAULT '0.00',
  D069_Valor_PIS decimal(10,2) DEFAULT '0.00',
  D069_Situacao_Tributaria_COFINS varchar(2) DEFAULT '01',
  D069_Flag_Isento_COFINS enum('S','N') DEFAULT 'N',
  D069_Valor_Base_COFINS decimal(20,2) DEFAULT '0.00',
  D069_Aliquota_COFINS decimal(5,2) DEFAULT '0.00',
  D069_Valor_COFINS decimal(10,2) DEFAULT '0.00',
  D069_Flag_Situacao_Tributaria varchar(4) DEFAULT NULL,
  D069_T099_Id int(11) DEFAULT '0',
  D069_Quantidade_Comprar decimal(10,3) DEFAULT '0.000',
  D069_Reordenar int(1) DEFAULT '1',
  D069_Flag_Tipo_Produto varchar(2) DEFAULT NULL,
  D069_D049_Id int(11) unsigned DEFAULT NULL,
  D069_Quantidade_Estoque decimal(12,3) DEFAULT NULL,
  D069_Observacao_Divergencia varchar(100) DEFAULT NULL,
  D069_Estoque_BOX varchar(20) DEFAULT NULL,
  D069_Flag_Faturar varchar(1) DEFAULT 'N',
  D069_D009_Id_Comprar int(10) DEFAULT NULL,
  D069_Flag_Cotar varchar(1) DEFAULT 'N',
  D069_Quantidade_Estoque_Padrao decimal(12,3) DEFAULT NULL,
  D069_Flag_Divergencia varchar(1) DEFAULT 'N',
  D069_Flag_Dividir varchar(1) DEFAULT 'N',
  D069_Observacao_Compra varchar(100) DEFAULT NULL,
  D069_Data_Separacao datetime DEFAULT '0000-00-00 00:00:00',
  D069_Valor_Custo_Compra_Unitario decimal(10,5) DEFAULT NULL,
  D069_Valor_Custo_Tabela_Unitario decimal(10,5) DEFAULT NULL,
  D069_Numero_Pedido_Compra varchar(15) DEFAULT NULL,
  D069_Item_Pedido_Compra int(6) DEFAULT NULL,
  D069_D082_Id int(10) unsigned DEFAULT NULL,
  D069_Valor_Preco_Original decimal(15,5) DEFAULT '0.00000',
  D069_Percentual_Desconto decimal(14,2) DEFAULT NULL,
  D069_Observacao text,
  D069_Quantidade_Entregar_Temp int(4) DEFAULT '0',
  D069_Retirou_Qtd decimal(20,5) DEFAULT NULL,
  D069_Flag_Comissao char(1) DEFAULT 'N',
  D069_T075_Id int(10) unsigned DEFAULT NULL,
  D069_Valor_Total_Agregados decimal(10,2) DEFAULT '0.00',
  D069_Liberou_Alcada_Saldo_Futuro decimal(10,2) DEFAULT '0.00',
  D069_D009_Id_Kit int(10) unsigned DEFAULT NULL,
  D069_Valor_Preco_Bruto decimal(10,2) DEFAULT NULL,
  D069_ST_ICMS_Interno_MVA decimal(14,2) DEFAULT NULL,
  D069_ICMS_Inter_Estadual_MVA decimal(14,2) DEFAULT NULL,
  D069_Valor_Preco_Original_Bruto decimal(14,5) DEFAULT '0.00000',
  D069_Valor_Desconto_Unitario_Bruto decimal(14,5) DEFAULT '0.00000',
  D069_Percentual_Desconto_Bruto decimal(14,5) DEFAULT '0.00000',
  D069_T206_Id int(10) unsigned DEFAULT NULL,
  D069_Valor_Comissao decimal(14,5) DEFAULT '0.00000',
  D069_Percentual_Comissao decimal(14,5) DEFAULT '0.00000',
  D069_D022_Id int(10) unsigned DEFAULT NULL,
  D069_T208_Id int(10) unsigned DEFAULT NULL,
  D069_Hora_Entrega time DEFAULT NULL,
  D069_D009_Id_Loja int(10) unsigned DEFAULT NULL,
  D069_Cubagem_Unitaria decimal(10,6) DEFAULT '0.000000',
  D069_Total_Cubagem decimal(10,6) DEFAULT '0.000000',
  D069_Peso_Bruto decimal(10,3) DEFAULT NULL,
  D069_Peso_Bruto_Total decimal(10,3) DEFAULT NULL,
  D069_Valor_Trib_Transparencia decimal(15,6) DEFAULT '0.000000',
  D069_Percentual_Trib_Transparencia decimal(15,2) DEFAULT '0.00',
  D069_Flag_Pre_Cadastro varchar(1) DEFAULT NULL,
  D069_Flag_Modificar varchar(1) DEFAULT NULL,
  D069_D144_Id int(10) unsigned DEFAULT NULL,
  D069_T189_Id int(10) unsigned DEFAULT NULL,
  D069_Flag_Cancelada char(1) DEFAULT 'N',
  D069_Quantidade_Recebida decimal(10,3) DEFAULT NULL,
  D069_Valor_Frete decimal(15,5) DEFAULT '0.00000',
  D069_Valor_Financeiro decimal(15,5) DEFAULT '0.00000',
  D069_Percentual_Frete decimal(15,5) DEFAULT '0.00000',
  D069_Percentual_Financeiro decimal(15,5) DEFAULT '0.00000',
  D069_Valor_Preco_Venda decimal(15,5) DEFAULT '0.00000',
  D069_ST_VA_Ajustada decimal(12,4) DEFAULT '0.0000',
  D069_Valor_Total_Desconto_Recebida decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Total_Preco_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_Total_Preco_Sem_Desconto_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_Total_Frete_Recebida decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Total_Despesas_Acessorias_Recebida decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Despesas_Acessorias_Unitario_Recebida decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Frete_Unitario_Recebida decimal(20,10) DEFAULT '0.0000000000',
  D069_Valor_Total_Custo_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Peso_Total_Recebida decimal(10,3) DEFAULT '0.000',
  D069_Peso_Bruto_Total_Recebida decimal(10,3) DEFAULT NULL,
  D069_Valor_Total_Despesas_Recebida decimal(20,5) DEFAULT '0.00000',
  D069_Valor_Despesas_Unitario_Recebida decimal(19,5) DEFAULT '0.00000',
  D069_Valor_Base_IPI_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Valor_IPI_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Valor_Total_Custo_Final_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Valor_Custo_Final_Unitario_Recebida decimal(19,5) DEFAULT '0.00000',
  D069_Valor_Base_ICMS_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Valor_ICMS_Recebida decimal(15,2) DEFAULT '0.00',
  D069_Valor_Base_ICMS_Substituicao_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_ICMS_Substituicao_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_ICMS_Substituicao_Retencao_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_Base_PIS_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_PIS_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Valor_Base_COFINS_Recebida decimal(20,2) DEFAULT '0.00',
  D069_Valor_COFINS_Recebida decimal(10,2) DEFAULT '0.00',
  D069_Valor_ICMS_Credito_Recebida decimal(15,2) DEFAULT '0.00',
  D069_Total_Cubagem_Recebida decimal(10,6) DEFAULT '0.000000',
  D069_Valor_Trib_Transparencia_Recebida decimal(15,6) DEFAULT '0.000000',
  D069_Flag_OS_Importada varchar(1) DEFAULT 'N',
  D069_Observacao_Entrega varchar(30) DEFAULT '',
  D069_Flag_OC_Importada varchar(1) DEFAULT 'N',
  D069_Valor_Custo_Unitario_Recebida decimal(19,5) DEFAULT '0.00000',
  D069_Flag_Autorizado varchar(1) DEFAULT 'N',
  D069_Flag_Origem_Produto varchar(1) DEFAULT '',
  D069_Valor_Preco_Venda_Negociado decimal(14,6) DEFAULT NULL,
  D069_Valor_ICM decimal(10,2) DEFAULT NULL,
  D069_Percentual_Reducao_ICM decimal(10,2) DEFAULT NULL,
  D069_Valor_ST decimal(10,2) DEFAULT NULL,
  D069_ALiquota_ICM_Inter_Estadual decimal(10,2) DEFAULT NULL,
  D069_Aliquota_ICM_Interno decimal(10,2) DEFAULT NULL,
  D069_Valor_Base_ICM decimal(10,2) DEFAULT NULL,
  D069_Valor_Base_ST decimal(10,2) DEFAULT NULL,
  D069_Valor_Outros decimal(10,5) DEFAULT '0.00000',
  D069_Percentual_Outros decimal(10,5) DEFAULT '0.00000',
  D069_MVA_Ajustada decimal(10,5) DEFAULT '0.00000',
  D069_Flag_Tabela_Principal varchar(1) DEFAULT 'N',
  D069_Prazo_Entrega varchar(100) DEFAULT NULL,
  D069_MVA decimal(5,2) DEFAULT NULL,
  D069_Flag_Nacional_Importado enum('N','I') DEFAULT NULL,
  D069_Codigo_Barras varchar(30) DEFAULT '',
  D069_NCM varchar(10) DEFAULT '',
  D069_Quantidade_Embalagem decimal(10,5) DEFAULT '0.00000',
  D069_Unidade varchar(15) DEFAULT NULL,
  D069_Percentual_ST decimal(10,4) DEFAULT '0.0000',
  PRIMARY KEY (D069_Id),
  KEY IDX_D069_D070_Id (D069_D070_Id),
  KEY IDX_D069_C004_Id (D069_C004_Id),
  KEY IDX_D069_D024_Id (D069_D024_Id),
  KEY IDX_D069_D037_Id (D069_D037_Id),
  KEY IDX_D069_D009_Id (D069_D009_Id),
  KEY IDX_D069_D005_Id (D069_D005_Id),
  KEY IDX_D069_D006_Id (D069_D006_Id),
  KEY IDX_D069_T219_Id (D069_T219_Id),
  KEY IDX_D069_Flag_Cancelada (D069_Flag_Cancelada),
  KEY D069_Codigo_Produto (D069_Codigo_Produto),
  KEY D069_Descricao_Produto (D069_Descricao_Produto),
  KEY D069_NCM (D069_NCM)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE if not exists `d075` (
  `D075_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D075_D079_Id` int(10) NOT NULL,
  `D075_D078_Id` int(10) NOT NULL,
  `D075_D001_Id` int(10) unsigned NOT NULL,
  `D075_Lead_Time` decimal(5,2) DEFAULT '0.00',
  `D075_Descricao` text,
  `D075_Ordem` int(2) DEFAULT '0',
  `D075_Numero_Funcionarios` int(10) DEFAULT NULL,
  PRIMARY KEY (`D075_Id`),
  KEY `D075_FKIndex1` (`D075_D001_Id`),
  KEY `D075_FKIndex2` (`D075_D078_Id`),
  KEY `D075_FKIndex3` (`D075_D079_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE if not exists `d077` (
  `D077_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D077_D078_Id` int(10) NOT NULL,
  `D077_Nome` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`D077_Id`),
  KEY `D077_FKIndex1` (`D077_D078_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE if not exists `d078` (
  `D078_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D078_Nome` varchar(30) DEFAULT NULL,
  `D078_Custo_Minuto` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`D078_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE if not exists `d079` (
  `D079_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D079_Nome` varchar(30) DEFAULT NULL,
  `D079_Custo_Minuto` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`D079_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Configurações Globais C031
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS `C031` (
  `C031_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C031_C004_Id` int(10) unsigned NOT NULL,
  `C031_Campo` varchar(100) NOT NULL,
  `C031_Valor_Padrao` longblob NOT NULL,
  `C031_Valor` longblob DEFAULT NULL,
  `C031_Observacao` longblob,
  PRIMARY KEY (`C031_Id`)
) ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Cadastro de Fornecedores por produto
$cClausula = <<<EOT
create table if not exists T241 (
  T241_Id int(11) NOT NULL AUTO_INCREMENT,
  T241_D024_Id int(11) DEFAULT NULL,
  T241_D001_Id int(11) DEFAULT NULL,
  T241_Ultima_Compra date DEFAULT '0000-00-00',
  T241_Flag_Principal varchar(1) DEFAULT 'N',
  PRIMARY KEY (T241_Id),
  UNIQUE KEY I_D024_D001_Id (T241_D024_Id, T241_D001_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Documentos relacionados a contas a receber
$cClausula = <<<EOT
create table if not exists T242 (
  T242_Id int(11) NOT NULL AUTO_INCREMENT,
  T242_T002_Id int(11) DEFAULT NULL,
  T242_Nome_Arquivo varchar(255) DEFAULT '',
  T242_Data_Hora_Upload datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (T242_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Documentos relacionados a contas a pagar
$cClausula = <<<EOT
create table if not exists T243 (
  T243_Id int(11) NOT NULL AUTO_INCREMENT,
  T243_T015_Id int(11) DEFAULT NULL,
  T243_Nome_Arquivo varchar(255) DEFAULT '',
  T243_Data_Hora_Upload datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (T243_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Layouts de Exportação
$cClausula = <<<EOT
create table if not exists D150 (
  D150_Id int(11) NOT NULL AUTO_INCREMENT,
  D150_Nome varchar(100),
  D150_Descricao text,
  D150_Link_Documentacao varchar(200),
  D150_Nome_Arquivo text,
  PRIMARY KEY (D150_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Layouts de Exportação (sub-layout)
$cClausula = <<<EOT
create table if not exists D151 (
  D151_Id int(11) NOT NULL AUTO_INCREMENT,
  D151_D150_Id int(11) unsigned DEFAULT NULL,
  D151_Nome varchar(100),
  D151_Descricao text,
  D151_Comentario text,
  D151_Select text,
  D151_Ordem int,
  PRIMARY KEY (D151_Id),
  KEY IDX_D151_D150_Id (D151_D150_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Layouts de Exportação (itens)
$cClausula = <<<EOT
create table if not exists D152 (
  D152_Id int(11) NOT NULL AUTO_INCREMENT,
  D152_D151_Id int(11) unsigned DEFAULT NULL,
  D152_D153_Id int(11) unsigned DEFAULT NULL,
  D152_D153_2_Id int(11) unsigned DEFAULT NULL,
  D152_Campo text,
  D152_Formato char(1),
  D152_Tamanho int,
  D152_Decimal int,
  D152_Posicao_Inicio int,
  D152_Posicao_Fim int,
  D152_Descricao text,
  D152_Exemplo text,
  D152_Comentario text,
  D152_Campo_Select text,
  D152_Valor_Fixo text,
  D152_Anotacoes text,
  PRIMARY KEY (D152_Id),
  KEY IDX_D152_D151_Id (D152_D151_Id),
  KEY IDX_D152_D153_Id (D152_D153_Id),
  KEY IDX_D152_D153_2_Id (D152_D153_2_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Layouts de Exportação (callbacks)
$cClausula = <<<EOT
create table if not exists D153 (
  D153_Id int(11) NOT NULL AUTO_INCREMENT,
  D153_Nome varchar(100),
  D153_Callback text,
  D153_Tipo text,
  PRIMARY KEY (D153_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Sub-grupos de plano de contas
$cClausula = <<<EOT
create table if not exists D154 (
  D154_Id int(11) NOT NULL AUTO_INCREMENT,
  D154_Descricao varchar(100),
  PRIMARY KEY (D154_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Relacionamentos entre titulos a receber reparcelados
$cClausula = <<<EOT
create table if not exists T244 (
  T244_Id int(11) NOT NULL AUTO_INCREMENT,
  T244_T002_Id int(11) unsigned DEFAULT NULL,
  T244_T166_Id int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (T244_Id),
  KEY IDX_T244_T002_Id (T244_T002_Id),
  KEY IDX_T244_T166_Id (T244_T166_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T162 (auditoria - logs)
$cClausula  = <<<EOT
CREATE TABLE if not exists T245 (
  T245_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T245_C007_Id int(11) DEFAULT NULL,
  T245_C004_Id int(11) DEFAULT NULL,
  T245_DataHora datetime DEFAULT NULL,
  T245_Registro_Id int(11) DEFAULT NULL,
  T245_Tabela varchar(100) DEFAULT NULL,
  T245_Referencia varchar(200) DEFAULT NULL,
  T245_Descricao varchar(200) DEFAULT NULL,
  T245_Log text DEFAULT NULL,
  T245_Flag_Erro char(1) DEFAULT 'N',
  T245_Hash varchar(100) DEFAULT NULL,
  PRIMARY KEY (T245_Id),
  KEY IDX_T245_C007_Id (T245_C007_Id),
  KEY IDX_T245_C004_Id (T245_C004_Id),
  KEY IDX_T245_Referencia (T245_Referencia)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela INT001 (logs de integrações)
$cClausula = <<<SQL
CREATE TABLE if not exists INT001 (
  INT001_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT001_C004_Id int(10) unsigned NOT NULL,
  INT001_Data date NOT NULL,
  INT001_Hora time NOT NULL,
  INT001_Integracao varchar(30) NOT NULL,
  INT001_Operacao varchar(50) NOT NULL,
  INT001_Status char(1) NOT NULL,
  INT001_Etapa varchar(50) NOT NULL,
  INT001_Id_Externo varchar(100) DEFAULT NULL,
  INT001_Tabela_Principal varchar(20) DEFAULT NULL,
  INT001_Id_Principal int(10) unsigned DEFAULT NULL,
  INT001_Mensagem varchar(255) NOT NULL,
  INT001_Log mediumtext DEFAULT NULL,
  PRIMARY KEY (INT001_Id),
  KEY IDX_INT001_C004_Id (INT001_C004_Id),
  KEY IDX_INT001_Integracao (INT001_Integracao),
  KEY IDX_INT001_Status (INT001_Status),
  KEY IDX_INT001_Data (INT001_Data),
  KEY IDX_INT001_Id_Externo (INT001_Id_Externo),
  KEY IDX_INT001_Tabela_Principal_Id_Principal (INT001_Tabela_Principal, INT001_Id_Principal)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
SQL;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

// último resultado da consulta de produtos ISET, separado do vínculo INT003
$cClausula = <<<EOT
CREATE TABLE if not exists INT004 (
  INT004_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT004_C004_Id int(10) unsigned NOT NULL,
  INT004_D009_Id int(10) unsigned NOT NULL,
  INT004_D001_Id int(10) unsigned NOT NULL,
  INT004_D049_Id int(10) unsigned NOT NULL,
  INT004_Id_Produto_ISET varchar(100) DEFAULT NULL,
  INT004_SKU varchar(100) DEFAULT NULL,
  INT004_Status_Http smallint(5) unsigned NOT NULL DEFAULT 0,
  INT004_Encontrado char(1) NOT NULL DEFAULT 'E',
  INT004_Consultado_Em datetime NOT NULL,
  INT004_Resposta_Json mediumtext DEFAULT NULL,
  INT004_Valores_Normalizados_Json text DEFAULT NULL,
  INT004_Mensagem_Erro varchar(1000) DEFAULT NULL,
  INT004_Ultimo_Sucesso_Em datetime DEFAULT NULL,
  PRIMARY KEY (INT004_Id),
  UNIQUE KEY UQ_INT004_Empresa_D009 (INT004_C004_Id, INT004_D009_Id),
  KEY IDX_INT004_Empresa_Consultado (INT004_C004_Id, INT004_Consultado_Em),
  KEY IDX_INT004_Empresa_ISET (INT004_C004_Id, INT004_Id_Produto_ISET)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

// pendências consolidadas para sincronização automática de produtos ISET.
// A chave única reúne alterações sucessivas do mesmo produto antes do worker externo.
$cClausula = <<<SQL
CREATE TABLE if not exists INT006 (
  INT006_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT006_C004_Id int(10) unsigned NOT NULL,
  INT006_D009_Id int(10) unsigned NOT NULL,
  INT006_Status char(1) NOT NULL DEFAULT 'P',
  INT006_Flag_Processando char(1) NOT NULL DEFAULT 'N',
  INT006_Token_Processamento varchar(64) DEFAULT NULL,
  INT006_Data_Hora_Pendente datetime NOT NULL,
  INT006_Data_Hora_Processando datetime DEFAULT NULL,
  INT006_Data_Hora_Processado datetime DEFAULT NULL,
  INT006_Mensagem_Erro varchar(1000) DEFAULT NULL,
  INT006_Versao bigint unsigned NOT NULL DEFAULT 1,
  INT006_Tentativas int unsigned NOT NULL DEFAULT 0,
  INT006_Proxima_Tentativa datetime DEFAULT NULL,
  INT006_Motivo varchar(40) NOT NULL DEFAULT 'AGUARDANDO',
  PRIMARY KEY (INT006_Id),
  UNIQUE KEY UQ_INT006_Empresa_D009 (INT006_C004_Id, INT006_D009_Id),
  KEY IDX_INT006_Processamento (INT006_C004_Id, INT006_Status, INT006_Flag_Processando, INT006_Data_Hora_Pendente)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
SQL;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

Criar_Campo("INT006_Token_Processamento", "varchar(64) DEFAULT NULL");

// pendências consolidadas para atualização assíncrona dos status de pedidos ISET.
// A chave única preserva somente a versão mais recente do status de cada pedido.
$cClausula = <<<SQL
CREATE TABLE if not exists INT008 (
  INT008_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT008_C004_Id int(10) unsigned NOT NULL,
  INT008_T005_Id int(10) unsigned NOT NULL,
  INT008_Status char(1) NOT NULL DEFAULT 'P',
  INT008_Flag_Processando char(1) NOT NULL DEFAULT 'N',
  INT008_Token_Processamento varchar(64) DEFAULT NULL,
  INT008_Data_Hora_Pendente datetime NOT NULL,
  INT008_Data_Hora_Processando datetime DEFAULT NULL,
  INT008_Data_Hora_Processado datetime DEFAULT NULL,
  INT008_Mensagem_Erro varchar(1000) DEFAULT NULL,
  INT008_Versao bigint unsigned NOT NULL DEFAULT 1,
  INT008_Tentativas int unsigned NOT NULL DEFAULT 0,
  INT008_Proxima_Tentativa datetime DEFAULT NULL,
  INT008_Motivo varchar(40) NOT NULL DEFAULT 'AGUARDANDO',
  PRIMARY KEY (INT008_Id),
  UNIQUE KEY UQ_INT008_Empresa_T005 (INT008_C004_Id, INT008_T005_Id),
  KEY IDX_INT008_Processamento (INT008_C004_Id, INT008_Status, INT008_Flag_Processando, INT008_Data_Hora_Pendente)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
SQL;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

Criar_Campo("INT008_Token_Processamento", "varchar(64) DEFAULT NULL");
Criar_Campo("INT008_Versao", "bigint unsigned NOT NULL DEFAULT 1");
Criar_Campo("INT008_Tentativas", "int unsigned NOT NULL DEFAULT 0");
Criar_Campo("INT008_Proxima_Tentativa", "datetime DEFAULT NULL");
Criar_Campo("INT008_Motivo", "varchar(40) NOT NULL DEFAULT 'AGUARDANDO'");
Criar_Indice("INT008_C004_Id,,INT008_Proxima_Tentativa,,INT008_Id");

// histórico imutável das sincronizações de produtos ISET
$cClausula = <<<EOT
CREATE TABLE if not exists INT005 (
  INT005_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT005_C004_Id int(10) unsigned NOT NULL,
  INT005_D001_Id int(10) unsigned NOT NULL,
  INT005_D049_Id int(10) unsigned NOT NULL,
  INT005_D009_Id int(10) unsigned NOT NULL,
  INT005_Codigo_Produto varchar(100) NOT NULL,
  INT005_Descricao_Produto varchar(255) NOT NULL,
  INT005_Id_Produto_ISET int(10) unsigned NOT NULL,
  INT005_SKU varchar(100) NOT NULL,
  INT005_Operacao varchar(20) NOT NULL,
  INT005_Data_Hora_Sincronizacao datetime NOT NULL,
  INT005_Cadastro_Anterior_Json mediumtext DEFAULT NULL,
  INT005_Cadastro_Posterior_Json mediumtext NOT NULL,
  PRIMARY KEY (INT005_Id),
  KEY IDX_INT005_Empresa_Produto_Id (INT005_C004_Id, INT005_D009_Id, INT005_Id),
  KEY IDX_INT005_Empresa_Data (INT005_C004_Id, INT005_Data_Hora_Sincronizacao)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

// vínculo persistente entre produtos do Hardness e produtos ISET
$cClausula = <<<EOT
CREATE TABLE if not exists INT003 (
  INT003_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT003_C004_Id int(10) unsigned NOT NULL,
  INT003_D001_Id int(10) unsigned NOT NULL,
  INT003_D049_Id int(10) unsigned NOT NULL,
  INT003_D009_Id int(10) unsigned NOT NULL,
  INT003_Id_Produto_ISET int(10) unsigned NOT NULL,
  INT003_SKU varchar(100) NOT NULL,
  INT003_Data_Hora_Ultima_Sincronizacao datetime NOT NULL,
  INT003_Status_Ultima_Sincronizacao char(1) NOT NULL,
  PRIMARY KEY (INT003_Id),
  UNIQUE KEY UQ_INT003_Empresa_D009 (INT003_C004_Id, INT003_D009_Id),
  UNIQUE KEY UQ_INT003_Empresa_ISET (INT003_C004_Id, INT003_Id_Produto_ISET),
  KEY IDX_INT003_Empresa_D001 (INT003_C004_Id, INT003_D001_Id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

// controle persistente de atualização de alíquotas de pedidos ISET
$cClausula = <<<EOT
CREATE TABLE if not exists INT002 (
  INT002_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  INT002_C004_Id int(10) unsigned NOT NULL,
  INT002_T005_Id int(10) unsigned NOT NULL,
  INT002_Id_Externo varchar(100) DEFAULT NULL,
  INT002_Operacao varchar(50) NOT NULL,
  INT002_Token char(64) NOT NULL,
  INT002_Total_Itens int(10) unsigned NOT NULL DEFAULT 0,
  INT002_Ultimo_T006_Id int(10) unsigned NOT NULL DEFAULT 0,
  INT002_Itens_Processados int(10) unsigned NOT NULL DEFAULT 0,
  INT002_Itens_Sucesso int(10) unsigned NOT NULL DEFAULT 0,
  INT002_Itens_Erro int(10) unsigned NOT NULL DEFAULT 0,
  INT002_Status char(1) NOT NULL DEFAULT 'P',
  INT002_Flag_Processando char(1) NOT NULL DEFAULT 'N',
  INT002_Flag_Log_Final char(1) NOT NULL DEFAULT 'N',
  INT002_Data_Hora_Inicio datetime NOT NULL,
  INT002_Data_Hora_Processando datetime DEFAULT NULL,
  INT002_Data_Hora_Fim datetime DEFAULT NULL,
  INT002_Erros text DEFAULT NULL,
  INT002_Log_Base text DEFAULT NULL,
  PRIMARY KEY (INT002_Id),
  UNIQUE KEY UQ_INT002_T005_Id (INT002_T005_Id),
  UNIQUE KEY UQ_INT002_Token (INT002_Token),
  KEY IDX_INT002_C004_Status (INT002_C004_Id, INT002_Status)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

// controle transacional de idempotência de pedidos da integração ISET
$cClausula = <<<EOT
CREATE TABLE if not exists IST001 (
  IST001_Id bigint unsigned NOT NULL AUTO_INCREMENT,
  IST001_C004_Id int unsigned NOT NULL,
  IST001_Id_Pedido_Externo varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  IST001_Status char(1) NOT NULL DEFAULT 'P',
  IST001_T005_Id int unsigned DEFAULT NULL,
  IST001_Data_Hora_Criacao datetime NOT NULL,
  IST001_Data_Hora_Conclusao datetime DEFAULT NULL,
  PRIMARY KEY (IST001_Id),
  UNIQUE KEY UQ_IST001_Empresa_Pedido (IST001_C004_Id, IST001_Id_Pedido_Externo),
  KEY IDX_IST001_Empresa_Status (IST001_C004_Id, IST001_Status)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

// Início da estrutura de proteção de produtos ISET. A carga histórica é executada pela IDE.
$cClausula = <<<SQL
CREATE TABLE if not exists INT007 (
  INT007_Id bigint unsigned NOT NULL AUTO_INCREMENT,
  INT007_C004_Id int unsigned NOT NULL,
  INT007_D009_Id int unsigned NOT NULL,
  INT007_SKU varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  INT007_Estado varchar(30) NOT NULL,
  INT007_Tentativa varchar(64) DEFAULT NULL,
  INT007_Data_Hora_Tentativa datetime DEFAULT NULL,
  INT007_Data_Hora_Confirmacao datetime DEFAULT NULL,
  INT007_Id_Produto_ISET int unsigned DEFAULT NULL,
  PRIMARY KEY (INT007_Id),
  UNIQUE KEY UQ_INT007_Empresa_SKU (INT007_C004_Id, INT007_SKU),
  KEY IDX_INT007_Empresa_D009 (INT007_C004_Id, INT007_D009_Id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
SQL;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
  echo "$cClausula<hr>$cErro";
}

Criar_Campo("INT006_Versao", "bigint unsigned NOT NULL DEFAULT 1");
Criar_Campo("INT006_Tentativas", "int unsigned NOT NULL DEFAULT 0");
Criar_Campo("INT006_Proxima_Tentativa", "datetime DEFAULT NULL");
Criar_Campo("INT006_Motivo", "varchar(40) NOT NULL DEFAULT 'AGUARDANDO'");
Criar_Indice("INT006_C004_Id,,INT006_Proxima_Tentativa,,INT006_Id");

// O corpo sanitizado pode ocupar 64 KiB, além do envelope JSON e dos demais campos.
Alterar_Tipo_Campo("INT001_Log", "mediumtext DEFAULT NULL");
// Fim da estrutura de proteção de produtos ISET.

// criação da tabela T162 (auditoria - logs)
$cClausula  = <<<EOT
CREATE TABLE if not exists T246 (
  T246_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T246_T002_Id int(11) DEFAULT NULL,
  T246_Numero_Cheque VARCHAR(50) DEFAULT NULL,
  T246_Data_Cheque date DEFAULT '0000-00-00',
  T246_Agencia_Cheque varchar(50) DEFAULT NULL,
  T246_Conta_Corrente_Cheque varchar(50) DEFAULT NULL,
  T246_Banco_Cheque varchar(50) DEFAULT NULL,
  T246_Nome_Cheque varchar(50) DEFAULT NULL,
  T246_Cidade_Cheque varchar(50) DEFAULT 'N',
  T246_Cheque_Repassado varchar(60) DEFAULT NULL,
  T246_Observacao_Cheque text DEFAULT NULL,
  PRIMARY KEY (T246_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T247 (Divisao de contas a pagar - tabela temporaria)
$cClausula  = <<<EOT
CREATE TABLE if not exists T247 (
  T247_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T247_C004_Id int(11) DEFAULT NULL,
  T247_C007_Id int(11) DEFAULT NULL,
  T247_D073_Id int(11) DEFAULT NULL,
  T247_D031_Id int(11) DEFAULT NULL,
  T247_Numero_Parcela int(3),
  T247_Numero_Documento VARCHAR(50) DEFAULT NULL,
  T247_Prazos_Dias int(5),
  T247_Data_Vencimento date DEFAULT '0000-00-00',
  T247_Valor_Parcela decimal(12,2) DEFAULT '0.00',
  T247_Data_Emissao date DEFAULT '0000-00-00',
  PRIMARY KEY (T247_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T317 (Divisao de contas a receber - tabela temporaria)
$cClausula  = <<<EOT
CREATE TABLE if not exists T317 (
  T317_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  T317_C004_Id int(11) DEFAULT NULL,
  T317_C007_Id int(11) DEFAULT NULL,
  T317_D073_Id int(11) DEFAULT NULL,
  T317_D027_Id int(11) DEFAULT NULL,
  T317_Numero_Parcela int(3),
  T317_Numero_Duplicata VARCHAR(50) DEFAULT NULL,
  T317_Numero_Documento VARCHAR(50) DEFAULT NULL,
  T317_Prazos_Dias int(5),
  T317_Data_Vencimento date DEFAULT '0000-00-00',
  T317_Valor_Parcela decimal(12,2) DEFAULT '0.00',
  T317_Data_Emissao date DEFAULT '0000-00-00',
  PRIMARY KEY (T317_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Relacionamentos entre titulos a pagar reparcelados
$cClausula = <<<EOT
create table if not exists T248 (
  T248_Id int(11) NOT NULL AUTO_INCREMENT,
  T248_T015_Id int(11) unsigned DEFAULT NULL,
  T248_T196_Id int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (T248_Id),
  KEY IDX_T248_T015_Id (T248_T015_Id),
  KEY IDX_T248_T196_Id (T248_T196_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela da composição do IPV
$cClausula = <<<EOT
create table if not exists T249 (
  T249_Id int(11) NOT NULL AUTO_INCREMENT,
  T249_C004_Id int(11) unsigned DEFAULT NULL,
  T249_Descricao VARCHAR(50) DEFAULT NULL,
  T249_Aliquota decimal(5,2) DEFAULT '0.00',
  T249_Tipo int(10) DEFAULT NULL,
  PRIMARY KEY (T249_Id),
  KEY IDX_T249_C004_Id (T249_C004_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela da composição do IPV no orçamento
$cClausula = <<<EOT
create table if not exists T250 (
  T250_Id int(11) NOT NULL AUTO_INCREMENT,
  T250_T003_Id int(11) unsigned DEFAULT NULL,
  T250_Descricao VARCHAR(50) DEFAULT NULL,
  T250_Aliquota decimal(5,2) DEFAULT '0.00',
  T250_Aliquota_Original decimal(5,2) DEFAULT '0.00',
  T250_Tipo int(10) DEFAULT NULL,
  PRIMARY KEY (T250_Id),
  KEY IDX_T250_T003_Id (T250_T003_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela da composição do IPV no pedido
$cClausula = <<<EOT
create table if not exists T251 (
  T251_Id int(11) NOT NULL AUTO_INCREMENT,
  T251_T005_Id int(11) unsigned DEFAULT NULL,
  T251_Descricao VARCHAR(50) DEFAULT NULL,
  T251_Aliquota decimal(5,2) DEFAULT '0.00',
  T251_Aliquota_Original decimal(5,2) DEFAULT '0.00',
  T251_Tipo int(10) DEFAULT NULL,
  PRIMARY KEY (T251_Id),
  KEY IDX_T251_T005_Id (T251_T005_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela da composição do IPV no pedido
$cClausula = <<<EOT
create table if not exists T251 (
  T251_Id int(11) NOT NULL AUTO_INCREMENT,
  T251_T005_Id int(11) unsigned DEFAULT NULL,
  T251_Descricao VARCHAR(50) DEFAULT NULL,
  T251_Aliquota decimal(5,2) DEFAULT '0.00',
  T251_Aliquota_Original decimal(5,2) DEFAULT '0.00',
  T251_Tipo int(10) DEFAULT NULL,
  PRIMARY KEY (T251_Id),
  KEY IDX_T251_T005_Id (T251_T005_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela de interações do suporte
$cClausula = <<<EOT
create table if not exists T252 (
  T252_Id int(11) NOT NULL AUTO_INCREMENT,
  T252_C007_Id int(11) unsigned DEFAULT NULL,
  T252_Usuario varchar(50) DEFAULT NULL,
  T252_T234_Id int(11) unsigned DEFAULT NULL,
  T252_Descricao text DEFAULT NULL,
  T252_Data_Hora datetime DEFAULT NULL,
  T252_C007_Id_Cliente int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (T252_Id),
  KEY IDX_T252_T234_Id (T252_T234_Id),
  KEY IDX_T252_C007_Id (T252_C007_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela de anexos do suporte
$cClausula = <<<EOT
create table if not exists T253 (
  T253_Id int(11) NOT NULL AUTO_INCREMENT,
  T253_T234_Id int(11) unsigned DEFAULT NULL,
  T253_Caminho_Arquivo varchar(255) DEFAULT NULL,
  T253_Nome_Arquivo varchar(50) DEFAULT NULL,
  T253_Link_Arquivo text DEFAULT NULL,
  PRIMARY KEY (T253_Id),
  KEY IDX_T253_T234_Id (T253_T234_Id)
) 
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T207
$cClausula = <<<EOT
CREATE TABLE if not exists `t207` (
  `T207_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T207_D009_Id` int(10) unsigned DEFAULT NULL,
  `T207_T006_Id` int(10) unsigned DEFAULT NULL,
  `T207_C007_Id` int(10) unsigned DEFAULT NULL,
  `T207_Data` date DEFAULT NULL,
  `T207_Flag_Status` char(1) DEFAULT '1',
  `T207_T005_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T207_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T254 - Histórico desenvolvimento
$cClausula = <<<EOT
CREATE TABLE if not exists `T254` (
  `T254_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T254_T234_Id` int(10) unsigned DEFAULT NULL,
  `T254_C007_Id` int(10) unsigned DEFAULT NULL,
  `T254_Data_Hora` datetime DEFAULT '0000-00-00 00:00:00',
  `T254_Status` varchar(100) DEFAULT '',
  PRIMARY KEY (`T254_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T255 - Cabeçalho Ordem de produção
$cClausula = <<<EOT
CREATE TABLE if not exists `T255` (
  `T255_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T255_Data_Emissao` date DEFAULT '0000-00-00',
  `T255_C004_Id` int(10) unsigned DEFAULT NULL,
  `T255_C007_Id` int(10) unsigned DEFAULT NULL,
  `T255_Flag_Status` varchar(2) DEFAULT '0',
  PRIMARY KEY (`T255_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T256 - Itens Ordem de produção
$cClausula = <<<EOT
CREATE TABLE if not exists `T256` (
  `T256_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T256_T255_Id` int(10) unsigned DEFAULT NULL,
  `T256_D009_Id` int(10) unsigned DEFAULT NULL,
  `T256_Quantidade` float(10,3) DEFAULT '0.00000',
  `T256_Quantidade_Produzida` float(10,3) DEFAULT '0.00000',
  `T256_Quantidade_Saldo` float(10,3) DEFAULT '0.00000',
  PRIMARY KEY (`T256_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T257 - Entrada de produção
$cClausula = <<<EOT
CREATE TABLE if not exists `T257` (
  `T257_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T257_T256_Id` int(10) unsigned DEFAULT NULL,
  `T257_C007_Id` int(10) unsigned DEFAULT NULL,
  `T257_T066_Id` int(10) unsigned DEFAULT NULL,
  `T257_Data_Producao` date DEFAULT '0000-00-00',
  `T257_Quantidade` float(14,5) DEFAULT '0.00000',
  PRIMARY KEY (`T257_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T256A
$cClausula  = <<<EOT
create table if not exists T256A (
    T256A_Id                              int(10) unsigned auto_increment,
    T256A_T256_Id                         int(10),
    PRIMARY KEY (T256A_Id),
    KEY IDX_T256A_T256_Id (T256A_T256_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

// criação da tabela D013A
$cClausula  = <<<EOT
create table if not exists D013A (
    D013A_Id                              int(10) unsigned auto_increment,
    D013A_D013_Id                         int(10),
    PRIMARY KEY (D013A_Id),
    KEY IDX_D013A_D013_Id (D013A_D013_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

//D155 - Tipo agenda
$cClausula = <<<EOT
CREATE TABLE if not exists `D155` (
  `D155_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D155_Tipo` varchar(100) DEFAULT '',
  `D155_Flag_Ativo` char(1) DEFAULT 'S',
  PRIMARY KEY (`D155_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T258 - Email whitelist
$cClausula = <<<EOT
CREATE TABLE if not exists `T258` (
  `T258_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T258_Registro` varchar(255) DEFAULT '',
  `T258_Tipo` char(1) DEFAULT 'E',
  `T258_D024_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T258_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T259 - Tarefas
$cClausula = <<<EOT
CREATE TABLE if not exists `T259` (
  `T259_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T259_Data_Hora` datetime DEFAULT '0000-00-00 00:00:00',
  `T259_D024_Id` int(10) unsigned DEFAULT NULL,
  `T259_C007_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T259_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//T260 - Confirmaçaõ de leitura das tarefas
$cClausula = <<<EOT
CREATE TABLE if not exists `T260` (
  `T260_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T260_T259_Id` int(10) unsigned DEFAULT NULL,
  `T260_C007_Id` int(10) unsigned DEFAULT NULL,
  `T260_Data_Hora` datetime DEFAULT '0000-00-00 00:00:00',
  `T260_Flag_Lido` char(1) DEFAULT 'S',
  PRIMARY KEY (`T260_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//D156 - Produtos Similares
$cClausula = <<<EOT
CREATE TABLE if not exists `D156` (
  `D156_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D156_D001_Id_1` int(10) unsigned DEFAULT NULL,
  `D156_D001_Id_2` int(10) unsigned DEFAULT NULL,
  `D156_Observacao` text ,
  PRIMARY KEY (`D156_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Itens da composição da produção de um produto (Utilizado na ordem de produção)
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T261 (
    T261_Id int(10) unsigned auto_increment,
    T261_D009_Id int(10) unsigned,
    T261_Quantidade int(10),
    T261_T256_Id int(10) unsigned,
    PRIMARY KEY (T261_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Processos da produção do produto (Utilizado na ordem de produção)
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T262 (
  T262_Id int(10) NOT NULL AUTO_INCREMENT,
  T262_D079_Id int(10) NOT NULL,
  T262_D078_Id int(10) NOT NULL,
  T262_Lead_Time decimal(5,2) DEFAULT '0.00',
  T262_Descricao text,
  T262_Ordem int(2) DEFAULT '0',
  T262_Numero_Funcionarios int(10) DEFAULT NULL,
  T262_T256_Id int(10) unsigned,
  PRIMARY KEY (`T262_Id`),
  KEY `T262_FKIndex2` (`T262_D078_Id`),
  KEY `T262_FKIndex3` (`T262_D079_Id`)
)
ENGINE=InnoDB
DEFAULT
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Materiais de Consumo de um processo de produção (Utilizado na ordem de produção)
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T263 (
    T263_Id int(10) unsigned auto_increment,
    T263_T262_Id int(10) unsigned,
    T263_D009_Id int(10) unsigned,
    T263_Quantidade int(10),
    PRIMARY KEY (T263_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Lista de materiais de consumo e materia prima a serem baixados para produção
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T264 (
    T264_Id int(10) unsigned auto_increment,
    T264_T255_Id int(10) unsigned,
    T264_D009_Id int(10) unsigned,
    T264_T066_Id int(10) unsigned,
    T264_Quantidade int(10),
    PRIMARY KEY (T264_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


//Notificações
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS N001 (
    N001_Id int(10) unsigned auto_increment,
    N001_C004_Id int(10) unsigned,
    N001_C007_Id int(10) unsigned,
    N001_C029 varchar(50),
    N001_C031 varchar(255),
    N001_Flag_Alcada varchar(1),
    N001_Nome_Notificacao varchar(40),
    N001_Id_Mensagem varchar(40),
    N001_Id_Mensagem_Md5 varchar(32),
    N001_Mensagem varchar(100),
    N001_C004_Nome_Abreviado varchar(100),
    N001_Funcao_JS text,
    N001_Flag_Excluido varchar(1) DEFAULT 'N',
    KEY IDX_N001_Id_Mensagem (N001_Id_Mensagem),
    KEY IDX_N001_Nome_Notificacao (N001_Nome_Notificacao),
    KEY IDX_N001_Id_Mensagem_Md5 (N001_Id_Mensagem_Md5),
    KEY IDX_N001_C004_Id (N001_C004_Id),
    KEY IDX_N001_C007_Id (N001_C007_Id),
    PRIMARY KEY (N001_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Logs (crontab...)
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T265 (
    T265_Id int(10) unsigned auto_increment,
    T265_Data_Inicio_Log datetime,
    T265_Data_Fim_Log datetime,
    T265_Referencia varchar(50),
    T265_Descricao varchar(200),
    PRIMARY KEY (T265_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela temporaria de inclusão de produtos no pedido
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T266 (
    T266_Id int(10) unsigned auto_increment,
    T266_D009_Id int(10) unsigned,
    T266_C007_Id int(10) unsigned,
    T266_D024_Id int(10) unsigned,
    T266_Valor_Preco_Sem_Desconto_Unitario decimal(19,5) DEFAULT '0.00000',
    T266_Quantidade decimal(12,5) DEFAULT '0.00000',
    PRIMARY KEY (T266_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// Tabela cabeçalho dos pedidos periódicos
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D157 (
  D157_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D157_T064_Id bigint(20) unsigned DEFAULT NULL,
  D157_C008_Id int(11) DEFAULT NULL,
  D157_D027_Id_Entrada int(10) unsigned DEFAULT NULL,
  D157_D027_Id int(10) unsigned DEFAULT NULL,
  D157_D047_Id int(10) unsigned DEFAULT NULL,
  D157_D024_Id int(10) unsigned DEFAULT NULL,
  D157_D021_Id int(10) unsigned DEFAULT NULL,
  D157_D022_Id int(10) unsigned DEFAULT NULL,
  D157_D036_Id int(10) unsigned DEFAULT NULL,
  D157_T003_Id int(10) unsigned DEFAULT NULL,
  D157_D006_Id int(10) unsigned DEFAULT NULL,
  D157_C007_Id_Vendedor_Interno int(10) unsigned DEFAULT NULL,
  D157_C007_Id_Vendedor_Externo int(10) unsigned DEFAULT NULL,
  D157_C007_Id_IPV_Liberado int(10) unsigned DEFAULT NULL,
  D157_C007_Id_Devolucao int(5) DEFAULT '0',
  D157_C004_Id int(11) NOT NULL DEFAULT '0',
  D157_Pedido_Ecommerce varchar(20) DEFAULT NULL,
  D157_Numero_Pedido_Venda int(10) DEFAULT NULL,
  D157_Data_Emissao date DEFAULT NULL,
  D157_Data_Saida date DEFAULT '0000-00-00',
  D157_Hora_Saida time DEFAULT '00:00:00',
  D157_Data_Entrega date DEFAULT NULL,
  D157_Valor_Desconto decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Percentual_Desconto decimal(30,20) DEFAULT NULL,
  D157_Valor_Percentual_Desconto decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Observacao_1 text,
  D157_Observacao_2 varchar(70) DEFAULT NULL,
  D157_Observacao_3 varchar(70) DEFAULT NULL,
  D157_Prazos_Pagamentos varchar(40) DEFAULT NULL,
  D157_Nome_Vendedor varchar(30) DEFAULT NULL,
  D157_Mao_Obra_1 text,
  D157_Mao_Obra_2 text,
  D157_Mao_Obra_3 text,
  D157_Mao_Obra_4 text,
  D157_Flag_Revenda_Consumidor varchar(1) DEFAULT NULL,
  D157_Valor_Mao_Obra decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_NF_ACP varchar(1) DEFAULT NULL,
  D157_Valor_Dolar decimal(10,4) NOT NULL DEFAULT '0.0000',
  D157_Flag_ACP varchar(1) DEFAULT NULL,
  D157_Prazo_1 int(4) NOT NULL DEFAULT '0',
  D157_Prazo_2 int(4) NOT NULL DEFAULT '0',
  D157_Prazo_3 int(4) NOT NULL DEFAULT '0',
  D157_Prazo_4 int(4) NOT NULL DEFAULT '0',
  D157_Prazo_5 int(4) NOT NULL DEFAULT '0',
  D157_Prazo_6 int(4) NOT NULL DEFAULT '0',
  D157_Prazo_7 int(4) NOT NULL DEFAULT '0',
  D157_Flag_Frete int(1) DEFAULT NULL,
  D157_NF_Observacao text,
  D157_Numero_NF_Devolucao varchar(12) DEFAULT NULL,
  D157_Data_Emissao_NF_Devolucao date DEFAULT NULL,
  D157_Aliquota_ICMS_NF_Devolucao decimal(5,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Frete decimal(19,2) NOT NULL DEFAULT '0.00',
  D157_Flag_Situacao varchar(1) DEFAULT NULL,
  D157_Valor_Despesas_Acessorias decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Desconto_NF decimal(12,6) NOT NULL DEFAULT '0.000000',
  D157_Valor_Encargos_NF decimal(12,6) NOT NULL DEFAULT '0.000000',
  D157_Observacao_Nota_Fiscal text,
  D157_Numero_Orcamento int(10) DEFAULT NULL,
  D157_Codigo_Cliente varchar(6) DEFAULT NULL,
  D157_Codigo_Representante varchar(6) DEFAULT NULL,
  D157_Codigo_Transportadora varchar(6) DEFAULT NULL,
  D157_Codigo_Natureza int(5) NOT NULL DEFAULT '0',
  D157_Codigo_Usuario int(4) NOT NULL DEFAULT '0',
  D157_IPV decimal(10,4) NOT NULL DEFAULT '0.0000',
  D157_IPV_Cliente decimal(10,4) NOT NULL DEFAULT '0.0000',
  D157_IPV_Data_Liberado datetime DEFAULT NULL,
  D157_IPV_Liberado decimal(10,4) NOT NULL DEFAULT '0.0000',
  D157_IPV_Normal decimal(10,4) NOT NULL DEFAULT '0.0000',
  D157_Valor_Total_Custo decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total_Produtos decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total_Produtos_Sem_Desconto decimal(10,2) DEFAULT '0.00',
  D157_Valor_Total_Custo_Final decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Percentual_Comissao decimal(5,2) DEFAULT '0.00',
  D157_Flag_Travar_Pedido int(1) DEFAULT '2',
  D157_Flag_Almoxarifado varchar(1) DEFAULT 'N',
  D157_Flag_Status int(2) DEFAULT '0',
  D157_Flag_Devolucao varchar(1) DEFAULT 'N',
  D157_Flag_Tipo_Devolucao varchar(10) DEFAULT NULL,
  D157_Percentual_Reducao_ICMS decimal(5,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total_Base_ICMS decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Aliquota_ICMS decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total_ICMS decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Base_ICMS_Substituicao decimal(14,2) DEFAULT '0.00',
  D157_Valor_ICMS_Substituicao decimal(14,2) DEFAULT '0.00',
  D157_Valor_ICMS_Substituicao_Retencao decimal(14,2) DEFAULT '0.00',
  D157_Percentual_Reducao_IPI decimal(5,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total_Base_IPI decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Aliquota_IPI decimal(5,2) NOT NULL DEFAULT '0.00',
  D157_Valor_Total_IPI decimal(10,2) NOT NULL DEFAULT '0.00',
  D157_Aliquota_ISS decimal(5,2) DEFAULT '0.00',
  D157_Valor_ISS decimal(10,2) DEFAULT '0.00',
  D157_Valor_Total_PIS decimal(10,2) DEFAULT '0.00',
  D157_Valor_Total_COFINS decimal(10,2) DEFAULT '0.00',
  D157_Data_Separacao_Pecas datetime DEFAULT NULL,
  D157_Usuario_Pecas int(11) DEFAULT NULL,
  D157_Liberacao_Credito_Id int(11) DEFAULT '0',
  D157_Peso_Total decimal(10,3) DEFAULT '0.000',
  D157_Observacao text,
  D157_Fax varchar(30) DEFAULT NULL,
  D157_Nome_Comprador varchar(30) DEFAULT NULL,
  D157_E_mail_Comprador varchar(60) DEFAULT NULL,
  D157_Prazo_Entrega varchar(40) DEFAULT NULL,
  D157_Observacao_Pedido_Perdido text,
  D157_Nome_Transportadora varchar(35) DEFAULT NULL,
  D157_Transportadora_Placa_Veiculo varchar(8) DEFAULT NULL,
  D157_Estado_Veiculo char(2) DEFAULT NULL,
  D157_Valor_Entrada decimal(10,2) DEFAULT '0.00',
  D157_Numero_Parcelas int(2) DEFAULT '0',
  D157_Numero_Dias_Entrada int(3) DEFAULT '0',
  D157_Numero_Dias int(3) DEFAULT '0',
  D157_Quantidade_Volumes int(15) unsigned DEFAULT '0',
  D157_Especie varchar(20) DEFAULT NULL,
  D157_Marca varchar(20) DEFAULT NULL,
  D157_Numero_Caixas varchar(20) DEFAULT NULL,
  D157_Peso_Liquido decimal(10,3) DEFAULT '0.000',
  D157_Peso_Bruto decimal(10,3) DEFAULT '0.000',
  D157_Flag_Considerar_Dias_Entrada varchar(1) DEFAULT 'S',
  D157_Motivo_Nao_Liberado tinytext,
  D157_Flag_Reenviado_Almoxarifado enum('S','N') DEFAULT 'N',
  D157_Alteracoes_Reenviado_Almoxarifado text,
  D157_Data_Hora_Almoxarifado datetime DEFAULT NULL,
  D157_Flag_Separacao varchar(1) DEFAULT NULL,
  D157_Flag_Separacao_Liberar varchar(1) DEFAULT NULL,
  D157_Numero_Nota_Manual varchar(20) DEFAULT NULL,
  D157_Flag_Expedicao int(2) DEFAULT NULL,
  D157_Flag_Dividido varchar(1) DEFAULT NULL,
  D157_Observacao_Separacao varchar(100) DEFAULT NULL,
  D157_Flag_Lista_Preco varchar(1) DEFAULT ' ',
  D157_Flag_Aguardar_Liberacao int(1) DEFAULT '0',
  D157_Observacao_Comercial text,
  D157_ST_VA decimal(10,2) DEFAULT '0.00',
  D157_ST_ICMS_Interno decimal(5,2) DEFAULT '0.00',
  D157_ICMS_Inter_Estadual decimal(5,2) DEFAULT '0.00',
  D157_Origem_UF char(2) DEFAULT NULL,
  D157_Origem_Ano_Mes char(4) DEFAULT NULL,
  D157_Origem_Cnpj varchar(14) DEFAULT NULL,
  D157_Origem_Modelo char(2) DEFAULT NULL,
  D157_Origem_Serie int(3) DEFAULT NULL,
  D157_Origem_Chave_Acesso varchar(44) DEFAULT NULL,
  D157_Flag_Imprimir_Romaneio enum('S','N') DEFAULT 'N',
  D157_Data_Impressao_Romaneio datetime DEFAULT '0000-00-00 00:00:00',
  D157_Flag_Importar_Pendente enum('S','N') DEFAULT 'N',
  D157_Estoque_Box varchar(20) DEFAULT ' ',
  D157_D018_Id int(10) unsigned DEFAULT NULL,
  D157_D013_Id int(10) unsigned DEFAULT NULL,
  D157_Observacao_Fatura_Emergencia text,
  D157_Observacao_Pagamento text,
  D157_Valor_Troca decimal(10,2) DEFAULT NULL,
  D157_D024_Id_Nfe int(10) unsigned DEFAULT NULL,
  D157_D024_Id_Contas int(10) unsigned DEFAULT NULL,
  D157_Ordem_Compra varchar(40) DEFAULT NULL,
  D157_D022_Id_Default int(10) unsigned DEFAULT NULL,
  D157_Pgto_Parcelas varchar(10) DEFAULT NULL,
  D157_Pgto_Primeiro_Vcto_Tipo varchar(10) DEFAULT NULL,
  D157_Pgto_Primeiro_Vcto_Valor varchar(10) DEFAULT NULL,
  D157_Pgto_Dias_Vcto_Tipo varchar(10) DEFAULT NULL,
  D157_Pgto_Dias_Vcto_Valor varchar(10) DEFAULT NULL,
  D157_Pgto_Portador varchar(10) DEFAULT NULL,
  D157_Pgto_Data datetime DEFAULT NULL,
  D157_Pgto_C007_Id int(10) unsigned DEFAULT NULL,
  D157_D116_Id int(10) unsigned DEFAULT NULL,
  D157_Valor_Total_Servicos decimal(10,2) DEFAULT NULL,
  D157_Liberou_Alcada_C007_Id int(11) DEFAULT NULL,
  D157_Liberou_Alcada_Data datetime DEFAULT NULL,
  D157_Liberou_Alcada_IPV decimal(10,4) DEFAULT NULL,
  D157_Liberou_Alcada_Valor_Total decimal(10,2) DEFAULT NULL,
  D157_Flag_Alcada varchar(1) DEFAULT NULL,
  D157_Liberou_Alcada_Prazo_Medio decimal(10,4) DEFAULT NULL,
  D157_Liberou_Alcada_Desconto decimal(10,2) DEFAULT NULL,
  D157_Liberou_Alcada_Observacao_Nao_Liberado text,
  D157_Valor_Desconto_Manual decimal(14,2) DEFAULT NULL,
  D157_Flag_Status_Liberacao varchar(1) DEFAULT NULL,
  D157_C007_Id_Separou int(10) unsigned DEFAULT NULL,
  D157_Hora_Inicio_Servico varchar(30) DEFAULT NULL,
  D157_Hora_Fim_Servico varchar(30) DEFAULT NULL,
  D157_Flag_Aguardando_Entrega char(1) DEFAULT 'S',
  D157_Flag_Contribuinte int(5) DEFAULT NULL,
  D157_Flag_Esta_No_Caixa varchar(1) DEFAULT 'S',
  D157_D157_Id_Origem int(10) unsigned DEFAULT NULL,
  D157_Flag_Status_Servicos int(1) DEFAULT '1',
  D157_Flag_Status_Financeiro int(1) DEFAULT '1',
  D157_Data_Finalizado_Caixa date DEFAULT NULL,
  D157_Motivo_Pedido_Liberacao text,
  D157_T134_Id int(5) DEFAULT '0',
  D157_Valor_Total_Agregados decimal(10,2) DEFAULT '0.00',
  D157_Flag_Coleta char(1) NOT NULL DEFAULT 'N',
  D157_Valor_Total_Comissao decimal(14,5) DEFAULT '0.00000',
  D157_T208_Id int(10) unsigned DEFAULT NULL,
  D157_Flag_Duplicar char(1) DEFAULT 'N',
  D157_D157_Id_Agrupado int(10) DEFAULT '0',
  D157_C007_Id_Conferiu int(10) DEFAULT NULL,
  D157_T007_Id int(10) DEFAULT NULL,
  D157_Flag_Gerar_NF char(1) DEFAULT NULL,
  D157_C007_Id_Embalagem int(10) NOT NULL,
  D157_Codigo_Rastreio varchar(30) DEFAULT '',
  D157_Custo_Frete decimal(14,2) DEFAULT '0.00',
  D157_C007_Gerar_NF int(10) DEFAULT '0',
  D157_Processo_Id int(10) DEFAULT NULL,
  D157_Processo_Log varchar(255) DEFAULT NULL,
  D157_Valor_Total_ICMS_Desonerado decimal(15,2) NOT NULL DEFAULT '0.00',
  D157_Motivo_Nao_Pedido_Liberacao text,
  D157_Valor_Total_ICMS_Oculto decimal(14,2) DEFAULT NULL,
  D157_Valor_Total_Base_ICMS_Oculto decimal(14,2) DEFAULT NULL,
  D157_Validade_Proposta varchar(50) DEFAULT NULL,
  D157_Peso_Bruto_Total decimal(10,3) DEFAULT NULL,
  D157_Flag_Totalizar_Peso varchar(1) NOT NULL DEFAULT 'N',
  D157_Total_Cubagem decimal(10,6) DEFAULT '0.000000',
  D157_Percentual_Comissao_Manual decimal(5,2) DEFAULT '0.00',
  D157_Valor_Total_Trib_Transparencia decimal(15,6) DEFAULT '0.000000',
  D157_Flag_Destino_Produto varchar(1) DEFAULT '',
  D157_D146_Id int(10) DEFAULT '1',
  D157_Liberou_Portador_Pagamento varchar(60) DEFAULT NULL,
  D157_Liberou_Parcelas_Pagamento int(10) DEFAULT NULL,
  D157_Flag_Notificacao varchar(1) DEFAULT 'N',
  D157_Flag_Notificacao_Estoque varchar(1) DEFAULT 'N',
  D157_Flag_Notificacao_Liberacao varchar(1) DEFAULT '',
  D157_Flag_Notificacao_Financeiro varchar(1) DEFAULT '',
  D157_Flag_Primeira_Liberacao_Credito varchar(1) DEFAULT '0',
  D157_Prazo_Medio_Orcamento int(10) DEFAULT NULL,
  D157_D148_Id_Entrega int(10) DEFAULT NULL,
  D157_D148_Id_Cobranca int(10) DEFAULT NULL,
  D157_Flag_Reenviado_Almoxarifado_Lido char(1) DEFAULT 'N',
  D157_Cliente_Area varchar(50) DEFAULT NULL,
  D157_Liberou_Credito_Valor_Total decimal(10,2) DEFAULT NULL,
  D157_Liberou_Credito_Desconto decimal(10,2) DEFAULT NULL,
  D157_Liberou_Credito_Prazo_Medio decimal(10,4) DEFAULT NULL,
  D157_T224_Id int(10) unsigned DEFAULT NULL,
  D157_C004_Id_Origem int(10) unsigned DEFAULT NULL,
  D157_Flag_Pdv_Importado char(1) DEFAULT 'N',
  D157_Casas_Decimais_Preco int(1) DEFAULT '4',
  D157_C007_Id int(10) unsigned DEFAULT NULL,
  D157_Percentual_Comissao_Representante decimal(5,2) DEFAULT '0.00',
  D157_Valor_Comissao_Representante decimal(14,5) DEFAULT '0.00000',
  D157_Percentual_Comissao_Externo decimal(5,2) DEFAULT '0.00',
  D157_Valor_Total_Comissao_Externo decimal(14,5) DEFAULT '0.00000',
  D157_Percentual_Comissao_Usuario decimal(5,2) DEFAULT '0.00',
  D157_Valor_Total_Comissao_Usuario decimal(14,5) DEFAULT '0.00000',
  D157_Percentual_Margem decimal(5,2) DEFAULT '0.00',
  D157_Percentual_Estoque_Disponivel decimal(5,2) DEFAULT '0.00',
  D157_Nome_Status char(30) NOT NULL DEFAULT 'PENDENTE',
  D157_Hora_Emissao time DEFAULT NULL,
  D157_D044_Id int(10) unsigned DEFAULT '1',
  D157_Cotacao_Moeda decimal(10,5) DEFAULT '0.00000',
  D157_Separacao_Lote int(11) DEFAULT NULL,
  D157_C004_Id_Estoque int(10) DEFAULT NULL,
  D157_Valor_Total_Base_IRPJ decimal(10,2) DEFAULT '0.00',
  D157_Valor_Total_IRPJ decimal(10,2) DEFAULT '0.00',
  D157_Valor_Total_Base_CSLL decimal(10,2) DEFAULT '0.00',
  D157_Valor_Total_CSLL decimal(10,2) DEFAULT '0.00',
  D157_Flag_Status_Cotacao char(25) DEFAULT '',
  D157_Tipo_Periodicidade char(25) DEFAULT '',
  D157_Periodicidade char(25) DEFAULT '',
  PRIMARY KEY (D157_Id),
  KEY D157_Index_Id (D157_Id),
  KEY D157_Index_C004_Id (D157_C004_Id),
  KEY D157_Index_Clientes (D157_D024_Id),
  KEY D157_Index_Natureza_Operacao (D157_D006_Id),
  KEY D157_Index_Vendedor_Interno (D157_C007_Id_Vendedor_Interno),
  KEY D157_Index_Vendedor_Externo (D157_C007_Id_Vendedor_Externo),
  KEY D157_Index_IPV_Liberado (D157_C007_Id_IPV_Liberado),
  KEY D157_Index_Revenda (D157_D021_Id),
  KEY D157_Index_Transportadora (D157_D022_Id),
  KEY D157_Index_Tipo_Transporte (D157_D036_Id),
  KEY D157_Index_Numero_Pedido_Venda (D157_Numero_Pedido_Venda),
  KEY D157_Index_Orcamento_Venda (D157_T003_Id),
  KEY D157_Index_Motivo (D157_D047_Id),
  KEY D157_Index_DATA_EMISSAO (D157_Data_Emissao),
  KEY D157_Index_SITUACAO (D157_Flag_Situacao),
  KEY D157_Index_PORTADOR_ENTRADA (D157_D027_Id_Entrada),
  KEY D157_Index_PORTADOR (D157_D027_Id),
  KEY IDX_C008 (D157_C008_Id),
  KEY IDX_D157_Flag_Status (D157_Flag_Status),
  KEY IDX_D157_D044_Id (D157_D044_Id),
  KEY IDX_D157_T224_Id (D157_T224_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D158 (
  D158_Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  D158_D059_Id int(11) unsigned DEFAULT NULL,
  D158_T008_Id int(10) unsigned DEFAULT NULL,
  D158_D158_Id int(10) unsigned DEFAULT NULL,
  D158_D005_Id int(10) unsigned DEFAULT NULL,
  D158_D037_Id int(10) unsigned DEFAULT NULL,
  D158_D006_Id int(10) unsigned DEFAULT NULL,
  D158_T004_Id int(10) unsigned DEFAULT NULL,
  D158_D024_Id int(10) unsigned DEFAULT NULL,
  D158_D157_Id int(10) unsigned DEFAULT NULL,
  D158_C007_Id_Separou int(11) unsigned DEFAULT NULL,
  D158_D009_Id int(10) unsigned DEFAULT NULL,
  D158_D004_Id int(10) unsigned DEFAULT NULL,
  D158_D040_Id int(10) unsigned DEFAULT NULL,
  D158_T014_Id int(10) unsigned DEFAULT NULL,
  D158_C004_Id int(10) unsigned NOT NULL DEFAULT '0',
  D158_Item int(4) NOT NULL DEFAULT '0',
  D158_Numero_Orcamento varchar(10) DEFAULT NULL,
  D158_Quantidade decimal(12,5) DEFAULT '0.00000',
  D158_Data_Entrega date DEFAULT NULL,
  D158_Valor_Custo_Unitario decimal(19,5) NOT NULL DEFAULT '0.00000',
  D158_Valor_Preco_Sem_Desconto_Unitario decimal(19,5) DEFAULT '0.00000',
  D158_Valor_Desconto_Unitario decimal(20,10) DEFAULT '0.0000000000',
  D158_Valor_Despesas_Unitario decimal(19,5) NOT NULL DEFAULT '0.00000',
  D158_Valor_Custo_Final_Unitario decimal(19,5) NOT NULL DEFAULT '0.00000',
  D158_Valor_Frete_Unitario decimal(20,10) DEFAULT '0.0000000000',
  D158_Valor_Despesas_Acessorias_Unitario decimal(20,10) DEFAULT '0.0000000000',
  D158_Valor_Preco_Unitario decimal(19,5) NOT NULL DEFAULT '0.00000',
  D158_Descricao_Produto varchar(120) DEFAULT NULL,
  D158_Codigo_Substituto varchar(18) DEFAULT NULL,
  D158_Flag_Status varchar(1) DEFAULT '',
  D158_Numero_Pedido_Venda varchar(6) DEFAULT NULL,
  D158_Codigo_Fornecedor varchar(6) DEFAULT NULL,
  D158_Codigo_Local varchar(10) DEFAULT NULL,
  D158_Codigo_Produto varchar(30) DEFAULT NULL,
  D158_Valor_Total_Custo decimal(10,2) NOT NULL DEFAULT '0.00',
  D158_Valor_Total_Preco_Sem_Desconto decimal(20,2) DEFAULT '0.00',
  D158_Valor_Total_Preco decimal(20,2) DEFAULT '0.00',
  D158_Valor_Total_Desconto decimal(20,10) DEFAULT '0.0000000000',
  D158_Valor_Total_Despesas decimal(10,2) NOT NULL DEFAULT '0.00',
  D158_Valor_Total_Custo_Final decimal(10,2) NOT NULL DEFAULT '0.00',
  D158_Valor_Total_Frete decimal(20,10) DEFAULT '0.0000000000',
  D158_Valor_Total_Despesas_Acessorias decimal(20,10) DEFAULT '0.0000000000',
  D158_IPV decimal(10,4) DEFAULT NULL,
  D158_Percentual_Reducao_ICMS decimal(5,2) NOT NULL DEFAULT '0.00',
  D158_Valor_Base_ICMS decimal(10,2) NOT NULL DEFAULT '0.00',
  D158_Modalidade_ICMS int(1) DEFAULT '3',
  D158_Aliquota_ICMS decimal(5,2) NOT NULL DEFAULT '0.00',
  D158_Valor_ICMS decimal(15,2) DEFAULT '0.00',
  D158_Aliquota_ICMS_Credito decimal(5,2) DEFAULT '0.00',
  D158_Valor_ICMS_Credito decimal(15,2) DEFAULT '0.00',
  D158_Percentual_Base_Operacao_Propria decimal(5,2) DEFAULT '0.00',
  D158_Valor_Agregado decimal(20,2) DEFAULT '0.00',
  D158_Valor_Base_ICMS_Substituicao decimal(20,2) DEFAULT '0.00',
  D158_Modalidade_ICMS_Substituicao int(1) DEFAULT '4',
  D158_Percentual_Reducao_ICMS_ST decimal(5,2) DEFAULT '0.00',
  D158_Aliquota_ICMS_ST decimal(5,2) DEFAULT '0.00',
  D158_Valor_ICMS_Substituicao decimal(20,2) DEFAULT '0.00',
  D158_Valor_ICMS_Substituicao_Retencao decimal(20,2) DEFAULT '0.00',
  D158_Valor_Base_ICMS_Substituicao_Retido decimal(15,2) DEFAULT '0.00',
  D158_Valor_ICMS_Substituicao_Retido decimal(15,2) DEFAULT '0.00',
  D158_Valor_Base_ICMS_Substituicao_Destino decimal(15,2) DEFAULT '0.00',
  D158_Valor_ICMS_Substituicao_Destino decimal(15,2) DEFAULT '0.00',
  D158_UF_ICMS_Substituicao_Devido char(2) DEFAULT NULL,
  D158_Percentual_Reducao_IPI decimal(5,2) NOT NULL DEFAULT '0.00',
  D158_Valor_Base_IPI decimal(10,2) NOT NULL DEFAULT '0.00',
  D158_Aliquota_IPI decimal(5,2) NOT NULL DEFAULT '0.00',
  D158_Valor_IPI decimal(10,2) NOT NULL DEFAULT '0.00',
  D158_Situacao_Tributaria_IPI int(2) unsigned zerofill DEFAULT NULL,
  D158_Marca varchar(20) DEFAULT NULL,
  D158_Peso_Unitario decimal(10,3) DEFAULT '0.000',
  D158_Peso_Total decimal(10,3) DEFAULT '0.000',
  D158_Ultimo_Preco varchar(50) DEFAULT NULL,
  D158_Ultimo_Preco_Confirmado varchar(50) DEFAULT NULL,
  D158_Flag_Excluir int(1) DEFAULT '2',
  D158_Flag_Preco_Especial varchar(1) DEFAULT NULL,
  D158_Classificacao_Fiscal varchar(12) DEFAULT NULL,
  D158_Flag_Comprar varchar(1) DEFAULT NULL,
  D158_D158_Id_Dividir int(10) DEFAULT NULL,
  D158_Quantidade_Dividir decimal(10,2) DEFAULT NULL,
  D158_Quantidade_Separacao decimal(12,5) DEFAULT NULL,
  D158_Quantidade_Etiqueta decimal(10,3) DEFAULT '0.000',
  D158_Quantidade_Itens_Etiqueta decimal(10,3) DEFAULT '0.000',
  D158_Quantidade_Dividido decimal(10,2) DEFAULT NULL,
  D158_Flag_ST varchar(1) DEFAULT 'N',
  D158_ICMS_Inter_Estadual decimal(5,2) DEFAULT '0.00',
  D158_ST_ICMS_Interno decimal(5,2) DEFAULT '0.00',
  D158_ST_VA decimal(10,2) DEFAULT '0.00',
  D158_Situacao_Tributaria_PIS varchar(2) DEFAULT '01',
  D158_Flag_Isento_PIS enum('S','N') DEFAULT 'N',
  D158_Valor_Base_PIS decimal(20,2) DEFAULT '0.00',
  D158_Aliquota_PIS decimal(5,2) DEFAULT '0.00',
  D158_Valor_PIS decimal(10,2) DEFAULT '0.00',
  D158_Situacao_Tributaria_COFINS varchar(2) DEFAULT '01',
  D158_Flag_Isento_COFINS enum('S','N') DEFAULT 'N',
  D158_Valor_Base_COFINS decimal(20,2) DEFAULT '0.00',
  D158_Aliquota_COFINS decimal(5,2) DEFAULT '0.00',
  D158_Valor_COFINS decimal(10,2) DEFAULT '0.00',
  D158_Flag_Situacao_Tributaria varchar(4) DEFAULT NULL,
  D158_T099_Id int(11) DEFAULT '0',
  D158_Quantidade_Comprar decimal(10,3) DEFAULT '0.000',
  D158_Reordenar int(1) DEFAULT '1',
  D158_Flag_Tipo_Produto varchar(2) DEFAULT NULL,
  D158_D049_Id int(11) unsigned DEFAULT NULL,
  D158_Quantidade_Estoque decimal(12,2) DEFAULT NULL,
  D158_Observacao_Divergencia varchar(100) DEFAULT '',
  D158_Estoque_BOX varchar(20) DEFAULT '',
  D158_Flag_Faturar varchar(1) DEFAULT 'N',
  D158_D009_Id_Comprar int(10) DEFAULT NULL,
  D158_Flag_Cotar varchar(1) DEFAULT 'N',
  D158_Quantidade_Estoque_Padrao decimal(12,2) DEFAULT NULL,
  D158_Flag_Divergencia varchar(1) DEFAULT 'N',
  D158_Flag_Dividir varchar(1) DEFAULT 'N',
  D158_Observacao_Compra varchar(100) DEFAULT '',
  D158_Data_Separacao datetime DEFAULT '0000-00-00 00:00:00',
  D158_Valor_Custo_Compra_Unitario decimal(10,5) DEFAULT NULL,
  D158_Valor_Custo_Tabela_Unitario decimal(10,5) DEFAULT NULL,
  D158_Numero_Pedido_Compra varchar(15) DEFAULT NULL,
  D158_Item_Pedido_Compra int(6) DEFAULT NULL,
  D158_D082_Id int(10) unsigned DEFAULT NULL,
  D158_Valor_Preco_Original decimal(14,5) DEFAULT '0.00000',
  D158_Percentual_Desconto decimal(10,5) DEFAULT NULL,
  D158_Observacao text,
  D158_Quantidade_Entregar_Temp int(4) DEFAULT '0',
  D158_Retirou_Qtd decimal(20,5) DEFAULT NULL,
  D158_Flag_Comissao char(1) DEFAULT 'N',
  D158_T075_Id int(10) DEFAULT '0',
  D158_Liberou_Alcada_Saldo_Futuro decimal(10,2) DEFAULT '0.00',
  D158_D009_Id_Kit int(10) unsigned DEFAULT NULL,
  D158_Valor_Total_Agregados decimal(10,2) DEFAULT '0.00',
  D158_Valor_Preco_Bruto decimal(15,5) DEFAULT '0.00000',
  D158_ST_ICMS_Interno_MVA decimal(14,2) DEFAULT NULL,
  D158_ICMS_Inter_Estadual_MVA decimal(14,2) DEFAULT NULL,
  D158_Valor_Preco_Original_Bruto decimal(14,5) DEFAULT '0.00000',
  D158_Valor_Desconto_Unitario_Bruto decimal(14,5) DEFAULT '0.00000',
  D158_Percentual_Desconto_Bruto decimal(14,5) DEFAULT '0.00000',
  D158_T206_Id int(10) unsigned DEFAULT NULL,
  D158_Valor_Comissao decimal(14,5) DEFAULT '0.00000',
  D158_D022_Id int(10) unsigned DEFAULT NULL,
  D158_T208_Id int(10) unsigned DEFAULT NULL,
  D158_Percentual_Comissao decimal(14,5) DEFAULT '0.00000',
  D158_Hora_Entrega time DEFAULT NULL,
  D158_D009_Id_Loja int(10) unsigned DEFAULT NULL,
  D158_Valor_ICMS_Desonerado decimal(15,2) NOT NULL DEFAULT '0.00',
  D158_Valor_Base_ICMS_Desonerado decimal(20,5) NOT NULL DEFAULT '0.00000',
  D158_Aliquota_ICMS_Oculto decimal(14,2) DEFAULT '0.00',
  D158_Motivo_Desoneracao_ICMS varchar(10) DEFAULT NULL,
  D158_Flag_Cancelado char(1) DEFAULT 'N',
  D158_Valor_ICMS_Oculto decimal(14,2) DEFAULT NULL,
  D158_Valor_Base_ICMS_Oculto decimal(14,2) DEFAULT NULL,
  D158_Percentual_Reducao_ICMS_Oculto decimal(14,2) DEFAULT NULL,
  D158_Flag_Origem_Produto enum('0','1','2','3','4','5','6','7') DEFAULT NULL,
  D158_T219_Id int(10) unsigned DEFAULT NULL,
  D158_T225_Id int(10) unsigned DEFAULT NULL,
  D158_Peso_Bruto decimal(10,3) DEFAULT NULL,
  D158_Peso_Bruto_Total decimal(10,3) DEFAULT NULL,
  D158_Cubagem_Unitaria decimal(10,6) DEFAULT '0.000000',
  D158_Total_Cubagem decimal(10,6) DEFAULT '0.000000',
  D158_Valor_Trib_Transparencia decimal(15,6) DEFAULT '0.000000',
  D158_Percentual_Trib_Transparencia decimal(15,2) DEFAULT '0.00',
  D158_Flag_Modificar varchar(1) DEFAULT NULL,
  D158_Flag_Pre_Cadastro varchar(1) DEFAULT NULL,
  D158_D144_Id int(10) unsigned DEFAULT NULL,
  D158_C007_Id_Comprador int(10) unsigned DEFAULT NULL,
  D158_Flag_Destino_Produto varchar(1) DEFAULT '',
  D158_Observacao_Entrega int(5) DEFAULT NULL,
  D158_Flag_Reservar_Estoque varchar(1) DEFAULT 'S',
  D158_Flag_Conf_Cega char(1) DEFAULT 'N',
  D158_Flag_Gerar_Cotacao char(1) DEFAULT 'N',
  D158_Quantidade_OC decimal(15,5) DEFAULT '0.00000',
  D158_Quantidade_OP decimal(15,5) DEFAULT '0.00000',
  D158_Prioridade int(10) DEFAULT '0',
  D158_T066_Id int(10) unsigned DEFAULT NULL,
  D158_T001_Id int(11) DEFAULT NULL,
  D158_Demonstrativo_Preco text,
  D158_Demonstrativo_Preco_Tabela text,
  D158_Demonstrativo_Preco_Cotacao text,
  D158_Valor_Preco_Compra decimal(14,5) DEFAULT '0.00000',
  D158_Valor_Preco_Tabela decimal(14,5) DEFAULT '0.00000',
  D158_Valor_Preco_Cotacao decimal(14,5) DEFAULT '0.00000',
  D158_D006_Id_Info text,
  D158_D005_Id_Info text,
  D158_C004_Id_Estoque int(10) unsigned DEFAULT NULL,
  D158_D158_Id_Origem int(10) unsigned DEFAULT NULL,
  D158_Flag_Pdv_Importado char(1) DEFAULT 'N',
  D158_Percentual_Margem decimal(5,2) DEFAULT '0.00',
  D158_Valor_Lucro decimal(10,2) DEFAULT NULL,
  D158_IPV_Sem_Lucro decimal(10,4) DEFAULT NULL,
  D158_Demonstrativo_Custo_Tabela text,
  D158_Aliquota_IRPJ decimal(5,2) DEFAULT '0.00',
  D158_Valor_Base_IRPJ decimal(20,5) DEFAULT '0.00000',
  D158_Valor_IRPJ decimal(15,2) DEFAULT '0.00',
  D158_Aliquota_CSLL decimal(5,2) DEFAULT '0.00',
  D158_Valor_Base_CSLL decimal(20,5) DEFAULT '0.00000',
  D158_Valor_CSLL decimal(15,2) DEFAULT '0.00',
  D158_Demonstrativo_Preco_Venda text,
  D158_Flag_Isento_IRPJ enum('S','N') DEFAULT 'N',
  D158_Flag_Isento_CSLL enum('S','N') DEFAULT 'N',
  D158_Flag_Transferencia enum('S','N') DEFAULT 'N',
  D158_C004_Id_Transferencia int(10) DEFAULT NULL,
  D158_Valor_Preco_Transferencia decimal(14,5) DEFAULT '0.00000',
  D158_Valor_Custo_Transferencia decimal(15,5) DEFAULT '0.00000',
  D158_Flag_Origem_Produto_Transferencia enum('0','1','2','3','4','5','6','7') DEFAULT NULL,
  D158_Demonstrativo_Preco_Transferencia text,
  PRIMARY KEY (D158_Id),
  KEY D158_IDX_UNIDADE (D158_D037_Id),
  KEY D158_IDX_ITENS_PEDIDO_VENDA (D158_D158_Id),
  KEY D158_IDX_CLASSIFICACAO_FISCAL (D158_D005_Id),
  KEY D158_IDX_NATUREZA (D158_D006_Id),
  KEY D158_IDX_PRODUTO_ESTOQUE (D158_D009_Id),
  KEY D158_IDX_LOCAL (D158_D004_Id),
  KEY D158_IDX_MAQUINAS (D158_D040_Id),
  KEY D158_IDX_ITENS_ORCAMENTO (D158_T004_Id),
  KEY D158_IDX_PEDIDO_VENDA (D158_D158_Id),
  KEY D158_IDX_FORNECEDOR (D158_D024_Id),
  KEY D158_IDX_CODIGO_PEDIDO_VENDA (D158_Numero_Pedido_Venda),
  KEY D158_IDX_CODIGO_FORNECEDOR (D158_Codigo_Fornecedor),
  KEY D158_IDX_CODIGO_LOCAL (D158_Codigo_Local),
  KEY D158_IDX_CODIGO_PRODUTO (D158_Codigo_Produto),
  KEY D158_IDX_ITENS_NOTA_RECEBIDA (D158_T014_Id),
  KEY D158_IDX_ITENS_NOTA_EMITIDA (D158_T008_Id),
  KEY D158_IDX_EMPRESAS (D158_C004_Id),
  KEY D158_T099_Id (D158_T099_Id),
  KEY IDX_D158_Flag_Divergencia (D158_Flag_Divergencia),
  KEY IDX_D158_D158_Id_Dividir (D158_D158_Id_Dividir),
  KEY IDX_D158_Flag_Status (D158_Flag_Status),
  KEY IDX_D158_Id (D158_Id),
  KEY IDX_D158_D009_Id_Comprar (D158_D009_Id_Comprar),
  KEY IDX_D158_D009_Id_Kit (D158_D009_Id_Kit),
  KEY IDX_D158_T219_Id (D158_T219_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D159 (
  D159_Id int(11) NOT NULL AUTO_INCREMENT,
  D159_D157_Id int(11) DEFAULT '0',
  D159_D027_Id int(11) DEFAULT '0',
  D159_Numero_Parcela int(3) DEFAULT '0',
  D159_Prazos_Dias int(5) DEFAULT '0',
  D159_Data_Vencimento date DEFAULT NULL,
  D159_Valor_Parcela decimal(12,2) DEFAULT '0.00',
  D159_Numero_Cheque varchar(50) DEFAULT NULL,
  D159_Banco_Cheque varchar(50) DEFAULT NULL,
  D159_Agencia_Cheque varchar(50) DEFAULT NULL,
  D159_Conta_Corrente_Cheque varchar(50) DEFAULT NULL,
  D159_Nome_Cheque varchar(50) DEFAULT NULL,
  D159_Data_Cheque date DEFAULT NULL,
  D159_Cidade_Cheque varchar(50) DEFAULT NULL,
  D159_Taxa_Juros decimal(12,2) DEFAULT NULL,
  D159_Valor_Juros decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (D159_Id),
  KEY IDX_D159_D157_Id (D159_D157_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela de preços dos clientes
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D160 (
  D160_Id int(10) unsigned auto_increment,
  D160_D009_Id int(10) unsigned,
  D160_D024_Id int(10) unsigned,
  D160_C007_Id int(10) unsigned,
  D160_Data_Hora datetime DEFAULT '0000-00-00 00:00:00',
  D160_Valor_Preco decimal (20,5) default '0',
  PRIMARY KEY (D160_Id),
  KEY IDX_D160_D009_Id (D160_D009_Id),
  KEY IDX_D160_D024_Id (D160_D024_Id),
  KEY IDX_D160_C007_Id (D160_C007_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Cabeçalho da tabela de preços dos clientes
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D161 (
  D161_Id int(10) unsigned auto_increment,
  D161_D024_Id int(10) unsigned,
  PRIMARY KEY (D161_Id),
  KEY IDX_D161_D024_Id (D161_D024_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela de versões do sistema
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T267 (
  T267_Id int(10) unsigned auto_increment,
  T267_Revisao_Final int(20) unsigned,
  T267_Data_Abertura date default '0000-00-00',
  T267_Data_Fechamento date default '0000-00-00',
  PRIMARY KEY (T267_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela de postagem para o portal postal
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T268 (
  T268_Id int(10) unsigned auto_increment,
  T268_C004_Id int(10) unsigned,
  T268_D024_Id int(10) unsigned,
  T268_D148_Id int(10) unsigned,
  T268_T007_Id int(10) unsigned,
  T268_C007_Id_Postagem int(10) unsigned,
  T268_Flag_Postagem varchar(1) DEFAULT 'N',
  T268_Flag_Status varchar(2) DEFAULT '0',
  T268_Centro_Custo int(10) DEFAULT '0',
  T268_Servico varchar(10) DEFAULT '',
  T268_Servico_Adicional_AR varchar(1) DEFAULT 'N',
  T268_Servico_Adicional_VD varchar(1) DEFAULT 'N',
  T268_Servico_Adicional_MP varchar(1) DEFAULT 'N',
  T268_Codigo_Rastreio varchar(20) DEFAULT '',
  T268_Sigla_Triagem varchar(3) DEFAULT '',
  T268_Data_Hora_Postagem datetime DEFAULT '0000-00-00 00:00:00',
  T268_Detalhes_Postagem text,
  T268_Peso decimal(7,2) DEFAULT '0.00',
  T268_Largura decimal(7,2) DEFAULT '0.00',
  T268_Altura decimal(7,2) DEFAULT '0.00',
  T268_Comprimento decimal(7,2) DEFAULT '0.00',
  T268_Valor_Declarado decimal(9,2) DEFAULT '0.00',
  T268_Recebedor varchar(30) DEFAULT '',
  PRIMARY KEY (T268_Id),
  KEY IDX_T268_D024_Id (T268_D024_Id),
  KEY IDX_T268_T007_Id (T268_T007_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Tabela carrinho de compra B2B
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T269 (
  T269_Id int(10) unsigned auto_increment,
  T269_D001_Id int(10) unsigned,
  T269_D024_Id int(10) unsigned,
  T269_C007_Id int(10) unsigned,
  T269_Quantidade decimal(10,5) DEFAULT '0',
  PRIMARY KEY (T269_Id),
  KEY IDX_T269_D001_Id (T269_D001_Id),
  KEY IDX_T269_D024_Id (T269_D024_Id),
  KEY IDX_T269_C007_Id (T269_C007_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


// CRIAÇÃO DE NOVAS TABELAS

echo "Criando tabela T270 - Licença de Importação  ";
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T270 (
  T270_Id int(11) NOT NULL AUTO_INCREMENT,
  T270_T055_Id int(11) NOT NULL,
  T270_T075_Id int(11) NOT NULL,
  T270_Numero_LI varchar(20) DEFAULT NULL, 
  T270_Numero_Processo_Anuente int(1) DEFAULT '0',
  T270_Sigla_Orgao_Anuente varchar(10) DEFAULT NULL,
  T270_Regime_Acordo_Tributario varchar(10) DEFAULT NULL, 
  T270_Fundamento_Legal_Regime varchar(10) DEFAULT NULL, 
  T270_Tipo_Acordo_Tarifario varchar(50) DEFAULT NULL, 
  T270_Codigo_Acordo_Alardi int(2) DEFAULT '0', 
  T270_Numero_Dias_Limite_Pagamento int(2) DEFAULT '0', 
  T270_Codigo_Orgao_Financeiro_Internacional varchar(10) DEFAULT NULL, 
  T270_Codigo_Motivo_Sem_Cobertura int(1) DEFAULT '0', 
  T270_Numero_Destaque_NCM int(3) DEFAULT '999',
  PRIMARY KEY (T270_Id),
  KEY IDX_T270_T075_Id (T270_T075_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

// criação da tabela T271 (Documentos de RMA)
$cClausula  = <<<EOT
create table if not exists T271 (
  T271_Id int(10) unsigned auto_increment,
  T271_T182_Id int(10) unsigned,
  T271_C007_Id int(10) unsigned,
  T271_Nome_Arquivo varchar(200),
  T271_Descricao text,
  T271_Data_Hora_Upload datetime,
  PRIMARY KEY (T271_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Criando Tabela D162 - Status RMA 
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D162(
  D162_Id int(11) NOT NULL AUTO_INCREMENT,
  D162_Status varchar(40) NOT NULL, 
  PRIMARY KEY (D162_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else{
    echo "Tabela D162 criada";
}

Criar_Campo("D162_Tipo_Status", "VARCHAR(100) DEFAULT NULL");


//Criando Tabela D163 - Status OC 
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D163(
  D163_Id int(11) NOT NULL AUTO_INCREMENT,
  D163_Status varchar(40) NOT NULL, 
  PRIMARY KEY (D163_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else{
    echo "Tabela D163 criada";
}

//Criando Tabela D164 - Tipo RMA 
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D164(
  D164_Id int(11) NOT NULL AUTO_INCREMENT,
  D164_Tipo varchar(40) NOT NULL, 
  PRIMARY KEY (D164_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else{
    echo "Tabela D164 criada";
}

//Criando Tabela D165 - Lei da transparência, IBPT
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D165(
  D165_Id int(11) NOT NULL AUTO_INCREMENT,
  D165_D005_Id int(10) unsigned,
  D165_D018_Id int(10) unsigned,
  D165_Percentual_Trib_Federal_Nacional DECIMAL(14,2) DEFAULT '0.00', 
  D165_Percentual_Trib_Federal_Importado DECIMAL(14,2) DEFAULT '0.00', 
  D165_Percentual_Trib_Estadual DECIMAL(14,2) DEFAULT '0.00', 
  D165_Percentual_Trib_Municipal DECIMAL(14,2) DEFAULT '0.00', 
  PRIMARY KEY (D165_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else{
    echo "Tabela D164 criada";
}

// Criando tabela T272 - Pacotes de melhorias
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS T272(
  T272_Id int(10) NOT NULL AUTO_INCREMENT,
  T272_Mes int(2) NOT NULL,
  T272_Ano int(4) NOT NULL,  
  PRIMARY KEY (T272_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Ricardo - CREATE TABLE() - Begin
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T273(
  T273_Id int(10) NOT NULL AUTO_INCREMENT,
  T273_T234_Id int(10) unsigned,
  T273_C007_Id int(10) unsigned,
  T273_Id_Interacao varchar(40),
  T273_Tipo varchar(20),
  T273_Data_Interacao datetime,
  T273_Descricao varchar(150),
  T273_Texto Text,  
  T273_Link_Anexo varchar(100),
  T273_D167_Antes int(10) unsigned,
  T273_D167_Depois int(10) unsigned,
  PRIMARY KEY (T273_Id),
  KEY `T273_T234_Id` (`T273_T234_Id`),
  KEY `T273_C007_Id` (`T273_C007_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T274(
  T274_Id int(10) NOT NULL AUTO_INCREMENT,
  T274_T234_Id int(10) unsigned,
  T274_Data date default '0000-00-00',
  T274_Hora_Inicio time DEFAULT '00:00:00',
  T274_Hora_Final time DEFAULT '00:00:00',
  T274_Total_Horas_trabalhadas time DEFAULT '00:00:00',
  PRIMARY KEY (T274_Id),
  KEY `T274_T234_Id` (`T274_T234_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T275(
  T275_Id int(10) NOT NULL AUTO_INCREMENT,
  T275_Data_Emissao date,
  T275_C007_Id int(10) unsigned,
  PRIMARY KEY (T275_Id),
  KEY `T275_C007_Id` (`T275_C007_Id`)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Cadastro de Veículos (D170)
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS D170(
  D170_Id int(10) NOT NULL AUTO_INCREMENT,
  D170_Identificacao_Veiculo varchar(100),
  D170_Placa varchar(7),
  D170_Renavam varchar(11),
  D170_Tara int(6),
  D170_Capacidade_Kg int(6),
  D170_Capacidade_M3 int(3),
  D170_Flag_Rodado int(3),
  D170_Flag_Carroceria int(3),
  D170_D018_Id int(10) unsigned,
  D170_Categoria int(1),
  D170_D109_Id int(10) unsigned,
  D170_Flag_Ativo enum('S','N') default'S',
  PRIMARY KEY (D170_Id),
  KEY IDX_D170_D109_Id (D170_D109_Id),
  KEY IDX_D170_D018_Id (D170_D018_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Cadastro Lacres (D171)
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS D171(
  D171_Id int(10) NOT NULL AUTO_INCREMENT,
  D171_Descrição varchar(100),
  D171_Numero_Lacre varchar(20),
  D171_Tipo int(1),
  D171_Flag_Ativo enum('S','N') default'S',
  PRIMARY KEY (D171_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Cadastro Seguradoras (D172)
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS D172(
  D172_Id int(10) NOT NULL AUTO_INCREMENT,
  D172_Nome varchar(60),
  D172_CNPJ varchar(20),
  D172_Informacoes_Adicionais Text,
  D172_DDD_Telefone int(2) unsigned,
  D172_Telefone varchar(20),
  D172_D020_Id int(10) unsigned,
  D172_D018_Id int(10) unsigned,
  D172_Flag_Ativo enum('S','N') default'S',
  PRIMARY KEY (D172_Id),
  KEY IDX_D172_D020_Id (D172_D020_Id),
  KEY IDX_D172_D018_Id (D172_D018_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Cadastro de Apólices
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS D173(
  D173_Id int(10) NOT NULL AUTO_INCREMENT,
  D173_Identificacao varchar(100),
  D173_Data_Inicio date default '0000-00-00',
  D173_Data_Validade date default '0000-00-00',
  D173_Ramo varchar(10),
  D173_Numero_Apolice varchar(30),
  D173_Flag_Ativo enum('S','N') default'S',
  D173_D172_Id int(10),
  PRIMARY KEY (D173_Id),
  KEY IDX_D173_D172_Id (D173_D172_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Cadastro de Averbação
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS D174(
  D174_Id int(10) NOT NULL AUTO_INCREMENT,
  D174_Numero_Averbacao varchar(40),
  D174_D173_Id int(10),
  PRIMARY KEY (D174_Id),
  KEY IDX_D174_D173_Id (D174_D173_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Cabeçalho MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T276(
  T276_Id int(10) NOT NULL AUTO_INCREMENT,
  T276_Ambiente_MDFe enum('1','2') default '1',
  T276_Serie int(5) default '1',
  T276_Numero_MDFe varchar(12),
  T276_Chave_Acesso_MDFe varchar(44),
  T276_Flag_Modalidade_Transporte int(3),
  T276_Data_Emissao date,
  T276_Hora_Emissao time,
  T276_Flag_Tipo_Emissao int(2) default '1',
  T276_UF_Carregamento varchar(2),
  T276_UF_Descarregamento varchar(2),
  T276_Data_Inicio_Viagem date,
  T276_Hora_Inicio_Viagem time,
  T276_Flag_Cancelada char(1) default 'N',
  T276_Flag_Encerrada char(1),
  T276_Data_Hora_Encerramento_MDFe datetime,
  T276_Data_Hora_Protocolo_MDFe datetime,
  T276_Numero_Protocolo_MDFe varchar(20),
  T276_Cidade_Carregamento varchar(30),
  T276_C004_Id int(11),
  T276_Flag_Emitente char(1),
  T276_UF_Empresa_Emitente varchar(2),
  T276_Emitente_CNPJ varchar(20),
  T276_Emitente_Inscricao_Estadual varchar(18),
  T276_Emitente_Nome varchar(60),
  T276_Emitente_Nome_Fantasia varchar(60),
  T276_Emitente_Rua varchar(60),
  T276_Emitente_Numero varchar(60),
  T276_Emitente_Cidade_IBGE int(7),
  T276_Emitente_Cidade varchar(60),
  T276_Emitente_CEP int(8),
  T276_Emitente_UF varchar(2),
  T276_Quantidade_NFe int(6),
  T276_Valor_Carga decimal(13,2),
  T276_Peso_Bruto_Total_Carga decimal(11,4),
  T276_Informacoes_Adicionais_Fisco text,
  T276_Informacoes_Adicionais_Contribuinte text,
  T276_Arquivo_XML Text,
  T276_Flag_Contingencia enum('S','N') default 'N',
  T276_Emitente_Bairro varchar(60),
  T276_D109_Id int(10),
  PRIMARY KEY (T276_Id),
  KEY IDX_T276_D109_Id (T276_D109_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Percurso/ Carregamento
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T277(
  T277_Id int(10) NOT NULL AUTO_INCREMENT,
  T277_T276_Id int(10) unsigned,
  T277_D018_Id int(10) unsigned, 
  T277_D020_Id int(10) unsigned,
  PRIMARY KEY (T277_Id),
  KEY IDX_T277_T276_Id (T277_T276_Id),
  KEY IDX_T277_D018_Id (T277_D018_Id),
  KEY IDX_T277_D020_Id (T277_D020_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Condutores MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T278(
  T278_Id int(10) NOT NULL AUTO_INCREMENT,
  T278_D109_Id int(10),
  T278_T276_Id int(10),
  PRIMARY KEY (T278_Id),
  KEY IDX_T278_D109_Id (T278_D109_Id),
  KEY IDX_T278_T276_Id (T278_T276_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Seguradora MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T279(
  T279_Id int(10) NOT NULL AUTO_INCREMENT,
  T279_T276_Id int(10),
  T279_D172_Id int(10),
  PRIMARY KEY (T279_Id),
  KEY IDX_T279_T276_Id (T279_T276_Id),
  KEY IDX_T279_D172_Id (T279_D172_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Notas MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T284(
  T284_Id int(10) NOT NULL AUTO_INCREMENT,
  T284_Chave_De_Acesso_NFe varchar(44),
  T284_Peso_Bruto_Total decimal(11,4) ,
  T284_Valor_Carga decimal(13,4),
  T284_Numero_NFe varchar(12),
  T284_Cidade_Descarregamento varchar(60),
  T284_Cidade_Descarregamento_Codigo_IBGE int(7),
  T284_Flag_Tipo_Documento int(1) unsigned default 2,
  T284_T276_Id int(10),
  T284_T007_Id int(10),
  T284_D020_Id int(10),
  T284_D018_Id int(10),
  PRIMARY KEY (T284_Id),
  KEY IDX_T284_T276_Id (T284_T276_Id),
  KEY IDX_T284_T007_Id (T284_T007_Id),
  KEY IDX_T284_D020_Id (T284_D020_Id),
  KEY IDX_T284_D018_Id (T284_D018_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Tipo Unidade Transporte MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T281(
  T281_Id int(10) NOT NULL AUTO_INCREMENT,
  T281_Flag_Unidade_Transporte int(1),
  T281_Identificacao_Unidade_Transporte varchar(20),
  T281_T284_Id int(10),
  T281_Quantidade_Rateada decimal(3,2),
  PRIMARY KEY (T281_Id),
  KEY IDX_T281_T284_Id (T281_T284_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Tipo Unidade Carga MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T282(
  T282_Id int(10) NOT NULL AUTO_INCREMENT,
  T282_Flag_Unidade_Carga int(1),
  T282_Identificacao_Unidade_Carga varchar(20),
  T282_Quantidade_Rateada decimal(3,2),
  T282_T281_Id int(10) unsigned,
  PRIMARY KEY (T282_Id),
  KEY IDX_T282_T281_Id (T282_T281_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Modal Rodoviário
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T283(
  T283_Id int(10) NOT NULL AUTO_INCREMENT,
  T283_Placa varchar(7),
  T283_Renavam varchar(11),
  T283_Tara int(6),
  T283_Capacidade_Kg int(6),
  T283_Capacidade_M3 int(3),
  T283_Flag_Rodado int(3),
  T283_Flag_Carroceria int(3),
  T283_UF_Licenciamento char(2),
  T283_D170_Id int(10),
  PRIMARY KEY (T283_Id),
  KEY IDX_T283_D170_Id (T283_D170_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Lacre modal rodoviário
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T286(
  T286_Id int(10) NOT NULL AUTO_INCREMENT,
  T286_T276_Id int(10),
  T286_D171_Id int(10),
  PRIMARY KEY (T286_Id),
  KEY IDX_T286_D171_Id (T286_D171_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Lacre Transporte
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T287(
  T287_Id int(10) NOT NULL AUTO_INCREMENT,
  T287_T281_Id int(10),
  T287_D171_Id int(10),
  PRIMARY KEY (T287_Id),
  KEY IDX_T287_D171_Id (T287_D171_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Lacre Carga
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T288(
  T288_Id int(10) NOT NULL AUTO_INCREMENT,
  T288_T282_Id int(10),
  T288_D171_Id int(10),
  PRIMARY KEY (T288_Id),
  KEY IDX_T288_D171_Id (T288_D171_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Tipo Lacre
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T280(
  T280_Id int(10) NOT NULL AUTO_INCREMENT,
  T280_D171_Id int(10),
  T280_T276_Id int(10),
  PRIMARY KEY (T280_Id),
  KEY IDX_T280_D171_Id (T280_D171_Id),
  KEY IDX_T280_T276_Id (T280_T276_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Log MDF-e
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T285(
  T285_Id int(10) NOT NULL AUTO_INCREMENT,
  T285_Chave_Acesso_MDFE varchar(44),
  T285_Codigo_Retorno varchar(3),
  T285_Numero_Recibo_MDFe varchar(20),
  T285_Numero_MDFE varchar(20),
  T285_Data_Transmicao datetime,
  T285_Retorno_Sefaz text,
  T285_Xml_Retorno text,
  T285_Modelo varchar(2),
  T285_C004_Id int(10) unsigned,
  T285_T284_Id int(10) unsigned,
  PRIMARY KEY (T285_Id),
  KEY IDX_T285_T284_Id (T285_T284_Id),
  KEY IDX_T285_C004_Id (T285_C004_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}
//Ricardo - CREATE TABLE() - End

//Tiago - CREATE TABLE()
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS C034(
  C034_Id int(10) NOT NULL AUTO_INCREMENT,
  C034_Grid_Id varchar(50) NOT NULL,
  C034_C007_Id int(10) NOT NULL,
  C034_Hash varchar(50) NOT NULL,
  C034_Data datetime NOT NULL,
  C034_Filtro text NOT NULL,
  C034_Filtro_Data text NOT NULL,
  C034_Count int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (C034_Id),
  KEY IDX_C034_C007_Id (C034_C007_Id),
  KEY IDX_C034_Data (C034_Data),
  KEY IDX_Busca (C034_Grid_Id,C034_C007_Id,C034_Hash)
)
ENGINE=InnoDB
DEFAULT
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
//Tiago - CREATE TABLE() - End

//Marcelo - CREATE TABLE()
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS C035(
  C035_Id int(10) NOT NULL AUTO_INCREMENT,
  C035_Tabela varchar(10) NOT NULL,
  C035_Tabela_Id int(10) NOT NULL,
  C035_C007_Id int(10) NOT NULL,
  C035_Data_Autorizacao datetime NOT NULL,
  C035_Descricao varchar(100) NOT NULL,
  C035_Valor decimal(20,10) NOT NULL,
  PRIMARY KEY (C035_Id),
  KEY IDX_C035_Tabela (C035_Tabela),
  KEY IDX_C035_Tabela_Id (C035_Tabela_Id),
  KEY IDX_C035_Data_Autorizacao (C035_Data_Autorizacao),
  KEY IDX_C035_C007_Id (C035_C007_Id)
)
ENGINE=InnoDB
DEFAULT
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS `t132` (                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
  `T132_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
  `T132_T007_Id` int(10) unsigned DEFAULT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
  `T132_Tipo` enum('NF','CTe','NFe','CUPOM','NF_PRODUTOR') DEFAULT 'NFe',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
  `T132_Chave_Acesso` varchar(44) DEFAULT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
  `T132_UF_IBGE` int(2) unsigned zerofill DEFAULT '00',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
  `T132_Ano_Mes` int(4) unsigned zerofill DEFAULT '0000',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
  `T132_Pessoa_Fisica_Juridica` enum('F','J') DEFAULT 'J',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
  `T132_Cnpj` varchar(14) DEFAULT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
  `T132_Cpf` varchar(11) DEFAULT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
  `T132_Inscricao_Estadual` varchar(14) DEFAULT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
  `T132_Modelo` int(2) unsigned zerofill DEFAULT '01',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
  `T132_Serie` int(3) DEFAULT '0',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
  `T132_Numero_Nota_Fiscal` int(9) DEFAULT '0',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
  `T132_Cupom_Numero_EFC` int(3) DEFAULT '0',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
  `T132_Cupom_Numero_COO` int(6) DEFAULT '0',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
  `T132_Cupom_Modelo` char(2) DEFAULT NULL,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
  PRIMARY KEY (`T132_Id`),                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
  KEY `T132_T007_Id` (`T132_T007_Id`)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
) ENGINE=InnoDB DEFAULT CHARSET=utf8  
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}

//Marcelo - CREATE TABLE() - End

//Osni - Popula T289
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T289(
  T289_Id INT(10) NOT NULL AUTO_INCREMENT,
  T289_D009_Id VARCHAR(10),
  T289_T014_Id VARCHAR(10),
  T289_Quantidade DECIMAL(10,3),
  T289_Quantidade_Saldo DECIMAL(10,3),
  T289_Flag_Operacao VARCHAR(1),
  PRIMARY KEY (T289_Id),
  UNIQUE KEY I_T014_Id (T289_T014_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Osni - Popula T290
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T290(
  T290_Id INT(10) NOT NULL AUTO_INCREMENT,
  T290_D009_Id VARCHAR(10),
  T290_T008_Id VARCHAR(10),
  T290_T289_Id VARCHAR(10),
  T290_Quantidade DECIMAL(10,3),
  T290_Flag_Operacao VARCHAR(1),
  PRIMARY KEY (T290_Id),
  UNIQUE KEY I_T008_Id (T290_T008_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Osni - Popula T290A

// criação da tabela T290A
$cClausula  = <<<EOT
create table if not exists T290A (
    T290A_Id                              int(10) unsigned auto_increment,
    T290A_T290_Id                         int(10),
    T290A_Observacao                      varchar(200) DEFAULT '',   
    PRIMARY KEY (T290A_Id),
    UNIQUE KEY IDX_T290A_T290_Id (T290A_T290_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Raquel - 21/02/2019
// Tabela Criada para fazer o vínculo entre o usuário e o representante
$cClausula = <<<EOT

CREATE TABLE IF NOT EXISTS T291(
  T291_Id int(10) NOT NULL AUTO_INCREMENT,
  T291_C007_Id int(10) unsigned,
  T291_D021_Id int(10) unsigned, 
  PRIMARY KEY (T291_Id),
  KEY IDX_T291_C007_Id (T291_C007_Id),
  KEY IDX_T291_D021_Id (T291_D021_Id)

)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela de impressora
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS D175(
  D175_Id int(10) unsigned auto_increment,
  D175_Nome varchar(60),
  D175_Observacao text,
  PRIMARY KEY (D175_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela dos relatorios BI
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS D176(
  D176_Id int(10) unsigned auto_increment,
  D176_Titulo varchar(60),
  D176_Link varchar(250),
  D176_Descricao text,
  PRIMARY KEY (D176_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela de relação do relatorio do BI com os perfis de acesso
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS D177(
  D177_Id int(10) unsigned auto_increment,
  D177_D176_Id int(10) unsigned,
  D177_C021_Id int(10) unsigned,
  PRIMARY KEY (D177_Id),
  KEY IDX_D177_D176_Id (D177_D176_Id),
  KEY IDX_D177_C021_Id (D177_C021_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Criação da tabela de acessos e permissões da API
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS D178(
  D178_Id int(10) unsigned auto_increment,
  D178_C004_Id int(10) unsigned,
  D178_Key varchar(60),
  D178_Token varchar(60),
  D178_Nome_Usuario varchar(100),
  D178_Descricao_Usuario text,
  D178_Flag_Ativo VARCHAR(1) default 'S',
  D178_Permissao_Produto VARCHAR(1) default 'N',
  D178_Permissao_Estoque_Quantidade VARCHAR(1) default 'N',
  D178_Permissao_Estoque_Disponibilidade VARCHAR(1) default 'N',
  PRIMARY KEY (D178_Id),
  KEY IDX_D178_C004_Id (D178_C004_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
  CREATE TABLE IF NOT EXISTS D179 (
    D179_Id int(10) NOT NULL AUTO_INCREMENT,
    D179_Login varchar(60),
    D179_Senha varchar(60),
    PRIMARY KEY (D179_Id),
    UNIQUE INDEX IDX_D179_Login (D179_Login)
  )
  ENGINE=InnoDB
  DEFAULT
  CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)) {
    echo "$cClausula<hr>$cErro";
}else{
  echo "\nTabela D179 criada com sucesso.";
}

$cClausula = <<<EOT
  CREATE TABLE IF NOT EXISTS D180 (
    D180_Id int(10) NOT NULL AUTO_INCREMENT,
    D180_D179_Id int(10) unsigned,
    D180_Token varchar(60),
    PRIMARY KEY (D180_Id),
    KEY IDX_D180_D179_Id (D180_D179_Id),
    UNIQUE INDEX IDX_D180_Token (D180_Token)
  )
  ENGINE=InnoDB
  DEFAULT
  CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)) {
    echo "$cClausula<hr>$cErro";
} else {
    echo "\nTabela D180 criada com sucesso.";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D181(
  D181_Id int(10) NOT NULL AUTO_INCREMENT,
  D181_Titulo varchar(60) default "",
  D181_Subtitulo varchar(60) default "",
  D181_Texto text,
  D181_Data_Criacao date default "0000-00-00",
  D181_Data_Ultimo_Envio date default "0000-00-00",
  PRIMARY KEY (D181_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}else {
    echo "\nTabela D181 criada com sucesso.";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D182(
  D182_Id int(10) NOT NULL AUTO_INCREMENT,
  D182_D132_Id int(10) unsigned,
  D182_C007_Id int(10) unsigned,
  D182_Grid_Salvo longblob,
  PRIMARY KEY (D182_Id),
  UNIQUE INDEX IDX_D182_Grid (D182_D132_Id, D182_C007_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else {
    echo "\nTabela D182 criada com sucesso.";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS D183(
    D183_Id                int auto_increment primary key,
    D183_Nome              varchar(100)                       null,
    D183_Codigo            varchar(200)                       not null,
    D183_Codigo_Parametros varchar(255)                       null,
    D183_Codigo_Sql        text                               null,
    D183_Crontab           varchar(100)                       not null,
    D183_Crontab_Extra     varchar(100)                       null,
    D183_Flag_Execucao     char     default 'N'               null,
    D183_Ultima_Execucao   datetime                           null,
    D183_Criacao           datetime default CURRENT_TIMESTAMP null,
    D183_Uuid              varchar(255)                       null
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else {
    echo "\nTabela D183 criada com sucesso.";
}

$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS C039(
    C039_Id                int auto_increment primary key,
    C039_Nome              varchar(100)                       null,
    C039_Codigo            varchar(200)                       not null,
    C039_Codigo_Parametros varchar(255)                       null,
    C039_Codigo_Sql        text                               null,
    C039_Crontab           varchar(100)                       not null,
    C039_Crontab_Extra     varchar(100)                       null,
    C039_Flag_Execucao     char     default 'N'               null,
    C039_Ultima_Execucao   datetime                           null,
    C039_Criacao           datetime default CURRENT_TIMESTAMP null,
    C039_Uuid              varchar(255)                       null
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}else {
    echo "\nTabela C039 criada com sucesso.";
}

// criação da tabela Z001 - Lojas Tray
$cClausula  = <<<EOT
CREATE TABLE IF NOT EXISTS `Z001` (
  `Z001_Id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Z001_Db` VARCHAR(10) NOT NULL,
  `Z001_Store` VARCHAR(10) NOT NULL,
  `Z001_Code` VARCHAR(100) NOT NULL,
  `Z001_Access_Token` VARCHAR(100) DEFAULT NULL,
  `Z001_Refresh_Token` VARCHAR(100) DEFAULT NULL,
  `Z001_Access_Token_Expiration` DATETIME DEFAULT '0000-00-00 00:00:00',
  `Z001_Refresh_Token_Expiration` DATETIME DEFAULT '0000-00-00 00:00:00',
  `Z001_Export_URL` VARCHAR(100) DEFAULT NULL,
  `Z001_API_Address` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`Z001_Id`)
) ENGINE=INNODB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
}

// criação da tabela C033 - API
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS C033(
  C033_Id int(10) NOT NULL AUTO_INCREMENT,
  C033_Login varchar(50) default "",
  C033_Senha varchar(50) default "",
  C033_Url varchar(200) default "",
  C033_Metodo varchar(20) default "",
  PRIMARY KEY (C033_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T292 - Forma de pagamento PDV
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS T292(
  T292_Id int(10) NOT NULL AUTO_INCREMENT,
  T292_T007_Id int(10) not null,
  T292_D027_Id int(10) not null,
  T292_Valor_Recebido decimal(10,2) not null,
  T292_Quantidade_Parcelas int(5) not null,
  PRIMARY KEY (T292_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula  = <<<EOT
create table if not exists T138 (
    T138_Id                    int(10) unsigned NOT NULL auto_increment,  
    T138_T007_Id               int(10) unsigned,                    
    T138_C004_Id               int(10) unsigned,                   
    T138_D009_Id               int(10) unsigned,                     
    T138_Quantidade            decimal(12,5) default 0.00000,                       
    T138_Valor_Preco_Unitario  decimal(20,5) default 0.00000,                       
    T138_Flag_Documento        char(1) default 0,                                  
    T138_Numero_Nota_Fiscal    varchar(12),                          
    T138_Observacao            varchar(200),                         
    T138_T003_Id               int(10) unsigned,                     
    T138_T005_Id               int(10) unsigned,                    
    T138_C007_Id_Inspecao      int(10) unsigned,                     
    T138_Operacao              char(1),                              
    T138_Data_Inspecao         datetime,                             
    T138_Flag_Dividir          char(1) default 'N',                                               
    T138_Flag_Comissao         char(1) default 'N',                                           
    T138_Quantidade_Separado   decimal(10,2),  
    PRIMARY KEY (T138_Id)     
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula  = <<<EOT
create table if not exists T118 (
    T118_Id                     int(11) NOT NULL auto_increment,
    T118_T007_Id                int(10) unsigned default 0,                        
    T118_D027_Id                int(10) unsigned default 0,                        
    T118_D031_Id                int(11) unsigned,                     
    T118_Numero_Duplicata       varchar(15),                          
    T118_Numero_Parcela         int(3) default 0,                        
    T118_Prazos_Dias            int(5) default 0,                        
    T118_Data_Vencimento        date,                    
    T118_Valor_Parcela          decimal(12,2) default 0.00,                     
    T118_Numero_Cheque          varchar(50),                          
    T118_Banco_Cheque           varchar(50),                          
    T118_Agencia_Cheque         varchar(50),                          
    T118_Conta_Corrente_Cheque  varchar(50),                          
    T118_Nome_Cheque            varchar(50),                          
    T118_Data_Cheque            date,                                 
    T118_Cidade_Cheque          varchar(50),                          
    T118_Taxa_Juros             decimal(12,2),                        
    T118_Valor_Juros            decimal(12,2),      
    PRIMARY KEY (T118_Id)     
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula  = <<<EOT
create table if not exists T219A (
    T219A_Id                   int(10) unsigned NOT NULL auto_increment,
    T219A_T219_Id              int(10) unsigned NOT NULL,
    T219A_Percentual_Reducao_ICMS_IE_ST decimal(10,2),
    T219A_Percentual_Reducao_ICMS_Int_ST decimal(10,2),
    PRIMARY KEY (T219A_Id)
) 
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula  = <<<EOT
create table if not exists T225A (
    T225A_Id                   int(10) unsigned NOT NULL auto_increment,
    T225A_T225_Id              int(10) unsigned NOT NULL,
    T225A_Percentual_Reducao_ICMS_IE_ST decimal(10,2),
    T225A_Percentual_Reducao_ICMS_Int_ST decimal(10,2),
    PRIMARY KEY (T225A_Id)
) 
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T008A
$cClausula  = <<<EOT
create table if not exists T008A (
    T008A_Id                              int(10) unsigned auto_increment,
    T008A_T008_Id                         int(10),
    T008A_Codigo_Produto_ANP              varchar(12) DEFAULT NULL,
    T008A_Descricao_Produto_ANP           varchar (100) DEFAULT NULL,
    T008A_Percentual_GLP                  decimal (7,4) DEFAULT '0.0000',
    T008A_Percentual_GNN                  decimal (7,4) DEFAULT '0.0000',
    T008A_Percentual_GNI                  decimal (7,4) DEFAULT '0.0000',
    T008A_Valor_Partida                   decimal (15,2) DEFAULT '0.00',
    T008A_Codigo_Autorizacao_Codif        varchar (25) DEFAULT NULL,
    T008A_Quantidade_Temperatura_Ambiente decimal (16,4) DEFAULT '0.0000',    
    PRIMARY KEY (T008A_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T005A
$cClausula  = <<<EOT
create table if not exists T005A (
    T005A_Id                              int(10) unsigned auto_increment,
    T005A_T005_Id                         int(10),
    T005A_Flag_Tipo                       varchar(1) DEFAULT '1',   
    PRIMARY KEY (T005A_Id),
    KEY IDX_T005A_T005_Id (T005A_T005_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T004A
$cClausula  = <<<EOT
create table if not exists T004A (
    T004A_Id                              int(10) unsigned auto_increment,
    T004A_T004_Id                         int(10),
    T004A_Flag_Solicitar_Prazo_Compras    varchar(1) DEFAULT 'N',   
    PRIMARY KEY (T004A_Id),
    KEY IDX_T004A_T004_Id (T004A_T004_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T006A
$cClausula  = <<<EOT
create table if not exists T006A (
    T006A_Id                              int(10) unsigned auto_increment,
    T006A_T006_Id                         int(10),
    T006A_Percentual_Reducao_PIS          decimal(5,2) DEFAULT '0.00',
    T006A_Percentual_Reducao_COFINS       decimal(5,2) DEFAULT '0.00',   
    PRIMARY KEY (T006A_Id),
    UNIQUE KEY IDX_T006A_T006_Id (T006A_T006_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T293
$cClausula  = <<<EOT
create table if not exists T293 (
    T293_Id                                     int unsigned  auto_increment,
    T293_D018_Id                                int unsigned DEFAULT NULL,
    T293_T054_Id                                int NOT NULL DEFAULT 0,
    T293_Data_Emissao_DI                        date DEFAULT NULL,
    T293_Numero_DI                              varchar(30) DEFAULT NULL,
    T293_Valor_Produtos                         decimal(14,2) DEFAULT 0.00,
    T293_Valor_Frete                            decimal(14,4) DEFAULT 0.0000,
    T293_Valor_CIF                              decimal(14,2) DEFAULT 0.00  ,
    T293_Valor_THC                              decimal(14,4) DEFAULT 0.0000,
    T293_Valor_SISCOMEX                         decimal(14,4) DEFAULT 0.0000,
    T293_Valor_Imposto_Importacao               decimal(14,4) DEFAULT 0.0000,
    T293_Valor_IPI                              decimal(14,4) DEFAULT 0.0000,
    T293_Valor_PIS                              decimal(14,4) DEFAULT 0.0000,
    T293_Valor_COFINS                           decimal(14,4) DEFAULT 0.0000,
    T293_Valor_DI                               decimal(14,2) DEFAULT 0.00,
    T293_Local_Desembaraco                      varchar(60) DEFAULT NULL,
    T293_Data_Desembaraco                       date DEFAULT NULL,
    T293_Codigo_Exportador                      varchar(60) DEFAULT NULL,
    T293_D024_Id                                int unsigned DEFAULT NULL,
    T293_D118_Id                                int unsigned DEFAULT NULL,
    T293_D119_Id                                int unsigned DEFAULT NULL,
    T293_Cotacao_ME                             decimal(12,4)DEFAULT NULL,
    T293_Valor_Frete_Incluso                    decimal(14,4) DEFAULT 0.0000,
    T293_Valor_ICMS                             decimal(14,4) DEFAULT 0.0000,
    T293_Valor_Seguro                           decimal(14,4) DEFAULT 0.0000,
    T293_Valor_Despesa                          decimal(14,4) DEFAULT 0.0000,
    T293_Observacao_DI                          text DEFAULT NULL,
    T293_Observacao_NF                          text DEFAULT NULL,
    T293_C004_Id                                int unsigned DEFAULT NULL,
    T293_Data_Embarque                          date DEFAULT NULL,
    T293_ETA_Data                               date DEFAULT NULL,
    T293_ETA_Hora                               time DEFAULT NULL,
    T293_D044_Id                                int unsigned DEFAULT NULL,
    T293_Aliquota_ICMS                          decimal(5,2) DEFAULT NULL,
    T293_Aliquota_ICMS_Presumido                decimal(5,2) DEFAULT NULL,
    T293_Percentual_Reducao_ICMS                decimal(5,2) DEFAULT NULL,
    T293_Percentual_Reducao_ICMS_Presumido      decimal(5,2) DEFAULT NULL,
    T293_Valor_VMLE                             decimal(15,5) DEFAULT NULL,
    T293_Valor_VMLD                             decimal(15,5) DEFAULT NULL,
    T293_Aliquota_ICMS_PIS_COFINS               decimal(5,2) DEFAULT NULL,
    T293_D006_Id                                int DEFAULT NULL,
    T293_Aliquota_ICMS_Credito_Presumido        decimal(5,2) DEFAULT NULL,
    T293_Peso_Liquido                           decimal(10,4) DEFAULT 0.0000,
    T293_Peso_Bruto                             decimal(10,4) DEFAULT 0.0000,
    T293_C007_Id                                int unsigned DEFAULT NULL,
    T293_Ordem_Compra                           varchar(30)  DEFAULT NULL,
    T293_Data_Previsao                          date DEFAULT NULL,
    T293_Data_Estoque                           date DEFAULT NULL,
    T293_Valor_AFRMM                            decimal(14,4) DEFAULT 0.0000,
    T293_Data_Atualizacao_Custo                 datetime DEFAULT NULL,
    T293_Flag_MP                                int DEFAULT 0,
    T293_Data_Licenca_Importacao                date DEFAULT NULL,
    T293_Calculo_THC                            varchar(1) DEFAULT 1,
    T293_Somar_PIS_Custo                        char(1) DEFAULT 'N',
    T293_Somar_COFINS_Custo                     char(1) DEFAULT 'N',
    T293_Somar_Seguro_Custo                     char(1) DEFAULT 'N',
    T293_Somar_AFRMM_Custo                      char(1) DEFAULT 'N',
    T293_Somar_ICMS_Pagar_Custo                 char(1) DEFAULT 'N',
    T293_Somar_IPI_Custo                        char(1) DEFAULT 'N',
    T293_UF_Desembaraco                         varchar(2) DEFAULT NULL,
    T293_Codigo_Fabricante                      varchar(60) DEFAULT NULL,
    T293_Flag_Transporte_DI                     char(10) DEFAULT NULL,
    T293_Flag_Tipo_Intermedio_DI                varchar(50) DEFAULT NULL,
    T293_CNPJ_Importador                        varchar(50) DEFAULT NULL,
    T293_UF_Importador                          char(10) DEFAULT NULL,
    T293_Numero_Drawback_Importacao             varchar(50) DEFAULT NULL,
    T293_Cotacao_CC                             decimal(12,4) DEFAULT 0.0000,
    T293_Codigo_URF_Entrada                     int DEFAULT NULL,
    T293_Codigo_URF_Despacho                    int DEFAULT NULL,
    T293_Codigo_Moeda                           int DEFAULT NULL,
    T293_Codigo_Incoterms                       varchar(4) DEFAULT NULL,
    T293_Condicao_Mercadoria                    varchar(20) DEFAULT NULL,
    T293_Enquadramento_Material_Usado           varchar(25) DEFAULT NULL,
    T293_Operacao_Enquadramento_Material_Usado  varchar(30) DEFAULT NULL,
    T293_Data_Inicio_Demurrage                  date DEFAULT NULL,
    T293_Siscoserv_RAS                          text DEFAULT NULL,
    T293_Siscoserv_RP                           text DEFAULT NULL,
    T293_Orgao_Desembaraco                      varchar(100),
    PRIMARY KEY (T293_Id),
    KEY IDX_T293_D018_Id (T293_D018_Id),
    KEY IDX_T293_T054_Id (T293_T054_Id),
    KEY IDX_T293_D024_Id (T293_D024_Id),
    KEY IDX_T293_D118_Id (T293_D118_Id),
    KEY IDX_T293_D119_Id (T293_D119_Id),
    KEY IDX_T293_C004_Id (T293_C004_Id),
    KEY IDX_T293_D044_Id (T293_D044_Id),
    KEY IDX_T293_D006_Id (T293_D006_Id),
    KEY IDX_T293_C007_Id (T293_C007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T294
$cClausula  = <<<EOT
create table if not exists T294 (
    T294_Id                                          int unsigned auto_increment,
    T294_T060_Id                                     int unsigned DEFAULT NULL,
    T294_T054_Id                                     int unsigned DEFAULT NULL,
    T294_D037_Id                                     int unsigned DEFAULT NULL,
    T294_D005_Id                                     int unsigned DEFAULT NULL,
    T294_D009_Id                                     int unsigned DEFAULT NULL,
    T294_T053_Id                                     int unsigned DEFAULT NULL,
    T294_C004_Id                                     int unsigned DEFAULT 0,
    T294_Codigo_Produto                              varchar(30) DEFAULT NULL,
    T294_Codigo_Produto_Fornecedor                   varchar(20) DEFAULT NULL,
    T294_Descricao_Produto                           varchar(120) DEFAULT NULL,
    T294_Classificacao_Fiscal                        varchar(12) DEFAULT NULL,
    T294_Prazo_Entrega                               int DEFAULT 0,
    T294_Quantidade                                  int DEFAULT 0,
    T294_Valor_Custo_Unitario_Anterior               decimal(19,5) DEFAULT 0.00000,
    T294_Valor_Custo_Unitario                        decimal(20,7) DEFAULT NULL,
    T294_Valor_Total_Custo                           decimal(19,2) DEFAULT 0.00,
    T294_Valor_Preco_Unitario_Nota_Fiscal            decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Preco_Nota_Fiscal               decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Despesas_Acessorias_Nota_Fiscal decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Custo_Unitario_Original               decimal(19,5) DEFAULT 0.00000,
    T294_Valor_Total_Custo_Original                  decimal(19,2) DEFAULT 0.00,
    T294_Valor_Base_IPI                              decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_ICMS_Sem_Reducao                 decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_ICMS_Normal                      decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_ICMS_Presumido                   decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_COFINS                           decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_PIS                              decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_Extra                            decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Base_Imposto_Importacao               decimal(19,6) DEFAULT 0.000000,
    T294_Aliquota_IPI                                decimal(5,2) DEFAULT 0.00,
    T294_Aliquota_Imposto_Importacao                 decimal(5,2) DEFAULT 0.00,
    T294_Aliquota_COFINS                             decimal(5,2) DEFAULT 0.00,
    T294_Aliquota_PIS                                decimal(5,2) DEFAULT 0.00,
    T294_Aliquota_Extra                              decimal(5,2) DEFAULT 0.00,
    T294_Valor_ICMS                                  decimal(19,6) DEFAULT 0.000000,
    T294_Valor_ICMS_Presumido                        decimal(19,6) DEFAULT 0.000000,
    T294_Valor_ICMS_Pagar                            decimal(19,6) DEFAULT 0.000000,
    T294_Valor_IPI                                   decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Imposto_Importacao                    decimal(19,6) DEFAULT 0.000000,
    T294_Valor_COFINS                                decimal(19,6) DEFAULT 0.000000,
    T294_Valor_PIS                                   decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Extra                                 decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Frete                                 decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Despesas                              decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Despesas_Fatura                       decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Aduaneira                             decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Despesas_Aduaneira                    decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Custo_Final_Unitario                  decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Custo_Final                     decimal(19,2) DEFAULT 0.00,
    T294_Peso_Kg_Liquido_Unitario                    decimal(19,6) DEFAULT 0.000000,
    T294_Peso_Kg_Liquido_Total                       decimal(19,6) DEFAULT 0.000000,
    T294_Peso_Lb_Liquido_Unitario                    decimal(19,3) DEFAULT 0.000,
    T294_Peso_Lb_Liquido_Total                       decimal(19,3) DEFAULT 0.000,
    T294_Multiplicador_X                             decimal(19,6) DEFAULT 0.000000,
    T294_Multiplicador_Y                             decimal(19,6) DEFAULT 0.000000,
    T294_Data_Transferencia_Estoque                  date DEFAULT NULL,
    T294_Valor_Total_ICMS                            decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_ICMS_Presumido                  decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_ICMS_Pagar                      decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_IPI                             decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Imposto_Importacao              decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_COFINS                          decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_PIS                             decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Extra                           decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Frete                           decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Despesas                        decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Despesas_Fatura                 decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Aduaneira                       decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Despesas_Aduaneira              decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_IPI                        decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_ICMS_Sem_Reducao           decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_ICMS_Normal                decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_ICMS_Presumido             decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_COFINS                     decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_PIS                        decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_Extra                      decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Total_Base_Imposto_Importacao         decimal(19,6) DEFAULT 0.000000,
    T294_Peso_Kg_Liquido_Unitario_Fatura             decimal(19,6) DEFAULT 0.000000,
    T294_Peso_Kg_Liquido_Total_Fatura                decimal(19,6) DEFAULT 0.000000,
    T294_Valor_Adicao                                varchar(10) DEFAULT NULL,
    T294_Flag_ME                                     char(1) DEFAULT NULL,
    T294_T293_Id                                     int unsigned DEFAULT NULL,
    T294_D006_Id                                     int unsigned DEFAULT NULL,
    T294_Flag_Frete                                  char(1) DEFAULT 'S',
    T294_Valor_AFRMM                                 decimal(14,2) DEFAULT NULL,
    T294_Valor_Total_AFRMM                           decimal(14,2) DEFAULT NULL,
    T294_Aliquota_ICMS                               decimal(5,2) DEFAULT NULL,
    T294_Aliquota_ICMS_Presumido                     decimal(5,2) DEFAULT NULL,
    T294_Percentual_Reducao_ICMS                     decimal(5,2) DEFAULT NULL,
    T294_Percentual_Reducao_ICMS_Presumido           decimal(5,2) DEFAULT NULL,
    T294_Aliquota_ICMS_PIS_COFINS                    decimal(5,2) DEFAULT NULL,
    T294_Aliquota_ICMS_Credito_Presumido             decimal(5,2) DEFAULT NULL,
    T294_Adicao_Item                                 varchar(5),
    T294_T225_Id                                     int unsigned DEFAULT NULL,
    T294_Flag_Calculo_Manual                         char(1) DEFAULT 'N',
    T294_Valor_Total_Multa                           decimal(19,6) DEFAULT NULL,
    T294_Valor_Juros_Multa                           decimal(19,6) DEFAULT NULL,
    T294_Valor_Dumping                               decimal(19,6) DEFAULT NULL,
    T294_Valor_Seguro                                decimal(19,6) DEFAULT NULL,
    T294_Valor_Total_Seguro                          decimal(19,6) DEFAULT NULL,
    T294_Valor_Custo_Debug                           decimal(19,6) DEFAULT NULL,
    T294_Valor_Base_II_Debug                         decimal(19,6) DEFAULT NULL,
    T294_Cotacao_CC                                  decimal(12,4) DEFAULT 0.0000,
    T294_Item_Ac_Drawback                            varchar(20) DEFAULT NULL,
    T294_Unidade_Medida                              varchar(5) DEFAULT NULL,
    T294_T270_Id                                     int unsigned DEFAULT NULL,
    T294_T066_Id                                     int unsigned DEFAULT NULL,
    T294_Percentual_Acrescimo_Impostos               decimal(5,2) DEFAULT NULL,
    PRIMARY KEY (T294_Id),
    KEY IDX_T294_T060_Id (T294_T060_Id),
    KEY IDX_T294_T054_Id (T294_T054_Id),
    KEY IDX_T294_D037_Id (T294_D037_Id),
    KEY IDX_T294_D005_Id (T294_D005_Id),
    KEY IDX_T294_D009_Id (T294_D009_Id),
    KEY IDX_T294_T053_Id (T294_T053_Id),
    KEY IDX_T294_C004_Id (T294_C004_Id),
    KEY IDX_T294_T293_Id (T294_T293_Id),
    KEY IDX_T294_D006_Id (T294_D006_Id),
    KEY IDX_T294_T225_Id (T294_T225_Id),
    KEY IDX_T294_T270_Id (T294_T270_Id),
    KEY IDX_T294_T066_Id (T294_T066_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T295
$cClausula  = <<<EOT
create table if not exists T295 (
    T295_Id                                     int unsigned  auto_increment,
    T295_D018_Id                                int unsigned DEFAULT NULL,
    T295_T054_Id                                int NOT NULL DEFAULT 0,
    T295_Data_Emissao_DI                        date DEFAULT NULL,
    T295_Numero_DI                              varchar(30) DEFAULT NULL,
    T295_Valor_Produtos                         decimal(14,2) DEFAULT 0.00,
    T295_Valor_Frete                            decimal(14,4) DEFAULT 0.0000,
    T295_Valor_CIF                              decimal(14,2) DEFAULT 0.00  ,
    T295_Valor_THC                              decimal(14,4) DEFAULT 0.0000,
    T295_Valor_SISCOMEX                         decimal(14,4) DEFAULT 0.0000,
    T295_Valor_Imposto_Importacao               decimal(14,4) DEFAULT 0.0000,
    T295_Valor_IPI                              decimal(14,4) DEFAULT 0.0000,
    T295_Valor_PIS                              decimal(14,4) DEFAULT 0.0000,
    T295_Valor_COFINS                           decimal(14,4) DEFAULT 0.0000,
    T295_Valor_DI                               decimal(14,2) DEFAULT 0.00,
    T295_Local_Desembaraco                      varchar(60) DEFAULT NULL,
    T295_Data_Desembaraco                       date DEFAULT NULL,
    T295_Codigo_Exportador                      varchar(60) DEFAULT NULL,
    T295_D024_Id                                int unsigned DEFAULT NULL,
    T295_D118_Id                                int unsigned DEFAULT NULL,
    T295_D119_Id                                int unsigned DEFAULT NULL,
    T295_Cotacao_ME                             decimal(12,4)DEFAULT NULL,
    T295_Valor_Frete_Incluso                    decimal(14,4) DEFAULT 0.0000,
    T295_Valor_ICMS                             decimal(14,4) DEFAULT 0.0000,
    T295_Valor_Seguro                           decimal(14,4) DEFAULT 0.0000,
    T295_Valor_Despesa                          decimal(14,4) DEFAULT 0.0000,
    T295_Observacao_DI                          text DEFAULT NULL,
    T295_Observacao_NF                          text DEFAULT NULL,
    T295_C004_Id                                int unsigned DEFAULT NULL,
    T295_Data_Embarque                          date DEFAULT NULL,
    T295_ETA_Data                               date DEFAULT NULL,
    T295_ETA_Hora                               time DEFAULT NULL,
    T295_D044_Id                                int unsigned DEFAULT NULL,
    T295_Aliquota_ICMS                          decimal(5,2) DEFAULT NULL,
    T295_Aliquota_ICMS_Presumido                decimal(5,2) DEFAULT NULL,
    T295_Percentual_Reducao_ICMS                decimal(5,2) DEFAULT NULL,
    T295_Percentual_Reducao_ICMS_Presumido      decimal(5,2) DEFAULT NULL,
    T295_Valor_VMLE                             decimal(15,5) DEFAULT NULL,
    T295_Valor_VMLD                             decimal(15,5) DEFAULT NULL,
    T295_Aliquota_ICMS_PIS_COFINS               decimal(5,2) DEFAULT NULL,
    T295_D006_Id                                int DEFAULT NULL,
    T295_Aliquota_ICMS_Credito_Presumido        decimal(5,2) DEFAULT NULL,
    T295_Peso_Liquido                           decimal(10,4) DEFAULT 0.0000,
    T295_Peso_Bruto                             decimal(10,4) DEFAULT 0.0000,
    T295_C007_Id                                int unsigned DEFAULT NULL,
    T295_Ordem_Compra                           varchar(30)  DEFAULT NULL,
    T295_Data_Previsao                          date DEFAULT NULL,
    T295_Data_Estoque                           date DEFAULT NULL,
    T295_Valor_AFRMM                            decimal(14,4) DEFAULT 0.0000,
    T295_Data_Atualizacao_Custo                 datetime DEFAULT NULL,
    T295_Flag_MP                                int DEFAULT 0,
    T295_Data_Licenca_Importacao                date DEFAULT NULL,
    T295_Calculo_THC                            varchar(1) DEFAULT 1,
    T295_Somar_PIS_Custo                        char(1) DEFAULT 'N',
    T295_Somar_COFINS_Custo                     char(1) DEFAULT 'N',
    T295_Somar_Seguro_Custo                     char(1) DEFAULT 'N',
    T295_Somar_AFRMM_Custo                      char(1) DEFAULT 'N',
    T295_Somar_ICMS_Pagar_Custo                 char(1) DEFAULT 'N',
    T295_Somar_IPI_Custo                        char(1) DEFAULT 'N',
    T295_UF_Desembaraco                         varchar(2) DEFAULT NULL,
    T295_Codigo_Fabricante                      varchar(60) DEFAULT NULL,
    T295_Flag_Transporte_DI                     char(10) DEFAULT NULL,
    T295_Flag_Tipo_Intermedio_DI                varchar(50) DEFAULT NULL,
    T295_CNPJ_Importador                        varchar(50) DEFAULT NULL,
    T295_UF_Importador                          char(10) DEFAULT NULL,
    T295_Numero_Drawback_Importacao             varchar(50) DEFAULT NULL,
    T295_Cotacao_CC                             decimal(12,4) DEFAULT 0.0000,
    T295_Codigo_URF_Entrada                     int DEFAULT NULL,
    T295_Codigo_URF_Despacho                    int DEFAULT NULL,
    T295_Codigo_Moeda                           int DEFAULT NULL,
    T295_Codigo_Incoterms                       varchar(4) DEFAULT NULL,
    T295_Condicao_Mercadoria                    varchar(20) DEFAULT NULL,
    T295_Enquadramento_Material_Usado           varchar(25) DEFAULT NULL,
    T295_Operacao_Enquadramento_Material_Usado  varchar(30) DEFAULT NULL,
    T295_Data_Inicio_Demurrage                  date DEFAULT NULL,
    T295_Siscoserv_RAS                          text DEFAULT NULL,
    T295_Siscoserv_RP                           text DEFAULT NULL,
    T295_Orgao_Desembaraco                      varchar(100),
    PRIMARY KEY (T295_Id),
    KEY IDX_T295_D018_Id (T295_D018_Id),
    KEY IDX_T295_T054_Id (T295_T054_Id),
    KEY IDX_T295_D024_Id (T295_D024_Id),
    KEY IDX_T295_D118_Id (T295_D118_Id),
    KEY IDX_T295_D119_Id (T295_D119_Id),
    KEY IDX_T295_C004_Id (T295_C004_Id),
    KEY IDX_T295_D044_Id (T295_D044_Id),
    KEY IDX_T295_D006_Id (T295_D006_Id),
    KEY IDX_T295_C007_Id (T295_C007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T296
$cClausula  = <<<EOT
create table if not exists T296 (
    T296_Id                                          int unsigned auto_increment,
    T296_T060_Id                                     int unsigned DEFAULT NULL,
    T296_T054_Id                                     int unsigned DEFAULT NULL,
    T296_D037_Id                                     int unsigned DEFAULT NULL,
    T296_D005_Id                                     int unsigned DEFAULT NULL,
    T296_D009_Id                                     int unsigned DEFAULT NULL,
    T296_T053_Id                                     int unsigned DEFAULT NULL,
    T296_C004_Id                                     int unsigned DEFAULT 0,
    T296_Codigo_Produto                              varchar(30) DEFAULT NULL,
    T296_Codigo_Produto_Fornecedor                   varchar(20) DEFAULT NULL,
    T296_Descricao_Produto                           varchar(120) DEFAULT NULL,
    T296_Classificacao_Fiscal                        varchar(12) DEFAULT NULL,
    T296_Prazo_Entrega                               int DEFAULT 0,
    T296_Quantidade                                  int DEFAULT 0,
    T296_Valor_Custo_Unitario_Anterior               decimal(19,5) DEFAULT 0.00000,
    T296_Valor_Custo_Unitario                        decimal(20,7) DEFAULT NULL,
    T296_Valor_Total_Custo                           decimal(19,2) DEFAULT 0.00,
    T296_Valor_Preco_Unitario_Nota_Fiscal            decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Preco_Nota_Fiscal               decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Custo_Unitario_Original               decimal(19,5) DEFAULT 0.00000,
    T296_Valor_Total_Custo_Original                  decimal(19,2) DEFAULT 0.00,
    T296_Valor_Base_IPI                              decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_ICMS_Sem_Reducao                 decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_ICMS_Normal                      decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_ICMS_Presumido                   decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_COFINS                           decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_PIS                              decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_Extra                            decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Base_Imposto_Importacao               decimal(19,6) DEFAULT 0.000000,
    T296_Aliquota_IPI                                decimal(5,2) DEFAULT 0.00,
    T296_Aliquota_Imposto_Importacao                 decimal(5,2) DEFAULT 0.00,
    T296_Aliquota_COFINS                             decimal(5,2) DEFAULT 0.00,
    T296_Aliquota_PIS                                decimal(5,2) DEFAULT 0.00,
    T296_Aliquota_Extra                              decimal(5,2) DEFAULT 0.00,
    T296_Valor_ICMS                                  decimal(19,6) DEFAULT 0.000000,
    T296_Valor_ICMS_Presumido                        decimal(19,6) DEFAULT 0.000000,
    T296_Valor_ICMS_Pagar                            decimal(19,6) DEFAULT 0.000000,
    T296_Valor_IPI                                   decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Imposto_Importacao                    decimal(19,6) DEFAULT 0.000000,
    T296_Valor_COFINS                                decimal(19,6) DEFAULT 0.000000,
    T296_Valor_PIS                                   decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Extra                                 decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Frete                                 decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Despesas                              decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Despesas_Fatura                       decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Aduaneira                             decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Despesas_Aduaneira                    decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Custo_Final_Unitario                  decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Custo_Final                     decimal(19,2) DEFAULT 0.00,
    T296_Peso_Kg_Liquido_Unitario                    decimal(19,6) DEFAULT 0.000000,
    T296_Peso_Kg_Liquido_Total                       decimal(19,6) DEFAULT 0.000000,
    T296_Peso_Lb_Liquido_Unitario                    decimal(19,3) DEFAULT 0.000,
    T296_Peso_Lb_Liquido_Total                       decimal(19,3) DEFAULT 0.000,
    T296_Multiplicador_X                             decimal(19,6) DEFAULT 0.000000,
    T296_Multiplicador_Y                             decimal(19,6) DEFAULT 0.000000,
    T296_Data_Transferencia_Estoque                  date DEFAULT NULL,
    T296_Valor_Total_ICMS                            decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_ICMS_Presumido                  decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_ICMS_Pagar                      decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_IPI                             decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Imposto_Importacao              decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_COFINS                          decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_PIS                             decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Extra                           decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Frete                           decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Despesas                        decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Despesas_Fatura                 decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Aduaneira                       decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Despesas_Aduaneira              decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_IPI                        decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_ICMS_Sem_Reducao           decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_ICMS_Normal                decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_ICMS_Presumido             decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_COFINS                     decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_PIS                        decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_Extra                      decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Total_Base_Imposto_Importacao         decimal(19,6) DEFAULT 0.000000,
    T296_Peso_Kg_Liquido_Unitario_Fatura             decimal(19,6) DEFAULT 0.000000,
    T296_Peso_Kg_Liquido_Total_Fatura                decimal(19,6) DEFAULT 0.000000,
    T296_Valor_Adicao                                varchar(10) DEFAULT NULL,
    T296_Flag_ME                                     char(1) DEFAULT NULL,
    T296_T295_Id                                     int unsigned DEFAULT NULL,
    T296_D006_Id                                     int unsigned DEFAULT NULL,
    T296_Flag_Frete                                  char(1) DEFAULT 'S',
    T296_Valor_AFRMM                                 decimal(14,2) DEFAULT NULL,
    T296_Valor_Total_AFRMM                           decimal(14,2) DEFAULT NULL,
    T296_Aliquota_ICMS                               decimal(5,2) DEFAULT NULL,
    T296_Aliquota_ICMS_Presumido                     decimal(5,2) DEFAULT NULL,
    T296_Percentual_Reducao_ICMS                     decimal(5,2) DEFAULT NULL,
    T296_Percentual_Reducao_ICMS_Presumido           decimal(5,2) DEFAULT NULL,
    T296_Aliquota_ICMS_PIS_COFINS                    decimal(5,2) DEFAULT NULL,
    T296_Aliquota_ICMS_Credito_Presumido             decimal(5,2) DEFAULT NULL,
    T296_Adicao_Item                                 varchar(5),
    T296_T225_Id                                     int unsigned DEFAULT NULL,
    T296_Flag_Calculo_Manual                         char(1) DEFAULT 'N',
    T296_Valor_Total_Multa                           decimal(19,6) DEFAULT NULL,
    T296_Valor_Juros_Multa                           decimal(19,6) DEFAULT NULL,
    T296_Valor_Dumping                               decimal(19,6) DEFAULT NULL,
    T296_Valor_Seguro                                decimal(19,6) DEFAULT NULL,
    T296_Valor_Total_Seguro                          decimal(19,6) DEFAULT NULL,
    T296_Valor_Custo_Debug                           decimal(19,6) DEFAULT NULL,
    T296_Valor_Base_II_Debug                         decimal(19,6) DEFAULT NULL,
    T296_Cotacao_CC                                  decimal(12,4) DEFAULT 0.0000,
    T296_Item_Ac_Drawback                            varchar(20) DEFAULT NULL,
    T296_Unidade_Medida                              varchar(5) DEFAULT NULL,
    T296_T270_Id                                     int unsigned DEFAULT NULL,
    T296_T066_Id                                     int unsigned DEFAULT NULL,
    T296_Percentual_Acrescimo_Impostos               decimal(5,2) DEFAULT NULL,
    PRIMARY KEY (T296_Id),
    KEY IDX_T296_T060_Id (T296_T060_Id),
    KEY IDX_T296_T054_Id (T296_T054_Id),
    KEY IDX_T296_D037_Id (T296_D037_Id),
    KEY IDX_T296_D005_Id (T296_D005_Id),
    KEY IDX_T296_D009_Id (T296_D009_Id),
    KEY IDX_T296_T053_Id (T296_T053_Id),
    KEY IDX_T296_C004_Id (T296_C004_Id),
    KEY IDX_T296_T295_Id (T296_T295_Id),
    KEY IDX_T296_D006_Id (T296_D006_Id),
    KEY IDX_T296_T225_Id (T296_T225_Id),
    KEY IDX_T296_T270_Id (T296_T270_Id),
    KEY IDX_T296_T066_Id (T296_T066_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T002A
$cClausula  = <<<EOT
create table if not exists T002A (
    T002A_Id                              int(10) unsigned auto_increment,
    T002A_T002_Id                         int(10),
    T002A_Percentual_Comissao_Supervisor  decimal(5,2) DEFAULT '0.00',
    T002A_Valor_Total_Comissao_Supervisor decimal(14,2) DEFAULT '0.00',   
    PRIMARY KEY (T002A_Id),
    KEY IDX_T002A_T002_Id (T002A_T002_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T014A
$cClausula  = <<<EOT
create table if not exists T014A (
    T014A_Id                              int(10) unsigned auto_increment,
    T014A_T014_Id                         int(10),
    T014A_Quantidade  decimal(10,3),
    T014A_Valor_Custo_Unitario decimal(20,10) DEFAULT '0.0000000000',   
    T014A_Aliquota_ICMS_Credito_Recebida decimal(7,4) DEFAULT '0.0000',
    PRIMARY KEY (T014A_Id),
    KEY IDX_T014A_T014_Id (T014A_T014_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D185
$cClausula  = <<<EOT
create table if not exists D185 (
    D185_Id int unsigned NOT NULL AUTO_INCREMENT,
    D185_Codigo text COMMENT 'Codigo Integração e-commerce',
    D185_Descricao text COMMENT 'Descrição e-commerce',
    D185_Comprimento decimal(14,4) DEFAULT '0.0000' COMMENT 'Comprimento',
    D185_Altura decimal(14,4) DEFAULT '0.0000' COMMENT 'Altura',
    D185_Largura decimal(14,4) DEFAULT '0.0000' COMMENT 'Largura',
    D185_Descricao_Detalhada text COMMENT 'Descrição Detalhada',
    D185_Integracao_ID int DEFAULT NULL COMMENT 'ID Integração E-commerce',
    PRIMARY KEY (D185_Id)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8 
COMMENT='Produtos e-commerce tray';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D186
$cClausula  = <<<EOT
create table if not exists D186 (
  D186_Id int unsigned NOT NULL AUTO_INCREMENT,
  D186_D001_Id int NOT NULL COMMENT 'Id Produto',
  D186_D185_Id int NOT NULL COMMENT 'Id Produto e-commerce tray',
  D186_Atributo_Nome text COMMENT 'Atributo Nome',
  D186_Atributo_Valor text COMMENT 'Atributo Valor',
  D186_Integracao_ID int DEFAULT NULL COMMENT 'ID integração e-commerce tray',
  PRIMARY KEY (D186_Id)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=latin1 
COMMENT='Atributos de produtos e-commerce tray';
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela C040
$cClausula  = <<<EOT
create table if not exists C040 (
  C040_Id int unsigned NOT NULL AUTO_INCREMENT,
  C040_Ambiente int NOT NULL COMMENT '0 - PRODUCAO / 1 - HOMOLOGACAO',
  C040_Versao_NFCE text COMMENT 'Versao da NFCE que o PDV ira emitir',
  C040_Lote_NFCE text COMMENT 'Lote da NFCE que o PDV ira emitir',
  C040_Numero_NFCE text COMMENT 'Numero da NFCE que o PDV ira emitir',
  C040_Serie_NFCE text COMMENT 'Serie da NFCE que o PDV ira emitir',
  C040_Impressora_Nome text COMMENT 'Nome da impressora conforme instalada no windows do PDV',
  C040_Impressora_Porta text COMMENT 'Porta da impressora conforme instalada no windows do PDV',
  C040_Impressora_Modelo text COMMENT 'Modelo POS da impressora conforme documentacao da ACBR',
  PRIMARY KEY (C040_Id)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=latin1 
COMMENT='PDVs NFC-e Smartness';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela C041
$cClausula  = <<<EOT
create table if not exists C041 (
  C041_Id int unsigned NOT NULL AUTO_INCREMENT,
  C041_De text,
  C041_Para text,
  C041_Assunto text,
  C041_Corpo text,
  C041_Tabela varchar(10),
  C041_Tabela_Valor int,
  PRIMARY KEY (C041_Id),
  KEY C041_Index_Tabela (C041_Tabela),
  KEY C041_Index_Tabela_Valor (C041_Tabela_Valor)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=latin1 
COMMENT='E-mails enviados';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela C042
$cClausula  = <<<EOT
create table if not exists C042 (
  C042_Id int unsigned NOT NULL AUTO_INCREMENT,
  C042_C041_Id int,
  C042_Url varchar(200),
  C042_Parametros text,
  C042_Arquivo varchar(200),
  C042_Arquivo_Salvo varchar(200),
  PRIMARY KEY (C042_Id),
  KEY IDX_C042_C041_Id (C042_C041_Id)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=latin1 
COMMENT='E-mails enviados - lista de anexos';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D001A
$cClausula  = <<<EOT
create table if not exists D001A (
    D001A_Id                              int(10) unsigned auto_increment,
    D001A_D001_Id                         int(10),
    PRIMARY KEY (D001A_Id),
    KEY IDX_D001A_D001_Id (D001A_D001_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists D187 (
    D187_Id                                   int(10) unsigned auto_increment,
    D187_Codigo                               varchar(5) not null,           
    D187_Descricao                            varchar(50) not null,           
    D187_Codigo_Plano_Conta_Contabil          varchar(50) not null,           
    PRIMARY KEY (D187_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists D188 (
	 D188_Id 			                            int(10) unsigned auto_increment,
	 D188_Codigo_Observacao		                varchar(6) not null,
	 D188_Descricao_Observacao 	              text not null,
   PRIMARY KEY (D188_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists D189 (
  D189_Id                 int(10) unsigned auto_increment,   
  D189_D018_Id		        int(10),
  D189_Codigo_Ajuste	    varchar(10),
  D189_Descricao_Ajuste	  text,
  PRIMARY KEY (D189_Id),
  KEY IDX_D189_D018_Id (D189_D018_Id)
)
DEFAULT CHARSET=utf8
ENGINE=INNODB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists T297 (
   T297_Id                int(10) unsigned auto_increment,
	 T297_T013_Id           int(10),
   T297_T007_Id           int(10),
   T297_D188_Id           int(10),            
   PRIMARY KEY (T297_Id),
   KEY IDX_T297_T013_Id (T297_T013_Id),
   KEY IDX_T297_T007_Id (T297_T007_Id),
   KEY IDX_T297_D188_Id (T297_D188_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists T298 (
   T298_Id                int(10) unsigned auto_increment,
	 T298_T014_Id 		      int(10),
	 T298_T008_Id 		      int(10),
	 T298_Descricao_Complementar_Ajuste text,
	 T298_D189_Id		        int(10),
	 T298_Valor_Base_ICMS 	decimal(10,2),
	 T298_Aliquota_ICMS	    decimal(5,2),
	 T298_Valor_ICMS		    decimal(12,5),
	 T298_Outros_Valores	  decimal(10,2),		
	 T298_T297_Id 		      int(10),
   PRIMARY KEY (T298_Id),
   KEY IDX_T298_T014_Id   (T298_T014_Id),   
   KEY IDX_T298_T008_Id   (T298_T008_Id),
   KEY IDX_T298_D189_Id   (T298_D189_Id),
   KEY IDX_T298_T297_Id   (T298_T297_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D009A
$cClausula  = <<<EOT
create table if not exists D009A (
    D009A_Id                              int(10) unsigned auto_increment,
    D009A_D009_Id                         int(10),
    D009A_Quantidade_Orcada               decimal(12,5),
    PRIMARY KEY (D009A_Id),
    KEY IDX_D009A_D009_Id (D009A_D009_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D149A
$cClausula  = <<<EOT
create table if not exists D149A (
    D149A_Id                                          int(10) unsigned auto_increment,
    D149A_D149_Id                                     int(10),
    D149A_Aliquota_ICMS_Difal_Uso_Consumo             decimal(10,2) DEFAULT '0.00',
    PRIMARY KEY (D149A_Id),
    UNIQUE KEY IDX_D149A_D149_Id (D149A_D149_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T003A
$cClausula  = <<<EOT
create table if not exists T003A (
    T003A_Id                              int(10) unsigned auto_increment,
    T003A_T003_Id                         int(10),
    T003A_Hora_Inclusao                   time default '00:00:00',
    PRIMARY KEY (T003A_Id),
    UNIQUE KEY IDX_T003A_T003_Id (T003A_T003_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T199
$cClausula  = <<<EOT
create table if not exists T199 (
    T199_Id                               int(10) unsigned auto_increment,
    T199_T007_Id                          int(10) unsigned,
    T199_C007_Id                          int(10) unsigned,
    T199_Data_Hora_Inclusao               datetime default '0000-00-00 00:00:00',
    T199_Sequencial_Evento                int(10) default '1',
    T199_Correcao                         text not null,
    T199_Flag_Enviado                     enum('S','N') default 'N',
    T199_Data_Envio_Email_Cce             datetime DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (T199_Id),
    KEY IDX_T199_T007_Id (T199_T007_Id),
    KEY IDX_T199_C007_Id (T199_C007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T200
$cClausula  = <<<EOT
create table if not exists T200 (
    T200_Id                               int(10) unsigned auto_increment,
    T200_T199_Id                          int(10) unsigned,
    T200_C007_Id                          int(10) unsigned,
    T200_Data_Hora_Inclusao               datetime default '0000-00-00 00:00:00',
    T200_Status                           int(10) default '0',
    T200_Motivo                           tinytext,
    T200_Retorno_XML                      text,
    PRIMARY KEY (T200_Id),
    KEY IDX_T200_T199_Id (T200_T199_Id),
    KEY IDX_T200_C007_Id (T200_C007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D190 - Cadastros tipo de despesas PO e Quotation
$cClausula = <<<EOT
create table if not exists D190 (
  D190_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D190_Tipo_Despesa varchar(100) NOT NULL
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela D191 - Cadastros de despesas da PO e quotation
$cClausula = <<<EOT
create table if not exists D191 (
  D191_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D191_Descricao_Despesa varchar(100) NOT NULL
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela D192 - Cadastro de pacotes de despesas PO e Quotation
$cClausula = <<<EOT
create table if not exists D192 (
  D192_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D192_D190_Id int(10),
  D192_D191_Id int(10),
  D192_Aliquota decimal(5,2) default '0',
  D192_Flag_Tipo ENUM('F','V'),
  D192_Valor decimal(14,4) default '0',
  D192_Formula text,
  KEY IDX_D192_D190_Id (D192_D190_Id),
  KEY IDX_D192_D191_Id (D192_D191_Id),
  KEY IDX_D192_Flag_Tipo (D192_Flag_Tipo)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T299 - despesas da Quotation
$cClausula = <<<EOT
create table if not exists T299 (
  T299_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T299_T293_Id INT(10),
  T299_D190_Id INT(10),
  T299_D191_Id INT(10),
  T299_Aliquota decimal(5,2) default '0',
  T299_Flag_Tipo varchar(1),
  T299_Valor decimal(14,4) default '0', 
  T299_Formula text,
  T299_Data date,
  KEY IDX_T299_T293_Id (T299_T293_Id),
  KEY IDX_T299_D190_Id (T299_D190_Id),
  KEY IDX_T299_D191_Id (T299_D191_Id),
  KEY IDX_T299_Flag_Tipo (T299_Flag_Tipo),
  KEY IDX_T299_Data (T299_Data)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela T300 - despesas da PO
$cClausula = <<<EOT
create table if not exists T300 (
  T300_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T300_T295_Id INT(10),
  T300_D190_Id INT(10),
  T300_D191_Id INT(10),
  T300_Aliquota decimal(5,2) default '0',
  T300_Flag_Tipo varchar(1),
  T300_Valor decimal(14,4) default '0', 
  T300_Formula text,
  T300_Data date,
  KEY IDX_T300_T295_Id (T300_T295_Id),
  KEY IDX_T300_D190_Id (T300_D190_Id),
  KEY IDX_T300_D191_Id (T300_D191_Id),
  KEY IDX_T300_Flag_Tipo (T300_Flag_Tipo),
  KEY IDX_T300_Data (T300_Data)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//criação da tabela D193 - plano de contas
$cClausula  = <<<EOT
create table if not exists D193 (  
    D193_Id                   INT(10) UNSIGNED AUTO_INCREMENT,
    D193_Codigo               VARCHAR(70),    
    D193_Nome_Conta           VARCHAR(80),    
    D193_Tipo                 CHAR(1),
    D193_Flag_Conta_Movimento ENUM('S','N') default 'N',
    PRIMARY KEY (D193_Id)    
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

//criação da tabela T301
$cClausula  = <<<EOT
create table if not exists T301 (
  T301_Id		            INT(10) UNSIGNED AUTO_INCREMENT,
  T301_Mes		          VARCHAR(2),
  T301_Ano		          VARCHAR(4),
  T301_Saldo		        DECIMAL(14,2),
  T301_D193_Id          INT(10),
  PRIMARY KEY (T301_Id),
  KEY IDX_T301_D193_Id (T301_D193_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}
////criação da tabela T302 - Lançamentos
$cClausula  = <<<EOT
create table if not exists T302 (
    T302_Id                 INT(10) UNSIGNED AUTO_INCREMENT,
    T302_D193_Id		        INT(10),
    T302_Data		            DATE,
    T302_Valor		          DECIMAL(14,2),
    T302_Descricao		      VARCHAR(200),
    T302_Credito_Debito	    CHAR(1),
    T302_Tabela		          VARCHAR(4),
    T302_Tabela_Id		      INT(10),
    T302_Entidade           INT(10),
    PRIMARY KEY (T302_Id),
    UNIQUE(T302_Entidade, T302_Tabela_Id, T302_Tabela,T302_Credito_Debito),
    KEY IDX_T302_D193_Id  (T302_D193_Id)   
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists D194 (  
    D194_Id INT(10)   UNSIGNED AUTO_INCREMENT,
    D194_Descricao    VARCHAR(70),
    D194_Tabela       VARCHAR(5),
    D194_Campo_Data   VARCHAR(30),
    D194_Extra        TEXT,
    PRIMARY KEY (D194_Id)  
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists D195 (  
    D195_Id                   INT(10) UNSIGNED AUTO_INCREMENT,
    D195_D194_Id              INT(10) UNSIGNED,
    D195_Descricao            VARCHAR(80),
    D195_Campo                VARCHAR(50),
    D195_Conta_Debito         INT(10) UNSIGNED,
    D195_Conta_Credito        INT(10) UNSIGNED,
    D195_Descricao_Contabil   VARCHAR(200),
    PRIMARY KEY (D195_Id),
    KEY IDX_D195_D194_Id (D195_D194_Id) 
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists T224A (  
    T224A_Id                  INT(10) UNSIGNED AUTO_INCREMENT,
    T224A_T224_Id             INT(10) UNSIGNED,
    T224A_Valor_FCP_ST        decimal(15,2) DEFAULT 0.00,                          
    T224A_Valor_Base_FCP_ST   decimal(15,2) DEFAULT 0.00,    
    T224A_Valor_FCP           decimal(15,2) DEFAULT 0.00,                          
    T224A_Valor_Base_FCP      decimal(15,2) DEFAULT 0.00,                       
    PRIMARY KEY (T224A_Id),
    UNIQUE KEY IDX_T224A_T224_Id (T224A_T224_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists T218A (  
    T218A_Id                  INT(10) UNSIGNED AUTO_INCREMENT,
    T218A_T218_Id             INT(10) UNSIGNED,
    T218A_Valor_FCP_ST        decimal(15,2) DEFAULT 0.00,                          
    T218A_Valor_Base_FCP_ST   decimal(15,2) DEFAULT 0.00,   
    T218A_Valor_FCP           decimal(15,2) DEFAULT 0.00,                          
    T218A_Valor_Base_FCP      decimal(15,2) DEFAULT 0.00,                       
    PRIMARY KEY (T218A_Id),
    UNIQUE KEY IDX_T218A_T218_Id (T218A_T218_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists T055A (  
    T055A_Id                  INT(10) UNSIGNED AUTO_INCREMENT,
    T055A_T055_Id             INT(10) UNSIGNED,
    T055A_Data_Validade       date DEFAULT '0000-00-00',                       
    PRIMARY KEY (T055A_Id),
    UNIQUE KEY IDX_T055A_T055_Id (T055A_T055_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

$cClausula  = <<<EOT
create table if not exists T007A (  
    T007A_Id                     INT(10) UNSIGNED AUTO_INCREMENT,
    T007A_T007_Id                INT(10) UNSIGNED,
    T007A_Flag_Conversao_Unidade VARCHAR(1) DEFAULT 'N',                       
    PRIMARY KEY (T007A_Id),
    UNIQUE KEY IDX_T007A_T007_Id (T007A_T007_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// TABELA ESTENDIDA T066A
$cClausula  = <<<EOT
create table if not exists T066A (  
    T066A_Id                      INT(10) UNSIGNED AUTO_INCREMENT,
    T066A_T066_Id                 INT(10) UNSIGNED,
    T066A_Quantidade_Estoque_Fora DECIMAL(12,2),                       
    PRIMARY KEY (T066A_Id),
    UNIQUE KEY IDX_T066A_T066_Id (T066A_T066_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T299 - despesas da Quotation
$cClausula = <<<EOT
create table if not exists T303 (
  T303_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T303_T140_Id INT(10),
  T303_D001_Id INT(10),
  KEY IDX_T303_T140_Id (T303_T140_Id),
  KEY IDX_T303_D001_Id (T303_D001_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}


// Componente do valor da prestação - CT-e
$cClausula = <<<EOT
create table if not exists T304 (
  T304_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T304_Nome_Componente VARCHAR(15),
  T304_Valor_Componente DECIMAL(15,2),
  T304_T084_Id INT(10),
  KEY IDX_T304_T084_Id (T304_T084_Id)  
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// INformações de quantidade da carga - CT-e
$cClausula = <<<EOT
create table if not exists T305 (
  T305_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T305_Codigo_Unidade char(2) NOT NULL,
  T305_Tipo_Medida varchar(20),
  T305_Quantidade decimal(15,4),
  T305_T084_Id INT(10),
  KEY IDX_T305_T084_Id (T305_T084_Id)  
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Observações do Contribuinte- CT-e
$cClausula = <<<EOT
create table if not exists T306 (
  T306_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T306_Identificador VARCHAR(20) NOT NULL,
  T306_Conteudo VARCHAR(160) NOT NULL,   
  T306_T084_Id INT(10),
  KEY IDX_T306_T084_Id (T306_T084_Id)  
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}



// // Pagamento - CT-e
$cClausula = <<<EOT
create table if not exists T307 (
  T307_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  T307_Numero_Duplicata VARCHAR(60) NOT NULL,
  T307_Numero_Parcela INT(3) NOT NULL,
  T307_Prazos_Dias INT(3),
  T307_Data_Vencimento  DATE,
  T307_Valor_Parcela  DECIMAL(15,2) NOT NULL,
  T307_T084_Id  INT(10),
  T307_D027_Id  INT(10),
  T307_Taxa_Juros DECIMAL(12,2),
  T307_Valor_Juros DECIMAL(12,2),
  KEY IDX_T307_T084_Id (T307_T084_Id),  
  KEY IDX_T307_D027_Id (T307_D027_Id)  
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// // ICMS Transporte
$cClausula = <<<EOT
create table if not exists D196 (
  D196_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  D196_Aliquota_ICMS_Interno  DECIMAL(5,2),
  D196_Aliquota_ICMS_Inter_Estadual DECIMAL(5,2),
  D196_C004_Id  int(10) unsigned,
  D196_D018_Id_Origem INT(10) unsigned,
  D196_D018_Id_Destino INT(10) unsigned,
  KEY IDX_D196_C004_Id (D196_C004_Id),  
  KEY IDX_D196_D018_Id_Origem (D196_D018_Id_Origem),  
  KEY IDX_D196_D018_Id_Destino (D196_D018_Id_Destino)  
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}


// TABELA ESTENDIDA T001A
$cClausula  = <<<EOT
create table if not exists T001A (  
    T001A_Id                           INT(10) UNSIGNED AUTO_INCREMENT,
    T001A_T001_Id                      INT(10) UNSIGNED,
    T001A_Valor_Custo_Unitario_Liquido DECIMAL(20,10),                       
    PRIMARY KEY (T001A_Id),
    UNIQUE KEY IDX_T001A_T001_Id (T001A_T001_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// Documentos de Transporte Anterior
$cClausula = <<<EOT
create table if not exists T308 (
  T308_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  T308_Chave_Acesso VARCHAR(44) NOT NULL,
  T308_T084_Id  INT(10),
  KEY IDX_T308_T084_Id (T308_T084_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// criação da tabela D069A
$cClausula  = <<<EOT
create table if not exists D069A (
    D069A_Id                             int(10) unsigned auto_increment,
    D069A_D069_Id                        int(10),
    D069A_Percentual_Reducao_ICMS_IE_ST  decimal(10,2) DEFAULT '0.00',
    D069A_Percentual_Reducao_ICMS_INT_ST decimal(10,2) DEFAULT '0.00',   
    PRIMARY KEY (D069A_Id),
    UNIQUE KEY IDX_D069A_D069_Id (D069A_D069_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela T117A
$cClausula  = <<<EOT
create table if not exists T117A (
    T117A_Id                             int(10) unsigned auto_increment,
    T117A_T117_Id                        int(10),
    T117A_Mensagem_Juros_Multa           text default '',
    PRIMARY KEY (T117A_Id),
    UNIQUE KEY IDX_T117A_T117_Id (T117A_T117_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}


// criação da tabela T013A
$cClausula  = <<<EOT
create table if not exists T013A (
    T013A_Id                                int(10) unsigned auto_increment,
    T013A_T013_Id                           int(10),
    T013A_Valor_Total_ICMS_Mono_Retido      decimal(15,2) DEFAULT '0.00',
    T013A_Valor_Total_Base_ICMS_Mono_Retido decimal(15,2) DEFAULT '0.00',   
    T013A_Aliquota_ICMS_Credito_Recebida    decimal(7,4) DEFAULT '0.0000',
    T013A_Valor_ICMS_Credito_Recebida       decimal(15,2) DEFAULT '0.00',
    PRIMARY KEY (T013A_Id),
    UNIQUE KEY IDX_T013A_T013_Id (T013A_T013_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

// criação da tabela D024A
$cClausula  = <<<EOT
create table if not exists D024A (
    D024A_Id                                int(10) unsigned auto_increment,
    D024A_D024_Id                           int(10),
    D024A_SMT001_Id                         int(10),
    D024A_Fidelidade                        varchar(20) DEFAULT '',   
    D024A_Fidelidade_Sugestao               varchar(20) DEFAULT '',   
    D024A_Aprovado_Compra                   char(1) DEFAULT '',   
    D024A_SMT018_Id                         int(10),
    D024A_SMT018_Id_Sugestao                int(10),
    D024A_Empresa                           text,
    D024A_Salario                           decimal(13,2),
    D024A_Cargo                             text,
    PRIMARY KEY (D024A_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}


// SMARTNESS - Tamba
// Extensão da tabela T255 - OP
$t255aCreate  = " CREATE TABLE IF NOT EXISTS T255A ( ";
$t255aCreate .=   " T255A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$t255aCreate .=   " T255A_T255_Id INT(10) NOT NULL, ";
$t255aCreate .=   " T255A_OP_Impressa enum('S','N') DEFAULT 'N' COMMENT 'Indica se a OP já foi impressa ou não.', ";
$t255aCreate .=   " T255A_Data_Hora timestamp NULL DEFAULT NULL COMMENT 'Data e hora da impressão', ";
$t255aCreate .=   " T255A_Usuario text COMMENT 'Usuário que fez a impressão da OP', ";
$t255aCreate .=   " T255A_Data_Simulacao_Estoque datetime DEFAULT CURRENT_TIMESTAMP, ";
$t255aCreate .=   " UNIQUE KEY T255A_T255_Id_IX (T255A_T255_Id) ";
$t255aCreate .= " ) ";
$t255aCreate .= " ENGINE=InnoDB ";
$t255aCreate .= " COMMENT='Extensão da tabela T255 - OP' ";
$t255aCreate .= " DEFAULT CHARSET=utf8; ";

$t255aSql   = mysql_query($t255aCreate);
$t255aError = mysql_error();
if (!empty($t255aError)){
  echo "$t255aCreate<hr>$t255aError";
}
// Extensão da tabela D083 - Referencia
$d083aCreate  = " CREATE TABLE IF NOT EXISTS D083A ( ";
$d083aCreate .=   " D083A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$d083aCreate .=   " D083A_D083_Id INT(10) NOT NULL, ";
$d083aCreate .=   " D083A_Multiplicador	decimal(14,4) COMMENT 'Quantidade Embalagem Referente', ";
$d083aCreate .=   " UNIQUE KEY D083A_D083_Id_IX (D083A_D083_Id) ";
$d083aCreate .= " ) ";
$d083aCreate .= " ENGINE=InnoDB ";
$d083aCreate .= " COMMENT='Extensão da tabela D083 - Referencia' ";
$d083aCreate .= " DEFAULT CHARSET=utf8; ";

$d083aSql   = mysql_query($d083aCreate);
$d083aError = mysql_error();
if (!empty($d083aError)){
  echo "$d083aCreate<hr>$d083aError";
}

// Extensão da tabela D002 - SubGrupo
$D002aCreate  = " CREATE TABLE IF NOT EXISTS D002A ( ";
$D002aCreate .=   " D002A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D002aCreate .=   " D002A_D002_Id INT(10) NOT NULL, ";
$D002aCreate .=   " D002A_Utiliza_Conversao enum('S','N') DEFAULT 'N' COMMENT 'Determina se utiliza conversão de quantidade.', ";
$D002aCreate .=   " D002A_Tolerancia_Conversao decimal(5,2) DEFAULT 0 COMMENT 'Percentual de tolerancia na conferencia quando utilizada conversao.', ";
$D002aCreate .=   " UNIQUE KEY D002A_D002_Id_IX (D002A_D002_Id) ";
$D002aCreate .= " ) ";
$D002aCreate .= " ENGINE=InnoDB ";
$D002aCreate .= " COMMENT='Extensão da tabela D002 - SubGrupo' ";
$D002aCreate .= " DEFAULT CHARSET=utf8; ";

$D002aSql   = mysql_query($D002aCreate);
$D002aError = mysql_error();
if (!empty($D002aError)) {
  echo "$D002aCreate<hr>$D002aError";
}

// Tabela D200 - Cadastro de Embalagem
$D200Create  = " CREATE TABLE IF NOT EXISTS D200 ( ";
$D200Create .=   " D200_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D200Create .=   " D200_Descricao VARCHAR(100) NOT NULL ";
$D200Create .= " ) ";
$D200Create .= " ENGINE=InnoDB ";
$D200Create .= " COMMENT='Cadastro de embalagem' ";
$D200Create .= " DEFAULT CHARSET=utf8; ";

$D200Sql   = mysql_query($D200Create);
$D200Error = mysql_error();
if (!empty($D200Error)) {
  echo "$D200Create<hr>$D200Error";
}

// Tabela T313 - Conferencia Volume (Temporaria)
$T313Create  = " CREATE TABLE IF NOT EXISTS T313 ( ";
$T313Create .=   " T313_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$T313Create .=   " T313_T005_Id INT(10) NOT NULL, ";
$T313Create .=   " T313_T006_Id INT(10) NOT NULL, ";
$T313Create .=   " T313_D009_Id INT(10) NOT NULL, ";
$T313Create .=   " T313_C007_Id INT(10) NOT NULL, ";
$T313Create .=   " T313_Quantidade decimal(5,2) DEFAULT 0 COMMENT 'Quantidade conferida pro volume.' ";
$T313Create .= " ) ";
$T313Create .= " ENGINE=InnoDB ";
$T313Create .= " COMMENT='Conferencia Volume (Temporaria)' ";
$T313Create .= " DEFAULT CHARSET=utf8; ";

$T313Sql   = mysql_query($T313Create);
$T313Error = mysql_error();
if (!empty($T313Error)) {
  echo "$T313Create<hr>$T313Error";
}

// Tabela T314 - Volume
$T314Create  = " CREATE TABLE IF NOT EXISTS T314 ( ";
$T314Create .=   " T314_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$T314Create .=   " T314_T005_Id INT(10) NOT NULL, ";
$T314Create .=   " T314_C007_Id INT(10) NOT NULL, ";
$T314Create .=   " T314_D200_Id INT(10) NOT NULL COMMENT 'Embalagem.', ";
$T314Create .=   " T314_Volume VARCHAR(20) NOT NULL COMMENT 'Codigo do volume.', ";
$T314Create .=   " T314_Identificador VARCHAR(20) COMMENT 'Identificador do embarque.', ";
$T314Create .=   " T314_Sequencia INT(10) COMMENT 'Sequencia do embarque.', ";
$T314Create .=   " T314_Peso_Bruto decimal(5,2) DEFAULT 0 COMMENT 'Peso bruto total do volume.', ";
$T314Create .=   " T314_Peso_Liquido decimal(5,2) DEFAULT 0 COMMENT 'Peso liquido total do volume.' ";
$T314Create .= " ) ";
$T314Create .= " ENGINE=InnoDB ";
$T314Create .= " COMMENT='Montagem de volume' ";
$T314Create .= " DEFAULT CHARSET=utf8; ";

$T314Sql   = mysql_query($T314Create);
$T314Error = mysql_error();
if (!empty($T314Error)) {
  echo "$T314Create<hr>$T314Error";
}

// Tabela T315 - Item do Volume
$T315Create  = " CREATE TABLE IF NOT EXISTS T315 ( ";
$T315Create .=   " T315_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$T315Create .=   " T315_T314_Id INT(10) NOT NULL, ";
$T315Create .=   " T315_T005_Id INT(10) NOT NULL, ";
$T315Create .=   " T315_T006_Id INT(10) NOT NULL, ";
$T315Create .=   " T315_D009_Id INT(10) NOT NULL, ";
$T315Create .=   " T315_Quantidade decimal(5,2) DEFAULT 0, ";
$T315Create .=   " T315_Quantidade_Conversao decimal(5,2) DEFAULT 0, ";
$T315Create .=   " T315_Peso_Bruto decimal(5,2) DEFAULT 0 COMMENT 'Peso bruto total no volume.', ";
$T315Create .=   " T315_Peso_Liquido decimal(5,2) DEFAULT 0 COMMENT 'Peso liquido total no volume.' ";
$T315Create .= " ) ";
$T315Create .= " ENGINE=InnoDB ";
$T315Create .= " COMMENT='Item do volume' ";
$T315Create .= " DEFAULT CHARSET=utf8; ";

$T315Sql   = mysql_query($T315Create);
$T315Error = mysql_error();
if (!empty($T315Error)) {
  echo "$T315Create<hr>$T315Error";
}

// Tabela C044 - Perfil de Email
$C044Create  = " CREATE TABLE IF NOT EXISTS C044 ( ";
$C044Create .=   " C044_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$C044Create .=   " C044_Descricao  VARCHAR(30)  DEFAULT NULL, ";
$C044Create .=   " C044_Observacao VARCHAR(150) DEFAULT NULL ";
$C044Create .= " ) ";
$C044Create .= " ENGINE=InnoDB ";
$C044Create .= " COMMENT='Perfil de Email' ";
$C044Create .= " DEFAULT CHARSET=utf8; ";

$C044Sql   = mysql_query($C044Create);
$C044Error = mysql_error();
if (!empty($T315Error)) {
  echo "$C044Create<hr>$C044Error";
}

$registrosC044 = array(
                  "INSERT INTO C044 (`C044_Id`,`C044_Descricao`,`C044_Observacao`) VALUES (1, 'Eventos NF-e',   'Email ao realizar um evento da NFe (carta de correção ou cancelamento)');",
                  "INSERT INTO C044 (`C044_Id`,`C044_Descricao`,`C044_Observacao`) VALUES (2, 'Divisao Pedido', 'Email ao realizar a divisão de pedidos por itens não conferidos na conferência de volumes');"
                      );
foreach ($registrosC044 as $registroC044) {
	$cSql = mysql_query($registroC044);
}

// Tabela D202 - Acessos ao Perfil de Email
$D202Create  = " CREATE TABLE IF NOT EXISTS D202 ( ";
$D202Create .=   " D202_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D202Create .=   " D202_C044_Id INT(10) DEFAULT NULL, ";
$D202Create .=   " D202_C021_Id INT(10) DEFAULT NULL, ";
$D202Create .=   " D202_C007_Id INT(10) DEFAULT NULL ";
$D202Create .= " ) ";
$D202Create .= " ENGINE=InnoDB ";
$D202Create .= " COMMENT='Acessos ao Perfil de Email' ";
$D202Create .= " DEFAULT CHARSET=utf8; ";

$D202Sql   = mysql_query($D202Create);
$D202Error = mysql_error();
if (!empty($D202Error)) {
  echo "$D202Create<hr>$D202Error";
}
// SMARTNESS - Tamba

// SMARTNESS - Moacir Ricardo
// Extensão da tabela T238 - Lote para separacao item pedido.
$t238aCreate  = " CREATE TABLE IF NOT EXISTS T238A ( ";
$t238aCreate .=   " T238A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$t238aCreate .=   " T238A_T238_Id INT(10) NOT NULL, ";
$t238aCreate .=   " T238A_Lote VARCHAR(44)COMMENT 'Lote', ";
$t238aCreate .=   " UNIQUE KEY T238A_T238_Id_IX (T238A_T238_Id) ";
$t238aCreate .= " ) ";
$t238aCreate .= " ENGINE=InnoDB ";
$t238aCreate .= " COMMENT='Extensão da tabela T238 - Separação Item pedido' ";
$t238aCreate .= " DEFAULT CHARSET=utf8; ";

$t238aSql   = mysql_query($t238aCreate);
$t238aError = mysql_error();
if (!empty($t238aError)){
  echo "$t238aCreate<hr>$t238aError";
}

/*************** Moacir Ricardo *********************/ 
Criar_Campo("D074A_Cliente_Fornece", "enum('S','N') DEFAULT 'N' COMMENT 'Indica se o Cliente fornece a MP'");
Criar_Campo("T313_Lote", "VARCHAR(20) NOT NULL COMMENT 'Lote'");
Criar_Campo("T313_Nr_Volume", "INT(10) DEFAULT '1' COMMENT 'Quantidade de volumes gerados'");
Criar_Campo("T315_Lote", "VARCHAR(20) NOT NULL COMMENT 'Lote'");
/***************************************************/


// Vinculo do Item do Pedido com o Item da OP
$t309Create  = " CREATE TABLE IF NOT EXISTS T309 ( ";
$t309Create .=   " T309_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$t309Create .=   " T309_T006_Id INT(10) NOT NULL, ";
$t309Create .=   " T309_T256_Id INT(10) NOT NULL, ";
$t309Create .=   " T309_Quantidade_Pedido  float(10,3) DEFAULT '0.00000', ";
$t309Create .=   " T309_Quantidade_Baixada float(10,3) DEFAULT '0.00000', ";
$t309Create .=   " KEY IDX_T309_T006_Id (T309_T006_Id), ";
$t309Create .=   " KEY IDX_T309_T256_Id (T309_T256_Id) ";
$t309Create .= " ) ";
$t309Create .= " ENGINE=InnoDB ";
$t309Create .= " COMMENT='Vinculo do Item do Pedido com o Item da OP' ";
$t309Create .= " DEFAULT CHARSET=utf8; ";

$t309Sql   = mysql_query($t309Create);
$t309Error = mysql_error();
if (!empty($t309Error)){
  echo "$t309Create<hr>$t309Error";
}

// Tabela cabeçalho feriados
$cClausula = <<<EOT
create table if not exists D206 (
  D206_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  D206_Descricao VARCHAR(50) DEFAULT '',
  D206_Flag_Ativo  VARCHAR(1) DEFAULT 'S'
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Tabela datas feriado
$cClausula = <<<EOT
create table if not exists D207 (
  D207_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  D207_D206_Id INT(10) NOT NULL,
  D207_Descricao_Feriado VARCHAR(50) DEFAULT '',
  D207_Data DATE DEFAULT '0000-00-00'
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// SMARTNESS - Bruno

Criar_Campo("D082_Percentual_Desconto_Clube", "decimal(10,2)");
Criar_Campo("D082_Valor_Fidelidade", "decimal(10,2)");
Criar_Campo("D082_Pontos_Fidelidade", "decimal(10,2)");

Criar_Campo("T005A_T005_Id_Original", "int COMMENT 'ID do pedido orignal que foi dividido pela conferencia de volumes'");
Criar_Campo("T005A_D047_Id_Divisao", "int COMMENT 'ID do motivo da divisao pela conferencia de volumes'");
Criar_Campo("T313_D109_Id_Primeiro", "int COMMENT 'ID do primeiro separador da conferencia'");
Criar_Campo("T313_D109_Id_Segundo", "int COMMENT 'ID do segundo separador da conferencia'");
Criar_Campo("T238A_D109_Id_Primeiro", "int COMMENT 'ID do primeiro separador da conferencia'");
Criar_Campo("T238A_D109_Id_Segundo", "int COMMENT 'ID do segundo separador da conferencia'");


// SMARTNESS - Carlos
// Tabela auxiliar de composição com campo de ordenação.
$t261aCreate  = " CREATE TABLE IF NOT EXISTS T261A ( ";
$t261aCreate .=   " T261A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$t261aCreate .=   " T261A_T261_Id INT(10) NOT NULL, ";
$t261aCreate .=   " T261A_T006_Id INT(10) NOT NULL, ";
$t261aCreate .=   " T261A_Ordem INT(10) COMMENT 'Determina a ordem da composição', ";
$t261aCreate .=   " UNIQUE KEY T261A_T261_Id_IX (T261A_T261_Id) ";
$t261aCreate .= " ) ";
$t261aCreate .= " ENGINE=InnoDB ";
$t261aCreate .= " COMMENT='Tabela auxiliar de composição com campo de ordenação.' ";
$t261aCreate .= " DEFAULT CHARSET=utf8; ";

$t261aSql   = mysql_query($t261aCreate);
$t261aError = mysql_error();
if (!empty($t261aError)){
  echo "$t261aCreate<hr>$t261aError";
}

Alter_Query("ALTER TABLE T313 CHANGE T313_Quantidade T313_Quantidade decimal(14,4);");
Alter_Query("ALTER TABLE T315 CHANGE T315_Quantidade_Conversao T315_Quantidade_Conversao decimal(14,4);");
Alter_Query("ALTER TABLE T315 CHANGE T315_Quantidade T315_Quantidade decimal(14,4);");
Alter_Query("ALTER TABLE T315 CHANGE T315_Peso_Liquido T315_Peso_Liquido decimal(14,4);");
Alter_Query("ALTER TABLE T315 CHANGE T315_Peso_Bruto T315_Peso_Bruto decimal(14,4);");

// Tabela auxiliar de documentos do produto.
$t167aCreate  = " CREATE TABLE IF NOT EXISTS T167A ( ";
$t167aCreate .=   " T167A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$t167aCreate .=   " T167A_T167_Id INT(10) NOT NULL, ";
$t167aCreate .=   " T167A_T006_Id INT(10) NOT NULL, ";
$t167aCreate .=   " T167A_Revisao varchar(20) COMMENT 'Revisão do documento', ";
$t167aCreate .=   " UNIQUE KEY T167A_T167_Id_IX (T167A_T167_Id) ";
$t167aCreate .= " ) ";
$t167aCreate .= " ENGINE=InnoDB ";
$t167aCreate .= " COMMENT='Tabela auxiliar de documentos do produto.' ";
$t167aCreate .= " DEFAULT CHARSET=utf8; ";

$t167aSql   = mysql_query($t167aCreate);
$t167aError = mysql_error();
if (!empty($t167aError)){
  echo "$t167aCreate<hr>$t167aError";
}

// Cadastro itens ao importar XML DI
$cClausula = <<<EOT
create table if not exists T310 (
  T310_Id                           INT(10) PRIMARY KEY AUTO_INCREMENT,  
  T310_T075_Id                      INT(10),
  T310_T055_Id                      INT(10),
  T310_Classificacao_Fiscal         varchar(12) default '',
  T310_Valor_Adicao                 varchar(10) default '',
  T310_Adicao_Item                  varchar(5) default '',
  T310_Descricao_Produto            text,
  T310_Quantidade decimal(12,5)     default '0.00',
  T310_Valor_Custo_Unitario         decimal(20,7) default '0.00',
  T310_Aliquota_COFINS              decimal(5,2) default '0.00',
  T310_Aliquota_Imposto_Importacao  decimal(5,2) default '0.00',
  T310_Aliquota_IPI                 decimal(5,2) default '0.00',
  T310_Aliquota_PIS                 decimal(5,2) default '0.00',
  KEY IDX_T310_T075_Id (T310_T075_Id),
  KEY IDX_T310_T055_Id (T310_T055_Id)

)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql  = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Separacao de itens de engenharia
$cClausula = <<<EOT
create table if not exists T311 (
  T311_Id                    INT(10) PRIMARY KEY AUTO_INCREMENT,  
  T311_T006_Id               INT(10),
  T311_D074_Id               INT(10),
  T311_Quantidade_Separada   DECIMAL(12,5) default '0.00',
  T311_Quantidade_A_Separar  DECIMAL(12,5) default '0.00',
  KEY IDX_T311_T006_Id (T311_T006_Id),
  KEY IDX_T311_D074_Id (T311_D074_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql  = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Motivo RMA
$cClausula = <<<EOT
create table if not exists D197 (
  D197_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  D197_Motivo VARCHAR(40) NOT NULL,
  D197_D164_Id INT,
  KEY IDX_D197_D164_Id (D197_D164_Id)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}


// Custo RMA
$cClausula = <<<EOT
create table if not exists T312 (
  T312_Id                           INT(10) PRIMARY KEY AUTO_INCREMENT,  
  T312_Valor                        decimal(9,3) NOT NULL default '0.00',
  T312_T182_Id                      INT,  
  KEY IDX_T312_T182_Id (T312_T182_Id)  
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql  = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Tipo Custo RMA
$cClausula = <<<EOT
create table if not exists D198 (
  D198_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  D198_Descricao VARCHAR(70) NOT NULL
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Origem Atendimento RMA
$cClausula = <<<EOT
create table if not exists D199 (
  D199_Id INT(10) PRIMARY KEY AUTO_INCREMENT,  
  D199_Descricao VARCHAR(70) NOT NULL
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Pessoas autorizadas a ter acesso ao XML
$cClausula = <<<EOT
create table if not exists C043 (
  C043_Id               INT(10) PRIMARY KEY AUTO_INCREMENT,  
  C043_C004_Id          INT(10),
  C043_Nome_Autorizado  VARCHAR(120) NOT NULL,
  C043_CPF              VARCHAR(20) DEFAULT '',
  C043_CNPJ             VARCHAR(20) DEFAULT ''
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// TABELA ESTENDIDA T084A
$cClausula  = <<<EOT
create table if not exists T084A (  
    T084A_Id                           INT(10) UNSIGNED AUTO_INCREMENT,
    T084A_T084_Id                      INT(10) UNSIGNED,
    T084A_Flag_Empresa_Tomadora        VARCHAR(1) DEFAULT '',                       
    PRIMARY KEY (T084A_Id),
    UNIQUE KEY IDX_T084A_T084_Id (T084A_T084_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}


// Tabela registros CIAP (Bens)
$cClausula = <<<EOT
create table if not exists T316 (
  T316_Id                              INT(10) PRIMARY KEY AUTO_INCREMENT,  
  T316_T014_Id                         INT,
  T316_Data_Inicio_Apropriacao_Credito DATE DEFAULT '0000-00-00', 
  T316_Data_Fim_Apropriacao_Credito    DATE DEFAULT '0000-00-00', 
  T316_Numero_Parcelas_Apropriacao     INT DEFAULT 0, 
  T316_Flag_Ativo_Baixado              VARCHAR(1) DEFAULT 'N', 
  T316_Descricao_Utilizacao_Bem        VARCHAR(60) DEFAULT '',
  KEY IDX_T316_T014_Id (T316_T014_Id),  
  KEY IDX_T316_Data_Inicio_Apropriacao_Credito (T316_Data_Inicio_Apropriacao_Credito),  
  KEY IDX_T316_Data_Fim_Apropriacao_Credito (T316_Data_Fim_Apropriacao_Credito),  
  KEY IDX_T316_Flag_Ativo_Baixado (T316_Flag_Ativo_Baixado)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;
$cSql  = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

/* 
Canal prospecção clientes, ou seja, classifica o cliente por qual rede
ou meio de comunicação ele encontrou a empresa
*/
$cClausula = <<<EOT
create table if not exists D201 (
  D201_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D201_Descricao VARCHAR(300)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Estendida T289
$cClausula  = <<<EOT
create table if not exists T289A (  
    T289A_Id                           INT(10) UNSIGNED AUTO_INCREMENT,
    T289A_T289_Id                      INT(10) UNSIGNED,
    T289A_T008_Id                      INT(10) UNSIGNED,
    PRIMARY KEY (T289A_Id),
    UNIQUE KEY IDX_T289A_T289_Id (T289A_T289_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Estendida D085
$cClausula  = <<<EOT
create table if not exists D085A (  
    D085A_Id                           INT(10) UNSIGNED AUTO_INCREMENT,
    D085A_D085_Id                      INT(10) UNSIGNED,
    D085A_C004_Id                      INT(10) UNSIGNED,
    PRIMARY KEY (D085A_Id),
    UNIQUE KEY IDX_D085A_D085_Id (D085A_D085_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//Verifica se os acessos antigos foram removidos, caso contrario atualiza os acessos
$sqlC029 = mysql_query("SELECT * FROM C029 WHERE C029_Id = '169'");
$rowC029 = mysql_fetch_array($sqlC029);
if($rowC029['C029_Descricao_Acesso'] != 'Vendas - Notas Fiscais'){
  mysql_query("TRUNCATE C029");
}

Criar_Campo("C029_Flag_Tipo","char(1) NOT NULL DEFAULT 'M'");
Criar_Campo("C029_Flag_Modulo","varchar(2) DEFAULT NULL");
/* INÍCIO PROJETO GNRE — Task 02: cadastro manual de rascunhos */
// O modelo guarda somente os dados que o operador informa para identificar a
// guia manual. Transmissão, XML, fila, PDF e configurações de UF não fazem
// parte destas tabelas.
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE001 (
GNRE001_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE001_C004_Id int unsigned NOT NULL COMMENT 'Empresa proprietária da guia',
GNRE001_GNRE007_Id int unsigned DEFAULT NULL COMMENT 'Snapshot imutável TConfigUf escolhido conscientemente para esta guia',
GNRE001_GNRE001_Id_Origem_Correcao int unsigned DEFAULT NULL COMMENT 'Guia rejeitada que originou este rascunho de correção; nunca representa retransmissão',
GNRE001_Modalidade enum('INDIVIDUAL','APURACAO') NOT NULL COMMENT 'Forma operacional da guia manual',
GNRE001_Status enum('R','C','P','I','F') NOT NULL DEFAULT 'R' COMMENT 'R=rascunho; C=cancelada; P=recibo pendente; I=transmissão inconclusiva; F=resultado final consultado',
GNRE001_Arquivada CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'S=guia arquivada sem apagar histórico técnico',
GNRE001_Numero_Controle varchar(20) DEFAULT NULL COMMENT 'Projeção operacional do numeroControle oficial de resultado individual autorizado',
GNRE001_UF_Favorecida char(2) NOT NULL COMMENT 'UF favorecida informada manualmente',
GNRE001_Tipo_GNRE char(1) DEFAULT NULL COMMENT 'Tipo oficial GNRE do XML (0, 1 ou 2); validado futuramente contra o XSD e TConfigUf',
GNRE001_Codigo_Receita varchar(20) NOT NULL DEFAULT '' COMMENT 'Vazio somente enquanto rascunho sem receita informada',
GNRE001_Detalhamento_Receita varchar(50) DEFAULT NULL COMMENT 'Detalhamento de receita, quando conhecido',
GNRE001_Data_Vencimento date DEFAULT NULL COMMENT 'Vencimento manual; nulo enquanto rascunho incompleto',
GNRE001_Valor decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Zero somente enquanto rascunho sem valores informados',
GNRE001_Referencia varchar(50) DEFAULT NULL COMMENT 'Referência ou competência livre',
GNRE001_Observacao varchar(1000) DEFAULT NULL COMMENT 'Observação livre da guia',
GNRE001_Data_Pagamento date DEFAULT NULL COMMENT 'Data de pagamento global da guia no XML GNRE',
GNRE001_Data_Hora_Criacao datetime NOT NULL,
GNRE001_C007_Id_Criacao int NOT NULL COMMENT 'Usuário ERP que criou a guia',
GNRE001_Data_Hora_Alteracao datetime DEFAULT NULL,
GNRE001_C007_Id_Alteracao int DEFAULT NULL COMMENT 'Usuário ERP que alterou o rascunho',
GNRE001_Data_Hora_Cancelamento datetime DEFAULT NULL,
GNRE001_C007_Id_Cancelamento int DEFAULT NULL COMMENT 'Usuário ERP que cancelou o rascunho',
GNRE001_Motivo_Cancelamento varchar(500) DEFAULT NULL,
PRIMARY KEY (GNRE001_Id),
KEY IDX_GNRE001_C004_Id_Status (GNRE001_C004_Id,GNRE001_Status),
KEY IDX_GNRE001_EMPRESA_ARQUIVADA (GNRE001_C004_Id,GNRE001_Arquivada),
UNIQUE KEY UQ_GNRE001_Empresa_Numero_Controle (GNRE001_C004_Id,GNRE001_Numero_Controle),
KEY IDX_GNRE001_GNRE007_Id (GNRE001_GNRE007_Id),
KEY IDX_GNRE001_Origem_Correcao_Status (GNRE001_GNRE001_Id_Origem_Correcao,GNRE001_Status),
KEY IDX_GNRE001_C004_Id_Data_Vencimento (GNRE001_C004_Id,GNRE001_Data_Vencimento)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Guias GNRE cadastradas manualmente';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 17: versão oficial imutável por empresa, ambiente, UF e conteúdo.
   Este CREATE descreve somente instalações novas. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE007 (
GNRE007_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE007_C004_Id int unsigned NOT NULL COMMENT 'Empresa/CNPJ cujo certificado consultou o Portal',
GNRE007_Ambiente char(1) NOT NULL COMMENT 'Ambiente informado na TConfigUf (homologação=2)',
GNRE007_UF char(2) NOT NULL COMMENT 'UF favorecida da configuração oficial',
GNRE007_XML mediumtext NOT NULL COMMENT 'TConfigUf bruto validado no XSD oficial',
GNRE007_Regras_JSON mediumtext NOT NULL COMMENT 'Interpretação Task 16 do XML validado',
GNRE007_XML_SHA256 char(64) NOT NULL COMMENT 'SHA-256 do XML bruto',
GNRE007_Data_Hora_Consulta datetime NOT NULL,
GNRE007_C007_Id_Consulta int DEFAULT NULL COMMENT 'Usuário que solicitou a consulta manual',
GNRE007_Origem varchar(30) NOT NULL DEFAULT 'PORTAL_MANUAL' COMMENT 'Origem técnica auditável',
PRIMARY KEY (GNRE007_Id),
UNIQUE KEY UQ_GNRE007_Empresa_Ambiente_UF_Hash (GNRE007_C004_Id,GNRE007_Ambiente,GNRE007_UF,GNRE007_XML_SHA256),
KEY IDX_GNRE007_Localizacao (GNRE007_C004_Id,GNRE007_Ambiente,GNRE007_UF,GNRE007_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Snapshots imutáveis da configuração oficial TConfigUf';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE002 (
GNRE002_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE002_GNRE001_Id int unsigned NOT NULL COMMENT 'Guia manual relacionada',
GNRE002_T007_Id int unsigned DEFAULT NULL COMMENT 'NF de origem vinculada manualmente no Escopo 3 da Task 60',
GNRE002_Tributo_Origem varchar(10) DEFAULT NULL COMMENT 'Componente temporário da NF de origem: ST, DIFAL ou FCP',
GNRE002_Identificacao varchar(100) NOT NULL COMMENT 'Identificação livre do documento de origem',
GNRE002_Descricao varchar(500) DEFAULT NULL COMMENT 'Descrição livre do documento de origem',
GNRE002_Data_Hora_Criacao datetime NOT NULL,
PRIMARY KEY (GNRE002_Id),
KEY IDX_GNRE002_GNRE001_Id (GNRE002_GNRE001_Id),
UNIQUE KEY UQ_GNRE002_NF_TRIBUTO (GNRE002_T007_Id,GNRE002_Tributo_Origem)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Documentos de origem vinculados manualmente à GNRE';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 05: cada transmissão é imutável para auditoria e para impedir reenvio
   automático após resposta ambígua. Não armazena certificado, senha ou chave. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE004 (
GNRE004_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE004_GNRE001_Id int unsigned NOT NULL COMMENT 'Guia transmitida',
GNRE004_Sequencia smallint unsigned NOT NULL COMMENT 'Tentativa sequencial por guia',
GNRE004_XML_Enviado mediumtext NOT NULL COMMENT 'XML de lote validado antes do envio',
GNRE004_XML_SHA256 char(64) NOT NULL COMMENT 'Hash SHA-256 do XML enviado',
GNRE004_Data_Hora_Criacao datetime NOT NULL,
GNRE004_Data_Hora_Envio datetime DEFAULT NULL,
GNRE004_C007_Id_Solicitante int NOT NULL COMMENT 'Usuário ERP que solicitou a transmissão',
GNRE004_HTTP_Status smallint unsigned DEFAULT NULL,
GNRE004_Codigo_Retorno varchar(10) DEFAULT NULL,
GNRE004_Descricao_Retorno varchar(255) DEFAULT NULL,
GNRE004_Recibo varchar(20) DEFAULT NULL,
GNRE004_Data_Hora_Recibo varchar(40) DEFAULT NULL,
GNRE004_Tempo_Estimado_Processamento_MS int DEFAULT NULL,
GNRE004_Resposta_SOAP mediumtext DEFAULT NULL,
GNRE004_Erro_Tecnico text DEFAULT NULL,
GNRE004_Estado enum('CRIADA','RECEBIDA','REJEITADA','INCONCLUSIVA') NOT NULL COMMENT 'Estado imutável da tentativa de recepção',
PRIMARY KEY (GNRE004_Id),
UNIQUE KEY UQ_GNRE004_Guia_Sequencia (GNRE004_GNRE001_Id,GNRE004_Sequencia),
KEY IDX_GNRE004_GNRE001_Id_Data (GNRE004_GNRE001_Id,GNRE004_Data_Hora_Criacao)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Auditoria das tentativas de transmissão GNRE';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 74: cabeçalho persistente do futuro lote físico GNRE. A criação local
   ainda prepara somente a coorte; campos de transporte/consulta ficam NULL
   até task própria. Não armazena PDF coletivo. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE010 (
GNRE010_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE010_C004_Id int unsigned NOT NULL COMMENT 'Empresa GNRE proprietária do lote',
GNRE010_GNRE007_Id int unsigned NOT NULL COMMENT 'Snapshot oficial imutável comum às participantes',
GNRE010_UF_Favorecida char(2) NOT NULL COMMENT 'UF favorecida homogênea do lote',
GNRE010_Ambiente char(1) NOT NULL COMMENT 'Ambiente exclusivamente do GNRE007 vinculado',
GNRE010_Layout_Versao varchar(20) NOT NULL COMMENT 'Versão do layout oficial aplicável ao lote',
GNRE010_Fingerprint_Versao varchar(64) NOT NULL COMMENT 'Versão do contrato de correlação',
GNRE010_Chave_Idempotencia varchar(64) DEFAULT NULL COMMENT 'Chave persistida antes do futuro envio físico; NULL em coorte local legada',
GNRE010_XML_SHA256 char(64) DEFAULT NULL COMMENT 'SHA-256 do XML físico congelado do lote',
GNRE010_XML_Fisico mediumtext DEFAULT NULL COMMENT 'XML físico congelado do lote, quando futuro envio o gerar',
GNRE010_Data_Hora_Envio datetime DEFAULT NULL COMMENT 'Horário da única chamada física futura',
GNRE010_C007_Id_Solicitante_Envio int DEFAULT NULL COMMENT 'Usuário que solicitará o futuro envio',
GNRE010_HTTP_Recepcao_Status smallint unsigned DEFAULT NULL COMMENT 'HTTP da recepção futura',
GNRE010_SOAP_Recepcao mediumtext DEFAULT NULL COMMENT 'SOAP bruto da recepção futura',
GNRE010_Mensagem_Tecnica text DEFAULT NULL COMMENT 'Erro ou mensagem técnica de recepção/recuperação, sem segredo',
GNRE010_Recibo varchar(20) DEFAULT NULL COMMENT 'Recibo compartilhado retornado pelo Portal',
GNRE010_Data_Hora_Recibo varchar(40) DEFAULT NULL COMMENT 'Data/hora do recibo preservada do retorno oficial',
GNRE010_Situacao enum('PREPARADO','ENVIANDO','RECEBIDO','PENDENTE','FINALIZADO','INCONCLUSIVO','REJEITADO','CANCELADO') NOT NULL DEFAULT 'PREPARADO',
GNRE010_Proxima_Tentativa_Consulta datetime DEFAULT NULL COMMENT 'Próxima consulta futura do recibo compartilhado',
GNRE010_Consulta_Lock_Token char(64) DEFAULT NULL COMMENT 'Lease futuro da consulta do lote',
GNRE010_Consulta_Lock_Ate datetime DEFAULT NULL COMMENT 'Validade do lease futuro da consulta do lote',
GNRE010_Ultima_Consulta_SOAP mediumtext DEFAULT NULL COMMENT 'Último SOAP futuro de consulta do recibo',
GNRE010_Data_Hora_Ultima_Consulta datetime DEFAULT NULL COMMENT 'Horário da última consulta futura do recibo',
GNRE010_Data_Hora_Criacao datetime NOT NULL,
PRIMARY KEY (GNRE010_Id),
UNIQUE KEY UQ_GNRE010_Empresa_Idempotencia (GNRE010_C004_Id,GNRE010_Chave_Idempotencia),
KEY IDX_GNRE010_Empresa_Situacao (GNRE010_C004_Id,GNRE010_Situacao),
KEY IDX_GNRE010_Snapshot (GNRE010_GNRE007_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lotes físicos GNRE futuros; criação atual permanece somente local';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE011 (
GNRE011_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE011_GNRE010_Id int unsigned NOT NULL COMMENT 'Lote GNRE proprietário',
GNRE011_GNRE001_Id int unsigned NOT NULL COMMENT 'Guia participante',
GNRE011_Fragmento_Canonico mediumtext NOT NULL COMMENT 'Fragmento TDadosGNRE canônico de auditoria congelado',
GNRE011_Fragmento_SHA256 char(64) NOT NULL COMMENT 'SHA-256 do fragmento canônico',
GNRE011_Fingerprint_Negocio char(64) NOT NULL COMMENT 'SHA-256 de negócio usado exclusivamente para correlação futura',
GNRE011_Fingerprint_Versao varchar(64) NOT NULL COMMENT 'Versão do vetor de correlação',
GNRE011_Ordinal_Envio smallint unsigned DEFAULT NULL COMMENT 'Ordinal apenas de auditoria; nunca critério de correlação',
GNRE011_Situacao enum('PREPARADO','PENDENTE','FINALIZADO','INCONCLUSIVO','REJEITADO') NOT NULL DEFAULT 'PREPARADO' COMMENT 'Ciclo individual futuro da participante',
GNRE011_Situacao_Guia char(1) DEFAULT NULL COMMENT 'situacaoGuia oficial individual futura; 0 é a única confirmação de autorização',
GNRE011_Resultado_Individual mediumtext DEFAULT NULL COMMENT 'Resultado individual bruto/projetado futuro',
GNRE011_Motivos_Rejeicao mediumtext DEFAULT NULL COMMENT 'Motivos individuais futuros de rejeição',
GNRE011_Decisao_Correlacao varchar(32) DEFAULT NULL COMMENT 'MATCHED, UNMATCHED, AMBIGUOUS ou INSUFFICIENT_EVIDENCE',
GNRE011_Candidatos_Correlacao mediumtext DEFAULT NULL COMMENT 'Auditoria futura dos candidatos de correlação',
GNRE011_Data_Hora_Decisao datetime DEFAULT NULL COMMENT 'Horário futuro da decisão individual persistida',
GNRE011_Data_Hora_Criacao datetime NOT NULL,
PRIMARY KEY (GNRE011_Id),
UNIQUE KEY UQ_GNRE011_Coorte_Guia (GNRE011_GNRE010_Id,GNRE011_GNRE001_Id),
KEY IDX_GNRE011_Guia (GNRE011_GNRE001_Id),
KEY IDX_GNRE011_Lote (GNRE011_GNRE010_Id),
KEY IDX_GNRE011_Lote_Situacao (GNRE011_GNRE010_Id,GNRE011_Situacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Participantes imutáveis de lote GNRE real';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 25: o binário oficial não se mistura com XML/SOAP das trilhas GNRE004/5. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE008 (
GNRE008_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE008_C004_Id int unsigned NOT NULL COMMENT 'Empresa autorizada proprietária do documento',
GNRE008_GNRE001_Id int unsigned NOT NULL COMMENT 'Guia aprovada',
GNRE008_GNRE004_Id int unsigned NOT NULL COMMENT 'Tentativa de recepção que originou o recibo',
GNRE008_Recibo varchar(20) NOT NULL COMMENT 'Recibo consultado no Portal',
GNRE008_Tipo varchar(20) NOT NULL COMMENT 'PDF_OFICIAL',
GNRE008_Origem varchar(20) NOT NULL COMMENT 'PORTAL_GNRE',
GNRE008_Conteudo mediumblob NOT NULL COMMENT 'PDF original retornado pelo Portal Nacional',
GNRE008_Mime varchar(100) NOT NULL COMMENT 'application/pdf',
GNRE008_Tamanho int unsigned NOT NULL COMMENT 'Tamanho do binário em bytes',
GNRE008_SHA256 char(64) NOT NULL COMMENT 'SHA-256 do binário original',
GNRE008_Data_Hora_Obtencao datetime NOT NULL,
GNRE008_C007_Id_Solicitante int DEFAULT NULL COMMENT 'Usuário que solicitou a obtenção',
PRIMARY KEY (GNRE008_Id),
UNIQUE KEY UQ_GNRE008_Tentativa_Hash (GNRE008_GNRE004_Id,GNRE008_SHA256),
KEY IDX_GNRE008_Empresa_Guia (GNRE008_C004_Id,GNRE008_GNRE001_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='PDF oficial imutável retornado pelo Portal Nacional GNRE';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 38: preferência preparatória; não muda ambiente operacional. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE009 (
GNRE009_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE009_C004_Id int unsigned NOT NULL,
GNRE009_Ambiente char(1) NOT NULL DEFAULT '2',
PRIMARY KEY (GNRE009_Id),
UNIQUE KEY UQ_GNRE009_Empresa (GNRE009_C004_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 04: cada linha representa um item de recolhimento do XML, e não um
   produto ou uma linha de NF-e. GNRE002 continua sendo apenas o documento de
   origem; uma guia pode ter vários itens e um documento pode originar vários
   recolhimentos. Não há XML, recibo, fila ou dados específicos por UF aqui. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE003 (
GNRE003_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE003_GNRE001_Id int unsigned NOT NULL COMMENT 'Guia GNRE proprietária do item de recolhimento',
GNRE003_GNRE002_Id int unsigned DEFAULT NULL COMMENT 'Documento de origem opcional do item de recolhimento',
GNRE003_Codigo_Receita varchar(6) NOT NULL COMMENT 'Receita do item no XML GNRE',
GNRE003_Detalhamento_Receita varchar(6) DEFAULT NULL COMMENT 'Detalhamento da receita do item no XML GNRE',
GNRE003_Tipo_Valor char(2) NOT NULL DEFAULT '11' COMMENT 'Tipo de valor GNRE: 11 principal, 12 FECP, 21 total, etc.',
GNRE003_Valor decimal(15,2) NOT NULL COMMENT 'Valor do item de recolhimento',
GNRE003_Tipo_Documento_Origem char(2) DEFAULT NULL COMMENT 'Tipo de documento de origem exigido pelo XML',
GNRE003_Codigo_Produto varchar(4) DEFAULT NULL COMMENT 'Código de produto GNRE, quando aplicável',
GNRE003_Referencia_Periodo char(1) DEFAULT NULL COMMENT '0 mensal, 1/2 quinzena, 3/4/5 decêndio',
GNRE003_Referencia_Mes char(2) DEFAULT NULL COMMENT 'Mês de referência',
GNRE003_Referencia_Ano char(4) DEFAULT NULL COMMENT 'Ano de referência',
GNRE003_Referencia_Parcela varchar(3) DEFAULT NULL COMMENT 'Parcela de referência',
GNRE003_Data_Vencimento date DEFAULT NULL COMMENT 'Vencimento do item no XML GNRE',
GNRE003_Convenio varchar(30) DEFAULT NULL COMMENT 'Número do convênio do item no XML GNRE (XSD: TString, máximo 30)',
GNRE003_Destinatario_Tipo_Identificacao enum('CNPJ','CPF','IE') DEFAULT NULL COMMENT 'Tipo da identificação do destinatário',
GNRE003_Destinatario_Identificacao varchar(16) DEFAULT NULL COMMENT 'CNPJ, CPF ou IE do destinatário',
GNRE003_Destinatario_Razao_Social varchar(60) DEFAULT NULL COMMENT 'Nome ou razão social do destinatário',
GNRE003_Destinatario_Codigo_Municipio char(5) DEFAULT NULL COMMENT 'Código IBGE de cinco dígitos exigido pelo XML GNRE',
GNRE003_Campos_Extras_JSON text COMMENT 'Lista JSON genérica de codigo/valor de campos extras GNRE',
GNRE003_Data_Hora_Criacao datetime NOT NULL,
GNRE003_C007_Id_Criacao int NOT NULL COMMENT 'Usuário ERP que criou o item',
GNRE003_Data_Hora_Alteracao datetime DEFAULT NULL,
GNRE003_C007_Id_Alteracao int DEFAULT NULL COMMENT 'Usuário ERP que alterou o item',
PRIMARY KEY (GNRE003_Id),
KEY IDX_GNRE003_GNRE001_Id (GNRE003_GNRE001_Id),
KEY IDX_GNRE003_GNRE002_Id (GNRE003_GNRE002_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Itens de recolhimento para geração local do XML GNRE';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE006 (
GNRE006_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE006_GNRE003_Id int unsigned NOT NULL COMMENT 'Item de recolhimento proprietário do valor tributário',
GNRE006_Tipo_Valor char(2) NOT NULL COMMENT 'Tipo de valor XML GNRE: 11, 12, 21, 22, 31, 32, 41, 42, 51 ou 52',
GNRE006_Valor decimal(15,2) NOT NULL COMMENT 'Valor tributário do item no XML GNRE',
GNRE006_Data_Hora_Criacao datetime NOT NULL,
GNRE006_C007_Id_Criacao int NOT NULL COMMENT 'Usuário ERP que criou o valor',
GNRE006_Data_Hora_Alteracao datetime DEFAULT NULL,
GNRE006_C007_Id_Alteracao int DEFAULT NULL COMMENT 'Usuário ERP que alterou o valor',
PRIMARY KEY (GNRE006_Id),
KEY IDX_GNRE006_GNRE003_Id (GNRE006_GNRE003_Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Valores tributários por item de recolhimento GNRE';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* Task 06: fila própria de consulta de recibos. A chave única impede que a
   mesma tentativa recebida gere duas consultas, inclusive ao reaplicar a base. */
$cClausula = <<<EOT
CREATE TABLE IF NOT EXISTS GNRE005 (
GNRE005_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE005_GNRE004_Id int unsigned NOT NULL COMMENT 'Tentativa de recepção que forneceu o recibo',
GNRE005_Recibo varchar(20) NOT NULL COMMENT 'Recibo GNRE a consultar',
GNRE005_Situacao enum('PENDENTE','PROCESSANDO','FINALIZADA','BLOQUEADA') NOT NULL DEFAULT 'PENDENTE',
GNRE005_Proxima_Tentativa datetime NOT NULL,
GNRE005_Tentativas int unsigned NOT NULL DEFAULT 0,
GNRE005_Lock_Token char(64) DEFAULT NULL COMMENT 'Token aleatório do worker que detém o lease',
GNRE005_Lock_Ate datetime DEFAULT NULL COMMENT 'Lease temporário; item volta a ser elegível depois dele',
GNRE005_Data_Hora_Processamento datetime DEFAULT NULL,
GNRE005_Erro_Tecnico text DEFAULT NULL,
GNRE005_Ultima_Consulta_HTTP smallint unsigned DEFAULT NULL,
GNRE005_Ultima_Consulta_Codigo varchar(10) DEFAULT NULL,
GNRE005_Ultima_Consulta_Descricao varchar(255) DEFAULT NULL,
GNRE005_Ultima_Consulta_SOAP mediumtext DEFAULT NULL,
GNRE005_Data_Hora_Ultima_Consulta datetime DEFAULT NULL,
GNRE005_Data_Hora_Criacao datetime NOT NULL,
GNRE005_Data_Hora_Alteracao datetime DEFAULT NULL,
PRIMARY KEY (GNRE005_Id),
UNIQUE KEY UQ_GNRE005_GNRE004_Id (GNRE005_GNRE004_Id),
KEY IDX_GNRE005_Execucao (GNRE005_Situacao,GNRE005_Proxima_Tentativa,GNRE005_Lock_Ate),
KEY IDX_GNRE005_Recibo (GNRE005_Recibo)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Fila persistente GNRE de consulta de recibos';
EOT;
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
}
/* FIM PROJETO GNRE */

$registroC029 = <<<EOT
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (1,'Cliente/Fornecedor',NULL,NULL,NULL,'Cadastros',NULL,'N','15');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (2,'Transportadoras',NULL,NULL,NULL,'Cadastros',NULL,'N','13');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (3,'Representantes',NULL,NULL,NULL,'Cadastros',NULL,'N','13');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (4,'Agenda de Contatos',NULL,NULL,NULL,'Cadastros',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (5,'Produtos',NULL,NULL,NULL,'Cadastros',NULL,'N','10');
delete from C029 WHERE C029_Id = '6';
delete from C029 WHERE C029_Id = '7';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (8,'Bancos/Caixas',NULL,NULL,NULL,'Cadastros',NULL,'N', '11');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (9,'Centro de Custos',NULL,NULL,NULL,'Cadastros',NULL,'N', '11');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (10,'Funcionários',NULL,NULL,NULL,'Cadastros',NULL,'N');
delete from C029 WHERE C029_Id = '11';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (12,'Plano de Contas',NULL,NULL,NULL,'Cadastros',NULL,'N','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (13,'Emails',NULL,NULL,NULL,'Vendas',NULL,'N');
delete from C029 WHERE C029_Id = '14';
delete from C029 WHERE C029_Id = '15';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (16,'Notas Fiscais',NULL,NULL,NULL,'Vendas',NULL,'N','1');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (200,'Notas Fiscais - Completo',NULL,NULL,NULL,'Vendas - CRM',NULL,'N','1');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (17,'Emitir Notas Fiscais',NULL,NULL,NULL,'Vendas',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (18,'Análise de Orçamento',NULL,NULL,NULL,'Vendas',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (19,'Divergências de Estoque',NULL,NULL,NULL,'Estoque - Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (20,'Expedição Mercadorias',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (21,'Notas Fiscais Recebidas',NULL,NULL,NULL,'Estoque',NULL,'N','3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (22,'Conhecimentos',NULL,NULL,NULL,'Estoque',NULL,'N','3');
delete from C029 WHERE C029_Id = '23';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (24,'Montagens/Desmontagens',NULL,NULL,NULL,'Estoque - Estoque',NULL,'N');
delete from C029 WHERE C029_Id = '25';
delete from C029 WHERE C029_Id = '26';
delete from C029 WHERE C029_Id = '27';
delete from C029 WHERE C029_Id = '28';
delete from C029 WHERE C029_Id = '29';
delete from C029 WHERE C029_Id = '30';
delete from C029 WHERE C029_Id = '31';
delete from C029 WHERE C029_Id = '32';
delete from C029 WHERE C029_Id = '33';
delete from C029 WHERE C029_Id = '34';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (35,'Contas a Receber',NULL,NULL,NULL,'Financeiro',NULL,'N','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (36,'Contas a Pagar',NULL,NULL,NULL,'Financeiro',NULL,'N','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (37,'Caixa e Bancos',NULL,NULL,NULL,'Financeiro',NULL,'N','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (38,'Boleto Bancário',NULL,NULL,NULL,'Financeiro - Contas a Receber',NULL,'N','5');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (39,'Análise de Crédito',NULL,NULL,NULL,'Financeiro',NULL,'N');
delete from C029 WHERE C029_Id = '40';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (41,'CFOP',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (242,'MDF-e',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (42,'NCM',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (43,'ICMS',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (44,'NCM por Grupo',NULL,NULL,NULL,'Fiscal',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (45,'Cidades',NULL,NULL,NULL,'Fiscal',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (46,'Exportar Sintegra',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (47,'Exportar XML NF-e',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (48,'Exportar TXT NF-e',NULL,NULL,NULL,'Fiscal',NULL,'N','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (49,'DI - Declaração Importação',NULL,NULL,NULL,'Importação',NULL,'N');
delete from C029 WHERE C029_Id = '50';
delete from C029 WHERE C029_Id = '51';
delete from C029 WHERE C029_Id = '52';
delete from C029 WHERE C029_Id = '53';
delete from C029 WHERE C029_Id = '54';
delete from C029 WHERE C029_Id = '55';
delete from C029 WHERE C029_Id = '56';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (57,'Empresas',NULL,NULL,NULL,'Configuração',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (58,'DI',NULL,NULL,NULL,'Configuração',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (246,'Periféricos',NULL,NULL,NULL,'Configuração',NULL,'N','0');
delete from C029 WHERE C029_Id = '59';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (60,'Pagar - Portadores',NULL,NULL,NULL,'Configuração - Financeiro',NULL,'N');
delete from C029 WHERE C029_Id = '61';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (62,'Receber - Portadores',NULL,NULL,NULL,'Configuração - Financeiro',NULL,'N','12');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (63,'Produtos',NULL,NULL,NULL,'Configuração',NULL,'N','10');
delete from C029 WHERE C029_Id = '64';
delete from C029 WHERE C029_Id = '65';
delete from C029 WHERE C029_Id = '66';
delete from C029 WHERE C029_Id = '67';
delete from C029 WHERE C029_Id = '68';
delete from C029 WHERE C029_Id = '69';
delete from C029 WHERE C029_Id = '70';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (71,'Motivos Cancelamento',NULL,NULL,NULL,'Configuração - Comercial',NULL,'N','0');
delete from C029 WHERE C029_Id = '72';
delete from C029 WHERE C029_Id = '73';
delete from C029 WHERE C029_Id = '74';
delete from C029 WHERE C029_Id = '75';
delete from C029 WHERE C029_Id = '76';
delete from C029 WHERE C029_Id = '77';
delete from C029 WHERE C029_Id = '78';
delete from C029 WHERE C029_Id = '79';
delete from C029 WHERE C029_Id = '80';
delete from C029 WHERE C029_Id = '81';
delete from C029 WHERE C029_Id = '82';
delete from C029 WHERE C029_Id = '83';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (84,'Usuários/Perfis',NULL,NULL,NULL,'Configuração',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (85,'Países',NULL,NULL,NULL,'Fiscal',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (86,'Usuário',NULL,NULL,NULL,'Configuração',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (87,'Faturar Emergência','Vendas - Pedido','A');
delete from C029 WHERE C029_Id = '88';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (89,'Simples Nacional',NULL,NULL,NULL,'Fiscal',NULL,'N');
delete from C029 WHERE C029_Id = '90';
delete from C029 WHERE C029_Id = '91';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (92,'Tipos de Transporte',NULL,NULL,NULL,'Configuração - Comercial',NULL,'N','0');
delete from C029 WHERE C029_Id = '93';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (94,'Compras de Estoque',NULL,NULL,NULL,'Estoque - SRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (95,'Compras de Emergência',NULL,NULL,NULL,'Estoque - SRM',NULL,'N');
delete from C029 WHERE C029_Id = '96';
delete from C029 WHERE C029_Id = '97';
delete from C029 WHERE C029_Id = '98';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (99,'Análise de Pedido',NULL,NULL,NULL,'Vendas',NULL,'N');
delete from C029 WHERE C029_Id = '100';
delete from C029 WHERE C029_Id = '101';
delete from C029 WHERE C029_Id = '102';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (103,'Incluir/Excluir/Alterar Cliente/Fornecedor','Cadastros - Cliente/Fornecedor','A','15');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (222,'Calcular Limite', 'Cadastros - Cliente/Fornecedor','A','15');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (104,'CRM',NULL,NULL,NULL,'Vendas',NULL,'N','4');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (105,'Permite acesso a qualquer usuário do sistema!','Usuários','A','0');
delete from C029 WHERE C029_Id = '106';
delete from C029 WHERE C029_Id = '107';
delete from C029 WHERE C029_Id = '108';
delete from C029 WHERE C029_Id = '109';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (110,'Estoque',NULL,NULL,NULL,'Estoque',NULL,'N','3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (111,'Excluir títulos e lançamentos','Financeiro','A','2');
delete from C029 WHERE C029_Id = '112';
delete from C029 WHERE C029_Id = '113';
delete from C029 WHERE C029_Id = '114';
delete from C029 WHERE C029_Id = '115';
delete from C029 WHERE C029_Id = '116';
delete from C029 WHERE C029_Id = '117';
delete from C029 WHERE C029_Id = '118';
delete from C029 WHERE C029_Id = '119';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (120,'P.C.P.',NULL,NULL,NULL,'Configuração - P.C.P.',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (121,'Clientes ativos/inativos',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
delete from C029 WHERE C029_Id = '122';
delete from C029 WHERE C029_Id = '123';
delete from C029 WHERE C029_Id = '124';
delete from C029 WHERE C029_Id = '125';
delete from C029 WHERE C029_Id = '126';
delete from C029 WHERE C029_Id = '127';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (128,'Catálogo',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
delete from C029 WHERE C029_Id = '129';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (130,'Orçamentos',NULL,NULL,NULL,'Vendas - CRM',NULL,'N','4');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (131,'Pedidos',NULL,NULL,NULL,'Vendas - CRM',NULL,'N','4');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (132,'Notas Fiscais - Apenas consulta',NULL,NULL,NULL,'Vendas - CRM',NULL,'N','4');
delete from C029 WHERE C029_Id = '133';
delete from C029 WHERE C029_Id = '134';
delete from C029 WHERE C029_Id = '135';
delete from C029 WHERE C029_Id = '136';
delete from C029 WHERE C029_Id = '137';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (138,'Receber',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
delete from C029 WHERE C029_Id = '139';
delete from C029 WHERE C029_Id = '140';
delete from C029 WHERE C029_Id = '141';
delete from C029 WHERE C029_Id = '142';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (143,'Agenda',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
delete from C029 WHERE C029_Id = '144';
delete from C029 WHERE C029_Id = '145';
delete from C029 WHERE C029_Id = '146';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (147,'Produtos vendidos',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
delete from C029 WHERE C029_Id = '148';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (149,'Fluxo de Caixa',NULL,NULL,NULL,'Relatórios',NULL,'N','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (241,'Vendas Mensais',NULL,NULL,NULL,'Relatórios',NULL,'N','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (150,'Projeçao Orçamentária',NULL,NULL,NULL,'Financeiro',NULL,'N');
delete from C029 WHERE C029_Id = '151';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (152,'B.I. - Business Intelligence',NULL,NULL,NULL,'Gerencial',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (153,'Inventario estoque',NULL,NULL,NULL,'Relatórios',NULL,'N','3');
delete from C029 WHERE C029_Id = '154';
delete from C029 WHERE C029_Id = '155';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (156,'Referências Comerciais',NULL,NULL,NULL,'Financeiro',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (157,'Cobranças',NULL,NULL,NULL,'Financeiro',NULL,'N');
delete from C029 WHERE C029_Id = '158';
delete from C029 WHERE C029_Id = '159';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (160,'SPED Contribuições',NULL,NULL,NULL,'Fiscal',NULL,'N');
delete from C029 WHERE C029_Id = '161';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (162,'Balancete',NULL,NULL,NULL,'Relatórios',NULL,'N','2');
delete from C029 WHERE C029_Id = '163';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (164,'Mapa de Vendas',NULL,NULL,NULL,'Relatórios',NULL,'N','1');
delete from C029 WHERE C029_Id = '165';
delete from C029 WHERE C029_Id = '166';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (167,'Exportar XML NF-e (Entrada)',NULL,NULL,NULL,'Fiscal',NULL,'N',14);
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (168,'Bancos',NULL,NULL,NULL,'Configuração - Financeiro',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (169,'Cancelar Nota Emitida','Vendas - Notas Fiscais','A','1');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (170,'Cotação de Compra',NULL,NULL,NULL,'Estoque - SRM',NULL,'N','6');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (171,'Ordem de Compra',NULL,NULL,NULL,'Estoque - SRM',NULL,'N','6');
delete from C029 WHERE C029_Id = '172';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (173,'Análise de Ordem de Compra',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (174,'SPED Contribuições',NULL,NULL,NULL,'Fiscal',NULL,'N');
delete from C029 WHERE C029_Id = '175';
delete from C029 WHERE C029_Id = '176';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (177,'Resumo vendedor',NULL,NULL,NULL,'Relatórios',NULL,'A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (178,'Serasa - Relato',NULL,NULL,NULL,'Financeiro',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (179,'Nota de Devolução',NULL,NULL,NULL,'Vendas',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Tipo`) values (180,'Atualizar o Estoque das Divergências de Estoque (D.E.)',NULL,NULL,NULL,'Estoque - D.E.',NULL,'N','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (181,'SRM',NULL,NULL,NULL,'Estoque',NULL,'N','6');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (182,'Atualizar a conta dos títulos a receber/pagar',NULL,NULL,NULL,'Financeiro',NULL,'N','A','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (183,'Incluir/Excluir/Alterar Pré Cadastro Cliente/Fornecedor','Cadastros - Cliente/Fornecedor','A','15');
delete from C029 WHERE C029_Id = '184';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (185,'Opções',NULL,NULL,NULL,'Configuração',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (186,'Incluir/Excluir/Alterar Produto','Cadastros - Produtos','A','10');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (187,'Incluir/Excluir/Alterar Pré Cadastro Produto','Cadastros - Produtos','A','10');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (274,'Importar CSV Tabela Geral Fornecedor','Cadastros - Produtos','A','10');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (188,'Incluir Nota Fiscal de Entrada','Estoque - Notas Fiscais Recebidas','A', '3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (189,'Alterar/Excluir Nota Fiscal de Entrada','Estoque - Notas Fiscais Recebidas','A', '3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (190,'Incluir/Alterar/Excluir Contas a Receber','Financeiro','A','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (191,'Incluir/Alterar/Excluir Contas a Pagar','Financeiro','A','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (192,'Alterar valor comissão de contas a receber','Financeiro','A','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (193,'Exportação EBS',NULL,NULL,NULL,'Relatórios',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (194,'Moedas',NULL,NULL,NULL,'Configuração - Comercial',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (195,'Análitico de Vendas',NULL,NULL,NULL,'Relatórios',NULL,'N','0');
delete from C029 WHERE C029_Id = '196';
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (197,'Montagem IPV',NULL,NULL,NULL,'Configuração - Comercial',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (198,'DRE',NULL,NULL,NULL,'Relatórios',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (199,'Alterar ordem de compra finalizada / aprovada','Estoque - Ordem de Compra','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (200,'Resumo - R2',NULL,NULL,NULL,'Vendas - CRM',NULL,'N','4');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (201,'Alterar comissão vendedor',NULL,NULL,NULL,'Vendas - Notas Fiscais',NULL,'N', 'A', '1');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (202,'Alterar comissão representante',NULL,NULL,NULL,'Vendas - Notas Fiscais',NULL,'N', 'A', '1');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (203,'PCP',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (204,'Tipo de agenda',NULL,NULL,NULL,'Configuração - Comercial',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (205,'Email - Whitelist',NULL,NULL,NULL,'Outros',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`, `C029_Flag_Tipo`) values (206,'Processar balanço',NULL,NULL,NULL,'Estoque - Balanço',NULL,'N', 'A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (207,'Tarefas(Atividades)',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (208,'Exibir demonstrativo preço tabela','Preço','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (209,'Incluir produtos similares','Cadastros - Produtos','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (210,'Alterar produtos similares','Cadastros - Produtos','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (211,'Excluir produtos similares','Cadastros - Produtos','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (212,'Pedidos Periódicos','Cadastros - Pedidos periódicos','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (213,'Alterar pedido','Mobile','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (214,'Alterar nota fiscal finalizada','Vendas - Notas Fiscais','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (215,'Demonstrativo IPV','Vendas - Demonstrativo IPV','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (216,'RMA',NULL,NULL,NULL,'Estoque',NULL,'N','3');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (217,'Desabilitar obs comercial cliente nas vendas','Financeiro','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (218,'Desabilitar obs financeira cliente nas vendas','Financeiro','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (219,'CNPJ Matriz',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (220,'Finalizar financeiro nota fiscal entrada','Estoque - Notas Fiscais Recebidas','A', '3');


insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (221,'Permite gerar cotação manual de compra no orçamento de venda','Vendas - Orcamento','A','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (223,'Listas',NULL,NULL,NULL,'Configuração - Suporte Técnico',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (224,'Quadros',NULL,NULL,NULL,'Configuração - Suporte Técnico',NULL,'N','0');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (225,'Exportar XML Conhecimentos',NULL,NULL,NULL,'Fiscal',NULL,'N','14');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (226,'Analítico de Compras',NULL,NULL,NULL,'Relatórios',NULL,'N','0');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (227,'Gerencial - Relatórios','Gerencial','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (228,'Gerencial - Exportação Power BI','Gerencial','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (229,'Botão suporte cliente',NULL,NULL,NULL,'Configuração - Suporte Técnico',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (230,'Alterar romaneio entrega do pedido na expedição aguardando coleta','Vendas - Pedido','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (299,'Desabilitar botão Faturar no Pedido','Vendas - Pedido','A');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (231,'Desabilitar Incluir/Excluir/Alterar na aba Cadastros','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (232,'Desabilitar Incluir/Excluir/Alterar na aba Contatos','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (233,'Desabilitar Incluir/Excluir/Alterar na aba Endereços','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (234,'Desabilitar Incluir/Excluir/Alterar na aba Fiscal','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (235,'Desabilitar Incluir/Excluir/Alterar na aba Comercial','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (236,'Desabilitar Incluir/Excluir/Alterar na aba Financeiro','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (237,'Desabilitar Incluir/Excluir/Alterar na aba Documentos','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (238,'Desabilitar Incluir/Excluir/Alterar na aba Agenda','Cadastros - Cliente/Fornecedor','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (239,'Desabilitar "Processar saldo" na aba Créditos','Cadastros - Cliente/Fornecedor','A');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (240,'Grupo Clientes','Cadastros','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (243,'Veículos','Cadastros','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (244,'Lacres','Cadastros','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (245,'Seguradoras','Cadastros','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (247,'B.I. - Configuração',NULL,NULL,NULL,'Configuração - Gerencial',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (248,'Usuários API','Usuários','A','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (249,'Usuários B2B','Usuários','A','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (250,'B2B',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (251,'Notificações Celular',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (252,'PDV',NULL,NULL,NULL,'Vendas',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (253,'Alterar obrigação de vincúlo NF de compra com OC ','Estoque - Notas Fiscais Recebidas','A', '3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (254,'Outros','Fiscal','M', '');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (255,'Análise PO',NULL,NULL,NULL,'Importação',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (256,'Contábil',NULL,NULL,NULL,'Contábil',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (257,'CT-e',NULL,NULL,NULL,'CT-e',NULL,'N');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (258,'Compras Estoque',NULL,NULL,NULL,'Importação',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (259,'Compras Emergência',NULL,NULL,NULL,'Importação',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (260,'Quotation',NULL,NULL,NULL,'Importação',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (261,'Purchase Order',NULL,NULL,NULL,'Importação',NULL,'N');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (262,'Incluir Locação','Estoque - Locação','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (263,'Alterar Locação','Estoque - Locação','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (264,'Excluir Locação','Estoque - Locação','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (265,'Autorizar divergência entre NF e OC','Estoque - Notas Fiscais Recebidas','A', '3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`,`C029_Flag_Modulo`) values (266,'Nota Fiscal',NULL,NULL,NULL,'Configuração',NULL,'N','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (267,'Desabilitar alteração dos valores no lançamento de baixa de titulos','Financeiro','A','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (268,'Cálculo de Preço',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (269,'Cálculo de Preço Engenharia',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (270,'Caixa PDV',NULL,NULL,NULL,'Vendas',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (271,'Não permitir alterar lançamentos','Financeiro','A','2');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (272,'Auditoria','Outros','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (273,'Log Erros','Outros','M','');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (275,'CIAP - Crédito ICMS Ativo Imobilizado','Estoque - Notas Fiscais Recebidas','A', '3');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (276,'Acesso Framework Novo', 'Outros', 'M', '');

insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (277,'Recebimento de Notas Fiscais',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (278,'Recebimento de Materiais',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (279,'Transferência de Materiais OP',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (280,'Atendimento de Requisição',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (281,'Motivos de Feedback',NULL,NULL,NULL,'Cadastros',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (282,'Menu Engenharia',NULL,NULL,NULL,'Engenharia',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (283,'Menu Exportacoes',NULL,NULL,NULL,'Exportacoes',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (284,'Aba Ajustes',NULL,NULL,NULL,'Configurador',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (285,'Menu Relatorio Pedidos',NULL,NULL,NULL,'Relatorio Pedidos',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (286,'Logistica',NULL,NULL,NULL,'Processo Carga',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (287,'Rotas e Veículos',NULL,NULL,NULL,'Cadastros',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (288,'Fotos',NULL,NULL,NULL,'Cadastros',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (289,'Contagem Cíclica',NULL,NULL,NULL,'Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (290,'Conferência D.I',NULL,NULL,NULL,'Estoque - Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (291,'Analises',NULL,NULL,NULL,'Qualidade',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (292,'Contratos Importacao',NULL,NULL,NULL,'Estoque - SRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (293,'Responsavel Atividades',NULL,NULL,NULL,'Cadastros',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (294,'Atualizar Comissão',NULL,NULL,NULL,'Vendas - CRM',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (295,'Inventario',NULL,NULL,NULL,'Estoque - Estoque',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (296,'Lista de Relatorios',NULL,NULL,NULL,'Relatorio Pedidos',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (297,'Exportar XML Conhecimentos Emitidos',NULL,NULL,NULL,'Fiscal',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (298,'Conferencia Cega - Recebimento',NULL,NULL,NULL,'Fiscal',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (300,'Cadastro de Setores - Aprovação OP',NULL,NULL,NULL,'Estoque',NULL,'N'); 
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (301,'Aprovação OP por Setor',NULL,NULL,NULL,'Estoque',NULL,'N'); 
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`) values (302,'Desabilitar botão Incluir Orçamento','Vendas - Orcamento','A');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (303,'Preencher Observações Paraná Competitivo','Fiscal','A','14');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (304,'Acessar painel de integrações','Integrações','M','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (305,'Acessar configurações das integrações','Integrações','M','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (306,'Acessar conexões MCP','Integrações','M','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Grupo`,`C029_Flag_Tipo`,`C029_Flag_Modulo`) values (307,'Administrar Webhooks','Integrações','M','0');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (308,'Acessar Apontamento PCP',NULL,NULL,NULL,'Estoque - PCP',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (309,'Editar/Excluir Apontamentos PCP',NULL,NULL,NULL,'Estoque - PCP',NULL,'N');
insert into c029 (`C029_Id`,`C029_Descricao_Acesso`,`C029_Entidade`,`C029_Tela`,`C029_Opcao`,`C029_Grupo`,`C029_Grupo_NFe`,`C029_Flag_NFe`) values (310,'Acessar GNRE',NULL,NULL,NULL,'Fiscal',NULL,'N');


EOT;
$registroC029 = explode("\n", $registroC029);
foreach ($registroC029 as $registroC029SQL) {
	$cSql = mysql_query($registroC029SQL);
}












   

Criar_Campo("T008_Codigo_Barras_Tributavel", "varchar(13)");
Criar_Campo("D001_Codigo_Barras_Tributavel", "varchar(13)");
Criar_Campo("D001_Quantidade_Tributavel", "decimal(10,3)");
Criar_Campo("D005_D037_Id_Tributavel", "int(10)");
Criar_Campo("T008_D037_Id_Tributavel", "int(10)");
Criar_Campo("T008_Quantidade_Tributavel", "decimal(12,5)");
Criar_Campo("T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel", "decimal(20,10)");
Criar_Campo("D006_Flag_Unidade_Tributavel", " char(1) DEFAULT 'N'");
// campos adicionais em geral
Criar_Campo("T247_Numero_Duplicata", "VARCHAR(50) DEFAULT NULL");
Criar_Campo("D166_Flag_Ativo", "ENUM('S','N') DEFAULT 'S'");
Criar_Campo("D047_Flag_Compra", "ENUM('S','N')");
Criar_Campo("D047_Flag_Conferencia", "ENUM('S','N') DEFAULT 'N'");
Criar_Campo("C004_Logo_Marca_B2B","varchar(100)");
Criar_Campo("T003_Id_Pedido_Externo","varchar(32)");
Criar_Campo("T182_D162_Id","int(11)");
Criar_Campo("T206_D169_Id","int(11)");
Criar_Campo("T206_Flag_Marcado","varchar(1) DEFAULT 'N'");
Criar_Campo("D001_C004_Id","int(11)");
Criar_Campo("T234_T236_Id","int(10) unsigned");
//Cria nova chave da D166(grupo suporte) na T234
Criar_Campo("T234_D166_Id","int(10) unsigned");
//Cria nova chave da D167(Lista suporte) na T234
Criar_Campo("T234_D167_Id","int(10) unsigned");
Criar_Campo("D166_Flag_Atendimento","ENUM('S','N') DEFAULT 'N'");
Criar_Campo("T234_T272_Id", "int(10)");
Criar_Campo("T272_Nome_Arquivo","varchar(200)");
Criar_Campo("T234_Horas_Desenvolvimento","int(3) DEFAULT '0'");
Criar_Campo("T234_Email_Solicitante","text");
Alterar_Tipo_Campo("T234_Horas_Desenvolvimento","time default '00:00:00'");
Criar_Campo("T234_Valor_Hora_Tecnica","DECIMAL(14,2)");
Criar_Campo("T055_Item_Ac_Drawback", "varchar(20) DEFAULT NULL");
Criar_Campo("T055_Unidade_Medida", "varchar(5) DEFAULT NULL");
Criar_Campo("T270_Informacoes_Complementares", "text");
Criar_Campo("T270_Drawback_Ato_Isencao", "int(1)");
Criar_Campo("T270_Drawback_Ato_Suspensao", "int(1)");
Criar_Campo("T270_Cobertura_Cambial", "int(1)");
Criar_Campo("T270_Modalidade_Pagamento", "varchar(20)");
Criar_Campo("T075_Codigo_URF_Entrada", "int(7)");
Criar_Campo("T075_Codigo_URF_Despacho", "int(7)");
Criar_Campo("T075_Codigo_Moeda", "int(3)");
Criar_Campo("T075_Codigo_Incoterms", "varchar(4)");
Criar_Campo("T075_Condicao_Mercadoria", "varchar(20)");
Criar_Campo("T075_Enquadramento_Material_Usado", "varchar(25)");
Criar_Campo("T075_Operacao_Enquadramento_Material_Usado", "varchar(30)");
Criar_Campo("D027_Flag_Baixa_Automatica","ENUM('S','N') DEFAULT 'N'");
Criar_Campo("D031_Flag_Baixa_Automatica","ENUM('S','N') DEFAULT 'N'");
Criar_Campo("T135_Flag_Gerar_Recebimento","ENUM('S','N') DEFAULT 'N'");
Criar_Campo("T236_Especificacoes","text");
Criar_Campo("T236_Analise","text");
Criar_Campo("T236_Prioridade","varchar(20)");
Criar_Campo("T236_Valor_Hora","DECIMAL(14,2)");
Criar_Campo("T002_T141_Id","int(10) unsigned");
Criar_Campo("T002_Flag_Gerar_Boleto","int(1)");
Criar_Campo("D024_DDD_Telefone_2","varchar(4) default ''");
Criar_Campo("D024_JSON", "JSON DEFAULT NULL");
Criar_Campo("T075_D024_Id","int unsigned");
Criar_Campo("T075_D118_Id","int unsigned");
Criar_Campo("T075_D119_Id","int unsigned");
Criar_Campo("T002_T005_Id","int unsigned");
Criar_Campo("T001_T005_Id","int unsigned");
Criar_Campo("T075_Cotacao_ME","DECIMAL(14,2)");
Criar_Campo("T075_Valor_Frete_Incluso","DECIMAL(14,2)");
Criar_Campo("T075_Valor_ICMS","DECIMAL(14,2)");
Criar_Campo("T075_Valor_Seguro","DECIMAL(14,2)");
Criar_Campo("T075_Valor_Despesa","DECIMAL(14,2)");
Criar_Campo("T075_Observacao_DI","text");
Criar_Campo("T003_Motivo_Pedido_Liberacao","text");
Criar_Campo("T005_Motivo_Pedido_Liberacao","text");
Criar_Campo("T005_Motivo_Nao_Pedido_Liberacao","text");
Criar_Campo("T075_Observacao_NF","text");
Criar_Campo("T075_C004_Id","int(10) unsigned");
Criar_Campo("T075_Aliquota_ICMS_PIS_COFINS","DECIMAL(5,2)");
Criar_Campo("T075_Aliquota_ICMS_Credito_Presumido","DECIMAL(5,2)");
Criar_Campo("T075_Peso_Liquido","DECIMAL(10,3)");
Criar_Campo("T075_Peso_Bruto","DECIMAL(10,3)");
Criar_Campo("T007_T075_Id","int(10)");
Criar_Campo("T075_D006_Id","int(10)");
Criar_Campo("T055_D006_Id","int(10) unsigned");
Criar_Campo("T055_Valor_Adicao","DECIMAL(14,2)");
Criar_Campo("T202_Observacao","text");
Criar_Campo("T055_Flag_ME","char(1)");
Criar_Campo("T055_Flag_Frete","char(1) default 'S'");
Criar_Campo("T006_Flag_Cancelado","char(1) default 'N'");
Criar_Campo("D014_Flag_Ativo","char(1) default 'S'");
Criar_Campo("D014_Flag_Verificar_Saldo","char(1) default 'S'");
Criar_Campo("T055_T075_Id","int unsigned");
Criar_Campo("D016_Valor_Libra","DECIMAL(10,4) default NULL");
Criar_Campo("T139_Flag_Excluir","int(1) default 0");
Criar_Campo("T139_Flag_Lido","int(1) default 0");
Criar_Campo("T139_D024_Id","int(10) unsigned");
Criar_Campo("T139_D013_Id","int(10) unsigned");
Criar_Campo("T139_T003_Id","int(10) unsigned");
Criar_Campo("T139_D117_Id","int(10) unsigned");
Criar_Campo("T139_C007_Id","int(10) unsigned");
Criar_Campo("T139_T137_Id","int(10) unsigned");
Criar_Campo("D013_Tipo_Contato","varchar(30)");
Criar_Campo("T003_Forma_Pagamento","varchar(40)");
Criar_Campo("T003_Frete","varchar(40)");
Criar_Campo("D085_Data_Retorno","date");
Criar_Campo("D085_C007_Id_Inclusao","int(10) unsigned");
Criar_Campo("T139_Valor_Total","DECIMAL(14,2)");
Criar_Campo("T139_Numero_Registros","int(10) unsigned");
Criar_Campo("D061_Observacao_1","text");
Criar_Campo("D061_Observacao_2","text");
Criar_Campo("D061_Observacao_3","text");
Criar_Campo("D061_Observacao_Comercial_1","text");
Criar_Campo("D061_Observacao_Comercial_2","text");
Criar_Campo("D061_Observacao_Comercial_3","text");
Criar_Campo("D061_Observacao_Banco_1","text");
Criar_Campo("D061_Observacao_Banco_2","text");
Criar_Campo("D061_Observacao_Banco_3","text");
Criar_Campo("D061_Telefone_Banco_1","varchar(40)");
Criar_Campo("D061_Telefone_Banco_2","varchar(40)");
Criar_Campo("D061_Telefone_Banco_3","varchar(40)");
Criar_Campo("D137_Origem_Mercadoria","varchar(20)");
Criar_Campo("D061_Referencia_Bancaria_1","varchar(50)");
Criar_Campo("D061_Referencia_Bancaria_2","varchar(50)");
Criar_Campo("D061_Referencia_Bancaria_3","varchar(50)");
Criar_Campo("D106_Tipo","char(1)");
Criar_Campo("D120_Tipo","char(1)");
Criar_Campo("D024_Rg","varchar(30)");
Criar_Campo("D030_Codigo_Area","int(2)");
Criar_Campo("D007_CNPJ","varchar(20)");
Criar_Campo("D007_CPF","varchar(20)");
Criar_Campo("D007_Flag_Fluxo_Caixa","CHAR(1) DEFAULT 'S'");
Criar_Campo("T156_D007_Id","int(10) unsigned");
Criar_Campo("D024_Inscricao_Municipal","varchar(20)");
Criar_Campo("D061_Referencia_Tipo_1","varchar(50)");
Criar_Campo("D061_Referencia_Tipo_2","varchar(50)");
Criar_Campo("D061_Referencia_Tipo_3","varchar(50)");
Criar_Campo("D061_Referencia_Bancaria_Tipo_1","varchar(50)");
Criar_Campo("D061_Referencia_Bancaria_Tipo_2","varchar(50)");
Criar_Campo("D061_Referencia_Bancaria_Tipo_3","varchar(50)");
Criar_Campo("D061_Referencia_Comercial_Tipo_1","varchar(50)");
Criar_Campo("D061_Referencia_Comercial_Tipo_2","varchar(50)");
Criar_Campo("D061_Referencia_Comercial_Tipo_3","varchar(50)");
Criar_Campo("D007_Nome_Contato","varchar(50)");
Criar_Campo("D007_Observacao","text");
Criar_Campo("D007_DDD_Telefone","int(2)");
Criar_Campo("D007_Telefone","varchar(20)");
Criar_Campo("D109_Data_Admissao","date");
Criar_Campo("T005_Data_Finalizado_Caixa","date");
Criar_Campo("D109_Codigo_Barras","varchar(255)");
Criar_Campo("D109_Data_Demissao","date");
Criar_Campo("D109_Cpf","varchar(20)");
Criar_Campo("D109_Rg","varchar(20)");
Criar_Campo("D109_Endereco","varchar(40)");
Criar_Campo("D109_Numero","varchar(15)");
Criar_Campo("D109_D018_Id","int(10) unsigned");
Criar_Campo("D109_D020_Id","int(10) unsigned");
Criar_Campo("D109_D030_Id","int(10) unsigned");
Criar_Campo("D109_Cep","varchar(9)");
Criar_Campo("D109_Bairro","varchar(25)");
Criar_Campo("D109_Nascimento","date");
Criar_Campo("D109_Email","varchar(40)");
Criar_Campo("D109_DDD_Telefone","int(2)");
Criar_Campo("D109_Telefone","varchar(20)");
Criar_Campo("D109_Data_Cadastro","date");
Criar_Campo("C007_D018_Id","int(10) unsigned");
Criar_Campo("C007_D020_Id","int(10) unsigned");
Criar_Campo("C007_Nome_Vendedor_Ecommerce","text");
Criar_Campo("C007_Flag_Tipo_Etiqueta_Transporte","char(1) DEFAULT ''");
Criar_Campo("T005_D018_Id","int(10) unsigned");
Criar_Campo("T005_T208_Id","int(10) unsigned");
Criar_Campo("T006_D082_Id","int(10) unsigned");
Criar_Campo("T006_Valor_Preco_Original","DECIMAL(20,10)");
Criar_Campo("T006_Percentual_Desconto","DECIMAL(20,10)");
Criar_Campo("T008_Valor_Preco_Original","DECIMAL(14,2)");
Criar_Campo("T008_Percentual_Desconto","DECIMAL(10,5)");
Criar_Campo("T008_Valor_Base_ICMS_Interno","DECIMAL(14,2)");
Criar_Campo("T008_Valor_Base_ICMS_Inter_Estadual","DECIMAL(14,2)");
Criar_Campo("T008_Valor_ICMS_Interno","DECIMAL(14,2)");
Criar_Campo("T008_Valor_ICMS_Inter_Estadual","DECIMAL(14,2)");
Criar_Campo("T007_Valor_Base_ICMS_Interno","DECIMAL(14,2)");
Criar_Campo("T007_Valor_Base_ICMS_Inter_Estadual","DECIMAL(14,2)");
Criar_Campo("T007_Valor_ICMS_Interno","DECIMAL(14,2)");
Criar_Campo("T007_Valor_ICMS_Inter_Estadual","DECIMAL(14,2)");
Criar_Campo("T007_Config_Vars_Danfe","text");

Criar_Campo("T006_Valor_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T006_Valor_Base_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T006_Aliquota_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T006_Percentual_Reducao_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T005_Valor_Total_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T005_Valor_Total_Base_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T269_D009_Id","int(10) unsigned");
Criar_Campo("T269_D013_Id","int(10) unsigned");
Criar_Campo("T269_Preco_Antigo","DECIMAL(14,2)");

Criar_Campo("T269_Preco_Total","DECIMAL(14,2)");
Criar_Campo("T269_Preco","DECIMAL(14,2)");

Criar_Campo("D024_Observacao_Inativo","text");
Criar_Campo("D024_Json_Sintegra","mediumtext");
Criar_Campo("D024_Data_Json_Sintegra","datetime");
Criar_Campo("D024_Dia_Visita","varchar(255)");
Criar_Campo("D024_Data_Ultima_Analise", "date default '0000-00-00'");
Criar_Campo("D024_D201_Id", "int(10) default null"); // Qual canal de prospecção o cliente descobriu a empresa

Criar_Campo("T006_Observacao","text");
Criar_Campo("T160_D124_Id","int(10) unsigned");
Criar_Campo("T159_D125_Id","int(10) unsigned");
Criar_Campo("T006_D009_Id_Loja","int(10) unsigned");
Criar_Campo("T004_D009_Id_Loja","int(10) unsigned");
Criar_Campo("T075_Data_Embarque","date");
Criar_Campo("T075_ETA_Data","date");
Criar_Campo("T075_ETA_Hora","time");
Criar_Campo("T075_D044_Id","int(10) unsigned");
Criar_Campo("T075_Aliquota_ICMS","DECIMAL(5,2)");
Criar_Campo("T075_Aliquota_ICMS_Presumido","DECIMAL(5,2)");
Criar_Campo("T075_Percentual_Reducao_ICMS","DECIMAL(5,2)");
Criar_Campo("T075_Percentual_Reducao_ICMS_Presumido","DECIMAL(5,2)");
Criar_Campo("T075_Siscoserv_RAS","text");
Criar_Campo("T075_Siscoserv_RP","text");
Criar_Campo("T075_Valor_VMLE","DECIMAL(15,5)");
Criar_Campo("T075_Valor_VMLD","DECIMAL(15,5)");
Criar_Campo("D116_D126_Id","int(10) unsigned");
Criar_Campo("D116_Nome_Documento","int(10) unsigned");
Criar_Campo("T154_D031_Id","int(10) unsigned");
Criar_Campo("T144_Descricao","text");
Criar_Campo("T144_Flag_Tipo","varchar(1) default ''");
Criar_Campo("T162_Registro_Id","int(10) unsigned");
Criar_Campo("T162_Registro","blob");
Criar_Campo("T162_IP","varchar(15)");
Criar_Campo("D120_Descricao_Ingles","text");
Criar_Campo("D106_D128_Id","int(10) unsigned");
Criar_Campo("D120_D128_Id","int(10) unsigned");
Criar_Campo("D121_D128_Id","int(10) unsigned");
Criar_Campo("T141_D116_Id","int(10) unsigned");
Criar_Campo("D124_T141_Id","int(10) unsigned");
Criar_Campo("D024_Flag_Contribuinte","int(5)");
Criar_Campo("T164_D125_Id","int(10) unsigned");
Criar_Campo("D125_T141_Id","int(10) unsigned");
Criar_Campo("T139_T117_Id","int(10) unsigned");
Criar_Campo("T139_T075_Id","int(10) unsigned");
Criar_Campo("T139_T005_Id","int(10) unsigned");
Criar_Campo("T139_T011_Id","int(10) unsigned");
Criar_Campo("T005_D013_Id","int(10) unsigned");
Criar_Campo("T002_D032_Id","int(10) unsigned");
Criar_Campo("T015_D032_Id","int(10) unsigned");
Criar_Campo("T019_D032_Id","int(10) unsigned");
Criar_Campo("T008_T138_Id","int(10) unsigned");
Criar_Campo("T139_Flag_Selecionado","int(1) unsigned");
Criar_Campo("C007_Email_Verificacao","int(10) unsigned");
Criar_Campo("C007_Email_Pasta_Entrada","varchar(100)");
Criar_Campo("C007_Email_Pasta_Saida","varchar(100)");
Criar_Campo("C007_Email_Pastas","text");
Criar_Campo("T139_Cc","varchar(255)");
Criar_Campo("T139_Cco","varchar(255)");
Criar_Campo("D027_D007_Id","int(10) unsigned");
Criar_Campo("D117_DDD_Telefone","varchar(30) DEFAULT NULL");
Criar_Campo("T199_Data_Envio_Email_Cce","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D109_Senha","int(10) unsigned");
Criar_Campo("D049_ICF1","DECIMAL(10,2)");
Criar_Campo("D049_ICF2","DECIMAL(10,2)");
Criar_Campo("T139_T007_Id","int(10) unsigned");
Criar_Campo("T147_Hora_Pausa","datetime");
Criar_Campo("T139_T199_Id","int(10) unsigned");
Criar_Campo("T147_Prioridade","int(2) unsigned");
Criar_Campo("D109_Inicio_Ausencia","date");
Criar_Campo("D109_Fim_Ausencia","date");
Criar_Campo("D109_Carga_Horaria","int(2) unsigned");
Criar_Campo("T166_Tipo","char(1)");
Criar_Campo("D006_ST_MVA","decimal(5,2)");
Criar_Campo("T166_C007_Id","int(10) unsigned");
Criar_Campo("T196_Tipo","char(1)");
Criar_Campo("T196_C007_Id","int(10) unsigned");
Criar_Campo("T005_Observacao_Fatura_Emergencia","text");
Criar_Campo("D053_ST_ICMS_Inter_Estadual","decimal(5,2)");

Criar_Campo("D053_Aliquota_ICMS_Compra","decimal(5,2) default 0");
Criar_Campo("D053_Flag_ST_Compra","varchar(1) default 'N'");
Criar_Campo("D053_ST_ICMS_Interno_Compra","decimal(10,2) default 0");
Criar_Campo("D053_ST_ICMS_Inter_Estadual_Compra","decimal(10,2) default 0");
Criar_Campo("D053_Calculo_ST_Por_Dentro","varchar(1) default 'N'");
Criar_Campo("D053_Flag_Ajusta_MVA","varchar(1) default 'S'");

Criar_Campo("C033_D024_Id","int(10) unsigned NOT NULL");
Criar_Campo("D005_Aliquota_ICMS_Interno_Compra","decimal(10,2) default 0");

Criar_Campo("C004_CFOP_ST_Venda_Dentro_Estado","int(5)");
Criar_Campo("C004_CFOP_ST_Venda_Fora_Estado","int(5)");
Criar_Campo("C004_CFOP_CONSUMO_Venda_Dentro_Estado","int(5)");
Criar_Campo("C004_CFOP_CONSUMO_Venda_Fora_Estado","int(5)");
Criar_Campo("T015_T075_Id","int(10) unsigned");
Criar_Campo("D007_Flag_Ativo","char(1) DEFAULT 'S'");
Criar_Campo("T059_T173_Id","int(10) unsigned");
Criar_Campo("T011_Status_Aprovacao","int(1) DEFAULT 1");
Criar_Campo("T011_Frete","varchar(255)");
Criar_Campo("T011_Data_Aprovacao","date");
Criar_Campo("T011_C007_Id_Aprovador","int(10) unsigned");
Criar_Campo("T145_D116_Id","int(10) unsigned");
Criar_Campo("T147_D116_Id","int(10) unsigned");
Criar_Campo("T169_Observacao","text");
Criar_Campo("T035_Nome_Arquivo","varchar(20)");
Criar_Campo("T035_Numero_Titulos","int(10)");
Criar_Campo("T035_Total_Remessa","DECIMAL(14,2)");
Criar_Campo("T075_C007_Id","int(10) unsigned");
Criar_Campo("T055_T270_Id","int(10) unsigned");
Criar_Campo("C007_Email_Assinatura","text");
Criar_Campo("T030_Locacao","varchar(30)");
Criar_Campo("T066_Locacao","varchar(30)");
Criar_Campo("D109_Flag_Ativo","char(1) DEFAULT 'S'");
Criar_Campo("T147_Flag_Cancelado","char(1) DEFAULT 'N'");
Criar_Campo("T004_Flag_Dividir","char(1) DEFAULT 'N'");
Criar_Campo("D027_Flag_Tipo","int(1)");
Criar_Campo("D009_Quantidade_Estoque_B2B","decimal(10,3) default 0");
Criar_Campo("T090_Numero_Cheque","varchar(50)");
Criar_Campo("T090_Banco_Cheque","varchar(50)");
Criar_Campo("T090_Agencia_Cheque","varchar(50)");
Criar_Campo("T090_Conta_Corrente_Cheque","varchar(50)");
Criar_Campo("T090_Nome_Cheque","varchar(50)");
Criar_Campo("T090_Data_Cheque","date");
Criar_Campo("T236_Data_Inicio","date");
Criar_Campo("T090_Cidade_Cheque","varchar(50)");
Criar_Campo("D049_Origem_Mercadoria","INT");
Criar_Campo("T090_Observacao","varchar(100)");
Criar_Campo("D024_Json_Sintegra","mediumtext");
Criar_Campo("D024_Data_Json_Sintegra","datetime");


Criar_Campo("D148_Cnpj","varchar(20)");
Criar_Campo("D148_Cpf","varchar(20)");
Criar_Campo("D148_Inscricao_Estadual","varchar(20)");
Criar_Campo("D148_Flag_Pessoa_Fisica_Juridica","varchar(1) DEFAULT ''");

Criar_Campo("T004_Flag_Origem_Produto","char(1) DEFAULT ''");
Criar_Campo("T006_Flag_Origem_Produto","char(1) DEFAULT ''");
Criar_Campo("T008_Flag_Origem_Produto","char(1) DEFAULT ''");

Criar_Campo("T004_ST_VA_Ajustada","decimal(10,2) DEFAULT '0'");

Criar_Campo("T118_Numero_Cheque","varchar(50)");
Criar_Campo("T118_Banco_Cheque","varchar(50)");
Criar_Campo("T118_Agencia_Cheque","varchar(50)");
Criar_Campo("T118_Conta_Corrente_Cheque","varchar(50)");
Criar_Campo("T118_Nome_Cheque","varchar(50)");
Criar_Campo("T118_Data_Cheque","date");
Criar_Campo("T118_Cidade_Cheque","varchar(50)");
Criar_Campo("T118_Taxa_Juros","decimal(12,2)");
Criar_Campo("T118_Valor_Juros","decimal(12,2)");

Criar_Campo("D143_Aliquota_ICMS_1","decimal(5,2)");
Criar_Campo("D143_Percentual_Reducao_ICMS_1","decimal(5,2)");
Criar_Campo("D143_Aliquota_ICMS_2","decimal(5,2)");
Criar_Campo("D143_Percentual_Reducao_ICMS_2","decimal(5,2)");
Criar_Campo("D143_Flag_ST","char(1)");
Criar_Campo("D143_ST_ICMS_Interno","decimal(12,2)");
Criar_Campo("D143_ST_ICMS_Inter_Estadual","decimal(5,2)");
Criar_Campo("D143_MVA","decimal(5,2)");
Criar_Campo("D143_Flag_MVA_Ajustado","char(1) default 'S'");
Criar_Campo("D007_Codigo_Cooperativa","varchar(10) default ''");
Criar_Campo("D007_Digito_Verificador_Cooperativa","varchar(3) default ''");
Criar_Campo("D007_Codigo_Cliente", "varchar(10) default ''");
Criar_Campo("D007_Digito_Verificador_Cliente","varchar(3) default ''");


//Criação de campos tarefas Raquel
Criar_Campo("T007_Numero_Cupom_Fiscal","varchar(12)"); 
Criar_Campo("T007_Nota_Fiscal_Devolucao","varchar(70)"); 
Criar_Campo("T007_Nota_Fiscal_Devolucao_Data","varchar(110)");
Criar_Campo("D024_Flag_Transporta_Diferentes_UF","char(1) default 'N'"); 
Criar_Campo("D007_Modelo_Layout_Boleto","varchar(2) default ''");
Criar_Campo("T276_Numero_Recibo_MDFe","varchar(20) default ''");
Criar_Campo("T276_Versao_MDFe", "decimal(5,2) default '3.00'");
Criar_Campo("T276_Data_Hora_Recebimento_MDFe", "datetime");
Criar_Campo("T276_C007_Id", "int(11)");
Criar_Campo("T276_TX_Resposta_Codigo", "int(11) default 0");
Criar_Campo("T276_TX_Resposta_Extra", "text");
Criar_Campo("T276_TX_Retorno_Sefaz", "text");
Criar_Campo("T276_Arquivo_XML_Temp", "text");
Criar_Campo("T276_Emitente_UF_IBGE", "int(7)");
Criar_Campo("T276_TX_Flag_Finalizada", "int(1) default '0'");
Criar_Campo("T276_Flag_Scan", "enum('S','N') default 'N'");
Criar_Campo("T276_TX_Timestamp_Envio_Sefaz", "datetime default '0000-00-00 00:00:00'");
Criar_Campo("T276_TX_Mensagem", "text");
Criar_Campo("T276_Data_Envio_XML", "datetime default '0000-00-00 00:00:00'");
Criar_Campo("T276_Flag_Impresso", "varchar(1) default 'S'");
Criar_Campo("T276_TX_Flag_Erro", "int(1) default 0 not null");
Criar_Campo("T276_Flag_Transportador", "int(3)");
Criar_Campo("T276_Fuso_Horario_Emissao", "varchar(6) default null");
Criar_Campo("T276_Cidade_Carregamento_Codigo", "int(7) default null");
Criar_Campo("T283_Versao_Modal", "decimal(5,2) default '0.00'");
Criar_Campo("T283_T276_Id", "int(10) unsigned");
Criar_Indice('T283_T276_Id');
Criar_Indice('T178_Descricao');
Criar_Indice('D005_Flag_Ativo');
Criar_Campo("T283_Identificacao_Veiculo", "varchar(100)");
Criar_Campo("T278_Nome", "varchar(20)");
Criar_Campo("T278_CPF", "varchar(14)");
Criar_Campo("T280_Numero_Lacre", "varchar(20)");
Criar_Campo("T284_Cidade_Descarregamento", "varchar(60)");
Criar_Campo("T284_Cidade_Descarregamento_Codigo", "int(7)");
Criar_Campo("D174_T279_Id", "int(10) unsigned");
Criar_Campo("T280_T276_Lacre_MDFe_Id", "int(10) unsigned"); 
Criar_Campo("T276_Unidade_Peso_Bruto", "int(2) default 01");
Criar_Campo("T276_Cidade_Descarregamento", "varchar(40)");
Criar_Campo("T276_Cidade_Descarregamento_Codigo", "int(7) ");
Criar_Campo("T276_D018_Id_Descarregamento", "int(10) unsigned");
Criar_Campo("T276_Numero_Protocolo_Encerramento_MDFe", "varchar(15)");
Criar_Campo("T276_Data_Hora_Recebimento_Cancelamento_MDFe", "datetime");
Criar_Campo("T276_Flag_Travar_MDFe", "char(1) DEFAULT 'N'");
Criar_Campo("D171_Flag_Ativo", "enum('S','N') default'S'");
Criar_Campo("T276_D020_Id_Descarregamento", "int(10) unsigned");
Criar_Campo("T285_T276_Id", "int(10) unsigned");
Criar_Campo("T005_Liberou_D024_Id", "int(10) unsigned");
Criar_Campo("T276_Arquivo_Encerramento_Cancelamento_XML", "text");
Criar_Campo("D175_Tipo_Impressora", "int(2) default 1");
Criar_Campo("D006_Flag_Somar_DIFAL_Custo", "enum('S','N') default 'N'");
Criar_Campo("T014_Aliquota_ICMS_Diferenca_Aliquotas", "decimal(5,2) default '0.00'");
Criar_Campo("T001_Aliquota_ICMS_Diferenca_Aliquotas", "decimal(5,2) default '0.00'");
Criar_Campo("T289_Valor_ICMS_Substituto_Unitario", "decimal(12,5) default '0.00000'");
Criar_Campo("T290_Valor_ICMS_Substituto", "decimal(12,5) default '0.00000'");
Criar_Campo("T008_Valor_ICMS_Substituto", "decimal(12,5) default '0.00000'");
Criar_Campo("T289_Base_ICMS_Substituto_Unitario", "decimal(12,5) default '0.00000'");
Criar_Campo("T290_Base_ICMS_Substituto", "decimal(12,5) default '0.00000'");
Criar_Campo("T008_Base_ICMS_Substituto", "decimal(20,2)");
Criar_Campo("T007_TTD", "varchar(50) default '' ");
Criar_Campo("D024_TTD", "varchar(50) default '' ");
Criar_Campo("D024_Despacho_Concessorio", "varchar(50) default '' ");
Criar_Campo("D024A_Exportacao_UF_Embarque", "varchar(2)");
Criar_Campo("D024A_Exportacao_Local_Embarque", "varchar(60)");
Criar_Campo("T007_Despacho_Concessorio", "varchar(50) default '' ");
Criar_Campo("T001_Estoque_Locacao", "text");
Criar_Campo("D001_Id_Antigo", "int(10)");
Criar_Campo("D001_JSON", "JSON DEFAULT NULL");
Criar_Campo("D177_C004_Id", "int(10) unsigned");
Criar_Campo("D177_C007_Id", "int(10) unsigned");
Criar_Campo("T127_T007_Id", "int(10) unsigned");
Criar_Campo("D006_Modalidade_ICMS_Substituicao", "int(1)");
Criar_Campo("D006_Modalidade_ICMS", "int(1)");
Criar_Campo("C004_Ultimo_Numero_NFCe", "varchar(12) default 0 ");
Criar_Campo("C004_Ultimo_Numero_Serie_NFE", "varchar(10) default 0 ");
Criar_Campo("C004_Ultimo_Numero_Serie_NFCE", "varchar(10) default 0 ");
//Campos D013A
Criar_Campo("D013A_CPF", "text");
Criar_Campo("D013A_Empresa_Trabalho", "text COMMENT 'Cadastrar a empresa do Cliente'");
Criar_Campo("D013A_Salario", "decimal(14,2) COMMENT 'Salário do Cliente'");
Criar_Campo("D013A_Cargo", "text COMMENT 'Cargo do Cliente'");
Criar_Campo("D013A_Profissao", "text COMMENT 'Profissão do cliente'");
Criar_Campo("D013A_Flag_Envio_Certificado", "enum('S','N') default'N'");

//Campos D001A
Criar_Campo("D001A_PAF_P_ICMS","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D001A_D059_Id_PAF_CST_ICMS","int(10)");
Criar_Campo("D001A_D006_Id_PAF_CFOP","int(10)");
Criar_Campo("D001A_Quantidade_Estoque_Tray","decimal(12,2) DEFAULT '0' NOT NULL");
Criar_Campo("D001A_P_Comissao_Interna","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D001A_P_Comissao_Externa","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D001A_P_ICMS_Formacao_Preco","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D001A_Flag_Produto_Emite_Certificado", "enum('S','N') default'N'");
Criar_Campo("D001A_Flag_Kanban", "enum('S','N') default'N'");
Criar_Campo("D001A_Flag_Validade", "varchar(1) default 'N'");
Criar_Campo("D001A_Codigo_IMPA", "varchar(20) default ''");
Criar_Campo("D001A_D187_Id", "int(10) unsigned");
Criar_Campo("D001A_MC", "decimal(14,4) COMMENT 'Margem de Contribuição'");
Criar_Campo("D001A_Preco_12", "decimal(14,4) COMMENT 'Preço de Venda ICMS 12'");
Criar_Campo("D001A_Preco_17", "decimal(14,4) COMMENT 'Preço de Venda ICMS 17'");
Criar_Campo("D001A_Preco_18", "decimal(14,4) COMMENT 'Preço de Venda ICMS 18'");
Criar_Campo("D001A_Custo_Fixo", "decimal(14,4) COMMENT '% Custo Fixo'");
Criar_Campo("D001A_Lucro", "decimal(14,4) COMMENT '% Lucro'");
Criar_Campo("D001A_CodFab", "text COMMENT 'Código do Fabricante'");
Criar_Campo("D001A_D109_Id", "int(10) COMMENT 'Analista PCP'");
Criar_Campo("D001A_Potencia", "text COMMENT 'Potência para Polux'");
Criar_Campo("D001A_Modelo", "text COMMENT 'Modelo para Polux'");
Criar_Campo("D001A_Norma", "text COMMENT 'Norma para Polux'");
Criar_Campo("D001A_Flag_Generico", "varchar(1) default 'N' COMMENT 'Produto Generico'");
Criar_Campo("D001A_Flag_Engenharia_Liberada", "enum('S','N') default'N'");

Criar_Campo("C007_D007_Id", "int(10) unsigned");

Criar_Indice("D001A_D187_Id");
//Campos D001A

Criar_Indice('D177_C004_Id');
Criar_Indice('D177_C007_Id');

Criar_Indice('D009_T066_Id');
Criar_Indice('D049_D009_Id');
Criar_Indice('D001_Id_Ecommerce');
Criar_Indice('D001_Id_Antigo');
Criar_Indice('D001_D001_Id');
Criar_Indice('T174_D009_Id');
Criar_Indice('T059_D009_Id_6');
Criar_Indice('T059_D009_Id_5');
Criar_Indice('T059_D009_Id_4');
Criar_Indice('T059_D009_Id_3');
Criar_Indice('T059_D009_Id_2');
Criar_Indice('T059_D009_Id_1');
Criar_Indice('T238_T066_Id');
Criar_Indice('D049_D082_Id');
Criar_Indice('D001_D003_Id');
Criar_Indice('D049_D082_Id');
Criar_Indice('D001_D005_Id');

Criar_Indice('T003_Liberou_Alcada_C007_Id');
Criar_Indice('T003_C004_Id_Estoque');
Criar_Indice('T003_D148_Id_Entrega');
Criar_Indice('T003_D047_Id');
Criar_Indice('C007_C007_Id');
Criar_Indice('C007_Id_Vendedor_Substituto');
Criar_Indice('T005_T003_Id');
Criar_Indice('T005_Flag_Status');
Criar_Indice('T007_T005_Id');
Criar_Indice('T219_T004_Id');
Criar_Indice('D085_T003_Id');

//--------------------------------
// Melhoria geração da nota fiscal
Criar_Indice('T006_Quantidade');
Criar_Indice('T090_Valor_Parcela');
Criar_Indice('T090_D027_Id');
Criar_Indice('D027_Flag_Boleto');
Criar_Indice('T007_Flag_ACP ');
Criar_Indice('T007_Serie');
Criar_Indice('T007_Flag_Emitida_Recebida');
Criar_Indice('D006_Flag_Estoque');
Criar_Indice('T117_Nosso_Numero');
Criar_Indice('D007_Nosso_Numero');
//--------------------------------
 
Alterar_Campo("T285_Chave_Acesso_Nfe","T285_Chave_Acesso_MDFe","varchar(44) default ''");
Alterar_Campo("T285_Numero_Recibo_Nfe","T285_Numero_Recibo_MDFe","varchar(20) default ''");
Alterar_Campo("T285_Numero_Nota_Fiscal","T285_Numero_MDFe","varchar(12) default ''");
Alterar_Campo("T276_Cidade_Descarregamento_Codigo","T276_Cidade_Descarregamento_IBGE","int(7) ");
Alterar_Campo("T276_Cidade_Carregamento_Codigo","T276_Cidade_Carregamento_IBGE","int(7) ");
Alterar_Campo("T276_Empresa_UF_Codigo","T276_Empresa_UF_IBGE","int(2) ");

Alterar_Campo("T007_TDD","T007_TTD","varchar(50) default '' ");
Alterar_Campo("D024_TDD","D024_TTD","varchar(50) default '' ");

Alterar_Tipo_Campo("T276_Emitente_CEP","varchar(10)");
Alterar_Tipo_Campo("T283_Placa","varchar(8)");
Alterar_Tipo_Campo("T276_Flag_Encerrada","char(1) default 'N'");

Alterar_Tipo_Campo("D069_Percentual_Desconto", "decimal(15,5) default '0.00000'");
Alterar_Tipo_Campo("D001_Preco_1","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("D001_Preco_2","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("D001_Preco_3","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("D001_Preco_4","decimal(20,10) default '0.0000000000'");

Alterar_Tipo_Campo("D009_Preco_1","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("D009_Preco_2","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("D009_Preco_3","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("D009_Preco_4","decimal(20,10) default '0.0000000000'");




//D053 - ICMS
//Criar_Campo("D053_Aliquota_DIFAL", "decimal(5,2) default '0.00'");
Excluir_Campo("D053","D053_Aliquota_DIFAL");

Criar_Campo("D024_Observacao_Administracao","text");
Criar_Campo("D024_Codigo_Beneficio_Fiscal","varchar(10)");

// Produtos - Tabelas Fornecedor
Criar_Campo("D069_Valor_ICMS_Diferenca_Aliquotas","decimal(15,5) DEFAULT '0.00'");
Criar_Campo("D069_Aliquota_ICMS_Diferenca_Aliquotas", "decimal(5,2) default '0.00'");

Alterar_Tipo_Campo("D170_Identificacao_Veiculo","varchar(10)");

Alterar_Tipo_Campo("T003_Casas_Decimais_Preco", "int(2) default 4");

Alterar_Tipo_Campo("T004_Valor_Preco_Sem_Desconto_Unitario","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T004_Valor_Preco_Unitario", "decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T004_Valor_Preco_Original", "decimal(20,10) default '0.0000000000'");

Alterar_Tipo_Campo("T005_Casas_Decimais_Preco", "int(2) default 4");

Alterar_Tipo_Campo("T006_Valor_Preco_Sem_Desconto_Unitario","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T006_Valor_Preco_Unitario","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T006_Valor_Preco_Original", "decimal(20,10) default '0.0000000000'");

Alterar_Tipo_Campo("T007_Casas_Decimais_Preco", "int(2) default 4");

Alterar_Tipo_Campo("T008_Valor_Preco_Sem_Desconto_Unitario","decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel", "decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T008_Valor_Preco_Unitario", "decimal(20,10) default '0.0000000000'");
Alterar_Tipo_Campo("T008_Valor_Preco_Original", "decimal(20,10) default '0.0000000000'");

Alterar_Tipo_Campo("T014_Valor_Custo_Unitario", "decimal(20,10) default '0.0000000000'");


Criar_Campo("T219_Valor_ICMS_Diferenca_Aliquotas","decimal(15,5) DEFAULT '0.00'");
Criar_Campo("T219_Aliquota_ICMS_Diferenca_Aliquotas", "decimal(5,2) default '0.00'");
Criar_Campo("T225_Valor_ICMS_Diferenca_Aliquotas","decimal(15,5) DEFAULT '0.00'");
Criar_Campo("T225_Aliquota_ICMS_Diferenca_Aliquotas", "decimal(5,2) default '0.00'");

Criar_Campo("T219_Flag_Lido","varchar(1) DEFAULT 'N'");
Criar_Indice('T219_Flag_Lido');

Alterar_Tipo_Campo("T283_Identificacao_Veiculo","varchar(10)");

Excluir_Indice("T290","I_T008_Id");

Alter_Query("ALTER TABLE T276 CHANGE T276_Cidade_Descarregamento_Codigo T276_Cidade_Descarregamento_IBGE int(7);");
Alter_Query("ALTER TABLE T276 CHANGE T276_Cidade_Carregamento_Codigo T276_Cidade_Carregamento_IBGE int(7);");
Alter_Query("ALTER TABLE T276 CHANGE T276_Empresa_UF_Codigo T276_Empresa_UF_IBGE int(2);");
Alter_Query("ALTER TABLE T276 CHANGE T276_Emitente_UF_IBGE T276_Emitente_UF_IBGE int(2);");

Excluir_Campo("T276","T276_UF_Carregamento");
Excluir_Campo("T276","T276_UF_Descarregamento");
Excluir_Campo("T276","T276_Cidade_Descarregamento");
Excluir_Campo("T276","T276_Cidade_Carregamento");
Excluir_Campo("T276","T276_Cidade_Descarregamento_IBGE");
Excluir_Campo("T276","T276_Cidade_Carregamento_IBGE");
Excluir_Campo("C004","C004_Flag_Emitir_MDFe");
Excluir_Campo("T285","T285_Numero_MDFE");
Excluir_Campo("T285","T285_C004_Id");
// Excluir_Campo("D013","D013_Senha");

Criar_Indice('T285_T276_Id');
Criar_Indice('T003_Id_Pedido_Externo');

//Criação de campos tarefas Alexa
Criar_Campo("T249_Nivel_Acesso","int(1)");
Criar_Campo("T250_Nivel_Acesso","int(1)");
Criar_Campo("C004_Imagem_Celular_B2B", "varchar(100)");
Criar_Campo("T234_Origem_Chamado","char(1) DEFAULT 'T'");

Criar_Campo("T250_Aliquota_Editavel","decimal(5,2) default '0.00'");
Criar_Campo("T250_Flag_Campo_Calculado","char(1) DEFAULT 'N'");
Criar_Campo("T249_Flag_Campo_Calculado","char(1) DEFAULT 'N'");
Criar_Campo("T250_Flag_Altera_Preco","char(1) DEFAULT 'N'");
Criar_Campo("T249_Flag_Altera_Preco","char(1) DEFAULT 'N'");
Criar_Campo("D027_Acrescimo_IPV","decimal(5,2) default '0.00'");
Criar_Campo("T075_Data_Inicio_Demurrage","date");
Criar_Campo("T055_T066_Id","int(10) unsigned");
Criar_Campo("T182_Data_Conclusao","date");
Criar_Campo("T182_Hora_Conclusao ","time");
Criar_Campo("T182_D164_Id","int(10) unsigned");
Criar_Campo("T182_D018_Id","int(10) unsigned");
Criar_Campo("T182_Lote_Fabricacao","varchar(50) DEFAULT ''");
Criar_Campo("T182_Acao","TEXT");
Criar_Campo("T005_Flag_Plataforma_Ecommerce","int(1) DEFAULT 0");
Criar_Campo("T005_Data_Hora_Envio_Ecommerce","datetime");
Criar_Campo("T005_Shipment_Increment_Id_Magento","varchar(50)");
Criar_Campo("D009_Quantidade_Estoque_Tabela","decimal(15,5) default 0");
Criar_Campo("D022_Descricao_Ecommerce","varchar(50) DEFAULT ''");
Criar_Campo("D036_Descricao_Ecommerce","varchar(50) DEFAULT ''");
Criar_Campo("D027_Descricao_Ecommerce","varchar(50) DEFAULT ''");
Criar_Campo("D027_Descricao_B2B","varchar(50) DEFAULT ''");
Criar_Campo("D168_D036_Id","int(10) unsigned");
Criar_Campo("T005_Data_Envio_Elogistics","datetime");
Criar_Campo("T135_D014_Id","int(10) unsigned");
Criar_Campo("T135_D032_Id","int(10) unsigned");
Criar_Campo("T005_T275_Id","int(10) unsigned");
Criar_Campo("D027_Parcelas_Ecommerce","int(5) default 0");
Criar_Campo("T268_D036_Id","int(10) unsigned");
Criar_Campo("T161_Observacao","varchar(150)");
Criar_Campo("T007_Data_Envio_XML_Transportadora","date default '0000-00-00'");
Criar_Campo("T007_Flag_Enviar_XML_Transportadora","char(1) DEFAULT 'N'");
Criar_Campo("D009_Meses_Reposicao","int(5) default 0");
Criar_Campo("T135_Qtde_Maxima_Chamados_Atendimento","char(4) default '3'");
Criar_Campo("T135_Qtde_Maxima_Chamados_Duvida","char(4) default '3'");
Criar_Campo("T234_Data_Previsao", "datetime default '0000-00-00 00:00:00'");
Alterar_Tipo_Campo("D180_Token", "varchar(200)");
Criar_Campo("T005_Canal_Vendas_Ecommerce","varchar(20) DEFAULT ''");
Criar_Campo("C004_Ordem_Empresa","int(5) unsigned DEFAULT 0");
Criar_Campo("D006_Valor_Codigo_Beneficio_Fiscal","varchar(10) DEFAULT ''");
Alterar_Tipo_Campo("T005_Canal_Vendas_Ecommerce", "varchar(40) DEFAULT ''");
Criar_Campo("D179_C007_Vendedor_Externo_Id","int(10) unsigned");
Criar_Campo("D179_C007_Id","int(10) unsigned");
Criar_Campo("D179_D021_Id","int(10) unsigned");

Criar_Campo("T006_Preco_Venda_Ecommerce","decimal(14,5) DEFAULT 0.00");
Criar_Campo("T006_Preco_Tabela_Ecommerce","decimal(14,5) DEFAULT 0.00");
Criar_Campo("T006_Valor_Desconto_Ecommerce","decimal(14,5) DEFAULT 0.00");

//Pagamento
Criar_Campo("T005_Flag_Frete_Primeira_Parcela","char(1) DEFAULT 'N'");
Criar_Campo("T005_Flag_ST_Primeira_Parcela","char(1) DEFAULT 'N'");
Criar_Campo("T005_Flag_IPI_Primeira_Parcela","char(1) DEFAULT 'N'");

Criar_Campo("T007_Flag_Frete_Primeira_Parcela","char(1) DEFAULT 'N'");
Criar_Campo("T007_Flag_ST_Primeira_Parcela","char(1) DEFAULT 'N'");
Criar_Campo("T007_Flag_IPI_Primeira_Parcela","char(1) DEFAULT 'N'");

Criar_Campo("T003_Flag_Frete_Primeira_Parcela","char(1) DEFAULT 'N'");
Criar_Campo("T003_Flag_ST_Primeira_Parcela","char(1) DEFAULT 'N'");
Criar_Campo("T003_Flag_IPI_Primeira_Parcela","char(1) DEFAULT 'N'");

Criar_Campo("D024_Cobrar_Frete_Primeira_Parcela","char(1) DEFAULT ''");
Criar_Campo("D024_Cobrar_IPI_Primeira_Parcela","char(1) DEFAULT ''");
Alterar_Tipo_Campo("D024_Cobrar_ST_Primeira_Parcela","char(1) DEFAULT ''");

Criar_Campo("D024_Pgto_Primeiro_Vcto_Tipo","varchar(10) DEFAULT ''");
Criar_Campo("D024_Pgto_Dias_Vcto_Tipo","varchar(10) DEFAULT ''");

Criar_Campo("T003_Pgto_Primeiro_Vcto_Valor_Data","date default '0000-00-00'");
Criar_Campo("T005_Pgto_Primeiro_Vcto_Valor_Data","date default '0000-00-00'");
Criar_Campo("T007_Pgto_Primeiro_Vcto_Valor_Data","date default '0000-00-00'");
Criar_Campo("T007_Prazo_Medio","int(10) unsigned");

//T005A
Criar_Campo("T005A_Data_Hora_Envio_Ecommerce2","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T005A_Data_Emissao_Ecommerce","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T005A_Status_Elogistics", "varchar(80) DEFAULT ''");
Criar_Campo("T005A_Observacao_Livre", "text");
Criar_Campo("T005A_Data_Ultimo_Status_Elogistics", "datetime default '0000-00-00 00:00:00'");
Criar_Campo("T005A_Previsao_Transportadora_Elogistics", "datetime default '0000-00-00 00:00:00'");
Criar_Campo("T005A_Codigo_Plp_Elogistics", "varchar(50) DEFAULT ''");
Criar_Campo("T005A_Flag_Etiqueta_Transp_Impressa","char(1) DEFAULT 'N'");
Criar_Campo("T005A_Data_Hora_Impressao","datetime default '0000-00-00 00:00:00' COMMENT 'Data hora impressão'");
Criar_Campo("T005A_Data_Hora_Reimpressao","datetime default '0000-00-00 00:00:00' COMMENT 'Data hora reimpressão'");
Criar_Campo("T005A_Percentual_Ordem_Compra","decimal(5,2) default '0.00'");
Criar_Campo("T005A_Percentual_Estoque_Disponivel_E_Ordem_Compra","decimal(5,2) default '0.00'");
Criar_Campo("T005A_Retornado", "int COMMENT 'Retornado para Alteração do Vendedor'");
Criar_Campo("T005A_D024_Id", "int COMMENT 'Cliente Remessa'");
Criar_Campo("T005A_JSON", "JSON DEFAULT NULL");
Criar_Campo("T005A_Flag_Auditoria", "char(1) DEFAULT 'N'");
Criar_Campo("T005A_Flag_Conferido", "varchar(20) DEFAULT 'AGUARDANDO'");

//Transportadora
Criar_Campo("D022_Valor_Frete_Peso","decimal(20,10) default '0'");
Criar_Campo("D022_Valor_Frete_Minimo","decimal(20,10) default '0'");
Criar_Campo("D022_Valor_Frete_Cubagem","decimal(20,10) default '0'");
Criar_Campo("T003_Valor_Frete_Calculado","decimal(20,2) default '0'");
Criar_Campo("T005_Valor_Frete_Calculado","decimal(20,2) default '0'");
Criar_Campo("T007_Valor_Frete_Calculado","decimal(20,2) default '0'");
Criar_Campo("D022_Valor_Taxa_Pedagio","decimal(20,2) default '0'");
Criar_Campo("D022_Valor_Fracao_Pedagio","decimal(20,3) default '0'");
Criar_Campo("D022_Taxa_ICMS_Dentro_Estado","decimal(20,2) default '0'");
Criar_Campo("D022_Taxa_ICMS_Fora_Estado","decimal(20,2) default '0'");
Criar_Campo("D022_Taxa_GRIS","decimal(20,2) default '0'");
Criar_Campo("D022_Url_Rastreio","text");
Criar_Campo("D022_Flag_Tipo_Etiqueta_Transporte","char(1) DEFAULT ''");

Alter_Query("ALTER TABLE D005 ADD CONSTRAINT I_UNIQUE_D005 UNIQUE (D005_Flag_Aplicacao,D005_Classificacao_Fiscal);");
Alter_Query("ALTER TABLE D149 ADD CONSTRAINT I_UNIQUE_D149 UNIQUE (D149_D005_Id,D149_C004_Id);");
Alter_Query("ALTER TABLE D178 ADD CONSTRAINT I_UNIQUE_D178 UNIQUE (D178_Token,D178_Key);");

//Criação de campos tarefas Lígia
Criar_Campo("T008_Valor_Acrescimo_Custo","decimal(20,2) default '0.00'");
Criar_Campo("T008_Valor_ICMS_Substituicao_Unitario","decimal(20,2) default '0.00'");
Criar_Campo("T007_Flag_Devolucao","char(1) DEFAULT ''");
Criar_Campo("T007_Flag_Tipo_Devolucao","varchar(150) DEFAULT ''");
Criar_Campo("D009_Valor_Total_Preco","decimal(14,5) DEFAULT 0.00");
Criar_Campo("D173_Identificacao","varchar(100)");
Criar_Campo("D173_Flag_Ativo","enum('S','N') default'S'");
Criar_Campo("T007_Flag_Marcado","varchar(1) DEFAULT 'N'");
Alterar_Tipo_Campo("T276_Valor_Carga","decimal(13,2)");
Alterar_Tipo_Campo("T276_Peso_Bruto_Total_Carga","decimal(11,4)");
Alterar_Tipo_Campo("T284_Peso_Bruto_Total","decimal(11,4)");
//Alterar_Tipo_Campo("T284_Valor_Carga","decimal(13,4)");
Criar_Campo("D174_Descricao","varchar(40)");
Criar_Campo("T279_Seguradora_Nome","varchar(60)");
Criar_Campo("T279_Seguradora_CNPJ","varchar(20)");
Criar_Campo("T279_Numero_Apolice","varchar(20)");
Criar_Campo("D170_Flag_Carro_Proprio","enum('S','N') default'S'");
Criar_Campo("T283_Flag_Carro_Proprio","enum('S','N') default'S'");
Criar_Campo("T283_CPF_Proprietario","varchar(20)");
Criar_Campo("T283_CNPJ_Proprietario","varchar(20)");
Criar_Campo("T283_RNTRC","varchar(8)");
Criar_Campo("T283_Proprietario_Veiculo","varchar(100)");
Criar_Campo("T283_Inscricao_Estadual_Proprietario","varchar(20)");
Criar_Campo("T283_Tipo_Proprietario","int(1)");
Criar_Campo("T283_Categoria_Veiculo","int(1)");
Criar_Campo("T283_D018_Id","int(10)");
Criar_Campo("D170_C004_Id","int(10)");
Criar_Campo("D171_C004_Id","int(10)");
Criar_Campo("D172_C004_Id","int(10)");
Alterar_Campo_Tabela("D172","D170_Flag_Ativo","D172_Flag_Ativo","enum('S','N');");
Alter_Query("ALTER TABLE D174 DROP COLUMN D174_D173_Id;");
Alter_Query("ALTER TABLE D132 DROP COLUMN D132_Grid_Tabela;");
Alter_Query("ALTER TABLE D132 DROP COLUMN D132_DivId;");
Criar_Campo("T276_UF_Percurso","varchar(50)");
Criar_Campo("T003_T134_Id","int(5) default 0");
Criar_Campo("T003_Valor_Total_ICMS_Credito","decimal(14,2) default 0.00");
Criar_Campo("T003_Aliquota_ICMS_Credito","decimal(14,2) default 0.00");
Criar_Campo("T005_Valor_Total_ICMS_Credito","decimal(14,2) default 0.00");
Criar_Campo("T005_Aliquota_ICMS_Credito","decimal(14,2) default 0.00");
Criar_Campo("D074_D001_Id_Composicao","int(10) unsigned");
Criar_Campo("T261_D001_Id_Composicao","int(10) unsigned");


//Criação de rotinas Lígia


//Correçoes campos MDf-e-----------------------------------------------------
//tabela D170
Alterar_Campo_Tabela("D170","T276_Placa","D170_Placa","varchar(8)");
Alterar_Campo_Tabela("D170","T276_Renavam","D170_Renavam","varchar(11)");
Alterar_Campo_Tabela("D170","T276_Tara","D170_Tara","int(6)");
Alterar_Campo_Tabela("D170","T276_Capacidade_Kg","D170_Capacidade_Kg","int(6)");
Alterar_Campo_Tabela("D170","T276_Capacidade_M3","D170_Capacidade_M3","int(6)");
Alterar_Campo_Tabela("D170","T276_Flag_Rodado","D170_Flag_Rodado","int(3)");
Alterar_Campo_Tabela("D170","T276_Flag_Carroceria","D170_Flag_Carroceria","int(3)");
Alterar_Campo_Tabela("D170","T276_Categoria","D170_Categoria","int(1)");
Alterar_Campo_Tabela("D170","T276_D019_Id","D170_D109_Id","int(10) unsigned");
Criar_Campo("D170_Identificacao_Veiculo","varchar(100)");
Criar_Campo("D170_Flag_Ativo","enum('S', 'N') default 'S'");
Criar_Campo("D170_D018_Id","int(10)");
Excluir_Campo("D170","T276_UF_Licenciamento");
Criar_Indice('D170_D109_Id');
Criar_Indice('D170_D018_Id');
Criar_Indice('T005_Canal_Vendas_Ecommerce');

Criar_Indice('T139_T007_Id');

Criar_Indice('D069_Data_Atualizacao_Preco');
Criar_Indice('D069_Data_Atualizacao_Estoque');

//tabela D172
Criar_Indice('D172_D020_Id');
Criar_Indice('D172_D018_Id');
Criar_Campo("D172_Flag_Ativo","enum('S', 'N') default 'S'");

//tabela T278
Alterar_Campo("T278_D019_Id","T278_D109_Id","int(10)");
Criar_Indice('T278_D109_Id');
Criar_Indice('T278_D109_Id');

//tabela T284
Alterar_Tipo_Campo("T284_Valor_Carga","decimal (13,2)");
Criar_Indice('T284_D020_Id');
Criar_Indice('T284_D018_Id');
Criar_Campo("T284_Cidade_Descarregamento_Codigo_IBGE","int(7)");

//tabela T281
Criar_indice('T281_T284_Id');

//tabela T282
Criar_indice('T282_T281_Id');

//tabela T280
Criar_indice('T280_D171_Id');
Criar_indice('T280_T276_Id');
Criar_indice('T280_T281_Id');
Criar_indice('T280_T282_Id');
Criar_indice('T280_T283_Id');

//tabela T280
Excluir_Campo("T280","T280_T281_Id");
Excluir_Campo("T280","T280_T282_Id");
Excluir_Campo("T280","T280_T283_Id");
Excluir_Campo("T280","T280_Numero_Lacre");
Excluir_Campo("T280","T280_T276_Lacre_MDFe_Id");

//tabela T276
Alterar_Campo("T276_D019_Id","T276_D109_Id","int(10) unsigned");

//Correçoes campos MDf-e-----------------------------------------------------

Alterar_Tipo_Campo("T252_C007_Id_Cliente","int(11) unsigned NOT NULL");
Alterar_Tipo_Campo("D022_Descricao_Ecommerce","varchar(250) DEFAULT ''");
Alterar_Tipo_Campo("D036_Descricao_Ecommerce","text DEFAULT ''");
Alterar_Tipo_Campo("T005_Data_Hora_Envio_Ecommerce","datetime default '0000-00-00 00:00:00'");
Alterar_Campo("D168_Id_Transportadora_Intelipost","D168_Id_Transportadora_Ecommerce","int(10) unsigned");
Alterar_Campo("D168_Nome_Transportadora_Intelipost","D168_Nome_Transportadora_Ecommerce","varchar(150) default ''");
Alterar_Campo("D171_Descrição","D171_Descricao","varchar(100)");


// Criação de campos chamado 33655 - Criação da tag vIPIDevol
Criar_Campo("T008_Valor_IPI_Devolucao", "decimal(14,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_IPI_Devolucao", "decimal(14,2) DEFAULT '0.00'");

// Criação de campo chamado 34095 - Criar campo T075_Orgao_Desembaraco
Criar_Campo("T075_Orgao_Desembaraco","varchar(100) default ''");

//trello
Criar_Campo("C007_Key_Trello","varchar(100)");
Criar_Campo("C007_Token_Trello","varchar(100)");
Criar_Campo("C007_Id_Usuario_Trello","varchar(100)");
Criar_Campo("T234_IdCard","varchar(100)");
Criar_Campo("T234_Retorno_Card","varchar(100)");
Criar_Campo("D166_Id_Quadro_Trello","varchar(100)");
Criar_Campo("D167_Id_Lista_Trello","varchar(100)");
Alterar_Tipo_Campo("T273_Link_Anexo","varchar(200) DEFAULT ''");
Criar_Campo("T252_Detalhe_Trello_Id","varchar(100) default ''");
Criar_Campo("D167_Mover_Lista_Id","int(10) unsigned");
Criar_Campo("T253_IdAnexo","varchar(100)");
Alterar_Tipo_Campo("T253_IdAnexo","varchar(100) default ''");
Criar_Campo("D166_Nome_Time","varchar(100) default ''");
Criar_Campo("T234_Avaliacao_Cliente","int(1) unsigned");
Criar_Campo("T234_Avaliacao_Cliente_Data","date");
Alterar_Tipo_Campo("T234_Avaliacao_Cliente_Data","date default '0000-00-00'");
Alterar_Tipo_Campo("T234_Avaliacao_Cliente","int(1) default '0'");
Criar_Campo("T155_T168_Id","int(10) unsigned");
Criar_Campo("T234_Flag_Arquivado","char(1) default'N'");
Criar_Campo("D167_Flag_Enviar_Detalhes","char(1) default'N'");
Alterar_Tipo_Campo("T234_IdCard","varchar(100) default ''");
Criar_Campo("D166_Flag_Melhoria","char(1) default'N'");
Criar_Campo("T234_Horas_Atendimento","time");
Criar_Campo("D166_T272_Id","int(10) unsigned");
Criar_Campo("D166_Lista_Analise_Id","int(10) unsigned");
Alterar_Tipo_Campo("D166_T272_Id","int(10) default '0'");
Criar_Campo("T234_Chamado_Origem_Id","int(10) unsigned");
Criar_Campo("D166_Flag_Sincronizar_Trello","char(1) default'S'");
Alterar_Tipo_Campo("T234_C007_Id_Programador", "varchar(150) default ''");
Alterar_Campo("T274_Total_Horas_trabalhadas ","T274_Total_Horas_Trabalhadas ","time default '00:00:00'");
Criar_Campo("T234_Horas_Trabalho","time default '00:00:00'");
Criar_Campo("T234_Horas_ReTrabalho","time default '00:00:00'");
Criar_Campo("T234_Horas_Pausado","time default '00:00:00'");
Criar_Campo("T234_Total_Horas_Atendimento","time default '00:00:00'");
Criar_Campo("D167_Flag_Finalizar_Chamado","char(1) default'N'");

//Suporte Parceiro
Criar_Campo("T234_T234_Vinculo_Id","int(10) unsigned");
Criar_Campo("T252_T252_Vinculo_Id","int(10) unsigned");
Criar_Campo("T253_T253_Vinculo_Id","int(10) unsigned");
Criar_Campo("T234_T234_Data_Abertura_Parceiro","datetime");
Criar_Campo("T234_D024_Parceiro_Link","varchar(255) default ''");
Criar_Campo("T234_D024_Parceiro_Nome","varchar(255) default ''");
Criar_Campo("T234_Info_Parceiro","text");
Criar_Campo("T234_Dominio_Parceiro","varchar(150) default ''");

Criar_Campo("T251_Nivel_Acesso","int(1)");
Criar_Campo("T251_Aliquota_Editavel","decimal(5,2) default '0.00'");
Criar_Campo("T251_Flag_Campo_Calculado","char(1) DEFAULT 'N'");
Criar_Campo("T251_Flag_Altera_Preco","char(1) DEFAULT 'N'");
Alterar_Tipo_Campo("C004_Flag_Ativo","char(1) DEFAULT 'S'");

Alterar_Tipo_Campo("T002_Numero_Cheque","varchar(50)");
Alterar_Tipo_Campo("D049_Origem_Mercadoria","varchar(10)");
Alterar_Tipo_Campo("T004_Descricao_Produto","varchar(120)");
Alterar_Tipo_Campo("T006_Descricao_Produto","varchar(120)");
Alterar_Tipo_Campo("D032_Descricao","varchar(50)");

Alterar_Tipo_Campo("T004_Flag_Cancelado","char(1) DEFAULT 'N'");

//Alterar_Tipo_Campo("T006_Percentual_Desconto","decimal(10,5)");
Alterar_Tipo_Campo("T002_Nome_Cliente","varchar(100)");
Alterar_Tipo_Campo("T019_Complemento","varchar(100)");

//Alterar_Tipo_Campo("D137_Produzido_Adquirido","varchar(2)");
//mysqli_query("UPDATE D137 SET D137_Produzido_Adquirido=IF(D137_Produzido_Adquirido='A', 'CP', IF(D137_Produzido_Adquirido='P', 'PD', ''));");

Alterar_Tipo_Campo("D007_Nome_Banco","varchar(30)");
Criar_Campo("D022_Taxa_Frete","decimal(5,2) default '0.00'");
Criar_Campo("T002_Banco_Cheque","varchar(50)");
Criar_Campo("T002_Agencia_Cheque","varchar(50)");
Criar_Campo("T002_Conta_Corrente_Cheque","varchar(50)");
Criar_Campo("T002_Nome_Cheque","varchar(50)");
Criar_Campo("T002_Data_Cheque","date");
Criar_Campo("T002_Cidade_Cheque","varchar(50)");
Criar_Campo("T147_D024_Id","int(10) unsigned");
Criar_Campo("T147_Flag_Novo","char(1) DEFAULT 'S'");
Criar_Campo("D116_D133_Id","int(10) unsigned");
Criar_Campo("D116_D134_Id","int(10) unsigned");
Criar_Campo("T005_Observacao_Pagamento","text");
Criar_Campo("T007_Observacao_Pagamento","text");
Criar_Campo("T147_Flag_Conferido","char(1) DEFAULT 'N'");
Criar_Campo("T002_T176_Id","int(10) unsigned");
Criar_Campo("T173_Valor_Cotacao","DECIMAL(14,5)");
Criar_Campo("T173_Valor_Confirmado","DECIMAL(14,5)");
Criar_Campo("I001_Descricao_Requisito","text");
Criar_Campo("I001_Modulo","varchar(15)");
Alterar_Tipo_Campo("I001_Previsao_Analise_Data","date");
Alterar_Tipo_Campo("I002_Previsao_OS_Data","date");
Criar_Campo("I002_Email_Enviado","text");
Criar_Campo("T002_Data_Emissao_Promissoria","date");
Criar_Campo("T002_T075_Id","int(10) unsigned");
Criar_Campo("T002_Total_Parcelas","int(5) unsigned");
Criar_Campo("T015_Total_Parcelas","int(5) unsigned");
Criar_Campo("T002_Valor_Original","decimal(14,2) unsigned");
Criar_Campo("T002_Valor_Taxas","decimal(14,2) default '0.00'");
Criar_Campo("T015_Valor_Original","decimal(14,2) unsigned");
Criar_Campo("T015_Valor_Saldo","decimal(14,2) unsigned");
Criar_Campo("T002_Valor_Saldo","decimal(14,2) unsigned");
Criar_Campo("T015_Data_Vencimento_Original","date default '0000-00-00'");
Criar_Campo("T003_Flag_Status_Orcamento","char(1) DEFAULT 'P'");
Criar_Campo("T075_Ordem_Compra","varchar(30)");
Criar_Campo("T013_T075_Id","int(10) unsigned");
Criar_Campo("T006_Quantidade_Entregar_Temp","int(10) DEFAULT 0");
Criar_Campo("T014_Quantidade_Alocada","int(10) DEFAULT 0");
Criar_Campo("T180_Cidade_Cheque"," varchar(50)");
Criar_Campo("T180_Numero_Cheque"," varchar(50)");
Criar_Campo("T180_Valor_Acrescimo"," DECIMAL(10,2)");
Criar_Campo("T180_Valor_Desconto"," DECIMAL(10,2)");
Criar_Campo("T003_Valor_Troca","decimal(10,2)");
Criar_Campo("T005_Valor_Troca","decimal(10,2)");
Criar_Campo("T138_T003_Id","int(10) unsigned");
Criar_Campo("T138_T005_Id","int(10) unsigned");
Criar_Campo("T138_C007_Id_Inspecao","int(10) unsigned");
Criar_Campo("T138_Operacao","char(1)");
Criar_Campo("T138_Data_Inspecao","datetime");
Criar_Campo("T180_Numero_Duplicata"," varchar(255)");
Alterar_Campo("T180_Numero_Duplicata","T180_Numero_Documento","varchar(255)");
Alterar_Campo("T139_T137_Id","T139_T173_Id","int(10)");
Criar_Campo("T180_Data_Emissao","date");
Criar_Campo("T145_T004_Id","int(10) unsigned");
Criar_Campo("T147_T006_Id","int(10) unsigned");
Criar_Campo("T090_Taxa_Juros","DECIMAL(12,2)");
Criar_Campo("T090_Valor_Juros","DECIMAL(12,2)");
Criar_Campo("C004_Taxa_Juros","DECIMAL(12,2) DEFAULT 0.00");
Criar_Campo("C004_Tipo_Juros","char(1) DEFAULT 'S'");
Criar_Campo("C004_Parcelas_Isentas_Juros","int(10) DEFAULT 99");
Criar_Campo("T089_Taxa_Juros","DECIMAL(12,2)");
Criar_Campo("T089_Valor_Parcela_Original","DECIMAL(12,2)");
Criar_Campo("T089_Valor_Juros","DECIMAL(12,2)");
Criar_Campo("T182_Status","char(1) DEFAULT 'P'");
Criar_Campo("T182_Marcado","char(1) DEFAULT 'N'");
Criar_Campo("T182_Tipo","char(1)");
Criar_Campo("T182_NF_Venda","char(20)");
Criar_Campo("T182_Pedido","char(20)");
Criar_Campo("T182_NF_Remessa_Cliente","char(20)");
Criar_Campo("T182_NF_Remessa_Fornecedor","char(20)");
Criar_Campo("T182_NF_Retorno_Fornecedor","char(20)");
Criar_Campo("T182_NF_Retorno_Cliente","char(20)");
Criar_Campo("T182_NF_Retorno_Cliente","char(20)");
Criar_Campo("T182_D024_Id_Cliente","varchar(11) not null");
Criar_Campo("T182_D024_Id_Fornecedor","varchar(11) not null");
Criar_Campo("T182_D013_Id_Cliente","varchar(11) not null");
Criar_Campo("T182_D013_Id_Fornecedor","varchar(11) not null");
Criar_Campo("T184_Tipo","char(1)");
Criar_Campo("T182_Tipo","char(1)");
Alterar_Tipo_Campo("T182_Tipo","char(2)");
Criar_Campo("T184_T182_Id","int(10) unsigned");
Criar_Campo("T184_T183_Id","int(10) unsigned");
Criar_Campo("T147_D109_Id_Conf","int(10) unsigned");
Criar_Campo("T147_C007_Id_Conf","int(10) unsigned");
Criar_Campo("D136_D037_Id","int(10) unsigned");
Criar_Campo("D136_Flag_Mercado_Livre","char(1) DEFAULT 'N'");
Criar_Campo("D020_D022_Id","int(10) unsigned");
Criar_Campo("T003_Valor_Desconto_Manual","DECIMAL(14,2)");
Criar_Campo("T003_Valor_Desconto_Manual_Confirmado","DECIMAL(14,2)");
Criar_Campo("T005_Valor_Desconto_Manual","DECIMAL(14,2)");
Criar_Campo("T003_Hora_Inicio_Servico","varchar(30)");
Criar_Campo("T003_Hora_Fim_Servico","varchar(30)");
Criar_Campo("T003_Flag_Aguardando_Entrega","char(1) default 'S'");
Criar_Campo("T003_Flag_PDF_Impresso","int(1)");
Criar_Campo("T005_C007_Id_Separou","int(10) unsigned");
Criar_Campo("T005_Hora_Inicio_Servico","varchar(30)");
Criar_Campo("T005_Hora_Fim_Servico","varchar(30)");
Criar_Campo("T005_Flag_Aguardando_Entrega","char(1) default 'S'");
Criar_Campo("D004_Flag_Tipo", "int (10) unsigned");
Criar_Campo("D004_Flag_Somar_Estoque_Fisico", "char(1) default 'S'");
Criar_Campo("T066_C007_Id_Retirou", "int (10) unsigned");
Criar_Campo("T030_D004_Id", "int (10) unsigned");
Criar_Campo("T066_Retirou_Qtd", "DECIMAL(20,5)");
Criar_Campo("T059_T189_Id","int(10) unsigned");
Criar_Campo("T189_D024_Id","int(10) unsigned");
Criar_Campo("T189_Quantidade_Estoque_Atual","DECIMAL(8,2)");
Criar_Campo("T012_T189_Id","int(10) unsigned");
Criar_Campo("T012_T174_Id","int(10) unsigned");

Criar_Campo("T030_D069_Id","int(10) unsigned");
Criar_Campo("D137_Flag_Cabecalho_Item","char(1) DEFAULT 'I'");
Criar_Campo("T003_Flag_Contribuinte","int(5) DEFAULT NULL");
Criar_Campo("T005_Flag_Contribuinte","int(5) DEFAULT NULL");
Criar_Campo("T005_Flag_Esta_No_Caixa","varchar(1) DEFAULT 'S'");
Criar_Campo("T191_Flag_Lido","varchar(1) DEFAULT 'N'");
Criar_Campo("D009_Flag_Imprimir_Etiqueta","char(1) default 'N'");
Criar_Campo("D004_Flag_Imprimir_Etiqueta","char(1) default 'N'");
Criar_Campo("D024_Flag_Imprimir_Etiqueta","char(1) default 'N'");
Criar_Campo("T003_Flag_Cortesia","char(1) default 'N'");
Criar_Campo("T147_Flag_Dividir","char(1) default 'N'");
Criar_Campo("T145_Flag_Dividir","char(1) default 'N'");
Criar_Campo("T138_Flag_Dividir","char(1) default 'N'");
Criar_Campo("T191_C007_Id_Lido","int(10) DEFAULT '0'");
Criar_Campo("T191_C007_Id_De","int(10) DEFAULT '0'");
Criar_Campo("T191_C007_Id_Para","int(10) DEFAULT '0'");
Criar_Campo("T175_D109_Codigo_Barras","VARCHAR(255)");
Alterar_Campo("T175_Flag_Status","T175_Flag_Operacao","CHAR(1)");
Alterar_Campo("T174_IPV","T174_IPV","DECIMAL(14,3)");
Criar_Campo("D006_Aliquota_ICMS","decimal(10,2) DEFAULT '0'");
Criar_Campo("D006_ST_ICMS_Inter_Estadual","decimal(10,2) DEFAULT '0'");
Criar_Campo("D006_ST_ICMS_Interno","decimal(10,2) DEFAULT '0'");
Criar_Campo("D006_ST_ICMS_Inter_Estadual_MVA","decimal(10,2) DEFAULT '0'");
Criar_Campo("D006_ST_ICMS_Interno_MVA","decimal(10,2) DEFAULT '0'");
Criar_Campo("D137_D005_Id","int(10) DEFAULT '0'");
Criar_Campo("D003_Imagem","varchar(100)");
Criar_Campo("T005_Flag_Status_Servicos","int(1) DEFAULT '1'");
Criar_Campo("T005_Flag_Status_Financeiro","int(1) DEFAULT '1'");
Criar_Campo("T006_Flag_Comissao","char(1) default 'N'");
Criar_Campo("T138_Flag_Comissao","char(1) default 'N'");
Criar_Campo("T147_Flag_Comissao","char(1) default 'N'");
Criar_Campo("D027_Flag_Comercial","char(1) default 'N'");
Criar_Campo("D009_Estoque_Defeito","DECIMAL(8,2)");
Criar_Campo("D009_Frequencia_Venda_Meses","decimal(5,2)");
Criar_Campo("D009_Estoque_Mostruario","DECIMAL(8,2)");
Criar_Campo("T004_ST_ICMS_Interno_MVA","decimal(14,2)");
Criar_Campo("T004_ICMS_Inter_Estadual_MVA","decimal(14,2)");
Criar_Campo("T006_ST_ICMS_Interno_MVA","decimal(14,2)");
Criar_Campo("T006_ICMS_Inter_Estadual_MVA","decimal(14,2)");
Criar_Campo("T189_C007_Id_Comprar","int(10)");
Criar_Campo("T189_C007_Id_Cotar","int(10)");
Criar_Campo("T139_T010_Id","int(10)");
Criar_Campo("T174_Flag_Email_Enviado","char(1) default 'N'");
//Criar_Campo("T174_Percentual_ICMS","decimal(10,2) default '0.00'");
Alterar_Campo("T005_Flag_Lista_Preco","T005_Flag_Lista_Preco","varchar(1) DEFAULT '1'");
Alterar_Campo("T003_Flag_Lista_Preco","T003_Flag_Lista_Preco","varchar(1) DEFAULT '1'");
Criar_Campo("D009_Observacao_Venda","VARCHAR(255)");
Criar_Campo("T075_Data_Estoque","date");
Criar_Campo("T004_T075_Id","int(10)");
Criar_Campo("T098_D049_IPV_1","DECIMAL(14,2)");
Criar_Campo("T098_D049_IPV_2","DECIMAL(14,2)");
Criar_Campo("T098_D049_ICF_1","DECIMAL(14,2)");
Criar_Campo("T098_D049_ICF_2","DECIMAL(14,2)");
Criar_Campo("T098_D009_Valor_Custo_Tabela","DECIMAL(14,6)");
Criar_Campo("D049_Flag_Promocao","CHAR(1) DEFAULT 'N'");
Criar_Campo("T098_Flag_Promocao","CHAR(1) DEFAULT 'N'");
Criar_Campo("D049_Preco_1","decimal(14,2)");
Criar_Campo("D049_Preco_2","decimal(14,2)");
Criar_Campo("D049_Preco_3","decimal(14,2)");
Criar_Campo("T098_D049_Preco_1","decimal(14,2)");
Criar_Campo("T098_D049_Preco_2","decimal(14,2)");
Criar_Campo("T098_D049_Preco_3","decimal(14,2)");
Criar_Campo("T001_Data_Validade","date");
Criar_Campo("T001_Data_Fabricacao","date");
Criar_Campo("T001_Saldo_Validade","decimal(10,2)");
Criar_Campo("T001_Lote_Validade","varchar(50)");
Criar_Campo("D049_Data_Validade","date");
Criar_Campo("D049_Data_Fabricacao","date");
Criar_Campo("D049_Lote","varchar(50)");
//Criar_Campo("T173_Codigo_Incoterm","char(3)");
Criar_Campo("T084_Tipo_Nota","varchar(10) DEFAULT '' ");

//Inserindo novos campos para a Tabela T084 referente ao XML
Criar_Campo("T084_Numero_Protocolo_Cte","varchar(20) DEFAULT '' ");

Criar_Campo("T001_Valor_ICMS","decimal(10,2)");
Criar_Campo("T001_Aliquota_ICMS","decimal(10,2)");
Criar_Campo("T001_Valor_IPI","decimal(10,2)");
Criar_Campo("T001_Aliquota_IPI","decimal(10,2)");
Criar_Campo("T001_Valor_ST","decimal(10,2)");
Criar_Campo("T001_Percentual_ST","decimal(10,2)");
Criar_Campo("T001_Valor_Frete","decimal(10,2)");
Criar_Campo("T001_Percentual_Frete","decimal(10,2)");
Criar_Campo("T001_Valor_Custo_Financeiro","decimal(10,2)");
Criar_Campo("T001_Percentual_Custo_Financeiro","decimal(10,2)");
Criar_Campo("T001_Valor_ST_Unitario","decimal(10,5) NOT NULL DEFAULT 0.00000");
Criar_Campo("T001_Valor_Preco_Fob","decimal(15,5)");
Criar_Campo("T001_T219_Id","int(10) unsigned");
Criar_Campo("T001_Valor_Seguro","decimal(10,2)");
Criar_Campo("T001_Valor_Despesas_Acessorias","decimal(10,2)");
Criar_Campo("T001_Valor_Desconto_Unitario","decimal(10,2)");
Criar_Campo("T001_Valor_ICMS_Diferenca_Aliquotas","decimal(15,5)");

Alterar_Tipo_Campo("T008_Percentual_Desconto","decimal(14,4) default 0");
 
Criar_Campo("T174_Quantidade","int(10)");
Criar_Campo("T174_Descricao_Produto","VARCHAR(255)");
Criar_Campo("T174_Valor_ST","DECIMAL(14,2)");
Criar_Campo("T174_Quantidade_Forn","int(10)");
Criar_Campo("T174_Valor_Custo_Unitario_Forn","DECIMAL(14,2)");
Criar_Campo("T174_IPI_Forn","DECIMAL(14,2)");
Criar_Campo("T174_Hora_Email_Forn","datetime");
Criar_Campo("T174_Valor_Total","decimal(10,2)");
Criar_Campo("T174_IPV","decimal(14,2)");
Criar_Campo("T174_Preco_Venda","decimal(14,2)");
Criar_Campo("T174_Prazo_Entrega_Dias","VARCHAR(255)");
Criar_Campo("T174_D009_Id","int(10) unsigned");
Criar_Campo("T174_Observacao","varchar(255)");
Criar_Campo("T174_Flag_Excluido","char(1) default 'N'");
Alterar_Tipo_Campo("C007_Flag_Vendedor","enum('','N','S','V','D','C','P')");
Alterar_Tipo_Campo("T003_Flag_PDF_Impresso","char(1) default 'N'");
Alterar_Tipo_Campo("T192_Data","datetime");

Alterar_Tipo_Campo("T219_Descricao_Produto","varchar(250)");
Alterar_Tipo_Campo("T225_Descricao_Produto","varchar(250)");

Alterar_Tipo_Campo("T047_Flag_Status","varchar(1) default 'N'");

Alterar_Tipo_Campo("T002_Observacao_Reparcelamento","text not null");

Alterar_Campo("D002_Grupo","D002_D003_Id","Int(10) not null");
Criar_Campo("D002_Id_Integracao_Tray","int");
Criar_Campo("D002_Integrado_Tray","char(1)");
Criar_Campo("D003_Id_Integracao_Tray","int");
Criar_Campo("D003_Integrado_Tray","char(1)");
Criar_Campo("T144_Integrado_Tray","char(1)");
Criar_Campo("D185_Integrado_Tray","char(1)");
Criar_Campo("D185_Data_Hora_Ult_Envio","datetime");
Criar_Campo("D185_Erro_Ult_Envio","text");
Criar_Campo("D186_Integrado_Tray","char(1)");
Criar_Campo("D186_Data_Hora_Ult_Envio","datetime");
Criar_Campo("D186_Erro_Ult_Envio","text");
Criar_Campo("T172_Integrado_Tray","char(1)");

Alterar_Tipo_Campo("D024_Data_Cadastro","date");
Alterar_Tipo_Campo("D009_Flag_Promocao","char(2)");
Alterar_Tipo_Campo("D001_Flag_Ativo","varchar(1) default 'S'");

mysqli_query("update D001 set D001_Flag_Ativo='S' where D001_Flag_Ativo!='N'");

Criar_Campo("D006_ST_ICMS_Interno_Reducao","decimal(5,2) DEFAULT '0.00'");

Criar_Campo("D006_Flag_Calcula_FCP","varchar(1) DEFAULT 'N'");
// Task 60 — estes marcadores classificam somente a elegibilidade da CFOP
// para a inclusão manual assistida de GNRE. Não participam de totalização,
// XML, contas, vencimento, receita ou qualquer regra fiscal existente.
Criar_Campo("D006_Flag_Gera_GNRE_ST", "char(1) NOT NULL DEFAULT 'N'");
Criar_Campo("D006_Flag_Gera_GNRE_DIFAL", "char(1) NOT NULL DEFAULT 'N'");
Criar_Campo("D006_Flag_Gera_GNRE_FCP", "char(1) NOT NULL DEFAULT 'N'");

Criar_Campo("D014_Grupo_DRE","varchar(2) DEFAULT ''");
Criar_Campo("D001_Flag_Desenho_Tecnico","varchar(1) DEFAULT 'N'");

// campos obsoletos **/
Criar_Campo("T010_Flag_Alcada_Status","char(1) DEFAULT 'K'");
Criar_Campo("T010_Flag_Alcada_Nivel","char(1)");
Criar_Campo("T010_Motivo_Nao_Liberado","text");
Criar_Campo("T010_C007_Id_Liberou_Alcada","int(10) unsigned");
Criar_Campo("T010_Data_Liberou_Alcada","datetime");
Criar_Campo("T010_Valor_Pretendido_Alcada","decimal(10,2)");
Criar_Campo("T010_Valor_Liberou_Alcada","decimal(10,2)");
// campos obsoletos **/

Criar_Campo("T173_Flag_Alcada_Status","char(1) DEFAULT 'K'");
Criar_Campo("T173_Flag_Alcada_Nivel","char(1)");
Criar_Campo("T173_Motivo_Nao_Liberado","text");
Criar_Campo("T173_C007_Id_Liberou_Alcada","int(10) unsigned");
Criar_Campo("T173_Data_Liberou_Alcada","datetime");
Criar_Campo("T173_Valor_Pretendido_Alcada","decimal(10,2)");
Criar_Campo("T173_Valor_Liberou_Alcada","decimal(10,2)");
Criar_Campo("T173_Flag_EMail_Enviado","char(1) default 'N'");
Criar_Campo("T173_Flag_EMail_Enviado","char(1) default 'N'");
Criar_Campo("D137_Flag_Inativar","char(1) default 'N'");

//Campos para opção de reparcelamento e agrupamento de contas a pagar
Criar_Campo("T015_Flag_Reparcelar","enum('S','N') default 'N'");
Criar_Campo("D024_Flag_Enviar_Agenda","enum('S','N') default 'N'");
Criar_Campo("T015_T015_Id_Agrupado","int(10) default '0'");

Criar_Campo("T005_T005_Id_Agrupado","int(10) default '0'");

Criar_Campo("T006_T075_Id","int(10) unsigned");
Criar_Campo("T075_Data_Previsao","date");

Criar_Campo("T007_T224_Id","int(10) unsigned");
Criar_Campo("T202_T219_Id","int(10) unsigned");
Criar_Campo("T185_T225_Id","int(10) unsigned");
Criar_Campo("T204_T225_Id","int(10) unsigned");
Criar_Campo("T139_T218_Id","int(10) unsigned");
Criar_Campo("T139_T218_Id","int(10) unsigned");
Criar_Campo("T139_T224_Id","int(10) unsigned");
Criar_Campo("T219_T189_Id","int(10) unsigned");
Criar_Campo("T225_T189_Id","int(10) unsigned");
Criar_Campo("T225_Observacao_Entrega","varchar(50)");


Criar_Campo('T225_Valor_Total_Desconto_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T225_Valor_Total_Preco_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Total_Preco_Sem_Desconto_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Total_Frete_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T225_Valor_Total_Despesas_Acessorias_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T224_Valor_Frete_Recebida', "decimal(19,2)       DEFAULT  '0.00'");
Criar_Campo('T224_Valor_Despesas_Acessorias_Recebida', "decimal(10,2)       DEFAULT  '0.00'");
Criar_Campo('T225_Valor_Despesas_Acessorias_Unitario_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T225_Valor_Total_Frete_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T225_Valor_Frete_Unitario_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T225_Valor_Total_Custo_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Peso_Total_Recebida', "decimal(10,3)             DEFAULT '0.000'");
Criar_Campo('T225_Peso_Bruto_Total_Recebida', "decimal(10,3)             DEFAULT NULL");
Criar_Campo('T225_Valor_Total_Despesas_Recebida', "decimal(20,5)             DEFAULT '0.00000'");
Criar_Campo('T225_Valor_Despesas_Unitario_Recebida', "decimal(19,5)             DEFAULT '0.00000'");
Criar_Campo('T225_Valor_Base_IPI_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_IPI_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Total_Custo_Final_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Custo_Final_Unitario_Recebida', "decimal(19,5)             DEFAULT '0.00000'");
Criar_Campo('T225_Valor_Base_ICMS_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_ICMS_Recebida', "decimal(15,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Base_ICMS_Substituicao_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_ICMS_Substituicao_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_ICMS_Substituicao_Retencao_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Base_PIS_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_PIS_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_Base_COFINS_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_COFINS_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T225_Valor_ICMS_Credito_Recebida', "decimal(15,2)             DEFAULT '0.00'");
Criar_Campo('T225_Total_Cubagem_Recebida', "decimal(10,6)             DEFAULT '0.000000'");
Criar_Campo('T225_Valor_Trib_Transparencia_Recebida', "decimal(15,6)             DEFAULT '0.000000'");
Criar_Campo('T225_Flag_OC_Importada', "varchar(1) default 'N'");
Criar_Campo('T225_Valor_Custo_Unitario_Recebida', "decimal(19,5)             DEFAULT '0.00000'");
Criar_Campo('T225_Flag_Origem_Produto', "varchar(1) DEFAULT ''");

// Todos os campos de 'Recebidos' foram passados da T225 para a T014 (porque pode existir 2 T014_Id para 1 T225_Id)
Criar_Campo('T014_Valor_Total_Desconto_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T014_Valor_Total_Preco_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Total_Preco_Sem_Desconto_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Total_Frete_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T014_Valor_Total_Despesas_Acessorias_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T014_Valor_Despesas_Acessorias_Unitario_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T014_Valor_Total_Frete_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T014_Valor_Frete_Unitario_Recebida', "decimal(20,10)            DEFAULT '0.0000000000'");
Criar_Campo('T014_Valor_Total_Custo_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Peso_Total_Recebida', "decimal(10,3)             DEFAULT '0.000'");
Criar_Campo('T014_Peso_Bruto_Total_Recebida', "decimal(10,3)             DEFAULT NULL");
Criar_Campo('T014_Valor_Total_Despesas_Recebida', "decimal(20,5)             DEFAULT '0.00000'");
Criar_Campo('T014_Valor_Despesas_Unitario_Recebida', "decimal(19,5)             DEFAULT '0.00000'");
Criar_Campo('T014_Valor_Base_IPI_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_IPI_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Total_Custo_Final_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Custo_Final_Unitario_Recebida', "decimal(19,5)             DEFAULT '0.00000'");
Criar_Campo('T014_Valor_Base_ICMS_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_ICMS_Recebida', "decimal(15,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Base_ICMS_Substituicao_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_ICMS_Substituicao_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_ICMS_Substituicao_Retencao_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Base_PIS_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_PIS_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_Base_COFINS_Recebida', "decimal(20,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_COFINS_Recebida', "decimal(10,2)             DEFAULT '0.00'");
Criar_Campo('T014_Valor_ICMS_Credito_Recebida', "decimal(15,2)             DEFAULT '0.00'");
Criar_Campo('T014A_Aliquota_ICMS_Credito_Recebida', "decimal(7,4)             DEFAULT '0.0000'");
Criar_Campo('T014_Total_Cubagem_Recebida', "decimal(10,6)             DEFAULT '0.000000'");
Criar_Campo('T014_Valor_Trib_Transparencia_Recebida', "decimal(15,6)             DEFAULT '0.000000'");
Criar_Campo('T014_Valor_Custo_Unitario_Recebida', "decimal(19,5)             DEFAULT '0.00000'");
// fim

Criar_Campo("T218_Data_Finalizado","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T218_Data_Finalizado_Fornecedor","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T218_Flag_Notificacao","int(1) default 0");

// campos DDD de todos os telefones
Criar_Campo("C004_DDD_Telefone","int(2) unsigned");
Criar_Campo("C007_DDD_Telefone","int(2) unsigned");
Criar_Campo("C007_DDD_Telefone_Direto","int(2) unsigned");
Criar_Campo("C026_DDD_Telefone","int(2) unsigned");
Criar_Campo("D010_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D010_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D011_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D011_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D012_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D012_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D013_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D013_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D021_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D021_Nome_Fantasia","varchar(50)");
Criar_Campo("D021_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D021_E_Mail","varchar(254) DEFAULT NULL COMMENT 'E-mail de acesso ao portal do representante'");
Criar_Campo("D022_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D022_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D023_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D023_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D024_DDD_Entrega_Telefone","int(2) unsigned");
Criar_Campo("D024_DDD_Cobranca_Telefone","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Comercial_1","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Comercial_2","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Comercial_3","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Comercial_4","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Comercial_5","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_1","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_2","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_3","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Banco_1","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Banco_2","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Banco_3","int(2) unsigned");
Criar_Campo("D061_DDD_Telefone_Banco","int(2) unsigned");
Criar_Campo("D061_DDD_Eng_Amigo_Telefone","int(2) unsigned");
Criar_Campo("D024_Mail_NFe","varchar(255)");
Criar_Campo("D024_Link","varchar(255)");



// campos DDD de todos os celulares
Criar_Campo("C007_DDD_Celular","int(2) unsigned");
Criar_Campo("D010_DDD_Celular","int(2) unsigned");
Criar_Campo("D011_DDD_Celular","int(2) unsigned");
Criar_Campo("D012_DDD_Celular","int(2) unsigned");
Criar_Campo("D013_DDD_Celular","int(2) unsigned");
Criar_Campo("D024_DDD_Celular","int(2) unsigned");
Criar_Campo("D061_DDD_Celular_1","int(2) unsigned");
Criar_Campo("D061_DDD_Celular_2","int(2) unsigned");
Criar_Campo("D061_DDD_Celular_3","int(2) unsigned");

// campos email do usuario
Criar_Campo("C007_Email_SMTP_Servidor","varchar(50)");
Criar_Campo("C007_Email_SMTP_Autenticacao","varchar(10)");
Criar_Campo("C007_Email_SMTP_Usuario","varchar(50)");
Criar_Campo("C007_Email_SMTP_Senha","varchar(50)");
Criar_Campo("C007_Email_SMTP_Porta","varchar(10)");
Criar_Campo("C007_Email_POP_Servidor","varchar(50)");
Criar_Campo("C007_Email_SMTP_Seguranca","int(2) unsigned  DEFAULT '0' COMMENT '0: Automático (atual) - 1: SSL - 2: TLS'");

// campos DDD de todos os FAX
Criar_Campo("C004_DDD_Fax","int(2) unsigned");
Criar_Campo("D010_DDD_Fax","int(2) unsigned");
Criar_Campo("D011_DDD_Fax","int(2) unsigned");
Criar_Campo("D012_DDD_Fax","int(2) unsigned");
Criar_Campo("D013_DDD_Fax","int(2) unsigned");
Criar_Campo("D021_DDD_Fax","int(2) unsigned");
Criar_Campo("D022_DDD_Fax","int(2) unsigned");
Criar_Campo("D023_DDD_Fax","int(2) unsigned");
Criar_Campo("D024_DDD_Fax","int(2) unsigned");
Criar_Campo("D024_DDD_Cobranca_Fax","int(2) unsigned");
Criar_Campo("D024_DDD_Entrega_Fax","int(2) unsigned");
Criar_Campo("D061_DDD_Fax_1","int(2) unsigned");
Criar_Campo("D006_Tipo_CFOP","varchar(30)");
Criar_Campo("T189_Flag_Tipo","int(10)");
Criar_Campo("T189_Flag_Cotar","varchar(1) default 'N'");
Criar_Campo("T189_Flag_Comprar","varchar(1) default 'N'");
Criar_Campo("T174_Flag_Indisponivel","varchar(1) default 'N'");
Criar_Campo("D006_Flag_Somar_Despesas_Base_ST","varchar(1) default 'S'");
Criar_Campo("T005_T134_Id","int(10) unsigned");
Criar_Campo("T006_D022_Id","int(10) unsigned");
Criar_Campo("T006_T208_Id","int(10) unsigned");
Criar_Campo("T006_Valor_Total_Agregados","decimal(10,2) DEFAULT '0'");
Criar_Campo("T005_Valor_Total_Agregados","decimal(10,2) DEFAULT '0'");
Criar_Campo("T004_Valor_Total_Agregados","decimal(10,2) DEFAULT '0'");
Criar_Campo("T003_Valor_Total_Agregados","decimal(10,2) DEFAULT '0'");
Criar_Campo("C004_Flag_Rateio_Frete","varchar(1) default '1'"); // 1 = Rateio proporcional ao valor do produto  2 = Rateio igual para todos os produtos
Criar_Campo("D049_Percentual_Acrescimo_Tabela","decimal(10,2) default '0.00'"); 
Criar_Campo("D049_IPV1","decimal(10,4) default '0.0000'"); 
Criar_Campo("D049_IPV2","decimal(10,4) default '0.0000'"); 
Criar_Campo("D009_Valor_Custo_Tabela","decimal(14,6) default '0.000000'"); 
//Novo tributo de importação
Criar_Campo("T075_Valor_AFRMM","DECIMAL(14,2)");
Criar_Campo("T055_Valor_AFRMM","DECIMAL(14,2)");
Criar_Campo("T055_Valor_Total_AFRMM","DECIMAL(14,2)");
Criar_Campo("T006_T206_Id","int(10) unsigned");
Criar_Campo("T004_T206_Id","int(10) unsigned");
Criar_Campo("T006_T219_Id","int(10) unsigned");
Criar_Campo("T006_T225_Id","int(10) unsigned");
Criar_Campo("T219_T004_Id","int(10) unsigned");
Criar_Campo("T219_T006_Id","int(10) unsigned");
Criar_Campo("D001_Observacao_Montagem","text");
Criar_Campo("D006_UF_ICMS_Substituicao_Devido","char(2)");
Criar_Campo("D006_Percentual_Base_Operacao_Propria","decimal(5,2)");
Criar_Campo("D001_CNPJ_Fabricante","varchar(20)");

Criar_Campo("D027_Flag_Cupom_Gaveta","char(1) default 'N'");

Criar_Campo("D149_Observacao","text");
Criar_Campo("D151_Campo_Datas","VARCHAR(30)");
Criar_Campo("T084_D073_Id","int(10) ");
Criar_Campo("D053_Aliquota_ICMS_Importado","decimal(5,2) default 4");
Criar_Campo("D053_Aliquota_ICMS_Importado_2","decimal(5,2) default 4");
Criar_Campo("D053_Aliquota_ICMS_Importado_Compra","decimal(5,2) default 4");
Criar_Campo("T005_Validade_Proposta","varchar(50)");
Criar_Campo("T066_C007_Id_Marcou_Balanco","char(4)");
Criar_Campo("T001_Quantidado_Saldo_Locacao","decimal(10,4)");
Criar_Campo("D032_Tipo","varchar(2)");
Criar_Campo("D032_Flag_Ativo","varchar(1) default 'S'");
Criar_Campo("D007_Baixar_Automatico_Valor_Excedido_Retorno","varchar(1) default 'N'");
Criar_Campo("T008_Percentual_Trib_Federal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Trib_Federal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_Trib_Estadual","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Trib_Estadual","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_Trib_Municipal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Trib_Municipal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Trib_Federal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Trib_Estadual","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Trib_Municipal","DECIMAL(14,2) DEFAULT '0.00'");

Criar_Campo("T006_Percentual_Trib_Federal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T006_Percentual_Trib_Municipal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T006_Percentual_Trib_Estadual","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Trib_Federal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Trib_Municipal","DECIMAL(14,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Trib_Estadual","DECIMAL(14,2) DEFAULT '0.00'");

Criar_Campo("D036_Flag_Correios","varchar(1) default 'N'");
Criar_Campo("T174_ICMS","decimal(14,2)");
Criar_Campo("T135_Dia_Vencimento","char(2)");
Criar_Campo("T135_Mes_Inicial","char(2)");
Criar_Campo("T135_Numero_Parcelas","char(2)");
Criar_Campo("T135_Tipo_Documento","char(2)");
Criar_Campo("T135_Franquia_Horas", "int(3)");
Alterar_Tipo_Campo("T135_Franquia_Horas","time");
Criar_Campo("T135_Valor_Hora_Tecnica", "decimal(14,2)");
Criar_Campo("T135_Qtde_Maxima_Melhorias_Mes","char(4) default '3'");
Criar_Campo("C007_Tema","varchar(20)");

/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 19/09/2025
==============================================================*/
/* Cabeçalho
---------- */
// Valores impostos
Criar_Campo("T007A_Valor_Total_CBS", "decimal(15, 2) COMMENT 'Valor total do CBS'");
Criar_Campo("T007A_Valor_Total_IBS_Municipal", "decimal(15, 2) COMMENT 'Valor total do IBS Municipal'");
Criar_Campo("T007A_Valor_Total_IBS_UF", "decimal(15, 2) COMMENT 'Valor total do IBS UF'");
Criar_Campo("T007A_Valor_Total_IBS", "decimal(15, 2) COMMENT 'Valor total do IBS'");
Criar_Campo("T007A_Valor_Total_Diferimento_CBS", "decimal(15,2) DEFAULT 0.00 COMMENT 'Valor total do diferimento do CBS'");
Criar_Campo("T007A_Valor_Total_Diferimento_IBS_UF", "decimal(15,2) DEFAULT 0.00 COMMENT 'Valor total do diferimento do IBS UF'");
Criar_Campo("T007A_Valor_Total_Diferimento_IBS_Municipal", "decimal(15,2) DEFAULT 0.00 COMMENT 'Valor total do diferimento do IBS Municipal'");
Criar_Campo("T007A_Valor_Total_IBS_Transferido", "decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total do credito de IBS transferido na NF de debito'");
Criar_Campo("T007A_Valor_Total_CBS_Transferido", "decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total do credito de CBS transferido na NF de debito'");
Criar_Campo("T007A_Valor_Total_IBS_Ajuste_Competencia", "decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total do ajuste de competencia do IBS na NF de debito'");
Criar_Campo("T007A_Valor_Total_CBS_Ajuste_Competencia", "decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total do ajuste de competencia da CBS na NF de debito'");
Criar_Campo("T007A_Valor_Total_IBS_Estornado", "decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total do credito de IBS estornado na NF de debito'");
Criar_Campo("T007A_Valor_Total_CBS_Estornado", "decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total do credito de CBS estornado na NF de debito'");
Criar_Campo("T007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao","varchar(1) default 'N'");

// Outros
Criar_Campo("T007A_Valor_Total_Base_CBS_IBS", "decimal(15, 2) COMMENT 'Valor total da base do IBS/CBS'");
Criar_Campo("T007A_Valor_Total_Reforma", "decimal(15, 2) COMMENT 'Valor total da NF na reforma'");
Criar_Campo("T007A_Tipo_NF_Credito", "varchar(2) DEFAULT '' COMMENT 'Tipo da nota de credito da RTC (tag tpNFCredito)'");
Criar_Campo("T007A_Tipo_NF_Debito", "varchar(2) DEFAULT '' COMMENT 'Tipo da nota de debito da RTC (tag tpNFDebito)'");
Criar_Campo("T005A_Flag_Pagamento_Antecipado", "enum('N','S') NOT NULL DEFAULT 'N' COMMENT 'Pedido com NF de debito tipo 06 gerada; habilita a busca de antecipacoes no faturamento'");
Criar_Campo("T007A_T007_Id_Origem_NF_Debito_Credito", "int(10) unsigned DEFAULT NULL COMMENT 'NF-e autorizada de origem da NF de debito ou credito gerada pelo botao interno'");
Criar_Indice("T007A_T007_Id_Origem_NF_Debito_Credito");

/* Itens
---------- */
// Situação fiscal -----
Criar_Campo("T008A_Situacao_Tributaria_CBS_IBS", "varchar(3) COMMENT 'CST do produto.'");
Criar_Campo("T008A_Classificacao_Tributaria_CBS_IBS", "varchar(6) COMMENT 'Classificação tributária do produto. É influenciado pela CST.'");

// Regular
Criar_Campo("T008A_Situacao_Tributaria_CBS_IBS_Regular", "varchar(3) COMMENT 'CST regular do produto'");
Criar_Campo("T008A_Classificacao_Tributaria_CBS_IBS_Regular", "varchar(6) COMMENT 'Classificação tributária regular do produto. É influenciado pela CST.'");

// CBS -----
Criar_Campo("T008A_Aliquota_CBS", "decimal(7, 4) COMMENT 'Aliquota do CBS'");
Criar_Campo("T008A_Percentual_Reducao_CBS", "decimal(7, 4) COMMENT 'Percentual de redução do CBS'");
Criar_Campo("T008A_Aliquota_CBS_Efetiva", "decimal(7, 4) COMMENT 'Aliquota efetiva CBS: Resultado da aliquota mais o percentual de redução do CBS'");
Criar_Campo("T008A_Valor_CBS", "decimal(15, 2) COMMENT 'Valor do CBS'");
Criar_Campo("T008A_Percentual_Diferimento_CBS", "decimal(7,4) DEFAULT 0.00 COMMENT 'Percentual de diferimento do CBS'");
Criar_Campo("T008A_Valor_Diferimento_CBS", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do diferimento do CBS'");

// Regular
Criar_Campo("T008A_Aliquota_CBS_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota regular do CBS'");
Criar_Campo("T008A_Percentual_Reducao_CBS_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Percentual de redução regular do CBS'");
Criar_Campo("T008A_Aliquota_CBS_Efetiva_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Alíquota efetiva regular do CBS'");
Criar_Campo("T008A_Valor_CBS_Regular", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor regular do CBS'");

// IBS Municipal ----
Criar_Campo("T008A_Aliquota_IBS_Municipal", "decimal(7, 4) COMMENT 'Aliquota do IBS Municipal'");
Criar_Campo("T008A_Percentual_Reducao_IBS_Municipal", "decimal(7, 4) COMMENT 'Percentual de redução do IBS Municipal'");
Criar_Campo("T008A_Aliquota_IBS_Municipal_Efetiva", "decimal(7, 4) COMMENT 'Aliquota efetiva IBS Municipal: Resultado da aliquota mais o percentual de redução do IBS Municipal'");
Criar_Campo("T008A_Valor_IBS_Municipal", "decimal(15, 2) COMMENT 'Valor do IBS Municipal'");
Criar_Campo("T008A_Percentual_Diferimento_IBS_Municipal", "decimal(7,4) DEFAULT 0.00 COMMENT 'Percentual de diferimento do IBS Municipal'");
Criar_Campo("T008A_Valor_Diferimento_IBS_Municipal", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do diferimento do IBS Municipal'");

// Regular
Criar_Campo("T008A_Aliquota_IBS_Municipal_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota regular do IBS Municipal'");
Criar_Campo("T008A_Percentual_Reducao_IBS_Municipal_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Percentual de redução regular do IBS Municipal'");
Criar_Campo("T008A_Aliquota_IBS_Municipal_Efetiva_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Alíquota efetiva regular do IBS Municipal'");
Criar_Campo("T008A_Valor_IBS_Municipal_Regular", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor regular do IBS Municipal'");

// IBS UF ----
Criar_Campo("T008A_Aliquota_IBS_UF", "decimal(7, 4) COMMENT 'Aliquota do IBS UF'");
Criar_Campo("T008A_Percentual_Reducao_IBS_UF", "decimal(7, 4) COMMENT 'Percentual de redução do IBS UF'");
Criar_Campo("T008A_Aliquota_IBS_UF_Efetiva", "decimal(7, 4) COMMENT 'Aliquota efetiva IBS UF: Resultado da aliquota mais o percentual de redução do IBS UF'");
Criar_Campo("T008A_Valor_IBS_UF", "decimal(15, 2) COMMENT 'Valor do IBS UF'");
Criar_Campo("T008A_Percentual_Diferimento_IBS_UF", "decimal(7,4) DEFAULT 0.00 COMMENT 'Percentual de diferimento do IBS UF'");
Criar_Campo("T008A_Valor_Diferimento_IBS_UF", "decimal(15,2) DEFAULT 0.00 COMMENT 'Valor do diferimento do IBS UF'");

// Regular
Criar_Campo("T008A_Aliquota_IBS_UF_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota regular do IBS_UF'");
Criar_Campo("T008A_Percentual_Reducao_IBS_UF_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Percentual de redução regular do IBS_UF'");
Criar_Campo("T008A_Aliquota_IBS_UF_Efetiva_Regular", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Alíquota efetiva regular do IBS_UF'");
Criar_Campo("T008A_Valor_IBS_UF_Regular", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor regular do IBS_UF'");

// Outros ----
Criar_Campo("T008A_Valor_Base_CBS_IBS", "decimal(15, 2) COMMENT 'Base do CBS e do IBS UF/Municipal (Base única para todos os impostos)'");
Criar_Campo("T008A_Valor_Total_Reforma", "decimal(15, 2) COMMENT 'Valor final do item (será somado no valor total da NF T007A_Valor_Total_Reforma)'");
Criar_Campo("T008A_Valor_IBS", "decimal(15, 2) COMMENT 'Valor final do IBS: Soma IBS UF + IBS Municipal'");
Criar_Campo("T008A_Valor_IBS_Transferido", "decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do crédito de IBS transferido na NF de débito'");
Criar_Campo("T008A_Valor_CBS_Transferido", "decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do crédito de CBS transferido na NF de débito'");
Criar_Campo("T008A_Competencia_Ajuste", "varchar(7) NOT NULL DEFAULT '' COMMENT 'Competência de apuração (MM/AAAA) do ajuste de IBS/CBS; convertida para AAAA-MM no XML'");
Criar_Campo("T008A_Valor_IBS_Ajuste_Competencia", "decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ajuste de competência do IBS na NF de débito'");
Criar_Campo("T008A_Valor_CBS_Ajuste_Competencia", "decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ajuste de competência da CBS na NF de débito'");
Criar_Campo("T008A_Valor_IBS_Estornado", "decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do crédito de IBS estornado na NF de débito'");
Criar_Campo("T008A_Valor_CBS_Estornado", "decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do crédito de CBS estornado na NF de débito'");
Criar_Campo("T008A_Chave_Acesso_DFe_Referenciado", "varchar(44) DEFAULT '' COMMENT 'Chave de acesso do DFe referenciado no item para NF-e de credito/debito RTC'");
Criar_Campo("T008A_Item_DFe_Referenciado", "int(3) unsigned DEFAULT 0 COMMENT 'Numero do item do DFe referenciado para NF-e de credito/debito RTC'");

/* Novos campos da NCM (para o preenchimento automático das aliquotas) 
---------- */
Criar_Campo("D005_Situacao_Tributaria_CBS_IBS", "varchar(3) COMMENT 'CST do produto. Vai preeencher automaticamente conforme o NCM nos itens.'");
Criar_Campo("D005_Classificacao_Tributaria_CBS_IBS", "varchar(6) COMMENT 'Classificação tributária do produto. É influenciado pela CST. Vai preeencher automaticamente conforme o NCM nos itens.'");
Criar_Campo("D005_Situacao_Tributaria_CBS_IBS_Regular", "varchar(3) COMMENT 'CST do produto para tributação regular. Vai preeencher automaticamente conforme o NCM nos itens.'");
Criar_Campo("D005_Classificacao_Tributaria_CBS_IBS_Regular", "varchar(6) COMMENT 'Classificação tributária do produto para tributação regular. É influenciado pela CST. Vai preeencher automaticamente conforme o NCM nos itens.'");

/* Novos campos da CFOP (para o preenchimento automático das aliquotas) 
---------- */
Criar_Campo("D006_Situacao_Tributaria_CBS_IBS", "varchar(3) COMMENT 'CST do produto. Vai preeencher automaticamente conforme o CFOP nos itens.'");
Criar_Campo("D006_Classificacao_Tributaria_CBS_IBS", "varchar(6) COMMENT 'Classificação tributária do produto. É influenciado pela CST. Vai preeencher automaticamente conforme o CFOP nos itens.'");
Criar_Campo("D006_Situacao_Tributaria_CBS_IBS_Regular", "varchar(3) COMMENT 'CST do produto para tributação regular. Vai preeencher automaticamente conforme o NCM nos itens.'");
Criar_Campo("D006_Classificacao_Tributaria_CBS_IBS_Regular", "varchar(6) COMMENT 'Classificação tributária do produto para tributação regular. É influenciado pela CST. Vai preeencher automaticamente conforme o NCM nos itens.'");
Criar_Campo("D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma", "varchar(1) DEFAULT 'S' COMMENT 'Define se nao deve somar IBS e CBS no valor total da reforma do item'");

// Deduções da base IBS/CBS: manter o comportamento anterior nos cadastros existentes.
Criar_Campo("D006_Flag_Deduz_PIS_Base_IBS_CBS", "varchar(1) DEFAULT 'S' COMMENT 'Deduzir PIS da base IBS e CBS'");
Criar_Campo("D006_Flag_Deduz_COFINS_Base_IBS_CBS", "varchar(1) DEFAULT 'S' COMMENT 'Deduzir COFINS da base IBS e CBS'");
Criar_Campo("D006_Flag_Deduz_ICMS_Base_IBS_CBS", "varchar(1) DEFAULT 'S' COMMENT 'Deduzir ICMS próprio da base IBS e CBS'");
Criar_Campo("D006_Flag_Deduz_DIFAL_Base_IBS_CBS", "varchar(1) DEFAULT 'S' COMMENT 'Deduzir partilha ICMS destino (DIFAL) da base IBS e CBS'");
Criar_Campo("D006_Flag_Deduz_FCP_Base_IBS_CBS", "varchar(1) DEFAULT 'S' COMMENT 'Deduzir FCP próprio da base IBS e CBS'");
Criar_Campo("D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS", "varchar(1) DEFAULT 'S' COMMENT 'Deduzir partilha FCP destino da base IBS e CBS'");

/* Configurações */
/* Define se o sistema irá preencher os campos de reforma tributária automaticamente em vendas
0 - MANUAL
1 - AUTOMÁTICO PELA NCM */
Criar_Campo("C004_Flag_Tipo_Preenchimento_Reforma_Tributaria","char(1) DEFAULT '0' COMMENT 'Define se o sistema irá preencher os campos de reforma tributária automaticamente em vendas'");

// Split Payment: N = desabilitado; H = somente homologação; S = homologação e produção.
Criar_Campo("C004_Flag_Vinculacao_Pagamento_Split", "enum('N','H','S') NOT NULL DEFAULT 'N' COMMENT 'Habilita a vinculação de pagamento do Split por ambiente da NF-e/NFC-e'");

// Split Payment: oito dígitos da base do CNPJ do PSP responsável pela cobrança.
Criar_Campo("D062_CNPJ_Base_PSP", "varchar(8) DEFAULT NULL COMMENT 'Base de 8 dígitos do CNPJ do PSP para vinculação de pagamento Split'");

/* Fórmulas
---------- */

/* Alterações de tipo de campo
--------------------------------- */
Alterar_Tipo_Campo("T008A_Aliquota_CBS", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Aliquota_IBS_UF", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Aliquota_IBS_Municipal", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Aliquota_CBS_Efetiva", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Aliquota_IBS_UF_Efetiva", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Aliquota_IBS_Municipal_Efetiva", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Percentual_Reducao_CBS", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Percentual_Reducao_IBS_UF", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Percentual_Reducao_IBS_Municipal", "decimal(7, 4)", "DEFAULT 0.00");
// pDif: TDec_0302_04RTC no XSD da NT 2025.002 v1.51; atualizar também bases existentes.
Alterar_Tipo_Campo("T008A_Percentual_Diferimento_IBS_UF", "decimal(7, 4)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T007A_Valor_Total_CBS", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T007A_Valor_Total_IBS_Municipal", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T007A_Valor_Total_IBS_UF", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T007A_Valor_Total_IBS", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T007A_Valor_Total_Base_CBS_IBS", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T007A_Valor_Total_Reforma", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Valor_CBS", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Valor_IBS_UF", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Valor_IBS_Municipal", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Valor_Base_CBS_IBS", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Valor_Total_Reforma", "decimal(15, 2)", "DEFAULT 0.00");
Alterar_Tipo_Campo("T008A_Valor_IBS", "decimal(15, 2)", "DEFAULT 0.00");
/*===========================================================================================================================*/

/* [REFORMA TRIBUTÁRIA CTe]  - Gabriel Cegantini 19/09/2025
==============================================================*/
// Situação fiscal
Criar_Campo("T084A_Situacao_Tributaria_CBS_IBS", "varchar(3) COMMENT 'CST do produto.'");
Criar_Campo("T084A_Classificacao_Tributaria_CBS_IBS", "varchar(6) COMMENT 'Classificação tributária do produto. É influenciado pela CST.'");

// CBS
Criar_Campo("T084A_Aliquota_CBS", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota do CBS'");
Criar_Campo("T084A_Percentual_Reducao_CBS", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Percentual de redução do CBS'");
Criar_Campo("T084A_Aliquota_CBS_Efetiva", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota efetiva CBS: Resultado da aliquota mais o percentual de redução do CBS'");
Criar_Campo("T084A_Valor_CBS", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do CBS'");
Criar_Campo("T084A_Percentual_Diferimento_CBS", "decimal(7,4) DEFAULT 0.00 COMMENT 'Percentual de diferimento do CBS'");
Criar_Campo("T084A_Valor_Diferimento_CBS", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do diferimento do CBS'");

// IBS Municipal
Criar_Campo("T084A_Aliquota_IBS_Municipal", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota do IBS Municipal'");
Criar_Campo("T084A_Percentual_Reducao_IBS_Municipal", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Percentual de redução do IBS Municipal'");
Criar_Campo("T084A_Aliquota_IBS_Municipal_Efetiva", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota efetiva IBS Municipal: Resultado da aliquota mais o percentual de redução do IBS Municipal'");
Criar_Campo("T084A_Valor_IBS_Municipal", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do IBS Municipal'");
Criar_Campo("T084A_Percentual_Diferimento_IBS_Municipal", "decimal(7,4) DEFAULT 0.00 COMMENT 'Percentual de diferimento do IBS Municipal'");
Criar_Campo("T084A_Valor_Diferimento_IBS_Municipal", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do diferimento do IBS Municipal'");

// IBS UF
Criar_Campo("T084A_Aliquota_IBS_UF", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota do IBS UF'");
Criar_Campo("T084A_Percentual_Reducao_IBS_UF", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Percentual de redução do IBS UF'");
Criar_Campo("T084A_Aliquota_IBS_UF_Efetiva", "decimal(7, 4) DEFAULT 0.00 COMMENT 'Aliquota efetiva IBS UF: Resultado da aliquota mais o percentual de redução do IBS UF'");
Criar_Campo("T084A_Valor_IBS_UF", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor do IBS UF'");
Criar_Campo("T084A_Percentual_Diferimento_IBS_UF", "decimal(15,2) DEFAULT 0.00 COMMENT 'Percentual de diferimento do IBS UF'");
Criar_Campo("T084A_Valor_Diferimento_IBS_UF", "decimal(15,2) DEFAULT 0.00 COMMENT 'Valor do diferimento do IBS UF'");

// Outros
Criar_Campo("T084A_Valor_Base_CBS_IBS", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Base do CBS e do IBS UF/Municipal (Base única para todos os impostos)'");
Criar_Campo("T084A_Valor_Total_Reforma", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor final do item (será somado no valor total da NF T007A_Valor_Total_Reforma)'");
Criar_Campo("T084A_Valor_IBS", "decimal(15, 2) DEFAULT 0.00 COMMENT 'Valor final do IBS: Soma IBS UF + IBS Municipal'");

/* Fórmulas
---------- */
/*===========================================================================================================================*/

// alterações em geral


mysql_query("DROP TRIGGER IF EXISTS T003_trigger_after_insert;");
mysql_query("DROP TRIGGER IF EXISTS T005_trigger_after_insert;");
mysql_query("DROP TRIGGER IF EXISTS T007_trigger_after_insert;");
mysql_query("DROP TRIGGER IF EXISTS T019_trigger_atualiza_saldo;");
mysql_query("DROP TRIGGER IF EXISTS T257_trigger_antes_exclusao;");
mysql_query("DROP TRIGGER IF EXISTS D085_atualiza_D024;");








// Novo módulo de cotação de compra
// D009_Quantidade_OC
// Separacao por Locação
// Alteração Intens do Pedido







Alterar_Tipo_Campo("T160_Valor","varchar(50)");
Alterar_Tipo_Campo("D112_Tempo_Estimado","time");
Alterar_Tipo_Campo("T145_Tempo_Estimado","time");
Alterar_Tipo_Campo("T147_Tempo_Estimado","time");
Alterar_Tipo_Campo("T075_Cotacao_ME","DECIMAL(12,4)");
Alterar_Campo("T147_Hora_Inicio_Atual","T147_Hora_Reinicio","datetime");
Alterar_Tipo_Campo("T147_Prioridade","int(5) unsigned DEFAULT 99999");
Alterar_Tipo_Campo("D109_Carga_Horaria","time");
Alterar_Tipo_Campo("T055_Valor_Adicao","varchar(10)");
Alterar_Tipo_Campo("T055_Valor_Custo_Unitario","decimal(20,7)");
Alterar_Campo("D114_D111_Id","D114_D009_Id","int(10) unsigned");
Alterar_Campo("T149_D111_Id","T149_D009_Id","int(10) unsigned");
Alterar_Campo("T152_D111_Id","T152_D009_Id","int(10) unsigned");
Alterar_Tipo_Campo("D114_Quantidade","DECIMAL(10,4)");
Alterar_Tipo_Campo("T149_Quantidade","DECIMAL(10,4)");
Alterar_Tipo_Campo("T152_Quantidade","DECIMAL(10,4)");
Alterar_Campo("T180_Prazo_Dias","T180_Prazos_Dias","int(5)");
//Alterar_Tipo_Campo("T004_Percentual_Desconto","DECIMAL(10,5)");
Alterar_Tipo_Campo("T179_Quantidade_Entregue","int(4) default 0");
// alterado para corrigir o erro de ao inserir produto no pedido quebrar o T005_Gravar_Totalizacao_3
Alterar_Tipo_Campo("T006_Flag_Status","varchar(1) default ''");
Alterar_Tipo_Campo("T189_Flag_Tipo","varchar(10) default ''");
Alterar_Tipo_Campo("T066_Quantidade_Estoque","decimal(20,5) default 0 NOT NULL");
Alterar_Tipo_Campo("T066_Quantidade_Estoque_Real","decimal(20,5) default 0 NOT NULL");
Alterar_Tipo_Campo("T066_Quantidade_Estoque_Liquido","decimal(20,5) default 0 NOT NULL");
Alterar_Tipo_Campo("T066_Agrupamento","decimal(20,5) default 1 NOT NULL");
//Alterar_Tipo_Campo("T066_Retirou_Qtd","decimal(20,5) default '' NOT NULL");
Alterar_Tipo_Campo("D004_Flag_Tipo","int(10) unsigned");
Alterar_Tipo_Campo("C008_Tipo","varchar(64) default ''");
Alterar_Tipo_Campo("T004_Quantidade","decimal(12,5) default 0");
Alterar_Tipo_Campo("T006_Quantidade","decimal(12,5) default 0");
Alterar_Tipo_Campo("T019_Conciliado","int(1) default 0");
Alterar_Tipo_Campo("T001_Descricao_Operacao","VARCHAR(40)");
Alterar_Tipo_Campo("T270_Numero_Processo_Anuente","int(10) ");
Alterar_Tipo_Campo("D162_Status","varchar(40)");

Criar_Campo("T249_Grupo", "varchar(3) default ''");
Criar_Campo("T250_Grupo", "varchar(3) default ''");
Criar_Campo("T251_Grupo", "varchar(3) default ''");

Criar_Campo("T003_Validade_Proposta","varchar(40)");
Criar_Campo("T005_Ordem_Compra","varchar(40)");
Criar_Campo("T005_D022_Id_Default","int(10) unsigned");
Criar_Campo("T003_Observacao_Pagamento","text");
Criar_Campo("T003_Pgto_Parcelas","varchar(10)");
Criar_Campo("T003_Pgto_Primeiro_Vcto_Tipo","varchar(10)");
Criar_Campo("T003_Pgto_Primeiro_Vcto_Valor","varchar(10)");
Criar_Campo("T003_Pgto_Dias_Vcto_Tipo","varchar(10)");
Criar_Campo("T003_Pgto_Dias_Vcto_Valor","varchar(10)");
Criar_Campo("T003_Pgto_Portador","varchar(10)");
Criar_Campo("T003_Pgto_Data","datetime");
Criar_Campo("T006_Hora_Entrega","time");
Criar_Campo("T003_Pgto_C007_Id","int(10) unsigned");
Criar_Campo("T005_D024_Id_Nfe","int(10) unsigned");
Criar_Campo("T005_D024_Id_Contas","int(10) unsigned");
Criar_Campo("T005_Pgto_Parcelas","varchar(10)");
Criar_Campo("T005_Pgto_Primeiro_Vcto_Tipo","varchar(10)");
Criar_Campo("T005_Pgto_Primeiro_Vcto_Valor","varchar(10)");
Criar_Campo("T005_Pgto_Dias_Vcto_Tipo","varchar(10)");
Criar_Campo("T005_Pgto_Dias_Vcto_Valor","varchar(10)");
Criar_Campo("T005_Pgto_Portador","varchar(10)");
Criar_Campo("T005_Pgto_Data","datetime");
Criar_Campo("T005_Pgto_C007_Id","int(10) unsigned");
Criar_Campo("T003_D116_Id","int(10) unsigned");
Criar_Campo("T005_D116_Id","int(10) unsigned");
Criar_Campo("T003_Flag_Liberou_Alcada_Cortesia","char(1) DEFAULT 'N'");
Criar_Campo("T003_Liberou_Alcada_Cortesia_Valor_Total","decimal(10,2)");
Criar_Campo("T003_Liberou_Alcada_Valor_Total","decimal(10,2)");
Criar_Campo("T004_Liberou_Alcada_Saldo_Futuro","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T006_Liberou_Alcada_Saldo_Futuro","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T003_Liberou_Alcada_Prazo_Medio","decimal(10,4)");
Criar_Campo("T003_Liberou_Alcada_Desconto","decimal(10,2)");
Criar_Campo("T003_Valor_Total_Servicos","decimal(10,2)");
Criar_Campo("T005_Valor_Total_Servicos","decimal(10,2)");
Criar_Campo("T005_Liberou_Alcada_C007_Id","int(11)");
Criar_Campo("T005_Liberou_Alcada_Data","datetime");
Criar_Campo("T005_Liberou_Alcada_IPV","decimal(10,4)");
Criar_Campo("T005_Liberou_Alcada_Valor_Total","decimal(10,2)");
Criar_Campo("T005_Flag_Alcada","varchar(1)");
Criar_Campo("T005_Liberou_Alcada_Valor_Total","decimal(10,2)");
Criar_Campo("T007_Valor_Total_Servicos","decimal(14,2) DEFAULT '0.00' NOT NULL");
Criar_Campo("T005_Liberou_Alcada_Prazo_Medio","decimal(10,4)");
Criar_Campo("T005_Liberou_Alcada_Desconto","decimal(10,2)");
Criar_Campo("T005_Liberou_Alcada_Observacao_Nao_Liberado","text");
Criar_Campo("T005_Flag_Status_Liberacao","varchar(1)");
Criar_Campo("C020_Numero_Meses_Relatorio_Estoque","INT DEFAULT '12' NOT NULL");
Criar_Campo("T185_Tipo_Divergencia","int(1) DEFAULT 0 NOT NULL");
Criar_Campo("T066_Minimo", "decimal(20,5) DEFAULT 0 NOT NULL");
Criar_Campo("T066_Maximo", "decimal(20,5) DEFAULT 0 NOT NULL");
Criar_Campo("T066_Agrupamento", "decimal(20,5) DEFAULT 0 NOT NULL");
Criar_Campo("D138_Flag_Tipo","varchar(1)");
Criar_Campo("D138_Flag_Mostrar","varchar(1) DEFAULT 'S'");
Criar_Campo("T173_Forma_Pagamento","varchar(255)");
Criar_Campo("D138_Flag_Editor","varchar(1) DEFAULT 'N'");
Criar_Campo("D007_Nome_Conta","varchar(50) DEFAULT ''");
Criar_Campo("D007_Instrucao_Protesto_1","varchar(30) DEFAULT ''");
Criar_Campo("D007_Instrucao_Protesto_2","varchar(30) DEFAULT ''");
Criar_Campo("D024_Flag_Protestar","varchar(1) DEFAULT 'N'");
Criar_Campo("T004_Valor_Preco_Original_Bruto","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_Valor_Desconto_Unitario_Bruto","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_Percentual_Desconto_Bruto","decimal(14,5) DEFAULT '0'");
Criar_Campo("D024_Percentual_Desconto_Tabela","decimal(10,4) DEFAULT '0'");

Criar_Campo("C004_Versao_NFe","decimal(3,2) DEFAULT '3.10'");

Alterar_Tipo_Campo("T008_Numero_Documento_Importacao","varchar(20) DEFAULT ''");

Criar_Campo('D024_IPV_1', "decimal(8,4) DEFAULT '0.0000'");

Criar_Campo("T006_Valor_Comissao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T006_Percentual_Comissao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T005_Valor_Total_Comissao","decimal(14,5) DEFAULT '0'");

Criar_Campo("T014_Flag_Divergencia","varchar(1) DEFAULT 'S'");

Criar_Campo("T008_Valor_Comissao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T008_Percentual_Comissao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T007_Valor_Total_Comissao","decimal(14,5) DEFAULT '0'");

Criar_Campo("T004_Valor_Comissao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_Percentual_Comissao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T003_Valor_Total_Comissao","decimal(14,5) DEFAULT '0'");

Criar_Campo("T006_Valor_Preco_Original_Bruto","decimal(14,5) DEFAULT '0'");
Criar_Campo("T006_Valor_Desconto_Unitario_Bruto","decimal(14,5) DEFAULT '0'");
Criar_Campo("T006_Percentual_Desconto_Bruto","decimal(14,5) DEFAULT '0'");

Alterar_Tipo_Campo("T059_Descricao_Produto","varchar(255)");

Alterar_Tipo_Campo("T006_Quantidade_Entregar_Temp","int(4) default 0");

Alterar_Tipo_Campo("D049_ICF1","DECIMAL(10,4)");
Alterar_Tipo_Campo("D049_ICF2","DECIMAL(10,4)");

Alterar_Tipo_Campo("T075_Data_Atualizacao_Custo","datetime");

Alterar_Tipo_Campo("D167_Horas_Validade","time");

Criar_Campo("T001_T138_Id","int(10) unsigned");

Criar_Campo("T066_Quantidade_Estoque_Real", "decimal(20,5)");
Criar_Campo("T066_Quantidade_Estoque_Liquido", "decimal(20,5)");

Criar_Campo("T177_Clientes_Total", "int(10) DEFAULT 0");
Criar_Campo("T177_Custo_Media", "decimal(20,5) DEFAULT 0");
Criar_Campo("T177_Custo_Fim_Mes", "decimal(20,5) DEFAULT 0");
Criar_Campo("T177_Estoque_Media", "decimal(20,5) DEFAULT 0");
Criar_Campo("T177_Estoque_Fim_Mes", "decimal(20,5) DEFAULT 0");

Criar_Campo("T066_Flag_Inventario_Por_Locacao", "char(1) DEFAULT 'N' NOT NULL");

Criar_Campo("D009_Codigo_Barra", "varchar(255)");
Criar_Campo("T147_Observacao", "varchar(500)");
Criar_Campo("T192_C007_Id_Destinatario", "int(10) unsigned");

Criar_Campo("T185_Flag_Status", "int(1) DEFAULT 0 NOT NULL");
Criar_Campo("T048_Flag_Tipo_Montagem","char(1)");
Criar_Campo("D118_Flag_Tipo","char(1)");

Criar_Campo("T030_D004_Id","int(10)");
Criar_Campo("D049_Flag_Curva_ABC","enum('A','B','C','D') DEFAULT 'D' NOT NULL");
Criar_Campo("D049_Flag_Curva_ABC_Qtd","enum('A','B','C','D') DEFAULT 'D' NOT NULL");
Criar_Campo("D024_Flag_Curva_ABC_Qtd","enum('A','B','C','D') DEFAULT 'D' NOT NULL");

Criar_Campo("T187_Flag_Prod_Anterior", "int(1) DEFAULT 1 NOT NULL");
Criar_Campo("D075_Numero_Funcionarios", "int(10) DEFAULT NULL");
Criar_Campo("D074_D009_Id","int(10) unsigned");
Alter_Query("ALTER TABLE T174 CHANGE T174_IPV T174_IPV DECIMAL(14,3) NULL;");

//Alter_Query("ALTER TABLE T139 ENGINE=MyISAM;");

Criar_Campo("T015_Id_Padrao","int(10) unsigned");
Criar_Campo("T002_Id_Padrao","int(10) unsigned");
Criar_Campo("T006_D009_Id_Kit","int(10) unsigned");

Criar_Campo("T174_T189_Id","int(10) unsigned");
Criar_Campo("T189_Flag_Status","char(1) DEFAULT 'N' NOT NULL");
Criar_Campo("T005_Flag_Coleta","char(1) DEFAULT 'N' NOT NULL");
Criar_Campo("C004_Observacao_Rodape_Pedido","text DEFAULT NULL");
Criar_Campo("T006_Valor_Preco_Bruto","decimal(10,2)");
Criar_Campo("T004_Valor_Preco_Bruto","decimal(10,2)");
Criar_Campo("D024_Apelido","varchar(50)");
Criar_Campo("D001_Flag_Multimarcas","char(1) DEFAULT 'N' NOT NULL");
Criar_Campo("T138_Quantidade_Separado","decimal(10,2)");
Alterar_Tipo_Campo("T185_Qtd_Separado","DECIMAL(12,5)");

Criar_Campo("D001_Flag_Pre_Cadastro","varchar(1) default 'N'");
Criar_Campo("D001_Descricao_Ingles","varchar(60) default ''");

Criar_Campo("T003_Ordem_Compra","varchar(40) default ''");

Criar_Campo("T004_Valor_Frete_Unitario","DECIMAL(19,2)");
Criar_Campo("T004_Valor_Frete_Total","DECIMAL(19,2)");
Criar_Campo("T004_Valor_Total_Despesas_Acessorias","DECIMAL(19,2)");
Criar_Campo("T004_Valor_Despesas_Acessorias_Unitario","DECIMAL(19,2)");
Criar_Campo("T185_Qtd_Devolvido","decimal(10,2)");
Criar_Campo("D005_Imposto_Importacao","decimal(5,2)");
Criar_Campo("D005_Importacao_IPI","decimal(5,2)");
Criar_Campo("D005_Importacao_PIS","decimal(5,2)");
Criar_Campo("D005_Importacao_COFINS","decimal(5,2)");
Criar_Campo("T207_T005_Id","int(10) unsigned");
Criar_Campo("D149_UF_ST_Compras","text");

// Transferindo campos de preços da D049 para D009
Criar_Campo("D009_ICF_1","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_ICF_2","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_ICF_3","decimal(10,4) DEFAULT 0.00");

Criar_Campo("D009_IPV_1","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_IPV_2","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_IPV_3","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_IPV_4","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009A_IPV_5","decimal(10,4) DEFAULT 0.00");

Criar_Campo("D009_Preco_1","decimal(14,2) DEFAULT 0.00");
Criar_Campo("D009_Preco_2","decimal(14,2) DEFAULT 0.00");
Criar_Campo("D009_Preco_3","decimal(14,2) DEFAULT 0.00");
Criar_Campo("D009_Preco_4","decimal(14,2) DEFAULT 0.00");
Criar_Campo("D009A_Preco_5","decimal(20,10) default '0.0000000000'");

Criar_Campo("D009_Valor_Preco_Tabela","decimal(14,5) DEFAULT 0.00");
Criar_Campo("D009_Aliquota_ICMS_Tabela","decimal(6,2) DEFAULT 0.00");
Criar_Campo("D009_Percentual_Desconto_Tabela","decimal(14,5) DEFAULT 0.00");
Criar_Campo("D009_Percentual_Acrescimo_Tabela","decimal(14,5) DEFAULT 0.00");
Criar_Campo("D009_Flag_Promocao","CHAR(1) DEFAULT 'N'");
Criar_Campo("D009_Data_Alteracao_Preco","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D009_Data_Alteracao_Custo","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D009_Preco_Pauta_ST","decimal(20,10) DEFAULT 0 not null");
Criar_Campo("T004_Preco_Pauta_ST","decimal(20,10) DEFAULT 0 not null");
Criar_Campo("T006_Preco_Pauta_ST","decimal(20,10) DEFAULT 0 not null");
Criar_Campo("T008_Preco_Pauta_ST","decimal(20,10) DEFAULT 0 not null");
// Alterando os campos da T098 que antes usava da D049 agora usará da D009
Criar_Campo("T098_D009_ICF_1","DECIMAL(10,4) DEFAULT 0.00");
Criar_Campo("T098_D009_ICF_2","DECIMAL(10,4) DEFAULT 0.00");
Criar_Campo("T098_D009_ICF_3","DECIMAL(10,4) DEFAULT 0.00");

Criar_Campo("T098_D009_IPV_1","DECIMAL(10,4) DEFAULT 0.00");
Criar_Campo("T098_D009_IPV_2","DECIMAL(10,4) DEFAULT 0.00");
Criar_Campo("T098_D009_IPV_3","DECIMAL(10,4) DEFAULT 0.00");

Criar_Campo("T098_D009_Preco_1","DECIMAL(14,2) DEFAULT 0.00");
Criar_Campo("T098_D009_Preco_2","DECIMAL(14,2) DEFAULT 0.00");
Criar_Campo("T098_D009_Preco_3","DECIMAL(14,2) DEFAULT 0.00");

Criar_Campo("T098_D009_Valor_Preco_Tabela","DECIMAL(14,5) DEFAULT 0.00");
Criar_Campo("T098_D009_Aliquota_ICMS_Tabela","DECIMAL(10,5) DEFAULT 0.00");
Criar_Campo("T098_D009_Percentual_Desconto_Tabela","DECIMAL(10,5) DEFAULT 0.00");
Criar_Campo("T098_D009_Id","int(10)");
Criar_Campo("T098_C004_Id","int(10)");
Criar_Campo("C007_Codigo_Barras","varchar(255)");
Criar_Campo("T007_Data_Envio_Email","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T035_Flag_Tipo", "int(1) DEFAULT 1 NOT NULL");
Alterar_Tipo_Campo("T038_Codigo_Motivo","varchar(10)");
Criar_Campo("T119_C007_Id_Abertura","int(10)");
Criar_Campo("T119_C007_Id_Fechamento","int(10)");
Criar_Campo("C004_Flag_Natureza_Pessoa_Juridica","char(2)");
Criar_Campo("C004_Flag_Tipo_Atividade","char(1)");
Criar_Campo("C004_Numero_Suframa","int(9) DEFAULT null");
Criar_Campo("C004_Flag_Incidencia_Tributaria","char(1)");
Criar_Campo("C004_Flag_Metodo_Apropriacao_Credito","char(1)");
Criar_Campo("C004_Flag_Tipo_Contribuicao","char(1)");
Criar_Campo("C004_Flag_Escrituracao_Contribuicao","char(1)");
Criar_Campo("D001_Flag_Tipo_Item","char(2) DEFAULT '00'");
Criar_Campo("D032_Data_Inclusao","date");
Criar_Campo("D014_Data_Inclusao","date");
Criar_Campo("T008_Flag_Base_Calculo","char(2) DEFAULT '00'");
Criar_Campo("T208_Producao","varchar(30)");
Criar_Campo("D024_Empresa_Emissora","int(10)");
Criar_Campo("D024_Prazo_Parcela","int(10)");
Criar_Campo("D024_Dias_Vencimento","int(10)");
Criar_Campo("D024_Quantidade_Parcelas","int(10)");
Criar_Campo("D024_D027_Id","int(10)");
Criar_Campo("T173_D036_Id","int(10)");
Criar_Campo("T005_C007_Id_Conferiu","int(10)");
Criar_Campo("T005_T007_Id","int(10)");
Criar_Campo("T005_Flag_Gerar_NF","char(1)");
Criar_Campo("T007_Data_Envio_Email_Boleto","datetime DEFAULT '0000-00-00 00:00:00'");
Alterar_Tipo_Campo("D001_Flag_Tipo","varchar(10) default 'COMPRADO'");
Alterar_Tipo_Campo("D049_Flag_Tipo","varchar(2) default 'CP'");
Criar_Campo("T005_Flag_Marcado", "varchar(1) default 'N'");

Criar_Indice('T007_T075_Id');
Criar_Indice('T098_D009_Id');
Criar_Indice('D156_D001_Id_1');
Criar_Indice('D156_D001_Id_2');
Criar_Indice('D069_D049_Id');
Criar_Indice('D168_D036_Id');
Criar_Indice('T135_D014_Id');
Criar_Indice('T135_D032_Id');
Criar_Indice('T005_T275_Id');
Criar_Indice('T234_Chamado_Origem_Id');

Criar_Indice('T182_D024_Id_Cliente');
Criar_Indice('T182_D024_Id_Fornecedor');
Criar_Indice('T182_D013_Id_Cliente');
Criar_Indice('T182_D013_Id_Fornecedor');
Criar_Indice('T182_D162_Id');
Criar_Indice('T182_C007_Id');
Criar_Indice('T182_D164_Id');
Criar_Indice('T182_D018_Id');
Criar_Indice('T182_Acao');
Criar_Indice('T182_Data');
Criar_Indice('T182_Status');
Criar_Indice('T182_NF_Venda');
Criar_Indice('T182_Tipo');
Criar_Indice('T182_D009_Id');

Criar_Indice('D087_D024_Id');
Criar_Indice('D024_D046_Id');

Criar_Indice('T006_T075_Id');

Criar_Indice('D166_T272_Id');
Criar_Indice('D166_Lista_Analise_Id');

Criar_Indice('T006_T206_Id');

Criar_Indice('T015_D073_Id');
Criar_Indice('T002_D073_Id');
Criar_Indice('T216_T002_Id');
Criar_Indice('D005_D037_Id_Tributavel');

Criar_Indice('D013_Nome_Contato');
Criar_Indice('D013_Id');

Criar_Indice('T162_C007_Id');
Criar_Indice('T162_Registro_Id');

Criar_Indice('T089_T003_Id');
Criar_Indice('T089_D027_Id');
Criar_Indice('T055_T270_Id');
Criar_Indice('T006_T066_Id');
Criar_Indice('T008_T066_Id');
Criar_Indice('T014_T066_Id');
Criar_Indice('T006_D009_Id_Loja');
Criar_Indice('T219_D009_Id_Loja');
Criar_Indice('T225_D009_Id_Comprar');
Criar_Indice('T225_D009_Id_Kit');
Criar_Indice('T225_D009_Id_Loja');
Criar_Indice('D069_D009_Id_Comprar');
Criar_Indice('D069_D009_Id_Kit');
Criar_Indice('D069_D009_Id_Loja');
Criar_Indice('R001_D049_Id');
Criar_Indice('R003_D049_Id');
Criar_Indice('R004_D049_Id');
Criar_Indice('T006_D049_Id');
Criar_Indice('T144_D001_Id');
Criar_Indice('T019_D027_Id');
Criar_Indice('T019_D031_Id');
Criar_Indice('T216_T019_Id');
Criar_Indice('T204_T006_Id');
Criar_Indice('T005_T134_Id');
Criar_Indice('D009_C007_Id_Comprar');
Criar_Indice('T225_D049_Id');
Criar_Indice('T206_D169_Id');
Criar_Indice('T004_T206_Id');
Criar_Indice('T006_T206_Id');

Criar_Campo("T007_TX_Resposta_Codigo","INT DEFAULT 0 NOT NULL");
Criar_Campo("T007_TX_Resposta_Extra","TEXT");
Criar_Campo("T007_TX_Retorno_Sefaz","TEXT");
Criar_Campo("T007_TX_Flag_Finalizada","INT(1) DEFAULT 0 NOT NULL");
Criar_Campo("T007_TX_Mensagem","TEXT");

Criar_Campo("T139_Flag_Tem_Xml","CHAR(1) DEFAULT 'N' NOT NULL");
Criar_Campo("T139_Flag_Xml_Importado","CHAR(1) DEFAULT 'N' NOT NULL");

Criar_Campo("T007_Flag_Enviar","CHAR(1) DEFAULT 'N' NOT NULL");
Criar_Campo("D137_C004_Id","INT(10) NOT NULL");
Criar_Campo("D137_Empresas","varchar(20) NOT NULL");
Criar_Campo("D005_UF_ST","TEXT NOT NULL");
Criar_Campo("D137_Flag_Revenda_Consumidor","varchar(20) NOT NULL");
Criar_Campo("D137_Flag_UF_Convenio_NCM_ST","varchar(1) DEFAULT ''");
Criar_Campo("D137_Flag_Preferencial_Sobre_D024_D006_Id","varchar(1) DEFAULT 'N'");
Criar_Campo("D137_D024_Codigo_Regime_Tributario","int(1)");
Criar_Campo("D024_Numero_Entrega","int(10)");

Criar_Campo("D069_Quantidade_Estoque","DECIMAL(10,3) DEFAULT '0'");
Criar_Campo("D069_Data_Atualizacao_Preco","DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D069_Data_Atualizacao_Estoque","DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D069_Quantidade_Estoque_Anterior","DECIMAL(10,3) DEFAULT '0'");
Criar_Campo("D069_Valor_Preco_Original_Anterior","DECIMAL(15,5) DEFAULT '0'");

Criar_Campo("D137_Peso_Minimo","DECIMAL(10,3) DEFAULT '0'");
Criar_Campo("D137_Peso_Maximo","DECIMAL(10,3) DEFAULT '0'");
Criar_Campo("T012_ST", "DECIMAL(14,5) DEFAULT '0'");
Criar_Campo("T117_Flag_Enviar_Email","varchar(1) DEFAULT ''");
Criar_Campo("D007_Flag_Tipo_Multa","varchar(1) DEFAULT 'V'");
Criar_Campo("T005_C007_Id_Embalagem","INT(10) NOT NULL");
Criar_Campo("T005_Codigo_Rastreio","varchar(30) DEFAULT ''");
Criar_Campo("T007_Codigo_Rastreio","varchar(30) DEFAULT ''");
Criar_Campo("T005_Custo_Frete","DECIMAL(14,2) DEFAULT 0.00");
Criar_Campo("T005_C007_Gerar_NF","INT(10) DEFAULT 0");
Criar_Campo("D007_Instrucoes_Protesto","TEXT");
Criar_Campo("C007_Email_POP_Porta","INT(10)");
Criar_Campo("T117_T002_Id","INT(10) DEFAULT 0");
Criar_Campo("D112_T212_Id","INT(10) DEFAULT 0");
Criar_Campo("D137_NCM","TEXT");
Criar_Campo("T117_Log_Boleto","TEXT");
Alterar_Tipo_Campo("T006_Valor_ICMS","DECIMAL(15,2) DEFAULT 0");
Alterar_Tipo_Campo("T008_Valor_ICMS","DECIMAL(15,2) DEFAULT 0");
Alterar_Tipo_Campo("D007_Numero_Agencia","varchar(10)");
Alterar_Tipo_Campo("D007_Numero_Convenio","varchar(20)");
Alterar_Tipo_Campo("T173_Data_Entrega","varchar(64)");
Alterar_Tipo_Campo("T174_Quantidade","DECIMAL(10,3)");

// Campos NF Entrada
Criar_Campo("T014_Modalidade_ICMS_Substituicao","int(1) DEFAULT '0'");
Criar_Campo("T014_Modalidade_ICMS","int(1) DEFAULT '0'");
Criar_Campo("T014_ICMS_Inter_Estadual","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T014_Situacao_Tributaria_IPI","varchar(2) DEFAULT '0'");
Criar_Campo("T014_Situacao_Tributaria_PIS","varchar(2)  DEFAULT '0'");
Criar_Campo("T014_Valor_Base_PIS","decimal(20,2)  DEFAULT '0.00'");
Criar_Campo("T014_Aliquota_PIS","decimal(5,2)  DEFAULT '0.00'");
Criar_Campo("T014_Valor_PIS","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T014_Flag_Isento_PIS","enum('S','N')");
Criar_Campo("T014_Valor_Base_ICMS_Substituicao","decimal(20,2) DEFAULT '0.00'");
Criar_Campo("T014_Id_Duplicado","int(10) unsigned");

Criar_Campo("D069_Flag_Excluir","int(1) DEFAULT '2'");

Criar_Campo("T014_Situacao_Tributaria_COFINS","varchar(2)  DEFAULT '0'");
Criar_Campo("T014_Flag_Situacao_Tributaria","varchar(2)  DEFAULT '0'");
Criar_Campo("T014_Flag_Isento_COFINS","enum('S','N')");
Criar_Campo("T014_Valor_Base_COFINS","decimal(20,2) DEFAULT '0.00'");
Criar_Campo("T014_Aliquota_COFINS","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_COFINS","decimal(10,2) DEFAULT '0.00'");

Criar_Campo("T013_Valor_Total_COFINS","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T013_Valor_Total_PIS","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T013A_Aliquota_ICMS_Credito_Recebida","decimal(7,4) DEFAULT '0.0000'");
Criar_Campo("T013A_Valor_ICMS_Credito_Recebida","decimal(15,2) DEFAULT '0.00'");

Criar_Campo("T013_Peso_Bruto","decimal(10,3) DEFAULT '0.00'");
Criar_Campo("T013_Peso_Liquido","decimal(10,3) DEFAULT '0.00'");
Criar_Campo("T013_Total_Peso","decimal(15,5) DEFAULT '0.00'");
Criar_Campo("T013_Valor_ICMS_Diferenca_Aliquotas","decimal(15,5) DEFAULT '0.00'");
Criar_Campo("T014_Valor_ICMS_Diferenca_Aliquotas","decimal(15,5) DEFAULT '0.00'");


Criar_Campo("T014_Aliquota_ICMS_Interno","decimal(5,2)  DEFAULT '0.00'");
Criar_Campo("T014_MVA_Ajustada","decimal(5,2)  DEFAULT '0.00'");
Criar_Campo("T014_Percentual_Reducao_ICMS_ST","decimal(5,2)  DEFAULT '0.00'");

// ****

Criar_Campo("T012_Quantidade_Recebida","decimal(10,3) DEFAULT '0.00'");
Criar_Campo("T129_Quantidade_Recebida","decimal(10,3) DEFAULT '0.00'");
Criar_Campo("T001_Data_Hora_Insercao","DATETIME");

Criar_Indice('T007_Data_Hora_Protocolo_Nfe');

Criar_Campo("C004_Menu_Extra_Html","varchar(255)");
Criar_Campo("C007_Id_Vendedor_Substituto","INT(10) DEFAULT 0");
Criar_Campo("D007_Variacao_Carteira","varchar(10)");
Criar_Campo("T005_Processo_Id","int(10)");
Criar_Campo("T005_Processo_Log","varchar(255)");
Criar_Campo("T013_Arquivo_XML","TEXT");
Criar_Campo("D005_Regras_MVA","TEXT");
Criar_Campo("D006_Reducao_MVA","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T147_T212_Id","int(10)");
Criar_Campo("D009_Quantidade_Estoque_Medio_Prazo","DECIMAL(12,2)");
Criar_Campo("T174_Flag_Origem_Produto","varchar(1)");
Criar_Campo("T174_Flag_ST","varchar(1)");
Criar_Campo("C007_Notificar_Versao","varchar(10)");
Criar_Campo("T173_Data_Entrega","varchar(64)");
Criar_Campo("D001_Peso_Unitario_Bruto","decimal(10,3)");
Criar_Campo("T004_Peso_Bruto","decimal(10,3)");
Criar_Campo("T004_Peso_Bruto_Total","decimal(10,3)");
Criar_Campo("T003_Peso_Bruto_Total","decimal(10,3)");
Criar_Campo("T006_Peso_Bruto","decimal(10,3)");
Criar_Campo("T006_Peso_Bruto_Total","decimal(10,3)");
Criar_Campo("T005_Peso_Bruto_Total","decimal(10,3)");
Criar_Campo("T005_Flag_Totalizar_Peso","varchar(1) DEFAULT 'N' NOT NULL");
Criar_Campo("T008_Peso_Bruto","decimal(10,3)");
Criar_Campo("T008_Peso_Bruto_Total","decimal(10,3)");
Criar_Campo("T007_Total_Peso_Bruto","decimal(10,3)");
Criar_Campo("T007_Flag_Totalizar_Peso","varchar(1) DEFAULT 'N' NOT NULL");
Criar_Campo("T173_Tipo_Transporte","varchar(255) DEFAULT NULL");
Criar_Campo("D024_E_Mail_Nota","varchar(255) DEFAULT NULL");
Criar_Campo("D024_E_Mail_Boleto","varchar(255) DEFAULT NULL");
Criar_Campo("T173_D013_Id","int(10)");
Criar_Campo("D009_Quantidade_Meses_Compra","int(10)");
Criar_Campo("D001_Quantidade_Meses_Compra","int(10)");
Criar_Campo("D001_Quantidade_Meses_Compra_Minimo","int(10)");
Criar_Campo("D009_Quantidade_Meses_Compra_Minimo","int(10)");
Criar_Campo("D009_Flag_Usar_Custo","varchar(1) default ''");


Criar_Campo("D069_Valor_ICM","decimal(10,2)");
Criar_Campo("D069_Percentual_Reducao_ICM","decimal(10,2)");
Criar_Campo("D069_Valor_IPI","decimal(10,2)");
Criar_Campo("D069_Valor_ST","decimal(10,2)");
Criar_Campo("D069_ALiquota_ICM_Inter_Estadual","decimal(10,2)");
Criar_Campo("D069_Aliquota_ICM_Interno","decimal(10,2)");
Criar_Campo("D069_Valor_Custo_Unitario","decimal(15,5)");
Criar_Campo("D069_Valor_Base_ICM","decimal(10,2)");
Criar_Campo("D069_Valor_Base_IPI","decimal(10,2)");
Criar_Campo("D069_Valor_Base_ST","decimal(10,2)");
Criar_Campo("D069_Flag_ST","varchar(1) default 'N'");
Criar_Campo("D069_Valor_Frete","decimal(10,5) default '0'");
Criar_Campo("D069_Valor_Financeiro","decimal(10,5) default '0'");
Criar_Campo("D069_Valor_Outros","decimal(10,5) default '0'");
Criar_Campo("D069_Percentual_Frete","decimal(10,5) default '0'");
Criar_Campo("D069_Percentual_Financeiro","decimal(10,5) default '0'");
Criar_Campo("D069_Percentual_Outros","decimal(10,5) default '0'");
//Alterar_Tipo_Campo("D069_Preco_Unitario","DECIMAL(15,5) default 0");
//Alterar_Tipo_Campo("D069_Preco_Unitario_Liquido","DECIMAL(15,5) default 0");
Criar_Campo("D069_MVA_Ajustada","decimal(10,5) default '0'");
Criar_Campo("D070_Flag_Ajustar_MVA","varchar(1) default 'N'");
Criar_Campo("D069_Flag_Tabela_Principal","varchar(1) default 'N'");
Criar_Campo("D149_Regras_MVA_Compra","text default ''");
Criar_Campo("D070_Flag_Calcula_Percentual_ST","varchar(1) default 'S'");

Criar_Campo("T256_Quantidade_Entrada_Estoque","decimal(10,3) default 0");
Criar_Campo("T255_Quantidade_Entrada_Estoque","decimal(10,3) default 0");

Criar_Campo("T256A_T256_Id_Op_Pai","int(10)");
Criar_Campo("T256A_Flag_Certificado_Qualidade", "enum('S','N') default'N'");

////////////////////////////////////////////////////
////--- Campos referente cubagem do produto ---/////
////////////////////////////////////////////////////
Criar_Campo("D001_Cubagem_Unitaria","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T004_Cubagem_Unitaria","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T004_Total_Cubagem","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T003_Total_Cubagem","DECIMAL(10,6) DEFAULT '0.000000'");

Criar_Campo("T006_Cubagem_Unitaria","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T006_Total_Cubagem","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T005_Total_Cubagem","DECIMAL(10,6) DEFAULT '0.000000'");

Criar_Campo("T008_Cubagem_Unitaria","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T008_Total_Cubagem","DECIMAL(10,6) DEFAULT '0.000000'");
Criar_Campo("T007_Total_Cubagem","DECIMAL(10,6) DEFAULT '0.000000'");

Criar_Campo("T003_Percentual_Comissao_Manual","DECIMAL(5,2) DEFAULT '0.00'");
Criar_Campo("T005_Percentual_Comissao_Manual","DECIMAL(5,2) DEFAULT '0.00'");
Criar_Campo("T007_Percentual_Comissao_Manual","DECIMAL(5,2) DEFAULT '0.00'");

//// --- Fim campos cubagem


////---- NOVOS CAMPOS LEI TRANSPARÊNCIA

//-- Orçamento
Criar_Campo("T003_Valor_Total_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
Criar_Campo("T004_Valor_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
Criar_Campo("T004_Percentual_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");

//-- Pedido
Criar_Campo("T005_Valor_Total_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");
Criar_Campo("T006_Valor_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
Criar_Campo("T006_Percentual_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");

//-- Nota
Criar_Campo("T007_Valor_Total_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
Criar_Campo("T007_Flag_Trib_Transparencia","enum('S','N') DEFAULT 'N'");
Criar_Campo("T008_Valor_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
Criar_Campo("T008_Percentual_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");

Alterar_Tipo_Campo("T003_Valor_Total_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");
Alterar_Tipo_Campo("T004_Valor_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");
//Alterar_Tipo_Campo("T005_Valor_Total_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");
Alterar_Tipo_Campo("T006_Valor_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");
Alterar_Tipo_Campo("T008_Valor_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");
Alterar_Tipo_Campo("T007_Valor_Total_Trib_Transparencia","DECIMAL(15,6) DEFAULT '0.000000'");

Alterar_Tipo_Campo("T236_Data_Inicio","DATETIME");
Alterar_Tipo_Campo("T236_Data_Finalizado","DATETIME");
Alterar_Tipo_Campo("T236_Tempo_Estimado","TIME");

Alterar_Tipo_Campo("D022_Nome_Empresa","VARCHAR(60)");
Alterar_Tipo_Campo("D022_Fax","VARCHAR(100)");
Alterar_Tipo_Campo("T225_Valor_Preco_Original","DECIMAL(15,5)");

Alterar_Tipo_Campo("T014_Flag_Situacao_Tributaria","VARCHAR (3) DEFAULT '0'");

//Alterar_Tipo_Campo("T007_Nome_Transportadora","VARCHAR(60)");
//Alterar_Tipo_Campo("T005_Nome_Transportadora","VARCHAR(60)");

Alterar_Tipo_Campo("T005_Flag_Primeira_Liberacao_Credito","varchar(1) DEFAULT '0'");
// C020 Foi criado para o sistema antigo, no hard3 é usado a global "exibirNoDanfeValorTribTransp" para mesma função
Criar_Campo("C020_Flag_Trib_Transparencia","enum('S','N') DEFAULT 'N'");
// CFOP
Criar_Campo("D006_Percentual_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
// NCM
Criar_Campo("D005_Percentual_Trib_Transparencia","DECIMAL(15,2) DEFAULT '0.000000'");
//// --- FIM CAMPOS LEI TRANSP.

Criar_Campo("D053_Inscricao_Estadual_ST","VARCHAR(20)");
Criar_Campo("T190_D031_Id","int(10) unsigned");

Criar_Campo("D024_Cnpj_Matriz","VARCHAR(20)");
Criar_Campo("T006_Flag_Modificar","VARCHAR(1)");
Criar_Campo("T189_Flag_Produzir","VARCHAR(1)");

Criar_Campo("T004_Flag_Pre_Cadastro","VARCHAR(1)");
Criar_Campo("T006_Flag_Pre_Cadastro","VARCHAR(1)");

Criar_Campo("D137_Empresas_Destinatario","VARCHAR(60)");
Criar_Campo("D001_D001_Id","int(10) unsigned");
Criar_Campo("T128_D006_Id","int(10) unsigned");

Criar_Campo("C007_Ultimo_Acesso","DATETIME");
Criar_Campo("C007_Cota_Vendas_Mensal","DECIMAL(15,2) DEFAULT '0.00'");
Criar_Campo("C007_Cota_Vendas_Diaria","DECIMAL(15,2) DEFAULT '0.00'");
Criar_Campo("T075_Data_Atualizacao_Custo", "DATE DEFAULT '0000-00-00'");
Criar_Campo("D009_Quantidade_Balanco", "DECIMAL(10,2) DEFAULT '0.00'");
Criar_Campo("T117_Flag_Atualizar","VARCHAR(1)");


Criar_Indice('T007_Flag_Enviar');
Criar_Indice('T007_TX_Flag_Finalizada');

Criar_Indice('D069_T008_Id');

Criar_Campo("T001_D144_Id","int(10) unsigned");
Criar_Campo("T004_D144_Id","int(10) unsigned");
Criar_Campo("T219_D144_Id","int(10) unsigned");
Criar_Campo("T219_Valor_Frete","decimal(15,5) default 0");
Criar_Campo("T219_Valor_Financeiro","decimal(15,5) default 0");
Criar_Campo("T219_Percentual_Frete","decimal(15,5) default 0");
Criar_Campo("T219_Percentual_Financeiro","decimal(15,5) default 0");
Criar_Campo("T219_Valor_Preco_Venda","decimal(15,5) default 0");
Criar_Campo("T219_Valor_Preco_Venda_Negociado","decimal(14,6)");


Criar_Campo("T225_Valor_Frete","decimal(15,5) default 0");
Criar_Campo("T225_Valor_Financeiro","decimal(15,5) default 0");
Criar_Campo("T225_Percentual_Frete","decimal(15,5) default 0");
Criar_Campo("T225_Percentual_Financeiro","decimal(15,5) default 0");
Criar_Campo("T225_Valor_Preco_Venda","decimal(15,5) default 0");
Criar_Campo("T225_ST_VA_Ajustada","decimal(12,2) DEFAULT 0");
Criar_Campo("T225_Valor_PReco_Venda_Negociado","decimal(14,6)");

Criar_Campo("T219_Flag_Indisponivel","varchar(1) DEFAULT ('N')");
Criar_Campo("T219_Valor_ST_Digitado","decimal(12,4) DEFAULT 0");
Criar_Campo("T219_ST_VA_Ajustada","decimal(12,4) DEFAULT 0");
Criar_Campo("T006_D144_Id","int(10) unsigned");
Criar_Campo("T008_D144_Id","int(10) unsigned");
Criar_Campo("T014_D144_Id","int(10) unsigned");
Criar_Campo("T030_D144_Id","int(10) unsigned");
Criar_Campo("T225_T189_Id","int(10) unsigned");
Criar_Campo("T014_T225_Id","int(10) unsigned");
Criar_Campo("T225_Flag_Cancelada","char(1) DEFAULT 'N'");
Criar_Campo("T224_Flag_Cancelada","char(1) DEFAULT 'N'");
Criar_Campo("T224_Status_Aprovacao","int(1)");
Criar_Campo("T225_Quantidade_Recebida","decimal(10,3)");
Criar_Campo("D001_Flag_Produto_Serie","enum('S','N') DEFAULT 'N'");
Criar_Campo("D085_Data_Previsao_Retorno", "DATE DEFAULT '0000-00-00'");

Criar_Campo("T129_T189_Id","int(10) unsigned");

Criar_Campo("D009_Flag_Balanco","enum('S','N') DEFAULT 'N'");
Criar_Campo("D009_Data_Balanco","date DEFAULT '0000-00-00'");
Criar_Campo("D009_Frequencia_Apos_Balanco","int(5)");

Criar_Campo("T206_Flag_Tipo","char(5)");
Criar_Campo("T015_Flag_Marcar_Lancamento","char(5)");
Criar_Campo("T216_T015_Id","int(10) unsigned");
Criar_Campo("T075_Calculo_THC","varchar(1) DEFAULT '1'");

Criar_Campo("D069_Prazo_Entrega","varchar(100)");
Criar_Campo("D069_MVA","decimal(5,2)");
Criar_Campo("D069_Aliquota_ICMS","decimal(5,2)");
Criar_Campo("D069_Flag_Nacional_Importado","enum('N','I')");
Criar_Campo("C007_Cnpj","varchar(20)");
Criar_Campo("C007_Crc","varchar(20)");
Criar_Campo("C007_Numero","varchar(20)");
Criar_Campo("D007_Tipo_Cobranca","varchar(30)");
Criar_Campo("D009_Percentual_Comissao","decimal(14,5)");
Criar_Campo("D024_Valor_Mensal","decimal(14,2)");
Criar_Campo("D024_Opcoes_Sistema","text");
Criar_Campo("D024_Periodo_Teste","varchar(1)");
Criar_Campo("D006_Flag_Ajustar_MVA","char(1) DEFAULT 'S'");
Criar_Campo("D006_Flag_CRT","char(1)");
Criar_Campo("T218_Flag_Visualizou_Liberacao","enum('S','N') DEFAULT 'S'");
Criar_Campo("T224_Flag_Visualizou_Liberacao","enum('S','N') DEFAULT 'S'");
Criar_Campo("T004_Observacao_Compra","text DEFAULT NULL");
Criar_Campo("T004_Flag_Gerar_Cotacao","char(1) DEFAULT 'N'");
Criar_Campo("T004_T219_Id","int(10) unsigned");
Criar_Campo("T004_C007_Id_Comprador","int(10) unsigned");
Criar_Campo("T006_C007_Id_Comprador","int(10) unsigned");
Criar_Campo("T075_Flag_MP","int(1) DEFAULT '1'");
Criar_Campo("T236_Tempo_Estimado","DATETIME");

Criar_Campo("D006_Flag_Deb_Crd_ST","varchar(1) DEFAULT ''");
Criar_Campo("T014_Flag_Origem_Produto","varchar(1) DEFAULT ''");
Criar_Campo("D137_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T003_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T004_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T005_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T006_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T007_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T008_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T218_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T219_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T224_Flag_Destino_Produto","varchar(1) DEFAULT ''");
Criar_Campo("T225_Flag_Destino_Produto","varchar(1) DEFAULT ''");

Criar_Campo("T006_Observacao_Entrega","int(5)");
Criar_Campo("D004_D146_Id","INT(10) DEFAULT 1");
Criar_Campo("T003_D146_Id","INT(10) DEFAULT 1");
Criar_Campo("T005_D146_Id","INT(10) DEFAULT 1");
Criar_Campo("T007_D146_Id","INT(10) DEFAULT 1");

Criar_Campo("D142_C029_Id","int(10) unsigned");
Criar_Campo("T005_Liberou_Portador_Pagamento","varchar(60)");
Criar_Campo("T005A_Liberou_Credito_Portador","int(10) unsigned");
Criar_Campo("T005_Liberou_Parcelas_Pagamento","int(10)");
Criar_Campo("T003_Liberou_Portador_Pagamento","varchar(60)");
Criar_Campo("T003_Liberou_Parcelas_Pagamento","int(10)");
Criar_Campo("D024_Instrucao_Pagamento","text");
Criar_Campo("T002_Motivo_Cancelamento","text");
Criar_Campo("T006_Flag_Reservar_Estoque","varchar(1) DEFAULT 'S'");
Criar_Campo("D005_Flag_Aplicacao","varchar(2) DEFAULT '0'");
Criar_Campo("T237_Data_Fechado","datetime");
Criar_Campo("D085_T003_Id","int(10)");
Criar_Campo("D085_T182_Id","int(10)");
Criar_Campo("D085_Hora", "time DEFAULT '00:00:00'");
Criar_Campo("D085_Hora_Previsao_Retorno", "time DEFAULT '00:00:00'");
Criar_Campo("D085_Hora_Retorno", "time DEFAULT '00:00:00'");
Criar_Campo("T005_Flag_Notificacao", "varchar(1) DEFAULT 'N'");
Criar_Campo("T005_Flag_Notificacao_Estoque", "varchar(1) DEFAULT 'N'");
Criar_Campo("T003_Flag_Notificacao_Liberacao", "varchar(1) DEFAULT ''");
Criar_Campo("T005_Flag_Notificacao_Liberacao", "varchar(1) DEFAULT ''");
Criar_Campo("T005_Flag_Notificacao_Financeiro", "varchar(1) DEFAULT ''");
Criar_Campo("T005_Flag_Primeira_Liberacao_Credito", "varchar(1) DEFAULT '0'");
Criar_Campo("T005_Prazo_Medio_Orcamento", "int(10)");

Criar_Campo("D009_Quantidade_OC","decimal(15,5) default 0");
Criar_Campo("D009_Quantidade_OP","decimal(15,5) default 0");
Criar_Campo("T003_D148_Id_Entrega","int(10)");
Criar_Campo("T003_D148_Id_Cobranca","int(10)");
Criar_Campo("T005_D148_Id_Entrega","int(10)");
Criar_Campo("T005_D148_Id_Cobranca","int(10)");
Criar_Campo("T007_D148_Id_Entrega","int(10)");
Criar_Campo("T007_D148_Id_Cobranca","int(10)");
Criar_Campo("T117_D148_Id_Entrega","int(10)");
Criar_Campo("T117_D148_Id_Cobranca","int(10)");
Criar_Campo("C007_Flag_Ausente","varchar(1) DEFAULT 'N'");
Criar_Campo("T006_Flag_Conf_Cega","char(1) DEFAULT 'N'");
Criar_Campo("T007_Numero_Lote_Rps","int(10)");
Criar_Campo("T007_Numero_Rps","int(10)");
Criar_Campo("T007_Serie_Rps","int(10)");
Criar_Campo("C004_Ultimo_Lote_Rps","int(10) DEFAULT '0'");
Criar_Campo("C004_Ultimo_Numero_Rps","int(15) DEFAULT '0'");
Criar_Campo("T007_Item_Servico_Nfse","varchar(5)");
Criar_Campo("T013_T084_Id","int(10)");
Criar_Campo("T005_Flag_Reenviado_Almoxarifado_Lido","char(1) DEFAULT 'N'");
Criar_Campo("T003_Status_Retorno_Email","varchar(10)");
Criar_Campo("T006_Flag_Gerar_Cotacao","char(1) DEFAULT 'N'");

Criar_Campo("T007_Pgto_Parcelas","varchar(10)");
Criar_Campo("T007_Pgto_Primeiro_Vcto_Tipo","varchar(10)");
Criar_Campo("T007_Pgto_Primeiro_Vcto_Valor","varchar(10)");
Criar_Campo("T007_Pgto_Dias_Vcto_Tipo","varchar(10)");
Criar_Campo("T007_Pgto_Dias_Vcto_Valor","varchar(10)");
Criar_Campo("T007_Pgto_Portador","varchar(10)");
Criar_Campo("T007_Pgto_Data","datetime");
Criar_Campo("T007_Pgto_C007_Id","int(10) unsigned");
Criar_Campo("T001_Quantidade_Saldo_Mes_Anterior","decimal(10,3)");
Criar_Campo("T001_Valor_Custo_Mes_Anterior","decimal(10,5)");
Criar_Campo("T001_Valor_Custo_Medio_Mes_Anterior","decimal(10,5)");

Criar_Campo("T014_T066_Id","int(10) unsigned");
Criar_Campo("T008_T066_Id","int(10) unsigned");

Criar_Campo("D009_Preco_Loja_Real","decimal(15,2) default 0");
Criar_Campo("D009_Preco_Loja","decimal(15,2) default 0");
Criar_Campo("D009_Flag_Preco_Loja","char(1) DEFAULT 'N'");
Criar_Campo("C020_Cupom_Fiscal_Transportadora","int(10) unsigned");
Criar_Campo("D009_Preco_Loja_Demonstrativo", "text");
Criar_Campo("D009_Preco_Loja_T001_Id","int(10) unsigned");
Criar_Campo("D009_Preco_Loja_Real_Demonstrativo", "text");
Criar_Campo("D009_Preco_Loja_Real_T001_Id","int(10) unsigned");
Criar_Campo("D009_Qtde_Reabastecimento_Loja","decimal(15,5) not null default 0");
Criar_Campo("T007_Flag_Analise_Credito", "int(1) not null default 0");
Criar_Campo("D024_Flag_Serasa", "int(1) not null default 0");
Criar_Campo("T002_Data_Exclusao", "date DEFAULT '0000-00-00'");

Criar_Campo("T008_Quantidade_Devolucao", "decimal(15,5) default 0");
Criar_Campo("T014_Quantidade_Devolucao", "decimal(15,5) default 0");
Criar_Campo("T002_Valor_Recebido_Total", "decimal(15,5) default 0");
Criar_Campo("T002_Valor_Troco", "decimal(15,5) default 0");

Criar_Campo("T007_Arquivo_XML_Temp","TEXT");

Criar_Campo("T006_Quantidade_OC", "decimal(15,5) default 0");
Criar_Campo("T006_Quantidade_OP", "decimal(15,5) default 0");
Criar_Campo("T006_Prioridade", "int(10) default 0");

Criar_Campo("T013_Data_Entrada_Estoque","datetime");
Criar_Campo("T013_Data_Financeiro","datetime");
Criar_Campo("T003_Cliente_Area","varchar(50)");
Criar_Campo("T005_Cliente_Area","varchar(50)");
Criar_Campo("T007_Cliente_Area","varchar(50)");
Criar_Campo("D007_Instrucao_Cobranca_1","varchar(2)");
Criar_Campo("D007_Instrucao_Cobranca_2","varchar(2)");
Criar_Campo("T038_Info_Complementar_1","varchar(20)");
Criar_Campo("T038_Info_Complementar_2","varchar(20)");
Criar_Campo("T038_Info_Complementar_3","varchar(20)");
Criar_Campo("T038_Valor_Multa", "decimal(5,2)");

Criar_Campo("T001_D004_Id","int(10)");
Criar_Campo("T014_D004_Id","int(10)");
Criar_Campo("T008_D004_Id","int(10)");

Criar_Campo("D146_Flag_Principal","char(1) DEFAULT 'N'");

Criar_Campo("T066_Quantidade_Balanco", "decimal(10,2) DEFAULT '0'");
Criar_Campo("T066_Flag_Balanco","char(1) DEFAULT 'N'");
Criar_Campo("T066_Data_Balanco","date DEFAULT '0000-00-00'");
Criar_Campo("T066_Frequencia_Apos_Balanco","int(5)");
Criar_Campo("T008_Compra_T001_Id","int(10) unsigned");

Criar_Campo("T014_Codigo_Barras","varchar(25)");
Criar_Campo("T014A_Codigo_Barras_Tributavel","varchar(25)");
Criar_Campo("D009_Meses_MMF","INT(5)");
Criar_Campo("D009_T001_Id","INT(10)");
Criar_Campo("D009_D069_Id","INT(10) UNSIGNED");
Criar_Campo("D009_C007_Id_Comprador","INT(10)");
Criar_Campo("D009_Data_Reprocessa_Historico","DATETIME");
Criar_Campo("D009_Valor_Custo_ST_Medio_Unitario","DECIMAL(10,5)");
Criar_Campo("D009_Valor_Custo_ST_Unitario","DECIMAL(10,5)");

Criar_Campo("T055_Aliquota_ICMS", "decimal(5,2)");
Criar_Campo("T055_Aliquota_ICMS_Presumido", "decimal(5,2)");
Criar_Campo("T055_Percentual_Reducao_ICMS", "decimal(5,2)");
Criar_Campo("T055_Percentual_Reducao_ICMS_Presumido", "decimal(5,2)");
Criar_Campo("T055_Aliquota_ICMS_PIS_COFINS", "decimal(5,2)");
Criar_Campo("T055_Aliquota_ICMS_Credito_Presumido", "decimal(5,2)");
Criar_Campo("T055_Percentual_Acrescimo_Impostos", "decimal(5,2) DEFAULT 0");

Criar_Campo("T055_Adicao_Item", "varchar(5) default ''");

Criar_Campo("T030_T066_Id","int(10) unsigned");
Criar_Campo("T238_T066_Id","int(10) unsigned");
Criar_Campo("T006_T066_Id","int(10) unsigned");

//Criar_Campo("T162_Extra","varchar(255)");
Criar_Campo("T002_Flag_Agrupado_Reparcelado","VARCHAR(1) DEFAULT 'N'");
Criar_Campo("T008_Informacoes_Adicionais","varchar(500)");

Criar_Campo("D009_Quantidade_Estoque_Loja","decimal(12,2) DEFAULT '0' NOT NULL");
Criar_Campo("D009_Quantidade_Estoque_Fora","decimal(12,2) DEFAULT '0' NOT NULL");

Criar_Campo("T237_Flag_Travar","CHAR(1) DEFAULT 'N' NOT NULL");
Criar_Campo("D009_Flag_Incluir_Produto","CHAR(1) DEFAULT 'N' NOT NULL");
Criar_Campo("T013_Flag_Estoque_Finalizado","VARCHAR(1) DEFAULT 'N' NOT NULL");
Criar_Campo("D005_Flag_Ativo","VARCHAR(1) DEFAULT 'S' NOT NULL");
Criar_Campo("D001_Codigo_Barra1","VARCHAR(20)");
Criar_Campo("D069_Descricao_Produto","varchar(60)");
Criar_Campo("D069_Flag_Origem_Produto","varchar(1)");
Criar_Campo("D069_T075_Id","int(10) unsigned");
Criar_Campo("D069_T206_Id","int(10) unsigned");
Criar_Campo("T004_Demonstrativo_Preco","text");
Criar_Campo("T004_Demonstrativo_Preco_Tabela","text");
Criar_Campo("T004_Demonstrativo_Preco_Cotacao","text");
Criar_Campo("T004_Valor_Preco_Compra","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_Valor_Preco_Tabela","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_Valor_Preco_Cotacao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_T001_Id","int(11)");

Criar_Campo("D007_Valor_Maximo_Boleto_Sem_Registro","decimal(14,5)");

Criar_Campo("C004_CFOP_Revenda_PR","int(5)");
Criar_Campo("C008_Flag_Inicial_Codigo","varchar(3)");
Criar_Campo("C020_Pedido_Imprimir_Campo_Entrega","enum('S','N')");
Criar_Campo("C020_Pedido_Imprimir_Campo_IPI","enum('S','N')");
Criar_Campo("C020_NFe_SCAN_Ativado","enum('S','N')");
Criar_Campo("C020_NFe_SVC_Ativado","enum('S','N') default 'N'");
Criar_Campo("C020_NFCe_SVC_Ativado","enum('S','N') default 'N'");
Criar_Campo("C020_Data_NFe_SVC_Ativado","datetime");
Criar_Campo("C020_Data_NFCe_SVC_Ativado","datetime");
Criar_Campo("D001_NCM","varchar(15)");
Criar_Campo("D001_IPV_1","decimal(10,5)");
Criar_Campo("D009_Valor_Preco_Importado","decimal(14,5)");
Criar_Campo("D009_T014_Id","int(10)");
Criar_Campo("D009_Quantidade_Cabos","decimal(14,3)");
Criar_Campo("D016_Valor_Iene","decimal(10,4)");
Criar_Campo("D016_Valor_Dolar","decimal(10,4)");
Criar_Campo("D016_Valor_Euro","decimal(10,4)");
Criar_Campo("D049_D009_Id","int(10)");
Criar_Campo("D056_Codigo","varchar(25)");
Criar_Campo("D056_Nome_Conta","varchar(40)");
Criar_Campo("D056_Flag_Tipo","varchar(1)");
Criar_Campo("D070_D082_Id","int(10)");
Criar_Campo("D070_C004_Id","int(10)");
Criar_Campo("D070_Flag_Tabela_Propria","varchar(1)");
Criar_Campo("D082_Codigo_Ferramenta","varchar(10)");
Criar_Campo("D082_Flag_Ativo","varchar(1)");
Criar_Campo("D085_Retorno","text");
Criar_Campo("D125_Validadade","date");
Criar_Campo("T003_Liberou_Alcada_Data","datetime");
Criar_Campo("T003_Liberou_Alcada_IPV","decimal(10,4)");
Criar_Campo("T004_Quantidade_Estoque","decimal(12,2)");
Criar_Campo("T005_Liberou_Credito_Valor_Total","decimal(10,2)");
Criar_Campo("T005_Liberou_Credito_Desconto","decimal(10,2)");
Criar_Campo("T005_Liberou_Credito_Prazo_Medio","decimal(10,4)");
Criar_Campo("T006_Retirou_Qtd","decimal(20,5)");
Criar_Campo("T006_T001_Id","int(11)");
Criar_Campo("T011_Centro_Custo","varchar(10)");
Criar_Campo("T013_Cnpj_Fornecedor","varchar(20)");
Criar_Campo("T013_Flag_Financeiro_Finalizado","varchar(1)");
Criar_Campo("T014_Observacao_Divergencia","text");
Criar_Campo("T014_Flag_Conferencia_Cega","enum('S','N') NOT NULL DEFAULT 'N'");
Criar_Campo("T022_C004_Id","int(10) unsigned NOT NULL DEFAULT 0");
Criar_Campo("T022_Codigo_Operador","int(6) NOT NULL DEFAULT 0");
Criar_Campo("T022_Nome","varchar(30)");
Criar_Campo("T022_Setor","varchar(1)");
Criar_Campo("T022_Ordem","int(5) NOT NULL DEFAULT 0");
Criar_Campo("T023_T021_Id","int(10) unsigned");
Criar_Campo("T023_C004_Id","int(10) unsigned");
Criar_Campo("T023_Codigo_Maquina","int(10) NOT NULL DEFAULT 0");
Criar_Campo("T023_Lote","int(4) NOT NULL DEFAULT 0");
Criar_Campo("T023_Codigo_Produto","varchar(30) NOT NULL DEFAULT 0");
Criar_Campo("T023_Letra","varchar(1)");
Criar_Campo("T023_Codigo_Operador","int(10)");
Criar_Campo("T023_Ocorrencia","varchar(5)");
Criar_Campo("T023_Data","date");
Criar_Campo("T023_Quantidade_Sucatada","int(10) NOT NULL DEFAULT 0");
Criar_Campo("T023_Flag_Servico","int(1) NOT NULL DEFAULT 0");
Criar_Campo("T023_Numero_Ficha","int(10) NOT NULL DEFAULT 0");
Criar_Campo("T066_Excluir","varchar(1)");
Criar_Campo("T066_Codigo_Produto","varchar(50)");
Criar_Campo("T186_C007_Id","int(11) unsigned");
Criar_Campo("T186_Flag_Operacao","varchar(1)");
Criar_Campo("T225_Flag_OS_Importada","varchar(1)");
Criar_Campo("T225_Flag_Autorizado","varchar(1)");
Criar_Campo("T006_Demonstrativo_Preco","text");
Criar_Campo("T006_Demonstrativo_Preco_Tabela","text");
Criar_Campo("T006_Demonstrativo_Preco_Cotacao","text");
Criar_Campo("T006_Valor_Preco_Compra","decimal(14,5) DEFAULT '0'");
Criar_Campo("T006_Valor_Preco_Tabela","decimal(14,5) DEFAULT '0'");
Criar_Campo("T006_Valor_Preco_Cotacao","decimal(14,5) DEFAULT '0'");
Criar_Campo("T204_T209_Id","int(10) unsigned");
Criar_Campo("C004_Observacao_Ordem_Compra","text CHARSET utf8 DEFAULT NULL");
Criar_Campo("C004_IPV_Padrao","decimal(10,6) DEFAULT 1.00");
Criar_Campo("C004_ICF_Padrao","decimal(10,6) DEFAULT 1.00");
Criar_Campo("D024_Codigo_Regime_Tributario","int(1) DEFAULT 0");
Criar_Campo("D014_Flag_Registrar_Conta","char(1) DEFAULT 'N'");
Criar_Campo("D014_D031_Id","int(10) unsigned");
Criar_Campo("D014_D024_Id","int(10) unsigned");
Criar_Campo("D006_D073_Id","int(10) unsigned");
Criar_Campo("T004_D006_Id_Info","text");
Criar_Campo("T006_D006_Id_Info","text");
Criar_Campo("T008_D006_Id_Info","text");
Criar_Campo("D014_Flag_Pagamento_Unico","VARCHAR(1) DEFAULT 'N'");
Criar_Campo("T219_D006_Id_Info","text");
Criar_Campo("T225_D006_Id_Info","text");
Criar_Campo("T004_D005_Id_Info","text");
Criar_Campo("T006_D005_Id_Info","text");
Criar_Campo("T008_D005_Id_Info","text");
Criar_Campo("T219_D005_Id_Info","text");
Criar_Campo("T225_D005_Id_Info","text");
Criar_Campo("T055_T225_Id","int(10) unsigned");
Criar_Campo("T224_Flag_Tipo","int(11) unsigned DEFAULT 1");
Criar_Campo("T005_T224_Id","int(10) unsigned");
Criar_Campo("T006_T225_Id","int(10) unsigned");
Criar_Campo("T006_C004_Id_Estoque","int(10) unsigned");
Criar_Campo("T005_C004_Id_Origem","int(10) unsigned");
Criar_Campo("D137_Flag_Origem_Pedido","varchar(1)");
Criar_Campo("T006_T006_Id_Origem","int(10) unsigned");
Criar_Campo("T014_Numero_Pedido_Compra","varchar(30)");
Criar_Campo("T014_Item_Pedido_Compra","varchar(30)");
Criar_Campo("D132_Grid_Tabela","longblob");
Criar_Campo("D132_DivId","varchar(50)");
Criar_Campo("T007_Nome_Arquivo_PDV","varchar(100)");
Criar_Campo("T007_Conteudo_Arquivo_PDV","text");
Criar_Campo("D007_Flag_Emite_Cupom","varchar(1) DEFAULT 'N'");
Criar_Campo("D014_Flag_Emite_Cupom","varchar(1) DEFAULT 'N'");
Criar_Campo("T007_Conteudo_Arquivo_PDV","text");
Criar_Campo("T005_Flag_Pdv_Importado","char(1) DEFAULT 'N'");
Criar_Campo("T006_Flag_Pdv_Importado","char(1) DEFAULT 'N'");
Criar_Campo("T003_Casas_Decimais_Preco","int(1) DEFAULT 4");
Criar_Campo("T005_Casas_Decimais_Preco","int(1) DEFAULT 4");
Criar_Campo("T224_Flag_Coleta","char(1) ''");
Criar_Campo("T224_Hora_Coleta","time");
Criar_Campo("T224_D109_Id","int(10) unsigned");
Criar_Campo("T224_D163_Id","varchar(30)");
Criar_Campo("T224_Flag_Transporte_Coleta","int(10)");
Criar_Campo("T218_Grupo","int(10)");
Criar_Campo("T224_Grupo","int(10)");
Criar_Campo("T219_Flag_Melhor_Preco","char(1) DEFAULT 'N'");
Criar_Campo("T219_Flag_Melhor_Prazo","char(1) DEFAULT 'N'");
Criar_Campo("C007_Programacao_Vendas","text DEFAULT ''");
Criar_Campo("T224_Valor_Pendente","decimal(14,5)");
Criar_Campo("T003_Valor_Pendente","decimal(14,5)");
Criar_Campo("T224_Data_Hora_Coleta","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T216_D031_Id","int(10)");
Criar_Campo("T217_D031_Id","int(10)");
Criar_Campo("C007_Valor_Maximo_OC","decimal(14,5)");
Criar_Campo("C007_Valor_Maximo_OC_Dia","decimal(14,5)");
Criar_Campo("C007_Valor_Maximo_OC_Mes","decimal(14,5)");
Criar_Campo("C007_Valor_Maximo_OC_Fornecedor","decimal(14,5)");
Criar_Campo("D085_Flag_Lido","char(1) DEFAULT 'N'");
Criar_Campo("T219_Demonstrativo_Preco","text");
Criar_Campo("T219_Valor_Custo_Medio_Unitario","decimal(14,5) DEFAULT '0'");
Criar_Campo("T225_Valor_Custo_Medio_Unitario","decimal(14,5) DEFAULT '0'");
Criar_Campo("T218_Flag_Calcular_ST","varchar(1) DEFAULT 'S'");
Criar_Campo("T218_Flag_Calcular_IPI","varchar(1) DEFAULT 'S'");
Criar_Campo("T218_Flag_Calcular_ICMS","varchar(1) DEFAULT 'S'");
Criar_Campo("T224_Flag_Calcular_ST","varchar(1) DEFAULT 'S'");
Criar_Campo("T224_Flag_Calcular_IPI","varchar(1) DEFAULT 'S'");
Criar_Campo("T224_Flag_Calcular_ICMS","varchar(1) DEFAULT 'S'");
Criar_Campo("D069_Data_Atualizacao_Custo","date DEFAULT '0000-00-00'");
Criar_Campo("D069_C007_Id_Atualizacao_Custo","int(11) unsigned");
Criar_Campo("D009_Data_Atualizacao_Custo_Fornecedor","date DEFAULT '0000-00-00'");
Criar_Campo("D009_Flag_Custo_Utilizado","varchar(2) DEFAULT ''");
Criar_Campo("T225_Valor_Custo_Medio_Unitario","decimal(14,5) DEFAULT '0'");
Criar_Campo("D049_Percentual_Acrescimo_Custo","decimal(10,2) default '0.00'");
Criar_Campo("T241_Data","date DEFAULT '0000-00-00'");
Criar_Campo("T241_Flag_Homologado","varchar(1) DEFAULT 'N'");
Criar_Campo("D069_Campos_Atualizados","text");
Criar_Campo("D006_Codigo_CFOP_Contra_Partida","varchar(30)");
Criar_Campo("D145_Codigo_Regime_Tributario","varchar(30)");
Criar_Campo("T014_CFOP_Xml","varchar(50)");
Criar_Campo("T014_D006_Id_Original","int(10) unsigned");
Criar_Campo("T220_D031_Id","int(11) DEFAULT '0'");
Criar_Campo("T226_D031_Id","int(11) DEFAULT '0'");
Criar_Campo("D009_Sugestao_Compra_Vendas","decimal(14,5) DEFAULT '0'");
Criar_Campo("D069_D006_Id_Info","text");
Criar_Campo("D069_D005_Id_Info","text");
Criar_Campo("D013_Flag_Ativo","varchar(1) DEFAULT 'S'");
Criar_Campo("T002_Flag_Marcar_Lancamento","char(5)");
Criar_Campo("T216_T002_Id","int(10) unsigned");
Criar_Campo("T002_Observacao_Reparcelamento","text");
Criar_Campo("T015_Observacao_Reparcelamento","text");
Criar_Campo("T003_C007_Id","int(10) unsigned");
Criar_Campo("T005_C007_Id","int(10) unsigned");
Criar_Campo("D024_Percentual_Comissao_Interno","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("D024_Percentual_Comissao_Externo","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("C007_Percentual_Comissao","decimal(5,2) DEFAULT '0.00'");

Criar_Campo("T003_Percentual_Comissao_Representante","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T003_Valor_Comissao_Representante","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T003_Percentual_Comissao_Externo","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T003_Valor_Total_Comissao_Externo","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T003_Percentual_Comissao_Usuario","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T003_Valor_Total_Comissao_Usuario","decimal(14,5) DEFAULT '0.00000'");

Criar_Campo("T005_Percentual_Comissao_Representante","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Comissao_Representante","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T005_Percentual_Comissao_Externo","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_Comissao_Externo","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T005_Percentual_Comissao_Usuario","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_Comissao_Usuario","decimal(14,5) DEFAULT '0.00000'");

Criar_Campo("T007_Percentual_Comissao","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Comissao_Representante","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T007_Percentual_Comissao_Externo","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Comissao_Externo","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T007_Percentual_Comissao_Usuario","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Comissao_Usuario","decimal(14,5) DEFAULT '0.00000'");

Criar_Campo("T002_Percentual_Comissao_Representante","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T002_Valor_Comissao_Representante","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T002_Percentual_Comissao_Externo","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T002_Valor_Total_Comissao_Externo","decimal(14,5) DEFAULT '0.00000'");
Criar_Campo("T002_Percentual_Comissao_Usuario","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T002_Valor_Total_Comissao_Usuario","decimal(14,5) DEFAULT '0.00000'");

Criar_Campo("T003_Percentual_Margem","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Margem","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T005_Percentual_Margem","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T006_Percentual_Margem","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T007_Percentual_Margem","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_Margem","decimal(5,2) DEFAULT '0.00'");

Criar_Campo("T005_Percentual_Estoque_Disponivel","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("D049_Quantidade_Embalagem_Compra","decimal(10,2) DEFAULT '0.00'");

Criar_Campo("T005_Nome_Status","char(30)");
Criar_Campo("D009_Quantidade_Estoque_Similar","decimal(12,2) DEFAULT '0' NOT NULL");
Criar_Campo("C007_Banco_Senha","varchar(255)");
Criar_Campo("T013_Fuso_Horario_Emissao","varchar(6)");
Criar_Campo("T005_Hora_Emissao","time DEFAULT NULL");

Criar_Campo("T008_Valor_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T008_Valor_Base_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T008_Aliquota_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T008_Percentual_Reducao_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T007_Valor_Total_ICMS_Oculto","DECIMAL(14,2)");
Criar_Campo("T007_Valor_Total_Base_ICMS_Oculto","DECIMAL(14,2)");

Criar_Campo("T007_Valor_Total_ICMS_Credito","DECIMAL(14,2)");
Criar_Campo("T007_Aliquota_ICMS_Credito","DECIMAL(14,2)");


Criar_Campo("D070_Flag_Codigos_Comuns","VARCHAR(1) DEFAULT 'S'");

Criar_Campo("T003_D044_Id","int(10) unsigned DEFAULT '1'");
Criar_Campo("T003_Cotacao_Moeda","decimal(10,5) DEFAULT 0");
Criar_Campo("T005_D044_Id","int(10) unsigned DEFAULT '1'");
Criar_Campo("T005_Cotacao_Moeda","decimal(10,5) DEFAULT 0");
Criar_Campo("T224A_D044_Id","int(10) unsigned DEFAULT '1'");
Criar_Campo("T224A_Cotacao_Moeda","decimal(10,5) DEFAULT 0");

Criar_Campo("D049_Valor_Preco_KG","decimal(14,4) DEFAULT 0");
Criar_Campo("T005_Separacao_Lote","int(11)");
Criar_Campo("T005_T005_Id_Origem","int(10) unsigned");
Criar_Campo("T055_Flag_Calculo_Manual","char(1) DEFAULT 'N'");
Criar_Campo("T055_Valor_Total_Multa", "decimal(19,6)");
Criar_Campo("T055_Valor_Juros_Multa", "decimal(19,6)");
Criar_Campo("T055_Valor_Dumping", "decimal(19,6)");
Criar_Campo("T055_Valor_Seguro", "decimal(19,6)");
Criar_Campo("T055_Valor_Total_Seguro", "decimal(19,6)");
Criar_Campo("T055_Valor_Custo_Debug", "decimal(19,6)");
Criar_Campo("T055_Valor_Base_II_Debug", "decimal(19,6)");
Criar_Campo("T075_Somar_PIS_Custo", "char(1) DEFAULT 'N'");
Criar_Campo("T075_Somar_COFINS_Custo", "char(1) DEFAULT 'N'");
Criar_Campo("T075_Somar_Seguro_Custo", "char(1) DEFAULT 'N'");
Criar_Campo("T075_Somar_AFRMM_Custo", "char(1) DEFAULT 'N'");
Criar_Campo("T075_Somar_ICMS_Pagar_Custo", "char(1) DEFAULT 'N'");
Criar_Campo("T075_Somar_IPI_Custo", "char(1) DEFAULT 'N'");
Criar_Campo("D027_Taxa", "decimal(10,4) DEFAULT '0.0000'");
Criar_Campo("D027_Descricao_Taxa", "varchar(30) DEFAULT ''");
Criar_Campo("D006_Observacao_Produto", "varchar(500) DEFAULT ''");
Criar_Campo("D085_T002_Id","int(10)");
Criar_Campo("D014_D154_Id","int(10)");
Criar_Campo("D014_D073_Id","int(10)");
Criar_Campo("D027_Flag_Credito_Antecipado","char(1) DEFAULT ''");
Criar_Campo("D024_Valor_Credito_Antecipado","decimal(14,2) DEFAULT '0.00'");
Criar_Campo("D024_Valor_Credito_Troca","decimal(14,2) DEFAULT '0.00'");
Criar_Campo("T002_Valor_Saldo_Credito","decimal(14,2) DEFAULT '0.00'");
Criar_Campo("T015_Valor_Saldo_Credito","decimal(14,2) DEFAULT '0.00'");
Criar_Campo("D031_Flag_Credito_Antecipado","char(1) DEFAULT ''");
Criar_Campo("D027_Baixa_Automatica_Dias","int(10) DEFAULT '0'");
Criar_Campo("D014_Dia_Semana_Vencimento","char(1) DEFAULT ''");
Criar_Campo("D014_Observacao","text");
Criar_Campo("T002_Observacao_Cheque","text");
Criar_Campo("T002_Cheque_Repassado","varchar(60)");
Criar_Campo("T007_Flag_Destino_Operacao","char(1)");
Criar_Campo("T007_Fuso_Horario_Emissao","varchar(6)");
Criar_Campo("T007_Fuso_Horario_Saida","varchar(6)");
Criar_Campo("T007_Fuso_Horario_Contingencia","varchar(6)");
Criar_Campo("C004_Fuso_Horario","varchar(40)");
Criar_Campo("T007_Hora_Emissao","time");
Criar_Campo("T008_Flag_Transporte_DI", "char(10)");
Criar_Campo("T008_Valor_Total_AFRMM", "decimal(14,2) DEFAULT '0.00'");
Criar_Campo("T008_Flag_Tipo_Intermedio_DI", "varchar(50)");
Criar_Campo("T008_CNPJ_Importador", "varchar(50)");
Criar_Campo("T008_UF_Importador", "char(10)");
Criar_Campo("T008_Numero_Drawback_Importacao", "varchar(50)");
Criar_Campo("T008_Numero_Drawback_Exportacao", "varchar(50)");
Criar_Campo("T008_Numero_Registro_Exportacao", "varchar(50)");
Criar_Campo("T008_Chave_Acesso_Exportacao", "varchar(50)");
Criar_Campo("T008_Quantidade_Exportada", "decimal(15,4) DEFAULT '0.0000'");
Criar_Campo("T008_Percentual_GN_GLP", "decimal(6,4) DEFAULT '0.0000'");
Criar_Campo("T008_Numero_Recopi", "varchar(20) DEFAULT ''");
Criar_Campo("T001_Aliquota_PIS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T001_Aliquota_COFINS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T001_Valor_PIS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T001_Valor_COFINS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("D006_Flag_Deb_Crd_PIS", "varchar(1)");
Criar_Campo("D006_Flag_Deb_Crd_COFINS", "varchar(1)");
Criar_Campo("D006_Motivo_Desoneracao_ICMS", "varchar(10)");
Criar_Campo("T004_Valor_ICMS_Desonerado", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T006_Valor_ICMS_Desonerado", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T008_Valor_ICMS_Desonerado", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T004_Valor_Base_ICMS_Desonerado", "decimal(20,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T006_Valor_Base_ICMS_Desonerado", "decimal(20,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T008_Valor_Base_ICMS_Desonerado", "decimal(20,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T003_Valor_Total_ICMS_Desonerado", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_ICMS_Desonerado", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_ICMS_Desonerado", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T004_Aliquota_ICMS_Oculto","decimal(14,2) DEFAULT '0.00'");
Criar_Campo("T004_Motivo_Desoneracao_ICMS", "varchar(10)");
Criar_Campo("T006_Motivo_Desoneracao_ICMS", "varchar(10)");
Criar_Campo("T008_Motivo_Desoneracao_ICMS", "varchar(10)");
Criar_Campo("D027_Id_Portador_PDV", "int(10)");
Criar_Campo("D027_Operacao_Importar_PDV", "varchar(10)");
Criar_Campo("C004_IPV_Padrao_Sem_Lucro","decimal(10,6) DEFAULT 1.00");
Criar_Campo("C004_IPV_Padrao_Demonstrativo","text");
Criar_Campo("T004_Valor_Lucro","decimal(10,2)");
Criar_Campo("T004_IPV_Sem_Lucro","decimal(10,4)");
Criar_Campo("T006_Valor_Lucro","decimal(10,2)");
Criar_Campo("T006_IPV_Sem_Lucro","decimal(10,4)");
Criar_Campo("T008_Valor_Lucro","decimal(10,2)");
Criar_Campo("T008_IPV_Sem_Lucro","decimal(10,4)");
Criar_Campo("T001_Quantidade_Estoque_Inicial","decimal(14,3) default '0.000'");
Criar_Campo("T004_Demonstrativo_Custo_Tabela","text");
Criar_Campo("T006_Demonstrativo_Custo_Tabela","text");
Criar_Campo("D070_Data_Vencimento","DATE DEFAULT '0000-00-00'");
Criar_Campo("T219_Valor_Custo_Momento_Compra","decimal(12,5)");
Criar_Campo("T225_Valor_Custo_Momento_Compra","decimal(12,5)"); 
Criar_Campo("D006_D088_Id","int(10)");
Criar_Campo("D009_Origem_Mercadoria","varchar(10)");
Criar_Campo("D007_Numero_Sequencial_Remessa","int(10)");
Criar_Campo("T246_Valor","decimal(10,2)");
Criar_Campo("T246_Data_Vencimento","date");
Criar_Campo("D132_Flag_Exibir_Dashboard","varchar(1) DEFAULT 'S'");
Criar_Campo("D132_Flag_Exibir_Alcada","varchar(1) DEFAULT '1'");
Criar_Campo("T013_Flag_Finalidade_NFe","enum('1','2','3','4')");
Criar_Campo("C007_Flag_Departamento","varchar(1)");
Criar_Campo("D151_Id_Pai","int(10)");
Criar_Campo("D151_Where","varchar(20)");
Criar_Campo("T246_Data_Deposito","date default '0000-00-00'");
Criar_Campo("T246_D007_Id","int(10) not null");
Criar_Campo("T246_Cpf_Cnpj","varchar(20)");
Criar_Campo("T002_Valor_Total_Cheques","decimal(14,2) default 0");
Criar_Campo("T003_C004_Id_Estoque","int(10)");
Criar_Campo("T005_C004_Id_Estoque","int(10)");
Criar_Campo("T007_C004_Id_Estoque","int(10)");
Criar_Campo("D137_Origem_Estoque","varchar(1)");
Criar_Campo("D009_Quantidade_DF","decimal(15,5) default 0");
Criar_Campo("D009_Quantidade_DM","decimal(15,5) default 0");
Criar_Campo("D009_Quantidade_OE","decimal(15,5) default 0");

Criar_Campo("T003_Valor_Total_Base_IRPJ","decimal(10,2) default '0.00'");
Criar_Campo("T005_Valor_Total_Base_IRPJ","decimal(10,2) default '0.00'");
Criar_Campo("T007_Valor_Total_Base_IRPJ","decimal(10,2) default '0.00'");
Criar_Campo("T003_Valor_Total_IRPJ","decimal(10,2) default '0.00'");
Criar_Campo("T005_Valor_Total_IRPJ","decimal(10,2) default '0.00'");
Criar_Campo("T007_Valor_Total_IRPJ","decimal(10,2) default '0.00'");

Criar_Campo("T003_Valor_Total_Base_CSLL","decimal(10,2) default '0.00'");
Criar_Campo("T005_Valor_Total_Base_CSLL","decimal(10,2) default '0.00'");
Criar_Campo("T007_Valor_Total_Base_CSLL","decimal(10,2) default '0.00'");
Criar_Campo("T003_Valor_Total_CSLL","decimal(10,2) default '0.00'");
Criar_Campo("T005_Valor_Total_CSLL","decimal(10,2) default '0.00'");
Criar_Campo("T007_Valor_Total_CSLL","decimal(10,2) default '0.00'");

Criar_Campo("T004_Aliquota_IRPJ","decimal(5,2) default '0.00'");
Criar_Campo("T006_Aliquota_IRPJ","decimal(5,2) default '0.00'");
Criar_Campo("T008_Aliquota_IRPJ","decimal(5,2) default '0.00'");
Criar_Campo("T004_Valor_Base_IRPJ","decimal(20,5) default '0.00000'");
Criar_Campo("T006_Valor_Base_IRPJ","decimal(20,5) default '0.00000'");
Criar_Campo("T008_Valor_Base_IRPJ","decimal(20,5) default '0.00000'");
Criar_Campo("T004_Valor_IRPJ","decimal(15,2) default '0.00'");
Criar_Campo("T006_Valor_IRPJ","decimal(15,2) default '0.00'");
Criar_Campo("T008_Valor_IRPJ","decimal(15,2) default '0.00'");

Criar_Campo("T004_Aliquota_CSLL","decimal(5,2) default '0.00'");
Criar_Campo("T006_Aliquota_CSLL","decimal(5,2) default '0.00'");
Criar_Campo("T008_Aliquota_CSLL","decimal(5,2) default '0.00'");
Criar_Campo("T004_Valor_Base_CSLL","decimal(20,5) default '0.00000'");
Criar_Campo("T006_Valor_Base_CSLL","decimal(20,5) default '0.00000'");
Criar_Campo("T008_Valor_Base_CSLL","decimal(20,5) default '0.00000'");
Criar_Campo("T004_Valor_CSLL","decimal(15,2) default '0.00'");
Criar_Campo("T006_Valor_CSLL","decimal(15,2) default '0.00'");
Criar_Campo("T008_Valor_CSLL","decimal(15,2) default '0.00'");

Criar_Campo("T004_Demonstrativo_Preco_Venda", "text");
Criar_Campo("T006_Demonstrativo_Preco_Venda", "text");

Criar_Campo("C004_Percentual_IRPJ", "decimal(5,2) default '0.00'");
Criar_Campo("C004_Percentual_CSLL", "decimal(5,2) default '0.00'");
Criar_Campo("D149_Aliquota_IRPJ", "decimal(5,2) default '0.00'");
Criar_Campo("D149_Aliquota_CSLL", "decimal(5,2) default '0.00'");
Criar_Campo("D006_Flag_Envia_PIS_XML", "enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Envia_COFINS_XML", "enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Envia_IRPJ_XML", "enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Envia_CSLL_XML", "enum('S','N') default 'N'");

Criar_Campo("D001_Preco_1", "decimal(15,4) default '0.0000'");
Criar_Campo("D001_Preco_2", "decimal(15,4) default '0.0000'");
Criar_Campo("D001_Preco_3", "decimal(15,4) default '0.0000'");
Criar_Campo("D001_Preco_4", "decimal(15,4) default '0.0000'");

Criar_Campo("T003_Valor_Total_PIS", "decimal(10,2) default '0.00'");
Criar_Campo("T003_Valor_Total_COFINS", "decimal(10,2) default '0.00'");

Criar_Campo("D006_Flag_Isento_IRPJ", "enum('S','N') DEFAULT 'N'");
Criar_Campo("D006_Flag_Isento_CSLL", "enum('S','N') DEFAULT 'N'");
Criar_Campo("D006_D088_Id_Sem_Aliquota", "int(11) not null");
Criar_Campo("D013_Flag_Ecommerce", "enum('S','N') DEFAULT 'N'");

Criar_Campo("T004_Flag_Isento_IRPJ", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T004_Flag_Isento_CSLL", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T006_Flag_Isento_IRPJ", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T006_Flag_Isento_CSLL", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T008_Flag_Isento_IRPJ", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T008_Flag_Isento_CSLL", "enum('S','N') DEFAULT 'N'");
Criar_Campo("C004_Token_NFC", "VARCHAR(25)");
Criar_Campo("D027_Tipo_Forma_Pagamento_NFCE", "VARCHAR(25)");
Criar_Campo("D027_Codigo_Bandeira_NFCE", "VARCHAR(25)");

Criar_Campo("T075_Data_Licenca_Importacao", "date");

Criar_Campo("T004_Flag_Transferencia", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T004_C004_Id_Transferencia", "int(10)");
Criar_Campo("T006_Flag_Transferencia", "enum('S','N') DEFAULT 'N'");
Criar_Campo("T006_C004_Id_Transferencia", "int(10)");

Criar_Campo("T234_C007_Id_Cliente", "int(10)");
Criar_Campo("T234_Link_Screenshot", "varchar(255)");
Criar_Campo("T234_Flag_Tipo_Suporte", "char(1)");
Criar_Campo("T234_Flag_Tipo_OS", "char(1)");
Criar_Campo("C004_D024_Id_Hardness", "int(10)");
Criar_Campo("T252_Flag_Lido", "char(1) DEFAULT 'N'");
Criar_Campo("T127_Modelo", "varchar(2) DEFAULT '55'");
Criar_Campo("T252_Flag_Feedback", "char(1) DEFAULT 'N'");

Criar_Campo("T182_C004_Id", "int(11) not null");

Criar_Campo("D009_IPT_1","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_IPT_2","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_IPT_3","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009_IPT_4","decimal(10,4) DEFAULT 0.00");
Criar_Campo("D009A_IPT_5","decimal(10,4) DEFAULT 0.00");

Criar_Campo("C004_Data_Inicio_Sistema","TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
Criar_Campo("C004_Banco_Atualizado","CHAR(1) DEFAULT 'S'");
Criar_Campo("C004_Modulos","varchar(50) DEFAULT '0'");
Alterar_Tipo_Campo("C004_Modulos","VARCHAR(50)");
Criar_Campo("C020_Atualizacao_Lei_Transparencia","enum('S','N') DEFAULT 'N'");
Criar_Campo("T219_Observacao_Compra","text");
Criar_Campo("D009_Observacao_Compra","varchar(255)");

Criar_Campo("T075_UF_Desembaraco","varchar(2)");
Criar_Campo("T075_Codigo_Fabricante","varchar(60)");
Criar_Campo("T075_Flag_Transporte_DI","char(10)");
Criar_Campo("T075_Flag_Tipo_Intermedio_DI","varchar(50)");
Criar_Campo("T075_CNPJ_Importador","varchar(50)");
Criar_Campo("T075_UF_Importador","char(10)");
Criar_Campo("T075_Numero_Drawback_Importacao","varchar(50)");
Criar_Campo("T008_Valor_Preco_Unitario", "decimal(19,5) DEFAULT '0.00000'");
Criar_Campo("C025_Flag_Ativo", "char(1) DEFAULT 'S'");
Criar_Campo("D009_Data_Atualizacao_Preco_Tabela", "date DEFAULT '0000-00-00'");
Criar_Campo("T234_Flag_Status_Desenvolvimento", "int(2) DEFAULT '0'");
Criar_Campo("T234_Tempo_Estimado", "time DEFAULT '00:00:00'");
Criar_Campo("T234_Ordem_Desenvolvimento", "int(2) DEFAULT '0'");
Criar_Campo("T234_Analise", "text");
Criar_Campo("T234_Data_Hora_Inicio_Estimado", "DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T234_Data_Hora_Final_Estimado", "DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T234_C007_Id_Programador", "int(10)");
Criar_Campo("T234_Nivel_Suporte", "int(10) DEFAULT '1'");
Criar_Campo("T234_Flag_Triagem", "char(1) DEFAULT 'S'");

Criar_Campo("D024_Cod_Pais_Telefone_1", "int(2)");
Criar_Campo("D024_Cod_Pais_Telefone_2", "int(2)");
Criar_Campo("D024_Cod_Pais_Celular", "int(2)");
Criar_Campo("D024_Cod_Pais_Fax", "int(2)");
Criar_Campo("D024_Flag_Frete", "varchar(1) DEFAULT ''");
Criar_Campo("T001_T257_Id","int(10) unsigned");

Criar_Campo("T055_Cotacao_CC", "decimal(12,4) DEFAULT '0.0000'");
Criar_Campo("T075_Cotacao_CC", "decimal(12,4) DEFAULT '0.0000'");
Criar_Campo("D085_T005_Id","int(10)");
Criar_Campo("D085_T007_Id","int(10)");
Criar_Campo("D085_T139_Id","int(10)");
Criar_Campo("D085_D013_Id","int(10)");
Criar_Campo("D085_D155_Id","int(10)");
Criar_Campo("D085_T259_Id","int(10)");
Criar_Campo("D085_Flag_Tipo","int(2) DEFAULT '1'");
Criar_Campo("T139_Flag_Spam","char(1) DEFAULT 'N'");

Criar_Campo("T007_TX_Flag_Erro","INT(1) DEFAULT 0 NOT NULL");

Criar_Campo("T255_Quantidade","decimal(14,3) DEFAULT '0.000'");
Criar_Campo("T255_Quantidade_Produzida","decimal(14,3) DEFAULT '0.000'");
Criar_Campo("T255_Quantidade_Saldo","decimal(14,3) DEFAULT '0.000'");
Criar_Campo("T255_Total_Itens","int(10) DEFAULT '0'");

Criar_Campo("T259_Flag_Excluido","char(1) DEFAULT 'N'");
Criar_Campo("T259_Data_Exclusao","datetime");
Criar_Campo("T003_T139_Id","int(10) unsigned");
Criar_Campo("T003_D085_Id","int(10) unsigned");
Criar_Campo("T252_Flag_Finalizacao","char(1) DEFAULT 'N'");
Criar_Campo("T234_Prioridade_Cliente", "int(2) DEFAULT '0'");
Criar_Campo("T140_T139_Id", "int(10) unsigned");
Criar_Campo("T140_Id_Anexo", "char(2) DEFAULT ''");
Criar_Campo("T140_Link", "varchar(1000) DEFAULT ''");
Criar_Campo("T256_T006_Id", "int(10) unsigned");
Criar_Campo("T007_TX_Timestamp_Envio_Sefaz", "DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T256_Data_Previsao_Entrega", "DATE DEFAULT '0000-00-00'");
Criar_Campo("T256_Observacao_Producao", "text");
Criar_Campo("T258_D013_Id", "int(10) unsigned");
Criar_Campo("D085_T258_Id", "int(10) unsigned");
Criar_Campo("T255_Data_Hora_Baixa_Materiais", "DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T001_T264_Id", "int(10) unsigned");
Criar_Campo("T255_Observacao", "text");
Criar_Campo("T259_Assunto", "text");
Criar_Campo("T005_Flag_Status_Cotacao", "char(1) DEFAULT ''");
Criar_Campo("T003_Flag_Status_Cotacao", "char(1) DEFAULT ''");

Criar_Campo("T004_Valor_Preco_Transferencia","decimal(14,5) DEFAULT '0'");
Criar_Campo("T006_Valor_Preco_Transferencia","decimal(14,5) DEFAULT '0'");
Criar_Campo("T004_Valor_Custo_Transferencia","decimal(15,5) DEFAULT '0'");
Criar_Campo("T006_Valor_Custo_Transferencia","decimal(15,5) DEFAULT '0'");
Criar_Campo("T004_Flag_Origem_Produto_Transferencia","enum('0','1','2','3','4','5','6','7')");
Criar_Campo("T006_Flag_Origem_Produto_Transferencia","enum('0','1','2','3','4','5','6','7')");
Criar_Campo("T004_Demonstrativo_Preco_Transferencia","text");
Criar_Campo("T006_Demonstrativo_Preco_Transferencia","text");


Criar_Campo("D149_Codigo_CEST","varchar(10)");
Criar_Campo("T008_Codigo_CEST","varchar(10)");
Criar_Campo("D053_Percentual_FCP","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_FCP","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_FCP","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D006_Percentual_Partilha_ICMS_Destino","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("D006_Percentual_Partilha_ICMS_Origem","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_Partilha_ICMS_Destino","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_Partilha_ICMS_Origem","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Partilha_ICMS_Destino","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Partilha_ICMS_Origem","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Percentual_Partilha_ICMS","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Base_Partilha_ICMS","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_FCP","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Partilha_ICMS_Destino","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_Partilha_ICMS_Origem","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D006_Codigo_Enq_Legal_IPI","varchar(3)");
Criar_Campo("T008_Codigo_Enq_Legal_IPI","varchar(3)");
Criar_Campo("D006_Flag_Somar_Fator_ICMS_Dif_Base","enum('S','N') DEFAULT 'N'");

//------------- PARTILHA ICMS ORÇAMENTO E PEDIDO - 09/07/2021 ----------------------
Criar_Campo("T006_Percentual_Partilha_ICMS_Destino","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T006_Percentual_Partilha_ICMS_Origem","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T006_Valor_Partilha_ICMS_Destino","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T006_Valor_Partilha_ICMS_Origem","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T006_Percentual_Partilha_ICMS","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T006_Valor_Base_Partilha_ICMS","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_Partilha_ICMS_Destino","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_Partilha_ICMS_Origem","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Partilha_ICMS_Destino","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Partilha_ICMS_Origem","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T004_Valor_Partilha_ICMS_Destino","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T004_Valor_Partilha_ICMS_Origem","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T004_Percentual_Partilha_ICMS","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T004_Valor_Base_Partilha_ICMS","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T003_Valor_Total_Partilha_ICMS_Destino","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T003_Valor_Total_Partilha_ICMS_Origem","decimal(15,2) DEFAULT '0.00'");
//---------------------------------------------------------------------------------

Criar_Campo("N001_Uniqid_Mensagem","varchar(20)");
Criar_Campo("N001_Hora_Data_Update","datetime");

Criar_Campo("T234_Revisao_SVN","varchar(50)");
Criar_Campo("T013_Flag_Rateio_Frete","char(1)");
Criar_Campo("T013_Flag_Obrigatorio_OC","char(1)");
Criar_Campo("T038_C007_Id_Processado","int(10) unsigned");
Criar_Campo("T038_Data_Hora_Processado","datetime");
Criar_Campo("T038_Flag_Autorizado","char(1) DEFAULT ''");
Criar_Campo("T037_D007_Id","int(10) unsigned");
Criar_Campo("D145_Empresas_Destinatario_Ignorar","varchar(60) DEFAULT ''");
Criar_Campo("T216_Flag_Excluido","char(1) DEFAULT 'N'");
Criar_Campo("C007_Configuracao_Notificacoes","text DEFAULT ''");
Criar_Campo("C007_C007_Id_2","int(10) unsigned");
Criar_Campo("T198_Valor_Preco_Sem_Desconto_Unitario","decimal(19,5) DEFAULT '0.00000'");
Criar_Campo("T198_Quantidade","decimal(12,5) DEFAULT '0.00000'");
Criar_Campo("T198_Quantidade_Conversao", "decimal(12,5) DEFAULT NULL");
Criar_Campo("T266_Quantidade_Conversao", "decimal(12,5) DEFAULT NULL");
Criar_Campo("C004_Observacao_Cotacao","text CHARSET utf8 DEFAULT NULL");
Criar_Campo("T261_T006_Id","int(10) unsigned");
Criar_Campo("T262_T006_Id","int(10) unsigned");
Criar_Campo("T084_Valor_ICMS_ST","decimal(10,2) default '0.00'");
Criar_Campo("D069_Data_Hora_Ultima_Alteracao","DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D069_C007_Id_Ultima_Alteracao","int(10) unsigned");
Criar_Campo("D009_C007_Id_Incluir_Produto","int(10) unsigned");
Criar_Campo("D160_D161_Id","int(10) unsigned");
Criar_Campo("T224_Nome_Status","char(30) NOT NULL DEFAULT 'Pendente'");
Criar_Campo("T006_Flag_Nao_Comprar","char(1) DEFAULT 'N'");
Criar_Campo("C004_Identificacao_CSC","varchar(6) DEFAULT ''");
Criar_Campo("C004_Codigo_CSC","varchar(40) DEFAULT ''");
Criar_Campo("T006_Flag_Gerar_Producao","char(1) DEFAULT 'N'");
Criar_Campo("T259_Data_Hora_Inicio","DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T005_Id_Pedido_Externo","varchar(32) default ''");
Criar_Campo("D024_Id_Matriz","int(10) unsigned");
Criar_Campo("C007_Dispositivo_Acesso","int(10) unsigned DEFAULT '0'");
Criar_Campo("T007_Flag_PDV","varchar(1) DEFAULT 'N'");
Criar_Campo("T234_T267_Id","int(10) unsigned");
Criar_Campo("T234_Flag_Personalizado","varchar(1) DEFAULT 'N'");
Criar_Campo("T245_Tipo_Log","int(10) unsigned");
Criar_Campo("T085_T007_Id","int(10) unsigned");
Criar_Campo("T007_Flag_Gerar_Conhecimento","char(1) DEFAULT 'N'");
Criar_Campo("T234_Teste", "text");
Criar_Campo("C007_Tamanho_Fonte_Sistema", "int(10) DEFAULT '13'");
Criar_Campo("D013_C007_Id_Cliente","int(10) unsigned");
Criar_Campo("T234_D013_Id","int(10) unsigned");
Criar_Campo("T234_T234_Id","int(10) unsigned");
Criar_Campo("C004_Codigo_Agencia_Correios","varchar(10) DEFAULT ''");
Criar_Campo("C004_Login_Portal_Postal","varchar(50) DEFAULT ''");
Criar_Campo("C004_Senha_Portal_Postal","varchar(50) DEFAULT ''");
Criar_Campo("D001_Id_Ecommerce","varchar(30) DEFAULT ''");
Criar_Campo("D006_Somar_FCP_ICMS_Interno","enum('S','N') DEFAULT 'N'");
Criar_Campo("D001_Flag_Ecommerce","enum('S','N') DEFAULT 'N'");
Criar_Campo("D013_Senha","varchar(50) not null");
Criar_Campo("D024_CNAE","char(30) NOT NULL DEFAULT ''");
Criar_Campo("D024_Percentual_Carga_Media","decimal(14,3) DEFAULT '0.000'");
Criar_Campo("T085_Flag_Tipo_Nota","varchar(15) DEFAULT ''");
Criar_Campo("T219_Percentual_ST","decimal(10,4) DEFAULT 0");
Criar_Campo("T225_Percentual_ST","decimal(10,4) DEFAULT 0");
Criar_Campo("D069_Data_Hora_Ultima_Alteracao_Sem_Calcular","DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D165_Classificacao_Fiscal","varchar(12) DEFAULT NULL");
Criar_Campo("D165_UF","char(2) DEFAULT ''");
Alterar_Tipo_Campo("D069_Descricao_Produto","VARCHAR(80)");
Criar_Campo("D024_Valor_Limite_Manual","decimal(14,3) DEFAULT '0.000'");



// Campos para NF-e 4.0
Criar_Campo("D149_Indicador_Escala","varchar(1)");
Criar_Campo("T008_Indicador_Escala","varchar(1)");
Criar_Campo("T007_Flag_Indicador_Presenca","varchar(1) DEFAULT '' NOT NULL");

Criar_Campo("D006_Codigo_Beneficio_Fiscal","varchar(15)");
Criar_Campo("T008_Codigo_Beneficio_Fiscal","varchar(10)");
Criar_Campo("T008_Valor_Base_FCP","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_FCP_Partilha","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Base_FCP_Ret","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_FCP_Retido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Base_FCP_ST","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_FCP_ST","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_FCP_Partilha","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Valor_Base_FCP_Dest","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_FCP_ST","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_FCP_ST_Ret","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T008_Aliquota_ST_FCP","decimal(10,2) DEFAULT '0.00'");

Criar_Campo("D149_Flag_Isento_PIS", "ENUM('S','N', '') DEFAULT ''");
Criar_Campo("D149_Situacao_Tributaria_PIS", "VARCHAR(2) DEFAULT ''");
Criar_Campo("D149_Flag_Isento_COFINS", "ENUM('S','N', '') DEFAULT ''");
Criar_Campo("D149_Situacao_Tributaria_COFINS", "VARCHAR(2) DEFAULT ''");

//Ricardo - Criar_Campo() - Begin
Criar_Campo("T007_Data_Entrega", "date default '0000-00-00'");

Criar_Campo("C004_D172_Id", "int(10) unsigned");
Criar_Campo("C004_D170_Id", "int(10) unsigned");
Criar_Campo("C004_D173_Id", "int(10) unsigned");
Criar_Campo("C004_Ambiente_MDFe", "enum('1','2') default '1'");
Criar_Campo("C004_Flag_Emitente", "int(3) unsigned");
Criar_Campo("C004_Flag_Transportador", "int(3) unsigned");
Criar_Campo("C004_Versao_MDFe", "decimal(3,2) default '3.00'");
Criar_Campo("C004_Tipo_Carga_Padrao", "int(1) unsigned");
Criar_Campo("C004_Tipo_Transporte_Padrao", "int(1) unsigned");
Criar_Campo("D007_Posto_Cooperativa", "varchar(10)");

Criar_Campo("T276_Numero_Protocolo_Cancelamento_MDFe", "varchar(20)");
Criar_Campo("T276_Observacao_Cancelamento", "text");
Criar_Campo("T276_D018_Id_Carregamento", "int(10) unsigned");
Criar_Campo("T276_D020_Id_Carregamento", "int(10) unsigned");

Criar_Campo("T284_Flag_Tipo_Documento", "int(1) unsigned default'2'");
Criar_Campo("T284_Destinatario_Nome", "varchar(100)");

Criar_Campo("T014_Valor_Base_FCP", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Percentual_FCP", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_FCP", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_Base_FCP_ST", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Percentual_FCP_ST", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_FCP_ST", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_Base_FCP_Ret", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Percentual_FCP_Ret", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_FCP_Ret", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_Base_FCP_Dest", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T014_Percentual_FCP_Dest", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T014_Valor_FCP_Dest", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T013_Valor_Total_FCP", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T013_Valor_Total_FCP_ST", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T013_Valor_Total_FCP_Ret", "decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T013_Valor_Total_FCP_Dest", "decimal(15,2) DEFAULT '0.00'");

// FK - APP Cadastro Segmento Cliente
Criar_Campo("D024A_CDS001_Id", "int(10) unsigned");

//Ricardo - Criar_Campo() - End

// Índices tabela email (T139)
Criar_Indice('T139_Pasta');
Criar_Indice('T139_Uid');
Criar_Indice('T139_D024_Id');
Criar_Indice('T139_T003_Id');
Criar_Indice('T139_T005_Id');
Criar_Indice('T139_T010_Id');
Criar_Indice('T139_C007_Id');
Criar_Indice('T139_De');
Criar_Indice('T139_Para');
Criar_Indice('T139_Data_Hora');
Criar_Indice('T139_T224_Id');
Criar_Indice('T139_T218_Id');

Criar_Indice('T004_D009_Id_Loja');
Criar_Indice('T006_D009_Id_Comprar');
Criar_Indice('T006_D009_Id_Kit');
Criar_Indice('T174_T059_Id');
Criar_Indice('T059_T189_Id');
Criar_Indice('T012_T189_Id');
Criar_Indice('T012_T059_Id');

Criar_Indice('T218_D024_Id');
Criar_Indice('T218_D013_Id');
Criar_Indice('T218_C007_Id_Vendedor_Interno');
Criar_Indice('T218_Liberou_Alcada_C007_Id');
Criar_Indice('T218_D006_Id');
Criar_Indice('T218_C004_Id');
Criar_Indice('T218_Data_Emissao');
Criar_Indice('T218_D116_Id');

Criar_Indice('D069_Flag_Tabela_Principal');

Criar_Indice('T162_DataHora');
Criar_Indice('T162_Tabela');

Criar_Indice('T219_T218_Id');
Criar_Indice('T219_C004_Id');
Criar_Indice('T219_D009_Id');
Criar_Indice('T219_D006_Id');
Criar_Indice('T219_D037_Id');
Criar_Indice('T219_D005_Id');

Criar_Indice('T047_Flag_Status');

Criar_Indice('T224_D024_Id');
Criar_Indice('T224_C007_Id_Vendedor_Interno');
Criar_Indice('T224_C004_Id');
Criar_Indice('T224_D006_Id');
Criar_Indice('T224_D116_Id');
Criar_Indice('T224_D021_Id');
Criar_Indice('T224_T224_Id_Agrupado');
Criar_Indice('T224_Data_Emissao');
Criar_Indice('T224_T218_Id');
Criar_Indice('T224_Flag_Status');

Criar_Indice('T014_T225_Id');
Criar_Indice('D009_D069_Id');
Criar_Indice('D049_Flag_Curva_ABC');
Criar_Indice('D049_Flag_Curva_ABC_Qtd');

Criar_Indice('D024_Flag_Empresas_Grupo');
Criar_Indice('D024_Flag_Curva_ABC_Qtd');

Criar_Indice('D069_Codigo_Barras');

mysql_query("CREATE UNIQUE INDEX IDX_T007_Numero_Nota_Fiscal_Composto ON T007 (T007_Numero_Nota_Fiscal,T007_C004_Id,T007_Flag_ACP,T007_Serie)");
mysql_query("CREATE UNIQUE INDEX IDX_T117_Nosso_Numero_Composto ON T117 (T117_D007_Id,T117_Nosso_Numero)");

Criar_Indice('T225_T224_Id');
Criar_Indice('T225_C004_Id');
Criar_Indice('T225_D024_Id');
Criar_Indice('T225_D037_Id');
Criar_Indice('T225_D009_Id');
Criar_Indice('T225_D005_Id');
Criar_Indice('T225_D006_Id');
Criar_Indice('T225_T219_Id');
Criar_Indice('T225_Flag_Cancelada');
Criar_Indice('T224_Flag_Cancelada');
Criar_Indice('D085_T003_Id');
Criar_Indice('D085_T005_Id');
Criar_Indice('D085_T007_Id');
Criar_Indice('T004_T219_Id');
Criar_Indice('T006_T219_Id');
Criar_Indice('T219_T004_Id');
Criar_Indice('T219_T006_Id');
Criar_Indice('T004_T001_Id');
Criar_Indice('T002_T117_Id');
Criar_Indice('T241_D001_Id');
Criar_Indice('T241_D024_Id');
Criar_Indice('C031_C004_Id');
Criar_Indice('C031_Campo');
Criar_Indice('T178_T005_Id');
Criar_Indice('T178_Descricao');
Criar_Indice('T090_T005_Id');
Criar_Indice('T139_T075_Id');
Criar_Indice('T139_Flag_Excluir');
Criar_Indice('T003_D044_Id');
Criar_Indice('T005_D044_Id');
Criar_Indice('T224A_D044_Id');
Criar_Indice('D085_T002_Id');
Criar_Indice('D009_Frequencia_Venda');
Criar_Indice('D006_D088_Id');
Criar_Indice('D009_C007_Id_Comprador');
Criar_Indice('D085_T259_Id');
Criar_Indice('T260_T259_Id');
Criar_Indice('D085_T139_Id');
Criar_Indice('T259_C007_Id');
Criar_Indice('T140_T139_Id');
Criar_Indice('T256_T006_Id');
Criar_Indice('T259_D024_Id');
Criar_Indice('T005_T224_Id');
Criar_Indice('T224_Flag_Tipo');
Criar_Indice('T004_Flag_Comprar');
Criar_Indice('D001_Flag_Ativo');
Criar_Indice('T005_Id_Pedido_Externo');
Criar_Indice('D024_Id_Matriz');
Criar_Indice('T219_Flag_Comprar');
Criar_Indice('T007_Flag_PDV');
Criar_Indice('T234_D024_Id');
Criar_Indice('T234_C007_Id');
Criar_Indice('T234_T235_Id');
Criar_Indice('T234_T236_Id');
Criar_Indice('T234_C007_Id_Programador');
Criar_Indice('T234_T267_Id');
Criar_Indice('T234_D013_Id');
Criar_Indice('T234_T234_Id');
Criar_Indice('T234_T272_Id');
Criar_Indice('T234_D166_Id');
Criar_Indice('T234_D167_Id');
Criar_Indice('T252_C007_Id_Cliente');
Criar_Indice('T252_Data_Hora');
Criar_Indice('D074_D001_Id_Composicao');

//---- MARCELO 
Criar_Indice('T234_Data_Hora_Abertura');
Criar_Indice('T234_Data_Hora_Finalizado');
//Criar_Campo('D009_Valor_Unitario_Base_ST', 'decimal(10,2) default 0');
Alterar_Tipo_Campo("D005_Flag_Aplicacao","varchar(2) DEFAULT '0'");
Criar_Indice('D009_Data_Reprocessa_Historico');
Criar_Campo("D009_Quantidade_Compra_Emergencia","decimal(14,5) default '0.00000'");
Criar_Indice('T007_Protocolo_Inutilizacao');
Criar_Indice('T007_Numero_Protocolo_Nfe');
Criar_Campo("C007_T007_Id_PDV","int(10) DEFAULT 0 NOT NULL");
Alterar_Tipo_Campo("T004_Codigo_Local","varchar(60) NOT NULL DEFAULT ''");
Alterar_Tipo_Campo("T006_Codigo_Local","varchar(60) NOT NULL DEFAULT ''");
Criar_Campo("T182_T007_Id","int(10) DEFAULT 0 NOT NULL");
Criar_Indice('T182_T007_Id');
Criar_Campo("C004_Logo_Etiqueta","text NOT NULL");
Criar_Campo("D022_Logo_Etiqueta","text NOT NULL");
Criar_Campo("C007_Id_Melhoria_Notificada","int(5) NOT NULL default 0");
Alterar_Tipo_Campo("C004_Menu_Extra_Html","text");
Criar_Campo("D001_Flag_Etiqueta_Pedido","varchar(1) NOT NULL default 'N'");
Criar_Campo("C033_Nome","varchar(100) NOT NULL");
Criar_Campo("C033_Token","varchar(100) NOT NULL");
Criar_Indice("T007_Flag_Entrada_Saida");
Criar_Indice("C007_C007_Id_2");
Criar_Indice("T218_C007_Id_Vendedor_Externo");
Criar_Campo("D132_Grid_Width","int(5) default 0");
Criar_Campo("D176_Ordem","int(5) default 0");
Criar_Campo("D132_Grupo","int(10)");
Alterar_Tipo_Campo("D132_Grupo","varchar(30) NOT NULL");
Criar_Indice("T018_Flag_Confirmado");
mysql_query("update D006 set D006_Flag_Venda_Compra_Outros='OE' where D006_Flag_Venda_Compra_Outros='O' and D006_Flag_Entrada_Saida='E'");
mysql_query("update D006 set D006_Flag_Venda_Compra_Outros='OS' where D006_Flag_Venda_Compra_Outros='O' and D006_Flag_Entrada_Saida='S'");
Criar_Campo("C025_Dominio", "text");
Criar_Campo("C025_Flag_Transferido", "int(1)");
Criar_Campo("C025_Flag_Removido", "int(1)");
Criar_Campo("C025_Servidor", "text");
Criar_Indice("T172_D001_Id");
Criar_Campo("T234_Prioridade", "varchar(10) not null");
Criar_Campo("T234_Programador", "varchar(20) not null");
Criar_Campo("T234_Retorno_Horas", "int(3) default 0");
Criar_Campo("T234_Status", "varchar(20) not null");
Criar_Campo("T234_Hora_Finalizado_Programador", "datetime");
Criar_Campo("T234_Logo_Empresa", "text");
Alter_Query("ALTER TABLE T219A ADD INDEX T219A_T219_Id_IX (T219A_T219_Id)");
Alter_Query("ALTER TABLE T225A ADD INDEX T225A_T225_Id_IX (T225A_T225_Id)");
Alterar_Tipo_Campo("D031_Portador","varchar(35) NOT NULL");
Criar_Indice('T004_Flag_Cancelado');
Criar_Indice('T001_T219_Id');
//Criar_Indice('T004_D037_Id');
Criar_Indice('C004_D018_Id');
Criar_Indice('T014_Flag_Divergencia');
Criar_Indice('T225_Flag_Autorizado');
Criar_Campo("T294_Quantidade_Container", "decimal(10,3) not null");
Criar_Campo("T296_Quantidade_Container", "decimal(10,3) not null");
Criar_Campo("T055_Valor_Total_Siscomex", "decimal(19,6) not null");
Criar_Campo("T055_Valor_Siscomex", "decimal(19,6) not null");
Criar_Campo("T055_Valor_Total_THC", "decimal(19,6) not null");
Criar_Campo("T055_Valor_THC", "decimal(19,6) not null");
Criar_Campo("T055_Valor_Total_Nota_Fiscal", "decimal(19,6) not null");
Alterar_Tipo_Campo("T007_Flag_Cancelada","VARCHAR(1) DEFAULT 'N'");
Criar_Campo("D191_Tipo","varchar(3) default '0'");
Criar_Campo("T168_Tipo","varchar(3) default '0'");
Criar_Campo("T055_D024_Id","int(11) not null");
Criar_Campo("T075_D118_Id_Origem","int(11) not null");
Criar_Campo("T168_Flag_Base_Rateio","varchar(3) default '1'");
Criar_Campo("D192_Flag_Base_Rateio","varchar(3) default '1'");
//---- FIM MARCELO

//---- TIAGO
Criar_Campo("C007_Info_Dialog","text");
Criar_Campo("D182_DivId","varchar(50)");
Criar_Campo("D132_Tipo","int(2) default 1");
Criar_Campo("D183_Uuid","varchar(255)");
Criar_Indice('D183_Uuid');
Criar_Indice('C039_Uuid');
Criar_Campo("D183_App_Id","varchar(255)");
Criar_Campo("C039_App_Id","varchar(255)");
Criar_Indice('D183_App_Id');
Criar_Indice('C039_App_Id');
Criar_Campo("D183_Desenvolvimento","int(1)");
//---- FIM TIAGO

//---- FELIPE
Criar_Campo("T008_Valor_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T007_Valor_Total_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D006_Flag_Calcula_FCP_ST","varchar(1) DEFAULT ''");
Criar_Campo("D006_Flag_Calcula_FCP_Retido","varchar(1) DEFAULT ''");
Criar_Campo("T006_Valor_Base_ICMS_Sem_Reducao","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T006_Valor_Base_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T006_Valor_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_Base_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T005_Valor_Total_Base_ICMS_Sem_Reducao","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("D027_Cnpj_Instituicao_Pagamento","varchar(20)");
Criar_Campo("D027_Cnpj_Intermediador_Transacao","varchar(20)");
Criar_Campo("D027_Id_Usuario_Intermediador_Transacao","varchar(60)");
Criar_Campo("T007_Flag_Intermediador_Transacao","varchar(2)");
Alterar_Tipo_Campo("D027_Descricao_Ecommerce","text");
Criar_Campo("D006_Flag_Deduz_ICMS_Base_PIS","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Flag_Deduz_ICMS_Base_COFINS","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Flag_Deduz_FCP_Base_COFINS","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Flag_Deduz_FCP_Base_PIS","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS","varchar(1) DEFAULT 'N'");
Alterar_Tipo_Campo("C033_Token","text not null");
Criar_Campo("C032_Descricao","text");
Criar_Campo("D006_Deduz_ICMS_Desonerado_Valor_Total","varchar(1) DEFAULT 'S'");
Criar_Campo("T214_Data_Finalizacao","DATETIME DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("C007_Alcada_OC_Empresa_Grupo","varchar(1) NOT NULL DEFAULT 'G'");
Criar_Indice('T008A_T008_Id');
Criar_Indice('T225A_T225_Id');
Criar_Indice('T219A_T219_Id');
Criar_Campo("D053_Calculo_ST_Por_Dentro_Compra","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Reducao_Aliquota_IPI","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T294_T004_Id","int(10) unsigned");
Criar_Campo("T293_Grupo","int(10)");
Criar_Campo("T294_T006_Id","int(10) unsigned");
Criar_Campo("T295_Grupo","int(10)");
Criar_Campo("T293_Shipping_Term","varchar(50) DEFAULT ''");
Criar_Campo("T293_Shipping_Condition","varchar(50) DEFAULT ''");
Criar_Campo("T293_Payment_Term","varchar(50) DEFAULT ''");
Criar_Campo("T293_Transfer_Fe","decimal(14,4) DEFAULT '0.00'"); 
Criar_Campo("T293_Important_Remark","text");
Criar_Campo("C004_Observao_Cotation","text");
Criar_Campo("T295_Shipping_Term","varchar(50) DEFAULT ''");
Criar_Campo("T295_Shipping_Condition","varchar(50) DEFAULT ''");
Criar_Campo("T295_Payment_Term","varchar(50) DEFAULT ''");
Criar_Campo("T295_Transfer_Fe","decimal(14,4) DEFAULT '0.00'"); 
Criar_Campo("T295_Important_Remark","text");
Criar_Campo("T295_Ship_To","text");
Criar_Campo("C004_Observao_Purchase_Order","text");
Criar_Campo("T295_Flag_Status","int DEFAULT '0'");
Criar_Campo("T295_Nome_Status","varchar(30) DEFAULT 'Pendente'");
Criar_Campo("T295_Flag_Alcada","varchar(1) DEFAULT ''");
Criar_Campo("T295_Liberou_Alcada_C007_Id","int DEFAULT '0'");
Criar_Campo("T295_Liberou_Alcada_Data","datetime");
Criar_Campo("T295_Flag_Status_Liberacao","varchar(1) DEFAULT ''");
Criar_Campo("T295_Motivo_Pedido_Liberacao","text");
Criar_Campo("T295_Liberou_Alcada_Observacao_Nao_Liberado","text");
Criar_Campo("C007_Valor_Maximo_PO","decimal(14,5) DEFAULT '0'");
Criar_Campo("C007_Valor_Maximo_PO_Dia","decimal(14,5) DEFAULT '0'");
Criar_Campo("C007_Valor_Maximo_PO_Mes","decimal(14,5) DEFAULT '0'");
Criar_Campo("C007_Valor_Maximo_PO_Fornecedor","decimal(14,5) DEFAULT '0'");
Criar_Campo("T295_Valor_Total","decimal(19,2) DEFAULT '0'");
Criar_Campo("T295_Liberou_Alcada_Valor_Total","decimal(19,2) DEFAULT '0'");
Criar_Campo("D053_Aliquota_ICMS_Importacao","decimal(5,2) DEFAULT '0'");
Criar_Campo("D053_Aliquota_ICMS_Presumido_Importacao","decimal(5,2) DEFAULT '0'");
Criar_Campo("D053_Percentual_Reducao_ICMS_Importacao","decimal(5,2) DEFAULT '0'");
Criar_Campo("D053_Percentual_Reducao_ICMS_Presumido_Importacao","decimal(5,2) DEFAULT '0'");
Criar_Campo("D053_Aliquota_ICMS_PIS_COFINS_Importacao","decimal(5,2) DEFAULT '0'");
Criar_Campo("D053_Aliquota_ICMS_Credito_Presumido_Importacao","decimal(5,2) DEFAULT '0'");
Alter_Query("ALTER TABLE T168 DROP COLUMN T168_T293_Id;");
Alter_Query("ALTER TABLE T168 DROP COLUMN T168_T295_Id;");
Criar_Campo("T293_D190_Id","int(10) unsigned");
Criar_Indice("T293_D190_Id");
Criar_Campo("T295_D190_Id","int(10) unsigned");
Criar_Indice("T295_D190_Id");
Criar_Campo("D192_Ordem","int(10) unsigned");
Criar_Campo("T299_Ordem","int(10) unsigned");
Criar_Campo("T299_Cotacao_ME","decimal(12,4) default '0'");
Criar_Campo("D192_Flag_Soma_Base_IPI","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Soma_Base_ICMS","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Soma_Base_PIS ","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Soma_Base_COFINS","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Soma_Base_II","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Soma_Custo","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Soma_Preco_NFe","varchar(1) default 'N'");
Criar_Campo("D192_Flag_Tipo_Rateio","varchar(1) default 'C'");
Criar_Indice("D192_Flag_Soma_Base_IPI");
Criar_Indice("D192_Flag_Soma_Base_ICMS");
Criar_Indice("D192_Flag_Soma_Base_PIS");
Criar_Indice("D192_Flag_Soma_Base_COFINS");
Criar_Indice("D192_Flag_Soma_Base_II");
Criar_Indice("D192_Flag_Soma_Custo");
Criar_Indice("D192_Flag_Soma_Preco_NFe");
Criar_Indice("D192_Flag_Tipo_Rateio");
Criar_Campo("T299_Flag_Soma_Base_IPI","varchar(1) default 'N'");
Criar_Campo("T299_Flag_Soma_Base_ICMS","varchar(1) default 'N'");
Criar_Campo("T299_Flag_Soma_Base_PIS ","varchar(1) default 'N'");
Criar_Campo("T299_Flag_Soma_Base_COFINS","varchar(1) default 'N'");
Criar_Campo("T299_Flag_Soma_Base_II","varchar(1) default 'N'");
Criar_Campo("T299_Flag_Soma_Custo","varchar(1) default'N'");
Criar_Campo("T299_Flag_Soma_Preco_NFe","varchar(1) default 'N'");
Criar_Campo("T299_Flag_Tipo_Rateio","varchar(1) default 'C'");
Criar_Indice("T299_Flag_Soma_Base_IPI");
Criar_Indice("T299_Flag_Soma_Base_ICMS");
Criar_Indice("T299_Flag_Soma_Base_PIS");
Criar_Indice("T299_Flag_Soma_Base_COFINS");
Criar_Indice("T299_Flag_Soma_Base_II");
Criar_Indice("T299_Flag_Soma_Custo");
Criar_Indice("T299_Flag_Soma_Preco_NFe");
Criar_Indice("T299_Flag_Tipo_Rateio");
Criar_Campo("D192_Flag_ME","varchar(1) default 'N'");
Criar_Campo("T299_Flag_ME","varchar(1) default 'N'");
Criar_Campo("T300_Flag_ME","varchar(1) default 'N'");
Criar_Campo("T300_Cotacao_ME","decimal(12,4) default '0'");
Criar_Campo("T300_Ordem","int(10) unsigned");
Criar_Campo("T300_Flag_Soma_Base_IPI","varchar(1) default 'N'");
Criar_Campo("T300_Flag_Soma_Base_ICMS","varchar(1) default 'N'");
Criar_Campo("T300_Flag_Soma_Base_PIS ","varchar(1) default 'N'");
Criar_Campo("T300_Flag_Soma_Base_COFINS","varchar(1) default 'N'");
Criar_Campo("T300_Flag_Soma_Base_II","varchar(1) default 'N'");
Criar_Campo("T300_Flag_Soma_Custo","varchar(1) default'N'");
Criar_Campo("T300_Flag_Soma_Preco_NFe","varchar(1) default 'N'");
Criar_Campo("T300_Flag_Tipo_Rateio","varchar(1) default 'C'");
Criar_Indice("T300_Flag_Soma_Base_IPI");
Criar_Indice("T300_Flag_Soma_Base_ICMS");
Criar_Indice("T300_Flag_Soma_Base_PIS");
Criar_Indice("T300_Flag_Soma_Base_COFINS");
Criar_Indice("T300_Flag_Soma_Base_II");
Criar_Indice("T300_Flag_Soma_Custo");
Criar_Indice("T300_Flag_Soma_Preco_NFe");
Criar_Indice("T300_Flag_Tipo_Rateio");
Criar_Campo("T294_Flag_Excluir","varchar(1) default 'N'");
Criar_Campo("T296_Flag_Excluir","varchar(1) default 'N'");
Criar_Campo("T293_Flag_Status","int DEFAULT '0'");
Criar_Indice("T293_Flag_Status");
Criar_Indice("T295_Flag_Status");
Criar_Campo("T293_Nome_Status","varchar(30) DEFAULT 'Pendente'");
Criar_Campo("T055_Flag_Excluir","varchar(1) default 'N'");
Criar_Campo("T295_C007_Id_Conferiu","int default '0'");
Criar_Campo("T296_C007_Id_Conferiu","int default '0'");
Criar_Campo("T055_Flag_Produto_Divergencia","varchar(1) default 'N'");
Criar_Campo("T296_Flag_Produto_Divergencia","varchar(1) default 'N'");
Criar_Campo("D006_C004_Id","int(10) DEFAULT '0'");
Criar_Indice("D006_C004_Id");
Criar_Campo("T075_Arquivo_XML","text");
Criar_Campo("T055_Descricao_Produto_Ingles","varchar(120) default ''");
Criar_Campo("T296_Valor_Custo_Unitario_ME_Origem ","decimal(20,7) default '0.00'");
Criar_Campo("T296_Valor_Custo_Total_ME_Origem ","decimal(20,7) default '0.00'");
Criar_Campo("T219A_Aliquota_FCP","decimal(10,2) default '0.00'");
Criar_Campo("T219A_Valor_Base_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T219A_Valor_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T219A_Data_Entrega","date");
Criar_Campo("T225A_Aliquota_FCP","decimal(10,2) default '0.00'");
Criar_Campo("T225A_Valor_Base_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T225A_Valor_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T225A_Valor_Base_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T225A_Valor_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T225A_Data_Entrega","date");
Criar_Campo("T219A_Valor_Base_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T219A_Valor_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T218A_Hora_Emissao","time DEFAULT '00:00:00'");
Criar_Campo("T218A_Data_Hora_Ultima_Resposta_Venda","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T219A_Data_Hora_Responta_Venda","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T066A_Data_Validade","date default '0000-00-00'");
Criar_Indice("T188_T003_Id");
Criar_Indice("T145_T003_Id");
Criar_Indice("T145_T004_Id");
Criar_Indice("T202_T004_Id");
Criar_Campo("T055A_Observacao_Divergencia","varchar(200) default ''");
Criar_Campo("T296_Observacao_Divergencia","varchar(200) default ''");
Criar_Campo("T103_Valor_Desconto","decimal(19,2) default '0.00'");
Criar_Campo("T103_Valor_Juros","decimal(19,2) default '0.00'");
Criar_Campo("T103_Valor_Taxas","decimal(19,2) default '0.00'");
Criar_Campo("T103_Valor_Total_Recebido","decimal(19,2) default '0.00'");
Criar_Campo("D009A_Quantidade_Meses_Estoque","decimal(10,2) default '0.00'");
Criar_Campo("D009A_Media_Venda_Antes_Ultima_Compra","decimal(10,3) default '0.00'");
Criar_Campo("D009A_Media_Venda_Apos_Ultima_Compra","decimal(10,3) default '0.00'");
Criar_Campo("T014A_Valor_Custo_Final_Liquido_Unitario","decimal(20,10) default '0.00'");
Criar_Campo("D009A_Valor_Custo_Liquido_Ultima_Compra","decimal(20,10) default '0.00'");
Criar_Campo("D009A_Valor_Custo_Unitario_Liquido","decimal(20,10) default '0.00'");
Criar_Campo("D006_Flag_Somar_Despesas_Base_PIS","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_Despesas_Base_COFINS","enum('S','N') default 'N'");
Criar_Campo("D024_Dias_Envio_Email_Orc_Apos_Emissao","int default '0'");
Criar_Campo("D024_Dias_Envio_Email_Orc_Antes_Vencer","int default '0'");
Alterar_Tipo_Campo("D137_Empresas_Destinatario","varchar(500) DEFAULT ''");
Criar_Campo("T168_D190_Id","int(10) unsigned"); 
Criar_Campo("T168_Valor_Confirmado","decimal(14,4) default 0.00");
Criar_Campo("T168_Aliquota","decimal(5,2) default 0.00");
Criar_Campo("T168_Flag_Tipo","varchar(1)"); 
Criar_Campo("T168_Formula","text"); 
Criar_Campo("T168_Cotacao_ME","decimal(12,4) default 0.00"); 
Criar_Campo("T168_Ordem","int(10) unsigned"); 
Criar_Campo("T168_Flag_Soma_Base_IPI","varchar(1) default 'N'"); 
Criar_Campo("T168_Flag_Soma_Base_ICMS","varchar(1) default 'N'");
Criar_Campo("T168_Flag_Soma_Base_PIS","varchar(1) default 'N'");
Criar_Campo("T168_Flag_Soma_Base_COFINS","varchar(1) default 'N'"); 
Criar_Campo("T168_Flag_Soma_Base_II","varchar(1) default 'N'"); 
Criar_Campo("T168_Flag_Soma_Custo","varchar(1) default 'N'"); 
Criar_Campo("T168_Flag_Soma_Preco_NFe","varchar(1) default 'N'"); 
Criar_Campo("T168_Flag_Tipo_Rateio","varchar(1) default 'C'"); 
Criar_Campo("T168_Tipo_Despesa","varchar(3) default '1'");
Criar_Campo("T075_Modelo_Calculos","varchar(1) default '1'");
Criar_Campo("D191_Tipo_Despesa","varchar(3) default '1'");
Criar_Campo("T008A_Numero_Lote","varchar(20) default ''");
Criar_Campo("T008A_Quantidade_Produto_Lote","decimal(8,3) default '0.00'");
Criar_Campo("T008A_Data_Fabricacao_Lote","date");
Criar_Campo("T008A_Data_Validade_Lote","date");
Criar_Campo("T008A_Codigo_Agregacao","varchar(20) default ''");
Criar_Campo("T008A_Codigo_Produto_ANVISA","varchar(20) default ''"); 
Criar_Campo("T008A_Valor_Preco_Maximo_Consumidor","decimal(13,2) default '0.00'"); 
Criar_Campo("T008A_Motivo_Isencao_ANVISA","varchar(255) default ''"); 
Criar_Campo("T007_Flag_Primeira_Ultima_Compra_Cliente","varchar(1) default 'N'"); 
Criar_Campo("D006_Impressao_Dados_Produtos_DANFE","text"); 
Criar_Campo("T182_D082_Id_Marca_Recebida","int(10) unsigned");
Criar_Campo("T003A_Flag_Ratear_Frete_Custo","varchar(1) NOT NULL DEFAULT ''");
Criar_Campo("T005A_Flag_Ratear_Frete_Custo","varchar(1) NOT NULL DEFAULT ''");
Criar_Campo("T003A_Flag_Frete_Requer_Alcada","varchar(1) DEFAULT 'N'");
Criar_Campo("T005A_Flag_Frete_Requer_Alcada","varchar(1) DEFAULT 'N'");
Criar_Campo("T003A_Liberou_Alcada_Frete","decimal(19,2) DEFAULT '0.00'");
Criar_Campo("T005A_Liberou_Alcada_Frete","decimal(19,2) DEFAULT '0.00'");
Alterar_Tipo_Campo("D137_D024_Flag_Contribuinte","varchar(100) DEFAULT ''");
Alterar_Tipo_Campo("D137_C004_Codigo_Regime_Tributario","varchar(50) DEFAULT ''");
Alterar_Tipo_Campo("D137_D024_Codigo_Regime_Tributario","varchar(50) DEFAULT ''");
Alterar_Tipo_Campo("D137_Flag_Destino_Produto","varchar(100) DEFAULT ''");
Alterar_Tipo_Campo("D137_Produzido_Adquirido","varchar(20) DEFAULT ''");
Criar_Campo("D137_Flag_Valida_Regra_Endereco_Entrega", "varchar(1) DEFAULT '' COMMENT 'Define qual endereço utilizar para validar se a operação é dentro ou fora do estado (D137_Dentro_Fora_Estado): P=Padrão, E=Entrega'");
//Alterar_Tipo_Campo("D137_Produzido_Adquirido","varchar(20) DEFAULT ''");
Criar_Campo("D137_Prioridade_Regra","int(10) default '0'");
Criar_Campo("D006_Percentual_Reducao_PIS","decimal(5,2) default '0.00'");
Criar_Campo("D006_Percentual_Reducao_COFINS","decimal(5,2) default '0.00'");
Criar_Campo("T004A_Percentual_Reducao_PIS","decimal(5,2) default '0.00'");
Criar_Campo("T004A_Percentual_Reducao_COFINS","decimal(5,2) default '0.00'");
Criar_Campo("T008A_Percentual_Reducao_PIS","decimal(5,2) default '0.00'");
Criar_Campo("T008A_Percentual_Reducao_COFINS","decimal(5,2) default '0.00'");
Criar_Campo("D137_Flag_Tipo_IPI","varchar(1) default ''");
Criar_Campo("D149_Flag_Parana_Competitivo","varchar(1) default ''");
Criar_Campo("T298_D005_Id", "int(10) unsigned");
Criar_Campo("T298_C007_Id_Responsavel", "int(10) unsigned");
Criar_Campo("T298_Data_Hora_Origem", "datetime");
Criar_Campo("T298_Origem_Ajuste", "varchar(40) default ''");
Criar_Campo("T298_Valor_Original_ICMS", "decimal(15,2)");
Criar_Campo("T298_Percentual_Ajuste", "decimal(5,2)");
Criar_Indice("T298_D005_Id");
Criar_Indice("T298_Origem_Ajuste");
Criar_Campo("T055A_Descricao_Produto_Fornecedor","varchar(200) default ''");
Criar_Campo("T055A_Flag_Item_Importado_XML","varchar(1) default 'N'");
Criar_Campo("D070_Flag_Vincula_Item_Pre_Cadastro","varchar(1) default 'S'");
Criar_Indice('T007_Chave_Acesso_Nfe');
Criar_Campo("T004A_Trib_Interna_UF_Dest_Reducao_Base","decimal(20,2) default '0.00' COMMENT 'Reducao utilizada para o calculo do DIFAL'");
Criar_Campo("T006A_Trib_Interna_UF_Dest_Reducao_Base","decimal(20,2) default '0.00' COMMENT 'Reducao utilizada para o calculo do DIFAL'");
Criar_Campo("T008A_Trib_Interna_UF_Dest_Reducao_Base","decimal(20,2) default '0.00' COMMENT 'Reducao utilizada para o calculo do DIFAL'");
Criar_Campo("D007_Layout_Cobranca","varchar(2) default '1'");
Criar_Campo("T003A_Flag_Conversao_Unidade","varchar(1) default 'N'");
Criar_Campo("T004A_Quantidade_Comercial","decimal(12,5) default '0.00000'");
Criar_Campo("T004A_Preco_Unitario_Comercial","decimal(20,10) default '0.00000'");
Criar_Campo("T004A_D184_Id","int(10) unsigned DEFAULT '0'");
Criar_Campo("T004A_Quantidade_Comercial_Confirmada","decimal(12,5) default '0.00000'");
Criar_Campo("T005A_Flag_Conversao_Unidade","varchar(1) default 'N'");
Criar_Campo("T006A_Quantidade_Comercial","decimal(12,5) default '0.00000'");
Criar_Campo("T006A_Preco_Unitario_Comercial","decimal(20,10) default '0.00000'");
Criar_Campo("T006A_D184_Id","int(10) unsigned DEFAULT '0'");
Criar_Campo("T008A_Quantidade_Comercial","decimal(12,5) default '0.00000'");
Criar_Campo("T008A_Preco_Unitario_Comercial","decimal(20,10) default '0.00000'");
Criar_Campo("T008A_D184_Id","int(10) unsigned DEFAULT '0'");
Criar_Campo("T014A_Valor_Base_ICMS_Mono_Retido","DECIMAL(15,2) DEFAULT '0.00'");
Criar_Campo("T014A_Valor_ICMS_Mono_Retido","DECIMAL(15,2) DEFAULT '0.00'");
Criar_Campo("D009A_Ficha_Conteudo_Importacao","varchar(40) DEFAULT ''");
Criar_Campo("T008A_Ficha_Conteudo_Importacao","varchar(40) DEFAULT ''");
Criar_Campo("D018_Prazo_Entrega","varchar(40) DEFAULT ''");
Criar_Campo("D018_Prazo_Transporte","int(10)");
Criar_Campo("D020_Prazo_Entrega","varchar(40) DEFAULT ''");
Criar_Campo("T006A_C007_Id_Impressao_Certificado","int(10) DEFAULT '0'");
Criar_Campo("T006A_Data_Impressao_Certificado","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D006_Percentual_FCP","decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T004A_D006_Id_Info","longtext");
Criar_Campo("T006A_D006_Id_Info","longtext");
Criar_Campo("T008A_D006_Id_Info","longtext");
Criar_Indice("T002_Data_Emissao");
Alterar_Tipo_Campo("T055_Quantidade","decimal(12,5) default '0.00000'");
Alterar_Tipo_Campo("T294_Quantidade","decimal(12,5) default '0.00000'");
Alterar_Tipo_Campo("T296_Quantidade","decimal(12,5) default '0.00000'");
Alterar_Tipo_Campo("T296_Quantidade_Saldo","decimal(12,5) default '0.00000'");
Alterar_Tipo_Campo("T294_Quantidade_Saldo","decimal(12,5) default '0.00000'");
Criar_Campo("T225A_Quantidade_Recebida","decimal(10,3) DEFAULT '0.00'");
Criar_Campo("T086_T225_Id","int(10) unsigned");
Criar_Campo("D007_Nome_Cooperativa", "varchar(30) default ''");
Criar_Campo("T003A_Valor_Total_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T003A_Valor_Total_FCP_Partilha","decimal(15,2) default '0.00'");
Criar_Campo("T003A_Valor_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T004A_Percentual_FCP","decimal(10,2) default '0.00'");
Criar_Campo("T004A_Valor_Base_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T004A_Valor_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T004A_Valor_Base_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T004A_Valor_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T004A_Valor_Base_FCP_Dest","decimal(15,2) default '0.00'");
Criar_Campo("T004A_Valor_FCP_Partilha","decimal(15,2) default '0.00'");
Criar_Campo("T005A_Valor_Total_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T005A_Valor_Total_FCP_Partilha","decimal(15,2) default '0.00'");
Criar_Campo("T005A_Valor_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T006A_Percentual_FCP","decimal(10,2) default '0.00'");
Criar_Campo("T006A_Valor_Base_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T006A_Valor_FCP","decimal(15,2) default '0.00'");
Criar_Campo("T006A_Valor_Base_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T006A_Valor_FCP_ST","decimal(15,2) default '0.00'");
Criar_Campo("T006A_Valor_Base_FCP_Dest","decimal(15,2) default '0.00'");
Criar_Campo("T006A_Valor_FCP_Partilha","decimal(15,2) default '0.00'"); 
Criar_Campo("T014A_Valor_Acrescimo_Custo","decimal(20,10) default '0.00'");
// Frete por NFS: vínculo da nota de serviço e parcela no custo dos produtos.
Criar_Campo("T013A_T013_Id_Frete_NFS", "int unsigned NOT NULL DEFAULT 0");
Criar_Indice("T013A_T013_Id_Frete_NFS");
Criar_Campo("T014A_Valor_Frete_NFS", "decimal(15,2) NOT NULL DEFAULT '0.00'");
Criar_Campo("T001A_Valor_Acrescimo_Custo","decimal(20,10) default '0.00'"); 
Criar_Indice("T001_Data_Hora_Insercao");
Alterar_Tipo_Campo("D042_Quantidade","decimal(10,5) default '0.00000'");
Alterar_Tipo_Campo("T071_Quantidade","decimal(10,5) default '0.00000'");
Criar_Campo("D006_Flag_Reduz_Base_DIFAL","varchar(1) default 'N'");
Criar_Campo("D006_Percentual_Reducao_Base_DIFAL","decimal(5,2) default '0.00'");
Criar_Campo("T004A_Percentual_Reducao_Base_DIFAL","decimal(5,2) default '0.00'");
Criar_Campo("T006A_Percentual_Reducao_Base_DIFAL","decimal(5,2) default '0.00'");
Criar_Campo("T008A_Percentual_Reducao_Base_DIFAL","decimal(5,2) default '0.00'");
Criar_Campo("D009A_Flag_Atualizar_Estoque","enum('S','N') default 'N'");
Criar_Indice("D009A_Flag_Atualizar_Estoque");
Criar_Campo("D053_Calculo_Difal_Por_Dentro","varchar(1) default 'N'"); 
Criar_Campo("D006_Flag_Isento_ICMS_Credito","varchar(1) default 'N'");
Criar_Campo("D006_Flag_Tipo_Desconto","varchar(1) default 'C'");
Criar_Campo("D006_Flag_Tipo_Desconto_PIS_COFINS","varchar(1) default 'C'");
Criar_Campo("T004A_Valor_Total_Desconto_Manual","decimal(20,2) default '0'");
Criar_Campo("T004A_Valor_Total_Desconto_Manual_Confirmada","decimal(20,2) default '0'");
Criar_Campo("T006A_Valor_Total_Desconto_Manual","decimal(20,2) default '0'");
Criar_Campo("D001A_Comprimento","decimal(10,6) default '0'");
Criar_Campo("D001A_Largura","decimal(10,6) default '0'");
Criar_Campo("D001A_Altura","decimal(10,6) default '0'");
Criar_Campo("T055A_Comprimento","decimal(10,6) default '0'");
Criar_Campo("T055A_Largura","decimal(10,6) default '0'");
Criar_Campo("T055A_Altura","decimal(10,6) default '0'");
Criar_Campo("D001A_Apelido_Produto","text default ''");
Criar_Indice("D001A_Apelido_Produto");
Criar_Campo("T055A_Resolucao_Divergencia","varchar(200) default ''");
Criar_Campo("T055A_Flag_Divergencia_Resolvida","varchar(1) default 'N'");
Criar_Campo("T172_Link_Video","varchar(500) default ''");
Criar_Campo("T075_C007_Id_Finalizou_Estoque","int default '0'");
Criar_Campo("T075_C007_Id_Finalizou_Divergencia","int default '0'");
Criar_Campo("D006_Flag_Deduz_FCP_ST_Valor_ST","varchar(1) default 'S'");
Criar_Campo("D006_Flag_Destaca_Desconto_NF","varchar(1) default 'N'");
Criar_Campo("D006_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("T004A_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("T006A_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("T008A_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("T007A_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("T005A_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("T003A_Percentual_Desconto_CFOP","decimal(5,2) default '0.00'");
Criar_Campo("D006_Aliquota_PIS","decimal(5,2) default '0.00'");
Criar_Campo("D006_Aliquota_COFINS","decimal(5,2) default '0.00'");
Criar_Campo("D006_Flag_Somar_Despesas_Base_IRPJ","varchar(1) default 'N'");
Criar_Campo("D006_Flag_Somar_Despesas_Base_CSLL","varchar(1) default 'N'");
Criar_Campo("D006_Flag_Somar_IPI_Base_Retido","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_ST_Base_Retido","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_FCP_Base_Retido","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_IPI_Base_COFINS_Retido","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_ST_Base_COFINS_Retido","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_FCP_Base_COFINS_Retido","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_FCP_Base_CSLL","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_IPI_Base_CSLL","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_ST_Base_CSLL","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_FCP_Base_IRPJ","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_IPI_Base_IRPJ","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_ST_Base_IRPJ","enum('S','N') default 'N'");
Criar_Campo("T008A_Valor_Base_PIS_Retido","decimal(20,5) default '0.00000'");
Criar_Campo("T008A_Valor_Base_COFINS_Retido","decimal(20,5) default '0.00000'");
Criar_Campo("T008A_Valor_PIS_Retido","decimal(20,5) default '0.00000'");
Criar_Campo("T008A_Valor_COFINS_Retido","decimal(20,5) default '0.00000'");
Criar_Campo("T007A_Valor_Total_PIS_Retido","decimal(20,5) default '0.00000'");
Criar_Campo("T007A_Valor_Total_COFINS_Retido","decimal(20,5) default '0.00000'");
Criar_Campo("D024A_D031_Id","int(10)");
Criar_Campo("D024A_Flag_Agendamento_Entrega","varchar(1)");
Criar_Campo("D024A_Instrucao_Entrega","text");
Criar_Campo("T005A_Data_Agendamento","date");
Criar_Indice("T004A_T066_Id");
Criar_Campo("T003A_D201_Id","int default '0'");
Criar_Campo("T003A_Data_Entrega","date default '0000-00-00'");
Criar_Campo("C007_Acesso_Hora_Inicio","time default '00:00:00'");
Criar_Campo("C007_Acesso_Hora_Fim","time default '00:00:00'");
Criar_Campo("D006_Flag_Escritura_Sped_Contribuicoes","varchar(1) default 'S'");
Criar_Campo("T007A_Flag_Escritura_Sped_Contribuicoes","varchar(1) default 'S'");
Criar_Campo("T013A_Flag_Escritura_Sped_Contribuicoes","varchar(1) default 'S'");
Criar_Indice("D006_Flag_Escritura_Sped_Contribuicoes");
Criar_Indice("T007A_Flag_Escritura_Sped_Contribuicoes");
Criar_Indice("T013A_Flag_Escritura_Sped_Contribuicoes");
Criar_Campo("T084A_D014_Id", "int default '0'");
Criar_Campo("T084A_D024_Recebedor_Id", "int");
Criar_Indice("T084A_D014_Id");
Criar_Campo("T084A_Cte_Original_Id", "int");
Criar_Campo("T084A_Peso_Base_Calculo", "decimal(10,4) default '0.0000'");
Criar_Campo("T084A_Peso_Cubado", "decimal(10,4) default '0.0000'");
Criar_Campo("T084A_Peso_Real", "decimal(10,4) default '0.0000'");
Criar_Campo("T084A_Chave_Acesso_Cte_Original", "varchar(44)");
Criar_Campo("D013A_Data_Ultima_Alteracao","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D013A_Data_Inclusao","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Indice("D013A_Data_Ultima_Alteracao");
Criar_Indice("D013A_Data_Inclusao");
Criar_Campo("C007_Id_Pipe_Drive","varchar(30)");
Criar_Campo("C007_Token_Pipe_Drive","varchar(100) default ''");
Criar_Campo("C007_Acesso_Dias_Nao_Uteis","varchar(1) default 'S'");
Criar_Campo("C007_D206_Id","int(10)");
Criar_Campo("T013A_Codigo_Situacao_Documento","varchar(2) default ''");
Criar_Campo("D137_D060_Ids","varchar(100) default ''");
Criar_Campo("D137_D003_Ids","varchar(100) default ''");
Criar_Campo("D145_D060_Ids","varchar(100) default ''");
Criar_Campo("D145_D003_Ids","varchar(100) default ''");
Criar_Campo("D006_Flag_Endereco_Aliquota_Icms_Interno_Difal", "varchar(1) default 'P'");
Criar_Campo("D007_Flag_Enviar_Multa_Remessa", "varchar(1) default 'N'");
Criar_Campo("T003A_Data_Ultima_Alteracao","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Indice("T003A_Data_Ultima_Alteracao");
Criar_Campo("D183_Tempo_Execucao","time DEFAULT '00:00:00'");
Criar_Campo("C039_Tempo_Execucao","time DEFAULT '00:00:00'");
Criar_Campo("D183_Grupo_Execucao","varchar(2) not null");
Criar_Campo("C039_Grupo_Execucao","varchar(2) not null");
Criar_Campo("D006_Flag_Tipo_Desconto_IPI","varchar(1) default 'C'");
Criar_Campo("T316_Codigo_Produto_CIAP","varchar(30) default ''");
Criar_Campo("T006A_Flag_Imprimir_Certificado","varchar(1) default 'N'");
Criar_Campo("T206_C007_Id","int default '0'");
Criar_Campo("T206_T066_Id","int default '0'");
Criar_Campo("T206_Flag_Requisicao_Interna","varchar(1) default 'N'");
Criar_Campo("T206_Flag_Finalizado","varchar(1) default 'A'");
Criar_Indice("T005A_Flag_Tipo");
Criar_Indice("T206_Flag_Finalizado");
Criar_Indice("T206_Data");
Criar_Indice("T206_Descricao_Defeito");
Criar_Indice("T206_Flag_Marcado");
Criar_Indice("D169_Descricao_Tipo_Produto");
Criar_Campo("T316_T008_Id","int default '0'");
Criar_Indice("T316_T008_Id");
Criar_Campo("T066A_Numero_Lote","varchar(100) default ''");
Criar_Campo("T066A_Flag_Ativo","varchar(1) default 'S'");
Criar_Indice("T066A_Flag_Ativo");
Criar_Campo("T048_T066_Id","int default '0'");
Criar_Campo("T071_T066_Id","int default '0'");
Criar_Campo("T030_T030_Id_Transferencia","int default '0'");
Criar_Campo("D006_Flag_Destaca_Reducao_ICMS_XML","varchar(1) default 'S'");
Criar_Campo("T013A_Flag_Destaca_Frete_SPED","varchar(1) default 'S'");
Criar_Campo("T055A_Valor_Outros_Acrescimos","decimal(19,6) default 0.00");
Criar_Campo("T075_Valor_Outros_Acrescimos","decimal(14,4) default 0.00");
Criar_Campo("D014_Codigo_Natureza_Conta_Corrente","varchar(3) default '01'");
Criar_Campo("D014_Codigo_Indicador_Conta_Corrente","varchar(3) default 'A'");
//Criar_Indice('D009_Data_Ultima_Entrada');
//Criar_Indice('D009_Data_Alteracao_Custo');
//Criar_Indice('D009_Data_Ultima_Venda');
// Campos serão criados por parte
//Criar_Campo("T004_Valor_Base_ICMS_Sem_Reducao","decimal(15,2) DEFAULT '0.00'");
//Criar_Campo("T004_Valor_Base_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
//Criar_Campo("T004_Valor_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
//Criar_Campo("T003_Valor_Total_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
//Criar_Campo("T003_Valor_Total_Base_ICMS_Diferido","decimal(15,2) DEFAULT '0.00'");
//Criar_Campo("T003_Valor_Total_Base_ICMS_Sem_Reducao","decimal(15,2) DEFAULT '0.00'");
Criar_Campo("T075_Duimp_Versao", "int(10) DEFAULT '0'");
Criar_Campo("T075_Duimp_Situacao", "varchar(80) DEFAULT ''");
Criar_Campo("T075_Duimp_Data_Consulta", "datetime DEFAULT NULL");
Criar_Campo("T055A_Duimp_Numero_Item", "int(10) DEFAULT '0'");
Criar_Campo("T055A_Duimp_Codigo_Produto", "varchar(60) DEFAULT ''");
Criar_Campo("T055A_Duimp_Versao_Produto", "varchar(20) DEFAULT ''");
Criar_Campo("T055A_Duimp_Codigo_Interno", "varchar(60) DEFAULT ''");
Criar_Campo("D001A_Quantidade_Estoque_Consolidado", "decimal(14,2) DEFAULT '0.00'");
Criar_Campo("D001A_Quantidade_Estoque_Liquido_Consolidado", "decimal(14,2) DEFAULT '0.00'");
Criar_Campo("D201_Chave_Sistema", "varchar(50) DEFAULT ''");
Criar_Campo("C004_Cor_Identidade","varchar(7) DEFAULT NULL");

// Segurança de senha: mantém a senha atual e as três anteriores em hash.
Alterar_Tipo_Campo("C007_Senha", "varchar(255)");
Criar_Campo("C007_Senha_Anterior_1", "varchar(255) DEFAULT NULL");
Criar_Campo("C007_Senha_Anterior_2", "varchar(255) DEFAULT NULL");
Criar_Campo("C007_Senha_Anterior_3", "varchar(255) DEFAULT NULL");
Criar_Campo("C007_Flag_Senha_Nao_Expira", "varchar(1) DEFAULT 'N'");
Criar_Campo("MCP008_Resource", "VARCHAR(255) NULL DEFAULT NULL");
// Existing MCP connections remain legacy connections: no identifier is backfilled here, so current /mcp registrations, OAuth grants, tokens and API Keys stay intact.
Criar_Campo("MCP001_Public_Id", "CHAR(24) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL");
$cMcp001PublicIdIndex = mysql_query("SHOW INDEX FROM MCP001 WHERE Key_name='U_MCP001_Public_Id'");
$cMcp001PublicIdIndexError = mysql_error();
if (!empty($cMcp001PublicIdIndexError)) { echo "SHOW INDEX FROM MCP001 WHERE Key_name='U_MCP001_Public_Id'<hr>$cMcp001PublicIdIndexError"; }
if (mysql_num_rows($cMcp001PublicIdIndex) === 0) {
    $cMcp001PublicIdUnique = "ALTER TABLE MCP001 ADD UNIQUE KEY U_MCP001_Public_Id (MCP001_Public_Id)";
    $cSql = mysql_query($cMcp001PublicIdUnique); $cErro = mysql_error(); if (!empty($cErro)) { echo "$cMcp001PublicIdUnique<hr>$cErro"; }
}

/* [REFORMA TRIBUTÁRIA] - Fluxos comerciais: orçamento/Peiddo.
 * Lote para a janela programada de T003A/T004A. */
Criar_Campos('T003A', array(
    'T003A_Valor_Total_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T003A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T004A', array(
    'T004A_Situacao_Tributaria_CBS_IBS' => "varchar(3) NOT NULL DEFAULT ''",
    'T004A_Classificacao_Tributaria_CBS_IBS' => "varchar(6) NOT NULL DEFAULT ''",
    'T004A_Situacao_Tributaria_CBS_IBS_Regular' => "varchar(3) NOT NULL DEFAULT ''",
    'T004A_Classificacao_Tributaria_CBS_IBS_Regular' => "varchar(6) NOT NULL DEFAULT ''",
    'T004A_Aliquota_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Percentual_Reducao_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Aliquota_CBS_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Percentual_Diferimento_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Aliquota_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Percentual_Reducao_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Aliquota_CBS_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_CBS_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Aliquota_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Percentual_Reducao_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Aliquota_IBS_Municipal_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Percentual_Diferimento_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Aliquota_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Percentual_Reducao_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Aliquota_IBS_Municipal_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_IBS_Municipal_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Aliquota_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Percentual_Reducao_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Aliquota_IBS_UF_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Percentual_Diferimento_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Aliquota_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Percentual_Reducao_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Aliquota_IBS_UF_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T004A_Valor_IBS_UF_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Valor_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T004A_Valor_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T005A', array(
    'T005A_Valor_Total_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T005A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T006A', array(
    'T006A_Situacao_Tributaria_CBS_IBS' => "varchar(3) NOT NULL DEFAULT ''",
    'T006A_Classificacao_Tributaria_CBS_IBS' => "varchar(6) NOT NULL DEFAULT ''",
    'T006A_Situacao_Tributaria_CBS_IBS_Regular' => "varchar(3) NOT NULL DEFAULT ''",
    'T006A_Classificacao_Tributaria_CBS_IBS_Regular' => "varchar(6) NOT NULL DEFAULT ''",
    'T006A_Aliquota_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Percentual_Reducao_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Aliquota_CBS_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Percentual_Diferimento_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Aliquota_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Percentual_Reducao_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Aliquota_CBS_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_CBS_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Aliquota_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Percentual_Reducao_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Aliquota_IBS_Municipal_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Percentual_Diferimento_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Aliquota_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Percentual_Reducao_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Aliquota_IBS_Municipal_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_IBS_Municipal_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Aliquota_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Percentual_Reducao_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Aliquota_IBS_UF_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Percentual_Diferimento_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Aliquota_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Percentual_Reducao_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Aliquota_IBS_UF_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T006A_Valor_IBS_UF_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Valor_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T006A_Valor_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T013A', array(
    'T013A_Valor_Total_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T013A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T014A', array(
    'T014A_Situacao_Tributaria_CBS_IBS' => "varchar(3) NOT NULL DEFAULT ''",
    'T014A_Classificacao_Tributaria_CBS_IBS' => "varchar(6) NOT NULL DEFAULT ''",
    'T014A_Situacao_Tributaria_CBS_IBS_Regular' => "varchar(3) NOT NULL DEFAULT ''",
    'T014A_Classificacao_Tributaria_CBS_IBS_Regular' => "varchar(6) NOT NULL DEFAULT ''",
    'T014A_Aliquota_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Percentual_Reducao_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Aliquota_CBS_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Percentual_Diferimento_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Aliquota_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Percentual_Reducao_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Aliquota_CBS_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_CBS_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Aliquota_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Percentual_Reducao_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Aliquota_IBS_Municipal_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Percentual_Diferimento_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Aliquota_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Percentual_Reducao_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Aliquota_IBS_Municipal_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_IBS_Municipal_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Aliquota_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Percentual_Reducao_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Aliquota_IBS_UF_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Percentual_Diferimento_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Aliquota_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Percentual_Reducao_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Aliquota_IBS_UF_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T014A_Valor_IBS_UF_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Valor_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T014A_Valor_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

// Cotação de compra e snapshot da cotação na ordem de compra.
Criar_Campos('T218A', array(
    'T218A_Valor_Total_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T218A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T219A', array(
    'T219A_Situacao_Tributaria_CBS_IBS' => "varchar(3) NOT NULL DEFAULT ''",
    'T219A_Classificacao_Tributaria_CBS_IBS' => "varchar(6) NOT NULL DEFAULT ''",
    'T219A_Situacao_Tributaria_CBS_IBS_Regular' => "varchar(3) NOT NULL DEFAULT ''",
    'T219A_Classificacao_Tributaria_CBS_IBS_Regular' => "varchar(6) NOT NULL DEFAULT ''",
    'T219A_Aliquota_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Percentual_Reducao_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Aliquota_CBS_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Percentual_Diferimento_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Aliquota_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Percentual_Reducao_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Aliquota_CBS_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_CBS_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Aliquota_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Percentual_Reducao_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Aliquota_IBS_Municipal_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Percentual_Diferimento_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Aliquota_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Percentual_Reducao_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Aliquota_IBS_Municipal_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_IBS_Municipal_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Aliquota_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Percentual_Reducao_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Aliquota_IBS_UF_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Percentual_Diferimento_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Aliquota_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Percentual_Reducao_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Aliquota_IBS_UF_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T219A_Valor_IBS_UF_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Valor_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T219A_Valor_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T224A', array(
    'T224A_Valor_Total_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T224A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

Criar_Campos('T225A', array(
    'T225A_Situacao_Tributaria_CBS_IBS' => "varchar(3) NOT NULL DEFAULT ''",
    'T225A_Classificacao_Tributaria_CBS_IBS' => "varchar(6) NOT NULL DEFAULT ''",
    'T225A_Situacao_Tributaria_CBS_IBS_Regular' => "varchar(3) NOT NULL DEFAULT ''",
    'T225A_Classificacao_Tributaria_CBS_IBS_Regular' => "varchar(6) NOT NULL DEFAULT ''",
    'T225A_Aliquota_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Percentual_Reducao_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Aliquota_CBS_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Percentual_Diferimento_CBS' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_Diferimento_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Aliquota_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Percentual_Reducao_CBS_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Aliquota_CBS_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_CBS_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Aliquota_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Percentual_Reducao_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Aliquota_IBS_Municipal_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Percentual_Diferimento_IBS_Municipal' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_Diferimento_IBS_Municipal' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Aliquota_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Percentual_Reducao_IBS_Municipal_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Aliquota_IBS_Municipal_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_IBS_Municipal_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Aliquota_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Percentual_Reducao_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Aliquota_IBS_UF_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Percentual_Diferimento_IBS_UF' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_Diferimento_IBS_UF' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Aliquota_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Percentual_Reducao_IBS_UF_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Aliquota_IBS_UF_Efetiva_Regular' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T225A_Valor_IBS_UF_Regular' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Valor_Base_CBS_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Valor_Total_Reforma' => "decimal(15,2) NOT NULL DEFAULT 0.00",
    'T225A_Valor_IBS' => "decimal(15,2) NOT NULL DEFAULT 0.00"
));

// Histórico: valores e alíquotas efetivas unitárias necessários para custo e preço de venda.
Criar_Campos('T001A', array(
    'T001A_Valor_CBS' => "decimal(20,10) NOT NULL DEFAULT 0.0000000000",
    'T001A_Valor_IBS' => "decimal(20,10) NOT NULL DEFAULT 0.0000000000",
    'T001A_Aliquota_CBS_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T001A_Aliquota_IBS_UF_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000",
    'T001A_Aliquota_IBS_Municipal_Efetiva' => "decimal(7,4) NOT NULL DEFAULT 0.0000"
));

// CFOP: define se o imposto recuperável da entrada deve ser abatido do custo/preço.
Criar_Campos('D006', array(
    'D006_Flag_Deb_Crd_CBS' => "enum('S','N') NOT NULL DEFAULT 'N'",
    'D006_Flag_Deb_Crd_IBS' => "enum('S','N') NOT NULL DEFAULT 'N'"
));

//---- FIM FELIPE
//-- Gabriel Ribeiro
Criar_Campo("T013A_JSON", "JSON DEFAULT NULL");
//-- FIM Gabriel Ribeiro

//---- Felipe Kadanos ----
Criar_Campo("C019_Ordem_Empresa","int(5) unsigned DEFAULT 0");
Criar_Campo("D021_Ativo","varchar(1) DEFAULT 'S'");
Criar_Campo("D006_Flag_Somar_ICMS_Diferido","varchar(1) DEFAULT 'N'");
Criar_Campo("D006_Flag_ICMS_Deson_DANFE","varchar(1) DEFAULT 'N'");
Criar_Campo("T055A_Flag_Credita_Estoque","varchar(1) DEFAULT 'S'");
Criar_Campo("D001A_Observacao_Entrada","text DEFAULT ''");
Criar_Campo("T295_Flag_Cancelado","varchar(1) DEFAULT 'N'");
Criar_Campo("T003A_D148_Id_Redespacho","int DEFAULT 0");
Criar_Campo("T005A_D148_Id_Redespacho","int DEFAULT 0");
Criar_Campo("T007A_D148_Id_Redespacho","int DEFAULT 0");
Criar_Campo("C007A_Ramal","varchar(255)");
Criar_Campo("C007_IPV_1", "decimal(10,4) DEFAULT '0.00'");
Criar_Campo("T140_Flag_Marcado","varchar(1) DEFAULT 'N'");
Criar_Campo("T140_Numero_Serie","text DEFAULT ''");
Criar_Campo("T140_C007_Id","int DEFAULT 0");
Criar_Campo("T140_Data_Hora_Upload","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T140_Tipo_Documento","int DEFAULT 0");
Criar_Campo("D073_Flag_Ativo","varchar(1) DEFAULT 'S'");
Criar_Campo("T007A_Ordem_Compra","varchar(60) DEFAULT ''");
Criar_Campo("T084_Chave_Acesso_Cte","varchar(44) DEFAULT NULL");
Criar_Campo("T004A_Altura", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T004A_Largura", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T004A_Comprimento", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T004A_Resultado", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T006A_Altura", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T006A_Largura", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T006A_Comprimento", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T006A_Resultado", "DECIMAL(12,5) NOT NULL DEFAULT '0.00000'");
Criar_Campo("T006A_Flag_Transacao_Externa", "char(1) NOT NULL DEFAULT 'N'");
Criar_Campo("D009A_Flag_Pertence_Empresa","enum('','S','N') DEFAULT ''");
//---- Fim Felipe Kadanos ----

//---- THOMAS
Criar_Campo("D137_D022_Ids" , "varchar(100) DEFAULT ''");
Criar_Campo("D004_Flag_Fulfillment", "varchar(1) DEFAULT 'N'");
Criar_Campo("D009A_JSON", "JSON DEFAULT NULL");
Criar_Campo("T066A_JSON", "JSON DEFAULT NULL");
//---- FIM THOMAS

/* ---- inicio Gabriel Armando ---- */
Criar_Campo("D069A_JSON", "JSON DEFAULT NULL");
Criar_Campo("T219A_JSON", "JSON DEFAULT NULL");
Criar_Campo("T004A_JSON", "JSON DEFAULT NULL");
Criar_Campo("T224A_JSON", "JSON DEFAULT NULL");
Criar_Campo("T224A_Hora_Emissao", "time DEFAULT NULL");
Criar_Campo("T006A_Data_Hora_Insercao", "datetime DEFAULT NULL");
/* ---- fim Gabriel Armando ------- */

// ---- inicio Gabriel Fasolim ---- 
Criar_Campo("C004_JSON", "JSON DEFAULT NULL");
// ---- FIM Gabriel Fasolim
//Campos T002a
Criar_Campo("T002A_Nr_Parcelas_Cartao","int COMMENT 'Numero de Parcelas Baixa com Cartao'");
Criar_Campo("T002A_Flag_Enviar_Email","enum('S','N') default 'N'");
Criar_Campo("T002A_Data_Envio_Email","datetime DEFAULT '0000-00-00 00:00:00'");

//Campos t003a
Criar_Campo("T003A_Data_Envio_Primeiro_Email", "datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T003A_SMT018_Id", "int COMMENT 'Id Cartao Fidelidade'");
Criar_Campo("T003A_Garantia", "int COMMENT 'Tempo de garantia dos produtos'");
Criar_Campo("T003A_D024_Id", "int");

//Campos t004a
Criar_Campo("T004A_T066_Id", "int(10) DEFAULT 0");
Criar_Campo("T004A_Data_Cliente", "date DEFAULT '0000-00-00'");
Criar_Campo("T004A_Data_Entrega", "date DEFAULT '0000-00-00'");
Criar_Campo("T004A_Data_Hora_Solicitacao_Compra","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T004A_Quantidade_Conversao", "DECIMAL(12,6) DEFAULT NULL");

Criar_Indice('T004A_Flag_Solicitar_Prazo_Compras');

//Campos t006a
Criar_Campo("T006A_Quantidade_Conversao", "DECIMAL(12,6) DEFAULT NULL");
Criar_Campo("T006A_Quantidade_Conversao_Separacao", "DECIMAL(12,6) DEFAULT NULL");
Criar_Campo("T006A_JSON", "JSON DEFAULT NULL");

//Campos t008a
Criar_Campo("T008A_Quantidade_Conversao", "DECIMAL(12,6) DEFAULT NULL");

//Campos t238a
Criar_Campo("T238A_Quantidade_Conversao_Separacao", "DECIMAL(12,6) DEFAULT NULL");
Criar_Campo("T238A_T314_Id", "int COMMENT 'Volume que gerou essa separacao'");
Criar_Campo("T238A_T315_Id", "int COMMENT 'Item do volume que gerou essa separacao'");

// ---- Lucas Passos
//Criar_Campo("T168_T293_Id", "int(10) unsigned");
//Criar_Campo("T168_T295_Id", "int(10) unsigned");
Criar_Campo("T296_T295_Id", "int(10) unsigned");

Criar_Campo("T294_Quantidade_Saldo ", "int DEFAULT 0");
Criar_Campo("T294_Quantidade_PO", "int DEFAULT 0");
Criar_Campo("T294_Flag_Finalizar_Compra", "char(1) DEFAULT 'N'");
Criar_Campo("T296_T294_Id", "int(10) unsigned");

Criar_Campo("T296_Quantidade_Saldo ", "int DEFAULT 0");
Criar_Campo("T296_Quantidade_DI", "int DEFAULT 0");
Criar_Campo("T296_Flag_Finalizar_Compra", "char(1) DEFAULT 'N'");
Criar_Campo("T055_T296_Id", "int(10) unsigned");
Alterar_Campo("T294_T075_Id","T294_T293_Id","int unsigned DEFAULT NULL");
Alterar_Campo("T296_T075_Id","T296_T295_Id","int unsigned DEFAULT NULL");
Alterar_Campo("T293_Data_Emissao_DI","T293_Data_Emissao","date DEFAULT NULL");
Alterar_Campo("T295_Data_Emissao_DI","T295_Data_Emissao","date DEFAULT NULL");


Criar_Indice('D049_Origem_Mercadoria');

// ---- Fim Lucas Passos


// --- Gabriel Carvalho

$cClausula = <<<EOT
create table if not exists D184 (
  D184_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D184_D037_Id INT (10) NOT NULL, 
  D184_D001_Id INT(10) NOT NULL, 
  D184_Quantidade_Conversao DECIMAL(12,6) NOT NULL, 
  D184_Tipo_Operacao VARCHAR(1) NOT NULL
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

//////////////////////////////////////////

// DOUGLAS GUIMEPA


Criar_Campo("C004_Aliquota_Simples","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("C004_Aliquota_TTD","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("C004_Flag_Usar_Aliquota_Difal","char(1) DEFAULT 'N'");


////////////////////////////////////////// 

// GUSTAVO GUIMEPA

Criar_Campo("D004_JSON", "JSON DEFAULT NULL");
Alterar_Tipo_Campo("D004_Local","varchar(150) DEFAULT ''");

Criar_Campo("D109_Estado_Civil","varchar(50) default ''");
Criar_Campo("D109_Filhos"," varchar(10) default ''");
Criar_Campo("D109_Nome_Contato_Um","text");
Criar_Campo("D109_Nome_Contato_Dois","text");
Criar_Campo("D109_Telefone_Contato_Um","int(15)");
Criar_Campo("D109_Telefone_Contato_Dois"," int(15)");
Criar_Campo("D109_Naturalidade"," varchar(100) default ''");

Criar_Campo("T014A_JSON","JSON DEFAULT NULL");


$cClausula = <<<EOT
create table if not exists D203 (
  D203_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D203_D109_Id INT(10) NOT NULL,
  D203_Periodo_Aquisitivo VARCHAR(50),
  D203_Ferias_Observacao VARCHAR(250),
  D203_Ferias_Ausencia VARCHAR(50),
  D203_Ferias_Inicio VARCHAR(50),
  D203_Ferias_Abono int(30),
  D203_Ferias_Fim VARCHAR(50),
  D203_Ferias_Soma VARCHAR(50)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}


$cClausula = <<<EOT
create table if not exists D204 (
  D204_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D204_D109_Id INT(10) NOT NULL,
  D204_Exame_Data VARCHAR(50),
  D204_Exame_Arq VARCHAR(250),
  D204_Exame_Local VARCHAR(50),
  D204_Exame VARCHAR(50),
  D204_Exame_Observacao VARCHAR(50)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

$cClausula = <<<EOT
create table if not exists D205 (
  D205_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  D205_D109_Id INT(10) NOT NULL,
  D205_Observacao VARCHAR(250),
  D205_JSON json
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

/////////////////////////////////////////



// D037_Variacao_Unidade Adicionar Campo
Criar_Campo("D037_Variacao_Unidade","varchar(60)");
Criar_Campo("T014A_Aliquota_IPI" ,"decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T014A_Aliquota_PIS" ,"decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T014A_Aliquota_COFINS" ,"decimal(5,2) DEFAULT '0.00'");


Criar_Campo("D014_Codigo_Plano_Conta_Fiscal", "varchar(50)"); 
Criar_Campo("D014_Mascara_Plano_Conta_Fiscal", "varchar(50)");
Criar_Campo("D004_Indicador_Propriedade", "varchar(3)");


Criar_Campo("T013_D024_Id_Participante", "int(10) unsigned");
Criar_Indice("T013_D024_Id_Participante");

Criar_Campo("T297_Descricao_Complementar_Observacao", "text");
Criar_Campo("D006_D188_Id", "int(10) unsigned");
Criar_Indice("D006_D188_Id ");
Criar_Campo("D188_D189_Id", "int(10) unsigned");
Criar_Indice("D188_D189_Id");

Criar_Campo("T084_Valor_Base_PIS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T084_Valor_Base_COFINS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T084_Aliquota_PIS", "decimal(6,2) DEFAULT '0.00'");
Criar_Campo("T084_Aliquota_COFINS", "decimal(6,2) DEFAULT '0.00'");
Criar_Campo("T084_Valor_PIS", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T084_Valor_COFINS", "decimal(10,2) DEFAULT '0.00'");

Criar_Campo("T302_C004_Id", "int(10) unsigned");
Criar_Indice("T302_C004_Id");
Criar_Campo("T301_C004_Id", "int(10) unsigned");
Criar_Indice("T301_C004_Id");
Criar_Campo("T301_Total", "decimal(14,2)");
Criar_Campo("D194_Link", "varchar(200)");
Criar_Campo("C004_Classificacao_Contribuinte", "varchar(2)");
Criar_Campo("T084_Situacao", "varchar(2)");
Criar_Campo("T084_Indicador_Tipo_Frete", "char(1)");
Criar_Campo("T084_Modelo", "varchar(2)");
Criar_Campo("T084_Serie", "varchar(4)");
Criar_Campo("T084_Subserie", "varchar(3)");
Criar_Campo("T084_Tipo_Conhecimento", "char(1)");
Criar_Campo("T084_Indicador_Natureza_Frete", "varchar(2)");
Criar_Campo("T084_Base_Calculo_Credito", "varchar(2)");
Criar_Campo("D006_Base_Calculo_Credito","varchar(3)");
Criar_Campo("D006_Indicador_Natureza_Frete", "varchar(3)");

/* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
Versão 1.64 – Setembro de 2024, por solicitação da Smartness
Gabriel Cegantini - 02/10/2025
================================================================================ */
Criar_Campo('D006_Codigo_Beneficio_Credito_Presumido', "varchar(10) COMMENT 'Representa o campo do XML cCredPresumido (I05h)'");
Criar_Campo('D006_Percentual_Beneficio_Credito_Presumido', "decimal(7,4) COMMENT 'Representa o campo do XML pCredPresumido (I05i)'");
Criar_Campo('D006_Codigo_Beneficio_Reducao_Base_ICMS', "varchar(10) COMMENT 'Representa o campo do XML cBenefRBC (N14a)'");

Criar_Campo('T008A_Codigo_Beneficio_Credito_Presumido', "varchar(10) COMMENT 'Representa o campo do XML cCredPresumido (I05h), preenchido automaticamente com o campo D006_Codigo_Beneficio_Credito_Presumido'");
Criar_Campo('T008A_Percentual_Beneficio_Credito_Presumido', "decimal(7,4) COMMENT 'Representa o campo do XML pCredPresumido (I05i), preenchido automaticamente com o campo D006_Percentual_Beneficio_Credito_Presumido'");
Criar_Campo('T008A_Valor_Beneficio_Credito_Presumido', "decimal(15,2) COMMENT 'Representa o campo do XML vCredPresumido (I05j), preenchido automaticamente com o campo D006_Valor_Beneficio_Credito_Presumido'");
Criar_Campo('T008A_Codigo_Beneficio_Reducao_Base_ICMS', "varchar(10) COMMENT 'Representa o campo do XML cBenefRBC (N14a), preenchido automaticamente com o campo D006_Codigo_Beneficio_Reducao_Base_ICMS'");
/* ================================================================================ */

Criar_Campo("T014A_Flag_Destino_Produto","varchar(1)");
Criar_Campo("T014A_Aliquota_ICMS","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T014A_ICMS_Inter_Estadual","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("T014A_Aliquota_ICMS_Interno","decimal(5,2) DEFAULT '0.00'");
Criar_Campo("D053_Percentual_FCP_Compra","decimal(10,2) DEFAULT '0.00'");
//T256A
Alter_Query("ALTER TABLE T256A ADD CONSTRAINT IDX_T256A_T256_IX UNIQUE(T256A_T256_Id)");
//T225A 
Alter_Query("ALTER TABLE T225A ADD CONSTRAINT IDX_T225A_T225_IX UNIQUE(T225A_T225_Id)");
//T008A
Alter_Query("ALTER TABLE T008A ADD CONSTRAINT IDX_T008A_T008_IX UNIQUE(T008A_T008_Id)");
//T005A
Alter_Query("ALTER TABLE T005A ADD CONSTRAINT IDX_T005A_T005_IX UNIQUE(T005A_T005_Id)");
//D013A
Alter_Query("ALTER TABLE D013A ADD CONSTRAINT IDX_D013A_D013_IX UNIQUE(D013A_D013_Id)");
//T004A
Alter_Query("ALTER TABLE T004A ADD CONSTRAINT IDX_T004A_T004_IX UNIQUE(T004A_T004_Id)");
//T002A
Alter_Query("ALTER TABLE T002A ADD CONSTRAINT IDX_T002A_T002_IX UNIQUE(T002A_T002_Id)");
//T014A
Alter_Query("ALTER TABLE T014A ADD CONSTRAINT IDX_T014A_T014_IX UNIQUE(T014A_T014_Id)");
//D009A
Alter_Query("ALTER TABLE D009A ADD CONSTRAINT IDX_D009A_D009_IX UNIQUE(D009A_D009_Id)");
//T066A
Alter_Query("ALTER TABLE T066A ADD CONSTRAINT IDX_T066A_T066_IX UNIQUE(T066A_T066_Id)");
//T015A Deixamos o indice assim pois a smartness ja tinha criado desse jeito na IDE deles, e para não duplicar mantivemos assimm
Alter_Query("ALTER TABLE T015A ADD CONSTRAINT T015A_T015_Id_IX UNIQUE(T015A_T015_Id)");

Criar_Campo("T014A_Valor_ICMS_Substituicao", "decimal(10,2) DEFAULT '0.00'");
Criar_Campo("T014A_Valor_FCP_ST", "decimal(15,2) DEFAULT '0.00'");

Criar_Campo("T013_Classe_Consumo","varchar(2)");
Criar_Campo("T013_Tipo_Ligacao","varchar(2)");
Criar_Campo("T013_Grupo_Tensao","varchar(2)");
Criar_Campo("T013_Tipo_Assinante", "varchar(2)");
Criar_Indice("D004_Indicador_Propriedade");

// CT-e
Criar_Campo("T084_Codigo_CFOP", "varchar(4)");
Criar_Campo("T084_Flag_Modalidade_Transporte", "varchar(2)");
Criar_Campo("T084_Flag_Travar", "char(1) DEFAULT 'N'");
Criar_Campo("T084_Hora_Emissao", "time");
Criar_Campo("T084_Observacao_Cancelamento", "text");
Criar_Campo("T084_Flag_Tipo_Servico", "char(1)");
Criar_Campo("T084_Flag_Retira", "char(1)");
Criar_Campo("T084_Informacoes_Adicionais_Retira", "varchar(160)");
Criar_Campo("T084_Natureza_Operacao", "varchar(60) NOT NULL");
Criar_Campo("T084_Flag_Indicador_IE", "char(1)");
Criar_Campo("T084_Observacoes_Gerais", "varchar(2000)");
Criar_Campo("T084_Versao_Cte", "varchar(20)");
Criar_Campo("T084_D018_Id_Origem","int");
Criar_Indice("T084_D018_Id_Origem");
Criar_Campo("T084_D020_Id_Origem","int");
Criar_Indice("T084_D020_Id_Origem");
Criar_Campo("T084_D018_Id_Destino","int");
Criar_Indice("T084_D018_Id_Destino");
Criar_Campo("T084_D020_Id_Destino","int");
Criar_Indice("T084_D020_Id_Destino");
Criar_Campo("T084_Tomador_CNPJ", "varchar(14)");
Criar_Campo("T084_Tomador_CPF", "varchar(11)");
Criar_Campo("T084_Tomador_IE", "varchar(14)");
Criar_Campo("T084_Tomador_Nome", "varchar(60)");
Criar_Campo("T084_Tomador_Nome_Fantasia", "varchar(60)");
Criar_Campo("T084_Tomador_Rua", "varchar(255)");
Criar_Campo("T084_Tomador_Numero", "varchar(60)");
Criar_Campo("T084_Tomador_Bairro", "varchar(60)");
Criar_Campo("T084_Tomador_Codigo_Cidade_IBGE", "varchar(7)");
Criar_Campo("T084_Tomador_Cidade", "varchar(60)");
Criar_Campo("T084_Tomador_UF", "varchar(2)");
Criar_Campo("T084_Emitente_CNPJ", "varchar(14)");
Criar_Campo("T084_Emitente_IE", "varchar(14)");
Criar_Campo("T084_Emitente_Nome", "varchar(60)");
Criar_Campo("T084_Emitente_Rua", "varchar(255)");
Criar_Campo("T084_Emitente_Numero", "varchar(60)");
Criar_Campo("T084_Emitente_Bairro", "varchar(60)");
Criar_Campo("T084_Emitente_Codigo_Cidade_IBGE", "varchar(7)");
Criar_Campo("T084_Emitente_Cidade", "varchar(60)");
Criar_Campo("T084_Emitente_CEP", "varchar(8)");
Criar_Campo("T084_Emitente_UF", "varchar(2)");
Criar_Campo("T084_Emitente_Telefone", "varchar(20)");
Criar_Campo("T084_Emitente_UF_IBGE", "int(7)");
Criar_Campo("T084_Remetente_CNPJ", "varchar(14)");
Criar_Campo("T084_Remetente_CPF", "varchar(11)");
Criar_Campo("T084_Remetente_IE", "varchar(14)");
Criar_Campo("T084_Remetente_Nome", "varchar(60)");
Criar_Campo("T084_Remetente_Telefone", "varchar(20)");
Criar_Campo("T084_Remetente_Rua", "varchar(255)");
Criar_Campo("T084_Remetente_Numero", "varchar(60)");
Criar_Campo("T084_Remetente_Bairro", "varchar(60)");
Criar_Campo("T084_Remetente_Codigo_Cidade_IBGE", "varchar(7)");
Criar_Campo("T084_Remetente_Cidade", "varchar(60)");
Criar_Campo("T084_Remetente_CEP", "varchar(8)");
Criar_Campo("T084_Remetente_UF", "varchar(2)");
Criar_Campo("T084_Remetente_Codigo_Pais", "varchar(4)");
Criar_Campo("T084_Remetente_Nome_Pais", "varchar(60)");
Criar_Campo("T084_Expedidor_CNPJ", "varchar(14)");
Criar_Campo("T084_Expedidor_CPF", "varchar(11)");
Criar_Campo("T084_Expedidor_IE", "varchar(14)");
Criar_Campo("T084_Expedidor_Nome", "varchar(60)");
Criar_Campo("T084_Expedidor_Rua", "varchar(255)");
Criar_Campo("T084_Expedidor_Numero", "varchar(60)");
Criar_Campo("T084_Expedidor_Bairro", "varchar(60)");
Criar_Campo("T084_Expedidor_Codigo_Cidade_IBGE", "varchar(7)");
Criar_Campo("T084_Expedidor_Cidade", "varchar(60)");
Criar_Campo("T084_Expedidor_CEP", "varchar(8)");
Criar_Campo("T084_Expedidor_UF", "varchar(2)");
Criar_Campo("T084_Recebedor_CNPJ", "varchar(14)");
Criar_Campo("T084_Recebedor_CPF", "varchar(11)");
Criar_Campo("T084_Recebedor_IE", "varchar(14)");
Criar_Campo("T084_Recebedor_Nome", "varchar(60)");
Criar_Campo("T084_Recebedor_Rua", "varchar(255)");
Criar_Campo("T084_Recebedor_Numero", "varchar(60)");
Criar_Campo("T084_Recebedor_Bairro", "varchar(60)");
Criar_Campo("T084_Recebedor_Codigo_Cidade_IBGE", "varchar(7)");
Criar_Campo("T084_Recebedor_Cidade", "varchar(60)");
Criar_Campo("T084_Recebedor_CEP", "varchar(8)");
Criar_Campo("T084_Recebedor_UF", "varchar(2)");
Criar_Campo("T084_Destinatario_CNPJ", "varchar(14)");
Criar_Campo("T084_Destinatario_CPF", "varchar(11)");
Criar_Campo("T084_Destinatario_IE", "varchar(14)");
Criar_Campo("T084_Destinatario_Nome", "varchar(60)");
Criar_Campo("T084_Destinatario_Telefone", "varchar(20)");
Criar_Campo("T084_Destinatario_Rua", "varchar(255)");
Criar_Campo("T084_Destinatario_Numero", "varchar(60)");
Criar_Campo("T084_Destinatario_Bairro", "varchar(60)");
Criar_Campo("T084_Destinatario_Codigo_Cidade_IBGE", "varchar(7)");
Criar_Campo("T084_Destinatario_Cidade", "varchar(60)");
Criar_Campo("T084_Destinatario_CEP", "varchar(8)");
Criar_Campo("T084_Destinatario_UF", "varchar(2)");
Criar_Campo("T084_Destinatario_Codigo_Pais", "varchar(4)");
Criar_Campo("T084_Destinatario_Nome_Pais", "varchar(60)");
Criar_Campo("T084_Destinatario_Email", "varchar(60)");
Criar_Campo("T084_Flag_Situacao_Tributaria","varchar(4)");
Criar_Campo("T084_Percentual_Reducao_ICMS","decimal(5,2) default '0.00'");
Criar_Campo("T084_Valor_Base_ICMS_Substituicao_Retido", "decimal(15,2) default '0.00'");
Criar_Campo("T084_Valor_ICMS_Substituicao_Retido", "decimal(15,2) default '0.00'");
Criar_Campo("T084_Valor_Total_Carga","decimal(15,2) default '0.00'");
Criar_Campo("T084_Produto_Predominante","varchar(60)");
Criar_Campo("T084_Produto_Predominante_Outras_Caracteristicas","varchar(30)");
Criar_Campo("T084_Valor_Averbacao","decimal(15,2) default '0.00'");
Criar_Campo("T085_Chave_Acesso_Nfe","varchar(44)");
Criar_Campo("T085_Flag_Tipo_Documento", "char(1) default '2'"); 
Criar_Campo("T084_Numero_Recibo_Cte", "varchar(20)");
Criar_Campo("T084_Numero_Protocolo_Cancelamento_Cte", "varchar(20)");
Criar_Campo("C004_Ultimo_Numero_CTe", "varchar(12)");
Criar_Campo("C004_Ultimo_Numero_Serie_CTe", "varchar(10)");
Criar_Campo("C004_Ambiente_CTe", "enum('1','2') default 2");
Criar_Campo("C004_Versao_CTe", "varchar(20)");
Criar_Campo("T084_Flag_Entrada_Saida", "enum('E','S') default 'E'");
Criar_Indice("T084_Flag_Entrada_Saida");
Criar_Campo("T085_Data_Prevista_Entrega","date");
Criar_Campo("T084_D024_Tomador_Id","int");
Criar_Indice("T084_D024_Tomador_Id");
Criar_Campo("T084_D024_Remetente_Id","int");
Criar_Indice("T084_D024_Remetente_Id");
Criar_Campo("T084_D024_Destinatario_Id","int");
Criar_Indice("T084_D024_Destinatario_Id");
Criar_Campo("T084_Valor_Receber","decimal(15,2)");
Criar_Campo("T084_Pgto_Parcelas","varchar(10)");
Criar_Campo("T084_Pgto_Primeiro_Vcto_Tipo","varchar(10)");
Criar_Campo("T084_Pgto_Primeiro_Vcto_Valor","varchar(10)");
Criar_Campo("T084_Pgto_Dias_Vcto_Tipo","varchar(10)");
Criar_Campo("T084_Pgto_Dias_Vcto_Valor","varchar(10)");
Criar_Campo("T084_Pgto_C007_Id","int");
Criar_Campo("T084_Pgto_Data","datetime");
Criar_Campo("T084_Pgto_Portador","varchar(10)");
Criar_Campo("T084_D027_Id","int");
Criar_Indice("T084_D027_Id");
//Criação do vinculo com o conhecimento
Criar_Campo("T002_T084_Id","int(10) unsigned");
Criar_Indice("T002_T084_Id");

Criar_Campo("T085_C004_Origem_Id", "int(10) default 0");
Criar_Indice("T085_C004_Origem_Id");
Criar_Indice("T085_T007_Id");
Criar_Campo("T084_Ambiente_CTe", "enum('1','2') default 2");
Criar_Campo("C004_RNTRC","varchar(8)");
Criar_Campo("T084_Data_Hora_Protocolo_CTe","datetime");
Criar_Campo("T084_Descricao_CFOP","varchar(60)");
Criar_Campo("T084_Fuso_Horario_Emissao","varchar(6) default null");


//Transmissão
Criar_Campo("T084_TX_Resposta_Codigo_Interno", "int(11) default 0");
Criar_Campo("T084_TX_Retorno_Codigo", "text");
Criar_Campo("T084_TX_Retorno_Motivo", "text");
Criar_Campo("T084_TX_Timestamp_Envio_Sefaz", "datetime default '0000-00-00 00:00:00'");
Criar_Campo("T084_TX_Flag_Finalizada", "int(1) default '0'");
Criar_Campo("T084_Arquivo_XML", "text");
Criar_Campo("T084_Data_Hora_Cancelamento_Cte","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T084_Flag_Enviar","char(1) default 'N'");
Criar_Campo("T084_Numero_Lote", "int");
Criar_Campo("T084_C007_Id_Marcou_Enviar","int(11)");


//lterar_Tipo_Campo("D137_Flag_Destino_Produto", "varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T003_Flag_Destino_Produto", "varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T004_Flag_Destino_Produto", "varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T005_Flag_Destino_Produto", "varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T006_Flag_Destino_Produto", "varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T007_Flag_Destino_Produto", "varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T008_Flag_Destino_Produto", "varchar(2) DEFAULT ''"); 


Criar_Campo("T084_Flag_Denegado","varchar(1) default 'N'");
Criar_Indice("T084_Flag_Denegado");
Criar_Indice("T084_Flag_Enviar");
Criar_Indice("T084_Flag_Cancelado");

Criar_Campo("T007_Numero_Lote", "int default 1");
Criar_Indice("T007_Numero_Lote");


// mysql_query("UPDATE T182 SET T182_Tipo_Documento=IF(T182_NF_Venda = 'NAO TEM','T005','T007'), 
// 	                  T182_Numero_Documento=IF(T182_NF_Venda='NAO TEM',T182_Pedido,T182_NF_Venda), 
//                     T182_Id_Produto= IF(T182_NF_Venda='NAO TEM',
//                         (SELECT T006_Id
//                          	FROM T006 
//                          		left join D009 on D009_Id=T006_D009_Id 
//                          	where T006_T005_Id = T182_Pedido and T006_D009_Id = T182_D009_Id
//                         	limit 1),                        
//                        	(SELECT T008_Id 
//                          	FROM T008 
//                          		left join D009 on D009_Id=T008_D009_Id     
//                          		left join T007 on T007_Id=T008_T007_Id
//                          	where T007_Numero_Nota_Fiscal= T182_NF_Venda and T008_D009_Id = T182_D009_Id
//                         	limit 1)),
//                     T182_C004_Id = IF(T182_C004_Id = 0,(SELECT D009_C004_Id FROM D009 WHERE D009_Id = T182_D009_Id),T182_C004_Id)");

Criar_Campo("T182_Tipo_Documento", "varchar(4) NOT NULL");
Criar_Campo("T182_Numero_Documento", "varchar(12) NOT NULL");
Criar_Campo("T182_Id_Produto", "int(11) NOT NULL");
Criar_Indice("T182_Id_Produto");
Criar_Campo("T182_D197_Id", "int(11) NOT NULL");
Criar_Indice("T182_D197_Id");
Criar_Campo("T182_Data_Conclusao_Cliente","date");
Criar_Campo("T182_Hora_Conclusao_Cliente", "time");
Alterar_Tipo_Campo("T182_Quantidade", "decimal(12,5) default '0.0000'");
Criar_Campo("T312_D198_Id", "int(11)");
Criar_Indice("T312_D198_Id");
Criar_Campo("T312_Flag_Com_Custo", "enum('S','N') default 'S'");
Criar_Campo("T182_Data_Previsao_Fechamento","date");
Criar_Campo("T182_D199_Id", "int(11)");
Criar_Indice("T182_D199_Id");
Criar_Indice("T182_Pedido");
Criar_Indice("T182_C004_Id");
/*mysql_query("UPDATE T182 SET
                T182_Pedido =  IF(T182_Tipo_Documento = 'T007', 
                (SELECT T007_T005_Id
                    FROM T007
                  WHERE T007_Id = T182_T007_Id and T007_C004_Id = T182_C004_Id 
                LIMIT 1),
                T182_Pedido),
    T182_T007_Id = IF(T182_Tipo_Documento = 'T005',
                (SELECT T005_T007_Id
                  FROM T005
                  WHERE T005_Id = T182_Pedido and T005_C004_Id = T182_C004_Id 
                  LIMIT 1),
                  T182_T007_Id)");
*/

// Ligação com a t264, para colocar fase gs
$cClausula = <<<EOT
create table if not exists T264A (
  T264A_Id INT(10) PRIMARY KEY AUTO_INCREMENT,
  T264A_T264_Id int,
  T264A_Fase varchar(30)
)
ENGINE=InnoDB 
DEFAULT 
CHARSET=utf8;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

Criar_Indice('T264A_T264_Id','UNIQUE');
Criar_Campo('T182_Campos','JSON');
Criar_Campo('T182_NF_Devolucao_Fornecedor','varchar(9)');
Criar_Campo('T182_NF_Refatura_Cliente','varchar(9)');
Criar_Campo("T182_Data_Retorno_Cliente","date");
Criar_Indice('D070_C004_Id');
Criar_Campo("D145_Flag_Destino_Produto","varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T218_Flag_Destino_Produto","varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T219_Flag_Destino_Produto","varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T224_Flag_Destino_Produto","varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("T225_Flag_Destino_Produto","varchar(2) DEFAULT ''");
Criar_Campo("T014A_D006_Id_Info","text");
Criar_Indice("T013_Chave_Acesso_Nfe");
Criar_Campo("D001A_Flag_Marcado", "enum('S','N') default'N'");
Criar_Campo("D024_Flag_Destino_Produto","varchar(2)");
Criar_Campo("T015_Data_Previsao_Pagamento", "date default '0000-00-00'");
Criar_Campo("T085_Tipo_Documento_Originario", "char(2)");
Criar_Campo("T085_Descricao_Documento", "varchar(100)");
Criar_Campo("T085_Data_Emissao", "date default '0000-00-00'");
Criar_Campo("T284_Chave_De_Acesso_CTe", "char(44)");
Criar_Campo("T276_Quantidade_CTe", "int(6)");
Criar_Campo("T276_Tipo_Carga", "char(2)");
Criar_Campo("T276_Descricao_Produto_Predominante", "varchar(120)");
Criar_Campo("T276_EAN_Produto_Predominante", "varchar(14)");
Criar_Campo("T276_NCM_Produto_Predominante", "varchar(8)");
Criar_Campo("T276_CEP_Carregamento", "varchar(9)");
Criar_Campo("T276_CEP_Descarregamento", "varchar(9)");
Criar_Indice("T284_Chave_De_Acesso_CTe");
Criar_Campo("T007A_Flag_Contingencia_Offline", "enum('N','S') default 'N'");
Criar_Indice("T007A_Flag_Contingencia_Offline");
Criar_Campo("T007A_Data_Entrada_Contingencia", "datetime");
Criar_Campo("T007A_Chave_Acesso_Contingencia", "varchar(44) DEFAULT NULL");
Criar_Campo("T007A_Data_Prazo_Transporte", "date");

// --- Fim Gabriel Carvalho

// --- Vitor Lacerda
Criar_Campo("C007_Tema_Layout", "varchar(30) not null");
// --- Fim Vitor Lacerda

// --- Beatriz Garcia
Criar_Campo("T312_Flag_Reversao", "enum('N','S') default 'N'");
// --- Fim Beatriz Garcia

Criar_Campo("T014A_D014_Id","int(10) unsigned");
Criar_Campo("T014A_Ficha_Conteudo_Importacao","varchar(40)");
Criar_Campo("T008A_D014_Id","int(10) unsigned");

Alterar_Tipo_Campo("T008_Valor_IPI","DECIMAL(14,2)");
Alterar_Tipo_Campo("T038_Nosso_Numero","varchar(25) DEFAULT NULL");
Alterar_Tipo_Campo("D007_Codigo_Cedente","varchar(30) DEFAULT ''");
Alterar_Tipo_Campo("T219_Flag_Cancelado","varchar(1) DEFAULT 'N'");
Alterar_Tipo_Campo("T003_Nome_Cliente","varchar(100)");

Alterar_Tipo_Campo("D009_Meses_MMF","int(5) default 6");
//Alterar_Tipo_Campo("D009_Meses_Compra","int(5) default 1");

Alterar_Tipo_Campo("T218_Nome_Cliente","VARCHAR(60)");
Alterar_Tipo_Campo("T218_Nome_Transportadora","VARCHAR(60)");
Alterar_Tipo_Campo("T004_Quantidade_Confirmada","decimal(10,3)");

Alterar_Tipo_Campo("T005_Nome_Status","char(30) NOT NULL DEFAULT 'Pendente'");

Criar_Indice('T007_Flag_Impresso');
Criar_Indice('D140_Nome_Md5', 'UNIQUE');
Criar_Indice('D001_Codigo_Barras', 'UNIQUE');
Criar_Indice('N001_Uniqid_Mensagem', 'UNIQUE');
Criar_Indice('N001_Flag_Excluido');
Criar_Indice('T015_T075_Id');
Criar_Indice('T261_T006_Id');
Criar_Indice('T262_T006_Id');
Criar_Indice('T013_Data_Emissao');
Criar_Indice('T005_Flag_Status_Liberacao');
Criar_Indice('T005_Flag_Notificacao_Liberacao');
Criar_Indice('D069_Data_Hora_Ultima_Alteracao');
Criar_Indice('D069_C007_Id_Ultima_Alteracao');
Criar_Indice('D160_D161_Id');
Criar_Indice('T218_Grupo');
Criar_Indice('T245_Hash');
Criar_Indice('T245_Tipo_Log');
Criar_Indice('T006_T225_Id');
Criar_Indice('T103_T002_Id');
Criar_Indice('T007_Flag_Imprimir_Romaneio');

//Ricardo - Criar_Indice() - Begin
Criar_Indice('C004_D173_Id');
//Ricardo - Criar_Indice() - End

Alterar_Tipo_Campo("T002_Numero_Documento","TEXT");
//Alterar_Tipo_Campo("T002_Numero_Duplicata","TEXT");
Alterar_Tipo_Campo("T015_Numero_Documento","TEXT");
Alterar_Tipo_Campo("T015_Numero_Duplicata","TEXT");

Alterar_Tipo_Campo("D009_Valor_Custo_Unitario","decimal(15,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("D009_Valor_Custo_Medio_Unitario","decimal(15,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("D006_Codigo_CFOP_Contra_Partida","varchar(255)");
Alterar_Tipo_Campo("D006_Descricao","varchar(60)");
Alterar_Tipo_Campo("T225_Quantidade","decimal(15,5) DEFAULT NULL");
Alterar_Tipo_Campo("D024_Flag_Lista_Preco","varchar(1) DEFAULT '1'");

Alterar_Tipo_Campo("T004_Flag_Origem_Produto","enum('0','1','2','3','4','5','6','7','8')");
Alterar_Tipo_Campo("T006_Flag_Origem_Produto","enum('0','1','2','3','4','5','6','7','8')");
Alterar_Tipo_Campo("T219_Flag_Origem_Produto","enum('0','1','2','3','4','5','6','7','8')");
Alterar_Tipo_Campo("T225_Flag_Origem_Produto","enum('0','1','2','3','4','5','6','7','8')");

Alterar_Tipo_Campo("D060_Descricao_Grupo","varchar(70)");
Alterar_Tipo_Campo("D060_Id","int(10) auto_increment");


Alterar_Tipo_Campo("T003_Flag_Perdido","varchar(1) DEFAULT 'N'");
Alterar_Tipo_Campo("T225_Flag_Autorizado","varchar(1) DEFAULT 'N'");
Alterar_Tipo_Campo("T013_Data_Emissao","datetime");
Alterar_Tipo_Campo("D001_Cubagem_Unitaria","decimal(10,6)");
Alterar_Tipo_Campo("T001_Valor_Custo_Unitario","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Valor_Custo_Medio_Unitario","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Valor_Preco_Unitario","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Percentual_ST","decimal(10,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T075_Peso_Bruto", "decimal(10,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Peso_Liquido", "decimal(10,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_Frete_Incluso", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_THC", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_Imposto_Importacao", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_PIS", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_SISCOMEX", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_Frete", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_Despesa", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_ICMS", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_IPI", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_COFINS", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_AFRMM", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("T075_Valor_Seguro", "decimal(14,4) DEFAULT '0.0000'");
Alterar_Tipo_Campo("D027_Portador", "VARCHAR(35) NOT NULL");
Alterar_Tipo_Campo("T007_Flag_Finalidade_NFe","enum('1','2','3','4','5','6') DEFAULT '1'");
Alterar_Tipo_Campo("T006_Quantidade_Separacao","decimal(12,5)");
Alterar_Tipo_Campo("C004_Parcelas_Isentas_Juros","int(10) DEFAULT 99");
Alterar_Tipo_Campo("T013_Nome_Transportadora","varchar(60)");
Alterar_Tipo_Campo("C004_Bairro","varchar(40)");
Alterar_Tipo_Campo("T055_Descricao_Produto","varchar(200)");
Alterar_Tipo_Campo("T001_Valor_ICMS","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Valor_IPI","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Valor_ST","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Valor_PIS","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T001_Valor_COFINS","decimal(14,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("D007_Tipo_Cobranca","varchar(30)");
Alterar_Tipo_Campo("T004_Valor_Preco_Bruto","decimal(15,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T006_Valor_Preco_Bruto","decimal(15,5) DEFAULT '0.00000'");
Alterar_Tipo_Campo("T015_Nome_Cliente ","varchar(100)");
Alterar_Tipo_Campo("T234_Descricao","text");
Alterar_Tipo_Campo("C004_Logradouro","varchar(40)");
Alterar_Tipo_Campo("T168_Valor","decimal(14,4)");
Alterar_Tipo_Campo("D009_Flag_Custo_Utilizado","varchar(2) DEFAULT ''");
Alterar_Tipo_Campo("N001_Flag_Excluido","varchar(1) DEFAULT 'N'");
Alterar_Tipo_Campo("T019_Referente","varchar(100)");
Alterar_Tipo_Campo("T265_Descricao", "varchar(300)");
Alterar_Tipo_Campo("D082_Marca", "varchar(30) DEFAULT ''");
Alterar_Tipo_Campo("D074_Quantidade", "decimal(15,6)");
Alterar_Tipo_Campo("T261_Quantidade", "decimal(15,6)");
Alterar_Tipo_Campo("T263_Quantidade", "decimal(15,6)");
Alterar_Tipo_Campo("T264_Quantidade", "decimal(15,6)");
Alterar_Tipo_Campo("T004_Valor_Frete_Unitario", "decimal(20,10) DEFAULT '0.0000000000'");
Alterar_Tipo_Campo("T004_Valor_Frete_Total", "decimal(20,10) DEFAULT '0.0000000000'");
Alterar_Tipo_Campo("T005_Id_Pedido_Externo", "varchar(32) NULL");
Alterar_Tipo_Campo("T007_Flag_Travar_Nota", "varchar(1) DEFAULT 'N'");

//Ricardo - Alterar_Campo() - Begin
Alter_Query("ALTER TABLE T284 CHANGE T284_Cidade_Descarregamento_Codigo T284_Cidade_Descarregamento_Codigo_IBGE int(7);");
//Ricardo - Alterar_Campo() - End

//Ricardo - Alterar_Tipo_Campo() - Begin
Alterar_Tipo_Campo("T004_Numero_Pedido_Compra", "varchar(60) DEFAULT ''");
Alterar_Tipo_Campo("T006_Numero_Pedido_Compra", "varchar(60) DEFAULT ''");
Alterar_Tipo_Campo("T008_Numero_Pedido_Compra", "varchar(60) DEFAULT ''");
Alterar_Tipo_Campo("T278_Nome", "varchar(100)");
Alterar_Tipo_Campo("T015_Valor_Cambio_Moeda", "decimal(10,6)");
Alterar_Tipo_Campo("T015_Valor_Duplicata_Moeda", "decimal(20,6)");

Alterar_Tipo_Campo("T004_Valor_Desconto_Unitario", "decimal(20,10)");
Alterar_Tipo_Campo("T004_Percentual_Desconto", "decimal(20,10)");
Alterar_Tipo_Campo("T004_Valor_Total_Desconto", "decimal(20,2)");

Alterar_Tipo_Campo("T006_Percentual_Desconto", "decimal(20,10)");
//Ricardo - Alterar_Tipo_Campo() - End

Criar_Campo("T255A_Liberado_Producao", "enum('N','S') DEFAULT 'N'");

/************************** CARLOS CARVALHO ***********************************************/ 
Criar_Campo("T261A_T256_Id","int(10)");
Criar_Campo("D001A_Flag_Caracteristica","enum('N','S') DEFAULT 'N'");
Criar_Campo("D001A_Flag_Disponivel_Venda","enum('N','S') DEFAULT 'S'");
Criar_Campo("T255A_Flag_Cancelamento","varchar(1)");
Criar_Campo("T255A_Observacao","text");
Criar_Campo("T255A_Data_Cancelamento","date DEFAULT '0000-00-00'");
Criar_Campo("T264A_Observacao","text");
Criar_Campo("D074A_Data_Hora_Alteracao","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("D075A_Data_Hora_Alteracao","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T219A_Prazo_Faturamento","date DEFAULT '0000-00-00'");
Criar_Campo("T225A_Prazo_Faturamento","date DEFAULT '0000-00-00'");
Criar_Campo("T224A_Data_Hora_Email","datetime DEFAULT '0000-00-00 00:00:00'");
Criar_Campo("T003A_D024_Id_Cliente_Proposta","int(10)");
Criar_Campo("T003A_T003_Id_Pai","int(10)");
Criar_Campo("T003A_Status_Revisao","enum('P','C') DEFAULT 'P'");
Criar_Campo("T003A_Numero_Revisao","int(10) DEFAULT 1");
Criar_Campo("T019A_Credito_Parcial","decimal(14,2)");
Criar_Campo("T019A_Saldo","decimal(14,2)");
Criar_Campo("T004A_SMT006_Id","int");
Criar_Campo("T004A_Custo_Processo","decimal(14,2)");
Criar_Campo("D001A_Area_Produto","decimal(14,4)");
Criar_Campo("T224A_Flag_Importacao","enum('N','S') DEFAULT 'N'");
Criar_Campo("T224A_Numero_Processo_Importacao","varchar(20)");
Criar_Campo("T224A_Dolar_Cambial","decimal (10,3)");
Criar_Campo("T224A_D024_Id","int(10)");
Criar_Campo("T225A_Valor_Dolar_Unitario","decimal (10,3)");
Criar_Campo("T225A_Valor_Dolar_Total","decimal (10,3)");
Criar_Campo("T019A_Flag_Saldo","enum('N','S') DEFAULT 'N'");
Criar_Campo("T218A_Flag_Desconto","enum('R','P')"); // R -> Desconto em reais. P -> Desconto em porcentagem
Criar_Campo("T218A_Flag_Desconto_Tela","enum('R','P')"); // R -> Desconto em reais. P -> Desconto em porcentagem
Criar_Campo("T218A_Flag_Mostrar_Desconto","enum('S','N') DEFAULT 'S'"); // S -> Mostra o desconto na aba Totais. N -> Não mostra o desconto.
Criar_Campo("T218A_Desconto_Cotacao","decimal(14,2)"); // Campo para armazenar o desconto da cotação online.
Criar_Campo("T256A_Data_Inicio","date DEFAULT '0000-00-00'");
Criar_Campo("T006A_Flag_Notificacao","enum('N','S') DEFAULT 'N'");
Criar_Campo("T006A_Flag_Producao_Emergencia","enum('N','S') DEFAULT 'S' COMMENT 'Se estiver em SIM, manda o item do pedido para a produção de emergência.'");
Criar_Campo("T004A_Flag_Notificacao","enum('N','S') DEFAULT 'N'");
Criar_Campo("T004A_Observacao_Interna","varchar(250) DEFAULT ''");
Criar_Campo("T004A_Codigo_Obra","varchar(100) DEFAULT ''");
Criar_Campo("T006A_Codigo_Obra","varchar(100) DEFAULT ''");
Criar_Campo("T255A_Flag_Baixa_Automatica", "enum('N','S') DEFAULT 'N'"); 
Criar_Campo("D024A_Flag_Certificado_Qualidade","enum('S','N') DEFAULT 'N'");
/*****************************************************************************************/ 

//Matheus
Criar_Campo("D022A_Flag_Ativo","varchar(1) DEFAULT 'S'");
//

//Osni

//Alterações em CADASTROS > CLIENTES/FORNECEDORES > EDITAR FORNECEDOR > TAB ENDEREÇOS

Alterar_Tipo_Campo("D148_Bairro","varchar(50) DEFAULT ''");
Alterar_Tipo_Campo("D148_Email","varchar(100) DEFAULT ''");
Alterar_Tipo_Campo("D148_Rua","varchar(80) DEFAULT ''");
Alterar_Tipo_Campo("T289_T014_Id", "int(10)");
Alterar_Tipo_Campo("T289_D009_Id", "int(10)");
Alterar_Tipo_Campo("T290_T008_Id", "int(10)");
Alterar_Tipo_Campo("T290_D009_Id", "int(10)");
Alterar_Tipo_Campo("T290_T289_Id",  "int(10)");

//Alterações em ESTOQUE > EXPEDIÇÃO DE MERCADORIAS > GERAR NOTA FISCAL

//--------------------------------------------------------------------------------------------------------
// Data: 31/08/2018
// Programador: OSNI
// Método: atualizacaoBase
// Chamado: 33004
// Descricao: Criado novo campo para definir se será inserido o ST na primeira parcela
// --------------------------------------------------------------------------------------------------------

Criar_Campo("D024_Cobrar_ST_Primeira_Parcela", "char(1) DEFAULT 'N'");
Criar_Campo("T290_Quantidade_Saldo", "decimal (10,3)");
Criar_Campo("T289_Data_Lancamento", "date");
Criar_Campo("T289_Valor_Base_ICMS_Substituicao_Unitario", "decimal(20,10)");
Criar_Campo("T289_Valor_ICMS_Substituicao_Unitario", "decimal(20,10)");
Criar_Campo("T290_Valor_Base_ICMS_Substituicao", "decimal(20,2)");
Criar_Campo("T290_Valor_ICMS_Substituicao", "decimal(20,2)");
Criar_Campo("T290_Data_Lancamento", "date");
Criar_Campo("D137_ST_Retido","char(1) DEFAULT ''");

Criar_Indice("T289_D009_Id");
Criar_Indice("T289_T014_Id");
Criar_Indice("T290_T008_Id");
Criar_Indice("T290_D009_Id");
Criar_Indice("T290_T289_Id");

// --------------------------------------------------------------------------------------------------------
// FIM - OSNI - 33004 - atualizacaoBase
// --------------------------------------------------------------------------------------------------------

// Atualização de triggers/procedures/functions
include('scripts/atualizacaoBase-rotinas-lista.php');

//Ricardo - adicionarConfGlobal() - End

//UNITY ARAMIS

// Campos adicionais portador a receber
Criar_Campo("D027_Flag_Ativo","ENUM('S','N') DEFAULT 'S'");
Criar_Campo("D027_Flag_Exibe_CRM","ENUM('S','N') DEFAULT 'S'");

Criar_Campo("D001A_Enviar_Nuvemshop","enum('N','S') DEFAULT 'N'");
Criar_Campo("D001A_Nuvemshop_Id","varchar(15) DEFAULT ''");
Criar_Campo("D001A_Nuvemshop_Retorno","text");

Criar_Campo("D001A_Produto_NS_Id","varchar(15) DEFAULT ''");
Criar_Campo("D001A_Variacao_NS_Id","varchar(15) DEFAULT ''");

Criar_Campo("D001A_Titulo_Nuvemshop","varchar(200) DEFAULT ''");
Criar_Campo("D001A_Descricao_Nuvemshop","text DEFAULT ''");

Criar_Campo("D001A_Preco_Nuvemshop","decimal(14,5) DEFAULT 0.00");

Criar_Campo("D001A_Log_Erro","text DEFAULT ''");

// Tipos de Zincagem
// BR - ZINCADO BRANCO
// AM - ZINCADO AMARELO
// PT - ZINCADO PRETO
// AZ - ZINCADO AZUL
// FG - ZINCADO FOGO
// AC - ZINCADO ACIDO
Criar_Campo("D001A_Tipo_Zincagem", "varchar(2) default ''");

// Campo criado para poder separar o plano de conta por empresa 
Criar_Campo("D014_Empresas","varchar(20) NOT NULL");

// Campo criado para o APP Entrega Parcial
Criar_Campo("T006A_Quantidade_Entregue","decimal(12,6) default '0.00000'");

// Campo criado para a integração com o Bitrix24
Criar_Campo("D024A_Bitrix_Id","varchar(15) DEFAULT ''");
Criar_Campo("T003A_Bitrix_Negocio_Id","varchar(15) DEFAULT ''");

// Extensão da tabela C007 - Usuarios
$C007aCreate  = " CREATE TABLE IF NOT EXISTS C007A ( ";
$C007aCreate .=   " C007A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$C007aCreate .=   " C007A_C007_Id INT(10) NOT NULL, ";
$C007aCreate .=   " C007A_Bitrix_Id varchar(15) DEFAULT '' COMMENT 'Guarda o ID de usuário do Bitrix24', ";
$C007aCreate .=   " UNIQUE KEY C007A_C007_Id_IX (C007A_C007_Id) ";
$C007aCreate .= " ) ";
$C007aCreate .= " ENGINE=InnoDB ";
$C007aCreate .= " COMMENT='Extensão da tabela C007 - Usuarios' ";
$C007aCreate .= " DEFAULT CHARSET=utf8; ";

$C007aSql   = mysql_query($C007aCreate);
$C007aError = mysql_error();
if (!empty($C007aError)) {
  echo "$C007aCreate<hr>$C007aError";
}

// Extensão da tabela D042 - Montagem
$C007aCreate  = " CREATE TABLE IF NOT EXISTS D042A ( ";
$C007aCreate .=   " D042A_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$C007aCreate .=   " D042A_D042_Id INT(10) NOT NULL, ";
$C007aCreate .=   " D042A_Variacao_Id_1 varchar(1) DEFAULT '' COMMENT 'Guarda o ID da Variacao', ";
$C007aCreate .=   " D042A_Valor_Variacao_Id_1 varchar(1) DEFAULT '' COMMENT 'Guarda o ID do Valor da Variacao', ";
$C007aCreate .=   " D042A_Variacao_Id_2 varchar(1) DEFAULT '' COMMENT 'Guarda o ID da Variacao', ";
$C007aCreate .=   " D042A_Valor_Variacao_Id_2 varchar(1) DEFAULT '' COMMENT 'Guarda o ID do Valor da Variacao', ";
$C007aCreate .=   " UNIQUE KEY D042A_D042_Id_IX (D042A_D042_Id) ";
$C007aCreate .= " ) ";
$C007aCreate .= " ENGINE=InnoDB ";
$C007aCreate .= " COMMENT='Extensão da tabela D042 - Montagem' ";
$C007aCreate .= " DEFAULT CHARSET=utf8; ";

$C007aSql   = mysql_query($C007aCreate);
$C007aError = mysql_error();
if (!empty($C007aError)) {
  echo "$C007aCreate<hr>$C007aError";
}

// Criação da tabela D208 - Categorias de Produto
$D208Create  = " CREATE TABLE IF NOT EXISTS D208 ( ";
$D208Create .=   " D208_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D208Create .=   " D208_Nome text COMMENT 'Nome da Categoria', ";
$D208Create .=   " D208_Id_Pai INT(10) COMMENT 'ID Categoria Pai' ";
$D208Create .= " ) ";
$D208Create .= " ENGINE=InnoDB ";
$D208Create .= " COMMENT='Criação da tabela D208 - Categorias de Produto' ";
$D208Create .= " DEFAULT CHARSET=utf8; ";

$D208Sql   = mysql_query($D208Create);
$D208Error = mysql_error();
if (!empty($D208Error)) {
  echo "$D208Create<hr>$D208Error";
}

// Criação da tabela D209 - Categorias Ecommerce
$D209Create  = " CREATE TABLE IF NOT EXISTS D209 ( ";
$D209Create .=   " D209_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D209Create .=   " D209_Nome text COMMENT 'Nome da Categoria', ";
$D209Create .=   " D209_CE_ID varchar(15) COMMENT 'ID Categoria Ecommerce', ";
$D208Create .=   " D209_CE_Pai_Id varchar(15) COMMENT 'ID Categoria Pai Ecommerce', ";
$D209Create .=   " D209_D208_Id INT(10) COMMENT 'ID Categoria ERP', ";
$D209Create .=   " D209_Ecommerce_Id INT(10) COMMENT 'ID Ecommerce ERP' ";
$D209Create .= " ) ";
$D209Create .= " ENGINE=InnoDB ";
$D209Create .= " COMMENT='Criação da tabela D209 - Categorias Ecommerce' ";
$D209Create .= " DEFAULT CHARSET=utf8; ";

$D209Sql   = mysql_query($D209Create);
$D209Error = mysql_error();
if (!empty($D209Error)) {
  echo "$D209Create<hr>$D209Error";
}

// Lista de Ecommerce
// 1 - Magento
// 2 - Tray
// 3 - NuvemShop
// 4 - Mercado Livre
// 5 - Shopee
// 6 - Amazon
// 7 - Magalu

// Criação da tabela D210 - Tabela de Anuncios
$D210Create  = " CREATE TABLE IF NOT EXISTS D210 ( ";
$D210Create .=   " D210_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D210Create .=   " D210_Titulo text COMMENT 'Titulo do Anuncio', ";
$D210Create .=   " D210_D001_Id varchar(15) COMMENT 'ID do Produto', ";
$D210Create .=   " D210_Descricao text COMMENT 'Descricao do Anuncio', ";
$D210Create .=   " D210_Preco decimal(20,10) COMMENT 'Preco do Anuncio', ";
$D210Create .=   " D210_Altura decimal(19,6) COMMENT 'Altura do Anuncio', ";
$D210Create .=   " D210_Largura decimal(19,6) COMMENT 'Largura do Anuncio', ";
$D210Create .=   " D210_Comprimento decimal(19,6) COMMENT 'Comprimento do Anuncio', ";
$D210Create .=   " D210_Peso decimal(19,6) COMMENT 'Peso do Anuncio' ";
$D210Create .= " ) ";
$D210Create .= " ENGINE=InnoDB ";
$D210Create .= " COMMENT='Criação da tabela D210 - Tabela de Anuncios' ";
$D210Create .= " DEFAULT CHARSET=utf8; ";

$D210Sql   = mysql_query($D210Create);
$D210Error = mysql_error();
if (!empty($D210Error)) {
  echo "$D210Create<hr>$D210Error";
}

// Criação da tabela D211 - Tabela de Anuncios Ecommerce
$D211Create  = " CREATE TABLE IF NOT EXISTS D211 ( ";
$D211Create .=   " D211_Id INT(10) PRIMARY KEY AUTO_INCREMENT, ";
$D211Create .=   " D211_D010_ID varchar(15) COMMENT 'ID do Anuncio', ";
$D211Create .=   " D211_Tipo_Anuncio varchar(20) COMMENT 'Tipo do Anuncio', ";
$D211Create .=   " D211_Tipo_Frete varchar(20) COMMENT 'Tipo do Frete' ";
$D211Create .= " ) ";
$D211Create .= " ENGINE=InnoDB ";
$D211Create .= " COMMENT='Criação da tabela D211 - Tabela de Anuncios Ecommerce' ";
$D211Create .= " DEFAULT CHARSET=utf8; ";

$D211Sql   = mysql_query($D211Create);
$D211Error = mysql_error();
if (!empty($D211Error)) {
  echo "$D211Create<hr>$D211Error";
}

// Campo criado para poder identificar qual o ecommerce que o produto está vinculado
Criar_Campo("D211_Ecommerce_Id","INT(10)");
Criar_Campo("T005A_Ecommerce_Id","INT(10)");
Criar_Campo("T005A_Pedido_Ecommerce_Id","varchar(30) DEFAULT ''");


// Campo criado para guardar o ID do Anuncio do Ecommerce
Criar_Campo("D211_Anuncio_Ecommerce_Id","varchar(15) DEFAULT ''");


// CRIACAO DA TABELA T015A
$cClausula  = <<<EOT
create table if not exists T015A (
    T015A_Id                              int(10) unsigned auto_increment,
    T015A_T015_Id                         int(10),
    T015A_Credita_PIS_COFINS              varchar(1) DEFAULT 'N',
    PRIMARY KEY (T015A_Id),
    UNIQUE KEY T015A_T015_Id_IX (T015A_T015_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error(); 

if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}

Criar_Campo("T005A_Valor_Comissao_Ecommerce","DECIMAL(14,2) DEFAULT 0.00");
Criar_Campo("T005A_Status_Order_Ecommerce","varchar(15) DEFAULT ''");
Criar_Campo("T005A_Status_Shipping_Ecommerce","varchar(15) DEFAULT ''");



// FIM UNITY ARAMIS


//Atualização da tabela de moedas
//Tabela original do DUMP de dados possui apenas 3 moedas por padrão
$sqlD044 = mysql_query("SELECT D044_Id FROM D044");
if(mysql_num_rows($sqlD044) <= 3){
  mysql_query("TRUNCATE D044");
  mysql_query("INSERT INTO D044 (D044_Moeda, D044_Simbolo) VALUES ('REAL', 'R$')");
  mysql_query("INSERT INTO D044 (D044_Moeda, D044_Simbolo) VALUES ('DOLAR', '$')");
  mysql_query("INSERT INTO D044 (D044_Moeda, D044_Simbolo) VALUES ('EURO', '€')");
  mysql_query("INSERT INTO D044 (D044_Moeda, D044_Simbolo) VALUES ('YUAN', '¥')");
  mysql_query("INSERT INTO D044 (D044_Moeda, D044_Simbolo) VALUES ('LIBRA', '£')");
}

$D020 = mysql_query("SHOW COLUMNS FROM D020 LIKE 'D020_Codigo_Cidade_EBS'");
if(mysql_num_rows($D020)==0){
    Criar_Campo("D020_Codigo_Cidade_EBS", "varchar(6) DEFAULT ''");
    require_once('scripts/atualizaD020.php');
}

$T008 = mysql_query("SHOW COLUMNS FROM T008 LIKE 'T008_Valor_Preco_Original_Bruto'");
if(mysql_num_rows($T008) > 0){
  mysqli_query("ALTER TABLE T008 DROP COLUMN T008_Valor_Preco_Original_Bruto;");
  mysqli_query("ALTER TABLE T008 DROP COLUMN T008_Percentual_Desconto_Bruto;");
  mysqli_query("ALTER TABLE T008 DROP COLUMN T008_Valor_Preco_Bruto;");
  mysqli_query("ALTER TABLE T008 DROP COLUMN T008_Valor_Desconto_Unitario_Bruto;");
}

//Popula tabela D053 se não houver registro para a empresa
$sqlC004 = mysql_query("SELECT C004_Id FROM C004 LEFT JOIN D053 ON D053_C004_Id=C004_Id WHERE D053_Id IS NULL GROUP BY C004_Id");
while($rowC004 = mysql_fetch_array($sqlC004)){
  mysql_query("INSERT INTO D053 (D053_C004_Id, D053_D018_Id, D053_UF) SELECT '{$rowC004['C004_Id']}', D018_Id, D018_UF FROM D018 WHERE D018_Codigo_IBGE IS NOT NULL");
}


$sqlC004 = mysql_query("SELECT C004_Id FROM C004 WHERE C004_Data_Inicio_Sistema = '0000-00-00 00:00:00'");
while($rowC004 = mysql_fetch_array($sqlC004)){
  mysql_query("UPDATE C004 SET C004_Data_Inicio_Sistema = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -2 MONTH) WHERE C004_Id = '{$rowC004['C004_Id']}'");
}

mysql_query("UPDATE C004 SET C004_Banco_Atualizado = 'S'");
mysql_query("UPDATE C004 SET C004_Modulos = '0' WHERE C004_Modulos IS NULL OR C004_Modulos = ''");

//Update para imprimir as duplicatas na Danfe
mysql_query("UPDATE C020 SET C020_Utiliza_Modulo_Financeiro='S'");

include_once('scripts/D165.php');
mysql_query("DROP TRIGGER IF EXISTS T006_estoque_real_alteracao;");

//Update T037_D007_Id
mysql_query("UPDATE T037 LEFT JOIN D007 ON (D007_Numero_Agencia = SUBSTRING(T037_Linha_Header, 27, 4) AND D007_Numero_Conta = SUBSTRING(T037_Linha_Header, 33, 5) AND D007_Digito_Verificador_Conta = SUBSTRING(T037_Linha_Header, 38, 1) AND T037_Codigo_Banco = '341') OR (D007_Numero_Convenio = TRIM(LEADING '0' FROM SUBSTRING(T037_Linha_Header, 27, 20)) AND T037_Codigo_Banco = '237') OR (D007_Numero_Agencia = SUBSTRING(T037_Linha_Header, 27, 4)  AND D007_Numero_Conta = TRIM(LEADING '0' FROM SUBSTRING(T037_Linha_Header, 32, 8))  AND D007_Digito_Verificador_Conta = SUBSTRING(T037_Linha_Header, 40, 1) AND T037_Codigo_Banco = '001') SET T037_D007_Id = D007_Id WHERE IFNULL(T037_D007_Id,0) <= 0");

include_once('scripts/insertCidadesEstados.php');

//Tabela de erros Sefaz
include_once('scripts/codigosErrosSefaz.php');

//Cadastra plano de contas padrão
include_once('scripts/planoContas.php');


//Ajuste configuração C031
mysql_query("UPDATE C031 SET C031_Valor_Padrao = '', C031_Valor = 'Helvetica' WHERE C031_Campo = 'base64FonteDANFE' AND C031_Valor != ''");
mysql_query("UPDATE C031 SET C031_Valor_Padrao = '', C031_Valor = '' WHERE C031_Campo = 'base64FonteDANFE' AND C031_Valor = ''");
mysql_query("UPDATE C031 SET C031_Valor_Padrao = 'S', C031_Valor = 'S' WHERE C031_Campo = 'obrigatorioFiltroTelaTransicao' AND C031_Valor != 'S'");

// Atualizando campo C004_Flag_Ativo
mysql_query("UPDATE C004 SET C004_Flag_Ativo='S' WHERE C004_Flag_Ativo IS NULL OR C004_Flag_Ativo='';");

echo "Atualização realizada com sucesso!";

mysql_query("UPDATE C020 SET C020_Versao_NFe='4.00'");

mysql_query("UPDATE C004 SET C004_Ambiente_MDFe='1'");

// Foi alteredo o tipo dos campos de int para varchar
mysql_query("UPDATE D137 SET D137_C004_Codigo_Regime_Tributario = '' WHERE D137_C004_Codigo_Regime_Tributario = '0'");
mysql_query("UPDATE D137 SET D137_D024_Flag_Contribuinte = '' WHERE D137_D024_Flag_Contribuinte = '0'");

/**
 * Esse script foi criado apenas para eurosul para popular a tabela D032 (Grupo) que estavam registrados na D014
 * Uma vez populado, não será mais necessário executar esse código!
 */

// $sqlD014 = "SELECT * FROM D014 WHERE D014_Flag_Plano='G'";
// $resultD014 = mysql_query($sqlD014);
// 
// while($linhaD014 = mysql_fetch_array($resultD014)){
	// $sqlD032 = "INSERT INTO D032 (D032_Descricao) VALUES ('{$linhaD014['D014_SubConta']}')";
	// $result = mysql_query($sqlD032);
	// $D032_Id = $g['mysqlLastId'];
// }
/* ATUALIZAÇÃO FIXAR
mysql_query("ALTER TABLE C004 CHANGE `C004_Observacao_Rodape_Orcamento` `C004_Observacao_Rodape_Orcamento` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
mysql_query("ALTER TABLE C004 CHANGE `C004_Observacao_Rodape_Pedido` `C004_Observacao_Rodape_Pedido` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
*
,
*/


// Verifica e popula tabela (D049) 
$D001=mysql_query("SELECT D001_Id FROM D001
                LEFT JOIN D049 ON D049_D001_Id=D001_Id
                    WHERE D049_Id IS NULL");
while ($mD001=mysql_fetch_array($D001)) {
      mysql_query("INSERT INTO D049 (D049_D001_Id) VALUES ('{$mD001['D001_Id']}')");
}

// Verifica e popula tabela (D009) 
$C004 = mysql_query("select C004_Id from C004");
while ($mC004 = mysql_fetch_array($C004)){
       $cSql_D049=mysql_query("SELECT D049_Id FROM D049
                            LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id='{$mC004['C004_Id']}'
                                WHERE D009_Id IS NULL");
       while ($mD049=mysql_fetch_array($cSql_D049)) {
              mysql_query("INSERT INTO D009 (D009_C004_Id, D009_D049_Id, D009_Data_Cadastro, D009_Flag_Ativo) VALUES ('{$mC004['C004_Id']}', '{$mD049['D049_Id']}', current_date(), 'S')");
       }
}

//Criação tabelas banco global
include('scripts/atualizacaoBaseGlobal.php');

//Atualização tabela dashboard
//include('scripts/D132_dump.php');

// Atualização IDE -> Tabelas
$g['modoDesenvolvedorApp'] = false;

include('scripts/atualizacaoBaseIde.php');

// Popula a tabela (D187) com os valores padrões de Tipo Produto
include('scripts/populaD187.php');


// Atualização de limite de caracteres para sub-grupos (Produtos) - Unity (Lucas) - Begin
Alterar_Tipo_Campo("D002_Descricao_Produto", "varchar(130)");
// Atualização de limite de caracteres para sub-grupos (Produtos) - Unity (Lucas) - End

// Atualização de limite de caracteres para grupos (Produtos) - Unity (Lucas) - Begin
Alterar_Tipo_Campo("D003_Nome_Linha", "varchar(130)");
// Atualização de limite de caracteres para grupos (Produtos) - Unity (Lucas) - End


/// GUIMEPA

Criar_Campo("D046_Taxa_Financeiro","float DEFAULT '0.00'");
Criar_Campo("D046_Taxa_Mercado_Eletronico","float DEFAULT '0.00'");

// Configuração Cyberlog
Criar_Campo("T005A_Data_Envio_WMS","datetime default '0000-00-00 00:00:00' COMMENT 'Data hora envio para o WMS'");
Criar_Campo("D001A_Qtde_Embalagem_Master","decimal(10,3) DEFAULT '0.000' COMMENT 'Quantidade de embalagem master'");

// Tabela logs Cyberlog para depuração de erros
$cClausula = <<<EOT
create table if not exists W001 (  
    W001_Id                           INT(10) UNSIGNED AUTO_INCREMENT,
    W001_T005_Id                      INT(10) DEFAULT NULL,
    W001_T007_Id                      INT(10) DEFAULT NULL,
    W001_T013_Id                      INT(10) DEFAULT NULL,
    W001_D001_Id                      INT(10) DEFAULT NULL,
    W001_D022_Id                      INT(10) DEFAULT NULL,
    W001_D024_Id                      INT(10) DEFAULT NULL,
    W001_Operacao                     VARCHAR(15),
    W001_Status                       VARCHAR(15),
    W001_Retorno                      TEXT DEFAULT NULL,
    W001_Data_Envio                   DATETIME DEFAULT NOW(),
    PRIMARY KEY (W001_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Tabela auxiliar para transportadoras D022
$cClausula  = <<<EOT
create table if not exists D022A (  
    D022A_Id                           INT(10) UNSIGNED AUTO_INCREMENT,
    D022A_D022_Id                      INT(10) UNSIGNED,
    D022A_Prioridade                   INT(10) UNSIGNED DEFAULT 0,
    PRIMARY KEY (D022A_Id),
    UNIQUE KEY IDX_D022A_D022_Id (D022A_D022_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// MDFe - T318 - Informações do pagamento do frete
$cClausula  = <<<EOT
create table if not exists T318 (  
    T318_Id                                 INT(10) UNSIGNED AUTO_INCREMENT,
    T318_T276_Id                            INT(10) UNSIGNED,
    T318_D024_Id                            INT(10) UNSIGNED,
    T318_Codigo_Banco                       varchar(5)        COMMENT 'Número do banco (codBanco | 45)',
    T318_Codigo_Agencia                     varchar(10)       COMMENT 'Número da agência bancária (codAgencia | 46)',
    T318_Valor_Contrato                     decimal(13,2)     COMMENT 'Valor Total do Contrato - 15 posições, sendo 13 inteiras e 2 casas decimais. (vContrato | 34)',
    T318_Indicador_Forma_Pagamento          varchar(1)        COMMENT 'Indicador da Forma de Pagamento:0-Pagamento à Vista;1-Pagamento à Prazo;  (indPag | 36)',
    T318_CIOT                               varchar(12)       COMMENT 'Código Identificador da Operação de Transporte (CIOT | infCIOT/CIOT)',
    T318_Chave_Pix                          text              COMMENT 'Chave PIX - Informar a chave PIX para recebimento do frete. Pode ser email, CPF/ CNPJ (somente numeros), Telefone com a seguinte formatação (+5599999999999) ou a chave aleatória gerada pela instituição. (PIX | 48)',
    T318_CNPJ_Instituicao_Pagamento_Frete   varchar(20)       COMMENT 'Número do CNPJ da Instituição de Pagamento Eletrônico do Frete - Informar os zeros não significativos. (CNPJIPEF | 47)',
    PRIMARY KEY (T318_Id),
    KEY IDX_T318_T276_Id (T318_T276_Id),
    KEY IDX_T318_D024_Id (T318_D024_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Compatibilidade para bases que já possuem a tabela T318 sem o campo CIOT
Criar_Campo("T318_CIOT", "varchar(12)");

// MDFe - T319 - Informações do pagamento do frete
$cClausula  = <<<EOT
    create table if not exists T319 (  
    T319_Id           INT(10) UNSIGNED auto_increment,
    T319_T318_Id      INT(10) UNSIGNED,
    T319_Tipo         varchar(2)      COMMENT 'Tipo do Componente (tpComp | 31)',
    T319_Valor        decimal(15,2)   COMMENT 'Valor do componente - 15 posições, sendo 13 inteiras e 2 casas decimais. (vComp | 32)',
    T319_Descricao    text            COMMENT 'Descrição do componente do tipo outros (xComp | 33)',
    PRIMARY KEY (T319_Id),
    KEY IDX_T319_T318_Id (T319_T318_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// MDFe - T320 - Informações do pagamento a prazo
$cClausula  = <<<EOT
    create table if not exists T320 (  
    T320_Id                 INT(10) UNSIGNED auto_increment,
    T320_T318_Id            INT(10) UNSIGNED,
    T320_Numero_Parcela     varchar(3)      COMMENT 'Número da Parcela (nParcela | 40)',
    T320_Data_Vencimento    date            COMMENT 'Data de vencimento da Parcela (AAAAMM-DD) (dVenc | 41)',
    T320_Valor_Parcela      decimal(15,2)   COMMENT 'Valor da Parcela - 15 posições, sendo 13 inteiras e 2 casas decimais. (vParcela | 42)',
    PRIMARY KEY (T320_Id),
    KEY IDX_T320_T318_Id (T320_T318_Id)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
}

// Conferência livre do pedido antes do faturamento em lote na expedição
Criar_Campo("T005A_Conferido", "varchar(255) DEFAULT ''");

// Dados de exportação comercial do pedido (Commercial Invoice / Packing List)
Criar_Campo("T005A_Invoice_Payment_Terms", "varchar(255) DEFAULT ''");
Criar_Campo("T005A_Invoice_From", "varchar(255) DEFAULT ''");
Criar_Campo("T005A_Invoice_To", "varchar(255) DEFAULT ''");
Criar_Campo("T005A_Invoice_Number", "varchar(100) DEFAULT ''");
Criar_Campo("T005A_Invoice_Incoterms", "varchar(100) DEFAULT ''");
Criar_Campo("C004_Invoice_Our_Bank", "text");
// DANFE RTC - NT 2026.010 v1.00. Modelo e orientação são configurados separadamente.
Criar_Campo("C004_Tipo_DANFE", "char(1) NOT NULL DEFAULT 'V' COMMENT 'V=RTC retrato; H=RTC paisagem'");
Criar_Campo("C004_Flag_Usar_DANFE_RTC", "char(1) NOT NULL DEFAULT 'N' COMMENT 'N=Modelo atual; S=Usar DANFE RTC'");
Criar_Campo("T007A_Tipo_DANFE", "char(1) NOT NULL DEFAULT '' COMMENT 'Vazio=Empresa; A=Atual; V=RTC vertical; H=RTC horizontal'");
