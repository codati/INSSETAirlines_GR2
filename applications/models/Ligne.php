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
            $tableligne = new Table_Ligne;
            $lignes = $tableligne->fetchAll();
            return $lignes;

        }

        //Renvoie le nb de vols de cette ligne
	public function getNbVolsDisponibles($idLigne)
	{
		$tableVol = new Table_Vol;
		$reqVol= $tableVol->select()
                                  ->from($tableVol)
                                  ->where('idLigne = ?', $idLigne);
		return $reqVol->query()->rowCount();
	}

    }
?>
