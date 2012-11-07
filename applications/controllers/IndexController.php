<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        Zend_Session::destroy();
        $this->_helper->actionStack('header','index','default',array());
    }	
    public function connexionAction()
    {
        $user = $this->getRequest()->getPost('input_user');
        $psw = md5($this->getRequest()->getPost('input_psw'));
        
        $db = Zend_Registry::get('db');        
        $reqUtil = $db->select()
            ->from(array('u' => 'utilisateurs'), array('*'))
            ->where('u.nomUtilisateur = ?', $user)
            ->where('u.mdpUtilisateur = ?', $psw)
           ;

         $leUtilisateur = $db->fetchRow($reqUtil);
         
         //Zend_Debug::dump($leUtilisateur);exit();
         
         $reqService = $db->select()
                 ->from(array('s' => 'service'), array('*'))
                 ->join(array('t' => 'travailler'),'t.idService = s.idService', array('*'))
                 ->where('t.idUtilisateur = ?', $leUtilisateur['idUtilisateur'])
                 ;
         
         $lesServices = $db->fetchAll($reqService);
         
         //Zend_Debug::dump($lesServices);exit();
         
         $tabLesServices = array();
         $tabLesSousServices = array();
         foreach ($lesServices as $unService)
         {
            $tabLesServices[] = $unService['nomService'];
            
            $reqSousServices = $db->select()
                                ->from(array('ss' => 'sousservice'),array('*'))
                                ->where('ss.idService = ?', $unService['idService'])
                                ;
            
            $tabLesSousServices[] = $db->fetchAll($reqSousServices);
            
         }
         
         if($leUtilisateur)
         {             
             $espaceSession = new Zend_Session_Namespace('utilisateurCourant');
             $espaceSession->idUtilisateur = $leUtilisateur['idUtilisateur'];
             $espaceSession->nomUtilisateur = $leUtilisateur['nomUtilisateur'];
             $espaceSession->lesServicesUtilisateur = $tabLesServices;
             $espaceSession->lesSousServicesUtilisateur = $tabLesSousServices;
         }
         
         $this->_helper->actionStack('header','index','default',array('lesServices' => $tabLesServices, 'lesSousServices' => $tabLesSousServices));
           //Zend_Debug::dump($tabLesSousServices);exit();     
   }
   public function headerAction()
   {
        $lesServices = $this->_getParam('lesServices');
        $lesSousServices = $this->_getParam('lesSousServices');
       
        $this->view->lesServices = $lesServices;
        $this->view->lesSousServices = $lesSousServices;
       
        $this->_helper->viewRenderer->setResponseSegment('header');
        $this->_helper->actionStack('footer','index','default',array());
   }
   public function footerAction()
   {
        /*$espaceSession = new Zend_Session_Namespace('utilisateurCourant');
        echo $espaceSession->idUtilisateur.'<br>';
        echo $espaceSession->nomUtilisateur.'<br>';
        var_dump($espaceSession->lesServicesUtilisateur);
        exit();*/
        $this->_helper->viewRenderer->setResponseSegment('footer');
   }
}

