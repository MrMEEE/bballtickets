<?php

require("../../connect.php");
require("../../config.php");
require("../../checkConfig.php");
require("../../checkLogin.php");
require("../../checkAdmin.php");
require("../../theme.php");

getThemeHeader();

getThemeTitle("Import/Eksport");

require("../../menu.php");

require("bballtickets_check_database.php");

?>
<a href="bballtickets_importexport_genexport.php">Eksport Data</a>

<?php
getThemeBottom();

?>
