$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = searchParams.get("eid"); //123
	var uid = searchParams.get("uid"); //123
	
	$.ajax({
	  type: "POST",
	  //data: "getUserTeam",
	  data: {action:"getUserTeam", userid:uid, eventid:eid},
	  url: "classes/fsTeamHandler.php",
	  dataType: "json",
	  async: false,
	  success: function(data){
				
		$('.navigation').html(data['nav']);
		//$('.eventmenu').html(data['menu']);
		//$('.allrounds').html(data['main']);
		$('.allrounds').html(data['team']);				
		$(document).foundation();
						
	  }
	});
	
});

$(".allrounds").on( "mouseover",".bestscore", function() {
	
	//$(".bestscorer").css("background-color","#ccc");

	
});

$(".allrounds").on( "mouseover",".bestavailscore", function() {
	
	$(".bestavailscorer").css("background-color","red");

	
});