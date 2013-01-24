<?php
    class Table_TypeRepas extends Zend_Db_Table_Abstract
    {
        protected $_name = 'typerepas';
        protected $_primary = 'idTypeRepas';
        
        public function getLibelles()
        {
            $req = $this->select()
                        ->from($this->_name)
                    ;
            return $this->fetchAll($req)->toArray();
        }
    }