-- Status:10:37:MP_0:otc:php:1.24.4::5.1.53-log:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|conversions|1|6304|2011-08-11 20:00:49|MyISAM
-- TABLE|exportlog|0|2048|2011-08-17 23:10:27|MyISAM
-- TABLE|filetemplates|2|2828|2011-08-17 23:07:36|MyISAM
-- TABLE|history|0|1024|2011-08-17 23:10:22|MyISAM
-- TABLE|keys|3|3160|2011-08-16 21:57:49|MyISAM
-- TABLE|languages|3|3140|2011-08-17 23:01:33|MyISAM
-- TABLE|translations|9|2264|2011-08-16 21:57:39|MyISAM
-- TABLE|userrights|10|2252|2011-08-11 20:00:49|MyISAM
-- TABLE|users|2|2148|2011-08-11 20:00:49|MyISAM
-- TABLE|usersettings|7|2304|2011-08-17 21:21:29|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2011-08-17 23:10

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
INSERT INTO `conversions` (`text`,`id`) VALUES ('<?php\n/**\n *    This file is part of OXID eShop Community Edition.\n *\n *    OXID eShop Community Edition is free software: you can redistribute it and/or modify\n *    it under the terms of the GNU General Public License as published by\n *    the Free Software Foundation, either version 3 of the License, or\n *    (at your option) any later version.\n *\n *    OXID eShop Community Edition is distributed in the hope that it will be useful,\n *    but WITHOUT ANY WARRANTY; without even the implied warranty of\n *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n *    GNU General Public License for more details.\n *\n *    You should have received a copy of the GNU General Public License\n *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.\n *\n * @link      http://www.oxid-esales.com\n * @package   lang\n * @copyright (C) OXID eSales AG 2003-2011\n * @version OXID eShop CE\n * @version   SVN: $Id: lang.php 36520 2011-06-23 09:40:53Z juergen.busch $\n */\n\n$sLangName  = \'Deutsch\';\n// -------------------------------\n// RESOURCE IDENTITFIER = STRING\n// -------------------------------\n$aLang = array(\n\n\'charset\'                                                  => \'ISO-8859-15\',\n\'fullDateFormat\'                                           => \'d.m.Y H:i:s\',\n\'simpleDateFormat\'                                         => \'d.m.Y\',\n\n\'GENERAL_ACTIVE\'                                           => \'Aktiv\',\n\'GENERAL_ACTIVFROMTILL\'                                    => \'Oder aktiv\',\n\'GENERAL_OR\'                                               => \'Oder\',\n\'GENERAL_ACTIVTITLE\'                                       => \'A\',\n\'GENERAL_ADMIN_TITLE\'                                      => \'[OXID eShop Administrationsbereich]\',\n\'GENERAL_ADMIN_TITLE_1\'                                    => \'[OXID eShop Administrationsbereich]\',\n\'GENERAL_AJAX_ASSIGNALL\'                                   => \'Alle zuordnen\',\n\'GENERAL_AJAX_DESCRIPTION\'                                 => \'Ziehen Sie die Elemente zwischen den Listen hin und her, um die Elemente zuzuordnen\',\n\'GENERAL_AJAX_UNASSIGNALL\'                                 => \'Alle Zuordnungen l','brh3dmol5a8j8tj40qe7825lb0');
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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- Data for Table `filetemplates`
--

/*!40000 ALTER TABLE `filetemplates` DISABLE KEYS */;
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('1','ÖXID','<?php\r\n/**\r\n * This file is part of MySQLDumper released under the GNU/GPL 2 license\r\n * http://www.mysqldumper.net\r\n *\r\n * @package       MySQLDumper\r\n * @subpackage    Language\r\n * @version       $Rev: 1291 $\r\n * @author        $Author: dsb $\r\n */\r\n$lang=array(',');\r\nreturn $lang;','                                                                                             \"{KEY}\" => \"{VALUE}\"','languages/{LOCALE}.php');
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
  KEY `tpl_assign` (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Data for Table `keys`
--

/*!40000 ALTER TABLE `keys` DISABLE KEYS */;
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('1','L_TEST_ENTRY','2','2011-06-30 13:54:17');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('12','L_OTTO','1','2011-07-23 11:51:43');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('13','L_OTTO_HERZ','2','2011-07-23 11:52:44');
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
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','1','Aktion','2011-08-16 21:57:39');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('3','1','???','2011-07-23 11:51:33');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','1','Action','2011-08-16 21:57:39');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('3','12','','2011-07-24 19:01:53');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','12','','2011-07-24 19:01:53');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','12','test','2011-07-24 19:01:53');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('3','13','','2011-07-24 19:03:07');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','13','Herz','2011-07-24 19:03:07');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','13','testr2','2011-07-24 19:03:07');
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
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=156 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('151','1','recordsPerPage','10');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('154','1','referenceLanguage','2');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('5','2','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('6','2','recordsPerPage','20');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('153','1','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('152','1','referenceLanguage','3');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('155','1','vcsCredentials','ZnB3G7nAS_YhlAFHIukbdhqyqbjfGTOvSksfbMyW0KVc-');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

