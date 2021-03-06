<?php
    class Table_Assurer extends Zend_Db_Table_Abstract
    {
        protected $_name = 'assurer';
        protected $_primary = array('idVol', 'idPilote');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Pilote' => array(
                    'columns' => 'idPilote',
                    'refTableClass' => 'Table_Pilote'
                    )
            );

		/**
		 * Ajoute un pilote à un vol
		 * @param int $idVol : L'id du vol
		 * @param int $idPilote : L'id du pilote
		 */
		public function insertPilote($idVol, $idPilote) {$this->insertData($idVol, $idPilote, 'pilote');}
		
		/**
		 * Ajoute un co-pilote à un vol
		 * @param int $idVol : L'id du vol
		 * @param int $idCoPilote : L'id du co-pilote
		 */
		public function insertCoPilote($idVol, $idCoPilote) {$this->insertData($idVol, $idCoPilote, 'co-pilote');}
		
		/**
		 * Ajoute une ligne dans la table
		 * @param int $idVol : L'id du vol
		 * @param int $idPilote : L'id du pilote
		 * @param string $role : "pilote" ou "co-pilote"
		 */
		public function insertData($idVol, $idPilote, $role)
		{
			$data = array('idVol' => $idVol, 'idPilote' => $idPilote, 'role' => $role);
			$this->insert($data);
		}
                /**
                 * retourne l'id du pilote pour un vol
                 * @param int $idVol : l'id du vol
                 * @return int $idPilote : l'id du pilote
                 */
                public function getIdPilote($idVol)
                {
                    $req = $this->select()
                                ->from($this->_name,'idPilote')
                                ->where('assurer.idVol = ?', $idVol)
                                ->where('role = ?','pilote')
                            ;
                    return $this->_db->fetchOne($req);
                }
                /**
                 * retourne l'id du copilote pour un vol
                 * @param int $idVol : l'id du vol
                 * @return int $idPilote : l'id du copilote
                 */
                public function getIdCoPilote($idVol)
                {
                    $req = $this->select()
                                ->from($this->_name,'idPilote')
                                ->where('assurer.idVol = ?', $idVol)
                                ->where('role = ?','co-pilote')
                            ;
                    //echo $req->assemble();exit;
                    return $this->_db->fetchOne($req);
                }
                /**
                 * supprime un vol pour un pilote
                 * @param int $idVol : l'id du vol
                 * @param int $idPilote : l'id du pilote
                 */
                public function deleteVolPilote($idVol, $idPilote)
                {
                    $this->delete(array('idVol' => $idVol,'idPilote' => $idPilote));
                }
    }