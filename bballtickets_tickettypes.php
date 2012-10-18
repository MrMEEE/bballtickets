<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkLogin.php");
require("../../checkAdmin.php");
require("../../theme.php");

getThemeHeader();

?>

function removeTicketType(typeid){
 
 answer = confirm("Er du sikker på at du vil slette denne billettype??")
 
 if (answer !=0)
 {
   document.type.typeid.value=typeid;
   document.type.action.value="remove";
   document.type.submit();
 }
 
}

function editTicketType(typeid){
 
   var path = "bballtickets_tickettypes_info.php?typeid=" + typeid;
   mywindow = window.open(path,"mywindow","menubar=1,resizable=1,width=500,height=480");
   
}

<?php

getThemeTitle("Billettyper");

require("../../menu.php");

require("bballtickets_check_database.php");

if(isset($_POST['typeid'])){

      if($_POST['action'] == "remove"){
             $query = "DELETE FROM bballtickets_tickettypes WHERE id='".$_POST['typeid']."'";
      }elseif($_POST['typeid']=="-1"){
             $query = "INSERT INTO bballtickets_tickettypes (`name`,`group`,`seats`,`expires`,`access`) VALUES ('".$_POST['name']."','".$_POST['group']."','".$_POST['seats']."','".$_POST['expires']."','".$_POST['access']."')";
      }else{
             $query = "UPDATE bballtickets_tickettypes SET `name`='".$_POST['name']."',`group`='".$_POST['group']."',`seats`= '".$_POST['seats']."',`expires`= '".$_POST['expires']."',`access`= '".$_POST['access']."' WHERE id = '".$_POST['typeid']."'";
      }
      mysql_query($query);
}

$query = mysql_query("SELECT * FROM `bballtickets_tickettypes`");

while($row = mysql_fetch_assoc($query)){
      
      if($row["seats"]== "unlimited"){
           $seats = "et ubegrænset antal";
      }else{
           $seats = $row["seats"];
      }
      
      $courts .= '<a href="javascript:void(removeTicketType(\''.$row["id"].'\'))"><img width="15px" src="img/remove.png"></a>
      <a href="javascript:void(editTicketType(\''.$row["id"].'\'))">
      <img width="15px" src="img/edit.png"></a> '.$row["name"].' - Giver adgang for '.$seats.' person(er)<br>';

}

echo "<h3>Billettyper:</h3> <br>".$courts."<br><br>";

?>

<form method="post" name="type">
  <input type="hidden" id="typeid" name="typeid" value="">
  <input type="hidden" id="action" name="action" value="">
  <input type="hidden" id="name" name="name" value="">
  <input type="hidden" id="seats" name="seats" value="">
  <input type="hidden" id="group" name="group" value="">
  <input type="hidden" id="expires" name="expires" value="">
  <input type="hidden" id="access" name="access" value="">
</form>


<?php

echo '<a href="javascript:void(0)" onclick="editTicketType(-1);"><img width="25px" src="img/add.png"></a> <font size="3">Tilføj Billettype</font>';

getThemeBottom();

?>
