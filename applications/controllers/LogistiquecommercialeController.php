<?php

class LogistiquecommercialeController extends Zend_Controller_Action
{
    public function init() 
    {
        $this->headStyleScript = array();
    
        if(!session_encours())
        {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
    }
    
    public function infosvolAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $espaceSession = new Zend_Session_Namespace('RetourTest');
        echo $espaceSession->messageErreur;
        $espaceSession->messageErreur = "";
        
        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('class','form');

        $monform->setAction($this->view->baseUrl().'/logistiquecommerciale/infosduvol');

        $eIdVol = new Zend_Form_Element_Text('idVol');
        $eIdVol->setLabel('Numero de vol : ');
        $eIdVol->setAttrib('required', 'required');

        $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class','valider');

        $monform->addElement($eIdVol);
        $monform->addElement($eSubmit);       

        $this->view->leform = $monform;
    }
    
    public function infosduvolAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $idVol = $this->getRequest()->getPost('idVol');
        
        $tableVol = new Table_Vol();
        $tableEscale = new Table_Escale();
        $tableReservation = new Table_Reservation();
        
        if((preg_match('#^[0-9\-]+$#', $idVol)) AND ($tableVol->existeVol($idVol)))
        {        
            $infosVol = $tableVol->get_InfosVol($idVol);
            $infosEscale = $tableEscale->get_InfosEscales($idVol);
            $infosRepas = $tableReservation->GetNbTypeRepasParReservationEtParVol($idVol);

            //Zend_Debug::dump($infosRepas);exit;
            $this->view->infosVol = $infosVol;
            $this->view->infosEscale = $infosEscale;
            $this->view->infosRepas = $infosRepas;
        }
        else
        {
            $espaceSession = new Zend_Session_Namespace('RetourTest');
            $espaceSession->messageErreur = '<h3 class="erreur">Saisie invalide.</h3>';
            
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl('/logistiquecommerciale/infosvol'));
        }
    }
    
    //Fab
    public function gererpromosAction()
    {
          $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
          
          /*
           * Récupérer tous les vols dont : 
           * le départ < à un mois
           * qui n'ont pas encore de promo 
           */
          $tVol = new Table_Vol();
          $dateDebut = DateFormat_SQL(Zend_Date::now());
          $dateFin = DateFormat_SQL(Zend_Date::now()->addMonth(1));

          $lesVolsAVenir = $tVol->getVolsEntreDate($dateDebut, $dateFin);
          
          $this->view->lesVolsAVenir = $lesVolsAVenir;
                  
          
    }
}
