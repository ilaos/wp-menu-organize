-- MySQL dump 10.13  Distrib 8.0.35, for Win64 (x86_64)
--
-- Host: ::1    Database: local
-- ------------------------------------------------------
-- Server version	8.0.35

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
-- Table structure for table `wp_actionscheduler_actions`
--

DROP TABLE IF EXISTS `wp_actionscheduler_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_actions` (
  `action_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hook` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `scheduled_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `scheduled_date_local` datetime DEFAULT '0000-00-00 00:00:00',
  `args` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `schedule` longtext COLLATE utf8mb4_unicode_520_ci,
  `group_id` bigint unsigned NOT NULL DEFAULT '0',
  `attempts` int NOT NULL DEFAULT '0',
  `last_attempt_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `last_attempt_local` datetime DEFAULT '0000-00-00 00:00:00',
  `claim_id` bigint unsigned NOT NULL DEFAULT '0',
  `extended_args` varchar(8000) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`action_id`),
  KEY `hook` (`hook`),
  KEY `status` (`status`),
  KEY `scheduled_date_gmt` (`scheduled_date_gmt`),
  KEY `args` (`args`),
  KEY `group_id` (`group_id`),
  KEY `last_attempt_gmt` (`last_attempt_gmt`),
  KEY `claim_id_status_scheduled_date_gmt` (`claim_id`,`status`,`scheduled_date_gmt`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_actions`
--

LOCK TABLES `wp_actionscheduler_actions` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_actions` DISABLE KEYS */;
INSERT INTO `wp_actionscheduler_actions` VALUES (140,'action_scheduler/migration_hook','pending','2025-10-07 23:55:12','2025-10-07 23:55:12','[]','O:30:\"ActionScheduler_SimpleSchedule\":2:{s:22:\"\0*\0scheduled_timestamp\";i:1759881312;s:41:\"\0ActionScheduler_SimpleSchedule\0timestamp\";i:1759881312;}',1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,NULL);
/*!40000 ALTER TABLE `wp_actionscheduler_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_actionscheduler_claims`
--

DROP TABLE IF EXISTS `wp_actionscheduler_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_claims` (
  `claim_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_created_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`claim_id`),
  KEY `date_created_gmt` (`date_created_gmt`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_claims`
--

LOCK TABLES `wp_actionscheduler_claims` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_actionscheduler_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_actionscheduler_groups`
--

DROP TABLE IF EXISTS `wp_actionscheduler_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_groups` (
  `group_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `slug` (`slug`(191))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_groups`
--

LOCK TABLES `wp_actionscheduler_groups` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_groups` DISABLE KEYS */;
INSERT INTO `wp_actionscheduler_groups` VALUES (1,'action-scheduler-migration');
/*!40000 ALTER TABLE `wp_actionscheduler_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_actionscheduler_logs`
--

DROP TABLE IF EXISTS `wp_actionscheduler_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_logs` (
  `log_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `log_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `log_date_local` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`log_id`),
  KEY `action_id` (`action_id`),
  KEY `log_date_gmt` (`log_date_gmt`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_logs`
--

LOCK TABLES `wp_actionscheduler_logs` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_logs` DISABLE KEYS */;
INSERT INTO `wp_actionscheduler_logs` VALUES (1,140,'action created','2025-10-07 23:54:12','2025-10-07 23:54:12');
/*!40000 ALTER TABLE `wp_actionscheduler_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_commentmeta`
--

DROP TABLE IF EXISTS `wp_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_commentmeta`
--

LOCK TABLES `wp_commentmeta` WRITE;
/*!40000 ALTER TABLE `wp_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_comments`
--

DROP TABLE IF EXISTS `wp_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_comments` (
  `comment_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_karma` int NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'comment',
  `comment_parent` bigint unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_comments`
--

LOCK TABLES `wp_comments` WRITE;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;
INSERT INTO `wp_comments` VALUES (1,1,'A WordPress Commenter','wapuu@wordpress.example','https://wordpress.org/','','2025-07-08 15:41:52','2025-07-08 15:41:52','Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com/\">Gravatar</a>.',0,'1','','comment',0,0);
/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_links`
--

DROP TABLE IF EXISTS `wp_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_links` (
  `link_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint unsigned NOT NULL DEFAULT '1',
  `link_rating` int NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_links`
--

LOCK TABLES `wp_links` WRITE;
/*!40000 ALTER TABLE `wp_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_options`
--

DROP TABLE IF EXISTS `wp_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_options` (
  `option_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=1755 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_options`
--

LOCK TABLES `wp_options` WRITE;
/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;
INSERT INTO `wp_options` VALUES (1,'cron','a:15:{i:1759967053;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1759981462;a:1:{s:21:\"wp_update_user_counts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1759984911;a:1:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1759986711;a:1:{s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1759988511;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1760024513;a:1:{s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1760024662;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1760024665;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1760030416;a:1:{s:24:\"sfb_purge_expired_drafts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1760039822;a:1:{s:37:\"siteground_optimizer_check_assets_dir\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1760364000;a:1:{s:28:\"wpforms_email_summaries_cron\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:30:\"wpforms_email_summaries_weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1760490643;a:1:{s:30:\"wp_delete_temp_updater_backups\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1760542913;a:1:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1760918400;a:1:{s:44:\"siteground_optimizer_performance_report_cron\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:15:\"sg_once_a_month\";s:4:\"args\";a:0:{}s:8:\"interval\";i:2592000;}}}s:7:\"version\";i:2;}','on');
INSERT INTO `wp_options` VALUES (2,'siteurl','http://my-playground.local','on');
INSERT INTO `wp_options` VALUES (3,'home','http://my-playground.local','on');
INSERT INTO `wp_options` VALUES (4,'blogname','My Playground','on');
INSERT INTO `wp_options` VALUES (5,'blogdescription','','on');
INSERT INTO `wp_options` VALUES (6,'users_can_register','0','on');
INSERT INTO `wp_options` VALUES (7,'admin_email','dev-email@wpengine.local','on');
INSERT INTO `wp_options` VALUES (8,'start_of_week','1','on');
INSERT INTO `wp_options` VALUES (9,'use_balanceTags','0','on');
INSERT INTO `wp_options` VALUES (10,'use_smilies','1','on');
INSERT INTO `wp_options` VALUES (11,'require_name_email','1','on');
INSERT INTO `wp_options` VALUES (12,'comments_notify','1','on');
INSERT INTO `wp_options` VALUES (13,'posts_per_rss','10','on');
INSERT INTO `wp_options` VALUES (14,'rss_use_excerpt','0','on');
INSERT INTO `wp_options` VALUES (15,'mailserver_url','mail.example.com','on');
INSERT INTO `wp_options` VALUES (16,'mailserver_login','login@example.com','on');
INSERT INTO `wp_options` VALUES (17,'mailserver_pass','','on');
INSERT INTO `wp_options` VALUES (18,'mailserver_port','110','on');
INSERT INTO `wp_options` VALUES (19,'default_category','1','on');
INSERT INTO `wp_options` VALUES (20,'default_comment_status','open','on');
INSERT INTO `wp_options` VALUES (21,'default_ping_status','open','on');
INSERT INTO `wp_options` VALUES (22,'default_pingback_flag','1','on');
INSERT INTO `wp_options` VALUES (23,'posts_per_page','10','on');
INSERT INTO `wp_options` VALUES (24,'date_format','F j, Y','on');
INSERT INTO `wp_options` VALUES (25,'time_format','g:i a','on');
INSERT INTO `wp_options` VALUES (26,'links_updated_date_format','F j, Y g:i a','on');
INSERT INTO `wp_options` VALUES (27,'comment_moderation','0','on');
INSERT INTO `wp_options` VALUES (28,'moderation_notify','1','on');
INSERT INTO `wp_options` VALUES (29,'permalink_structure','/%postname%/','on');
INSERT INTO `wp_options` VALUES (30,'rewrite_rules','a:94:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:13:\"favicon\\.ico$\";s:19:\"index.php?favicon=1\";s:12:\"sitemap\\.xml\";s:24:\"index.php??sitemap=index\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";s:27:\"[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\"[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\"[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\"[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"([^/]+)/embed/?$\";s:37:\"index.php?name=$matches[1]&embed=true\";s:20:\"([^/]+)/trackback/?$\";s:31:\"index.php?name=$matches[1]&tb=1\";s:40:\"([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:35:\"([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:28:\"([^/]+)/page/?([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&paged=$matches[2]\";s:35:\"([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&cpage=$matches[2]\";s:24:\"([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?name=$matches[1]&page=$matches[2]\";s:16:\"[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:26:\"[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:46:\"[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:22:\"[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";}','on');
INSERT INTO `wp_options` VALUES (31,'hack_file','0','on');
INSERT INTO `wp_options` VALUES (32,'blog_charset','UTF-8','on');
INSERT INTO `wp_options` VALUES (33,'moderation_keys','','off');
INSERT INTO `wp_options` VALUES (34,'active_plugins','a:2:{i:0;s:57:\"Submittal & Spec Sheet Builder/submittal-form-builder.php\";i:1;s:31:\"sg-cachepress/sg-cachepress.php\";}','on');
INSERT INTO `wp_options` VALUES (35,'category_base','','on');
INSERT INTO `wp_options` VALUES (36,'ping_sites','https://rpc.pingomatic.com/','on');
INSERT INTO `wp_options` VALUES (37,'comment_max_links','2','on');
INSERT INTO `wp_options` VALUES (38,'gmt_offset','0','on');
INSERT INTO `wp_options` VALUES (39,'default_email_category','1','on');
INSERT INTO `wp_options` VALUES (40,'recently_edited','','off');
INSERT INTO `wp_options` VALUES (41,'template','hello-elementor','on');
INSERT INTO `wp_options` VALUES (42,'stylesheet','hello-elementor','on');
INSERT INTO `wp_options` VALUES (43,'comment_registration','0','on');
INSERT INTO `wp_options` VALUES (44,'html_type','text/html','on');
INSERT INTO `wp_options` VALUES (45,'use_trackback','0','on');
INSERT INTO `wp_options` VALUES (46,'default_role','subscriber','on');
INSERT INTO `wp_options` VALUES (47,'db_version','60421','on');
INSERT INTO `wp_options` VALUES (48,'uploads_use_yearmonth_folders','1','on');
INSERT INTO `wp_options` VALUES (49,'upload_path','','on');
INSERT INTO `wp_options` VALUES (50,'blog_public','1','on');
INSERT INTO `wp_options` VALUES (51,'default_link_category','2','on');
INSERT INTO `wp_options` VALUES (52,'show_on_front','posts','on');
INSERT INTO `wp_options` VALUES (53,'tag_base','','on');
INSERT INTO `wp_options` VALUES (54,'show_avatars','1','on');
INSERT INTO `wp_options` VALUES (55,'avatar_rating','G','on');
INSERT INTO `wp_options` VALUES (56,'upload_url_path','','on');
INSERT INTO `wp_options` VALUES (57,'thumbnail_size_w','150','on');
INSERT INTO `wp_options` VALUES (58,'thumbnail_size_h','150','on');
INSERT INTO `wp_options` VALUES (59,'thumbnail_crop','1','on');
INSERT INTO `wp_options` VALUES (60,'medium_size_w','300','on');
INSERT INTO `wp_options` VALUES (61,'medium_size_h','300','on');
INSERT INTO `wp_options` VALUES (62,'avatar_default','mystery','on');
INSERT INTO `wp_options` VALUES (63,'large_size_w','1024','on');
INSERT INTO `wp_options` VALUES (64,'large_size_h','1024','on');
INSERT INTO `wp_options` VALUES (65,'image_default_link_type','none','on');
INSERT INTO `wp_options` VALUES (66,'image_default_size','','on');
INSERT INTO `wp_options` VALUES (67,'image_default_align','','on');
INSERT INTO `wp_options` VALUES (68,'close_comments_for_old_posts','0','on');
INSERT INTO `wp_options` VALUES (69,'close_comments_days_old','14','on');
INSERT INTO `wp_options` VALUES (70,'thread_comments','1','on');
INSERT INTO `wp_options` VALUES (71,'thread_comments_depth','5','on');
INSERT INTO `wp_options` VALUES (72,'page_comments','0','on');
INSERT INTO `wp_options` VALUES (73,'comments_per_page','50','on');
INSERT INTO `wp_options` VALUES (74,'default_comments_page','newest','on');
INSERT INTO `wp_options` VALUES (75,'comment_order','asc','on');
INSERT INTO `wp_options` VALUES (76,'sticky_posts','a:0:{}','on');
INSERT INTO `wp_options` VALUES (77,'widget_categories','a:0:{}','on');
INSERT INTO `wp_options` VALUES (78,'widget_text','a:0:{}','on');
INSERT INTO `wp_options` VALUES (79,'widget_rss','a:0:{}','on');
INSERT INTO `wp_options` VALUES (80,'uninstall_plugins','a:0:{}','off');
INSERT INTO `wp_options` VALUES (81,'timezone_string','','on');
INSERT INTO `wp_options` VALUES (82,'page_for_posts','0','on');
INSERT INTO `wp_options` VALUES (83,'page_on_front','0','on');
INSERT INTO `wp_options` VALUES (84,'default_post_format','0','on');
INSERT INTO `wp_options` VALUES (85,'link_manager_enabled','0','on');
INSERT INTO `wp_options` VALUES (86,'finished_splitting_shared_terms','1','on');
INSERT INTO `wp_options` VALUES (87,'site_icon','0','on');
INSERT INTO `wp_options` VALUES (88,'medium_large_size_w','768','on');
INSERT INTO `wp_options` VALUES (89,'medium_large_size_h','0','on');
INSERT INTO `wp_options` VALUES (90,'wp_page_for_privacy_policy','3','on');
INSERT INTO `wp_options` VALUES (91,'show_comments_cookies_opt_in','1','on');
INSERT INTO `wp_options` VALUES (92,'admin_email_lifespan','1767541311','on');
INSERT INTO `wp_options` VALUES (93,'disallowed_keys','','off');
INSERT INTO `wp_options` VALUES (94,'comment_previously_approved','1','on');
INSERT INTO `wp_options` VALUES (95,'auto_plugin_theme_update_emails','a:0:{}','off');
INSERT INTO `wp_options` VALUES (96,'auto_update_core_dev','enabled','on');
INSERT INTO `wp_options` VALUES (97,'auto_update_core_minor','enabled','on');
INSERT INTO `wp_options` VALUES (98,'auto_update_core_major','enabled','on');
INSERT INTO `wp_options` VALUES (99,'wp_force_deactivated_plugins','a:0:{}','on');
INSERT INTO `wp_options` VALUES (100,'wp_attachment_pages_enabled','0','on');
INSERT INTO `wp_options` VALUES (101,'initial_db_version','58975','on');
INSERT INTO `wp_options` VALUES (102,'wp_user_roles','a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:61:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}','on');
INSERT INTO `wp_options` VALUES (103,'fresh_site','0','off');
INSERT INTO `wp_options` VALUES (104,'user_count','5','off');
INSERT INTO `wp_options` VALUES (105,'widget_block','a:6:{i:2;a:1:{s:7:\"content\";s:19:\"<!-- wp:search /-->\";}i:3;a:1:{s:7:\"content\";s:154:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Posts</h2><!-- /wp:heading --><!-- wp:latest-posts /--></div><!-- /wp:group -->\";}i:4;a:1:{s:7:\"content\";s:227:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Comments</h2><!-- /wp:heading --><!-- wp:latest-comments {\"displayAvatar\":false,\"displayDate\":false,\"displayExcerpt\":false} /--></div><!-- /wp:group -->\";}i:5;a:1:{s:7:\"content\";s:146:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Archives</h2><!-- /wp:heading --><!-- wp:archives /--></div><!-- /wp:group -->\";}i:6;a:1:{s:7:\"content\";s:150:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Categories</h2><!-- /wp:heading --><!-- wp:categories /--></div><!-- /wp:group -->\";}s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (106,'sidebars_widgets','a:2:{s:19:\"wp_inactive_widgets\";a:5:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";i:3;s:7:\"block-5\";i:4;s:7:\"block-6\";}s:13:\"array_version\";i:3;}','auto');
INSERT INTO `wp_options` VALUES (107,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (108,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (109,'widget_archives','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (110,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (111,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (112,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (113,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (114,'widget_meta','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (115,'widget_search','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (116,'widget_recent-posts','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (117,'widget_recent-comments','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (118,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (119,'widget_nav_menu','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (120,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (121,'_transient_wp_core_block_css_files','a:2:{s:7:\"version\";s:5:\"6.8.1\";s:5:\"files\";a:536:{i:0;s:23:\"archives/editor-rtl.css\";i:1;s:27:\"archives/editor-rtl.min.css\";i:2;s:19:\"archives/editor.css\";i:3;s:23:\"archives/editor.min.css\";i:4;s:22:\"archives/style-rtl.css\";i:5;s:26:\"archives/style-rtl.min.css\";i:6;s:18:\"archives/style.css\";i:7;s:22:\"archives/style.min.css\";i:8;s:20:\"audio/editor-rtl.css\";i:9;s:24:\"audio/editor-rtl.min.css\";i:10;s:16:\"audio/editor.css\";i:11;s:20:\"audio/editor.min.css\";i:12;s:19:\"audio/style-rtl.css\";i:13;s:23:\"audio/style-rtl.min.css\";i:14;s:15:\"audio/style.css\";i:15;s:19:\"audio/style.min.css\";i:16;s:19:\"audio/theme-rtl.css\";i:17;s:23:\"audio/theme-rtl.min.css\";i:18;s:15:\"audio/theme.css\";i:19;s:19:\"audio/theme.min.css\";i:20;s:21:\"avatar/editor-rtl.css\";i:21;s:25:\"avatar/editor-rtl.min.css\";i:22;s:17:\"avatar/editor.css\";i:23;s:21:\"avatar/editor.min.css\";i:24;s:20:\"avatar/style-rtl.css\";i:25;s:24:\"avatar/style-rtl.min.css\";i:26;s:16:\"avatar/style.css\";i:27;s:20:\"avatar/style.min.css\";i:28;s:21:\"button/editor-rtl.css\";i:29;s:25:\"button/editor-rtl.min.css\";i:30;s:17:\"button/editor.css\";i:31;s:21:\"button/editor.min.css\";i:32;s:20:\"button/style-rtl.css\";i:33;s:24:\"button/style-rtl.min.css\";i:34;s:16:\"button/style.css\";i:35;s:20:\"button/style.min.css\";i:36;s:22:\"buttons/editor-rtl.css\";i:37;s:26:\"buttons/editor-rtl.min.css\";i:38;s:18:\"buttons/editor.css\";i:39;s:22:\"buttons/editor.min.css\";i:40;s:21:\"buttons/style-rtl.css\";i:41;s:25:\"buttons/style-rtl.min.css\";i:42;s:17:\"buttons/style.css\";i:43;s:21:\"buttons/style.min.css\";i:44;s:22:\"calendar/style-rtl.css\";i:45;s:26:\"calendar/style-rtl.min.css\";i:46;s:18:\"calendar/style.css\";i:47;s:22:\"calendar/style.min.css\";i:48;s:25:\"categories/editor-rtl.css\";i:49;s:29:\"categories/editor-rtl.min.css\";i:50;s:21:\"categories/editor.css\";i:51;s:25:\"categories/editor.min.css\";i:52;s:24:\"categories/style-rtl.css\";i:53;s:28:\"categories/style-rtl.min.css\";i:54;s:20:\"categories/style.css\";i:55;s:24:\"categories/style.min.css\";i:56;s:19:\"code/editor-rtl.css\";i:57;s:23:\"code/editor-rtl.min.css\";i:58;s:15:\"code/editor.css\";i:59;s:19:\"code/editor.min.css\";i:60;s:18:\"code/style-rtl.css\";i:61;s:22:\"code/style-rtl.min.css\";i:62;s:14:\"code/style.css\";i:63;s:18:\"code/style.min.css\";i:64;s:18:\"code/theme-rtl.css\";i:65;s:22:\"code/theme-rtl.min.css\";i:66;s:14:\"code/theme.css\";i:67;s:18:\"code/theme.min.css\";i:68;s:22:\"columns/editor-rtl.css\";i:69;s:26:\"columns/editor-rtl.min.css\";i:70;s:18:\"columns/editor.css\";i:71;s:22:\"columns/editor.min.css\";i:72;s:21:\"columns/style-rtl.css\";i:73;s:25:\"columns/style-rtl.min.css\";i:74;s:17:\"columns/style.css\";i:75;s:21:\"columns/style.min.css\";i:76;s:33:\"comment-author-name/style-rtl.css\";i:77;s:37:\"comment-author-name/style-rtl.min.css\";i:78;s:29:\"comment-author-name/style.css\";i:79;s:33:\"comment-author-name/style.min.css\";i:80;s:29:\"comment-content/style-rtl.css\";i:81;s:33:\"comment-content/style-rtl.min.css\";i:82;s:25:\"comment-content/style.css\";i:83;s:29:\"comment-content/style.min.css\";i:84;s:26:\"comment-date/style-rtl.css\";i:85;s:30:\"comment-date/style-rtl.min.css\";i:86;s:22:\"comment-date/style.css\";i:87;s:26:\"comment-date/style.min.css\";i:88;s:31:\"comment-edit-link/style-rtl.css\";i:89;s:35:\"comment-edit-link/style-rtl.min.css\";i:90;s:27:\"comment-edit-link/style.css\";i:91;s:31:\"comment-edit-link/style.min.css\";i:92;s:32:\"comment-reply-link/style-rtl.css\";i:93;s:36:\"comment-reply-link/style-rtl.min.css\";i:94;s:28:\"comment-reply-link/style.css\";i:95;s:32:\"comment-reply-link/style.min.css\";i:96;s:30:\"comment-template/style-rtl.css\";i:97;s:34:\"comment-template/style-rtl.min.css\";i:98;s:26:\"comment-template/style.css\";i:99;s:30:\"comment-template/style.min.css\";i:100;s:42:\"comments-pagination-numbers/editor-rtl.css\";i:101;s:46:\"comments-pagination-numbers/editor-rtl.min.css\";i:102;s:38:\"comments-pagination-numbers/editor.css\";i:103;s:42:\"comments-pagination-numbers/editor.min.css\";i:104;s:34:\"comments-pagination/editor-rtl.css\";i:105;s:38:\"comments-pagination/editor-rtl.min.css\";i:106;s:30:\"comments-pagination/editor.css\";i:107;s:34:\"comments-pagination/editor.min.css\";i:108;s:33:\"comments-pagination/style-rtl.css\";i:109;s:37:\"comments-pagination/style-rtl.min.css\";i:110;s:29:\"comments-pagination/style.css\";i:111;s:33:\"comments-pagination/style.min.css\";i:112;s:29:\"comments-title/editor-rtl.css\";i:113;s:33:\"comments-title/editor-rtl.min.css\";i:114;s:25:\"comments-title/editor.css\";i:115;s:29:\"comments-title/editor.min.css\";i:116;s:23:\"comments/editor-rtl.css\";i:117;s:27:\"comments/editor-rtl.min.css\";i:118;s:19:\"comments/editor.css\";i:119;s:23:\"comments/editor.min.css\";i:120;s:22:\"comments/style-rtl.css\";i:121;s:26:\"comments/style-rtl.min.css\";i:122;s:18:\"comments/style.css\";i:123;s:22:\"comments/style.min.css\";i:124;s:20:\"cover/editor-rtl.css\";i:125;s:24:\"cover/editor-rtl.min.css\";i:126;s:16:\"cover/editor.css\";i:127;s:20:\"cover/editor.min.css\";i:128;s:19:\"cover/style-rtl.css\";i:129;s:23:\"cover/style-rtl.min.css\";i:130;s:15:\"cover/style.css\";i:131;s:19:\"cover/style.min.css\";i:132;s:22:\"details/editor-rtl.css\";i:133;s:26:\"details/editor-rtl.min.css\";i:134;s:18:\"details/editor.css\";i:135;s:22:\"details/editor.min.css\";i:136;s:21:\"details/style-rtl.css\";i:137;s:25:\"details/style-rtl.min.css\";i:138;s:17:\"details/style.css\";i:139;s:21:\"details/style.min.css\";i:140;s:20:\"embed/editor-rtl.css\";i:141;s:24:\"embed/editor-rtl.min.css\";i:142;s:16:\"embed/editor.css\";i:143;s:20:\"embed/editor.min.css\";i:144;s:19:\"embed/style-rtl.css\";i:145;s:23:\"embed/style-rtl.min.css\";i:146;s:15:\"embed/style.css\";i:147;s:19:\"embed/style.min.css\";i:148;s:19:\"embed/theme-rtl.css\";i:149;s:23:\"embed/theme-rtl.min.css\";i:150;s:15:\"embed/theme.css\";i:151;s:19:\"embed/theme.min.css\";i:152;s:19:\"file/editor-rtl.css\";i:153;s:23:\"file/editor-rtl.min.css\";i:154;s:15:\"file/editor.css\";i:155;s:19:\"file/editor.min.css\";i:156;s:18:\"file/style-rtl.css\";i:157;s:22:\"file/style-rtl.min.css\";i:158;s:14:\"file/style.css\";i:159;s:18:\"file/style.min.css\";i:160;s:23:\"footnotes/style-rtl.css\";i:161;s:27:\"footnotes/style-rtl.min.css\";i:162;s:19:\"footnotes/style.css\";i:163;s:23:\"footnotes/style.min.css\";i:164;s:23:\"freeform/editor-rtl.css\";i:165;s:27:\"freeform/editor-rtl.min.css\";i:166;s:19:\"freeform/editor.css\";i:167;s:23:\"freeform/editor.min.css\";i:168;s:22:\"gallery/editor-rtl.css\";i:169;s:26:\"gallery/editor-rtl.min.css\";i:170;s:18:\"gallery/editor.css\";i:171;s:22:\"gallery/editor.min.css\";i:172;s:21:\"gallery/style-rtl.css\";i:173;s:25:\"gallery/style-rtl.min.css\";i:174;s:17:\"gallery/style.css\";i:175;s:21:\"gallery/style.min.css\";i:176;s:21:\"gallery/theme-rtl.css\";i:177;s:25:\"gallery/theme-rtl.min.css\";i:178;s:17:\"gallery/theme.css\";i:179;s:21:\"gallery/theme.min.css\";i:180;s:20:\"group/editor-rtl.css\";i:181;s:24:\"group/editor-rtl.min.css\";i:182;s:16:\"group/editor.css\";i:183;s:20:\"group/editor.min.css\";i:184;s:19:\"group/style-rtl.css\";i:185;s:23:\"group/style-rtl.min.css\";i:186;s:15:\"group/style.css\";i:187;s:19:\"group/style.min.css\";i:188;s:19:\"group/theme-rtl.css\";i:189;s:23:\"group/theme-rtl.min.css\";i:190;s:15:\"group/theme.css\";i:191;s:19:\"group/theme.min.css\";i:192;s:21:\"heading/style-rtl.css\";i:193;s:25:\"heading/style-rtl.min.css\";i:194;s:17:\"heading/style.css\";i:195;s:21:\"heading/style.min.css\";i:196;s:19:\"html/editor-rtl.css\";i:197;s:23:\"html/editor-rtl.min.css\";i:198;s:15:\"html/editor.css\";i:199;s:19:\"html/editor.min.css\";i:200;s:20:\"image/editor-rtl.css\";i:201;s:24:\"image/editor-rtl.min.css\";i:202;s:16:\"image/editor.css\";i:203;s:20:\"image/editor.min.css\";i:204;s:19:\"image/style-rtl.css\";i:205;s:23:\"image/style-rtl.min.css\";i:206;s:15:\"image/style.css\";i:207;s:19:\"image/style.min.css\";i:208;s:19:\"image/theme-rtl.css\";i:209;s:23:\"image/theme-rtl.min.css\";i:210;s:15:\"image/theme.css\";i:211;s:19:\"image/theme.min.css\";i:212;s:29:\"latest-comments/style-rtl.css\";i:213;s:33:\"latest-comments/style-rtl.min.css\";i:214;s:25:\"latest-comments/style.css\";i:215;s:29:\"latest-comments/style.min.css\";i:216;s:27:\"latest-posts/editor-rtl.css\";i:217;s:31:\"latest-posts/editor-rtl.min.css\";i:218;s:23:\"latest-posts/editor.css\";i:219;s:27:\"latest-posts/editor.min.css\";i:220;s:26:\"latest-posts/style-rtl.css\";i:221;s:30:\"latest-posts/style-rtl.min.css\";i:222;s:22:\"latest-posts/style.css\";i:223;s:26:\"latest-posts/style.min.css\";i:224;s:18:\"list/style-rtl.css\";i:225;s:22:\"list/style-rtl.min.css\";i:226;s:14:\"list/style.css\";i:227;s:18:\"list/style.min.css\";i:228;s:22:\"loginout/style-rtl.css\";i:229;s:26:\"loginout/style-rtl.min.css\";i:230;s:18:\"loginout/style.css\";i:231;s:22:\"loginout/style.min.css\";i:232;s:25:\"media-text/editor-rtl.css\";i:233;s:29:\"media-text/editor-rtl.min.css\";i:234;s:21:\"media-text/editor.css\";i:235;s:25:\"media-text/editor.min.css\";i:236;s:24:\"media-text/style-rtl.css\";i:237;s:28:\"media-text/style-rtl.min.css\";i:238;s:20:\"media-text/style.css\";i:239;s:24:\"media-text/style.min.css\";i:240;s:19:\"more/editor-rtl.css\";i:241;s:23:\"more/editor-rtl.min.css\";i:242;s:15:\"more/editor.css\";i:243;s:19:\"more/editor.min.css\";i:244;s:30:\"navigation-link/editor-rtl.css\";i:245;s:34:\"navigation-link/editor-rtl.min.css\";i:246;s:26:\"navigation-link/editor.css\";i:247;s:30:\"navigation-link/editor.min.css\";i:248;s:29:\"navigation-link/style-rtl.css\";i:249;s:33:\"navigation-link/style-rtl.min.css\";i:250;s:25:\"navigation-link/style.css\";i:251;s:29:\"navigation-link/style.min.css\";i:252;s:33:\"navigation-submenu/editor-rtl.css\";i:253;s:37:\"navigation-submenu/editor-rtl.min.css\";i:254;s:29:\"navigation-submenu/editor.css\";i:255;s:33:\"navigation-submenu/editor.min.css\";i:256;s:25:\"navigation/editor-rtl.css\";i:257;s:29:\"navigation/editor-rtl.min.css\";i:258;s:21:\"navigation/editor.css\";i:259;s:25:\"navigation/editor.min.css\";i:260;s:24:\"navigation/style-rtl.css\";i:261;s:28:\"navigation/style-rtl.min.css\";i:262;s:20:\"navigation/style.css\";i:263;s:24:\"navigation/style.min.css\";i:264;s:23:\"nextpage/editor-rtl.css\";i:265;s:27:\"nextpage/editor-rtl.min.css\";i:266;s:19:\"nextpage/editor.css\";i:267;s:23:\"nextpage/editor.min.css\";i:268;s:24:\"page-list/editor-rtl.css\";i:269;s:28:\"page-list/editor-rtl.min.css\";i:270;s:20:\"page-list/editor.css\";i:271;s:24:\"page-list/editor.min.css\";i:272;s:23:\"page-list/style-rtl.css\";i:273;s:27:\"page-list/style-rtl.min.css\";i:274;s:19:\"page-list/style.css\";i:275;s:23:\"page-list/style.min.css\";i:276;s:24:\"paragraph/editor-rtl.css\";i:277;s:28:\"paragraph/editor-rtl.min.css\";i:278;s:20:\"paragraph/editor.css\";i:279;s:24:\"paragraph/editor.min.css\";i:280;s:23:\"paragraph/style-rtl.css\";i:281;s:27:\"paragraph/style-rtl.min.css\";i:282;s:19:\"paragraph/style.css\";i:283;s:23:\"paragraph/style.min.css\";i:284;s:35:\"post-author-biography/style-rtl.css\";i:285;s:39:\"post-author-biography/style-rtl.min.css\";i:286;s:31:\"post-author-biography/style.css\";i:287;s:35:\"post-author-biography/style.min.css\";i:288;s:30:\"post-author-name/style-rtl.css\";i:289;s:34:\"post-author-name/style-rtl.min.css\";i:290;s:26:\"post-author-name/style.css\";i:291;s:30:\"post-author-name/style.min.css\";i:292;s:26:\"post-author/editor-rtl.css\";i:293;s:30:\"post-author/editor-rtl.min.css\";i:294;s:22:\"post-author/editor.css\";i:295;s:26:\"post-author/editor.min.css\";i:296;s:25:\"post-author/style-rtl.css\";i:297;s:29:\"post-author/style-rtl.min.css\";i:298;s:21:\"post-author/style.css\";i:299;s:25:\"post-author/style.min.css\";i:300;s:33:\"post-comments-form/editor-rtl.css\";i:301;s:37:\"post-comments-form/editor-rtl.min.css\";i:302;s:29:\"post-comments-form/editor.css\";i:303;s:33:\"post-comments-form/editor.min.css\";i:304;s:32:\"post-comments-form/style-rtl.css\";i:305;s:36:\"post-comments-form/style-rtl.min.css\";i:306;s:28:\"post-comments-form/style.css\";i:307;s:32:\"post-comments-form/style.min.css\";i:308;s:26:\"post-content/style-rtl.css\";i:309;s:30:\"post-content/style-rtl.min.css\";i:310;s:22:\"post-content/style.css\";i:311;s:26:\"post-content/style.min.css\";i:312;s:23:\"post-date/style-rtl.css\";i:313;s:27:\"post-date/style-rtl.min.css\";i:314;s:19:\"post-date/style.css\";i:315;s:23:\"post-date/style.min.css\";i:316;s:27:\"post-excerpt/editor-rtl.css\";i:317;s:31:\"post-excerpt/editor-rtl.min.css\";i:318;s:23:\"post-excerpt/editor.css\";i:319;s:27:\"post-excerpt/editor.min.css\";i:320;s:26:\"post-excerpt/style-rtl.css\";i:321;s:30:\"post-excerpt/style-rtl.min.css\";i:322;s:22:\"post-excerpt/style.css\";i:323;s:26:\"post-excerpt/style.min.css\";i:324;s:34:\"post-featured-image/editor-rtl.css\";i:325;s:38:\"post-featured-image/editor-rtl.min.css\";i:326;s:30:\"post-featured-image/editor.css\";i:327;s:34:\"post-featured-image/editor.min.css\";i:328;s:33:\"post-featured-image/style-rtl.css\";i:329;s:37:\"post-featured-image/style-rtl.min.css\";i:330;s:29:\"post-featured-image/style.css\";i:331;s:33:\"post-featured-image/style.min.css\";i:332;s:34:\"post-navigation-link/style-rtl.css\";i:333;s:38:\"post-navigation-link/style-rtl.min.css\";i:334;s:30:\"post-navigation-link/style.css\";i:335;s:34:\"post-navigation-link/style.min.css\";i:336;s:27:\"post-template/style-rtl.css\";i:337;s:31:\"post-template/style-rtl.min.css\";i:338;s:23:\"post-template/style.css\";i:339;s:27:\"post-template/style.min.css\";i:340;s:24:\"post-terms/style-rtl.css\";i:341;s:28:\"post-terms/style-rtl.min.css\";i:342;s:20:\"post-terms/style.css\";i:343;s:24:\"post-terms/style.min.css\";i:344;s:24:\"post-title/style-rtl.css\";i:345;s:28:\"post-title/style-rtl.min.css\";i:346;s:20:\"post-title/style.css\";i:347;s:24:\"post-title/style.min.css\";i:348;s:26:\"preformatted/style-rtl.css\";i:349;s:30:\"preformatted/style-rtl.min.css\";i:350;s:22:\"preformatted/style.css\";i:351;s:26:\"preformatted/style.min.css\";i:352;s:24:\"pullquote/editor-rtl.css\";i:353;s:28:\"pullquote/editor-rtl.min.css\";i:354;s:20:\"pullquote/editor.css\";i:355;s:24:\"pullquote/editor.min.css\";i:356;s:23:\"pullquote/style-rtl.css\";i:357;s:27:\"pullquote/style-rtl.min.css\";i:358;s:19:\"pullquote/style.css\";i:359;s:23:\"pullquote/style.min.css\";i:360;s:23:\"pullquote/theme-rtl.css\";i:361;s:27:\"pullquote/theme-rtl.min.css\";i:362;s:19:\"pullquote/theme.css\";i:363;s:23:\"pullquote/theme.min.css\";i:364;s:39:\"query-pagination-numbers/editor-rtl.css\";i:365;s:43:\"query-pagination-numbers/editor-rtl.min.css\";i:366;s:35:\"query-pagination-numbers/editor.css\";i:367;s:39:\"query-pagination-numbers/editor.min.css\";i:368;s:31:\"query-pagination/editor-rtl.css\";i:369;s:35:\"query-pagination/editor-rtl.min.css\";i:370;s:27:\"query-pagination/editor.css\";i:371;s:31:\"query-pagination/editor.min.css\";i:372;s:30:\"query-pagination/style-rtl.css\";i:373;s:34:\"query-pagination/style-rtl.min.css\";i:374;s:26:\"query-pagination/style.css\";i:375;s:30:\"query-pagination/style.min.css\";i:376;s:25:\"query-title/style-rtl.css\";i:377;s:29:\"query-title/style-rtl.min.css\";i:378;s:21:\"query-title/style.css\";i:379;s:25:\"query-title/style.min.css\";i:380;s:25:\"query-total/style-rtl.css\";i:381;s:29:\"query-total/style-rtl.min.css\";i:382;s:21:\"query-total/style.css\";i:383;s:25:\"query-total/style.min.css\";i:384;s:20:\"query/editor-rtl.css\";i:385;s:24:\"query/editor-rtl.min.css\";i:386;s:16:\"query/editor.css\";i:387;s:20:\"query/editor.min.css\";i:388;s:19:\"quote/style-rtl.css\";i:389;s:23:\"quote/style-rtl.min.css\";i:390;s:15:\"quote/style.css\";i:391;s:19:\"quote/style.min.css\";i:392;s:19:\"quote/theme-rtl.css\";i:393;s:23:\"quote/theme-rtl.min.css\";i:394;s:15:\"quote/theme.css\";i:395;s:19:\"quote/theme.min.css\";i:396;s:23:\"read-more/style-rtl.css\";i:397;s:27:\"read-more/style-rtl.min.css\";i:398;s:19:\"read-more/style.css\";i:399;s:23:\"read-more/style.min.css\";i:400;s:18:\"rss/editor-rtl.css\";i:401;s:22:\"rss/editor-rtl.min.css\";i:402;s:14:\"rss/editor.css\";i:403;s:18:\"rss/editor.min.css\";i:404;s:17:\"rss/style-rtl.css\";i:405;s:21:\"rss/style-rtl.min.css\";i:406;s:13:\"rss/style.css\";i:407;s:17:\"rss/style.min.css\";i:408;s:21:\"search/editor-rtl.css\";i:409;s:25:\"search/editor-rtl.min.css\";i:410;s:17:\"search/editor.css\";i:411;s:21:\"search/editor.min.css\";i:412;s:20:\"search/style-rtl.css\";i:413;s:24:\"search/style-rtl.min.css\";i:414;s:16:\"search/style.css\";i:415;s:20:\"search/style.min.css\";i:416;s:20:\"search/theme-rtl.css\";i:417;s:24:\"search/theme-rtl.min.css\";i:418;s:16:\"search/theme.css\";i:419;s:20:\"search/theme.min.css\";i:420;s:24:\"separator/editor-rtl.css\";i:421;s:28:\"separator/editor-rtl.min.css\";i:422;s:20:\"separator/editor.css\";i:423;s:24:\"separator/editor.min.css\";i:424;s:23:\"separator/style-rtl.css\";i:425;s:27:\"separator/style-rtl.min.css\";i:426;s:19:\"separator/style.css\";i:427;s:23:\"separator/style.min.css\";i:428;s:23:\"separator/theme-rtl.css\";i:429;s:27:\"separator/theme-rtl.min.css\";i:430;s:19:\"separator/theme.css\";i:431;s:23:\"separator/theme.min.css\";i:432;s:24:\"shortcode/editor-rtl.css\";i:433;s:28:\"shortcode/editor-rtl.min.css\";i:434;s:20:\"shortcode/editor.css\";i:435;s:24:\"shortcode/editor.min.css\";i:436;s:24:\"site-logo/editor-rtl.css\";i:437;s:28:\"site-logo/editor-rtl.min.css\";i:438;s:20:\"site-logo/editor.css\";i:439;s:24:\"site-logo/editor.min.css\";i:440;s:23:\"site-logo/style-rtl.css\";i:441;s:27:\"site-logo/style-rtl.min.css\";i:442;s:19:\"site-logo/style.css\";i:443;s:23:\"site-logo/style.min.css\";i:444;s:27:\"site-tagline/editor-rtl.css\";i:445;s:31:\"site-tagline/editor-rtl.min.css\";i:446;s:23:\"site-tagline/editor.css\";i:447;s:27:\"site-tagline/editor.min.css\";i:448;s:26:\"site-tagline/style-rtl.css\";i:449;s:30:\"site-tagline/style-rtl.min.css\";i:450;s:22:\"site-tagline/style.css\";i:451;s:26:\"site-tagline/style.min.css\";i:452;s:25:\"site-title/editor-rtl.css\";i:453;s:29:\"site-title/editor-rtl.min.css\";i:454;s:21:\"site-title/editor.css\";i:455;s:25:\"site-title/editor.min.css\";i:456;s:24:\"site-title/style-rtl.css\";i:457;s:28:\"site-title/style-rtl.min.css\";i:458;s:20:\"site-title/style.css\";i:459;s:24:\"site-title/style.min.css\";i:460;s:26:\"social-link/editor-rtl.css\";i:461;s:30:\"social-link/editor-rtl.min.css\";i:462;s:22:\"social-link/editor.css\";i:463;s:26:\"social-link/editor.min.css\";i:464;s:27:\"social-links/editor-rtl.css\";i:465;s:31:\"social-links/editor-rtl.min.css\";i:466;s:23:\"social-links/editor.css\";i:467;s:27:\"social-links/editor.min.css\";i:468;s:26:\"social-links/style-rtl.css\";i:469;s:30:\"social-links/style-rtl.min.css\";i:470;s:22:\"social-links/style.css\";i:471;s:26:\"social-links/style.min.css\";i:472;s:21:\"spacer/editor-rtl.css\";i:473;s:25:\"spacer/editor-rtl.min.css\";i:474;s:17:\"spacer/editor.css\";i:475;s:21:\"spacer/editor.min.css\";i:476;s:20:\"spacer/style-rtl.css\";i:477;s:24:\"spacer/style-rtl.min.css\";i:478;s:16:\"spacer/style.css\";i:479;s:20:\"spacer/style.min.css\";i:480;s:20:\"table/editor-rtl.css\";i:481;s:24:\"table/editor-rtl.min.css\";i:482;s:16:\"table/editor.css\";i:483;s:20:\"table/editor.min.css\";i:484;s:19:\"table/style-rtl.css\";i:485;s:23:\"table/style-rtl.min.css\";i:486;s:15:\"table/style.css\";i:487;s:19:\"table/style.min.css\";i:488;s:19:\"table/theme-rtl.css\";i:489;s:23:\"table/theme-rtl.min.css\";i:490;s:15:\"table/theme.css\";i:491;s:19:\"table/theme.min.css\";i:492;s:24:\"tag-cloud/editor-rtl.css\";i:493;s:28:\"tag-cloud/editor-rtl.min.css\";i:494;s:20:\"tag-cloud/editor.css\";i:495;s:24:\"tag-cloud/editor.min.css\";i:496;s:23:\"tag-cloud/style-rtl.css\";i:497;s:27:\"tag-cloud/style-rtl.min.css\";i:498;s:19:\"tag-cloud/style.css\";i:499;s:23:\"tag-cloud/style.min.css\";i:500;s:28:\"template-part/editor-rtl.css\";i:501;s:32:\"template-part/editor-rtl.min.css\";i:502;s:24:\"template-part/editor.css\";i:503;s:28:\"template-part/editor.min.css\";i:504;s:27:\"template-part/theme-rtl.css\";i:505;s:31:\"template-part/theme-rtl.min.css\";i:506;s:23:\"template-part/theme.css\";i:507;s:27:\"template-part/theme.min.css\";i:508;s:30:\"term-description/style-rtl.css\";i:509;s:34:\"term-description/style-rtl.min.css\";i:510;s:26:\"term-description/style.css\";i:511;s:30:\"term-description/style.min.css\";i:512;s:27:\"text-columns/editor-rtl.css\";i:513;s:31:\"text-columns/editor-rtl.min.css\";i:514;s:23:\"text-columns/editor.css\";i:515;s:27:\"text-columns/editor.min.css\";i:516;s:26:\"text-columns/style-rtl.css\";i:517;s:30:\"text-columns/style-rtl.min.css\";i:518;s:22:\"text-columns/style.css\";i:519;s:26:\"text-columns/style.min.css\";i:520;s:19:\"verse/style-rtl.css\";i:521;s:23:\"verse/style-rtl.min.css\";i:522;s:15:\"verse/style.css\";i:523;s:19:\"verse/style.min.css\";i:524;s:20:\"video/editor-rtl.css\";i:525;s:24:\"video/editor-rtl.min.css\";i:526;s:16:\"video/editor.css\";i:527;s:20:\"video/editor.min.css\";i:528;s:19:\"video/style-rtl.css\";i:529;s:23:\"video/style-rtl.min.css\";i:530;s:15:\"video/style.css\";i:531;s:19:\"video/style.min.css\";i:532;s:19:\"video/theme-rtl.css\";i:533;s:23:\"video/theme-rtl.min.css\";i:534;s:15:\"video/theme.css\";i:535;s:19:\"video/theme.min.css\";}}','on');
INSERT INTO `wp_options` VALUES (125,'recovery_keys','a:0:{}','off');
INSERT INTO `wp_options` VALUES (126,'WPLANG','','auto');
INSERT INTO `wp_options` VALUES (151,'_site_transient_wp_plugin_dependencies_plugin_data','a:0:{}','off');
INSERT INTO `wp_options` VALUES (152,'recently_activated','a:4:{s:19:\"wpforms/wpforms.php\";i:1759881269;s:27:\"smartnav-wp/smartnav-wp.php\";i:1759700029;s:37:\"wp-menu-organize/wp-menu-organize.php\";i:1759700024;s:44:\"Submittal Builder/submittal-form-builder.php\";i:1759536462;}','off');
INSERT INTO `wp_options` VALUES (155,'finished_updating_comment_type','1','auto');
INSERT INTO `wp_options` VALUES (160,'theme_mods_twentytwentyfive','a:2:{s:18:\"custom_css_post_id\";i:-1;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1752105937;s:4:\"data\";a:3:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:3:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";}s:9:\"sidebar-2\";a:2:{i:0;s:7:\"block-5\";i:1;s:7:\"block-6\";}}}}','off');
INSERT INTO `wp_options` VALUES (161,'_transient_wp_styles_for_blocks','a:2:{s:4:\"hash\";s:32:\"1a7f8946efbf284a4c14585980ade540\";s:6:\"blocks\";a:5:{s:11:\"core/button\";s:0:\"\";s:14:\"core/site-logo\";s:0:\"\";s:18:\"core/post-template\";s:0:\"\";s:12:\"core/columns\";s:0:\"\";s:14:\"core/pullquote\";s:69:\":root :where(.wp-block-pullquote){font-size: 1.5em;line-height: 1.6;}\";}}','on');
INSERT INTO `wp_options` VALUES (214,'_transient_health-check-site-status-result','{\"good\":14,\"recommended\":6,\"critical\":0}','on');
INSERT INTO `wp_options` VALUES (231,'current_theme','Hello Elementor','auto');
INSERT INTO `wp_options` VALUES (232,'theme_mods_twentytwentythree','a:4:{i:0;b:0;s:19:\"wp_classic_sidebars\";a:0:{}s:18:\"nav_menu_locations\";a:0:{}s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1752105939;s:4:\"data\";a:1:{s:19:\"wp_inactive_widgets\";a:5:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";i:3;s:7:\"block-5\";i:4;s:7:\"block-6\";}}}}','off');
INSERT INTO `wp_options` VALUES (233,'theme_switched','','auto');
INSERT INTO `wp_options` VALUES (237,'theme_mods_twentytwentyfour','a:4:{i:0;b:0;s:19:\"wp_classic_sidebars\";a:0:{}s:18:\"nav_menu_locations\";a:0:{}s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1752105990;s:4:\"data\";a:1:{s:19:\"wp_inactive_widgets\";a:5:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";i:3;s:7:\"block-5\";i:4;s:7:\"block-6\";}}}}','off');
INSERT INTO `wp_options` VALUES (245,'theme_mods_hello-elementor','a:3:{i:0;b:0;s:18:\"nav_menu_locations\";a:0:{}s:18:\"custom_css_post_id\";i:-1;}','on');
INSERT INTO `wp_options` VALUES (246,'hello_theme_version','3.4.4','auto');
INSERT INTO `wp_options` VALUES (249,'elementor_connect_site_key','da4b84e2a3143c228a6d8ec510a6d4a8','auto');
INSERT INTO `wp_options` VALUES (250,'_hello-elementor_notifications','a:2:{s:7:\"timeout\";i:1759991144;s:5:\"value\";s:7532:\"[{\"id\":\"hello-theme-3.4.4\",\"title\":\"3.4.4 - 2025-06-08\",\"description\":\"\\n            <ul>\\n\\t\\t\\t\\t<li>Tweak: Improve Header\\/Footer edit access from theme Home<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.4.3\",\"title\":\"3.4.3 - 2025-05-26\",\"description\":\"\\n            <ul>\\n\\t\\t\\t\\t<li>Fix: Settings page empty after 3.4.0 in translated sites<\\/li>\\n\\t\\t\\t\\t<li>Fix: PHP 8.4 deprecation notice<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.4.2\",\"title\":\"3.4.2 - 2025-05-19\",\"description\":\"\\n            <ul>\\n\\t\\t\\t\\t<li>Tweak: Set Home links font weight to regular<\\/li>\\n  \\t\\t        <li>Tweak: Dart SASS 3.0.0 - resolve scss deprecated warnings<\\/li>\\n    \\t\\t    <li>Fix: Settings page empty after 3.4.0<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.4.0\",\"title\":\"3.4.0 - 2025-05-05\",\"description\":\"\\n            <ul>\\n                <li>New: Added Theme Home<\\/li>\\n\\t\\t\\t\\t<li>Tweak: Update theme settings page style<\\/li>\\n\\t\\t\\t\\t<li>Tweak: Update tested up to version 6.8<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.3.0\",\"title\":\"3.3.0 - 2025-01-21\",\"description\":\"\\n            <ul>\\n                <li>Tweak: Added changelog link in theme settings<\\/li>\\n\\t\\t\\t\\t<li>Tweak: Updated minimum required Safari version to 15.5<\\/li>\\n  \\t\\t        <li>Tweak: Update autoprefixer to latest versions<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.2.1\",\"title\":\"3.2.1 - 2024-12-16\",\"description\":\"\\n            <ul>\\n                <li>\\n                    Fix: Gutenberg editor expanded disproportionately after adding support for <code>theme.json<\\/code>\\n                    (<a href=\\\"https:\\/\\/github.com\\/elementor\\/hello-theme\\/issues\\/430\\\" target=\\\"_blank\\\">#430<\\/a>)\\n                <\\/li>\\n                <li>Fix: Use CSS logical properties in the theme<\\/li>\\n                <li>Fix: Add ARIA attributes to header nav menu<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.2.0\",\"title\":\"3.2.0 - 2024-12-15\",\"description\":\"\\n            <ul>\\n                <li>Tweak: Convert classic to hybrid theme with block-editor support<\\/li>\\n                <li>Tweak: Added new design options to header\\/footer<\\/li>\\n                <li>Tweak: Update <code>Tested up to 6.7<\\/code><\\/li>\\n                <li>\\n                    Fix: Minify JS files\\n                    (<a href=\\\"https:\\/\\/github.com\\/elementor\\/hello-theme\\/issues\\/419\\\" target=\\\"_blank\\\">#419<\\/a>)\\n                <\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.1.1\",\"title\":\"3.1.1 - 2024-07-30\",\"description\":\"\\n            <ul>\\n                <li>Fix: Use consistent <code>&lt;h2&gt;<\\/code> for comments title and comment form<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.1.0\",\"title\":\"3.1.0 - 2024-06-19\",\"description\":\"\\n            <ul>\\n                <li>Tweak: Update <code>Requires PHP 7.4<\\/code><\\/li>\\n                <li>Tweak: Update <code>Tested up to 6.5<\\/code><\\/li>\\n                <li>Tweak: Add the ability to style the brand layout<\\/li>\\n                <li>Tweak: Remove deprecated Elementor code<\\/li>\\n                <li>Tweak: Restore default focus styling inside the theme<\\/li>\\n                <li>Tweak: Add <code>aria-label<\\/code> attribute to various <code>&lt;nav&gt;<\\/code> elements<\\/li>\\n                <li>Tweak: Improve mobile menu keyboard accessibility<\\/li>\\n                <li>Tweak: Semantic mobile menu toggle button<\\/li>\\n                <li>Fix: The header renders redundant <code>&lt;p&gt;<\\/code> when tagline is empty<\\/li>\\n                <li>Fix: Single post renders redundant wrapping <code>&lt;div&gt;<\\/code> when it has no tags<\\/li>\\n                <li>Fix: Remove redundant wrapping <code>&lt;div&gt;<\\/code> from <code>wp_nav_menu()<\\/code> output<\\/li>\\n                <li>Fix: Wrap page <code>&lt;h1&gt;<\\/code> with <code>&lt;div&gt;<\\/code>, not <code>&lt;header&gt;<\\/code><\\/li>\\n                <li>Fix: Use consistent <code>&lt;h3&gt;<\\/code> for comments title and comment form<\\/li>\\n                <li>Fix: Remove heading tags from dynamic header\\/footer<\\/li>\\n                <li>\\n                    Fix: Mobile Menu hamburger is not visible for logged-out users in some cases\\n                    (<a href=\\\"https:\\/\\/github.com\\/elementor\\/hello-theme\\/issues\\/369\\\" target=\\\"_blank\\\">#369<\\/a>)\\n                <\\/li>\\n                <li>Fix: Remove duplicate ID attributes in the header mobile menu<\\/li>\\n                <li>\\n                    Fix: Remove redundant table styles\\n                    (<a href=\\\"https:\\/\\/github.com\\/elementor\\/hello-theme\\/issues\\/311\\\" target=\\\"_blank\\\">#311<\\/a>)\\n                <\\/li>\\n                <li>Fix: Remove redundant space below Site Logo in the header\\/footer<\\/li>\\n                <li>Fix: Remove redundant CSS from dynamic header\\/footer layout<\\/li>\\n                <li>\\n                    Fix: Separate post tags in single post\\n                    (<a href=\\\"https:\\/\\/github.com\\/elementor\\/hello-theme\\/issues\\/304\\\" target=\\\"_blank\\\">#304<\\/a>)\\n                <\\/li>\\n                <li>Fix: Display <code>the_tags()<\\/code> after <code>wp_link_pages()<\\/code><\\/li>\\n                <li>Fix: Remove page break navigation from archives when using <code>&lt;!--nextpage--&gt;<\\/code><\\/li>\\n                <li>Fix: Style posts pagination component layout<\\/li>\\n                <li>Fix: Add RTL support to pagination arrows in archive pages<\\/li>\\n                <li>\\n                    Fix: Update pagination prev\\/next labels and positions\\n                    (<a href=\\\"https:\\/\\/github.com\\/elementor\\/hello-theme\\/issues\\/404\\\" target=\\\"_blank\\\">#404<\\/a>)\\n                <\\/li>\\n                <li>Fix: Check if Elementor is loaded when using dynamic header & footer<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.0.2\",\"title\":\"3.0.2 - 2024-05-28\",\"description\":\"\\n            <ul>\\n                <li>Internal: Version bump release to refresh WordPress repository<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.0.1\",\"title\":\"3.0.1 - 2024-01-24\",\"description\":\"\\n            <ul>\\n                <li>Fix: Harden security for admin notice dismiss button<\\/li>\\n                <li>Fix: Add <code>alt<\\/code> attribute to all the images in the dashboard<\\/li>\\n            <\\/ul>\"},{\"id\":\"hello-theme-3.0.0\",\"title\":\"3.0.0 - 2023-12-26\",\"description\":\"\\n            <ul>\\n                <li>New: Option to disable cross-site header & footer<\\/li>\\n                <li>Tweak: Update <code>Requires PHP 7.3<\\/code><\\/li>\\n                <li>Tweak: Update <code>Tested up to 6.4<\\/code><\\/li>\\n                <li>Tweak: Move cross-site header & footer styles to a separate CSS file<\\/li>\\n                <li>Tweak: Don\'t load <code>header-footer.min.css<\\/code> when disabling header & footer<\\/li>\\n                <li>Tweak: Don\'t load <code>hello-frontend.min.js<\\/code> when disabling header & footer<\\/li>\\n                <li>Tweak: Replace jQuery code with vanilla JS in the frontend<\\/li>\\n                <li>Tweak: Replace jQuery code with vanilla JS in WordPress admin<\\/li>\\n                <li>Tweak: Remove unused JS code from the frontend<\\/li>\\n                <li>Tweak: Remove unused CSS code from the editor<\\/li>\\n                <li>Tweak: Remove unnecessary <code>role<\\/code> attributes from HTML landmark elements<\\/li>\\n                <li>Tweak: Link from Elementor Site Settings to Hello Theme Settings<\\/li>\\n                <li>Fix: Dynamic script version for better caching<\\/li>\\n            <\\/ul>\"}]\";}','off');
INSERT INTO `wp_options` VALUES (251,'nav_menu_options','a:2:{i:0;b:0;s:8:\"auto_add\";a:0:{}}','off');
INSERT INTO `wp_options` VALUES (330,'wp_calendar_block_has_published_posts','1','auto');
INSERT INTO `wp_options` VALUES (332,'category_children','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (373,'wmo_test_menu_items','a:49:{i:0;a:5:{s:2:\"id\";i:1000;s:5:\"title\";s:9:\"Dashboard\";s:3:\"url\";s:13:\"dashboard.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:1;}i:1;a:5:{s:2:\"id\";i:1001;s:5:\"title\";s:5:\"Posts\";s:3:\"url\";s:9:\"posts.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:2;}i:2;a:5:{s:2:\"id\";i:1002;s:5:\"title\";s:5:\"Media\";s:3:\"url\";s:9:\"media.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:3;}i:3;a:5:{s:2:\"id\";i:1003;s:5:\"title\";s:5:\"Pages\";s:3:\"url\";s:9:\"pages.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:4;}i:4;a:5:{s:2:\"id\";i:1004;s:5:\"title\";s:8:\"Comments\";s:3:\"url\";s:12:\"comments.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:5;}i:5;a:5:{s:2:\"id\";i:1005;s:5:\"title\";s:10:\"Appearance\";s:3:\"url\";s:14:\"appearance.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:6;}i:6;a:5:{s:2:\"id\";i:1006;s:5:\"title\";s:7:\"Plugins\";s:3:\"url\";s:11:\"plugins.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:7;}i:7;a:5:{s:2:\"id\";i:1007;s:5:\"title\";s:5:\"Users\";s:3:\"url\";s:9:\"users.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:8;}i:8;a:5:{s:2:\"id\";i:1008;s:5:\"title\";s:5:\"Tools\";s:3:\"url\";s:9:\"tools.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:9;}i:9;a:5:{s:2:\"id\";i:1009;s:5:\"title\";s:8:\"Settings\";s:3:\"url\";s:12:\"settings.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:10;}i:10;a:5:{s:2:\"id\";i:1010;s:5:\"title\";s:9:\"Analytics\";s:3:\"url\";s:13:\"analytics.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:11;}i:11;a:5:{s:2:\"id\";i:1011;s:5:\"title\";s:7:\"Reports\";s:3:\"url\";s:11:\"reports.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:12;}i:12;a:5:{s:2:\"id\";i:1012;s:5:\"title\";s:9:\"Marketing\";s:3:\"url\";s:13:\"marketing.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:13;}i:13;a:5:{s:2:\"id\";i:1013;s:5:\"title\";s:3:\"SEO\";s:3:\"url\";s:7:\"seo.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:14;}i:14;a:5:{s:2:\"id\";i:1014;s:5:\"title\";s:8:\"Security\";s:3:\"url\";s:12:\"security.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:15;}i:15;a:5:{s:2:\"id\";i:1015;s:5:\"title\";s:6:\"Backup\";s:3:\"url\";s:10:\"backup.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:16;}i:16;a:5:{s:2:\"id\";i:1016;s:5:\"title\";s:11:\"Performance\";s:3:\"url\";s:15:\"performance.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:17;}i:17;a:5:{s:2:\"id\";i:1017;s:5:\"title\";s:10:\"Monitoring\";s:3:\"url\";s:14:\"monitoring.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:18;}i:18;a:5:{s:2:\"id\";i:1018;s:5:\"title\";s:7:\"Support\";s:3:\"url\";s:11:\"support.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:19;}i:19;a:5:{s:2:\"id\";i:1019;s:5:\"title\";s:4:\"Help\";s:3:\"url\";s:8:\"help.php\";s:16:\"menu_item_parent\";i:0;s:10:\"menu_order\";i:20;}i:20;a:5:{s:2:\"id\";i:1020;s:5:\"title\";s:9:\"All Posts\";s:3:\"url\";s:13:\"all-posts.php\";s:16:\"menu_item_parent\";i:1001;s:10:\"menu_order\";i:1;}i:21;a:5:{s:2:\"id\";i:1021;s:5:\"title\";s:7:\"Add New\";s:3:\"url\";s:11:\"add-new.php\";s:16:\"menu_item_parent\";i:1001;s:10:\"menu_order\";i:2;}i:22;a:5:{s:2:\"id\";i:1022;s:5:\"title\";s:10:\"Categories\";s:3:\"url\";s:14:\"categories.php\";s:16:\"menu_item_parent\";i:1001;s:10:\"menu_order\";i:3;}i:23;a:5:{s:2:\"id\";i:1023;s:5:\"title\";s:4:\"Tags\";s:3:\"url\";s:8:\"tags.php\";s:16:\"menu_item_parent\";i:1001;s:10:\"menu_order\";i:4;}i:24;a:5:{s:2:\"id\";i:1024;s:5:\"title\";s:7:\"Library\";s:3:\"url\";s:11:\"library.php\";s:16:\"menu_item_parent\";i:1002;s:10:\"menu_order\";i:1;}i:25;a:5:{s:2:\"id\";i:1025;s:5:\"title\";s:7:\"Add New\";s:3:\"url\";s:11:\"add-new.php\";s:16:\"menu_item_parent\";i:1002;s:10:\"menu_order\";i:2;}i:26;a:5:{s:2:\"id\";i:1026;s:5:\"title\";s:7:\"Folders\";s:3:\"url\";s:11:\"folders.php\";s:16:\"menu_item_parent\";i:1002;s:10:\"menu_order\";i:3;}i:27;a:5:{s:2:\"id\";i:1027;s:5:\"title\";s:9:\"All Pages\";s:3:\"url\";s:13:\"all-pages.php\";s:16:\"menu_item_parent\";i:1003;s:10:\"menu_order\";i:1;}i:28;a:5:{s:2:\"id\";i:1028;s:5:\"title\";s:7:\"Add New\";s:3:\"url\";s:11:\"add-new.php\";s:16:\"menu_item_parent\";i:1003;s:10:\"menu_order\";i:2;}i:29;a:5:{s:2:\"id\";i:1029;s:5:\"title\";s:9:\"Templates\";s:3:\"url\";s:13:\"templates.php\";s:16:\"menu_item_parent\";i:1003;s:10:\"menu_order\";i:3;}i:30;a:5:{s:2:\"id\";i:1030;s:5:\"title\";s:6:\"Themes\";s:3:\"url\";s:10:\"themes.php\";s:16:\"menu_item_parent\";i:1005;s:10:\"menu_order\";i:1;}i:31;a:5:{s:2:\"id\";i:1031;s:5:\"title\";s:9:\"Customize\";s:3:\"url\";s:13:\"customize.php\";s:16:\"menu_item_parent\";i:1005;s:10:\"menu_order\";i:2;}i:32;a:5:{s:2:\"id\";i:1032;s:5:\"title\";s:7:\"Widgets\";s:3:\"url\";s:11:\"widgets.php\";s:16:\"menu_item_parent\";i:1005;s:10:\"menu_order\";i:3;}i:33;a:5:{s:2:\"id\";i:1033;s:5:\"title\";s:5:\"Menus\";s:3:\"url\";s:9:\"menus.php\";s:16:\"menu_item_parent\";i:1005;s:10:\"menu_order\";i:4;}i:34;a:5:{s:2:\"id\";i:1034;s:5:\"title\";s:9:\"Installed\";s:3:\"url\";s:13:\"installed.php\";s:16:\"menu_item_parent\";i:1006;s:10:\"menu_order\";i:1;}i:35;a:5:{s:2:\"id\";i:1035;s:5:\"title\";s:7:\"Add New\";s:3:\"url\";s:11:\"add-new.php\";s:16:\"menu_item_parent\";i:1006;s:10:\"menu_order\";i:2;}i:36;a:5:{s:2:\"id\";i:1036;s:5:\"title\";s:6:\"Editor\";s:3:\"url\";s:10:\"editor.php\";s:16:\"menu_item_parent\";i:1006;s:10:\"menu_order\";i:3;}i:37;a:5:{s:2:\"id\";i:1037;s:5:\"title\";s:9:\"All Users\";s:3:\"url\";s:13:\"all-users.php\";s:16:\"menu_item_parent\";i:1007;s:10:\"menu_order\";i:1;}i:38;a:5:{s:2:\"id\";i:1038;s:5:\"title\";s:7:\"Add New\";s:3:\"url\";s:11:\"add-new.php\";s:16:\"menu_item_parent\";i:1007;s:10:\"menu_order\";i:2;}i:39;a:5:{s:2:\"id\";i:1039;s:5:\"title\";s:12:\"Your Profile\";s:3:\"url\";s:16:\"your-profile.php\";s:16:\"menu_item_parent\";i:1007;s:10:\"menu_order\";i:3;}i:40;a:5:{s:2:\"id\";i:1040;s:5:\"title\";s:15:\"Available Tools\";s:3:\"url\";s:19:\"available-tools.php\";s:16:\"menu_item_parent\";i:1008;s:10:\"menu_order\";i:1;}i:41;a:5:{s:2:\"id\";i:1041;s:5:\"title\";s:6:\"Import\";s:3:\"url\";s:10:\"import.php\";s:16:\"menu_item_parent\";i:1008;s:10:\"menu_order\";i:2;}i:42;a:5:{s:2:\"id\";i:1042;s:5:\"title\";s:6:\"Export\";s:3:\"url\";s:10:\"export.php\";s:16:\"menu_item_parent\";i:1008;s:10:\"menu_order\";i:3;}i:43;a:5:{s:2:\"id\";i:1043;s:5:\"title\";s:7:\"General\";s:3:\"url\";s:11:\"general.php\";s:16:\"menu_item_parent\";i:1009;s:10:\"menu_order\";i:1;}i:44;a:5:{s:2:\"id\";i:1044;s:5:\"title\";s:7:\"Writing\";s:3:\"url\";s:11:\"writing.php\";s:16:\"menu_item_parent\";i:1009;s:10:\"menu_order\";i:2;}i:45;a:5:{s:2:\"id\";i:1045;s:5:\"title\";s:7:\"Reading\";s:3:\"url\";s:11:\"reading.php\";s:16:\"menu_item_parent\";i:1009;s:10:\"menu_order\";i:3;}i:46;a:5:{s:2:\"id\";i:1046;s:5:\"title\";s:10:\"Discussion\";s:3:\"url\";s:14:\"discussion.php\";s:16:\"menu_item_parent\";i:1009;s:10:\"menu_order\";i:4;}i:47;a:5:{s:2:\"id\";i:1047;s:5:\"title\";s:5:\"Media\";s:3:\"url\";s:9:\"media.php\";s:16:\"menu_item_parent\";i:1009;s:10:\"menu_order\";i:5;}i:48;a:5:{s:2:\"id\";i:1048;s:5:\"title\";s:10:\"Permalinks\";s:3:\"url\";s:14:\"permalinks.php\";s:16:\"menu_item_parent\";i:1009;s:10:\"menu_order\";i:6;}}','auto');
INSERT INTO `wp_options` VALUES (392,'recovery_mode_email_last_sent','1759804449','auto');
INSERT INTO `wp_options` VALUES (436,'db_upgraded','','on');
INSERT INTO `wp_options` VALUES (460,'can_compress_scripts','0','on');
INSERT INTO `wp_options` VALUES (520,'siteground_optimizer_default_enable_cache','0','off');
INSERT INTO `wp_options` VALUES (521,'siteground_optimizer_default_autoflush_cache','0','off');
INSERT INTO `wp_options` VALUES (522,'siteground_optimizer_supercacher_permissions','1','off');
INSERT INTO `wp_options` VALUES (523,'sg_cachepress','a:8:{s:12:\"enable_cache\";i:0;s:15:\"autoflush_cache\";i:0;s:16:\"enable_memcached\";i:0;s:11:\"show_notice\";i:0;s:8:\"is_nginx\";i:0;s:13:\"checked_nginx\";i:0;s:9:\"first_run\";i:0;s:9:\"last_fail\";i:0;}','auto');
INSERT INTO `wp_options` VALUES (524,'siteground_optimizer_enable_cache','0','auto');
INSERT INTO `wp_options` VALUES (525,'siteground_optimizer_autoflush_cache','0','auto');
INSERT INTO `wp_options` VALUES (526,'siteground_optimizer_enable_memcached','0','auto');
INSERT INTO `wp_options` VALUES (527,'siteground_optimizer_show_notice','0','auto');
INSERT INTO `wp_options` VALUES (528,'siteground_optimizer_is_nginx','0','auto');
INSERT INTO `wp_options` VALUES (529,'siteground_optimizer_checked_nginx','0','auto');
INSERT INTO `wp_options` VALUES (530,'siteground_optimizer_first_run','0','auto');
INSERT INTO `wp_options` VALUES (531,'siteground_optimizer_last_fail','0','auto');
INSERT INTO `wp_options` VALUES (532,'siteground_optimizer_ssl_enabled','0','auto');
INSERT INTO `wp_options` VALUES (533,'siteground_optimizer_optimize_html','0','auto');
INSERT INTO `wp_options` VALUES (534,'siteground_optimizer_optimize_javascript','0','auto');
INSERT INTO `wp_options` VALUES (535,'siteground_optimizer_optimize_javascript_async','0','auto');
INSERT INTO `wp_options` VALUES (536,'siteground_optimizer_optimize_css','0','auto');
INSERT INTO `wp_options` VALUES (537,'siteground_optimizer_combine_css','0','auto');
INSERT INTO `wp_options` VALUES (538,'siteground_optimizer_remove_query_strings','0','auto');
INSERT INTO `wp_options` VALUES (539,'siteground_optimizer_disable_emojis','0','auto');
INSERT INTO `wp_options` VALUES (541,'siteground_optimizer_version','7.7.2','auto');
INSERT INTO `wp_options` VALUES (542,'siteground_optimizer_update_timestamp','1755806222','auto');
INSERT INTO `wp_options` VALUES (543,'siteground_optimizer_phpcompat_status','1','auto');
INSERT INTO `wp_options` VALUES (544,'siteground_optimizer_phpcompat_progress','0','auto');
INSERT INTO `wp_options` VALUES (545,'siteground_optimizer_phpcompat_is_compatible','0','auto');
INSERT INTO `wp_options` VALUES (546,'siteground_optimizer_phpcompat_result','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (547,'siteground_optimizer_image_optimization_completed','1','off');
INSERT INTO `wp_options` VALUES (548,'siteground_optimizer_enable_gzip_compression','1','auto');
INSERT INTO `wp_options` VALUES (549,'siteground_optimizer_enable_browser_caching','1','auto');
INSERT INTO `wp_options` VALUES (551,'siteground_optimizer_async_javascript_exclude','a:3:{i:0;s:11:\"jquery-core\";i:1;s:14:\"jquery-migrate\";i:2;s:6:\"jquery\";}','auto');
INSERT INTO `wp_options` VALUES (552,'siteground_optimizer_excluded_lazy_load_classes','a:1:{i:0;s:9:\"skip-lazy\";}','auto');
INSERT INTO `wp_options` VALUES (555,'siteground_optimizer_whats_new','a:1:{i:0;a:7:{s:4:\"type\";s:7:\"default\";s:5:\"title\";s:22:\"Web Fonts Optimization\";s:4:\"text\";s:271:\"With this optimization we are changing the default way to load Google fonts in order to save HTTP requests. In addition to that, all other fonts that your website uses will be properly preloaded so browsers take the least possible amount of time to cache and render them.\";s:4:\"icon\";s:33:\"presentational-fonts-optimization\";s:10:\"icon_color\";s:6:\"salmon\";s:12:\"optimization\";s:18:\"optimize_web_fonts\";s:6:\"button\";a:3:{s:4:\"text\";s:10:\"Enable Now\";s:5:\"color\";s:7:\"primary\";s:4:\"link\";s:8:\"frontend\";}}}','auto');
INSERT INTO `wp_options` VALUES (556,'siteground_optimizer_quality_webp','85','auto');
INSERT INTO `wp_options` VALUES (557,'siteground_optimizer_quality_type','lossy','auto');
INSERT INTO `wp_options` VALUES (558,'siteground_optimizer_heartbeat_post_interval','120','auto');
INSERT INTO `wp_options` VALUES (559,'siteground_optimizer_heartbeat_dashboard_interval','0','auto');
INSERT INTO `wp_options` VALUES (560,'siteground_optimizer_heartbeat_frontend_interval','0','auto');
INSERT INTO `wp_options` VALUES (561,'siteground_optimizer_excluded_lazy_load_media_types','a:6:{i:0;s:18:\"lazyload_gravatars\";i:1;s:19:\"lazyload_thumbnails\";i:2;s:19:\"lazyload_responsive\";i:3;s:20:\"lazyload_textwidgets\";i:4;s:19:\"lazyload_shortcodes\";i:5;s:20:\"lazyload_woocommerce\";}','auto');
INSERT INTO `wp_options` VALUES (562,'siteground_settings_optimizer_hello','1','auto');
INSERT INTO `wp_options` VALUES (563,'siteground_optimizer_database_optimization','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (564,'siteground_optimizer_performace_receipient','a:1:{i:0;s:24:\"dev-email@wpengine.local\";}','auto');
INSERT INTO `wp_options` VALUES (565,'sgo_install_7_4_0','1','auto');
INSERT INTO `wp_options` VALUES (566,'siteground_optimizer_current_version','7.7.2','auto');
INSERT INTO `wp_options` VALUES (630,'wp_menu_organize_colors','a:1:{s:9:\"dashboard\";s:7:\"#81d742\";}','auto');
INSERT INTO `wp_options` VALUES (839,'wmo_menu_backgrounds','a:2:{s:9:\"dashboard\";a:2:{s:6:\"normal\";s:7:\"#ffffff\";s:5:\"hover\";s:7:\"#eeee22\";}s:4:\"home\";a:2:{s:6:\"normal\";s:7:\"#2bbc46\";s:5:\"hover\";s:7:\"#dbb600\";}}','auto');
INSERT INTO `wp_options` VALUES (845,'wmo_menu_settings','a:2:{s:9:\"dashboard\";a:2:{s:16:\"background_color\";s:7:\"#ffffff\";s:10:\"menu_color\";s:7:\"#000000\";}s:4:\"home\";a:1:{s:16:\"background_color\";s:7:\"#2bbc46\";}}','auto');
INSERT INTO `wp_options` VALUES (924,'wmo_migrated_v2','1','auto');
INSERT INTO `wp_options` VALUES (925,'wmo_version','2.0.0','auto');
INSERT INTO `wp_options` VALUES (1277,'wmo_background_colors_migrated','1','auto');
INSERT INTO `wp_options` VALUES (1324,'wmo_settings','a:5:{s:10:\"typography\";a:4:{s:7:\"library\";a:1:{s:7:\"enabled\";b:1;}s:13:\"menu-organize\";a:2:{s:7:\"enabled\";b:1;s:11:\"font_family\";s:22:\"Comic Sans MS, cursive\";}s:14:\"customize-tabs\";a:1:{s:7:\"enabled\";b:1;}s:9:\"all-pages\";a:1:{s:7:\"enabled\";b:1;}}s:5:\"icons\";a:2:{s:10:\"appearance\";a:2:{s:4:\"type\";s:5:\"emoji\";s:5:\"value\";s:4:\"📁\";}s:15:\"submittal-items\";a:2:{s:4:\"type\";s:0:\"\";s:5:\"value\";s:0:\"\";}}s:10:\"menu_order\";a:14:{i:0;s:25:\"wp-menu-organize-settings\";i:1;s:15:\"hello-elementor\";i:2;s:19:\"options-general.php\";i:3;s:23:\"edit.php?post_type=page\";i:4;s:10:\"themes.php\";i:5;s:9:\"tools.php\";i:6;s:8:\"edit.php\";i:7;s:9:\"index.php\";i:8;s:17:\"edit-comments.php\";i:9;s:11:\"plugins.php\";i:10;s:10:\"upload.php\";i:11;s:9:\"users.php\";i:12;s:11:\"smartnav-wp\";i:13;s:13:\"sg-cachepress\";}s:16:\"theme_preference\";s:5:\"light\";s:6:\"badges\";a:0:{}}','auto');
INSERT INTO `wp_options` VALUES (1385,'wmo_color_cleanup_done','1','auto');
INSERT INTO `wp_options` VALUES (1386,'wmo_menu_background_colors','a:3:{s:13:\"menu-organize\";s:7:\"#7c3aed\";s:5:\"pages\";s:7:\"#dc2626\";s:15:\"submittal-items\";s:7:\"#059669\";}','auto');
INSERT INTO `wp_options` VALUES (1396,'_site_transient_timeout_community-events-d41d8cd98f00b204e9800998ecf8427e','1759999774','off');
INSERT INTO `wp_options` VALUES (1397,'_site_transient_community-events-d41d8cd98f00b204e9800998ecf8427e','a:4:{s:9:\"sandboxed\";b:0;s:5:\"error\";N;s:8:\"location\";a:1:{s:2:\"ip\";b:0;}s:6:\"events\";a:5:{i:0;a:10:{s:4:\"type\";s:6:\"meetup\";s:5:\"title\";s:40:\"WordPress + AI: What’s Possible Today?\";s:3:\"url\";s:69:\"https://www.meetup.com/spartanburg-wordpress-meetup/events/310109912/\";s:6:\"meetup\";s:31:\"Spartanburg SC WordPress Meetup\";s:10:\"meetup_url\";s:52:\"https://www.meetup.com/spartanburg-wordpress-meetup/\";s:4:\"date\";s:19:\"2025-10-07 23:00:00\";s:8:\"end_date\";s:19:\"2025-10-08 00:30:00\";s:20:\"start_unix_timestamp\";i:1759878000;s:18:\"end_unix_timestamp\";i:1759883400;s:8:\"location\";a:4:{s:8:\"location\";s:20:\"Spartanburg, SC, USA\";s:7:\"country\";s:2:\"us\";s:8:\"latitude\";d:34.959601999999997;s:9:\"longitude\";d:-81.940899999999999;}}i:1;a:10:{s:4:\"type\";s:6:\"meetup\";s:5:\"title\";s:42:\"Ideas for Using n8n and WordPress Together\";s:3:\"url\";s:82:\"https://www.meetup.com/greenville-south-carolina-wordpress-group/events/310123828/\";s:6:\"meetup\";s:29:\"Greenville SC WordPress Group\";s:10:\"meetup_url\";s:65:\"https://www.meetup.com/greenville-south-carolina-wordpress-group/\";s:4:\"date\";s:19:\"2025-10-16 15:30:00\";s:8:\"end_date\";s:19:\"2025-10-16 17:00:00\";s:20:\"start_unix_timestamp\";i:1760628600;s:18:\"end_unix_timestamp\";i:1760634000;s:8:\"location\";a:4:{s:8:\"location\";s:19:\"Greenville, SC, USA\";s:7:\"country\";s:2:\"us\";s:8:\"latitude\";d:34.825287000000003;s:9:\"longitude\";d:-82.395790000000005;}}i:2;a:10:{s:4:\"type\";s:6:\"meetup\";s:5:\"title\";s:41:\"What Can Chatbots Now Do on Your Website?\";s:3:\"url\";s:69:\"https://www.meetup.com/spartanburg-wordpress-meetup/events/310109915/\";s:6:\"meetup\";s:31:\"Spartanburg SC WordPress Meetup\";s:10:\"meetup_url\";s:52:\"https://www.meetup.com/spartanburg-wordpress-meetup/\";s:4:\"date\";s:19:\"2025-11-05 00:00:00\";s:8:\"end_date\";s:19:\"2025-11-05 01:30:00\";s:20:\"start_unix_timestamp\";i:1762300800;s:18:\"end_unix_timestamp\";i:1762306200;s:8:\"location\";a:4:{s:8:\"location\";s:20:\"Spartanburg, SC, USA\";s:7:\"country\";s:2:\"us\";s:8:\"latitude\";d:34.959601999999997;s:9:\"longitude\";d:-81.940899999999999;}}i:3;a:10:{s:4:\"type\";s:6:\"meetup\";s:5:\"title\";s:35:\"Voice AI Receptionists for Websites\";s:3:\"url\";s:82:\"https://www.meetup.com/greenville-south-carolina-wordpress-group/events/310124025/\";s:6:\"meetup\";s:29:\"Greenville SC WordPress Group\";s:10:\"meetup_url\";s:65:\"https://www.meetup.com/greenville-south-carolina-wordpress-group/\";s:4:\"date\";s:19:\"2025-11-13 16:30:00\";s:8:\"end_date\";s:19:\"2025-11-13 18:00:00\";s:20:\"start_unix_timestamp\";i:1763051400;s:18:\"end_unix_timestamp\";i:1763056800;s:8:\"location\";a:4:{s:8:\"location\";s:19:\"Greenville, SC, USA\";s:7:\"country\";s:2:\"us\";s:8:\"latitude\";d:34.825287000000003;s:9:\"longitude\";d:-82.395790000000005;}}i:4;a:10:{s:4:\"type\";s:6:\"meetup\";s:5:\"title\";s:43:\"Future-Proof Your Business: Trends for 2026\";s:3:\"url\";s:69:\"https://www.meetup.com/spartanburg-wordpress-meetup/events/310109919/\";s:6:\"meetup\";s:31:\"Spartanburg SC WordPress Meetup\";s:10:\"meetup_url\";s:52:\"https://www.meetup.com/spartanburg-wordpress-meetup/\";s:4:\"date\";s:19:\"2025-12-03 00:00:00\";s:8:\"end_date\";s:19:\"2025-12-03 01:30:00\";s:20:\"start_unix_timestamp\";i:1764720000;s:18:\"end_unix_timestamp\";i:1764725400;s:8:\"location\";a:4:{s:8:\"location\";s:20:\"Spartanburg, SC, USA\";s:7:\"country\";s:2:\"us\";s:8:\"latitude\";d:34.959601999999997;s:9:\"longitude\";d:-81.940899999999999;}}}}','off');
INSERT INTO `wp_options` VALUES (1431,'_site_transient_timeout_browser_a3f57bbe21c4e30379228ad7788f224d','1760050117','off');
INSERT INTO `wp_options` VALUES (1432,'_site_transient_browser_a3f57bbe21c4e30379228ad7788f224d','a:10:{s:4:\"name\";s:6:\"Chrome\";s:7:\"version\";s:9:\"140.0.0.0\";s:8:\"platform\";s:7:\"Windows\";s:10:\"update_url\";s:29:\"https://www.google.com/chrome\";s:7:\"img_src\";s:43:\"http://s.w.org/images/browsers/chrome.png?1\";s:11:\"img_src_ssl\";s:44:\"https://s.w.org/images/browsers/chrome.png?1\";s:15:\"current_version\";s:2:\"18\";s:7:\"upgrade\";b:0;s:8:\"insecure\";b:0;s:6:\"mobile\";b:0;}','off');
INSERT INTO `wp_options` VALUES (1433,'_site_transient_timeout_php_check_617fc4d260191bf0de418d0d961f5a43','1760050118','off');
INSERT INTO `wp_options` VALUES (1434,'_site_transient_php_check_617fc4d260191bf0de418d0d961f5a43','a:5:{s:19:\"recommended_version\";s:3:\"8.3\";s:15:\"minimum_version\";s:6:\"7.2.24\";s:12:\"is_supported\";b:0;s:9:\"is_secure\";b:1;s:13:\"is_acceptable\";b:1;}','off');
INSERT INTO `wp_options` VALUES (1437,'_site_transient_update_core','O:8:\"stdClass\":4:{s:7:\"updates\";a:1:{i:0;O:8:\"stdClass\":10:{s:8:\"response\";s:6:\"latest\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-6.8.3.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-6.8.3.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-6.8.3-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-6.8.3-new-bundled.zip\";s:7:\"partial\";s:0:\"\";s:8:\"rollback\";s:0:\"\";}s:7:\"current\";s:5:\"6.8.3\";s:7:\"version\";s:5:\"6.8.3\";s:11:\"php_version\";s:6:\"7.2.24\";s:13:\"mysql_version\";s:5:\"5.5.5\";s:11:\"new_bundled\";s:3:\"6.7\";s:15:\"partial_version\";s:0:\"\";}}s:12:\"last_checked\";i:1759956571;s:15:\"version_checked\";s:5:\"6.8.3\";s:12:\"translations\";a:0:{}}','off');
INSERT INTO `wp_options` VALUES (1504,'_site_transient_update_themes','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1759947943;s:7:\"checked\";a:3:{s:15:\"hello-elementor\";s:5:\"3.4.4\";s:16:\"twentytwentyfour\";s:3:\"1.3\";s:17:\"twentytwentythree\";s:3:\"1.6\";}s:8:\"response\";a:0:{}s:9:\"no_update\";a:3:{s:15:\"hello-elementor\";a:6:{s:5:\"theme\";s:15:\"hello-elementor\";s:11:\"new_version\";s:5:\"3.4.4\";s:3:\"url\";s:45:\"https://wordpress.org/themes/hello-elementor/\";s:7:\"package\";s:63:\"https://downloads.wordpress.org/theme/hello-elementor.3.4.4.zip\";s:8:\"requires\";s:3:\"6.0\";s:12:\"requires_php\";s:3:\"7.4\";}s:16:\"twentytwentyfour\";a:6:{s:5:\"theme\";s:16:\"twentytwentyfour\";s:11:\"new_version\";s:3:\"1.3\";s:3:\"url\";s:46:\"https://wordpress.org/themes/twentytwentyfour/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/theme/twentytwentyfour.1.3.zip\";s:8:\"requires\";s:3:\"6.4\";s:12:\"requires_php\";s:3:\"7.0\";}s:17:\"twentytwentythree\";a:6:{s:5:\"theme\";s:17:\"twentytwentythree\";s:11:\"new_version\";s:3:\"1.6\";s:3:\"url\";s:47:\"https://wordpress.org/themes/twentytwentythree/\";s:7:\"package\";s:63:\"https://downloads.wordpress.org/theme/twentytwentythree.1.6.zip\";s:8:\"requires\";s:3:\"6.1\";s:12:\"requires_php\";s:3:\"5.6\";}}s:12:\"translations\";a:0:{}}','off');
INSERT INTO `wp_options` VALUES (1510,'siteground_optimizer_total_non_converted_images','1','auto');
INSERT INTO `wp_options` VALUES (1511,'sfb_branding','a:7:{s:12:\"company_name\";s:19:\"Acme Framing Supply\";s:13:\"address_lines\";a:3:{i:0;s:13:\"123 Steel Ave\";i:1;s:9:\"Suite 400\";i:2;s:20:\"Metropolis, NY 10001\";}s:5:\"phone\";s:14:\"(212) 555-0199\";s:5:\"email\";s:25:\"sales@acmeframing.example\";s:8:\"logo_url\";s:61:\"https://dummyimage.com/300x80/111827/ffffff&text=ACME+FRAMING\";s:13:\"primary_color\";s:7:\"#0ea5e9\";s:11:\"footer_note\";s:50:\"Submittal packet auto-generated for demonstration.\";}','off');
INSERT INTO `wp_options` VALUES (1601,'sfb_onboarding_completed','1','off');
INSERT INTO `wp_options` VALUES (1704,'_site_transient_update_plugins','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1759947943;s:8:\"response\";a:0:{}s:12:\"translations\";a:0:{}s:9:\"no_update\";a:1:{s:31:\"sg-cachepress/sg-cachepress.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:27:\"w.org/plugins/sg-cachepress\";s:4:\"slug\";s:13:\"sg-cachepress\";s:6:\"plugin\";s:31:\"sg-cachepress/sg-cachepress.php\";s:11:\"new_version\";s:5:\"7.7.2\";s:3:\"url\";s:44:\"https://wordpress.org/plugins/sg-cachepress/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/plugin/sg-cachepress.7.7.2.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:66:\"https://ps.w.org/sg-cachepress/assets/icon-256x256.gif?rev=2971889\";s:2:\"1x\";s:66:\"https://ps.w.org/sg-cachepress/assets/icon-128x128.gif?rev=2971889\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:69:\"https://ps.w.org/sg-cachepress/assets/banner-1544x500.png?rev=2971889\";s:2:\"1x\";s:68:\"https://ps.w.org/sg-cachepress/assets/banner-772x250.png?rev=2971889\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"4.7\";}}s:7:\"checked\";a:4:{s:27:\"smartnav-wp/smartnav-wp.php\";s:5:\"1.0.0\";s:31:\"sg-cachepress/sg-cachepress.php\";s:5:\"7.7.2\";s:57:\"Submittal & Spec Sheet Builder/submittal-form-builder.php\";s:5:\"1.0.2\";s:37:\"wp-menu-organize/wp-menu-organize.php\";s:5:\"3.1.0\";}}','off');
INSERT INTO `wp_options` VALUES (1705,'action_scheduler_hybrid_store_demarkation','139','auto');
INSERT INTO `wp_options` VALUES (1706,'schema-ActionScheduler_StoreSchema','6.0.1759881251','auto');
INSERT INTO `wp_options` VALUES (1707,'schema-ActionScheduler_LoggerSchema','3.0.1759881251','auto');
INSERT INTO `wp_options` VALUES (1708,'wpforms_version','1.7.7.2','auto');
INSERT INTO `wp_options` VALUES (1709,'wpforms_version_lite','1.7.7.2','auto');
INSERT INTO `wp_options` VALUES (1710,'wpforms_activated','a:1:{s:3:\"pro\";i:1759881252;}','auto');
INSERT INTO `wp_options` VALUES (1715,'action_scheduler_lock_async-request-runner','1759881312','auto');
INSERT INTO `wp_options` VALUES (1716,'wpforms_versions_lite','a:7:{s:5:\"1.5.9\";i:0;s:7:\"1.6.7.2\";i:0;s:5:\"1.6.8\";i:0;s:5:\"1.7.5\";i:0;s:7:\"1.7.5.1\";i:0;s:5:\"1.7.7\";i:0;s:7:\"1.7.7.2\";i:1759881252;}','auto');
INSERT INTO `wp_options` VALUES (1717,'wpforms_versions','a:11:{s:5:\"1.1.6\";i:0;s:5:\"1.3.3\";i:0;s:5:\"1.4.3\";i:0;s:5:\"1.5.0\";i:0;s:5:\"1.5.9\";i:0;s:5:\"1.6.5\";i:0;s:5:\"1.6.8\";i:0;s:5:\"1.7.3\";i:0;s:5:\"1.7.5\";i:0;s:5:\"1.7.6\";i:0;s:7:\"1.7.7.2\";i:1759881252;}','auto');
INSERT INTO `wp_options` VALUES (1718,'widget_wpforms-widget','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (1719,'_transient_timeout_as-post-store-dependencies-met','1759967652','off');
INSERT INTO `wp_options` VALUES (1720,'_transient_as-post-store-dependencies-met','yes','off');
INSERT INTO `wp_options` VALUES (1722,'wpforms_admin_notices','a:1:{s:14:\"review_request\";a:2:{s:4:\"time\";i:1759881252;s:9:\"dismissed\";b:0;}}','auto');
INSERT INTO `wp_options` VALUES (1723,'_transient_wpforms_htaccess_file','a:3:{s:4:\"size\";i:737;s:5:\"mtime\";i:1759881253;s:5:\"ctime\";i:1759881253;}','on');
INSERT INTO `wp_options` VALUES (1733,'_site_transient_timeout_theme_roots','1759949743','off');
INSERT INTO `wp_options` VALUES (1734,'_site_transient_theme_roots','a:3:{s:15:\"hello-elementor\";s:7:\"/themes\";s:16:\"twentytwentyfour\";s:7:\"/themes\";s:17:\"twentytwentythree\";s:7:\"/themes\";}','off');
INSERT INTO `wp_options` VALUES (1735,'_site_transient_timeout_browser_2204ee63bef2f351470a66ffe1bb020e','1760552745','off');
INSERT INTO `wp_options` VALUES (1736,'_site_transient_browser_2204ee63bef2f351470a66ffe1bb020e','a:10:{s:4:\"name\";s:6:\"Chrome\";s:7:\"version\";s:9:\"141.0.0.0\";s:8:\"platform\";s:7:\"Windows\";s:10:\"update_url\";s:29:\"https://www.google.com/chrome\";s:7:\"img_src\";s:43:\"http://s.w.org/images/browsers/chrome.png?1\";s:11:\"img_src_ssl\";s:44:\"https://s.w.org/images/browsers/chrome.png?1\";s:15:\"current_version\";s:2:\"18\";s:7:\"upgrade\";b:0;s:8:\"insecure\";b:0;s:6:\"mobile\";b:0;}','off');
INSERT INTO `wp_options` VALUES (1746,'sfb_license','a:3:{s:3:\"key\";s:24:\"DEMO-ACTIVE-KEY-34b4d235\";s:5:\"email\";s:15:\"pro@example.com\";s:6:\"status\";s:6:\"active\";}','off');
INSERT INTO `wp_options` VALUES (1753,'_site_transient_timeout_wp_theme_files_patterns-be1386de40cf2d651bda19552fa1f5d0','1759966962','off');
INSERT INTO `wp_options` VALUES (1754,'_site_transient_wp_theme_files_patterns-be1386de40cf2d651bda19552fa1f5d0','a:2:{s:7:\"version\";s:5:\"3.4.4\";s:8:\"patterns\";a:0:{}}','off');
/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_postmeta`
--

DROP TABLE IF EXISTS `wp_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_postmeta`
--

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;
INSERT INTO `wp_postmeta` VALUES (1,2,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (2,3,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (3,6,'_edit_lock','1752105587:1');
INSERT INTO `wp_postmeta` VALUES (4,9,'_edit_lock','1752105814:1');
INSERT INTO `wp_postmeta` VALUES (5,11,'_edit_lock','1752105831:1');
INSERT INTO `wp_postmeta` VALUES (6,13,'_edit_lock','1752350012:1');
INSERT INTO `wp_postmeta` VALUES (7,15,'_edit_lock','1752105875:1');
INSERT INTO `wp_postmeta` VALUES (8,17,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (9,17,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (10,17,'_menu_item_object_id','15');
INSERT INTO `wp_postmeta` VALUES (11,17,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (12,17,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (13,17,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (14,17,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (15,17,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (17,18,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (18,18,'_menu_item_menu_item_parent','20');
INSERT INTO `wp_postmeta` VALUES (19,18,'_menu_item_object_id','13');
INSERT INTO `wp_postmeta` VALUES (20,18,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (21,18,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (22,18,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (23,18,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (24,18,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (26,19,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (27,19,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (28,19,'_menu_item_object_id','11');
INSERT INTO `wp_postmeta` VALUES (29,19,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (30,19,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (31,19,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (32,19,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (33,19,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (35,20,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (36,20,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (37,20,'_menu_item_object_id','9');
INSERT INTO `wp_postmeta` VALUES (38,20,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (39,20,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (40,20,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (41,20,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (42,20,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (44,21,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (45,21,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (46,21,'_menu_item_object_id','6');
INSERT INTO `wp_postmeta` VALUES (47,21,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (48,21,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (49,21,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (50,21,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (51,21,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (53,22,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (54,22,'_menu_item_menu_item_parent','19');
INSERT INTO `wp_postmeta` VALUES (55,22,'_menu_item_object_id','2');
INSERT INTO `wp_postmeta` VALUES (56,22,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (57,22,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (58,22,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (59,22,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (60,22,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (62,123,'_edit_lock','1752281641:1');
INSERT INTO `wp_postmeta` VALUES (67,126,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (68,126,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (69,126,'_menu_item_object_id','33');
INSERT INTO `wp_postmeta` VALUES (70,126,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (71,126,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (72,126,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (73,126,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (74,126,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (75,127,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (76,127,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (77,127,'_menu_item_object_id','54');
INSERT INTO `wp_postmeta` VALUES (78,127,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (79,127,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (80,127,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (81,127,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (82,127,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (83,13,'_edit_last','1');
INSERT INTO `wp_postmeta` VALUES (84,132,'_edit_lock','1759540343:1');
INSERT INTO `wp_postmeta` VALUES (85,132,'_wp_page_template','submittal-builder-template.php');
INSERT INTO `wp_postmeta` VALUES (86,134,'_edit_lock','1759448829:1');
INSERT INTO `wp_postmeta` VALUES (87,135,'_edit_lock','1759449183:1');
INSERT INTO `wp_postmeta` VALUES (88,136,'_edit_lock','1759449748:1');
INSERT INTO `wp_postmeta` VALUES (89,138,'_wp_attached_file','2025/10/formetallogo-p-500x387-1.png');
INSERT INTO `wp_postmeta` VALUES (90,138,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:500;s:6:\"height\";i:386;s:4:\"file\";s:36:\"2025/10/formetallogo-p-500x387-1.png\";s:8:\"filesize\";i:50712;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:36:\"formetallogo-p-500x387-1-300x232.png\";s:5:\"width\";i:300;s:6:\"height\";i:232;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:10701;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:36:\"formetallogo-p-500x387-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:5980;}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}');
/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_posts`
--

DROP TABLE IF EXISTS `wp_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_posts` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_parent` bigint unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `menu_order` int NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_posts`
--

LOCK TABLES `wp_posts` WRITE;
/*!40000 ALTER TABLE `wp_posts` DISABLE KEYS */;
INSERT INTO `wp_posts` VALUES (1,1,'2025-07-08 15:41:52','2025-07-08 15:41:52','<!-- wp:paragraph -->\n<p>Welcome to WordPress. This is your first post. Edit or delete it, then start writing!</p>\n<!-- /wp:paragraph -->','Hello world!','','publish','open','open','','hello-world','','','2025-07-08 15:41:52','2025-07-08 15:41:52','',0,'http://my-playground.local/?p=1',0,'post','',1);
INSERT INTO `wp_posts` VALUES (2,1,'2025-07-08 15:41:52','2025-07-08 15:41:52','<!-- wp:paragraph -->\n<p>This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>...or something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>As a new WordPress user, you should go to <a href=\"http://my-playground.local/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!</p>\n<!-- /wp:paragraph -->','Sample Page','','publish','closed','open','','sample-page','','','2025-07-08 15:41:52','2025-07-08 15:41:52','',0,'http://my-playground.local/?page_id=2',0,'page','',0);
INSERT INTO `wp_posts` VALUES (3,1,'2025-07-08 15:41:52','2025-07-08 15:41:52','<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we are</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Our website address is: http://my-playground.local.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Comments</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Media</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Cookies</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Embedded content from other websites</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we share your data with</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you request a password reset, your IP address will be included in the reset email.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">How long we retain your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">What rights you have over your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Where your data is sent</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Visitor comments may be checked through an automated spam detection service.</p>\n<!-- /wp:paragraph -->\n','Privacy Policy','','draft','closed','open','','privacy-policy','','','2025-07-08 15:41:52','2025-07-08 15:41:52','',0,'http://my-playground.local/?page_id=3',0,'page','',0);
INSERT INTO `wp_posts` VALUES (5,1,'2025-07-08 16:25:49','2025-07-08 16:25:49','<!-- wp:page-list /-->','Navigation','','publish','closed','closed','','navigation','','','2025-07-08 16:25:49','2025-07-08 16:25:49','',0,'http://my-playground.local/navigation/',0,'wp_navigation','',0);
INSERT INTO `wp_posts` VALUES (6,1,'2025-07-09 23:59:47','2025-07-09 23:59:47','','Home','','publish','closed','closed','','home','','','2025-07-09 23:59:47','2025-07-09 23:59:47','',0,'http://my-playground.local/?page_id=6',0,'page','',0);
INSERT INTO `wp_posts` VALUES (7,1,'2025-07-09 23:59:32','2025-07-09 23:59:32','{\"version\": 3, \"isGlobalStylesUserThemeJSON\": true }','Custom Styles','','publish','closed','closed','','wp-global-styles-twentytwentyfive','','','2025-07-09 23:59:32','2025-07-09 23:59:32','',0,'http://my-playground.local/wp-global-styles-twentytwentyfive/',0,'wp_global_styles','',0);
INSERT INTO `wp_posts` VALUES (8,1,'2025-07-09 23:59:47','2025-07-09 23:59:47','','Home','','inherit','closed','closed','','6-revision-v1','','','2025-07-09 23:59:47','2025-07-09 23:59:47','',6,'http://my-playground.local/?p=8',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (9,1,'2025-07-10 00:03:34','2025-07-10 00:03:34','','About Us','','publish','closed','closed','','about-us','','','2025-07-10 00:03:34','2025-07-10 00:03:34','',0,'http://my-playground.local/?page_id=9',0,'page','',0);
INSERT INTO `wp_posts` VALUES (10,1,'2025-07-10 00:03:34','2025-07-10 00:03:34','','About Us','','inherit','closed','closed','','9-revision-v1','','','2025-07-10 00:03:34','2025-07-10 00:03:34','',9,'http://my-playground.local/?p=10',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (11,1,'2025-07-10 00:03:54','2025-07-10 00:03:54','','Services','','publish','closed','closed','','services','','','2025-07-10 00:03:54','2025-07-10 00:03:54','',0,'http://my-playground.local/?page_id=11',0,'page','',0);
INSERT INTO `wp_posts` VALUES (12,1,'2025-07-10 00:03:54','2025-07-10 00:03:54','','Services','','inherit','closed','closed','','11-revision-v1','','','2025-07-10 00:03:54','2025-07-10 00:03:54','',11,'http://my-playground.local/?p=12',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (13,1,'2025-07-10 00:04:09','2025-07-10 00:04:09','','FAQS','','publish','closed','closed','','faqs','','','2025-07-12 19:53:32','2025-07-12 19:53:32','',9,'http://my-playground.local/?page_id=13',1,'page','',0);
INSERT INTO `wp_posts` VALUES (14,1,'2025-07-10 00:04:09','2025-07-10 00:04:09','','FAQS','','inherit','closed','closed','','13-revision-v1','','','2025-07-10 00:04:09','2025-07-10 00:04:09','',13,'http://my-playground.local/?p=14',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (15,1,'2025-07-10 00:04:34','2025-07-10 00:04:34','','Contact','','publish','closed','closed','','contact','','','2025-07-10 00:04:34','2025-07-10 00:04:34','',0,'http://my-playground.local/?page_id=15',0,'page','',0);
INSERT INTO `wp_posts` VALUES (16,1,'2025-07-10 00:04:34','2025-07-10 00:04:34','','Contact','','inherit','closed','closed','','15-revision-v1','','','2025-07-10 00:04:34','2025-07-10 00:04:34','',15,'http://my-playground.local/?p=16',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (17,1,'2025-07-10 00:10:08','2025-07-10 00:07:13',' ','','','publish','closed','closed','','17','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',0,'http://my-playground.local/?p=17',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (18,1,'2025-07-10 00:10:08','2025-07-10 00:07:13',' ','','','publish','closed','closed','','18','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',20,'http://my-playground.local/?p=18',8,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (19,1,'2025-07-10 00:10:08','2025-07-10 00:07:13',' ','','','publish','closed','closed','','19','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',0,'http://my-playground.local/?p=19',1,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (20,1,'2025-07-10 00:10:08','2025-07-10 00:07:13',' ','','','publish','closed','closed','','20','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',0,'http://my-playground.local/?p=20',7,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (21,1,'2025-07-10 00:10:08','2025-07-10 00:07:13',' ','','','publish','closed','closed','','21','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',0,'http://my-playground.local/?p=21',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (22,1,'2025-07-10 00:10:08','2025-07-10 00:07:13',' ','','','publish','closed','closed','','22','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',19,'http://my-playground.local/?p=22',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (23,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Parent Page 1.','Parent Page 1','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/?page_id=23',0,'page','',0);
INSERT INTO `wp_posts` VALUES (24,5,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Parent Page 2.','Parent Page 2','','publish','closed','closed','','parent-page-2','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/parent-page-2/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (25,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Parent Page 3.','Parent Page 3','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/?page_id=25',0,'page','',0);
INSERT INTO `wp_posts` VALUES (26,1,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Parent Page 4.','Parent Page 4','','publish','closed','closed','','parent-page-4','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/parent-page-4/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (27,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Parent Page 5.','Parent Page 5','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/?page_id=27',0,'page','',0);
INSERT INTO `wp_posts` VALUES (28,1,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Parent Page 6.','Parent Page 6','','publish','closed','closed','','parent-page-6','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/parent-page-6/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (29,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Parent Page 7.','Parent Page 7','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/?page_id=29',0,'page','',0);
INSERT INTO `wp_posts` VALUES (30,4,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Parent Page 8.','Parent Page 8','','publish','closed','closed','','parent-page-8','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/parent-page-8/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (31,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Parent Page 9.','Parent Page 9','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/?page_id=31',0,'page','',0);
INSERT INTO `wp_posts` VALUES (32,5,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Parent Page 10.','Parent Page 10','','publish','closed','closed','','parent-page-10','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',0,'http://my-playground.local/parent-page-10/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (33,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 1.','Child Page 1 (Parent: 24)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/?page_id=33',0,'page','',0);
INSERT INTO `wp_posts` VALUES (34,4,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 2.','Child Page 2 (Parent: 27)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/?page_id=34',0,'page','',0);
INSERT INTO `wp_posts` VALUES (35,2,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 3.','Child Page 3 (Parent: 25)','','publish','closed','closed','','child-page-3-parent-25','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',25,'http://my-playground.local/child-page-3-parent-25/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (36,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 4.','Child Page 4 (Parent: 27)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/?page_id=36',0,'page','',0);
INSERT INTO `wp_posts` VALUES (37,4,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 5.','Child Page 5 (Parent: 23)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',23,'http://my-playground.local/?page_id=37',0,'page','',0);
INSERT INTO `wp_posts` VALUES (38,2,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 6.','Child Page 6 (Parent: 32)','','publish','closed','closed','','child-page-6-parent-32','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/parent-page-10/child-page-6-parent-32/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (39,4,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 7.','Child Page 7 (Parent: 23)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',23,'http://my-playground.local/?page_id=39',0,'page','',0);
INSERT INTO `wp_posts` VALUES (40,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 8.','Child Page 8 (Parent: 31)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',31,'http://my-playground.local/?page_id=40',0,'page','',0);
INSERT INTO `wp_posts` VALUES (41,4,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 9.','Child Page 9 (Parent: 30)','','publish','closed','closed','','child-page-9-parent-30','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',30,'http://my-playground.local/parent-page-8/child-page-9-parent-30/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (42,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 10.','Child Page 10 (Parent: 31)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',31,'http://my-playground.local/?page_id=42',0,'page','',0);
INSERT INTO `wp_posts` VALUES (43,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 11.','Child Page 11 (Parent: 24)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/?page_id=43',0,'page','',0);
INSERT INTO `wp_posts` VALUES (44,4,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 12.','Child Page 12 (Parent: 31)','','publish','closed','closed','','child-page-12-parent-31','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',31,'http://my-playground.local/child-page-12-parent-31/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (45,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 13.','Child Page 13 (Parent: 23)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',23,'http://my-playground.local/?page_id=45',0,'page','',0);
INSERT INTO `wp_posts` VALUES (46,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 14.','Child Page 14 (Parent: 26)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',26,'http://my-playground.local/?page_id=46',0,'page','',0);
INSERT INTO `wp_posts` VALUES (47,2,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 15.','Child Page 15 (Parent: 32)','','publish','closed','closed','','child-page-15-parent-32','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/parent-page-10/child-page-15-parent-32/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (48,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 16.','Child Page 16 (Parent: 28)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',28,'http://my-playground.local/?page_id=48',0,'page','',0);
INSERT INTO `wp_posts` VALUES (49,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 17.','Child Page 17 (Parent: 32)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/?page_id=49',0,'page','',0);
INSERT INTO `wp_posts` VALUES (50,3,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 18.','Child Page 18 (Parent: 24)','','publish','closed','closed','','child-page-18-parent-24','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/parent-page-2/child-page-18-parent-24/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (51,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 19.','Child Page 19 (Parent: 24)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/?page_id=51',0,'page','',0);
INSERT INTO `wp_posts` VALUES (52,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 20.','Child Page 20 (Parent: 31)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',31,'http://my-playground.local/?page_id=52',0,'page','',0);
INSERT INTO `wp_posts` VALUES (53,2,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 21.','Child Page 21 (Parent: 27)','','publish','closed','closed','','child-page-21-parent-27','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/child-page-21-parent-27/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (54,4,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 22.','Child Page 22 (Parent: 32)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/?page_id=54',0,'page','',0);
INSERT INTO `wp_posts` VALUES (55,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 23.','Child Page 23 (Parent: 27)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/?page_id=55',0,'page','',0);
INSERT INTO `wp_posts` VALUES (56,4,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 24.','Child Page 24 (Parent: 23)','','publish','closed','closed','','child-page-24-parent-23','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',23,'http://my-playground.local/child-page-24-parent-23/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (57,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 25.','Child Page 25 (Parent: 32)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/?page_id=57',0,'page','',0);
INSERT INTO `wp_posts` VALUES (58,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 26.','Child Page 26 (Parent: 23)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',23,'http://my-playground.local/?page_id=58',0,'page','',0);
INSERT INTO `wp_posts` VALUES (59,5,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 27.','Child Page 27 (Parent: 24)','','publish','closed','closed','','child-page-27-parent-24','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/parent-page-2/child-page-27-parent-24/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (60,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 28.','Child Page 28 (Parent: 28)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',28,'http://my-playground.local/?page_id=60',0,'page','',0);
INSERT INTO `wp_posts` VALUES (61,4,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 29.','Child Page 29 (Parent: 29)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',29,'http://my-playground.local/?page_id=61',0,'page','',0);
INSERT INTO `wp_posts` VALUES (62,3,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 30.','Child Page 30 (Parent: 29)','','publish','closed','closed','','child-page-30-parent-29','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',29,'http://my-playground.local/child-page-30-parent-29/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (63,1,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 31.','Child Page 31 (Parent: 28)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',28,'http://my-playground.local/?page_id=63',0,'page','',0);
INSERT INTO `wp_posts` VALUES (64,4,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 32.','Child Page 32 (Parent: 32)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/?page_id=64',0,'page','',0);
INSERT INTO `wp_posts` VALUES (65,3,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 33.','Child Page 33 (Parent: 27)','','publish','closed','closed','','child-page-33-parent-27','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/child-page-33-parent-27/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (66,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 34.','Child Page 34 (Parent: 27)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/?page_id=66',0,'page','',0);
INSERT INTO `wp_posts` VALUES (67,5,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 35.','Child Page 35 (Parent: 31)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',31,'http://my-playground.local/?page_id=67',0,'page','',0);
INSERT INTO `wp_posts` VALUES (68,4,'2025-07-11 21:49:41','2025-07-11 21:49:41','This is test content for Child Page 36.','Child Page 36 (Parent: 24)','','publish','closed','closed','','child-page-36-parent-24','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/parent-page-2/child-page-36-parent-24/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (69,2,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 37.','Child Page 37 (Parent: 32)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/?page_id=69',0,'page','',0);
INSERT INTO `wp_posts` VALUES (70,2,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 38.','Child Page 38 (Parent: 30)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',30,'http://my-playground.local/?page_id=70',0,'page','',0);
INSERT INTO `wp_posts` VALUES (71,2,'2025-07-11 21:49:42','2025-07-11 21:49:42','This is test content for Child Page 39.','Child Page 39 (Parent: 24)','','publish','closed','closed','','child-page-39-parent-24','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/parent-page-2/child-page-39-parent-24/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (72,3,'2025-07-11 21:56:29','0000-00-00 00:00:00','This is test content for Child Page 40.','Child Page 40 (Parent: 27)','','draft','closed','closed','','','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/?page_id=72',0,'page','',0);
INSERT INTO `wp_posts` VALUES (73,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 40.','Child Page 40 (Parent: 27)','','inherit','closed','closed','','72-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',72,'http://my-playground.local/?p=73',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (74,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 39.','Child Page 39 (Parent: 24)','','inherit','closed','closed','','71-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',71,'http://my-playground.local/?p=74',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (75,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 1.','Parent Page 1','','inherit','closed','closed','','23-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',23,'http://my-playground.local/?p=75',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (76,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 3.','Parent Page 3','','inherit','closed','closed','','25-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',25,'http://my-playground.local/?p=76',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (77,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 5.','Parent Page 5','','inherit','closed','closed','','27-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',27,'http://my-playground.local/?p=77',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (78,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 7.','Parent Page 7','','inherit','closed','closed','','29-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',29,'http://my-playground.local/?p=78',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (79,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 9.','Parent Page 9','','inherit','closed','closed','','31-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',31,'http://my-playground.local/?p=79',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (80,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 1.','Child Page 1 (Parent: 24)','','inherit','closed','closed','','33-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',33,'http://my-playground.local/?p=80',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (81,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 13.','Child Page 13 (Parent: 23)','','inherit','closed','closed','','45-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',45,'http://my-playground.local/?p=81',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (82,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 4.','Child Page 4 (Parent: 27)','','inherit','closed','closed','','36-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',36,'http://my-playground.local/?p=82',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (83,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 5.','Child Page 5 (Parent: 23)','','inherit','closed','closed','','37-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',37,'http://my-playground.local/?p=83',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (84,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 7.','Child Page 7 (Parent: 23)','','inherit','closed','closed','','39-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',39,'http://my-playground.local/?p=84',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (85,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 8.','Child Page 8 (Parent: 31)','','inherit','closed','closed','','40-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',40,'http://my-playground.local/?p=85',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (86,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 10.','Child Page 10 (Parent: 31)','','inherit','closed','closed','','42-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',42,'http://my-playground.local/?p=86',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (87,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 11.','Child Page 11 (Parent: 24)','','inherit','closed','closed','','43-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',43,'http://my-playground.local/?p=87',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (88,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 14.','Child Page 14 (Parent: 26)','','inherit','closed','closed','','46-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',46,'http://my-playground.local/?p=88',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (89,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 16.','Child Page 16 (Parent: 28)','','inherit','closed','closed','','48-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',48,'http://my-playground.local/?p=89',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (90,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 17.','Child Page 17 (Parent: 32)','','inherit','closed','closed','','49-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',49,'http://my-playground.local/?p=90',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (91,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 19.','Child Page 19 (Parent: 24)','','inherit','closed','closed','','51-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',51,'http://my-playground.local/?p=91',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (92,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 20.','Child Page 20 (Parent: 31)','','inherit','closed','closed','','52-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',52,'http://my-playground.local/?p=92',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (93,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 22.','Child Page 22 (Parent: 32)','','inherit','closed','closed','','54-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',54,'http://my-playground.local/?p=93',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (94,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 23.','Child Page 23 (Parent: 27)','','inherit','closed','closed','','55-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',55,'http://my-playground.local/?p=94',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (95,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 25.','Child Page 25 (Parent: 32)','','inherit','closed','closed','','57-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',57,'http://my-playground.local/?p=95',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (96,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 26.','Child Page 26 (Parent: 23)','','inherit','closed','closed','','58-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',58,'http://my-playground.local/?p=96',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (97,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 28.','Child Page 28 (Parent: 28)','','inherit','closed','closed','','60-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',60,'http://my-playground.local/?p=97',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (98,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 29.','Child Page 29 (Parent: 29)','','inherit','closed','closed','','61-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',61,'http://my-playground.local/?p=98',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (99,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 31.','Child Page 31 (Parent: 28)','','inherit','closed','closed','','63-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',63,'http://my-playground.local/?p=99',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (100,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 32.','Child Page 32 (Parent: 32)','','inherit','closed','closed','','64-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',64,'http://my-playground.local/?p=100',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (101,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 34.','Child Page 34 (Parent: 27)','','inherit','closed','closed','','66-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',66,'http://my-playground.local/?p=101',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (102,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 35.','Child Page 35 (Parent: 31)','','inherit','closed','closed','','67-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',67,'http://my-playground.local/?p=102',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (103,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 37.','Child Page 37 (Parent: 32)','','inherit','closed','closed','','69-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',69,'http://my-playground.local/?p=103',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (104,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 38.','Child Page 38 (Parent: 30)','','inherit','closed','closed','','70-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',70,'http://my-playground.local/?p=104',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (105,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 2.','Parent Page 2','','inherit','closed','closed','','24-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',24,'http://my-playground.local/?p=105',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (106,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 4.','Parent Page 4','','inherit','closed','closed','','26-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',26,'http://my-playground.local/?p=106',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (107,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 6.','Parent Page 6','','inherit','closed','closed','','28-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',28,'http://my-playground.local/?p=107',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (108,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 2.','Child Page 2 (Parent: 27)','','inherit','closed','closed','','34-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',34,'http://my-playground.local/?p=108',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (109,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 10.','Parent Page 10','','inherit','closed','closed','','32-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',32,'http://my-playground.local/?p=109',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (110,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 3.','Child Page 3 (Parent: 25)','','inherit','closed','closed','','35-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',35,'http://my-playground.local/?p=110',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (111,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 6.','Child Page 6 (Parent: 32)','','inherit','closed','closed','','38-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',38,'http://my-playground.local/?p=111',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (112,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 9.','Child Page 9 (Parent: 30)','','inherit','closed','closed','','41-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',41,'http://my-playground.local/?p=112',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (113,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 12.','Child Page 12 (Parent: 31)','','inherit','closed','closed','','44-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',44,'http://my-playground.local/?p=113',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (114,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 15.','Child Page 15 (Parent: 32)','','inherit','closed','closed','','47-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',47,'http://my-playground.local/?p=114',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (115,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Parent Page 8.','Parent Page 8','','inherit','closed','closed','','30-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',30,'http://my-playground.local/?p=115',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (116,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 21.','Child Page 21 (Parent: 27)','','inherit','closed','closed','','53-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',53,'http://my-playground.local/?p=116',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (117,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 24.','Child Page 24 (Parent: 23)','','inherit','closed','closed','','56-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',56,'http://my-playground.local/?p=117',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (118,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 27.','Child Page 27 (Parent: 24)','','inherit','closed','closed','','59-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',59,'http://my-playground.local/?p=118',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (119,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 30.','Child Page 30 (Parent: 29)','','inherit','closed','closed','','62-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',62,'http://my-playground.local/?p=119',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (120,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 33.','Child Page 33 (Parent: 27)','','inherit','closed','closed','','65-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',65,'http://my-playground.local/?p=120',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (121,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 36.','Child Page 36 (Parent: 24)','','inherit','closed','closed','','68-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',68,'http://my-playground.local/?p=121',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (122,1,'2025-07-11 21:56:29','2025-07-11 21:56:29','This is test content for Child Page 18.','Child Page 18 (Parent: 24)','','inherit','closed','closed','','50-revision-v1','','','2025-07-11 21:56:29','2025-07-11 21:56:29','',50,'http://my-playground.local/?p=122',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (123,1,'2025-07-12 00:43:35','2025-07-12 00:43:35','','Post Page 2','','publish','open','open','','post-page-2','','','2025-07-12 00:53:51','2025-07-12 00:53:51','',0,'http://my-playground.local/?p=123',0,'post','',0);
INSERT INTO `wp_posts` VALUES (124,1,'2025-07-12 00:43:25','2025-07-12 00:43:25','{\"version\": 3, \"isGlobalStylesUserThemeJSON\": true }','Custom Styles','','publish','closed','closed','','wp-global-styles-hello-elementor','','','2025-07-12 00:43:25','2025-07-12 00:43:25','',0,'http://my-playground.local/wp-global-styles-hello-elementor/',0,'wp_global_styles','',0);
INSERT INTO `wp_posts` VALUES (125,1,'2025-07-12 00:43:35','2025-07-12 00:43:35','','Post Page 2','','inherit','closed','closed','','123-revision-v1','','','2025-07-12 00:43:35','2025-07-12 00:43:35','',123,'http://my-playground.local/?p=125',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (126,1,'2025-07-12 01:38:46','2025-07-12 01:38:46',' ','','','publish','closed','closed','','126','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',0,'http://my-playground.local/126/',5,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (127,1,'2025-07-12 01:39:08','2025-07-12 01:39:08',' ','','','publish','closed','closed','','127','','','2025-07-13 17:56:30','2025-07-13 17:56:30','',0,'http://my-playground.local/127/',6,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (131,1,'2025-10-02 22:48:39','0000-00-00 00:00:00','','Auto Draft','','auto-draft','open','open','','','','','2025-10-02 22:48:39','0000-00-00 00:00:00','',0,'http://my-playground.local/?p=131',0,'post','',0);
INSERT INTO `wp_posts` VALUES (132,1,'2025-10-02 22:55:39','2025-10-02 22:55:39','<!-- wp:shortcode -->\n[submittal_builder id=\"1\"]\n<!-- /wp:shortcode -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->','Submittal Builder','','publish','closed','closed','','submittal-builder','','','2025-10-03 23:27:27','2025-10-03 23:27:27','',0,'http://my-playground.local/?page_id=132',0,'page','',0);
INSERT INTO `wp_posts` VALUES (133,1,'2025-10-02 22:55:39','2025-10-02 22:55:39','','Submittal Builder','','inherit','closed','closed','','132-revision-v1','','','2025-10-02 22:55:39','2025-10-02 22:55:39','',132,'http://my-playground.local/?p=133',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (134,1,'2025-10-02 23:49:30','0000-00-00 00:00:00','','Auto Draft','','auto-draft','closed','closed','','','','','2025-10-02 23:49:30','0000-00-00 00:00:00','',0,'http://my-playground.local/?post_type=submittal_item&p=134',0,'submittal_item','',0);
INSERT INTO `wp_posts` VALUES (135,1,'2025-10-02 23:55:25','0000-00-00 00:00:00','','Auto Draft','','auto-draft','closed','closed','','','','','2025-10-02 23:55:25','0000-00-00 00:00:00','',0,'http://my-playground.local/?post_type=submittal_item&p=135',0,'submittal_item','',0);
INSERT INTO `wp_posts` VALUES (136,1,'2025-10-03 00:03:51','0000-00-00 00:00:00','','Auto Draft','','auto-draft','closed','closed','','','','','2025-10-03 00:03:51','0000-00-00 00:00:00','',0,'http://my-playground.local/?post_type=submittal_item&p=136',0,'submittal_item','',0);
INSERT INTO `wp_posts` VALUES (137,1,'2025-10-03 23:27:27','2025-10-03 23:27:27','<!-- wp:shortcode -->\n[submittal_builder id=\"1\"]\n<!-- /wp:shortcode -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->','Submittal Builder','','inherit','closed','closed','','132-revision-v1','','','2025-10-03 23:27:27','2025-10-03 23:27:27','',132,'http://my-playground.local/?p=137',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (138,1,'2025-10-04 16:14:17','2025-10-04 16:14:17','','formetallogo-p-500x387','','inherit','open','closed','','formetallogo-p-500x387','','','2025-10-04 16:14:17','2025-10-04 16:14:17','',0,'http://my-playground.local/wp-content/uploads/2025/10/formetallogo-p-500x387-1.png',0,'attachment','image/png',0);
/*!40000 ALTER TABLE `wp_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_sfb_forms`
--

DROP TABLE IF EXISTS `wp_sfb_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_sfb_forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `settings_json` longtext COLLATE utf8mb4_unicode_520_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_sfb_forms`
--

LOCK TABLES `wp_sfb_forms` WRITE;
/*!40000 ALTER TABLE `wp_sfb_forms` DISABLE KEYS */;
INSERT INTO `wp_sfb_forms` VALUES (1,'Demo Form',NULL,'2025-10-03 20:15:54','2025-10-03 20:15:54');
/*!40000 ALTER TABLE `wp_sfb_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_sfb_nodes`
--

DROP TABLE IF EXISTS `wp_sfb_nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_sfb_nodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `node_type` enum('category','product','type','model') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `title` varchar(190) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `slug` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `position` int unsigned DEFAULT '0',
  `settings_json` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  KEY `parent_id` (`parent_id`),
  KEY `node_type` (`node_type`),
  KEY `form_parent_pos` (`form_id`,`parent_id`,`position`),
  KEY `form_type` (`form_id`,`node_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1745 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_sfb_nodes`
--

LOCK TABLES `wp_sfb_nodes` WRITE;
/*!40000 ALTER TABLE `wp_sfb_nodes` DISABLE KEYS */;
INSERT INTO `wp_sfb_nodes` VALUES (1320,1,0,'category','Roofing & Decking','roofing-decking',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1321,1,0,'category','Floor Systems','floor-systems',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1322,1,0,'category','Wall Panels','wall-panels',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1323,1,0,'category','Structural Columns','structural-columns',40,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1324,1,0,'category','Trusses & Joists','trusses-joists',50,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1325,1,1320,'product','Track (C1P1)','track-c1p1',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1326,1,1320,'product','Shaftwall (C1P2)','shaftwall-c1p2',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1327,1,1320,'product','Furring (C1P3)','furring-c1p3',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1328,1,1321,'product','Track (C2P1)','track-c2p1',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1329,1,1321,'product','Shaftwall (C2P2)','shaftwall-c2p2',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1330,1,1321,'product','Furring (C2P3)','furring-c2p3',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1331,1,1322,'product','Track (C3P1)','track-c3p1',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1332,1,1322,'product','Shaftwall (C3P2)','shaftwall-c3p2',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1333,1,1322,'product','Furring (C3P3)','furring-c3p3',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1334,1,1323,'product','Track (C4P1)','track-c4p1',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1335,1,1323,'product','Shaftwall (C4P2)','shaftwall-c4p2',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1336,1,1323,'product','Furring (C4P3)','furring-c4p3',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1337,1,1324,'product','Track (C5P1)','track-c5p1',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1338,1,1324,'product','Shaftwall (C5P2)','shaftwall-c5p2',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1339,1,1324,'product','Furring (C5P3)','furring-c5p3',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1340,1,1325,'type','25 Gauge (T11)','25-gauge-t11',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1341,1,1325,'type','30 mil (T12)','30-mil-t12',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1342,1,1325,'type','33 mil (T13)','33-mil-t13',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1343,1,1326,'type','25 Gauge (T21)','25-gauge-t21',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1344,1,1326,'type','30 mil (T22)','30-mil-t22',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1345,1,1326,'type','33 mil (T23)','33-mil-t23',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1346,1,1327,'type','25 Gauge (T31)','25-gauge-t31',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1347,1,1327,'type','30 mil (T32)','30-mil-t32',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1348,1,1327,'type','33 mil (T33)','33-mil-t33',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1349,1,1328,'type','25 Gauge (T11)','25-gauge-t11',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1350,1,1328,'type','30 mil (T12)','30-mil-t12',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1351,1,1328,'type','33 mil (T13)','33-mil-t13',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1352,1,1329,'type','25 Gauge (T21)','25-gauge-t21',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1353,1,1329,'type','30 mil (T22)','30-mil-t22',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1354,1,1329,'type','33 mil (T23)','33-mil-t23',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1355,1,1330,'type','25 Gauge (T31)','25-gauge-t31',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1356,1,1330,'type','30 mil (T32)','30-mil-t32',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1357,1,1330,'type','33 mil (T33)','33-mil-t33',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1358,1,1331,'type','25 Gauge (T11)','25-gauge-t11',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1359,1,1331,'type','30 mil (T12)','30-mil-t12',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1360,1,1331,'type','33 mil (T13)','33-mil-t13',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1361,1,1332,'type','25 Gauge (T21)','25-gauge-t21',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1362,1,1332,'type','30 mil (T22)','30-mil-t22',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1363,1,1332,'type','33 mil (T23)','33-mil-t23',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1364,1,1333,'type','25 Gauge (T31)','25-gauge-t31',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1365,1,1333,'type','30 mil (T32)','30-mil-t32',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1366,1,1333,'type','33 mil (T33)','33-mil-t33',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1367,1,1334,'type','25 Gauge (T11)','25-gauge-t11',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1368,1,1334,'type','30 mil (T12)','30-mil-t12',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1369,1,1334,'type','33 mil (T13)','33-mil-t13',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1370,1,1335,'type','25 Gauge (T21)','25-gauge-t21',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1371,1,1335,'type','30 mil (T22)','30-mil-t22',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1372,1,1335,'type','33 mil (T23)','33-mil-t23',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1373,1,1336,'type','25 Gauge (T31)','25-gauge-t31',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1374,1,1336,'type','30 mil (T32)','30-mil-t32',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1375,1,1336,'type','33 mil (T33)','33-mil-t33',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1376,1,1337,'type','25 Gauge (T11)','25-gauge-t11',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1377,1,1337,'type','30 mil (T12)','30-mil-t12',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1378,1,1337,'type','33 mil (T13)','33-mil-t13',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1379,1,1338,'type','25 Gauge (T21)','25-gauge-t21',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1380,1,1338,'type','30 mil (T22)','30-mil-t22',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1381,1,1338,'type','33 mil (T23)','33-mil-t23',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1382,1,1339,'type','25 Gauge (T31)','25-gauge-t31',10,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1383,1,1339,'type','30 mil (T32)','30-mil-t32',20,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1384,1,1339,'type','33 mil (T33)','33-mil-t33',30,NULL);
INSERT INTO `wp_sfb_nodes` VALUES (1385,1,1340,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1386,1,1340,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1387,1,1340,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1388,1,1340,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1389,1,1340,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1390,1,1340,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1391,1,1340,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1392,1,1340,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1393,1,1341,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1394,1,1341,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1395,1,1341,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1396,1,1341,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1397,1,1341,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1398,1,1341,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1399,1,1341,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1400,1,1341,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1401,1,1342,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1402,1,1342,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1403,1,1342,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1404,1,1342,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1405,1,1342,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1406,1,1342,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1407,1,1342,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1408,1,1342,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1409,1,1343,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1410,1,1343,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1411,1,1343,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1412,1,1343,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1413,1,1343,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1414,1,1343,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1415,1,1343,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1416,1,1343,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1417,1,1344,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1418,1,1344,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1419,1,1344,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1420,1,1344,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1421,1,1344,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1422,1,1344,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1423,1,1344,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1424,1,1344,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1425,1,1345,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1426,1,1345,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1427,1,1345,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1428,1,1345,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1429,1,1345,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1430,1,1345,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1431,1,1345,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1432,1,1345,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1433,1,1346,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1434,1,1346,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1435,1,1346,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1436,1,1346,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1437,1,1346,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1438,1,1346,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1439,1,1346,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1440,1,1346,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1441,1,1347,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1442,1,1347,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1443,1,1347,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1444,1,1347,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1445,1,1347,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1446,1,1347,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1447,1,1347,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1448,1,1347,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1449,1,1348,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1450,1,1348,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1451,1,1348,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1452,1,1348,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1453,1,1348,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1454,1,1348,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1455,1,1348,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1456,1,1348,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1457,1,1349,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1458,1,1349,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1459,1,1349,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1460,1,1349,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1461,1,1349,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1462,1,1349,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1463,1,1349,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1464,1,1349,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1465,1,1350,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1466,1,1350,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1467,1,1350,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1468,1,1350,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1469,1,1350,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1470,1,1350,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1471,1,1350,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1472,1,1350,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1473,1,1351,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1474,1,1351,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1475,1,1351,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1476,1,1351,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1477,1,1351,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1478,1,1351,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1479,1,1351,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1480,1,1351,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1481,1,1352,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1482,1,1352,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1483,1,1352,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1484,1,1352,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1485,1,1352,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1486,1,1352,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1487,1,1352,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1488,1,1352,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1489,1,1353,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1490,1,1353,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1491,1,1353,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1492,1,1353,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1493,1,1353,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1494,1,1353,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1495,1,1353,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1496,1,1353,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1497,1,1354,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1498,1,1354,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1499,1,1354,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1500,1,1354,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1501,1,1354,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1502,1,1354,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1503,1,1354,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1504,1,1354,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1505,1,1355,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1506,1,1355,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1507,1,1355,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1508,1,1355,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1509,1,1355,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1510,1,1355,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1511,1,1355,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1512,1,1355,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1513,1,1356,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1514,1,1356,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1515,1,1356,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1516,1,1356,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1517,1,1356,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1518,1,1356,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1519,1,1356,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1520,1,1356,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1521,1,1357,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1522,1,1357,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1523,1,1357,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1524,1,1357,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1525,1,1357,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1526,1,1357,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1527,1,1357,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1528,1,1357,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1529,1,1358,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1530,1,1358,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1531,1,1358,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1532,1,1358,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1533,1,1358,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1534,1,1358,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1535,1,1358,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1536,1,1358,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1537,1,1359,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1538,1,1359,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1539,1,1359,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1540,1,1359,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1541,1,1359,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1542,1,1359,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1543,1,1359,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1544,1,1359,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1545,1,1360,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1546,1,1360,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1547,1,1360,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1548,1,1360,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1549,1,1360,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1550,1,1360,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1551,1,1360,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1552,1,1360,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1553,1,1361,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1554,1,1361,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1555,1,1361,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1556,1,1361,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1557,1,1361,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1558,1,1361,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1559,1,1361,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1560,1,1361,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1561,1,1362,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1562,1,1362,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1563,1,1362,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1564,1,1362,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1565,1,1362,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1566,1,1362,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1567,1,1362,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1568,1,1362,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1569,1,1363,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1570,1,1363,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1571,1,1363,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1572,1,1363,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1573,1,1363,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1574,1,1363,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1575,1,1363,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1576,1,1363,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1577,1,1364,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1578,1,1364,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1579,1,1364,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1580,1,1364,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1581,1,1364,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1582,1,1364,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1583,1,1364,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1584,1,1364,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1585,1,1365,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1586,1,1365,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1587,1,1365,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1588,1,1365,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1589,1,1365,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1590,1,1365,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1591,1,1365,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1592,1,1365,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1593,1,1366,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1594,1,1366,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1595,1,1366,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1596,1,1366,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1597,1,1366,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1598,1,1366,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1599,1,1366,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1600,1,1366,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1601,1,1367,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1602,1,1367,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1603,1,1367,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1604,1,1367,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1605,1,1367,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1606,1,1367,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1607,1,1367,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1608,1,1367,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1609,1,1368,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1610,1,1368,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1611,1,1368,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1612,1,1368,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1613,1,1368,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1614,1,1368,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1615,1,1368,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1616,1,1368,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1617,1,1369,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1618,1,1369,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1619,1,1369,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1620,1,1369,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1621,1,1369,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1622,1,1369,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1623,1,1369,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1624,1,1369,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1625,1,1370,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1626,1,1370,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1627,1,1370,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1628,1,1370,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1629,1,1370,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1630,1,1370,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1631,1,1370,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1632,1,1370,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1633,1,1371,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1634,1,1371,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1635,1,1371,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1636,1,1371,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1637,1,1371,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1638,1,1371,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1639,1,1371,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1640,1,1371,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1641,1,1372,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1642,1,1372,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1643,1,1372,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1644,1,1372,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1645,1,1372,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1646,1,1372,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1647,1,1372,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1648,1,1372,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1649,1,1373,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1650,1,1373,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1651,1,1373,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1652,1,1373,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1653,1,1373,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1654,1,1373,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1655,1,1373,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1656,1,1373,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1657,1,1374,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1658,1,1374,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1659,1,1374,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1660,1,1374,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1661,1,1374,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1662,1,1374,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1663,1,1374,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1664,1,1374,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1665,1,1375,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1666,1,1375,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1667,1,1375,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1668,1,1375,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1669,1,1375,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1670,1,1375,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1671,1,1375,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1672,1,1375,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1673,1,1376,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1674,1,1376,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1675,1,1376,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1676,1,1376,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1677,1,1376,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1678,1,1376,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1679,1,1376,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1680,1,1376,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1681,1,1377,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1682,1,1377,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1683,1,1377,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1684,1,1377,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1685,1,1377,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1686,1,1377,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1687,1,1377,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1688,1,1377,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1689,1,1378,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1690,1,1378,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1691,1,1378,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1692,1,1378,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1693,1,1378,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1694,1,1378,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1695,1,1378,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1696,1,1378,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1697,1,1379,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1698,1,1379,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1699,1,1379,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1700,1,1379,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1701,1,1379,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1702,1,1379,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1703,1,1379,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1704,1,1379,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1705,1,1380,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1706,1,1380,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1707,1,1380,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1708,1,1380,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1709,1,1380,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1710,1,1380,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1711,1,1380,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1712,1,1380,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1713,1,1381,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1714,1,1381,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1715,1,1381,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1716,1,1381,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1717,1,1381,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1718,1,1381,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1719,1,1381,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1720,1,1381,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1721,1,1382,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1722,1,1382,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1723,1,1382,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1724,1,1382,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1725,1,1382,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1726,1,1382,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1727,1,1382,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1728,1,1382,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1729,1,1383,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1730,1,1383,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1731,1,1383,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1732,1,1383,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1733,1,1383,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1734,1,1383,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1735,1,1383,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1736,1,1383,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1737,1,1384,'model','300S150-25','300s150-25',10,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1738,1,1384,'model','362S162-30','362s162-30',20,'{\"fields\":{\"size\":\"3-5/8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"33 mil (20 ga+)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1739,1,1384,'model','400S200-33','400s200-33',30,'{\"fields\":{\"size\":\"4\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"43 mil (18 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1740,1,1384,'model','450S125-43','450s125-43',40,'{\"fields\":{\"size\":\"6\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"54 mil (16 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1741,1,1384,'model','600S150-54','600s150-54',50,'{\"fields\":{\"size\":\"8\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"68 mil (14 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1742,1,1384,'model','800S162-68','800s162-68',60,'{\"fields\":{\"size\":\"10\\\"\",\"flange\":\"1-1/4\\\"\",\"thickness\":\"97 mil (12 ga)\",\"ksi\":33}}');
INSERT INTO `wp_sfb_nodes` VALUES (1743,1,1384,'model','1000S200-97','1000s200-97',70,'{\"fields\":{\"size\":\"2-1/2\\\"\",\"flange\":\"1-1/2\\\"\",\"thickness\":\"20 mil (25 ga)\",\"ksi\":50}}');
INSERT INTO `wp_sfb_nodes` VALUES (1744,1,1384,'model','250S125-20','250s125-20',80,'{\"fields\":{\"size\":\"3\\\"\",\"flange\":\"2\\\"\",\"thickness\":\"30 mil (20 ga)\",\"ksi\":33}}');
/*!40000 ALTER TABLE `wp_sfb_nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_sfb_shares`
--

DROP TABLE IF EXISTS `wp_sfb_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_sfb_shares` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `payload_json` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_sfb_shares`
--

LOCK TABLES `wp_sfb_shares` WRITE;
/*!40000 ALTER TABLE `wp_sfb_shares` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_sfb_shares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_relationships`
--

DROP TABLE IF EXISTS `wp_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_relationships`
--

LOCK TABLES `wp_term_relationships` WRITE;
/*!40000 ALTER TABLE `wp_term_relationships` DISABLE KEYS */;
INSERT INTO `wp_term_relationships` VALUES (1,1,0);
INSERT INTO `wp_term_relationships` VALUES (7,2,0);
INSERT INTO `wp_term_relationships` VALUES (17,3,0);
INSERT INTO `wp_term_relationships` VALUES (18,3,0);
INSERT INTO `wp_term_relationships` VALUES (19,3,0);
INSERT INTO `wp_term_relationships` VALUES (20,3,0);
INSERT INTO `wp_term_relationships` VALUES (21,3,0);
INSERT INTO `wp_term_relationships` VALUES (22,3,0);
INSERT INTO `wp_term_relationships` VALUES (23,4,0);
INSERT INTO `wp_term_relationships` VALUES (24,4,0);
INSERT INTO `wp_term_relationships` VALUES (25,4,0);
INSERT INTO `wp_term_relationships` VALUES (26,4,0);
INSERT INTO `wp_term_relationships` VALUES (27,4,0);
INSERT INTO `wp_term_relationships` VALUES (28,4,0);
INSERT INTO `wp_term_relationships` VALUES (29,4,0);
INSERT INTO `wp_term_relationships` VALUES (30,4,0);
INSERT INTO `wp_term_relationships` VALUES (31,4,0);
INSERT INTO `wp_term_relationships` VALUES (32,4,0);
INSERT INTO `wp_term_relationships` VALUES (123,6,0);
INSERT INTO `wp_term_relationships` VALUES (124,5,0);
INSERT INTO `wp_term_relationships` VALUES (126,3,0);
INSERT INTO `wp_term_relationships` VALUES (127,3,0);
/*!40000 ALTER TABLE `wp_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_taxonomy`
--

DROP TABLE IF EXISTS `wp_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` bigint unsigned NOT NULL DEFAULT '0',
  `count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_taxonomy`
--

LOCK TABLES `wp_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;
INSERT INTO `wp_term_taxonomy` VALUES (1,1,'category','',0,1);
INSERT INTO `wp_term_taxonomy` VALUES (2,2,'wp_theme','',0,1);
INSERT INTO `wp_term_taxonomy` VALUES (3,3,'nav_menu','',0,8);
INSERT INTO `wp_term_taxonomy` VALUES (4,4,'category','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (5,5,'wp_theme','',0,1);
INSERT INTO `wp_term_taxonomy` VALUES (6,6,'category','',0,1);
/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_termmeta`
--

DROP TABLE IF EXISTS `wp_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_termmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_termmeta`
--

LOCK TABLES `wp_termmeta` WRITE;
/*!40000 ALTER TABLE `wp_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_termmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_terms`
--

DROP TABLE IF EXISTS `wp_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_terms` (
  `term_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `term_group` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_terms`
--

LOCK TABLES `wp_terms` WRITE;
/*!40000 ALTER TABLE `wp_terms` DISABLE KEYS */;
INSERT INTO `wp_terms` VALUES (1,'Uncategorized','uncategorized',0);
INSERT INTO `wp_terms` VALUES (2,'twentytwentyfive','twentytwentyfive',0);
INSERT INTO `wp_terms` VALUES (3,'Main Menu','main-menu',0);
INSERT INTO `wp_terms` VALUES (4,'Test Category','test-category',0);
INSERT INTO `wp_terms` VALUES (5,'hello-elementor','hello-elementor',0);
INSERT INTO `wp_terms` VALUES (6,'Recovery','recovery',0);
/*!40000 ALTER TABLE `wp_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_usermeta`
--

DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_usermeta`
--

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (1,1,'nickname','ilaos');
INSERT INTO `wp_usermeta` VALUES (2,1,'first_name','');
INSERT INTO `wp_usermeta` VALUES (3,1,'last_name','');
INSERT INTO `wp_usermeta` VALUES (4,1,'description','');
INSERT INTO `wp_usermeta` VALUES (5,1,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (6,1,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (7,1,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (8,1,'admin_color','fresh');
INSERT INTO `wp_usermeta` VALUES (9,1,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (10,1,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (11,1,'locale','');
INSERT INTO `wp_usermeta` VALUES (12,1,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (13,1,'wp_user_level','10');
INSERT INTO `wp_usermeta` VALUES (14,1,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (15,1,'show_welcome_panel','0');
INSERT INTO `wp_usermeta` VALUES (16,1,'session_tokens','a:7:{s:64:\"3b6be55c13256d5e5dd7226ca31786861bedccb2cb0258809b4aa2dca335dbb0\";a:4:{s:10:\"expiration\";i:1759967585;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\";s:5:\"login\";i:1759794785;}s:64:\"e67100a59882a84e34b51ee2ec7eceec4b6ecd2dc4714513d51dc355e5847e0e\";a:4:{s:10:\"expiration\";i:1760027728;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\";s:5:\"login\";i:1759854928;}s:64:\"aa5b59c84d512b6281a41884c5ad4fee387f26212bde1c3dfe3456f53c1481c8\";a:4:{s:10:\"expiration\";i:1760030451;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\";s:5:\"login\";i:1759857651;}s:64:\"cff5e4326c148c012262a10fe9293d6636ba1fe4a954446a78f1d97074ecb424\";a:4:{s:10:\"expiration\";i:1760120742;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\";s:5:\"login\";i:1759947942;}s:64:\"f0c42a5137aa30fc677c66c50bd88ca045f328d62eb42b9c9b4d4ba82057f1aa\";a:4:{s:10:\"expiration\";i:1760122498;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\";s:5:\"login\";i:1759949698;}s:64:\"72ddeb28a3e620b8bfed78f1c0b1d632867115f7f049a07cea8156b03734d566\";a:4:{s:10:\"expiration\";i:1760126860;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\";s:5:\"login\";i:1759954060;}s:64:\"8c4fa4782db32a9477642b846fe5b9652d39fac5d827357674cbf7800805454d\";a:4:{s:10:\"expiration\";i:1760129373;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36\";s:5:\"login\";i:1759956573;}}');
INSERT INTO `wp_usermeta` VALUES (17,1,'wp_dashboard_quick_press_last_post_id','131');
INSERT INTO `wp_usermeta` VALUES (18,1,'wp_persisted_preferences','a:3:{s:4:\"core\";a:3:{s:26:\"isComplementaryAreaVisible\";b:1;s:24:\"enableChoosePatternModal\";b:1;s:10:\"openPanels\";a:2:{i:0;s:11:\"post-status\";i:1;s:23:\"taxonomy-panel-category\";}}s:14:\"core/edit-post\";a:1:{s:12:\"welcomeGuide\";b:0;}s:9:\"_modified\";s:24:\"2025-07-12T00:53:35.254Z\";}');
INSERT INTO `wp_usermeta` VALUES (19,1,'managenav-menuscolumnshidden','a:5:{i:0;s:11:\"link-target\";i:1;s:11:\"css-classes\";i:2;s:3:\"xfn\";i:3;s:11:\"description\";i:4;s:15:\"title-attribute\";}');
INSERT INTO `wp_usermeta` VALUES (20,1,'metaboxhidden_nav-menus','a:1:{i:0;s:12:\"add-post_tag\";}');
INSERT INTO `wp_usermeta` VALUES (21,1,'_hello_elementor_install_notice','1');
INSERT INTO `wp_usermeta` VALUES (22,2,'nickname','test-admin');
INSERT INTO `wp_usermeta` VALUES (23,2,'first_name','');
INSERT INTO `wp_usermeta` VALUES (24,2,'last_name','');
INSERT INTO `wp_usermeta` VALUES (25,2,'description','');
INSERT INTO `wp_usermeta` VALUES (26,2,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (27,2,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (28,2,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (29,2,'admin_color','fresh');
INSERT INTO `wp_usermeta` VALUES (30,2,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (31,2,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (32,2,'locale','');
INSERT INTO `wp_usermeta` VALUES (33,2,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (34,2,'wp_user_level','10');
INSERT INTO `wp_usermeta` VALUES (35,2,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (36,3,'nickname','test-editor1');
INSERT INTO `wp_usermeta` VALUES (37,3,'first_name','');
INSERT INTO `wp_usermeta` VALUES (38,3,'last_name','');
INSERT INTO `wp_usermeta` VALUES (39,3,'description','');
INSERT INTO `wp_usermeta` VALUES (40,3,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (41,3,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (42,3,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (43,3,'admin_color','fresh');
INSERT INTO `wp_usermeta` VALUES (44,3,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (45,3,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (46,3,'locale','');
INSERT INTO `wp_usermeta` VALUES (47,3,'wp_capabilities','a:1:{s:6:\"editor\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (48,3,'wp_user_level','7');
INSERT INTO `wp_usermeta` VALUES (49,3,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (50,4,'nickname','test-editor2');
INSERT INTO `wp_usermeta` VALUES (51,4,'first_name','');
INSERT INTO `wp_usermeta` VALUES (52,4,'last_name','');
INSERT INTO `wp_usermeta` VALUES (53,4,'description','');
INSERT INTO `wp_usermeta` VALUES (54,4,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (55,4,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (56,4,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (57,4,'admin_color','fresh');
INSERT INTO `wp_usermeta` VALUES (58,4,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (59,4,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (60,4,'locale','');
INSERT INTO `wp_usermeta` VALUES (61,4,'wp_capabilities','a:1:{s:6:\"editor\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (62,4,'wp_user_level','7');
INSERT INTO `wp_usermeta` VALUES (63,4,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (64,5,'nickname','test-author');
INSERT INTO `wp_usermeta` VALUES (65,5,'first_name','');
INSERT INTO `wp_usermeta` VALUES (66,5,'last_name','');
INSERT INTO `wp_usermeta` VALUES (67,5,'description','');
INSERT INTO `wp_usermeta` VALUES (68,5,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (69,5,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (70,5,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (71,5,'admin_color','fresh');
INSERT INTO `wp_usermeta` VALUES (72,5,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (73,5,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (74,5,'locale','');
INSERT INTO `wp_usermeta` VALUES (75,5,'wp_capabilities','a:1:{s:6:\"author\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (76,5,'wp_user_level','2');
INSERT INTO `wp_usermeta` VALUES (77,5,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (78,1,'community-events-location','a:1:{s:2:\"ip\";s:9:\"127.0.0.0\";}');
INSERT INTO `wp_usermeta` VALUES (79,1,'closedpostboxes_dashboard','a:5:{i:0;s:21:\"dashboard_site_health\";i:1;s:19:\"dashboard_right_now\";i:2;s:18:\"dashboard_activity\";i:3;s:21:\"dashboard_quick_press\";i:4;s:17:\"dashboard_primary\";}');
INSERT INTO `wp_usermeta` VALUES (80,1,'metaboxhidden_dashboard','a:0:{}');
INSERT INTO `wp_usermeta` VALUES (81,1,'edit_page_per_page','99');
INSERT INTO `wp_usermeta` VALUES (82,1,'wp_user-settings','posts_list_mode=list&libraryContent=browse');
INSERT INTO `wp_usermeta` VALUES (83,1,'wp_user-settings-time','1759594574');
INSERT INTO `wp_usermeta` VALUES (84,1,'nav_menu_recently_edited','3');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_users`
--

DROP TABLE IF EXISTS `wp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_users` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_users`
--

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;
INSERT INTO `wp_users` VALUES (1,'ilaos','$wp$2y$10$6.j0dQo7hdL84/pMwxVKpebfMfX/823nljb9HR1CGQHuKdUNHg3z2','ilaos','dev-email@wpengine.local','http://my-playground.local','2025-07-08 15:41:52','',0,'ilaos');
INSERT INTO `wp_users` VALUES (2,'test-admin','$wp$2y$10$0dCRLMulujXOUh6lP4YERuEkn6KOf3mYbXQe8lYyK8IttjQ4K5Pma','test-admin','test-admin@example.com','','2025-07-11 21:56:28','',0,'test-admin');
INSERT INTO `wp_users` VALUES (3,'test-editor1','$wp$2y$10$fzV6mv7Hrnd7y39DJ9CVL.HsSkMqdB.afspPJWMctE.fUXRLO5Z1i','test-editor1','test-editor1@example.com','','2025-07-11 21:56:28','',0,'test-editor1');
INSERT INTO `wp_users` VALUES (4,'test-editor2','$wp$2y$10$5HFlVgo6CdDs.Z5E97FKDuntKSkFPtDP9oOkSPtpVskwtn2/DGVL2','test-editor2','test-editor2@example.com','','2025-07-11 21:56:28','',0,'test-editor2');
INSERT INTO `wp_users` VALUES (5,'test-author','$wp$2y$10$TGGx8chqO/U1boox6TJk0uCzBKjx3./H294OFxg3fxkNUB/CSoFLa','test-author','test-author@example.com','','2025-07-11 21:56:28','',0,'test-author');
/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_wpforms_entries`
--

DROP TABLE IF EXISTS `wp_wpforms_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_wpforms_entries` (
  `entry_id` bigint NOT NULL AUTO_INCREMENT,
  `form_id` bigint NOT NULL,
  `post_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `viewed` tinyint(1) DEFAULT '0',
  `starred` tinyint(1) DEFAULT '0',
  `fields` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `meta` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `ip_address` varchar(128) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_agent` varchar(256) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_uuid` varchar(36) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`entry_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_wpforms_entries`
--

LOCK TABLES `wp_wpforms_entries` WRITE;
/*!40000 ALTER TABLE `wp_wpforms_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_wpforms_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_wpforms_entry_fields`
--

DROP TABLE IF EXISTS `wp_wpforms_entry_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_wpforms_entry_fields` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `entry_id` bigint NOT NULL,
  `form_id` bigint NOT NULL,
  `field_id` int NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `form_id` (`form_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_wpforms_entry_fields`
--

LOCK TABLES `wp_wpforms_entry_fields` WRITE;
/*!40000 ALTER TABLE `wp_wpforms_entry_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_wpforms_entry_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_wpforms_entry_meta`
--

DROP TABLE IF EXISTS `wp_wpforms_entry_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_wpforms_entry_meta` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `entry_id` bigint NOT NULL,
  `form_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_wpforms_entry_meta`
--

LOCK TABLES `wp_wpforms_entry_meta` WRITE;
/*!40000 ALTER TABLE `wp_wpforms_entry_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_wpforms_entry_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_wpforms_tasks_meta`
--

DROP TABLE IF EXISTS `wp_wpforms_tasks_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_wpforms_tasks_meta` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_wpforms_tasks_meta`
--

LOCK TABLES `wp_wpforms_tasks_meta` WRITE;
/*!40000 ALTER TABLE `wp_wpforms_tasks_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_wpforms_tasks_meta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-08 19:32:13
