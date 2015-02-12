<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>TdM 2012 Admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js_lib.js"></script>
</head>
<body>
<?
require('ValUser.php');
if(!$loggedin) {
	echo '<h1 align="center">TdM 2012 Admin</h1><div class="login">';
	if($_POST) {
		echo '<span class="error">Benutzername oder Passwort falsch</span>';
	}
	echo '<form method="POST"><table class="logintable"><tr><td style="padding-right: 10px"><label>Benutzername:</label></td><td><input type="text" name="username" style="margin-bottom: 5px"></td></tr>
	<tr><td><label>Passwort:</label></td><td><input type="password" name="password"></td></tr></table>
	<input type="submit" name="submit" value="anmelden" style="margin-top: 20px"></form></div>';
} else {
	echo '<div class="navi"><table class="navtable"><tr>';
	$entries = scandir('content');
	foreach($entries as $entry) {
		if($entry != "." && $entry != "..") {
			echo '<td><a href="?s=content/'.$entry.'">'.substr($entry, 0, strlen($entry) - 4).'</a></td>';
		}
	}
	echo '<td style="text-align: right;"><a href="?s=Passwortaendern.php">Passwort ändern</a></td><td style="text-align: right;"><a href="logout.php">Ausloggen</a></td></tr></table></div><div class="content">';
	if(isset($_GET['s'])) {
		include($_GET['s']);
	} else {
		include('content/'.$entries[2]);
	}
	echo '</div>';
}

?>
</body>
