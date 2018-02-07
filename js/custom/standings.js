$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = searchParams.get("eid"); //123
	
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
	
});


$( document ).ready(function() {

	$('.leaderboard-result').hover(function(){				
		
		var thisevent = ($(this).attr('id').split('e'))[1];
		
		var resultclass = ".resulte" + thisevent;
		var resulttitle = "#title" + thisevent;
		
		$(".result-expanded").removeClass("result-expanded");
		$(resultclass).addClass("result-expanded");
		
		$(".title-expanded").removeClass("title-expanded");
		$(resulttitle).addClass("title-expanded");
		
		
//		$(this).addClass("result-expanded");
//		$(this).siblings(".result-expanded").removeClass("result-expanded");

	});

});