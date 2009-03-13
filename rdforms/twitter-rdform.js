/*
 Creating RDForms based on HTML forms
 http://sw-app.org/mic.xhtml#i
 licensed under GPL v2
*/


WRAPPER = new Object();

WRAPPER.createRDForm = function() {
	
	includeJS("http://jqueryui.com/jquery-1.3.2.js");
	includeJS("http://jqueryui.com/ui/ui.core.js");
	includeJS("http://jqueryui.com/ui/ui.draggable.js");
	//includeJS("http://localhost:8888/pushback/rdforms/twitter/twitter-rdform.js");
	includeCSS("http://jqueryui.com/themes/base/ui.all.css");
	includeCSS("http://jqueryui.com/demos/demos.css");
	var rdformscript = $('<script>').attr('type', 'text/javascript')
	.html('$(document).ready(function() { $("#twitter-pb").draggable(); }); $("#closerdform").click(function () { $("#twitter-pb").hide(); return true;});');
	$('head').append(rdformscript);
	var contentdiv = $('<div>')
	.attr('id', 'twitter-pb')
	.css({"background-color" : "#f0f0f0", "border" : "1px #0f0f0f solid", "padding" : "10px", "width" : "600px", "heigth" : "400px", "position" : "absolute", "top" : "50px" , "left" : "50px"});
	var  rdform = $('<div>').load("http://localhost:8888/pushback/rdforms/rdform2.html");
	var menu = $('<div>').html('<button id="closerdform" style="padding: 5px">close ...</button>');
	contentdiv.append(menu);
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
