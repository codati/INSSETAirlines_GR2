<?php
/**
 * Les fonctions en rapport avec les sessions
 */

// sert a savoir si on est connecté
function session_encours()
{
    Zend_Session::start();
    if((Zend_Session::namespaceIsset('utilisateurCourant')) || (Zend_Session::namespaceIsset('agenceCourante')))
    {
        $connecte = true;
    }
    else 
    {
       // Zend_Debug::dump($_SESSION);exit;
        $connecte = null;    
    }
    return $connecte;
}

?>
