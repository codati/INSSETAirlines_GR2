<?php
class TestController extends Zend_Controller_Action
{
	public function indexAction() 
	{
		$this->_helper->actionStack('header','index','default',array());
	}
}
