<?php
    class Table_Reservation extends Zend_Db_Table_Abstract
    {
        protected $_name = 'reservation';
        
        protected $_primary = 'idReservation';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Classe' => array(
                    'columns' => 'idClasse',
                    'refTableClass' => 'Table_Classe'
                     ),
                'TypeRepas' => array(
                    'columns' => 'idTypeRepas',
                    'refTableClass' => 'Table_TypeRepas'
                    )
            );
		
		/**
		 * @author : Vermeulen Maxime
		 * Retourne les informations à propos d'une escales
		 * @param int $idResa : L'id de la réservation
		 * @return array : Un tableau contenant toutes les informations sur la réservation. 
		 */
		public function getInfosResa($idResa)
		{
			//On fait la requête pour récuperer les infos de la réservation
			$reqNbEscales = $this->select()->setIntegrityCheck(false);
			$reqNbEscales->from('escale', 'COUNT(numeroEscale)')
						 ->where('idVol=v.idVol');
			//SELECT COUNT(numeroEscale) FROM escale WHERE idVol=v.idVol
			//echo $reqNbEscales->assemble();
			
			$reqInfo_resa = $this->select()->setIntegrityCheck(false);
			$reqInfo_resa->from(array('r' => 'reservation'), array(
							//'etatReservation' => 'etatReservation',
							'nbEscale' => '('.new Zend_Db_Expr($reqNbEscales).')'
						 ))
						 ->join(array('c' => 'classe'), 'c.idClasse=r.idClasse', 'nomClasse')
						 ->join(array('rep' => 'typerepas'), 'rep.idTypeRepas=r.idTypeRepas', 'nomTypeRepas')
						 
						 ->join(array('v' => 'vol'), 'v.idVol=r.idVol', array(
						 	'idVol', 
						 	'remarqueVol', 
						 	'dateHeureDepartEffectiveVol',
						 	'dateHeureDepartPrevueVol',
						 	'dateHeureArriveeEffectiveVol',
						 	'dateHeureArriveePrevueVol'
						 ))
						 ->join(array('l' => 'ligne'), 'l.idLigne=v.idLigne', '')
						 
						 ->join(array('aeDep' => 'aeroport'), 'aeDep.trigrammeAeroport=l.trigrammeAeroportDepart', array('nomAeroportDepart' => 'nomAeroport'))
						 ->join(array('dDep' => 'desservir'), 'dDep.trigrammeAeroport=aeDep.trigrammeAeroport', '')
						 ->join(array('vDep' => 'ville'), 'vDep.idVille=dDep.idVille', array('villeDepart' => 'nomVille'))
						 ->join(array('pDep' => 'pays'), 'pDep.idPays=vDep.idPays', array('paysDepart' => 'nomPays'))
						 
						 ->join(array('aeArr' => 'aeroport'), 'aeArr.trigrammeAeroport=l.trigrammeAeroportArrivee', array('nomAeroportArrivee' => 'nomAeroport'))
						 ->join(array('dArr' => 'desservir'), 'dArr.trigrammeAeroport=aeArr.trigrammeAeroport', '')
						 ->join(array('vArr' => 'ville'), 'vArr.idVille=dArr.idVille', array('villeArrivee' => 'nomVille'))
						 ->join(array('pArr' => 'pays'), 'pArr.idPays=vArr.idPays', array('paysArrivee' => 'nomPays'))
						 
						 ->where('r.idReservation='.$idResa);
			
			//echo $reqInfo_resa->assemble();
			//exit;
			
			try {$resInfo_resa = $this->fetchRow($reqInfo_resa);}
			catch (Zend_Db_Exception $e) {die ($e->getMessage());}
			
			//echo '<pre>';print_r($resInfo_resa);echo '</pre>';exit;
			if($resInfo_resa) {return $resInfo_resa->toArray();}
			else {return false;}
		}
                public function getIdResaVol($idVol, $classe)
                {
                    $req = $this->select()
                                ->from($this->_name, 'idReservation')
                                ->where('idVol = ?', $idVol)
                                ->where('idClasse = ?', $classe)
                                ;
                    
                    $res = $this->_db->fetchOne($req);
                    return isset($res) ? $res : NULL;
                }
                public function nouvelleResa($donnees)
                {
                    $this->insert($donnees);
                }
           
        public function getVolEtClasse($idResa)
        {
            $req = $this->select()
                        ->from($this->_name, array('idClasse','idVol'))
                        ->where('idReservation = ?',$idResa)
                    ;
            
            return $this->_db->fetchRow($req);
        }
        public function supprimerReservation($idResa)
        {
            $where = $this->getAdapter()->quoteInto('idReservation = ?', $idResa);
            $this->delete($where);
        }
    }
