-- Status:7:47:MP_1:translations:php:1.24.4::5.1.50-community:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|history|26|3140|2011-07-04 20:05:21|MyISAM
-- TABLE|keys|5|2300|2011-07-01 17:26:28|MyISAM
-- TABLE|languages|3|5180|2011-07-01 10:13:59|MyISAM
-- TABLE|translations|2|2136|2011-07-02 16:43:37|MyISAM
-- TABLE|userrights|5|2156|2011-07-03 15:00:58|MyISAM
-- TABLE|users|2|2184|2011-07-04 20:04:45|MyISAM
-- TABLE|usersettings|4|2212|2011-07-04 00:00:58|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2011-07-04 20:05

--
-- Create Table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `key_id` smallint(5) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `oldValue` text NOT NULL,
  `newValue` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

--
-- Data for Table `history`
--

/*!40000 ALTER TABLE `history` DISABLE KEYS */;
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('1','1','2011-07-01 16:44:41','2','changed','3','otto','otto2');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('2','1','2011-07-01 16:45:40','0','created','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('3','1','2011-07-01 16:46:58','0','created','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('4','1','2011-07-01 16:47:42','0','created','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('10','1','2011-07-01 18:14:31','2','changed','1','karl','Action x');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('6','1','2011-07-01 16:59:12','10','created','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('7','1','2011-07-01 17:00:51','10','deleted','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('9','1','2011-07-01 17:06:13','0','deleted \'L_OTTO_XX\'','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('11','1','2011-07-01 18:14:31','2','changed','3','otto2','Action x');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('12','1','2011-07-02 16:39:37','1','changed','1','Aktion','Aktion22');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('13','1','2011-07-02 16:39:37','1','changed','3','Action','Action22');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('14','1','2011-07-02 16:43:35','1','changed','1','Aktion','Aktion33');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('15','1','2011-07-02 16:43:35','1','changed','3','Action','Action33');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('16','0','2011-07-03 13:26:58','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('17','1','2011-07-03 13:27:05','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('18','1','2011-07-03 13:29:47','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('19','1','2011-07-03 13:29:53','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('20','1','2011-07-03 13:30:26','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('21','1','2011-07-04 15:14:21','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('22','1','2011-07-04 20:04:03','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('23','0','2011-07-04 20:04:06','0','<i>Tester</i> failed to log in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('24','0','2011-07-04 20:04:11','0','<i>Tester</i> failed to log in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('25','0','2011-07-04 20:04:49','0','<i>Tester</i> failed to log in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('26','2','2011-07-04 20:04:55','0','logged in','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('27','2','2011-07-04 20:05:13','0','logged out','0','-','-');
INSERT INTO `history` (`id`,`user_id`,`dt`,`key_id`,`action`,`lang_id`,`oldValue`,`newValue`) VALUES ('28','2','2011-07-04 20:05:20','0','logged in','0','-','-');
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
INSERT INTO `keys` (`id`,`key`,`dt`) VALUES ('1','L_ACTION','2011-06-30 13:54:17');
INSERT INTO `keys` (`id`,`key`,`dt`) VALUES ('2','L_ACTIONX','2011-06-30 13:57:02');
INSERT INTO `keys` (`id`,`key`,`dt`) VALUES ('3','L_ACTIONXX','2011-06-30 18:31:01');
INSERT INTO `keys` (`id`,`key`,`dt`) VALUES ('8','L_OTTO_XXX','2011-07-01 16:47:42');
INSERT INTO `keys` (`id`,`key`,`dt`) VALUES ('9','L_OTTO_XXXY','2011-07-01 16:49:16');
/*!40000 ALTER TABLE `keys` ENABLE KEYS */;


--
-- Create Table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` smallint(5) unsigned NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `id_3` (`id`),
  KEY `id_4` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `languages`
--

/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` (`id`,`locale`,`name`) VALUES ('1','de','Deutsch');
INSERT INTO `languages` (`id`,`locale`,`name`) VALUES ('2','ar','Arabic');
INSERT INTO `languages` (`id`,`locale`,`name`) VALUES ('3','en','English');
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
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','1','Aktion33','2011-07-02 16:43:35');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('3','1','Action33','2011-07-02 16:43:35');
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
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('3','2','edit','0');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('4','1','edit','3');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('5','1','admin','1');
INSERT INTO `userrights` (`id`,`user_id`,`right`,`value`) VALUES ('2','2','edit','1');
/*!40000 ALTER TABLE `userrights` ENABLE KEYS */;


--
-- Create Table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Data for Table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`,`username`,`password`) VALUES ('1','DSB','47e7681b8a64e5f1e2e7a2ce7278dfad');
INSERT INTO `users` (`id`,`username`,`password`) VALUES ('2','Tester','098f6bcd4621d373cade4e832627b4f6');
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
) ENGINE=MyISAM AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;

--
-- Data for Table `usersettings`
--

/*!40000 ALTER TABLE `usersettings` DISABLE KEYS */;
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('1','1','recordsPerPage','30');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('2','1','referenceLanguage','3');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('3','1','referenceLanguage','2');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('4','1','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('5','2','referenceLanguage','1');
INSERT INTO `usersettings` (`id`,`user_id`,`setting`,`value`) VALUES ('6','2','recordsPerPage','20');
/*!40000 ALTER TABLE `usersettings` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

