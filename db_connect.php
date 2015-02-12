<?

/*
Dieses Skript wird von den Anmeldeskripten aufgerufen in htdocs/scripts/*.php
Hier befinden sich die Zugangsdaten für die MySQL Datenbank, in der die Anmeldeinformationen gespeichert werde
Diese Datei sollte aus Sicherheitsgründen immer außerhalb den Webspaces liegen.
*/
$host = 'database_host';
$user = 'database_user';
$pw = 'database_password';
$database = 'database_name';

$db = @new mysqli($host, $user, $pw, $database);
$db->set_charset("utf8");
?>