<?php
    class Table_Classe extends Zend_Db_Table_Abstract
    {
        protected $_name = 'classe';
        
        protected $_primary = 'idClasse';
        
    	/**
		 * Liste toutes les classes qu'à un modèle
		 */
		public function get_LstClasses_PourModele($idModele)
		{
			$req = $this->select()->setIntegrityCheck(false)
						->from(array('co' => 'contenir'), array('idClasse', 'nbPlaces'))
						->join(array('cl' => 'classe'), 'cl.idClasse=co.idClasse', 'nomClasse')
						->where('co.idModeleAvion=?', $idModele)
						->order('cl.idClasse ASC');
			$res = $this->fetchAll($req);
			return $res->toArray();
		}
    }