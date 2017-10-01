<?php

//NEXT STEP ADD PICK HEADERS
//GET AVILABLES FOR THAT SPECIFIC EVENT (& WILDCARDS) AND FACTOR INTO BEST POSSIBLE SCORE
	
class FSTeam{
	
	public function __construct(){
		
		session_start();
		//include_once(fsbasics.php);
		require_once("../config/db.php");
		
		include "fsEvent.php";
	}
	
	
	private function getAvailableScorers($user_id,$event_id,$league_id,$scores){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}

		if (!$this->db_connection->connect_errno) {

			//---GET ALL SURFERS IN ROUND 1 AKA ALL SURFERS IN EVENT
			$sql = "SELECT surfer_id FROM heats WHERE event_id=$event_id AND round=1 ORDER BY surfer_id";

			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$allsurfers[$row['surfer_id']] = 0;
			}
			//---END GET ROUND 1
			
			//---ADD ALL TEAM PICJS INTO ALL SURFERS ARRAY
			$sql = "SELECT pick_id FROM league_picks WHERE league_id=$league_id AND event=$event_id";

			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$thissurfer = $row['pick_id'];
				if(isset($allsurfers[$thissurfer])){$allsurfers[$thissurfer]++;}
				
			}
			//---END ADD UP SURFERS
			
		}
		
		
		foreach($allsurfers as $sid=>$count){
			//AVAILABILITY STANDARDS SET BELOW

			if($sid<=1005 && $count<2){
				$availables[$sid] = $scores[$sid]['sco'];
				
			}
			
			if($sid>1015 && $count<4){
				$availables[$sid] = $scores[$sid]['sco'];
				
			}
			
			if(($sid>1005 && $sid<1015) && $count<3){
				$availables[$sid] = $scores[$sid]['sco'];
				
			}
			
			
		}
		
		arsort($availables);
		return $availables;
		
	}
	
	private function getPicksByUser($user_id,$event_id,$league_id){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}

		if (!$this->db_connection->connect_errno) {

			//---GET ROUND
			$sql = "SELECT p.user_id,p.pick_id,p.status,p.active,p.wc,u.user_name,u.user_team 
					FROM league_picks p
					LEFT JOIN users AS u
					ON p.user_id = u.id
					WHERE p.event=$event_id AND p.league_id=$league_id AND u.id =$user_id
					ORDER BY p.active";

			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$picks[$row['user_id']][$row['active']] = $row['pick_id'];
				
				$users[$row['user_id']]['name'] = $row['user_name'];
				$users[$row['user_id']]['short'] = explode(" ",$row['user_name'])[0];
				$users[$row['user_id']]['team'] = $row['user_team'];
			}
			//---END GET ROUND
		}
		
		$toreturn['picks'] = $picks;
		$toreturn['users'] = $users;
		
		return $toreturn;
		
	}
	
	private function getScoresByEvent($event_id){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}

		if (!$this->db_connection->connect_errno) {

			//---GET ROUND
			$sql = "SELECT surfer_id,position,points
					FROM surfer_scores
					WHERE event=$event_id";

			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$scores[$row['surfer_id']]['sco'] = $row['points'];
				$scores[$row['surfer_id']]['pos'] = $row['position'];
			}
			//---END GET ROUND
		}
		
		return $scores;
		
	}
	
	private function calculateTeam($user_id,$eventdata,$surfers,$pickdata,$scores,$availscores){
		
		$userpicks = $pickdata['picks'];
		
		//create arrays with starting, main and out to organize by score
		foreach($userpicks as $uid=>$v){
			foreach($v as $k=>$sid){
				$allpicks[$sid] = $scores[$sid]['sco'];
				if($k<=7){$startingpicks[$sid] = $scores[$sid]['sco'];$startingscore += $scores[$sid]['sco'];}
				elseif($k>7 && $k<99){$benchpicks[$sid] = $scores[$sid]['sco'];}
				elseif($k>=100){$outpicks[$sid] = $outpicks[$sid]['sco'];}
			}
		}
		
		//sort arrays by highest score
		arsort($allpicks);
		arsort($startingpicks);
		arsort($benchpicks);
		arsort($outpicks);
		
		//calculate highest possible score
		$i = 0;
		foreach($allpicks as $sid=>$sco){
			if($i<8){ //stops when the 7 highest scores have been registered
				$bestscore += $sco;
				$topscorer[$sid] = 1;
				$i++;
			}			
		}
		
		$i = 0;
		foreach($allpicks as $sid=>$sco){
			if($i<8){//stop when a top 8 has been registered
						
				foreach($availscores as $sid2=>$sco2){
					//go through each available score and compare to the current team member score

					if($sco2>$sco){
						//available surfer score is higher than current team meber score, add to array

						$availscorer[$sid2] = $sco2;
						$availtotal+=$sco2;
						unset($availscores[$sid2]);//deletes this top avail score since its already been registered

						$i++;
						
					}else{
						
						//no scores beat this team member score, register team member score and move on to next member
						$availscorer[$sid] = $sco;
						$availtotal+=$sco;
						$i++;
						break;
						
					}
				}
				
			}
			
			
		}
		
		
		//display lineups
		$toreturn.= "<div class='grid-x align-center teamheader'>
						
						<div class='small-12 cell teamname'>".$pickdata['users'][$user_id]['team']."</div>
						<div class='small-12 cell teamuser'>".$pickdata['users'][$user_id]['name']."</div>
						
					</div>";
		
		
		foreach($startingpicks as $sid=>$sco){
			
			$pos = $scores[$sid]['pos'];
			
			if($topscorer[$sid]==1 && isset($availscorer[$sid])){
				$toreturn.= "<div class='grid-x align-center startingsurfer bestscorer bestavailscorer pos$pos is-$sid'>";
			}
			else if($topscorer[$sid]==1){$toreturn.= "<div class='grid-x align-center startingsurfer bestscorer pos$pos is-$sid'>";}
			else{$toreturn.= "<div class='grid-x align-center startingsurfer pos$pos is-$sid'>";}
			
			$toreturn .= "
					
					<div class='large-3 medium-5 cell hide-for-small-only teamsurfername'>".$surfers[$sid]['name']."</div>
					<div class='small-2 cell show-for-small-only teamsurfername'>".$surfers[$sid]['aka']."</div>
					
					<div class='large-2 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
					<div class='large-2 medium-2 small-2 cell teamsurferscore'>".$scores[$sid]['sco']."</div>
					
				</div>
			";
			
		}
		
		$toreturn.= "<div class='grid-x align-center startingscore'><div class='small-12 cell'>$startingscore</div></div>";
		
		foreach($benchpicks as $sid=>$sco){
			$pos = $scores[$sid]['pos'];
			if($topscorer[$sid]==1 && isset($availscorer[$sid])){$toreturn.= "<div class='grid-x align-center benchedsurfer bestscorer bestavailscorer pos$pos is-$sid'>";}
			else if($topscorer[$sid]==1){$toreturn.= "<div class='grid-x align-center benchedsurfer bestscorer pos$pos is-$sid'>";	}
			else{$toreturn.= "<div class='grid-x align-center benchedsurfer pos$pos is-$sid'>";}
			
			$toreturn .= "
					
					<div class='large-3 medium-5 cell hide-for-small-only teamsurfername'>".$surfers[$sid]['name']."</div>
					<div class='small-2 cell show-for-small-only teamsurfername'>".$surfers[$sid]['aka']."</div>
					
					<div class='large-2 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
					<div class='large-2 medium-2 small-2 cell teamsurferscore'>".$scores[$sid]['sco']."</div>
					
				</div>
			";
		}
		
		foreach($outpicks as $sid=>$sco){
			$pos = $scores[$sid]['pos'];
			
			if($topscorer[$sid]==1){$toreturn.= "<div class='grid-x align-center outsurfer bestscorer pos$pos is-$sid'>";	}
			else{$toreturn.= "<div class='grid-x align-center outsurfer pos$pos is-$sid'>";}
			
			$toreturn .= "
					
					<div class='large-3 medium-5 cell hide-for-small-only teamsurfername'>".$surfers[$sid]['name']."</div>
					<div class='small-2 cell show-for-small-only teamsurfername'>".$surfers[$sid]['aka']."</div>
					
					<div class='large-2 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
					<div class='large-2 medium-2 small-2 cell teamsurferscore'>".$scores[$sid]['sco']."</div>
					
				</div>
			";
		}
		
		$toreturn.= "<div class='grid-x align-center bestscore'><div class='small-12 cell'>$bestscore</div></div>";
		
		foreach($availscores as $sid=>$sco){
			
				$pos = $scores[$sid]['pos'];
				
				if(isset($availscorer[$sid])){$toreturn.= "<div class='grid-x align-center availsurfer bestavailscorer pos$pos is-$sid'>";}
				else{$toreturn.= "<div class='grid-x align-center availsurfer pos$pos is-$sid'>";}
			
				$toreturn .= "
					
						<div class='large-3 medium-5 cell hide-for-small-only availsurfername'>".$surfers[$sid]['name']."</div>
						<div class='small-2 cell show-for-small-only availsurfername'>".$surfers[$sid]['aka']."</div>
					
						<div class='large-2 medium-2 small-2 cell availsurferpos'>$pos</div>
					
						<div class='large-2 medium-2 small-2 cell availsurferscore'>".$scores[$sid]['sco']."</div>
					
					</div>
				";
			
		}
		print_r($availscorer);
		$toreturn.= "<div class='grid-x align-center bestavailscore'><div class='small-12 cell'>$availtotal</div></div>";
		
		return $toreturn;
	}
	
	public function getTeam($event_id,$user_id){
		
		$league_id = 1; //<------------------------------CHANGE LEAGUE ID
		
		$fsevent = new FSEvent();
		$eventdata = $fsevent->getEventStatus($event_id);
		$surfers = 	 $fsevent->getSurfers();
		
		$event_name = 	$eventdata['name'];
		$event_status = $eventdata['status'];
		$rounds = 		$eventdata['rounds'];
		
		if($event_status == 4){
			
			//event is over
			$pickdata = $this->getPicksByUser($user_id,$event_id,$league_id);
			$scores = $this->getScoresByEvent($event_id);
			
			$availscores = $this->getAvailableScorers($user_id,$event_id,$league_id,$scores);
			
			$display = $this->calculateTeam($user_id,$eventdata,$surfers,$pickdata,$scores,$availscores);
			
			
			return $display;
			
		}
		
		
		
		//return "Data: $toreturn";
		
		//find event status
		//display accordingly
		//if past -> display results & analysis
		//if current -> display results & analysis
		//if future -> display lineup
		
		//analysis = full team stats, surfers per round, most successful combo, points
		
	}
	
	
}//end class FSEvent
	
?>