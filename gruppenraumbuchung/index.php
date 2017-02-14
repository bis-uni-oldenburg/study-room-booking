<?php 
// Gruppenraumbuchung
// Anwendung: gap
// Lars Heuer - 03.2009/09.2011

// Klassen
require_once("classes/required_classes.php");

// Objekte
$template=new Template();
$template->enableLocalization();

$rr=new RoomReservation();

// Sprache
// Cookie expiration
$cookie_expires=10; //Config::getValue("days_until_cookie_expires");

// Language setting
if(isset($_GET["language"])) 
{
	$language=$_GET["language"];
}
else if(isset($_COOKIE["gap_language"])) 
{
	$language=$_COOKIE["gap_language"];
}
else 
{
	$language=LOC::getDefaultLanguage();
}

setcookie("gap_language", $language, time()+60*60*24 * $cookie_expires); // Expires after $cookie_expires days
$_COOKIE["gap_language"]=$language;

// Delete über URL
// Muster: ?delete=[datum jjjjmmtt],[uhrzeit hhmm],[raum]&pw=[passwort]
// ?delete=20091209,1200,3&pw=xxx
if(isset($_GET['delete']))
{
	$pw=$_GET['pw'];
	if($pw=="xxx")
	{
		$delete=$_GET['delete'];
		$data=explode(",", $delete);
		$rr->delete($data);
	}
}

// Create über URL
// ?create=20091209,1200,1500,3&pw=lk943def
if(isset($_GET['create']))
{
	$pw=$_GET['pw'];
	if($pw=="lk943def")
	{
		$create=$_GET['create'];
		$data=explode(",", $create);
		if(!isset($data[4])) $data[]="admin";
		$data["validate"]=false;
		$rr->bookRoom($data);
		$c_data=array($data[0], $data[1], $data[3]);
		$rr->confirm($c_data);
	}
}

// Login
session_start();
if(isset($_GET['login']) or isset($_GET['logout']))
{
	$auth=new Authentication($rr->CONFIG["auth_method"]);
	if(isset($_GET['logout'])) $ub_user=false;
	else $ub_user=$auth->getUser();
}
else 
{
	$ub_user=0;
	$_SESSION["ub_user"]=$ub_user;
}

// Load layout frame
$html=$template->getTemplate("index");

// Replacements
$javascript="<script language=\"javascript\" type=\"text/javascript\">\nvar segment_length=" . $rr->segment_length . ";\n</script>\n";

// Include bookings
$included=true;
include("ajax_php/bookings.php");

if(!$ub_user) 
{
	$login_onclick="login()";
	$login="Login";
}
else 
{
	$login_onclick="logout()";
	$login="Logout";
}

$is_admin=$rr->isAdmin($ub_user);

$output=$template->tplReplace($html, array(
                            "header"=>$template->getTemplate("header"),
                            "reservation"=>$reservation,
                            "legend"=>$template->getTemplate("legend"),
                            "news"=>$template->getTemplate("news"),
                            "info_buttons"=>$template->getTemplate("info_buttons"),
							"rooms"=>$template->getTemplate("rooms"),
                            "dialogs"=>$template->getTemplate("dialogs"),
                            "footer"=>$template->getTemplate("footer"),
                            "javascript"=>$javascript,
                            "css"=>"",
                            "login"=>$login,
                            "login_onclick"=>$login_onclick));

$output=$template->displayOptionalArea($output, "admin", $is_admin);
$output=$template->displayOptionalArea($output, "logged_in", $ub_user);


echo $output;
?>
