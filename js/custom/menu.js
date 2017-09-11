$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = "eid" + searchParams.get("eid"); //123
	
	//get the event name using eid and place it in selected event
	var thisevent = $(".eventnav-expanded").children("#"+eid).text();
	$(".eventnav").children(".selected-event").children("h4").text(thisevent);
	$(".eventnav-expanded").children("#"+eid).addClass("selected-event");
	
});


$(".eventnav").click(function(){
	$(".eventnav-expanded").slideDown("fast");
	$(".eventnav").slideUp("fast");
});

$(".eventselect").click(function(){
	$(".eventnav-expanded").slideUp("fast");
	$(".eventnav").slideDown("fast");
	
	$(this).parent().children(".selected-event").removeClass("selected-event");
	$(this).addClass("selected-event");
	
	var eventid = "events.php?eid=" + ((this).id).substring(3);
	
	var thisevent = $(this).children("h4").text();
	
	$(".eventnav").children(".selected-event").children("h4").text(thisevent);
	
	//send to event page after 1.5 seconds (gives time for the menu to collapse)
	window.setTimeout(function(){window.location.href = eventid;}, 150);
	
});

$("#roundback").click(function(){
	var round = parseInt($("#roundback").parent().siblings(".selected-round").attr("id").slice(10)) -1;
	var nextround = round+1;
	var prevround = "#menu-round" + round;
	
	if(round!=0){
		
		$(".selected-round").removeClass("selected-round");
		$(prevround).addClass("selected-round");
		
		$('.allrounds').children('#r'+nextround).hide(); 
		$('.allrounds').children('#r'+round).show();
		
	}
	
});

$("#roundnext").click(function(){
	var round = parseInt($("#roundnext").parent().siblings(".selected-round").attr("id").slice(10))+1;
	var prevround = round-1;
	var nextround =  "#menu-round" + round;
	
	if(round!=9){
		$(".selected-round").removeClass("selected-round");
		$(nextround).addClass("selected-round");
		
		$('.allrounds').children('#r'+prevround).hide(); 
		$('.allrounds').children('#r'+round).show(); 
	}

});