<?php

class ErrorController extends Zend_Controller_Action
{
    private $_exception;
    private static $errorMessage;
    private static $httpCode;
    
    public function init()
    {
        $this->headStyleScript = array('css' => 'error');
    }
    public function preDispatch()
    {
    	$this->_exception = $this->_getParam('error_handler');
        
    	switch ($this->_exception->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                self::$httpCode = 404;
                self::$errorMessage = 'Erreur : Page introuvable';
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
            		default:
            			self::$httpCode = 500;
            			self::$errorMessage = 'Erreur inconnue :<br> '. $this->_exception->exception->getMessage();
            		break;
            	}
            break;
    	}
    	
    }
    
    public function errorAction()
    {
        $this->_helper->actionStack('header','index','default',array('head'=>  $this->headStyleScript));
    	$this->view->message = self::$errorMessage;
    	$this->view->httpCode = self::$httpCode;
    }
    
}

