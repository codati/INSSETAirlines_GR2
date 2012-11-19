<?php
class TestController extends Zend_Controller_Action
{
	public function indexAction() 
	{   
            
            $Tuser = new TUtilisateur;
            $user = $Tuser->fetchAll();
            Zend_Debug::dump($user);
            exit;
           /* $user = $Tuser->select()->from($Tuser)->where('idUtilisateur = ?',2);
            $truc = $Tuser->fetchRow($user);
            Zend_Debug::dump($truc->nomUtilisateur);*/
	}
}
