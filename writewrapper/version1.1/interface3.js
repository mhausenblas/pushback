
function startHandleScan() {
	var debug = false;
	
	if ($("#debug").is(":checked")){
		debug = true;
	}
	
	var ref = $("#page2scan").val();//document.referrer;
	var numofLinks = 0;
	var RDF_NS = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";

	addStatus('Processing ' + ref);
	
	rdfdoc.getRDFURL('relay.php?URI=' + ref, findPosts);
	
}


function findPosts(){
	var siocNS = "http://rdfs.org/sioc/types#";
	var ref = $("#page2scan").val();

	var posts = rdfdoc.Match(null, null, null, siocNS + "BoardPost");
	
	for (post in posts) {
		var postURI = posts[post].subject;
		if(postURI.indexOf("http://") == 0) {
			var status = postURI.slice("http://linkeddata.uriburner.com/about/rdf/".length);
			var base = postURI.slice(ref.length + "status/".length + 1);
			var statusID = base.slice(0, base.indexOf("#this"));
			addStatus('found: <a href="' + status.slice(0, status.indexOf("#this")) +'">' + statusID + '</a>');
		}
	}
}

function pushback() {
	var debug = false;
	var verbosedebug  = false;
	
	if ($("#debug").is(":checked")){
		debug = true;
	}
		
	addStatus('pushing back data to RDF wrapper ...');

	// glean RDF from the RDFa marked-up form - this is our input graph
	var calendarformrdf = $('#calendarform').rdf();
	var jsoningraph = $.toJSON(calendarformrdf.databank.dump());
	
	// go through all updateable fields and update with current values (set by user) - this is our output graph
	var outgraph = initFormGraph(calendarformrdf, debug);
	var founddelete = false;
	var isFieldProcessed = new Array();
	var postFieldValues = "";
	var id = "";
	var i = 0;
	var operation = "";
	
	if(debug) addStatus('start scanning fields ...');
	
	calendarformrdf
	.prefix('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
	.prefix('pb', 'http://ld2sd.deri.org/pb/ns#')
	.prefix('dcterms', 'http://purl.org/dc/terms/')	
	.where('?rdform rdf:type pb:RDForm')
	.where('?rdform pb:operation ?crudop')
	.where('?rdform pb:field ?field')
	.where('?field rdf:type ?type')
	.where('?crudop	rdf:type ?crudoptype')
	.where('?field pb:key ?key')
	.where('?field dcterms:title ?title')
	.where('?field pb:value ?val')
	.where('?val rdf:value ?fval')
	.each(function () {
		var newval = null;
		var testval = null;
		
		if(debug) addStatus(' - currently looking at path: <br />-- form=' + this.rdform.value + 
		                    ' <br />-- field=' + this.field.value + ' <br />-- key=' + this.key.value + 
							' <br />-- val=' + this.val.value + 
							' <br />-- operation=' + this.crudoptype.value);
		
		if (this.crudoptype.value == "http://ld2sd.deri.org/pb/ns#CRUDOperationDELETE") operation = "delete";
		else if (this.crudoptype.value == "http://ld2sd.deri.org/pb/ns#CRUDOperationUPDATE") operation = "update";
		else operation = "create";
		
		if(isFieldProcessed[this.field.value]) return; //the field has already been processed 
		else { // we have not yet come accross this field
			// obtain the value of the field currently in scope (for example for @about='http://ld2sd.deri.org/pb/demo#fo1.f2.val')		
			// we need to make some destincitons based on type of UI widget (select, checkbox, etc.)
			if(debug) addStatus(' - detected updateable field ' + this.key.value);
			
			if ($("div[rel=pb:value] input[type=text]")){// a text input field
				testval = $("[about=" + this.val.value + "] input[type=text]").val();
				if(testval == undefined) {
					 newval = null; // this field value doesn't exist, forget about it and flag so in newval
					if(debug) addStatus(' - there is no text input field for ' + this.val.value);
				}
				else { // found a field to delete
					founddelete = true;
					newval = $("[about=" + this.val.value + "]  input[type=text]").val();
					id = $("[about=" + this.val.value + "]").find("input").attr("id");				
					postFieldValues = postFieldValues + id + "=" + newval + "&";
					if(debug) addStatus(' - there is an input text field with value=' + this.val.value);
				}
			}

			// make sure to flag that field so that it gets processed only once
			isFieldProcessed[this.field.value] = true;

			addFieldGraph(outgraph, this.rdform.value, this.crudop.value, this.crudoptype.value, this.field.value, this.key.value, this.val.value, newval, verbosedebug);
	
			if(debug) addStatus('Gonna pushback value of field with label "' + this.title.value + '" and key=' + this.key.value);
			
			}
		}// end of isFieldProcessed[this.val.value]
	);
	
	if(debug) addStatus('scanned all fields and ready to submit RDF diff graph.');
	
	
	postFieldValues = postFieldValues + "operation=" + operation;
	
	$.ajax({
	   type: "GET",
	   url: "http://localhost/writewrapper/version2.1/updateCalendar.php",
	   data: postFieldValues,
	   success: function(msg){
		 document.write(msg );
		 document.close();
	   }
	 });

	if(founddelete) {// at elast one updatedable field has a CRUD DELETE operation on it, send the entire graph of the RDForm
		if(debug) addStatus('at least one field was detected which will be deleted.');
	}
	else { // otherwise send an empty graph
		 outgraph =  $.rdf();
	}
	
	if(debug) dumpGraphToStatus(outgraph);

	if(debug) alert((debug) ? 'pushing back changes in debug mode.' : 'pushing back changes.');
	var jsonoutgraph = $.toJSON(outgraph.dump());
	
	$.post("http://localhost:8888/pushback/pbcontroler/demo3.php", 
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

function addFieldGraph(databank, formURI, crudOPURI, crudOPTypeURI, fieldURI, keyURI, valURI, val, debug){
	if(debug) addStatus('graph size before update=' + databank.size());
	if(debug) addStatus(' adding field to outgraph <br /> -- form=' + formURI + ' <br />--- CRUD operation=' + crudOPTypeURI + ' <br />--- field=' + fieldURI + ' <br />---- key=' + keyURI + ' <br />---- val=' + valURI + ' <br />---- value=' + val);
	databank
		.add('<' + formURI + '> pb:operation <' + crudOPURI + '> .')
		.add('<' + crudOPURI + '> pb:onField <' + fieldURI + '> .')
		.add('<' +  crudOPURI + '> rdf:type <' + crudOPTypeURI + '> .')
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
