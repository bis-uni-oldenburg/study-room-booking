# study-room-booking
A web application that enables library users to book library study rooms online.

<html>
<head>
<meta charset="utf-8">
<title>BIS - Bibliotheks- und Informationssystem der Universität Oldenburg: Software zur Reservierung von Gruppenarbeitsplätzen</title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	width: 900px;
	margin-left: auto;
	margin-right: auto;
}
code { font-size: 16px; }

img { border: 1px solid #999 }

</style>
</head>
<body>
<h1>Web-Anwendung:<br>Online-Reservierung von Gruppenarbeitsplätzen</h1>
<a name="download"></a>
<h4>Download:</h4>

<ul id="versionen">
<li><a href="gap_04_2016.zip">Version vom 07.04.2016 (ZIP-Archiv)</a></li>
<li><a href="gap_10_2015.zip">Version vom 02.10.2015 (ZIP-Archiv)</a></li>
<li><a href="gap_11_2013.zip">Version vom 10.11.2013 (ZIP-Archiv)</a></li>
<li><a href="gap_10_2011.zip">Version vom 04.10.2011 (ZIP-Archiv)</a></li>

</ul>


<h3>Folgende Institutionen setzen dieses System zurzeit (Oktober 2015) ein:</h3>
<ul>
<li>UB Oldenburg</li>
<li>UB Heidelberg</li>
<li>UB Rostock</li>
<li>UB Würzburg</li>
<li>UB TU Berlin</li>
<li>UB Siegen</li>
<li>UB Hildesheim</li>
<li>UB Frankfurt</li>
<li>UB HSU Hamburg</li>
<li>HLB Fulda</li>

</ul>

<h3>Hinweise zur Nachnutzung:</h3>
<h4>Systemvoraussetzungen:</h4>
<p>PHP 5.x, MySQL</p>
<p>Die Anwendung wurde auf Basis von PHP 5.1.6 und MySQL 5.0.77 entwickelt. <br>
Beim Betrieb der Anwendung mit PHP 5.2.x und 5.3.[1-6] kommt es zu Darstellungsproblemen (multiples Wiederholen der Buchungstabelle). Diese können in der php.ini durch Setzen des Wertes von pcre.backtrack_limit auf 1000000 behoben werden.</p>

<h4>Installation:</h4>
<ol>
	<li>ZIP-Archiv <a href="#download">herunterladen</a></li>
	<li>ZIP-Archiv entpacken</li>
	<li>Das Verzeichnis <code>gruppenraumbuchung</code> auf den Webserver kopieren.</li>
	<li>Eine neue MySQL-Datenbank einrichten (InnoDB, utf8_unicode_ci).</li>
	<li>Die Datei <code>datenbanktabellen.sql</code> aus dem Verzeichnis <code>gap_datenbank</code> in die neue Datenbank importieren.
	Um Probleme beim Import zu vermeiden: Datei-Inhalt in PHPMyAdmin ins SQL-Feld kopieren und ausführen.</li>
	<li>Im Skript <code>gruppenraumbuchung/classes/class_db_access.php</code> im Konstruktor (<code>function __construct</code>) die Zugangsdaten
	für die neu eingerichtete Datenbank eintragen.</li>
	<li>Die Anwendung im Browser starten (<code>index.php</code>). </li>
	<li>Login ist im Testmodus über die Login-ID '12345' bzw. '12346' und das Passwort 'test' möglich. 
	Um den lokalen Authentifizierungsdienst (z. B. LDAP) zu integrieren, muss das Skript <code>gruppenraumbuchung/classes/class_authentication.php</code>
	(Klassenmethode <code>accessGranted</code>) angepasst werden.</li>
	<li>In der Datenbanktabelle <code>gap_rooms</code> werden die vorhandenen Gruppenarbeitsräume eingetragen.</li>
	<li>In der Datenbanktabelle <code>gap_locations</code> wird/werden der Standort/die Standorte Ihrer Einrichtung eingetragen.</li>
	<li>In der Datenbanktabelle <code>gap_ot</code> werden die Öffnungszeiten der Standorte angegeben.
	Einträge ohne Werte für <code>datum_von</code> und <code>datum_bis</code> stellen die regulären Öffnungszeiten dar.</li>
	<li>In der Datenbanktabelle <code>gap_ot_extradays</code> können einzelne Schließtage (ohne Uhrzeitangabe) bzw. Tage 
	mit veränderten Öffnungszeiten eingetragen werden. Optional kann ein Eintrag auf einen Standort (<code>department</code>) begrenzt werden.
	Vierstellige Einträge ohne Jahresangabe (z. B. 1003 = 03.10.) gelten für jedes Jahr.</li>
	<li>In der Datenbanktabelle <code>gap_config</code> können Sie ggf. Änderungen an der Grundkonfiguration vornehmen. Erläuterungen
	zu den einzelnen Punkten stehen im Feld <code>description</code>.</li>
	<li>In der Datenbanktabelle <code>gap_localization</code> können Sie Änderungen an den Standardtexten der Anwendung vornehmen.
	Es ist hier möglich, z. B. eine englischsprachige Version der Anwendung zu konfigurieren (URL-Parameter: language=en). Englischsprachige
	Versionen der Hinweistexte, die sich nach Klick auf 'So funktioniert es' und 'Regeln' öffnen, liegen im Verzeichnis 
	<code>gruppenraumbuchung/info</code> bereits vor.
	</li>
	<li>In der Datenbanktabelle <code>gap_admins</code> können Admin-Benutzer mit erweiterten Rechten eingetragen werden. Diese Benutzer dürfen
		<ul>
			<li>auf einer Admin-Reservierungsseite Räume über selbst definierte Zeiträume reservieren. Ein Link auf diese Seite erscheint nach dem Login
			hinter dem Button 'Regeln'.</li>
			<li>über die normale Buchungsseite Räume reservieren, ohne dass ein zweiter Benutzer die Reservierung bestätigen muss.</li>
		</ul>
	</li>
	<li>Durch Anpassung des Templates <code>gruppenraumbuchung/templates/header.tpl</code> und des Stylesheets 
	<code>gruppenraumbuchung/css/gruppenraeume.css</code> kann das Layout der Seite verändert werden.</li>
	<li>Passen Sie die Templates <code>templates/rooms.tpl</code> und <code>templates/legend.tpl</code>an.</li>
	<li>Passen Sie die Hinweistexte im Verzeichnis <code>info</code> an.</li>
</ol>

<br>
<h3>Screenshots:</h3>
<p>Durch Klick auf ein Zeitsegment erscheint der Dialog zur Vormerkung eines Raums:</p>
<img src="screenshots/gap-1.png" />
<br /><p>Benutzer 12345 hat sich angemeldet und die Vormerkung durch Klick auf das Zeitsegment in eine Reservierung umgewandelt. Der Raum ist reserviert:</p>
<img src="screenshots/gap-2.png" />  
<br><br>
<h4>Kontakt:</h4>
Lars Heuer <a href="mailto:lars.heuer@uni-oldenburg.de">lars.heuer@uni-oldenburg.de</a>
<p>Für Anregungen und Hinweise auf Anwendungsfehler bzw. Vorschläge zur Verbesserung der Software bin ich dankbar.  </p>
<p>Stand: 07.04.2016</p>
</body>
</html>