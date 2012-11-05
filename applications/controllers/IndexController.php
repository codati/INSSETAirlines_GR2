<?php
class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
       $this->_helper->actionStack('header','index','default',array());
	}	

   public function headerAction()
   {
  		$this->_helper->viewRenderer->setResponseSegment('header');
  		$this->_helper->actionStack('footer','index','default',array());
   }
   public function footerAction()
   {
  		$this->_helper->viewRenderer->setResponseSegment('footer');
   }
}

