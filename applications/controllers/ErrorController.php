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
     * @var : Description à mettre.
     */
    private $_exception;
	
    /**
     * @var : Description à mettre.
     */
    private static $errorMessage;
	
    /**
     * @var : Description à mettre.
     */
    private static $httpCode;
    
    public function init()
    {
        $this->headStyleScript = array('css'=>'error'); 
    }
    /**
     * Description à mettre.
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
	 * Description à mettre.
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

