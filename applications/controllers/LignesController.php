<?php
/**
 * Contrôleur des lignes
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Kevin Verschaeve <kevin.verschaeve@live.fr>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Lignes
 */

/**
 * Classe du contrôleur lignes
 * 
 * @category INSSET
 * @package  Airline
 * @author   Kevin Verschaeve <kevin.verschaeve@live.fr>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Lignes
 */
class LignesController extends Zend_Controller_Action
{
    /**
	 * Méthode d'initialisation du contrôleur.
	 * Permet de déclarer les css & js à utiliser.
	 * 
	 * @return null
	 */
	public function init() 
    {
        $this->headStyleScript = array();
        if (!session_encours()) {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
    }
	
	/**
	 * Page index des lignes
	 * 
	 * @return null
	 */
    public function indexAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
    }
    
	/**
	 * Ajouter une ligne (sql)
	 * 
	 * @return null
	 */
    public function ajouterAction()
    {
        $trigDepart = $this->getRequest()->getPost('trigDepart');
        $trigArrivee = $this->getRequest()->getPost('trigArrivee');
        $periode = $this->getRequest()->getPost('periodicite');
        
        if ($trigDepart == $trigArrivee) {
            $message = "<div class=\"erreur\">L'aéroport de départ doit être différent de l'aéroport d'arrivée</div>";
        } else {
            $tableLigne = new Table_Ligne;
            $existe = $tableLigne->existeLigne($trigDepart, $trigArrivee);
            
            if (!$existe) {
                //Zend_Debug::dump($existe);exit;
                $donnees = array(
                    'trigrammeAeroportDepart' => $trigDepart,
                    'trigrammeAeroportArrivee' => $trigArrivee,
                    'idPeriodicite' => $periode
                );                
                $tableLigne->ajouter($donnees);
                $message = '<div class="reussi">Ligne créée !</div>';
            } else {
                //Zend_Debug::dump($existe);exit;
                $message = '<div class="information">Cette ligne existe déja !<br />
                    Souhaitez vous <a href="'.$this->view->baseUrl('/directionstrategique/modifierligne/idligne/'.$existe['idLigne']).'">la modifier</a> ?</div>';
            }
        }
        
        $this->_helper->FlashMessenger($message);
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoUrl($this->view->baseUrl('/directionstrategique/ajouterligne'));
    }
	
	/**
	 * Modifier une ligne (sql)
	 * 
	 * @return null
	 */
    public function modifierAction()
    {
        $trigDepart = $this->getRequest()->getPost('trigDepart');
        $trigArrivee = $this->getRequest()->getPost('trigArrivee');
        $idPeriode = $this->getRequest()->getPost('periodicite');
		
        $idLigne = $this->_getParam('idligne');
        // echo $idPeriode;exit;
        try {
	        $data = array(
	        	'idPeriodicite' => $idPeriode,
            	'trigrammeAeroportDepart' => $trigDepart,
            	'trigrammeAeroportArrivee' => $trigArrivee
			);
			
	        $tableLigne = new Table_Ligne;
			$tableLigne->modifier($data, $idLigne);
        } catch(Exception $e) {
            echo $e->getMessage();exit;
        }
		
        $this->_helper->FlashMessenger('<div class="reussi">Modification terminée</div>');
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoUrl($this->view->baseUrl('/directionstrategique/modifierligne/ligne/'.$idLigne));
    }
}