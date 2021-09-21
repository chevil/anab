-- MySQL dump 10.13  Distrib 8.0.26, for Linux (x86_64)
--
-- Host: localhost    Database: wavesurfer
-- ------------------------------------------------------
-- Server version	8.0.26-0ubuntu0.20.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `annotation` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `norder` bigint NOT NULL DEFAULT '-1',
  `start` float NOT NULL DEFAULT '-1',
  `end` float NOT NULL DEFAULT '-1',
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `source` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `color` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3322 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `annotation` DISABLE KEYS */;
--
-- Table structure for table `archive`
--

DROP TABLE IF EXISTS `archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archive` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `uri` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `author` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `collection` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `date` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `creator` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `biography` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audiobook`
--

DROP TABLE IF EXISTS `audiobook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audiobook` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `aoid` bigint NOT NULL DEFAULT '-1',
  `norder` bigint NOT NULL DEFAULT '-1',
  `excerpt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `_id` int NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'waveColor','#F43B5A'),(2,'progressColor','#ED0A28'),(3,'mapWaveColor','#82E8DA'),(4,'mapProgressColor','#FFF94A');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload`
--

DROP TABLE IF EXISTS `upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upload` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `aid` bigint NOT NULL DEFAULT '-1',
  `uri` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `size` bigint NOT NULL DEFAULT '-1',
  `type` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `_id` int NOT NULL AUTO_INCREMENT,
  `user` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `color` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (6,'admin','admin_pass','rgba(198,131,212,0.1)');
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

-- Dump completed on 2021-09-20 21:30:39
