<?php
/**
 * Contrôleur des index
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Groupe2 <webmasters@insset-airline.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Index
 */

/**
 * Classe du contrôleur index
 * 
 * @category INSSET
 * @package  Airline
 * @author   Groupe2 <webmasters@insset-airline.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Error
 */
class IndexController extends Zend_Controller_Action
{
	/**
	 * Méthode d'initialisation du contrôleur.
	 * Permet de déclarer les css & js à utiliser.
	 * 
	 * @return null
	 */
    public function init()
    {
		$this->headStyleScript = array(
			'css' => array('nivo-slider'),
			'js' => 'jquery.nivo.slider.pack'
		);
    }	
    
	/**
	 * Page index
	 * 
	 * @return null
	 */
    public function indexAction()
    {
        $this->view->msgDoitCo = $this->_getParam('nonCo');
        $this->view->msgDeco = $this->_getParam('decoReussie');
    	$this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
    }	
    
	/**
	 * L'header des pages
	 * 
	 * @return null
	 */
    public function headerAction()
    {  
        $this->view->pasCo = session_encours(); // va voir si une session est en cours (agence ou insset)

        $arr = $this->_getParam('head', null);
    	if (is_array($arr)) {
            if (isset($arr['css'])) {
                if (!is_array($arr['css'])) {
                	$arr['css'] = array($arr['css']);
				}
                $css = $arr['css'];
            } else {
            	$css = null;
			}

            if (isset($arr['js'])) {
                if (!is_array($arr['js'])) {
                	$arr['js'] = array($arr['js']);
				}
                $js = $arr['js'];
            } else {
            	$js = null;
			}
        } else {
        	$css = $js = null;
		}

        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $view->css = $css;
        $view->js = $js;

        $this->_helper->viewRenderer->setResponseSegment('header');
        $this->_helper->actionStack('footer', 'index', 'default', array());
    }
	
	/**
	 * Le footer des pages
	 * 
	 * @return null
	 */
    public function footerAction()
    {
         $this->_helper->viewRenderer->setResponseSegment('footer');
    }
	
	/**
	 * Déconnexion
	 * 
	 * @return null
	 */
    public function logoutAction()
    {
        if (session_encours()) {
            Zend_Session::namespaceUnset('utilisateurCourant');
            Zend_Session::namespaceUnset('agenceCourante');
        }
        $this->_helper->actionStack('index', 'index', 'default', array('decoReussie' => 'Déconnexion réussie'));       
    }
	 
	/**
	 * Connexion
	 * 
	 * @return null
	 */
    public function connexionAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('test' => true, 'head' => $this->headStyleScript));  
        
        $user = $this->getRequest()->getPost('input_user');
        $psw = md5($this->getRequest()->getPost('input_psw'));
        $radio = $this->getRequest()->getPost('radio_form_co'); // 0 => insset, 1 => agence
        
        // utilisateur de l'insset
		if ($radio == 0) {
			// connecte l'utilisateur
			$utilisateur = new Table_Utilisateur;
			$leUtilisateur = $utilisateur->login($user, $psw);
			
			//if(is_array($leUtilisateur))
			if (!is_null($leUtilisateur)) { 
				// requete recuperation des services de l'utilisateur   
				$service = new Table_Service;  
				$sousservice = new Table_SousService;
				
				//recupere les services de l'utilisateur
				$lesServices = $service->getLesServices($leUtilisateur['idUtilisateur']);
				
				$tabLesServices = array();
				$tabSousServices = array();
				foreach ($lesServices as $unService) {
					$tabLesServices[] = $unService['nomService'];
					
					// requete recuperation des sous services 
					$tabSousServices[] = $sousservice->getLesSousServices($unService['idService']);
				}      
				// deconnecte une agence, au cas ou...
				// Zend_Session::namespaceUnset('agenceCourante');
				 
				// crée la session de l'utilisateur et ajoute des données nécéssaires
				$espaceSession = new Zend_Session_Namespace('utilisateurCourant');
				$espaceSession->idUtilisateur = $leUtilisateur['idUtilisateur'];
				$espaceSession->nomUtilisateur = $leUtilisateur['nomUtilisateur'];
				$espaceSession->lesServicesUtilisateur = $tabLesServices;
				$espaceSession->lesSousServicesUtilisateur = $tabSousServices;
				$espaceSession->connecte = true;    
			}
		} else {  // radio = 1, c'est une agence
			// connexion de l'agence avec les champs saisis
			$tableAgence = new Table_Agence;
			$agence = $tableAgence->login($user, $psw);
			
			if (session_encours()) {
				Zend_Session::namespaceUnset('utilisateurCourant');
			}
			
			// pour ajouter une action aux agences, ajouter une valeur dans le tableau
			$lesServicesAgences = array(
				'reservations' => 'Reserver des places sur un vol',
				'gererresas' => 'Gerer mes réservations',
			);
			
			// crée la session de l'agence et ajoute des données nécéssaires
			$espaceAgence = new Zend_Session_Namespace('agenceCourante');
			$espaceAgence->idAgence = $agence['idAgence'];
			$espaceAgence->nomAgence = $agence['nomAgence'];
			$espaceAgence->lesServicesAgence = $lesServicesAgences;
			$espaceAgence->connecte = true; 
		}
		
		$redirector = $this->_helper->getHelper('Redirector');
		$redirector->gotoUrl($this->view->baseUrl());        
	}

	/**
	 * Vérifie si l'user est connecté
	 * 
	 * @return null
	 */
    public function verifconnexionAction()
    {
        $user = $this->_getParam('user');
        $psw = md5($this->_getParam('pass'));
        $radio = $this->_getParam('rad');
        
        if ($radio == 0) { // insset
        
            $tableutilisateur = new Table_Utilisateur;
            $utilisateur = $tableutilisateur->login($user, $psw);
            $agence = null;
        } else { // radio == 1, agence
            $tableagence = new Table_Agence;
            $agence = $tableagence->login($user, $psw);
            $utilisateur = null;
        }
	 
	 	if (!is_null($utilisateur) || !is_null($agence)) {
            echo '1';
        } else {
            echo '0';
        }
	 
        exit();
    }
	
	/**
	 * Page de téléchargement de l'appli android
	 * 
	 * @return null
	 */
    public function telechargerAction()
    {
    	$this->headStyleScript['css'][] = 'android';
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript)); 
    }
    
	/**
	 * Page des mentions
	 * 
	 * @return null
	 */
    public function mentionAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
    }
    
	/**
	 * Page de contact
	 * 
	 * @return null
	 */
    public function contactAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
    }
    
	/**
	 * Page de consultation du catalogue
	 * 
	 * @return null
	 */
    public function consulterAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
        $tableLigne = new Table_Ligne;
        $lignes = $tableLigne->getLignes();
        $this->view->lignes= $lignes;
        
        $nbVolsLigne = array();
        foreach ($lignes as $ligne) {
            $nbVolsLigne[$ligne['idLigne']] = $tableLigne->getNbVolsDisponibles($ligne['idLigne']);
        }
        $this->view->nbVolsLigne = $nbVolsLigne;

    }
    
	/**
	 * Page des informations sur les vols (exemple : retard)
	 * 
	 * @return null
	 */
    public function retardAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tableVol = new Table_Vol();
        $retards = $tableVol->GetVolRetardataire();

        $this->view->retards = $retards;
    }
}

