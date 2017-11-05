ALTER TABLE `translations` ADD COLUMN `project_id` INT( 11 ) UNSIGNED NOT NULL AFTER `key_id`;
ALTER TABLE `translations` DROP PRIMARY KEY, ADD PRIMARY KEY (`lang_id`,`key_id`, `project_id`);
ALTER TABLE `keys` ADD COLUMN `project_id` INT( 11 ) UNSIGNED NOT NULL AFTER `template_id`;
UPDATE `translations` SET `project_id` = 1;
UPDATE `keys` SET `project_id` = 1;
