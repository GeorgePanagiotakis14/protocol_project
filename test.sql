-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (166,1,'incoming','create',77,'2026-02-16 06:10:02','2026-02-16 06:10:02'),(167,1,'incoming','create',78,'2026-02-16 07:17:56','2026-02-16 07:17:56'),(168,1,'incoming','create',79,'2026-02-16 07:20:53','2026-02-16 07:20:53'),(169,1,'outgoing','create',90,'2026-02-16 07:21:47','2026-02-16 07:21:47'),(170,1,'incoming','create',80,'2026-02-16 07:22:37','2026-02-16 07:22:37'),(171,1,'outgoing','create',91,'2026-02-16 07:26:26','2026-02-16 07:26:26'),(172,1,'incoming','update',79,'2026-02-16 07:27:25','2026-02-16 07:27:25'),(173,1,'incoming','update',78,'2026-02-16 07:28:45','2026-02-16 07:28:45'),(174,1,'incoming','create',81,'2026-02-16 07:57:30','2026-02-16 07:57:30'),(175,1,'incoming','create',82,'2026-02-16 07:57:50','2026-02-16 07:57:50'),(176,1,'incoming','create',83,'2026-02-16 07:57:51','2026-02-16 07:57:51'),(177,1,'incoming','create',84,'2026-02-16 07:58:56','2026-02-16 07:58:56'),(178,1,'incoming','create',85,'2026-02-16 07:58:57','2026-02-16 07:58:57'),(179,1,'outgoing','create',92,'2026-02-16 07:59:27','2026-02-16 07:59:27'),(180,1,'outgoing','create',93,'2026-02-16 07:59:28','2026-02-16 07:59:28'),(181,1,'incoming','create',86,'2026-02-16 08:02:18','2026-02-16 08:02:18'),(182,1,'outgoing','create',94,'2026-02-16 08:02:45','2026-02-16 08:02:45'),(183,1,'outgoing','create',95,'2026-02-16 08:02:58','2026-02-16 08:02:58'),(184,1,'incoming','create',87,'2026-02-16 10:07:35','2026-02-16 10:07:35'),(185,1,'incoming','update',78,'2026-02-17 04:34:17','2026-02-17 04:34:17'),(186,1,'incoming','update',78,'2026-02-17 04:35:51','2026-02-17 04:35:51'),(187,1,'incoming','update',78,'2026-02-17 04:36:18','2026-02-17 04:36:18'),(188,1,'incoming','update',78,'2026-02-17 04:48:19','2026-02-17 04:48:19'),(189,1,'incoming','update',78,'2026-02-17 05:19:32','2026-02-17 05:19:32'),(190,1,'incoming','update',78,'2026-02-17 05:20:45','2026-02-17 05:20:45'),(191,1,'incoming','update',78,'2026-02-17 05:36:30','2026-02-17 05:36:30'),(192,1,'outgoing','delete',91,'2026-02-17 06:29:17','2026-02-17 06:29:17'),(193,1,'outgoing','delete',90,'2026-02-17 06:35:22','2026-02-17 06:35:22'),(194,1,'incoming','update',82,'2026-02-17 06:46:11','2026-02-17 06:46:11'),(195,1,'incoming','update',78,'2026-02-17 07:17:30','2026-02-17 07:17:30'),(196,1,'incoming','update',78,'2026-02-17 07:18:14','2026-02-17 07:18:14'),(197,1,'incoming','update',78,'2026-02-17 07:18:38','2026-02-17 07:18:38'),(198,1,'outgoing','delete',92,'2026-02-17 07:46:51','2026-02-17 07:46:51');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `incoming_document_attachments`
--

DROP TABLE IF EXISTS `incoming_document_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incoming_document_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `incoming_document_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incoming_document_attachments_incoming_document_id_index` (`incoming_document_id`),
  CONSTRAINT `incoming_document_attachments_incoming_document_id_foreign` FOREIGN KEY (`incoming_document_id`) REFERENCES `incoming_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_document_attachments`
--

LOCK TABLES `incoming_document_attachments` WRITE;
/*!40000 ALTER TABLE `incoming_document_attachments` DISABLE KEYS */;
INSERT INTO `incoming_document_attachments` VALUES (80,77,'2026/incoming/2026-02-22/incoming_1_081001_1.pdf','incoming_3 (1) - Αντιγραφή.pdf',374779,'2026-02-16 06:10:02','2026-02-16 06:10:02'),(86,79,'2026/incoming/2026-02-22/incoming_2_092053_1.pdf','incoming_3 (1) - Αντιγραφή.pdf',374779,'2026-02-16 07:20:53','2026-02-16 07:20:53'),(87,80,'2026/incoming/2026-02-02/incoming_4_092237_1.pdf','incoming_3 (1).pdf',374779,'2026-02-16 07:22:37','2026-02-16 07:22:37'),(88,81,'2026/incoming/2026-02-13/incoming_5_095730_1.pdf','incoming_3 (1).pdf',374779,'2026-02-16 07:57:30','2026-02-16 07:57:30'),(89,82,'2026/incoming/2026-02-14/incoming_6_095750_1.pdf','incoming_3 (2).pdf',374779,'2026-02-16 07:57:50','2026-02-16 07:57:50'),(90,82,'2026/incoming/2026-02-14/incoming_6_095750_2.pdf','incoming_3 (1) - Αντιγραφή.pdf',374779,'2026-02-16 07:57:50','2026-02-16 07:57:50'),(91,82,'2026/incoming/2026-02-14/incoming_6_095750_3.pdf','incoming_3.pdf',374779,'2026-02-16 07:57:50','2026-02-16 07:57:50'),(92,83,'2026/incoming/2026-02-14/incoming_7_095751_1.pdf','incoming_3 (2).pdf',374779,'2026-02-16 07:57:51','2026-02-16 07:57:51'),(93,83,'2026/incoming/2026-02-14/incoming_7_095751_2.pdf','incoming_3 (1) - Αντιγραφή.pdf',374779,'2026-02-16 07:57:51','2026-02-16 07:57:51'),(94,83,'2026/incoming/2026-02-14/incoming_7_095751_3.pdf','incoming_3.pdf',374779,'2026-02-16 07:57:51','2026-02-16 07:57:51'),(95,84,'2026/incoming/2026-02-21/incoming_8_095856_1.pdf','incoming_3 (1).pdf',374779,'2026-02-16 07:58:56','2026-02-16 07:58:56'),(96,85,'2026/incoming/2026-02-21/incoming_9_095857_1.pdf','incoming_3 (1).pdf',374779,'2026-02-16 07:58:57','2026-02-16 07:58:57'),(97,86,'2025/incoming/2025-01-29/incoming_12_100218_1.pdf','incoming_3 (2).pdf',374779,'2026-02-16 08:02:18','2026-02-16 08:02:18'),(98,86,'2025/incoming/2025-01-29/incoming_12_100218_2.pdf','incoming_3 (1).pdf',374779,'2026-02-16 08:02:18','2026-02-16 08:02:18'),(99,87,'2026/incoming/2026-02-21/incoming_14_120735_1.pdf','incoming_3 (2).pdf',374779,'2026-02-16 10:07:35','2026-02-16 10:07:35'),(100,87,'2026/incoming/2026-02-21/incoming_14_120735_2.pdf','incoming_3 (1) - Αντιγραφή.pdf',374779,'2026-02-16 10:07:35','2026-02-16 10:07:35'),(101,87,'2026/incoming/2026-02-21/incoming_14_120735_3.pdf','incoming_3 (1).pdf',374779,'2026-02-16 10:07:35','2026-02-16 10:07:35'),(102,87,'2026/incoming/2026-02-21/incoming_14_120735_4.pdf','incoming_3.pdf',374779,'2026-02-16 10:07:35','2026-02-16 10:07:35'),(103,87,'2026/incoming/2026-02-21/incoming_14_120735_5.pdf','03_28012026.pdf',374779,'2026-02-16 10:07:35','2026-02-16 10:07:35'),(109,82,'2026/incoming/2026-02-14/incoming_6_084611_1.pdf','incoming_3 (1).pdf',374779,'2026-02-17 06:46:11','2026-02-17 06:46:11'),(110,78,'2024/incoming/2024-02-29/incoming_1_091837_1.pdf','ΦΟΡΤΗΓΑ.pdf',2086440,'2026-02-17 07:18:38','2026-02-17 07:18:38'),(111,78,'2024/incoming/2024-02-29/incoming_1_091838_2.pdf','Ηλεκτρικά επαναφορτιζόμενα φορτηγά.pdf',5763205,'2026-02-17 07:18:38','2026-02-17 07:18:38');
/*!40000 ALTER TABLE `incoming_document_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoming_documents`
--

DROP TABLE IF EXISTS `incoming_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incoming_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `aa` int unsigned NOT NULL,
  `protocol_year` smallint unsigned NOT NULL DEFAULT '2026',
  `protocol_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incoming_protocol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incoming_date` date DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_paths` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incoming_documents_protocol_year_aa_unique` (`protocol_year`,`aa`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_documents`
--

LOCK TABLES `incoming_documents` WRITE;
/*!40000 ALTER TABLE `incoming_documents` DISABLE KEYS */;
INSERT INTO `incoming_documents` VALUES (77,1,2016,'1','12','2026-02-22',NULL,'σαφ','2026-02-08','αδαφ','αφ','2026/incoming/2026-02-22/incoming_1_081001_1.pdf',NULL,'2026-02-16 06:10:01','2026-02-16 06:10:02'),(78,1,2013,'1','34ws3','2024-02-29','ef','sef','2026-02-05','se0000000','fsf1111','2024/incoming/2024-02-29/incoming_1_091837_1.pdf',NULL,'2026-02-16 07:17:56','2026-02-17 07:18:38'),(79,2,2013,'2','sfsgs','2026-02-22','sgsg','dg','2026-02-12','111111111','sgs','2026/incoming/2026-02-22/incoming_2_092053_1.pdf',NULL,'2026-02-16 07:20:53','2026-02-16 07:27:25'),(80,4,2013,'4','fse','2026-02-02','sfe','tg','2026-02-28','fsef','sef','2026/incoming/2026-02-02/incoming_4_092237_1.pdf',NULL,'2026-02-16 07:22:37','2026-02-16 07:22:37'),(81,5,2013,'5','srs','2026-02-13','resr','sr',NULL,'sres','rsr','2026/incoming/2026-02-13/incoming_5_095730_1.pdf',NULL,'2026-02-16 07:57:30','2026-02-16 07:57:30'),(82,6,2013,'6','se','2026-02-14','ser','ser',NULL,'asrraaaaaaaaa','aaaaasr','2026/incoming/2026-02-14/incoming_6_084611_1.pdf',NULL,'2026-02-16 07:57:50','2026-02-17 06:46:11'),(83,7,2013,'7','se','2026-02-14','ser','ser',NULL,'asrraaaaaaaaa','aaaaasr','2026/incoming/2026-02-14/incoming_7_095751_1.pdf',NULL,'2026-02-16 07:57:51','2026-02-16 07:57:51'),(84,8,2013,'8','sf','2026-02-21',NULL,'f',NULL,'rsg','sssssssssf','2026/incoming/2026-02-21/incoming_8_095856_1.pdf',NULL,'2026-02-16 07:58:56','2026-02-16 07:58:56'),(85,9,2013,'9','sf','2026-02-21',NULL,'f',NULL,'rsg','sssssssssf','2026/incoming/2026-02-21/incoming_9_095857_1.pdf',NULL,'2026-02-16 07:58:57','2026-02-16 07:58:57'),(86,12,2013,'12','zsf','2025-01-29','fzf','zfz','2026-02-14','zf','zf','2025/incoming/2025-01-29/incoming_12_100218_1.pdf',NULL,'2026-02-16 08:02:18','2026-02-16 08:02:18'),(87,14,2013,'14','φφ','2026-02-21',NULL,NULL,NULL,'ηγβ',NULL,'2026/incoming/2026-02-21/incoming_14_120735_1.pdf',NULL,'2026-02-16 10:07:35','2026-02-16 10:07:35');
/*!40000 ALTER TABLE `incoming_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
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
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_20_111501_create_incoming_documents_table',1),(5,'2026_01_20_111606_create_outgoing_documents_table',1),(6,'2026_01_20_111805_add_outgoing_date_to_outgoing_documents',1),(7,'2026_01_22_071626_add_is_admin_to_users_table',1),(8,'2026_01_22_110236_add_is_active_to_users_table',1),(9,'2026_01_23_105750_create_audit_logs_table',1),(10,'2026_01_27_113519_add_aa_to_incoming_documents_table',1),(11,'2026_01_27_113856_add_aa_and_reply_to_incoming_id_to_outgoing_documents_table',1),(12,'2026_01_29_063926_add_attachment_path_to_incoming_documents_table',2),(13,'2026_02_02_073422_add_attachment_path_to_outgoing_documents_table',3),(14,'2026_02_02_085532_add_attachment_paths_to_incoming_documents_table',4),(15,'2026_02_02_085754_add_attachment_paths_to_outgoing_documents_table',4),(16,'2026_02_02_123607_add_attachment_path_to_outgoing_documents_table',5),(17,'2026_02_04_073641_create_incoming_document_attachments_table',6),(18,'2026_02_04_104425_create_outgoing_document_attachments_table',7),(19,'2026_01_29_124309_add_attachment_path_to_incoming_documents_table',8),(20,'2026_02_06_110020_create_protocol_counters_table',9),(21,'2026_02_16_064205_add_protocol_year_to_documents_and_counters',10);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outgoing_document_attachments`
--

DROP TABLE IF EXISTS `outgoing_document_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outgoing_document_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outgoing_document_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outgoing_document_attachments_outgoing_document_id_index` (`outgoing_document_id`),
  CONSTRAINT `outgoing_document_attachments_outgoing_document_id_foreign` FOREIGN KEY (`outgoing_document_id`) REFERENCES `outgoing_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outgoing_document_attachments`
--

LOCK TABLES `outgoing_document_attachments` WRITE;
/*!40000 ALTER TABLE `outgoing_document_attachments` DISABLE KEYS */;
INSERT INTO `outgoing_document_attachments` VALUES (67,94,'2026/outgoing/2026-02-16/outgoing_13_100245_1.pdf','incoming_3 (1) - Αντιγραφή.pdf',374779,'2026-02-16 08:02:45','2026-02-16 08:02:45'),(68,95,'2026/outgoing/2026-02-16/outgoing_6_100258_1.pdf','incoming_3 (1).pdf',374779,'2026-02-16 08:02:58','2026-02-16 08:02:58'),(69,95,'2026/outgoing/2026-02-17/outgoing_6_074343_1.pdf','Ηλεκτρικά επαναφορτιζόμενα φορτηγά.pdf',5763205,'2026-02-17 05:43:43','2026-02-17 05:43:43'),(70,95,'2026/outgoing/2026-02-17/outgoing_6_074343_2.pdf','ΦΟΡΤΗΓΑ.pdf',2086440,'2026-02-17 05:43:43','2026-02-17 05:43:43'),(76,93,'2026/outgoing/2026-02-17/outgoing_11_084649_1.pdf','incoming_3 (1).pdf',374779,'2026-02-17 06:46:49','2026-02-17 06:46:49');
/*!40000 ALTER TABLE `outgoing_document_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outgoing_documents`
--

DROP TABLE IF EXISTS `outgoing_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outgoing_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `aa` int unsigned DEFAULT NULL,
  `protocol_year` smallint unsigned NOT NULL DEFAULT '2026',
  `reply_to_incoming_id` bigint unsigned DEFAULT NULL,
  `protocol_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incoming_protocol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_paths` json DEFAULT NULL,
  `incoming_date` date DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `incoming_document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outgoing_documents_reply_to_incoming_id_foreign` (`reply_to_incoming_id`),
  KEY `outgoing_documents_protocol_year_aa_index` (`protocol_year`,`aa`),
  CONSTRAINT `outgoing_documents_reply_to_incoming_id_foreign` FOREIGN KEY (`reply_to_incoming_id`) REFERENCES `incoming_documents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outgoing_documents`
--

LOCK TABLES `outgoing_documents` WRITE;
/*!40000 ALTER TABLE `outgoing_documents` DISABLE KEYS */;
INSERT INTO `outgoing_documents` VALUES (93,11,2013,NULL,'11','sf','2026/outgoing/2026-02-17/outgoing_11_084649_1.pdf',NULL,NULL,NULL,'sef','2026-02-27','fsef','ege','sf','2026-02-16 07:59:28','2026-02-17 06:46:50'),(94,13,2013,NULL,'13','drh','2026/outgoing/2026-02-16/outgoing_13_100245_1.pdf',NULL,NULL,NULL,'dgr','2026-02-19','drh','drhdr','dr','2026-02-16 08:02:45','2026-02-16 08:02:45'),(95,6,2013,82,'6',NULL,'2026/outgoing/2026-02-17/outgoing_6_074343_1.pdf',NULL,NULL,NULL,'sg',NULL,NULL,'vds',NULL,'2026-02-16 08:02:58','2026-02-17 05:43:43');
/*!40000 ALTER TABLE `outgoing_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `protocol_counters`
--

DROP TABLE IF EXISTS `protocol_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `protocol_counters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint unsigned NOT NULL DEFAULT '2026',
  `current` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `protocol_counters_year_unique` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `protocol_counters`
--

LOCK TABLES `protocol_counters` WRITE;
/*!40000 ALTER TABLE `protocol_counters` DISABLE KEYS */;
INSERT INTO `protocol_counters` VALUES (1,2026,0,'2026-02-06 09:01:14','2026-02-11 08:48:19'),(2,2016,1,'2026-02-16 06:10:01','2026-02-16 06:10:01'),(3,2013,14,'2026-02-16 07:17:56','2026-02-16 10:07:35');
/*!40000 ALTER TABLE `protocol_counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('ZlNkXlM1cKm1nX1A2a2IU4n3P80437EGTQHXnTqk',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiYUlnbGVOM29Yc3VtT2Z1b2lNeWlZR3Jmb0pnZXBRNjdUUlRoQTFUTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjEzOiJwcm90b2NvbF95ZWFyIjtpOjIwMTM7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czoyMDoicHJvdG9jb2xfeWVhcl9tYW51YWwiO2I6MTt9',1771328673);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@example.com',NULL,'$2y$12$kTVV5jyAmNE91DUDHhWSr.yW10zYVa43C8qMtxzTuOFYHe8RdnRgG',1,1,NULL,'2026-01-27 10:43:23','2026-02-02 10:19:28'),(2,'ΔΗΜΟΣΙΑ ΚΕΝΤΡΙΚΗ ΒΙΒΛΙΟΘΗΚΗ ΣΠΑΡΤΗΣ','biblspar@sch.gr',NULL,'$2y$12$IQhtJi5KvzmY9tHG50nuVe.Sp.58keP8pFpfr2kOaGvh48MtPy9Yi',0,1,NULL,'2026-02-05 05:50:36','2026-02-05 05:50:36');
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

-- Dump completed on 2026-02-17 13:45:20
