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

        /**
        * @author : Piercourt Fabien
        * Retournes toutes les lignes de la bdd
        * @return array : Toutes les lignes
        */
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
            ///Zend_Debug::dump($lignes->toArray());exit;
            return $lignes->toArray();
        }
        /**
         * @author Kevin Verschaeve
         * Renvoi les infos sur une ligne dont l'id est passé en parametre
         * @return une ligne
         */
        public function getUneLigne($idLigne)
        {
            $req = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('l'=>'ligne'),array('*'))
                        ->join(array('a'=>'aeroport'),'a.trigrammeAeroport = l.trigrammeAeroportDepart','a.nomAeroport as depart')
                        ->join(array('ae'=>'aeroport'),'ae.trigrammeAeroport = l.trigrammeAeroportArrivee','ae.nomAeroport as arrivee')
                        ->join(array('p'=>'periodicite'),'p.idPeriode = l.idPeriodicite','nomPeriode')
                        ->where('idLigne = ?', $idLigne)
                    ;
            $res = $this->fetchRow($req);
            if($res)
            {
                return $res->toArray();
            }
            else
            {
                return null;
            }
        }
        
        public function getPeriodiciteLigne($idLigne)
        {
            $req = $this->select()
                        ->setIntegrityCheck(false)
                        ->from($this->_name,'idPeriodicite')
                        //->join(array('p'=>'periodicite'),'p.idPeriode = ligne.idPeriodicite','nomPeriode')
                        ->where('idLigne = ?', $idLigne)
                    ;
            $res = $this->_db->fetchOne($req);
            return $res;
            //Zend_Debug::dump($res);exit;return $res;
        }
        /**
        * @author : Piercourt Fabien
        * Renvoie le nb de vols de cette ligne à venir et planifiés
        * @return nb de vols 
        */
	public function getNbVolsDisponibles($idLigne)
	{
                $date = Zend_Date::now(); // date actuelle
		$tableVol = new Table_Vol;
                $imbrique = $this->select()->setIntegrityCheck(false)
                                ->from(array('va'=>'valoir'),'va.idVol');
		$reqVol= $tableVol->select()
                                  ->from($tableVol)
                                  ->where('idLigne = ?', $idLigne)
                                  ->where('dateHeureDepartPrevueVol > ?', $date->getIso())
                                  ->where("idVol IN ($imbrique)")
                                     ;           
		return $reqVol->query()->rowCount();
	}
        public function existeLigne($trigDepart, $trigArrivee)
        {
            $req = $this->select()
                        ->from($this->_name,'*')
                        ->where('trigrammeAeroportDepart = ?', $trigDepart)
                        ->where('trigrammeAeroportArrivee = ?', $trigArrivee)
                        ;
            $res = $this->fetchAll($req)->toArray();
            $nbRes = count($res);
            if($nbRes > 1)
            {
                return true;
            }
            else
            {
                if($nbRes == 1)
                {
                    return $this->fetchRow($req)->toArray();
                }
                else
                {
                    return false;
                }
            }
        }
        public function ajouter($donnees)
        {
            $this->insert($donnees);
        }
        public function modifier($donnees, $idLigne)
        {
            $where = $this->getAdapter()->quoteInto('idLigne = ?', $idLigne);
            $this->update(array(
            	'idPeriodicite' => $donnees['idPeriodicite'],
            	'trigrammeAeroportDepart' => $donnees['trigrammeAeroportDepart'],
            	'trigrammeAeroportArrivee' => $donnees['trigrammeAeroportArrivee']
			), $where);
        }
		
		/**
		 * Retourne les infos sur la ligne
		 * @author Vermeulen Maxime
		 * @param int $idLigne : L'id de la ligne
		 * @return array : Les infos sur la ligne
		 */
		public function getInfoLigne($idLigne)
		{
			$req = $this->select()->setIntegrityCheck(false)
                        ->from(array('l'=>'ligne'))
                        ->join(array('a'=>'aeroport'),'a.trigrammeAeroport = l.trigrammeAeroportDepart','a.nomAeroport as depart')
                        ->join(array('ae'=>'aeroport'),'ae.trigrammeAeroport = l.trigrammeAeroportArrivee','ae.nomAeroport as arrivee')
                        ->join(array('p'=>'periodicite'),'p.idPeriode = l.idPeriodicite','nomPeriode')
						->where('l.idLigne=?',$idLigne);
			
			try {$res = $this->fetchRow($req);}
			catch (Zend_Db_Exception $e) {die ($e->getMessage());}
			
			return $res->toArray();
		}
		
        /**
         * @author Vermeulen Maxime
         * Retourne les trigrammes des aéroports de départ et d'arrivée de la ligne
         * @return array : Les trigrammes
         */
        public function getTrigAeroLigne($idLigne)
        {
            $req = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('l'=>'ligne'),array(
							'trigDepart' => 'trigrammeAeroportDepart',
							'trigArrivee' => 'trigrammeAeroportArrivee',
						))
						->where('idLigne = ?', $idLigne);
					
            $res = $this->fetchRow($req);
            if($res) {return $res->toArray();}
            else {return null;}
        }
    }
?>
