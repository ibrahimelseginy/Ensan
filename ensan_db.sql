-- MySQL dump 10.13  Distrib 5.7.24, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: ensan_db
-- ------------------------------------------------------
-- Server version	5.7.24
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
-- Table structure for table `accounts`
--
DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('asset','liability','equity','revenue','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounts_code_unique` (`code`),
  KEY `accounts_parent_id_foreign` (`parent_id`),
  CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,NULL,'102','donation_cash',NULL,'asset',NULL,NULL),(2,NULL,'120','Inventory - In Kind',NULL,'asset',NULL,NULL),(3,NULL,'401','Donations Revenue',NULL,'revenue',NULL,NULL),(4,NULL,'501','Operational Expense',NULL,'expense',NULL,NULL),(5,NULL,'502','Aid Expense',NULL,'expense',NULL,NULL),(6,NULL,'503','Logistics Expense',NULL,'expense',NULL,NULL);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachments_entity_type_entity_id_index` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
-- Dumping data for table `attachments`

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;
-- Table structure for table `audits`
DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` int(11) DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_user_id_foreign` (`user_id`),
  CONSTRAINT `audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits`
--

LOCK TABLES `audits` WRITE;
/*!40000 ALTER TABLE `audits` DISABLE KEYS */;
INSERT INTO `audits` VALUES (1,NULL,'POST','login',302,'::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"admin@ensan.local\", \"_token\": \"6hQEG7fcq72b7VyhsfPdEbT3PwAbytEiciZeTb1R\", \"password\": \"password\"}','2026-02-16 09:44:05','2026-02-16 09:44:05'),(2,NULL,'POST','login',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"password\": \"Password\", \"remember\": \"on\"}','2026-02-17 10:12:52','2026-02-17 10:12:52'),(3,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية تنتظر مساهمتك\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"انطلاق حملة الشتاء لتوزيع البطاطين والمواد الغذائية\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"stats_beneficiaries\": \"10,000+\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"#\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:13:13','2026-02-17 10:13:13'),(4,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية تنتظر مساهمتك\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"انطلاق حملة الشتاء لتوزيع البطاطين والمواد الغذائية\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"#\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:13:22','2026-02-17 10:13:22'),(5,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية تنتظر مساهمتك\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"#\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:14:39','2026-02-17 10:14:39'),(6,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية تنتظر مساهمتك\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"#\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:15:43','2026-02-17 10:15:43'),(7,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية تنتظر مساهمتك\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"#\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:17:07','2026-02-17 10:17:07'),(8,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية تنتظر مساهمتك\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:18:38','2026-02-17 10:18:38'),(9,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:22:51','2026-02-17 10:22:51'),(10,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:23:18','2026-02-17 10:23:18'),(11,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"10M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:29:33','2026-02-17 10:29:33'),(12,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"10M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:30:14','2026-02-17 10:30:14'),(13,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": null, \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"10M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"15K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": null, \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": null, \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:30:34','2026-02-17 10:30:34'),(14,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"10M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:31:30','2026-02-17 10:31:30'),(15,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"10M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:32:08','2026-02-17 10:32:08'),(16,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"10M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:32:30','2026-02-17 10:32:30'),(17,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"10M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:32:47','2026-02-17 10:32:47'),(18,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"10M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"محمد محموود\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:50:44','2026-02-17 10:50:44'),(19,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"10M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"200\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 10:51:02','2026-02-17 10:51:02'),(20,8,'POST','admin/website/guest-house-content',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"title\": \"دار الضيافة\", \"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"gh_stat1_label\": \"سرير\", \"gh_stat1_value\": \"+55\", \"gh_stat2_label\": \"مريض سنوياً\", \"gh_stat2_value\": \"+3000\", \"gh_stat3_label\": \"فرع\", \"gh_stat3_value\": \"2\", \"gh_stat4_label\": \"استقبال\", \"gh_stat4_value\": \"24/7\", \"gh_hero_subtitle\": \"ملاذ آمن للمرضى ومرافقيهم\"}','2026-02-17 10:53:17','2026-02-17 10:53:17'),(21,8,'POST','admin/website/projects-stats',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"stats_projects\": \"4\", \"stats_donations\": \"10M+\", \"stats_governorates\": \"2\", \"stats_beneficiaries\": \"200\"}','2026-02-17 12:42:02','2026-02-17 12:42:02'),(22,8,'POST','admin/website/projects-stats',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"stats_projects\": \"4\", \"stats_donations\": \"15M+\", \"stats_governorates\": \"2\", \"stats_beneficiaries\": \"400K\"}','2026-02-17 12:42:37','2026-02-17 12:42:37'),(23,8,'POST','admin/website/projects-stats',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"stats_projects\": \"4\", \"stats_donations\": \"15M+\", \"stats_governorates\": \"2\", \"stats_beneficiaries\": \"400K\"}','2026-02-17 12:42:56','2026-02-17 12:42:56'),(24,8,'DELETE','admin/website/projects/3',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:43:31','2026-02-17 12:43:31'),(25,8,'DELETE','admin/website/projects/4',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:43:37','2026-02-17 12:43:37'),(26,8,'DELETE','admin/website/projects/7',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:43:48','2026-02-17 12:43:48'),(27,8,'DELETE','admin/website/projects/8',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:43:55','2026-02-17 12:43:55'),(28,8,'DELETE','admin/website/projects/9',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:44:01','2026-02-17 12:44:01'),(29,8,'DELETE','admin/website/projects/10',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:44:07','2026-02-17 12:44:07'),(30,8,'DELETE','admin/website/projects/45',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:44:26','2026-02-17 12:44:26'),(31,8,'DELETE','admin/website/projects/1',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:44:33','2026-02-17 12:44:33'),(32,8,'DELETE','admin/website/projects/12',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:45:36','2026-02-17 12:45:36'),(33,8,'DELETE','admin/website/projects/13',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:45:42','2026-02-17 12:45:42'),(34,8,'DELETE','admin/website/projects/14',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:45:48','2026-02-17 12:45:48'),(35,8,'DELETE','admin/website/projects/15',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:45:54','2026-02-17 12:45:54'),(36,8,'DELETE','admin/website/projects/16',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:01','2026-02-17 12:46:01'),(37,8,'DELETE','admin/website/projects/17',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:07','2026-02-17 12:46:07'),(38,8,'DELETE','admin/website/projects/18',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:14','2026-02-17 12:46:14'),(39,8,'DELETE','admin/website/projects/11',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:21','2026-02-17 12:46:21'),(40,8,'DELETE','admin/website/projects/2',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:27','2026-02-17 12:46:27'),(41,8,'DELETE','admin/website/projects/20',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:32','2026-02-17 12:46:32'),(42,8,'DELETE','admin/website/projects/22',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:46:39','2026-02-17 12:46:39'),(43,8,'DELETE','admin/website/projects/44',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:06','2026-02-17 12:47:06'),(44,8,'DELETE','admin/website/projects/21',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:11','2026-02-17 12:47:11'),(45,8,'DELETE','admin/website/projects/23',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:17','2026-02-17 12:47:17'),(46,8,'DELETE','admin/website/projects/24',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:23','2026-02-17 12:47:23'),(47,8,'DELETE','admin/website/projects/25',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:29','2026-02-17 12:47:29'),(48,8,'DELETE','admin/website/projects/27',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:35','2026-02-17 12:47:35'),(49,8,'DELETE','admin/website/projects/19',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:41','2026-02-17 12:47:41'),(50,8,'DELETE','admin/website/projects/43',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:49','2026-02-17 12:47:49'),(51,8,'DELETE','admin/website/projects/29',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:47:55','2026-02-17 12:47:55'),(52,8,'DELETE','admin/website/projects/30',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:01','2026-02-17 12:48:01'),(53,8,'DELETE','admin/website/projects/28',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:10','2026-02-17 12:48:10'),(54,8,'DELETE','admin/website/projects/31',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:19','2026-02-17 12:48:19'),(55,8,'DELETE','admin/website/projects/32',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:24','2026-02-17 12:48:24'),(56,8,'DELETE','admin/website/projects/33',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:38','2026-02-17 12:48:38'),(57,8,'DELETE','admin/website/projects/34',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:44','2026-02-17 12:48:44'),(58,8,'DELETE','admin/website/projects/35',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:49','2026-02-17 12:48:49'),(59,8,'DELETE','admin/website/projects/36',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:48:54','2026-02-17 12:48:54'),(60,8,'DELETE','admin/website/projects/38',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:49:00','2026-02-17 12:49:00'),(61,8,'DELETE','admin/website/projects/39',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:49:07','2026-02-17 12:49:07'),(62,8,'DELETE','admin/website/projects/42',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:49:14','2026-02-17 12:49:14'),(63,8,'DELETE','admin/website/projects/40',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:49:20','2026-02-17 12:49:20'),(64,8,'DELETE','admin/website/projects/37',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:49:26','2026-02-17 12:49:26'),(65,8,'DELETE','admin/website/projects/41',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 12:49:57','2026-02-17 12:49:57'),(66,8,'POST','admin/website/projects',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"name\": \"مشروع بعثاء الأمل\", \"image\": {}, \"stats\": [{\"label\": \"كسوه\", \"value\": \"200\"}], \"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"category\": null, \"features\": [{\"text\": \"دعم طبي\"}], \"action_url\": null, \"badge_text\": null, \"is_visible\": \"on\", \"show_badge\": \"on\", \"action_text\": null, \"theme_colors\": {\"iconColor\": \"#0d6efd\", \"lightTint\": \"#c1ccdc\", \"borderColor\": \"#cfe2ff\", \"primaryColor\": \"#fd0dd1\"}, \"website_content\": null, \"short_description\": \"اكفل شخصاا\", \"sponsorship_details\": \"500\"}','2026-02-17 12:53:08','2026-02-17 12:53:08'),(67,8,'PUT','admin/website/projects/46',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"name\": \"مشروع بعثاء الأمل\", \"stats\": [{\"icon\": null, \"label\": \"طفل مستفيد\", \"value\": null}], \"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"PUT\", \"category\": \"أمل\", \"features\": [{\"icon\": null, \"text\": \"500+\"}, {\"icon\": null, \"text\": \"شامل\"}], \"action_url\": null, \"badge_text\": \"مساندة أطفال السرطان\", \"is_visible\": \"on\", \"show_badge\": \"on\", \"action_text\": null, \"theme_colors\": {\"iconColor\": \"#0d6efd\", \"lightTint\": \"#c1ccdc\", \"borderColor\": \"#cfe2ff\", \"primaryColor\": \"#fd0dd1\"}, \"website_content\": null, \"short_description\": \"مساندة أطفال السرطان وأسرتهم مادياً ومعنوياً وطبياً لتجاوز رحلة العلاج .\", \"sponsorship_details\": null}','2026-02-17 12:58:43','2026-02-17 12:58:43'),(68,8,'DELETE','admin/website/projects/26',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 13:00:18','2026-02-17 13:00:18'),(69,8,'POST','admin/website/projects-stats',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"stats_projects\": \"4\", \"stats_donations\": \"15M+\", \"stats_governorates\": \"1\", \"stats_beneficiaries\": \"400K\"}','2026-02-17 13:01:26','2026-02-17 13:01:26'),(70,8,'POST','admin/website/board',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"name\": \"Ibrahim Elseginy\", \"role\": \"مدير زاد\", \"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"sort_order\": \"1\", \"description\": \"رئيس زاد\"}','2026-02-17 13:02:55','2026-02-17 13:02:55'),(71,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_image\": {}, \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 13:08:14','2026-02-17 13:08:14'),(72,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 13:08:29','2026-02-17 13:08:29'),(73,8,'DELETE','admin/website/testimonials/1',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"_method\": \"DELETE\"}','2026-02-17 13:09:08','2026-02-17 13:09:08'),(74,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاحنا\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"10+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 13:09:51','2026-02-17 13:09:51'),(75,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"20+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 13:10:04','2026-02-17 13:10:04'),(76,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"campaign_stats_active\": \"8\", \"campaign_stats_donations\": \"2M+\", \"campaign_stats_governorates\": \"3 محافظة\", \"campaign_stats_beneficiaries\": \"15,000+\"}','2026-02-17 13:13:56','2026-02-17 13:13:56'),(77,8,'POST','admin/website/settings',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"featured_campaign_title\": \"حملة الشتاء 2025\", \"featured_campaign_progress\": \"65\", \"featured_campaign_button_text\": \"ساهم الآن\", \"featured_campaign_beneficiaries\": \"2,500+\"}','2026-02-17 13:15:06','2026-02-17 13:15:06'),(78,8,'POST','admin/website/volunteer-content',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.\", \"volunteer_stats_hours\": \"25,000+\", \"volunteer_stats_branches\": \"3\", \"volunteer_stats_volunteers\": \"1200+\"}','2026-02-17 13:20:32','2026-02-17 13:20:32'),(79,8,'POST','admin/website/volunteer-content',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.\", \"volunteer_stats_hours\": \"25,000+\", \"volunteer_stats_branches\": \"2\", \"volunteer_stats_volunteers\": \"1200+\"}','2026-02-17 13:21:25','2026-02-17 13:21:25'),(80,8,'POST','admin/website/volunteer-content',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.\", \"volunteer_stats_hours\": \"25,00+\", \"volunteer_stats_branches\": \"2\", \"volunteer_stats_volunteers\": \"1200+\"}','2026-02-17 13:21:54','2026-02-17 13:21:54'),(81,8,'POST','admin/website/volunteer-content',302,'192.168.1.145','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"eeMN8W6mp0oeYLRFjGM7E5uISTCM9QwCgZ1OHC3d\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.\", \"volunteer_stats_hours\": \"25,00+\", \"volunteer_stats_branches\": \"2\", \"volunteer_stats_volunteers\": \"42\"}','2026-02-17 13:22:24','2026-02-17 13:22:24'),(82,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"HSXnSjRqhMAywd8KjOx5ylAMHAvUzr04DSlEsI9f\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقياً\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"notification_link_text\": \"اعرف المزيد\"}','2026-02-17 14:31:09','2026-02-17 14:31:09'),(83,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"password\": \"password\"}','2026-02-17 14:39:34','2026-02-17 14:39:34'),(84,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"+8\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:41:40','2026-02-17 14:41:40'),(85,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"15M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:42:54','2026-02-17 14:42:54'),(86,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": null, \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:43:17','2026-02-17 14:43:17'),(87,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيد\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:45:55','2026-02-17 14:45:55'),(88,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+150K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:46:19','2026-02-17 14:46:19'),(89,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:47:28','2026-02-17 14:47:28'),(90,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"3 فروع\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:48:04','2026-02-17 14:48:04'),(91,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": null, \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"3 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"stats_projects_label\": \"المشاريع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:56:35','2026-02-17 14:56:35'),(92,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": \"20\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"3 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"400+\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"الفروع\", \"stats_projects_label\": \"المشاريع\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 14:58:20','2026-02-17 14:58:20'),(93,8,'POST','admin/website/volunteer-content',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.ووو\", \"volunteer_stats_hours\": \"25,00+\", \"volunteer_stats_branches\": \"2\", \"volunteer_stats_volunteers\": \"42\"}','2026-02-17 15:01:46','2026-02-17 15:01:46'),(94,8,'POST','admin/website/volunteer-content',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.\", \"volunteer_stats_hours\": \"25,00+\", \"volunteer_stats_branches\": \"2\", \"volunteer_stats_volunteers\": \"42\"}','2026-02-17 15:01:55','2026-02-17 15:01:55'),(95,8,'POST','admin/website/volunteer-content',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"volunteer_title\": \"تطوع معنا وكن جزءاً من التغيير الجامد\", \"volunteer_subtitle\": \"ساعات من وقتك تساوي حياة كاملة عند غيرك\", \"volunteer_description\": \"نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.\", \"volunteer_stats_hours\": \"25,00+\", \"volunteer_stats_branches\": \"2\", \"volunteer_stats_volunteers\": \"2\"}','2026-02-17 15:02:34','2026-02-17 15:02:34'),(96,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": \"20\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"2 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"+200\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"(كفرالشيخ والغربية)\", \"stats_projects_label\": \"مستفيد سنوياً\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 15:06:54','2026-02-17 15:06:54'),(97,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": \"20\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"2 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"+200\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"(كفرالشيخ والغربية)\", \"stats_projects_label\": \"مستفيد سنوياً\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 15:07:14','2026-02-17 15:07:14'),(98,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": \"20\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"2 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"+200\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"(كفرالشيخ والغربية)\", \"stats_projects_label\": \"مستفيد سنوياً\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 15:07:24','2026-02-17 15:07:24'),(99,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": \"20\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"2 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"+200\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"(كفرالشيخ والغربية)\", \"stats_projects_label\": \"مستفيد سنوياً\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 15:07:35','2026-02-17 15:07:35'),(100,8,'POST','admin/website/settings',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"Ihqg6dT7L4PxWrqlUBZqFmZLmwuVvdG9xXCBQI3b\", \"cta_text\": \"كل مساهمة منك تصنع فارقاً في حياة إنسان.\", \"cta_title\": \"كن جزءاً من قصة نجاح\", \"stats_shares\": \"20\", \"gh_home_title\": \"ضيافة إنسان الخيريه\", \"stats_branches\": \"2 فروع\", \"stats_projects\": \"4\", \"campaigns_title\": \"حملاتنا الجارية\", \"cta_stat1_label\": \"تبرعات\", \"cta_stat1_value\": \"50M+\", \"cta_stat2_label\": \"مستفيده\", \"cta_stat2_value\": \"150K+\", \"cta_stat3_label\": \"سنوات عطاء\", \"cta_stat3_value\": \"12+\", \"gh_home_content\": \"سلببببببببببببببببببببببببببببببببببببببببببب\", \"hero_stat_money\": \"+50M\", \"hero_stat_years\": \"12\", \"stats_donations\": \"12M+\", \"hero_description\": \"من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.\", \"hero_stat_smiles\": \"+15K\", \"stats_volunteers\": \"+200\", \"notification_text\": \"دار الضيافة\", \"campaigns_subtitle\": \"شارك في دعم الأيتام\", \"hero_title_primary\": \"معاً نصنع أثراً حقيقيا\", \"notification_label\": \"جديد\", \"stats_governorates\": \"1\", \"stats_shares_label\": \"الأسهم\", \"notification_active\": \"on\", \"stats_beneficiaries\": \"400K\", \"hero_title_secondary\": \"في حياة المحتاجين\", \"stats_branches_label\": \"(كفرالشيخ والغربية)\", \"stats_projects_label\": \"مستفيد سنوياً\", \"notification_link_url\": \"http://192.168.1.145:4200/diyafa\", \"stats_donations_label\": \"التبرعات\", \"notification_link_text\": \"اعرف المزيد\", \"stats_volunteers_label\": \"المتطوعون\", \"stats_governorates_label\": \"المحافظات\", \"stats_beneficiaries_label\": \"المستفيدون\"}','2026-02-17 15:15:36','2026-02-17 15:15:36'),(101,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"uG3Jkm7e10DmvsILXdmPJqVEUA8GmwATgsmwcs6v\", \"password\": \"password\"}','2026-02-17 15:30:40','2026-02-17 15:30:40'),(102,8,'POST','admin/website/guest-house-content',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"title\": \"دار الضيافة\", \"_token\": \"uG3Jkm7e10DmvsILXdmPJqVEUA8GmwATgsmwcs6v\", \"gh_stat1_label\": \"سرير\", \"gh_stat1_value\": \"+55\", \"gh_stat2_label\": \"مريض سنوياً\", \"gh_stat2_value\": \"+3000\", \"gh_stat3_label\": \"فرع\", \"gh_stat3_value\": \"23\", \"gh_stat4_label\": \"استقبال\", \"gh_stat4_value\": \"24/7\", \"gh_hero_subtitle\": \"ملاذ آمن للمرضى ومرافقيهم\"}','2026-02-17 15:43:27','2026-02-17 15:43:27'),(103,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"7DiKBb9pzYGx6Re3lwFQkbD3d7AZhyTv1iCPtZuI\", \"password\": \"password\"}','2026-02-17 15:43:43','2026-02-17 15:43:43'),(104,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"15E7b1cJmDE6PU3jRnxPMc4NmC1DZwarReWAQJSU\", \"password\": \"password\"}','2026-02-17 16:14:54','2026-02-17 16:14:54'),(105,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"15E7b1cJmDE6PU3jRnxPMc4NmC1DZwarReWAQJSU\", \"password\": \"password\"}','2026-02-17 16:14:55','2026-02-17 16:14:55'),(106,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"vNCZdTL8hT6LGVFZYvpONrTInnUE8lvqFwi3vnqe\", \"password\": \"password\"}','2026-02-17 16:22:52','2026-02-17 16:22:52'),(107,8,'POST','admin/website/headquarters',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"vNCZdTL8hT6LGVFZYvpONrTInnUE8lvqFwi3vnqe\", \"headquarters_title\": \"مقر مؤسسة إنسان\", \"headquarters_description\": \"متواجدون في محافظة كفر الشيخ والغربية. زورونا في أي فرع من فروعنا للتبرع أو الاستفسار\", \"headquarters_stats_donors\": \"+10K\", \"headquarters_stats_branches\": \"1\", \"headquarters_stats_employees\": \"+200\", \"headquarters_stats_governorates\": \"2\"}','2026-02-17 16:33:43','2026-02-17 16:33:43'),(108,8,'DELETE','admin/website/partners/2',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"_token\": \"vNCZdTL8hT6LGVFZYvpONrTInnUE8lvqFwi3vnqe\", \"_method\": \"DELETE\"}','2026-02-17 16:36:51','2026-02-17 16:36:51'),(109,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"uYpx1q4UdpCpR9B7qoEQGq7RbUhWm9b6WxM02jnv\", \"password\": \"password\", \"remember\": \"on\"}','2026-02-18 05:16:07','2026-02-18 05:16:07'),(110,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"uYpx1q4UdpCpR9B7qoEQGq7RbUhWm9b6WxM02jnv\", \"password\": \"password\", \"remember\": \"on\"}','2026-02-18 05:16:09','2026-02-18 05:16:09'),(111,NULL,'POST','login',302,'192.168.1.118','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"y5PkjG2Oykyek8zP3rgNqMy0oa5bJNZIIIlsliDr\", \"password\": \"حشسسصخقي\"}','2026-02-18 05:31:10','2026-02-18 05:31:10'),(112,NULL,'POST','login',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"email\": \"IbrahimElfil@gmail.com\", \"_token\": \"vo5wERVFxrLij3oSthFZlIRCkKOdMKrIe2R7eiUr\", \"password\": \"password\"}','2026-03-01 15:41:34','2026-03-01 15:41:34'),(113,8,'PUT','admin/website/partners/1',302,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'{\"logo\": {}, \"name\": \"شركة النور القابضة\", \"type\": \"platinum\", \"_token\": \"vo5wERVFxrLij3oSthFZlIRCkKOdMKrIe2R7eiUr\", \"_method\": \"PUT\", \"description\": \"شريك استراتيجي في حملات الكفالة والإطعام.\", \"website_url\": \"https://example.com\"}','2026-03-01 15:50:09','2026-03-01 15:50:09');
/*!40000 ALTER TABLE `audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiaries`
--

DROP TABLE IF EXISTS `beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beneficiaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistance_type` enum('financial','in_kind','service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','under_review','accepted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `beneficiaries_code_unique` (`code`),
  KEY `beneficiaries_project_id_foreign` (`project_id`),
  KEY `beneficiaries_campaign_id_foreign` (`campaign_id`),
  KEY `beneficiaries_guest_house_id_foreign` (`guest_house_id`),
  CONSTRAINT `beneficiaries_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `beneficiaries_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `beneficiaries_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiaries`
--

LOCK TABLES `beneficiaries` WRITE;
/*!40000 ALTER TABLE `beneficiaries` DISABLE KEYS */;
INSERT INTO `beneficiaries` VALUES (1,NULL,'أسرة أم محمد','29001011234567','01100000007','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL),(2,NULL,'الطفل كريم','31505051234567','01100000006','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL),(3,NULL,'الحاجة فاطمة','26002021234567','01100000003','القاهرة - عنوان تجريبي','in_kind','under_review',NULL,NULL,NULL,NULL,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL),(4,NULL,'الطالب عمر','30503031234567','01100000002','القاهرة - عنوان تجريبي','service','new',NULL,NULL,NULL,NULL,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL),(5,NULL,'أسرة أم محمد','29001011234567','01100000008','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL),(6,NULL,'الطفل كريم','31505051234567','01100000005','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL),(7,NULL,'الحاجة فاطمة','26002021234567','01100000008','القاهرة - عنوان تجريبي','in_kind','under_review',NULL,NULL,NULL,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL),(8,NULL,'الطالب عمر','30503031234567','01100000001','القاهرة - عنوان تجريبي','service','new',NULL,NULL,NULL,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL),(9,NULL,'أسرة أم محمد','29001011234567','01100000002','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL),(10,NULL,'الطفل كريم','31505051234567','01100000003','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL),(11,NULL,'الحاجة فاطمة','26002021234567','01100000007','القاهرة - عنوان تجريبي','in_kind','under_review',NULL,NULL,NULL,NULL,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL),(12,NULL,'الطالب عمر','30503031234567','01100000001','القاهرة - عنوان تجريبي','service','new',NULL,NULL,NULL,NULL,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL),(13,NULL,'أسرة أم محمد','29001011234567','01100000006','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL),(14,NULL,'الطفل كريم','31505051234567','01100000007','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL),(15,NULL,'الحاجة فاطمة','26002021234567','01100000007','القاهرة - عنوان تجريبي','in_kind','under_review',NULL,NULL,NULL,NULL,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL),(16,NULL,'الطالب عمر','30503031234567','01100000004','القاهرة - عنوان تجريبي','service','new',NULL,NULL,NULL,NULL,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL),(17,NULL,'أسرة أم محمد','29001011234567','01100000003','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL),(18,NULL,'الطفل كريم','31505051234567','01100000000','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL),(19,NULL,'الحاجة فاطمة','26002021234567','01100000001','القاهرة - عنوان تجريبي','in_kind','under_review',NULL,NULL,NULL,NULL,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL),(20,NULL,'الطالب عمر','30503031234567','01100000007','القاهرة - عنوان تجريبي','service','new',NULL,NULL,NULL,NULL,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL),(21,NULL,'أسرة أم محمد','29001011234567','01100000001','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL),(22,NULL,'الطفل كريم','31505051234567','01100000003','القاهرة - عنوان تجريبي','financial','accepted',NULL,NULL,NULL,NULL,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL),(23,NULL,'الحاجة فاطمة','26002021234567','01100000007','القاهرة - عنوان تجريبي','in_kind','under_review',NULL,NULL,NULL,NULL,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL),(24,NULL,'الطالب عمر','30503031234567','01100000003','القاهرة - عنوان تجريبي','service','new',NULL,NULL,NULL,NULL,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL);
/*!40000 ALTER TABLE `beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_daily_menus`
--

DROP TABLE IF EXISTS `campaign_daily_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_daily_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `day_date` date NOT NULL,
  `responsible_user_id` bigint(20) unsigned DEFAULT NULL,
  `meal_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meal_count` int(11) NOT NULL DEFAULT '0',
  `ingredients` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_daily_menus_campaign_id_foreign` (`campaign_id`),
  KEY `campaign_daily_menus_responsible_user_id_foreign` (`responsible_user_id`),
  CONSTRAINT `campaign_daily_menus_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_daily_menus_responsible_user_id_foreign` FOREIGN KEY (`responsible_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_daily_menus`
--

LOCK TABLES `campaign_daily_menus` WRITE;
/*!40000 ALTER TABLE `campaign_daily_menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_daily_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_monthly_volunteers`
--

DROP TABLE IF EXISTS `campaign_monthly_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_monthly_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_monthly_volunteers_campaign_id_foreign` (`campaign_id`),
  KEY `campaign_monthly_volunteers_user_id_foreign` (`user_id`),
  CONSTRAINT `campaign_monthly_volunteers_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_monthly_volunteers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_monthly_volunteers`
--

LOCK TABLES `campaign_monthly_volunteers` WRITE;
/*!40000 ALTER TABLE `campaign_monthly_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_monthly_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_volunteers`
--

DROP TABLE IF EXISTS `campaign_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` date DEFAULT NULL,
  `hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_volunteers_campaign_id_user_id_unique` (`campaign_id`,`user_id`),
  KEY `campaign_volunteers_user_id_foreign` (`user_id`),
  CONSTRAINT `campaign_volunteers_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_volunteers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_volunteers`
--

LOCK TABLES `campaign_volunteers` WRITE;
/*!40000 ALTER TABLE `campaign_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_amount` decimal(15,2) DEFAULT NULL,
  `season_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season_year` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `manager_user_id` bigint(20) unsigned DEFAULT NULL,
  `deputy_user_id` bigint(20) unsigned DEFAULT NULL,
  `deputy_photo_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_photo_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_content` text COLLATE utf8mb4_unicode_ci,
  `goal_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `goal_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'جنيه',
  `current_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `beneficiaries_count` int(11) NOT NULL DEFAULT '0',
  `mobile_content` text COLLATE utf8mb4_unicode_ci,
  `show_on_mobile` tinyint(1) NOT NULL DEFAULT '1',
  `share_price` decimal(15,2) DEFAULT '0.00',
  `ui_contribute_btn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_remind_btn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_ended_btn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_filter_upcoming` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_collected_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_benefited_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_share_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ui_goal_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaigns_manager_user_id_foreign` (`manager_user_id`),
  KEY `campaigns_deputy_user_id_foreign` (`deputy_user_id`),
  CONSTRAINT `campaigns_deputy_user_id_foreign` FOREIGN KEY (`deputy_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `campaigns_manager_user_id_foreign` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaigns`
--

LOCK TABLES `campaigns` WRITE;
/*!40000 ALTER TABLE `campaigns` DISABLE KEYS */;
INSERT INTO `campaigns` VALUES (1,NULL,'حملة الشتاء',NULL,NULL,2026,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,NULL,'حملة رمضان',NULL,NULL,2026,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,NULL,'حملة المدارس',NULL,NULL,2026,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,NULL,'عيد الفطر',NULL,NULL,2026,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,NULL,'عيد الأضحى',NULL,NULL,2026,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,11,'حملة كفالة اليتيم - رمضان',131106.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:28','2026-02-16 10:01:28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,12,'حملة إطعام الطعام - رمضان',104662.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:28','2026-02-16 10:01:28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,13,'حملة سقيا الماء - رمضان',139853.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:28','2026-02-16 10:01:28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,14,'حملة كفالة اليتيم - رمضان',483746.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:55','2026-02-16 10:01:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,15,'حملة إطعام الطعام - رمضان',328919.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:55','2026-02-16 10:01:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,16,'حملة سقيا الماء - رمضان',353913.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:55','2026-02-16 10:01:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,17,'حملة التعليم للجميع - رمضان',156657.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:01:55','2026-02-16 10:01:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,18,'حملة كفالة اليتيم - رمضان',341145.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,19,'حملة إطعام الطعام - رمضان',303862.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,20,'حملة سقيا الماء - رمضان',194884.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,21,'حملة التعليم للجميع - رمضان',237892.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,22,'حملة كفالة اليتيم - رمضان',394830.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,23,'حملة إطعام الطعام - رمضان',496803.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,24,'حملة سقيا الماء - رمضان',188383.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,25,'حملة التعليم للجميع - رمضان',258050.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,26,'حملة كفالة اليتيم - رمضان',330396.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,27,'حملة إطعام الطعام - رمضان',72058.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,28,'حملة سقيا الماء - رمضان',438978.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,29,'حملة التعليم للجميع - رمضان',328996.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,30,'حملة كفالة اليتيم - رمضان',467903.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,31,'حملة إطعام الطعام - رمضان',211061.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,32,'حملة سقيا الماء - رمضان',268122.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,33,'حملة التعليم للجميع - رمضان',242127.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,34,'حملة كفالة اليتيم - رمضان',369561.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,35,'حملة إطعام الطعام - رمضان',85897.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,36,'حملة سقيا الماء - رمضان',312383.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,37,'حملة التعليم للجميع - رمضان',236877.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,38,'حملة كفالة اليتيم - رمضان',62099.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,39,'حملة إطعام الطعام - رمضان',395134.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,40,'حملة سقيا الماء - رمضان',74021.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,41,'حملة التعليم للجميع - رمضان',358058.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,42,'حملة كفالة اليتيم - رمضان',229704.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,43,'حملة إطعام الطعام - رمضان',305259.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,44,'حملة سقيا الماء - رمضان',73378.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,45,'حملة التعليم للجميع - رمضان',479523.00,NULL,2026,'2026-02-16','2026-03-16','active','2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,'جنيه',0.00,0,NULL,1,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_requests`
--

DROP TABLE IF EXISTS `change_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `change_requests_user_id_foreign` (`user_id`),
  KEY `change_requests_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `change_requests_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `change_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_requests`
--

LOCK TABLES `change_requests` WRITE;
/*!40000 ALTER TABLE `change_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `change_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaints` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `source_type` enum('donor','beneficiary','employee') COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `against_user_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('open','in_progress','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaints_against_user_id_foreign` (`against_user_id`),
  KEY `complaints_source_type_source_id_index` (`source_type`,`source_id`),
  CONSTRAINT `complaints_against_user_id_foreign` FOREIGN KEY (`against_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delegate_ratings`
--

DROP TABLE IF EXISTS `delegate_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delegate_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delegate_id` bigint(20) unsigned NOT NULL,
  `trip_id` bigint(20) unsigned DEFAULT NULL,
  `rated_by` bigint(20) unsigned DEFAULT NULL,
  `punctuality_rating` int(11) NOT NULL DEFAULT '5',
  `professionalism_rating` int(11) NOT NULL DEFAULT '5',
  `communication_rating` int(11) NOT NULL DEFAULT '5',
  `overall_rating` int(11) NOT NULL DEFAULT '5',
  `comments` text COLLATE utf8mb4_unicode_ci,
  `rating_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delegate_ratings_delegate_id_foreign` (`delegate_id`),
  KEY `delegate_ratings_trip_id_foreign` (`trip_id`),
  KEY `delegate_ratings_rated_by_foreign` (`rated_by`),
  CONSTRAINT `delegate_ratings_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delegate_ratings_rated_by_foreign` FOREIGN KEY (`rated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delegate_ratings_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `delegate_trips` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delegate_ratings`
--

LOCK TABLES `delegate_ratings` WRITE;
/*!40000 ALTER TABLE `delegate_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `delegate_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delegate_trips`
--

DROP TABLE IF EXISTS `delegate_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delegate_trips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delegate_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fuel_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_expenses` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delegate_trips_delegate_id_foreign` (`delegate_id`),
  KEY `delegate_trips_journal_entry_id_foreign` (`journal_entry_id`),
  CONSTRAINT `delegate_trips_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delegate_trips_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delegate_trips`
--

LOCK TABLES `delegate_trips` WRITE;
/*!40000 ALTER TABLE `delegate_trips` DISABLE KEYS */;
/*!40000 ALTER TABLE `delegate_trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delegates`
--

DROP TABLE IF EXISTS `delegates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delegates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_id` bigint(20) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `vehicle_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_plate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `emergency_contact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `hire_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delegates_route_id_foreign` (`route_id`),
  KEY `delegates_user_id_foreign` (`user_id`),
  CONSTRAINT `delegates_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `travel_routes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delegates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delegates`
--

LOCK TABLES `delegates` WRITE;
/*!40000 ALTER TABLE `delegates` DISABLE KEYS */;
INSERT INTO `delegates` VALUES (1,'مندوب راضي القاهرة','01000000001',NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-16 09:38:36','2026-02-16 09:38:36',NULL,NULL),(2,'مندوب راضي الجيزة','01000000002',NULL,2,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-16 09:38:36','2026-02-16 09:38:36',NULL,NULL),(3,'مندوب راضي القليوبية','01000000003',NULL,3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-16 09:38:36','2026-02-16 09:38:36',NULL,NULL),(4,'مندوب راضي الإسكندرية','01000000004',NULL,4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-16 09:38:36','2026-02-16 09:38:36',NULL,NULL),(5,'مندوب راضي الشرقية','01000000005',NULL,11,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-16 09:38:36','2026-02-16 09:38:36',NULL,NULL);
/*!40000 ALTER TABLE `delegates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donor_id` bigint(20) unsigned NOT NULL,
  `type` enum('cash','in_kind') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash_channel` enum('cash','instapay','vodafone_cash') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `receipt_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_value` decimal(12,2) DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `treasury_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `delegate_id` bigint(20) unsigned DEFAULT NULL,
  `route_id` bigint(20) unsigned DEFAULT NULL,
  `allocation_note` text COLLATE utf8mb4_unicode_ci,
  `received_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `auto_added_to_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `donations_donor_id_foreign` (`donor_id`),
  KEY `donations_project_id_foreign` (`project_id`),
  KEY `donations_campaign_id_foreign` (`campaign_id`),
  KEY `donations_warehouse_id_foreign` (`warehouse_id`),
  KEY `donations_delegate_id_foreign` (`delegate_id`),
  KEY `donations_route_id_foreign` (`route_id`),
  KEY `donations_guest_house_id_foreign` (`guest_house_id`),
  KEY `donations_treasury_id_foreign` (`treasury_id`),
  KEY `donations_item_id_foreign` (`item_id`),
  KEY `donations_created_by_foreign` (`created_by`),
  KEY `donations_cancelled_by_foreign` (`cancelled_by`),
  CONSTRAINT `donations_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_donor_id_foreign` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `donations_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `travel_routes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_treasury_id_foreign` FOREIGN KEY (`treasury_id`) REFERENCES `treasuries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donations`
--

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
INSERT INTO `donations` VALUES (1,18,'cash',NULL,369.00,'EGP','REC-4204',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-18 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(2,13,'cash',NULL,2142.00,'EGP','REC-5307',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-22 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(3,2,'cash',NULL,4893.00,'EGP','REC-5033',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(4,18,'cash',NULL,376.00,'EGP','REC-4021',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-29 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(5,2,'cash',NULL,4291.00,'EGP','REC-7577',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-27 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(6,18,'cash',NULL,726.00,'EGP','REC-5199',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-22 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(7,8,'cash',NULL,4793.00,'EGP','REC-9593',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-31 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(8,14,'cash',NULL,4803.00,'EGP','REC-7727',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-25 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(9,4,'cash',NULL,1559.00,'EGP','REC-4988',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(10,18,'cash',NULL,3964.00,'EGP','REC-3481',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(11,12,'cash',NULL,1604.00,'EGP','REC-9029',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-13 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(12,12,'cash',NULL,148.00,'EGP','REC-9760',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(13,6,'cash',NULL,4102.00,'EGP','REC-5021',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(14,15,'cash',NULL,4747.00,'EGP','REC-5068',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-17 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(15,4,'cash',NULL,797.00,'EGP','REC-2192',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(16,6,'cash',NULL,434.00,'EGP','REC-3300',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-21 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(17,10,'cash',NULL,801.00,'EGP','REC-9004',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-29 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(18,13,'cash',NULL,3966.00,'EGP','REC-9367',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-26 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(19,6,'cash',NULL,3747.00,'EGP','REC-6251',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(20,3,'cash',NULL,2261.00,'EGP','REC-4600',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:07:24','active',NULL,1,0,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(21,18,'cash',NULL,2600.00,'EGP','REC-6183',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-03 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(22,1,'cash',NULL,2183.00,'EGP','REC-5260',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-17 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(23,2,'cash',NULL,959.00,'EGP','REC-7737',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-29 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(24,26,'cash',NULL,613.00,'EGP','REC-2243',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(25,24,'cash',NULL,2843.00,'EGP','REC-6935',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-17 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(26,18,'cash',NULL,2488.00,'EGP','REC-9547',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-09 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(27,8,'cash',NULL,1450.00,'EGP','REC-1292',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-27 10:07:53','active',NULL,1,0,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(28,24,'cash',NULL,4810.00,'EGP','REC-4066',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:07:53','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(29,10,'cash',NULL,3114.00,'EGP','REC-3356',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(30,14,'cash',NULL,3502.00,'EGP','REC-5425',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-21 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(31,20,'cash',NULL,4117.00,'EGP','REC-6849',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-12 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(32,5,'cash',NULL,124.00,'EGP','REC-3765',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-03 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(33,18,'cash',NULL,1674.00,'EGP','REC-8646',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-21 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(34,22,'cash',NULL,2018.00,'EGP','REC-8252',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(35,9,'cash',NULL,2514.00,'EGP','REC-9614',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(36,8,'cash',NULL,2718.00,'EGP','REC-4471',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-05 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(37,22,'cash',NULL,2657.00,'EGP','REC-8085',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-18 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(38,2,'cash',NULL,1845.00,'EGP','REC-8518',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-20 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(39,6,'cash',NULL,162.00,'EGP','REC-5170',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(40,26,'cash',NULL,3877.00,'EGP','REC-5296',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:07:54','active',NULL,1,0,'2026-02-16 10:07:54','2026-02-16 10:07:54',NULL,NULL),(41,24,'cash',NULL,3366.00,'EGP','REC-9280',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-14 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(42,13,'cash',NULL,2595.00,'EGP','REC-6558',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-12 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(43,31,'cash',NULL,3382.00,'EGP','REC-8867',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-25 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(44,29,'cash',NULL,3099.00,'EGP','REC-6647',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(45,14,'cash',NULL,3639.00,'EGP','REC-1569',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-28 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(46,20,'cash',NULL,3563.00,'EGP','REC-3115',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-10 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(47,30,'cash',NULL,3900.00,'EGP','REC-4236',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-19 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(48,9,'cash',NULL,3842.00,'EGP','REC-9286',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-31 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(49,29,'cash',NULL,927.00,'EGP','REC-9123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-14 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(50,26,'cash',NULL,3981.00,'EGP','REC-1472',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-13 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(51,25,'cash',NULL,3331.00,'EGP','REC-2767',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-29 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(52,3,'cash',NULL,4057.00,'EGP','REC-7526',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-23 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(53,6,'cash',NULL,1694.00,'EGP','REC-4580',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-30 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(54,27,'cash',NULL,1318.00,'EGP','REC-9307',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-12 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(55,35,'cash',NULL,185.00,'EGP','REC-4091',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(56,25,'cash',NULL,3104.00,'EGP','REC-4015',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-05 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(57,21,'cash',NULL,1412.00,'EGP','REC-2483',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-23 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(58,31,'cash',NULL,4979.00,'EGP','REC-3005',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-30 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(59,11,'cash',NULL,1533.00,'EGP','REC-5336',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-30 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(60,17,'cash',NULL,3863.00,'EGP','REC-6485',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-30 10:08:22','active',NULL,1,0,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(61,42,'cash',NULL,2950.00,'EGP','REC-5459',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(62,21,'cash',NULL,1020.00,'EGP','REC-7463',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-21 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(63,29,'cash',NULL,1860.00,'EGP','REC-6896',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(64,15,'cash',NULL,4055.00,'EGP','REC-8913',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(65,25,'cash',NULL,1580.00,'EGP','REC-5751',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-28 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(66,21,'cash',NULL,2595.00,'EGP','REC-1440',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(67,2,'cash',NULL,2494.00,'EGP','REC-2836',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-31 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(68,1,'cash',NULL,3052.00,'EGP','REC-1620',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-31 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(69,34,'cash',NULL,2891.00,'EGP','REC-4951',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-03 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(70,42,'cash',NULL,496.00,'EGP','REC-5041',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-31 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(71,31,'cash',NULL,2569.00,'EGP','REC-1557',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(72,38,'cash',NULL,3033.00,'EGP','REC-4116',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-17 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(73,18,'cash',NULL,3979.00,'EGP','REC-8259',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-31 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(74,15,'cash',NULL,1069.00,'EGP','REC-5677',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-30 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(75,2,'cash',NULL,581.00,'EGP','REC-9230',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-29 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(76,25,'cash',NULL,1475.00,'EGP','REC-5290',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-20 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(77,4,'cash',NULL,3755.00,'EGP','REC-6592',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-11 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(78,22,'cash',NULL,192.00,'EGP','REC-2438',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-01 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(79,12,'cash',NULL,2759.00,'EGP','REC-5492',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-07 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(80,4,'cash',NULL,359.00,'EGP','REC-2209',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-24 10:08:54','active',NULL,1,0,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(81,41,'cash',NULL,2223.00,'EGP','REC-6653',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-18 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(82,19,'cash',NULL,1518.00,'EGP','REC-3153',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-08 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(83,25,'cash',NULL,3947.00,'EGP','REC-7018',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-19 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(84,19,'cash',NULL,3714.00,'EGP','REC-2289',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-19 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(85,7,'cash',NULL,4101.00,'EGP','REC-5374',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-12 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(86,49,'cash',NULL,765.00,'EGP','REC-6627',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-20 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(87,47,'cash',NULL,413.00,'EGP','REC-3230',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-21 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(88,38,'cash',NULL,3086.00,'EGP','REC-7039',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-05 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(89,43,'cash',NULL,2108.00,'EGP','REC-3021',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-16 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(90,11,'cash',NULL,4262.00,'EGP','REC-8039',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-26 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(91,33,'cash',NULL,3310.00,'EGP','REC-8640',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(92,5,'cash',NULL,530.00,'EGP','REC-4516',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-25 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(93,17,'cash',NULL,283.00,'EGP','REC-3173',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-28 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(94,15,'cash',NULL,2647.00,'EGP','REC-9513',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-25 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(95,45,'cash',NULL,788.00,'EGP','REC-1377',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-14 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(96,12,'cash',NULL,370.00,'EGP','REC-4164',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-10 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(97,26,'cash',NULL,1555.00,'EGP','REC-5073',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-25 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(98,29,'cash',NULL,1167.00,'EGP','REC-2819',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-28 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(99,22,'cash',NULL,643.00,'EGP','REC-5517',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(100,45,'cash',NULL,4073.00,'EGP','REC-5220',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-15 10:09:22','active',NULL,1,0,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL);
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donors`
--

DROP TABLE IF EXISTS `donors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('individual','organization') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `classification` enum('one_time','recurring') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'one_time',
  `recurring_cycle` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sponsorship_type` enum('none','monthly_sponsor','sadaqa_jariya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `sponsored_beneficiary_id` bigint(20) unsigned DEFAULT NULL,
  `sponsorship_project_id` bigint(20) unsigned DEFAULT NULL,
  `sponsorship_monthly_amount` decimal(12,2) DEFAULT NULL,
  `allocation_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'project, campaign, sponsorship, guest_house, sadaqa_jariya',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donors_campaign_id_foreign` (`campaign_id`),
  KEY `donors_guest_house_id_foreign` (`guest_house_id`),
  CONSTRAINT `donors_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donors_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donors`
--

LOCK TABLES `donors` WRITE;
/*!40000 ALTER TABLE `donors` DISABLE KEYS */;
INSERT INTO `donors` VALUES (1,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(2,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(3,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(4,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(5,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(6,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(7,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20',NULL,NULL),(8,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(9,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(10,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(11,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(12,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(13,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(14,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06',NULL,NULL),(15,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(16,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(17,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(18,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(19,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(20,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(21,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24',NULL,NULL),(22,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(23,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(24,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(25,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(26,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(27,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(28,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53',NULL,NULL),(29,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(30,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(31,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(32,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(33,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(34,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(35,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22',NULL,NULL),(36,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(37,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(38,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(39,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(40,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(41,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(42,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54',NULL,NULL),(43,'محمد أحمد','individual','01000000000','donor0@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(44,'أحمد علي','individual','01000000001','donor1@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(45,'سارة محمود','individual','01000000002','donor2@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(46,'منى خليل','individual','01000000003','donor3@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(47,'خالد يوسف','individual','01000000004','donor4@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(48,'شركة النور','individual','01000000005','donor5@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL),(49,'مؤسسة الخير','organization','01000000006','donor6@test.com',NULL,'one_time',NULL,'none',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22',NULL,NULL);
/*!40000 ALTER TABLE `donors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_attendances`
--

DROP TABLE IF EXISTS `employee_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in_at` time DEFAULT NULL,
  `check_out_at` time DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `evaluation_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_attendances_user_id_foreign` (`user_id`),
  CONSTRAINT `employee_attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attendances`
--

LOCK TABLES `employee_attendances` WRITE;
/*!40000 ALTER TABLE `employee_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('operational','aid','logistics') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `description` text COLLATE utf8mb4_unicode_ci,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  `beneficiary_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `paid_at` date DEFAULT NULL,
  `status` enum('active','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `workspace_id` bigint(20) unsigned DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `expenses_project_id_foreign` (`project_id`),
  KEY `expenses_campaign_id_foreign` (`campaign_id`),
  KEY `expenses_beneficiary_id_foreign` (`beneficiary_id`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_guest_house_id_foreign` (`guest_house_id`),
  KEY `expenses_workspace_id_foreign` (`workspace_id`),
  KEY `expenses_cancelled_by_foreign` (`cancelled_by`),
  CONSTRAINT `expenses_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field_visits`
--

DROP TABLE IF EXISTS `field_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `beneficiary_id` bigint(20) unsigned NOT NULL,
  `researcher_id` bigint(20) unsigned NOT NULL,
  `visit_date` date NOT NULL,
  `status` enum('scheduled','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `report` text COLLATE utf8mb4_unicode_ci,
  `recommendation` enum('approve','reject','needs_review') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_visits_beneficiary_id_foreign` (`beneficiary_id`),
  KEY `field_visits_researcher_id_foreign` (`researcher_id`),
  CONSTRAINT `field_visits_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `field_visits_researcher_id_foreign` FOREIGN KEY (`researcher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_visits`
--

LOCK TABLES `field_visits` WRITE;
/*!40000 ALTER TABLE `field_visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `field_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_closures`
--

DROP TABLE IF EXISTS `financial_closures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_closures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `branch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closed_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_closures_closed_by_foreign` (`closed_by`),
  KEY `financial_closures_approved_by_foreign` (`approved_by`),
  CONSTRAINT `financial_closures_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financial_closures_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_closures`
--

LOCK TABLES `financial_closures` WRITE;
/*!40000 ALTER TABLE `financial_closures` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_closures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_house_monthly_volunteers`
--

DROP TABLE IF EXISTS `guest_house_monthly_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_house_monthly_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guest_house_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_house_monthly_volunteers_user_id_foreign` (`user_id`),
  KEY `guest_house_monthly_volunteers_guest_house_id_year_month_index` (`guest_house_id`,`year`,`month`),
  CONSTRAINT `guest_house_monthly_volunteers_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guest_house_monthly_volunteers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_house_monthly_volunteers`
--

LOCK TABLES `guest_house_monthly_volunteers` WRITE;
/*!40000 ALTER TABLE `guest_house_monthly_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `guest_house_monthly_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_house_volunteers`
--

DROP TABLE IF EXISTS `guest_house_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_house_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guest_house_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` date DEFAULT NULL,
  `hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guest_house_volunteers_guest_house_id_user_id_unique` (`guest_house_id`,`user_id`),
  KEY `guest_house_volunteers_user_id_foreign` (`user_id`),
  CONSTRAINT `guest_house_volunteers_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guest_house_volunteers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_house_volunteers`
--

LOCK TABLES `guest_house_volunteers` WRITE;
/*!40000 ALTER TABLE `guest_house_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `guest_house_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_houses`
--

DROP TABLE IF EXISTS `guest_houses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_houses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` enum('active','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `manager_user_id` bigint(20) unsigned DEFAULT NULL,
  `manager_photo_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_houses_manager_user_id_foreign` (`manager_user_id`),
  CONSTRAINT `guest_houses_manager_user_id_foreign` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_houses`
--

LOCK TABLES `guest_houses` WRITE;
/*!40000 ALTER TABLE `guest_houses` DISABLE KEYS */;
INSERT INTO `guest_houses` VALUES (1,'دار ضيافة طنطا',NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL),(2,'دار ضيافة كفر الشيخ',NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `guest_houses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transactions`
--

DROP TABLE IF EXISTS `inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `type` enum('in','transfer','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `source_donation_id` bigint(20) unsigned DEFAULT NULL,
  `beneficiary_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `expense_id` bigint(20) unsigned DEFAULT NULL,
  `delegate_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `location_in_warehouse` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transactions_item_id_foreign` (`item_id`),
  KEY `inventory_transactions_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_transactions_source_donation_id_index` (`source_donation_id`),
  KEY `inventory_transactions_beneficiary_id_foreign` (`beneficiary_id`),
  KEY `inventory_transactions_project_id_foreign` (`project_id`),
  KEY `inventory_transactions_campaign_id_foreign` (`campaign_id`),
  KEY `inventory_transactions_expense_id_foreign` (`expense_id`),
  KEY `inventory_transactions_delegate_id_foreign` (`delegate_id`),
  KEY `inventory_transactions_user_id_foreign` (`user_id`),
  KEY `inventory_transactions_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `inventory_transactions_approved_by_foreign` (`approved_by`),
  CONSTRAINT `inventory_transactions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_transactions_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transactions`
--

LOCK TABLES `inventory_transactions` WRITE;
/*!40000 ALTER TABLE `inventory_transactions` DISABLE KEYS */;
INSERT INTO `inventory_transactions` VALUES (1,1,1,'in',102.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:38:56','2026-02-16 09:38:56'),(2,2,1,'in',355.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:38:56','2026-02-16 09:38:56'),(3,3,1,'in',306.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:38:56','2026-02-16 09:38:56'),(4,4,1,'in',289.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:38:56','2026-02-16 09:38:56'),(5,5,2,'in',439.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:39:36','2026-02-16 09:39:36'),(6,6,2,'in',232.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:39:36','2026-02-16 09:39:36'),(7,7,2,'in',157.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:39:36','2026-02-16 09:39:36'),(8,8,2,'in',450.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:39:36','2026-02-16 09:39:36'),(9,9,3,'in',471.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:08','2026-02-16 09:40:08'),(10,10,3,'in',115.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:08','2026-02-16 09:40:08'),(11,11,3,'in',470.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:08','2026-02-16 09:40:08'),(12,12,3,'in',175.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:08','2026-02-16 09:40:08'),(13,13,4,'in',176.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:37','2026-02-16 09:40:37'),(14,14,4,'in',272.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:37','2026-02-16 09:40:37'),(15,15,4,'in',116.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:37','2026-02-16 09:40:37'),(16,16,4,'in',392.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 09:40:37','2026-02-16 09:40:37'),(17,17,5,'in',313.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:28','2026-02-16 10:01:28'),(18,18,5,'in',499.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:28','2026-02-16 10:01:28'),(19,19,5,'in',217.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:28','2026-02-16 10:01:28'),(20,20,5,'in',120.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:28','2026-02-16 10:01:28'),(21,21,6,'in',476.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:55','2026-02-16 10:01:55'),(22,22,6,'in',199.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:55','2026-02-16 10:01:55'),(23,23,6,'in',445.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:55','2026-02-16 10:01:55'),(24,24,6,'in',408.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:01:55','2026-02-16 10:01:55'),(25,25,7,'in',267.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:02:20','2026-02-16 10:02:20'),(26,26,7,'in',246.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:02:20','2026-02-16 10:02:20'),(27,27,7,'in',393.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:02:20','2026-02-16 10:02:20'),(28,28,7,'in',491.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:02:20','2026-02-16 10:02:20'),(29,29,8,'in',469.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:03:06','2026-02-16 10:03:06'),(30,30,8,'in',272.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:03:06','2026-02-16 10:03:06'),(31,31,8,'in',148.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:03:06','2026-02-16 10:03:06'),(32,32,8,'in',386.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:03:06','2026-02-16 10:03:06'),(33,33,9,'in',499.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:24','2026-02-16 10:07:24'),(34,34,9,'in',364.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:24','2026-02-16 10:07:24'),(35,35,9,'in',156.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:24','2026-02-16 10:07:24'),(36,36,9,'in',222.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:24','2026-02-16 10:07:24'),(37,37,10,'in',279.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:53','2026-02-16 10:07:53'),(38,38,10,'in',384.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:53','2026-02-16 10:07:53'),(39,39,10,'in',137.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:53','2026-02-16 10:07:53'),(40,40,10,'in',402.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:07:53','2026-02-16 10:07:53'),(41,41,11,'in',134.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:22','2026-02-16 10:08:22'),(42,42,11,'in',343.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:22','2026-02-16 10:08:22'),(43,43,11,'in',404.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:22','2026-02-16 10:08:22'),(44,44,11,'in',494.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:22','2026-02-16 10:08:22'),(45,45,12,'in',481.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:54','2026-02-16 10:08:54'),(46,46,12,'in',294.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:54','2026-02-16 10:08:54'),(47,47,12,'in',439.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:54','2026-02-16 10:08:54'),(48,48,12,'in',150.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:08:54','2026-02-16 10:08:54'),(49,49,13,'in',289.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:09:22','2026-02-16 10:09:22'),(50,50,13,'in',117.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:09:22','2026-02-16 10:09:22'),(51,51,13,'in',170.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:09:22','2026-02-16 10:09:22'),(52,52,13,'in',252.000,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,'OPENING-STOCK','2026-02-16 10:09:22','2026-02-16 10:09:22');
/*!40000 ALTER TABLE `inventory_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_stock_level` int(11) NOT NULL DEFAULT '10',
  `max_stock_level` int(11) DEFAULT NULL,
  `reorder_point` int(11) DEFAULT NULL,
  `estimated_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_barcode_unique` (`barcode`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'SKU-2096',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,212.00,'2026-02-16 09:38:56','2026-02-16 09:38:56'),(2,'SKU-5094',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,494.00,'2026-02-16 09:38:56','2026-02-16 09:38:56'),(3,'SKU-3709',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,60.00,'2026-02-16 09:38:56','2026-02-16 09:38:56'),(4,'SKU-3257',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,248.00,'2026-02-16 09:38:56','2026-02-16 09:38:56'),(5,'SKU-2107',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,206.00,'2026-02-16 09:39:36','2026-02-16 09:39:36'),(6,'SKU-1456',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,343.00,'2026-02-16 09:39:36','2026-02-16 09:39:36'),(7,'SKU-6458',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,119.00,'2026-02-16 09:39:36','2026-02-16 09:39:36'),(8,'SKU-9387',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,204.00,'2026-02-16 09:39:36','2026-02-16 09:39:36'),(9,'SKU-6403',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,333.00,'2026-02-16 09:40:08','2026-02-16 09:40:08'),(10,'SKU-9299',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,355.00,'2026-02-16 09:40:08','2026-02-16 09:40:08'),(11,'SKU-4157',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,295.00,'2026-02-16 09:40:08','2026-02-16 09:40:08'),(12,'SKU-2060',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,69.00,'2026-02-16 09:40:08','2026-02-16 09:40:08'),(13,'SKU-2836',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,282.00,'2026-02-16 09:40:37','2026-02-16 09:40:37'),(14,'SKU-2026',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,352.00,'2026-02-16 09:40:37','2026-02-16 09:40:37'),(15,'SKU-2481',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,186.00,'2026-02-16 09:40:37','2026-02-16 09:40:37'),(16,'SKU-2601',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,158.00,'2026-02-16 09:40:37','2026-02-16 09:40:37'),(17,'SKU-3237',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,156.00,'2026-02-16 10:01:28','2026-02-16 10:01:28'),(18,'SKU-7636',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,329.00,'2026-02-16 10:01:28','2026-02-16 10:01:28'),(19,'SKU-2794',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,265.00,'2026-02-16 10:01:28','2026-02-16 10:01:28'),(20,'SKU-4604',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,343.00,'2026-02-16 10:01:28','2026-02-16 10:01:28'),(21,'SKU-6004',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,233.00,'2026-02-16 10:01:55','2026-02-16 10:01:55'),(22,'SKU-5070',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,63.00,'2026-02-16 10:01:55','2026-02-16 10:01:55'),(23,'SKU-5278',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,242.00,'2026-02-16 10:01:55','2026-02-16 10:01:55'),(24,'SKU-6648',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,105.00,'2026-02-16 10:01:55','2026-02-16 10:01:55'),(25,'SKU-5603',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,191.00,'2026-02-16 10:02:20','2026-02-16 10:02:20'),(26,'SKU-8925',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,139.00,'2026-02-16 10:02:20','2026-02-16 10:02:20'),(27,'SKU-2909',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,412.00,'2026-02-16 10:02:20','2026-02-16 10:02:20'),(28,'SKU-2443',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,286.00,'2026-02-16 10:02:20','2026-02-16 10:02:20'),(29,'SKU-1814',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,270.00,'2026-02-16 10:03:06','2026-02-16 10:03:06'),(30,'SKU-3528',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,63.00,'2026-02-16 10:03:06','2026-02-16 10:03:06'),(31,'SKU-3775',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,100.00,'2026-02-16 10:03:06','2026-02-16 10:03:06'),(32,'SKU-2586',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,215.00,'2026-02-16 10:03:06','2026-02-16 10:03:06'),(33,'SKU-9132',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,196.00,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(34,'SKU-1051',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,183.00,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(35,'SKU-6931',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,88.00,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(36,'SKU-5702',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,326.00,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(37,'SKU-4922',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,119.00,'2026-02-16 10:07:53','2026-02-16 10:07:53'),(38,'SKU-4782',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,397.00,'2026-02-16 10:07:53','2026-02-16 10:07:53'),(39,'SKU-9047',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,266.00,'2026-02-16 10:07:53','2026-02-16 10:07:53'),(40,'SKU-3943',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,273.00,'2026-02-16 10:07:53','2026-02-16 10:07:53'),(41,'SKU-3848',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,86.00,'2026-02-16 10:08:22','2026-02-16 10:08:22'),(42,'SKU-4645',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,408.00,'2026-02-16 10:08:22','2026-02-16 10:08:22'),(43,'SKU-2501',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,272.00,'2026-02-16 10:08:22','2026-02-16 10:08:22'),(44,'SKU-3849',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,140.00,'2026-02-16 10:08:22','2026-02-16 10:08:22'),(45,'SKU-7603',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,384.00,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(46,'SKU-5727',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,370.00,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(47,'SKU-3868',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,389.00,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(48,'SKU-2873',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,204.00,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(49,'SKU-2630',NULL,NULL,1,'بطانيات شتوية','قطعة',NULL,0.00,NULL,10,NULL,NULL,146.00,'2026-02-16 10:09:22','2026-02-16 10:09:22'),(50,'SKU-4417',NULL,NULL,1,'كرتونة مواد غذائية','كرتونة',NULL,0.00,NULL,10,NULL,NULL,378.00,'2026-02-16 10:09:22','2026-02-16 10:09:22'),(51,'SKU-9889',NULL,NULL,1,'شنطة مدرسية','قطعة',NULL,0.00,NULL,10,NULL,NULL,210.00,'2026-02-16 10:09:22','2026-02-16 10:09:22'),(52,'SKU-9539',NULL,NULL,1,'أدوية متنوعة','علبة',NULL,0.00,NULL,10,NULL,NULL,465.00,'2026-02-16 10:09:22','2026-02-16 10:09:22');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `branch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entry_lines`
--

DROP TABLE IF EXISTS `journal_entry_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_entry_lines` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint(20) unsigned NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `debit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `journal_entry_lines_account_id_foreign` (`account_id`),
  CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entry_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('annual','sick','unpaid','emergency','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'annual',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_user_id_foreign` (`user_id`),
  KEY `leaves_approved_by_foreign` (`approved_by`),
  CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_11_26_000001_create_donors_table',1),(2,'2025_11_26_000002_create_projects_table',1),(3,'2025_11_26_000003_create_campaigns_table',1),(4,'2025_11_26_000004_create_routes_table',1),(5,'2025_11_26_000005_create_delegates_table',1),(6,'2025_11_26_000006_create_beneficiaries_table',1),(7,'2025_11_26_000007_create_attachments_table',1),(8,'2025_11_26_000008_create_warehouses_table',1),(9,'2025_11_26_000009_create_items_table',1),(10,'2025_11_26_000010_create_inventory_transactions_table',1),(11,'2025_11_26_000011_create_donations_table',1),(12,'2025_11_26_000012_create_users_table',1),(13,'2025_11_26_000013_create_roles_and_permissions_tables',1),(14,'2025_11_26_000014_create_tasks_table',1),(15,'2025_11_26_000015_create_accounts_and_journal_tables',1),(16,'2025_11_26_000016_create_complaints_table',1),(17,'2025_11_26_000017_create_tokens_table',1),(18,'2025_11_26_000018_seed_baseline_data',1),(19,'2025_11_26_000019_create_expenses_table',1),(20,'2025_11_26_000020_create_financial_closures_table',1),(21,'2025_11_26_000021_create_audits_table',1),(22,'2025_11_26_000022_alter_audits_add_meta',1),(23,'2025_11_26_000022_create_volunteer_hours_table',1),(24,'2025_11_26_000023_create_payrolls_table',1),(25,'2025_11_26_000030_create_volunteer_attendances_table',1),(26,'2025_11_26_000031_seed_projects_and_campaigns',1),(27,'2025_11_26_000032_create_guest_houses_table',1),(28,'2025_11_26_000033_seed_guest_houses',1),(29,'2025_11_26_000034_seed_travel_routes_governorates',1),(30,'2025_11_26_000035_seed_delegates',1),(31,'2025_11_26_000036_alter_donations_add_cash_channel',1),(32,'2025_11_26_000037_alter_donations_add_receipt_number',1),(33,'2025_11_26_000038_alter_donors_add_sponsorship_fields',1),(34,'2025_11_26_000039_alter_donors_add_sponsorship_monthly_amount',1),(35,'2025_11_26_000040_alter_projects_add_management_fields',1),(36,'2025_11_26_000041_create_project_volunteers_table',1),(37,'2025_11_26_000042_alter_project_volunteers_add_fields',1),(38,'2025_12_04_000001_add_cities_to_travel_routes',1),(39,'2025_12_04_000002_add_notes_to_beneficiaries',1),(40,'2025_12_05_000101_alter_tasks_add_volunteer_fields',1),(41,'2025_12_06_000001_alter_users_add_volunteer_profile_fields',1),(42,'2025_12_06_000002_alter_project_volunteers_add_hours',1),(43,'2025_12_06_000003_alter_campaigns_add_project_id',1),(44,'2025_12_06_000004_create_project_monthly_volunteers_table',1),(45,'2025_12_06_000005_create_project_activities_table',1),(46,'2025_12_06_000006_alter_project_activities_add_location_expenses',1),(47,'2025_12_06_000007_enhance_hr_and_accounts_tables',1),(48,'2025_12_06_000008_enhance_guest_houses',1),(49,'2025_12_06_000009_enhance_campaigns_and_projects',1),(50,'2025_12_06_000010_create_campaign_daily_menus_table',1),(51,'2025_12_06_000011_create_workspaces_tables',1),(52,'2025_12_06_000012_add_workspace_id_to_expenses',1),(53,'2025_12_06_000013_add_amenities_to_workspaces',1),(54,'2025_12_06_000014_create_delegate_trips_table',1),(55,'2025_12_07_000001_seed_additional_roles_and_permissions',1),(56,'2025_12_07_000002_seed_dynamic_roles_from_entities',1),(57,'2025_12_07_000003_add_description_to_roles',1),(58,'2025_12_07_000004_populate_roles_and_permissions',1),(59,'2025_12_07_000005_add_profile_photo_to_users_and_delegates_table',1),(60,'2025_12_07_000006_create_employee_attendances_table',1),(61,'2025_12_07_000007_add_evaluation_fields_to_attendances_tables',1),(62,'2025_12_07_000008_add_evaluation_fields_to_tasks_table',1),(63,'2025_12_07_000009_add_category_to_expenses_table',1),(64,'2025_12_07_000010_add_allocation_fields_to_donors_table',1),(65,'2025_12_11_000001_add_user_id_to_delegates_table',1),(66,'2025_12_11_000002_fix_permissions_gaps',1),(67,'2025_12_15_000000_add_rejection_reason_to_beneficiaries_table',1),(68,'2025_12_15_000001_add_code_to_beneficiaries_table',1),(69,'2025_12_15_010000_create_leaves_table',1),(70,'2025_12_15_020000_create_purchases_tables',1),(71,'2025_12_15_030000_create_change_requests_table',1),(72,'2025_12_31_183157_create_reception_logs_table',1),(73,'2025_12_31_183346_create_field_visits_table',1),(74,'2026_01_04_000001_add_original_name_to_attachments',1),(75,'2026_01_04_201500_add_deputy_to_campaigns_table',1),(76,'2026_01_07_145700_add_accounting_fields_to_payrolls',1),(77,'2026_01_07_151300_add_accounting_fields_to_delegate_trips',1),(78,'2026_01_07_152000_add_advanced_logistics_features',1),(79,'2026_01_07_154000_add_warehouse_integration_fields',1),(80,'2026_01_08_154000_add_treasury_and_enhanced_donations',1),(81,'2026_01_28_000000_add_soft_cancellation_to_financial_records',1),(82,'2026_01_28_043114_add_missing_columns_to_treasuries_table',1),(83,'2026_01_29_083443_add_missing_columns_to_users_and_payrolls_and_transactions',1),(84,'2026_01_29_154500_fix_items_table_columns',1),(85,'2026_02_02_000000_add_leave_quota_to_users',1),(86,'2026_02_07_120143_create_web_board_members_table',1),(87,'2026_02_07_120146_create_web_partners_table',1),(88,'2026_02_07_120147_create_web_news_table',1),(89,'2026_02_07_120149_create_web_room_bookings_table',1),(90,'2026_02_07_120150_create_web_volunteer_requests_table',1),(91,'2026_02_07_120151_create_web_contact_messages_table',1),(92,'2026_02_07_120159_add_website_fields_to_projects_and_campaigns_table',1),(93,'2026_02_07_122053_create_mobile_notifications_table',1),(94,'2026_02_07_122054_create_mobile_banners_table',1),(95,'2026_02_07_122055_create_mobile_case_applications_table',1),(96,'2026_02_07_122055_create_mobile_in_kind_donations_table',1),(97,'2026_02_07_122056_add_mobile_fields_to_projects_and_campaigns',1),(98,'2026_02_12_090446_create_web_pages_table',1),(99,'2026_02_12_095050_recreate_web_pages_schema',1),(100,'2026_02_12_103000_create_web_faqs_table',1),(101,'2026_02_12_103320_update_web_room_bookings_table_v2',1),(102,'2026_02_12_103455_update_web_volunteer_requests_table_v2',1),(103,'2026_02_12_104630_modify_web_partners_type_column',1),(104,'2026_02_12_170000_add_missing_columns_to_campaigns_table',1),(105,'2026_02_12_171500_add_share_price_to_campaigns_table',1),(106,'2026_02_12_173000_update_campaign_status_to_string',1),(107,'2026_02_12_180000_add_season_title_to_campaigns',1),(108,'2026_02_12_181500_add_goal_unit_to_campaigns',1),(109,'2026_02_12_182000_add_image_path_to_web_contact_messages_table',1),(110,'2026_02_12_193000_add_ui_labels_to_campaigns',1),(111,'2026_02_12_200000_create_web_dynamic_cards_table',1),(112,'2026_02_12_210000_create_web_settings_table',1),(113,'2026_02_12_220000_add_color_to_cards_table',1),(114,'2026_02_14_095952_add_views_and_shares_to_web_news_table',1),(115,'2026_02_14_100412_add_category_to_web_news_table',1),(116,'2026_02_14_100713_add_person_details_to_web_news_table',1),(117,'2026_02_14_113000_add_status_to_web_testimonials_table',1),(118,'2026_02_14_130000_add_statistic_fields_to_web_news',1),(119,'2026_02_14_133646_enhance_projects_table_for_dynamic_system',1),(120,'2026_02_14_140000_add_detailed_fields_to_web_volunteer_requests',1),(121,'2026_02_14_160000_add_details_to_web_room_bookings',1),(122,'2026_02_16_000000_create_web_testimonials_table',2),(123,'2026_02_16_000100_create_web_volunteers_wall_table',3),(124,'2026_02_16_000110_create_web_branches_table',4),(125,'2026_02_16_000120_create_web_sectors_table',5),(126,'2026_02_16_000130_create_web_features_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile_banners`
--

DROP TABLE IF EXISTS `mobile_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile_banners`
--

LOCK TABLES `mobile_banners` WRITE;
/*!40000 ALTER TABLE `mobile_banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `mobile_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile_case_applications`
--

DROP TABLE IF EXISTS `mobile_case_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_case_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicant_phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicant_id_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `case_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `governorate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `id_image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_report_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile_case_applications_user_id_foreign` (`user_id`),
  CONSTRAINT `mobile_case_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile_case_applications`
--

LOCK TABLES `mobile_case_applications` WRITE;
/*!40000 ALTER TABLE `mobile_case_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `mobile_case_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile_in_kind_donations`
--

DROP TABLE IF EXISTS `mobile_in_kind_donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_in_kind_donations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donor_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `donor_phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_pickup_time` timestamp NULL DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile_in_kind_donations_user_id_foreign` (`user_id`),
  CONSTRAINT `mobile_in_kind_donations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile_in_kind_donations`
--

LOCK TABLES `mobile_in_kind_donations` WRITE;
/*!40000 ALTER TABLE `mobile_in_kind_donations` DISABLE KEYS */;
/*!40000 ALTER TABLE `mobile_in_kind_donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile_notifications`
--

DROP TABLE IF EXISTS `mobile_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_audience` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile_notifications`
--

LOCK TABLES `mobile_notifications` WRITE;
/*!40000 ALTER TABLE `mobile_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `mobile_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payrolls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonuses` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `paid_at` date DEFAULT NULL,
  `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `treasury_id` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `payrolls_user_id_foreign` (`user_id`),
  KEY `payrolls_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `payrolls_treasury_id_foreign` (`treasury_id`),
  KEY `payrolls_cancelled_by_foreign` (`cancelled_by`),
  CONSTRAINT `payrolls_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_treasury_id_foreign` FOREIGN KEY (`treasury_id`) REFERENCES `treasuries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1),(13,2),(14,2),(15,2),(21,2),(22,2),(23,2),(24,2),(25,2),(26,2),(27,2),(28,2),(29,2),(30,2),(31,2),(32,2),(33,2),(34,2),(35,2),(36,2),(37,2),(38,2),(39,2),(40,2),(41,2),(42,2),(43,2),(44,2),(45,2),(46,2),(47,2),(48,2),(49,2),(50,2),(51,2),(52,2),(53,2),(54,2),(55,2),(56,2),(57,2),(58,2),(59,2),(60,2),(65,2),(66,2),(67,2),(68,2),(69,2),(70,2),(71,2),(72,2),(73,2),(74,2),(75,2),(76,2),(81,2),(82,2),(83,2),(84,2),(85,2),(25,3),(26,3),(27,3),(28,3),(49,3),(57,3),(58,3),(59,3),(60,3),(61,3),(62,3),(63,3),(64,3),(77,3),(78,3),(79,3),(80,3),(82,3),(83,3),(73,4),(75,4),(82,4),(83,4),(1,5),(3,5),(4,5),(1,6),(5,6),(1,7),(6,7),(1,8),(7,8),(13,9),(14,9),(15,9),(16,9),(53,9),(55,9),(61,9),(62,9),(63,9),(64,9),(73,9),(74,9),(75,9),(76,9),(82,9),(83,9),(11,10),(12,11),(3,12),(3,13),(3,14),(3,15),(3,16),(3,17),(6,18),(6,19),(6,20),(6,21),(6,22),(7,23),(7,24);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'عرض المهام الخاصة','view_own_tasks','2026-02-16 09:38:40','2026-02-16 09:38:40'),(2,'تسجيل ساعات تطوع','log_volunteer_hours','2026-02-16 09:38:40','2026-02-16 09:38:40'),(3,'إدارة المشروع','manage_project','2026-02-16 09:38:40','2026-02-16 09:38:40'),(4,'إدارة متطوعي المشروع','manage_project_volunteers','2026-02-16 09:38:40','2026-02-16 09:38:40'),(5,'مساعدة في إدارة المشروع','assist_project_management','2026-02-16 09:38:40','2026-02-16 09:38:40'),(6,'إدارة الحملة','manage_campaign','2026-02-16 09:38:40','2026-02-16 09:38:40'),(7,'إدارة الدار','manage_guest_house','2026-02-16 09:38:40','2026-02-16 09:38:40'),(8,'إدارة الموظفين','manage_employees','2026-02-16 09:38:40','2026-02-16 09:38:40'),(9,'إدارة المتطوعين','manage_volunteers_hr','2026-02-16 09:38:40','2026-02-16 09:38:40'),(10,'إدارة الرواتب','manage_payrolls','2026-02-16 09:38:40','2026-02-16 09:38:40'),(11,'إدارة المالية','manage_finance','2026-02-16 09:38:40','2026-02-16 09:38:40'),(12,'إدارة اللوجستيك','manage_logistics','2026-02-16 09:38:40','2026-02-16 09:38:40'),(13,'عرض المستخدمين','users.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(14,'إضافة المستخدمين','users.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(15,'تعديل المستخدمين','users.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(16,'حذف المستخدمين','users.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(17,'عرض الأدوار','roles.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(18,'إضافة الأدوار','roles.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(19,'تعديل الأدوار','roles.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(20,'حذف الأدوار','roles.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(21,'عرض المندوبين','delegates.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(22,'إضافة المندوبين','delegates.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(23,'تعديل المندوبين','delegates.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(24,'حذف المندوبين','delegates.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(25,'عرض التبرعات','donations.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(26,'إضافة التبرعات','donations.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(27,'تعديل التبرعات','donations.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(28,'حذف التبرعات','donations.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(29,'عرض المشاريع','projects.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(30,'إضافة المشاريع','projects.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(31,'تعديل المشاريع','projects.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(32,'حذف المشاريع','projects.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(33,'عرض الحملات','campaigns.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(34,'إضافة الحملات','campaigns.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(35,'تعديل الحملات','campaigns.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(36,'حذف الحملات','campaigns.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(37,'عرض المستفيدين','beneficiaries.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(38,'إضافة المستفيدين','beneficiaries.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(39,'تعديل المستفيدين','beneficiaries.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(40,'حذف المستفيدين','beneficiaries.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(41,'عرض المخازن','warehouses.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(42,'إضافة المخازن','warehouses.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(43,'تعديل المخازن','warehouses.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(44,'حذف المخازن','warehouses.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(45,'عرض الأصناف','items.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(46,'إضافة الأصناف','items.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(47,'تعديل الأصناف','items.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(48,'حذف الأصناف','items.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(49,'عرض حركات المخزون','inventory_transactions.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(50,'إضافة حركات المخزون','inventory_transactions.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(51,'تعديل حركات المخزون','inventory_transactions.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(52,'حذف حركات المخزون','inventory_transactions.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(53,'عرض الشكاوى','complaints.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(54,'إضافة الشكاوى','complaints.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(55,'تعديل الشكاوى','complaints.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(56,'حذف الشكاوى','complaints.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(57,'عرض المصروفات','expenses.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(58,'إضافة المصروفات','expenses.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(59,'تعديل المصروفات','expenses.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(60,'حذف المصروفات','expenses.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(61,'عرض الرواتب','payrolls.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(62,'إضافة الرواتب','payrolls.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(63,'تعديل الرواتب','payrolls.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(64,'حذف الرواتب','payrolls.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(65,'عرض دور الضيافة','guest_houses.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(66,'إضافة دور الضيافة','guest_houses.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(67,'تعديل دور الضيافة','guest_houses.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(68,'حذف دور الضيافة','guest_houses.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(69,'عرض مساحات العمل','workspaces.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(70,'إضافة مساحات العمل','workspaces.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(71,'تعديل مساحات العمل','workspaces.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(72,'حذف مساحات العمل','workspaces.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(73,'عرض المهام','tasks.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(74,'إضافة المهام','tasks.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(75,'تعديل المهام','tasks.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(76,'حذف المهام','tasks.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(77,'عرض الإغلاقات المالية','financial_closures.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(78,'إضافة الإغلاقات المالية','financial_closures.create','2026-02-16 09:38:40','2026-02-16 09:38:40'),(79,'تعديل الإغلاقات المالية','financial_closures.edit','2026-02-16 09:38:40','2026-02-16 09:38:40'),(80,'حذف الإغلاقات المالية','financial_closures.delete','2026-02-16 09:38:40','2026-02-16 09:38:40'),(81,'عرض سجلات النظام','audits.view','2026-02-16 09:38:40','2026-02-16 09:38:40'),(82,'عرض لوحة التحكم ','dashboard.view','2026-02-16 09:38:41','2026-02-16 09:38:41'),(83,'عرض التنبيهات ','notifications.view','2026-02-16 09:38:41','2026-02-16 09:38:41'),(84,'إضافة المرفقات','attachments.create','2026-02-16 09:38:41','2026-02-16 09:38:41'),(85,'حذف المرفقات','attachments.delete','2026-02-16 09:38:41','2026-02-16 09:38:41');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_activities`
--

DROP TABLE IF EXISTS `project_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_date` date NOT NULL,
  `revenue` decimal(12,2) NOT NULL DEFAULT '0.00',
  `expenses` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_activities_project_id_type_index` (`project_id`,`type`),
  CONSTRAINT `project_activities_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_activities`
--

LOCK TABLES `project_activities` WRITE;
/*!40000 ALTER TABLE `project_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_monthly_volunteers`
--

DROP TABLE IF EXISTS `project_monthly_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_monthly_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_monthly_volunteers_user_id_foreign` (`user_id`),
  KEY `project_monthly_volunteers_project_id_year_month_index` (`project_id`,`year`,`month`),
  CONSTRAINT `project_monthly_volunteers_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_monthly_volunteers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_monthly_volunteers`
--

LOCK TABLES `project_monthly_volunteers` WRITE;
/*!40000 ALTER TABLE `project_monthly_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_monthly_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_volunteers`
--

DROP TABLE IF EXISTS `project_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` date DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `hours` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_volunteers_project_id_user_id_unique` (`project_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_volunteers`
--

LOCK TABLES `project_volunteers` WRITE;
/*!40000 ALTER TABLE `project_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fixed` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('active','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `manager_user_id` bigint(20) unsigned DEFAULT NULL,
  `deputy_user_id` bigint(20) unsigned DEFAULT NULL,
  `manager_photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deputy_photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_content` text COLLATE utf8mb4_unicode_ci,
  `sponsorship_details` text COLLATE utf8mb4_unicode_ci,
  `mobile_content` text COLLATE utf8mb4_unicode_ci,
  `show_on_mobile` tinyint(1) NOT NULL DEFAULT '1',
  `icon_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `features` json DEFAULT NULL,
  `stats` json DEFAULT NULL,
  `theme_colors` json DEFAULT NULL,
  `action_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `show_badge` tinyint(1) NOT NULL DEFAULT '1',
  `badge_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subcategory_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_subcategory` tinyint(1) NOT NULL DEFAULT '1',
  `action_icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (5,'دار ضيافة طنطا',1,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL,NULL,NULL,1,NULL),(6,'دار ضيافة كفر الشيخ',1,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL,NULL,NULL,1,NULL),(46,'مشروع بعثاء الأمل',1,'active',NULL,NULL,NULL,NULL,NULL,'2026-02-17 12:53:08','2026-02-17 12:58:43','website/projects/d225fac0-eef9-4f7b-b24a-0798be09e99a.webp','أمل',NULL,NULL,NULL,1,NULL,'مساندة أطفال السرطان وأسرتهم مادياً ومعنوياً وطبياً لتجاوز رحلة العلاج .','[{\"icon\": null, \"text\": \"500+\"}, {\"icon\": null, \"text\": \"شامل\"}]','[{\"icon\": null, \"label\": \"طفل مستفيد\", \"value\": null}]','{\"iconColor\": \"#0d6efd\", \"lightTint\": \"#c1ccdc\", \"borderColor\": \"#cfe2ff\", \"primaryColor\": \"#fd0dd1\"}',NULL,NULL,1,1,'مساندة أطفال السرطان',NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `item_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `original_price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `final_price` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reception_logs`
--

DROP TABLE IF EXISTS `reception_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reception_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visitor_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_type` enum('personal','phone') COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` enum('help_request','complaint','donation','inquiry','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','directed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `directed_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reception_logs`
--

LOCK TABLES `reception_logs` WRITE;
/*!40000 ALTER TABLE `reception_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `reception_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_user` (
  `role_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`user_id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,1),(2,2),(9,3),(3,4),(25,5),(26,6),(27,7),(1,8);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator','admin','صلاحيات كاملة للتحكم في النظام وإدارة جميع الموارد والمستخدمين.',NULL,NULL),(2,'Manager','manager','إشراف عام على العمليات والمشاريع والموظفين.',NULL,NULL),(3,'Finance','finance','إدارة التبرعات، المصروفات، والتقارير المالية والمحاسبية.',NULL,NULL),(4,'متطوع','volunteer','المشاركة في الأنشطة والفعاليات الميدانية.','2026-02-16 09:38:40','2026-02-16 09:38:40'),(5,'مدير مشروع','project_manager',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(6,'نائب مدير مشروع','project_deputy',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(7,'مدير حملة','campaign_manager',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(8,'مدير دار','guest_house_manager',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(9,'الموارد البشرية','hr','إدارة شؤون الموظفين، الحضور والانصراف، والرواتب.','2026-02-16 09:38:40','2026-02-16 09:38:40'),(10,'مدير مالية','finance_manager',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(11,'مدير لوجستيك','logistics_manager',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(12,'مدير مشروع كسوة','project_manager_1',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(13,'مدير مشروع بعثاء الأمل','project_manager_2',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(14,'مدير مشروع زاد','project_manager_3',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(15,'مدير مشروع مدرار','project_manager_4',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(16,'مدير دار ضيافة طنطا','project_manager_5',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(17,'مدير دار ضيافة كفر الشيخ','project_manager_6',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(18,'مدير حملة الشتاء','campaign_manager_1',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(19,'مدير حملة رمضان','campaign_manager_2',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(20,'مدير حملة المدارس','campaign_manager_3',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(21,'مدير عيد الفطر','campaign_manager_4',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(22,'مدير عيد الأضحى','campaign_manager_5',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(23,'مدير دار ضيافة طنطا','guest_house_manager_1',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(24,'مدير دار ضيافة كفر الشيخ','guest_house_manager_2',NULL,'2026-02-16 09:38:40','2026-02-16 09:38:40'),(25,'أمين المخزن','store_keeper',NULL,'2026-02-16 09:38:53','2026-02-16 09:38:53'),(26,'الاستقبال','receptionist',NULL,'2026-02-16 09:38:54','2026-02-16 09:38:54'),(27,'باحث ميداني','field_researcher',NULL,'2026-02-16 09:38:55','2026-02-16 09:38:55');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_trips`
--

DROP TABLE IF EXISTS `scheduled_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheduled_trips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delegate_id` bigint(20) unsigned NOT NULL,
  `route_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `from_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `estimated_distance` decimal(10,2) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `actual_trip_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scheduled_trips_delegate_id_foreign` (`delegate_id`),
  KEY `scheduled_trips_route_id_foreign` (`route_id`),
  KEY `scheduled_trips_actual_trip_id_foreign` (`actual_trip_id`),
  CONSTRAINT `scheduled_trips_actual_trip_id_foreign` FOREIGN KEY (`actual_trip_id`) REFERENCES `delegate_trips` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_trips_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scheduled_trips_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `travel_routes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_trips`
--

LOCK TABLES `scheduled_trips` WRITE;
/*!40000 ALTER TABLE `scheduled_trips` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `volunteer_activity_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','done') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `evaluation_notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `tasks_assigned_to_foreign` (`assigned_to`),
  KEY `tasks_assigned_by_foreign` (`assigned_by`),
  KEY `tasks_project_id_foreign` (`project_id`),
  KEY `tasks_campaign_id_foreign` (`campaign_id`),
  KEY `tasks_guest_house_id_foreign` (`guest_house_id`),
  CONSTRAINT `tasks_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokens_token_unique` (`token`),
  KEY `tokens_user_id_foreign` (`user_id`),
  CONSTRAINT `tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `travel_routes`
--

DROP TABLE IF EXISTS `travel_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travel_routes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cities` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_routes`
--

LOCK TABLES `travel_routes` WRITE;
/*!40000 ALTER TABLE `travel_routes` DISABLE KEYS */;
INSERT INTO `travel_routes` VALUES (1,'القاهرة',NULL,NULL,NULL,NULL),(2,'الجيزة',NULL,NULL,NULL,NULL),(3,'القليوبية',NULL,NULL,NULL,NULL),(4,'الإسكندرية',NULL,NULL,NULL,NULL),(5,'البحيرة',NULL,NULL,NULL,NULL),(6,'كفر الشيخ',NULL,NULL,NULL,NULL),(7,'الغربية',NULL,NULL,NULL,NULL),(8,'المنوفية',NULL,NULL,NULL,NULL),(9,'دمياط',NULL,NULL,NULL,NULL),(10,'الدقهلية',NULL,NULL,NULL,NULL),(11,'الشرقية',NULL,NULL,NULL,NULL),(12,'بورسعيد',NULL,NULL,NULL,NULL),(13,'الإسماعيلية',NULL,NULL,NULL,NULL),(14,'السويس',NULL,NULL,NULL,NULL),(15,'شمال سيناء',NULL,NULL,NULL,NULL),(16,'جنوب سيناء',NULL,NULL,NULL,NULL),(17,'بني سويف',NULL,NULL,NULL,NULL),(18,'الفيوم',NULL,NULL,NULL,NULL),(19,'المنيا',NULL,NULL,NULL,NULL),(20,'أسيوط',NULL,NULL,NULL,NULL),(21,'سوهاج',NULL,NULL,NULL,NULL),(22,'الأقصر',NULL,NULL,NULL,NULL),(23,'قنا',NULL,NULL,NULL,NULL),(24,'أسوان',NULL,NULL,NULL,NULL),(25,'مطروح',NULL,NULL,NULL,NULL),(26,'البحر الأحمر',NULL,NULL,NULL,NULL),(27,'الوادي الجديد',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `travel_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treasuries`
--

DROP TABLE IF EXISTS `treasuries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `treasuries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'main',
  `description` text COLLATE utf8mb4_unicode_ci,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `delegate_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `treasuries_code_unique` (`code`),
  KEY `treasuries_manager_id_foreign` (`manager_id`),
  KEY `treasuries_project_id_foreign` (`project_id`),
  KEY `treasuries_campaign_id_foreign` (`campaign_id`),
  KEY `treasuries_delegate_id_foreign` (`delegate_id`),
  CONSTRAINT `treasuries_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasuries_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasuries_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasuries_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treasuries`
--

LOCK TABLES `treasuries` WRITE;
/*!40000 ALTER TABLE `treasuries` DISABLE KEYS */;
/*!40000 ALTER TABLE `treasuries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treasury_transactions`
--

DROP TABLE IF EXISTS `treasury_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `treasury_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `treasury_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `status` enum('active','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `donation_id` bigint(20) unsigned DEFAULT NULL,
  `expense_id` bigint(20) unsigned DEFAULT NULL,
  `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `payroll_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `treasury_transactions_treasury_id_foreign` (`treasury_id`),
  KEY `treasury_transactions_created_by_foreign` (`created_by`),
  KEY `treasury_transactions_donation_id_foreign` (`donation_id`),
  KEY `treasury_transactions_expense_id_foreign` (`expense_id`),
  KEY `treasury_transactions_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `treasury_transactions_cancelled_by_foreign` (`cancelled_by`),
  KEY `treasury_transactions_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `treasury_transactions_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_treasury_id_foreign` FOREIGN KEY (`treasury_id`) REFERENCES `treasuries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treasury_transactions`
--

LOCK TABLES `treasury_transactions` WRITE;
/*!40000 ALTER TABLE `treasury_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `treasury_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annual_leave_quota` int(11) NOT NULL DEFAULT '21',
  `leave_balance` int(11) NOT NULL DEFAULT '21',
  `salary` decimal(12,2) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `is_employee` tinyint(1) NOT NULL DEFAULT '0',
  `is_volunteer` tinyint(1) NOT NULL DEFAULT '0',
  `college` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `governorate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volunteer_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `contract_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criminal_record_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_card_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_project_id_foreign` (`project_id`),
  KEY `users_campaign_id_foreign` (`campaign_id`),
  KEY `users_guest_house_id_foreign` (`guest_house_id`),
  CONSTRAINT `users_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'System Admin','admin@ensan.local','$2y$12$fGRXVONH8HUXcenZWR5BK.v1de9QQb62yjwLSi6zP7K/YohiRzJ1K',NULL,NULL,NULL,21,21,NULL,NULL,1,0,NULL,NULL,NULL,NULL,0.00,1,NULL,'2026-02-16 09:44:04',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'موظف مدير المؤسسة','manager@ensan.local','$2y$12$hGtcnOTWOUwk1Wqb8T782OWgjJCjk52bvIYJCzTQa8y.cEDMewELq',NULL,NULL,NULL,21,21,4273.00,'2025-07-16',1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-16 09:38:52','2026-02-16 09:38:52',NULL,NULL,NULL,NULL,'2025-02-16','2026-09-16',NULL,NULL,NULL),(3,'موظف الموارد البشرية','hr@ensan.local','$2y$12$pwEQLv/Moi/J0uk6b.Um5.wIOE7W7BWDnqr5VnEgdHSdsF0B6A8qq',NULL,NULL,NULL,21,21,8695.00,'2024-12-16',1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-16 09:38:53','2026-02-16 09:38:53',NULL,NULL,NULL,NULL,'2025-04-16','2027-01-16',NULL,NULL,NULL),(4,'موظف المالية','finance@ensan.local','$2y$12$t0pAJNGA/rE.jhWhZ986l.2GKuxtIGrTR1nwcStm9n5X1EsegMnQq',NULL,NULL,NULL,21,21,5886.00,'2024-03-16',1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-16 09:38:53','2026-02-16 09:38:53',NULL,NULL,NULL,NULL,'2025-10-16','2026-08-16',NULL,NULL,NULL),(5,'موظف أمين المخزن','store_keeper@ensan.local','$2y$12$ubteSiozpG0L79Rpqlq2FuD6QvI1rPSXY7iljpiQDScgGVa6ideWq',NULL,NULL,NULL,21,21,5550.00,'2024-03-16',1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-16 09:38:54','2026-02-16 09:38:54',NULL,NULL,NULL,NULL,'2025-03-16','2026-12-16',NULL,NULL,NULL),(6,'موظف الاستقبال','receptionist@ensan.local','$2y$12$Dm3WV0XvV1ZPaaeJDVMN2OdJWbz8k6xcvjdynQzSWNAV8gYDrgotW',NULL,NULL,NULL,21,21,4506.00,'2025-08-16',1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-16 09:38:55','2026-02-16 09:38:55',NULL,NULL,NULL,NULL,'2025-11-16','2026-11-16',NULL,NULL,NULL),(7,'موظف باحث ميداني','field_researcher@ensan.local','$2y$12$CZ7V98f0k4s3hpLwgBK1DeMK5u.gG7EsMMrX9pGCP5PHrDGJ5IW6u',NULL,NULL,NULL,21,21,7623.00,'2026-01-16',1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-16 09:38:56','2026-02-16 09:38:56',NULL,NULL,NULL,NULL,'2025-04-16','2026-08-16',NULL,NULL,NULL),(8,'IbrahimElfil','IbrahimElfil@gmail.com','$2y$12$ZfScvD4A/hYL16OpM91hz.49C/Mq2jk9OEEQPqt/OkaKdG.bs7jAq',NULL,NULL,NULL,21,21,NULL,NULL,1,0,NULL,NULL,NULL,NULL,0.00,1,'2026-02-17 10:12:51','2026-03-01 15:41:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_maintenance`
--

DROP TABLE IF EXISTS `vehicle_maintenance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_maintenance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delegate_id` bigint(20) unsigned NOT NULL,
  `vehicle_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_plate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maintenance_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `maintenance_date` date NOT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `odometer_reading` int(11) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_maintenance_delegate_id_foreign` (`delegate_id`),
  CONSTRAINT `vehicle_maintenance_delegate_id_foreign` FOREIGN KEY (`delegate_id`) REFERENCES `delegates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_maintenance`
--

LOCK TABLES `vehicle_maintenance` WRITE;
/*!40000 ALTER TABLE `vehicle_maintenance` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_maintenance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volunteer_attendances`
--

DROP TABLE IF EXISTS `volunteer_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volunteer_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in_at` time DEFAULT NULL,
  `check_out_at` time DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `evaluation_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `volunteer_attendances_user_id_foreign` (`user_id`),
  CONSTRAINT `volunteer_attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteer_attendances`
--

LOCK TABLES `volunteer_attendances` WRITE;
/*!40000 ALTER TABLE `volunteer_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `volunteer_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volunteer_hours`
--

DROP TABLE IF EXISTS `volunteer_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volunteer_hours` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `task` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `volunteer_hours_user_id_foreign` (`user_id`),
  CONSTRAINT `volunteer_hours_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteer_hours`
--

LOCK TABLES `volunteer_hours` WRITE;
/*!40000 ALTER TABLE `volunteer_hours` DISABLE KEYS */;
/*!40000 ALTER TABLE `volunteer_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `capacity` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_manager_id_foreign` (`manager_id`),
  CONSTRAINT `warehouses_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 09:38:56','2026-02-16 09:38:56'),(2,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 09:39:36','2026-02-16 09:39:36'),(3,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 09:40:08','2026-02-16 09:40:08'),(4,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 09:40:37','2026-02-16 09:40:37'),(5,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:01:28','2026-02-16 10:01:28'),(6,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:01:55','2026-02-16 10:01:55'),(7,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:02:20','2026-02-16 10:02:20'),(8,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:03:06','2026-02-16 10:03:06'),(9,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(10,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:07:53','2026-02-16 10:07:53'),(11,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:22','2026-02-16 10:08:22'),(12,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(13,'المخزن الرئيسي - القاهرة','مدينة نصر',NULL,NULL,NULL,NULL,1,'2026-02-16 10:09:22','2026-02-16 10:09:22');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_board_members`
--

DROP TABLE IF EXISTS `web_board_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_board_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_board_members`
--

LOCK TABLES `web_board_members` WRITE;
/*!40000 ALTER TABLE `web_board_members` DISABLE KEYS */;
INSERT INTO `web_board_members` VALUES (1,'د. عبد الرحمن السيد','رئيس مجلس الإدارة','خبرة طويلة في إدارة المؤسسات غير الربحية والعمل الإنساني.',NULL,1,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(2,'م. آمنة الشربيني','نائب الرئيس','متخصصة في إدارة المشاريع الاجتماعية والتنموية.',NULL,2,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(3,'Ibrahim Elseginy','مدير زاد','رئيس زاد',NULL,1,'2026-02-17 13:02:55','2026-02-17 13:02:55');
/*!40000 ALTER TABLE `web_board_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_branches`
--

DROP TABLE IF EXISTS `web_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `working_hours` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_maps_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `status_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_branches`
--

LOCK TABLES `web_branches` WRITE;
/*!40000 ALTER TABLE `web_branches` DISABLE KEYS */;
INSERT INTO `web_branches` VALUES (1,'الفرع الرئيسي - القاهرة','القاهرة، مدينة نصر، شارع مكرم عبيد','01000000003','من 9 صباحاً حتى 5 مساءً','cairo@ensan.org',NULL,1,'يخدم محافظات القاهرة الكبرى','2026-02-16 10:07:54','2026-02-16 10:07:54'),(2,'فرع الإسكندرية','الإسكندرية، سموحة، طريق 14 مايو','01000000004','من 10 صباحاً حتى 6 مساءً','alex@ensan.org',NULL,0,'يخدم محافظة الإسكندرية وما حولها','2026-02-16 10:07:54','2026-02-16 10:07:54');
/*!40000 ALTER TABLE `web_branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_contact_messages`
--

DROP TABLE IF EXISTS `web_contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_contact_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_contact_messages`
--

LOCK TABLES `web_contact_messages` WRITE;
/*!40000 ALTER TABLE `web_contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `web_contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_dynamic_cards`
--

DROP TABLE IF EXISTS `web_dynamic_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_dynamic_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `card_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_visible` tinyint(1) NOT NULL DEFAULT '0',
  `badge_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stats_data` json DEFAULT NULL,
  `buttons_data` json DEFAULT NULL,
  `main_button_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_button_action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_button_icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_dynamic_cards`
--

LOCK TABLES `web_dynamic_cards` WRITE;
/*!40000 ALTER TABLE `web_dynamic_cards` DISABLE KEYS */;
INSERT INTO `web_dynamic_cards` VALUES (1,'كفالة يتيم','ساهم بمبلغ شهري لتأمين حياة كريمة لليتيم.',NULL,NULL,1,'أولوية',NULL,NULL,'[{\"label\": \"أيتام مكفولين\", \"value\": \"320+\"}, {\"label\": \"مدن مستفيدة\", \"value\": \"12\"}]','[{\"icon\": null, \"text\": \"ساهم الآن\", \"style\": \"primary\", \"action\": \"#\"}]',NULL,NULL,NULL,1,1,'2026-02-16 10:09:22','2026-02-16 10:09:22');
/*!40000 ALTER TABLE `web_dynamic_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_faqs`
--

DROP TABLE IF EXISTS `web_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_faqs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_faqs`
--

LOCK TABLES `web_faqs` WRITE;
/*!40000 ALTER TABLE `web_faqs` DISABLE KEYS */;
INSERT INTO `web_faqs` VALUES (1,'كيف يمكنني التبرع؟','يمكنك التبرع من خلال مقر المؤسسة، أو التحويل البنكي، أو بوابة التبرع الإلكترونية.','عام',1,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(2,'هل تصل التبرعات لمستحقيها فعلاً؟','نلتزم بإجراءات صارمة في البحث الاجتماعي والمتابعة الميدانية لضمان وصول التبرعات لمستحقيها.','الشفافية',2,'2026-02-16 10:08:54','2026-02-16 10:08:54');
/*!40000 ALTER TABLE `web_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_features`
--

DROP TABLE IF EXISTS `web_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_features` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_features`
--

LOCK TABLES `web_features` WRITE;
/*!40000 ALTER TABLE `web_features` DISABLE KEYS */;
INSERT INTO `web_features` VALUES (1,'شفافية في التقارير','إصدار تقارير دورية عن أثر كل حملة ومصارف التبرعات.','bi-graph-up',1,'2026-02-16 10:08:54','2026-02-16 10:08:54'),(2,'شراكات موثوقة','نتعاون مع مؤسسات وجهات رسمية لضمان وصول الدعم لمستحقيه.','bi-handshake',2,'2026-02-16 10:08:54','2026-02-16 10:08:54');
/*!40000 ALTER TABLE `web_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_news`
--

DROP TABLE IF EXISTS `web_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statistic_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statistic_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `views_count` int(10) unsigned NOT NULL DEFAULT '0',
  `shares_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_news`
--

LOCK TABLES `web_news` WRITE;
/*!40000 ALTER TABLE `web_news` DISABLE KEYS */;
INSERT INTO `web_news` VALUES (1,'انطلاق حملة الشتاء 2026 لتوزيع البطاطين','حملات',NULL,NULL,'فريق الحملات','01000000001','أطلقت مؤسسة إنسان حملة شتاء 2026 لتوزيع بطاطين وكراتين غذائية على الأسر الأكثر احتياجاً في القرى والمناطق النائية.',NULL,'2026-02-11 10:07:24','2026-02-16 10:07:24','2026-02-16 10:07:24',120,35),(2,'تكريم المتطوعين المتميزين في ضيافة إنسان','متطوعين',NULL,NULL,'قسم التطوع','01000000002','نظمت المؤسسة حفلاً لتكريم المتطوعين الذين تجاوزت ساعات عطائهم 300 ساعة خلال العام الماضي.',NULL,'2026-02-06 10:07:24','2026-02-16 10:07:24','2026-02-16 10:07:24',80,20);
/*!40000 ALTER TABLE `web_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_pages`
--

DROP TABLE IF EXISTS `web_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_pages`
--

LOCK TABLES `web_pages` WRITE;
/*!40000 ALTER TABLE `web_pages` DISABLE KEYS */;
INSERT INTO `web_pages` VALUES (1,'guest-house','دار الضيافة',NULL,NULL,NULL,NULL,NULL,1,0,'2026-02-16 09:58:34','2026-02-17 10:53:17'),(2,'mn-nhn','من نحن','<p>مؤسسة إنسان هي مؤسسة أهلية غير هادفة للربح تعمل في مجالات الكفالة، الإغاثة، التنمية والتعليم.</p>',NULL,'عن مؤسسة إنسان','تعرف على رسالة ورؤية وأهداف مؤسسة إنسان.','مؤسسة إنسان, أعمال خيرية, كفالة يتيم',1,1,'2026-02-16 10:08:54','2026-02-16 10:08:54');
/*!40000 ALTER TABLE `web_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_partners`
--

DROP TABLE IF EXISTS `web_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_partners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'partner',
  `website_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_partners`
--

LOCK TABLES `web_partners` WRITE;
/*!40000 ALTER TABLE `web_partners` DISABLE KEYS */;
INSERT INTO `web_partners` VALUES (1,'شركة النور القابضة','website/partners/7a19867a-5524-4e10-a9c5-e9a3a25107bd.webp','شريك استراتيجي في حملات الكفالة والإطعام.','platinum','https://example.com','2026-02-16 10:08:54','2026-03-01 15:50:09');
/*!40000 ALTER TABLE `web_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_room_bookings`
--

DROP TABLE IF EXISTS `web_room_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_room_bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_house_id` bigint(20) unsigned DEFAULT NULL,
  `room_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companion_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companion_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrival_date` date DEFAULT NULL,
  `expected_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_center` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_id_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companion_id_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_transfer_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `followup_card_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_report_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `web_room_bookings_guest_house_id_foreign` (`guest_house_id`),
  CONSTRAINT `web_room_bookings_guest_house_id_foreign` FOREIGN KEY (`guest_house_id`) REFERENCES `guest_houses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_room_bookings`
--

LOCK TABLES `web_room_bookings` WRITE;
/*!40000 ALTER TABLE `web_room_bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `web_room_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_sectors`
--

DROP TABLE IF EXISTS `web_sectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_sectors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_sectors`
--

LOCK TABLES `web_sectors` WRITE;
/*!40000 ALTER TABLE `web_sectors` DISABLE KEYS */;
INSERT INTO `web_sectors` VALUES (1,'الكفالة','bi-people','برامج متخصصة لكفالة الأيتام والأسر الأكثر احتياجاً.','2026-02-16 10:08:22','2026-02-16 10:08:22'),(2,'الإطعام','bi-basket3','توزيع وجبات ساخنة وكراتين غذائية على مدار العام.','2026-02-16 10:08:22','2026-02-16 10:08:22'),(3,'السقيا','bi-droplet','حفر الآبار وتأمين مصادر مياه آمنة للقرى المحرومة.','2026-02-16 10:08:22','2026-02-16 10:08:22');
/*!40000 ALTER TABLE `web_sectors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_settings`
--

DROP TABLE IF EXISTS `web_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_settings`
--

LOCK TABLES `web_settings` WRITE;
/*!40000 ALTER TABLE `web_settings` DISABLE KEYS */;
INSERT INTO `web_settings` VALUES (1,'site_name','مؤسسة إنسان للأعمال الخيرية','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(2,'hero_title_primary','معاً نصنع أثراً حقيقيا','general','text','2026-02-16 10:07:24','2026-02-17 14:41:40'),(3,'hero_title_secondary','في حياة المحتاجين','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(4,'hero_description','من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(5,'notification_label','جديد','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(6,'notification_text','دار الضيافة','general','text','2026-02-16 10:07:24','2026-02-17 10:51:02'),(7,'notification_link_text','اعرف المزيد','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(8,'notification_link_url','http://192.168.1.145:4200/diyafa','general','text','2026-02-16 10:07:24','2026-02-17 10:50:44'),(9,'campaigns_title','حملاتنا الجارية','general','text','2026-02-16 10:07:24','2026-02-17 10:22:51'),(10,'campaigns_subtitle','شارك في دعم الأيتام','general','text','2026-02-16 10:07:24','2026-02-17 10:23:17'),(11,'cta_title','كن جزءاً من قصة نجاح','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(12,'cta_text','كل مساهمة منك تصنع فارقاً في حياة إنسان.','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(13,'cta_stat1_value','50M+','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(14,'cta_stat1_label','تبرعات','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(15,'cta_stat2_value','150K+','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(16,'cta_stat2_label','مستفيده','general','text','2026-02-16 10:07:24','2026-02-17 14:46:19'),(17,'cta_stat3_value','12+','general','text','2026-02-16 10:07:24','2026-02-17 14:31:08'),(18,'cta_stat3_label','سنوات عطاء','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(19,'volunteer_title','تطوع معنا وكن جزءاً من التغيير الجامد','general','text','2026-02-16 10:07:24','2026-02-17 13:20:32'),(20,'volunteer_subtitle','ساعات من وقتك تساوي حياة كاملة عند غيرك','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(21,'volunteer_description','نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.','general','text','2026-02-16 10:07:24','2026-02-17 15:01:55'),(22,'volunteer_stats_volunteers','2','general','text','2026-02-16 10:07:24','2026-02-17 15:02:34'),(23,'volunteer_stats_hours','25,00+','general','text','2026-02-16 10:07:24','2026-02-17 13:21:54'),(24,'volunteer_stats_branches','2','general','text','2026-02-16 10:07:24','2026-02-17 13:21:25'),(25,'partners_stats_donors','5000+','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(26,'partners_stats_volunteers','1200+','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(27,'partners_stats_campaigns','180+','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(28,'partners_stats_institutions','25+','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(29,'footer_about_text','مؤسسة غير ربحية تعمل على تمكين الأفراد والأسر من خلال مشاريع مستدامة في مجالات الكفالة، التعليم، الصحة، والإغاثة.','general','text','2026-02-16 10:07:24','2026-02-16 10:07:24'),(30,'stats_beneficiaries','400K','general','text','2026-02-17 10:13:13','2026-02-17 12:42:37'),(31,'stats_branches','2 فروع','general','text','2026-02-17 10:13:13','2026-02-17 15:06:53'),(32,'stats_donations','12M+','general','text','2026-02-17 10:13:13','2026-02-17 14:43:17'),(33,'stats_volunteers','+200','general','text','2026-02-17 10:13:13','2026-02-17 15:06:53'),(34,'hero_stat_money','+50M','general','text','2026-02-17 10:13:13','2026-02-17 10:13:13'),(35,'hero_stat_smiles','+15K','general','text','2026-02-17 10:13:13','2026-02-17 14:47:27'),(36,'hero_stat_years','12','general','text','2026-02-17 10:13:13','2026-02-17 14:42:54'),(37,'notification_active','on','general','text','2026-02-17 10:13:13','2026-02-17 10:17:07'),(38,'gh_hero_subtitle','ملاذ آمن للمرضى ومرافقيهم','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(39,'gh_stat1_value','+55','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(40,'gh_stat1_label','سرير','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(41,'gh_stat2_value','+3000','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(42,'gh_stat2_label','مريض سنوياً','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(43,'gh_stat3_value','23','guest_house','text','2026-02-17 10:53:17','2026-02-17 15:43:27'),(44,'gh_stat3_label','فرع','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(45,'gh_stat4_value','24/7','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(46,'gh_stat4_label','استقبال','guest_house','text','2026-02-17 10:53:17','2026-02-17 10:53:17'),(47,'stats_projects','4','general','text','2026-02-17 12:42:02','2026-02-17 12:42:02'),(48,'stats_governorates','1','general','text','2026-02-17 12:42:02','2026-02-17 13:01:26'),(49,'campaign_stats_beneficiaries','15,000+','general','text','2026-02-17 13:13:56','2026-02-17 13:13:56'),(50,'campaign_stats_active','8','general','text','2026-02-17 13:13:56','2026-02-17 13:13:56'),(51,'campaign_stats_donations','2M+','general','text','2026-02-17 13:13:56','2026-02-17 13:13:56'),(52,'campaign_stats_governorates','3 محافظة','general','text','2026-02-17 13:13:56','2026-02-17 13:13:56'),(53,'featured_campaign_title','حملة الشتاء 2025','general','text','2026-02-17 13:15:06','2026-02-17 13:15:06'),(54,'featured_campaign_beneficiaries','2,500+','general','text','2026-02-17 13:15:06','2026-02-17 13:15:06'),(55,'featured_campaign_progress','65','general','text','2026-02-17 13:15:06','2026-02-17 13:15:06'),(56,'featured_campaign_button_text','ساهم الآن','general','text','2026-02-17 13:15:06','2026-02-17 13:15:06'),(57,'gh_home_title','ضيافة إنسان الخيريه','general','text','2026-02-17 14:31:08','2026-02-17 14:45:55'),(58,'gh_home_content','سلببببببببببببببببببببببببببببببببببببببببببب','general','text','2026-02-17 14:31:08','2026-02-17 14:45:55'),(59,'stats_beneficiaries_label','المستفيدون','general','text','2026-02-17 14:41:39','2026-02-17 14:41:39'),(60,'stats_branches_label','(كفرالشيخ والغربية)','general','text','2026-02-17 14:41:40','2026-02-17 15:06:53'),(61,'stats_donations_label','التبرعات','general','text','2026-02-17 14:41:40','2026-02-17 14:41:40'),(62,'stats_volunteers_label','المتطوعون','general','text','2026-02-17 14:41:40','2026-02-17 14:41:40'),(63,'stats_governorates_label','المحافظات','general','text','2026-02-17 14:56:34','2026-02-17 14:56:34'),(64,'stats_projects_label','مستفيد سنوياً','general','text','2026-02-17 14:56:34','2026-02-17 15:06:53'),(65,'stats_shares','20','general','text','2026-02-17 14:56:34','2026-02-17 14:58:20'),(66,'stats_shares_label','الأسهم','general','text','2026-02-17 14:56:34','2026-02-17 14:56:34'),(67,'headquarters_title','مقر مؤسسة إنسان','general','text','2026-02-17 16:33:41','2026-02-17 16:33:41'),(68,'headquarters_description','متواجدون في محافظة كفر الشيخ والغربية. زورونا في أي فرع من فروعنا للتبرع أو الاستفسار','general','text','2026-02-17 16:33:41','2026-02-17 16:33:41'),(69,'headquarters_stats_branches','1','general','text','2026-02-17 16:33:41','2026-02-17 16:33:41'),(70,'headquarters_stats_governorates','2','general','text','2026-02-17 16:33:41','2026-02-17 16:33:41'),(71,'headquarters_stats_employees','+200','general','text','2026-02-17 16:33:42','2026-02-17 16:33:42'),(72,'headquarters_stats_donors','+10K','general','text','2026-02-17 16:33:42','2026-02-17 16:33:42');
/*!40000 ALTER TABLE `web_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_testimonials`
--

DROP TABLE IF EXISTS `web_testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_testimonials`
--

LOCK TABLES `web_testimonials` WRITE;
/*!40000 ALTER TABLE `web_testimonials` DISABLE KEYS */;
INSERT INTO `web_testimonials` VALUES (2,'سارة علي','متطوعة','أكثر ما أعجبني هو التنظيم واحترام وقت المتطوعين. تجربة أفتخر بها.',5,NULL,'approved','2026-02-16 10:07:24','2026-02-16 10:07:24');
/*!40000 ALTER TABLE `web_testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_volunteer_requests`
--

DROP TABLE IF EXISTS `web_volunteer_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_volunteer_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_of_interest` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','contacted','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education_level` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faculty` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `university` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_job` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_experience` tinyint(1) NOT NULL DEFAULT '0',
  `skills` text COLLATE utf8mb4_unicode_ci,
  `goal` text COLLATE utf8mb4_unicode_ci,
  `expectations` text COLLATE utf8mb4_unicode_ci,
  `volunteer_hours` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_volunteer_requests`
--

LOCK TABLES `web_volunteer_requests` WRITE;
/*!40000 ALTER TABLE `web_volunteer_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `web_volunteer_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_volunteers_wall`
--

DROP TABLE IF EXISTS `web_volunteers_wall`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_volunteers_wall` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hours` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int(10) unsigned NOT NULL DEFAULT '1',
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_volunteers_wall`
--

LOCK TABLES `web_volunteers_wall` WRITE;
/*!40000 ALTER TABLE `web_volunteers_wall` DISABLE KEYS */;
INSERT INTO `web_volunteers_wall` VALUES (1,'محمد سامي','420',1,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(2,'ريما خالد','360',2,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24'),(3,'مصطفى حسن','310',3,NULL,'2026-02-16 10:07:24','2026-02-16 10:07:24');
/*!40000 ALTER TABLE `web_volunteers_wall` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workspace_rentals`
--

DROP TABLE IF EXISTS `workspace_rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workspace_rentals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint(20) unsigned NOT NULL,
  `renter_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `renter_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workspace_rentals_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `workspace_rentals_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workspace_rentals`
--

LOCK TABLES `workspace_rentals` WRITE;
/*!40000 ALTER TABLE `workspace_rentals` DISABLE KEYS */;
/*!40000 ALTER TABLE `workspace_rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workspaces`
--

DROP TABLE IF EXISTS `workspaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workspaces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `amenities` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `price_per_hour` decimal(10,2) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workspaces_manager_id_foreign` (`manager_id`),
  CONSTRAINT `workspaces_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workspaces`
--

LOCK TABLES `workspaces` WRITE;
-- 1. Mobile Home Items
CREATE TABLE IF NOT EXISTS `mobile_home_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `image_path` VARCHAR(255) NULL,
    `icon` VARCHAR(255) NULL,
    `price` DECIMAL(15, 2) NULL,
    `share_price` DECIMAL(15, 2) NULL,
    `details` TEXT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Mobile Case Applications
CREATE TABLE IF NOT EXISTS `mobile_case_applications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `applicant_name` VARCHAR(255) NOT NULL,
    `applicant_phone` VARCHAR(255) NOT NULL,
    `applicant_id_number` VARCHAR(255) NULL,
    `case_type` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `governorate` VARCHAR(255) NULL,
    `city` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `id_image_path` VARCHAR(255) NULL,
    `medical_report_path` VARCHAR(255) NULL,
    `status` VARCHAR(255) DEFAULT 'pending',
    `admin_notes` TEXT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Mobile Notifications
CREATE TABLE IF NOT EXISTS `mobile_notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `image_path` VARCHAR(255) NULL,
    `target_audience` VARCHAR(255) NULL,
    `is_sent` TINYINT(1) DEFAULT 0,
    `sent_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Mobile In-Kind Donations
CREATE TABLE IF NOT EXISTS `mobile_in_kind_donations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `donor_name` VARCHAR(255) NULL,
    `donor_phone` VARCHAR(255) NOT NULL,
    `item_name` VARCHAR(255) NOT NULL,
    `item_description` VARCHAR(255) NULL,
    `quantity` INT DEFAULT 1,
    `image_path` VARCHAR(255) NULL,
    `pickup_address` VARCHAR(255) NULL,
    `preferred_pickup_time` TIMESTAMP NULL,
    `status` VARCHAR(255) DEFAULT 'pending',
    `user_id` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Mobile Banners
CREATE TABLE IF NOT EXISTS `mobile_banners` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `image_path` VARCHAR(255) NULL,
    `title` VARCHAR(255) NULL,
    `link_type` VARCHAR(255) NULL,
    `link_id` VARCHAR(255) NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `workspaces` DISABLE KEYS */;
/*!40000 ALTER TABLE `workspaces` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-01 20:02:31

-- --------------------------------------------------------
-- NEW TABLES ADDED: Payment Transactions & Notification Logs 
-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--
DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE `payment_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `donor_id` bigint(20) unsigned DEFAULT NULL,
  `donation_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `gateway` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway_response` json DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_transactions_transaction_id_index` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `notification_logs`
--
DROP TABLE IF EXISTS `notification_logs`;
CREATE TABLE `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `provider_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Add payment_transaction_id to donations table
--
-- ALTER TABLE `donations` ADD COLUMN `payment_transaction_id` bigint(20) unsigned DEFAULT NULL;

