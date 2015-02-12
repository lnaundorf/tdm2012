<?
/*

*/

include("../ValUser.php");

if($loggedin) {
	$lines = file("points.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	for($i = 0; $i < count($lines); $i++) {
		$parts = str_getcsv($lines[$i]);
		echo "Number: ".$parts[0].", 1: ".$parts[2].", 2: ".$parts[3].", 3: ".$parts[4].", 4: ".$parts[5]." ";
		
		/* Zur Sicherheit auskommentiert
		$db->query("INSERT INTO punkte (teamNumber, aufgabe1, aufgabe2, aufgabe3, aufgabe4) VALUES (".$db->real_escape_string($parts[0]).", ".$db->real_escape_string($parts[2]).", ".$db->real_escape_string($parts[3]).", ".$db->real_escape_string($parts[4]).", ".$db->real_escape_string($parts[5]).")") OR DIE($db->error);
		
		if($db->errno == 0) {
			echo "success";
		} else {
			echo "error";
		}*/
		echo "<br>";
	}
}
?>