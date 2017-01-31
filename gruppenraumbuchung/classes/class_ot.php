<?php 

class OT
{
	private $access;
	private $table;
	private $tbl_departments;
	public $weekdays;
	public $weekdays_long;
	private $tbl_extra_days;
	private $german_days;
	
	function __construct()
	{
		$this->access=new DB_Access("gap");
		$this->table="gap_ot";
		$this->tbl_extra_days="gap_ot_extradays";
		$this->tbl_departments="gap_locations";
		$this->weekdays=array("mo", "di", "mi", "do", "fr", "sa", "so");
		
		$LOC=LOC::getLocales();
		$this->weekdays_long=array($LOC["monday"], 
		                           $LOC["tuesday"],
		                           $LOC["wednesday"], 
		                           $LOC["thursday"], 
		                           $LOC["friday"], 
		                           $LOC["saturday"], 
		                           $LOC["sunday"]
		);
		
		$this->german_days=array("Mon"=>"mo",
		                         "Tue"=>"di",
		                         "Wed"=>"mi",
		                         "Thu"=>"do",
		                         "Fri"=>"fr",
		                         "Sat"=>"sa",
		                         "Sun"=>"so");
	}
	
	public function getWeeksOT($department, $date=false)
	{
		if(!$date) $date=date("Ymd");
		$access=clone $this->access;
		
		$sql="SELECT * FROM $this->table WHERE department='$department'";
		$access->executeSQL($sql);
		
		$week_ots=false;
		for($t=0; $t < $access->rows; $t++)
		{
			$datum_von=$access->db_data[$t]["datum_von"];
			$datum_bis=$access->db_data[$t]["datum_bis"];
			
			if($date >= $datum_von and $date <= $datum_bis)
			{
				$weeks_ot=$access->db_data[$t];
				break;
			}
			
			if(!$datum_von and !$datum_bis) $weeks_ot=$access->db_data[$t];
		}
		
		return $weeks_ot;
	}
	
	function getShortWeeksOT($department, $xs, $date=false)
	{
		$weeks_ot=$this->getWeeksOT($department, $date);
		$previous_opt="";
		
		if(!$xs) $start_weekday=$this->weekdays_long[0];
		else $start_weekday=ucfirst($this->weekdays[0]);
		
		$the_days=array();
		$the_times=array();
		$duration_days=0;
		for($w=0; $w < count($this->weekdays); $w++)
		{
			$weekday=$this->weekdays[$w];
			
			if(!$xs) $weekday_long=$this->weekdays_long[$w];
			else $weekday_long=ucfirst($weekday);
			
			if(!$weeks_ot[$weekday]) $weeks_ot[$weekday]="geschlossen";
			else 
			{
				if($xs)
				{
					preg_match("!^([0-9]{2})([0-9]{2})\-([0-9]{2})([0-9]{2})$!", $weeks_ot[$weekday], $matches);
					
					$start_hour=$matches[1];
					$start_min=$matches[2];
					$end_hour=$matches[3];
					$end_min=$matches[4];
					
					$start_hour=preg_replace("!^0!", "", $start_hour);
					$end_hour=preg_replace("!^0!", "", $end_hour);
					
					$weeks_ot[$weekday]=$start_hour;
					if($start_min != "00") $weeks_ot[$weekday].=":$start_min";
					$weeks_ot[$weekday].="&ndash;$end_hour";
					if($end_min != "00") $weeks_ot[$weekday].=":$end_min";
					$weeks_ot[$weekday].=" Uhr";
				}
				else $weeks_ot[$weekday]=preg_replace("!^([0-9]{2})([0-9]{2})\-([0-9]{2})([0-9]{2})$!", "$1:$2&ndash;$3:$4 Uhr", $weeks_ot[$weekday]);
			}
			
			if($weeks_ot[$weekday] != $previous_opt and $w > 0) $changed_opt=true;
			else $changed_opt=false;
			
			if($w == count($this->weekdays)-1) $last_weekday=true;
			else $last_weekday=false;
			
			if($changed_opt or $last_weekday)
			{
				if($duration_days == 1)
				{
					$these_days=$start_weekday;
					if($last_weekday and !$changed_opt) $these_days.="/$weekday_long";
				}
				if($duration_days == 2)
				{
					if($last_weekday) $these_days="$start_weekday/$weekday_long";
					else 
					{
						if(!$xs) $weekday_long1=$this->weekdays_long[$w-1];
						else $weekday_long1=ucfirst($this->weekdays[$w-1]);
						$these_days="$start_weekday/" . $weekday_long1;
					}
				}
				if($duration_days > 2)
				{
					if($last_weekday) $these_days="$start_weekday - $weekday_long";
					else 
					{
						if(!$xs) $weekday_long1=$this->weekdays_long[$w-1];
						else $weekday_long1=ucfirst($this->weekdays[$w-1]);
						$these_days="$start_weekday - " . $weekday_long1;
					}
				}
	
				$the_days[]=$these_days;
				
				$the_times[]=$weeks_ot[$this->weekdays[$w-1]];
				
				$start_weekday=$weekday_long;
				$duration_days=0;
			}
	
			
			$duration_days++;
			$previous_opt=$weeks_ot[$weekday];
		}
		
		$days=implode("<br>", $the_days);
		$times=implode("<br>", $the_times);
		
		return array("days"	=> $days,
		             "times"=> $times);
	}
	
	function getDaysOT($timestamp, $department)
	{
		$access=clone $this->access;
		$table=$this->table;

		if($ed=$this->isExtraDay($timestamp, $department)) return $ed;
		else 
		{
			$day=date("D", $timestamp);
			$day=$this->german_days[$day];
			$ymd=date("Ymd", $timestamp);
			
			$weeks_ot=$this->getWeeksOT($department, $ymd);
			
			return $weeks_ot[$day];
		}
	}
	
	function getPeriodOT($datum_von, $datum_bis)
	{
		$date_diff=Time::getDateDifference($datum_von, $datum_bis);
		$periodOT=array();
		$departments=$this->getDepartments();
		
		for($t=0; $t < $date_diff+1; $t++)
		{
			$timestamp=mktime(0,0,0, substr($datum_von, 4, 2), substr($datum_von, 6, 2)+$t, substr($datum_von, 0, 4));
			$day=date("Ymd", $timestamp);
			$weekday=date("N", $timestamp);
			$weekday_long=$this->weekdays_long[$weekday-1];
			
			$days_ot=array();
			foreach($departments as $short => $long)
			{
				$days_ot[$short]=$this->getDaysOT(mktime(0, 0, 0, substr($day, 4, 2), substr($day, 6, 2), substr($day, 0, 4)), $short);
				if($days_ot[$short]==-1 or $days_ot[$short]==false) $days_ot[$short]="geschlossen";
				else $days_ot[$short].=" Uhr";
				
				$periodOT[$t][$short]=Time::HiToGermanTime($days_ot[$short]);
			}
			
			$periodOT[$t]["tag"]="$weekday_long, " . Time::YmdToGermanDate($day);
		}
		
		return $periodOT;
	}
	
	function isExtraDay($timestamp, $department)
	{
		$access=clone $this->access;
		$table=$this->tbl_extra_days;
		
		$ymd=date("Ymd", $timestamp);
		$md=date("md", $timestamp);
		
		$sql="SELECT uhrzeit_von, uhrzeit_bis FROM $table WHERE (department='' OR department='$department') AND (datum = '$ymd' OR datum = '$md')";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			$uhrzeit_von=$access->db_data[0]["uhrzeit_von"];
			$uhrzeit_bis=$access->db_data[0]["uhrzeit_bis"];
			
			if($uhrzeit_von) return "$uhrzeit_von-$uhrzeit_bis";
			else return -1;
		}
		else return false;
	}
	
	function getFirstChristmasOTDay()
	{
		$access=clone $this->access;
		$table=$this->tbl_extra_days;
		
		if(date("md") > "1101") $this_year=date("Y");
		else $this_year=date("Y") - 1;
		$next_year=$this_year + 1;
		
		$december=$this_year . "12";
		
		$sql="SELECT MIN(datum) AS first_day FROM $table WHERE LEFT(datum, 6)='$december' AND bemerkung REGEXP 'Weihnachten'";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["first_day"];
		}
		else return false;
	}
	
	function getLastNewYearOTDay()
	{
		$access=clone $this->access;
		$table=$this->tbl_extra_days;
		
		if(date("md") > "1101") $this_year=date("Y");
		else $this_year=date("Y") - 1;
		$next_year=$this_year + 1;
		
		$january=$next_year . "01";
		
		$sql="SELECT MAX(datum) AS last_day FROM $table WHERE LEFT(datum, 6)='$january' AND bemerkung REGEXP 'Neujahr'";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["last_day"];
		}
		else return false;
	}
	
	public function getDepartments()
	{
		$access=clone $this->access;
		
		$sql="SELECT * FROM $this->tbl_departments ORDER BY position";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			$departments=array();
			for($t=0; $t < $access->rows; $t++)
			{
				$department=$access->db_data[$t]["short"];
				$bezeichnung=$access->db_data[$t]["long"];
				
				$departments[$department]=$bezeichnung;
			}
			
			return $departments;
		}
		else return false;
	} 
	
}


?>