This demo uses the ARC library.
To run it you need thr ARC library, which you can find here: http://arc.semsol.org/download
Just download it and put the library files in an "arc" folder.

index.php will create a SPARQL endpoint - you don't really need it - it's just for user interface

1) Export your Google calendar to an ics file.
2) Open the ICS file with a text editor
3) Write down the ID of the event you want to modify
	You will find a line like this: UID:91frcv11a84sbm2t80bj59kkgg@google.com
	The event ID should be 91frcv11a84sbm2t80bj59kkgg
   Note: an event is located between the BEGIN:VEVENT line and the END:VEVENT line

4) Run CalendarRDForm.html (sample inputs are provided in the text boxes)
5) Write the ID of your event in the ID text input box
6) Change start time, end time, title, location, whatever suits you
7) Click update (the form will call updateStore.php)
   updateStore.php inserts triples into the triple store 
   Note: in this step only the triple store is updated

8) Run updateCalendar.php (sure, under Apache) -> this will actually update the Google calendar and
   it will prompt you to log into Google 
   it checks for new data in the triple store and updates the Google calendar
   A cron job should be set up for this updateCalendar.php -> run every 5 minutes

If everything goes right you should see:

Old title: Your old title
New title: Your new title
.......................
.......................

If it doesn't work contact me at oana.ureche@deri.org.