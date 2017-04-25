-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_taxonomy`
-- 

CREATE TABLE `tl_taxonomy` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0', 
  `type` varchar(255) NOT NULL default '', 
  `alias` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `name_en` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `alias` (`alias`)
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

CREATE TABLE `tl_user_group` (
  `taxonomymounts` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_user` (
  `taxonomymounts` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

