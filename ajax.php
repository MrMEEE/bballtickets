<?php

require("../../connect.php");

switch($_POST['action']){

    case 'update':
        if($_POST['game']=="all"){
            $query = mysql_query("SELECT * FROM `bballtickets_checkins` WHERE `status` = 0");
        }else{
            $query = mysql_query("SELECT * FROM `bballtickets_checkins` WHERE `status` = 0 AND `game` = ".$_POST['game']."");    
        }
 
        while($checkin = mysql_fetch_assoc($query)){
            
            $types[] = substr($checkin['code'], 0, -10);
        
        }
        
        $typecount = array_count_values($types);
        foreach($_POST['checkbox'] as $checkbox){
            $type = substr_replace($checkbox, "", -1, -10);
            $result = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_tickettypes` WHERE `id`='".$type."'"));
            $names[] = $result['name'];
            if(array_key_exists($checkbox, $typecount)){
                $values[] = $typecount[$checkbox];
            }else{
                $values[] = 0;
            }
        }
        
        $json = '{ ';
        $json .= '"values" : '.json_encode($values).', ';
        $json .= '"names" : '.json_encode($names).'' ;
        $json .= '}';
        
        echo $json;
        
    break;
    case 'createTicket':
        if(isset($_POST['createCID'])){
            mysql_query("INSERT INTO `bballtickets_tickets` (`name`,`type`,`suspended`) VALUES ('Conventus Bruger: ".$_POST['CIDname']."','".$_POST['tickettype']."','0')");
            $barcodeid = str_pad((int) $_POST['tickettype'],"4","0",STR_PAD_LEFT).str_pad((int) mysql_insert_id(),"10","0",STR_PAD_LEFT);
            mysql_query("UPDATE `bballtickets_conventus` SET `ticketid`='".mysql_insert_id()."' WHERE `id`='".$_POST['createCID']."'");
        }
        $arr = array('barcodeid'=>$barcodeid);        
        echo json_encode($arr);
    break;

}
?>
