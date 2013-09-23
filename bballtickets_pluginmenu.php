<?php

$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE `id`=1"));

echo '
<li class="dir">Billetter
        <ul>
                  <li class="first"><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_courts.php">Baner/Pladser</a></li>
                  <li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_tickettypes.php">Billettyper</a></li>
                  <li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_tickets.php">Billetter/Kort</a></li>';
if($config['convensus_enabled']){

      echo '<li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_convensustickets.php">Convensus Plugin</a></li>';

}                  
                  
echo                  '<li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_statistic.php">Statistik</a></li>
                  <li><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_importexport.php">Import/Eksport</a></li>
                  <li class="last"><a href="http://' . $klubadresse . $klubpath . '/admin/plugins/bballtickets/bballtickets_config.php">Konfiguration</a></li>
        </ul>
</li>';

?>