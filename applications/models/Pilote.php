<?php
    class Table_Pilote extends Zend_Db_Table_Abstract
    {
        protected $_name = 'pilote';
        
        protected $_primary = 'idPilote';
        
        /**
         * Retourne la liste des pilotes disponible pour un modèle d'avion et entre deux date
         * @param int $idModele : L'id du modèle d'avion voulu
         * @param string $dateDepart : La date de départ au format SQL
         * @param string $dateArrivee : La date d'arrivée au format SQL
         * @return array : La liste des pilotes disponible
         */
		public function get_LstPiloteDispo_PourModele($idModele, $dateDepart, $dateArrivee)
        {
        	//echo 'test';exit;
        	$dateDeb = new Zend_Date($dateDepart);
        	$dateDeb->addHour(5);
        	$dateDebSql = DateFormat_SQL($dateDeb);
        	
        	$dateValide = new Zend_Date($dateArrivee);
        	$dateValideSQL = $dateValide->toString('YYYY-MM-dd');
        	
        	$reqDate = $this->select()->setIntegrityCheck(false)
        						->from(array('v' => 'vol'), 'COUNT(v.idVol)')
        						->join(array('a' => 'assurer'), 'a.idVol=v.idVol', '')
        						->where('a.idPilote=p.idPilote')
        						->where('dateHeureDepartPrevueVol >= "'.$dateDebSql.'"')
        						->where('dateHeureArriveePrevueVol <= "'.$dateArrivee.'"');
        	
        	$req = $this->select()->setIntegrityCheck(false)
        				->from(array('p' => 'pilote'), array('idPilote', 'nomPilote', 'prenomPilote'))
        				->join(array('b' => 'breveter'), 'b.idPilote=p.idPilote', '')
        				->where('b.idModeleAvion="'.$idModele.'"')
        				->where('b.dateValiditeBrevet>="'.$dateValideSQL.'"')
        				->where('('.new Zend_Db_Expr($reqDate).') = 0');
        	//echo $req->assemble();exit;
        	
        	$res = $this->fetchAll($req)->toArray();
        	//echo '<pre>';print_r($res);echo '</pre>';
        	return $res;
        }
        
    	/**
		 * Récupère le nom et le prénom du pilote
		 * @param int $idPilote : L'id du pilote
		 * @return array : Le nom et le prénom du pilote
		 */
		public function get_NomPrenom($idPilote)
		{
			$req = $this->select()->from($this->_name, array('nomPilote', 'prenomPilote'))->where('idPilote=?', $idPilote);
			$res = $this->fetchRow($req)->toArray();
			return $res;
		}
    }