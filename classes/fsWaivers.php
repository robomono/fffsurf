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
	
	
	private function getPastLeaguePicks($event_id,$league_id,$surfers){
		
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
					FROM league_picks WHERE league_id=$league_id AND event=$lastevent AND wc=0";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				
				$users[$row['user_id']] = 1;	//create array with one entry per user for league count
				$picked[$row['pick_id']] += 1;	//count number of teams in which each pick is a member of

				
			}
		}
		//end get all picks
		
		$leaguesize = 9;
		
		if($leaguesize==2)						{ $top5 = 1; $next6to10 = 1; $next11to22 = 1; $next23to34 = 1; }
		elseif($leaguesize==3)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 1; $next23to34 = 2; }
		elseif($leaguesize==4)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 1; $next23to34 = 2; }
		elseif($leaguesize==5)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 2; }
		elseif($leaguesize==6)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 3; }
		elseif($leaguesize==7)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 3; }
		elseif($leaguesize==8)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 4; }
		elseif($leaguesize==9)					{ $top5 = 1; $next6to10 = 1; $next11to22 = 2; $next23to34 = 4; }
		elseif($leaguesize==10)					{ $top5 = 1; $next6to10 = 2; $next11to22 = 3; $next23to34 = 4; }
		
		//the multipliers below should add to 34
		$top5total = $top5 *5;
		$next6to10total = $next6to10 *5;
		$next11to22total = $next11to22 *12;
		$next23to34total = $next23to34 *12; //10 qualified through QS + 2 WSL wildcards
		
		
		$totalneeded = $leaguesize * 8;
		
		$display.="<div class='grid-x align-center align-middle' style='font-size:15pt'>
					<div class='large-6 medium-6 small-8 cell'>";
		
		
		$display.= "League Size: $leaguesize </br> Total Needed: $totalneeded </br></br>";
		
		$display.= "R1 - R5 --- $top5 surfer</br> 
					Top 5 Total: $top5total </br> 
					Remaining: " .($totalneeded - $top5total) ."</br></br>";
		
		$display.= "R6 - R10 --- $next6to10 surfer</br>
					Next 10 Total: $next6to10total </br> 
					Remaining: " .($totalneeded - $top5total - $next6to10total) ."</br></br>";
		
		$display.= "R11 - R22 --- $next11to22 surfer</br>
					Next 10 Total: $next11to22total  </br>
					Remaining: " .($totalneeded - $top5total - $next10total - $next11to22total) ."</br></br>";
					
		$display.= "R23 - R34 --- $next23to34 surfer</br>
					Next 10 Total: $next23to34total  </br>
					Remaining: " .($totalneeded - $top5total - $next10total - $next11to22total - $next23to34total) ."</br></br>";
		
		
		foreach($surfers as $sid=>$v){
			$display.="$sid - ".$surfers[$sid]['name'] ." - " .$surfers[$sid]['wc'] ."</br>";
			if($sid<=1005){
				$surfers[$sid]['available'] = $top5;
				$surfers[$sid]['available'] = $surfers[$sid]['available'] - $surfercount[$sid];
			}elseif($sid>1015){
				$surfers[$sid]['available'] = $lowerhalf;
				$surfers[$sid]['available'] = $surfers[$sid]['available'] - $surfercount[$sid];
			}elseif($sid<=1015 && $sid>1005){
				$surfers[$sid]['available'] = $next10;
				$surfers[$sid]['available'] = $surfers[$sid]['available'] - $surfercount[$sid];
			}
		}
		
		
		
		$display.="</div></div>";
		
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
			
			$waivers = $this->getPastLeaguePicks($event_id,$league_id,$surfers);
 			
			$display = $waivers;
			
		}
		else{
			
			$display.="<div class='grid-x align-center align-middle'><div class='large-6 medium-9 small-12 cell'>
						<h3>What are you doing here?</h3><h4>Take your fish and go to first.</h4></div></div>";
		}
		
		
		return $display;
	}
	
	
	
	
}//end class FSEvent
	
?>