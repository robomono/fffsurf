$( document ).ready(function() {
	
	//get the url parameter for event id and string it - e.g. "eid1" - THIS COULD CONFLICT WITH URL REWRITE
	var searchParams = new URLSearchParams(window.location.search); //?anything=123
	var eid = "eid" + searchParams.get("eid"); //123
	
	//get the event name using eid and place it in selected event
	var thisevent = $(".eventnav-expanded").children("#"+eid).text();
	$(".eventnav").children(".selected-event").children("h4").text(thisevent);
	$(".eventnav-expanded").children("#"+eid).addClass("selected-event");
	
});

/*-----EVENT NAVIGATION-----*/

$(".eventnav").click(function(){
	$(".eventnav-expanded").slideDown("fast");
	$(".eventnav").slideUp("fast");
});


$(".eventselect").click(function(){
	
	$(".eventnav-expanded").slideUp("fast");
	$(".eventnav").slideDown("fast");
	
	var searchParams = new URLSearchParams(window.location.search); //gets all the php search parameters e.g. ?id=3
	var currentLocation = window.location.pathname;//e.g. teams.php or events.php
	
	var eid = this.id.substring(3);//gets the event id from div id
	
	$(this).parent().children(".selected-event").removeClass("selected-event");
	$(this).addClass("selected-event");
	
	var thisevent = $(this).children("h4").text();
	$(".eventnav").children(".selected-event").children("h4").text(thisevent);
	
	if (currentLocation == "/fffsurf/events.php"){var redirect = currentLocation + "?eid=" + eid;}
	else if (currentLocation == "/fffsurf/teams.php"){var redirect = currentLocation + "?eid=" + eid;}
	else if (currentLocation == "/fffsurf/standings.php"){var redirect = currentLocation + "?eid=" + eid;}
	
	//send to event page after 1.5 seconds (gives time for the menu to collapse)
	window.setTimeout(function(){window.location.href = redirect;}, 150);
	
});

/*-----ROUND NAVIGATION-----*/
$(".navigation").on( "click","#roundback", function() {
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

$(".navigation").on( "click","#roundnext", function() {
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

/*-----FILTER MENU-----*/

$(".eventmenu").on( "click",".heat-filter-select", function() {
	$(".highlighted").removeClass("highlighted");
	
	var uname = $(this).html();
	
	var uid = $(this).attr("id").slice(6);
	var hasheat = ".has-" + uid;
	var notheat = ":not(." + "has-" + uid +")";
	var pick = "." + "is-" + uid;
	
	if(uid=="all"){
		
		$(".eventrounddetails").slideDown("fast");
		$('.emptypicks').hide();
		
		$("#selectedfilter").html("Showing: All");
		
	}else{
			
			$(".roundcontainer").children(hasheat).slideDown("fast");
			$(".roundcontainer").children(notheat).slideUp("fast");
			$(pick).parents(".eventheatrow").addClass("highlighted");
			
			$("#selectedfilter").html("Showing: " + uname);
	}
});

$(".eventmenu").on( "click",".filter-menu", function() {
	$(".heat-filter-select").slideToggle("fast");
});