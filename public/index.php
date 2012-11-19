<?php 
defined ('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) .'/../applications'));
defined ('LIBRARY_PATH') || define('LIBRARY_PATH', realpath(dirname(__FILE__) .'/../library'));

defined ('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// on modifie l include path php
set_include_path(implode(PATH_SEPARATOR, array(realpath(LIBRARY_PATH), get_include_path())));

// masque les erreurs notice et depreciées
error_reporting(!E_NOTICE & !E_DEPRECATED);

// on a besoin de zend app pour lance lappli
require_once 'Zend/Application.php';

// on lance la session
require_once 'Zend/Session.php';
Zend_Session::start();

// on cree lappli et on lance le bootstrap
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH .'/config/application.ini');
$application->bootstrap()->run();

