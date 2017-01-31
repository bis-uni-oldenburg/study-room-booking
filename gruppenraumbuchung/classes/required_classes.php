<?php
// Required Classes

$required_classes=array("class_db_access.php",
                        "class_config.php",
                        "class_loc.php",
                        "class_template.php",
                        "class_time.php",
					    "class_ot.php",
                        "class_authentication.php",
                        "class_room_reservation.php");
                        
foreach($required_classes as $required_class)
{
	require_once($required_class);
}

?>