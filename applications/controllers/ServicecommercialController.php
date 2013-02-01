<?php
/**
 * ContrÃ´leur de service commercial
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Kevin Verschaeve <kevin.verschaeve@live.fr>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /servicecommercial
 */

/**
 * Classe du contrÃ´leur service commercial
 * 
 * @category INSSET
 * @package  Airline
 * @author   Kevin Verschaeve <kevin.verschaeve@live.fr>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /servicecommercial
 */
class ServiceCommercialController extends Zend_Controller_Action
{
     /**
	 * MÃ©thode d'initialisation du contrÃ´leur.
	 * Permet de dÃ©clarer les css & js Ã  utiliser.
	 * Verifie que l'on est connectÃ© 
     * Verifie que l'on a les droits nÃ©cÃ©ssaires pour accÃ©der a la page
	 * @return null
	 */
    public function init()
    {
        $this->headStyleScript = array(
            'css' => 'service_commercial'
        );
        if (!session_encours()) {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
        if (!Services_verifAcces('Service commercial')) {
            throw new Zend_Controller_Action_Exception('', 403);
        }
    }
    /**
	 * Action index. Renvoi automatiquement vers l'action volscatalogue
	 * 
	 * @return null
	 */
    public function indexAction() 
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
    }
    /**
    * Renvoi la liste des places rÃ©servÃ©es et libre pour un vol et par classe
    *
    * @return array
    */
    public function placesbloqueesAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
        
        $tableVol = new Table_Vol;
        $lesVols = $tableVol->getVolAVenirToutesLignes();

        $tableResa = new Table_Reservation;
        $tableDemander = new Table_Demander;
        $tableAvion = new Table_Avion;
        $tableContenir = new Table_Contenir;
        $lesResasVol = array();
        $nbPlacesReservees = array();
        foreach ($lesVols as $unVol) {
            $newVols[$unVol['idVol']] = $unVol;
            $lesResasVol[$unVol['idVol']] = $tableResa->getResasParVol($unVol['idVol']); 
            $lesMatriculesAvion[$unVol['idVol']] = $unVol['matriculeAvion'];
        }
        foreach ($lesResasVol as $idVol => $uneResaVol) {
            foreach ($uneResaVol as $idUneResa) {
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

