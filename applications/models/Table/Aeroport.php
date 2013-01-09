<?php
    class Table_Aeroport extends Zend_Db_Table_Abstract
    {
        protected $_name = 'aeroport';
        protected $_primary = 'trigrammeAeroport';
        
        public function getTrigrammes()
        {
            $req = $this->select()
                        ->from($this->_name,'trigrammeAeroport')
                ;
            $res = $this->_db->fetchCol($req);
            return $res;
        }
    }