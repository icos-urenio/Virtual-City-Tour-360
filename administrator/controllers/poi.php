<?php


// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Poi controller class.
 */
class Virtualcitytour360ControllerPoi extends JControllerForm
{

    function __construct() {
        $this->view_list = 'pois';
        parent::__construct();
    }

}
