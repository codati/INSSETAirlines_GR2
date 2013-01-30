<?php
    class Table_Valoir extends Zend_Db_Table_Abstract
    {
        protected $_name = 'valoir';
        protected $_primary = array('idVol', 'idClasse');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Classe' => array(
                    'columns' => 'idClasse',
                    'refTableClass' => 'Table_Classe'
                    )
            );
        public function getTarifsVol($idVol)
               {
                    $req = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('va'=>'valoir'),array('va.prixUnitaire', 'va.pourcentagePromo', 'va.dateDebutPromo', 'va.dateFinPromo'))
                    ->join(array('c'=>'classe'),'c.idClasse = va.idClasse', array('c.nomClasse'))
                    ->where('idVol= ?', $idVol)
                    ;
                   $tarifs = $this->fetchAll($req)->toArray();
                    //if(count($tarifs) > 0) 
                   if(!is_null($tarifs))
                    {
                         return $tarifs;
                    }
               }

		public function insertPrixVol($idVol, $idClasse, $prix)
		{
			$data = array('idVol' => $idVol, 'idClasse' => $idClasse, 'prixUnitaire' => $prix);
			try {$this->insert($data);}
			catch (Zend_Db_Exception $e) {die ($e->getMessage());}
		}
          
          public function existePromo($idVol)
          {
               
          }
    }