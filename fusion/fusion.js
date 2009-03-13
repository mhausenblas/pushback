/* RDForms field types */
var RDFORMS_FIELD_TYPES = new Array();
var RDFORMS_FIELD_XSD = new Array();

//////////////////////////////////////////////////////////////////////////////////////////////
// <input type="text" id="status-id-val" property="rdf:value" content="" value="" size="60" />
//////////////////////////////////////////////////////////////////////////////////////////////

RDFORMS_FIELD_TYPES['HTML_FIELD_TEXT'] = "<input type=\"text\" id=\"ID\" property=\"rdf:value\" content=\"VALUE\" value=\"VALUE\" size=\"SIZE\" maxlength=\"MAXLENGTH\" />";

RDFORMS_FIELD_XSD['HTML_FIELD_TEXT'] = "xsd:string";

//////////////////////////////////////////////////////////////////////////////////////////////
//textarea
//checkbox
//radio
//file
//select

function startFusion(debug) {
	showStatus("Start fusion ...");
	rdfdoc.getRDFURL('relay.php?URI=' + mapInstanceData(false), fuse);
}

function fuse(){
	var pbNS = "http://ld2sd.deri.org/pb/ns#";
	var rdf_type = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
	var pb_RDForm =  pbNS + "RDForm";
	var pb_UpdateableField =  pbNS + "UpdateableField";
	var pb_field =  pbNS + "field";
		
	var inputHTMLForm = $("#inputHTMLForm").val();
	var baseURI = $("#baseURI").val();
	var rdformRDFaHTML ="";
	var rdforms = rdfdoc.Match(null, null, rdf_type, pb_RDForm);
	
	$("#rdform").val("");
	
	rdformURI = rdforms[0].subject; // the URI of the RDForm

	$(inputHTMLForm, "[id='" + rdformURI.slice(rdformURI.indexOf("#") + 1) + "']").each(function (i) {// look up in th input for the corresponding form 
		rdformRDFaHTML += startForm(rdformURI, this.id, this.action, this.method);
		var rdformfields = rdfdoc.Match(null, rdformURI, pb_field, null); // get fields of this RDForm
		//alert(rdformfields.toNTriples());
		for (rdformfield in rdformfields) {
			var rdformfieldURI = rdformfields[rdformfield].object;
			if(rdformfieldURI != undefined) {
				//alert(rdformfieldURI);
				var fieldID = rdformfieldURI.slice(rdformfieldURI.indexOf("#") + 1);

				// select C(R)UD op here:
				rdformRDFaHTML += addCRUDop(baseURI, rdformfieldURI, fieldID);

				// for each field set the according decorations
				rdformRDFaHTML += startField(rdformfieldURI, fieldID);
				// look up the correct label here:
				rdformRDFaHTML += addFieldKey(rdformfieldURI + ".key", fieldID, "LABEL");
				//make case distinction on field type here:
				rdformRDFaHTML += startTextFieldValue(rdformfieldURI + ".val", fieldID);
				rdformRDFaHTML += endFieldValue(fieldID);
				rdformRDFaHTML += endField(fieldID);
			}	
		}
		rdformRDFaHTML += endForm(this.id);
	});
	$("#rdform").val(rdformRDFaHTML);
	showStatus("Fusion done. RDForm created.");
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

function extractForm(html){
	var afterForm = html.indexOf("form");
	return html.substring(afterForm-1);
}

function showStatus(message) {
	$("#status").html(message);     
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
	var ret = "<div xmlns:xsd =\"http://www.w3.org/2001/XMLSchema#\" \n xmlns:dcterms=\"http://purl.org/dc/terms/\" \n xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\" \n xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"  \n xmlns:pb=\"http://ld2sd.deri.org/pb/ns#\">\n\n";
	
	ret += "<!-- START OF FORM {" + id + "} -->\n";
	ret += "<form id=\"" + id + "\" action=\"" + action + "\" method=\"" + method + "\" about=\""+formURI + "\" typeof=\"pb:RDForm\">";
	ret += "\n";
	return ret;
}

function endForm(id){	
	return "\n</form>\n<!-- END OF FORM {" + id + "} -->\n\n</div>";
}

function addCRUDop(baseURI, onfieldURI, id){
	var ret = " \n <!-- START OF CRUD OPERATION FOR FIELD {" + id + "} -->\n";
	ret += " <div rel=\"pb:operation\">\n";
	ret += "  <div about=\"" + baseURI + "crud_delete\" typeof=\"pb:CRUDOperationDELETE\">\n";
	ret += "   <span rel=\"pb:onField\" resource=\"" + onfieldURI + "\" />\n";
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
	ret += "   <label rel=\"pb:key\" resource=\"" + fieldURI + "\" property=\"dcterms:title\">" + label + "</label> :";
	return ret;
}


function startTextFieldValue(fieldURI, id, value, size, maxlength){
	var ret = "";
	var htmlfield = RDFORMS_FIELD_TYPES["HTML_FIELD_TEXT"];
	// for  <input type="text" id="ID" property="rdf:value" content="VALUE" value="VALUE" size="SIZE" maxlength="MAXLENGTH" /> the template fill is:
	htmlfield = htmlfield.replace("ID", id);
	if(value != undefined) htmlfield = htmlfield.replace(/VALUE/g, value);
	else htmlfield = htmlfield.replace(/VALUE/g, "");
	if(size != undefined) htmlfield = htmlfield.replace(/SIZE/g, size);
	else htmlfield = htmlfield.replace(/SIZE/g, "30");
	if(maxlength != undefined) htmlfield = htmlfield.replace(/MAXLENGTH/g, maxlength);
	else htmlfield = htmlfield.replace(/MAXLENGTH/g, "30");
	// end of <input type="text" ... handling
	ret += " \n   <!-- START OF SIMPLPE TEXT FIELD VALUE {" + id + "} -->\n";
	ret += "   <div rel=\"pb:value\">\n";
	ret += "    <div about=\"" + fieldURI + "\" typeof=\"pb:FieldValue\">";	
	ret += "     \n     " + htmlfield;
	ret += "\n";
	return ret;
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
