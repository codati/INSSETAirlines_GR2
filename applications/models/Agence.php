<?php
    class Table_Agence extends Zend_Db_Table_Abstract
    {
        protected $_name = 'agence';
        protected $_primary = 'idAgence';
        
        public function login($user, $psw)
        {
            $reqUtil = $this->select()
                 ->from(array('a' => 'agence'), array('*'))
                 ->where('a.nomAgence = ?', $user)
                 ->where('a.mdpAgence = ?', $psw)
                ;
            $laAgence = $this->fetchRow($reqUtil);

            if(!is_null($laAgence))
            {
                $identifiants = array( 
                    'idUtilisateur' => $laAgence->idAgence,
                    'nomUtilisateur' => $laAgence->nomAgence             
                    );
                return $identifiants;
            }
            else 
            {
                return null;
            }
        }
}