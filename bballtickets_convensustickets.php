<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkLogin.php");
require("../../checkAdmin.php");
require("../../theme.php");
require("bballtickets_functions.php");

getThemeHeader();

?>

<?php

getThemeTitle("Convensus Billetter/Kort");

$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE `id`=1"));

if(isset($_POST['convensus_sync'])){

$content  = file_get_contents("https://www.conventus.dk/dataudv/www/medlemsliste.php?foreningsid=".$config['convensus_id']."&gruppe_type=".$config['convensus_grouptype']."&gruppe_id=".$config['convensus_groupid']."&vis_ikoner=1&id=1&alt_id=1&type=1&koen=1&titel=1&navn=1&adresse1=1&adresse2=1&postnr=1&by=1&tlf=1&mobil=1&email=1&birth=1&hjemmeside=1&har_bs_aftale=1&tilmeldtdato=1&indmeldtdato=1");

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
         if(mysql_num_rows(mysql_query("SELECT * FROM `bballtickets_convensus` WHERE id = '".$cols->item(0)->nodeValue."'")) == 0){
            mysql_query("INSERT INTO `bballtickets_convensus` (`id`,`name`,`team`) VALUES ('".$cols->item(0)->nodeValue."','".$cols->item(5)->nodeValue."','".$teamname."')");
            $new_persons++;
         }else{
            mysql_query("UPDATE `bballtickets_convensus` SET `name`='".$cols->item(5)->nodeValue."', `team`='".$teamname."' WHERE `id`='".$cols->item(0)->nodeValue."'");
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

$message = '<font color="green">Medlemmer synkroniseret fra Convensus, '.$new_persons.' nye, '.$updated_persons.' opdateret..</font><br><br>';

}

require("../../menu.php");

require("bballtickets_check_database.php");

echo $message;

echo '<form method="post">
      <input type="submit" name="convensus_sync" value="Sync fra Convensus">
      </form><br><br>';


echo '<table>
       <tr>
        <th width="70px" align="left">CID</th>
        <th width="200px" align="left">Navn</th>
        <th width="100px" align="left">Billet</th>
       </tr>';


$persons = mysql_query("SELECT * FROM `bballtickets_convensus` ORDER BY `team`,`name`");

$team = "";

while($person = mysql_fetch_assoc($persons)){

if($team != $person["team"]){

 $team = $person["team"];
echo '<tr>
       <th colspan="3">'.$team.'</th>
      </tr>';

}

echo '<tr>
       <td>'.$person["id"].'</td>
       <td>'.$person["name"].'</td>
       <td>'.$person["ticketid"].'</td>
      </tr>';

}

echo '</table>';

getThemeBottom();



?>
