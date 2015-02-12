<?
require('/web/tdm.math.fu-berlin.de/scripts/db_connect.php');
session_start();
if(isset($_POST['username']) && isset($_POST['password'])) {
	$username = $_SESSION['username'] = $_POST['username'];
	$password = $_SESSION['password'] = $_POST['password'];
} elseif(isset($_SESSION['username']) && isset($_SESSION['password'])) {
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];
} else {
	$loggedin = false;
	return;
}
$result = $db->query("SELECT password FROM users WHERE username = '".$username."' LIMIT 1");
$row = $result->fetch_row();
if(sha1($password) == $row[0]) {
	$loggedin = true;
} else {
	$loggedin = false;
}
?>