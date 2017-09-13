<?php

require_once 'fsEvent.php';
	
if (isset($_POST) && is_array($_POST)) {
    $action = $_POST["action"];
	$event_id = $_POST["eventid"];
	
	if($action == "getEventRounds"){
		$fsevent = new FSEvent();
		$return = $fsevent->getAllRounds($event_id);

		echo json_encode($return);
	}
	
}
	
?>