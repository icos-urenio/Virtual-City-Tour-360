$(document).ready(function(){
	
	jVct("a[rel='colorbox']").colorbox();	
	
	jVct("#toggleMapSize").click(function(event){
        event.preventDefault();
        var height = jVct("#mapCanvas").height();
        
		var header = 200;
		
        if(height==400) {
        	jVct("#headerbar").hide(1000);
        	jVct("#toolbar").hide(1000);
        	jVct("#mapCanvas").height($(window).height()-header);
        	jVct(".wrapper").animate({width:'98%'}, 1000);			
        	jVct("#maininner").animate({width:'100%'}, 1000, function(){google.maps.event.trigger(map, 'resize');map.setZoom( map.getZoom() );resetBounds();});			
			
        	jVct("#map-size").html("(Επαναφορά χάρτη)");

        }
        else {
        	jVct("#mapCanvas").height(400);
        	jVct("#headerbar").show(1000);
        	jVct("#toolbar").show(1000);
        	jVct(".wrapper").animate({width:'980px'}, 1000);
        	jVct("#maininner").animate({width:'980px'}, 1000, function(){google.maps.event.trigger(map, 'resize');map.setZoom( map.getZoom() );resetBounds();});			
			
        	jVct("#map-size").html("(Μεγάλος χάρτης)");
        }
		
        jVct("#content-info").height(jVct("#wrapper-info").height()-75);
        jVct("#content-filters").height(jVct("#wrapper-filters").height()-50);
    });
	
	jVct("#searchMarker").click(function(event){	
		markerSearch( jVct("#searchTextMarker").val() );
	});
	
	

	jVct(".info-close").click(function (event) 
	{ 
		event.preventDefault(); 
		jVct("#wrapper-info").hide(500);
		if(infoWindow != null)
			infoWindow.close();		
		if(infoBox != null)
			infoBox.close();				
		
	});	
 
	jVct(".filter-close").click(function (event) 
	{ 
		event.preventDefault(); 
		jVct("#wrapper-filters").hide(500);
	});	

	jVct(".filter-open").click(function (event) 
	{ 
		event.preventDefault(); 
		
		if( jVct('#wrapper-filters').is(':visible') ) {
			jVct("#wrapper-filters").hide(500);
		}
		else {
			jVct("#wrapper-filters").show(500);
		}		
	});	
	
	jVct("#content-info").height(jVct("#wrapper-info").height()-75);
	jVct("#content-filters").height(jVct("#wrapper-filters").height()-50);

	
	
	
});