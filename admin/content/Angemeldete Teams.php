<h1>Angemeldete Teams</h1>
<label>Filter:</label><input type="text" name="search" onkeyup="searchTeams(this)"><a style="margin-left: 30px" href="scripts/teamsearch.php?action=download" target="_blank">ALLE AKZEPTIERTEN ANMELDUNGEN ALS HTML HERUNTERLADEN</a><div id="searchResult" style="margin-top: 20px">
<?
$included = true;
include('scripts/teamsearch.php');
?>
</div>