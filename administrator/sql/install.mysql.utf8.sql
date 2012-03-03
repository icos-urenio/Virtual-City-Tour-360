DROP TABLE IF EXISTS `#__virtualcitytour360`;

CREATE TABLE IF NOT EXISTS `#__virtualcitytour360` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `catid` int(11) NOT NULL default '0',
  `latitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `description` text,
  `photos` text,
  `panoramas` text,
  `params` text NOT NULL,
  `state` tinyint(3) NOT NULL default '1',
  `language` char(7) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT COLLATE=utf8_general_ci;

