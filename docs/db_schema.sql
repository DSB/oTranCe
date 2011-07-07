-- Status:7:23:MP_0:translations:php:1.24.4::5.1.50-community:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|history|0|1024|2011-07-07 20:12:25|MyISAM
-- TABLE|keys|1|2080|2011-07-06 12:47:31|MyISAM
-- TABLE|languages|3|3152|2011-07-07 12:55:17|MyISAM
-- TABLE|translations|2|2104|2011-07-06 12:47:31|MyISAM
-- TABLE|userrights|5|2152|2011-07-06 12:47:31|MyISAM
-- TABLE|users|4|2264|2011-07-07 14:10:56|MyISAM
-- TABLE|usersettings|8|2324|2011-07-07 14:04:56|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2011-07-07 20:13

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
  `dt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

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
  `locale` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `flag_exentsion` enum('gif','jpeg','jpg','png') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locale` (`locale`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Data for Table `languages`
--

/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` (`id`,`locale`,`name`,`flag_exentsion`) VALUES ('1','de','Deutsch','gif');
INSERT INTO `languages` (`id`,`locale`,`name`,`flag_exentsion`) VALUES ('2','en','English','gif');
INSERT INTO `languages` (`id`,`locale`,`name`,`flag_exentsion`) VALUES ('3','ar','Arabic','gif');
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
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','1','Test DE','2011-07-02 16:43:35');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('3','1','TEST AR','2011-07-02 16:43:35');
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;


--
-- Create Table `userrights`
--

DROP TABLE IF EXISTS `userrights`;
CREATE TABLE `userrights` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `right` varchar(10) NOT NULL,
  `value` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Data for Table `userrights`
--

/*!40000 ALTER TABLE `userrights` DISABLE KEYS */;
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('1','1','addVar','1');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('2','1','edit','1');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('3','1','edit','3');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('4','1','admin','1');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('5','2','edit','1');
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
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('2','Tester','ad0234829205b9033196ba818f7a872b','1');
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('6','dsb4','6155a95c9e7ef4e13f939b5a7ef78af7','1');
INSERT INTO `users` (`id`,`username`,`password`,`active`) VALUES ('7','Otto','8ce4b16b22b58894aa86c421e8759df3','0');
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
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('120','1','recordsPerPage','10');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('123','1','referenceLanguage','3');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('122','1','referenceLanguage','2');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('5','2','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('6','2','recordsPerPage','20');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('121','1','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('124','6','recordsPerPage','10');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('125','6','referenceLanguage','1');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB
