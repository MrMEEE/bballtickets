<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkLogin.php");
require("../../checkAdmin.php");
require("../../theme.php");

getThemeHeader();

?>

function goBatch(){
   
      document.tickettypelist.submit();
      
}

function test(){

    alert("Test");
}

<?php
getThemeTitle("Billet Statistik");


require("../../menu.php");

require("bballtickets_check_database.php");

echo '<script type="application/javascript" src="js/awesomechart.js"></script>';




echo '<canvas id="canvas1" width="1024" height="500">
        Your web-browser does not support the HTML 5 canvas element.
            </canvas>';

echo '<script type="application/javascript">
        if(!!document.createElement(\'canvas\').getContext){ //check that the canvas
                                                           // element is supported
            var mychart = new AwesomeChart(\'canvas1\');
            mychart.title = "Statistik";
            mychart.data = [0];
            mychart.labels = [];
            mychart.draw();
        }
      
    </script><br><br>';

$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `bballtickets_config` WHERE id='1'"));
$teams = explode(",",$config['hold']);

foreach($teams as $teamid){
     if($teamid != ""){
          $teaminfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `calendars` WHERE id='".$teamid."'"));
          $games = mysql_query("SELECT * FROM `games` WHERE team='".$teamid."' AND homegame='1' ORDER BY date");
          while($game = mysql_fetch_assoc($games)){
               $opponent = explode(">",$game['text']);
               $gamelist .= "<option value='".$game['id']."' ";
               $gamelist .= ">".$game['date']." - ".$teaminfo['team']." ".$opponent[1]."</option>";
          }
     } 
}

    
    echo '<h3>Billettyper</h3><br>';

    $query = "SELECT * FROM `bballtickets_tickettypes`";
    $query = mysql_query($query);
    echo '<form name="tickettypelist" id="tickettypelist">';
    while($row = mysql_fetch_assoc($query)){
    
          echo '<input type="checkbox" value="'.$row['id'].'" name="checkbox[]"> '.$row['name']."<br>";
    
    }
    echo '<br><select id="game" name="game">
           <option value="all" selected>Alle Kampe</option>
           '.$gamelist.'
          </select>';
    echo '<br><br><input type="submit" value="opdater"><br><br>';
    echo '<input type="hidden" id="action" name="action" value="update">';
    echo '</form><br><br>';


getThemeBottom();

?>

<script type="text/javascript">

$('#tickettypelist').submit(function() {
  var form = $('#tickettypelist');
  $.ajax({type: "POST", url: "ajax.php",dataType: "json",data: form.serialize(),success: function(data){
     
     var values = [];
     $.each(data.values,function(index,value) {
      values.push(value);
     });
     mychart.data=values;
     var names = [];
     
     $.each(data.names,function(index,value) {
      names.push(value);
     });
     mychart.labels=names;
     mychart.animateBarChart();
  },error: function(xhr, status, err) {
     alert(status + ": " + err);
  }
  });
     
  return false;
});
</script>
