<?php 
// Gruppenraumbuchung - Raumbuchung für Admins 
// Anwendung: gap
// Lars Heuer, 17.08.2010


// Klassen
require_once("classes/required_classes.php");

// Objekte
$template=new Template();
$template->enableLocalization();
$rr=new RoomReservation();

// Login und Berechtigungsabfrage
session_start();
if(!(isset($_SESSION["ub_user"]) and $rr->isAdmin($_SESSION["ub_user"])))
{
	echo "Keine Berechtigung!";
	exit;
}

if(!isset($_POST['datum_von']))
{
	$html=$template->getTemplate("admin_book_room");
	
	$room_select="\n<option selected>[Bitte auswählen]</option>";
	foreach($rr->rooms as $room)
	{
		$value=$room["number"];
		$text=$room["title"];
		$room_select.="\n<option value=\"$value\">$text</option>";
	}
	$output=$template->tplReplace($html, array("room_select" => $room_select,
	                                           "header"=>$template->getTemplate("header"),
	                                           "footer"=>$template->getTemplate("footer"),
	                                           "css"=>"",
						                       "javascript"=>""));
	echo $output;
}
else
{
	$meldung=$rr->adminBookRoom($_POST, $_SESSION["ub_user"]);
	echo $meldung;
	echo "<br><br><a href=\"admin_book_room.php\">Weitere Buchung durchführen</a>";
	echo "<br><a href=\"index.php\">Zur öffentlichen Buchungsseite</a>";
}

?>
