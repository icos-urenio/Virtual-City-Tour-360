<?php
/**
 * @version     2.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Virtualcitytour360 component
 */
class Virtualcitytour360ViewVirtualcitytour360 extends JView
{
	protected $state;
	protected $items;
	protected $categories;
	protected $params;
	protected $pageclass_sfx;	
	protected $customMarkers = '';
	protected $filters = '';
	
	
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$this->params		= $app->getParams();
		
		//remove || from title
		$strip_title = $this->params->get('page_title');
		$strip_title = str_replace('||', '', $strip_title);
		$this->params->set('page_title', $strip_title);
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		// Get some data from the models
		$this->state	= $this->get('State');
		$this->items	= $this->get('Items');
		$this->categories = $this->get('Categories');
		
		$this->createFilters($this->categories);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
        parent::display($tpl);
		
		// Set the document
		$this->setDocument();
	}
	
	protected function createFilters($cats = array())
	{
		$this->filters .= '<ul>';
		
		foreach($cats as $JCatNode){
			//name is the parent id
			//id is the category id
			
			//$this->filters .='<li><input path="'.$JCatNode->path.'" name="box'.$JCatNode->parentid.'" type="checkbox" checked="checked" id="box'.$JCatNode->id.'" onclick="boxclick2(this,'.$JCatNode->id.',\''.$JCatNode->parentid.'\')" />'.$JCatNode->title.'</li>' . "\n";
			$this->filters .='<li><input path="'.$JCatNode->path.'" name="box" type="checkbox" checked="checked" id="box'.$JCatNode->id.'" onclick="boxclick2(this,'.$JCatNode->id.')" />'.$JCatNode->title.'</li>' . "\n";
			if(!empty($JCatNode->children))
				$this->createFilters($JCatNode->children);
		
		}
		$this->filters .= '</ul>';
		return false;
	}
	

	protected function createCustomMarkers($cats = array())
    {
        if(is_array($cats))
        {
			
            $i = 0;
            $return = array();
            foreach($cats as $JCatNode)
            {
                $return[$i]->title = $JCatNode->title;
                $return[$i]->id = $JCatNode->id;

				$return[$i]->image = $JCatNode->image;

				if(!empty($return[$i]->image)){
					$this->customMarkers .= $return[$i]->id . ": {icon: '".JURI::root(true).'/'.$return[$i]->image."', shadow: '".JURI::root(true).'/images/markers/shadow.png'."' }," . "\n";
				}

				if(!empty($JCatNode->children))
                    $return[$i]->children = $this->createCustomMarkers($JCatNode->children);
                else
                    $return[$i]->children = false;
                $i++;
            }
            return $return;
        }
        return false;
    }	
	
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/js/colorbox/css/colorbox.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/css/virtualcitytour360.css');
		
		//add jquery scripts
		$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/jquery-1.5.2.min.js');
		$document->addScript(JURI::root(true) . "/components/com_virtualcitytour360/js/colorbox/jquery.colorbox-min.js");
		$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/virtualcitytour360.js');	
		
		//add google maps
		$document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=en&region=GB");
		$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/infobox_packed.js');

		$LAT = ''; //todo get point from component's parameter
		$LON = '';
		if($LAT == '' || $LON == ''){
			$LAT = '40.54629751976399';
			$LON = '23.01861169311519';
		}
		
		//prepare custom icons according (get images from virtualcitytour360 categories)
		
		$this->createCustomMarkers($this->categories);
		$this->customMarkers = substr($this->customMarkers, 0, -2);	//remove /n and comma
		
		$googleMapInit = "
			var geocoder = new google.maps.Geocoder();
			var map = null;
			var gmarkers = [];
			
			function zoomIn() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()+1);
			}

			function zoomOut() {
				map.setCenter(marker.getPosition());
				map.setZoom(map.getZoom()-1);
			}
			
			var customIcons = {
			  ".$this->customMarkers."
			};
			
			// Creating a LatLngBounds object
			var bounds = new google.maps.LatLngBounds();			
			var infoWindow = null;
			var infoBox = null;

			function initialize() {
				var LAT = ".$LAT.";
				var LON = ".$LON.";

				var latLng = new google.maps.LatLng(LAT, LON);
				map = new google.maps.Map(document.getElementById('mapCanvas'), {
				zoom: 11,
				center: latLng,
				panControl: false,
				streetViewControl: false,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.SMALL
				},
				mapTypeId: google.maps.MapTypeId.ROADMAP
				});

				
				
				
				infoWindow = new google.maps.InfoWindow;
				
				var infoBoxOptions = {
					disableAutoPan: false
					,maxWidth: 0
					,pixelOffset: new google.maps.Size(-100, 0)
					,zIndex: null
					,boxStyle: { 
					  background: \"url(" . JURI::base().'components/com_virtualcitytour360/images/tipbox.gif' . ") -40px 0 no-repeat\"
					  ,opacity: 0.75
					  ,width: \"200px\"
					 }
					,closeBoxMargin: \"10px 2px 2px 2px\"
					,closeBoxURL: \"http://www.google.com/intl/en_us/mapfiles/close.gif\"
					,infoBoxClearance: new google.maps.Size(1, 1)
					,isHidden: false
					,pane: \"floatPane\"
					,enableEventPropagation: false
				};
				infoBox = new InfoBox(infoBoxOptions);



				
				var URL = 'index.php?option=com_virtualcitytour360&view=virtualcitytour360&task=getMarkersAsXML&format=raw&".JUtility::getToken()."=1';
				// Change this depending on the name of your PHP file
				downloadUrl(URL, function(data) {
					var xml = data.responseXML;
					var markers = xml.documentElement.getElementsByTagName('marker');
					for (var i = 0; i < markers.length; i++) {
						var name = markers[i].getAttribute('name');
						var description = markers[i].getAttribute('description');
						var catid = markers[i].getAttribute('catid');
						var id = markers[i].getAttribute('id');
						var photos = markers[i].getAttribute('photos');
						var panoramas = markers[i].getAttribute('panoramas');

						var point = new google.maps.LatLng(
							parseFloat(markers[i].getAttribute('lat')),
							parseFloat(markers[i].getAttribute('lng'))
						);


						var html = '<strong>' + name + '</strong>';
						var icon = customIcons[catid] || {};
						var marker = new google.maps.Marker({
							map: map,
							position: point,
							title: name,
							icon: icon.icon,
							shadow: icon.shadow
						});
						
						marker.catid = catid;
						marker.id = id;
						marker.photos = photos;
						marker.panoramas = panoramas;
						marker.description = description;
						
						//bindInfoWindow(marker, map, infoWindow, html);
						bindInfoBox(marker, map, infoBox, html);
						gmarkers.push(marker);
					}
					
					$(\"#loading\").hide();
					
				});
				
				// google.maps.event.addListenerOnce(map, 'idle', function(){
					// $(\"#loading\").hide();
				// });
			}
			
			/*
			function bindInfoWindow(marker, map, infoWindow, html) {
			  google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
				map.panTo(marker.getPosition());
				showInfo(marker);
			  });
			  
			  google.maps.event.addListener(marker, 'mouseover', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
				//map.panTo(marker.getPosition());
				//showInfo(marker);
			  });			  
			}
			*/
			
			
			//alternative to infoWindow is the infoBox
			function bindInfoBox(marker, map, infoWindow, html) {
			
				var boxText = document.createElement(\"div\");
				boxText.style.cssText = \"border: 1px solid black; margin-top: 8px; background-color: yellow; padding: 5px;\";
				boxText.innerHTML = html;			
		
				google.maps.event.addListener(marker, 'click', function() {
					infoBox.setContent(boxText);
					infoBox.open(map, marker);
					map.panTo(marker.getPosition());
					showInfo(marker);
				});
			  
				google.maps.event.addListener(marker, 'mouseover', function() {
					infoBox.setContent(boxText);
					infoBox.open(map, marker);
				});			  
			}
			
			
			
			

			function downloadUrl(url, callback) {
			  var request = window.ActiveXObject ?
				  new ActiveXObject('Microsoft.XMLHTTP') :
				  new XMLHttpRequest;

			  request.onreadystatechange = function() {
				if (request.readyState == 4) {
				  request.onreadystatechange = doNothing;
				  callback(request, request.status);
				  resetBounds();
				}
			  };

			  request.open('GET', url, true);
			  request.send(null);
			}

			function doNothing() {}
			
			function resetBounds() {
				var a = 0;
				bounds = null;
				bounds = new google.maps.LatLngBounds();
				for (var i=0; i<gmarkers.length; i++) {
					if(gmarkers[i].getVisible()){
						a++;
						bounds.extend(gmarkers[i].position);	
					}
				}
				if(a > 0){
					map.fitBounds(bounds);
					var listener = google.maps.event.addListener(map, 'idle', function() { 
					  if (map.getZoom() > 16) map.setZoom(16); 
					  google.maps.event.removeListener(listener); 
					});
				}
			}
			
			//show markers according to filtering
			function show(category) {
				for (var i=0; i<gmarkers.length; i++) {
					if (gmarkers[i].catid == category) {
						gmarkers[i].setVisible(true);
					}
				}
				// == check the checkbox ==
				document.getElementById('box'+category).checked = true;
				resetBounds();
			}			
			function hide(category) {
				for (var i=0; i<gmarkers.length; i++) {
					if (gmarkers[i].catid == category) {
						gmarkers[i].setVisible(false);
					}
				}
				// == clear the checkbox ==
				document.getElementById('box'+category).checked = false;
				if(infoWindow != null)
					infoWindow.close();
				
				if(infoBox != null)
					infoBox.close();				
				
				$(\"#markerInfo\").html('');
				$(\"#panorama\").html('');
				
				$(\"#wrapper-info\").hide(500);
				
				resetBounds();
			}
			
			//--- recursively get tree
			function boxclick(box, category, parent) {
				if (box.checked) {
					show(category);
				} else {
					hide(category);	
				}
				
				var arr = new Array();
				arr = document.getElementsByName('box'+category);

				for(var i = 0; i < arr.length; i++)
				{
					var obj = document.getElementsByName('box'+category).item(i);
					var c = obj.id.substr(3, obj.id.length);
					var p = obj.name.substr(3, obj.name.length);

					if (box.checked) {
						obj.checked = true;
					} else {
						obj.checked = false;
					}
					boxclick(obj, c, p);
					
				}
				
				// == rebuild the side bar
				makeSidebar();
				return false;
			}			
			
			//--- non recursive since IE cannot handle it (doh!!)
			function boxclick2(box, category) {
				if (box.checked) {
					show(category);
				} else {
					hide(category);	
				}
				
				var com = box.getAttribute('path');				
				var arr = new Array();
				arr = document.getElementsByName('box');
				
				for(var i = 0; i < arr.length; i++)
				{
					var obj = document.getElementsByName('box').item(i);
					var c = obj.id.substr(3, obj.id.length);
					
					var path = obj.getAttribute('path');
					if(com == path.substring(0,com.length)){
						if (box.checked) {
							obj.checked = true;
							show(c);
						} else {
							obj.checked = false;
							hide(c);
						}
					}
				}
				
				// == rebuild the side bar
				makeSidebar();
				return false;
			}			
			
			function markerSearch(text){
				if(text == ''){
					alert('Γράψτε μια λέξη προς αναζήτηση σημείου ενδιαφέροντος');
					return;
				}
				

				var index = -1;
				for (var i=0; i<gmarkers.length; i++) {				
					var a1 = gmarkers[i].title.toUpperCase();
					var a2 = gmarkers[i].description.toUpperCase();
					
					var b = text.toUpperCase();
					if(a1.contains(b) || a2.contains(b)){
						if(gmarkers[i].getVisible()){
							index = i;
							break;
						}
					}
				}
				
				
				
				if(index == -1)
					alert('Δε βρέθηκε σημείο ενδιαφέροντος')
				else
					google.maps.event.trigger(gmarkers[index],'click');
			}
			
			function markerclick(i) {
				google.maps.event.trigger(gmarkers[i],'click');
			}			

			function markerclick2(id) {
				var index;
				for (var i=0; i<gmarkers.length; i++) {				
					if(gmarkers[i].id == id){
						index = i;
					}
				}
				google.maps.event.trigger(gmarkers[index],'click');
			}			

			// == rebuilds the sidebar to match the markers currently displayed ==
			function makeSidebar() {
				var html = '<ul>';
				for (var i=0; i<gmarkers.length; i++) {
					if (gmarkers[i].getVisible()) {
						html += '<li><a href=\"javascript:markerclick(' + i + ');\">' + gmarkers[i].title + '<\/a><\/li>';
					}
				}
				html += '<\/ul>';
				document.getElementById('infobar').innerHTML = html;
			}

			function showInfo(marker){
				$(\"#markerTitle\").html('<h2>' + marker.title + '</h2>');
				$(\"#panorama\").html('');
				$(\"#markerInfo\").html('');
				

				html = '';

				if(marker.panoramas != ''){
					//html += '<h3>Πανοράματα</h3>';
					html += createInfoPanoramas(marker);
				}
				
				if(marker.photos != ''){
					//html += '<h3>Φωτογραφίες</h3>';
					html += '<br /><br />';
					html += createInfoImages(marker);
				}
				
				
				html += '<p>' + marker.description + '</p>';

				$(\"#markerInfo\").html(html);
				
				if(marker.photos != ''){
					$(\"a[rel='photos']\").colorbox();	
				}
				
				if(marker.panoramas != ''){
					$(\"#panorama\").show();
				}
				else{
					$(\"#panorama\").hide();
				}					
				
				$(\"#wrapper-info\").show(500);
			}
			
			function createInfoImages(marker){
				var arr = marker.photos.split(';');
				var html = '';
				for(i = 0; i < arr.length; i++){
					if(arr[i] != ''){
						var thumb = '" . JURI::root(true). "/images/virtualcitytour360/". "' + marker.id + '/images/thumbs/' + arr[i];
						var img = '" . JURI::root(true). "/images/virtualcitytour360/". "' + marker.id + '/images/' + arr[i];
						html += '<a title=\"'+marker.title+'\" rel=\"photos\" href=\"'+img+'\">';
						html +=  '<img src=\"' ;
						html +=	thumb;
						html += '\" />';
						html += '</a>';
					}
				}				
				return html;
			}
			
			function createInfoPanoramas(marker){
				var arr = marker.panoramas.split(';');
				var html = '';

				
				for(i = 0; i < arr.length; i++){
					if(arr[i] != ''){
						var pan = '" . JURI::root(true). "/images/virtualcitytour360/". "' + marker.id + '/panoramas/original/' + arr[i];
						if(i == 0){
							embedFlash(pan);
							if(arr.length == 2){
								return html;
							}							
							html += '<ul>';
						}					

						html += '<li><a href=\"javascript:void(0);\" onclick=\"embedFlash(\''+pan+'\')\"><span class=\"tab\">' + (i+1) + '</span></a></li>';
						if(i == arr.length-1){
							html += '</ul>';	
						}
					}
				}

				return html;
			}
			
			function embedFlash(pan){
				var flash = '<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0\" id=\"RyubinPanorama\" >';
				flash += '<param name=\"wmode\" value=\"transparent\"> ';
				flash +='<embed src=\"components/com_virtualcitytour360/pano/RyubinPanoPlayer5.swf\" wmode=\"transparent\" FlashVars=\"playmode=sphere&internal_ctrl=no&img_path='+pan+'&cursor_path=components/com_virtualcitytour360/pano/my_cursor.png&xml_path=components/com_virtualcitytour360/pano/panosettings.xml\" width=\"100%\" height=\"100%\" name=\"RyubinPanorama\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />';
				flash += '</object>';
				$(\"#panorama\").html(flash);
			}
		
			
			// Onload handler to fire off the app.
			google.maps.event.addDomListener(window, 'load', initialize);
			
			
		";

		//add the javascript to the head of the html document
		$document->addScriptDeclaration($googleMapInit);
		

	}
}