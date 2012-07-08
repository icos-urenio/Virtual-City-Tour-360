<?php
/**
 * @version     2.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

defined('_JEXEC') or die;

// require helper file
JLoader::register('Virtualcitytour360Helper', dirname(__FILE__) . DS . 'helpers' . DS . 'virtualcitytour360.php');

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JController::getInstance('virtualcitytour360');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
