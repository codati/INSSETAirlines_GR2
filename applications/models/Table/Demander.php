<?php
    class Table_Demander extends Zend_Db_Table_Abstract
    {
        protected $_name = 'demander';
        protected $_primary = array('idAgence', 'idReservation');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Agence' => array(
                    'columns' => 'idAgence',
                    'refTableClass' => 'Table_Agence'
                     ),
                'Reservation' => array(
                    'columns' => 'idReservation',
                    'refTableClass' => 'Table_Reservation'
                    )
            );
        /**
         * retourne le nombre de places reservees pour un vol
         * @param array $resasVol : tableau d'id de reservations pour un vol
         * @return int $nbPlacesReservee : nombre de places reservees pour ce vol
         */
        public function getNbPlacesReservee($resasVol)
        {
            $req = $this->select()
                        ->from($this->_name, 'SUM(nbPlacesReservees) as nbPlacesReservees')
                        ->where('idReservation IN (?)', $resasVol)
                        ;
           $res = $this->_db->fetchOne($req);
           return isset($res) ? $res : 0;
        }
        /**
         * Permet de savoir si une demande existe deja en fontion de l'agence et de la reservation
         * @param int $idAgence : id de lagence
         * @param int $idResaVol : id de la reservation du vol
         * @return bool : true si on trouve un resultat, false sinon
         */
        public function existeDemande($idAgence, $idResaVol)
        {
            return ((bool)$this->find($idAgence, $idResaVol)->toArray());
        }
        public function reserver($donnees)
        {
            try {
                $this->insert($donnees);
            }
            catch(Exception $e)
            {
                echo $e->getMessage();exit;
            }
        }
        public function modifier($idAgence, $idReservation, $nbPlaces, $nvDem = true)
        {    
            $where[] = $this->getAdapter()->quoteInto('idAgence = ?', $idAgence);
            $where[]= $this->getAdapter()->quoteInto('idReservation = ?', $idReservation);
            if($nvDem) { $places = new Zend_Db_Expr('nbPlacesReservees +'.$nbPlaces); }
            else { $places = $nbPlaces; }
            
            $mtn = DateFormat_SQL(Zend_Date::now());
            $this->update(array('nbPlacesReservees' => $places,'dateDemande'=> $mtn),$where);
            
        }
        public function getResasAgence($idAgence)
        {
            try {
            $req = $this->select()->setIntegrityCheck(false)
                        ->from(array('d'=>$this->_name), '*')
                        ->join(array('r'=>'reservation'),'r.idReservation = d.idReservation', array('idClasse', 'idVol','idTypeRepas'))
                        ->join(array('c'=>'classe'), 'r.idClasse = c.idClasse', 'nomClasse')
                        ->join(array('tp'=>'typerepas'), 'r.idTypeRepas = tp.idTypeRepas', 'nomTypeRepas')
                        ->where('idAgence = ?', $idAgence)
                        ->order(array('idVol ASC','idReservation ASC'))
                    ;
            //echo $req->assemble();exit;
            return $this->fetchAll($req)->toArray();
            }
            catch(Exception $e)
            {
                echo $e->getMessage();exit;
            }
        }     
        public function confirmer($idResa, $idAgence)
        {
            $where[] = $this->getAdapter()->quoteInto('idReservation = ?', $idResa);
            $where[] = $this->getAdapter()->quoteInto('idAgence = ?', $idAgence);
            return $this->update(array('etatDemande' => 'Validée'),$where);
        }
        public function expirer($idResa, $idAgence)
        {
            $where[] = $this->getAdapter()->quoteInto('idReservation = ?', $idResa);
            $where[] = $this->getAdapter()->quoteInto('idAgence = ?', $idAgence);
            $this->update(array('etatDemande'=>'Expirée'), $where);
        }
        public function setEnAttente($idResa, $idAgence)
        {
            $where[] = $this->getAdapter()->quoteInto('idReservation = ?', $idResa);
            $where[]= $this->getAdapter()->quoteInto('idAgence = ?', $idAgence);
            $this->update(array('etatDemande'=>'En attente'), $where);
        }
        public function supprimerDemande($idResa, $idAgence)
        {
            $where[]= $this->getAdapter()->quoteInto('idReservation = ?', $idResa);
            $where[]= $this->getAdapter()->quoteInto('idAgence = ?', $idAgence);
            $this->delete($where);
        }
    }