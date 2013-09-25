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

if(isset($_POST['conventus_id'])){
    
    mysql_query("UPDATE `bballtickets_config` SET `conventus_id`='".$_POST['conventus_id']."' WHERE `id`='1'");

}

if(isset($_POST['conventus_grouptype'])){ 
    
    mysql_query("UPDATE `bballtickets_config` SET `conventus_grouptype`='".$_POST['conventus_grouptype']."' WHERE `id`='1'");
         
}

if(isset($_POST['conventus_groupid'])){ 
    
    mysql_query("UPDATE `bballtickets_config` SET `conventus_groupid`='".$_POST['conventus_groupid']."' WHERE `id`='1'");
         
}

if(isset($_POST['conventus_save'])){ 
    
    if($_POST['conventus_enabled'] == "on"){
        $conventus_enabled = 1;
    }else{
        $conventus_enabled = 0;
    }
    
    mysql_query("UPDATE `bballtickets_config` SET `conventus_enabled`='".$conventus_enabled."' WHERE `id`='1'");
                
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
               }else{
                     $status = "";
               }
               echo '<option value="'.$entry.'" '.$status.'>'.$entry.'</option>';
         }
    }
}
echo "</select>";
echo "</form>";
echo "<br><br>";

if($config["conventus_enabled"] != 1){
   
   $useconventus = "disabled";

}else{

   $conventusactive = "checked";

}

echo '<form method="post" name="conventus">';
echo '<h3>Conventus aktiveret:</h3>
     <input type="checkbox" name="conventus_enabled" '.$conventusactive.'>';
echo '<h3>Conventus Foreningsid:</h3>
      <input type="text" name="conventus_id" value="'.$config["conventus_id"].'" '.$useconventus.'>';
echo '<h3>Conventus Gruppetype:</h3>
      <input type="text" name="conventus_grouptype" value="'.$config["conventus_grouptype"].'" '.$useconventus.'>';
echo '<h3>Conventus Gruppeid:</h3>
      <input type="text" name="conventus_groupid" value="'.$config["conventus_groupid"].'" '.$useconventus.'>';
      
echo '<br><input name="conventus_save" type="submit" value="Gem">
      </form><br><br>';


echo "<h3>Hold med billetsystem aktiveret:</h3> <br>".$stats."<br><br>";
echo "<h3>Hold uden billetsystem aktiveret:</h3> <br>".$nostats."<br><br>";

?>

<?php
getThemeBottom();

?>
