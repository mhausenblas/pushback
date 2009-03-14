
/**
 * @author : Aftab Iqbal
 * @institute: Digital Enterprise Research Institute, Galway, Ireland
 */



How to configure
================


Open the rdform-bookmarklet.html (located in rdforms directory) in your favorite editor and edit the s.src property before dragging it to the bookmarklet bar.

e.g.
s.src='http://localhost:8081/pushback/rdformscontainer/includeJS.js';

where pushback contains all the directories (fusion, pbcontroler, rdforms, rdfwrapper, usecases, rdformscontainer).



includeJS.js
------------

Change the 'baseUri' variable to the directory of pushback on ur server/machine.

e.g. 
var baseUri = "http://localhost:8081/pushback/";

where pushback contains all the directories (fusion, pbcontroler, rdforms, rdfwrapper, usecases, rdformscontainer).


rdform-container.js
-----------------

1. Change the 'loadForm' variable to load the RDform.


2. Change the 'baseUri' variable to the directory of pushback on ur server/machine.

   e.g. 
   var baseUri = "http://localhost:8081/pushback/";

   where pushback contains all the directories (fusion, pbcontroler, rdforms, rdfwrapper, usecases, rdformscontainer).



How to RUN
==========

After making above stated changes. Simply drag the bookmarklet to your browser and click on it.
