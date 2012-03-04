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
