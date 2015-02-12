<?
include('../ValUser.php');

if($loggedin) {
	if(isset($_GET['id']) && isset($_GET['number'])) {
		$id = $db->real_escape_string($_GET['id']);
		$number = $db->real_escape_string($_GET['number']);
		
		if($number != 0 && $number != "") {
			$result = $db->query("SELECT id FROM anmeldungen WHERE teamNumber = ".$number." LIMIT 1");
			if($result->num_rows > 0) {
				$row = $result->fetch_row();
				if($row[0] == $id) {
					echo "success";
				} else {
					echo "Die übergebene Teamnummer existiert bereits";
				}
				exit(0);
			}
		}
		if($number == "") {
			$number = 0;
		}
		$db->query("UPDATE anmeldungen SET teamNumber = ".$number." WHERE id = ".$id." LIMIT 1");
		
		if($db->errno == 0) {
			echo "success";
		} else {
			echo "Es existiert kein passender Datenbankeintrag zum aktualisieren (ID = ".$id.")";
		}
	} else {
		echo "Falsche Paramter gegeben";
	}
} else {
	echo "Sie sind nicht eingeloggt. Loggen Sie sich aus und wieder ein";
}
?>