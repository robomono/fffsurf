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
			$sql = "SELECT t.user_id,t.after_event,t.event_total,t.agg_total,t.rank,l.name,l.team,l.short
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
				$users[$row['user_id']]['short'] = $row['short'];
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
		
//-----------------BUILD FOR LARGE AND MEDIUM SCREENS		
		$display.= "<div class='grid-container hide-for-small-only standingstable'>";
		
		foreach($totals as $uid=>$total){
			$display.= "<div class='grid-x standingsrow eventrowu".$uid."'>"; //start row
			$display.= "<div class='cell large-2 medium-2 small-3 standingsuser'>" .$users[$uid]['team'] ."</div>"; //team name
			
			//build small points here
			//end build small points
			
			//----------------build big display results
			$display.= "<div class='cell large-10 medium-10 standingsresults'>";
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
		
//-----------------END BUILD FOR LARGE AND MEDIUM SCREENS	



//-----------------BUILD FOR SMALL SCREENS

		$display.= "<div class='grid-container show-for-small-only sm-standingstable'>";
		
		
		
		$display.= "</div>";//ends grid-container standingstable

//-----------------END BUILD FOR SMALL SCREENS

		
		return $display;
		
	}
	
	private function displayLeagueStandings($event_id,$surfers,$users,$standings,$picks,$changes){
		
		$events[1]['name']  = "Quiksilver Pro Gold Coast";
		$events[1]['flag']  = "australia1.png";
		
		$events[2]['name']  = "Rip Curl Pro Bells Beach";
		$events[2]['flag']  = "australia2.png";
		
		$events[3]['name']  = "Margaret River Pro";
		$events[3]['flag']  = "australia3.png";
		
		$events[4]['name']  = "Oi Rio Pro";
		$events[4]['flag']  = "brazil.png";
		
		$events[5]['name']  = "Bali Pro Keramas";
		$events[5]['flag']  = "indonesia.png";
		
		$events[6]['name']  = "Corona Open J-Bay";
		$events[6]['flag']  = "southafrica.png";
		
		$events[7]['name']  = "Tahiti Pro Teahupoo";
		$events[7]['flag']  = "tahiti.png";
		
		$events[8]['name']  = "Surf Ranch Open";
		$events[8]['flag']  = "usa.png";
		
		$events[9]['name']  = "Quiksilver Pro France";
		$events[9]['flag']  = "france.png";
		
		$events[10]['name'] = "MEO Rip Curl Pro Portugal";
		$events[10]['flag'] = "portugal.png";
		
		$events[11]['name'] = "Billabong Pipe Masters";
		$events[11]['flag'] = "hawaii.png";
		
		//display event leaderboard header
		$display.= "<div class='grid-x align-center leaguestandingsheader'><div class='small-12 cell'>LEADERBOARD</div></div>";
		//end of event leaderboard header


//----------------------START LARGE AND MEDIUM LEADERBOARD
		
		$display.= "<div class='grid-container leaguetable hide-for-small-only'>";
			
		$display.= "<div class='grid-x align-center league-title-row'>
									<div class='cell large-2 medium-2 leaderboard-title-username'>User</div>
									<div class='cell medium-8 medium-8 leaderboard-title-results'>
										<div class='grid-x'>";
		
										for($e=1;$e<=11;$e++){//build title cell for each event
											
											if($e<=$event_id){
												
												//non-empty result
												if($e==$event_id){	//current event loads up expanded
													$display.="<div class='leaderboard-title title-expanded' id='title".$e."'>".$e."</div>";
												}else{//not current event loads up collapsed
													$display.="<div class='leaderboard-title' id='title".$e."'>".$e."</div>";
												}
												
											}else{
												
												//empty result
												$display.="<div class='leaderboard-title emptyresult' id='title".$e."'>".$e."</div>";
												
											}
											
											
										}
		
		$display.= "							
										</div>
									</div>
									<div class='large-2 medium-2 columns leaderboard-title-total'>Total</div>
								</div>";


			//>->->-BUILD USER ROW WITH EVENT TOTALS	
	
			$oddcount = 0; //keeps count of odd/even rows for color display purposes					
			
			foreach($standings[$event_id] as $uid=>$v1){ //goes through leaguestandings for this event to start with highest scoring user
				
				$display.= "<div class='grid-x align-center align-middle leaguerow odd".$oddcount." rowu".$uid."'>"; //starts a new row for leaguestandings table, if its even then class is odd0, if its odd then class is odd1
				
				//display name and ranking change
				$display.= "<div class='cell medium-2 leaderboard-username' id='lbnu".$uid."'>
											<div class='grid-x align-center align-middle'>
												<div class='cell large-1 medium-2 ranking'>".$changes[$uid]."</div>
												<div class='cell large-11 medium-10'>
																				<div class='show-for-large lb-user-team-lg'>".strtoupper($users[$uid]['team'])."</div>
																				<div class='show-for-medium-only lb-user-team-md'>".strtoupper($users[$uid]['team'])."</div>
												</div>
											</div>
										</div>";
				
				//start section for event scores
				$display.= "<div class='cell medium-8 leaderboard-user-results' id='lbu".$uid."n'>
							<div class='grid-x'>";
				
				for($e=1;$e<=11;$e++){
					
					if(!empty($standings[$e][$uid]['evt'] && $e<=$event_id)){
						
						if($e==$event_id){
							$display.= "<div class='leaderboard-result cellu".$uid." resulte".$e." result-expanded' id='matchu".$uid."e".$e."'>".number_format($standings[$e][$uid]['evt'])."</div>";
						}else{
							$display.= "<div class='leaderboard-result cellu".$uid." resulte".$e."' id='matchu".$uid."e".$e."'>".number_format($standings[$e][$uid]['evt'])."</div>";
						}
						
						
					}else{
						$display.= "<div class='leaderboard-result emptyresult cellu".$uid." resulte".$e."' id='matchu".$uid."e".$e."'>---</div>";
					}
					
				}	
				
				$display.= "</div></div>";//end section for event scores
				
				//display total aggregated score
				$display.= "<div class='cell medium-2 leaderboard-total' id='lbsu".$uid."'>".number_format($v1['pts'])."</div>";
				
				$display.= "</div>"; //ends leaguerow, end of user totals
				//<-<-<-END BUILD USER ROW WITH EVENT TOTALS
				
				//->->->BUILD EVENT-BY-EVENT TEAM SCORES
								
				
				for($e=1;$e<=11;$e++){
					
					$display.= "<div class='grid-x scoresrow detu".$uid."e".$e." teamscore-hidden'>";
					
					$display.= "<div class='cell large-6 medium-6 team-comtainer'><div class='grid-x'>";
					
					$columncount = 1;
					
					foreach($picks[$uid][$e] as $sid=>$sco){
						
						if($columncount<4){
							
							$display.= "<div class='cell large-5 medium-3 team-surfer-fill'> </div>";
							$display.= "<div class='cell large-4 medium-6 team-surfer-name'>".$surfers[$sid]['name']."</div>";
							$display.= "<div class='cell large-2 medium-2 team-surfer-score'>".number_format($sco)."</div>";
							$display.= "<div class='cell large-1 medium-1 team-surfer-fill'> </div>";
							
							$columncount++;
						
						}else if($columncount==4){
							
							$display.= "<div class='cell large-5 medium-3 team-surfer-fill'> </div>";
							$display.= "<div class='cell large-4 medium-6 team-surfer-name'>".$surfers[$sid]['name']."</div>";
							$display.= "<div class='cell large-2 medium-2 team-surfer-score'>".number_format($sco)."</div>";
							$display.= "<div class='cell large-1 medium-1 team-surfer-fill'> </div>";
							$display.= "</div></div>";
							$display.= "<div class='cell medium-6 team-comtainer'><div class='grid-x'>";
							
							$columncount++;
							
						}else if($columncount>4){
							
							$display.= "<div class='cell large-4 medium-6 team-surfer-name'>".$surfers[$sid]['name']."</div>";
							$display.= "<div class='cell large-2 medium-2 team-surfer-score'>".number_format($sco)."</div>";
							$display.= "<div class='cell large-6 medium-4 team-surfer-fill'> </div>";
							
							$columncount++;
							
						}
						
						
						
					}
					$display.= "</div></div></div>";//ends grid-x, ends team-container #2, ends scoresrow
					
					
				}
				//<-<-<-END BUILD EVENT-BY-EVENT TEAM SCORES
				
			if($oddcount==0){$oddcount = 1;}else{$oddcount = 0;}//reset odd counter to populate next row
				
			}//end foreach standings, going from highest ranked user to lowest
		
		$display.= "</div>";//end grid container leaguetable
		
		//----------------------END LARGE AND MEDIUM LEADERBOARD	
		
		//---------------------START SMALL LEADERBOARD
		$display.= "<div class='grid-container sm-leaguetable show-for-small-only'>";
		
		
		foreach($standings[$event_id] as $uid=>$v1){ 
			
			$display.= "<div class='grid-x sm-lb-row' id='sm-lb-u".$uid."'>
										<div class='cell small-1 sm-lb-chng'>".$changes[$uid]."</div>
										<div class='cell small-6 sm-lb-username'>
																		<div class='lb-user-team-sm'>".strtoupper($users[$uid]['team'])."</div>
										</div>
										<div class='cell small-4 sm-lb-total'>".number_format($v1['pts'])."</div>
										<div class='cell small-1 sm-lb-expanduser'> 
																	<i class='material-icons closeduserrow'>chevron_left</i>
																	<i class='material-icons openeduserrow' style='display:none'>expand_more</i> 
										</div>
									</div>
									";
									
			$display.="<div class='sm-lb-eventscontainer sm-events-u".$uid."'>";
				
			for($e=1;$e<=11;$e++){
				if($e<=$event_id){
					
					$display.= "<div class='grid-x sm-lb-event-row' id='sm-evt-u".$uid."e".$e."'>
												<div class='cell small-8 sm-lb-eventname'>".$events[$e]['name']."</div>
												<div class='cell small-3 sm-lb-eventscore'>".number_format($standings[$e][$uid]['evt'])."</div>
												<div class='cell small-1 sm-lb-expandevent'>
																<i class='material-icons closedeventrow'>chevron_left</i>
																<i class='material-icons openedeventrow' style='display:none'>expand_more</i> 
												</div>
											</div>";
				
					foreach($picks[$uid][$e] as $sid=>$sco){
							
						$display.= "<div class='grid-x sm-lb-surfers-row sm-surfers-foru".$uid."e".$e."'>
													<div class='cell small-8 sm-lb-surfer'>".$surfers[$sid]['name']."</div>
													<div class='cell small-3 sm-lb-score'>".number_format($sco)."</div>
													<div class='cell small-1'> </div>
												</div>";
							
					}//end for each picks
					
				}//end if event is lower than event id
			}//end for each event
			
			$display.="</div>"; //ends events container for user
										
		}//ends for each user in standings
		
		$display.= "</div>";//ends small leaguetable contianer
		//----------------------END SMALL LEADERBOARD
		
				
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