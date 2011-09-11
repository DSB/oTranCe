-- Status:11:36:MP_0:translations:php:1.24.4::5.1.50-community-log:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|conversions|0|2048|2011-09-04 20:07:12|MyISAM
-- TABLE|exportlog|0|2048|2011-09-11 13:34:43|MyISAM
-- TABLE|filetemplates|1|2828|2011-09-04 20:23:03|MyISAM
-- TABLE|history|2|2120|2011-09-11 13:35:44|MyISAM
-- TABLE|keys|0|1024|2011-09-11 13:34:54|MyISAM
-- TABLE|languages|3|3160|2011-09-08 21:22:01|MyISAM
-- TABLE|translations|0|1024|2011-09-11 13:35:01|MyISAM
-- TABLE|user_languages|4|2129|2011-09-11 13:17:30|MyISAM
-- TABLE|userrights|22|2544|2011-09-11 13:17:30|MyISAM
-- TABLE|users|2|2196|2011-09-11 13:17:30|MyISAM
-- TABLE|usersettings|2|2336|2011-09-11 13:17:30|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2011-09-11 13:36

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Data for Table `filetemplates`
--

/*!40000 ALTER TABLE `filetemplates` DISABLE KEYS */;
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('1','OXID','<?php\r\n/**\r\n * This file is part of MySQLDumper released under the GNU/GPL 2 license\r\n * http://www.mysqldumper.net\r\n *\r\n * @package       MySQLDumper\r\n * @subpackage    Language\r\n * @version       $Rev: 1291 $\r\n * @author        $Author: dsb $\r\n */\r\n$lang=array(',');\r\nreturn $lang;','\"{KEY}\" => \"{VALUE}\"','languages/{LOCALE}.php');
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

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
  KEY `tpl_assign` (`template_id`)
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
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`,`is_fallback`) VALUES ('1','1','de','Deutsch','gif','0');
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`,`is_fallback`) VALUES ('2','1','en','English','gif','1');
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`,`is_fallback`) VALUES ('3','0','ar','Arabic','gif','0');
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
  PRIMARY KEY (`lang_id`,`key_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `translations`
--

/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;


--
-- Create Table `user_languages`
--

DROP TABLE IF EXISTS `user_languages`;
CREATE TABLE `user_languages` (
  `user_id` int(11) unsigned NOT NULL,
  `language_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `user_languages`
--

/*!40000 ALTER TABLE `user_languages` DISABLE KEYS */;
INSERT INTO `user_languages` (`user_id`,`language_id`) VALUES ('1','1');
INSERT INTO `user_languages` (`user_id`,`language_id`) VALUES ('1','2');
INSERT INTO `user_languages` (`user_id`,`language_id`) VALUES ('1','3');
INSERT INTO `user_languages` (`user_id`,`language_id`) VALUES ('2','1');
/*!40000 ALTER TABLE `user_languages` ENABLE KEYS */;


--
-- Create Table `userrights`
--

DROP TABLE IF EXISTS `userrights`;
CREATE TABLE `userrights` (
  `user_id` int(11) NOT NULL,
  `right` varchar(10) NOT NULL,
  `value` smallint(5) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`right`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `userrights`
--

/*!40000 ALTER TABLE `userrights` DISABLE KEYS */;
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','admin','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','addVar','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','export','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','createFile','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editConfig','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','addVar','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','admin','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','export','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','createFile','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editConfig','1');
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Data for Table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('1','Admin','21232f297a57a5a743894a0e4a801fc3','1');
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('2','tester','098f6bcd4621d373cade4e832627b4f6','1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


--
-- Create Table `usersettings`
--

DROP TABLE IF EXISTS `usersettings`;
CREATE TABLE `usersettings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `setting` varchar(20) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('1','1','recordsPerPage','30');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('2','1','referenceLanguage','1');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

