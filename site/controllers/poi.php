<?php
/**
 * @version     2.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */


// No direct access.
defined('_JEXEC') or die;
JRequest::checkToken() or jexit( 'Invalid Token' );

jimport('joomla.application.component.controllerform');

class Virtualcitytour360ControllerPoi extends JControllerForm
{
	
	public function cancel($key = 'poi_id')
	{
		/* Never edit a record just redirect */
		//parent::cancel($key);	
		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}
	
	
	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 */
	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}	
	
	public function getModel($name = 'addpoi', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}	
	
	
	
}
