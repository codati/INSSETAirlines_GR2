<?php

class HeaderController extends Zend_Controller_Action 
{
	public function indexAction()
	{
		//$this->_helper->actionStack('header','header','default',array());
   }
   public function headerAction()
   {
  		$this->_helper->viewRenderer->setResponseSegment('header');
  		$this->_helper->actionStack('footer','footer','default',array());
   }
}
