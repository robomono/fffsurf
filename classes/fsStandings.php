<?php

//MAKE TABLE FOR CURRENT OVERALL STANDINGS (SINCE LAST EVENT)
//UPDATE ALL FUNCTIONS TO GET USER AND TEAM NAME FROM LEAGUE CONTROL
//


class FSStandings{
	
	public function __construct(){
		
		session_start();
		//include_once(fsbasics.php);
		require_once("../config/db.php");
		
		include "fsEvent.php";
		
	}
	
	private function getEventScores($event_id, $league_id){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}
		
		//get all picks for thie event and events before
		if (!$this->db_connection->connect_errno) {

			//---GET ALL PICKS PER USER IN EVENT
			$sql = "SELECT user_id,event,pick_id,active 
					FROM league_picks WHERE league_id=$league_id AND event>0 AND event<=$event_id
					ORDER BY user_id,event,active";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				
				$picks[$row['user_id']][$row['event']][$row['active']]['sid'] = $row['pick_id'];
			}
		}
		//end get all picks
		
		//get surfer scores
		if (!$this->db_connection->connect_errno) {

			//---GET ALL PICKS PER USER IN EVENT
			$sql = "SELECT surfer_id,event,position,points FROM surfer_scores WHERE event>0 AND event<=$event_id";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$scores[$row['surfer_id']][$row['event']]['res'] = $row['position'];
				$scores[$row['surfer_id']][$row['event']]['pts'] = $row['points'];
			}
		}
		//end get surfer scores
		
		//apply scores into picks grid and then sort total scores higest to lowest
		foreach($picks as $uid=>$v1){
			foreach($v1 as $eid=>$v2){
				foreach ($v2 as $pos=>$v3){
					$sid = $v3['sid'];
					$picks[$uid][$eid][$pos]['pts'] = $scores[$sid][$eid]['pts'];
					$picks[$uid][$eid][$pos]['res'] = $scores[$sid][$eid]['res'];
					
					if($eid==$event_id && $pos<=7){
						$totals[$uid]+= $scores[$sid][$event_id]['pts'];
					}
					
				}
			}
		}		
		
		arsort($totals);
		//end apply scores to picks and sort

		$toreturn['picks'] = $picks;
		$toreturn['totals'] = $totals;
			
		return $toreturn;
		
	}
	
	private function getOverallStandings($event_id, $league_id){
		
		$last_event = $event_id-1;
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}
		
		if (!$this->db_connection->connect_errno) {

			//---GET ALL PICKS PER USER IN EVENT
			$sql = "SELECT t.user_id,t.after_event,t.event_total,t.agg_total,t.rank,l.name,l.team
					FROM league_totals t
					LEFT JOIN league_control AS l 
					ON t.user_id = l.user_id
					WHERE t.league_id=$league_id AND l.league_id=$league_id 
					ORDER BY t.after_event,t.agg_total DESC";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				
				$ranking[$row['after_event']][$row['user_id']]['rnk'] = $row['rank'];
				$ranking[$row['after_event']][$row['user_id']]['pts'] = $row['agg_total'];
				$ranking[$row['after_event']][$row['user_id']]['evt'] = $row['event_total'];
				$users[$row['user_id']]['name'] = $row['name'];
				$users[$row['user_id']]['team'] = $row['team'];
				
			}
		}
		
		$return['ranking'] = $ranking;
		$return['users'] = $users;
		
		return $return;
		
	}
	
	private function getNav($event_id,$event_status){
		
		if($event_status==0){
			//upcoming event
			$navmenu = '<div class="grid-x align-center navmenu idleeventnav">
							<div class="cell large-4 small-4">Team</div>
							<div class="cell large-4 small-4">Waivers</div>
							<div class="cell large-4 small-4 selected">Leaderboard</div>
						</div>';
			
		}elseif($event_status==1){
			//idle event, waiver request
			$navmenu='
				<div class="grid-x align-center navmenu idleeventnav">
					<div class="cell large-4 small-4 selected">Team</div>
					<div class="cell large-4 small-4">Waivers</div>
					<div class="cell large-4 small-4 selected">Leaderboard</div>
				</div>
			';
			
		}elseif($event_status==2){
			//idle event, waiver open
			$navmenu='
				<div class="grid-x align-center navmenu idleeventnav">
					<div class="cell large-4 small-4 selected">Team</div>
					<div class="cell large-4 small-4">Waivers</div>
					<div class="cell large-4 small-4 selected">Leaderboard</div>
				</div>
			';
			
		}elseif($event_status==3){
			//live event
			$navmenu='
				<div class="grid-x align-center navmenu activeeventnav">
					<div class="cell large-4 small-4">Live</div>
					<div class="cell large-4 small-4">Team</div>
					<div class="cell large-4 small-4 selected">Standings</div>
				</div>
				';
			
		}elseif($event_status==4){
			//finished event
			$navmenu='
				<div class="grid-x align-center navmenu finishedeventnav">
					<div class="cell large-4 small-4"><a href="events.php?eid='.$event_id.'">Rounds</a></div>
					<div class="cell large-4 small-4"><a href="teams.php?eid='.$event_id.'">Team</a></div>
					<div class="cell large-4 small-4 selected">Standings</div>
				</div>
			';
		}
		
		return $navmenu;
	}
	
	private function sortPicks($picks){
		
		//sorts pick by top scorer
		
			foreach($picks as $uid=>$v1){
				foreach($v1 as $eid=>$v2){
					foreach($v2 as $pos=>$v3){
						if($pos<8){
							$ascarray[$uid][$eid][$v3['sid']] = $v3['pts'];
							$descarray[$uid][$eid][$v3['sid']] = $v3['pts'];
						}	
					}
					arsort($descarray[$uid][$eid]);
					asort($ascarray[$uid][$eid]);
				}
			}
			
			$newarray['asc'] = $ascarray;
			$newarray['desc'] = $descarray;
		
		return $newarray;
		
	}
	
	private function getLeaderboardChanges($event_id,$standings){
		
		if($event_id>1){
			
			$last_event = $event_id-1;
			
			foreach($standings[$event_id] as $uid=>$v1){
				$chng[$uid] = $standings[$last_event][$uid]['rnk'] - $standings[$event_id][$uid]['rnk'];
			}
			
		}else{
			
			foreach($standings[$event_id] as $uid=>$v1){$chng[$uid] = 0;}
			
		}
		
		foreach($chng as $uid=>$factor){
			
			if($factor==0){
				$changes[$uid] = " ";
			}
			else if($factor>0){
				$changes[$uid] = "+" .$factor;
			}
			else if($factor<0){
				$changes[$uid] = $factor;
			}
			
		}
		
		return $changes;
		
	}
	
	private function displayEventStandings($event_id,$surfers,$users,$picks,$totals){
		
		//display event leaderboard header
		$display.= "<div class='grid-x align-center eventleaderboardheader'><div class='small-12 cell'>EVENT RESULTS</div></div>";
		//end of event leaderboard header
		
		$display.= "<div class='grid-container standingstable'>";
		
		foreach($totals as $uid=>$total){
			$display.= "<div class='grid-x standingsrow'>"; //start row
			$display.= "<div class='cell large-2 medium-2 small-3 standingsuser'>" .$users[$uid]['team'] ."</div>"; //team name
			
			//build small points here
			//end build small points
			
			//----------------build big display results
			$display.= "<div class='cell large-10 medium-10 hide-for-small-only standingsresults'>";
			$display.= "<div class='grid-x'>";
			foreach($picks[$uid][$event_id] as $sid=>$pts){
				$display.= "<div class='cell large-auto medium-auto standingssurfer pts$pts'>
											<div class='outsurfer'>
												<span data-tooltip aria-haspopup='true' class='has-tip' title='".$surfers[$sid]['name']."'>
													" .$surfers[$sid]['aka'] ."
												</span>
											</div>
											<div class='outpoints'>$pts</div>
										</div>";
			
			}
			$display.= "<div class='cell large-auto medium-auto standingsscores'>$total</div>";
			$display.= "</div></div>";//ends grid-x	 //ends standingsresults
			//----------------end build big display results

			$display.= "</div>";//ends standings row
		}
		
		
		
		$display.= "</div>";//ends grid-container standingstable
		
		return $display;
		
	}
	
	private function displayLeagueStandings($event_id,$surfers,$users,$standings,$picks,$changes){
		
		//display event leaderboard header
		$display.= "<div class='grid-x align-center leaguestandingsheader'><div class='small-12 cell'>LEADERBOARD</div></div>";
		//end of event leaderboard header
		
		$display.= "<div class='grid-container leaguetable'>";
			
		$display.= "<div class='grid-x align-center league-title-row'>
									<div class='cell large-2 medium-2 leaderboard-title-username'>User</div>
									<div class='cell medium-8 medium-8 leaderboard-title-results'>
										<div class='grid-x'>
											<div class='leaderboard-title-result' id='tite1'> 1 </div>
											<div class='leaderboard-title-result' id='tite2'> 2 </div>
											<div class='leaderboard-title-result' id='tite3'> 3 </div>
											<div class='leaderboard-title-result' id='tite4'> 4 </div>
											<div class='leaderboard-title-result' id='tite5'> 5 </div>
											<div class='leaderboard-title-result' id='tite6'> 6 </div>
											<div class='leaderboard-title-result' id='tite7'> 7 </div>
											<div class='leaderboard-title-result' id='tite8'> 8 </div>
											<div class='leaderboard-title-result' id='tite9'> 9 </div>
											<div class='leaderboard-title-result' id='tite10'> 10 </div>
											<div class='leaderboard-title-result' id='tite11'> 11 </div>
										</div>
									</div>
									<div class='large-2 medium-2 columns leaderboard-title-total'>Total</div>
								</div>";
			
			foreach($standings[$event_id] as $uid=>$v1){ //goes through leaguestandings for this event to start with highest scoring user
				
				$display.= "<div class='grid-x align-center leaguerow'>"; //starts a new row for league standings table
				
				//display name and ranking change
				$display.= "<div class='cell medium-2 leaderboard-username' id='lbu".$uid."n' style='border:1px solid black;'>
								<div class='ranking'>".$changes[$uid]."</div>
								<div class=''>".$users[$uid]['name']."</div>
							</div>";
				
				//start section for event scores
				$display.= "<div class='cell medium-8 leaderboard-user-results' id='lbu".$uid."n'>
							<div class='grid-x'>";
				
				for($e=1;$e<=12;$e++){
					
					if(!empty($standings[$e][$uid]['evt'])){
						$display.= "<div class='cell large-auto medium-auto leaderboard-result noselect' id='matchu".$uid."e".$e."'>".$standings[$e][$uid]['evt']."</div>";
					}else{
						$display.= "<div class='leaderboard-result noselect' id='matchu".$uid."e".$e."'>---</div>";
					}
					
				}	
				
				$display.= "</div></div>";//end section for event scores
				
				//display total aggregated score
				$display.= "<div class='medium-2 columns leaderboard-total' id='lbu".$uid."s'>".$v1['pts']."</div>";
				
				$display.= "</div>"; //ends leaguerow, end of user
			
			}//end foreach standings, going from highest ranked user to lowest
		
		$display.= "</div>";//end grid container leaguetable
		
		
		return $display;
		
	}
	
	public function getStandings($event_id){
		
		$league_id = 1;
		
		$fsevent 	= new FSEvent();
		$eventdata 	= $fsevent->getEventStatus($event_id);
		$surfers 	= $fsevent->getSurfers();
		
		$event_name 	= $eventdata['name'];
		$event_status 	= $eventdata['status'];
		$rounds 		= $eventdata['rounds'];
		
		
		if($event_status==4){
			
			//event is over
			
			//get detailed scores for each surfer/team
			$scoresdata = $this->getEventScores($event_id, $league_id);
			
			$picks = $scoresdata['picks'];
			$totals = $scoresdata['totals'];
			
			//sort scores by points descending
			$sortedpicks = $this->sortPicks($picks);
			
			//get standings from this event/last event
			$overall = $this->getOverallStandings($event_id, $league_id);
			
			$standings  = $overall['ranking'];
			$users	 	= $overall['users'];	
						
			//get leaderboard changes
			$changes = $this->getLeaderboardChanges($event_id,$standings);					
			
			//produce displayable standings
			$display .= $this->displayEventStandings($event_id,$surfers,$users,$sortedpicks['asc'],$totals);
			
			//produce league leaderboard
			$display .= $this->displayLeagueStandings($event_id,$surfers,$users,$standings,$sortedpicks['desc'],$changes);
			
			
			
		}
		
		
		
		
		
		
		
		
		
		
		
		
		foreach($totals as $uid=>$total){
			$toreturn .= $uid ." -- " .$total ."</br>";
		}
		
		
		foreach ($sortedpicks as $uid=>$v1){
			foreach ($v1 as $sid=>$pts){
				$toreturn .= $uid ." - " .$sid ." - " .$pts ."</br>";
			}
		}
		
		
		
		foreach($picks as $uid=>$v1){
			foreach($v1 as $pos=>$v2){
				$toreturn .= $uid ." - " .$pos ." - ";
				$toreturn .= $v2['sid'] ." - " .$v2['res'] ." - " .$v2['pts'] ."</br>";
			}
		}
		
		$navigation = $this->getNav($event_id,$event_status);
		
		$return['nav'] = $navigation;
		$return['standings'] = $display;
		
		return $return;
		
	}
	
}//end class FSEvent
	
?>