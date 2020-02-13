/*
Navicat MySQL Data Transfer

Source Server         : localhost_php7
Source Server Version : 50505
Source Host           : localhost:3308
Source Database       : du_system

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-02-13 10:23:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `activation`
-- ----------------------------
DROP TABLE IF EXISTS `activation`;
CREATE TABLE `activation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trxid` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msisdn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serviceid` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `du_request` text COLLATE utf8mb4_unicode_ci,
  `du_response` text COLLATE utf8mb4_unicode_ci,
  `status_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of activation
-- ----------------------------

-- ----------------------------
-- Table structure for `charges`
-- ----------------------------
DROP TABLE IF EXISTS `charges`;
CREATE TABLE `charges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) unsigned NOT NULL,
  `billing_request` text COLLATE utf8_bin NOT NULL,
  `billing_response` text COLLATE utf8_bin NOT NULL,
  `status_id` int(11) unsigned NOT NULL,
  `charging_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `charge_sub_fk` (`subscriber_id`),
  KEY `charge_status_fk` (`status_id`),
  CONSTRAINT `charge_status_fk` FOREIGN KEY (`status_id`) REFERENCES `statues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `charge_sub_fk` FOREIGN KEY (`subscriber_id`) REFERENCES `subscribers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of charges
-- ----------------------------

-- ----------------------------
-- Table structure for `countries`
-- ----------------------------
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of countries
-- ----------------------------
INSERT INTO `countries` VALUES ('1', 'Dubi', '2020-02-12 08:26:31', '2020-02-12 08:26:31');

-- ----------------------------
-- Table structure for `messages`
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MTBody` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `MTURL` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ShortnedURL` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IsysURL` text COLLATE utf8_unicode_ci,
  `IsysResponse` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_service_id_foreign` (`service_id`),
  KEY `messages_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of messages
-- ----------------------------

-- ----------------------------
-- Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_activation_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_charges_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_countries_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_messages_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_operators_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_password_resets_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_services_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_statues_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_subscribers_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_unsubscribers_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_uploads_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114753_create_users_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114754_add_foreign_keys_to_charges_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114754_add_foreign_keys_to_subscribers_table', '0');
INSERT INTO `migrations` VALUES ('2020_02_12_114754_add_foreign_keys_to_unsubscribers_table', '0');

-- ----------------------------
-- Table structure for `operators`
-- ----------------------------
DROP TABLE IF EXISTS `operators`;
CREATE TABLE `operators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `channel` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `operators_country_id_foreign` (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of operators
-- ----------------------------
INSERT INTO `operators` VALUES ('1', 'du', 'du', '1', '2020-02-12 08:26:58', '2020-02-12 08:26:58');

-- ----------------------------
-- Table structure for `password_resets`
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for `services`
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `service` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `operator_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `size` int(11) NOT NULL DEFAULT '800',
  `ExURL` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'http://ivas.mobi',
  `service_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sender_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_operator_id_foreign` (`operator_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of services
-- ----------------------------
INSERT INTO `services` VALUES ('1', 'flatter_daily', 'flatter_daily', 'en', 'SMS', '1', '2020-02-12 08:29:40', '2020-02-12 08:29:40', '2048', 'http://filters.digizone.com.kw', '1', '4971');
INSERT INTO `services` VALUES ('2', 'flatter_weekly', 'flatter_weekly', 'en', 'SMS', '1', '2020-02-12 08:30:07', '2020-02-12 08:30:07', '2048', 'https://filters.digizone.com.kw', '2', '4971');

-- ----------------------------
-- Table structure for `statues`
-- ----------------------------
DROP TABLE IF EXISTS `statues`;
CREATE TABLE `statues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of statues
-- ----------------------------

-- ----------------------------
-- Table structure for `subscribers`
-- ----------------------------
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activation_id` bigint(20) unsigned NOT NULL,
  `next_charging_date` date NOT NULL DEFAULT '0000-00-00',
  `subscribe_date` date NOT NULL,
  `final_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0=not active , 1=active',
  `charging_cron` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0=  cronfail, 1= run success',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_act_fk` (`activation_id`),
  CONSTRAINT `sub_act_fk` FOREIGN KEY (`activation_id`) REFERENCES `activation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of subscribers
-- ----------------------------

-- ----------------------------
-- Table structure for `unsubscribers`
-- ----------------------------
DROP TABLE IF EXISTS `unsubscribers`;
CREATE TABLE `unsubscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activation_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unsub_act_fk` (`activation_id`),
  CONSTRAINT `unsub_act_fk` FOREIGN KEY (`activation_id`) REFERENCES `activation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of unsubscribers
-- ----------------------------

-- ----------------------------
-- Table structure for `uploads`
-- ----------------------------
DROP TABLE IF EXISTS `uploads`;
CREATE TABLE `uploads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fid` bigint(20) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of uploads
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Emad Mohamed', 'emad@ivas.com.eg', '$2y$10$u2evAW530miwgUb2jcXkTuqIGswxnSQ3DSmX1Ji5rtO3Tx.MtVcX2', '1', 'xe9i7NuxobpLMhlxqLTlUfnEnH0ngZAfVwcTWj6Srp2DI7WHmrBOMy7vhblk', '2015-07-26 20:48:09', '2016-02-17 09:46:55');
INSERT INTO `users` VALUES ('11', 'sherif', 'sherif.mohamed@ivas.com.eg', '$2y$10$sK8Rb1QqU1okO.sGDtRhhOj.0J7/zbSR4Li4IRdQ4JCadf7.V5Yg2', '0', null, '2020-02-12 08:34:14', '2020-02-12 08:34:14');
