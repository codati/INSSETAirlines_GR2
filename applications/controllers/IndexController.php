<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->msgDeco = $this->_getParam('decoReussie');
        $this->_helper->actionStack('header','index','default',array());
    }	
    public function headerAction()
    {       
        if(Zend_Session::isStarted())
        {
            $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
            $this->view->pasCo = $espaceSession->connecte;// $this->_getParam('pasCo');
        }
        $this->_helper->viewRenderer->setResponseSegment('header');
        $this->_helper->actionStack('footer','index','default',array());
    }
    public function footerAction()
    {
         $this->_helper->viewRenderer->setResponseSegment('footer');
    }
     public function logoutAction()
     {
         //Zend_Session::destroy();  
         Zend_Session::namespaceUnset('utilisateurCourant');
         $this->_helper->actionStack('index','index','default',array('decoReussie'=>'Déconnexion réussie'));       
     }
     public function connexionAction()
     {
         $user = $this->getRequest()->getPost('input_user');
         $psw = md5($this->getRequest()->getPost('input_psw'));
            
         $utilisateur = new Table_Utilisateur;
         $leUtilisateur = $utilisateur->login($user,$psw);
        
            //Zend_Debug::dump($leUtilisateur);exit;
          // requete recuperation des services de l'utilisateur         
          //if(is_array($leUtilisateur))
         if(!is_null($leUtilisateur))
          { 
                 
            $service = new Table_Service;  
            $sousservice = new Table_SousService;
             
            //recupere les services de l'utilisateur
            $lesServices = $service->getLesServices($leUtilisateur['idUtilisateur']);

             $tabLesServices = array();
             $tabSousServices = array();
             foreach ($lesServices as $unService)
             {
                $tabLesServices[] = $unService['nomService'];
              
                // requete recuperation des sous services 
                $tabSousServices[] = $sousservice->getLesSousServices($unService['idService']);
             }             
              $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
              $espaceSession->idUtilisateur = $leUtilisateur['idUtilisateur'];
              $espaceSession->nomUtilisateur = $leUtilisateur['nomUtilisateur'];
              $espaceSession->lesServicesUtilisateur = $tabLesServices;
              $espaceSession->lesSousServicesUtilisateur = $tabSousServices;
              $espaceSession->connecte = true;    
          }
          $this->_helper->actionStack('header','index','default',array('test'=>true));    
    }
    public function verifconnexionAction()
    {
         $user = $this->_getParam('user');
         $psw = md5($this->_getParam('pass'));
         
         $utilisateur = new Table_Utilisateur;
         $leUtilisateur = $utilisateur->login($user,$psw);
         
          if(!is_null($leUtilisateur))
          {
              echo '1';
          }
          else 
          {
              echo '0';
          }
          exit();

    }
    public function telechargerAction()
    {
        $this->_helper->actionStack('header','index','default',array()); 
    }
}

