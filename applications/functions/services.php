<?php
/**
 * Les fonctions en rapport avec les services
 */

/**
 * Vérifie que l'user est accès au service
 * @param string $nomService : Le nom du service
 * @return bool : true si ok, false sinon
 */
function Services_verifAcces($nomService)
{
	if(Zend_Session::isStarted())
	{
		$espaceSession = new Zend_Session_Namespace('utilisateurCourant');
		
		if(isset($espaceSession->lesServicesUtilisateur))
		{
			if(in_array($nomService, $espaceSession->lesServicesUtilisateur)) {return true;}
		}
	}
	
	return false;
}
	