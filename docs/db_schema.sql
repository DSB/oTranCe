-- Status:11:72:MP_0:translations:php:1.24.4::5.1.50-community:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|conversions|0|2048|2012-03-03 16:24:39|MyISAM
-- TABLE|exportlog|0|2048|2012-03-03 16:24:39|MyISAM
-- TABLE|filetemplates|2|5456|2012-03-03 16:48:10|MyISAM
-- TABLE|history|12|2480|2012-03-03 16:46:05|MyISAM
-- TABLE|keys|0|1024|2012-03-03 16:24:39|MyISAM
-- TABLE|languages|3|3144|2012-03-03 17:49:11|MyISAM
-- TABLE|translations|0|1024|2012-03-03 16:24:39|MyISAM
-- TABLE|user_languages|3|2075|2012-03-03 16:52:42|MyISAM
-- TABLE|userrights|43|3032|2012-03-03 16:49:04|MyISAM
-- TABLE|users|2|3172|2012-03-03 16:51:05|MyISAM
-- TABLE|usersettings|7|2264|2012-03-03 16:49:04|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2012-03-03 17:49

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
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('1','Admin','<?php\r\n/**\r\n *    This file is part of OXID eShop Community Edition.\r\n *\r\n *    OXID eShop Community Edition is free software: you can redistribute it and/or modify\r\n *    it under the terms of the GNU General Public License as published by\r\n *    the Free Software Foundation, either version 3 of the License, or\r\n *    (at your option) any later version.\r\n *\r\n *    OXID eShop Community Edition is distributed in the hope that it will be useful,\r\n *    but WITHOUT ANY WARRANTY; without even the implied warranty of\r\n *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\r\n *    GNU General Public License for more details.\r\n *\r\n *    You should have received a copy of the GNU General Public License\r\n *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.\r\n *\r\n * @link      http://www.oxid-esales.com\r\n * @package   lang\r\n * @copyright (C) OXID eSales AG 2003-2011\r\n * @version OXID eShop CE\r\n * @version   SVN: $Id: lang.php 38692 2011-09-09 08:32:48Z arvydas.vapsva $\r\n */\r\n\r\n/*\r\n * Capitalisation in this document:\r\n * First letter is always capitalized\r\n * All nouns are capitalized\r\n */\r\n$sLangName  = \'{LANG_NAME}\';\r\n\r\n// -------------------------------\r\n// RESOURCE IDENTITFIER = STRING\r\n// -------------------------------\r\n$aLang = array(',');','\'{KEY}\' => \'{VALUE}\',','out/admin/{LOCALE}/lang.php');
INSERT INTO `filetemplates` (`id`,`name`,`header`,`footer`,`content`,`filename`) VALUES ('2','Admin help','<?php\r\n/**\r\n *    This file is part of OXID eShop Community Edition.\r\n *\r\n *    OXID eShop Community Edition is free software: you can redistribute it and/or modify\r\n *    it under the terms of the GNU General Public License as published by\r\n *    the Free Software Foundation, either version 3 of the License, or\r\n *    (at your option) any later version.\r\n *\r\n *    OXID eShop Community Edition is distributed in the hope that it will be useful,\r\n *    but WITHOUT ANY WARRANTY; without even the implied warranty of\r\n *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\r\n *    GNU General Public License for more details.\r\n *\r\n *    You should have received a copy of the GNU General Public License\r\n *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.\r\n *\r\n * @link      http://www.oxid-esales.com\r\n * @package   lang\r\n * @copyright (C) OXID eSales AG 2003-2011\r\n * @version OXID eShop CE\r\n * @version   SVN: $Id: help_lang.php 38434 2011-08-25 09:21:54Z juergen.busch $\r\n */\r\n\r\n/**\r\n * In this file, the content for help popups is stored:\r\n *\r\n * Syntax for identifier: HELP_TABNAME_INPUTNAME, e.g. HELP_SHOP_CONFIG_BIDIRECTCROSS.\r\n * !!!The INPUTNAME is same as in lang.php for avoiding even more different Identifiers.!!!\r\n * In some cases, in lang.php GENERAL_ identifiers are used. In this file, always the tab name is used.\r\n *\r\n *\r\n * HTML Tags for markup (same as in online manual):\r\n * <span class=\'navipath_or_inputname\'>...</span> for names of input fields, selectlists and Buttons, e.g. <span class=\'navipath_or_inputname\'>Active</span>\r\n * <span class=\'userinput_or_code\'>...</span> for input in input fields (also options in selectlists) and code\r\n * <span class=\'filename_filepath_or_italic\'>...</span> for filenames, filepaths and other italic stuff\r\n * <span class=\'warning_or_important_hint\'>...</span> for warning and important things\r\n * <ul> and <li> for lists\r\n */\r\n\r\n$aLang =  array(',');','\'{KEY}\' => \'{VALUE}\',','out/admin/{LOCALE}/help_lang.php');
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
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('1','1','2012-03-03 16:33:05','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('2','2','2012-03-03 16:33:11','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('3','2','2012-03-03 16:33:26','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('4','2','2012-03-03 16:33:30','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('5','2','2012-03-03 16:33:45','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('6','1','2012-03-03 16:33:49','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('7','1','2012-03-03 16:34:37','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('8','3','2012-03-03 16:34:42','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('9','3','2012-03-03 16:45:04','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('10','2','2012-03-03 16:45:09','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('11','2','2012-03-03 16:46:01','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('12','1','2012-03-03 16:46:05','0','logged in','0','-','-');
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
INSERT INTO `userrights` (`user_id`,`right`,`value`) VALUES ('3','admin','1');
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

