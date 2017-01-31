<?php 
// Klassen
require_once("classes/required_classes.php");
$rr=new RoomReservation();
header("content-type:text/plain");
echo json_encode($rr->rooms);


?>