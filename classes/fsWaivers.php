<?php

//NEXT STEP ADD PICK HEADERS
//GET AVILABLES FOR THAT SPECIFIC EVENT (& WILDCARDS) AND FACTOR INTO BEST POSSIBLE SCORE


class FSWaivers{
	
	public function __construct(){
		
		session_start();
		//include_once(fsbasics.php);
		require_once("../config/db.php");
		
		include "fsEvent.php";
	}
	
	
	private function getPastLeaguePicks($user_id,$event_id,$league_id,$surfers){
		
		$lastevent = $event_id - 1;
		
		//get all picks
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}
		
		//get all picks for thie event and events before
		if (!$this->db_connection->connect_errno) {

			//---GET ALL PICKS PER USER IN EVENT
			$sql = "SELECT user_id,event,pick_id,active 
					FROM league_picks WHERE league_id=$league_id AND (event=$lastevent OR event=$event_id) ORDER BY active";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$picks[$row['user_id']][$row['event']][$row['active']] = $row['pick_id'];
				$users[$row['user_id']] = 1;	//create array with one entry per user for league count
			}
		}
		//end get all picks
		
		$leaguesize = count($users);
		
		if($leaguesize==2)							{ $top5 = 1; $next6to10 = 1; $next11to22 = 1; $next23to34 = 1; }
		elseif($leaguesize==3)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 1; $next23to34 = 1; }
		elseif($leaguesize==4)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 1; $next23to34 = 2; }
		elseif($leaguesize==5)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 2; }
		elseif($leaguesize==6)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 2; }
		elseif($leaguesize==7)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 3; }
		elseif($leaguesize==8)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 4; }
		elseif($leaguesize==9)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 4; }
		elseif($leaguesize==10)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 3; $next23to34 = 4; }
		elseif($leaguesize==11)					{ $top5 = 1; $next6to10 = 2; $next11to22 = 4; $next23to34 = 4; }
		elseif($leaguesize==12)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 4; $next23to34 = 4; }
		elseif($leaguesize==13)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 4; $next23to34 = 5; }
		elseif($leaguesize==14)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 4; $next23to34 = 5; }
		elseif($leaguesize==15)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 5; $next23to34 = 5; }
		elseif($leaguesize==16)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 5; $next23to34 = 6; }
		elseif($leaguesize==17)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 5; $next23to34 = 6; }
		elseif($leaguesize==18)					{ $top5 = 2; $next6to10 = 2; $next11to22 = 5; $next23to34 = 7; }
		elseif($leaguesize==19)					{ $top5 = 2; $next6to10 = 3; $next11to22 = 6; $next23to34 = 7; }
		elseif($leaguesize==20)					{ $top5 = 3; $next6to10 = 3; $next11to22 = 6; $next23to34 = 7; }
		
		//the multipliers below should add to 34
//		$top5total = $top5 *5;
//		$next6to10total = $next6to10 *5;
//		$next11to22total = $next11to22 *12;
//		$next23to34total = $next23to34 *12; //10 qualified through QS + 2 WSL wildcards
		
		//calculate remaining surfers
		foreach($surfers as $sid=>$v){
			if($v['wc']==0){
				
				if($sid>1000 && $sid<=1005){
					$surfers[$sid]['available'] = $top5;
					$surfers[$sid]['available'] = $surfers[$sid]['available'] - $picked[$sid];
				}elseif($sid>1005 && $sid<=1010){
					$surfers[$sid]['available'] = $next6to10;
					$surfers[$sid]['available'] = $surfers[$sid]['available'] - $picked[$sid];
				}elseif($sid>1010 && $sid<=1022){
					$surfers[$sid]['available'] = $next11to22;
					$surfers[$sid]['available'] = $surfers[$sid]['available'] - $picked[$sid];
				}elseif($sid>1022){
					$surfers[$sid]['available'] = $next23to34;
					$surfers[$sid]['available'] = $surfers[$sid]['available'] - $picked[$sid];
				}
				
			}
		}
		
		
		foreach($users as $uid=>$v){
			
			$display.= "$uid </br>";
			$display.= "---- " .sizeof($picks[$uid][$lastevent]) ."</br>";
			$display.= "---- " .sizeof($picks[$uid][$event_id]) ."</br>";
			
		}
		
		
		return $display;
		
	}
	
	public function getWaivers($event_id){
		
		$user_id = 108; //<------------------------------eventually remove and use session id
		$league_id = 1; //<------------------------------CHANGE LEAGUE ID
		
		$fsevent = new FSEvent();
		$eventdata = $fsevent->getEventStatus($event_id); //['status'] ['name'] ['current'] ['rounds'] ['nextheat'] ['score'] ['roundresults']
		$surfers = 	 $fsevent->getSurfers();
		
		$event_status = $eventdata['status'];
		
		if($event_status == 1){
			
			$surfers = $this->getPastLeaguePicks($user_id,$event_id,$league_id,$surfers);
 			
			
			
			$display = $surfers;
			
		}
		else{
			
			$display.="<div class='grid-x align-center align-middle'><div class='large-6 medium-9 small-12 cell'>
						<h3>What are you doing here?</h3><h4>Take your fish and go to first.</h4></div></div>";
		}
		
		
		return $display;
	}
	
	
	
	
}//end class FSEvent
	
?>