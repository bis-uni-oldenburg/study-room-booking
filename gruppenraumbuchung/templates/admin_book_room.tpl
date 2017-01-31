%header%
<h1>%~group_work_room_reservation% - Admin</h1>
<div id="admin-raumbuchung">
<form name="raumbuchung" method="post" action="admin_book_room.php">
Erster Buchungstag (Beispiel: 03.09.2010)<br><input name="datum_von" type="text"><br><br>
Letzter Buchungstag (Beispiel: 10.09.2010)<br><input name="datum_bis" type="text"><br><br>
Raum<br><select name="raum">%room_select%</select><br><br>
Uhrzeit (Beispiel: 8:00)<br>
von<br><input name="von" type="text"><br><br>
bis<br><input name="bis" type="text"><br><br>
<br><input type="submit" value="Abschicken">
</form>
</div>
%footer%