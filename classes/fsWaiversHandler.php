<?php

require_once 'fsWaivers.php';
	
if (isset($_POST) && is_array($_POST)) {
    	$action 	= $_POST["action"];
		$event_id	= $_POST["eventid"];
	
	
	//get all waivers for event
	if($action == "getWaiversAndWildcards"){
		
		$fswaivers = new FSWaivers();
		$return = $fswaivers->getWaivers($event_id);	
		
		echo json_encode($return);
	}
	
	
	
}
	
?>