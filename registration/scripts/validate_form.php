<?
include("variables.php");

/*
Dieses Skript validiert die übergebenen Anmeldedaten und gibt im Fehlerfall zurück,
welche Anmeldedaten fehlerhaft waren. Diese werden dann in der Anmeldung rot angezeigt.
Im Erfolgsfall werden die Anmeldedaten zur Korrektur zurückgeschickt und gleichzeitig auf dem Server
in der $_SESSION variable gespeichert.
Die Bestätigung der Anmeldedaten und Eintrag in die Datenbank macht das Skript complete_registration.php,
welches im gleichen Verzeichnis wie dieses Skript liegt.

Dieses Skript gibt seine Rückgabe als XML aus. Diese Rückgabe wird vom Javascript in js_lib.js passend verarbeitet
und auf der Anmeldeseite ausgegeben.

*/

if($_POST) {
	$count = $_POST['count'];
	$stufe = $_POST['stufe'];
	$error = false;
	$correct = array();
	
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$answer = $doc->createElement("formAnswer");
	$doc->appendChild($answer);
	$r = $doc->createElement("elements");
	$answer->appendChild($r);
	
	if(is_number($count) && is_number($stufe) && $count >= 3 && $count <= 5 && $stufe >= 1 && $stufe <= 3) {
		if($stufe == 1) {
			$klasse_min = 7;
			$klasse_max = 8;
		} elseif($stufe == 2) {
			$klasse_min = 9;
			$klasse_max = 10;
		} elseif($stufe == 3) {
			$klasse_min = 11;
			$klasse_max = 13;
		}
		$correct['stufe'] = true;
		$correct['count'] = true;
		
		//überprüfe allgemeine Informationen auf korrektheit
		if(is_string($_POST['schule']) && strlen($_POST['schule']) > 0) {
			$correct['schule'] = true;
		} else {
			$error = true;
			$correct['schule'] = false;
		}
		
		if(is_numeric($_POST['region']) && $_POST['region'] >= 1 && $_POST['region'] <= count($regions)) {
			$correct['region'] = true;
		} else {
			$error = true;
			$correct['region'] = false;
		}
		
		if(is_string($_POST['teamName']) && strlen($_POST['teamName']) > 0) {
			include("../../scripts/db_connect.php");
			$result = $db->query("SELECT * FROM anmeldungen WHERE teamName = '".$db->real_escape_string($_POST['teamName'])."' LIMIT 1");
			if($result->num_rows > 0) {
				$correct['teamName'] = -1;
				$error = true;
			} else {
				$correct['teamName'] = true;
			}
		} else {
			$error = true;
			$correct['teamName'] = false;
		}

		if(is_string($_POST['kontakt']) && strlen($_POST['kontakt']) > 0) {
			$correct['kontakt'] = true;
		} else {
			$error = true;
			$correct['kontakt'] = false;
		}
		
		if(is_string($_POST['email']) && strlen($_POST['email']) > 0 && is_email($_POST['email'])) {
			$correct['email'] = true;
		} else {
			$error = true;
			$correct['email'] = false;
		}
		
		if($_POST['begleitEssen'] >= 0 && $_POST['begleitEssen'] <= count($essen)) {
			$correct['begleitEssen'] = true;
		} else {
			$error = true;
			$correct['begleitEssen'] = false;
		}
		
		for ($i = 1; $i <= $count; $i++) {
			//überprüfe Schüler auf Korrektheit
			if(is_string($_POST['vorname'.$i]) && strlen($_POST['vorname'.$i]) > 0) {
				$correct['vorname'.$i] = true;
			} else {
				$error = true;
				$correct['vorname'.$i] = false;
			}
			
			if(is_string($_POST['name'.$i]) && strlen($_POST['name'.$i]) > 0) {
				$correct['name'.$i] = true;
			} else {
				$error = true;
				$correct['name'.$i] = false;
			}
			
			if(is_numeric($_POST['klasse'.$i]) && $_POST['klasse'.$i] >= $klasse_min && $_POST['klasse'.$i] <= $klasse_max) {
				$correct['klasse'.$i] = true;
			} else {
				$error = true;
				$correct['klasse'.$i] = false;
			}
			
			if($_POST['essen'.$i] >= 0 && $_POST['essen'.$i] <= count($essen)) {
				$correct['essen'.$i] = true;
			} else {
				$error = true;
				$correct['essen'.$i] = false;
			}
		}
	} else {
		$error = true;
		addElemToDoc($doc, $r, "format", false);
	}
	
	foreach($correct as $name => $value) {
		addElemToDoc($doc, $r, $name, $value);
	}
	
	if(!$error) {
		//Alle Daten korrekt. Zur Kontrolle zurücksenden, aber Eingabedaten auf dem Server merken
		session_start();
		
		for($i = $_POST['count'] + 1; $i <= 5; $i++) {
			$_SESSION['name'.$i] = $_SESSION['vorname'.$i] = "";
			$_SESSION['klasse'.$i] = $_SESSION['essen'.$i] = 0;
		}
		
		$review = $doc->createElement("reviewElements");
		$answer->appendChild($review);
		
		foreach($correct as $name => $value) {
			$_SESSION[$name] = $_POST[$name];
			
			$elem = $doc->createElement("reviewElement");
			
			$namedoc = $doc->createElement("name");
			$namedoc->appendChild($doc->createTextNode($name));
			$elem->appendChild($namedoc);
			
			$value = $doc->createElement("value");
			$insertValue = $_SESSION[$name];
			
			if(substr($name, 0, 6) == "klasse") {
				$insertValue .= ". Klasse";
			} elseif(substr($name, 0, 5) == "essen" || $name == "begleitEssen") {
				$insertValue = $essen[$insertValue];
			} elseif($name == "region") {
				$insertValue = $_SESSION['bezirkName'] = $regions[$insertValue];
			} elseif($name == "stufe") {
				if ($insertValue == 1) {
					$insertValue = "Stufe I (Klasse 7 und 8)";
				} elseif($insertValue == 2) {
					$insertValue = "Stufe II (Klasse 9 und 10)";
				} else {
					$insertValue = "Stufe III (Klasse 11 bis 12/13)";
				}
			}
			
			$value->appendChild($doc->createTextNode($insertValue));
			$elem->appendChild($value);
			
			$review->appendChild($elem);
		}
		
		$key = $doc->createElement("key");
		$key->appendChild($doc->createTextNode(session_id()));
		$answer->appendChild($key);
	}

	header("Content-Type: text/xml");
	echo $doc->saveXML();
} else {
	//echo 'NO correct request';
}

function addElemToDoc($doc, $elementsDoc, $string, $correct) {
	if($correct === true) {
		$value = 1;
	} elseif($correct === false) {
		$value = 0;
	} else {
		$value = $correct;
	}
	
	$elem = $doc->createElement("element");
	$name = $doc->createElement("name");
	$name->appendChild($doc->createTextNode($string));
	$elem->appendChild($name);
	
	$correct = $doc->createElement("correct");
	$correct->appendChild($doc->createTextNode($value));
	$elem->appendChild($correct);
	
	$elementsDoc->appendChild($elem);
}

function is_email($email) {
    return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
}

function is_number($string) {
	return preg_match('|^\d$|i', $string);
}
?>