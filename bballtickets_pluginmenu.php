<?php

$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE `id`=1"));

if (checkAdmin($_SESSION['username'])){

echo '
<li class="dir">Billetter
        <ul>
                  <li class="first"><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_courts.php">Baner/Pladser</a></li>
                  <li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_tickettypes.php">Billettyper</a></li>
                  <li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_tickets.php">Billetter/Kort</a></li>';
if($config['conventus_enabled']){

      echo '<li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_conventustickets.php">Conventus Plugin</a></li>';

}                  
                  
echo                  '<li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_statistic.php">Statistik</a></li>
                  <li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_importexport.php">Import/Eksport</a></li>
                  <li class="last"><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_config.php">Konfiguration</a></li>
        </ul>
</li>';

}

?>