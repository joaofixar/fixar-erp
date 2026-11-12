<?php
namespace hardness;

set_time_limit(0);
ini_set('default_charset', 'utf-8');
require_once('bibliotecas/funcoesGerais.php');
require_once('scripts/atualizacaoBaseAuditoria-funcoes.php');

echo "<font style='font-weight:bold;font-size:16px;'>Iniciando o processo de Atualização Base da Auditoria.</font><hr>";


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

$cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
$cErro = mysql_error($g['conexaoBancoAuditoria']);

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
    exit;
}else{
  echo "<br> Tabela T162 criada ou já existente.";
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

$cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
$cErro = mysql_error($g['conexaoBancoAuditoria']);

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
    exit;
}else{
  echo "<br> Tabela T265 criada ou já existente.";
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

$cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
$cErro = mysql_error($g['conexaoBancoAuditoria']);

if (!empty($cErro)){
  echo "$cClausula<hr>$cErro";
    exit;
}else{
  echo "<br> Tabela T245 criada ou já existente.";
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
$cSql = mysqli_query($cClausula,$g['conexaoBancoAuditoria']);
$cErro = mysql_error($g['conexaoBancoAuditoria']);
if (!empty($cErro)){
    echo "$cClausula<hr>$cErro";
    exit;
}else{
  echo "<br> Tabela T213 criada ou já existente.";
}

Criar_Campo("T162_Registro_Id","int(10) unsigned");
Criar_Campo("T162_Registro","blob");
Criar_Campo("T162_IP","varchar(15)");
Criar_Indice('T162_C007_Id');
Criar_Indice('T162_Registro_Id');
Criar_Indice('T162_DataHora');
Criar_Indice('T162_Tabela');

Alterar_Tipo_Campo("T265_Descricao", "varchar(300)");

Criar_Campo("T245_Tipo_Log","int(10) unsigned");
Criar_Campo("T245_Teste_Criacao","int(10) unsigned");

Criar_Indice('T245_Hash');
Criar_Indice('T245_Tipo_Log');

echo "<br><br><hr><font style='font-weight:bold;font-size:16px;'>Finalizando processo de Atualização Base da Auditoria.</font>";

?>