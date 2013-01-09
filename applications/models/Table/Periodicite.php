<?php
    class Table_Periodicite extends Zend_Db_Table_Abstract
    {
        protected $_name = 'periodicite';
        protected $_primary = 'idPeriode';
        
        public function getPeriodicites()
        {
            $req = $this->select()
                        ->from($this->_name,'*')
                        ;
            return $this->fetchAll($req)->toArray();
        }
    }