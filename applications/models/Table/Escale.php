<?php
    class Table_Escale extends Zend_Db_Table_Abstract
    {
        protected $_name = 'escale';
        
        protected $_primary = 'numeroEscale';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Aeroport' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'Table_Aeroport'
                    )
            );

		/**
		 * Retournes les informations sur les escales d'un vol
		 * @author : Vermeulen Maxime
		 * @param int $idVol : L'id du vol
		 * @return array : Les informations pour chaque escales du vol
		 */
		public function get_InfosEscales($idVol)
		{
			$reqInfosEscales = $this->select()->setIntegrityCheck(false);
			$reqInfosEscales->from(array('e' => 'escale'), array(
												'numeroEscale',
                                                'datehArriveeEffectiveEscale',
                                                'datehArriveePrevueEscale',
                                                'datehDepartEffectiveEscale',
                                                'datehDepartPrevueEscale',
                                                'trigrammeAeroport'
                                        ))
                                        ->join(array('ae' => 'aeroport'), 'ae.trigrammeAeroport=e.trigrammeAeroport', 'nomAeroport')
                                        ->join(array('d' => 'desservir'), 'd.trigrammeAeroport=e.trigrammeAeroport', '')
                                        ->join(array('v' => 'ville'), 'v.idVille=d.idVille', 'nomVille')
                                        ->join(array('p' => 'pays'), 'p.idPays=v.idPays', 'nomPays')
                                        ->where('e.idVol='.$idVol)
										//->order('e.datehArriveePrevueEscale ASC');
										->order('e.numeroEscale ASC');

			//echo $reqInfosEscales->assemble();
			//exit;
			
			try {$resInfosEscales = $this->fetchAll($reqInfosEscales);}
			catch (Zend_Db_Exception $e) {die ($e->getMessage());}
			
			if($resInfosEscales) {return $resInfosEscales->toArray();}
			else {return false;}
		}

		public function ajouter($idVol, $numEscale, $arriverDate, $departDate, $aeroport)
		{
			$data = array
			(
				'numeroEscale' => $numEscale,
				'datehArriveePrevueEscale' => $arriverDate,
				'datehDepartPrevueEscale' => $departDate,
				'idVol' => $idVol,
				'trigrammeAeroport' => $aeroport
			);
			
			$this->insert($data);
		}

		public function supprAllEscale($idVol)
		{
			$where[] = $this->getAdapter()->quoteInto('idVol = ?', $idVol);
            $this->delete($where);
		}
    }
