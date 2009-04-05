/*
<#this>	dcterms:creator [
			owl:sameAs <http://sw-app.org/mic.xhtml#i> ;
			foaf:name "Michael Hausenblas" ;
			foaf:mbox <mailto:michael.hausenblas@deri.org> .
		] ;
		dcterms:isPartOf <http://ld2sd.deri.org/pushback/doap#pushback"> ;
		rdfs:seeAlso <http://esw.w3.org/topic/PushBackDataToLegacySourcesFusion>.
*/

var DEBUG = false; // show more detailed status messages and halt on critical points

function startFusion(debug) {
	showStatus("Start fusion ...");
	if ($("#debug").is(":checked")){
		DEBUG = true;
	}
	rdfdoc.getRDFURL('relay.php?URI=' + mapInstanceData(false), fuse);
}

function fuse(){
	var pbNS = "http://rdfs.org/ns/rdforms#";
	var rdf_type = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
	var pb_RDForm =  pbNS + "RDForm";
	var pb_UpdateableField =  pbNS + "UpdateableField";
	var pb_field =  pbNS + "field";
	var pb_operation =  pbNS + "operation";
	var pb_onfield =  pbNS + "onField";
	
		
	var inputHTMLForm = $("#inputHTMLForm").val();
	var baseURI = $("#baseURI").val();
	var rdformRDFaHTML ="";
	var rdforms = rdfdoc.Match(null, null, rdf_type, pb_RDForm);
	
	$("#rdform").val("");
	
	rdformURI = rdforms[0].subject; // the URI of the RDForm
	formID = rdformURI.slice(rdformURI.indexOf("#") + 1);
	
	$(inputHTMLForm, "[id='"+formID+"']").each(function(i) {// look up in th input for the corresponding form 
		rdformRDFaHTML += startForm(rdformURI, this.id, this.action, this.method);
		var rdformfields;
		//alert(rdformfields.toNTriples());

		// process all operations
		rdformfields = rdfdoc.Match(null, rdformURI, pb_operation, null); // get operations of this RDForm
		for (rdformfield in rdformfields) {
			var rdformfieldURI = rdformfields[rdformfield].object;
			if(rdformfieldURI != undefined) {
				var opID = rdformfieldURI.slice(rdformfieldURI.indexOf("#") + 1);
				var operation = rdfdoc.getSingleObject(null, rdformfieldURI, rdf_type, null);
				var onfield = rdfdoc.getSingleObject(null, rdformfieldURI, pb_onfield, null);
				showStatus("OP::looking at operation with id=" + opID + " of type=" + operation + " on field=" + onfield, DEBUG);
				rdformRDFaHTML += addCRUDop(baseURI, opID, operation, onfield);
			}	
		}

		// process all fields
		rdformfields = rdfdoc.Match(null, rdformURI, pb_field, null); // get fields of this RDForm
		for (rdformfield in rdformfields) {
			var rdformfieldURI = rdformfields[rdformfield].object;
			if(rdformfieldURI != undefined) {
				var fieldID = rdformfieldURI.slice(rdformfieldURI.indexOf("#") + 1);
				showStatus("CONTENT::looking at field with id=" + fieldID, DEBUG);
				// case distinction per field type (input, textarea, etc.)
				rdformRDFaHTML += createFieldTypeValues(rdfdoc, rdformfieldURI, fieldID);
				// end of case distinction
			}	
		}
		rdformRDFaHTML += endForm(this.id);
	});
	$("#rdform").val(rdformRDFaHTML);
	showStatus("Fusion done. RDForm created.");
}

function getLabelForField(fieldID){
	var label = "LABEL_FOR_" + fieldID;
	$($('#inputHTMLForm').val()).find("[for='" + fieldID + "']").each(function() {
		label = $(this).text();
		showStatus("Label for element with id=" + fieldID + " is '" + label + "'" , DEBUG);
	});	
	return label;
}

function createFieldTypeValues(rdfdoc, rdformfieldURI, fieldID) {
	var rdf_value = "http://www.w3.org/1999/02/22-rdf-syntax-ns#value";
	var ret = "";
	var fieldTagName = "";
	var fieldType = "";
	var fieldSelector = "";
	var fieldValue = "";
	var fieldSize = "";
		
	// look up the element's tag to drive case distinction
	$($('#inputHTMLForm').val()).find("#"+fieldID).each(function() {
		fieldTagName = this.tagName.toLowerCase(); 
		fieldType = this.type;
		fieldValue = this.value;
		fieldSize = this.size;
		
		if(fieldTagName === HTML_INPUT_FIELD) {
			fieldSelector = fieldTagName + fieldType; // special treatment for all <input type="xxx" ... />
			showStatus("Looking at an element with tag=" + fieldTagName.toLowerCase() + ", type=" + fieldType + ", id=" + this.id + ", value=" + fieldValue, DEBUG);
		}
		else {
			fieldSelector = fieldTagName;
			showStatus("Looking at an element with tag=" + fieldTagName.toLowerCase() + " and id=" + this.id, DEBUG);
		}
	});
	
	if(isKnownFieldType(fieldSelector)) {
		// for each field set the according decorations
		ret += startField(rdformfieldURI, fieldID);
		// look up the correct label here:
		ret += addFieldKey(rdformfieldURI + ".key", fieldID, getLabelForField(fieldID));// via <label for="ID" ...> 

		// following three lines are generic, hence independed of field type
		ret += " \n   <!-- START OF FIELD VALUE {" + fieldID + "} -->\n";
		ret += "   <div rel=\"pb:value\">\n";
		ret += "    <div about=\"" + rdformfieldURI + "\" typeof=\"pb:FieldValue\">";	
	
		// and here comes the actual case distinction
		showStatus("About to add field with fieldSelector=" + fieldSelector, DEBUG);
		switch (fieldSelector){
			case (HTML_INPUT_FIELD+HTML_TYPE_TEXT): // <input type="text" ... />
			
				fieldValue = rdfdoc.getSingleObject(null, rdformfieldURI + ".val", rdf_value, null); // retrieve value from supplied FOAF file 
				ret += startTextFieldValue(rdformfieldURI + ".val", fieldID, fieldValue, fieldSize);//, maxlength);
				showStatus("Added field tag=" + HTML_INPUT_FIELD + "/" + HTML_TYPE_TEXT +  " with id=" + fieldID, DEBUG);
				break;
			case (HTML_TEXT_AREA): // <textarea ... />
				ret += startTextAreaValue(rdformfieldURI + ".val", fieldID);
				showStatus("Added field tag=" + HTML_TEXT_AREA  + " with id=" + fieldID, DEBUG);
				break;
		}
		ret += "\n";
		ret += endFieldValue(fieldID);
		ret += endField(fieldID);
	}
	else handleUnknownFieldTypeValues(fieldSelector);
	return ret;
}

function isKnownFieldType(fieldSelector) {
	switch (fieldSelector){
		case (HTML_INPUT_FIELD+HTML_TYPE_TEXT): // <input type="text" ... />
			return true;
		case (HTML_TEXT_AREA): // <textarea ... />
			return true;
		default: 
			return false;
	}
}

function handleUnknownFieldTypeValues(fieldSelector) {	
	var info = "Dunno how to handle element " + fieldSelector + ". I only know: \n\n";
	info += "+ " + HTML_INPUT_FIELD + "/" + HTML_TYPE_TEXT + "\n\n";
	info += "+ " + HTML_TEXT_AREA + "\n\n";
	info += "so far."
	alert(info);
}

function viewInstanceData(){
	var rdfinstanceURI = $("#rdfinstanceURI").val();

	dump("<p>pulling from "+ rdfinstanceURI + "</p>"); 

	$.ajax({
	   url: "relay.php",
	   data: "URI=" + escape(rdfinstanceURI),
	   success: function(data, status){
			dump("<pre>" + htmlentities(data) + "</pre>");
			showStatus("Preview instance data done.");	 
		},
		error:  function(XMLHttpRequest, status, errorThrown){
			showStatus("Error previewing instance data.");
		}
	 });			
}

function mapInstanceData(showDump){
	var rdfinstanceURI = $("#rdfinstanceURI").val();
	var sparqlService = "http://sparql.org/sparql?query=";
	var mappingQuery = $("#mapping").val();
	var queryURI =  sparqlService + escape(mappingQuery) + "&default-graph-uri=" + escape(rdfinstanceURI);
	//alert(queryURI);
	
	if(showDump) {
		dump("<p>mapping with "+ rdfinstanceURI + "</p>"); 

		$.ajax({
		   url: "relay.php",
		   data: "URI=" + escape(queryURI),
		   success: function(data, status){
				//alert(data);
				 dump( "<pre>" + htmlentities(data) + "</pre>");
				showStatus("Mapping instance data done.");	 
			},
			error:  function(XMLHttpRequest, status, errorThrown){
				showStatus("Error mapping instance data.");
			}
		 });
		$("#dumpwindow").fadeIn("slow");
	}
	return escape(queryURI);
}


function loadHTMLForm(){
	var htmlformURI = $("#inputHTMLfieldURI").val();
	$.ajax({
	   url: "relay.php",
	   data: "URI=" + escape(htmlformURI),
	   success: function(data, status){
			$("#inputHTMLForm").val(data);
			//alert(extractForm(data));
			showStatus("Load HTML form done.");	 
		},
		error:  function(XMLHttpRequest, status, errorThrown){
			showStatus("Error loading HTML form.");
		}
	 });	
}

function loadMapping(){
	var mappingdocURI = $("#mappingdocURI").val();
	$.ajax({
	   url: "relay.php",
	   data: "URI=" + escape(mappingdocURI),
	   success: function(data, status){
			$("#mapping").val(data);
			showStatus("Load mapping done.");	 
		},
		error:  function(XMLHttpRequest, status, errorThrown){
			showStatus("Error loading mapping form.");
		}
	 });	
}

function extractForm(html){
	var afterForm = html.indexOf("form");
	return html.substring(afterForm-1);
}

function showStatus(message, doStop) {
	$("#pbfusion-status").html(message); 
	if(doStop) 	alert('continue?');    
}

function dump(message) {
	$("#dump").html(message); 
}

function decodexml(xml){
   var text = xml.replace( /&quot;/g, '"' );
   return text;
}



/********** RDFORM CONSTRUCTION *************/
function startForm(formURI, id, action, method){
	var dctermsNS = "http://purl.org/dc/terms/";
	var ret = "<div xmlns:xsd =\"http://www.w3.org/2001/XMLSchema#\" \n xmlns:dcterms=\"http://purl.org/dc/terms/\" \n xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\" \n xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"  \n xmlns:pb=\"http://rdfs.org/ns/rdforms#\">\n\n";
	
	ret += "<!-- START OF FORM {" + id + "} -->\n";
	ret += "<form id=\"" + id + "\" action=\"" + action + "\" method=\"" + method + "\" about=\""+formURI + "\" typeof=\"pb:RDForm\">";
	ret += "\n";
	return ret;
}

function endForm(id){	
	return "\n</form>\n<!-- END OF FORM {" + id + "} -->\n\n</div>";
}

function addCRUDop(baseURI, id, operation, onfield){
	var ret = " \n <!-- START OF CRUD OPERATION FOR FIELD {" + id + "} -->\n";
	ret += " <div rel=\"pb:operation\">\n";
	ret += "  <div about=\"" + baseURI + id + "\" typeof=\"" + operation + "\">\n";
	ret += "   <span rel=\"pb:onField\" resource=\"" + onfield + "\" />\n";
	ret += "  </div>\n";
	ret += " </div>\n <!-- END OF CRUD OPERATION FOR FIELD {" + id + "} -->\n";	
	return ret;
}


function startField(fieldURI, id){
	var ret = " \n <!-- START OF FIELD {" + id + "} -->\n";
	ret += " <div rel=\"pb:field\">\n";
	ret += "  <div about=\"" + fieldURI + "\" typeof=\"pb:UpdateableField\">";
	ret += "\n";
	return ret;
}

function endField(id){
	var ret = "  \n  </div>\n </div>\n <!-- END OF FIELD {" + id + "} -->";
	ret += "\n";
	return ret;
}


function addFieldKey(fieldURI, id, label){
	var ret = "   <!-- START OF FIELD KEY {" + id + "} -->\n";
	ret += "   <label rel=\"pb:key\" resource=\"" + fieldURI + "\" property=\"dcterms:title\">" + label + "</label>:<br />";
	return ret;
}


function startTextFieldValue(fieldURI, id, value, size, maxlength){	// first two parameter mandatory
	var ret = "";
	var htmlfield = HTML_FIELD_TYPES[HTML_INPUT_FIELD+HTML_TYPE_TEXT];
	// for  <input type="text" id="$ID" property="rdf:value" content="$VALUE" value="$VALUE" size="$SIZE" maxlength="$MAXLENGTH" /> the template fill is:
	htmlfield = htmlfield.replace("ID", id);
	if(value != undefined) htmlfield = htmlfield.replace(/VALUE/g, value);
	else htmlfield = htmlfield.replace(/VALUE/g, "");
	if(size != undefined) htmlfield = htmlfield.replace(/SIZE/g, size);
	else htmlfield = htmlfield.replace(/SIZE/g, "30");
	if(maxlength != undefined) htmlfield = htmlfield.replace(/MAXLENGTH/g, maxlength);
	else htmlfield = htmlfield.replace(/MAXLENGTH/g, "30");
	return "     \n     " + htmlfield;
}

function startTextAreaValue(fieldURI, id, value, cols, rows, wrap){ // first two parameter mandatory
	var ret = "";
	var htmlfield = HTML_FIELD_TYPES[HTML_TEXT_AREA];
	// for  <textarea id="$ID" property="rdf:value" cols="$COLS" rows="$ROWS" />$VALUE</textarea> the template fill is:
	htmlfield = htmlfield.replace("ID", id);
	if(value != undefined) htmlfield = htmlfield.replace(/VALUE/g, value);
	else htmlfield = htmlfield.replace(/VALUE/g, "");
	if(cols != undefined) htmlfield = htmlfield.replace(/COLS/g, cols);
	else htmlfield = htmlfield.replace(/COLS/g, "10");
	if(rows != undefined) htmlfield = htmlfield.replace(/ROWS/g, rows);
	else htmlfield = htmlfield.replace(/ROWS/g, "5");
	return "     \n     " + htmlfield;
}

function endFieldValue(id){
	var ret = "    </div>\n   </div>\n <!-- END OF FIELD VALUE {" + id + "} -->";
	return ret;
}

// scan HTML form template for @id
/*
showStatus('Scanning HTML form template ...');
$(inputHTMLForm).filter('*').each(function(i){ 
	showStatus('Detected: @id=' + this.id + ' in HTML Form Template');
	if(debug) alert('Next?');
})
showStatus('Scanning HTML form template done.');
*/
