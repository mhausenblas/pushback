function pushback() {
	var debug = false;
	var verbosedebug = false;
	
	if ($("#debug").is(":checked")){
		debug = true;
	}
	
	
	addStatus('pushing back data to RDF wrapper ...');

	// glean RDF from the RDFa marked-up form - this is our input graph
	var twitterformrdf = $('#twitterform').rdf();
	var jsoningraph = $.toJSON(twitterformrdf.databank.dump());
	
	// go through all updateable fields and update with current values (set by user) - this is our output graph
	var outgraph = initFormGraph(twitterformrdf, debug);
	var isFieldProcessed = new Array();

	if(debug) addStatus('start scanning fields ...');
	
	twitterformrdf
	.prefix('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
	.prefix('pb', 'http://ld2sd.deri.org/pb/ns#')
	.prefix('dcterms', 'http://purl.org/dc/terms/')	
	.where('?rdform rdf:type pb:RDForm')
	.where('?rdform pb:field ?field')
	.where('?field rdf:type pb:UpdateableField')
	.where('?field pb:key ?key')
	.where('?field dcterms:title ?title')
	.where('?field pb:value ?val')
	.where('?val rdf:value ?fval')
	.each(function () {
		var newval = null;
		var testval = null;
		
		if(debug) addStatus(' - currently looking at path: <br />-- form=' + this.rdform.value + ' <br />-- field=' + this.field.value + ' <br />-- key=' + this.key.value + ' <br />-- val=' + this.val.value);
		
		if(isFieldProcessed[this.field.value]) return; //the field has already been processed 
		else { // we have not yet come accross this field
			// obtain the value of the field currently in scope (for example for @about='http://ld2sd.deri.org/pb/demo#fo1.f2.val')		
			// we need to make some destincitons based on type of UI widget (select, checkbox, etc.)
			if(debug) addStatus(' - detected updateable field ' + this.key.value);
			
			if ($("div[rel=pb:value] input[type=text]")){// a text input field
				testval = $("[about=" + this.val.value + "] input[type=text]").val();
				if(testval == undefined) {
					 newval = null; // this field value doesn't exist, forget about it and flag so in newval
					if(debug) addStatus(' - there is no text input field for ' + newval);
				}
				else { // obtain new value for field
					newval = $("[about=" + this.val.value + "]  input[type=text]").val();
					if(debug) addStatus(' - there is an input text field with value=' + newval);
				}
			}

			// make sure to flag that field so that it gets processed only once
			isFieldProcessed[this.field.value] = true;
			
			addFieldGraph(outgraph, this.rdform.value, this.field.value, this.key.value, this.val.value, newval, verbosedebug);

			if(debug) addStatus('outgraph size=' +  outgraph.size());
			
			if(debug) addStatus('Gonna pushback value of field with label "' + this.title.value + '" and key=' + this.key.value +" with value=" + newval);
			}
		}// end of isFieldProcessed[this.val.value]
	);
	
	if(debug) addStatus('scanned all fields and ready to submit RDF diff graph.');
	if(verbosedebug) dumpGraphToStatus(outgraph);
	if(debug) alert('pushing back changes');
	var jsonoutgraph = $.toJSON(outgraph.dump());
	
	$.post("http://localhost:8888/pushback/pbcontroler/demo2.php", 
			{	
				ing:  jsoningraph,
				outg: jsonoutgraph,
				debug: debug
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

	if(debug) dumpGraphToStatus(databank);
	return databank;	
}

function addFieldGraph(databank, formURI, fieldURI, keyURI, valURI, val, debug){
	if(debug) addStatus('graph size before update=' + databank.size());
	if(debug) addStatus(' adding field to outgraph <br /> -- form=' + formURI + ' <br />-- field=' + fieldURI + ' <br />-- key=' + keyURI + ' <br />-- val=' + valURI + ' <br />-- value=' + val);
	databank
		.add('<' + formURI + '> pb:field <' + fieldURI + '> .')
		.add('<' + fieldURI + '> rdf:type pb:UpdateableField .')
		.add('<' + fieldURI + '> pb:key <' +  keyURI + '> .')
		.add('<' + fieldURI + '> pb:value <' +  valURI + '> .')
		.add('<' + valURI + '> rdf:value "' +  val + '" .');
	if(debug) addStatus('graph size after update=' + databank.size());
	if(debug) dumpGraphToStatus(databank);
	return databank;	
}

function addStatus(str){
	var tmp = document.getElementById('result').innerHTML;
	document.getElementById('result').innerHTML = tmp + '<br />' + str;
}

function dumpGraphToStatus(databank){
	addStatus('graph dump: ' +  $.toJSON(databank.dump()));	
}
