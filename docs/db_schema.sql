-- Status:10:22:MP_0:translations:php:1.24.4::5.1.50-community-log:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|conversions|0|267356|2011-08-08 00:38:30|MyISAM
-- TABLE|exportlog|0|2048|2011-08-11 20:22:48|MyISAM
-- TABLE|filetemplates|2|2748|2011-07-31 22:51:23|MyISAM
-- TABLE|history|0|1024|2011-08-11 20:22:40|MyISAM
-- TABLE|keys|0|4096|2011-08-11 20:22:42|MyISAM
-- TABLE|languages|3|3160|2011-08-07 21:08:30|MyISAM
-- TABLE|translations|0|1024|2011-08-11 20:22:32|MyISAM
-- TABLE|userrights|9|1228|2011-08-07 18:08:30|MyISAM
-- TABLE|users|2|2148|2011-07-31 22:51:27|MyISAM
-- TABLE|usersettings|6|2264|2011-08-07 21:38:30|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2011-08-11 20:23

--
-- Create Table `conversions`
--

DROP TABLE IF EXISTS `conversions`;
CREATE TABLE `conversions` (
  `text` longtext NOT NULL,
  `id` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `conversions`
--

/*!40000 ALTER TABLE `conversions` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversions` ENABLE KEYS */;


--
-- Create Table `exportlog`
--

DROP TABLE IF EXISTS `exportlog`;
CREATE TABLE `exportlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `export_id` varchar(128) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `export_id` (`export_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `exportlog`
--

/*!40000 ALTER TABLE `exportlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `exportlog` ENABLE KEYS */;


--
-- Create Table `filetemplates`
--

DROP TABLE IF EXISTS `filetemplates`;
CREATE TABLE `filetemplates` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `content` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Data for Table `filetemplates`
--

/*!40000 ALTER TABLE `filetemplates` DISABLE KEYS */;
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('1','PHP language array','<?php\r\n/**\r\n * This file is part of MySQLDumper released under the GNU/GPL 2 license\r\n * http://www.mysqldumper.net\r\n *\r\n * @package       MySQLDumper\r\n * @subpackage    Language\r\n * @version       $Rev: 1291 $\r\n * @author        $Author: dsb $\r\n */\r\n$lang=array(',');\r\nreturn $lang;','\"{KEY}\" => \"{VALUE}\"','languages/{LOCALE}.php');
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('2','PHP language array','<?php\r\n/**\r\n * This file is part of MySQLDumper released under the GNU/GPL 2 license\r\n * http://www.mysqldumper.net\r\n *\r\n * @package       MySQLDumper\r\n * @subpackage    Language\r\n * @version       $Rev: 1291 $\r\n * @author        $Author: dsb $\r\n */','return $lang;','$lang[\"{KEY}\"] = \"{VALUE}\";','languages/{LOCALE}/lang.php');
/*!40000 ALTER TABLE `filetemplates` ENABLE KEYS */;


--
-- Create Table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `key_id` smallint(5) unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `oldValue` text NOT NULL,
  `newValue` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `history`
--

/*!40000 ALTER TABLE `history` DISABLE KEYS */;
/*!40000 ALTER TABLE `history` ENABLE KEYS */;


--
-- Create Table `keys`
--

DROP TABLE IF EXISTS `keys`;
CREATE TABLE `keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(200) NOT NULL,
  `template_id` tinyint(4) NOT NULL,
  `dt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `keys`
--

/*!40000 ALTER TABLE `keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `keys` ENABLE KEYS */;


--
-- Create Table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `locale` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `flag_extension` varchar(255) NOT NULL,
  `is_fallback` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `locale` (`locale`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Data for Table `languages`
--

/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`,`is_fallback`) VALUES ('1','1','de','Deutsch','gif','1');
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`,`is_fallback`) VALUES ('2','1','en','English','gif','0');
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`,`is_fallback`) VALUES ('3','1','ar','Arabic','gif','0');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;


--
-- Create Table `translations`
--

DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
  `lang_id` smallint(5) unsigned NOT NULL,
  `key_id` smallint(5) unsigned NOT NULL,
  `text` longtext NOT NULL,
  `dt` datetime NOT NULL,
  PRIMARY KEY (`lang_id`,`key_id`),
  KEY `key_id` (`key_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `translations`
--

/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;


--
-- Create Table `userrights`
--

DROP TABLE IF EXISTS `userrights`;
CREATE TABLE `userrights` (
  `user_id` int(11) NOT NULL,
  `right` varchar(10) NOT NULL,
  `value` smallint(5) unsigned NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `userrights`
--

/*!40000 ALTER TABLE `userrights` DISABLE KEYS */;
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','edit','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','admin','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','addVar','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','export','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','edit','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','edit','2');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','edit','2');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','export','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','createFile','1');
/*!40000 ALTER TABLE `userrights` ENABLE KEYS */;


--
-- Create Table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Data for Table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('1','Admin','21232f297a57a5a743894a0e4a801fc3','1');
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('2','Tester','098f6bcd4621d373cade4e832627b4f6','1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


--
-- Create Table `usersettings`
--

DROP TABLE IF EXISTS `usersettings`;
CREATE TABLE `usersettings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `setting` varchar(20) NOT NULL,
  `value` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('177','1','recordsPerPage','30');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('5','2','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('6','2','recordsPerPage','20');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('180','1','referenceLanguage','2');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('179','1','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('178','1','referenceLanguage','3');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB
