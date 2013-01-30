<?php
    class Table_Aeroport extends Zend_Db_Table_Abstract
    {
        protected $_name = 'aeroport';
        protected $_primary = 'trigrammeAeroport';
        
        public function getAeroports()
        {
            $req = $this->select()
                        ->from($this->_name,array('trigrammeAeroport', 'nomAeroport'))
                ;
            $res = $this->fetchAll($req)->toArray();
            return $res;
        }
		
		public function getNomAeroport($trigramme)
		{
			$req = $this->select()->from($this->_name, 'nomAeroport')->where('trigrammeAeroport=?', $trigramme);
			$res = $this->fetchRow($req)->toArray();
			return $res;
		}
    }