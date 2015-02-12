<h1>Ergebnisse erzeugen</h1>
Die Ergebnisse werden generiert aus der Tabelle "punkte". Die Tabelle muss davor mit den Ergebnisdaten gefüllt werden.<br><br>
Parameter:<br>
names: Zeige bis zu dieser Platzierung die vollen Namen der Teammitglieder an (default: 3)<br>
fullInfo: Zeige bis zu diesem Platz Schule sowie Bezirk an (default: 10. Platz)<br>
placesUntil: Zeige bis zu diesem Platz die Platzierung an (default: 10. Platz)<br><br>
<label style="font-family: monospace">generateResults.php?</label><input type="text" id="query" size="50"><input type="submit" value="Anzeigen" name="querybutton" onclick='window.open("scripts/generateResults.php?" + document.getElementById("query").value, "ergebnis")'>
<div id="queryresult" class="query"></div></div>
