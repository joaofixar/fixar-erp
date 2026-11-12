-- MySQL dump 10.11
--
-- Host: mysql.hardness.com.br    Database: e039
-- -----------------------------------------------------
-- Server version	5.1.39-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `B001`
--

-- DROP TABLE IF EXISTS `B001`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `B001` (
  `B001_Id` int(11) NOT NULL,
  `B001_CODGRP` varchar(5) NOT NULL,
  `B001_CODBAR` varchar(13) DEFAULT NULL,
  `B001_NOMPRO` varchar(50) NOT NULL,
  `B001_EMBPRO` varchar(10) DEFAULT NULL,
  `B001_QTDPOR` varchar(50) DEFAULT NULL,
  `B001_UNDPRO` varchar(10) NOT NULL DEFAULT 'UND',
  `B001_GRMPRO` varchar(3) DEFAULT NULL,
  `B001_CUSMAT` decimal(11,2) NOT NULL DEFAULT '0.00',
  `B001_CUSSER` decimal(11,2) NOT NULL DEFAULT '0.00',
  `B001_ALQICM` decimal(5,2) NOT NULL DEFAULT '0.00',
  `B001_ALQIPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `B001_MARLUC` decimal(5,2) NOT NULL DEFAULT '0.00',
  `B001_PRECUS` decimal(11,2) NOT NULL DEFAULT '0.00',
  `B001_PREUNT` decimal(11,2) NOT NULL DEFAULT '0.00',
  `B001_PESLIQ` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `B001_OBSCON` varchar(250) DEFAULT NULL,
  `B001_OBSING` varchar(250) DEFAULT NULL,
  `B001_OBSPRO` varchar(250) DEFAULT NULL,
  `B001_VLRCAL` varchar(10) DEFAULT '0',
  `B001_VDR001` varchar(5) DEFAULT '0',
  `B001_VLRCRB` varchar(10) DEFAULT '0',
  `B001_VDR002` varchar(5) DEFAULT '0',
  `B001_VLRPRO` varchar(10) DEFAULT '0',
  `B001_VDR003` varchar(5) DEFAULT '0',
  `B001_GORTOT` varchar(10) DEFAULT '0',
  `B001_VDR004` varchar(5) DEFAULT '0',
  `B001_GORSAT` varchar(10) DEFAULT '0',
  `B001_VDR005` varchar(5) DEFAULT '0',
  `B001_VLRCOL` varchar(10) DEFAULT '0',
  `B001_VDR006` varchar(5) DEFAULT '0',
  `B001_FIBALI` varchar(10) DEFAULT '0',
  `B001_VDR007` varchar(5) DEFAULT '0',
  `B001_CALCIO` varchar(10) DEFAULT '0',
  `B001_VDR008` varchar(5) DEFAULT '0',
  `B001_VLRFER` varchar(10) DEFAULT '0',
  `B001_VDR009` varchar(5) DEFAULT '0',
  `B001_VLRSOD` varchar(10) DEFAULT '0',
  `B001_VDR010` varchar(5) DEFAULT '0',
  `B001_CUSACE` decimal(11,2) NOT NULL,
  `B001_CODSER` char(5) NOT NULL,
  `B001_TIPPRO` char(1) NOT NULL DEFAULT 'C',
  `B001_PRZVAL` int(11) NOT NULL DEFAULT '1',
  `B001_CODAME` varchar(50) DEFAULT NULL,
  `B001_QTDPCT` int(11) NOT NULL DEFAULT '0',
  `B001_PESTOT` decimal(11,3) NOT NULL DEFAULT '0.000',
  `B001_NOMTEC` varchar(50) NOT NULL,
  `B001_EAN013` char(13) DEFAULT NULL,
  `B001_PATFOT` varchar(100) DEFAULT NULL,
  `B001_FOTPRO` text,
  `B001_CLAIPI` char(8) DEFAULT NULL,
  `B001_PRESUG` decimal(11,2) DEFAULT '0.00',
  `B001_PREFIX` decimal(11,2) DEFAULT '0.00',
  `B001_ITMESC` smallint(6) NOT NULL DEFAULT '0',
  `B001_IMPETQ` char(1) NOT NULL DEFAULT 'N',
  `B001_PRETMP` decimal(11,2) NOT NULL DEFAULT '0.00',
  `B001_PREANT` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`B001_Id`),
  KEY `B001_CODGRP` (`B001_CODGRP`),
  KEY `B001_CODSER` (`B001_CODSER`),
  KEY `IND_B00101` (`B001_Id`),
  KEY `IND_B00102` (`B001_CODBAR`),
  KEY `IND_B00103` (`B001_NOMPRO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `B001`
--

LOCK TABLES `B001` WRITE;
/*!40000 ALTER TABLE `B001` DISABLE KEYS */;
/*!40000 ALTER TABLE `B001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C001`
--

-- DROP TABLE IF EXISTS `C001`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C001` (
  `C001_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C001_C002_Id` int(10) unsigned NOT NULL,
  `C001_Chave_Primaria` int(1) DEFAULT '2',
  `C001_Nome_Campo_Destino` varchar(100) DEFAULT NULL,
  `C001_Nome_Campo_Origem` varchar(100) DEFAULT NULL,
  `C001_Tipo_Campo_Origem` char(1) DEFAULT NULL,
  `C001_Tamanho_Campo_Origem` int(10) unsigned DEFAULT NULL,
  `C001_Decimal_Campo_Origem` int(10) unsigned DEFAULT NULL,
  `C001_Campo_Destino_Pesquisa` varchar(100) DEFAULT NULL,
  `C001_Ativo` int(1) DEFAULT '1',
  `C001_Descricao_Campo` text,
  `C001_Dominio_Campo` text,
  `C001_Posicao_Inicial_Txt` int(11) DEFAULT '0',
  `C001_Posicao_Final_Txt` int(11) DEFAULT '0',
  `C001_Ordem` int(3) DEFAULT '0',
  `C001_Campo_Origem_Pesquisa` varchar(100) DEFAULT NULL,
  `C001_Macro` varchar(200) DEFAULT NULL,
  `C001_Flag_Tipo_Data` int(1) DEFAULT '1',
  PRIMARY KEY (`C001_Id`),
  KEY `D100_FKIndex1` (`C001_C002_Id`),
  CONSTRAINT `C001_ibfk_1` FOREIGN KEY (`C001_C002_Id`) REFERENCES `C002` (`C002_Id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C001`
--

LOCK TABLES `C001` WRITE;
/*!40000 ALTER TABLE `C001` DISABLE KEYS */;
/*!40000 ALTER TABLE `C001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C002`
--

-- DROP TABLE IF EXISTS `C002`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C002` (
  `C002_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C002_C003_Id` int(10) unsigned NOT NULL,
  `C002_Tabela_Origem` varchar(100) DEFAULT NULL,
  `C002_Tabela_Destino` varchar(100) DEFAULT NULL,
  `C002_Ordem_Importacao` int(4) DEFAULT NULL,
  `C002_Ativo` int(1) DEFAULT '1',
  `C002_Descricao_Tabela_Origem` text,
  `C002_Importar` int(1) DEFAULT '2',
  `C002_Macro_Tabela_Origem` varchar(200) DEFAULT NULL,
  `C002_Compartilhado` int(1) DEFAULT NULL,
  `C002_Caminho_Arquivo_Origem` varchar(100) DEFAULT NULL,
  `C002_PI_Data_Movimento` int(4) DEFAULT NULL,
  `C002_PF_Data_Movimento` int(4) DEFAULT NULL,
  `C002_LI_Data_Movimento` int(4) DEFAULT NULL,
  `C002_PI_Quantidade_Linhas` int(4) DEFAULT NULL,
  `C002_PF_Quantidade_Linhas` int(4) DEFAULT NULL,
  `C002_LI_Quantidade_Linhas` int(4) DEFAULT NULL,
  `C002_Condicao_Exec_Sql` varchar(200) DEFAULT NULL,
  `C002_Clausula_Sql` varchar(200) DEFAULT NULL,
  `C002_Verifica_Chave_Primaria` int(1) DEFAULT '1',
  `C002_Pular_Registros_Existentes` int(1) DEFAULT '2',
  PRIMARY KEY (`C002_Id`),
  KEY `D101_FKIndex1` (`C002_C003_Id`),
  CONSTRAINT `C002_ibfk_1` FOREIGN KEY (`C002_C003_Id`) REFERENCES `C003` (`C003_Id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C002`
--

LOCK TABLES `C002` WRITE;
/*!40000 ALTER TABLE `C002` DISABLE KEYS */;
/*!40000 ALTER TABLE `C002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C003`
--

-- DROP TABLE IF EXISTS `C003`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C003` (
  `C003_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C003_Tipo_Campo_Origem` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`C003_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C003`
--

LOCK TABLES `C003` WRITE;
/*!40000 ALTER TABLE `C003` DISABLE KEYS */;
/*!40000 ALTER TABLE `C003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C004`
--

-- DROP TABLE IF EXISTS `C004`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C004` (
  `C004_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C004_D020_Id` int(10) unsigned DEFAULT NULL,
  `C004_D018_Id` int(11) DEFAULT '0',
  `C004_D024_Id` int(11) DEFAULT '0',
  `C004_Nome_Empresa` varchar(100) DEFAULT NULL,
  `C004_Cidade` varchar(30) DEFAULT NULL,
  `C004_Uf` char(2) DEFAULT NULL,
  `C004_Cnpj` varchar(18) DEFAULT NULL,
  `C004_Caminho_Sistema` varchar(100) DEFAULT NULL,
  `C004_Matriz` int(1) DEFAULT NULL,
  `C004_Banco_Dados` varchar(30) DEFAULT NULL,
  `C004_Caminho_Dbf` varchar(100) DEFAULT NULL,
  `C004_Importar` int(1) DEFAULT '1',
  `C004_Banco_Configuracao` varchar(15) DEFAULT NULL,
  `C004_Nome_Abreviado` varchar(60) DEFAULT NULL,
  `C004_Inscricao_Estadual` varchar(14) DEFAULT NULL,
  `C004_Inscricao_Municipal` varchar(20) DEFAULT NULL,
  `C004_Responsavel_Contato` varchar(28) DEFAULT NULL,
  `C004_Telefone` varchar(12) DEFAULT NULL,
  `C004_Fax` varchar(12) DEFAULT NULL,
  `C004_Logradouro` varchar(34) DEFAULT NULL,
  `C004_Numero` varchar(5) DEFAULT NULL,
  `C004_Complemento` varchar(22) DEFAULT NULL,
  `C004_Bairro` varchar(15) DEFAULT NULL,
  `C004_Cep` varchar(10) DEFAULT NULL,
  `C004_Estado` varchar(20) DEFAULT NULL,
  `C004_Endereco_Impressora_Nf` varchar(20) DEFAULT NULL,
  `C004_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `C004_Aliquota_ICMS` decimal(5,2) DEFAULT NULL,
  `C004_Ultimo_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `C004_Ultimo_Numero_Nota_Fiscal_Eletronica` varchar(12) DEFAULT NULL,
  `C004_Ultimo_Numero_ACP` varchar(12) DEFAULT NULL,
  `C004_Ultimo_Numero_Controle` varchar(12) DEFAULT NULL,
  `C004_Flag_Isento_ICMS` int(1) DEFAULT '2',
  `C004_Flag_Isento_ISS` int(1) DEFAULT '2',
  `C004_Flag_Isento_IPI` int(1) DEFAULT '2',
  `C004_Flag_Empresa_Simples` int(1) DEFAULT '2',
  `C004_Aliquota_ISS` decimal(5,2) DEFAULT '0.00',
  `C004_Flag_IPI_Base_ICMS_Revenda` varchar(1) DEFAULT 'N',
  `C004_Flag_IPI_Base_ICMS_Consumidor` varchar(1) DEFAULT 'N',
  `C004_Site` varchar(50) DEFAULT NULL,
  `C004_Logo_Marca` varchar(100) DEFAULT NULL,
  `C004_Observacao_Fiscal` text,
  `C004_Observacao_Fiscal_ICMS_Diferido` text,
  `C004_Flag_Controla_Estoque` varchar(1) DEFAULT NULL,
  `C004_Conta_Clientes` int(11) NOT NULL,
  `C004_Cojnta_Fornecedores` int(10) NOT NULL,
  `C004_Conta_Frete` int(10) NOT NULL,
  `C004_Meses_MMF` int(2) DEFAULT NULL,
  `C004_CFOP_Venda_Dentro_Estado` int(3) DEFAULT NULL,
  `C004_CFOP_Venda_Fora_Estado` int(3) DEFAULT NULL,
  `C004_Emite_NF_Caixa` varchar(1) DEFAULT NULL,
  `C004_Observacao_Promocao` text,
  `C004_Observacao_Logo` varchar(100) DEFAULT NULL,
  `C004_Observacao_Rodape_Orcamento` text,
  `C004_Observacao_Cabecalho` text,
  `C004_Flag_Decimais_Sugestao_Compra` varchar(1) DEFAULT NULL,
  `C004_Observacao_Cotacao_Compra` text,
  `C004_Ultimo_Numero_Cupom_Fiscal` int(10) DEFAULT NULL,
  `C004_PEDVEN_Botao_Liberar_Almoxarifado` varchar(1) DEFAULT NULL,
  `C004_PEDVEN_Botao_Liberar_Separacao` varchar(1) DEFAULT NULL,
  `C004_Impressora_Pedido_Almox` varchar(30) DEFAULT NULL,
  `C004_Linhas_Obs_Fiscal` int(3) DEFAULT NULL,
  `C004_Obs_Reduzido` text,
  `C004_Casas_Decimais_Preco` int(2) DEFAULT '2',
  `C004_Flag_Ativo` varchar(1) NOT NULL,
  `C004_Impressora_Comercial` varchar(50) DEFAULT NULL,
  `C004_Impressora_Balcao` varchar(50) DEFAULT NULL,
  `C004_Senha_Certificados` varchar(30) DEFAULT NULL,
  `C004_Localizacao_Certificados` varchar(200) DEFAULT NULL,
  `C004_CNAE_Fiscal` varchar(7) DEFAULT NULL,
  `C004_Percentual_PIS` decimal(6,2) DEFAULT '0.00',
  `C004_Percentual_COFINS` decimal(6,2) DEFAULT '0.00',
  `C004_Ambiente_NFe` enum('1','2') DEFAULT '2',
  PRIMARY KEY (`C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C004`
--

LOCK TABLES `C004` WRITE;
/*!40000 ALTER TABLE `C004` DISABLE KEYS */;
/*!40000 ALTER TABLE `C004` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C005`
--

-- DROP TABLE IF EXISTS `C005`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C005` (
  `C005_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C005_C006_Id` int(10) unsigned DEFAULT NULL,
  `C005_Campo_Conta` varchar(30) DEFAULT NULL,
  `C005_Entidade` varchar(4) DEFAULT NULL,
  `C005_Campo_Logico` varchar(50) DEFAULT NULL,
  `C005_Campo_Valor` text,
  `C005_Campo_Data` varchar(50) DEFAULT NULL,
  `C005_Condicao` text,
  `C005_Relacionamento` text,
  `C005_Flag_Reatualizar` int(1) DEFAULT NULL,
  `C005_Flag_Historico` int(1) NOT NULL DEFAULT '2',
  `C005_Referencia` varchar(200) DEFAULT NULL,
  `C005_Link` text,
  `C005_Flag_Positivo_Negativo` varchar(1) DEFAULT NULL,
  `C005_Flag_Agrupar` varchar(1) DEFAULT NULL,
  `C005_Flag_Ativo` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`C005_Id`),
  KEY `C005_FKIndex1` (`C005_C006_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C005`
--

LOCK TABLES `C005` WRITE;
/*!40000 ALTER TABLE `C005` DISABLE KEYS */;
/*!40000 ALTER TABLE `C005` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C006`
--

-- DROP TABLE IF EXISTS `C006`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C006` (
  `C006_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C006_Grupo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`C006_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C006`
--

LOCK TABLES `C006` WRITE;
/*!40000 ALTER TABLE `C006` DISABLE KEYS */;
/*!40000 ALTER TABLE `C006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C007`
--

-- DROP TABLE IF EXISTS `C007`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C007` (
  `C007_Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `C007_C007_Id` int(11) unsigned DEFAULT NULL,
  `C007_D024_Id` int(11) DEFAULT NULL,
  `C007_C004_Id` int(10) DEFAULT NULL,
  `C007_Id_Usuario_Chat` int(11) DEFAULT NULL,
  `C007_Login` varchar(20) DEFAULT NULL,
  `C007_Senha` varchar(20) DEFAULT NULL,
  `C007_Email` text,
  `C007_Nome` varchar(30) DEFAULT NULL,
  `C007_Primeiro_Nome` varchar(20) DEFAULT NULL,
  `C007_Especial` varchar(1) DEFAULT 'N',
  `C007_Ativo` char(1) DEFAULT 'S',
  `C007_Cargo` varchar(30) DEFAULT NULL,
  `C007_Rg` varchar(12) DEFAULT NULL,
  `C007_Cpf` varchar(14) DEFAULT NULL,
  `C007_Endereco` varchar(100) DEFAULT NULL,
  `C007_Complemento` varchar(20) DEFAULT NULL,
  `C007_Bairro` varchar(35) DEFAULT NULL,
  `C007_Cep` varchar(10) DEFAULT NULL,
  `C007_Cidade` varchar(35) DEFAULT NULL,
  `C007_Estado` varchar(25) DEFAULT NULL,
  `C007_Telefone` text,
  `C007_Id_Perfil` int(4) DEFAULT NULL,
  `C007_Data_Alteracao_Senha` date DEFAULT NULL,
  `C007_Usuario_Suporte` char(1) DEFAULT 'N',
  `C007_Usuario_Administracao` varchar(1) DEFAULT 'N',
  `C007_Usuario_Ancora` varchar(1) DEFAULT 'N',
  `C007_Usuario_Acessos` varchar(1) DEFAULT 'N',
  `C007_Codigo_Sistema_Antigo` varchar(11) DEFAULT NULL,
  `C007_Msn` varchar(100) DEFAULT NULL,
  `C007_Telefone_Direto` varchar(30) DEFAULT NULL,
  `C007_Flag_Expedicao` varchar(1) DEFAULT NULL,
  `C007_Estilo` varchar(30) DEFAULT NULL,
  `C007_Observacao` text,
  `C007_Celular` varchar(30) DEFAULT NULL,
  `C007_Flag_Vendedor` enum('','S','V','D') DEFAULT '',
  `C007_Flag_Comercial` varchar(1) DEFAULT NULL,
  `C007_Flag_Mostrar_Boleto` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`C007_Id`),
  KEY `C007_Primeiro_Nome` (`C007_Primeiro_Nome`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C007`
--

LOCK TABLES `C007` WRITE;
/*!40000 ALTER TABLE `C007` DISABLE KEYS */;
/*!40000 ALTER TABLE `C007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C008`
--

-- DROP TABLE IF EXISTS `C008`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C008` (
  `C008_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C008_Tipo` varchar(20) DEFAULT NULL,
  `C008_Flag_Vendas` varchar(1) DEFAULT ' ',
  `C008_Flag_Inicial_Codigo` varchar(3) DEFAULT ' ',
  `C008_Flag_Codigo_Proprio` varchar(1) DEFAULT ' ',
  `C008_Apelido_Linha` varchar(15) DEFAULT 'Linha',
  `C008_Apelido_Grupo` varchar(15) DEFAULT 'Grupo',
  `C008_Apelido_SubGrupo` varchar(15) DEFAULT 'Sub-Grupo',
  `C008_Apelido_Aplicacao` varchar(15) DEFAULT 'Aplicação',
  `C008_Apelido_Marca` varchar(15) DEFAULT 'Marca',
  `C008_Apelido_Complemento_Descricao` varchar(25) DEFAULT 'Complemento Descrição',
  `C008_Codigo_Padrao` varchar(10) DEFAULT ' ',
  `C008_Descricao_Padrao` varchar(30) DEFAULT ' ',
  `C008_Inclusao_Produto_Mostrar_Codigo_Grupos` enum('S','N') DEFAULT 'N',
  `C008_Inclusao_Produto_Delimitador_Codigo` varchar(1) DEFAULT '',
  `C008_Apelido_Preco_Revenda` varchar(20) DEFAULT 'Revenda',
  `C008_Apelido_Preco_Cons_Atacado` varchar(20) DEFAULT 'Consumidor Atacado',
  `C008_Apelido_Preco_Cons_Varejo` varchar(20) DEFAULT 'Consumidor Varejo',
  PRIMARY KEY (`C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C008`
--

LOCK TABLES `C008` WRITE;
/*!40000 ALTER TABLE `C008` DISABLE KEYS */;
/*!40000 ALTER TABLE `C008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C009`
--

-- DROP TABLE IF EXISTS `C009`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C009` (
  `C009_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C009_Descricao_Acesso` varchar(150) DEFAULT NULL,
  `C009_Entidade` char(4) DEFAULT NULL,
  `C009_Tela` varchar(150) DEFAULT NULL,
  `C009_Opcao` varchar(30) DEFAULT NULL,
  `C009_Grupo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`C009_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C009`
--

LOCK TABLES `C009` WRITE;
/*!40000 ALTER TABLE `C009` DISABLE KEYS */;
INSERT INTO `C009` VALUES (1,'LIBERAR IPV MINIMO',NULL,NULL,NULL,'99'),(2,'LIBERAR CREDITO',NULL,NULL,NULL,'99'),(3,'RETORNAR PEDIDO DO ALMOXARIFADO',NULL,NULL,NULL,'99'),(4,'CANCELAR PEDIDO VENDA',NULL,NULL,NULL,'99'),(5,'CANCELAR PEDIDO PENDENTE',NULL,NULL,NULL,'99'),(6,'ALTERAR OBSERVACAO COMPRA EM PRODUTOS',NULL,NULL,NULL,'99'),(7,'CANCELAR NOTA FISCAL EMITIDA',NULL,NULL,NULL,'99'),(8,'INCLUSAO E ALTERAÇÃO DE ITENS (D001)',NULL,NULL,NULL,'99'),(9,'ATUALIZAR D.E.',NULL,NULL,NULL,'99'),(10,'ALTERAÇÃO DO CAMPO COMPLEMENTO (D001)',NULL,NULL,NULL,'99'),(11,'ALTERAÇÃO DO CAMPO PESO (D001)',NULL,NULL,NULL,'99'),(12,'MENS.MOTIVO PEDIDO NEGADO',NULL,NULL,NULL,'99'),(13,'LIBERAR NOTA DE EXPOSIÇÃO',NULL,NULL,NULL,'99'),(14,'CANCELAR NOTA FISCAL RECEBIDA',NULL,NULL,NULL,'99'),(15,'LIBERAR TRANSFERENCIA ABAIXO DO IPV',NULL,NULL,NULL,'99'),(16,'ATIVAR / INATIVAR CLIENTE/FORNECEDOR',NULL,NULL,NULL,'99'),(17,'ATIVAR / INATIVAR TRANSPORTADORA',NULL,NULL,NULL,'99'),(18,'MENSAGEM PIN',NULL,NULL,NULL,'99'),(19,'ALTERAÇÃO DO CAMPO FOTO (D001)',NULL,NULL,NULL,'99'),(20,'CONTROLE DE REPRESENTANTE',NULL,NULL,NULL,'99'),(21,'ALTERAR CFOP NOTAS',NULL,NULL,NULL,'99'),(22,'EXCLUIR CLIENTE',NULL,NULL,NULL,'99'),(32,'PROCESSAR MONTAGEM',NULL,NULL,NULL,'99'),(33,'MENSAGEM PEDIDO PARA IMPRIMIR',NULL,NULL,NULL,'99'),(34,'ALTERAR PRECOS NO ORCAMENTO/PEDIDOS',NULL,NULL,NULL,'99'),(35,'IMPRIMIR COPIA NF OFICIAL',NULL,NULL,NULL,'99'),(36,'MUDAR PRE-CADASTRO CLIENTE',NULL,NULL,NULL,'99'),(37,'PERMITE IMPRIMIR A COLUNA CUSTOS NO INVENTARIO',NULL,NULL,NULL,'99'),(38,'ALTERAR PEDIDO PENDENTE',NULL,NULL,NULL,'99'),(39,'CLIENTES/FORNECEDORES',NULL,NULL,NULL,'2'),(40,'REPRESENTANTES',NULL,NULL,NULL,'2'),(41,'TRANSPORTADORA',NULL,NULL,NULL,'2'),(42,'PRODUTOS',NULL,NULL,NULL,'2'),(43,'C.F.O.P',NULL,NULL,NULL,'2'),(44,'CLASS.FISCAL',NULL,NULL,NULL,'2'),(45,'BANCOS/CAIXAS',NULL,NULL,NULL,'2'),(46,'PORTADORES',NULL,NULL,NULL,'2'),(47,'CENTRO CUSTO',NULL,NULL,NULL,'2'),(48,'EMPRESA',NULL,NULL,NULL,'2'),(49,'PERFIL',NULL,NULL,NULL,'2'),(50,'VENDEDORES',NULL,NULL,NULL,'2'),(51,'USUARIOS',NULL,NULL,NULL,'2'),(52,'LISTA DE PRECOS',NULL,NULL,NULL,'3'),(53,'PRE PEDIDO',NULL,NULL,NULL,'3'),(54,'ORCAMENTOS',NULL,NULL,NULL,'3'),(55,'PEDIDOS',NULL,NULL,NULL,'3'),(56,'NOTAS FISCAIS',NULL,NULL,NULL,'3'),(57,'EMITIR NOTAS FISCAIS',NULL,NULL,NULL,'3'),(58,'INUTILIZAR NF-e',NULL,NULL,NULL,'3'),(59,'COMODATO',NULL,NULL,NULL,'3'),(60,'ORDEM DE SERVIÇO',NULL,NULL,NULL,'3'),(61,'CONTAS A RECEBER',NULL,NULL,NULL,'4'),(62,'CONTAS A PAGAR',NULL,NULL,NULL,'4'),(63,'CAIXA E BANCOS',NULL,NULL,NULL,'4'),(64,'BOLETO BANCARIO',NULL,NULL,NULL,'4'),(65,'FATURAR ROMANEIO',NULL,NULL,NULL,'4'),(66,'PROJECAO ORCAMENTARIA',NULL,NULL,NULL,'4'),(67,'CONTROLE DE ESTOQUE',NULL,NULL,NULL,'5'),(68,'NOTAS FISCAIS RECEBIDAS',NULL,NULL,NULL,'5'),(69,'EXPEDICAO MERCADORIA',NULL,NULL,NULL,'5'),(70,'DIVERGENCIA ESTOQUE',NULL,NULL,NULL,'5'),(71,'MONTAGEM',NULL,NULL,NULL,'5'),(72,'FLUXO DE CAIXA',NULL,NULL,NULL,'6'),(73,'BALANCETE',NULL,NULL,NULL,'6'),(74,'RELATORIO CONTAS A PAGAR',NULL,NULL,NULL,'6'),(75,'RELATORIO CONTAS A RECEBER',NULL,NULL,NULL,'6'),(76,'MAPA DE VENDA',NULL,NULL,NULL,'6'),(77,'INVENTARIO ESTOQUE',NULL,NULL,NULL,'6'),(78,'COMISSAO REPRESENTANTES',NULL,NULL,NULL,'6'),(79,'FECHAMENTOS',NULL,NULL,NULL,'6'),(80,'GERAR SINTEGRA',NULL,NULL,NULL,'7'),(81,'XML NFe',NULL,NULL,NULL,'7'),(82,'UF(ICMS)',NULL,NULL,NULL,'8'),(83,'CIDADES',NULL,NULL,NULL,'8'),(84,'LAYOUT NOTA FISCAL',NULL,NULL,NULL,'8'),(85,'MYSQL',NULL,NULL,NULL,'8'),(86,'PARAMETRIZAÇOES',NULL,NULL,NULL,'8'),(87,'MAILING',NULL,NULL,NULL,'9'),(88,'RECLAME JÁ',NULL,NULL,NULL,'9'),(89,'CARTEIRA CLIENTES',NULL,NULL,NULL,'3'),(90,'EXCLUIR NOTA REJEITADA PELO SEFAZ',NULL,NULL,NULL,'99'),(91,'COTAÇÃO',NULL,NULL,NULL,'5'),(92,'COMPRAS EMERGÊNCIA',NULL,NULL,NULL,'5'),(93,'COMPRAS ESTOQUE',NULL,NULL,NULL,'5'),(94,'ORDEM COMPRA',NULL,NULL,NULL,'5'),(95,'CURVA ABC VENDEDORES',NULL,NULL,NULL,'3'),(96,'ANALISE DE CRÉDITO',NULL,NULL,NULL,'4'),(97,'APROVACAO DE ORCAMENTOS',NULL,NULL,NULL,'3'),(98,'C.R.M.',NULL,NULL,NULL,'3'),(99,'IPV',NULL,NULL,NULL,'8'),(100,'MOTIVOS CANCELAMENTO VENDAS',NULL,NULL,NULL,'8'),(101,'TABELA DE PRECOS',NULL,NULL,NULL,'2'),(102,'TABELA FORNECEDOR',NULL,NULL,NULL,'2'),(103,'CURVA ABC PRODUTOS',NULL,NULL,NULL,'5');
/*!40000 ALTER TABLE `C009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C010`
--

-- DROP TABLE IF EXISTS `C010`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C010` (
  `C010_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C010_C021_Id` int(10) unsigned NOT NULL,
  `C010_C009_Id` int(10) unsigned NOT NULL,
  `C010_Id_Perfil` int(10) unsigned NOT NULL,
  `C010_C007_Id` int(10) DEFAULT NULL,
  `C010_Acesso_Liberado` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`C010_Id`),
  KEY `C010_FKIndex1` (`C010_C009_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C010`
--

LOCK TABLES `C010` WRITE;
/*!40000 ALTER TABLE `C010` DISABLE KEYS */;
INSERT INTO `C010` VALUES (1,1,39,1,NULL,'S'),(2,1,40,1,NULL,'S'),(3,1,41,1,NULL,'S'),(4,1,42,1,NULL,'S'),(5,1,43,1,NULL,'S'),(6,1,44,1,NULL,'S'),(7,1,45,1,NULL,'S'),(8,1,46,1,NULL,'S'),(9,1,47,1,NULL,'S'),(10,1,48,1,NULL,'S'),(11,1,49,1,NULL,'S'),(12,1,50,1,NULL,'S'),(13,1,51,1,NULL,'S'),(14,1,101,1,NULL,'S'),(15,1,102,1,NULL,'S'),(16,1,52,1,NULL,'S'),(17,1,53,1,NULL,'S'),(18,1,54,1,NULL,'S'),(19,1,55,1,NULL,'S'),(20,1,56,1,NULL,'S'),(21,1,57,1,NULL,'S'),(22,1,58,1,NULL,'S'),(23,1,59,1,NULL,'S'),(24,1,60,1,NULL,'S'),(25,1,89,1,NULL,'S'),(26,1,95,1,NULL,'S'),(27,1,97,1,NULL,'S'),(28,1,98,1,NULL,'S'),(29,1,61,1,NULL,'S'),(30,1,62,1,NULL,'S'),(31,1,63,1,NULL,'S'),(32,1,64,1,NULL,'S'),(33,1,65,1,NULL,'S'),(34,1,66,1,NULL,'S'),(35,1,96,1,NULL,'S'),(36,1,67,1,NULL,'S'),(37,1,68,1,NULL,'S'),(38,1,69,1,NULL,'S'),(39,1,70,1,NULL,'S'),(40,1,71,1,NULL,'S'),(41,1,91,1,NULL,'S'),(42,1,92,1,NULL,'S'),(43,1,93,1,NULL,'S'),(44,1,94,1,NULL,'S'),(45,1,103,1,NULL,'S'),(46,1,72,1,NULL,'S'),(47,1,73,1,NULL,'S'),(48,1,74,1,NULL,'S'),(49,1,75,1,NULL,'S'),(50,1,76,1,NULL,'S'),(51,1,77,1,NULL,'S'),(52,1,78,1,NULL,'S'),(53,1,79,1,NULL,'S'),(54,1,80,1,NULL,'S'),(55,1,81,1,NULL,'S'),(56,1,82,1,NULL,'S'),(57,1,83,1,NULL,'S'),(58,1,84,1,NULL,'S'),(59,1,85,1,NULL,'S'),(60,1,86,1,NULL,'S'),(61,1,99,1,NULL,'S'),(62,1,100,1,NULL,'S'),(63,1,87,1,NULL,'S'),(64,1,88,1,NULL,'S'),(65,1,1,1,NULL,'S'),(66,1,2,1,NULL,'S'),(67,1,3,1,NULL,'S'),(68,1,4,1,NULL,'S'),(69,1,5,1,NULL,'S'),(70,1,6,1,NULL,'S'),(71,1,7,1,NULL,'S'),(72,1,8,1,NULL,'S'),(73,1,10,1,NULL,'S'),(74,1,9,1,NULL,'S'),(75,1,11,1,NULL,'S'),(76,1,12,1,NULL,'S'),(77,1,13,1,NULL,'S'),(78,1,14,1,NULL,'S'),(79,1,15,1,NULL,'S'),(80,1,16,1,NULL,'S'),(81,1,17,1,NULL,'S'),(82,1,18,1,NULL,'S'),(83,1,19,1,NULL,'S'),(84,1,20,1,NULL,'S'),(85,1,21,1,NULL,'S'),(86,1,22,1,NULL,'S'),(87,1,32,1,NULL,'S'),(88,1,33,1,NULL,'S'),(89,1,34,1,NULL,'S'),(90,1,35,1,NULL,'S'),(91,1,36,1,NULL,'S'),(92,1,37,1,NULL,'S'),(93,1,38,1,NULL,'S'),(94,1,90,1,NULL,'S');
/*!40000 ALTER TABLE `C010` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C011`
--

-- DROP TABLE IF EXISTS `C011`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C011` (
  `C011_Id` int(11) NOT NULL AUTO_INCREMENT,
  `C011_C002_Id` int(11) DEFAULT '0',
  `C011_Nome_Arquivo` varchar(50) DEFAULT NULL,
  `C011_Data_Importacao` datetime DEFAULT NULL,
  `C011_Flag_Erro` varchar(1) DEFAULT NULL,
  `C011_Nome_Arquivo_Log` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`C011_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C011`
--

LOCK TABLES `C011` WRITE;
/*!40000 ALTER TABLE `C011` DISABLE KEYS */;
/*!40000 ALTER TABLE `C011` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C012`
--

-- DROP TABLE IF EXISTS `C012`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C012` (
  `C012_Id` int(11) NOT NULL AUTO_INCREMENT,
  `C012_Nome` text,
  `C012_Clausula_Sql` text,
  PRIMARY KEY (`C012_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C012`
--

LOCK TABLES `C012` WRITE;
/*!40000 ALTER TABLE `C012` DISABLE KEYS */;
/*!40000 ALTER TABLE `C012` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C013`
--

-- DROP TABLE IF EXISTS `C013`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C013` (
  `C013_C007_Id` int(10) NOT NULL DEFAULT '0',
  `C013_C004_Id` int(10) DEFAULT NULL,
  `C013_Login` datetime DEFAULT NULL,
  `C013_Entidade` varchar(10) DEFAULT NULL,
  `C013_Atualizado` datetime DEFAULT NULL,
  `C013_Tela` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`C013_C007_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C013`
--

LOCK TABLES `C013` WRITE;
/*!40000 ALTER TABLE `C013` DISABLE KEYS */;
/*!40000 ALTER TABLE `C013` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C014`
--

-- DROP TABLE IF EXISTS `C014`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C014` (
  `C014_Id` int(10) NOT NULL AUTO_INCREMENT,
  `C014_C005_Id` int(10) DEFAULT NULL,
  `C014_Campo_Logico` varchar(30) DEFAULT NULL,
  `C014_Campo_Fisico` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`C014_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C014`
--

LOCK TABLES `C014` WRITE;
/*!40000 ALTER TABLE `C014` DISABLE KEYS */;
/*!40000 ALTER TABLE `C014` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C015`
--

-- DROP TABLE IF EXISTS `C015`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C015` (
  `C015_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C015_Impressora` varchar(50) DEFAULT NULL,
  `C015_Comando` varchar(50) DEFAULT NULL,
  `C015_C004_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`C015_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C015`
--

LOCK TABLES `C015` WRITE;
/*!40000 ALTER TABLE `C015` DISABLE KEYS */;
/*!40000 ALTER TABLE `C015` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C016`
--

-- DROP TABLE IF EXISTS `C016`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C016` (
  `C016_Id` int(10) NOT NULL AUTO_INCREMENT,
  `C016_C007_Id` int(10) NOT NULL,
  `C016_Entidade` varchar(4) NOT NULL,
  `C016_Id_Entidade` int(10) NOT NULL,
  `C016_Data_Hora` datetime DEFAULT NULL,
  `C016_Operacao` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`C016_Id`),
  KEY `IDX_Entidade` (`C016_Entidade`),
  KEY `IDX_Id_Entidade` (`C016_Id_Entidade`),
  KEY `IDX_Operacao` (`C016_Operacao`),
  KEY `IDX_Data_Hora` (`C016_Data_Hora`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C016`
--

LOCK TABLES `C016` WRITE;
/*!40000 ALTER TABLE `C016` DISABLE KEYS */;
/*!40000 ALTER TABLE `C016` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C017`
--

-- DROP TABLE IF EXISTS `C017`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C017` (
  `C017_Id` int(11) NOT NULL AUTO_INCREMENT,
  `C017_C004_Id` int(11) DEFAULT '0',
  `C017_Caracter_Complementar` char(1) DEFAULT '*',
  `C017_Linhas_Pagina` int(2) DEFAULT '0',
  `C017_Tipo_Impressao` enum('LPR','PDF') DEFAULT 'LPR',
  `C017_PDF_Largura_Papel` decimal(6,3) DEFAULT '0.000',
  `C017_PDF_Altura_Papel` decimal(6,3) DEFAULT '0.000',
  `C017_PDF_Margem_Esquerda` decimal(5,2) DEFAULT '0.00',
  `C017_PDF_Margem_Direita` decimal(5,2) DEFAULT '0.00',
  `C017_PDF_Margem_Superior` decimal(5,2) DEFAULT '0.00',
  `C017_PDF_Tamanho_Letra_Expandido` decimal(5,2) DEFAULT '0.00',
  `C017_PDF_Tamanho_Letra_Normal` decimal(5,2) DEFAULT '0.00',
  `C017_PDF_Tamanho_Letra_Comprimido` decimal(5,2) DEFAULT '0.00',
  `C017_PDF_Altura_Linha` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`C017_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C017`
--

LOCK TABLES `C017` WRITE;
/*!40000 ALTER TABLE `C017` DISABLE KEYS */;
/*!40000 ALTER TABLE `C017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C018`
--

-- DROP TABLE IF EXISTS `C018`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C018` (
  `C018_Id` int(11) NOT NULL AUTO_INCREMENT,
  `C018_C017_Id` int(11) DEFAULT NULL,
  `C018_Sessao` int(1) DEFAULT '0',
  `C018_Descricao` varchar(200) DEFAULT NULL,
  `C018_Tipo_Letra` varchar(50) DEFAULT NULL,
  `C018_Campo` text,
  `C018_Flag_Ativo` enum('S','N') DEFAULT 'S',
  `C018_Flag_Especial` varchar(20) DEFAULT NULL,
  `C018_Linha` int(4) DEFAULT '0',
  `C018_Posicao_Inicial` int(3) DEFAULT '0',
  `C018_Tamanho` int(3) DEFAULT '0',
  `C018_Alinhamento` enum('E','C','D') DEFAULT 'E',
  `C018_Flag_Apenas_Ultima_Pagina` enum('S','N') DEFAULT 'N',
  `C018_Formatacao` varchar(1) DEFAULT NULL,
  `C018_Casas_Decimais` int(2) DEFAULT '2',
  PRIMARY KEY (`C018_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C018`
--

LOCK TABLES `C018` WRITE;
/*!40000 ALTER TABLE `C018` DISABLE KEYS */;
/*!40000 ALTER TABLE `C018` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C019`
--

-- DROP TABLE IF EXISTS `C019`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C019` (
  `C019_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C019_C021_Id` int(10) unsigned NOT NULL,
  `C019_C004_Id` int(10) unsigned NOT NULL,
  `C019_C007_Id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`C019_Id`),
  UNIQUE KEY `Duplicidade` (`C019_C021_Id`,`C019_C004_Id`,`C019_C007_Id`),
  KEY `C019_FKIndex1` (`C019_C007_Id`),
  KEY `C019_FKIndex3` (`C019_C004_Id`),
  CONSTRAINT `C019_ibfk_1` FOREIGN KEY (`C019_C021_Id`) REFERENCES `C021` (`C021_Id`) ON DELETE CASCADE,
  CONSTRAINT `C019_ibfk_2` FOREIGN KEY (`C019_C004_Id`) REFERENCES `C004` (`C004_Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C019`
--

LOCK TABLES `C019` WRITE;
/*!40000 ALTER TABLE `C019` DISABLE KEYS */;
/*!40000 ALTER TABLE `C019` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C020`
--

-- DROP TABLE IF EXISTS `C020`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C020` (
  `C020_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C020_C004_Id` int(10) unsigned DEFAULT NULL,
  `C020_Orcamento_Alterar_Preco_Venda` enum('S','N') DEFAULT 'S',
  `C020_Orcamento_Mostrar_Fornecedor_Tipo` enum('NOME','NI') DEFAULT 'NOME',
  `C020_Orcamento_Permite_IPV_Cliente_Zerado` enum('S','N') DEFAULT 'N',
  `C020_Orcamento_Permite_Pre_Cadastro` enum('S','N') DEFAULT 'N',
  `C020_Orcamento_Permite_Item_Sem_Preco` enum('S','N') DEFAULT 'S',
  `C020_Orcamento_Visualizar_IPV_Item` enum('S','N') DEFAULT 'S',
  `C020_Orcamento_Visualizar_Custo` enum('S','N') DEFAULT 'S',
  `C020_Orcamento_Puxar_Descricao_Orcamento` enum('S','N') DEFAULT 'N',
  `C020_Orcamento_Imprimir_Campo_Entrega` enum('S','N') DEFAULT 'N',
  `C020_Orcamento_Imprimir_Campo_IPI` enum('S','N') DEFAULT 'S',
  `C020_Pedido_Alterar_Preco_Venda` enum('S','N') DEFAULT 'S',
  `C020_Pedido_Mostrar_Fornecedor_Tipo` enum('NOME','NI') DEFAULT 'NOME',
  `C020_Pedido_Visualizar_IPV_Item` enum('S','N') DEFAULT 'S',
  `C020_Pedido_Visualizar_Custo` enum('S','N') DEFAULT 'S',
  `C020_Pedido_Puxar_Descricao_Pedido` enum('S','N') DEFAULT 'N',
  `C020_Pedido_Imprimir_Campo_Entrega` enum('S','N') DEFAULT 'N',
  `C020_Pedido_Imprimir_Campo_IPI` enum('S','N') DEFAULT 'S',
  `C020_Impressora_NF_Recebida` varchar(30) DEFAULT NULL,
  `C020_Modulo_Insuficiencia` varchar(50) DEFAULT NULL,
  `C020_Orcamento_Obter_Ultimo_Preco_Cliente` enum('S','N') DEFAULT 'S',
  `C020_Orcamento_Representante_Obrigatorio` enum('S','N') DEFAULT 'S',
  `C020_Consulta_Paginacao` enum('S','N') DEFAULT 'S',
  `C020_Consulta_Linhas_Paginacao` int(4) DEFAULT '15',
  `C020_Modelo_Menu` varchar(30) DEFAULT 'menu_principal.hardness',
  `C020_Loja_Virtual_Visitante_Consultar_Produto` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Visitante_Ver_Preco` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Visitante_Comprar` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Visitante_Pagar` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Visitante_Cadastrar` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Cliente_Ver_Preco` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Cliente_Comprar` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Cliente_Pagar` enum('S','N') DEFAULT 'S',
  `C020_Loja_Virtual_Nome_Email` varchar(100) DEFAULT NULL,
  `C020_Loja_Virtual_Email` varchar(60) DEFAULT NULL,
  `C020_Loja_Virtual_Dominio` varchar(100) DEFAULT NULL,
  `C020_Loja_Virtual_Formato_Lista` enum('S','N') DEFAULT 'N',
  `C020_Flag_Expedicao` enum('S','N') DEFAULT 'S',
  `C020_NFe_SCAN_Ativado` enum('S','N') DEFAULT 'N',
  `C020_Caixa_Obrigatorio_Abrir_Fechar_Caixa` enum('S','N') DEFAULT 'N',
  `C020_Caixa_Cupom_Fiscal` enum('S','N') DEFAULT 'N',
  `C020_NFe_Habilitada` enum('S','N') DEFAULT 'S',
  `C020_Inclusao_Produtos_Simples` enum('S','N') DEFAULT 'S',
  `C020_Pre_Pedido_Horario_Entrega_Obrigatorio` enum('S','N') DEFAULT 'S',
  `C020_Pre_Pedido_Vendedor_Obrigatorio` enum('S','N') DEFAULT 'S',
  `C020_Pre_Pedido_Prazos_Obrigatorio` enum('S','N') DEFAULT 'S',
  `C020_IPV_Revenda` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Atacado` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Varejo` decimal(10,4) DEFAULT '0.0000',
  `C020_Casas_Decimais_Quantidade` int(1) DEFAULT '3',
  `C020_Cliente_Telefone_Obrigatorio` enum('S','N') DEFAULT 'S',
  `C020_Pedido_Gerar_Pedido_Sem_Estoque` enum('S','N') DEFAULT 'S',
  `C020_Data_Ultima_Atualizacao` datetime DEFAULT '0000-00-00 00:00:00',
  `C020_Ultima_Revisao` int(10) DEFAULT NULL,
  `C020_Modelo_Sistema_Nfe` varchar(2) DEFAULT '01',
  `C020_NFe_Tipo_Certificado` enum('A1','A3') DEFAULT 'A1',
  `C020_Cliente_Validar_Duplicidade_CNPJ_CPF` enum('S','N') DEFAULT 'S',
  `C020_Regras_Faturamento_Vendas` text,
  `C020_IPV_Revenda_Minimo` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Atacado_Minimo` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Varejo_Minimo` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Revenda_Minimo_Supervisor` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Atacado_Minimo_Supervisor` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Varejo_Minimo_Supervisor` decimal(10,4) DEFAULT '0.0000',
  `C020_IPV_Custo` decimal(10,4) DEFAULT '0.0000',
  `C020_NFe_Obrigatorio_Cadastro_Produto` enum('S','N') DEFAULT 'S',
  `C020_NFe_Treinamento` enum('S','N') DEFAULT 'N',
  `C020_NFe_Tamanho_Campo_Descricao` int(3) DEFAULT '60',
  `C020_NFe_Tipo_DANFE` enum('HTML','PDF') DEFAULT 'HTML',
  PRIMARY KEY (`C020_Id`),
  UNIQUE KEY `Duplicidade` (`C020_C004_Id`),
  KEY `C020_IDX_EMPRESAS` (`C020_C004_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C020`
--

LOCK TABLES `C020` WRITE;
/*!40000 ALTER TABLE `C020` DISABLE KEYS */;
/*!40000 ALTER TABLE `C020` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C021`
--

-- DROP TABLE IF EXISTS `C021`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C021` (
  `C021_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C021_Nome_Perfil` varchar(50) DEFAULT NULL,
  `C021_Flag_Visualizar_Opcoes_Desabilitadas` enum('S','N') DEFAULT 'N',
  `C021_Visualizar_Clientes` enum('S','N') DEFAULT 'S',
  `C021_Visualizar_Produtos` enum('S','N') DEFAULT 'S',
  `C021_Visualizar_Orcamento` enum('S','N') DEFAULT 'S',
  `C021_Visualizar_Expedicao` enum('S','N') DEFAULT 'S',
  `C021_Visualizar_Caixa` enum('S','N') DEFAULT 'S',
  `C021_Visualizar_Transportadora` enum('S','N') DEFAULT 'S',
  `C021_Iniciar_Opcoes_Menu_Abertas` enum('S','N') DEFAULT 'N',
  `C021_Pagina_Inicial` varchar(250) DEFAULT 'principal/pag_inicial.php3',
  `C021_Visualizar_Pedido` enum('S','N') DEFAULT 'N',
  `C021_Visualizar_PrePedido` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`C021_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C021`
--

LOCK TABLES `C021` WRITE;
/*!40000 ALTER TABLE `C021` DISABLE KEYS */;
/*!40000 ALTER TABLE `C021` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C022`
--

-- DROP TABLE IF EXISTS `C022`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C022` (
  `C022_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C022_C021_Id` int(10) unsigned NOT NULL,
  `C022_Id_Menu` int(10) unsigned NOT NULL,
  `C022_Acesso_Liberado` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`C022_Id`),
  UNIQUE KEY `Duplicidade` (`C022_C021_Id`,`C022_Id_Menu`),
  KEY `C022_FKIndex1` (`C022_C021_Id`),
  CONSTRAINT `C022_ibfk_1` FOREIGN KEY (`C022_C021_Id`) REFERENCES `C021` (`C021_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C022`
--

LOCK TABLES `C022` WRITE;
/*!40000 ALTER TABLE `C022` DISABLE KEYS */;
/*!40000 ALTER TABLE `C022` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C024`
--

-- DROP TABLE IF EXISTS `C024`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C024` (
  `C024_Banco_Dados` varchar(10) NOT NULL,
  `C024_D024_Id` int(10) unsigned DEFAULT '0',
  `C024_C026_Id` int(10) DEFAULT '0',
  PRIMARY KEY (`C024_Banco_Dados`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C024`
--

LOCK TABLES `C024` WRITE;
/*!40000 ALTER TABLE `C024` DISABLE KEYS */;
/*!40000 ALTER TABLE `C024` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C025`
--

-- DROP TABLE IF EXISTS `C025`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C025` (
  `C025_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C025_D024_Id` varchar(10) NOT NULL DEFAULT '0',
  `C025_Banco_Dados` varchar(10) DEFAULT NULL,
  `C025_Login` varchar(20) DEFAULT NULL,
  `C025_Senha` varchar(10) DEFAULT NULL,
  `C025_Email` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`C025_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C025`
--

LOCK TABLES `C025` WRITE;
/*!40000 ALTER TABLE `C025` DISABLE KEYS */;
/*!40000 ALTER TABLE `C025` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `C026`
--

-- DROP TABLE IF EXISTS `C026`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `C026` (
  `C026_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `C026_Razao_Social` varchar(40) DEFAULT NULL,
  `C026_Nome_Contador` varchar(40) DEFAULT NULL,
  `C026_Nome_Contato` varchar(40) DEFAULT NULL,
  `C026_Telefone` varchar(40) DEFAULT NULL,
  `C026_Observacao` text,
  `C026_Login` varchar(20) DEFAULT NULL,
  `C026_Senha` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`C026_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `C026`
--

LOCK TABLES `C026` WRITE;
/*!40000 ALTER TABLE `C026` DISABLE KEYS */;
/*!40000 ALTER TABLE `C026` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D001`
--

-- DROP TABLE IF EXISTS `D001`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D001` (
  `D001_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D001_D037_Id` int(10) unsigned DEFAULT NULL,
  `D001_D002_Id` int(10) unsigned DEFAULT NULL,
  `D001_D060_Id` int(3) unsigned NOT NULL,
  `D001_D003_Id` int(10) unsigned DEFAULT NULL,
  `D001_D015_Id` int(10) unsigned DEFAULT NULL,
  `D001_C008_Id` int(10) unsigned DEFAULT NULL,
  `D001_D005_Id` int(10) unsigned DEFAULT NULL,
  `D001_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D001_Descricao_Produto` varchar(60) DEFAULT NULL,
  `D001_Descricao_Comercial` varchar(40) DEFAULT NULL,
  `D001_Data_Ultimo_Movimento` date DEFAULT NULL,
  `D001_Peso_Unitario_Kg` decimal(10,3) NOT NULL DEFAULT '0.000',
  `D001_Data_Cadastro` date DEFAULT NULL,
  `D001_Aplicacao` varchar(60) DEFAULT NULL,
  `D001_Flag_Ativo` varchar(1) DEFAULT NULL,
  `D001_Foto` varchar(30) DEFAULT NULL,
  `D001_Codigo_Descricao` int(5) DEFAULT NULL,
  `D001_Codigo_Linha` varchar(11) DEFAULT NULL,
  `D001_Codigo_Grupo` int(5) DEFAULT NULL,
  `D001_Quantidade_Comprar` int(10) DEFAULT '0',
  `D001_Frequencia_Venda` int(10) DEFAULT '0',
  `D001_Flag_Classe` varchar(1) DEFAULT NULL,
  `D001_Observacao_Compra` text,
  `D001_Especificacoes` text,
  `D001_Codigo_SubGrupo` int(5) DEFAULT NULL,
  `D001_Flag_Tipo` enum('PRODUZIDO','COMPRADO') DEFAULT NULL,
  `D001_Flag_Permite_Venda_Sem_Estoque` enum('S','N') DEFAULT 'N',
  `D001_Flag_Visualizar_Site` enum('S','N') DEFAULT 'S',
  `D001_Codigo_Barras` varchar(13) DEFAULT NULL,
  `D001_Peso_Total_Kg` decimal(10,3) DEFAULT '0.000',
  `D001_Qte_Embalagem` decimal(10,3) DEFAULT '0.000',
  PRIMARY KEY (`D001_Id`),
  KEY `D001_Index_Unidades` (`D001_D037_Id`),
  KEY `D001_Index_Linha_Id` (`D001_D003_Id`),
  KEY `D001_Index_Descricao_Id` (`D001_D002_Id`),
  KEY `D001_Index_Grupo_Id` (`D001_D015_Id`),
  KEY `D001_index_Codigo_Produto` (`D001_Codigo_Produto`),
  KEY `D001_Index_Codigo_Grupo` (`D001_Codigo_Grupo`),
  KEY `D001_Index_Tipo` (`D001_C008_Id`),
  KEY `IDX_Descricao` (`D001_Descricao_Produto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D001`
--

LOCK TABLES `D001` WRITE;
/*!40000 ALTER TABLE `D001` DISABLE KEYS */;
/*!40000 ALTER TABLE `D001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D002`
--

-- DROP TABLE IF EXISTS `D002`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D002` (
  `D002_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D002_D060_Id` int(3) unsigned NOT NULL,
  `D002_C008_Id` int(10) unsigned DEFAULT NULL,
  `D002_Codigo` int(5) DEFAULT NULL,
  `D002_Descricao_Produto` varchar(50) DEFAULT NULL,
  `D002_Grupo` int(5) DEFAULT NULL,
  `D002_IPV` decimal(10,4) DEFAULT NULL,
  `D002_Flag_Visualizar_Site` enum('S','N') DEFAULT 'S',
  `D002_Codigo_Padrao` varchar(10) DEFAULT '',
  `D002_Descricao_Padrao` varchar(30) DEFAULT '',
  PRIMARY KEY (`D002_Id`),
  KEY `D002_index_Codigo` (`D002_Codigo`),
  KEY `D002_index_Tipo` (`D002_C008_Id`),
  KEY `IDX_Descricao` (`D002_Descricao_Produto`),
  KEY `D002_Grupo` (`D002_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D002`
--

LOCK TABLES `D002` WRITE;
/*!40000 ALTER TABLE `D002` DISABLE KEYS */;
/*!40000 ALTER TABLE `D002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D003`
--

-- DROP TABLE IF EXISTS `D003`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D003` (
  `D003_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D003_C008_Id` int(10) unsigned DEFAULT NULL,
  `D003_Nome_Linha` varchar(30) DEFAULT NULL,
  `D003_Flag_Tipo` varchar(2) DEFAULT NULL,
  `D003_Flag_Trabalho` varchar(1) DEFAULT NULL,
  `D003_Flag_Visualizar_Site` enum('S','N') DEFAULT 'S',
  `D003_Codigo_Padrao` varchar(10) DEFAULT '',
  `D003_Descricao_Padrao` varchar(30) DEFAULT '',
  `D003_D082_Id` int(10) DEFAULT NULL,
  PRIMARY KEY (`D003_Id`),
  KEY `D003_index_Nome_Linha` (`D003_Nome_Linha`),
  KEY `D003_index_Tipo` (`D003_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D003`
--

LOCK TABLES `D003` WRITE;
/*!40000 ALTER TABLE `D003` DISABLE KEYS */;
/*!40000 ALTER TABLE `D003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D004`
--

-- DROP TABLE IF EXISTS `D004`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D004` (
  `D004_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D004_C004_Id` int(11) NOT NULL DEFAULT '0',
  `D004_C008_Id` int(11) NOT NULL DEFAULT '0',
  `D004_Local` varchar(10) DEFAULT NULL,
  `D004_Descricao` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`D004_Id`),
  KEY `D004_Index_Local` (`D004_Local`),
  KEY `D004_Index_C004_Id` (`D004_C004_Id`),
  KEY `D004_Index_D004_Id` (`D004_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D004`
--

LOCK TABLES `D004` WRITE;
/*!40000 ALTER TABLE `D004` DISABLE KEYS */;
/*!40000 ALTER TABLE `D004` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D005`
--

-- DROP TABLE IF EXISTS `D005`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D005` (
  `D005_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D005_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `D005_Flag_Isento_IPI` int(1) DEFAULT '2',
  `D005_Percentual_Reducao_IPI` int(10) unsigned DEFAULT NULL,
  `D005_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `D005_Observacao` text,
  `D005_Flag_Tipo_IPI` varchar(1) DEFAULT NULL,
  `D005_Aliquota_PIS` decimal(5,2) DEFAULT '0.00',
  `D005_Aliquota_COFINS` decimal(5,2) DEFAULT '0.00',
  `D005_Aliquota_Imposto_Importacao` decimal(5,2) DEFAULT '0.00',
  `D005_Flag_ST` varchar(1) DEFAULT NULL,
  `D005_Flag_VA` varchar(1) DEFAULT NULL,
  `D005_Valor_VA` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`D005_Id`),
  KEY `D005_index_Classificacao_Fiscal` (`D005_Classificacao_Fiscal`)
) ENGINE=MyISAM AUTO_INCREMENT=933 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D005`
--

LOCK TABLES `D005` WRITE;
/*!40000 ALTER TABLE `D005` DISABLE KEYS */;
/*!40000 ALTER TABLE `D005` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D006`
--

-- DROP TABLE IF EXISTS `D006`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D006` (
  `D006_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D006_D014_Id` int(10) unsigned DEFAULT NULL,
  `D006_D058_Id` int(11) DEFAULT NULL,
  `D006_D059_Id` int(11) DEFAULT '0',
  `D006_D006_Id` int(10) unsigned DEFAULT NULL,
  `D006_Codigo_Natureza` varchar(10) DEFAULT NULL,
  `D006_Codigo_CFOP` varchar(30) DEFAULT NULL,
  `D006_Descricao` varchar(51) DEFAULT NULL,
  `D006_Flag_Venda_Compra_Outros` varchar(2) DEFAULT NULL,
  `D006_Flag_Gera_Contas` varchar(1) DEFAULT NULL,
  `D006_Flag_Estoque` varchar(1) DEFAULT NULL,
  `D006_Flag_Estado` varchar(1) DEFAULT NULL,
  `D006_Flag_Devolucao` varchar(1) DEFAULT NULL,
  `D006_Flag_Isento_ICMS` varchar(1) DEFAULT NULL,
  `D006_Flag_Isento_IPI` varchar(1) NOT NULL DEFAULT 'N',
  `D006_Flag_Retorno` varchar(1) DEFAULT NULL,
  `D006_Flag_Deb_Crd_ICMS` varchar(1) DEFAULT NULL,
  `D006_Flag_Deb_Crd_IPI` varchar(1) DEFAULT NULL,
  `D006_Flag_Entrada_Saida` varchar(1) DEFAULT NULL,
  `D006_Flag_Venda_Maquina` varchar(1) DEFAULT NULL,
  `D006_Flag_Inativo` varchar(1) DEFAULT NULL,
  `D006_Flag_MMF` varchar(1) NOT NULL DEFAULT 'N',
  `D006_Flag_Atualiza_Custo` varchar(1) NOT NULL DEFAULT 'N',
  `D006_Flag_Departamento` varchar(20) DEFAULT NULL,
  `D006_Observacao` text,
  `D006_Percentual_Reducao_ICMS` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D006_Contra_Partida` int(5) NOT NULL DEFAULT '0',
  `D006_Flag_Zona_Franca` varchar(1) DEFAULT NULL,
  `D006_Flag_Comercial_Exportadora` varchar(1) DEFAULT NULL,
  `D006_Numero_Dias_Retorno` int(3) DEFAULT '0',
  `D006_Flag_Substituicao_Tributaria` varchar(1) DEFAULT 'N',
  `D006_Flag_ST_VA` varchar(1) DEFAULT 'S',
  `D006_Descricao_Comercial` varchar(50) DEFAULT NULL,
  `D006_Flag_Isento_PIS` enum('S','N') DEFAULT 'N',
  `D006_Flag_Forma_Calculo_PIS` enum('V','L') DEFAULT 'V',
  `D006_Situacao_Tributaria_PIS` varchar(2) DEFAULT '01',
  `D006_Flag_Isento_COFINS` enum('S','N') DEFAULT 'N',
  `D006_Flag_Forma_Calculo_COFINS` enum('V','L') DEFAULT 'V',
  `D006_Situacao_Tributaria_COFINS` varchar(2) DEFAULT '01',
  `D006_Flag_Somar_IPI_Base_ICMS` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`D006_Id`),
  KEY `D006_index_Codigo_Natureza` (`D006_Codigo_Natureza`),
  KEY `D006_index_Codigo_CFOP` (`D006_Codigo_CFOP`),
  KEY `D006_FKIndex1` (`D006_D006_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D006`
--

LOCK TABLES `D006` WRITE;
/*!40000 ALTER TABLE `D006` DISABLE KEYS */;
INSERT INTO `D006` VALUES (1,0,NULL,6,0,'','5.102','VENDA DE MERCADORIA','V','N','N','D','N','S','S','N','N','N','E',NULL,'N','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA DE MERCADORIA DENTRO DO ESTADO','S','V','99','S','V','99','N'),(2,0,NULL,6,0,'','6.102','VENDA DE MERCADORIA','V','N','N','F','N','S','S','N','N','N','E',NULL,'N','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA DE MERCADORIA FORA DO ESTADO','S','V','99','S','V','99','N');
/*!40000 ALTER TABLE `D006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D007`
--

-- DROP TABLE IF EXISTS `D007`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D007` (
  `D007_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D007_C004_Id` int(11) NOT NULL DEFAULT '0',
  `D007_D062_Id` int(11) DEFAULT NULL,
  `D007_Codigo_Conta` int(10) unsigned NOT NULL DEFAULT '0',
  `D007_Nome_Banco` varchar(15) DEFAULT NULL,
  `D007_Numero_Agencia` int(4) NOT NULL DEFAULT '0',
  `D007_Digito_Verificador_Agencia` char(3) DEFAULT NULL,
  `D007_Numero_Conta` varchar(10) DEFAULT NULL,
  `D007_Digito_Verificador_Conta` char(3) DEFAULT NULL,
  `D007_Valor_Saldo` decimal(14,2) NOT NULL DEFAULT '0.00',
  `D007_Valor_Aplicado` decimal(14,2) NOT NULL DEFAULT '0.00',
  `D007_Flag_Layout` varchar(1) DEFAULT NULL,
  `D007_Flag_Com_Registro` enum('S','N') DEFAULT 'N',
  `D007_Numero_Convenio` int(6) DEFAULT NULL,
  `D007_Tipo_Carteira_Banco_Brasil` varchar(10) DEFAULT NULL,
  `D007_Valor_Saldo_Inicial` decimal(10,2) DEFAULT '0.00',
  `D007_Numero_Contrato` int(10) unsigned DEFAULT '0',
  `D007_Codigo_Baixa` varchar(30) DEFAULT NULL,
  `D007_Nosso_Numero` varchar(30) DEFAULT NULL,
  `D007_Aceite` enum('S','N') DEFAULT 'N',
  `D007_Especie` varchar(30) DEFAULT NULL,
  `D007_Especie_Documento` varchar(30) DEFAULT NULL,
  `D007_Uso_Banco` varchar(30) DEFAULT NULL,
  `D007_Percentual_Juros_Dia` decimal(10,2) DEFAULT '0.00',
  `D007_Percentual_Multa` decimal(10,2) DEFAULT '0.00',
  `D007_Instrucoes_Boleto` text,
  `D007_Codigo_Cedente` int(10) DEFAULT '0',
  `D007_Numero_Range` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`D007_Id`),
  KEY `D007_Index_Codigo_Banco` (`D007_Codigo_Conta`),
  KEY `D007_Index_C004_Id` (`D007_C004_Id`),
  KEY `D007_D062_Id` (`D007_D062_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D007`
--

LOCK TABLES `D007` WRITE;
/*!40000 ALTER TABLE `D007` DISABLE KEYS */;
/*!40000 ALTER TABLE `D007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D008`
--

-- DROP TABLE IF EXISTS `D008`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D008` (
  `D008_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D008_Comprimento` decimal(5,2) DEFAULT '0.00',
  `D008_Altura` decimal(5,2) DEFAULT '0.00',
  `D008_Espaco_Entre_Colunas` decimal(5,2) DEFAULT '0.00',
  `D008_Numero_Colunas` int(1) DEFAULT '0',
  `D008_Etiquetas_Coluna` int(2) DEFAULT '0',
  PRIMARY KEY (`D008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D008`
--

LOCK TABLES `D008` WRITE;
/*!40000 ALTER TABLE `D008` DISABLE KEYS */;
/*!40000 ALTER TABLE `D008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D009`
--

-- DROP TABLE IF EXISTS `D009`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D009` (
  `D009_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D009_D024_Id` int(10) unsigned DEFAULT NULL,
  `D009_D004_Id` int(10) unsigned DEFAULT NULL,
  `D009_D001_Id` int(10) unsigned DEFAULT NULL,
  `D009_D049_Id` int(10) unsigned DEFAULT NULL,
  `D009_C004_Id` int(11) NOT NULL DEFAULT '0',
  `D009_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D009_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `D009_Descricao_Produto` varchar(60) DEFAULT NULL,
  `D009_Valor_Custo_Medio_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `D009_Quantidade_Estoque` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D009_Quantidade_Estoque_Real` decimal(10,2) DEFAULT '0.00',
  `D009_Quantidade_Maxima_Estoque` decimal(10,2) DEFAULT NULL,
  `D009_Quantidade_Comprar` decimal(10,2) DEFAULT NULL,
  `D009_Quantidade_Pedido` int(10) DEFAULT '0',
  `D009_Frequencia_Venda` int(5) NOT NULL DEFAULT '0',
  `D009_Quantidade_Minima_Estoque` decimal(10,2) DEFAULT NULL,
  `D009_Quantidade_Seguranca_Estoque` int(10) NOT NULL DEFAULT '0',
  `D009_Data_Ultimo_Movimento` date NOT NULL DEFAULT '0000-00-00',
  `D009_Peso_Unitario_Kg` decimal(10,3) NOT NULL DEFAULT '0.000',
  `D009_Valor_Custo_Unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D009_Data_Cadastro` date NOT NULL DEFAULT '0000-00-00',
  `D009_Aplicacao` varchar(60) DEFAULT NULL,
  `D009_Flag_Ativo` varchar(1) DEFAULT NULL,
  `D009_Flag_Nacional_Importado` varchar(1) DEFAULT NULL,
  `D009_Ordem_Frequencia` int(2) NOT NULL DEFAULT '0',
  `D009_Flag_Mono` varchar(1) DEFAULT NULL,
  `D009_Flag_Base_ICMS_Cheia_NF` int(1) DEFAULT '1',
  `D009_Flag_Isento_ICMS_Venda` int(1) DEFAULT '2',
  `D009_Percentual_Reducao_ICMS_Venda` decimal(6,2) DEFAULT '0.00',
  `D009_Aliquota_ICMS_Venda` decimal(6,2) DEFAULT '0.00',
  `D009_Flag_Isento_ICMS` int(1) DEFAULT '2',
  `D009_Percentual_Reducao_ICMS` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D009_Aliquota_ICMS` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D009_Flag_Isento_IPI` int(1) DEFAULT '2',
  `D009_Percentual_Reducao_IPI` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D009_Aliquota_IPI` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D009_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `D009_Codigo_Descricao` int(5) DEFAULT NULL,
  `D009_Codigo_Linha` varchar(11) DEFAULT NULL,
  `D009_Codigo_Local` varchar(10) DEFAULT NULL,
  `D009_Prazo_Entrega` int(10) DEFAULT '0',
  `D009_Marca` varchar(20) DEFAULT NULL,
  `D009_Quantidade_Estoque_Parado_1_Ano` int(10) DEFAULT '0',
  `D009_Valor_Estoque_Parado_1_ano` decimal(10,2) DEFAULT '0.00',
  `D009_Quantidade_Estoque_Parado_2_Ano` int(10) DEFAULT '0',
  `D009_Valor_Estoque_Parado_2_ano` decimal(10,2) DEFAULT '0.00',
  `D009_Data_Ultima_Saida` date NOT NULL DEFAULT '0000-00-00',
  `D009_Data_Ultima_Entrada` date NOT NULL DEFAULT '0000-00-00',
  `D009_Quantidade_Estoque_2` int(5) NOT NULL DEFAULT '0',
  `D009_Codigo_Local_2` varchar(10) DEFAULT NULL,
  `D009_Flag_Comprar` varchar(1) DEFAULT NULL,
  `D009_Meses_Compra` int(3) DEFAULT NULL,
  `D009_Data_Ultima_Venda` date NOT NULL DEFAULT '0000-00-00',
  `D009_C007_Id_Comprar` int(11) DEFAULT NULL,
  `D009_Data_Comprar` datetime DEFAULT NULL,
  `D009_Quantidade_Estoque_Importado` decimal(10,3) DEFAULT NULL,
  `D009_Estoque_Inicial` decimal(10,2) DEFAULT NULL,
  `D009_Valor_Custo_Importado` decimal(10,2) DEFAULT NULL,
  `D009_Quantidade_Comprar_Calculada` decimal(10,2) DEFAULT NULL,
  `D009_Valor_Custo_Compra` decimal(12,5) DEFAULT NULL,
  `D009_Flag_Travar_Custo` varchar(1) DEFAULT NULL,
  `D009_Dados_Adicionais` text,
  `D009_Valor_Melhor_Custo` decimal(12,5) DEFAULT NULL,
  PRIMARY KEY (`D009_Id`),
  UNIQUE KEY `UK` (`D009_C004_Id`,`D009_D049_Id`),
  KEY `D009_Index_Id` (`D009_Id`),
  KEY `D009_Index_C004_Id` (`D009_C004_Id`),
  KEY `D009_Index_Data_Ultimo_Movimento` (`D009_Data_Ultimo_Movimento`),
  KEY `D009_Index_Locacao_Id` (`D009_D004_Id`),
  KEY `D009_Index_Codigo_Produto_Id` (`D009_Codigo_Produto`),
  KEY `D009_Index_Codigo_Fornecedor` (`D009_Codigo_Fornecedor`),
  KEY `D009_Index_Produtos` (`D009_D001_Id`),
  KEY `D009_Index_Produto_Fornecedor` (`D009_D049_Id`),
  KEY `D009_Index_Fornecedores` (`D009_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D009`
--

LOCK TABLES `D009` WRITE;
/*!40000 ALTER TABLE `D009` DISABLE KEYS */;
/*!40000 ALTER TABLE `D009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D010`
--

-- DROP TABLE IF EXISTS `D010`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D010` (
  `D010_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D010_D021_Id` int(10) unsigned DEFAULT NULL,
  `D010_Codigo_Contato` int(6) DEFAULT NULL,
  `D010_Cpf` varchar(14) DEFAULT NULL,
  `D010_Nome_Contato` varchar(40) DEFAULT NULL,
  `D010_Funcao` varchar(40) DEFAULT NULL,
  `D010_Endereco` varchar(40) DEFAULT NULL,
  `D010_Data_Nascimento` date DEFAULT NULL,
  `D010_Telefone_1` varchar(30) DEFAULT NULL,
  `D010_Telefone_2` varchar(30) DEFAULT NULL,
  `D010_E_Mail` varchar(60) DEFAULT NULL,
  `D010_Site` varchar(60) DEFAULT NULL,
  `D010_Celular` varchar(30) DEFAULT NULL,
  `D010_Cidade` varchar(25) DEFAULT NULL,
  `D010_Codigo_Empresa` varchar(6) DEFAULT NULL,
  `D010_Cnpj` varchar(18) DEFAULT NULL,
  `D010_Ramal` varchar(10) DEFAULT NULL,
  `D010_Fax` varchar(30) DEFAULT NULL,
  `D010_Estado` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`D010_Id`),
  KEY `D010_Index_Representante` (`D010_D021_Id`),
  KEY `D010_Index_Codigo_Contato` (`D010_Codigo_Contato`),
  KEY `D010_Index_Telefone_1` (`D010_Telefone_1`),
  KEY `D010_Index_Telefone_2` (`D010_Telefone_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D010`
--

LOCK TABLES `D010` WRITE;
/*!40000 ALTER TABLE `D010` DISABLE KEYS */;
/*!40000 ALTER TABLE `D010` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D011`
--

-- DROP TABLE IF EXISTS `D011`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D011` (
  `D011_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D011_D022_Id` int(10) unsigned DEFAULT NULL,
  `D011_Codigo_Contato` int(6) DEFAULT NULL,
  `D011_Cpf` varchar(14) DEFAULT NULL,
  `D011_Nome_Contato` varchar(40) DEFAULT NULL,
  `D011_Funcao` varchar(40) DEFAULT NULL,
  `D011_Endereco` varchar(40) DEFAULT NULL,
  `D011_Data_Nascimento` date DEFAULT NULL,
  `D011_Telefone_1` varchar(30) DEFAULT NULL,
  `D011_Telefone_2` varchar(30) DEFAULT NULL,
  `D011_E_Mail` varchar(60) DEFAULT NULL,
  `D011_Site` varchar(60) DEFAULT NULL,
  `D011_Celular` varchar(30) DEFAULT NULL,
  `D011_Cidade` varchar(25) DEFAULT NULL,
  `D011_Codigo_Empresa` varchar(6) DEFAULT NULL,
  `D011_Cnpj` varchar(18) DEFAULT NULL,
  `D011_Ramal` varchar(10) DEFAULT NULL,
  `D011_Fax` varchar(30) DEFAULT NULL,
  `D011_Estado` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`D011_Id`),
  KEY `D011_Index_Transportadora` (`D011_D022_Id`),
  KEY `D011_Index_Codigo_Contato` (`D011_Codigo_Contato`),
  KEY `D011_Index_Telefone_1` (`D011_Telefone_1`),
  KEY `D011_Index_Telefone_2` (`D011_Telefone_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D011`
--

LOCK TABLES `D011` WRITE;
/*!40000 ALTER TABLE `D011` DISABLE KEYS */;
/*!40000 ALTER TABLE `D011` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D012`
--

-- DROP TABLE IF EXISTS `D012`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D012` (
  `D012_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D012_D023_Id` int(10) unsigned DEFAULT NULL,
  `D012_Codigo_Contato` int(6) DEFAULT NULL,
  `D012_Cpf` varchar(14) DEFAULT NULL,
  `D012_Nome_Contato` varchar(40) DEFAULT NULL,
  `D012_Funcao` varchar(40) DEFAULT NULL,
  `D012_Endereco` varchar(40) DEFAULT NULL,
  `D012_Data_Nascimento` date DEFAULT NULL,
  `D012_Telefone_1` varchar(30) DEFAULT NULL,
  `D012_Telefone_2` varchar(30) DEFAULT NULL,
  `D012_E_Mail` varchar(60) DEFAULT NULL,
  `D012_Site` varchar(60) DEFAULT NULL,
  `D012_Celular` varchar(30) DEFAULT NULL,
  `D012_Cidade` varchar(25) DEFAULT NULL,
  `D012_Codigo_Empresa` varchar(6) DEFAULT NULL,
  `D012_Cnpj` varchar(18) DEFAULT NULL,
  `D012_Ramal` varchar(10) DEFAULT NULL,
  `D012_Fax` varchar(30) DEFAULT NULL,
  `D012_Estado` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`D012_Id`),
  KEY `D011_Index_Codigo_Contato` (`D012_Codigo_Contato`),
  KEY `D011_Index_Telefone_1` (`D012_Telefone_1`),
  KEY `D011_Index_Telefone_2` (`D012_Telefone_2`),
  KEY `D012_Index_Fornecedor` (`D012_D023_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D012`
--

LOCK TABLES `D012` WRITE;
/*!40000 ALTER TABLE `D012` DISABLE KEYS */;
/*!40000 ALTER TABLE `D012` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D013`
--

-- DROP TABLE IF EXISTS `D013`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D013` (
  `D013_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D013_D024_Id` int(10) unsigned DEFAULT NULL,
  `D013_Codigo_Contato` int(6) DEFAULT NULL,
  `D013_Cpf` varchar(14) DEFAULT NULL,
  `D013_Nome_Contato` varchar(40) DEFAULT NULL,
  `D013_Funcao` varchar(40) DEFAULT NULL,
  `D013_Endereco` varchar(40) DEFAULT NULL,
  `D013_Data_Nascimento` date DEFAULT NULL,
  `D013_Telefone_1` varchar(30) DEFAULT NULL,
  `D013_Telefone_2` varchar(30) DEFAULT NULL,
  `D013_E_Mail` varchar(60) DEFAULT NULL,
  `D013_Site` varchar(60) DEFAULT NULL,
  `D013_Celular` varchar(30) DEFAULT NULL,
  `D013_Cidade` varchar(25) DEFAULT NULL,
  `D013_Codigo_Empresa` varchar(6) DEFAULT NULL,
  `D013_Cnpj` varchar(18) DEFAULT NULL,
  `D013_Ramal` varchar(10) DEFAULT NULL,
  `D013_Fax` varchar(30) DEFAULT NULL,
  `D013_Estado` varchar(10) DEFAULT NULL,
  `D013_Flag_Departamento` int(11) DEFAULT '0',
  PRIMARY KEY (`D013_Id`),
  KEY `D011_Index_Codigo_Contato` (`D013_Codigo_Contato`),
  KEY `D011_Index_Telefone_1` (`D013_Telefone_1`),
  KEY `D011_Index_Telefone_2` (`D013_Telefone_2`),
  KEY `D012_Index_Clientes` (`D013_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D013`
--

LOCK TABLES `D013` WRITE;
/*!40000 ALTER TABLE `D013` DISABLE KEYS */;
/*!40000 ALTER TABLE `D013` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D014`
--

-- DROP TABLE IF EXISTS `D014`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D014` (
  `D014_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D014_D032_Id` int(10) unsigned DEFAULT NULL,
  `D014_Conta` int(2) DEFAULT NULL,
  `D014_SubConta` varchar(30) DEFAULT NULL,
  `D014_Flag_Reserva` varchar(1) DEFAULT NULL,
  `D014_Flag_Plano` varchar(4) DEFAULT NULL,
  `D014_Flag_Tipo` varchar(2) DEFAULT NULL,
  `D014_Observacao` text,
  `D014_Data_Inicio` date DEFAULT NULL,
  `D014_Data_Fim` date DEFAULT NULL,
  `D014_Grupo` varchar(15) DEFAULT NULL,
  `D014_Codigo` int(10) NOT NULL DEFAULT '0',
  `D014_Dia_Vencimento` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`D014_Id`),
  KEY `D014_Index_Id` (`D014_Id`),
  KEY `D014_Index_Conta` (`D014_Conta`),
  KEY `D014_Index_SubConta` (`D014_SubConta`),
  KEY `D014_Index_Codigo` (`D014_Codigo`),
  KEY `D014_Index_Tipos_Plano_Contas` (`D014_D032_Id`),
  KEY `D014_Index_Grupo` (`D014_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D014`
--

LOCK TABLES `D014` WRITE;
/*!40000 ALTER TABLE `D014` DISABLE KEYS */;
/*!40000 ALTER TABLE `D014` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D015`
--

-- DROP TABLE IF EXISTS `D015`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D015` (
  `D015_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D015_C008_Id` int(10) unsigned DEFAULT NULL,
  `D015_Codigo` varchar(4) DEFAULT NULL,
  `D015_Nome_Grupo` varchar(30) DEFAULT NULL,
  `D015_Codigo_Padrao` varchar(10) DEFAULT '',
  `D015_Descricao_Padrao` varchar(30) DEFAULT '',
  PRIMARY KEY (`D015_Id`),
  KEY `D015_Index_Codigo` (`D015_Codigo`),
  KEY `D015_Index_Tipos` (`D015_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D015`
--

LOCK TABLES `D015` WRITE;
/*!40000 ALTER TABLE `D015` DISABLE KEYS */;
/*!40000 ALTER TABLE `D015` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D016`
--

-- DROP TABLE IF EXISTS `D016`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D016` (
  `D016_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D016_Data` date NOT NULL,
  `D016_Valor_Engepecas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `D016_Valor_Oficial_Dolar` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `D016_Valor_Oficial_Euro` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`D016_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D016`
--

LOCK TABLES `D016` WRITE;
/*!40000 ALTER TABLE `D016` DISABLE KEYS */;
/*!40000 ALTER TABLE `D016` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D017`
--

-- DROP TABLE IF EXISTS `D017`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D017` (
  `D017_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D017_D001_Id` int(10) unsigned DEFAULT NULL,
  `D017_C007_Id` int(10) unsigned DEFAULT NULL,
  `D017_C008_Id` int(10) unsigned DEFAULT NULL,
  `D017_Codigo_Novo` varchar(18) DEFAULT NULL,
  `D017_Codigo_Antigo` varchar(18) DEFAULT NULL,
  `D017_Data_Inclusao` date DEFAULT NULL,
  `D017_Flag_Mudanca` varchar(1) DEFAULT NULL,
  `D017_Descricao` varchar(30) NOT NULL,
  PRIMARY KEY (`D017_Id`),
  KEY `D017_Index_D001_Id` (`D017_D001_Id`),
  KEY `D017_Index_C007_Id` (`D017_C007_Id`),
  KEY `D017_Index_C008_Id` (`D017_C008_Id`),
  KEY `D017_Index_Codigo_Novo` (`D017_Codigo_Novo`),
  KEY `D017_Index_Codigo_Antigo` (`D017_Codigo_Antigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D017`
--

LOCK TABLES `D017` WRITE;
/*!40000 ALTER TABLE `D017` DISABLE KEYS */;
/*!40000 ALTER TABLE `D017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D018`
--

-- DROP TABLE IF EXISTS `D018`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D018` (
  `D018_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D018_Codigo_IBGE` int(2) DEFAULT NULL,
  `D018_UF` varchar(2) NOT NULL,
  `D018_Percentual_Reducao_ICMS_1` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Aliquota_ICMS_1` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Percentual_Reducao_ICMS_2` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Aliquota_ICMS_2` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Percentual_Reducao_ICMS_Revenda` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Aliquota_ICMS_Revenda` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Percentual_Reducao_ICMS_Consumidor` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Aliquota_ICMS_Consumidor` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D018_Pais` varchar(2) NOT NULL,
  `D018_Maquinas` int(10) NOT NULL DEFAULT '0',
  `D018_Clientes` int(10) NOT NULL DEFAULT '0',
  `D018_Atendidos` int(10) NOT NULL DEFAULT '0',
  `D018_Cidades` int(10) NOT NULL DEFAULT '0',
  `D018_Motores` int(10) NOT NULL DEFAULT '0',
  `D018_Com_Maquina` int(10) NOT NULL DEFAULT '0',
  `D018_Com_Compra` int(10) NOT NULL DEFAULT '0',
  `D018_Estado` varchar(40) NOT NULL,
  `D018_Observacao` text NOT NULL,
  PRIMARY KEY (`D018_Id`),
  KEY `D018_IDX_UF` (`D018_UF`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D018`
--

LOCK TABLES `D018` WRITE;
/*!40000 ALTER TABLE `D018` DISABLE KEYS */;
/*!40000 ALTER TABLE `D018` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D019`
--

-- DROP TABLE IF EXISTS `D019`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D019` (
  `D019_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D019_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D019_IPV_Minimo` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D019_IPV_Maximo` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D019_Percentual_Desconto_ACP` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D019_Tipo_1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D019_Tipo_2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D019_Fixas` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`D019_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D019`
--

LOCK TABLES `D019` WRITE;
/*!40000 ALTER TABLE `D019` DISABLE KEYS */;
/*!40000 ALTER TABLE `D019` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D020`
--

-- DROP TABLE IF EXISTS `D020`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D020` (
  `D020_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D020_D084_Id` int(10) unsigned DEFAULT NULL,
  `D020_D018_Id` int(10) unsigned NOT NULL,
  `D020_Codigo_Cidade` varchar(5) NOT NULL,
  `D020_Codigo_IBGE` int(7) DEFAULT NULL,
  `D020_UF` varchar(2) NOT NULL,
  `D020_Nome_Cidade` varchar(40) NOT NULL,
  `D020_Flag_IPI` enum('S','N') DEFAULT 'N',
  `D020_Flag_ICMS` enum('S','N') DEFAULT 'N',
  `D020_Flag_ISS` enum('S','N') DEFAULT 'N',
  `D020_Flag_Suframa` enum('S','N') DEFAULT 'N',
  `D020_Observacao` text NOT NULL,
  `D020_Flag_Regiao` varchar(4) NOT NULL,
  `D020_Nome_Estado` varchar(40) NOT NULL,
  PRIMARY KEY (`D020_Id`),
  KEY `D020_IDX_ESTADO` (`D020_D018_Id`),
  KEY `D020_IDX_CODIGO_CIDADE` (`D020_Codigo_Cidade`),
  KEY `D020_IDX_NOME_CIDADE` (`D020_Nome_Cidade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D020`
--

LOCK TABLES `D020` WRITE;
/*!40000 ALTER TABLE `D020` DISABLE KEYS */;
/*!40000 ALTER TABLE `D020` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D021`
--

-- DROP TABLE IF EXISTS `D021`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D021` (
  `D021_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D021_D062_Id` int(11) DEFAULT NULL,
  `D021_D018_Id` int(11) NOT NULL DEFAULT '0',
  `D021_D020_Id` int(11) NOT NULL DEFAULT '0',
  `D021_D030_Id` int(11) NOT NULL DEFAULT '0',
  `D021_Codigo` varchar(6) DEFAULT NULL,
  `D021_Nome_Empresa` varchar(50) DEFAULT NULL,
  `D021_Classe` varchar(1) DEFAULT NULL,
  `D021_Endereco` varchar(40) DEFAULT NULL,
  `D021_Complemento` varchar(15) DEFAULT NULL,
  `D021_Cep` varchar(9) DEFAULT NULL,
  `D021_Bairro` varchar(25) DEFAULT NULL,
  `D021_Cidade` varchar(30) DEFAULT NULL,
  `D021_Estado` varchar(2) DEFAULT NULL,
  `D021_Telefone_1` varchar(20) DEFAULT NULL,
  `D021_Telefone_2` varchar(20) DEFAULT NULL,
  `D021_Telex` varchar(20) DEFAULT NULL,
  `D021_Fax` varchar(20) DEFAULT NULL,
  `D021_Cnpj` varchar(20) DEFAULT NULL,
  `D021_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `D021_Flag_Tipo_Empresa` varchar(1) DEFAULT NULL,
  `D021_Flag_Simples` varchar(1) DEFAULT NULL,
  `D021_Pais` varchar(2) DEFAULT NULL,
  `D021_Cpf` varchar(20) DEFAULT NULL,
  `D021_Cobranca_Rua` varchar(40) DEFAULT NULL,
  `D021_Cobranca_Complemento` varchar(15) DEFAULT NULL,
  `D021_Cobranca_Bairro` varchar(25) DEFAULT NULL,
  `D021_Cobranca_Cidade` varchar(30) DEFAULT NULL,
  `D021_Cobranca_Uf` varchar(2) DEFAULT NULL,
  `D021_Cobranca_Cep` varchar(9) DEFAULT NULL,
  `D021_Cobranca_Pais` varchar(2) DEFAULT NULL,
  `D021_Data_Fundacao` date DEFAULT NULL,
  `D021_Flag_Suframa` varchar(1) DEFAULT NULL,
  `D021_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `D021_Flag_Pessoa_Fisica_Juridica` varchar(1) DEFAULT NULL,
  `D021_Valor_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D021_Data_Irregular` date DEFAULT NULL,
  `D021_Data_Atraso` date DEFAULT NULL,
  `D021_Observacao_Compra` text,
  `D021_Flag_Custo_Zero` varchar(1) DEFAULT NULL,
  `D021_Flag_Mono` varchar(1) DEFAULT NULL,
  `D021_ISS` varchar(20) DEFAULT NULL,
  `D021_Praca_Pagamento` varchar(65) DEFAULT NULL,
  `D021_Aviso` varchar(65) DEFAULT NULL,
  `D021_Observacoes` text,
  `D021_Percentual_Comissao` decimal(5,2) DEFAULT '0.00',
  `D021_Agencia` varchar(10) DEFAULT NULL,
  `D021_Conta_Bancaria` varchar(20) DEFAULT NULL,
  `D021_Nome_Conta` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`D021_Id`),
  KEY `D021_index_Codigo` (`D021_Codigo`),
  KEY `D021_index_Nome_Empresa` (`D021_Nome_Empresa`),
  KEY `D021_index_Cnpj` (`D021_Cnpj`),
  KEY `D021_index_Empresas_Estado` (`D021_D018_Id`),
  KEY `D021_index_Empresas_Cidade` (`D021_D020_Id`),
  KEY `D021_index_Empresas_Pais` (`D021_D030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D021`
--

LOCK TABLES `D021` WRITE;
/*!40000 ALTER TABLE `D021` DISABLE KEYS */;
/*!40000 ALTER TABLE `D021` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D022`
--

-- DROP TABLE IF EXISTS `D022`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D022` (
  `D022_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D022_D036_Id` int(11) NOT NULL DEFAULT '0',
  `D022_D018_Id` int(11) NOT NULL DEFAULT '0',
  `D022_D020_Id` int(11) NOT NULL DEFAULT '0',
  `D022_D030_Id` int(11) NOT NULL DEFAULT '0',
  `D022_Codigo` varchar(6) DEFAULT NULL,
  `D022_Nome_Empresa` varchar(50) DEFAULT NULL,
  `D022_Classe` varchar(1) DEFAULT NULL,
  `D022_Endereco` varchar(40) DEFAULT NULL,
  `D022_Complemento` varchar(15) DEFAULT NULL,
  `D022_Cep` varchar(9) DEFAULT NULL,
  `D022_Bairro` varchar(25) DEFAULT NULL,
  `D022_Cidade` varchar(30) DEFAULT NULL,
  `D022_Estado` varchar(2) DEFAULT NULL,
  `D022_Telefone_1` varchar(20) DEFAULT NULL,
  `D022_Telefone_2` varchar(20) DEFAULT NULL,
  `D022_Telex` varchar(20) DEFAULT NULL,
  `D022_Fax` varchar(20) DEFAULT NULL,
  `D022_Cnpj` varchar(20) DEFAULT NULL,
  `D022_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `D022_Flag_Tipo_Empresa` varchar(1) DEFAULT NULL,
  `D022_Flag_Simples` varchar(1) DEFAULT NULL,
  `D022_Pais` varchar(2) DEFAULT NULL,
  `D022_Cpf` varchar(20) DEFAULT NULL,
  `D022_Cobranca_Rua` varchar(40) DEFAULT NULL,
  `D022_Cobranca_Complemento` varchar(15) DEFAULT NULL,
  `D022_Cobranca_Bairro` varchar(25) DEFAULT NULL,
  `D022_Cobranca_Cidade` varchar(30) DEFAULT NULL,
  `D022_Cobranca_Uf` varchar(2) DEFAULT NULL,
  `D022_Cobranca_Cep` varchar(9) DEFAULT NULL,
  `D022_Cobranca_Pais` varchar(2) DEFAULT NULL,
  `D022_Data_Fundacao` date DEFAULT NULL,
  `D022_Flag_Suframa` varchar(1) DEFAULT NULL,
  `D022_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `D022_Flag_Pessoa_Fisica_Juridica` varchar(1) DEFAULT NULL,
  `D022_Valor_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D022_Data_Irregular` date DEFAULT NULL,
  `D022_Data_Atraso` date DEFAULT NULL,
  `D022_Observacao_Compra` text,
  `D022_Flag_Custo_Zero` varchar(1) DEFAULT NULL,
  `D022_Flag_Mono` varchar(1) DEFAULT NULL,
  `D022_ISS` varchar(20) DEFAULT NULL,
  `D022_Praca_Pagamento` varchar(65) DEFAULT NULL,
  `D022_Aviso` varchar(65) DEFAULT NULL,
  `D022_Observacoes` text,
  `D022_Email` varchar(50) DEFAULT NULL,
  `D022_WebSite` varchar(50) DEFAULT NULL,
  `D022_Nome_Fantasia` varchar(60) DEFAULT NULL,
  `D022_Nome_Contato` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`D022_Id`),
  KEY `D022_index_Tipo_Transporte` (`D022_D036_Id`),
  KEY `D022_index_Codigo` (`D022_Codigo`),
  KEY `D022_index_Nome_Empresa` (`D022_Nome_Empresa`),
  KEY `D022_index_Cnpj` (`D022_Cnpj`),
  KEY `D022_index_Empresas_Estado` (`D022_D018_Id`),
  KEY `D022_index_Empresas_Cidade` (`D022_D020_Id`),
  KEY `D022_index_Empresas_Pais` (`D022_D030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D022`
--

LOCK TABLES `D022` WRITE;
/*!40000 ALTER TABLE `D022` DISABLE KEYS */;
/*!40000 ALTER TABLE `D022` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D023`
--

-- DROP TABLE IF EXISTS `D023`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D023` (
  `D023_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D023_Codigo` varchar(6) DEFAULT NULL,
  `D023_Nome_Empresa` varchar(50) DEFAULT NULL,
  `D023_Classe` varchar(1) DEFAULT NULL,
  `D023_Endereco` varchar(40) DEFAULT NULL,
  `D023_Complemento` varchar(15) DEFAULT NULL,
  `D023_Cep` varchar(9) DEFAULT NULL,
  `D023_Bairro` varchar(25) DEFAULT NULL,
  `D023_Cidade` varchar(30) DEFAULT NULL,
  `D023_Estado` varchar(2) DEFAULT NULL,
  `D023_Telefone_1` varchar(20) DEFAULT NULL,
  `D023_Telefone_2` varchar(20) DEFAULT NULL,
  `D023_Telex` varchar(20) DEFAULT NULL,
  `D023_Fax` varchar(20) DEFAULT NULL,
  `D023_Cnpj` varchar(20) DEFAULT NULL,
  `D023_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `D023_Flag_Tipo_Empresa` varchar(1) DEFAULT NULL,
  `D023_Flag_Simples` varchar(1) DEFAULT NULL,
  `D023_Pais` varchar(2) DEFAULT NULL,
  `D023_Cpf` varchar(20) DEFAULT NULL,
  `D023_Cobranca_Rua` varchar(40) DEFAULT NULL,
  `D023_Cobranca_Complemento` varchar(15) DEFAULT NULL,
  `D023_Cobranca_Bairro` varchar(25) DEFAULT NULL,
  `D023_Cobranca_Cidade` varchar(30) DEFAULT NULL,
  `D023_Cobranca_Uf` varchar(2) DEFAULT NULL,
  `D023_Cobranca_Cep` varchar(9) DEFAULT NULL,
  `D023_Cobranca_Pais` varchar(2) DEFAULT NULL,
  `D023_Data_Fundacao` date DEFAULT NULL,
  `D023_Flag_Suframa` varchar(1) DEFAULT NULL,
  `D023_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `D023_Flag_Pessoa_Fisica_Juridica` varchar(1) DEFAULT NULL,
  `D023_Valor_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D023_Percentual_IPV_Fornecedor_1` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D023_Percentual_IPV_Fornecedor_2` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D023_Percentual_IPV_Fornecedor_3` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D023_Percentual_IPV_Consumidor` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D023_Data_Irregular` date DEFAULT NULL,
  `D023_Data_Atraso` date DEFAULT NULL,
  `D023_Observacao_Compra` text,
  `D023_Flag_Custo_Zero` varchar(1) DEFAULT NULL,
  `D023_Flag_Mono` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`D023_Id`),
  KEY `D023_index_Codigo` (`D023_Codigo`),
  KEY `D023_index_Nome_Empresa` (`D023_Nome_Empresa`),
  KEY `D023_index_Cnpj` (`D023_Cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D023`
--

LOCK TABLES `D023` WRITE;
/*!40000 ALTER TABLE `D023` DISABLE KEYS */;
/*!40000 ALTER TABLE `D023` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D024`
--

-- DROP TABLE IF EXISTS `D024`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D024` (
  `D024_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D024_C004_Id` int(11) NOT NULL DEFAULT '0',
  `D024_D018_Id` int(11) NOT NULL DEFAULT '0',
  `D024_D020_Id` int(10) unsigned DEFAULT NULL,
  `D024_D030_Id` int(11) NOT NULL DEFAULT '0',
  `D024_D006_Id` int(11) NOT NULL DEFAULT '0',
  `D024_D021_Id` int(11) NOT NULL DEFAULT '0',
  `D024_D022_Id` int(11) DEFAULT NULL,
  `D024_D046_Id` int(11) DEFAULT NULL,
  `D024_D039_Id` int(11) DEFAULT NULL,
  `D024_D014_Id` int(11) DEFAULT NULL,
  `D024_D061_Id` varchar(50) DEFAULT NULL,
  `D024_D062_Id` int(10) unsigned DEFAULT '0',
  `D024_D024_Id_Eng_Amigo` int(10) unsigned DEFAULT NULL,
  `D024_C004_Id_Ultima_Compra` int(11) DEFAULT NULL,
  `D024_C007_Id_Eng_Amigo_Vendedor` int(5) DEFAULT '0',
  `D024_D018_Cobranca` int(11) NOT NULL DEFAULT '0',
  `D024_D020_Cobranca` int(11) NOT NULL DEFAULT '0',
  `D024_D030_Cobranca` int(11) NOT NULL DEFAULT '0',
  `D024_D018_Entrega` int(11) DEFAULT NULL,
  `D024_D020_Entrega` int(11) DEFAULT NULL,
  `D024_D030_Entrega` int(11) DEFAULT NULL,
  `D024_C007_Vendedor_Externo` int(11) NOT NULL DEFAULT '0',
  `D024_C007_Vendedor_Interno` int(11) NOT NULL DEFAULT '0',
  `D024_C007_Id` int(5) DEFAULT NULL,
  `D024_Codigo` varchar(20) DEFAULT NULL,
  `D024_Nome_Empresa` varchar(50) DEFAULT NULL,
  `D024_Nome_Fantasia` varchar(50) DEFAULT NULL,
  `D024_Classe` varchar(1) DEFAULT NULL,
  `D024_Endereco` varchar(40) DEFAULT NULL,
  `D024_Numero` varchar(15) DEFAULT NULL,
  `D024_Complemento` varchar(15) DEFAULT NULL,
  `D024_Cep` varchar(9) DEFAULT NULL,
  `D024_Bairro` varchar(25) DEFAULT NULL,
  `D024_Cidade` varchar(30) DEFAULT NULL,
  `D024_Estado` varchar(2) DEFAULT NULL,
  `D024_Nome_Contato` varchar(40) DEFAULT NULL,
  `D024_DDD_Telefone_1` varchar(2) DEFAULT NULL,
  `D024_Telefone_1` varchar(30) DEFAULT NULL,
  `D024_Ramal_Telefone_1` varchar(10) DEFAULT NULL,
  `D024_Telefone_2` varchar(30) DEFAULT NULL,
  `D024_Ramal_Telefone_2` varchar(10) DEFAULT NULL,
  `D024_Telex` varchar(20) DEFAULT NULL,
  `D024_Fax` varchar(30) DEFAULT NULL,
  `D024_Ramal_Fax` varchar(10) DEFAULT NULL,
  `D024_Celular` varchar(30) DEFAULT NULL,
  `D024_E_Mail` varchar(60) DEFAULT NULL,
  `D024_Senha_Acesso` varchar(50) DEFAULT NULL,
  `D024_Cnpj` varchar(20) DEFAULT NULL,
  `D024_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `D024_Pais` varchar(2) DEFAULT NULL,
  `D024_Cpf` varchar(20) DEFAULT NULL,
  `D024_Cobranca_Rua` varchar(40) DEFAULT NULL,
  `D024_Cobranca_Complemento` varchar(15) DEFAULT NULL,
  `D024_Cobranca_Bairro` varchar(25) DEFAULT NULL,
  `D024_Cobranca_Cidade` varchar(30) DEFAULT NULL,
  `D024_Cobranca_Uf` varchar(2) DEFAULT NULL,
  `D024_Cobranca_Cep` varchar(9) DEFAULT NULL,
  `D024_Cobranca_Pais` varchar(2) DEFAULT NULL,
  `D024_Data_Fundacao` date DEFAULT NULL,
  `D024_Flag_Tipo_Empresa` varchar(1) DEFAULT NULL,
  `D024_Flag_Simples` varchar(1) DEFAULT NULL,
  `D024_Flag_Suframa` enum('S','N','') DEFAULT '',
  `D024_Numero_Suframa` varchar(35) DEFAULT NULL,
  `D024_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `D024_Flag_Pessoa_Fisica_Juridica` varchar(1) DEFAULT NULL,
  `D024_Flag_Isento_ICMS` int(1) NOT NULL DEFAULT '2',
  `D024_Flag_Isento_ICMS_Venda` enum('S','N') DEFAULT 'N',
  `D024_Flag_Isento_PIS_COFINS` enum('S','N') DEFAULT 'N',
  `D024_Flag_Cotacao_Compra` varchar(1) NOT NULL DEFAULT 'S',
  `D024_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT NULL,
  `D024_Aliquota_ICMS` decimal(5,2) DEFAULT NULL,
  `D024_Flag_Isento_IPI` int(1) NOT NULL DEFAULT '2',
  `D024_Valor_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D024_Percentual_IPV_Fornecedor_1` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D024_Percentual_IPV_Fornecedor_2` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D024_Percentual_IPV_Fornecedor_3` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D024_Percentual_IPV_Consumidor` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `D024_Data_Irregular` date DEFAULT '0000-00-00',
  `D024_Flag_Ativo` enum('S','N') DEFAULT 'S',
  `D024_Data_Atraso` date DEFAULT NULL,
  `D024_Observacao_Compra` text,
  `D024_Flag_Custo_Zero` varchar(1) DEFAULT NULL,
  `D024_Flag_Mono` varchar(1) DEFAULT NULL,
  `D024_ISS` varchar(20) DEFAULT NULL,
  `D024_Praca_Pagamento` varchar(65) DEFAULT NULL,
  `D024_Aviso` varchar(65) DEFAULT NULL,
  `D024_Observacoes` text,
  `D024_Marca` varchar(20) DEFAULT NULL,
  `D024_Flag_Etiqueta` varchar(1) DEFAULT NULL,
  `D024_Flag_Mkt` varchar(1) DEFAULT NULL,
  `D024_Percentual_Comissao` decimal(5,2) DEFAULT '0.00',
  `D024_Observacao_Comercial` text,
  `D024_Observacao_Isento_ICMS` text,
  `D024_Observacao_Isento_IPI` text,
  `D024_Data_Ultima_Compra` date DEFAULT NULL,
  `D024_Data_Primeira_Compra` date DEFAULT NULL,
  `D024_Situacao` varchar(30) DEFAULT NULL,
  `D024_Flag_Liberado` varchar(1) DEFAULT NULL,
  `D024_Flag_Classe` varchar(1) DEFAULT NULL,
  `D024_Cadastro` varchar(16) DEFAULT NULL,
  `D024_Valor_Limite` decimal(10,2) DEFAULT NULL,
  `D024_Valor_Saldo_Limite` decimal(10,2) DEFAULT NULL,
  `D024_Flag_Empresas_Grupo` varchar(1) DEFAULT 'N',
  `D024_Flag_Permite_ACP` varchar(1) DEFAULT 'N',
  `D024_Flag_Enviar_Banco_Protestar` int(1) NOT NULL DEFAULT '1',
  `D024_Flag_Permite_Agrupamentos` int(1) NOT NULL DEFAULT '1',
  `D024_Referencia_Comercial_1` varchar(60) DEFAULT NULL,
  `D024_Referencia_Comercial_2` varchar(60) DEFAULT NULL,
  `D024_Referencia_Comercial_3` varchar(60) DEFAULT NULL,
  `D024_Referencia_Comercial_4` varchar(60) DEFAULT NULL,
  `D024_Referencia_Comercial_5` varchar(60) DEFAULT NULL,
  `D024_Referencia_Bancaria` varchar(80) DEFAULT NULL,
  `D024_Flag_ICMS_Substituicao_Tributaria_Substituto` varchar(1) DEFAULT 'N',
  `D024_Eng_Amigo_Empresa_Trabalho` varchar(50) DEFAULT NULL,
  `D024_Eng_Amigo_Profissao` varchar(50) DEFAULT NULL,
  `D024_Eng_Amigo_Observacao_Brinde` text,
  `D024_Eng_Amigo_Empresa_Ramo` enum('S','N') DEFAULT 'N',
  `D024_Eng_Amigo_Relacionado_Produtos_Comercializacao` varchar(1) DEFAULT 'N',
  `D024_Flag_Eng_Amigo` enum('S','N') DEFAULT 'N',
  `D024_Codigo_Fornecedor` varchar(15) DEFAULT NULL,
  `D024_Flag_Atacado_Varejo` varchar(1) DEFAULT NULL,
  `D024_Flag_Tipo_Origem` int(2) NOT NULL,
  `D024_Flag_Curva_Abc` enum('A','B','C','D') DEFAULT NULL,
  `D024_Flag_Frequencia` int(6) DEFAULT '0',
  `D024_Data_Atualizacao` datetime DEFAULT '0000-00-00 00:00:00',
  `D024_Meses_Inativo` decimal(6,2) DEFAULT '0.00',
  `D024_Flag_Lista_Preco` varchar(1) DEFAULT ' ',
  `D024_Data_Cadastro` datetime DEFAULT '0000-00-00 00:00:00',
  `D024_Flag_Pre_Cadastro` enum('S','N') DEFAULT 'N',
  `D024_Flag_Cliente` varchar(1) DEFAULT ' ',
  `D024_Flag_Fornecedor` varchar(1) DEFAULT ' ',
  `D024_Horario_Entrega` varchar(30) DEFAULT ' ',
  `D024_Dias_Carencia_Pagamento` int(2) DEFAULT '0',
  `D024_Dia_Semana_Pagamento` int(1) DEFAULT '0',
  `D024_Dia_Mes_Pagamento` varchar(30) DEFAULT ' ',
  `D024_Web_Site` varchar(50) DEFAULT ' ',
  `D024_Condicoes_Pagamento` varchar(50) DEFAULT ' ',
  `D024_Entrega_Rua` varchar(60) DEFAULT NULL,
  `D024_Entrega_Complemento` varchar(30) DEFAULT NULL,
  `D024_Entrega_Bairro` varchar(25) DEFAULT NULL,
  `D024_Entrega_Cep` varchar(9) DEFAULT NULL,
  `D024_Entrega_Telefone` varchar(20) DEFAULT NULL,
  `D024_Cobranca_Telefone` varchar(20) DEFAULT NULL,
  `D024_Cobranca_Fax` varchar(20) DEFAULT NULL,
  `D024_Entrega_Fax` varchar(20) DEFAULT NULL,
  `D024_Cobranca_Email` varchar(50) DEFAULT NULL,
  `D024_Entrega_Email` varchar(50) DEFAULT NULL,
  `D024_Tipo_Pagamento` varchar(3) DEFAULT ' ',
  `D024_Percentual_Desconto_Tabela` decimal(10,4) DEFAULT '0.0000',
  `D024_Flag_Restricao` enum('S','N') DEFAULT 'N',
  `D024_Observacao_Financeiro` text,
  `D024_Flag_Comercial` varchar(1) DEFAULT '',
  `D024_Flag_Inativo_Comercial` varchar(1) DEFAULT 'N',
  `D024_Flag_Tempo` varchar(2) DEFAULT '  ',
  PRIMARY KEY (`D024_Id`),
  KEY `D024_index_Id` (`D024_Id`),
  KEY `D024_index_Codigo` (`D024_Codigo`),
  KEY `D024_index_Nome_Empresa` (`D024_Nome_Empresa`),
  KEY `D024_index_Cnpj` (`D024_Cnpj`),
  KEY `D024_index_Vendedor_Externo` (`D024_C007_Vendedor_Externo`),
  KEY `D024_index_Vendedor_Interno` (`D024_C007_Vendedor_Interno`),
  KEY `D024_index_Grupo` (`D024_Flag_Mono`),
  KEY `D024_index_Estado` (`D024_D018_Id`),
  KEY `D024_index_Cidade` (`D024_D020_Id`),
  KEY `D024_index_Pais` (`D024_D030_Id`),
  KEY `D024_index_Estado_Cobranca` (`D024_D018_Cobranca`),
  KEY `D024_index_Cidade_Cobranca` (`D024_D020_Cobranca`),
  KEY `D024_index_Pais_Cobranca` (`D024_D030_Cobranca`),
  KEY `D024_index_Natureza` (`D024_D006_Id`),
  KEY `D024_index_Representante` (`D024_D021_Id`),
  KEY `D024_index_Empresas` (`D024_C004_Id`),
  KEY `D024_index_D062_Id` (`D024_D062_Id`),
  KEY `D024_index_C004_Id_Ultima_Compra` (`D024_C004_Id_Ultima_Compra`),
  KEY `IDX_D024_Flag_Curva_Abc` (`D024_Flag_Curva_Abc`),
  KEY `IDX_D024_Flag_Comercial` (`D024_Flag_Comercial`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D024`
--

LOCK TABLES `D024` WRITE;
/*!40000 ALTER TABLE `D024` DISABLE KEYS */;
/*!40000 ALTER TABLE `D024` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D025`
--

-- DROP TABLE IF EXISTS `D025`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D025` (
  `D025_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D025_D018_Id` int(10) unsigned NOT NULL,
  `D025_Codigo_Despesa` varchar(5) NOT NULL,
  `D025_Descricao_Despesa` varchar(40) NOT NULL,
  `D025_Formula` text NOT NULL,
  `D025_Flag_Tipo_Frete` varchar(1) NOT NULL,
  `D025_Flag_IPI` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Flag_Taxa_Importacao` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Flag_ICMS` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Flag_Peso` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Flag_Dolar` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Flag_Regime_Tributacao_Simplificado` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Flag_Aduaneira` varchar(1) NOT NULL DEFAULT 'N',
  `D025_Valor_Faixa_1` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D025_Valor_Faixa_2` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D025_Valor_Faixa_3` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D025_Valor_KG_Faixa_1` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D025_Valor_KG_Faixa_2` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D025_Valor_KG_Faixa_3` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `D025_Estado` varchar(2) NOT NULL,
  PRIMARY KEY (`D025_Id`),
  KEY `D025_IDX_ESTADO` (`D025_D018_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D025`
--

LOCK TABLES `D025` WRITE;
/*!40000 ALTER TABLE `D025` DISABLE KEYS */;
/*!40000 ALTER TABLE `D025` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D026`
--

-- DROP TABLE IF EXISTS `D026`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D026` (
  `D026_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D026_Data` date NOT NULL,
  `D026_Valor_Engepecas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `D026_Valor_Oficial` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`D026_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D026`
--

LOCK TABLES `D026` WRITE;
/*!40000 ALTER TABLE `D026` DISABLE KEYS */;
/*!40000 ALTER TABLE `D026` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D027`
--

-- DROP TABLE IF EXISTS `D027`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D027` (
  `D027_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D027_D071_Id` int(11) DEFAULT NULL,
  `D027_Portador` varchar(20) NOT NULL,
  `D027_Flag_Prazo` varchar(1) DEFAULT NULL,
  `D027_Flag_Boleto` varchar(1) DEFAULT NULL,
  `D027_Flag_Cupom` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`D027_Id`),
  KEY `D027_Index_Portador` (`D027_Portador`),
  KEY `D027_Index_Id` (`D027_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D027`
--

LOCK TABLES `D027` WRITE;
/*!40000 ALTER TABLE `D027` DISABLE KEYS */;
/*!40000 ALTER TABLE `D027` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D028`
--

-- DROP TABLE IF EXISTS `D028`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D028` (
  `D028_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D028_Codigo_Grupo` int(2) NOT NULL DEFAULT '0',
  `D028_Descricao_Grupo` varchar(30) NOT NULL,
  `D028_Percentual_Taxa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D028_Tipo` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`D028_Id`),
  KEY `D028_IDX_CODIGO_GRUPO` (`D028_Codigo_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D028`
--

LOCK TABLES `D028` WRITE;
/*!40000 ALTER TABLE `D028` DISABLE KEYS */;
/*!40000 ALTER TABLE `D028` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D029`
--

-- DROP TABLE IF EXISTS `D029`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D029` (
  `D029_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D029_D014_Id` int(10) unsigned NOT NULL,
  `D029_D028_Id` int(10) unsigned NOT NULL,
  `D029_Descricao` varchar(30) NOT NULL,
  `D029_Percentual_Taxa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D029_Aplicacao` int(1) NOT NULL DEFAULT '0',
  `D029_Sobre` int(2) NOT NULL DEFAULT '0',
  `D029_Conta` int(2) NOT NULL DEFAULT '0',
  `D029_SubConta` varchar(30) NOT NULL,
  `D029_Taxa_Real` decimal(10,2) NOT NULL DEFAULT '0.00',
  `D029_Codigo_Grupo` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`D029_Id`),
  KEY `D029_IDX_GRUPOS` (`D029_D028_Id`),
  KEY `D029_IDX_PLANOS` (`D029_D014_Id`),
  KEY `D029_IDX_CODIGO_GRUPO` (`D029_Codigo_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D029`
--

LOCK TABLES `D029` WRITE;
/*!40000 ALTER TABLE `D029` DISABLE KEYS */;
/*!40000 ALTER TABLE `D029` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D030`
--

-- DROP TABLE IF EXISTS `D030`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D030` (
  `D030_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D030_Sigla_Pais` varchar(2) DEFAULT NULL,
  `D030_Nome_Pais` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`D030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D030`
--

LOCK TABLES `D030` WRITE;
/*!40000 ALTER TABLE `D030` DISABLE KEYS */;
/*!40000 ALTER TABLE `D030` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D031`
--

-- DROP TABLE IF EXISTS `D031`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D031` (
  `D031_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D031_Portador` varchar(20) NOT NULL,
  PRIMARY KEY (`D031_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D031`
--

LOCK TABLES `D031` WRITE;
/*!40000 ALTER TABLE `D031` DISABLE KEYS */;
/*!40000 ALTER TABLE `D031` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D032`
--

-- DROP TABLE IF EXISTS `D032`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D032` (
  `D032_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D032_Tipo` varchar(2) NOT NULL,
  `D032_Descricao` varchar(20) NOT NULL,
  PRIMARY KEY (`D032_Id`),
  KEY `D032_Indice_Tipos` (`D032_Tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D032`
--

LOCK TABLES `D032` WRITE;
/*!40000 ALTER TABLE `D032` DISABLE KEYS */;
/*!40000 ALTER TABLE `D032` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D033`
--

-- DROP TABLE IF EXISTS `D033`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D033` (
  `D033_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D033_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `D033_Nome_Linha` varchar(11) DEFAULT NULL,
  `D033_Flag_Tipo` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`D033_Id`),
  KEY `D033_FKIndex1` (`D033_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D033`
--

LOCK TABLES `D033` WRITE;
/*!40000 ALTER TABLE `D033` DISABLE KEYS */;
/*!40000 ALTER TABLE `D033` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D034`
--

-- DROP TABLE IF EXISTS `D034`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D034` (
  `D034_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D034_Codigo` varchar(5) DEFAULT NULL,
  `D034_Descricao_Produto` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`D034_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D034`
--

LOCK TABLES `D034` WRITE;
/*!40000 ALTER TABLE `D034` DISABLE KEYS */;
/*!40000 ALTER TABLE `D034` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D035`
--

-- DROP TABLE IF EXISTS `D035`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D035` (
  `D035_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D035_D024_Id` int(10) unsigned DEFAULT NULL,
  `D035_D033_Id` int(10) unsigned DEFAULT NULL,
  `D035_D034_Id` int(10) unsigned DEFAULT NULL,
  `D035_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `D035_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D035_Descricao_Produto` varchar(60) DEFAULT NULL,
  `D035_Data_Ultimo_Movimento` date DEFAULT NULL,
  `D035_Peso_Unitario_Kg` decimal(10,3) DEFAULT NULL,
  `D035_Data_Cadastro` date DEFAULT NULL,
  `D035_Flag_Ativo` int(1) DEFAULT NULL,
  `D035_Codigo_Descricao` int(5) DEFAULT NULL,
  `D035_Codigo_Linha` varchar(11) DEFAULT NULL,
  `D035_Valor_Custo_Unitario` decimal(10,2) DEFAULT NULL,
  `D035_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `D035_Quantidade_Estoque` int(5) DEFAULT NULL,
  `D035_Flag_Nacional_Importado` varchar(1) DEFAULT NULL,
  `D035_Flag_Unidade_Estoque` varchar(1) DEFAULT NULL,
  `D035_Flag_Unidade_Conversao` varchar(1) DEFAULT NULL,
  `D035_Ordem_Frequencia` int(2) DEFAULT NULL,
  `D035_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`D035_Id`),
  KEY `D035_FKIndex0` (`D035_D024_Id`),
  KEY `D035_FKIndex1` (`D035_D034_Id`),
  KEY `D035_FKIndex2` (`D035_D033_Id`),
  KEY `D035_FKIndex3` (`D035_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D035`
--

LOCK TABLES `D035` WRITE;
/*!40000 ALTER TABLE `D035` DISABLE KEYS */;
/*!40000 ALTER TABLE `D035` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D036`
--

-- DROP TABLE IF EXISTS `D036`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D036` (
  `D036_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D036_Tipo_Transporte` varchar(20) NOT NULL,
  PRIMARY KEY (`D036_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D036`
--

LOCK TABLES `D036` WRITE;
/*!40000 ALTER TABLE `D036` DISABLE KEYS */;
/*!40000 ALTER TABLE `D036` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D037`
--

-- DROP TABLE IF EXISTS `D037`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D037` (
  `D037_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D037_Unidade` varchar(5) DEFAULT NULL,
  `D037_Descricao_Unidade` varchar(20) DEFAULT NULL,
  `D037_Quantidade` decimal(10,3) DEFAULT '1.000',
  PRIMARY KEY (`D037_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D037`
--

LOCK TABLES `D037` WRITE;
/*!40000 ALTER TABLE `D037` DISABLE KEYS */;
INSERT INTO `D037` VALUES (1,'UN','UNIDADE','1.000');
/*!40000 ALTER TABLE `D037` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D038`
--

-- DROP TABLE IF EXISTS `D038`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D038` (
  `D038_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D038_D024_Id` int(10) unsigned DEFAULT NULL,
  `D038_D036_Id` int(10) unsigned DEFAULT NULL,
  `D038_D022_Id` int(10) unsigned DEFAULT NULL,
  `D038_D044_Id` int(10) unsigned DEFAULT NULL,
  `D038_Valor_Frete` decimal(10,2) DEFAULT '0.00',
  `D038_Prazo_Entrega` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_1` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_2` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_3` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_4` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_5` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_6` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_7` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_8` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_9` int(10) DEFAULT '0',
  `D038_Prazo_Pagamento_10` int(10) DEFAULT '0',
  `D038_Observacao` text,
  PRIMARY KEY (`D038_Id`),
  KEY `D038_FKIndex1` (`D038_D024_Id`),
  KEY `D038_FKIndex2` (`D038_D022_Id`),
  KEY `D038_FKIndex3` (`D038_D036_Id`),
  KEY `D038_index_Moeda` (`D038_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D038`
--

LOCK TABLES `D038` WRITE;
/*!40000 ALTER TABLE `D038` DISABLE KEYS */;
/*!40000 ALTER TABLE `D038` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D039`
--

-- DROP TABLE IF EXISTS `D039`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D039` (
  `D039_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D039_C007_Id` int(10) unsigned DEFAULT NULL,
  `D039_D046_Id` int(10) unsigned DEFAULT NULL,
  `D039_Codigo_Classe` varchar(5) DEFAULT NULL,
  `D039_Data_Cadastro` datetime DEFAULT NULL,
  `D039_Descricao` varchar(50) DEFAULT NULL,
  `D039_Flag_Visitar` varchar(1) DEFAULT NULL,
  `D039_Flag_Maquina` varchar(1) DEFAULT NULL,
  `D039_Flag_Revenda` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`D039_Id`),
  KEY `T016_FKIndex1` (`D039_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D039`
--

LOCK TABLES `D039` WRITE;
/*!40000 ALTER TABLE `D039` DISABLE KEYS */;
/*!40000 ALTER TABLE `D039` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D040`
--

-- DROP TABLE IF EXISTS `D040`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D040` (
  `D040_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D040_Nome_Maquina` varchar(30) DEFAULT NULL,
  `D040_Descricao` text,
  `D040_Numero_Serie` varchar(20) DEFAULT NULL,
  `D040_Ano_Maquina` year(4) DEFAULT NULL,
  `D040_Flag_Disponivel` varchar(1) DEFAULT 'S',
  `D040_Situacao` varchar(12) DEFAULT NULL,
  `D040_Valor_Preco_Venda` decimal(12,2) DEFAULT '0.00',
  `D040_Foto1` varchar(100) DEFAULT NULL,
  `D040_Foto2` varchar(100) DEFAULT NULL,
  `D040_Foto3` varchar(100) DEFAULT NULL,
  `D040_Foto4` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`D040_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D040`
--

LOCK TABLES `D040` WRITE;
/*!40000 ALTER TABLE `D040` DISABLE KEYS */;
/*!40000 ALTER TABLE `D040` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D041`
--

-- DROP TABLE IF EXISTS `D041`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D041` (
  `D041_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D041_D049_Id` int(10) unsigned DEFAULT NULL,
  `D041_Valor_Base` decimal(15,5) DEFAULT NULL,
  `D041_Valor_Preco_Consumidor` decimal(15,5) DEFAULT NULL,
  `D041_Flag_Tipo` int(1) DEFAULT NULL,
  `D041_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D041_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `D041_Flag_Consumidor` varchar(1) DEFAULT NULL,
  `D041_Flag_Tem_Consumidor` varchar(1) DEFAULT NULL,
  `D041_Flag_Excluir` varchar(1) DEFAULT 'N',
  `D041_Valor_Custo` decimal(10,2) NOT NULL,
  `D041_Valor_Preco_Revenda` decimal(15,5) DEFAULT NULL,
  `D041_D024_Id` int(10) DEFAULT NULL,
  PRIMARY KEY (`D041_Id`),
  KEY `D041_FKIndex1` (`D041_D049_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D041`
--

LOCK TABLES `D041` WRITE;
/*!40000 ALTER TABLE `D041` DISABLE KEYS */;
/*!40000 ALTER TABLE `D041` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D042`
--

-- DROP TABLE IF EXISTS `D042`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D042` (
  `D042_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D042_D049_Id` int(10) unsigned DEFAULT NULL,
  `D042_C004_Id` int(10) unsigned DEFAULT NULL,
  `D042_D049_Id_Itens` int(10) unsigned DEFAULT NULL,
  `D042_Quantidade` decimal(10,3) DEFAULT '0.000',
  `D042_Codigo_Produto_Principal` varchar(30) DEFAULT NULL,
  `D042_Codigo_Produto_Itens` varchar(30) DEFAULT NULL,
  `D042_Codigo_Fornecedor_Itens` varchar(30) DEFAULT NULL,
  `D042_Codigo_Fornecedor_Principal` varchar(30) DEFAULT NULL,
  `D042_Observacao` text,
  PRIMARY KEY (`D042_Id`),
  KEY `D042_FKIndex1` (`D042_D049_Id`),
  KEY `D042_FKIndex2` (`D042_C004_Id`),
  KEY `D042_FKIndex3` (`D042_D049_Id_Itens`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D042`
--

LOCK TABLES `D042` WRITE;
/*!40000 ALTER TABLE `D042` DISABLE KEYS */;
/*!40000 ALTER TABLE `D042` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D043`
--

-- DROP TABLE IF EXISTS `D043`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D043` (
  `D043_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D043_D001_Id` int(10) unsigned DEFAULT NULL,
  `D043_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D043_Descricao_Produto` varchar(40) DEFAULT NULL,
  `D043_Valor_Preco_Consumidor` varchar(12) DEFAULT NULL,
  `D043_Codigo_Linha` decimal(5,2) DEFAULT NULL,
  `D043_Aplicacao` varchar(10) DEFAULT NULL,
  `D043_Aliquota_IPI` decimal(5,2) DEFAULT NULL,
  `D043_Peso_Liquido` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`D043_Id`),
  KEY `D043_FKIndex1` (`D043_D001_Id`),
  KEY `D043_FKIndex2` (`D043_Codigo_Produto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D043`
--

LOCK TABLES `D043` WRITE;
/*!40000 ALTER TABLE `D043` DISABLE KEYS */;
/*!40000 ALTER TABLE `D043` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D044`
--

-- DROP TABLE IF EXISTS `D044`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D044` (
  `D044_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D044_Moeda` varchar(20) DEFAULT NULL,
  `D044_Simbolo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D044`
--

LOCK TABLES `D044` WRITE;
/*!40000 ALTER TABLE `D044` DISABLE KEYS */;
/*!40000 ALTER TABLE `D044` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D045`
--

-- DROP TABLE IF EXISTS `D045`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D045` (
  `D045_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D045_D044_Id` int(10) DEFAULT NULL,
  `D045_D014_Id` int(10) DEFAULT NULL,
  `D045_Descricao` varchar(30) DEFAULT NULL,
  `D045_Formula` varchar(50) DEFAULT NULL,
  `D045_Observacao_Formula` text,
  `D045_Flag_Selecionado` varchar(1) DEFAULT 'N',
  `D045_Flag_Frete` varchar(1) DEFAULT 'N',
  `D045_Flag_Calcular_Aduaneira` varchar(1) DEFAULT 'N',
  `D045_Flag_Calcular_ICMS` varchar(1) DEFAULT 'N',
  `D045_Flag_Calcular_PIS_COFINS` varchar(1) DEFAULT 'N',
  `D045_Flag_Calcular_IPI` varchar(1) DEFAULT 'N',
  `D045_Flag_Calcular_Imposto_Importacao` varchar(1) DEFAULT 'N',
  `D045_Flag_Tipo` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`D045_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D045`
--

LOCK TABLES `D045` WRITE;
/*!40000 ALTER TABLE `D045` DISABLE KEYS */;
/*!40000 ALTER TABLE `D045` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D046`
--

-- DROP TABLE IF EXISTS `D046`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D046` (
  `D046_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D046_Nome` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`D046_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D046`
--

LOCK TABLES `D046` WRITE;
/*!40000 ALTER TABLE `D046` DISABLE KEYS */;
/*!40000 ALTER TABLE `D046` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D047`
--

-- DROP TABLE IF EXISTS `D047`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D047` (
  `D047_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D047_Descricao_Motivo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`D047_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D047`
--

LOCK TABLES `D047` WRITE;
/*!40000 ALTER TABLE `D047` DISABLE KEYS */;
/*!40000 ALTER TABLE `D047` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D048`
--

-- DROP TABLE IF EXISTS `D048`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D048` (
  `D048_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D048_Estoque` varchar(30) NOT NULL,
  PRIMARY KEY (`D048_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D048`
--

LOCK TABLES `D048` WRITE;
/*!40000 ALTER TABLE `D048` DISABLE KEYS */;
/*!40000 ALTER TABLE `D048` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D049`
--

-- DROP TABLE IF EXISTS `D049`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D049` (
  `D049_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D049_D082_Id` int(11) unsigned DEFAULT NULL,
  `D049_D024_Id` int(10) unsigned DEFAULT NULL,
  `D049_D001_Id` int(10) unsigned DEFAULT NULL,
  `D049_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D049_Codigo_Barras` varchar(25) DEFAULT NULL,
  `D049_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `D049_Descricao_Produto` varchar(60) DEFAULT NULL,
  `D049_Peso_Unitario_Kg` decimal(10,3) NOT NULL DEFAULT '0.000',
  `D049_Data_Cadastro` date NOT NULL DEFAULT '0000-00-00',
  `D049_Aplicacao` varchar(60) DEFAULT NULL,
  `D049_Flag_Ativo` varchar(1) DEFAULT NULL,
  `D049_Flag_Nacional_Importado` varchar(1) DEFAULT NULL,
  `D049_Flag_Isento_ICMS` int(1) DEFAULT '2',
  `D049_Percentual_Reducao_ICMS` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D049_Aliquota_ICMS` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D049_Flag_Isento_IPI` int(1) DEFAULT '2',
  `D049_Percentual_Reducao_IPI` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D049_Aliquota_IPI` decimal(6,2) NOT NULL DEFAULT '0.00',
  `D049_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `D049_Codigo_Descricao` int(5) DEFAULT NULL,
  `D049_Codigo_Linha` varchar(11) DEFAULT NULL,
  `D049_Codigo_Local` varchar(10) DEFAULT NULL,
  `D049_Prazo_Entrega` int(10) DEFAULT '0',
  `D049_Marca` varchar(20) DEFAULT NULL,
  `D049_Data_Ultima_Venda` date NOT NULL DEFAULT '0000-00-00',
  `D049_Quantidade_Estoque_Parado_1_Ano` int(10) DEFAULT '0',
  `D049_Quantidade_Estoque_Parado_2_Ano` int(10) DEFAULT '0',
  `D049_Flag_Substituicao_Tributaria` varchar(1) DEFAULT 'N',
  `D049_Flag_Nao_Comprar` varchar(1) DEFAULT 'N',
  `D049_Observacao_Compra` text,
  `D049_Quantidade_Minima_Compra` decimal(10,2) DEFAULT NULL,
  `D049_Flag_Tipo` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`D049_Id`),
  KEY `D049_Index_Codigo_Produto_Id` (`D049_Codigo_Produto`),
  KEY `D049_Index_Codigo_Fornecedor` (`D049_Codigo_Fornecedor`),
  KEY `D049_Index_Produtos` (`D049_D001_Id`),
  KEY `D049_Index_Fornecedores` (`D049_D024_Id`),
  KEY `IDX_Codigo_Produto_Fornecedor` (`D049_Codigo_Produto_Fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D049`
--

LOCK TABLES `D049` WRITE;
/*!40000 ALTER TABLE `D049` DISABLE KEYS */;
/*!40000 ALTER TABLE `D049` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D050`
--

-- DROP TABLE IF EXISTS `D050`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D050` (
  `D050_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D050_D024_Id` int(10) DEFAULT NULL,
  `D050_D051_Id` int(10) DEFAULT NULL,
  `D050_Numero_Serie` varchar(30) DEFAULT NULL,
  `D050_Ano_Fabricacao` varchar(4) DEFAULT NULL,
  `D050_CPL` varchar(8) DEFAULT NULL,
  `D050_Descricao` text,
  `D050_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `D050_Codigo_Maquina` int(5) DEFAULT NULL,
  PRIMARY KEY (`D050_Id`),
  KEY `D050_Index_D024_Id` (`D050_D024_Id`),
  KEY `D050_Index_D051_Id` (`D050_D051_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D050`
--

LOCK TABLES `D050` WRITE;
/*!40000 ALTER TABLE `D050` DISABLE KEYS */;
/*!40000 ALTER TABLE `D050` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D051`
--

-- DROP TABLE IF EXISTS `D051`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D051` (
  `D051_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D051_Nome` varchar(30) DEFAULT NULL,
  `D051_Modelo` varchar(20) DEFAULT NULL,
  `D051_Marca` varchar(20) DEFAULT NULL,
  `D051_Codigo` int(5) DEFAULT NULL,
  PRIMARY KEY (`D051_Id`),
  KEY `D051_Index_Codigo` (`D051_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D051`
--

LOCK TABLES `D051` WRITE;
/*!40000 ALTER TABLE `D051` DISABLE KEYS */;
/*!40000 ALTER TABLE `D051` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D052`
--

-- DROP TABLE IF EXISTS `D052`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D052` (
  `D052_Id` int(5) NOT NULL AUTO_INCREMENT,
  `D052_Login` varchar(20) DEFAULT NULL,
  `D052_Email` text,
  `D052_Nome` varchar(30) DEFAULT NULL,
  `D052_Primeiro_Nome` varchar(20) DEFAULT NULL,
  `D052_Data_Alteracao_Senha` date DEFAULT NULL,
  `D052_Codigo_Sistema_Antigo` int(11) DEFAULT NULL,
  PRIMARY KEY (`D052_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D052`
--

LOCK TABLES `D052` WRITE;
/*!40000 ALTER TABLE `D052` DISABLE KEYS */;
/*!40000 ALTER TABLE `D052` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D053`
--

-- DROP TABLE IF EXISTS `D053`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D053` (
  `D053_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D053_D018_Id` int(10) NOT NULL,
  `D053_C004_Id` int(10) NOT NULL,
  `D053_UF` varchar(2) NOT NULL,
  `D053_Percentual_Reducao_ICMS_1` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Aliquota_ICMS_1` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Percentual_Reducao_ICMS_2` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Aliquota_ICMS_2` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Percentual_Reducao_ICMS_Revenda` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Aliquota_ICMS_Revenda` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Percentual_Reducao_ICMS_Consumidor` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Aliquota_ICMS_Consumidor` decimal(5,2) NOT NULL DEFAULT '0.00',
  `D053_Pais` varchar(2) NOT NULL,
  `D053_Maquinas` int(10) NOT NULL DEFAULT '0',
  `D053_Clientes` int(10) NOT NULL DEFAULT '0',
  `D053_Atendidos` int(10) NOT NULL DEFAULT '0',
  `D053_Cidades` int(10) NOT NULL DEFAULT '0',
  `D053_Motores` int(10) NOT NULL DEFAULT '0',
  `D053_Com_Maquina` int(10) NOT NULL DEFAULT '0',
  `D053_Com_Compra` int(10) NOT NULL DEFAULT '0',
  `D053_Estado` varchar(40) NOT NULL,
  `D053_Observacao` text NOT NULL,
  `D053_Flag_ST` varchar(1) DEFAULT 'N',
  `D053_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `D053_ST_VA` decimal(10,2) DEFAULT '0.00',
  `D053_ST_VA_2` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`D053_Id`),
  KEY `D053_IDX_UF` (`D053_UF`),
  KEY `D053_Index_D018_Id` (`D053_D018_Id`),
  KEY `D053_Index_C004_Id` (`D053_C004_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D053`
--

LOCK TABLES `D053` WRITE;
/*!40000 ALTER TABLE `D053` DISABLE KEYS */;
INSERT INTO `D053` VALUES (1,1,0,'RO','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(2,2,0,'AC','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(3,3,0,'AM','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(4,4,0,'RR','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(5,5,0,'PA','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(6,6,0,'AP','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(7,7,0,'TO','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(8,8,0,'MA','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(9,9,0,'PI','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(10,10,0,'CE','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(11,11,0,'RN','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(12,12,0,'PB','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(13,13,0,'PE','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(14,14,0,'AL','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(15,15,0,'SE','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(16,16,0,'BA','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(17,17,0,'MG','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(18,18,0,'ES','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(19,19,0,'RJ','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(20,20,0,'SP','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(21,21,0,'PR','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(22,22,0,'SC','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(23,23,0,'RS','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(24,24,0,'MS','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(25,25,0,'MT','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(26,26,0,'GO','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(27,27,0,'DF','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(28,28,0,'EX','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00');
/*!40000 ALTER TABLE `D053` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D054`
--

-- DROP TABLE IF EXISTS `D054`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D054` (
  `D054_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D054_Codigo_Kit` varchar(18) DEFAULT NULL,
  `D054_Descricao` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`D054_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D054`
--

LOCK TABLES `D054` WRITE;
/*!40000 ALTER TABLE `D054` DISABLE KEYS */;
/*!40000 ALTER TABLE `D054` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D055`
--

-- DROP TABLE IF EXISTS `D055`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D055` (
  `D055_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D055_D054_Id` int(10) unsigned DEFAULT NULL,
  `D055_D001_Id` int(10) unsigned DEFAULT NULL,
  `D055_Codigo_Produto` varchar(18) DEFAULT NULL,
  `D055_Quantidade` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`D055_Id`),
  KEY `D055_index_Kit` (`D055_D054_Id`),
  KEY `D055_index_Produto` (`D055_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D055`
--

LOCK TABLES `D055` WRITE;
/*!40000 ALTER TABLE `D055` DISABLE KEYS */;
/*!40000 ALTER TABLE `D055` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D056`
--

-- DROP TABLE IF EXISTS `D056`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D056` (
  `D056_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D056_Codigo` varchar(25) DEFAULT NULL,
  `D056_Nome_Conta` varchar(40) DEFAULT NULL,
  `D056_Flag_Tipo` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`D056_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D056`
--

LOCK TABLES `D056` WRITE;
/*!40000 ALTER TABLE `D056` DISABLE KEYS */;
/*!40000 ALTER TABLE `D056` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D057`
--

-- DROP TABLE IF EXISTS `D057`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D057` (
  `D057_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D057_Descricao` varchar(30) DEFAULT NULL,
  `D057_Flag_Selecionado` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`D057_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D057`
--

LOCK TABLES `D057` WRITE;
/*!40000 ALTER TABLE `D057` DISABLE KEYS */;
/*!40000 ALTER TABLE `D057` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D058`
--

-- DROP TABLE IF EXISTS `D058`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D058` (
  `D058_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D058_Tipo_Natureza_Operacao` varchar(40) DEFAULT NULL,
  `D058_Observacao` text,
  PRIMARY KEY (`D058_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D058`
--

LOCK TABLES `D058` WRITE;
/*!40000 ALTER TABLE `D058` DISABLE KEYS */;
/*!40000 ALTER TABLE `D058` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D059`
--

-- DROP TABLE IF EXISTS `D059`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D059` (
  `D059_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D059_Codigo_ST` varchar(3) DEFAULT NULL,
  `D059_Descricao_Origem` varchar(100) DEFAULT NULL,
  `D059_Flag_Substituicao_Tributaria` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`D059_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D059`
--

LOCK TABLES `D059` WRITE;
/*!40000 ALTER TABLE `D059` DISABLE KEYS */;
INSERT INTO `D059` VALUES (1,'00','TRIBUTADA INTEGRALMENTE','N'),(2,'10','TRIBUTADA E COM COBRANÇA DE ICMS POR SUBSTITUIÇÃO TRIBUTÁRIA','N'),(3,'20','COM REDUÇÃO DE BASE DE CALCULO','N'),(4,'30','ISENTA OU NÃO TRIBUTADA E COM COBRANÇA DO ICMS POR SUBSTITUIÇÃO TRIBUTÁRIA','N'),(5,'40','ISENTA','N'),(6,'41','NÃO TRIBUTADA','N'),(7,'50','SUSPENSÃO','N'),(8,'51','DIFERIMENTO','N'),(9,'60','ICMS COBRADO ANTERIORMENTE POR SUBSTITUIÇÃO TRIBUTÁRIA','N'),(10,'70','COM REDUÇÃO DE BASE DE CÁLCULO E COBRANÇA DO ICMS POR SUBSTIUIÇÃO TRIBUTÁRIA','N'),(11,'90','OUTRAS','N');
/*!40000 ALTER TABLE `D059` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D060`
--

-- DROP TABLE IF EXISTS `D060`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D060` (
  `D060_Id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `D060_C008_Id` int(10) unsigned DEFAULT NULL,
  `D060_D003_Id` int(10) unsigned DEFAULT NULL,
  `D060_Codigo` int(5) DEFAULT NULL,
  `D060_Descricao_Grupo` varchar(50) DEFAULT NULL,
  `D060_Codigo_Padrao` varchar(10) DEFAULT '',
  `D060_Descricao_Padrao` varchar(30) DEFAULT '',
  PRIMARY KEY (`D060_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CADASTRO DE GRUPOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D060`
--

LOCK TABLES `D060` WRITE;
/*!40000 ALTER TABLE `D060` DISABLE KEYS */;
/*!40000 ALTER TABLE `D060` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D061`
--

-- DROP TABLE IF EXISTS `D061`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D061` (
  `D061_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D061_C004_Id` int(11) DEFAULT NULL,
  `D061_C007_Id` int(11) DEFAULT NULL,
  `D061_D018_Id` int(11) DEFAULT NULL,
  `D061_D020_Id` varchar(40) DEFAULT NULL,
  `D061_D039_Id` int(11) DEFAULT NULL,
  `D061_D024_Id` int(10) unsigned DEFAULT NULL,
  `D061_Flag_Pessoa_Fisica_Juridica` varchar(1) DEFAULT NULL,
  `D061_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `D061_Cnpj` varchar(20) DEFAULT NULL,
  `D061_Cpf` varchar(20) DEFAULT NULL,
  `D061_Nome_Empresa` varchar(50) DEFAULT NULL,
  `D061_Nome_Fantasia` varchar(50) DEFAULT NULL,
  `D061_Endereco` varchar(40) DEFAULT NULL,
  `D061_Cep` varchar(9) DEFAULT NULL,
  `D061_Bairro` varchar(25) DEFAULT NULL,
  `D061_Praca_Pagamento` varchar(65) DEFAULT NULL,
  `D061_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `D061_Referencia_Comercial_1` varchar(60) DEFAULT NULL,
  `D061_Tipo_Empresa_1` char(1) DEFAULT NULL,
  `D061_Telefone_Comercial_1` varchar(14) DEFAULT NULL,
  `D061_Referencia_Comercial_2` varchar(60) DEFAULT NULL,
  `D061_Tipo_Empresa_2` char(1) DEFAULT NULL,
  `D061_Telefone_Comercial_2` varchar(14) DEFAULT NULL,
  `D061_Referencia_Comercial_3` varchar(60) DEFAULT NULL,
  `D061_Tipo_Empresa_3` char(1) DEFAULT NULL,
  `D061_Telefone_Comercial_3` varchar(14) DEFAULT NULL,
  `D061_Referencia_Comercial_4` varchar(60) DEFAULT NULL,
  `D061_Tipo_Empresa_4` char(1) DEFAULT NULL,
  `D061_Telefone_Comercial_4` varchar(14) DEFAULT NULL,
  `D061_Referencia_Comercial_5` varchar(60) DEFAULT NULL,
  `D061_Tipo_Empresa_5` char(1) DEFAULT NULL,
  `D061_Telefone_Comercial_5` varchar(14) DEFAULT NULL,
  `D061_Referencia_Bancaria` varchar(80) DEFAULT NULL,
  `D061_Nome_Contato_1` varchar(40) DEFAULT NULL,
  `D061_Telefone_1` varchar(30) DEFAULT NULL,
  `D061_Ramal_Telefone_1` varchar(10) DEFAULT NULL,
  `D061_Fax_1` varchar(30) DEFAULT NULL,
  `D061_Celular_1` varchar(30) DEFAULT NULL,
  `D061_Email_1` varchar(255) DEFAULT NULL,
  `D061_Flag_Departamento_1` int(11) NOT NULL DEFAULT '0',
  `D061_Nome_Contato_2` varchar(40) DEFAULT NULL,
  `D061_Telefone_2` varchar(30) DEFAULT NULL,
  `D061_Ramal_Telefone_2` varchar(10) DEFAULT NULL,
  `D061_Fax_2` varchar(30) DEFAULT NULL,
  `D061_Celular_2` varchar(30) DEFAULT NULL,
  `D061_Email_2` varchar(255) DEFAULT NULL,
  `D061_Flag_Departamento_2` int(11) NOT NULL DEFAULT '0',
  `D061_Nome_Contato_3` varchar(40) DEFAULT NULL,
  `D061_Telefone_3` varchar(30) DEFAULT NULL,
  `D061_Ramal_Telefone_3` varchar(10) DEFAULT NULL,
  `D061_Fax_3` varchar(30) DEFAULT NULL,
  `D061_Celular_3` varchar(30) DEFAULT NULL,
  `D061_Email_3` varchar(255) DEFAULT NULL,
  `D061_Flag_Departamento_3` int(11) NOT NULL DEFAULT '0',
  `D061_Observacoes_Pre_Cadastro` text,
  `D061_Observacoes_Cadastro` text,
  `D061_Flag_Situacao` int(1) DEFAULT '1',
  `D061_Data_Inclusao` datetime DEFAULT NULL,
  `D061_Telefone_Banco` char(14) DEFAULT NULL,
  `D061_Flag_Como_Conheceu_Engepecas` tinyint(2) DEFAULT '0',
  `D061_Eng_Amigo_Nome` char(50) DEFAULT NULL,
  `D061_Eng_Amigo_Nome_Empresa` char(50) DEFAULT NULL,
  `D061_Eng_Amigo_Telefone` char(14) DEFAULT NULL,
  `D061_Eng_Amigo_Observacao_Brinde` text,
  `D061_Flag_Eventos` tinyint(1) DEFAULT '0',
  `D061_Flag_Revista` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`D061_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=646 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D061`
--

LOCK TABLES `D061` WRITE;
/*!40000 ALTER TABLE `D061` DISABLE KEYS */;
/*!40000 ALTER TABLE `D061` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D062`
--

-- DROP TABLE IF EXISTS `D062`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D062` (
  `D062_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D062_Nome_Banco` varchar(40) NOT NULL,
  `D062_Numero_Banco` int(3) unsigned zerofill NOT NULL,
  PRIMARY KEY (`D062_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D062`
--

LOCK TABLES `D062` WRITE;
/*!40000 ALTER TABLE `D062` DISABLE KEYS */;
INSERT INTO `D062` VALUES (1,'BANCO DO BRASIL',001),(2,'BRADESCO',237),(3,'BANCO CENTRAL DO BRASIL',002),(4,'BANCO DA AMAZONIA S.A.',003),(5,'BANCO DO NORDESTE DO BRASIL S.A.',004),(6,'BANCO NAC. DE CREDITO COOP. S.A.',006),(7,'BANCO NAC.DESENV.ECON. SOCIAL S.A.',007),(8,'BANCO MERIDIONAL S.A.',008),(9,'BACEN - TESOURO NACIONAL',009),(10,'BANCO DO EST. DE ALAGOAS S.A.',020),(11,'BANCO DO EST. DO ESPIRITO STO. S.A.',021),(12,'BANCO DE CRED. REAL DE M. GER. S.A',022),(13,'BANCO DO EST. DE PERNAMBUCO S.A.',024),(14,'BANCO ALFA S.A.',025),(15,'BANCO DO EST. DO ACRE S.A.',026),(16,'BANCO DO EST. DE SANTA CATARINA S.A',027),(17,'BANCO DO EST. DA BAHIA S.A.',028),(18,'BANCO BANERJ S.A.',029),(19,'PARAIBAN-BCO DO EST DA PARAIBA S.A.',030),(20,'BANCO DO EST. DE GOIAS S.A.',031),(21,'BANCO DO EST. DE MATO GROSSO S.A',032),(22,'BANCO DO EST. DE SP S.A.-BANESPA',033),(23,'BANCO DO EST. DO AMAZONAS S.A.',034),(24,'BANCO DO EST. DO CEARA S.A.',035),(25,'BANCO DO EST. DO MARANHAO S.A.',036),(26,'BANCO DO EST. DO PARA S.A.',037),(27,'BANCO DO EST. DO PARANA S.A',038),(28,'BANCO DO EST. DO PIAUI S.A.',039),(29,'BANCO CARGILL S .A .',040),(30,'BANCO DO EST. R. GRANDE DO SUL S.A.',041),(31,'BANCO J. SAFRA S. A .',042),(32,'BANCO DO EST. RIO GRANDE NORTE S.A.',043),(33,'BANCO BVA S . A .',044),(34,'BANCO DO EST. DE SERGIPE S.A.',047),(35,'BANCO BEMGE S.A.',048),(36,'BANCO DO EST. DE RONDONIA S.A.',059),(37,'BRB-BANCO DE BRASILIA S.A.',070),(38,'CAIXA ECONOMICA FEDERAL',104),(39,'BANCO ITABANCO S.A.',106),(40,'BANCO BBM S.A.',107),(41,'BANCO CREDIBANCO S.A.',109),(42,'BANCO BNL DO BRASIL S.A.',116),(43,'MULTI BANCO S.A.',148),(44,'CAIXA ECONOMICA EST. MINAS GERAIS',150),(45,'NOSSA CAIXA - NOSSO BANCO S.A.',151),(46,'CAIXA ECONOMICA DO ESTADO DE GOIAS',152),(47,'CAIXA ECONOMICA EST. RIO GDE. SUL',153),(48,'CAIXA EC DO EST DE ST CATARINA S.A',154),(49,'BANCO CREDIT COMMERCIAL FRANCE S.A.',164),(50,'BANCO NORCHEM S.A.',165),(51,'BANCO CCF BRASIL S.A.',168),(52,'BANCO DE FINANC. INTERNACIONAL S.A.',171),(53,'CONTINENTAL BANCO S.A.',175),(54,'BANCO BBA-CREDITANSTALT S.A.',184),(55,'BANCO FICRISA AXELRUD S.A.',200),(56,'BANCO AXIAL S.A.',201),(57,'BANCO BRASEG S.A.',202),(58,'BANCO SIBISA S.A.',203),(59,'BANCO INTER AMERICAN EXPRESS S.A.',204),(60,'BANCO SUL AMERICA S.A.',205),(61,'BANCO MARTINELLI S.A.',206),(62,'BANCO GARAVELO S.A.',207),(63,'BANCO PACTUAL S.A.',208),(64,'AGROBANCO BANCO COMERCIAL S.A.',209),(65,'DRESDNER BANK L. AKTIENGESELLCHAFT',210),(66,'BANCO SISTEMA S.A.',211),(67,'BANCO MATONE S.A.',212),(68,'BANCO ARBI S.A.',213),(69,'BANCO DIBENS S.A.',214),(70,'BANCO AMERICA DO SUL S.A.',215),(71,'BANCO REGIONAL MALCON S.A.',216),(72,'BANCO JOHN DEERE S . A .',217),(73,'BBS-BANCO BONSUCESSO S.A.',218),(74,'BANCO ZOGBI S . A .',219),(75,'BANCO CREFISUL S.A.',220),(76,'BANCO FLEMING GRAPHUS S.A.',221),(77,'BANCO AGF BRASEG S.A.',222),(78,'BANCO INTERUNION S.A.',223),(79,'BANCO FIBRA S.A.',224),(80,'BANCO BRASCAN S.A.',225),(81,'BANCO AUXILIAR S.A.',226),(82,'BANCO ROSA S.A.',227),(83,'BANCO ICATU S.A.',228),(84,'BANCO CRUZEIRO DO SUL S.A.',229),(85,'BANCO BANDEIRANTES S.A.',230),(86,'BANCO BOAVISTA INTERATLANTICO S.A.',231),(87,'BANCO INTERPART S.A.',232),(88,'BANCO GE CAPITAL S.A.',233),(89,'BANCO LAVRA S.A.',234),(90,'BANCO LIBERAL S.A.',235),(91,'BANCO CAMBIAL S.A.',236),(92,'BANCO BANCRED S.A.',239),(93,'BANCO DE CRED. REAL DE M. GER. S.A.',240),(94,'BANCO CLASSICO S.A.',241),(95,'BANCO EUROINVEST S.A.-EUROBANCO',242),(96,'BANCO STOCK MAXIMA S.A.',243),(97,'BANCO CIDADE S.A.',244),(98,'BANCO EMPRESARIAL S.A.',245),(99,'BANCO ABC-BRASIL S.A.',246),(100,'BANCO WARBURG DILLON READ S.A.',247),(101,'BANCO INVESTCRED S.A.',249),(102,'BANCO SCHAHIN S.A.',250),(103,'BANCO SAO JORGE S.A.',251),(104,'BANCO FININVEST S.A.',252),(105,'PARANA BANCO S.A.',254),(106,'MILBANCO S.A.',255),(107,'BANCO GULFINVEST S.A.',256),(108,'BANCO INDUSCRED S.A.',258),(109,'BANCO VARIG S.A.',261),(110,'BANCO BOREAL S.A.',262),(111,'BANCO CACIQUE S.A.',263),(112,'BANCO PERFORMANCE S.A.',264),(113,'BANCO FATOR S.A.',265),(114,'BANCO CEDULA S.A.',266),(115,'BANCO BBM-COM.C.IMOB.C.FIN.INV.S.A.',267),(116,'BANCO PLANIBANC S.A.',277),(117,'BANCO BRASILEIRO COMERCIAL S.A.',282),(118,'BANCO BCN S.A.',291),(119,'BCR-BANCO DE CREDITO REAL S.A.',294),(120,'BANCO CREDIPLAN S.A',295),(121,'BANCO DE LA NACION ARGENTINA',300),(122,'BANCO DO PROGRESSO S.A.',302),(123,'BANCO HNF S.A.',303),(124,'BANCO PONTUAL S.A.',304),(125,'BANCO COMERCIAL BANCESA S.A.',308),(126,'BANCO DO COMERCIO E IND. DE SP S.A',314),(127,'BANCO INTER-ATLANTICO S.A.',316),(128,'BANCO DO COMERCIO S.A.',317),(129,'BANCO BMG S.A',318),(130,'BANCO INDUSTRIAL E COMERCIAL S.A.',320),(131,'BANCO ECONOMICO S.A.',334),(132,'BANCO SAVENA S.A.',337),(133,'BANCO F. BARRETO S.A.',338),(134,'BANCO ITAU S.A.',341),(135,'BANCO MERCANTIL S.A.',344),(136,'BANCO FINANCIAL S.A.',345),(137,'BANCO SUDAMERIS BRASIL S.A.',347),(138,'BANCO BOZANO, SIMONSEN S.A.',351),(139,'BANCO SANTANDER BRASIL S.A.',353),(140,'BANCO ABN AMRO S.A.',356),(141,'BANCO INDUSTRIAL DE PERNAMBUCO S.A.',361),(142,'BANCO SOGERAL S.A.',366),(143,'BANCO DIGIBANCO S.A.',369),(144,'BANCO EUROPEU P/AM.LATINA (BEAL)S.A',370),(145,'BANCO ITAMARATI S.A.',372),(146,'BANCO FENICIA S.A.',375),(147,'BANCO CHASE MANHATTAN S.A.',376),(148,'BANCO BMD S.A.',388),(149,'BANCO MERCANTIL DO BRASIL S.A.',389),(150,'BANCO BMC S.A.',394),(151,'HSBC BANK BRASIL S.A.-BCO MULTIPLO',399),(152,'BANCO MINEIRO S.A.',405),(153,'UNIBANCO-UNIAO BCOS BRASILEIROS S.A',409),(154,'BANCO CAPITAL S.A.',412),(155,'BANCO NACIONAL S.A.',415),(156,'BANCO SUL BRASILEIRO S . A .',417),(157,'BANCO BANORTE S.A.',420),(158,'BANCO SAFRA S.A.',422),(159,'BANCO SANTANDER NOROESTE S.A.',424),(160,'BANCO PINTO DE MAGALHAES S . A .',432),(161,'BANFORT-BANCO FORTALEZA S.A.',434),(162,'BANCO REAL DE SAO PAULO S . A .',441),(163,'BANCO REGIONAL S . A .',446),(164,'BANCO RURAL S.A.',453),(165,'BANCO DETOKYO-MITSUBISHI BRASIL S.A',456),(166,'BANCO SUMITOMO BRASILEIRO S.A.',464),(167,'BANCO MITSUBISHI BRASILEIRO S.A.',466),(168,'LLOYDS BANK PLC',472),(169,'BANCO FINANCIAL PORTUGUES',473),(170,'BANCO DE CREDITO COMERCIAL S . A .',474),(171,'CITIBANK N.A.',477),(172,'BANKBOSTON BCO MULTIPLO S.A.',479),(173,'BANCO WACHOVIA S.A.',480),(174,'BANCO AGRIMISA S.A.',483),(175,'BANCO DE RORAIMA S.A.',485),(176,'DEUTSCHE BANK S.A.-BANCO ALEMAO',487),(177,'MORGAN GUARANTY TRUST COMPANY OF NY',488),(178,'BANCO FRANCES INTERN.-BRASIL S.A.',489),(179,'BANCO RESIDENCIA S . A .',490),(180,'BANCO MAISONNAVE S.A.',491),(181,'ING BANK N.V.',492),(182,'BANCO UNION, S.A.C.A.',493),(183,'BANCO DE LA REP. OR. DEL URUGUAY',494),(184,'BANCO DE LA PROV. DE BUENOS AIRES',495),(185,'BANCO EXTERIOR DE ESPANA S.A.',496),(186,'BANCO HISPANO AMERICANO S . A.',497),(187,'CENTRO HISPANO BANCO',498),(188,'BANCO IOCHPE S.A.',499),(189,'BANCO HABITASUL S . A .',500),(190,'BANCO BRASILEIRO IRAQUIANO S.A.',501),(191,'BANCO SANTANDER DE NEGOCIOS S.A',502),(192,'BANCO BRJ S . A .',503),(193,'BANCO MULTIPLIC S.A.',504),(194,'BANCO CREDIT SUISSE F. B. G. S.A.',505),(195,'BANCO LUSO BRASILEIRO S.A.',600),(196,'BFC BANCO S.A.',601),(197,'BANCO PATENTE S.A.',602),(198,'BANCO HERCULES S.A.',603),(199,'BANCO INDUSTRIAL DO BRASIL S.A.',604),(200,'BPA BANCO PAO DE ACUCAR S . A .',605),(201,'BANCO GRANDE RIO S.A.',606),(202,'BANCO SANTOS NEVES S.A.',607),(203,'BANCO OPEN S.A.',608),(204,'BANCO ADOLPHO OLIVEIRA E ASSOC.S.A.',609),(205,'BANCO VR S.A.',610),(206,'BANCO PAULISTA S.A.',611),(207,'BANCO GUANABARA S.A.',612),(208,'BANCO PECUNIA S.A.',613),(209,'BANCO INTERPACIFICO S.A.',616),(210,'BANCO INVESTOR S.A.',617),(211,'BANCO TENDENCIA S.A.',618),(212,'BANCO APLICAP S.A.',621),(213,'BANCO DRACMA S.A.',622),(214,'BANCO PANAMERICANO S.A.',623),(215,'BANCO GENERAL MOTORS S.A.',624),(216,'BANCO ARAUCARIA S.A.',625),(217,'BANCO FICSA S.A.',626),(218,'BANCO DESTAK S.A.',627),(219,'BANCO CRITERIUM S.A.',628),(220,'BANCORP - BANCO COM. E DE INV. S.A.',629),(221,'BANCO INTERCAP S.A.',630),(222,'BANCO COLUMBIA S.A.',631),(223,'BANCO RENDIMENTO S.A.',633),(224,'BANCO TRIANGULO S.A.',634),(225,'BANCO DO EST. DO AMAPA S.A.',635),(226,'BANCO SOFISA S.A.',637),(227,'BANCO PROSPER S.A.',638),(228,'BIG S.A.- BANCO IRMAOS GUIMARAES',639),(229,'BANCO CREDITO METROPOLITANO S.A.',640),(230,'BANCO BILBAO VIZCAYA BRASIL S.A.',641),(231,'BRASBANCO S.A. - BANCO COMERCIAL',642),(232,'BANCO PINE S.A.',643),(233,'BANCO DO EST. DE RORAIMA S.A.',645),(234,'BANCO MARKA S.A.',647),(235,'BANCO ATLANTIS S.A.',648),(236,'BANCO DIMENSAO S.A.',649),(237,'BANCO PEBB S.A.',650),(238,'BANCO FRANCES E BRASILEIRO S.A.',652),(239,'BANCO INDUSVAL S.A.',653),(240,'BANCO A.J.RENNER S.A.',654),(241,'BANCO VOTORANTIM S.A.',655),(242,'BANCO MATRIX S.A.',656),(243,'BANCO TECNICORP S.A.',657),(244,'BANCO PORTO REAL S.A.',658),(245,'BANCO SANTOS S.A.',702),(246,'BANCO INVESTCORP S.A.',705),(247,'BANCO DAYCOVAL S.A.',707),(248,'BANCO VETOR S.A.',711),(249,'BANCO CINDAM S.A.',713),(250,'BANCO VEGA S.A.',715),(251,'BANCO OPERADOR S.A.',718),(252,'BANCO BANIF PRIMUS S . A .',719),(253,'BANCO MAXINVEST S.A.',720),(254,'BANCO CREDIBEL S.A.',721),(255,'BANCO INTERIOR DE SAO PAULO S.A.',722),(256,'BANCO PORTO SEGURO S.A.',724),(257,'BANCO FINANSINOS S.A.',725),(258,'BANCO UNIVERSAL S.A.',726),(259,'BANCO COMERCIAL DE SAO PAULO S.A.',727),(260,'BANCO FITAL S.A.',728),(261,'BANCO FONTE CINDAM S.A.',729),(262,'BANCO COMERCIAL PARAGUAYO S.A.',730),(263,'BANCO GNPP S.A.',731),(264,'BANCO MINAS S.A.',732),(265,'BANCO DAS NACOES S.A.',733),(266,'BANCO GERDAU S.A.',734),(267,'BANCO POTTENCIAL S.A.',735);
/*!40000 ALTER TABLE `D062` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D063`
--

-- DROP TABLE IF EXISTS `D063`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D063` (
  `D063_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D063_D024_Id` int(11) DEFAULT NULL,
  `D063_Nome_Empresa` varchar(60) DEFAULT NULL,
  `D063_Tipo_Empresa` char(1) DEFAULT NULL,
  `D063_Telefone` varchar(14) DEFAULT NULL,
  `D063_Data_Inclusao` datetime DEFAULT NULL,
  `D063_Data_Cliente_Desde` date DEFAULT NULL,
  `D063_Data_Ultima_Compra` date DEFAULT NULL,
  `D063_Valor_Ultima_Compra` decimal(15,2) DEFAULT NULL,
  `D063_Data_Maior_Compra` date DEFAULT NULL,
  `D063_Valor_Maior_Compra` decimal(15,2) DEFAULT NULL,
  `D063_Valor_Limite` decimal(15,2) DEFAULT NULL,
  `D063_Pagamento` varchar(100) DEFAULT NULL,
  `D063_Informante` varchar(100) DEFAULT NULL,
  `D063_Observacao` text,
  PRIMARY KEY (`D063_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2554 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D063`
--

LOCK TABLES `D063` WRITE;
/*!40000 ALTER TABLE `D063` DISABLE KEYS */;
/*!40000 ALTER TABLE `D063` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D064`
--

-- DROP TABLE IF EXISTS `D064`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D064` (
  `D064_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D064_C007_Id` int(11) DEFAULT NULL COMMENT 'Id do Usuario',
  `D064_Nome` varchar(50) NOT NULL COMMENT 'Nome da Enquete',
  `D064_Descricao` text NOT NULL COMMENT 'Descricao do Objetivo da Enquete',
  `D064_Data_Cadastro` date NOT NULL COMMENT 'Data do Cadastro da Enquete',
  PRIMARY KEY (`D064_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabela Enquetes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D064`
--

LOCK TABLES `D064` WRITE;
/*!40000 ALTER TABLE `D064` DISABLE KEYS */;
/*!40000 ALTER TABLE `D064` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D065`
--

-- DROP TABLE IF EXISTS `D065`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D065` (
  `D065_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D065_D064_Id` int(11) NOT NULL COMMENT 'Chave estrangeira da Enquete',
  `D065_Questao` varchar(150) DEFAULT NULL,
  `D065_Tipo_Questao` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 para Descritivo e 2 para Opcoes',
  `D065_Ordem` tinyint(1) unsigned DEFAULT NULL COMMENT 'Ordem das questoes - E chave composta com ID',
  `D065_Flag_Multi_Escolha` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 para sim e 2 para nao -   multipla escolha',
  PRIMARY KEY (`D065_Id`),
  KEY `FK_D065_D064_Id` (`D065_D064_Id`),
  CONSTRAINT `FK_D065_D064_Id` FOREIGN KEY (`D065_D064_Id`) REFERENCES `D064` (`D064_Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Questoes da Enquete D064';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D065`
--

LOCK TABLES `D065` WRITE;
/*!40000 ALTER TABLE `D065` DISABLE KEYS */;
/*!40000 ALTER TABLE `D065` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D066`
--

-- DROP TABLE IF EXISTS `D066`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D066` (
  `D066_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D066_D065_Id` int(11) DEFAULT NULL,
  `D066_D066_Id` int(11) DEFAULT NULL COMMENT 'Auto-Relacionamento com o D066_Id',
  `D066_Alternativa` varchar(100) NOT NULL COMMENT 'Descricao da Alternativa',
  `D066_Valor_Alternativa` varchar(100) DEFAULT NULL COMMENT 'A alternativa pode conter um valor',
  `D066_Tipo_Alternativa` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1 para descritivo e 2 para opcao',
  `D066_Ordem` tinyint(3) unsigned DEFAULT '1' COMMENT 'Ordem das opcoes na questao. Chave composta com o ID',
  PRIMARY KEY (`D066_Id`),
  KEY `FK_D066_D066_Id` (`D066_D066_Id`),
  CONSTRAINT `FK_D066_D066_Id` FOREIGN KEY (`D066_D066_Id`) REFERENCES `D066` (`D066_Id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Alternativas das questoes da enquete';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D066`
--

LOCK TABLES `D066` WRITE;
/*!40000 ALTER TABLE `D066` DISABLE KEYS */;
/*!40000 ALTER TABLE `D066` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D067`
--

-- DROP TABLE IF EXISTS `D067`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D067` (
  `D067_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D067_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `D067_Simbolo` varchar(3) NOT NULL,
  `D067_Descricao` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`D067_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D067`
--

LOCK TABLES `D067` WRITE;
/*!40000 ALTER TABLE `D067` DISABLE KEYS */;
/*!40000 ALTER TABLE `D067` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D068`
--

-- DROP TABLE IF EXISTS `D068`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D068` (
  `D068_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D068_D005_Id` int(11) DEFAULT NULL,
  `D068_D067_Id` int(11) DEFAULT NULL,
  `D068_C004_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`D068_Id`),
  UNIQUE KEY `UK` (`D068_C004_Id`,`D068_D005_Id`),
  KEY `FK_Aliquotas_ICMS` (`D068_D067_Id`),
  KEY `FK_Classificacao` (`D068_D005_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3657 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D068`
--

LOCK TABLES `D068` WRITE;
/*!40000 ALTER TABLE `D068` DISABLE KEYS */;
/*!40000 ALTER TABLE `D068` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D069`
--

-- DROP TABLE IF EXISTS `D069`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D069` (
  `D069_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D069_D070_Id` int(10) DEFAULT NULL,
  `D069_D002_Id` int(10) NOT NULL,
  `D069_D015_Id` int(10) NOT NULL,
  `D069_Codigo_Produto` varchar(30) DEFAULT NULL,
  `D069_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `D069_Descricao` varchar(50) DEFAULT NULL,
  `D069_Preco_Unitario` decimal(10,2) DEFAULT NULL,
  `D069_Percentual_Desconto` decimal(7,4) DEFAULT '0.0000',
  PRIMARY KEY (`D069_Id`),
  KEY `D069_Codigo_Produto` (`D069_Codigo_Produto`)
) ENGINE=MyISAM AUTO_INCREMENT=9410 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D069`
--

LOCK TABLES `D069` WRITE;
/*!40000 ALTER TABLE `D069` DISABLE KEYS */;
/*!40000 ALTER TABLE `D069` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D070`
--

-- DROP TABLE IF EXISTS `D070`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D070` (
  `D070_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D070_Descricao` varchar(50) DEFAULT NULL,
  `D070_D024_Id` int(10) NOT NULL,
  `D070_Desconto` decimal(10,2) DEFAULT NULL,
  `D070_Observacao` text,
  `D070_Tipo_Fator` varchar(1) NOT NULL,
  PRIMARY KEY (`D070_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D070`
--

LOCK TABLES `D070` WRITE;
/*!40000 ALTER TABLE `D070` DISABLE KEYS */;
/*!40000 ALTER TABLE `D070` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D071`
--

-- DROP TABLE IF EXISTS `D071`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D071` (
  `D071_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D071_Tipo_Portador` varchar(20) NOT NULL,
  PRIMARY KEY (`D071_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D071`
--

LOCK TABLES `D071` WRITE;
/*!40000 ALTER TABLE `D071` DISABLE KEYS */;
/*!40000 ALTER TABLE `D071` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D072`
--

-- DROP TABLE IF EXISTS `D072`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D072` (
  `D072_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `D072_D001_Id` int(10) unsigned DEFAULT NULL,
  `D072_Arquivo_Foto` varchar(50) NOT NULL,
  `D072_Flag_Principal` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`D072_Id`),
  UNIQUE KEY `Duplicidade` (`D072_D001_Id`,`D072_Arquivo_Foto`),
  KEY `D072_IDX_PRODUTOS` (`D072_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='FOTOS DOS ITENS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D072`
--

LOCK TABLES `D072` WRITE;
/*!40000 ALTER TABLE `D072` DISABLE KEYS */;
/*!40000 ALTER TABLE `D072` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D073`
--

-- DROP TABLE IF EXISTS `D073`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D073` (
  `D073_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D073_Nome` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`D073_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='CENTRO DE CUSTOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D073`
--

LOCK TABLES `D073` WRITE;
/*!40000 ALTER TABLE `D073` DISABLE KEYS */;
/*!40000 ALTER TABLE `D073` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D074`
--

-- DROP TABLE IF EXISTS `D074`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D074` (
  `D074_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D074_D001_Id_Produto` int(10) unsigned NOT NULL,
  `D074_D001_Id` int(10) unsigned NOT NULL,
  `D074_Quantidade` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`D074_Id`),
  KEY `D074_FKIndex1` (`D074_D001_Id`),
  KEY `D074_FKIndex2` (`D074_D001_Id_Produto`),
  CONSTRAINT `D074_ibfk_1` FOREIGN KEY (`D074_D001_Id`) REFERENCES `D001` (`D001_Id`),
  CONSTRAINT `D074_ibfk_2` FOREIGN KEY (`D074_D001_Id_Produto`) REFERENCES `D001` (`D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='COMPOSICAO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D074`
--

LOCK TABLES `D074` WRITE;
/*!40000 ALTER TABLE `D074` DISABLE KEYS */;
/*!40000 ALTER TABLE `D074` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D075`
--

-- DROP TABLE IF EXISTS `D075`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D075` (
  `D075_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D075_D079_Id` int(10) NOT NULL,
  `D075_D078_Id` int(10) NOT NULL,
  `D075_D001_Id` int(10) unsigned NOT NULL,
  `D075_Lead_Time` decimal(5,2) DEFAULT '0.00',
  `D075_Descricao` text,
  `D075_Ordem` int(2) DEFAULT '0',
  PRIMARY KEY (`D075_Id`),
  KEY `D075_FKIndex1` (`D075_D001_Id`),
  KEY `D075_FKIndex2` (`D075_D078_Id`),
  KEY `D075_FKIndex3` (`D075_D079_Id`),
  CONSTRAINT `D075_ibfk_1` FOREIGN KEY (`D075_D001_Id`) REFERENCES `D001` (`D001_Id`),
  CONSTRAINT `D075_ibfk_2` FOREIGN KEY (`D075_D078_Id`) REFERENCES `D078` (`D078_Id`),
  CONSTRAINT `D075_ibfk_3` FOREIGN KEY (`D075_D079_Id`) REFERENCES `D079` (`D079_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='PROCESSOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D075`
--

LOCK TABLES `D075` WRITE;
/*!40000 ALTER TABLE `D075` DISABLE KEYS */;
/*!40000 ALTER TABLE `D075` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D076`
--

-- DROP TABLE IF EXISTS `D076`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D076` (
  `D076_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D076_D075_Id` int(10) NOT NULL,
  `D076_D001_Id` int(10) unsigned NOT NULL,
  `D076_Quantidade` decimal(10,3) DEFAULT '0.000',
  PRIMARY KEY (`D076_Id`),
  KEY `D076_FKIndex1` (`D076_D075_Id`),
  KEY `D076_FKIndex2` (`D076_D001_Id`),
  CONSTRAINT `D076_ibfk_1` FOREIGN KEY (`D076_D075_Id`) REFERENCES `D075` (`D075_Id`),
  CONSTRAINT `D076_ibfk_2` FOREIGN KEY (`D076_D001_Id`) REFERENCES `D001` (`D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='CONSUMIVEIS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D076`
--

LOCK TABLES `D076` WRITE;
/*!40000 ALTER TABLE `D076` DISABLE KEYS */;
/*!40000 ALTER TABLE `D076` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D077`
--

-- DROP TABLE IF EXISTS `D077`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D077` (
  `D077_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D077_D078_Id` int(10) NOT NULL,
  `D077_Nome` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`D077_Id`),
  KEY `D077_FKIndex1` (`D077_D078_Id`),
  CONSTRAINT `D077_ibfk_1` FOREIGN KEY (`D077_D078_Id`) REFERENCES `D078` (`D078_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='FUNCIONARIOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D077`
--

LOCK TABLES `D077` WRITE;
/*!40000 ALTER TABLE `D077` DISABLE KEYS */;
/*!40000 ALTER TABLE `D077` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D078`
--

-- DROP TABLE IF EXISTS `D078`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D078` (
  `D078_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D078_Nome` varchar(30) DEFAULT NULL,
  `D078_Custo_Minuto` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`D078_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='FUNCAO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D078`
--

LOCK TABLES `D078` WRITE;
/*!40000 ALTER TABLE `D078` DISABLE KEYS */;
/*!40000 ALTER TABLE `D078` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D079`
--

-- DROP TABLE IF EXISTS `D079`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D079` (
  `D079_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D079_Nome` varchar(30) DEFAULT NULL,
  `D079_Custo_Minuto` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`D079_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MAQUINAS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D079`
--

LOCK TABLES `D079` WRITE;
/*!40000 ALTER TABLE `D079` DISABLE KEYS */;
/*!40000 ALTER TABLE `D079` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D080`
--

-- DROP TABLE IF EXISTS `D080`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D080` (
  `D080_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D080_Turno` varchar(20) NOT NULL,
  `D080_Hora_Inicio` varchar(5) NOT NULL,
  `D080_Hora_Fim` varchar(5) NOT NULL,
  PRIMARY KEY (`D080_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D080`
--

LOCK TABLES `D080` WRITE;
/*!40000 ALTER TABLE `D080` DISABLE KEYS */;
/*!40000 ALTER TABLE `D080` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D081`
--

-- DROP TABLE IF EXISTS `D081`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D081` (
  `D081_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D081_D002_Id` int(11) NOT NULL,
  `D081_D080_Id` int(11) NOT NULL,
  PRIMARY KEY (`D081_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D081`
--

LOCK TABLES `D081` WRITE;
/*!40000 ALTER TABLE `D081` DISABLE KEYS */;
/*!40000 ALTER TABLE `D081` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D082`
--

-- DROP TABLE IF EXISTS `D082`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D082` (
  `D082_Id` int(11) NOT NULL AUTO_INCREMENT,
  `D082_Marca` varchar(30) DEFAULT NULL,
  `D082_C008_Id` int(10) DEFAULT NULL,
  `D082_Percentual_Acrescimo` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`D082_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D082`
--

LOCK TABLES `D082` WRITE;
/*!40000 ALTER TABLE `D082` DISABLE KEYS */;
/*!40000 ALTER TABLE `D082` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D083`
--

-- DROP TABLE IF EXISTS `D083`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D083` (
  `D083_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D083_D024_Id` int(10) unsigned DEFAULT NULL,
  `D083_D001_Id` int(10) unsigned DEFAULT NULL,
  `D083_Codigo_Barras` varchar(25) DEFAULT NULL,
  `D083_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `D083_Descricao_Produto` varchar(60) DEFAULT NULL,
  `D083_Data_Cadastro` date NOT NULL DEFAULT '0000-00-00',
  `D083_Flag_Nacional_Importado` varchar(1) DEFAULT NULL,
  `D083_Flag_Nao_Comprar` varchar(1) DEFAULT 'N',
  `D083_Observacao_Compra` text,
  `D083_Quantidade_Minima_Compra` decimal(10,2) DEFAULT NULL,
  `D083_D049_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`D083_Id`),
  KEY `D083_Index_Produtos` (`D083_D001_Id`),
  KEY `D083_Index_Fornecedores` (`D083_D024_Id`),
  KEY `IDX_Codigo_Produto_Fornecedor` (`D083_Codigo_Produto_Fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D083`
--

LOCK TABLES `D083` WRITE;
/*!40000 ALTER TABLE `D083` DISABLE KEYS */;
/*!40000 ALTER TABLE `D083` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D084`
--

-- DROP TABLE IF EXISTS `D084`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D084` (
  `D084_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D084_Regiao` varchar(25) DEFAULT NULL,
  `D084_Observacao` text,
  PRIMARY KEY (`D084_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D084`
--

LOCK TABLES `D084` WRITE;
/*!40000 ALTER TABLE `D084` DISABLE KEYS */;
/*!40000 ALTER TABLE `D084` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D085`
--

-- DROP TABLE IF EXISTS `D085`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D085` (
  `D085_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D085_D024_Id` int(10) unsigned DEFAULT NULL,
  `D085_C007_Id` int(10) unsigned DEFAULT NULL,
  `D085_Data` date DEFAULT '0000-00-00',
  `D085_Mensagem` text,
  PRIMARY KEY (`D085_Id`),
  KEY `IDX_D085_D024_Id` (`D085_D024_Id`),
  KEY `IDX_D085_C007_Id` (`D085_C007_Id`),
  KEY `IDX_D085_Data` (`D085_Data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D085`
--

LOCK TABLES `D085` WRITE;
/*!40000 ALTER TABLE `D085` DISABLE KEYS */;
/*!40000 ALTER TABLE `D085` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D100`
--

-- DROP TABLE IF EXISTS `D100`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D100` (
  `D100_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `D100_Data` date NOT NULL,
  `D100_Dia` int(2) NOT NULL DEFAULT '0',
  `D100_Mes` int(2) unsigned zerofill DEFAULT NULL,
  `D100_Ano` int(4) NOT NULL DEFAULT '0',
  `D100_Numero_Semana` int(1) NOT NULL DEFAULT '0',
  `D100_Dia_Semana` varchar(15) NOT NULL,
  `D100_Observacao` text NOT NULL,
  `D100_Bimestre` int(1) DEFAULT '0',
  `D100_Trimestre` int(1) DEFAULT '0',
  `D100_Semestre` int(1) DEFAULT '0',
  PRIMARY KEY (`D100_Id`),
  KEY `D100_Index_Data` (`D100_Data`),
  KEY `D100_Index_Dia` (`D100_Dia`),
  KEY `D100_Index_Mes` (`D100_Mes`),
  KEY `D100_Index_Ano` (`D100_Ano`),
  KEY `D100_Index_Numero_Semana` (`D100_Numero_Semana`),
  KEY `D100_Index_Dia_Semana` (`D100_Dia_Semana`),
  KEY `D100_Index_Bimestre` (`D100_Bimestre`),
  KEY `D100_Index_Trimestre` (`D100_Trimestre`),
  KEY `D100_Index_Semestre` (`D100_Semestre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D100`
--

LOCK TABLES `D100` WRITE;
/*!40000 ALTER TABLE `D100` DISABLE KEYS */;
/*!40000 ALTER TABLE `D100` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `D101`
--

-- DROP TABLE IF EXISTS `D101`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `D101` (
  `D101_Id` int(10) NOT NULL AUTO_INCREMENT,
  `D101_Ano` varchar(4) DEFAULT NULL,
  `D101_Mes` varchar(2) DEFAULT NULL,
  `D101_Trimestre` int(1) NOT NULL,
  `D101_Semestre` int(1) NOT NULL,
  `D101_Nome_Mes` varchar(30) NOT NULL,
  PRIMARY KEY (`D101_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `D101`
--

LOCK TABLES `D101` WRITE;
/*!40000 ALTER TABLE `D101` DISABLE KEYS */;
/*!40000 ALTER TABLE `D101` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `L001`
--

-- DROP TABLE IF EXISTS `L001`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `L001` (
  `L001_Id` int(11) NOT NULL AUTO_INCREMENT,
  `L001_D009_Id` int(11) DEFAULT NULL,
  `L001_Qte1` decimal(10,3) DEFAULT NULL,
  `L001_Qte2` decimal(10,3) DEFAULT NULL,
  `L001_Estoque_Fisico` decimal(10,3) DEFAULT NULL,
  `L001_Estoque_Inicial` decimal(10,3) DEFAULT NULL,
  `L001_Estoque` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`L001_Id`),
  KEY `L001_D009_Id` (`L001_D009_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=1411987 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `L001`
--

LOCK TABLES `L001` WRITE;
/*!40000 ALTER TABLE `L001` DISABLE KEYS */;
/*!40000 ALTER TABLE `L001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S001`
--

-- DROP TABLE IF EXISTS `S001`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S001` (
  `S001_Id` int(11) NOT NULL AUTO_INCREMENT,
  `S001_Data_Hora_Inclusao` datetime DEFAULT NULL,
  `S001_Nome` varchar(200) DEFAULT NULL,
  `S001_Assunto` varchar(200) DEFAULT NULL,
  `S001_Email` varchar(200) DEFAULT NULL,
  `S001_Mensagem_Fale_Conosco` text,
  PRIMARY KEY (`S001_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S001`
--

LOCK TABLES `S001` WRITE;
/*!40000 ALTER TABLE `S001` DISABLE KEYS */;
/*!40000 ALTER TABLE `S001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S002`
--

-- DROP TABLE IF EXISTS `S002`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S002` (
  `S002_Id` int(11) NOT NULL AUTO_INCREMENT,
  `S002_D002_Id` int(10) DEFAULT '0',
  `S002_Data` date DEFAULT NULL,
  `S002_Titulo` varchar(100) DEFAULT NULL,
  `S002_Publicar` enum('S','N') DEFAULT 'N',
  `S002_Arquivo_Foto` varchar(100) DEFAULT NULL,
  `S002_Link` varchar(200) DEFAULT NULL,
  `S002_Mostrar_Vitrine` enum('S','N') DEFAULT 'N',
  `S002_Tipo_Banner` enum('1','2','3') DEFAULT NULL,
  `S002_Ordem` int(3) DEFAULT '0',
  PRIMARY KEY (`S002_Id`),
  KEY `Fk_Descricao` (`S002_D002_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S002`
--

LOCK TABLES `S002` WRITE;
/*!40000 ALTER TABLE `S002` DISABLE KEYS */;
/*!40000 ALTER TABLE `S002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S003`
--

-- DROP TABLE IF EXISTS `S003`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S003` (
  `S003_Email` varchar(200) NOT NULL,
  `S003_Receber` enum('S','N') DEFAULT 'S',
  `S003_Ativo` enum('S','N') DEFAULT 'S',
  `S003_Data_Inclusao` datetime DEFAULT '0000-00-00 00:00:00',
  `S003_Nome` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`S003_Email`),
  KEY `IDX_Email` (`S003_Email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S003`
--

LOCK TABLES `S003` WRITE;
/*!40000 ALTER TABLE `S003` DISABLE KEYS */;
/*!40000 ALTER TABLE `S003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S006`
--

-- DROP TABLE IF EXISTS `S006`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S006` (
  `S006_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `S006_T064_Id` bigint(20) unsigned DEFAULT NULL,
  `S006_Data_Hora_Inclusao` datetime DEFAULT NULL,
  `S006_Flag_Status` enum('0','1','2','3','4','5','6') DEFAULT '0',
  PRIMARY KEY (`S006_Id`),
  KEY `Fk_Pedido_Internet` (`S006_T064_Id`),
  CONSTRAINT `Fk_Pedido_Internet` FOREIGN KEY (`S006_T064_Id`) REFERENCES `T064` (`T064_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S006`
--

LOCK TABLES `S006` WRITE;
/*!40000 ALTER TABLE `S006` DISABLE KEYS */;
/*!40000 ALTER TABLE `S006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S007`
--

-- DROP TABLE IF EXISTS `S007`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S007` (
  `S007_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `S007_Data_Inclusao` datetime DEFAULT NULL,
  `S007_Email_Remetente` varchar(60) DEFAULT NULL,
  `S007_Email_Apresentacao` varchar(100) DEFAULT NULL,
  `S007_Titulo` varchar(200) DEFAULT NULL,
  `S007_Arquivo_Anexo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`S007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S007`
--

LOCK TABLES `S007` WRITE;
/*!40000 ALTER TABLE `S007` DISABLE KEYS */;
/*!40000 ALTER TABLE `S007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S008`
--

-- DROP TABLE IF EXISTS `S008`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S008` (
  `S008_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `S008_S007_Id` bigint(20) unsigned DEFAULT NULL,
  `S008_Email` varchar(200) NOT NULL,
  `S008_Data_Envio` datetime DEFAULT '0000-00-00 00:00:00',
  `S008_Data_Visualizado` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`S008_Id`),
  UNIQUE KEY `Duplicidade` (`S008_S007_Id`,`S008_Email`),
  CONSTRAINT `Fk_News` FOREIGN KEY (`S008_S007_Id`) REFERENCES `S007` (`S007_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S008`
--

LOCK TABLES `S008` WRITE;
/*!40000 ALTER TABLE `S008` DISABLE KEYS */;
/*!40000 ALTER TABLE `S008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `S009`
--

-- DROP TABLE IF EXISTS `S009`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `S009` (
  `S009_Id` int(10) NOT NULL AUTO_INCREMENT,
  `S009_Nome` varchar(20) DEFAULT NULL,
  `S009_Email` varchar(50) DEFAULT NULL,
  `S009_Observacao` text,
  `S009_Melhorar_Prazo_Entrega` enum('S','N') DEFAULT 'N',
  `S009_Melhorar_Servicos` enum('S','N') DEFAULT 'N',
  `S009_Melhorar_Embalagem` enum('S','N') DEFAULT 'N',
  `S009_Melhorar_Atendimento_Telefonico` enum('S','N') DEFAULT 'N',
  `S009_Outros` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`S009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `S009`
--

LOCK TABLES `S009` WRITE;
/*!40000 ALTER TABLE `S009` DISABLE KEYS */;
/*!40000 ALTER TABLE `S009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T001`
--

-- DROP TABLE IF EXISTS `T001`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T001` (
  `T001_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T001_T072_Id` int(10) unsigned DEFAULT NULL,
  `T001_T013_Id` int(10) unsigned DEFAULT NULL,
  `T001_T014_Id` int(10) unsigned DEFAULT NULL,
  `T001_T007_Id` int(10) unsigned DEFAULT NULL,
  `T001_T008_Id` int(10) unsigned DEFAULT NULL,
  `T001_T054_Id` int(10) unsigned DEFAULT NULL,
  `T001_T055_Id` int(10) unsigned DEFAULT NULL,
  `T001_D024_Id` int(10) unsigned DEFAULT NULL,
  `T001_D024_Id_Cliente` int(10) unsigned DEFAULT NULL,
  `T001_D009_Id` int(10) unsigned DEFAULT NULL,
  `T001_D001_Id` int(10) unsigned DEFAULT NULL,
  `T001_T048_Id` int(10) unsigned DEFAULT NULL,
  `T001_T071_Id` int(11) DEFAULT '0',
  `T001_T066_Id` int(10) unsigned DEFAULT NULL,
  `T001_D006_Id` int(10) unsigned DEFAULT NULL,
  `T001_T029_Id` int(10) unsigned DEFAULT NULL,
  `T001_T030_Id` int(10) unsigned DEFAULT NULL,
  `T001_C004_Id` int(11) DEFAULT NULL,
  `T001_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T001_Data_Lancamento` date DEFAULT NULL,
  `T001_Descricao_Operacao` varchar(20) DEFAULT NULL,
  `T001_Quantidade` decimal(10,3) NOT NULL DEFAULT '0.000',
  `T001_Quantidade_Saldo` decimal(10,3) NOT NULL DEFAULT '0.000',
  `T001_Flag_Operacao` varchar(1) DEFAULT NULL,
  `T001_Valor_Preco_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T001_Observacao` varchar(40) DEFAULT NULL,
  `T001_Valor_Custo_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T001_Valor_Custo_Medio_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T001_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T001_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T001_Flag_Complemento` varchar(1) DEFAULT NULL,
  `T001_Demonstrativo_Custo` text,
  `T001_Flag_MMF` varchar(1) DEFAULT NULL,
  `T001_Flag_Atualiza_Custo` varchar(1) DEFAULT NULL,
  `T001_Flag_Venda_Compra_Outros` varchar(1) DEFAULT NULL,
  `T001_Flag_Devolucao` varchar(1) DEFAULT NULL,
  `T001_Numero_Controle` varchar(20) NOT NULL,
  PRIMARY KEY (`T001_Id`),
  KEY `T001_Index_C004_Id` (`T001_C004_Id`),
  KEY `T001_Index_Nota_Entrada` (`T001_T013_Id`),
  KEY `T001_Index_Itens_Nota_Entrada` (`T001_T014_Id`),
  KEY `T001_Index_Nota_Saida` (`T001_T007_Id`),
  KEY `T001_Index_Itens_Nota_Saida` (`T001_T008_Id`),
  KEY `T001_Index_Importacao` (`T001_T054_Id`),
  KEY `T001_Index_Itens_Importacao` (`T001_T055_Id`),
  KEY `T001_Index_Codigo_Produto` (`T001_Codigo_Produto`),
  KEY `T001_Index_Codigo_Fornecedor` (`T001_Codigo_Fornecedor`),
  KEY `T001_Index_Descricao_Operacao` (`T001_Descricao_Operacao`),
  KEY `T001_Index_Fornecedores` (`T001_D024_Id`),
  KEY `T001_Index_Clientes` (`T001_D024_Id_Cliente`),
  KEY `T001_Index_Estoque` (`T001_D009_Id`),
  KEY `T001_Index_Montagem` (`T001_T048_Id`),
  KEY `T001_Index_Estoque2` (`T001_T066_Id`),
  KEY `T001_Index_Natureza_Operacao` (`T001_D006_Id`),
  KEY `T001_Index_Historico` (`T001_D001_Id`),
  KEY `T001_Index_DE` (`T001_T029_Id`),
  KEY `T001_Index_Pendencia` (`T001_T072_Id`),
  KEY `T001_Index_Itens_DE` (`T001_T030_Id`),
  KEY `IDX_T071` (`T001_T071_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T001`
--

LOCK TABLES `T001` WRITE;
/*!40000 ALTER TABLE `T001` DISABLE KEYS */;
/*!40000 ALTER TABLE `T001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T002`
--

-- DROP TABLE IF EXISTS `T002`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T002` (
  `T002_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T002_T117_Id` int(10) unsigned DEFAULT NULL,
  `T002_D073_Id` int(10) unsigned DEFAULT NULL,
  `T002_T054_Id` int(10) NOT NULL DEFAULT '0',
  `T002_T045_Id` int(10) unsigned DEFAULT NULL,
  `T002_D014_Id` int(10) unsigned DEFAULT NULL,
  `T002_T018_Id` int(10) unsigned DEFAULT NULL,
  `T002_D027_Id` int(10) unsigned DEFAULT NULL,
  `T002_D024_Id` int(10) unsigned DEFAULT NULL,
  `T002_D021_Id` int(10) unsigned DEFAULT NULL,
  `T002_D007_Id` int(10) unsigned DEFAULT NULL,
  `T002_T013_Id` int(10) unsigned DEFAULT NULL,
  `T002_T007_Id` int(10) unsigned DEFAULT NULL,
  `T002_T035_Id` int(10) unsigned DEFAULT NULL,
  `T002_D062_Id` int(10) unsigned NOT NULL DEFAULT '1',
  `T002_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T002_Flag_Conta` varchar(1) DEFAULT NULL,
  `T002_Nome_Cliente` varchar(50) DEFAULT NULL,
  `T002_Numero_Duplicata` varchar(15) DEFAULT NULL,
  `T002_Numero_Documento` varchar(15) DEFAULT NULL,
  `T002_Data_Emissao` date DEFAULT NULL,
  `T002_Data_Vencimento` date DEFAULT NULL,
  `T002_Data_Recebimento` date DEFAULT NULL,
  `T002_Valor_Duplicata` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T002_Valor_Juros` decimal(12,2) NOT NULL DEFAULT '0.00',
  `T002_Valor_Desconto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `T002_Valor_Total` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T002_Numero_Agencia` int(4) NOT NULL DEFAULT '0',
  `T002_Numero_Conta` varchar(10) DEFAULT NULL,
  `T002_Numero_Cheque` int(10) NOT NULL DEFAULT '0',
  `T002_Observacao_1` varchar(40) DEFAULT NULL,
  `T002_Observacao_2` varchar(40) DEFAULT NULL,
  `T002_Numero_Controle` int(6) NOT NULL DEFAULT '0',
  `T002_Data_Lancamento` date DEFAULT NULL,
  `T002_Hora_Lancamento` time DEFAULT NULL,
  `T002_Valor_Base_Comissao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T002_Percentual_Comissao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T002_Valor_Comissao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T002_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T002_Data_Vencimento_1` date DEFAULT NULL,
  `T002_Data_Vencimento_2` date DEFAULT NULL,
  `T002_Data_Vencimento_3` date DEFAULT NULL,
  `T002_Data_Vencimento_4` date DEFAULT NULL,
  `T002_Data_Vencimento_5` date DEFAULT NULL,
  `T002_Agrupamento` varchar(20) DEFAULT NULL,
  `T002_Data_Agrupamento` date DEFAULT NULL,
  `T002_Nosso_Numero` varchar(12) DEFAULT NULL,
  `T002_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T002_Codigo_Revenda` varchar(6) DEFAULT NULL,
  `T002_Codigo_Conta_Banco` int(3) NOT NULL DEFAULT '0',
  `T002_Conta` varchar(16) DEFAULT NULL,
  `T002_SubConta` varchar(30) DEFAULT NULL,
  `T002_Flag_Deb_Cred` varchar(1) DEFAULT NULL,
  `T002_Flag_Enviar_Banco` int(1) NOT NULL DEFAULT '1',
  `T002_Flag_Enviar_Banco_Protestar` int(1) NOT NULL DEFAULT '1',
  `T002_Flag_Enviar_Banco_Juros` int(1) NOT NULL DEFAULT '1',
  `T002_Flag_Tipo` int(1) NOT NULL DEFAULT '0',
  `T002_Flag_Despesas` int(1) NOT NULL DEFAULT '1',
  `T002_Obs_Mudanca_Vencto` text,
  `T002_Obs_Divisao_Titulo` text,
  `T002_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T002_Flag_Baixar_Lote` varchar(1) DEFAULT 'N',
  `T002_Numero_Parcela` int(3) DEFAULT '0',
  `T002_Flag_Titulo_Conferido` char(1) DEFAULT 'N',
  `T002_Observacao` text,
  `T002_Data_Baixa` date DEFAULT '0000-00-00',
  `T002_Flag_Reparcelar` enum('S','N') DEFAULT 'N',
  `T002_Data_Previsao_Recebimento` date DEFAULT NULL,
  `T002_Observacao_Cobranca` tinytext,
  `T002_Valor_Recebido` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`T002_Id`),
  KEY `T002_Index_Id` (`T002_Id`),
  KEY `T002_Index_Nota_Fiscal_Entrada` (`T002_T013_Id`),
  KEY `T002_Index_Baixa_Lote` (`T002_T045_Id`),
  KEY `T002_Index_Nota_Fiscal_Saida` (`T002_T007_Id`),
  KEY `T002_Index_Clientes` (`T002_D024_Id`),
  KEY `T002_Index_Contas_Bancarias` (`T002_D007_Id`),
  KEY `T002_Index_Revendas` (`T002_D021_Id`),
  KEY `T002_Index_Codigo_Cliente` (`T002_Codigo_Cliente`),
  KEY `T002_Index_C004_Id` (`T002_C004_Id`),
  KEY `T002_Index_Numero_Duplicata` (`T002_Numero_Duplicata`),
  KEY `T002_Index_Conta` (`T002_Conta`),
  KEY `T002_Index_SubConta` (`T002_SubConta`),
  KEY `T002_FKIndex6` (`T002_D014_Id`),
  KEY `T002_Index_Data_Vencimento` (`T002_Data_Vencimento`),
  KEY `T002_Index_Data_Recebimento` (`T002_Data_Recebimento`),
  KEY `T002_Index_Portador` (`T002_D027_Id`),
  KEY `T002_Index_Projecoes` (`T002_T018_Id`),
  KEY `T002_Index_Remessa` (`T002_T035_Id`),
  KEY `T002_Index_Flag_Enviar_Banco` (`T002_Flag_Enviar_Banco`),
  KEY `T002_Index_Agrupamento` (`T002_Agrupamento`),
  KEY `T002_Index_Nosso_Numero` (`T002_Nosso_Numero`),
  KEY `T002_index_D062_Id` (`T002_D062_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T002`
--

LOCK TABLES `T002` WRITE;
/*!40000 ALTER TABLE `T002` DISABLE KEYS */;
/*!40000 ALTER TABLE `T002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T003`
--

-- DROP TABLE IF EXISTS `T003`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T003` (
  `T003_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T003_C008_Id` int(11) DEFAULT NULL,
  `T003_D027_Id_Entrada` int(10) unsigned DEFAULT NULL,
  `T003_D027_Id` int(10) unsigned DEFAULT NULL,
  `T003_T064_Id` int(10) unsigned DEFAULT NULL,
  `T003_D047_Id` int(10) unsigned DEFAULT NULL,
  `T003_D018_Id` int(10) unsigned DEFAULT NULL,
  `T003_D013_Id` int(10) unsigned DEFAULT NULL,
  `T003_D022_Id` int(10) unsigned DEFAULT NULL,
  `T003_D036_Id` int(10) unsigned DEFAULT NULL,
  `T003_D021_Id` int(10) unsigned DEFAULT NULL,
  `T003_D024_Id` int(10) unsigned DEFAULT NULL,
  `T003_D006_Id` int(10) unsigned DEFAULT NULL,
  `T003_C007_Id_Vendedor_Interno` int(10) unsigned DEFAULT NULL,
  `T003_C007_Id_Vendedor_Externo` int(10) unsigned DEFAULT NULL,
  `T003_C007_Id_IPV_Liberado` int(10) unsigned DEFAULT NULL,
  `T003_C004_Id` int(10) unsigned DEFAULT NULL,
  `T003_Numero_Orcamento_Venda` int(10) NOT NULL DEFAULT '0',
  `T003_Nome_Cliente` varchar(35) DEFAULT NULL,
  `T003_Nome_Transportadora` varchar(35) DEFAULT NULL,
  `T003_Data_Emissao` date DEFAULT NULL,
  `T003_Flag_Frete` int(1) NOT NULL DEFAULT '0',
  `T003_Prazo_Entrega` varchar(40) DEFAULT NULL,
  `T003_Nome_Comprador` varchar(30) DEFAULT NULL,
  `T003_E_mail_Comprador` varchar(60) DEFAULT NULL,
  `T003_Observacao_1` text,
  `T003_Observacao_2` varchar(70) DEFAULT NULL,
  `T003_Fax` varchar(30) DEFAULT NULL,
  `T003_Nome_Vendedor` varchar(10) DEFAULT NULL,
  `T003_Valor_Desconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Desconto_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Percentual_Desconto` decimal(30,20) DEFAULT NULL,
  `T003_Percentual_Desconto_Confirmado` decimal(30,20) DEFAULT NULL,
  `T003_Valor_Percentual_Desconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Percentual_Desconto_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Percentual_Margem` decimal(6,2) NOT NULL DEFAULT '0.00',
  `T003_Mao_Obra_1` varchar(100) DEFAULT NULL,
  `T003_Mao_Obra_2` varchar(100) DEFAULT NULL,
  `T003_Mao_Obra_3` varchar(100) DEFAULT NULL,
  `T003_Mao_Obra_4` varchar(100) DEFAULT NULL,
  `T003_Valor_Mao_Obra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Flag_Confirmar_Mao_Obra` varchar(1) NOT NULL DEFAULT 'N',
  `T003_Flag_Perdido` varchar(1) DEFAULT NULL,
  `T003_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `T003_Flag_Acp` varchar(1) DEFAULT NULL,
  `T003_Prazo_1` int(4) NOT NULL DEFAULT '0',
  `T003_Prazo_2` int(4) NOT NULL DEFAULT '0',
  `T003_Prazo_3` int(4) NOT NULL DEFAULT '0',
  `T003_Prazo_4` int(4) NOT NULL DEFAULT '0',
  `T003_Prazo_5` int(4) NOT NULL DEFAULT '0',
  `T003_Prazo_6` int(4) NOT NULL DEFAULT '0',
  `T003_Prazo_7` int(4) NOT NULL DEFAULT '0',
  `T003_NF_Observacao_1` text,
  `T003_NF_Observacao_2` text,
  `T003_NF_Devolucao_Numero` varchar(12) DEFAULT NULL,
  `T003_NF_Devolucao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T003_NF_Devolucao_Numero_Pedido` int(10) NOT NULL DEFAULT '0',
  `T003_NF_Devolucao_Emissao` date DEFAULT NULL,
  `T003_Valor_Frete` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Despesas_Acessorias` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Estado` varchar(2) DEFAULT NULL,
  `T003_Codigo_Usuario` int(4) NOT NULL DEFAULT '0',
  `T003_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T003_Codigo_Natureza` int(5) NOT NULL DEFAULT '0',
  `T003_Codigo_Transportadora` varchar(6) DEFAULT NULL,
  `T003_Codigo_Representante` varchar(6) DEFAULT NULL,
  `T003_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T003_IPV_Confirmado` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T003_IPV_Cliente` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T003_IPV_Data_Liberado` datetime DEFAULT NULL,
  `T003_IPV_Liberado` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T003_IPV_Normal` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T003_IPV_Normal_Confirmado` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T003_Valor_Total_Custo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Custo_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Produtos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Produtos_Sem_Desconto` decimal(10,2) DEFAULT '0.00',
  `T003_Valor_Total_Produtos_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Produtos_Sem_Desconto_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T003_Valor_Total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Custo_Final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Custo_Final_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Percentual_Comissao` decimal(5,2) DEFAULT '0.00',
  `T003_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Base_ICMS_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_ICMS_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Base_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_Base_IPI_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Valor_Total_IPI_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T003_Aliquota_ISS` decimal(5,2) DEFAULT '0.00',
  `T003_Valor_ISS` decimal(10,2) DEFAULT '5.00',
  `T003_Peso_Total` decimal(10,3) DEFAULT '0.000',
  `T003_Peso_Total_Confirmado` decimal(10,3) DEFAULT '0.000',
  `T003_Observacao` text,
  `T003_Observacao_Orcamento_Perdido` text,
  `T003_Valor_Entrada` decimal(10,2) DEFAULT '0.00',
  `T003_Numero_Parcelas` int(2) DEFAULT '0',
  `T003_Numero_Dias_Entrada` int(3) DEFAULT '0',
  `T003_Numero_Dias` int(3) DEFAULT '0',
  `T003_Valor_Dolar` decimal(10,4) DEFAULT '0.0000',
  `T003_Telefone` varchar(40) DEFAULT NULL,
  `T003_Flag_Expedicao` int(2) DEFAULT NULL,
  `T003_Flag_Lista_Preco` varchar(1) DEFAULT ' ',
  `T003_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `T003_ST_VA` decimal(10,2) DEFAULT '0.00',
  `T003_Valor_Base_ST` decimal(10,2) DEFAULT '0.00',
  `T003_Valor_ST` decimal(10,2) DEFAULT '0.00',
  `T003_Valor_Base_ST_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T003_Valor_ST_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T003_ICMS_Inter_Estadual` decimal(5,2) DEFAULT '0.00',
  `T003_Flag_Alcada_Vendedor` varchar(1) DEFAULT ' ',
  `T003_Flag_Alcada` varchar(1) DEFAULT ' ',
  `T003_Liberou_Alcada_C007_Id` int(11) DEFAULT NULL,
  `T003_Liberou_Alcada_Data` datetime DEFAULT '0000-00-00 00:00:00',
  `T003_Liberou_Alcada_IPV` decimal(10,4) DEFAULT '0.0000',
  `T003_Liberou_Alcada_Observacao_Nao_Liberado` text,
  `T003_C007_Id_Liberou_Alcada` int(11) DEFAULT NULL,
  `T003_C007_Id_Liberou_Alcada_Data` datetime DEFAULT '0000-00-00 00:00:00',
  `T003_C007_Id_Liberou_Alcada_IPV` decimal(10,4) DEFAULT '0.0000',
  PRIMARY KEY (`T003_Id`),
  KEY `T003_Index_Id` (`T003_Id`),
  KEY `T003_IDX_ESTADO_CLIENTE` (`T003_D018_Id`),
  KEY `T003_IDX_CONTATOS` (`T003_D013_Id`),
  KEY `T003_IDX_CLIENTE` (`T003_D024_Id`),
  KEY `T003_IDX_NATUREZA_OPERACAO` (`T003_D006_Id`),
  KEY `T003_IDX_VENDEDOR_INTERNO` (`T003_C007_Id_Vendedor_Interno`),
  KEY `T003_IDX_VENDEDOR_EXTERNO` (`T003_C007_Id_Vendedor_Externo`),
  KEY `T003_IDX_VENDEDOR_IPV_LIBERADO` (`T003_C007_Id_IPV_Liberado`),
  KEY `T003_IDX_REPRESENTANTE` (`T003_D021_Id`),
  KEY `T003_IDX_TRANSPORTADORA` (`T003_D022_Id`),
  KEY `T003_IDX_TIPO_TRANSPORTE` (`T003_D036_Id`),
  KEY `T003_IDX_CODIGO_USUARIO` (`T003_Codigo_Usuario`),
  KEY `T003_IDX_CODIGO_CLIENTE` (`T003_Codigo_Cliente`),
  KEY `T003_IDX_CODIGO_NATUREZA` (`T003_Codigo_Natureza`),
  KEY `T003_IDX_CODIGO_TRANSPORTADORA` (`T003_Codigo_Transportadora`),
  KEY `T003_IDX_CODIGO_REPRESENTANTE` (`T003_Codigo_Representante`),
  KEY `T003_IDX_EMPRESAS` (`T003_C004_Id`),
  KEY `T003_IDX_MOTIVO` (`T003_D047_Id`),
  KEY `T003_IDX_ORCAMENTO_VENDA` (`T003_Numero_Orcamento_Venda`),
  KEY `T003_IDX_ORCAMENTO_INTERNET` (`T003_T064_Id`),
  KEY `T003_IDX_DATA_EMISSAO` (`T003_Data_Emissao`),
  KEY `T003_IDX_PORTADOR_ENTRADA` (`T003_D027_Id_Entrada`),
  KEY `T003_IDX_PORTADOR` (`T003_D027_Id`),
  KEY `IDX_C008` (`T003_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T003`
--

LOCK TABLES `T003` WRITE;
/*!40000 ALTER TABLE `T003` DISABLE KEYS */;
/*!40000 ALTER TABLE `T003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T004`
--

-- DROP TABLE IF EXISTS `T004`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T004` (
  `T004_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T004_D005_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_D037_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_D006_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_T003_Id` int(10) unsigned DEFAULT NULL,
  `T004_D024_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_D009_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_D004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_D040_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T004_Item` int(4) NOT NULL DEFAULT '0',
  `T004_Quantidade` decimal(10,2) DEFAULT NULL,
  `T004_Descricao_Produto` varchar(50) DEFAULT NULL,
  `T004_Observacao_Entrega` varchar(30) DEFAULT NULL,
  `T004_Valor_Preco_Unitario` decimal(15,5) DEFAULT '0.00000',
  `T004_Valor_Preco_Unitario_Confirmado` decimal(15,5) DEFAULT '0.00000',
  `T004_Valor_Custo_Unitario` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Preco_Sem_Desconto_Unitario` decimal(15,5) DEFAULT '0.00000',
  `T004_Valor_Desconto_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T004_Valor_Desconto_Unitario_Confirmado` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T004_Valor_Despesas_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T004_Valor_Despesas_Unitario_Confirmado` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T004_Valor_Custo_Final_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T004_Valor_Custo_Final_Unitario_Confirmado` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T004_Quantidade_Confirmada` decimal(10,2) DEFAULT NULL,
  `T004_Numero_Orcamento_Venda` varchar(10) DEFAULT NULL,
  `T004_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T004_Codigo_Substituto` varchar(25) DEFAULT NULL,
  `T004_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T004_Codigo_Local` varchar(10) DEFAULT NULL,
  `T004_Valor_Total_Custo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Preco_Sem_Desconto` decimal(20,2) DEFAULT '0.00',
  `T004_Valor_Total_Custo_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Preco` decimal(20,2) DEFAULT '0.00',
  `T004_Valor_Total_Preco_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Preco_Sem_Desconto_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_Total_Desconto` decimal(20,10) DEFAULT '0.0000000000',
  `T004_Valor_Total_Desconto_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Despesas` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Despesas_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Custo_Final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_Valor_Total_Custo_Final_Confirmado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T004_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T004_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T004_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_Base_ICMS_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T004_Valor_ICMS` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_ICMS_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T004_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_Base_IPI_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T004_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_IPI_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Marca` varchar(20) DEFAULT NULL,
  `T004_Peso_Unitario` decimal(10,3) DEFAULT '0.000',
  `T004_Peso_Total` decimal(10,3) DEFAULT '0.000',
  `T004_Peso_Total_Confirmado` decimal(10,3) DEFAULT '0.000',
  `T004_Ultimo_Preco` varchar(250) DEFAULT NULL,
  `T004_Ultimo_Preco_Confirmado` varchar(250) DEFAULT NULL,
  `T004_Flag_Excluir` int(1) DEFAULT '2',
  `T004_Flag_Preco_Especial` varchar(1) DEFAULT NULL,
  `T004_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T004_Valor_Ultimo_Preco_Consulta` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_Ultimo_Preco_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Data_Ultimo_Preco_Consulta` date DEFAULT NULL,
  `T004_Data_Ultimo_Preco_Confirmado` date DEFAULT NULL,
  `T004_Obs_Preco` varchar(30) DEFAULT NULL,
  `T004_Observacao` text,
  `T004_Flag_Comprar` varchar(1) DEFAULT NULL,
  `T004_Flag_ST` varchar(1) DEFAULT 'N',
  `T004_ICMS_Inter_Estadual` decimal(5,2) DEFAULT '0.00',
  `T004_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `T004_ST_VA` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_Base_ST` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_ST` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_Base_ST_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Valor_ST_Confirmado` decimal(10,2) DEFAULT '0.00',
  `T004_Flag_Situacao_Tributaria` varchar(3) DEFAULT NULL,
  `T004_Situacao_Tributaria_PIS` varchar(2) DEFAULT '01',
  `T004_Flag_Isento_PIS` enum('S','N') DEFAULT 'N',
  `T004_Valor_Base_PIS` decimal(20,2) DEFAULT '0.00',
  `T004_Aliquota_PIS` decimal(5,2) DEFAULT '0.00',
  `T004_Valor_PIS` decimal(10,2) DEFAULT '0.00',
  `T004_Situacao_Tributaria_COFINS` varchar(2) DEFAULT '01',
  `T004_Flag_Isento_COFINS` enum('S','N') DEFAULT 'N',
  `T004_Valor_Base_COFINS` decimal(20,2) DEFAULT '0.00',
  `T004_Aliquota_COFINS` decimal(5,2) DEFAULT '0.00',
  `T004_Valor_COFINS` decimal(10,2) DEFAULT '0.00',
  `T004_Reordenar` int(1) DEFAULT '1',
  `T004_D082_Id` int(10) DEFAULT NULL,
  `T004_Valor_Preco_Original` decimal(10,2) DEFAULT NULL,
  `T004_Percentual_Desconto` decimal(10,4) DEFAULT NULL,
  `T004_Flag_Cancelado` varchar(1) DEFAULT ' ',
  PRIMARY KEY (`T004_Id`),
  KEY `T004_IDX_PRODUTO_ESTOQUE` (`T004_D009_Id`),
  KEY `T004_IDX_CLASSIFICACAO_FISCAL` (`T004_D005_Id`),
  KEY `T004_IDX_UNIDADE` (`T004_D037_Id`),
  KEY `T004_IDX_NATUREZA` (`T004_D006_Id`),
  KEY `T004_IDX_LOCAL` (`T004_D004_Id`),
  KEY `T004_IDX_MAQUINAS` (`T004_D040_Id`),
  KEY `T004_IDX_FORNECEDOR` (`T004_D024_Id`),
  KEY `T004_IDX_ORCAMENTO_VENDA` (`T004_T003_Id`),
  KEY `T004_IDX_CODIGO_PRODUTO` (`T004_Codigo_Produto`),
  KEY `T004_IDX_CODIGO_FORNECEDOR` (`T004_Codigo_Fornecedor`),
  KEY `T004_IDX_CODIGO_LOCAL` (`T004_Codigo_Local`),
  KEY `T004_IDX_EMPRESAS` (`T004_C004_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T004`
--

LOCK TABLES `T004` WRITE;
/*!40000 ALTER TABLE `T004` DISABLE KEYS */;
/*!40000 ALTER TABLE `T004` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T005`
--

-- DROP TABLE IF EXISTS `T005`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T005` (
  `T005_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T005_T064_Id` bigint(20) unsigned DEFAULT NULL,
  `T005_C008_Id` int(11) DEFAULT NULL,
  `T005_D027_Id_Entrada` int(10) unsigned DEFAULT NULL,
  `T005_D027_Id` int(10) unsigned DEFAULT NULL,
  `T005_D047_Id` int(10) unsigned DEFAULT NULL,
  `T005_D024_Id` int(10) unsigned DEFAULT NULL,
  `T005_D021_Id` int(10) unsigned DEFAULT NULL,
  `T005_D022_Id` int(10) unsigned DEFAULT NULL,
  `T005_D036_Id` int(10) unsigned DEFAULT NULL,
  `T005_T003_Id` int(10) unsigned DEFAULT NULL,
  `T005_D006_Id` int(10) unsigned DEFAULT NULL,
  `T005_C007_Id_Vendedor_Interno` int(10) unsigned DEFAULT NULL,
  `T005_C007_Id_Vendedor_Externo` int(10) unsigned DEFAULT NULL,
  `T005_C007_Id_IPV_Liberado` int(10) unsigned DEFAULT NULL,
  `T005_C007_Id_Devolucao` int(5) DEFAULT '0',
  `T005_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T005_Numero_Pedido_Venda` int(10) DEFAULT NULL,
  `T005_Data_Emissao` date DEFAULT NULL,
  `T005_Data_Saida` date DEFAULT '0000-00-00',
  `T005_Hora_Saida` time DEFAULT '00:00:00',
  `T005_Data_Entrega` date DEFAULT NULL,
  `T005_Valor_Desconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Percentual_Desconto` decimal(30,20) DEFAULT NULL,
  `T005_Valor_Percentual_Desconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Observacao_1` text,
  `T005_Observacao_2` varchar(70) DEFAULT NULL,
  `T005_Observacao_3` varchar(70) DEFAULT NULL,
  `T005_Prazos_Pagamentos` varchar(40) DEFAULT NULL,
  `T005_Nome_Vendedor` varchar(30) DEFAULT NULL,
  `T005_Mao_Obra_1` text,
  `T005_Mao_Obra_2` text,
  `T005_Mao_Obra_3` text,
  `T005_Mao_Obra_4` text,
  `T005_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `T005_Valor_Mao_Obra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_NF_ACP` varchar(1) DEFAULT NULL,
  `T005_Valor_Dolar` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T005_Flag_ACP` varchar(1) DEFAULT NULL,
  `T005_Prazo_1` int(4) NOT NULL DEFAULT '0',
  `T005_Prazo_2` int(4) NOT NULL DEFAULT '0',
  `T005_Prazo_3` int(4) NOT NULL DEFAULT '0',
  `T005_Prazo_4` int(4) NOT NULL DEFAULT '0',
  `T005_Prazo_5` int(4) NOT NULL DEFAULT '0',
  `T005_Prazo_6` int(4) NOT NULL DEFAULT '0',
  `T005_Prazo_7` int(4) NOT NULL DEFAULT '0',
  `T005_Flag_Frete` int(1) DEFAULT NULL,
  `T005_NF_Observacao` text,
  `T005_Numero_NF_Devolucao` varchar(12) DEFAULT NULL,
  `T005_Data_Emissao_NF_Devolucao` date DEFAULT NULL,
  `T005_Aliquota_ICMS_NF_Devolucao` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Frete` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T005_Flag_Situacao` varchar(1) DEFAULT NULL,
  `T005_Valor_Despesas_Acessorias` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Desconto_NF` decimal(12,6) NOT NULL DEFAULT '0.000000',
  `T005_Valor_Encargos_NF` decimal(12,6) NOT NULL DEFAULT '0.000000',
  `T005_Observacao_Nota_Fiscal` text,
  `T005_Numero_Orcamento` int(10) DEFAULT NULL,
  `T005_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T005_Codigo_Representante` varchar(6) DEFAULT NULL,
  `T005_Codigo_Transportadora` varchar(6) DEFAULT NULL,
  `T005_Codigo_Natureza` int(5) NOT NULL DEFAULT '0',
  `T005_Codigo_Usuario` int(4) NOT NULL DEFAULT '0',
  `T005_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T005_IPV_Cliente` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T005_IPV_Data_Liberado` datetime DEFAULT NULL,
  `T005_IPV_Liberado` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T005_IPV_Normal` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T005_Valor_Total_Custo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total_Produtos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total_Produtos_Sem_Desconto` decimal(10,2) DEFAULT '0.00',
  `T005_Valor_Total_Custo_Final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Percentual_Comissao` decimal(5,2) DEFAULT '0.00',
  `T005_Flag_Travar_Pedido` int(1) DEFAULT '2',
  `T005_Flag_Almoxarifado` varchar(1) DEFAULT 'N',
  `T005_Flag_Status` int(2) DEFAULT '0',
  `T005_Flag_Devolucao` varchar(1) DEFAULT 'N',
  `T005_Flag_Tipo_Devolucao` varchar(10) DEFAULT NULL,
  `T005_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Aliquota_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Base_ICMS_Substituicao` decimal(14,2) DEFAULT '0.00',
  `T005_Valor_ICMS_Substituicao` decimal(14,2) DEFAULT '0.00',
  `T005_Valor_ICMS_Substituicao_Retencao` decimal(14,2) DEFAULT '0.00',
  `T005_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total_Base_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T005_Valor_Total_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T005_Aliquota_ISS` decimal(5,2) DEFAULT '0.00',
  `T005_Valor_ISS` decimal(10,2) DEFAULT '0.00',
  `T005_Valor_Total_PIS` decimal(10,2) DEFAULT '0.00',
  `T005_Valor_Total_COFINS` decimal(10,2) DEFAULT '0.00',
  `T005_Data_Separacao_Pecas` datetime DEFAULT NULL,
  `T005_Usuario_Pecas` int(11) DEFAULT NULL,
  `T005_Liberacao_Credito_Id` int(11) DEFAULT '0',
  `T005_Peso_Total` decimal(10,3) DEFAULT '0.000',
  `T005_Observacao` text,
  `T005_Fax` varchar(30) DEFAULT NULL,
  `T005_Nome_Comprador` varchar(30) DEFAULT NULL,
  `T005_E_mail_Comprador` varchar(60) DEFAULT NULL,
  `T005_Prazo_Entrega` varchar(40) DEFAULT NULL,
  `T005_Observacao_Pedido_Perdido` text,
  `T005_Nome_Transportadora` varchar(35) DEFAULT NULL,
  `T005_Transportadora_Placa_Veiculo` varchar(8) DEFAULT NULL,
  `T005_Estado_Veiculo` char(2) DEFAULT NULL,
  `T005_Valor_Entrada` decimal(10,2) DEFAULT '0.00',
  `T005_Numero_Parcelas` int(2) DEFAULT '0',
  `T005_Numero_Dias_Entrada` int(3) DEFAULT '0',
  `T005_Numero_Dias` int(3) DEFAULT '0',
  `T005_Quantidade_Volumes` int(15) unsigned DEFAULT '0',
  `T005_Especie` varchar(20) DEFAULT NULL,
  `T005_Marca` varchar(20) DEFAULT NULL,
  `T005_Numero_Caixas` varchar(20) DEFAULT NULL,
  `T005_Peso_Liquido` decimal(10,3) DEFAULT '0.000',
  `T005_Peso_Bruto` decimal(10,3) DEFAULT '0.000',
  `T005_Flag_Considerar_Dias_Entrada` varchar(1) DEFAULT 'S',
  `T005_Motivo_Nao_Liberado` tinytext,
  `T005_Flag_Reenviado_Almoxarifado` enum('S','N') DEFAULT 'N',
  `T005_Alteracoes_Reenviado_Almoxarifado` text,
  `T005_Data_Hora_Almoxarifado` datetime DEFAULT NULL,
  `T005_Flag_Separacao` varchar(1) DEFAULT NULL,
  `T005_Flag_Separacao_Liberar` varchar(1) DEFAULT NULL,
  `T005_Numero_Nota_Manual` varchar(20) DEFAULT NULL,
  `T005_Flag_Expedicao` int(2) DEFAULT NULL,
  `T005_Flag_Dividido` varchar(1) DEFAULT NULL,
  `T005_Observacao_Separacao` varchar(100) DEFAULT NULL,
  `T005_Flag_Lista_Preco` varchar(1) DEFAULT ' ',
  `T005_Flag_Aguardar_Liberacao` int(1) DEFAULT '0',
  `T005_Observacao_Comercial` text,
  `T005_ST_VA` decimal(10,2) DEFAULT '0.00',
  `T005_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `T005_ICMS_Inter_Estadual` decimal(5,2) DEFAULT '0.00',
  `T005_Origem_UF` char(2) DEFAULT NULL,
  `T005_Origem_Ano_Mes` char(4) DEFAULT NULL,
  `T005_Origem_Cnpj` varchar(14) DEFAULT NULL,
  `T005_Origem_Modelo` char(2) DEFAULT NULL,
  `T005_Origem_Serie` int(3) DEFAULT NULL,
  `T005_Origem_Chave_Acesso` varchar(44) DEFAULT NULL,
  `T005_Flag_Imprimir_Romaneio` enum('S','N') DEFAULT 'N',
  `T005_Data_Impressao_Romaneio` datetime DEFAULT '0000-00-00 00:00:00',
  `T005_Flag_Importar_Pendente` enum('S','N') DEFAULT 'N',
  `T005_Estoque_Box` varchar(20) DEFAULT ' ',
  PRIMARY KEY (`T005_Id`),
  KEY `T005_Index_Id` (`T005_Id`),
  KEY `T005_Index_C004_Id` (`T005_C004_Id`),
  KEY `T005_Index_Clientes` (`T005_D024_Id`),
  KEY `T005_Index_Natureza_Operacao` (`T005_D006_Id`),
  KEY `T005_Index_Vendedor_Interno` (`T005_C007_Id_Vendedor_Interno`),
  KEY `T005_Index_Vendedor_Externo` (`T005_C007_Id_Vendedor_Externo`),
  KEY `T005_Index_IPV_Liberado` (`T005_C007_Id_IPV_Liberado`),
  KEY `T005_Index_Revenda` (`T005_D021_Id`),
  KEY `T005_Index_Transportadora` (`T005_D022_Id`),
  KEY `T005_Index_Tipo_Transporte` (`T005_D036_Id`),
  KEY `T005_Index_Numero_Pedido_Venda` (`T005_Numero_Pedido_Venda`),
  KEY `T005_Index_Orcamento_Venda` (`T005_T003_Id`),
  KEY `T005_Index_Motivo` (`T005_D047_Id`),
  KEY `T005_Index_DATA_EMISSAO` (`T005_Data_Emissao`),
  KEY `T005_Index_SITUACAO` (`T005_Flag_Situacao`),
  KEY `T005_Index_PORTADOR_ENTRADA` (`T005_D027_Id_Entrada`),
  KEY `T005_Index_PORTADOR` (`T005_D027_Id`),
  KEY `IDX_C008` (`T005_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T005`
--

LOCK TABLES `T005` WRITE;
/*!40000 ALTER TABLE `T005` DISABLE KEYS */;
/*!40000 ALTER TABLE `T005` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T006`
--

-- DROP TABLE IF EXISTS `T006`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T006` (
  `T006_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T006_T008_Id` int(10) unsigned DEFAULT NULL,
  `T006_T006_Id` int(10) unsigned DEFAULT NULL,
  `T006_D005_Id` int(10) unsigned DEFAULT NULL,
  `T006_D037_Id` int(10) unsigned DEFAULT NULL,
  `T006_D006_Id` int(10) unsigned DEFAULT NULL,
  `T006_T004_Id` int(10) unsigned DEFAULT NULL,
  `T006_D024_Id` int(10) unsigned DEFAULT NULL,
  `T006_T005_Id` int(10) unsigned DEFAULT NULL,
  `T006_D009_Id` int(10) unsigned DEFAULT NULL,
  `T006_D004_Id` int(10) unsigned DEFAULT NULL,
  `T006_D040_Id` int(10) unsigned DEFAULT NULL,
  `T006_T014_Id` int(10) unsigned DEFAULT NULL,
  `T006_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T006_Item` int(4) NOT NULL DEFAULT '0',
  `T006_Numero_Orcamento` varchar(10) DEFAULT NULL,
  `T006_Quantidade` decimal(10,2) DEFAULT NULL,
  `T006_Data_Entrega` date DEFAULT NULL,
  `T006_Valor_Custo_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T006_Valor_Preco_Sem_Desconto_Unitario` decimal(19,5) DEFAULT '0.00000',
  `T006_Valor_Desconto_Unitario` decimal(20,10) DEFAULT '0.0000000000',
  `T006_Valor_Despesas_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T006_Valor_Custo_Final_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T006_Valor_Frete_Unitario` decimal(20,10) DEFAULT '0.0000000000',
  `T006_Valor_Despesas_Acessorias_Unitario` decimal(20,10) DEFAULT '0.0000000000',
  `T006_Valor_Preco_Unitario` decimal(19,5) NOT NULL DEFAULT '0.00000',
  `T006_Descricao_Produto` varchar(60) DEFAULT NULL,
  `T006_Codigo_Substituto` varchar(18) DEFAULT NULL,
  `T006_Flag_Status` varchar(1) DEFAULT NULL,
  `T006_Numero_Pedido_Venda` varchar(6) DEFAULT NULL,
  `T006_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T006_Codigo_Local` varchar(10) DEFAULT NULL,
  `T006_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T006_Valor_Total_Custo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_Total_Preco_Sem_Desconto` decimal(20,2) DEFAULT '0.00',
  `T006_Valor_Total_Preco` decimal(20,2) DEFAULT '0.00',
  `T006_Valor_Total_Desconto` decimal(20,10) DEFAULT '0.0000000000',
  `T006_Valor_Total_Despesas` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_Total_Custo_Final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_Total_Frete` decimal(20,10) DEFAULT '0.0000000000',
  `T006_Valor_Total_Despesas_Acessorias` decimal(20,10) DEFAULT '0.0000000000',
  `T006_IPV` decimal(10,4) DEFAULT NULL,
  `T006_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_Base_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T006_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_ICMS` decimal(20,5) DEFAULT '0.00000',
  `T006_Valor_Agregado` decimal(20,2) DEFAULT '0.00',
  `T006_Valor_Base_ICMS_Substituicao` decimal(20,2) DEFAULT '0.00',
  `T006_Percentual_Reducao_ICMS_ST` decimal(5,2) DEFAULT '0.00',
  `T006_Aliquota_ICMS_ST` decimal(5,2) DEFAULT '0.00',
  `T006_Valor_ICMS_Substituicao` decimal(20,2) DEFAULT '0.00',
  `T006_Valor_ICMS_Substituicao_Retencao` decimal(20,2) DEFAULT '0.00',
  `T006_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_Base_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T006_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T006_Valor_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T006_Marca` varchar(20) DEFAULT NULL,
  `T006_Peso_Unitario` decimal(10,3) DEFAULT '0.000',
  `T006_Peso_Total` decimal(10,3) DEFAULT '0.000',
  `T006_Ultimo_Preco` varchar(50) DEFAULT NULL,
  `T006_Ultimo_Preco_Confirmado` varchar(50) DEFAULT NULL,
  `T006_Flag_Excluir` int(1) DEFAULT '2',
  `T006_Flag_Preco_Especial` varchar(1) DEFAULT NULL,
  `T006_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T006_Flag_Comprar` varchar(1) DEFAULT NULL,
  `T006_T006_Id_Dividir` int(10) DEFAULT NULL,
  `T006_Quantidade_Dividir` decimal(10,2) DEFAULT NULL,
  `T006_Quantidade_Separacao` decimal(10,2) DEFAULT NULL,
  `T006_Quantidade_Dividido` decimal(10,2) DEFAULT NULL,
  `T006_Flag_ST` varchar(1) DEFAULT 'N',
  `T006_ICMS_Inter_Estadual` decimal(5,2) DEFAULT '0.00',
  `T006_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `T006_ST_VA` decimal(10,2) DEFAULT '0.00',
  `T006_Situacao_Tributaria_PIS` varchar(2) DEFAULT '01',
  `T006_Flag_Isento_PIS` enum('S','N') DEFAULT 'N',
  `T006_Valor_Base_PIS` decimal(20,2) DEFAULT '0.00',
  `T006_Aliquota_PIS` decimal(5,2) DEFAULT '0.00',
  `T006_Valor_PIS` decimal(10,2) DEFAULT '0.00',
  `T006_Situacao_Tributaria_COFINS` varchar(2) DEFAULT '01',
  `T006_Flag_Isento_COFINS` enum('S','N') DEFAULT 'N',
  `T006_Valor_Base_COFINS` decimal(20,2) DEFAULT '0.00',
  `T006_Aliquota_COFINS` decimal(5,2) DEFAULT '0.00',
  `T006_Valor_COFINS` decimal(10,2) DEFAULT '0.00',
  `T006_Flag_Situacao_Tributaria` varchar(3) DEFAULT NULL,
  `T006_T099_Id` int(11) DEFAULT '0',
  `T006_Reordenar` int(1) DEFAULT '1',
  `T006_Quantidade_Comprar` decimal(10,3) DEFAULT '0.000',
  `T006_Flag_Tipo_Produto` varchar(2) DEFAULT NULL,
  `T006_D049_Id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`T006_Id`),
  KEY `T006_IDX_UNIDADE` (`T006_D037_Id`),
  KEY `T006_IDX_ITENS_PEDIDO_VENDA` (`T006_T006_Id`),
  KEY `T006_IDX_CLASSIFICACAO_FISCAL` (`T006_D005_Id`),
  KEY `T006_IDX_NATUREZA` (`T006_D006_Id`),
  KEY `T006_IDX_PRODUTO_ESTOQUE` (`T006_D009_Id`),
  KEY `T006_IDX_LOCAL` (`T006_D004_Id`),
  KEY `T006_IDX_MAQUINAS` (`T006_D040_Id`),
  KEY `T006_IDX_ITENS_ORCAMENTO` (`T006_T004_Id`),
  KEY `T006_IDX_PEDIDO_VENDA` (`T006_T005_Id`),
  KEY `T006_IDX_FORNECEDOR` (`T006_D024_Id`),
  KEY `T006_IDX_CODIGO_PEDIDO_VENDA` (`T006_Numero_Pedido_Venda`),
  KEY `T006_IDX_CODIGO_FORNECEDOR` (`T006_Codigo_Fornecedor`),
  KEY `T006_IDX_CODIGO_LOCAL` (`T006_Codigo_Local`),
  KEY `T006_IDX_CODIGO_PRODUTO` (`T006_Codigo_Produto`),
  KEY `T006_IDX_ITENS_NOTA_RECEBIDA` (`T006_T014_Id`),
  KEY `T006_IDX_ITENS_NOTA_EMITIDA` (`T006_T008_Id`),
  KEY `T006_IDX_EMPRESAS` (`T006_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T006`
--

LOCK TABLES `T006` WRITE;
/*!40000 ALTER TABLE `T006` DISABLE KEYS */;
/*!40000 ALTER TABLE `T006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T007`
--

-- DROP TABLE IF EXISTS `T007`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T007` (
  `T007_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T007_C008_Id` int(10) unsigned DEFAULT '0',
  `T007_T007_Id_Nota_Complementar` int(10) unsigned DEFAULT NULL,
  `T007_D027_Id` int(10) unsigned DEFAULT NULL,
  `T007_D027_Id_Entrada` int(10) unsigned DEFAULT NULL,
  `T007_T005_Id` int(10) unsigned DEFAULT NULL,
  `T007_T054_Id` int(10) unsigned DEFAULT NULL,
  `T007_D024_Id` int(10) unsigned DEFAULT NULL,
  `T007_D022_Id` int(10) unsigned DEFAULT NULL,
  `T007_D036_Id` int(10) unsigned DEFAULT NULL,
  `T007_D021_Id` int(10) unsigned DEFAULT NULL,
  `T007_D006_Id` int(10) unsigned DEFAULT NULL,
  `T007_D016_Id` int(10) unsigned DEFAULT NULL,
  `T007_D044_Id` int(10) unsigned DEFAULT NULL,
  `T007_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T007_C007_Id_Vendedor_Externo` int(11) NOT NULL DEFAULT '0',
  `T007_C007_Id_Vendedor_Interno` int(11) NOT NULL DEFAULT '0',
  `T007_C007_Id_Devolucao` int(10) DEFAULT NULL,
  `T007_C007_Id` int(11) NOT NULL DEFAULT '0',
  `T007_Flag_Cancelada` varchar(1) DEFAULT NULL,
  `T007_Flag_Conferencia_Frete` varchar(1) DEFAULT 'N',
  `T007_Valor_Frete_Nao_Cobrado` decimal(10,2) DEFAULT '0.00',
  `T007_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T007_Data_Hora_Protocolo_Nfe` datetime DEFAULT NULL,
  `T007_Data_Hora_Cancelamento_Nfe` datetime DEFAULT NULL,
  `T007_Numero_Protocolo_Nfe` varchar(20) DEFAULT NULL,
  `T007_Numero_Protocolo_Cancelamento_Nfe` varchar(20) DEFAULT NULL,
  `T007_Chave_Acesso_Nfe` varchar(44) DEFAULT NULL,
  `T007_Protocolo_Inutilizacao` varchar(20) DEFAULT NULL,
  `T007_Data_Hora_Inutilizacao` datetime DEFAULT NULL,
  `T007_Justificativa_Inutilizacao` text,
  `T007_Ambiente_NFe` enum('1','2') DEFAULT '2',
  `T007_Flag_Contingencia` enum('S','N') DEFAULT 'N',
  `T007_Flag_Finalidade_NFe` enum('1','2','3') DEFAULT '1',
  `T007_Flag_Denegado` enum('S','N') DEFAULT 'N',
  `T007_Flag_Inutilizado` enum('S','N') DEFAULT 'N',
  `T007_Data_Hora_Recebimento_Nfe` datetime DEFAULT NULL,
  `T007_Numero_Recibo_Nfe` varchar(20) DEFAULT NULL,
  `T007_Numero_Nota_Devolvida` varchar(50) DEFAULT NULL,
  `T007_Data_Emissao` date DEFAULT NULL,
  `T007_Data_Saida` date DEFAULT '0000-00-00',
  `T007_Data_Vencimento` date DEFAULT NULL,
  `T007_Numero_Total_Parcelas` int(2) DEFAULT NULL,
  `T007_Codigo_CFOP` varchar(30) DEFAULT NULL,
  `T007_Descricao_CFOP` varchar(30) DEFAULT NULL,
  `T007_Nome_Transportadora` varchar(30) DEFAULT NULL,
  `T007_Tipo_Transporte` varchar(10) DEFAULT NULL,
  `T007_Observacao_1` text,
  `T007_Observacao_2` varchar(60) DEFAULT NULL,
  `T007_Observacao_3` varchar(60) DEFAULT NULL,
  `T007_Flag_Frete` int(1) DEFAULT NULL,
  `T007_Observacao_Nota_Fiscal_1` text,
  `T007_Valor_Frete` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Custo` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Preco` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Produtos` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Produtos_Sem_Desconto` decimal(10,2) DEFAULT '0.00',
  `T007_Valor_Total_Custo_Final` decimal(20,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Prazo_Parcelas` varchar(30) DEFAULT NULL,
  `T007_Percentual_Comissao_Representante` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Despesas_Acessorias` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Desconto` decimal(12,6) NOT NULL DEFAULT '0.000000',
  `T007_Percentual_Desconto` decimal(7,4) DEFAULT '0.0000',
  `T007_Valor_Percentual_Desconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Dolar` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T007_Observacao_Nota_Fiscal_2` text,
  `T007_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T007_Codigo_Natureza` int(5) DEFAULT NULL,
  `T007_Codigo_Representante` varchar(6) DEFAULT NULL,
  `T007_Codigo_Transportadora` varchar(6) DEFAULT NULL,
  `T007_Numero_Pedido_Venda` int(6) DEFAULT NULL,
  `T007_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Base_ICMS_Impresso` decimal(20,5) DEFAULT '0.00000',
  `T007_Valor_Total_Base_ICMS` decimal(20,5) DEFAULT '0.00000',
  `T007_Valor_Total_Base_ICMS_Sem_Reducao` decimal(20,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Base_ICMS_Diferido` decimal(20,2) DEFAULT '0.00',
  `T007_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_ICMS` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Base_ICMS_Substituicao` decimal(14,2) DEFAULT '0.00',
  `T007_Valor_ICMS_Substituicao` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_ICMS_Substituicao_Retencao` decimal(14,2) DEFAULT '0.00',
  `T007_Flag_IPI` varchar(1) DEFAULT NULL,
  `T007_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_Base_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T007_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T007_Valor_Total_IPI` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `T007_Cliente_Razao` varchar(50) DEFAULT NULL,
  `T007_Cliente_CNPJ_CPF` varchar(20) DEFAULT NULL,
  `T007_Cliente_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `T007_Cliente_Inscricao_Suframa` varchar(9) DEFAULT NULL,
  `T007_Cliente_Endereco` varchar(40) DEFAULT NULL,
  `T007_Cliente_Endereco_Numero` varchar(15) DEFAULT NULL,
  `T007_Cliente_Complemento` varchar(60) DEFAULT NULL,
  `T007_Cliente_Bairro` varchar(25) DEFAULT NULL,
  `T007_Cliente_CEP` varchar(9) DEFAULT NULL,
  `T007_Cliente_Cidade` varchar(30) DEFAULT NULL,
  `T007_Cliente_Codigo_Cidade_IBGE` int(7) DEFAULT NULL,
  `T007_Cliente_Telefone` varchar(20) DEFAULT NULL,
  `T007_Cliente_Estado` varchar(2) DEFAULT NULL,
  `T007_Cliente_Codigo_Estado_IBGE` int(2) DEFAULT NULL,
  `T007_Hora_Saida` time DEFAULT '00:00:00',
  `T007_Transportadora_CNPJ` varchar(20) DEFAULT NULL,
  `T007_Transportadora_Inscricao_Estadual` varchar(20) DEFAULT NULL,
  `T007_Transportadora_Endereco` varchar(40) DEFAULT NULL,
  `T007_Transportadora_Cidade` varchar(30) DEFAULT NULL,
  `T007_Transportadora_Estado` varchar(2) DEFAULT NULL,
  `T007_Transportadora_Placa_Veiculo` varchar(8) DEFAULT NULL,
  `T007_Estado_Veiculo` char(2) DEFAULT NULL,
  `T007_Quantidade_Volumes` int(15) unsigned DEFAULT '0',
  `T007_Especie` varchar(20) DEFAULT NULL,
  `T007_Marca` varchar(20) DEFAULT NULL,
  `T007_Numero_Caixas` varchar(20) DEFAULT NULL,
  `T007_Peso_Bruto` decimal(10,3) DEFAULT '0.000',
  `T007_Peso_Liquido` decimal(10,3) DEFAULT '0.000',
  `T007_Mao_Obra_1` varchar(100) DEFAULT NULL,
  `T007_Mao_Obra_2` varchar(100) DEFAULT NULL,
  `T007_Mao_Obra_3` varchar(100) DEFAULT NULL,
  `T007_Mao_Obra_4` varchar(100) DEFAULT NULL,
  `T007_Valor_Mao_Obra` decimal(10,2) DEFAULT '0.00',
  `T007_Inscricao_Municipal` varchar(20) DEFAULT NULL,
  `T007_Aliquota_ISS` decimal(5,2) DEFAULT '0.00',
  `T007_Valor_ISS` decimal(10,2) DEFAULT '0.00',
  `T007_Empresa_Usuaria_Nome` varchar(100) DEFAULT NULL,
  `T007_Empresa_Usuaria_Endereco` varchar(34) DEFAULT NULL,
  `T007_Empresa_Usuaria_Numero` varchar(5) DEFAULT NULL,
  `T007_Empresa_Usuaria_Bairro` varchar(15) DEFAULT NULL,
  `T007_Empresa_Usuaria_Cep` varchar(10) DEFAULT NULL,
  `T007_Empresa_Usuaria_Cidade` varchar(30) DEFAULT NULL,
  `T007_Empresa_Usuaria_Codigo_Cidade_IBGE` int(7) DEFAULT NULL,
  `T007_Empresa_Usuaria_Uf` varchar(2) DEFAULT NULL,
  `T007_Empresa_Usuaria_Estado` varchar(20) DEFAULT NULL,
  `T007_Empresa_Usuaria_Codigo_Estado_IBGE` int(2) DEFAULT NULL,
  `T007_Empresa_Usuaria_Pais` varchar(2) DEFAULT 'BR',
  `T007_Empresa_Usuaria_Telefone` varchar(12) DEFAULT NULL,
  `T007_Empresa_Usuaria_Fax` varchar(12) DEFAULT NULL,
  `T007_Empresa_Usuaria_Cnpj` varchar(18) DEFAULT NULL,
  `T007_Empresa_Usuaria_Inscricao_Estadual` varchar(14) DEFAULT NULL,
  `T007_Empresa_Usuaria_CNAE_Fiscal` varchar(7) DEFAULT NULL,
  `T007_IPV` decimal(10,4) DEFAULT '0.0000',
  `T007_IPV_Cliente` decimal(10,4) DEFAULT '0.0000',
  `T007_Flag_Impresso` varchar(1) DEFAULT 'S',
  `T007_Flag_ACP` varchar(1) DEFAULT NULL,
  `T007_Flag_Entrada_Saida` varchar(1) DEFAULT NULL,
  `T007_Flag_Venda_Compra_Outros` varchar(1) DEFAULT NULL,
  `T007_Flag_Revenda_Consumidor` varchar(1) DEFAULT NULL,
  `T007_Flag_Travar_Nota` varchar(1) DEFAULT 'S',
  `T007_Observacao_Cancelamento` text,
  `T007_Valor_Entrada` decimal(10,2) DEFAULT '0.00',
  `T007_Numero_Parcelas` int(2) DEFAULT '0',
  `T007_Numero_Dias_Entrada` int(3) DEFAULT '0',
  `T007_Numero_Dias` int(3) DEFAULT '0',
  `T007_Flag_OK_Fiscal` varchar(1) DEFAULT NULL,
  `T007_Flag_Mao_Obra` varchar(10) DEFAULT NULL,
  `T007_Numero_Conhecimento` varchar(20) DEFAULT NULL,
  `T007_IPV_sem_icms` decimal(10,4) DEFAULT NULL,
  `T007_Flag_Classificacao` int(2) DEFAULT NULL,
  `T007_Arquivo_PIN` varchar(100) DEFAULT NULL,
  `T007_Numero_Controle` varchar(20) NOT NULL,
  `T007_Serie` varchar(10) NOT NULL,
  `T007_Obs_Classificacao_Fiscal` text,
  `T007_Flag_Expedicao` varchar(1) DEFAULT NULL,
  `T007_ST_VA` decimal(10,2) DEFAULT '0.00',
  `T007_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `T007_Valor_Total_PIS` decimal(15,2) DEFAULT '0.00',
  `T007_Valor_Total_COFINS` decimal(15,2) DEFAULT '0.00',
  `T007_ICMS_Inter_Estadual` decimal(5,2) DEFAULT '0.00',
  `T007_Flag_Inclusao_Manual` enum('S','N') DEFAULT 'N',
  `T007_Origem_UF` char(2) DEFAULT NULL,
  `T007_Origem_Ano_Mes` char(4) DEFAULT NULL,
  `T007_Origem_Cnpj` varchar(14) DEFAULT NULL,
  `T007_Origem_Modelo` char(2) DEFAULT NULL,
  `T007_Origem_Serie` int(3) DEFAULT NULL,
  `T007_Origem_Chave_Acesso` varchar(44) DEFAULT NULL,
  `T007_Flag_SCAN` enum('S','N') DEFAULT 'N',
  `T007_Flag_Almoxarifado` enum('S','N') DEFAULT 'N',
  `T007_Flag_Imprimir_Romaneio` enum('S','N') DEFAULT 'N',
  `T007_Total_Peso` decimal(15,5) DEFAULT NULL,
  `T007_Data_Envio_XML` datetime DEFAULT '0000-00-00 00:00:00',
  `T007_Valor_Total_Imposto_Importacao` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`T007_Id`),
  KEY `T007_Index_Id` (`T007_Id`),
  KEY `T007_Index_Flag_Cancelada` (`T007_Flag_Cancelada`),
  KEY `T007_Index_Clientes` (`T007_D024_Id`),
  KEY `T007_Index_C004_Id` (`T007_C004_Id`),
  KEY `T007_Index_Pedido_Venda` (`T007_T005_Id`),
  KEY `T007_Index_Importacao` (`T007_T054_Id`),
  KEY `T007_Index_Natureza_Operacao` (`T007_D006_Id`),
  KEY `T007_Index_Dolar` (`T007_D016_Id`),
  KEY `T007_Index_Representantes` (`T007_D021_Id`),
  KEY `T007_Index_Transportadoras` (`T007_D022_Id`),
  KEY `T007_Index_Tipo_Transporte` (`T007_D036_Id`),
  KEY `T007_Index_Numero_Nota` (`T007_Numero_Nota_Fiscal`),
  KEY `T007_Index_Data_Emissao` (`T007_Data_Emissao`),
  KEY `T007_index_Vendedor_Externo` (`T007_C007_Id_Vendedor_Externo`),
  KEY `T007_index_Vendedor_Interno` (`T007_C007_Id_Vendedor_Interno`),
  KEY `T007_index_Usuario` (`T007_C007_Id`),
  KEY `T007_index_Moeda_Importacao` (`T007_D044_Id`),
  KEY `T007_index_PORTADOR` (`T007_D027_Id`),
  KEY `T007_index_PORTADOR_ENTRADA` (`T007_D027_Id_Entrada`),
  KEY `T007_Flag_Classificacao` (`T007_Flag_Classificacao`),
  KEY `T007_Cliente_Estado` (`T007_Cliente_Estado`),
  KEY `T007_Numero_Controle` (`T007_Numero_Controle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T007`
--

LOCK TABLES `T007` WRITE;
/*!40000 ALTER TABLE `T007` DISABLE KEYS */;
/*!40000 ALTER TABLE `T007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T008`
--

-- DROP TABLE IF EXISTS `T008`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T008` (
  `T008_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T008_T055_Id` int(10) unsigned DEFAULT NULL,
  `T008_D037_Id` int(10) unsigned DEFAULT NULL,
  `T008_T006_Id` int(10) unsigned DEFAULT NULL,
  `T008_T007_Id` int(10) unsigned DEFAULT NULL,
  `T008_T008_Id` int(10) unsigned DEFAULT NULL,
  `T008_T014_Id` int(10) unsigned DEFAULT NULL,
  `T008_D009_Id` int(10) unsigned DEFAULT NULL,
  `T008_D009_Id_Pendencia` int(10) unsigned DEFAULT NULL,
  `T008_D005_Id` int(10) unsigned DEFAULT NULL,
  `T008_D006_Id` int(10) unsigned DEFAULT NULL,
  `T008_D040_Id` int(10) unsigned DEFAULT NULL,
  `T008_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T008_D003_Id` int(11) DEFAULT NULL,
  `T008_Quantidade` decimal(10,3) NOT NULL DEFAULT '0.000',
  `T008_Unidade` varchar(5) DEFAULT NULL,
  `T008_Codigo_CFOP` decimal(5,3) DEFAULT '0.000',
  `T008_Flag_Entrada_Saida` varchar(1) DEFAULT NULL,
  `T008_Data_Emissao` date DEFAULT NULL,
  `T008_Nome_Transportadora` varchar(30) DEFAULT NULL,
  `T008_Flag_Nacional_Importado` varchar(1) DEFAULT NULL,
  `T008_Descricao_Produto` varchar(120) DEFAULT NULL,
  `T008_Codigo_Substituto` varchar(18) DEFAULT NULL,
  `T008_Valor_Custo_Unitario` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `T008_Valor_Total_Custo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `T008_Valor_Preco_Sem_Desconto_Unitario` decimal(20,5) DEFAULT '0.00000',
  `T008_Valor_Total_Preco_Sem_Desconto` decimal(20,2) DEFAULT '0.00',
  `T008_Valor_Preco_Unitario` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `T008_Valor_Total_Preco` decimal(20,2) NOT NULL DEFAULT '0.00',
  `T008_Valor_Desconto_Unitario` decimal(20,10) DEFAULT '0.0000000000',
  `T008_Valor_Total_Desconto` decimal(20,10) DEFAULT '0.0000000000',
  `T008_Valor_Despesas_Unitario` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `T008_Valor_Total_Despesas` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `T008_Valor_Custo_Final_Unitario` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `T008_Valor_Total_Custo_Final` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `T008_Valor_Frete_Unitario` decimal(20,10) DEFAULT '0.0000000000',
  `T008_Valor_Total_Frete` decimal(20,10) DEFAULT '0.0000000000',
  `T008_Valor_Despesas_Acessorias_Unitario` decimal(20,10) DEFAULT '0.0000000000',
  `T008_Valor_Total_Despesas_Acessorias` decimal(20,10) DEFAULT '0.0000000000',
  `T008_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T008_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T008_Codigo_Produto_Pendencia` varchar(30) DEFAULT NULL,
  `T008_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T008_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T008_Codigo_Fornecedor_Pendencia` varchar(6) DEFAULT NULL,
  `T008_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T008_Codigo_Natureza` int(5) NOT NULL DEFAULT '0',
  `T008_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T008_Valor_Base_ICMS_Impresso` decimal(20,5) DEFAULT '0.00000',
  `T008_Valor_Base_ICMS` decimal(20,5) DEFAULT '0.00000',
  `T008_Valor_Base_ICMS_Sem_Reducao` decimal(20,5) DEFAULT '0.00000',
  `T008_Valor_Base_ICMS_Diferido` decimal(20,5) DEFAULT '0.00000',
  `T008_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T008_Valor_ICMS` decimal(20,5) DEFAULT '0.00000',
  `T008_Valor_Agregado` decimal(20,2) DEFAULT '0.00',
  `T008_Valor_Base_ICMS_Substituicao` decimal(20,2) DEFAULT '0.00',
  `T008_Percentual_Reducao_ICMS_ST` decimal(5,2) DEFAULT '0.00',
  `T008_Aliquota_ICMS_ST` decimal(5,2) DEFAULT '0.00',
  `T008_Valor_ICMS_Substituicao` decimal(20,2) DEFAULT '0.00',
  `T008_Valor_ICMS_Substituicao_Retencao` decimal(20,2) DEFAULT '0.00',
  `T008_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T008_Valor_Base_IPI` decimal(20,2) NOT NULL DEFAULT '0.00',
  `T008_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T008_Valor_IPI` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `T008_IPV` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `T008_Codigo_Produto_Nota` varchar(25) DEFAULT NULL,
  `T008_Flag_Preco_Especial` varchar(1) DEFAULT NULL,
  `T008_Flag_Situacao_Tributaria` varchar(3) DEFAULT NULL,
  `T008_Flag_Classificacao` int(2) DEFAULT NULL,
  `T008_Peso_Unitario` decimal(10,3) DEFAULT '0.000',
  `T008_Peso_Total` decimal(10,3) DEFAULT '0.000',
  `T008_Flag_Classificacao_Fiscal` varchar(5) DEFAULT NULL,
  `T008_Flag_ST` varchar(1) DEFAULT 'N',
  `T008_ICMS_Inter_Estadual` decimal(5,2) DEFAULT '0.00',
  `T008_ST_ICMS_Interno` decimal(5,2) DEFAULT '0.00',
  `T008_ST_VA` decimal(10,2) DEFAULT '0.00',
  `T008_Situacao_Tributaria_PIS` varchar(2) DEFAULT '01',
  `T008_Flag_Isento_PIS` enum('S','N') DEFAULT 'N',
  `T008_Valor_Base_PIS` decimal(20,2) DEFAULT '0.00',
  `T008_Aliquota_PIS` decimal(5,2) DEFAULT '0.00',
  `T008_Valor_PIS` decimal(10,2) DEFAULT '0.00',
  `T008_Situacao_Tributaria_COFINS` varchar(2) DEFAULT '01',
  `T008_Flag_Isento_COFINS` enum('S','N') DEFAULT 'N',
  `T008_Valor_Base_COFINS` decimal(20,2) DEFAULT '0.00',
  `T008_Aliquota_COFINS` decimal(5,2) DEFAULT '0.00',
  `T008_Valor_COFINS` decimal(10,2) DEFAULT '0.00',
  `T008_Flag_Excluir` enum('S','N') DEFAULT 'N',
  `T008_Aliquota_Imposto_Importacao` decimal(5,2) DEFAULT '0.00',
  `T008_Valor_Base_Imposto_Importacao_Unitario` decimal(19,6) DEFAULT '0.000000',
  `T008_Valor_Total_Base_Imposto_Importacao` decimal(19,2) DEFAULT '0.00',
  `T008_Valor_Imposto_Importacao_Unitario` decimal(19,6) DEFAULT '0.000000',
  `T008_Valor_Total_Imposto_Importacao` decimal(19,2) DEFAULT '0.00',
  `T008_Valor_Total_Despesas_Aduaneira` decimal(19,2) DEFAULT '0.00',
  `T008_Valor_Total_IOF` decimal(19,2) DEFAULT '0.00',
  `T008_Numero_Documento_Importacao` varchar(10) DEFAULT NULL,
  `T008_Data_Registro_Documento_Importacao` date DEFAULT NULL,
  `T008_Local_Desembaraco` varchar(60) DEFAULT NULL,
  `T008_UF_Desembaraco` varchar(2) DEFAULT NULL,
  `T008_Data_Desembaraco` date DEFAULT NULL,
  `T008_Codigo_Exportador` varchar(60) DEFAULT NULL,
  `T008_Numero_Adicao` int(3) DEFAULT '0',
  `T008_Numero_Sequencia_Adicao` int(3) DEFAULT '0',
  `T008_Codigo_Fabricante` varchar(60) DEFAULT NULL,
  `T008_Valor_Desconto_Item_Adicao` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`T008_Id`),
  KEY `T008_Index_C004_Id` (`T008_C004_Id`),
  KEY `T008_Index_Estoque` (`T008_D009_Id`),
  KEY `T008_Index_Pendencia` (`T008_D009_Id_Pendencia`),
  KEY `T008_Index_Maquinas` (`T008_D040_Id`),
  KEY `T008_Index_Unidade` (`T008_D037_Id`),
  KEY `T008_Index_Itens_Pedido` (`T008_T006_Id`),
  KEY `T008_Index_Classificacao_Fiscal` (`T008_D005_Id`),
  KEY `T008_Index_Nota_Fiscal` (`T008_T007_Id`),
  KEY `T008_Index_Item_Devolvido_Emitido` (`T008_T008_Id`),
  KEY `T008_Index_Item_Devolvido_Entrada` (`T008_T014_Id`),
  KEY `T008_Index_Natureza_Operacao` (`T008_D006_Id`),
  KEY `T008_Index_Valor_Unitario` (`T008_Valor_Preco_Unitario`),
  KEY `T008_Index_Codigo_Produto` (`T008_Codigo_Produto`),
  KEY `T008_Index_Codigo_Fornecedor` (`T008_Codigo_Fornecedor`),
  KEY `T008_Index_Numero_Nota_Fiscal` (`T008_Numero_Nota_Fiscal`),
  KEY `T008_Index_Itens_Importacao` (`T008_T055_Id`),
  KEY `T008_D003_Id` (`T008_D003_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T008`
--

LOCK TABLES `T008` WRITE;
/*!40000 ALTER TABLE `T008` DISABLE KEYS */;
/*!40000 ALTER TABLE `T008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T009`
--

-- DROP TABLE IF EXISTS `T009`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T009` (
  `T009_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T009_C008_Id` int(10) unsigned DEFAULT NULL,
  `T009_C007_Id` int(10) unsigned DEFAULT NULL,
  `T009_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T009_Data` date DEFAULT NULL,
  `T009_Observacao` text,
  `T009_Flag_Cotacao_Finalizada` varchar(1) DEFAULT 'N',
  `T009_Flag_Fornecedor_Automatico` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T009_Id`),
  KEY `T009_IDX_USUARIO` (`T009_C007_Id`),
  KEY `T009_IDX_TIPO_PRODUTOS` (`T009_C008_Id`),
  KEY `T009_IDX_EMPRESAS` (`T009_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T009`
--

LOCK TABLES `T009` WRITE;
/*!40000 ALTER TABLE `T009` DISABLE KEYS */;
/*!40000 ALTER TABLE `T009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T010`
--

-- DROP TABLE IF EXISTS `T010`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T010` (
  `T010_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T010_D044_Id` int(10) unsigned DEFAULT NULL,
  `T010_D036_Id` int(10) unsigned DEFAULT NULL,
  `T010_D022_Id` int(10) unsigned DEFAULT NULL,
  `T010_T009_Id` int(10) unsigned DEFAULT NULL,
  `T010_D024_Id` int(10) unsigned DEFAULT NULL,
  `T010_C007_Id` int(10) unsigned DEFAULT NULL,
  `T010_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_1` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_2` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_3` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_4` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_5` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_6` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_7` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_8` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_9` int(10) NOT NULL DEFAULT '0',
  `T010_Prazo_Pagamento_10` int(10) NOT NULL DEFAULT '0',
  `T010_Flag_Gerar_OC` int(1) DEFAULT '2',
  `T010_Flag_Excluir` varchar(1) DEFAULT 'N',
  `T010_Observacao` text,
  `T010_Prazo_Entrega` int(10) NOT NULL DEFAULT '0',
  `T010_Valor_Frete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T010_Valor_Total_Custo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T010_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T010_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T010_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T010_Valor_Total_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T010_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T010_Valor_Total_Base_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T010_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T010_Valor_Total_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T010_Nome_Fornecedor` varchar(50) DEFAULT NULL,
  `T010_Nome_Contato` varchar(40) DEFAULT NULL,
  `T010_Fax` varchar(30) DEFAULT NULL,
  `T010_Flag_Frete` int(1) DEFAULT NULL,
  `T010_Prazo_Pagamento` varchar(60) DEFAULT NULL,
  `T010_E_mail_Comprador` varchar(50) DEFAULT NULL,
  `T010_Data` date DEFAULT NULL,
  `T010_Flag_Cotacao_Finalizada` varchar(1) DEFAULT NULL,
  `T010_C008_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`T010_Id`),
  KEY `T010_IDX_COTACA_COMPRA` (`T010_T009_Id`),
  KEY `T010_IDX_EMPRESAS` (`T010_C004_Id`),
  KEY `T010_FKIndex3` (`T010_D024_Id`),
  KEY `T010_FKIndex4` (`T010_D022_Id`),
  KEY `T010_FKIndex5` (`T010_D036_Id`),
  KEY `T010_FKIndex6` (`T010_D044_Id`),
  KEY `T010_FKIndex7` (`T010_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T010`
--

LOCK TABLES `T010` WRITE;
/*!40000 ALTER TABLE `T010` DISABLE KEYS */;
/*!40000 ALTER TABLE `T010` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T011`
--

-- DROP TABLE IF EXISTS `T011`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T011` (
  `T011_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T011_D022_Id` int(10) unsigned DEFAULT NULL,
  `T011_D024_Id` int(10) unsigned DEFAULT NULL,
  `T011_T009_Id` int(10) unsigned DEFAULT NULL,
  `T011_D036_Id` int(10) unsigned DEFAULT NULL,
  `T011_T010_Id` int(10) unsigned DEFAULT NULL,
  `T011_C007_Id` int(10) unsigned DEFAULT NULL,
  `T011_C008_Id` int(10) unsigned DEFAULT NULL,
  `T011_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T011_Numero` varchar(10) DEFAULT NULL,
  `T011_Data_Emissao` date DEFAULT NULL,
  `T011_Data_Previsao_Recebimento` date DEFAULT NULL,
  `T011_Data_Previsao_Pagamento` date DEFAULT NULL,
  `T011_Prazo_Pagamento_1` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_2` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_3` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_4` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_5` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_6` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_7` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_8` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_9` int(10) DEFAULT '0',
  `T011_Prazo_Pagamento_10` int(10) DEFAULT '0',
  `T011_Observacao` text,
  `T011_Prazo_Entrega` varchar(50) DEFAULT NULL,
  `T011_Valor_Frete` decimal(10,2) DEFAULT '0.00',
  `T011_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T011_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T011_Valor_Total_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T011_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T011_Valor_Total_ICMS` decimal(10,2) DEFAULT '0.00',
  `T011_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T011_Valor_Total_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T011_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T011_Valor_Total_IPI` decimal(10,2) DEFAULT '0.00',
  `T011_Nome_Contato` varchar(40) DEFAULT NULL,
  `T011_Fax` varchar(30) DEFAULT NULL,
  `T011_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T011_Codigo_Comprador` varchar(10) DEFAULT NULL,
  `T011_Centro_Custo` varchar(10) NOT NULL,
  `T011_Prazo_Pagamento` varchar(50) DEFAULT NULL,
  `T011_Valor_Total_Pendente` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T011_Id`),
  KEY `T011_IDX_USUARIOS` (`T011_C007_Id`),
  KEY `T011_IDX_EMPRESAS` (`T011_C004_Id`),
  KEY `T011_IDX_TIPO_PRODUTO` (`T011_C008_Id`),
  KEY `T011_FKIndex3` (`T011_D036_Id`),
  KEY `T011_FKIndex4` (`T011_T010_Id`),
  KEY `T011_FKIndex5` (`T011_T009_Id`),
  KEY `T011_FKIndex6` (`T011_D024_Id`),
  KEY `T011_FKIndex7` (`T011_D022_Id`),
  KEY `IDX_T011_Id` (`T011_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T011`
--

LOCK TABLES `T011` WRITE;
/*!40000 ALTER TABLE `T011` DISABLE KEYS */;
/*!40000 ALTER TABLE `T011` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T012`
--

-- DROP TABLE IF EXISTS `T012`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T012` (
  `T012_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T012_T059_Id` int(10) unsigned DEFAULT NULL,
  `T012_D009_Id` int(10) unsigned DEFAULT NULL,
  `T012_T011_Id` int(10) unsigned DEFAULT NULL,
  `T012_C004_Id` int(10) unsigned DEFAULT '0',
  `T012_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T012_Codigo_Produto_Fornecedor` varchar(20) DEFAULT NULL,
  `T012_Descricao_Produto` varchar(50) DEFAULT NULL,
  `T012_Quantidade` decimal(10,3) DEFAULT NULL,
  `T012_Valor_Custo_Unitario` decimal(14,5) DEFAULT '0.00000',
  `T012_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T012_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T012_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T012_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T012_Valor_ICMS` decimal(10,2) DEFAULT '0.00',
  `T012_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T012_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T012_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T012_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T012_Prazo_Entrega` int(10) DEFAULT '0',
  `T012_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T012_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T012_Flag_Travar_Pedido` varchar(1) DEFAULT 'N',
  `T012_Numero` varchar(10) DEFAULT NULL,
  `T012_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T012_Observacao` tinytext NOT NULL,
  `T012_T006_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T012_Id`),
  KEY `T012_IDX_PRODUTO_ESTOQUE` (`T012_D009_Id`),
  KEY `T012_IDX_PEDIDO_COMPRA` (`T012_T011_Id`),
  KEY `T012_IDX_CODIGO_PRODUTO` (`T012_Codigo_Produto`),
  KEY `T012_IDX_CODIGO_FORNECEDOR` (`T012_Codigo_Fornecedor`),
  KEY `T012_IDX_EMPRESAS` (`T012_C004_Id`),
  KEY `T012_IDX_PRECOS_COTACAO` (`T012_T059_Id`),
  KEY `T012_IDX_NUMERO_PEDIDO` (`T012_Numero`),
  KEY `T012_IDX_ID` (`T012_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T012`
--

LOCK TABLES `T012` WRITE;
/*!40000 ALTER TABLE `T012` DISABLE KEYS */;
/*!40000 ALTER TABLE `T012` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T013`
--

-- DROP TABLE IF EXISTS `T013`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T013` (
  `T013_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T013_D014_Id` int(11) DEFAULT '0',
  `T013_C007_Id` int(10) unsigned DEFAULT NULL,
  `T013_C007_Id_Vendedor_Retorno` int(11) DEFAULT '0',
  `T013_T011_Id` int(10) unsigned DEFAULT NULL,
  `T013_D006_Id` int(10) unsigned DEFAULT NULL,
  `T013_D024_Id` int(10) unsigned DEFAULT NULL,
  `T013_D022_Id` int(10) unsigned DEFAULT NULL,
  `T013_T085_Id` int(10) unsigned DEFAULT NULL,
  `T013_C008_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T013_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T013_D021_Id` int(10) unsigned DEFAULT '0',
  `T013_D031_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T013_D031_Id_Entrada` int(10) unsigned NOT NULL DEFAULT '0',
  `T013_C007_Id_Devolucao` int(10) DEFAULT NULL,
  `T013_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T013_Numero_Nota_Fiscal_Devolucao` varchar(40) DEFAULT NULL,
  `T013_Data_Emissao` date DEFAULT NULL,
  `T013_Data_Entrada` date DEFAULT NULL,
  `T013_Numero_Parcelas` int(2) DEFAULT '0',
  `T013_Parcelas` varchar(30) DEFAULT NULL,
  `T013_Codigo_CFOP` decimal(5,3) DEFAULT '0.000',
  `T013_Numero_Controle` int(6) DEFAULT '0',
  `T013_Nome_Transportadora` varchar(30) DEFAULT NULL,
  `T013_Tipo_Transporte` varchar(10) DEFAULT NULL,
  `T013_Valor_Frete` decimal(10,2) DEFAULT '0.00',
  `T013_Flag_Frete` int(1) DEFAULT '0',
  `T013_Observacao_1` tinytext,
  `T013_Observacao_2` varchar(60) DEFAULT NULL,
  `T013_Observacao_3` varchar(60) DEFAULT NULL,
  `T013_Mao_Obra_1` text,
  `T013_Mao_Obra_2` varchar(60) DEFAULT NULL,
  `T013_Valor_Mao_Obra` decimal(10,2) DEFAULT '0.00',
  `T013_Flag_Mao_Obra` varchar(10) DEFAULT NULL,
  `T013_Valor_Total` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_Total_Produtos` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_Total_Custo_Final` decimal(19,2) DEFAULT '0.00',
  `T013_Numero_Conhecimento` varchar(20) DEFAULT NULL,
  `T013_Valor_Seguro` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_Despesas_Acessorias` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_Encargos` decimal(12,6) DEFAULT '0.000000',
  `T013_Valor_Desconto_Produtos` decimal(10,2) DEFAULT '0.00',
  `T013_Valor_Desconto` decimal(12,6) DEFAULT '0.000000',
  `T013_Numero_Ordem_Compra` int(10) DEFAULT '0',
  `T013_Codigo_Natureza` int(4) DEFAULT '0',
  `T013_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T013_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T013_Valor_Total_Base_ICMS` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_Total_Base_ICMS_Diferido` decimal(14,2) DEFAULT '0.00',
  `T013_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T013_Valor_Total_ICMS` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_Total_Base_ICMS_Substituicao` decimal(14,2) DEFAULT '0.00',
  `T013_Valor_ICMS_Substituicao` decimal(14,2) DEFAULT '0.00',
  `T013_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T013_Valor_Total_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T013_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T013_Valor_Total_IPI` decimal(10,2) DEFAULT '0.00',
  `T013_Aliquota_ISS` decimal(5,2) DEFAULT '0.00',
  `T013_Valor_ISS` decimal(10,2) DEFAULT '0.00',
  `T013_Prazo_Pagamento_1` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_2` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_3` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_4` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_5` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_6` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_7` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_8` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_9` int(10) DEFAULT '0',
  `T013_Prazo_Pagamento_10` int(10) DEFAULT '0',
  `T013_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T013_Flag_Travar_Nota` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_ICMS_Frete` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_ICMS_Seguro` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_ICMS_Despesas_Acessorias` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_ICMS_IPI` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_IPI_Frete` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_IPI_Seguro` varchar(1) DEFAULT 'N',
  `T013_Flag_Calcular_IPI_Despesas_Acessorias` varchar(1) DEFAULT 'N',
  `T013_Flag_Arredondar_Valor_IPI` varchar(1) DEFAULT 'N',
  `T013_Flag_OK_Fiscal` int(1) DEFAULT '2',
  `T013_Flag_Devolucao` varchar(1) DEFAULT 'N',
  `T013_Casas_Decimais_Valor_ICMS` int(1) DEFAULT '2',
  `T013_Numero_Dias_Entrada` int(3) DEFAULT '0',
  `T013_Valor_Entrada` decimal(10,2) DEFAULT '0.00',
  `T013_Numero_Dias` int(3) DEFAULT '0',
  `T013_Valor_Juros` decimal(10,2) DEFAULT '0.00',
  `T013_Observacao_Cancelamento` text,
  `T013_IPV` decimal(10,4) DEFAULT NULL,
  `T013_Percentual_Comissao_Representante` decimal(10,2) DEFAULT NULL,
  `T013_IPV_sem_icms` decimal(10,3) DEFAULT NULL,
  `T013_Valor_Dolar` decimal(12,4) DEFAULT '0.0000',
  `T013_Flag_Classificacao` varchar(15) DEFAULT NULL,
  `T013_Codigo_Sistema_Antigo` varchar(20) NOT NULL,
  `T013_Serie` varchar(10) NOT NULL,
  `T013_Tipo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`T013_Id`),
  KEY `T013_Index_Natureza_Operacao` (`T013_D006_Id`),
  KEY `T013_Index_Numero_Nota_Fiscal` (`T013_Numero_Nota_Fiscal`),
  KEY `T013_Index_Codigo_Fornecedor` (`T013_Codigo_Fornecedor`),
  KEY `T013_FKIndex3` (`T013_T011_Id`),
  KEY `T013_Index_Tipo_Produto` (`T013_C008_Id`),
  KEY `T013_Index_Usuarios` (`T013_C007_Id`),
  KEY `T013_Index_Tipo_Portador` (`T013_D031_Id`),
  KEY `T013_Index_Tipo_Portador_Entrada` (`T013_D031_Id_Entrada`),
  KEY `T013_Index_Cliente` (`T013_D024_Id`),
  KEY `T013_Index_Transportadora` (`T013_D022_Id`),
  KEY `T013_Index_Conhecimento` (`T013_T085_Id`),
  KEY `T013_IDX_ID` (`T013_Id`),
  KEY `T013_IDX_Cancelada` (`T013_Flag_Cancelada`),
  KEY `IDX_C007_DEV` (`T013_C007_Id_Vendedor_Retorno`),
  KEY `T013_Codigo_Sistema_Antigo` (`T013_Codigo_Sistema_Antigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T013`
--

LOCK TABLES `T013` WRITE;
/*!40000 ALTER TABLE `T013` DISABLE KEYS */;
/*!40000 ALTER TABLE `T013` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T014`
--

-- DROP TABLE IF EXISTS `T014`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T014` (
  `T014_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T014_D006_Id` int(10) unsigned DEFAULT NULL,
  `T014_T012_Id` int(10) unsigned DEFAULT NULL,
  `T014_D005_Id` int(10) unsigned DEFAULT NULL,
  `T014_T013_Id` int(10) unsigned DEFAULT NULL,
  `T014_T014_Id` int(10) unsigned DEFAULT NULL,
  `T014_D009_Id` int(10) unsigned DEFAULT NULL,
  `T014_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T014_T008_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T014_D003_Id` int(10) DEFAULT NULL,
  `T014_Codigo_CFOP` decimal(4,2) DEFAULT '0.00',
  `T014_Data_Emissao` date DEFAULT NULL,
  `T014_Flag_Nacional_Importado` varchar(1) DEFAULT NULL,
  `T014_Descricao_Produto` varchar(30) DEFAULT NULL,
  `T014_Quantidade` decimal(10,3) DEFAULT NULL,
  `T014_Valor_Custo_Unitario` decimal(14,5) DEFAULT '0.00000',
  `T014_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T014_Valor_Custo_Final_Unitario` decimal(20,6) DEFAULT '0.000000',
  `T014_Valor_Total_Custo_Final` decimal(14,5) DEFAULT '0.00000',
  `T014_Valor_Desconto_Unitario` decimal(10,2) DEFAULT '0.00',
  `T014_Valor_Total_Desconto` decimal(10,5) DEFAULT '0.00000',
  `T014_Valor_Frete` decimal(10,3) DEFAULT '0.000',
  `T014_Valor_Seguro` decimal(10,2) DEFAULT '0.00',
  `T014_Valor_Despesas_Acessorias` decimal(10,2) DEFAULT '0.00',
  `T014_Valor_Unitario_Desp_Nota_Origem` decimal(20,5) DEFAULT '0.00000',
  `T014_Valor_Total_Desp_Nota_Origem` decimal(20,2) DEFAULT '0.00',
  `T014_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T014_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T014_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T014_Valor_ICMS` decimal(12,5) DEFAULT '0.00000',
  `T014_Valor_ICMS_Substituicao` decimal(10,2) DEFAULT '0.00',
  `T014_Valor_ICMS_Substituicao_Unitario` decimal(20,5) DEFAULT '0.00000',
  `T014_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T014_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T014_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T014_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T014_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T014_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T014_Codigo_Substituto` varchar(20) DEFAULT NULL,
  `T014_Codigo_Produto_Fornecedor` varchar(20) DEFAULT NULL,
  `T014_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T014_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T014_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T014_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T014_Flag_Excluir` int(1) DEFAULT '2',
  `T014_Flag_Classificacao` int(2) DEFAULT NULL,
  `T014_Unidade` varchar(5) NOT NULL,
  `T014_D037_Id` int(10) unsigned DEFAULT NULL,
  `T014_T129_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T014_Id`),
  KEY `T014_Index_Estoque` (`T014_D009_Id`),
  KEY `T014_Index_Classificacao_Fiscal` (`T014_D005_Id`),
  KEY `T014_Index_Quantidade` (`T014_Quantidade`),
  KEY `T014_Index_Valor_Unitario` (`T014_Valor_Custo_Unitario`),
  KEY `T014_Numero_Nota_Fiscal` (`T014_Numero_Nota_Fiscal`),
  KEY `T014_Index_Codigo_Fornecedor` (`T014_Codigo_Fornecedor`),
  KEY `T014_FKIndex5` (`T014_T013_Id`),
  KEY `T014_FKIndex4` (`T014_T012_Id`),
  KEY `T014_FKIndex6` (`T014_T008_Id`),
  KEY `T014_Index_Item_Devolvido_Entrada` (`T014_T014_Id`),
  KEY `T014_D003_Id` (`T014_D003_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T014`
--

LOCK TABLES `T014` WRITE;
/*!40000 ALTER TABLE `T014` DISABLE KEYS */;
/*!40000 ALTER TABLE `T014` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T015`
--

-- DROP TABLE IF EXISTS `T015`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T015` (
  `T015_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T015_D073_Id` int(10) unsigned DEFAULT NULL,
  `T015_T084_Id` int(10) unsigned DEFAULT NULL,
  `T015_T056_Id` int(10) unsigned DEFAULT NULL,
  `T015_T060_Id` int(10) unsigned DEFAULT NULL,
  `T015_T045_Id` int(10) unsigned DEFAULT NULL,
  `T015_D014_Id` int(10) unsigned DEFAULT NULL,
  `T015_T018_Id` int(10) unsigned DEFAULT NULL,
  `T015_D031_Id` int(10) unsigned DEFAULT NULL,
  `T015_D024_Id` int(10) unsigned DEFAULT NULL,
  `T015_D021_Id` int(10) unsigned DEFAULT NULL,
  `T015_D007_Id` int(10) unsigned DEFAULT NULL,
  `T015_T007_Id` int(10) unsigned DEFAULT NULL,
  `T015_T013_Id` int(10) unsigned DEFAULT NULL,
  `T015_T054_Id` int(10) unsigned DEFAULT NULL,
  `T015_D044_Id` int(10) unsigned DEFAULT NULL,
  `T015_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T015_T052_Id` int(10) unsigned DEFAULT NULL,
  `T015_T061_Id` int(10) DEFAULT NULL,
  `T015_T076_Id` int(10) DEFAULT NULL,
  `T015_Flag_Conta` varchar(1) DEFAULT NULL,
  `T015_Nome_Cliente` varchar(50) DEFAULT NULL,
  `T015_Numero_Duplicata` varchar(15) DEFAULT NULL,
  `T015_Numero_Documento` varchar(15) DEFAULT NULL,
  `T015_Data_Emissao` date DEFAULT NULL,
  `T015_Data_Vencimento` date DEFAULT NULL,
  `T015_Data_Pagamento` date DEFAULT NULL,
  `T015_Valor_Duplicata` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T015_Valor_Juros` decimal(12,2) NOT NULL DEFAULT '0.00',
  `T015_Valor_Desconto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `T015_Valor_Total` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T015_Observacao` text,
  `T015_Numero_Agencia` int(4) NOT NULL DEFAULT '0',
  `T015_Numero_Conta` varchar(10) DEFAULT NULL,
  `T015_Numero_Cheque` int(10) NOT NULL DEFAULT '0',
  `T015_Observacao_1` varchar(40) DEFAULT NULL,
  `T015_Observacao_2` varchar(40) DEFAULT NULL,
  `T015_Numero_Controle` int(6) NOT NULL DEFAULT '0',
  `T015_Data_Lancamento` date DEFAULT NULL,
  `T015_Hora_Lancamento` time DEFAULT NULL,
  `T015_Valor_Base_Comissao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T015_Percentual_Comissao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T015_Valor_Comissao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T015_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T015_Data_Vencimento_1` date DEFAULT NULL,
  `T015_Data_Vencimento_2` date DEFAULT NULL,
  `T015_Data_Vencimento_3` date DEFAULT NULL,
  `T015_Data_Vencimento_4` date DEFAULT NULL,
  `T015_Data_Vencimento_5` date DEFAULT NULL,
  `T015_Agrupamento` varchar(20) DEFAULT NULL,
  `T015_Nosso_Numero` varchar(12) DEFAULT NULL,
  `T015_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T015_Codigo_Revenda` varchar(6) DEFAULT NULL,
  `T015_Codigo_Conta_Banco` int(3) NOT NULL DEFAULT '0',
  `T015_Conta` varchar(16) DEFAULT NULL,
  `T015_SubConta` varchar(30) DEFAULT NULL,
  `T015_Flag_Deb_Cred` varchar(1) DEFAULT NULL,
  `T015_Flag_Tipo` int(1) NOT NULL DEFAULT '0',
  `T015_Flag_Despesas` int(1) NOT NULL DEFAULT '1',
  `T015_Obs_Mudanca_Vencto` text,
  `T015_Obs_Divisao_Titulo` text,
  `T015_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T015_Flag_Baixar_Lote` varchar(1) DEFAULT 'N',
  `T015_Valor_Cambio_Moeda` decimal(10,4) DEFAULT '0.0000',
  `T015_Data_Cambio_Moeda` date DEFAULT NULL,
  `T015_Valor_Duplicata_Moeda` decimal(14,2) DEFAULT '0.00',
  `T015_Numero_Parcela` int(3) DEFAULT '0',
  `T015_Flag_Boleto` enum('S','N') DEFAULT 'N',
  `T015_Valor_Pago` decimal(10,2) DEFAULT '0.00',
  `T015_Data_Baixa` date DEFAULT '0000-00-00',
  PRIMARY KEY (`T015_Id`),
  KEY `T015_Index_Id` (`T015_Id`),
  KEY `T015_Index_Baixa_Lote` (`T015_T045_Id`),
  KEY `T015_Index_Contas_Bancarias` (`T015_D007_Id`),
  KEY `T015_Index_Revenda` (`T015_D021_Id`),
  KEY `T015_Index_Cliente` (`T015_D024_Id`),
  KEY `T015_Index_Nota_Compra_Entrada` (`T015_T007_Id`),
  KEY `T015_Index_Nota_Compra_Saida` (`T015_T013_Id`),
  KEY `T015_Index_Plano_Contas` (`T015_D014_Id`),
  KEY `T015_Index_Projecoes` (`T015_T018_Id`),
  KEY `T015_Index_Portador` (`T015_D031_Id`),
  KEY `T015_Index_Numero_Nota_Fiscal` (`T015_Numero_Nota_Fiscal`),
  KEY `T015_Index_C004_Id` (`T015_C004_Id`),
  KEY `T015_Index_Codigo_Fornecedor` (`T015_Codigo_Fornecedor`),
  KEY `T015_Index_Conta` (`T015_Conta`),
  KEY `T015_Index_SubCodigo` (`T015_SubConta`),
  KEY `T015_Index_Data_Vencimento` (`T015_Data_Vencimento`),
  KEY `T015_Index_Data_Pagamento` (`T015_Data_Pagamento`),
  KEY `T015_Index_Importacao` (`T015_T054_Id`),
  KEY `T015_Index_Moeda` (`T015_D044_Id`),
  KEY `T015_Index_Cambio` (`T015_T060_Id`),
  KEY `T015_Index_Despesas_Importacao` (`T015_T056_Id`),
  KEY `T015_Index_Conhecimentos` (`T015_T084_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T015`
--

LOCK TABLES `T015` WRITE;
/*!40000 ALTER TABLE `T015` DISABLE KEYS */;
/*!40000 ALTER TABLE `T015` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T016`
--

-- DROP TABLE IF EXISTS `T016`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T016` (
  `T016_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T016_Numero` varchar(5) DEFAULT NULL,
  `T016_Data_Inicio` date DEFAULT NULL,
  `T016_Data_Final` date DEFAULT NULL,
  `T016_Observacao` text,
  PRIMARY KEY (`T016_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T016`
--

LOCK TABLES `T016` WRITE;
/*!40000 ALTER TABLE `T016` DISABLE KEYS */;
/*!40000 ALTER TABLE `T016` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T017`
--

-- DROP TABLE IF EXISTS `T017`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T017` (
  `T017_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T017_D039_Id` int(10) unsigned DEFAULT NULL,
  `T017_D024_Id` int(10) unsigned DEFAULT NULL,
  `T017_Codigo` varchar(6) DEFAULT NULL,
  `T017_Cod_Clas` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`T017_Id`),
  KEY `T017_FKIndex1` (`T017_D039_Id`),
  KEY `T017_FKIndex2` (`T017_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T017`
--

LOCK TABLES `T017` WRITE;
/*!40000 ALTER TABLE `T017` DISABLE KEYS */;
/*!40000 ALTER TABLE `T017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T018`
--

-- DROP TABLE IF EXISTS `T018`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T018` (
  `T018_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T018_C005_Id` int(10) unsigned DEFAULT NULL,
  `T018_D014_Id` int(10) unsigned DEFAULT NULL,
  `T018_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T018_Conta` varchar(16) DEFAULT NULL,
  `T018_SubConta` varchar(30) DEFAULT NULL,
  `T018_Mes` varchar(2) DEFAULT NULL,
  `T018_Ano` varchar(4) DEFAULT NULL,
  `T018_Data_Vencimento` date DEFAULT NULL,
  `T018_Valor` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T018_Percentual` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `T018_Periodicidade` int(1) NOT NULL DEFAULT '1',
  `T018_Funcao` varchar(30) DEFAULT NULL,
  `T018_Flag_Confirmado` varchar(1) DEFAULT NULL,
  `T018_Valor_Receber` decimal(14,2) DEFAULT '0.00',
  `T018_Valor_Pagar` decimal(14,2) DEFAULT '0.00',
  `T018_Valor_Saldo` decimal(14,2) DEFAULT '0.00',
  `T018_Valor_Pago` decimal(10,2) DEFAULT NULL,
  `T018_Valor_Recebido` decimal(10,2) DEFAULT NULL,
  `T018_Valor_Saldo_Projecao` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T018_Id`),
  KEY `T018_Index_Plano_Contas` (`T018_D014_Id`),
  KEY `T018_Index_C004_Id` (`T018_C004_Id`),
  KEY `T018_Index_C005_Id` (`T018_C005_Id`),
  KEY `T018_Index_Mes` (`T018_Mes`),
  KEY `T018_Index_Ano` (`T018_Ano`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T018`
--

LOCK TABLES `T018` WRITE;
/*!40000 ALTER TABLE `T018` DISABLE KEYS */;
/*!40000 ALTER TABLE `T018` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T019`
--

-- DROP TABLE IF EXISTS `T019`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T019` (
  `T019_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T019_D031_Id` int(10) unsigned DEFAULT NULL,
  `T019_T005_Id` int(10) unsigned DEFAULT NULL,
  `T019_D027_Id` int(10) unsigned DEFAULT NULL,
  `T019_T007_Id` int(10) unsigned DEFAULT NULL,
  `T019_D024_Id` int(10) unsigned DEFAULT NULL,
  `T019_D014_Id` int(10) unsigned DEFAULT NULL,
  `T019_T002_Id` int(10) unsigned DEFAULT NULL,
  `T019_T015_Id` int(10) unsigned DEFAULT NULL,
  `T019_D007_Id` int(10) unsigned DEFAULT NULL,
  `T019_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T019_T018_Id` int(10) DEFAULT NULL,
  `T019_Numero_Agencia` int(4) NOT NULL DEFAULT '0',
  `T019_Numero_Conta` varchar(10) DEFAULT NULL,
  `T019_Numero_Cheque` int(10) NOT NULL DEFAULT '0',
  `T019_Data_Lancamento` date DEFAULT NULL,
  `T019_Referente` varchar(30) DEFAULT NULL,
  `T019_Conta` varchar(40) DEFAULT NULL,
  `T019_SubConta` varchar(30) DEFAULT NULL,
  `T019_Valor_Lancamento` decimal(14,2) DEFAULT NULL,
  `T019_Codigo_Conta_Banco` int(3) NOT NULL DEFAULT '0',
  `T019_Conciliado` int(1) NOT NULL DEFAULT '1',
  `T019_Obs_Estorno` text,
  `T019_Complemento` varchar(50) DEFAULT NULL,
  `T019_Flag_Deb_Crd` varchar(1) DEFAULT NULL,
  `T019_Saldo` decimal(10,2) DEFAULT '0.00',
  `T019_Flag_Excluido` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T019_Id`),
  KEY `T019_Index_Contas_Bancarias` (`T019_D007_Id`),
  KEY `T019_Index_C004_Id` (`T019_C004_Id`),
  KEY `T019_Index_D024` (`T019_D024_Id`),
  KEY `T019_Index_D014` (`T019_D014_Id`),
  KEY `T019_Index_Contas_Pagar` (`T019_T015_Id`),
  KEY `T019_Index_Contas_Receber` (`T019_T002_Id`),
  KEY `T019_Data_Lancamento` (`T019_Data_Lancamento`),
  KEY `IDX_T019_T005_Id` (`T019_T005_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T019`
--

LOCK TABLES `T019` WRITE;
/*!40000 ALTER TABLE `T019` DISABLE KEYS */;
/*!40000 ALTER TABLE `T019` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T020`
--

-- DROP TABLE IF EXISTS `T020`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T020` (
  `T020_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T020_T002_Id` int(10) unsigned DEFAULT NULL,
  `T020_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T020_Conta` varchar(16) DEFAULT NULL,
  `T020_SubConta` varchar(30) DEFAULT NULL,
  `T020_Codigo_Cliente` varchar(50) DEFAULT NULL,
  `T020_Numero_Duplicata` varchar(15) DEFAULT NULL,
  `T020_Data_Lancamento` date DEFAULT NULL,
  `T020_Hora_Lancamento` time DEFAULT NULL,
  `T020_Historico` varchar(30) DEFAULT NULL,
  `T020_Valor` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T020_Flag_Deb_Cred` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`T020_Id`),
  KEY `T020_Index_Conta` (`T020_Conta`),
  KEY `T020_Index_SubConta` (`T020_SubConta`),
  KEY `T020_Index_Codigo_Cliente` (`T020_Codigo_Cliente`),
  KEY `T020_Index_Numero_Duplicata` (`T020_Numero_Duplicata`),
  KEY `T020_Index_Contas_Receber` (`T020_T002_Id`),
  KEY `T020_Index_C004_Id` (`T020_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T020`
--

LOCK TABLES `T020` WRITE;
/*!40000 ALTER TABLE `T020` DISABLE KEYS */;
/*!40000 ALTER TABLE `T020` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T021`
--

-- DROP TABLE IF EXISTS `T021`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T021` (
  `T021_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T021_C004_Id` int(10) unsigned DEFAULT NULL,
  `T021_Codigo_Maquina` int(3) NOT NULL DEFAULT '0',
  `T021_Nome` varchar(30) DEFAULT NULL,
  `T021_Setor` varchar(1) DEFAULT NULL,
  `T021_Planilha` varchar(10) DEFAULT NULL,
  `T021_Quantidade` int(1) NOT NULL DEFAULT '0',
  `T021_Flag_Critica` varchar(1) DEFAULT NULL,
  `T021_Flag_Inativa` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`T021_Id`),
  KEY `T021_Index_Codigo_Maquina` (`T021_Codigo_Maquina`),
  KEY `T021_Index_C004_Id` (`T021_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T021`
--

LOCK TABLES `T021` WRITE;
/*!40000 ALTER TABLE `T021` DISABLE KEYS */;
/*!40000 ALTER TABLE `T021` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T022`
--

-- DROP TABLE IF EXISTS `T022`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T022` (
  `T022_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T022_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T022_Codigo_Operador` int(6) NOT NULL DEFAULT '0',
  `T022_Nome` varchar(30) DEFAULT NULL,
  `T022_Setor` varchar(1) DEFAULT NULL,
  `T022_Ordem` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`T022_Id`),
  KEY `T022_Index_Codigo_Operador` (`T022_Codigo_Operador`),
  KEY `T022_Index_C004_Id` (`T022_C004_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T022`
--

LOCK TABLES `T022` WRITE;
/*!40000 ALTER TABLE `T022` DISABLE KEYS */;
/*!40000 ALTER TABLE `T022` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T023`
--

-- DROP TABLE IF EXISTS `T023`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T023` (
  `T023_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T023_D001_Id` int(10) unsigned DEFAULT NULL,
  `T023_T022_Id` int(10) unsigned DEFAULT NULL,
  `T023_T021_Id` int(10) unsigned DEFAULT NULL,
  `T023_C004_Id` int(10) unsigned DEFAULT NULL,
  `T023_Codigo_Maquina` int(10) NOT NULL DEFAULT '0',
  `T023_Lote` int(4) NOT NULL DEFAULT '0',
  `T023_Codigo_Produto` varchar(30) NOT NULL DEFAULT '0',
  `T023_Letra` varchar(1) DEFAULT NULL,
  `T023_Quantidade` int(10) NOT NULL DEFAULT '0',
  `T023_Codigo_Operador` int(10) DEFAULT NULL,
  `T023_Ocorrencia` varchar(5) DEFAULT NULL,
  `T023_Data` date DEFAULT NULL,
  `T023_Quantidade_Sucatada` int(10) NOT NULL DEFAULT '0',
  `T023_Flag_Servico` int(1) NOT NULL DEFAULT '0',
  `T023_Numero_Ficha` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`T023_Id`),
  KEY `T023_Index_Equipe` (`T023_T021_Id`),
  KEY `T023_Index_Maquina` (`T023_T022_Id`),
  KEY `T023_Index_Produtos` (`T023_D001_Id`),
  KEY `T023_Index_C004_Id` (`T023_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T023`
--

LOCK TABLES `T023` WRITE;
/*!40000 ALTER TABLE `T023` DISABLE KEYS */;
/*!40000 ALTER TABLE `T023` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T025`
--

-- DROP TABLE IF EXISTS `T025`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T025` (
  `T025_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T025_D005_Id` int(10) unsigned DEFAULT NULL,
  `T025_D002_Id` int(10) unsigned DEFAULT NULL,
  `T025_D015_Id` int(10) unsigned DEFAULT NULL,
  `T025_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T025_Codigo_Descricao` varchar(5) DEFAULT NULL,
  `T025_Codigo_Grupo` varchar(4) DEFAULT NULL,
  `T025_D024_Id` int(10) DEFAULT NULL,
  PRIMARY KEY (`T025_Id`),
  KEY `T025_Index_Grupo` (`T025_D015_Id`),
  KEY `T025_Index_Descricao` (`T025_D002_Id`),
  KEY `T025_Index_Classificacao` (`T025_D005_Id`),
  KEY `T025_Index_Codigo_Grupo` (`T025_Codigo_Grupo`),
  KEY `T025_Index_Codigo_Descricao` (`T025_Codigo_Descricao`),
  KEY `T025_Index_Classificacao_Fiscal` (`T025_Classificacao_Fiscal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T025`
--

LOCK TABLES `T025` WRITE;
/*!40000 ALTER TABLE `T025` DISABLE KEYS */;
/*!40000 ALTER TABLE `T025` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T026`
--

-- DROP TABLE IF EXISTS `T026`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T026` (
  `T026_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T026_D024_Id` int(10) unsigned DEFAULT NULL,
  `T026_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T026_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T026_Cobranca` text,
  `T026_Nome_Cliente` varchar(40) DEFAULT NULL,
  `T026_Data_Atraso` date DEFAULT NULL,
  `T026_Valor_Atraso` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T026_Id`),
  KEY `T026_IDX_CLIENTE` (`T026_D024_Id`),
  KEY `T026_IDX_EMPRESAS` (`T026_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T026`
--

LOCK TABLES `T026` WRITE;
/*!40000 ALTER TABLE `T026` DISABLE KEYS */;
/*!40000 ALTER TABLE `T026` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T027`
--

-- DROP TABLE IF EXISTS `T027`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T027` (
  `T027_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T027_C004_Id` int(10) unsigned DEFAULT NULL,
  `T027_Numero` varchar(10) DEFAULT NULL,
  `T027_Data` date DEFAULT NULL,
  `T027_Observacao` text,
  PRIMARY KEY (`T027_Id`),
  KEY `T027_IDX_EMPRESAS` (`T027_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T027`
--

LOCK TABLES `T027` WRITE;
/*!40000 ALTER TABLE `T027` DISABLE KEYS */;
/*!40000 ALTER TABLE `T027` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T028`
--

-- DROP TABLE IF EXISTS `T028`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T028` (
  `T028_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T028_D004_Id` int(10) unsigned DEFAULT NULL,
  `T028_D009_Id` int(10) unsigned DEFAULT NULL,
  `T028_D023_Id` int(10) unsigned DEFAULT NULL,
  `T028_T027_Id` int(10) unsigned DEFAULT NULL,
  `T028_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T028_Quantidade_Contada` int(10) NOT NULL DEFAULT '0',
  `T028_Quantidade_Estoque_Atual` int(10) NOT NULL DEFAULT '0',
  `T028_Flag_Cancelado` varchar(1) DEFAULT NULL,
  `T028_Divergencia_Estoque` varchar(10) DEFAULT NULL,
  `T028_Valor_Custo_Unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T028_Numero` varchar(10) DEFAULT NULL,
  `T028_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T028_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T028_Codigo_Local` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`T028_Id`),
  KEY `T028_IDX_BALANCO` (`T028_T027_Id`),
  KEY `T028_IDX_EMPRESAS` (`T028_C004_Id`),
  KEY `T028_IDX_PRODUTOS_ESTOQUE` (`T028_D009_Id`),
  KEY `T028_IDX_FORNECEDOR` (`T028_D023_Id`),
  KEY `T028_IDX_CODIGO_PRODUTO_ESTOQUE` (`T028_Codigo_Produto`),
  KEY `T028_IDX_CODIGO_FORNECEDOR` (`T028_Codigo_Fornecedor`),
  KEY `T028_IDX_CODIGO_BALANCO` (`T028_Numero`),
  KEY `T028_IDX_LOCAL` (`T028_D004_Id`),
  KEY `T028_IDX_CODIGO_LOCAL` (`T028_Codigo_Local`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T028`
--

LOCK TABLES `T028` WRITE;
/*!40000 ALTER TABLE `T028` DISABLE KEYS */;
/*!40000 ALTER TABLE `T028` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T029`
--

-- DROP TABLE IF EXISTS `T029`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T029` (
  `T029_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T029_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T029_C007_Id` int(10) NOT NULL,
  `T029_Numero` int(10) NOT NULL DEFAULT '0',
  `T029_Data` date DEFAULT NULL,
  `T029_Descricao` text,
  `T029_Flag_Ok` varchar(1) DEFAULT NULL,
  `T029_Valor_Debitos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T029_Valor_Creditos` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`T029_Id`),
  KEY `T029_IDX_EMPRESAS` (`T029_C004_Id`),
  KEY `T029_IDX_NUMERO` (`T029_Numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T029`
--

LOCK TABLES `T029` WRITE;
/*!40000 ALTER TABLE `T029` DISABLE KEYS */;
/*!40000 ALTER TABLE `T029` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T030`
--

-- DROP TABLE IF EXISTS `T030`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T030` (
  `T030_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T030_D009_Id` int(10) unsigned DEFAULT NULL,
  `T030_T029_Id` int(10) unsigned DEFAULT NULL,
  `T030_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T030_C007_Id` int(10) NOT NULL,
  `T030_Quantidade` decimal(10,2) DEFAULT NULL,
  `T030_Flag_Entrada_Saida` varchar(1) DEFAULT NULL,
  `T030_Valor_Custo_Unitario` decimal(14,5) DEFAULT '0.00000',
  `T030_Flag_Alterar_Custo` varchar(1) NOT NULL DEFAULT 'N',
  `T030_Numero` int(10) NOT NULL DEFAULT '0',
  `T030_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T030_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T030_Valor_Custo_Unitario_Origem` decimal(12,2) DEFAULT '0.00',
  `T030_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T030_Observacao` tinytext NOT NULL,
  `T030_Data_Inclusao` datetime NOT NULL,
  PRIMARY KEY (`T030_Id`),
  KEY `T030_IDX_DIVERGENCIA_ESTOQUE` (`T030_T029_Id`),
  KEY `T030_IDX_EMPRESAS` (`T030_C004_Id`),
  KEY `T030_IDX_CODIGO_PRODUTO` (`T030_Codigo_Produto`),
  KEY `T030_IDX_CODIGO_FORNECEDOR` (`T030_Codigo_Fornecedor`),
  KEY `T030_IDX_PRODUTO_ESTOQUE` (`T030_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T030`
--

LOCK TABLES `T030` WRITE;
/*!40000 ALTER TABLE `T030` DISABLE KEYS */;
/*!40000 ALTER TABLE `T030` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T031`
--

-- DROP TABLE IF EXISTS `T031`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T031` (
  `T031_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T031_D009_Id` int(10) unsigned DEFAULT NULL,
  `T031_D024_Id` int(10) unsigned DEFAULT NULL,
  `T031_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T031_Data_Lancamento` date DEFAULT NULL,
  `T031_Descricao_Operacao` varchar(20) DEFAULT NULL,
  `T031_Quantidade` int(6) NOT NULL DEFAULT '0',
  `T031_Flag_Operacao` varchar(1) DEFAULT NULL,
  `T031_Valor_Preco_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T031_Observacao` varchar(40) DEFAULT NULL,
  `T031_Valor_Custo_Unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T031_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T031_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T031_Codigo_Cliente` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`T031_Id`),
  KEY `T031_IDX_CLIENTE` (`T031_D024_Id`),
  KEY `T031_IDX_PRODUTO_ESTOQUE` (`T031_D009_Id`),
  KEY `T031_IDX_CODIGO_FORNECEDOR` (`T031_Codigo_Fornecedor`),
  KEY `T031_IDX_CODIGO_PRODUTO` (`T031_Codigo_Produto`),
  KEY `T031_IDX_CODIGO_CLIENTE` (`T031_Codigo_Cliente`),
  KEY `T031_IDX_EMPRESAS` (`T031_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T031`
--

LOCK TABLES `T031` WRITE;
/*!40000 ALTER TABLE `T031` DISABLE KEYS */;
/*!40000 ALTER TABLE `T031` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T032`
--

-- DROP TABLE IF EXISTS `T032`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T032` (
  `T032_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T032_D023_Id` int(10) unsigned DEFAULT NULL,
  `T032_C007_Id` int(10) unsigned DEFAULT NULL,
  `T032_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T032_Numero` int(10) NOT NULL DEFAULT '0',
  `T032_Data_Emissao` date DEFAULT NULL,
  `T032_Nome_Comprador` varchar(30) DEFAULT NULL,
  `T032_Numero_Pedido` int(10) NOT NULL DEFAULT '0',
  `T032_Data_Confirmacao` date DEFAULT NULL,
  `T032_Codigo_Usuario` int(4) NOT NULL DEFAULT '0',
  `T032_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T032_Codigo_Fornecedor_2` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`T032_Id`),
  KEY `T032_IDX_USUARIO` (`T032_C007_Id`),
  KEY `T032_IDX_FORNECEDOR` (`T032_D023_Id`),
  KEY `T032_IDX_EMPRESAS` (`T032_C004_Id`),
  KEY `T032_IDX_NUMERO` (`T032_Numero`),
  KEY `T032_IDX_CODIGO_USUARIO` (`T032_Codigo_Usuario`),
  KEY `T032_IDX_CODIGO_FORNECEDOR` (`T032_Codigo_Fornecedor`),
  KEY `T032_IDX_CODIGO_FORNECEDOR_2` (`T032_Codigo_Fornecedor_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T032`
--

LOCK TABLES `T032` WRITE;
/*!40000 ALTER TABLE `T032` DISABLE KEYS */;
/*!40000 ALTER TABLE `T032` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T033`
--

-- DROP TABLE IF EXISTS `T033`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T033` (
  `T033_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T033_T015_Id` int(10) unsigned DEFAULT NULL,
  `T033_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T033_Conta` varchar(16) DEFAULT NULL,
  `T033_SubConta` varchar(30) DEFAULT NULL,
  `T033_Codigo_Fornecedor` varchar(50) DEFAULT NULL,
  `T033_Numero_Duplicata` varchar(15) DEFAULT NULL,
  `T033_Data_Lancamento` date DEFAULT NULL,
  `T033_Hora_Lancamento` time DEFAULT NULL,
  `T033_Historico` varchar(30) DEFAULT NULL,
  `T033_Valor` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T033_Flag_Deb_Cred` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`T033_Id`),
  KEY `T033_Index_Conta` (`T033_Conta`),
  KEY `T033_Index_SubConta` (`T033_SubConta`),
  KEY `T033_Index_Codigo_Fornecedor` (`T033_Codigo_Fornecedor`),
  KEY `T033_Index_Numero_Duplicata` (`T033_Numero_Duplicata`),
  KEY `T033_Index_Contas_Pagar` (`T033_T015_Id`),
  KEY `T033_Index_C004_Id` (`T033_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T033`
--

LOCK TABLES `T033` WRITE;
/*!40000 ALTER TABLE `T033` DISABLE KEYS */;
/*!40000 ALTER TABLE `T033` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T034`
--

-- DROP TABLE IF EXISTS `T034`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T034` (
  `T034_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T034_T018_Id` int(10) unsigned DEFAULT NULL,
  `T034_D014_Id` int(10) unsigned DEFAULT NULL,
  `T034_C004_Id` int(10) unsigned DEFAULT NULL,
  `T034_Conta` varchar(16) DEFAULT NULL,
  `T034_SubConta` varchar(30) DEFAULT NULL,
  `T034_Data` date DEFAULT NULL,
  `T034_Valor` decimal(19,2) DEFAULT NULL,
  `T034_Flag_Debito_Credito` varchar(1) DEFAULT NULL,
  `T034_Historico` varchar(72) DEFAULT NULL,
  PRIMARY KEY (`T034_Id`),
  KEY `T034_IDX_PLANO_CONTAS` (`T034_D014_Id`),
  KEY `T034_IDX_EMPRESAS` (`T034_C004_Id`),
  KEY `T034_FKIndex2` (`T034_T018_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T034`
--

LOCK TABLES `T034` WRITE;
/*!40000 ALTER TABLE `T034` DISABLE KEYS */;
/*!40000 ALTER TABLE `T034` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T035`
--

-- DROP TABLE IF EXISTS `T035`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T035` (
  `T035_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T035_D007_Id` int(10) unsigned DEFAULT NULL,
  `T035_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T035_D062_Id` int(11) DEFAULT NULL,
  `T035_Data_Processamento` datetime DEFAULT NULL,
  `T035_Conteudo_Arquivo` text,
  `T035_Mensagem_Erro` text,
  `T035_Sequencial_Remessa` int(7) DEFAULT NULL,
  PRIMARY KEY (`T035_Id`),
  KEY `T035_FKIndex1` (`T035_D007_Id`),
  KEY `T035_index_D062_Id` (`T035_D062_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T035`
--

LOCK TABLES `T035` WRITE;
/*!40000 ALTER TABLE `T035` DISABLE KEYS */;
/*!40000 ALTER TABLE `T035` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T036`
--

-- DROP TABLE IF EXISTS `T036`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T036` (
  `T036_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T036_T002_Id` int(10) unsigned DEFAULT NULL,
  `T036_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T036_Data_Entrada` date DEFAULT NULL,
  `T036_Data_Credito` date DEFAULT NULL,
  `T036_Data_Processamento` date DEFAULT NULL,
  `T036_Valor_Titulo` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T036_Valor_Abatimento` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T036_Valor_Recebido` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T036_Valor_Juros_Mora` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T036_Valor_Outras_Despesas` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T036_Valor_Tarifa` decimal(19,2) NOT NULL DEFAULT '0.00',
  `T036_Flag_Debito_Credito` varchar(2) DEFAULT NULL,
  `T036_Numero_Banco` varchar(3) DEFAULT NULL,
  `T036_Numero_Titulo` varchar(15) DEFAULT NULL,
  `T036_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T036_Nome_Cliente` varchar(50) DEFAULT NULL,
  `T036_Nosso_Numero` varchar(13) DEFAULT NULL,
  `T036_Codigo_Motivo` varchar(2) DEFAULT NULL,
  `T036_Descricao_Motivo` varchar(50) DEFAULT NULL,
  `T036_Data_Vencimento` date DEFAULT NULL,
  PRIMARY KEY (`T036_Id`),
  KEY `T036_FKIndex1` (`T036_T002_Id`),
  KEY `T036_FKIndex3` (`T036_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T036`
--

LOCK TABLES `T036` WRITE;
/*!40000 ALTER TABLE `T036` DISABLE KEYS */;
/*!40000 ALTER TABLE `T036` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T037`
--

-- DROP TABLE IF EXISTS `T037`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T037` (
  `T037_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T037_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T037_Nome_Arquivo` varchar(20) DEFAULT NULL,
  `T037_Data_Importacao` datetime DEFAULT NULL,
  `T037_Data_Processamento` datetime DEFAULT NULL,
  `T037_HEADER_Prefixo_Agencia` int(4) NOT NULL DEFAULT '0',
  `T037_HEADER_Digito_Verificador_Agencia` varchar(1) DEFAULT NULL,
  `T037_HEADER_Codigo_Cedente` int(8) NOT NULL DEFAULT '0',
  `T037_HEADER_Digito_Verificador_Cedente` varchar(1) DEFAULT NULL,
  `T037_HEADER_Numero_Convenio` int(6) NOT NULL DEFAULT '0',
  `T037_HEADER_Nome_Empresa` varchar(30) DEFAULT NULL,
  `T037_HEADER_Codigo_Banco` varchar(3) DEFAULT NULL,
  `T037_HEADER_Nome_Banco` varchar(15) DEFAULT NULL,
  `T037_HEADER_Data_Gravacao_Arquivo` date DEFAULT NULL,
  `T037_HEADER_Sequencial_Retorno` int(7) NOT NULL DEFAULT '0',
  `T037_HEADER_String` text,
  `T037_TRAILLER_Quantidade_Titulos` int(8) NOT NULL DEFAULT '0',
  `T037_TRAILLER_Valor_Total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `T037_TRAILLER_Numero_Aviso` int(8) NOT NULL DEFAULT '0',
  `T037_TRAILLER_String` text,
  PRIMARY KEY (`T037_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T037`
--

LOCK TABLES `T037` WRITE;
/*!40000 ALTER TABLE `T037` DISABLE KEYS */;
/*!40000 ALTER TABLE `T037` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T038`
--

-- DROP TABLE IF EXISTS `T038`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T038` (
  `T038_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T038_T037_Id` int(10) unsigned DEFAULT NULL,
  `T038_T002_Id` int(10) unsigned DEFAULT NULL,
  `T038_C004_Id` int(11) NOT NULL DEFAULT '0',
  `T038_Prefixo_Agencia` int(4) NOT NULL DEFAULT '0',
  `T038_Digito_Verificador_Agencia` varchar(1) DEFAULT NULL,
  `T038_Codigo_Cedente` int(8) NOT NULL DEFAULT '0',
  `T038_Digito_Verificador_Cedente` varchar(1) DEFAULT NULL,
  `T038_Numero_Convenio` int(6) NOT NULL DEFAULT '0',
  `T038_Numero_Documento` varchar(25) DEFAULT NULL,
  `T038_Nosso_Numero` varchar(12) DEFAULT NULL,
  `T038_Digito_Verificador_Nosso_Numero` varchar(1) DEFAULT NULL,
  `T038_Codigo_Tipo_Cobranca` int(1) NOT NULL DEFAULT '0',
  `T038_Codigo_Tipo_Cobranca_Anterior` int(1) NOT NULL DEFAULT '0',
  `T038_Dias_Calculo` int(4) NOT NULL DEFAULT '0',
  `T038_Codigo_Natureza_Recebimento` int(2) NOT NULL DEFAULT '0',
  `T038_Descricao_Natureza_Recebimento` text,
  `T038_Prefixo_Titulo` varchar(3) DEFAULT NULL,
  `T038_Variacao_Carteira` int(3) NOT NULL DEFAULT '0',
  `T038_Conta_Caucao` int(1) NOT NULL DEFAULT '0',
  `T038_Codigo_Responsabilidade` int(5) NOT NULL DEFAULT '0',
  `T038_Digito_Verificador_Codigo_Responsabilidade` varchar(1) DEFAULT NULL,
  `T038_Valor_Taxa_Desconto` decimal(3,2) DEFAULT NULL,
  `T038_Valor_Taxa_IOF` decimal(4,1) DEFAULT NULL,
  `T038_Carteira` int(2) NOT NULL DEFAULT '0',
  `T038_Codigo_Motivo` int(2) NOT NULL DEFAULT '0',
  `T038_Descricao_Motivo` text,
  `T038_Data_Recebimento` date DEFAULT NULL,
  `T038_Numero_Titulo_Cedente` varchar(10) DEFAULT NULL,
  `T038_Confirmacao` varchar(20) DEFAULT NULL,
  `T038_Data_Vencimento` date DEFAULT NULL,
  `T038_Valor_Titulo` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Codigo_Banco_Recebedor` int(3) NOT NULL DEFAULT '0',
  `T038_Prefixo_Agencia_Recebedora` int(4) NOT NULL DEFAULT '0',
  `T038_Digito_Verificador_Agencia_Recebedora` varchar(1) DEFAULT NULL,
  `T038_Especie_Titulo` int(2) NOT NULL DEFAULT '0',
  `T038_Data_Credito` date DEFAULT NULL,
  `T038_Valor_Tarifa` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Outras_Despesas` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Juros_Desconto` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_IOF_Desconto` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Abatimento` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Desconto` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Recebido` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Juros_Mora` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Outros_Recebimentos` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Abatimento_Nao_Aproveitado` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Valor_Lancamento` decimal(11,2) NOT NULL DEFAULT '0.00',
  `T038_Flag_Debito_Credito` int(1) NOT NULL DEFAULT '0',
  `T038_Flag_Indicador_Valor` int(1) NOT NULL DEFAULT '0',
  `T038_Valor_Ajuste` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T038_Flag_Situacao` int(1) NOT NULL DEFAULT '0',
  `T038_Descricao_Situacao` varchar(50) DEFAULT NULL,
  `T038_Numero_Linha` int(11) NOT NULL DEFAULT '0',
  `T038_String` text,
  PRIMARY KEY (`T038_Id`),
  KEY `T038_FKIndex1` (`T038_T002_Id`),
  KEY `T038_FKIndex2` (`T038_T037_Id`),
  KEY `T038_FKIndex3` (`T038_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T038`
--

LOCK TABLES `T038` WRITE;
/*!40000 ALTER TABLE `T038` DISABLE KEYS */;
/*!40000 ALTER TABLE `T038` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T039`
--

-- DROP TABLE IF EXISTS `T039`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T039` (
  `T039_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T039_T032_Id` int(10) unsigned DEFAULT NULL,
  `T039_D024_Id` int(10) unsigned DEFAULT NULL,
  `T039_D035_Id` int(10) unsigned DEFAULT NULL,
  `T039_C004_Id` int(10) unsigned DEFAULT NULL,
  `T039_Quantidade` int(8) DEFAULT NULL,
  `T039_Descricao_Produto` varchar(20) DEFAULT NULL,
  `T039_Valor_Custo_Unitario` decimal(19,5) DEFAULT NULL,
  `T039_Numero_Pedido` varchar(5) DEFAULT NULL,
  `T039_Data` date DEFAULT NULL,
  `T039_Flag_Qual_Fornecedor` int(1) DEFAULT NULL,
  `T039_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `T039_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T039_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T039_Numero` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`T039_Id`),
  KEY `T039_FKIndex1` (`T039_D035_Id`),
  KEY `T039_FKIndex2` (`T039_T032_Id`),
  KEY `T039_FKIndex3` (`T039_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T039`
--

LOCK TABLES `T039` WRITE;
/*!40000 ALTER TABLE `T039` DISABLE KEYS */;
/*!40000 ALTER TABLE `T039` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T040`
--

-- DROP TABLE IF EXISTS `T040`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T040` (
  `T040_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T040_C007_Id` int(10) unsigned DEFAULT NULL,
  `T040_D024_Id` int(10) unsigned DEFAULT NULL,
  `T040_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T040_Numero` int(10) DEFAULT NULL,
  `T040_Data_Emissao` date DEFAULT NULL,
  `T040_Valor_Total` decimal(10,2) DEFAULT NULL,
  `T040_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T040_Codigo_Usuario` int(4) DEFAULT NULL,
  `T040_Numero_Orcamento` int(10) DEFAULT NULL,
  PRIMARY KEY (`T040_Id`),
  KEY `T040_FKIndex1` (`T040_D024_Id`),
  KEY `T040_FKIndex2` (`T040_C007_Id`),
  KEY `T040_FKIndex3` (`T040_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T040`
--

LOCK TABLES `T040` WRITE;
/*!40000 ALTER TABLE `T040` DISABLE KEYS */;
/*!40000 ALTER TABLE `T040` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T041`
--

-- DROP TABLE IF EXISTS `T041`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T041` (
  `T041_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T041_T040_Id` int(10) unsigned DEFAULT NULL,
  `T041_D035_Id` int(10) unsigned DEFAULT NULL,
  `T041_D024_Id` int(10) unsigned DEFAULT NULL,
  `T041_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T041_Quantidade` int(8) DEFAULT NULL,
  `T041_Valor_Preco` decimal(14,5) DEFAULT NULL,
  `T041_Descricao_Produto` varchar(20) DEFAULT NULL,
  `T041_Data` date DEFAULT NULL,
  `T041_Valor_Preco_Corrigido` decimal(10,2) DEFAULT NULL,
  `T041_Numero` varchar(10) DEFAULT NULL,
  `T041_Codigo_Local` varchar(10) DEFAULT NULL,
  `T041_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T041_Codigo_Produto` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`T041_Id`),
  KEY `T041_FKIndex1` (`T041_T040_Id`),
  KEY `T041_FKIndex2` (`T041_D035_Id`),
  KEY `T041_FKIndex3` (`T041_D024_Id`),
  KEY `T041_FKIndex4` (`T041_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T041`
--

LOCK TABLES `T041` WRITE;
/*!40000 ALTER TABLE `T041` DISABLE KEYS */;
/*!40000 ALTER TABLE `T041` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T042`
--

-- DROP TABLE IF EXISTS `T042`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T042` (
  `T042_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T042_C004_Id` int(10) unsigned DEFAULT NULL,
  `T042_Descricao` tinytext,
  `T042_Data` date DEFAULT NULL,
  `T042_Valor` decimal(19,2) DEFAULT NULL,
  `T042_Valor_IPI` decimal(19,2) DEFAULT NULL,
  `T042_Valor_ICMS` decimal(19,2) DEFAULT NULL,
  `T042_Mes_Ano_Movimento` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`T042_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T042`
--

LOCK TABLES `T042` WRITE;
/*!40000 ALTER TABLE `T042` DISABLE KEYS */;
/*!40000 ALTER TABLE `T042` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T043`
--

-- DROP TABLE IF EXISTS `T043`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T043` (
  `T043_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T043_T009_Id` int(10) unsigned DEFAULT NULL,
  `T043_D001_Id` int(10) unsigned DEFAULT NULL,
  `T043_C004_Id` int(10) unsigned DEFAULT NULL,
  `T043_Quantidade_Estoque_Total` int(10) DEFAULT '0',
  `T043_Quantidade` decimal(10,3) DEFAULT '0.000',
  `T043_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T043_Descricao_Produto` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`T043_Id`),
  KEY `T043_FKIndex1` (`T043_D001_Id`),
  KEY `T043_FKIndex2` (`T043_T009_Id`),
  KEY `T043_FKIndex3` (`T043_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T043`
--

LOCK TABLES `T043` WRITE;
/*!40000 ALTER TABLE `T043` DISABLE KEYS */;
/*!40000 ALTER TABLE `T043` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T044`
--

-- DROP TABLE IF EXISTS `T044`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T044` (
  `T044_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T044_D009_Id` int(10) unsigned DEFAULT NULL,
  `T044_Mes` char(2) DEFAULT NULL,
  `T044_Ano` char(4) DEFAULT NULL,
  `T044_Frequencia_Venda` int(10) DEFAULT NULL,
  `T044_Quantidade_Vendida` int(10) DEFAULT NULL,
  `T044_Medida_Por_Venda` int(10) DEFAULT NULL,
  `T044_Trimestre` char(1) DEFAULT NULL,
  PRIMARY KEY (`T044_Id`),
  KEY `T044_FKIndex1` (`T044_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T044`
--

LOCK TABLES `T044` WRITE;
/*!40000 ALTER TABLE `T044` DISABLE KEYS */;
/*!40000 ALTER TABLE `T044` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T045`
--

-- DROP TABLE IF EXISTS `T045`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T045` (
  `T045_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T045_C007_Id` int(10) unsigned DEFAULT NULL,
  `T045_C004_Id` int(11) DEFAULT '0',
  `T045_Data` datetime DEFAULT NULL,
  `T045_Observacao` text,
  PRIMARY KEY (`T045_Id`),
  KEY `T045_FKIndex1` (`T045_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T045`
--

LOCK TABLES `T045` WRITE;
/*!40000 ALTER TABLE `T045` DISABLE KEYS */;
/*!40000 ALTER TABLE `T045` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T046`
--

-- DROP TABLE IF EXISTS `T046`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T046` (
  `T046_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T046_D001_Id` int(10) unsigned DEFAULT NULL,
  `T046_D049_Id` int(10) unsigned DEFAULT NULL,
  `T046_T016_Id` int(10) unsigned DEFAULT NULL,
  `T046_Numero` varchar(5) DEFAULT NULL,
  `T046_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T046_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T046_Valor_Base` decimal(14,2) DEFAULT NULL,
  `T046_Data_Inicio` date DEFAULT NULL,
  `T046_Flag_ICMS_Iguais` varchar(1) DEFAULT 'N',
  `T046_Valor_Consumidor` decimal(14,2) DEFAULT '0.00',
  `T046_Flag_Ignorar_Promocao_Consumidor` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T046_Id`),
  KEY `T046_FKIndex1` (`T046_T016_Id`),
  KEY `T046_FKIndex2` (`T046_D049_Id`),
  KEY `T046_FKIndex3` (`T046_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T046`
--

LOCK TABLES `T046` WRITE;
/*!40000 ALTER TABLE `T046` DISABLE KEYS */;
/*!40000 ALTER TABLE `T046` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T047`
--

-- DROP TABLE IF EXISTS `T047`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T047` (
  `T047_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T047_C007_Id` int(10) unsigned DEFAULT NULL,
  `T047_C004_Id` int(10) unsigned DEFAULT NULL,
  `T047_Numero` varchar(5) DEFAULT NULL,
  `T047_Data` date DEFAULT NULL,
  `T047_Observacao` text,
  `T047_Flag_Status` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`T047_Id`),
  KEY `T047_FKIndex1` (`T047_C007_Id`),
  KEY `T047_FKIndex2` (`T047_C004_Id`),
  KEY `IDX_Data` (`T047_Data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T047`
--

LOCK TABLES `T047` WRITE;
/*!40000 ALTER TABLE `T047` DISABLE KEYS */;
/*!40000 ALTER TABLE `T047` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T048`
--

-- DROP TABLE IF EXISTS `T048`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T048` (
  `T048_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T048_T047_Id` int(10) unsigned DEFAULT NULL,
  `T048_D049_Id` int(10) unsigned DEFAULT NULL,
  `T048_C004_Id` int(10) unsigned DEFAULT NULL,
  `T048_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T048_Quantidade` decimal(10,2) DEFAULT NULL,
  `T048_Itens` text,
  `T048_Observacao` text,
  PRIMARY KEY (`T048_Id`),
  KEY `T048_FKIndex1` (`T048_D049_Id`),
  KEY `T048_FKIndex2` (`T048_T047_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T048`
--

LOCK TABLES `T048` WRITE;
/*!40000 ALTER TABLE `T048` DISABLE KEYS */;
/*!40000 ALTER TABLE `T048` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T049`
--

-- DROP TABLE IF EXISTS `T049`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T049` (
  `T049_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T049_C008_Id` int(10) unsigned DEFAULT NULL,
  `T049_C007_Id` int(10) unsigned DEFAULT NULL,
  `T049_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T049_Data` datetime DEFAULT NULL,
  `T049_Observacao` text,
  `T049_Flag_Cotacao_Finalizada` varchar(1) DEFAULT 'N',
  `T049_Flag_Fornecedor_Automatico` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T049_Id`),
  KEY `T009_IDX_EMPRESAS` (`T049_C004_Id`),
  KEY `T049_FKIndex1` (`T049_C007_Id`),
  KEY `T049_FKIndex2` (`T049_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T049`
--

LOCK TABLES `T049` WRITE;
/*!40000 ALTER TABLE `T049` DISABLE KEYS */;
/*!40000 ALTER TABLE `T049` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T050`
--

-- DROP TABLE IF EXISTS `T050`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T050` (
  `T050_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T050_D001_Id` int(10) unsigned DEFAULT NULL,
  `T050_T049_Id` int(10) unsigned DEFAULT NULL,
  `T050_C004_Id` int(10) unsigned DEFAULT NULL,
  `T050_Quantidade_Estoque_Total` int(10) DEFAULT '0',
  `T050_Quantidade` int(10) DEFAULT '0',
  `T050_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T050_Descricao_Produto` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`T050_Id`),
  KEY `T050_FKIndex1` (`T050_T049_Id`),
  KEY `T050_FKIndex3` (`T050_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T050`
--

LOCK TABLES `T050` WRITE;
/*!40000 ALTER TABLE `T050` DISABLE KEYS */;
/*!40000 ALTER TABLE `T050` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T051`
--

-- DROP TABLE IF EXISTS `T051`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T051` (
  `T051_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T051_D044_Id` int(10) unsigned DEFAULT NULL,
  `T051_D036_Id` int(10) unsigned DEFAULT NULL,
  `T051_C007_Id` int(10) unsigned DEFAULT NULL,
  `T051_D022_Id` int(10) unsigned DEFAULT NULL,
  `T051_T049_Id` int(10) unsigned DEFAULT NULL,
  `T051_D024_Id` int(10) unsigned DEFAULT NULL,
  `T051_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_1` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_2` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_3` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_4` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_5` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_6` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_7` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_8` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_9` int(10) NOT NULL DEFAULT '0',
  `T051_Prazo_Pagamento_10` int(10) NOT NULL DEFAULT '0',
  `T051_Flag_Gerar_OC` int(1) NOT NULL DEFAULT '2',
  `T051_Observacao` text,
  `T051_Prazo_Entrega` int(10) NOT NULL DEFAULT '0',
  `T051_Valor_Frete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T051_Valor_Total_Custo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T051_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T051_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T051_Aliquota_ICMS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T051_Valor_Total_ICMS` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T051_Percentual_Reducao_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T051_Valor_Total_Base_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T051_Aliquota_IPI` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T051_Valor_Total_IPI` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T051_Nome_Fornecedor` varchar(50) DEFAULT NULL,
  `T051_Nome_Contato` varchar(40) DEFAULT NULL,
  `T051_Fax` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`T051_Id`),
  KEY `T010_IDX_EMPRESAS` (`T051_C004_Id`),
  KEY `T051_FKIndex1` (`T051_T049_Id`),
  KEY `T051_FKIndex2` (`T051_D024_Id`),
  KEY `T051_FKIndex3` (`T051_D022_Id`),
  KEY `T051_FKIndex4` (`T051_C007_Id`),
  KEY `T051_FKIndex5` (`T051_D036_Id`),
  KEY `T051_FKIndex6` (`T051_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T051`
--

LOCK TABLES `T051` WRITE;
/*!40000 ALTER TABLE `T051` DISABLE KEYS */;
/*!40000 ALTER TABLE `T051` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T052`
--

-- DROP TABLE IF EXISTS `T052`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T052` (
  `T052_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T052_D044_Id` int(10) unsigned DEFAULT NULL,
  `T052_C008_Id` int(10) unsigned DEFAULT NULL,
  `T052_D022_Id` int(10) unsigned DEFAULT NULL,
  `T052_T051_Id` int(10) unsigned DEFAULT NULL,
  `T052_C007_Id` int(10) unsigned DEFAULT NULL,
  `T052_D036_Id` int(10) unsigned DEFAULT NULL,
  `T052_T049_Id` int(10) unsigned DEFAULT NULL,
  `T052_D024_Id` int(10) unsigned DEFAULT NULL,
  `T052_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T052_Data_Emissao` date DEFAULT NULL,
  `T052_Prazo_Pagamento_1` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_2` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_3` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_4` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_5` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_6` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_7` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_8` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_9` int(10) DEFAULT '0',
  `T052_Prazo_Pagamento_10` int(10) DEFAULT '0',
  `T052_Observacao` text,
  `T052_Prazo_Entrega` int(10) DEFAULT '0',
  `T052_Valor_Frete` decimal(10,2) DEFAULT '0.00',
  `T052_Valor_Custo_Unitario` decimal(10,2) DEFAULT '0.00',
  `T052_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T052_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T052_Valor_Total_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T052_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T052_Valor_Total_ICMS` decimal(10,2) DEFAULT '0.00',
  `T052_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T052_Valor_Total_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T052_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T052_Valor_Total_IPI` decimal(10,2) DEFAULT '0.00',
  `T052_Nome_Contato` varchar(40) DEFAULT NULL,
  `T052_Fax` varchar(30) DEFAULT NULL,
  `T052_Numero` int(10) DEFAULT NULL,
  `T052_Data_Previsao_Recebimento` date DEFAULT NULL,
  `T052_Data_Previsao_Pagamento` date DEFAULT NULL,
  `T052_Valor_Antecipado` decimal(10,2) DEFAULT '0.00',
  `T052_Data_Vencimento_Antecipado` date DEFAULT NULL,
  `T052_Flag_Excluir` varchar(1) DEFAULT 'N',
  `T052_Peso_Kg_Total` decimal(10,3) DEFAULT '0.000',
  `T052_Flag_Cancelada` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T052_Id`),
  KEY `T011_IDX_EMPRESAS` (`T052_C004_Id`),
  KEY `T052_FKIndex1` (`T052_D024_Id`),
  KEY `T052_FKIndex2` (`T052_T049_Id`),
  KEY `T052_FKIndex3` (`T052_D036_Id`),
  KEY `T052_FKIndex4` (`T052_C007_Id`),
  KEY `T052_FKIndex5` (`T052_T051_Id`),
  KEY `T052_FKIndex6` (`T052_D022_Id`),
  KEY `T052_FKIndex7` (`T052_D044_Id`),
  KEY `T052_FKIndex8` (`T052_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T052`
--

LOCK TABLES `T052` WRITE;
/*!40000 ALTER TABLE `T052` DISABLE KEYS */;
/*!40000 ALTER TABLE `T052` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T053`
--

-- DROP TABLE IF EXISTS `T053`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T053` (
  `T053_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T053_T058_Id` int(10) unsigned DEFAULT NULL,
  `T053_D009_Id` int(10) unsigned DEFAULT NULL,
  `T053_T050_Id` int(10) unsigned DEFAULT NULL,
  `T053_T052_Id` int(10) unsigned DEFAULT NULL,
  `T053_C004_Id` int(10) unsigned DEFAULT '0',
  `T053_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T053_Codigo_Produto_Fornecedor` varchar(20) DEFAULT NULL,
  `T053_Descricao_Produto` varchar(30) DEFAULT NULL,
  `T053_Quantidade` int(10) DEFAULT '0',
  `T053_Quantidade_Lancamento` int(10) DEFAULT '0',
  `T053_Valor_Custo_Unitario` decimal(20,6) DEFAULT '0.000000',
  `T053_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T053_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T053_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T053_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T053_Valor_ICMS` decimal(10,2) DEFAULT '0.00',
  `T053_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T053_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T053_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T053_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T053_Prazo_Entrega` int(10) DEFAULT '0',
  `T053_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T053_Flag_Travar_Pedido` varchar(1) DEFAULT 'N',
  `T053_Peso_Kg_Unitario` decimal(19,3) DEFAULT '0.000',
  `T053_Peso_Kg_Total` decimal(10,3) DEFAULT '0.000',
  `T053_Peso_Kg_Unitario_Ajustado` decimal(19,6) DEFAULT '0.000000',
  `T053_Flag_Cancelada` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T053_Id`),
  KEY `T012_IDX_CODIGO_PRODUTO` (`T053_Codigo_Produto`),
  KEY `T012_IDX_EMPRESAS` (`T053_C004_Id`),
  KEY `T053_FKIndex1` (`T053_T052_Id`),
  KEY `T053_FKIndex2` (`T053_T050_Id`),
  KEY `T053_FKIndex3` (`T053_D009_Id`),
  KEY `T053_FKIndex4` (`T053_T058_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T053`
--

LOCK TABLES `T053` WRITE;
/*!40000 ALTER TABLE `T053` DISABLE KEYS */;
/*!40000 ALTER TABLE `T053` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T054`
--

-- DROP TABLE IF EXISTS `T054`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T054` (
  `T054_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T054_D006_Id` int(10) unsigned DEFAULT NULL,
  `T054_D022_Id` int(10) unsigned DEFAULT NULL,
  `T054_C008_Id` int(10) unsigned DEFAULT NULL,
  `T054_C007_Id` int(10) unsigned DEFAULT NULL,
  `T054_D036_Id` int(10) unsigned DEFAULT NULL,
  `T054_D044_Id` int(10) unsigned DEFAULT NULL,
  `T054_D018_Id` int(10) unsigned DEFAULT NULL,
  `T054_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T054_Data_Emissao` datetime DEFAULT NULL,
  `T054_Data_Transferencia_Estoque` datetime DEFAULT NULL,
  `T054_Data_Transferencia_Custo` datetime DEFAULT NULL,
  `T054_Numero_DI` varchar(30) DEFAULT NULL,
  `T054_Valor_DI` decimal(10,2) DEFAULT '0.00',
  `T054_Observacao` text,
  `T054_Observacao_Fiscal` text,
  `T054_Valor_Total_Custo_Original` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Produtos` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Produtos_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_IPI` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_IPI` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_IPI_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_Imposto_Importacao` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Imposto_Importacao` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Imposto_Importacao_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_COFINS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_COFINS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_COFINS_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_PIS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_PIS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_PIS_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_Extra` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Extra` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Extra_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_Fatura` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Frete` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Frete_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_CIF` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_CIF_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Aduaneiro` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Aduaneiro_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_Imposto_Importacao` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_IPI` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_PIS_COFINS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_Aduaneira` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_Aduaneira_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Despesas_ICMS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_ICMS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_Base_ICMS_Presumido` decimal(19,2) DEFAULT '0.00',
  `T054_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T054_Percentual_Reducao_ICMS_Presumido` decimal(5,2) DEFAULT '0.00',
  `T054_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T054_Aliquota_ICMS_Presumido` decimal(5,2) DEFAULT '0.00',
  `T054_Valor_Total_ICMS` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_ICMS_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_ICMS_Presumido` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_ICMS_Presumido_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_ICMS_Pagar` decimal(19,2) DEFAULT '0.00',
  `T054_Valor_Total_ICMS_Pagar_Real` decimal(19,2) DEFAULT '0.00',
  `T054_Peso_Kg_Bruto` decimal(19,3) DEFAULT '0.000',
  `T054_Peso_Kg_Liquido` decimal(19,3) DEFAULT '0.000',
  `T054_Peso_Kg_Liquido_Fatura` decimal(19,6) DEFAULT '0.000000',
  `T054_Peso_Lb_Bruto` decimal(19,3) DEFAULT '0.000',
  `T054_Peso_Lb_Liquido` decimal(19,3) DEFAULT '0.000',
  `T054_Valor_Cambio_Euro` decimal(19,4) DEFAULT '0.0000',
  `T054_Valor_Cambio_Dolar` decimal(19,4) DEFAULT '0.0000',
  `T054_Flag_Fechado` varchar(1) DEFAULT 'N',
  `T054_Flag_Almoxarifado` varchar(1) DEFAULT 'N',
  `T054_Flag_Status` int(1) DEFAULT '0',
  `T054_Flag_Isento_II` varchar(1) DEFAULT 'N',
  `T054_Data_Emissao_BL` date DEFAULT NULL,
  `T054_Numero_BL` varchar(20) DEFAULT NULL,
  `T054_Valor_Total_DI` decimal(10,2) DEFAULT '0.00',
  `T054_Valor_Total_DI_Real` decimal(10,2) DEFAULT '0.00',
  `T054_Valor_Total2_Real` decimal(10,2) DEFAULT '0.00',
  `T054_Valor_Total_Diferenca` decimal(10,2) DEFAULT '0.00',
  `T054_Valor_Cambio_Real` decimal(19,4) DEFAULT '0.0000',
  `T054_Data_Envio_Contabilidade` date DEFAULT NULL,
  `T054_Data_Retorno_Contabilidade` date DEFAULT NULL,
  `T054_Numero_Antigo` int(10) DEFAULT NULL,
  PRIMARY KEY (`T054_Id`),
  KEY `T054_FKIndex1` (`T054_D036_Id`),
  KEY `T054_FKIndex2` (`T054_C007_Id`),
  KEY `T054_FKIndex3` (`T054_C008_Id`),
  KEY `T054_FKIndex4` (`T054_D018_Id`),
  KEY `T054_FKIndex5` (`T054_D022_Id`),
  KEY `T054_FKIndex6` (`T054_D044_Id`),
  KEY `T054_FKIndex7` (`T054_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T054`
--

LOCK TABLES `T054` WRITE;
/*!40000 ALTER TABLE `T054` DISABLE KEYS */;
/*!40000 ALTER TABLE `T054` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T055`
--

-- DROP TABLE IF EXISTS `T055`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T055` (
  `T055_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T055_T060_Id` int(10) unsigned DEFAULT NULL,
  `T055_T054_Id` int(10) unsigned DEFAULT NULL,
  `T055_D037_Id` int(10) unsigned DEFAULT NULL,
  `T055_D005_Id` int(10) unsigned DEFAULT NULL,
  `T055_D009_Id` int(10) unsigned DEFAULT NULL,
  `T055_T053_Id` int(10) unsigned DEFAULT NULL,
  `T055_C004_Id` int(10) unsigned DEFAULT '0',
  `T055_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T055_Codigo_Produto_Fornecedor` varchar(20) DEFAULT NULL,
  `T055_Descricao_Produto` varchar(30) DEFAULT NULL,
  `T055_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T055_Prazo_Entrega` int(10) DEFAULT '0',
  `T055_Quantidade` int(10) DEFAULT '0',
  `T055_Valor_Custo_Unitario_Anterior` decimal(19,5) DEFAULT '0.00000',
  `T055_Valor_Custo_Unitario` decimal(19,5) DEFAULT '0.00000',
  `T055_Valor_Total_Custo` decimal(19,2) DEFAULT '0.00',
  `T055_Valor_Preco_Unitario_Nota_Fiscal` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Preco_Nota_Fiscal` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Custo_Unitario_Original` decimal(19,5) DEFAULT '0.00000',
  `T055_Valor_Total_Custo_Original` decimal(19,2) DEFAULT '0.00',
  `T055_Valor_Base_IPI` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_ICMS_Sem_Reducao` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_ICMS_Normal` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_ICMS_Presumido` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_COFINS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_PIS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_Extra` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Base_Imposto_Importacao` decimal(19,6) DEFAULT '0.000000',
  `T055_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T055_Aliquota_Imposto_Importacao` decimal(5,2) DEFAULT '0.00',
  `T055_Aliquota_COFINS` decimal(5,2) DEFAULT '0.00',
  `T055_Aliquota_PIS` decimal(5,2) DEFAULT '0.00',
  `T055_Aliquota_Extra` decimal(5,2) DEFAULT '0.00',
  `T055_Valor_ICMS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_ICMS_Presumido` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_ICMS_Pagar` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_IPI` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Imposto_Importacao` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_COFINS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_PIS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Extra` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Frete` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Despesas` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Despesas_Fatura` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Aduaneira` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Despesas_Aduaneira` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Custo_Final_Unitario` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Custo_Final` decimal(19,2) DEFAULT '0.00',
  `T055_Peso_Kg_Liquido_Unitario` decimal(19,6) DEFAULT '0.000000',
  `T055_Peso_Kg_Liquido_Total` decimal(19,6) DEFAULT '0.000000',
  `T055_Peso_Lb_Liquido_Unitario` decimal(19,3) DEFAULT '0.000',
  `T055_Peso_Lb_Liquido_Total` decimal(19,3) DEFAULT '0.000',
  `T055_Multiplicador_X` decimal(19,6) DEFAULT '0.000000',
  `T055_Multiplicador_Y` decimal(19,6) DEFAULT '0.000000',
  `T055_Data_Transferencia_Estoque` date DEFAULT NULL,
  `T055_Valor_Total_ICMS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_ICMS_Presumido` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_ICMS_Pagar` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_IPI` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Imposto_Importacao` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_COFINS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_PIS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Extra` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Frete` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Despesas` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Despesas_Fatura` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Aduaneira` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Despesas_Aduaneira` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_IPI` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_ICMS_Sem_Reducao` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_ICMS_Normal` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_ICMS_Presumido` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_COFINS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_PIS` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_Extra` decimal(19,6) DEFAULT '0.000000',
  `T055_Valor_Total_Base_Imposto_Importacao` decimal(19,6) DEFAULT '0.000000',
  `T055_Peso_Kg_Liquido_Unitario_Fatura` decimal(19,6) DEFAULT '0.000000',
  `T055_Peso_Kg_Liquido_Total_Fatura` decimal(19,6) DEFAULT '0.000000',
  PRIMARY KEY (`T055_Id`),
  KEY `T055_IDX_CODIGO_PRODUTO` (`T055_Codigo_Produto`),
  KEY `T055_IDX_EMPRESAS` (`T055_C004_Id`),
  KEY `T055_FKIndex1` (`T055_T054_Id`),
  KEY `T055_FKIndex2` (`T055_T053_Id`),
  KEY `T055_FKIndex3` (`T055_D037_Id`),
  KEY `T055_FKIndex4` (`T055_D009_Id`),
  KEY `T055_FKIndex5` (`T055_T060_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T055`
--

LOCK TABLES `T055` WRITE;
/*!40000 ALTER TABLE `T055` DISABLE KEYS */;
/*!40000 ALTER TABLE `T055` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T056`
--

-- DROP TABLE IF EXISTS `T056`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T056` (
  `T056_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T056_T054_Id` int(10) unsigned DEFAULT NULL,
  `T056_D024_Id` int(10) unsigned DEFAULT NULL,
  `T056_D037_Id` int(10) unsigned DEFAULT NULL,
  `T056_D044_Id` int(10) unsigned DEFAULT NULL,
  `T056_D045_Id` int(10) unsigned DEFAULT NULL,
  `T056_T060_Id` int(10) unsigned DEFAULT NULL,
  `T056_D014_Id` int(10) unsigned DEFAULT NULL,
  `T056_T076_Id` int(10) DEFAULT NULL,
  `T056_Descricao` varchar(30) DEFAULT NULL,
  `T056_Flag_Frete` varchar(1) DEFAULT 'N',
  `T056_Flag_Calcular_Aduaneira` varchar(1) DEFAULT 'N',
  `T056_Flag_Calcular_ICMS` varchar(1) DEFAULT 'N',
  `T056_Flag_Calcular_PIS_COFINS` varchar(1) DEFAULT 'N',
  `T056_Flag_Calcular_IPI` varchar(1) DEFAULT 'N',
  `T056_Flag_Calcular_Imposto_Importacao` varchar(1) DEFAULT 'N',
  `T056_Valor_Unitario_Despesa` decimal(19,6) DEFAULT '0.000000',
  `T056_Data_Cambio` date DEFAULT NULL,
  `T056_Valor_Cambio` decimal(10,4) DEFAULT '0.0000',
  `T056_Quantidade` decimal(19,3) DEFAULT '0.000',
  `T056_Valor_Total_Despesa` decimal(19,2) DEFAULT '0.00',
  `T056_Valor_Total_Despesa_Real` decimal(19,2) DEFAULT '0.00',
  `T056_Observacao_Formula` text,
  `T056_Data_Vencimento` date DEFAULT NULL,
  `T056_Numero_Documento` varchar(20) DEFAULT NULL,
  `T056_Flag_Tipo` varchar(15) DEFAULT NULL,
  `T056_Flag_Confirmado` varchar(1) DEFAULT NULL,
  `T056_Data_Cambio_Pagamento` date DEFAULT NULL,
  `T056_Valor_Cambio_Pagamento` decimal(10,4) DEFAULT NULL,
  `T056_Valor_Total_Despesa_Pagamento` decimal(10,2) DEFAULT NULL,
  `T056_Valor_Total_Despesa_Real_Pagamento` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T056_Id`),
  KEY `T056_FKIndex1` (`T056_T054_Id`),
  KEY `T056_FKIndex2` (`T056_D045_Id`),
  KEY `T056_FKIndex3` (`T056_D044_Id`),
  KEY `T056_FKIndex4` (`T056_T060_Id`),
  KEY `T056_FKIndex5` (`T056_D014_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T056`
--

LOCK TABLES `T056` WRITE;
/*!40000 ALTER TABLE `T056` DISABLE KEYS */;
/*!40000 ALTER TABLE `T056` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T057`
--

-- DROP TABLE IF EXISTS `T057`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T057` (
  `T057_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T057_T055_Id` int(10) unsigned DEFAULT NULL,
  `T057_T056_Id` int(10) unsigned DEFAULT NULL,
  `T057_T060_Id` int(10) unsigned DEFAULT NULL,
  `T057_T076_Id` int(10) unsigned DEFAULT NULL,
  `T057_T075_Id` int(10) unsigned DEFAULT NULL,
  `T057_Descricao` varchar(40) DEFAULT NULL,
  `T057_Flag_Frete` varchar(1) DEFAULT 'N',
  `T057_Flag_Calcular_Aduaneira` varchar(1) DEFAULT 'N',
  `T057_Flag_Calcular_ICMS` varchar(1) DEFAULT 'N',
  `T057_Flag_Calcular_PIS_COFINS` varchar(1) DEFAULT 'N',
  `T057_Flag_Calcular_IPI` varchar(1) DEFAULT 'N',
  `T057_Flag_Calcular_Imposto_Importacao` varchar(1) DEFAULT 'N',
  `T057_Valor` decimal(19,6) DEFAULT '0.000000',
  `T057_Valor_Total` decimal(19,6) DEFAULT '0.000000',
  `T057_Valor_Total_Despesa` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`T057_Id`),
  KEY `T057_FKIndex1` (`T057_T055_Id`),
  KEY `T057_FKIndex2` (`T057_T056_Id`),
  KEY `T057_FKIndex3` (`T057_T076_Id`),
  KEY `T057_FKIndex4` (`T057_T075_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T057`
--

LOCK TABLES `T057` WRITE;
/*!40000 ALTER TABLE `T057` DISABLE KEYS */;
/*!40000 ALTER TABLE `T057` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T058`
--

-- DROP TABLE IF EXISTS `T058`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T058` (
  `T058_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T058_T049_Id` int(10) unsigned DEFAULT NULL,
  `T058_T050_Id` int(10) unsigned DEFAULT NULL,
  `T058_T051_Id` int(10) unsigned DEFAULT NULL,
  `T058_D009_Id` int(10) unsigned DEFAULT NULL,
  `T058_C004_Id` int(10) unsigned DEFAULT NULL,
  `T058_Flag_Gerar_OC` int(1) unsigned DEFAULT '2',
  `T058_Valor_Custo_Unitario` decimal(14,4) DEFAULT '0.0000',
  `T058_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T058_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `T058_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T058_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T058_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T058_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T058_Prazo_Entrega` int(10) DEFAULT '0',
  PRIMARY KEY (`T058_Id`),
  KEY `T058_FKIndex1` (`T058_D009_Id`),
  KEY `T058_FKIndex2` (`T058_T050_Id`),
  KEY `T058_FKIndex3` (`T058_T051_Id`),
  KEY `T058_FKIndex4` (`T058_T049_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T058`
--

LOCK TABLES `T058` WRITE;
/*!40000 ALTER TABLE `T058` DISABLE KEYS */;
/*!40000 ALTER TABLE `T058` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T059`
--

-- DROP TABLE IF EXISTS `T059`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T059` (
  `T059_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T059_T009_Id` int(10) unsigned DEFAULT NULL,
  `T059_T043_Id` int(10) unsigned DEFAULT NULL,
  `T059_T010_Id` int(10) unsigned DEFAULT NULL,
  `T059_D009_Id` int(10) unsigned DEFAULT NULL,
  `T059_D005_Id` int(10) unsigned DEFAULT NULL,
  `T059_C004_Id` int(10) unsigned DEFAULT NULL,
  `T059_Flag_Gerar_OC` int(1) unsigned DEFAULT '2',
  `T059_Valor_Custo_Unitario` decimal(14,4) DEFAULT '0.0000',
  `T059_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T059_Codigo_Produto_Fornecedor` varchar(30) DEFAULT NULL,
  `T059_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T059_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T059_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T059_Valor_ICMS` decimal(10,2) DEFAULT '0.00',
  `T059_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T059_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T059_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T059_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T059_Prazo_Entrega` int(10) DEFAULT '0',
  `T059_Classificacao_Fiscal` varchar(12) DEFAULT '0',
  `T059_Flag_Imprimir` varchar(1) DEFAULT 'N',
  `T059_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T059_Quantidade` decimal(10,3) DEFAULT NULL,
  `T059_Descricao_Produto` varchar(50) DEFAULT NULL,
  `T059_D037_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`T059_Id`),
  KEY `T059_FKIndex1` (`T059_D009_Id`),
  KEY `T059_FKIndex2` (`T059_T043_Id`),
  KEY `T059_FKIndex3` (`T059_T010_Id`),
  KEY `T059_FKIndex4` (`T059_D005_Id`),
  KEY `T059_FKIndex5` (`T059_T009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T059`
--

LOCK TABLES `T059` WRITE;
/*!40000 ALTER TABLE `T059` DISABLE KEYS */;
/*!40000 ALTER TABLE `T059` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T060`
--

-- DROP TABLE IF EXISTS `T060`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T060` (
  `T060_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T060_T054_Id` int(10) unsigned DEFAULT NULL,
  `T060_D024_Id` int(10) unsigned DEFAULT NULL,
  `T060_D044_Id` int(10) unsigned DEFAULT NULL,
  `T060_C004_Id` int(10) unsigned DEFAULT NULL,
  `T060_Numero_Fatura` varchar(30) DEFAULT NULL,
  `T060_Data_Emissao` date DEFAULT NULL,
  `T060_Valor_Fatura` decimal(19,2) DEFAULT '0.00',
  `T060_Data_Vencimento_Antecipado` date DEFAULT NULL,
  `T060_Peso_Liquido` decimal(19,3) DEFAULT '0.000',
  `T060_Valor_Antecipado` decimal(10,2) DEFAULT NULL,
  `T060_Valor_A_Vista` decimal(10,2) DEFAULT NULL,
  `T060_Valor_A_Prazo` decimal(10,2) DEFAULT NULL,
  `T060_Data_Vencimento_A_Vista` date DEFAULT NULL,
  `T060_Data_Vencimento_A_Prazo` date DEFAULT NULL,
  PRIMARY KEY (`T060_Id`),
  KEY `T060_FKIndex1` (`T060_T054_Id`),
  KEY `T060_FKIndex2` (`T060_D024_Id`),
  KEY `T060_FKIndex3` (`T060_D044_Id`),
  KEY `T060_FKIndex4` (`T060_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T060`
--

LOCK TABLES `T060` WRITE;
/*!40000 ALTER TABLE `T060` DISABLE KEYS */;
/*!40000 ALTER TABLE `T060` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T061`
--

-- DROP TABLE IF EXISTS `T061`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T061` (
  `T061_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T061_D014_Id` int(10) unsigned DEFAULT NULL,
  `T061_T054_Id` int(10) unsigned DEFAULT NULL,
  `T061_D024_Id` int(10) unsigned DEFAULT NULL,
  `T061_D044_Id` int(10) unsigned DEFAULT NULL,
  `T061_C004_Id` int(10) unsigned DEFAULT NULL,
  `T061_Data_Vencimento` date DEFAULT NULL,
  `T061_Numero_Contrato_Cambio` varchar(14) DEFAULT NULL,
  `T061_Valor_Contrato` decimal(19,2) DEFAULT '0.00',
  `T061_Valor_Cambio` decimal(10,4) DEFAULT '0.0000',
  `T061_Valor_Real` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`T061_Id`),
  KEY `T061_FKIndex1` (`T061_T054_Id`),
  KEY `T061_FKIndex2` (`T061_D024_Id`),
  KEY `T061_FKIndex3` (`T061_D044_Id`),
  KEY `T061_FKIndex4` (`T061_C004_Id`),
  KEY `T061_FKIndex5` (`T061_D014_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T061`
--

LOCK TABLES `T061` WRITE;
/*!40000 ALTER TABLE `T061` DISABLE KEYS */;
/*!40000 ALTER TABLE `T061` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T062`
--

-- DROP TABLE IF EXISTS `T062`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T062` (
  `T062_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T062_D009_Id` int(10) DEFAULT NULL,
  `T062_T001_Id` int(10) DEFAULT NULL,
  `T062_Data` date DEFAULT NULL,
  `T062_Saldo` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`T062_Id`),
  KEY `IDX_T062_Id` (`T062_Id`),
  KEY `IDX_T062_D009_Id` (`T062_D009_Id`),
  KEY `IDX_T062_T001_Id` (`T062_T001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T062`
--

LOCK TABLES `T062` WRITE;
/*!40000 ALTER TABLE `T062` DISABLE KEYS */;
/*!40000 ALTER TABLE `T062` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T063`
--

-- DROP TABLE IF EXISTS `T063`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T063` (
  `T063_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T063_D009_Id` int(10) DEFAULT NULL,
  `T063_T001_Id` int(10) DEFAULT NULL,
  `T063_T062_Id` int(10) DEFAULT NULL,
  `T063_Data` date DEFAULT NULL,
  `T063_Quantidade` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`T063_Id`),
  KEY `IDX_T063_Id` (`T063_Id`),
  KEY `IDX_T063_T062_Id` (`T063_T062_Id`),
  KEY `IDX_T063_T001_Id` (`T063_T001_Id`),
  KEY `IDX_T063_D009_Id` (`T063_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T063`
--

LOCK TABLES `T063` WRITE;
/*!40000 ALTER TABLE `T063` DISABLE KEYS */;
/*!40000 ALTER TABLE `T063` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T064`
--

-- DROP TABLE IF EXISTS `T064`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T064` (
  `T064_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `T064_D024_Id` int(10) unsigned DEFAULT NULL,
  `T064_Data_Inclusao` datetime DEFAULT NULL,
  `T064_Tipo_Frete` enum('','SEDEX','PAC') DEFAULT '',
  `T064_Valor_Frete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T064_Valor_Total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T064_Peso_Total` decimal(10,3) DEFAULT '0.000',
  `T064_Transportadora` varchar(50) DEFAULT NULL,
  `T064_Flag_Pedido_Fechado` enum('S','N') DEFAULT 'N',
  `T064_Flag_Status` enum('0','1','2','3','4','5','6') DEFAULT '0',
  `T064_Flag_Cancelado` enum('S','N') DEFAULT 'N',
  `T064_Forma_Pagamento` enum('0','1','2') DEFAULT '0',
  `T064_Aviso_Recebimento_Correios` varchar(15) DEFAULT NULL,
  `T064_Observacao_Cancelamento` text,
  `T064_Flag_Tipo` enum('Orçamento','Pedido') DEFAULT 'Pedido',
  PRIMARY KEY (`T064_Id`),
  KEY `T064_IDX_CLIENTE` (`T064_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='PEDIDO INTERNET';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T064`
--

LOCK TABLES `T064` WRITE;
/*!40000 ALTER TABLE `T064` DISABLE KEYS */;
/*!40000 ALTER TABLE `T064` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T065`
--

-- DROP TABLE IF EXISTS `T065`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T065` (
  `T065_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `T065_D001_Id` int(10) unsigned DEFAULT NULL,
  `T065_T064_Id` bigint(20) unsigned DEFAULT NULL,
  `T065_Quantidade` int(4) NOT NULL DEFAULT '0',
  `T065_Valor_Preco_Unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T065_Valor_Preco_Total` decimal(10,2) DEFAULT '0.00',
  `T065_Peso_Unitario` decimal(10,3) DEFAULT '0.000',
  `T065_Peso_Total` decimal(10,3) DEFAULT '0.000',
  PRIMARY KEY (`T065_Id`),
  KEY `T065_IDX_PRODUTO` (`T065_D001_Id`),
  KEY `T065_IDX_PEDIDO_INTERNET` (`T065_T064_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ITENS DO PEDIDO INTERNET';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T065`
--

LOCK TABLES `T065` WRITE;
/*!40000 ALTER TABLE `T065` DISABLE KEYS */;
/*!40000 ALTER TABLE `T065` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T066`
--

-- DROP TABLE IF EXISTS `T066`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T066` (
  `T066_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T066_D004_Id` int(10) DEFAULT NULL,
  `T066_D009_Id` int(10) DEFAULT NULL,
  `T066_D048_Id` int(10) DEFAULT NULL,
  `T066_Quantidade_Estoque` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`T066_Id`),
  KEY `T066_Index_Locacao` (`T066_D004_Id`),
  KEY `T066_Index_D009` (`T066_D009_Id`),
  KEY `T066_Index_D048` (`T066_D048_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T066`
--

LOCK TABLES `T066` WRITE;
/*!40000 ALTER TABLE `T066` DISABLE KEYS */;
/*!40000 ALTER TABLE `T066` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T067`
--

-- DROP TABLE IF EXISTS `T067`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T067` (
  `T067_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T067_T070_Id` int(10) DEFAULT NULL,
  `T067_Ano` varchar(4) DEFAULT NULL,
  `T067_Margem_Inicial` decimal(14,2) DEFAULT NULL,
  `T067_Margem_Final` decimal(14,2) DEFAULT NULL,
  `T067_Empresa` varchar(5) DEFAULT NULL,
  `T067_Aliquota_Empresa_Comercial` decimal(5,2) DEFAULT NULL,
  `T067_Aliquota_Empresa_Industrial` decimal(5,2) DEFAULT NULL,
  `T067_Aliquota_Empresa_Servicos` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`T067_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T067`
--

LOCK TABLES `T067` WRITE;
/*!40000 ALTER TABLE `T067` DISABLE KEYS */;
/*!40000 ALTER TABLE `T067` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T068`
--

-- DROP TABLE IF EXISTS `T068`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T068` (
  `T068_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T068_Legislacao` text,
  `T068_Data_Inicio` date DEFAULT NULL,
  `T068_Data_Final` date DEFAULT NULL,
  PRIMARY KEY (`T068_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T068`
--

LOCK TABLES `T068` WRITE;
/*!40000 ALTER TABLE `T068` DISABLE KEYS */;
/*!40000 ALTER TABLE `T068` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T069`
--

-- DROP TABLE IF EXISTS `T069`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T069` (
  `T069_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T069_T068_Id` int(10) DEFAULT NULL,
  `T069_Empresa` varchar(20) DEFAULT NULL,
  `T069_Faixa_Inicial` decimal(14,2) DEFAULT NULL,
  `T069_Faixa_Final` decimal(14,2) DEFAULT NULL,
  `T069_Aliquota` decimal(5,2) DEFAULT NULL,
  `T069_Valor_Deduzir` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T069_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T069`
--

LOCK TABLES `T069` WRITE;
/*!40000 ALTER TABLE `T069` DISABLE KEYS */;
/*!40000 ALTER TABLE `T069` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T070`
--

-- DROP TABLE IF EXISTS `T070`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T070` (
  `T070_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T070_Observacao` text,
  `T070_Legislacao` text,
  PRIMARY KEY (`T070_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T070`
--

LOCK TABLES `T070` WRITE;
/*!40000 ALTER TABLE `T070` DISABLE KEYS */;
/*!40000 ALTER TABLE `T070` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T071`
--

-- DROP TABLE IF EXISTS `T071`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T071` (
  `T071_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T071_T048_Id` int(10) DEFAULT NULL,
  `T071_D009_Id` int(10) DEFAULT NULL,
  `T071_Quantidade` decimal(10,2) DEFAULT NULL,
  `T071_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T071_Observacao` text,
  PRIMARY KEY (`T071_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T071`
--

LOCK TABLES `T071` WRITE;
/*!40000 ALTER TABLE `T071` DISABLE KEYS */;
/*!40000 ALTER TABLE `T071` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T072`
--

-- DROP TABLE IF EXISTS `T072`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T072` (
  `T072_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T072_T013_Id` int(10) unsigned DEFAULT NULL,
  `T072_T014_Id` int(10) unsigned DEFAULT NULL,
  `T072_T007_Id` int(10) unsigned DEFAULT NULL,
  `T072_T008_Id` int(10) unsigned DEFAULT NULL,
  `T072_T054_Id` int(10) unsigned DEFAULT NULL,
  `T072_T055_Id` int(10) unsigned DEFAULT NULL,
  `T072_D024_Id` int(10) unsigned DEFAULT NULL,
  `T072_D024_Id_Cliente` int(10) unsigned DEFAULT NULL,
  `T072_D009_Id` int(10) unsigned DEFAULT NULL,
  `T072_D009_Id_Original` int(10) unsigned DEFAULT NULL,
  `T072_D001_Id` int(10) unsigned DEFAULT NULL,
  `T072_T048_Id` int(10) unsigned DEFAULT NULL,
  `T072_T066_Id` int(10) unsigned DEFAULT NULL,
  `T072_D006_Id` int(10) unsigned DEFAULT NULL,
  `T072_T029_Id` int(10) unsigned DEFAULT NULL,
  `T072_T030_Id` int(10) unsigned DEFAULT NULL,
  `T072_C004_Id` int(11) DEFAULT NULL,
  `T072_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T072_Data_Lancamento` date DEFAULT NULL,
  `T072_Descricao_Operacao` varchar(20) DEFAULT NULL,
  `T072_Quantidade` int(6) NOT NULL DEFAULT '0',
  `T072_Quantidade_Saldo` int(6) NOT NULL DEFAULT '0',
  `T072_Flag_Operacao` varchar(1) DEFAULT NULL,
  `T072_Valor_Preco_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T072_Observacao` varchar(40) DEFAULT NULL,
  `T072_Valor_Custo_Unitario` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T072_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T072_Codigo_Cliente` varchar(6) DEFAULT NULL,
  `T072_Codigo_Natureza` decimal(5,3) NOT NULL DEFAULT '0.000',
  `T072_Numero_Nota_Fiscal` varchar(20) DEFAULT NULL,
  `T072_Motivo` varchar(10) DEFAULT NULL,
  `T072_Flag_Complemento` varchar(1) DEFAULT NULL,
  `T072_Demonstrativo_Custo` text,
  `T072_Flag_MMF` varchar(1) DEFAULT NULL,
  `T072_Flag_Atualiza_Custo` varchar(1) DEFAULT NULL,
  `T072_Flag_Atualizado` varchar(1) DEFAULT 'N',
  `T072_Flag_Venda_Compra_Outros` varchar(1) DEFAULT NULL,
  `T072_Flag_Devolucao` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`T072_Id`),
  KEY `T072_Index_C004_Id` (`T072_C004_Id`),
  KEY `T072_Index_Nota_Entrada` (`T072_T013_Id`),
  KEY `T072_Index_Itens_Nota_Entrada` (`T072_T014_Id`),
  KEY `T072_Index_Nota_Saida` (`T072_T007_Id`),
  KEY `T072_Index_Itens_Nota_Saida` (`T072_T008_Id`),
  KEY `T072_Index_Importacao` (`T072_T054_Id`),
  KEY `T072_Index_Itens_Importacao` (`T072_T055_Id`),
  KEY `T072_Index_Codigo_Produto` (`T072_Codigo_Produto`),
  KEY `T072_Index_Codigo_Fornecedor` (`T072_Codigo_Fornecedor`),
  KEY `T072_Index_Descricao_Operacao` (`T072_Descricao_Operacao`),
  KEY `T072_Index_Fornecedores` (`T072_D024_Id`),
  KEY `T072_Index_Clientes` (`T072_D024_Id_Cliente`),
  KEY `T072_Index_Estoque` (`T072_D009_Id`),
  KEY `T072_Index_Estoque_Original` (`T072_D009_Id_Original`),
  KEY `T072_Index_Montagem` (`T072_T048_Id`),
  KEY `T072_Index_Estoque2` (`T072_T066_Id`),
  KEY `T072_Index_Natureza_Operacao` (`T072_D006_Id`),
  KEY `T072_Index_Historico` (`T072_D001_Id`),
  KEY `T072_Index_DE` (`T072_T029_Id`),
  KEY `T072_Index_Itens_DE` (`T072_T030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T072`
--

LOCK TABLES `T072` WRITE;
/*!40000 ALTER TABLE `T072` DISABLE KEYS */;
/*!40000 ALTER TABLE `T072` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T073`
--

-- DROP TABLE IF EXISTS `T073`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T073` (
  `T073_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T073_D024_Id` int(11) NOT NULL DEFAULT '0',
  `T073_D044_Id` int(11) NOT NULL DEFAULT '0',
  `T073_T054_Id` int(10) DEFAULT NULL,
  `T073_Data_Emissao_BL` date DEFAULT NULL,
  `T073_Numero_BL` varchar(20) DEFAULT NULL,
  `T073_Peso_Kg_Bruto` decimal(19,3) DEFAULT '0.000',
  `T073_Peso_Lb_Bruto` decimal(19,3) DEFAULT '0.000',
  `T073_Valor_Total_BL` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T073_Flag_Fechado` varchar(1) DEFAULT 'N',
  `T073_Flag_Pago` varchar(1) DEFAULT 'N',
  `T073_Transportadora` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`T073_Id`),
  KEY `T073_Index_D024` (`T073_D024_Id`),
  KEY `T073_Index_D044` (`T073_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T073`
--

LOCK TABLES `T073` WRITE;
/*!40000 ALTER TABLE `T073` DISABLE KEYS */;
/*!40000 ALTER TABLE `T073` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T074`
--

-- DROP TABLE IF EXISTS `T074`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T074` (
  `T074_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T074_T073_Id` int(11) NOT NULL DEFAULT '0',
  `T074_D057_Id` int(11) NOT NULL DEFAULT '0',
  `T074_Descricao` varchar(30) DEFAULT NULL,
  `T074_Valor` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`T074_Id`),
  KEY `T074_Index_T054` (`T074_D057_Id`),
  KEY `T074_Index_T075` (`T074_T073_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T074`
--

LOCK TABLES `T074` WRITE;
/*!40000 ALTER TABLE `T074` DISABLE KEYS */;
/*!40000 ALTER TABLE `T074` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T075`
--

-- DROP TABLE IF EXISTS `T075`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T075` (
  `T075_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T075_T054_Id` int(11) NOT NULL DEFAULT '0',
  `T075_Data_Emissao_DI` date DEFAULT NULL,
  `T075_Numero_DI` varchar(30) DEFAULT NULL,
  `T075_Valor_Produtos` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_Frete` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_CIF` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_THC` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_SISCOMEX` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_Imposto_Importacao` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_IPI` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_PIS` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_COFINS` decimal(14,2) DEFAULT '0.00',
  `T075_Valor_DI` decimal(14,2) DEFAULT '0.00',
  PRIMARY KEY (`T075_Id`),
  KEY `T075_Index_T054` (`T075_T054_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T075`
--

LOCK TABLES `T075` WRITE;
/*!40000 ALTER TABLE `T075` DISABLE KEYS */;
/*!40000 ALTER TABLE `T075` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T076`
--

-- DROP TABLE IF EXISTS `T076`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T076` (
  `T076_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T076_T054_Id` int(11) NOT NULL DEFAULT '0',
  `T076_D024_Id` int(11) NOT NULL DEFAULT '0',
  `T076_D044_Id` int(11) NOT NULL DEFAULT '0',
  `T076_D014_Id` int(10) DEFAULT NULL,
  `T076_D031_Id` int(10) DEFAULT NULL,
  `T076_Data_Emissao_Recibo` date DEFAULT NULL,
  `T076_Numero_Recibo` varchar(20) DEFAULT NULL,
  `T076_Valor_Recibo` decimal(14,2) NOT NULL DEFAULT '0.00',
  `T076_Flag_Tipo` varchar(15) DEFAULT NULL,
  `T076_Flag_Pagamento_Adiantado` varchar(1) DEFAULT NULL,
  `T076_Flag_Fechado` varchar(1) DEFAULT NULL,
  `T076_Data_Vencimento` date DEFAULT NULL,
  `T076_Observacao` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`T076_Id`),
  KEY `T076_Index_T076` (`T076_T054_Id`),
  KEY `T076_Index_D024` (`T076_D024_Id`),
  KEY `T076_Index_D044` (`T076_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T076`
--

LOCK TABLES `T076` WRITE;
/*!40000 ALTER TABLE `T076` DISABLE KEYS */;
/*!40000 ALTER TABLE `T076` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T077`
--

-- DROP TABLE IF EXISTS `T077`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T077` (
  `T077_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T077_Mes` varchar(2) DEFAULT NULL,
  `T077_Ano` varchar(4) DEFAULT NULL,
  `T077_Valor_Venda_Bruta` decimal(14,2) DEFAULT NULL,
  `T077_Valor_Devolucoes` decimal(14,2) DEFAULT NULL,
  `T077_Valor_Venda_Liquida` decimal(14,2) DEFAULT NULL,
  `T077_Aliquota` decimal(10,2) DEFAULT NULL,
  `T077_Valor_Base_Calculo` decimal(14,2) DEFAULT NULL,
  `T077_Valor_Deducao` decimal(14,2) DEFAULT NULL,
  `T077_Valor_Simples` decimal(14,2) DEFAULT NULL,
  `T077_C004_Id` int(10) DEFAULT NULL,
  PRIMARY KEY (`T077_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T077`
--

LOCK TABLES `T077` WRITE;
/*!40000 ALTER TABLE `T077` DISABLE KEYS */;
/*!40000 ALTER TABLE `T077` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T078`
--

-- DROP TABLE IF EXISTS `T078`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T078` (
  `T078_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T078_C004_Id` int(10) DEFAULT NULL,
  `T078_Mes` varchar(2) DEFAULT NULL,
  `T078_Ano` varchar(4) DEFAULT NULL,
  `T078_Valor_Venda_Bruta` decimal(14,2) DEFAULT NULL,
  `T078_Valor_Devolucoes` decimal(14,2) DEFAULT NULL,
  `T078_Valor_Venda_Liquida` decimal(14,2) DEFAULT NULL,
  `T078_Valor_Acumulado` decimal(14,2) DEFAULT NULL,
  `T078_Aliquota` decimal(10,2) DEFAULT NULL,
  `T078_Valor_Simples` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`T078_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T078`
--

LOCK TABLES `T078` WRITE;
/*!40000 ALTER TABLE `T078` DISABLE KEYS */;
/*!40000 ALTER TABLE `T078` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T079`
--

-- DROP TABLE IF EXISTS `T079`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T079` (
  `T079_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T079_D009_Id` int(11) NOT NULL DEFAULT '0',
  `T079_Mes` varchar(2) NOT NULL DEFAULT '00',
  `T079_Ano` varchar(4) NOT NULL DEFAULT '0000',
  `T079_Quantidade_Estoque` int(6) NOT NULL DEFAULT '0',
  `T079_Valor_Custo_Medio_Unitario` decimal(14,2) DEFAULT '0.00',
  `T079_Valor_Custo_Medio_Total` decimal(14,2) DEFAULT '0.00',
  `T079_Valor_Custo_Unitario` decimal(14,2) DEFAULT '0.00',
  `T079_Valor_Custo_Total` decimal(14,2) DEFAULT '0.00',
  PRIMARY KEY (`T079_Id`),
  KEY `T079_Index_D009` (`T079_D009_Id`),
  KEY `T079_Index_Mes` (`T079_Mes`),
  KEY `T079_Index_Ano` (`T079_Ano`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T079`
--

LOCK TABLES `T079` WRITE;
/*!40000 ALTER TABLE `T079` DISABLE KEYS */;
/*!40000 ALTER TABLE `T079` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T080`
--

-- DROP TABLE IF EXISTS `T080`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T080` (
  `T080_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T080_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T080_Mes` varchar(2) DEFAULT NULL,
  `T080_Ano` varchar(4) DEFAULT NULL,
  `T080_PIS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T080_COFINS` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T080_IR_Mercadoria` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T080_IR_MO` decimal(5,2) NOT NULL DEFAULT '0.00',
  `T080_Contribuicao_Social` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`T080_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T080`
--

LOCK TABLES `T080` WRITE;
/*!40000 ALTER TABLE `T080` DISABLE KEYS */;
/*!40000 ALTER TABLE `T080` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T081`
--

-- DROP TABLE IF EXISTS `T081`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T081` (
  `T081_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T081_D009_Id` int(10) unsigned DEFAULT NULL,
  `T081_C004_Id` int(10) unsigned DEFAULT NULL,
  `T081_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T081_Observacao` varchar(200) DEFAULT NULL,
  `T081_Data` date DEFAULT NULL,
  `T081_Descricao_Falha` text,
  `T081_Nome_Empresa` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`T081_Id`),
  KEY `T081_FKIndex1` (`T081_D009_Id`),
  KEY `T081_FKIndex2` (`T081_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T081`
--

LOCK TABLES `T081` WRITE;
/*!40000 ALTER TABLE `T081` DISABLE KEYS */;
/*!40000 ALTER TABLE `T081` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T082`
--

-- DROP TABLE IF EXISTS `T082`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T082` (
  `T082_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T082_C004_Id` int(10) unsigned DEFAULT NULL,
  `T082_Referencia` varchar(30) DEFAULT NULL,
  `T082_Nome_Empresa` varchar(50) DEFAULT NULL,
  `T082_Modulo` varchar(30) DEFAULT NULL,
  `T082_Descricao_Falha` text,
  `T082_Data` date DEFAULT NULL,
  PRIMARY KEY (`T082_Id`),
  KEY `T082_FKIndex1` (`T082_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T082`
--

LOCK TABLES `T082` WRITE;
/*!40000 ALTER TABLE `T082` DISABLE KEYS */;
/*!40000 ALTER TABLE `T082` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T083`
--

-- DROP TABLE IF EXISTS `T083`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T083` (
  `T083_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T083_D003_Id` int(10) DEFAULT NULL,
  `T083_C008_Id` int(10) DEFAULT NULL,
  `T083_C004_Id` int(10) DEFAULT NULL,
  `T083_C007_Id` int(10) DEFAULT NULL,
  `T083_Mes` varchar(2) DEFAULT NULL,
  `T083_Ano` varchar(4) DEFAULT NULL,
  `T083_Percentual_Comissao` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`T083_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T083`
--

LOCK TABLES `T083` WRITE;
/*!40000 ALTER TABLE `T083` DISABLE KEYS */;
/*!40000 ALTER TABLE `T083` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T084`
--

-- DROP TABLE IF EXISTS `T084`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T084` (
  `T084_Id` int(5) NOT NULL AUTO_INCREMENT,
  `T084_C004_Id` int(11) DEFAULT '0',
  `T084_C007_Id` int(11) DEFAULT '0',
  `T084_D022_Id` int(11) DEFAULT '0',
  `T084_D024_Id` int(11) DEFAULT '0',
  `T084_D006_Id` int(11) DEFAULT NULL,
  `T084_Cnpj_Transportadora` varchar(20) DEFAULT NULL,
  `T084_Numero_Conhecimento` varchar(20) DEFAULT NULL,
  `T084_Valor_Conhecimento` decimal(10,2) DEFAULT '0.00',
  `T084_Data_Emissao` date DEFAULT NULL,
  `T084_Data_Vencimento` date DEFAULT NULL,
  `T084_Flag_Fechado` varchar(1) DEFAULT 'N',
  `T084_Aliquota_ICMS` decimal(6,2) DEFAULT '0.00',
  `T084_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T084_Valor_ICMS` decimal(10,2) DEFAULT '0.00',
  `T084_Data_Lancamento` date DEFAULT NULL,
  `T084_Flag_Cancelado` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T084_Id`),
  KEY `T084_Index_C004` (`T084_C004_Id`),
  KEY `T084_Index_C007` (`T084_C007_Id`),
  KEY `T084_Index_D022` (`T084_D022_Id`),
  KEY `T084_Index_D024` (`T084_D024_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2974 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T084`
--

LOCK TABLES `T084` WRITE;
/*!40000 ALTER TABLE `T084` DISABLE KEYS */;
/*!40000 ALTER TABLE `T084` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T085`
--

-- DROP TABLE IF EXISTS `T085`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T085` (
  `T085_Id` int(5) NOT NULL AUTO_INCREMENT,
  `T085_T013_Id` int(11) DEFAULT '0',
  `T085_T084_Id` int(11) DEFAULT '0',
  `T085_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T085_Valor_Total_Nota` decimal(14,2) DEFAULT '0.00',
  `T085_Valor_Rateio` decimal(14,2) DEFAULT '0.00',
  `T085_Flag_Fechado` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`T085_Id`),
  KEY `T084_Index_T084` (`T085_T084_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4625 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T085`
--

LOCK TABLES `T085` WRITE;
/*!40000 ALTER TABLE `T085` DISABLE KEYS */;
/*!40000 ALTER TABLE `T085` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T086`
--

-- DROP TABLE IF EXISTS `T086`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T086` (
  `T086_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T086_T012_Id` int(10) unsigned DEFAULT NULL,
  `T086_T013_Id` int(10) unsigned DEFAULT NULL,
  `T086_C004_Id` int(10) unsigned DEFAULT NULL,
  `T086_T008_Id` int(10) unsigned DEFAULT NULL,
  `T086_Quantidade` decimal(14,5) DEFAULT '0.00000',
  `T086_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T086_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`T086_Id`),
  KEY `T086_FKIndex1` (`T086_T012_Id`),
  KEY `T086_FKIndex2` (`T086_T013_Id`),
  KEY `T086_FKIndex3` (`T086_T008_Id`),
  KEY `T086_FKIndex4` (`T086_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T086`
--

LOCK TABLES `T086` WRITE;
/*!40000 ALTER TABLE `T086` DISABLE KEYS */;
/*!40000 ALTER TABLE `T086` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T087`
--

-- DROP TABLE IF EXISTS `T087`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T087` (
  `T087_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T087_D024_Id` int(10) unsigned DEFAULT NULL,
  `T087_C004_Id` int(10) unsigned DEFAULT NULL,
  `T087_C007_Id` int(10) unsigned DEFAULT NULL,
  `T087_Flag_Tipo` varchar(20) DEFAULT NULL,
  `T087_Flag_Canal` varchar(20) DEFAULT NULL,
  `T087_Departamento` varchar(50) DEFAULT NULL,
  `T087_Texto` text,
  `T087_Data_Hora_Inclusao` datetime DEFAULT NULL,
  `T087_Flag_Alerta` varchar(1) DEFAULT 'N',
  `T087_Flag_Tipo_Inclusao` varchar(30) DEFAULT NULL,
  `T087_Flag_Retorno` varchar(1) DEFAULT 'N',
  `T087_Texto_Retorno` text,
  `T087_Data_Retorno` date DEFAULT NULL,
  PRIMARY KEY (`T087_Id`),
  KEY `T087_FKIndex1` (`T087_D024_Id`),
  KEY `T087_FKIndex2` (`T087_C004_Id`),
  KEY `T087_FKIndex3` (`T087_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T087`
--

LOCK TABLES `T087` WRITE;
/*!40000 ALTER TABLE `T087` DISABLE KEYS */;
/*!40000 ALTER TABLE `T087` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T088`
--

-- DROP TABLE IF EXISTS `T088`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T088` (
  `T088_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T088_T087_Id` int(10) unsigned DEFAULT NULL,
  `T088_Nome_Arquivo` varchar(200) DEFAULT NULL,
  `T088_Arquivo` longtext,
  PRIMARY KEY (`T088_Id`),
  KEY `T088_FKIndex1` (`T088_T087_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T088`
--

LOCK TABLES `T088` WRITE;
/*!40000 ALTER TABLE `T088` DISABLE KEYS */;
/*!40000 ALTER TABLE `T088` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T089`
--

-- DROP TABLE IF EXISTS `T089`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T089` (
  `T089_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T089_T003_Id` int(11) DEFAULT '0',
  `T089_D027_Id` int(11) DEFAULT '0',
  `T089_Numero_Parcela` int(3) DEFAULT '0',
  `T089_Prazos_Dias` int(5) DEFAULT '0',
  `T089_Data_Vencimento` date DEFAULT NULL,
  `T089_Valor_Parcela` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`T089_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T089`
--

LOCK TABLES `T089` WRITE;
/*!40000 ALTER TABLE `T089` DISABLE KEYS */;
/*!40000 ALTER TABLE `T089` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T090`
--

-- DROP TABLE IF EXISTS `T090`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T090` (
  `T090_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T090_T005_Id` int(11) DEFAULT '0',
  `T090_D027_Id` int(11) DEFAULT '0',
  `T090_Numero_Parcela` int(3) DEFAULT '0',
  `T090_Prazos_Dias` int(5) DEFAULT '0',
  `T090_Data_Vencimento` date DEFAULT NULL,
  `T090_Valor_Parcela` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`T090_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T090`
--

LOCK TABLES `T090` WRITE;
/*!40000 ALTER TABLE `T090` DISABLE KEYS */;
/*!40000 ALTER TABLE `T090` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T091`
--

-- DROP TABLE IF EXISTS `T091`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T091` (
  `T091_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T091_D009_Id` int(11) DEFAULT NULL,
  `T091_T066_Id` int(11) DEFAULT NULL,
  `T091_Data_Hora_Alteracao` datetime DEFAULT NULL,
  `T091_Local_Antigo` varchar(10) DEFAULT NULL,
  `T091_Local_Novo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`T091_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=13877 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T091`
--

LOCK TABLES `T091` WRITE;
/*!40000 ALTER TABLE `T091` DISABLE KEYS */;
/*!40000 ALTER TABLE `T091` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T092`
--

-- DROP TABLE IF EXISTS `T092`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T092` (
  `T092_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T092_C004_Id` int(10) unsigned DEFAULT NULL,
  `T092_HEADER_Identificacao_Arquivo_Retorno` int(2) DEFAULT NULL,
  `T092_Nome_Arquivo` varchar(15) DEFAULT NULL,
  `T092_Data_Importacao` datetime DEFAULT NULL,
  `T092_Data_Processamento` datetime DEFAULT NULL,
  `T092_HEADER_Literal_Retorno` varchar(7) DEFAULT NULL,
  `T092_HEADER_Codigo_Servico` int(2) DEFAULT NULL,
  `T092_HEADER_Literal_Servico` varchar(15) DEFAULT NULL,
  `T092_HEADER_Codigo_Empresa` int(20) DEFAULT NULL,
  `T092_HEADER_Nome_Empresa` varchar(30) DEFAULT NULL,
  `T092_HEADER_Numero_Bradesco` int(3) DEFAULT NULL,
  `T092_HEADER_Nome_Banco` varchar(15) DEFAULT NULL,
  `T092_HEADER_Data_Gravacao_Arquivo` date DEFAULT NULL,
  `T092_HEADER_Numero_Aviso_Bancario` int(5) DEFAULT NULL,
  `T092_HEADER_Data_Credito` datetime DEFAULT NULL,
  `T092_HEADER_Sequencial_Registro` int(6) DEFAULT NULL,
  `T092_HEADER_String` text,
  `T092_TRAILLER_Identificacao_Registro` int(1) DEFAULT NULL,
  `T092_TRAILLER_Identificacao_Retorno` int(2) DEFAULT NULL,
  `T092_TRAILLER_Identificacao_Tipo_Registro` int(2) DEFAULT NULL,
  `T092_TRAILLER_Codigo_Banco` int(3) DEFAULT NULL,
  `T092_TRAILLER_Quantidade_Titulos_Cobranca` int(8) DEFAULT NULL,
  `T092_TRAILLER_Valor_Total_Cobranca` int(14) DEFAULT NULL,
  `T092_TRAILLER_Numero_Aviso_Bancario` int(8) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_02` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_02` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_06` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_06` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_060910` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_0910` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_0910` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_13` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_13` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_14` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_14` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_12` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_12` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_19` int(5) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_19` decimal(15,2) DEFAULT NULL,
  `T092_TRAILLER_Vlr_Total_Rateios_Efetuados` decimal(17,2) DEFAULT NULL,
  `T092_TRAILLER_Qtd_Total_Rateios_Efetuados` int(8) DEFAULT NULL,
  `T092_TRAILLER_Numero_Sequencial_Registro` int(6) DEFAULT NULL,
  `T092_TRAILLER_String` text,
  PRIMARY KEY (`T092_Id`),
  KEY `T092_Index_C004_Id` (`T092_C004_Id`),
  CONSTRAINT `FK_T092_C004_Id` FOREIGN KEY (`T092_C004_Id`) REFERENCES `C004` (`C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bradesco Retorno Header e Trailler';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T092`
--

LOCK TABLES `T092` WRITE;
/*!40000 ALTER TABLE `T092` DISABLE KEYS */;
/*!40000 ALTER TABLE `T092` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T093`
--

-- DROP TABLE IF EXISTS `T093`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T093` (
  `T093_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T093_T092_Id` int(11) DEFAULT NULL,
  `T093_T002_Id` int(10) unsigned DEFAULT NULL,
  `T093_C004_Id` int(10) unsigned DEFAULT NULL,
  `T093_Identificacao_Registro` int(1) DEFAULT NULL,
  `T093_Tipo_Inscricao_Empresa` int(2) DEFAULT NULL,
  `T093_Tipo_Inscricao_Empresa_Descricao` varchar(10) DEFAULT NULL,
  `T093_Numero_Inscricao_Empresa` varchar(14) DEFAULT NULL,
  `T093_Identificacao_Empresa_Cedente_Banco` varchar(17) DEFAULT NULL,
  `T093_Numero_Controle_Participante` varchar(25) DEFAULT NULL,
  `T093_Nosso_Numero` varchar(20) DEFAULT NULL,
  `T093_Identificacao_Rateio_Credito` char(1) DEFAULT NULL,
  `T093_Carteira` int(1) DEFAULT NULL,
  `T093_Identificacao_Ocorrencia` int(2) DEFAULT NULL,
  `T093_Identificacao_Ocorrencia_msg` varchar(60) DEFAULT NULL,
  `T093_Data_Ocorrencia_Banco` date DEFAULT NULL,
  `T093_Numero_Documento` varchar(10) DEFAULT NULL,
  `T093_Data_Vencimento_Titulo` date DEFAULT NULL,
  `T093_Valor_Titulo` decimal(15,2) DEFAULT NULL,
  `T093_Banco_Cobrador` int(3) DEFAULT NULL,
  `T093_Agencia_Cobradora` int(5) DEFAULT NULL,
  `T093_Especie_Titulo` varchar(2) DEFAULT NULL,
  `T093_Valor_Despesas_Cobranca_Ocorrencia_0228` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Outras_despesas_custas_protesto` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Juros_Operacao_Atraso` decimal(15,2) DEFAULT NULL,
  `T093_Valor_IOF_Devido` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Abatimento_Concedido_Titulo` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Desconto_Concedido` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Pago` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Juros_Mora` decimal(15,2) DEFAULT NULL,
  `T093_Valor_Outros_Creditos` decimal(15,2) DEFAULT NULL,
  `T093_Motivo_Codigo_Ocorrencia_19` char(1) DEFAULT NULL,
  `T093_Motivo_Codigo_Ocorrencia_19_msg` varchar(10) DEFAULT NULL,
  `T093_Data_Credito` date DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_01` int(2) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_01_msg` varchar(80) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_02` int(2) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_02_msg` varchar(80) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_03` int(2) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_03_msg` varchar(80) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_04` int(2) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_04_msg` varchar(80) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_05` int(2) DEFAULT NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_05_msg` varchar(80) DEFAULT NULL,
  `T093_Sequencial_Registro` smallint(6) DEFAULT NULL,
  `T093_Numero_Linha` smallint(6) DEFAULT NULL,
  `T093_Flag_Situacao` smallint(6) DEFAULT NULL,
  `T093_Descricao_Situacao` varchar(50) DEFAULT NULL,
  `T093_String` text,
  PRIMARY KEY (`T093_Id`),
  KEY `T093_Index_T092_Id` (`T093_T092_Id`),
  KEY `T093_Index_T002_Id` (`T093_T002_Id`),
  KEY `T093_Index_C004_Id` (`T093_C004_Id`),
  CONSTRAINT `FK_T093_C004_Id` FOREIGN KEY (`T093_C004_Id`) REFERENCES `C004` (`C004_Id`),
  CONSTRAINT `FK_T093_T002_Id` FOREIGN KEY (`T093_T002_Id`) REFERENCES `T002` (`T002_Id`),
  CONSTRAINT `FK_T093_T092_Id` FOREIGN KEY (`T093_T092_Id`) REFERENCES `T092` (`T092_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bradesco Retorno DETALHE';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T093`
--

LOCK TABLES `T093` WRITE;
/*!40000 ALTER TABLE `T093` DISABLE KEYS */;
/*!40000 ALTER TABLE `T093` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T094`
--

-- DROP TABLE IF EXISTS `T094`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T094` (
  `T094_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T094_D024_Id` int(10) unsigned DEFAULT NULL,
  `T094_D064_Id` int(11) DEFAULT NULL,
  `T094_Data_Pesquisa` date DEFAULT NULL,
  `T094_Contato` varchar(50) DEFAULT NULL,
  `T094_Telefone` varchar(50) DEFAULT NULL,
  `T094_Valor_Compra` double(15,2) DEFAULT NULL,
  `T094_Observacao` text,
  PRIMARY KEY (`T094_Id`),
  KEY `FK_T094_D024_Id` (`T094_D024_Id`),
  KEY `FK_T094_D064_Id` (`T094_D064_Id`),
  CONSTRAINT `FK_T094_D024_Id` FOREIGN KEY (`T094_D024_Id`) REFERENCES `D024` (`D024_Id`),
  CONSTRAINT `FK_T094_D064_Id` FOREIGN KEY (`T094_D064_Id`) REFERENCES `D064` (`D064_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Cabecalho das Respostas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T094`
--

LOCK TABLES `T094` WRITE;
/*!40000 ALTER TABLE `T094` DISABLE KEYS */;
/*!40000 ALTER TABLE `T094` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T095`
--

-- DROP TABLE IF EXISTS `T095`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T095` (
  `T095_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T095_D066_Id` int(11) DEFAULT '0' COMMENT 'Alternativa - Pode ser nulo quando a questao tem apenas uma resposta descritiva',
  `T095_D065_Id` int(11) DEFAULT '0' COMMENT 'Questao - Existe para identificar a questao que nao tem alternativas.',
  `T095_T094_Id` int(11) NOT NULL DEFAULT '0' COMMENT 'Cabecalho das Respostas',
  `T095_Resposta` text NOT NULL COMMENT 'Resposta',
  PRIMARY KEY (`T095_Id`),
  KEY `FK_T095_D066_Id` (`T095_D066_Id`),
  KEY `FK_T095_D065_Id` (`T095_D065_Id`),
  KEY `FK_T095_T094_Id` (`T095_T094_Id`),
  CONSTRAINT `FK_T095_D065_Id` FOREIGN KEY (`T095_D065_Id`) REFERENCES `D065` (`D065_Id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_T095_D066_Id` FOREIGN KEY (`T095_D066_Id`) REFERENCES `D066` (`D066_Id`),
  CONSTRAINT `FK_T095_T094_Id` FOREIGN KEY (`T095_T094_Id`) REFERENCES `T094` (`T094_Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Respostas as Alternativas das Questoes da Enquete';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T095`
--

LOCK TABLES `T095` WRITE;
/*!40000 ALTER TABLE `T095` DISABLE KEYS */;
/*!40000 ALTER TABLE `T095` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T096`
--

-- DROP TABLE IF EXISTS `T096`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T096` (
  `T096_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T096_D021_Id` int(10) unsigned DEFAULT NULL,
  `T096_T015_Id` int(10) unsigned DEFAULT NULL COMMENT 'Este ID sera preenchido pela trigger do T015 apos a insercao na T015',
  `T096_C004_Id` int(10) unsigned DEFAULT NULL,
  `T096_Flag_Finalizado` enum('S','N') NOT NULL DEFAULT 'N',
  `T096_Data_Inclusao` date DEFAULT NULL,
  `T096_Data_Finalizado` date DEFAULT NULL,
  `T096_Valor_Total` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Este valor é atualizado numa trigger do T097',
  PRIMARY KEY (`T096_Id`),
  KEY `index_T096_T015_Id` (`T096_T015_Id`),
  KEY `FK_T096_D021_Id` (`T096_D021_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabela de Comissoes de Representantes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T096`
--

LOCK TABLES `T096` WRITE;
/*!40000 ALTER TABLE `T096` DISABLE KEYS */;
/*!40000 ALTER TABLE `T096` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T097`
--

-- DROP TABLE IF EXISTS `T097`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T097` (
  `T097_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T097_T096_Id` int(11) DEFAULT NULL,
  `T097_T015_Id` int(10) unsigned DEFAULT NULL COMMENT 'Este campo T015_Id é referente ao debito do item cadastrado. Ou seja diferente da tabela Mestre.',
  `T097_T002_Id` int(10) unsigned DEFAULT NULL,
  `T097_Data` date DEFAULT NULL,
  `T097_Observacao` text,
  `T097_Flag_Debito_Credito` enum('D','C') NOT NULL DEFAULT 'C',
  `T097_Valor_Base_Comissao` decimal(20,2) unsigned DEFAULT NULL,
  `T097_Valor_Comissao` decimal(15,2) unsigned DEFAULT NULL,
  `T097_Percentual_Comissao` decimal(3,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`T097_Id`),
  KEY `INDEX_T097_T015_Id` (`T097_T015_Id`),
  KEY `INDEX_T097_T002_Id` (`T097_T002_Id`),
  KEY `FK_T097_T096` (`T097_T096_Id`),
  CONSTRAINT `FK_T097_T096` FOREIGN KEY (`T097_T096_Id`) REFERENCES `T096` (`T096_Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabela de Itens da T096 Comissoes. Esta tabela ira incluir D';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T097`
--

LOCK TABLES `T097` WRITE;
/*!40000 ALTER TABLE `T097` DISABLE KEYS */;
/*!40000 ALTER TABLE `T097` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T098`
--

-- DROP TABLE IF EXISTS `T098`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T098` (
  `T098_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T098_D049_Id` int(11) DEFAULT NULL,
  `T098_Data` datetime DEFAULT NULL,
  `T098_Valor_Preco_Revenda` decimal(15,5) DEFAULT NULL,
  `T098_Valor_Preco_Consumidor_Atacado` decimal(15,5) DEFAULT NULL,
  `T098_Valor_Preco_Consumidor_Varejo` decimal(15,5) DEFAULT NULL,
  `T098_C007_Id` int(11) DEFAULT NULL,
  `T098_D041_Id` int(11) DEFAULT NULL,
  `T098_Valor_Custo_Unitario` decimal(15,5) DEFAULT '0.00000',
  `T098_Observacao` longtext,
  `T098_Atualizacao` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`T098_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=11958 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T098`
--

LOCK TABLES `T098` WRITE;
/*!40000 ALTER TABLE `T098` DISABLE KEYS */;
/*!40000 ALTER TABLE `T098` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T099`
--

-- DROP TABLE IF EXISTS `T099`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T099` (
  `T099_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T099_D024_Id` int(10) unsigned NOT NULL,
  `T099_D009_Id` int(10) unsigned NOT NULL,
  `T099_D037_Id` decimal(10,3) DEFAULT '0.000',
  `T099_Quantidade` decimal(10,3) DEFAULT '0.000',
  `T099_Data` date DEFAULT '0000-00-00',
  `T099_Valor_Preco_Unitario` decimal(10,2) DEFAULT '0.00',
  `T099_Peso_Unitario` decimal(10,3) DEFAULT '0.000',
  `T099_Horario_Entrega` varchar(10) DEFAULT ' ',
  `T099_Qte_UND` decimal(10,3) DEFAULT '0.000',
  `T099_Qte_PCT` decimal(10,3) DEFAULT '0.000',
  `T099_Qte_KLG` decimal(10,3) DEFAULT '0.000',
  `T099_Flag_Cancelado` varchar(1) DEFAULT ' ',
  `T099_C004_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T099_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T099`
--

LOCK TABLES `T099` WRITE;
/*!40000 ALTER TABLE `T099` DISABLE KEYS */;
/*!40000 ALTER TABLE `T099` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T100`
--

-- DROP TABLE IF EXISTS `T100`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T100` (
  `T100_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T100_C004_Id` int(11) DEFAULT NULL,
  `T100_Data_Reprocessamento` datetime DEFAULT NULL,
  `T100_Data` datetime DEFAULT NULL,
  `T100_Hora_Inicio` time DEFAULT NULL,
  `T100_Hora_Termino` time DEFAULT NULL,
  PRIMARY KEY (`T100_Id`),
  KEY `T100_Data` (`T100_Data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T100`
--

LOCK TABLES `T100` WRITE;
/*!40000 ALTER TABLE `T100` DISABLE KEYS */;
/*!40000 ALTER TABLE `T100` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T101`
--

-- DROP TABLE IF EXISTS `T101`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T101` (
  `T101_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T101_C005_Id` int(10) unsigned DEFAULT NULL,
  `T101_T100_Id` int(10) unsigned DEFAULT NULL,
  `T101_T108_Id` int(10) DEFAULT NULL,
  `T101_C004_Id` int(11) DEFAULT NULL,
  `T101_Campo_Logico` varchar(50) DEFAULT NULL,
  `T101_Campo` varchar(50) DEFAULT NULL,
  `T101_Valor` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`T101_Id`),
  KEY `T101_FKIndex1` (`T101_T100_Id`),
  KEY `T101_FKIndex2` (`T101_C005_Id`),
  KEY `T101_T108_Id` (`T101_T108_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T101`
--

LOCK TABLES `T101` WRITE;
/*!40000 ALTER TABLE `T101` DISABLE KEYS */;
/*!40000 ALTER TABLE `T101` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T102`
--

-- DROP TABLE IF EXISTS `T102`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T102` (
  `T102_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T102_C005_Id` int(10) unsigned DEFAULT NULL,
  `T102_T100_Id` int(10) unsigned DEFAULT NULL,
  `T102_C004_Id` int(11) DEFAULT NULL,
  `T102_T104_Id` int(10) DEFAULT NULL,
  `T102_Campo_Logico` varchar(50) DEFAULT NULL,
  `T102_Campo` varchar(50) DEFAULT NULL,
  `T102_Valor` decimal(19,2) DEFAULT NULL,
  `T102_Data` date DEFAULT NULL,
  PRIMARY KEY (`T102_Id`),
  KEY `T102_FKIndex1` (`T102_T100_Id`),
  KEY `T102_FKIndex2` (`T102_C005_Id`),
  KEY `T102_Data` (`T102_Data`),
  KEY `T102_T104_Id` (`T102_T104_Id`),
  KEY `T102_Id` (`T102_Id`),
  KEY `T102_C004_Id` (`T102_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T102`
--

LOCK TABLES `T102` WRITE;
/*!40000 ALTER TABLE `T102` DISABLE KEYS */;
/*!40000 ALTER TABLE `T102` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T103`
--

-- DROP TABLE IF EXISTS `T103`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T103` (
  `T103_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T103_T015_Id` int(10) unsigned DEFAULT NULL,
  `T103_D031_Id` int(10) unsigned DEFAULT NULL,
  `T103_T002_Id` int(10) unsigned DEFAULT NULL,
  `T103_D027_Id` int(10) unsigned DEFAULT NULL,
  `T103_Flag_Baixar_Lote` enum('S','N') DEFAULT 'N',
  `T103_Valor_Recebido` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T103_Id`),
  KEY `T103_Index_Id` (`T103_Id`),
  KEY `T103_Index_Portador` (`T103_D027_Id`),
  KEY `T103_Index_Receber` (`T103_T002_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T103`
--

LOCK TABLES `T103` WRITE;
/*!40000 ALTER TABLE `T103` DISABLE KEYS */;
/*!40000 ALTER TABLE `T103` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T104`
--

-- DROP TABLE IF EXISTS `T104`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T104` (
  `T104_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T104_T100_Id` int(10) DEFAULT NULL,
  `T104_C005_Id` int(10) unsigned DEFAULT NULL,
  `T104_T107_Id` int(10) DEFAULT NULL,
  `T104_Mes` int(2) unsigned zerofill DEFAULT NULL,
  `T104_Ano` int(4) DEFAULT NULL,
  `T104_Valor` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T104_Id`),
  KEY `Table_76_FKIndex1` (`T104_C005_Id`),
  KEY `T104_Mes` (`T104_Mes`),
  KEY `T104_Ano` (`T104_Ano`),
  KEY `T104_T107_Id` (`T104_T107_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T104`
--

LOCK TABLES `T104` WRITE;
/*!40000 ALTER TABLE `T104` DISABLE KEYS */;
/*!40000 ALTER TABLE `T104` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T105`
--

-- DROP TABLE IF EXISTS `T105`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T105` (
  `T105_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T105_C005_Id` int(10) unsigned DEFAULT NULL,
  `T105_T101_Id` int(10) unsigned DEFAULT NULL,
  `T105_T108_Id` int(10) DEFAULT NULL,
  `T105_Conta_Id` int(10) DEFAULT NULL,
  `T105_Id_Entidade` int(10) unsigned DEFAULT NULL,
  `T105_Data` date DEFAULT NULL,
  `T105_Valor` decimal(19,2) DEFAULT NULL,
  `T105_Referencia` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`T105_Id`),
  KEY `T105_FKIndex1` (`T105_T101_Id`),
  KEY `T105_FKIndex2` (`T105_C005_Id`),
  KEY `T105_Busca` (`T105_T101_Id`,`T105_C005_Id`,`T105_Id_Entidade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T105`
--

LOCK TABLES `T105` WRITE;
/*!40000 ALTER TABLE `T105` DISABLE KEYS */;
/*!40000 ALTER TABLE `T105` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T106`
--

-- DROP TABLE IF EXISTS `T106`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T106` (
  `T106_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T106_C005_Id` int(10) DEFAULT NULL,
  `T106_C006_Id` int(10) DEFAULT NULL,
  PRIMARY KEY (`T106_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T106`
--

LOCK TABLES `T106` WRITE;
/*!40000 ALTER TABLE `T106` DISABLE KEYS */;
/*!40000 ALTER TABLE `T106` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T107`
--

-- DROP TABLE IF EXISTS `T107`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T107` (
  `T107_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T107_T100_Id` int(10) DEFAULT NULL,
  `T107_C005_Id` int(10) DEFAULT NULL,
  `T107_Ano` varchar(4) DEFAULT NULL,
  `T107_Valor` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T107_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=14271 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T107`
--

LOCK TABLES `T107` WRITE;
/*!40000 ALTER TABLE `T107` DISABLE KEYS */;
/*!40000 ALTER TABLE `T107` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T108`
--

-- DROP TABLE IF EXISTS `T108`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T108` (
  `T108_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T108_T100_Id` int(10) DEFAULT NULL,
  `T108_C005_Id` int(10) unsigned DEFAULT NULL,
  `T108_T109_Id` int(10) DEFAULT NULL,
  `T108_C004_Id` int(10) DEFAULT NULL,
  `T108_D101_Id` int(10) DEFAULT NULL,
  `T108_Mes` int(2) unsigned zerofill DEFAULT NULL,
  `T108_Ano` int(4) DEFAULT NULL,
  `T108_Valor` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T108_Id`),
  KEY `Table_76_FKIndex1` (`T108_C005_Id`),
  KEY `T108_Mes` (`T108_Mes`),
  KEY `T108_Ano` (`T108_Ano`),
  KEY `T108_T109_Id` (`T108_T109_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T108`
--

LOCK TABLES `T108` WRITE;
/*!40000 ALTER TABLE `T108` DISABLE KEYS */;
/*!40000 ALTER TABLE `T108` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T109`
--

-- DROP TABLE IF EXISTS `T109`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T109` (
  `T109_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T109_T100_Id` int(10) DEFAULT NULL,
  `T109_C005_Id` int(10) DEFAULT NULL,
  `T109_C004_Id` int(10) DEFAULT NULL,
  `T109_Ano` varchar(4) DEFAULT NULL,
  `T109_Valor` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T109_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=363 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T109`
--

LOCK TABLES `T109` WRITE;
/*!40000 ALTER TABLE `T109` DISABLE KEYS */;
/*!40000 ALTER TABLE `T109` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T110`
--

-- DROP TABLE IF EXISTS `T110`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T110` (
  `T110_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T110_C005_Id` int(10) unsigned DEFAULT NULL,
  `T110_C014_Id` int(10) DEFAULT NULL,
  `T110_T100_Id` int(10) unsigned DEFAULT NULL,
  `T110_T112_Id` int(10) DEFAULT NULL,
  `T110_C004_Id` int(11) DEFAULT NULL,
  `T110_Campo_Logico` varchar(50) DEFAULT NULL,
  `T110_Campo` varchar(50) DEFAULT NULL,
  `T110_Valor` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`T110_Id`),
  KEY `T110_FKIndex1` (`T110_T100_Id`),
  KEY `T110_FKIndex2` (`T110_C005_Id`),
  KEY `T110_T112_Id` (`T110_T112_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T110`
--

LOCK TABLES `T110` WRITE;
/*!40000 ALTER TABLE `T110` DISABLE KEYS */;
/*!40000 ALTER TABLE `T110` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T111`
--

-- DROP TABLE IF EXISTS `T111`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T111` (
  `T111_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T111_T110_Id` int(10) DEFAULT NULL,
  `T111_C005_Id` int(10) unsigned DEFAULT NULL,
  `T111_T112_Id` int(10) DEFAULT NULL,
  `T111_Conta_Id` int(10) DEFAULT NULL,
  `T111_Id_Entidade` int(10) unsigned DEFAULT NULL,
  `T111_Data` date DEFAULT NULL,
  `T111_Valor` decimal(19,2) DEFAULT NULL,
  `T111_Referencia` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`T111_Id`),
  KEY `Table_75_FKIndex1` (`T111_C005_Id`),
  KEY `T111_FKIndex2` (`T111_T110_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T111`
--

LOCK TABLES `T111` WRITE;
/*!40000 ALTER TABLE `T111` DISABLE KEYS */;
/*!40000 ALTER TABLE `T111` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T112`
--

-- DROP TABLE IF EXISTS `T112`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T112` (
  `T112_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T112_T100_Id` int(10) DEFAULT NULL,
  `T112_C005_Id` int(10) unsigned DEFAULT NULL,
  `T112_C014_Id` int(10) DEFAULT NULL,
  `T112_T113_Id` int(10) DEFAULT NULL,
  `T112_Mes` int(2) unsigned zerofill DEFAULT NULL,
  `T112_Ano` int(4) DEFAULT NULL,
  `T112_Valor` decimal(10,2) DEFAULT NULL,
  `T112_Campo` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`T112_Id`),
  KEY `Table_76_FKIndex1` (`T112_C005_Id`),
  KEY `T112_Mes` (`T112_Mes`),
  KEY `T112_Ano` (`T112_Ano`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T112`
--

LOCK TABLES `T112` WRITE;
/*!40000 ALTER TABLE `T112` DISABLE KEYS */;
/*!40000 ALTER TABLE `T112` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T113`
--

-- DROP TABLE IF EXISTS `T113`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T113` (
  `T113_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T113_T100_Id` int(10) DEFAULT NULL,
  `T113_C005_Id` int(10) DEFAULT NULL,
  `T113_C014_Id` int(10) DEFAULT NULL,
  `T113_Ano` varchar(4) DEFAULT NULL,
  `T113_Valor` decimal(10,2) DEFAULT NULL,
  `T113_Campo` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`T113_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T113`
--

LOCK TABLES `T113` WRITE;
/*!40000 ALTER TABLE `T113` DISABLE KEYS */;
/*!40000 ALTER TABLE `T113` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T114`
--

-- DROP TABLE IF EXISTS `T114`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T114` (
  `T114_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T114_D101_Id` int(10) DEFAULT NULL,
  `T114_C004_Id` int(10) DEFAULT NULL,
  `T114_Data` date DEFAULT NULL,
  `T114_Valor_Banco_Anterior` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Recebido` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Pago` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Banco_Final` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Vendas` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Compras` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Venda_Sem_Icms` decimal(10,2) DEFAULT NULL,
  `T114_Valor_CMV` decimal(10,2) DEFAULT NULL,
  `T114_IPV` decimal(10,4) DEFAULT NULL,
  `T114_Valor_Estoque_Normal` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Estoque_Custo_Medio` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Estoque_Dolar` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Estoque_Parado_1_Ano` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Estoque_Parado_2_Ano` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Receber` decimal(10,2) DEFAULT NULL,
  `T114_Valor_Pagar` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T114_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=964 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T114`
--

LOCK TABLES `T114` WRITE;
/*!40000 ALTER TABLE `T114` DISABLE KEYS */;
/*!40000 ALTER TABLE `T114` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T115`
--

-- DROP TABLE IF EXISTS `T115`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T115` (
  `T115_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T115_T116_Id` int(10) DEFAULT NULL,
  `T115_C004_Id` int(10) DEFAULT NULL,
  `T115_Data` date DEFAULT NULL,
  `T115_Mes` varchar(2) DEFAULT NULL,
  `T115_Ano` varchar(4) DEFAULT NULL,
  `T115_Valor_Receber` decimal(10,2) DEFAULT NULL,
  `T115_Valor_Recebido` decimal(10,2) DEFAULT NULL,
  `T115_Valor_Pagar` decimal(10,2) DEFAULT NULL,
  `T115_Valor_Pago` decimal(10,2) DEFAULT NULL,
  `T115_Valor_Saldo_Banco` decimal(10,2) DEFAULT NULL,
  `T115_Valor_Saldo` decimal(10,2) DEFAULT NULL,
  `T115_Valor_Saldo_Acumulado` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`T115_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=15902 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T115`
--

LOCK TABLES `T115` WRITE;
/*!40000 ALTER TABLE `T115` DISABLE KEYS */;
/*!40000 ALTER TABLE `T115` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T116`
--

-- DROP TABLE IF EXISTS `T116`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T116` (
  `T116_Id` int(10) NOT NULL AUTO_INCREMENT,
  `T116_D101_Id` int(10) DEFAULT NULL,
  `T116_C004_Id` int(10) DEFAULT NULL,
  `T116_Data` date DEFAULT NULL,
  `T116_Valor_Receber` decimal(10,2) DEFAULT NULL,
  `T116_Valor_Pagar` decimal(10,2) DEFAULT NULL,
  `T116_Valor_Saldo_Banco` decimal(10,2) DEFAULT NULL,
  `T116_Valor_Saldo` decimal(10,2) DEFAULT NULL,
  `T116_Valor_Diferenca` decimal(10,2) DEFAULT NULL,
  `T116_Observacao` text,
  PRIMARY KEY (`T116_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=870 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T116`
--

LOCK TABLES `T116` WRITE;
/*!40000 ALTER TABLE `T116` DISABLE KEYS */;
/*!40000 ALTER TABLE `T116` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T117`
--

-- DROP TABLE IF EXISTS `T117`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T117` (
  `T117_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T117_T035_Id` int(10) unsigned DEFAULT NULL,
  `T117_C004_Id` int(10) unsigned DEFAULT NULL,
  `T117_D024_Id` int(10) unsigned DEFAULT NULL,
  `T117_D007_Id` int(10) unsigned DEFAULT NULL,
  `T117_Data_Emissao` date DEFAULT '0000-00-00',
  `T117_Data_Vencimento` date DEFAULT '0000-00-00',
  `T117_Data_Pagamento` date DEFAULT '0000-00-00',
  `T117_Valor_Total` decimal(10,2) DEFAULT '0.00',
  `T117_Instrucoes` text,
  `T117_Codigo_Barras` varchar(200) DEFAULT NULL,
  `T117_Nosso_Numero` varchar(30) DEFAULT NULL,
  `T117_Aceite` enum('S','N') DEFAULT 'N',
  `T117_Especie` varchar(30) DEFAULT NULL,
  `T117_Especie_Documento` varchar(30) DEFAULT NULL,
  `T117_Uso_Banco` varchar(30) DEFAULT NULL,
  `T117_Percentual_Juros_Dia` decimal(10,2) DEFAULT '0.00',
  `T117_Percentual_Multa` decimal(10,2) DEFAULT '0.00',
  `T117_Linha_Digitavel` varchar(100) DEFAULT NULL,
  `T117_Codigo_Cedente` int(10) DEFAULT '0',
  `T117_Flag_Cancelado` enum('S','N') DEFAULT 'N',
  `T117_Flag_Layout` enum('S','N') DEFAULT 'N',
  `T117_Flag_Com_Registro` enum('S','N') DEFAULT 'N',
  `T117_Flag_Enviar_Remessa` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`T117_Id`),
  KEY `Fk_T117_C004` (`T117_C004_Id`),
  KEY `Fk_T117_D007` (`T117_D007_Id`),
  KEY `Fk_T117_D024` (`T117_D024_Id`),
  CONSTRAINT `Fk_T117_C004` FOREIGN KEY (`T117_C004_Id`) REFERENCES `C004` (`C004_Id`),
  CONSTRAINT `Fk_T117_D007` FOREIGN KEY (`T117_D007_Id`) REFERENCES `D007` (`D007_Id`),
  CONSTRAINT `Fk_T117_D024` FOREIGN KEY (`T117_D024_Id`) REFERENCES `D024` (`D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T117`
--

LOCK TABLES `T117` WRITE;
/*!40000 ALTER TABLE `T117` DISABLE KEYS */;
/*!40000 ALTER TABLE `T117` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T118`
--

-- DROP TABLE IF EXISTS `T118`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T118` (
  `T118_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T118_T007_Id` int(10) unsigned DEFAULT '0',
  `T118_D027_Id` int(10) unsigned DEFAULT '0',
  `T118_Numero_Parcela` int(3) DEFAULT '0',
  `T118_Prazos_Dias` int(5) DEFAULT '0',
  `T118_Data_Vencimento` date DEFAULT NULL,
  `T118_Valor_Parcela` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`T118_Id`),
  KEY `Fk_T118_T007` (`T118_T007_Id`),
  CONSTRAINT `Fk_T118_T007` FOREIGN KEY (`T118_T007_Id`) REFERENCES `T007` (`T007_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T118`
--

LOCK TABLES `T118` WRITE;
/*!40000 ALTER TABLE `T118` DISABLE KEYS */;
/*!40000 ALTER TABLE `T118` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T119`
--

-- DROP TABLE IF EXISTS `T119`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T119` (
  `T119_Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `T119_D007_Id` int(10) unsigned DEFAULT NULL,
  `T119_Data_Abertura` datetime DEFAULT '0000-00-00 00:00:00',
  `T119_Data_Fechamento` datetime DEFAULT '0000-00-00 00:00:00',
  `T119_Valor_Saldo_Inicial` decimal(10,2) NOT NULL DEFAULT '0.00',
  `T119_Valor_Saldo_Final` decimal(10,2) DEFAULT '0.00',
  `T119_Observacao` text,
  `T119_C007_Id` int(10) DEFAULT NULL,
  PRIMARY KEY (`T119_Id`),
  KEY `IDX_T119_D007_Id` (`T119_D007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ABERTURA E FECHAMENTO DE CAIXA';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T119`
--

LOCK TABLES `T119` WRITE;
/*!40000 ALTER TABLE `T119` DISABLE KEYS */;
/*!40000 ALTER TABLE `T119` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T120`
--

-- DROP TABLE IF EXISTS `T120`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T120` (
  `T120_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T120_C004_Id` int(10) unsigned DEFAULT NULL,
  `T120_C007_Id` int(11) unsigned DEFAULT NULL,
  `T120_D024_Id` int(10) unsigned DEFAULT NULL,
  `T120_Numero_Contrato` varchar(30) DEFAULT NULL,
  `T120_Data_Contrato` date DEFAULT '0000-00-00',
  `T120_Data_Vencimento` date DEFAULT '0000-00-00',
  `T120_Flag_Ativo` enum('S','N') DEFAULT 'S',
  `T120_Valor_Compra_Contratada` decimal(10,2) DEFAULT '0.00',
  `T120_Observacao` text,
  PRIMARY KEY (`T120_Id`),
  KEY `IDX_T120_D024_Id` (`T120_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CONTRATO DE COMODATO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T120`
--

LOCK TABLES `T120` WRITE;
/*!40000 ALTER TABLE `T120` DISABLE KEYS */;
/*!40000 ALTER TABLE `T120` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T121`
--

-- DROP TABLE IF EXISTS `T121`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T121` (
  `T121_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T121_T120_Id` int(10) unsigned DEFAULT NULL,
  `T121_D009_Id` int(10) unsigned DEFAULT NULL,
  `T121_Data_Inclusao` datetime DEFAULT '0000-00-00 00:00:00',
  `T121_Data_Entrega` date DEFAULT '0000-00-00',
  `T121_Data_Devolucao` date DEFAULT '0000-00-00',
  `T121_Valor` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T121_Id`),
  KEY `IDX_T121_D009_Id` (`T121_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ITENS DO CONTRATO DE COMODATO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T121`
--

LOCK TABLES `T121` WRITE;
/*!40000 ALTER TABLE `T121` DISABLE KEYS */;
/*!40000 ALTER TABLE `T121` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T122`
--

-- DROP TABLE IF EXISTS `T122`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T122` (
  `T122_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T122_C004_Id` int(10) unsigned DEFAULT NULL,
  `T122_C007_Id` int(11) unsigned DEFAULT NULL,
  `T122_D024_Id` int(10) unsigned DEFAULT NULL,
  `T122_Data` date DEFAULT '0000-00-00',
  `T122_Observacao` text,
  PRIMARY KEY (`T122_Id`),
  KEY `IDX_T122_D024_Id` (`T122_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ORDEM DE SERVICO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T122`
--

LOCK TABLES `T122` WRITE;
/*!40000 ALTER TABLE `T122` DISABLE KEYS */;
/*!40000 ALTER TABLE `T122` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T123`
--

-- DROP TABLE IF EXISTS `T123`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T123` (
  `T123_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T123_T122_Id` int(10) unsigned DEFAULT NULL,
  `T123_D009_Id` int(10) unsigned DEFAULT NULL,
  `T123_Data_Inclusao` datetime DEFAULT '0000-00-00 00:00:00',
  `T123_Quantidade` decimal(10,3) DEFAULT '0.000',
  `T123_Valor_Preco` decimal(10,2) DEFAULT '0.00',
  `T123_Descricao` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`T123_Id`),
  KEY `IDX_T123_D009_Id` (`T123_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ITENS DA ORDEM DE SERVICO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T123`
--

LOCK TABLES `T123` WRITE;
/*!40000 ALTER TABLE `T123` DISABLE KEYS */;
/*!40000 ALTER TABLE `T123` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T124`
--

-- DROP TABLE IF EXISTS `T124`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T124` (
  `T124_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T124_D024_Id` int(10) unsigned DEFAULT NULL,
  `T124_Ano` varchar(4) DEFAULT '',
  `T124_Curva_ABC` varchar(1) DEFAULT '',
  `T124_Vendas_01` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_02` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_03` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_04` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_05` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_06` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_07` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_08` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_09` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_10` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_11` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_12` decimal(10,2) DEFAULT '0.00',
  `T124_Vendas_Total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T124_Id`),
  KEY `T124_IDX_PRODUTO` (`T124_D024_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T124`
--

LOCK TABLES `T124` WRITE;
/*!40000 ALTER TABLE `T124` DISABLE KEYS */;
/*!40000 ALTER TABLE `T124` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T125`
--

-- DROP TABLE IF EXISTS `T125`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T125` (
  `T125_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T125_D001_Id` int(10) unsigned DEFAULT NULL,
  `T125_Ano` varchar(4) DEFAULT '',
  `T125_Curva_ABC` varchar(1) DEFAULT '',
  `T125_Vendas_Valor_01` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_02` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_03` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_04` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_05` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_06` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_07` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_08` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_09` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_10` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_11` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_12` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Valor_Total` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_01` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_02` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_03` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_04` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_05` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_06` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_07` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_08` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_09` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_10` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_11` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_12` decimal(10,2) DEFAULT '0.00',
  `T125_Vendas_Qte_Total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T125_Id`),
  KEY `T125_IDX_PRODUTO` (`T125_D001_Id`),
  KEY `T125_IDX_ANO` (`T125_Ano`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T125`
--

LOCK TABLES `T125` WRITE;
/*!40000 ALTER TABLE `T125` DISABLE KEYS */;
/*!40000 ALTER TABLE `T125` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T126`
--

-- DROP TABLE IF EXISTS `T126`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T126` (
  `T126_Id` int(11) NOT NULL AUTO_INCREMENT,
  `T126_C007_Id` int(10) unsigned DEFAULT NULL,
  `T126_Ano` varchar(4) DEFAULT '',
  `T126_Curva_ABC` varchar(1) DEFAULT '',
  `T126_Vendas_01` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_02` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_03` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_04` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_05` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_06` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_07` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_08` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_09` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_10` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_11` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_12` decimal(10,2) DEFAULT '0.00',
  `T126_Vendas_Total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T126_Id`),
  KEY `T126_IDX_PRODUTO` (`T126_C007_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T126`
--

LOCK TABLES `T126` WRITE;
/*!40000 ALTER TABLE `T126` DISABLE KEYS */;
/*!40000 ALTER TABLE `T126` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T127`
--

-- DROP TABLE IF EXISTS `T127`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T127` (
  `T127_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T127_D024_Id` int(10) unsigned DEFAULT NULL,
  `T127_C004_Id` int(10) unsigned DEFAULT NULL,
  `T127_Chave_Acesso_Nfe` varchar(44) DEFAULT NULL,
  `T127_Numero_Nota_Fiscal` varchar(12) DEFAULT NULL,
  `T127_Codigo_Retorno` varchar(3) DEFAULT NULL,
  `T127_Numero_Recibo_Nfe` varchar(20) DEFAULT NULL,
  `T127_Data_Transmicao` datetime DEFAULT '0000-00-00 00:00:00',
  `T127_Retorno_Sefaz` text,
  PRIMARY KEY (`T127_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T127`
--

LOCK TABLES `T127` WRITE;
/*!40000 ALTER TABLE `T127` DISABLE KEYS */;
/*!40000 ALTER TABLE `T127` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T128`
--

-- DROP TABLE IF EXISTS `T128`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T128` (
  `T128_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T128_D022_Id` int(10) unsigned DEFAULT NULL,
  `T128_D024_Id` int(10) unsigned DEFAULT NULL,
  `T128_D036_Id` int(10) unsigned DEFAULT NULL,
  `T128_C007_Id` int(10) unsigned DEFAULT NULL,
  `T128_C008_Id` int(10) unsigned DEFAULT NULL,
  `T128_C004_Id` int(10) unsigned NOT NULL DEFAULT '0',
  `T128_Numero` varchar(10) DEFAULT NULL,
  `T128_Data_Emissao` date DEFAULT NULL,
  `T128_Data_Previsao_Recebimento` date DEFAULT NULL,
  `T128_Data_Previsao_Pagamento` date DEFAULT NULL,
  `T128_Observacao` text,
  `T128_Prazo_Entrega` varchar(50) DEFAULT NULL,
  `T128_Valor_Frete` decimal(10,2) DEFAULT '0.00',
  `T128_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T128_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T128_Valor_Total_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T128_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T128_Valor_Total_ICMS` decimal(10,2) DEFAULT '0.00',
  `T128_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T128_Valor_Total_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T128_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T128_Valor_Total_IPI` decimal(10,2) DEFAULT '0.00',
  `T128_Nome_Contato` varchar(40) DEFAULT NULL,
  `T128_Fax` varchar(30) DEFAULT NULL,
  `T128_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T128_Codigo_Comprador` varchar(10) DEFAULT NULL,
  `T128_Centro_Custo` varchar(10) NOT NULL,
  `T128_Prazo_Pagamento` varchar(50) DEFAULT NULL,
  `T128_Valor_Total_Pendente` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`T128_Id`),
  KEY `T128_IDX_USUARIOS` (`T128_C007_Id`),
  KEY `T128_IDX_EMPRESAS` (`T128_C004_Id`),
  KEY `T128_IDX_TIPO_PRODUTO` (`T128_C008_Id`),
  KEY `T128_FKIndex3` (`T128_D036_Id`),
  KEY `T128_FKIndex6` (`T128_D024_Id`),
  KEY `T128_FKIndex7` (`T128_D022_Id`),
  KEY `IDX_T128_Id` (`T128_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T128`
--

LOCK TABLES `T128` WRITE;
/*!40000 ALTER TABLE `T128` DISABLE KEYS */;
/*!40000 ALTER TABLE `T128` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T129`
--

-- DROP TABLE IF EXISTS `T129`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T129` (
  `T129_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T129_T059_Id` int(10) unsigned DEFAULT NULL,
  `T129_D009_Id` int(10) unsigned DEFAULT NULL,
  `T129_T128_Id` int(10) unsigned DEFAULT NULL,
  `T129_C004_Id` int(10) unsigned DEFAULT '0',
  `T129_Codigo_Produto` varchar(30) DEFAULT NULL,
  `T129_Codigo_Produto_Fornecedor` varchar(20) DEFAULT NULL,
  `T129_Descricao_Produto` varchar(50) DEFAULT NULL,
  `T129_Quantidade` decimal(10,3) DEFAULT NULL,
  `T129_Valor_Custo_Unitario` decimal(14,5) DEFAULT '0.00000',
  `T129_Valor_Total_Custo` decimal(10,2) DEFAULT '0.00',
  `T129_Percentual_Reducao_ICMS` decimal(5,2) DEFAULT '0.00',
  `T129_Valor_Base_ICMS` decimal(10,2) DEFAULT '0.00',
  `T129_Aliquota_ICMS` decimal(5,2) DEFAULT '0.00',
  `T129_Valor_ICMS` decimal(10,2) DEFAULT '0.00',
  `T129_Percentual_Reducao_IPI` decimal(5,2) DEFAULT '0.00',
  `T129_Valor_Base_IPI` decimal(10,2) DEFAULT '0.00',
  `T129_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  `T129_Valor_IPI` decimal(10,2) DEFAULT '0.00',
  `T129_Prazo_Entrega` int(10) DEFAULT '0',
  `T129_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T129_Codigo_Fornecedor` varchar(6) DEFAULT NULL,
  `T129_Flag_Travar_Pedido` varchar(1) DEFAULT 'N',
  `T129_Numero` varchar(10) DEFAULT NULL,
  `T129_Flag_Cancelada` varchar(1) DEFAULT 'N',
  `T129_Observacao` tinytext NOT NULL,
  `T129_T006_Id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`T129_Id`),
  KEY `T129_IDX_PRODUTO_ESTOQUE` (`T129_D009_Id`),
  KEY `T129_IDX_PEDIDO_COMPRA` (`T129_T128_Id`),
  KEY `T129_IDX_CODIGO_PRODUTO` (`T129_Codigo_Produto`),
  KEY `T129_IDX_CODIGO_FORNECEDOR` (`T129_Codigo_Fornecedor`),
  KEY `T129_IDX_EMPRESAS` (`T129_C004_Id`),
  KEY `T129_IDX_PRECOS_COTACAO` (`T129_T059_Id`),
  KEY `T129_IDX_NUMERO_PEDIDO` (`T129_Numero`),
  KEY `T129_IDX_ID` (`T129_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T129`
--

LOCK TABLES `T129` WRITE;
/*!40000 ALTER TABLE `T129` DISABLE KEYS */;
/*!40000 ALTER TABLE `T129` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `T130`
--

-- DROP TABLE IF EXISTS `T130`
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `T130` (
  `T130_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T130_T129_Id` int(10) unsigned DEFAULT NULL,
  `T130_T013_Id` int(10) unsigned DEFAULT NULL,
  `T130_C004_Id` int(10) unsigned DEFAULT NULL,
  `T130_T008_Id` int(10) unsigned DEFAULT NULL,
  `T130_Quantidade` decimal(14,5) DEFAULT '0.00000',
  `T130_Classificacao_Fiscal` varchar(12) DEFAULT NULL,
  `T130_Aliquota_IPI` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`T130_Id`),
  KEY `T130_FKIndex1` (`T130_T129_Id`),
  KEY `T130_FKIndex2` (`T130_T013_Id`),
  KEY `T130_FKIndex3` (`T130_T008_Id`),
  KEY `T130_FKIndex4` (`T130_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `T130`
--

LOCK TABLES `T130` WRITE;
/*!40000 ALTER TABLE `T130` DISABLE KEYS */;
/*!40000 ALTER TABLE `T130` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-06-22 11:53:14

-- MySQL dump 10.11
--
-- Host: localhost    Database: e050
-- ------------------------------------------------------
-- Server version 5.0.83-0ubuntu3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `b001`
--

DROP TABLE IF EXISTS `b001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b001` (
  `B001_Id` int(11) NOT NULL,
  `B001_CODGRP` varchar(5) NOT NULL,
  `B001_CODBAR` varchar(13) default NULL,
  `B001_NOMPRO` varchar(50) NOT NULL,
  `B001_EMBPRO` varchar(10) default NULL,
  `B001_QTDPOR` varchar(50) default NULL,
  `B001_UNDPRO` varchar(10) NOT NULL default 'UND',
  `B001_GRMPRO` varchar(3) default NULL,
  `B001_CUSMAT` decimal(11,2) NOT NULL default '0.00',
  `B001_CUSSER` decimal(11,2) NOT NULL default '0.00',
  `B001_ALQICM` decimal(5,2) NOT NULL default '0.00',
  `B001_ALQIPI` decimal(5,2) NOT NULL default '0.00',
  `B001_MARLUC` decimal(5,2) NOT NULL default '0.00',
  `B001_PRECUS` decimal(11,2) NOT NULL default '0.00',
  `B001_PREUNT` decimal(11,2) NOT NULL default '0.00',
  `B001_PESLIQ` decimal(11,4) NOT NULL default '0.0000',
  `B001_OBSCON` varchar(250) default NULL,
  `B001_OBSING` varchar(250) default NULL,
  `B001_OBSPRO` varchar(250) default NULL,
  `B001_VLRCAL` varchar(10) default '0',
  `B001_VDR001` varchar(5) default '0',
  `B001_VLRCRB` varchar(10) default '0',
  `B001_VDR002` varchar(5) default '0',
  `B001_VLRPRO` varchar(10) default '0',
  `B001_VDR003` varchar(5) default '0',
  `B001_GORTOT` varchar(10) default '0',
  `B001_VDR004` varchar(5) default '0',
  `B001_GORSAT` varchar(10) default '0',
  `B001_VDR005` varchar(5) default '0',
  `B001_VLRCOL` varchar(10) default '0',
  `B001_VDR006` varchar(5) default '0',
  `B001_FIBALI` varchar(10) default '0',
  `B001_VDR007` varchar(5) default '0',
  `B001_CALCIO` varchar(10) default '0',
  `B001_VDR008` varchar(5) default '0',
  `B001_VLRFER` varchar(10) default '0',
  `B001_VDR009` varchar(5) default '0',
  `B001_VLRSOD` varchar(10) default '0',
  `B001_VDR010` varchar(5) default '0',
  `B001_CUSACE` decimal(11,2) NOT NULL,
  `B001_CODSER` char(5) NOT NULL,
  `B001_TIPPRO` char(1) NOT NULL default 'C',
  `B001_PRZVAL` int(11) NOT NULL default '1',
  `B001_CODAME` varchar(50) default NULL,
  `B001_QTDPCT` int(11) NOT NULL default '0',
  `B001_PESTOT` decimal(11,3) NOT NULL default '0.000',
  `B001_NOMTEC` varchar(50) NOT NULL,
  `B001_EAN013` char(13) default NULL,
  `B001_PATFOT` varchar(100) default NULL,
  `B001_FOTPRO` text,
  `B001_CLAIPI` char(8) default NULL,
  `B001_PRESUG` decimal(11,2) default '0.00',
  `B001_PREFIX` decimal(11,2) default '0.00',
  `B001_ITMESC` smallint(6) NOT NULL default '0',
  `B001_IMPETQ` char(1) NOT NULL default 'N',
  `B001_PRETMP` decimal(11,2) NOT NULL default '0.00',
  `B001_PREANT` decimal(11,2) NOT NULL default '0.00',
  PRIMARY KEY  (`B001_Id`),
  KEY `B001_CODGRP` (`B001_CODGRP`),
  KEY `B001_CODSER` (`B001_CODSER`),
  KEY `IND_B00101` (`B001_Id`),
  KEY `IND_B00102` (`B001_CODBAR`),
  KEY `IND_B00103` (`B001_NOMPRO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `b001`
--

LOCK TABLES `b001` WRITE;
/*!40000 ALTER TABLE `b001` DISABLE KEYS */;
/*!40000 ALTER TABLE `b001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c001`
--

DROP TABLE IF EXISTS `c001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c001` (
  `C001_Id` int(10) unsigned NOT NULL auto_increment,
  `C001_C002_Id` int(10) unsigned NOT NULL,
  `C001_Chave_Primaria` int(1) default '2',
  `C001_Nome_Campo_Destino` varchar(100) default NULL,
  `C001_Nome_Campo_Origem` varchar(100) default NULL,
  `C001_Tipo_Campo_Origem` char(1) default NULL,
  `C001_Tamanho_Campo_Origem` int(10) unsigned default NULL,
  `C001_Decimal_Campo_Origem` int(10) unsigned default NULL,
  `C001_Campo_Destino_Pesquisa` varchar(100) default NULL,
  `C001_Ativo` int(1) default '1',
  `C001_Descricao_Campo` text,
  `C001_Dominio_Campo` text,
  `C001_Posicao_Inicial_Txt` int(11) default '0',
  `C001_Posicao_Final_Txt` int(11) default '0',
  `C001_Ordem` int(3) default '0',
  `C001_Campo_Origem_Pesquisa` varchar(100) default NULL,
  `C001_Macro` varchar(200) default NULL,
  `C001_Flag_Tipo_Data` int(1) default '1',
  PRIMARY KEY  (`C001_Id`),
  KEY `D100_FKIndex1` (`C001_C002_Id`),
  CONSTRAINT `C001_ibfk_1` FOREIGN KEY (`C001_C002_Id`) REFERENCES `c002` (`C002_Id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c001`
--

LOCK TABLES `c001` WRITE;
/*!40000 ALTER TABLE `c001` DISABLE KEYS */;
/*!40000 ALTER TABLE `c001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c002`
--

DROP TABLE IF EXISTS `c002`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c002` (
  `C002_Id` int(10) unsigned NOT NULL auto_increment,
  `C002_C003_Id` int(10) unsigned NOT NULL,
  `C002_Tabela_Origem` varchar(100) default NULL,
  `C002_Tabela_Destino` varchar(100) default NULL,
  `C002_Ordem_Importacao` int(4) default NULL,
  `C002_Ativo` int(1) default '1',
  `C002_Descricao_Tabela_Origem` text,
  `C002_Importar` int(1) default '2',
  `C002_Macro_Tabela_Origem` varchar(200) default NULL,
  `C002_Compartilhado` int(1) default NULL,
  `C002_Caminho_Arquivo_Origem` varchar(100) default NULL,
  `C002_PI_Data_Movimento` int(4) default NULL,
  `C002_PF_Data_Movimento` int(4) default NULL,
  `C002_LI_Data_Movimento` int(4) default NULL,
  `C002_PI_Quantidade_Linhas` int(4) default NULL,
  `C002_PF_Quantidade_Linhas` int(4) default NULL,
  `C002_LI_Quantidade_Linhas` int(4) default NULL,
  `C002_Condicao_Exec_Sql` varchar(200) default NULL,
  `C002_Clausula_Sql` varchar(200) default NULL,
  `C002_Verifica_Chave_Primaria` int(1) default '1',
  `C002_Pular_Registros_Existentes` int(1) default '2',
  PRIMARY KEY  (`C002_Id`),
  KEY `D101_FKIndex1` (`C002_C003_Id`),
  CONSTRAINT `C002_ibfk_1` FOREIGN KEY (`C002_C003_Id`) REFERENCES `c003` (`C003_Id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c002`
--

LOCK TABLES `c002` WRITE;
/*!40000 ALTER TABLE `c002` DISABLE KEYS */;
/*!40000 ALTER TABLE `c002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c003`
--

DROP TABLE IF EXISTS `c003`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c003` (
  `C003_Id` int(10) unsigned NOT NULL auto_increment,
  `C003_Tipo_Campo_Origem` varchar(30) default NULL,
  PRIMARY KEY  (`C003_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c003`
--

LOCK TABLES `c003` WRITE;
/*!40000 ALTER TABLE `c003` DISABLE KEYS */;
/*!40000 ALTER TABLE `c003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c004`
--

DROP TABLE IF EXISTS `c004`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c004` (
  `C004_Id` int(10) unsigned NOT NULL auto_increment,
  `C004_D020_Id` int(10) unsigned default NULL,
  `C004_D018_Id` int(11) default '0',
  `C004_D024_Id` int(11) default '0',
  `C004_Nome_Empresa` varchar(100) default NULL,
  `C004_Cidade` varchar(30) default NULL,
  `C004_Uf` char(2) default NULL,
  `C004_Cnpj` varchar(18) default NULL,
  `C004_Caminho_Sistema` varchar(100) default NULL,
  `C004_Matriz` int(1) default NULL,
  `C004_Banco_Dados` varchar(30) default NULL,
  `C004_Caminho_Dbf` varchar(100) default NULL,
  `C004_Importar` int(1) default '1',
  `C004_Banco_Configuracao` varchar(15) default NULL,
  `C004_Nome_Abreviado` varchar(60) default NULL,
  `C004_Inscricao_Estadual` varchar(14) default NULL,
  `C004_Inscricao_Municipal` varchar(20) default NULL,
  `C004_Responsavel_Contato` varchar(28) default NULL,
  `C004_Telefone` varchar(12) default NULL,
  `C004_Fax` varchar(12) default NULL,
  `C004_Logradouro` varchar(34) default NULL,
  `C004_Numero` varchar(5) default NULL,
  `C004_Complemento` varchar(22) default NULL,
  `C004_Bairro` varchar(15) default NULL,
  `C004_Cep` varchar(10) default NULL,
  `C004_Estado` varchar(20) default NULL,
  `C004_Endereco_Impressora_Nf` varchar(20) default NULL,
  `C004_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `C004_Aliquota_ICMS` decimal(5,2) default NULL,
  `C004_Ultimo_Numero_Nota_Fiscal` varchar(12) default NULL,
  `C004_Ultimo_Numero_Nota_Fiscal_Eletronica` varchar(12) default NULL,
  `C004_Ultimo_Numero_ACP` varchar(12) default NULL,
  `C004_Ultimo_Numero_Controle` varchar(12) default NULL,
  `C004_Flag_Isento_ICMS` int(1) default '2',
  `C004_Flag_Isento_ISS` int(1) default '2',
  `C004_Flag_Isento_IPI` int(1) default '2',
  `C004_Flag_Empresa_Simples` int(1) default '2',
  `C004_Aliquota_ISS` decimal(5,2) default '0.00',
  `C004_Flag_IPI_Base_ICMS_Revenda` varchar(1) default 'N',
  `C004_Flag_IPI_Base_ICMS_Consumidor` varchar(1) default 'N',
  `C004_Site` varchar(50) default NULL,
  `C004_Logo_Marca` varchar(100) default NULL,
  `C004_Observacao_Fiscal` text,
  `C004_Observacao_Fiscal_ICMS_Diferido` text,
  `C004_Flag_Controla_Estoque` varchar(1) default NULL,
  `C004_Conta_Clientes` int(11) NOT NULL,
  `C004_Cojnta_Fornecedores` int(10) NOT NULL,
  `C004_Conta_Frete` int(10) NOT NULL,
  `C004_Meses_MMF` int(2) default NULL,
  `C004_CFOP_Venda_Dentro_Estado` int(3) default NULL,
  `C004_CFOP_Venda_Fora_Estado` int(3) default NULL,
  `C004_Emite_NF_Caixa` varchar(1) default NULL,
  `C004_Observacao_Promocao` text,
  `C004_Observacao_Logo` varchar(100) default NULL,
  `C004_Observacao_Rodape_Orcamento` text,
  `C004_Observacao_Cabecalho` text,
  `C004_Flag_Decimais_Sugestao_Compra` varchar(1) default NULL,
  `C004_Observacao_Cotacao_Compra` text,
  `C004_Ultimo_Numero_Cupom_Fiscal` int(10) default NULL,
  `C004_PEDVEN_Botao_Liberar_Almoxarifado` varchar(1) default NULL,
  `C004_PEDVEN_Botao_Liberar_Separacao` varchar(1) default NULL,
  `C004_Impressora_Pedido_Almox` varchar(30) default NULL,
  `C004_Linhas_Obs_Fiscal` int(3) default NULL,
  `C004_Obs_Reduzido` text,
  `C004_Casas_Decimais_Preco` int(2) default '2',
  `C004_Flag_Ativo` varchar(1) NOT NULL,
  `C004_Impressora_Comercial` varchar(50) default NULL,
  `C004_Impressora_Balcao` varchar(50) default NULL,
  `C004_Senha_Certificados` varchar(30) default NULL,
  `C004_Localizacao_Certificados` varchar(200) default NULL,
  `C004_CNAE_Fiscal` varchar(7) default NULL,
  `C004_Percentual_PIS` decimal(6,2) default '0.00',
  `C004_Percentual_COFINS` decimal(6,2) default '0.00',
  `C004_Ambiente_NFe` enum('1','2') default '2',
  PRIMARY KEY  (`C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1250;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c004`
--

LOCK TABLES `c004` WRITE;
/*!40000 ALTER TABLE `c004` DISABLE KEYS */;
/*!40000 ALTER TABLE `c004` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c005`
--

DROP TABLE IF EXISTS `c005`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c005` (
  `C005_Id` int(10) unsigned NOT NULL auto_increment,
  `C005_C006_Id` int(10) unsigned default NULL,
  `C005_Campo_Conta` varchar(30) default NULL,
  `C005_Entidade` varchar(4) default NULL,
  `C005_Campo_Logico` varchar(50) default NULL,
  `C005_Campo_Valor` text,
  `C005_Campo_Data` varchar(50) default NULL,
  `C005_Condicao` text,
  `C005_Relacionamento` text,
  `C005_Flag_Reatualizar` int(1) default NULL,
  `C005_Flag_Historico` int(1) NOT NULL default '2',
  `C005_Referencia` varchar(200) default NULL,
  `C005_Link` text,
  `C005_Flag_Positivo_Negativo` varchar(1) default NULL,
  `C005_Flag_Agrupar` varchar(1) default NULL,
  `C005_Flag_Ativo` varchar(1) default NULL,
  PRIMARY KEY  (`C005_Id`),
  KEY `C005_FKIndex1` (`C005_C006_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c005`
--

LOCK TABLES `c005` WRITE;
/*!40000 ALTER TABLE `c005` DISABLE KEYS */;
/*!40000 ALTER TABLE `c005` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c006`
--

DROP TABLE IF EXISTS `c006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c006` (
  `C006_Id` int(10) unsigned NOT NULL auto_increment,
  `C006_Grupo` varchar(50) default NULL,
  PRIMARY KEY  (`C006_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c006`
--

LOCK TABLES `c006` WRITE;
/*!40000 ALTER TABLE `c006` DISABLE KEYS */;
/*!40000 ALTER TABLE `c006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c007`
--

DROP TABLE IF EXISTS `c007`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c007` (
  `C007_Id` int(11) unsigned NOT NULL auto_increment,
  `C007_C007_Id` int(11) unsigned default NULL,
  `C007_D024_Id` int(11) default NULL,
  `C007_C004_Id` int(10) default NULL,
  `C007_Id_Usuario_Chat` int(11) default NULL,
  `C007_Login` varchar(20) default NULL,
  `C007_Senha` varchar(20) default NULL,
  `C007_Email` text,
  `C007_Nome` varchar(30) default NULL,
  `C007_Primeiro_Nome` varchar(20) default NULL,
  `C007_Especial` varchar(1) default 'N',
  `C007_Ativo` char(1) default 'S',
  `C007_Cargo` varchar(30) default NULL,
  `C007_Rg` varchar(12) default NULL,
  `C007_Cpf` varchar(14) default NULL,
  `C007_Endereco` varchar(100) default NULL,
  `C007_Complemento` varchar(20) default NULL,
  `C007_Bairro` varchar(35) default NULL,
  `C007_Cep` varchar(10) default NULL,
  `C007_Cidade` varchar(35) default NULL,
  `C007_Estado` varchar(25) default NULL,
  `C007_Telefone` text,
  `C007_Id_Perfil` int(4) default NULL,
  `C007_Data_Alteracao_Senha` date default NULL,
  `C007_Usuario_Suporte` char(1) default 'N',
  `C007_Usuario_Administracao` varchar(1) default 'N',
  `C007_Usuario_Ancora` varchar(1) default 'N',
  `C007_Usuario_Acessos` varchar(1) default 'N',
  `C007_Codigo_Sistema_Antigo` varchar(11) default NULL,
  `C007_Msn` varchar(100) default NULL,
  `C007_Telefone_Direto` varchar(30) default NULL,
  `C007_Flag_Expedicao` varchar(1) default NULL,
  `C007_Estilo` varchar(30) default NULL,
  `C007_Observacao` text,
  `C007_Celular` varchar(30) default NULL,
  `C007_Flag_Vendedor` enum('','S','V','D') default '',
  `C007_Flag_Comercial` varchar(1) default NULL,
  `C007_Flag_Mostrar_Boleto` enum('S','N') default 'N',
  PRIMARY KEY  (`C007_Id`),
  KEY `C007_Primeiro_Nome` (`C007_Primeiro_Nome`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c007`
--

LOCK TABLES `c007` WRITE;
/*!40000 ALTER TABLE `c007` DISABLE KEYS */;
/*!40000 ALTER TABLE `c007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c008`
--

DROP TABLE IF EXISTS `c008`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c008` (
  `C008_Id` int(10) unsigned NOT NULL auto_increment,
  `C008_Tipo` varchar(20) default NULL,
  `C008_Flag_Vendas` varchar(1) default ' ',
  `C008_Flag_Inicial_Codigo` varchar(3) default ' ',
  `C008_Flag_Codigo_Proprio` varchar(1) default ' ',
  `C008_Apelido_Linha` varchar(15) default 'Linha',
  `C008_Apelido_Grupo` varchar(15) default 'Grupo',
  `C008_Apelido_SubGrupo` varchar(15) default 'Sub-Grupo',
  `C008_Apelido_Aplicacao` varchar(15) default 'Aplicação',
  `C008_Apelido_Marca` varchar(15) default 'Marca',
  `C008_Apelido_Complemento_Descricao` varchar(25) default 'Complemento Descrição',
  `C008_Codigo_Padrao` varchar(10) default ' ',
  `C008_Descricao_Padrao` varchar(30) default ' ',
  `C008_Inclusao_Produto_Mostrar_Codigo_Grupos` enum('S','N') default 'N',
  `C008_Inclusao_Produto_Delimitador_Codigo` varchar(1) default '',
  `C008_Apelido_Preco_Revenda` varchar(20) default 'Revenda',
  `C008_Apelido_Preco_Cons_Atacado` varchar(20) default 'Consumidor Atacado',
  `C008_Apelido_Preco_Cons_Varejo` varchar(20) default 'Consumidor Varejo',
  PRIMARY KEY  (`C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c008`
--

LOCK TABLES `c008` WRITE;
/*!40000 ALTER TABLE `c008` DISABLE KEYS */;
/*!40000 ALTER TABLE `c008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c009`
--

DROP TABLE IF EXISTS `c009`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c009` (
  `C009_Id` int(10) unsigned NOT NULL auto_increment,
  `C009_Descricao_Acesso` varchar(150) default NULL,
  `C009_Entidade` char(4) default NULL,
  `C009_Tela` varchar(150) default NULL,
  `C009_Opcao` varchar(30) default NULL,
  `C009_Grupo` varchar(10) default NULL,
  PRIMARY KEY  (`C009_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c009`
--

LOCK TABLES `c009` WRITE;
/*!40000 ALTER TABLE `c009` DISABLE KEYS */;
INSERT INTO `c009` VALUES (1,'LIBERAR IPV MINIMO',NULL,NULL,NULL,'99'),(2,'LIBERAR CREDITO',NULL,NULL,NULL,'99'),(3,'RETORNAR PEDIDO DO ALMOXARIFADO',NULL,NULL,NULL,'99'),(4,'CANCELAR PEDIDO VENDA',NULL,NULL,NULL,'99'),(5,'CANCELAR PEDIDO PENDENTE',NULL,NULL,NULL,'99'),(6,'ALTERAR OBSERVACAO COMPRA EM PRODUTOS',NULL,NULL,NULL,'99'),(7,'CANCELAR NOTA FISCAL EMITIDA',NULL,NULL,NULL,'99'),(8,'INCLUSAO E ALTERAÇÃO DE ITENS (D001)',NULL,NULL,NULL,'99'),(9,'ATUALIZAR D.E.',NULL,NULL,NULL,'99'),(10,'ALTERAÇÃO DO CAMPO COMPLEMENTO (D001)',NULL,NULL,NULL,'99'),(11,'ALTERAÇÃO DO CAMPO PESO (D001)',NULL,NULL,NULL,'99'),(12,'MENS.MOTIVO PEDIDO NEGADO',NULL,NULL,NULL,'99'),(13,'LIBERAR NOTA DE EXPOSIÇÃO',NULL,NULL,NULL,'99'),(14,'CANCELAR NOTA FISCAL RECEBIDA',NULL,NULL,NULL,'99'),(15,'LIBERAR TRANSFERENCIA ABAIXO DO IPV',NULL,NULL,NULL,'99'),(16,'ATIVAR / INATIVAR CLIENTE/FORNECEDOR',NULL,NULL,NULL,'99'),(17,'ATIVAR / INATIVAR TRANSPORTADORA',NULL,NULL,NULL,'99'),(18,'MENSAGEM PIN',NULL,NULL,NULL,'99'),(19,'ALTERAÇÃO DO CAMPO FOTO (D001)',NULL,NULL,NULL,'99'),(20,'CONTROLE DE REPRESENTANTE',NULL,NULL,NULL,'99'),(21,'ALTERAR CFOP NOTAS',NULL,NULL,NULL,'99'),(22,'EXCLUIR CLIENTE',NULL,NULL,NULL,'99'),(32,'PROCESSAR MONTAGEM',NULL,NULL,NULL,'99'),(33,'MENSAGEM PEDIDO PARA IMPRIMIR',NULL,NULL,NULL,'99'),(34,'ALTERAR PRECOS NO ORCAMENTO/PEDIDOS',NULL,NULL,NULL,'99'),(35,'IMPRIMIR COPIA NF OFICIAL',NULL,NULL,NULL,'99'),(36,'MUDAR PRE-CADASTRO CLIENTE',NULL,NULL,NULL,'99'),(37,'PERMITE IMPRIMIR A COLUNA CUSTOS NO INVENTARIO',NULL,NULL,NULL,'99'),(38,'ALTERAR PEDIDO PENDENTE',NULL,NULL,NULL,'99'),(39,'CLIENTES/FORNECEDORES',NULL,NULL,NULL,'2'),(40,'REPRESENTANTES',NULL,NULL,NULL,'2'),(41,'TRANSPORTADORA',NULL,NULL,NULL,'2'),(42,'PRODUTOS',NULL,NULL,NULL,'2'),(43,'C.F.O.P',NULL,NULL,NULL,'2'),(44,'CLASS.FISCAL',NULL,NULL,NULL,'2'),(45,'BANCOS/CAIXAS',NULL,NULL,NULL,'2'),(46,'PORTADORES',NULL,NULL,NULL,'2'),(47,'CENTRO CUSTO',NULL,NULL,NULL,'2'),(48,'EMPRESA',NULL,NULL,NULL,'2'),(49,'PERFIL',NULL,NULL,NULL,'2'),(50,'VENDEDORES',NULL,NULL,NULL,'2'),(51,'USUARIOS',NULL,NULL,NULL,'2'),(52,'LISTA DE PRECOS',NULL,NULL,NULL,'3'),(53,'PRE PEDIDO',NULL,NULL,NULL,'3'),(54,'ORCAMENTOS',NULL,NULL,NULL,'3'),(55,'PEDIDOS',NULL,NULL,NULL,'3'),(56,'NOTAS FISCAIS',NULL,NULL,NULL,'3'),(57,'EMITIR NOTAS FISCAIS',NULL,NULL,NULL,'3'),(58,'INUTILIZAR NF-e',NULL,NULL,NULL,'3'),(59,'COMODATO',NULL,NULL,NULL,'3'),(60,'ORDEM DE SERVIÇO',NULL,NULL,NULL,'3'),(61,'CONTAS A RECEBER',NULL,NULL,NULL,'4'),(62,'CONTAS A PAGAR',NULL,NULL,NULL,'4'),(63,'CAIXA E BANCOS',NULL,NULL,NULL,'4'),(64,'BOLETO BANCARIO',NULL,NULL,NULL,'4'),(65,'FATURAR ROMANEIO',NULL,NULL,NULL,'4'),(66,'PROJECAO ORCAMENTARIA',NULL,NULL,NULL,'4'),(67,'CONTROLE DE ESTOQUE',NULL,NULL,NULL,'5'),(68,'NOTAS FISCAIS RECEBIDAS',NULL,NULL,NULL,'5'),(69,'EXPEDICAO MERCADORIA',NULL,NULL,NULL,'5'),(70,'DIVERGENCIA ESTOQUE',NULL,NULL,NULL,'5'),(71,'MONTAGEM',NULL,NULL,NULL,'5'),(72,'FLUXO DE CAIXA',NULL,NULL,NULL,'6'),(73,'BALANCETE',NULL,NULL,NULL,'6'),(74,'RELATORIO CONTAS A PAGAR',NULL,NULL,NULL,'6'),(75,'RELATORIO CONTAS A RECEBER',NULL,NULL,NULL,'6'),(76,'MAPA DE VENDA',NULL,NULL,NULL,'6'),(77,'INVENTARIO ESTOQUE',NULL,NULL,NULL,'6'),(78,'COMISSAO REPRESENTANTES',NULL,NULL,NULL,'6'),(79,'FECHAMENTOS',NULL,NULL,NULL,'6'),(80,'GERAR SINTEGRA',NULL,NULL,NULL,'7'),(81,'XML NFe',NULL,NULL,NULL,'7'),(82,'UF(ICMS)',NULL,NULL,NULL,'8'),(83,'CIDADES',NULL,NULL,NULL,'8'),(84,'LAYOUT NOTA FISCAL',NULL,NULL,NULL,'8'),(85,'MYSQL',NULL,NULL,NULL,'8'),(86,'PARAMETRIZAÇOES',NULL,NULL,NULL,'8'),(87,'MAILING',NULL,NULL,NULL,'9'),(88,'RECLAME JÁ',NULL,NULL,NULL,'9'),(89,'CARTEIRA CLIENTES',NULL,NULL,NULL,'3'),(90,'EXCLUIR NOTA REJEITADA PELO SEFAZ',NULL,NULL,NULL,'99'),(91,'COTAÇÃO',NULL,NULL,NULL,'5'),(92,'COMPRAS EMERGÊNCIA',NULL,NULL,NULL,'5'),(93,'COMPRAS ESTOQUE',NULL,NULL,NULL,'5'),(94,'ORDEM COMPRA',NULL,NULL,NULL,'5'),(95,'CURVA ABC VENDEDORES',NULL,NULL,NULL,'3'),(96,'ANALISE DE CRÉDITO',NULL,NULL,NULL,'4'),(97,'APROVACAO DE ORCAMENTOS',NULL,NULL,NULL,'3'),(98,'C.R.M.',NULL,NULL,NULL,'3'),(99,'IPV',NULL,NULL,NULL,'8'),(100,'MOTIVOS CANCELAMENTO VENDAS',NULL,NULL,NULL,'8'),(101,'TABELA DE PRECOS',NULL,NULL,NULL,'2'),(102,'TABELA FORNECEDOR',NULL,NULL,NULL,'2'),(103,'CURVA ABC PRODUTOS',NULL,NULL,NULL,'5');
/*!40000 ALTER TABLE `c009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c010`
--

DROP TABLE IF EXISTS `c010`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c010` (
  `C010_Id` int(10) unsigned NOT NULL auto_increment,
  `C010_C021_Id` int(10) unsigned NOT NULL,
  `C010_C009_Id` int(10) unsigned NOT NULL,
  `C010_Id_Perfil` int(10) unsigned NOT NULL,
  `C010_C007_Id` int(10) default NULL,
  `C010_Acesso_Liberado` enum('S','N') default 'N',
  PRIMARY KEY  (`C010_Id`),
  KEY `C010_FKIndex1` (`C010_C009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c010`
--

LOCK TABLES `c010` WRITE;
/*!40000 ALTER TABLE `c010` DISABLE KEYS */;
/*!40000 ALTER TABLE `c010` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c011`
--

DROP TABLE IF EXISTS `c011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c011` (
  `C011_Id` int(11) NOT NULL auto_increment,
  `C011_C002_Id` int(11) default '0',
  `C011_Nome_Arquivo` varchar(50) default NULL,
  `C011_Data_Importacao` datetime default NULL,
  `C011_Flag_Erro` varchar(1) default NULL,
  `C011_Nome_Arquivo_Log` varchar(100) default NULL,
  PRIMARY KEY  (`C011_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c011`
--

LOCK TABLES `c011` WRITE;
/*!40000 ALTER TABLE `c011` DISABLE KEYS */;
/*!40000 ALTER TABLE `c011` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c012`
--

DROP TABLE IF EXISTS `c012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c012` (
  `C012_Id` int(11) NOT NULL auto_increment,
  `C012_Nome` text,
  `C012_Clausula_Sql` text,
  PRIMARY KEY  (`C012_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c012`
--

LOCK TABLES `c012` WRITE;
/*!40000 ALTER TABLE `c012` DISABLE KEYS */;
/*!40000 ALTER TABLE `c012` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c013`
--

DROP TABLE IF EXISTS `c013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c013` (
  `C013_C007_Id` int(10) NOT NULL default '0',
  `C013_C004_Id` int(10) default NULL,
  `C013_Login` datetime default NULL,
  `C013_Entidade` varchar(10) default NULL,
  `C013_Atualizado` datetime default NULL,
  `C013_Tela` varchar(30) default NULL,
  PRIMARY KEY  (`C013_C007_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c013`
--

LOCK TABLES `c013` WRITE;
/*!40000 ALTER TABLE `c013` DISABLE KEYS */;
/*!40000 ALTER TABLE `c013` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c014`
--

DROP TABLE IF EXISTS `c014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c014` (
  `C014_Id` int(10) NOT NULL auto_increment,
  `C014_C005_Id` int(10) default NULL,
  `C014_Campo_Logico` varchar(30) default NULL,
  `C014_Campo_Fisico` varchar(30) default NULL,
  PRIMARY KEY  (`C014_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c014`
--

LOCK TABLES `c014` WRITE;
/*!40000 ALTER TABLE `c014` DISABLE KEYS */;
/*!40000 ALTER TABLE `c014` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c015`
--

DROP TABLE IF EXISTS `c015`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c015` (
  `C015_Id` int(10) unsigned NOT NULL auto_increment,
  `C015_Impressora` varchar(50) default NULL,
  `C015_Comando` varchar(50) default NULL,
  `C015_C004_Id` int(11) default NULL,
  PRIMARY KEY  (`C015_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c015`
--

LOCK TABLES `c015` WRITE;
/*!40000 ALTER TABLE `c015` DISABLE KEYS */;
/*!40000 ALTER TABLE `c015` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c016`
--

DROP TABLE IF EXISTS `c016`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c016` (
  `C016_Id` int(10) NOT NULL auto_increment,
  `C016_C007_Id` int(10) NOT NULL,
  `C016_Entidade` varchar(4) NOT NULL,
  `C016_Id_Entidade` int(10) NOT NULL,
  `C016_Data_Hora` datetime default NULL,
  `C016_Operacao` varchar(100) default NULL,
  PRIMARY KEY  (`C016_Id`),
  KEY `IDX_Entidade` (`C016_Entidade`),
  KEY `IDX_Id_Entidade` (`C016_Id_Entidade`),
  KEY `IDX_Operacao` (`C016_Operacao`),
  KEY `IDX_Data_Hora` (`C016_Data_Hora`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c016`
--

LOCK TABLES `c016` WRITE;
/*!40000 ALTER TABLE `c016` DISABLE KEYS */;
/*!40000 ALTER TABLE `c016` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c017`
--

DROP TABLE IF EXISTS `c017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c017` (
  `C017_Id` int(11) NOT NULL auto_increment,
  `C017_C004_Id` int(11) default '0',
  `C017_Caracter_Complementar` char(1) default '*',
  `C017_Linhas_Pagina` int(2) default '0',
  `C017_Tipo_Impressao` enum('LPR','PDF') default 'LPR',
  `C017_PDF_Largura_Papel` decimal(6,3) default '0.000',
  `C017_PDF_Altura_Papel` decimal(6,3) default '0.000',
  `C017_PDF_Margem_Esquerda` decimal(5,2) default '0.00',
  `C017_PDF_Margem_Direita` decimal(5,2) default '0.00',
  `C017_PDF_Margem_Superior` decimal(5,2) default '0.00',
  `C017_PDF_Tamanho_Letra_Expandido` decimal(5,2) default '0.00',
  `C017_PDF_Tamanho_Letra_Normal` decimal(5,2) default '0.00',
  `C017_PDF_Tamanho_Letra_Comprimido` decimal(5,2) default '0.00',
  `C017_PDF_Altura_Linha` decimal(5,2) default '0.00',
  PRIMARY KEY  (`C017_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c017`
--

LOCK TABLES `c017` WRITE;
/*!40000 ALTER TABLE `c017` DISABLE KEYS */;
/*!40000 ALTER TABLE `c017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c018`
--

DROP TABLE IF EXISTS `c018`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c018` (
  `C018_Id` int(11) NOT NULL auto_increment,
  `C018_C017_Id` int(11) default NULL,
  `C018_Sessao` int(1) default '0',
  `C018_Descricao` varchar(200) default NULL,
  `C018_Tipo_Letra` varchar(50) default NULL,
  `C018_Campo` text,
  `C018_Flag_Ativo` enum('S','N') default 'S',
  `C018_Flag_Especial` varchar(20) default NULL,
  `C018_Linha` int(4) default '0',
  `C018_Posicao_Inicial` int(3) default '0',
  `C018_Tamanho` int(3) default '0',
  `C018_Alinhamento` enum('E','C','D') default 'E',
  `C018_Flag_Apenas_Ultima_Pagina` enum('S','N') default 'N',
  `C018_Formatacao` varchar(1) default NULL,
  `C018_Casas_Decimais` int(2) default '2',
  PRIMARY KEY  (`C018_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c018`
--

LOCK TABLES `c018` WRITE;
/*!40000 ALTER TABLE `c018` DISABLE KEYS */;
/*!40000 ALTER TABLE `c018` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c019`
--

DROP TABLE IF EXISTS `c019`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c019` (
  `C019_Id` int(10) unsigned NOT NULL auto_increment,
  `C019_C021_Id` int(10) unsigned NOT NULL,
  `C019_C004_Id` int(10) unsigned NOT NULL,
  `C019_C007_Id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`C019_Id`),
  UNIQUE KEY `Duplicidade` (`C019_C021_Id`,`C019_C004_Id`,`C019_C007_Id`),
  KEY `C019_FKIndex1` (`C019_C007_Id`),
  KEY `C019_FKIndex3` (`C019_C004_Id`),
  CONSTRAINT `C019_ibfk_1` FOREIGN KEY (`C019_C021_Id`) REFERENCES `c021` (`C021_Id`) ON DELETE CASCADE,
  CONSTRAINT `C019_ibfk_2` FOREIGN KEY (`C019_C004_Id`) REFERENCES `c004` (`C004_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c019`
--

LOCK TABLES `c019` WRITE;
/*!40000 ALTER TABLE `c019` DISABLE KEYS */;
/*!40000 ALTER TABLE `c019` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c020`
--

DROP TABLE IF EXISTS `c020`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c020` (
  `C020_Id` int(10) unsigned NOT NULL auto_increment,
  `C020_C004_Id` int(10) unsigned default NULL,
  `C020_Orcamento_Alterar_Preco_Venda` enum('S','N') default 'S',
  `C020_Orcamento_Mostrar_Fornecedor_Tipo` enum('NOME','NI') default 'NOME',
  `C020_Orcamento_Permite_IPV_Cliente_Zerado` enum('S','N') default 'N',
  `C020_Orcamento_Permite_Pre_Cadastro` enum('S','N') default 'N',
  `C020_Orcamento_Permite_Item_Sem_Preco` enum('S','N') default 'S',
  `C020_Orcamento_Visualizar_IPV_Item` enum('S','N') default 'S',
  `C020_Orcamento_Visualizar_Custo` enum('S','N') default 'S',
  `C020_Orcamento_Puxar_Descricao_Orcamento` enum('S','N') default 'N',
  `C020_Orcamento_Imprimir_Campo_Entrega` enum('S','N') default 'N',
  `C020_Orcamento_Imprimir_Campo_IPI` enum('S','N') default 'S',
  `C020_Pedido_Alterar_Preco_Venda` enum('S','N') default 'S',
  `C020_Pedido_Mostrar_Fornecedor_Tipo` enum('NOME','NI') default 'NOME',
  `C020_Pedido_Visualizar_IPV_Item` enum('S','N') default 'S',
  `C020_Pedido_Visualizar_Custo` enum('S','N') default 'S',
  `C020_Pedido_Puxar_Descricao_Pedido` enum('S','N') default 'N',
  `C020_Pedido_Imprimir_Campo_Entrega` enum('S','N') default 'N',
  `C020_Pedido_Imprimir_Campo_IPI` enum('S','N') default 'S',
  `C020_Impressora_NF_Recebida` varchar(30) default NULL,
  `C020_Modulo_Insuficiencia` varchar(50) default NULL,
  `C020_Orcamento_Obter_Ultimo_Preco_Cliente` enum('S','N') default 'S',
  `C020_Orcamento_Representante_Obrigatorio` enum('S','N') default 'S',
  `C020_Consulta_Paginacao` enum('S','N') default 'S',
  `C020_Consulta_Linhas_Paginacao` int(4) default '15',
  `C020_Modelo_Menu` varchar(30) default 'menu_principal.hardness',
  `C020_Loja_Virtual_Visitante_Consultar_Produto` enum('S','N') default 'S',
  `C020_Loja_Virtual_Visitante_Ver_Preco` enum('S','N') default 'S',
  `C020_Loja_Virtual_Visitante_Comprar` enum('S','N') default 'S',
  `C020_Loja_Virtual_Visitante_Pagar` enum('S','N') default 'S',
  `C020_Loja_Virtual_Visitante_Cadastrar` enum('S','N') default 'S',
  `C020_Loja_Virtual_Cliente_Ver_Preco` enum('S','N') default 'S',
  `C020_Loja_Virtual_Cliente_Comprar` enum('S','N') default 'S',
  `C020_Loja_Virtual_Cliente_Pagar` enum('S','N') default 'S',
  `C020_Loja_Virtual_Nome_Email` varchar(100) default NULL,
  `C020_Loja_Virtual_Email` varchar(60) default NULL,
  `C020_Loja_Virtual_Dominio` varchar(100) default NULL,
  `C020_Loja_Virtual_Formato_Lista` enum('S','N') default 'N',
  `C020_Flag_Expedicao` enum('S','N') default 'S',
  `C020_NFe_SCAN_Ativado` enum('S','N') default 'N',
  `C020_Caixa_Obrigatorio_Abrir_Fechar_Caixa` enum('S','N') default 'N',
  `C020_Caixa_Cupom_Fiscal` enum('S','N') default 'N',
  `C020_NFe_Habilitada` enum('S','N') default 'S',
  `C020_Inclusao_Produtos_Simples` enum('S','N') default 'S',
  `C020_Pre_Pedido_Horario_Entrega_Obrigatorio` enum('S','N') default 'S',
  `C020_Pre_Pedido_Vendedor_Obrigatorio` enum('S','N') default 'S',
  `C020_Pre_Pedido_Prazos_Obrigatorio` enum('S','N') default 'S',
  `C020_IPV_Revenda` decimal(10,4) default '0.0000',
  `C020_IPV_Atacado` decimal(10,4) default '0.0000',
  `C020_IPV_Varejo` decimal(10,4) default '0.0000',
  `C020_Casas_Decimais_Quantidade` int(1) default '3',
  `C020_Cliente_Telefone_Obrigatorio` enum('S','N') default 'S',
  `C020_Pedido_Gerar_Pedido_Sem_Estoque` enum('S','N') default 'S',
  `C020_Data_Ultima_Atualizacao` datetime default '0000-00-00 00:00:00',
  `C020_Ultima_Revisao` int(10) default NULL,
  `C020_Modelo_Sistema_Nfe` varchar(2) default '01',
  `C020_NFe_Tipo_Certificado` enum('A1','A3') default 'A1',
  `C020_Cliente_Validar_Duplicidade_CNPJ_CPF` enum('S','N') default 'S',
  `C020_Regras_Faturamento_Vendas` text,
  `C020_IPV_Revenda_Minimo` decimal(10,4) default '0.0000',
  `C020_IPV_Atacado_Minimo` decimal(10,4) default '0.0000',
  `C020_IPV_Varejo_Minimo` decimal(10,4) default '0.0000',
  `C020_IPV_Revenda_Minimo_Supervisor` decimal(10,4) default '0.0000',
  `C020_IPV_Atacado_Minimo_Supervisor` decimal(10,4) default '0.0000',
  `C020_IPV_Varejo_Minimo_Supervisor` decimal(10,4) default '0.0000',
  `C020_IPV_Custo` decimal(10,4) default '0.0000',
  `C020_NFe_Obrigatorio_Cadastro_Produto` enum('S','N') default 'S',
  `C020_NFe_Treinamento` enum('S','N') default 'N',
  `C020_NFe_Tamanho_Campo_Descricao` int(3) default '60',
  `C020_NFe_Tipo_DANFE` enum('HTML','PDF') default 'HTML',
  PRIMARY KEY  (`C020_Id`),
  UNIQUE KEY `Duplicidade` (`C020_C004_Id`),
  KEY `C020_IDX_EMPRESAS` (`C020_C004_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c020`
--

LOCK TABLES `c020` WRITE;
/*!40000 ALTER TABLE `c020` DISABLE KEYS */;
/*!40000 ALTER TABLE `c020` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c021`
--

DROP TABLE IF EXISTS `c021`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c021` (
  `C021_Id` int(10) unsigned NOT NULL auto_increment,
  `C021_Nome_Perfil` varchar(50) default NULL,
  `C021_Flag_Visualizar_Opcoes_Desabilitadas` enum('S','N') default 'N',
  `C021_Visualizar_Clientes` enum('S','N') default 'S',
  `C021_Visualizar_Produtos` enum('S','N') default 'S',
  `C021_Visualizar_Orcamento` enum('S','N') default 'S',
  `C021_Visualizar_Expedicao` enum('S','N') default 'S',
  `C021_Visualizar_Caixa` enum('S','N') default 'S',
  `C021_Visualizar_Transportadora` enum('S','N') default 'S',
  `C021_Iniciar_Opcoes_Menu_Abertas` enum('S','N') default 'N',
  `C021_Pagina_Inicial` varchar(250) default 'principal/pag_inicial.php3',
  `C021_Visualizar_Pedido` enum('S','N') default 'N',
  `C021_Visualizar_PrePedido` enum('S','N') default 'N',
  PRIMARY KEY  (`C021_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c021`
--

LOCK TABLES `c021` WRITE;
/*!40000 ALTER TABLE `c021` DISABLE KEYS */;
/*!40000 ALTER TABLE `c021` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c022`
--

DROP TABLE IF EXISTS `c022`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c022` (
  `C022_Id` int(10) unsigned NOT NULL auto_increment,
  `C022_C021_Id` int(10) unsigned NOT NULL,
  `C022_Id_Menu` int(10) unsigned NOT NULL,
  `C022_Acesso_Liberado` enum('S','N') default 'N',
  PRIMARY KEY  (`C022_Id`),
  UNIQUE KEY `Duplicidade` (`C022_C021_Id`,`C022_Id_Menu`),
  KEY `C022_FKIndex1` (`C022_C021_Id`),
  CONSTRAINT `C022_ibfk_1` FOREIGN KEY (`C022_C021_Id`) REFERENCES `c021` (`C021_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c022`
--

LOCK TABLES `c022` WRITE;
/*!40000 ALTER TABLE `c022` DISABLE KEYS */;
/*!40000 ALTER TABLE `c022` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c024`
--

DROP TABLE IF EXISTS `c024`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c024` (
  `C024_Banco_Dados` varchar(10) NOT NULL,
  `C024_D024_Id` int(10) unsigned default '0',
  `C024_C026_Id` int(10) default '0',
  PRIMARY KEY  (`C024_Banco_Dados`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c024`
--

LOCK TABLES `c024` WRITE;
/*!40000 ALTER TABLE `c024` DISABLE KEYS */;
/*!40000 ALTER TABLE `c024` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c025`
--

DROP TABLE IF EXISTS `c025`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c025` (
  `C025_Id` int(10) unsigned NOT NULL auto_increment,
  `C025_D024_Id` varchar(10) NOT NULL default '0',
  `C025_Banco_Dados` varchar(10) default NULL,
  `C025_Login` varchar(20) default NULL,
  `C025_Senha` varchar(10) default NULL,
  `C025_Email` varchar(60) default NULL,
  PRIMARY KEY  (`C025_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c025`
--

LOCK TABLES `c025` WRITE;
/*!40000 ALTER TABLE `c025` DISABLE KEYS */;
/*!40000 ALTER TABLE `c025` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c026`
--

DROP TABLE IF EXISTS `c026`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c026` (
  `C026_Id` int(10) unsigned NOT NULL auto_increment,
  `C026_Razao_Social` varchar(40) default NULL,
  `C026_Nome_Contador` varchar(40) default NULL,
  `C026_Nome_Contato` varchar(40) default NULL,
  `C026_Telefone` varchar(40) default NULL,
  `C026_Observacao` text,
  `C026_Login` varchar(20) default NULL,
  `C026_Senha` varchar(20) default NULL,
  PRIMARY KEY  (`C026_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c026`
--

LOCK TABLES `c026` WRITE;
/*!40000 ALTER TABLE `c026` DISABLE KEYS */;
/*!40000 ALTER TABLE `c026` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d001`
--

DROP TABLE IF EXISTS `d001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d001` (
  `D001_Id` int(10) unsigned NOT NULL auto_increment,
  `D001_D037_Id` int(10) unsigned default NULL,
  `D001_D002_Id` int(10) unsigned default NULL,
  `D001_D060_Id` int(3) unsigned NOT NULL,
  `D001_D003_Id` int(10) unsigned default NULL,
  `D001_D015_Id` int(10) unsigned default NULL,
  `D001_C008_Id` int(10) unsigned default NULL,
  `D001_D005_Id` int(10) unsigned default NULL,
  `D001_Codigo_Produto` varchar(30) default NULL,
  `D001_Descricao_Produto` varchar(60) default NULL,
  `D001_Descricao_Comercial` varchar(40) default NULL,
  `D001_Data_Ultimo_Movimento` date default NULL,
  `D001_Peso_Unitario_Kg` decimal(10,3) NOT NULL default '0.000',
  `D001_Data_Cadastro` date default NULL,
  `D001_Aplicacao` varchar(60) default NULL,
  `D001_Flag_Ativo` varchar(1) default NULL,
  `D001_Foto` varchar(30) default NULL,
  `D001_Codigo_Descricao` int(5) default NULL,
  `D001_Codigo_Linha` varchar(11) default NULL,
  `D001_Codigo_Grupo` int(5) default NULL,
  `D001_Quantidade_Comprar` int(10) default '0',
  `D001_Frequencia_Venda` int(10) default '0',
  `D001_Flag_Classe` varchar(1) default NULL,
  `D001_Observacao_Compra` text,
  `D001_Especificacoes` text,
  `D001_Codigo_SubGrupo` int(5) default NULL,
  `D001_Flag_Tipo` enum('PRODUZIDO','COMPRADO') default NULL,
  `D001_Flag_Permite_Venda_Sem_Estoque` enum('S','N') default 'N',
  `D001_Flag_Visualizar_Site` enum('S','N') default 'S',
  `D001_Codigo_Barras` varchar(13) default NULL,
  `D001_Peso_Total_Kg` decimal(10,3) default '0.000',
  `D001_Qte_Embalagem` decimal(10,3) default '0.000',
  PRIMARY KEY  (`D001_Id`),
  KEY `D001_Index_Unidades` (`D001_D037_Id`),
  KEY `D001_Index_Linha_Id` (`D001_D003_Id`),
  KEY `D001_Index_Descricao_Id` (`D001_D002_Id`),
  KEY `D001_Index_Grupo_Id` (`D001_D015_Id`),
  KEY `D001_index_Codigo_Produto` (`D001_Codigo_Produto`),
  KEY `D001_Index_Codigo_Grupo` (`D001_Codigo_Grupo`),
  KEY `D001_Index_Tipo` (`D001_C008_Id`),
  KEY `IDX_Descricao` (`D001_Descricao_Produto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d001`
--

LOCK TABLES `d001` WRITE;
/*!40000 ALTER TABLE `d001` DISABLE KEYS */;
/*!40000 ALTER TABLE `d001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d002`
--

DROP TABLE IF EXISTS `d002`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d002` (
  `D002_Id` int(10) unsigned NOT NULL auto_increment,
  `D002_D060_Id` int(3) unsigned NOT NULL,
  `D002_C008_Id` int(10) unsigned default NULL,
  `D002_Codigo` int(5) default NULL,
  `D002_Descricao_Produto` varchar(50) default NULL,
  `D002_Grupo` int(5) default NULL,
  `D002_IPV` decimal(10,4) default NULL,
  `D002_Flag_Visualizar_Site` enum('S','N') default 'S',
  `D002_Codigo_Padrao` varchar(10) default '',
  `D002_Descricao_Padrao` varchar(30) default '',
  PRIMARY KEY  (`D002_Id`),
  KEY `D002_index_Codigo` (`D002_Codigo`),
  KEY `D002_index_Tipo` (`D002_C008_Id`),
  KEY `IDX_Descricao` (`D002_Descricao_Produto`),
  KEY `D002_Grupo` (`D002_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d002`
--

LOCK TABLES `d002` WRITE;
/*!40000 ALTER TABLE `d002` DISABLE KEYS */;
/*!40000 ALTER TABLE `d002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d003`
--

DROP TABLE IF EXISTS `d003`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d003` (
  `D003_Id` int(10) unsigned NOT NULL auto_increment,
  `D003_C008_Id` int(10) unsigned default NULL,
  `D003_Nome_Linha` varchar(30) default NULL,
  `D003_Flag_Tipo` varchar(2) default NULL,
  `D003_Flag_Trabalho` varchar(1) default NULL,
  `D003_Flag_Visualizar_Site` enum('S','N') default 'S',
  `D003_Codigo_Padrao` varchar(10) default '',
  `D003_Descricao_Padrao` varchar(30) default '',
  `D003_D082_Id` int(10) default NULL,
  PRIMARY KEY  (`D003_Id`),
  KEY `D003_index_Nome_Linha` (`D003_Nome_Linha`),
  KEY `D003_index_Tipo` (`D003_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d003`
--

LOCK TABLES `d003` WRITE;
/*!40000 ALTER TABLE `d003` DISABLE KEYS */;
/*!40000 ALTER TABLE `d003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d004`
--

DROP TABLE IF EXISTS `d004`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d004` (
  `D004_Id` int(10) unsigned NOT NULL auto_increment,
  `D004_C004_Id` int(11) NOT NULL default '0',
  `D004_C008_Id` int(11) NOT NULL default '0',
  `D004_Local` varchar(10) default NULL,
  `D004_Descricao` varchar(200) default NULL,
  PRIMARY KEY  (`D004_Id`),
  KEY `D004_Index_Local` (`D004_Local`),
  KEY `D004_Index_C004_Id` (`D004_C004_Id`),
  KEY `D004_Index_D004_Id` (`D004_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d004`
--

LOCK TABLES `d004` WRITE;
/*!40000 ALTER TABLE `d004` DISABLE KEYS */;
/*!40000 ALTER TABLE `d004` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d005`
--

DROP TABLE IF EXISTS `d005`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d005` (
  `D005_Id` int(10) unsigned NOT NULL auto_increment,
  `D005_Classificacao_Fiscal` varchar(12) default NULL,
  `D005_Flag_Isento_IPI` int(1) default '2',
  `D005_Percentual_Reducao_IPI` int(10) unsigned default NULL,
  `D005_Aliquota_IPI` decimal(5,2) default '0.00',
  `D005_Observacao` text,
  `D005_Flag_Tipo_IPI` varchar(1) default NULL,
  `D005_Aliquota_PIS` decimal(5,2) default '0.00',
  `D005_Aliquota_COFINS` decimal(5,2) default '0.00',
  `D005_Aliquota_Imposto_Importacao` decimal(5,2) default '0.00',
  `D005_Flag_ST` varchar(1) default NULL,
  `D005_Flag_VA` varchar(1) default NULL,
  `D005_Valor_VA` decimal(10,2) default NULL,
  PRIMARY KEY  (`D005_Id`),
  KEY `D005_index_Classificacao_Fiscal` (`D005_Classificacao_Fiscal`)
) ENGINE=MyISAM AUTO_INCREMENT=933 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d005`
--

LOCK TABLES `d005` WRITE;
/*!40000 ALTER TABLE `d005` DISABLE KEYS */;
/*!40000 ALTER TABLE `d005` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d006`
--

DROP TABLE IF EXISTS `d006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d006` (
  `D006_Id` int(10) unsigned NOT NULL auto_increment,
  `D006_D014_Id` int(10) unsigned default NULL,
  `D006_D058_Id` int(11) default NULL,
  `D006_D059_Id` int(11) default '0',
  `D006_D006_Id` int(10) unsigned default NULL,
  `D006_Codigo_Natureza` varchar(10) default NULL,
  `D006_Codigo_CFOP` varchar(30) default NULL,
  `D006_Descricao` varchar(51) default NULL,
  `D006_Flag_Venda_Compra_Outros` varchar(2) default NULL,
  `D006_Flag_Gera_Contas` varchar(1) default NULL,
  `D006_Flag_Estoque` varchar(1) default NULL,
  `D006_Flag_Estado` varchar(1) default NULL,
  `D006_Flag_Devolucao` varchar(1) default NULL,
  `D006_Flag_Isento_ICMS` varchar(1) default NULL,
  `D006_Flag_Isento_IPI` varchar(1) NOT NULL default 'N',
  `D006_Flag_Retorno` varchar(1) default NULL,
  `D006_Flag_Deb_Crd_ICMS` varchar(1) default NULL,
  `D006_Flag_Deb_Crd_IPI` varchar(1) default NULL,
  `D006_Flag_Entrada_Saida` varchar(1) default NULL,
  `D006_Flag_Venda_Maquina` varchar(1) default NULL,
  `D006_Flag_Inativo` varchar(1) default NULL,
  `D006_Flag_MMF` varchar(1) NOT NULL default 'N',
  `D006_Flag_Atualiza_Custo` varchar(1) NOT NULL default 'N',
  `D006_Flag_Departamento` varchar(20) default NULL,
  `D006_Observacao` text,
  `D006_Percentual_Reducao_ICMS` decimal(6,2) NOT NULL default '0.00',
  `D006_Contra_Partida` int(5) NOT NULL default '0',
  `D006_Flag_Zona_Franca` varchar(1) default NULL,
  `D006_Flag_Comercial_Exportadora` varchar(1) default NULL,
  `D006_Numero_Dias_Retorno` int(3) default '0',
  `D006_Flag_Substituicao_Tributaria` varchar(1) default 'N',
  `D006_Flag_ST_VA` varchar(1) default 'S',
  `D006_Descricao_Comercial` varchar(50) default NULL,
  `D006_Flag_Isento_PIS` enum('S','N') default 'N',
  `D006_Flag_Forma_Calculo_PIS` enum('V','L') default 'V',
  `D006_Situacao_Tributaria_PIS` varchar(2) default '01',
  `D006_Flag_Isento_COFINS` enum('S','N') default 'N',
  `D006_Flag_Forma_Calculo_COFINS` enum('V','L') default 'V',
  `D006_Situacao_Tributaria_COFINS` varchar(2) default '01',
  `D006_Flag_Somar_IPI_Base_ICMS` enum('S','N') default 'N',
  PRIMARY KEY  (`D006_Id`),
  KEY `D006_index_Codigo_Natureza` (`D006_Codigo_Natureza`),
  KEY `D006_index_Codigo_CFOP` (`D006_Codigo_CFOP`),
  KEY `D006_FKIndex1` (`D006_D006_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d006`
--

LOCK TABLES `d006` WRITE;
/*!40000 ALTER TABLE `d006` DISABLE KEYS */;
INSERT INTO `d006` VALUES (1,0,NULL,6,0,'','5.102','VENDA  MERC. AD OU REC DE TERCEIROS C/DIFERIMENTO','V','R','N','D','N','S','S','N','N','N','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA  MERC. AD OU REC DE TERCEIROS C/DIFERIMENTO','S','V','99','S','V','99','N'),(2,0,NULL,6,0,'','6.102','VENDA DE MERCADORIA AD OU REC DE TERCEIROS','V','R','N','F','N','S','S','N','N','N','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA DE MERCADORIA AD OU REC DE TERCEIROS','S','V','99','S','V','99','N'),(9,0,NULL,6,0,'','6.101','VENDA PRODUCAO DO ESTABELECIMENTO','V','R','N','F','N','S','S','N','N','N','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','S','VENDA PRODUCAO DO ESTABELECIMENTO','S','V','99','S','V','99','N'),(10,0,NULL,6,0,'','5.911','AMOSTRA GRÁTIS','O','N','N','D','N','S','S','N','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','AMOSTRA GRÁTIS','S','V','99','S','V','99','N'),(11,0,NULL,6,0,'','6.911','AMOSTRA GRÁTIS','O','N','N','F','N','S','S','N','N','N','S',NULL,'N','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','AMOSTRA GRÁTIS','S','V','99','S','V','99','N'),(12,0,NULL,6,0,'','5.915','REMESSA PARA CONCERTO','O','N','N','D','S','S','S','N','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','REMESSA PARA CONCERTO','S','V','99','S','V','99','N'),(13,0,NULL,6,0,'','6.915','REMESSA PARA CONCERTO','O','N','N','F','S','S','S','N','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','S','REMESSA PARA CONCERTO','S','V','99','S','V','99','N'),(14,0,NULL,6,0,'','5.916','RETORNO DE MERCADORIA OU BEM RECEBIDO PARA CONCERT','O','N','N','D','S','S','S','S','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','S','RETORNO DE MERCADORIA OU BEM RECEBIDO PARA O CONCE','S','V','99','S','V','99','N'),(16,0,NULL,6,0,'','5.901','REMESSA PARA INDUSTRIALIZAÇÃO POR ENCOMENDA','O','N','N','D','S','S','S','S','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',180,'N','S','REMESSA PARA INDUSTRIALIZAÇÃO POR ENCOMENDA','S','V','99','S','V','99','N'),(17,0,NULL,6,0,'','6.901','REMESSA PARA INDUSTRIALIZAÇÃO POR ENCOMENDA','O','N','N','F','S','S','S','S','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',180,'N','S','REMESSA PARA INDUSTRIALIZAÇÃO POR ENCOMENDA','S','V','99','S','V','99','N'),(18,0,NULL,6,0,'','5.902','RETORNO DE INDUSTRIALIZAÇÃO','O','N','N','D','N','S','S','N','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','S','RETORNO DE INDUSTRIALIZAÇÃO','S','V','99','S','V','99','N'),(19,0,NULL,6,0,'','6.902','RETORNO DE INDUSTRIALIZAÇÃO','O','N','N','F','N','S','S','N','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','S','RETORNO DE INDUSTRIALIZAÇÃO','S','V','99','S','V','99','N'),(21,0,NULL,6,0,'','6.916','RETORNO DE MERCADORIA OU BEM RECEBIDO PARA CONCERT','O','N','N','F','N','S','S','N','N','N','S',NULL,'','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','RETORNO DE MERCADORIA OU BEM RECEBIDO PARA CONCERT','S','V','99','S','V','99','N'),(22,0,NULL,6,0,'','5.101','VENDA DE PRODUCAO DO ESTABELECIMENTO','V','R','N','D','N','S','S','N','N','S','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA PRODUCAO DO ESTABELECIMENTO','S','V','99','S','V','99','N'),(23,0,NULL,6,0,'','5.910','BONIFICACAO/BRINDE/DOACAO','O','N','N','D','N','S','S','N','N','N','S',NULL,'N','N','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','BONIFICACAO/BRINDE/DOACAO','S','V','99','S','V','99','N'),(24,0,NULL,6,0,'','6.107','VENDA DE PRODUÇÃO DO ESTABELECIMENTO, NÃO CONTRIBU','V','R','N','F','N','S','S','N','N','N','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA PROD.ESTABELECIMENTO, NÃO CONTRIBUINTE','S','V','99','S','V','99','N'),(25,0,NULL,6,0,'','6.108','VENDA MERCADORIA AD.REC.TERCEIROS, NÃO CONTRIB.','V','R','N','F','N','S','S','N','N','N','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA MERC.AD.REC.TERCEIROS, NÃO CONTRIBUINTE','S','V','99','S','V','99','N'),(26,0,NULL,6,0,'','5.116','VENDA PROD.ESTABELECIMENTO, ENTREGA FUTURA','V','R','N','D','N','S','S','N','N','N','S',NULL,'N','S','N','COMERCIAL','','0.00',0,'N','N',0,'N','N','VENDA PROD. ESTABELECIMENTO, ENTREGA FUTURA','S','V','99','S','V','99','N');
/*!40000 ALTER TABLE `d006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d007`
--

DROP TABLE IF EXISTS `d007`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d007` (
  `D007_Id` int(10) unsigned NOT NULL auto_increment,
  `D007_C004_Id` int(11) NOT NULL default '0',
  `D007_D062_Id` int(11) default NULL,
  `D007_Codigo_Conta` int(10) unsigned NOT NULL default '0',
  `D007_Nome_Banco` varchar(15) default NULL,
  `D007_Numero_Agencia` int(4) NOT NULL default '0',
  `D007_Digito_Verificador_Agencia` char(3) default NULL,
  `D007_Numero_Conta` varchar(10) default NULL,
  `D007_Digito_Verificador_Conta` char(3) default NULL,
  `D007_Valor_Saldo` decimal(14,2) NOT NULL default '0.00',
  `D007_Valor_Aplicado` decimal(14,2) NOT NULL default '0.00',
  `D007_Flag_Layout` varchar(1) default NULL,
  `D007_Flag_Com_Registro` enum('S','N') default 'N',
  `D007_Numero_Convenio` int(6) default NULL,
  `D007_Tipo_Carteira_Banco_Brasil` varchar(10) default NULL,
  `D007_Valor_Saldo_Inicial` decimal(10,2) default '0.00',
  `D007_Numero_Contrato` int(10) unsigned default '0',
  `D007_Codigo_Baixa` varchar(30) default NULL,
  `D007_Nosso_Numero` varchar(30) default NULL,
  `D007_Aceite` enum('S','N') default 'N',
  `D007_Especie` varchar(30) default NULL,
  `D007_Especie_Documento` varchar(30) default NULL,
  `D007_Uso_Banco` varchar(30) default NULL,
  `D007_Percentual_Juros_Dia` decimal(10,2) default '0.00',
  `D007_Percentual_Multa` decimal(10,2) default '0.00',
  `D007_Instrucoes_Boleto` text,
  `D007_Codigo_Cedente` int(10) default '0',
  `D007_Numero_Range` varchar(10) default NULL,
  PRIMARY KEY  (`D007_Id`),
  KEY `D007_Index_Codigo_Banco` (`D007_Codigo_Conta`),
  KEY `D007_Index_C004_Id` (`D007_C004_Id`),
  KEY `D007_D062_Id` (`D007_D062_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d007`
--

LOCK TABLES `d007` WRITE;
/*!40000 ALTER TABLE `d007` DISABLE KEYS */;
/*!40000 ALTER TABLE `d007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d008`
--

DROP TABLE IF EXISTS `d008`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d008` (
  `D008_Id` int(11) NOT NULL auto_increment,
  `D008_Comprimento` decimal(5,2) default '0.00',
  `D008_Altura` decimal(5,2) default '0.00',
  `D008_Espaco_Entre_Colunas` decimal(5,2) default '0.00',
  `D008_Numero_Colunas` int(1) default '0',
  `D008_Etiquetas_Coluna` int(2) default '0',
  PRIMARY KEY  (`D008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d008`
--

LOCK TABLES `d008` WRITE;
/*!40000 ALTER TABLE `d008` DISABLE KEYS */;
/*!40000 ALTER TABLE `d008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d009`
--

DROP TABLE IF EXISTS `d009`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d009` (
  `D009_Id` int(10) unsigned NOT NULL auto_increment,
  `D009_D024_Id` int(10) unsigned default NULL,
  `D009_D004_Id` int(10) unsigned default NULL,
  `D009_D001_Id` int(10) unsigned default NULL,
  `D009_D049_Id` int(10) unsigned default NULL,
  `D009_C004_Id` int(11) NOT NULL default '0',
  `D009_Codigo_Produto` varchar(30) default NULL,
  `D009_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `D009_Descricao_Produto` varchar(60) default NULL,
  `D009_Valor_Custo_Medio_Unitario` decimal(14,2) NOT NULL default '0.00',
  `D009_Quantidade_Estoque` decimal(10,2) NOT NULL default '0.00',
  `D009_Quantidade_Estoque_Real` decimal(10,2) default '0.00',
  `D009_Quantidade_Maxima_Estoque` decimal(10,2) default NULL,
  `D009_Quantidade_Comprar` decimal(10,2) default NULL,
  `D009_Quantidade_Pedido` int(10) default '0',
  `D009_Frequencia_Venda` int(5) NOT NULL default '0',
  `D009_Quantidade_Minima_Estoque` decimal(10,2) default NULL,
  `D009_Quantidade_Seguranca_Estoque` int(10) NOT NULL default '0',
  `D009_Data_Ultimo_Movimento` date NOT NULL default '0000-00-00',
  `D009_Peso_Unitario_Kg` decimal(10,3) NOT NULL default '0.000',
  `D009_Valor_Custo_Unitario` decimal(10,2) NOT NULL default '0.00',
  `D009_Data_Cadastro` date NOT NULL default '0000-00-00',
  `D009_Aplicacao` varchar(60) default NULL,
  `D009_Flag_Ativo` varchar(1) default NULL,
  `D009_Flag_Nacional_Importado` varchar(1) default NULL,
  `D009_Ordem_Frequencia` int(2) NOT NULL default '0',
  `D009_Flag_Mono` varchar(1) default NULL,
  `D009_Flag_Base_ICMS_Cheia_NF` int(1) default '1',
  `D009_Flag_Isento_ICMS_Venda` int(1) default '2',
  `D009_Percentual_Reducao_ICMS_Venda` decimal(6,2) default '0.00',
  `D009_Aliquota_ICMS_Venda` decimal(6,2) default '0.00',
  `D009_Flag_Isento_ICMS` int(1) default '2',
  `D009_Percentual_Reducao_ICMS` decimal(6,2) NOT NULL default '0.00',
  `D009_Aliquota_ICMS` decimal(6,2) NOT NULL default '0.00',
  `D009_Flag_Isento_IPI` int(1) default '2',
  `D009_Percentual_Reducao_IPI` decimal(6,2) NOT NULL default '0.00',
  `D009_Aliquota_IPI` decimal(6,2) NOT NULL default '0.00',
  `D009_Codigo_Fornecedor` varchar(6) default NULL,
  `D009_Codigo_Descricao` int(5) default NULL,
  `D009_Codigo_Linha` varchar(11) default NULL,
  `D009_Codigo_Local` varchar(10) default NULL,
  `D009_Prazo_Entrega` int(10) default '0',
  `D009_Marca` varchar(20) default NULL,
  `D009_Quantidade_Estoque_Parado_1_Ano` int(10) default '0',
  `D009_Valor_Estoque_Parado_1_ano` decimal(10,2) default '0.00',
  `D009_Quantidade_Estoque_Parado_2_Ano` int(10) default '0',
  `D009_Valor_Estoque_Parado_2_ano` decimal(10,2) default '0.00',
  `D009_Data_Ultima_Saida` date NOT NULL default '0000-00-00',
  `D009_Data_Ultima_Entrada` date NOT NULL default '0000-00-00',
  `D009_Quantidade_Estoque_2` int(5) NOT NULL default '0',
  `D009_Codigo_Local_2` varchar(10) default NULL,
  `D009_Flag_Comprar` varchar(1) default NULL,
  `D009_Meses_Compra` int(3) default NULL,
  `D009_Data_Ultima_Venda` date NOT NULL default '0000-00-00',
  `D009_C007_Id_Comprar` int(11) default NULL,
  `D009_Data_Comprar` datetime default NULL,
  `D009_Quantidade_Estoque_Importado` decimal(10,3) default NULL,
  `D009_Estoque_Inicial` decimal(10,2) default NULL,
  `D009_Valor_Custo_Importado` decimal(10,2) default NULL,
  `D009_Quantidade_Comprar_Calculada` decimal(10,2) default NULL,
  `D009_Valor_Custo_Compra` decimal(12,5) default NULL,
  `D009_Flag_Travar_Custo` varchar(1) default NULL,
  `D009_Dados_Adicionais` text,
  `D009_Valor_Melhor_Custo` decimal(12,5) default NULL,
  PRIMARY KEY  (`D009_Id`),
  UNIQUE KEY `UK` (`D009_C004_Id`,`D009_D049_Id`),
  KEY `D009_Index_Id` (`D009_Id`),
  KEY `D009_Index_C004_Id` (`D009_C004_Id`),
  KEY `D009_Index_Data_Ultimo_Movimento` (`D009_Data_Ultimo_Movimento`),
  KEY `D009_Index_Locacao_Id` (`D009_D004_Id`),
  KEY `D009_Index_Codigo_Produto_Id` (`D009_Codigo_Produto`),
  KEY `D009_Index_Codigo_Fornecedor` (`D009_Codigo_Fornecedor`),
  KEY `D009_Index_Produtos` (`D009_D001_Id`),
  KEY `D009_Index_Produto_Fornecedor` (`D009_D049_Id`),
  KEY `D009_Index_Fornecedores` (`D009_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d009`
--

LOCK TABLES `d009` WRITE;
/*!40000 ALTER TABLE `d009` DISABLE KEYS */;
/*!40000 ALTER TABLE `d009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d010`
--

DROP TABLE IF EXISTS `d010`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d010` (
  `D010_Id` int(10) unsigned NOT NULL auto_increment,
  `D010_D021_Id` int(10) unsigned default NULL,
  `D010_Codigo_Contato` int(6) default NULL,
  `D010_Cpf` varchar(14) default NULL,
  `D010_Nome_Contato` varchar(40) default NULL,
  `D010_Funcao` varchar(40) default NULL,
  `D010_Endereco` varchar(40) default NULL,
  `D010_Data_Nascimento` date default NULL,
  `D010_Telefone_1` varchar(30) default NULL,
  `D010_Telefone_2` varchar(30) default NULL,
  `D010_E_Mail` varchar(60) default NULL,
  `D010_Site` varchar(60) default NULL,
  `D010_Celular` varchar(30) default NULL,
  `D010_Cidade` varchar(25) default NULL,
  `D010_Codigo_Empresa` varchar(6) default NULL,
  `D010_Cnpj` varchar(18) default NULL,
  `D010_Ramal` varchar(10) default NULL,
  `D010_Fax` varchar(30) default NULL,
  `D010_Estado` varchar(10) default NULL,
  PRIMARY KEY  (`D010_Id`),
  KEY `D010_Index_Representante` (`D010_D021_Id`),
  KEY `D010_Index_Codigo_Contato` (`D010_Codigo_Contato`),
  KEY `D010_Index_Telefone_1` (`D010_Telefone_1`),
  KEY `D010_Index_Telefone_2` (`D010_Telefone_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d010`
--

LOCK TABLES `d010` WRITE;
/*!40000 ALTER TABLE `d010` DISABLE KEYS */;
/*!40000 ALTER TABLE `d010` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d011`
--

DROP TABLE IF EXISTS `d011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d011` (
  `D011_Id` int(10) unsigned NOT NULL auto_increment,
  `D011_D022_Id` int(10) unsigned default NULL,
  `D011_Codigo_Contato` int(6) default NULL,
  `D011_Cpf` varchar(14) default NULL,
  `D011_Nome_Contato` varchar(40) default NULL,
  `D011_Funcao` varchar(40) default NULL,
  `D011_Endereco` varchar(40) default NULL,
  `D011_Data_Nascimento` date default NULL,
  `D011_Telefone_1` varchar(30) default NULL,
  `D011_Telefone_2` varchar(30) default NULL,
  `D011_E_Mail` varchar(60) default NULL,
  `D011_Site` varchar(60) default NULL,
  `D011_Celular` varchar(30) default NULL,
  `D011_Cidade` varchar(25) default NULL,
  `D011_Codigo_Empresa` varchar(6) default NULL,
  `D011_Cnpj` varchar(18) default NULL,
  `D011_Ramal` varchar(10) default NULL,
  `D011_Fax` varchar(30) default NULL,
  `D011_Estado` varchar(10) default NULL,
  PRIMARY KEY  (`D011_Id`),
  KEY `D011_Index_Transportadora` (`D011_D022_Id`),
  KEY `D011_Index_Codigo_Contato` (`D011_Codigo_Contato`),
  KEY `D011_Index_Telefone_1` (`D011_Telefone_1`),
  KEY `D011_Index_Telefone_2` (`D011_Telefone_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d011`
--

LOCK TABLES `d011` WRITE;
/*!40000 ALTER TABLE `d011` DISABLE KEYS */;
/*!40000 ALTER TABLE `d011` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d012`
--

DROP TABLE IF EXISTS `d012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d012` (
  `D012_Id` int(10) unsigned NOT NULL auto_increment,
  `D012_D023_Id` int(10) unsigned default NULL,
  `D012_Codigo_Contato` int(6) default NULL,
  `D012_Cpf` varchar(14) default NULL,
  `D012_Nome_Contato` varchar(40) default NULL,
  `D012_Funcao` varchar(40) default NULL,
  `D012_Endereco` varchar(40) default NULL,
  `D012_Data_Nascimento` date default NULL,
  `D012_Telefone_1` varchar(30) default NULL,
  `D012_Telefone_2` varchar(30) default NULL,
  `D012_E_Mail` varchar(60) default NULL,
  `D012_Site` varchar(60) default NULL,
  `D012_Celular` varchar(30) default NULL,
  `D012_Cidade` varchar(25) default NULL,
  `D012_Codigo_Empresa` varchar(6) default NULL,
  `D012_Cnpj` varchar(18) default NULL,
  `D012_Ramal` varchar(10) default NULL,
  `D012_Fax` varchar(30) default NULL,
  `D012_Estado` varchar(10) default NULL,
  PRIMARY KEY  (`D012_Id`),
  KEY `D011_Index_Codigo_Contato` (`D012_Codigo_Contato`),
  KEY `D011_Index_Telefone_1` (`D012_Telefone_1`),
  KEY `D011_Index_Telefone_2` (`D012_Telefone_2`),
  KEY `D012_Index_Fornecedor` (`D012_D023_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d012`
--

LOCK TABLES `d012` WRITE;
/*!40000 ALTER TABLE `d012` DISABLE KEYS */;
/*!40000 ALTER TABLE `d012` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d013`
--

DROP TABLE IF EXISTS `d013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d013` (
  `D013_Id` int(10) unsigned NOT NULL auto_increment,
  `D013_D024_Id` int(10) unsigned default NULL,
  `D013_Codigo_Contato` int(6) default NULL,
  `D013_Cpf` varchar(14) default NULL,
  `D013_Nome_Contato` varchar(40) default NULL,
  `D013_Funcao` varchar(40) default NULL,
  `D013_Endereco` varchar(40) default NULL,
  `D013_Data_Nascimento` date default NULL,
  `D013_Telefone_1` varchar(30) default NULL,
  `D013_Telefone_2` varchar(30) default NULL,
  `D013_E_Mail` varchar(60) default NULL,
  `D013_Site` varchar(60) default NULL,
  `D013_Celular` varchar(30) default NULL,
  `D013_Cidade` varchar(25) default NULL,
  `D013_Codigo_Empresa` varchar(6) default NULL,
  `D013_Cnpj` varchar(18) default NULL,
  `D013_Ramal` varchar(10) default NULL,
  `D013_Fax` varchar(30) default NULL,
  `D013_Estado` varchar(10) default NULL,
  `D013_Flag_Departamento` int(11) default '0',
  PRIMARY KEY  (`D013_Id`),
  KEY `D011_Index_Codigo_Contato` (`D013_Codigo_Contato`),
  KEY `D011_Index_Telefone_1` (`D013_Telefone_1`),
  KEY `D011_Index_Telefone_2` (`D013_Telefone_2`),
  KEY `D012_Index_Clientes` (`D013_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d013`
--

LOCK TABLES `d013` WRITE;
/*!40000 ALTER TABLE `d013` DISABLE KEYS */;
/*!40000 ALTER TABLE `d013` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d014`
--

DROP TABLE IF EXISTS `d014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d014` (
  `D014_Id` int(10) unsigned NOT NULL auto_increment,
  `D014_D032_Id` int(10) unsigned default NULL,
  `D014_Conta` int(2) default NULL,
  `D014_SubConta` varchar(30) default NULL,
  `D014_Flag_Reserva` varchar(1) default NULL,
  `D014_Flag_Plano` varchar(4) default NULL,
  `D014_Flag_Tipo` varchar(2) default NULL,
  `D014_Observacao` text,
  `D014_Data_Inicio` date default NULL,
  `D014_Data_Fim` date default NULL,
  `D014_Grupo` varchar(15) default NULL,
  `D014_Codigo` int(10) NOT NULL default '0',
  `D014_Dia_Vencimento` varchar(2) default NULL,
  PRIMARY KEY  (`D014_Id`),
  KEY `D014_Index_Id` (`D014_Id`),
  KEY `D014_Index_Conta` (`D014_Conta`),
  KEY `D014_Index_SubConta` (`D014_SubConta`),
  KEY `D014_Index_Codigo` (`D014_Codigo`),
  KEY `D014_Index_Tipos_Plano_Contas` (`D014_D032_Id`),
  KEY `D014_Index_Grupo` (`D014_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d014`
--

LOCK TABLES `d014` WRITE;
/*!40000 ALTER TABLE `d014` DISABLE KEYS */;
/*!40000 ALTER TABLE `d014` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d015`
--

DROP TABLE IF EXISTS `d015`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d015` (
  `D015_Id` int(10) unsigned NOT NULL auto_increment,
  `D015_C008_Id` int(10) unsigned default NULL,
  `D015_Codigo` varchar(4) default NULL,
  `D015_Nome_Grupo` varchar(30) default NULL,
  `D015_Codigo_Padrao` varchar(10) default '',
  `D015_Descricao_Padrao` varchar(30) default '',
  PRIMARY KEY  (`D015_Id`),
  KEY `D015_Index_Codigo` (`D015_Codigo`),
  KEY `D015_Index_Tipos` (`D015_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d015`
--

LOCK TABLES `d015` WRITE;
/*!40000 ALTER TABLE `d015` DISABLE KEYS */;
/*!40000 ALTER TABLE `d015` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d016`
--

DROP TABLE IF EXISTS `d016`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d016` (
  `D016_Id` int(10) unsigned NOT NULL auto_increment,
  `D016_Data` date NOT NULL,
  `D016_Valor_Engepecas` decimal(12,4) NOT NULL default '0.0000',
  `D016_Valor_Oficial_Dolar` decimal(12,4) NOT NULL default '0.0000',
  `D016_Valor_Oficial_Euro` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`D016_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d016`
--

LOCK TABLES `d016` WRITE;
/*!40000 ALTER TABLE `d016` DISABLE KEYS */;
/*!40000 ALTER TABLE `d016` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d017`
--

DROP TABLE IF EXISTS `d017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d017` (
  `D017_Id` int(10) unsigned NOT NULL auto_increment,
  `D017_D001_Id` int(10) unsigned default NULL,
  `D017_C007_Id` int(10) unsigned default NULL,
  `D017_C008_Id` int(10) unsigned default NULL,
  `D017_Codigo_Novo` varchar(18) default NULL,
  `D017_Codigo_Antigo` varchar(18) default NULL,
  `D017_Data_Inclusao` date default NULL,
  `D017_Flag_Mudanca` varchar(1) default NULL,
  `D017_Descricao` varchar(30) NOT NULL,
  PRIMARY KEY  (`D017_Id`),
  KEY `D017_Index_D001_Id` (`D017_D001_Id`),
  KEY `D017_Index_C007_Id` (`D017_C007_Id`),
  KEY `D017_Index_C008_Id` (`D017_C008_Id`),
  KEY `D017_Index_Codigo_Novo` (`D017_Codigo_Novo`),
  KEY `D017_Index_Codigo_Antigo` (`D017_Codigo_Antigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d017`
--

LOCK TABLES `d017` WRITE;
/*!40000 ALTER TABLE `d017` DISABLE KEYS */;
/*!40000 ALTER TABLE `d017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d018`
--

DROP TABLE IF EXISTS `d018`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d018` (
  `D018_Id` int(10) unsigned NOT NULL auto_increment,
  `D018_Codigo_IBGE` int(2) default NULL,
  `D018_UF` varchar(2) NOT NULL,
  `D018_Percentual_Reducao_ICMS_1` decimal(5,2) NOT NULL default '0.00',
  `D018_Aliquota_ICMS_1` decimal(5,2) NOT NULL default '0.00',
  `D018_Percentual_Reducao_ICMS_2` decimal(5,2) NOT NULL default '0.00',
  `D018_Aliquota_ICMS_2` decimal(5,2) NOT NULL default '0.00',
  `D018_Percentual_Reducao_ICMS_Revenda` decimal(5,2) NOT NULL default '0.00',
  `D018_Aliquota_ICMS_Revenda` decimal(5,2) NOT NULL default '0.00',
  `D018_Percentual_Reducao_ICMS_Consumidor` decimal(5,2) NOT NULL default '0.00',
  `D018_Aliquota_ICMS_Consumidor` decimal(5,2) NOT NULL default '0.00',
  `D018_Pais` varchar(2) NOT NULL,
  `D018_Maquinas` int(10) NOT NULL default '0',
  `D018_Clientes` int(10) NOT NULL default '0',
  `D018_Atendidos` int(10) NOT NULL default '0',
  `D018_Cidades` int(10) NOT NULL default '0',
  `D018_Motores` int(10) NOT NULL default '0',
  `D018_Com_Maquina` int(10) NOT NULL default '0',
  `D018_Com_Compra` int(10) NOT NULL default '0',
  `D018_Estado` varchar(40) NOT NULL,
  `D018_Observacao` text NOT NULL,
  PRIMARY KEY  (`D018_Id`),
  KEY `D018_IDX_UF` (`D018_UF`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d018`
--

LOCK TABLES `d018` WRITE;
/*!40000 ALTER TABLE `d018` DISABLE KEYS */;
/*!40000 ALTER TABLE `d018` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d019`
--

DROP TABLE IF EXISTS `d019`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d019` (
  `D019_Id` int(10) unsigned NOT NULL auto_increment,
  `D019_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `D019_IPV_Minimo` decimal(10,4) NOT NULL default '0.0000',
  `D019_IPV_Maximo` decimal(10,4) NOT NULL default '0.0000',
  `D019_Percentual_Desconto_ACP` decimal(10,4) NOT NULL default '0.0000',
  `D019_Tipo_1` decimal(10,2) NOT NULL default '0.00',
  `D019_Tipo_2` decimal(10,2) NOT NULL default '0.00',
  `D019_Fixas` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`D019_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d019`
--

LOCK TABLES `d019` WRITE;
/*!40000 ALTER TABLE `d019` DISABLE KEYS */;
/*!40000 ALTER TABLE `d019` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d020`
--

DROP TABLE IF EXISTS `d020`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d020` (
  `D020_Id` int(10) unsigned NOT NULL auto_increment,
  `D020_D084_Id` int(10) unsigned default NULL,
  `D020_D018_Id` int(10) unsigned NOT NULL,
  `D020_Codigo_Cidade` varchar(5) NOT NULL,
  `D020_Codigo_IBGE` int(7) default NULL,
  `D020_UF` varchar(2) NOT NULL,
  `D020_Nome_Cidade` varchar(40) NOT NULL,
  `D020_Flag_IPI` enum('S','N') default 'N',
  `D020_Flag_ICMS` enum('S','N') default 'N',
  `D020_Flag_ISS` enum('S','N') default 'N',
  `D020_Flag_Suframa` enum('S','N') default 'N',
  `D020_Observacao` text NOT NULL,
  `D020_Flag_Regiao` varchar(4) NOT NULL,
  `D020_Nome_Estado` varchar(40) NOT NULL,
  PRIMARY KEY  (`D020_Id`),
  KEY `D020_IDX_ESTADO` (`D020_D018_Id`),
  KEY `D020_IDX_CODIGO_CIDADE` (`D020_Codigo_Cidade`),
  KEY `D020_IDX_NOME_CIDADE` (`D020_Nome_Cidade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d020`
--

LOCK TABLES `d020` WRITE;
/*!40000 ALTER TABLE `d020` DISABLE KEYS */;
/*!40000 ALTER TABLE `d020` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d021`
--

DROP TABLE IF EXISTS `d021`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d021` (
  `D021_Id` int(10) unsigned NOT NULL auto_increment,
  `D021_D062_Id` int(11) default NULL,
  `D021_D018_Id` int(11) NOT NULL default '0',
  `D021_D020_Id` int(11) NOT NULL default '0',
  `D021_D030_Id` int(11) NOT NULL default '0',
  `D021_Codigo` varchar(6) default NULL,
  `D021_Nome_Empresa` varchar(50) default NULL,
  `D021_Classe` varchar(1) default NULL,
  `D021_Endereco` varchar(40) default NULL,
  `D021_Complemento` varchar(15) default NULL,
  `D021_Cep` varchar(9) default NULL,
  `D021_Bairro` varchar(25) default NULL,
  `D021_Cidade` varchar(30) default NULL,
  `D021_Estado` varchar(2) default NULL,
  `D021_Telefone_1` varchar(20) default NULL,
  `D021_Telefone_2` varchar(20) default NULL,
  `D021_Telex` varchar(20) default NULL,
  `D021_Fax` varchar(20) default NULL,
  `D021_Cnpj` varchar(20) default NULL,
  `D021_Inscricao_Estadual` varchar(20) default NULL,
  `D021_Flag_Tipo_Empresa` varchar(1) default NULL,
  `D021_Flag_Simples` varchar(1) default NULL,
  `D021_Pais` varchar(2) default NULL,
  `D021_Cpf` varchar(20) default NULL,
  `D021_Cobranca_Rua` varchar(40) default NULL,
  `D021_Cobranca_Complemento` varchar(15) default NULL,
  `D021_Cobranca_Bairro` varchar(25) default NULL,
  `D021_Cobranca_Cidade` varchar(30) default NULL,
  `D021_Cobranca_Uf` varchar(2) default NULL,
  `D021_Cobranca_Cep` varchar(9) default NULL,
  `D021_Cobranca_Pais` varchar(2) default NULL,
  `D021_Data_Fundacao` date default NULL,
  `D021_Flag_Suframa` varchar(1) default NULL,
  `D021_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `D021_Flag_Pessoa_Fisica_Juridica` varchar(1) default NULL,
  `D021_Valor_IPV` decimal(10,4) NOT NULL default '0.0000',
  `D021_Data_Irregular` date default NULL,
  `D021_Data_Atraso` date default NULL,
  `D021_Observacao_Compra` text,
  `D021_Flag_Custo_Zero` varchar(1) default NULL,
  `D021_Flag_Mono` varchar(1) default NULL,
  `D021_ISS` varchar(20) default NULL,
  `D021_Praca_Pagamento` varchar(65) default NULL,
  `D021_Aviso` varchar(65) default NULL,
  `D021_Observacoes` text,
  `D021_Percentual_Comissao` decimal(5,2) default '0.00',
  `D021_Agencia` varchar(10) default NULL,
  `D021_Conta_Bancaria` varchar(20) default NULL,
  `D021_Nome_Conta` varchar(50) default NULL,
  PRIMARY KEY  (`D021_Id`),
  KEY `D021_index_Codigo` (`D021_Codigo`),
  KEY `D021_index_Nome_Empresa` (`D021_Nome_Empresa`),
  KEY `D021_index_Cnpj` (`D021_Cnpj`),
  KEY `D021_index_Empresas_Estado` (`D021_D018_Id`),
  KEY `D021_index_Empresas_Cidade` (`D021_D020_Id`),
  KEY `D021_index_Empresas_Pais` (`D021_D030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d021`
--

LOCK TABLES `d021` WRITE;
/*!40000 ALTER TABLE `d021` DISABLE KEYS */;
/*!40000 ALTER TABLE `d021` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d022`
--

DROP TABLE IF EXISTS `d022`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d022` (
  `D022_Id` int(10) unsigned NOT NULL auto_increment,
  `D022_D036_Id` int(11) NOT NULL default '0',
  `D022_D018_Id` int(11) NOT NULL default '0',
  `D022_D020_Id` int(11) NOT NULL default '0',
  `D022_D030_Id` int(11) NOT NULL default '0',
  `D022_Codigo` varchar(6) default NULL,
  `D022_Nome_Empresa` varchar(50) default NULL,
  `D022_Classe` varchar(1) default NULL,
  `D022_Endereco` varchar(40) default NULL,
  `D022_Complemento` varchar(15) default NULL,
  `D022_Cep` varchar(9) default NULL,
  `D022_Bairro` varchar(25) default NULL,
  `D022_Cidade` varchar(30) default NULL,
  `D022_Estado` varchar(2) default NULL,
  `D022_Telefone_1` varchar(20) default NULL,
  `D022_Telefone_2` varchar(20) default NULL,
  `D022_Telex` varchar(20) default NULL,
  `D022_Fax` varchar(20) default NULL,
  `D022_Cnpj` varchar(20) default NULL,
  `D022_Inscricao_Estadual` varchar(20) default NULL,
  `D022_Flag_Tipo_Empresa` varchar(1) default NULL,
  `D022_Flag_Simples` varchar(1) default NULL,
  `D022_Pais` varchar(2) default NULL,
  `D022_Cpf` varchar(20) default NULL,
  `D022_Cobranca_Rua` varchar(40) default NULL,
  `D022_Cobranca_Complemento` varchar(15) default NULL,
  `D022_Cobranca_Bairro` varchar(25) default NULL,
  `D022_Cobranca_Cidade` varchar(30) default NULL,
  `D022_Cobranca_Uf` varchar(2) default NULL,
  `D022_Cobranca_Cep` varchar(9) default NULL,
  `D022_Cobranca_Pais` varchar(2) default NULL,
  `D022_Data_Fundacao` date default NULL,
  `D022_Flag_Suframa` varchar(1) default NULL,
  `D022_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `D022_Flag_Pessoa_Fisica_Juridica` varchar(1) default NULL,
  `D022_Valor_IPV` decimal(10,4) NOT NULL default '0.0000',
  `D022_Data_Irregular` date default NULL,
  `D022_Data_Atraso` date default NULL,
  `D022_Observacao_Compra` text,
  `D022_Flag_Custo_Zero` varchar(1) default NULL,
  `D022_Flag_Mono` varchar(1) default NULL,
  `D022_ISS` varchar(20) default NULL,
  `D022_Praca_Pagamento` varchar(65) default NULL,
  `D022_Aviso` varchar(65) default NULL,
  `D022_Observacoes` text,
  `D022_Email` varchar(50) default NULL,
  `D022_WebSite` varchar(50) default NULL,
  `D022_Nome_Fantasia` varchar(60) default NULL,
  `D022_Nome_Contato` varchar(30) default NULL,
  PRIMARY KEY  (`D022_Id`),
  KEY `D022_index_Tipo_Transporte` (`D022_D036_Id`),
  KEY `D022_index_Codigo` (`D022_Codigo`),
  KEY `D022_index_Nome_Empresa` (`D022_Nome_Empresa`),
  KEY `D022_index_Cnpj` (`D022_Cnpj`),
  KEY `D022_index_Empresas_Estado` (`D022_D018_Id`),
  KEY `D022_index_Empresas_Cidade` (`D022_D020_Id`),
  KEY `D022_index_Empresas_Pais` (`D022_D030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d022`
--

LOCK TABLES `d022` WRITE;
/*!40000 ALTER TABLE `d022` DISABLE KEYS */;
/*!40000 ALTER TABLE `d022` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d023`
--

DROP TABLE IF EXISTS `d023`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d023` (
  `D023_Id` int(10) unsigned NOT NULL auto_increment,
  `D023_Codigo` varchar(6) default NULL,
  `D023_Nome_Empresa` varchar(50) default NULL,
  `D023_Classe` varchar(1) default NULL,
  `D023_Endereco` varchar(40) default NULL,
  `D023_Complemento` varchar(15) default NULL,
  `D023_Cep` varchar(9) default NULL,
  `D023_Bairro` varchar(25) default NULL,
  `D023_Cidade` varchar(30) default NULL,
  `D023_Estado` varchar(2) default NULL,
  `D023_Telefone_1` varchar(20) default NULL,
  `D023_Telefone_2` varchar(20) default NULL,
  `D023_Telex` varchar(20) default NULL,
  `D023_Fax` varchar(20) default NULL,
  `D023_Cnpj` varchar(20) default NULL,
  `D023_Inscricao_Estadual` varchar(20) default NULL,
  `D023_Flag_Tipo_Empresa` varchar(1) default NULL,
  `D023_Flag_Simples` varchar(1) default NULL,
  `D023_Pais` varchar(2) default NULL,
  `D023_Cpf` varchar(20) default NULL,
  `D023_Cobranca_Rua` varchar(40) default NULL,
  `D023_Cobranca_Complemento` varchar(15) default NULL,
  `D023_Cobranca_Bairro` varchar(25) default NULL,
  `D023_Cobranca_Cidade` varchar(30) default NULL,
  `D023_Cobranca_Uf` varchar(2) default NULL,
  `D023_Cobranca_Cep` varchar(9) default NULL,
  `D023_Cobranca_Pais` varchar(2) default NULL,
  `D023_Data_Fundacao` date default NULL,
  `D023_Flag_Suframa` varchar(1) default NULL,
  `D023_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `D023_Flag_Pessoa_Fisica_Juridica` varchar(1) default NULL,
  `D023_Valor_IPV` decimal(10,4) NOT NULL default '0.0000',
  `D023_Percentual_IPV_Fornecedor_1` decimal(8,4) NOT NULL default '0.0000',
  `D023_Percentual_IPV_Fornecedor_2` decimal(8,4) NOT NULL default '0.0000',
  `D023_Percentual_IPV_Fornecedor_3` decimal(8,4) NOT NULL default '0.0000',
  `D023_Percentual_IPV_Consumidor` decimal(8,4) NOT NULL default '0.0000',
  `D023_Data_Irregular` date default NULL,
  `D023_Data_Atraso` date default NULL,
  `D023_Observacao_Compra` text,
  `D023_Flag_Custo_Zero` varchar(1) default NULL,
  `D023_Flag_Mono` varchar(1) default NULL,
  PRIMARY KEY  (`D023_Id`),
  KEY `D023_index_Codigo` (`D023_Codigo`),
  KEY `D023_index_Nome_Empresa` (`D023_Nome_Empresa`),
  KEY `D023_index_Cnpj` (`D023_Cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d023`
--

LOCK TABLES `d023` WRITE;
/*!40000 ALTER TABLE `d023` DISABLE KEYS */;
/*!40000 ALTER TABLE `d023` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d024`
--

DROP TABLE IF EXISTS `d024`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d024` (
  `D024_Id` int(10) unsigned NOT NULL auto_increment,
  `D024_C004_Id` int(11) NOT NULL default '0',
  `D024_D018_Id` int(11) NOT NULL default '0',
  `D024_D020_Id` int(10) unsigned default NULL,
  `D024_D030_Id` int(11) NOT NULL default '0',
  `D024_D006_Id` int(11) NOT NULL default '0',
  `D024_D021_Id` int(11) NOT NULL default '0',
  `D024_D022_Id` int(11) default NULL,
  `D024_D046_Id` int(11) default NULL,
  `D024_D039_Id` int(11) default NULL,
  `D024_D014_Id` int(11) default NULL,
  `D024_D061_Id` varchar(50) default NULL,
  `D024_D062_Id` int(10) unsigned default '0',
  `D024_D024_Id_Eng_Amigo` int(10) unsigned default NULL,
  `D024_C004_Id_Ultima_Compra` int(11) default NULL,
  `D024_C007_Id_Eng_Amigo_Vendedor` int(5) default '0',
  `D024_D018_Cobranca` int(11) NOT NULL default '0',
  `D024_D020_Cobranca` int(11) NOT NULL default '0',
  `D024_D030_Cobranca` int(11) NOT NULL default '0',
  `D024_D018_Entrega` int(11) default NULL,
  `D024_D020_Entrega` int(11) default NULL,
  `D024_D030_Entrega` int(11) default NULL,
  `D024_C007_Vendedor_Externo` int(11) NOT NULL default '0',
  `D024_C007_Vendedor_Interno` int(11) NOT NULL default '0',
  `D024_C007_Id` int(5) default NULL,
  `D024_Codigo` varchar(20) default NULL,
  `D024_Nome_Empresa` varchar(50) default NULL,
  `D024_Nome_Fantasia` varchar(50) default NULL,
  `D024_Classe` varchar(1) default NULL,
  `D024_Endereco` varchar(40) default NULL,
  `D024_Numero` varchar(15) default NULL,
  `D024_Complemento` varchar(15) default NULL,
  `D024_Cep` varchar(9) default NULL,
  `D024_Bairro` varchar(25) default NULL,
  `D024_Cidade` varchar(30) default NULL,
  `D024_Estado` varchar(2) default NULL,
  `D024_Nome_Contato` varchar(40) default NULL,
  `D024_DDD_Telefone_1` varchar(2) default NULL,
  `D024_Telefone_1` varchar(30) default NULL,
  `D024_Ramal_Telefone_1` varchar(10) default NULL,
  `D024_Telefone_2` varchar(30) default NULL,
  `D024_Ramal_Telefone_2` varchar(10) default NULL,
  `D024_Telex` varchar(20) default NULL,
  `D024_Fax` varchar(30) default NULL,
  `D024_Ramal_Fax` varchar(10) default NULL,
  `D024_Celular` varchar(30) default NULL,
  `D024_E_Mail` varchar(60) default NULL,
  `D024_Senha_Acesso` varchar(50) default NULL,
  `D024_Cnpj` varchar(20) default NULL,
  `D024_Inscricao_Estadual` varchar(20) default NULL,
  `D024_Pais` varchar(2) default NULL,
  `D024_Cpf` varchar(20) default NULL,
  `D024_Cobranca_Rua` varchar(40) default NULL,
  `D024_Cobranca_Complemento` varchar(15) default NULL,
  `D024_Cobranca_Bairro` varchar(25) default NULL,
  `D024_Cobranca_Cidade` varchar(30) default NULL,
  `D024_Cobranca_Uf` varchar(2) default NULL,
  `D024_Cobranca_Cep` varchar(9) default NULL,
  `D024_Cobranca_Pais` varchar(2) default NULL,
  `D024_Data_Fundacao` date default NULL,
  `D024_Flag_Tipo_Empresa` varchar(1) default NULL,
  `D024_Flag_Simples` varchar(1) default NULL,
  `D024_Flag_Suframa` enum('S','N','') default '',
  `D024_Numero_Suframa` varchar(35) default NULL,
  `D024_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `D024_Flag_Pessoa_Fisica_Juridica` varchar(1) default NULL,
  `D024_Flag_Isento_ICMS` int(1) NOT NULL default '2',
  `D024_Flag_Isento_ICMS_Venda` enum('S','N') default 'N',
  `D024_Flag_Isento_PIS_COFINS` enum('S','N') default 'N',
  `D024_Flag_Cotacao_Compra` varchar(1) NOT NULL default 'S',
  `D024_Percentual_Reducao_ICMS` decimal(5,2) default NULL,
  `D024_Aliquota_ICMS` decimal(5,2) default NULL,
  `D024_Flag_Isento_IPI` int(1) NOT NULL default '2',
  `D024_Valor_IPV` decimal(10,4) NOT NULL default '0.0000',
  `D024_Percentual_IPV_Fornecedor_1` decimal(8,4) NOT NULL default '0.0000',
  `D024_Percentual_IPV_Fornecedor_2` decimal(8,4) NOT NULL default '0.0000',
  `D024_Percentual_IPV_Fornecedor_3` decimal(8,4) NOT NULL default '0.0000',
  `D024_Percentual_IPV_Consumidor` decimal(8,4) NOT NULL default '0.0000',
  `D024_Data_Irregular` date default '0000-00-00',
  `D024_Flag_Ativo` enum('S','N') default 'S',
  `D024_Data_Atraso` date default NULL,
  `D024_Observacao_Compra` text,
  `D024_Flag_Custo_Zero` varchar(1) default NULL,
  `D024_Flag_Mono` varchar(1) default NULL,
  `D024_ISS` varchar(20) default NULL,
  `D024_Praca_Pagamento` varchar(65) default NULL,
  `D024_Aviso` varchar(65) default NULL,
  `D024_Observacoes` text,
  `D024_Marca` varchar(20) default NULL,
  `D024_Flag_Etiqueta` varchar(1) default NULL,
  `D024_Flag_Mkt` varchar(1) default NULL,
  `D024_Percentual_Comissao` decimal(5,2) default '0.00',
  `D024_Observacao_Comercial` text,
  `D024_Observacao_Isento_ICMS` text,
  `D024_Observacao_Isento_IPI` text,
  `D024_Data_Ultima_Compra` date default NULL,
  `D024_Data_Primeira_Compra` date default NULL,
  `D024_Situacao` varchar(30) default NULL,
  `D024_Flag_Liberado` varchar(1) default NULL,
  `D024_Flag_Classe` varchar(1) default NULL,
  `D024_Cadastro` varchar(16) default NULL,
  `D024_Valor_Limite` decimal(10,2) default NULL,
  `D024_Valor_Saldo_Limite` decimal(10,2) default NULL,
  `D024_Flag_Empresas_Grupo` varchar(1) default 'N',
  `D024_Flag_Permite_ACP` varchar(1) default 'N',
  `D024_Flag_Enviar_Banco_Protestar` int(1) NOT NULL default '1',
  `D024_Flag_Permite_Agrupamentos` int(1) NOT NULL default '1',
  `D024_Referencia_Comercial_1` varchar(60) default NULL,
  `D024_Referencia_Comercial_2` varchar(60) default NULL,
  `D024_Referencia_Comercial_3` varchar(60) default NULL,
  `D024_Referencia_Comercial_4` varchar(60) default NULL,
  `D024_Referencia_Comercial_5` varchar(60) default NULL,
  `D024_Referencia_Bancaria` varchar(80) default NULL,
  `D024_Flag_ICMS_Substituicao_Tributaria_Substituto` varchar(1) default 'N',
  `D024_Eng_Amigo_Empresa_Trabalho` varchar(50) default NULL,
  `D024_Eng_Amigo_Profissao` varchar(50) default NULL,
  `D024_Eng_Amigo_Observacao_Brinde` text,
  `D024_Eng_Amigo_Empresa_Ramo` enum('S','N') default 'N',
  `D024_Eng_Amigo_Relacionado_Produtos_Comercializacao` varchar(1) default 'N',
  `D024_Flag_Eng_Amigo` enum('S','N') default 'N',
  `D024_Codigo_Fornecedor` varchar(15) default NULL,
  `D024_Flag_Atacado_Varejo` varchar(1) default NULL,
  `D024_Flag_Tipo_Origem` int(2) NOT NULL,
  `D024_Flag_Curva_Abc` enum('A','B','C','D') default NULL,
  `D024_Flag_Frequencia` int(6) default '0',
  `D024_Data_Atualizacao` datetime default '0000-00-00 00:00:00',
  `D024_Meses_Inativo` decimal(6,2) default '0.00',
  `D024_Flag_Lista_Preco` varchar(1) default ' ',
  `D024_Data_Cadastro` datetime default '0000-00-00 00:00:00',
  `D024_Flag_Pre_Cadastro` enum('S','N') default 'N',
  `D024_Flag_Cliente` varchar(1) default ' ',
  `D024_Flag_Fornecedor` varchar(1) default ' ',
  `D024_Horario_Entrega` varchar(30) default ' ',
  `D024_Dias_Carencia_Pagamento` int(2) default '0',
  `D024_Dia_Semana_Pagamento` int(1) default '0',
  `D024_Dia_Mes_Pagamento` varchar(30) default ' ',
  `D024_Web_Site` varchar(50) default ' ',
  `D024_Condicoes_Pagamento` varchar(50) default ' ',
  `D024_Entrega_Rua` varchar(60) default NULL,
  `D024_Entrega_Complemento` varchar(30) default NULL,
  `D024_Entrega_Bairro` varchar(25) default NULL,
  `D024_Entrega_Cep` varchar(9) default NULL,
  `D024_Entrega_Telefone` varchar(20) default NULL,
  `D024_Cobranca_Telefone` varchar(20) default NULL,
  `D024_Cobranca_Fax` varchar(20) default NULL,
  `D024_Entrega_Fax` varchar(20) default NULL,
  `D024_Cobranca_Email` varchar(50) default NULL,
  `D024_Entrega_Email` varchar(50) default NULL,
  `D024_Tipo_Pagamento` varchar(3) default ' ',
  `D024_Percentual_Desconto_Tabela` decimal(10,4) default '0.0000',
  `D024_Flag_Restricao` enum('S','N') default 'N',
  `D024_Observacao_Financeiro` text,
  `D024_Flag_Comercial` varchar(1) default '',
  `D024_Flag_Inativo_Comercial` varchar(1) default 'N',
  `D024_Flag_Tempo` varchar(2) default '  ',
  PRIMARY KEY  (`D024_Id`),
  KEY `D024_index_Id` (`D024_Id`),
  KEY `D024_index_Codigo` (`D024_Codigo`),
  KEY `D024_index_Nome_Empresa` (`D024_Nome_Empresa`),
  KEY `D024_index_Cnpj` (`D024_Cnpj`),
  KEY `D024_index_Vendedor_Externo` (`D024_C007_Vendedor_Externo`),
  KEY `D024_index_Vendedor_Interno` (`D024_C007_Vendedor_Interno`),
  KEY `D024_index_Grupo` (`D024_Flag_Mono`),
  KEY `D024_index_Estado` (`D024_D018_Id`),
  KEY `D024_index_Cidade` (`D024_D020_Id`),
  KEY `D024_index_Pais` (`D024_D030_Id`),
  KEY `D024_index_Estado_Cobranca` (`D024_D018_Cobranca`),
  KEY `D024_index_Cidade_Cobranca` (`D024_D020_Cobranca`),
  KEY `D024_index_Pais_Cobranca` (`D024_D030_Cobranca`),
  KEY `D024_index_Natureza` (`D024_D006_Id`),
  KEY `D024_index_Representante` (`D024_D021_Id`),
  KEY `D024_index_Empresas` (`D024_C004_Id`),
  KEY `D024_index_D062_Id` (`D024_D062_Id`),
  KEY `D024_index_C004_Id_Ultima_Compra` (`D024_C004_Id_Ultima_Compra`),
  KEY `IDX_D024_Flag_Curva_Abc` (`D024_Flag_Curva_Abc`),
  KEY `IDX_D024_Flag_Comercial` (`D024_Flag_Comercial`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d024`
--

LOCK TABLES `d024` WRITE;
/*!40000 ALTER TABLE `d024` DISABLE KEYS */;
/*!40000 ALTER TABLE `d024` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d025`
--

DROP TABLE IF EXISTS `d025`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d025` (
  `D025_Id` int(10) unsigned NOT NULL auto_increment,
  `D025_D018_Id` int(10) unsigned NOT NULL,
  `D025_Codigo_Despesa` varchar(5) NOT NULL,
  `D025_Descricao_Despesa` varchar(40) NOT NULL,
  `D025_Formula` text NOT NULL,
  `D025_Flag_Tipo_Frete` varchar(1) NOT NULL,
  `D025_Flag_IPI` varchar(1) NOT NULL default 'N',
  `D025_Flag_Taxa_Importacao` varchar(1) NOT NULL default 'N',
  `D025_Flag_ICMS` varchar(1) NOT NULL default 'N',
  `D025_Flag_Peso` varchar(1) NOT NULL default 'N',
  `D025_Flag_Dolar` varchar(1) NOT NULL default 'N',
  `D025_Flag_Regime_Tributacao_Simplificado` varchar(1) NOT NULL default 'N',
  `D025_Flag_Aduaneira` varchar(1) NOT NULL default 'N',
  `D025_Valor_Faixa_1` decimal(10,4) NOT NULL default '0.0000',
  `D025_Valor_Faixa_2` decimal(10,4) NOT NULL default '0.0000',
  `D025_Valor_Faixa_3` decimal(10,4) NOT NULL default '0.0000',
  `D025_Valor_KG_Faixa_1` decimal(10,4) NOT NULL default '0.0000',
  `D025_Valor_KG_Faixa_2` decimal(10,4) NOT NULL default '0.0000',
  `D025_Valor_KG_Faixa_3` decimal(10,4) NOT NULL default '0.0000',
  `D025_Estado` varchar(2) NOT NULL,
  PRIMARY KEY  (`D025_Id`),
  KEY `D025_IDX_ESTADO` (`D025_D018_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d025`
--

LOCK TABLES `d025` WRITE;
/*!40000 ALTER TABLE `d025` DISABLE KEYS */;
/*!40000 ALTER TABLE `d025` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d026`
--

DROP TABLE IF EXISTS `d026`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d026` (
  `D026_Id` int(10) unsigned NOT NULL auto_increment,
  `D026_Data` date NOT NULL,
  `D026_Valor_Engepecas` decimal(12,4) NOT NULL default '0.0000',
  `D026_Valor_Oficial` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`D026_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d026`
--

LOCK TABLES `d026` WRITE;
/*!40000 ALTER TABLE `d026` DISABLE KEYS */;
/*!40000 ALTER TABLE `d026` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d027`
--

DROP TABLE IF EXISTS `d027`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d027` (
  `D027_Id` int(10) unsigned NOT NULL auto_increment,
  `D027_D071_Id` int(11) default NULL,
  `D027_Portador` varchar(20) NOT NULL,
  `D027_Flag_Prazo` varchar(1) default NULL,
  `D027_Flag_Boleto` varchar(1) default NULL,
  `D027_Flag_Cupom` varchar(1) default NULL,
  PRIMARY KEY  (`D027_Id`),
  KEY `D027_Index_Portador` (`D027_Portador`),
  KEY `D027_Index_Id` (`D027_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d027`
--

LOCK TABLES `d027` WRITE;
/*!40000 ALTER TABLE `d027` DISABLE KEYS */;
/*!40000 ALTER TABLE `d027` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d028`
--

DROP TABLE IF EXISTS `d028`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d028` (
  `D028_Id` int(10) unsigned NOT NULL auto_increment,
  `D028_Codigo_Grupo` int(2) NOT NULL default '0',
  `D028_Descricao_Grupo` varchar(30) NOT NULL,
  `D028_Percentual_Taxa` decimal(10,2) NOT NULL default '0.00',
  `D028_Tipo` varchar(2) default NULL,
  PRIMARY KEY  (`D028_Id`),
  KEY `D028_IDX_CODIGO_GRUPO` (`D028_Codigo_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d028`
--

LOCK TABLES `d028` WRITE;
/*!40000 ALTER TABLE `d028` DISABLE KEYS */;
/*!40000 ALTER TABLE `d028` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d029`
--

DROP TABLE IF EXISTS `d029`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d029` (
  `D029_Id` int(10) unsigned NOT NULL auto_increment,
  `D029_D014_Id` int(10) unsigned NOT NULL,
  `D029_D028_Id` int(10) unsigned NOT NULL,
  `D029_Descricao` varchar(30) NOT NULL,
  `D029_Percentual_Taxa` decimal(10,2) NOT NULL default '0.00',
  `D029_Aplicacao` int(1) NOT NULL default '0',
  `D029_Sobre` int(2) NOT NULL default '0',
  `D029_Conta` int(2) NOT NULL default '0',
  `D029_SubConta` varchar(30) NOT NULL,
  `D029_Taxa_Real` decimal(10,2) NOT NULL default '0.00',
  `D029_Codigo_Grupo` int(2) NOT NULL default '0',
  PRIMARY KEY  (`D029_Id`),
  KEY `D029_IDX_GRUPOS` (`D029_D028_Id`),
  KEY `D029_IDX_PLANOS` (`D029_D014_Id`),
  KEY `D029_IDX_CODIGO_GRUPO` (`D029_Codigo_Grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d029`
--

LOCK TABLES `d029` WRITE;
/*!40000 ALTER TABLE `d029` DISABLE KEYS */;
/*!40000 ALTER TABLE `d029` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d030`
--

DROP TABLE IF EXISTS `d030`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d030` (
  `D030_Id` int(10) unsigned NOT NULL auto_increment,
  `D030_Sigla_Pais` varchar(2) default NULL,
  `D030_Nome_Pais` varchar(40) default NULL,
  PRIMARY KEY  (`D030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d030`
--

LOCK TABLES `d030` WRITE;
/*!40000 ALTER TABLE `d030` DISABLE KEYS */;
/*!40000 ALTER TABLE `d030` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d031`
--

DROP TABLE IF EXISTS `d031`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d031` (
  `D031_Id` int(10) unsigned NOT NULL auto_increment,
  `D031_Portador` varchar(20) NOT NULL,
  PRIMARY KEY  (`D031_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d031`
--

LOCK TABLES `d031` WRITE;
/*!40000 ALTER TABLE `d031` DISABLE KEYS */;
/*!40000 ALTER TABLE `d031` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d032`
--

DROP TABLE IF EXISTS `d032`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d032` (
  `D032_Id` int(10) unsigned NOT NULL auto_increment,
  `D032_Tipo` varchar(2) NOT NULL,
  `D032_Descricao` varchar(20) NOT NULL,
  PRIMARY KEY  (`D032_Id`),
  KEY `D032_Indice_Tipos` (`D032_Tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d032`
--

LOCK TABLES `d032` WRITE;
/*!40000 ALTER TABLE `d032` DISABLE KEYS */;
/*!40000 ALTER TABLE `d032` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d033`
--

DROP TABLE IF EXISTS `d033`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d033` (
  `D033_Id` int(10) unsigned NOT NULL auto_increment,
  `D033_C004_Id` int(10) unsigned NOT NULL default '0',
  `D033_Nome_Linha` varchar(11) default NULL,
  `D033_Flag_Tipo` varchar(2) default NULL,
  PRIMARY KEY  (`D033_Id`),
  KEY `D033_FKIndex1` (`D033_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d033`
--

LOCK TABLES `d033` WRITE;
/*!40000 ALTER TABLE `d033` DISABLE KEYS */;
/*!40000 ALTER TABLE `d033` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d034`
--

DROP TABLE IF EXISTS `d034`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d034` (
  `D034_Id` int(10) unsigned NOT NULL auto_increment,
  `D034_Codigo` varchar(5) default NULL,
  `D034_Descricao_Produto` varchar(50) default NULL,
  PRIMARY KEY  (`D034_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d034`
--

LOCK TABLES `d034` WRITE;
/*!40000 ALTER TABLE `d034` DISABLE KEYS */;
/*!40000 ALTER TABLE `d034` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d035`
--

DROP TABLE IF EXISTS `d035`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d035` (
  `D035_Id` int(10) unsigned NOT NULL auto_increment,
  `D035_D024_Id` int(10) unsigned default NULL,
  `D035_D033_Id` int(10) unsigned default NULL,
  `D035_D034_Id` int(10) unsigned default NULL,
  `D035_C004_Id` int(10) unsigned NOT NULL default '0',
  `D035_Codigo_Produto` varchar(30) default NULL,
  `D035_Descricao_Produto` varchar(60) default NULL,
  `D035_Data_Ultimo_Movimento` date default NULL,
  `D035_Peso_Unitario_Kg` decimal(10,3) default NULL,
  `D035_Data_Cadastro` date default NULL,
  `D035_Flag_Ativo` int(1) default NULL,
  `D035_Codigo_Descricao` int(5) default NULL,
  `D035_Codigo_Linha` varchar(11) default NULL,
  `D035_Valor_Custo_Unitario` decimal(10,2) default NULL,
  `D035_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `D035_Quantidade_Estoque` int(5) default NULL,
  `D035_Flag_Nacional_Importado` varchar(1) default NULL,
  `D035_Flag_Unidade_Estoque` varchar(1) default NULL,
  `D035_Flag_Unidade_Conversao` varchar(1) default NULL,
  `D035_Ordem_Frequencia` int(2) default NULL,
  `D035_Codigo_Fornecedor` varchar(6) default NULL,
  PRIMARY KEY  (`D035_Id`),
  KEY `D035_FKIndex0` (`D035_D024_Id`),
  KEY `D035_FKIndex1` (`D035_D034_Id`),
  KEY `D035_FKIndex2` (`D035_D033_Id`),
  KEY `D035_FKIndex3` (`D035_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d035`
--

LOCK TABLES `d035` WRITE;
/*!40000 ALTER TABLE `d035` DISABLE KEYS */;
/*!40000 ALTER TABLE `d035` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d036`
--

DROP TABLE IF EXISTS `d036`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d036` (
  `D036_Id` int(10) unsigned NOT NULL auto_increment,
  `D036_Tipo_Transporte` varchar(20) NOT NULL,
  PRIMARY KEY  (`D036_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d036`
--

LOCK TABLES `d036` WRITE;
/*!40000 ALTER TABLE `d036` DISABLE KEYS */;
/*!40000 ALTER TABLE `d036` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d037`
--

DROP TABLE IF EXISTS `d037`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d037` (
  `D037_Id` int(10) unsigned NOT NULL auto_increment,
  `D037_Unidade` varchar(5) default NULL,
  `D037_Descricao_Unidade` varchar(20) default NULL,
  `D037_Quantidade` decimal(10,3) default '1.000',
  PRIMARY KEY  (`D037_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d037`
--

LOCK TABLES `d037` WRITE;
/*!40000 ALTER TABLE `d037` DISABLE KEYS */;
INSERT INTO `d037` VALUES (1,'UN','UNIDADE','1.000');
/*!40000 ALTER TABLE `d037` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d038`
--

DROP TABLE IF EXISTS `d038`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d038` (
  `D038_Id` int(10) unsigned NOT NULL auto_increment,
  `D038_D024_Id` int(10) unsigned default NULL,
  `D038_D036_Id` int(10) unsigned default NULL,
  `D038_D022_Id` int(10) unsigned default NULL,
  `D038_D044_Id` int(10) unsigned default NULL,
  `D038_Valor_Frete` decimal(10,2) default '0.00',
  `D038_Prazo_Entrega` int(10) default '0',
  `D038_Prazo_Pagamento_1` int(10) default '0',
  `D038_Prazo_Pagamento_2` int(10) default '0',
  `D038_Prazo_Pagamento_3` int(10) default '0',
  `D038_Prazo_Pagamento_4` int(10) default '0',
  `D038_Prazo_Pagamento_5` int(10) default '0',
  `D038_Prazo_Pagamento_6` int(10) default '0',
  `D038_Prazo_Pagamento_7` int(10) default '0',
  `D038_Prazo_Pagamento_8` int(10) default '0',
  `D038_Prazo_Pagamento_9` int(10) default '0',
  `D038_Prazo_Pagamento_10` int(10) default '0',
  `D038_Observacao` text,
  PRIMARY KEY  (`D038_Id`),
  KEY `D038_FKIndex1` (`D038_D024_Id`),
  KEY `D038_FKIndex2` (`D038_D022_Id`),
  KEY `D038_FKIndex3` (`D038_D036_Id`),
  KEY `D038_index_Moeda` (`D038_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d038`
--

LOCK TABLES `d038` WRITE;
/*!40000 ALTER TABLE `d038` DISABLE KEYS */;
/*!40000 ALTER TABLE `d038` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d039`
--

DROP TABLE IF EXISTS `d039`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d039` (
  `D039_Id` int(10) unsigned NOT NULL auto_increment,
  `D039_C007_Id` int(10) unsigned default NULL,
  `D039_D046_Id` int(10) unsigned default NULL,
  `D039_Codigo_Classe` varchar(5) default NULL,
  `D039_Data_Cadastro` datetime default NULL,
  `D039_Descricao` varchar(50) default NULL,
  `D039_Flag_Visitar` varchar(1) default NULL,
  `D039_Flag_Maquina` varchar(1) default NULL,
  `D039_Flag_Revenda` varchar(1) default NULL,
  PRIMARY KEY  (`D039_Id`),
  KEY `T016_FKIndex1` (`D039_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d039`
--

LOCK TABLES `d039` WRITE;
/*!40000 ALTER TABLE `d039` DISABLE KEYS */;
/*!40000 ALTER TABLE `d039` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d040`
--

DROP TABLE IF EXISTS `d040`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d040` (
  `D040_Id` int(10) unsigned NOT NULL auto_increment,
  `D040_Nome_Maquina` varchar(30) default NULL,
  `D040_Descricao` text,
  `D040_Numero_Serie` varchar(20) default NULL,
  `D040_Ano_Maquina` year(4) default NULL,
  `D040_Flag_Disponivel` varchar(1) default 'S',
  `D040_Situacao` varchar(12) default NULL,
  `D040_Valor_Preco_Venda` decimal(12,2) default '0.00',
  `D040_Foto1` varchar(100) default NULL,
  `D040_Foto2` varchar(100) default NULL,
  `D040_Foto3` varchar(100) default NULL,
  `D040_Foto4` varchar(100) default NULL,
  PRIMARY KEY  (`D040_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d040`
--

LOCK TABLES `d040` WRITE;
/*!40000 ALTER TABLE `d040` DISABLE KEYS */;
/*!40000 ALTER TABLE `d040` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d041`
--

DROP TABLE IF EXISTS `d041`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d041` (
  `D041_Id` int(10) unsigned NOT NULL auto_increment,
  `D041_D049_Id` int(10) unsigned default NULL,
  `D041_Valor_Base` decimal(15,5) default NULL,
  `D041_Valor_Preco_Consumidor` decimal(15,5) default NULL,
  `D041_Flag_Tipo` int(1) default NULL,
  `D041_Codigo_Produto` varchar(30) default NULL,
  `D041_Codigo_Fornecedor` varchar(6) default NULL,
  `D041_Flag_Consumidor` varchar(1) default NULL,
  `D041_Flag_Tem_Consumidor` varchar(1) default NULL,
  `D041_Flag_Excluir` varchar(1) default 'N',
  `D041_Valor_Custo` decimal(10,2) NOT NULL,
  `D041_Valor_Preco_Revenda` decimal(15,5) default NULL,
  `D041_D024_Id` int(10) default NULL,
  PRIMARY KEY  (`D041_Id`),
  KEY `D041_FKIndex1` (`D041_D049_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d041`
--

LOCK TABLES `d041` WRITE;
/*!40000 ALTER TABLE `d041` DISABLE KEYS */;
/*!40000 ALTER TABLE `d041` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d042`
--

DROP TABLE IF EXISTS `d042`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d042` (
  `D042_Id` int(10) unsigned NOT NULL auto_increment,
  `D042_D049_Id` int(10) unsigned default NULL,
  `D042_C004_Id` int(10) unsigned default NULL,
  `D042_D049_Id_Itens` int(10) unsigned default NULL,
  `D042_Quantidade` decimal(10,3) default '0.000',
  `D042_Codigo_Produto_Principal` varchar(30) default NULL,
  `D042_Codigo_Produto_Itens` varchar(30) default NULL,
  `D042_Codigo_Fornecedor_Itens` varchar(30) default NULL,
  `D042_Codigo_Fornecedor_Principal` varchar(30) default NULL,
  `D042_Observacao` text,
  PRIMARY KEY  (`D042_Id`),
  KEY `D042_FKIndex1` (`D042_D049_Id`),
  KEY `D042_FKIndex2` (`D042_C004_Id`),
  KEY `D042_FKIndex3` (`D042_D049_Id_Itens`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d042`
--

LOCK TABLES `d042` WRITE;
/*!40000 ALTER TABLE `d042` DISABLE KEYS */;
/*!40000 ALTER TABLE `d042` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d043`
--

DROP TABLE IF EXISTS `d043`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d043` (
  `D043_Id` int(10) unsigned NOT NULL auto_increment,
  `D043_D001_Id` int(10) unsigned default NULL,
  `D043_Codigo_Produto` varchar(30) default NULL,
  `D043_Descricao_Produto` varchar(40) default NULL,
  `D043_Valor_Preco_Consumidor` varchar(12) default NULL,
  `D043_Codigo_Linha` decimal(5,2) default NULL,
  `D043_Aplicacao` varchar(10) default NULL,
  `D043_Aliquota_IPI` decimal(5,2) default NULL,
  `D043_Peso_Liquido` decimal(10,5) default NULL,
  PRIMARY KEY  (`D043_Id`),
  KEY `D043_FKIndex1` (`D043_D001_Id`),
  KEY `D043_FKIndex2` (`D043_Codigo_Produto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d043`
--

LOCK TABLES `d043` WRITE;
/*!40000 ALTER TABLE `d043` DISABLE KEYS */;
/*!40000 ALTER TABLE `d043` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d044`
--

DROP TABLE IF EXISTS `d044`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d044` (
  `D044_Id` int(10) unsigned NOT NULL auto_increment,
  `D044_Moeda` varchar(20) default NULL,
  `D044_Simbolo` varchar(10) default NULL,
  PRIMARY KEY  (`D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d044`
--

LOCK TABLES `d044` WRITE;
/*!40000 ALTER TABLE `d044` DISABLE KEYS */;
/*!40000 ALTER TABLE `d044` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d045`
--

DROP TABLE IF EXISTS `d045`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d045` (
  `D045_Id` int(10) unsigned NOT NULL auto_increment,
  `D045_D044_Id` int(10) default NULL,
  `D045_D014_Id` int(10) default NULL,
  `D045_Descricao` varchar(30) default NULL,
  `D045_Formula` varchar(50) default NULL,
  `D045_Observacao_Formula` text,
  `D045_Flag_Selecionado` varchar(1) default 'N',
  `D045_Flag_Frete` varchar(1) default 'N',
  `D045_Flag_Calcular_Aduaneira` varchar(1) default 'N',
  `D045_Flag_Calcular_ICMS` varchar(1) default 'N',
  `D045_Flag_Calcular_PIS_COFINS` varchar(1) default 'N',
  `D045_Flag_Calcular_IPI` varchar(1) default 'N',
  `D045_Flag_Calcular_Imposto_Importacao` varchar(1) default 'N',
  `D045_Flag_Tipo` varchar(15) default NULL,
  PRIMARY KEY  (`D045_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d045`
--

LOCK TABLES `d045` WRITE;
/*!40000 ALTER TABLE `d045` DISABLE KEYS */;
/*!40000 ALTER TABLE `d045` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d046`
--

DROP TABLE IF EXISTS `d046`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d046` (
  `D046_Id` int(10) unsigned NOT NULL auto_increment,
  `D046_Nome` varchar(30) default NULL,
  PRIMARY KEY  (`D046_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d046`
--

LOCK TABLES `d046` WRITE;
/*!40000 ALTER TABLE `d046` DISABLE KEYS */;
/*!40000 ALTER TABLE `d046` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d047`
--

DROP TABLE IF EXISTS `d047`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d047` (
  `D047_Id` int(10) NOT NULL auto_increment,
  `D047_Descricao_Motivo` varchar(50) default NULL,
  PRIMARY KEY  (`D047_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d047`
--

LOCK TABLES `d047` WRITE;
/*!40000 ALTER TABLE `d047` DISABLE KEYS */;
/*!40000 ALTER TABLE `d047` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d048`
--

DROP TABLE IF EXISTS `d048`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d048` (
  `D048_Id` int(10) NOT NULL auto_increment,
  `D048_Estoque` varchar(30) NOT NULL,
  PRIMARY KEY  (`D048_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d048`
--

LOCK TABLES `d048` WRITE;
/*!40000 ALTER TABLE `d048` DISABLE KEYS */;
/*!40000 ALTER TABLE `d048` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d049`
--

DROP TABLE IF EXISTS `d049`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d049` (
  `D049_Id` int(10) unsigned NOT NULL auto_increment,
  `D049_D082_Id` int(11) unsigned default NULL,
  `D049_D024_Id` int(10) unsigned default NULL,
  `D049_D001_Id` int(10) unsigned default NULL,
  `D049_Codigo_Produto` varchar(30) default NULL,
  `D049_Codigo_Barras` varchar(25) default NULL,
  `D049_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `D049_Descricao_Produto` varchar(60) default NULL,
  `D049_Peso_Unitario_Kg` decimal(10,3) NOT NULL default '0.000',
  `D049_Data_Cadastro` date NOT NULL default '0000-00-00',
  `D049_Aplicacao` varchar(60) default NULL,
  `D049_Flag_Ativo` varchar(1) default NULL,
  `D049_Flag_Nacional_Importado` varchar(1) default NULL,
  `D049_Flag_Isento_ICMS` int(1) default '2',
  `D049_Percentual_Reducao_ICMS` decimal(6,2) NOT NULL default '0.00',
  `D049_Aliquota_ICMS` decimal(6,2) NOT NULL default '0.00',
  `D049_Flag_Isento_IPI` int(1) default '2',
  `D049_Percentual_Reducao_IPI` decimal(6,2) NOT NULL default '0.00',
  `D049_Aliquota_IPI` decimal(6,2) NOT NULL default '0.00',
  `D049_Codigo_Fornecedor` varchar(6) default NULL,
  `D049_Codigo_Descricao` int(5) default NULL,
  `D049_Codigo_Linha` varchar(11) default NULL,
  `D049_Codigo_Local` varchar(10) default NULL,
  `D049_Prazo_Entrega` int(10) default '0',
  `D049_Marca` varchar(20) default NULL,
  `D049_Data_Ultima_Venda` date NOT NULL default '0000-00-00',
  `D049_Quantidade_Estoque_Parado_1_Ano` int(10) default '0',
  `D049_Quantidade_Estoque_Parado_2_Ano` int(10) default '0',
  `D049_Flag_Substituicao_Tributaria` varchar(1) default 'N',
  `D049_Flag_Nao_Comprar` varchar(1) default 'N',
  `D049_Observacao_Compra` text,
  `D049_Quantidade_Minima_Compra` decimal(10,2) default NULL,
  `D049_Flag_Tipo` varchar(2) default NULL,
  PRIMARY KEY  (`D049_Id`),
  KEY `D049_Index_Codigo_Produto_Id` (`D049_Codigo_Produto`),
  KEY `D049_Index_Codigo_Fornecedor` (`D049_Codigo_Fornecedor`),
  KEY `D049_Index_Produtos` (`D049_D001_Id`),
  KEY `D049_Index_Fornecedores` (`D049_D024_Id`),
  KEY `IDX_Codigo_Produto_Fornecedor` (`D049_Codigo_Produto_Fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d049`
--

LOCK TABLES `d049` WRITE;
/*!40000 ALTER TABLE `d049` DISABLE KEYS */;
/*!40000 ALTER TABLE `d049` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d050`
--

DROP TABLE IF EXISTS `d050`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d050` (
  `D050_Id` int(10) NOT NULL auto_increment,
  `D050_D024_Id` int(10) default NULL,
  `D050_D051_Id` int(10) default NULL,
  `D050_Numero_Serie` varchar(30) default NULL,
  `D050_Ano_Fabricacao` varchar(4) default NULL,
  `D050_CPL` varchar(8) default NULL,
  `D050_Descricao` text,
  `D050_Codigo_Cliente` varchar(6) default NULL,
  `D050_Codigo_Maquina` int(5) default NULL,
  PRIMARY KEY  (`D050_Id`),
  KEY `D050_Index_D024_Id` (`D050_D024_Id`),
  KEY `D050_Index_D051_Id` (`D050_D051_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d050`
--

LOCK TABLES `d050` WRITE;
/*!40000 ALTER TABLE `d050` DISABLE KEYS */;
/*!40000 ALTER TABLE `d050` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d051`
--

DROP TABLE IF EXISTS `d051`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d051` (
  `D051_Id` int(10) NOT NULL auto_increment,
  `D051_Nome` varchar(30) default NULL,
  `D051_Modelo` varchar(20) default NULL,
  `D051_Marca` varchar(20) default NULL,
  `D051_Codigo` int(5) default NULL,
  PRIMARY KEY  (`D051_Id`),
  KEY `D051_Index_Codigo` (`D051_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d051`
--

LOCK TABLES `d051` WRITE;
/*!40000 ALTER TABLE `d051` DISABLE KEYS */;
/*!40000 ALTER TABLE `d051` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d052`
--

DROP TABLE IF EXISTS `d052`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d052` (
  `D052_Id` int(5) NOT NULL auto_increment,
  `D052_Login` varchar(20) default NULL,
  `D052_Email` text,
  `D052_Nome` varchar(30) default NULL,
  `D052_Primeiro_Nome` varchar(20) default NULL,
  `D052_Data_Alteracao_Senha` date default NULL,
  `D052_Codigo_Sistema_Antigo` int(11) default NULL,
  PRIMARY KEY  (`D052_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d052`
--

LOCK TABLES `d052` WRITE;
/*!40000 ALTER TABLE `d052` DISABLE KEYS */;
/*!40000 ALTER TABLE `d052` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d053`
--

DROP TABLE IF EXISTS `d053`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d053` (
  `D053_Id` int(10) unsigned NOT NULL auto_increment,
  `D053_D018_Id` int(10) NOT NULL,
  `D053_C004_Id` int(10) NOT NULL,
  `D053_UF` varchar(2) NOT NULL,
  `D053_Percentual_Reducao_ICMS_1` decimal(5,2) NOT NULL default '0.00',
  `D053_Aliquota_ICMS_1` decimal(5,2) NOT NULL default '0.00',
  `D053_Percentual_Reducao_ICMS_2` decimal(5,2) NOT NULL default '0.00',
  `D053_Aliquota_ICMS_2` decimal(5,2) NOT NULL default '0.00',
  `D053_Percentual_Reducao_ICMS_Revenda` decimal(5,2) NOT NULL default '0.00',
  `D053_Aliquota_ICMS_Revenda` decimal(5,2) NOT NULL default '0.00',
  `D053_Percentual_Reducao_ICMS_Consumidor` decimal(5,2) NOT NULL default '0.00',
  `D053_Aliquota_ICMS_Consumidor` decimal(5,2) NOT NULL default '0.00',
  `D053_Pais` varchar(2) NOT NULL,
  `D053_Maquinas` int(10) NOT NULL default '0',
  `D053_Clientes` int(10) NOT NULL default '0',
  `D053_Atendidos` int(10) NOT NULL default '0',
  `D053_Cidades` int(10) NOT NULL default '0',
  `D053_Motores` int(10) NOT NULL default '0',
  `D053_Com_Maquina` int(10) NOT NULL default '0',
  `D053_Com_Compra` int(10) NOT NULL default '0',
  `D053_Estado` varchar(40) NOT NULL,
  `D053_Observacao` text NOT NULL,
  `D053_Flag_ST` varchar(1) default 'N',
  `D053_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `D053_ST_VA` decimal(10,2) default '0.00',
  `D053_ST_VA_2` decimal(10,2) default '0.00',
  PRIMARY KEY  (`D053_Id`),
  KEY `D053_IDX_UF` (`D053_UF`),
  KEY `D053_Index_D018_Id` (`D053_D018_Id`),
  KEY `D053_Index_C004_Id` (`D053_C004_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d053`
--

LOCK TABLES `d053` WRITE;
/*!40000 ALTER TABLE `d053` DISABLE KEYS */;
INSERT INTO `d053` VALUES (1,1,0,'RO','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(2,2,0,'AC','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(3,3,0,'AM','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(4,4,0,'RR','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(5,5,0,'PA','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(6,6,0,'AP','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(7,7,0,'TO','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(8,8,0,'MA','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(9,9,0,'PI','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(10,10,0,'CE','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(11,11,0,'RN','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(12,12,0,'PB','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(13,13,0,'PE','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(14,14,0,'AL','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(15,15,0,'SE','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(16,16,0,'BA','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(17,17,0,'MG','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(18,18,0,'ES','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(19,19,0,'RJ','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(20,20,0,'SP','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(21,21,0,'PR','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(22,22,0,'SC','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(23,23,0,'RS','0.00','18.00','0.00','12.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(24,24,0,'MS','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(25,25,0,'MT','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(26,26,0,'GO','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(27,27,0,'DF','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00'),(28,28,0,'EX','0.00','18.00','0.00','7.00','0.00','0.00','0.00','0.00','',0,0,0,0,0,0,0,'','','N','0.00','0.00','0.00');
/*!40000 ALTER TABLE `d053` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d054`
--

DROP TABLE IF EXISTS `d054`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d054` (
  `D054_Id` int(10) unsigned NOT NULL auto_increment,
  `D054_Codigo_Kit` varchar(18) default NULL,
  `D054_Descricao` varchar(50) default NULL,
  PRIMARY KEY  (`D054_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d054`
--

LOCK TABLES `d054` WRITE;
/*!40000 ALTER TABLE `d054` DISABLE KEYS */;
/*!40000 ALTER TABLE `d054` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d055`
--

DROP TABLE IF EXISTS `d055`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d055` (
  `D055_Id` int(10) unsigned NOT NULL auto_increment,
  `D055_D054_Id` int(10) unsigned default NULL,
  `D055_D001_Id` int(10) unsigned default NULL,
  `D055_Codigo_Produto` varchar(18) default NULL,
  `D055_Quantidade` int(6) NOT NULL default '0',
  PRIMARY KEY  (`D055_Id`),
  KEY `D055_index_Kit` (`D055_D054_Id`),
  KEY `D055_index_Produto` (`D055_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d055`
--

LOCK TABLES `d055` WRITE;
/*!40000 ALTER TABLE `d055` DISABLE KEYS */;
/*!40000 ALTER TABLE `d055` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d056`
--

DROP TABLE IF EXISTS `d056`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d056` (
  `D056_Id` int(10) NOT NULL auto_increment,
  `D056_Codigo` varchar(25) default NULL,
  `D056_Nome_Conta` varchar(40) default NULL,
  `D056_Flag_Tipo` varchar(1) default NULL,
  PRIMARY KEY  (`D056_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d056`
--

LOCK TABLES `d056` WRITE;
/*!40000 ALTER TABLE `d056` DISABLE KEYS */;
/*!40000 ALTER TABLE `d056` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d057`
--

DROP TABLE IF EXISTS `d057`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d057` (
  `D057_Id` int(10) unsigned NOT NULL auto_increment,
  `D057_Descricao` varchar(30) default NULL,
  `D057_Flag_Selecionado` varchar(1) default NULL,
  PRIMARY KEY  (`D057_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d057`
--

LOCK TABLES `d057` WRITE;
/*!40000 ALTER TABLE `d057` DISABLE KEYS */;
/*!40000 ALTER TABLE `d057` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d058`
--

DROP TABLE IF EXISTS `d058`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d058` (
  `D058_Id` int(11) NOT NULL auto_increment,
  `D058_Tipo_Natureza_Operacao` varchar(40) default NULL,
  `D058_Observacao` text,
  PRIMARY KEY  (`D058_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d058`
--

LOCK TABLES `d058` WRITE;
/*!40000 ALTER TABLE `d058` DISABLE KEYS */;
/*!40000 ALTER TABLE `d058` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d059`
--

DROP TABLE IF EXISTS `d059`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d059` (
  `D059_Id` int(11) NOT NULL auto_increment,
  `D059_Codigo_ST` varchar(3) default NULL,
  `D059_Descricao_Origem` varchar(100) default NULL,
  `D059_Flag_Substituicao_Tributaria` varchar(1) default 'N',
  PRIMARY KEY  (`D059_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d059`
--

LOCK TABLES `d059` WRITE;
/*!40000 ALTER TABLE `d059` DISABLE KEYS */;
INSERT INTO `d059` VALUES (1,'00','TRIBUTADA INTEGRALMENTE','N'),(2,'10','TRIBUTADA E COM COBRANÇA DE ICMS POR SUBSTITUIÇÃO TRIBUTÁRIA','N'),(3,'20','COM REDUÇÃO DE BASE DE CALCULO','N'),(4,'30','ISENTA OU NÃO TRIBUTADA E COM COBRANÇA DO ICMS POR SUBSTITUIÇÃO TRIBUTÁRIA','N'),(5,'40','ISENTA','N'),(6,'41','NÃO TRIBUTADA','N'),(7,'50','SUSPENSÃO','N'),(8,'51','DIFERIMENTO','N'),(9,'60','ICMS COBRADO ANTERIORMENTE POR SUBSTITUIÇÃO TRIBUTÁRIA','N'),(10,'70','COM REDUÇÃO DE BASE DE CÁLCULO E COBRANÇA DO ICMS POR SUBSTIUIÇÃO TRIBUTÁRIA','N'),(11,'90','OUTRAS','N');
/*!40000 ALTER TABLE `d059` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d060`
--

DROP TABLE IF EXISTS `d060`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d060` (
  `D060_Id` int(3) unsigned NOT NULL auto_increment,
  `D060_C008_Id` int(10) unsigned default NULL,
  `D060_D003_Id` int(10) unsigned default NULL,
  `D060_Codigo` int(5) default NULL,
  `D060_Descricao_Grupo` varchar(50) default NULL,
  `D060_Codigo_Padrao` varchar(10) default '',
  `D060_Descricao_Padrao` varchar(30) default '',
  PRIMARY KEY  (`D060_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CADASTRO DE GRUPOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d060`
--

LOCK TABLES `d060` WRITE;
/*!40000 ALTER TABLE `d060` DISABLE KEYS */;
/*!40000 ALTER TABLE `d060` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d061`
--

DROP TABLE IF EXISTS `d061`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d061` (
  `D061_Id` int(11) NOT NULL auto_increment,
  `D061_C004_Id` int(11) default NULL,
  `D061_C007_Id` int(11) default NULL,
  `D061_D018_Id` int(11) default NULL,
  `D061_D020_Id` varchar(40) default NULL,
  `D061_D039_Id` int(11) default NULL,
  `D061_D024_Id` int(10) unsigned default NULL,
  `D061_Flag_Pessoa_Fisica_Juridica` varchar(1) default NULL,
  `D061_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `D061_Cnpj` varchar(20) default NULL,
  `D061_Cpf` varchar(20) default NULL,
  `D061_Nome_Empresa` varchar(50) default NULL,
  `D061_Nome_Fantasia` varchar(50) default NULL,
  `D061_Endereco` varchar(40) default NULL,
  `D061_Cep` varchar(9) default NULL,
  `D061_Bairro` varchar(25) default NULL,
  `D061_Praca_Pagamento` varchar(65) default NULL,
  `D061_Inscricao_Estadual` varchar(20) default NULL,
  `D061_Referencia_Comercial_1` varchar(60) default NULL,
  `D061_Tipo_Empresa_1` char(1) default NULL,
  `D061_Telefone_Comercial_1` varchar(14) default NULL,
  `D061_Referencia_Comercial_2` varchar(60) default NULL,
  `D061_Tipo_Empresa_2` char(1) default NULL,
  `D061_Telefone_Comercial_2` varchar(14) default NULL,
  `D061_Referencia_Comercial_3` varchar(60) default NULL,
  `D061_Tipo_Empresa_3` char(1) default NULL,
  `D061_Telefone_Comercial_3` varchar(14) default NULL,
  `D061_Referencia_Comercial_4` varchar(60) default NULL,
  `D061_Tipo_Empresa_4` char(1) default NULL,
  `D061_Telefone_Comercial_4` varchar(14) default NULL,
  `D061_Referencia_Comercial_5` varchar(60) default NULL,
  `D061_Tipo_Empresa_5` char(1) default NULL,
  `D061_Telefone_Comercial_5` varchar(14) default NULL,
  `D061_Referencia_Bancaria` varchar(80) default NULL,
  `D061_Nome_Contato_1` varchar(40) default NULL,
  `D061_Telefone_1` varchar(30) default NULL,
  `D061_Ramal_Telefone_1` varchar(10) default NULL,
  `D061_Fax_1` varchar(30) default NULL,
  `D061_Celular_1` varchar(30) default NULL,
  `D061_Email_1` varchar(255) default NULL,
  `D061_Flag_Departamento_1` int(11) NOT NULL default '0',
  `D061_Nome_Contato_2` varchar(40) default NULL,
  `D061_Telefone_2` varchar(30) default NULL,
  `D061_Ramal_Telefone_2` varchar(10) default NULL,
  `D061_Fax_2` varchar(30) default NULL,
  `D061_Celular_2` varchar(30) default NULL,
  `D061_Email_2` varchar(255) default NULL,
  `D061_Flag_Departamento_2` int(11) NOT NULL default '0',
  `D061_Nome_Contato_3` varchar(40) default NULL,
  `D061_Telefone_3` varchar(30) default NULL,
  `D061_Ramal_Telefone_3` varchar(10) default NULL,
  `D061_Fax_3` varchar(30) default NULL,
  `D061_Celular_3` varchar(30) default NULL,
  `D061_Email_3` varchar(255) default NULL,
  `D061_Flag_Departamento_3` int(11) NOT NULL default '0',
  `D061_Observacoes_Pre_Cadastro` text,
  `D061_Observacoes_Cadastro` text,
  `D061_Flag_Situacao` int(1) default '1',
  `D061_Data_Inclusao` datetime default NULL,
  `D061_Telefone_Banco` char(14) default NULL,
  `D061_Flag_Como_Conheceu_Engepecas` tinyint(2) default '0',
  `D061_Eng_Amigo_Nome` char(50) default NULL,
  `D061_Eng_Amigo_Nome_Empresa` char(50) default NULL,
  `D061_Eng_Amigo_Telefone` char(14) default NULL,
  `D061_Eng_Amigo_Observacao_Brinde` text,
  `D061_Flag_Eventos` tinyint(1) default '0',
  `D061_Flag_Revista` tinyint(2) default '0',
  PRIMARY KEY  (`D061_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=646 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d061`
--

LOCK TABLES `d061` WRITE;
/*!40000 ALTER TABLE `d061` DISABLE KEYS */;
/*!40000 ALTER TABLE `d061` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d062`
--

DROP TABLE IF EXISTS `d062`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d062` (
  `D062_Id` int(11) NOT NULL auto_increment,
  `D062_Nome_Banco` varchar(40) NOT NULL,
  `D062_Numero_Banco` int(3) unsigned zerofill NOT NULL,
  PRIMARY KEY  (`D062_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d062`
--

LOCK TABLES `d062` WRITE;
/*!40000 ALTER TABLE `d062` DISABLE KEYS */;
INSERT INTO `d062` VALUES (1,'BANCO DO BRASIL',001),(2,'BRADESCO',237),(3,'BANCO CENTRAL DO BRASIL',002),(4,'BANCO DA AMAZONIA S.A.',003),(5,'BANCO DO NORDESTE DO BRASIL S.A.',004),(6,'BANCO NAC. DE CREDITO COOP. S.A.',006),(7,'BANCO NAC.DESENV.ECON. SOCIAL S.A.',007),(8,'BANCO MERIDIONAL S.A.',008),(9,'BACEN - TESOURO NACIONAL',009),(10,'BANCO DO EST. DE ALAGOAS S.A.',020),(11,'BANCO DO EST. DO ESPIRITO STO. S.A.',021),(12,'BANCO DE CRED. REAL DE M. GER. S.A',022),(13,'BANCO DO EST. DE PERNAMBUCO S.A.',024),(14,'BANCO ALFA S.A.',025),(15,'BANCO DO EST. DO ACRE S.A.',026),(16,'BANCO DO EST. DE SANTA CATARINA S.A',027),(17,'BANCO DO EST. DA BAHIA S.A.',028),(18,'BANCO BANERJ S.A.',029),(19,'PARAIBAN-BCO DO EST DA PARAIBA S.A.',030),(20,'BANCO DO EST. DE GOIAS S.A.',031),(21,'BANCO DO EST. DE MATO GROSSO S.A',032),(22,'BANCO DO EST. DE SP S.A.-BANESPA',033),(23,'BANCO DO EST. DO AMAZONAS S.A.',034),(24,'BANCO DO EST. DO CEARA S.A.',035),(25,'BANCO DO EST. DO MARANHAO S.A.',036),(26,'BANCO DO EST. DO PARA S.A.',037),(27,'BANCO DO EST. DO PARANA S.A',038),(28,'BANCO DO EST. DO PIAUI S.A.',039),(29,'BANCO CARGILL S .A .',040),(30,'BANCO DO EST. R. GRANDE DO SUL S.A.',041),(31,'BANCO J. SAFRA S. A .',042),(32,'BANCO DO EST. RIO GRANDE NORTE S.A.',043),(33,'BANCO BVA S . A .',044),(34,'BANCO DO EST. DE SERGIPE S.A.',047),(35,'BANCO BEMGE S.A.',048),(36,'BANCO DO EST. DE RONDONIA S.A.',059),(37,'BRB-BANCO DE BRASILIA S.A.',070),(38,'CAIXA ECONOMICA FEDERAL',104),(39,'BANCO ITABANCO S.A.',106),(40,'BANCO BBM S.A.',107),(41,'BANCO CREDIBANCO S.A.',109),(42,'BANCO BNL DO BRASIL S.A.',116),(43,'MULTI BANCO S.A.',148),(44,'CAIXA ECONOMICA EST. MINAS GERAIS',150),(45,'NOSSA CAIXA - NOSSO BANCO S.A.',151),(46,'CAIXA ECONOMICA DO ESTADO DE GOIAS',152),(47,'CAIXA ECONOMICA EST. RIO GDE. SUL',153),(48,'CAIXA EC DO EST DE ST CATARINA S.A',154),(49,'BANCO CREDIT COMMERCIAL FRANCE S.A.',164),(50,'BANCO NORCHEM S.A.',165),(51,'BANCO CCF BRASIL S.A.',168),(52,'BANCO DE FINANC. INTERNACIONAL S.A.',171),(53,'CONTINENTAL BANCO S.A.',175),(54,'BANCO BBA-CREDITANSTALT S.A.',184),(55,'BANCO FICRISA AXELRUD S.A.',200),(56,'BANCO AXIAL S.A.',201),(57,'BANCO BRASEG S.A.',202),(58,'BANCO SIBISA S.A.',203),(59,'BANCO INTER AMERICAN EXPRESS S.A.',204),(60,'BANCO SUL AMERICA S.A.',205),(61,'BANCO MARTINELLI S.A.',206),(62,'BANCO GARAVELO S.A.',207),(63,'BANCO PACTUAL S.A.',208),(64,'AGROBANCO BANCO COMERCIAL S.A.',209),(65,'DRESDNER BANK L. AKTIENGESELLCHAFT',210),(66,'BANCO SISTEMA S.A.',211),(67,'BANCO MATONE S.A.',212),(68,'BANCO ARBI S.A.',213),(69,'BANCO DIBENS S.A.',214),(70,'BANCO AMERICA DO SUL S.A.',215),(71,'BANCO REGIONAL MALCON S.A.',216),(72,'BANCO JOHN DEERE S . A .',217),(73,'BBS-BANCO BONSUCESSO S.A.',218),(74,'BANCO ZOGBI S . A .',219),(75,'BANCO CREFISUL S.A.',220),(76,'BANCO FLEMING GRAPHUS S.A.',221),(77,'BANCO AGF BRASEG S.A.',222),(78,'BANCO INTERUNION S.A.',223),(79,'BANCO FIBRA S.A.',224),(80,'BANCO BRASCAN S.A.',225),(81,'BANCO AUXILIAR S.A.',226),(82,'BANCO ROSA S.A.',227),(83,'BANCO ICATU S.A.',228),(84,'BANCO CRUZEIRO DO SUL S.A.',229),(85,'BANCO BANDEIRANTES S.A.',230),(86,'BANCO BOAVISTA INTERATLANTICO S.A.',231),(87,'BANCO INTERPART S.A.',232),(88,'BANCO GE CAPITAL S.A.',233),(89,'BANCO LAVRA S.A.',234),(90,'BANCO LIBERAL S.A.',235),(91,'BANCO CAMBIAL S.A.',236),(92,'BANCO BANCRED S.A.',239),(93,'BANCO DE CRED. REAL DE M. GER. S.A.',240),(94,'BANCO CLASSICO S.A.',241),(95,'BANCO EUROINVEST S.A.-EUROBANCO',242),(96,'BANCO STOCK MAXIMA S.A.',243),(97,'BANCO CIDADE S.A.',244),(98,'BANCO EMPRESARIAL S.A.',245),(99,'BANCO ABC-BRASIL S.A.',246),(100,'BANCO WARBURG DILLON READ S.A.',247),(101,'BANCO INVESTCRED S.A.',249),(102,'BANCO SCHAHIN S.A.',250),(103,'BANCO SAO JORGE S.A.',251),(104,'BANCO FININVEST S.A.',252),(105,'PARANA BANCO S.A.',254),(106,'MILBANCO S.A.',255),(107,'BANCO GULFINVEST S.A.',256),(108,'BANCO INDUSCRED S.A.',258),(109,'BANCO VARIG S.A.',261),(110,'BANCO BOREAL S.A.',262),(111,'BANCO CACIQUE S.A.',263),(112,'BANCO PERFORMANCE S.A.',264),(113,'BANCO FATOR S.A.',265),(114,'BANCO CEDULA S.A.',266),(115,'BANCO BBM-COM.C.IMOB.C.FIN.INV.S.A.',267),(116,'BANCO PLANIBANC S.A.',277),(117,'BANCO BRASILEIRO COMERCIAL S.A.',282),(118,'BANCO BCN S.A.',291),(119,'BCR-BANCO DE CREDITO REAL S.A.',294),(120,'BANCO CREDIPLAN S.A',295),(121,'BANCO DE LA NACION ARGENTINA',300),(122,'BANCO DO PROGRESSO S.A.',302),(123,'BANCO HNF S.A.',303),(124,'BANCO PONTUAL S.A.',304),(125,'BANCO COMERCIAL BANCESA S.A.',308),(126,'BANCO DO COMERCIO E IND. DE SP S.A',314),(127,'BANCO INTER-ATLANTICO S.A.',316),(128,'BANCO DO COMERCIO S.A.',317),(129,'BANCO BMG S.A',318),(130,'BANCO INDUSTRIAL E COMERCIAL S.A.',320),(131,'BANCO ECONOMICO S.A.',334),(132,'BANCO SAVENA S.A.',337),(133,'BANCO F. BARRETO S.A.',338),(134,'BANCO ITAU S.A.',341),(135,'BANCO MERCANTIL S.A.',344),(136,'BANCO FINANCIAL S.A.',345),(137,'BANCO SUDAMERIS BRASIL S.A.',347),(138,'BANCO BOZANO, SIMONSEN S.A.',351),(139,'BANCO SANTANDER BRASIL S.A.',353),(140,'BANCO ABN AMRO S.A.',356),(141,'BANCO INDUSTRIAL DE PERNAMBUCO S.A.',361),(142,'BANCO SOGERAL S.A.',366),(143,'BANCO DIGIBANCO S.A.',369),(144,'BANCO EUROPEU P/AM.LATINA (BEAL)S.A',370),(145,'BANCO ITAMARATI S.A.',372),(146,'BANCO FENICIA S.A.',375),(147,'BANCO CHASE MANHATTAN S.A.',376),(148,'BANCO BMD S.A.',388),(149,'BANCO MERCANTIL DO BRASIL S.A.',389),(150,'BANCO BMC S.A.',394),(151,'HSBC BANK BRASIL S.A.-BCO MULTIPLO',399),(152,'BANCO MINEIRO S.A.',405),(153,'UNIBANCO-UNIAO BCOS BRASILEIROS S.A',409),(154,'BANCO CAPITAL S.A.',412),(155,'BANCO NACIONAL S.A.',415),(156,'BANCO SUL BRASILEIRO S . A .',417),(157,'BANCO BANORTE S.A.',420),(158,'BANCO SAFRA S.A.',422),(159,'BANCO SANTANDER NOROESTE S.A.',424),(160,'BANCO PINTO DE MAGALHAES S . A .',432),(161,'BANFORT-BANCO FORTALEZA S.A.',434),(162,'BANCO REAL DE SAO PAULO S . A .',441),(163,'BANCO REGIONAL S . A .',446),(164,'BANCO RURAL S.A.',453),(165,'BANCO DETOKYO-MITSUBISHI BRASIL S.A',456),(166,'BANCO SUMITOMO BRASILEIRO S.A.',464),(167,'BANCO MITSUBISHI BRASILEIRO S.A.',466),(168,'LLOYDS BANK PLC',472),(169,'BANCO FINANCIAL PORTUGUES',473),(170,'BANCO DE CREDITO COMERCIAL S . A .',474),(171,'CITIBANK N.A.',477),(172,'BANKBOSTON BCO MULTIPLO S.A.',479),(173,'BANCO WACHOVIA S.A.',480),(174,'BANCO AGRIMISA S.A.',483),(175,'BANCO DE RORAIMA S.A.',485),(176,'DEUTSCHE BANK S.A.-BANCO ALEMAO',487),(177,'MORGAN GUARANTY TRUST COMPANY OF NY',488),(178,'BANCO FRANCES INTERN.-BRASIL S.A.',489),(179,'BANCO RESIDENCIA S . A .',490),(180,'BANCO MAISONNAVE S.A.',491),(181,'ING BANK N.V.',492),(182,'BANCO UNION, S.A.C.A.',493),(183,'BANCO DE LA REP. OR. DEL URUGUAY',494),(184,'BANCO DE LA PROV. DE BUENOS AIRES',495),(185,'BANCO EXTERIOR DE ESPANA S.A.',496),(186,'BANCO HISPANO AMERICANO S . A.',497),(187,'CENTRO HISPANO BANCO',498),(188,'BANCO IOCHPE S.A.',499),(189,'BANCO HABITASUL S . A .',500),(190,'BANCO BRASILEIRO IRAQUIANO S.A.',501),(191,'BANCO SANTANDER DE NEGOCIOS S.A',502),(192,'BANCO BRJ S . A .',503),(193,'BANCO MULTIPLIC S.A.',504),(194,'BANCO CREDIT SUISSE F. B. G. S.A.',505),(195,'BANCO LUSO BRASILEIRO S.A.',600),(196,'BFC BANCO S.A.',601),(197,'BANCO PATENTE S.A.',602),(198,'BANCO HERCULES S.A.',603),(199,'BANCO INDUSTRIAL DO BRASIL S.A.',604),(200,'BPA BANCO PAO DE ACUCAR S . A .',605),(201,'BANCO GRANDE RIO S.A.',606),(202,'BANCO SANTOS NEVES S.A.',607),(203,'BANCO OPEN S.A.',608),(204,'BANCO ADOLPHO OLIVEIRA E ASSOC.S.A.',609),(205,'BANCO VR S.A.',610),(206,'BANCO PAULISTA S.A.',611),(207,'BANCO GUANABARA S.A.',612),(208,'BANCO PECUNIA S.A.',613),(209,'BANCO INTERPACIFICO S.A.',616),(210,'BANCO INVESTOR S.A.',617),(211,'BANCO TENDENCIA S.A.',618),(212,'BANCO APLICAP S.A.',621),(213,'BANCO DRACMA S.A.',622),(214,'BANCO PANAMERICANO S.A.',623),(215,'BANCO GENERAL MOTORS S.A.',624),(216,'BANCO ARAUCARIA S.A.',625),(217,'BANCO FICSA S.A.',626),(218,'BANCO DESTAK S.A.',627),(219,'BANCO CRITERIUM S.A.',628),(220,'BANCORP - BANCO COM. E DE INV. S.A.',629),(221,'BANCO INTERCAP S.A.',630),(222,'BANCO COLUMBIA S.A.',631),(223,'BANCO RENDIMENTO S.A.',633),(224,'BANCO TRIANGULO S.A.',634),(225,'BANCO DO EST. DO AMAPA S.A.',635),(226,'BANCO SOFISA S.A.',637),(227,'BANCO PROSPER S.A.',638),(228,'BIG S.A.- BANCO IRMAOS GUIMARAES',639),(229,'BANCO CREDITO METROPOLITANO S.A.',640),(230,'BANCO BILBAO VIZCAYA BRASIL S.A.',641),(231,'BRASBANCO S.A. - BANCO COMERCIAL',642),(232,'BANCO PINE S.A.',643),(233,'BANCO DO EST. DE RORAIMA S.A.',645),(234,'BANCO MARKA S.A.',647),(235,'BANCO ATLANTIS S.A.',648),(236,'BANCO DIMENSAO S.A.',649),(237,'BANCO PEBB S.A.',650),(238,'BANCO FRANCES E BRASILEIRO S.A.',652),(239,'BANCO INDUSVAL S.A.',653),(240,'BANCO A.J.RENNER S.A.',654),(241,'BANCO VOTORANTIM S.A.',655),(242,'BANCO MATRIX S.A.',656),(243,'BANCO TECNICORP S.A.',657),(244,'BANCO PORTO REAL S.A.',658),(245,'BANCO SANTOS S.A.',702),(246,'BANCO INVESTCORP S.A.',705),(247,'BANCO DAYCOVAL S.A.',707),(248,'BANCO VETOR S.A.',711),(249,'BANCO CINDAM S.A.',713),(250,'BANCO VEGA S.A.',715),(251,'BANCO OPERADOR S.A.',718),(252,'BANCO BANIF PRIMUS S . A .',719),(253,'BANCO MAXINVEST S.A.',720),(254,'BANCO CREDIBEL S.A.',721),(255,'BANCO INTERIOR DE SAO PAULO S.A.',722),(256,'BANCO PORTO SEGURO S.A.',724),(257,'BANCO FINANSINOS S.A.',725),(258,'BANCO UNIVERSAL S.A.',726),(259,'BANCO COMERCIAL DE SAO PAULO S.A.',727),(260,'BANCO FITAL S.A.',728),(261,'BANCO FONTE CINDAM S.A.',729),(262,'BANCO COMERCIAL PARAGUAYO S.A.',730),(263,'BANCO GNPP S.A.',731),(264,'BANCO MINAS S.A.',732),(265,'BANCO DAS NACOES S.A.',733),(266,'BANCO GERDAU S.A.',734),(267,'BANCO POTTENCIAL S.A.',735);
/*!40000 ALTER TABLE `d062` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d063`
--

DROP TABLE IF EXISTS `d063`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d063` (
  `D063_Id` int(11) NOT NULL auto_increment,
  `D063_D024_Id` int(11) default NULL,
  `D063_Nome_Empresa` varchar(60) default NULL,
  `D063_Tipo_Empresa` char(1) default NULL,
  `D063_Telefone` varchar(14) default NULL,
  `D063_Data_Inclusao` datetime default NULL,
  `D063_Data_Cliente_Desde` date default NULL,
  `D063_Data_Ultima_Compra` date default NULL,
  `D063_Valor_Ultima_Compra` decimal(15,2) default NULL,
  `D063_Data_Maior_Compra` date default NULL,
  `D063_Valor_Maior_Compra` decimal(15,2) default NULL,
  `D063_Valor_Limite` decimal(15,2) default NULL,
  `D063_Pagamento` varchar(100) default NULL,
  `D063_Informante` varchar(100) default NULL,
  `D063_Observacao` text,
  PRIMARY KEY  (`D063_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2554 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d063`
--

LOCK TABLES `d063` WRITE;
/*!40000 ALTER TABLE `d063` DISABLE KEYS */;
/*!40000 ALTER TABLE `d063` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d064`
--

DROP TABLE IF EXISTS `d064`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d064` (
  `D064_Id` int(11) NOT NULL auto_increment,
  `D064_C007_Id` int(11) default NULL COMMENT 'Id do Usuario',
  `D064_Nome` varchar(50) NOT NULL COMMENT 'Nome da Enquete',
  `D064_Descricao` text NOT NULL COMMENT 'Descricao do Objetivo da Enquete',
  `D064_Data_Cadastro` date NOT NULL COMMENT 'Data do Cadastro da Enquete',
  PRIMARY KEY  (`D064_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabela Enquetes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d064`
--

LOCK TABLES `d064` WRITE;
/*!40000 ALTER TABLE `d064` DISABLE KEYS */;
/*!40000 ALTER TABLE `d064` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d065`
--

DROP TABLE IF EXISTS `d065`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d065` (
  `D065_Id` int(11) NOT NULL auto_increment,
  `D065_D064_Id` int(11) NOT NULL COMMENT 'Chave estrangeira da Enquete',
  `D065_Questao` varchar(150) default NULL,
  `D065_Tipo_Questao` tinyint(1) unsigned NOT NULL default '2' COMMENT '1 para Descritivo e 2 para Opcoes',
  `D065_Ordem` tinyint(1) unsigned default NULL COMMENT 'Ordem das questoes - E chave composta com ID',
  `D065_Flag_Multi_Escolha` tinyint(1) unsigned NOT NULL default '2' COMMENT '1 para sim e 2 para nao -   multipla escolha',
  PRIMARY KEY  (`D065_Id`),
  KEY `FK_D065_D064_Id` (`D065_D064_Id`),
  CONSTRAINT `FK_D065_D064_Id` FOREIGN KEY (`D065_D064_Id`) REFERENCES `d064` (`D064_Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Questoes da Enquete D064';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d065`
--

LOCK TABLES `d065` WRITE;
/*!40000 ALTER TABLE `d065` DISABLE KEYS */;
/*!40000 ALTER TABLE `d065` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d066`
--

DROP TABLE IF EXISTS `d066`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d066` (
  `D066_Id` int(11) NOT NULL auto_increment,
  `D066_D065_Id` int(11) default NULL,
  `D066_D066_Id` int(11) default NULL COMMENT 'Auto-Relacionamento com o D066_Id',
  `D066_Alternativa` varchar(100) NOT NULL COMMENT 'Descricao da Alternativa',
  `D066_Valor_Alternativa` varchar(100) default NULL COMMENT 'A alternativa pode conter um valor',
  `D066_Tipo_Alternativa` tinyint(3) unsigned NOT NULL default '2' COMMENT '1 para descritivo e 2 para opcao',
  `D066_Ordem` tinyint(3) unsigned default '1' COMMENT 'Ordem das opcoes na questao. Chave composta com o ID',
  PRIMARY KEY  (`D066_Id`),
  KEY `FK_D066_D066_Id` (`D066_D066_Id`),
  CONSTRAINT `FK_D066_D066_Id` FOREIGN KEY (`D066_D066_Id`) REFERENCES `d066` (`D066_Id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Alternativas das questoes da enquete';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d066`
--

LOCK TABLES `d066` WRITE;
/*!40000 ALTER TABLE `d066` DISABLE KEYS */;
/*!40000 ALTER TABLE `d066` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d067`
--

DROP TABLE IF EXISTS `d067`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d067` (
  `D067_Id` int(11) NOT NULL auto_increment,
  `D067_Aliquota_ICMS` decimal(5,2) default '0.00',
  `D067_Simbolo` varchar(3) NOT NULL,
  `D067_Descricao` varchar(30) default NULL,
  PRIMARY KEY  (`D067_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d067`
--

LOCK TABLES `d067` WRITE;
/*!40000 ALTER TABLE `d067` DISABLE KEYS */;
/*!40000 ALTER TABLE `d067` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d068`
--

DROP TABLE IF EXISTS `d068`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d068` (
  `D068_Id` int(11) NOT NULL auto_increment,
  `D068_D005_Id` int(11) default NULL,
  `D068_D067_Id` int(11) default NULL,
  `D068_C004_Id` int(11) default NULL,
  PRIMARY KEY  (`D068_Id`),
  UNIQUE KEY `UK` (`D068_C004_Id`,`D068_D005_Id`),
  KEY `FK_Aliquotas_ICMS` (`D068_D067_Id`),
  KEY `FK_Classificacao` (`D068_D005_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3657 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d068`
--

LOCK TABLES `d068` WRITE;
/*!40000 ALTER TABLE `d068` DISABLE KEYS */;
/*!40000 ALTER TABLE `d068` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d069`
--

DROP TABLE IF EXISTS `d069`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d069` (
  `D069_Id` int(10) NOT NULL auto_increment,
  `D069_D070_Id` int(10) default NULL,
  `D069_D002_Id` int(10) NOT NULL,
  `D069_D015_Id` int(10) NOT NULL,
  `D069_Codigo_Produto` varchar(30) default NULL,
  `D069_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `D069_Descricao` varchar(50) default NULL,
  `D069_Preco_Unitario` decimal(10,2) default NULL,
  `D069_Percentual_Desconto` decimal(7,4) default '0.0000',
  PRIMARY KEY  (`D069_Id`),
  KEY `D069_Codigo_Produto` (`D069_Codigo_Produto`)
) ENGINE=MyISAM AUTO_INCREMENT=9410 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d069`
--

LOCK TABLES `d069` WRITE;
/*!40000 ALTER TABLE `d069` DISABLE KEYS */;
/*!40000 ALTER TABLE `d069` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d070`
--

DROP TABLE IF EXISTS `d070`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d070` (
  `D070_Id` int(10) NOT NULL auto_increment,
  `D070_Descricao` varchar(50) default NULL,
  `D070_D024_Id` int(10) NOT NULL,
  `D070_Desconto` decimal(10,2) default NULL,
  `D070_Observacao` text,
  `D070_Tipo_Fator` varchar(1) NOT NULL,
  PRIMARY KEY  (`D070_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d070`
--

LOCK TABLES `d070` WRITE;
/*!40000 ALTER TABLE `d070` DISABLE KEYS */;
/*!40000 ALTER TABLE `d070` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d071`
--

DROP TABLE IF EXISTS `d071`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d071` (
  `D071_Id` int(11) NOT NULL auto_increment,
  `D071_Tipo_Portador` varchar(20) NOT NULL,
  PRIMARY KEY  (`D071_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d071`
--

LOCK TABLES `d071` WRITE;
/*!40000 ALTER TABLE `d071` DISABLE KEYS */;
/*!40000 ALTER TABLE `d071` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d072`
--

DROP TABLE IF EXISTS `d072`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d072` (
  `D072_Id` bigint(20) unsigned NOT NULL auto_increment,
  `D072_D001_Id` int(10) unsigned default NULL,
  `D072_Arquivo_Foto` varchar(50) NOT NULL,
  `D072_Flag_Principal` enum('S','N') default 'N',
  PRIMARY KEY  (`D072_Id`),
  UNIQUE KEY `Duplicidade` (`D072_D001_Id`,`D072_Arquivo_Foto`),
  KEY `D072_IDX_PRODUTOS` (`D072_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='FOTOS DOS ITENS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d072`
--

LOCK TABLES `d072` WRITE;
/*!40000 ALTER TABLE `d072` DISABLE KEYS */;
/*!40000 ALTER TABLE `d072` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d073`
--

DROP TABLE IF EXISTS `d073`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d073` (
  `D073_Id` int(10) NOT NULL auto_increment,
  `D073_Nome` varchar(30) default NULL,
  PRIMARY KEY  (`D073_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='CENTRO DE CUSTOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d073`
--

LOCK TABLES `d073` WRITE;
/*!40000 ALTER TABLE `d073` DISABLE KEYS */;
/*!40000 ALTER TABLE `d073` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d074`
--

DROP TABLE IF EXISTS `d074`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d074` (
  `D074_Id` int(10) NOT NULL auto_increment,
  `D074_D001_Id_Produto` int(10) unsigned NOT NULL,
  `D074_D001_Id` int(10) unsigned NOT NULL,
  `D074_Quantidade` decimal(10,3) default NULL,
  PRIMARY KEY  (`D074_Id`),
  KEY `D074_FKIndex1` (`D074_D001_Id`),
  KEY `D074_FKIndex2` (`D074_D001_Id_Produto`),
  CONSTRAINT `D074_ibfk_1` FOREIGN KEY (`D074_D001_Id`) REFERENCES `d001` (`D001_Id`),
  CONSTRAINT `D074_ibfk_2` FOREIGN KEY (`D074_D001_Id_Produto`) REFERENCES `d001` (`D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='COMPOSICAO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d074`
--

LOCK TABLES `d074` WRITE;
/*!40000 ALTER TABLE `d074` DISABLE KEYS */;
/*!40000 ALTER TABLE `d074` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d075`
--

DROP TABLE IF EXISTS `d075`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d075` (
  `D075_Id` int(10) NOT NULL auto_increment,
  `D075_D079_Id` int(10) NOT NULL,
  `D075_D078_Id` int(10) NOT NULL,
  `D075_D001_Id` int(10) unsigned NOT NULL,
  `D075_Lead_Time` decimal(5,2) default '0.00',
  `D075_Descricao` text,
  `D075_Ordem` int(2) default '0',
  PRIMARY KEY  (`D075_Id`),
  KEY `D075_FKIndex1` (`D075_D001_Id`),
  KEY `D075_FKIndex2` (`D075_D078_Id`),
  KEY `D075_FKIndex3` (`D075_D079_Id`),
  CONSTRAINT `D075_ibfk_1` FOREIGN KEY (`D075_D001_Id`) REFERENCES `d001` (`D001_Id`),
  CONSTRAINT `D075_ibfk_2` FOREIGN KEY (`D075_D078_Id`) REFERENCES `d078` (`D078_Id`),
  CONSTRAINT `D075_ibfk_3` FOREIGN KEY (`D075_D079_Id`) REFERENCES `d079` (`D079_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='PROCESSOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d075`
--

LOCK TABLES `d075` WRITE;
/*!40000 ALTER TABLE `d075` DISABLE KEYS */;
/*!40000 ALTER TABLE `d075` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d076`
--

DROP TABLE IF EXISTS `d076`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d076` (
  `D076_Id` int(10) NOT NULL auto_increment,
  `D076_D075_Id` int(10) NOT NULL,
  `D076_D001_Id` int(10) unsigned NOT NULL,
  `D076_Quantidade` decimal(10,3) default '0.000',
  PRIMARY KEY  (`D076_Id`),
  KEY `D076_FKIndex1` (`D076_D075_Id`),
  KEY `D076_FKIndex2` (`D076_D001_Id`),
  CONSTRAINT `D076_ibfk_1` FOREIGN KEY (`D076_D075_Id`) REFERENCES `d075` (`D075_Id`),
  CONSTRAINT `D076_ibfk_2` FOREIGN KEY (`D076_D001_Id`) REFERENCES `d001` (`D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='CONSUMIVEIS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d076`
--

LOCK TABLES `d076` WRITE;
/*!40000 ALTER TABLE `d076` DISABLE KEYS */;
/*!40000 ALTER TABLE `d076` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d077`
--

DROP TABLE IF EXISTS `d077`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d077` (
  `D077_Id` int(10) NOT NULL auto_increment,
  `D077_D078_Id` int(10) NOT NULL,
  `D077_Nome` varchar(30) default NULL,
  PRIMARY KEY  (`D077_Id`),
  KEY `D077_FKIndex1` (`D077_D078_Id`),
  CONSTRAINT `D077_ibfk_1` FOREIGN KEY (`D077_D078_Id`) REFERENCES `d078` (`D078_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='FUNCIONARIOS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d077`
--

LOCK TABLES `d077` WRITE;
/*!40000 ALTER TABLE `d077` DISABLE KEYS */;
/*!40000 ALTER TABLE `d077` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d078`
--

DROP TABLE IF EXISTS `d078`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d078` (
  `D078_Id` int(10) NOT NULL auto_increment,
  `D078_Nome` varchar(30) default NULL,
  `D078_Custo_Minuto` decimal(10,3) default NULL,
  PRIMARY KEY  (`D078_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='FUNCAO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d078`
--

LOCK TABLES `d078` WRITE;
/*!40000 ALTER TABLE `d078` DISABLE KEYS */;
/*!40000 ALTER TABLE `d078` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d079`
--

DROP TABLE IF EXISTS `d079`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d079` (
  `D079_Id` int(10) NOT NULL auto_increment,
  `D079_Nome` varchar(30) default NULL,
  `D079_Custo_Minuto` decimal(10,3) default NULL,
  PRIMARY KEY  (`D079_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MAQUINAS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d079`
--

LOCK TABLES `d079` WRITE;
/*!40000 ALTER TABLE `d079` DISABLE KEYS */;
/*!40000 ALTER TABLE `d079` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d080`
--

DROP TABLE IF EXISTS `d080`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d080` (
  `D080_Id` int(11) NOT NULL auto_increment,
  `D080_Turno` varchar(20) NOT NULL,
  `D080_Hora_Inicio` varchar(5) NOT NULL,
  `D080_Hora_Fim` varchar(5) NOT NULL,
  PRIMARY KEY  (`D080_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d080`
--

LOCK TABLES `d080` WRITE;
/*!40000 ALTER TABLE `d080` DISABLE KEYS */;
/*!40000 ALTER TABLE `d080` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d081`
--

DROP TABLE IF EXISTS `d081`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d081` (
  `D081_Id` int(11) NOT NULL auto_increment,
  `D081_D002_Id` int(11) NOT NULL,
  `D081_D080_Id` int(11) NOT NULL,
  PRIMARY KEY  (`D081_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d081`
--

LOCK TABLES `d081` WRITE;
/*!40000 ALTER TABLE `d081` DISABLE KEYS */;
/*!40000 ALTER TABLE `d081` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d082`
--

DROP TABLE IF EXISTS `d082`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d082` (
  `D082_Id` int(11) NOT NULL auto_increment,
  `D082_Marca` varchar(30) default NULL,
  `D082_C008_Id` int(10) default NULL,
  `D082_Percentual_Acrescimo` decimal(10,2) default NULL,
  PRIMARY KEY  (`D082_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d082`
--

LOCK TABLES `d082` WRITE;
/*!40000 ALTER TABLE `d082` DISABLE KEYS */;
/*!40000 ALTER TABLE `d082` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d083`
--

DROP TABLE IF EXISTS `d083`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d083` (
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
  `D083_D049_Id` int(11) default NULL,
  PRIMARY KEY  (`D083_Id`),
  KEY `D083_Index_Produtos` (`D083_D001_Id`),
  KEY `D083_Index_Fornecedores` (`D083_D024_Id`),
  KEY `IDX_Codigo_Produto_Fornecedor` (`D083_Codigo_Produto_Fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d083`
--

LOCK TABLES `d083` WRITE;
/*!40000 ALTER TABLE `d083` DISABLE KEYS */;
/*!40000 ALTER TABLE `d083` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d084`
--

DROP TABLE IF EXISTS `d084`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d084` (
  `D084_Id` int(10) unsigned NOT NULL auto_increment,
  `D084_Regiao` varchar(25) default NULL,
  `D084_Observacao` text,
  PRIMARY KEY  (`D084_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d084`
--

LOCK TABLES `d084` WRITE;
/*!40000 ALTER TABLE `d084` DISABLE KEYS */;
/*!40000 ALTER TABLE `d084` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d085`
--

DROP TABLE IF EXISTS `d085`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d085` (
  `D085_Id` int(10) unsigned NOT NULL auto_increment,
  `D085_D024_Id` int(10) unsigned default NULL,
  `D085_C007_Id` int(10) unsigned default NULL,
  `D085_Data` date default '0000-00-00',
  `D085_Mensagem` text,
  PRIMARY KEY  (`D085_Id`),
  KEY `IDX_D085_D024_Id` (`D085_D024_Id`),
  KEY `IDX_D085_C007_Id` (`D085_C007_Id`),
  KEY `IDX_D085_Data` (`D085_Data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d085`
--

LOCK TABLES `d085` WRITE;
/*!40000 ALTER TABLE `d085` DISABLE KEYS */;
/*!40000 ALTER TABLE `d085` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d100`
--

DROP TABLE IF EXISTS `d100`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d100` (
  `D100_Id` int(10) unsigned NOT NULL auto_increment,
  `D100_Data` date NOT NULL,
  `D100_Dia` int(2) NOT NULL default '0',
  `D100_Mes` int(2) unsigned zerofill default NULL,
  `D100_Ano` int(4) NOT NULL default '0',
  `D100_Numero_Semana` int(1) NOT NULL default '0',
  `D100_Dia_Semana` varchar(15) NOT NULL,
  `D100_Observacao` text NOT NULL,
  `D100_Bimestre` int(1) default '0',
  `D100_Trimestre` int(1) default '0',
  `D100_Semestre` int(1) default '0',
  PRIMARY KEY  (`D100_Id`),
  KEY `D100_Index_Data` (`D100_Data`),
  KEY `D100_Index_Dia` (`D100_Dia`),
  KEY `D100_Index_Mes` (`D100_Mes`),
  KEY `D100_Index_Ano` (`D100_Ano`),
  KEY `D100_Index_Numero_Semana` (`D100_Numero_Semana`),
  KEY `D100_Index_Dia_Semana` (`D100_Dia_Semana`),
  KEY `D100_Index_Bimestre` (`D100_Bimestre`),
  KEY `D100_Index_Trimestre` (`D100_Trimestre`),
  KEY `D100_Index_Semestre` (`D100_Semestre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d100`
--

LOCK TABLES `d100` WRITE;
/*!40000 ALTER TABLE `d100` DISABLE KEYS */;
/*!40000 ALTER TABLE `d100` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d101`
--

DROP TABLE IF EXISTS `d101`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d101` (
  `D101_Id` int(10) NOT NULL auto_increment,
  `D101_Ano` varchar(4) default NULL,
  `D101_Mes` varchar(2) default NULL,
  `D101_Trimestre` int(1) NOT NULL,
  `D101_Semestre` int(1) NOT NULL,
  `D101_Nome_Mes` varchar(30) NOT NULL,
  PRIMARY KEY  (`D101_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d101`
--

LOCK TABLES `d101` WRITE;
/*!40000 ALTER TABLE `d101` DISABLE KEYS */;
/*!40000 ALTER TABLE `d101` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `l001`
--

DROP TABLE IF EXISTS `l001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `l001` (
  `L001_Id` int(11) NOT NULL auto_increment,
  `L001_D009_Id` int(11) default NULL,
  `L001_Qte1` decimal(10,3) default NULL,
  `L001_Qte2` decimal(10,3) default NULL,
  `L001_Estoque_Fisico` decimal(10,3) default NULL,
  `L001_Estoque_Inicial` decimal(10,3) default NULL,
  `L001_Estoque` varchar(10) default NULL,
  PRIMARY KEY  (`L001_Id`),
  KEY `L001_D009_Id` (`L001_D009_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=1411987 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `l001`
--

LOCK TABLES `l001` WRITE;
/*!40000 ALTER TABLE `l001` DISABLE KEYS */;
/*!40000 ALTER TABLE `l001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s001`
--

DROP TABLE IF EXISTS `s001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s001` (
  `S001_Id` int(11) NOT NULL auto_increment,
  `S001_Data_Hora_Inclusao` datetime default NULL,
  `S001_Nome` varchar(200) default NULL,
  `S001_Assunto` varchar(200) default NULL,
  `S001_Email` varchar(200) default NULL,
  `S001_Mensagem_Fale_Conosco` text,
  PRIMARY KEY  (`S001_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s001`
--

LOCK TABLES `s001` WRITE;
/*!40000 ALTER TABLE `s001` DISABLE KEYS */;
/*!40000 ALTER TABLE `s001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s002`
--

DROP TABLE IF EXISTS `s002`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s002` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s002`
--

LOCK TABLES `s002` WRITE;
/*!40000 ALTER TABLE `s002` DISABLE KEYS */;
/*!40000 ALTER TABLE `s002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s003`
--

DROP TABLE IF EXISTS `s003`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s003` (
  `S003_Email` varchar(200) NOT NULL,
  `S003_Receber` enum('S','N') default 'S',
  `S003_Ativo` enum('S','N') default 'S',
  `S003_Data_Inclusao` datetime default '0000-00-00 00:00:00',
  `S003_Nome` varchar(50) default NULL,
  PRIMARY KEY  (`S003_Email`),
  KEY `IDX_Email` (`S003_Email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s003`
--

LOCK TABLES `s003` WRITE;
/*!40000 ALTER TABLE `s003` DISABLE KEYS */;
/*!40000 ALTER TABLE `s003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s006`
--

DROP TABLE IF EXISTS `s006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s006` (
  `S006_Id` bigint(20) unsigned NOT NULL auto_increment,
  `S006_T064_Id` bigint(20) unsigned default NULL,
  `S006_Data_Hora_Inclusao` datetime default NULL,
  `S006_Flag_Status` enum('0','1','2','3','4','5','6') default '0',
  PRIMARY KEY  (`S006_Id`),
  KEY `Fk_Pedido_Internet` (`S006_T064_Id`),
  CONSTRAINT `Fk_Pedido_Internet` FOREIGN KEY (`S006_T064_Id`) REFERENCES `t064` (`T064_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s006`
--

LOCK TABLES `s006` WRITE;
/*!40000 ALTER TABLE `s006` DISABLE KEYS */;
/*!40000 ALTER TABLE `s006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s007`
--

DROP TABLE IF EXISTS `s007`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s007` (
  `S007_Id` bigint(20) unsigned NOT NULL auto_increment,
  `S007_Data_Inclusao` datetime default NULL,
  `S007_Email_Remetente` varchar(60) default NULL,
  `S007_Email_Apresentacao` varchar(100) default NULL,
  `S007_Titulo` varchar(200) default NULL,
  `S007_Arquivo_Anexo` varchar(200) default NULL,
  PRIMARY KEY  (`S007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s007`
--

LOCK TABLES `s007` WRITE;
/*!40000 ALTER TABLE `s007` DISABLE KEYS */;
/*!40000 ALTER TABLE `s007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s008`
--

DROP TABLE IF EXISTS `s008`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s008` (
  `S008_Id` bigint(20) unsigned NOT NULL auto_increment,
  `S008_S007_Id` bigint(20) unsigned default NULL,
  `S008_Email` varchar(200) NOT NULL,
  `S008_Data_Envio` datetime default '0000-00-00 00:00:00',
  `S008_Data_Visualizado` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`S008_Id`),
  UNIQUE KEY `Duplicidade` (`S008_S007_Id`,`S008_Email`),
  CONSTRAINT `Fk_News` FOREIGN KEY (`S008_S007_Id`) REFERENCES `s007` (`S007_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s008`
--

LOCK TABLES `s008` WRITE;
/*!40000 ALTER TABLE `s008` DISABLE KEYS */;
/*!40000 ALTER TABLE `s008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s009`
--

DROP TABLE IF EXISTS `s009`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s009` (
  `S009_Id` int(10) NOT NULL auto_increment,
  `S009_Nome` varchar(20) default NULL,
  `S009_Email` varchar(50) default NULL,
  `S009_Observacao` text,
  `S009_Melhorar_Prazo_Entrega` enum('S','N') default 'N',
  `S009_Melhorar_Servicos` enum('S','N') default 'N',
  `S009_Melhorar_Embalagem` enum('S','N') default 'N',
  `S009_Melhorar_Atendimento_Telefonico` enum('S','N') default 'N',
  `S009_Outros` enum('S','N') default 'N',
  PRIMARY KEY  (`S009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s009`
--

LOCK TABLES `s009` WRITE;
/*!40000 ALTER TABLE `s009` DISABLE KEYS */;
/*!40000 ALTER TABLE `s009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t001`
--

DROP TABLE IF EXISTS `t001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t001` (
  `T001_Id` int(10) unsigned NOT NULL auto_increment,
  `T001_T072_Id` int(10) unsigned default NULL,
  `T001_T013_Id` int(10) unsigned default NULL,
  `T001_T014_Id` int(10) unsigned default NULL,
  `T001_T007_Id` int(10) unsigned default NULL,
  `T001_T008_Id` int(10) unsigned default NULL,
  `T001_T054_Id` int(10) unsigned default NULL,
  `T001_T055_Id` int(10) unsigned default NULL,
  `T001_D024_Id` int(10) unsigned default NULL,
  `T001_D024_Id_Cliente` int(10) unsigned default NULL,
  `T001_D009_Id` int(10) unsigned default NULL,
  `T001_D001_Id` int(10) unsigned default NULL,
  `T001_T048_Id` int(10) unsigned default NULL,
  `T001_T071_Id` int(11) default '0',
  `T001_T066_Id` int(10) unsigned default NULL,
  `T001_D006_Id` int(10) unsigned default NULL,
  `T001_T029_Id` int(10) unsigned default NULL,
  `T001_T030_Id` int(10) unsigned default NULL,
  `T001_C004_Id` int(11) default NULL,
  `T001_Codigo_Produto` varchar(30) default NULL,
  `T001_Data_Lancamento` date default NULL,
  `T001_Descricao_Operacao` varchar(20) default NULL,
  `T001_Quantidade` decimal(10,3) NOT NULL default '0.000',
  `T001_Quantidade_Saldo` decimal(10,3) NOT NULL default '0.000',
  `T001_Flag_Operacao` varchar(1) default NULL,
  `T001_Valor_Preco_Unitario` decimal(14,2) NOT NULL default '0.00',
  `T001_Observacao` varchar(40) default NULL,
  `T001_Valor_Custo_Unitario` decimal(14,2) NOT NULL default '0.00',
  `T001_Valor_Custo_Medio_Unitario` decimal(14,2) NOT NULL default '0.00',
  `T001_Codigo_Fornecedor` varchar(6) default NULL,
  `T001_Codigo_Cliente` varchar(6) default NULL,
  `T001_Flag_Complemento` varchar(1) default NULL,
  `T001_Demonstrativo_Custo` text,
  `T001_Flag_MMF` varchar(1) default NULL,
  `T001_Flag_Atualiza_Custo` varchar(1) default NULL,
  `T001_Flag_Venda_Compra_Outros` varchar(1) default NULL,
  `T001_Flag_Devolucao` varchar(1) default NULL,
  `T001_Numero_Controle` varchar(20) NOT NULL,
  PRIMARY KEY  (`T001_Id`),
  KEY `T001_Index_C004_Id` (`T001_C004_Id`),
  KEY `T001_Index_Nota_Entrada` (`T001_T013_Id`),
  KEY `T001_Index_Itens_Nota_Entrada` (`T001_T014_Id`),
  KEY `T001_Index_Nota_Saida` (`T001_T007_Id`),
  KEY `T001_Index_Itens_Nota_Saida` (`T001_T008_Id`),
  KEY `T001_Index_Importacao` (`T001_T054_Id`),
  KEY `T001_Index_Itens_Importacao` (`T001_T055_Id`),
  KEY `T001_Index_Codigo_Produto` (`T001_Codigo_Produto`),
  KEY `T001_Index_Codigo_Fornecedor` (`T001_Codigo_Fornecedor`),
  KEY `T001_Index_Descricao_Operacao` (`T001_Descricao_Operacao`),
  KEY `T001_Index_Fornecedores` (`T001_D024_Id`),
  KEY `T001_Index_Clientes` (`T001_D024_Id_Cliente`),
  KEY `T001_Index_Estoque` (`T001_D009_Id`),
  KEY `T001_Index_Montagem` (`T001_T048_Id`),
  KEY `T001_Index_Estoque2` (`T001_T066_Id`),
  KEY `T001_Index_Natureza_Operacao` (`T001_D006_Id`),
  KEY `T001_Index_Historico` (`T001_D001_Id`),
  KEY `T001_Index_DE` (`T001_T029_Id`),
  KEY `T001_Index_Pendencia` (`T001_T072_Id`),
  KEY `T001_Index_Itens_DE` (`T001_T030_Id`),
  KEY `IDX_T071` (`T001_T071_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t001`
--

LOCK TABLES `t001` WRITE;
/*!40000 ALTER TABLE `t001` DISABLE KEYS */;
/*!40000 ALTER TABLE `t001` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t002`
--

DROP TABLE IF EXISTS `t002`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t002` (
  `T002_Id` int(10) unsigned NOT NULL auto_increment,
  `T002_T117_Id` int(10) unsigned default NULL,
  `T002_D073_Id` int(10) unsigned default NULL,
  `T002_T054_Id` int(10) NOT NULL default '0',
  `T002_T045_Id` int(10) unsigned default NULL,
  `T002_D014_Id` int(10) unsigned default NULL,
  `T002_T018_Id` int(10) unsigned default NULL,
  `T002_D027_Id` int(10) unsigned default NULL,
  `T002_D024_Id` int(10) unsigned default NULL,
  `T002_D021_Id` int(10) unsigned default NULL,
  `T002_D007_Id` int(10) unsigned default NULL,
  `T002_T013_Id` int(10) unsigned default NULL,
  `T002_T007_Id` int(10) unsigned default NULL,
  `T002_T035_Id` int(10) unsigned default NULL,
  `T002_D062_Id` int(10) unsigned NOT NULL default '1',
  `T002_C004_Id` int(11) NOT NULL default '0',
  `T002_Flag_Conta` varchar(1) default NULL,
  `T002_Nome_Cliente` varchar(50) default NULL,
  `T002_Numero_Duplicata` varchar(15) default NULL,
  `T002_Numero_Documento` varchar(15) default NULL,
  `T002_Data_Emissao` date default NULL,
  `T002_Data_Vencimento` date default NULL,
  `T002_Data_Recebimento` date default NULL,
  `T002_Valor_Duplicata` decimal(14,2) NOT NULL default '0.00',
  `T002_Valor_Juros` decimal(12,2) NOT NULL default '0.00',
  `T002_Valor_Desconto` decimal(12,2) NOT NULL default '0.00',
  `T002_Valor_Total` decimal(14,2) NOT NULL default '0.00',
  `T002_Numero_Agencia` int(4) NOT NULL default '0',
  `T002_Numero_Conta` varchar(10) default NULL,
  `T002_Numero_Cheque` int(10) NOT NULL default '0',
  `T002_Observacao_1` varchar(40) default NULL,
  `T002_Observacao_2` varchar(40) default NULL,
  `T002_Numero_Controle` int(6) NOT NULL default '0',
  `T002_Data_Lancamento` date default NULL,
  `T002_Hora_Lancamento` time default NULL,
  `T002_Valor_Base_Comissao` decimal(10,2) NOT NULL default '0.00',
  `T002_Percentual_Comissao` decimal(10,2) NOT NULL default '0.00',
  `T002_Valor_Comissao` decimal(10,2) NOT NULL default '0.00',
  `T002_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T002_Data_Vencimento_1` date default NULL,
  `T002_Data_Vencimento_2` date default NULL,
  `T002_Data_Vencimento_3` date default NULL,
  `T002_Data_Vencimento_4` date default NULL,
  `T002_Data_Vencimento_5` date default NULL,
  `T002_Agrupamento` varchar(20) default NULL,
  `T002_Data_Agrupamento` date default NULL,
  `T002_Nosso_Numero` varchar(12) default NULL,
  `T002_Codigo_Cliente` varchar(6) default NULL,
  `T002_Codigo_Revenda` varchar(6) default NULL,
  `T002_Codigo_Conta_Banco` int(3) NOT NULL default '0',
  `T002_Conta` varchar(16) default NULL,
  `T002_SubConta` varchar(30) default NULL,
  `T002_Flag_Deb_Cred` varchar(1) default NULL,
  `T002_Flag_Enviar_Banco` int(1) NOT NULL default '1',
  `T002_Flag_Enviar_Banco_Protestar` int(1) NOT NULL default '1',
  `T002_Flag_Enviar_Banco_Juros` int(1) NOT NULL default '1',
  `T002_Flag_Tipo` int(1) NOT NULL default '0',
  `T002_Flag_Despesas` int(1) NOT NULL default '1',
  `T002_Obs_Mudanca_Vencto` text,
  `T002_Obs_Divisao_Titulo` text,
  `T002_Flag_Cancelada` varchar(1) default 'N',
  `T002_Flag_Baixar_Lote` varchar(1) default 'N',
  `T002_Numero_Parcela` int(3) default '0',
  `T002_Flag_Titulo_Conferido` char(1) default 'N',
  `T002_Observacao` text,
  `T002_Data_Baixa` date default '0000-00-00',
  `T002_Flag_Reparcelar` enum('S','N') default 'N',
  `T002_Data_Previsao_Recebimento` date default NULL,
  `T002_Observacao_Cobranca` tinytext,
  `T002_Valor_Recebido` decimal(12,2) default '0.00',
  PRIMARY KEY  (`T002_Id`),
  KEY `T002_Index_Id` (`T002_Id`),
  KEY `T002_Index_Nota_Fiscal_Entrada` (`T002_T013_Id`),
  KEY `T002_Index_Baixa_Lote` (`T002_T045_Id`),
  KEY `T002_Index_Nota_Fiscal_Saida` (`T002_T007_Id`),
  KEY `T002_Index_Clientes` (`T002_D024_Id`),
  KEY `T002_Index_Contas_Bancarias` (`T002_D007_Id`),
  KEY `T002_Index_Revendas` (`T002_D021_Id`),
  KEY `T002_Index_Codigo_Cliente` (`T002_Codigo_Cliente`),
  KEY `T002_Index_C004_Id` (`T002_C004_Id`),
  KEY `T002_Index_Numero_Duplicata` (`T002_Numero_Duplicata`),
  KEY `T002_Index_Conta` (`T002_Conta`),
  KEY `T002_Index_SubConta` (`T002_SubConta`),
  KEY `T002_FKIndex6` (`T002_D014_Id`),
  KEY `T002_Index_Data_Vencimento` (`T002_Data_Vencimento`),
  KEY `T002_Index_Data_Recebimento` (`T002_Data_Recebimento`),
  KEY `T002_Index_Portador` (`T002_D027_Id`),
  KEY `T002_Index_Projecoes` (`T002_T018_Id`),
  KEY `T002_Index_Remessa` (`T002_T035_Id`),
  KEY `T002_Index_Flag_Enviar_Banco` (`T002_Flag_Enviar_Banco`),
  KEY `T002_Index_Agrupamento` (`T002_Agrupamento`),
  KEY `T002_Index_Nosso_Numero` (`T002_Nosso_Numero`),
  KEY `T002_index_D062_Id` (`T002_D062_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t002`
--

LOCK TABLES `t002` WRITE;
/*!40000 ALTER TABLE `t002` DISABLE KEYS */;
/*!40000 ALTER TABLE `t002` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t003`
--

DROP TABLE IF EXISTS `t003`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t003` (
  `T003_Id` int(10) unsigned NOT NULL auto_increment,
  `T003_C008_Id` int(11) default NULL,
  `T003_D027_Id_Entrada` int(10) unsigned default NULL,
  `T003_D027_Id` int(10) unsigned default NULL,
  `T003_T064_Id` int(10) unsigned default NULL,
  `T003_D047_Id` int(10) unsigned default NULL,
  `T003_D018_Id` int(10) unsigned default NULL,
  `T003_D013_Id` int(10) unsigned default NULL,
  `T003_D022_Id` int(10) unsigned default NULL,
  `T003_D036_Id` int(10) unsigned default NULL,
  `T003_D021_Id` int(10) unsigned default NULL,
  `T003_D024_Id` int(10) unsigned default NULL,
  `T003_D006_Id` int(10) unsigned default NULL,
  `T003_C007_Id_Vendedor_Interno` int(10) unsigned default NULL,
  `T003_C007_Id_Vendedor_Externo` int(10) unsigned default NULL,
  `T003_C007_Id_IPV_Liberado` int(10) unsigned default NULL,
  `T003_C004_Id` int(10) unsigned default NULL,
  `T003_Numero_Orcamento_Venda` int(10) NOT NULL default '0',
  `T003_Nome_Cliente` varchar(35) default NULL,
  `T003_Nome_Transportadora` varchar(35) default NULL,
  `T003_Data_Emissao` date default NULL,
  `T003_Flag_Frete` int(1) NOT NULL default '0',
  `T003_Prazo_Entrega` varchar(40) default NULL,
  `T003_Nome_Comprador` varchar(30) default NULL,
  `T003_E_mail_Comprador` varchar(60) default NULL,
  `T003_Observacao_1` text,
  `T003_Observacao_2` varchar(70) default NULL,
  `T003_Fax` varchar(30) default NULL,
  `T003_Nome_Vendedor` varchar(10) default NULL,
  `T003_Valor_Desconto` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Desconto_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Percentual_Desconto` decimal(30,20) default NULL,
  `T003_Percentual_Desconto_Confirmado` decimal(30,20) default NULL,
  `T003_Valor_Percentual_Desconto` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Percentual_Desconto_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Percentual_Margem` decimal(6,2) NOT NULL default '0.00',
  `T003_Mao_Obra_1` varchar(100) default NULL,
  `T003_Mao_Obra_2` varchar(100) default NULL,
  `T003_Mao_Obra_3` varchar(100) default NULL,
  `T003_Mao_Obra_4` varchar(100) default NULL,
  `T003_Valor_Mao_Obra` decimal(10,2) NOT NULL default '0.00',
  `T003_Flag_Confirmar_Mao_Obra` varchar(1) NOT NULL default 'N',
  `T003_Flag_Perdido` varchar(1) default NULL,
  `T003_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `T003_Flag_Acp` varchar(1) default NULL,
  `T003_Prazo_1` int(4) NOT NULL default '0',
  `T003_Prazo_2` int(4) NOT NULL default '0',
  `T003_Prazo_3` int(4) NOT NULL default '0',
  `T003_Prazo_4` int(4) NOT NULL default '0',
  `T003_Prazo_5` int(4) NOT NULL default '0',
  `T003_Prazo_6` int(4) NOT NULL default '0',
  `T003_Prazo_7` int(4) NOT NULL default '0',
  `T003_NF_Observacao_1` text,
  `T003_NF_Observacao_2` text,
  `T003_NF_Devolucao_Numero` varchar(12) default NULL,
  `T003_NF_Devolucao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T003_NF_Devolucao_Numero_Pedido` int(10) NOT NULL default '0',
  `T003_NF_Devolucao_Emissao` date default NULL,
  `T003_Valor_Frete` decimal(19,2) NOT NULL default '0.00',
  `T003_Valor_Despesas_Acessorias` decimal(10,2) NOT NULL default '0.00',
  `T003_Estado` varchar(2) default NULL,
  `T003_Codigo_Usuario` int(4) NOT NULL default '0',
  `T003_Codigo_Cliente` varchar(6) default NULL,
  `T003_Codigo_Natureza` int(5) NOT NULL default '0',
  `T003_Codigo_Transportadora` varchar(6) default NULL,
  `T003_Codigo_Representante` varchar(6) default NULL,
  `T003_IPV` decimal(10,4) NOT NULL default '0.0000',
  `T003_IPV_Confirmado` decimal(10,4) NOT NULL default '0.0000',
  `T003_IPV_Cliente` decimal(10,4) NOT NULL default '0.0000',
  `T003_IPV_Data_Liberado` datetime default NULL,
  `T003_IPV_Liberado` decimal(10,4) NOT NULL default '0.0000',
  `T003_IPV_Normal` decimal(10,4) NOT NULL default '0.0000',
  `T003_IPV_Normal_Confirmado` decimal(10,4) NOT NULL default '0.0000',
  `T003_Valor_Total_Custo` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Custo_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Produtos` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Produtos_Sem_Desconto` decimal(10,2) default '0.00',
  `T003_Valor_Total_Produtos_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Produtos_Sem_Desconto_Confirmado` decimal(10,2) default '0.00',
  `T003_Valor_Total` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Custo_Final` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Custo_Final_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Percentual_Comissao` decimal(5,2) default '0.00',
  `T003_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T003_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Base_ICMS_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T003_Valor_Total_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_ICMS_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T003_Valor_Total_Base_IPI` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_Base_IPI_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T003_Valor_Total_IPI` decimal(10,2) NOT NULL default '0.00',
  `T003_Valor_Total_IPI_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T003_Aliquota_ISS` decimal(5,2) default '0.00',
  `T003_Valor_ISS` decimal(10,2) default '5.00',
  `T003_Peso_Total` decimal(10,3) default '0.000',
  `T003_Peso_Total_Confirmado` decimal(10,3) default '0.000',
  `T003_Observacao` text,
  `T003_Observacao_Orcamento_Perdido` text,
  `T003_Valor_Entrada` decimal(10,2) default '0.00',
  `T003_Numero_Parcelas` int(2) default '0',
  `T003_Numero_Dias_Entrada` int(3) default '0',
  `T003_Numero_Dias` int(3) default '0',
  `T003_Valor_Dolar` decimal(10,4) default '0.0000',
  `T003_Telefone` varchar(40) default NULL,
  `T003_Flag_Expedicao` int(2) default NULL,
  `T003_Flag_Lista_Preco` varchar(1) default ' ',
  `T003_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `T003_ST_VA` decimal(10,2) default '0.00',
  `T003_Valor_Base_ST` decimal(10,2) default '0.00',
  `T003_Valor_ST` decimal(10,2) default '0.00',
  `T003_Valor_Base_ST_Confirmado` decimal(10,2) default '0.00',
  `T003_Valor_ST_Confirmado` decimal(10,2) default '0.00',
  `T003_ICMS_Inter_Estadual` decimal(5,2) default '0.00',
  `T003_Flag_Alcada_Vendedor` varchar(1) default ' ',
  `T003_Flag_Alcada` varchar(1) default ' ',
  `T003_Liberou_Alcada_C007_Id` int(11) default NULL,
  `T003_Liberou_Alcada_Data` datetime default '0000-00-00 00:00:00',
  `T003_Liberou_Alcada_IPV` decimal(10,4) default '0.0000',
  `T003_Liberou_Alcada_Observacao_Nao_Liberado` text,
  `T003_C007_Id_Liberou_Alcada` int(11) default NULL,
  `T003_C007_Id_Liberou_Alcada_Data` datetime default '0000-00-00 00:00:00',
  `T003_C007_Id_Liberou_Alcada_IPV` decimal(10,4) default '0.0000',
  PRIMARY KEY  (`T003_Id`),
  KEY `T003_Index_Id` (`T003_Id`),
  KEY `T003_IDX_ESTADO_CLIENTE` (`T003_D018_Id`),
  KEY `T003_IDX_CONTATOS` (`T003_D013_Id`),
  KEY `T003_IDX_CLIENTE` (`T003_D024_Id`),
  KEY `T003_IDX_NATUREZA_OPERACAO` (`T003_D006_Id`),
  KEY `T003_IDX_VENDEDOR_INTERNO` (`T003_C007_Id_Vendedor_Interno`),
  KEY `T003_IDX_VENDEDOR_EXTERNO` (`T003_C007_Id_Vendedor_Externo`),
  KEY `T003_IDX_VENDEDOR_IPV_LIBERADO` (`T003_C007_Id_IPV_Liberado`),
  KEY `T003_IDX_REPRESENTANTE` (`T003_D021_Id`),
  KEY `T003_IDX_TRANSPORTADORA` (`T003_D022_Id`),
  KEY `T003_IDX_TIPO_TRANSPORTE` (`T003_D036_Id`),
  KEY `T003_IDX_CODIGO_USUARIO` (`T003_Codigo_Usuario`),
  KEY `T003_IDX_CODIGO_CLIENTE` (`T003_Codigo_Cliente`),
  KEY `T003_IDX_CODIGO_NATUREZA` (`T003_Codigo_Natureza`),
  KEY `T003_IDX_CODIGO_TRANSPORTADORA` (`T003_Codigo_Transportadora`),
  KEY `T003_IDX_CODIGO_REPRESENTANTE` (`T003_Codigo_Representante`),
  KEY `T003_IDX_EMPRESAS` (`T003_C004_Id`),
  KEY `T003_IDX_MOTIVO` (`T003_D047_Id`),
  KEY `T003_IDX_ORCAMENTO_VENDA` (`T003_Numero_Orcamento_Venda`),
  KEY `T003_IDX_ORCAMENTO_INTERNET` (`T003_T064_Id`),
  KEY `T003_IDX_DATA_EMISSAO` (`T003_Data_Emissao`),
  KEY `T003_IDX_PORTADOR_ENTRADA` (`T003_D027_Id_Entrada`),
  KEY `T003_IDX_PORTADOR` (`T003_D027_Id`),
  KEY `IDX_C008` (`T003_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t003`
--

LOCK TABLES `t003` WRITE;
/*!40000 ALTER TABLE `t003` DISABLE KEYS */;
/*!40000 ALTER TABLE `t003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t004`
--

DROP TABLE IF EXISTS `t004`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t004` (
  `T004_Id` int(10) unsigned NOT NULL auto_increment,
  `T004_D005_Id` int(10) unsigned NOT NULL default '0',
  `T004_D037_Id` int(10) unsigned NOT NULL default '0',
  `T004_D006_Id` int(10) unsigned NOT NULL default '0',
  `T004_T003_Id` int(10) unsigned default NULL,
  `T004_D024_Id` int(10) unsigned NOT NULL default '0',
  `T004_D009_Id` int(10) unsigned NOT NULL default '0',
  `T004_D004_Id` int(10) unsigned NOT NULL default '0',
  `T004_D040_Id` int(10) unsigned NOT NULL default '0',
  `T004_C004_Id` int(10) unsigned NOT NULL default '0',
  `T004_Item` int(4) NOT NULL default '0',
  `T004_Quantidade` decimal(10,2) default NULL,
  `T004_Descricao_Produto` varchar(50) default NULL,
  `T004_Observacao_Entrega` varchar(30) default NULL,
  `T004_Valor_Preco_Unitario` decimal(15,5) default '0.00000',
  `T004_Valor_Preco_Unitario_Confirmado` decimal(15,5) default '0.00000',
  `T004_Valor_Custo_Unitario` decimal(19,2) NOT NULL default '0.00',
  `T004_Valor_Preco_Sem_Desconto_Unitario` decimal(15,5) default '0.00000',
  `T004_Valor_Desconto_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T004_Valor_Desconto_Unitario_Confirmado` decimal(19,5) NOT NULL default '0.00000',
  `T004_Valor_Despesas_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T004_Valor_Despesas_Unitario_Confirmado` decimal(19,5) NOT NULL default '0.00000',
  `T004_Valor_Custo_Final_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T004_Valor_Custo_Final_Unitario_Confirmado` decimal(19,5) NOT NULL default '0.00000',
  `T004_Quantidade_Confirmada` decimal(10,2) default NULL,
  `T004_Numero_Orcamento_Venda` varchar(10) default NULL,
  `T004_Codigo_Produto` varchar(30) default NULL,
  `T004_Codigo_Substituto` varchar(25) default NULL,
  `T004_Codigo_Fornecedor` varchar(6) default NULL,
  `T004_Codigo_Local` varchar(10) default NULL,
  `T004_Valor_Total_Custo` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Preco_Sem_Desconto` decimal(20,2) default '0.00',
  `T004_Valor_Total_Custo_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Preco` decimal(20,2) default '0.00',
  `T004_Valor_Total_Preco_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Preco_Sem_Desconto_Confirmado` decimal(10,2) default '0.00',
  `T004_Valor_Total_Desconto` decimal(20,10) default '0.0000000000',
  `T004_Valor_Total_Desconto_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Despesas` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Despesas_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Custo_Final` decimal(10,2) NOT NULL default '0.00',
  `T004_Valor_Total_Custo_Final_Confirmado` decimal(10,2) NOT NULL default '0.00',
  `T004_IPV` decimal(10,4) NOT NULL default '0.0000',
  `T004_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T004_Valor_Base_ICMS` decimal(10,2) default '0.00',
  `T004_Valor_Base_ICMS_Confirmado` decimal(10,2) default '0.00',
  `T004_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T004_Valor_ICMS` decimal(10,2) default '0.00',
  `T004_Valor_ICMS_Confirmado` decimal(10,2) default '0.00',
  `T004_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T004_Valor_Base_IPI` decimal(10,2) default '0.00',
  `T004_Valor_Base_IPI_Confirmado` decimal(10,2) default '0.00',
  `T004_Aliquota_IPI` decimal(5,2) default '0.00',
  `T004_Valor_IPI` decimal(10,2) default '0.00',
  `T004_Valor_IPI_Confirmado` decimal(10,2) default '0.00',
  `T004_Marca` varchar(20) default NULL,
  `T004_Peso_Unitario` decimal(10,3) default '0.000',
  `T004_Peso_Total` decimal(10,3) default '0.000',
  `T004_Peso_Total_Confirmado` decimal(10,3) default '0.000',
  `T004_Ultimo_Preco` varchar(250) default NULL,
  `T004_Ultimo_Preco_Confirmado` varchar(250) default NULL,
  `T004_Flag_Excluir` int(1) default '2',
  `T004_Flag_Preco_Especial` varchar(1) default NULL,
  `T004_Classificacao_Fiscal` varchar(12) default NULL,
  `T004_Valor_Ultimo_Preco_Consulta` decimal(10,2) default '0.00',
  `T004_Valor_Ultimo_Preco_Confirmado` decimal(10,2) default '0.00',
  `T004_Data_Ultimo_Preco_Consulta` date default NULL,
  `T004_Data_Ultimo_Preco_Confirmado` date default NULL,
  `T004_Obs_Preco` varchar(30) default NULL,
  `T004_Observacao` text,
  `T004_Flag_Comprar` varchar(1) default NULL,
  `T004_Flag_ST` varchar(1) default 'N',
  `T004_ICMS_Inter_Estadual` decimal(5,2) default '0.00',
  `T004_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `T004_ST_VA` decimal(10,2) default '0.00',
  `T004_Valor_Base_ST` decimal(10,2) default '0.00',
  `T004_Valor_ST` decimal(10,2) default '0.00',
  `T004_Valor_Base_ST_Confirmado` decimal(10,2) default '0.00',
  `T004_Valor_ST_Confirmado` decimal(10,2) default '0.00',
  `T004_Flag_Situacao_Tributaria` varchar(3) default NULL,
  `T004_Situacao_Tributaria_PIS` varchar(2) default '01',
  `T004_Flag_Isento_PIS` enum('S','N') default 'N',
  `T004_Valor_Base_PIS` decimal(20,2) default '0.00',
  `T004_Aliquota_PIS` decimal(5,2) default '0.00',
  `T004_Valor_PIS` decimal(10,2) default '0.00',
  `T004_Situacao_Tributaria_COFINS` varchar(2) default '01',
  `T004_Flag_Isento_COFINS` enum('S','N') default 'N',
  `T004_Valor_Base_COFINS` decimal(20,2) default '0.00',
  `T004_Aliquota_COFINS` decimal(5,2) default '0.00',
  `T004_Valor_COFINS` decimal(10,2) default '0.00',
  `T004_Reordenar` int(1) default '1',
  `T004_D082_Id` int(10) default NULL,
  `T004_Valor_Preco_Original` decimal(10,2) default NULL,
  `T004_Percentual_Desconto` decimal(10,4) default NULL,
  `T004_Flag_Cancelado` varchar(1) default ' ',
  PRIMARY KEY  (`T004_Id`),
  KEY `T004_IDX_PRODUTO_ESTOQUE` (`T004_D009_Id`),
  KEY `T004_IDX_CLASSIFICACAO_FISCAL` (`T004_D005_Id`),
  KEY `T004_IDX_UNIDADE` (`T004_D037_Id`),
  KEY `T004_IDX_NATUREZA` (`T004_D006_Id`),
  KEY `T004_IDX_LOCAL` (`T004_D004_Id`),
  KEY `T004_IDX_MAQUINAS` (`T004_D040_Id`),
  KEY `T004_IDX_FORNECEDOR` (`T004_D024_Id`),
  KEY `T004_IDX_ORCAMENTO_VENDA` (`T004_T003_Id`),
  KEY `T004_IDX_CODIGO_PRODUTO` (`T004_Codigo_Produto`),
  KEY `T004_IDX_CODIGO_FORNECEDOR` (`T004_Codigo_Fornecedor`),
  KEY `T004_IDX_CODIGO_LOCAL` (`T004_Codigo_Local`),
  KEY `T004_IDX_EMPRESAS` (`T004_C004_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t004`
--

LOCK TABLES `t004` WRITE;
/*!40000 ALTER TABLE `t004` DISABLE KEYS */;
/*!40000 ALTER TABLE `t004` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t005`
--

DROP TABLE IF EXISTS `t005`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t005` (
  `T005_Id` int(10) unsigned NOT NULL auto_increment,
  `T005_T064_Id` bigint(20) unsigned default NULL,
  `T005_C008_Id` int(11) default NULL,
  `T005_D027_Id_Entrada` int(10) unsigned default NULL,
  `T005_D027_Id` int(10) unsigned default NULL,
  `T005_D047_Id` int(10) unsigned default NULL,
  `T005_D024_Id` int(10) unsigned default NULL,
  `T005_D021_Id` int(10) unsigned default NULL,
  `T005_D022_Id` int(10) unsigned default NULL,
  `T005_D036_Id` int(10) unsigned default NULL,
  `T005_T003_Id` int(10) unsigned default NULL,
  `T005_D006_Id` int(10) unsigned default NULL,
  `T005_C007_Id_Vendedor_Interno` int(10) unsigned default NULL,
  `T005_C007_Id_Vendedor_Externo` int(10) unsigned default NULL,
  `T005_C007_Id_IPV_Liberado` int(10) unsigned default NULL,
  `T005_C007_Id_Devolucao` int(5) default '0',
  `T005_C004_Id` int(11) NOT NULL default '0',
  `T005_Numero_Pedido_Venda` int(10) default NULL,
  `T005_Data_Emissao` date default NULL,
  `T005_Data_Saida` date default '0000-00-00',
  `T005_Hora_Saida` time default '00:00:00',
  `T005_Data_Entrega` date default NULL,
  `T005_Valor_Desconto` decimal(10,2) NOT NULL default '0.00',
  `T005_Percentual_Desconto` decimal(30,20) default NULL,
  `T005_Valor_Percentual_Desconto` decimal(10,2) NOT NULL default '0.00',
  `T005_Observacao_1` text,
  `T005_Observacao_2` varchar(70) default NULL,
  `T005_Observacao_3` varchar(70) default NULL,
  `T005_Prazos_Pagamentos` varchar(40) default NULL,
  `T005_Nome_Vendedor` varchar(30) default NULL,
  `T005_Mao_Obra_1` text,
  `T005_Mao_Obra_2` text,
  `T005_Mao_Obra_3` text,
  `T005_Mao_Obra_4` text,
  `T005_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `T005_Valor_Mao_Obra` decimal(10,2) NOT NULL default '0.00',
  `T005_NF_ACP` varchar(1) default NULL,
  `T005_Valor_Dolar` decimal(10,4) NOT NULL default '0.0000',
  `T005_Flag_ACP` varchar(1) default NULL,
  `T005_Prazo_1` int(4) NOT NULL default '0',
  `T005_Prazo_2` int(4) NOT NULL default '0',
  `T005_Prazo_3` int(4) NOT NULL default '0',
  `T005_Prazo_4` int(4) NOT NULL default '0',
  `T005_Prazo_5` int(4) NOT NULL default '0',
  `T005_Prazo_6` int(4) NOT NULL default '0',
  `T005_Prazo_7` int(4) NOT NULL default '0',
  `T005_Flag_Frete` int(1) default NULL,
  `T005_NF_Observacao` text,
  `T005_Numero_NF_Devolucao` varchar(12) default NULL,
  `T005_Data_Emissao_NF_Devolucao` date default NULL,
  `T005_Aliquota_ICMS_NF_Devolucao` decimal(5,2) NOT NULL default '0.00',
  `T005_Valor_Frete` decimal(19,2) NOT NULL default '0.00',
  `T005_Flag_Situacao` varchar(1) default NULL,
  `T005_Valor_Despesas_Acessorias` decimal(10,2) NOT NULL default '0.00',
  `T005_Valor_Desconto_NF` decimal(12,6) NOT NULL default '0.000000',
  `T005_Valor_Encargos_NF` decimal(12,6) NOT NULL default '0.000000',
  `T005_Observacao_Nota_Fiscal` text,
  `T005_Numero_Orcamento` int(10) default NULL,
  `T005_Codigo_Cliente` varchar(6) default NULL,
  `T005_Codigo_Representante` varchar(6) default NULL,
  `T005_Codigo_Transportadora` varchar(6) default NULL,
  `T005_Codigo_Natureza` int(5) NOT NULL default '0',
  `T005_Codigo_Usuario` int(4) NOT NULL default '0',
  `T005_IPV` decimal(10,4) NOT NULL default '0.0000',
  `T005_IPV_Cliente` decimal(10,4) NOT NULL default '0.0000',
  `T005_IPV_Data_Liberado` datetime default NULL,
  `T005_IPV_Liberado` decimal(10,4) NOT NULL default '0.0000',
  `T005_IPV_Normal` decimal(10,4) NOT NULL default '0.0000',
  `T005_Valor_Total_Custo` decimal(10,2) NOT NULL default '0.00',
  `T005_Valor_Total_Produtos` decimal(10,2) NOT NULL default '0.00',
  `T005_Valor_Total_Produtos_Sem_Desconto` decimal(10,2) default '0.00',
  `T005_Valor_Total_Custo_Final` decimal(10,2) NOT NULL default '0.00',
  `T005_Valor_Total` decimal(10,2) NOT NULL default '0.00',
  `T005_Percentual_Comissao` decimal(5,2) default '0.00',
  `T005_Flag_Travar_Pedido` int(1) default '2',
  `T005_Flag_Almoxarifado` varchar(1) default 'N',
  `T005_Flag_Status` int(2) default '0',
  `T005_Flag_Devolucao` varchar(1) default 'N',
  `T005_Flag_Tipo_Devolucao` varchar(10) default NULL,
  `T005_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T005_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T005_Aliquota_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T005_Valor_Total_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T005_Valor_Base_ICMS_Substituicao` decimal(14,2) default '0.00',
  `T005_Valor_ICMS_Substituicao` decimal(14,2) default '0.00',
  `T005_Valor_ICMS_Substituicao_Retencao` decimal(14,2) default '0.00',
  `T005_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T005_Valor_Total_Base_IPI` decimal(10,2) NOT NULL default '0.00',
  `T005_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T005_Valor_Total_IPI` decimal(10,2) NOT NULL default '0.00',
  `T005_Aliquota_ISS` decimal(5,2) default '0.00',
  `T005_Valor_ISS` decimal(10,2) default '0.00',
  `T005_Valor_Total_PIS` decimal(10,2) default '0.00',
  `T005_Valor_Total_COFINS` decimal(10,2) default '0.00',
  `T005_Data_Separacao_Pecas` datetime default NULL,
  `T005_Usuario_Pecas` int(11) default NULL,
  `T005_Liberacao_Credito_Id` int(11) default '0',
  `T005_Peso_Total` decimal(10,3) default '0.000',
  `T005_Observacao` text,
  `T005_Fax` varchar(30) default NULL,
  `T005_Nome_Comprador` varchar(30) default NULL,
  `T005_E_mail_Comprador` varchar(60) default NULL,
  `T005_Prazo_Entrega` varchar(40) default NULL,
  `T005_Observacao_Pedido_Perdido` text,
  `T005_Nome_Transportadora` varchar(35) default NULL,
  `T005_Transportadora_Placa_Veiculo` varchar(8) default NULL,
  `T005_Estado_Veiculo` char(2) default NULL,
  `T005_Valor_Entrada` decimal(10,2) default '0.00',
  `T005_Numero_Parcelas` int(2) default '0',
  `T005_Numero_Dias_Entrada` int(3) default '0',
  `T005_Numero_Dias` int(3) default '0',
  `T005_Quantidade_Volumes` int(15) unsigned default '0',
  `T005_Especie` varchar(20) default NULL,
  `T005_Marca` varchar(20) default NULL,
  `T005_Numero_Caixas` varchar(20) default NULL,
  `T005_Peso_Liquido` decimal(10,3) default '0.000',
  `T005_Peso_Bruto` decimal(10,3) default '0.000',
  `T005_Flag_Considerar_Dias_Entrada` varchar(1) default 'S',
  `T005_Motivo_Nao_Liberado` tinytext,
  `T005_Flag_Reenviado_Almoxarifado` enum('S','N') default 'N',
  `T005_Alteracoes_Reenviado_Almoxarifado` text,
  `T005_Data_Hora_Almoxarifado` datetime default NULL,
  `T005_Flag_Separacao` varchar(1) default NULL,
  `T005_Flag_Separacao_Liberar` varchar(1) default NULL,
  `T005_Numero_Nota_Manual` varchar(20) default NULL,
  `T005_Flag_Expedicao` int(2) default NULL,
  `T005_Flag_Dividido` varchar(1) default NULL,
  `T005_Observacao_Separacao` varchar(100) default NULL,
  `T005_Flag_Lista_Preco` varchar(1) default ' ',
  `T005_Flag_Aguardar_Liberacao` int(1) default '0',
  `T005_Observacao_Comercial` text,
  `T005_ST_VA` decimal(10,2) default '0.00',
  `T005_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `T005_ICMS_Inter_Estadual` decimal(5,2) default '0.00',
  `T005_Origem_UF` char(2) default NULL,
  `T005_Origem_Ano_Mes` char(4) default NULL,
  `T005_Origem_Cnpj` varchar(14) default NULL,
  `T005_Origem_Modelo` char(2) default NULL,
  `T005_Origem_Serie` int(3) default NULL,
  `T005_Origem_Chave_Acesso` varchar(44) default NULL,
  `T005_Flag_Imprimir_Romaneio` enum('S','N') default 'N',
  `T005_Data_Impressao_Romaneio` datetime default '0000-00-00 00:00:00',
  `T005_Flag_Importar_Pendente` enum('S','N') default 'N',
  `T005_Estoque_Box` varchar(20) default ' ',
  PRIMARY KEY  (`T005_Id`),
  KEY `T005_Index_Id` (`T005_Id`),
  KEY `T005_Index_C004_Id` (`T005_C004_Id`),
  KEY `T005_Index_Clientes` (`T005_D024_Id`),
  KEY `T005_Index_Natureza_Operacao` (`T005_D006_Id`),
  KEY `T005_Index_Vendedor_Interno` (`T005_C007_Id_Vendedor_Interno`),
  KEY `T005_Index_Vendedor_Externo` (`T005_C007_Id_Vendedor_Externo`),
  KEY `T005_Index_IPV_Liberado` (`T005_C007_Id_IPV_Liberado`),
  KEY `T005_Index_Revenda` (`T005_D021_Id`),
  KEY `T005_Index_Transportadora` (`T005_D022_Id`),
  KEY `T005_Index_Tipo_Transporte` (`T005_D036_Id`),
  KEY `T005_Index_Numero_Pedido_Venda` (`T005_Numero_Pedido_Venda`),
  KEY `T005_Index_Orcamento_Venda` (`T005_T003_Id`),
  KEY `T005_Index_Motivo` (`T005_D047_Id`),
  KEY `T005_Index_DATA_EMISSAO` (`T005_Data_Emissao`),
  KEY `T005_Index_SITUACAO` (`T005_Flag_Situacao`),
  KEY `T005_Index_PORTADOR_ENTRADA` (`T005_D027_Id_Entrada`),
  KEY `T005_Index_PORTADOR` (`T005_D027_Id`),
  KEY `IDX_C008` (`T005_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t005`
--

LOCK TABLES `t005` WRITE;
/*!40000 ALTER TABLE `t005` DISABLE KEYS */;
/*!40000 ALTER TABLE `t005` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t006`
--

DROP TABLE IF EXISTS `t006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t006` (
  `T006_Id` int(10) unsigned NOT NULL auto_increment,
  `T006_T008_Id` int(10) unsigned default NULL,
  `T006_T006_Id` int(10) unsigned default NULL,
  `T006_D005_Id` int(10) unsigned default NULL,
  `T006_D037_Id` int(10) unsigned default NULL,
  `T006_D006_Id` int(10) unsigned default NULL,
  `T006_T004_Id` int(10) unsigned default NULL,
  `T006_D024_Id` int(10) unsigned default NULL,
  `T006_T005_Id` int(10) unsigned default NULL,
  `T006_D009_Id` int(10) unsigned default NULL,
  `T006_D004_Id` int(10) unsigned default NULL,
  `T006_D040_Id` int(10) unsigned default NULL,
  `T006_T014_Id` int(10) unsigned default NULL,
  `T006_C004_Id` int(10) unsigned NOT NULL default '0',
  `T006_Item` int(4) NOT NULL default '0',
  `T006_Numero_Orcamento` varchar(10) default NULL,
  `T006_Quantidade` decimal(10,2) default NULL,
  `T006_Data_Entrega` date default NULL,
  `T006_Valor_Custo_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T006_Valor_Preco_Sem_Desconto_Unitario` decimal(19,5) default '0.00000',
  `T006_Valor_Desconto_Unitario` decimal(20,10) default '0.0000000000',
  `T006_Valor_Despesas_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T006_Valor_Custo_Final_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T006_Valor_Frete_Unitario` decimal(20,10) default '0.0000000000',
  `T006_Valor_Despesas_Acessorias_Unitario` decimal(20,10) default '0.0000000000',
  `T006_Valor_Preco_Unitario` decimal(19,5) NOT NULL default '0.00000',
  `T006_Descricao_Produto` varchar(60) default NULL,
  `T006_Codigo_Substituto` varchar(18) default NULL,
  `T006_Flag_Status` varchar(1) default NULL,
  `T006_Numero_Pedido_Venda` varchar(6) default NULL,
  `T006_Codigo_Fornecedor` varchar(6) default NULL,
  `T006_Codigo_Local` varchar(10) default NULL,
  `T006_Codigo_Produto` varchar(30) default NULL,
  `T006_Valor_Total_Custo` decimal(10,2) NOT NULL default '0.00',
  `T006_Valor_Total_Preco_Sem_Desconto` decimal(20,2) default '0.00',
  `T006_Valor_Total_Preco` decimal(20,2) default '0.00',
  `T006_Valor_Total_Desconto` decimal(20,10) default '0.0000000000',
  `T006_Valor_Total_Despesas` decimal(10,2) NOT NULL default '0.00',
  `T006_Valor_Total_Custo_Final` decimal(10,2) NOT NULL default '0.00',
  `T006_Valor_Total_Frete` decimal(20,10) default '0.0000000000',
  `T006_Valor_Total_Despesas_Acessorias` decimal(20,10) default '0.0000000000',
  `T006_IPV` decimal(10,4) default NULL,
  `T006_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T006_Valor_Base_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T006_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T006_Valor_ICMS` decimal(20,5) default '0.00000',
  `T006_Valor_Agregado` decimal(20,2) default '0.00',
  `T006_Valor_Base_ICMS_Substituicao` decimal(20,2) default '0.00',
  `T006_Percentual_Reducao_ICMS_ST` decimal(5,2) default '0.00',
  `T006_Aliquota_ICMS_ST` decimal(5,2) default '0.00',
  `T006_Valor_ICMS_Substituicao` decimal(20,2) default '0.00',
  `T006_Valor_ICMS_Substituicao_Retencao` decimal(20,2) default '0.00',
  `T006_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T006_Valor_Base_IPI` decimal(10,2) NOT NULL default '0.00',
  `T006_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T006_Valor_IPI` decimal(10,2) NOT NULL default '0.00',
  `T006_Marca` varchar(20) default NULL,
  `T006_Peso_Unitario` decimal(10,3) default '0.000',
  `T006_Peso_Total` decimal(10,3) default '0.000',
  `T006_Ultimo_Preco` varchar(50) default NULL,
  `T006_Ultimo_Preco_Confirmado` varchar(50) default NULL,
  `T006_Flag_Excluir` int(1) default '2',
  `T006_Flag_Preco_Especial` varchar(1) default NULL,
  `T006_Classificacao_Fiscal` varchar(12) default NULL,
  `T006_Flag_Comprar` varchar(1) default NULL,
  `T006_T006_Id_Dividir` int(10) default NULL,
  `T006_Quantidade_Dividir` decimal(10,2) default NULL,
  `T006_Quantidade_Separacao` decimal(10,2) default NULL,
  `T006_Quantidade_Dividido` decimal(10,2) default NULL,
  `T006_Flag_ST` varchar(1) default 'N',
  `T006_ICMS_Inter_Estadual` decimal(5,2) default '0.00',
  `T006_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `T006_ST_VA` decimal(10,2) default '0.00',
  `T006_Situacao_Tributaria_PIS` varchar(2) default '01',
  `T006_Flag_Isento_PIS` enum('S','N') default 'N',
  `T006_Valor_Base_PIS` decimal(20,2) default '0.00',
  `T006_Aliquota_PIS` decimal(5,2) default '0.00',
  `T006_Valor_PIS` decimal(10,2) default '0.00',
  `T006_Situacao_Tributaria_COFINS` varchar(2) default '01',
  `T006_Flag_Isento_COFINS` enum('S','N') default 'N',
  `T006_Valor_Base_COFINS` decimal(20,2) default '0.00',
  `T006_Aliquota_COFINS` decimal(5,2) default '0.00',
  `T006_Valor_COFINS` decimal(10,2) default '0.00',
  `T006_Flag_Situacao_Tributaria` varchar(3) default NULL,
  `T006_T099_Id` int(11) default '0',
  `T006_Reordenar` int(1) default '1',
  `T006_Quantidade_Comprar` decimal(10,3) default '0.000',
  `T006_Flag_Tipo_Produto` varchar(2) default NULL,
  `T006_D049_Id` int(11) unsigned default NULL,
  PRIMARY KEY  (`T006_Id`),
  KEY `T006_IDX_UNIDADE` (`T006_D037_Id`),
  KEY `T006_IDX_ITENS_PEDIDO_VENDA` (`T006_T006_Id`),
  KEY `T006_IDX_CLASSIFICACAO_FISCAL` (`T006_D005_Id`),
  KEY `T006_IDX_NATUREZA` (`T006_D006_Id`),
  KEY `T006_IDX_PRODUTO_ESTOQUE` (`T006_D009_Id`),
  KEY `T006_IDX_LOCAL` (`T006_D004_Id`),
  KEY `T006_IDX_MAQUINAS` (`T006_D040_Id`),
  KEY `T006_IDX_ITENS_ORCAMENTO` (`T006_T004_Id`),
  KEY `T006_IDX_PEDIDO_VENDA` (`T006_T005_Id`),
  KEY `T006_IDX_FORNECEDOR` (`T006_D024_Id`),
  KEY `T006_IDX_CODIGO_PEDIDO_VENDA` (`T006_Numero_Pedido_Venda`),
  KEY `T006_IDX_CODIGO_FORNECEDOR` (`T006_Codigo_Fornecedor`),
  KEY `T006_IDX_CODIGO_LOCAL` (`T006_Codigo_Local`),
  KEY `T006_IDX_CODIGO_PRODUTO` (`T006_Codigo_Produto`),
  KEY `T006_IDX_ITENS_NOTA_RECEBIDA` (`T006_T014_Id`),
  KEY `T006_IDX_ITENS_NOTA_EMITIDA` (`T006_T008_Id`),
  KEY `T006_IDX_EMPRESAS` (`T006_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t006`
--

LOCK TABLES `t006` WRITE;
/*!40000 ALTER TABLE `t006` DISABLE KEYS */;
/*!40000 ALTER TABLE `t006` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t007`
--

DROP TABLE IF EXISTS `t007`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t007` (
  `T007_Id` int(10) unsigned NOT NULL auto_increment,
  `T007_C008_Id` int(10) unsigned default '0',
  `T007_T007_Id_Nota_Complementar` int(10) unsigned default NULL,
  `T007_D027_Id` int(10) unsigned default NULL,
  `T007_D027_Id_Entrada` int(10) unsigned default NULL,
  `T007_T005_Id` int(10) unsigned default NULL,
  `T007_T054_Id` int(10) unsigned default NULL,
  `T007_D024_Id` int(10) unsigned default NULL,
  `T007_D022_Id` int(10) unsigned default NULL,
  `T007_D036_Id` int(10) unsigned default NULL,
  `T007_D021_Id` int(10) unsigned default NULL,
  `T007_D006_Id` int(10) unsigned default NULL,
  `T007_D016_Id` int(10) unsigned default NULL,
  `T007_D044_Id` int(10) unsigned default NULL,
  `T007_C004_Id` int(11) NOT NULL default '0',
  `T007_C007_Id_Vendedor_Externo` int(11) NOT NULL default '0',
  `T007_C007_Id_Vendedor_Interno` int(11) NOT NULL default '0',
  `T007_C007_Id_Devolucao` int(10) default NULL,
  `T007_C007_Id` int(11) NOT NULL default '0',
  `T007_Flag_Cancelada` varchar(1) default NULL,
  `T007_Flag_Conferencia_Frete` varchar(1) default 'N',
  `T007_Valor_Frete_Nao_Cobrado` decimal(10,2) default '0.00',
  `T007_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T007_Data_Hora_Protocolo_Nfe` datetime default NULL,
  `T007_Data_Hora_Cancelamento_Nfe` datetime default NULL,
  `T007_Numero_Protocolo_Nfe` varchar(20) default NULL,
  `T007_Numero_Protocolo_Cancelamento_Nfe` varchar(20) default NULL,
  `T007_Chave_Acesso_Nfe` varchar(44) default NULL,
  `T007_Protocolo_Inutilizacao` varchar(20) default NULL,
  `T007_Data_Hora_Inutilizacao` datetime default NULL,
  `T007_Justificativa_Inutilizacao` text,
  `T007_Ambiente_NFe` enum('1','2') default '2',
  `T007_Flag_Contingencia` enum('S','N') default 'N',
  `T007_Flag_Finalidade_NFe` enum('1','2','3') default '1',
  `T007_Flag_Denegado` enum('S','N') default 'N',
  `T007_Flag_Inutilizado` enum('S','N') default 'N',
  `T007_Data_Hora_Recebimento_Nfe` datetime default NULL,
  `T007_Numero_Recibo_Nfe` varchar(20) default NULL,
  `T007_Numero_Nota_Devolvida` varchar(50) default NULL,
  `T007_Data_Emissao` date default NULL,
  `T007_Data_Saida` date default '0000-00-00',
  `T007_Data_Vencimento` date default NULL,
  `T007_Numero_Total_Parcelas` int(2) default NULL,
  `T007_Codigo_CFOP` varchar(30) default NULL,
  `T007_Descricao_CFOP` varchar(30) default NULL,
  `T007_Nome_Transportadora` varchar(30) default NULL,
  `T007_Tipo_Transporte` varchar(10) default NULL,
  `T007_Observacao_1` text,
  `T007_Observacao_2` varchar(60) default NULL,
  `T007_Observacao_3` varchar(60) default NULL,
  `T007_Flag_Frete` int(1) default NULL,
  `T007_Observacao_Nota_Fiscal_1` text,
  `T007_Valor_Frete` decimal(19,2) NOT NULL default '0.00',
  `T007_Valor_Total_Custo` decimal(14,2) NOT NULL default '0.00',
  `T007_Valor_Total_Preco` decimal(14,2) NOT NULL default '0.00',
  `T007_Valor_Total_Produtos` decimal(14,2) NOT NULL default '0.00',
  `T007_Valor_Total_Produtos_Sem_Desconto` decimal(10,2) default '0.00',
  `T007_Valor_Total_Custo_Final` decimal(20,2) NOT NULL default '0.00',
  `T007_Valor_Total` decimal(14,2) NOT NULL default '0.00',
  `T007_Prazo_Parcelas` varchar(30) default NULL,
  `T007_Percentual_Comissao_Representante` decimal(14,2) NOT NULL default '0.00',
  `T007_Valor_Despesas_Acessorias` decimal(10,2) NOT NULL default '0.00',
  `T007_Valor_Desconto` decimal(12,6) NOT NULL default '0.000000',
  `T007_Percentual_Desconto` decimal(7,4) default '0.0000',
  `T007_Valor_Percentual_Desconto` decimal(10,2) NOT NULL default '0.00',
  `T007_Valor_Dolar` decimal(10,4) NOT NULL default '0.0000',
  `T007_Observacao_Nota_Fiscal_2` text,
  `T007_Codigo_Cliente` varchar(6) default NULL,
  `T007_Codigo_Natureza` int(5) default NULL,
  `T007_Codigo_Representante` varchar(6) default NULL,
  `T007_Codigo_Transportadora` varchar(6) default NULL,
  `T007_Numero_Pedido_Venda` int(6) default NULL,
  `T007_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T007_Valor_Total_Base_ICMS_Impresso` decimal(20,5) default '0.00000',
  `T007_Valor_Total_Base_ICMS` decimal(20,5) default '0.00000',
  `T007_Valor_Total_Base_ICMS_Sem_Reducao` decimal(20,2) NOT NULL default '0.00',
  `T007_Valor_Total_Base_ICMS_Diferido` decimal(20,2) default '0.00',
  `T007_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T007_Valor_Total_ICMS` decimal(14,2) NOT NULL default '0.00',
  `T007_Valor_Base_ICMS_Substituicao` decimal(14,2) default '0.00',
  `T007_Valor_ICMS_Substituicao` decimal(14,2) NOT NULL default '0.00',
  `T007_Valor_ICMS_Substituicao_Retencao` decimal(14,2) default '0.00',
  `T007_Flag_IPI` varchar(1) default NULL,
  `T007_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T007_Valor_Total_Base_IPI` decimal(10,2) NOT NULL default '0.00',
  `T007_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T007_Valor_Total_IPI` decimal(14,4) NOT NULL default '0.0000',
  `T007_Cliente_Razao` varchar(50) default NULL,
  `T007_Cliente_CNPJ_CPF` varchar(20) default NULL,
  `T007_Cliente_Inscricao_Estadual` varchar(20) default NULL,
  `T007_Cliente_Inscricao_Suframa` varchar(9) default NULL,
  `T007_Cliente_Endereco` varchar(40) default NULL,
  `T007_Cliente_Endereco_Numero` varchar(15) default NULL,
  `T007_Cliente_Complemento` varchar(60) default NULL,
  `T007_Cliente_Bairro` varchar(25) default NULL,
  `T007_Cliente_CEP` varchar(9) default NULL,
  `T007_Cliente_Cidade` varchar(30) default NULL,
  `T007_Cliente_Codigo_Cidade_IBGE` int(7) default NULL,
  `T007_Cliente_Telefone` varchar(20) default NULL,
  `T007_Cliente_Estado` varchar(2) default NULL,
  `T007_Cliente_Codigo_Estado_IBGE` int(2) default NULL,
  `T007_Hora_Saida` time default '00:00:00',
  `T007_Transportadora_CNPJ` varchar(20) default NULL,
  `T007_Transportadora_Inscricao_Estadual` varchar(20) default NULL,
  `T007_Transportadora_Endereco` varchar(40) default NULL,
  `T007_Transportadora_Cidade` varchar(30) default NULL,
  `T007_Transportadora_Estado` varchar(2) default NULL,
  `T007_Transportadora_Placa_Veiculo` varchar(8) default NULL,
  `T007_Estado_Veiculo` char(2) default NULL,
  `T007_Quantidade_Volumes` int(15) unsigned default '0',
  `T007_Especie` varchar(20) default NULL,
  `T007_Marca` varchar(20) default NULL,
  `T007_Numero_Caixas` varchar(20) default NULL,
  `T007_Peso_Bruto` decimal(10,3) default '0.000',
  `T007_Peso_Liquido` decimal(10,3) default '0.000',
  `T007_Mao_Obra_1` varchar(100) default NULL,
  `T007_Mao_Obra_2` varchar(100) default NULL,
  `T007_Mao_Obra_3` varchar(100) default NULL,
  `T007_Mao_Obra_4` varchar(100) default NULL,
  `T007_Valor_Mao_Obra` decimal(10,2) default '0.00',
  `T007_Inscricao_Municipal` varchar(20) default NULL,
  `T007_Aliquota_ISS` decimal(5,2) default '0.00',
  `T007_Valor_ISS` decimal(10,2) default '0.00',
  `T007_Empresa_Usuaria_Nome` varchar(100) default NULL,
  `T007_Empresa_Usuaria_Endereco` varchar(34) default NULL,
  `T007_Empresa_Usuaria_Numero` varchar(5) default NULL,
  `T007_Empresa_Usuaria_Bairro` varchar(15) default NULL,
  `T007_Empresa_Usuaria_Cep` varchar(10) default NULL,
  `T007_Empresa_Usuaria_Cidade` varchar(30) default NULL,
  `T007_Empresa_Usuaria_Codigo_Cidade_IBGE` int(7) default NULL,
  `T007_Empresa_Usuaria_Uf` varchar(2) default NULL,
  `T007_Empresa_Usuaria_Estado` varchar(20) default NULL,
  `T007_Empresa_Usuaria_Codigo_Estado_IBGE` int(2) default NULL,
  `T007_Empresa_Usuaria_Pais` varchar(2) default 'BR',
  `T007_Empresa_Usuaria_Telefone` varchar(12) default NULL,
  `T007_Empresa_Usuaria_Fax` varchar(12) default NULL,
  `T007_Empresa_Usuaria_Cnpj` varchar(18) default NULL,
  `T007_Empresa_Usuaria_Inscricao_Estadual` varchar(14) default NULL,
  `T007_Empresa_Usuaria_CNAE_Fiscal` varchar(7) default NULL,
  `T007_IPV` decimal(10,4) default '0.0000',
  `T007_IPV_Cliente` decimal(10,4) default '0.0000',
  `T007_Flag_Impresso` varchar(1) default 'S',
  `T007_Flag_ACP` varchar(1) default NULL,
  `T007_Flag_Entrada_Saida` varchar(1) default NULL,
  `T007_Flag_Venda_Compra_Outros` varchar(1) default NULL,
  `T007_Flag_Revenda_Consumidor` varchar(1) default NULL,
  `T007_Flag_Travar_Nota` varchar(1) default 'S',
  `T007_Observacao_Cancelamento` text,
  `T007_Valor_Entrada` decimal(10,2) default '0.00',
  `T007_Numero_Parcelas` int(2) default '0',
  `T007_Numero_Dias_Entrada` int(3) default '0',
  `T007_Numero_Dias` int(3) default '0',
  `T007_Flag_OK_Fiscal` varchar(1) default NULL,
  `T007_Flag_Mao_Obra` varchar(10) default NULL,
  `T007_Numero_Conhecimento` varchar(20) default NULL,
  `T007_IPV_sem_icms` decimal(10,4) default NULL,
  `T007_Flag_Classificacao` int(2) default NULL,
  `T007_Arquivo_PIN` varchar(100) default NULL,
  `T007_Numero_Controle` varchar(20) NOT NULL,
  `T007_Serie` varchar(10) NOT NULL,
  `T007_Obs_Classificacao_Fiscal` text,
  `T007_Flag_Expedicao` varchar(1) default NULL,
  `T007_ST_VA` decimal(10,2) default '0.00',
  `T007_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `T007_Valor_Total_PIS` decimal(15,2) default '0.00',
  `T007_Valor_Total_COFINS` decimal(15,2) default '0.00',
  `T007_ICMS_Inter_Estadual` decimal(5,2) default '0.00',
  `T007_Flag_Inclusao_Manual` enum('S','N') default 'N',
  `T007_Origem_UF` char(2) default NULL,
  `T007_Origem_Ano_Mes` char(4) default NULL,
  `T007_Origem_Cnpj` varchar(14) default NULL,
  `T007_Origem_Modelo` char(2) default NULL,
  `T007_Origem_Serie` int(3) default NULL,
  `T007_Origem_Chave_Acesso` varchar(44) default NULL,
  `T007_Flag_SCAN` enum('S','N') default 'N',
  `T007_Flag_Almoxarifado` enum('S','N') default 'N',
  `T007_Flag_Imprimir_Romaneio` enum('S','N') default 'N',
  `T007_Total_Peso` decimal(15,5) default NULL,
  `T007_Data_Envio_XML` datetime default '0000-00-00 00:00:00',
  `T007_Valor_Total_Imposto_Importacao` decimal(19,2) default '0.00',
  PRIMARY KEY  (`T007_Id`),
  KEY `T007_Index_Id` (`T007_Id`),
  KEY `T007_Index_Flag_Cancelada` (`T007_Flag_Cancelada`),
  KEY `T007_Index_Clientes` (`T007_D024_Id`),
  KEY `T007_Index_C004_Id` (`T007_C004_Id`),
  KEY `T007_Index_Pedido_Venda` (`T007_T005_Id`),
  KEY `T007_Index_Importacao` (`T007_T054_Id`),
  KEY `T007_Index_Natureza_Operacao` (`T007_D006_Id`),
  KEY `T007_Index_Dolar` (`T007_D016_Id`),
  KEY `T007_Index_Representantes` (`T007_D021_Id`),
  KEY `T007_Index_Transportadoras` (`T007_D022_Id`),
  KEY `T007_Index_Tipo_Transporte` (`T007_D036_Id`),
  KEY `T007_Index_Numero_Nota` (`T007_Numero_Nota_Fiscal`),
  KEY `T007_Index_Data_Emissao` (`T007_Data_Emissao`),
  KEY `T007_index_Vendedor_Externo` (`T007_C007_Id_Vendedor_Externo`),
  KEY `T007_index_Vendedor_Interno` (`T007_C007_Id_Vendedor_Interno`),
  KEY `T007_index_Usuario` (`T007_C007_Id`),
  KEY `T007_index_Moeda_Importacao` (`T007_D044_Id`),
  KEY `T007_index_PORTADOR` (`T007_D027_Id`),
  KEY `T007_index_PORTADOR_ENTRADA` (`T007_D027_Id_Entrada`),
  KEY `T007_Flag_Classificacao` (`T007_Flag_Classificacao`),
  KEY `T007_Cliente_Estado` (`T007_Cliente_Estado`),
  KEY `T007_Numero_Controle` (`T007_Numero_Controle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t007`
--

LOCK TABLES `t007` WRITE;
/*!40000 ALTER TABLE `t007` DISABLE KEYS */;
/*!40000 ALTER TABLE `t007` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t008`
--

DROP TABLE IF EXISTS `t008`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t008` (
  `T008_Id` int(10) unsigned NOT NULL auto_increment,
  `T008_T055_Id` int(10) unsigned default NULL,
  `T008_D037_Id` int(10) unsigned default NULL,
  `T008_T006_Id` int(10) unsigned default NULL,
  `T008_T007_Id` int(10) unsigned default NULL,
  `T008_T008_Id` int(10) unsigned default NULL,
  `T008_T014_Id` int(10) unsigned default NULL,
  `T008_D009_Id` int(10) unsigned default NULL,
  `T008_D009_Id_Pendencia` int(10) unsigned default NULL,
  `T008_D005_Id` int(10) unsigned default NULL,
  `T008_D006_Id` int(10) unsigned default NULL,
  `T008_D040_Id` int(10) unsigned default NULL,
  `T008_C004_Id` int(11) NOT NULL default '0',
  `T008_D003_Id` int(11) default NULL,
  `T008_Quantidade` decimal(10,3) NOT NULL default '0.000',
  `T008_Unidade` varchar(5) default NULL,
  `T008_Codigo_CFOP` decimal(5,3) default '0.000',
  `T008_Flag_Entrada_Saida` varchar(1) default NULL,
  `T008_Data_Emissao` date default NULL,
  `T008_Nome_Transportadora` varchar(30) default NULL,
  `T008_Flag_Nacional_Importado` varchar(1) default NULL,
  `T008_Descricao_Produto` varchar(120) default NULL,
  `T008_Codigo_Substituto` varchar(18) default NULL,
  `T008_Valor_Custo_Unitario` decimal(20,4) NOT NULL default '0.0000',
  `T008_Valor_Total_Custo` decimal(20,2) NOT NULL default '0.00',
  `T008_Valor_Preco_Sem_Desconto_Unitario` decimal(20,5) default '0.00000',
  `T008_Valor_Total_Preco_Sem_Desconto` decimal(20,2) default '0.00',
  `T008_Valor_Preco_Unitario` decimal(20,5) NOT NULL default '0.00000',
  `T008_Valor_Total_Preco` decimal(20,2) NOT NULL default '0.00',
  `T008_Valor_Desconto_Unitario` decimal(20,10) default '0.0000000000',
  `T008_Valor_Total_Desconto` decimal(20,10) default '0.0000000000',
  `T008_Valor_Despesas_Unitario` decimal(20,5) NOT NULL default '0.00000',
  `T008_Valor_Total_Despesas` decimal(20,5) NOT NULL default '0.00000',
  `T008_Valor_Custo_Final_Unitario` decimal(20,5) NOT NULL default '0.00000',
  `T008_Valor_Total_Custo_Final` decimal(20,5) NOT NULL default '0.00000',
  `T008_Valor_Frete_Unitario` decimal(20,10) default '0.0000000000',
  `T008_Valor_Total_Frete` decimal(20,10) default '0.0000000000',
  `T008_Valor_Despesas_Acessorias_Unitario` decimal(20,10) default '0.0000000000',
  `T008_Valor_Total_Despesas_Acessorias` decimal(20,10) default '0.0000000000',
  `T008_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T008_Codigo_Produto` varchar(30) default NULL,
  `T008_Codigo_Produto_Pendencia` varchar(30) default NULL,
  `T008_Codigo_Cliente` varchar(6) default NULL,
  `T008_Codigo_Fornecedor` varchar(6) default NULL,
  `T008_Codigo_Fornecedor_Pendencia` varchar(6) default NULL,
  `T008_Classificacao_Fiscal` varchar(12) default NULL,
  `T008_Codigo_Natureza` int(5) NOT NULL default '0',
  `T008_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T008_Valor_Base_ICMS_Impresso` decimal(20,5) default '0.00000',
  `T008_Valor_Base_ICMS` decimal(20,5) default '0.00000',
  `T008_Valor_Base_ICMS_Sem_Reducao` decimal(20,5) default '0.00000',
  `T008_Valor_Base_ICMS_Diferido` decimal(20,5) default '0.00000',
  `T008_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T008_Valor_ICMS` decimal(20,5) default '0.00000',
  `T008_Valor_Agregado` decimal(20,2) default '0.00',
  `T008_Valor_Base_ICMS_Substituicao` decimal(20,2) default '0.00',
  `T008_Percentual_Reducao_ICMS_ST` decimal(5,2) default '0.00',
  `T008_Aliquota_ICMS_ST` decimal(5,2) default '0.00',
  `T008_Valor_ICMS_Substituicao` decimal(20,2) default '0.00',
  `T008_Valor_ICMS_Substituicao_Retencao` decimal(20,2) default '0.00',
  `T008_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T008_Valor_Base_IPI` decimal(20,2) NOT NULL default '0.00',
  `T008_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T008_Valor_IPI` decimal(20,4) NOT NULL default '0.0000',
  `T008_IPV` decimal(10,4) NOT NULL default '0.0000',
  `T008_Codigo_Produto_Nota` varchar(25) default NULL,
  `T008_Flag_Preco_Especial` varchar(1) default NULL,
  `T008_Flag_Situacao_Tributaria` varchar(3) default NULL,
  `T008_Flag_Classificacao` int(2) default NULL,
  `T008_Peso_Unitario` decimal(10,3) default '0.000',
  `T008_Peso_Total` decimal(10,3) default '0.000',
  `T008_Flag_Classificacao_Fiscal` varchar(5) default NULL,
  `T008_Flag_ST` varchar(1) default 'N',
  `T008_ICMS_Inter_Estadual` decimal(5,2) default '0.00',
  `T008_ST_ICMS_Interno` decimal(5,2) default '0.00',
  `T008_ST_VA` decimal(10,2) default '0.00',
  `T008_Situacao_Tributaria_PIS` varchar(2) default '01',
  `T008_Flag_Isento_PIS` enum('S','N') default 'N',
  `T008_Valor_Base_PIS` decimal(20,2) default '0.00',
  `T008_Aliquota_PIS` decimal(5,2) default '0.00',
  `T008_Valor_PIS` decimal(10,2) default '0.00',
  `T008_Situacao_Tributaria_COFINS` varchar(2) default '01',
  `T008_Flag_Isento_COFINS` enum('S','N') default 'N',
  `T008_Valor_Base_COFINS` decimal(20,2) default '0.00',
  `T008_Aliquota_COFINS` decimal(5,2) default '0.00',
  `T008_Valor_COFINS` decimal(10,2) default '0.00',
  `T008_Flag_Excluir` enum('S','N') default 'N',
  `T008_Aliquota_Imposto_Importacao` decimal(5,2) default '0.00',
  `T008_Valor_Base_Imposto_Importacao_Unitario` decimal(19,6) default '0.000000',
  `T008_Valor_Total_Base_Imposto_Importacao` decimal(19,2) default '0.00',
  `T008_Valor_Imposto_Importacao_Unitario` decimal(19,6) default '0.000000',
  `T008_Valor_Total_Imposto_Importacao` decimal(19,2) default '0.00',
  `T008_Valor_Total_Despesas_Aduaneira` decimal(19,2) default '0.00',
  `T008_Valor_Total_IOF` decimal(19,2) default '0.00',
  `T008_Numero_Documento_Importacao` varchar(10) default NULL,
  `T008_Data_Registro_Documento_Importacao` date default NULL,
  `T008_Local_Desembaraco` varchar(60) default NULL,
  `T008_UF_Desembaraco` varchar(2) default NULL,
  `T008_Data_Desembaraco` date default NULL,
  `T008_Codigo_Exportador` varchar(60) default NULL,
  `T008_Numero_Adicao` int(3) default '0',
  `T008_Numero_Sequencia_Adicao` int(3) default '0',
  `T008_Codigo_Fabricante` varchar(60) default NULL,
  `T008_Valor_Desconto_Item_Adicao` decimal(15,2) default '0.00',
  PRIMARY KEY  (`T008_Id`),
  KEY `T008_Index_C004_Id` (`T008_C004_Id`),
  KEY `T008_Index_Estoque` (`T008_D009_Id`),
  KEY `T008_Index_Pendencia` (`T008_D009_Id_Pendencia`),
  KEY `T008_Index_Maquinas` (`T008_D040_Id`),
  KEY `T008_Index_Unidade` (`T008_D037_Id`),
  KEY `T008_Index_Itens_Pedido` (`T008_T006_Id`),
  KEY `T008_Index_Classificacao_Fiscal` (`T008_D005_Id`),
  KEY `T008_Index_Nota_Fiscal` (`T008_T007_Id`),
  KEY `T008_Index_Item_Devolvido_Emitido` (`T008_T008_Id`),
  KEY `T008_Index_Item_Devolvido_Entrada` (`T008_T014_Id`),
  KEY `T008_Index_Natureza_Operacao` (`T008_D006_Id`),
  KEY `T008_Index_Valor_Unitario` (`T008_Valor_Preco_Unitario`),
  KEY `T008_Index_Codigo_Produto` (`T008_Codigo_Produto`),
  KEY `T008_Index_Codigo_Fornecedor` (`T008_Codigo_Fornecedor`),
  KEY `T008_Index_Numero_Nota_Fiscal` (`T008_Numero_Nota_Fiscal`),
  KEY `T008_Index_Itens_Importacao` (`T008_T055_Id`),
  KEY `T008_D003_Id` (`T008_D003_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t008`
--

LOCK TABLES `t008` WRITE;
/*!40000 ALTER TABLE `t008` DISABLE KEYS */;
/*!40000 ALTER TABLE `t008` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t009`
--

DROP TABLE IF EXISTS `t009`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t009` (
  `T009_Id` int(10) unsigned NOT NULL auto_increment,
  `T009_C008_Id` int(10) unsigned default NULL,
  `T009_C007_Id` int(10) unsigned default NULL,
  `T009_C004_Id` int(10) unsigned NOT NULL default '0',
  `T009_Data` date default NULL,
  `T009_Observacao` text,
  `T009_Flag_Cotacao_Finalizada` varchar(1) default 'N',
  `T009_Flag_Fornecedor_Automatico` varchar(1) default 'N',
  PRIMARY KEY  (`T009_Id`),
  KEY `T009_IDX_USUARIO` (`T009_C007_Id`),
  KEY `T009_IDX_TIPO_PRODUTOS` (`T009_C008_Id`),
  KEY `T009_IDX_EMPRESAS` (`T009_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t009`
--

LOCK TABLES `t009` WRITE;
/*!40000 ALTER TABLE `t009` DISABLE KEYS */;
/*!40000 ALTER TABLE `t009` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t010`
--

DROP TABLE IF EXISTS `t010`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t010` (
  `T010_Id` int(10) unsigned NOT NULL auto_increment,
  `T010_D044_Id` int(10) unsigned default NULL,
  `T010_D036_Id` int(10) unsigned default NULL,
  `T010_D022_Id` int(10) unsigned default NULL,
  `T010_T009_Id` int(10) unsigned default NULL,
  `T010_D024_Id` int(10) unsigned default NULL,
  `T010_C007_Id` int(10) unsigned default NULL,
  `T010_C004_Id` int(10) unsigned NOT NULL default '0',
  `T010_Prazo_Pagamento_1` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_2` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_3` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_4` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_5` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_6` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_7` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_8` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_9` int(10) NOT NULL default '0',
  `T010_Prazo_Pagamento_10` int(10) NOT NULL default '0',
  `T010_Flag_Gerar_OC` int(1) default '2',
  `T010_Flag_Excluir` varchar(1) default 'N',
  `T010_Observacao` text,
  `T010_Prazo_Entrega` int(10) NOT NULL default '0',
  `T010_Valor_Frete` decimal(10,2) NOT NULL default '0.00',
  `T010_Valor_Total_Custo` decimal(10,2) NOT NULL default '0.00',
  `T010_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T010_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T010_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T010_Valor_Total_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T010_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T010_Valor_Total_Base_IPI` decimal(10,2) NOT NULL default '0.00',
  `T010_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T010_Valor_Total_IPI` decimal(10,2) NOT NULL default '0.00',
  `T010_Nome_Fornecedor` varchar(50) default NULL,
  `T010_Nome_Contato` varchar(40) default NULL,
  `T010_Fax` varchar(30) default NULL,
  `T010_Flag_Frete` int(1) default NULL,
  `T010_Prazo_Pagamento` varchar(60) default NULL,
  `T010_E_mail_Comprador` varchar(50) default NULL,
  `T010_Data` date default NULL,
  `T010_Flag_Cotacao_Finalizada` varchar(1) default NULL,
  `T010_C008_Id` int(11) default NULL,
  PRIMARY KEY  (`T010_Id`),
  KEY `T010_IDX_COTACA_COMPRA` (`T010_T009_Id`),
  KEY `T010_IDX_EMPRESAS` (`T010_C004_Id`),
  KEY `T010_FKIndex3` (`T010_D024_Id`),
  KEY `T010_FKIndex4` (`T010_D022_Id`),
  KEY `T010_FKIndex5` (`T010_D036_Id`),
  KEY `T010_FKIndex6` (`T010_D044_Id`),
  KEY `T010_FKIndex7` (`T010_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t010`
--

LOCK TABLES `t010` WRITE;
/*!40000 ALTER TABLE `t010` DISABLE KEYS */;
/*!40000 ALTER TABLE `t010` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t011`
--

DROP TABLE IF EXISTS `t011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t011` (
  `T011_Id` int(10) unsigned NOT NULL auto_increment,
  `T011_D022_Id` int(10) unsigned default NULL,
  `T011_D024_Id` int(10) unsigned default NULL,
  `T011_T009_Id` int(10) unsigned default NULL,
  `T011_D036_Id` int(10) unsigned default NULL,
  `T011_T010_Id` int(10) unsigned default NULL,
  `T011_C007_Id` int(10) unsigned default NULL,
  `T011_C008_Id` int(10) unsigned default NULL,
  `T011_C004_Id` int(10) unsigned NOT NULL default '0',
  `T011_Numero` varchar(10) default NULL,
  `T011_Data_Emissao` date default NULL,
  `T011_Data_Previsao_Recebimento` date default NULL,
  `T011_Data_Previsao_Pagamento` date default NULL,
  `T011_Prazo_Pagamento_1` int(10) default '0',
  `T011_Prazo_Pagamento_2` int(10) default '0',
  `T011_Prazo_Pagamento_3` int(10) default '0',
  `T011_Prazo_Pagamento_4` int(10) default '0',
  `T011_Prazo_Pagamento_5` int(10) default '0',
  `T011_Prazo_Pagamento_6` int(10) default '0',
  `T011_Prazo_Pagamento_7` int(10) default '0',
  `T011_Prazo_Pagamento_8` int(10) default '0',
  `T011_Prazo_Pagamento_9` int(10) default '0',
  `T011_Prazo_Pagamento_10` int(10) default '0',
  `T011_Observacao` text,
  `T011_Prazo_Entrega` varchar(50) default NULL,
  `T011_Valor_Frete` decimal(10,2) default '0.00',
  `T011_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T011_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T011_Valor_Total_Base_ICMS` decimal(10,2) default '0.00',
  `T011_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T011_Valor_Total_ICMS` decimal(10,2) default '0.00',
  `T011_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T011_Valor_Total_Base_IPI` decimal(10,2) default '0.00',
  `T011_Aliquota_IPI` decimal(5,2) default '0.00',
  `T011_Valor_Total_IPI` decimal(10,2) default '0.00',
  `T011_Nome_Contato` varchar(40) default NULL,
  `T011_Fax` varchar(30) default NULL,
  `T011_Flag_Cancelada` varchar(1) default 'N',
  `T011_Codigo_Comprador` varchar(10) default NULL,
  `T011_Centro_Custo` varchar(10) NOT NULL,
  `T011_Prazo_Pagamento` varchar(50) default NULL,
  `T011_Valor_Total_Pendente` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T011_Id`),
  KEY `T011_IDX_USUARIOS` (`T011_C007_Id`),
  KEY `T011_IDX_EMPRESAS` (`T011_C004_Id`),
  KEY `T011_IDX_TIPO_PRODUTO` (`T011_C008_Id`),
  KEY `T011_FKIndex3` (`T011_D036_Id`),
  KEY `T011_FKIndex4` (`T011_T010_Id`),
  KEY `T011_FKIndex5` (`T011_T009_Id`),
  KEY `T011_FKIndex6` (`T011_D024_Id`),
  KEY `T011_FKIndex7` (`T011_D022_Id`),
  KEY `IDX_T011_Id` (`T011_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t011`
--

LOCK TABLES `t011` WRITE;
/*!40000 ALTER TABLE `t011` DISABLE KEYS */;
/*!40000 ALTER TABLE `t011` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t012`
--

DROP TABLE IF EXISTS `t012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t012` (
  `T012_Id` int(10) unsigned NOT NULL auto_increment,
  `T012_T059_Id` int(10) unsigned default NULL,
  `T012_D009_Id` int(10) unsigned default NULL,
  `T012_T011_Id` int(10) unsigned default NULL,
  `T012_C004_Id` int(10) unsigned default '0',
  `T012_Codigo_Produto` varchar(30) default NULL,
  `T012_Codigo_Produto_Fornecedor` varchar(20) default NULL,
  `T012_Descricao_Produto` varchar(50) default NULL,
  `T012_Quantidade` decimal(10,3) default NULL,
  `T012_Valor_Custo_Unitario` decimal(14,5) default '0.00000',
  `T012_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T012_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T012_Valor_Base_ICMS` decimal(10,2) default '0.00',
  `T012_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T012_Valor_ICMS` decimal(10,2) default '0.00',
  `T012_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T012_Valor_Base_IPI` decimal(10,2) default '0.00',
  `T012_Aliquota_IPI` decimal(5,2) default '0.00',
  `T012_Valor_IPI` decimal(10,2) default '0.00',
  `T012_Prazo_Entrega` int(10) default '0',
  `T012_Classificacao_Fiscal` varchar(12) default NULL,
  `T012_Codigo_Fornecedor` varchar(6) default NULL,
  `T012_Flag_Travar_Pedido` varchar(1) default 'N',
  `T012_Numero` varchar(10) default NULL,
  `T012_Flag_Cancelada` varchar(1) default 'N',
  `T012_Observacao` tinytext NOT NULL,
  `T012_T006_Id` int(10) unsigned default NULL,
  PRIMARY KEY  (`T012_Id`),
  KEY `T012_IDX_PRODUTO_ESTOQUE` (`T012_D009_Id`),
  KEY `T012_IDX_PEDIDO_COMPRA` (`T012_T011_Id`),
  KEY `T012_IDX_CODIGO_PRODUTO` (`T012_Codigo_Produto`),
  KEY `T012_IDX_CODIGO_FORNECEDOR` (`T012_Codigo_Fornecedor`),
  KEY `T012_IDX_EMPRESAS` (`T012_C004_Id`),
  KEY `T012_IDX_PRECOS_COTACAO` (`T012_T059_Id`),
  KEY `T012_IDX_NUMERO_PEDIDO` (`T012_Numero`),
  KEY `T012_IDX_ID` (`T012_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t012`
--

LOCK TABLES `t012` WRITE;
/*!40000 ALTER TABLE `t012` DISABLE KEYS */;
/*!40000 ALTER TABLE `t012` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t013`
--

DROP TABLE IF EXISTS `t013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t013` (
  `T013_Id` int(10) unsigned NOT NULL auto_increment,
  `T013_D014_Id` int(11) default '0',
  `T013_C007_Id` int(10) unsigned default NULL,
  `T013_C007_Id_Vendedor_Retorno` int(11) default '0',
  `T013_T011_Id` int(10) unsigned default NULL,
  `T013_D006_Id` int(10) unsigned default NULL,
  `T013_D024_Id` int(10) unsigned default NULL,
  `T013_D022_Id` int(10) unsigned default NULL,
  `T013_T085_Id` int(10) unsigned default NULL,
  `T013_C008_Id` int(10) unsigned NOT NULL default '0',
  `T013_C004_Id` int(10) unsigned NOT NULL default '0',
  `T013_D021_Id` int(10) unsigned default '0',
  `T013_D031_Id` int(10) unsigned NOT NULL default '0',
  `T013_D031_Id_Entrada` int(10) unsigned NOT NULL default '0',
  `T013_C007_Id_Devolucao` int(10) default NULL,
  `T013_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T013_Numero_Nota_Fiscal_Devolucao` varchar(40) default NULL,
  `T013_Data_Emissao` date default NULL,
  `T013_Data_Entrada` date default NULL,
  `T013_Numero_Parcelas` int(2) default '0',
  `T013_Parcelas` varchar(30) default NULL,
  `T013_Codigo_CFOP` decimal(5,3) default '0.000',
  `T013_Numero_Controle` int(6) default '0',
  `T013_Nome_Transportadora` varchar(30) default NULL,
  `T013_Tipo_Transporte` varchar(10) default NULL,
  `T013_Valor_Frete` decimal(10,2) default '0.00',
  `T013_Flag_Frete` int(1) default '0',
  `T013_Observacao_1` tinytext,
  `T013_Observacao_2` varchar(60) default NULL,
  `T013_Observacao_3` varchar(60) default NULL,
  `T013_Mao_Obra_1` text,
  `T013_Mao_Obra_2` varchar(60) default NULL,
  `T013_Valor_Mao_Obra` decimal(10,2) default '0.00',
  `T013_Flag_Mao_Obra` varchar(10) default NULL,
  `T013_Valor_Total` decimal(14,2) default '0.00',
  `T013_Valor_Total_Produtos` decimal(14,2) default '0.00',
  `T013_Valor_Total_Custo_Final` decimal(19,2) default '0.00',
  `T013_Numero_Conhecimento` varchar(20) default NULL,
  `T013_Valor_Seguro` decimal(14,2) default '0.00',
  `T013_Valor_Despesas_Acessorias` decimal(14,2) default '0.00',
  `T013_Valor_Encargos` decimal(12,6) default '0.000000',
  `T013_Valor_Desconto_Produtos` decimal(10,2) default '0.00',
  `T013_Valor_Desconto` decimal(12,6) default '0.000000',
  `T013_Numero_Ordem_Compra` int(10) default '0',
  `T013_Codigo_Natureza` int(4) default '0',
  `T013_Codigo_Fornecedor` varchar(6) default NULL,
  `T013_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T013_Valor_Total_Base_ICMS` decimal(14,2) default '0.00',
  `T013_Valor_Total_Base_ICMS_Diferido` decimal(14,2) default '0.00',
  `T013_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T013_Valor_Total_ICMS` decimal(14,2) default '0.00',
  `T013_Valor_Total_Base_ICMS_Substituicao` decimal(14,2) default '0.00',
  `T013_Valor_ICMS_Substituicao` decimal(14,2) default '0.00',
  `T013_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T013_Valor_Total_Base_IPI` decimal(10,2) default '0.00',
  `T013_Aliquota_IPI` decimal(5,2) default '0.00',
  `T013_Valor_Total_IPI` decimal(10,2) default '0.00',
  `T013_Aliquota_ISS` decimal(5,2) default '0.00',
  `T013_Valor_ISS` decimal(10,2) default '0.00',
  `T013_Prazo_Pagamento_1` int(10) default '0',
  `T013_Prazo_Pagamento_2` int(10) default '0',
  `T013_Prazo_Pagamento_3` int(10) default '0',
  `T013_Prazo_Pagamento_4` int(10) default '0',
  `T013_Prazo_Pagamento_5` int(10) default '0',
  `T013_Prazo_Pagamento_6` int(10) default '0',
  `T013_Prazo_Pagamento_7` int(10) default '0',
  `T013_Prazo_Pagamento_8` int(10) default '0',
  `T013_Prazo_Pagamento_9` int(10) default '0',
  `T013_Prazo_Pagamento_10` int(10) default '0',
  `T013_Flag_Cancelada` varchar(1) default 'N',
  `T013_Flag_Travar_Nota` varchar(1) default 'N',
  `T013_Flag_Calcular_ICMS_Frete` varchar(1) default 'N',
  `T013_Flag_Calcular_ICMS_Seguro` varchar(1) default 'N',
  `T013_Flag_Calcular_ICMS_Despesas_Acessorias` varchar(1) default 'N',
  `T013_Flag_Calcular_ICMS_IPI` varchar(1) default 'N',
  `T013_Flag_Calcular_IPI_Frete` varchar(1) default 'N',
  `T013_Flag_Calcular_IPI_Seguro` varchar(1) default 'N',
  `T013_Flag_Calcular_IPI_Despesas_Acessorias` varchar(1) default 'N',
  `T013_Flag_Arredondar_Valor_IPI` varchar(1) default 'N',
  `T013_Flag_OK_Fiscal` int(1) default '2',
  `T013_Flag_Devolucao` varchar(1) default 'N',
  `T013_Casas_Decimais_Valor_ICMS` int(1) default '2',
  `T013_Numero_Dias_Entrada` int(3) default '0',
  `T013_Valor_Entrada` decimal(10,2) default '0.00',
  `T013_Numero_Dias` int(3) default '0',
  `T013_Valor_Juros` decimal(10,2) default '0.00',
  `T013_Observacao_Cancelamento` text,
  `T013_IPV` decimal(10,4) default NULL,
  `T013_Percentual_Comissao_Representante` decimal(10,2) default NULL,
  `T013_IPV_sem_icms` decimal(10,3) default NULL,
  `T013_Valor_Dolar` decimal(12,4) default '0.0000',
  `T013_Flag_Classificacao` varchar(15) default NULL,
  `T013_Codigo_Sistema_Antigo` varchar(20) NOT NULL,
  `T013_Serie` varchar(10) NOT NULL,
  `T013_Tipo` varchar(10) default NULL,
  PRIMARY KEY  (`T013_Id`),
  KEY `T013_Index_Natureza_Operacao` (`T013_D006_Id`),
  KEY `T013_Index_Numero_Nota_Fiscal` (`T013_Numero_Nota_Fiscal`),
  KEY `T013_Index_Codigo_Fornecedor` (`T013_Codigo_Fornecedor`),
  KEY `T013_FKIndex3` (`T013_T011_Id`),
  KEY `T013_Index_Tipo_Produto` (`T013_C008_Id`),
  KEY `T013_Index_Usuarios` (`T013_C007_Id`),
  KEY `T013_Index_Tipo_Portador` (`T013_D031_Id`),
  KEY `T013_Index_Tipo_Portador_Entrada` (`T013_D031_Id_Entrada`),
  KEY `T013_Index_Cliente` (`T013_D024_Id`),
  KEY `T013_Index_Transportadora` (`T013_D022_Id`),
  KEY `T013_Index_Conhecimento` (`T013_T085_Id`),
  KEY `T013_IDX_ID` (`T013_Id`),
  KEY `T013_IDX_Cancelada` (`T013_Flag_Cancelada`),
  KEY `IDX_C007_DEV` (`T013_C007_Id_Vendedor_Retorno`),
  KEY `T013_Codigo_Sistema_Antigo` (`T013_Codigo_Sistema_Antigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t013`
--

LOCK TABLES `t013` WRITE;
/*!40000 ALTER TABLE `t013` DISABLE KEYS */;
/*!40000 ALTER TABLE `t013` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t014`
--

DROP TABLE IF EXISTS `t014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t014` (
  `T014_Id` int(10) unsigned NOT NULL auto_increment,
  `T014_D006_Id` int(10) unsigned default NULL,
  `T014_T012_Id` int(10) unsigned default NULL,
  `T014_D005_Id` int(10) unsigned default NULL,
  `T014_T013_Id` int(10) unsigned default NULL,
  `T014_T014_Id` int(10) unsigned default NULL,
  `T014_D009_Id` int(10) unsigned default NULL,
  `T014_C004_Id` int(10) unsigned NOT NULL default '0',
  `T014_T008_Id` int(10) unsigned NOT NULL default '0',
  `T014_D003_Id` int(10) default NULL,
  `T014_Codigo_CFOP` decimal(4,2) default '0.00',
  `T014_Data_Emissao` date default NULL,
  `T014_Flag_Nacional_Importado` varchar(1) default NULL,
  `T014_Descricao_Produto` varchar(30) default NULL,
  `T014_Quantidade` decimal(10,3) default NULL,
  `T014_Valor_Custo_Unitario` decimal(14,5) default '0.00000',
  `T014_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T014_Valor_Custo_Final_Unitario` decimal(20,6) default '0.000000',
  `T014_Valor_Total_Custo_Final` decimal(14,5) default '0.00000',
  `T014_Valor_Desconto_Unitario` decimal(10,2) default '0.00',
  `T014_Valor_Total_Desconto` decimal(10,5) default '0.00000',
  `T014_Valor_Frete` decimal(10,3) default '0.000',
  `T014_Valor_Seguro` decimal(10,2) default '0.00',
  `T014_Valor_Despesas_Acessorias` decimal(10,2) default '0.00',
  `T014_Valor_Unitario_Desp_Nota_Origem` decimal(20,5) default '0.00000',
  `T014_Valor_Total_Desp_Nota_Origem` decimal(20,2) default '0.00',
  `T014_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T014_Valor_Base_ICMS` decimal(10,2) default '0.00',
  `T014_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T014_Valor_ICMS` decimal(12,5) default '0.00000',
  `T014_Valor_ICMS_Substituicao` decimal(10,2) default '0.00',
  `T014_Valor_ICMS_Substituicao_Unitario` decimal(20,5) default '0.00000',
  `T014_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T014_Valor_Base_IPI` decimal(10,2) default '0.00',
  `T014_Aliquota_IPI` decimal(5,2) default '0.00',
  `T014_Valor_IPI` decimal(10,2) default '0.00',
  `T014_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T014_Codigo_Produto` varchar(30) default NULL,
  `T014_Codigo_Substituto` varchar(20) default NULL,
  `T014_Codigo_Produto_Fornecedor` varchar(20) default NULL,
  `T014_Codigo_Fornecedor` varchar(6) default NULL,
  `T014_Codigo_Cliente` varchar(6) default NULL,
  `T014_Classificacao_Fiscal` varchar(12) default NULL,
  `T014_Flag_Cancelada` varchar(1) default 'N',
  `T014_Flag_Excluir` int(1) default '2',
  `T014_Flag_Classificacao` int(2) default NULL,
  `T014_Unidade` varchar(5) NOT NULL,
  `T014_D037_Id` int(10) unsigned default NULL,
  `T014_T129_Id` int(10) unsigned default NULL,
  PRIMARY KEY  (`T014_Id`),
  KEY `T014_Index_Estoque` (`T014_D009_Id`),
  KEY `T014_Index_Classificacao_Fiscal` (`T014_D005_Id`),
  KEY `T014_Index_Quantidade` (`T014_Quantidade`),
  KEY `T014_Index_Valor_Unitario` (`T014_Valor_Custo_Unitario`),
  KEY `T014_Numero_Nota_Fiscal` (`T014_Numero_Nota_Fiscal`),
  KEY `T014_Index_Codigo_Fornecedor` (`T014_Codigo_Fornecedor`),
  KEY `T014_FKIndex5` (`T014_T013_Id`),
  KEY `T014_FKIndex4` (`T014_T012_Id`),
  KEY `T014_FKIndex6` (`T014_T008_Id`),
  KEY `T014_Index_Item_Devolvido_Entrada` (`T014_T014_Id`),
  KEY `T014_D003_Id` (`T014_D003_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t014`
--

LOCK TABLES `t014` WRITE;
/*!40000 ALTER TABLE `t014` DISABLE KEYS */;
/*!40000 ALTER TABLE `t014` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t015`
--

DROP TABLE IF EXISTS `t015`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t015` (
  `T015_Id` int(10) unsigned NOT NULL auto_increment,
  `T015_D073_Id` int(10) unsigned default NULL,
  `T015_T084_Id` int(10) unsigned default NULL,
  `T015_T056_Id` int(10) unsigned default NULL,
  `T015_T060_Id` int(10) unsigned default NULL,
  `T015_T045_Id` int(10) unsigned default NULL,
  `T015_D014_Id` int(10) unsigned default NULL,
  `T015_T018_Id` int(10) unsigned default NULL,
  `T015_D031_Id` int(10) unsigned default NULL,
  `T015_D024_Id` int(10) unsigned default NULL,
  `T015_D021_Id` int(10) unsigned default NULL,
  `T015_D007_Id` int(10) unsigned default NULL,
  `T015_T007_Id` int(10) unsigned default NULL,
  `T015_T013_Id` int(10) unsigned default NULL,
  `T015_T054_Id` int(10) unsigned default NULL,
  `T015_D044_Id` int(10) unsigned default NULL,
  `T015_C004_Id` int(11) NOT NULL default '0',
  `T015_T052_Id` int(10) unsigned default NULL,
  `T015_T061_Id` int(10) default NULL,
  `T015_T076_Id` int(10) default NULL,
  `T015_Flag_Conta` varchar(1) default NULL,
  `T015_Nome_Cliente` varchar(50) default NULL,
  `T015_Numero_Duplicata` varchar(15) default NULL,
  `T015_Numero_Documento` varchar(15) default NULL,
  `T015_Data_Emissao` date default NULL,
  `T015_Data_Vencimento` date default NULL,
  `T015_Data_Pagamento` date default NULL,
  `T015_Valor_Duplicata` decimal(14,2) NOT NULL default '0.00',
  `T015_Valor_Juros` decimal(12,2) NOT NULL default '0.00',
  `T015_Valor_Desconto` decimal(12,2) NOT NULL default '0.00',
  `T015_Valor_Total` decimal(14,2) NOT NULL default '0.00',
  `T015_Observacao` text,
  `T015_Numero_Agencia` int(4) NOT NULL default '0',
  `T015_Numero_Conta` varchar(10) default NULL,
  `T015_Numero_Cheque` int(10) NOT NULL default '0',
  `T015_Observacao_1` varchar(40) default NULL,
  `T015_Observacao_2` varchar(40) default NULL,
  `T015_Numero_Controle` int(6) NOT NULL default '0',
  `T015_Data_Lancamento` date default NULL,
  `T015_Hora_Lancamento` time default NULL,
  `T015_Valor_Base_Comissao` decimal(10,2) NOT NULL default '0.00',
  `T015_Percentual_Comissao` decimal(10,2) NOT NULL default '0.00',
  `T015_Valor_Comissao` decimal(10,2) NOT NULL default '0.00',
  `T015_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T015_Data_Vencimento_1` date default NULL,
  `T015_Data_Vencimento_2` date default NULL,
  `T015_Data_Vencimento_3` date default NULL,
  `T015_Data_Vencimento_4` date default NULL,
  `T015_Data_Vencimento_5` date default NULL,
  `T015_Agrupamento` varchar(20) default NULL,
  `T015_Nosso_Numero` varchar(12) default NULL,
  `T015_Codigo_Fornecedor` varchar(6) default NULL,
  `T015_Codigo_Revenda` varchar(6) default NULL,
  `T015_Codigo_Conta_Banco` int(3) NOT NULL default '0',
  `T015_Conta` varchar(16) default NULL,
  `T015_SubConta` varchar(30) default NULL,
  `T015_Flag_Deb_Cred` varchar(1) default NULL,
  `T015_Flag_Tipo` int(1) NOT NULL default '0',
  `T015_Flag_Despesas` int(1) NOT NULL default '1',
  `T015_Obs_Mudanca_Vencto` text,
  `T015_Obs_Divisao_Titulo` text,
  `T015_Flag_Cancelada` varchar(1) default 'N',
  `T015_Flag_Baixar_Lote` varchar(1) default 'N',
  `T015_Valor_Cambio_Moeda` decimal(10,4) default '0.0000',
  `T015_Data_Cambio_Moeda` date default NULL,
  `T015_Valor_Duplicata_Moeda` decimal(14,2) default '0.00',
  `T015_Numero_Parcela` int(3) default '0',
  `T015_Flag_Boleto` enum('S','N') default 'N',
  `T015_Valor_Pago` decimal(10,2) default '0.00',
  `T015_Data_Baixa` date default '0000-00-00',
  PRIMARY KEY  (`T015_Id`),
  KEY `T015_Index_Id` (`T015_Id`),
  KEY `T015_Index_Baixa_Lote` (`T015_T045_Id`),
  KEY `T015_Index_Contas_Bancarias` (`T015_D007_Id`),
  KEY `T015_Index_Revenda` (`T015_D021_Id`),
  KEY `T015_Index_Cliente` (`T015_D024_Id`),
  KEY `T015_Index_Nota_Compra_Entrada` (`T015_T007_Id`),
  KEY `T015_Index_Nota_Compra_Saida` (`T015_T013_Id`),
  KEY `T015_Index_Plano_Contas` (`T015_D014_Id`),
  KEY `T015_Index_Projecoes` (`T015_T018_Id`),
  KEY `T015_Index_Portador` (`T015_D031_Id`),
  KEY `T015_Index_Numero_Nota_Fiscal` (`T015_Numero_Nota_Fiscal`),
  KEY `T015_Index_C004_Id` (`T015_C004_Id`),
  KEY `T015_Index_Codigo_Fornecedor` (`T015_Codigo_Fornecedor`),
  KEY `T015_Index_Conta` (`T015_Conta`),
  KEY `T015_Index_SubCodigo` (`T015_SubConta`),
  KEY `T015_Index_Data_Vencimento` (`T015_Data_Vencimento`),
  KEY `T015_Index_Data_Pagamento` (`T015_Data_Pagamento`),
  KEY `T015_Index_Importacao` (`T015_T054_Id`),
  KEY `T015_Index_Moeda` (`T015_D044_Id`),
  KEY `T015_Index_Cambio` (`T015_T060_Id`),
  KEY `T015_Index_Despesas_Importacao` (`T015_T056_Id`),
  KEY `T015_Index_Conhecimentos` (`T015_T084_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t015`
--

LOCK TABLES `t015` WRITE;
/*!40000 ALTER TABLE `t015` DISABLE KEYS */;
/*!40000 ALTER TABLE `t015` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t016`
--

DROP TABLE IF EXISTS `t016`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t016` (
  `T016_Id` int(10) unsigned NOT NULL auto_increment,
  `T016_Numero` varchar(5) default NULL,
  `T016_Data_Inicio` date default NULL,
  `T016_Data_Final` date default NULL,
  `T016_Observacao` text,
  PRIMARY KEY  (`T016_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t016`
--

LOCK TABLES `t016` WRITE;
/*!40000 ALTER TABLE `t016` DISABLE KEYS */;
/*!40000 ALTER TABLE `t016` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t017`
--

DROP TABLE IF EXISTS `t017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t017` (
  `T017_Id` int(10) unsigned NOT NULL auto_increment,
  `T017_D039_Id` int(10) unsigned default NULL,
  `T017_D024_Id` int(10) unsigned default NULL,
  `T017_Codigo` varchar(6) default NULL,
  `T017_Cod_Clas` varchar(5) default NULL,
  PRIMARY KEY  (`T017_Id`),
  KEY `T017_FKIndex1` (`T017_D039_Id`),
  KEY `T017_FKIndex2` (`T017_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t017`
--

LOCK TABLES `t017` WRITE;
/*!40000 ALTER TABLE `t017` DISABLE KEYS */;
/*!40000 ALTER TABLE `t017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t018`
--

DROP TABLE IF EXISTS `t018`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t018` (
  `T018_Id` int(10) unsigned NOT NULL auto_increment,
  `T018_C005_Id` int(10) unsigned default NULL,
  `T018_D014_Id` int(10) unsigned default NULL,
  `T018_C004_Id` int(11) NOT NULL default '0',
  `T018_Conta` varchar(16) default NULL,
  `T018_SubConta` varchar(30) default NULL,
  `T018_Mes` varchar(2) default NULL,
  `T018_Ano` varchar(4) default NULL,
  `T018_Data_Vencimento` date default NULL,
  `T018_Valor` decimal(19,2) NOT NULL default '0.00',
  `T018_Percentual` decimal(10,6) NOT NULL default '0.000000',
  `T018_Periodicidade` int(1) NOT NULL default '1',
  `T018_Funcao` varchar(30) default NULL,
  `T018_Flag_Confirmado` varchar(1) default NULL,
  `T018_Valor_Receber` decimal(14,2) default '0.00',
  `T018_Valor_Pagar` decimal(14,2) default '0.00',
  `T018_Valor_Saldo` decimal(14,2) default '0.00',
  `T018_Valor_Pago` decimal(10,2) default NULL,
  `T018_Valor_Recebido` decimal(10,2) default NULL,
  `T018_Valor_Saldo_Projecao` decimal(10,2) default NULL,
  PRIMARY KEY  (`T018_Id`),
  KEY `T018_Index_Plano_Contas` (`T018_D014_Id`),
  KEY `T018_Index_C004_Id` (`T018_C004_Id`),
  KEY `T018_Index_C005_Id` (`T018_C005_Id`),
  KEY `T018_Index_Mes` (`T018_Mes`),
  KEY `T018_Index_Ano` (`T018_Ano`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t018`
--

LOCK TABLES `t018` WRITE;
/*!40000 ALTER TABLE `t018` DISABLE KEYS */;
/*!40000 ALTER TABLE `t018` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t019`
--

DROP TABLE IF EXISTS `t019`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t019` (
  `T019_Id` int(10) unsigned NOT NULL auto_increment,
  `T019_D031_Id` int(10) unsigned default NULL,
  `T019_T005_Id` int(10) unsigned default NULL,
  `T019_D027_Id` int(10) unsigned default NULL,
  `T019_T007_Id` int(10) unsigned default NULL,
  `T019_D024_Id` int(10) unsigned default NULL,
  `T019_D014_Id` int(10) unsigned default NULL,
  `T019_T002_Id` int(10) unsigned default NULL,
  `T019_T015_Id` int(10) unsigned default NULL,
  `T019_D007_Id` int(10) unsigned default NULL,
  `T019_C004_Id` int(10) unsigned NOT NULL default '0',
  `T019_T018_Id` int(10) default NULL,
  `T019_Numero_Agencia` int(4) NOT NULL default '0',
  `T019_Numero_Conta` varchar(10) default NULL,
  `T019_Numero_Cheque` int(10) NOT NULL default '0',
  `T019_Data_Lancamento` date default NULL,
  `T019_Referente` varchar(30) default NULL,
  `T019_Conta` varchar(40) default NULL,
  `T019_SubConta` varchar(30) default NULL,
  `T019_Valor_Lancamento` decimal(14,2) default NULL,
  `T019_Codigo_Conta_Banco` int(3) NOT NULL default '0',
  `T019_Conciliado` int(1) NOT NULL default '1',
  `T019_Obs_Estorno` text,
  `T019_Complemento` varchar(50) default NULL,
  `T019_Flag_Deb_Crd` varchar(1) default NULL,
  `T019_Saldo` decimal(10,2) default '0.00',
  `T019_Flag_Excluido` varchar(1) default 'N',
  PRIMARY KEY  (`T019_Id`),
  KEY `T019_Index_Contas_Bancarias` (`T019_D007_Id`),
  KEY `T019_Index_C004_Id` (`T019_C004_Id`),
  KEY `T019_Index_D024` (`T019_D024_Id`),
  KEY `T019_Index_D014` (`T019_D014_Id`),
  KEY `T019_Index_Contas_Pagar` (`T019_T015_Id`),
  KEY `T019_Index_Contas_Receber` (`T019_T002_Id`),
  KEY `T019_Data_Lancamento` (`T019_Data_Lancamento`),
  KEY `IDX_T019_T005_Id` (`T019_T005_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t019`
--

LOCK TABLES `t019` WRITE;
/*!40000 ALTER TABLE `t019` DISABLE KEYS */;
/*!40000 ALTER TABLE `t019` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t020`
--

DROP TABLE IF EXISTS `t020`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t020` (
  `T020_Id` int(10) unsigned NOT NULL auto_increment,
  `T020_T002_Id` int(10) unsigned default NULL,
  `T020_C004_Id` int(11) NOT NULL default '0',
  `T020_Conta` varchar(16) default NULL,
  `T020_SubConta` varchar(30) default NULL,
  `T020_Codigo_Cliente` varchar(50) default NULL,
  `T020_Numero_Duplicata` varchar(15) default NULL,
  `T020_Data_Lancamento` date default NULL,
  `T020_Hora_Lancamento` time default NULL,
  `T020_Historico` varchar(30) default NULL,
  `T020_Valor` decimal(19,2) NOT NULL default '0.00',
  `T020_Flag_Deb_Cred` varchar(1) default NULL,
  PRIMARY KEY  (`T020_Id`),
  KEY `T020_Index_Conta` (`T020_Conta`),
  KEY `T020_Index_SubConta` (`T020_SubConta`),
  KEY `T020_Index_Codigo_Cliente` (`T020_Codigo_Cliente`),
  KEY `T020_Index_Numero_Duplicata` (`T020_Numero_Duplicata`),
  KEY `T020_Index_Contas_Receber` (`T020_T002_Id`),
  KEY `T020_Index_C004_Id` (`T020_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t020`
--

LOCK TABLES `t020` WRITE;
/*!40000 ALTER TABLE `t020` DISABLE KEYS */;
/*!40000 ALTER TABLE `t020` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t021`
--

DROP TABLE IF EXISTS `t021`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t021` (
  `T021_Id` int(10) unsigned NOT NULL auto_increment,
  `T021_C004_Id` int(10) unsigned default NULL,
  `T021_Codigo_Maquina` int(3) NOT NULL default '0',
  `T021_Nome` varchar(30) default NULL,
  `T021_Setor` varchar(1) default NULL,
  `T021_Planilha` varchar(10) default NULL,
  `T021_Quantidade` int(1) NOT NULL default '0',
  `T021_Flag_Critica` varchar(1) default NULL,
  `T021_Flag_Inativa` varchar(1) default NULL,
  PRIMARY KEY  (`T021_Id`),
  KEY `T021_Index_Codigo_Maquina` (`T021_Codigo_Maquina`),
  KEY `T021_Index_C004_Id` (`T021_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t021`
--

LOCK TABLES `t021` WRITE;
/*!40000 ALTER TABLE `t021` DISABLE KEYS */;
/*!40000 ALTER TABLE `t021` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t022`
--

DROP TABLE IF EXISTS `t022`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t022` (
  `T022_Id` int(10) unsigned NOT NULL auto_increment,
  `T022_C004_Id` int(10) unsigned NOT NULL default '0',
  `T022_Codigo_Operador` int(6) NOT NULL default '0',
  `T022_Nome` varchar(30) default NULL,
  `T022_Setor` varchar(1) default NULL,
  `T022_Ordem` int(5) NOT NULL default '0',
  PRIMARY KEY  (`T022_Id`),
  KEY `T022_Index_Codigo_Operador` (`T022_Codigo_Operador`),
  KEY `T022_Index_C004_Id` (`T022_C004_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t022`
--

LOCK TABLES `t022` WRITE;
/*!40000 ALTER TABLE `t022` DISABLE KEYS */;
/*!40000 ALTER TABLE `t022` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t023`
--

DROP TABLE IF EXISTS `t023`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t023` (
  `T023_Id` int(10) unsigned NOT NULL auto_increment,
  `T023_D001_Id` int(10) unsigned default NULL,
  `T023_T022_Id` int(10) unsigned default NULL,
  `T023_T021_Id` int(10) unsigned default NULL,
  `T023_C004_Id` int(10) unsigned default NULL,
  `T023_Codigo_Maquina` int(10) NOT NULL default '0',
  `T023_Lote` int(4) NOT NULL default '0',
  `T023_Codigo_Produto` varchar(30) NOT NULL default '0',
  `T023_Letra` varchar(1) default NULL,
  `T023_Quantidade` int(10) NOT NULL default '0',
  `T023_Codigo_Operador` int(10) default NULL,
  `T023_Ocorrencia` varchar(5) default NULL,
  `T023_Data` date default NULL,
  `T023_Quantidade_Sucatada` int(10) NOT NULL default '0',
  `T023_Flag_Servico` int(1) NOT NULL default '0',
  `T023_Numero_Ficha` int(10) NOT NULL default '0',
  PRIMARY KEY  (`T023_Id`),
  KEY `T023_Index_Equipe` (`T023_T021_Id`),
  KEY `T023_Index_Maquina` (`T023_T022_Id`),
  KEY `T023_Index_Produtos` (`T023_D001_Id`),
  KEY `T023_Index_C004_Id` (`T023_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t023`
--

LOCK TABLES `t023` WRITE;
/*!40000 ALTER TABLE `t023` DISABLE KEYS */;
/*!40000 ALTER TABLE `t023` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t025`
--

DROP TABLE IF EXISTS `t025`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t025` (
  `T025_Id` int(10) unsigned NOT NULL auto_increment,
  `T025_D005_Id` int(10) unsigned default NULL,
  `T025_D002_Id` int(10) unsigned default NULL,
  `T025_D015_Id` int(10) unsigned default NULL,
  `T025_Classificacao_Fiscal` varchar(12) default NULL,
  `T025_Codigo_Descricao` varchar(5) default NULL,
  `T025_Codigo_Grupo` varchar(4) default NULL,
  `T025_D024_Id` int(10) default NULL,
  PRIMARY KEY  (`T025_Id`),
  KEY `T025_Index_Grupo` (`T025_D015_Id`),
  KEY `T025_Index_Descricao` (`T025_D002_Id`),
  KEY `T025_Index_Classificacao` (`T025_D005_Id`),
  KEY `T025_Index_Codigo_Grupo` (`T025_Codigo_Grupo`),
  KEY `T025_Index_Codigo_Descricao` (`T025_Codigo_Descricao`),
  KEY `T025_Index_Classificacao_Fiscal` (`T025_Classificacao_Fiscal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t025`
--

LOCK TABLES `t025` WRITE;
/*!40000 ALTER TABLE `t025` DISABLE KEYS */;
/*!40000 ALTER TABLE `t025` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t026`
--

DROP TABLE IF EXISTS `t026`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t026` (
  `T026_Id` int(10) unsigned NOT NULL auto_increment,
  `T026_D024_Id` int(10) unsigned default NULL,
  `T026_C004_Id` int(10) unsigned NOT NULL default '0',
  `T026_Codigo_Cliente` varchar(6) default NULL,
  `T026_Cobranca` text,
  `T026_Nome_Cliente` varchar(40) default NULL,
  `T026_Data_Atraso` date default NULL,
  `T026_Valor_Atraso` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T026_Id`),
  KEY `T026_IDX_CLIENTE` (`T026_D024_Id`),
  KEY `T026_IDX_EMPRESAS` (`T026_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t026`
--

LOCK TABLES `t026` WRITE;
/*!40000 ALTER TABLE `t026` DISABLE KEYS */;
/*!40000 ALTER TABLE `t026` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t027`
--

DROP TABLE IF EXISTS `t027`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t027` (
  `T027_Id` int(10) unsigned NOT NULL auto_increment,
  `T027_C004_Id` int(10) unsigned default NULL,
  `T027_Numero` varchar(10) default NULL,
  `T027_Data` date default NULL,
  `T027_Observacao` text,
  PRIMARY KEY  (`T027_Id`),
  KEY `T027_IDX_EMPRESAS` (`T027_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t027`
--

LOCK TABLES `t027` WRITE;
/*!40000 ALTER TABLE `t027` DISABLE KEYS */;
/*!40000 ALTER TABLE `t027` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t028`
--

DROP TABLE IF EXISTS `t028`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t028` (
  `T028_Id` int(10) unsigned NOT NULL auto_increment,
  `T028_D004_Id` int(10) unsigned default NULL,
  `T028_D009_Id` int(10) unsigned default NULL,
  `T028_D023_Id` int(10) unsigned default NULL,
  `T028_T027_Id` int(10) unsigned default NULL,
  `T028_C004_Id` int(10) unsigned NOT NULL default '0',
  `T028_Quantidade_Contada` int(10) NOT NULL default '0',
  `T028_Quantidade_Estoque_Atual` int(10) NOT NULL default '0',
  `T028_Flag_Cancelado` varchar(1) default NULL,
  `T028_Divergencia_Estoque` varchar(10) default NULL,
  `T028_Valor_Custo_Unitario` decimal(10,2) NOT NULL default '0.00',
  `T028_Numero` varchar(10) default NULL,
  `T028_Codigo_Produto` varchar(30) default NULL,
  `T028_Codigo_Fornecedor` varchar(6) default NULL,
  `T028_Codigo_Local` varchar(10) default NULL,
  PRIMARY KEY  (`T028_Id`),
  KEY `T028_IDX_BALANCO` (`T028_T027_Id`),
  KEY `T028_IDX_EMPRESAS` (`T028_C004_Id`),
  KEY `T028_IDX_PRODUTOS_ESTOQUE` (`T028_D009_Id`),
  KEY `T028_IDX_FORNECEDOR` (`T028_D023_Id`),
  KEY `T028_IDX_CODIGO_PRODUTO_ESTOQUE` (`T028_Codigo_Produto`),
  KEY `T028_IDX_CODIGO_FORNECEDOR` (`T028_Codigo_Fornecedor`),
  KEY `T028_IDX_CODIGO_BALANCO` (`T028_Numero`),
  KEY `T028_IDX_LOCAL` (`T028_D004_Id`),
  KEY `T028_IDX_CODIGO_LOCAL` (`T028_Codigo_Local`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t028`
--

LOCK TABLES `t028` WRITE;
/*!40000 ALTER TABLE `t028` DISABLE KEYS */;
/*!40000 ALTER TABLE `t028` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t029`
--

DROP TABLE IF EXISTS `t029`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t029` (
  `T029_Id` int(10) unsigned NOT NULL auto_increment,
  `T029_C004_Id` int(10) unsigned NOT NULL default '0',
  `T029_C007_Id` int(10) NOT NULL,
  `T029_Numero` int(10) NOT NULL default '0',
  `T029_Data` date default NULL,
  `T029_Descricao` text,
  `T029_Flag_Ok` varchar(1) default NULL,
  `T029_Valor_Debitos` decimal(10,2) NOT NULL default '0.00',
  `T029_Valor_Creditos` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`T029_Id`),
  KEY `T029_IDX_EMPRESAS` (`T029_C004_Id`),
  KEY `T029_IDX_NUMERO` (`T029_Numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t029`
--

LOCK TABLES `t029` WRITE;
/*!40000 ALTER TABLE `t029` DISABLE KEYS */;
/*!40000 ALTER TABLE `t029` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t030`
--

DROP TABLE IF EXISTS `t030`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t030` (
  `T030_Id` int(10) unsigned NOT NULL auto_increment,
  `T030_D009_Id` int(10) unsigned default NULL,
  `T030_T029_Id` int(10) unsigned default NULL,
  `T030_C004_Id` int(10) unsigned NOT NULL default '0',
  `T030_C007_Id` int(10) NOT NULL,
  `T030_Quantidade` decimal(10,2) default NULL,
  `T030_Flag_Entrada_Saida` varchar(1) default NULL,
  `T030_Valor_Custo_Unitario` decimal(14,5) default '0.00000',
  `T030_Flag_Alterar_Custo` varchar(1) NOT NULL default 'N',
  `T030_Numero` int(10) NOT NULL default '0',
  `T030_Codigo_Produto` varchar(30) default NULL,
  `T030_Codigo_Fornecedor` varchar(6) default NULL,
  `T030_Valor_Custo_Unitario_Origem` decimal(12,2) default '0.00',
  `T030_Flag_Cancelada` varchar(1) default 'N',
  `T030_Observacao` tinytext NOT NULL,
  `T030_Data_Inclusao` datetime NOT NULL,
  PRIMARY KEY  (`T030_Id`),
  KEY `T030_IDX_DIVERGENCIA_ESTOQUE` (`T030_T029_Id`),
  KEY `T030_IDX_EMPRESAS` (`T030_C004_Id`),
  KEY `T030_IDX_CODIGO_PRODUTO` (`T030_Codigo_Produto`),
  KEY `T030_IDX_CODIGO_FORNECEDOR` (`T030_Codigo_Fornecedor`),
  KEY `T030_IDX_PRODUTO_ESTOQUE` (`T030_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t030`
--

LOCK TABLES `t030` WRITE;
/*!40000 ALTER TABLE `t030` DISABLE KEYS */;
/*!40000 ALTER TABLE `t030` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t031`
--

DROP TABLE IF EXISTS `t031`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t031` (
  `T031_Id` int(10) unsigned NOT NULL auto_increment,
  `T031_D009_Id` int(10) unsigned default NULL,
  `T031_D024_Id` int(10) unsigned default NULL,
  `T031_C004_Id` int(10) unsigned NOT NULL default '0',
  `T031_Data_Lancamento` date default NULL,
  `T031_Descricao_Operacao` varchar(20) default NULL,
  `T031_Quantidade` int(6) NOT NULL default '0',
  `T031_Flag_Operacao` varchar(1) default NULL,
  `T031_Valor_Preco_Unitario` decimal(14,2) NOT NULL default '0.00',
  `T031_Observacao` varchar(40) default NULL,
  `T031_Valor_Custo_Unitario` decimal(10,2) NOT NULL default '0.00',
  `T031_Codigo_Fornecedor` varchar(6) default NULL,
  `T031_Codigo_Produto` varchar(30) default NULL,
  `T031_Codigo_Cliente` varchar(6) default NULL,
  PRIMARY KEY  (`T031_Id`),
  KEY `T031_IDX_CLIENTE` (`T031_D024_Id`),
  KEY `T031_IDX_PRODUTO_ESTOQUE` (`T031_D009_Id`),
  KEY `T031_IDX_CODIGO_FORNECEDOR` (`T031_Codigo_Fornecedor`),
  KEY `T031_IDX_CODIGO_PRODUTO` (`T031_Codigo_Produto`),
  KEY `T031_IDX_CODIGO_CLIENTE` (`T031_Codigo_Cliente`),
  KEY `T031_IDX_EMPRESAS` (`T031_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t031`
--

LOCK TABLES `t031` WRITE;
/*!40000 ALTER TABLE `t031` DISABLE KEYS */;
/*!40000 ALTER TABLE `t031` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t032`
--

DROP TABLE IF EXISTS `t032`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t032` (
  `T032_Id` int(10) unsigned NOT NULL auto_increment,
  `T032_D023_Id` int(10) unsigned default NULL,
  `T032_C007_Id` int(10) unsigned default NULL,
  `T032_C004_Id` int(10) unsigned NOT NULL default '0',
  `T032_Numero` int(10) NOT NULL default '0',
  `T032_Data_Emissao` date default NULL,
  `T032_Nome_Comprador` varchar(30) default NULL,
  `T032_Numero_Pedido` int(10) NOT NULL default '0',
  `T032_Data_Confirmacao` date default NULL,
  `T032_Codigo_Usuario` int(4) NOT NULL default '0',
  `T032_Codigo_Fornecedor` varchar(6) default NULL,
  `T032_Codigo_Fornecedor_2` varchar(6) default NULL,
  PRIMARY KEY  (`T032_Id`),
  KEY `T032_IDX_USUARIO` (`T032_C007_Id`),
  KEY `T032_IDX_FORNECEDOR` (`T032_D023_Id`),
  KEY `T032_IDX_EMPRESAS` (`T032_C004_Id`),
  KEY `T032_IDX_NUMERO` (`T032_Numero`),
  KEY `T032_IDX_CODIGO_USUARIO` (`T032_Codigo_Usuario`),
  KEY `T032_IDX_CODIGO_FORNECEDOR` (`T032_Codigo_Fornecedor`),
  KEY `T032_IDX_CODIGO_FORNECEDOR_2` (`T032_Codigo_Fornecedor_2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t032`
--

LOCK TABLES `t032` WRITE;
/*!40000 ALTER TABLE `t032` DISABLE KEYS */;
/*!40000 ALTER TABLE `t032` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t033`
--

DROP TABLE IF EXISTS `t033`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t033` (
  `T033_Id` int(10) unsigned NOT NULL auto_increment,
  `T033_T015_Id` int(10) unsigned default NULL,
  `T033_C004_Id` int(11) NOT NULL default '0',
  `T033_Conta` varchar(16) default NULL,
  `T033_SubConta` varchar(30) default NULL,
  `T033_Codigo_Fornecedor` varchar(50) default NULL,
  `T033_Numero_Duplicata` varchar(15) default NULL,
  `T033_Data_Lancamento` date default NULL,
  `T033_Hora_Lancamento` time default NULL,
  `T033_Historico` varchar(30) default NULL,
  `T033_Valor` decimal(19,2) NOT NULL default '0.00',
  `T033_Flag_Deb_Cred` varchar(1) default NULL,
  PRIMARY KEY  (`T033_Id`),
  KEY `T033_Index_Conta` (`T033_Conta`),
  KEY `T033_Index_SubConta` (`T033_SubConta`),
  KEY `T033_Index_Codigo_Fornecedor` (`T033_Codigo_Fornecedor`),
  KEY `T033_Index_Numero_Duplicata` (`T033_Numero_Duplicata`),
  KEY `T033_Index_Contas_Pagar` (`T033_T015_Id`),
  KEY `T033_Index_C004_Id` (`T033_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t033`
--

LOCK TABLES `t033` WRITE;
/*!40000 ALTER TABLE `t033` DISABLE KEYS */;
/*!40000 ALTER TABLE `t033` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t034`
--

DROP TABLE IF EXISTS `t034`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t034` (
  `T034_Id` int(10) unsigned NOT NULL auto_increment,
  `T034_T018_Id` int(10) unsigned default NULL,
  `T034_D014_Id` int(10) unsigned default NULL,
  `T034_C004_Id` int(10) unsigned default NULL,
  `T034_Conta` varchar(16) default NULL,
  `T034_SubConta` varchar(30) default NULL,
  `T034_Data` date default NULL,
  `T034_Valor` decimal(19,2) default NULL,
  `T034_Flag_Debito_Credito` varchar(1) default NULL,
  `T034_Historico` varchar(72) default NULL,
  PRIMARY KEY  (`T034_Id`),
  KEY `T034_IDX_PLANO_CONTAS` (`T034_D014_Id`),
  KEY `T034_IDX_EMPRESAS` (`T034_C004_Id`),
  KEY `T034_FKIndex2` (`T034_T018_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t034`
--

LOCK TABLES `t034` WRITE;
/*!40000 ALTER TABLE `t034` DISABLE KEYS */;
/*!40000 ALTER TABLE `t034` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t035`
--

DROP TABLE IF EXISTS `t035`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t035` (
  `T035_Id` int(10) unsigned NOT NULL auto_increment,
  `T035_D007_Id` int(10) unsigned default NULL,
  `T035_C004_Id` int(11) NOT NULL default '0',
  `T035_D062_Id` int(11) default NULL,
  `T035_Data_Processamento` datetime default NULL,
  `T035_Conteudo_Arquivo` text,
  `T035_Mensagem_Erro` text,
  `T035_Sequencial_Remessa` int(7) default NULL,
  PRIMARY KEY  (`T035_Id`),
  KEY `T035_FKIndex1` (`T035_D007_Id`),
  KEY `T035_index_D062_Id` (`T035_D062_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t035`
--

LOCK TABLES `t035` WRITE;
/*!40000 ALTER TABLE `t035` DISABLE KEYS */;
/*!40000 ALTER TABLE `t035` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036`
--

DROP TABLE IF EXISTS `t036`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t036` (
  `T036_Id` int(10) unsigned NOT NULL auto_increment,
  `T036_T002_Id` int(10) unsigned default NULL,
  `T036_C004_Id` int(10) unsigned NOT NULL default '0',
  `T036_Data_Entrada` date default NULL,
  `T036_Data_Credito` date default NULL,
  `T036_Data_Processamento` date default NULL,
  `T036_Valor_Titulo` decimal(19,2) NOT NULL default '0.00',
  `T036_Valor_Abatimento` decimal(19,2) NOT NULL default '0.00',
  `T036_Valor_Recebido` decimal(19,2) NOT NULL default '0.00',
  `T036_Valor_Juros_Mora` decimal(19,2) NOT NULL default '0.00',
  `T036_Valor_Outras_Despesas` decimal(19,2) NOT NULL default '0.00',
  `T036_Valor_Tarifa` decimal(19,2) NOT NULL default '0.00',
  `T036_Flag_Debito_Credito` varchar(2) default NULL,
  `T036_Numero_Banco` varchar(3) default NULL,
  `T036_Numero_Titulo` varchar(15) default NULL,
  `T036_Codigo_Cliente` varchar(6) default NULL,
  `T036_Nome_Cliente` varchar(50) default NULL,
  `T036_Nosso_Numero` varchar(13) default NULL,
  `T036_Codigo_Motivo` varchar(2) default NULL,
  `T036_Descricao_Motivo` varchar(50) default NULL,
  `T036_Data_Vencimento` date default NULL,
  PRIMARY KEY  (`T036_Id`),
  KEY `T036_FKIndex1` (`T036_T002_Id`),
  KEY `T036_FKIndex3` (`T036_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036`
--

LOCK TABLES `t036` WRITE;
/*!40000 ALTER TABLE `t036` DISABLE KEYS */;
/*!40000 ALTER TABLE `t036` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t037`
--

DROP TABLE IF EXISTS `t037`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t037` (
  `T037_Id` int(10) unsigned NOT NULL auto_increment,
  `T037_C004_Id` int(10) unsigned NOT NULL default '0',
  `T037_Nome_Arquivo` varchar(20) default NULL,
  `T037_Data_Importacao` datetime default NULL,
  `T037_Data_Processamento` datetime default NULL,
  `T037_HEADER_Prefixo_Agencia` int(4) NOT NULL default '0',
  `T037_HEADER_Digito_Verificador_Agencia` varchar(1) default NULL,
  `T037_HEADER_Codigo_Cedente` int(8) NOT NULL default '0',
  `T037_HEADER_Digito_Verificador_Cedente` varchar(1) default NULL,
  `T037_HEADER_Numero_Convenio` int(6) NOT NULL default '0',
  `T037_HEADER_Nome_Empresa` varchar(30) default NULL,
  `T037_HEADER_Codigo_Banco` varchar(3) default NULL,
  `T037_HEADER_Nome_Banco` varchar(15) default NULL,
  `T037_HEADER_Data_Gravacao_Arquivo` date default NULL,
  `T037_HEADER_Sequencial_Retorno` int(7) NOT NULL default '0',
  `T037_HEADER_String` text,
  `T037_TRAILLER_Quantidade_Titulos` int(8) NOT NULL default '0',
  `T037_TRAILLER_Valor_Total` decimal(12,2) NOT NULL default '0.00',
  `T037_TRAILLER_Numero_Aviso` int(8) NOT NULL default '0',
  `T037_TRAILLER_String` text,
  PRIMARY KEY  (`T037_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t037`
--

LOCK TABLES `t037` WRITE;
/*!40000 ALTER TABLE `t037` DISABLE KEYS */;
/*!40000 ALTER TABLE `t037` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t038`
--

DROP TABLE IF EXISTS `t038`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t038` (
  `T038_Id` int(10) unsigned NOT NULL auto_increment,
  `T038_T037_Id` int(10) unsigned default NULL,
  `T038_T002_Id` int(10) unsigned default NULL,
  `T038_C004_Id` int(11) NOT NULL default '0',
  `T038_Prefixo_Agencia` int(4) NOT NULL default '0',
  `T038_Digito_Verificador_Agencia` varchar(1) default NULL,
  `T038_Codigo_Cedente` int(8) NOT NULL default '0',
  `T038_Digito_Verificador_Cedente` varchar(1) default NULL,
  `T038_Numero_Convenio` int(6) NOT NULL default '0',
  `T038_Numero_Documento` varchar(25) default NULL,
  `T038_Nosso_Numero` varchar(12) default NULL,
  `T038_Digito_Verificador_Nosso_Numero` varchar(1) default NULL,
  `T038_Codigo_Tipo_Cobranca` int(1) NOT NULL default '0',
  `T038_Codigo_Tipo_Cobranca_Anterior` int(1) NOT NULL default '0',
  `T038_Dias_Calculo` int(4) NOT NULL default '0',
  `T038_Codigo_Natureza_Recebimento` int(2) NOT NULL default '0',
  `T038_Descricao_Natureza_Recebimento` text,
  `T038_Prefixo_Titulo` varchar(3) default NULL,
  `T038_Variacao_Carteira` int(3) NOT NULL default '0',
  `T038_Conta_Caucao` int(1) NOT NULL default '0',
  `T038_Codigo_Responsabilidade` int(5) NOT NULL default '0',
  `T038_Digito_Verificador_Codigo_Responsabilidade` varchar(1) default NULL,
  `T038_Valor_Taxa_Desconto` decimal(3,2) default NULL,
  `T038_Valor_Taxa_IOF` decimal(4,1) default NULL,
  `T038_Carteira` int(2) NOT NULL default '0',
  `T038_Codigo_Motivo` int(2) NOT NULL default '0',
  `T038_Descricao_Motivo` text,
  `T038_Data_Recebimento` date default NULL,
  `T038_Numero_Titulo_Cedente` varchar(10) default NULL,
  `T038_Confirmacao` varchar(20) default NULL,
  `T038_Data_Vencimento` date default NULL,
  `T038_Valor_Titulo` decimal(11,2) NOT NULL default '0.00',
  `T038_Codigo_Banco_Recebedor` int(3) NOT NULL default '0',
  `T038_Prefixo_Agencia_Recebedora` int(4) NOT NULL default '0',
  `T038_Digito_Verificador_Agencia_Recebedora` varchar(1) default NULL,
  `T038_Especie_Titulo` int(2) NOT NULL default '0',
  `T038_Data_Credito` date default NULL,
  `T038_Valor_Tarifa` decimal(5,2) NOT NULL default '0.00',
  `T038_Valor_Outras_Despesas` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Juros_Desconto` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_IOF_Desconto` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Abatimento` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Desconto` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Recebido` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Juros_Mora` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Outros_Recebimentos` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Abatimento_Nao_Aproveitado` decimal(11,2) NOT NULL default '0.00',
  `T038_Valor_Lancamento` decimal(11,2) NOT NULL default '0.00',
  `T038_Flag_Debito_Credito` int(1) NOT NULL default '0',
  `T038_Flag_Indicador_Valor` int(1) NOT NULL default '0',
  `T038_Valor_Ajuste` decimal(10,2) NOT NULL default '0.00',
  `T038_Flag_Situacao` int(1) NOT NULL default '0',
  `T038_Descricao_Situacao` varchar(50) default NULL,
  `T038_Numero_Linha` int(11) NOT NULL default '0',
  `T038_String` text,
  PRIMARY KEY  (`T038_Id`),
  KEY `T038_FKIndex1` (`T038_T002_Id`),
  KEY `T038_FKIndex2` (`T038_T037_Id`),
  KEY `T038_FKIndex3` (`T038_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t038`
--

LOCK TABLES `t038` WRITE;
/*!40000 ALTER TABLE `t038` DISABLE KEYS */;
/*!40000 ALTER TABLE `t038` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t039`
--

DROP TABLE IF EXISTS `t039`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t039` (
  `T039_Id` int(10) unsigned NOT NULL auto_increment,
  `T039_T032_Id` int(10) unsigned default NULL,
  `T039_D024_Id` int(10) unsigned default NULL,
  `T039_D035_Id` int(10) unsigned default NULL,
  `T039_C004_Id` int(10) unsigned default NULL,
  `T039_Quantidade` int(8) default NULL,
  `T039_Descricao_Produto` varchar(20) default NULL,
  `T039_Valor_Custo_Unitario` decimal(19,5) default NULL,
  `T039_Numero_Pedido` varchar(5) default NULL,
  `T039_Data` date default NULL,
  `T039_Flag_Qual_Fornecedor` int(1) default NULL,
  `T039_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `T039_Codigo_Produto` varchar(30) default NULL,
  `T039_Codigo_Fornecedor` varchar(6) default NULL,
  `T039_Numero` varchar(10) default NULL,
  PRIMARY KEY  (`T039_Id`),
  KEY `T039_FKIndex1` (`T039_D035_Id`),
  KEY `T039_FKIndex2` (`T039_T032_Id`),
  KEY `T039_FKIndex3` (`T039_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t039`
--

LOCK TABLES `t039` WRITE;
/*!40000 ALTER TABLE `t039` DISABLE KEYS */;
/*!40000 ALTER TABLE `t039` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t040`
--

DROP TABLE IF EXISTS `t040`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t040` (
  `T040_Id` int(10) unsigned NOT NULL auto_increment,
  `T040_C007_Id` int(10) unsigned default NULL,
  `T040_D024_Id` int(10) unsigned default NULL,
  `T040_C004_Id` int(10) unsigned NOT NULL default '0',
  `T040_Numero` int(10) default NULL,
  `T040_Data_Emissao` date default NULL,
  `T040_Valor_Total` decimal(10,2) default NULL,
  `T040_Codigo_Fornecedor` varchar(6) default NULL,
  `T040_Codigo_Usuario` int(4) default NULL,
  `T040_Numero_Orcamento` int(10) default NULL,
  PRIMARY KEY  (`T040_Id`),
  KEY `T040_FKIndex1` (`T040_D024_Id`),
  KEY `T040_FKIndex2` (`T040_C007_Id`),
  KEY `T040_FKIndex3` (`T040_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t040`
--

LOCK TABLES `t040` WRITE;
/*!40000 ALTER TABLE `t040` DISABLE KEYS */;
/*!40000 ALTER TABLE `t040` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t041`
--

DROP TABLE IF EXISTS `t041`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t041` (
  `T041_Id` int(10) unsigned NOT NULL auto_increment,
  `T041_T040_Id` int(10) unsigned default NULL,
  `T041_D035_Id` int(10) unsigned default NULL,
  `T041_D024_Id` int(10) unsigned default NULL,
  `T041_C004_Id` int(10) unsigned NOT NULL default '0',
  `T041_Quantidade` int(8) default NULL,
  `T041_Valor_Preco` decimal(14,5) default NULL,
  `T041_Descricao_Produto` varchar(20) default NULL,
  `T041_Data` date default NULL,
  `T041_Valor_Preco_Corrigido` decimal(10,2) default NULL,
  `T041_Numero` varchar(10) default NULL,
  `T041_Codigo_Local` varchar(10) default NULL,
  `T041_Codigo_Fornecedor` varchar(6) default NULL,
  `T041_Codigo_Produto` varchar(30) default NULL,
  PRIMARY KEY  (`T041_Id`),
  KEY `T041_FKIndex1` (`T041_T040_Id`),
  KEY `T041_FKIndex2` (`T041_D035_Id`),
  KEY `T041_FKIndex3` (`T041_D024_Id`),
  KEY `T041_FKIndex4` (`T041_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t041`
--

LOCK TABLES `t041` WRITE;
/*!40000 ALTER TABLE `t041` DISABLE KEYS */;
/*!40000 ALTER TABLE `t041` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t042`
--

DROP TABLE IF EXISTS `t042`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t042` (
  `T042_Id` int(10) unsigned NOT NULL auto_increment,
  `T042_C004_Id` int(10) unsigned default NULL,
  `T042_Descricao` tinytext,
  `T042_Data` date default NULL,
  `T042_Valor` decimal(19,2) default NULL,
  `T042_Valor_IPI` decimal(19,2) default NULL,
  `T042_Valor_ICMS` decimal(19,2) default NULL,
  `T042_Mes_Ano_Movimento` varchar(7) default NULL,
  PRIMARY KEY  (`T042_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t042`
--

LOCK TABLES `t042` WRITE;
/*!40000 ALTER TABLE `t042` DISABLE KEYS */;
/*!40000 ALTER TABLE `t042` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t043`
--

DROP TABLE IF EXISTS `t043`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t043` (
  `T043_Id` int(10) unsigned NOT NULL auto_increment,
  `T043_T009_Id` int(10) unsigned default NULL,
  `T043_D001_Id` int(10) unsigned default NULL,
  `T043_C004_Id` int(10) unsigned default NULL,
  `T043_Quantidade_Estoque_Total` int(10) default '0',
  `T043_Quantidade` decimal(10,3) default '0.000',
  `T043_Codigo_Produto` varchar(30) default NULL,
  `T043_Descricao_Produto` varchar(50) default NULL,
  PRIMARY KEY  (`T043_Id`),
  KEY `T043_FKIndex1` (`T043_D001_Id`),
  KEY `T043_FKIndex2` (`T043_T009_Id`),
  KEY `T043_FKIndex3` (`T043_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t043`
--

LOCK TABLES `t043` WRITE;
/*!40000 ALTER TABLE `t043` DISABLE KEYS */;
/*!40000 ALTER TABLE `t043` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t044`
--

DROP TABLE IF EXISTS `t044`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t044` (
  `T044_Id` int(10) unsigned NOT NULL auto_increment,
  `T044_D009_Id` int(10) unsigned default NULL,
  `T044_Mes` char(2) default NULL,
  `T044_Ano` char(4) default NULL,
  `T044_Frequencia_Venda` int(10) default NULL,
  `T044_Quantidade_Vendida` int(10) default NULL,
  `T044_Medida_Por_Venda` int(10) default NULL,
  `T044_Trimestre` char(1) default NULL,
  PRIMARY KEY  (`T044_Id`),
  KEY `T044_FKIndex1` (`T044_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t044`
--

LOCK TABLES `t044` WRITE;
/*!40000 ALTER TABLE `t044` DISABLE KEYS */;
/*!40000 ALTER TABLE `t044` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t045`
--

DROP TABLE IF EXISTS `t045`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t045` (
  `T045_Id` int(10) unsigned NOT NULL auto_increment,
  `T045_C007_Id` int(10) unsigned default NULL,
  `T045_C004_Id` int(11) default '0',
  `T045_Data` datetime default NULL,
  `T045_Observacao` text,
  PRIMARY KEY  (`T045_Id`),
  KEY `T045_FKIndex1` (`T045_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t045`
--

LOCK TABLES `t045` WRITE;
/*!40000 ALTER TABLE `t045` DISABLE KEYS */;
/*!40000 ALTER TABLE `t045` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t046`
--

DROP TABLE IF EXISTS `t046`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t046` (
  `T046_Id` int(10) unsigned NOT NULL auto_increment,
  `T046_D001_Id` int(10) unsigned default NULL,
  `T046_D049_Id` int(10) unsigned default NULL,
  `T046_T016_Id` int(10) unsigned default NULL,
  `T046_Numero` varchar(5) default NULL,
  `T046_Codigo_Produto` varchar(30) default NULL,
  `T046_Codigo_Fornecedor` varchar(6) default NULL,
  `T046_Valor_Base` decimal(14,2) default NULL,
  `T046_Data_Inicio` date default NULL,
  `T046_Flag_ICMS_Iguais` varchar(1) default 'N',
  `T046_Valor_Consumidor` decimal(14,2) default '0.00',
  `T046_Flag_Ignorar_Promocao_Consumidor` varchar(1) default 'N',
  PRIMARY KEY  (`T046_Id`),
  KEY `T046_FKIndex1` (`T046_T016_Id`),
  KEY `T046_FKIndex2` (`T046_D049_Id`),
  KEY `T046_FKIndex3` (`T046_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t046`
--

LOCK TABLES `t046` WRITE;
/*!40000 ALTER TABLE `t046` DISABLE KEYS */;
/*!40000 ALTER TABLE `t046` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t047`
--

DROP TABLE IF EXISTS `t047`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t047` (
  `T047_Id` int(10) unsigned NOT NULL auto_increment,
  `T047_C007_Id` int(10) unsigned default NULL,
  `T047_C004_Id` int(10) unsigned default NULL,
  `T047_Numero` varchar(5) default NULL,
  `T047_Data` date default NULL,
  `T047_Observacao` text,
  `T047_Flag_Status` varchar(1) default NULL,
  PRIMARY KEY  (`T047_Id`),
  KEY `T047_FKIndex1` (`T047_C007_Id`),
  KEY `T047_FKIndex2` (`T047_C004_Id`),
  KEY `IDX_Data` (`T047_Data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t047`
--

LOCK TABLES `t047` WRITE;
/*!40000 ALTER TABLE `t047` DISABLE KEYS */;
/*!40000 ALTER TABLE `t047` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t048`
--

DROP TABLE IF EXISTS `t048`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t048` (
  `T048_Id` int(10) unsigned NOT NULL auto_increment,
  `T048_T047_Id` int(10) unsigned default NULL,
  `T048_D049_Id` int(10) unsigned default NULL,
  `T048_C004_Id` int(10) unsigned default NULL,
  `T048_Codigo_Produto` varchar(30) default NULL,
  `T048_Quantidade` decimal(10,2) default NULL,
  `T048_Itens` text,
  `T048_Observacao` text,
  PRIMARY KEY  (`T048_Id`),
  KEY `T048_FKIndex1` (`T048_D049_Id`),
  KEY `T048_FKIndex2` (`T048_T047_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t048`
--

LOCK TABLES `t048` WRITE;
/*!40000 ALTER TABLE `t048` DISABLE KEYS */;
/*!40000 ALTER TABLE `t048` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t049`
--

DROP TABLE IF EXISTS `t049`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t049` (
  `T049_Id` int(10) unsigned NOT NULL auto_increment,
  `T049_C008_Id` int(10) unsigned default NULL,
  `T049_C007_Id` int(10) unsigned default NULL,
  `T049_C004_Id` int(10) unsigned NOT NULL default '0',
  `T049_Data` datetime default NULL,
  `T049_Observacao` text,
  `T049_Flag_Cotacao_Finalizada` varchar(1) default 'N',
  `T049_Flag_Fornecedor_Automatico` varchar(1) default 'N',
  PRIMARY KEY  (`T049_Id`),
  KEY `T009_IDX_EMPRESAS` (`T049_C004_Id`),
  KEY `T049_FKIndex1` (`T049_C007_Id`),
  KEY `T049_FKIndex2` (`T049_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t049`
--

LOCK TABLES `t049` WRITE;
/*!40000 ALTER TABLE `t049` DISABLE KEYS */;
/*!40000 ALTER TABLE `t049` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t050`
--

DROP TABLE IF EXISTS `t050`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t050` (
  `T050_Id` int(10) unsigned NOT NULL auto_increment,
  `T050_D001_Id` int(10) unsigned default NULL,
  `T050_T049_Id` int(10) unsigned default NULL,
  `T050_C004_Id` int(10) unsigned default NULL,
  `T050_Quantidade_Estoque_Total` int(10) default '0',
  `T050_Quantidade` int(10) default '0',
  `T050_Codigo_Produto` varchar(30) default NULL,
  `T050_Descricao_Produto` varchar(50) default NULL,
  PRIMARY KEY  (`T050_Id`),
  KEY `T050_FKIndex1` (`T050_T049_Id`),
  KEY `T050_FKIndex3` (`T050_D001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t050`
--

LOCK TABLES `t050` WRITE;
/*!40000 ALTER TABLE `t050` DISABLE KEYS */;
/*!40000 ALTER TABLE `t050` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t051`
--

DROP TABLE IF EXISTS `t051`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t051` (
  `T051_Id` int(10) unsigned NOT NULL auto_increment,
  `T051_D044_Id` int(10) unsigned default NULL,
  `T051_D036_Id` int(10) unsigned default NULL,
  `T051_C007_Id` int(10) unsigned default NULL,
  `T051_D022_Id` int(10) unsigned default NULL,
  `T051_T049_Id` int(10) unsigned default NULL,
  `T051_D024_Id` int(10) unsigned default NULL,
  `T051_C004_Id` int(10) unsigned NOT NULL default '0',
  `T051_Prazo_Pagamento_1` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_2` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_3` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_4` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_5` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_6` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_7` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_8` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_9` int(10) NOT NULL default '0',
  `T051_Prazo_Pagamento_10` int(10) NOT NULL default '0',
  `T051_Flag_Gerar_OC` int(1) NOT NULL default '2',
  `T051_Observacao` text,
  `T051_Prazo_Entrega` int(10) NOT NULL default '0',
  `T051_Valor_Frete` decimal(10,2) NOT NULL default '0.00',
  `T051_Valor_Total_Custo` decimal(10,2) NOT NULL default '0.00',
  `T051_Percentual_Reducao_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T051_Valor_Total_Base_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T051_Aliquota_ICMS` decimal(5,2) NOT NULL default '0.00',
  `T051_Valor_Total_ICMS` decimal(10,2) NOT NULL default '0.00',
  `T051_Percentual_Reducao_IPI` decimal(5,2) NOT NULL default '0.00',
  `T051_Valor_Total_Base_IPI` decimal(10,2) NOT NULL default '0.00',
  `T051_Aliquota_IPI` decimal(5,2) NOT NULL default '0.00',
  `T051_Valor_Total_IPI` decimal(10,2) NOT NULL default '0.00',
  `T051_Nome_Fornecedor` varchar(50) default NULL,
  `T051_Nome_Contato` varchar(40) default NULL,
  `T051_Fax` varchar(30) default NULL,
  PRIMARY KEY  (`T051_Id`),
  KEY `T010_IDX_EMPRESAS` (`T051_C004_Id`),
  KEY `T051_FKIndex1` (`T051_T049_Id`),
  KEY `T051_FKIndex2` (`T051_D024_Id`),
  KEY `T051_FKIndex3` (`T051_D022_Id`),
  KEY `T051_FKIndex4` (`T051_C007_Id`),
  KEY `T051_FKIndex5` (`T051_D036_Id`),
  KEY `T051_FKIndex6` (`T051_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t051`
--

LOCK TABLES `t051` WRITE;
/*!40000 ALTER TABLE `t051` DISABLE KEYS */;
/*!40000 ALTER TABLE `t051` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t052`
--

DROP TABLE IF EXISTS `t052`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t052` (
  `T052_Id` int(10) unsigned NOT NULL auto_increment,
  `T052_D044_Id` int(10) unsigned default NULL,
  `T052_C008_Id` int(10) unsigned default NULL,
  `T052_D022_Id` int(10) unsigned default NULL,
  `T052_T051_Id` int(10) unsigned default NULL,
  `T052_C007_Id` int(10) unsigned default NULL,
  `T052_D036_Id` int(10) unsigned default NULL,
  `T052_T049_Id` int(10) unsigned default NULL,
  `T052_D024_Id` int(10) unsigned default NULL,
  `T052_C004_Id` int(10) unsigned NOT NULL default '0',
  `T052_Data_Emissao` date default NULL,
  `T052_Prazo_Pagamento_1` int(10) default '0',
  `T052_Prazo_Pagamento_2` int(10) default '0',
  `T052_Prazo_Pagamento_3` int(10) default '0',
  `T052_Prazo_Pagamento_4` int(10) default '0',
  `T052_Prazo_Pagamento_5` int(10) default '0',
  `T052_Prazo_Pagamento_6` int(10) default '0',
  `T052_Prazo_Pagamento_7` int(10) default '0',
  `T052_Prazo_Pagamento_8` int(10) default '0',
  `T052_Prazo_Pagamento_9` int(10) default '0',
  `T052_Prazo_Pagamento_10` int(10) default '0',
  `T052_Observacao` text,
  `T052_Prazo_Entrega` int(10) default '0',
  `T052_Valor_Frete` decimal(10,2) default '0.00',
  `T052_Valor_Custo_Unitario` decimal(10,2) default '0.00',
  `T052_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T052_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T052_Valor_Total_Base_ICMS` decimal(10,2) default '0.00',
  `T052_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T052_Valor_Total_ICMS` decimal(10,2) default '0.00',
  `T052_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T052_Valor_Total_Base_IPI` decimal(10,2) default '0.00',
  `T052_Aliquota_IPI` decimal(5,2) default '0.00',
  `T052_Valor_Total_IPI` decimal(10,2) default '0.00',
  `T052_Nome_Contato` varchar(40) default NULL,
  `T052_Fax` varchar(30) default NULL,
  `T052_Numero` int(10) default NULL,
  `T052_Data_Previsao_Recebimento` date default NULL,
  `T052_Data_Previsao_Pagamento` date default NULL,
  `T052_Valor_Antecipado` decimal(10,2) default '0.00',
  `T052_Data_Vencimento_Antecipado` date default NULL,
  `T052_Flag_Excluir` varchar(1) default 'N',
  `T052_Peso_Kg_Total` decimal(10,3) default '0.000',
  `T052_Flag_Cancelada` varchar(1) default 'N',
  PRIMARY KEY  (`T052_Id`),
  KEY `T011_IDX_EMPRESAS` (`T052_C004_Id`),
  KEY `T052_FKIndex1` (`T052_D024_Id`),
  KEY `T052_FKIndex2` (`T052_T049_Id`),
  KEY `T052_FKIndex3` (`T052_D036_Id`),
  KEY `T052_FKIndex4` (`T052_C007_Id`),
  KEY `T052_FKIndex5` (`T052_T051_Id`),
  KEY `T052_FKIndex6` (`T052_D022_Id`),
  KEY `T052_FKIndex7` (`T052_D044_Id`),
  KEY `T052_FKIndex8` (`T052_C008_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t052`
--

LOCK TABLES `t052` WRITE;
/*!40000 ALTER TABLE `t052` DISABLE KEYS */;
/*!40000 ALTER TABLE `t052` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t053`
--

DROP TABLE IF EXISTS `t053`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t053` (
  `T053_Id` int(10) unsigned NOT NULL auto_increment,
  `T053_T058_Id` int(10) unsigned default NULL,
  `T053_D009_Id` int(10) unsigned default NULL,
  `T053_T050_Id` int(10) unsigned default NULL,
  `T053_T052_Id` int(10) unsigned default NULL,
  `T053_C004_Id` int(10) unsigned default '0',
  `T053_Codigo_Produto` varchar(30) default NULL,
  `T053_Codigo_Produto_Fornecedor` varchar(20) default NULL,
  `T053_Descricao_Produto` varchar(30) default NULL,
  `T053_Quantidade` int(10) default '0',
  `T053_Quantidade_Lancamento` int(10) default '0',
  `T053_Valor_Custo_Unitario` decimal(20,6) default '0.000000',
  `T053_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T053_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T053_Valor_Base_ICMS` decimal(10,2) default '0.00',
  `T053_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T053_Valor_ICMS` decimal(10,2) default '0.00',
  `T053_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T053_Valor_Base_IPI` decimal(10,2) default '0.00',
  `T053_Aliquota_IPI` decimal(5,2) default '0.00',
  `T053_Valor_IPI` decimal(10,2) default '0.00',
  `T053_Prazo_Entrega` int(10) default '0',
  `T053_Classificacao_Fiscal` varchar(12) default NULL,
  `T053_Flag_Travar_Pedido` varchar(1) default 'N',
  `T053_Peso_Kg_Unitario` decimal(19,3) default '0.000',
  `T053_Peso_Kg_Total` decimal(10,3) default '0.000',
  `T053_Peso_Kg_Unitario_Ajustado` decimal(19,6) default '0.000000',
  `T053_Flag_Cancelada` varchar(1) default 'N',
  PRIMARY KEY  (`T053_Id`),
  KEY `T012_IDX_CODIGO_PRODUTO` (`T053_Codigo_Produto`),
  KEY `T012_IDX_EMPRESAS` (`T053_C004_Id`),
  KEY `T053_FKIndex1` (`T053_T052_Id`),
  KEY `T053_FKIndex2` (`T053_T050_Id`),
  KEY `T053_FKIndex3` (`T053_D009_Id`),
  KEY `T053_FKIndex4` (`T053_T058_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t053`
--

LOCK TABLES `t053` WRITE;
/*!40000 ALTER TABLE `t053` DISABLE KEYS */;
/*!40000 ALTER TABLE `t053` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t054`
--

DROP TABLE IF EXISTS `t054`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t054` (
  `T054_Id` int(10) unsigned NOT NULL auto_increment,
  `T054_D006_Id` int(10) unsigned default NULL,
  `T054_D022_Id` int(10) unsigned default NULL,
  `T054_C008_Id` int(10) unsigned default NULL,
  `T054_C007_Id` int(10) unsigned default NULL,
  `T054_D036_Id` int(10) unsigned default NULL,
  `T054_D044_Id` int(10) unsigned default NULL,
  `T054_D018_Id` int(10) unsigned default NULL,
  `T054_C004_Id` int(10) unsigned NOT NULL default '0',
  `T054_Data_Emissao` datetime default NULL,
  `T054_Data_Transferencia_Estoque` datetime default NULL,
  `T054_Data_Transferencia_Custo` datetime default NULL,
  `T054_Numero_DI` varchar(30) default NULL,
  `T054_Valor_DI` decimal(10,2) default '0.00',
  `T054_Observacao` text,
  `T054_Observacao_Fiscal` text,
  `T054_Valor_Total_Custo_Original` decimal(19,2) default '0.00',
  `T054_Valor_Total_Produtos` decimal(19,2) default '0.00',
  `T054_Valor_Total_Produtos_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_IPI` decimal(19,2) default '0.00',
  `T054_Valor_Total_IPI` decimal(19,2) default '0.00',
  `T054_Valor_Total_IPI_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_Imposto_Importacao` decimal(19,2) default '0.00',
  `T054_Valor_Total_Imposto_Importacao` decimal(19,2) default '0.00',
  `T054_Valor_Total_Imposto_Importacao_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_COFINS` decimal(19,2) default '0.00',
  `T054_Valor_Total_COFINS` decimal(19,2) default '0.00',
  `T054_Valor_Total_COFINS_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_PIS` decimal(19,2) default '0.00',
  `T054_Valor_Total_PIS` decimal(19,2) default '0.00',
  `T054_Valor_Total_PIS_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_Extra` decimal(19,2) default '0.00',
  `T054_Valor_Total_Extra` decimal(19,2) default '0.00',
  `T054_Valor_Total_Extra_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_Fatura` decimal(19,2) default '0.00',
  `T054_Valor_Total_Frete` decimal(19,2) default '0.00',
  `T054_Valor_Total_Frete_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_CIF` decimal(19,2) default '0.00',
  `T054_Valor_Total_CIF_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Aduaneiro` decimal(19,2) default '0.00',
  `T054_Valor_Total_Aduaneiro_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total` decimal(19,2) default '0.00',
  `T054_Valor_Total_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_Imposto_Importacao` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_IPI` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_PIS_COFINS` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_Aduaneira` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_Aduaneira_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_Despesas_ICMS` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_ICMS` decimal(19,2) default '0.00',
  `T054_Valor_Total_Base_ICMS_Presumido` decimal(19,2) default '0.00',
  `T054_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T054_Percentual_Reducao_ICMS_Presumido` decimal(5,2) default '0.00',
  `T054_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T054_Aliquota_ICMS_Presumido` decimal(5,2) default '0.00',
  `T054_Valor_Total_ICMS` decimal(19,2) default '0.00',
  `T054_Valor_Total_ICMS_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_ICMS_Presumido` decimal(19,2) default '0.00',
  `T054_Valor_Total_ICMS_Presumido_Real` decimal(19,2) default '0.00',
  `T054_Valor_Total_ICMS_Pagar` decimal(19,2) default '0.00',
  `T054_Valor_Total_ICMS_Pagar_Real` decimal(19,2) default '0.00',
  `T054_Peso_Kg_Bruto` decimal(19,3) default '0.000',
  `T054_Peso_Kg_Liquido` decimal(19,3) default '0.000',
  `T054_Peso_Kg_Liquido_Fatura` decimal(19,6) default '0.000000',
  `T054_Peso_Lb_Bruto` decimal(19,3) default '0.000',
  `T054_Peso_Lb_Liquido` decimal(19,3) default '0.000',
  `T054_Valor_Cambio_Euro` decimal(19,4) default '0.0000',
  `T054_Valor_Cambio_Dolar` decimal(19,4) default '0.0000',
  `T054_Flag_Fechado` varchar(1) default 'N',
  `T054_Flag_Almoxarifado` varchar(1) default 'N',
  `T054_Flag_Status` int(1) default '0',
  `T054_Flag_Isento_II` varchar(1) default 'N',
  `T054_Data_Emissao_BL` date default NULL,
  `T054_Numero_BL` varchar(20) default NULL,
  `T054_Valor_Total_DI` decimal(10,2) default '0.00',
  `T054_Valor_Total_DI_Real` decimal(10,2) default '0.00',
  `T054_Valor_Total2_Real` decimal(10,2) default '0.00',
  `T054_Valor_Total_Diferenca` decimal(10,2) default '0.00',
  `T054_Valor_Cambio_Real` decimal(19,4) default '0.0000',
  `T054_Data_Envio_Contabilidade` date default NULL,
  `T054_Data_Retorno_Contabilidade` date default NULL,
  `T054_Numero_Antigo` int(10) default NULL,
  PRIMARY KEY  (`T054_Id`),
  KEY `T054_FKIndex1` (`T054_D036_Id`),
  KEY `T054_FKIndex2` (`T054_C007_Id`),
  KEY `T054_FKIndex3` (`T054_C008_Id`),
  KEY `T054_FKIndex4` (`T054_D018_Id`),
  KEY `T054_FKIndex5` (`T054_D022_Id`),
  KEY `T054_FKIndex6` (`T054_D044_Id`),
  KEY `T054_FKIndex7` (`T054_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t054`
--

LOCK TABLES `t054` WRITE;
/*!40000 ALTER TABLE `t054` DISABLE KEYS */;
/*!40000 ALTER TABLE `t054` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t055`
--

DROP TABLE IF EXISTS `t055`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t055` (
  `T055_Id` int(10) unsigned NOT NULL auto_increment,
  `T055_T060_Id` int(10) unsigned default NULL,
  `T055_T054_Id` int(10) unsigned default NULL,
  `T055_D037_Id` int(10) unsigned default NULL,
  `T055_D005_Id` int(10) unsigned default NULL,
  `T055_D009_Id` int(10) unsigned default NULL,
  `T055_T053_Id` int(10) unsigned default NULL,
  `T055_C004_Id` int(10) unsigned default '0',
  `T055_Codigo_Produto` varchar(30) default NULL,
  `T055_Codigo_Produto_Fornecedor` varchar(20) default NULL,
  `T055_Descricao_Produto` varchar(30) default NULL,
  `T055_Classificacao_Fiscal` varchar(12) default NULL,
  `T055_Prazo_Entrega` int(10) default '0',
  `T055_Quantidade` int(10) default '0',
  `T055_Valor_Custo_Unitario_Anterior` decimal(19,5) default '0.00000',
  `T055_Valor_Custo_Unitario` decimal(19,5) default '0.00000',
  `T055_Valor_Total_Custo` decimal(19,2) default '0.00',
  `T055_Valor_Preco_Unitario_Nota_Fiscal` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Preco_Nota_Fiscal` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal` decimal(19,6) default '0.000000',
  `T055_Valor_Custo_Unitario_Original` decimal(19,5) default '0.00000',
  `T055_Valor_Total_Custo_Original` decimal(19,2) default '0.00',
  `T055_Valor_Base_IPI` decimal(19,6) default '0.000000',
  `T055_Valor_Base_ICMS_Sem_Reducao` decimal(19,6) default '0.000000',
  `T055_Valor_Base_ICMS_Normal` decimal(19,6) default '0.000000',
  `T055_Valor_Base_ICMS_Presumido` decimal(19,6) default '0.000000',
  `T055_Valor_Base_COFINS` decimal(19,6) default '0.000000',
  `T055_Valor_Base_PIS` decimal(19,6) default '0.000000',
  `T055_Valor_Base_Extra` decimal(19,6) default '0.000000',
  `T055_Valor_Base_Imposto_Importacao` decimal(19,6) default '0.000000',
  `T055_Aliquota_IPI` decimal(5,2) default '0.00',
  `T055_Aliquota_Imposto_Importacao` decimal(5,2) default '0.00',
  `T055_Aliquota_COFINS` decimal(5,2) default '0.00',
  `T055_Aliquota_PIS` decimal(5,2) default '0.00',
  `T055_Aliquota_Extra` decimal(5,2) default '0.00',
  `T055_Valor_ICMS` decimal(19,6) default '0.000000',
  `T055_Valor_ICMS_Presumido` decimal(19,6) default '0.000000',
  `T055_Valor_ICMS_Pagar` decimal(19,6) default '0.000000',
  `T055_Valor_IPI` decimal(19,6) default '0.000000',
  `T055_Valor_Imposto_Importacao` decimal(19,6) default '0.000000',
  `T055_Valor_COFINS` decimal(19,6) default '0.000000',
  `T055_Valor_PIS` decimal(19,6) default '0.000000',
  `T055_Valor_Extra` decimal(19,6) default '0.000000',
  `T055_Valor_Frete` decimal(19,6) default '0.000000',
  `T055_Valor_Despesas` decimal(19,6) default '0.000000',
  `T055_Valor_Despesas_Fatura` decimal(19,6) default '0.000000',
  `T055_Valor_Aduaneira` decimal(19,6) default '0.000000',
  `T055_Valor_Despesas_Aduaneira` decimal(19,6) default '0.000000',
  `T055_Valor_Custo_Final_Unitario` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Custo_Final` decimal(19,2) default '0.00',
  `T055_Peso_Kg_Liquido_Unitario` decimal(19,6) default '0.000000',
  `T055_Peso_Kg_Liquido_Total` decimal(19,6) default '0.000000',
  `T055_Peso_Lb_Liquido_Unitario` decimal(19,3) default '0.000',
  `T055_Peso_Lb_Liquido_Total` decimal(19,3) default '0.000',
  `T055_Multiplicador_X` decimal(19,6) default '0.000000',
  `T055_Multiplicador_Y` decimal(19,6) default '0.000000',
  `T055_Data_Transferencia_Estoque` date default NULL,
  `T055_Valor_Total_ICMS` decimal(19,6) default '0.000000',
  `T055_Valor_Total_ICMS_Presumido` decimal(19,6) default '0.000000',
  `T055_Valor_Total_ICMS_Pagar` decimal(19,6) default '0.000000',
  `T055_Valor_Total_IPI` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Imposto_Importacao` decimal(19,6) default '0.000000',
  `T055_Valor_Total_COFINS` decimal(19,6) default '0.000000',
  `T055_Valor_Total_PIS` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Extra` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Frete` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Despesas` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Despesas_Fatura` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Aduaneira` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Despesas_Aduaneira` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_IPI` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_ICMS_Sem_Reducao` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_ICMS_Normal` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_ICMS_Presumido` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_COFINS` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_PIS` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_Extra` decimal(19,6) default '0.000000',
  `T055_Valor_Total_Base_Imposto_Importacao` decimal(19,6) default '0.000000',
  `T055_Peso_Kg_Liquido_Unitario_Fatura` decimal(19,6) default '0.000000',
  `T055_Peso_Kg_Liquido_Total_Fatura` decimal(19,6) default '0.000000',
  PRIMARY KEY  (`T055_Id`),
  KEY `T055_IDX_CODIGO_PRODUTO` (`T055_Codigo_Produto`),
  KEY `T055_IDX_EMPRESAS` (`T055_C004_Id`),
  KEY `T055_FKIndex1` (`T055_T054_Id`),
  KEY `T055_FKIndex2` (`T055_T053_Id`),
  KEY `T055_FKIndex3` (`T055_D037_Id`),
  KEY `T055_FKIndex4` (`T055_D009_Id`),
  KEY `T055_FKIndex5` (`T055_T060_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t055`
--

LOCK TABLES `t055` WRITE;
/*!40000 ALTER TABLE `t055` DISABLE KEYS */;
/*!40000 ALTER TABLE `t055` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t056`
--

DROP TABLE IF EXISTS `t056`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t056` (
  `T056_Id` int(10) unsigned NOT NULL auto_increment,
  `T056_T054_Id` int(10) unsigned default NULL,
  `T056_D024_Id` int(10) unsigned default NULL,
  `T056_D037_Id` int(10) unsigned default NULL,
  `T056_D044_Id` int(10) unsigned default NULL,
  `T056_D045_Id` int(10) unsigned default NULL,
  `T056_T060_Id` int(10) unsigned default NULL,
  `T056_D014_Id` int(10) unsigned default NULL,
  `T056_T076_Id` int(10) default NULL,
  `T056_Descricao` varchar(30) default NULL,
  `T056_Flag_Frete` varchar(1) default 'N',
  `T056_Flag_Calcular_Aduaneira` varchar(1) default 'N',
  `T056_Flag_Calcular_ICMS` varchar(1) default 'N',
  `T056_Flag_Calcular_PIS_COFINS` varchar(1) default 'N',
  `T056_Flag_Calcular_IPI` varchar(1) default 'N',
  `T056_Flag_Calcular_Imposto_Importacao` varchar(1) default 'N',
  `T056_Valor_Unitario_Despesa` decimal(19,6) default '0.000000',
  `T056_Data_Cambio` date default NULL,
  `T056_Valor_Cambio` decimal(10,4) default '0.0000',
  `T056_Quantidade` decimal(19,3) default '0.000',
  `T056_Valor_Total_Despesa` decimal(19,2) default '0.00',
  `T056_Valor_Total_Despesa_Real` decimal(19,2) default '0.00',
  `T056_Observacao_Formula` text,
  `T056_Data_Vencimento` date default NULL,
  `T056_Numero_Documento` varchar(20) default NULL,
  `T056_Flag_Tipo` varchar(15) default NULL,
  `T056_Flag_Confirmado` varchar(1) default NULL,
  `T056_Data_Cambio_Pagamento` date default NULL,
  `T056_Valor_Cambio_Pagamento` decimal(10,4) default NULL,
  `T056_Valor_Total_Despesa_Pagamento` decimal(10,2) default NULL,
  `T056_Valor_Total_Despesa_Real_Pagamento` decimal(10,2) default NULL,
  PRIMARY KEY  (`T056_Id`),
  KEY `T056_FKIndex1` (`T056_T054_Id`),
  KEY `T056_FKIndex2` (`T056_D045_Id`),
  KEY `T056_FKIndex3` (`T056_D044_Id`),
  KEY `T056_FKIndex4` (`T056_T060_Id`),
  KEY `T056_FKIndex5` (`T056_D014_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t056`
--

LOCK TABLES `t056` WRITE;
/*!40000 ALTER TABLE `t056` DISABLE KEYS */;
/*!40000 ALTER TABLE `t056` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t057`
--

DROP TABLE IF EXISTS `t057`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t057` (
  `T057_Id` int(10) unsigned NOT NULL auto_increment,
  `T057_T055_Id` int(10) unsigned default NULL,
  `T057_T056_Id` int(10) unsigned default NULL,
  `T057_T060_Id` int(10) unsigned default NULL,
  `T057_T076_Id` int(10) unsigned default NULL,
  `T057_T075_Id` int(10) unsigned default NULL,
  `T057_Descricao` varchar(40) default NULL,
  `T057_Flag_Frete` varchar(1) default 'N',
  `T057_Flag_Calcular_Aduaneira` varchar(1) default 'N',
  `T057_Flag_Calcular_ICMS` varchar(1) default 'N',
  `T057_Flag_Calcular_PIS_COFINS` varchar(1) default 'N',
  `T057_Flag_Calcular_IPI` varchar(1) default 'N',
  `T057_Flag_Calcular_Imposto_Importacao` varchar(1) default 'N',
  `T057_Valor` decimal(19,6) default '0.000000',
  `T057_Valor_Total` decimal(19,6) default '0.000000',
  `T057_Valor_Total_Despesa` decimal(19,2) default '0.00',
  PRIMARY KEY  (`T057_Id`),
  KEY `T057_FKIndex1` (`T057_T055_Id`),
  KEY `T057_FKIndex2` (`T057_T056_Id`),
  KEY `T057_FKIndex3` (`T057_T076_Id`),
  KEY `T057_FKIndex4` (`T057_T075_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t057`
--

LOCK TABLES `t057` WRITE;
/*!40000 ALTER TABLE `t057` DISABLE KEYS */;
/*!40000 ALTER TABLE `t057` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t058`
--

DROP TABLE IF EXISTS `t058`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t058` (
  `T058_Id` int(10) unsigned NOT NULL auto_increment,
  `T058_T049_Id` int(10) unsigned default NULL,
  `T058_T050_Id` int(10) unsigned default NULL,
  `T058_T051_Id` int(10) unsigned default NULL,
  `T058_D009_Id` int(10) unsigned default NULL,
  `T058_C004_Id` int(10) unsigned default NULL,
  `T058_Flag_Gerar_OC` int(1) unsigned default '2',
  `T058_Valor_Custo_Unitario` decimal(14,4) default '0.0000',
  `T058_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T058_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `T058_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T058_Valor_Base_IPI` decimal(10,2) default '0.00',
  `T058_Aliquota_IPI` decimal(5,2) default '0.00',
  `T058_Valor_IPI` decimal(10,2) default '0.00',
  `T058_Prazo_Entrega` int(10) default '0',
  PRIMARY KEY  (`T058_Id`),
  KEY `T058_FKIndex1` (`T058_D009_Id`),
  KEY `T058_FKIndex2` (`T058_T050_Id`),
  KEY `T058_FKIndex3` (`T058_T051_Id`),
  KEY `T058_FKIndex4` (`T058_T049_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t058`
--

LOCK TABLES `t058` WRITE;
/*!40000 ALTER TABLE `t058` DISABLE KEYS */;
/*!40000 ALTER TABLE `t058` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t059`
--

DROP TABLE IF EXISTS `t059`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t059` (
  `T059_Id` int(10) unsigned NOT NULL auto_increment,
  `T059_T009_Id` int(10) unsigned default NULL,
  `T059_T043_Id` int(10) unsigned default NULL,
  `T059_T010_Id` int(10) unsigned default NULL,
  `T059_D009_Id` int(10) unsigned default NULL,
  `T059_D005_Id` int(10) unsigned default NULL,
  `T059_C004_Id` int(10) unsigned default NULL,
  `T059_Flag_Gerar_OC` int(1) unsigned default '2',
  `T059_Valor_Custo_Unitario` decimal(14,4) default '0.0000',
  `T059_Valor_Total_Custo` decimal(10,2) default '0.00',
  `T059_Codigo_Produto_Fornecedor` varchar(30) default NULL,
  `T059_Percentual_Reducao_ICMS` decimal(5,2) default '0.00',
  `T059_Valor_Base_ICMS` decimal(10,2) default '0.00',
  `T059_Aliquota_ICMS` decimal(5,2) default '0.00',
  `T059_Valor_ICMS` decimal(10,2) default '0.00',
  `T059_Percentual_Reducao_IPI` decimal(5,2) default '0.00',
  `T059_Valor_Base_IPI` decimal(10,2) default '0.00',
  `T059_Aliquota_IPI` decimal(5,2) default '0.00',
  `T059_Valor_IPI` decimal(10,2) default '0.00',
  `T059_Prazo_Entrega` int(10) default '0',
  `T059_Classificacao_Fiscal` varchar(12) default '0',
  `T059_Flag_Imprimir` varchar(1) default 'N',
  `T059_Codigo_Produto` varchar(30) default NULL,
  `T059_Quantidade` decimal(10,3) default NULL,
  `T059_Descricao_Produto` varchar(50) default NULL,
  `T059_D037_Id` int(11) default NULL,
  PRIMARY KEY  (`T059_Id`),
  KEY `T059_FKIndex1` (`T059_D009_Id`),
  KEY `T059_FKIndex2` (`T059_T043_Id`),
  KEY `T059_FKIndex3` (`T059_T010_Id`),
  KEY `T059_FKIndex4` (`T059_D005_Id`),
  KEY `T059_FKIndex5` (`T059_T009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t059`
--

LOCK TABLES `t059` WRITE;
/*!40000 ALTER TABLE `t059` DISABLE KEYS */;
/*!40000 ALTER TABLE `t059` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t060`
--

DROP TABLE IF EXISTS `t060`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t060` (
  `T060_Id` int(10) unsigned NOT NULL auto_increment,
  `T060_T054_Id` int(10) unsigned default NULL,
  `T060_D024_Id` int(10) unsigned default NULL,
  `T060_D044_Id` int(10) unsigned default NULL,
  `T060_C004_Id` int(10) unsigned default NULL,
  `T060_Numero_Fatura` varchar(30) default NULL,
  `T060_Data_Emissao` date default NULL,
  `T060_Valor_Fatura` decimal(19,2) default '0.00',
  `T060_Data_Vencimento_Antecipado` date default NULL,
  `T060_Peso_Liquido` decimal(19,3) default '0.000',
  `T060_Valor_Antecipado` decimal(10,2) default NULL,
  `T060_Valor_A_Vista` decimal(10,2) default NULL,
  `T060_Valor_A_Prazo` decimal(10,2) default NULL,
  `T060_Data_Vencimento_A_Vista` date default NULL,
  `T060_Data_Vencimento_A_Prazo` date default NULL,
  PRIMARY KEY  (`T060_Id`),
  KEY `T060_FKIndex1` (`T060_T054_Id`),
  KEY `T060_FKIndex2` (`T060_D024_Id`),
  KEY `T060_FKIndex3` (`T060_D044_Id`),
  KEY `T060_FKIndex4` (`T060_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t060`
--

LOCK TABLES `t060` WRITE;
/*!40000 ALTER TABLE `t060` DISABLE KEYS */;
/*!40000 ALTER TABLE `t060` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t061`
--

DROP TABLE IF EXISTS `t061`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t061` (
  `T061_Id` int(10) unsigned NOT NULL auto_increment,
  `T061_D014_Id` int(10) unsigned default NULL,
  `T061_T054_Id` int(10) unsigned default NULL,
  `T061_D024_Id` int(10) unsigned default NULL,
  `T061_D044_Id` int(10) unsigned default NULL,
  `T061_C004_Id` int(10) unsigned default NULL,
  `T061_Data_Vencimento` date default NULL,
  `T061_Numero_Contrato_Cambio` varchar(14) default NULL,
  `T061_Valor_Contrato` decimal(19,2) default '0.00',
  `T061_Valor_Cambio` decimal(10,4) default '0.0000',
  `T061_Valor_Real` decimal(19,2) default '0.00',
  PRIMARY KEY  (`T061_Id`),
  KEY `T061_FKIndex1` (`T061_T054_Id`),
  KEY `T061_FKIndex2` (`T061_D024_Id`),
  KEY `T061_FKIndex3` (`T061_D044_Id`),
  KEY `T061_FKIndex4` (`T061_C004_Id`),
  KEY `T061_FKIndex5` (`T061_D014_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t061`
--

LOCK TABLES `t061` WRITE;
/*!40000 ALTER TABLE `t061` DISABLE KEYS */;
/*!40000 ALTER TABLE `t061` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t062`
--

DROP TABLE IF EXISTS `t062`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t062` (
  `T062_Id` int(10) NOT NULL auto_increment,
  `T062_D009_Id` int(10) default NULL,
  `T062_T001_Id` int(10) default NULL,
  `T062_Data` date default NULL,
  `T062_Saldo` decimal(10,3) default NULL,
  PRIMARY KEY  (`T062_Id`),
  KEY `IDX_T062_Id` (`T062_Id`),
  KEY `IDX_T062_D009_Id` (`T062_D009_Id`),
  KEY `IDX_T062_T001_Id` (`T062_T001_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t062`
--

LOCK TABLES `t062` WRITE;
/*!40000 ALTER TABLE `t062` DISABLE KEYS */;
/*!40000 ALTER TABLE `t062` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t063`
--

DROP TABLE IF EXISTS `t063`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t063` (
  `T063_Id` int(10) NOT NULL auto_increment,
  `T063_D009_Id` int(10) default NULL,
  `T063_T001_Id` int(10) default NULL,
  `T063_T062_Id` int(10) default NULL,
  `T063_Data` date default NULL,
  `T063_Quantidade` decimal(10,3) default NULL,
  PRIMARY KEY  (`T063_Id`),
  KEY `IDX_T063_Id` (`T063_Id`),
  KEY `IDX_T063_T062_Id` (`T063_T062_Id`),
  KEY `IDX_T063_T001_Id` (`T063_T001_Id`),
  KEY `IDX_T063_D009_Id` (`T063_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t063`
--

LOCK TABLES `t063` WRITE;
/*!40000 ALTER TABLE `t063` DISABLE KEYS */;
/*!40000 ALTER TABLE `t063` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t064`
--

DROP TABLE IF EXISTS `t064`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t064` (
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
  `T064_Flag_Tipo` enum('Orçamento','Pedido') default 'Pedido',
  PRIMARY KEY  (`T064_Id`),
  KEY `T064_IDX_CLIENTE` (`T064_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='PEDIDO INTERNET';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t064`
--

LOCK TABLES `t064` WRITE;
/*!40000 ALTER TABLE `t064` DISABLE KEYS */;
/*!40000 ALTER TABLE `t064` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t065`
--

DROP TABLE IF EXISTS `t065`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t065` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ITENS DO PEDIDO INTERNET';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t065`
--

LOCK TABLES `t065` WRITE;
/*!40000 ALTER TABLE `t065` DISABLE KEYS */;
/*!40000 ALTER TABLE `t065` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t066`
--

DROP TABLE IF EXISTS `t066`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t066` (
  `T066_Id` int(10) NOT NULL auto_increment,
  `T066_D004_Id` int(10) default NULL,
  `T066_D009_Id` int(10) default NULL,
  `T066_D048_Id` int(10) default NULL,
  `T066_Quantidade_Estoque` decimal(10,3) default NULL,
  PRIMARY KEY  (`T066_Id`),
  KEY `T066_Index_Locacao` (`T066_D004_Id`),
  KEY `T066_Index_D009` (`T066_D009_Id`),
  KEY `T066_Index_D048` (`T066_D048_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t066`
--

LOCK TABLES `t066` WRITE;
/*!40000 ALTER TABLE `t066` DISABLE KEYS */;
/*!40000 ALTER TABLE `t066` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t067`
--

DROP TABLE IF EXISTS `t067`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t067` (
  `T067_Id` int(10) NOT NULL auto_increment,
  `T067_T070_Id` int(10) default NULL,
  `T067_Ano` varchar(4) default NULL,
  `T067_Margem_Inicial` decimal(14,2) default NULL,
  `T067_Margem_Final` decimal(14,2) default NULL,
  `T067_Empresa` varchar(5) default NULL,
  `T067_Aliquota_Empresa_Comercial` decimal(5,2) default NULL,
  `T067_Aliquota_Empresa_Industrial` decimal(5,2) default NULL,
  `T067_Aliquota_Empresa_Servicos` decimal(5,2) default NULL,
  PRIMARY KEY  (`T067_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t067`
--

LOCK TABLES `t067` WRITE;
/*!40000 ALTER TABLE `t067` DISABLE KEYS */;
/*!40000 ALTER TABLE `t067` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t068`
--

DROP TABLE IF EXISTS `t068`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t068` (
  `T068_Id` int(10) NOT NULL auto_increment,
  `T068_Legislacao` text,
  `T068_Data_Inicio` date default NULL,
  `T068_Data_Final` date default NULL,
  PRIMARY KEY  (`T068_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t068`
--

LOCK TABLES `t068` WRITE;
/*!40000 ALTER TABLE `t068` DISABLE KEYS */;
/*!40000 ALTER TABLE `t068` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t069`
--

DROP TABLE IF EXISTS `t069`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t069` (
  `T069_Id` int(10) NOT NULL auto_increment,
  `T069_T068_Id` int(10) default NULL,
  `T069_Empresa` varchar(20) default NULL,
  `T069_Faixa_Inicial` decimal(14,2) default NULL,
  `T069_Faixa_Final` decimal(14,2) default NULL,
  `T069_Aliquota` decimal(5,2) default NULL,
  `T069_Valor_Deduzir` decimal(10,2) default NULL,
  PRIMARY KEY  (`T069_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t069`
--

LOCK TABLES `t069` WRITE;
/*!40000 ALTER TABLE `t069` DISABLE KEYS */;
/*!40000 ALTER TABLE `t069` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t070`
--

DROP TABLE IF EXISTS `t070`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t070` (
  `T070_Id` int(10) NOT NULL auto_increment,
  `T070_Observacao` text,
  `T070_Legislacao` text,
  PRIMARY KEY  (`T070_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t070`
--

LOCK TABLES `t070` WRITE;
/*!40000 ALTER TABLE `t070` DISABLE KEYS */;
/*!40000 ALTER TABLE `t070` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t071`
--

DROP TABLE IF EXISTS `t071`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t071` (
  `T071_Id` int(10) NOT NULL auto_increment,
  `T071_T048_Id` int(10) default NULL,
  `T071_D009_Id` int(10) default NULL,
  `T071_Quantidade` decimal(10,2) default NULL,
  `T071_Codigo_Produto` varchar(30) default NULL,
  `T071_Observacao` text,
  PRIMARY KEY  (`T071_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t071`
--

LOCK TABLES `t071` WRITE;
/*!40000 ALTER TABLE `t071` DISABLE KEYS */;
/*!40000 ALTER TABLE `t071` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t072`
--

DROP TABLE IF EXISTS `t072`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t072` (
  `T072_Id` int(10) unsigned NOT NULL auto_increment,
  `T072_T013_Id` int(10) unsigned default NULL,
  `T072_T014_Id` int(10) unsigned default NULL,
  `T072_T007_Id` int(10) unsigned default NULL,
  `T072_T008_Id` int(10) unsigned default NULL,
  `T072_T054_Id` int(10) unsigned default NULL,
  `T072_T055_Id` int(10) unsigned default NULL,
  `T072_D024_Id` int(10) unsigned default NULL,
  `T072_D024_Id_Cliente` int(10) unsigned default NULL,
  `T072_D009_Id` int(10) unsigned default NULL,
  `T072_D009_Id_Original` int(10) unsigned default NULL,
  `T072_D001_Id` int(10) unsigned default NULL,
  `T072_T048_Id` int(10) unsigned default NULL,
  `T072_T066_Id` int(10) unsigned default NULL,
  `T072_D006_Id` int(10) unsigned default NULL,
  `T072_T029_Id` int(10) unsigned default NULL,
  `T072_T030_Id` int(10) unsigned default NULL,
  `T072_C004_Id` int(11) default NULL,
  `T072_Codigo_Produto` varchar(30) default NULL,
  `T072_Data_Lancamento` date default NULL,
  `T072_Descricao_Operacao` varchar(20) default NULL,
  `T072_Quantidade` int(6) NOT NULL default '0',
  `T072_Quantidade_Saldo` int(6) NOT NULL default '0',
  `T072_Flag_Operacao` varchar(1) default NULL,
  `T072_Valor_Preco_Unitario` decimal(14,2) NOT NULL default '0.00',
  `T072_Observacao` varchar(40) default NULL,
  `T072_Valor_Custo_Unitario` decimal(14,2) NOT NULL default '0.00',
  `T072_Codigo_Fornecedor` varchar(6) default NULL,
  `T072_Codigo_Cliente` varchar(6) default NULL,
  `T072_Codigo_Natureza` decimal(5,3) NOT NULL default '0.000',
  `T072_Numero_Nota_Fiscal` varchar(20) default NULL,
  `T072_Motivo` varchar(10) default NULL,
  `T072_Flag_Complemento` varchar(1) default NULL,
  `T072_Demonstrativo_Custo` text,
  `T072_Flag_MMF` varchar(1) default NULL,
  `T072_Flag_Atualiza_Custo` varchar(1) default NULL,
  `T072_Flag_Atualizado` varchar(1) default 'N',
  `T072_Flag_Venda_Compra_Outros` varchar(1) default NULL,
  `T072_Flag_Devolucao` varchar(1) default NULL,
  PRIMARY KEY  (`T072_Id`),
  KEY `T072_Index_C004_Id` (`T072_C004_Id`),
  KEY `T072_Index_Nota_Entrada` (`T072_T013_Id`),
  KEY `T072_Index_Itens_Nota_Entrada` (`T072_T014_Id`),
  KEY `T072_Index_Nota_Saida` (`T072_T007_Id`),
  KEY `T072_Index_Itens_Nota_Saida` (`T072_T008_Id`),
  KEY `T072_Index_Importacao` (`T072_T054_Id`),
  KEY `T072_Index_Itens_Importacao` (`T072_T055_Id`),
  KEY `T072_Index_Codigo_Produto` (`T072_Codigo_Produto`),
  KEY `T072_Index_Codigo_Fornecedor` (`T072_Codigo_Fornecedor`),
  KEY `T072_Index_Descricao_Operacao` (`T072_Descricao_Operacao`),
  KEY `T072_Index_Fornecedores` (`T072_D024_Id`),
  KEY `T072_Index_Clientes` (`T072_D024_Id_Cliente`),
  KEY `T072_Index_Estoque` (`T072_D009_Id`),
  KEY `T072_Index_Estoque_Original` (`T072_D009_Id_Original`),
  KEY `T072_Index_Montagem` (`T072_T048_Id`),
  KEY `T072_Index_Estoque2` (`T072_T066_Id`),
  KEY `T072_Index_Natureza_Operacao` (`T072_D006_Id`),
  KEY `T072_Index_Historico` (`T072_D001_Id`),
  KEY `T072_Index_DE` (`T072_T029_Id`),
  KEY `T072_Index_Itens_DE` (`T072_T030_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t072`
--

LOCK TABLES `t072` WRITE;
/*!40000 ALTER TABLE `t072` DISABLE KEYS */;
/*!40000 ALTER TABLE `t072` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t073`
--

DROP TABLE IF EXISTS `t073`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t073` (
  `T073_Id` int(10) unsigned NOT NULL auto_increment,
  `T073_D024_Id` int(11) NOT NULL default '0',
  `T073_D044_Id` int(11) NOT NULL default '0',
  `T073_T054_Id` int(10) default NULL,
  `T073_Data_Emissao_BL` date default NULL,
  `T073_Numero_BL` varchar(20) default NULL,
  `T073_Peso_Kg_Bruto` decimal(19,3) default '0.000',
  `T073_Peso_Lb_Bruto` decimal(19,3) default '0.000',
  `T073_Valor_Total_BL` decimal(14,2) NOT NULL default '0.00',
  `T073_Flag_Fechado` varchar(1) default 'N',
  `T073_Flag_Pago` varchar(1) default 'N',
  `T073_Transportadora` varchar(50) default NULL,
  PRIMARY KEY  (`T073_Id`),
  KEY `T073_Index_D024` (`T073_D024_Id`),
  KEY `T073_Index_D044` (`T073_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t073`
--

LOCK TABLES `t073` WRITE;
/*!40000 ALTER TABLE `t073` DISABLE KEYS */;
/*!40000 ALTER TABLE `t073` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t074`
--

DROP TABLE IF EXISTS `t074`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t074` (
  `T074_Id` int(10) unsigned NOT NULL auto_increment,
  `T074_T073_Id` int(11) NOT NULL default '0',
  `T074_D057_Id` int(11) NOT NULL default '0',
  `T074_Descricao` varchar(30) default NULL,
  `T074_Valor` decimal(14,2) NOT NULL default '0.00',
  PRIMARY KEY  (`T074_Id`),
  KEY `T074_Index_T054` (`T074_D057_Id`),
  KEY `T074_Index_T075` (`T074_T073_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t074`
--

LOCK TABLES `t074` WRITE;
/*!40000 ALTER TABLE `t074` DISABLE KEYS */;
/*!40000 ALTER TABLE `t074` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t075`
--

DROP TABLE IF EXISTS `t075`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t075` (
  `T075_Id` int(10) unsigned NOT NULL auto_increment,
  `T075_T054_Id` int(11) NOT NULL default '0',
  `T075_Data_Emissao_DI` date default NULL,
  `T075_Numero_DI` varchar(30) default NULL,
  `T075_Valor_Produtos` decimal(14,2) default '0.00',
  `T075_Valor_Frete` decimal(14,2) default '0.00',
  `T075_Valor_CIF` decimal(14,2) default '0.00',
  `T075_Valor_THC` decimal(14,2) default '0.00',
  `T075_Valor_SISCOMEX` decimal(14,2) default '0.00',
  `T075_Valor_Imposto_Importacao` decimal(14,2) default '0.00',
  `T075_Valor_IPI` decimal(14,2) default '0.00',
  `T075_Valor_PIS` decimal(14,2) default '0.00',
  `T075_Valor_COFINS` decimal(14,2) default '0.00',
  `T075_Valor_DI` decimal(14,2) default '0.00',
  PRIMARY KEY  (`T075_Id`),
  KEY `T075_Index_T054` (`T075_T054_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t075`
--

LOCK TABLES `t075` WRITE;
/*!40000 ALTER TABLE `t075` DISABLE KEYS */;
/*!40000 ALTER TABLE `t075` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t076`
--

DROP TABLE IF EXISTS `t076`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t076` (
  `T076_Id` int(10) unsigned NOT NULL auto_increment,
  `T076_T054_Id` int(11) NOT NULL default '0',
  `T076_D024_Id` int(11) NOT NULL default '0',
  `T076_D044_Id` int(11) NOT NULL default '0',
  `T076_D014_Id` int(10) default NULL,
  `T076_D031_Id` int(10) default NULL,
  `T076_Data_Emissao_Recibo` date default NULL,
  `T076_Numero_Recibo` varchar(20) default NULL,
  `T076_Valor_Recibo` decimal(14,2) NOT NULL default '0.00',
  `T076_Flag_Tipo` varchar(15) default NULL,
  `T076_Flag_Pagamento_Adiantado` varchar(1) default NULL,
  `T076_Flag_Fechado` varchar(1) default NULL,
  `T076_Data_Vencimento` date default NULL,
  `T076_Observacao` varchar(40) default NULL,
  PRIMARY KEY  (`T076_Id`),
  KEY `T076_Index_T076` (`T076_T054_Id`),
  KEY `T076_Index_D024` (`T076_D024_Id`),
  KEY `T076_Index_D044` (`T076_D044_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t076`
--

LOCK TABLES `t076` WRITE;
/*!40000 ALTER TABLE `t076` DISABLE KEYS */;
/*!40000 ALTER TABLE `t076` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t077`
--

DROP TABLE IF EXISTS `t077`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t077` (
  `T077_Id` int(10) NOT NULL auto_increment,
  `T077_Mes` varchar(2) default NULL,
  `T077_Ano` varchar(4) default NULL,
  `T077_Valor_Venda_Bruta` decimal(14,2) default NULL,
  `T077_Valor_Devolucoes` decimal(14,2) default NULL,
  `T077_Valor_Venda_Liquida` decimal(14,2) default NULL,
  `T077_Aliquota` decimal(10,2) default NULL,
  `T077_Valor_Base_Calculo` decimal(14,2) default NULL,
  `T077_Valor_Deducao` decimal(14,2) default NULL,
  `T077_Valor_Simples` decimal(14,2) default NULL,
  `T077_C004_Id` int(10) default NULL,
  PRIMARY KEY  (`T077_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t077`
--

LOCK TABLES `t077` WRITE;
/*!40000 ALTER TABLE `t077` DISABLE KEYS */;
/*!40000 ALTER TABLE `t077` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t078`
--

DROP TABLE IF EXISTS `t078`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t078` (
  `T078_Id` int(10) NOT NULL auto_increment,
  `T078_C004_Id` int(10) default NULL,
  `T078_Mes` varchar(2) default NULL,
  `T078_Ano` varchar(4) default NULL,
  `T078_Valor_Venda_Bruta` decimal(14,2) default NULL,
  `T078_Valor_Devolucoes` decimal(14,2) default NULL,
  `T078_Valor_Venda_Liquida` decimal(14,2) default NULL,
  `T078_Valor_Acumulado` decimal(14,2) default NULL,
  `T078_Aliquota` decimal(10,2) default NULL,
  `T078_Valor_Simples` decimal(14,2) default NULL,
  PRIMARY KEY  (`T078_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t078`
--

LOCK TABLES `t078` WRITE;
/*!40000 ALTER TABLE `t078` DISABLE KEYS */;
/*!40000 ALTER TABLE `t078` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t079`
--

DROP TABLE IF EXISTS `t079`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t079` (
  `T079_Id` int(10) unsigned NOT NULL auto_increment,
  `T079_D009_Id` int(11) NOT NULL default '0',
  `T079_Mes` varchar(2) NOT NULL default '00',
  `T079_Ano` varchar(4) NOT NULL default '0000',
  `T079_Quantidade_Estoque` int(6) NOT NULL default '0',
  `T079_Valor_Custo_Medio_Unitario` decimal(14,2) default '0.00',
  `T079_Valor_Custo_Medio_Total` decimal(14,2) default '0.00',
  `T079_Valor_Custo_Unitario` decimal(14,2) default '0.00',
  `T079_Valor_Custo_Total` decimal(14,2) default '0.00',
  PRIMARY KEY  (`T079_Id`),
  KEY `T079_Index_D009` (`T079_D009_Id`),
  KEY `T079_Index_Mes` (`T079_Mes`),
  KEY `T079_Index_Ano` (`T079_Ano`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t079`
--

LOCK TABLES `t079` WRITE;
/*!40000 ALTER TABLE `t079` DISABLE KEYS */;
/*!40000 ALTER TABLE `t079` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t080`
--

DROP TABLE IF EXISTS `t080`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t080` (
  `T080_Id` int(10) unsigned NOT NULL auto_increment,
  `T080_C004_Id` int(10) unsigned NOT NULL default '0',
  `T080_Mes` varchar(2) default NULL,
  `T080_Ano` varchar(4) default NULL,
  `T080_PIS` decimal(5,2) NOT NULL default '0.00',
  `T080_COFINS` decimal(5,2) NOT NULL default '0.00',
  `T080_IR_Mercadoria` decimal(5,2) NOT NULL default '0.00',
  `T080_IR_MO` decimal(5,2) NOT NULL default '0.00',
  `T080_Contribuicao_Social` decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`T080_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t080`
--

LOCK TABLES `t080` WRITE;
/*!40000 ALTER TABLE `t080` DISABLE KEYS */;
/*!40000 ALTER TABLE `t080` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t081`
--

DROP TABLE IF EXISTS `t081`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t081` (
  `T081_Id` int(10) unsigned NOT NULL auto_increment,
  `T081_D009_Id` int(10) unsigned default NULL,
  `T081_C004_Id` int(10) unsigned default NULL,
  `T081_Codigo_Produto` varchar(30) default NULL,
  `T081_Observacao` varchar(200) default NULL,
  `T081_Data` date default NULL,
  `T081_Descricao_Falha` text,
  `T081_Nome_Empresa` varchar(50) default NULL,
  PRIMARY KEY  (`T081_Id`),
  KEY `T081_FKIndex1` (`T081_D009_Id`),
  KEY `T081_FKIndex2` (`T081_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t081`
--

LOCK TABLES `t081` WRITE;
/*!40000 ALTER TABLE `t081` DISABLE KEYS */;
/*!40000 ALTER TABLE `t081` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t082`
--

DROP TABLE IF EXISTS `t082`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t082` (
  `T082_Id` int(10) unsigned NOT NULL auto_increment,
  `T082_C004_Id` int(10) unsigned default NULL,
  `T082_Referencia` varchar(30) default NULL,
  `T082_Nome_Empresa` varchar(50) default NULL,
  `T082_Modulo` varchar(30) default NULL,
  `T082_Descricao_Falha` text,
  `T082_Data` date default NULL,
  PRIMARY KEY  (`T082_Id`),
  KEY `T082_FKIndex1` (`T082_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t082`
--

LOCK TABLES `t082` WRITE;
/*!40000 ALTER TABLE `t082` DISABLE KEYS */;
/*!40000 ALTER TABLE `t082` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t083`
--

DROP TABLE IF EXISTS `t083`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t083` (
  `T083_Id` int(10) NOT NULL auto_increment,
  `T083_D003_Id` int(10) default NULL,
  `T083_C008_Id` int(10) default NULL,
  `T083_C004_Id` int(10) default NULL,
  `T083_C007_Id` int(10) default NULL,
  `T083_Mes` varchar(2) default NULL,
  `T083_Ano` varchar(4) default NULL,
  `T083_Percentual_Comissao` decimal(10,4) default NULL,
  PRIMARY KEY  (`T083_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t083`
--

LOCK TABLES `t083` WRITE;
/*!40000 ALTER TABLE `t083` DISABLE KEYS */;
/*!40000 ALTER TABLE `t083` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t084`
--

DROP TABLE IF EXISTS `t084`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t084` (
  `T084_Id` int(5) NOT NULL auto_increment,
  `T084_C004_Id` int(11) default '0',
  `T084_C007_Id` int(11) default '0',
  `T084_D022_Id` int(11) default '0',
  `T084_D024_Id` int(11) default '0',
  `T084_D006_Id` int(11) default NULL,
  `T084_Cnpj_Transportadora` varchar(20) default NULL,
  `T084_Numero_Conhecimento` varchar(20) default NULL,
  `T084_Valor_Conhecimento` decimal(10,2) default '0.00',
  `T084_Data_Emissao` date default NULL,
  `T084_Data_Vencimento` date default NULL,
  `T084_Flag_Fechado` varchar(1) default 'N',
  `T084_Aliquota_ICMS` decimal(6,2) default '0.00',
  `T084_Valor_Base_ICMS` decimal(10,2) default '0.00',
  `T084_Valor_ICMS` decimal(10,2) default '0.00',
  `T084_Data_Lancamento` date default NULL,
  `T084_Flag_Cancelado` varchar(1) default 'N',
  PRIMARY KEY  (`T084_Id`),
  KEY `T084_Index_C004` (`T084_C004_Id`),
  KEY `T084_Index_C007` (`T084_C007_Id`),
  KEY `T084_Index_D022` (`T084_D022_Id`),
  KEY `T084_Index_D024` (`T084_D024_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2974 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t084`
--

LOCK TABLES `t084` WRITE;
/*!40000 ALTER TABLE `t084` DISABLE KEYS */;
/*!40000 ALTER TABLE `t084` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t085`
--

DROP TABLE IF EXISTS `t085`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t085` (
  `T085_Id` int(5) NOT NULL auto_increment,
  `T085_T013_Id` int(11) default '0',
  `T085_T084_Id` int(11) default '0',
  `T085_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T085_Valor_Total_Nota` decimal(14,2) default '0.00',
  `T085_Valor_Rateio` decimal(14,2) default '0.00',
  `T085_Flag_Fechado` varchar(1) default 'N',
  PRIMARY KEY  (`T085_Id`),
  KEY `T084_Index_T084` (`T085_T084_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4625 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t085`
--

LOCK TABLES `t085` WRITE;
/*!40000 ALTER TABLE `t085` DISABLE KEYS */;
/*!40000 ALTER TABLE `t085` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t086`
--

DROP TABLE IF EXISTS `t086`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t086` (
  `T086_Id` int(10) unsigned NOT NULL auto_increment,
  `T086_T012_Id` int(10) unsigned default NULL,
  `T086_T013_Id` int(10) unsigned default NULL,
  `T086_C004_Id` int(10) unsigned default NULL,
  `T086_T008_Id` int(10) unsigned default NULL,
  `T086_Quantidade` decimal(14,5) default '0.00000',
  `T086_Classificacao_Fiscal` varchar(12) default NULL,
  `T086_Aliquota_IPI` decimal(5,2) default '0.00',
  PRIMARY KEY  (`T086_Id`),
  KEY `T086_FKIndex1` (`T086_T012_Id`),
  KEY `T086_FKIndex2` (`T086_T013_Id`),
  KEY `T086_FKIndex3` (`T086_T008_Id`),
  KEY `T086_FKIndex4` (`T086_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t086`
--

LOCK TABLES `t086` WRITE;
/*!40000 ALTER TABLE `t086` DISABLE KEYS */;
/*!40000 ALTER TABLE `t086` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t087`
--

DROP TABLE IF EXISTS `t087`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t087` (
  `T087_Id` int(10) unsigned NOT NULL auto_increment,
  `T087_D024_Id` int(10) unsigned default NULL,
  `T087_C004_Id` int(10) unsigned default NULL,
  `T087_C007_Id` int(10) unsigned default NULL,
  `T087_Flag_Tipo` varchar(20) default NULL,
  `T087_Flag_Canal` varchar(20) default NULL,
  `T087_Departamento` varchar(50) default NULL,
  `T087_Texto` text,
  `T087_Data_Hora_Inclusao` datetime default NULL,
  `T087_Flag_Alerta` varchar(1) default 'N',
  `T087_Flag_Tipo_Inclusao` varchar(30) default NULL,
  `T087_Flag_Retorno` varchar(1) default 'N',
  `T087_Texto_Retorno` text,
  `T087_Data_Retorno` date default NULL,
  PRIMARY KEY  (`T087_Id`),
  KEY `T087_FKIndex1` (`T087_D024_Id`),
  KEY `T087_FKIndex2` (`T087_C004_Id`),
  KEY `T087_FKIndex3` (`T087_C007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t087`
--

LOCK TABLES `t087` WRITE;
/*!40000 ALTER TABLE `t087` DISABLE KEYS */;
/*!40000 ALTER TABLE `t087` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t088`
--

DROP TABLE IF EXISTS `t088`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t088` (
  `T088_Id` int(10) unsigned NOT NULL auto_increment,
  `T088_T087_Id` int(10) unsigned default NULL,
  `T088_Nome_Arquivo` varchar(200) default NULL,
  `T088_Arquivo` longtext,
  PRIMARY KEY  (`T088_Id`),
  KEY `T088_FKIndex1` (`T088_T087_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t088`
--

LOCK TABLES `t088` WRITE;
/*!40000 ALTER TABLE `t088` DISABLE KEYS */;
/*!40000 ALTER TABLE `t088` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t089`
--

DROP TABLE IF EXISTS `t089`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t089` (
  `T089_Id` int(11) NOT NULL auto_increment,
  `T089_T003_Id` int(11) default '0',
  `T089_D027_Id` int(11) default '0',
  `T089_Numero_Parcela` int(3) default '0',
  `T089_Prazos_Dias` int(5) default '0',
  `T089_Data_Vencimento` date default NULL,
  `T089_Valor_Parcela` decimal(12,2) default '0.00',
  PRIMARY KEY  (`T089_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t089`
--

LOCK TABLES `t089` WRITE;
/*!40000 ALTER TABLE `t089` DISABLE KEYS */;
/*!40000 ALTER TABLE `t089` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t090`
--

DROP TABLE IF EXISTS `t090`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t090` (
  `T090_Id` int(11) NOT NULL auto_increment,
  `T090_T005_Id` int(11) default '0',
  `T090_D027_Id` int(11) default '0',
  `T090_Numero_Parcela` int(3) default '0',
  `T090_Prazos_Dias` int(5) default '0',
  `T090_Data_Vencimento` date default NULL,
  `T090_Valor_Parcela` decimal(12,2) default '0.00',
  PRIMARY KEY  (`T090_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t090`
--

LOCK TABLES `t090` WRITE;
/*!40000 ALTER TABLE `t090` DISABLE KEYS */;
/*!40000 ALTER TABLE `t090` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t091`
--

DROP TABLE IF EXISTS `t091`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t091` (
  `T091_Id` int(11) NOT NULL auto_increment,
  `T091_D009_Id` int(11) default NULL,
  `T091_T066_Id` int(11) default NULL,
  `T091_Data_Hora_Alteracao` datetime default NULL,
  `T091_Local_Antigo` varchar(10) default NULL,
  `T091_Local_Novo` varchar(10) default NULL,
  PRIMARY KEY  (`T091_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=13877 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t091`
--

LOCK TABLES `t091` WRITE;
/*!40000 ALTER TABLE `t091` DISABLE KEYS */;
/*!40000 ALTER TABLE `t091` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t092`
--

DROP TABLE IF EXISTS `t092`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t092` (
  `T092_Id` int(11) NOT NULL auto_increment,
  `T092_C004_Id` int(10) unsigned default NULL,
  `T092_HEADER_Identificacao_Arquivo_Retorno` int(2) default NULL,
  `T092_Nome_Arquivo` varchar(15) default NULL,
  `T092_Data_Importacao` datetime default NULL,
  `T092_Data_Processamento` datetime default NULL,
  `T092_HEADER_Literal_Retorno` varchar(7) default NULL,
  `T092_HEADER_Codigo_Servico` int(2) default NULL,
  `T092_HEADER_Literal_Servico` varchar(15) default NULL,
  `T092_HEADER_Codigo_Empresa` int(20) default NULL,
  `T092_HEADER_Nome_Empresa` varchar(30) default NULL,
  `T092_HEADER_Numero_Bradesco` int(3) default NULL,
  `T092_HEADER_Nome_Banco` varchar(15) default NULL,
  `T092_HEADER_Data_Gravacao_Arquivo` date default NULL,
  `T092_HEADER_Numero_Aviso_Bancario` int(5) default NULL,
  `T092_HEADER_Data_Credito` datetime default NULL,
  `T092_HEADER_Sequencial_Registro` int(6) default NULL,
  `T092_HEADER_String` text,
  `T092_TRAILLER_Identificacao_Registro` int(1) default NULL,
  `T092_TRAILLER_Identificacao_Retorno` int(2) default NULL,
  `T092_TRAILLER_Identificacao_Tipo_Registro` int(2) default NULL,
  `T092_TRAILLER_Codigo_Banco` int(3) default NULL,
  `T092_TRAILLER_Quantidade_Titulos_Cobranca` int(8) default NULL,
  `T092_TRAILLER_Valor_Total_Cobranca` int(14) default NULL,
  `T092_TRAILLER_Numero_Aviso_Bancario` int(8) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_02` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_02` decimal(15,2) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_06` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_06` decimal(15,2) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_060910` decimal(15,2) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_0910` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_0910` decimal(15,2) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_13` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_13` decimal(15,2) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_14` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_14` decimal(15,2) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_12` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_12` decimal(15,2) default NULL,
  `T092_TRAILLER_Qtd_Registros_Ocorrencia_19` int(5) default NULL,
  `T092_TRAILLER_Vlr_Registros_Ocorrencia_19` decimal(15,2) default NULL,
  `T092_TRAILLER_Vlr_Total_Rateios_Efetuados` decimal(17,2) default NULL,
  `T092_TRAILLER_Qtd_Total_Rateios_Efetuados` int(8) default NULL,
  `T092_TRAILLER_Numero_Sequencial_Registro` int(6) default NULL,
  `T092_TRAILLER_String` text,
  PRIMARY KEY  (`T092_Id`),
  KEY `T092_Index_C004_Id` (`T092_C004_Id`),
  CONSTRAINT `FK_T092_C004_Id` FOREIGN KEY (`T092_C004_Id`) REFERENCES `c004` (`C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bradesco Retorno Header e Trailler';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t092`
--

LOCK TABLES `t092` WRITE;
/*!40000 ALTER TABLE `t092` DISABLE KEYS */;
/*!40000 ALTER TABLE `t092` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t093`
--

DROP TABLE IF EXISTS `t093`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t093` (
  `T093_Id` int(11) NOT NULL auto_increment,
  `T093_T092_Id` int(11) default NULL,
  `T093_T002_Id` int(10) unsigned default NULL,
  `T093_C004_Id` int(10) unsigned default NULL,
  `T093_Identificacao_Registro` int(1) default NULL,
  `T093_Tipo_Inscricao_Empresa` int(2) default NULL,
  `T093_Tipo_Inscricao_Empresa_Descricao` varchar(10) default NULL,
  `T093_Numero_Inscricao_Empresa` varchar(14) default NULL,
  `T093_Identificacao_Empresa_Cedente_Banco` varchar(17) default NULL,
  `T093_Numero_Controle_Participante` varchar(25) default NULL,
  `T093_Nosso_Numero` varchar(20) default NULL,
  `T093_Identificacao_Rateio_Credito` char(1) default NULL,
  `T093_Carteira` int(1) default NULL,
  `T093_Identificacao_Ocorrencia` int(2) default NULL,
  `T093_Identificacao_Ocorrencia_msg` varchar(60) default NULL,
  `T093_Data_Ocorrencia_Banco` date default NULL,
  `T093_Numero_Documento` varchar(10) default NULL,
  `T093_Data_Vencimento_Titulo` date default NULL,
  `T093_Valor_Titulo` decimal(15,2) default NULL,
  `T093_Banco_Cobrador` int(3) default NULL,
  `T093_Agencia_Cobradora` int(5) default NULL,
  `T093_Especie_Titulo` varchar(2) default NULL,
  `T093_Valor_Despesas_Cobranca_Ocorrencia_0228` decimal(15,2) default NULL,
  `T093_Valor_Outras_despesas_custas_protesto` decimal(15,2) default NULL,
  `T093_Valor_Juros_Operacao_Atraso` decimal(15,2) default NULL,
  `T093_Valor_IOF_Devido` decimal(15,2) default NULL,
  `T093_Valor_Abatimento_Concedido_Titulo` decimal(15,2) default NULL,
  `T093_Valor_Desconto_Concedido` decimal(15,2) default NULL,
  `T093_Valor_Pago` decimal(15,2) default NULL,
  `T093_Valor_Juros_Mora` decimal(15,2) default NULL,
  `T093_Valor_Outros_Creditos` decimal(15,2) default NULL,
  `T093_Motivo_Codigo_Ocorrencia_19` char(1) default NULL,
  `T093_Motivo_Codigo_Ocorrencia_19_msg` varchar(10) default NULL,
  `T093_Data_Credito` date default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_01` int(2) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_01_msg` varchar(80) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_02` int(2) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_02_msg` varchar(80) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_03` int(2) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_03_msg` varchar(80) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_04` int(2) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_04_msg` varchar(80) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_05` int(2) default NULL,
  `T093_Motivo_Rejeicao_Ocorrencia_05_msg` varchar(80) default NULL,
  `T093_Sequencial_Registro` smallint(6) default NULL,
  `T093_Numero_Linha` smallint(6) default NULL,
  `T093_Flag_Situacao` smallint(6) default NULL,
  `T093_Descricao_Situacao` varchar(50) default NULL,
  `T093_String` text,
  PRIMARY KEY  (`T093_Id`),
  KEY `T093_Index_T092_Id` (`T093_T092_Id`),
  KEY `T093_Index_T002_Id` (`T093_T002_Id`),
  KEY `T093_Index_C004_Id` (`T093_C004_Id`),
  CONSTRAINT `FK_T093_C004_Id` FOREIGN KEY (`T093_C004_Id`) REFERENCES `c004` (`C004_Id`),
  CONSTRAINT `FK_T093_T002_Id` FOREIGN KEY (`T093_T002_Id`) REFERENCES `t002` (`T002_Id`),
  CONSTRAINT `FK_T093_T092_Id` FOREIGN KEY (`T093_T092_Id`) REFERENCES `t092` (`T092_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bradesco Retorno DETALHE';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t093`
--

LOCK TABLES `t093` WRITE;
/*!40000 ALTER TABLE `t093` DISABLE KEYS */;
/*!40000 ALTER TABLE `t093` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t094`
--

DROP TABLE IF EXISTS `t094`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t094` (
  `T094_Id` int(11) NOT NULL auto_increment,
  `T094_D024_Id` int(10) unsigned default NULL,
  `T094_D064_Id` int(11) default NULL,
  `T094_Data_Pesquisa` date default NULL,
  `T094_Contato` varchar(50) default NULL,
  `T094_Telefone` varchar(50) default NULL,
  `T094_Valor_Compra` double(15,2) default NULL,
  `T094_Observacao` text,
  PRIMARY KEY  (`T094_Id`),
  KEY `FK_T094_D024_Id` (`T094_D024_Id`),
  KEY `FK_T094_D064_Id` (`T094_D064_Id`),
  CONSTRAINT `FK_T094_D024_Id` FOREIGN KEY (`T094_D024_Id`) REFERENCES `d024` (`D024_Id`),
  CONSTRAINT `FK_T094_D064_Id` FOREIGN KEY (`T094_D064_Id`) REFERENCES `d064` (`D064_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Cabecalho das Respostas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t094`
--

LOCK TABLES `t094` WRITE;
/*!40000 ALTER TABLE `t094` DISABLE KEYS */;
/*!40000 ALTER TABLE `t094` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t095`
--

DROP TABLE IF EXISTS `t095`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t095` (
  `T095_Id` int(11) NOT NULL auto_increment,
  `T095_D066_Id` int(11) default '0' COMMENT 'Alternativa - Pode ser nulo quando a questao tem apenas uma resposta descritiva',
  `T095_D065_Id` int(11) default '0' COMMENT 'Questao - Existe para identificar a questao que nao tem alternativas.',
  `T095_T094_Id` int(11) NOT NULL default '0' COMMENT 'Cabecalho das Respostas',
  `T095_Resposta` text NOT NULL COMMENT 'Resposta',
  PRIMARY KEY  (`T095_Id`),
  KEY `FK_T095_D066_Id` (`T095_D066_Id`),
  KEY `FK_T095_D065_Id` (`T095_D065_Id`),
  KEY `FK_T095_T094_Id` (`T095_T094_Id`),
  CONSTRAINT `FK_T095_D065_Id` FOREIGN KEY (`T095_D065_Id`) REFERENCES `d065` (`D065_Id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_T095_D066_Id` FOREIGN KEY (`T095_D066_Id`) REFERENCES `d066` (`D066_Id`),
  CONSTRAINT `FK_T095_T094_Id` FOREIGN KEY (`T095_T094_Id`) REFERENCES `t094` (`T094_Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Respostas as Alternativas das Questoes da Enquete';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t095`
--

LOCK TABLES `t095` WRITE;
/*!40000 ALTER TABLE `t095` DISABLE KEYS */;
/*!40000 ALTER TABLE `t095` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t096`
--

DROP TABLE IF EXISTS `t096`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t096` (
  `T096_Id` int(11) NOT NULL auto_increment,
  `T096_D021_Id` int(10) unsigned default NULL,
  `T096_T015_Id` int(10) unsigned default NULL COMMENT 'Este ID sera preenchido pela trigger do T015 apos a insercao na T015',
  `T096_C004_Id` int(10) unsigned default NULL,
  `T096_Flag_Finalizado` enum('S','N') NOT NULL default 'N',
  `T096_Data_Inclusao` date default NULL,
  `T096_Data_Finalizado` date default NULL,
  `T096_Valor_Total` decimal(15,2) NOT NULL default '0.00' COMMENT 'Este valor é atualizado numa trigger do T097',
  PRIMARY KEY  (`T096_Id`),
  KEY `index_T096_T015_Id` (`T096_T015_Id`),
  KEY `FK_T096_D021_Id` (`T096_D021_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabela de Comissoes de Representantes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t096`
--

LOCK TABLES `t096` WRITE;
/*!40000 ALTER TABLE `t096` DISABLE KEYS */;
/*!40000 ALTER TABLE `t096` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t097`
--

DROP TABLE IF EXISTS `t097`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t097` (
  `T097_Id` int(11) NOT NULL auto_increment,
  `T097_T096_Id` int(11) default NULL,
  `T097_T015_Id` int(10) unsigned default NULL COMMENT 'Este campo T015_Id é referente ao debito do item cadastrado. Ou seja diferente da tabela Mestre.',
  `T097_T002_Id` int(10) unsigned default NULL,
  `T097_Data` date default NULL,
  `T097_Observacao` text,
  `T097_Flag_Debito_Credito` enum('D','C') NOT NULL default 'C',
  `T097_Valor_Base_Comissao` decimal(20,2) unsigned default NULL,
  `T097_Valor_Comissao` decimal(15,2) unsigned default NULL,
  `T097_Percentual_Comissao` decimal(3,2) unsigned default NULL,
  PRIMARY KEY  (`T097_Id`),
  KEY `INDEX_T097_T015_Id` (`T097_T015_Id`),
  KEY `INDEX_T097_T002_Id` (`T097_T002_Id`),
  KEY `FK_T097_T096` (`T097_T096_Id`),
  CONSTRAINT `FK_T097_T096` FOREIGN KEY (`T097_T096_Id`) REFERENCES `t096` (`T096_Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabela de Itens da T096 Comissoes. Esta tabela ira incluir D';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t097`
--

LOCK TABLES `t097` WRITE;
/*!40000 ALTER TABLE `t097` DISABLE KEYS */;
/*!40000 ALTER TABLE `t097` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t098`
--

DROP TABLE IF EXISTS `t098`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t098` (
  `T098_Id` int(11) NOT NULL auto_increment,
  `T098_D049_Id` int(11) default NULL,
  `T098_Data` datetime default NULL,
  `T098_Valor_Preco_Revenda` decimal(15,5) default NULL,
  `T098_Valor_Preco_Consumidor_Atacado` decimal(15,5) default NULL,
  `T098_Valor_Preco_Consumidor_Varejo` decimal(15,5) default NULL,
  `T098_C007_Id` int(11) default NULL,
  `T098_D041_Id` int(11) default NULL,
  `T098_Valor_Custo_Unitario` decimal(15,5) default '0.00000',
  `T098_Observacao` longtext,
  `T098_Atualizacao` varchar(10) default NULL,
  PRIMARY KEY  (`T098_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=11958 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t098`
--

LOCK TABLES `t098` WRITE;
/*!40000 ALTER TABLE `t098` DISABLE KEYS */;
/*!40000 ALTER TABLE `t098` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t099`
--

DROP TABLE IF EXISTS `t099`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t099` (
  `T099_Id` int(10) unsigned NOT NULL auto_increment,
  `T099_D024_Id` int(10) unsigned NOT NULL,
  `T099_D009_Id` int(10) unsigned NOT NULL,
  `T099_D037_Id` decimal(10,3) default '0.000',
  `T099_Quantidade` decimal(10,3) default '0.000',
  `T099_Data` date default '0000-00-00',
  `T099_Valor_Preco_Unitario` decimal(10,2) default '0.00',
  `T099_Peso_Unitario` decimal(10,3) default '0.000',
  `T099_Horario_Entrega` varchar(10) default ' ',
  `T099_Qte_UND` decimal(10,3) default '0.000',
  `T099_Qte_PCT` decimal(10,3) default '0.000',
  `T099_Qte_KLG` decimal(10,3) default '0.000',
  `T099_Flag_Cancelado` varchar(1) default ' ',
  `T099_C004_Id` int(10) unsigned default NULL,
  PRIMARY KEY  (`T099_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t099`
--

LOCK TABLES `t099` WRITE;
/*!40000 ALTER TABLE `t099` DISABLE KEYS */;
/*!40000 ALTER TABLE `t099` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t100`
--

DROP TABLE IF EXISTS `t100`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t100` (
  `T100_Id` int(10) unsigned NOT NULL auto_increment,
  `T100_C004_Id` int(11) default NULL,
  `T100_Data_Reprocessamento` datetime default NULL,
  `T100_Data` datetime default NULL,
  `T100_Hora_Inicio` time default NULL,
  `T100_Hora_Termino` time default NULL,
  PRIMARY KEY  (`T100_Id`),
  KEY `T100_Data` (`T100_Data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t100`
--

LOCK TABLES `t100` WRITE;
/*!40000 ALTER TABLE `t100` DISABLE KEYS */;
/*!40000 ALTER TABLE `t100` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t101`
--

DROP TABLE IF EXISTS `t101`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t101` (
  `T101_Id` int(10) unsigned NOT NULL auto_increment,
  `T101_C005_Id` int(10) unsigned default NULL,
  `T101_T100_Id` int(10) unsigned default NULL,
  `T101_T108_Id` int(10) default NULL,
  `T101_C004_Id` int(11) default NULL,
  `T101_Campo_Logico` varchar(50) default NULL,
  `T101_Campo` varchar(50) default NULL,
  `T101_Valor` decimal(19,2) default NULL,
  PRIMARY KEY  (`T101_Id`),
  KEY `T101_FKIndex1` (`T101_T100_Id`),
  KEY `T101_FKIndex2` (`T101_C005_Id`),
  KEY `T101_T108_Id` (`T101_T108_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t101`
--

LOCK TABLES `t101` WRITE;
/*!40000 ALTER TABLE `t101` DISABLE KEYS */;
/*!40000 ALTER TABLE `t101` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t102`
--

DROP TABLE IF EXISTS `t102`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t102` (
  `T102_Id` int(10) unsigned NOT NULL auto_increment,
  `T102_C005_Id` int(10) unsigned default NULL,
  `T102_T100_Id` int(10) unsigned default NULL,
  `T102_C004_Id` int(11) default NULL,
  `T102_T104_Id` int(10) default NULL,
  `T102_Campo_Logico` varchar(50) default NULL,
  `T102_Campo` varchar(50) default NULL,
  `T102_Valor` decimal(19,2) default NULL,
  `T102_Data` date default NULL,
  PRIMARY KEY  (`T102_Id`),
  KEY `T102_FKIndex1` (`T102_T100_Id`),
  KEY `T102_FKIndex2` (`T102_C005_Id`),
  KEY `T102_Data` (`T102_Data`),
  KEY `T102_T104_Id` (`T102_T104_Id`),
  KEY `T102_Id` (`T102_Id`),
  KEY `T102_C004_Id` (`T102_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t102`
--

LOCK TABLES `t102` WRITE;
/*!40000 ALTER TABLE `t102` DISABLE KEYS */;
/*!40000 ALTER TABLE `t102` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t103`
--

DROP TABLE IF EXISTS `t103`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t103` (
  `T103_Id` int(10) unsigned NOT NULL auto_increment,
  `T103_T015_Id` int(10) unsigned default NULL,
  `T103_D031_Id` int(10) unsigned default NULL,
  `T103_T002_Id` int(10) unsigned default NULL,
  `T103_D027_Id` int(10) unsigned default NULL,
  `T103_Flag_Baixar_Lote` enum('S','N') default 'N',
  `T103_Valor_Recebido` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T103_Id`),
  KEY `T103_Index_Id` (`T103_Id`),
  KEY `T103_Index_Portador` (`T103_D027_Id`),
  KEY `T103_Index_Receber` (`T103_T002_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t103`
--

LOCK TABLES `t103` WRITE;
/*!40000 ALTER TABLE `t103` DISABLE KEYS */;
/*!40000 ALTER TABLE `t103` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t104`
--

DROP TABLE IF EXISTS `t104`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t104` (
  `T104_Id` int(10) unsigned NOT NULL auto_increment,
  `T104_T100_Id` int(10) default NULL,
  `T104_C005_Id` int(10) unsigned default NULL,
  `T104_T107_Id` int(10) default NULL,
  `T104_Mes` int(2) unsigned zerofill default NULL,
  `T104_Ano` int(4) default NULL,
  `T104_Valor` decimal(10,2) default NULL,
  PRIMARY KEY  (`T104_Id`),
  KEY `Table_76_FKIndex1` (`T104_C005_Id`),
  KEY `T104_Mes` (`T104_Mes`),
  KEY `T104_Ano` (`T104_Ano`),
  KEY `T104_T107_Id` (`T104_T107_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t104`
--

LOCK TABLES `t104` WRITE;
/*!40000 ALTER TABLE `t104` DISABLE KEYS */;
/*!40000 ALTER TABLE `t104` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t105`
--

DROP TABLE IF EXISTS `t105`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t105` (
  `T105_Id` int(10) unsigned NOT NULL auto_increment,
  `T105_C005_Id` int(10) unsigned default NULL,
  `T105_T101_Id` int(10) unsigned default NULL,
  `T105_T108_Id` int(10) default NULL,
  `T105_Conta_Id` int(10) default NULL,
  `T105_Id_Entidade` int(10) unsigned default NULL,
  `T105_Data` date default NULL,
  `T105_Valor` decimal(19,2) default NULL,
  `T105_Referencia` varchar(50) default NULL,
  PRIMARY KEY  (`T105_Id`),
  KEY `T105_FKIndex1` (`T105_T101_Id`),
  KEY `T105_FKIndex2` (`T105_C005_Id`),
  KEY `T105_Busca` (`T105_T101_Id`,`T105_C005_Id`,`T105_Id_Entidade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t105`
--

LOCK TABLES `t105` WRITE;
/*!40000 ALTER TABLE `t105` DISABLE KEYS */;
/*!40000 ALTER TABLE `t105` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t106`
--

DROP TABLE IF EXISTS `t106`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t106` (
  `T106_Id` int(10) NOT NULL auto_increment,
  `T106_C005_Id` int(10) default NULL,
  `T106_C006_Id` int(10) default NULL,
  PRIMARY KEY  (`T106_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t106`
--

LOCK TABLES `t106` WRITE;
/*!40000 ALTER TABLE `t106` DISABLE KEYS */;
/*!40000 ALTER TABLE `t106` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t107`
--

DROP TABLE IF EXISTS `t107`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t107` (
  `T107_Id` int(10) NOT NULL auto_increment,
  `T107_T100_Id` int(10) default NULL,
  `T107_C005_Id` int(10) default NULL,
  `T107_Ano` varchar(4) default NULL,
  `T107_Valor` decimal(10,2) default NULL,
  PRIMARY KEY  (`T107_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=14271 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t107`
--

LOCK TABLES `t107` WRITE;
/*!40000 ALTER TABLE `t107` DISABLE KEYS */;
/*!40000 ALTER TABLE `t107` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t108`
--

DROP TABLE IF EXISTS `t108`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t108` (
  `T108_Id` int(10) unsigned NOT NULL auto_increment,
  `T108_T100_Id` int(10) default NULL,
  `T108_C005_Id` int(10) unsigned default NULL,
  `T108_T109_Id` int(10) default NULL,
  `T108_C004_Id` int(10) default NULL,
  `T108_D101_Id` int(10) default NULL,
  `T108_Mes` int(2) unsigned zerofill default NULL,
  `T108_Ano` int(4) default NULL,
  `T108_Valor` decimal(10,2) default NULL,
  PRIMARY KEY  (`T108_Id`),
  KEY `Table_76_FKIndex1` (`T108_C005_Id`),
  KEY `T108_Mes` (`T108_Mes`),
  KEY `T108_Ano` (`T108_Ano`),
  KEY `T108_T109_Id` (`T108_T109_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t108`
--

LOCK TABLES `t108` WRITE;
/*!40000 ALTER TABLE `t108` DISABLE KEYS */;
/*!40000 ALTER TABLE `t108` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t109`
--

DROP TABLE IF EXISTS `t109`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t109` (
  `T109_Id` int(10) NOT NULL auto_increment,
  `T109_T100_Id` int(10) default NULL,
  `T109_C005_Id` int(10) default NULL,
  `T109_C004_Id` int(10) default NULL,
  `T109_Ano` varchar(4) default NULL,
  `T109_Valor` decimal(10,2) default NULL,
  PRIMARY KEY  (`T109_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=363 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t109`
--

LOCK TABLES `t109` WRITE;
/*!40000 ALTER TABLE `t109` DISABLE KEYS */;
/*!40000 ALTER TABLE `t109` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t110`
--

DROP TABLE IF EXISTS `t110`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t110` (
  `T110_Id` int(10) unsigned NOT NULL auto_increment,
  `T110_C005_Id` int(10) unsigned default NULL,
  `T110_C014_Id` int(10) default NULL,
  `T110_T100_Id` int(10) unsigned default NULL,
  `T110_T112_Id` int(10) default NULL,
  `T110_C004_Id` int(11) default NULL,
  `T110_Campo_Logico` varchar(50) default NULL,
  `T110_Campo` varchar(50) default NULL,
  `T110_Valor` decimal(19,2) default NULL,
  PRIMARY KEY  (`T110_Id`),
  KEY `T110_FKIndex1` (`T110_T100_Id`),
  KEY `T110_FKIndex2` (`T110_C005_Id`),
  KEY `T110_T112_Id` (`T110_T112_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t110`
--

LOCK TABLES `t110` WRITE;
/*!40000 ALTER TABLE `t110` DISABLE KEYS */;
/*!40000 ALTER TABLE `t110` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t111`
--

DROP TABLE IF EXISTS `t111`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t111` (
  `T111_Id` int(10) unsigned NOT NULL auto_increment,
  `T111_T110_Id` int(10) default NULL,
  `T111_C005_Id` int(10) unsigned default NULL,
  `T111_T112_Id` int(10) default NULL,
  `T111_Conta_Id` int(10) default NULL,
  `T111_Id_Entidade` int(10) unsigned default NULL,
  `T111_Data` date default NULL,
  `T111_Valor` decimal(19,2) default NULL,
  `T111_Referencia` varchar(50) default NULL,
  PRIMARY KEY  (`T111_Id`),
  KEY `Table_75_FKIndex1` (`T111_C005_Id`),
  KEY `T111_FKIndex2` (`T111_T110_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t111`
--

LOCK TABLES `t111` WRITE;
/*!40000 ALTER TABLE `t111` DISABLE KEYS */;
/*!40000 ALTER TABLE `t111` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t112`
--

DROP TABLE IF EXISTS `t112`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t112` (
  `T112_Id` int(10) unsigned NOT NULL auto_increment,
  `T112_T100_Id` int(10) default NULL,
  `T112_C005_Id` int(10) unsigned default NULL,
  `T112_C014_Id` int(10) default NULL,
  `T112_T113_Id` int(10) default NULL,
  `T112_Mes` int(2) unsigned zerofill default NULL,
  `T112_Ano` int(4) default NULL,
  `T112_Valor` decimal(10,2) default NULL,
  `T112_Campo` varchar(30) default NULL,
  PRIMARY KEY  (`T112_Id`),
  KEY `Table_76_FKIndex1` (`T112_C005_Id`),
  KEY `T112_Mes` (`T112_Mes`),
  KEY `T112_Ano` (`T112_Ano`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t112`
--

LOCK TABLES `t112` WRITE;
/*!40000 ALTER TABLE `t112` DISABLE KEYS */;
/*!40000 ALTER TABLE `t112` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t113`
--

DROP TABLE IF EXISTS `t113`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t113` (
  `T113_Id` int(10) NOT NULL auto_increment,
  `T113_T100_Id` int(10) default NULL,
  `T113_C005_Id` int(10) default NULL,
  `T113_C014_Id` int(10) default NULL,
  `T113_Ano` varchar(4) default NULL,
  `T113_Valor` decimal(10,2) default NULL,
  `T113_Campo` varchar(30) default NULL,
  PRIMARY KEY  (`T113_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t113`
--

LOCK TABLES `t113` WRITE;
/*!40000 ALTER TABLE `t113` DISABLE KEYS */;
/*!40000 ALTER TABLE `t113` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t114`
--

DROP TABLE IF EXISTS `t114`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t114` (
  `T114_Id` int(10) NOT NULL auto_increment,
  `T114_D101_Id` int(10) default NULL,
  `T114_C004_Id` int(10) default NULL,
  `T114_Data` date default NULL,
  `T114_Valor_Banco_Anterior` decimal(10,2) default NULL,
  `T114_Valor_Recebido` decimal(10,2) default NULL,
  `T114_Valor_Pago` decimal(10,2) default NULL,
  `T114_Valor_Banco_Final` decimal(10,2) default NULL,
  `T114_Valor_Vendas` decimal(10,2) default NULL,
  `T114_Valor_Compras` decimal(10,2) default NULL,
  `T114_Valor_Venda_Sem_Icms` decimal(10,2) default NULL,
  `T114_Valor_CMV` decimal(10,2) default NULL,
  `T114_IPV` decimal(10,4) default NULL,
  `T114_Valor_Estoque_Normal` decimal(10,2) default NULL,
  `T114_Valor_Estoque_Custo_Medio` decimal(10,2) default NULL,
  `T114_Valor_Estoque_Dolar` decimal(10,2) default NULL,
  `T114_Valor_Estoque_Parado_1_Ano` decimal(10,2) default NULL,
  `T114_Valor_Estoque_Parado_2_Ano` decimal(10,2) default NULL,
  `T114_Valor_Receber` decimal(10,2) default NULL,
  `T114_Valor_Pagar` decimal(10,2) default NULL,
  PRIMARY KEY  (`T114_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=964 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t114`
--

LOCK TABLES `t114` WRITE;
/*!40000 ALTER TABLE `t114` DISABLE KEYS */;
/*!40000 ALTER TABLE `t114` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t115`
--

DROP TABLE IF EXISTS `t115`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t115` (
  `T115_Id` int(10) NOT NULL auto_increment,
  `T115_T116_Id` int(10) default NULL,
  `T115_C004_Id` int(10) default NULL,
  `T115_Data` date default NULL,
  `T115_Mes` varchar(2) default NULL,
  `T115_Ano` varchar(4) default NULL,
  `T115_Valor_Receber` decimal(10,2) default NULL,
  `T115_Valor_Recebido` decimal(10,2) default NULL,
  `T115_Valor_Pagar` decimal(10,2) default NULL,
  `T115_Valor_Pago` decimal(10,2) default NULL,
  `T115_Valor_Saldo_Banco` decimal(10,2) default NULL,
  `T115_Valor_Saldo` decimal(10,2) default NULL,
  `T115_Valor_Saldo_Acumulado` decimal(10,2) default NULL,
  PRIMARY KEY  (`T115_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=15902 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t115`
--

LOCK TABLES `t115` WRITE;
/*!40000 ALTER TABLE `t115` DISABLE KEYS */;
/*!40000 ALTER TABLE `t115` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t116`
--

DROP TABLE IF EXISTS `t116`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t116` (
  `T116_Id` int(10) NOT NULL auto_increment,
  `T116_D101_Id` int(10) default NULL,
  `T116_C004_Id` int(10) default NULL,
  `T116_Data` date default NULL,
  `T116_Valor_Receber` decimal(10,2) default NULL,
  `T116_Valor_Pagar` decimal(10,2) default NULL,
  `T116_Valor_Saldo_Banco` decimal(10,2) default NULL,
  `T116_Valor_Saldo` decimal(10,2) default NULL,
  `T116_Valor_Diferenca` decimal(10,2) default NULL,
  `T116_Observacao` text,
  PRIMARY KEY  (`T116_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=870 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t116`
--

LOCK TABLES `t116` WRITE;
/*!40000 ALTER TABLE `t116` DISABLE KEYS */;
/*!40000 ALTER TABLE `t116` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t117`
--

DROP TABLE IF EXISTS `t117`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t117` (
  `T117_Id` int(10) unsigned NOT NULL auto_increment,
  `T117_T035_Id` int(10) unsigned default NULL,
  `T117_C004_Id` int(10) unsigned default NULL,
  `T117_D024_Id` int(10) unsigned default NULL,
  `T117_D007_Id` int(10) unsigned default NULL,
  `T117_Data_Emissao` date default '0000-00-00',
  `T117_Data_Vencimento` date default '0000-00-00',
  `T117_Data_Pagamento` date default '0000-00-00',
  `T117_Valor_Total` decimal(10,2) default '0.00',
  `T117_Instrucoes` text,
  `T117_Codigo_Barras` varchar(200) default NULL,
  `T117_Nosso_Numero` varchar(30) default NULL,
  `T117_Aceite` enum('S','N') default 'N',
  `T117_Especie` varchar(30) default NULL,
  `T117_Especie_Documento` varchar(30) default NULL,
  `T117_Uso_Banco` varchar(30) default NULL,
  `T117_Percentual_Juros_Dia` decimal(10,2) default '0.00',
  `T117_Percentual_Multa` decimal(10,2) default '0.00',
  `T117_Linha_Digitavel` varchar(100) default NULL,
  `T117_Codigo_Cedente` int(10) default '0',
  `T117_Flag_Cancelado` enum('S','N') default 'N',
  `T117_Flag_Layout` enum('S','N') default 'N',
  `T117_Flag_Com_Registro` enum('S','N') default 'N',
  `T117_Flag_Enviar_Remessa` enum('S','N') default 'N',
  PRIMARY KEY  (`T117_Id`),
  KEY `Fk_T117_C004` (`T117_C004_Id`),
  KEY `Fk_T117_D007` (`T117_D007_Id`),
  KEY `Fk_T117_D024` (`T117_D024_Id`),
  CONSTRAINT `Fk_T117_C004` FOREIGN KEY (`T117_C004_Id`) REFERENCES `c004` (`C004_Id`),
  CONSTRAINT `Fk_T117_D007` FOREIGN KEY (`T117_D007_Id`) REFERENCES `d007` (`D007_Id`),
  CONSTRAINT `Fk_T117_D024` FOREIGN KEY (`T117_D024_Id`) REFERENCES `d024` (`D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t117`
--

LOCK TABLES `t117` WRITE;
/*!40000 ALTER TABLE `t117` DISABLE KEYS */;
/*!40000 ALTER TABLE `t117` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t118`
--

DROP TABLE IF EXISTS `t118`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t118` (
  `T118_Id` int(11) NOT NULL auto_increment,
  `T118_T007_Id` int(10) unsigned default '0',
  `T118_D027_Id` int(10) unsigned default '0',
  `T118_Numero_Parcela` int(3) default '0',
  `T118_Prazos_Dias` int(5) default '0',
  `T118_Data_Vencimento` date default NULL,
  `T118_Valor_Parcela` decimal(12,2) default '0.00',
  PRIMARY KEY  (`T118_Id`),
  KEY `Fk_T118_T007` (`T118_T007_Id`),
  CONSTRAINT `Fk_T118_T007` FOREIGN KEY (`T118_T007_Id`) REFERENCES `t007` (`T007_Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t118`
--

LOCK TABLES `t118` WRITE;
/*!40000 ALTER TABLE `t118` DISABLE KEYS */;
/*!40000 ALTER TABLE `t118` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t119`
--

DROP TABLE IF EXISTS `t119`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t119` (
  `T119_Id` bigint(20) unsigned NOT NULL auto_increment,
  `T119_D007_Id` int(10) unsigned default NULL,
  `T119_Data_Abertura` datetime default '0000-00-00 00:00:00',
  `T119_Data_Fechamento` datetime default '0000-00-00 00:00:00',
  `T119_Valor_Saldo_Inicial` decimal(10,2) NOT NULL default '0.00',
  `T119_Valor_Saldo_Final` decimal(10,2) default '0.00',
  `T119_Observacao` text,
  `T119_C007_Id` int(10) default NULL,
  PRIMARY KEY  (`T119_Id`),
  KEY `IDX_T119_D007_Id` (`T119_D007_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ABERTURA E FECHAMENTO DE CAIXA';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t119`
--

LOCK TABLES `t119` WRITE;
/*!40000 ALTER TABLE `t119` DISABLE KEYS */;
/*!40000 ALTER TABLE `t119` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t120`
--

DROP TABLE IF EXISTS `t120`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t120` (
  `T120_Id` int(10) unsigned NOT NULL auto_increment,
  `T120_C004_Id` int(10) unsigned default NULL,
  `T120_C007_Id` int(11) unsigned default NULL,
  `T120_D024_Id` int(10) unsigned default NULL,
  `T120_Numero_Contrato` varchar(30) default NULL,
  `T120_Data_Contrato` date default '0000-00-00',
  `T120_Data_Vencimento` date default '0000-00-00',
  `T120_Flag_Ativo` enum('S','N') default 'S',
  `T120_Valor_Compra_Contratada` decimal(10,2) default '0.00',
  `T120_Observacao` text,
  PRIMARY KEY  (`T120_Id`),
  KEY `IDX_T120_D024_Id` (`T120_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CONTRATO DE COMODATO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t120`
--

LOCK TABLES `t120` WRITE;
/*!40000 ALTER TABLE `t120` DISABLE KEYS */;
/*!40000 ALTER TABLE `t120` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t121`
--

DROP TABLE IF EXISTS `t121`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t121` (
  `T121_Id` int(10) unsigned NOT NULL auto_increment,
  `T121_T120_Id` int(10) unsigned default NULL,
  `T121_D009_Id` int(10) unsigned default NULL,
  `T121_Data_Inclusao` datetime default '0000-00-00 00:00:00',
  `T121_Data_Entrega` date default '0000-00-00',
  `T121_Data_Devolucao` date default '0000-00-00',
  `T121_Valor` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T121_Id`),
  KEY `IDX_T121_D009_Id` (`T121_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ITENS DO CONTRATO DE COMODATO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t121`
--

LOCK TABLES `t121` WRITE;
/*!40000 ALTER TABLE `t121` DISABLE KEYS */;
/*!40000 ALTER TABLE `t121` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t122`
--

DROP TABLE IF EXISTS `t122`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t122` (
  `T122_Id` int(10) unsigned NOT NULL auto_increment,
  `T122_C004_Id` int(10) unsigned default NULL,
  `T122_C007_Id` int(11) unsigned default NULL,
  `T122_D024_Id` int(10) unsigned default NULL,
  `T122_Data` date default '0000-00-00',
  `T122_Observacao` text,
  PRIMARY KEY  (`T122_Id`),
  KEY `IDX_T122_D024_Id` (`T122_D024_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ORDEM DE SERVICO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t122`
--

LOCK TABLES `t122` WRITE;
/*!40000 ALTER TABLE `t122` DISABLE KEYS */;
/*!40000 ALTER TABLE `t122` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t123`
--

DROP TABLE IF EXISTS `t123`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t123` (
  `T123_Id` int(10) unsigned NOT NULL auto_increment,
  `T123_T122_Id` int(10) unsigned default NULL,
  `T123_D009_Id` int(10) unsigned default NULL,
  `T123_Data_Inclusao` datetime default '0000-00-00 00:00:00',
  `T123_Quantidade` decimal(10,3) default '0.000',
  `T123_Valor_Preco` decimal(10,2) default '0.00',
  `T123_Descricao` varchar(60) default NULL,
  PRIMARY KEY  (`T123_Id`),
  KEY `IDX_T123_D009_Id` (`T123_D009_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ITENS DA ORDEM DE SERVICO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t123`
--

LOCK TABLES `t123` WRITE;
/*!40000 ALTER TABLE `t123` DISABLE KEYS */;
/*!40000 ALTER TABLE `t123` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t124`
--

DROP TABLE IF EXISTS `t124`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t124` (
  `T124_Id` int(11) NOT NULL auto_increment,
  `T124_D024_Id` int(10) unsigned default NULL,
  `T124_Ano` varchar(4) default '',
  `T124_Curva_ABC` varchar(1) default '',
  `T124_Vendas_01` decimal(10,2) default '0.00',
  `T124_Vendas_02` decimal(10,2) default '0.00',
  `T124_Vendas_03` decimal(10,2) default '0.00',
  `T124_Vendas_04` decimal(10,2) default '0.00',
  `T124_Vendas_05` decimal(10,2) default '0.00',
  `T124_Vendas_06` decimal(10,2) default '0.00',
  `T124_Vendas_07` decimal(10,2) default '0.00',
  `T124_Vendas_08` decimal(10,2) default '0.00',
  `T124_Vendas_09` decimal(10,2) default '0.00',
  `T124_Vendas_10` decimal(10,2) default '0.00',
  `T124_Vendas_11` decimal(10,2) default '0.00',
  `T124_Vendas_12` decimal(10,2) default '0.00',
  `T124_Vendas_Total` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T124_Id`),
  KEY `T124_IDX_PRODUTO` (`T124_D024_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t124`
--

LOCK TABLES `t124` WRITE;
/*!40000 ALTER TABLE `t124` DISABLE KEYS */;
/*!40000 ALTER TABLE `t124` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t125`
--

DROP TABLE IF EXISTS `t125`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t125` (
  `T125_Id` int(11) NOT NULL auto_increment,
  `T125_D001_Id` int(10) unsigned default NULL,
  `T125_Ano` varchar(4) default '',
  `T125_Curva_ABC` varchar(1) default '',
  `T125_Vendas_Valor_01` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_02` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_03` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_04` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_05` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_06` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_07` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_08` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_09` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_10` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_11` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_12` decimal(10,2) default '0.00',
  `T125_Vendas_Valor_Total` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_01` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_02` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_03` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_04` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_05` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_06` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_07` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_08` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_09` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_10` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_11` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_12` decimal(10,2) default '0.00',
  `T125_Vendas_Qte_Total` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T125_Id`),
  KEY `T125_IDX_PRODUTO` (`T125_D001_Id`),
  KEY `T125_IDX_ANO` (`T125_Ano`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t125`
--

LOCK TABLES `t125` WRITE;
/*!40000 ALTER TABLE `t125` DISABLE KEYS */;
/*!40000 ALTER TABLE `t125` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t126`
--

DROP TABLE IF EXISTS `t126`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t126` (
  `T126_Id` int(11) NOT NULL auto_increment,
  `T126_C007_Id` int(10) unsigned default NULL,
  `T126_Ano` varchar(4) default '',
  `T126_Curva_ABC` varchar(1) default '',
  `T126_Vendas_01` decimal(10,2) default '0.00',
  `T126_Vendas_02` decimal(10,2) default '0.00',
  `T126_Vendas_03` decimal(10,2) default '0.00',
  `T126_Vendas_04` decimal(10,2) default '0.00',
  `T126_Vendas_05` decimal(10,2) default '0.00',
  `T126_Vendas_06` decimal(10,2) default '0.00',
  `T126_Vendas_07` decimal(10,2) default '0.00',
  `T126_Vendas_08` decimal(10,2) default '0.00',
  `T126_Vendas_09` decimal(10,2) default '0.00',
  `T126_Vendas_10` decimal(10,2) default '0.00',
  `T126_Vendas_11` decimal(10,2) default '0.00',
  `T126_Vendas_12` decimal(10,2) default '0.00',
  `T126_Vendas_Total` decimal(10,2) default '0.00',
  PRIMARY KEY  (`T126_Id`),
  KEY `T126_IDX_PRODUTO` (`T126_C007_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t126`
--

LOCK TABLES `t126` WRITE;
/*!40000 ALTER TABLE `t126` DISABLE KEYS */;
/*!40000 ALTER TABLE `t126` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t127`
--

DROP TABLE IF EXISTS `t127`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t127` (
  `T127_Id` int(10) unsigned NOT NULL auto_increment,
  `T127_D024_Id` int(10) unsigned default NULL,
  `T127_C004_Id` int(10) unsigned default NULL,
  `T127_Chave_Acesso_Nfe` varchar(44) default NULL,
  `T127_Numero_Nota_Fiscal` varchar(12) default NULL,
  `T127_Codigo_Retorno` varchar(3) default NULL,
  `T127_Numero_Recibo_Nfe` varchar(20) default NULL,
  `T127_Data_Transmicao` datetime default '0000-00-00 00:00:00',
  `T127_Retorno_Sefaz` text,
  PRIMARY KEY  (`T127_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t127`
--

LOCK TABLES `t127` WRITE;
/*!40000 ALTER TABLE `t127` DISABLE KEYS */;
/*!40000 ALTER TABLE `t127` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t128`
--

DROP TABLE IF EXISTS `t128`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t128` (
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
  PRIMARY KEY  (`T128_Id`),
  KEY `T128_IDX_USUARIOS` (`T128_C007_Id`),
  KEY `T128_IDX_EMPRESAS` (`T128_C004_Id`),
  KEY `T128_IDX_TIPO_PRODUTO` (`T128_C008_Id`),
  KEY `T128_FKIndex3` (`T128_D036_Id`),
  KEY `T128_FKIndex6` (`T128_D024_Id`),
  KEY `T128_FKIndex7` (`T128_D022_Id`),
  KEY `IDX_T128_Id` (`T128_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t128`
--

LOCK TABLES `t128` WRITE;
/*!40000 ALTER TABLE `t128` DISABLE KEYS */;
/*!40000 ALTER TABLE `t128` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t129`
--

DROP TABLE IF EXISTS `t129`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t129` (
  `T129_Id` int(10) unsigned NOT NULL auto_increment,
  `T129_T059_Id` int(10) unsigned default NULL,
  `T129_D009_Id` int(10) unsigned default NULL,
  `T129_T128_Id` int(10) unsigned default NULL,
  `T129_C004_Id` int(10) unsigned default '0',
  `T129_Codigo_Produto` varchar(30) default NULL,
  `T129_Codigo_Produto_Fornecedor` varchar(20) default NULL,
  `T129_Descricao_Produto` varchar(50) default NULL,
  `T129_Quantidade` decimal(10,3) default NULL,
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
  PRIMARY KEY  (`T129_Id`),
  KEY `T129_IDX_PRODUTO_ESTOQUE` (`T129_D009_Id`),
  KEY `T129_IDX_PEDIDO_COMPRA` (`T129_T128_Id`),
  KEY `T129_IDX_CODIGO_PRODUTO` (`T129_Codigo_Produto`),
  KEY `T129_IDX_CODIGO_FORNECEDOR` (`T129_Codigo_Fornecedor`),
  KEY `T129_IDX_EMPRESAS` (`T129_C004_Id`),
  KEY `T129_IDX_PRECOS_COTACAO` (`T129_T059_Id`),
  KEY `T129_IDX_NUMERO_PEDIDO` (`T129_Numero`),
  KEY `T129_IDX_ID` (`T129_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t129`
--

LOCK TABLES `t129` WRITE;
/*!40000 ALTER TABLE `t129` DISABLE KEYS */;
/*!40000 ALTER TABLE `t129` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t130`
--

DROP TABLE IF EXISTS `t130`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t130` (
  `T130_Id` int(10) unsigned NOT NULL auto_increment,
  `T130_T129_Id` int(10) unsigned default NULL,
  `T130_T013_Id` int(10) unsigned default NULL,
  `T130_C004_Id` int(10) unsigned default NULL,
  `T130_T008_Id` int(10) unsigned default NULL,
  `T130_Quantidade` decimal(14,5) default '0.00000',
  `T130_Classificacao_Fiscal` varchar(12) default NULL,
  `T130_Aliquota_IPI` decimal(5,2) default '0.00',
  PRIMARY KEY  (`T130_Id`),
  KEY `T130_FKIndex1` (`T130_T129_Id`),
  KEY `T130_FKIndex2` (`T130_T013_Id`),
  KEY `T130_FKIndex3` (`T130_T008_Id`),
  KEY `T130_FKIndex4` (`T130_C004_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t130`
--

LOCK TABLES `t130` WRITE;
/*!40000 ALTER TABLE `t130` DISABLE KEYS */;
/*!40000 ALTER TABLE `t130` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-05 10:50:40
