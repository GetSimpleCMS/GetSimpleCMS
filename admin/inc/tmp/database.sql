-- Users Table
CREATE TABLE IF NOT EXISTS `gs_users` (
  `user_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80),
  `password` varchar(80),
  `email` varchar(45),
  `htmleditor` varchar(1),
  `timezone` varchar(100),
  `lang` varchar(15),
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- Website Table
CREATE TABLE IF NOT EXISTS `gs_website` (
  `website_id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `sitename` varchar(100),
  `siteurl` varchar(100),
  `template` varchar(80),
  `prettyurls` varchar(1),
  `permalink` varchar(100),
  PRIMARY KEY (`website_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- Pages Table
CREATE TABLE IF NOT EXISTS `gs_pages` (
  `page_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100),
  `slug` varchar(45),
  `parent` varchar(45),
  `private` varchar(1),
  `template` varchar(45),
  `keywords` text,
  `description` text,
  `content` mediumtext,
  `pubDate` varchar(45),
  `menu_text` varchar(100),
  `menu_priority` varchar(2),
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- Components Table
