-- MySQL dump 10.13  Distrib 5.6.19, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: global
-- ------------------------------------------------------
-- Server version	5.6.19-0ubuntu0.14.04.1

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
-- Table structure for table `q001`
--

DROP TABLE IF EXISTS `q001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `q001` (
  `Q001_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Q001_Database` char(50) DEFAULT NULL,
  `Q001_T007_Id` int(11) DEFAULT NULL,
  `Q001_C004_Id` int(11) DEFAULT NULL,
  `Q001_Flag_Refazer` char(1) NOT NULL DEFAULT 'N',
  `Q001_Flag_Scan` char(1) NOT NULL DEFAULT 'N',
  `Q001_Url` varchar(255) DEFAULT NULL,
  `Q001_Path` varchar(255) DEFAULT NULL,
  `Q001_DataHora` datetime DEFAULT NULL,
  `Q001_C007_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`Q001_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=282258 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `q002`
--

DROP TABLE IF EXISTS `q002`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `q002` (
  `Q002_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Q002_Servidor_Id` varchar(20) DEFAULT NULL,
  `Q002_Database` varchar(50) DEFAULT NULL,
  `Q002_C004_Id` int(11) DEFAULT NULL,
  `Q002_T007_Id` int(11) DEFAULT NULL,
  `Q002_Etapa` int(1) DEFAULT NULL,
  `Q002_Codigo_Resposta` int(11) DEFAULT NULL,
  `Q002_Resposta_Extra` text,
  `Q002_Soap_Debug` text,
  `Q002_Numero_NF` int(11) DEFAULT NULL,
  `Q002_Retorno_Sefaz` text,
  `Q002_T007_Codigo_CFOP` varchar(255) DEFAULT NULL,
  `Q002_T007_Serie` int(11) DEFAULT NULL,
  `Q002_T007_D024_Nome_Empresa` varchar(255) DEFAULT NULL,
  `Q002_T007_Data_Emissao` date DEFAULT NULL,
  `Q002_C004_Nome_Abreviado` varchar(255) DEFAULT NULL,
  `Q002_C004_Telefone` varchar(255) DEFAULT NULL,
  `Q002_Url` varchar(255) DEFAULT NULL,
  `Q002_DataHora` datetime DEFAULT NULL,
  `Q002_T007_Valor_Total` decimal(20,5) DEFAULT NULL,
  PRIMARY KEY (`Q002_Id`),
  KEY `I_Q003` (`Q002_Database`,`Q002_C004_Id`,`Q002_T007_Id`),
  KEY `Q002_T007_Data_Emissao` (`Q002_T007_Data_Emissao`),
  KEY `NewIndex1` (`Q002_C004_Nome_Abreviado`)
) ENGINE=MyISAM AUTO_INCREMENT=762324 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `q003`
--

DROP TABLE IF EXISTS `q003`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `q003` (
  `Q003_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Q003_Md5` varchar(255) DEFAULT NULL,
  `Q003_Q002_Id` int(11) DEFAULT NULL,
  `Q003_Mensagem` text,
  `Q003_XML` longtext,
  PRIMARY KEY (`Q003_Id`),
  UNIQUE KEY `I_Q003_Md5` (`Q003_Md5`),
  UNIQUE KEY `NewIndex1` (`Q003_Q002_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=72718 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t136`
--

DROP TABLE IF EXISTS `t136`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t136` (
  `T136_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T136_Numero_Erro` int(3) DEFAULT NULL,
  `T136_Descricao_Erro` text,
  `T136_Procedimento` text,
  `T136_Flag_Refazer_Chave_Acesso` enum('S','N') DEFAULT 'N',
  PRIMARY KEY (`T136_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=521 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `w004`
--

DROP TABLE IF EXISTS `w004`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `w004` (
  `W004_Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `W004_Logradouro` varchar(70) DEFAULT NULL,
  `W004_Bairro` varchar(72) DEFAULT NULL,
  `W004_Cep` varchar(9) NOT NULL,
  `W004_Cidade` varchar(50) NOT NULL,
  `W004_UF` char(2) NOT NULL,
  `W004_IBGE_Cidade` int(7) DEFAULT NULL,
  `W004_IBGE_UF` int(2) DEFAULT NULL,
  PRIMARY KEY (`W004_Id`),
  KEY `W004_Idx_Cep` (`W004_Cep`)
) ENGINE=InnoDB AUTO_INCREMENT=456060 DEFAULT CHARSET=latin1;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-07 11:21:35
