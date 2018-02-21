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
	
	private function calculatepoints($round,$count){
		
		$points = $count['points'];
		$scored = $count['scored'];
		$wins = $count['wins'];
		$losses = $count['losses'];
		$unsurfed = $count['unsurfed'];
		
//		$toreturn.= " -------- " .$points ." -- </br>";
//		$toreturn.= " -------- scor - " .$scored ."</br>";	
//		$toreturn.= " -------- wins - " .$wins ."</br>";	
//		$toreturn.= " -------- lose - " .$losses ."</br>";
//		$toreturn.= " -------- uns - " .$unsurfed ."</br>";
		
		$bestpossible = $wins + $unsurfed;
		$worstpossible = $losses + $unsurfed;
		
		if($round==2){$roundpoints = 500;}
		if($round==3){$roundpoints = 1750;}
		if($round==5){$roundpoints = 4000;}
		if($round==6){$roundpoints = 5200;}
		if($round==7){$roundpoints = 6500;}
		if($round==8){$roundpoints = 10000;}
		
		//-------------QF-----QF-----SF-----SF-----F-------W--
		$best[1][6] = 5200 + 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[1][5] = 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[1][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[1][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[1][2] = 8000 + 10000;
		//--------------W--
		$best[1][1] = 10000;
		
		//-------------RD2--
		$worst[1][1] = 500;
		//-------------RD2---RD2--
		$worst[1][2] = 500 + 500;
		//-------------RD2---RD2---RD2--
		$worst[1][3] = 500 + 500 + 500;
		//-------------RD2---RD2---RD2---RD2
		$worst[1][4] = 500 + 500 + 500 + 500;
		//-------------RD2---RD2---RD2---RD2---RD2
		$worst[1][5] = 500 + 500 + 500 + 500 + 500;
		//-------------RD2---RD2---RD2---RD2---RD2---RD2
		$worst[1][6] = 500 + 500 + 500 + 500 + 500 + 500;
		
		//-------------QF-----QF-----SF-----SF-----F-------W--
		$best[2][6] = 5200 + 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[2][5] = 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[2][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[2][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[2][2] = 8000 + 10000;
		//--------------W--
		$best[2][1] = 10000;
		
		//-------------RD2--
		$worst[2][1] = 500;
		//-------------RD2---RD2--
		$worst[2][2] = 500 + 500;
		//-------------RD2---RD2---RD2--
		$worst[2][3] = 500 + 500 + 500;
		//-------------RD2---RD2---RD2---RD2
		$worst[2][4] = 500 + 500 + 500 + 500;
		//-------------RD2---RD2---RD2---RD2---RD2
		$worst[2][5] = 500 + 500 + 500 + 500 + 500;
		//-------------RD2---RD2---RD2---RD2---RD2---RD2
		$worst[2][6] = 500 + 500 + 500 + 500 + 500 + 500;
		
		//-------------QF-----QF-----SF-----SF-----F-------W--
		$best[3][6] = 5200 + 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[3][5] = 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[3][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[3][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[3][2] = 8000 + 10000;
		//--------------W--
		$best[3][1] = 10000;
		
		//--------------RD3--
		$worst[3][1] = 1750;
		//--------------RD3----RD3--
		$worst[3][2] = 1750 + 1750;
		//--------------RD3----RD3----RD3--
		$worst[3][3] = 1750 + 1750 + 1750;
		//--------------RD3----RD3----RD3----RD3
		$worst[3][4] = 1750 + 1750 + 1750 + 1750;
		//-------------RD3----RD3-----RD3----RD3----RD3
		$worst[3][5] = 1750 + 1750 + 1750 + 1750 + 1750;
		//-------------RD3-----RD3----RD3----RD3----RD3---RD3
		$worst[3][6] = 1750 + 1750 + 1750 + 1750 + 1750 + 1750;
		
		//-------------QF-----QF-----SF-----SF-----F-------W--
		$best[4][6] = 5200 + 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[4][5] = 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[4][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[4][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[4][2] = 8000 + 10000;
		//--------------W--
		$best[4][1] = 10000;
		
		//--------------RD5--
		$worst[4][1] = 4000;
		//--------------RD5----RD5--
		$worst[4][2] = 4000 + 4000;
		//--------------RD5----RD5----RD5--
		$worst[4][3] = 4000 + 4000 + 4000;
		//--------------RD5----RD5----RD5----RD5
		$worst[4][4] = 4000 + 4000 + 4000 + 4000;
		//-------------RD5-----RD5----RD5----RD5----QF
		$worst[4][5] = 4000 + 4000 + 4000 + 4000 + 6500;
		//-------------RD5-----RD5----RD5----RD5----QF-----QF
		$worst[4][6] = 4000 + 4000 + 4000 + 4000 + 6500 + 6500;
		
		//-------------QF----QF-----SF-----SF-----F-------W--
		$best[5][6] = 5200 + 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[5][5] = 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------SF-----SF-----F-------W--
		$best[5][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[5][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[5][2] = 8000 + 10000;
		//--------------W--
		$best[5][1] = 10000;
		
		//--------------RD5--
		$worst[5][1] = 4000;
		//--------------RD5----RD5--
		$worst[5][2] = 4000 + 4000;
		//--------------RD5----RD5----RD5--
		$worst[5][3] = 4000 + 4000 + 4000;
		//--------------RD5----RD5----RD5----RD5
		$worst[5][4] = 4000 + 4000 + 4000 + 4000;
		//-------------RD5-----RD5----RD5----RD5----QF
		$worst[5][5] = 4000 + 4000 + 4000 + 4000 + 6500;
		//-------------RD5-----RD5----RD5----RD5----QF-----QF
		$worst[5][6] = 4000 + 4000 + 4000 + 4000 + 6500 + 6500;
		
		//-------------QF-----QF-----SF-----SF-----F-------W--
		$best[6][6] = 5200 + 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------QF-----SF-----SF-----F-------W--
		$best[6][5] = 5200 + 6500 + 6500 + 8000 + 10000;
		//-------------SF-----SF-----SF-----F-------W--
		$best[6][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[6][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[6][2] = 8000 + 10000;
		//--------------W--
		$best[6][1] = 10000;
		
		//---------------QF----QF------QF----QF-----SF-----SF
		$worst[6][6] = 5200 + 5200 + 5200 + 5200 + 6500 + 6500;
		//---------------QF----QF------QF----QF-----SF---
		$worst[6][5] = 5200 + 5200 + 5200 + 5200 + 6500;
		//---------------QF----QF------QF----QF---
		$worst[6][4] = 5200 + 5200 + 5200 + 5200;
		//---------------QF----QF------QF--
		$worst[6][3] = 5200 + 5200 + 5200;
		//---------------QF----QF--
		$worst[6][3] = 5200 + 5200;
		//---------------QF----QF--
		$worst[6][3] = 5200;
		
		//-------------SF-----SF-----SF-----F-------W--
		$best[7][4] = 6500 + 6500 + 8000 + 10000;
		//--------------SF-----F-------W--
		$best[7][3] = 6500 + 8000 + 10000;
		//--------------F-------W--
		$best[7][2] = 8000 + 10000;
		//--------------W--
		$best[7][1] = 10000;
		
		//--------------SF--
		$worst[7][1] = 6500;
		//--------------SF------SF--
		$worst[7][2] = 6500 + 6500;
		//--------------SF------SF----F--
		$worst[7][3] = 6500 + 6500 + 8000;
		//--------------SF------SF----F-------W---
		$worst[7][4] = 6500 + 6500 + 8000 + 10000;
		
		//-------------2-------1----
		$best[8][2] = 8000 + 10000;
		//--------------1--
		$best[8][1] = 10000;
		
		//--------------2---
		$worst[8][1] = 8000;
		//--------------2-------1---
		$worst[8][2] = 8000 + 10000;
		
		
		
		
		$bestscore = $points + $best[$round][$bestpossible] + ($losses*$roundpoints);
		$worstscore = $points + $worst[$round][$wins] + ($worstpossible*$roundpoints);
		
//		$toreturn.= "Best Possible: " .$bestscore ."</br>";
//		$toreturn.= "Worst Possible: " .$worstscore ."</br></br>";
		
		$toreturn['best'] = $bestscore;
		$toreturn['worst'] = $worstscore;
		
		return $toreturn;
		
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
					
					if($eid==$event_id && $pos<=5){
						$totals[$uid]+= $scores[$sid][$event_id]['pts'];
					}
					
				}
			}
		}		
		
		arsort($totals);//sort highest to lowest
		$livetotals = $totals;asort($livetotals);//sort lowest to highest
		//end apply scores to picks and sort

		$toreturn['picks'] = $picks;
		$toreturn['totals'] = $totals;
		$toreturn['livetotals'] = $livetotals;
			
		return $toreturn;
		
	}
	
	private function getCurrentRound($event_id,$picks,$totals){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}
		
		//get all heats from current event
		if (!$this->db_connection->connect_errno) {

			$sql = "SELECT h.round,h.heat,h.player,h.surfer_id,h.result,h.jersey,e.nowsurfing
					FROM heats as h
					LEFT JOIN events as e
					ON h.event_id = e.id
					WHERE e.id=$event_id AND h.event_id=$event_id";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				
				$roundandheat = $row['nowsurfing'];
				
				$heats[$row['round']][$row['heat']][$row['player']]['sid'] = $row['surfer_id'];
				$heats[$row['round']][$row['heat']][$row['player']]['res'] = $row['result'];
				$heats[$row['round']][$row['heat']][$row['player']]['jer'] = $row['jersey'];
				
			}
		}
		
		//get current round and heat
		$pieces = explode(".", $roundandheat);
		$thisround = $pieces[0];
		$thisheat = $pieces[1];
		
		
		$toreturn['allheats'] = $heats;
		$toreturn['currentround'] = $thisround;
		$toreturn['currentheat'] = $thisheat;
		
		return $toreturn;
		
	}
	
	private function calculateSurfersAndPoints($event_id,$heats,$thisround,$picks){
		
		//make array of surfers still to surf and surfers that are done with the heat
		foreach($heats[$thisround] as $heat=>$v1){
			foreach($v1 as $k2=>$v2){
				if($v2['res']>0){
					$surfed[] = $v2['sid'];
				}else{
					$tosurf[$heat][] = $v2['sid'];
				}
			}
		}
		
		//go through each users picks and count losses and wins based on surfed and tosurf surfers
		foreach($picks as $uid=>$v1){
			foreach($v1[$event_id] as $pid=>$v2){
				if($pid<6){//pick must be under 6 (starting lineup)
					$sid = $v2['sid'];
					if($v2['pts']>0){
						//if points are more than 0 surfer lost and has been awarded points
						$count[$uid]['scored'] +=1;
						$count[$uid]['points'] += $v2['pts'];
						$sorted[$uid]['scored'][] = $sid;
						$sorted[$uid]['points'][$sid] = $v2['pts'];
					}else{
						//points are 0 or less
						if (in_array($sid, $surfed)) {
							//no points but has surfed this round, meaning surfer won
							$count[$uid]['wins'] +=1;
							$sorted[$uid]['wins'][] = $sid;
						}else{
							//add this surfer to array of unsurfed picks
							$sorted[$uid]['unsurfed'][] = $sid;
							$count[$uid]['unsurfed'] += 1; 
						}
						
					}
				}
			}
		}
		
		//find if any user has two or more surfers in the same heat and count/discount to wins
		foreach($heats[$thisround] as $heat=>$h1){	
			
			if($thisround==1 || $thisround==3){$thisheat = array($h1[1]['sid'],$h1[2]['sid'],$h1[3]['sid']);}
			else{$thisheat = array($h1[1]['sid'],$h1[2]['sid']);}
			
			foreach($picks as $uid=>$v1){
				//create array thisheat with all surfers in this heat
					
				//intersect heats. if count is more than 1 then user has two or more picks surfing against each other
				if(count(array_intersect($thisheat, $sorted[$uid]['unsurfed']))==2){
					$count[$uid]['wins'] +=1;
					$count[$uid]['losses'] +=1;
					$count[$uid]['unsurfed']-=2;
				}
				else if(count(array_intersect($thisheat, $sorted[$uid]['unsurfed']))==3){
					$count[$uid]['wins'] +=1;
					$count[$uid]['losses'] +=2;
					$count[$uid]['unsurfed'] -=3;
				}
			}	
		}
		
		
		foreach($picks as $uid=>$v1){
			$possible = $this->calculatepoints($thisround,$count[$uid]);	
			$count[$uid]['best'] = $possible['best'];
			$count[$uid]['worst'] = $possible['worst'];
		}
		
		//count[$uid] - [wins] - [losses] - [unsurfed] - [scored] - [points] - [best] - [worst]
		//sorted[$uid] - [scored] - [wins] - [unsurfed]
		
		$toreturn['sorted'] = $sorted;
		$toreturn['count'] = $count;
		
		
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
					<div class="cell large-4 small-4"><a href="live.php?eid='.$event_id.'">Live</a></div>
					<div class="cell large-4 small-4"><a href="teams.php?eid='.$event_id.'">Team</a></div>
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
	
	private function sortLivePicks($count){
		
//		foreach($picks as $uid=>$v1){
//			$toreturn.="</br><b>$uid</b></br>";
//			$toreturn.="--- W: " .$count[$uid]['wins'] ."</br>";
//			$toreturn.="--- L: " .($count[$uid]['losses'] + $count[$uid]['scored']) ."</br>";
//			$toreturn.="--- U: " .$count[$uid]['unsurfed'] ."</br>";
//			$toreturn.="--- BS: " .$count[$uid]['best'] ."</br>";
//			$toreturn.="--- WS: " .$count[$uid]['worst'] ."</br>";
//		}
		
		//sort more wins + less losses on top, more losses + less wins on bottom //adds factors (not actual points) as tiebrakers
		foreach($count as $uid=>$v){
			$bestscores[$uid] =  $count[$uid]['best'] + (($count[$uid]['wins'])*2) + $count[$uid]['unsurfed'] - (($count[$uid]['losses'])*2);
			$toreturn.="$uid - ".$count[$uid]['best']." </br>";
		}
		
		arsort($bestscores);
		
		return $bestscores;
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
		
		foreach($totals as $uid=>$total){
			//build row with username and total event score
			$display.= "<div class='grid-x sm-standingsrow sm-eventrowu".$uid."'>
							<div class='cell small-8 sm-standings-username'>".strtoupper($users[$uid]['team'])."</div>
							<div class='cell small-4 sm-standings-total'>".number_format($total)."</div>
						</div>";
			
			//separate row with surfer-by-surfer score
			$display.= "<div class='grid-x sm-surfersrow sm-teamrowu".$uid."'>";			
			foreach($picks[$uid][$event_id] as $sid=>$pts){
						
				$display.="<div class='cell small-2 sm-standingssurfer pts$pts'>							
								<div class='sm-standings-surferpoints'>".$pts."</div>
								<div class='sm-standings-surfername'>".$surfers[$sid]['aka']."</div>
							</div>";
					
			}
			$display.= "</div>";
				
		}
		
		
			
		$display.= "</div>";//ends grid-container standingstable

//-----------------END BUILD FOR SMALL SCREENS

		
		return $display;
		
	}
	
	private function displayRunningEventStandings($event_id,$surfers,$users,$order,$count,$sorted){
		
		
		
		foreach($order as $uid=>$useless){
				
			$display.="<b>$uid - ".$users[$uid]['name']."</b></br>";
			
			foreach($sorted[$uid]['wins'] as $k=>$v){
				$display.="--- W --- $v - ".$surfers[$v]['name']."</br>";
			}
			
			foreach($sorted[$uid]['unsurfed'] as $k=>$v){
				$display.="--- U --- $v - ".$surfers[$v]['name']."</br>";
			}
			
			foreach($sorted[$uid]['scored'] as $k=>$v){
				$display.= "--- L --- $v - ".$surfers[$v]['name']." - ";
				$display.= $sorted[$uid]['points'][$v] ."</br>";
			}
			
			$display.= "----------------------------------------------<b>" .number_format($count[$uid]['points'])."</b></br>";
			$display.= "----------------------------------------BP: " .number_format($count[$uid]['best'])."</br>";
			$display.= "----------------------------------------WP: " .number_format($count[$uid]['worst'])."</br></br>";
		}
		
		
		
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
		elseif($event_status==3){
			
			//running event
			
			//get detailed scores for each surfer/team
			$scoresdata = $this->getEventScores($event_id, $league_id);
			
			$picks = $scoresdata['picks'];
			$totals = $scoresdata['livetotals'];
			
			//get all rounds and heats from current event plus current round and heat
			$eventround = $this->getCurrentRound($event_id,$picks,$totals);
			
			$allheats = $eventround['allheats'];
			
			$thisround = $eventround['currentround'];
			$thisheat  =  $eventround['currentheat'];
			
			
			//get possible scores
			$surfersandpoints= $this->calculateSurfersAndPoints($event_id,$allheats,$thisround,$picks);

			$count = $surfersandpoints['count'];//count[$uid] - [wins] - [losses] - [unsurfed] - [scored] - [points] - [best] - [worst]		
			$sorted = $surfersandpoints['sorted'];//sorted[$uid] - [scored] - [wins] - [unsurfed]
			
			//sort scores by current standings (more wins less losses to more losses less wins)
			$order = $this->sortLivePicks($count);
			
			//next----display standings
			
//----------EVENT STANDINGS DATA ENDS HERE			
			
			//get standings from this event/last event
			$overall = $this->getOverallStandings($event_id, $league_id);
			
			$standings  = $overall['ranking'];
			$users	 	= $overall['users'];	
						
			//get leaderboard changes
//			$changes = $this->getLeaderboardChanges($event_id,$standings);					
			
			//produce displayable standings
			$display .= $this->displayRunningEventStandings($event_id,$surfers,$users,$order,$count,$sorted);
			
			//produce league leaderboard
//			$display .= $this->displayLeagueStandings($event_id,$surfers,$users,$standings,$sortedpicks['desc'],$changes);
		
		}
		
		
		
		
		
		
		
		$navigation = $this->getNav($event_id,$event_status);
		
		$return['nav'] = $navigation;
		$return['standings'] = $display;
		
		return $return;
		
	}
	
}//end class FSEvent
	
?>