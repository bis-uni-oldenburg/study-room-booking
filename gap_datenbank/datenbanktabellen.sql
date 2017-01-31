--
-- Datenbank: `gap`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_admins`
--

CREATE TABLE IF NOT EXISTS `gap_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login_id` varchar(45) collate utf8_unicode_ci NOT NULL,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `gap_admins`
--

INSERT INTO `gap_admins` (`id`, `login_id`, `name`) VALUES
(1, '12345', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_config`
--

CREATE TABLE IF NOT EXISTS `gap_config` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `var` varchar(100) collate utf8_unicode_ci NOT NULL,
  `value` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `gap_config`
--

INSERT INTO `gap_config` (`id`, `var`, `value`, `description`) VALUES
(1, 'days_in_advance', '8', 'Anzahl der im Reservierungsplan angezeigten Tage.'),
(2, 'segment_length', '50', 'Länge eines Plansegments\r\n100 = 1 Stunde\r\n50 = 30 Minuten\r\n25 = 15 Minuten\r\n'),
(3, 'max_booking', '300', 'Maximale Dauer einer Buchung\r\n300 = 3 Stunden'),
(4, 'reservations_per_day', '1', 'Anzahl der möglichen Reservierungen pro Tag pro Login-ID'),
(5, 'reservations_per_week', '3', 'Anzahl der möglichen Reservierungen pro Woche pro Login-ID'),
(6, 'library_headlines', '1', 'Nur relevant bei mehreren Standorten mit unterschiedlichen Öffnungszeiten: \r\n1 = Standortüberschrift (z. B. Zentralbibliothek) wird im Reservierungsplan angezeigt\r\n0 = Standortüberschrift wird nicht angezeigt'),
(7, 'scrollable_table', '1', '1 = Reservierungsplan wird in scrollbarem Fenster angezeigt (sinnvoll bei vielen Räumen)\r\n0 = Klassische Ansicht ohne Scrollen'),
(8, 'auth_method', 'custom_login', 'Authentifizierungs-Methode\r\ncas = Authentifizierung via CAS\r\ncustom_login = selbstdefinierte Methode'),
(9, 'default_language', 'de', 'Standard-Sprache'),
(10, 'delete_marked_segments', '24', 'Zeitpunkt, an dem unbestätigte Vormerkungen gelöscht werden.\r\n0 = Eine Vormerkung verfällt, wenn sie nicht spätestens am Tag vorm Termin in eine Reservierung umgewandelt worden ist.\r\n24 = Eine Vormerkung verfällt, wenn sie nicht innerhalb von 24 Stunden bestätigt worden ist.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_localization`
--

CREATE TABLE IF NOT EXISTS `gap_localization` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `key_term` varchar(100) collate utf8_unicode_ci NOT NULL,
  `language` varchar(10) collate utf8_unicode_ci NOT NULL,
  `term` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=64 ;

--
-- Daten für Tabelle `gap_localization`
--

INSERT INTO `gap_localization` (`id`, `key_term`, `language`, `term`) VALUES
(1, 'alert_login_ids_identical', 'de', 'Die Login-IDs dürfen nicht identisch sein.'),
(2, 'alert_reservations_per_day', 'de', 'Sie dürfen pro Tag nur 1 Reservierung vornehmen.'),
(3, 'alert_reservations_per_week', 'de', 'Sie dürfen pro Woche nur 3 Reservierungen vornehmen.'),
(4, 'room_not_available', 'de', 'Der Gruppenarbeitsplatz ist in diesem Zeitraum nicht verfügbar.'),
(5, 'alert_save_failed', 'de', 'Ein Fehler ist aufgetreten. Ihre Reservierung konnte nicht gespeichert werden.'),
(6, 'alert_confirmation_save_failed', 'de', 'Ein Fehler ist aufgetreten. Ihre Vormerkung konnte nicht bestätigt werden.'),
(7, 'alert_reservation_deleted', 'de', 'Ihre Reservierung wurde gelöscht.'),
(8, 'alert_marking_deleted', 'de', 'Ihre Vormerkung wurde gelöscht.'),
(9, 'type_login_id2', 'de', 'Bitte geben Sie die Login-ID von Benutzer 2 an!'),
(10, 'mark_group_work_room', 'de', 'Gruppenarbeitsplatz vormerken'),
(11, 'login_id_of_user', 'de', 'Login-ID von Benutzer'),
(12, 'date_on', 'de', 'am'),
(13, 'between', 'de', 'zwischen'),
(14, 'and', 'de', 'und'),
(15, 'time_uhr', 'de', 'Uhr'),
(16, 'room', 'de', 'Raum'),
(17, 'closed', 'de', 'geschlossen'),
(18, 'time_from', 'de', 'ab'),
(19, 'confirm_marking', 'de', 'Bestätigen'),
(20, 'delete_marking', 'de', 'Vormerkung Löschen'),
(21, 'print_receipt', 'de', 'Beleg drucken'),
(22, 'login_required', 'de', 'Login erforderlich'),
(23, 'please_login', 'de', 'Bitte melden Sie sich an, um eine Reservierung vorzunehmen.'),
(24, 'no_reservation', 'de', 'Reservierung nicht möglich'),
(25, 'no_reservation_for_current_day', 'de', 'Für den laufenden Tag können keine Reservierungen vorgenommen werden.'),
(26, 'mark', 'de', 'Vormerken'),
(27, 'delete_reservation', 'de', 'Reservierung löschen'),
(28, 'reservation', 'de', 'Reservierung'),
(29, 'alert_marking_saved', 'de', 'Ihre Vormerkung wurde gespeichert.'),
(30, 'alert_marking_confirmed', 'de', 'Ihre Vormerkung wurde bestätigt.'),
(31, 'alert_deletion_not_possible', 'de', 'Ein Fehler ist aufgetreten. Löschen nicht möglich.'),
(32, 'reservation_receipt', 'de', 'Reservierungsbeleg'),
(33, 'marking', 'de', 'Vormerkung'),
(34, 'group_work_room_reservation', 'de', 'Reservierung von Gruppenarbeitsplätzen'),
(35, 'how_it_works', 'de', 'So funktioniert es'),
(36, 'rules', 'de', 'Regeln'),
(37, 'room_free', 'de', 'Arbeitsplatz ist frei'),
(38, 'room_marked', 'de', 'Arbeitsplatz ist vorgemerkt'),
(39, 'room_reserved', 'de', 'Arbeitsplatz ist reserviert'),
(40, 'room_marked_by_you', 'de', 'Arbeitsplatz wurde von Ihnen vorgemerkt'),
(41, 'room_reserved_by_you', 'de', 'Arbeitsplatz wurde von Ihnen reserviert'),
(42, 'group_work_rooms', 'de', 'Gruppenarbeitsräume'),
(43, 'group_work_room', 'de', 'Gruppenarbeitsraum'),
(44, 'legend', 'de', 'Legende'),
(45, 'floor', 'de', 'Ebene'),
(46, 'photo', 'de', 'Foto'),
(47, 'central_library', 'de', 'Zentralbibliothek'),
(48, 'branch_library', 'de', 'Bereichsbibliothek'),
(49, 'no_online_reservation', 'de', 'Kann nicht online gebucht werden!'),
(50, 'monday', 'de', 'Montag'),
(51, 'tuesday', 'de', 'Dienstag'),
(52, 'wednesday', 'de', 'Mittwoch'),
(53, 'thursday', 'de', 'Donnerstag'),
(54, 'friday', 'de', 'Freitag'),
(55, 'saturday', 'de', 'Samstag'),
(56, 'sunday', 'de', 'Sonntag'),
(57, 'today', 'de', 'heute'),
(58, 'date', 'de', 'Datum'),
(59, 'work_room', 'de', 'Arbeitsplatz'),
(60, 'institution', 'de', 'Universitätsbibliothek Oldenburg'),
(61, 'the_users', 'de', 'Die Benutzer'),
(62, 'users', 'de', 'Benutzer'),
(63, 'day_time', 'de', 'Uhrzeit'),
(64, 'login_id2_not_valid', 'de', 'Die Login-ID ist ungültig.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_locations`
--

CREATE TABLE IF NOT EXISTS `gap_locations` (
  `id` tinyint(3) unsigned NOT NULL,
  `short` varchar(45) collate utf8_unicode_ci NOT NULL,
  `long` varchar(150) collate utf8_unicode_ci NOT NULL,
  `headline_short` varchar(100) collate utf8_unicode_ci NOT NULL,
  `header` varchar(20) collate utf8_unicode_ci NOT NULL,
  `free` varchar(20) collate utf8_unicode_ci NOT NULL,
  `marked` varchar(20) collate utf8_unicode_ci NOT NULL,
  `marked_by_me` varchar(20) collate utf8_unicode_ci NOT NULL,
  `occupied` varchar(20) collate utf8_unicode_ci NOT NULL,
  `occupied_by_me` varchar(20) collate utf8_unicode_ci NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `gap_locations`
--

INSERT INTO `gap_locations` (`id`, `short`, `long`, `headline_short`, `header`, `free`, `marked`, `marked_by_me`, `occupied`, `occupied_by_me`, `position`) VALUES
(1, 'zentralbibliothek', 'Zentralbibliothek', 'Zentralbibliothek', '#365F8C', '#9CCB5A', 'orange', '#CCCCCC', '#DF2E3B', '#999999', 1),
(2, 'wechloy', 'Bereichsbibliothek', 'Wechloy', '#365F8C', '#7c9952', 'orange', '#CCCCCC', '#DF2E3B', '#999999', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_ot`
--

CREATE TABLE IF NOT EXISTS `gap_ot` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `department` varchar(45) collate utf8_unicode_ci NOT NULL,
  `datum_von` varchar(8) collate utf8_unicode_ci NOT NULL,
  `datum_bis` varchar(8) collate utf8_unicode_ci NOT NULL,
  `mo` varchar(10) collate utf8_unicode_ci NOT NULL,
  `di` varchar(10) collate utf8_unicode_ci NOT NULL,
  `mi` varchar(10) collate utf8_unicode_ci NOT NULL,
  `do` varchar(10) collate utf8_unicode_ci NOT NULL,
  `fr` varchar(10) collate utf8_unicode_ci NOT NULL,
  `sa` varchar(10) collate utf8_unicode_ci NOT NULL,
  `so` varchar(10) collate utf8_unicode_ci NOT NULL,
  `bezeichnung` varchar(45) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Daten für Tabelle `gap_ot`
--

INSERT INTO `gap_ot` (`id`, `department`, `datum_von`, `datum_bis`, `mo`, `di`, `mi`, `do`, `fr`, `sa`, `so`, `bezeichnung`) VALUES
(1, 'wechloy', '', '', '0800-2000', '0800-2000', '0800-2000', '0800-2000', '0800-2000', '', '', ''),
(3, 'zentralbibliothek', '', '', '0800-2400', '0800-2400', '0800-2400', '0800-2400', '0800-2400', '1000-1800', '1000-1800', ''),
(4, 'zentralbibliothek', '20110730', '20110918', '0900-2000', '0900-2000', '0900-2000', '0900-2000', '0900-2000', '', '', 'Sommerpause'),
(19, 'wechloy', '20110723', '20110930', '0900-1700', '0900-1700', '0900-1700', '0900-1700', '0900-1700', '', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_ot_extradays`
--

CREATE TABLE IF NOT EXISTS `gap_ot_extradays` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `datum` varchar(8) collate utf8_unicode_ci NOT NULL,
  `department` varchar(100) collate utf8_unicode_ci NOT NULL,
  `uhrzeit_von` varchar(4) collate utf8_unicode_ci NOT NULL,
  `uhrzeit_bis` varchar(4) collate utf8_unicode_ci NOT NULL,
  `bemerkung` varchar(150) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48 ;

--
-- Daten für Tabelle `gap_ot_extradays`
--

INSERT INTO `gap_ot_extradays` (`id`, `datum`, `department`, `uhrzeit_von`, `uhrzeit_bis`, `bemerkung`) VALUES
(3, '1231', '', '', '', 'Silvester'),
(5, '1224', '', '', '', 'Heilig Abend'),
(6, '1225', '', '', '', '1. Weihnachtstag'),
(7, '1226', '', '', '', '2. Weihnachtstag'),
(8, '0101', '', '', '', 'Neujahr'),
(9, '1003', '', '', '', 'Tag der Deutschen Einheit'),
(14, '0501', '', '', '', 'Maifeiertag'),
(47, '20110901', 'zentralbibliothek', '0900', '1700', 'Wissenschaftssoiree 2011');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gap_reservations`
--

CREATE TABLE IF NOT EXISTS `gap_reservations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `department` varchar(100) collate utf8_unicode_ci NOT NULL,
  `raum` tinyint(3) unsigned NOT NULL,
  `login_id1` varchar(8) collate utf8_unicode_ci NOT NULL,
  `login_id2` varchar(8) collate utf8_unicode_ci NOT NULL,
  `datum` varchar(8) collate utf8_unicode_ci NOT NULL,
  `von` varchar(4) collate utf8_unicode_ci NOT NULL,
  `bis` varchar(4) collate utf8_unicode_ci NOT NULL,
  `aktionszeit` varchar(14) collate utf8_unicode_ci NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5811 ;



--
-- Tabellenstruktur für Tabelle `gap_rooms`
--

CREATE TABLE IF NOT EXISTS `gap_rooms` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `number` tinyint(3) unsigned NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Daten für Tabelle `gap_rooms`
--

INSERT INTO `gap_rooms` (`id`, `location`, `number`, `position`, `title`, `active`) VALUES
(1, 'zentralbibliothek', 1, 5, '2.1', 1),
(2, 'zentralbibliothek', 2, 10, '2.2', 1),
(3, 'zentralbibliothek', 3, 15, '3.1', 1),
(4, 'zentralbibliothek', 4, 20, '3.2', 1),
(5, 'zentralbibliothek', 5, 25, '3.3', 1),
(6, 'zentralbibliothek', 6, 30, '4.1', 1),
(7, 'zentralbibliothek', 7, 35, '4.3', 1),
(8, 'wechloy', 8, 40, '1', 1),
(9, 'wechloy', 9, 45, '2', 1),
(10, 'wechloy', 10, 50, '3', 1),
(11, 'wechloy', 11, 55, '4', 1),
(12, 'wechloy', 12, 60, '5', 1),
(14, 'zentralbibliothek', 13, 13, '2.4', 1);
