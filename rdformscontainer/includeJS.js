/*
 Creating RDForms based on HTML forms
 Author: Aftab Iqbal [firstname.lastname@deri.org]
 Digital Enterprise Research Institute, Galway, Ireland(www.deri.ie)
 licensed under GPL v2
*/


WRAPPER = new Object();

WRAPPER.createRDForm = function() {

	var baseUri = "http://localhost:8081/pushback/";
	
	// include css
	includeCSS("http://jqueryui.com/themes/base/ui.all.css");
	includeCSS("http://jqueryui.com/demos/demos.css");	
	
	// include js
	includeJS("http://jqueryui.com/jquery-1.3.2.js");
	includeJS("http://jqueryui.com/ui/ui.core.js");
	includeJS("http://jqueryui.com/ui/ui.draggable.js");
	includeJS("http://jqueryui.com/ui/ui.resizable.js");
	includeJS(baseUri + "rdformscontainer/ui.dialog.js");
	includeJS("http://jqueryui.com/external/bgiframe/jquery.bgiframe.js");
	includeJS(baseUri + "rdformscontainer/rdform-container.js");
	
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
