<?php 

// Gruppenraumbuchung
// Drucken des Belegs
// Lars Heuer

// Klassen
require_once("classes/required_classes.php");

// Login und Berechtigungsabfrage
session_start();
if(!(isset($_SESSION["ub_user"]) and $_SESSION["ub_user"]))
{
	echo "Keine Berechtigung!";
	exit;
}

// Objekte
$template=new Template();
$template->enableLocalization();

$rr=new RoomReservation();

$print=$template->getTemplate("print");

$datum=$_GET['datum'];
$von=$_GET['von'];
$raum=$_GET['raum'];

if(!preg_match("!^[0-9]{8}$!", $datum) || !preg_match("!^[0-9]{3,4}$!", $von) || !preg_match("!^[0-9]{1,3}$!", $raum)) 
{
    echo "Error";
    exit;   
}

if($data=$rr->getBooking($datum, $von, $raum))
{
	$data["raum"]=$rr->getRoomTitleByNumber($_GET['raum']);
	$data["von"]=$rr->convertTimeFromDec($data["von"]);
	$data["bis"]=$rr->convertTimeFromDec($data["bis"]);
	$data["datum"]=preg_replace("!([0-9]{4})([0-9]{2})([0-9]{2})!", "$3.$2.$1", $data["datum"]);
	
	$output=$template->tplReplace($print, $data);
}
else $output="false";

echo $output;
?>
