<?
if(!isset($included)) {
	include('../ValUser.php');
}

if(isset($_GET['action']) && $_GET['action'] == "download") {
	$download = true;
} else {
	$download = false;
}

if($loggedin) {
	if(isset($_GET['q']) && $_GET['q'] != '') {
		$parts = explode(" ", $_GET['q']);
		$arr = array("anmeldungen.schule", "anmeldungen.name1", "anmeldungen.vorname1", "anmeldungen.name2", "anmeldungen.vorname2", "anmeldungen.name3", "anmeldungen.vorname3", "anmeldungen.name4", "anmeldungen.vorname4", "anmeldungen.name5", "anmeldungen.vorname5", "anmeldungen.teamName", "anmeldungen.email", "anmeldungen.kontakt", "anmeldungen.teamName", "anmeldungen.bezirkName", "anmeldungen.teamNumber", "tische.tisch");
		
		$likeString = "(";
		$orderString = "(";
		$counter = 0;
		foreach($arr as $string) {
			$counter++;
			
			for($i = 0; $i < count($parts) - 1; $i++) {
				$likeString .= " $string LIKE '%".$parts[$i]."%' OR";
				$orderString .= "(CASE WHEN $string LIKE '%".$parts[$i]."%' THEN 1 ELSE 0 END) + ";
			}
			if($counter	== count($arr)) {
				$likeString .= " $string LIKE '%".$parts[count($parts) - 1]."%')";
				$orderString .= "(CASE WHEN $string LIKE '%".$parts[count($parts) - 1]."%' THEN 1 ELSE 0 END)) DESC, ";
			} else {
				$likeString .= " $string LIKE '%".$parts[count($parts) - 1]."%' OR";
				$orderString .= "(CASE WHEN $string LIKE '%".$parts[count($parts) - 1]."%' THEN 1 ELSE 0 END) + ";
			}
		}
		
		$sql = "SELECT anmeldungen.id, anmeldungen.schule, anmeldungen.bezirkName, anmeldungen.teamName, anmeldungen.kontakt, anmeldungen.email, anmeldungen.stufe, CONCAT(anmeldungen.vorname1, ' ', anmeldungen.name1, ' (', anmeldungen.klasse1, ')'), CONCAT(anmeldungen.vorname2, ' ', anmeldungen.name2, ' (', anmeldungen.klasse2, ')'), CONCAT(anmeldungen.vorname3, ' ', anmeldungen.name3, ' (', anmeldungen.klasse3, ')'), IF(anmeldungen.klasse4 = 0, '-', CONCAT(anmeldungen.vorname4, ' ', anmeldungen.name4, ' (', anmeldungen.klasse4, ')')), IF(anmeldungen.klasse5 = 0, '-', CONCAT(anmeldungen.vorname5, ' ', anmeldungen.name5, ' (', anmeldungen.klasse5, ')')), DATE_FORMAT(anmeldungen.date, '%e.%c.'), IF(anmeldungen.teamNumber = 0, '-', anmeldungen.teamNumber), IFNULL(tische.tisch, '-') FROM anmeldungen LEFT JOIN tische ON anmeldungen.teamNumber = tische.teamNumber WHERE".$likeString." ORDER BY ".$orderString."anmeldungen.id";
	} else {
		$sql = "SELECT anmeldungen.id, anmeldungen.schule, anmeldungen.bezirkName, anmeldungen.teamName, anmeldungen.kontakt, anmeldungen.email, anmeldungen.stufe, CONCAT(anmeldungen.vorname1, ' ', anmeldungen.name1, ' (', anmeldungen.klasse1, ')'), CONCAT(anmeldungen.vorname2, ' ', anmeldungen.name2, ' (', anmeldungen.klasse2, ')'), CONCAT(anmeldungen.vorname3, ' ', anmeldungen.name3, ' (', anmeldungen.klasse3, ')'), IF(anmeldungen.klasse4 = 0, '-', CONCAT(anmeldungen.vorname4, ' ', anmeldungen.name4, ' (', anmeldungen.klasse4, ')')), IF(anmeldungen.klasse5 = 0, '-', CONCAT(anmeldungen.vorname5, ' ', anmeldungen.name5, ' (', anmeldungen.klasse5, ')')), DATE_FORMAT(anmeldungen.date, '%e.%c.'), IF(anmeldungen.teamNumber = 0, '-', anmeldungen.teamNumber), IFNULL(tische.tisch, '-') FROM anmeldungen LEFT JOIN tische ON anmeldungen.teamNumber = tische.teamNumber ORDER BY anmeldungen.id";
	}
	//echo "SQL: ".$sql."<br><br>";
	//echo "ERROR: ".$db->error."<br><br>";
	$result = $db->query($sql);
	if($result->num_rows > 0) {
		if($download) {
			header('Content-Disposition: attachment; filename="Anmeldungen.html"');
			echo '<html><head><title>Anmeldungen</title><meta http-equiv="content-type" content="text/html; charset=UTF-8">
			<style type="text/css">
			body {
				font-family: Helvetica, Arial, Verdana, sans-serif;
			}
			.searchtable {
	font-size: 9pt;
}

			table {
  max-width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
}
.table {
  width: 100%;
  margin-bottom: 18px;
}
.table th, .table td {
  padding: 8px;
  line-height: 18px;
  text-align: left;
  vertical-align: top;
  border-top: 1px solid #ddd;
}
.table th {
  font-weight: bold;
}
.table thead th {
  vertical-align: bottom;
}
.table thead:first-child tr th, .table thead:first-child tr td {
  border-top: 0;
}
.table tbody + tbody {
  border-top: 2px solid #ddd;
}
.table-condensed th, .table-condensed td {
  padding: 3px 4px;
  vertical-align: middle;
}
.table-bordered {
  border: 1px solid #ddd;
  border-collapse: separate;
  *border-collapse: collapsed;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}
.table-bordered th + th,
.table-bordered td + td,
.table-bordered th + td,
.table-bordered td + th {
  border-left: 1px solid #ddd;
}
.table-bordered thead:first-child tr:first-child th, .table-bordered tbody:first-child tr:first-child th, .table-bordered tbody:first-child tr:first-child td {
  border-top: 0;
}
.table-bordered thead:first-child tr:first-child th:first-child, .table-bordered tbody:first-child tr:first-child td:first-child {
  -webkit-border-radius: 4px 0 0 0;
  -moz-border-radius: 4px 0 0 0;
  border-radius: 4px 0 0 0;
}
.table-bordered thead:first-child tr:first-child th:last-child, .table-bordered tbody:first-child tr:first-child td:last-child {
  -webkit-border-radius: 0 4px 0 0;
  -moz-border-radius: 0 4px 0 0;
  border-radius: 0 4px 0 0;
}
.table-bordered thead:last-child tr:last-child th:first-child, .table-bordered tbody:last-child tr:last-child td:first-child {
  -webkit-border-radius: 0 0 0 4px;
  -moz-border-radius: 0 0 0 4px;
  border-radius: 0 0 0 4px;
}
.table-bordered thead:last-child tr:last-child th:last-child, .table-bordered tbody:last-child tr:last-child td:last-child {
  -webkit-border-radius: 0 0 4px 0;
  -moz-border-radius: 0 0 4px 0;
  border-radius: 0 0 4px 0;
}
.table-striped tbody tr:nth-child(odd) td, .table-striped tbody tr:nth-child(odd) th {
  background-color: #f9f9f9;
}
.table tbody tr:hover td, .table tbody tr:hover th {
  background-color: #f5f5f5;
}

			</style></head><body><h1>Anmeldungen</h1>';
		} else {
			echo 'Anzahl der gefundenen Teams: '.$result->num_rows.'<br><br>';
		}
		echo '<table class="table table-striped table-bordered table-condensed searchtable"><thead><tr>';
		echo '<th>ID</th><th>Schule</th><th>Bezirk</th><th>Teamname</th><th>Kontakt</th><th>E-Mail</th><th>St.</th><th>Person 1</th><th>Person 2</th><th>Person 3</th><th>Person 4</th><th>Person 5</th><th>Datum</th><th>Nr.</th><th>Tisch</th>';
		echo '</tr></thead><tbody>';
		while($row = $result->fetch_row()) {
			if(!$download || $row[13] != '-') {
				echo '<tr>';
				for($i = 0; $i < count($row); $i++) {
					echo '<td>'.$row[$i].'</td>';
				}
				echo '</tr>';
			}
		}
		echo '</tbody></table>';
		
		if($download) {
			echo '</body></html>';
		}
	} else {
		echo 'Kein passendes Team gefunden';
	}
} else {
	echo 'Zu lange inaktiv. Bitte loggen Sie sich aus und wieder ein';
}
?>