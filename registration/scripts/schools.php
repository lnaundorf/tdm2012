<?
/*
Dieses Skript führt die Schulsuche aus. Es sucht zu einem übergebenen Suchstring passende Schulnamen aus der
Datenbank heraus und gibt die gefundenen Schulen als XML zurück.
Der der String wird im GET-Parameter 'q' übergeben. Ein Aufruf dieses Skriptes lautet z.B:
schools.php?q=bertha
Es werden dann alle passende Schulen zurückgegeben, die den String "Bertha" enthalten.
Das zurückgegebene XML sieht in etwa wie folgt aus:


<?xml version="1.0"?>
<schoolSearch>
  <school>
    <name>Freie Montessori Schule</name>
    <string>Freie Mon&lt;b&gt;tes&lt;/b&gt;sori Schule</string>
    <region>12</region>
    <regionString>Treptow-K&#xF6;penick</regionString>
  </school>
  <school>
    <name>Luise-und-Wilhelm-Teske-Oberschule</name>
    <string>Luise-und-Wilhelm-&lt;b&gt;Tes&lt;/b&gt;ke-Oberschule</string>
    <region>11</region>
    <regionString>Tempelhof-Sch&#xF6;neberg</regionString>
  </school>
  <school>
    <name>Privates Europa-Gymnasium</name>
    <string>Priva&lt;b&gt;tes&lt;/b&gt; Europa-Gymnasium</string>
    <region>11</region>
    <regionString>Tempelhof-Sch&#xF6;neberg</regionString>
  </school>
  <school>
    <name>Tesla-Oberschule</name>
    <string>&lt;b&gt;Tes&lt;/b&gt;la-Oberschule</string>
    <region>7</region>
    <regionString>Pankow</regionString>
  </school>
</schoolSearch>

Die Javascript Funktionen in der Datei js_lib.js verarbeiten dieses XML, sodass es auf der Anmeldeseite
passend als Liste erscheint, wenn jemand einen Schulnamen eintippt

*/
if(isset($_GET['q'])) {
	include("../../scripts/db_connect.php");
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$search = $doc->createElement("schoolSearch");
	$doc->appendChild($search);
	
	$escapedSearch = $db->real_escape_string($_GET['q']);
	
	$parts = preg_split('/ |\-/', $escapedSearch, -1, PREG_SPLIT_NO_EMPTY);
	
	$likeString = "";
	$orderString = "";
	if(count($parts) > 0) {
		$likeString .= "(";
		$orderString .= "(";
		
		if(isset($_GET['region'])) {
			$bezirk = "";
			$orderString .= "(CASE WHEN bezirk = ".$db->real_escape_string($_GET['region'])." THEN 1 ELSE 0 END) + ";
		} else {
			$bezirk = "";
		}
		
		for($i = 0; $i < count($parts) - 1; $i++) {
			$likeString .= "name LIKE '%".$parts[$i]."%' OR ";
			$orderString .= "(CASE WHEN name LIKE '%".$parts[$i]."%' THEN 1 ELSE 0 END) + ";
		}
		$likeString .= "name LIKE '%".$parts[count($parts) - 1]."%')";
		$orderString .= "(CASE WHEN name LIKE '%".$parts[count($parts) - 1]."%' THEN 1 ELSE 0 END)) DESC, ";
	}
	
	$sql = "SELECT name, bezirkString, bezirk FROM schulen WHERE".$bezirk.$likeString." GROUP BY name, bezirk ORDER BY ".$orderString."name ASC LIMIT 15";
	
	/* hier kommen debug Informationen. Diese Zeile können zum Testen auskommentiert werden */
	//echo 'sql: '.$sql;
	/*
	$debugElem = $doc->createElement("debug");
	$search->appendChild($debugElem);
	$sqlElem = $doc->createElement("sql");
	$sqlElem->appendChild($doc->createTextNode($sql));
	$debugElem->appendChild($sqlElem);
	*/
	$result = $db->query($sql);
	while($row = $result->fetch_assoc()) {
		addSchoolToAnswer($doc, $search, $row['name'],formattedSearchString($row['name'], $parts) , $row['bezirk'], $row['bezirkString']);
	}
	
	$db->close();
	
	header("Content-Type: text/xml");
	echo $doc->saveXML();
	
} else {
	echo 'no complete $_GET';
}

function addSchoolToAnswer($doc, $search, $schoolname, $schoolstring, $schoolregion, $schoolregionString) {
	$nameElem = $doc->createElement("name");
	$nameElem->appendChild($doc->createTextNode($schoolname));
	
	$stringElem = $doc->createElement("string");
	$stringElem->appendChild($doc->createTextNode($schoolstring));
	
	$regionElem = $doc->createElement("region");
	$regionElem->appendChild($doc->createTextNode($schoolregion));
	
	$regionStringElem = $doc->createElement("regionString");
	$regionStringElem->appendChild($doc->createTextNode($schoolregionString));
	
	$schoolElem = $doc->createElement("school");
	$schoolElem->appendChild($nameElem);
	$schoolElem->appendChild($stringElem);
	$schoolElem->appendChild($regionElem);
	$schoolElem->appendChild($regionStringElem);
	
	$search->appendChild($schoolElem);
}

function formattedSearchString($haystack, $parts) {
	for($i = 0; $i < count($parts); $i++) {
		if($parts[$i] != "b" && $parts[$i] != "B") {
			$pos = stripos($haystack, $parts[$i]);
			if(!($pos === false)) {
				$length = strlen($parts[$i]);
				$haystack = substr($haystack, 0, $pos).'<b>'.substr($haystack, $pos, $length).'</b>'.substr($haystack, $pos + $length);
			}
		}
	}
	return $haystack;
}
?>