<?php

require("bballticketclient_connect.php");
require("bballticketclient_check_database.php");
require("bballticketclient_theme.php");

getThemeHeader();
?>

function formfocus() {
   document.getElementById('scan').focus();
}

function FormSubmitGame(el) {
  
  gamelist.submit() ;

  return;
}

function isNumberKey(evt){
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;
     return true;
}

window.onload = formfocus;

<?php

/*
/ Status Codes
/
/ 0 - OK
/ 1 - Invalid Code Length
/ 2 - Non-existing Card/Ticket
/ 3 - Wrong or Unknown Type
/ 4 - Card/Ticket Suspended
/ 5 - Tickettype has Expired
/ 6 - Card/Ticket doesn't give access to this game
/ 7 - Ticket sold
/ 8 - Number of accesses for the Card/Ticket has been exceeded
/ 9 - No room in available seatgroups
*/

if(isset($_POST['scan'])){
    $scan = $_POST['scan'];
    $game = $_POST['game'];
    
    if(strlen($scan) != 14){
         $message = "Ugyldig billet/kort kode";
         $status = 1;
         $color = "red";
    }else{
         $ticket = substr($scan,4,10);
         $type = substr($scan,0,4);
         $ticketquery = mysql_query("SELECT * FROM `bballtickets_tickets` WHERE `id`=".$ticket);
         if(!mysql_num_rows($ticketquery)){
              $message = "Billet/Kort eksisterer ikke";
              $status = 2;
              $color = "red";
         }else{
              $ticketinfo = mysql_fetch_assoc($ticketquery);
              $typequery = mysql_query("SELECT * FROM `bballtickets_tickettypes` WHERE `id`=".$type);
              $typeinfo = mysql_fetch_assoc($typequery);
              if($ticketinfo['type'] != $type || !mysql_num_rows($typequery)){
                   $message = "Billet/Kort er forkert type eller typen eksisterer ikke";
                   $status = 3;
                   $color = "red";
              }elseif($ticketinfo['suspended']){
                   $message = "Billetten/Kortet er deaktiveret";
                   $status = 4;
                   $color = "red";
              }elseif(($typeinfo['expires'] < date("Y-m-d")) && !($typeinfo['expires'] == "0000-00-00")){
                   $message = "Billettypes udløbsdato er nået";
                   $status = 5;
                   $color = "red";
              }
         }
    }     
    if(!isset($status)){
         $typeaccess = explode(',',$typeinfo['access']);
         $typeseatgroups = explode(',',$typeinfo['group']);
         foreach($typeseatgroups as $typeseatgroup){
              if(($typeseatgroup != "") && ($typeseatgroup != 0)){
                   $typegroupquery .= "`id` = '".$typeseatgroup."' OR ";
              }
         }
         $typegroupquery = substr($typegroupquery,0,-3);
         $seatgroupquery = mysql_query("SELECT * FROM `bballtickets_seatgroups` WHERE ".$typegroupquery." ORDER BY `priority` DESC");
         
         if(in_array('all',$typeaccess)){
               $checkinquery = mysql_query("SELECT * FROM `bballtickets_checkins` WHERE `game`='".$game."' AND `code`='".$scan."' AND `status`='0'");
         }elseif(in_array('free',$typeaccess)){
               $checkinquery = mysql_query("SELECT * FROM `bballtickets_checkins` WHERE `code`='".$scan."' AND `status`='0'");
         }elseif(in_array('counter',$typeaccess)){
               $checkinquery = "counter";
         }else{
               if(!in_array($game,$typeaccess)){
                    $message = "Billetten/Kortet giver ikke adgang til denne kamp";
                    $status = 6;
                    $color = "red";
               }else{
                    $checkinquery = mysql_query("SELECT * FROM `bballtickets_checkins` WHERE `game`='".$game."' AND `code`='".$scan."' AND `status`='0'");
               }
         }
    }
    if(!isset($status)){
         if($checkinquery != "counter"){
              if($typeinfo['seats'] != "unlimited"){
                   if(mysql_num_rows($checkinquery) >= $typeinfo['seats']){
                        $message = "Billetten/Kortet giver adgang for ".$typeinfo['seats']." person(er), og er allerede opbrugt";
                        $status = 8;
                        $color = "red";
                   }
              }
         }
    }
    if(!isset($status)){
         foreach($typeseatgroups as $typeseatgroup){
              if(($typeseatgroup != "") && ($typeseatgroup != 0) && ($seatgroup == "")){
                   $seatgroupinfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_seatgroups` WHERE `id`='".$typeseatgroup."'"));
                   $checkinquery = mysql_query("SELECT * FROM `bballtickets_checkins` WHERE `game`='".$game."' AND `status`='0' AND `seatgroup`='".$typeseatgroup."'");
                   if(mysql_num_rows($checkinquery) < $seatgroupinfo['seats']){
                        $seatgroup = $typeseatgroup;
                   }
              }
         }
         if($seatgroup == ""){
              $message = "Ingen af de sædegrupper billetten/kortet giver adgang til er frie";
              $status = 9;
              $color = "red";
         }elseif($checkinquery == "counter"){
              $message = "Billet solgt, placeret i ".$seatgroupinfo['name']."";
              $status = 7;
              $color = "green";         
         }else{
              $message = "Billet/Kort OK, placeret i ".$seatgroupinfo['name']."";
              $status = 0;
              $color = "green";
         }
         
    }
    
    
    
    
    mysql_query("INSERT INTO `bballtickets_checkins` (`game`,`code`,`status`,`seatgroup`,`new`) VALUES ('".$_POST['game']."','".$scan."','".$status."','".$seatgroup."','1')");

}


getThemeTitle();


if(!isset($_POST['game'])){
    $config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE id='1'"));
    $teams = explode(",",$config['hold']);
    
    $gamelist = "<option>--- Vælg Kamp ---</option>";
    foreach($teams as $teamid){
         if($teamid != ""){
              $teaminfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `calendars` WHERE id='".$teamid."'"));
              $games = mysql_query("SELECT * FROM `games` WHERE team='".$teamid."' AND homegame='1' ORDER BY date");
              while($game = mysql_fetch_assoc($games)){
                   $opponent = explode(">",$game['text']);
                   $gamelist .= "<option value='".$game['id']."'>".$game['date']."  ".$teaminfo['team']." ".$opponent[1]."</option>\n";
              }
         }
    }

    
    echo '<center><form method="post" name="gamelist" onChange="FormSubmitGame(this)">
           <select name="game">'.$gamelist.'</select>
           </form>
          </center>';

}else{
    $gameinfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `games` WHERE id=".$_POST['game']));
    echo "<center><h3>".$gameinfo['text']."</h3><br></center>";
    echo '<center><form action="bballticketclient_scan.php" method="post">
           <input id="scan" name="scan" onkeypress="return isNumberKey(event)" type="text" size="20">
           <input id="game" name="game" type="hidden" value="'.$_POST['game'].'">
          </form></center>';
    echo '<br><center><h3><font color="'.$color.'">'.$message.'</font></h3></center><br>';
}
getThemeBottom();

?>
