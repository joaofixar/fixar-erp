<?php
namespace hardness;

echo "Limpando tabela D182 <br />";

mysql_query("truncate D182");

echo mysql_error();

echo "Apagando tabela D132 <br />";

mysql_query("DROP TABLE IF EXISTS `D132`");

echo mysql_error();

mysql_query("
CREATE TABLE `d132` (
  `D132_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D132_Descricao` varchar(70) NOT NULL,
  `D132_Salvo` blob NOT NULL,
  `D132_Filtro` blob NOT NULL,
  `D132_Grupo` varchar(30) NOT NULL,
  `D132_Flag_Exibir_Dashboard` varchar(1) DEFAULT 'S',
  `D132_Flag_Exibir_Alcada` varchar(1) DEFAULT '1',
  `D132_Grid_Width` int(5) DEFAULT '0',
  `D132_Tipo` int(2) DEFAULT '1',
  `D132_Grid_Tabela` longblob,
  `D132_DivId` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`D132_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
");

echo "Criando tabela D132 <br />";

echo mysql_error();

echo "Populando tabela D132 <br />";

$D132 = file_get_contents("scripts/D132.sql");

mysql_query($D132);

echo mysql_error();


