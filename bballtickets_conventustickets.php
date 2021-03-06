<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkLogin.php");
require("../../checkAdmin.php");
require("../../theme.php");
require("bballtickets_functions.php");

echo '<link rel="stylesheet" type="text/css" href="css/conventus.css" />';

getThemeHeader();

?>

function goBatch(){
   
 document.ticketlist.submit();
      
}

<?php

getThemeTitle("Conventus Billetter/Kort");

if($_POST['action'] == "barcodelist"){

 foreach($_POST['checkbox'] as $checkbox){
    echo $checkbox . '<br>';
 }

}

if(isset($_POST['createCID'])){

   mysql_query("INSERT INTO `bballtickets_tickets` (`name`,`type`,`suspended`) VALUES ('Conventus Bruger: ".$_POST['CIDname']."','".$_POST['tickettype']."','0')");
   $barcodeid = str_pad((int) $_POST['tickettype'],"4","0",STR_PAD_LEFT).str_pad((int) mysql_insert_id(),"10","0",STR_PAD_LEFT);
   mysql_query("UPDATE `bballtickets_conventus` SET `ticketid`='".$barcodeid."' WHERE `id`='".$_POST['createCID']."'");
}

$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE `id`=1"));

if(isset($_POST['conventus_sync'])){

$content  = file_get_contents("https://www.conventus.dk/dataudv/www/medlemsliste.php?foreningsid=".$config['conventus_id']."&gruppe_type=".$config['conventus_grouptype']."&gruppe_id=".$config['conventus_groupid']."&vis_ikoner=1&id=1&alt_id=1&type=1&koen=1&titel=1&navn=1&adresse1=1&adresse2=1&postnr=1&by=1&tlf=1&mobil=1&email=1&birth=1&hjemmeside=1&har_bs_aftale=1&tilmeldtdato=1&indmeldtdato=1");

$page = '<html>
    <head> 
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Dommer Sync</title>
    </head>
    <body></body>
    </html>';
$page .= $content;
$dom = new DOMDocument();
$html = $dom->loadHTML($page);

$dom->preserveWhiteSpace = false;

$lines = $dom->getElementsByTagName('tr');

$ignorenext = 1;
$new_persons = 0;
$updated_persons = 0;

foreach ($lines as $line){

$cols = $line->getElementsByTagName('td');

   if($ignorenext == 0){
      if(substr($cols->item(0)->nodeValue,0,2) == "Id"){
         echo "ID";
      }elseif(is_numeric($cols->item(0)->nodeValue)){
         if(mysql_num_rows(mysql_query("SELECT * FROM `bballtickets_conventus` WHERE id = '".$cols->item(0)->nodeValue."'")) == 0){
            mysql_query("INSERT INTO `bballtickets_conventus` (`id`,`name`,`team`) VALUES ('".$cols->item(0)->nodeValue."','".$cols->item(5)->nodeValue."','".$teamname."')");
            $new_persons++;
         }else{
            mysql_query("UPDATE `bballtickets_conventus` SET `name`='".$cols->item(5)->nodeValue."', `team`='".$teamname."' WHERE `id`='".$cols->item(0)->nodeValue."'");
            $updated_persons++;
         }
      }else{
         $team = explode('(',$cols->item(0)->nodeValue);
         $teamname = $team[0];
         $ignorenext = 2;
      }
      
   }else{
      $ignorenext--;
   }

}

$message = '<font color="green">Medlemmer synkroniseret fra Conventus, '.$new_persons.' nye, '.$updated_persons.' opdateret..</font><br><br>';

}

require("../../menu.php");

require("bballtickets_check_database.php");

echo $message;

echo '<div id="test"></div>';

echo '<form method="post">
      <input type="submit" name="conventus_sync" value="Sync fra Conventus">
      </form><br><br>';

echo '<form name="ticketlist" id="ticketlist" method="post">
      <select name="action" id="batchAction">
       <option value="-" selected>Vælg operation</option>
       <option value="createTickets">Opret kort</option>
      </select>
      <input type="hidden" name="tickettype-batch" id="tickettype-batch">
      <br><br>
      <table>
       <tr>
        <th width="30px" align="left"><input type="checkbox" id="checkall"></th>
        <th width="70px" align="left">CID</th>
        <th width="200px" align="left">Navn</th>
        <th width="100px" align="left">Billet</th>
       </tr>';


$persons = mysql_query("SELECT * FROM `bballtickets_conventus` ORDER BY `team`,`name`");

$team = "";

while($person = mysql_fetch_assoc($persons)){

if($team != $person["team"]){

 $team = $person["team"];
echo '<tr>
       <th colspan="3">'.$team.'</th>
      </tr>';

}

echo '<tr>
       <td><input id="checkbox-'.$person["id"].'" type="checkbox" value="'.$person["id"].'" name="checkbox[]"></td>
       <td>'.$person["id"].'</td>
       <td>'.$person["name"].'</td>
       <td id="ticketid-'.$person["id"].'">';
       
       if($person["ticketid"] == ""){
          echo '<div id="'.$person["id"].'">Opret billet</div>';
       }else{
          $ticket = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_tickets` WHERE `id`='".$person["ticketid"]."'"));
          $barcodeid = str_pad((int) $ticket['type'],"4","0",STR_PAD_LEFT).str_pad((int) $person["ticketid"],"10","0",STR_PAD_LEFT);
          echo $barcodeid;
       }
       
       echo '</td>
      </tr>';



echo '<script>
$("#'.$person["id"].'").click(function(e) {       
    $("#popupContent").html("Opret Kort/Billet for '.$person["name"].'");
    $("#popupName").val("'.$person["name"].'");
    $("#popupCID").val("'.$person["id"].'");
    $("#popUpDiv").show();
});
</script>';


}

echo '</table></form><br><br>';

echo '<div id="popUpDiv">
      
      <select id="popupSelect">';
      
      $tickettypes = mysql_query("SELECT * FROM `bballtickets_tickettypes`");
      while($tickettype = mysql_fetch_assoc($tickettypes)){
         echo '<option value="'.$tickettype['id'].'">'.$tickettype['name'].'</option>';
      }


echo  '</select>
      <input type="submit" id="popupOk" value="OK">
      <input type="submit" id="popupCancel" value="Annuller">
      <input type="hidden" id="popupName">
      <input type="hidden" id="popupCID">
      <div id="popupContent"></div>
      </div>';

echo '<form method="post" id="createticket">
       <input type="hidden" id="createCID" name="createCID">
       <input type="hidden" id="CIDname" name="CIDname">
       <input type="hidden" id="tickettype" name="tickettype">
       <input type="hidden" id="action" name="action" value="createTicket">
      </form>';

getThemeBottom();

?>

<script>

$("#checkall").change(function(e){
 if($("#checkall").is(':checked')){
  $("input[id*='checkbox-']").attr('checked', true);
 }else{
  $("input[id*='checkbox-']").attr('checked', false);
 }
});

$("#batchAction").change(function(e) {
 $("#popupContent").html("Opret Kort/Billet for de valgte personer");
 $("#popupName").val("");
 $("#popupCID").val("");
 $("#popUpDiv").show();
});

$("#popupOk").click(function(e) {
  $("#createCID").val($("#popupCID").val());
  $("#tickettype").val($("#popupSelect").val());
  $("#tickettype-batch").val($("#popupSelect").val());
  $("#CIDname").val($("#popupName").val());
  $("#popUpDiv").hide();
  if($("#popupContent").html() == "Opret Kort/Billet for de valgte personer"){
   var form = $("#ticketlist");
  }else{
   var form = $("#createticket");
  }
  $.ajax({type: "POST", url: "ajax.php",dataType: "json",data: form.serialize(),success: function(data){
    $.each(data, function(label,value){
      $("#ticketid-"+label).html(value);
      $("#checkbox-"+label).attr('checked', false);
    }); 
  }, error: function(xhr, status, err) {
    alert(status + ": " + err);
  }
  });
  $("#batchAction").val("-");  
});

$("#popupCancel").click(function(e) {
  $("#popUpDiv").hide();
  $("#batchAction").val("-");
});

</script>
