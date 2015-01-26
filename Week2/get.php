<div id="calendar">
<?php
    $host = '';
    $dbname = ''; // your username
    $user = ''; // your username
    $pass = ''; // your database password
    
    try {
            $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $DBH->query("SET NAMES 'UTF8';");
        
        } catch(PDOException $e) {
            echo "Could not connect to database.";
            file_put_contents('log.txt', $e->getMessage(), FILE_APPEND);
        }

try {
    $eventList = array();
    $sql = "SELECT * FROM calendar ORDER BY eDate ASC";
    $STH = $DBH->query($sql);
    $STH->setFetchMode(PDO::FETCH_OBJ);
    while ($row = $STH->fetch()){
		// create standard object which complies with this: http://fullcalendar.io/docs/event_data/Event_Object/
		$event = new stdClass();
    	$event->date = $row->eDate;
		// TODO: you also need
		// title
        $event->title = $row->eName;

		// description
        $event->description = $row->eDescription;

		// email
        $event->email = $row->pEmail; 

		// phone
        $event->phone = $row->pPhone;

		$eventList[] = $event;
    }
	$eventsJSON = json_encode($eventList);
 } catch (PDOException $e) {
	echo 'Something went wrong';
	file_put_contents('log.txt', $e->getMessage()."\n\r", FILE_APPEND); // remember to set the permissions so that log.txt can be created
}      

?>
</div>
<script>
// JSON made in PHP is saved in JavaScript
var jsonEvents = <?php echo $eventsJSON; ?>;
console.log(jsonEvents);
// TODO: use jQuery FullCalendar plugin to display the events in a proper calendar
// calendar has to have at least month, week and day views
$(document).ready(function() {

    // page is now ready, initialize the calendar...

    $('#calendar').fullCalendar({
		events: jsonEvents,
		header: {
		left:   'today',
		center: 'title',
		right:  'month,agendaWeek,agendaDay, prev,next'
		},
		// timeformat is 24-hour clock
		titleFormat: 'YYYY, MM, DD',
		// when an event is clicked all the event details (jsonEvents) are displayed
		eventClick: function(event) {

        alert('Event: ' + event.title
		+ '\n' + event.date
		+ '\n' + event.description
		+ '\n' + event.email
		+ '\n' + event.phone);
        // change the border colour of clicked events
			$(this).css('border-color', 'red');
		}
});	

});
	
</script>
