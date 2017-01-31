<div id="rb-container">
<table id="rb-uhrzeit" class="raumbuchung" style="float: left">
	<tr>
		<th class="datum">%~date%:</th>
	</tr>
	%optional bibliotheken1%
	<tr><th class="leerzelle-bibliotheken">&nbsp;</th></tr>
	%/optional bibliotheken1%
	<tr>
		<th class="arbeitsplatz-header">%~work_room%:</th>
	</tr>
	
	%loop_zeitabschnitte_uhrzeit%
	<tr>
		<th id="time-%period%" class="zeitabschnitt">%zeitabschnitt%</th>
	</tr>
	%/loop_zeitabschnitte_uhrzeit%
	<tr>
		<th class="zeitabschnitt"
			style="background-image: url(images/scroll-hg.gif); background-repeat: repeat-x; background-color: #d4d0c8">&nbsp;</th>
	</tr>
</table>
<div id="rb-scroll-container">
<table id="raumbuchung" class="raumbuchung">
	<tr>
		%loop_datum%
		<th colspan="%date_colspan%" class="datum">%datum%</th>
		<td></td>
		%/loop_datum%
	</tr>
	%optional bibliotheken%
	<tr class="bibliotheken-headline">
	%loop_bibliotheken%
		%bibliotheken_headlines%
		<td></td>
	%/loop_bibliotheken%
	%/optional bibliotheken%
	</tr>
	<tr>
		%loop_arbeitsplaetze% %arbeitsplaetze% %/loop_arbeitsplaetze%
	</tr>
	%loop_zeitabschnitte%
	<tr>
		%belegungen%
	</tr>
	%/loop_zeitabschnitte%
</table>
</div>
</div>



