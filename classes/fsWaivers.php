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
	
	
	private function getRankDistribution($leaguesize){
		
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
		
		$distribution['top5'] = $top5;
		$distribution['next6to10'] = $next6to10;
		$distribution['next11to22'] = $next11to22;
		$distribution['next23to34'] = $next23to34;
		
		//the multipliers below should add to 34
//		$top5total = $top5 *5;
//		$next6to10total = $next6to10 *5;
//		$next11to22total = $next11to22 *12;
//		$next23to34total = $next23to34 *12; //10 qualified through QS + 2 WSL wildcards
		
		return $distribution;
		
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
			//---END GET ALL PICKS
			
			//---GET WILDCARDS
			$sql = "SELECT surfer_id,for_event,type,replacing 
					FROM surfers_record WHERE for_event=$event_id ORDER BY type";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				
				$wildcards[$row['surfer_id']]['type'] = $row['type'];
				$wildcards[$row['surfer_id']]['replacing'] = $row['replacing'];
				$injuries[$row['replacing']] = $row['surfer_id'];
				
			}
			//---END GET WILDCARDS
			
		}
		//end get all picks
		
		$leaguesize = count($users);
		
		//----GET NUMBER OF DRAFT AVAILABLE SURFERS
		 $distribution = $this->getRankDistribution($leaguesize);
		 $next23to34 = $distribution['next23to34'];
		 $next11to22 = $distribution['next11to22'];
		 $next6to10 = $distribution['next6to10'];
		 $top5 = $distribution['top5'];
	    //----END GET NUMBER OF DRAFT AVAILABLE SURFERS
		
		
		//----count number of teams each surfer is in - also keep count of current event wc picks
		foreach($users as $uid=>$usercount){
			if(sizeof($picks[$uid][$event_id])>0){
				//user has picks for this event
				foreach($picks[$uid][$event_id] as $pos=>$pid){
					//go through each pick - add non-wc to $picked - add wcs to $pickedwc
					if($surfers[$pid]['wc'] == 0){$picked[$pid] += 1;$mostcurrentpicks[$uid][] = $pid;}else{$pickedwc[$pid] += 1;$mostcurrentpicks[$uid][] = $pid;}
				}
			}else{
				//no picks for this event - use last event
				foreach($picks[$uid][$lastevent] as $pos=>$pid){
					if($surfers[$pid]['wc'] == 0){$picked[$pid] += 1;$mostcurrentpicks[$uid][] = $pid;}
				}
			}
		}
		//----end count number of picked surfers
		
		
		//-----calculate remaining surfers
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
		//--------end calculate remaining surfers
		
		$waivers['picks'] = $mostcurrentpicks;
		$waivers['surfers'] = $surfers;
		$waivers['wildcards'] = $wildcards;
		$waivers['injuries'] = $injuries;
		
		
		return $waivers;
		
	}
	
	private function calculateWaivers($user_id,$event_id,$league_id,$surfers,$waivers){
		
		$picks = $waivers['picks'];
		$injuries = $waivers['injuries'];
		$wildcards = $waivers['wildcards'];

		foreach($picks[$user_id] as $k=>$pid){
			$display.=" $pid </br>";
			if($injuries[$pid]>0){
				$topwildcards[$pid] = $injuries[$pid];
				$display.=" $pid - HAS REPLACEMENT -- ";
				$display.= $injuries[$pid] ."</br>";
				$userpriority[$injuries[$pid]] = 1; //inserts id of replacing surfer into new array
			}		
		}
		
		foreach($surfers as $sid=>$v){
			if($v['available']>0){
				$availables[$sid] = $v['available'];
				$display.="$sid - " .$v['aka'] ." - " .$v['available'] ."</br>";
			}
		}
		
		foreach($wildcards as $wid=>$v){
			if($v['type']==1){
				$bottomwildcards[1][] = $wid;
				$display.= "$wid - WC </br>";
			}elseif( ($v['type']==2) && $userpriority[$wid]!=1 ){
				$display.= "$wid - IWC - ";
				$display.= $v['replacing'] . "</br>";
				$bottomwildcards[2][$wid] = $v['replacing'];
			}
			
		}
		
		
		$toreturn['topwildcards'] = $topwildcards;
		$toreturn['waivers'] = $availables;
		$toreturn['bottomwildcards'] = $bottomwildcards;
		
		return $toreturn;
		
	}
	
	private function displayWaivers($user_id,$event_id,$league_id,$surfers,$ready){
		
		$topwildcards = $ready['topwildcards'];
		$waivers = $ready['waivers'];
		$bottomwildcards = $ready['bottomwildcards'];
		
		
		//------SHOW INJURY REPLACEMENTS FOR USERS TEAM
		if(sizeof($topwildcards)>0){
			
			$display.="<div class='grid-x'><div class='large-6 medium-9 small-12 cell'><b>Team Injury Replacements</b></div></div>
								<div class='grid-x'>";
			
			foreach($topwildcards as $wid=>$inj){
				
				$display.="<div class='large-4 medium-5 small-8 cell'>
											".$surfers[$wid]['name']." replacing ".$surfers[$inj]['name']."
										</div>
										
									<div class='large-2 medium-4 small-4 cell'>
											<a class='button pickinstantir' id='sid$sid'> Add ".$surfers[$wid]['aka']."</a>
									</div>";
				
			}
			
			$display.= "</div>";
			
		}
		//--------END INJURY REPLACEMENTS FOR USERS TEAM
		
		//------SHOW GENERAL WAIVERS
		if(sizeof($waivers)>0){
			
			$display.="<div class='grid-x'><div class='large-12 medium-9 small-12 cell'><b>Waivers</b></div></div>
								<div class='grid-x'>";
			
			foreach($waivers as $wid=>$v){
				
				$display.="<div class='large-4 medium-5 small-8 cell'>".$surfers[$wid]['name']."</div>
									<div class='large-2 medium-4 small-4 cell'>
											<a class='button requestwv' id='sid$sid'> Request ".$surfers[$wid]['aka']."</a>
									</div>";
				
			}
			
			$display.= "</div>";
			
		}
		//------END GENERAL WAIVERS
		
		//------SHOW WILDCARDS
		if(sizeof($bottomwildcards[1])>0){
			
			$display.="<div class='grid-x'><div class='large-12 medium-9 small-12 cell'><b>Wildcards</b></div></div>
								<div class='grid-x'>";
			
			foreach($bottomwildcards[1] as $k=>$wid){
				
				$display.="<div class='large-4 medium-5 small-8 cell'>".$surfers[$wid]['name']."</div>							
									<div class='large-2 medium-4 small-4 cell'>
											<a class='button requestwc' id='sid$sid'> Request ".$surfers[$wid]['aka']."</a>
									</div>";
			}
			
			
			$display.= "</div>";
		}
		//------END WILDCARDS
		
		//------SHOW OTHER INJURY REPLACEMENTS
		if(sizeof($bottomwildcards[2])>0){
			
			$display.="<div class='grid-x'><div class='large-12 medium-9 small-12 cell'><b>Injury Replacements</b></div></div>
								<div class='grid-x'>";
			
			foreach($bottomwildcards[2] as $wid=>$inj){
				
				$display.="<div class='large-4 medium-5 small-8 cell'>
											".$surfers[$wid]['name']." replacing ".$surfers[$inj]['name']."
										</div>
										
										<div class='large-2 medium-4 small-4 cell'>
												<a class='button requestotherir' id='sid$sid'> Request ".$surfers[$wid]['aka']."</a>
										</div>";
			}
			
			
			$display.= "</div>";
		}
		//------END OTHER INJURY REPLACEMENTS
		
		return $display;
		
	}
	
	public function getWaivers($event_id){
		
		$user_id = 106; //<------------------------------eventually remove and use session id
		$league_id = 1; //<------------------------------CHANGE LEAGUE ID
		
		$fsevent = new FSEvent();
		$eventdata = $fsevent->getEventStatus($event_id); //['status'] ['name'] ['current'] ['rounds'] ['nextheat'] ['score'] ['roundresults']
		$surfers = 	 $fsevent->getSurfers();
		
		$event_status = $eventdata['status'];
		
		if($event_status == 1){
			
			$waivers = $this->getPastLeaguePicks($user_id,$event_id,$league_id,$surfers);
 			
			$surfers = $waivers['surfers'];
			
			$ready = $this->calculateWaivers($user_id,$event_id,$league_id,$surfers,$waivers);
			
			$display = $this->displayWaivers($user_id,$event_id,$league_id,$surfers,$ready);
			
			
		}
		else{
			
			$display.="<div class='grid-x align-center align-middle'><div class='large-6 medium-9 small-12 cell'>
						<h3>What are you doing here?</h3><h4>Take your fish and go to first.</h4></div></div>";
		}
		
		
		return $display;
	}
	
	
	
	
}//end class FSEvent
	
?>