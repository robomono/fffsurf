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


//------------------FOR TEAM EDIT------------------//
$( document ).ready(function() {
	
	$(".savechanges").hide();
	$(".cancelchanges").hide();
	$(".step2").hide();
	
	$( "#sortable1, #sortable2" ).sortable({
		connectWith: ".connectedSortable",
		axis:"y",
		handle: ".icon-move",
		cursor: "pointer",
		placeholder: "ui-state-highlight",
		opacity: 0.9,
		
		start: function(event,ui){
			ui.item.startPos = ui.item.index();
			ui.item.startPar = ui.item.parent().attr("id");
			if(ui.item.hasClass("movable-wc")){ui.item.isWc=1;}else{ui.item.isWc=0};//if moving a wc
			if($("#sortable1 > li.movable-surfer:last").hasClass("movable-wc")){ui.item.wcLast=1;}else{ui.item.wcLast=0;}//if a wc is last in active
		},
		
		change: function(event,ui){
			
			if($(ui.item).parent().attr("id") == "sortable1" && ui.placeholder.parent().attr("id") == "sortable2"){
				//moving an item from active to bench
				if(ui.placeholder.index()!=0 && ui.item.isWc!=1){
					//change colors if its being dropped below 0 (0 is technically still active)
					$(ui.item).children(".teamsurfer").addClass("isbench");
					$("#sortable2 > li.movable-surfer:eq(0)").children(".teamsurfer").removeClass("isbench").addClass("isactive");;
					ui.item.classChange = 1;
				}else if(ui.item.isWc==1){
					//if moving a wildcard to bench change to forbidden move
					$(ui.item).children(".teamsurfer").addClass("forbiddenmove");
					ui.item.wcChange = 1;
				}
				
			}

			else if($(ui.item).parent().attr("id") == "sortable2" && ui.placeholder.parent().attr("id") == "sortable1"){
				//moving an item from bench to active
				if(ui.placeholder.index()!=6 && !$("#sortable1 > li.movable-surfer:last").hasClass("movable-wc")){
					//moving to position below 8 and a wc isnt last on active
					$(ui.item).children(".teamsurfer").removeClass("isbench").addClass("isactive");
					$("#sortable1 > li.movable-surfer:last").children(".teamsurfer").addClass("isbench");
					ui.item.classChange = 1;
				}else if(ui.placeholder.index()<6 && $("#sortable1 > li.movable-surfer:last").hasClass("movable-wc")){
					//moving to a position below 8 and wc is last on active
					$("#sortable1 > li.movable-surfer:last").children(".teamsurfer").addClass("forbiddenmove");
					ui.item.wcChange = 2;
				}else if(ui.placeholder.index()==6 && ui.item.wcChange==2){
					$("#sortable1 > li.movable-surfer:eq(5)").children(".teamsurfer").removeClass("forbiddenmove");
				}
			}
		
			else if($(ui.item).parent().attr("id") == "sortable1" && ui.placeholder.parent().attr("id") == "sortable1"){
				//moving an item from active to active
				if(ui.item.classChange==1){
					//revert classes
					$(ui.item).children(".teamsurfer").removeClass("isbench");
					$("#sortable2 > li.movable-surfer:eq(0)").children(".teamsurfer").addClass("isbench");
					ui.item.classChange = 0;
				}else if(ui.item.wcChange==1){
					//revert class for wc
					$(ui.item).children(".teamsurfer").removeClass("forbiddenmove");
					ui.item.wcChange = 0;
				}
			}
		
			else if($(ui.item).parent().attr("id") == "sortable2" && ui.placeholder.parent().attr("id") == "sortable2"){
				//moving an item from bench to bench
				if(ui.item.classChange==1){
					//revert classes
					$(ui.item).children(".teamsurfer").addClass("isbench");
					$("#sortable1 > li.movable-surfer:last").children(".teamsurfer").removeClass("isbench");
					ui.item.classChange = 0;
				}else if(ui.item.wcChange==1){
					//revert class for wc
					$("#sortable1 > li.movable-surfer:last").children(".teamsurfer").removeClass("forbiddenmove");
					ui.item.wcChange = 0;
				}
			}
			
			//loophole moves (for class changed on bottom of active and on top of bench)
			
			 if(ui.item.classChange==1 
				 			&& ui.item.startPar=="sortable2"
							&& ui.placeholder.parent().attr("id")=="sortable1" 
							&& ui.placeholder.index()==6){
								//class revert when adding bench surfer to bottom of active (so, back to bench)
								$(ui.item).children(".teamsurfer").addClass("isbench");
								$("#sortable1 > li.movable-surfer:eq(5)").children(".teamsurfer").removeClass("isbench");
								ui.item.classChange = 0;
			}
			else if(ui.item.classChange==1 
							&& ui.item.startPar=="sortable1"
							&& ui.placeholder.parent().attr("id")=="sortable2" 
							&& ui.placeholder.index()==0){
								//class rever when moving from active to top of bench (so still active)
								$(ui.item).children(".teamsurfer").removeClass("isbench");
								$("#sortable2 > li.movable-surfer:eq(0)").children(".teamsurfer").addClass("isbench");
								ui.item.classChange = 0;	
			}
			
			$(".savechanges").show("fast");
			$(".cancelchanges").show("fast");
			
		},
		
	  beforeStop: function(evt,ui){
	 
		 if(ui.item.isWc==1 && ui.placeholder.parent().attr("id") == "sortable2"){
		 	//moving a wc to bench
			 if(ui.item.wcChange == 1){
			 	$(ui.item).children(".teamsurfer").removeClass("forbiddenmove")
			 } 
			
			$(this).sortable('cancel');
			
		 }
		 
		 else if(ui.item.startPar=="sortable2" && ui.placeholder.parent().attr("id") == "sortable1" && ui.item.wcLast==1){
		 	//moving bench to active when wc is last active
			 if(ui.item.wcChange == 2){
				$("#sortable1 > li.movable-wc:last").children(".teamsurfer").removeClass("forbiddenmove");
			 } 
			$(this).sortable('cancel');
		 }
	 
	  },
		
		stop: function( event, ui ) {
			ui.item.endPos = ui.item.index();
			ui.item.endPar = ui.item.parent().attr("id");
			
			
			if(ui.item.startPar=="sortable1" && ui.item.endPar=="sortable2" && ui.item.endPos==0){
				//from active to first bench spot - so still bench
				$('#sortable1').append(ui.item);//move item to top of bench
			}
			
			else if(ui.item.startPar=="sortable2" && ui.item.endPar=="sortable1" && ui.item.endPos==6){
				//from bench to last active spot - so still bench
				$('#sortable2').prepend(ui.item);//move item to bottom of active
			}
			
			else if(ui.item.startPar=="sortable1" && ui.item.endPar=="sortable2" && ui.item.endPos!=0){
				//valid move from active to bench
				$('#sortable1').append($("#sortable2 > li.movable-surfer:eq(0)"));
				
			}
			
			else if(ui.item.startPar=="sortable2" && ui.item.endPar=="sortable1" && ui.item.endPos!=6){
				//valid move from bench to active
				$('#sortable2').prepend($("#sortable1 > li.movable-surfer:last"));
			}
		
		
			$(".step1").hide("fast");
			$(".step2").show("fast");
			
		}
		
	}).disableSelection();
	
	//-----------SAVE / REVERT BUTTON ACTIONS ------//
	
	$(".cancelchanges").click(function(){
		location.reload();
	});
	
	$(".savechanges").click(function(){
		
		var searchParams = new URLSearchParams(window.location.search); //?anything=123
		var eid = searchParams.get("eid"); //123
		
		var idArray = [];
		$('.movable-surfer').each(function () {
		    idArray.push(this.id);
		});
		
		var allids = idArray.toString();
		
		$.ajax({
		  type: "POST",
		  data: {action:"updateTeamChanges", eventid:eid, allids:allids},
		  url: "classes/fsTeamHandler.php",
		  dataType: "json",
		  async: false,
		  success: function(data){
				if(data === "success"){
					location.reload();
				}
		  }
		});
		
	});
	
});