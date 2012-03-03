<?php
/**
 * @version     1.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_virtualcitytour360')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// require helper file
JLoader::register('Virtualcitytour360Helper', dirname(__FILE__) . DS . 'helpers' . DS . 'virtualcitytour360.php');

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Virtualcitytour360');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
