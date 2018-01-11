<?php

require_once 'fsStandings.php';
	
if (isset($_POST) && is_array($_POST)) {
   	 	$action 	= $_POST["action"];
		$event_id	= $_POST["eventid"];
	
	if($action == "getEventStandings"){
		$fsstandings = new FSStandings();
		$return = $fsstandings->getStandings($event_id);

		echo json_encode($return);
		
	}
	
	
	
}
	
?>