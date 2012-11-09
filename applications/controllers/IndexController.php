<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        //Zend_Session::destroy();
        $this->view->msgDeco = $this->_getParam('decoReussie');
        $this->_helper->actionStack('header','index','default',array('test'=>false));
    }	
    public function logoutAction()
    {
        Zend_Session::destroy();
        $this->_helper->actionStack('index','index','default',array('decoReussie'=>'Vous avez ete deconnecte'));
    }
    public function connexionAction()
    {
        $user = $this->getRequest()->getPost('input_user');
        $psw = md5($this->getRequest()->getPost('input_psw'));
        
        $db = Zend_Registry::get('db');        
        
        
        // requete recuperation utilisateur
        $reqUtil = $db->select()
            ->from(array('u' => 'utilisateurs'), array('*'))
            ->where('u.nomUtilisateur = ?', $user)
            ->where('u.mdpUtilisateur = ?', $psw)
           ;

         $leUtilisateur = $db->fetchRow($reqUtil);
         
         //Zend_Debug::dump($leUtilisateur);exit();
         
         // requete recuperation des services de l'utilisateur         
         if($leUtilisateur)
         { 
            $reqService = $db->select()
                    ->from(array('s' => 'service'), array('*'))
                    ->join(array('t' => 'travailler'),'t.idService = s.idService', array('*'))
                    ->where('t.idUtilisateur = ?', $leUtilisateur['idUtilisateur'])
                    ;

            $lesServices = $db->fetchAll($reqService); 
        
        
        // requete recuperation utilisateur
        $reqUtil = $db->select()
            ->from(array('u' => 'utilisateurs'), array('*'))
            ->where('u.nomUtilisateur = ?', $user)
            ->where('u.mdpUtilisateur = ?', $psw)
           ;
            //Zend_Debug::dump($lesServices);exit();

            $tabLesServices = array();
            $tabLesSousServices = array();
            foreach ($lesServices as $unService)
            {
               $tabLesServices[] = $unService['nomService'];

               // requete recuperation des sous services 
               $reqSousServices = $db->select()
                                   ->from(array('ss' => 'sousservice'),array('*'))
                                   ->where('ss.idService = ?', $unService['idService'])
                                   ;

               $tabLesSousServices[] = $db->fetchAll($reqSousServices);
            }                
             $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
             $espaceSession->idUtilisateur = $leUtilisateur['idUtilisateur'];
             $espaceSession->nomUtilisateur = $leUtilisateur['nomUtilisateur'];
             $espaceSession->lesServicesUtilisateur = $tabLesServices;
             $espaceSession->lesSousServicesUtilisateur = $tabLesSousServices;
             $espaceSession->connecte = true;            
         }
         $this->_helper->actionStack('header','index','default',array('test'=>true));    
   }
   public function headerAction()
   {
        $test = $this->_getParam('test');
        $this->view->test = $test;
        
        $this->_helper->viewRenderer->setResponseSegment('header');
        $this->_helper->actionStack('footer','index','default',array());
   }
   public function footerAction()
   {
        $this->_helper->viewRenderer->setResponseSegment('footer');
   }
   public function verifconnexionAction()
   {
        $login = $this->_getParam('login',0);
        $mdp = md5($this->_getParam('pass'));
        $db = Zend_Registry::get('db');               
        
        // requete recuperation utilisateur
        $reqUtil = $db->select()
            ->from(array('u' => 'utilisateurs'), array('*'))
            ->where('u.nomUtilisateur = ?', $login)
            ->where('u.mdpUtilisateur = ?', $mdp)
           ;


        // Zend_Debug::dump($db->fetchAll($reqUtil));exit();

         $leUtilisateur = $db->fetchRow($reqUtil);
         if($leUtilisateur)
         {
             echo '1';
         }
         else 
         {
             echo '0';
         }
         exit();

   }
}

