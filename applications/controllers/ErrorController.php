<?php
/**
 * Contrôleur des erreurs
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Elie DHERVILLE <eliedherville@hotmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Error
 */

/**
 * Classe du contrôleur error
 * 
 * @category INSSET
 * @package  Airline
 * @author   Elie DHERVILLE <eliedherville@hotmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Error
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * @var : Object permettant l'identification du type d'erreur
     */
    private $_exception;
	
    /**
     * @var : Message correspondant au code d'erreur HTTP et envoyé à la vue
     */
    private static $errorMessage;
	
    /**
     * @var : Code de l'erreur HTTP détecté par la fonction preDispatch()
     */
    private static $httpCode;
    
    /**
     * Detecte le type d'erreur HTML et traite cette valeur pour choisir l'un des
     * messages personnalisés à envoyer à la vue
     * 
     * @return null
     */
    public function preDispatch()
    {
    	$this->_exception = $this->_getParam('error_handler');
    	switch ($this->_exception->type) {
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            self::$httpCode = 404;
            self::$errorMessage = 'Page introuvable';
            break;
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
            switch (get_class($this->_exception->exception)) {
            case 'Zend_View_Exception' :
                self::$httpCode = 500;
                self::$errorMessage = 'Erreur de traitement d\'une vue';
                break;
            case 'Zend_Db_Exception' :
                self::$httpCode = 503;
                self::$errorMessage = 'Erreur de traitement dans la base de données';
                break;
            case 'Metier_Exception' :
                self::$httpCode = 200;
                self::$errorMessage = $this->_exception->exception->getMessage();
                break;
            case 'Zend_Controller_Action_Exception' :
                self::$httpCode = $this->_exception->exception->getCode(); 
                self::$errorMessage = 'Vous n\'avez pas les droits nécéssaires pour accéder à cette page !';
                break;
    		default:
                    self::$httpCode = 500;
                    self::$errorMessage = 'Erreur inconnue : '. $this->_exception->exception->getMessage();
                    break;
        	}
        	break;
    	}
    	
    }
    
    /**
     * Recupère et envoi le code d'erreur HTTP avec le message correspondant à la vue
     * 
     * @return null
     */
    public function errorAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head'=>$this->headStyleScript));
    	$this->view->message = self::$errorMessage;
    	$this->view->httpCode = self::$httpCode;
    }
    
}

