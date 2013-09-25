ALTER TABLE `bballtickets_config` ADD `conventus_id` TEXT NOT NULL ,
ADD `conventus_grouptype` TEXT NOT NULL ,
ADD `conventus_groupid` TEXT NOT NULL ,
ADD `conventus_enabled` BOOLEAN NOT NULL  ;
CREATE TABLE `bballtickets_conventus` (`id` text NOT NULL, `ticketid` text NOT NULL,`name` text NOT NULL,`team` text NOT NULL,) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
