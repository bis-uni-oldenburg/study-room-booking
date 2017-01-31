<div id="rb-static">
<table id="rb-uhrzeit" class="raumbuchung" style="float: left">
	<tr>
		<th class="datum">Datum:</th>
	</tr>
	%optional bibliotheken1%
	<tr><th class="leerzelle-bibliotheken">&nbsp;</th></tr>
	%/optional bibliotheken1%
	<tr>
		<th class="arbeitsplatz-header">Arbeitsplatz:</th>
	</tr>
	
	%loop_zeitabschnitte_uhrzeit%
	<tr>
		<th id="time-%period%" class="zeitabschnitt">%zeitabschnitt%</th>
	</tr>
	%/loop_zeitabschnitte_uhrzeit%
	
</table>

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



