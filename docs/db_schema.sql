-- Status:8:37:MP_0:translations:php:1.24.4::5.1.50-community:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|filetemplates|2|2764|2011-07-09 17:49:16|MyISAM
-- TABLE|history|12|2520|2011-07-09 14:17:51|MyISAM
-- TABLE|keys|1|2104|2011-07-09 14:19:16|MyISAM
-- TABLE|languages|3|3144|2011-07-09 16:49:16|MyISAM
-- TABLE|translations|3|2124|2011-07-08 13:30:27|MyISAM
-- TABLE|userrights|9|2408|2011-07-09 14:49:16|MyISAM
-- TABLE|users|2|2264|2011-07-09 14:49:16|MyISAM
-- TABLE|usersettings|5|2324|2011-07-09 14:49:16|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2011-07-09 18:07

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
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('1','PHP language array','<?php\r\n/**\r\n * This file is part of MySQLDumper released under the GNU/GPL 2 license\r\n  * http://www.mysqldumper.net\r\n *\r\n * @package       MySQLDumper\r\n * @subpackage    Language\r\n * @version       $Rev: 1291 $\r\n * @author        $Author: dsb $\r\n */\r\n$lang=array(\r\n',');\r\nreturn $lang;\r\n','\"{KEY}\" => \"{VALUE}\"\r\n','languages/{LOCALE}.php');
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('2','PHP language array','<?php\r\n/**\r\n * This file is part of MySQLDumper released under the GNU/GPL 2 license\r\n  * http://www.mysqldumper.net\r\n *\r\n * @package       MySQLDumper\r\n * @subpackage    Language\r\n * @version       $Rev: 1291 $\r\n * @author        $Author: dsb $\r\n */\r\n','return $lang;\r\n','$lang[\"{KEY}\"]= \"{VALUE}\";\r\n','languages/{LOCALE}/lang.php');
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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Data for Table `history`
--

/*!40000 ALTER TABLE `history` DISABLE KEYS */;
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('1','1','2011-07-07 23:22:51','1','changed','1','Test DE','Test DE Neu');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('2','1','2011-07-08 13:05:53','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('3','2','2011-07-08 13:05:57','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('4','2','2011-07-08 13:06:23','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('5','1','2011-07-08 13:06:27','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('6','1','2011-07-08 13:07:18','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('7','2','2011-07-08 13:07:22','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('8','2','2011-07-08 13:07:27','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('9','1','2011-07-08 13:07:31','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('10','1','2011-07-08 13:20:19','1','changed','1','Test DE Neu','Aktion');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('11','1','2011-07-09 14:17:10','11','created','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('12','1','2011-07-09 14:17:47','0','deleted \'L_OTTO\'','0','-','-');
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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8

--
-- Data for Table `keys`
--

/*!40000 ALTER TABLE `keys` DISABLE KEYS */;
INSERT INTO `keys` (`id`,`key`,`dt`) VALUES ('1','L_TEST_ENTRY','2011-06-30 13:54:17');
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
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`) VALUES ('1','1','de','Deutsch','gif');
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`) VALUES ('2','1','en','English','gif');
INSERT INTO `languages` (`id`,`active`,`locale`,`name`,`flag_extension`) VALUES ('3','0','ar','Arabic','gif');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;


--
-- Create Table `translations`
--

DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
  `lang_id` smallint(5) unsigned NOT NULL,
  `key_id` smallint(5) unsigned NOT NULL,
  `text` text NOT NULL,
  `dt` datetime NOT NULL,
  PRIMARY KEY (`lang_id`,`key_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `translations`
--

/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','1','Aktion','2011-07-08 13:22:07');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('3','1','TEST AR','2011-07-08 13:22:07');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','1','','2011-07-08 13:22:07');
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
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','edit','3');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','admin','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','addVar','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','export','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','edit','1');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','edit','2');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('1','edit','2');
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('2','export','1');
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
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('142','1','recordsPerPage','10');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('5','2','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('6','2','recordsPerPage','20');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('143','1','referenceLanguage','3');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('144','1','referenceLanguage','1');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

