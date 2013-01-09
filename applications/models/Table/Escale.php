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
		 * @author : Vermeulen Maxime
		 * Retournes les informations sur les escales d'un vol
		 * @param int $idVol : L'id du vol
		 * @return array : Les informations pour chaque escales du vol
		 */
		public function get_InfosEscales($idVol)
		{
			$reqInfosEscales = $this->select()->setIntegrityCheck(false);
			$reqInfosEscales->from(array('e' => 'escale'), array(
                                                'datehArriveeEffectiveEscale',
                                                'datehArriveePrevueEscale',
                                                'datehDepartEffectiveEscale',
                                                'datehDepartPrevueEscale'
                                        ))
                                        ->join(array('ae' => 'aeroport'), 'ae.trigrammeAeroport=e.trigrammeAeroport', 'nomAeroport')
                                        ->join(array('d' => 'desservir'), 'd.trigrammeAeroport=e.trigrammeAeroport', '')
                                        ->join(array('v' => 'ville'), 'v.idVille=d.idVille', 'nomVille')
                                        ->join(array('p' => 'pays'), 'p.idPays=v.idPays', 'nomPays')
                                        ->where('e.idVol='.$idVol);

			//echo $reqInfosEscales->assemble();
			//exit;
			
			try {$resInfosEscales = $this->fetchAll($reqInfosEscales);}
			catch (Zend_Db_Exception $e) {die ($e->getMessage());}
			
			if($resInfosEscales) {return $resInfosEscales->toArray();}
			else {return false;}
		}
    }
