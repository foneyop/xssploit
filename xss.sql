-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: xssploit
-- ------------------------------------------------------
-- Server version	5.5.34-0ubuntu0.12.04.1

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
-- Table structure for table `audit`
--

DROP TABLE IF EXISTS `audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit` (
  `guid` varchar(32) NOT NULL DEFAULT '',
  `command` text,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`,`guid`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit`
--

LOCK TABLES `audit` WRITE;
/*!40000 ALTER TABLE `audit` DISABLE KEYS */;
INSERT INTO `audit` VALUES ('kin2oPrpj6OSLd2W','alert.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=alert&message=payload\\%201',162),('kin2oPrpj6OSLd2W','alert.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=alert&message=Hello\\%20Girls',163),('kin2oPrpj6OSLd2W','audio_hack.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=audio_hack&message=Hello\\%20Girls',164),('kin2oPrpj6OSLd2W','audio_hack.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=audio_hack&message=Hello\\%20Girls',165),('kin2oPrpj6OSLd2W','audio_hack.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=audio_hack&message=Hello\\%20Girls',166),('kin2oPrpj6OSLd2W','audio_hack.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=audio_hack&message=Hello\\%20Girls',167),('0SouIhqC9z5aqqg1','audio_hack.php?auth=pinedale&A=exploit&target=0SouIhqC9z5aqqg1&payload=audio_hack&message=Hello\\%20Girls',168),('aCcxEV7lEsJYLWwl','audio_hack.php?auth=pinedale&A=exploit&target=aCcxEV7lEsJYLWwl&payload=audio_hack&message=Hello\\%20Girls',169),('aCcxEV7lEsJYLWwl','alert.php?auth=pinedale&A=exploit&target=aCcxEV7lEsJYLWwl&payload=alert&message=it\'s\\%20working!',170),('CfGsvlPbXJTJSmPV','alert.php?auth=Pine\\%20Dale&A=exploit&target=CfGsvlPbXJTJSmPV&payload=alert&message=payload\\%201',171),('CfGsvlPbXJTJSmPV','alert.php?auth=Pine\\%20Dale&A=exploit&target=CfGsvlPbXJTJSmPV&payload=alert&message=payload\\%201',172),('tNTqHQSKjoVzteB','alert.php?auth=Pine\\%20Dale&A=exploit&target=tNTqHQSKjoVzteB&payload=alert&message=payload\\%201',173),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=payload\\%201',174),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=payload\\%201',175),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=Big\\%20Bang',176),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=No\\%20Jet\\%20Skis',177),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=Auto\\%20Update',178),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=Nothing\\%20To\\%20Do',179),('R31TCah8TPy01dTb','alert.php?auth=Pine\\%20Dale&A=exploit&target=R31TCah8TPy01dTb&payload=alert&message=NO\\%20UPDATE',180);
/*!40000 ALTER TABLE `audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_id` int(11) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `user` varchar(45) DEFAULT NULL,
  `pass` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commands`
--

DROP TABLE IF EXISTS `commands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commands` (
  `guid` varchar(32) NOT NULL,
  `command` text,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`,`guid`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commands`
--

LOCK TABLES `commands` WRITE;
/*!40000 ALTER TABLE `commands` DISABLE KEYS */;
INSERT INTO `commands` VALUES ('kin2oPrpj6OSLd2W','audio_hack.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=audio_hack&message=Hello\\%20Girls',171),('kin2oPrpj6OSLd2W','audio_hack.php?auth=pinedale&A=exploit&target=kin2oPrpj6OSLd2W&payload=audio_hack&message=Hello\\%20Girls',172),('0SouIhqC9z5aqqg1','audio_hack.php?auth=pinedale&A=exploit&target=0SouIhqC9z5aqqg1&payload=audio_hack&message=Hello\\%20Girls',174),('aCcxEV7lEsJYLWwl','frame_me.php?auth=pinedale&A=exploit&target=aCcxEV7lEsJYLWwl&payload=frame_me&message=it\'s\\%20working!',177),('all','dos.php?auth=Pine\\%20Dale&A=exploit&target=all&payload=dos&message=payload\\%201&url=http\\%3A\\%2F\\%2Flocalhost\\%2Ftest.php',180);
/*!40000 ALTER TABLE `commands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debug_log`
--

DROP TABLE IF EXISTS `debug_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debug_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(32) NOT NULL,
  `log` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guid` (`guid`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debug_log`
--

LOCK TABLES `debug_log` WRITE;
/*!40000 ALTER TABLE `debug_log` DISABLE KEYS */;
INSERT INTO `debug_log` VALUES (43,'CfGsvlPbXJTJSmPV','alert start.  message: [payload 1].  ','2014-06-13 04:20:51'),(44,'tNTqHQSKjoVzteB','alert start.  sent message: [payload 1].  ','2014-06-14 02:52:43'),(45,'R31TCah8TPy01dTb','alert start.  sent message: [payload 1].  ','2014-06-14 04:36:29'),(46,'R31TCah8TPy01dTb','alert start.  sent message: [payload 1].  ','2014-06-14 15:36:06'),(47,'R31TCah8TPy01dTb','alert start.  sent message: [Big Bang].  ','2014-06-14 15:56:26'),(48,'R31TCah8TPy01dTb','alert start.  sent message: [No Jet Skis].  ','2014-06-14 15:59:52'),(49,'R31TCah8TPy01dTb','alert start.  sent message: [Auto Update].  ','2014-06-14 16:21:39'),(50,'R31TCah8TPy01dTb','alert start.  sent message: [Nothing To Do].  ','2014-06-14 16:24:05'),(51,'R31TCah8TPy01dTb','alert start.  sent message: [NO UPDATE].  ','2014-06-14 16:33:17');
/*!40000 ALTER TABLE `debug_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `host`
--

DROP TABLE IF EXISTS `host`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remote_ip` varchar(32) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `headers` text NOT NULL,
  `inject_source` varchar(255) DEFAULT NULL,
  `cookies` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `os` varchar(32) NOT NULL,
  `browser` varchar(32) NOT NULL,
  `guid` varchar(45) NOT NULL,
  `heartbeat` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`remote_ip`),
  KEY `agent` (`agent`),
  KEY `referer` (`inject_source`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1 COMMENT='hosts seen on network';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `host`
--

LOCK TABLES `host` WRITE;
/*!40000 ALTER TABLE `host` DISABLE KEYS */;
INSERT INTO `host` VALUES (85,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/31.0.1650.63 Chrome/31.0.1650.63 Safari/537.36','en-US,en;q=0.8','http://localhost/test.html','null','2014-06-13 02:16:28','linux','chrome','R31TCah8TPy01dTb','2014-06-14 19:34:05');
/*!40000 ALTER TABLE `host` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `param`
--

DROP TABLE IF EXISTS `param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `param` (
  `id` int(11) NOT NULL,
  `url_id` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `guid` varchar(5) DEFAULT NULL,
  `last_test` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param`
--

LOCK TABLES `param` WRITE;
/*!40000 ALTER TABLE `param` DISABLE KEYS */;
INSERT INTO `param` VALUES (0,1,'foo','bbaab','2014-06-20 01:32:16');
/*!40000 ALTER TABLE `param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `param_display`
--

DROP TABLE IF EXISTS `param_display`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `param_display` (
  `id` int(11) NOT NULL,
  `param_id` varchar(45) DEFAULT NULL,
  `domain` varchar(45) DEFAULT NULL,
  `protocol` varchar(45) DEFAULT NULL,
  `url` int(11) DEFAULT NULL,
  `eq` int(11) DEFAULT NULL,
  `sq` int(11) DEFAULT NULL,
  `dq` int(11) DEFAULT NULL,
  `lt` int(11) DEFAULT NULL,
  `gt` int(11) DEFAULT NULL,
  `sc` int(11) DEFAULT NULL,
  `es` int(11) DEFAULT NULL,
  `seen` varchar(45) DEFAULT 'CURRENT_TIMESTAMP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param_display`
--

LOCK TABLES `param_display` WRITE;
/*!40000 ALTER TABLE `param_display` DISABLE KEYS */;
/*!40000 ALTER TABLE `param_display` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `url`
--

DROP TABLE IF EXISTS `url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(45) NOT NULL,
  `protocol` varchar(12) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ignore` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `url`
--

LOCK TABLES `url` WRITE;
/*!40000 ALTER TABLE `url` DISABLE KEYS */;
INSERT INTO `url` VALUES (1,'infosecc3','http','/foobar',0);
/*!40000 ALTER TABLE `url` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-25  8:41:41
