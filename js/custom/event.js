$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = searchParams.get("eid"); //123
	
	$.ajax({
	  type: "POST",
	  //data: "getEventRounds",
	  data: {action:"getEventRounds", eventid:eid},
	  url: "classes/fsEventHandler.php",
	  dataType: "json",
	  async: false,
	  success: function(data){
				
		$('.navigation').html(data['nav']);
		$('.eventmenu').html(data['menu']);
		$('.allrounds').html(data['main']);
		//$('.allrounds').html(data);				
		$(document).foundation();
						
	  }
	});
	
	$('.allrounds').children('#r1').show();
	
});