<?
include('../ValUser.php');

if($loggedin) {
	$resultString =  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"><head>
    <title>Tag der Mathematik 2012</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Am 20. Mai 2012 findet der Tag der Mathematik an der Freien Universität Berlin statt.">
        <meta name="robots" content="index, follow">
    <link href="../ergebnisData/stylesheet.css" type="text/css" rel="stylesheet">
	<link href="http://tdm.math.fu-berlin.de/favicon.ico" type="image/x-icon" rel="shortcut icon"> 

</head>

<body>
<div id="box">
<div id="content">
    <h1>
        <a href="http://www.fu-berlin.de/" title="Zur Startseite der Freien Universität Berlin">
            <img src="../ergebnisData/fu_logo.gif" alt="Logo der Freien Universität Berlin" height="63" width="225">
        </a>
    </h1>
	<ul id="service-navigation">
		<li><a href="http://tdm.math.fu-berlin.de/data/sitemap.html">Sitemap</a></li>
		<li><a href="http://tdm.math.fu-berlin.de/data/contact.html">Kontakt</a></li>
		<li><a href="http://www.mi.fu-berlin.de/impressum/">Impressum</a></li>

	</ul>
<div class="mittig"><h3>17. Berliner Tag der Mathematik</h3><h6>Samstag, 5. Mai 2012<br>Freie Universität Berlin</h6></div>';
	
	if(isset($_GET['names'])) {
		$nameMax = $_GET['names'];
	} else {
		$nameMax = 3;
	}
	
	if(isset($_GET['fullInfo'])) {
		$infoMax = $_GET['fullInfo'];
	} else {
		$infoMax = 10;
	}
	
	if(isset($_GET['placesUntil'])) {
		$placesUntil = $_GET['placesUntil'];
	} else {
		$placesUntil = 10;
	}
	
	for($i = 1; $i <= 3; $i++) {
		$sql = "SELECT anmeldungen.teamName, anmeldungen.schule, anmeldungen.bezirkName, punkte.aufgabe1, punkte.aufgabe2, punkte.aufgabe3, punkte.aufgabe4, (punkte.aufgabe1 + punkte.aufgabe2 + punkte.aufgabe3 + punkte.aufgabe4) AS SUMME, (punkte.aufgabe1 * punkte.aufgabe2 * punkte.aufgabe3 * punkte.aufgabe4) AS PRODUKT, CONCAT('".intToSimpleRoman($i)."/', anmeldungen.teamNumber), anmeldungen.teamNumber FROM anmeldungen LEFT JOIN punkte ON anmeldungen.teamNumber = punkte.teamNumber WHERE (anmeldungen.stufe = ".$i." AND anmeldungen.teamNumber <> 0) ORDER BY SUMME DESC, PRODUKT DESC";
		$result = $db->query($sql) OR DIE ($db->error);
		//echo "sql: ".$sql."<br><br>";
		
		$resultString .= "<h3>Ergebnisse der Stufe ".intToSimpleRoman($i)."</h3><table class='table table-bordered table-striped'><thead><tr><th>Platz</th><th>Teamname</th><th>Schule</th><th>Region/Bezirk</th><th>1</th><th>2</th><th>3</th><th>4</th><th>Summe</th><th>Produkt</th><th>Nr.</th></tr></thead><tbody>";
		$platz = 1;
		while($row = $result->fetch_row()) {
			if ($platz <= $placesUntil) {
				$displayedPlace = $platz;
			} else {
				$displayedPlace = "";
			}
			$resultString .= "<tr><td>".$displayedPlace."</td>";
			// Enthält der Schulname ein "/", wird danach ein Zeilenumbruch eingefügt
			$row[1] = str_replace("/", "/<br>", $row[1]);
			for($j = 0; $j < count($row) - 1; $j++) {
				if($platz > $infoMax) {
					if($j == 0) {
						$resultString .= "<td colspan='3'>".$row[0]."</td>";
					} elseif($j >= 3) {
						$resultString .= "<td>".$row[$j]."</td>";
					}
				} else {
					$resultString .= "<td>".$row[$j]."</td>";
				}
			}
			$resultString .= "</tr>";
			if($platz <= $nameMax) {
				$names = $db->query("SELECT count, vorname1, name1, vorname2, name2, vorname3, name3, vorname4, name4, vorname5, name5 FROM anmeldungen WHERE teamNumber = ".$row[count($row) - 1]." LIMIT 1");
				$names = $names->fetch_assoc();
				$resultString .= '<tr><td></td><td colspan="10">(';
				for($k = 1; $k <= $names['count'] - 1; $k++) {
					$resultString .= $names['vorname'.$k]." ".mb_strtoupper($names['name'.$k], "UTF-8").", ";
				}
				$resultString .= $names['vorname'.$names['count']]." ".mb_strtoupper($names['name'.$names['count']], "UTF-8").")</td></tr>";
			}
			$platz++;
		}
		$resultString .= "</tbody></table>";
	}
	
	echo $resultString.'
	</div>
<br class="clear">
<div class="image">
<div class="date">Stand: '.date('d.m.Y').'</div>
<img src="../ergebnisData/TdM_Logo_2012_Baer.png" alt="Logo des Tages der Mathematik 2012" style="float: right">
</div>
<div id="footer">
<p id="footer-university">
	<a href="http://www.fu-berlin.de/" title="Zur Startseite der Freien Universität Berlin">
		Freie Universität Berlin
	</a>
</p>
<p id="footer-institute">
	<a href="http://www.math.fu-berlin.de/index.html" title="Zur Homepage des Mathematischen Institus der FU Berlin">
		INSTITUT FÜR MATHEMATIK
	</a>
</p>

<ul id="footer-navigation">
	<li><a href="http://tdm.math.fu-berlin.de/data/sitemap.html">Sitemap</a></li>

	<li><a href="http://tdm.math.fu-berlin.de/data/contact.html">Kontakt</a></li>
	<li><a href="http://www.mi.fu-berlin.de/impressum/">Impressum</a></li>
</ul>
</div></div>
</body></html>';
} else {
	echo "Sie sind nicht eingeloggt. Loggen Sie sich aus und wieder ein";
}

function intToSimpleRoman($int) {
	$return = "";
	for($i = 0; $i < $int; $i++) {
		$return .= "I";
	}
	
	return $return;
}
?>