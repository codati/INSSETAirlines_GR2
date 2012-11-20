<?php
class TestController extends Zend_Controller_Action
{
	public function indexAction() 
	{   
            $nAvion = new Table_Avion;
            
            $unAvion = $nAvion->Ajouter('TEST', '2');
            
            /* $user = $Tuser->select()->from($Tuser)->where('idUtilisateur = ?',2);
            $truc = $Tuser->fetchRow($user);
            Zend_Debug::dump($truc->nomUtilisateur);*/
	}
}
