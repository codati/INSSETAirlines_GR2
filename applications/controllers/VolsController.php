<?php

class VolsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->actionStack('header','index','default',array());
    }

    public function consulterAction()
    {
        $this->_helper->actionStack('header','index','default',array());
    }
}
?>
