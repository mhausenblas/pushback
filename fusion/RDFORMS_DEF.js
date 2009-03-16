/*
<#this>	dcterms:creator [
			owl:sameAs <http://sw-app.org/mic.xhtml#i> ;
			foaf:name "Michael Hausenblas" ;
			foaf:mbox <mailto:michael.hausenblas@deri.org> .
		] ;
		dcterms:isPartOf <http://ld2sd.deri.org/pushback/doap#pushback"> ;
		rdfs:seeAlso <http://esw.w3.org/topic/PushBackDataToLegacySourcesRDForms>.
*/

////////////////////////////////////////////////////////////////////////////////////////////////
//
//	This JS file defines the rendering and the default datatypes for the RDForm fields
//  rdfs:seeAlso <http://esw.w3.org/topic/PushBackDataToLegacySourcesRDForms>
//
// Note: In the templates, stuff that starts with '$', such as $ID, is assumed to be a variable 
//       and will be replaced by the runtime parameters (of provided, otherwise defaults).

/* RDForms field type and datatypes */
var HTML_FIELD_TYPES = new Array();
var HTML_FIELD_XSD = new Array();

var HTML_INPUT_FIELD = "input";
var HTML_TYPE_TEXT = "text";

var HTML_TEXT_AREA = "textarea";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//	<input type="text" id="$ID" property="rdf:value" content="$VALUE" value="$VALUE" size="$SIZE" maxlength="$MAXLENGTH"/>
//
/* the HTML rendering template */
HTML_FIELD_TYPES[HTML_INPUT_FIELD+HTML_TYPE_TEXT] = "<input type=\"text\" id=\"ID\" property=\"rdf:value\" content=\"VALUE\" value=\"VALUE\" size=\"SIZE\" maxlength=\"MAXLENGTH\" />";
/* the default XML Schema datatype */
HTML_FIELD_XSD[HTML_INPUT_FIELD+HTML_TYPE_TEXT] = "xsd:string";


////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//	<textarea id="$ID" property="rdf:value" cols="$COLS" rows="$ROWS" />$VALUE</textarea>
//
/* the HTML rendering template */
HTML_FIELD_TYPES[HTML_TEXT_AREA] = "<textarea id=\"ID\" property=\"rdf:value\" cols=\"COLS\" rows=\"ROWS\">VALUE</textarea>";
/* the default XML Schema datatype */
HTML_FIELD_XSD[HTML_TEXT_AREA] = "xsd:string";

//checkbox
//radio
//file
//select