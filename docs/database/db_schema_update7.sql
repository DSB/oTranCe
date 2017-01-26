ALTER TABLE  `history` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `history` CHANGE  `key_id`  `key_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `history` CHANGE  `lang_id`  `lang_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `translations` CHANGE  `lang_id`  `lang_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `translations` CHANGE  `key_id`  `key_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `keys` CHANGE  `template_id`  `template_id` INT( 11 ) NOT NULL ;
ALTER TABLE  `usersettings` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL ;