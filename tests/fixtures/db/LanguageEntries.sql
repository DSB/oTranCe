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
) ENGINE=MyISAM AUTO_INCREMENT=756 DEFAULT CHARSET=utf8;

--
-- Data for Table `keys`
--

/*!40000 ALTER TABLE `keys` DISABLE KEYS */;
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('1','L_TEST','1','2012-03-03 20:39:02');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('2','L_ACTION','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('3','L_ACTIVATED','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('4','L_ACTUALLY_INSERTED_RECORDS','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('5','L_ACTUALLY_INSERTED_RECORDS_OF','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('6','L_ADD','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('7','L_ADDED','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('8','L_ADD_DB_MANUALLY','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('9','L_ADD_RECIPIENT','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('10','L_ALL','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('11','L_ANALYZE','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('12','L_ANALYZING_TABLE','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('13','L_ASKDBCOPY','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('14','L_ASKDBDELETE','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('15','L_ASKDBEMPTY','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('16','L_ASKDELETEFIELD','1','2012-03-04 22:28:10');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('17','L_ASKDELETERECORD','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('18','L_ASKDELETETABLE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('19','L_ASKTABLEEMPTY','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('20','L_ASKTABLEEMPTYKEYS','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('21','L_ATTACHED_AS_FILE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('22','L_ATTACH_BACKUP','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('23','L_AUTHENTICATE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('24','L_AUTHORIZE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('25','L_AUTODELETE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('26','L_BACK','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('27','L_BACKUPFILESANZAHL','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('28','L_BACKUPS','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('29','L_BACKUP_DBS','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('30','L_BACKUP_TABLE_DONE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('31','L_BACK_TO_OVERVIEW','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('32','L_CALL','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('33','L_CANCEL','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('34','L_CANT_CREATE_DIR','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('35','L_CHANGE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('36','L_CHANGEDIR','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('37','L_CHANGEDIRERROR','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('38','L_CHARSET','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('39','L_CHARSETS','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('40','L_CHECK','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('41','L_CHECK_DIRS','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('42','L_CHOOSE_CHARSET','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('43','L_CHOOSE_DB','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('44','L_CLEAR_DATABASE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('45','L_CLOSE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('46','L_COLLATION','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('47','L_COMMAND','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('48','L_COMMAND_AFTER_BACKUP','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('49','L_COMMAND_BEFORE_BACKUP','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('50','L_COMMENT','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('51','L_COMPRESSED','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('52','L_CONFBASIC','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('53','L_CONFIG','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('54','L_CONFIGFILE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('55','L_CONFIGFILES','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('56','L_CONFIGURATIONS','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('57','L_CONFIG_AUTODELETE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('58','L_CONFIG_CRONPERL','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('59','L_CONFIG_EMAIL','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('60','L_CONFIG_FTP','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('61','L_CONFIG_HEADLINE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('62','L_CONFIG_INTERFACE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('63','L_CONFIG_LOADED','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('64','L_CONFIRM_CONFIGFILE_DELETE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('65','L_CONFIRM_DELETE_FILE','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('66','L_CONFIRM_DELETE_TABLES','1','2012-03-04 22:28:11');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('67','L_CONFIRM_DROP_DATABASES','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('68','L_CONFIRM_RECIPIENT_DELETE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('69','L_CONFIRM_TRUNCATE_DATABASES','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('70','L_CONFIRM_TRUNCATE_TABLES','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('71','L_CONNECT','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('72','L_CONNECTIONPARS','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('73','L_CONNECTTOMYSQL','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('74','L_CONTINUE_MULTIPART_RESTORE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('75','L_CONVERTED_FILES','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('76','L_CONVERTER','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('77','L_CONVERTING','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('78','L_CONVERT_FILE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('79','L_CONVERT_FILENAME','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('80','L_CONVERT_FILEREAD','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('81','L_CONVERT_FINISHED','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('82','L_CONVERT_START','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('83','L_CONVERT_TITLE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('84','L_CONVERT_WRONG_PARAMETERS','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('85','L_CREATE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('86','L_CREATED','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('87','L_CREATEDIRS','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('88','L_CREATE_AUTOINDEX','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('89','L_CREATE_CONFIGFILE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('90','L_CREATE_DATABASE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('91','L_CREATE_TABLE_SAVED','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('92','L_CREDITS','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('93','L_CRONSCRIPT','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('94','L_CRON_COMMENT','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('95','L_CRON_COMPLETELOG','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('96','L_CRON_EXECPATH','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('97','L_CRON_EXTENDER','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('98','L_CRON_PRINTOUT','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('99','L_CSVOPTIONS','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('100','L_CSV_EOL','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('101','L_CSV_ERRORCREATETABLE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('102','L_CSV_FIELDCOUNT_NOMATCH','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('103','L_CSV_FIELDSENCLOSED','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('104','L_CSV_FIELDSEPERATE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('105','L_CSV_FIELDSESCAPE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('106','L_CSV_FIELDSLINES','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('107','L_CSV_FILEOPEN','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('108','L_CSV_NAMEFIRSTLINE','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('109','L_CSV_NODATA','1','2012-03-04 22:28:12');
INSERT INTO `keys` (`id`,`key`,`template_id`,`dt`) VALUES ('110','L_CSV_NULL','1','2012-03-04 22:28:12');
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
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','2','','2012-03-04 22:29:38');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','3','','2012-03-04 22:29:44');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','4','','2012-03-04 22:29:50');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','5','Es wurden bisher <b>%s</b> von <b>%s</b> Datensätzen erfolgreich eingetragen.','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','6','Hinzufügen','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','7','hinzugefügt','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','8','Datenbank manuell hinzufügen','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','9','Empfänger hinzufügen','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','10','alle','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','11','Analysiere','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','12','Momentan werden Daten der Tabelle \'<b>%s</b>\' analysiert.','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','13','Soll der Inhalt der Datenbank `%s` in die Datenbank `%s` kopiert werden?','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','14','Soll die Datenbank `%s` samt Inhalt wirklich gelöscht werden?','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','15','Soll die Datenbank `%s` wirklich geleert werden?','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','16','Soll das Feld gelöscht werden?','2012-03-04 22:28:10');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','17','Soll der Datensatz gelöscht werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','18','Soll die Tabelle `%s` gelöscht werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','19','Soll die Tabelle `%s` geleert werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','20','Sollen die Tabelle `%s` geleert und die Indizes zurückgesetzt werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','21','als Datei angehängt','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','22','Backup anhängen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','23','Anmeldeinformationen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','24','Autorisieren','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','25','Automatisches Löschen der Backups','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','26','zurück','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','27','Im Backup-Verzeichnis befinden sich','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','28','Sicherungsdateien','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','29','zu sichernde DBs','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','30','Sicherung der Tabelle `%s` abgeschlossen. %s Datensätze wurden gespeichert.','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','31','Datenbank-Übersicht','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','32','Aufruf','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','33','Abbruch','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','34','Ich konnte das Verzeichnis \'%s\' nicht anlegen. Bitte erstellen Sie es mit Ihrem FTP-Programm.','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','35','Ändern','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','36','Wechsle in das Verzeichnis','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','37','Es konnte nicht in das Verzeichnis gewechselt werden!','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','38','Zeichensatz','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','39','Zeichensätze','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','40','Überprüfe','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','41','Verzeichnisse überprüfen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','42','Leider konnte nicht automatisch ermittelt werden mit welchem Zeichensatz diese Backupdatei seinerzeit angelegt wurde. <br /><br />Sie müssen die Kodierung, in der Zeichenketten in dieser Datei vorliegen, manuell angeben.<br /><br />Danach stellt MySQLDumper die Verbindungskennung zum MySQL-Server auf den ausgewählten Zeichensatz und beginnt mit der Wiederherstellung der Daten.<br /><br />Sollten Sie nach der Wiederherstellung Probleme mit Sonderzeichen entdecken, so können Sie versuchen, das Backup mit einer anderen Zeichensatzauswahl wiederherzustellen.<br /><br />Viel Glück. ;)<br /><br /><br />','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','43','Datenbank wählen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','44','Datenbank leeren','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','45','Schließen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','46','Sortierung','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','47','Befehl','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','48','Befehl nach Backup','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','49','Befehl vor Backup','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','50','Kommentar','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','51','komprimiert (gz)','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','52','Grundeinstellungen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','53','Konfiguration','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','54','Konfigurationsdatei','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','55','Konfigurationsdateien','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','56','Einstellungen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','57','Automatisches Löschen','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','58','Crondump-Einstellungen für das Perlscript','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','59','E-Mail-Benachrichtigung','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','60','FTP-Transfer der Backup-Datei','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','61','Konfiguration','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','62','Oberfläche','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','63','Die Konfiguration \"%s\" wurde erfolgreich geladen.','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','64','Soll die Konfigurationsdatei %s wirklich gelöscht werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','65','Soll die Datei \'%s\' wirklich gelöscht werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','66','Sollen die gewählten Tabellen wirklich gelöscht werden?','2012-03-04 22:28:11');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','67','Soll/en die gewählte/n Datenbank/en wirklich gelöscht werden?<br /><br />Achtung: alle Daten gehen unwiderruflich verloren! Legen Sie sicherheitshalber vorher eine Sicherung der Daten an.','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','68','Soll der Empfänger \"%s\" wirklich entfernt werden?','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','69','Soll/en die gewählte/n Datenbank/en wirklich geleert werden?<br /><br />Achtung: alle Tabellen gehen unwiderruflich verloren! Legen Sie sicherheitshalber vorher eine Sicherung der Daten an.','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','70','Sollen die gewählten Tabellen wirklich geleert werden?','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','71','verbinden','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','72','Verbindungsparameter','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','73','zu MySQL verbinden','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','74','Multipart-Wiederherstellung mit nächster Datei \'%s\' fortfahren .','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','75','Konvertierte Dateien','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','76','Backup-Konverter','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','77','Konvertierung','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','78','zu konvertierende Datei','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','79','Name der Zieldatei (ohne Endung)','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','80','Datei \'%s\' wird eingelesen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','81','Konvertierung abgeschlossen, \'%s\' wurde erzeugt.','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','82','Konvertierung starten','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','83','Konvertiere Dump ins MSD-Format','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','84','Falsche Parameter! Konvertierung ist nicht möglich.','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','85','anlegen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','86','Erstellt','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','87','erstelle Verzeichnisse','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','88','Autoindex erzeugen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','89','Eine neue Konfigurationsdatei anlegen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','90','Neue Datenbank anlegen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','91','Definition der Tabelle `%s` gespeichert.','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','92','Credits / Hilfe','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','93','Cronscript','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','94','Kommentar eingeben','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','95','Komplette Ausgabe loggen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','96','Pfad der Perlskripte','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','97','Dateiendung des Scripts','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','98','Textausgabe','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','99','CSV-Optionen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','100','Zeilen getrennt mit','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','101','Fehler beim Erstellen der Tabelle `%s`!','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','102','Die Anzahl der Tabellenfelder stimmen nicht mit den zu importierenden Daten überein (%d statt %d).','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','103','Felder eingeschlossen von','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','104','Felder getrennt mit','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','105','Felder escaped von','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','106','%d Felder ermittelt, insgesamt %d Zeilen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','107','CSV-Datei öffnen','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','108','Feldnamen in die erste Zeile','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','109','Keine Daten zum Importieren gefunden!','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('1','110','Ersetze NULL durch','2012-03-04 22:28:12');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','2','Action','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','3','activated','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','4','Up to now <b>%s</b> records were successfully added.','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','5','Up to now  <b>%s</b> of <b>%s</b> records were successfully added.','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','6','Add','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','7','added','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','8','Add database manually','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','9','Add recipient','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','10','all','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','11','Analyze','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','12','Now data of the table \'<b>%s</b>\' is being analyzed.','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','13','Do you  want to copy database `%s` to database `%s`?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','14','Do you want to delete the Database `%s` with the content?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','15','Do you want to empty the Database `%s` ?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','16','Do you want to delete the Field?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','17','Are you sure to delete this record?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','18','Should the table `%s` be deleted?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','19','Should the table `%s` be emptied?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','20','Should the table `%s` be emptied and the Indices reset?','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','21','attached as file','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','22','Attach backup','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','23','Login information','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','24','Authorize','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','25','Delete backups automatically','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','26','back','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','27','In the Backup directory there are','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','28','Backups','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','29','DBs to backup','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','30','Dumping of table `%s` finished. %s records have been saved.','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','31','Database Overview','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','32','Call','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','33','Cancel','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','34','Couldn\' t create dir \'%s\'. <br />Please create it using your FTP program.','2012-03-04 22:29:00');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','35','change','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','36','Changing to Directory','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','37','Couldn`t change directory!','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','38','Charset','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','39','Character Sets','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','40','Check','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','41','Check my directories','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','42','MySQLDumper couldn\'t detect the encoding of the backup file automatically.<br /><br />You must choose the charset with which this backup was saved.<br /><br />If you discover any problems with some characters after restoring, you can repeat the backup-progress and then choose another character set.<br /><br />Good luck. ;)<br /><br />','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','43','Select Database','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','44','Clear database','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','45','Close','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','46','Collation','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','47','Command','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','48','Command after backup','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','49','Command before backup','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','50','Comment','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','51','compressed (gz)','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','52','Basic Parameter','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','53','Configuration','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','54','Config File','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','55','Configuration Files','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','56','Configurations','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','57','Autodelete','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','58','Crondump Settings for Perl script','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','59','Email Notification','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','60','FTP Transfer of Backup file','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','61','Configuration','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','62','Interface','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','63','Configuration \"%s\" has been imported successfully.','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','64','Really delete the configuration file %s?','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','65','Should the file \'%s\' really be deleted?','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','66','Really delete the selected tables?','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','67','Should the selected databases really be deleted?<br /><br />Attention: all data will be deleted! Maybe you should create a backup first.','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','68','Should the recipient \"%s\" really be deleted?','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','69','Should all tables of the selected databases really be deleted?<br /><br />Attention: all data will be deleted! Maybe you want to create a backup first.','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','70','Really empty the selected tables?','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','71','connect','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','72','Connection Parameter','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','73','Connect to MySQL','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','74','Continue Multipart-Restore with next file \'%s\'.','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','75','Converted Files','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','76','Backup Converter','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','77','Converting','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','78','File to be converted','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','79','Name of destination file (without extension)','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','80','Read file \'%s\'','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','81','Conversion finished, \'%s\' was written successfully.','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','82','Start Conversion','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','83','Convert Dump to MSD Format','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','84','Wrong parameters!  Conversion is not possible.','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','85','Create','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','86','Created','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','87','Create Directories','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','88','Create Auto-Index','2012-03-04 22:29:01');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','89','Create a new configuration file','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','90','Create new database','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','91','Definition of table `%s` saved.','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','92','Credits / Help','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','93','Cronscript','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','94','Enter Comment','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','95','Log complete output','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','96','Path of Perl scripts','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','97','File extension','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','98','Print output on screen.','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','99','CSV Options','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','100','Seperate lines with','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','101','Error while creating table `%s`!','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','102','The count of fields doesn\'t match with that of the data to import (%d instead of %d).','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','103','Fields enclosed by','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','104','Fields separated with','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','105','Fields escaped with','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','106','%d fields recognized, totally %d lines','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','107','Open CSV file','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','108','Field names in first line','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','109','No data found for import!','2012-03-04 22:29:02');
INSERT INTO `translations` (`lang_id`,`key_id`,`text`,`dt`) VALUES ('2','110','Replace NULL with','2012-03-04 22:29:02');
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
