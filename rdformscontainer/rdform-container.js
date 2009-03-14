/*
 Creating RDForms based on HTML forms
 Author: Aftab Iqbal [firstname.lastname@deri.org]
 Digital Enterprise Research Institute, Galway, Ireland(www.deri.ie)
 licensed under GPL v2
*/


WRAPPER = new Object();

WRAPPER.createRDForm = function() {

	var baseUri = "http://localhost:8081/pushback/";
	
	var loadForm = baseUri + "rdforms/rdform3.html";

	var loadImage = baseUri + "rdformscontainer/image/pb-logo.png";

	var rdformscript = $('<script>').attr('type', 'text/javascript')
	.html('$(document).ready(function() { });$.ui.dialog.defaults.bgiframe = true;$("#pb-container").dialog();');
	
	$('head').append(rdformscript);
	
	var contentdiv = $('<div title="&nbsp;Pb" img=' + loadImage + '></div>')
	.attr('id', 'pb-container');

	var  rdform = $('<p>').load(loadForm);
	contentdiv.append(rdform);
	
	$('body').append(contentdiv);
}

function includeJS(scriptName) {
    var htmlHead = document.getElementsByTagName('head').item(0);
    var js = document.createElement('script');
    js.setAttribute('type', 'text/javascript');
    js.setAttribute('src', scriptName);
    htmlHead.appendChild(js);
    return false;
}

function includeCSS(styleName) {
    var htmlHead = document.getElementsByTagName('head').item(0);
    var css = document.createElement('style');
    css.setAttribute('type', 'text/css');
    css.setAttribute('src', styleName);
    htmlHead.appendChild(css);
    return false;
}


WRAPPER.createRDForm();
