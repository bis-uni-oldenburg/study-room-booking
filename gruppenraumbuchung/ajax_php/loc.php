<?php 
// Returns loc values via ajax requests

// Load class
require_once("../classes/class_db_access.php");
require_once("../classes/class_config.php");
require_once("../classes/class_loc.php");

if(!isset($_POST["key_term"])) 
{
	echo ""; exit;
}
else $key_term=$_POST["key_term"];

echo utf8_encode(LOC::getLocale($key_term));
?>