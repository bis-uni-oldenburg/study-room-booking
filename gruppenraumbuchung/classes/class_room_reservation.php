<?php 
class RoomReservation
{
	// Members
	public $days_in_advance;
	public $segment_length;
	public $max_booking;
	public $number_of_rooms;
	public $reservations_per_day;
	public $reservations_per_week;
	public $admins;
	public $rooms;
	public $colors;
	public $locations;
	public $library_headlines;
	public $CONFIG;
	protected $access;
	protected $table;
	
	// Methods
	
	function __construct()
	{
		$this->access=new DB_Access("gap");
		$config=$this->CONFIG=Config::getValues();
		
		$this->days_in_advance=$config["days_in_advance"];
		$this->segment_length=$config["segment_length"];
		$this->max_booking=$config["max_booking"];
		$this->reservations_per_day=$config["reservations_per_day"];
		$this->reservations_per_week=$config["reservations_per_week"];
		$this->library_headlines=$config["library_headlines"];
		$this->delete_marked_segments=$config["delete_marked_segments"];
		
		$this->table="gap_reservations";
		
		$this->locations=$this->getLocations();
		$this->colors=$this->getColors();
		                   
		$this->rooms=$this->getRooms();
		$this->number_of_rooms=count($this->rooms);                   
		
		$this->admins=$this->getAdmins();
	}
	
	function getRoomTitleByNumber($number)
	{
		foreach($this->rooms as $room)
		{
			if($room["number"] == $number)
			{
				return $room["title"];
			}
		}
		
		return false;
	}
	
	function bookRoom($data)
	{
		$access=clone $this->access;
		$template=new Template;
		$table=$this->table;
		
		$datum=$data[0];
		$von=$data[1];
		$bis=$data[2];
		$raum=$data[3];
		//$login_id2=$data[4];
		$login_id2=Authentication::normalizeLoginId($data[4]);
		if(isset($data["validate"])) $validate=false;
		else $validate=true;
		
		session_start();
		if(isset($_SESSION["ub_user"])) $login_id1=$_SESSION["ub_user"];
		else return LOC::getLocale("alert_not_logged_in");
		
		if(isset($_SERVER['HTTP_USER_AGENT']) and preg_match("!package http!", $_SERVER['HTTP_USER_AGENT']))
		{
			return LOC::getLocale("alert_error");
		}
		
		if(!preg_match("!^[0-9]{3,4}$!", $von) or !preg_match("!^[0-9]{3,4}$!", $bis) or !preg_match("!^[0-9]{8}$!", $datum) or !preg_match("!^[0-9]+$!", $raum))
		{
		    return LOC::getLocale("alert_invalid_input");
		}
		
		if(($von % $this->segment_length) != 0 or ($bis % $this->segment_length) != 0)
		{
		    return LOC::getLocale("alert_invalid_time_input");
		}
				
		if($von == $bis) return LOC::getLocale("alert_start_and_end_time_identical");		
		if($von > $bis) return LOC::getLocale("alert_invalid_time_input");	
		if(($bis - $von) > $this->max_booking) return LOC::getLocale("alert_max_booking_time_exceeded");	
		if(!$this->roomExists($raum)) return LOC::getLocale("alert_room_not_existing");
		if(!$this->isAvailable($datum, $von, $bis, $raum)) return LOC::getLocale("alert_room_not_available");
		if($datum == date("Ymd")) return LOC::getLocale("alert_booking_not_allowed_for_today");
		
		$max_date = date('Ymd', strtotime("+" . ($this->days_in_advance - 1) . " day"));
		if($datum > $max_date) return LOC::getLocale("alert_booking_not_allowed_after_$max_date");
		
		if($login_id1 and $this->isAdmin($login_id1)) $status=1;
		else 
		{
			if($login_id1==$login_id2) return LOC::getLocale("alert_login_ids_identical");
			if($validate and ($this->dayQuotaExhausted($login_id1, $datum) or $this->dayQuotaExhausted($login_id2, $datum)))
			{
				return LOC::getLocale("alert_reservations_per_day");
			}
			if($validate and !Authentication::isValidLoginId($login_id2)) 
			{
				return LOC::getLocale("login_id2_not_valid");
			}
			if($validate and ($this->weekQuotaExhausted($login_id1, $datum) or $this->weekQuotaExhausted($login_id2, $datum))) 
			{
				return LOC::getLocale("alert_reservations_per_week");
			}
			$status=0;
		}
		
		$aktionszeit=date("YmdHis");
					
		$sql="INSERT INTO $table (login_id1, login_id2, datum, von, bis, raum, aktionszeit, status) VALUES ('$login_id1', '$login_id2', '$datum', '$von', '$bis', '$raum', '$aktionszeit', '$status')";
		if($access->executeSQL($sql))
		{
			$response="ok";
		}
		else $response=LOC::getLocale("alert_save_failed");

		return $response;
	}
	
	function adminBookRoom($data, $login_id1)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		extract($data);
		
		if(!$datum_bis) $datum_bis=$datum_von;
		$datum_von=preg_replace("!^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$!", "$3$2$1", $datum_von);
		$datum_bis=preg_replace("!^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$!", "$3$2$1", $datum_bis);
		
		$date_difference=Time::getDateDifference($datum_von, $datum_bis);
		
		//$von=preg_replace("!^([0-9]{1,2})\:([0-9]{2})$!", "$1$2", $von);
		//$bis=preg_replace("!^([0-9]{1,2})\:([0-9]{2})$!", "$1$2", $bis);
		
		$von=$von_std . $von_min;
		$bis=$bis_std . $bis_min;
		
		$location=$this->getLocationByNumber($raum);
		
		$aktionszeit=date("YmdHis");
		$error="";
		$successful=0;
		
		if(!$intervall) $intervall = 1;

		for($t=0; $t < ($date_difference + 1); $t = $t + $intervall)
		{
			$day_timestamp=mktime(0, 0, 0, substr($datum_von, 4, 2), substr($datum_von, 6, 2) + $t, substr($datum_von, 0, 4));
			$day=date("Ymd", $day_timestamp);
			
			$ot=new OT;
			$days_ot=$ot->getDaysOT($day_timestamp, $location);
			if(!$days_ot) continue;
			
			$days_ot=explode("-", $days_ot);
			$open=$days_ot[0];
			$close=$days_ot[1];
			
			if($open == 1 or $close == 1) 
			{
				$error.="<br>Buchung am " . date("d.m.Y", $day_timestamp) . " nicht m&ouml;glich.";
				continue;
			}
			
			if($von < $open) $von=$open;
			if($bis > $close) $bis=$close;
			$von=preg_replace("!^0!", "", $von);
			$bis=preg_replace("!^0!", "", $bis);

			$sql="INSERT INTO $table (login_id1, login_id2, datum, von, bis, raum, aktionszeit, status) 
			      VALUES ('$login_id1', 'admin', '$day', '$von', '$bis', '$raum', '$aktionszeit', '1')";
			if(!$access->executeSQL($sql)) $error.="<br>Speichern der Buchung für den $datum_von gescheitert.";
			else $successful++;
		}
		
		if(!$successful)
		{
			$feedback="<strong>Buchungen konnten nicht vorgenommen werden.</strong>";
		}
		else 
		{
			$feedback="<strong>$successful Buchungen wurden vorgenommen.</strong>";
		}
		
		if($error) $feedback.=$error;
		
		return $feedback;
	}
	
	function confirm($data)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$datum=$data[0];
		$von=$data[1];
		$raum=$data[2];
		
		session_start();

		if(isset($_SESSION["ub_user"])) $login_id2=$_SESSION["ub_user"];
		else $login_id2="";
		
		$sql="UPDATE $table SET status=1 WHERE datum='$datum' AND von='$von' AND raum='$raum' AND login_id2='$login_id2'";
		if($access->executeSQL($sql)) $response="ok";
		else $response=LOC::getLocale("alert_confirmation_save_failed");
		
		return $response;
	}
	
	function delete($data)
	{
	    session_start();
	    
	    if(isset($_SESSION["ub_user"])) $login_id=$_SESSION["ub_user"];
	    else return 0;
	    
		$access=clone $this->access;
		$table=$this->table;
		
		$datum=$data[0];
		$von=$data[1];
		$raum=$data[2];
		
		$status=$this->getStatus($datum, $von, $raum);
		
		$sql="DELETE FROM $table WHERE datum='$datum' AND von='$von' AND raum='$raum' AND (login_id1='$login_id' OR login_id2='$login_id') LIMIT 1";

		if($access->executeSQL($sql)) 
		{
		    if($access->connection->affected_rows == 0) return 0;
		    
			if($status) $response=LOC::getLocale("alert_reservation_deleted");
			else $response=LOC::getLocale("alert_marking_deleted");
		}
		else $response=0;
		
		return $response;
	}
	
	function setAsExpired($data)
	{
		$access=clone $this->access;
		$table=$this->table;
	
		$datum=$data[0];
		$von=$data[1];
		$raum=$data[2];
	
		$sql="UPDATE $table SET status=2 WHERE datum='$datum' AND von='$von' AND raum='$raum'";
		if($access->executeSQL($sql))
		{
			return true;
		}
		else return false;
	}
	
	function getBookings()
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$today=date("Ymd");
		$last_day=$this->getDate($this->days_in_advance, "Ymd");
		
		$sql="SELECT * FROM $table WHERE datum >='$today' AND datum <='$last_day' AND status < 2";
		$access->executeSQL($sql);
		if($access->rows)
		{
			$bookings=array();
			for($t=0; $t < $access->rows; $t++)
			{
				$id=$access->db_data[$t]["id"];
				$datum=$access->db_data[$t]["datum"];
				$von=$access->db_data[$t]["von"];
				$bis=$access->db_data[$t]["bis"];
				$raum=$access->db_data[$t]["raum"];
				$status=$access->db_data[$t]["status"];
				$login_id1=$access->db_data[$t]["login_id1"];
				$login_id2=$access->db_data[$t]["login_id2"];
				$aktionszeit=$access->db_data[$t]["aktionszeit"];
				
				$zeitabschnitt=$von;
				for($b=0; $b < (($bis-$von)/$this->segment_length); $b++)
				{
					$bookings[$datum][$zeitabschnitt][$raum]["status"]=$status;
					$bookings[$datum][$zeitabschnitt][$raum]["login_id1"]=$login_id1;
					$bookings[$datum][$zeitabschnitt][$raum]["login_id2"]=$login_id2;
					$bookings[$datum][$zeitabschnitt][$raum]["id"]=$id;
					$bookings[$datum][$zeitabschnitt][$raum]["aktionszeit"]=$aktionszeit;
					$zeitabschnitt+=$this->segment_length;
				}
			}
			return $bookings;
		}
		else return false;
	}
	
	function markingPeriodExpired($date, $booking)
	{
		if(!$this->delete_marked_segments)
		{
			if($date == date("Ymd")) return true;
			else return false;
		}
		else 
		{
			if($date == date("Ymd")) return true;
			
			if($booking["aktionszeit"] >= 20130116000000) // Regel gilt für Buchungen ab dem 16.01.13, 0:00 Uhr
			{
				$now=date("YmdHis");
				$hours_since_marking=Time::getTimeDifference($booking["aktionszeit"], $now);
				
				if($hours_since_marking >= $this->delete_marked_segments) 
				{
					return true;
				}
				else 
				{
					return false;
				}
			}
			else return false;
		}
	}
	
	function getBooking($datum, $von, $raum)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$sql="SELECT * FROM $table WHERE datum='$datum' AND von='$von' AND raum='$raum' AND status = 1";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0];
		}
		else return false;
	}
	
	function getStatus($datum, $von, $raum)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$sql="SELECT status FROM $table WHERE datum='$datum' AND von='$von' AND raum='$raum'";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["status"];
		}
		else return false;
	}
	
	
	function getLocationByNumber($number)
	{
		foreach($this->rooms as $room)
		{
			if($room["number"]==$number)
			{
				return $room["location"];
			}
		}
		
		return false;
	}
	
	function isAvailable($date, $von2, $bis2, $room)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$is_available=true;
		
		if(!$bis2) 
		{
			$sql="SELECT * FROM $table WHERE datum ='$date' AND von <= $von2 AND bis > $von2 AND raum = $room AND status < 2";
		}
		else 
		{
			$sql="SELECT * FROM $table WHERE datum ='$date' AND raum = $room AND status < 2 
															AND ((von = $von2 OR  bis = $bis2) 
															  OR (von < $von2 AND bis > $bis2) 
															  OR (von > $von2 AND bis < $bis2)
															  OR (von > $von2 AND bis > $bis2 AND von < $bis2)
															  OR (von < $von2 AND bis < $bis2 AND bis > $von2))";
		}
		
		$access->executeSQL($sql);
		
		if($access->rows) $is_available=false;
		else
		{
			$location=$this->getLocationByNumber($room);
			$ot=new OT;
			$days_ot=$ot->getDaysOT(mktime(0, 0, 0, substr($date,4,2), substr($date,6,2), substr($date,0,4)), $location);

			$days_ot=explode("-", $days_ot);
			$close=$this->convertTimeToDec($days_ot[1]);
			
			if($close < ($von2 + $this->segment_length)) $is_available=false;
		}
		
		return $is_available;
	}
	
	
	
	function dayQuotaExhausted($user, $date)
	{
		if(!$user) return true;
		
		$access=clone $this->access;
		$table=$this->table;
		
		$sql="SELECT id FROM $table WHERE (login_id1='$user' OR login_id2='$user') AND datum='$date' AND status < 2";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			if($access->rows >= $this->reservations_per_day) return true;
			else return false;
		}
		else return false;
	}
	
	function weekQuotaExhausted($user, $date)
	{
		if(!$user) return true;
		
		$access=clone $this->access;
		$table=$this->table;
		
		$month=substr($date,4,2);
		$day=substr($date,6,2);
		$year=substr($date,0,4);
		
		$today=mktime(0, 0, 0, $month, $day, $year);
		$today_week=date("N", $today);
		$monday=mktime(0, 0, 0, $month, $day-($today_week-1), $year);
		$sunday=mktime(0, 0, 0, $month, $day+(7-$today_week), $year);
		
		$monday=date("Ymd", $monday);
		$sunday=date("Ymd", $sunday);
		
		$sql="SELECT id FROM $table WHERE (login_id1='$user' OR login_id2='$user') AND datum >= '$monday' AND datum <= '$sunday' AND status < 2";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			if($access->rows >= $this->reservations_per_week) return true;
			else return false;
		}
		else return false;
	}
	
	function convertTimeFromDec($time)
	{
		if(preg_match("!^([0-9]{1,2})([0-9]{2})$!", $time, $matches))
		{
			$hours=$matches[1];
			$minutes=$matches[2];
			
			$time=sprintf("%02d:%02d", $hours, $minutes*0.6);
		}
		
		return $time;
	}
	
	function convertTimeToDec($time)
	{
		if(preg_match("!^([0-9]{1,2})([0-9]{2})$!", $time, $matches))
		{
			$hours=$matches[1];
			$minutes=$matches[2];
			
			$time=sprintf("%02d%02d", $hours, $minutes*(5/3));
		}
		
		return $time;
	}
	
	function getPossibleEndTimes($date, $time, $room)
	{
		$end=$time+$this->max_booking;
		
		$current_time=$time+$this->segment_length;
		$rows=$this->max_booking/$this->segment_length;
		
		$options=array();
		$selected=0;
		
		for($t=0; $t < $rows; $t++)
		{
			$is_available=$this->isAvailable($date, $current_time, false, $room);
			if(!$is_available or $t==($rows-1)) 
			{
				if(!$is_available) $break=true;
				else $break=false;
				$selected=$t;
			}
			else
			{
				$break=false;
			}
			$options[$t]=$current_time . "," . $this->convertTimeFromDec($current_time);
			
			if($break) break;
			
			$current_time+=$this->segment_length;
		}
		
		$options[]="selected,$selected";
		
		return implode(" ", $options);
	}
	
	function getDate($d, $format, $weekday=false)
	{
		$date=date($format, mktime(0, 0, 0, date("m"), date("d")+$d, date("Y")));
		
		if($weekday) $date=Time::getWeekDay(date("w", mktime(0, 0, 0, date("m"), date("d")+$d, date("Y")))) . "<br />$date";
		
		return $date;
	}
	
	function getLength($date, $time, $room)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$sql="SELECT (bis-von) AS length FROM $table WHERE datum='$date' AND von <= $time AND bis >= $time AND raum=$room";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["length"];
		}
		else 
		{
			return false;
		}
	}
	
	function getLengthById($id)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$sql="SELECT (bis-von) AS length FROM $table WHERE id=$id";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["length"];
		}
		else 
		{
			return false;
		}
	}
	
	function getOpenings()
	{
		$ot=new OT;
		
		$earliest_open=0;
		$latest_close=0;
		
		$openings=array();
		
		for($t=0; $t < $this->days_in_advance; $t++)
		{
			foreach($this->locations as $location => $name)
			{
				$days_ot=$ot->getDaysOT(mktime(0, 0, 0, date("m"), date("d")+$t, date("Y")), $location);
				
				if(!$days_ot or $days_ot==-1) 
				{
					$openings[$t][$location]["closed"]=true;
					$openings[$t][$location]["open"]=false;
					$openings[$t][$location]["close"]=false;
					$openings[$t][$location]["hours"]=false;
				}
				else 
				{
					$days_ot=explode("-", $days_ot);
					$open=$this->convertTimeToDec($days_ot[0]);
					$close=$this->convertTimeToDec($days_ot[1]);
					
					$openings[$t][$location]["closed"]=false;
					$openings[$t][$location]["open"]=$open;
					$openings[$t][$location]["close"]=$close;
					$openings[$t][$location]["hours"]=($close-$open);
					
					if(!$earliest_open or $earliest_open > $open) $earliest_open=$open;
					if(!$latest_close or $latest_close < $close) $latest_close=$close;	
				}
			}
		}
		
		$openings["earliest_open"]=$earliest_open;
		$openings["latest_close"]=$latest_close;
		$openings["max_hours"]=($latest_close-$earliest_open);
		$openings["max_segments"]=($openings["max_hours"]/$this->segment_length);
		
		return $openings;
	}
	
	function getRooms()
	{
		$access=clone $this->access;
		$table="gap_rooms";
		
		$sql="SELECT * FROM $table WHERE active = 1 ORDER BY position";
		$access->executeSQL($sql);
		
		return $access->db_data;
	}
	
	function roomExists($number)
	{
	    $access=clone $this->access;
	    $table="gap_rooms";
	    
	    $sql="SELECT * FROM $table WHERE active = 1 AND number = $number";
	    $access->executeSQL($sql);
	    
	    if($access->rows) return true;
	    else return false;
	}
	
	function getLocations($all=false)
	{
		$access=clone $this->access;
		$table="gap_locations";
		
		$sql="SELECT * FROM $table ORDER BY position";
		$access->executeSQL($sql);
		
		if($all) return $access->db_data;
		
		$locations=array();
		for($t=0; $t < $access->rows; $t++)
		{
			$short=$access->db_data[$t]["short"];
			$long=$access->db_data[$t]["long"];
			
			$locations[$short]=$long;
		}
		
		return $locations;
	}
	
	function getLocationHeadlines()
	{
		$locations=$this->getLocations(true);
		foreach($locations as $location)
		{
			$short=$location["short"];
			$headline_short=$location["headline_short"];
			$header=$location["header"];
			$colspan=0;
			foreach($this->rooms as $room)
			{
				if($room["location"] == $short) $colspan++;
			}
			
			$headlines[]="<td colspan=\"$colspan\" style=\"background-color:$header\">" . LOC::getLocale($short) . "</td>";
		}
		
		$headlines=implode("\n", $headlines);
		return $headlines;
	}
	
	function getColors()
	{
		$access=clone $this->access;
		$table="gap_locations";
		
		$sql="SELECT * FROM $table ORDER BY position";
		$access->executeSQL($sql);
		
		$colors=array();
		for($t=0; $t < $access->rows; $t++)
		{
			$short=$access->db_data[$t]["short"];
			$long=$access->db_data[$t]["long"];
			
			$colors[$short]=$access->db_data[$t];
		}
		
		return $colors;
	}
	
	function getAdmins()
	{
		$access=clone $this->access;
		$table="gap_admins";
		
		$sql="SELECT * FROM $table";
		$access->executeSQL($sql);
		
		$admins=array();
		for($t=0; $t < $access->rows; $t++)
		{
			$login_id=$access->db_data[$t]["login_id"];
			$admins[]=$login_id;
		}

		return $admins;
	}
	
	function isAdmin($login_id)
	{
		if($login_id and in_array($login_id, $this->admins)) return true;
		else return false;
	}
	
	
	// Statistics
	
	function getBookingHours($room, $period_start, $period_end=0)
	{
		$access=clone $this->access;
		$table=$this->table;
		
		$period_start_length=strlen($period_start);
		
		if($period_end)
		{
			$period_end_length=strlen($period_end);
			$where="LEFT(datum, $period_start_length) < '$period_start' " .
			       "AND LEFT(datum, $period_end_length) >= '$period_end'";
		}
		else
		{
			$where="LEFT(datum, $period_start_length)='$period_start'";
		}
		
		$sql="SELECT (bis - von) AS dauer FROM $table WHERE $where AND raum='$room'";
		
		$access->executeSQL($sql);

		$total_hours=0;
		for($t=0; $t < $access->rows; $t++)
		{
			$hours=$access->db_data[$t]['dauer'];

			$total_hours+=$hours;
		}
		
		return $total_hours/100;
	}
	

	
	
}

?>
