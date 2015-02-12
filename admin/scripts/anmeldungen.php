<?
include('../ValUser.php');

if($loggedin) {
	$nodate = true;
	$yearget = true;
	$monthget = true;

	if(isset($_GET['beginyear']) && isset($_GET['endyear'])) {
		$nodate = false;
		$beginyear = $_GET['beginyear'];
		$endyear = $_GET['endyear'];
	} elseif(isset($_GET['year'])) {
		$nodate = false;
		$beginyear = $endyear = $_GET['year'];
	} else {
		$yearget = false;
		$beginyear = $endyear = intval(date('Y'));
	}

	if(isset($_GET['beginmonth']) && isset($_GET['endmonth'])) {
		$nodate = false;
		$beginmonth = $_GET['beginmonth'];
		$endmonth = $_GET['endmonth'];
	} elseif(isset($_GET['month'])) {
		$nodate = false;
		$beginmonth = $endmonth = $_GET['month'];
	} else {
		$monthget = false;
		if($yearget) {
			$beginmonth = 1;
			$endmonth = 12;
		} else {
			$beginmonth = $endmonth = date('m');
		}
	}

	if(isset($_GET['beginday']) && isset($_GET['endday'])) {
		$nodate = false;
		$beginday = $_GET['beginday'];
		$endday = $_GET['endday'];;
	} elseif(isset($_GET['day'])) {
		$nodate = false;
		$beginday = $endday = $_GET['day'];
	} else {
		$beginday =  1;
		$endday = cal_days_in_month(CAL_GREGORIAN, $endmonth, $endyear);
	}

	if(!$nodate) {
		$between = "WHERE date BETWEEN '".$beginyear."-".$beginmonth."-".$beginday." 00:00:00' AND '".$endyear."-".$endmonth."-".$endday." 23:59:59' ";
		$filename =  "anmeldungen_".$beginday."_".$beginmonth."_".$beginyear."_bis_".$endday."_".$endmonth."_".$endyear.".txt";
	} else {
		$between = "";
		$filename = "anmeldungen.txt";
	}

	if(isset($_GET['action']) && $_GET['action'] == 'download') {
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	}
	
	if(isset($_GET['display']) && $_GET['display'] == 'table') {
		//evtl custom header
		$displaytable = true;
	} elseif(isset($_GET['action']) && $_GET['action'] == 'essen') {
		header('Content-Type: text/plain; charset=ISO-8859-1');
		header('Content-Disposition: attachment; filename=essen.txt');
	} else {
		$displaytable = false;
		header('Content-Type: text/plain; charset=ISO-8859-1');
	}
	
	if(isset($_GET['newteams'])) {
		if ($_GET['newteams'] == 1 || $_GET['newteams'] == "true") {
			if(strlen($between) > 0) {
				$additionalsql = " AND teamNumber = 0 ";
			} else {
				$additionalsql = "WHERE teamNumber = 0 ";
			}
		} elseif ($_GET['newteams'] == 0 || $_GET['newteams'] == "false") {
			if(strlen($between) > 0) {
				$additionalsql = " AND teamNumber <> 0 ";
			} else {
				$additionalsql = "WHERE teamNumber <> 0 ";
			}
		} else {
			$additionalsql = "";
		}
	} else {
		$additionalsql = "";
	}

	if(isset($_GET['action']) && $_GET['action'] == 'essen') {
		if(isset($_GET['type']) && $_GET['type'] == 'file') {
			$file = true;
		} else {
			$file = false;
		}
		$result = $db->query('SELECT count(*) AS anzahl, sum(count) as summe, sum(IF(begleitEssen > 0, 1, 0)) as summeBegleit FROM anmeldungen '.$between.$additionalsql);
		$row = $result->fetch_array();
		if(!$file) {
			echo "Info über Anmeldungen\r\nZeitraum: ";
			if($nodate) {
				echo "Alle Anmeldungen\r\n";
			} else {
				echo "Beginn: ".$beginday.".".$beginmonth.".".$beginyear.", ";
				echo "Ende: ".$endday.".".$endmonth.".".$endyear."\r\n";
			}
			if($row['summe'] == '') {
				$row['summe'] = 0;
			}
			
			echo "Anzahl Teams: ".$row['anzahl']."<br>";
			echo "Anzahl Schüler: ".$row['summe']."<br>";
			echo "Anzahl Begleitpersonen mit Essen: ".$row['summeBegleit']."<br><br>";
		}
		//Die folgende Zeile sollte angepasst werden, wenn es beim nächsten mal anderes Essen gibt
		$essenNamen = array("Kein Essen", "Hähnchenschnitzel mit Gemüse und Kartoffelpüree", "Currywurst mit Pommes Frites", "Vegetarisches Essen");
		$essen = array();
		$essenBegleit = array();
		
		for($i = 0; $i < count($essenNamen); $i++) {
			$essen[$i] = 0;
			$essenBegleit[$i] = 0;
		}
		
		$result = $db->query('SELECT count, essen1, essen2, essen3, essen4, essen5, begleitEssen, stufe, IF(teamNumber = 0, \'-\', teamNumber) AS teamNumber FROM anmeldungen '.$between.$additionalsql);
		while($row = $result->fetch_array()) {
			for($i = 1; $i <= $row['count']; $i++) {
				$essen[$row[$i]]++;
			}
			$essenBegleit[$row['begleitEssen']]++;
			
			if($file) {
				echo $row['stufe']." ".$row['teamNumber'];
				for ($i = 1; $i < count($essenNamen); $i++) {
					echo " ".($essen[$i] + $essenBegleit[$i]);
					$essen[$i] = $essenBegleit[$i] = 0;
				}
				echo "\r\n";
			}
		}
		
		if(!$file) {
			echo "<table class='table table-bordered' style='width: 0'>";
			echo "<tr><th>Essen</th><th>Schüler</th><th>Begleit</th><th>Gesamt</th></tr>";
			for($i = 0; $i < count($essenNamen); $i++) {
				if($i == 0) {
					$begleit = "-";
					$sum = $essen[$i];
				} else {
					$begleit = $essenBegleit[$i];
					$sum = $essen[$i] + $essenBegleit[$i];
				}
				echo "<tr><td>".$essenNamen[$i]."</td><td>".$essen[$i]."</td><td>".$begleit."</td><td>".$sum."</td></tr>";
			}
			echo "</table>";
		}
	} else {
		if($displaytable) {
			$result2 = $db->query("SELECT teamNumber FROM anmeldungen WHERE teamNumber != 0 ORDER BY teamNumber DESC LIMIT 1");
			$row2 = $result2->fetch_row();
			echo '<div style="display: none" id="lastNumber">'.$row2[0].'</div>';
			
			$sql = "SELECT id, schule, bezirkName, teamName, stufe, CONCAT(vorname1, ' ', name1, ' (', klasse1, '. Kl)'), CONCAT(vorname2, ' ', name2, ' (', klasse2, '. Kl)'), CONCAT(vorname3, ' ', name3, ' (', klasse3, '. Kl)'), IF(klasse4 = 0, '-', CONCAT(vorname4, ' ', name4, ' (', klasse4, '. Kl)')), IF(klasse5 = 0, '-', CONCAT(vorname5, ' ', name5, ' (', klasse5, '. Kl)')), DATE_FORMAT(date, '%e.%c.'), teamNumber FROM anmeldungen ".$between.$additionalsql.'ORDER BY id ASC';
			//echo 'sql: '.$sql."\r\n";
			$result = $db->query($sql);
			if($result->num_rows == 0) {
				echo 'Keine passenden Teams gefunden';
			} else {
				echo '<table class="table table-striped table-bordered table-condensed searchtable"><thead><tr><th>ID</th><th>Schule</th><th>Bezirk</th><th>Teamname</th><th>St.</th><th>Person 1</th><th>Person 2</th><th>Person 3</th><th>Person 4</th><th>Person 5</th><th>Datum</th><th>Teamnummer</th></tr></thead><tbody>';
				
				while($row = $result->fetch_row()) {
					echo '<tr>';
					for($i = 0; $i < count($row) - 1; $i++) {
						echo '<td>'.$row[$i].'</td>';
					}
					if($row[count($row) - 1] != 0) {
						echo '<td class="buttonright"><input type="text" id="number'.$row[0].'" size="1" maxlength="3" value="'.$row[count($row) - 1].'" onkeyup="updateButton('.$row[0].')" onfocus="oldval = this.value" disabled><input name="button" type="button" value="aendern" id="button'.$row[0].'" onclick="buttonAction('.$row[0].')"></td>';
					} else {
						echo '<td class="buttonright"><input type="text" id="number'.$row[0].'" size="1" maxlength="3" onkeyup="updateButton('.$row[0].')" onfocus="oldval = this.value"><input name="button" type="button" value="'.(intval($row2[0]) + 1).' eintragen" id="button'.$row[0].'" onclick="buttonAction('.$row[0].')"></td>';
					}
					
					echo '</tr>';
				}
				echo '</tbody></table>';
			}
		} else {
			$bezirke = array("-- Bitte auswählen --", "Charlottenburg-Wilmersdorf", "Friedrichshain-Kreuzberg", "Lichtenberg", "Marzahn-Hellersdorf", "Mitte", "Neukölln", "Pankow", "Reinickendorf", "Spandau", "Steglitz-Zehlendorf", "Tempelhof-Schöneberg", "Treptow-Köpenick", "Brandenburg", "andere Region");
			
			$sql = 'SELECT * FROM anmeldungen '.$between.$additionalsql.'ORDER BY id ASC';
			//echo 'sql: '.$sql."\r\n";
			$result = $db->query($sql);
			$fields = $result->fetch_fields();
			$names = array();
			for($i = 0; $i < count($fields); $i++) {
				$names[$i] = fillSpaces($fields[$i]->name);
			}
			$first = true;
			
			while($row = $result->fetch_array()) {
				if($first) {
					$first = false;
				} else {
					echo "\n";
				}
				echo "***Meldung***";
				if($row['stufe'] == 1) {
					$stufe = 'I (Klassen 7 und 8)';
				} elseif($row['stufe'] == 2) {
					$stufe = 'II (Klassen 9 und 10)';
				} elseif($row['stufe'] == 3) {
					$stufe = 'III (Klassen 11 bis 13)';
				} else {
					$stufe = 'undefined';
				}
				echo "\r\nSchule  :".utf8_decode($row['schule'])."\r\nBezirk  :".$bezirke[$row['bezirk']]."\r\nKontakt :"
				.utf8_decode($row['kontakt'])."\r\nemail   :".utf8_decode($row['email'])."\r\nStufe   :".$stufe;
				for($i = 1; $i <= 5; $i++) {
					if($row['name'.$i] != '') {
						$klasse = $row['klasse'.$i].". Klasse";
						//$essen = bool2String($row['essen'.$i]);
						//Da Essensverwaltung nicht im MatheTag Programm geschieht, Ausgabe immer "Nein"
						$essen = "Nein";
					} else {
						$klasse = $essen = '';
					}
					echo "\r\nName".$i."   :".utf8_decode($row['name'.$i])."\r\nVorname".$i.":".utf8_decode($row['vorname'.$i]).
					"\r\nKlasse".$i." :".$klasse."\r\nEssen".$i."  :".$essen;
				}
			}
		}
	}
	$result->close();
	$db->close();
} else {
	echo 'Sie sind nicht eingeloggt. Bitte loggen Sie sich aus und wieder ein';
}

function fillSpaces($string) {
	$length = 8;
	for($i = strlen($string); $i < $length; $i++) {
		$string .= " ";
	}
	return $string;
}

function bool2string($bool) {
	if(is_numeric($bool)) {
		if($bool >= 1) {
			return "Ja";
		} else {
			return "Nein";
		}
	} else {
		return "Nein";
	}
}
?>