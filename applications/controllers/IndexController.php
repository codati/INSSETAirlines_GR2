<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->actionStack('header','index','default',array());
    }	
    public function connexionAction()
    {
        $this->_helper->actionStack('header','index','default',array());
        
        $user = $this->getRequest()->getPost('input_user');
        $psw = md5($this->getRequest()->getPost('input_psw'));
        
        $db = Zend_Registry::get('db');        
        $reqUtil = $db->select()
            ->from(array('u' => 'utilisateurs'), array('idUtilisateur'))
            ->where('u.nomUtilisateur = ?', $user)
            ->where('u.mdpUtilisateur = ?', $psw)
           ;

         $leUtilisateur = $db->fetchOne($reqUtil);
         //Zend_Debug::dump($res);        
         echo $leUtilisateur;
         
         $reqService = $db->select()
                 ->from(array('s' => 'service'), array('*'))
                 ->join(array('t' => 'travailler'),'t.idService = s.idService', array('*'))
                 ->where('t.idUtilisateur = ?', $leUtilisateur)
                 ;
         
         $lesServices = $db->fetchAll($reqService);
         Zend_Debug::dump($lesServices);
         
         
         
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

