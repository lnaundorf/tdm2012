/*
Der Javascript in dieser Datei wird von der Anmeldungsseite "registrationform.html" benutzt und wird zwingend benötigt.
*/

stufeSet = false;
numberSet = false;
regionSet = false;

key = "";
selectedSchool = null;
selectedRegion = null;
selectedElem = null;
regionNumber = 0;
timeout = false;
waiting = false;
lastSearch = "";
validateAnswer = 0;

/*
Mit dieser Funktion lässt sich mit Hilfe der UP- und DOWN-Taste durch die gefunden
Schulen navigieren.
*/
function switchResults(event) {
	var search = document.getElementById("schoolsearch");
	if(search && search.style.display == "block") {
		var temp = selectedElem;
		if(event.keyCode == 38) {
			//key UP
			if(selectedElem.previousSibling != null) {
				highlightOut(selectedElem);
				highlightOver(temp.previousSibling);
			}
		} else if(event.keyCode == 40) {
			//key DOWN
			if(selectedElem == null) {
				highlightOver(search.firstChild.firstChild.firstChild);
			} else if(selectedElem.nextSibling != null) {
				highlightOut(selectedElem);
				highlightOver(temp.nextSibling);
			}
		} else if(event.keyCode == 13) {
			//key ENTER
			schoolSearchBlur();
			highlightOut(selectedElem);
		}
	}
}

/*
Diese Funktion wird aufgerufen, wenn Text in das Schulofeld eingegeben wird.
Sie sorgt dafür, dass nur alle 0.5 Sekunden eine Suchanfrage für die Schulnamen
an den Server geschickt wird, um den Server nicht zu überlasten.
*/
function searchSchool(event, doc) {
	if(event.keyCode != 40 && event.keyCode != 38) {
		if(event.keyCode == 13) {
			var display = false;
		} else {
			var display = true;
		}
		if(!timeout) {
			timeout = true;
			setTimeout(function() { 
				timeout = false;
				if(waiting) {
					waiting = false;
					invokeSearch(doc, display);
				}
			}, 500);
			invokeSearch(doc, display);
		} else {
			waiting = true;
		}
	} else {
		var search = document.getElementById("schoolsearch");
		if(search && event.keyCode == 40 && selectedElem == null) {
			search.style.display = "block";
			highlightOver(search.firstChild.firstChild.firstChild);
		}
	}
}

/*
Diese Funktion ruft das php-Skript schools.php auf und es werden die gefundenen Schulnamen angezeigt
*/
function invokeSearch(doc, display) {
	var name = doc.value;
	if(name != lastSearch) {
		lastSearch = name;
		var search = document.getElementById("schoolsearch");
		if(name.length < 3 && search != null) {
			search.style.display = "none";
		} else {
			var xmlHttp = getXMLHttp();
			
			if(xmlHttp) {
				if(regionNumber != 0) {
					var regionString = "&region=" + regionNumber; 
				} else {
					var regionString = "";
				}
				xmlHttp.open("GET", "/scripts/schools.php?q=" + encodeURIComponent(name) + regionString, true);
				xmlHttp.onreadystatechange = function() {
					if(xmlHttp.readyState == 4) {
						var tableString = '<table class="searchtable">';
						var schools = xmlHttp.responseXML.getElementsByTagName("school");
						if(schools.length == 0) {
							tableString += '<tr><td>Keine passende Schule gefunden</td></tr>';
						}
						for(i = 0; i < schools.length; i++) {
							var name = schools[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
							var string = schools[i].getElementsByTagName("string")[0].childNodes[0].nodeValue;
							var region = schools[i].getElementsByTagName("region")[0].childNodes[0].nodeValue;
							var regionString = schools[i].getElementsByTagName("regionString")[0].childNodes[0].nodeValue;
							
							tableString += '<tr onmouseover="highlightOver(this)" onmouseout="highlightOut(this)" class="schoolresult"><td><div class="schoolinvisible">' + name + '</div><div class="schoolinvisible">' + region +'</div><span class="searchleft">' + string + '</span><span class="searchright">' + regionString + '</span></td></tr>';
						}
						selectedSchool = selectedRegion = selectedElem = null;
						search.innerHTML = tableString + '</table>';

						if(display) {
							search.style.display = "block";
						}
					}
				};
				xmlHttp.send(null);
			}
		}
	}
}

function highlightOver(doc) {
	doc.style.backgroundColor = "#839DBA";
	selectedSchool = doc.firstChild.childNodes[0].innerHTML;
	selectedRegion = doc.firstChild.childNodes[1].innerHTML;
	selectedElem = doc;
}

function highlightOut(doc) {
	doc.style.backgroundColor = "white";
	selectedSchool = selectedRegion = selectedElem = null;
}

function schoolSearchBlur() {
	if(selectedSchool != null) {
		document.getElementById("schoolinput").value = selectedSchool;
		var regionSelect = document.getElementById("regionSelect");
		if(regionSelect) {
			var options = regionSelect.options;
			for(i = 0; i < options.length; i++) {
				if(options[i].value == selectedRegion) {
					options[i].selected = true;
					changeRegion(regionSelect);
					break;
				}
			}
		}
	}
	document.getElementById('schoolsearch').style.display = "none";
}

function setNumber(val) {
	for(i = 1; i <= 5; i++) {
		if(i <= val) {
			var style = "block";
		} else {
			var style = "none";
		}
		var doc = document.getElementById("person" + i);
		doc.style.display = style;
	}
	document.getElementById("teamCount").value = val;
	
	if(!numberSet) {
		var doc = document.getElementById("numberzero");
		if(doc) {
			doc.parentNode.removeChild(doc);
		}
		numberSet = true;
		if(stufeSet) {
			document.getElementById("submit").style.display = "block";
		}
	}
}

/*
Hilfsfunktion für den Internet Explorer, da dieser nicht den "placeholder" Eintrag in einem
Text-Input unterstützt.
*/
function contactHint(elem, focus) {
	if(focus && elem.value == "Vorname Nachname") {
		elem.value = "";
		elem.style.color = "";
	} else if(!focus && elem.value == "" && isIE()) {
		elem.value = "Vorname Nachname";
		elem.style.color = "#c7c7c7";
	}
}

function changeRegion(doc) {
	if(!regionSet) {
		regionSet = true;
	}
	regionNumber = doc[doc.selectedIndex].value;
	var doc = document.getElementById("regionzero");
	if(doc) {
		doc.parentNode.removeChild(doc);
	}
}

function setStufe(val) {
	if(val == 1) {
		var klassen = new Array(7,8);
	} else if (val == 2) {
		var klassen = new Array(9,10);
	} else {
		var klassen = new Array(11,12,13);
	}
	
	for(i = 1; i <= 5; i++) {
		var string = '';
		var doc = document.getElementById("stufe_span" + i);
		for(j = 0; j < klassen.length; j++) {
			string += '<li><label name="person' + i + 'klasse"><input type="radio" name="KlasseInput' + i + '" value="' + klassen[j] + '"><span>Klasse ' + klassen[j] + '</span></label></li>';
		}
		doc.innerHTML = string;
	}
	
	if(!stufeSet) {
		var doc = document.getElementById("stufezero");
		if(doc) {
			doc.parentNode.removeChild(doc);
		}
		
		stufeSet = true;
		if(numberSet) {
			document.getElementById("submit").style.display = "block";
		}
	}
}

/*
Sendet die Eingebenen Anmeldedaten an das php-Skript validate_form.php,
parst die XML-Antwort des php-Skriptes und gibt im Fehlerfall aus, welche
Anmeldefelder fehlerhaft ausgefüllt wurden.
*/
function validate_registration() {
	validateAnswer = 0;
	setTimeout(function() {
		if(validateAnswer == 0) {
			document.getElementById("ajaxloader").style.display = "inline";
			var button = document.getElementById("submitButton");
			if(button) {
				button.value = "überprüfen...";
				button.disabled = true;
			}
		}
	}, 250);
	var registration_elements = document.getElementsByName("anmeldung");
	var stufe = registration_elements[0][registration_elements[0].selectedIndex].value;
	var schule = registration_elements[1].value;
	var region = registration_elements[2][registration_elements[2].selectedIndex].value;
	var teamName = registration_elements[3].value;
	var kontakt = registration_elements[4].value;
	if(kontakt == "Vorname Nachname") {
		kontakt = "";
	}
	var email = registration_elements[5].value;
	var begleitEssen = getCheckedValue(document.getElementsByName("BegleitInput"));
	var count = registration_elements[6][registration_elements[6].selectedIndex].value;

	var persons = new Array(count);
	for(i = 1; i <= count; i++) {
		var person = new Array(4);
		var person_elem = document.getElementsByName("personInput" + i);
		person[0] = person_elem[0].value;
		
		if(person[0] == undefined) {
			person[0] = "";
		}
		person[1] = person_elem[1].value;
		
		if(person[1] == undefined) {
			person[1] = "";
		}
		person[2] = getCheckedValue(document.getElementsByName("KlasseInput" + i));
		person[3] = getCheckedValue(document.getElementsByName("EssenInput" + i));
		
		persons[i-1] = person;
	}
	
	var xmlHttp = getXMLHttp();
	if(xmlHttp) {
		var params = 'stufe=' + stufe + '&schule=' + schule + '&region=' + region + '&teamName=' + teamName + '&kontakt=' +
		kontakt + '&email=' + email + '&begleitEssen=' + begleitEssen + '&count=' + count;
		
		for(i = 1; i <= count; i++) {
			var person = persons[i - 1];
			params += "&vorname" + i + "=" + person[0] + "&name" + i + "=" + person[1] +
			"&klasse" + i + "=" + person[2] + "&essen" + i + "=" + person[3];
		}
		xmlHttp.open("POST", "/scripts/validate_form.php", true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", params.length);
		xmlHttp.setRequestHeader("Connection", "close");
		
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4) {
				document.getElementById("ajaxloader").style.display = "none";
				var button = document.getElementById("submitButton");
				button.value = "Daten überprüfen";
				button.disabled = false;

				updateForm(xmlHttp.responseXML);
			}
		};
		xmlHttp.send(params);
	}
}

function getCheckedValue(radioObj) {
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		if(radioObj.checked) {
			return radioObj.value;
		} else {
			return -1;
		}
	}
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return -1;
}

function getXMLHttp() {
	var xmlHttp = null;
	try {
		xmlHttp = new XMLHttpRequest();
	} catch(e) {
		try {
			xmlHttp  = new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			try {
				xmlHttp  = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xmlHttp  = null;
			}
		}
	}
	return xmlHttp;
}

function updateForm(xml) {
	var reviewElements = xml.getElementsByTagName("reviewElement");
	if(reviewElements.length > 0) {
		document.getElementById("form").style.display =  "none";
		document.getElementById("submit").style.display = "none";
		
		for(i = 0; i < reviewElements.length; i++) {
			var name = reviewElements[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
			var value = reviewElements[i].getElementsByTagName("value")[0].childNodes[0].nodeValue;
			
			if(name == "count") {
				for(j = 1; j <= 5; j++) {
					if(j <= value) {
						document.getElementById("table" + j).style.display = "block";
					} else {
						document.getElementById("table" + j).style.display = "none";
					}
				}
				
				if(value == 4) {
					document.getElementById("hr4").style.display = "block";
					document.getElementById("hr5").style.display = "none";
				} else if(value == 5) {
					document.getElementById("hr4").style.display = "block";
					document.getElementById("hr5").style.display = "block";
				} else {
					document.getElementById("hr4").style.display = "none";
					document.getElementById("hr5").style.display = "none";
				}
			}
			
			var div = document.getElementById(name + "Review");
			if(div != undefined) {
				div.innerHTML = value;
			}
		}
		
		var keyElem = xml.getElementsByTagName("key");
		if(keyElem.length > 0) {
			key = keyElem[0].childNodes[0].nodeValue;
		}
		document.getElementById("review").style.display = "block";
		window.scrollTo(0,0);
	}
	var errors = xml.getElementsByTagName("element");
	
	var error = false;
	for(i = 0; i < errors.length; i++) {
		var name = errors[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
		var value = errors[i].getElementsByTagName("correct")[0].childNodes[0].nodeValue;
		if(name == "email") {
			var doc = document.getElementById("mail-help");
			if (value == 0) {
				doc.style.display = "block";
			} else {
				doc.style.display = "none";
			}
		} else if(name == "teamName") {
			var doc = document.getElementById("team-help");
			if (value < 0) {
				doc.style.display = "block";
			} else {
				doc.style.display = "none";
			}
		}
		
		var div = document.getElementById(name + "div");
		if(div != undefined) {
			if(value <= 0 ) {
				if(!error) {
					document.getElementById("messages").innerHTML = "Fehler: Bitte füllen Sie alle angegebenen Felder aus";
					error = true;
				}
				div.setAttribute("class", "clearfix error");
			} else {
				div.setAttribute("class", "clearfix");
			}
		}
	}
	
	if(!error) {
		document.getElementById("messages").innerHTML = "&nbsp;";
	}
	validateAnswer = 1;
}

function setBack() {
	document.getElementById("form").style.display =  "block";
	document.getElementById("submit").style.display = "block";
	document.getElementById("review").style.display =  "none";
	window.scrollTo(0,0);
}

/*
Ruft das php-Skript complete_registration.php auf
*/
function completeRegistration() {
	if (key != "") {
		var xmlHttp = getXMLHttp();
		if(xmlHttp) {
			xmlHttp.open("GET", "/scripts/complete_registration.php?key=" + key, true);
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4) {
					var status = xmlHttp.responseXML.getElementsByTagName("registrationAnswer")[0].getElementsByTagName("status")[0].childNodes[0].nodeValue;
					if(status == 4) {
						document.getElementById("completeActions").style.display = "none";
						document.getElementById("messageText").innerHTML = "<div class='alert-message success'>Ihre Anmeldung war erfolgreich. Sie erhalten in Kürze eine E-Mail mit einer Kopie der Anmeldedaten an die angegebene Mail-Adresse.</div>";
					} else {
						document.getElementById("messageText").innerHTML = "<div class='alert-message error'>Bei Ihrer Anmeldung ist ein Fehler aufgetreten. Bitte versuchen Sie es zu einem späteren Zeitpunkt erneut.</div>";
					}
					window.scrollTo(0,0);
				}
			};
			xmlHttp.send(null);
		}
	}
}

function isIE() {
	return /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent);
}