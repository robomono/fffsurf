<?php

//NEXT STEP ADD PICK HEADERS
	
class FSEvent{
	
	public function __construct(){
		
		session_start();
		//include_once(fsbasics.php);
		require_once("../config/db.php");
		
	}
	
	private function getEventStatus($event_id){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}

		if (!$this->db_connection->connect_errno) {

			//---GET ROUND
			$sql = "SELECT e.name, e.status, h.round, h.heat, h.player, h.surfer_id, h.result
					FROM events AS e
					LEFT JOIN heats AS h
					ON e.id = h.event_id
					WHERE e.id=$event_id
					ORDER BY h.round,h.heat,h.result,h.player";

			$result = $this->db_connection->query($sql);
		
			while($row = mysqli_fetch_array($result)){
				$eventstauts = $row['status'];
				$eventname = $row['name'];
				
				if($eventstauts==4 || $eventstatus==3){
					$event[$row['round']][$row['heat']][$row['player']]['sid'] = $row['surfer_id'];
					$event[$row['round']][$row['heat']][$row['player']]['sco'] = $row['result'];
				}
				
			}
			//---END GET ROUND
			
			$return['status'] = $eventstauts;
			$return['name'] = $eventname;
			$return['rounds'] = $event;
			
			return $return;

		}//connection errors
		
	}
	
	private function getSurfers(){
		
		$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if (!$this->db_connection->set_charset("utf8")) {
			$this->errors[] = $this->db_connection->error;
		}

		if (!$this->db_connection->connect_errno) {

			//---GET ROUND
			$sql = "SELECT id,name,img,aka FROM surfers";

			$result = $this->db_connection->query($sql);
		
			while($row = mysqli_fetch_array($result)){
				$surfers[$row['id']]['name'] = $row['name'];
				$surfers[$row['id']]['aka'] = $row['aka'];
				$surfers[$row['id']]['img'] = $row['img'];
			}
			//---END GET ROUND
		}
		
		return $surfers;
		
	}
	
	private function getPicks($event_id,$league_id){
		
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
					WHERE p.event=$event_id AND p.league_id=$league_id AND p.active<=7
					ORDER BY p.pick_id";

			$result = $this->db_connection->query($sql);
			
			while($row = mysqli_fetch_array($result)){
				$picks[$row['pick_id']][] = $row['user_id'];
				$pick_header[$row['pick_id']] .= " has-".$row['user_id'];
				
				$users[$row['user_id']]['name'] = $row['user_name'];
				$users[$row['user_id']]['short'] = explode(" ",$row['user_name'])[0];
				$users[$row['user_id']]['team'] = $row['user_team'];
			}
			//---END GET ROUND
		}
		
		$toreturn['picks'] = $picks;
		$toreturn['headers'] = $pick_header;
		$toreturn['users'] = $users;
		
		return $toreturn;
		
	}
	
	private function buildEventMenu($eventdata){
		
		$event_status = $eventdata['status'];
		
		if($event_status==0){
			//upcoming event
			$navmenu = '';
			
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
					<div class="cell large-4 small-4 selected">Live</div>
					<div class="cell large-4 small-4">Team</div>
					<div class="cell large-4 small-4">Standings</div>
				</div>
				
				<div class="grid-x align-center roundnav">
					<div class="cell medium-2 small-2"><a href="#" id="roundback"><i class="material-icons">chevron_left</i></a></div>
		
					<div class="cell medium-2 small-8 roundselect selected-round" id="menu-round1">Round 1</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round2">Round 2</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round3">Round 3</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round4">Round 4</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round5">Round 5</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round6">Quarterfinal</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round7">Semifinal</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round8">Final</div>
		
					<div class="cell medium-2 small-2"><a href="#" id="roundnext"><i class="material-icons">chevron_right</i></a></div>
				</div>
				';
			
		}elseif($event_status==4){
			//finished event
			$navmenu='
				<div class="grid-x align-center navmenu finishedeventnav">
					<div class="cell large-4 small-4 selected">Rounds</div>
					<div class="cell large-4 small-4">Team</div>
					<div class="cell large-4 small-4">Standings</div>
				</div>
				
				<div class="grid-x align-center navmenu leaderboardnav hidden">
					<div class="cell large-6 small-6 selected">Fantasy League</div>
					<div class="cell large-6 small-6">World Surf League</div>
				</div>
				
				<div class="grid-x align-center roundnav">
					<div class="cell medium-2 small-2"><a href="#" id="roundback"><i class="material-icons">chevron_left</i></a></div>
		
					<div class="cell medium-2 small-8 roundselect selected-round" id="menu-round1">Round 1</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round2">Round 2</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round3">Round 3</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round4">Round 4</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round5">Round 5</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round6">Quarterfinal</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round7">Semifinal</div>
					<div class="cell medium-2 small-8 roundselect" id="menu-round8">Final</div>
		
					<div class="cell medium-2 small-2"><a href="#" id="roundnext"><i class="material-icons">chevron_right</i></a></div>
				</div>
			';
		}
		
		return $navmenu;
		
	}
	
	private function buildHeatHeaders($rounds,$picks){
		
		foreach($rounds as $round=>$v1){
			foreach($v1 as $heat=>$v2){
				foreach($v2 as $player=>$v3){
					if(!empty($picks[$v3['sid']][0])){
						$headers[$round][$heat] .= " has-".$picks[$v3['sid']][0];
					}
					if(!empty($picks[$v3['sid']][1])){
						$headers[$round][$heat] .= " has-".$picks[$v3['sid']][1];
					}
					if(!empty($picks[$v3['sid']][2])){
						$headers[$round][$heat] .= " has-".$picks[$v3['sid']][2];
					}
					if(!empty($picks[$v3['sid']][3])){
						$headers[$round][$heat] .= " has-".$picks[$v3['sid']][3];
					}
				}
			}
		}
		
		return $headers;
		
	}
	
	private function buildSurferPicks($surfers,$users,$picks){
		
		foreach($picks as $sid=>$v1){
			
			if(!empty($picks[$sid][0])){
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick is-".$picks[$sid][0]."'>".$users[$picks[$sid][0]]['short']."</div>";
			}else{
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick nopicks'></div>";
			}
			
			if(!empty($picks[$sid][1])){
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick is-".$picks[$sid][1]."'>".$users[$picks[$sid][1]]['short']."</div>";
			}else{
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick nopicks'></div>";
			}
			
			if(!empty($picks[$sid][2])){
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick is-".$picks[$sid][2]."'>".$users[$picks[$sid][2]]['short']."</div>";
			}else{
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick nopicks'></div>";
			}
			
			if(!empty($picks[$sid][3])){
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick is-".$picks[$sid][3]."'>".$users[$picks[$sid][3]]['short']."</div>";
			}else{
				$surfers[$sid]['pickcell'] .= "<div class='small-3 cell eventpick nopicks'></div>";
			}
				
			
			
		}
		
		return $surfers;
		
	}
	
	private function buildFilterMenu($users){
		
		$filtermenu.='
			<div class="grid-x align-center filter-menu">
			<div class="large-10 medium-12 small-12 cell" id="selectedfilter">Showing: All <i class="material-icons">chevron_left</i> </div>
			<div class="large-10 medium-12 small-12 cell heat-filter-select" id="selectall">All</div>';
			
			foreach($users as $uid=>$v){
				
				$filtermenu.='<div class="large-10 medium-12 small-12 cell heat-filter-select" id="select'.$uid.'">'.$v['short'].'</div>';
			}	
			
			$filtermenu.='</div>';
			
			return $filtermenu;
		
	}
	
	private function displayFinishedRounds($rounds,$surfers,$picks,$users,$headers){
		
		foreach($rounds as $round=>$v1){
			$toreturn.= "<div class='roundcontainer hiddenround' id='r".$round."'>"; 
			
			foreach($v1 as $heat=>$v2){
				
				$toreturn.= "<div class='grid-x align-center eventrounddetails ".$headers[$round][$heat]."' id='e1h".$heat."'>";
				$toreturn.= "<div class='large-10 medium-12 small-12 cell eventheattitle round".$round."complete'>Heat ".$heat."</div>";
				$toreturn.= "<div class='large-10 medium-12 small-12 cell'>";
				
				foreach($v2 as $player=>$v3){
					
					$sid = $v3['sid'];
					
					if($v3['sco']==1){
						
						$toreturn.="<div class='grid-x heatwinner eventheatrow'>";
						$toreturn.="<div class='large-3 medium-4 cell eventsurfer hide-for-small-only'>".$surfers[$sid]['name']."</div>
									<div class='small-2 cell eventsurfershort show-for-small-only'>".$surfers[$sid]['aka']."</div>";
						
						$toreturn.="<div class='large-9 medium-8 small-10 cell eventpicklist'>
										<div class='grid-x is-collapse-child'>
											".$surfers[$sid]['pickcell']."
										</div>
									</div>";
						
						$toreturn.="</div>";//ends grid-x row heatwinner
						
					}
					elseif($round!=1 && $round!=4 && $v3['sco']==2){
						//lost
						$toreturn.="<div class='grid-x rd".$round."loser eventheatrow'>";
						$toreturn.="<div class='large-3 medium-4 cell eventsurfer hide-for-small-only'>".$surfers[$sid]['name']."</div>
									<div class='small-2 cell eventsurfershort show-for-small-only'>".$surfers[$sid]['aka']."</div>";
						
						$toreturn.="<div class='large-9 medium-8 small-10 cell eventpicklist'>
										<div class='grid-x is-collapse-child'>
											".$surfers[$sid]['pickcell']."
										</div>
									</div>";
						
						$toreturn.="</div>";//ends grid-x row heatloser
					}
					elseif(($round==1 || $round==4) && $v3['sco']==2){
						//relegated second
						$toreturn.="<div class='grid-x heatrelegated eventheatrow'>";
						$toreturn.="<div class='large-3 medium-4 cell eventsurfer hide-for-small-only'>".$surfers[$sid]['name']."</div>
									<div class='small-2 cell eventsurfershort show-for-small-only'>".$surfers[$sid]['aka']."</div>";
						
						$toreturn.="<div class='large-9 medium-8 small-10 cell eventpicklist'>
										<div class='grid-x is-collapse-child'>
											".$surfers[$sid]['pickcell']."
										</div>
									</div>";
						
						$toreturn.="</div>";//ends grid-x row heatloser
						
					}
					elseif($v3['sco']==3){
						//relegated third
						$toreturn.="<div class='grid-x heatrelegated eventheatrow'>";
						$toreturn.="<div class='large-3 medium-4 cell eventsurfer hide-for-small-only'>".$surfers[$sid]['name']."</div>
									<div class='small-2 cell eventsurfershort show-for-small-only'>".$surfers[$sid]['aka']."</div>";
						
						$toreturn.="<div class='large-9 medium-8 small-10 cell eventpicklist'>
										<div class='grid-x is-collapse-child'>
											".$surfers[$sid]['pickcell']."
										</div>
									</div>";
						
						$toreturn.="</div>";//ends grid-x row heatloser
						
					}

				}
				
				$toreturn .= "</div></div>";//ends row grid-x for each heat
			}
			
			$toreturn.= "</div>";//ends round countainer
		}
		
		return $toreturn;
		
	}
	
	public function getAllRounds($event_id){
		
		$league_id = 1;
		
		$eventdata = $this->getEventStatus($event_id);
		$surfers = $this->getSurfers();
		$allpicks = $this->getPicks($event_id,$league_id);
		
		$picks = $allpicks['picks'];
		$users = $allpicks['users'];
				
		$event_name = 	$eventdata['name'];
		$event_status = $eventdata['status'];
		$rounds = 		$eventdata['rounds'];
		
		if($event_status==4){
			//finished event
			$filtermenu = $this->buildFilterMenu($users);
			$surfers 	= $this->buildSurferPicks($surfers,$users,$picks);
			
			$navmenu = $this->buildEventMenu($eventdata);
			
			$headers 	= $this->buildHeatHeaders($rounds,$picks);
			$rounds 	= $this->displayFinishedRounds($rounds,$surfers,$picks,$users,$headers);
			
			$display['nav']	 = $navmenu;
			$display['menu'] = $filtermenu;
			$display['main'] = $rounds;
			
		}
		elseif($event_status==3){
			//live event
			$navmenu = $this->buildEventMenu($eventdata);
			
			$filtermenu = $this->buildFilterMenu($users);
			$headers 	= $this->buildHeatHeaders($rounds,$picks);
			
			$rounds 	= $this->displayFinishedRounds($rounds,$surfers,$picks,$users,$headers);
			
			$display['nav']	 = $navmenu;
			$display['menu'] = $filtermenu;
			$display['main'] = $rounds;
			
		}
		elseif($event_status==2){
			//lineups open - free waivers
			
		}
		elseif($event_status==1){
			//lineups open - waiver period
			
		}
		elseif($event_status==0){
			//upcoming event
			
		}
		
		//return $display;
		
		return $display;
		
	}
	
}//end class FSEvent
	
?>