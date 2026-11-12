<?php

// Cria ou inicia uma sessão
require "../include/sessao.inc";

$cConfirmado=$_GET['Confirmado'];
$cSenha=$_GET['Senha'];

if (($cConfirmado=='S') and ($cSenha!='LOCALHOST') and ($cSenha!=''))
{
    // abre uma conexão no servidor
    $con=@mysql_connect($host,'root',$Senha);
    $Error=mysql_error();
    if (!empty($Error)) {
        echo "<table border=0 width=100% height=60%>";
          echo "<tr>";
            echo "<td align=center>";
              echo "<table border=0 width=500 height=100 bgcolor=#EBE9E0 cellpadding=0 cellspacing=1  style='border: 1px solid;border-color:  white #B0B0B0 #B0B0B0 white;'>";
                echo "<tr>";
                  echo "<td align=center>";
                    echo "<img src='../imagens/gif/proibido.gif'>";
                  echo "</td>";
                  echo "<td align=left>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 24;font-weight:bold;'>Acesso negado</font>";
                    echo "<br>";
                    echo "<br>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 16;font-weight:bold;'>$Nome_Usuario</font>";
                    echo "<br>";
                    echo "<br>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 12;font-weight:normal; font-color:red'>$Error</font>";
                  echo "</td>";
                echo "</tr>";
              echo "</table>";
            echo "</td>";
          echo "</tr>";
        echo "</table>";
        exit;
    }

    $dbs=mysql_select_db("$Banco_Dados",$con) or die (mysql_error());
    
}
else
{
   // Conexão com o banco de dados
   require "../include/conexao.inc";
}

// Funções Genéricas em PHP
require "../func/funcoes_genericas.inc";

echo "<style>body { font-family:arial,verdana;font-size:12px;color:black; }</style>";

echo "<script>
function MostrarMensagem(Visivel)
{
    document.getElementById('divLoadingCenter').style.display=Visivel;
    document.getElementById('divLoadingCenter').style.zIndex = zCount++;
}
</script>";

set_time_limit(0);

echo "<body bgcolor='#AFC2CD'>";
            
if ($cConfirmado=='S')
{
   	mysql_query("SET FOREIGN_KEY_CHECKS=0;");
	mysql_query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
}

$cSql=@mysql_query("select * from D071");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D071` ( `D071_Id` int(11) NOT NULL auto_increment, `D071_Tipo_Portador` varchar(20) NOT NULL, PRIMARY KEY (`D071_Id`) ) ENGINE=InnoDb AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D071</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D080");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D080` ( `D080_Id` int(11) NOT NULL auto_increment, `D080_Turno` varchar(20) NOT NULL, `D080_Hora_Inicio` varchar(5) NOT NULL, `D080_Hora_Fim` varchar(5) NOT NULL, PRIMARY KEY (`D080_Id`) ) ENGINE=InnoDb AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D080</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D081");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D081` ( `D081_Id` int(11) NOT NULL auto_increment, `D081_D002_Id` int(11) NOT NULL, `D081_D080_Id` int(11) NOT NULL, PRIMARY KEY (`D081_Id`) ) ENGINE=InnoDb AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D081</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D082");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D082` ( `D082_Id` int(11) NOT NULL auto_increment,
                                             `D082_Marca` varchar(30),
                                             PRIMARY KEY (`D082_Id`) ) ENGINE=InnoDb AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D082</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from C024");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `C024` (
                                  `C024_Banco_Dados` varchar(10) NOT NULL,
                                  `C024_D024_Id` int(10) unsigned default 0,
                                  PRIMARY KEY  (`C024_Banco_Dados`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>C024</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from C026");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `C026` (
                                  `C026_Id` int(10) unsigned NOT NULL auto_increment,
                                  `C026_Razao_Social` varchar(40),
                                  `C026_Nome_Contador` varchar(40),
                                  `C026_Nome_Contato` varchar(40),
                                  `C026_Telefone` varchar(40),
                                  `C026_Observacao` text,
                                  `C026_Login` varchar(20),
                                  `C026_Senha` varchar(20),
                                  PRIMARY KEY  (`C026_Id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>C026</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T135");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T135` (
                                    `T135_Id` int(10) unsigned NOT NULL auto_increment,
                                    `T135_D024_Id` int(10) unsigned,
                                    `T135_Data_Inicio` date,
                                    `T135_Data_Termino` date,
                                    `T135_Valor_Contrato` decimal(10,2),
                                    `T135_Observacao` text,
                                    `T135_Tipo` varchar(2),
                                    PRIMARY KEY  (`T135_Id`)
                                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T135</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T136");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T136` (
                                    `T136_Id` int(10) unsigned NOT NULL auto_increment,
                                    `T136_Numero_Erro` int(3),
                                    `T136_Descricao_Erro` text,
                                    `T136_Procedimento` text,
                                    `T136_Flag_Refazer_Chave_Acesso` enum('S','N') default 'N',
                                    PRIMARY KEY  (`T136_Id`)
                                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T136</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T137");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T137` (
                                    `T137_Id` int(10) unsigned NOT NULL auto_increment,
                                    `T137_Mes` varchar(2),
                                    `T137_Ano` varchar(4),
                                    `T137_Data_Inicial` date,
                                    `T137_Data_Final` date,
                                    `T137_C007_Id_Vendedor` int(10),
                                    `T137_C007_Id_Supervisor` int(10),
                                    `T137_Valor_CMV` decimal(10,2),
                                    `T137_Valor_Vendas` decimal(10,2),
                                    `T137_Valor_Devolucoes` decimal(10,2),
                                    `T137_IPV` decimal(8,4),
                                    `T137_Valor_CMV_Equipe` decimal(10,2),
                                    `T137_Valor_Vendas_Equipe` decimal(10,2),
                                    `T137_Valor_Devolucoes_Equipe` decimal(10,2),
                                    `T137_IPV_Equipe` decimal(8,4),
                                    PRIMARY KEY  (`T137_Id`)
                                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T137</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from T138");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T138` (
                                    `T138_Id` int(10) unsigned NOT NULL auto_increment,
                                    `T138_T007_Id` int(10) unsigned NOT NULL,
                                    `T138_C004_Id` int(10) unsigned NOT NULL,                                    
                                    `T138_D009_Id` int(10) unsigned NOT NULL,
                                    `T138_Quantidade` decimal(12,5) DEFAULT '0',
                                    `T138_Valor_Preco_Unitario` decimal(20,5) DEFAULT '0',
                                    `T138_Flag_Documento` char(1) DEFAULT '0',
                                    `T138_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
                                    `T138_Observacao` varchar(200) DEFAULT NULL,
                                    PRIMARY KEY  (`T138_Id`),
                                    CONSTRAINT `FK_T138_C004_Id` FOREIGN KEY (`T138_C004_Id`) REFERENCES `C004` (`C004_Id`) ON DELETE RESTRICT,
                                    CONSTRAINT `FK_T138_D009_Id` FOREIGN KEY (`T138_D009_Id`) REFERENCES `D009` (`D009_Id`) ON DELETE RESTRICT,
                                    CONSTRAINT `FK_T138_T007_Id` FOREIGN KEY (`T138_T007_Id`) REFERENCES `T007` (`T007_Id`) ON DELETE CASCADE
                                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 comment='Tabela de trocas'";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T138</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from D088");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D088` (
                                  `D088_Id` int(10) unsigned NOT NULL auto_increment,
                                  `D088_Codigo_ST` int(2) zerofill default '00',
                                  `D088_Descricao` varchar(100),
                                  PRIMARY KEY  (`D088_Id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D088</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D089");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D089` (
                                  `D089_Id` int(10) unsigned NOT NULL auto_increment,
                                  `D089_D007_Id` int(10) unsigned,
                                  `D089_Numero_PDV` varchar(20),
                                  `D089_Flag_Imprime_Cupom` enum('S','N') default 'S',                                  
                                  PRIMARY KEY  (`D089_Id`)
                                ) ENGINE=InnoDB comment='Cadatro de PDV'";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D089</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T132");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T132` (
                                  `T132_Id` int(10) unsigned NOT NULL auto_increment,
                                  `T132_T007_Id` int(10) unsigned,
                                  `T132_Tipo`  enum('NF','CTe','NFe','CUPOM','NF_PRODUTOR') default 'NFe',
                                  `T132_Chave_Acesso` varchar(44),
                                  `T132_UF_IBGE` int(2) zerofill default '00',
                                  `T132_Ano_Mes` int(4) zerofill default '0000',
                                  `T132_Pessoa_Fisica_Juridica` enum('F','J') default 'J',
                                  `T132_Cnpj` varchar(14),
                                  `T132_Cpf` varchar(11),
                                  `T132_Inscricao_Estadual` varchar(14),
                                  `T132_Modelo` INT(2) ZEROFILL DEFAULT '01',
                                  `T132_Serie` int(3) default 0,
                                  `T132_Numero_Nota_Fiscal` int(9) default '0',
                                  `T132_Cupom_Numero_EFC` INT(3) DEFAULT 0,
                                  `T132_Cupom_Numero_COO` INT(6) DEFAULT 0,
                                  `T132_Cupom_Modelo` char(2),
                                  PRIMARY KEY  (`T132_Id`),
                                  INDEX T132_T007_Id(T132_T007_Id),
                                  FOREIGN KEY(T132_T007_Id) REFERENCES T007 (T007_Id) ON DELETE CASCADE
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }

   $mAlteracoes[]="Criar tabela: <b>T132</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D105");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D105` (
                                  `D105_Id` int(10) unsigned NOT NULL auto_increment,
                                  `D105_D024_Id` int(10) unsigned NOT NULL,
                                  `D105_Descricao_Fornecedor` varchar(50) NOT NULL,
                                  `D105_Descricao_Empresa` varchar(50) NOT NULL,
                                  PRIMARY KEY (`D105_Id`),
                                  INDEX D105_Id(D105_Id)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }

   $mAlteracoes[]="Criar tabela: <b>D105</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from C025");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `C025` (
                                  `C025_Id` int(10) unsigned NOT NULL auto_increment,
                                  `C025_D024_Id` varchar(10) NOT Null default 0,
                                  `C025_Banco_Dados` varchar(10),
                                  `C025_Login` varchar(20),
                                  `C025_Senha` varchar(10),
                                  `C025_Email` varchar(60),
                                  PRIMARY KEY  (`C025_Id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>C025</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from D083");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D083` (
                                  `D083_Id` int(10) unsigned NOT NULL auto_increment,
                                  `D083_D024_Id` int(10) unsigned default NULL,
                                  `D083_D001_Id` int(10) unsigned default NULL,
                                  `D083_Codigo_Barras` varchar(25) default NULL,
                                  `D083_Codigo_Produto_Fornecedor` varchar(30) default NULL,
                                  `D083_Descricao_Produto` varchar(60) default NULL,
                                  `D083_Data_Cadastro` date NOT NULL default '0000-00-00',
                                  `D083_Flag_Nacional_Importado` varchar(1) default NULL,
                                  `D083_Flag_Nao_Comprar` varchar(1) default 'N',
                                  `D083_Observacao_Compra` text,
                                  `D083_Quantidade_Minima_Compra` decimal(10,2) default NULL,
                                  PRIMARY KEY  (`D083_Id`),
                                  KEY `D083_Index_Produtos` (`D083_D001_Id`),
                                  KEY `D083_Index_Fornecedores` (`D083_D024_Id`),
                                  KEY `IDX_Codigo_Produto_Fornecedor` (`D083_Codigo_Produto_Fornecedor`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D083</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D086");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " create table D086 (
                                    D086_Id int(10) unsigned,
                                    D086_D009_Id int(10) unsigned,
                                    D086_D024_Id int(10) unsigned,
                                    D086_Percentual_Desconto decimal(10,4) default 0
                       ) ENGINE=InnoDB ; ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D086</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D084");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D084` (
                                    `D084_Id` int(10) unsigned NOT NULL auto_increment,
                                    `D084_Regiao` varchar(50) default NULL,
                                    `D084_Observacao` text,
                                    PRIMARY KEY  (`D084_Id`)
                                  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D084</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D085");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D085` (
                                    `D085_Id` int(10) unsigned NOT NULL auto_increment,
                                    `D085_D024_Id` int(10) unsigned,
                                    `D085_C007_Id` int(10) unsigned,
                                    `D085_Data` date default '0000-00-00',
                                    `D085_Mensagem` text,
                                    PRIMARY KEY  (`D085_Id`)
                                  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D084</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T128");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T128` (
       `T128_Id` int(10) unsigned NOT NULL auto_increment,
       `T128_D022_Id` int(10) unsigned default NULL,
       `T128_D024_Id` int(10) unsigned default NULL,
       `T128_D036_Id` int(10) unsigned default NULL,
       `T128_C007_Id` int(10) unsigned default NULL,
       `T128_C008_Id` int(10) unsigned default NULL,
       `T128_C004_Id` int(10) unsigned NOT NULL default '0',
       `T128_Numero` varchar(10) default NULL,
       `T128_Data_Emissao` date default NULL,
       `T128_Data_Previsao_Recebimento` date default NULL,
       `T128_Data_Previsao_Pagamento` date default NULL,
       `T128_Observacao` text,
       `T128_Prazo_Entrega` varchar(50) default NULL,
       `T128_Valor_Frete` decimal(10,2) default '0.00',
       `T128_Valor_Total_Custo` decimal(10,2) default '0.00',
       `T128_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
       `T128_Valor_Total_Base_ICMS` decimal(10,2) default '0.00',
       `T128_Aliquota_ICMS` decimal(5,2) default '0.00',
       `T128_Valor_Total_ICMS` decimal(10,2) default '0.00',
       `T128_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
       `T128_Valor_Total_Base_IPI` decimal(10,2) default '0.00',
       `T128_Aliquota_IPI` decimal(5,2) default '0.00',
       `T128_Valor_Total_IPI` decimal(10,2) default '0.00',
       `T128_Nome_Contato` varchar(40) default NULL,
       `T128_Fax` varchar(30) default NULL,
       `T128_Flag_Cancelada` varchar(1) default 'N',
       `T128_Codigo_Comprador` varchar(10) default NULL,
       `T128_Centro_Custo` varchar(10) NOT NULL,
       `T128_Prazo_Pagamento` varchar(50) default NULL,
       `T128_Valor_Total_Pendente` decimal(10,2) default '0.00',
       PRIMARY KEY (`T128_Id`),
       KEY `T128_IDX_USUARIOS` (`T128_C007_Id`),
       KEY `T128_IDX_EMPRESAS` (`T128_C004_Id`),
       KEY `T128_IDX_TIPO_PRODUTO` (`T128_C008_Id`),
       KEY `T128_FKIndex3` (`T128_D036_Id`),
       KEY `T128_FKIndex6` (`T128_D024_Id`),
       KEY `T128_FKIndex7` (`T128_D022_Id`),
       KEY `IDX_T128_Id` (`T128_Id`) ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T128</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T129");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T129` (
       `T129_Id` int(10) unsigned NOT NULL auto_increment,
       `T129_T059_Id` int(10) unsigned default NULL,
       `T129_D009_Id` int(10) unsigned default NULL,
       `T129_T128_Id` int(10) unsigned default NULL,
       `T129_C004_Id` int(10) unsigned default '0',
       `T129_D049_Id` int(10) unsigned default '0',
       `T129_Codigo_Produto` varchar(30) default NULL,
       `T129_Codigo_Produto_Fornecedor` varchar(20) default NULL,
       `T129_Descricao_Produto` varchar(50) default NULL,
       `T129_Quantidade` decimal(10,3) default NULL,
       `T129_Quantidade_Separacao` decimal(10,3) default NULL,
       `T129_Quantidade_Estoque` decimal(10,3) default NULL,
       `T129_Valor_Custo_Unitario` decimal(14,5) default '0.00000',
       `T129_Valor_Total_Custo` decimal(10,2) default '0.00',
       `T129_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
       `T129_Valor_Base_ICMS` decimal(10,2) default '0.00',
       `T129_Aliquota_ICMS` decimal(5,2) default '0.00',
       `T129_Valor_ICMS` decimal(10,2) default '0.00',
       `T129_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
       `T129_Valor_Base_IPI` decimal(10,2) default '0.00',
       `T129_Aliquota_IPI` decimal(5,2) default '0.00',
       `T129_Valor_IPI` decimal(10,2) default '0.00',
       `T129_Prazo_Entrega` int(10) default '0',
       `T129_Classificacao_Fiscal` varchar(12) default NULL,
       `T129_Codigo_Fornecedor` varchar(6) default NULL,
       `T129_Flag_Travar_Pedido` varchar(1) default 'N',
       `T129_Numero` varchar(10) default NULL,
       `T129_Flag_Cancelada` varchar(1) default 'N',
       `T129_Observacao` tinytext NOT NULL,
       `T129_T006_Id` int(10) unsigned default NULL,
       PRIMARY KEY (`T129_Id`),
       KEY `T129_IDX_PRODUTO_ESTOQUE` (`T129_D009_Id`),
       KEY `T129_IDX_PEDIDO_COMPRA` (`T129_T128_Id`),
       KEY `T129_IDX_CODIGO_PRODUTO` (`T129_Codigo_Produto`),
       KEY `T129_IDX_CODIGO_FORNECEDOR` (`T129_Codigo_Fornecedor`),
       KEY `T129_IDX_EMPRESAS` (`T129_C004_Id`),
       KEY `T129_IDX_PRECOS_COTACAO` (`T129_T059_Id`),
       KEY `T129_IDX_NUMERO_PEDIDO` (`T129_Numero`),
       KEY `T129_IDX_ID` (`T129_Id`) ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T129</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D056");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D056` (
      `D056_Id` bigint(20) NOT NULL auto_increment,
      `D056_D020_Id` int(10) unsigned,
      `D056_Logradouro` varchar(70) default NULL,
      `D056_Bairro` varchar(72) default NULL,
      `D056_Cep` varchar(9) NOT NULL,
      `D056_Tipo_Logradouro` varchar(20) default NULL,
      PRIMARY KEY  (`D056_Id`),
      KEY `D056_Idx_Cidade` (`D056_D020_Id`),
      KEY `D056_Idx_Cep` (`D056_Cep`),
      CONSTRAINT `D056_Fk_Cidade` FOREIGN KEY (`D056_D020_Id`) REFERENCES `D020` (`D020_Id`) ON DELETE RESTRICT
      ) ENGINE=InnoDB";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D056</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T133");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T133` (
      `T133_Id` bigint(20) NOT NULL auto_increment,
      `T133_Data` datetime default '0000-00-00 00:00:00',
      `T133_Topico` varchar(50) default '',
      `T133_Resumo` text default '',
      `T133_Texto` text default '',
      PRIMARY KEY  (`T133_Id`),
      KEY `T133_Id` (`T133_Id`),
      KEY `T133_Data` (`T133_Data`)
      ) ENGINE=InnoDB";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T133</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T134");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T134` (
      `T134_Id` bigint(20) NOT NULL auto_increment,
      `T134_Faixa_Inicial` decimal(10,2) default '0.00',
      `T134_Faixa_Final` decimal(10,2) default '0.00',
      `T134_Aliquota` decimal(10,2) default '0.00',
      `T134_IRPJ` decimal(10,2) default '0.00',
      `T134_CSLL` decimal(10,2) default '0.00',
      `T134_COFINS` decimal(10,2) default '0.00',
      `T134_PIS` decimal(10,2) default '0.00',
      `T134_INSS_CPP` decimal(10,2) default '0.00',
      `T134_ICMS` decimal(10,2) default '0.00',
      `T134_IPI` decimal(10,2) default '0.00',
      `T134_Reducao_Base_ICMS` decimal(10,2) default '0.00',
      PRIMARY KEY  (`T134_Id`),
      KEY `T134_Id` (`T134_Id`)
      ) ENGINE=InnoDB";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T134</b><BR>Clausula: <b>$cClausula</b>";
}

if ($cConfirmado=='S')
{
	$T134=mysql_query("select * from T134");
	If (mysql_num_rows($T134)<=0)
	{

		mysql_query("insert into T134 values (' 1','      0.00',' 120000.00',' 4.50',' 0.00',' 0.00',' 0.00',' 0.00',' 2.75',' 1.25',' 0.50','100.00')");
		mysql_query("insert into T134 values (' 2',' 120000.01',' 240000.00',' 5.97',' 0.00',' 0.00',' 0.86',' 0.00',' 2.75',' 1.86',' 0.50','100.00')");
		mysql_query("insert into T134 values (' 3',' 240000.01',' 360000.00',' 7.34',' 0.27',' 0.31',' 0.95',' 0.23',' 2.75',' 2.33',' 0.50','100.00')");
		mysql_query("insert into T134 values (' 4',' 360000.01',' 480000.00',' 8.04',' 0.35',' 0.35',' 1.04',' 0.25',' 2.99',' 2.56',' 0.50',' 73.96')");
		mysql_query("insert into T134 values (' 5',' 480000.01',' 600000.00',' 8.10',' 0.35',' 0.35',' 1.05',' 0.25',' 3.02',' 2.58',' 0.50',' 58.66')");
		mysql_query("insert into T134 values (' 6',' 600000.01',' 720000.00',' 8.78',' 0.38',' 0.38',' 1.15',' 0.27',' 3.28',' 2.82',' 0.50',' 52.72')");
		mysql_query("insert into T134 values (' 7',' 720000.01',' 840000.00',' 8.86',' 0.39',' 0.39',' 1.16',' 0.28',' 3.30',' 2.84',' 0.50',' 46.34')");
		mysql_query("insert into T134 values (' 8',' 840000.01',' 960000.00',' 8.95',' 0.39',' 0.39',' 1.17',' 0.28',' 3.35',' 2.87',' 0.50',' 36.12')");
		mysql_query("insert into T134 values (' 9',' 960000.01','1080000.00',' 9.53',' 0.42',' 0.42',' 1.25',' 0.30',' 3.57',' 3.07',' 0.50',' 32.44')");
		mysql_query("insert into T134 values ('10','1080000.01','1200000.00',' 9.62',' 0.42',' 0.42',' 1.26',' 0.30',' 3.62',' 3.10',' 0.50',' 26.88')");
		mysql_query("insert into T134 values ('11','1200000.01','1320000.00','10.45',' 0.46',' 0.46',' 1.38',' 0.33',' 3.94',' 3.38',' 0.50',' 28.28')");
		mysql_query("insert into T134 values ('12','1320000.01','1440000.00','10.54',' 0.46',' 0.46',' 1.39',' 0.33',' 3.99',' 3.41',' 0.50',' 25.06')");
		mysql_query("insert into T134 values ('13','1440000.01','1560000.00','10.63',' 0.47',' 0.47',' 1.40',' 0.33',' 4.01',' 3.45',' 0.50',' 22.71')");
		mysql_query("insert into T134 values ('14','1560000.01','1680000.00','10.73',' 0.47',' 0.47',' 1.42',' 0.34',' 4.05',' 3.48',' 0.50',' 20.63')");
		mysql_query("insert into T134 values ('15','1680000.01','1800000.00','10.82',' 0.48',' 0.48',' 1.43',' 0.34',' 4.08',' 3.51',' 0.50',' 18.96')");
		mysql_query("insert into T134 values ('16','1800000.01','1920000.00','11.73',' 0.52',' 0.52',' 1.56',' 0.37',' 4.44',' 3.82',' 0.50',' 23.65')");
		mysql_query("insert into T134 values ('17','1920000.01','2040000.00','11.82',' 0.52',' 0.52',' 1.57',' 0.37',' 4.49',' 3.85',' 0.50',' 20.55')");
		mysql_query("insert into T134 values ('18','2040000.01','2160000.00','11.92',' 0.53',' 0.53',' 1.58',' 0.38',' 4.52',' 3.88',' 0.50',' 17.91')");
		mysql_query("insert into T134 values ('19','2160000.01','2280000.00','12.01',' 0.53',' 0.53',' 1.60',' 0.38',' 4.56',' 3.91',' 0.50',' 15.65')");
		mysql_query("insert into T134 values ('20','2280000.01','2400000.00','12.11',' 0.54',' 0.54',' 1.60',' 0.38',' 4.60',' 3.95',' 0.50',' 13.92')");
        $mAlteracoes[]="Criar tabela: <b>T134</b><BR>Incluindo registros";
		
	}
}

$cSql=@mysql_query("select * from S009");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `S009` (
      `S009_Id` int(10) NOT NULL auto_increment,
      `S009_Nome` varchar(20) default NULL,
      `S009_Email` varchar(50) default NULL,
      `S009_Observacao` text default NULL,
      PRIMARY KEY  (`S009_Id`)
      ) ENGINE=InnoDB";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>S009</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T064");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T064` (
      `T064_Id` bigint(20) unsigned NOT NULL auto_increment,
      `T064_D024_Id` int(10) unsigned default NULL,
      `T064_Data_Inclusao` datetime default NULL,
      `T064_Tipo_Frete` enum('','SEDEX','PAC') default '',
      `T064_Valor_Frete` decimal(10,2) NOT NULL default '0.00',
      `T064_Valor_Total` decimal(10,2) NOT NULL default '0.00',
      `T064_Peso_Total` decimal(10,3) default '0.000',
      `T064_Transportadora` varchar(50) default NULL,
      `T064_Flag_Pedido_Fechado` enum('S','N') default 'N',
      `T064_Flag_Status` enum('0','1','2','3','4','5','6') default '0',
      `T064_Flag_Cancelado` enum('S','N') default 'N',
      `T064_Forma_Pagamento` enum('0','1','2') default '0',
      `T064_Aviso_Recebimento_Correios` varchar(15) default NULL,
      `T064_Observacao_Cancelamento` text,
      PRIMARY KEY  (`T064_Id`),
      KEY `T064_IDX_CLIENTE` (`T064_D024_Id`)
    ) ENGINE=InnoDB COMMENT='PEDIDO INTERNET' AUTO_INCREMENT=1000;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T064</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T065");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T065` (
      `T065_Id` bigint(20) unsigned NOT NULL auto_increment,
      `T065_D001_Id` int(10) unsigned default NULL,
      `T065_T064_Id` bigint(20) unsigned default NULL,
      `T065_Quantidade` int(4) NOT NULL default '0',
      `T065_Valor_Preco_Unitario` decimal(10,2) NOT NULL default '0.00',
      `T065_Valor_Preco_Total` decimal(10,2) default '0.00',
      `T065_Peso_Unitario` decimal(10,3) default '0.000',
      `T065_Peso_Total` decimal(10,3) default '0.000',
      PRIMARY KEY  (`T065_Id`),
      KEY `T065_IDX_PRODUTO` (`T065_D001_Id`),
      KEY `T065_IDX_PEDIDO_INTERNET` (`T065_T064_Id`)
      ) ENGINE=InnoDB COMMENT='ITENS DO PEDIDO INTERNET';";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T065</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from S001");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `S001` (
      `S001_Id` int(11) NOT NULL auto_increment,
      `S001_Data_Hora_Inclusao` datetime default NULL,
      `S001_Nome` varchar(200) default NULL,
      `S001_Assunto` varchar(200) default NULL,
      `S001_Email` varchar(200) default NULL,
      `S001_Mensagem_Fale_Conosco` text,
      PRIMARY KEY  (`S001_Id`)
    ) ENGINE=MyISAM;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>S001</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T131");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `T131` (
      `T131_Id` int(11) NOT NULL auto_increment,
      `T131_C007_Id` int(11) NOT NULL,
      `T131_Mes` varchar(7) NOT NULL,
      `T131_Valor_Salario` decimal(10,2) NOT NULL,
      `T131_Teto_Comissao` decimal(10,2) NOT NULL,
      `T131_Valor_Meta_Vendas` decimal(10,2) NOT NULL,
      PRIMARY KEY  (`T131_Id`),
      KEY `T131_IDX_C007_Id` (`T131_C007_Id`),
      KEY `T131_IDX_Mes` (`T131_Mes`)
    ) ENGINE=MyISAM;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T131</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T124");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `T124` (
      `T124_Id` int(11) NOT NULL auto_increment,
      `T124_D024_Id` int(10) unsigned default NULL,
      `T124_Ano` varchar(4) default '',
      `T124_Curva_ABC` varchar(1) default '',
      `T124_Vendas_01` decimal(10,2) default 0.00,
      `T124_Vendas_02` decimal(10,2) default 0.00,
      `T124_Vendas_03` decimal(10,2) default 0.00,
      `T124_Vendas_04` decimal(10,2) default 0.00,
      `T124_Vendas_05` decimal(10,2) default 0.00,
      `T124_Vendas_06` decimal(10,2) default 0.00,
      `T124_Vendas_07` decimal(10,2) default 0.00,
      `T124_Vendas_08` decimal(10,2) default 0.00,
      `T124_Vendas_09` decimal(10,2) default 0.00,
      `T124_Vendas_10` decimal(10,2) default 0.00,
      `T124_Vendas_11` decimal(10,2) default 0.00,
      `T124_Vendas_12` decimal(10,2) default 0.00,
      `T124_Vendas_Total` decimal(10,2) default 0.00,
      PRIMARY KEY  (`T124_Id`),
      KEY `T124_IDX_PRODUTO` (`T124_D024_Id`)
    ) ENGINE=MyISAM;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T124</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T125");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `T125` (
      `T125_Id` int(11) NOT NULL auto_increment,
      `T125_D001_Id` int(10) unsigned default NULL,
      `T125_Ano` varchar(4) default '',
      `T125_Curva_ABC` varchar(1) default '',
      `T125_Vendas_Valor_01` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_02` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_03` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_04` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_05` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_06` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_07` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_08` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_09` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_10` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_11` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_12` decimal(10,2) default 0.00,
      `T125_Vendas_Valor_Total` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_01` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_02` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_03` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_04` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_05` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_06` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_07` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_08` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_09` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_10` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_11` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_12` decimal(10,2) default 0.00,
      `T125_Vendas_Qte_Total` decimal(10,2) default 0.00,
      PRIMARY KEY  (`T125_Id`),
      KEY `T125_IDX_PRODUTO` (`T125_D001_Id`),
      KEY `T125_IDX_ANO` (`T125_Ano`)
    ) ENGINE=MyISAM;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T125</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T126");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `T126` (
      `T126_Id` int(11) NOT NULL auto_increment,
      `T126_C007_Id` int(10) unsigned default NULL,
      `T126_Ano` varchar(4) default '',
      `T126_Curva_ABC` varchar(1) default '',
      `T126_Vendas_01` decimal(10,2) default 0.00,
      `T126_Vendas_02` decimal(10,2) default 0.00,
      `T126_Vendas_03` decimal(10,2) default 0.00,
      `T126_Vendas_04` decimal(10,2) default 0.00,
      `T126_Vendas_05` decimal(10,2) default 0.00,
      `T126_Vendas_06` decimal(10,2) default 0.00,
      `T126_Vendas_07` decimal(10,2) default 0.00,
      `T126_Vendas_08` decimal(10,2) default 0.00,
      `T126_Vendas_09` decimal(10,2) default 0.00,
      `T126_Vendas_10` decimal(10,2) default 0.00,
      `T126_Vendas_11` decimal(10,2) default 0.00,
      `T126_Vendas_12` decimal(10,2) default 0.00,
      `T126_Vendas_Total` decimal(10,2) default 0.00,
      PRIMARY KEY  (`T126_Id`),
      KEY `T126_IDX_PRODUTO` (`T126_C007_Id`)
    ) ENGINE=MyISAM;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T126</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from S003");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `S003` (
      `S003_Id` int(11) NOT NULL auto_increment,
      `S003_Email` varchar(200) NOT NULL,
      `S003_Receber` enum('S','N') default 'S',
      `S003_Ativo` enum('S','N') default 'S',
      `S003_Data_Inclusao` datetime default '0000-00-00 00:00:00',
      PRIMARY KEY  (`S003_Id`),
      KEY `IDX_Email` (`S003_Email`)
      ) ENGINE=InnoDB;";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>S003</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D073");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `D073` ( `D073_Id` int(10) NOT NULL auto_increment, `D073_Nome` varchar(30) default NULL, PRIMARY KEY (`D073_Id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='CENTRO DE CUSTOS' ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D073</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from C021");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE C021 (C021_Id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,C021_Nome_Perfil VARCHAR(50) NULL,PRIMARY KEY(C021_Id)) TYPE=InnoDB; ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>C021</b><BR>Clausula: <b>$cClausula</b>";
}
Alterar_Tipo_Campo("C007_Id","INT(11) UNSIGNED","NOT NULL auto_increment");
$cSql=@mysql_query("select * from C019");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE C019 (
      C019_Id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
      C019_C021_Id INTEGER UNSIGNED NOT NULL,
      C019_C004_Id INT(10) UNSIGNED NOT NULL,
      C019_C007_Id INT(10) UNSIGNED NOT NULL,
      PRIMARY KEY(C019_Id),
      UNIQUE KEY `Duplicidade` (`C019_C021_Id`,`C019_C004_Id`,`C019_C007_Id`),
      INDEX C019_FKIndex1(C019_C007_Id),
      INDEX C019_FKIndex3(C019_C004_Id),
      FOREIGN KEY(C019_C021_Id)
        REFERENCES C021(C021_Id)
          ON DELETE CASCADE,
      FOREIGN KEY(C019_C004_Id)
        REFERENCES C004(C004_Id)
          ON DELETE CASCADE
    ) TYPE=InnoDB;";

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>C019</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from D072");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `D072` (
      `D072_Id` bigint(20) unsigned NOT NULL auto_increment,
      `D072_D001_Id` int(10) unsigned default NULL,
      `D072_Arquivo_Foto` varchar(50) NOT NULL,
      `D072_Flag_Principal` enum('S','N') default 'N',
      PRIMARY KEY  (`D072_Id`),
      UNIQUE KEY `Duplicidade` (`D072_D001_Id`,`D072_Arquivo_Foto`),
      KEY `D072_IDX_PRODUTOS` (`D072_D001_Id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='FOTOS DOS ITENS'";

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D072</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from S002");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE IF NOT EXISTS `S002` (
      `S002_Id` int(11) NOT NULL auto_increment,
      `S002_D002_Id` int(10) default '0',
      `S002_Data` date default NULL,
      `S002_Titulo` varchar(100) default NULL,
      `S002_Publicar` enum('S','N') default 'N',
      `S002_Arquivo_Foto` varchar(100) default NULL,
      `S002_Link` varchar(200) default NULL,
      `S002_Mostrar_Vitrine` enum('S','N') default 'N',
      `S002_Tipo_Banner` enum('1','2','3') default NULL,
      `S002_Ordem` int(3) default '0',
      PRIMARY KEY  (`S002_Id`),
      KEY `Fk_Descricao` (`S002_D002_Id`)
      ) ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>S002</b><BR>Clausula: <b>$cClausula</b>";
}



$cSql=@mysql_query("select * from C022");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE C022 (
       C022_Id int(10) unsigned NOT NULL auto_increment,
       C022_C021_Id int(10) unsigned NOT NULL,
       C022_Id_Menu int(10) unsigned NOT NULL,
       C022_Acesso_Liberado enum('S','N') default 'N',
       PRIMARY KEY (C022_Id),
       UNIQUE KEY `Duplicidade` (`C022_C021_Id`,`C022_Id_Menu`),
       KEY C022_FKIndex1 (C022_C021_Id),
       FOREIGN KEY(C022_C021_Id)
          REFERENCES C021 (C021_Id)
          ON DELETE CASCADE
       ) ENGINE=InnoDB ";

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>C022</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from T099");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE T099 (
       T099_Id int(10) unsigned NOT NULL auto_increment,
       T099_D024_Id int(10) unsigned NOT NULL,
       T099_D009_Id int(10) unsigned NOT NULL,
       T099_Quantidade decimal(10,3) default 0,
       T099_Data date default '0000-00-00',
       T099_Valor_Preco_Unitario decimal(10,2) default '0',
       PRIMARY KEY (T099_Id)
       ) ENGINE=InnoDB ";

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T099</b><BR>Clausula: <b>$cClausula</b>";
}

$cClausula = "show columns from D024 like 'D024_Flag_Pre_Cadastro'";
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   echo "$cClausula<hr>$cErro";
   exit;
}
if (mysql_num_rows($cSql)==0)
{
   if ($cConfirmado=='S')
   {
        Criar_Campo("D024_Flag_Pre_Cadastro","enum('S','N') default 'N'");

        $cSql=mysql_query("update D024 set D024_Flag_Lista_Preco=D024_Flag_Revenda_Consumidor");
        $cSql=mysql_query("update T003 left join D024 on D024_Id=T003_D024_Id set T003_Flag_Lista_Preco=D024_Flag_Lista_Preco");
        $cSql=mysql_query("update T005 left join D024 on D024_Id=T005_D024_Id set T005_Flag_Lista_Preco=D024_Flag_Lista_Preco");
   }
}

$cSql=@mysql_query("select * from C020");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " create table C020 ( ";
       $cClausula .= " C020_Id int(10) unsigned auto_increment,";
       $cClausula .= " C020_C004_Id int(10) unsigned,";
       $cClausula .= " PRIMARY KEY (C020_Id),";
       $cClausula .= " UNIQUE KEY Duplicidade (C020_C004_Id), ";
       $cClausula .= " KEY C020_IDX_EMPRESAS (C020_C004_Id),";
       $cClausula .= " CONSTRAINT C020_Fk_Empresas FOREIGN KEY (C020_C004_Id) REFERENCES C004 (C004_Id) ON DELETE CASCADE";
       $cClausula .= " )";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela C020";
}

$cSql=@mysql_query("select * from T130");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE `T130`
                    ( `T130_Id` int(10) unsigned NOT NULL auto_increment,
                      `T130_T129_Id` int(10) unsigned default NULL,
                      `T130_T013_Id` int(10) unsigned default NULL,
                      `T130_C004_Id` int(10) unsigned default NULL,
                      `T130_T008_Id` int(10) unsigned default NULL,
                      `T130_Quantidade` decimal(14,5) default '0.00000',
                      `T130_Classificacao_Fiscal` varchar(12) default NULL,
                      `T130_Aliquota_IPI` decimal(5,2) default '0.00',
                      PRIMARY KEY (`T130_Id`),
                      KEY `T130_FKIndex1` (`T130_T129_Id`),
                      KEY `T130_FKIndex2` (`T130_T013_Id`),
                      KEY `T130_FKIndex3` (`T130_T008_Id`),
                      KEY `T130_FKIndex4` (`T130_C004_Id`) )
                      ENGINE=InnoDB DEFAULT CHARSET=latin1";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela T130";
}


$cSql=@mysql_query("select * from S006");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $mAlteracoes[]='Criar tabela S006';
       $cClausula  = " CREATE TABLE S006 (
                       S006_Id bigint(20) unsigned  auto_increment,
                       S006_T064_Id bigint(20) unsigned,
                       S006_Data_Hora_Inclusao datetime,
                       S006_Flag_Status enum('0','1','2','3','4','5','6') default 0,
                       PRIMARY KEY  (`S006_Id`),
                       CONSTRAINT `Fk_Pedido_Internet` FOREIGN KEY (`S006_T064_Id`) REFERENCES `T064` (`T064_Id`) ON DELETE CASCADE
                       ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1; ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela S006";

}

$cSql=@mysql_query("select * from S007");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $mAlteracoes[]='Criar tabela S007';
       $cClausula  = " CREATE TABLE S007 (
                       S007_Id bigint(20) unsigned auto_increment,
                       S007_Data_Inclusao datetime,
                       S007_Email_Remetente varchar(60),
                       S007_Email_Apresentacao varchar(100),
                       S007_Titulo varchar(200),
                       S007_Arquivo_Anexo varchar(200),
                       PRIMARY KEY  (`S007_Id`)
    				   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela S007";
}

$cSql=@mysql_query("select * from S008");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $mAlteracoes[]='Criar tabela S008';
       $cClausula  = " CREATE TABLE S008 (
                       S008_Id bigint(20) unsigned auto_increment,
                       S008_S007_Id bigint(20) unsigned,
                       S008_Email varchar(200) NOT NULL,
                       S008_Data_Envio datetime default '0000-00-00 00:00:00',
                       S008_Data_Visualizado datetime default '0000-00-00 00:00:00',
                       PRIMARY KEY  (`S008_Id`),
                       UNIQUE KEY `Duplicidade` (`S008_S007_Id`,`S008_Email`),
                       CONSTRAINT `Fk_News` FOREIGN KEY (`S008_S007_Id`) REFERENCES `S007` (`S007_Id`) ON DELETE CASCADE
    				   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela S008";
}

$cSql=@mysql_query("select * from T127");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE T127 (
                       T127_Id int(10) unsigned auto_increment,
                       T127_D024_Id int(10) unsigned,
                       T127_Chave_Acesso_Nfe varchar(44),
                       T127_Numero_Nota_Fiscal varchar(12),
                       T127_Codigo_Retorno varchar(3),
                       T127_Numero_Recibo_Nfe varchar(20),
                       T127_Data_Transmicao datetime default '0000-00-00 00:00:00',
                       T127_Retorno_Sefaz TEXT,
                       PRIMARY KEY  (`T127_Id`)
    				   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela T127";
}


$cSql=@mysql_query("select * from T117");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE T117 (
                       T117_Id int(10) unsigned auto_increment,
                       T117_C004_Id int(10) unsigned,
                       T117_D024_Id int(10) unsigned,
                       T117_D007_Id int(10) unsigned,
                       T117_Data_Emissao date default '0000-00-00',
                       T117_Data_Vencimento date default '0000-00-00',
                       T117_Data_Pagamento date default '0000-00-00',
                       T117_Valor_Total decimal(10,2) default '0',
                       T117_Instrucoes text,
                       T117_Codigo_Barras varchar(200),
                       PRIMARY KEY  (`T117_Id`),
                       CONSTRAINT `Fk_T117_C004` FOREIGN KEY (`T117_C004_Id`) REFERENCES `C004` (`C004_Id`) ON DELETE RESTRICT,
                       CONSTRAINT `Fk_T117_D007` FOREIGN KEY (`T117_D007_Id`) REFERENCES `D007` (`D007_Id`) ON DELETE RESTRICT,
                       CONSTRAINT `Fk_T117_D024` FOREIGN KEY (`T117_D024_Id`) REFERENCES `D024` (`D024_Id`) ON DELETE RESTRICT
    				   ) ENGINE=InnoDB AUTO_INCREMENT=34567 DEFAULT CHARSET=latin1 ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela T117";
}


$cSql=@mysql_query("select * from T118");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " CREATE TABLE T118 (
                       T118_Id int(11) NOT NULL auto_increment,
                       T118_T007_Id int(10) unsigned default '0',
                       T118_D027_Id int(10) unsigned default '0',
                       T118_Numero_Parcela int(3) default '0',
                       T118_Prazos_Dias int(5) default '0',
                       T118_Data_Vencimento date default NULL,
                       T118_Valor_Parcela decimal(12,2) default '0.00',
                       PRIMARY KEY (T118_Id),
                       CONSTRAINT `Fk_T118_T007` FOREIGN KEY (`T118_T007_Id`) REFERENCES `T007` (`T007_Id`) ON DELETE CASCADE
                       ) ENGINE=InnoDB; ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela T118";
}

$cSql=@mysql_query("select * from T119");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T119` (
      `T119_Id` bigint(20) unsigned NOT NULL auto_increment,
      `T119_D007_Id` int(10) unsigned default NULL,
      `T119_C007_Id` int(10) unsigned default NULL,
      `T119_Data_Abertura` datetime default '0000-00-00 00:00:00',
      `T119_Data_Fechamento` datetime default '0000-00-00 00:00:00',
      `T119_Valor_Saldo_Inicial` decimal(10,2) NOT NULL default '0.00',
      `T119_Valor_Saldo_Final` decimal(10,2) default '0.00',
      `T119_Observacao` text,
      PRIMARY KEY  (`T119_Id`),
      KEY `IDX_T119_D007_Id` (`T119_D007_Id`)
      KEY `IDX_T119_C007_Id` (`T119_C007_Id`)
      ) ENGINE=InnoDB COMMENT='ABERTURA E FECHAMENTO DE CAIXA';";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T119</b><BR>Clausula: <b>$cClausula</b>";
}



$cSql=@mysql_query("select * from T120");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T120` (
      `T120_Id` int(10) unsigned NOT NULL auto_increment,
      `T120_C004_Id` int(10) unsigned,
      `T120_C007_Id` int(11) unsigned,
      `T120_D024_Id` int(10) unsigned,
      `T120_Numero_Contrato` varchar(30),
      `T120_Data_Contrato` date default '0000-00-00',
      `T120_Data_Vencimento` date default '0000-00-00',
      `T120_Flag_Ativo` enum('S','N') default 'S',
      `T120_Valor_Compra_Contratada` decimal(10,2) default '0.00',
      `T120_Observacao` text,
      PRIMARY KEY  (`T120_Id`),
      KEY `IDX_T120_D024_Id` (`T120_D024_Id`)
      ) ENGINE=InnoDB COMMENT='CONTRATO DE COMODATO';";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T120</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T121");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T121` (
      `T121_Id` int(10) unsigned NOT NULL auto_increment,
      `T121_T120_Id` int(10) unsigned,
      `T121_D009_Id` int(10) unsigned,
      `T121_Data_Inclusao` datetime default '0000-00-00 00:00:00',
      `T121_Data_Entrega` date default '0000-00-00',
      `T121_Data_Devolucao` date default '0000-00-00',
      `T121_Valor` decimal(10,2) default '0.00',
      PRIMARY KEY  (`T121_Id`),
      KEY `IDX_T121_D009_Id` (`T121_D009_Id`)
      ) ENGINE=InnoDB COMMENT='ITENS DO CONTRATO DE COMODATO';";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T121</b><BR>Clausula: <b>$cClausula</b>";
}




$cSql=@mysql_query("select * from T103");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE `T103`
                   (`T103_Id` int(10) unsigned NOT NULL auto_increment,
                    `T103_T002_Id` int(10) unsigned,
                    `T103_D027_Id` int(10) unsigned,
                    `T103_Flag_Baixar_Lote` enum('S','N') default 'N',
                   `T103_Valor_Recebido` decimal(10,2) default 0,
                   PRIMARY KEY (`T103_Id`),
                   KEY `T103_Index_Id` (`T103_Id`),
                   KEY `T103_Index_Portador` (`T103_D027_Id`),
                   KEY `T103_Index_Receber` (`T103_T002_Id`)
                   ) ENGINE=InnoDB;";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T103</b><BR>Clausula: <b>$cClausula</b>";
}


$cSql=@mysql_query("select * from T122");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T122` (
      `T122_Id` int(10) unsigned NOT NULL auto_increment,
      `T122_C004_Id` int(10) unsigned,
      `T122_C007_Id` int(11) unsigned,
      `T122_D024_Id` int(10) unsigned,
      `T122_Data` date default '0000-00-00',
      `T122_Observacao` text,
      PRIMARY KEY  (`T122_Id`),
      KEY `IDX_T122_D024_Id` (`T122_D024_Id`)
      ) ENGINE=InnoDB COMMENT='ORDEM DE SERVICO';";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T122</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from D090");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `D090` (
      `D090_Id` int(10) unsigned NOT NULL auto_increment,
      `D090_Aliquota_Icms` decimal(5,2) default 0,
      `D090_D059_Id` int(10) default 0,
      `D090_Flag_ST` enum('S','N') default 'N',
      `D090_Codigo_Impressora` varchar(10) default null,
      PRIMARY KEY  (`D090_Id`)
      ) ENGINE=InnoDB COMMENT='CORRELACAO DE CODIGOS CUPOM FISCAL'";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D090</b><BR>Clausula: <b>$cClausula</b>";
}

$cSql=@mysql_query("select * from T123");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `T123` (
      `T123_Id` int(10) unsigned NOT NULL auto_increment,
      `T123_T122_Id` int(10) unsigned,
      `T123_D009_Id` int(10) unsigned,
      `T123_Data_Inclusao` datetime default '0000-00-00 00:00:00',
      `T123_Quantidade` decimal(10,3) default '0.00',
      `T123_Valor_Preco` decimal(10,2) default '0.00',
      `T123_Descricao` varchar(60),
      PRIMARY KEY  (`T123_Id`),
      KEY `IDX_T123_D009_Id` (`T123_D009_Id`)
      ) ENGINE=InnoDB COMMENT='ITENS DA ORDEM DE SERVICO';";
   if ($cConfirmado=='S')
   {

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>T123</b><BR>Clausula: <b>$cClausula</b>";
}


$cClausula = "select C004_Id from C004 left join C020 on C020_C004_Id=C004_Id where C020_Id is null order by C004_Id";
$cSql_C004 = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
  echo "$cClausula<hr>$cErro";
  exit;
}

while ($mC004 = mysql_fetch_array($cSql_C004))
{
   if ($cConfirmado=='S')
   {
       $cClausula = "insert into C020 (C020_C004_Id) values ('$mC004[C004_Id]')";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Inserir registros na tabela C020";
}

$cSql=@mysql_query("select * from D060");
$cErro=mysql_error();
if (!empty($cErro))
{
       $cClausula  = "CREATE TABLE IF NOT EXISTS `D060` (
      `D060_Id` int(3) unsigned NOT NULL auto_increment,
      `D060_C008_Id` int(10) unsigned default NULL,
      `D060_Codigo` int(5),
      `D060_Descricao_Grupo` varchar(50),
      PRIMARY KEY  (`D060_Id`)
      ) ENGINE=InnoDB COMMENT='CADASTRO DE GRUPOS';";
   if ($cConfirmado=='S')
   {
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
       Criar_Campo("D002_D060_Id","int(3) unsigned NOT NULL after D002_Id");
       Criar_Campo("D001_D060_Id","int(3) unsigned NOT NULL after D001_D002_Id");

       $cClausula  = " select ";
       $cClausula .= " D002_Id, ";
       $cClausula .= " D002_C008_Id, ";
       $cClausula .= " D002_Grupo, ";
       $cClausula .= " D002_Codigo, ";
       $cClausula .= " D002_Descricao_Produto ";
       $cClausula .= " from D002 ";
       $cClausula .= " order by D002_Grupo,D002_Codigo ";
       $cSql_D002 = mysql_query($cClausula);
       while ($mD002 = mysql_fetch_array($cSql_D002))
       {
            if ($mD002[D002_Grupo]!='' and $mD002[D002_Codigo]=='0')
            {
               $cSql = mysql_query("insert into D060 (D060_C008_Id,D060_Codigo,D060_Descricao_Grupo) values ('$mD002[D002_C008_Id]','$mD002[D002_Grupo]','$mD002[D002_Descricao_Produto]')");
               echo mysql_error();
               $nD060_Id = mysql_insert_id();
               $Grupo=$mD002[D002_Grupo];
               //mysql_query("delete from D002 where D002_Id='$mD002[D002_Id]'");
               //echo mysql_error();
            }
            elseif($Grupo==$mD002[D002_Grupo])
            {
               mysql_query("update D002 set D002_D060_Id='$nD060_Id' where D002_Id='$mD002[D002_Id]'");
               mysql_query("update D001 set D001_D060_Id='$nD060_Id' where D001_D002_Id='$mD002[D002_Id]'");
               echo mysql_error();
            }
            
       }

   }
   $mAlteracoes[]="Criar tabela: <b>D060</b><BR>Clausula: <b>$cClausula</b>";
}

$cClausula = "show columns from T014 like 'T014_D006_Id'";
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   echo "$cClausula<hr>$cErro";
}
if (mysql_num_rows($cSql)==0)
{
   $cClausula = "alter table T014 add T014_D006_Id int(10) unsigned after T014_Id";
   $cSql = mysql_query($cClausula);
   $cErro = mysql_error();
   if (!empty($cErro))
   {
      echo "$cClausula<hr>$cErro";
      exit;
   }
   $cClausula = "update T014 left join T013 on T013_Id=T014_T013_Id set T014_D006_Id=T013_D006_Id";
   $cSql = mysql_query($cClausula);
   $cErro = mysql_error();
   if (!empty($cErro))
   {
      echo "$cClausula<hr>$cErro";
      exit;
   }
}

$cSql=@mysql_query("select * from D087");
$cErro=mysql_error();
if (!empty($cErro))
{
   if ($cConfirmado=='S')
   {
       $cClausula  = " create table D087 (
                                    D087_Id int(10) unsigned primary key auto_increment,
                                    D087_D024_Id int(10) unsigned,
                                    D087_C007_Id int(10) unsigned,
                                    D087_Nome_Arquivo varchar(200),
                                    D087_Descricao text,
                                    D087_Data_Hora_UpLoad datetime default '0000-00-00 00:00:00'
                       ) ENGINE=InnoDB ; ";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }
   $mAlteracoes[]="Criar tabela: <b>D087</b><BR>Clausula: <b>$cClausula</b>";
}

Criar_Campo("T127_C004_Id","int(10) unsigned after T127_D024_Id");
Criar_Campo("T127_Xml_Retorno","text");

Alterar_Campo("C020_Ativar_Scan_NFe","C020_NFe_SCAN_Ativado","enum('S','N') default 'N'");
Criar_Campo("C020_Versao_NFe","decimal(4,2) default '1.10'");
Criar_Campo("C020_Utiliza_Modulo_Financeiro","enum('S','N') default 'N'");
Criar_Campo("C020_Utiliza_Modulo_Orcamento","enum('S','N') default 'N'");
Criar_Campo("C020_Utiliza_Modulo_Orcamento_PDV","enum('S','N') default 'N'");
Criar_Campo("C020_Emite_Nota_D1","enum('S','N') default 'N'");

Alterar_Campo("D014_Flag_Tipo","D014_Flag_Tipo","varchar(2) default 'PG'");
Alterar_Campo("T133_Topico","T133_Topico","varchar(200) default ''");

Criar_Campo("C020_Casas_Decimais_Quantidade","int(1) default 3");
Criar_Campo("C020_Data_Ultima_Atualizacao","datetime default '0000-00-00 00:00:00'");
Criar_Campo("C020_Data_Ultima_Atualizacao_Erros_NFe","datetime default '0000-00-00 00:00:00' after C020_Data_Ultima_Atualizacao");
Criar_Campo("C020_Ultima_Revisao","int(10)");
Criar_Campo("C020_Obrigatorio_OC_NF","enum('S','N') default 'N'");

Criar_Campo("C020_Expedicao_Direcionar_Compra","enum('S','N') default 'N'");
Criar_Campo("C020_Expedicao_Confirmar_Quantidade_Separada","enum('S','N') default 'N'");

Criar_Campo("C020_Orcamento_Alterar_Preco_Venda","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Mostrar_Fornecedor_Tipo","enum('NOME','NI') default 'NOME'");
Criar_Campo("C020_Orcamento_Permite_IPV_Cliente_Zerado","enum('S','N') default 'N'");
Criar_Campo("C020_Orcamento_Permite_Pre_Cadastro","enum('S','N') default 'N'");
Criar_Campo("C020_Orcamento_Permite_Item_Sem_Preco","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Visualizar_IPV_Item","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Visualizar_Custo","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Puxar_Descricao_Orcamento","enum('S','N') default 'N'");
Criar_Campo("C020_Orcamento_Imprimir_Campo_Entrega","enum('S','N') default 'N'");
Criar_Campo("C020_Orcamento_Imprimir_Campo_IPI","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Obter_Ultimo_Preco_Cliente","enum('S','N') default 'S'");

Criar_Campo("C020_Pedido_Gerar_Pedido_Sem_Estoque","enum('S','N') default 'S'");
Criar_Campo("C020_Pedido_Alterar_Preco_Venda","enum('S','N') default 'S'");
Criar_Campo("C020_Pedido_Mostrar_Fornecedor_Tipo","enum('NOME','NI') default 'NOME'");
Criar_Campo("C020_Pedido_Visualizar_IPV_Item","enum('S','N') default 'S'");
Criar_Campo("C020_Pedido_Visualizar_Custo","enum('S','N') default 'S'");
Criar_Campo("C020_Pedido_Puxar_Descricao_Pedido","enum('S','N') default 'S'");
Criar_Campo("C020_Flag_Expedicao","enum('S','N') default 'S'");

Criar_Campo("C020_Caixa_Obrigatorio_Abrir_Fechar_Caixa","enum('S','N') default 'N'");
Criar_Campo("C020_Caixa_Cupom_Fiscal","enum('S','N') default 'N'");
Criar_Campo("C020_Cupom_Fiscal_CFOP","int(10) default 0 after C020_Caixa_Cupom_Fiscal");
Criar_Campo("C020_Cupom_Fiscal_CFOP_Fora","int(10) default 0 after C020_Cupom_Fiscal_CFOP");
Criar_Campo("C020_Cupom_Fiscal_CFOP_ST","int(10) default 0 after C020_Cupom_Fiscal_CFOP_Fora");
Criar_Campo("C020_Cupom_Fiscal_CFOP_ST_Fora","int(10) default 0 after C020_Cupom_Fiscal_CFOP_ST");
Criar_Campo("C020_Cupom_Fiscal_Cliente","int(10) default 0 after C020_Cupom_Fiscal_CFOP_ST_Fora");
Criar_Campo("C020_Cupom_Fiscal_Portador","int(10) default 0 after C020_Cupom_Fiscal_Cliente");

Criar_Campo("C020_Pre_Pedido_Horario_Entrega_Obrigatorio","enum('S','N') default 'S'");
Criar_Campo("C020_Pre_Pedido_Vendedor_Obrigatorio","enum('S','N') default 'S'");
Criar_Campo("C020_Pre_Pedido_Prazos_Obrigatorio","enum('S','N') default 'S'");
Criar_Campo("C020_Modelo_Sistema_Nfe","varchar(2) default '01'");
Criar_Campo("C020_NFe_Tipo_Certificado","enum('A1','A3') default 'A1'");
Criar_Campo("C020_NFe_Solicitar_Senha_Certificado","enum('S','N') default 'N'");

Criar_Campo("C024_C026_Id","int(10) default '0'");

Criar_Campo("C020_Impressora_NF_Recebida","varchar(30)");
Criar_Campo("C020_Modulo_Insuficiencia","varchar(50)");
Criar_Campo("C020_Cliente_Validar_Duplicidade_CNPJ_CPF","enum('S','N') default 'S'");
Criar_Campo("C020_Regras_Faturamento_Vendas","text");

Criar_Campo("T030_Observacao","tinytext");
Criar_Campo("T030_Data_Inclusao","datetime");
Criar_Campo("T030_C007_Id","int(10) default 0 after T030_C004_Id");

Alterar_Tipo_Campo("T030_Quantidade","decimal(14,5)","default 0");

Criar_Campo("T003_Flag_Lista_Preco","varchar(1) default ' '");
Criar_Campo("T003_ST_ICMS_Interno","decimal(5,2) default '0'");
Criar_Campo("T003_ICMS_Inter_Estadual","decimal(5,2) default '0'");
Criar_Campo("T003_ST_VA","decimal(10,2) default '0'");
Criar_Campo("T003_Valor_Base_ST","decimal(10,2) default '0'");
Criar_Campo("T003_Valor_ST","decimal(10,2) default '0'");
Criar_Campo("T003_Valor_Base_ST_Confirmado","decimal(10,2) default '0'");
Criar_Campo("T003_Valor_ST_Confirmado","decimal(10,2) default '0'");
Criar_Campo("T003_Flag_Alcada_Vendedor","varchar(1) default ' '");
Criar_Campo("T003_Flag_Alcada","varchar(1) default ' '");
Criar_Campo("T003_Flag_Status_Liberacao","varchar(1) default '0'");

Criar_Campo("T003_C007_Id_Liberou_Alcada","int(11)");
Criar_Campo("T003_C007_Id_Liberou_Alcada_Data","DateTime default '0000-00-00'");
Criar_Campo("T003_C007_Id_Liberou_Alcada_IPV","decimal(10,4) default '0.0000'");

Criar_Campo("T118_Numero_Duplicata","varchar(15) after T118_D027_Id");
Criar_Campo("T118_D031_Id","int(11) unsigned after T118_D027_Id");

Criar_Campo("T003_Liberou_Alcada_Observacao_Nao_Liberado","text");

Alterar_Campo("T003_C007_Id_Liberou_Alcada","T003_Liberou_Alcada_C007_Id","int(11)");
Alterar_Campo("T003_C007_Id_Liberou_Alcada_Data","T003_Liberou_Alcada_Data","DateTime default '0000-00-00'");
Alterar_Campo("T003_C007_Id_Liberou_Alcada_IPV","T003_Liberou_Alcada_IPV","decimal(10,4) default '0.0000'");

Alterar_Campo("D085_Data","D085_Data","datetime default '0000-00-00 00:00:00'");

Criar_Campo("T005_ST_VA","decimal(10,2) default '0'");
Criar_Campo("T005_ST_ICMS_Interno","decimal(5,2) default '0'");
Criar_Campo("T005_ICMS_Inter_Estadual","decimal(5,2) default '0'");

Criar_Campo("T005_Estoque_Box","varchar(20) default ' '");

Criar_Campo("T005_Pedido_Ecommerce","varchar(20) default NULL after T005_C004_Id");

Criar_Campo("T006_Flag_ST","varchar(1) default 'N'");
Criar_Campo("T006_ST_VA","Decimal(10,2) default '0'");
Criar_Campo("T006_Quantidade_Comprar","Decimal(10,3) default '0'");
Criar_Campo("T006_Observacao_Divergencia","varchar(100) default ''");
Criar_Campo("T006_Observacao_Compra","varchar(100) default ''");
Criar_Campo("T006_Flag_Divergencia","varchar(1) default 'N'");
Criar_Campo("T006_Estoque_BOX","varchar(20) default ''");
Criar_Campo("T006_D009_Id_Comprar","int(10)","after T006_D009_Id");
Criar_Campo("T006_Flag_Cotar","varchar(1) default 'N'");
Criar_Campo("T006_C007_Id_Separou","int(11) unsigned after T006_T005_Id");
Criar_Campo("T006_Data_Separacao","datetime default '0000-00-00 00:00:00'");

Criar_Campo("T007_D089_Id","int(10) unsigned after T007_Id");
Criar_Campo("T007_T007_Id_Transporte","int(10) unsigned after T007_C007_Id");
Criar_Campo("T007_ST_VA","decimal(10,2) default '0'");
Criar_Campo("T007_ST_ICMS_Interno","decimal(5,2) default '0'");
Criar_Campo("T007_ICMS_Inter_Estadual","decimal(5,2) default '0'");
Criar_Campo("T007_Data_Envio_XML","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T007_Casas_Decimais_Quantidade","int(1) default '0'");
Criar_Campo("T007_Casas_Decimais_Preco","int(1) default '2'");

Criar_Campo("T007_Data_Envio_Orcamento","datetime default '0000-00-00 00:00:00'");
Criar_Campo("T007_Flag_Orcamento","varchar(1) default ''");
Criar_Campo("T007_Flag_Emitida_Recebida","varchar(1) default ''");
Criar_Campo("T007_Numero_Orcamento","varchar(30) default ''");


Criar_Campo("T007_Flag_Finalidade_NFe","enum('1','2','3') default 1 after T007_Flag_Contingencia");
Criar_Campo("T007_Exportacao_UF_Embarque","varchar(2)");
Criar_Campo("T007_Exportacao_Local_Embarque","varchar(60)");

Alterar_Tipo_Campo("T007_Codigo_CFOP","varchar(30)");

Alterar_Tipo_Campo("T003_Flag_Perdido","varchar(1)","default 'N'");

Alterar_Tipo_Campo("D006_Codigo_CFOP","varchar(30)");
Alterar_Tipo_Campo("D006_Flag_Venda_Compra_Outros","varchar(2)");

Alterar_Tipo_Campo("D006_Flag_Deb_Crd_ICMS","varchar(1)","default 'S'");

if ($_SESSION[Modulo_NFe]=='S')
{
    mysql_query("update D006 set D006_Flag_Deb_Crd_ICMS='S' where D006_Flag_Deb_Crd_ICMS!='S'");
}

Criar_Campo("D006_Descricao_Comercial","varchar(50)");
Criar_Campo("D006_D014_Id"," int(10) unsigned after D006_Id");
Criar_Campo("D006_Flag_Isento_PIS","enum('S','N') default 'N'");
Criar_Campo("D006_Situacao_Tributaria_PIS","varchar(2) default '01'");
Criar_Campo("D006_Flag_Isento_COFINS","enum('S','N') default 'N'");
Criar_Campo("D006_Situacao_Tributaria_COFINS","varchar(2) default '01'");
Criar_Campo("D006_Flag_ST_VA","varchar(1) default 'S'");
Criar_Campo("D006_Flag_Forma_Calculo_PIS","enum('V','L') default 'V' after D006_Flag_Isento_PIS");
Criar_Campo("D006_Flag_Forma_Calculo_COFINS","enum('V','L') default 'V' after D006_Flag_Isento_COFINS");

Criar_Campo("T008_Flag_ST","varchar(1) default 'N'");
Criar_Campo("T008_Flag_Calculo_Manual","varchar(1) default 'N'");
Criar_Campo("T008_ST_VA","Decimal(10,2) default '0'");
Criar_Campo("T008_Valor_Custo_Compra_Unitario","Decimal(14,5) default '0'");

Criar_Campo("D053_Flag_ST","varchar(1) default 'N'");
Criar_Campo("D053_ST_ICMS_Interno","decimal(5,2) default '0'");
Criar_Campo("D053_ST_VA","decimal(10,2) default '0'");
Criar_Campo("D053_ST_VA_2","decimal(10,2) default '0'");

Criar_Campo("T004_ST_ICMS_Interno","decimal(5,2) default 0 after T004_Flag_ST");
Criar_Campo("T004_ICMS_Inter_Estadual","decimal(5,2) default 0 after T004_Flag_ST");
Criar_Campo("T004_Flag_Isento_PIS","enum('S','N') default 'N' after T004_Situacao_Tributaria_PIS");
Criar_Campo("T004_Flag_Isento_COFINS","enum('S','N') default 'N' after T004_Situacao_Tributaria_COFINS");
Criar_Campo("T004_D082_Id","int(10)");
Criar_Campo("T004_Valor_Preco_Original","decimal(10,2)");
Criar_Campo("T004_Percentual_Desconto","decimal(10,4)");

Criar_Campo("T004_Flag_ST","varchar(1) default 'N'");
Criar_Campo("T004_ST_VA","Decimal(10,2) default '0'");
Criar_Campo("T004_Valor_Base_ST","Decimal(10,2) default '0'");
Criar_Campo("T004_Valor_ST","Decimal(10,2) default '0'");
Criar_Campo("T004_Valor_Base_ST_Confirmado","Decimal(10,2) default '0'");
Criar_Campo("T004_Valor_ST_Confirmado","Decimal(10,2) default '0'");
Criar_Campo("T004_Reordenar","int(1) default '1'");

Criar_Campo("T005_Flag_Lista_Preco","varchar(1) default ' '");
Criar_Campo("D024_Flag_Lista_Preco","varchar(1) default ' '");
Criar_Campo("D024_Flag_Tempo","varchar(2) default '  '");
Criar_Campo("D024_Flag_Inativo_Comercial","varchar(1) default 'N'");
Alterar_Campo("D024_Flag_Inativo_Comercial","D024_Flag_Comercial","varchar(1) default ''");
Criar_Campo("D024_Data_Cadastro","datetime default '0000-00-00 00:00:00'");
Criar_Campo("D024_Data_Ativo","date default '0000-00-00'");
Criar_Campo("D024_Data_Inativo","date default '0000-00-00'");
Criar_Campo("D024_D085_Id","int(11)");
Criar_Campo("D085_D021_Id","int(11) not null");
Criar_Campo("D024_Flag_Cliente","varchar(1) default ' '");
Criar_Campo("D024_Flag_Fornecedor","varchar(1) default ' '");
Criar_Campo("D024_Horario_Entrega","varchar(30) default ' '");
Criar_Campo("D024_Dias_Carencia_Pagamento","int(2) default 0");
Criar_Campo("D024_Dia_Semana_Pagamento","int(1) default 0");
Criar_Campo("D024_Dia_Mes_Pagamento","varchar(30) default ' '");
Criar_Campo("D024_Tipo_Pagamento","varchar(3) default ' '");
Criar_Campo("D024_Percentual_Desconto_Tabela","decimal(10,4) default 0");
Criar_Campo("D024_Web_Site","varchar(50) default ' '");
Criar_Campo("D024_Condicoes_Pagamento","varchar(50) default ' '");
Criar_Campo("D024_D014_Id","int(11) after D024_D021_Id");
Criar_Campo("D024_Flag_Ativo","enum('S','N') default 'S' after D024_Data_Irregular");
mysql_query("update D024 set D024_Flag_Ativo='N' where D024_Data_Irregular!='0000-00-00'");
mysql_query("update D024 set D024_Flag_Ativo='S' where D024_Data_Irregular='0000-00-00'");
Criar_Campo("D024_Flag_Restricao","enum('S','N') default 'N'");
Criar_Campo("D024_Observacao_Financeiro","text");
Criar_Campo("D024_T007_Id","int(10) default 0");
Criar_Campo("T003_Observacao_Comercial","text");

Criar_Campo("D014_Dia_Vencimento","varchar(2)");

Criar_Campo("T006_Flag_Faturar","varchar(1) default 'N'");
Criar_Campo("T006_Flag_Dividir","varchar(1) default 'N'");

Criar_Campo("D024_D059_Id","int(10) default '0'");
Criar_Campo("D024_D088_Id","int(10) default '0'");
Criar_Campo("D024_Situacao_Tributaria_PIS_COFINS","varchar(2)");

Criar_Campo("T002_Data_Baixa","date default '0000-00-00'");
Criar_Campo("T002_Data_Vencimento_Original","date default '0000-00-00'");
Criar_Campo("T002_Valor_Recebido","decimal(12,2) default '0.00'");
Criar_Campo("T002_T002_Id_Agrupado","int(10) default 0 after T002_Id ");
Alterar_Tipo_Campo("T002_Agrupamento","varchar(250)","default ''");
Alterar_Tipo_Campo("T002_Numero_Duplicata","varchar(255)");
Alterar_Tipo_Campo("T002_Numero_Documento","varchar(255)");

Criar_Campo("T005_Flag_Aguardar_Liberacao","int(1) default 0");
Criar_Campo("T005_Observacao_Comercial","text");
Criar_Campo("D027_D071_Id","int(11) default NULL after D027_Id");
Criar_Campo("T002_D073_Id","int(10) unsigned after T002_Id");
Criar_Campo("T015_D073_Id","int(10) unsigned after T015_Id");
Criar_Campo("T103_T015_Id","int(10) unsigned after T103_Id");
Criar_Campo("T103_D031_Id","int(10) unsigned after T103_T015_Id");
Criar_Campo("D027_Valor_Minimo_Parcela","decimal(10,2) default 0");

Criar_Campo("T015_Codigo_Barras","varchar(100) not null");

Criar_Campo("T015_Flag_Boleto","enum('S','N') default 'N'");
Criar_Campo("T015_Valor_Pago","decimal(10,2) default '0.00'");
Criar_Campo("T015_Data_Baixa","date default '0000-00-00'");
Alterar_Tipo_Campo("T015_Observacao","text","default ''");
Alterar_Tipo_Campo("T008_Quantidade","decimal(12,5)","default '0'");

Criar_Campo("D105_Modificados","varchar(30)");
Criar_Campo("D105_Flag_Processar","varchar(1) default 'N'");
Criar_Campo("D105_Adicionar","varchar(100)");

Criar_Campo("D069_Regras","varchar(30)");
Criar_Campo("D069_Flag_Travado","varchar(1) default 'N'");

Alterar_Tipo_Campo("D069_Descricao","varchar(100)","default ''");
Alterar_Tipo_Campo("D069_Descricao_Empresa","varchar(100)","default ''");

Criar_Campo("D001_Flag_Tipo","enum('PRODUZIDO','COMPRADO')");
Criar_Campo("D001_Codigo_Barras","varchar(13)");
Criar_Campo("D001_Peso_Total_Kg","decimal(10,3) default '0.000'");
Criar_Campo("D001_Lote_Economico","decimal(10,3) default '0.000'");
Criar_Campo("D001_Qte_Embalagem","decimal(10,3) default '0.000'");
Criar_Campo("T002_Flag_Reparcelar","enum('S','N') default 'N'");
Criar_Campo("T013_Tipo","int(2)");
Criar_Campo("T013_Flag_Validacoes","enum('S','N') default 'S'");
Criar_Campo("T002_Data_Previsao_Recebimento","date");
Criar_Campo("T002_Observacao_Cobranca","tinytext");
Criar_Campo("T013_Chave_Acesso_Nfe","varchar(50) default ''");
Criar_Campo("T013_Numero_Recibo_Nfe","varchar(20) default ''");
Criar_Campo("T013_Numero_Protocolo_Nfe","varchar(20) default ''");
Criar_Campo("T013_Retorno_Nfe","text");
Criar_Campo("T013_Data_Hora_Protocolo_Nfe","datetime");
Criar_Campo("T013_Ambiente_Nfe","varchar(5) default ''");
Criar_Campo("T013_Codigo_Servico","varchar(100) default ''");
Criar_Campo("T013_Valor_Credito_IPTU","decimal(10,2) default '0.00'");
Criar_Campo("T013_Flag_Calcular","enum('S','N') default 'S'");

Criar_Campo("T098_Valor_Custo_Unitario","decimal(15,5) default '0.00'");
Criar_Campo("T098_Observacao","longtext");
Criar_Campo("T098_Atualizacao","varchar(10)");
Criar_Campo("T098_D049_ICF","decimal(10,5) default '0'");
Criar_Campo("T098_D049_IPV","decimal(10,5) default '0'");
Criar_Campo("T098_D049_Valor_Preco_Tabela","decimal(10,5) default '0'");
Criar_Campo("T098_D049_Aliquota_ICMS_Tabela","decimal(10,5) default '0'");
Criar_Campo("T098_D049_Percentual_Desconto_Tabela","decimal(10,5) default '0'");

Criar_Campo("C007_Estilo","varchar(30)");
Criar_Campo("C007_Observacao","text");
Criar_Campo("C007_Celular","varchar(30)");
Alterar_Tipo_Campo("C007_Flag_Vendedor","enum('','S','V','D')","default ''");
Criar_Campo("C007_C007_Id","int(11) unsigned after C007_Id");
Criar_Campo("C007_Flag_Comercial","varchar(1)");
Criar_Campo("C007_Flag_Mostrar_Boleto","enum('S','N') default 'N'");
Criar_Campo("C007_Senha_Especial","varchar(20) default ''");

Criar_Campo("D001_Flag_Permite_Venda_Sem_Estoque","enum('S','N') default 'N'");
Criar_Campo("D001_Flag_Visualizar_Site","enum('S','N') default 'S'");

mysql_query("update C007 set C007_Estilo='estilo1' where C007_Estilo is null or C007_Estilo=''");

Alterar_Tipo_Campo("T030_Valor_Custo_Unitario","decimal(14,5)","default 0");

Criar_Campo("C020_Loja_Virtual_Visitante_Consultar_Produto","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Visitante_Ver_Preco","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Visitante_Comprar","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Visitante_Pagar","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Visitante_Cadastrar","enum('S','N') default 'S'");

Criar_Campo("C020_Loja_Virtual_Cliente_Ver_Preco","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Cliente_Comprar","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Cliente_Pagar","enum('S','N') default 'S'");
Criar_Campo("C020_Loja_Virtual_Nome_Email","varchar(100)");
Criar_Campo("C020_Loja_Virtual_Email","varchar(60)");
Criar_Campo("C020_Loja_Virtual_Dominio","varchar(100)");
Criar_Campo("C020_Loja_Virtual_Formato_Lista","enum('S','N') default 'N'");
Criar_Campo("C020_Prazo_Dias_Inativar_Cliente","int(3) default 0");

Criar_Campo("T064_Flag_Tipo","enum('Orçamento','Pedido') default 'Pedido'");

Criar_Campo("D083_D049_Id","int(11)");

Criar_Campo("D083_Flag_Cliente","varchar(1)");
Criar_Campo("D083_Flag_Fornecedor","varchar(1)");

Criar_Campo("D049_Flag_Tipo","varchar(2)");
Criar_Campo("T006_Flag_Tipo_Produto","varchar(2)");
Criar_Campo("T006_D049_Id","int(11) unsigned");

Criar_Campo("D049_Valor_Preco_Tabela","decimal(14,5) default 0");
Criar_Campo("D049_Percentual_Desconto_Tabela","decimal(10,5) default 0");
Criar_Campo("D049_Aliquota_ICMS_Tabela","decimal(6,2) default 0");

Criar_Campo("D049_Flag_ICF_Temporario","varchar(1) default ''");

Criar_Campo("T059_Valor_Custo_Unitario_1","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_2","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_3","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_4","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_5","decimal(14,5) default 0");

Criar_Campo("T059_Flag_Gerar_OC_1","varchar(1) default ''");
Criar_Campo("T059_Flag_Gerar_OC_2","varchar(1) default ''");
Criar_Campo("T059_Flag_Gerar_OC_3","varchar(1) default ''");
Criar_Campo("T059_Flag_Gerar_OC_4","varchar(1) default ''");
Criar_Campo("T059_Flag_Gerar_OC_5","varchar(1) default ''");

Criar_Campo("T059_Quantidade_1","decimal(10,5) default '0'");
Criar_Campo("T059_Quantidade_2","decimal(10,5) default '0'");
Criar_Campo("T059_Quantidade_3","decimal(10,5) default '0'");
Criar_Campo("T059_Quantidade_4","decimal(10,5) default '0'");
Criar_Campo("T059_Quantidade_5","decimal(10,5) default '0'");
Criar_Campo("T059_Quantidade_6","decimal(10,5) default '0'");

Criar_Campo("T059_Valor_Custo_Unitario_Online_1","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_Online_2","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_Online_3","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_Online_4","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_Online_5","decimal(14,5) default 0");
Criar_Campo("T059_Valor_Custo_Unitario_Online_6","decimal(14,5) default 0");

Criar_Campo("T059_Flag_Custo_Online_1","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T059_Flag_Custo_Online_2","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T059_Flag_Custo_Online_3","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T059_Flag_Custo_Online_4","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T059_Flag_Custo_Online_5","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T059_Flag_Custo_Online_6","DateTime default '0000-00-00 00:00:00'");

Criar_Campo("T059_D009_Id_1","int(11) default '0'");
Criar_Campo("T059_D009_Id_2","int(11) default '0'");
Criar_Campo("T059_D009_Id_3","int(11) default '0'");
Criar_Campo("T059_D009_Id_4","int(11) default '0'");
Criar_Campo("T059_D009_Id_5","int(11) default '0'");
Criar_Campo("T059_D009_Id_6","int(11) default '0'");

Criar_Campo("T010_Flag_Marca_Individual","varchar(1) default 'N'");
Criar_Campo("T010_Flag_Quantidade_Individual","varchar(1) default 'N'");

Criar_Campo("T010_Flag_EMail_Enviado_1","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T010_Flag_EMail_Enviado_2","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T010_Flag_EMail_Enviado_3","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T010_Flag_EMail_Enviado_4","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T010_Flag_EMail_Enviado_5","DateTime default '0000-00-00 00:00:00'");
Criar_Campo("T010_Flag_EMail_Enviado_6","DateTime default '0000-00-00 00:00:00'");

Criar_Campo("T010_Valor_Cotacao_1","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Cotacao_2","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Cotacao_3","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Cotacao_4","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Cotacao_5","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Cotacao_6","decimal(10,2) default '0.00'");

Criar_Campo("T010_Valor_Confirmado_1","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Confirmado_2","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Confirmado_3","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Confirmado_4","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Confirmado_5","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_Confirmado_6","decimal(10,2) default '0.00'");

Criar_Campo("T010_Valor_OC_1","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_OC_2","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_OC_3","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_OC_4","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_OC_5","decimal(10,2) default '0.00'");
Criar_Campo("T010_Valor_OC_6","decimal(10,2) default '0.00'");

Criar_Campo("T010_Numero_OC_1","varchar(50) default ''");
Criar_Campo("T010_Numero_OC_2","varchar(50) default ''");
Criar_Campo("T010_Numero_OC_3","varchar(50) default ''");
Criar_Campo("T010_Numero_OC_4","varchar(50) default ''");
Criar_Campo("T010_Numero_OC_5","varchar(50) default ''");
Criar_Campo("T010_Numero_OC_6","varchar(50) default ''");

Criar_Campo("T010_C007_Id_1_Analista","int(11) default 0");
Criar_Campo("T010_C007_Id_2_Analista","int(11) default 0");
Criar_Campo("T010_C007_Id_3_Analista","int(11) default 0");
Criar_Campo("T010_C007_Id_4_Analista","int(11) default 0");
Criar_Campo("T010_C007_Id_5_Analista","int(11) default 0");
Criar_Campo("T010_C007_Id_6_Analista","int(11) default 0");

Criar_Campo("T010_C007_Id_Melhor_Preco_Analista","int(11) default 0");

Criar_Campo("T010_Valor_1_Aprovado","decimal(10,2) default 0.00");
Criar_Campo("T010_Valor_2_Aprovado","decimal(10,2) default 0.00");
Criar_Campo("T010_Valor_3_Aprovado","decimal(10,2) default 0.00");
Criar_Campo("T010_Valor_4_Aprovado","decimal(10,2) default 0.00");
Criar_Campo("T010_Valor_5_Aprovado","decimal(10,2) default 0.00");
Criar_Campo("T010_Valor_6_Aprovado","decimal(10,2) default 0.00");

Criar_Campo("T010_Valor_Melhor_Preco_Aprovado","decimal(10,2) default 0.00");
Criar_Campo("T137_C004_Id","int(10) default 0 after T137_Id");
Criar_Campo("T137_Valor_Meta","decimal(10,2) default 0");
Criar_Campo("T137_Valor_Comissao","decimal(10,2) default 0");
Criar_Campo("T137_Percentual_Fechamento","decimal(10,2) default 0");
Criar_Campo("T137_Percentual_Aproveitamento","decimal(10,2) default 0");
Criar_Campo("T137_Formula_Comissao","text");
Criar_Campo("C025_C026_Id","int(5) default '0'");

Alterar_Tipo_Campo("T137_Percentual_Fechamento","decimal(10,2)","default 0");

Criar_Campo("C004_PEDVEN_Botao_Liberar_Almoxarifado","varchar(1) ");
Criar_Campo("C004_E_Mail","varchar(50) default ''");
Alterar_Tipo_Campo("C004_Numero","varchar(20)");
Alterar_Tipo_Campo("C004_Linhas_Obs_Fiscal","int(3)");
Alterar_Tipo_Campo("C004_Casas_Decimais_Preco","int(2)","default '2'");
Criar_Campo("C004_Flag_Ativo","varchar(1)");
Alterar_Tipo_Campo("C018_Formatacao","varchar(1)");
Alterar_Tipo_Campo("C018_Casas_Decimais","int(2)","default '2'");
Alterar_Tipo_Campo("D001_D005_Id","int(10) unsigned");
Alterar_Tipo_Campo("D003_Nome_Linha","varchar(30)");
Alterar_Tipo_Campo("D074_Quantidade","decimal(12,5)");
//Alterar_Tipo_Campo("D005_Id","int(11) unsigned","primary key auto_increment before D005_Classificacao_Fiscal");
Criar_Campo("D005_Flag_VA","varchar(1)");
Criar_Campo("D005_D088_Id","int(11) after D005_Id");
Criar_Campo("D059_Simples_Nacional","enum('S','N') default 'N'");
Alterar_Tipo_Campo("D059_Descricao_Origem","varchar(200)");
Criar_Campo("D005_Valor_VA","decimal(10,2)","default 0");
Alterar_Tipo_Campo("D006_Descricao","varchar(51)");
Criar_Campo("D003_D082_Id","int(10)","default 0");

Criar_Campo("D049_Flag_Padrao","varchar(1)","enum('S','N') default 'S'");

Criar_Campo("D082_Percentual_Acrescimo","decimal(10,2)","default 0");
Criar_Campo("D082_Codigo_Padrao","varchar(10)","default ''");

Criar_Campo("T071_Quantidade_Etiqueta","decimal(10,2)","default '0.00'");
Criar_Campo("T071_Quantidade_Itens_Etiqueta","decimal(10,2)","default '0.00'");

Criar_Campo("D009_Valor_Custo_Importado","decimal(10,2)","default 0");
Criar_Campo("D009_Valor_Custo_Compra","decimal(12,5)","default 0");
Criar_Campo("D009_Flag_Travar_Custo","varchar(1)","default 'N'");
Criar_Campo("D009_Flag_Cotar","varchar(1)","default 'N'");
Criar_Campo("D009_Dados_Adicionais","text");
Criar_Campo("D009_Valor_Melhor_Custo","decimal(12,5)","default 0");
Criar_Campo("D009_D070_Id","int(11)","default 0");
Criar_Campo("D009_Quantidade_Estoque_Liquido","decimal(12,2)","default 0");
Criar_Campo("T006_Quantidade_Estoque","decimal(12,2)","default 0");
Criar_Campo("T006_Quantidade_Estoque_Padrao","decimal(12,2)","default 0");
Criar_Campo("T131_Teto_Comissao_Minimo","decimal(12,2)","default 0");
Criar_Campo("T131_D021_Id","int(11)","default 0");
Criar_Campo("D021_C007_Id","int(11)","default 0");
Criar_Campo("D009_Quantidade_Consumo","decimal(10,3)","default 0");
Criar_Campo("D009_Clientes_Consumo","decimal(5)","default 0");
//Criar_Campo("T004_D001_Id","int(11) after T004_D009_Id");
Criar_Campo("T010_D024_Id_1","int(11) after T010_D024_Id");
Criar_Campo("T010_D024_Id_2","int(11) after T010_D024_Id_1");
Criar_Campo("T010_D024_Id_3","int(11) after T010_D024_Id_2");
Criar_Campo("T010_D024_Id_4","int(11) after T010_D024_Id_3");
Criar_Campo("T010_D024_Id_5","int(11) after T010_D024_Id_4");
Criar_Campo("T010_Flag_Tipo","varchar(20) default ''");
Criar_Campo("T011_Flag_Tipo","varchar(20) default ''");
Criar_Campo("T010_Desc1","decimal(10,2) default 0.00");
Criar_Campo("T010_Desc2","decimal(10,2) default 0.00");
Criar_Campo("T010_Desc3","decimal(10,2) default 0.00");
Criar_Campo("T010_Desc4","decimal(10,2) default 0.00");
Criar_Campo("T010_Desc5","decimal(10,2) default 0.00");
Criar_Campo("T010_Desc6","decimal(10,2) default 0.00");

Criar_Campo("T007_Validar_NFe","enum('S','N') default 'S'");
Criar_Campo("T007_Flag_Calcular","enum('S','N') default 'S'");

Criar_Campo("T007_T134_Id","int(5) default '0'");

Criar_Campo("C020_Limite_Compra_Emergencia_Supervisor","decimal(10,2) default 0.00");
Criar_Campo("C020_Limite_Compra_Estoque_Supervisor","decimal(10,2) default 0.00");
Criar_Campo("C020_Mostrar_Todas_CFOP_Nota","enum('N','S') default 'N'");
Criar_Campo("D009_Flag_Preco_Tabelado","varchar(1)","default 'N'");
Criar_Campo("D009_Valor_Custo_Ultima_Compra","decimal(14,5)","default 0");

Alterar_Tipo_Campo("D024_C007_Id","int(5)");
Criar_Campo("D024_Flag_Tipo_Origem","int(2)");
Criar_Campo("D024_Flag_Curva_Abc","enum('A','B','C','D')");
Criar_Campo("D024_Flag_Frequencia","int(6) default 0");
Criar_Campo("D024_Data_Atualizacao","datetime","default '0000-00-00 00:00:00'");
Criar_Campo("D024_Meses_Inativo","decimal(6,2)","default '0.00'");
Alterar_Tipo_Campo("D024_Flag_Pre_Cadastro","enum('S','N')","default 'N'");
Alterar_Tipo_Campo("D024_Data_Cadastro","datetime");
Criar_Campo("D041_Valor_Custo","decimal(10,2)","default 0");
Alterar_Tipo_Campo("D042_Quantidade","decimal(10,3)","default 0");
//Criar_Campo("L001_Id","int(11) primary key auto_increment before L001_D009_Id");
Criar_Campo("L001_Qte1","decimal(10,3)","default 0");
Criar_Campo("L001_Qte2","decimal(10,3)","default 0");
Criar_Campo("L001_Estoque_Fisico","decimal(10,3)","default 0");
Criar_Campo("L001_Estoque_Inicial","decimal(10,3)","default 0");
Criar_Campo("T002_Flag_Status","varchar(1) default '1'");
Alterar_Tipo_Campo("T002_Observacao","text");
Alterar_Tipo_Campo("T002_Flag_Reparcelar","enum('S','N')","default 'N'");
Alterar_Tipo_Campo("T002_Flag_Enviar_Banco","varchar(10)");
Alterar_Tipo_Campo("T003_Telefone","varchar(40)");
Alterar_Tipo_Campo("T004_Valor_Preco_Unitario","decimal(15,5)","default 0");
Alterar_Tipo_Campo("T004_Valor_Preco_Unitario_Confirmado","decimal(15,5)","default 0");
Alterar_Tipo_Campo("T004_Valor_Preco_Sem_Desconto_Unitario","decimal(15,5)","default 0");
Alterar_Tipo_Campo("T004_Valor_Total_Preco","decimal(20,2)","default 0");
Alterar_Tipo_Campo("T004_Valor_Total_Desconto","decimal(20,10)","default 0");
Alterar_Tipo_Campo("T004_Observacao","text");
Alterar_Tipo_Campo("T005_Aliquota_ICMS","decimal(10,2)","default 0");
Alterar_Tipo_Campo("T006_T006_Id","int(10) unsigned");
Alterar_Tipo_Campo("T006_Valor_Preco_Sem_Desconto_Unitario","decimal(19,5)","default 0");
Alterar_Tipo_Campo("T006_Valor_Preco_Unitario","decimal(19,5)","default 0");
Alterar_Tipo_Campo("T006_Valor_Total_Preco","decimal(20,2)","default 0");
Criar_Campo("T006_Quantidade_Dividido","decimal(10,2)","default 0 after T006_Quantidade_Dividir");
Criar_Campo("T006_Quantidade_Etiqueta","Decimal(10,3) default '0' after T006_Quantidade_Separacao");
Criar_Campo("T006_Quantidade_Itens_Etiqueta","Decimal(10,3) default '0' after T006_Quantidade_Etiqueta");

Alterar_Tipo_Campo("T007_Valor_Frete","decimal(19,2)","default 0");
Alterar_Tipo_Campo("T007_Valor_Total_Base_ICMS","decimal(20,5)","default 0");
Criar_Campo("T007_Total_Peso","decimal(15,5)","default 0");
Alterar_Tipo_Campo("T008_Flag_Classificacao_Fiscal","varchar(5)");
Alterar_Tipo_Campo("T008_Flag_Situacao_Tributaria","varchar(4)");
Alterar_Tipo_Campo("T006_Flag_Situacao_Tributaria","varchar(4)");
Alterar_Tipo_Campo("T004_Flag_Situacao_Tributaria","varchar(4)");
Criar_Campo("T008_D059_Id","int(11) unsigned after T008_Id");
Criar_Campo("T008_Modalidade_ICMS","int(1) default '3' after T008_Valor_Base_ICMS");
Criar_Campo("T008_Modalidade_ICMS_Substituicao","int(1) default '4' after T008_Valor_Base_ICMS_Substituicao");
Criar_Campo("T008_Percentual_Base_Operacao_Propria","decimal(5,2) default '0' after T008_Valor_ICMS");
Criar_Campo("T008_Aliquota_ICMS_Credito","decimal(5,2) default '0' after T008_Valor_ICMS");
Criar_Campo("T008_Valor_ICMS_Credito","decimal(15,2) default '0' after T008_Aliquota_ICMS_Credito");
Criar_Campo("T008_UF_ICMS_Substituicao_Devido","char(2) after T008_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T008_Valor_ICMS_Substituicao_Destino","decimal(15,2) default 0 after T008_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T008_Valor_Base_ICMS_Substituicao_Destino","decimal(15,2) default 0 after T008_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T008_Valor_ICMS_Substituicao_Retido","decimal(15,2) default 0 after T008_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T008_Valor_Base_ICMS_Substituicao_Retido","decimal(15,2) default 0 after T008_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T008_Situacao_Tributaria_IPI","int(2) unsigned zerofill after T008_Valor_IPI");

Alterar_Tipo_Campo("S003_Email","varchar(100)","default ''");
Criar_Campo("S003_Id","int(11)");
Criar_Campo("S003_Empresa","varchar(50) default ''");
Criar_Campo("S003_Contato","varchar(50) default ''");
Criar_Campo("S003_Telefone","varchar(30) default ''");
Criar_Campo("S003_Segmento","varchar(30) default ''");
Criar_Campo("S003_Observacao","text default ''");
Criar_Campo("S003_C007_Id","int(10) default 0");
Criar_Campo("S003_Indicacao","varchar(50) default ''");
Criar_Campo("S003_Sistema_Atual","varchar(50) default ''");

Criar_Campo("T006_D059_Id","int(11) unsigned after T006_Id");
Criar_Campo("T006_Modalidade_ICMS","int(1) default '3' after T006_Valor_Base_ICMS");
Criar_Campo("T006_Modalidade_ICMS_Substituicao","int(1) default '4' after T006_Valor_Base_ICMS_Substituicao");
Criar_Campo("T006_Percentual_Base_Operacao_Propria","decimal(5,2) default '0' after T006_Valor_ICMS");
Criar_Campo("T006_Aliquota_ICMS_Credito","decimal(5,2) default '0' after T006_Valor_ICMS");
Criar_Campo("T006_Valor_ICMS_Credito","decimal(15,2) default '0' after T006_Aliquota_ICMS_Credito");
Criar_Campo("T006_UF_ICMS_Substituicao_Devido","char(2) after T006_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T006_Valor_ICMS_Substituicao_Destino","decimal(15,2) default 0 after T006_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T006_Valor_Base_ICMS_Substituicao_Destino","decimal(15,2) default 0 after T006_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T006_Valor_ICMS_Substituicao_Retido","decimal(15,2) default 0 after T006_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T006_Valor_Base_ICMS_Substituicao_Retido","decimal(15,2) default 0 after T006_Valor_ICMS_Substituicao_Retencao");
Criar_Campo("T006_Situacao_Tributaria_IPI","int(2) unsigned zerofill after T006_Valor_IPI");

Criar_Campo("T004_D059_Id","int(11) unsigned after T004_Id");
Criar_Campo("T004_Modalidade_ICMS","int(1) default '3' after T004_Valor_Base_ICMS");
Criar_Campo("T004_Modalidade_ICMS_Substituicao","int(1) default '4' after T004_Valor_Base_ST");
Criar_Campo("T004_Percentual_Base_Operacao_Propria","decimal(5,2) default '0' after T004_Valor_ICMS");
Criar_Campo("T004_Aliquota_ICMS_Credito","decimal(5,2) default '0' after T004_Valor_ICMS");
Criar_Campo("T004_Valor_ICMS_Credito","decimal(15,2) default '0' after T004_Aliquota_ICMS_Credito");
Criar_Campo("T004_UF_ICMS_Substituicao_Devido","char(2) after T004_Valor_ST_Confirmado");
Criar_Campo("T004_Valor_ICMS_Substituicao_Destino","decimal(15,2) default 0 after T004_Valor_ST_Confirmado");
Criar_Campo("T004_Valor_Base_ICMS_Substituicao_Destino","decimal(15,2) default 0 after T004_Valor_ST_Confirmado");
Criar_Campo("T004_Valor_ICMS_Substituicao_Retido","decimal(15,2) default 0 after T004_Valor_ST_Confirmado");
Criar_Campo("T004_Valor_Base_ICMS_Substituicao_Retido","decimal(15,2) default 0 after T004_Valor_ST_Confirmado");
Criar_Campo("T004_Situacao_Tributaria_IPI","int(2) unsigned zerofill after T004_Valor_IPI");
Criar_Campo("T004_Percentual_Reducao_ICMS_ST","decimal(5,2) default '0' after T004_Valor_Base_ST");

Alterar_Tipo_Campo("T012_Quantidade","decimal(10,3)","default 0");
Criar_Campo("T012_Desconto_Tabela","decimal(10,2)","default 0");
Alterar_Tipo_Campo("T012_Observacao","tinytext");
Alterar_Tipo_Campo("T012_Descricao_Produto","varchar(50)");
Criar_Campo("T012_T006_Id","int(10) unsigned","after T012_Id");
Criar_Campo("T059_T006_Id","int(10) unsigned","after T059_Id");

Criar_Campo("T135_Valor_Renovacao","decimal(10,2)","default 0");
Criar_Campo("T135_Flag_Renovado","varchar(1)","default ''");

Criar_Campo("D041_C004_Id","int(10) unsigned");

Criar_Campo("C007_IP_Acesso","varchar(200) default ''");

Criar_Campo("T007_Empresa_Usuaria_CRT","int(1)","");
Criar_Campo("T007_Flag_XML_Enviado","enum('S','N')","default 'N'");

Alterar_Tipo_Campo("T013_Valor_Desconto","decimal(12,6)","default 0");
Alterar_Tipo_Campo("T013_Tipo","varchar(10)");
Alterar_Tipo_Campo("T014_Valor_Custo_Unitario","decimal(16,8)","default 0");

Alterar_Tipo_Campo("T015_Flag_Boleto","enum('S','N')","DEFAULT 'N'");
Criar_Campo("T119_C007_Id","int(10)","after T119_Id");
Criar_Campo("T030_C007_Id","int(5)","after T030_Id");
Criar_Campo("T030_Observacao","tinytext");
Criar_Campo("T030_Data_Inclusao","datetime");
Alterar_Tipo_Campo("T039_Numero","varchar(10)");
Alterar_Tipo_Campo("T043_Quantidade","decimal(10,3)","default 0");
Alterar_Tipo_Campo("T048_Quantidade","decimal(10,2)","default 0");
Alterar_Tipo_Campo("T086_Quantidade","decimal(14,5)","default 0");
Alterar_Tipo_Campo("T100_Data","datetime");
Alterar_Tipo_Campo("C029_Descricao_Acesso","varchar(150)");
Criar_Campo("C029_Grupo","varchar(10)");
Criar_Campo("C029_Grupo_NFe","varchar(10)");
Criar_Campo("C029_Flag_NFe","varchar(1) default 'N'");
Criar_Campo("T048_Observacao","text");
Criar_Campo("T048_T006_Id","int(10) unsigned after T048_Id");
Criar_Campo("T071_Observacao","text");
Criar_Campo("D042_Observacao","text");
Criar_Campo("C029_Entidade","char(4)");
Criar_Campo("C029_Tela","varchar(150)");
Criar_Campo("C029_Opcao","varchar(30)");

Criar_Campo("D082_C008_Id","int(10)","after D082_Id");

Criar_Campo("C020_Imprimir_Marca_NF","enum('S','N') default 'N'");

mysql_query("update C020 set C020_Imprimir_Marca_NF='N'");

Criar_Campo("C020_Cliente_Telefone_Obrigatorio","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Representante_Obrigatorio","enum('S','N') default 'S'");
Criar_Campo("C020_Orcamento_Representante_Obrigatorio","enum('S','N') default 'S'");
Criar_Campo("C020_Consulta_Paginacao","enum('S','N') default 'S'");
Criar_Campo("C020_Consulta_Linhas_Paginacao","int(4) default 15");
Criar_Campo("C020_Inclusao_Produtos_Simples","enum('S','N') default 'S'");
Criar_Campo("C020_IPV_Atacado","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Varejo","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Revenda_Minimo","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Atacado_Minimo","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Varejo_Minimo","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Revenda_Minimo_Supervisor","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Atacado_Minimo_Supervisor","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Varejo_Minimo_Supervisor","decimal(10,4) default '0.0000'");
Criar_Campo("C020_IPV_Custo","decimal(10,4) default '0.0000'");

Alterar_Tipo_Campo("D037_Unidade","varchar(6)");

Criar_Campo("C020_NFe_Obrigatorio_Cadastro_Produto","enum('S','N') default 'S'");
Criar_Campo("C020_NFe_Habilitada","enum('S','N') default 'S'");
Criar_Campo("C020_NFe_Treinamento","enum('S','N') default 'N'");
Criar_Campo("C020_NFe_Tamanho_Campo_Descricao","int(3) default 60");
Criar_Campo("C020_NFe_Tipo_DANFE","enum('HTML','PDF') DEFAULT 'HTML'");
Criar_Campo("D087_Data_Validade","date");

Criar_Campo("C030_C021_Id","INTEGER UNSIGNED NOT NULL AFTER C030_Id");
Criar_Campo("C030_Acesso_Liberado","enum('S','N') default 'N'");
Criar_Campo("T014_D037_Id","int(10) unsigned","after T014_Id");
Criar_Campo("T014_T129_Id","int(10) unsigned","after T014_Id");
Criar_Campo("T014_Unidade","varchar(5)","after T014_Quantidade");
Criar_Campo("T129_Quantidade_Separacao","decimal(10,3)","default 0");
Criar_Campo("T129_Quantidade_Estoque","decimal(10,3)","default 0");
Criar_Campo("T129_D049_Id","int(10) unsigned","after T129_Id");
Alterar_Tipo_Campo("T008_Unidade","varchar(6)");
Criar_Campo("C020_Modelo_Menu","varchar(30) default 'menu_principal.hardness'");
Criar_Campo("C021_Flag_Visualizar_Opcoes_Desabilitadas","enum('S','N') default 'N'");
Criar_Campo("C021_Visualizar_Pedido","enum('S','N') default 'N'");
Criar_Campo("C021_Visualizar_PrePedido","enum('S','N') default 'N'");
Criar_Campo("T005_Observacao_Separacao","varchar(100) after T005_Flag_Dividido");
Criar_Campo("T005_T064_Id","bigint(20) unsigned after T005_Id");
Criar_Campo("C021_Visualizar_Clientes","enum('S','N') default 'S'");
Criar_Campo("C021_Visualizar_Produtos","enum('S','N') default 'S'");
Criar_Campo("C021_Visualizar_Orcamento","enum('S','N') default 'S'");
Criar_Campo("C021_Visualizar_Expedicao","enum('S','N') default 'S'");
Criar_Campo("C021_Visualizar_Caixa","enum('S','N') default 'S'");
Criar_Campo("C021_Visualizar_Transportadora","enum('S','N') default 'S'");
Criar_Campo("C021_Iniciar_Opcoes_Menu_Abertas","enum('S','N') default 'N'");
Criar_Campo("C021_Pagina_Inicial","varchar(250) default 'principal/pag_inicial.php3'");
Criar_Campo("D024_Numero","varchar(15) after D024_Endereco");
Criar_Campo("D024_Senha_Acesso","varchar(50) AFTER D024_E_Mail");
Criar_Campo("D024_DDD_Telefone_1","varchar(2) AFTER D024_Nome_Contato");

Criar_Campo("D024_D022_Id","int(11) after D024_D021_Id");
Criar_Campo("D024_D046_Id","int(11) after D024_D022_Id");
Criar_Campo("D024_D039_Id","int(11) after D024_D046_Id");
Criar_Campo("D024_D018_Entrega","int(11) after D024_D030_Cobranca");
Criar_Campo("D024_D020_Entrega","int(11) after D024_D018_Entrega");
Criar_Campo("D024_D030_Entrega","int(11) after D024_D020_Entrega");
Criar_Campo("D024_Entrega_Rua","varchar(60)");
Criar_Campo("D024_Entrega_Complemento","varchar(30)");
Criar_Campo("D024_Entrega_Bairro","varchar(25)");
Criar_Campo("D024_Entrega_Cep","varchar(9)");
Criar_Campo("D024_Entrega_Telefone","varchar(20)");
Criar_Campo("D024_Cobranca_Telefone","varchar(20)");
Criar_Campo("D024_Cobranca_Fax","varchar(20)");
Criar_Campo("D024_Entrega_Fax","varchar(20)");
Criar_Campo("D024_Cobranca_Email","varchar(50)");
Criar_Campo("D024_Entrega_Email","varchar(50)");
Criar_Campo("D030_Codigo_Pais","int(4) zerofill");
Alterar_Tipo_Campo("D030_Nome_Pais","varchar(60)");

Criar_Campo("T124_Orcamentos_01","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_02","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_03","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_04","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_05","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_06","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_07","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_08","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_09","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_10","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_11","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_12","decimal(10,2) default 0");
Criar_Campo("T124_Orcamentos_Total","decimal(10,2) default 0");

Criar_Campo("T125_Orcamentos_Valor_01","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_02","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_03","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_04","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_05","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_06","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_07","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_08","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_09","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_10","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_11","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_12","decimal(10,2) default 0");
Criar_Campo("T125_Orcamentos_Valor_Total","decimal(10,2) default 0");

Criar_Campo("D024_Valor_Vendas_Pior_Mes","decimal(10,2) default 0");
Criar_Campo("D024_Valor_Vendas_Melhor_Mes","decimal(10,2) default 0");
Criar_Campo("D024_Data_Ultimo_Orcamento","date default '0000-00-00'");


Criar_Campo("C008_ICF","decimal(10,4)");
Criar_Campo("D082_ICF","decimal(10,4)");

Criar_Campo("C007_Email_SMTP_Servidor","varchar(50)");
Criar_Campo("C007_Email_SMTP_Autenticacao","varchar(10)");
Criar_Campo("C007_Email_SMTP_Usuario","varchar(50)");
Criar_Campo("C007_Email_SMTP_Senha","varchar(50)");
Criar_Campo("C007_Email_SMTP_Porta","varchar(10)");

Criar_Campo("S003_Nome","varchar(50)");
Criar_Campo("S009_Melhorar_Prazo_Entrega","enum('S','N') default 'N'");
Criar_Campo("S009_Melhorar_Servicos","enum('S','N') default 'N'");
Criar_Campo("S009_Melhorar_Embalagem","enum('S','N') default 'N'");
Criar_Campo("S009_Melhorar_Atendimento_Telefonico","enum('S','N') default 'N'");
Criar_Campo("S009_Outros","enum('S','N') default 'N'");


Criar_Campo("D022_Email","varchar(50)");
Criar_Campo("D022_WebSite","varchar(50)");
Criar_Campo("D022_Nome_Fantasia","varchar(60)");
Criar_Campo("D022_Nome_Contato","varchar(30)");
Criar_Campo("T019_T007_Id","int(10) unsigned after T019_Id");
Criar_Campo("T019_T005_Id","int(10) unsigned after T019_Id");
Criar_Campo("T019_D027_Id","int(10) unsigned after T019_Id");
Criar_Campo("T019_D031_Id","int(10) unsigned after T019_Id");
Criar_Campo("T019_Flag_Excluido","varchar(1) default 'N'");
Criar_Campo("T019_Observacao","text default ''");
Criar_Campo("D027_Observacao","text default ''");

Criar_Campo("D020_D084_Id","int(10) unsigned after D020_Id");
Criar_Campo("D084_D021_Id","int(10) not null");
Criar_Campo("D084_C007_Id","int(10) unsigned after D084_Id");
Criar_Campo("D084_C007_Id_Externo","int(10) unsigned after D084_Id");


Criar_Campo("D018_Codigo_IBGE","int(2) AFTER D018_Id");
Criar_Campo("D020_Codigo_IBGE","int(7) AFTER D020_Codigo_Cidade");
Criar_Campo("T007_Numero_Recibo_Nfe","varchar(20) AFTER T007_Numero_Nota_Fiscal");
Criar_Campo("T007_Numero_Protocolo_Nfe","varchar(20) AFTER T007_Numero_Nota_Fiscal");
Criar_Campo("T007_Numero_Protocolo_Cancelamento_Nfe","varchar(20) AFTER T007_Numero_Protocolo_Nfe");
Criar_Campo("T007_Data_Hora_Cancelamento_Nfe","datetime AFTER T007_Numero_Nota_Fiscal");
Criar_Campo("T007_Data_Hora_Recebimento_Nfe","datetime AFTER T007_Numero_Nota_Fiscal");
Criar_Campo("T007_Data_Hora_Protocolo_Nfe","datetime AFTER T007_Numero_Nota_Fiscal");
Criar_Campo("T007_Chave_Acesso_Nfe","varchar(44) AFTER T007_Numero_Nota_Fiscal");
Criar_Campo("T007_Arquivo_XML","text after T007_Chave_Acesso_Nfe");
Criar_Campo("T007_Protocolo_Inutilizacao","varchar(20) AFTER T007_Chave_Acesso_Nfe");
Criar_Campo("T007_Data_Hora_Inutilizacao","datetime AFTER T007_Protocolo_Inutilizacao");
Criar_Campo("T007_Justificativa_Inutilizacao","text AFTER T007_Data_Hora_Inutilizacao");
Criar_Campo("T007_Cliente_Codigo_Estado_IBGE","int(2) AFTER T007_Cliente_Estado");
Criar_Campo("T007_Cliente_Codigo_Cidade_IBGE","int(7) AFTER T007_Cliente_Cidade");
Criar_Campo("T007_Cliente_Endereco_Numero","varchar(60) after T007_Cliente_Endereco");
Criar_Campo("T007_Cliente_Complemento","varchar(60) after T007_Cliente_Endereco_Numero");
Criar_Campo("T007_Cliente_Pais","varchar(60) default 'BRASIL' after T007_Cliente_Codigo_Estado_IBGE");
Criar_Campo("T007_Cliente_Codigo_Pais","int(4) zerofill default '1058' after T007_Cliente_Pais");
Criar_Campo("T007_Cliente_Inscricao_Suframa","varchar(9) after T007_Cliente_Inscricao_Estadual");
Criar_Campo("C004_Ultimo_Numero_Nota_Fiscal_Eletronica","varchar(12) after C004_Ultimo_Numero_Nota_Fiscal");
Criar_Campo("C004_Senha_Certificados","varchar(30)");
Criar_Campo("C004_Localizacao_Certificados","varchar(200)");
Criar_Campo("C004_Certificado_Digital","text after C004_Localizacao_Certificados");
Criar_Campo("C004_Impressora_Comercial","varchar(30)");
Criar_Campo("C004_Impressora_Balcao","varchar(30)");
Criar_Campo("C004_CNAE_Fiscal","varchar(7)");
Criar_Campo("C004_Percentual_PIS","decimal(6,2) default 0");
Criar_Campo("C004_Percentual_COFINS","decimal(6,2) default 0");
Criar_Campo("C004_Codigo_Regime_Tributario","int(1) default '1' after C004_CNAE_Fiscal");
Criar_Campo("T007_Empresa_Usuaria_Codigo_Estado_IBGE","int(2) AFTER T007_Empresa_Usuaria_Estado");
Criar_Campo("T007_Empresa_Usuaria_Codigo_Cidade_IBGE","int(7) AFTER T007_Empresa_Usuaria_Cidade");
Criar_Campo("T007_Ambiente_NFe","enum('1','2') default '2' after T007_Chave_Acesso_Nfe");
Criar_Campo("T007_Versao_NFe","decimal(5,2) default 0");
Alterar_Tipo_Campo("T007_Versao_NFe","decimal(5,2)");

Criar_Campo("C004_Ambiente_NFe","enum('1','2') default '2'");
Criar_Campo("C004_D018_Id","int(11) default '0' after C004_Id");
Criar_Campo("C004_D020_Id","int(10) unsigned after C004_Id");

Criar_Campo("T007_Empresa_Usuaria_CNAE_Fiscal","varchar(7) after T007_Empresa_Usuaria_Inscricao_Estadual");
Criar_Campo("T008_Situacao_Tributaria_PIS","varchar(2) default '01'");
Criar_Campo("T008_Valor_Base_PIS","decimal(20,2) default '0'");
Criar_Campo("T008_Aliquota_PIS","decimal(5,2) default '0'");
Criar_Campo("T008_Valor_PIS","decimal(10,2) default '0'");
Criar_Campo("T008_Situacao_Tributaria_COFINS","varchar(2) default '01'");
Criar_Campo("T008_Valor_Base_COFINS","decimal(20,2) default '0'");
Criar_Campo("T008_Aliquota_COFINS","decimal(5,2) default '0'");
Criar_Campo("T008_Valor_COFINS","decimal(10,2) default '0'");
Criar_Campo("T008_Aliquota_ICMS_ST","decimal(5,2) default '0' after T008_Valor_Base_ICMS_Substituicao");
Criar_Campo("T008_Percentual_Reducao_ICMS_ST","decimal(5,2) default '0' after T008_Valor_Base_ICMS_Substituicao");
Criar_Campo("T008_ST_ICMS_Interno","decimal(5,2) default 0 after T008_Flag_ST");
Criar_Campo("T008_ICMS_Inter_Estadual","decimal(5,2) default 0 after T008_Flag_ST");
Criar_Campo("T008_Flag_Isento_PIS","enum('S','N') default 'N' after T008_Situacao_Tributaria_PIS");
Criar_Campo("T008_Flag_Isento_COFINS","enum('S','N') default 'N' after T008_Situacao_Tributaria_COFINS");
Criar_Campo("T008_Numero_Pedido_Compra","varchar(15)");
Criar_Campo("T008_Item_Pedido_Compra","int(6)");
Criar_Campo("T008_Codigo_Barras","varchar(20) after T008_Codigo_Produto");
Alterar_Tipo_Campo("T008_Codigo_CFOP","decimal(5,3)","default 0");
Alterar_Tipo_Campo("T008_Descricao_Produto","varchar(120)");
Alterar_Tipo_Campo("D084_Regiao","varchar(50)");


Criar_Campo("T008_Aliquota_Imposto_Importacao","decimal(5,2) default 0");
Criar_Campo("T008_Valor_Base_Imposto_Importacao_Unitario","decimal(19,6) default 0");
Criar_Campo("T008_Valor_Total_Base_Imposto_Importacao","decimal(19,2) default 0");
Criar_Campo("T008_Valor_Imposto_Importacao_Unitario","decimal(19,6) default 0");
Criar_Campo("T008_Valor_Total_Imposto_Importacao","decimal(19,2) default 0");
Criar_Campo("T008_Valor_Total_Despesas_Aduaneira","decimal(19,2) default 0");
Criar_Campo("T008_Valor_Total_IOF","decimal(19,2) default 0");
Criar_Campo("T008_Numero_Documento_Importacao","varchar(10)");
Criar_Campo("T008_Data_Registro_Documento_Importacao","date");
Criar_Campo("T008_Local_Desembaraco","varchar(60)");
Criar_Campo("T008_UF_Desembaraco","varchar(2)");
Criar_Campo("T008_Data_Desembaraco","date");
Criar_Campo("T008_Codigo_Exportador","varchar(60)");
Criar_Campo("T008_Numero_Adicao","int(3) default 0");
Criar_Campo("T008_Numero_Sequencia_Adicao","int(3) default 0");
Criar_Campo("T008_Codigo_Fabricante","varchar(60)");
Criar_Campo("T008_Valor_Desconto_Item_Adicao","decimal(15,2) default 0");

Criar_Campo("T007_Valor_Total_Imposto_Importacao","decimal(19,2) default 0");
Criar_Campo("T007_Valor_Total_PIS","decimal(15,2) default '0'");
Criar_Campo("T007_Valor_Total_COFINS","decimal(15,2) default '0'");
Criar_Campo("T007_Flag_Venda_Compra_Outros","varchar(1) after T007_Flag_Entrada_Saida");

Criar_Campo("T007_T128_Id","int(11) default '0' after T007_Id");
Criar_Campo("T008_T129_Id","int(11) default '0' after T008_Id");

Criar_Campo("D006_Flag_Somar_IPI_Base_ICMS","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Somar_Despesas_Base_IPI","enum('S','N') default 'N'");
Criar_Campo("D006_Flag_Credita_IPI","enum('S','N') default 'N'");
Criar_Campo("T008_Flag_Excluir","enum('S','N') default 'N'");

Criar_Campo("T006_Flag_Situacao_Tributaria","varchar(3)");
Criar_Campo("T006_Situacao_Tributaria_PIS","varchar(2) default '01'");
Criar_Campo("T006_Valor_Base_PIS","decimal(20,2) default '0'");
Criar_Campo("T006_Aliquota_PIS","decimal(5,2) default '0'");
Criar_Campo("T006_Valor_PIS","decimal(10,2) default '0'");
Criar_Campo("T006_Situacao_Tributaria_COFINS","varchar(2) default '01'");
Criar_Campo("T006_Valor_Base_COFINS","decimal(20,2) default '0'");
Criar_Campo("T006_Aliquota_COFINS","decimal(5,2) default '0'");
Criar_Campo("T006_Valor_COFINS","decimal(10,2) default '0'");
Criar_Campo("T006_Aliquota_ICMS_ST","decimal(5,2) default '0' after T006_Valor_Base_ICMS_Substituicao");
Criar_Campo("T006_Percentual_Reducao_ICMS_ST","decimal(5,2) default '0' after T006_Valor_Base_ICMS_Substituicao");
Criar_Campo("T006_ST_ICMS_Interno","decimal(5,2) default 0 after T006_Flag_ST");
Criar_Campo("T006_ICMS_Inter_Estadual","decimal(5,2) default 0 after T006_Flag_ST");
Criar_Campo("T006_Flag_Isento_PIS","enum('S','N') default 'N' after T006_Situacao_Tributaria_PIS");
Criar_Campo("T006_Flag_Isento_COFINS","enum('S','N') default 'N' after T006_Situacao_Tributaria_COFINS");
Criar_Campo("T006_Reordenar","int(1) default '1'");

Criar_Campo("T007_Flag_Contingencia","enum('S','N') default 'N' after T007_Chave_Acesso_Nfe");
Criar_Campo("T007_Flag_Inclusao_Manual","enum('S','N') default 'N'");
Criar_Campo("T007_Flag_Inutilizado","enum('S','N') default 'N' after T007_Flag_Contingencia");
Criar_Campo("T007_Flag_Denegado","enum('S','N') default 'N' after T007_Flag_Contingencia");
Alterar_Tipo_Campo("T007_Quantidade_Volumes","int(15) unsigned","default 0");
Alterar_Tipo_Campo("T005_Quantidade_Volumes","int(15) unsigned","default 0");
Alterar_Tipo_Campo("T007_Empresa_Usuaria_Numero","varchar(20)","");

Criar_Campo("T004_Flag_Situacao_Tributaria","varchar(3)");
Criar_Campo("T004_Situacao_Tributaria_PIS","varchar(2) default '01'");
Criar_Campo("T004_Valor_Base_PIS","decimal(20,2) default '0'");
Criar_Campo("T004_Aliquota_PIS","decimal(5,2) default '0'");
Criar_Campo("T004_Valor_PIS","decimal(10,2) default '0'");
Criar_Campo("T004_Situacao_Tributaria_COFINS","varchar(2) default '01'");
Criar_Campo("T004_Valor_Base_COFINS","decimal(20,2) default '0'");
Criar_Campo("T004_Aliquota_COFINS","decimal(5,2) default '0'");
Criar_Campo("T004_Valor_COFINS","decimal(10,2) default '0'");

Criar_Campo("T005_Valor_Total_PIS","decimal(10,2) default '0' after T005_Valor_ISS");
Criar_Campo("T005_Valor_Total_COFINS","decimal(10,2) default '0' after T005_Valor_Total_PIS");

Criar_Campo("T007_Origem_UF","char(2)");
Criar_Campo("T007_Origem_Ano_Mes","char(4)");
Criar_Campo("T007_Origem_Cnpj","varchar(14)");
Criar_Campo("T007_Origem_Modelo","char(2)");
Criar_Campo("T007_Origem_Serie","int(3)");
Criar_Campo("T007_Origem_Chave_Acesso","varchar(44)");

Criar_Campo("T005_Origem_UF","char(2)");
Criar_Campo("T005_Origem_Ano_Mes","char(4)");
Criar_Campo("T005_Origem_Cnpj","varchar(14)");
Criar_Campo("T005_Origem_Modelo","char(2)");
Criar_Campo("T005_Origem_Serie","int(3)");
Criar_Campo("T005_Origem_Chave_Acesso","varchar(44)");

Criar_Campo("T099_Peso_Unitario","decimal(10,3) default 0");
Criar_Campo("T099_Horario_Entrega","varchar(10) default ' '");
Criar_Campo("T099_D037_Id","decimal(10,3) default 0 after T099_D009_Id");
Criar_Campo("T006_T099_Id","int(11) default 0");
Criar_Campo("T099_Qte_UND","decimal(10,3) default 0");
Criar_Campo("T099_Qte_PCT","decimal(10,3) default 0");
Criar_Campo("T099_Qte_KLG","decimal(10,3) default 0");
Criar_Campo("T099_Flag_Cancelado","varchar(1) default ' '");
Criar_Campo("T099_C004_Id","int(10) unsigned","after T099_Id");

Criar_Campo("D070_Data","date default '0000-00-00'");
Criar_Campo("D070_Flag_Ativo","varchar(1) default 'S'");

Criar_Campo("T004_Flag_Cancelado","varchar(1) default ' '");

Criar_Campo("T002_T117_Id","int(10) unsigned after T002_Id");
Criar_Campo("T002_T135_Id","int(10) unsigned after T002_Id");

Criar_Campo("T005_Flag_Importar_Pendente","enum('S','N') default 'N'");

Criar_Campo("T005_Data_Saida","date default '0000-00-00' after T005_Data_Emissao");
Criar_Campo("T005_Hora_Saida","time default '00:00:00' after T005_Data_Saida");
Criar_Campo("T007_Data_Saida","date default '0000-00-00' after T007_Data_Emissao");
Criar_Campo("T007_Flag_SCAN","enum('S','N') default 'N'");
Criar_Campo("T007_Flag_Almoxarifado","enum('S','N') default 'N'");

Criar_Campo("D007_Flag_Tipo_Registro","int(1) default '0' after D007_Flag_Layout");
Alterar_Campo("D007_Flag_Com_Registro","D007_Flag_Tipo_Registro","int(1) default '0' after D007_Flag_Layout");

Criar_Campo("D007_Numero_Range","varchar(10)");
Criar_Campo("D007_Numero_Contrato","int(6) default '0'");
Criar_Campo("D007_Codigo_Cedente","int(10) default '0'");
Alterar_Tipo_Campo("D007_Tipo_Carteira_Banco_Brasil","varchar(10)");
Alterar_Tipo_Campo("D007_Numero_Contrato","int(10) unsigned","default 0");
Criar_Campo("D007_Codigo_Baixa","varchar(30)");
Criar_Campo("D007_Nosso_Numero","varchar(30)");
Alterar_Tipo_Campo("D007_Nosso_Numero","varchar(30)");
Criar_Campo("D007_Aceite","enum('S','N') default 'N'");
Criar_Campo("D007_Especie","varchar(30)");
Criar_Campo("D007_Especie_Documento","varchar(30)");
Criar_Campo("D007_Uso_Banco","varchar(30)");
Criar_Campo("D007_Percentual_Juros_Dia","Decimal(10,2) default '0'");
Criar_Campo("D007_Percentual_Multa","Decimal(10,2) default '0'");
Criar_Campo("D007_Instrucoes_Boleto","text");
Alterar_Tipo_Campo("D007_Digito_Verificador_Agencia","char(3)");
Alterar_Tipo_Campo("D007_Digito_Verificador_Conta","char(3)");

Criar_Campo("D021_Nome_Conta","varchar(50)");

Criar_Campo("T007_Valor_Seguro","decimal(10,2) default 0");
Criar_Campo("T008_Valor_Seguro_Unitario","decimal(19,10) default 0");
Criar_Campo("T008_Valor_Total_Seguro","decimal(19,10) default 0");

Alterar_Tipo_Campo("T008_Valor_Seguro_Unitario","decimal(19,10)");
Alterar_Tipo_Campo("T008_Valor_Total_Seguro","decimal(19,10)");

Alterar_Campo("T035_Nome_Arquivo","T035_Conteudo_Arquivo","text");
Alterar_Tipo_Campo("T035_Conteudo_Arquivo","longtext");

Criar_Campo("T117_T035_Id","int(10) unsigned after T117_Id");
Criar_Campo("T117_Flag_Layout","enum('S','N') default 'N'");
Criar_Campo("T117_Flag_Enviar_Remessa","enum('S','N') default 'N'");
Criar_Campo("T117_Flag_Com_Registro","enum('S','N') default 'N' after T117_Flag_Layout");
Criar_Campo("T117_Nosso_Numero","varchar(30)");
Criar_Campo("T117_Linha_Digitavel","varchar(100)");
Criar_Campo("T117_Codigo_Cedente","int(10) default '0'");
Criar_Campo("T117_Data_Envio_Email","datetime default '0000-00-00 00:00:00'");
Criar_Campo("C020_Ordenar_Codigo_Numerico","varchar(1) default 'N'");


Criar_Campo("C020_Cadastro_Produtos_Avancado","varchar(1) default 'N'");
Criar_Campo("C020_Cadastro_Clientes_Avancado","varchar(1) default 'N'");

Criar_Campo("T001_T006_Id","int(10) unsigned after T001_D009_Id");

Alterar_Tipo_Campo("T117_Nosso_Numero","varchar(30)");
Criar_Campo("T117_Aceite","enum('S','N') default 'N'");
Criar_Campo("T117_Especie","varchar(30)");
Criar_Campo("T117_Especie_Documento","varchar(30)");
Criar_Campo("T117_Uso_Banco","varchar(30)");
Criar_Campo("T117_Percentual_Juros_Dia","Decimal(10,2) default '0'");
Criar_Campo("T117_Percentual_Multa","Decimal(10,2) default '0' after T117_Percentual_Juros_Dia");
Criar_Campo("T117_Flag_Cancelado","enum('S','N') default 'N'");

Criar_Campo("T005_Flag_Imprimir_Romaneio","enum('S','N') default 'N'");
Criar_Campo("T005_Data_Impressao_Romaneio","datetime default '0000-00-00 00:00:00'");
Criar_Campo("D002_Flag_Visualizar_Site","enum('S','N') default 'S'");
Criar_Campo("D003_Flag_Visualizar_Site","enum('S','N') default 'S'");

Criar_Campo("D003_Codigo_Padrao","varchar(10) default ''");
Criar_Campo("D003_Descricao_Padrao","varchar(30) default ''");

Criar_Campo("D002_Codigo_Padrao","varchar(10) default ''");
Criar_Campo("D002_Descricao_Padrao","varchar(30) default ''");

Criar_Campo("D060_Codigo_Padrao","varchar(10) default ''");
Criar_Campo("D060_Descricao_Padrao","varchar(30) default ''");
Criar_Campo("D060_D003_Id","int(10) unsigned after D060_C008_Id");

Criar_Campo("D009_D059_Id","int(10) unsigned after D009_C004_Id");

Criar_Campo("D015_Codigo_Padrao","varchar(10) default ''");
Criar_Campo("D015_Descricao_Padrao","varchar(30) default ''");

Criar_Campo("T014_Codigo_Produto","varchar(30) default '' T014_Data_Emissao");
Criar_Campo("D069_Preco_Unitario_Liquido","decimal(10,2) default '0'");
Criar_Campo("D069_Descricao_Empresa","varchar(60) default ''");
Criar_Campo("D069_Peso_Unitario","decimal(10,3) default '0'");
Criar_Campo("D069_Unidade","varchar(20) default ''");
Criar_Campo("D069_Aliquota_IPI","decimal(10,2) default '0'");
Criar_Campo("D069_NCM","varchar(15) default ''");
Criar_Campo("D069_Codigo_Barras","varchar(20) default ''");
Criar_Campo("D069_Complemento","varchar(30) default ''");
Criar_Campo("D069_Codigo_Montado","varchar(30) default ''");
Criar_Campo("D069_D082_Id","int(11) unsigned not null default '0'");
Criar_Campo("D069_D009_Id","int(11) unsigned not null default '0'");
Criar_Campo("D069_D003_Id","int(11) unsigned not null default '0'");
Criar_Campo("D069_D002_Id","int(11) unsigned not null default '0'");
Criar_Campo("D069_D060_Id","int(11) unsigned not null default '0'");
Criar_Campo("D069_D015_Id","int(11) unsigned not null default '0'");
Criar_Campo("D105_Condicao","varchar(200) default ''");
Criar_Campo("D105_Apagar","varchar(200) default ''");
Criar_Campo("D105_Codigo_Empresa","varchar(30) default ''");

Alterar_Tipo_Campo("D105_Apagar","varchar(500)","");
Alterar_Tipo_Campo("D105_Condicao","varchar(500)","");

Criar_Campo("C017_Tipo_Impressao","enum('LPR','PDF') default 'LPR'");
Criar_Campo("C017_PDF_Largura_Papel","decimal(6,3) default '0'");
Criar_Campo("C017_PDF_Altura_Papel","decimal(6,3) default '0'");
Criar_Campo("C017_PDF_Margem_Esquerda","decimal(5,2) default '0'");
Criar_Campo("C017_PDF_Margem_Direita","decimal(5,2) default '0'");
Criar_Campo("C017_PDF_Margem_Superior","decimal(5,2) default '0'");
Criar_Campo("C017_PDF_Tamanho_Letra_Expandido","decimal(5,2) default '0'");
Criar_Campo("C017_PDF_Tamanho_Letra_Normal","decimal(5,2) default '0'");
Criar_Campo("C017_PDF_Tamanho_Letra_Comprimido","decimal(5,2) default '0'");
Criar_Campo("C017_PDF_Altura_Linha","decimal(5,2) default '0'");
Criar_Campo("C008_Flag_Vendas","varchar(1) default ' '");
Criar_Campo("C008_Flag_Custo","varchar(20) default 'HISTORICO'");
Criar_Campo("C008_Flag_Codigo_Proprio","varchar(1) default ' '");
Criar_Campo("C008_Apelido_Linha","varchar(15) default 'Linha'");
Criar_Campo("C008_Apelido_Grupo","varchar(15) default 'Grupo'");
Criar_Campo("C008_Apelido_SubGrupo","varchar(15) default 'Sub-Grupo'");
Criar_Campo("C008_Apelido_Aplicacao","varchar(15) default 'Aplicação'");
Criar_Campo("C008_Apelido_Marca","varchar(15) default 'Marca'");
Criar_Campo("C008_Apelido_Complemento_Descricao","varchar(25) default 'Complemento Descrição'");
Criar_Campo("C008_Codigo_Padrao","varchar(10) default ' '");
Criar_Campo("C008_Descricao_Padrao","varchar(30) default ' '");
Criar_Campo("C008_Inclusao_Produto_Mostrar_Codigo_Grupos","enum('S','N') default 'N'");
Criar_Campo("C008_Inclusao_Produto_Delimitador_Codigo","varchar(1) default ''");
Criar_Campo("C008_Apelido_Preco_Revenda","varchar(20) default 'Revenda'");
Criar_Campo("C008_Apelido_Preco_Cons_Atacado","varchar(20) default 'Atacado'");
Criar_Campo("C008_Apelido_Preco_Cons_Varejo","varchar(20) default 'Varejo'");

mysql_query("update C008 set C008_Apelido_Linha='Linha' where C008_Apelido_Linha=''");
mysql_query("update C008 set C008_Apelido_Grupo='Grupo' where C008_Apelido_Grupo=''");
mysql_query("update C008 set C008_Apelido_SubGrupo='Sub-Grupo' where C008_Apelido_SubGrupo=''");
mysql_query("update C008 set C008_Apelido_Aplicacao='Aplicacao' where C008_Apelido_Aplicacao=''");
mysql_query("update C008 set C008_Apelido_Marca='Marca' where C008_Apelido_Marca=''");

$D044=mysql_query("select * from D044");

if (mysql_num_rows($D044)<=0)
{
	mysql_query("insert into D044 values (1,'Reais','R$')");
	mysql_query("insert into D044 values (2,'Dolar','$')");
	mysql_query("insert into D044 values (3,'Euro','€')");
}

Criar_Campo("D049_D082_Id","int(11) unsigned after D049_Id");
Criar_Campo("D049_ICF","decimal(10,4)");
Criar_Campo("D049_IPV","decimal(10,4)");

//mysql_query("alter table D024 change D024_Data_Irregular D024_Data_Irregular date default '0000-00-00'");
Criar_Campo("T005_Transportadora_Placa_Veiculo","varchar(8) after T005_Nome_Transportadora");
Criar_Campo("T007_Transportadora_Placa_Veiculo","varchar(8) after T007_Transportadora_Estado");

Criar_Campo("T011_Data_Entrega","date");
Criar_Campo("T011_Forma_Pagamento","varchar(100)");

Criar_Campo("T005_Estado_Veiculo","char(2) after T005_Transportadora_Placa_Veiculo");
Criar_Campo("T007_Estado_Veiculo","char(2) after T007_Transportadora_Placa_Veiculo");

Criar_Campo("D069_Percentual_Desconto","decimal(7,4) default 0");

Criar_Campo("T060_Flag_Fatura_Finalizada","enum('S','N') default 'N'");
Criar_Campo("T060_T075_Id","int(10) unsigned after T060_Id");

Criar_Campo("T075_D018_Id","int(10) unsigned after T075_Id");
Criar_Campo("T075_Local_Desembaraco","varchar(60)");
Criar_Campo("T075_Data_Desembaraco","date");
Criar_Campo("T075_Codigo_Exportador","varchar(60)");

Criar_Campo("D017_Log_Juntar_Codigo","text");
Criar_Campo("D017_D049_Id","int(0) default 0 after D017_D001_Id");

Alterar_Tipo_Campo("D017_Descricao","varchar(100)");

Criar_Indice("D083_Codigo_Produto_Fornecedor,,D083_D024_Id");
Criar_Indice("T004_Id");
Criar_Indice("T019_T005_Id");
Criar_Indice("D024_Flag_Curva_Abc");
Criar_Indice("D024_Flag_Comercial");
Criar_Indice("D085_D024_Id");
Criar_Indice("D085_C007_Id");
Criar_Indice("D085_Data");
Criar_Indice("T008_T129_Id");
Criar_Indice("T006_Flag_Divergencia");
Criar_Indice("T001_T006_Id");
Criar_Indice("D083_D049_Id");
Criar_Indice("D082_Id");
Criar_Indice("D037_Id");
Criar_Indice("C008_Id");
Criar_Indice("T014_T129_Id");
Criar_Indice("D070_Id");
Criar_Indice("D070_D024_Id");
Criar_Indice("T001_Data_Lancamento");
Criar_Indice("T001_Flag_MMF");
Criar_Indice("T001_Flag_Operacao");
Criar_Indice("D069_Preco_Unitario_Liquido");
Criar_Indice("D049_Flag_Tipo");
Criar_Indice("T129_T006_Id");
Criar_Indice("T012_T006_Id");
Criar_Indice("T007_T128_Id");
Criar_Indice("T005_Flag_Status");
Criar_Indice("D006_Flag_Venda_Compra_Outros");
Criar_Indice("D049_D082_Id");
Criar_Indice("D069_D070_Id");
Criar_Indice("D069_D082_Id");
Criar_Indice("D015_Nome_Grupo");
Criar_Indice("D060_Id");
Criar_Indice("D060_Descricao_Grupo");
Criar_Indice("D082_Marca");
Criar_Indice("D001_D060_Id");
Criar_Indice("D001_Id");
//Criar_Indice("D083_Id");
Criar_Indice("C008_Tipo");
Criar_Indice("D060_C008_Id");
Criar_Indice("D015_Id");
Criar_Indice("D069_Codigo_Montado");
Criar_Indice("D069_D009_Id");
Criar_Indice("D105_Id");
Criar_Indice("T098_Id");
Criar_Indice("T098_Data");
Criar_Indice("T098_D049_Id");
Criar_Indice("T006_T006_Id_Dividir");
Criar_Indice("T006_Flag_Status");

Criar_Indice("D069_Flag_Travado");
//Criar_Indice("D069_D009_Id,,D069_D070_Id,,D070_Flag_Travado");
Criar_Indice("T001_Flag_Atualiza_Custo");
Criar_Indice("T127_Id");
Criar_Indice("T127_Numero_Nota_Fiscal");
Criar_Indice("T127_D024_Id");
Criar_Indice("T127_C004_Id");
Criar_Indice("T127_Id");
Criar_Indice("T127_Data_Transmicao");

Criar_Indice("T124_Id");
Criar_Indice("T124_D024_Id");
Criar_Indice("T124_Ano");


Criar_Indice("D024_D085_Id");
Criar_Indice("D024_Data_Inativo");
Criar_Indice("D024_Data_Ativo");
Criar_Indice("D024_Flag_Classe");
Criar_Indice("D024_Flag_Pre_Cadastro");

Criar_Indice("D020_D084_Id");

if (!empty($cSenha))
{
    Atualizar_Rotina("TRIGGER","D009_trigger_estoque_principal");
    Atualizar_Rotina("TRIGGER","D009_trigger_estoque_real");
    Atualizar_Rotina("TRIGGER","T019_trigger_atualizar_banco");
    Atualizar_Rotina("TRIGGER","T019_trigger_atualiza_saldo");    
    Atualizar_Rotina("TRIGGER","T001_trigger_atualiza_estoque");
    Atualizar_Rotina("TRIGGER","D085_atualiza_D024");
    //Atualizar_Rotina("TRIGGER","T006_trigger_apos_alteracao");
    //Atualizar_Rotina("TRIGGER","T006_trigger_apos_exclusao");
    Atualizar_Rotina("TRIGGER","T020_trigger_atualizar_extras");
    Atualizar_Rotina("TRIGGER","T033_trigger_atualizar_extras");
    Atualizar_Rotina("TRIGGER","T003_trigger_after_insert");
    Atualizar_Rotina("TRIGGER","T005_trigger_after_insert");
    Atualizar_Rotina("TRIGGER","T007_trigger_after_insert");
    Atualizar_Rotina("TRIGGER","T117_trigger_after_insert");
}

/*
    mysql_query("drop trigger T006_trigger_apos_alteracao");
    mysql_query("drop trigger T006_trigger_apos_exclusao");

*/
    Atualizar_Rotina("FUNCTION","D001_Quantidade_Estoque");
    Atualizar_Rotina("FUNCTION","D009_Preco_Unitario");
    Atualizar_Rotina("FUNCTION","D009_Custo_Unitario");
    Atualizar_Rotina("FUNCTION","D009_IPV_ICMS");
    Atualizar_Rotina("FUNCTION","D009_ICMS_Venda");
    Atualizar_Rotina("FUNCTION","D009_Classificacao_Fiscal");
    Atualizar_Rotina("FUNCTION","D009_D005_Id");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Separado");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Separar");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Comprar");
    Atualizar_Rotina("FUNCTION","D009_IPI");
    Atualizar_Rotina("FUNCTION","D005_IPI");
    Atualizar_Rotina("FUNCTION","D009_Local");
    Atualizar_Rotina("FUNCTION","D009_Local");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_OC");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_OS");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Pedido");
    Atualizar_Rotina("FUNCTION","D009_Marca_Padrao");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Pedido_Separar");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Montagem");
    Atualizar_Rotina("FUNCTION","D009_Quantidade_Pendencia");
    Atualizar_Rotina("FUNCTION","D009_Origem_Preco");
    Atualizar_Rotina("FUNCTION","D009_Minimo_Maximo");
    Atualizar_Rotina("FUNCTION","D009_Frequencia");    

    Atualizar_Rotina("FUNCTION","T003_IPV_Medio");
    Atualizar_Rotina("FUNCTION","T003_Prazos");

    Atualizar_Rotina("FUNCTION","T004_Ultimo_Preco");

    Atualizar_Rotina("FUNCTION","T005_Status_Pedido");
    Atualizar_Rotina("FUNCTION","T005_Prazos");
    Atualizar_Rotina("FUNCTION","T005_IPV_Medio");

    Atualizar_Rotina("FUNCTION","T007_Prazos");
    Atualizar_Rotina("FUNCTION","T007_IPV_Medio");
    Atualizar_Rotina("FUNCTION","T007_Primeira_Ultima_Compra");

    Atualizar_Rotina("FUNCTION","T008_ST_Cupom");
    
    Atualizar_Rotina("FUNCTION","D017_Pesquisa_Substituicao");
    Atualizar_Rotina("FUNCTION","D016_Dolar");

    Atualizar_Rotina("FUNCTION","D024_CFOP");
    Atualizar_Rotina("FUNCTION","D002_Grupo");

    Atualizar_Rotina("FUNCTION","T018_lancado");
    Atualizar_Rotina("FUNCTION","T018_lancado_banco");

    Atualizar_Rotina("FUNCTION","Quantidade");
    Atualizar_Rotina("FUNCTION","ICMS_Inter_Estadual");
    Atualizar_Rotina("FUNCTION","Alinha");

    Atualizar_Rotina("PROCEDURE","T002_Baixar_Titulo");
    Atualizar_Rotina("PROCEDURE","T015_Baixar_Titulo");

    Atualizar_Rotina("PROCEDURE","T002_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T015_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T003_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T004_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T014_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T013_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T014_Gravar_Totalizacao_Custo");
    Atualizar_Rotina("PROCEDURE","T005_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T005_Gravar_Totalizacao_3");
    Atualizar_Rotina("PROCEDURE","T006_Gravar_Totalizacao_3");
    Atualizar_Rotina("PROCEDURE","T007_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T008_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T054_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T055_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T056_Gravar_Totalizacao");
    Atualizar_Rotina("FUNCTION","T055_Converte_Custo");
    Atualizar_Rotina("FUNCTION","T054_Status_Importacao");
    Atualizar_Rotina("PROCEDURE","T059_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T010_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T011_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T128_Gravar_Totalizacao");
    Atualizar_Rotina("PROCEDURE","T012_Gravar_Totalizacao");
    Atualizar_Rotina("FUNCTION","T012_Quantidade_Pendente");
    Atualizar_Rotina("FUNCTION","T012_Quantidade_Falta_Lancar");
    Atualizar_Rotina("PROCEDURE","T129_Gravar_Totalizacao");
    Atualizar_Rotina("FUNCTION","T129_Quantidade_Pendente");
    Atualizar_Rotina("FUNCTION","D009_Flag_Preco_Tabelado");
    Atualizar_Rotina("FUNCTION","T129_Quantidade_Falta_Lancar");
    

   
$mControleAcessos[]="99,,1,,LIBERAR IPV MINIMO";
$mControleAcessos[]="99,,2,,LIBERAR CREDITO";
$mControleAcessos[]="99,,3,,RETORNAR PEDIDO DO ALMOXARIFADO";
$mControleAcessos[]="99,,4,,CANCELAR PEDIDO VENDA";
$mControleAcessos[]="99,,5,,CANCELAR PEDIDO PENDENTE";
$mControleAcessos[]="99,,6,,ALTERAR OBSERVACAO COMPRA EM PRODUTOS";
$mControleAcessos[]="99,,7,,CANCELAR NOTA FISCAL EMITIDA";
$mControleAcessos[]="99,,8,,INCLUSAO E ALTERAÇÃO DE ITENS (D001)";
$mControleAcessos[]="99,,9,,ATUALIZAR D.E.";
$mControleAcessos[]="99,,10,,ALTERAÇÃO DO CAMPO COMPLEMENTO (D001)";
$mControleAcessos[]="99,,11,,ALTERAÇÃO DO CAMPO PESO (D001)";
$mControleAcessos[]="99,,12,,MENS.MOTIVO PEDIDO NEGADO";
$mControleAcessos[]="99,,13,,LIBERAR NOTA DE EXPOSIÇÃO";
$mControleAcessos[]="99,,14,,CANCELAR NOTA FISCAL RECEBIDA";
$mControleAcessos[]="99,,15,,LIBERAR TRANSFERENCIA ABAIXO DO IPV";
$mControleAcessos[]="99,,16,,ATIVAR / INATIVAR CLIENTE/FORNECEDOR";
$mControleAcessos[]="99,,17,,ATIVAR / INATIVAR TRANSPORTADORA";
$mControleAcessos[]="99,,18,,MENSAGEM PIN";
$mControleAcessos[]="99,,19,,ALTERAÇÃO DO CAMPO FOTO (D001)";
$mControleAcessos[]="99,,20,,CONTROLE DE REPRESENTANTE";
$mControleAcessos[]="99,,21,,ALTERAR CFOP NOTAS";
$mControleAcessos[]="99,,22,,EXCLUIR CLIENTE";
$mControleAcessos[]="99,,32,,PROCESSAR MONTAGEM";
$mControleAcessos[]="99,,33,,MENSAGEM PEDIDO PARA IMPRIMIR";
$mControleAcessos[]="99,,34,,ALTERAR PRECOS NO ORCAMENTO/PEDIDOS";
$mControleAcessos[]="99,,35,,IMPRIMIR COPIA NF OFICIAL";
$mControleAcessos[]="99,,36,,MUDAR PRE-CADASTRO CLIENTE";
$mControleAcessos[]="99,,37,,PERMITE IMPRIMIR A COLUNA CUSTOS NO INVENTARIO";
$mControleAcessos[]="99,,38,,ALTERAR PEDIDO PENDENTE";
$mControleAcessos[]="99,,90,,EXCLUIR NOTA REJEITADA PELO SEFAZ";
$mControleAcessos[]="99,,104,,ATIVAR / DESATIVAR SCAN";
$mControleAcessos[]="2,,39,,CLIENTES/FORNECEDORES";
$mControleAcessos[]="2,,40,,REPRESENTANTES";
$mControleAcessos[]="2,,41,,TRANSPORTADORA";
$mControleAcessos[]="2,,42,,PRODUTOS";
$mControleAcessos[]="2,,101,,TABELA DE PRECOS";
$mControleAcessos[]="2,,102,,TABELA FORNECEDOR";
$mControleAcessos[]="2,,110,,TABELA CUSTOS";
$mControleAcessos[]="2,,43,,C.F.O.P";
$mControleAcessos[]="2,,44,,CLASS.FISCAL";
$mControleAcessos[]="2,,114,,SIMPLES NACIONAL";
$mControleAcessos[]="2,,45,,BANCOS/CAIXAS";
$mControleAcessos[]="2,,46,,PORTADORES";
$mControleAcessos[]="2,,47,,CENTRO CUSTO";
$mControleAcessos[]="2,,48,,EMPRESA";
$mControleAcessos[]="2,,49,,PERFIL";
$mControleAcessos[]="2,,50,,VENDEDORES";
$mControleAcessos[]="2,,51,,USUARIOS";
$mControleAcessos[]="3,,52,,LISTA DE PRECOS";
$mControleAcessos[]="3,,89,,CARTEIRA CLIENTES";
$mControleAcessos[]="3,,53,,PRE PEDIDO";
$mControleAcessos[]="3,,98,,C.R.M.";
$mControleAcessos[]="3,,54,,ORCAMENTOS";
$mControleAcessos[]="3,,55,,PEDIDOS";
$mControleAcessos[]="3,,97,,APROVACAO DE ORCAMENTOS";
$mControleAcessos[]="3,,56,,NOTAS FISCAIS";
$mControleAcessos[]="3,,57,,TRANSMITIR NOTAS FISCAIS";
$mControleAcessos[]="3,,58,,INUTILIZAR NF-e";
$mControleAcessos[]="3,,59,,COMODATO";
$mControleAcessos[]="3,,60,,ORDEM DE SERVIÇO";
$mControleAcessos[]="3,,95,,CURVA ABC VENDEDORES";
$mControleAcessos[]="4,,61,,CONTAS A RECEBER";
$mControleAcessos[]="4,,62,,CONTAS A PAGAR";
$mControleAcessos[]="4,,63,,CAIXA E BANCOS";
$mControleAcessos[]="4,,64,,BOLETO BANCARIO";
$mControleAcessos[]="4,,65,,FATURAR ROMANEIO";
$mControleAcessos[]="4,,66,,PROJECAO ORCAMENTARIA";
$mControleAcessos[]="4,,96,,ANALISE DE CRÉDITO";
$mControleAcessos[]="4,,109,,COMISSÃO VENDAS";
$mControleAcessos[]="4,,72,,FLUXO DE CAIXA";
$mControleAcessos[]="4,,73,,BALANCETE";
$mControleAcessos[]="5,,67,,CONTROLE DE ESTOQUE";
$mControleAcessos[]="5,,103,,CURVA ABC PRODUTOS";
$mControleAcessos[]="5,,68,,NOTAS FISCAIS RECEBIDAS";
$mControleAcessos[]="5,,105,,CONHECIMENTOS";
$mControleAcessos[]="5,,69,,EXPEDICAO MERCADORIA";
$mControleAcessos[]="5,,70,,DIVERGENCIA ESTOQUE";
$mControleAcessos[]="5,,71,,MONTAGEM";
$mControleAcessos[]="5,,77,,INVENTARIO ESTOQUE";
$mControleAcessos[]="6,,74,,RELATORIO CONTAS A PAGAR";
$mControleAcessos[]="6,,75,,RELATORIO CONTAS A RECEBER";
$mControleAcessos[]="6,,76,,MAPA DE VENDA";
$mControleAcessos[]="6,,111,,MOVIMENTAÇÃO ESTOQUE";
$mControleAcessos[]="6,,112,,FATURAMENTO PRODUTOS";
$mControleAcessos[]="6,,78,,COMISSAO REPRESENTANTES";
$mControleAcessos[]="6,,79,,FECHAMENTOS";
$mControleAcessos[]="7,,80,,GERAR SINTEGRA";
$mControleAcessos[]="7,,81,,XML NFe";
$mControleAcessos[]="8,,82,,UF(ICMS)";
$mControleAcessos[]="8,,83,,CIDADES";
$mControleAcessos[]="8,,99,,IPV";
$mControleAcessos[]="8,,84,,LAYOUT NOTA FISCAL";
$mControleAcessos[]="8,,100,,MOTIVOS CANCELAMENTO VENDAS";
$mControleAcessos[]="8,,85,,MYSQL";
$mControleAcessos[]="8,,86,,PARAMETRIZAÇOES";
$mControleAcessos[]="9,,87,,MAILING";
$mControleAcessos[]="9,,88,,RECLAME JÁ";
$mControleAcessos[]="9,,113,,NEWS";
$mControleAcessos[]="5,,91,,COTAÇÃO";
$mControleAcessos[]="5,,92,,COMPRAS EMERGÊNCIA";
$mControleAcessos[]="5,,93,,COMPRAS ESTOQUE";
$mControleAcessos[]="5,,94,,ORDEM COMPRA";
$mControleAcessos[]="5,,106,,PRODUÇÃO EMERGÊNCIA";
$mControleAcessos[]="5,,107,,PRODUÇÃO ESTOQUE";
$mControleAcessos[]="5,,108,,ORDEM SERVIÇO";

$mControleAcessos[]="8,,900,,BANCO DADOS CLIENTES";
$mControleAcessos[]="8,,901,,LOG ACESSOS";
$mControleAcessos[]="8,,902,,USUARIOS CLIENTE";
$mControleAcessos[]="8,,903,,CONTADORES CLIENTES";
$mControleAcessos[]="8,,904,,MYSQL";
$mControleAcessos[]="8,,905,,ALTERAR SENHA";
$mControleAcessos[]="8,,906,,ICF";
$mControleAcessos[]="8,,907,,CONTRATOS CLIENTES";

for ($x=0;$x<count($mControleAcessos);$x++)
{
    list($C029_Grupo,$C029_Id,$C029_Descricao_Acesso)=explode(",,",$mControleAcessos[$x]);
    $cClausula="select * from C029 where C029_Id='$C029_Id'";
    $cSql_C029=mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
       exit;
    }
    if (mysql_num_rows($cSql_C029)==0)
    {
       $cClausula  = " insert into C029 ( ";
       $cClausula .= " C029_Id,";
       $cClausula .= " C029_Grupo, ";
       $cClausula .= " C029_Descricao_Acesso ";
       $cClausula .= " ) values ( ";
       $cClausula .= " '$C029_Id',";
       $cClausula .= " '$C029_Grupo',";
       $cClausula .= " '$C029_Descricao_Acesso' ";
       $cClausula .= " ) ";
       if ($cConfirmado=='S')
       {
          mysql_query($cClausula);
          if (!empty($cErro))
          {
              msg_alerta("ERRO: $cClausula");
              echo "$cClausula<hr>$cErro";
              exit;
          }
       }
       $mAlteracoes[]="$cClausula";
    }
    else
    {
       $cClausula  = " update C029 set ";
       $cClausula .= " C029_Descricao_Acesso='$C029_Descricao_Acesso',C029_Grupo='$C029_Grupo' ";
       $cClausula .= " where C029_Id='$C029_Id' ";
       if ($cConfirmado=='S')
       {
          mysql_query($cClausula);
          if (!empty($cErro))
          {
              msg_alerta("ERRO: $cClausula");
              echo "$cClausula<hr>$cErro";
              exit;
          }
       }
       //$mAlteracoes[]="$cClausula";
    }
}

mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='2' where C029_Id='39'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='2' where C029_Id='41'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='2' where C029_Id='42'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='43'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='44'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='45'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='46'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='47'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='48'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='49'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='51'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='114'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='10' where C029_Id='56'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='10' where C029_Id='57'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='4' where C029_Id='61'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='4' where C029_Id='62'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='4' where C029_Id='63'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='5' where C029_Id='68'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='5' where C029_Id='70'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='5' where C029_Id='105'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='80'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='81'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='82'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='83'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='8' where C029_Id='905'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='3' where C029_Id='54'");
mysql_query("update C029 set C029_Flag_NFe='S',C029_Grupo_Nfe='99' where C029_Id='7'");

$C030=mysql_query("select C030_Id from C030 where C030_Id_Perfil='1' and C030_C029_Id='105'");
if (mysql_num_rows($C030)<=0)
{
    mysql_query("insert into C030 (C030_C029_Id,C030_C021_Id,C030_Id_Perfil,C030_Acesso_Liberado)
                           values ('105','1','1','S')");
}
$C030=mysql_query("select C030_Id from C030 where C030_Id_Perfil='1' and C030_C029_Id='114'");
if (mysql_num_rows($C030)<=0)
{
    mysql_query("insert into C030 (C030_C029_Id,C030_C021_Id,C030_Id_Perfil,C030_Acesso_Liberado)
                           values ('114','1','1','S')");
}
$C030=mysql_query("select C030_Id from C030 where C030_Id_Perfil='1' and C030_C029_Id='905'");
if (mysql_num_rows($C030)<=0)
{
    mysql_query("insert into C030 (C030_C029_Id,C030_C021_Id,C030_Id_Perfil,C030_Acesso_Liberado)
                           values ('905','1','1','S')");
}

$cClausula = "show columns from T011 like 'T011_Valor_Total_Pendente'";
$cSql = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   msg_alerta("ERRO: $cClausula");
   echo "$cClausula<hr>$cErro";
}
if (mysql_num_rows($cSql)==0)
{
   $cClausula = "alter table T011 add T011_Valor_Total_Pendente decimal(10,2) default 0";
   $cSql = mysql_query($cClausula);
   $cErro = mysql_error();
   if (!empty($cErro))
   {
      msg_alerta("ERRO: $cClausula");
      echo "$cClausula<hr>$cErro";
      exit;
   }
   $cClausula = "select T011_Id from T011";
   $cSql_T011 = mysql_query($cClausula);
   $cErro = mysql_error();
   if (!empty($cErro))
   {
      msg_alerta("ERRO: $cClausula");
      echo "$cClausula<hr>$cErro";
      exit;
   }

   while ($mT011=mysql_fetch_array($cSql_T011))
   {
       $cClausula = "call T011_Gravar_Totalizacao($mT011[T011_Id])";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          msg_alerta("ERRO: $cClausula");
          echo "$cClausula<hr>$cErro";
          exit;
       }
   }

}

if ($cConfirmado=='S')
{    
    $Data_Alteracao_Arquivo_Erros_NFe=date("Y-m-d H:i:s", filemtime('codigo_erros_nfe.php'));
    if ($mConfig[C020_Data_Ultima_Atualizacao_Erros_NFe]!=$Data_Alteracao_Arquivo_Erros_NFe)
    {	
		include "codigo_erros_nfe.php";
		mysql_query("update C020 set C020_Data_Ultima_Atualizacao_Erros_NFe='$Data_Alteracao_Arquivo_Erros_NFe'");	
    }
	
    
}
if ($cConfirmado!='S')
{
   if (empty($mAlteracoes))
   {
       $mAlteracoes[]='';
   }   
   echo "Alterações a serem efetuadas:<BR><BR>".implode("<HR>",$mAlteracoes);
   echo "<hr>";
   echo "<font class='fonte'>Senha: <input type='password' value='' size=20 onblur=\"location.replace('alteracoes.php?Confirmado=S&Senha='+this.value)\">";
//   echo "<input type='button' value='Confirmar?' onclick=\"location.replace('alteracoes.php?Confirmado=S')\">";
}
else
{
   $Data_Alteracao_Arquivo=date("Y-m-d H:i:s", filemtime('alteracoes.php'));
   mysql_query("update C020 set C020_Data_Ultima_Atualizacao='$Data_Alteracao_Arquivo'");
   echo implode("<HR>",$mAlteracoes);
   echo "<script>parent.document.getElementById('Div_Executar_Atualizacoes').style.display='none';
                 parent.document.getElementById('Div_Executar_Atualizacoes_Sombra').style.display='none';
   </script>";
   msg_alerta("As atualizacões foram efetuadas com sucesso!");
}

if ($cConfirmado=='S')
{
	mysql_query("SET FOREIGN_KEY_CHECKS=1;");
	mysql_query("SET SQL_MODE=''");
}

function Atualizar_Rotina($Tipo,$cNome_Funcao)
{
   global $mAlteracoes;
   global $cConfirmado;

   $cArquivo=$cNome_Funcao;
   if (empty($cArquivo))
   {
      "Nome do arquivo não foi definido para funcao Atualizar_Rotina";
      exit;
   }
   $cArquivo="../regras_mysql/$cArquivo.sql";
   if (!file_exists($cArquivo))
   {
      echo "Arquivo não existe: $cArquivo";
      exit;
   }

   $mAlteracoes[]="Criar $Tipo $cNome_Funcao";

   if ($cConfirmado=='S')
   {
       $cClausula="drop $Tipo IF EXISTS $cNome_Funcao";
       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          msg_alerta("ERRO: $cClausula");
          echo "$cClausula<BR>$cErro<hr>";
//          exit;
       }
       $cClausula = file_get_contents($cArquivo);

       $cSql = mysql_query($cClausula);
       $cErro = mysql_error();
       if (!empty($cErro))
       {
          msg_alerta("ERRO: $cClausula");
          echo "$cClausula<BR>$cErro<hr>";
       }
   }
}

function Criar_Indice($cCampo)
{
    global $mAlteracoes;
    global $cConfirmado;

    $mCampo=explode(",,",$cCampo);
    if (count($mCampo)==0)
    {
        $mAlteracoes[]='<b><font color=red>Não foi passado o paramento: $cCampo</b>';
        return false;
    }

    $cTabela = substr($mCampo[0],0,4);    
    $cIndice = "IDX_".implode("_",$mCampo);

    //-- CRIA O INDICE
    $cClausula = "SHOW INDEX FROM $cTabela where Key_name = '$cIndice'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
        // VERIFICA SE O CAMPO EXISTE PARA PODER CRIAR O INDICE
        for($x=0;$x<count($mCampo);$x++)
        {
        	$cClausula = "show columns from $cTabela like '".$mCampo[$x]."'";
        	$cSql = mysql_query($cClausula);
	        $cErro = mysql_error();
	        if (!empty($cErro))
	        {
	           msg_alerta("ERRO: $cClausula");
	           echo "$cClausula<hr>$cErro";
	        }
	        if (mysql_num_rows($cSql)==0)
	        {
	            $mAlteracoes[]="<b><font color=red>Campo: ".$mCampo[$x]." não existe na tabela: $cTabela</b>";
	            return false;
	        }
        }
        $mAlteracoes[]="Adicionar indice: <b>$cIndice</b> na tabela <b>$cTabela</b>";
        if ($cConfirmado=='S')
        {
            $cClausula = "alter table $cTabela add INDEX $cIndice (".implode(",",$mCampo).")";
            $cSql = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro))
            {
               msg_alerta("ERRO: $cClausula");
               echo "$cClausula<hr>$cErro";
//               exit;
            }
        }
    }
}

function Criar_Campo($cCampo,$cTipo)
{
    global $mAlteracoes;
    global $cConfirmado;

    $cTabela = substr($cCampo,0,4);
    $cClausula = "show columns from $cTabela like '$cCampo'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       $mAlteracoes[]="Adicionar campo: <b>$cCampo</b> <b>$cTipo</b>";
       if ($cConfirmado=='S')
       {
           $cClausula = "alter table $cTabela add $cCampo $cTipo";
           $cSql = mysql_query($cClausula);
           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_alerta("ERRO: $cClausula");
              echo "$cClausula<hr>$cErro";
//              exit;
           }
       }
    }
}

function Alterar_Tipo_Campo($cCampo,$cTipo,$cDefault="")
{
    global $mAlteracoes;
    global $cConfirmado;

    $cTabela = substr($cCampo,0,4);
    $cClausula = "show columns from $cTabela  like '$cCampo'";
    $cSql = mysql_query($cClausula);
    $mSql = mysql_fetch_array($cSql);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    $cTipo=strtoupper($cTipo);
    $mSql[Type]=strtoupper($mSql[Type]);
    if ($mSql[Type]!=$cTipo)
    {
       $mAlteracoes[]="ALTERANDO TIPO DO CAMPO: <b>$cCampo</b> <BR>DE: <b>$mSql[Type]</b> <BR>PARA: <b>$cTipo</b>"."<BR>Clausula: <b>$cClausula</b>";
       if ($cConfirmado=='S')
       {
           $cClausula="alter table $cTabela  change $cCampo $cCampo $cTipo $cDefault";
           $cSql=mysql_query($cClausula);

           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_alerta("ERRO: $cClausula");
              $mAlteracoes[]="Erro: <b>$cErro</b>";
           }
       }
    }
}

function Alterar_Campo($cCampo_Antigo,$cCampo_Novo,$cTipo='')
{
    global $mAlteracoes;
    global $cConfirmado;
    
    $cTabela = substr($cCampo_Antigo,0,4);
    $cClausula = "show columns from $cTabela like '$cCampo_Novo'";
    $cSql = mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       msg_alerta("ERRO: $cClausula");
       echo "$cClausula<hr>$cErro";
    }
    if (mysql_num_rows($cSql)==0)
    {
       $mAlteracoes[]="Alterar campo <b>$cCampo_Antigo</b> para <b>$cCampo_Novo</b> na tabela $cTabela";
       if ($cConfirmado=='S')
       {
           $cClausula = "alter table $cTabela change $cCampo_Antigo $cCampo_Novo $cTipo";
           $cSql = mysql_query($cClausula);
           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_alerta("ERRO: $cClausula");
              echo "$cClausula<hr>$cErro";
              exit;
           }
       }
    }
}

?>