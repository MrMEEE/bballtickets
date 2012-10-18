<?php

require("connect.php");
require("bballticketclient_check_database.php");
require("theme.php");

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
     return mb_convert_encoding($content, 'UTF-8',
     mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function importTDE($filename){
      $file = mb_convert_encoding(file_get_contents_utf8($filename), 'HTML-ENTITIES', "UTF-8");
      $lines = preg_split( '/\r\n|\r|\n/', $file );
      if(array_shift($lines) == "BBALLTICKETDATABASEEXPORT"){
	    foreach($lines as $line){
		   if(substr($line,0,1)=="%"){
			  $table=str_replace('%','',$line);
                   }elseif(substr($line,0,1)=="&"){
                          if($table == "bballtickets_checkins"){
				  $fields = substr($line,4);
                          }else{
                                  $fields = substr($line,1);
                          }
                   }else{
			  $query="INSERT INTO `$table` ($fields) VALUES ($line)";
                          mysql_query($query);
                          //echo $query;
                   }
            }
            return "Data fra eksporten blev indlæst.";
       }else{
            return "Den oploadede fil er ikke en 'Ticket Database Export'-fil";
       }

}
                     

if(isset($_FILES['file'])){

      // Where the file is going to be placed 
      $target_path = "imports/";

      if(!file_exists($target_path)){
            mkdir($target_path);
      }
      /* Add the original filename to our target path.  
      Result is "uploads/filename.extension" */
      $target_filepath = $target_path . basename( $_FILES['file']['name']);
      $filename=explode('.',$_FILES['file']['name']);
      
      if(end($filename) == "tde"){
            if(!file_exists($target_path."/".$_FILES['file']['name'])){
                  if(move_uploaded_file($_FILES['file']['tmp_name'], $target_filepath)) {
                        $message="Filen ".  basename( $_FILES['file']['name'])." blev oploadet.";
                        importTDE($target_filepath);
                        
                  }else{
                        $message="Der opstod en fejl under opload.";
                  }
            }else{
                  $message="Denne eksport er allerede blevet oploadet.";
            }
      }else{
            $message="Den oploadede fil er ikke en 'Ticket Database Export'-fil, eller har ikke suffix '.tde'";
      }

            
}

getThemeHeader();
getThemeTitle();


echo $message;


?>

<html>
<body>

<form action="bballticketclient_import.php" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Importer Fil" />
</form>

</body>
</html>