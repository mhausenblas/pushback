Note: This demo has jquery and rdfquery dependencies.
If you want to run it check the CalendarRDForm.html<head></head> section.
It should be possible to download the dependencies from the jquery site: http://jquery.com/

This is the readme file from the Google calendar demo.
In order to use it, you must have a Google account and some events in the Google calendar.

1) Export your Google calendar to an ics file.
2) Open the ICS file with a text editor
3) Write down the ID of the event you want to modify
	You will find a line like this: UID:91frcv11a84sbm2t80bj59kkgg@google.com
	The event ID should be 91frcv11a84sbm2t80bj59kkgg
   Note: an event is located between the BEGIN:VEVENT line and the END:VEVENT line

4) Run CalendarRDForm.html (sample inputs are provided in the text boxes)
5) Write the ID of your event in the ID text input box
6) Change start time, end time, title, location, whatever suits you
7) Click "Submit RDForm to RDF wrapper". You should be prompted to log in to Google. 

If everything goes right you should see:

Old title: Your old title
New title: Your new title
.......................
.......................

If it doesn't work contact me at oana.ureche@deri.org.
