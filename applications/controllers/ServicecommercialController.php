<?php
class ServiceCommercialController extends Zend_Controller_Action
{
    public function init(){$this->headStyleScript = array();
    }

    public function indexAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }
    public function voirresasagenceAction()
    {
        
    }
    
    public function placesbloqueesAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
//        $tableDemander = new Table_Demander();
//        $tableReservation = new Table_Reservation();
//        
//        $lesVols = $tableReservation->GetVolReserve();
//        $i = 0;
//        foreach($lesVols as $unVol)
//        {
//            $placesBloquees[$i] = $tableDemander->GetNbPlacesBloquees($unVol['idReservation']);
//            $i++;
//        }
//        Zend_Debug::dump($placesBloquees);exit;
//        $this->view->lesPlacesBloquees = $placesBloquees;
    }
}

