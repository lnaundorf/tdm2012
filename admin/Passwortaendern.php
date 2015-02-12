<h1>Passwort ändern</h1>
Hinweis: Das neue Passwort muss mindestens 7 Zeichen lang sein<br><br>
<?
if(isset($_POST['changepw'])) {
	if($_SESSION['password'] == $_POST['oldpass']) {
		if($_POST['newpass1'] == $_POST['newpass2']) {
			if(strlen($_POST['newpass1']) >= 7) {
				$db->query("UPDATE users SET password = sha1('".$db->real_escape_string($_POST['newpass1'])."') WHERE username = '".$_SESSION['username']."' LIMIT 1");
				if($db->affected_rows == 1) {
					$_SESSION['password'] = $_POST['newpass1'];
					echo '<span class="success">Das Passwort wurde erfolgreich geändert</span>';
				} else {
					echo '<span class="error">Fehler: Beim ändern des Passworts ist ein Fehler aufgetreten</span>';
				}
			} else {
				echo '<span class="error">Fehler: Das neue Passwort muss mindestens 7 Zeichen lang sein</span>';
			}
		} else {
			echo '<span class="error">Fehler: Die neuen Passwörter stimmen nicht überein</span>';
		}
	} else {
		echo '<span class="error">Fehler: Das alte Passwort stimmt nicht</span>';
	}
 
}
?>

<form method="POST"><table class="logintable"><tr><td><label>Altes Passwort:</label></td><td><input type="password" name="oldpass"></td></tr>
<tr><td><label>Neues Passwort:</label></td><td><input type="password" name="newpass1"></td></tr>
<tr><td style="padding-right: 10px"><label>Neues Passwort wiederholen:</label></td><td><input type="password" name="newpass2"></td></tr></table>
<input style="margin-top: 20px" type="submit" name="changepw" value="Passwort ändern"></form>
