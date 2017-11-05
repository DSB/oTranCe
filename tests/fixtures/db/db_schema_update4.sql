USE `phpunit_otc`;
ALTER TABLE `translations` ADD `needs_update` TINYINT( 1 ) DEFAULT '0' NOT NULL;
