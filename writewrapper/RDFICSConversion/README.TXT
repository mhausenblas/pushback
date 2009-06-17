INTRO:
fromIcal.py converts an ICS file to RDF
toIcal.py converts an RDF file to ICS 

WHAT YOU NEED TO RUN THEM:

To test fromIcal.py you need you need Python (I tested with 2.4)
and two modules: 
XMLWriter.py and icslex.py (which you can find them in this folder)
You can either copy them in the same folder with fromIcal.py or copy
them to python/installation/foder/Lib/site-packages

TO test the toIcal.py you need Python (I tested with 2.4)
and the swap package, which you can find it in here:
http://www.w3.org/2000/10/swap/doc/cwm
Simply download cwm, extract it and copy the swap folder to 
python/installation/foder/Lib/site-packages

Note: there is a bug in toIcal.py --> it works when the RDF
file is on a server (e.g. if you use apache, copy it in htdocs and
call it with http://localhost/cal.rdf (i guess it is a problem with
paths, please contact the author of toIcal.py if you want)
