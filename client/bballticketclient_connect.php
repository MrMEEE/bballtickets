<?php


/* Database config */

$db_host		= 'HOST';
$db_user		= 'USER';
$db_pass		= 'PASSWD';
$db_database		= 'DATABASE'; 

/* End config */


$link = @mysql_pconnect($db_host,$db_user,$db_pass);
// || die('Unable to establish a DB connection');

mysql_set_charset('utf8');
mysql_select_db($db_database,$link);

?>