<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
       //  Zend_Session::namespaceUnset('utilisateurCourant');
        // Zend_Session::namespaceUnset('agenceCourante');
        $this->view->msgDeco = $this->_getParam('decoReussie');
        $this->_helper->actionStack('header','index','default',array());
    }	
    public function headerAction()
    {   
        if(Zend_Session::namespaceIsset('utilisateurCourant'))
        {
             $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
             $pasCo = $espaceSession->connecte;
        }
        else
        {
            if(Zend_Session::namespaceIsset('agenceCourante'))
            {
                $espaceAgence = new Zend_Session_Namespace('agenceCourante');
                $pasCo = $espaceAgence->connecte;
            }
            else
            {
                $pasCo = null;
            }
        }
        
        $this->view->pasCo = $pasCo;// $this->_getParam('pasCo');
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
         Zend_Session::namespaceUnset('agenceCourante');
         $this->_helper->actionStack('index','index','default',array('decoReussie'=>'Déconnexion réussie'));       
     }
     public function connexionAction()
     {
         $this->_helper->actionStack('header','index','default',array('test'=>true));  
         
         $user = $this->getRequest()->getPost('input_user');
         $psw = md5($this->getRequest()->getPost('input_psw'));
         $radio = $this->getRequest()->getPost('radio_form_co'); // 0 => insset, 1 => agence
        // echo $radio;exit;
         
         if($radio == 0) /// utilisateur de l'insset
         {
            $utilisateur = new Table_Utilisateur;
            $leUtilisateur = $utilisateur->login($user,$psw);
            
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
                 Zend_Session::namespaceUnset('agenceCourante');
                 $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
                 $espaceSession->idUtilisateur = $leUtilisateur['idUtilisateur'];
                 $espaceSession->nomUtilisateur = $leUtilisateur['nomUtilisateur'];
                 $espaceSession->lesServicesUtilisateur = $tabLesServices;
                 $espaceSession->lesSousServicesUtilisateur = $tabSousServices;
                 $espaceSession->connecte = true;    
             }
         }
         else   // radio = 1, agence
         {
             //echo 'test';exit;
             $tableAgence = new Table_Agence;
             $agence = $tableAgence->login($user,$psw);
             
             Zend_Session::namespaceUnset('utilisateurCourant');
             //Zend_Debug::dump($agence);exit;
             $espaceAgence = new Zend_Session_Namespace('agenceCourante');
             $espaceAgence->idAgence = $agence['idAgence'];
             $espaceAgence->nomAgence = $agence['nomAgence'];
             $espaceAgence->connecte = true; 
             
             // ecrire le code pour le menu des agences  
             
         }
            
    }
    public function verifconnexionAction()
    {
         $user = $this->_getParam('user');
         $psw = md5($this->_getParam('pass'));
         $radio = $this->_getParam('rad');
         
         if($radio == 0) // insset
         {
             $tableutilisateur = new Table_Utilisateur;
             $utilisateur = $tableutilisateur->login($user,$psw);
             $agence = null;
         }
         else // radio == 1, agence
         {
             $tableagence = new Table_Agence;
             $agence = $tableagence->login($user,$psw);
             $utilisateur = null;
         }
          if(!is_null($utilisateur) || !is_null($agence))
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

