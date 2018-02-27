$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = searchParams.get("eid"); //123
	
	$.ajax({
	  type: "POST",
	  //data: "getUserTeam",
	  data: {action:"getUserTeam", eventid:eid},
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


//FOR LIVE EVENTS
$( document ).ready(function() {
	
	//hide all matches showing surfers next rivals or who surfer lost against
	$(".surfermatch").hide();
	
	$(".livelost").click(function(){
		
		var thissurfer = ($(this).attr('id').split('is-'))[1];
		var thisexpand = ".for-" + thissurfer;
		var thismatch = ".match-" + thissurfer;
		
		$(thismatch).slideDown("fast");
		$(thisexpand).children(".closeduserrow").hide("fast");
		$(thisexpand).children(".openeduserrow").show("fast");
			
	});
	
});