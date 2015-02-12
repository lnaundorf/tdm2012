timeout = false;
waiting = false;
currentNumber = 0;
mode = 0;
oldval = "";

function searchTeams(doc) {
	if(!timeout) {
		timeout = true;
		setTimeout(function() { 
			timeout = false;
			if(waiting) {
				waiting = false;
				invokeSearch(doc);
			}
		}, 500);
		invokeSearch(doc);
	} else {
		waiting = true;
	}
}

function invokeSearch(doc) {
	var input = doc.value;
	//console.log("search. input: " + input);
	var xmlHttp = getXMLHttp();
	xmlHttp.open("GET", "scripts/teamsearch.php?q=" + input, true);
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4) {
			var doc = document.getElementById("searchResult");
			doc.innerHTML = xmlHttp.responseText;
			//console.log("response: " + xmlHttp.responseText);
		}
	};
	xmlHttp.send(null);
}

function getXMLHttp() {
	var xmlHttp = null;
	try {
		// Mozilla, Opera, Safari sowie Internet Explorer (ab v7)
		xmlHttp = new XMLHttpRequest();
	} catch(e) {
		try {
			// MS Internet Explorer (ab v6)
			xmlHttp  = new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			try {
				// MS Internet Explorer (ab v5)
				xmlHttp  = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xmlHttp  = null;
			}
		}
	}
	return xmlHttp;
}

function queryanmeldungen() {
	var val = document.getElementById("query").value;
	var doc = document.getElementById("queryresult");
	doc.innerHTML = "";
	var doc2 = document.getElementById("querytable");
	doc2.innerHTML = "";
	var alterdoc = doc;
	var check = document.getElementById("newwindow");
	var addparams = "";
	if(check.checked == true) {
		alterdoc = doc2;
		window.open("scripts/anmeldungen.php?" + val, "anmeldungen");
		addparams = "display=table&";
	}
	
	var xmlHttp = getXMLHttp();
	xmlHttp.open("GET", "scripts/anmeldungen.php?" + addparams + val, true);
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4) {
			alterdoc.innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(null);
}

function buttonAction(id) {
	if(currentNumber == 0) {
		currentNumber = parseInt(document.getElementById("lastNumber").innerHTML) + 1;
	}
	
	var doc = document.getElementById("number" + id);
	if(doc) {
		if(doc.disabled == true) {
			toggleChange(id);
		} else {
			updateTeamNumber(id);
		}
	}
}

function updateTeamNumber(id) {
	var input = document.getElementById("number" + id);
	if(mode == 2) {
		var val = currentNumber = input.value;
	} else {
		var val = input.value = currentNumber;
	}
	if(val == "" || is_int(val)) {
		if(input) {
			var xmlHttp = getXMLHttp();
			xmlHttp.open("GET", "scripts/updateTeamnumber.php?id=" + id + "&number=" + val, true);
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4) {
					mode = 0;
					currentNumber++;
					updateCurrentNumber();
					toggleChange(id);
					
					if(xmlHttp.responseText != "success") {
						input.value = oldval;
						alert("Beim Speichern ist ein Fehler aufgetreten. Fehlerbeschreibung: " + xmlHttp.responseText);
					}
				}
			};
			xmlHttp.send(null);
		}
	} else {
		alert("Bitte geben Sie als Teamnummer eine ganze positive Zahl ein");
	}
}

function toggleChange(id) {
	var doc = document.getElementById("number" + id);
	if(doc) {
		if(doc.disabled) {
			doc.disabled = false;
			mode = 1;
		} else {
			doc.disabled = true;
		}
	}
	
	doc = document.getElementById("button" + id);
	if(doc) {
		if(mode == 0) {
			doc.value = "aendern";
		} else {
			doc.value = currentNumber + " eintragen";
		}
	}
}

function updateCurrentNumber() {
	var buttons = document.getElementsByName("button");
	
	for(i in buttons) {
		var input = buttons[i].previousSibling;
		if(input && input.disabled == false) {
			buttons[i].value = currentNumber + " eintragen";
		}
	}
}

function updateButton(id) {
	console.log("update button, id: " + id);
	mode = 2;
	var doc = document.getElementById("button" + id);
	if(doc) {
		doc.value="speichern";
	}
}

function is_int(input){
	return !isNaN(input)&&parseInt(input)==input;
}
