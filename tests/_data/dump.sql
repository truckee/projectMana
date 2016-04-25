-- MySQL dump 10.13  Distrib 5.7.9, for Win32 (AMD64)
--
-- Host: localhost    Database: projectmana_test
-- ------------------------------------------------------
-- Server version	5.7.12-log

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

create schema if not exists `projectmana_test`;
use `projectmana_test`;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_id` int(11) DEFAULT NULL,
  `line1` varchar(45) DEFAULT NULL,
  `line2` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `county_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `addresstype_id` int(11) DEFAULT NULL,
  `zip` varchar(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_address_household_idx` (`household_id`),
  KEY `idx_address_state_idx` (`state_id`),
  KEY `idx_address_county_idx` (`county_id`),
  CONSTRAINT `idx_address_county` FOREIGN KEY (`county_id`) REFERENCES `county` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `idx_address_household` FOREIGN KEY (`household_id`) REFERENCES `household` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `idx_address_state` FOREIGN KEY (`state_id`) REFERENCES `state` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9869 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `address_type`
--

DROP TABLE IF EXISTS `address_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_type` varchar(45) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_880E0D76BF396750` FOREIGN KEY (`id`) REFERENCES `person` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_outbox`
--

DROP TABLE IF EXISTS `admin_outbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_outbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` int(11) NOT NULL,
  `message_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oppId` int(11) DEFAULT NULL,
  `orgId` int(11) DEFAULT NULL,
  `function` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appliance`
--

DROP TABLE IF EXISTS `appliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appliance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appliance` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `center`
--

DROP TABLE IF EXISTS `center`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `center` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `center` varchar(45) DEFAULT NULL,
  `county_id` int(11) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_center_county_idx` (`county_id`),
  CONSTRAINT `idx_center_county` FOREIGN KEY (`county_id`) REFERENCES `county` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_id` int(11) DEFAULT NULL,
  `contact_date` date DEFAULT NULL,
  `contact_type_id` int(11) DEFAULT NULL,
  `center_id` int(11) DEFAULT NULL,
  `county_id` int(11) DEFAULT NULL,
  `first` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_contact_household_idx` (`household_id`),
  KEY `idx_contact_type_idx` (`contact_type_id`),
  KEY `idx_contact_center_idx` (`center_id`),
  KEY `idx_contact_date` (`contact_date`),
  CONSTRAINT `idx_contact_center` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `idx_contact_household` FOREIGN KEY (`household_id`) REFERENCES `household` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `idx_contact_type` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=125092 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_type`
--

DROP TABLE IF EXISTS `contact_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_desc` varchar(45) DEFAULT NULL,
  `enabled` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `county`
--

DROP TABLE IF EXISTS `county`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `county` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ethnicity`
--

DROP TABLE IF EXISTS `ethnicity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ethnicity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ethnicity` varchar(45) DEFAULT NULL,
  `abbr` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `eventDate` date DEFAULT NULL,
  `location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `starttime` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3BAE0AA7A20C4B1C` (`personId`),
  CONSTRAINT `FK_3BAE0AA7A20C4B1C` FOREIGN KEY (`personId`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fs_amount`
--

DROP TABLE IF EXISTS `fs_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fs_amount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fs_status`
--

DROP TABLE IF EXISTS `fs_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fs_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `household`
--

DROP TABLE IF EXISTS `household`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `household` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hoh_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `foodstamp_id` tinyint(4) DEFAULT NULL,
  `arrivalMonth` int(11) DEFAULT NULL,
  `arrivalYear` int(11) DEFAULT NULL,
  `notfoodstamp_id` int(11) DEFAULT NULL,
  `fsamount_id` int(11) DEFAULT NULL,
  `housing_id` int(11) DEFAULT NULL,
  `income_id` int(11) DEFAULT NULL,
  `income_source_id` int(11) DEFAULT NULL,
  `reason_id` int(11) DEFAULT NULL,
  `compliance` tinyint(1) DEFAULT NULL,
  `compliance_date` date DEFAULT NULL,
  `shared` tinyint(1) DEFAULT NULL,
  `shared_date` date DEFAULT NULL,
  `center_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fs_status` (`foodstamp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10564 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `household_appliance`
--

DROP TABLE IF EXISTS `household_appliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `household_appliance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_id` int(11) DEFAULT NULL,
  `appliance_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_household_appliance_idx` (`household_id`),
  KEY `fk_appliance_household_idx` (`appliance_id`),
  CONSTRAINT `fk_appliance_household` FOREIGN KEY (`appliance_id`) REFERENCES `appliance` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_household_appliance` FOREIGN KEY (`household_id`) REFERENCES `household` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5647 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `household_incomesource`
--

DROP TABLE IF EXISTS `household_incomesource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `household_incomesource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_id` int(11) DEFAULT NULL,
  `incomesource_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_household_appliance_idx` (`household_id`),
  KEY `fk_income_household_idx` (`incomesource_id`),
  CONSTRAINT `fk_household_income` FOREIGN KEY (`household_id`) REFERENCES `household` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_income_household` FOREIGN KEY (`incomesource_id`) REFERENCES `income_source` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1318 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `household_reason`
--

DROP TABLE IF EXISTS `household_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `household_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_id` int(11) DEFAULT NULL,
  `reason_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_household_appliance_idx` (`household_id`),
  KEY `fk_reason_household_idx` (`reason_id`),
  CONSTRAINT `fk_household_reason` FOREIGN KEY (`household_id`) REFERENCES `household` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reason_household` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3173 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `housing`
--

DROP TABLE IF EXISTS `housing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `housing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `housing` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `income`
--

DROP TABLE IF EXISTS `income`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `income` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `income_source`
--

DROP TABLE IF EXISTS `income_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `income_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `income_source` varchar(45) DEFAULT NULL,
  `income_abbr` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `household_id` int(11) DEFAULT NULL,
  `fname` varchar(45) DEFAULT NULL,
  `sname` varchar(45) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `ethnicity_id` int(11) DEFAULT NULL,
  `include` tinyint(1) DEFAULT NULL,
  `exclude_date` date DEFAULT NULL,
  `cid` int(11) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `relationship_id` int(11) DEFAULT NULL,
  `criminal_id` int(11) DEFAULT NULL,
  `work_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_client_ehtnicity` (`ethnicity_id`),
  KEY `idx_client_household` (`household_id`),
  KEY `idx_member_relationship_idx` (`relationship_id`),
  FULLTEXT KEY `idx_name` (`fname`,`sname`)
) ENGINE=MyISAM AUTO_INCREMENT=34583 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_offence`
--

DROP TABLE IF EXISTS `member_offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_offence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `offence_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `new_appliance`
--

DROP TABLE IF EXISTS `new_appliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `new_appliance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notfoodstamp`
--

DROP TABLE IF EXISTS `notfoodstamp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notfoodstamp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notfoodstamp` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offence`
--

DROP TABLE IF EXISTS `offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offence` varchar(45) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_34DCD17692FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_34DCD176A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone`
--

DROP TABLE IF EXISTS `phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areacode` varchar(3) DEFAULT NULL,
  `phone_number` varchar(8) DEFAULT NULL,
  `household_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_phone_household_idx` (`household_id`),
  CONSTRAINT `idx_phone_household` FOREIGN KEY (`household_id`) REFERENCES `household` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7018 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reason`
--

DROP TABLE IF EXISTS `reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relationship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation` varchar(45) DEFAULT NULL,
  `enabled` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `state`
--

DROP TABLE IF EXISTS `state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_contact`
--

DROP TABLE IF EXISTS `temp_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_type_id` int(11) DEFAULT NULL COMMENT 'contact id',
  `household_id` int(11) DEFAULT NULL COMMENT 'client id',
  `contact_date` date DEFAULT NULL,
  `first` tinyint(4) DEFAULT NULL,
  `center_id` int(11) DEFAULT NULL COMMENT 'center id',
  `county_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_vid` (`contact_type_id`),
  KEY `idx_cid` (`household_id`),
  KEY `idx_date` (`contact_date`)
) ENGINE=MyISAM AUTO_INCREMENT=7442505 DEFAULT CHARSET=utf8 COMMENT='household contacts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_household`
--

DROP TABLE IF EXISTS `temp_household`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_household` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hoh_id` int(11) NOT NULL DEFAULT '0' COMMENT 'client id',
  `res` int(11) DEFAULT NULL COMMENT 'date of birth',
  `date_added` date DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`hoh_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10549 DEFAULT CHARSET=utf8 COMMENT='members of client''s household';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_member`
--

DROP TABLE IF EXISTS `temp_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_member` (
  `id` int(11) NOT NULL,
  `household_id` int(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL COMMENT 'Date of birth',
  `sex` enum('Female','Male') DEFAULT NULL,
  `ethnicity_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Main Client Table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertable`
--

DROP TABLE IF EXISTS `usertable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(12) DEFAULT NULL,
  `role` varchar(25) DEFAULT 'ROLE_USER',
  `fname` varchar(25) NOT NULL DEFAULT '',
  `sname` varchar(45) NOT NULL DEFAULT '',
  `email` varchar(45) DEFAULT '',
  `salt` varchar(32) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  `password` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `work`
--

DROP TABLE IF EXISTS `work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `work` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work` varchar(45) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Dumping routines for database 'projectmana'
--
/*!50003 DROP FUNCTION IF EXISTS `age` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`projectmana`@`localhost` FUNCTION `age`(dob DATE) RETURNS int(11)
return 
(SELECT (year(now()) - year(dob) - (concat(month(now()),'-01') < right(dob,5)))) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `household_size` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`projectmana`@`localhost` FUNCTION `household_size`(id INT, dt DATE) RETURNS int(11)
RETURN
	(select if(count(dob)=0,1,count(dob)) from member m
	where m.household_id = id and (m.include = 1 or m.exclude_date > dt)) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `residency` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`projectmana`@`localhost` FUNCTION `residency`( id INT) RETURNS int(11)
begin
		declare arrYear INT;
        declare arrMonth INT;
		set arrYear = (select arrivalYear from household h where h.id = id);
        set arrMonth = (select arrivalMonth from household h where h.id = id);
        return (select 12*(year(now()) - arrYear) + (month(now()) - arrMonth));
	end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-28 11:01:45
-- MySQL dump 10.13  Distrib 5.7.9, for Win32 (AMD64)
--
-- Host: 127.0.0.1    Database: projectmana
-- ------------------------------------------------------
-- Server version	5.7.9-log

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
-- Dumping data for table `appliance`
--

LOCK TABLES `appliance` WRITE;
/*!40000 ALTER TABLE `appliance` DISABLE KEYS */;
INSERT INTO `appliance` VALUES (1,'Microwave',0),(2,'Refrigerator',0),(3,'Stove',0),(4,'Oven',0);
/*!40000 ALTER TABLE `appliance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `center`
--

LOCK TABLES `center` WRITE;
/*!40000 ALTER TABLE `center` DISABLE KEYS */;
INSERT INTO `center` VALUES (1,'Tahoe City',1,1),(2,'Squaw Valley',1,0),(3,'Kings Beach',1,1),(4,'Soda Springs',1,0),(5,'Truckee',2,1),(6,'Incline Village',3,1),(7,'N/A',8,0);
/*!40000 ALTER TABLE `center` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `contact_type`
--

LOCK TABLES `contact_type` WRITE;
/*!40000 ALTER TABLE `contact_type` DISABLE KEYS */;
INSERT INTO `contact_type` VALUES (1,'Critical',0),(2,'Emergency',0),(3,'FACE',1),(4,'General Dist.',1),(5,'Let\'s Talk Turkey',1),(6,'TEFAP',0),(7,'Emergency plus General',0),(8,'TEFAP plus General',1),(9,'Food Distribution',0),(10,'Emergency Food Bag',1),(11,'Cooking Compromised Food Bag',1);
/*!40000 ALTER TABLE `contact_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `county`
--

LOCK TABLES `county` WRITE;
/*!40000 ALTER TABLE `county` DISABLE KEYS */;
INSERT INTO `county` VALUES (1,'Placer',1),(2,'Nevada',1),(3,'Washoe',1),(4,'El Dorado',1),(5,'Douglas',1),(6,'Carson',1),(7,'Alpine',1),(8,'N/A',0);
/*!40000 ALTER TABLE `county` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ethnicity`
--

LOCK TABLES `ethnicity` WRITE;
/*!40000 ALTER TABLE `ethnicity` DISABLE KEYS */;
INSERT INTO `ethnicity` VALUES (1,'Caucasian','Cau',1),(2,'African-American','AfrAm',1),(3,'Asian','Asian',1),(4,'Hispanic','Hisp',1),(5,'Native American','NtvAm',1),(6,'Hawaiian/Pacific Islander','HaPI',1),(7,'Other','Oth',1),(8,'Unknown','Unk',1);
/*!40000 ALTER TABLE `ethnicity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fs_amount`
--

LOCK TABLES `fs_amount` WRITE;
/*!40000 ALTER TABLE `fs_amount` DISABLE KEYS */;
INSERT INTO `fs_amount` VALUES (1,'0 - 200',1),(2,'201 - 400',1),(3,'401 - 600',1),(4,'601 - 800',1),(5,'801 - 1,000',1),(6,'1,000+',1);
/*!40000 ALTER TABLE `fs_amount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fs_status`
--

LOCK TABLES `fs_status` WRITE;
/*!40000 ALTER TABLE `fs_status` DISABLE KEYS */;
INSERT INTO `fs_status` VALUES (1,'No',1),(2,'Yes',1),(3,'Appl',1),(4,'Unknown',1);
/*!40000 ALTER TABLE `fs_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `housing`
--

LOCK TABLES `housing` WRITE;
/*!40000 ALTER TABLE `housing` DISABLE KEYS */;
INSERT INTO `housing` VALUES (1,'Renting',1),(2,'Home owner',1),(3,'Shelter/hotel',1),(4,'Car/tent',1),(5,'Friends',1),(6,'Traveling',1),(7,'Homeless',1);
/*!40000 ALTER TABLE `housing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `income`
--

LOCK TABLES `income` WRITE;
/*!40000 ALTER TABLE `income` DISABLE KEYS */;
INSERT INTO `income` VALUES (1,'0 - 500',1),(2,'501 - 1,000',1),(3,'1,001 - 2,000',1),(4,'2,001 - 3,000',1),(5,'3,001 - 4,000',1),(6,'4,000 - 5,000',1),(7,'Source:',0),(8,'Unknown',1);
/*!40000 ALTER TABLE `income` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `income_source`
--

LOCK TABLES `income_source` WRITE;
/*!40000 ALTER TABLE `income_source` DISABLE KEYS */;
INSERT INTO `income_source` VALUES (1,'Cash Assistance',NULL,0),(2,'Social Security',NULL,0),(3,'SSI',NULL,0),(4,'SSDI',NULL,0),(5,'Unemployment',NULL,0),(6,'Workman’s Comp.',NULL,0),(7,'Child Support',NULL,0),(8,'VA',NULL,0),(9,'Pension',NULL,0),(10,'Other',NULL,0),(11,'None',NULL,0);
/*!40000 ALTER TABLE `income_source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `notfoodstamp`
--

LOCK TABLES `notfoodstamp` WRITE;
/*!40000 ALTER TABLE `notfoodstamp` DISABLE KEYS */;
INSERT INTO `notfoodstamp` VALUES (1,'Not qualified',1),(2,'Not applied',1),(3,'In process',1);
/*!40000 ALTER TABLE `notfoodstamp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `offence`
--

LOCK TABLES `offence` WRITE;
/*!40000 ALTER TABLE `offence` DISABLE KEYS */;
INSERT INTO `offence` VALUES (1,'Felony',1),(2,'Assault',1),(3,'Sex offender',1);
/*!40000 ALTER TABLE `offence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `reason`
--

LOCK TABLES `reason` WRITE;
/*!40000 ALTER TABLE `reason` DISABLE KEYS */;
INSERT INTO `reason` VALUES (1,'Housing/Utility Cost',1),(2,'Unemployed',1),(3,'Low Wages',1),(4,'Medical Cost',1),(5,'Childcare Cost',1),(6,'Out of benefits',1),(7,'Benefits are late',1),(8,'Emergency',1),(9,'Moved/Relocated',1);
/*!40000 ALTER TABLE `reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `relationship`
--

LOCK TABLES `relationship` WRITE;
/*!40000 ALTER TABLE `relationship` DISABLE KEYS */;
INSERT INTO `relationship` VALUES (1,'Self',1),(2,'Mother',1),(3,'Father',1),(4,'Husband',1),(5,'Wife',1),(6,'Son',1),(7,'Daughter',1),(8,'Brother',1),(9,'Sister',1),(10,'Grandfather',1),(11,'Grandmother',1),(12,'Aunt',1),(13,'Uncle',1),(14,'Cousin',1),(15,'Friend',1),(16,'Other',1);
/*!40000 ALTER TABLE `relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `state`
--

LOCK TABLES `state` WRITE;
/*!40000 ALTER TABLE `state` DISABLE KEYS */;
INSERT INTO `state` VALUES (1,'AK',NULL),(2,'AL',NULL),(3,'AR',NULL),(4,'AZ',NULL),(5,'CA',1),(6,'CO',NULL),(7,'CT',NULL),(8,'DE',NULL),(9,'FL',NULL),(10,'GA',NULL),(11,'HI',NULL),(12,'IA',NULL),(13,'ID',NULL),(14,'IL',NULL),(15,'IN',NULL),(16,'KS',NULL),(17,'KY',NULL),(18,'LA',NULL),(19,'MA',NULL),(20,'MD',NULL),(21,'ME',NULL),(22,'MI',NULL),(23,'MN',NULL),(24,'MO',NULL),(25,'MS',NULL),(26,'MT',NULL),(27,'NC',NULL),(28,'ND',NULL),(29,'NE',NULL),(30,'NH',NULL),(31,'NJ',NULL),(32,'NM',NULL),(33,'NV',1),(34,'NY',NULL),(35,'OH',NULL),(36,'OK',NULL),(37,'OR',NULL),(38,'PA',NULL),(39,'RI',NULL),(40,'SC',NULL),(41,'SD',NULL),(42,'TN',NULL),(43,'TX',NULL),(44,'UT',NULL),(45,'VA',NULL),(46,'VT',NULL),(47,'WA',NULL),(48,'WI',NULL),(49,'WV',NULL),(50,'WY',NULL),(51,'DC',NULL),(52,'AS',NULL),(53,'FM',NULL),(54,'GU',NULL),(55,'MH',NULL),(56,'MP',NULL),(57,'PR',NULL),(58,'PW',NULL),(59,'VI',NULL),(60,'AA',NULL),(61,'AE',NULL),(62,'AP',NULL),(63,'Undetermined',NULL),(64,'Declined',NULL);
/*!40000 ALTER TABLE `state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usertable`
--

LOCK TABLES `usertable` WRITE;
/*!40000 ALTER TABLE `usertable` DISABLE KEYS */;
INSERT INTO `usertable` VALUES (1,'projectmana','ROLE_ADMIN','Original','User','',NULL,1,'b42910335688fb5ff0fe971de6f5262c'),(2,'admin','ROLE_ADMIN','Application','Administrator','admin@projectmana.org','56225c363a3c4b53e92ee2ddb3593940',1,'16e711fbad2f007492a3e78b7520e1b1'),(3,'superadmin','ROLE_SUPER_ADMIN','George','Brooks',NULL,'d69629bd6e90ef6f2c15aa7cfccc6b89',1,'b02959189eb06fb84a86265302fc9c17');
/*!40000 ALTER TABLE `usertable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `work`
--

LOCK TABLES `work` WRITE;
/*!40000 ALTER TABLE `work` DISABLE KEYS */;
INSERT INTO `work` VALUES (1,'Full Time',1),(2,'Part Time',1),(3,'Seasonal',1),(4,'No',1);
/*!40000 ALTER TABLE `work` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'projectmana'
--
/*!50003 DROP FUNCTION IF EXISTS `age` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`projectmana`@`localhost` FUNCTION `age`(dob DATE) RETURNS int(11)
return 
(SELECT (year(now()) - year(dob) - (concat(month(now()),'-01') < right(dob,5)))) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `household_size` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`projectmana`@`localhost` FUNCTION `household_size`(id INT, dt DATE) RETURNS int(11)
RETURN
	(select if(count(dob)=0,1,count(dob)) from member m
	where m.household_id = id and (m.include = 1 or m.exclude_date > dt)) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `residency` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`projectmana`@`localhost` FUNCTION `residency`( id INT) RETURNS int(11)
begin
		declare arrYear INT;
        declare arrMonth INT;
		set arrYear = (select arrivalYear from household h where h.id = id);
        set arrMonth = (select arrivalMonth from household h where h.id = id);
        return (select 12*(year(now()) - arrYear) + (month(now()) - arrMonth));
	end ;;
DELIMITER ;

## single head
set foreign_key_checks = 0;
insert into member (fname, sname, ethnicity_id, include)
values ('Single', 'Head', '8', 1);
set @hohId = last_insert_id();
insert into household (hoh_id, foodstamp_id)
values (@hohId, 4);
set @householdId = last_insert_id();
update member set household_id = @householdId where id = @hohId;

## several members
insert into member (fname, sname, ethnicity_id, include)
values ('MoreThanOne', 'Member', '8', 1);
set @hohId = last_insert_id();
insert into member (fname, sname, ethnicity_id, include)
values ('Added', 'Member', '8', 1);
set @memberId = last_insert_id();
insert into household (hoh_id, foodstamp_id)
values (@hohId, 4);
set @householdId = last_insert_id();
update member set household_id = @householdId where id = @hohId;
update member set household_id = @householdId where id = @memberId;

## v2 household
insert into member (fname, sname, ethnicity_id, sex, dob)
values ('Benny', 'Borko', '8', 'Male', '1968-06-14');
set @hohId = last_insert_id();
insert into household (hoh_id, foodstamp_id, center_id, compliance, compliance_date, shared, shared_date)
values (@hohId, 4, 4, 0, '2016-03-16', 0, '2016-03-16');
set @householdId = last_insert_id();
update member set household_id = @householdId where id = @hohId;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-28 10:12:46



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-04-24  9:24:34
