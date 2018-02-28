$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = searchParams.get("eid"); //123
	
	$.ajax({
	  type: "POST",
	  data: {action:"getWaiversAndWildcards", eventid:eid},
	  url: "classes/fsWaiversHandler.php",
	  dataType: "json",
	  async: false,
	  success: function(data){

		$('.allrounds').html(data);				
		$(document).foundation();
						
	  }
	});
	
});