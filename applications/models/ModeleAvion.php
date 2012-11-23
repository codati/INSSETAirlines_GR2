<?php
    class Table_ModeleAvion extends Zend_Db_Table_Abstract
    {
        protected $_name = 'modeleavion';
        
        protected $_primary = 'idModeleAvion';
        
        
        public function GetListLibelle()
        {
            $laListe = $this->select()
                    ->from(array('modeleavion'), array('idModeleAvion','libelleModeleAvion'));
                        
            return $this->fetchAll($laListe)->toArray();
        }
    }