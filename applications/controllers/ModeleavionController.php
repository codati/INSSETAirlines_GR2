<?php
class ModeleavionController extends Zend_Controller_Action
{
    public function indexAction() 
    {
        
    } 
    public function ajouterAction() 
    {
        $this->_helper->actionStack('header','index','default',array());
    }
    
}
