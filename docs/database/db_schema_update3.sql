-- Add new table for forgotpassword feature
CREATE TABLE `forgotpasswords` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`userid` INT(11) NULL DEFAULT '0',
	`timestamp` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM;
