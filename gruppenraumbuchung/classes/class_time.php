<?php
// Class Time

class Time
{
	function getGermanWeekDay($day)
	{
		$weekdays=array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");	
		
		return $weekdays[$day];
	}
	
	function YmdToGermanDate($ymd, $month_as_word=false)
	{	
		if($month_as_word)
		{
			$german_months=array("01"=>"Januar",
		      			  		 "02"=>"Februar",
				       			 "03"=>"M�rz",
							     "04"=>"April",
							     "05"=>"Mai",
							     "06"=>"Juni",
							     "07"=>"Juli",
							     "08"=>"August",
							     "09"=>"September",
							     "10"=>"Oktober",
							     "11"=>"November",
							     "12"=>"Dezember");
							     
			if(preg_match("!^([0-9]{4})([0-9]{2})([0-9]{2})!", $ymd, $matches))
			{
				$year=$matches[1];
				$month=$matches[2];
				$day=$matches[3];
				
				$day=preg_replace("!^(0)([1-9]{1})$!", "$2", $day);
				$german_month=$german_months[$month];
				
				$german_date="$day.&nbsp;$german_month&nbsp;$year";
			}
			else $this->YmdToGermanDate($ymd, false);				     

		}
		else $german_date=preg_replace("!^([0-9]{4})([0-9]{2})([0-9]{2})!", "$3.$2.$1", $ymd);

		return $german_date;
	}
	
	function mysqlTimestampToGermanDateTime($mysql_timestamp, $seconds=false)
	{
		//2010-10-15 08:55:46
		$regex="!^([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})!";
		if($seconds)
		{
			$german_date_time=preg_replace($regex, "$3.$2.$1, $4:$5:$6", $ymd);
		}
		else $german_date_time=preg_replace($regex, "$3.$2.$1, $4:$5", $ymd);
		
		return $german_date_time;
	}
	
	function YmdHisToGermanDate($ymdhis, $format="d.m.Y, H:i Uhr")
	{
		$regex="!([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})!";
		
		if(preg_match($regex, $ymdhis))
		{
			$format=str_replace("d", "$3", $format);
			$format=str_replace("m", "$2", $format);
			$format=str_replace("Y", "$1", $format);
			$format=str_replace("H", "$4", $format);
			$format=str_replace("i", "$5", $format);
			$format=str_replace("s", "$6", $format);
			
			$date=preg_replace($regex, $format, $ymdhis);
		}
		else $date=$ymdhis;
		
		return $date;
	}
	
	function germanDateToYmd($german_date)
	{
		if(preg_match("!([0-9]{1,2})(?:\.|\/|\-)([0-9]{1,2})(?:\.|\/|\-)([0-9]{2,4})!", $german_date, $matches))
		{
			if(strlen($matches[3])==2) $matches[3]="19" . $matches[3];
			return sprintf("%04d%02d%02d", $matches[3], $matches[2], $matches[1]);
		}
		else return $german_date;
	}
	
	function HiToGermanTime($hi, $uhr=false)
	{
		if($uhr) $uhr=" Uhr";
		else $uhr="";
		
		$german_time=preg_replace("!([0-9]{2})([0-9]{2})!", "$1:$2$uhr", $hi);
		
		return $german_time;
	}
	
	function germanTimeToHi($german_time)
	{	
		if(preg_match("!([0-9]{1,2})[:\.]([0-9]{2})( Uhr)?!", $german_time, $matches))
		{
			return sprintf("%02d%02d", $matches[1], $matches[2]);
		}
		else return $german_time;
	}
	
	function getDateDifference($date1, $date2)
	{
		$regex="!^(2[0-9]{3})([0-1]?[0-9]{1})([0-3]?[0-9]{1})$!";
		if(preg_match($regex, $date1, $matches))
		{
			$year1=$matches[1];
			$month1=preg_replace("!^0!", "", $matches[2]);
			$day1=preg_replace("!^0!", "", $matches[3]);
		}
		if(preg_match($regex, $date2, $matches))
		{
			$year2=$matches[1];
			$month2=preg_replace("!^0!", "", $matches[2]);
			$day2=preg_replace("!^0!", "", $matches[3]);
		}

		$datetime1 = mktime(0, 0, 0, $month1, $day1, $year1);
		$datetime2 = mktime(0, 0, 0, $month2, $day2, $year2);
		$difference = $datetime2 - $datetime1;
		$date_diff = $difference / 86400;
		$date_diff=floor($date_diff);
		
		return $date_diff;
	}
	
	// Difference in hours
	function getTimeDifference($time1, $time2)
	{
		$regex="!^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$!";
		if(preg_match($regex, $time1, $matches))
		{
			$year1=$matches[1];
			$month1=$matches[2];
			$day1=$matches[3];
			$hour1=$matches[4];
			$minute1=$matches[5];
			$second1=$matches[6];
		}
		if(preg_match($regex, $time2, $matches))
		{
			$year2=$matches[1];
			$month2=$matches[2];
			$day2=$matches[3];
			$hour2=$matches[4];
			$minute2=$matches[5];
			$second2=$matches[6];
		}
	
		$datetime1 = mktime($hour1, $minute1, $second1, $month1, $day1, $year1);
		$datetime2 = mktime($hour2, $minute2, $second2, $month2, $day2, $year2);
		$difference = $datetime2 - $datetime1;
		$hours_diff = $difference / 3600;
		$hours_diff=floor($hours_diff);
	
		return $hours_diff;
	}
	
}


?>