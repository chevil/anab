-- MySQL dump 10.13  Distrib 5.7.42, for Linux (x86_64)
--
-- Host: localhost    Database: wavesurfer
-- ------------------------------------------------------
-- Server version	5.7.42-0ubuntu0.18.04.1

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
-- Table structure for table `annotation`
--

DROP TABLE IF EXISTS `annotation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `norder` bigint(20) NOT NULL DEFAULT '-1',
  `start` float NOT NULL DEFAULT '-1',
  `end` float NOT NULL DEFAULT '-1',
  `url` longtext COLLATE utf8mb4_bin,
  `source` longtext COLLATE utf8mb4_bin,
  `data` longtext COLLATE utf8mb4_bin,
  `user` longtext COLLATE utf8mb4_bin,
  `color` longtext COLLATE utf8mb4_bin,
  `whispered` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27389 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation`
--

--
-- Table structure for table `archive`
--

DROP TABLE IF EXISTS `archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uri` longtext COLLATE utf8mb4_bin,
  `url` longtext COLLATE utf8mb4_bin,
  `author` longtext COLLATE utf8mb4_bin,
  `title` longtext COLLATE utf8mb4_bin,
  `collection` longtext COLLATE utf8mb4_bin,
  `date` longtext COLLATE utf8mb4_bin,
  `creator` longtext COLLATE utf8mb4_bin,
  `biography` longtext COLLATE utf8mb4_bin,
  `description` longtext COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audiobook`
--

DROP TABLE IF EXISTS `audiobook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audiobook` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` longtext COLLATE utf8mb4_bin,
  `aoid` bigint(20) NOT NULL DEFAULT '-1',
  `norder` bigint(20) NOT NULL DEFAULT '-1',
  `excerpt` longtext COLLATE utf8mb4_bin,
  `user` longtext COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext COLLATE utf8mb4_bin NOT NULL,
  `value` longtext COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'waveColor','#93F470'),(2,'progressColor','#ED54BF'),(3,'mapWaveColor','#4CC4E8'),(4,'mapProgressColor','#FFF94A');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload`
--

DROP TABLE IF EXISTS `upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `aid` bigint(20) NOT NULL DEFAULT '-1',
  `uri` longtext COLLATE utf8mb4_bin,
  `size` bigint(20) NOT NULL DEFAULT '-1',
  `type` longtext COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` longtext COLLATE utf8mb4_bin NOT NULL,
  `password` longtext COLLATE utf8mb4_bin NOT NULL,
  `color` longtext COLLATE utf8mb4_bin NOT NULL,
  `dark` int(11) DEFAULT '1',
  `nbt` int(11) DEFAULT '0',
  `tts` time DEFAULT '00:00:00',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','<your-password>','rgba(198,131,212,0.1)',1,66,'13:07:03');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-28 23:40:26
