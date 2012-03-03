$(document).ready(function(){
    $("#toggleMapSize").click(function(event){
        event.preventDefault();
        var height = $("#mapCanvas").height();
        
		var header = 200;
		
        if(height==400) {
            $("#headerbar").hide(1000);
            $("#toolbar").hide(1000);
			$("#mapCanvas").height($(window).height()-header);
			$(".wrapper").animate({width:'98%'}, 1000);			
			$("#maininner").animate({width:'100%'}, 1000, function(){google.maps.event.trigger(map, 'resize');map.setZoom( map.getZoom() );resetBounds();});			
			
			$("#map-size").html("(Επαναφορά χάρτη)");

        }
        else {
			$("#mapCanvas").height(400);
            $("#headerbar").show(1000);
            $("#toolbar").show(1000);
			$(".wrapper").animate({width:'980px'}, 1000);
			$("#maininner").animate({width:'980px'}, 1000, function(){google.maps.event.trigger(map, 'resize');map.setZoom( map.getZoom() );resetBounds();});			
			
			$("#map-size").html("(Μεγάλος χάρτης)");
        }
		
		$("#content-info").height($("#wrapper-info").height()-75);
		$("#content-filters").height($("#wrapper-filters").height()-50);
    });
	
	$("#searchMarker").click(function(event){	
		markerSearch( $("#searchTextMarker").val() );
	});
	
	$("a[rel='colorbox']").colorbox();

	$(".info-close").click(function (event) 
	{ 
		event.preventDefault(); 
		$("#wrapper-info").hide(500);
		if(infoWindow != null)
			infoWindow.close();		
		if(infoBox != null)
			infoBox.close();				
		
	});	

	$(".filter-close").click(function (event) 
	{ 
		event.preventDefault(); 
		$("#wrapper-filters").hide(500);
	});	

	$(".filter-open").click(function (event) 
	{ 
		event.preventDefault(); 
		
		if( $('#wrapper-filters').is(':visible') ) {
			$("#wrapper-filters").hide(500);
		}
		else {
			$("#wrapper-filters").show(500);
		}		
	});	
	
	$("#content-info").height($("#wrapper-info").height()-75);
	$("#content-filters").height($("#wrapper-filters").height()-50);

});