<?php

require_once 'fsTeam.php';
	
if (isset($_POST) && is_array($_POST)) {
    $action 	= $_POST["action"];
	$event_id	= $_POST["eventid"];
	$user_id 	= $_POST["userid"];
	
	if($action == "getUserTeam"){
		$fsteam = new FSTeam();
		$return = $fsteam->getTeam($event_id,$user_id);

		//echo json_encode($return);
		//print_r($return['main']);
		
		echo $return;
	}
	
	
	
}
	
?>