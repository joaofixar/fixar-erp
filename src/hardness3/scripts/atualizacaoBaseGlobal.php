<?php
namespace hardness;

mysql_query("USE global;");

// Criação das tabelas 
$cClausula  = <<<EOT
CREATE  TABLE IF NOT EXISTS Q007 (
  Q007_Id INT(10) NOT NULL AUTO_INCREMENT ,
  Q007_Campo varchar(50),
  Q007_Valor varchar(50), 
  Q007_Observacao text,
  PRIMARY KEY (Q007_Id)
)
DEFAULT CHARSET=utf8
ENGINE = InnoDB;
EOT;

$cSql = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)){
	echo "$cClausula<hr>$cErro";
    exit;
}

// Adicionando registros na tabela Q007
insertQ007('habilitarSVC', '', 'Habilita/Desabilita o botão para enviar via SVC a nota fiscal para todas as empresas');

echo "<br>Criada tabela Q007 no banco Global! <br><br>";
mysql_query("USE {$confUsuario['dbDatabase']};");