<?php
class ModeleavionController extends Zend_Controller_Action
{
    public function init() {$this->headStyleScript = array();}
	
	public function indexAction() 
    {
        
    } 
    public function ajouterAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }
    
}
