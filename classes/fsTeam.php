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
			$sql = "SELECT p.user_id,p.pick_id,p.status,p.active,p.wc,u.name,u.team,u.short
					FROM league_picks p
					LEFT JOIN league_control AS u
					ON p.user_id = u.user_id
					WHERE p.event=$event_id AND p.league_id=$league_id AND u.user_id = $user_id
					ORDER BY p.active";

			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$picks[$row['user_id']][$row['active']] = $row['pick_id'];
				
				$users[$row['user_id']]['name'] = $row['name'];
				$users[$row['user_id']]['shortname'] = explode(" ",$row['name'])[0];	//what user wants to set as name
				$users[$row['user_id']]['short'] = $row['short'];						//what user wants displayed on limited space
				$users[$row['user_id']]['team'] = $row['team'];
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
					
					<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
					<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
					<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
				</div>
			";
			
		}
		
		$toreturn.= "<div class='grid-x align-center startingscore'>
									<div class='large-5 medium-7 small-8 cell scoretitle'>Total</div>
									<div class='large-1 small-4 medium-2 cell scorenumber'>".number_format($startingscore)."</div>
								</div>";
		
		foreach($benchpicks as $sid=>$sco){
			$pos = $scores[$sid]['pos'];
			if($topscorer[$sid]==1 && isset($availscorer[$sid])){$toreturn.= "<div class='grid-x align-center benchedsurfer bestscorer bestavailscorer pos$pos is-$sid'>";}
			else if($topscorer[$sid]==1){$toreturn.= "<div class='grid-x align-center benchedsurfer bestscorer pos$pos is-$sid'>";	}
			else{$toreturn.= "<div class='grid-x align-center benchedsurfer pos$pos is-$sid'>";}
			
			$toreturn .= "
					
					<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
					<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
					<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
				</div>
			";
		}
		
		foreach($outpicks as $sid=>$sco){
			$pos = $scores[$sid]['pos'];
			
			if($topscorer[$sid]==1){$toreturn.= "<div class='grid-x align-center outsurfer bestscorer pos$pos is-$sid'>";	}
			else{$toreturn.= "<div class='grid-x align-center outsurfer pos$pos is-$sid'>";}
			
			$toreturn .= "
					
					<div class='large-1 medium-2 small-2 cell teamsurferpos'> -- </div>
					
					<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
					<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
				</div>
			";
		}
		
		$toreturn.= "<div class='grid-x align-center bestscore'>
									<div class='large-1 medium-2 small-2 cell highlightscores highlight-team-best'> <i class='material-icons'>remove_red_eye</i> </div>
									<div class='large-4 medium-5 small-6 cell scoretitle'>Team Best</div>
									<div class='large-1 medium-2 small-4 cell scorenumber'>".number_format($bestscore)."</div>
								</div>
								
								<div class='grid-x align-center availablestitle'>
									<div class='large-5 medium-7 small-5 cell'>Available Surfers</div>
								</div>
								
								";
								
		
		foreach($availscores as $sid=>$sco){
			
				$pos = $scores[$sid]['pos'];
				
				if(isset($availscorer[$sid])){$toreturn.= "<div class='grid-x align-center availsurfer bestavailscorer pos$pos is-$sid'>";}
				else{$toreturn.= "<div class='grid-x align-center availsurfer pos$pos is-$sid'>";}
			
				$toreturn .= "
					
						<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
						<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
						<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
					</div>
				";
			
		}
		//print_r($availscorer);
		$toreturn.= "<div class='grid-x align-center bestavailscore'>
			<div class='large-1 medium-2 small-2 cell highlightscores highlight-best-available'> <i class='material-icons'>remove_red_eye</i> </div>
			<div class='large-4 medium-5 small-6 cell scoretitle'> Avail Best</div>
			<div class='large-1 medium-2 small-4 cell scorenumber'>".number_format($availtotal)."</div>
		</div>";
		
		return $toreturn;
	}
	
	private function calculateLiveTeam($user_id,$eventdata,$surfers,$pickdata,$scores){
		
		$userpicks = $pickdata['picks']; //all of user picks
		$scores = $eventdata['score'];	//scores per sid
		
		$liveround = explode(".",$eventdata['current'])[0];//current round being surfed
		$liveheat = explode(".",$eventdata['current'])[1];//current heat being surfed
		
		$nextheat = $eventdata['nextheat'];//last (or next) registered round and heat for surfer
		
		$roundresult = $eventdata['roundresults'];
		
		//sort picks according to score, wins, unsurfed
		
		foreach($userpicks[$user_id] as $k=>$sid){
			
			if($scores[$sid]['rnk']>0){//surfer lost
				
				if($k<=7){
					$startlost[$sid] = $scores[$sid]['pts'];
					$startingscore += $scores[$sid]['pts'];
				}elseif($k>7 && $k<99){
					$benchlost[$sid] = $scores[$sid]['pts'];
					$benchscore += $scores[$sid]['pts'];
				}elseif($k>=100){
					$outlost[$sid] = $scores[$sid]['pts'];
					$outscore += $scores[$sid]['pts'];
				}
				
			}elseif(isset($roundresult[$sid][$liveround])){
				
				if($roundresult[$sid][$liveround]>0){
					
					if($k<=7){
						$startwon[$sid] = $roundresult[$sid][$liveround];
					}elseif($k>7 && $k<99){
						$benchwon[$sid] = $roundresult[$sid][$liveround];
					}elseif($k>=100){
						$outwon[$sid] = $roundresult[$sid][$liveround];
					}					
					
				}elseif($roundresult[$sid][$liveround]==0){
					
					if($k<=7){
						$startunsurfed[$sid] = $nextheat[$sid];
					}elseif($k>7 && $k<99){
						$benchunsurfed[$sid] = $nextheat[$sid];
					}elseif($k>=100){
						$outunsurfed[$sid] = $nextheat[$sid];
					}	
					
				}
				
			}
			
		}
		
		asort($startlost);
		asort($benchlost);
		
		arsort($startunsurfed);
		arsort($benchunsurfed);
		
		
		
		foreach($startwon as $sid=>$v){
//			$toreturn.= "$sid - $v </br>";
		}
		foreach($startunsurfed as $sid=>$v){
//			$toreturn.= "$sid - $v </br>";
		}
		foreach($startlost as $sid=>$v){
//			$toreturn.= "$sid - $v </br>";
		}
		
		foreach($benchwon as $sid=>$v){
//			$toreturn.= "-- $sid - $v </br>";
		}
		foreach($benchunsurfed as $sid=>$v){
//			$toreturn.= "-- $sid - $v </br>";
		}
		foreach($benchlost as $sid=>$v){
//			$toreturn.= "-- $sid - $v </br>";
		}
		
		
		//display lineups
		$toreturn.= "<div class='grid-x align-center teamheader'>
						
						<div class='small-12 cell teamname'>".$pickdata['users'][$user_id]['team']."</div>
						<div class='small-12 cell teamuser'>".$pickdata['users'][$user_id]['name']."</div>
						
					</div>";
		
		foreach($startwon as $sid=>$v){
			
			$toreturn.="<div class='grid-x align-center startingsurfer pos$pos is-$sid'>
			
						<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
						<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
						<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
					</div>";
			
		}
		
		
		foreach($startingpicks as $sid=>$sco){
			
			$pos = $scores[$sid]['pos'];
			
			if(isset($scores[$sid])){
				
				$toreturn .= "
					<div class='grid-x align-center startingsurfer pos$pos is-$sid'>
			
						<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
						<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
						<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
					</div>";
				
			}else{
				
				$toreturn .= "
					<div class='grid-x align-center startingsurfer pos$pos is-$sid'>
			
						<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
						<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
						<div class='large-2 medium-2 small-4 cell teamsurferscore'>".$eventdata['roundresults'][$sid][$liveround]."</div>
					
					</div>";
				
			}

		}
		
		$toreturn.= "<div class='grid-x align-center startingscore'>
									<div class='large-5 medium-7 small-8 cell scoretitle'>Total</div>
									<div class='large-1 small-4 medium-2 cell scorenumber'>".number_format($startingscore)."</div>
								</div>";
		
		foreach($benchpicks as $sid=>$sco){
			$pos = $scores[$sid]['pos'];
			
			$toreturn .= "
					
					<div class='grid-x align-center benchedsurfer pos$pos is-$sid'>
					
						<div class='large-1 medium-2 small-2 cell teamsurferpos'>$pos</div>
					
						<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
						<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
				</div>
			";
		}
		
		$toreturn.= "<div class='grid-x align-center startingscore'>
									<div class='large-5 medium-7 small-8 cell scoretitle'>Bench Total</div>
									<div class='large-1 small-4 medium-2 cell scorenumber'>".number_format($benchscore)."</div>
								</div>";
		
		foreach($outpicks as $sid=>$sco){

			$toreturn .= "
					
				<div class='grid-x align-center outsurfer pos$pos is-$sid'>
					
					<div class='large-1 medium-2 small-2 cell teamsurferpos'> -- </div>
					
					<div class='large-3 medium-5 small-6 cell teamsurfername'>".$surfers[$sid]['name']."</div>
					
					<div class='large-2 medium-2 small-4 cell teamsurferscore'>".number_format($scores[$sid]['sco'])."</div>
					
				</div>
			";
		}
		
		return $toreturn;
	}
	
	private function getNavMenu($event_id,$event_status){
		
		if($event_status==0){
			//upcoming event
			$navmenu = '<div class="grid-x align-center navmenu idleeventnav">
							<div class="cell large-4 small-4 selected">Team</div>
							<div class="cell large-4 small-4">Waivers</div>
							<div class="cell large-4 small-4">Leaderboard</div>
						</div>';
			
		}elseif($event_status==1){
			//idle event, waiver request
			$navmenu='
				<div class="grid-x align-center navmenu idleeventnav">
					<div class="cell large-4 small-4 selected">Team</div>
					<div class="cell large-4 small-4">Waivers</div>
					<div class="cell large-4 small-4">Leaderboard</div>
				</div>
			';
			
		}elseif($event_status==2){
			//idle event, waiver open
			$navmenu='
				<div class="grid-x align-center navmenu idleeventnav">
					<div class="cell large-4 small-4 selected">Team</div>
					<div class="cell large-4 small-4">Waivers</div>
					<div class="cell large-4 small-4">Leaderboard</div>
				</div>
			';
			
		}elseif($event_status==3){
			//live event
			$navmenu='
				<div class="grid-x align-center navmenu activeeventnav">
					<div class="cell large-4 small-4"><a href="events.php?eid='.$event_id.'">Live</a></div>
					<div class="cell large-4 small-4 selected">Team</div>
					<div class="cell large-4 small-4"><a href="standings.php?eid='.$event_id.'">Standings</a></div>
				</div>
				';
			
		}elseif($event_status==4){
			//finished event
			$navmenu='
				<div class="grid-x align-center navmenu finishedeventnav">
					<div class="cell large-4 small-4"><a href="events.php?eid='.$event_id.'">Rounds</a></div>
					<div class="cell large-4 small-4 selected">Team</div>
					<div class="cell large-4 small-4"><a href="standings.php?eid='.$event_id.'">Standings</a></div>
				</div>
			';
		}
		
		return $navmenu;
		
	}
	
	public function getTeam($event_id,$user_id){
		
		$user_id = 104; //<------------------------------eventually remove and use session id
		$league_id = 1; //<------------------------------CHANGE LEAGUE ID
		
		$fsevent = new FSEvent();
		$eventdata = $fsevent->getEventStatus($event_id); //['status'] ['name'] ['current'] ['rounds'] ['nextheat'] ['score'] ['roundresults']
		$surfers = 	 $fsevent->getSurfers();
		
		$event_name = 	$eventdata['name'];
		$event_status = $eventdata['status'];
		$rounds = 		$eventdata['rounds'];
		
		if($event_status == 4){
			
			//event is over
			$pickdata = $this->getPicksByUser($user_id,$event_id,$league_id);
			$scores = $this->getScoresByEvent($event_id);
			
			$availscores = $this->getAvailableScorers($user_id,$event_id,$league_id,$scores);
			
			$thisteam = $this->calculateTeam($user_id,$eventdata,$surfers,$pickdata,$scores,$availscores);
			
			$navmenu = $this->getNavMenu($event_id,$event_status);
			
			$display['team'] = $thisteam;
			$display['nav'] = $navmenu;
 			
			
		}
		else if($event_status == 3){
			
			$pickdata = $this->getPicksByUser($user_id,$event_id,$league_id);			
			
			$thisteam = $this->calculateLiveTeam($user_id,$eventdata,$surfers,$pickdata);
			
			
			$display['team'] = $thisteam;
			
		}
		
		
		
		//TO DO return "Data: $toreturn";
		
		//TO DO find event status
		//TO DO display accordingly
		//TO DO if past -> display results & analysis
		//TO DO if current -> display results & analysis
		//TO DO if future -> display lineup
		
		//TO DO analysis = full team stats, surfers per round, most successful combo, points
		return $display;
	}
	
	
}//end class FSEvent
	
?>