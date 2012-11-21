<?php
class Table_Utilisateur extends Zend_Db_Table_Abstract
{
    protected $_name = 'utilisateur';
    protected $_primary = 'idUtilisateur';
    
    public function login($user, $psw)
    {
        $reqUtil = $this->select()
             ->from(array('u' => 'utilisateur'), array('*'))
             ->where('u.nomUtilisateur = ?', $user)
             ->where('u.mdpUtilisateur = ?', $psw)
            ;
        $leUtilisateur = $this->fetchRow($reqUtil);
        
        $identifiants = array( 
                'idUtilisateur' => $leUtilisateur->idUtilisateur,
                'nomUtilisateur' => $leUtilisateur->nomUtilisateur             
                );
       return $identifiants;
    }
}

?>
