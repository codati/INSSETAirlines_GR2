<?php
/**
 * Contrôleur des test
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Groupe2 <webmasters@insset-airline.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Planning
 */

/**
 * Classe du contrôleur test
 * 
 * @category INSSET
 * @package  Airline
 * @author   Groupe2 <webmasters@insset-airline.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Planning
 */
class TestController extends Zend_Controller_Action
{
    /**
     * Méthode d'initialisation du contrôleur.
     * Permet de déclarer les css & js à utiliser.
     * 
     * @return null
     */
    public function init() { 
        $this->headStyleScript = array();
        if (!session_encours()) {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }        
    }	
    
    /**
     * vue index
     * 
     * @return null
     */
    public function indexAction() 
    {
        echo 'ya plus rien d\'interressant a voir ici !';
    }
}
