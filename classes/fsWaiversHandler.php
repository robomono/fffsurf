<?php

require_once 'fsTeam.php';
	
if (isset($_POST) && is_array($_POST)) {
    $action 	= $_POST["action"];
		$event_id	= $_POST["eventid"];
		$user_id 	= $_POST["userid"];
	
	if($action == "getUserTeam"){
		$fsteam = new FSTeam();
		$return = $fsteam->getTeam($event_id,$user_id);

		echo json_encode($return);
		//print_r($return['test']);
		
		//echo $return;
	}
	
	
	//update team lineup after user presses on "Save" lineup button
	if($action == "updateTeamChanges"){
		
		//comma separated list of ids in the order they are displayed (order in which user set them up)
		$allids 	= $_POST["allids"];
		
		$fsteam = new FSTeam();
		$return = $fsteam->updateTeam($event_id,$allids);

		//echo json_encode($return);
		//print_r($return['test']);
		
		echo json_encode($return);
	}
	
	
	
}
	
?>