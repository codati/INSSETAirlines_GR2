<?php
    class TestController extends Zend_Controller_Action
    {
        public function init() {$this->headStyleScript = array();}
	
        public function indexAction() 
        {
            echo 'ya plus rien d\'interressant a voir ici !';
        }        
}
