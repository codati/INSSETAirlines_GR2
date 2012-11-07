<?php
class TestController extends Zend_Controller_Action
{
	public function indexAction() 
	{
		//$this->_helper->actionStack('header','index','default',array());
            $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
            echo $espaceSession->idUtilisateur.'v<br>';
            echo $espaceSession->nomUtilisateur.'v<br>';
            var_dump($espaceSession->lesServicesUtilisateur);
            echo '<br><br>fiiiin';
            exit();
	}
}
