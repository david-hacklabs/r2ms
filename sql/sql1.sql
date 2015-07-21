CREATE DATABASE  IF NOT EXISTS `r2ms` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `r2ms`;
-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: 127.0.0.1    Database: r2ms
-- ------------------------------------------------------
-- Server version	5.5.34

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
-- Table structure for table `CVSS_scoring`
--

DROP TABLE IF EXISTS `CVSS_scoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CVSS_scoring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metric_name` varchar(30) NOT NULL,
  `abrv_metric_name` varchar(3) NOT NULL,
  `metric_value` varchar(30) NOT NULL,
  `abrv_metric_value` varchar(3) NOT NULL,
  `numeric_value` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CVSS_scoring`
--

LOCK TABLES `CVSS_scoring` WRITE;
/*!40000 ALTER TABLE `CVSS_scoring` DISABLE KEYS */;
INSERT INTO `CVSS_scoring` VALUES (1,'AccessComplexity','AC','High','H',0.35),(2,'AccessComplexity','AC','Medium','M',0.61),(3,'AccessComplexity','AC','Low','L',0.71),(4,'AccessVector','AV','Local','L',0.395),(5,'AccessVector','AV','Adjacent Network','A',0.646),(6,'AccessVector','AV','Network','N',1),(7,'Authentication','Au','None','N',0.704),(8,'Authentication','Au','Single Instance','S',0.56),(9,'Authentication','Au','Multiple Instances','M',0.45),(10,'AvailabilityRequirement','AR','Undefined','ND',1),(11,'AvailabilityRequirement','AR','Low','L',0.5),(12,'AvailabilityRequirement','AR','Medium','M',1),(13,'AvailabilityRequirement','AR','High','H',1.51),(14,'AvailImpact','A','None','N',0),(15,'AvailImpact','A','Partial','P',0.275),(16,'AvailImpact','A','Complete','C',0.66),(17,'CollateralDamagePotential','CDP','Undefined','ND',0),(18,'CollateralDamagePotential','CDP','None','N',0),(19,'CollateralDamagePotential','CDP','Low (light loss)','L',0.1),(20,'CollateralDamagePotential','CDP','Low-Medium','LM',0.3),(21,'CollateralDamagePotential','CDP','Medium-High','MH',0.4),(22,'CollateralDamagePotential','CDP','High','H',0.5),(23,'ConfidentialityRequirement','CR','Undefined','ND',1),(24,'ConfidentialityRequirement','CR','Low','L',0.5),(25,'ConfidentialityRequirement','CR','Medium','M',1),(26,'ConfidentialityRequirement','CR','High','H',1.51),(27,'ConfImpact','C','None','N',0),(28,'ConfImpact','C','Partial','P',0.275),(29,'ConfImpact','C','Complete','C',0.66),(30,'Exploitability','E','Undefined','ND',1),(31,'Exploitability','E','Unproven that exploit exists','U',0.85),(32,'Exploitability','E','Proof of concept code','POC',0.9),(33,'Exploitability','E','Functional exploit exists','F',0.95),(34,'Exploitability','E','Widespread','H',1),(35,'IntegImpact','I','None','N',0),(36,'IntegImpact','I','Partial','P',0.275),(37,'IntegImpact','I','Complete','C',0.66),(38,'IntegrityRequirement','IR','Undefined','ND',1),(39,'IntegrityRequirement','IR','Low','L',0.5),(40,'IntegrityRequirement','IR','Medium','M',1),(41,'IntegrityRequirement','IR','High','H',1.51),(42,'RemediationLevel','RL','Undefined','ND',1),(43,'RemediationLevel','RL','Official fix','OF',0.87),(44,'RemediationLevel','RL','Temporary fix','TF',0.9),(45,'RemediationLevel','RL','Workaround','W',0.95),(46,'RemediationLevel','RL','Unavailable','U',1),(47,'ReportConfidence','RC','Undefined','ND',1),(48,'ReportConfidence','RC','Unconfirmed','UC',0.9),(49,'ReportConfidence','RC','Uncorroborated','UR',0.95),(50,'ReportConfidence','RC','Confirmed','C',1),(51,'TargetDistribution','TD','Undefined','ND',1),(52,'TargetDistribution','TD','None (0%)','N',0),(53,'TargetDistribution','TD','Low (0-25%)','L',0.25),(54,'TargetDistribution','TD','Medium (26-75%)','M',0.75),(55,'TargetDistribution','TD','High (76-100%)','H',1);
/*!40000 ALTER TABLE `CVSS_scoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_log` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `risk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` VALUES ('2014-05-14 02:45:05',0,1,'Username \"admin\" logged in successfully.'),('2014-05-14 02:45:15',0,1,'Username \"admin\" logged in successfully.'),('2014-05-14 05:27:24',0,1,'Username \"admin\" logged out successfully.'),('2014-05-19 07:02:46',0,1,'Username \"admin\" logged in successfully.'),('2014-05-20 00:13:44',0,1,'Username \"admin\" logged in successfully.'),('2014-05-20 00:37:39',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 00:37:39',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 00:38:19',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 00:38:19',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 02:36:11',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 02:36:11',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 02:36:27',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 02:36:27',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 02:37:02',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 02:37:02',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 02:37:11',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 02:37:11',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 04:40:46',0,1,'Username \"admin\" logged in successfully.'),('2014-05-20 04:40:54',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 04:40:54',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 06:00:00',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 06:00:18',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 06:03:05',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 06:03:57',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 06:28:55',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 06:28:55',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 06:28:58',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 06:28:58',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 07:05:51',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 07:05:51',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 07:19:13',0,1,'Risk level scoring was modified by the \"admin\" user.'),('2014-05-20 07:19:13',0,1,'The risk formula was modified by the \"admin\" user.'),('2014-05-20 23:28:50',0,1,'Username \"admin\" logged in successfully.'),('2014-05-21 00:06:50',0,1,'The review settings were modified by the \"admin\" user.'),('2014-05-21 00:07:12',0,1,'The review settings were modified by the \"admin\" user.'),('2014-05-21 00:07:34',0,1,'The review settings were modified by the \"admin\" user.'),('2014-05-21 00:34:41',0,1,'A new user was added by the \"admin\" user.'),('2014-05-21 00:39:07',0,1,'A new team was added by the \"admin\" user.'),('2014-05-21 00:39:18',0,1,'A new team was added by the \"admin\" user.'),('2014-05-21 00:50:10',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 00:50:17',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 00:50:47',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 00:51:31',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 00:51:43',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 00:52:02',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 00:52:09',0,1,'An existing category was removed by the \"admin\" user.'),('2014-05-21 00:52:30',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 05:44:41',0,1,'Username \"admin\" logged in successfully.'),('2014-05-21 05:58:43',0,1,'A new type of user was added by the \"admin\" user.'),('2014-05-21 05:58:49',0,1,'An existing type of user was removed by the \"admin\" user.'),('2014-05-21 06:12:58',0,1,'A new user was added by the \"admin\" user.'),('2014-05-21 06:21:00',0,1,'An existing user was deleted by the \"admin\" user.'),('2014-05-21 06:21:50',0,1,'A new user was added by the \"admin\" user.'),('2014-05-21 06:24:59',0,1,'An existing user was deleted by the \"admin\" user.'),('2014-05-21 06:28:38',0,1,'A new user was added by the \"admin\" user.'),('2014-05-21 06:29:26',0,1,'Username \"admin\" logged out successfully.'),('2014-05-21 06:30:44',0,1,'Username \"admin\" logged in successfully.'),('2014-05-21 06:30:48',0,1,'Username \"admin\" logged out successfully.'),('2014-05-21 06:36:29',0,5,'Username \"david\" logged in successfully.'),('2014-05-21 06:36:45',0,5,'Username \"david\" logged out successfully.'),('2014-05-21 06:37:12',0,1,'Username \"admin\" logged in successfully.'),('2014-05-21 07:14:18',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 07:14:31',0,1,'A new category was added by the \"admin\" user.'),('2014-05-21 07:15:15',0,1,'A new category was added by the \"admin\" user.'),('2014-05-22 04:20:20',0,1,'Username \"admin\" logged in successfully.'),('2014-07-09 04:31:15',0,1,'Username \"admin\" logged in successfully.'),('2014-07-09 04:34:05',0,1,'Username \"admin\" logged out successfully.'),('2014-07-09 04:34:30',0,1,'Username \"admin\" logged in successfully.'),('2014-07-09 04:37:13',0,1,'A new technology was added by the \"admin\" user.'),('2014-07-09 04:37:26',0,1,'A new technology was added by the \"admin\" user.'),('2014-07-09 04:37:36',0,1,'A new technology was added by the \"admin\" user.'),('2014-07-09 07:29:46',0,1,'Error inserting new company \"admin\" user.'),('2014-07-09 07:35:50',0,1,'Error inserting new company \"admin\" user.'),('2014-07-09 07:35:55',0,1,'Error inserting new company \"admin\" user.'),('2014-07-09 07:36:35',0,1,'Error inserting new company \"admin\" user.'),('2014-07-09 07:36:52',0,1,'Error inserting new company \"admin\" user.'),('2014-07-09 07:37:24',0,1,'Error inserting new company \"admin\" user.'),('2014-07-09 07:38:49',0,1,'A new company was added by the \"admin\" user.'),('2014-07-09 08:42:29',0,1,'Error modifying a company \"admin\" user.'),('2014-07-09 08:48:21',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-09 08:48:33',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-10 00:04:57',0,1,'Username \"admin\" logged in successfully.'),('2014-07-10 00:07:23',0,1,'Error modifying a company \"admin\" user.'),('2014-07-10 00:07:30',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-10 00:07:45',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-10 00:07:57',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-10 00:11:32',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-10 00:11:37',0,1,'Error modifying a company \"admin\" user.'),('2014-07-10 00:11:40',0,1,'An existing company was modified by the \"admin\" user.'),('2014-07-10 00:21:41',0,1,'An existing team was removed by the \"admin\" user.'),('2014-07-10 00:21:49',0,1,'A new team was added by the \"admin\" user.'),('2014-07-10 00:22:11',0,1,'An existing team was removed by the \"admin\" user.'),('2014-07-10 00:22:20',0,1,'A new team was added by the \"admin\" user.'),('2014-07-10 00:23:04',0,1,'A new team was added by the \"admin\" user.'),('2014-07-10 01:05:33',0,1,'A new company was added by the \"admin\" user.'),('2014-07-10 01:09:27',0,1,'A new user was added by the \"admin\" user.'),('2014-07-10 02:04:33',0,1,'An existing user was modified by the \"admin\" user.'),('2014-07-10 02:20:03',0,1,'A new user was added by the \"admin\" user.'),('2014-07-10 02:36:15',0,1,'Alert!! Trying to add a new project with non existing company by the \"admin\" user.'),('2014-07-10 02:37:19',0,1,'Alert!! Trying to add a new project with non existing company by the \"admin\" user.'),('2014-07-10 02:41:28',0,1,'A new project was added by the \"admin\" user.'),('2014-07-10 03:52:54',0,1,'Username \"admin\" logged in successfully.'),('2014-07-10 05:32:43',0,1,'An AJAX request to get projects from company fail by the \"admin\" user.'),('2014-07-10 05:34:05',0,1,'An AJAX request to get projects from company fail by the \"admin\" user.'),('2014-07-10 06:49:56',0,1,'An existing project was deleted by the \"admin\" user.'),('2014-07-10 06:50:21',0,1,'A new project was added by the \"admin\" user.'),('2014-07-10 06:50:31',0,1,'A new project was added by the \"admin\" user.'),('2014-07-11 01:10:10',0,1,'Username \"admin\" logged in successfully.'),('2014-07-11 05:31:56',0,1,'Username \"admin\" logged in successfully.'),('2014-07-11 05:52:21',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 05:52:32',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 05:53:58',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 05:58:53',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 06:06:20',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 06:07:27',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 06:07:38',0,1,'Alert!! Trying to add a client from a company fail by the \"admin\" user.'),('2014-07-11 06:11:15',0,1,'An existing project was associated to the client/s by the \"admin\" user.');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Access Management'),(2,'Environmental Resilience'),(3,'Monitoring'),(4,'Physical Security'),(5,'Policy and Procedure'),(6,'Sensitive Data Management'),(7,'Technical Vulnerability Management'),(8,'Third-Party Management'),(9,'External Penetration Test'),(10,'Internal Penetration Test'),(11,'Social Engineering Assessment'),(12,'Web Application Penetration Test'),(13,'Host Assessment Review'),(14,'Vulnerability Assessment'),(15,'Wireless Penetration Test'),(16,'Source Code Review'),(17,'SCADA System Penetration Testing'),(18,'VoIP Penetration Testing & Security Design Assess.');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `close_reason`
--

DROP TABLE IF EXISTS `close_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `close_reason` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `close_reason`
--

LOCK TABLES `close_reason` WRITE;
/*!40000 ALTER TABLE `close_reason` DISABLE KEYS */;
INSERT INTO `close_reason` VALUES (1,'Fully Mitigated'),(2,'System Retired'),(3,'Cancelled'),(4,'Too Insignificant');
/*!40000 ALTER TABLE `close_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `closures`
--

DROP TABLE IF EXISTS `closures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `closures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `closure_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `close_reason` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `closures`
--

LOCK TABLES `closures` WRITE;
/*!40000 ALTER TABLE `closures` DISABLE KEYS */;
/*!40000 ALTER TABLE `closures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company` (
  `value` int(10) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `address` text,
  `zip` mediumint(8) unsigned DEFAULT NULL,
  `country` int(11) NOT NULL,
  `contactemail` tinytext NOT NULL,
  `contactname` tinytext NOT NULL,
  `startdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company`
--

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
INSERT INTO `company` VALUES (1,'Elastic Digital ','Kent St',2000,2,'david@hehhe.es','David','2014-07-09 17:38:49','2014-07-10 10:11:40'),(2,'HackLabs','Level 29, Chifley Tower 2 Chifley Square  Suite',2000,3,'info@hacklabs.com','Chris Gatford','2014-07-10 11:05:33','2014-07-10 11:05:33');
/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `value` int(10) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'USA'),(2,'UK'),(3,'Australia');
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impact`
--

DROP TABLE IF EXISTS `impact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impact` (
  `name` varchar(20) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impact`
--

LOCK TABLES `impact` WRITE;
/*!40000 ALTER TABLE `impact` DISABLE KEYS */;
INSERT INTO `impact` VALUES ('Insignificant',1),('Minor',2),('Moderate',3),('Major',4),('Extreme/Catastrophic',5);
/*!40000 ALTER TABLE `impact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(2) NOT NULL,
  `full` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'en','English'),(2,'bp','Brazilian Portuguese');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likelihood`
--

DROP TABLE IF EXISTS `likelihood`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likelihood` (
  `name` varchar(20) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likelihood`
--

LOCK TABLES `likelihood` WRITE;
/*!40000 ALTER TABLE `likelihood` DISABLE KEYS */;
INSERT INTO `likelihood` VALUES ('Credible',1),('Likely',2),('Almost Certain',3),('Certain',4);
/*!40000 ALTER TABLE `likelihood` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'All Sites');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mgmt_reviews`
--

DROP TABLE IF EXISTS `mgmt_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mgmt_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `review` int(11) NOT NULL,
  `reviewer` int(11) NOT NULL,
  `next_step` int(11) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mgmt_reviews`
--

LOCK TABLES `mgmt_reviews` WRITE;
/*!40000 ALTER TABLE `mgmt_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `mgmt_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mitigation_effort`
--

DROP TABLE IF EXISTS `mitigation_effort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitigation_effort` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mitigation_effort`
--

LOCK TABLES `mitigation_effort` WRITE;
/*!40000 ALTER TABLE `mitigation_effort` DISABLE KEYS */;
INSERT INTO `mitigation_effort` VALUES (1,'Trivial'),(2,'Minor'),(3,'Considerable'),(4,'Significant'),(5,'Exceptional');
/*!40000 ALTER TABLE `mitigation_effort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mitigations`
--

DROP TABLE IF EXISTS `mitigations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitigations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `planning_strategy` int(11) NOT NULL,
  `mitigation_effort` int(11) NOT NULL,
  `current_solution` text NOT NULL,
  `security_requirements` text NOT NULL,
  `security_recommendations` text NOT NULL,
  `submitted_by` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mitigations`
--

LOCK TABLES `mitigations` WRITE;
/*!40000 ALTER TABLE `mitigations` DISABLE KEYS */;
/*!40000 ALTER TABLE `mitigations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `next_step`
--

DROP TABLE IF EXISTS `next_step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `next_step` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `next_step`
--

LOCK TABLES `next_step` WRITE;
/*!40000 ALTER TABLE `next_step` DISABLE KEYS */;
INSERT INTO `next_step` VALUES (1,'Accept Until Next Review'),(2,'Consider for Project'),(3,'Submit as a Production Issue');
/*!40000 ALTER TABLE `next_step` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset` (
  `username` varchar(20) NOT NULL,
  `token` varchar(20) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planning_strategy`
--

DROP TABLE IF EXISTS `planning_strategy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planning_strategy` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planning_strategy`
--

LOCK TABLES `planning_strategy` WRITE;
/*!40000 ALTER TABLE `planning_strategy` DISABLE KEYS */;
INSERT INTO `planning_strategy` VALUES (1,'Research'),(2,'Accept'),(3,'Mitigate'),(4,'Watch');
/*!40000 ALTER TABLE `planning_strategy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project` (
  `value` int(10) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `creation` datetime NOT NULL,
  `company_id` int(10) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`),
  KEY `fk_project_company_idx` (`company_id`),
  CONSTRAINT `fk_project_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`value`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (2,'Web Application Penetration Testing','2014-07-10 16:50:21',1),(3,'External Penetration Testing','2014-07-10 16:50:31',1);
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_client`
--

DROP TABLE IF EXISTS `project_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_client` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `project` int(11) NOT NULL,
  `creation` datetime NOT NULL,
  `update` datetime NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`),
  KEY `fk_project-client_user_idx` (`user`),
  KEY `fk_project-client_project_idx` (`project`),
  CONSTRAINT `fk_project-client_user` FOREIGN KEY (`user`) REFERENCES `user` (`value`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_project-client_project` FOREIGN KEY (`project`) REFERENCES `project` (`value`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_client`
--

LOCK TABLES `project_client` WRITE;
/*!40000 ALTER TABLE `project_client` DISABLE KEYS */;
INSERT INTO `project_client` VALUES (1,7,2,'2014-07-11 16:11:15','2014-07-11 16:11:15');
/*!40000 ALTER TABLE `project_client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '999999',
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (0,'Unassigned Risks',0);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regulation`
--

DROP TABLE IF EXISTS `regulation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `regulation` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regulation`
--

LOCK TABLES `regulation` WRITE;
/*!40000 ALTER TABLE `regulation` DISABLE KEYS */;
INSERT INTO `regulation` VALUES (1,'PCI DSS'),(2,'Sarbanes-Oxley (SOX)'),(3,'HIPAA'),(4,'ISO 27001');
/*!40000 ALTER TABLE `regulation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,'Approve Risk'),(2,'Reject Risk');
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_levels`
--

DROP TABLE IF EXISTS `review_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_levels` (
  `value` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_levels`
--

LOCK TABLES `review_levels` WRITE;
/*!40000 ALTER TABLE `review_levels` DISABLE KEYS */;
INSERT INTO `review_levels` VALUES (90,'High'),(180,'Medium'),(360,'Low'),(30,'Critical');
/*!40000 ALTER TABLE `review_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_levels`
--

DROP TABLE IF EXISTS `risk_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_levels` (
  `value` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_levels`
--

LOCK TABLES `risk_levels` WRITE;
/*!40000 ALTER TABLE `risk_levels` DISABLE KEYS */;
INSERT INTO `risk_levels` VALUES (4,'High'),(3,'Medium'),(2,'Low'),(5,'Critical'),(1,'Irrelevant');
/*!40000 ALTER TABLE `risk_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_models`
--

DROP TABLE IF EXISTS `risk_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_models` (
  `value` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_models`
--

LOCK TABLES `risk_models` WRITE;
/*!40000 ALTER TABLE `risk_models` DISABLE KEYS */;
INSERT INTO `risk_models` VALUES (1,'Likelihood x Impact + 2(Impact)'),(2,'Likelihood x Impact + Impact'),(3,'Likelihood x Impact'),(4,'Likelihood x Impact + Likelihood'),(5,'Likelihood x Impact + 2(Likelihood)'),(6,'HackLabs Risk Formula');
/*!40000 ALTER TABLE `risk_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_scoring`
--

DROP TABLE IF EXISTS `risk_scoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_scoring` (
  `id` int(11) NOT NULL,
  `scoring_method` int(11) NOT NULL,
  `calculated_risk` float NOT NULL,
  `CLASSIC_likelihood` float NOT NULL DEFAULT '5',
  `CLASSIC_impact` float NOT NULL DEFAULT '5',
  `CVSS_AccessVector` varchar(3) NOT NULL DEFAULT 'N',
  `CVSS_AccessComplexity` varchar(3) NOT NULL DEFAULT 'L',
  `CVSS_Authentication` varchar(3) NOT NULL DEFAULT 'N',
  `CVSS_ConfImpact` varchar(3) NOT NULL DEFAULT 'C',
  `CVSS_IntegImpact` varchar(3) NOT NULL DEFAULT 'C',
  `CVSS_AvailImpact` varchar(3) NOT NULL DEFAULT 'C',
  `CVSS_Exploitability` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_RemediationLevel` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_ReportConfidence` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_CollateralDamagePotential` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_TargetDistribution` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_ConfidentialityRequirement` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_IntegrityRequirement` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_AvailabilityRequirement` varchar(3) NOT NULL DEFAULT 'ND',
  `DREAD_DamagePotential` int(11) DEFAULT '10',
  `DREAD_Reproducibility` int(11) DEFAULT '10',
  `DREAD_Exploitability` int(11) DEFAULT '10',
  `DREAD_AffectedUsers` int(11) DEFAULT '10',
  `DREAD_Discoverability` int(11) DEFAULT '10',
  `OWASP_SkillLevel` int(11) DEFAULT '10',
  `OWASP_Motive` int(11) DEFAULT '10',
  `OWASP_Opportunity` int(11) DEFAULT '10',
  `OWASP_Size` int(11) DEFAULT '10',
  `OWASP_EaseOfDiscovery` int(11) DEFAULT '10',
  `OWASP_EaseOfExploit` int(11) DEFAULT '10',
  `OWASP_Awareness` int(11) DEFAULT '10',
  `OWASP_IntrusionDetection` int(11) DEFAULT '10',
  `OWASP_LossOfConfidentiality` int(11) DEFAULT '10',
  `OWASP_LossOfIntegrity` int(11) DEFAULT '10',
  `OWASP_LossOfAvailability` int(11) DEFAULT '10',
  `OWASP_LossOfAccountability` int(11) DEFAULT '10',
  `OWASP_FinancialDamage` int(11) DEFAULT '10',
  `OWASP_ReputationDamage` int(11) DEFAULT '10',
  `OWASP_NonCompliance` int(11) DEFAULT '10',
  `OWASP_PrivacyViolation` int(11) DEFAULT '10',
  `Custom` float DEFAULT '10',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_scoring`
--

LOCK TABLES `risk_scoring` WRITE;
/*!40000 ALTER TABLE `risk_scoring` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_scoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risks`
--

DROP TABLE IF EXISTS `risks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `reference_id` varchar(20) NOT NULL DEFAULT '',
  `regulation` int(11) DEFAULT NULL,
  `control_number` varchar(20) DEFAULT NULL,
  `location` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `team` int(11) NOT NULL,
  `technology` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `manager` int(11) NOT NULL,
  `assessment` longtext NOT NULL,
  `notes` longtext NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mitigation_id` int(11) NOT NULL,
  `mgmt_review` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `close_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risks`
--

LOCK TABLES `risks` WRITE;
/*!40000 ALTER TABLE `risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL,
  `access` int(10) unsigned DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5d80d4aab9ebd65b7fe1df0f99588656',1405059162,'uid|s:1:\"1\";user|s:5:\"admin\";name|s:5:\"Admin\";admin|s:1:\"1\";review_high|s:1:\"1\";review_medium|s:1:\"1\";review_low|s:1:\"1\";submit_risks|s:1:\"1\";modify_risks|s:1:\"1\";close_risks|s:1:\"1\";plan_mitigations|s:1:\"1\";user_type|s:1:\"1\";lang|s:2:\"en\";access|s:7:\"granted\";LAST_ACTIVITY|i:1405059162;CREATED|i:1405058693;'),('b27475a24578f96f68957ffa11d15e6a',1405046198,'');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `name` varchar(20) NOT NULL,
  `value` varchar(40) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('db_version','20140413-001'),('risk_model','6');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (1,'Branch Management'),(2,'Collaboration'),(3,'Data Center & Storage'),(4,'Database'),(5,'Information Security'),(6,'IT Systems Management'),(7,'Network'),(8,'Unix'),(9,'Web Systems'),(10,'Windows'),(13,'Marketing'),(14,'Security Pentesting'),(15,'Security Audit');
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technology`
--

DROP TABLE IF EXISTS `technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `technology` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technology`
--

LOCK TABLES `technology` WRITE;
/*!40000 ALTER TABLE `technology` DISABLE KEYS */;
INSERT INTO `technology` VALUES (1,'All'),(2,'Anti-Virus'),(3,'Backups'),(4,'Blackberry'),(5,'Citrix'),(6,'Datacenter'),(7,'Mail Routing'),(8,'Live Collaboration'),(9,'Messaging'),(10,'Mobile'),(11,'Network'),(12,'Power'),(13,'Remote Access'),(14,'SAN'),(15,'Telecom'),(16,'Unix'),(17,'VMWare'),(18,'Web'),(19,'Windows'),(20,''),(21,'Mobile iOS'),(22,'Mobile Android');
/*!40000 ALTER TABLE `technology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeuser`
--

DROP TABLE IF EXISTS `typeuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeuser` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeuser`
--

LOCK TABLES `typeuser` WRITE;
/*!40000 ALTER TABLE `typeuser` DISABLE KEYS */;
INSERT INTO `typeuser` VALUES (1,'HackLabs Staff'),(2,'HackLabs Client');
/*!40000 ALTER TABLE `typeuser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `type` int(11) NOT NULL DEFAULT '1',
  `username` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `password` binary(60) NOT NULL,
  `last_login` datetime NOT NULL,
  `teams` varchar(200) NOT NULL DEFAULT 'none',
  `lang` varchar(2) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `companyid` int(10) DEFAULT NULL,
  `review_high` tinyint(1) NOT NULL DEFAULT '0',
  `review_medium` tinyint(1) NOT NULL DEFAULT '0',
  `review_low` tinyint(1) NOT NULL DEFAULT '0',
  `submit_risks` tinyint(1) NOT NULL DEFAULT '0',
  `modify_risks` tinyint(1) NOT NULL DEFAULT '0',
  `plan_mitigations` tinyint(1) NOT NULL DEFAULT '0',
  `close_risks` tinyint(1) NOT NULL DEFAULT '1',
  `multi_factor` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`value`),
  UNIQUE KEY `value_UNIQUE` (`value`),
  KEY `fk_user_typeuser_idx` (`type`),
  CONSTRAINT `fk_user_typeuser` FOREIGN KEY (`type`) REFERENCES `typeuser` (`value`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,0,1,1,'admin','Admin','user@example.com','sAbwTbIFywWKcheyQw9a','$2a$15$7b2601b4979b1ad031b2fuqf1XkeSa4iNxsHK27tq5Va2jLhzkShW','2014-07-11 15:31:56','all','en',1,NULL,1,1,1,1,1,1,1,1),(5,0,1,1,'david','David ZL','david@hacklabs.com','dzWSQRRfI7mtSyrgji1z','$2a$15$8782d1326f1e51991cf56OW0V39eUuzZVBi8EyO.LSDeWHBxFRsM6','2014-05-21 16:36:29',':11:',NULL,0,NULL,1,1,1,1,1,1,1,1),(6,2,1,1,'gio','Giovanni Buzzin','gio@hacklabs.com','qplQ7QdsJNr14LtOUVLm','$2a$15$81d2ca73674a3cf563ddeuuX6EHxW4tmoBlWTq41yIn4Ky/00//im','0000-00-00 00:00:00',':14:',NULL,0,NULL,1,1,1,1,1,1,1,1),(7,1,1,2,'cliente','Client Test','cliente@cliente.com','b06Bvrvhs8XNeSuvp7ny','$2a$15$8186aebdd8cf3faeecaa1ePrm2KmXBLLrDhio1hoAxCGsqiZKt4Ua','0000-00-00 00:00:00','none',NULL,0,NULL,1,1,1,0,0,1,0,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'r2ms'
--

--
-- Dumping routines for database 'r2ms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-07-11 17:00:51
