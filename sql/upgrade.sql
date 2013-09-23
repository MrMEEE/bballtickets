ALTER TABLE `bballtickets_config` ADD `convensus_id` TEXT NOT NULL ,
ADD `convensus_grouptype` TEXT NOT NULL ,
ADD `convensus_groupid` TEXT NOT NULL ,
ADD `convensus_enabled` BOOLEAN NOT NULL  ;
CREATE TABLE `bballtickets_convensus` (`id` text NOT NULL, `ticketid` text NOT NULL,`name` text NOT NULL,`team` text NOT NULL,) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
