-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: fit_production
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `account_requests`
--

DROP TABLE IF EXISTS `account_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `football_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','contacted','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contacted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `rejected_by` bigint unsigned DEFAULT NULL,
  `association_id` bigint unsigned DEFAULT NULL,
  `generated_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_created_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_requests_approved_by_foreign` (`approved_by`),
  KEY `account_requests_rejected_by_foreign` (`rejected_by`),
  KEY `account_requests_association_id_foreign` (`association_id`),
  KEY `account_requests_status_created_at_index` (`status`,`created_at`),
  KEY `account_requests_email_index` (`email`),
  KEY `account_requests_organization_type_index` (`organization_type`),
  KEY `account_requests_football_type_index` (`football_type`),
  CONSTRAINT `account_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `account_requests_association_id_foreign` FOREIGN KEY (`association_id`) REFERENCES `associations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `account_requests_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_requests`
--

LOCK TABLES `account_requests` WRITE;
/*!40000 ALTER TABLE `account_requests` DISABLE KEYS */;
INSERT INTO `account_requests` VALUES (1,'jhbjbjb','nu9u9uyyuiyi','iyy@gg.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:18:29','2025-09-01 08:18:29'),(2,'uuuu','aaaa','ou@ww.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:20:27','2025-09-01 08:20:27'),(3,'bbvv','yyy','yy@ww.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:21:24','2025-09-01 08:21:24'),(4,'oooo','pppp','ggg@sd.yu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:23:40','2025-09-01 08:23:40'),(5,'uuu','ttt','vgvgv@ghj.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:25:44','2025-09-01 08:25:44'),(6,'nnnn','uuuu','qq@ww.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:29:20','2025-09-01 08:29:20'),(7,'bbmnbn','tetewtewre','byt@wtrt.yy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:29:54','2025-09-01 08:29:54'),(8,'bkbn','gfcgfc','ggg@ww.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:31:43','2025-09-01 08:31:43'),(9,'bkbn','gfcgfc','ggg@ww.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:39:37','2025-09-01 08:39:37'),(10,'bkbn','gfcgfc','ggg@ww.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:41:17','2025-09-01 08:41:17'),(11,'bkbn','gfcgfc','ggg@ww.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:43:38','2025-09-01 08:43:38'),(12,'bkbn','gfcgfc','ggg@ww.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:45:03','2025-09-01 08:45:03'),(13,'bkbn','gfcgfc','ggg@ww.uk','112223',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:45:10','2025-09-01 08:45:10'),(14,'sdfsgfd','awfafe','afae@dd.com','3132413',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:45:47','2025-09-01 08:45:47'),(15,'sdfsgfd','awfafe','afae@dd.com','3132413',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:46:42','2025-09-01 08:46:42'),(16,'erawefr','fsFASDASSA','SGF@sfgdsfg.com','124123243',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:47:05','2025-09-01 08:47:05'),(17,'Test','User','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:48:06','2025-09-01 08:48:06'),(18,'Test','User','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:48:39','2025-09-01 08:48:39'),(19,'Test','User','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:48:48','2025-09-01 08:48:48'),(20,'xfbfzx','sdgsfgd','jhgh@as.uk','1234234',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:51:16','2025-09-01 08:51:16'),(21,'xfbfzx','sdgsfgd','jhgh@as.uk','1234234',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:53:14','2025-09-01 08:53:14'),(22,'xfbfzx','sdgsfgd','jhgh@as.uk','1234234',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:53:17','2025-09-01 08:53:17'),(23,'zssdd','weewqe','wewfs@dsf.uk','134',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:53:33','2025-09-01 08:53:33'),(24,'zssdd','weewqe','wewfs@dsf.uk','134',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:55:19','2025-09-01 08:55:19'),(25,'zssdd','weewqe','test@example.com','134',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:55:28','2025-09-01 08:55:28'),(26,'qweqwe','qwq','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:55:47','2025-09-01 08:55:47'),(27,'Test','User','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:57:44','2025-09-01 08:57:44'),(28,'qweqwe','qwq','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:58:32','2025-09-01 08:58:32'),(29,'qweqwe','qwq','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:58:34','2025-09-01 08:58:34'),(30,'erawefr','fsFASDASSA','SGF@sfgdsfg.com','124123243',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:58:40','2025-09-01 08:58:40'),(31,'asdasd','wwqdfwf','wdfqw@asdkhhakds.uk','1134',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 08:58:59','2025-09-01 08:58:59'),(32,'Test','User','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 09:00:34','2025-09-01 09:00:34'),(33,'asdasd','wwqdfwf','wdfqw@asdkhhakds.uk','1134',NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 09:01:20','2025-09-01 09:01:20'),(34,'sadfsadf','swfasdf','sdf@wef.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 09:01:37','2025-09-01 09:01:37'),(35,'zsfga','sfgasgf','qad@mj.uk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 09:02:54','2025-09-01 09:02:54'),(36,'Test','User','test@example.com','0123456789','Test Club','club','11-a-side',NULL,'Paris','Test account request','rejected','Rejected: feF',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-01 09:08:25','2025-09-01 09:14:27'),(37,'Votre','Nom','im0668@gmail.com','0123456789','Test Club','club','11-a-side',NULL,'Paris','Test new request','approved','OK',NULL,NULL,NULL,NULL,NULL,NULL,'im0668@gmail.com','ZsTomf83Yc37','2025-09-01 13:51:36','2025-09-01 09:25:12','2025-09-01 13:51:36');
/*!40000 ALTER TABLE `account_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_integrations`
--

DROP TABLE IF EXISTS `api_integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_integrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `api_name` varchar(255) NOT NULL,
  `api_type` enum('health','fitness','mental_health','medical','sports') NOT NULL,
  `api_endpoint` varchar(500) NOT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `connection_status` enum('connected','disconnected','error','pending') DEFAULT 'connected',
  `data_types` json DEFAULT NULL,
  `last_sync` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_api` (`player_id`,`api_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_integrations`
--

LOCK TABLES `api_integrations` WRITE;
/*!40000 ALTER TABLE `api_integrations` DISABLE KEYS */;
INSERT INTO `api_integrations` VALUES (1,4,'Apple HealthKit','health','healthkit.apple.com/api/v1','apple_health_key_2024','connected','[\"fréquence cardiaque\", \"sommeil\", \"activité\", \"nutrition\"]','2025-08-31 13:09:49','2025-08-31 13:09:49','2025-08-31 13:09:49'),(2,4,'Garmin Connect','fitness','api.garmin.com/connect','garmin_connect_key_789','connected','[\"GPS\", \"performance\", \"récupération\", \"stress\"]','2025-08-31 11:09:49','2025-08-31 13:09:49','2025-08-31 13:09:49'),(3,4,'Calm API','mental_health','api.calm.com/v1','calm_api_key_123','connected','[\"méditation\", \"sommeil\", \"stress\", \"respiration\"]','2025-08-30 13:09:49','2025-08-31 13:09:49','2025-08-31 13:09:49'),(4,4,'FIFA Physio API','medical','api.fifa-physio.com/v1','fifa_physio_key_2024','connected','[\"traitements\", \"rééducation\", \"suivi\", \"blessures\"]','2025-08-28 13:09:49','2025-08-31 13:09:49','2025-08-31 13:09:49');
/*!40000 ALTER TABLE `api_integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `fifa_connect_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `doctor_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `appointment_date` datetime NOT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `type` enum('consultation','examination','follow_up','emergency') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'consultation',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_athlete_id_appointment_date_index` (`athlete_id`,`appointment_date`),
  KEY `appointments_fifa_connect_id_index` (`fifa_connect_id`),
  KEY `appointments_status_index` (`status`),
  CONSTRAINT `appointments_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `associations`
--

DROP TABLE IF EXISTS `associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `associations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `confederation_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `confederation` varchar(100) DEFAULT NULL,
  `fifa_ranking` int DEFAULT NULL,
  `fifa_version` varchar(50) DEFAULT NULL,
  `fifa_sync_status` enum('pending','synced','failed') NOT NULL DEFAULT 'pending',
  `fifa_sync_date` timestamp NULL DEFAULT NULL,
  `fifa_last_error` text,
  `association_logo_url` varchar(500) DEFAULT NULL,
  `nation_flag_url` varchar(500) DEFAULT NULL,
  `founded_year` int DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `address` text,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `associations_name_index` (`name`),
  KEY `associations_code_index` (`short_name`),
  KEY `fk_associations_confederation` (`confederation_id`),
  KEY `associations_tenant_id_index` (`tenant_id`),
  CONSTRAINT `associations_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_associations_confederation` FOREIGN KEY (`confederation_id`) REFERENCES `confederations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `associations`
--

LOCK TABLES `associations` WRITE;
/*!40000 ALTER TABLE `associations` DISABLE KEYS */;
INSERT INTO `associations` VALUES (1,NULL,1,'Fédération Tunisienne de Football','FTF','Tunisie',NULL,NULL,NULL,'pending',NULL,NULL,'associations/logos/k5NmFyIiPWPbZGqQVAytEA9XyQirDZq5bHNvr59P.png','associations/flags/f0MkML9eprF4SRC72Z3BtxcH4vTQzKFL2NMQ519Y.png',NULL,'associations/logos/federation-tunisienne.png',NULL,NULL,NULL,NULL,'active','2025-08-26 21:09:09','2025-08-30 14:45:49'),(2,NULL,2,'Fédération Française de Football','FFF','France',NULL,NULL,NULL,'pending',NULL,NULL,'association_logos/BVeSEiOisEQ1tu3731ulrPQXTzUTI4riodTwYTZL.jpg','nation_flags/4u5DHwy1bAC8xPov99Xpp33PC3YEmgvlS2PHUCDS.png',NULL,NULL,NULL,NULL,NULL,NULL,'active','2025-08-26 21:09:09','2025-08-31 14:52:34'),(3,NULL,2,'Fédération Anglaise de Football','FA','Angleterre',NULL,NULL,NULL,'pending',NULL,NULL,'association_logos/association_3_1756653050.png','nation_flags/nation_3_1756653050.jpg',NULL,NULL,NULL,NULL,NULL,NULL,'active','2025-08-26 21:09:09','2025-08-31 15:10:50');
/*!40000 ALTER TABLE `associations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `athletes`
--

DROP TABLE IF EXISTS `athletes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `athletes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fifa_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  `nationality` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `team_id` bigint unsigned NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jersey_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male',
  `blood_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` json DEFAULT NULL,
  `medical_history` json DEFAULT NULL,
  `allergies` json DEFAULT NULL,
  `medications` json DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `athletes_fifa_id_unique` (`fifa_id`),
  KEY `athletes_fifa_id_index` (`fifa_id`),
  KEY `athletes_team_id_index` (`team_id`),
  KEY `athletes_nationality_index` (`nationality`),
  KEY `athletes_active_index` (`active`),
  CONSTRAINT `athletes_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `athletes`
--

LOCK TABLES `athletes` WRITE;
/*!40000 ALTER TABLE `athletes` DISABLE KEYS */;
/*!40000 ALTER TABLE `athletes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `model_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `changes` json DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `severity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_event_type_created_at_index` (`event_type`,`created_at`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `audit_logs_action_created_at_index` (`action`,`created_at`),
  KEY `audit_logs_severity_created_at_index` (`severity`,`created_at`),
  KEY `audit_logs_tenant_id_created_at_index` (`tenant_id`,`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'user_action','App\\Models\\User',NULL,NULL,NULL,NULL,NULL,'login','Connexion utilisateur','127.0.0.1','Symfony','http://localhost:8080','GET',1,'System Administrator','admin@medpredictor.com',NULL,'auth','info',NULL,'2025-09-05 16:37:18','2025-09-05 16:37:18'),(2,'model_change','App\\Models\\User',1,NULL,'{\"name\": \"Old Name\"}','{\"name\": \"System Administrator\"}','{\"name\": \"System Administrator\"}','update','Mise à jour du profil utilisateur','127.0.0.1','Symfony','http://localhost:8080','GET',1,'System Administrator','admin@medpredictor.com',NULL,NULL,'info',NULL,'2025-09-05 16:37:18','2025-09-05 16:37:18'),(3,'security','security',NULL,NULL,NULL,NULL,NULL,'failed_login','Tentative de connexion échouée','127.0.0.1','Symfony','http://localhost:8080','GET',1,'System Administrator','admin@medpredictor.com',NULL,'security','warning',NULL,'2025-09-05 16:37:18','2025-09-05 16:37:18'),(4,'system','system',NULL,NULL,NULL,NULL,NULL,'backup','Sauvegarde automatique effectuée','127.0.0.1','Symfony','http://localhost:8080','GET',1,'System Administrator','admin@medpredictor.com',NULL,'system','info',NULL,'2025-09-05 16:37:18','2025-09-05 16:37:18'),(5,'created','App\\Models\\Club',1,'Club Demo',NULL,NULL,NULL,'create','Nouveau club créé','127.0.0.1','Symfony','http://localhost:8080','GET',1,'System Administrator','admin@medpredictor.com',NULL,'clubs','info',NULL,'2025-09-05 16:37:18','2025-09-05 16:37:18');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banned_substances`
--

DROP TABLE IF EXISTS `banned_substances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banned_substances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `substance_name` varchar(255) NOT NULL,
  `substance_category` enum('anabolic_steroids','stimulants','diuretics','peptide_hormones','beta_blockers','cannabinoids','narcotics','glucocorticoids','other') NOT NULL,
  `wada_code` varchar(50) DEFAULT NULL,
  `detection_date` date DEFAULT NULL,
  `last_detected` date DEFAULT NULL,
  `detection_count` int DEFAULT '0',
  `status` enum('active','cleared','monitoring') DEFAULT 'active',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_substance` (`player_id`,`substance_name`),
  KEY `idx_player_category` (`player_id`,`substance_category`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banned_substances`
--

LOCK TABLES `banned_substances` WRITE;
/*!40000 ALTER TABLE `banned_substances` DISABLE KEYS */;
INSERT INTO `banned_substances` VALUES (1,4,'Testostérone','anabolic_steroids','S1.1',NULL,NULL,0,'cleared','Aucune détection - Joueur propre','2025-08-31 13:13:08','2025-08-31 13:13:08'),(2,4,'Éphédrine','stimulants','S6.1',NULL,NULL,0,'cleared','Aucune détection - Contrôles négatifs','2025-08-31 13:13:08','2025-08-31 13:13:08'),(3,4,'Furosémide','diuretics','S5.1',NULL,NULL,0,'cleared','Aucune détection - Profil urinaire normal','2025-08-31 13:13:08','2025-08-31 13:13:08');
/*!40000 ALTER TABLE `banned_substances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `behavioral_data`
--

DROP TABLE IF EXISTS `behavioral_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `behavioral_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `data_date` date NOT NULL,
  `sleep_hours` decimal(3,1) DEFAULT NULL,
  `sleep_quality_score` decimal(5,2) DEFAULT NULL,
  `steps_count` int DEFAULT NULL,
  `calories_burned` int DEFAULT NULL,
  `active_minutes` int DEFAULT NULL,
  `sedentary_minutes` int DEFAULT NULL,
  `stress_level` decimal(5,2) DEFAULT NULL,
  `mood_score` decimal(5,2) DEFAULT NULL,
  `heart_rate_variability` decimal(5,2) DEFAULT NULL,
  `resting_heart_rate` int DEFAULT NULL,
  `device_source` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`data_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `behavioral_data`
--

LOCK TABLES `behavioral_data` WRITE;
/*!40000 ALTER TABLE `behavioral_data` DISABLE KEYS */;
INSERT INTO `behavioral_data` VALUES (1,4,'2025-08-31',8.2,8.50,12450,2850,180,480,25.00,8.20,45.50,58,'Apple Watch','2025-08-31 13:03:16','2025-08-31 13:03:16'),(2,4,'2025-08-30',7.8,7.90,11800,2650,165,495,28.00,7.80,42.00,61,'Apple Watch','2025-08-31 13:03:16','2025-08-31 13:03:16');
/*!40000 ALTER TABLE `behavioral_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL COMMENT 'Nom abrégé du club',
  `association_id` bigint unsigned DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `address` text,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `founded_year` int DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clubs_name_index` (`name`),
  KEY `clubs_association_id_index` (`association_id`),
  KEY `clubs_tenant_id_index` (`tenant_id`),
  CONSTRAINT `clubs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubs`
--

LOCK TABLES `clubs` WRITE;
/*!40000 ALTER TABLE `clubs` DISABLE KEYS */;
INSERT INTO `clubs` VALUES (1,1,'Club Africain','CA',1,'club_logos/uBSg4JJYzOUi91bbsXZrlBYOm6GapUNyRCwS5EPQ.png','Stade Olympique de Radès, Radès, Tunisie','+216 71 234 567','contact@clubafricain.com','https://www.clubafricain.com',1920,'active','2025-08-26 21:09:17','2025-09-07 02:04:13'),(2,1,'Espérance Sportive de Tunis','EST',1,'club_logos/hL7bn3WEr4ynOlbgKsx6taXJP2gnpdum4T3fOgXL.png','Stade Olympique de Radès, Radès, Tunisie','+216 71 345 678','contact@esperance.com','https://www.esperance.com',1919,'active','2025-08-26 21:09:17','2025-09-07 02:04:13'),(3,2,'Paris Saint-Germain','PSG',2,'club_logos/club_3_1756651757.jpeg',NULL,NULL,NULL,NULL,1973,'active','2025-08-26 21:09:17','2025-09-07 02:04:14'),(4,2,'Manchester United','MU',3,'club_logos/club_4_1756652611.svg',NULL,NULL,NULL,NULL,NULL,'active','2025-08-26 21:09:17','2025-09-07 02:04:14'),(6,NULL,'Étoile du Sahel','ESS',1,'club_logos/club_6_1756651488.svg','Stade Olympique de Sousse, Sousse, Tunisie','+216 73 000 000','contact@etoile-du-sahel.com','http://www.etoile-du-sahel.com',NULL,'active','2025-08-31 14:15:03','2025-08-31 14:44:48'),(7,NULL,'CS Sfaxien','CSS',1,NULL,'Stade Taïeb Mhiri, Sfax',NULL,NULL,NULL,1928,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(8,NULL,'US Monastir','USM',1,NULL,'Stade Mustapha Ben Jannet, Monastir',NULL,NULL,NULL,1959,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(9,NULL,'CA Bizertin','CAB',1,NULL,'Stade 15 Octobre, Bizerte',NULL,NULL,NULL,1928,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(10,NULL,'JS Kairouan','JSK',1,NULL,'Stade Hamda Laouani, Kairouan',NULL,NULL,NULL,1942,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(11,NULL,'AS Gabès','ASG',1,NULL,'Stade Municipal de Gabès, Gabès',NULL,NULL,NULL,1977,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(12,NULL,'Stade Tunisien','ST',1,NULL,'Stade Chedli Zouiten, Tunis',NULL,NULL,NULL,1948,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(13,NULL,'US Ben Guerdane','USBG',1,NULL,'Stade du 7 Mars, Ben Guerdane',NULL,NULL,NULL,1936,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(14,NULL,'Olympique de Béja','OB',1,NULL,'Stade Boujemaa Kmiti, Béja',NULL,NULL,NULL,1929,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(15,NULL,'AS Soliman','ASS',1,NULL,'Stade Municipal de Soliman, Soliman',NULL,NULL,NULL,1958,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(16,NULL,'CS Hammam-Lif','CSHL',1,NULL,'Stade Municipal de Hammam-Lif, Hammam-Lif',NULL,NULL,NULL,1944,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(17,NULL,'JS Médenine','JSM',1,NULL,'Stade Municipal de Médenine, Médenine',NULL,NULL,NULL,1956,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(18,NULL,'US Tataouine','UST',1,NULL,'Stade Municipal de Tataouine, Tataouine',NULL,NULL,NULL,1980,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(19,NULL,'AS Marsa','ASM',1,NULL,'Stade Abdelaziz Chtioui, La Marsa',NULL,NULL,NULL,1948,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(20,NULL,'ES Métlaoui','ESM',1,NULL,'Stade Municipal de Métlaoui, Métlaoui',NULL,NULL,NULL,1950,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(21,NULL,'US Siliana','USS',1,NULL,'Stade Municipal de Siliana, Siliana',NULL,NULL,NULL,1975,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(22,NULL,'AS Djerba','ASD',1,NULL,'Stade Municipal de Djerba, Djerba',NULL,NULL,NULL,1960,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(23,NULL,'JS Tabarka','JST',1,NULL,'Stade Municipal de Tabarka, Tabarka',NULL,NULL,NULL,1985,'active','2025-09-02 18:14:20','2025-09-02 18:14:20'),(24,NULL,'Club Africain de Tunis','CA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active','2025-09-07 02:02:48','2025-09-07 02:02:48'),(25,NULL,'Espérance Sportive de Tunis','EST',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active','2025-09-07 02:02:48','2025-09-07 02:02:48');
/*!40000 ALTER TABLE `clubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competition_club`
--

DROP TABLE IF EXISTS `competition_club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competition_club` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `competition_id` bigint unsigned NOT NULL,
  `club_id` bigint unsigned NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registered',
  `points` int NOT NULL DEFAULT '0',
  `goals_for` int NOT NULL DEFAULT '0',
  `goals_against` int NOT NULL DEFAULT '0',
  `goal_difference` int NOT NULL DEFAULT '0',
  `matches_played` int NOT NULL DEFAULT '0',
  `wins` int NOT NULL DEFAULT '0',
  `draws` int NOT NULL DEFAULT '0',
  `losses` int NOT NULL DEFAULT '0',
  `registration_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competition_club_competition_id_club_id_unique` (`competition_id`,`club_id`),
  KEY `competition_club_club_id_foreign` (`club_id`),
  CONSTRAINT `competition_club_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `competition_club_competition_id_foreign` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competition_club`
--

LOCK TABLES `competition_club` WRITE;
/*!40000 ALTER TABLE `competition_club` DISABLE KEYS */;
INSERT INTO `competition_club` VALUES (1,3,1,'registered',11,11,9,-4,11,2,5,6,'2025-09-02 18:12:50','2025-09-02 18:12:50','2025-09-02 18:12:50'),(2,3,2,'registered',8,11,10,-2,9,3,2,6,'2025-09-02 18:12:50','2025-09-02 18:12:50','2025-09-02 18:12:50'),(3,3,6,'registered',11,25,4,-2,5,0,3,5,'2025-09-02 18:12:50','2025-09-02 18:12:50','2025-09-02 18:12:50'),(7,3,7,'registered',11,20,3,-1,7,1,1,3,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(8,3,8,'registered',30,24,10,5,8,2,4,3,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(9,3,9,'registered',7,15,5,-2,10,6,1,4,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(10,3,10,'registered',1,10,13,2,15,7,5,5,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(11,3,11,'registered',6,5,16,0,13,0,0,2,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(12,3,12,'registered',4,18,18,-2,7,1,0,4,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(13,3,13,'registered',27,6,12,-2,9,6,3,3,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(14,3,14,'registered',4,13,17,1,6,5,0,1,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(15,3,15,'registered',9,20,15,5,10,8,1,6,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(16,3,16,'registered',8,21,5,7,8,3,1,5,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(17,3,17,'registered',8,7,17,-2,15,5,0,2,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(18,3,18,'registered',0,5,20,-5,12,3,5,0,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(19,3,19,'registered',19,13,4,14,7,4,0,3,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(20,3,20,'registered',18,16,4,11,10,3,1,3,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(21,3,21,'registered',12,5,12,-1,8,4,5,2,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(22,3,22,'registered',21,20,16,14,13,3,4,5,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22'),(23,3,23,'registered',20,16,3,15,6,7,5,4,'2025-09-02 18:14:22','2025-09-02 18:14:22','2025-09-02 18:14:22');
/*!40000 ALTER TABLE `competition_club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competitions`
--

DROP TABLE IF EXISTS `competitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `association_id` bigint unsigned DEFAULT NULL,
  `fifa_connect_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `require_federation_license` tinyint(1) NOT NULL DEFAULT '0',
  `fifa_sync_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `fifa_sync_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fifa_sync_date` timestamp NULL DEFAULT NULL,
  `fifa_last_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `registration_deadline` date DEFAULT NULL,
  `max_teams` int DEFAULT NULL,
  `min_teams` int DEFAULT NULL,
  `format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prize_pool` decimal(15,2) DEFAULT NULL,
  `entry_fee` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organizer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sponsors` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `broadcast_partners` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `competitions_association_id_foreign` (`association_id`),
  KEY `competitions_tenant_id_index` (`tenant_id`),
  CONSTRAINT `competitions_association_id_foreign` FOREIGN KEY (`association_id`) REFERENCES `associations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `competitions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competitions`
--

LOCK TABLES `competitions` WRITE;
/*!40000 ALTER TABLE `competitions` DISABLE KEYS */;
INSERT INTO `competitions` VALUES (1,NULL,1,NULL,0,0,'pending',NULL,NULL,'Ligue 1 Tunisienne','L1T','national',NULL,NULL,'2024-2025','2024-08-15','2025-05-25','2024-07-31',16,16,'round_robin',NULL,NULL,'active','Championnat national de Tunisie',NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-30 16:55:33','2025-08-30 16:55:33',NULL),(2,NULL,2,'COMP_AJcrfACx_1756836288',1,1,'synced',NULL,NULL,'Ligue 1 Uber Eats','L1','league','France',NULL,'2023-2024','2023-08-12','2024-05-19',NULL,20,20,'round_robin',NULL,NULL,'completed','Championnat de France de football - Division 1','Règlement de la Ligue 1 Uber Eats 2023-2024',NULL,NULL,NULL,NULL,NULL,'2025-09-02 18:04:48','2025-09-02 18:04:48',NULL),(3,NULL,1,'COMP_FH9w7z6F_1756836700',1,1,'synced',NULL,NULL,'Ligue 1 Tunisienne','L1-TN','league','Tunisie',NULL,'2023-2024','2023-08-12','2024-05-19',NULL,16,16,'round_robin',NULL,NULL,'completed','Championnat de Tunisie de football - Division 1','Règlement de la Ligue 1 Tunisienne 2023-2024',NULL,NULL,NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40',NULL),(4,NULL,1,'COMP_Qk4JWljK_1756906785',0,0,'pending',NULL,NULL,'Championnat Tunisien Ligue 1',NULL,'league',NULL,NULL,'2024-2025','2024-08-15','2025-05-31',NULL,NULL,NULL,NULL,NULL,NULL,'active','Championnat de première division tunisienne',NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-03 13:39:45','2025-09-03 13:39:45',NULL);
/*!40000 ALTER TABLE `competitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compliance_resources`
--

DROP TABLE IF EXISTS `compliance_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compliance_resources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `resource_type` enum('guide','procedure','contact','document','training') NOT NULL,
  `resource_name` varchar(255) NOT NULL,
  `resource_url` varchar(500) DEFAULT NULL,
  `resource_description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `priority` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_type` (`player_id`,`resource_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compliance_resources`
--

LOCK TABLES `compliance_resources` WRITE;
/*!40000 ALTER TABLE `compliance_resources` DISABLE KEYS */;
INSERT INTO `compliance_resources` VALUES (1,4,'guide','Guide Anti-Dopage FIFA 2024','https://resources.fifa.com/anti-doping-guide-2024','Guide complet des règles anti-dopage FIFA - Mise à jour 2024',1,1,'2025-08-31 13:24:24','2025-08-31 13:24:24'),(2,4,'procedure','Procédures de Déclaration de Médicaments','https://resources.fifa.com/medication-declaration','Procédures obligatoires pour déclarer tout médicament dans les 24h',1,2,'2025-08-31 13:24:24','2025-08-31 13:24:24'),(3,4,'document','Liste des Substances Interdites WADA 2024','https://www.wada-ama.org/prohibited-list-2024','Liste officielle des substances et méthodes interdites',1,1,'2025-08-31 13:24:24','2025-08-31 13:24:24'),(4,4,'contact','Équipe Médicale FIFA','mailto:medical@fifa.com','Contact direct pour questions médicales et conformité',1,3,'2025-08-31 13:24:24','2025-08-31 13:24:24'),(5,4,'training','Formation Anti-Dopage en Ligne','https://training.fifa.com/anti-doping-certification','Formation obligatoire annuelle - Certification requise',1,2,'2025-08-31 13:24:24','2025-08-31 13:24:24');
/*!40000 ALTER TABLE `compliance_resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compliance_status`
--

DROP TABLE IF EXISTS `compliance_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compliance_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `compliance_type` enum('anti_doping','medical','licensing','behavioral') NOT NULL,
  `status` enum('compliant','non_compliant','under_review','warning') DEFAULT 'compliant',
  `last_assessment_date` date NOT NULL,
  `next_assessment_date` date DEFAULT NULL,
  `compliance_score` decimal(5,2) DEFAULT '100.00',
  `violations_count` int DEFAULT '0',
  `warnings_count` int DEFAULT '0',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_type` (`player_id`,`compliance_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compliance_status`
--

LOCK TABLES `compliance_status` WRITE;
/*!40000 ALTER TABLE `compliance_status` DISABLE KEYS */;
INSERT INTO `compliance_status` VALUES (1,4,'anti_doping','compliant','2025-08-31','2025-12-01',98.50,0,1,'Excellent niveau de conformité - 1 avertissement mineur pour rappel de procédure','2025-08-31 13:24:04','2025-08-31 13:24:04'),(2,4,'medical','compliant','2025-08-16','2025-10-31',95.00,0,0,'TUE approuvées et respectées - Aucune violation','2025-08-31 13:24:04','2025-08-31 13:24:04');
/*!40000 ALTER TABLE `compliance_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confederations`
--

DROP TABLE IF EXISTS `confederations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `confederations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fifa_ranking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fifa_version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fifa_sync_status` enum('pending','syncing','synced','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fifa_sync_date` timestamp NULL DEFAULT NULL,
  `fifa_last_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `confederation_logo_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `founded_year` int DEFAULT NULL,
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `confederations_code_unique` (`short_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confederations`
--

LOCK TABLES `confederations` WRITE;
/*!40000 ALTER TABLE `confederations` DISABLE KEYS */;
INSERT INTO `confederations` VALUES (1,'Confédération Africaine de Football','CAF','Afrique','1','FIFA 24','synced',NULL,NULL,'confederation_logos/Ndrq6xiJdD7kF9jb3Qdj9rIlFNgDgcxFB0q86qZC.jpg',1957,'active','2025-08-30 16:15:41','2025-08-30 16:32:51'),(2,'Union des Associations Européennes de Football','UEFA','Europe','1','FIFA 24','synced',NULL,NULL,'confederation_logos/usWDgf7kiKjd371Be6rWIDGADJWKBp54a6CGzgXS.png',1954,'active','2025-08-30 16:15:41','2025-08-30 16:33:13');
/*!40000 ALTER TABLE `confederations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doping_alerts`
--

DROP TABLE IF EXISTS `doping_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doping_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `alert_type` enum('test_due','positive_result','substance_detected','tue_expiring','compliance_warning') NOT NULL,
  `alert_severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `alert_message` text NOT NULL,
  `alert_date` date NOT NULL,
  `alert_status` enum('active','acknowledged','resolved') DEFAULT 'active',
  `related_test_id` bigint unsigned DEFAULT NULL,
  `related_substance_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_alert` (`player_id`,`alert_type`),
  KEY `idx_player_severity` (`player_id`,`alert_severity`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doping_alerts`
--

LOCK TABLES `doping_alerts` WRITE;
/*!40000 ALTER TABLE `doping_alerts` DISABLE KEYS */;
INSERT INTO `doping_alerts` VALUES (1,4,'test_due','medium','Test anti-dopage programmé dans 30 jours - Préparation requise','2025-08-31','active',NULL,NULL,'2025-08-31 13:13:34','2025-08-31 13:13:34'),(2,4,'tue_expiring','low','Autorisation TUE pour Cortisone expire dans 15 jours - Renouvellement recommandé','2025-08-31','active',NULL,NULL,'2025-08-31 13:13:34','2025-08-31 13:13:34'),(3,4,'compliance_warning','low','Rappel: Déclaration obligatoire de tout médicament dans les 24h','2025-08-31','acknowledged',NULL,NULL,'2025-08-31 13:13:34','2025-08-31 13:13:34');
/*!40000 ALTER TABLE `doping_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doping_tests`
--

DROP TABLE IF EXISTS `doping_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doping_tests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `test_date` date NOT NULL,
  `test_type` enum('urine','sang','salive','cheveux','sueur') NOT NULL,
  `test_location` varchar(255) DEFAULT NULL,
  `test_laboratory` varchar(255) DEFAULT NULL,
  `test_result` enum('negative','positive','inconclusive','pending') DEFAULT 'pending',
  `banned_substances` json DEFAULT NULL,
  `substance_levels` json DEFAULT NULL,
  `test_notes` text,
  `next_test_date` date DEFAULT NULL,
  `test_status` enum('completed','in_progress','scheduled','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`test_date`),
  KEY `idx_player_result` (`player_id`,`test_result`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doping_tests`
--

LOCK TABLES `doping_tests` WRITE;
/*!40000 ALTER TABLE `doping_tests` DISABLE KEYS */;
INSERT INTO `doping_tests` VALUES (1,4,'2025-08-01','urine','Centre de Contrôle FIFA - Zurich','Laboratoire Anti-Dopage Suisse (LADS)','negative','[]','[]','Test de routine - Aucune substance interdite détectée','2025-10-30','completed','2025-08-31 13:12:57','2025-08-31 13:12:57'),(2,4,'2025-06-02','sang','Stade de France - Paris','Laboratoire National de Dépistage du Dopage (LNDD)','negative','[]','[]','Test post-match - Contrôles sanguins normaux','2025-08-01','completed','2025-08-31 13:12:57','2025-08-31 13:12:57'),(3,4,'2025-09-30','urine','Centre Médical du Club','Laboratoire Anti-Dopage FIFA','pending','[]','[]','Test programmé - Contrôle de routine',NULL,'scheduled','2025-08-31 13:12:57','2025-08-31 13:12:57');
/*!40000 ALTER TABLE `doping_tests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fifa_connect_ids`
--

DROP TABLE IF EXISTS `fifa_connect_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fifa_connect_ids` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fifa_connect_ids`
--

LOCK TABLES `fifa_connect_ids` WRITE;
/*!40000 ALTER TABLE `fifa_connect_ids` DISABLE KEYS */;
/*!40000 ALTER TABLE `fifa_connect_ids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_matches`
--

DROP TABLE IF EXISTS `game_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_matches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `competition_id` bigint unsigned DEFAULT NULL,
  `home_team_id` bigint unsigned DEFAULT NULL,
  `away_team_id` bigint unsigned DEFAULT NULL,
  `home_club_id` bigint unsigned DEFAULT NULL,
  `away_club_id` bigint unsigned DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `kickoff_time` datetime DEFAULT NULL,
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stadium` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `attendance` int DEFAULT NULL,
  `weather_conditions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pitch_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistant_referee_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistant_referee_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fourth_official` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `var_referee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_status` enum('scheduled','live','completed','postponed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `home_score` int DEFAULT NULL,
  `away_score` int DEFAULT NULL,
  `home_penalties` int DEFAULT NULL,
  `away_penalties` int DEFAULT NULL,
  `home_yellow_cards` int DEFAULT NULL,
  `away_yellow_cards` int DEFAULT NULL,
  `home_red_cards` int DEFAULT NULL,
  `away_red_cards` int DEFAULT NULL,
  `home_possession` int DEFAULT NULL,
  `away_possession` int DEFAULT NULL,
  `home_shots` int DEFAULT NULL,
  `away_shots` int DEFAULT NULL,
  `home_shots_on_target` int DEFAULT NULL,
  `away_shots_on_target` int DEFAULT NULL,
  `home_corners` int DEFAULT NULL,
  `away_corners` int DEFAULT NULL,
  `home_fouls` int DEFAULT NULL,
  `away_fouls` int DEFAULT NULL,
  `home_offsides` int DEFAULT NULL,
  `away_offsides` int DEFAULT NULL,
  `match_highlights` text COLLATE utf8mb4_unicode_ci,
  `match_report` text COLLATE utf8mb4_unicode_ci,
  `broadcast_info` text COLLATE utf8mb4_unicode_ci,
  `ticket_info` text COLLATE utf8mb4_unicode_ci,
  `matchday` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `referee_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_matches_away_team_id_foreign` (`away_team_id`),
  KEY `game_matches_away_club_id_foreign` (`away_club_id`),
  KEY `game_matches_referee_id_foreign` (`referee_id`),
  KEY `game_matches_created_by_foreign` (`created_by`),
  KEY `game_matches_updated_by_foreign` (`updated_by`),
  KEY `game_matches_competition_id_match_status_index` (`competition_id`,`match_status`),
  KEY `game_matches_home_team_id_away_team_id_index` (`home_team_id`,`away_team_id`),
  KEY `game_matches_home_club_id_away_club_id_index` (`home_club_id`,`away_club_id`),
  KEY `game_matches_match_date_index` (`match_date`),
  CONSTRAINT `game_matches_away_club_id_foreign` FOREIGN KEY (`away_club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_away_team_id_foreign` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_competition_id_foreign` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_home_club_id_foreign` FOREIGN KEY (`home_club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_home_team_id_foreign` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_referee_id_foreign` FOREIGN KEY (`referee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_matches_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_matches`
--

LOCK TABLES `game_matches` WRITE;
/*!40000 ALTER TABLE `game_matches` DISABLE KEYS */;
/*!40000 ALTER TABLE `game_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `health_records`
--

DROP TABLE IF EXISTS `health_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `player_id` bigint unsigned DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `doctor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_type` enum('consultation','emergency','follow_up','pre_season','post_match','rehabilitation') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chief_complaint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `physical_examination` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `laboratory_results` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imaging_results` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prescriptions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `follow_up_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `visit_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `blood_pressure_systolic` int DEFAULT NULL,
  `blood_pressure_diastolic` int DEFAULT NULL,
  `heart_rate` int DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,2) DEFAULT NULL,
  `blood_type` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allergies` json DEFAULT NULL,
  `medications` json DEFAULT NULL,
  `medical_history` json DEFAULT NULL,
  `symptoms` json DEFAULT NULL,
  `diagnosis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `treatment_plan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `risk_score` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `prediction_confidence` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `record_date` timestamp NOT NULL,
  `next_checkup_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','archived','pending','hl7_report','review_required','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `illness_records` json DEFAULT NULL COMMENT 'ICD-10 codes for general illnesses',
  `heart_disease_records` json DEFAULT NULL COMMENT 'ICD-10 codes for cardiovascular diseases',
  `icd_10_primary_diagnosis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Primary ICD-10 diagnosis code',
  `icd_10_secondary_diagnosis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Secondary ICD-10 diagnosis codes',
  `snomed_ct_condition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT condition identifier',
  `doping_tests` json DEFAULT NULL COMMENT 'Doping test results and history',
  `doping_test_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Current doping test status',
  `last_doping_test_date` timestamp NULL DEFAULT NULL,
  `next_doping_test_date` timestamp NULL DEFAULT NULL,
  `doping_test_lab` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Laboratory performing doping tests',
  `loinc_doping_panel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOINC code for doping panel',
  `aut_records` json DEFAULT NULL COMMENT 'AUT - Autorisation d''Usage Thérapeutique',
  `aut_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'AUT approval status',
  `aut_approval_date` timestamp NULL DEFAULT NULL,
  `aut_expiry_date` timestamp NULL DEFAULT NULL,
  `aut_authorized_substance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Substance authorized for therapeutic use',
  `aut_authorizing_physician` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aut_medical_justification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `snomed_ct_aut` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for therapeutic authorization',
  `blood_test_results` json DEFAULT NULL COMMENT 'Comprehensive blood test results',
  `blood_test_panel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOINC code for blood test panel',
  `last_blood_test_date` timestamp NULL DEFAULT NULL,
  `next_blood_test_date` timestamp NULL DEFAULT NULL,
  `hematology_results` json DEFAULT NULL COMMENT 'Complete blood count and hematology',
  `biochemistry_results` json DEFAULT NULL COMMENT 'Biochemical markers',
  `hormone_results` json DEFAULT NULL COMMENT 'Hormonal profile',
  `vitamin_results` json DEFAULT NULL COMMENT 'Vitamin levels',
  `mineral_results` json DEFAULT NULL COMMENT 'Mineral and electrolyte levels',
  `biological_profile` json DEFAULT NULL COMMENT 'Comprehensive biological profile',
  `biological_age` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Calculated biological age',
  `metabolic_markers` json DEFAULT NULL COMMENT 'Metabolic health markers',
  `inflammatory_markers` json DEFAULT NULL COMMENT 'Inflammation markers',
  `oxidative_stress_markers` json DEFAULT NULL COMMENT 'Oxidative stress indicators',
  `snomed_ct_biological_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for biological profile',
  `dental_records` json DEFAULT NULL COMMENT 'Dental health records',
  `postural_assessment` json DEFAULT NULL COMMENT 'Postural analysis and assessment',
  `dental_health_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Overall dental health status',
  `postural_alignment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Postural alignment assessment',
  `dental_treatments` json DEFAULT NULL COMMENT 'Dental treatment history',
  `postural_corrections` json DEFAULT NULL COMMENT 'Postural correction interventions',
  `icd_10_dental` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ICD-10 codes for dental conditions',
  `icd_10_postural` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ICD-10 codes for postural disorders',
  `injury_records` json DEFAULT NULL COMMENT 'Comprehensive injury history',
  `fifa_fmarc_assessments` json DEFAULT NULL COMMENT 'FIFA F-MARC injury assessments',
  `scat_assessments` json DEFAULT NULL COMMENT 'SCAT concussion assessments',
  `injury_severity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Injury severity classification',
  `injury_mechanism` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mechanism of injury',
  `injury_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Anatomical location of injury',
  `injury_date` timestamp NULL DEFAULT NULL,
  `return_to_play_date` timestamp NULL DEFAULT NULL,
  `snomed_ct_injury` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for injury type',
  `icd_10_injury` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ICD-10 code for injury',
  `heart_disease_assessments` json DEFAULT NULL COMMENT 'Cardiovascular disease assessments',
  `cardiac_risk_factors` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cardiac risk factor assessment',
  `cardiac_markers` json DEFAULT NULL COMMENT 'Cardiac biomarkers',
  `heart_disease_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Current heart disease status',
  `icd_10_cardiac` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ICD-10 codes for cardiac conditions',
  `snomed_ct_cardiac` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for cardiac conditions',
  `mapa_results` json DEFAULT NULL COMMENT 'MAPA - Mesure ambulatoire de la P.A.',
  `mapa_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MAPA test status',
  `mapa_test_date` timestamp NULL DEFAULT NULL,
  `mapa_24h_profile` json DEFAULT NULL COMMENT '24-hour blood pressure profile',
  `mapa_day_night_ratio` json DEFAULT NULL COMMENT 'Day/night blood pressure ratio',
  `mapa_dipper_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dipper/non-dipper status',
  `loinc_mapa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOINC code for ambulatory BP monitoring',
  `mri_results` json DEFAULT NULL COMMENT 'MRI examination results',
  `mri_body_part` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Anatomical region imaged',
  `mri_technique` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MRI technique used',
  `mri_date` timestamp NULL DEFAULT NULL,
  `mri_facility` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MRI facility name',
  `mri_radiologist` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Radiologist interpreting MRI',
  `mri_findings` json DEFAULT NULL COMMENT 'Detailed MRI findings',
  `loinc_mri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOINC code for MRI examination',
  `snomed_ct_mri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for MRI procedure',
  `ecg_effort_results` json DEFAULT NULL COMMENT 'ECG effort test results',
  `ecg_effort_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ECG effort test status',
  `ecg_effort_date` timestamp NULL DEFAULT NULL,
  `ecg_effort_duration` int DEFAULT NULL COMMENT 'Test duration in minutes',
  `ecg_effort_max_hr` int DEFAULT NULL COMMENT 'Maximum heart rate achieved',
  `ecg_effort_interpretation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ECG effort interpretation',
  `ecg_effort_findings` json DEFAULT NULL COMMENT 'Detailed ECG effort findings',
  `loinc_ecg_effort` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOINC code for exercise ECG',
  `snomed_ct_ecg_effort` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for exercise ECG',
  `scintigraphy_results` json DEFAULT NULL COMMENT 'Scintigraphy examination results',
  `scintigraphy_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of scintigraphy',
  `scintigraphy_isotope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Radioisotope used',
  `scintigraphy_date` timestamp NULL DEFAULT NULL,
  `scintigraphy_facility` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Scintigraphy facility',
  `scintigraphy_radiologist` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Radiologist interpreting scintigraphy',
  `scintigraphy_findings` json DEFAULT NULL COMMENT 'Detailed scintigraphy findings',
  `loinc_scintigraphy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOINC code for scintigraphy',
  `snomed_ct_scintigraphy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SNOMED CT code for scintigraphy',
  `icd_10_codes` json DEFAULT NULL COMMENT 'All ICD-10 codes for this record',
  `snomed_ct_codes` json DEFAULT NULL COMMENT 'All SNOMED CT codes for this record',
  `loinc_codes` json DEFAULT NULL COMMENT 'All LOINC codes for this record',
  `medical_record_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of medical record',
  `medical_record_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Primary medical category',
  `aut_substance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Substance médicamenteuse autorisée',
  `aut_dosage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Posologie prescrite',
  `aut_diagnosis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Diagnostic médical justifiant l''AUT',
  `aut_justification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Justification médicale de l''usage thérapeutique',
  `aut_start_date` date DEFAULT NULL COMMENT 'Date de début de l''AUT',
  `aut_end_date` date DEFAULT NULL COMMENT 'Date de fin de l''AUT',
  `aut_application_form_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chemin vers le formulaire de demande AUT',
  `aut_response_document_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chemin vers le document de réponse officielle',
  `aut_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Notes et observations sur l''AUT',
  PRIMARY KEY (`id`),
  KEY `health_records_user_id_record_date_index` (`user_id`,`record_date`),
  KEY `health_records_player_id_record_date_index` (`player_id`,`record_date`),
  KEY `health_records_status_record_date_index` (`status`,`record_date`),
  CONSTRAINT `health_records_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE,
  CONSTRAINT `health_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `health_records`
--

LOCK TABLES `health_records` WRITE;
/*!40000 ALTER TABLE `health_records` DISABLE KEYS */;
INSERT INTO `health_records` VALUES (1,1,4,'2025-08-31','Dr. Martinez FIFA','follow_up',NULL,NULL,NULL,NULL,NULL,NULL,NULL,116,78,54,36.8,73.60,180.00,23.26,'O+',NULL,NULL,NULL,NULL,NULL,NULL,0.1500,0.0000,'2025-08-31 12:15:59',NULL,'active','2025-08-31 12:15:59','2025-08-31 12:15:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,4,'2025-08-24','Dr. Martinez FIFA','follow_up',NULL,NULL,NULL,NULL,NULL,NULL,NULL,118,75,59,36.8,76.70,180.00,23.39,'O+',NULL,NULL,NULL,NULL,NULL,NULL,0.1200,0.0000,'2025-08-24 12:15:59',NULL,'active','2025-08-31 12:15:59','2025-08-31 12:15:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,4,'2025-08-17','Dr. Martinez FIFA','follow_up',NULL,NULL,NULL,NULL,NULL,NULL,NULL,111,75,56,36.8,77.40,180.00,23.28,'O+',NULL,NULL,NULL,NULL,NULL,NULL,0.2000,0.0000,'2025-08-17 12:15:59',NULL,'active','2025-08-31 12:15:59','2025-08-31 12:15:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,1,4,'2025-08-10','Dr. Martinez FIFA','follow_up',NULL,NULL,NULL,NULL,NULL,NULL,NULL,114,75,54,36.9,76.80,180.00,23.23,'O+',NULL,NULL,NULL,NULL,NULL,NULL,0.1000,0.0000,'2025-08-10 12:15:59',NULL,'active','2025-08-31 12:15:59','2025-08-31 12:15:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `health_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `health_scores`
--

DROP TABLE IF EXISTS `health_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `score` int NOT NULL COMMENT 'Health score from 0-100',
  `trend` enum('improving','stable','worsening') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stable',
  `contributing_factors` json DEFAULT NULL COMMENT 'Factors that influenced the score',
  `metrics` json DEFAULT NULL COMMENT 'Detailed metrics used in calculation',
  `ai_analysis` json DEFAULT NULL COMMENT 'AI-generated insights',
  `calculated_date` date NOT NULL COMMENT 'Date for which score was calculated',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `health_scores_athlete_id_calculated_date_index` (`athlete_id`,`calculated_date`),
  KEY `health_scores_calculated_date_index` (`calculated_date`),
  KEY `health_scores_score_index` (`score`),
  KEY `health_scores_trend_index` (`trend`),
  CONSTRAINT `health_scores_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `health_scores`
--

LOCK TABLES `health_scores` WRITE;
/*!40000 ALTER TABLE `health_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `health_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `injury_alerts`
--

DROP TABLE IF EXISTS `injury_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `injury_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `injury_type` varchar(255) DEFAULT NULL,
  `body_part` varchar(255) DEFAULT NULL,
  `risk_level` decimal(5,2) DEFAULT NULL,
  `risk_description` text,
  `last_assessment_date` date DEFAULT NULL,
  `next_assessment_date` date DEFAULT NULL,
  `recommendations` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_risk` (`player_id`,`risk_level`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `injury_alerts`
--

LOCK TABLES `injury_alerts` WRITE;
/*!40000 ALTER TABLE `injury_alerts` DISABLE KEYS */;
INSERT INTO `injury_alerts` VALUES (1,4,'Fatigue musculaire','Quadriceps droit',15.50,'Risque faible de blessure musculaire due à la charge d\'entraînement','2025-08-28','2025-09-07','Surveiller la récupération, ajuster l\'intensité si nécessaire','2025-08-31 12:26:58','2025-08-31 12:26:58');
/*!40000 ALTER TABLE `injury_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"61082416-520b-42fd-a699-2a08610e19d9\",\"displayName\":\"App\\\\Notifications\\\\AccountRequestRejected\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:44:\\\"Illuminate\\\\Notifications\\\\AnonymousNotifiable\\\":1:{s:6:\\\"routes\\\";a:1:{s:4:\\\"mail\\\";s:16:\\\"test@example.com\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\AccountRequestRejected\\\":4:{s:14:\\\"accountRequest\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:25:\\\"App\\\\Models\\\\AccountRequest\\\";s:2:\\\"id\\\";i:36;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"rejectedBy\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:6:\\\"reason\\\";s:3:\\\"feF\\\";s:2:\\\"id\\\";s:36:\\\"d18b8c08-b12a-4bf1-88c5-563f1af2ff2c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1756718067,1756718067),(2,'default','{\"uuid\":\"607900d2-6e46-4f2d-a69d-1a444f123a9b\",\"displayName\":\"App\\\\Notifications\\\\AccountRequestRejected\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:44:\\\"Illuminate\\\\Notifications\\\\AnonymousNotifiable\\\":1:{s:6:\\\"routes\\\";a:1:{s:4:\\\"mail\\\";s:16:\\\"test@example.com\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\AccountRequestRejected\\\":4:{s:14:\\\"accountRequest\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:25:\\\"App\\\\Models\\\\AccountRequest\\\";s:2:\\\"id\\\";i:36;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"rejectedBy\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:6:\\\"reason\\\";s:3:\\\"feF\\\";s:2:\\\"id\\\";s:36:\\\"d18b8c08-b12a-4bf1-88c5-563f1af2ff2c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1756718067,1756718067),(3,'default','{\"uuid\":\"2999bbb5-5d56-4cef-8ede-31a3faec2f62\",\"displayName\":\"App\\\\Notifications\\\\AccountRequestApproved\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:44:\\\"Illuminate\\\\Notifications\\\\AnonymousNotifiable\\\":1:{s:6:\\\"routes\\\";a:1:{s:4:\\\"mail\\\";s:16:\\\"im0668@gmail.com\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\AccountRequestApproved\\\":3:{s:14:\\\"accountRequest\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:25:\\\"App\\\\Models\\\\AccountRequest\\\";s:2:\\\"id\\\";i:37;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"approvedBy\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"e4de5253-bffb-40c5-a26f-09f5267bfa42\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1756735083,1756735083),(4,'default','{\"uuid\":\"9274983c-c285-4ddb-9f11-61bb7a277617\",\"displayName\":\"App\\\\Notifications\\\\AccountRequestApproved\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:44:\\\"Illuminate\\\\Notifications\\\\AnonymousNotifiable\\\":1:{s:6:\\\"routes\\\";a:1:{s:4:\\\"mail\\\";s:16:\\\"im0668@gmail.com\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:12:\\\"notification\\\";O:40:\\\"App\\\\Notifications\\\\AccountRequestApproved\\\":3:{s:14:\\\"accountRequest\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:25:\\\"App\\\\Models\\\\AccountRequest\\\";s:2:\\\"id\\\";i:37;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"approvedBy\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"e4de5253-bffb-40c5-a26f-09f5267bfa42\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1756735083,1756735083);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `license_photos`
--

DROP TABLE IF EXISTS `license_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `license_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `club_id` bigint unsigned NOT NULL,
  `photo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive','deleted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `license_photos_uploaded_by_foreign` (`uploaded_by`),
  KEY `license_photos_player_id_club_id_index` (`player_id`,`club_id`),
  KEY `license_photos_club_id_status_index` (`club_id`,`status`),
  KEY `license_photos_uploaded_at_index` (`uploaded_at`),
  CONSTRAINT `license_photos_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `license_photos_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE,
  CONSTRAINT `license_photos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license_photos`
--

LOCK TABLES `license_photos` WRITE;
/*!40000 ALTER TABLE `license_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `license_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `license_requests`
--

DROP TABLE IF EXISTS `license_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `license_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fifa_connect_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID unique FIFA Connect',
  `fifa_license_request_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numéro de demande FIFA',
  `fifa_license_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numéro de licence FIFA (après approbation)',
  `request_type` enum('new_license','renewal','upgrade','transfer','replacement') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new_license',
  `license_type` enum('player','staff','medical','coach','referee','administrative') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'player',
  `license_category` enum('amateur','semi_pro','professional','international') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amateur',
  `license_level` enum('basic','intermediate','advanced','expert') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic',
  `request_status` enum('draft','submitted','under_review','additional_info_required','approved','rejected','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `request_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Raison de la demande de licence',
  `validity_period` enum('1_year','2_years','3_years','5_years') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1_year',
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nom complet officiel',
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `second_nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_certificate_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` int NOT NULL COMMENT 'Taille en cm',
  `weight` int NOT NULL COMMENT 'Poids en kg',
  `eye_color` enum('brown','blue','green','gray','hazel','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hair_color` enum('black','brown','blonde','red','gray','white','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distinguishing_marks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Marques distinctives',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relationship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_club_id` bigint unsigned DEFAULT NULL,
  `current_club_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_club_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_club_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `club_contract_start` date DEFAULT NULL,
  `club_contract_end` date DEFAULT NULL,
  `club_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rôle dans le club',
  `national_association_id` bigint unsigned DEFAULT NULL,
  `national_association_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_association_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_association_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confederation_id` bigint unsigned DEFAULT NULL,
  `confederation_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confederation_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_license_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numéro de licence locale du pays',
  `previous_licenses` json DEFAULT NULL COMMENT 'Historique des licences précédentes',
  `previous_license_numbers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numéros des licences précédentes',
  `last_license_issue_date` date DEFAULT NULL COMMENT 'Date de la dernière licence',
  `last_license_expiry_date` date DEFAULT NULL COMMENT 'Date d''expiration de la dernière licence',
  `last_license_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Statut de la dernière licence',
  `last_license_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Catégorie de la dernière licence',
  `last_license_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Niveau de la dernière licence',
  `last_license_issuer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Émetteur de la dernière licence',
  `last_license_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pays de la dernière licence',
  `disciplinary_history` json DEFAULT NULL COMMENT 'Historique disciplinaire complet',
  `has_active_suspension` tinyint(1) NOT NULL DEFAULT '0',
  `suspension_start_date` date DEFAULT NULL,
  `suspension_end_date` date DEFAULT NULL,
  `suspension_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `suspension_issuer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Qui a prononcé la suspension',
  `suspension_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type de suspension',
  `transfer_history` json DEFAULT NULL COMMENT 'Historique des transferts entre clubs',
  `previous_clubs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Clubs précédents',
  `last_transfer_date` date DEFAULT NULL COMMENT 'Date du dernier transfert',
  `medical_certificate_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_certificate_issue_date` date DEFAULT NULL,
  `medical_certificate_expiry_date` date DEFAULT NULL,
  `medical_certificate_issuer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_restrictions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Restrictions médicales',
  `medical_conditions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Conditions médicales',
  `medical_clearance_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Statut de l''aptitude médicale',
  `medical_tests` json DEFAULT NULL COMMENT 'Tests médicaux effectués avec résultats',
  `last_medical_checkup` date DEFAULT NULL,
  `medical_doctor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom du médecin',
  `medical_clinic_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom de la clinique',
  `medical_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Notes médicales additionnelles',
  `medical_history` json DEFAULT NULL COMMENT 'Antécédents médicaux du joueur',
  `has_chronic_conditions` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'A des conditions chroniques',
  `chronic_conditions_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Détails des conditions chroniques',
  `takes_medication` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Prend des médicaments',
  `medication_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Détails des médicaments',
  `passport_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Copie du passeport',
  `national_id_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Copie de la carte d''identité',
  `birth_certificate_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Copie de l''acte de naissance',
  `photo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Photo d''identité',
  `previous_license_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Copie de la licence précédente',
  `club_contract_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Copie du contrat de club',
  `medical_certificate_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Copie du certificat médical',
  `additional_documents` json DEFAULT NULL COMMENT 'Documents additionnels requis',
  `document_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Notes sur les documents',
  `requested_by` bigint unsigned DEFAULT NULL COMMENT 'Utilisateur qui fait la demande',
  `requested_at` timestamp NULL DEFAULT NULL COMMENT 'Date de la demande',
  `request_notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes du demandeur',
  `club_approved_by` bigint unsigned DEFAULT NULL COMMENT 'Approbation par le club',
  `club_approved_at` timestamp NULL DEFAULT NULL,
  `club_approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `association_reviewed_by` bigint unsigned DEFAULT NULL COMMENT 'Révision par l''association',
  `association_reviewed_at` timestamp NULL DEFAULT NULL,
  `association_review_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `association_decision` enum('pending','approved','rejected','additional_info_required') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fifa_approved_by` bigint unsigned DEFAULT NULL COMMENT 'Approbation finale FIFA',
  `fifa_approved_at` timestamp NULL DEFAULT NULL,
  `fifa_approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fifa_decision` enum('pending','approved','rejected','additional_info_required') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Raison du rejet',
  `rejected_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Qui a rejeté',
  `rejected_at` timestamp NULL DEFAULT NULL,
  `correction_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Instructions de correction',
  `license_issue_date` date DEFAULT NULL COMMENT 'Date d''émission de la licence',
  `license_expiry_date` date DEFAULT NULL COMMENT 'Date d''expiration de la licence',
  `license_renewal_date` date DEFAULT NULL COMMENT 'Date de renouvellement',
  `license_status` enum('active','suspended','expired','cancelled','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `license_restrictions` json DEFAULT NULL COMMENT 'Restrictions sur la licence',
  `license_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Notes sur la licence',
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `license_requests_current_club_id_foreign` (`current_club_id`),
  KEY `license_requests_national_association_id_foreign` (`national_association_id`),
  CONSTRAINT `license_requests_current_club_id_foreign` FOREIGN KEY (`current_club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `license_requests_national_association_id_foreign` FOREIGN KEY (`national_association_id`) REFERENCES `associations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license_requests`
--

LOCK TABLES `license_requests` WRITE;
/*!40000 ALTER TABLE `license_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `license_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lineup_players`
--

DROP TABLE IF EXISTS `lineup_players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lineup_players` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lineup_id` bigint unsigned NOT NULL,
  `player_id` bigint unsigned NOT NULL,
  `is_substitute` tinyint(1) NOT NULL DEFAULT '0',
  `position_order` int NOT NULL DEFAULT '0',
  `assigned_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tactical_instructions` text COLLATE utf8mb4_unicode_ci,
  `fitness_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fit',
  `expected_performance` decimal(3,1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lineup_players_lineup_id_player_id_unique` (`lineup_id`,`player_id`),
  KEY `lineup_players_player_id_foreign` (`player_id`),
  KEY `lineup_players_lineup_id_is_substitute_index` (`lineup_id`,`is_substitute`),
  KEY `lineup_players_lineup_id_position_order_index` (`lineup_id`,`position_order`),
  CONSTRAINT `lineup_players_lineup_id_foreign` FOREIGN KEY (`lineup_id`) REFERENCES `lineups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lineup_players_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lineup_players`
--

LOCK TABLES `lineup_players` WRITE;
/*!40000 ALTER TABLE `lineup_players` DISABLE KEYS */;
/*!40000 ALTER TABLE `lineup_players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lineups`
--

DROP TABLE IF EXISTS `lineups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lineups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned NOT NULL,
  `club_id` bigint unsigned NOT NULL,
  `competition_id` bigint unsigned DEFAULT NULL,
  `match_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `formation` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tactical_style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playing_philosophy` text COLLATE utf8mb4_unicode_ci,
  `captain_id` bigint unsigned DEFAULT NULL,
  `vice_captain_id` bigint unsigned DEFAULT NULL,
  `penalty_taker_id` bigint unsigned DEFAULT NULL,
  `free_kick_taker_id` bigint unsigned DEFAULT NULL,
  `corner_taker_id` bigint unsigned DEFAULT NULL,
  `match_type` enum('league','cup','friendly','international') COLLATE utf8mb4_unicode_ci NOT NULL,
  `opponent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue` enum('home','away','neutral') COLLATE utf8mb4_unicode_ci NOT NULL,
  `weather_conditions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pitch_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tactical_notes` text COLLATE utf8mb4_unicode_ci,
  `substitutions_plan` text COLLATE utf8mb4_unicode_ci,
  `set_pieces_strategy` text COLLATE utf8mb4_unicode_ci,
  `pressing_intensity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `possession_style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter_attack_style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `defensive_line_height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marking_system` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('planned','confirmed','used','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lineups_competition_id_foreign` (`competition_id`),
  KEY `lineups_captain_id_foreign` (`captain_id`),
  KEY `lineups_vice_captain_id_foreign` (`vice_captain_id`),
  KEY `lineups_penalty_taker_id_foreign` (`penalty_taker_id`),
  KEY `lineups_free_kick_taker_id_foreign` (`free_kick_taker_id`),
  KEY `lineups_corner_taker_id_foreign` (`corner_taker_id`),
  KEY `lineups_created_by_foreign` (`created_by`),
  KEY `lineups_approved_by_foreign` (`approved_by`),
  KEY `lineups_club_id_status_index` (`club_id`,`status`),
  KEY `lineups_team_id_status_index` (`team_id`,`status`),
  KEY `lineups_match_id_index` (`match_id`),
  CONSTRAINT `lineups_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_captain_id_foreign` FOREIGN KEY (`captain_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lineups_competition_id_foreign` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_corner_taker_id_foreign` FOREIGN KEY (`corner_taker_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_free_kick_taker_id_foreign` FOREIGN KEY (`free_kick_taker_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_match_id_foreign` FOREIGN KEY (`match_id`) REFERENCES `game_matches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_penalty_taker_id_foreign` FOREIGN KEY (`penalty_taker_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lineups_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lineups_vice_captain_id_foreign` FOREIGN KEY (`vice_captain_id`) REFERENCES `players` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lineups`
--

LOCK TABLES `lineups` WRITE;
/*!40000 ALTER TABLE `lineups` DISABLE KEYS */;
/*!40000 ALTER TABLE `lineups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_metrics`
--

DROP TABLE IF EXISTS `match_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `match_metrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `match_performance_id` bigint unsigned NOT NULL,
  `metric_name` varchar(255) NOT NULL,
  `metric_value` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_metrics`
--

LOCK TABLES `match_metrics` WRITE;
/*!40000 ALTER TABLE `match_metrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `match_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_officials`
--

DROP TABLE IF EXISTS `match_officials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `match_officials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `match_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` enum('main_referee','assistant_referee_1','assistant_referee_2','fourth_official') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_officials_match_id_foreign` (`match_id`),
  KEY `match_officials_user_id_foreign` (`user_id`),
  CONSTRAINT `match_officials_match_id_foreign` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `match_officials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_officials`
--

LOCK TABLES `match_officials` WRITE;
/*!40000 ALTER TABLE `match_officials` DISABLE KEYS */;
INSERT INTO `match_officials` VALUES (11,2,6,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(12,2,8,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(13,3,7,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(14,3,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(15,4,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(16,4,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(17,5,9,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(18,5,7,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(19,5,6,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(20,6,5,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(21,6,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(22,7,9,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(23,7,7,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(24,7,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(25,8,7,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(26,8,9,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(27,9,7,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(28,9,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(29,10,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(30,11,6,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(31,11,9,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(32,12,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(33,13,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(34,13,7,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(35,13,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(36,13,5,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(37,14,7,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(38,14,6,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(39,14,7,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(40,15,5,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(41,15,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(42,15,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(43,16,6,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(44,16,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(47,18,7,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(48,18,7,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(49,18,8,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(50,19,6,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(51,19,7,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(52,19,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(53,20,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(54,20,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(55,21,6,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(56,21,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(57,22,6,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(58,22,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(59,22,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(60,22,5,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(61,23,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(62,23,9,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(63,23,7,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(64,24,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(65,24,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(66,24,8,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(67,25,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(68,25,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(69,25,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(70,26,5,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(71,26,6,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(72,26,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(73,26,8,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(78,28,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(79,28,5,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(80,29,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(81,29,6,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(82,30,6,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(83,30,7,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(84,31,5,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(85,32,5,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(86,32,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(87,33,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(88,33,5,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(89,34,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(90,34,6,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(91,34,9,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(92,35,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(93,35,7,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(94,36,9,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(95,37,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(96,37,6,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(97,38,8,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(98,38,5,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(99,38,7,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(100,39,9,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(103,41,6,'main_referee','2025-09-03 17:52:47','2025-09-03 17:52:47'),(104,41,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(105,42,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(106,42,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(107,43,7,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(108,43,9,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(110,45,9,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(111,45,9,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(112,45,9,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(113,46,9,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(114,46,7,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(115,47,6,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(116,47,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(117,47,8,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(118,48,5,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(119,48,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(120,49,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(121,49,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(122,50,8,'assistant_referee_1','2025-09-03 17:52:47','2025-09-03 17:52:47'),(123,50,8,'assistant_referee_2','2025-09-03 17:52:47','2025-09-03 17:52:47'),(124,50,6,'fourth_official','2025-09-03 17:52:47','2025-09-03 17:52:47'),(128,54,5,'main_referee','2025-09-04 01:31:26','2025-09-04 01:31:26'),(129,54,9,'assistant_referee_1','2025-09-04 01:31:26','2025-09-04 01:31:26'),(130,54,8,'assistant_referee_2','2025-09-04 01:31:26','2025-09-04 01:31:26'),(131,54,6,'fourth_official','2025-09-04 01:31:26','2025-09-04 01:31:26');
/*!40000 ALTER TABLE `match_officials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_performances`
--

DROP TABLE IF EXISTS `match_performances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `match_performances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `match_id` bigint unsigned NOT NULL,
  `goals_scored` int DEFAULT '0',
  `assists` int DEFAULT '0',
  `yellow_cards` int DEFAULT '0',
  `red_cards` int DEFAULT '0',
  `minutes_played` int DEFAULT '0',
  `match_rating` decimal(3,1) DEFAULT '0.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_performances`
--

LOCK TABLES `match_performances` WRITE;
/*!40000 ALTER TABLE `match_performances` DISABLE KEYS */;
/*!40000 ALTER TABLE `match_performances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `competition_id` bigint unsigned NOT NULL,
  `home_team_id` bigint unsigned NOT NULL,
  `away_team_id` bigint unsigned NOT NULL,
  `match_date` date NOT NULL,
  `match_time` time NOT NULL,
  `venue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_score` int DEFAULT NULL,
  `away_score` int DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `referee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistant_referee_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistant_referee_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `var_referee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_official` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `round` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `matches_competition_id_foreign` (`competition_id`),
  KEY `matches_home_team_id_foreign` (`home_team_id`),
  KEY `matches_away_team_id_foreign` (`away_team_id`),
  KEY `matches_tenant_id_index` (`tenant_id`),
  CONSTRAINT `matches_away_team_id_foreign` FOREIGN KEY (`away_team_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matches_competition_id_foreign` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matches_home_team_id_foreign` FOREIGN KEY (`home_team_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matches_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES (2,NULL,4,18,21,'2024-08-17','15:00:00','US Tataouine Stadium',1,1,'completed','Mehdi Abid Charef','Sadok Selmi','Youssef Srairi','Kamel Harrouche','Délégué 1','Observateur 3',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(3,NULL,4,8,19,'2024-08-19','15:00:00','US Monastir Stadium',2,4,'completed','Bechir Hassani','Sadok Selmi','Mohamed Jebali','Nabil Jebali','Délégué 9','Observateur 4',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(4,NULL,4,10,7,'2024-08-21','15:00:00','JS Kairouan Stadium',4,4,'completed','Kamel Harrouche','Haythem Guirat','Slim Belkhouja','Sadok Selmi','Délégué 2','Observateur 4',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(5,NULL,4,12,9,'2024-08-16','15:00:00','Stade Tunisien Stadium',1,1,'completed','Haythem Guirat','Bechir Hassani','Mehdi Abid Charef','Youssef Srairi','Délégué 5','Observateur 3',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(6,NULL,4,16,6,'2024-08-18','15:00:00','CS Hammam-Lif Stadium',2,1,'completed','Mohamed Jebali','Ridha Mejri','Kamel Harrouche','Slim Belkhouja','Délégué 3','Observateur 2',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(7,NULL,4,11,17,'2024-08-20','15:00:00','AS Gabès Stadium',2,1,'completed','Haythem Guirat','Bechir Hassani','Mohamed Jebali','Ridha Mejri','Délégué 5','Observateur 4',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(8,NULL,4,13,15,'2024-08-15','15:00:00','US Ben Guerdane Stadium',3,3,'completed','Slim Belkhouja','Nabil Jebali','Bechir Hassani','Haythem Guirat','Délégué 7','Observateur 4',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(9,NULL,4,23,22,'2024-08-17','15:00:00','JS Tabarka Stadium',3,0,'completed','Sadok Selmi','Ridha Mejri','Bechir Hassani','Mehdi Abid Charef','Délégué 9','Observateur 1',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(10,NULL,4,2,20,'2024-08-19','15:00:00','Espérance Sportive de Tunis Stadium',3,4,'completed','Slim Belkhouja','Ridha Mejri','Youssef Srairi','Mehdi Abid Charef','Délégué 9','Observateur 2',1,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(11,NULL,4,15,16,'2024-08-22','15:00:00','AS Soliman Stadium',0,2,'completed','Sadok Selmi','Slim Belkhouja','Mehdi Abid Charef','Haythem Guirat','Délégué 4','Observateur 2',2,'2025-09-03 14:19:56','2025-09-05 13:14:27'),(12,NULL,4,10,21,'2024-08-24','15:00:00','JS Kairouan Stadium',1,4,'completed','Nabil Jebali','Haythem Guirat','Youssef Srairi','Youssef Srairi','Délégué 2','Observateur 4',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(13,NULL,4,20,22,'2024-08-26','15:00:00','ES Métlaoui Stadium',4,4,'completed','Kamel Harrouche','Bechir Hassani','Mohamed Jebali','Mohamed Jebali','Délégué 9','Observateur 5',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(14,NULL,4,12,23,'2024-08-28','15:00:00','Stade Tunisien Stadium',2,2,'completed','Ridha Mejri','Bechir Hassani','Mehdi Abid Charef','Bechir Hassani','Délégué 1','Observateur 3',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(15,NULL,4,2,13,'2024-08-23','15:00:00','Espérance Sportive de Tunis Stadium',3,2,'completed','Mohamed Jebali','Haythem Guirat','Mohamed Jebali','Youssef Srairi','Délégué 1','Observateur 2',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(16,NULL,4,9,17,'2024-08-25','15:00:00','CA Bizertin Stadium',4,3,'completed','Mehdi Abid Charef','Nabil Jebali','Kamel Harrouche','Ridha Mejri','Délégué 4','Observateur 5',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(18,NULL,4,7,19,'2024-08-22','15:00:00','CS Sfaxien Stadium',4,0,'completed','Bechir Hassani','Ridha Mejri','Bechir Hassani','Kamel Harrouche','Délégué 7','Observateur 4',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(19,NULL,4,8,11,'2024-08-24','15:00:00','US Monastir Stadium',1,2,'completed','Mehdi Abid Charef','Bechir Hassani','Sadok Selmi','Mehdi Abid Charef','Délégué 9','Observateur 5',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(20,NULL,4,18,6,'2024-08-26','15:00:00','US Tataouine Stadium',0,1,'completed','Ridha Mejri','Sadok Selmi','Kamel Harrouche','Mehdi Abid Charef','Délégué 4','Observateur 3',2,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(21,NULL,4,6,11,'2024-08-29','15:00:00','Étoile du Sahel Stadium',0,4,'completed','Mehdi Abid Charef','Haythem Guirat','Slim Belkhouja','Sadok Selmi','Délégué 6','Observateur 1',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(22,NULL,4,14,22,'2024-08-31','15:00:00','Olympique de Béja Stadium',0,3,'completed','Mehdi Abid Charef','Kamel Harrouche','Mohamed Jebali','Mohamed Jebali','Délégué 10','Observateur 4',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(23,NULL,4,10,23,'2024-09-02','15:00:00','JS Kairouan Stadium',0,1,'completed','Slim Belkhouja','Kamel Harrouche','Haythem Guirat','Bechir Hassani','Délégué 5','Observateur 3',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(24,NULL,4,15,20,'2024-09-04','15:00:00','AS Soliman Stadium',2,3,'completed','Kamel Harrouche','Haythem Guirat','Slim Belkhouja','Kamel Harrouche','Délégué 2','Observateur 1',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(25,NULL,4,12,7,'2024-08-30','15:00:00','Stade Tunisien Stadium',0,3,'completed','Kamel Harrouche','Haythem Guirat','Nabil Jebali','Mehdi Abid Charef','Délégué 4','Observateur 4',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(26,NULL,4,13,21,'2024-09-01','15:00:00','US Ben Guerdane Stadium',0,1,'completed','Mohamed Jebali','Mehdi Abid Charef','Kamel Harrouche','Kamel Harrouche','Délégué 3','Observateur 4',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(28,NULL,4,19,8,'2024-08-29','15:00:00','AS Marsa Stadium',0,1,'completed','Kamel Harrouche','Ridha Mejri','Ridha Mejri','Mohamed Jebali','Délégué 10','Observateur 2',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(29,NULL,4,9,16,'2024-08-31','15:00:00','CA Bizertin Stadium',1,0,'completed','Youssef Srairi','Kamel Harrouche','Mehdi Abid Charef','Nabil Jebali','Délégué 5','Observateur 1',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(30,NULL,4,18,2,'2024-09-02','15:00:00','US Tataouine Stadium',2,4,'completed','Ridha Mejri','Mehdi Abid Charef','Ridha Mejri','Bechir Hassani','Délégué 5','Observateur 4',3,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(31,NULL,4,21,12,'2024-09-05','15:00:00','US Siliana Stadium',1,0,'completed','Ridha Mejri','Mohamed Jebali','Ridha Mejri','Slim Belkhouja','Délégué 2','Observateur 1',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(32,NULL,4,7,11,'2024-09-07','15:00:00','CS Sfaxien Stadium',1,1,'completed','Mohamed Jebali','Haythem Guirat','Sadok Selmi','Nabil Jebali','Délégué 3','Observateur 2',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(33,NULL,4,19,9,'2024-09-09','15:00:00','AS Marsa Stadium',3,0,'completed','Ridha Mejri','Slim Belkhouja','Kamel Harrouche','Mohamed Jebali','Délégué 7','Observateur 2',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(34,NULL,4,14,2,'2024-09-11','15:00:00','Olympique de Béja Stadium',0,2,'completed','Kamel Harrouche','Sadok Selmi','Mehdi Abid Charef','Haythem Guirat','Délégué 3','Observateur 2',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(35,NULL,4,15,8,'2024-09-06','15:00:00','AS Soliman Stadium',1,4,'completed','Kamel Harrouche','Youssef Srairi','Slim Belkhouja','Bechir Hassani','Délégué 4','Observateur 1',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(36,NULL,4,13,20,'2024-09-08','15:00:00','US Ben Guerdane Stadium',3,1,'completed','Ridha Mejri','Nabil Jebali','Haythem Guirat','Slim Belkhouja','Délégué 8','Observateur 4',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(37,NULL,4,16,22,'2024-09-10','15:00:00','CS Hammam-Lif Stadium',2,0,'completed','Sadok Selmi','Kamel Harrouche','Mehdi Abid Charef','Sadok Selmi','Délégué 8','Observateur 2',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(38,NULL,4,23,17,'2024-09-05','15:00:00','JS Tabarka Stadium',2,4,'completed','Kamel Harrouche','Mohamed Jebali','Bechir Hassani','Ridha Mejri','Délégué 1','Observateur 2',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(39,NULL,4,6,10,'2024-09-07','15:00:00','Étoile du Sahel Stadium',3,2,'completed','Ridha Mejri','Slim Belkhouja','Sadok Selmi','Haythem Guirat','Délégué 7','Observateur 5',4,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(41,NULL,4,12,18,'2024-09-12','15:00:00','Stade Tunisien Stadium',2,3,'completed','Mehdi Abid Charef','Kamel Harrouche','Youssef Srairi','Nabil Jebali','Délégué 5','Observateur 3',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(42,NULL,4,9,10,'2024-09-14','15:00:00','CA Bizertin Stadium',4,3,'completed','Ridha Mejri','Kamel Harrouche','Nabil Jebali','Mehdi Abid Charef','Délégué 2','Observateur 3',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(43,NULL,4,2,22,'2024-09-16','15:00:00','Espérance Sportive de Tunis Stadium',2,2,'completed','Nabil Jebali','Bechir Hassani','Youssef Srairi','Haythem Guirat','Délégué 3','Observateur 5',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(45,NULL,4,6,13,'2024-09-13','15:00:00','Étoile du Sahel Stadium',0,4,'completed','Sadok Selmi','Haythem Guirat','Haythem Guirat','Haythem Guirat','Délégué 9','Observateur 3',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(46,NULL,4,23,16,'2024-09-15','15:00:00','JS Tabarka Stadium',0,4,'completed','Youssef Srairi','Ridha Mejri','Haythem Guirat','Bechir Hassani','Délégué 5','Observateur 2',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(47,NULL,4,7,21,'2024-09-17','15:00:00','CS Sfaxien Stadium',2,3,'completed','Nabil Jebali','Mehdi Abid Charef','Mohamed Jebali','Kamel Harrouche','Délégué 6','Observateur 4',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(48,NULL,4,20,19,'2024-09-12','15:00:00','ES Métlaoui Stadium',3,0,'completed','Slim Belkhouja','Youssef Srairi','Mohamed Jebali','Mehdi Abid Charef','Délégué 6','Observateur 4',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(49,NULL,4,11,14,'2024-09-14','15:00:00','AS Gabès Stadium',3,0,'completed','Youssef Srairi','Kamel Harrouche','Kamel Harrouche','Ridha Mejri','Délégué 1','Observateur 4',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(50,NULL,4,8,17,'2024-09-16','15:00:00','US Monastir Stadium',4,3,'completed','Sadok Selmi','Kamel Harrouche','Kamel Harrouche','Mehdi Abid Charef','Délégué 8','Observateur 2',5,'2025-09-03 14:19:56','2025-09-03 14:19:56'),(54,NULL,1,3,2,'2025-09-15','20:00:00','Stade Olympique de RadÃ¨s',NULL,NULL,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-09-04 00:08:27','2025-09-04 00:08:27');
/*!40000 ALTER TABLE `matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medical_notes`
--

DROP TABLE IF EXISTS `medical_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `note_json` json NOT NULL,
  `generated_by_ai` tinyint(1) NOT NULL DEFAULT '0',
  `approved_by_physician_id` bigint unsigned DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `note_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `ai_metadata` json DEFAULT NULL,
  `fifa_compliance_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_notes_athlete_id_foreign` (`athlete_id`),
  CONSTRAINT `medical_notes_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medical_notes`
--

LOCK TABLES `medical_notes` WRITE;
/*!40000 ALTER TABLE `medical_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `medical_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medical_predictions`
--

DROP TABLE IF EXISTS `medical_predictions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_predictions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `health_record_id` bigint unsigned NOT NULL,
  `player_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `prediction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `predicted_condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risk_probability` decimal(5,4) NOT NULL,
  `confidence_score` decimal(5,4) NOT NULL,
  `prediction_factors` json DEFAULT NULL,
  `recommendations` json DEFAULT NULL,
  `prediction_date` timestamp NOT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `status` enum('active','expired','verified','false_positive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `ai_model_version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `prediction_notes` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_predictions_user_id_foreign` (`user_id`),
  KEY `medical_predictions_player_id_prediction_type_index` (`player_id`,`prediction_type`),
  KEY `medical_predictions_health_record_id_status_index` (`health_record_id`,`status`),
  KEY `medical_predictions_prediction_date_status_index` (`prediction_date`,`status`),
  CONSTRAINT `medical_predictions_health_record_id_foreign` FOREIGN KEY (`health_record_id`) REFERENCES `health_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medical_predictions_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medical_predictions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medical_predictions`
--

LOCK TABLES `medical_predictions` WRITE;
/*!40000 ALTER TABLE `medical_predictions` DISABLE KEYS */;
/*!40000 ALTER TABLE `medical_predictions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mental_health_app`
--

DROP TABLE IF EXISTS `mental_health_app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mental_health_app` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_type` enum('meditation','mood_tracking','stress_management','therapy','wellness') NOT NULL,
  `api_endpoint` varchar(500) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `last_session_date` date DEFAULT NULL,
  `session_duration` int DEFAULT NULL,
  `mood_score` decimal(5,2) DEFAULT NULL,
  `stress_level` decimal(5,2) DEFAULT NULL,
  `anxiety_score` decimal(5,2) DEFAULT NULL,
  `depression_score` decimal(5,2) DEFAULT NULL,
  `wellness_score` decimal(5,2) DEFAULT NULL,
  `session_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_app` (`player_id`,`app_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mental_health_app`
--

LOCK TABLES `mental_health_app` WRITE;
/*!40000 ALTER TABLE `mental_health_app` DISABLE KEYS */;
INSERT INTO `mental_health_app` VALUES (1,4,'Calm - Méditation & Sommeil','meditation','https://api.calm.com/v1/user-wellness','calm_api_key_123','2025-08-31',20,8.50,22.00,18.00,15.00,8.80,'Session de méditation guidée, respiration profonde','2025-08-31 13:03:49','2025-08-31 13:03:49'),(2,4,'Headspace - Gestion du Stress','stress_management','https://api.headspace.com/v2/mental-health','headspace_api_key_456','2025-08-29',15,8.20,25.00,20.00,16.00,8.50,'Exercices de respiration, techniques de relaxation','2025-08-31 13:03:49','2025-08-31 13:03:49');
/*!40000 ALTER TABLE `mental_health_app` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2024_01_15_000001_create_teams_table',1),(4,'2024_01_15_000002_create_athletes_table',1),(5,'2024_01_01_000000_create_license_photos_table',2),(6,'2024_01_01_000000_create_player_licenses_table',2),(7,'2025_01_01_000001_create_confederations_table',3),(9,'2025_07_10_211843_create_health_records_table',4),(10,'2025_08_01_155815_add_hl7_report_status_to_health_records_table',5),(11,'2025_08_01_190000_enhance_health_records_with_comprehensive_medical_categories',6),(12,'2025_08_01_215021_add_enhanced_aut_fields_to_health_records_table',7),(13,'2025_08_02_140914_add_emr_fields_to_health_records_table',8),(14,'2025_07_14_200400_create_pcmas_table',9),(15,'2025_08_01_000001_update_pcmas_table_fifa_compliant',10),(16,'2025_08_01_123259_add_medical_imaging_fields_to_pcmas_table',11),(17,'2025_07_10_211640_create_fifa_connect_ids_table',12),(18,'2025_08_05_211217_add_signature_fields_to_pcmas_table',13),(19,'2025_07_14_200318_create_competitions_table',14),(20,'2025_08_30_144316_add_fifa_fields_to_associations_table',15),(21,'2025_08_30_150645_add_short_name_to_clubs_table',16),(22,'2025_08_30_151417_add_club_id_to_teams_table',17),(23,'2025_08_30_161439_add_fifa_fields_to_confederations_table',18),(24,'2025_08_30_165442_add_association_id_to_competitions_table',19),(25,'2025_08_30_165624_create_competition_club_table',20),(28,'2025_07_23_181042_create_account_requests_table',21),(30,'2025_09_03_141458_create_matches_table',22),(31,'2025_07_14_230707_create_match_officials_table',23),(32,'2025_09_03_210234_create_referee_reports_table',24),(33,'2025_09_05_142141_create_permissions_table',25),(34,'2025_09_05_143639_create_roles_table',26),(35,'2025_09_05_142243_create_role_permissions_table',27),(36,'2025_09_05_142325_create_user_permissions_table',28),(37,'2025_09_05_161603_create_audit_logs_table',29),(38,'2025_09_05_164708_create_system_settings_table',30),(39,'2024_01_15_000006_create_tue_requests_table',1),(40,'2024_01_15_000007_create_medical_notes_table',1),(41,'2024_01_15_000008_create_risk_alerts_table',1),(42,'2024_01_15_create_player_performances_table',1),(43,'2024_01_20_000001_add_role_to_users_table',31),(44,'2024_01_20_000002_create_appointments_table',2),(45,'2024_01_20_000003_create_uploaded_documents_table',3),(46,'2024_01_20_000004_create_health_records_table',3),(47,'2024_01_20_000005_create_player_health_wellbeing_table',3),(48,'2024_01_20_000006_create_player_injuries_diseases_table',3),(49,'2024_01_20_000007_create_player_licenses_table',3),(50,'2024_01_20_000008_create_player_medical_aptitude_table',3),(51,'2024_01_20_000009_create_player_medications_table',3),(52,'2024_01_20_000010_create_player_notifications_table',3),(53,'2024_01_20_000011_create_player_nutrition_table',3),(54,'2024_01_20_000012_create_player_pcma_table',3),(55,'2024_01_20_000013_create_player_recovery_table',3),(56,'2024_01_20_000014_create_player_season_stats_table',3),(57,'2024_01_20_000015_create_player_vital_signs_table',3),(58,'2024_01_20_000016_create_players_table',3),(59,'2024_01_20_000017_create_referee_reports_table',3),(60,'2024_01_20_000018_create_risk_alerts_table',3),(61,'2024_01_20_000019_create_role_permissions_table',3),(62,'2024_01_20_000020_create_roles_table',3),(63,'2024_01_20_000021_create_sdoh_factors_table',3),(64,'2024_01_20_000022_create_seasons_table',3),(65,'2024_01_20_000023_create_sports_devices_table',3),(66,'2024_01_20_000024_create_system_settings_table',3),(67,'2024_01_20_000025_create_teams_table',3),(68,'2024_01_20_000026_create_therapeutic_use_exemptions_table',3),(69,'2025_01_01_000003_create_complete_license_request_table',4),(70,'2025_01_15_000001_create_health_scores_table',32),(71,'2025_01_20_000001_create_tenants_table',32),(72,'2025_01_20_000002_add_tenant_id_to_existing_tables',32),(73,'2025_07_10_211658_create_clubs_table',5),(74,'2025_07_10_211722_create_associations_table',6),(75,'2025_07_10_221826_create_medical_predictions_table',33),(76,'2025_07_11_001300_create_game_matches_table',33),(77,'2025_07_11_001325_create_lineups_table',33),(78,'2025_07_11_001328_create_lineup_players_table',33),(79,'2025_07_11_001350_create_team_players_table',33),(80,'2025_01_01_000002_update_existing_tables',34);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('24ecfc3b-8035-4b87-82ec-6578ebc56a29','App\\Notifications\\LicenseApproved','App\\Models\\User',2,'{\"title\":\"Licence valid\\u00e9e\",\"message\":\"La licence LIC-2024-001 pour Ahmed Ben Salah a \\u00e9t\\u00e9 approuv\\u00e9e\",\"license_number\":\"LIC-2024-001\",\"player_name\":\"Ahmed Ben Salah\",\"approved_by\":\"System Admin\"}',NULL,'2025-09-07 00:10:08','2025-09-07 00:10:08'),('39f4bbf0-8d7d-4350-90ff-1d9007e943bb','App\\Notifications\\LicensePending','App\\Models\\User',2,'{\"title\":\"Licence en attente\",\"message\":\"La licence LIC-2024-002 pour Hamza Lahmar est en attente de validation\",\"license_number\":\"LIC-2024-002\",\"player_name\":\"Hamza Lahmar\",\"submitted_at\":\"2025-09-06 20:10:08\"}',NULL,'2025-09-06 20:10:08','2025-09-06 20:10:08'),('423f6c4c-64de-46c6-ae58-d45504a63de1','App\\Notifications\\MedicalAppointment','App\\Models\\Player',3,'{\"title\":\"Control M\\u00e9dico\",\"message\":\"Revisi\\u00f3n rutinaria con Dr. Mart\\u00ednez\",\"date\":\"09\\/09\\/2025\",\"priority\":\"medium\",\"type\":\"medical\",\"urgent\":false,\"icon\":\"fas fa-user-md\",\"appointment_type\":\"Consultation\",\"purpose\":\"Revisi\\u00f3n rutinaria\",\"time\":\"10:00\",\"location\":\"Centre m\\u00e9dical\",\"doctor\":\"Dr. Mart\\u00ednez\"}',NULL,'2025-09-06 20:11:37','2025-09-06 20:11:37'),('54e0d3d0-2113-4edf-b35d-007564bf154b','App\\Notifications\\NationalTeamCall','App\\Models\\Player',3,'{\"title\":\"Convocaci\\u00f3n Selecci\\u00f3n UK\",\"message\":\"Convocado para partidos vs Brasil y Uruguay\",\"date\":\"12\\/09\\/2025\",\"priority\":\"high\",\"type\":\"national\",\"urgent\":true,\"icon\":\"fas fa-flag\"}',NULL,'2025-09-07 00:11:37','2025-09-07 00:11:37'),('5b3b9725-0a9b-4356-b296-776f85855e1e','App\\Notifications\\MedicalRecordCreated','App\\Models\\User',2,'{\"title\":\"Nouveau dossier m\\u00e9dical\",\"message\":\"Un nouveau dossier m\\u00e9dical a \\u00e9t\\u00e9 cr\\u00e9\\u00e9 pour Athl\\u00e8te #1234\",\"player_id\":1234,\"player_name\":\"Youssef Msakni\",\"record_type\":\"Medical Checkup\"}',NULL,'2025-09-06 22:10:08','2025-09-06 22:10:08'),('9324faaf-1785-4059-86ae-0790b01a6118','App\\Notifications\\MatchCall','App\\Models\\Player',4,'{\"title\":\"Convocaci\\u00f3n para el pr\\u00f3ximo partido\",\"message\":\"Has sido convocado para el partido contra Real Madrid el pr\\u00f3ximo domingo\",\"date\":\"10\\/09\\/2025\",\"priority\":\"high\",\"type\":\"matches\",\"urgent\":true,\"icon\":\"fas fa-futbol\",\"match_opponent\":\"Real Madrid\",\"match_date\":\"10\\/09\\/2025\",\"match_time\":\"20:00\"}',NULL,'2025-09-07 02:18:50','2025-09-07 02:18:50'),('e60bc35e-9fcb-447c-ab2e-1d7aea9cd156','App\\Notifications\\TrainingSession','App\\Models\\Player',3,'{\"title\":\"Entrenamiento T\\u00e9cnico\",\"message\":\"Sesi\\u00f3n de pases y finalizaci\\u00f3n\",\"date\":\"08\\/09\\/2025\",\"priority\":\"medium\",\"type\":\"training\",\"urgent\":false,\"icon\":\"fas fa-dumbbell\"}',NULL,'2025-09-06 22:11:37','2025-09-06 22:11:37');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pcmas`
--

DROP TABLE IF EXISTS `pcmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pcmas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `type` enum('bpma','cardio','dental','neurological','orthopedic') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `result_json` json NOT NULL,
  `medical_history` json DEFAULT NULL,
  `physical_examination` json DEFAULT NULL,
  `cardiovascular_investigations` json DEFAULT NULL,
  `final_statement` json DEFAULT NULL,
  `scat_assessment` json DEFAULT NULL,
  `anatomical_annotations` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `form_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `last_updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','completed','failed','cleared','not_cleared') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fifa_compliant` tinyint(1) NOT NULL DEFAULT '0',
  `fifa_approved_at` timestamp NULL DEFAULT NULL,
  `fifa_approved_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `assessor_id` bigint unsigned NOT NULL,
  `assessment_date` date DEFAULT NULL,
  `fifa_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `competition_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `competition_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fifa_compliance_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ecg_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ecg_date` date DEFAULT NULL,
  `ecg_interpretation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ecg_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mri_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mri_date` date DEFAULT NULL,
  `mri_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mri_findings` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mri_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `xray_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ct_scan_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ultrasound_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_signed` tinyint(1) NOT NULL DEFAULT '0',
  `signed_at` timestamp NULL DEFAULT NULL,
  `signed_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `signature_data` json DEFAULT NULL,
  `player_id` bigint unsigned DEFAULT NULL,
  `fifa_connect_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pcmas_assessor_id_foreign` (`assessor_id`),
  KEY `pcmas_athlete_id_index` (`athlete_id`),
  KEY `pcmas_type_index` (`type`),
  KEY `pcmas_status_index` (`status`),
  KEY `pcmas_completed_at_index` (`completed_at`),
  KEY `pcmas_player_id_foreign` (`player_id`),
  CONSTRAINT `pcmas_assessor_id_foreign` FOREIGN KEY (`assessor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pcmas_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pcmas_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pcmas`
--

LOCK TABLES `pcmas` WRITE;
/*!40000 ALTER TABLE `pcmas` DISABLE KEYS */;
/*!40000 ALTER TABLE `pcmas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_predictions`
--

DROP TABLE IF EXISTS `performance_predictions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `performance_predictions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `prediction_type` enum('performance','health','wellbeing') NOT NULL,
  `current_score` decimal(5,2) DEFAULT NULL,
  `predicted_score_3months` decimal(5,2) DEFAULT NULL,
  `predicted_score_6months` decimal(5,2) DEFAULT NULL,
  `trend_direction` enum('ascending','stable','descending') DEFAULT NULL,
  `confidence_level` decimal(5,2) DEFAULT NULL,
  `prediction_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_type` (`player_id`,`prediction_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_predictions`
--

LOCK TABLES `performance_predictions` WRITE;
/*!40000 ALTER TABLE `performance_predictions` DISABLE KEYS */;
INSERT INTO `performance_predictions` VALUES (1,4,'performance',85.00,92.00,95.00,'ascending',0.85,'2025-08-31','2025-08-31 12:18:04','2025-08-31 12:18:04'),(2,4,'health',90.00,92.00,93.00,'stable',0.90,'2025-08-31','2025-08-31 12:18:04','2025-08-31 12:18:04'),(3,4,'wellbeing',78.00,85.00,88.00,'ascending',0.80,'2025-08-31','2025-08-31 12:18:04','2025-08-31 12:18:04');
/*!40000 ALTER TABLE `performance_predictions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_trends`
--

DROP TABLE IF EXISTS `performance_trends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `performance_trends` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `health_score` decimal(5,2) DEFAULT NULL,
  `wellbeing_score` decimal(5,2) DEFAULT NULL,
  `fitness_score` decimal(5,2) DEFAULT NULL,
  `energy_level` decimal(5,2) DEFAULT NULL,
  `sleep_quality` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_trends`
--

LOCK TABLES `performance_trends` WRITE;
/*!40000 ALTER TABLE `performance_trends` DISABLE KEYS */;
INSERT INTO `performance_trends` VALUES (1,4,'2025-08-31',76.00,87.00,71.00,83.00,84.00,7.40,'2025-08-31 12:16:49','2025-08-31 12:16:49'),(2,4,'2025-08-24',77.00,90.00,73.00,83.50,86.00,7.60,'2025-08-31 12:16:49','2025-08-31 12:16:49'),(3,4,'2025-08-17',79.00,87.00,75.00,85.00,83.00,7.80,'2025-08-31 12:16:49','2025-08-31 12:16:49'),(4,4,'2025-08-10',82.00,88.00,75.00,86.50,86.00,8.40,'2025-08-31 12:16:49','2025-08-31 12:16:49');
/*!40000 ALTER TABLE `performance_trends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Voir les joueurs','players.view','Consulter la liste des joueurs','players','view','players','2025-09-05 16:05:27','2025-09-05 16:05:27'),(2,'Créer des joueurs','players.create','Ajouter de nouveaux joueurs','players','create','players','2025-09-05 16:05:27','2025-09-05 16:05:27'),(3,'Modifier les joueurs','players.edit','Modifier les informations des joueurs','players','edit','players','2025-09-05 16:05:27','2025-09-05 16:05:27'),(4,'Supprimer les joueurs','players.delete','Supprimer des joueurs','players','delete','players','2025-09-05 16:05:27','2025-09-05 16:05:27'),(5,'Voir les clubs','clubs.view','Consulter la liste des clubs','clubs','view','clubs','2025-09-05 16:05:27','2025-09-05 16:05:27'),(6,'Créer des clubs','clubs.create','Ajouter de nouveaux clubs','clubs','create','clubs','2025-09-05 16:05:27','2025-09-05 16:05:27'),(7,'Modifier les clubs','clubs.edit','Modifier les informations des clubs','clubs','edit','clubs','2025-09-05 16:05:27','2025-09-05 16:05:27'),(8,'Supprimer les clubs','clubs.delete','Supprimer des clubs','clubs','delete','clubs','2025-09-05 16:05:27','2025-09-05 16:05:27'),(9,'Voir les compétitions','competitions.view','Consulter les compétitions','competitions','view','competitions','2025-09-05 16:05:27','2025-09-05 16:05:27'),(10,'Créer des compétitions','competitions.create','Créer de nouvelles compétitions','competitions','create','competitions','2025-09-05 16:05:27','2025-09-05 16:05:27'),(11,'Modifier les compétitions','competitions.edit','Modifier les compétitions','competitions','edit','competitions','2025-09-05 16:05:27','2025-09-05 16:05:27'),(12,'Gérer les matchs','matches.manage','Gérer les matchs et résultats','competitions','manage','matches','2025-09-05 16:05:27','2025-09-05 16:05:27'),(13,'Voir les arbitres','referees.view','Consulter la liste des arbitres','referees','view','referees','2025-09-05 16:05:27','2025-09-05 16:05:27'),(14,'Assigner des arbitres','referees.assign','Assigner des arbitres aux matchs','referees','assign','referees','2025-09-05 16:05:27','2025-09-05 16:05:27'),(15,'Gérer les arbitres','referees.manage','Gérer les informations des arbitres','referees','manage','referees','2025-09-05 16:05:27','2025-09-05 16:05:27'),(16,'Administration système','system.admin','Accès à l\'administration système','system','admin','system','2025-09-05 16:05:27','2025-09-05 16:05:27'),(17,'Gestion des utilisateurs','users.manage','Gérer les utilisateurs du système','system','manage','users','2025-09-05 16:05:27','2025-09-05 16:05:27'),(18,'Gestion des rôles','roles.manage','Gérer les rôles et permissions','system','manage','roles','2025-09-05 16:05:27','2025-09-05 16:05:27'),(19,'Statistiques système','system.stats','Consulter les statistiques système','system','stats','system','2025-09-05 16:05:27','2025-09-05 16:05:27'),(20,'Accès FIFA Connect','fifa.connect','Accès aux fonctionnalités FIFA Connect','fifa','connect','fifa','2025-09-05 16:05:27','2025-09-05 16:05:27'),(21,'Synchronisation FIFA','fifa.sync','Synchroniser les données avec FIFA','fifa','sync','fifa','2025-09-05 16:05:27','2025-09-05 16:05:27'),(22,'Accès médical','medical.access','Accès aux dossiers médicaux','medical','access','medical','2025-09-05 16:05:27','2025-09-05 16:05:27'),(23,'Gestion des dossiers médicaux','medical.manage','Gérer les dossiers médicaux','medical','manage','medical','2025-09-05 16:05:27','2025-09-05 16:05:27');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `physio_centers`
--

DROP TABLE IF EXISTS `physio_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `physio_centers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `center_name` varchar(255) NOT NULL,
  `center_type` enum('physiotherapy','rehabilitation','sports_medicine','wellness') NOT NULL,
  `api_endpoint` varchar(500) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `last_session_date` date DEFAULT NULL,
  `session_count` int DEFAULT '0',
  `treatment_plan` json DEFAULT NULL,
  `equipment_used` json DEFAULT NULL,
  `therapist_notes` text,
  `next_appointment` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_center` (`player_id`,`center_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physio_centers`
--

LOCK TABLES `physio_centers` WRITE;
/*!40000 ALTER TABLE `physio_centers` DISABLE KEYS */;
INSERT INTO `physio_centers` VALUES (1,4,'Centre de Physiothérapie Sportive FIFA','sports_medicine','https://api.fifa-physio.com/v1/player-data','fifa_physio_key_2024','2025-08-28',12,'{\"duree\": \"6 semaines\", \"objectif\": \"Récupération post-blessure\", \"exercices\": [\"étirements\", \"renforcement\", \"proprioception\"]}','[\"ultrasons\", \"electrostimulation\", \"plateforme vibrante\"]','Progrès satisfaisants, récupération en bonne voie','2025-09-04','2025-08-31 13:03:34','2025-08-31 13:03:34'),(2,4,'Institut de Rééducation Connecté','rehabilitation','https://api.rehab-connect.com/v2/patient-data','rehab_connect_key_789','2025-08-24',8,'{\"objectif\": \"Prévention des blessures\", \"exercices\": [\"stabilisation\", \"équilibre\", \"mobilité\"]}','[\"capteurs de mouvement\", \"plateforme d\'équilibre\", \"tapis roulant\"]','Amélioration de la stabilité, continuer les exercices','2025-09-10','2025-08-31 13:03:34','2025-08-31 13:03:34');
/*!40000 ALTER TABLE `physio_centers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_awards`
--

DROP TABLE IF EXISTS `player_awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_awards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `award_name` varchar(255) NOT NULL,
  `award_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_awards`
--

LOCK TABLES `player_awards` WRITE;
/*!40000 ALTER TABLE `player_awards` DISABLE KEYS */;
/*!40000 ALTER TABLE `player_awards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_contracts`
--

DROP TABLE IF EXISTS `player_contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `club_id` bigint unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_contracts`
--

LOCK TABLES `player_contracts` WRITE;
/*!40000 ALTER TABLE `player_contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `player_contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_health_wellbeing`
--

DROP TABLE IF EXISTS `player_health_wellbeing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_health_wellbeing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `assessment_date` date NOT NULL,
  `fitness_score` decimal(5,2) DEFAULT NULL,
  `energy_level` decimal(5,2) DEFAULT NULL,
  `sleep_quality` decimal(5,2) DEFAULT NULL,
  `hydration_level` decimal(5,2) DEFAULT NULL,
  `calories_consumed` int DEFAULT NULL,
  `protein_intake` decimal(5,2) DEFAULT NULL,
  `stress_level` decimal(5,2) DEFAULT NULL,
  `mood_score` decimal(5,2) DEFAULT NULL,
  `recovery_score` decimal(5,2) DEFAULT NULL,
  `overall_wellbeing` decimal(5,2) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`assessment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_health_wellbeing`
--

LOCK TABLES `player_health_wellbeing` WRITE;
/*!40000 ALTER TABLE `player_health_wellbeing` DISABLE KEYS */;
INSERT INTO `player_health_wellbeing` VALUES (1,4,'2025-08-31',82.50,78.00,7.80,85.00,2850,180.50,25.00,8.20,76.00,79.80,'État général bon, amélioration de la récupération','2025-08-31 12:34:02','2025-08-31 12:34:02');
/*!40000 ALTER TABLE `player_health_wellbeing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_injuries_diseases`
--

DROP TABLE IF EXISTS `player_injuries_diseases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_injuries_diseases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `incident_date` date NOT NULL,
  `type` enum('injury','disease','surgery','rehabilitation') NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `icd_11_code` varchar(20) DEFAULT NULL,
  `icd_11_description` text,
  `severity` enum('mild','moderate','severe','critical') NOT NULL,
  `body_part` varchar(255) DEFAULT NULL,
  `diagnosis` text,
  `treatment` text,
  `recovery_days` int DEFAULT NULL,
  `recurrence_count` int DEFAULT '0',
  `is_resolved` tinyint(1) DEFAULT '0',
  `medical_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_type` (`player_id`,`type`),
  KEY `idx_player_severity` (`player_id`,`severity`),
  KEY `idx_player_icd` (`player_id`,`icd_11_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_injuries_diseases`
--

LOCK TABLES `player_injuries_diseases` WRITE;
/*!40000 ALTER TABLE `player_injuries_diseases` DISABLE KEYS */;
INSERT INTO `player_injuries_diseases` VALUES (1,4,'2025-03-03','injury','Musculo-squelettique','NC56.0','Lésion du ligament croisé antérieur du genou','moderate','Genou droit','Entorse du ligament croisé antérieur grade 2','Rééducation fonctionnelle, renforcement musculaire',45,0,1,'Récupération complète, retour au jeu autorisé','2025-08-31 12:50:07','2025-08-31 12:50:07'),(2,4,'2025-05-31','injury','Musculo-squelettique','NC56.1','Lésion du ligament latéral interne du genou','mild','Genou gauche','Entorse du ligament latéral interne grade 1','Repos, glace, compression, élévation',14,1,1,'Blessure récurrente, surveillance renforcée','2025-08-31 12:50:07','2025-08-31 12:50:07'),(3,4,'2025-07-31','injury','Musculo-squelettique','NC56.2','Lésion du tendon d\'Achille','moderate','Cheville droite','Tendinopathie d\'insertion du tendon d\'Achille','Physiothérapie, étirements progressifs',30,0,0,'En cours de récupération, surveillance continue','2025-08-31 12:50:07','2025-08-31 12:50:07'),(4,4,'2024-12-31','disease','Respiratoire','CA23.0','Asthme induit par l\'exercice','mild','Système respiratoire','Asthme d\'effort léger','Ventoline avant effort, évitement des déclencheurs',0,3,0,'Condition chronique, bien contrôlée','2025-08-31 12:50:07','2025-08-31 12:50:07'),(5,4,'2024-08-31','injury','Musculo-squelettique','NC56.3','Fracture de fatigue du 5ème métatarsien','severe','Pied droit','Fracture de fatigue du 5ème métatarsien','Immobilisation, repos strict, reprise progressive',90,0,1,'Récupération complète, prévention renforcée','2025-08-31 12:50:07','2025-08-31 12:50:07');
/*!40000 ALTER TABLE `player_injuries_diseases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_licenses`
--

DROP TABLE IF EXISTS `player_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_licenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `club_id` bigint unsigned NOT NULL,
  `license_type` enum('amateur','semi_pro','professional','international') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','suspended','revoked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `issued_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `license_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_licenses_license_number_unique` (`license_number`),
  KEY `player_licenses_player_id_club_id_index` (`player_id`,`club_id`),
  KEY `player_licenses_club_id_status_index` (`club_id`,`status`),
  KEY `player_licenses_license_type_status_index` (`license_type`,`status`),
  KEY `player_licenses_end_date_index` (`end_date`),
  CONSTRAINT `player_licenses_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `player_licenses_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_licenses`
--

LOCK TABLES `player_licenses` WRITE;
/*!40000 ALTER TABLE `player_licenses` DISABLE KEYS */;
INSERT INTO `player_licenses` VALUES (4,4,1,'amateur','2012-08-01','2013-07-31','expired',NULL,'Licence Jeune Formation - Première licence à 14 ans','TUN-2012-001','2025-08-31 14:10:47','2025-08-31 14:10:47'),(5,4,1,'amateur','2013-08-01','2014-07-31','expired',NULL,'Licence Jeune Formation - Développement technique','TUN-2013-001','2025-08-31 14:10:59','2025-08-31 14:10:59'),(6,4,1,'amateur','2014-08-01','2015-07-31','expired',NULL,'Licence Jeune Formation - Perfectionnement','TUN-2014-001','2025-08-31 14:11:09','2025-08-31 14:11:09'),(7,4,1,'amateur','2015-08-01','2016-07-31','expired',NULL,'Licence Jeune Formation - Dernière année formation','TUN-2015-001','2025-08-31 14:11:19','2025-08-31 14:11:19'),(8,4,1,'amateur','2016-08-01','2017-07-31','expired',NULL,'Licence Amateur - Première licence senior','TUN-2016-001','2025-08-31 14:11:57','2025-08-31 14:11:57'),(9,4,1,'amateur','2017-08-01','2018-07-31','expired',NULL,'Licence Amateur - Développement senior','TUN-2017-001','2025-08-31 14:12:36','2025-08-31 14:12:36'),(10,4,1,'professional','2018-08-01','2019-07-31','expired',NULL,'Licence Professionnelle - Début carrière pro','TUN-2018-001','2025-08-31 14:12:46','2025-08-31 14:12:46'),(11,4,1,'professional','2019-08-01','2020-07-31','expired',NULL,'Licence Professionnelle - Confirmation','TUN-2019-001','2025-08-31 14:12:55','2025-08-31 14:12:55'),(12,4,3,'professional','2020-08-01','2021-07-31','expired',NULL,'Licence Professionnelle - Prêt international PSG','FRA-2020-001','2025-08-31 14:13:05','2025-08-31 14:13:05'),(13,4,4,'professional','2021-08-01','2022-07-31','expired',NULL,'Licence Professionnelle - Transfert international Manchester United','ENG-2021-001','2025-08-31 14:13:14','2025-08-31 14:13:14'),(14,4,2,'professional','2022-08-01','2023-07-31','expired',NULL,'Licence Professionnelle - Retour Tunisie - Club rival Espérance','TUN-2022-001','2025-08-31 14:13:27','2025-08-31 14:13:27'),(16,4,6,'professional','2023-08-01','2024-07-31','expired',NULL,'Licence Professionnelle - 3ème club tunisien Étoile du Sahel','TUN-2023-001','2025-08-31 14:15:14','2025-08-31 14:15:14'),(17,4,1,'professional','2024-08-01','2025-07-31','active',NULL,'Licence Professionnelle ACTIVE - Retour au club formateur Club Africain','TUN-2024-001','2025-08-31 14:15:26','2025-08-31 14:15:26');
/*!40000 ALTER TABLE `player_licenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_medical_aptitude`
--

DROP TABLE IF EXISTS `player_medical_aptitude`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_medical_aptitude` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `assessment_date` date NOT NULL,
  `medical_status` enum('fit','unfit','temporarily_unfit','under_observation') DEFAULT 'fit',
  `fitness_level` enum('excellent','good','fair','poor') DEFAULT 'good',
  `cardiovascular_health` decimal(5,2) DEFAULT NULL,
  `respiratory_health` decimal(5,2) DEFAULT NULL,
  `musculoskeletal_health` decimal(5,2) DEFAULT NULL,
  `neurological_health` decimal(5,2) DEFAULT NULL,
  `mental_health` decimal(5,2) DEFAULT NULL,
  `overall_health_score` decimal(5,2) DEFAULT NULL,
  `restrictions` json DEFAULT NULL,
  `medical_conditions` json DEFAULT NULL,
  `medications_list` json DEFAULT NULL,
  `allergies` json DEFAULT NULL,
  `emergency_contact` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_status` (`player_id`,`medical_status`),
  KEY `idx_player_date` (`player_id`,`assessment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_medical_aptitude`
--

LOCK TABLES `player_medical_aptitude` WRITE;
/*!40000 ALTER TABLE `player_medical_aptitude` DISABLE KEYS */;
INSERT INTO `player_medical_aptitude` VALUES (1,4,'2025-08-21','fit','excellent',94.00,91.00,88.00,93.00,89.00,91.20,'[]','{\"asthme\": \"léger\", \"allergie_pollens\": \"modérée\"}','{\"ventoline\": \"si nécessaire\", \"antihistaminique\": \"saison pollens\"}','{\"pollens\": \"modérée\", \"pénicilline\": \"aucune\"}','{\"médecin\": \"Dr. Martinez\", \"téléphone\": \"+33 1 23 45 67 89\"}','2025-08-31 12:44:28','2025-08-31 12:44:28');
/*!40000 ALTER TABLE `player_medical_aptitude` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_medications`
--

DROP TABLE IF EXISTS `player_medications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_medications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `medication_name` varchar(255) NOT NULL,
  `medication_type` enum('painkiller','anti_inflammatory','supplement','prescription','other') NOT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed','discontinued') DEFAULT 'active',
  `side_effects` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_status` (`player_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_medications`
--

LOCK TABLES `player_medications` WRITE;
/*!40000 ALTER TABLE `player_medications` DISABLE KEYS */;
INSERT INTO `player_medications` VALUES (1,4,'Vitamine D3','supplement','2000 UI','1 fois par jour','2025-07-01','2025-10-01','active','Aucun effet secondaire rapporté','Supplément pour maintenir les niveaux de vitamine D','2025-08-31 12:27:11','2025-08-31 12:27:11'),(2,4,'Oméga-3','supplement','1000mg','2 fois par jour','2025-07-31','2025-10-31','active','Aucun effet secondaire rapporté','Acides gras essentiels pour la récupération','2025-08-31 12:27:11','2025-08-31 12:27:11');
/*!40000 ALTER TABLE `player_medications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_notifications`
--

DROP TABLE IF EXISTS `player_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `notification_type` enum('injury_alert','medication_alert','performance_alert','medical_alert','general') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `severity` enum('low','medium','high','critical') NOT NULL,
  `status` enum('active','acknowledged','resolved') DEFAULT 'active',
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_type` (`player_id`,`notification_type`),
  KEY `idx_player_status` (`player_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_notifications`
--

LOCK TABLES `player_notifications` WRITE;
/*!40000 ALTER TABLE `player_notifications` DISABLE KEYS */;
INSERT INTO `player_notifications` VALUES (1,4,'performance_alert','Forme physique en baisse','Votre score de forme physique a diminué de 5% cette semaine','medium','active','{\"trend\": \"decreasing\", \"current_score\": 75, \"previous_score\": 80}','2025-08-31 12:28:04','2025-08-31 12:28:04'),(2,4,'medical_alert','Prochaine consultation médicale','Rendez-vous médical prévu dans 3 jours','low','active','{\"doctor\": \"Dr. Martinez\", \"appointment_date\": \"2025-09-03\"}','2025-08-31 12:28:04','2025-08-31 12:28:04'),(3,4,'general','Test antidopage programmé','Test antidopage prévu la semaine prochaine','medium','active','{\"type\": \"random\", \"test_date\": \"2025-09-07\"}','2025-08-31 12:28:04','2025-08-31 12:28:04');
/*!40000 ALTER TABLE `player_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_nutrition`
--

DROP TABLE IF EXISTS `player_nutrition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_nutrition` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `total_calories` int DEFAULT NULL,
  `protein_grams` decimal(5,2) DEFAULT NULL,
  `carbs_grams` decimal(5,2) DEFAULT NULL,
  `fat_grams` decimal(5,2) DEFAULT NULL,
  `fiber_grams` decimal(5,2) DEFAULT NULL,
  `water_liters` decimal(3,2) DEFAULT NULL,
  `vitamins_supplements` json DEFAULT NULL,
  `meal_quality_score` decimal(5,2) DEFAULT NULL,
  `hydration_score` decimal(5,2) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_nutrition`
--

LOCK TABLES `player_nutrition` WRITE;
/*!40000 ALTER TABLE `player_nutrition` DISABLE KEYS */;
INSERT INTO `player_nutrition` VALUES (1,4,'2025-08-31',2850,180.50,320.00,95.00,28.50,3.20,'{\"omega_3\": \"1000mg\", \"vitamin_d\": \"2000 UI\", \"multivitamin\": \"1 capsule\"}',8.50,85.00,'Régime équilibré, bon apport en protéines','2025-08-31 12:34:15','2025-08-31 12:34:15');
/*!40000 ALTER TABLE `player_nutrition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_pcma`
--

DROP TABLE IF EXISTS `player_pcma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_pcma` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `assessment_date` date NOT NULL,
  `pcma_status` enum('pending','approved','rejected','expired') DEFAULT 'pending',
  `pcma_score` decimal(5,2) DEFAULT NULL,
  `cardiovascular_fitness` decimal(5,2) DEFAULT NULL,
  `respiratory_fitness` decimal(5,2) DEFAULT NULL,
  `musculoskeletal_fitness` decimal(5,2) DEFAULT NULL,
  `neurological_fitness` decimal(5,2) DEFAULT NULL,
  `overall_fitness` decimal(5,2) DEFAULT NULL,
  `risk_factors` json DEFAULT NULL,
  `recommendations` text,
  `medical_notes` text,
  `next_assessment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_status` (`player_id`,`pcma_status`),
  KEY `idx_player_date` (`player_id`,`assessment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_pcma`
--

LOCK TABLES `player_pcma` WRITE;
/*!40000 ALTER TABLE `player_pcma` DISABLE KEYS */;
INSERT INTO `player_pcma` VALUES (1,4,'2025-08-16','approved',87.50,92.00,88.00,85.00,90.00,87.50,'{\"diabetes\": \"none\", \"hypertension\": \"low\", \"cardiac_history\": \"none\"}','Maintenir le niveau actuel, surveillance annuelle recommandée','Joueur en excellente condition physique, apte à la compétition','2026-07-31','2025-08-31 12:42:45','2025-08-31 12:42:45');
/*!40000 ALTER TABLE `player_pcma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_performances`
--

DROP TABLE IF EXISTS `player_performances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_performances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `season_id` bigint unsigned NOT NULL,
  `competition_id` bigint unsigned NOT NULL,
  `matches_played` int NOT NULL DEFAULT '0',
  `minutes_played` int NOT NULL DEFAULT '0',
  `goals_scored` int NOT NULL DEFAULT '0',
  `assists` int NOT NULL DEFAULT '0',
  `shots_on_target` int NOT NULL DEFAULT '0',
  `shots_total` int NOT NULL DEFAULT '0',
  `passes_completed` int NOT NULL DEFAULT '0',
  `passes_total` int NOT NULL DEFAULT '0',
  `key_passes` int NOT NULL DEFAULT '0',
  `crosses_completed` int NOT NULL DEFAULT '0',
  `crosses_total` int NOT NULL DEFAULT '0',
  `tackles_won` int NOT NULL DEFAULT '0',
  `tackles_total` int NOT NULL DEFAULT '0',
  `interceptions` int NOT NULL DEFAULT '0',
  `clearances` int NOT NULL DEFAULT '0',
  `blocks` int NOT NULL DEFAULT '0',
  `duels_won` int NOT NULL DEFAULT '0',
  `duels_total` int NOT NULL DEFAULT '0',
  `fouls_committed` int NOT NULL DEFAULT '0',
  `fouls_drawn` int NOT NULL DEFAULT '0',
  `yellow_cards` int NOT NULL DEFAULT '0',
  `red_cards` int NOT NULL DEFAULT '0',
  `distance_covered` decimal(5,2) NOT NULL DEFAULT '0.00',
  `sprint_distance` decimal(5,2) NOT NULL DEFAULT '0.00',
  `max_speed` decimal(4,1) NOT NULL DEFAULT '0.0',
  `sprints_count` int NOT NULL DEFAULT '0',
  `avg_speed` decimal(4,1) NOT NULL DEFAULT '0.0',
  `expected_goals` decimal(4,2) NOT NULL DEFAULT '0.00',
  `expected_assists` decimal(4,2) NOT NULL DEFAULT '0.00',
  `pass_accuracy` decimal(5,2) NOT NULL DEFAULT '0.00',
  `shot_accuracy` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tackle_success_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `duel_success_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overall_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `attacking_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `defending_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `physical_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `technical_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `mental_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_performances`
--

LOCK TABLES `player_performances` WRITE;
/*!40000 ALTER TABLE `player_performances` DISABLE KEYS */;
/*!40000 ALTER TABLE `player_performances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_recovery`
--

DROP TABLE IF EXISTS `player_recovery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_recovery` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `sleep_hours` decimal(3,1) DEFAULT NULL,
  `sleep_quality_score` decimal(5,2) DEFAULT NULL,
  `muscle_soreness` decimal(5,2) DEFAULT NULL,
  `fatigue_level` decimal(5,2) DEFAULT NULL,
  `stress_level` decimal(5,2) DEFAULT NULL,
  `recovery_activities` json DEFAULT NULL,
  `massage_therapy` tinyint(1) DEFAULT NULL,
  `ice_bath` tinyint(1) DEFAULT NULL,
  `stretching_minutes` int DEFAULT NULL,
  `meditation_minutes` int DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_recovery`
--

LOCK TABLES `player_recovery` WRITE;
/*!40000 ALTER TABLE `player_recovery` DISABLE KEYS */;
INSERT INTO `player_recovery` VALUES (1,4,'2025-08-31',8.2,7.80,35.00,28.00,25.00,'{\"massage\": \"30 min\", \"ice_bath\": \"5 min\", \"stretching\": \"20 min\"}',1,1,20,15,'Bonne récupération, techniques avancées utilisées','2025-08-31 12:34:35','2025-08-31 12:34:35');
/*!40000 ALTER TABLE `player_recovery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_season_stats`
--

DROP TABLE IF EXISTS `player_season_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_season_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `competition_id` bigint unsigned NOT NULL,
  `matches_played` int NOT NULL DEFAULT '0',
  `minutes_played` int NOT NULL DEFAULT '0',
  `goals` int NOT NULL DEFAULT '0',
  `assists` int NOT NULL DEFAULT '0',
  `yellow_cards` int NOT NULL DEFAULT '0',
  `red_cards` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player_season_stats_player_id_foreign` (`player_id`),
  CONSTRAINT `player_season_stats_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_season_stats`
--

LOCK TABLES `player_season_stats` WRITE;
/*!40000 ALTER TABLE `player_season_stats` DISABLE KEYS */;
INSERT INTO `player_season_stats` VALUES (2,4,1,16,1462,8,6,1,0,'2025-08-31 12:16:09','2025-08-31 12:16:09'),(3,4,1,15,1334,11,5,1,0,'2025-08-31 12:16:09','2025-08-31 12:16:09'),(4,4,1,15,1399,10,7,3,0,'2025-08-31 12:16:09','2025-08-31 12:16:09'),(5,4,1,18,1263,10,5,2,1,'2025-08-31 12:16:09','2025-08-31 12:16:09');
/*!40000 ALTER TABLE `player_season_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_trophies`
--

DROP TABLE IF EXISTS `player_trophies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_trophies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `trophy_name` varchar(255) NOT NULL,
  `season` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_trophies`
--

LOCK TABLES `player_trophies` WRITE;
/*!40000 ALTER TABLE `player_trophies` DISABLE KEYS */;
/*!40000 ALTER TABLE `player_trophies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_vital_signs`
--

DROP TABLE IF EXISTS `player_vital_signs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_vital_signs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `measurement_date` date NOT NULL,
  `blood_pressure_systolic` int DEFAULT NULL,
  `blood_pressure_diastolic` int DEFAULT NULL,
  `heart_rate_resting` int DEFAULT NULL,
  `heart_rate_max` int DEFAULT NULL,
  `heart_rate_recovery` int DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `oxygen_saturation` decimal(4,1) DEFAULT NULL,
  `respiratory_rate` int DEFAULT NULL,
  `blood_glucose` decimal(4,1) DEFAULT NULL,
  `body_weight` decimal(5,2) DEFAULT NULL,
  `body_height` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,2) DEFAULT NULL,
  `body_fat_percentage` decimal(4,1) DEFAULT NULL,
  `muscle_mass_percentage` decimal(4,1) DEFAULT NULL,
  `hydration_percentage` decimal(4,1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`measurement_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_vital_signs`
--

LOCK TABLES `player_vital_signs` WRITE;
/*!40000 ALTER TABLE `player_vital_signs` DISABLE KEYS */;
INSERT INTO `player_vital_signs` VALUES (1,4,'2025-08-31',118,75,58,185,25,36.8,98.5,14,5.2,75.50,180.00,23.30,12.5,45.2,62.8,'2025-08-31 12:44:44','2025-08-31 12:44:44');
/*!40000 ALTER TABLE `player_vital_signs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `players` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `position` varchar(10) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `club_id` bigint unsigned DEFAULT NULL,
  `association_id` bigint unsigned DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `legal_guardian` varchar(255) DEFAULT NULL,
  `school_professional_status` varchar(50) DEFAULT NULL,
  `parental_consent` varchar(50) DEFAULT NULL,
  `license_type` varchar(50) DEFAULT NULL,
  `previous_clubs` text,
  `previous_license_number` varchar(100) DEFAULT NULL,
  `player_picture` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `players_name_index` (`name`),
  KEY `players_club_id_index` (`club_id`),
  KEY `fk_players_association_id` (`association_id`),
  KEY `players_tenant_id_index` (`tenant_id`),
  CONSTRAINT `fk_players_association_id` FOREIGN KEY (`association_id`) REFERENCES `associations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `players_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1034 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `players`
--

LOCK TABLES `players` WRITE;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
INSERT INTO `players` VALUES (3,1,'Mike Wilson','Mike','Wilson','1997-11-08','DF','UK',4,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-26 20:57:09','2025-09-07 02:03:26'),(4,1,'Ahmed Ben Salah','Ahmed','Ben Salah','1998-05-15','MF','Tunisie',1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'players/photos/sUVZDDzSNxgTz5zsdlMbBybPywgsYb0gy5KgEoCS.jpg','2025-08-30 12:18:44','2025-09-07 02:03:26'),(5,1,'Youssef Msakni','Youssef','Msakni','1990-10-28','FW','Tunisie',2,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'players/photos/Z6BmTKSCmocXR59untugh7PuUgY5zsVw8G1EmVW6.jpg','2025-08-30 12:18:44','2025-09-07 02:03:26'),(6,1,'Hamza Lahmar','Hamza','Lahmar','1995-12-03','DF','Tunisie',1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'players/photos/zo3vfevB4ptrRtOet7e2MtKsyvoW2qRDXPgeDm6j.png','2025-08-30 12:18:44','2025-09-07 02:03:26'),(7,1,'Aymen Mathlouthi','Aymen','Mathlouthi','1984-09-14','GK','Tunisie',2,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'players/photos/46dZVAoftPYrl93ha97KIEAQzpgRtezh5sRFcue6.jpg','2025-08-30 12:18:44','2025-09-07 02:03:26'),(8,2,'Hugo Morel','Hugo','Morel','1998-06-10','FWD','Algeria',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-07 02:03:26'),(9,2,'Romain David','Romain','David','2007-06-06','DEF','Netherlands',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-07 02:03:26'),(10,2,'Quentin Morel','Quentin','Morel','1993-08-02','FWD','Poland',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-07 02:03:26'),(11,2,'Baptiste André','Baptiste','André','1997-09-09','MID','Morocco',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-07 02:03:26'),(12,2,'Adrien Garnier','Adrien','Garnier','2000-08-30','GK','Argentina',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-07 02:03:26'),(13,NULL,'Sébastien Dubois','Sébastien','Dubois','1993-06-16','FWD','Mali',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(14,NULL,'Zinedine Richard','Zinedine','Richard','1990-02-28','MID','Algeria',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(15,NULL,'Benjamin Martin','Benjamin','Martin','2002-03-05','DEF','Croatia',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(16,NULL,'Hugo Girard','Hugo','Girard','2005-10-03','FWD','Belgium',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(17,NULL,'Nicolas Michel','Nicolas','Michel','2000-10-30','FWD','Tunisia',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(18,NULL,'Pierre Robert','Pierre','Robert','1992-11-18','DEF','Morocco',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(19,NULL,'Kévin François','Kévin','François','2001-10-08','MID','Morocco',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(20,NULL,'Corentin Roux','Corentin','Roux','1998-09-30','DEF','Tunisia',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(21,NULL,'Damien Durand','Damien','Durand','1993-10-11','DEF','Brazil',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(22,NULL,'Corentin David','Corentin','David','1990-04-02','FWD','Mali',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(23,NULL,'Olivier David','Olivier','David','1994-02-23','GK','Netherlands',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(24,NULL,'Adrien Dubois','Adrien','Dubois','1989-10-25','DEF','Algeria',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(25,NULL,'Pierre Moreau','Pierre','Moreau','1995-12-11','FWD','Spain',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(26,NULL,'William Dubois','William','Dubois','2006-07-23','FWD','Poland',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(27,NULL,'Adrien Thomas','Adrien','Thomas','2005-09-30','FWD','Croatia',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(28,NULL,'Benjamin Durand','Benjamin','Durand','1990-12-17','MID','France',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(29,NULL,'Olivier Laurent','Olivier','Laurent','1997-03-16','GK','Mali',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(30,NULL,'Hugo François','Hugo','François','2000-08-30','GK','Argentina',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(31,NULL,'Édouard Garnier','Édouard','Garnier','2002-01-10','FWD','Spain',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(32,NULL,'Vincent Dubois','Vincent','Dubois','2003-03-11','FWD','Senegal',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(33,NULL,'Lucas André','Lucas','André','1995-04-17','GK','Portugal',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(34,NULL,'Damien Garnier','Damien','Garnier','2006-02-06','GK','Senegal',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(35,NULL,'Zinedine Dubois','Zinedine','Dubois','2003-12-30','GK','Brazil',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(36,NULL,'Romain André','Romain','André','2006-01-10','GK','Senegal',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(37,NULL,'Mathieu Simon','Mathieu','Simon','1990-03-04','DEF','France',3,2,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:05:32','2025-09-02 18:05:32'),(38,NULL,'Cédric David','Cédric','David','2004-07-27','MID','Ivory Coast',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(39,NULL,'Gabriel Leroy','Gabriel','Leroy','2006-08-19','DEF','Belgium',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(40,NULL,'Nicolas Roux','Nicolas','Roux','1996-03-04','MID','Italy',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(41,NULL,'Damien Girard','Damien','Girard','1994-09-13','GK','Mali',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(42,NULL,'Damien Moreau','Damien','Moreau','2004-12-25','GK','Cameroon',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(43,NULL,'Benjamin Martinez','Benjamin','Martinez','2006-04-28','DEF','Poland',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(44,NULL,'Quentin Bonnet','Quentin','Bonnet','2004-03-07','DEF','Ivory Coast',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(45,NULL,'Corentin Durand','Corentin','Durand','1996-05-19','MID','Senegal',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(46,NULL,'Fabien André','Fabien','André','1994-05-08','MID','Serbia',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(47,NULL,'Zinedine Richard','Zinedine','Richard','1999-03-29','MID','France',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(48,NULL,'Nicolas Legrand','Nicolas','Legrand','1995-01-22','DEF','Serbia',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(49,NULL,'Benoît Mercier','Benoît','Mercier','1991-07-11','DEF','Brazil',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(50,NULL,'Vincent Moreau','Vincent','Moreau','2006-08-14','FWD','Morocco',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(51,NULL,'Lucas Laurent','Lucas','Laurent','1989-12-18','DEF','Croatia',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(52,NULL,'Nicolas Garnier','Nicolas','Garnier','1998-08-31','GK','Poland',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(53,NULL,'Mathieu Lefèvre','Mathieu','Lefèvre','1990-12-19','DEF','Netherlands',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(54,NULL,'Damien Dupont','Damien','Dupont','1999-09-11','DEF','Senegal',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(55,NULL,'David Dupont','David','Dupont','2001-07-19','GK','Spain',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(56,NULL,'Édouard Petit','Édouard','Petit','1991-04-02','FWD','Belgium',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(57,NULL,'Gabriel Thomas','Gabriel','Thomas','1994-07-17','GK','Spain',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(58,NULL,'Antoine Martinez','Antoine','Martinez','1993-10-14','FWD','Croatia',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(59,NULL,'Baptiste André','Baptiste','André','2006-05-29','GK','Italy',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(60,NULL,'Lucas Legrand','Lucas','Legrand','2002-06-01','DEF','Germany',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(61,NULL,'Nicolas Bonnet','Nicolas','Bonnet','2005-07-13','FWD','Germany',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(62,NULL,'Benoît Bernard','Benoît','Bernard','1996-09-18','DEF','Portugal',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(63,NULL,'Sébastien Girard','Sébastien','Girard','2005-06-30','FWD','Burkina Faso',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(64,NULL,'Julien Martinez','Julien','Martinez','1996-01-07','GK','Argentina',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(65,NULL,'Alexandre Legrand','Alexandre','Legrand','1996-01-05','FWD','Mali',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(66,NULL,'Quentin David','Quentin','David','1997-09-19','MID','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(67,NULL,'Pierre Girard','Pierre','Girard','2001-06-20','MID','Spain',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(68,NULL,'Mathieu Robert','Mathieu','Robert','2003-08-03','DEF','Netherlands',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(69,NULL,'Quentin Richard','Quentin','Richard','2006-09-07','DEF','Argentina',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(70,NULL,'Benjamin Thomas','Benjamin','Thomas','1997-08-21','GK','Mali',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(71,NULL,'Olivier Bonnet','Olivier','Bonnet','1998-07-26','MID','Algeria',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(72,NULL,'Adrien Leroy','Adrien','Leroy','1996-05-09','GK','Brazil',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(73,NULL,'Thomas Morel','Thomas','Morel','1999-10-14','MID','Cameroon',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(74,NULL,'Sébastien Legrand','Sébastien','Legrand','1998-04-20','DEF','Burkina Faso',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(75,NULL,'Yann Thomas','Yann','Thomas','1997-05-04','GK','Brazil',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(76,NULL,'Corentin Girard','Corentin','Girard','2004-08-02','FWD','Italy',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(77,NULL,'Adrien Martin','Adrien','Martin','1996-09-23','FWD','Netherlands',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(78,NULL,'Corentin Morel','Corentin','Morel','1992-09-04','FWD','Senegal',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(79,NULL,'William Garnier','William','Garnier','2007-04-15','GK','Spain',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(80,NULL,'Kévin Fournier','Kévin','Fournier','2002-09-30','MID','Germany',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(81,NULL,'William Richard','William','Richard','1991-09-19','DEF','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(82,NULL,'Adrien Durand','Adrien','Durand','1991-04-09','GK','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(83,NULL,'Lucas Leroy','Lucas','Leroy','1998-03-14','FWD','Italy',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(84,NULL,'David François','David','François','1992-02-02','MID','Senegal',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(85,NULL,'Nicolas Girard','Nicolas','Girard','2003-10-06','MID','France',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(86,NULL,'Thomas Lefèvre','Thomas','Lefèvre','1999-01-10','MID','Brazil',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(87,NULL,'Yann Simon','Yann','Simon','2001-07-27','DEF','Morocco',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(88,NULL,'Kévin Morel','Kévin','Morel','1997-05-30','DEF','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(89,NULL,'Corentin Richard','Corentin','Richard','1989-12-20','DEF','Netherlands',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(90,NULL,'Kévin Lefebvre','Kévin','Lefebvre','2005-12-28','MID','Argentina',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(91,NULL,'Mathieu Moreau','Mathieu','Moreau','1995-10-07','DEF','Burkina Faso',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(92,NULL,'Cédric Petit','Cédric','Petit','2003-10-04','DEF','Tunisia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(93,NULL,'Ibrahim Michel','Ibrahim','Michel','1994-01-04','DEF','France',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(94,NULL,'Damien Bernard','Damien','Bernard','2005-12-03','MID','France',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(95,NULL,'Gabriel Bertrand','Gabriel','Bertrand','2006-10-15','DEF','Ivory Coast',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(96,NULL,'Édouard Martinez','Édouard','Martinez','1998-02-01','DEF','Tunisia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(97,NULL,'Fabien Robert','Fabien','Robert','1998-07-05','MID','Spain',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(98,NULL,'Sébastien Robert','Sébastien','Robert','1998-08-18','DEF','Netherlands',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(99,NULL,'Nicolas Petit','Nicolas','Petit','1993-10-21','DEF','Algeria',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(100,NULL,'Zinedine André','Zinedine','André','1990-05-15','DEF','Mali',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(101,NULL,'Fabien André','Fabien','André','1999-03-17','DEF','Italy',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(102,NULL,'Kévin Richard','Kévin','Richard','2006-04-07','MID','Germany',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(103,NULL,'Hugo Simon','Hugo','Simon','2003-05-22','DEF','Portugal',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(104,NULL,'Ibrahim Bernard','Ibrahim','Bernard','2005-01-23','GK','Croatia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(105,NULL,'Fabien Martinez','Fabien','Martinez','1997-10-21','FWD','Croatia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(106,NULL,'Antoine Bernard','Antoine','Bernard','2001-11-03','GK','Italy',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(107,NULL,'Édouard Dupont','Édouard','Dupont','2002-07-05','MID','Tunisia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(108,NULL,'David Garnier','David','Garnier','1994-07-15','DEF','Italy',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(109,NULL,'Olivier Moreau','Olivier','Moreau','2006-03-28','GK','Ivory Coast',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(110,NULL,'Thomas Legrand','Thomas','Legrand','2005-10-13','MID','Poland',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(111,NULL,'Édouard Girard','Édouard','Girard','2005-06-13','MID','Spain',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(112,NULL,'Sébastien Legrand','Sébastien','Legrand','2000-09-08','FWD','Morocco',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(113,NULL,'Quentin Morel','Quentin','Morel','2005-01-22','DEF','Portugal',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(114,NULL,'Damien Garnier','Damien','Garnier','2001-04-22','MID','Netherlands',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(115,NULL,'Pierre Laurent','Pierre','Laurent','1992-05-05','FWD','Croatia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(116,NULL,'Romain Simon','Romain','Simon','1996-12-12','DEF','Netherlands',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(117,NULL,'Zinedine David','Zinedine','David','2002-10-15','DEF','Ivory Coast',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(118,NULL,'Romain Petit','Romain','Petit','2000-01-09','GK','Argentina',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(119,NULL,'Thomas Dubois','Thomas','Dubois','1990-01-01','FWD','Morocco',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(120,NULL,'Cédric Dubois','Cédric','Dubois','1996-10-01','MID','France',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:12:50','2025-09-02 18:12:50'),(121,NULL,'Thomas Roux','Thomas','Roux','1990-11-01','DEF','Algeria',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:12:50','2025-09-02 18:12:50'),(122,NULL,'William Fournier','William','Fournier','1994-09-20','GK','Argentina',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:12:50','2025-09-02 18:12:50'),(123,NULL,'Ibrahim David','Ibrahim','David','1993-10-22','FWD','Poland',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:12:50','2025-09-02 18:12:50'),(124,NULL,'Pierre André','Pierre','André','1992-03-26','MID','Algeria',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(125,NULL,'Benoît Martin','Benoît','Martin','2001-11-04','FWD','Netherlands',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(126,NULL,'Sébastien Laurent','Sébastien','Laurent','2005-03-08','MID','Portugal',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(127,NULL,'Kévin Fournier','Kévin','Fournier','1999-01-26','FWD','Belgium',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(128,NULL,'Corentin François','Corentin','François','2003-12-11','DEF','Tunisia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(129,NULL,'Thomas Richard','Thomas','Richard','2002-08-22','FWD','Morocco',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(130,NULL,'Antoine Roux','Antoine','Roux','1995-05-30','GK','Germany',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(131,NULL,'Cédric Lambert','Cédric','Lambert','1991-04-19','MID','France',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(132,NULL,'Fabien Legrand','Fabien','Legrand','1989-12-19','MID','Tunisia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(133,NULL,'Pierre Martinez','Pierre','Martinez','1997-10-21','MID','Morocco',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(134,NULL,'Nicolas Petit','Nicolas','Petit','2005-11-07','GK','France',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(135,NULL,'Quentin Martinez','Quentin','Martinez','1993-11-09','DEF','Burkina Faso',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(136,NULL,'Vincent Garnier','Vincent','Garnier','1995-05-02','GK','Burkina Faso',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(137,NULL,'Corentin Simon','Corentin','Simon','2006-10-27','MID','Netherlands',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(138,NULL,'Ibrahim Bernard','Ibrahim','Bernard','1991-09-02','GK','Italy',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(139,NULL,'Cédric François','Cédric','François','1996-06-23','MID','Netherlands',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(140,NULL,'Adrien François','Adrien','François','1999-04-20','DEF','Spain',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(141,NULL,'Benoît Lefèvre','Benoît','Lefèvre','2004-08-19','DEF','Belgium',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(142,NULL,'Alexandre Martin','Alexandre','Martin','2006-04-17','MID','Burkina Faso',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(143,NULL,'Édouard Lefebvre','Édouard','Lefebvre','1989-09-11','FWD','Italy',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(144,NULL,'Adrien Dupont','Adrien','Dupont','1991-07-19','DEF','Burkina Faso',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(145,NULL,'Benjamin Durand','Benjamin','Durand','2003-05-07','FWD','Mali',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(146,NULL,'Yann Laurent','Yann','Laurent','1993-04-25','DEF','Poland',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(147,NULL,'Fabien Bertrand','Fabien','Bertrand','2007-07-05','FWD','France',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(148,NULL,'Thomas Martin','Thomas','Martin','1994-11-29','FWD','France',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(149,NULL,'Damien Fournier','Damien','Fournier','2003-04-12','MID','Portugal',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(150,NULL,'Antoine Girard','Antoine','Girard','1999-01-29','FWD','Belgium',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(151,NULL,'Nicolas Michel','Nicolas','Michel','1994-11-19','MID','Belgium',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(152,NULL,'Gabriel Martin','Gabriel','Martin','1990-04-10','MID','France',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(153,NULL,'Romain Michel','Romain','Michel','1996-04-09','GK','Belgium',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(154,NULL,'Adrien Bertrand','Adrien','Bertrand','1997-04-21','FWD','Cameroon',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(155,NULL,'Romain Girard','Romain','Girard','1992-06-05','DEF','Portugal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(156,NULL,'Hugo Robert','Hugo','Robert','1991-01-13','DEF','Tunisia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(157,NULL,'Kévin Leroy','Kévin','Leroy','2005-01-17','MID','Serbia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(158,NULL,'Baptiste David','Baptiste','David','1993-01-09','MID','Serbia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(159,NULL,'Vincent Lefebvre','Vincent','Lefebvre','1994-03-08','MID','Germany',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(160,NULL,'David André','David','André','2003-04-02','FWD','Brazil',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(161,NULL,'Édouard Dubois','Édouard','Dubois','1998-04-28','DEF','France',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(162,NULL,'Gabriel Girard','Gabriel','Girard','1999-03-12','MID','Portugal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(163,NULL,'Corentin Robert','Corentin','Robert','2005-12-05','MID','Morocco',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(164,NULL,'Ibrahim François','Ibrahim','François','2003-01-24','DEF','Serbia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(165,NULL,'Lucas Legrand','Lucas','Legrand','2002-12-06','MID','Tunisia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(166,NULL,'Baptiste Richard','Baptiste','Richard','1995-04-04','FWD','Ivory Coast',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(167,NULL,'Cédric Bernard','Cédric','Bernard','1994-10-17','FWD','Argentina',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(168,NULL,'Vincent Mercier','Vincent','Mercier','1995-10-20','MID','Netherlands',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(169,NULL,'Corentin Dubois','Corentin','Dubois','2002-07-06','MID','Burkina Faso',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(170,NULL,'Gabriel Durand','Gabriel','Durand','2001-08-01','MID','Portugal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(171,NULL,'Nicolas Fournier','Nicolas','Fournier','2003-07-19','FWD','Portugal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(172,NULL,'Mathieu Garnier','Mathieu','Garnier','1994-02-12','DEF','Italy',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(173,NULL,'Corentin Roux','Corentin','Roux','1992-12-10','DEF','Mali',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(174,NULL,'Hugo Mercier','Hugo','Mercier','1994-04-04','DEF','Morocco',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(175,NULL,'Gabriel Michel','Gabriel','Michel','1999-03-28','MID','Argentina',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(176,NULL,'Romain Laurent','Romain','Laurent','2002-06-09','FWD','Belgium',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(177,NULL,'Yann Simon','Yann','Simon','2001-01-07','DEF','Senegal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(178,NULL,'Olivier Thomas','Olivier','Thomas','1996-02-11','GK','Cameroon',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(179,NULL,'William Richard','William','Richard','1999-03-24','DEF','Poland',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(180,NULL,'Quentin Leroy','Quentin','Leroy','1992-09-12','GK','Senegal',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(181,NULL,'Yann Thomas','Yann','Thomas','1997-01-27','DEF','Cameroon',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(182,NULL,'Fabien Thomas','Fabien','Thomas','1992-01-12','GK','Tunisia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(183,NULL,'Pierre Michel','Pierre','Michel','1998-08-24','GK','Burkina Faso',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(184,NULL,'Hugo Simon','Hugo','Simon','1999-12-15','GK','Senegal',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(185,NULL,'Cédric Simon','Cédric','Simon','2003-10-20','DEF','Brazil',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(186,NULL,'Corentin Laurent','Corentin','Laurent','1993-05-23','FWD','Cameroon',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(187,NULL,'Lucas Durand','Lucas','Durand','2004-12-26','FWD','Cameroon',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(188,NULL,'William Roux','William','Roux','1990-09-22','MID','Morocco',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(189,NULL,'William Bonnet','William','Bonnet','2005-06-16','DEF','Italy',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(190,NULL,'Fabien Bernard','Fabien','Bernard','1993-04-27','FWD','Morocco',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(191,NULL,'Zinedine Mercier','Zinedine','Mercier','1996-05-02','MID','Serbia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(192,NULL,'Hugo Laurent','Hugo','Laurent','1997-02-27','FWD','Brazil',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(193,NULL,'Benjamin François','Benjamin','François','1995-01-26','MID','Morocco',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(194,NULL,'Lucas Mercier','Lucas','Mercier','1998-07-06','MID','Belgium',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(195,NULL,'Vincent Lefèvre','Vincent','Lefèvre','1993-12-07','FWD','Croatia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(196,NULL,'Yann Garnier','Yann','Garnier','1995-09-01','FWD','Croatia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(197,NULL,'Mathieu Lefebvre','Mathieu','Lefebvre','1996-11-15','DEF','Italy',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(198,NULL,'Fabien Martin','Fabien','Martin','1996-03-08','DEF','Tunisia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(199,NULL,'Olivier Michel','Olivier','Michel','2001-12-18','GK','Italy',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(200,NULL,'Zinedine Thomas','Zinedine','Thomas','2005-04-21','FWD','Netherlands',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(201,NULL,'Ibrahim Bernard','Ibrahim','Bernard','1990-05-13','MID','Germany',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(202,NULL,'Édouard Morel','Édouard','Morel','1999-02-15','GK','Croatia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(203,NULL,'Ibrahim François','Ibrahim','François','1991-10-09','MID','Portugal',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(204,NULL,'Vincent Roux','Vincent','Roux','1996-11-28','MID','Morocco',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(205,NULL,'Adrien Michel','Adrien','Michel','1992-04-15','FWD','Croatia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(206,NULL,'William Michel','William','Michel','2005-11-24','MID','Netherlands',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(207,NULL,'Nicolas Moreau','Nicolas','Moreau','1994-05-22','DEF','Serbia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(208,NULL,'Lucas Martinez','Lucas','Martinez','1998-01-20','MID','Poland',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(209,NULL,'Vincent Lefèvre','Vincent','Lefèvre','1999-02-14','FWD','Ivory Coast',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(210,NULL,'Cédric Michel','Cédric','Michel','2005-10-01','MID','Cameroon',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(211,NULL,'Quentin Mercier','Quentin','Mercier','1992-06-12','MID','Algeria',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(212,NULL,'Olivier Bernard','Olivier','Bernard','1990-07-29','DEF','Portugal',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(213,NULL,'Ibrahim Martinez','Ibrahim','Martinez','1997-01-02','MID','Serbia',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(214,NULL,'Julien Martin','Julien','Martin','1994-09-15','FWD','Brazil',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(215,NULL,'Cédric Petit','Cédric','Petit','1995-10-27','MID','Cameroon',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(216,NULL,'Julien Lambert','Julien','Lambert','2001-01-04','MID','Netherlands',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(217,NULL,'Benjamin Richard','Benjamin','Richard','1998-03-20','FWD','Morocco',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(218,NULL,'Cédric Simon','Cédric','Simon','2003-08-11','GK','Argentina',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(219,NULL,'William Dubois','William','Dubois','1994-06-22','FWD','Morocco',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(220,NULL,'Mathieu Durand','Mathieu','Durand','1990-10-14','FWD','Cameroon',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(221,NULL,'Sébastien Bernard','Sébastien','Bernard','2003-05-18','DEF','Senegal',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(222,NULL,'Gabriel Martin','Gabriel','Martin','1990-07-10','FWD','Morocco',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(223,NULL,'Adrien Robert','Adrien','Robert','1992-02-21','GK','France',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(224,NULL,'Olivier David','Olivier','David','1990-06-25','MID','Tunisia',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(225,NULL,'Benjamin Morel','Benjamin','Morel','1992-03-27','GK','Germany',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(226,NULL,'Adrien Lambert','Adrien','Lambert','2003-08-11','MID','Mali',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(227,NULL,'Julien Petit','Julien','Petit','1990-12-09','FWD','Germany',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(228,NULL,'Fabien Bernard','Fabien','Bernard','1999-09-24','MID','Argentina',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(229,NULL,'Zinedine Michel','Zinedine','Michel','1994-03-02','DEF','Morocco',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(230,NULL,'Benoît Leroy','Benoît','Leroy','2007-07-09','FWD','France',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(231,NULL,'Sébastien André','Sébastien','André','1998-01-27','DEF','Germany',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(232,NULL,'Baptiste Thomas','Baptiste','Thomas','1999-03-30','GK','Germany',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(233,NULL,'David Girard','David','Girard','2001-08-24','MID','Belgium',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(234,NULL,'Mathieu Bonnet','Mathieu','Bonnet','1993-08-08','FWD','Croatia',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(235,NULL,'Mathieu Lefèvre','Mathieu','Lefèvre','2003-03-17','FWD','Germany',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(236,NULL,'Corentin Laurent','Corentin','Laurent','1990-02-03','FWD','Ivory Coast',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(237,NULL,'Hugo Simon','Hugo','Simon','2002-01-29','DEF','Cameroon',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(238,NULL,'Édouard Thomas','Édouard','Thomas','2004-11-27','GK','Cameroon',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(239,NULL,'David André','David','André','1997-02-08','DEF','Serbia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(240,NULL,'Pierre Lefebvre','Pierre','Lefebvre','1990-06-25','DEF','Mali',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(241,NULL,'Benoît André','Benoît','André','2001-09-07','MID','Cameroon',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(242,NULL,'David Martinez','David','Martinez','2007-01-20','FWD','Morocco',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(243,NULL,'Lucas Legrand','Lucas','Legrand','2001-01-12','MID','Mali',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(244,NULL,'Thomas Lefèvre','Thomas','Lefèvre','1997-10-06','FWD','Poland',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(245,NULL,'Hugo Morel','Hugo','Morel','1992-12-02','MID','Morocco',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(246,NULL,'Lucas Thomas','Lucas','Thomas','1993-03-07','MID','Serbia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(247,NULL,'Adrien Thomas','Adrien','Thomas','2000-04-14','GK','Croatia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(248,NULL,'Hugo Fournier','Hugo','Fournier','1995-07-02','DEF','Argentina',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(249,NULL,'Benoît François','Benoît','François','1998-12-20','FWD','Portugal',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(250,NULL,'Cédric François','Cédric','François','1995-04-02','MID','Netherlands',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(251,NULL,'Romain Thomas','Romain','Thomas','2006-05-13','MID','France',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(252,NULL,'Fabien Girard','Fabien','Girard','1990-06-07','FWD','Spain',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(253,NULL,'Olivier Leroy','Olivier','Leroy','1996-04-02','DEF','Ivory Coast',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(254,NULL,'Pierre Lefèvre','Pierre','Lefèvre','1996-08-02','GK','Serbia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(255,NULL,'Antoine André','Antoine','André','1990-11-05','MID','France',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(256,NULL,'Benoît Morel','Benoît','Morel','2006-09-07','FWD','Mali',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(257,NULL,'Mathieu Morel','Mathieu','Morel','2007-05-01','MID','Serbia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(258,NULL,'Hugo Girard','Hugo','Girard','1997-09-15','FWD','Morocco',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(259,NULL,'Yann Thomas','Yann','Thomas','1990-09-20','MID','Poland',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(260,NULL,'Kévin Girard','Kévin','Girard','2002-11-16','GK','Croatia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(261,NULL,'David Petit','David','Petit','1993-03-05','GK','Portugal',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(262,NULL,'Pierre Legrand','Pierre','Legrand','1995-04-11','DEF','Burkina Faso',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(263,NULL,'Zinedine Girard','Zinedine','Girard','2004-12-27','MID','Morocco',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(264,NULL,'Romain Robert','Romain','Robert','1997-01-27','FWD','Spain',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(265,NULL,'Pierre Girard','Pierre','Girard','1999-02-09','DEF','France',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(266,NULL,'Thomas François','Thomas','François','1992-06-19','MID','Portugal',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(267,NULL,'Romain Fournier','Romain','Fournier','1996-07-31','MID','Belgium',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(268,NULL,'Olivier Roux','Olivier','Roux','2006-11-05','GK','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(269,NULL,'Thomas Dupont','Thomas','Dupont','1998-10-04','GK','Croatia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(270,NULL,'Adrien Lefèvre','Adrien','Lefèvre','1996-02-20','GK','Belgium',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(271,NULL,'Fabien André','Fabien','André','1990-10-07','GK','Morocco',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(272,NULL,'Benoît Garnier','Benoît','Garnier','2006-02-19','DEF','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(273,NULL,'Ibrahim Martin','Ibrahim','Martin','2006-10-23','MID','Netherlands',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(274,NULL,'Corentin David','Corentin','David','1993-04-21','GK','France',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(275,NULL,'Benoît Bernard','Benoît','Bernard','1994-11-26','DEF','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(276,NULL,'William Simon','William','Simon','2003-05-11','GK','Argentina',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(277,NULL,'Édouard Martin','Édouard','Martin','2006-01-20','FWD','Ivory Coast',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(278,NULL,'Benoît Petit','Benoît','Petit','2002-03-26','FWD','Mali',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(279,NULL,'Adrien Dubois','Adrien','Dubois','2006-05-28','MID','Serbia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(280,NULL,'Édouard Garnier','Édouard','Garnier','1993-04-12','FWD','Burkina Faso',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(281,NULL,'Gabriel Laurent','Gabriel','Laurent','2000-05-15','GK','Germany',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(282,NULL,'Vincent Moreau','Vincent','Moreau','1994-04-03','DEF','Spain',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(283,NULL,'Adrien Petit','Adrien','Petit','2005-01-27','MID','Algeria',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(284,NULL,'Baptiste Petit','Baptiste','Petit','1990-07-27','FWD','Poland',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(285,NULL,'Alexandre Bonnet','Alexandre','Bonnet','1990-01-13','FWD','Germany',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(286,NULL,'Hugo Mercier','Hugo','Mercier','1990-12-02','DEF','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(287,NULL,'Benoît Simon','Benoît','Simon','2001-03-08','MID','Ivory Coast',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(288,NULL,'Adrien Martinez','Adrien','Martinez','2005-05-26','MID','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(289,NULL,'Quentin Leroy','Quentin','Leroy','1992-09-30','DEF','Burkina Faso',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(290,NULL,'Ibrahim Richard','Ibrahim','Richard','2005-05-23','DEF','Italy',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(291,NULL,'Zinedine Mercier','Zinedine','Mercier','2004-11-23','FWD','Argentina',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(292,NULL,'Quentin Lefebvre','Quentin','Lefebvre','1990-10-01','MID','Morocco',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(293,NULL,'Baptiste David','Baptiste','David','1994-07-29','MID','Serbia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(294,NULL,'Thomas Michel','Thomas','Michel','2005-03-07','MID','Tunisia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(295,NULL,'Yann François','Yann','François','2000-12-08','GK','Algeria',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(296,NULL,'Pierre Robert','Pierre','Robert','1992-01-18','MID','Senegal',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(297,NULL,'Nicolas Bonnet','Nicolas','Bonnet','1996-07-31','GK','Argentina',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(298,NULL,'Gabriel Mercier','Gabriel','Mercier','1997-06-17','GK','Poland',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(299,NULL,'Fabien Michel','Fabien','Michel','1995-08-03','GK','Portugal',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(300,NULL,'Sébastien Bertrand','Sébastien','Bertrand','1992-04-09','GK','Burkina Faso',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(301,NULL,'Nicolas Moreau','Nicolas','Moreau','1990-01-14','FWD','Brazil',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(302,NULL,'Romain Martin','Romain','Martin','2005-06-18','FWD','Tunisia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(303,NULL,'Damien Leroy','Damien','Leroy','1995-11-01','DEF','Serbia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(304,NULL,'Édouard Legrand','Édouard','Legrand','1990-01-16','GK','Serbia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(305,NULL,'Thomas Laurent','Thomas','Laurent','1989-10-15','FWD','Ivory Coast',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(306,NULL,'Sébastien Petit','Sébastien','Petit','2007-03-05','FWD','Serbia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(307,NULL,'Baptiste François','Baptiste','François','2003-11-05','GK','Poland',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(308,NULL,'Pierre Lefebvre','Pierre','Lefebvre','1991-06-10','DEF','Poland',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(309,NULL,'Édouard Richard','Édouard','Richard','1998-11-29','DEF','Netherlands',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(310,NULL,'Alexandre Lefèvre','Alexandre','Lefèvre','1996-05-09','DEF','Italy',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(311,NULL,'William Laurent','William','Laurent','1997-06-08','MID','Morocco',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(312,NULL,'Damien Mercier','Damien','Mercier','2003-04-19','GK','Tunisia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(313,NULL,'Thomas Bernard','Thomas','Bernard','2006-10-08','DEF','Cameroon',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(314,NULL,'Benoît Moreau','Benoît','Moreau','1999-03-22','MID','Portugal',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(315,NULL,'Olivier David','Olivier','David','1997-08-15','FWD','Netherlands',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(316,NULL,'Nicolas Garnier','Nicolas','Garnier','2006-06-06','MID','France',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(317,NULL,'Fabien Dubois','Fabien','Dubois','2006-04-17','GK','Belgium',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(318,NULL,'Cédric Martin','Cédric','Martin','1991-11-01','DEF','Croatia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(319,NULL,'Sébastien Durand','Sébastien','Durand','2003-11-10','FWD','Croatia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(320,NULL,'Sébastien Martin','Sébastien','Martin','2005-12-18','FWD','Burkina Faso',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(321,NULL,'Olivier Lefebvre','Olivier','Lefebvre','1991-01-15','FWD','Ivory Coast',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(322,NULL,'Benoît André','Benoît','André','1991-02-08','GK','Senegal',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(323,NULL,'David Fournier','David','Fournier','1992-03-11','DEF','Serbia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(324,NULL,'William Lefebvre','William','Lefebvre','1993-08-25','MID','Spain',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(325,NULL,'Adrien Dubois','Adrien','Dubois','1996-12-22','DEF','Mali',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(326,NULL,'Baptiste Lefebvre','Baptiste','Lefebvre','2006-10-21','DEF','Italy',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(327,NULL,'Adrien André','Adrien','André','1999-10-01','FWD','Ivory Coast',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(328,NULL,'Julien Simon','Julien','Simon','1995-07-27','FWD','Serbia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(329,NULL,'Benoît Girard','Benoît','Girard','2001-01-24','FWD','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(330,NULL,'Fabien Lefèvre','Fabien','Lefèvre','1990-03-05','MID','Serbia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(331,NULL,'Lucas Durand','Lucas','Durand','1996-10-30','MID','Croatia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(332,NULL,'Damien Durand','Damien','Durand','2006-12-23','MID','Burkina Faso',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(333,NULL,'David Roux','David','Roux','1989-09-02','DEF','Algeria',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(334,NULL,'William Martin','William','Martin','2005-12-09','MID','Algeria',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(335,NULL,'Olivier André','Olivier','André','2006-08-01','DEF','Argentina',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(336,NULL,'Romain Martin','Romain','Martin','1989-09-05','GK','Croatia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(337,NULL,'Benoît Garnier','Benoît','Garnier','1997-03-06','MID','Burkina Faso',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(338,NULL,'Ibrahim Roux','Ibrahim','Roux','1997-12-30','GK','Croatia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(339,NULL,'Benjamin Lefèvre','Benjamin','Lefèvre','1996-08-11','FWD','Senegal',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(340,NULL,'Gabriel André','Gabriel','André','2006-05-24','MID','Brazil',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(341,NULL,'Julien Bertrand','Julien','Bertrand','1999-05-25','FWD','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(342,NULL,'Romain Mercier','Romain','Mercier','2001-08-15','GK','Argentina',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(343,NULL,'Yann Mercier','Yann','Mercier','2006-05-21','GK','Croatia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(344,NULL,'Fabien Leroy','Fabien','Leroy','2006-11-04','FWD','Netherlands',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(345,NULL,'Édouard Petit','Édouard','Petit','1995-07-25','MID','Algeria',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(346,NULL,'Antoine Durand','Antoine','Durand','2002-12-28','GK','Tunisia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(347,NULL,'Damien Martin','Damien','Martin','1999-07-27','DEF','Serbia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(348,NULL,'Zinedine David','Zinedine','David','2006-12-14','GK','Tunisia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(349,NULL,'Benoît Martinez','Benoît','Martinez','2001-02-14','DEF','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(350,NULL,'Nicolas Dubois','Nicolas','Dubois','1997-09-21','GK','Algeria',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(351,NULL,'Édouard Michel','Édouard','Michel','2005-09-05','DEF','Croatia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(352,NULL,'Romain Leroy','Romain','Leroy','2005-02-27','DEF','Cameroon',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(353,NULL,'Kévin Mercier','Kévin','Mercier','1990-06-20','MID','Portugal',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(354,NULL,'Mathieu Michel','Mathieu','Michel','2006-11-15','GK','Italy',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(355,NULL,'Fabien David','Fabien','David','2007-04-22','MID','Argentina',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(356,NULL,'Lucas Martin','Lucas','Martin','2006-07-26','MID','Italy',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(357,NULL,'Sébastien Mercier','Sébastien','Mercier','2000-08-16','MID','Serbia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(358,NULL,'Damien Martinez','Damien','Martinez','1992-02-03','DEF','Italy',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(359,NULL,'Nicolas Simon','Nicolas','Simon','2007-08-27','FWD','Belgium',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(360,NULL,'Alexandre Michel','Alexandre','Michel','1995-03-05','MID','Tunisia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(361,NULL,'Pierre Petit','Pierre','Petit','1994-02-14','DEF','France',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(362,NULL,'Zinedine Laurent','Zinedine','Laurent','1990-04-21','DEF','Brazil',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(363,NULL,'Nicolas Richard','Nicolas','Richard','2002-06-25','MID','Croatia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(364,NULL,'Baptiste Bonnet','Baptiste','Bonnet','1997-07-07','GK','France',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(365,NULL,'Quentin Martin','Quentin','Martin','1990-06-30','DEF','Morocco',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(366,NULL,'Kévin Garnier','Kévin','Garnier','2003-03-03','FWD','Germany',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(367,NULL,'Alexandre Mercier','Alexandre','Mercier','1990-04-20','MID','Morocco',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(368,NULL,'Cédric Mercier','Cédric','Mercier','2003-05-24','GK','Portugal',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(369,NULL,'Romain Lefebvre','Romain','Lefebvre','1992-05-25','GK','Italy',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(370,NULL,'Benjamin Dupont','Benjamin','Dupont','2002-08-27','GK','Italy',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(371,NULL,'Gabriel Thomas','Gabriel','Thomas','1990-12-12','MID','Spain',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(372,NULL,'Antoine Petit','Antoine','Petit','1996-04-06','DEF','Serbia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(373,NULL,'Gabriel Roux','Gabriel','Roux','2006-08-15','DEF','Argentina',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(374,NULL,'Adrien Dubois','Adrien','Dubois','2004-12-25','MID','Burkina Faso',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(375,NULL,'Gabriel Leroy','Gabriel','Leroy','1996-12-14','DEF','Serbia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(376,NULL,'Nicolas Martinez','Nicolas','Martinez','2006-09-03','DEF','Croatia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(377,NULL,'Lucas Lambert','Lucas','Lambert','1991-06-03','FWD','Argentina',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(378,NULL,'Benjamin Bernard','Benjamin','Bernard','1989-09-13','DEF','Spain',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(379,NULL,'Julien Leroy','Julien','Leroy','1989-12-06','DEF','Burkina Faso',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(380,NULL,'David Richard','David','Richard','1994-06-14','MID','Senegal',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(381,NULL,'Ibrahim Morel','Ibrahim','Morel','2006-12-21','MID','Morocco',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(382,NULL,'Cédric Legrand','Cédric','Legrand','2003-08-31','MID','Brazil',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(383,NULL,'Yann Lambert','Yann','Lambert','1991-04-13','GK','Serbia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(384,NULL,'Antoine Durand','Antoine','Durand','1995-12-04','DEF','Spain',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(385,NULL,'Damien Morel','Damien','Morel','1993-06-24','DEF','Brazil',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(386,NULL,'Nicolas David','Nicolas','David','1994-10-06','FWD','Netherlands',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(387,NULL,'William Bertrand','William','Bertrand','2000-06-02','MID','Italy',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(388,NULL,'Corentin Michel','Corentin','Michel','1990-06-17','FWD','Croatia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(389,NULL,'Pierre Garnier','Pierre','Garnier','2002-06-27','MID','Italy',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(390,NULL,'Nicolas Thomas','Nicolas','Thomas','1998-04-30','DEF','Morocco',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(391,NULL,'Yann Bernard','Yann','Bernard','1994-10-13','GK','Mali',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(392,NULL,'Benoît André','Benoît','André','1993-07-06','GK','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(393,NULL,'Antoine Moreau','Antoine','Moreau','1993-02-10','FWD','Morocco',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(394,NULL,'Yann Robert','Yann','Robert','2001-09-11','DEF','Tunisia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(395,NULL,'Gabriel Martin','Gabriel','Martin','1997-08-28','FWD','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(396,NULL,'Ibrahim Simon','Ibrahim','Simon','2003-12-10','GK','Italy',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(397,NULL,'Benjamin Mercier','Benjamin','Mercier','1999-08-26','FWD','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(398,NULL,'Quentin Laurent','Quentin','Laurent','1994-01-07','DEF','Croatia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(399,NULL,'Pierre Dupont','Pierre','Dupont','1992-04-21','FWD','Ivory Coast',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(400,NULL,'Olivier François','Olivier','François','1995-02-15','GK','Croatia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(401,NULL,'Sébastien Fournier','Sébastien','Fournier','2003-09-19','MID','Portugal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(402,NULL,'Nicolas Bernard','Nicolas','Bernard','2003-12-23','FWD','Croatia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(403,NULL,'Adrien Bertrand','Adrien','Bertrand','1994-02-20','GK','Poland',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(404,NULL,'Quentin Moreau','Quentin','Moreau','2007-04-03','DEF','Serbia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(405,NULL,'Romain André','Romain','André','1993-05-02','DEF','Algeria',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(406,NULL,'Zinedine Morel','Zinedine','Morel','1992-03-03','FWD','Algeria',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(407,NULL,'Olivier David','Olivier','David','1996-12-15','GK','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(408,NULL,'Gabriel Laurent','Gabriel','Laurent','2001-09-14','MID','Belgium',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(409,NULL,'Baptiste Durand','Baptiste','Durand','1993-10-09','MID','Serbia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(410,NULL,'Olivier Legrand','Olivier','Legrand','1992-01-15','MID','Brazil',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(411,NULL,'Baptiste Roux','Baptiste','Roux','2006-03-07','MID','Cameroon',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(412,NULL,'Romain Lefèvre','Romain','Lefèvre','1994-03-16','MID','Cameroon',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(413,NULL,'Hugo Roux','Hugo','Roux','2004-11-22','DEF','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(414,NULL,'David Martin','David','Martin','2002-03-13','GK','Germany',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(415,NULL,'Nicolas Moreau','Nicolas','Moreau','2006-03-11','FWD','Italy',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(416,NULL,'Thomas Roux','Thomas','Roux','2007-04-04','MID','Mali',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(417,NULL,'Corentin Petit','Corentin','Petit','2006-01-12','DEF','Poland',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(418,NULL,'Cédric Bonnet','Cédric','Bonnet','2006-07-23','FWD','Poland',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(419,NULL,'Fabien Durand','Fabien','Durand','1995-05-06','FWD','Portugal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(420,NULL,'Yann Dubois','Yann','Dubois','1999-04-24','MID','Croatia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(421,NULL,'Édouard Garnier','Édouard','Garnier','1992-09-18','GK','Italy',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(422,NULL,'Fabien Simon','Fabien','Simon','1995-04-08','GK','Belgium',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(423,NULL,'Mathieu Martin','Mathieu','Martin','2002-06-13','DEF','Cameroon',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(424,NULL,'Mathieu Petit','Mathieu','Petit','1995-09-03','DEF','Germany',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(425,NULL,'Antoine Martinez','Antoine','Martinez','2007-08-03','DEF','Belgium',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(426,NULL,'Benjamin Legrand','Benjamin','Legrand','2003-08-27','DEF','Mali',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(427,NULL,'Zinedine Lefèvre','Zinedine','Lefèvre','1994-12-25','GK','Cameroon',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(428,NULL,'Nicolas Michel','Nicolas','Michel','1994-08-15','FWD','Morocco',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(429,NULL,'Benoît Bernard','Benoît','Bernard','1998-02-27','DEF','Morocco',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(430,NULL,'Cédric Leroy','Cédric','Leroy','1994-04-05','FWD','Portugal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(431,NULL,'Baptiste Simon','Baptiste','Simon','1996-10-16','DEF','Ivory Coast',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(432,NULL,'Thomas Michel','Thomas','Michel','2001-04-17','MID','Netherlands',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(433,NULL,'Adrien Roux','Adrien','Roux','1998-06-08','GK','Tunisia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(434,NULL,'Thomas Bertrand','Thomas','Bertrand','2001-09-06','FWD','Portugal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(435,NULL,'Antoine Fournier','Antoine','Fournier','1989-12-27','FWD','Portugal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(436,NULL,'Thomas Leroy','Thomas','Leroy','1993-10-31','MID','Cameroon',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(437,NULL,'Nicolas Lefebvre','Nicolas','Lefebvre','1995-10-15','DEF','Belgium',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(438,NULL,'William Michel','William','Michel','2003-02-03','FWD','Tunisia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(439,NULL,'Alexandre Bernard','Alexandre','Bernard','2004-04-25','MID','Argentina',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(440,NULL,'Édouard Durand','Édouard','Durand','1990-08-08','FWD','Tunisia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(441,NULL,'Alexandre Simon','Alexandre','Simon','1994-05-09','DEF','Cameroon',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(442,NULL,'Hugo Laurent','Hugo','Laurent','1991-12-05','DEF','Portugal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(443,NULL,'Romain Mercier','Romain','Mercier','1998-11-24','MID','Italy',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(444,NULL,'Vincent François','Vincent','François','2004-07-26','MID','Spain',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(445,NULL,'Pierre Bertrand','Pierre','Bertrand','1999-04-03','GK','Serbia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(446,NULL,'Quentin Laurent','Quentin','Laurent','1989-12-24','MID','Morocco',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(447,NULL,'Romain Thomas','Romain','Thomas','2002-12-20','FWD','Tunisia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(448,NULL,'William Durand','William','Durand','1996-04-18','GK','Senegal',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(449,NULL,'Benoît André','Benoît','André','2006-09-28','DEF','Mali',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(450,NULL,'Corentin Richard','Corentin','Richard','2000-04-01','MID','Brazil',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(451,NULL,'Antoine David','Antoine','David','2006-04-08','DEF','Portugal',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(452,NULL,'Fabien Martinez','Fabien','Martinez','2000-02-07','FWD','Netherlands',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(453,NULL,'Ibrahim Michel','Ibrahim','Michel','1996-07-12','FWD','Mali',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(454,NULL,'Olivier Dupont','Olivier','Dupont','1993-07-02','DEF','Ivory Coast',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(455,NULL,'Corentin Girard','Corentin','Girard','1990-05-11','GK','Senegal',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(456,NULL,'Adrien Laurent','Adrien','Laurent','1992-05-03','DEF','Netherlands',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(457,NULL,'Ibrahim Richard','Ibrahim','Richard','1997-07-31','MID','Tunisia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(458,NULL,'Baptiste Simon','Baptiste','Simon','1999-09-11','FWD','Croatia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(459,NULL,'Ibrahim Martinez','Ibrahim','Martinez','1999-05-08','FWD','Tunisia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(460,NULL,'Quentin Fournier','Quentin','Fournier','1998-03-10','GK','Brazil',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(461,NULL,'Alexandre Lefèvre','Alexandre','Lefèvre','1991-05-22','GK','France',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(462,NULL,'David Simon','David','Simon','1992-07-03','GK','Serbia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(463,NULL,'Olivier Morel','Olivier','Morel','2005-08-15','MID','Spain',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(464,NULL,'Sébastien Durand','Sébastien','Durand','1995-01-04','DEF','Cameroon',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(465,NULL,'Édouard Thomas','Édouard','Thomas','2000-02-27','GK','Senegal',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(466,NULL,'Yann Bertrand','Yann','Bertrand','2004-02-05','DEF','Serbia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(467,NULL,'Thomas Leroy','Thomas','Leroy','1991-04-08','DEF','Serbia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(468,NULL,'Vincent Bertrand','Vincent','Bertrand','1996-10-23','MID','France',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(469,NULL,'Baptiste Roux','Baptiste','Roux','1991-03-20','GK','Spain',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(470,NULL,'Zinedine Martin','Zinedine','Martin','1999-06-17','GK','Serbia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(471,NULL,'Benjamin Morel','Benjamin','Morel','1994-07-22','GK','Algeria',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(472,NULL,'Quentin Lefebvre','Quentin','Lefebvre','1991-10-24','DEF','Algeria',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(473,NULL,'Baptiste André','Baptiste','André','2007-03-27','MID','Algeria',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(474,NULL,'Thomas Bernard','Thomas','Bernard','1998-07-31','DEF','Algeria',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(475,NULL,'Pierre Laurent','Pierre','Laurent','1996-08-09','MID','Germany',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(476,NULL,'Quentin Fournier','Quentin','Fournier','2004-03-10','DEF','Germany',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(477,NULL,'Quentin André','Quentin','André','1996-12-18','DEF','Netherlands',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(478,NULL,'Zinedine André','Zinedine','André','2000-12-06','MID','Argentina',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(479,NULL,'Baptiste Bonnet','Baptiste','Bonnet','1997-11-03','DEF','Serbia',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(480,NULL,'Kévin Robert','Kévin','Robert','1996-03-10','MID','Spain',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(481,NULL,'Thomas Laurent','Thomas','Laurent','1998-08-20','DEF','Brazil',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(482,NULL,'Kévin Morel','Kévin','Morel','1996-08-18','DEF','Spain',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(483,NULL,'Baptiste Morel','Baptiste','Morel','2001-08-13','MID','Ivory Coast',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(484,NULL,'Zinedine Durand','Zinedine','Durand','1997-06-05','DEF','Ivory Coast',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(485,NULL,'Édouard André','Édouard','André','1994-09-02','GK','Burkina Faso',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(486,NULL,'Yann Martinez','Yann','Martinez','2005-11-02','FWD','Italy',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(487,NULL,'Benjamin Bonnet','Benjamin','Bonnet','1996-09-13','FWD','Burkina Faso',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(488,NULL,'Vincent Roux','Vincent','Roux','2001-03-20','GK','Brazil',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(489,NULL,'Vincent Lefèvre','Vincent','Lefèvre','2003-06-27','DEF','Brazil',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(490,NULL,'Vincent Michel','Vincent','Michel','1995-01-20','GK','Germany',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(491,NULL,'Yann David','Yann','David','2003-09-30','FWD','Cameroon',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(492,NULL,'Adrien Bernard','Adrien','Bernard','2001-05-04','GK','Portugal',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(493,NULL,'Julien Bernard','Julien','Bernard','1992-05-31','DEF','Spain',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(494,NULL,'Cédric Thomas','Cédric','Thomas','1990-04-11','MID','Brazil',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(495,NULL,'Baptiste Robert','Baptiste','Robert','2004-08-19','FWD','Ivory Coast',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(496,NULL,'Mathieu Mercier','Mathieu','Mercier','1992-09-19','MID','Cameroon',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(497,NULL,'Fabien Richard','Fabien','Richard','1996-02-05','GK','Algeria',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(498,NULL,'Gabriel Morel','Gabriel','Morel','1998-01-24','MID','Belgium',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(499,NULL,'Vincent Thomas','Vincent','Thomas','2001-11-30','FWD','Tunisia',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(500,NULL,'Kévin Petit','Kévin','Petit','1994-05-06','DEF','Brazil',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(501,NULL,'Lucas François','Lucas','François','1994-09-02','GK','Spain',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(502,NULL,'Ibrahim Bernard','Ibrahim','Bernard','2001-06-04','FWD','Poland',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(503,NULL,'Benoît Morel','Benoît','Morel','1998-06-03','GK','Ivory Coast',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(504,NULL,'Pierre Girard','Pierre','Girard','1993-08-28','MID','Algeria',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(505,NULL,'Olivier Lefèvre','Olivier','Lefèvre','2002-04-17','GK','Belgium',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(506,NULL,'Yann Garnier','Yann','Garnier','1992-08-11','MID','Poland',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(507,NULL,'Benoît Moreau','Benoît','Moreau','1995-12-21','DEF','Portugal',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(508,NULL,'Fabien Bertrand','Fabien','Bertrand','2002-03-08','MID','Italy',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(509,NULL,'William Thomas','William','Thomas','1991-11-17','MID','Italy',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(510,NULL,'Ibrahim Bernard','Ibrahim','Bernard','1991-06-15','DEF','Italy',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(511,NULL,'Gabriel Martinez','Gabriel','Martinez','1996-01-28','GK','Serbia',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(512,NULL,'Gabriel Bernard','Gabriel','Bernard','1992-06-05','GK','Tunisia',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(513,NULL,'Lucas Bonnet','Lucas','Bonnet','1993-03-11','GK','Croatia',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(514,NULL,'Mathieu Bernard','Mathieu','Bernard','2005-06-18','GK','Cameroon',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(515,NULL,'Damien Petit','Damien','Petit','2003-07-05','FWD','Senegal',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(516,NULL,'Alexandre Morel','Alexandre','Morel','1996-10-11','FWD','Morocco',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(517,NULL,'Corentin Martinez','Corentin','Martinez','2002-02-23','MID','Belgium',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(518,NULL,'Cédric Dupont','Cédric','Dupont','1999-08-16','DEF','Morocco',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(519,NULL,'Gabriel André','Gabriel','André','2006-02-01','DEF','Germany',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(520,NULL,'David Fournier','David','Fournier','1999-05-04','DEF','Brazil',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(521,NULL,'Lucas Leroy','Lucas','Leroy','2000-04-27','FWD','Brazil',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(522,NULL,'Édouard Bertrand','Édouard','Bertrand','1998-02-18','DEF','Mali',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(523,NULL,'Zinedine Martinez','Zinedine','Martinez','1999-09-24','MID','Algeria',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(524,NULL,'Édouard Leroy','Édouard','Leroy','1995-05-24','DEF','Tunisia',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(525,NULL,'Cédric Robert','Cédric','Robert','1994-01-10','FWD','Serbia',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(526,NULL,'Édouard François','Édouard','François','1995-04-24','FWD','Mali',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(527,NULL,'Vincent Robert','Vincent','Robert','1990-12-01','DEF','Belgium',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(528,NULL,'Antoine Bonnet','Antoine','Bonnet','1991-08-19','GK','Belgium',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(529,NULL,'Thomas Lambert','Thomas','Lambert','2002-02-27','FWD','Portugal',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(530,NULL,'Antoine Michel','Antoine','Michel','1996-01-06','MID','Brazil',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(531,NULL,'Thomas Dupont','Thomas','Dupont','1993-09-22','DEF','Argentina',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(532,NULL,'Alexandre Roux','Alexandre','Roux','2003-10-24','MID','Germany',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(533,NULL,'Yann Fournier','Yann','Fournier','1992-06-07','DEF','Italy',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(534,NULL,'Sébastien Lambert','Sébastien','Lambert','1994-09-14','GK','Serbia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(535,NULL,'Antoine Lefebvre','Antoine','Lefebvre','1998-06-05','MID','Ivory Coast',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(536,NULL,'Thomas Martin','Thomas','Martin','1996-01-20','MID','Spain',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(537,NULL,'Mathieu André','Mathieu','André','1993-04-23','GK','Morocco',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(538,NULL,'Corentin Mercier','Corentin','Mercier','2004-08-26','MID','Algeria',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(539,NULL,'Adrien André','Adrien','André','2004-12-30','FWD','Mali',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(540,NULL,'David Mercier','David','Mercier','2002-08-24','GK','Spain',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(541,NULL,'Ibrahim Girard','Ibrahim','Girard','1992-01-08','MID','Cameroon',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(542,NULL,'Pierre Morel','Pierre','Morel','1999-10-05','DEF','Netherlands',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(543,NULL,'Damien Simon','Damien','Simon','1993-09-24','GK','Argentina',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(544,NULL,'Fabien Lambert','Fabien','Lambert','1998-02-06','FWD','Croatia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(545,NULL,'Sébastien Dupont','Sébastien','Dupont','2000-07-02','FWD','Mali',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(546,NULL,'Hugo Mercier','Hugo','Mercier','2004-09-15','DEF','Belgium',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(547,NULL,'Cédric Lambert','Cédric','Lambert','2000-09-01','FWD','Cameroon',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(548,NULL,'Cédric Dupont','Cédric','Dupont','1993-07-30','GK','Tunisia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(549,NULL,'Gabriel Michel','Gabriel','Michel','1990-10-10','DEF','Serbia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(550,NULL,'William André','William','André','2002-03-11','MID','Algeria',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(551,NULL,'Fabien Michel','Fabien','Michel','2006-11-29','DEF','Morocco',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(552,NULL,'Édouard Lefebvre','Édouard','Lefebvre','1999-05-05','MID','Poland',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(553,NULL,'Antoine Durand','Antoine','Durand','1999-02-08','GK','Burkina Faso',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(554,NULL,'Édouard Fournier','Édouard','Fournier','1990-07-26','FWD','Mali',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(555,NULL,'Julien André','Julien','André','2006-03-22','DEF','Burkina Faso',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(556,NULL,'Antoine Robert','Antoine','Robert','1995-12-12','MID','Tunisia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(557,NULL,'Sébastien David','Sébastien','David','2006-01-30','FWD','Mali',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(558,NULL,'Antoine Fournier','Antoine','Fournier','2005-12-07','MID','Croatia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(559,NULL,'Nicolas Legrand','Nicolas','Legrand','2007-06-14','GK','Italy',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(560,NULL,'Mathieu Thomas','Mathieu','Thomas','2004-11-15','GK','Senegal',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(561,NULL,'Corentin Robert','Corentin','Robert','2006-01-19','MID','Italy',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(562,NULL,'Kévin André','Kévin','André','2006-12-10','DEF','Ivory Coast',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(563,NULL,'Nicolas Fournier','Nicolas','Fournier','1994-04-11','FWD','Serbia',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(564,NULL,'Antoine Girard','Antoine','Girard','1996-06-07','DEF','Spain',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(565,NULL,'Thomas Thomas','Thomas','Thomas','1994-10-22','MID','Poland',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(566,NULL,'Vincent Morel','Vincent','Morel','1991-06-13','FWD','France',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(567,NULL,'Yann Bonnet','Yann','Bonnet','1998-09-17','MID','Netherlands',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(568,NULL,'Hugo Richard','Hugo','Richard','1991-05-30','DEF','Germany',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(569,NULL,'Benoît Fournier','Benoît','Fournier','1998-03-18','MID','Burkina Faso',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(570,NULL,'Benjamin Roux','Benjamin','Roux','1991-03-06','FWD','Cameroon',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(571,NULL,'Sébastien Petit','Sébastien','Petit','1995-01-31','GK','Belgium',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(572,NULL,'Adrien Moreau','Adrien','Moreau','1995-12-07','FWD','Italy',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(573,NULL,'Adrien Garnier','Adrien','Garnier','1992-01-29','GK','Spain',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(574,NULL,'Hugo Petit','Hugo','Petit','2006-07-19','FWD','Netherlands',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(575,NULL,'Thomas Petit','Thomas','Petit','1994-12-24','DEF','Portugal',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(576,NULL,'Yann Robert','Yann','Robert','1989-12-25','GK','Morocco',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(577,NULL,'Romain Laurent','Romain','Laurent','1997-03-14','FWD','Italy',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(578,NULL,'Julien Leroy','Julien','Leroy','1991-04-18','MID','Mali',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(579,NULL,'Damien Bernard','Damien','Bernard','2003-03-13','MID','France',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(580,NULL,'Julien Richard','Julien','Richard','2006-08-12','MID','Croatia',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(581,NULL,'Damien Roux','Damien','Roux','1993-05-26','GK','Burkina Faso',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(582,NULL,'Édouard Bertrand','Édouard','Bertrand','1990-05-23','GK','Netherlands',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(583,NULL,'Corentin Legrand','Corentin','Legrand','1992-11-19','DEF','Tunisia',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(584,NULL,'Romain Dubois','Romain','Dubois','1990-06-30','DEF','Ivory Coast',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(585,NULL,'Corentin Richard','Corentin','Richard','2005-01-06','GK','Netherlands',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(586,NULL,'Gabriel David','Gabriel','David','2006-09-23','FWD','Algeria',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(587,NULL,'Lucas Robert','Lucas','Robert','1992-09-18','GK','Italy',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(588,NULL,'Pierre Martin','Pierre','Martin','2001-02-17','FWD','Portugal',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(589,NULL,'Pierre Girard','Pierre','Girard','2005-09-25','FWD','Brazil',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(590,NULL,'Kévin Dupont','Kévin','Dupont','2002-06-04','MID','Tunisia',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(591,NULL,'Adrien Laurent','Adrien','Laurent','1999-08-02','FWD','Italy',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(592,NULL,'Pierre Durand','Pierre','Durand','2003-07-17','MID','Tunisia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(593,NULL,'Alexandre Legrand','Alexandre','Legrand','2000-05-04','DEF','Italy',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(594,NULL,'Antoine Bonnet','Antoine','Bonnet','1992-09-04','DEF','Germany',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(595,NULL,'Kévin Mercier','Kévin','Mercier','2001-03-18','MID','Italy',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(596,NULL,'Lucas Legrand','Lucas','Legrand','1997-02-21','GK','Germany',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(597,NULL,'Fabien Fournier','Fabien','Fournier','1991-08-30','GK','Ivory Coast',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(598,NULL,'Baptiste Martin','Baptiste','Martin','2007-02-06','MID','Morocco',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(599,NULL,'Vincent Leroy','Vincent','Leroy','1991-09-23','MID','Ivory Coast',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(600,NULL,'Pierre Roux','Pierre','Roux','1999-11-01','MID','Spain',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(601,NULL,'Benjamin Richard','Benjamin','Richard','1992-05-04','GK','Italy',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(602,NULL,'Sébastien Michel','Sébastien','Michel','1990-07-18','GK','Serbia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(603,NULL,'Hugo David','Hugo','David','1990-04-06','MID','Brazil',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(604,NULL,'Pierre Robert','Pierre','Robert','1998-11-10','FWD','Netherlands',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(605,NULL,'Quentin Bertrand','Quentin','Bertrand','1991-01-10','DEF','Croatia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(606,NULL,'William Laurent','William','Laurent','2002-03-23','MID','Serbia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(607,NULL,'Julien Moreau','Julien','Moreau','2004-09-14','MID','Germany',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(608,NULL,'Ibrahim Moreau','Ibrahim','Moreau','1992-12-13','FWD','Cameroon',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(609,NULL,'Fabien Simon','Fabien','Simon','1993-02-22','DEF','Netherlands',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(610,NULL,'Hugo Martin','Hugo','Martin','2007-06-23','FWD','Belgium',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(611,NULL,'Thomas Garnier','Thomas','Garnier','2004-01-24','MID','Morocco',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(612,NULL,'Benjamin Lambert','Benjamin','Lambert','2006-12-19','FWD','Tunisia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(613,NULL,'Sébastien André','Sébastien','André','2005-04-27','GK','Mali',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(614,NULL,'Olivier Petit','Olivier','Petit','2006-08-17','FWD','Morocco',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(615,NULL,'Antoine Martinez','Antoine','Martinez','1991-05-20','FWD','Cameroon',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(616,NULL,'Baptiste Roux','Baptiste','Roux','1993-02-27','MID','Serbia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(617,NULL,'David Durand','David','Durand','1993-02-23','GK','Portugal',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(618,NULL,'Édouard François','Édouard','François','2004-10-25','FWD','Italy',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(619,NULL,'Olivier Legrand','Olivier','Legrand','1998-12-06','GK','Germany',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(620,NULL,'Gabriel Durand','Gabriel','Durand','1997-05-31','GK','France',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(621,NULL,'Damien Bonnet','Damien','Bonnet','1996-06-10','FWD','Belgium',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(622,NULL,'Corentin Bertrand','Corentin','Bertrand','2002-09-04','FWD','Ivory Coast',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(623,NULL,'Alexandre Garnier','Alexandre','Garnier','2000-02-14','MID','Netherlands',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(624,NULL,'Vincent David','Vincent','David','1997-02-05','FWD','Argentina',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(625,NULL,'Zinedine Martinez','Zinedine','Martinez','2004-02-26','GK','Morocco',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(626,NULL,'Olivier Lefèvre','Olivier','Lefèvre','2000-03-31','GK','Portugal',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(627,NULL,'Corentin Fournier','Corentin','Fournier','1994-01-27','MID','Serbia',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(628,NULL,'Adrien Dubois','Adrien','Dubois','2006-09-09','MID','Mali',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(629,NULL,'Kévin Lefèvre','Kévin','Lefèvre','2001-04-27','MID','Portugal',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(630,NULL,'Hugo Dupont','Hugo','Dupont','1996-06-04','MID','Brazil',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(631,NULL,'Nicolas Moreau','Nicolas','Moreau','2005-03-23','FWD','Germany',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(632,NULL,'Damien Martin','Damien','Martin','1995-08-08','DEF','Mali',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(633,NULL,'Alexandre Michel','Alexandre','Michel','2006-09-18','GK','Morocco',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(634,NULL,'Mathieu Leroy','Mathieu','Leroy','1993-10-30','FWD','Croatia',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(635,NULL,'Sébastien Thomas','Sébastien','Thomas','1993-06-06','MID','Senegal',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(636,NULL,'Yann Laurent','Yann','Laurent','2004-09-17','MID','Mali',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(637,NULL,'Fabien Martin','Fabien','Martin','2002-03-14','GK','Burkina Faso',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(638,NULL,'Romain Bertrand','Romain','Bertrand','2003-06-21','FWD','Germany',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(639,NULL,'Hugo Thomas','Hugo','Thomas','1994-06-03','MID','Brazil',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(640,NULL,'Ibrahim Girard','Ibrahim','Girard','1990-01-12','DEF','Italy',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(641,NULL,'Vincent Martin','Vincent','Martin','2000-09-10','FWD','Croatia',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(642,NULL,'Édouard Richard','Édouard','Richard','2000-03-31','DEF','Croatia',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(643,NULL,'Lucas Roux','Lucas','Roux','1999-06-03','FWD','Italy',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(644,NULL,'Olivier Fournier','Olivier','Fournier','2005-03-18','GK','Germany',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(645,NULL,'Adrien Lefèvre','Adrien','Lefèvre','2005-03-04','GK','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(646,NULL,'William Bernard','William','Bernard','1992-12-13','GK','Netherlands',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(647,NULL,'Quentin Garnier','Quentin','Garnier','1989-10-09','GK','Cameroon',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(648,NULL,'Romain Simon','Romain','Simon','1998-01-17','MID','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(649,NULL,'Édouard François','Édouard','François','2001-10-08','FWD','Mali',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(650,NULL,'Yann Michel','Yann','Michel','1997-06-08','DEF','France',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(651,NULL,'Sébastien Moreau','Sébastien','Moreau','1998-08-06','GK','Italy',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(652,NULL,'Thomas Leroy','Thomas','Leroy','1991-08-14','MID','Ivory Coast',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(653,NULL,'Quentin Bernard','Quentin','Bernard','2002-07-25','FWD','Portugal',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(654,NULL,'Zinedine David','Zinedine','David','2004-10-14','GK','Belgium',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(655,NULL,'Gabriel Petit','Gabriel','Petit','2002-10-18','DEF','Brazil',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(656,NULL,'William Robert','William','Robert','1994-07-19','MID','Argentina',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(657,NULL,'Thomas Thomas','Thomas','Thomas','2004-10-31','FWD','Netherlands',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(658,NULL,'Cédric Roux','Cédric','Roux','1999-08-31','FWD','Ivory Coast',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(659,NULL,'Baptiste Girard','Baptiste','Girard','2001-11-26','MID','Tunisia',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(660,NULL,'William Lambert','William','Lambert','1990-04-13','MID','Poland',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(661,NULL,'Olivier Garnier','Olivier','Garnier','1992-12-12','MID','Belgium',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(662,NULL,'Sébastien Mercier','Sébastien','Mercier','1995-09-28','DEF','Burkina Faso',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(663,NULL,'Zinedine Richard','Zinedine','Richard','2007-03-04','MID','Portugal',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(664,NULL,'Pierre David','Pierre','David','1997-09-05','MID','Senegal',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(665,NULL,'Thomas Richard','Thomas','Richard','2005-08-03','DEF','Ivory Coast',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(666,NULL,'Gabriel Richard','Gabriel','Richard','1993-02-01','DEF','Portugal',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(667,NULL,'Gabriel Thomas','Gabriel','Thomas','1996-10-22','DEF','Cameroon',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(668,NULL,'Édouard Dubois','Édouard','Dubois','2002-07-15','DEF','Burkina Faso',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(669,NULL,'Adrien Bonnet','Adrien','Bonnet','2004-09-15','MID','Algeria',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(670,NULL,'Corentin David','Corentin','David','1992-09-02','DEF','Morocco',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(671,NULL,'Zinedine Bertrand','Zinedine','Bertrand','1995-05-31','GK','Morocco',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(672,NULL,'Adrien Garnier','Adrien','Garnier','2007-01-02','FWD','Poland',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(673,NULL,'Sébastien Fournier','Sébastien','Fournier','2005-06-22','DEF','Spain',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(674,NULL,'David François','David','François','2007-06-19','FWD','Germany',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(675,NULL,'Cédric Robert','Cédric','Robert','1996-05-18','GK','Burkina Faso',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(676,NULL,'Hugo Lefebvre','Hugo','Lefebvre','1996-11-09','MID','Argentina',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(677,NULL,'Olivier Laurent','Olivier','Laurent','1994-05-20','FWD','Argentina',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(678,NULL,'Hugo Michel','Hugo','Michel','1995-08-13','FWD','Mali',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(679,NULL,'Nicolas Robert','Nicolas','Robert','1993-06-05','FWD','Croatia',6,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(680,NULL,'Nicolas Dubois','Nicolas','Dubois','1995-09-02','GK','Italy',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(681,NULL,'Antoine Martin','Antoine','Martin','1993-03-18','FWD','Serbia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(682,NULL,'Cédric David','Cédric','David','1995-04-29','DEF','Serbia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(683,NULL,'Olivier Girard','Olivier','Girard','1990-12-27','MID','Morocco',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(684,NULL,'Fabien Michel','Fabien','Michel','1999-10-18','DEF','Croatia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(685,NULL,'Olivier Michel','Olivier','Michel','2005-02-19','DEF','Ivory Coast',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(686,NULL,'William Morel','William','Morel','2003-11-11','MID','Netherlands',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(687,NULL,'Adrien Michel','Adrien','Michel','1991-02-25','FWD','Poland',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(688,NULL,'Kévin Bertrand','Kévin','Bertrand','1993-01-12','FWD','Spain',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(689,NULL,'Pierre Moreau','Pierre','Moreau','2002-03-22','FWD','Belgium',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(690,NULL,'Kévin Roux','Kévin','Roux','2000-09-08','FWD','Serbia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(691,NULL,'Mathieu Leroy','Mathieu','Leroy','1991-03-12','GK','Algeria',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(692,NULL,'Sébastien Moreau','Sébastien','Moreau','1994-05-06','DEF','Tunisia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(693,NULL,'Mathieu Dupont','Mathieu','Dupont','1990-01-17','DEF','Belgium',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(694,NULL,'Kévin Bernard','Kévin','Bernard','1990-06-30','MID','Germany',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(695,NULL,'Édouard Bonnet','Édouard','Bonnet','1993-04-18','FWD','France',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(696,NULL,'Mathieu Mercier','Mathieu','Mercier','1998-03-26','GK','France',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(697,NULL,'Cédric Bernard','Cédric','Bernard','2003-07-03','FWD','Argentina',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(698,NULL,'Lucas Dupont','Lucas','Dupont','2003-04-02','DEF','Tunisia',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(699,NULL,'Édouard David','Édouard','David','1991-10-04','MID','France',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(700,NULL,'Pierre Dubois','Pierre','Dubois','2004-05-14','MID','Mali',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(701,NULL,'Kévin Roux','Kévin','Roux','1994-11-11','DEF','Serbia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(702,NULL,'Gabriel Dupont','Gabriel','Dupont','2002-07-31','MID','Serbia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(703,NULL,'Nicolas André','Nicolas','André','1994-08-26','FWD','Argentina',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(704,NULL,'Mathieu Lefèvre','Mathieu','Lefèvre','1998-07-12','DEF','Spain',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(705,NULL,'Édouard Roux','Édouard','Roux','1992-03-05','GK','Ivory Coast',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(706,NULL,'Adrien Girard','Adrien','Girard','2003-09-12','MID','Senegal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(707,NULL,'Nicolas Martin','Nicolas','Martin','2007-03-19','FWD','Italy',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(708,NULL,'Sébastien Dupont','Sébastien','Dupont','1998-05-23','DEF','Mali',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(709,NULL,'Lucas Thomas','Lucas','Thomas','2001-12-22','GK','Argentina',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(710,NULL,'Quentin Bonnet','Quentin','Bonnet','2001-02-07','MID','Italy',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(711,NULL,'Adrien Fournier','Adrien','Fournier','1997-01-12','GK','Germany',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(712,NULL,'William Durand','William','Durand','1991-04-13','MID','Morocco',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(713,NULL,'Ibrahim Richard','Ibrahim','Richard','1994-04-09','MID','Burkina Faso',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(714,NULL,'Mathieu Dupont','Mathieu','Dupont','2001-09-07','GK','Ivory Coast',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(715,NULL,'Baptiste Girard','Baptiste','Girard','1992-06-28','FWD','Cameroon',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(716,NULL,'Romain Leroy','Romain','Leroy','1994-09-20','MID','Brazil',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(717,NULL,'Damien Mercier','Damien','Mercier','1992-06-09','DEF','Burkina Faso',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(718,NULL,'Vincent Durand','Vincent','Durand','1994-12-21','MID','Morocco',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(719,NULL,'Damien Lambert','Damien','Lambert','1995-06-23','DEF','Senegal',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(720,NULL,'Olivier Bernard','Olivier','Bernard','2004-03-09','FWD','Italy',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(721,NULL,'Kévin Lefebvre','Kévin','Lefebvre','2004-08-19','MID','Poland',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(722,NULL,'Hugo Bertrand','Hugo','Bertrand','1999-08-09','GK','Ivory Coast',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(723,NULL,'Romain Bertrand','Romain','Bertrand','1991-07-12','GK','Argentina',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(724,NULL,'Édouard Bonnet','Édouard','Bonnet','1998-04-24','FWD','Ivory Coast',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(725,NULL,'Ibrahim Garnier','Ibrahim','Garnier','2004-09-08','FWD','Senegal',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(726,NULL,'Antoine Robert','Antoine','Robert','1992-10-23','FWD','Poland',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(727,NULL,'Ibrahim Legrand','Ibrahim','Legrand','1998-07-07','FWD','Morocco',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(728,NULL,'Zinedine Simon','Zinedine','Simon','2001-08-11','MID','Belgium',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(729,NULL,'Vincent Petit','Vincent','Petit','1992-11-15','GK','Belgium',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(730,NULL,'Quentin Moreau','Quentin','Moreau','1992-11-30','DEF','Serbia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(731,NULL,'Nicolas Laurent','Nicolas','Laurent','2007-08-04','DEF','Poland',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(732,NULL,'Édouard Martin','Édouard','Martin','1991-03-22','DEF','Serbia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(733,NULL,'Vincent Bertrand','Vincent','Bertrand','2000-02-06','GK','Serbia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(734,NULL,'Pierre François','Pierre','François','1991-10-27','GK','Tunisia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(735,NULL,'Nicolas Thomas','Nicolas','Thomas','2002-03-26','DEF','Poland',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(736,NULL,'Julien Robert','Julien','Robert','2006-08-30','FWD','Mali',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(737,NULL,'Damien Bertrand','Damien','Bertrand','2001-10-20','DEF','Cameroon',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(738,NULL,'Benoît Michel','Benoît','Michel','1992-01-09','FWD','Portugal',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(739,NULL,'Mathieu Dubois','Mathieu','Dubois','1990-11-01','MID','Croatia',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(740,NULL,'Corentin Dubois','Corentin','Dubois','1991-06-24','FWD','Tunisia',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(741,NULL,'Lucas Fournier','Lucas','Fournier','1993-04-02','DEF','France',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(742,NULL,'Corentin Mercier','Corentin','Mercier','2003-08-12','FWD','Mali',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(743,NULL,'Yann Bonnet','Yann','Bonnet','2007-03-05','MID','Tunisia',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(744,NULL,'Corentin Dupont','Corentin','Dupont','1998-11-18','GK','Italy',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(745,NULL,'Adrien Bernard','Adrien','Bernard','1991-07-25','GK','Ivory Coast',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(746,NULL,'Vincent Martinez','Vincent','Martinez','2007-04-10','GK','Mali',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(747,NULL,'Vincent André','Vincent','André','2006-09-08','MID','Burkina Faso',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(748,NULL,'Ibrahim Mercier','Ibrahim','Mercier','1993-04-24','GK','Senegal',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(749,NULL,'Lucas Moreau','Lucas','Moreau','2006-10-13','DEF','Burkina Faso',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(750,NULL,'Nicolas Richard','Nicolas','Richard','2006-08-19','MID','Ivory Coast',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(751,NULL,'Mathieu André','Mathieu','André','1995-11-24','MID','Portugal',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(752,NULL,'Benoît Bernard','Benoît','Bernard','2006-05-25','DEF','Spain',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(753,NULL,'Adrien Michel','Adrien','Michel','2001-04-25','GK','Argentina',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(754,NULL,'Lucas Garnier','Lucas','Garnier','1994-12-04','GK','Belgium',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(755,NULL,'Nicolas Bertrand','Nicolas','Bertrand','2006-12-24','MID','Italy',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(756,NULL,'Hugo Laurent','Hugo','Laurent','2006-12-02','GK','Croatia',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(757,NULL,'Hugo Robert','Hugo','Robert','2005-04-12','MID','Burkina Faso',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(758,NULL,'Cédric Roux','Cédric','Roux','2004-02-15','GK','Ivory Coast',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(759,NULL,'Damien Garnier','Damien','Garnier','1996-07-17','FWD','Brazil',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(760,NULL,'Vincent Laurent','Vincent','Laurent','2005-09-03','FWD','Argentina',10,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(761,NULL,'Pierre Dupont','Pierre','Dupont','2006-05-18','MID','Mali',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(762,NULL,'Lucas Durand','Lucas','Durand','1994-01-12','MID','Algeria',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(763,NULL,'Julien Robert','Julien','Robert','1998-06-29','GK','Italy',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(764,NULL,'Benoît Durand','Benoît','Durand','2003-03-14','MID','Portugal',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(765,NULL,'Benjamin Bonnet','Benjamin','Bonnet','1993-12-23','MID','Portugal',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(766,NULL,'Édouard Bertrand','Édouard','Bertrand','1996-07-05','GK','Burkina Faso',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(767,NULL,'Fabien Fournier','Fabien','Fournier','2000-08-18','GK','Netherlands',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(768,NULL,'William Moreau','William','Moreau','1993-12-23','MID','Croatia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(769,NULL,'Édouard Fournier','Édouard','Fournier','1993-12-01','MID','Netherlands',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(770,NULL,'Mathieu Mercier','Mathieu','Mercier','2004-03-30','DEF','Argentina',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(771,NULL,'Alexandre Legrand','Alexandre','Legrand','1996-10-16','FWD','Burkina Faso',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(772,NULL,'Corentin Robert','Corentin','Robert','1996-04-26','FWD','Cameroon',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(773,NULL,'Corentin Fournier','Corentin','Fournier','1990-05-13','MID','Brazil',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(774,NULL,'Gabriel Dubois','Gabriel','Dubois','2001-04-25','FWD','Tunisia',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(775,NULL,'Hugo Martinez','Hugo','Martinez','1996-06-06','FWD','Mali',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(776,NULL,'Adrien Roux','Adrien','Roux','1993-11-28','MID','Spain',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(777,NULL,'Quentin Martinez','Quentin','Martinez','2002-01-22','MID','Argentina',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(778,NULL,'Hugo Moreau','Hugo','Moreau','1995-07-11','GK','Mali',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(779,NULL,'Gabriel Mercier','Gabriel','Mercier','1998-11-16','FWD','Brazil',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(780,NULL,'Hugo Bonnet','Hugo','Bonnet','2007-01-08','MID','Burkina Faso',11,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(781,NULL,'Lucas Petit','Lucas','Petit','2003-05-01','MID','Poland',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(782,NULL,'Nicolas Leroy','Nicolas','Leroy','1994-01-15','GK','Poland',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(783,NULL,'Olivier Laurent','Olivier','Laurent','1991-08-26','FWD','Spain',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(784,NULL,'Sébastien Martinez','Sébastien','Martinez','1989-12-28','MID','Serbia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(785,NULL,'Olivier Martinez','Olivier','Martinez','1995-08-11','GK','Spain',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(786,NULL,'Yann Martin','Yann','Martin','2002-08-01','GK','Germany',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(787,NULL,'William Martin','William','Martin','1999-11-28','FWD','Serbia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(788,NULL,'Nicolas Petit','Nicolas','Petit','1993-05-10','MID','Serbia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(789,NULL,'Vincent Lefèvre','Vincent','Lefèvre','1992-10-10','FWD','Senegal',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(790,NULL,'Sébastien Morel','Sébastien','Morel','2007-06-26','FWD','Croatia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(791,NULL,'Benjamin Legrand','Benjamin','Legrand','2005-06-19','FWD','Argentina',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(792,NULL,'William Legrand','William','Legrand','2001-03-18','DEF','Spain',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(793,NULL,'Olivier André','Olivier','André','1992-01-31','GK','France',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(794,NULL,'William Lefèvre','William','Lefèvre','1999-08-29','DEF','Morocco',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(795,NULL,'Romain Richard','Romain','Richard','2001-06-27','GK','Italy',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(796,NULL,'Julien Durand','Julien','Durand','2007-01-10','FWD','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:35','2025-09-02 18:16:35'),(797,NULL,'Baptiste Leroy','Baptiste','Leroy','1991-03-08','FWD','Algeria',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(798,NULL,'Hugo Moreau','Hugo','Moreau','2003-06-27','MID','Burkina Faso',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(799,NULL,'Édouard Lefèvre','Édouard','Lefèvre','2004-11-05','GK','Tunisia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(800,NULL,'Sébastien Bonnet','Sébastien','Bonnet','2005-01-01','DEF','Croatia',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(801,NULL,'Édouard Martinez','Édouard','Martinez','1996-11-29','MID','Germany',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(802,NULL,'Sébastien Laurent','Sébastien','Laurent','2005-02-21','GK','Argentina',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(803,NULL,'Damien Simon','Damien','Simon','2005-03-02','MID','Cameroon',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(804,NULL,'Gabriel David','Gabriel','David','1998-06-01','MID','Mali',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(805,NULL,'Kévin Roux','Kévin','Roux','2004-11-14','MID','Ivory Coast',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(806,NULL,'Adrien David','Adrien','David','2005-07-10','MID','Algeria',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(807,NULL,'Édouard Robert','Édouard','Robert','2004-08-16','MID','Netherlands',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(808,NULL,'Lucas Roux','Lucas','Roux','1997-11-11','DEF','Burkina Faso',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(809,NULL,'Zinedine Roux','Zinedine','Roux','2006-07-31','DEF','Serbia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(810,NULL,'Lucas Mercier','Lucas','Mercier','1998-03-01','DEF','Germany',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(811,NULL,'Olivier Bertrand','Olivier','Bertrand','2006-02-18','FWD','Belgium',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(812,NULL,'Gabriel Simon','Gabriel','Simon','2001-12-21','FWD','Morocco',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(813,NULL,'Olivier Petit','Olivier','Petit','2004-02-29','MID','Poland',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(814,NULL,'Nicolas Thomas','Nicolas','Thomas','2005-11-18','GK','Burkina Faso',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(815,NULL,'Damien Dubois','Damien','Dubois','1991-05-01','DEF','Senegal',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(816,NULL,'Julien Lambert','Julien','Lambert','1997-03-08','GK','Croatia',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(817,NULL,'Romain Leroy','Romain','Leroy','2004-07-28','FWD','Germany',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(818,NULL,'Olivier Fournier','Olivier','Fournier','2003-06-01','GK','Ivory Coast',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(819,NULL,'Damien Petit','Damien','Petit','1993-07-21','FWD','Spain',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(820,NULL,'Alexandre Simon','Alexandre','Simon','2001-09-17','FWD','Ivory Coast',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(821,NULL,'Damien Dupont','Damien','Dupont','2002-09-01','FWD','Tunisia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(822,NULL,'Gabriel Simon','Gabriel','Simon','2005-02-23','DEF','Spain',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(823,NULL,'Hugo David','Hugo','David','2005-03-20','GK','Brazil',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(824,NULL,'Gabriel Laurent','Gabriel','Laurent','1989-11-13','DEF','Mali',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(825,NULL,'Olivier Michel','Olivier','Michel','1990-09-20','GK','Portugal',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(826,NULL,'Corentin Bertrand','Corentin','Bertrand','1996-01-14','MID','Morocco',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(827,NULL,'Romain Martinez','Romain','Martinez','2003-05-06','MID','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(828,NULL,'Romain Morel','Romain','Morel','1999-11-28','FWD','Serbia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(829,NULL,'Hugo Morel','Hugo','Morel','1992-03-06','FWD','Argentina',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(830,NULL,'Mathieu Roux','Mathieu','Roux','1999-12-25','GK','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(831,NULL,'Benjamin Garnier','Benjamin','Garnier','1991-03-11','DEF','France',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(832,NULL,'Lucas Bonnet','Lucas','Bonnet','2007-08-23','DEF','Italy',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(833,NULL,'Alexandre Fournier','Alexandre','Fournier','1990-01-27','FWD','Brazil',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(834,NULL,'Damien Legrand','Damien','Legrand','2005-02-01','FWD','Poland',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(835,NULL,'Gabriel David','Gabriel','David','2007-05-26','MID','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(836,NULL,'Gabriel Martin','Gabriel','Martin','1989-09-16','FWD','Croatia',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(837,NULL,'Cédric Roux','Cédric','Roux','2006-07-08','FWD','Germany',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(838,NULL,'Vincent David','Vincent','David','1996-07-05','DEF','Cameroon',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(839,NULL,'Thomas Dupont','Thomas','Dupont','2004-08-23','DEF','France',14,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(840,NULL,'Corentin Morel','Corentin','Morel','1999-08-03','DEF','Cameroon',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(841,NULL,'Benjamin Girard','Benjamin','Girard','2007-07-23','GK','Poland',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(842,NULL,'Antoine Dupont','Antoine','Dupont','1997-02-04','MID','Algeria',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(843,NULL,'Fabien Bernard','Fabien','Bernard','2001-05-05','MID','Belgium',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(844,NULL,'Adrien Michel','Adrien','Michel','2007-06-01','DEF','Burkina Faso',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(845,NULL,'David Lefèvre','David','Lefèvre','2003-08-08','FWD','Tunisia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(846,NULL,'Quentin Legrand','Quentin','Legrand','2001-01-03','GK','Cameroon',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(847,NULL,'Benoît Mercier','Benoît','Mercier','1991-10-16','FWD','Cameroon',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(848,NULL,'Vincent Lefèvre','Vincent','Lefèvre','2000-05-11','DEF','Spain',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(849,NULL,'William Bonnet','William','Bonnet','2007-05-19','FWD','Croatia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(850,NULL,'Mathieu Durand','Mathieu','Durand','1999-10-14','MID','Argentina',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(851,NULL,'Benoît Petit','Benoît','Petit','2004-12-28','MID','Argentina',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(852,NULL,'Zinedine Thomas','Zinedine','Thomas','1998-09-21','MID','Senegal',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(853,NULL,'Damien Dubois','Damien','Dubois','1993-10-21','FWD','Burkina Faso',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(854,NULL,'Gabriel Petit','Gabriel','Petit','1991-07-16','DEF','Algeria',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(855,NULL,'Nicolas Lefebvre','Nicolas','Lefebvre','2002-10-31','GK','Poland',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(856,NULL,'Mathieu Garnier','Mathieu','Garnier','1996-02-05','MID','Serbia',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(857,NULL,'Mathieu Thomas','Mathieu','Thomas','1993-05-11','MID','Algeria',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(858,NULL,'Gabriel Mercier','Gabriel','Mercier','1990-06-05','FWD','France',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(859,NULL,'Yann Roux','Yann','Roux','1993-04-18','DEF','Belgium',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(860,NULL,'Sébastien Martin','Sébastien','Martin','2004-08-23','MID','Burkina Faso',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(861,NULL,'Zinedine Lambert','Zinedine','Lambert','2004-03-25','FWD','Brazil',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(862,NULL,'Olivier André','Olivier','André','2005-09-04','FWD','Netherlands',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(863,NULL,'Olivier Dubois','Olivier','Dubois','2005-09-03','GK','Morocco',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(864,NULL,'Hugo Girard','Hugo','Girard','2005-03-03','DEF','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(865,NULL,'Fabien Richard','Fabien','Richard','1995-11-08','DEF','Senegal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(866,NULL,'Gabriel Simon','Gabriel','Simon','1990-04-25','GK','Brazil',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(867,NULL,'David Lambert','David','Lambert','1994-12-24','FWD','Germany',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(868,NULL,'Vincent Laurent','Vincent','Laurent','1995-02-28','MID','Burkina Faso',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(869,NULL,'Nicolas Robert','Nicolas','Robert','1993-05-04','GK','Serbia',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(870,NULL,'Benjamin Martinez','Benjamin','Martinez','1990-07-01','DEF','Germany',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(871,NULL,'Mathieu Lefèvre','Mathieu','Lefèvre','2002-02-28','DEF','Italy',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(872,NULL,'Pierre Mercier','Pierre','Mercier','1995-01-05','MID','Netherlands',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(873,NULL,'Olivier Richard','Olivier','Richard','2006-03-19','GK','Portugal',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(874,NULL,'David Robert','David','Robert','1999-04-03','FWD','Morocco',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(875,NULL,'Sébastien Thomas','Sébastien','Thomas','2002-04-14','DEF','Mali',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(876,NULL,'Gabriel Bernard','Gabriel','Bernard','1992-12-14','MID','Belgium',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(877,NULL,'Antoine Bernard','Antoine','Bernard','1993-04-20','FWD','Argentina',16,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(878,NULL,'Gabriel Bonnet','Gabriel','Bonnet','2003-08-14','MID','Mali',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(879,NULL,'Yann Morel','Yann','Morel','2004-12-20','GK','Belgium',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(880,NULL,'Fabien André','Fabien','André','1989-11-12','GK','Burkina Faso',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(881,NULL,'Alexandre Bonnet','Alexandre','Bonnet','2003-07-24','GK','Germany',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(882,NULL,'Hugo Morel','Hugo','Morel','1996-07-31','MID','Netherlands',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(883,NULL,'Thomas Thomas','Thomas','Thomas','2001-07-16','FWD','Cameroon',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(884,NULL,'Olivier Mercier','Olivier','Mercier','1994-04-16','FWD','Burkina Faso',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(885,NULL,'Nicolas Roux','Nicolas','Roux','1993-11-16','FWD','Portugal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(886,NULL,'Alexandre Morel','Alexandre','Morel','1998-10-21','DEF','Senegal',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(887,NULL,'Romain Fournier','Romain','Fournier','2001-09-12','GK','Serbia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(888,NULL,'Édouard Moreau','Édouard','Moreau','1996-07-14','MID','Cameroon',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(889,NULL,'Ibrahim Morel','Ibrahim','Morel','2006-10-26','FWD','Belgium',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(890,NULL,'Fabien Bernard','Fabien','Bernard','2007-03-03','DEF','Burkina Faso',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(891,NULL,'Quentin Lefebvre','Quentin','Lefebvre','2004-09-07','DEF','Mali',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(892,NULL,'Olivier Durand','Olivier','Durand','2007-04-19','DEF','Croatia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(893,NULL,'Fabien Thomas','Fabien','Thomas','1989-11-12','GK','Spain',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(894,NULL,'Quentin André','Quentin','André','2007-01-14','FWD','Ivory Coast',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(895,NULL,'Vincent André','Vincent','André','2005-04-08','GK','France',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(896,NULL,'Sébastien Leroy','Sébastien','Leroy','1999-10-21','DEF','Tunisia',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(897,NULL,'William Garnier','William','Garnier','2006-06-11','FWD','Cameroon',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(898,NULL,'Julien Laurent','Julien','Laurent','1998-09-16','MID','Netherlands',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(899,NULL,'Benoît Bernard','Benoît','Bernard','2005-08-30','MID','Germany',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(900,NULL,'Olivier Legrand','Olivier','Legrand','2001-07-12','FWD','Belgium',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(901,NULL,'Antoine Michel','Antoine','Michel','1995-11-22','MID','Argentina',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(902,NULL,'Kévin Fournier','Kévin','Fournier','2004-09-11','FWD','Poland',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(903,NULL,'Mathieu Michel','Mathieu','Michel','2000-06-22','FWD','France',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(904,NULL,'Romain Petit','Romain','Petit','1997-04-24','DEF','Croatia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(905,NULL,'Cédric Michel','Cédric','Michel','1994-10-06','MID','Belgium',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(906,NULL,'Benoît Richard','Benoît','Richard','1990-07-17','MID','Senegal',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(907,NULL,'Hugo Martin','Hugo','Martin','2006-08-30','GK','Brazil',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(908,NULL,'Julien Lambert','Julien','Lambert','1990-03-25','MID','Belgium',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(909,NULL,'Nicolas Mercier','Nicolas','Mercier','1991-09-12','GK','Portugal',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(910,NULL,'Baptiste Martinez','Baptiste','Martinez','2004-03-25','MID','Mali',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(911,NULL,'Romain Lambert','Romain','Lambert','2007-06-13','MID','France',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(912,NULL,'Baptiste Dupont','Baptiste','Dupont','2006-06-16','MID','Brazil',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(913,NULL,'Vincent Durand','Vincent','Durand','2004-11-21','FWD','Poland',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(914,NULL,'Baptiste Bonnet','Baptiste','Bonnet','1996-03-07','GK','Serbia',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(915,NULL,'Thomas David','Thomas','David','1990-04-20','FWD','Spain',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(916,NULL,'Romain Fournier','Romain','Fournier','1994-09-10','DEF','Germany',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(917,NULL,'Romain André','Romain','André','1999-10-08','GK','Portugal',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(918,NULL,'Fabien Petit','Fabien','Petit','2002-08-07','GK','Italy',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(919,NULL,'William Girard','William','Girard','2007-07-06','FWD','France',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(920,NULL,'Alexandre Bernard','Alexandre','Bernard','2007-01-31','DEF','Germany',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(921,NULL,'David Dubois','David','Dubois','1996-10-05','FWD','Netherlands',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(922,NULL,'Corentin Laurent','Corentin','Laurent','2001-05-17','DEF','Belgium',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(923,NULL,'Quentin Lefebvre','Quentin','Lefebvre','2001-03-20','MID','Serbia',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(924,NULL,'Gabriel David','Gabriel','David','2001-02-13','FWD','Senegal',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(925,NULL,'David Simon','David','Simon','2003-07-09','GK','Senegal',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(926,NULL,'Kévin Lefèvre','Kévin','Lefèvre','1995-11-24','MID','Ivory Coast',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(927,NULL,'Gabriel Richard','Gabriel','Richard','2001-03-31','DEF','Mali',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(928,NULL,'Julien Bonnet','Julien','Bonnet','2007-08-24','DEF','Belgium',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(929,NULL,'Thomas Petit','Thomas','Petit','2002-08-31','MID','Germany',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(930,NULL,'Nicolas Roux','Nicolas','Roux','1994-05-31','GK','Serbia',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(931,NULL,'Thomas David','Thomas','David','2005-06-20','DEF','Brazil',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(932,NULL,'Yann Leroy','Yann','Leroy','2006-01-12','MID','Serbia',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(933,NULL,'Benoît Fournier','Benoît','Fournier','1990-09-30','MID','Belgium',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(934,NULL,'Mathieu Moreau','Mathieu','Moreau','1998-08-20','GK','France',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(935,NULL,'Corentin Laurent','Corentin','Laurent','1990-04-11','DEF','Netherlands',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(936,NULL,'Mathieu Mercier','Mathieu','Mercier','1991-11-30','DEF','Cameroon',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(937,NULL,'Pierre Michel','Pierre','Michel','2006-09-08','DEF','Algeria',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(938,NULL,'Antoine Leroy','Antoine','Leroy','1999-01-23','GK','Brazil',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(939,NULL,'Hugo Lambert','Hugo','Lambert','1999-07-04','GK','Algeria',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(940,NULL,'Sébastien Bertrand','Sébastien','Bertrand','2003-11-27','GK','Belgium',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(941,NULL,'Pierre Lambert','Pierre','Lambert','1998-05-20','GK','Morocco',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(942,NULL,'Adrien Richard','Adrien','Richard','2002-04-09','DEF','Cameroon',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(943,NULL,'William Dubois','William','Dubois','1993-04-14','GK','Ivory Coast',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(944,NULL,'Yann Girard','Yann','Girard','2000-11-22','DEF','Italy',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(945,NULL,'Romain Morel','Romain','Morel','1998-09-16','GK','Spain',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(946,NULL,'Alexandre Lefebvre','Alexandre','Lefebvre','2006-08-15','FWD','Argentina',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(947,NULL,'Damien Michel','Damien','Michel','2004-01-07','MID','Morocco',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(948,NULL,'Antoine Morel','Antoine','Morel','2002-08-20','FWD','Portugal',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(949,NULL,'Mathieu Simon','Mathieu','Simon','2003-06-30','DEF','Portugal',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(950,NULL,'Corentin Morel','Corentin','Morel','2003-11-30','MID','Brazil',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(951,NULL,'Benjamin Legrand','Benjamin','Legrand','2002-03-01','FWD','Argentina',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(952,NULL,'Cédric Lefebvre','Cédric','Lefebvre','2005-02-10','DEF','Netherlands',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(953,NULL,'Quentin Moreau','Quentin','Moreau','2006-10-13','FWD','Mali',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(954,NULL,'Corentin Girard','Corentin','Girard','1992-02-05','GK','Netherlands',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(955,NULL,'Pierre Leroy','Pierre','Leroy','1995-10-24','GK','Cameroon',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(956,NULL,'Kévin Roux','Kévin','Roux','2000-10-02','FWD','Serbia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(957,NULL,'Quentin Garnier','Quentin','Garnier','2004-10-30','MID','Algeria',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(958,NULL,'Pierre Richard','Pierre','Richard','1991-12-07','GK','Mali',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(959,NULL,'Zinedine Lambert','Zinedine','Lambert','2000-11-14','MID','Ivory Coast',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(960,NULL,'David Dubois','David','Dubois','1997-11-01','GK','Senegal',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(961,NULL,'Lucas Richard','Lucas','Richard','1995-06-09','FWD','Senegal',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(962,NULL,'Gabriel Laurent','Gabriel','Laurent','1997-05-27','FWD','Belgium',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(963,NULL,'Cédric Bernard','Cédric','Bernard','1997-04-15','FWD','Netherlands',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(964,NULL,'Fabien Roux','Fabien','Roux','1997-08-01','FWD','Poland',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(965,NULL,'Adrien Dubois','Adrien','Dubois','2007-01-18','MID','Belgium',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(966,NULL,'Fabien Dupont','Fabien','Dupont','2002-07-02','GK','Italy',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(967,NULL,'Lucas David','Lucas','David','1999-04-17','GK','Senegal',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(968,NULL,'Lucas Fournier','Lucas','Fournier','2005-07-01','FWD','Morocco',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(969,NULL,'Nicolas Lefèvre','Nicolas','Lefèvre','2007-03-27','GK','Italy',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(970,NULL,'Benoît Lambert','Benoît','Lambert','2004-09-14','GK','Netherlands',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(971,NULL,'Thomas Dupont','Thomas','Dupont','1989-09-11','GK','Morocco',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(972,NULL,'Zinedine Morel','Zinedine','Morel','1997-03-30','MID','France',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(973,NULL,'Vincent Dupont','Vincent','Dupont','2007-04-13','DEF','Spain',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(974,NULL,'Édouard Lefebvre','Édouard','Lefebvre','2006-08-08','MID','Senegal',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(975,NULL,'Damien Simon','Damien','Simon','1992-02-05','DEF','Algeria',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(976,NULL,'Julien Garnier','Julien','Garnier','1998-05-06','GK','Poland',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(977,NULL,'Alexandre Garnier','Alexandre','Garnier','2005-12-03','FWD','Argentina',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(978,NULL,'Benjamin Bertrand','Benjamin','Bertrand','2002-05-07','MID','Portugal',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(979,NULL,'Quentin François','Quentin','François','1995-07-13','MID','Mali',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(980,NULL,'Kévin Durand','Kévin','Durand','2004-10-01','FWD','France',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(981,NULL,'Gabriel Durand','Gabriel','Durand','2006-02-26','GK','Poland',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(982,NULL,'Ibrahim Dupont','Ibrahim','Dupont','1990-11-18','MID','Serbia',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(983,NULL,'Yann Martinez','Yann','Martinez','1994-11-04','GK','Algeria',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(984,NULL,'Zinedine Lefèvre','Zinedine','Lefèvre','2006-04-26','MID','Ivory Coast',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(985,NULL,'Ibrahim Bonnet','Ibrahim','Bonnet','1999-08-15','MID','Senegal',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(986,NULL,'Sébastien Laurent','Sébastien','Laurent','1997-05-10','DEF','France',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(987,NULL,'Fabien Dubois','Fabien','Dubois','1994-12-08','FWD','Spain',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(988,NULL,'Corentin Mercier','Corentin','Mercier','2007-03-17','GK','Ivory Coast',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(989,NULL,'Zinedine Martin','Zinedine','Martin','1994-09-03','MID','Belgium',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(990,NULL,'Quentin Martinez','Quentin','Martinez','1999-03-24','DEF','Germany',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(991,NULL,'David Martinez','David','Martinez','1991-07-03','FWD','Portugal',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(992,NULL,'Quentin Morel','Quentin','Morel','2002-08-08','DEF','Tunisia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(993,NULL,'William Michel','William','Michel','2004-10-09','MID','Serbia',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(994,NULL,'Cédric Dupont','Cédric','Dupont','1991-06-13','GK','Poland',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(995,NULL,'Benoît Durand','Benoît','Durand','1993-11-20','DEF','Portugal',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(996,NULL,'Hugo Robert','Hugo','Robert','1998-08-10','MID','Belgium',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(997,NULL,'Vincent Bertrand','Vincent','Bertrand','1994-01-03','DEF','Algeria',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(998,NULL,'Nicolas Michel','Nicolas','Michel','2002-04-19','FWD','Belgium',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(999,NULL,'Adrien Morel','Adrien','Morel','1993-05-14','GK','Cameroon',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1000,NULL,'Thomas Dupont','Thomas','Dupont','1995-07-11','GK','Spain',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1001,NULL,'Quentin Dubois','Quentin','Dubois','2001-10-20','DEF','Spain',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1002,NULL,'Olivier Lefèvre','Olivier','Lefèvre','2000-02-18','GK','Germany',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1003,NULL,'Édouard Leroy','Édouard','Leroy','1998-11-01','GK','Mali',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1004,NULL,'Lucas Lambert','Lucas','Lambert','2007-07-12','MID','Italy',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1005,NULL,'Sébastien Garnier','Sébastien','Garnier','1995-07-18','MID','Belgium',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1006,NULL,'Nicolas Petit','Nicolas','Petit','1993-07-05','FWD','Germany',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1007,NULL,'Thomas François','Thomas','François','2000-11-01','GK','Belgium',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1008,NULL,'Romain Lefèvre','Romain','Lefèvre','1989-10-28','DEF','Senegal',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1009,NULL,'Lucas Michel','Lucas','Michel','1992-10-08','GK','Spain',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1010,NULL,'Antoine Mercier','Antoine','Mercier','1992-08-22','MID','France',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1011,NULL,'Quentin François','Quentin','François','2005-08-04','FWD','Burkina Faso',23,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:16:36','2025-09-02 18:16:36'),(1012,NULL,'William Morel','William','Morel','1994-03-04','FWD','Argentina',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1013,NULL,'Édouard Garnier','Édouard','Garnier','1998-11-13','FWD','Senegal',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1014,NULL,'Ibrahim David','Ibrahim','David','1996-12-16','GK','Algeria',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1015,NULL,'Olivier Lefebvre','Olivier','Lefebvre','2005-08-07','DEF','Portugal',7,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1016,NULL,'Yann André','Yann','André','1998-08-15','DEF','Croatia',8,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1017,NULL,'Fabien Lefèvre','Fabien','Lefèvre','1990-01-09','DEF','Morocco',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1018,NULL,'Romain Michel','Romain','Michel','2004-07-02','FWD','Argentina',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1019,NULL,'Vincent Leroy','Vincent','Leroy','2006-03-03','DEF','Cameroon',12,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1020,NULL,'Antoine Petit','Antoine','Petit','1994-08-13','FWD','Senegal',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1021,NULL,'Sébastien Moreau','Sébastien','Moreau','2000-01-21','FWD','Spain',13,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1022,NULL,'Julien François','Julien','François','1996-01-08','FWD','Italy',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1023,NULL,'Vincent Leroy','Vincent','Leroy','2000-12-11','DEF','Ivory Coast',15,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1024,NULL,'Fabien Girard','Fabien','Girard','1990-09-19','FWD','Germany',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1025,NULL,'Benjamin Martin','Benjamin','Martin','1990-04-23','DEF','Spain',17,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1026,NULL,'Quentin Moreau','Quentin','Moreau','2004-07-01','FWD','Italy',18,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1027,NULL,'Nicolas Girard','Nicolas','Girard','1998-02-21','GK','Netherlands',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1028,NULL,'Sébastien Legrand','Sébastien','Legrand','2003-03-16','MID','Tunisia',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1029,NULL,'Cédric Laurent','Cédric','Laurent','1991-02-22','FWD','Cameroon',19,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1030,NULL,'Corentin François','Corentin','François','2004-11-01','DEF','France',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1031,NULL,'Hugo Lefebvre','Hugo','Lefebvre','2007-08-28','MID','Cameroon',20,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1032,NULL,'Yann Lambert','Yann','Lambert','1989-09-27','GK','Tunisia',21,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03'),(1033,NULL,'Fabien François','Fabien','François','2007-03-03','FWD','Burkina Faso',22,1,NULL,NULL,NULL,NULL,NULL,NULL,'professional',NULL,NULL,NULL,'2025-09-02 18:17:03','2025-09-02 18:17:03');
/*!40000 ALTER TABLE `players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referee_reports`
--

DROP TABLE IF EXISTS `referee_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referee_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `match_id` bigint unsigned NOT NULL,
  `referee_id` bigint unsigned NOT NULL,
  `competition_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_date` datetime NOT NULL,
  `stadium` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `weather` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pitch_condition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_referee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `assistant_referee_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `assistant_referee_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fourth_official` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_referee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avar_referee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_score` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `half_time_score` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_time_minutes` int NOT NULL DEFAULT '0',
  `penalty_shootout` tinyint(1) NOT NULL DEFAULT '0',
  `penalty_shootout_score` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `goals` json DEFAULT NULL,
  `yellow_cards` json DEFAULT NULL,
  `red_cards` json DEFAULT NULL,
  `substitutions` json DEFAULT NULL,
  `injuries` json DEFAULT NULL,
  `disciplinary_incidents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `crowd_incidents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `safety_issues` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `general_comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_quality_assessment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `match_rating` int DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `electronic_signature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referee_reports_match_id_foreign` (`match_id`),
  KEY `referee_reports_referee_id_foreign` (`referee_id`),
  CONSTRAINT `referee_reports_match_id_foreign` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `referee_reports_referee_id_foreign` FOREIGN KEY (`referee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referee_reports`
--

LOCK TABLES `referee_reports` WRITE;
/*!40000 ALTER TABLE `referee_reports` DISABLE KEYS */;
INSERT INTO `referee_reports` VALUES (4,2,1,'Ligue 1 Tunisienne','2025-08-30 19:45:12','Stade Olympique de Radès','Ensoleillé','Excellente','Ahmed Ben Ali','Mohamed Trabelsi','Ali Mansouri','Hassan Dridi','Karim Saidi','Sami Ben Amor','2-1','1-0',3,0,NULL,'[{\"team\": \"Club Africain\", \"minute\": 25, \"player\": \"Ahmed Ben Salah\"}, {\"team\": \"Espérance Sportive de Tunis\", \"minute\": 67, \"player\": \"Youssef Msakni\"}, {\"team\": \"Club Africain\", \"minute\": 89, \"player\": \"Hamza Lahmar\"}]','[{\"minute\": 53, \"player\": \"Ahmed Ben Salah\", \"reason\": \"Faute de jeu\"}, {\"minute\": 65, \"player\": \"Youssef Msakni\", \"reason\": \"Faute de jeu\"}, {\"minute\": 56, \"player\": \"Hamza Lahmar\", \"reason\": \"Faute de jeu\"}, {\"minute\": 18, \"player\": \"Aymen Mathlouthi\", \"reason\": \"Faute de jeu\"}, {\"minute\": 70, \"player\": \"Cédric David\", \"reason\": \"Faute de jeu\"}, {\"minute\": 73, \"player\": \"Gabriel Leroy\", \"reason\": \"Faute de jeu\"}]','[{\"minute\": 80, \"player\": \"Nicolas Roux\", \"reason\": \"Comportement antisportif\"}, {\"minute\": 70, \"player\": \"Damien Girard\", \"reason\": \"Comportement antisportif\"}]','[]','[]','Incident mineur en fin de match',NULL,NULL,'Match bien arbitré','Bon niveau technique',8,'submitted','2025-09-04 19:45:12','Ahmed_Ben_Ali_2024','2025-09-04 19:45:12','2025-09-04 19:45:12'),(5,3,2,'Coupe de Tunisie','2025-09-01 19:46:20','Stade Municipal de Sfax','Nuageux','Bonne','Mohamed Khedira','Ali Ben Salem','Hassan Ben Youssef','Karim Ben Amor','Sami Trabelsi','Ahmed Mansouri','1-1','0-1',0,1,'4-3','[{\"team\": \"Espérance Sportive de Tunis\", \"minute\": 45, \"player\": \"Aymen Mathlouthi\"}, {\"team\": \"Club Africain\", \"minute\": 78, \"player\": \"Cédric David\"}]','[{\"minute\": 64, \"player\": \"Fabien André\", \"reason\": \"Retard de jeu\"}, {\"minute\": 66, \"player\": \"Zinedine Richard\", \"reason\": \"Retard de jeu\"}, {\"minute\": 78, \"player\": \"Nicolas Legrand\", \"reason\": \"Retard de jeu\"}, {\"minute\": 54, \"player\": \"Benoît Mercier\", \"reason\": \"Retard de jeu\"}]','[{\"minute\": 88, \"player\": \"Vincent Moreau\", \"reason\": \"Violence\"}, {\"minute\": 88, \"player\": \"Lucas Laurent\", \"reason\": \"Violence\"}]','[]','[]','Carton rouge direct pour violence',NULL,NULL,'Match disputé avec incidents','Niveau correct',6,'submitted','2025-09-04 19:46:20','Mohamed_Khedira_2024','2025-09-04 19:46:20','2025-09-04 19:46:20');
/*!40000 ALTER TABLE `referee_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_alerts`
--

DROP TABLE IF EXISTS `risk_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risk_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `type` enum('sca','injury','concussion','cardiac','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` decimal(3,2) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT '0',
  `priority` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `ai_metadata` json DEFAULT NULL,
  `recommendations` json DEFAULT NULL,
  `acknowledged_by` bigint unsigned DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `fifa_alert_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_alerts_athlete_id_foreign` (`athlete_id`),
  CONSTRAINT `risk_alerts_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_alerts`
--

LOCK TABLES `risk_alerts` WRITE;
/*!40000 ALTER TABLE `risk_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,1,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(2,1,2,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(3,1,3,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(4,1,4,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(5,1,5,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(6,1,6,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(7,1,7,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(8,1,8,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(9,1,9,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(10,1,10,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(11,1,11,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(12,1,12,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(13,1,13,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(14,1,14,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(15,1,15,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(16,1,16,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(17,1,17,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(18,1,18,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(19,1,19,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(20,1,20,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(21,1,21,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(22,1,22,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(23,1,23,'2025-09-07 00:18:09','2025-09-07 00:18:09'),(24,2,1,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(25,2,2,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(26,2,3,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(27,2,5,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(28,2,7,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(29,2,9,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(30,2,10,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(31,2,11,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(32,2,12,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(33,2,13,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(34,2,22,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(35,2,23,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(36,8,5,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(37,8,9,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(38,8,12,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(39,8,22,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(40,8,1,'2025-09-07 00:18:41','2025-09-07 00:18:41'),(41,8,13,'2025-09-07 00:18:41','2025-09-07 00:18:41');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `permissions` json DEFAULT NULL,
  `is_system_role` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `fifa_connect_id_prefix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  KEY `roles_created_by_foreign` (`created_by`),
  KEY `roles_updated_by_foreign` (`updated_by`),
  KEY `roles_tenant_id_index` (`tenant_id`),
  CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `roles_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `roles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,NULL,'system_admin','System Administrator','Full system access with all permissions','[\"player_registration_access\", \"competition_management_access\", \"healthcare_access\", \"system_administration\", \"user_management\", \"back_office_access\", \"fifa_connect_access\", \"fifa_data_sync\"]',1,1,'FIFA_SYS',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(2,NULL,'club_admin','Club Administrator','Club management with administrative access','[\"player_registration_access\", \"competition_management_access\", \"healthcare_access\", \"fifa_connect_access\"]',1,1,'FIFA_CLUB_ADMIN',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(3,NULL,'club_manager','Club Manager','Club operations management','[\"player_registration_access\", \"competition_management_access\", \"healthcare_access\", \"fifa_connect_access\"]',1,1,'FIFA_CLUB_MGR',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(4,NULL,'club_medical','Club Medical Staff','Healthcare and medical record management','[\"healthcare_access\", \"health_record_management\"]',1,1,'FIFA_CLUB_MED',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(5,NULL,'association_admin','Association Administrator','Football association management','[\"player_registration_access\", \"competition_management_access\", \"healthcare_access\", \"user_management\", \"fifa_connect_access\", \"fifa_data_sync\"]',1,1,'FIFA_ASSOC_ADMIN',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(6,NULL,'association_registrar','Association Registrar','Player registration and competition management','[\"player_registration_access\", \"competition_management_access\", \"fifa_connect_access\"]',1,1,'FIFA_ASSOC_REG',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(7,NULL,'association_medical','Association Medical Director','Healthcare oversight and medical records','[\"healthcare_access\", \"health_record_management\"]',1,1,'FIFA_ASSOC_MED',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(8,NULL,'referee','Referee','Match officiating and referee functions','[\"match_sheet_management\", \"referee_access\"]',1,1,'FIFA_REF',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(9,NULL,'assistant_referee','Assistant Referee','Assistant referee functions','[\"match_sheet_management\"]',1,1,'FIFA_ASST_REF',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(10,NULL,'fourth_official','Fourth Official','Fourth official functions','[\"match_sheet_management\"]',1,1,'FIFA_4TH_OFF',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(11,NULL,'var_official','VAR Official','Video Assistant Referee functions','[\"match_sheet_management\"]',1,1,'FIFA_VAR_OFF',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(12,NULL,'match_commissioner','Match Commissioner','Match oversight and competition management','[\"match_sheet_management\", \"competition_management_access\"]',1,1,'FIFA_MATCH_COMM',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(13,NULL,'match_official','Match Official','General match official functions','[\"match_sheet_management\"]',1,1,'FIFA_MATCH_OFF',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(14,NULL,'team_doctor','Team Doctor','Team medical care and health records','[\"healthcare_access\", \"health_record_management\"]',1,1,'FIFA_TEAM_DOC',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(15,NULL,'physiotherapist','Physiotherapist','Physical therapy and healthcare','[\"healthcare_access\"]',1,1,'FIFA_PHYSIO',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15'),(16,NULL,'sports_scientist','Sports Scientist','Sports science and performance analysis','[\"healthcare_access\"]',1,1,'FIFA_SPORTS_SCI',NULL,NULL,'2025-09-05 14:45:15','2025-09-05 14:45:15');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdoh_factors`
--

DROP TABLE IF EXISTS `sdoh_factors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdoh_factors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `environment_score` decimal(5,2) DEFAULT NULL,
  `social_support_score` decimal(5,2) DEFAULT NULL,
  `healthcare_access_score` decimal(5,2) DEFAULT NULL,
  `financial_status_score` decimal(5,2) DEFAULT NULL,
  `education_score` decimal(5,2) DEFAULT NULL,
  `overall_sdoh_score` decimal(5,2) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_date` (`player_id`,`assessment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdoh_factors`
--

LOCK TABLES `sdoh_factors` WRITE;
/*!40000 ALTER TABLE `sdoh_factors` DISABLE KEYS */;
INSERT INTO `sdoh_factors` VALUES (1,4,85.00,85.00,75.00,80.00,90.00,83.00,'2025-08-31','2025-08-31 12:17:10','2025-08-31 12:17:10');
/*!40000 ALTER TABLE `sdoh_factors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seasons`
--

DROP TABLE IF EXISTS `seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seasons`
--

LOCK TABLES `seasons` WRITE;
/*!40000 ALTER TABLE `seasons` DISABLE KEYS */;
INSERT INTO `seasons` VALUES (1,'Saison 2024-2025','2024-08-01','2025-07-31',1,'2025-09-02 13:22:30','2025-09-02 13:22:30'),(2,'Saison 2023-2024','2023-08-01','2024-07-31',0,'2025-09-02 13:22:30','2025-09-02 13:22:30'),(3,'2023-2024','2023-08-12','2024-05-19',0,'2025-09-02 18:04:21','2025-09-02 18:04:21');
/*!40000 ALTER TABLE `seasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` text NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0pnXELkmHZP2f75YWZHn6C4Wt7tNRddqTZmrGxn9',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ2xFeVFISEN3T2g5VUYzUm5QVk9odUtrSjlrVXBsZjFGbEh4UXdnZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9yZWFkeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756277375),('3MrjBJKeYSh2pu5N8UqoEKrrKHIPdp81Ph3cFmVR',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiamhXakVzOThKWDBFNnFSellRdFpPVkd2OVkwSW1GT3pmQlNqOUNleCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC90ZXN0LWFwcG9pbnRtZW50cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756243062),('jWHiK8zP6yuTCUVbFxdLEZRzwOEcE5LF67JRLv4n',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3hNeTlWaFBpdExMMFBlMGozOExTbEdBb0tMNUNBU21lNHlEb2JWRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9yZWFkeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756244141),('kiFDc6dO6tO5P0nQ9m2QnbNHvCWUPfLEqB0U7FoK',1,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoid1NZazNEeE1ncjRtY2pyd1VLRzJjR210dXpUREN2YnNFNHYzTWk0RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9maXh0dXJlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR4TThqd29yWHo1Vi9ILlc3NHhsRDAuTU84SzBGR2w2WDQxdDFyWjlabGE1Nkxlc0d0TGZkZSI7fQ==',1756243541),('qVHMZaUImYrTrwkWUISUZIzVgeI5oWhz21d55uTo',NULL,'192.168.65.1','curl/8.7.1','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWjdaRlk3Y0pLSkhNbGplS29lMG1ZR3Q0cjNVMTdtZmY5bWl4NkRxMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNDoiaHR0cDovL2xvY2FsaG9zdDo4MDgwL2FwcG9pbnRtZW50cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM0OiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBwb2ludG1lbnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1756242948),('Roxl8uBi1Yl5rkKQT8F0i9MwEdhqP8zDOun5x2e6',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiekN6UHRiRFpwMW5tNnBLNFFOU1F5NmhLYmFHdndPc0ZkWHRRaE9JVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9yZWFkeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756244232),('sHYtxLWa9OhEb6SYMHaMB3eRJ6wOrLrnYbgkD3yT',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMnRiUWtHZ0s2V2pFcnNOUUJKbERrQXF5YWpaOWNidFhrOVJZUUE4cSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756241118),('sL7nmInktl2VZdjJI6YNrhzYQ6isHHFfdJCKHbxJ',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVgxRGVpdWhLUURGbHp3dUJTb2laWEhIclhUWmVyRnVYQ2pDYTV3NSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756241083),('test-session-1757206640',NULL,'127.0.0.1','test','YToxOntzOjY6ImxvY2FsZSI7czoyOiJlbiI7fQ==',1757206640),('uqNndt6wIS5MnoN0HsAAY7VChYqG6WuiO4C1H7su',NULL,'192.168.65.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDdqbGtZRTcySlRvaGJxZ3hMd1R6MVBpUWxCOUtNZkE5RzRoeVZzYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC90ZXN0LWhlYWx0aC1yZWNvcmRzLWNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1756242115);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sports_devices`
--

DROP TABLE IF EXISTS `sports_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sports_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `device_type` enum('smartwatch','fitness_tracker','heart_rate_monitor','gps_tracker','muscle_sensor','sleep_tracker') NOT NULL,
  `device_name` varchar(255) NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `api_endpoint` varchar(500) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `last_sync` timestamp NULL DEFAULT NULL,
  `battery_level` int DEFAULT NULL,
  `connection_status` enum('connected','disconnected','syncing','error') DEFAULT 'connected',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_type` (`player_id`,`device_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sports_devices`
--

LOCK TABLES `sports_devices` WRITE;
/*!40000 ALTER TABLE `sports_devices` DISABLE KEYS */;
INSERT INTO `sports_devices` VALUES (1,4,'smartwatch','Apple Watch Series 9','Apple','Series 9','https://api.apple.com/health/connect','apple_health_key_123','2025-08-31 13:02:53',85,'connected','2025-08-31 13:02:53','2025-08-31 13:02:53'),(2,4,'heart_rate_monitor','Polar H10','Polar','H10','https://api.polar.com/v8/heart-rate','polar_api_key_456','2025-08-31 11:02:53',92,'connected','2025-08-31 13:02:53','2025-08-31 13:02:53'),(3,4,'gps_tracker','Garmin Forerunner 965','Garmin','Forerunner 965','https://api.garmin.com/connect/rest','garmin_connect_key_789','2025-08-31 12:02:53',78,'syncing','2025-08-31 13:02:53','2025-08-31 13:02:53');
/*!40000 ALTER TABLE `sports_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_editable` tinyint(1) NOT NULL DEFAULT '1',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `options` json DEFAULT NULL,
  `validation_rules` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`),
  KEY `system_settings_updated_by_foreign` (`updated_by`),
  KEY `system_settings_group_is_public_index` (`group`,`is_public`),
  KEY `system_settings_key_is_editable_index` (`key`,`is_editable`),
  CONSTRAINT `system_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'app_name','Nom de l\'application','Nom affiché de l\'application','FIT Platform','string','general',1,1,1,NULL,NULL,'FIT Platform',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(2,'app_version','Version de l\'application','Version actuelle de l\'application','1.0.0','string','general',1,1,1,NULL,NULL,'1.0.0',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(3,'maintenance_mode','Mode maintenance','Activer le mode maintenance','0','boolean','general',0,1,0,NULL,NULL,'0',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(4,'max_login_attempts','Tentatives de connexion max','Nombre maximum de tentatives de connexion','5','integer','security',0,1,1,NULL,NULL,'5',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(5,'session_timeout','Timeout de session (minutes)','Durée avant expiration de la session','120','integer','security',0,1,1,NULL,NULL,'120',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(6,'password_min_length','Longueur minimale du mot de passe','Nombre minimum de caractères pour le mot de passe','8','integer','security',0,1,1,NULL,NULL,'8',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(7,'mail_from_address','Adresse email expéditeur','Adresse email utilisée pour l\'envoi d\'emails','noreply@fitplatform.com','string','email',0,1,1,NULL,NULL,'noreply@fitplatform.com',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(8,'mail_from_name','Nom expéditeur','Nom affiché pour l\'expéditeur d\'emails','FIT Platform','string','email',0,1,1,NULL,NULL,'FIT Platform',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(9,'max_file_size','Taille max des fichiers (MB)','Taille maximale autorisée pour les fichiers','10','integer','files',0,1,1,NULL,NULL,'10',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(10,'allowed_file_types','Types de fichiers autorisés','Types de fichiers autorisés pour l\'upload','jpg,jpeg,png,pdf,doc,docx','string','files',0,1,1,NULL,NULL,'jpg,jpeg,png,pdf,doc,docx',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(11,'fifa_api_url','URL API FIFA','URL de base pour l\'API FIFA Connect','https://api.fifa.com/v1','string','fifa',0,1,0,NULL,NULL,'https://api.fifa.com/v1',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(12,'fifa_sync_interval','Intervalle de synchronisation FIFA (minutes)','Fréquence de synchronisation avec FIFA','60','integer','fifa',0,1,0,NULL,NULL,'60',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(13,'medical_certificate_validity_days','Validité certificat médical (jours)','Durée de validité d\'un certificat médical en jours','365','integer','medical',0,1,1,NULL,NULL,'365',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(14,'medical_exam_required_age','Âge requis pour examen médical','Âge minimum pour passer un examen médical','16','integer','medical',0,1,1,NULL,NULL,'16',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(15,'medical_clearance_types','Types d\'aptitude médicale','Types d\'aptitude médicale autorisés (séparés par virgule)','apte,inapte,apte_avec_reserves','string','medical',0,1,1,NULL,NULL,'apte,inapte,apte_avec_reserves',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(16,'medical_document_retention_years','Conservation documents médicaux (années)','Durée de conservation des documents médicaux','5','integer','medical',0,1,1,NULL,NULL,'5',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(17,'medical_emergency_contact_required','Contact d\'urgence obligatoire','Exiger un contact d\'urgence pour tous les joueurs','1','boolean','medical',0,1,1,NULL,NULL,'1',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(18,'medical_insurance_required','Assurance médicale obligatoire','Exiger une assurance médicale pour tous les joueurs','1','boolean','medical',0,1,1,NULL,NULL,'1',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(19,'competition_registration_deadline_days','Délai d\'inscription (jours avant)','Nombre de jours avant le début pour fermer les inscriptions','7','integer','competition',0,1,1,NULL,NULL,'7',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(20,'competition_min_players_per_team','Joueurs minimum par équipe','Nombre minimum de joueurs requis par équipe','11','integer','competition',0,1,1,NULL,NULL,'11',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(21,'competition_max_players_per_team','Joueurs maximum par équipe','Nombre maximum de joueurs autorisés par équipe','25','integer','competition',0,1,1,NULL,NULL,'25',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(22,'competition_match_duration_minutes','Durée d\'un match (minutes)','Durée standard d\'un match en minutes','90','integer','competition',0,1,1,NULL,NULL,'90',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(23,'competition_halftime_duration_minutes','Durée de la mi-temps (minutes)','Durée de la pause entre les deux mi-temps','15','integer','competition',0,1,1,NULL,NULL,'15',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(24,'competition_referee_assignments_auto','Assignation automatique des arbitres','Activer l\'assignation automatique des arbitres','0','boolean','competition',0,1,1,NULL,NULL,'0',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(25,'competition_disciplinary_sanctions_enabled','Sanctions disciplinaires activées','Activer le système de sanctions disciplinaires','1','boolean','competition',0,1,1,NULL,NULL,'1',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(26,'competition_points_win','Points pour victoire','Nombre de points attribués pour une victoire','3','integer','competition',0,1,1,NULL,NULL,'3',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(27,'competition_points_draw','Points pour match nul','Nombre de points attribués pour un match nul','1','integer','competition',0,1,1,NULL,NULL,'1',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(28,'competition_points_loss','Points pour défaite','Nombre de points attribués pour une défaite','0','integer','competition',0,1,1,NULL,NULL,'0',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(29,'competition_ranking_criteria','Critères de classement','Critères de classement (séparés par virgule)','points,goal_difference,goals_scored,head_to_head','string','competition',0,1,1,NULL,NULL,'points,goal_difference,goals_scored,head_to_head',NULL,'2025-09-05 17:05:04','2025-09-05 17:05:04'),(30,'medical_blood_type_required','Groupe sanguin obligatoire','Exiger le groupe sanguin pour tous les joueurs','1','boolean','medical',0,1,0,NULL,NULL,'1',NULL,'2025-09-05 17:12:29','2025-09-05 17:12:29'),(31,'competition_substitution_limit','Limite de remplacements','Nombre maximum de remplacements autorisés par match','5','integer','competition',0,1,1,NULL,'min:1|max:10','5',NULL,'2025-09-05 17:12:29','2025-09-05 17:12:29');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_players`
--

DROP TABLE IF EXISTS `team_players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_players` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned NOT NULL,
  `player_id` bigint unsigned NOT NULL,
  `role` enum('starter','substitute','reserve','loan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'substitute',
  `squad_number` int DEFAULT NULL,
  `joined_date` date NOT NULL,
  `contract_end_date` date DEFAULT NULL,
  `position_preference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','injured','suspended','loaned_out','retired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_players_team_id_player_id_unique` (`team_id`,`player_id`),
  UNIQUE KEY `team_players_team_id_squad_number_unique` (`team_id`,`squad_number`),
  KEY `team_players_player_id_foreign` (`player_id`),
  KEY `team_players_team_id_role_index` (`team_id`,`role`),
  KEY `team_players_team_id_status_index` (`team_id`,`status`),
  CONSTRAINT `team_players_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE,
  CONSTRAINT `team_players_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_players`
--

LOCK TABLES `team_players` WRITE;
/*!40000 ALTER TABLE `team_players` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('national','club','academy','regional') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'club',
  `federation_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `club_id` bigint unsigned DEFAULT NULL,
  `fifa_team_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_federation_id_index` (`federation_id`),
  KEY `teams_fifa_team_id_index` (`fifa_team_id`),
  KEY `teams_club_id_foreign` (`club_id`),
  KEY `teams_tenant_id_index` (`tenant_id`),
  CONSTRAINT `teams_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teams_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (2,NULL,'Équipe Première','club',NULL,1,NULL,'2025-08-30 15:15:34','2025-09-02 11:58:41'),(3,NULL,'Équipe Première','club',NULL,2,NULL,'2025-08-30 15:15:34','2025-09-02 11:58:49'),(4,NULL,'Réserve','club',NULL,1,NULL,'2025-08-30 15:15:34','2025-09-02 11:58:49'),(5,NULL,'Réserve','club',NULL,2,NULL,'2025-08-30 15:15:34','2025-09-02 11:58:49'),(6,NULL,'Équipe Première','club',NULL,3,NULL,'2025-09-02 18:05:09','2025-09-02 18:05:09'),(7,NULL,'Équipe Première','club',NULL,6,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(8,NULL,'Équipe Première','club',NULL,7,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(9,NULL,'Équipe Première','club',NULL,8,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(10,NULL,'Équipe Première','club',NULL,9,NULL,'2025-09-02 18:14:20','2025-09-02 18:14:20'),(11,NULL,'Équipe Première','club',NULL,10,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(12,NULL,'Équipe Première','club',NULL,11,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(13,NULL,'Équipe Première','club',NULL,12,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(14,NULL,'Équipe Première','club',NULL,13,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(15,NULL,'Équipe Première','club',NULL,14,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(16,NULL,'Équipe Première','club',NULL,15,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(17,NULL,'Équipe Première','club',NULL,16,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(18,NULL,'Équipe Première','club',NULL,17,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(19,NULL,'Équipe Première','club',NULL,18,NULL,'2025-09-02 18:14:21','2025-09-02 18:14:21'),(20,NULL,'Équipe Première','club',NULL,19,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(21,NULL,'Équipe Première','club',NULL,20,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(22,NULL,'Équipe Première','club',NULL,21,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(23,NULL,'Équipe Première','club',NULL,22,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22'),(24,NULL,'Équipe Première','club',NULL,23,NULL,'2025-09-02 18:14:22','2025-09-02 18:14:22');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('system','association','club','federation','organization') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'organization',
  `status` enum('active','inactive','suspended','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `fifa_connect_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fifa_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `settings` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `parent_tenant_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`),
  UNIQUE KEY `tenants_fifa_connect_id_unique` (`fifa_connect_id`),
  KEY `tenants_created_by_foreign` (`created_by`),
  KEY `tenants_updated_by_foreign` (`updated_by`),
  KEY `tenants_type_status_index` (`type`,`status`),
  KEY `tenants_country_status_index` (`country`,`status`),
  KEY `tenants_parent_tenant_id_index` (`parent_tenant_id`),
  KEY `tenants_fifa_connect_id_index` (`fifa_connect_id`),
  CONSTRAINT `tenants_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tenants_parent_tenant_id_foreign` FOREIGN KEY (`parent_tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenants_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (1,'Club Africain','club-africain','Club Africain de Tunis',NULL,'club','active',NULL,NULL,'TN','UTC','fr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-07 01:59:54','2025-09-07 01:59:54',NULL),(2,'Espérance Sportive','esperance-sportive','Espérance Sportive de Tunis',NULL,'club','active',NULL,NULL,'TN','UTC','fr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-07 01:59:54','2025-09-07 01:59:54',NULL),(3,'Fédération Tunisienne de Football','ftf','Fédération Tunisienne de Football',NULL,'federation','active',NULL,NULL,'TN','UTC','fr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-07 01:59:54','2025-09-07 01:59:54',NULL);
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `therapeutic_use_exemptions`
--

DROP TABLE IF EXISTS `therapeutic_use_exemptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `therapeutic_use_exemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint unsigned NOT NULL,
  `substance_name` varchar(255) NOT NULL,
  `medical_condition` text NOT NULL,
  `prescribing_doctor` varchar(255) DEFAULT NULL,
  `doctor_license` varchar(255) DEFAULT NULL,
  `exemption_start_date` date NOT NULL,
  `exemption_end_date` date DEFAULT NULL,
  `exemption_status` enum('approved','pending','rejected','expired') DEFAULT 'pending',
  `wada_approval` tinyint(1) DEFAULT '0',
  `fifa_approval` tinyint(1) DEFAULT '0',
  `approval_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_player_substance` (`player_id`,`substance_name`),
  KEY `idx_player_status` (`player_id`,`exemption_status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `therapeutic_use_exemptions`
--

LOCK TABLES `therapeutic_use_exemptions` WRITE;
/*!40000 ALTER TABLE `therapeutic_use_exemptions` DISABLE KEYS */;
INSERT INTO `therapeutic_use_exemptions` VALUES (1,4,'Salbutamol (Ventoline)','Asthme d\'effort diagnostiqué par pneumologue','Dr. Marie Dubois','Ordre des Médecins - 12345','2025-03-03','2026-03-03','approved',1,1,'Autorisation complète pour usage inhalé uniquement','2025-08-31 13:13:22','2025-08-31 13:13:22'),(2,4,'Cortisone (Prednisone)','Traitement d\'urgence pour réaction allergique sévère','Dr. Jean Martin','Ordre des Médecins - 67890','2025-08-16','2025-09-15','approved',1,1,'Traitement court terme - Surveillance renforcée','2025-08-31 13:13:22','2025-08-31 13:13:22');
/*!40000 ALTER TABLE `therapeutic_use_exemptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tue_requests`
--

DROP TABLE IF EXISTS `tue_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tue_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `medication` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `physician_id` bigint unsigned NOT NULL,
  `status` enum('pending','approved','rejected','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `request_date` date NOT NULL,
  `approved_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `supporting_documents` json DEFAULT NULL,
  `fifa_tue_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tue_requests_athlete_id_foreign` (`athlete_id`),
  CONSTRAINT `tue_requests_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tue_requests`
--

LOCK TABLES `tue_requests` WRITE;
/*!40000 ALTER TABLE `tue_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `tue_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploaded_documents`
--

DROP TABLE IF EXISTS `uploaded_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uploaded_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` bigint unsigned NOT NULL,
  `fifa_connect_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `document_type` enum('medical_record','imaging','lab_result','prescription','certificate','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ai_analysis` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `status` enum('pending','processed','analyzed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uploaded_documents_athlete_id_document_type_index` (`athlete_id`,`document_type`),
  KEY `uploaded_documents_fifa_connect_id_index` (`fifa_connect_id`),
  KEY `uploaded_documents_status_index` (`status`),
  KEY `uploaded_documents_uploaded_by_index` (`uploaded_by`),
  CONSTRAINT `uploaded_documents_athlete_id_foreign` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploaded_documents`
--

LOCK TABLES `uploaded_documents` WRITE;
/*!40000 ALTER TABLE `uploaded_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploaded_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permissions_user_id_permission_id_unique` (`user_id`,`permission_id`),
  KEY `user_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` bigint unsigned DEFAULT NULL,
  `club_id` bigint unsigned DEFAULT NULL,
  `association_id` bigint unsigned DEFAULT NULL,
  `team_id` bigint unsigned DEFAULT NULL,
  `player_id` bigint unsigned DEFAULT NULL,
  `fifa_connect_id` varchar(255) NOT NULL,
  `permissions` json DEFAULT NULL,
  `preferences` json DEFAULT NULL,
  `status` enum('active','inactive','suspended','pending') NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `login_count` int NOT NULL DEFAULT '0',
  `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
  `language` varchar(255) NOT NULL DEFAULT 'fr',
  `notifications_email` tinyint(1) NOT NULL DEFAULT '1',
  `notifications_sms` tinyint(1) NOT NULL DEFAULT '0',
  `profile_picture_url` varchar(255) DEFAULT NULL,
  `profile_picture_alt` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_tenant_id_index` (`tenant_id`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'System Administrator','admin@medpredictor.com','$2y$12$ZCkn5vfsSAM5eCS8pvhI8uZVuD46vV5HQO2XpkLiB8gGDchLS0RSm','system_admin','2025-08-26 20:12:02','2025-09-04 18:39:10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','[\"user_read\", \"user_write\", \"user_delete\", \"admin_access\", \"referee_access\", \"player_registration_access\", \"competition_management_access\", \"healthcare_access\", \"fifa_connect_access\", \"club_management\", \"team_management\", \"report_generate\", \"data_export\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(2,NULL,'Test Admin','test@admin.com','$2y$12$Wp9a4TgWIPK4jrRDq5HToOFOruAw./k8J62N4diaY8BLiQkWS2SYK','system_admin','2025-08-31 15:45:36','2025-09-07 02:01:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','[\"user_read\", \"user_write\", \"user_delete\", \"admin_access\", \"referee_access\", \"player_registration_access\", \"competition_management_access\", \"healthcare_access\", \"fifa_connect_access\", \"club_management\", \"team_management\", \"report_generate\", \"data_export\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(3,NULL,'Votre Nom','im0668@gmail.com','$2y$12$rVriIW7iCq3JBMq8wLD7CusBqlmemJ0QWVv.tQeOy3DOAnlWadHHi','club_admin','2025-09-01 13:51:36','2025-09-04 11:31:38','0123456789',NULL,NULL,NULL,NULL,NULL,NULL,'FIFA_CLUB_ADMIN_20250901135136_EEJ1AO','[\"player_registration_access\", \"team_management\", \"healthcare_access\", \"fifa_connect_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(4,NULL,'Test Referee','referee@test.com','$2y$12$P5rY.mdevVz6AHRzeSf/Mut.R85xIN2yQIR4SsLhbDnIOcPU4FaYy','referee','2025-09-03 16:48:56','2025-09-04 11:31:38',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'REF001','[\"referee_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(5,3,'Mohamed Jebali','mohamed.jebali@ftf.tn','$2y$12$8UzTfv5SqEEpozy/2vY86.E30rxUVyf9OMOxz6eRM5r7Zmf4lcEMO','referee','2025-09-03 17:52:07','2025-09-07 02:02:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'REF010','[\"referee_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(6,3,'Mehdi Abid Charef','mehdi.abid.charef@ftf.tn','$2y$12$ZGUUNKgtZIM9idrVqdEE4ecMjV/8PjIdETr48Q2kkV5C6ctU2m.ge','referee','2025-09-03 17:52:07','2025-09-07 02:02:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'REF011','[\"referee_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(7,3,'Bechir Hassani','bechir.hassani@ftf.tn','$2y$12$p1NfrUkoGSrhXWx9aCTJ8e0eiEsAiJkqwIcJ2UUqCgYv8P.jqMkBi','referee','2025-09-03 17:52:08','2025-09-07 02:02:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'REF012','[\"referee_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(8,3,'Kamel Harrouche','kamel.harrouche@ftf.tn','$2y$12$chqU0r4wGYF863ca4CD9CO/SAIrNHblDS7l0yPs.U/lMPhpvBem9K','referee','2025-09-03 17:52:08','2025-09-07 02:02:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'REF013','[\"referee_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(9,3,'Haythem Guirat','haythem.guirat@ftf.tn','$2y$12$44xMmKvPdh1izz9RwdngjOtDVQlzG93s8C6CgKLfqDxdhXzvIRC0K','referee','2025-09-03 17:52:08','2025-09-07 02:02:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'REF014','[\"referee_access\", \"report_generate\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(10,1,'Admin Club Africain','admin.clubafricain@fifa.com','$2y$12$y1gRYden0TDC1OLOR2MjlOCOFxjBi3lEH3m/0Y2xTbNyUO2scdzcy','club_admin','2025-09-04 17:57:27','2025-09-07 02:02:22',NULL,NULL,NULL,1,NULL,NULL,NULL,'CA_ADMIN_1757008647',NULL,NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(11,2,'Admin Espérance','admin.esperance@fifa.com','$2y$12$Jo18p1borgNthy1Fz.NWvOdVFi6TgBUmrGwDepbAXW7SCgMpF3KFO','club_admin','2025-09-04 17:57:27','2025-09-07 02:02:22',NULL,NULL,NULL,2,NULL,NULL,NULL,'EST_ADMIN_1757008647',NULL,NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(13,NULL,'Mike Wilson','joueur.3@fit-portal.local','$2y$12$W3AZNWgLTcugQRHyKZjN4.oG/tPSh.1GmS6.f3MjA8DJPduxLo.yq','player','2025-09-05 19:21:07','2025-09-05 19:21:07',NULL,NULL,NULL,4,3,NULL,3,'FIT-3-1757100067','[\"player_portal_access\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(14,NULL,'Ahmed Ben Salah','joueur.4@fit-portal.local','$2y$12$7gpLXikGPOO7Vgi6Ser1A.VwG6.xsTVJx6d/OZ7NACAHkhzs/WHzm','player','2025-09-05 19:21:36','2025-09-05 19:21:36',NULL,NULL,NULL,1,1,NULL,4,'FIT-4-1757100096','[\"player_portal_access\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(15,NULL,'Youssef Msakni','joueur.5@fit-portal.local','$2y$12$Tgy.mE5xC.NH7.NiclOhP.Vk0V3jiKE5/AB.pnU.0JdSiucn/j/T6','player','2025-09-05 19:21:36','2025-09-05 19:21:36',NULL,NULL,NULL,2,1,NULL,5,'FIT-5-1757100096','[\"player_portal_access\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(16,NULL,'Hamza Lahmar','joueur.6@fit-portal.local','$2y$12$REtwlI1YfwlhuBD.l2.GROliYyyk5U/Zt7aEqLvATz7aYRToyQ55C','player','2025-09-05 19:21:36','2025-09-05 19:21:36',NULL,NULL,NULL,1,1,NULL,6,'FIT-6-1757100096','[\"player_portal_access\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL),(17,NULL,'Aymen Mathlouthi','joueur.7@fit-portal.local','$2y$12$LrPe//wDnJ/oTi9X56DE0uXQk1oxPKMXMUom4oQqAaRphUq0I4bUe','player','2025-09-05 19:21:37','2025-09-05 19:21:37',NULL,NULL,NULL,2,1,NULL,7,'FIT-7-1757100097','[\"player_portal_access\"]',NULL,'active',NULL,0,'UTC','fr',1,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-15 17:38:14
