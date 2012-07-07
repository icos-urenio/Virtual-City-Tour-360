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

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');


JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_virtualcitytour360/tables');

/**
 * Model
 */
class Virtualcitytour360ModelVirtualcitytour360 extends JModelList
{
	//protected $_item;
	private $_categories = null;
	private $_parent = null;

	function getCategories($recursive = false)
	{
        $_categories = JCategories::getInstance('Virtualcitytour360');
        $this->_parent = $_categories->get();
        if(is_object($this->_parent))
        {
            $this->_items = $this->_parent->getChildren($recursive);
        }
        else
        {
            $this->_items = false;
        }
        return $this->loadCats($this->_items);
	}
		
	protected function loadCats($cats = array())
    {
        if(is_array($cats))
        {
            $i = 0;
            $return = array();
            foreach($cats as $JCatNode)
            {
                $return[$i]->title = $JCatNode->title;
                $return[$i]->parentid = $JCatNode->parent_id;
                $return[$i]->path = $JCatNode->get('path');
                $return[$i]->id = $JCatNode->id;
				$params = new JRegistry();
				$params->loadJSON($JCatNode->params);
				$return[$i]->image = $params->get('image');

				if($JCatNode->hasChildren())
                    $return[$i]->children = $this->loadCats($JCatNode->getChildren());
                else
                    $return[$i]->children = false;

                $i++;
            }
            return $return;
        }
        return false;
    }
	
	function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = &parent::getItems();

		// Convert the params field into an object, saving original in _params
		for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item = &$items[$i];
			if (!isset($this->_params)) {
				$params = new JRegistry();
				$params->loadJSON($item->params);
				$item->params = $params;
			}
		}
		return $items;	
	}

	protected function getListQuery()
	{
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());


		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select(
			$this->getState(
				'list.select',
				'a.*, #__categories.title as category, catid, #__categories.path, #__categories.parent_id'
			)
		);
		$query->from('`#__virtualcitytour360` AS a');
		$query->leftJoin('#__categories on catid=#__categories.id');		
		$query->where('a.state = 1');
		
		return $query;
	}	
	
}