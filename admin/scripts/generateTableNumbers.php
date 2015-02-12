<?
include("../ValUser.php");

if($loggedin) {
	$result = $db->query("SELECT teamNumber FROM anmeldungen WHERE teamNumber <> 0");
	echo "ERROR: ".$db->error."<br>";
	
	while($row = $result->fetch_row()) {
		$db->query("INSERT INTO tische (teamNumber, tisch) VALUES (".$row[0].", '".randLetter().rand(0,30)."')");
		echo "ERROR: ".$db->error."<br>";
	}
}


function randLetter() {
	$int = rand(0,25);
	$a_z = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$rand_letter = $a_z[$int];
	return $rand_letter;
}