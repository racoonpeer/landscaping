$(function() {
	
	var totalPanels			= $(".scrollContainer").children().size();
		
	var regWidth			= $(".panel").css("width");
	var regImgWidth			= $(".panel img").css("width");
	var regTitleSize		= $(".panel h2").css("font-size");
	var regParSize			= $(".panel p").css("font-size");
	
	var movingDistance	    = 116;
	
	var curWidth			= 116; //114
	var curImgWidth			= 104;
	var curTitleSize		= "20px";
	var curParSize			= "15px";

	var $panels				= $('#slider .scrollContainer > div');
	var $container			= $('#slider .scrollContainer');

	$panels.css({'float' : 'left','position' : 'relative'});
    
	$("#slider").data("currentlyMoving", false);

	$container
		.css('width', ($panels[0].offsetWidth * $panels.length) + 100 )
		.css('left', "-230px"); //"-300px"

	var scroll = $('#slider .scroll').css('overflow', 'hidden');
	$('.scrollContainer img').css({opacity: 0.3});
	
	function returnToNormal(element) {
		$(element)
			.animate({ width: regWidth })
			.find(".inside2")
			.addClass("inside")
			.removeClass("inside2")
			.end()
			.find("img")
			.animate({ width: regImgWidth, opacity: 0.3 })
		    .end()
			.find("h2")
			.animate({ fontSize: regTitleSize })
			.end()
			.find("p")
			.animate({ fontSize: regParSize });
	};
	
	function growBigger(element) {
		$(element)
			.animate({ width: curWidth })
			.find(".inside")
			.addClass("inside2")
			.removeClass("inside")
			.end()
			.find("img")
			.animate({ width: curImgWidth, opacity: 1 })
		    .end()
			.find("h2")
			.animate({ fontSize: curTitleSize })
			.end()
			.find("p")
			.animate({ fontSize: curParSize });
	}
	
	//direction true = right, false = left
	function change(direction) {
	   
	    //if not at the first or last panel
		var allowShow = true;
		//if((direction && !(curPanel < totalPanels)) || (!direction && (curPanel <= 1))) { return false; }	
		if((direction && !(curPanel < totalPanels-1)) || (!direction && (curPanel <= 2))) { 
			allowShow = false; /*return false;*/ 
		}	
		
		//alert('direction='+direction+' curPanel='+curPanel);
		if(direction && (curPanel > totalPanels-3)) $("#btnRight").hide();
		if(!direction && (curPanel <= 3)) $("#btnLeft").hide();
			
		
		if((direction && (curPanel == totalPanels)) || (!direction &&(curPanel == 1 ))) {return false;}
		if((!direction && (curPanel == totalPanels)) || (direction && (curPanel == 1))) allowShow = false;
		
		$("#slider").data("currentlyMoving", false);
		
        //if not currently moving
        if (($("#slider").data("currentlyMoving") == false)) {
            
			$("#slider").data("currentlyMoving", true);
			
			
			var next         = direction ? curPanel + 1 : curPanel - 1;
			var leftValue    = $(".scrollContainer").css("left");
			var movement	 = direction ? parseFloat(leftValue, 10) - movingDistance : parseFloat(leftValue, 10) + movingDistance;

			if(allowShow) {
			if(curPanel!=3) $("#btnLeft").show();
			if((totalPanels-1)<=curPanel) $("#btnRight").show();
			
			$(".scrollContainer")
				.stop()
				.animate({
					"left": movement
				}, function() {
					$("#slider").data("currentlyMoving", false);
				});
			} 
			
			returnToNormal("#panel_"+curPanel);
			growBigger("#panel_"+next);
			
			curPanel = next;
			
			//setBigImage("#panel_"+curPanel+" img"); //added by me
			
			//remove all previous bound functions
			$("#panel_"+(curPanel+1)).unbind();	
			
			//go forward
			$("#panel_"+(curPanel+1)).click( 
					function(){   setBigImage("#panel_"+(curPanel+1)+" img");  change(true); 
			});
			
            //remove all previous bound functions															
			$("#panel_"+(curPanel-1)).unbind();
			
			//go back
			$("#panel_"+(curPanel-1)).click(function(){	setBigImage("#panel_"+(curPanel-1)+" img");  change(false); }); 
			
			//remove all previous bound functions
			$("#panel_"+curPanel).unbind();
		}
	}
	
	// Set up "Current" panel and next and prev
	growBigger("#panel_4");	
	var curPanel = 4;
	
	$("#panel_"+(curPanel+1)).click(function(){ setBigImage("#panel_"+(curPanel+1)+" img");  change(true); });
	$("#panel_"+(curPanel-1)).click(function(){ setBigImage("#panel_"+(curPanel-1)+" img");  change(false); });
	
	//when the left/right arrows are clicked
	$(".right").click(function(){ change(true); });	
	$(".left").click(function(){  change(false); });
	
	$(window).keydown(function(event){
	  switch (event.keyCode) {
			case 13: //enter
				$(".right").click();
				break;
			case 32: //space
				$(".right").click();
				break;
	    case 37: //left arrow
				$(".left").click();
				break;
			case 39: //right arrow
				$(".right").click();
				break;
	  }
	});
		
	//$(".scrollContainer .panel").click(function(){ alert('zxd');	/*setBigImage("#panel_"+curPanel+" img"); */  alert($(this).attr("id"));  }); 
});
function setBigImage(n) {
	var path = $(n).attr("src");
	path = path.replace(/small_/g, "big_");
	$("#big_image").attr("src",path);	
}
