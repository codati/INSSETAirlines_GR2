<?php
class ServiceCommercialController extends Zend_Controller_Action
{
    public function init()
    {
        $this->headStyleScript = array(
            'css' => 'service_commercial'
        );
    }
    public function indexAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }
    public function placesbloqueesAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $tableVol = new Table_Vol;
        $lesVols = $tableVol->getVolAVenirToutesLignes();
        
        $tableResa = new Table_Reservation;
        $tableDemander = new Table_Demander;
        $tableAvion = new Table_Avion;
        $tableContenir = new Table_Contenir;
        $lesResasVol = array();
        $nbPlacesReservees = array();
        foreach($lesVols as $unVol)
        {
            $newVols[$unVol['idVol']] = $unVol;
            $lesResasVol[$unVol['idVol']] = $tableResa->getResasParVol($unVol['idVol']); 
            $lesMatriculesAvion[$unVol['idVol']] = $unVol['matriculeAvion'];
        }
        foreach ($lesResasVol as $idVol => $uneResaVol)
        {
            foreach ($uneResaVol as $idUneResa)
            {
                $modeleAvion = $tableAvion->getModele($lesMatriculesAvion[$idVol]);
                $laClasse = $tableResa->getClasse($idUneResa);
                $placesTotales = $tableContenir->getNbPlacesTotales($modeleAvion, $laClasse['idClasse']);
                $nbPlacesReservees[$idVol][$idUneResa] = array(
                        'classe' => $laClasse['nomClasse'],
                        'placeReservees' => $tableDemander->getNbPlacesReservee($idUneResa),
                        'placesTotales' => $placesTotales
                    );
            }
        }
        $this->view->lesVols = $newVols;
        $this->view->tabPlaces = $nbPlacesReservees;
    }
    
}

