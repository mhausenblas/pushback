$(document).ready(function(){
	$('#get-bugs-from-jira').click(getBugsFromJira);
	$('#clear-bugs').click(clearBugs);
	// lets pull bugs description from server before user will start using this demo 
	getBugsFromJira();
});


/*
 * Pull bugs description RDFDump.rdf file from server and generate a bug list  
 */
function getBugsFromJira(){
	var debug = false;
	if ($("#debug").is(":checked")){
		debug = true;
	}

	$('#ajax-indicator').show();
	$('#result').html('');
	$('#bugs').html('');
	
	

	// here we should call the script which will update the RDFDocument
	// because now the RDFDump data from jira are just placed on some server in the web
	$.ajax({
		  url: "../pbcontroler/updateJiraRDF.php",
		  async:false,
		  success: function(data){
				var result = data;
				if(debug)addStatus('<br/ >'+result);
		  }
		});

	var ref = $("#page2scan").val();
	var rand_no=Math.floor(Math.random()*10001);
	rdfdoc = new RDF();	
	/*
	 * to Michael :-) I'll be happy if RDF class has a method called "reset" 
	 * to clear all data which were processed in previous calls
	 * then instead create new object here we would be able to reuse existing one
	 */
	rdfdoc.getRDFURL('relay.php?URI=' + ref +'&rand_no='+rand_no , processBugs);
	$('#ajax-indicator').hide();
}

function processBugs(){
	var debug = false;
	if ($("#debug").is(":checked")){
		debug = true;
	}
	//var siocNS = "http://rdfs.org/sioc/types#";
	var bNS ="http://baetle.googlecode.com/svn/ns/#";
	var rdfNS ="http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	
	var bugsTriple = rdfdoc.Match(null, null , rdfNS +"type", bNS + "Issue");
	
	var bugsTripelSubjects = new Array();
	var i=0;
	for (bugTriple in bugsTriple) {
		var subject = bugsTriple[bugTriple].subject;
		var predicate = bugsTriple[bugTriple].predicate;
		var object = bugsTriple[bugTriple].object;
		
		if(subject!=undefined && subject.indexOf("/browse/")!=-1 ){	
			// hurray we have a bug 
			bugsTripelSubjects[i]=subject;
			i++;
		}
	}
	// lets now iterate through end grab all info we neeed 
	
	for(var i=0;i<bugsTripelSubjects.length;i++){
		var bugId = bugsTripelSubjects[i];
		
		var summary = findTriple(bugId, bNS +"summary", null,"o");
		var reporter = findTriple(bugId, bNS +"reporter", null,"o");
			reporter = reporter.substring(reporter.indexOf("=")+1);
		var assign_to = findTriple(bugId, bNS +"assigned_to", null,"o");
			assign_to = assign_to.substring(assign_to.indexOf("=")+1);
		var type = findTriple(bugId, bNS +"type", null,"o");
			type = type.substring( "http://localhost:8080/".length);
			if(type=="Bug")type = "1";
			if(type=="NewFeature")type = "2";
			if(type=="Task")type = "3";
			if(type=="Improvement")type = "4";
			
		var priority = findTriple(bugId, bNS +"priority", null,"o");
			if(priority=="Blocker")priority="1";
			if(priority=="Critical")priority="2";
			if(priority=="Major")priority="3";
			if(priority=="Minor")priority="4";
			if(priority=="Trivial")priority="5";
			
		var due_date = findTriple(bugId, bNS +"due_date", null,"o");
			if(due_date==undefined)due_date="";
			else{ 
				// here covert date to correct format
				//var d = new Date();
				//due_date = d.getDate+'/'+d.getMonth()+'/'+d.getFullYear();
			}
			
		addToBugList(bugId.substring("/browse/".length ),summary,reporter,assign_to,type,priority,due_date);
		
		if(debug)addStatus('<br />found bug <br />id: '+bugId);
		if(debug)addStatus('summary: ' + summary);
		if(debug)addStatus('assigned to: ' + assign_to);
		if(debug)addStatus('reporter: ' + reporter);
		if(debug)addStatus('priority: ' + priority);
		if(debug)addStatus('due date: ' + due_date);
		if(debug)addStatus('type: ' + type);
	}
	

	$('.item').bind('click',function(){
		var id = $(this).attr("id");
		$("#f_bug-id").val(id);
		$("#f_bug-summary").val($('#'+id+'-summary').attr("title"));
		$("#f_bug-reporter").val($('#'+id+'-reporter').attr("title"));
		$("#f_bug-assign_to").val($('#'+id+'-assign_to').attr("title"));
		$("#f_bug-type").val( $('#'+id+'-type').attr("title") ,true);
		$("#f_bug-priority").val($('#'+id+'-priority').attr("title"));
		$("#f_bug-duedate").val($('#'+id+'-due_date').attr("title"));
		
	});
}

function addToBugList(bugid,summary,reporter,assign_to,type,priority,due_date){
	var bugList = $('#bugs').html();
	
	var toAdd  = '<br /><span id = "'+bugid+'" class="item"> '+bugid+'</span>';
		toAdd += '<span style="display:none;" id = "'+bugid+'-summary" title="'+summary+'" />';
		toAdd += '<span style="display:none;" id = "'+bugid+'-reporter" title="'+reporter+'" />';
		toAdd += '<span style="display:none;" id = "'+bugid+'-assign_to" title="'+assign_to+'" />';
		toAdd += '<span style="display:none;" id = "'+bugid+'-type" title="'+type+'" />';
		toAdd += '<span style="display:none;" id = "'+bugid+'-priority" title="'+priority+'" />';
		toAdd += '<span style="display:none;" id = "'+bugid+'-due_date" title="'+due_date+'" />';
	
	$('#bugs').html(bugList+toAdd);
}

function clearBugs(){
	$('#bugs').html("");
}


function findTriple(s,p,o,ret){
	var triples = rdfdoc.Match(null, s , p, o);
	for (t in triples) {
		var subject = triples[t].subject;
		var predicate = triples[t].predicate;
		var object = triples[t].object;
		
		if(ret=="s"){
			if(subject!=undefined ){	
				return subject;
			}
		}
		if(ret=="p"){
			if(predicate!=undefined ){	
				return predicate;
			}
		}
		if(ret=="o"){
			if(object!=undefined ){	
				return object;
			}
		}
	}
}



function putBugIdToForm(){
	//$(this)
	alert("zf");
}

function pushback() {
	$('#ajax-indicator').show();

	var debug = false;
	var verbosedebug  = false;
	
	if(!validateMandatoryFields()){
		return false;
	}
	$('#result').html('');
	
	if ($("#debug").is(":checked")){
		debug = true;
	}
		
	addStatus('pushing back data to RDF wrapper ...');

	// glean RDF from the RDFa marked-up form - this is our input graph
	var jiraformrdf = $('#f_jira-form').rdf();
	var jsoningraph = $.toJSON(jiraformrdf.databank.dump());
	
	if(debug){
		//addStatus('json in graph: <br /> ');
		//addStatus(jsoningraph);
	}
	
	
	// go through all updateable fields and update with current values (set by user) - this is our output graph
	var outgraph = initFormGraph(jiraformrdf, debug);
	
	if(debug){
		//addStatus('json out graph: <br />');
		//addStatus(  $.toJSON( outgraph.dump() )  );
	}
	
	
	var foundupdate = false;
	var isFieldProcessed = new Array();

	
	
	if(debug) addStatus('start scanning fields ...');

	
	
	jiraformrdf
	.prefix('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
	.prefix('pb', 'http://ld2sd.deri.org/pb/ns#')
	.prefix('dcterms', 'http://purl.org/dc/terms/')	
	.where('?rdform rdf:type pb:RDForm')
	.where('?rdform pb:operation ?crudop')
	.where('?rdform pb:field ?field')
	.where('?field rdf:type pb:UpdateableField')
	.where('?crudop pb:onField ?field')
	.where('?crudop	rdf:type ?crudoptype')
	.where('?field pb:key ?key')
	.where('?field dcterms:title ?title')
	.where('?field pb:value ?val')
	.where('?val rdf:value ?fval')
	.each(function () {
		var newval = null;
		var testval = null;
		
		if(debug){
			var msg = '<br />-- currently looking at path: <br />';
				msg+= '-- form=' + this.rdform.value + ' <br />';
				msg+= '-- field=' + this.field.value + ' <br />';
				msg+= '-- key=' + this.key.value + ' <br />';
				msg+= '-- val=' + this.val.value + ' <br />';
			addStatus(msg);
		}
		
		
		if(isFieldProcessed[this.field.value]) return; //the field has already been processed 
		else { // we have not yet come accross this field
			// obtain the value of the field currently in scope (for example for @about='http://ld2sd.deri.org/pb/demo#fo1.f2.val')		
			// we need to make some destincitons based on type of UI widget (select, checkbox, etc.)
			if(debug) addStatus(' - detected updateable field ' + this.key.value + '<br />');
			
			var textFieldjQuerySelector = "div[about='" + this.val.value + "'] > input[type=text]"; 
			var selectFieldjQuerySelector = "div[rel='pb:value'] > select[about='" + this.val.value + "']";
			
			if( $(textFieldjQuerySelector).length==1 ){
				if(debug){
					addStatus('--FOUND TEXT FIELD ' + this.val.value);
					addStatus('--USING SELECTOR ' + textFieldjQuerySelector);
				}
				testval = $(textFieldjQuerySelector).val();
				if(testval == undefined) {
					 newval = null; // this field value doesn't exist, forget about it and flag so in newval
					if(debug) addStatus(' - there is no text input field for ' + this.val.value);
				}
				else { // found a field to update
					foundupdate = true;
					newval = testval; 
					if(debug) addStatus(' - there is an input text field for: ' + this.val.value +' with value: '+newval);
				}
			}else if( $(selectFieldjQuerySelector).length==1 ){
				if(debug){
					addStatus('--FOUND SELECT FIELD ' + this.val.value);
					addStatus('--USING SELECTOR ' + selectFieldjQuerySelector);
				}
				testval = $(selectFieldjQuerySelector).val();
				if(testval == undefined) {
					 newval = null; // this field value doesn't exist, forget about it and flag so in newval
					if(debug) addStatus(' - there is no select field for ' + this.val.value);
				}
				else { // found a field to update
					foundupdate = true;
					newval = testval; 
					if(debug) addStatus(' - there is a select field for:' + this.val.value +' with value: '+newval);
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

	if(foundupdate) {// at elast one updatedable field has a CRUD DELETE operation on it, send the entire graph of the RDForm
		if(debug){
			addStatus('at least one field was detected which will be updated.');
		}
	}
	else { // otherwise send an empty graph
		 outgraph =  $.rdf();
	}
	
	if(debug){
		dumpGraphToStatus(outgraph);
	}

	//if(debug) alert((debug) ? 'pushing back changes in debug mode.' : 'pushing back changes.');
	var jsonoutgraph = $.toJSON(outgraph.dump());
	
	$.post("../pbcontroler/demo4.php", 
			{	
				ing:  jsoningraph,
				outg: jsonoutgraph,
				debug: debug
			} , 
			function(data){
				var result = data;
				addStatus('<br/ ><br/ >'+result);
				//document.getElementById('result').innerHTML = result;
			
				$('#ajax-indicator').hide();
			// REFRESH BUG LIST FROM JIRA
				getBugsFromJira();
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

	if(debug){
		dumpGraphToStatus(databank);
	}
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
//	var tmp = $('#result').html();
//	$('#result').html( tmp + '<br />' + str);
	
}

function dumpGraphToStatus(databank){
	addStatus('graph dump: ' +  $.toJSON(databank.dump()));	
}

function validateMandatoryFields(){
	if($('#f_bug-id').val()==''){
		alert('Bug id is mandatory');
		return false;
	}
	if($('#f_bug-summary').val()==''){
		alert('Bug summary is mandatory');
		return false;
	}
	if($('#f_bug-reporter').val()==''){
		alert('Bug reporter is mandatory');
		return false;
	}
	return true;
}
