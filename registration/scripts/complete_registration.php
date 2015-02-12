<?
/*
Dieses Skript fügt die Anmeldedaten in die Datenbank ein, wenn bei der Anmeldung auf "Daten bestätigen" geklickt wird.
Es werden prepared statements benutzt, um eine MySQL injection zu verhindern.

Die IP-Adresse, von der die Anmeldung abgeschickt wird, wird anonymisiert in der Form
sha1($_SERVER['REMOTE_ADDR'].$salt)
gespeichert. Sie ist somit nicht direkt auslesbar. Im Falle von vielen "Scherzanmeldungen" von einer einzigen IP-Adresse
lassen sich diese jedoch einfach raussuchen und gezielt löschen, da mit an Sicherheit grenzender Wahrscheinlichkeit
angenommen werden kann, dass der gleiche Eintrag in der IP-Adress-Spalte von der gleichen IP-Adresse ausgeht.

Im erfolgsfall wird eine Bestätigungsmail an die angegebene E-Mail Adresse geschickt, dies geschieht in der Funktion
writeConfirmationMail().
Diese Funktion kann auf die aktuelle Session zugreifen und somit die Anmeldedaten benutzen.
Wenn dieses Skript wieder benutzt wird, muss natürlich der Text in dieser Funktion abgeändert werden.

*/
$doc = new DOMDocument();
$doc->formatOutput = true;
$answer = $doc->createElement("registrationAnswer");
$doc->appendChild($answer);

if(isset($_GET['key'])) {
	include("../../scripts/db_connect.php");
	include("variables.php");
	$salt = "X6LSbNd3hidf9MTh2mA8oN3PiubHq0";
	
	if($_GET['key'] != "undefined") {
		session_id($_GET['key']);
	}
	session_start();
	
	//print_r($_SESSION);
	
	if(isset($_SESSION['stufe']) && isset($_SESSION['count']) && isset($_SESSION['schule']) && 
	isset($_SESSION['region']) && isset($_SESSION['kontakt']) && isset($_SESSION['email'])) { 
		if(mysqli_connect_errno() == 0) {
		
			$sql = "INSERT INTO anmeldungen (stufe, count, schule, bezirk, kontakt, email, name1, vorname1, klasse1, essen1, name2, 
			vorname2, klasse2, essen2, name3, vorname3, klasse3, essen3, name4, vorname4, klasse4, essen4, name5, vorname5, klasse5, 
			essen5, bezirkName, teamName, begleitEssen, remote_addr) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$eintrag = $db->prepare($sql);
			$eintrag->bind_param('iisissssiissiissiissiissiissss', $_SESSION['stufe'], $_SESSION['count'], $_SESSION['schule'],
			$_SESSION['region'], $_SESSION['kontakt'], $_SESSION['email'], $_SESSION['name1'], $_SESSION['vorname1'], 
			$_SESSION['klasse1'], $_SESSION['essen1'], $_SESSION['name2'], $_SESSION['vorname2'], $_SESSION['klasse2'], 
			$_SESSION['essen2'], $_SESSION['name3'], $_SESSION['vorname3'], $_SESSION['klasse3'], $_SESSION['essen3'], 
			$_SESSION['name4'], $_SESSION['vorname4'], $_SESSION['klasse4'], $_SESSION['essen4'], $_SESSION['name5'], 
			$_SESSION['vorname5'], $_SESSION['klasse5'], $_SESSION['essen5'], $_SESSION['bezirkName'], $_SESSION['teamName'], $_SESSION['begleitEssen'], sha1($_SERVER['REMOTE_ADDR'].$salt));
			
			$eintrag->execute();
			
			if($eintrag->affected_rows == 1) {
				addStatusToAnswer($doc, $answer, "4", "Registration complete");
				writeConfirmationMail();
				session_unset();
				session_destroy();
			} else {
				addStatusToAnswer($doc, $answer, "3", "Error inserting in database");
			}
		} else {
			addStatusToAnswer($doc, $answer, "2", "Error connecting to database");
		}
	} else {
		addStatusToAnswer($doc, $answer, "1", "No session under this key");
	}
} else {
	addStatusToAnswer($doc, $answer, "0", "No correct request");
}

header("Content-Type: text/xml");
echo $doc->saveXML();

function addStatusToAnswer($doc, $answer, $status, $text) {
	$statusElem = $doc->createElement("status");
	$statusElem->appendChild($doc->createTextNode($status));
	
	$textElem = $doc->createElement("text");
	$textElem->appendChild($doc->createTextNode($text));
	
	$answer->appendChild($statusElem);
	$answer->appendChild($textElem);
}

function writeConfirmationMail() {
	include("variables.php");
	
	if ($_SESSION['stufe'] == 1) {
		$stufe = "Stufe I (Klasse 7 und 8)";
	} elseif($_SESSION['stufe'] == 2) {
		$stufe = "Stufe II (Klasse 9 und 10)";
	} else {
		$stufe = "Stufe III (Klasse 11 und 12)";
	}
	
	$css = ".content{font-size: 13pt; width: 650px; text-align: justify; font-family: Arial, Helvetica, sans-serif} hr{width: 100%} table td{padding: 2px 2px 1px; line-height: 14pt; text-align: left; vertical-align: top} .left{width: 150px; font-weight: bold} .right{width:220px} a{color: #06C} a:link{text-decoration: none} a:hover{text-decoration: underline}";
	$body = '<div class="content"><p>Sie haben erfolgreich ein Schülerteam zum Tag der Mathematik 2012 angemeldet.<br>Dieser findet statt am Samstag, den 5. Mai 2012 an der Freien Universität Berlin.<br>Die Daten des angemeldeten Teams sind:</p>
	<table style="margin-top: 20px">
<tbody><tr><td class="left">Allgemein</td><td class="right">Stufe</td><td>'.$stufe.'</td></tr>
<tr><td></td><td class="right">Name der Schule</td><td>'.$_SESSION['schule'].'</td></tr>
<tr><td></td><td class="right">Region</td><td>'.$regions[$_SESSION['region']].'</td></tr>
<tr><td></td><td class="right">Teamname</td><td>'.$_SESSION['teamName'].'</td></tr>
<tr><td></td><td class="right">Kontaktperson</td><td>'.$_SESSION['kontakt'].'</td></tr>
<tr><td></td><td class="right">Kontakt-E-Mail</td><td>'.$_SESSION['email'].'</td></tr>
<tr><td></td><td class="right">Essen für Begleitperson</td><td>'.$essen[$_SESSION['begleitEssen']].'</td></tr>
<tr><td></td><td class="right">Anzahl Team-Mitglieder</td><td>'.$_SESSION['count'].'</td></tr>
</tbody></table>';

	for($i = 1; $i <= $_SESSION['count']; $i++) {
		if($i == $_SESSION['count']) {
			$style = " style='margin-bottom: 20px'";
		} else {
			$style = "";
		}
		$body .= '<hr><table class="table-plain"'.$style.'>
		<tbody><tr><td class="left">Teammitglied '.$i.'</td><td class="right">Vorname</td><td>'.$_SESSION['vorname'.$i].'</td></tr>
		<tr><td></td><td class="right">Nachname</td><td>'.$_SESSION['name'.$i].'</td></tr>
		<tr><td></td><td class="right">Klasse</td><td>'.$_SESSION['klasse'.$i].'. Klasse</td></tr>
		<tr><td></td><td class="right">Mittagessen</td><td>'.$essen[$_SESSION['essen'.$i]].'</td></tr>
		</tbody></table>';
	}
	$body .= "Die Teamnummer und weitere Informationen erhalten Sie in einer E-Mail am 2. Mai.<br>";
	$body .= "Bitte beachten Sie: Es kann eventuell einige Tage dauern, bis das angemeldete Team auf der TdM-Webseite erscheint.<br><br>";
	$body .= "Weitere Informationen zum Tag der Mathematik 2012 gibt es auf den Webseiten unter:<br><a href='http://tdm.math.fu-berlin.de'>http://tdm.math.fu-berlin.de</a><br><br>
	Informationen speziell zum Wettbewerb finden Sie auf der Wettbewerbsseite unter:<br><a href='http://tdm.math.fu-berlin.de/data/competition.html'>http://tdm.math.fu-berlin.de/data/competition.html</a></div>";
	$text = "<html><head><title>Anmeldung zum Tag der Mathematik 2012</title><style type='text/css'>".$css."</style></head><body>".$body."</body></html>";
	$receiver = $_SESSION['email'];
	$sender = "noreply@tdm.math.fu-berlin.de";
	$subject = "Anmeldung zum Tag der Mathematik 2012";
	$header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: ".$sender."\r\nX-Mailer: PHP ".phpversion();
	//Richte eine Adresse als Return-Path ein, damit nicht zugestellte Mails erkannt werden
	$returnMail = "dmvwebsite@zedat.fu-berlin.de";
	$additionalparams = "-r".$returnMail;
	mail($receiver, $subject, $text, $header, $additionalparams);
}	
?>