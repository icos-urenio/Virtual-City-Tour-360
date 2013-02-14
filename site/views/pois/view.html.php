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
 * HTML View class for the virtualcitytour360 component
 */
class Virtualcitytour360ViewPois extends JView
{
	protected $state;
	protected $items;
	protected $categories;
	protected $pagination;
	protected $params;
	protected $pageclass_sfx;
	protected $customMarkers = '';
	protected $filters = '';
	protected $markers = '';
	protected $statusFilters = '';
	protected $getLimitBox = '';
	protected $language = '';
	protected $region = '';
	protected $lat = '';
	protected $lon = '';
	protected $searchterm = '';
	protected $zoom;
	protected $loadjquery;
	protected $loadbootstrap;
	protected $loadbootstrapcss;
	protected $credits;
	protected $arCat;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$this->params		= $app->getParams();
		
		//remove || from title
		$strip_title = $this->params->get('page_title');
		$strip_title = str_replace('||', '', $strip_title);
		$this->params->set('page_title', $strip_title);
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		$this->state = $this->get('State');		
		
		// Get some data from the models
		$this->items	= $this->get('Items');
		//echo json_encode($this->items);
		$this->categories = $this->get('Categories');
		$this->pagination	= $this->get('Pagination');
		$this->arCat = $this->createFiltersAsArray($this->categories);
		$this->createFilters($this->categories);				
		$this->getLimitBox = $this->createLimitBox();

		//merge params
		$this->params	= $this->state->get('params');


		/*
		This method only for menu parameters namely: views/issues/tmpl/default.xml
		$lang = JRequest::getVar('maplanguage');
		$region = JRequest::getVar('mapregion');
		$lat = JRequest::getFloat('latitude');
		$lon = JRequest::getFloat('longitude');
		$term = JRequest::getVar('searchterm');
		*/
		
		$lang = $this->params->get('maplanguage');
		$region = $this->params->get('mapregion');
		$lat = $this->params->get('latitude');
		$lon = $this->params->get('longitude');
		$term = $this->params->get('searchterm');
		$zoom = $this->params->get('zoom');
		$this->loadjquery = $this->params->get('loadjquery');
		$this->loadbootstrap = $this->params->get('loadbootstrap');
		$this->loadbootstrapcss = $this->params->get('loadbootstrapcss');
		$this->credits = $this->params->get('credits');
		
		$this->language = (empty($lang) ? "en" : $lang);
		$this->region = (empty($region) ? "GB" : $region);
		$this->lat = (empty($lat) ? 40.54629751976399 : $lat);
		$this->lon = (empty($lon) ? 23.01861169311519 : $lon);
		$this->searchterm = (empty($term) ? "" : $term);
		$this->zoom = (empty($zoom) ? 17 : $zoom);

		
		
		//testing: retrieve data from another model
		//$model = $this->getModel('issue');
		//$item = $model->getItem();		
		//testing ends
		

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
		$filter_category = $this->state->get('filter_category');	
	
		$this->filters .= '<ul>';
		foreach($cats as $JCatNode){
			//id is the category id
			if(empty($filter_category)){
				if($JCatNode->parentid == 'root')		
					$this->filters .='<li><input path="'.$JCatNode->path.'" parent="box'.$JCatNode->parentid.'" name="cat[]" value="'.$JCatNode->id.'" type="checkbox" checked="checked" id="cat-'.$JCatNode->id.'" onclick="boxclick2(this,'.$JCatNode->id.')" /><span class="root">'.$JCatNode->title.'</span></li>' . "\n";
				else
					$this->filters .='<li><input path="'.$JCatNode->path.'" parent="box'.$JCatNode->parentid.'" name="cat[]" value="'.$JCatNode->id.'" type="checkbox" checked="checked" id="cat-'.$JCatNode->id.'" onclick="boxclick2(this,'.$JCatNode->id.')" />'.$JCatNode->title.'</li>' . "\n";
			}
			else{
				if($JCatNode->parentid == 'root'){
					$this->filters .='<li><input path="'.$JCatNode->path.'" parent="box'.$JCatNode->parentid.'" name="cat[]" value="'.$JCatNode->id.'" type="checkbox" '; if(in_array($JCatNode->id, $filter_category)) $this->filters .= 'checked="checked"'; $this->filters .= ' id="cat-'.$JCatNode->id.'" onclick="boxclick2(this,'.$JCatNode->id.')" /><span class="root">'.$JCatNode->title.'</span></li>' . "\n";
				}
				else{
					$this->filters .='<li><input path="'.$JCatNode->path.'" parent="box'.$JCatNode->parentid.'" name="cat[]" value="'.$JCatNode->id.'" type="checkbox" '; if(in_array($JCatNode->id, $filter_category)) $this->filters .= 'checked="checked"'; $this->filters .= ' id="cat-'.$JCatNode->id.'" onclick="boxclick2(this,'.$JCatNode->id.')" />'.$JCatNode->title.'</li>' . "\n";
				}	
			}
			
			if(!empty($JCatNode->children)){
				$this->createFilters($JCatNode->children);
			}
		
		}
		$this->filters .= '</ul>';

		return $this->filters;
	}
	
	protected function createFiltersAsArray($cats)
	{
		foreach($cats as $cat){
			$this->filters = '';
			$ar[] = $this->createFilters(array($cat));
		}
		$this->filters = '';
		return $ar;
	}
	
	protected function createLimitBox()
	{
		$selected = $this->state->get('list.limit');
		$html = '';
		$values = array (10, 20, 100, 0);
		foreach($values as $i){
			$a = $i;
			if($a == 0)
				$a = JText::_('ALL');
			if($selected == $i){
				$html .= '<li><a href="#" onclick="$(\'input[name=limit]\').val('.$i.');$(\'#adminForm\').submit();">'.$a.' <i class="icon-ok"></i></a></li>';
			}
			else {
				$html .= '<li><a href="#" onclick="$(\'input[name=limit]\').val('.$i.');$(\'#adminForm\').submit();">'.$a.'</a></li>';
			}
		}
		return $html;
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
	
	protected function getMarkersArrayFromItems() {
		$ar = array();
		foreach($this->items as $item){
			$ar[] = array('name'=>$item->title,
						'description'=>$item->description,
						'catid'=>$item->catid,
						'id'=>$item->id,
						'lat'=>$item->latitude,
						'lng'=>$item->longitude
						);
		}
		
		return $ar;
	}
	
	protected function setDocumentOLD() 
	{
		$document = JFactory::getDocument();
		
		if($this->loadbootstrapcss == 1)
			$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/bootstrap/css/bootstrap.min.css');	
		
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/css/mega-menu.css');	
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/css/virtualcitytour360_list.css');	

		//add scripts
		if($this->loadjquery == 1){
			$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/jquery-1.7.1.min.js');
			//jquery noConflict
			$document->addScriptDeclaration( 'var jVct = jQuery.noConflict();' );
		}		

		
		if($this->loadbootstrap == 1)
			$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/bootstrap/js/bootstrap.min.js');

		$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/virtualcitytour360_list.js');	
	
		
		
		//add google maps
		$document->addScript("https://maps.google.com/maps/api/js?sensor=false&language=". $this->language ."&region=". $this->region);
		$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/infobox_packed.js');		

		$document->addScriptDeclaration('var jsonMarkers = '.json_encode($this->getMarkersArrayFromItems()).';');
		
		
		$LAT = $this->lat;
		$LON = $this->lon;
		
		//prepare custom icons accordingly (get images from virtualcitytour360 categories)
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
				zoom: ".$this->zoom.",
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
					,closeBoxURL: \"https://www.google.com/intl/en_us/mapfiles/close.gif\"
					,infoBoxClearance: new google.maps.Size(1, 1)
					,isHidden: false
					,pane: \"floatPane\"
					,enableEventPropagation: false
				};
				infoBox = new InfoBox(infoBoxOptions);				

				for (var i = 0; i < jsonMarkers.length; i++) {
					var name = jsonMarkers[i].name;
					var description = jsonMarkers[i].description;
					var catid = jsonMarkers[i].catid;
					var id = jsonMarkers[i].id;
					//var photos = markers[i].photos;
					

					var point = new google.maps.LatLng(
						parseFloat(jsonMarkers[i].lat),
						parseFloat(jsonMarkers[i].lng)
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
					//marker.photos = photos;
					
					marker.description = description;
					
					//bindInfoWindow(marker, map, infoWindow, html);
					bindInfoBox(marker, map, infoBox, html);
					
					gmarkers.push(marker);
				}

				resetBounds();

				
				$(\"#loading\").hide();
			}

			function bindInfoWindow(marker, map, infoWindow, html) {
			  google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
				map.panTo(marker.getPosition());
				//showInfo(marker);
			  });
			}
			
			//alternative to infoWindow is the infoBox
			function bindInfoBox(marker, map, infoWindow, html) {
				var boxText = document.createElement(\"div\");
				boxText.style.cssText = \"border: 1px solid black; margin-top: 8px; background-color: yellow; padding: 5px;\";
				boxText.innerHTML = html;			
		
				google.maps.event.addListener(marker, 'click', function() {
					alert( marker.id );
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
				// == check the checkbox ==
				document.getElementById('cat-'+category).checked = true;
			}			
			
			function hide(category) {
				// == clear the checkbox ==
				document.getElementById('cat-'+category).checked = false;
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
				arr = document.getElementsByName('cat[]');
				for(var i = 0; i < arr.length; i++)
				{
					var obj = document.getElementsByName('cat[]').item(i);
					var c = obj.id.substr(4, obj.id.length);

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
				return false;
			}			
			
			function markerclick(i) {
				google.maps.event.trigger(gmarkers[i],'click');
			}			

			function markerhover(id) {
				var index;
				for (var i=0; i<gmarkers.length; i++) {				
					if(gmarkers[i].id == id){
						index = i;
					}
				}
				google.maps.event.trigger(gmarkers[index],'mouseover');
			}
			
			function markerout(id) {
				var index;
				for (var i=0; i<gmarkers.length; i++) {				
					if(gmarkers[i].id == id){
						index = i;
						
					}
				}
				google.maps.event.trigger(gmarkers[index],'mouseout');

			}			
			
			window.addEvent('domready', function() {
				jVct(\".imc-issue-item\").mouseenter(function(event)
				{
					$(this).addClass(\"imc-highlight\");
					markerhover($(this).attr('id').substring(8));
				});

				jVct(\".imc-issue-item\").mouseleave(function(event)
				{
					$(this).removeClass(\"imc-highlight\");
					markerout($(this).attr('id').substring(8));
				});	  

				jVct(document).click(function(e) {
					if( $('#drop-1').is('.hover')) { $('#drop-1').removeClass('hover');	}				   
					if( $('#drop-2').is('.hover')) { $('#drop-2').removeClass('hover');	}				   
					if( $('#drop-3').is('.hover')) { $('#drop-3').removeClass('hover');	}				   
				});
				
				jVct('#btn-1').click(function(event)
				{
					if( $('#drop-2').is('.hover')) { $('#btn-2').click(); }
					if( $('#drop-3').is('.hover')) { $('#btn-3').click(); }
					
					if( $('#drop-1').is('.hover')) {
						$('#drop-1').removeClass('hover');
					}
					else{
						$('#drop-1').addClass('hover');
					}
					event.stopPropagation();
				});
				
				jVct('#btn-2').click(function(event)
				{
					if( $('#drop-1').is('.hover')) { $('#btn-1').click(); }
					if( $('#drop-3').is('.hover')) { $('#btn-3').click(); }
				
					if( $('#drop-2').is('.hover')) {
						$('#drop-2').removeClass('hover');
					}
					else{
						$('#drop-2').addClass('hover');
					}
					event.stopPropagation();
				});
				jVct('#btn-3').click(function(event)
				{
					if( $('#drop-1').is('.hover')) { $('#btn-1').click(); }
					if( $('#drop-2').is('.hover')) { $('#btn-2').click(); }
				
					if( $('#drop-3').is('.hover')) {
						$('#drop-3').removeClass('hover');
					}
					else{
						$('#drop-3').addClass('hover');
					}
					event.stopPropagation();
				});
				
				jVct('.megadrop').click(function(event) { event.stopPropagation();	});
				
			});
			
			// Onload handler to fire off the app.
			google.maps.event.addDomListener(window, 'load', initialize);
			
		";

		//add the javascript to the head of the html document
		$document->addScriptDeclaration($googleMapInit);
	}
	
	
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		
		if($this->loadbootstrapcss == 1)
			$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/bootstrap/css/bootstrap.min.css');	
		
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/css/mega-menu.css');	
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/css/virtualcitytour360_list.css');	
		
		//add scripts
		if($this->loadjquery == 1){
			$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/jquery-1.7.1.min.js');
			//jquery noConflict
			$document->addScriptDeclaration( 'var jVct = jQuery.noConflict();' );
		}		
		
		
		if($this->loadbootstrap == 1)
			$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/bootstrap/js/bootstrap.min.js');
		
		//colorbox (keep this?)
		$document->addScript(JURI::root(true) . "/components/com_virtualcitytour360/js/colorbox/jquery.colorbox-min.js");
		$document->addScript(JURI::root(true).'/components/com_virtualcitytour360/js/virtualcitytour360.js');	
		//colorbox (keep this?)
		$document->addStyleSheet(JURI::root(true).'/components/com_virtualcitytour360/js/colorbox/css/colorbox.css');
		
		
		
		//add google maps
		$document->addScript("https://maps.google.com/maps/api/js?sensor=false&language=". $this->language ."&region=". $this->region);
			
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
		
		$googleMap = "
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
				,closeBoxURL: \"https://www.google.com/intl/en_us/mapfiles/close.gif\"
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
		
			google.maps.event.addListenerOnce(map, 'idle', function(){
				// $(\"#loading\").hide();
				google.maps.event.trigger(gmarkers[0], 'click'); //FIRST POI IS SELECTED BY DEFAULT (TODO: set this on settings) 
			});
			
			jVct(\"#loading\").hide();
		}
			
		//alternative to infoWindow is the infoBox
		function bindInfoBox(marker, map, infoWindow, html) {
			var boxText = document.createElement(\"div\");
			boxText.style.cssText = \"border: 1px solid black; margin-top: 8px; background-color: yellow; padding: 5px;\";
			boxText.innerHTML = html;

			google.maps.event.addListener(marker, 'click', function() {
				infoBox.setContent(boxText);
				infoBox.open(map, marker);
				//map.panTo(marker.getPosition());
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
			///document.getElementById('box'+category).checked = true;
			jVct('#box'+category).checked = true;
			resetBounds();
		}
		
		function hide(category) {
			for (var i=0; i<gmarkers.length; i++) {
				if (gmarkers[i].catid == category) {
					gmarkers[i].setVisible(false);
				}
			}
			// == clear the checkbox ==
			///document.getElementById('box'+category).checked = false;
			jVct('#box'+category).checked = false;
			if(infoWindow != null)
				infoWindow.close();
			
			if(infoBox != null)
				infoBox.close();
			
			jVct(\"#markerInfo\").html('');
			jVct(\"#panorama\").html('');
			jVct(\"#wrapper-info\").hide(500);
			resetBounds();
		}
			
		//--- non recursive since IE cannot handle it (doh!!)
		function boxclick2(box, category) {
			if (box.checked) {
				show(category);
			} 
			else {
				hide(category);
			}
			
			var com = box.getAttribute('path');
			var arr = new Array();
			arr = document.getElementsByName('box');
			
			for(var i = 0; i < arr.length; i++) {
				var obj = document.getElementsByName('box').item(i);
				var c = obj.id.substr(3, obj.id.length);
				var path = obj.getAttribute('path');
				if(com == path.substring(0,com.length)){
					if (box.checked) {
						obj.checked = true;
						show(c);
					} 
					else {
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
			jVct(\"#markerTitle\").html('<span class=\"markerTitle\">' + marker.title + '</span>');
			jVct(\"#panorama\").html('');
			jVct(\"#markerHead\").html('');
			jVct(\"#markerInfo\").html('');
			jVct(\"#markerImages\").html('');
			
			
			if(marker.panoramas != ''){
				jVct(\"#markerHead\").html(createInfoPanoramas(marker));
				jVct(\"#markerImages\").html(createInfoImages(marker));
				jVct(\"#markerInfo\").html(marker.description);	
			}
			else if(marker.photos != ''){
				jVct(\"#markerHead\").html(createInfoImages(marker));
				jVct(\"#markerInfo\").html(marker.description);
			} 
			else {
				jVct(\"#markerHead\").html(marker.description);
			}
					
					
					
					
					
			
			if(marker.photos != ''){
				jVct(\"a[rel='photos']\").colorbox();
			}
			
			if(marker.panoramas != ''){
				jVct(\"#panorama\").show();
			}
			else{
				jVct(\"#panorama\").hide();
			}
			
			jVct(\"#wrapper-info\").show(500);
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
						html += '<div class=\"btn-group\">';
					}
					
					html += '<button class=\"btn\" onclick=\"embedFlash(\''+pan+'\')\">' + (i+1) + '</button>';
					if(i == arr.length-1){
						html += '</div>';
					}
				}
			}
			
			return html;
		}
			
		function embedFlash(pan){
			var flash = '<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0\" id=\"RyubinPanorama\" >';
			flash += '<param name=\"wmode\" value=\"transparent\"> ';
			flash +='<embed src=\"". JURI::base()."components/com_virtualcitytour360/pano/RyubinPanoPlayer5.swf\" wmode=\"transparent\" FlashVars=\"playmode=sphere&internal_ctrl=no&img_path='+pan+'&cursor_path=". JURI::base()."components/com_virtualcitytour360/pano/my_cursor.png&xml_path=". JURI::base()."components/com_virtualcitytour360/pano/panosettings.xml\" width=\"100%\" height=\"350px\" name=\"RyubinPanorama\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"https://www.macromedia.com/go/getflashplayer\" />';
			flash += '</object>';
			jVct(\"#panorama\").html(flash);
		}
		
		// Onload handler to fire off the app.
		//google.maps.event.addDomListener(window, 'load', initialize);
		";

		
		$megamenu_js = "
		window.addEvent('domready', function() {
			initialize();
				
			jVct(\".imc-issue-item\").mouseenter(function(event)
			{
				jVct(this).addClass(\"imc-highlight\");
				markerhover($(this).attr('id').substring(8));
			});

			jVct(\".imc-issue-item\").mouseleave(function(event)
			{
				jVct(this).removeClass(\"imc-highlight\");
				markerout($(this).attr('id').substring(8));
			});	  

			jVct(document).click(function(e) {
				if( jVct('#drop-1').is('.hover')) { jVct('#drop-1').removeClass('hover');	}				   
				if( jVct('#drop-2').is('.hover')) { jVct('#drop-2').removeClass('hover');	}				   
				if( jVct('#drop-3').is('.hover')) { jVct('#drop-3').removeClass('hover');	}				   
			});
			
			jVct('#btn-1').click(function(event)
			{
				if( jVct('#drop-2').is('.hover')) { jVct('#btn-2').click(); }
				if( jVct('#drop-3').is('.hover')) { jVct('#btn-3').click(); }
				
				if( jVct('#drop-1').is('.hover')) {
					jVct('#drop-1').removeClass('hover');
				}
				else{
					jVct('#drop-1').addClass('hover');
				}
				event.stopPropagation();
			});
			
			jVct('#btn-2').click(function(event)
			{
				if( jVct('#drop-1').is('.hover')) { jVct('#btn-1').click(); }
				if( jVct('#drop-3').is('.hover')) { jVct('#btn-3').click(); }
			
				if( jVct('#drop-2').is('.hover')) {
					jVct('#drop-2').removeClass('hover');
				}
				else{
					jVct('#drop-2').addClass('hover');
				}
				event.stopPropagation();
			});
			jVct('#btn-3').click(function(event)
			{
				if( jVct('#drop-1').is('.hover')) { jVct('#btn-1').click(); }
				if( jVct('#drop-2').is('.hover')) { v('#btn-2').click(); }
			
				if( jVct('#drop-3').is('.hover')) {
					jVct('#drop-3').removeClass('hover');
				}
				else{
					jVct('#drop-3').addClass('hover');
				}
				event.stopPropagation();
			});
			
			jVct('.megadrop').click(function(event) { event.stopPropagation();	});
			
		});			
		";
		
		//add javascript to the head of the html document
		$document->addScriptDeclaration($googleMap);
		$document->addScriptDeclaration($megamenu_js);
	}

}