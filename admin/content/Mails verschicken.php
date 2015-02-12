<h1>Teamnummern verschicken</h1>
<?
set_time_limit(120);
$registration_table_name = "anmeldungen";
$tables_table_name = "tische";

$result = $db->query("SELECT $registration_table_name.*, $tables_table_name.tisch, IF(secondMailDate = 0, '-', DATE_FORMAT(secondMailDate, '%e.%c.%Y %k:%i:%s')) AS MAILDATUM FROM $registration_table_name LEFT JOIN $tables_table_name on $registration_table_name.teamNumber = $tables_table_name.teamNumber WHERE $registration_table_name.teamNumber <> 0 ORDER BY $registration_table_name.teamNumber");
$tableContent = "";

while($row = $result->fetch_assoc()) {
	if(isset($_POST['sendmails'])) {
		if($row['secondMailDate'] != 0) {
			$mailString = "<span style='color: orange'>wurde bereits versendet</span>";
		} elseif($row['tisch'] == "") {
			$mailString = "<span style='color: orange'>Noch keine zugewiesene Tischnummer</span>";
		} elseif(writeMail($row)) {
			$mailString = "<span style='color: green'>E-Mail jetzt versendet</span>";
			$db->query("UPDATE $registration_table_name SET secondMailDate = CURRENT_TIMESTAMP WHERE id = ".$row['id']." LIMIT 1");
		} else {
			$mailString = "<span style='color: red'>Fehler beim versenden</span>";
		}
	} elseif(isset($_POST['sendsinglemail']) && $row['teamNumber'] == $_POST['teamNumber']) {
		echo "Soll wirklich eine Mail an das Team mit der Teamnummer ".$row['teamNumber']." versendet werden?<br>";
		if($row['tisch'] == "") {
			echo "<span style='color: red'>Das Team hat bisher keine Tischnummer zugewiesen bekommen</span><br>";
		}
		if($row['secondMailDate'] != 0) {
			echo "<span style='color: red'>Es wurde bereits eine Mail an dieses Team versendet</span><br>";
		}
		echo "<form method='POST'><input type='submit' name='reallysend' value='Mail an das Team mit der Teamnummer ".$row['teamNumber']." verschicken'><input type='hidden' name='teamNumber' value=".$row['teamNumber']."></form><br><br>";
		$mailString = "<span style='color: orange'>Soll jetzt versendet werden</span>";
	} elseif(isset($_POST['reallysend']) && $row['teamNumber'] == $_POST['teamNumber']) {
		if(writeMail($row)) {
			$mailString = "<span style='color: green'>E-Mail jetzt versendet</span>";
			echo "Mail an das Team mit der Teamnummer ".$row['teamNumber']." wurde erfolgreich versendet<br><br>";
			$db->query("UPDATE $registration_table_name SET secondMailDate = CURRENT_TIMESTAMP WHERE id = ".$row['id']." LIMIT 1");
		} else {
			$mailString = "<span style='color: red'>Fehler beim versenden</span>";
		}
	} else {
		$mailString = $row['MAILDATUM'];
	}
	$tableContent .= '<tr><td>'.$row['schule'].'</td><td>'.$row['teamName'].'</td><td>'.$row['teamNumber'].'</td><td>'.$row['tisch'].'</td><td>'.$mailString.'</td><td><form method="POST"><input name="teamNumber" type="hidden" value="'.$row['teamNumber'].'"><input type="submit" value="einzelne E-Mail verschicken" name="sendsinglemail"></form></td></tr>';
}

function writeMail($row) {

	$css = ".content{font-size: 13pt; width: 750px; text-align: justify; font-family: Arial, Helvetica, sans-serif} .important table td{font-weight: bold; font-size: 14pt;} a{color: #06C} a:link{text-decoration: none} a:hover{text-decoration: underline}";
	/*$body = "<div class='content'>Liebe Schülerinnen und Schüler,<br><br>

diese Nachricht enthält wichtige Informationen für den Wettbewerb zum Tag der Mathematik am kommenden Samstag, den 5. Mai 2012.
<br><br>
Bei der Anmeldung hat Ihr Team folgende Team- und Tischnummer erhalten:<br>
<div class='important'><table><tr><td>Teamname:</td><td>".$row['teamName']."</td></tr>
<tr><td>Team-Nr.:</td><td>".toRoman($row['stufe'])."/".$row['teamNumber']."</td></tr>
<tr><td>Tisch-Nr.:</td><td>".$row['tisch']."</td></tr></table></div>
<br>

<strong>Notieren Sie bitte beide Nummern sorgfältig und halten Sie diese am Wettbewerbstag bereit.</strong><br><br>

Die Team-Nr. dient zur Identifikation der Wettbewerbsteilnehmer und muss auf allen Lösungsblättern unbedingt eingetragen werden (andernfalls kann die Lösung nicht berücksichtigt werden).
<br><br>
Die Tisch-Nr. wird benötigt, um in der Mensa der FU Berlin den zugeteilten Tisch zu finden. Im Eingangsbereich der Mensa werden dazu Saalpläne aushängen, die bei der Orientierung helfen.
<br><br>
Die Mensa wird ab 8.30 Uhr geöffnet sein. Der Wettbewerb beginnt pünktlich um 9.00 Uhr. Sie sollten daher spätestens um 8.50 Uhr Ihre Plätze eingenommen haben.
<br>
Zusammen mit den Wettbewerbsaufgaben erhalten Sie um 9.00 Uhr einem Umschlag mit:
<ul>
<li>Essensgutscheinen für die bestellten Mittagessen</li>
<li>einem Datenblatt mit den Namen der bei der Anmeldung angegebenen Teammitglieder</li>
</ul>
Bitte prüfen Sie diese Angaben sofort nach Erhalt und vermerken Sie eventuelle Änderungen. Das Datenblatt wird bald nach Beginn des Wettbewerbs zusammen mit dem beschrifteten Umschlag eingesammelt, der das Geld für das Mittagessen enthält.
<br><br>
Alle weiteren Informationen zum Wettbewerb finden Sie in einem Merkblatt, das Sie unter<br>
<a href='http://tdm.math.fu-berlin.de/download/TdM-Wettbewerb_Merkblatt.pdf'>http://tdm.math.fu-berlin.de/download/TdM-Wettbewerb_Merkblatt.pdf</a> herunterladen können.
<br><br>
Wir wünschen viel Spaß beim Lösen der Aufgaben und einen erfolgreichen Wettbewerb!
<br><br>
Alexander Bockmayr</div>";*/

	$body = "<div class='content'>Liebe Wettbewerbsteilnehmer,<br><br>

inzwischen gibt es Fotos und sogar ein Video zum Tag der Mathematik
auf unseren Webseiten.<br><br>

<a href='http://tdm.math.fu-berlin.de/index.html'>http://tdm.math.fu-berlin.de/index.html</a><br><br>

Weitere Fotos findet man hier:<br><br>

<a href='http://tinyurl.com/d5ezwb9'>http://tinyurl.com/d5ezwb9</a><br><br>

Die Ergebnisse des Wettbewerbes findet man unter folgendem Link:<br><br>

<a href='http://tdm.math.fu-berlin.de/data/results/results.html'>http://tdm.math.fu-berlin.de/data/results/results.html</a><br><br>

In Kürze werden auch die Musterlösungen ins Netz gestellt.<br><br>

Herzliche Grüße und viel Spaß mit den Fotos!<br><br><br>


Holger Reich<br><br>

(Koordinator des Tages der Mathematik 2012)</div>";
	$text = "<html><head><title>Weitere Informationen zum Tag der Mathematik 2012</title><style type='text/css'>".$css."</style></head><body>".$body."</body></html>";
	$receiver = $row['email'];
	$sender = "noreply@tdm.math.fu-berlin.de";
	//$subject = "Weitere Informationen zum Tag der Mathematik 2012";
	$subject = "Fotos und Video zum Tag der Mathematik 2012";
	$header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: ".$sender."\r\nX-Mailer: PHP ".phpversion();
	//Richte eine Adresse als Return-Path ein, damit nicht zugestellte Mails erkannt werden
	$additionalparams = "-rdmvwebsite@zedat.fu-berlin.de";
	
	if(isset($simulate) && $simulate = true) {
		echo $body;
		return true;
	} else {
		//Zur Sicherheit auskommentiert. Damit Mails versendet werden sollen, müssen die Kommentare vor der folgenden Zeile wieder entfernt werden
		//return mail($receiver, $subject, $text, $header, $additionalparams);
	}
}

function toRoman($int) {
	if($int == 1) {
		return "I";
	} elseif($int == 2) {
		return "II";
	} elseif($int == 3) {
		return "III";
	} else {
		return "";
	}
}
?>
<form method="POST"><input type="submit" value="E-Mails mit Teamnummern und Tischnummer an alle Teams verschicken" name="sendmails"></form><br><br>
<table class="table table-bordered table-striped middle" style="width: 1000px;"><thead><tr><th>Schule</th><th>Teamname</th><th>Teamnummer</th><th>Tisch</th><th>Mail verschickt</th><th>Mail versenden</th></tr></thead><tbody>
<? echo $tableContent; ?>
</tbody></table>