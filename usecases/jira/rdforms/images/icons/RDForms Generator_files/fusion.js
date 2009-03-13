
function startFusion(debug) {
	showStatus("Start fusion ...");
	rdfdoc.getRDFURL('relay.php?URI=' + mapInstanceData(false), fuse);
}

function fuse(){
	var pbNS = "http://ld2sd.deri.org/pb/ns#";
	var rdf_type = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
	var pb_RDForm =  pbNS + "RDForm";
	var pb_field =  pbNS + "field";
		
	var inputHTMLForm = $("#inputHTMLForm").val();
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
				rdformRDFaHTML += startFieldValue(rdformfieldURI, fieldID);
				rdformRDFaHTML += endFieldValue(fieldID);
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
	var ret = "<!-- START OF FORM {" + id + "} -->\n";
	ret += "<form id=\"" + id + "\" action=\"" + action + "\" method=\"" + method + "\" about=\""+formURI + "\" typeof=\"pb:RDForm\">";
	ret += "\n";
	return ret;
}

function endForm(id){	
	return "</div>\n</form>\n<!-- END OF FORM {" + id + "} -->";
}

function startFieldValue(fieldURI, id){
	var ret = " <!-- START OF FIELD {" + id + "} -->\n";
	ret += " <div rel=\"pb:value\">\n";
	ret += "  <div about=\"" + fieldURI + "\" typeof=\"pb:FieldValue\">";
	ret += "\n";
	return ret;
}

function endFieldValue(id){
	var ret = " </div>\n <!-- END OF FIELD {" + id + "} -->";
	ret += "\n";
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
