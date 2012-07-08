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

jimport('joomla.application.component.controller');

class Virtualcitytour360Controller extends JController
{
	
	public function display($cachable = false, $urlparams = false)
	{
	
		$view = JRequest::getCmd('view', 'pois');
		JRequest::setVar('view', $view);
		$v = & $this->getView($view, 'html');
		$v->setModel($this->getModel($view), true); //the default model (true) :: $view is either pois or virtualcitytour360
		$v->display();
	
		return $this;
	}	
	
	//called only as format=raw from ajax
	function getMarkersAsXML()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		
		$model = $this->getModel('virtualcitytour360');
		$items = $model->getItems();

		$dom = new DOMDocument('1.0', 'UTF-8');
		$node = $dom->createElement("markers");
		$parnode = $dom->appendChild($node); 		
		
		$document = &JFactory::getDocument();
		$document->setMimeEncoding('text/xml');
		foreach($items as $item){
			$node = $dom->createElement("marker");  
			$newnode = $parnode->appendChild($node);   
			$newnode->setAttribute("name",$item->title);
			$newnode->setAttribute("description", $item->description);  
			$newnode->setAttribute("lat", $item->latitude);  
			$newnode->setAttribute("lng", $item->longitude);  
			$newnode->setAttribute("catid", $item->catid);
			$newnode->setAttribute("id", $item->id);
			$newnode->setAttribute("photos", $item->photos);
			$newnode->setAttribute("panoramas", $item->panoramas);
		}		
		echo $dom->saveXML();
	}

}