<?php
    class Table_Ligne extends Zend_Db_Table_Abstract
    {
        protected $_name = 'ligne';
        protected $_primary = 'idLigne';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'AeroportDepart' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'Table_Aeroport'
                     ),
                'AeroportArrivee' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'Table_Aeroport'
                    ),
                'Periodicite' => array(
                    'columns' => 'idPeriodicite',
                    'refTableClass' => 'Table_Periodicite'
                    )
            );
        
        /*************** Fonctions ***************/

        //Renvoie toutes les lignes
        public function getLignes()
        {            
            $req = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('l'=>'ligne'),array('*'))
                        ->join(array('a'=>'aeroport'),'a.trigrammeAeroport = l.trigrammeAeroportDepart','a.nomAeroport as depart')
                        ->join(array('ae'=>'aeroport'),'ae.trigrammeAeroport = l.trigrammeAeroportArrivee','ae.nomAeroport as arrivee')
                        ->join(array('p'=>'periodicite'),'p.idPeriode = l.idPeriodicite','nomPeriode')
                    ;
            $lignes = $this->fetchAll($req);
            return $lignes;
            
        }

        //Renvoie le nb de vols de cette ligne
	public function getNbVolsDisponibles($idLigne)
	{
                $date = Zend_Date::now(); // date actuelle
		$tableVol = new Table_Vol;
		$reqVol= $tableVol->select()
                                  ->from($tableVol)
                                  ->where('idLigne = ?', $idLigne)
                                  ->where('dateHeureDepartPrevueVol > ?', $date->getIso())
                        ;
                                  
		return $reqVol->query()->rowCount();
	}

    }
?>
