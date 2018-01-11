<?php

//MAKE TABLE FOR CURRENT OVERALL STANDINGS (SINCE LAST EVENT)

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
		
		//get all picks and scores for this event
		if (!$this->db_connection->connect_errno) {

			//---GET ALL PICKS PER USER IN EVENT
			$sql = "SELECT p.user_id,p.pick_id,p.active,s.surfer_id,s.position,s.points FROM league_picks AS p
					LEFT JOIN surfer_scores AS s
					ON p.pick_id = s.surfer_id
					WHERE p.event=$event_id AND p.league_id=$league_id AND s.event=$event_id
					ORDER BY p.user_id,p.active";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$picks[$row['user_id']][$row['active']]['sid'] = $row['surfer_id'];
				$picks[$row['user_id']][$row['active']]['res'] = $row['position'];
				$picks[$row['user_id']][$row['active']]['pts'] = $row['points'];
				
				if($row['active']<=7){
					$totals[$row['user_id']] += $row['points'];
				}
				
			}
		}
		
		arsort($totals);

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
			$sql = "SELECT user_id,after_event,agg_total,rank FROM league_totals
					WHERE league_id=$league_id AND after_event=$event_id OR after_event=$last_event
					ORDER BY after_event,agg_total DESC";
			
			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				
				$ranking[$row['after_event']][$row['user_id']]['rnk'] = $row['rank'];
				$ranking[$row['after_event']][$row['user_id']]['pts'] = $row['agg_total'];
				
			}
		}
		
		return $ranking;
		
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
			foreach($v1 as $pos=>$v2){
				if($pos<=8){
					$newarray[$uid][$v2['sid']] = $v2['pts'];
				}
			}
			arsort($newarray[$uid]);
		}
		
		return $newarray;
		
	}
	
	public function getStandings($event_id){
		
		$league_id = 1;
		
		$fsevent = new FSEvent();
		$eventdata = $fsevent->getEventStatus($event_id);
		$surfers = 	 $fsevent->getSurfers();
		
		$event_name = 	$eventdata['name'];
		$event_status = $eventdata['status'];
		$rounds = 		$eventdata['rounds'];
		
		
		
		if($event_status==4){
			
			//event is over
			
			//get detailed scores for each surfer/team
			$scoresdata = $this->getEventScores($event_id, $league_id);
			
			$picks = $scoresdata['picks'];
			$totals = $scoresdata['totals'];
			$lasttotals = $scoresdata['lasttotals'];
			
			//sort scores by points descending
			$sortedpicks = $this->sortPicks($picks);
			
			//get standings from this event/last event
			$standings = $this->getOverallStandings($event_id, $league_id);
			
			
			
			
		}
		
		
		$lastevt = $event_id - 1;
		
		foreach($standings as $evt=>$v1){
			
			foreach($v1 as $uid=>$v2){
				
				if($evt==$event_id){
					
					$chng = $standings[$lastevt][$uid]['rnk'] - $v2['rnk'];
					
					$toreturn.= "[$chng] " .$v2['rnk'] ." - $uid - " .$v2['pts'] ."</br>";
					
				}
				
				
				
			}
			
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
		$return['standings'] = $toreturn;
		
		return $return;
		
	}
	
}//end class FSEvent
	
?>