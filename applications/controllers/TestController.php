<?php
class TestController extends Zend_Controller_Action
{
        public function init() { 
            $this->headStyleScript = array();
            if(!session_encours())
            {
                $redirector = $this->_helper->getHelper('Redirector');
                $redirector->gotoUrl($this->view->baseUrl());  
            }        
        }	
        public function indexAction() 
        {
            echo 'ya plus rien d\'interressant a voir ici !';
        }
}
