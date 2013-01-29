<?php
class ServiceCommercialController extends Zend_Controller_Action
{
    public function init(){$this->headStyleScript = array();
    }

    public function indexAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }
    public function voirresasagenceAction()
    {
        
    }
    
    public function placesbloqueesAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }
}

