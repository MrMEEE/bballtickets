<?php

if(!mysql_num_rows(mysql_query("SHOW TABLES LIKE 'bballtickets_config'"))){
      mysql_query("CREATE TABLE `bballtickets_config` (`id` int(11) NOT NULL AUTO_INCREMENT, `hold` text NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
      mysql_query("INSERT INTO `bballtickets_config` SET `id`=1,`hold`=''");
}
if(!mysql_num_rows(mysql_query("SHOW COLUMNS FROM 'bballtickets_courts'"))){
      mysql_query("CREATE TABLE `bballtickets_courts` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` text NOT NULL,`address` text NOT NULL, `seats` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
      mysql_query("CREATE TABLE `bballtickets_seatgroups` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` text NOT NULL,`seats` int(11) NOT NULL, `court` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
      mysql_query("CREATE TABLE `bballtickets_tickettypes` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` text NOT NULL,`seats` int(11) NOT NULL,`group` int(11) NOT NULL, `expires` date NOT NULL, `access` text NOT NULL ,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
}

?>
