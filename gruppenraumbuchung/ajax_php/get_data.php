<?php
// Get data

require_once("../classes/required_classes.php");

$rr=new RoomReservation();

$action=$_GET['action'];

switch($action)
{
	case "convert_time":
		$time=$_GET['value'];
		
		if(!preg_match("!^[0-9]{3,4}$!", $time))
		{
		    echo $time;
		    exit;
		}
		
		$time=$rr->convertTimeFromDec($time);
		echo utf8_encode($time);
		break;	
	
	case "convert_date":
		$date=$_GET['value'];
		
		if(!preg_match("!^[0-9]{8}$!", $date))
		{
		    echo $date;
		    exit;
		}
		
		$date=@Time::YmdToGermanDate($date);
		echo utf8_encode($date);
		break;	
		
	case "get_end_times":
		$dtr=$_GET['value'];
		
		if(!preg_match("!^[0-9]{8}\,[0-9]{3,4}\,[0-9]{1,3}$!", $dtr))
		{
		    echo $dtr;
		    exit;
		}
		
		$p=explode(",", $dtr);
		$times=$rr->getPossibleEndTimes($p[0], $p[1], $p[2]);
		echo utf8_encode($times);
		break;
		
	case "get_room_title":
		$room_number=$_GET['value'];
		
		if(!preg_match("!^[0-9]{1,3}$!", $room_number))
		{
		    echo $room_number;
		    exit;
		}
		
		$room_title=$rr->getRoomTitleByNumber($room_number);
		echo utf8_encode($room_title);
		break;
		
	case "day_quota_exhausted":
		$data=$_GET['value'];
		
		if(!preg_match("!^[^\ ,\'\’]+\,[0-9]{8}$!", $data))
		{
		    echo 1;
		    exit;
		}
		
		$d=explode(",", $data);
		$user=$d[0];
		$date=$d[1];
		echo $rr->dayQuotaExhausted($user, $date);
		break;

}

?>
