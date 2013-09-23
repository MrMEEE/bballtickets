<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkLogin.php");
require("../../checkAdmin.php");
require("../../theme.php");

getThemeHeader();

getThemeTitle("Billet Konfiguration");

require("../../menu.php");

require("bballtickets_check_database.php");

if(isset($_POST['convensus_id'])){
    
    mysql_query("UPDATE `bballtickets_config` SET `convensus_id`='".$_POST['convensus_id']."' WHERE `id`='1'");

}

if(isset($_POST['convensus_grouptype'])){ 
    
    mysql_query("UPDATE `bballtickets_config` SET `convensus_grouptype`='".$_POST['convensus_grouptype']."' WHERE `id`='1'");
         
}

if(isset($_POST['convensus_groupid'])){ 
    
    mysql_query("UPDATE `bballtickets_config` SET `convensus_groupid`='".$_POST['convensus_groupid']."' WHERE `id`='1'");
         
}

if(isset($_POST['convensus_save'])){ 
    
    if($_POST['convensus_enabled'] == "on"){
        $convensus_enabled = 1;
    }else{
        $convensus_enabled = 0;
    }
    
    mysql_query("UPDATE `bballtickets_config` SET `convensus_enabled`='".$convensus_enabled."' WHERE `id`='1'");
                
}
                 

if(isset($_POST['template'])){

      mysql_query("UPDATE `bballtickets_config` SET `template`='".$_POST['template']."' WHERE `id`='1'");

}

$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE `id`=1"));

$hold = explode(",",$config['hold']);

if(isset($_GET['add'])){

      if(!in_array($_GET['add'],$hold)){
            array_push($hold,$_GET['add']);
            $holdstr = implode(",",$hold);
            mysql_query("UPDATE `bballtickets_config` SET `hold`='".$holdstr."' WHERE `id`='1'");
      }

}

if(isset($_GET['remove'])){

      if(in_array($_GET['remove'],$hold)){
            $hold = array_diff($hold,array($_GET['remove']));
            $holdstr = implode(",",$hold);
            if($holdstr == ","){
                  $holdstr = "";
            }
            mysql_query("UPDATE `bballtickets_config` SET `hold`='".$holdstr."' WHERE `id`='1'");
      }
}
$stats = "";
$nostats = "";

$query = mysql_query("SELECT * FROM `calendars`");

while($row = mysql_fetch_assoc($query)){

      if(in_array($row['id'],$hold)){
            $stats .= '<a href="bballtickets_config.php?remove='.$row['id'].'"><img width="15px" src="img/remove.png"></a> '.$row['team'].'<br>';
      }else{
            $nostats .= '<a href="bballtickets_config.php?add='.$row['id'].'"><img width="15px" src="img/add.png"></a> '.$row['team'].'<br>';
      }

}

echo "<h3>Kort Template:</h3><br>";
echo '<form method="post">';
echo '<select id="template" name="template" onchange="this.form.submit()">';
if(($config['template'] == "") && ($config['template'] == "")){
    $status = "selected";
}

echo '<option value="-" '.$status.'>--- Ingen template valgt ---</option>';

if ($handle = opendir('templates/')) {

    while (false !== ($entry = readdir($handle))) {
         if(($entry != ".") && ($entry != "..")){
               if($config['template'] == $entry){
                     $status = "selected";
               }
               echo '<option value"'.$entry.'" '.$status.'>'.$entry.'</option>';
         }
    }
}
echo "</select>";
echo "</form>";
echo "<br><br>";

if($config["convensus_enabled"] != 1){
   
   $useconvensus = "disabled";

}else{

   $convensusactive = "checked";

}

echo '<form method="post" name="convensus">';
echo '<h3>Convensus aktiveret:</h3>
     <input type="checkbox" name="convensus_enabled" '.$convensusactive.'>';
echo '<h3>Convensus Foreningsid:</h3>
      <input type="text" name="convensus_id" value="'.$config["convensus_id"].'" '.$useconvensus.'>';
echo '<h3>Convensus Gruppetype:</h3>
      <input type="text" name="convensus_grouptype" value="'.$config["convensus_grouptype"].'" '.$useconvensus.'>';
echo '<h3>Convensus Gruppeid:</h3>
      <input type="text" name="convensus_groupid" value="'.$config["convensus_groupid"].'" '.$useconvensus.'>';
      
echo '<br><input name="convensus_save" type="submit" value="Gem">
      </form><br><br>';


echo "<h3>Hold med billetsystem aktiveret:</h3> <br>".$stats."<br><br>";
echo "<h3>Hold uden billetsystem aktiveret:</h3> <br>".$nostats."<br><br>";

?>

<?php
getThemeBottom();

?>
