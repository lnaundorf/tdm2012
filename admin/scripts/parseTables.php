<?
/*
Dieses Skript erzeugt aus einer im gleichen Verzeichnis liegenden Datei tische.txt die Zuweisung in die
Datenbanktabelle "tische". Das Dateiformat muss sein:
Teamnummer (TAB) Tischnummer
*/

include("../ValUser.php");

if($loggedin) {
	$lines = file("tische.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	for($i = 0; $i < count($lines); $i++) {
		$parts = explode("\t", $lines[$i], 3);
		echo "teamNumber: ".$parts[0].", Tisch: ".$parts[1]." ";
		
		/* Zur Sicherheit auskommentiert, damit nicht aus Versehen Tische mehrfach hinzugefügt werden
		$db->query("INSERT INTO tische (teamNumber, tisch) VALUES (".$db->real_escape_string($parts[0]).", '".$db->real_escape_string($parts[1])."')") OR DIE($db->error);
		
		if($db->errno == 0) {
			echo "success";
		} else {
			echo "error<br>";
		}*/
		echo "<br>";
	}
}
?>