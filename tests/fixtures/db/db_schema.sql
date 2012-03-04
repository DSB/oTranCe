-- Status:11:71:MP_0:translations:php:1.24.4::5.1.50-community:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|conversions|0|2048|2012-03-04 12:15:23|MyISAM
-- TABLE|exportlog|0|2048|2012-03-04 12:15:23|MyISAM
-- TABLE|filetemplates|2|2440|2012-03-04 12:21:39|MyISAM
-- TABLE|history|9|2396|2012-03-04 12:21:39|MyISAM
-- TABLE|keys|1|3096|2012-03-04 12:15:24|MyISAM
-- TABLE|languages|3|3140|2012-03-04 12:15:24|MyISAM
-- TABLE|translations|2|2112|2012-03-04 12:15:24|MyISAM
-- TABLE|user_languages|3|2075|2012-03-04 12:15:24|MyISAM
-- TABLE|userrights|42|3012|2012-03-04 12:21:39|MyISAM
-- TABLE|users|2|3172|2012-03-04 12:15:24|MyISAM
-- TABLE|usersettings|7|2264|2012-03-04 12:15:24|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2012-03-04 12:21

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
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
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
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('1','Admin','<?php\r\n/**\r\n * This file is part of the oTranCe default installation.\r\n * Remove or change this. \r\n */\r\n$lang = array(',');\r\nreturn $lang;','\'{KEY}\' => \'{VALUE}\',','{LOCALE}/lang.php');
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('2','Admin help','<?php\r\n/**\r\n * This file is part of the oTranCe default installation.\r\n * Remove or change this. \r\n */\r\n$lang = array(',');\r\nreturn $lang;','\'{KEY}\' => \'{VALUE}\',','{LOCALE}/help_lang.php');
/*!40000 ALTER TABLE `filetemplates` ENABLE KEYS */;


--
-- Create Table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `dt` datetime NOT NULL,
  `key_id` smallint(5) unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `oldValue` text NOT NULL,
  `newValue` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Data for Table `history`
--

/*!40000 ALTER TABLE `history` DISABLE KEYS */;
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('4','2','2012-03-03 16:33:30','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('5','2','2012-03-03 16:33:45','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('6','1','2012-03-03 16:33:49','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('7','1','2012-03-03 16:34:37','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('12','1','2012-03-03 16:46:05','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('13','1','2012-03-03 20:39:02','1','created','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('14','1','2012-03-03 20:39:16','1','changed','1','-','Test eintrag');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('15','1','2012-03-03 20:39:16','1','changed','2','-','Test records');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('16','1','2012-03-04 12:17:16','0','logged in','0','-','-');
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Data for Table `keys`
--

/*!40000 ALTER TABLE `keys` DISABLE KEYS */;
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('1','L_TEST','1','2012-03-03 20:39:02');
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','1','Test eintrag','2012-03-03 20:39:16');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','1','Test records','2012-03-03 20:39:16');
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
INSERT INTO `user_languages` (`user_id`,`language_id`) VALUES ('2','1');
/*!40000 ALTER TABLE `user_languages` ENABLE KEYS */;


--
-- Create Table `userrights`
--

DROP TABLE IF EXISTS `userrights`;
CREATE TABLE `userrights` (
  `user_id` int(11) NOT NULL,
  `right` varchar(20) NOT NULL,
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
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editTemplate','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','addLanguage','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showEntries','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showDownloads','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showBrowseFiles','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showImport','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showExport','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showLog','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','showStatistics','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editConfig','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showEntries','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editLanguage','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','addUser','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editProject','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editTemplate','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','addTemplate','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editLanguage','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editUsers','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editVcs','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','addLanguage','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editUsers','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editProject','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','admin','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','addUser','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showStatistics','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showLog','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showExport','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showImport','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showBrowseFiles','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','showDownloads','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','addVar','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','addTemplate','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','editVcs','0');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','editKey','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','importEqualVar','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','deleteUsers','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','deleteLanguage','1');
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('13','1','recordsPerPage','30');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('2','1','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('10','2','recordsPerPage','10');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('11','2','interfaceLanguage','de');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('7','2','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('12','1','referenceLanguage','3');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('14','1','interfaceLanguage','en');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

