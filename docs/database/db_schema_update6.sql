CREATE TABLE `module_config` (
  `module_id` varchar(80) NOT NULL,
  `varname` varchar(40) NOT NULL,
  `varvalue` text NOT NULL,
  UNIQUE KEY `module_id` (`module_id`,`varname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
