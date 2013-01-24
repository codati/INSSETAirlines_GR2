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
        
        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('id','formSaisie');

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

        $tableVol = new Table_Vol();
        $tableEscale = new Table_Escale();
        $tableReservation = new Table_Reservation();
        
        $idVol = $this->getRequest()->getPost('idVol');

        $infosVol = $tableVol->get_InfosVol($idVol);
        $infosEscale = $tableEscale->get_InfosEscales($idVol);
        $infosRepas = $tableReservation->GetNbTypeRepasParReservationEtParVol($idVol);

        //Zend_Debug::dump($infosRepas);exit;
        $this->view->infosVol = $infosVol;
        $this->view->infosEscale = $infosEscale;
        $this->view->infosRepas = $infosRepas;
    }
}