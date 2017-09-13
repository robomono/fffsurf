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
			
			//if(data=="setnameandteam"){
				//$('.maincontent').load('views/userteamform.html');
				//}else{
				
				$('.eventmenu').html(data['menu']);
				$('.allrounds').html(data['main']);
				$(document).foundation();
				//$('.isHidden').hide();
				//}
						
		}
	});
	
	$('.allrounds').children('#r1').show();
	
});