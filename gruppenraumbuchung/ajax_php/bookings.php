<?php
// Gruppenraumbuchung
// Anwendung: gap
// Lars Heuer - 03.2009/09.2011/12.2013

if(!isset($included))
{
	require_once("../classes/required_classes.php");
	
	session_start();
	$ub_user=$_SESSION["ub_user"];
	
	$template=new Template();
	$template->enableLocalization();
	
	$rr=new RoomReservation();
	
	$path="../";
}
else $path="";

if($rr->CONFIG["scrollable_table"]) 
{
	$reservation_tpl=$template->getTemplate("reservation", $path);
}
else 
{
	$reservation_tpl=$template->getTemplate("reservation_static", $path);
}

// Load booking data and opening times
$bookings=$rr->getBookings();
$openings=$rr->getOpenings();

// Build booking table
$tpl_data=array();
for($d=0; $d < $rr->days_in_advance; $d++)
{
	if($d) 
	{
		$tpl_data[$d]["datum"]=$rr->getDate($d, "d.m.Y", true);
	}
	else 
	{
		$tpl_data[$d]["datum"]=LOC::getLocale("today");
	}
	$date_ymd=$rr->getDate($d, "Ymd");
	$tpl_data[$d]["date_colspan"]=$rr->number_of_rooms;
	$tpl_data[$d]["arbeitsplaetze"]="";
	for($r=0; $r < $rr->number_of_rooms; $r++)
	{
		$room=$rr->rooms[$r]["number"];
		$title=$rr->rooms[$r]["title"];
		$location=$rr->rooms[$r]["location"];
		$tpl_data[$d]["arbeitsplaetze"].="<th class=\"arbeitsplatz\" id=\"room-" . $date_ymd . "-" . $room . "\" title=\"$title\">" . "<img class=\"room-number\" src=\"images/raumnummern/" . $room . ".gif\">" . "</th>";
	}
	$tpl_data[$d]["arbeitsplaetze"].="<td></td>";
}

$reservation=$template->tplLoop($tpl_data, $reservation_tpl, "loop_datum");
$reservation=$template->displayOptionalArea($reservation, "bibliotheken", $rr->library_headlines);
$reservation=$template->displayOptionalArea($reservation, "bibliotheken1", $rr->library_headlines);

if($rr->library_headlines) 
{
	$reservation=$template->tplReplace($reservation, array("bibliotheken_headlines" => $rr->getLocationHeadlines()));
	$reservation=$template->tplLoop($tpl_data, $reservation, "loop_bibliotheken");
}

$reservation=$template->tplLoop($tpl_data, $reservation, "loop_arbeitsplaetze");

$tpl_data2=array();
$tpl_data3=array();
$current_time=$openings["earliest_open"];
$current_time=preg_replace("!^0([0-9]{3})$!", "$1", $current_time);

for($z=0; $z < $openings["max_segments"]; $z++)
{
	$von=$rr->convertTimeFromDec($current_time);
	$bis=$rr->convertTimeFromDec($current_time+$rr->segment_length);

	if($bis=="24:00") $bis="00:00";
	// Zeitabschnitt in <span> verpackt, damit im IE7 das whitespace:nowrap funktioniert
	$tpl_data3[$z]["zeitabschnitt"]="<span>$von&nbsp;-&nbsp;$bis</span>";
	$tpl_data3[$z]["period"]=$current_time;
	$tpl_data2[$z]["belegungen"]="";
	
	for($t=0; $t < $rr->days_in_advance; $t++)
	{
		$date=$rr->getDate($t, "Ymd");
		$rr_date=$date;
		$tpl_data2[$z]["belegungen"].="";

		for($r=0; $r < $rr->number_of_rooms; $r++)
		{
			$room=$rr->rooms[$r]["number"];
			$location=$rr->rooms[$r]["location"];
			$colors=$rr->colors[$location];
			
			$closed=$openings[$t][$location]["closed"];
			$open=$openings[$t][$location]["open"];
			$close=$openings[$t][$location]["close"];
		
			if((!$closed and (($von < $open) or ($von > $close))) or $closed)
			{
				$tpl_data2[$z]["belegungen"].="<td style=\"background-color: #e8e3e3\" title=\"" . LOC::getLocale("closed") . "\">&nbsp;</td>";
				
			}
			else 
			{
				if(isset($bookings[$date][$current_time][$room])) 
				{
					$current_segment_booking=$bookings[$date][$current_time][$room];
					
					$status=$current_segment_booking["status"];
					if(!$status and $rr->markingPeriodExpired($date, $current_segment_booking)) 
					{
						$rr->setAsExpired(array($date, $current_time, $room));
						$bookings=$rr->getBookings();
					}
					else 
					{
						if(isset($bookings[$date][($current_time - $rr->segment_length)][$room]))
						{
							$prev_segment_booking=$bookings[$date][($current_time - $rr->segment_length)][$room];
						}
						else $prev_segment_booking=false;
						
						if($prev_segment_booking and ($prev_segment_booking["id"] == $current_segment_booking["id"]))
						{
							continue;
						}
						else 
						{		
							if(!$status) $color=$colors["marked"];
							else $color=$colors["occupied"];
							
							//$number_of_rows=($rr->getLength($date, $current_time, ($r+1)))/$rr->segment_length;
							$number_of_rows=($rr->getLengthById($current_segment_booking["id"]))/$rr->segment_length;
							$rowspan=" rowspan=\"$number_of_rows\"";
							
							if($ub_user) 
							{
								$login_id1=$current_segment_booking["login_id1"];
								$login_id2=$current_segment_booking["login_id2"];
								
								if($ub_user==$login_id1 or $ub_user==$login_id2)
								{
									if($ub_user==$login_id1) 
									{
										$confirm=0;
									}
									else 
									{
										$confirm=1;
									}
									
									if(!$status) $color=$colors["marked_by_me"];
									else 
									{
										$color=$colors["occupied_by_me"];
										$confirm=2;
									}
									
									$onclick=" onclick=\"getConfirmationForm('$date', $current_time, " . $room . ", $confirm)\"";
								}
								else 
								{
									$onclick="";
								}
							}
							else $onclick=" onclick=\"getLoginAlert()\"";
							
							$style=" style=\"background-color: $color\"";
							
						}
					}
				}
				else 
				{
					
					$color=$colors["free"];
					$style=" style=\"background-color: $color\"";
					$rowspan="";

					if($date == date("Ymd"))
					{
						$onclick=" onclick=\"getTodayAlert();\"";
					}
					else if($ub_user) 
					{
						$onclick=" onclick=\"getBookingForm('$date', $current_time, " . $room . ")\"";
					}
					else 
					{
						$onclick=" onclick=\"getLoginAlert()\"";
					}
				}
				
				$title=$rr->rooms[$r]["title"];
				
				$td_content="&nbsp;";

				$tpl_data2[$z]["belegungen"].="<td id=\"cell-$date-$current_time-" . $room . "\"$onclick$style$rowspan onmouseover=\"showRoomAndTime($date, $current_time, $room)\" onmouseout=\"hideRoomAndTime($date, $current_time, $room)\" title=\"" . LOC::getLocale("room") . " $title, $von-$bis Uhr\">$td_content</td>";
			}
		}
		
		$tpl_data2[$z]["belegungen"].="<td style=\"width: 1px\"></td>\n";	
	}
	
	$current_time+=$rr->segment_length;
}
$reservation=$template->tplLoop($tpl_data2, $reservation, "loop_zeitabschnitte");
$reservation=$template->tplLoop($tpl_data3, $reservation, "loop_zeitabschnitte_uhrzeit"); 

if(!isset($included)) echo $reservation;
?>