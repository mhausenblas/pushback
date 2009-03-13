function submitBugPriority(debug) {

	addStatus('pushing back data to RDF wrapper ...');

	// glean RDF from the RDFa marked-up form - this is our input graph
	var bugformrdf = $('#bugform').rdf();
	var jsoningraph = $.toJSON(bugformrdf.databank.dump());
	
	// go through all updateable fields and update with current values (set by user) - this is our output graph
	var outgraph = initFormGraph(bugformrdf, debug);
	var isFieldProcessed = new Array();

	bugformrdf
	.prefix('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
	.prefix('pb', 'http://ld2sd.deri.org/pb/ns#')
	.prefix('dcterms', 'http://purl.org/dc/terms/')	
	.where('?rdform rdf:type pb:RDForm')
	.where('?rdform pb:field ?field')
	.where('?field rdf:type pb:UpateableField')
	.where('?field pb:key ?key')
	.where('?field dcterms:title ?title')
	.where('?field pb:value ?val')
	.where('?val rdf:value ?fval')
	.each(function () {
		var newval = null;
		var testval = null;
		var newgraph = null;
		
		if(isFieldProcessed[this.field.value]) return; //the field has already been processed 
		else { // we have not yet come accross this field
			// obtain the value of the field currently in scope (for example for @about='http://ld2sd.deri.org/pb/demo#fo1.f2.val')		
			// we need to make some destincitons based on type of UI widget (select, checkbox, etc.)
			if(debug) addStatus('checking updateable field ' + this.key.value);
			
			if ($("div[rel=pb:value] select")){// a select/option field
				testval = $("select[about=" + this.val.value + "]").val();
				if(testval == undefined)  newval = null; // this field value doesn't exist, forget about it and flag so in newval
				else { // obtain new value for field
					newval = $("select[about=" + this.val.value + "]").val();
					if(debug) addStatus('... select/option detected with value=' + newval);
				}
			}
			if ($("div[rel=pb:value] input[type=checkbox]")){// a checkbox field
				// explicitly evaluate and set the value, not sure if this is really necessary, but so we are on the save side ;)
				if(newval == null) { // we have not yet obtained a field value check for existence
					testval = $("[about=" + this.val.value + "] input[type=checkbox]");
					if(testval == undefined)  newval = null; // this field value doesn't exist, forget about it and flag so in newval
					else {
						if ($("[about=" + this.val.value + "] input[type=checkbox]").is(":checked")){
							newval = true;
						}
						else newval = false;
					}
					if(debug) addStatus('... checkbox detected with value=' + newval);
				}
			}

			// make sure to flag that field so that it gets processed only once
			isFieldProcessed[this.field.value] = true;
			
			addFieldGraph(outgraph, this.rdform.value, this.field.value, this.key.value, this.val.value, newval);

			if(debug) addStatus('outgraph size=' +  outgraph.size());
			
			if(debug) addStatus('Gonna pushback value of field with label "' + this.title.value + '" and key=' + this.key.value +" with value=" + newval);
			}
		}// end of isFieldProcessed[this.val.value]
	);
	
	if(debug) alert($.toJSON(outgraph.dump()));
	var jsonoutgraph = $.toJSON(outgraph.dump());
	
	$.post("http://localhost:8888/pushback/pbcontroler/demo1.php", 
			{	
				bugid: document.getElementById('bugid').value,
				bugprio: document.getElementById('bugprio').value,
				ing:  jsoningraph,
				outg: jsonoutgraph
			} , 
			function(data){
				var result = data;
				document.getElementById('result').innerHTML = result;
			}
	);
}

function initFormGraph(rdf, debug){
	var databank =	$.rdf().databank
					.base('http://ld2sd.deri.org/pb/demo#')
					.prefix('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
					.prefix('pb', 'http://ld2sd.deri.org/pb/ns#')
					.prefix('dcterms', 'http://purl.org/dc/terms/');
	
	rdf
	.prefix('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
	.prefix('pb', 'http://ld2sd.deri.org/pb/ns#')
	.where('?rdform rdf:type pb:RDForm')
	.each(function () {
		databank.add('<' + this.rdform.value + '> rdf:type pb:RDForm .')	
	});

	if(debug) addStatus('outgraph setup: ' +  $.toJSON(databank.dump()));
	return databank;	
}

function addFieldGraph(databank, formURI, fieldURI, keyURI, valURI, val){
	databank
		.add('<' + formURI + '> pb:field <' + fieldURI + '> .')
		.add('<' + fieldURI + '> rdf:type pb:UpateableField .')
		.add('<' + fieldURI + '> pb:key <' +  keyURI + '> .')
		.add('<' + fieldURI + '> pb:value <' +  valURI + '> .')
		.add('<' + valURI + '> rdf:value ' +  val + ' .');
	return databank;	
}

function addStatus(str){
	var tmp = document.getElementById('result').innerHTML;
	document.getElementById('result').innerHTML = tmp + '<br />' + str;
}
