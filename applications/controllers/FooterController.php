<?php

class FooterController extends Zend_Controller_Action 
{
	public function indexAction()
	{

   }
   public function footerAction()
   {
  		$this->_helper->viewRenderer->setResponseSegment('footer');
   }
}
