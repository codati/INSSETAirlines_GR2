<?php
    class Table_Agence extends Zend_Db_Table_Abstract
    {
        protected $_name = 'agence';
        protected $_primary = 'idAgence';
        
        public function login($user, $psw)
        {
            $reqAgence = $this->select()
                 ->from(array('a' => 'agence'), array('*'))
                 ->where('a.nomAgence = ?', $user)
                 ->where('a.mdpAgence = ?', $psw)
                ;
            $laAgence = $this->fetchRow($reqAgence);

            if(!is_null($laAgence))
            {
                $identifiants = array(
                    'idAgence' => $laAgence->idAgence,
                    'nomAgence' => $laAgence->nomAgence             
                    );
                //Zend_Debug::dump($identifiants);exit;
                return $identifiants;
            }
            else 
            {
                return null;
            }
        }
}