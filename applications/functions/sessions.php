<?php
/**
 * Les fonctions en rapport avec les sessions
 */

// sert a savoir si on est connecté
function session_encours()
{
    if((Zend_Session::namespaceIsset('utilisateurCourant')) || (Zend_Session::namespaceIsset('agenceCourante')))
    {
        $connecte = true;
    }
    else 
    {
        $connecte = null;    
    }
    return $connecte;
}

?>
