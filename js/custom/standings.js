$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = searchParams.get("eid"); //123
	var huid = searchParams.get("huid"); //123
	
	$.ajax({
	  type: "POST",
	  //data: "getUserTeam",
	  data: {action:"getEventStandings", eventid:eid},
	  url: "classes/fsStandingsHandler.php",
	  dataType: "json",
	  async: false,
	  success: function(data){
				
		$('.navigation').html(data['nav']);
		//$('.eventmenu').html(data['menu']);
		//$('.allrounds').html(data['main']);
		$('.allstandings').html(data['standings']);				
		$(document).foundation();
						
	  }
	});
	
	$(".teamscore-hidden").hide();
	
});



//EXPANDING AND HIHGLIGHTING LEADERBOARD ROWS
$( document ).ready(function() {
	
	var selectedcell = 0; //keeps track if a cell has been selected or not
	
	//-----TABLE HOVER ACTIONS
	$('.leaderboard-result').hover(function(){		
		
		var thisevent = ($(this).attr('id').split('e'))[1];
		var thisuser = $(this).attr('id').split(/['u''e']+/)[1];
	
		var userrow = ".rowu" + thisuser;
		var resultclass = ".resulte" + thisevent;
		var resulttitle = "#title" + thisevent;
		
		if($(this).hasClass("emptyresult")){
			
			//emptyresult, no hover expand
			
			$(".cell-highlighted").removeClass("cell-highlighted");
			
			$(".column-highlighted").removeClass("column-highlighted");
			$(".row-highlighted").removeClass("row-highlighted");
			
			$(userrow).addClass("row-highlighted");
			$(resultclass).addClass("column-highlighted");
			
		}else{
			
			//result, all functions should work
		
			if(selectedcell==1){
				
				//a user's team is expanded, limited functions
				$(".column-highlighted").removeClass("column-highlighted");
				$(".cell-highlighted").removeClass("cell-highlighted");
				$(".row-highlighted").removeClass("row-highlighted");
				
				$(userrow).addClass("row-highlighted");
				$(resultclass).addClass("column-highlighted");
				$(this).addClass("cell-highlighted");
				
			}else{
				
				//all team results are collapsed, all functions work
				$(".column-highlighted").removeClass("column-highlighted");
				$(".result-expanded").removeClass("result-expanded");
				$(".cell-highlighted").removeClass("cell-highlighted");
				$(".title-expanded").removeClass("title-expanded");
				$(".row-highlighted").removeClass("row-highlighted");
		
		
				$(userrow).addClass("row-highlighted");
				$(resultclass).addClass("result-expanded column-highlighted");
				$(resulttitle).addClass("title-expanded");
				$(this).addClass("cell-highlighted");
				
			}
		
			
			
		}
		
	});
	
	//-----TITLE HOVER ACTIONS
	$('.leaderboard-title').hover(function(){
			
		var thisevent = ($(this).attr('id').split('e'))[1];
			
		var resultclass = ".resulte" + thisevent;
		var resulttitle = "#title" + thisevent;
		
		
		if($(this).hasClass("emptyresult")){
			
			//empty result, no expand
			$(".row-highlighted").removeClass("row-highlighted");
			$(".cell-highlighted").removeClass("cell-highlighted");
			$(".column-highlighted").removeClass("column-highlighted");
			
			$(resultclass).addClass("column-highlighted");
			
		}else{
			
			//complete result, highlight functions available
			
			if(selectedcell==1){
				
				//a cell is clicked, limited highlight functions
				
				$(".row-highlighted").removeClass("row-highlighted");
				$(".cell-highlighted").removeClass("cell-highlighted");
			
				$(".column-highlighted").removeClass("column-highlighted");
				$(resultclass).addClass("column-highlighted");

				
			}else{
				
				//no cells clicked, all highlight functions functional
				
				$(".row-highlighted").removeClass("row-highlighted");
				$(".cell-highlighted").removeClass("cell-highlighted");
			
				$(".column-highlighted").removeClass("column-highlighted");
				$(".result-expanded").removeClass("result-expanded");
				$(resultclass).addClass("result-expanded column-highlighted");
		
				$(".title-expanded").removeClass("title-expanded");
				$(resulttitle).addClass("title-expanded");
				
			}
			
			
			
		}
	});	
	
	//-----USERNAME HOVER ACTIONS
	$('.leaderboard-username').hover(function(){
		
		var thisuser = ($(this).attr('id').split('u'))[1];
		var userrow = ".rowu" + thisuser;
		
		$(".row-highlighted").removeClass("row-highlighted");
		$(".cell-highlighted").removeClass("cell-highlighted");
		$(".column-highlighted").removeClass("column-highlighted");
		
		$(userrow).addClass("row-highlighted");
		
	});		
	
	$('.leaderboard-total').hover(function(){
		
		var thisuser = ($(this).attr('id').split('u'))[1];
		var userrow = ".rowu" + thisuser;
	
		$(".row-highlighted").removeClass("row-highlighted");
		$(".cell-highlighted").removeClass("cell-highlighted");
		$(".column-highlighted").removeClass("column-highlighted");
		
		$(userrow).addClass("row-highlighted");
	});
	
	
	//-----TABLE HOVER OUT
	$('.leaguetable').mouseleave(function(){
		
		$(".column-highlighted").removeClass("column-highlighted");
		$(".cell-highlighted").removeClass("cell-highlighted");
		$(".row-highlighted").removeClass("row-highlighted");
		
	});	
	
	//-----CLICK ACTIONS
	$('.leaderboard-result').click(function(){
		
		var thisevent = ($(this).attr('id').split('e'))[1];
		var thisuser = $(this).attr('id').split(/['u''e']+/)[1];
		
		var details = ".detu" + thisuser + "e" + thisevent;
				
		
		if($(this).hasClass("emptyresult")){
				
				//empty spot, click not allowed
				
		}else{
			
			if(selectedcell==1){
				
				//some other cell is already open
				
				if ( $(details).hasClass("teamscore-visible") ) {
					
					//user clicked on cell to collapse visible team scores
					$(".teamscore-visible").slideUp('fast');
					$(".teamscore-visible").addClass('teamscore-hidden');
					$(".teamscore-visible").removeClass('teamscore-visible');
					$(".result-clicked").removeClass('result-clicked');
				
					selectedcell = 0;
					
					
				}else{
					
					//click is to open new team score results while another result is open
					
					var userrow = ".rowu" + thisuser;
					var resultclass = ".resulte" + thisevent;
					var resulttitle = "#title" + thisevent;
					
					//transfer clicked class to new cell
					$('.result-clicked').removeClass('result-clicked');
					$(this).addClass('result-clicked');
					
					//close other results
					$(".teamscore-visible").slideUp('fast');
					$(".teamscore-visible").addClass('teamscore-hidden');
					$(".teamscore-visible").removeClass('teamscore-visible');
					
					//open new results
					$(details).slideDown('fast');
					$(details).removeClass('teamscore-hidden');
					$(details).addClass('teamscore-visible');
					
					//collapse previous cell and expand new cell
					$(".result-expanded").removeClass("result-expanded");
					$(".title-expanded").removeClass("title-expanded");
					
					$(userrow).addClass("row-highlighted");
					$(resultclass).addClass("result-expanded column-highlighted");
					$(resulttitle).addClass("title-expanded");
					
				}
					
				
				
			}else{
				
				//no cells currently open
				//open new results
				$(this).addClass('result-clicked');
				$(details).slideDown('fast');
				$(details).removeClass('teamscore-hidden');
				$(details).addClass('teamscore-visible');
				
				selectedcell = 1;
				
			}
			
			
		}
		
	});	
	
});


//SMALL SCREEN LEADERBOARD NAVIGATION
$( document ).ready(function() {
	
	$(".sm-lb-eventscontainer").hide();
	$(".sm-lb-surfers-row").hide();
	
	$(".sm-lb-row").click(function(){
		
		var eventselector = ".sm-events-u" + ($(this).attr('id').split('u'))[1];
		
		if ($(this).hasClass("sm-user-expanded")){
		
			$(eventselector).slideUp("fast");
			$(this).removeClass("sm-user-expanded");
			
			$(this).children(".sm-lb-expanduser").find(".closeduserrow").show("fast");
			$(this).children(".sm-lb-expanduser").find(".openeduserrow").hide("fast");
			
		}else{
			
			$(eventselector).slideDown("fast");
			$(this).addClass("sm-user-expanded");
			
			$(this).children(".sm-lb-expanduser").find(".closeduserrow").hide("fast");
			$(this).children(".sm-lb-expanduser").find(".openeduserrow").show("fast");
		}
		
	});	
	
	
	$(".sm-lb-event-row").click(function(){
		
		var data = $(this).attr('id').split(/['u''e']+/);
		var teamselector = ".sm-surfers-foru" + data[2] + "e" + data[3];
		
		if ($(this).hasClass("sm-team-expanded")){
			
			$(teamselector).slideUp("fast");
			$(this).removeClass("sm-team-expanded");
			
			$(this).children(".sm-lb-expandevent").find(".closedeventrow").show("fast");
			$(this).children(".sm-lb-expandevent").find(".openedeventrow").hide("fast");
			
		}else{
			
			$(teamselector).slideDown("fast");
			$(this).addClass("sm-team-expanded");
			
			$(this).children(".sm-lb-expandevent").find(".closedeventrow").hide("fast");
			$(this).children(".sm-lb-expandevent").find(".openedeventrow").show("fast");
			
		}
		
		
	});	
	
	
});	