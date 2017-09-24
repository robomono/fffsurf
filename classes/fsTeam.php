<?php

//NEXT STEP ADD PICK HEADERS
	
class FSTeam{
	
	public function __construct(){
		
		session_start();
		//include_once(fsbasics.php);
		require_once("../config/db.php");
		
	}
	
	public function getTeam($event_id,$user_id){
			
		return "Event: $event_id, User: $user_id";
		
		//find event status
		//display accordingly
		//if past -> display results & analysis
		//if current -> display results & analysis
		//if future -> display lineup
		
		//analysis = full team stats, surfers per round, most successful combo, points
		
	}
	
	
}//end class FSEvent
	
?>