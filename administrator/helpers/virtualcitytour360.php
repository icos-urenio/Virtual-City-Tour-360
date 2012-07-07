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

/**
 * Virtualcitytour360 helper.
 */
abstract class Virtualcitytour360Helper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{

		JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCITYTOUR360_TITLE_ITEMS'),
			'index.php?option=com_virtualcitytour360&view=items',
			$vName == 'items'
		);
		JSubMenuHelper::addEntry(JText::_('COM_VIRTUALCITYTOUR360_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_virtualcitytour360', $vName == 'categories');
		
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-item {background-image: url(../media/com_virtualcitytour360/images/virtualcitytour360-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-items {background-image: url(../media/com_virtualcitytour360/images/virtualcitytour360-48x48.png);}');
		if ($vName == 'categories') 
		{
			$document->setTitle(JText::_('COM_VIRTUALCITYTOUR360_ADMINISTRATION_CATEGORIES'));
		}		
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_virtualcitytour360';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public static function getImages($itemId)
	{
		$html = '';
		$imagesPath = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.$itemId.DS.'images'.DS.'thumbs'.DS.'*.*';
		foreach (glob($imagesPath) as $filename) {
			$imgpath = JURI::root().'images/virtualcitytour360/'.$itemId.'/images/thumbs/' . basename($filename);
			$imgbig = JURI::root().'images/virtualcitytour360/'.$itemId.'/images/' . basename($filename);
			
 			$html .= '<div class="thumb">';
			$html .= '<a class="modal" href="'.$imgbig.'"><img src="' .$imgpath. '" width="100" height="80" /></a>';
			$html .= '<br /><a href="#" onclick="deleteImage(\' '.$imgpath.'\' ,\''.basename($filename).'\');">Διαγραφή</a></div>';
			
		}
	
		return $html;
	}
	
	public static function getPanoramas($itemId)
	{
		$html = '';
		$imagesPath = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.$itemId.DS.'panoramas'.DS.'thumbs'.DS.'*.*';
		foreach (glob($imagesPath) as $filename) {
			$imgpath = JURI::root().'images/virtualcitytour360/'.$itemId.'/panoramas/thumbs/' . basename($filename);
			$imgbig = JURI::root().'images/virtualcitytour360/'.$itemId.'/panoramas/' . basename($filename);

			$html .= '<div class="thumb">';
			$html .= '<a class="modal" href="'.$imgbig.'"><img src="' .$imgpath. '" width="100" height="80" /></a>';
			$html .= '<br /><a href="#" onclick="deletePanorama(\' '.$imgpath.'\' ,\''.basename($filename).'\');">Διαγραφή</a></div>';
			
			
		}
	
		return $html;
	}
	
}
