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

jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Model
 */
class Virtualcitytour360ModelPoi extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_virtualcitytour360.poi';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		// Load the object state.
		$id	= JRequest::getInt('poi_id');
		$this->setState('virtualcitytour360.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}
	

	function &getItem($id = null)
	{
		if (!isset($this->_item))
		{

			if ($this->_item === null) {
				if (empty($id)) {
					$id = $this->getState('virtualcitytour360.id');
				}				

				$db		= $this->getDbo();
				$query	= $db->getQuery(true);
				$query->select(
					'a.*'
					);
				$query->from('#__virtualcitytour360 as a');
				$query->where('a.id = ' . (int) $id);

				// Join on user table.
				$query->select('u.name AS username');
				$query->join('LEFT', '#__users AS u on u.id = a.userid');	

				// Join on catid table.
				$query->select('c.title AS catname');
				$query->join('LEFT', '#__categories AS c on c.id = a.catid');	

				
				$db->setQuery((string) $query);

				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}

				$this->_item = $db->loadObject();
				
			}
		}
		return $this->_item;
	}	
	
	
	
	public function getCategoryIcon($pk = 0)
	{
		$pk = (!empty($pk)) ? $pk : (int) $id = $this->getState('virtualcitytour360.id');
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.catid');
		$query->from('#__virtualcitytour360 as a');
		$query->where('a.id = ' . (int) $id);
		// Join on catid table.
		$query->select('c.params AS params');
		$query->join('LEFT', '#__categories AS c on c.id = a.catid');	
		
		$db->setQuery($query);
		//$result = $db->loadResult();
		$row = $db->loadAssoc();

		$parameters = new JRegistry();
		$parameters->loadJSON($row['params']);
		$image = $parameters->get('image');		

		return $image;
	}
}