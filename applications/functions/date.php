<?php
/**
 * Les fonctions en rapport avec les dates
 */

/**
 * Retourne la date au format sql
 * 
 * @param &Zend_Date : L'instance Zend_Date de la date
 * @return string : La date au format sql
*/
function DateFormat_SQL(&$Zend_Date, $complet=true)
{
    if($complet == true) 
    {
        return $Zend_Date->toString('YYYY-MM-dd HH:mm:ss');
    }
    else
    {
        return $Zend_Date->toString('YYYY-MM-dd');
    }
}
 
/**
 * Retourne la date pour l'affichage
 * @param &Zend_Date : L'instance Zend_Date de la date
 * @param bool [opt] : Pour tout afficher ou non. Si non on affiche pas les heures/minutes. Par défault on affiche tout.
*/
function DateFormat_View(&$Zend_Date, $complet=true, $jour=true)
{
	$formatJour = '';
	if($jour == true) {$formatJour = 'EEEE ';}
	
	if($complet == true) {return $Zend_Date->toString($formatJour.'dd MMMM YYYY à HH:mm');}
	else {return $Zend_Date->toString($formatJour.'dd MMMM YYYY');}
} 