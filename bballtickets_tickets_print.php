<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkAdmin.php");

require("bballtickets_functions.php");

if(isset($_GET['ticketid'])){
        $ticket = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_tickets` WHERE `id`='".$_GET['ticketid']."'"));
        $type = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_tickettypes` WHERE `id`='".$ticket['type']."'"));
        $cardcode = trim(str_pad((int) $ticket['type'],"4","0",STR_PAD_LEFT).str_pad((int) $ticket['id'],"10","0",STR_PAD_LEFT));

        if(!file_exists("barcodes/".$cardcode.".jpg")){
                generateBarcode($cardcode);
        }
        $config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE `id`=1"));

        include_once('includes/tbs_class.php');
        include_once('includes/tbs_plugin_opentbs.php');

        $TBS = new clsTinyButStrong;
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
        $TBS->LoadTemplate('templates/'.$config['template'], OPENTBS_ALREADY_UTF8);
        $name = $ticket['name'];
        $name = str_replace("Conventus Bruger: ","",$name);
        $type = $type['name'];
        $barcode = "barcodes/".$cardcode.".jpg";
        $logo = "images/logo.jpg";
        $TBS->Show(OPENTBS_DOWNLOAD);


}
?>
