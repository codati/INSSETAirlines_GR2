<?php
    class Table_Avion extends Zend_Db_Table_Abstract
    {
        protected $_name = 'avion';
        protected $_primary = 'immatriculationAvion';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'ModeleAvion' => array(
                    'columns' => 'idModeleAvion',
                    'refTableClass' => 'Table_ModeleAvion'
                    )
            );
 
        public function Ajouter($p_immatriculation, $p_modele) {     
            $data = array('immatriculationAvion' => $p_immatriculation, 'idModeleAvion' => $p_modele);
            try {                
                $this->insert($data);
            }
            catch (Exception $e)
            {
                return false;
            }
            return true;
        }
        
        public function Modifier($p_immatriculation, $p_newImmatriculation, $p_modele) {
            $data = array('immatriculationAvion' => $p_newImmatriculation, 'idModeleAvion' => $p_modele);
            $where = $this->getAdapter()->quoteInto('immatriculationAvion = ?', $p_immatriculation);
            try {   
               $this->update($data, $where); 
            }
            catch (Exception $e)
            {
                return false;
            }
            return true;
        }
        
        public function Supprimer($p_immatriculation)
        {   
            $where = $this->getAdapter()->quoteInto('immatriculationAvion = ?', $p_immatriculation);
            try {   
               $this->delete($where); 
            }
            catch (Exception $e)
            {
                return false;
            }
            return true;          
        }
        
        public function Reset($p_immatriculation) {                   
            $data = array('heuresVolDerniereIntervention' => 0);
            $where = $this->getAdapter()->quoteInto('immatriculationAvion = ?', $p_immatriculation);
            $this->update($data, $where);
        }
        
        public function Up($p_immatriculation, $p_heuresVol) {
            $data = array('heuresVolDerniereIntervention' => $p_heuresVol);
            $where = $this->getAdapter()->quoteInto('immatriculationAvion = ?', $p_immatriculation);
            $this->update($data, $where);
        }
        
        /**
         * Retourne la liste des avions disponible pour un modèle et entre deux date
         * @param int $idModele : L'id du modèle d'avion voulu
         * @param string $dateDepart : La date de départ au format SQL
         * @param string $dateArrivee : La date d'arrivée au format SQL
         * @return array : La liste des avions disponible
         */
        public function get_LstAvionsDispo_PourModeleEtDate($idModele, $dateDepart, $dateArrivee)
        {
            $reqDate = $this->select()->setIntegrityCheck(false)
                    ->from(array('v' => 'vol'), 'COUNT(v.idVol)')
                    ->where('v.matriculeAvion=a.immatriculationAvion')
                    ->where('dateHeureDepartPrevueVol >= "'.$dateDepart.'"')
                    ->where('dateHeureArriveePrevueVol <= "'.$dateArrivee.'"');

            $req = $this->select()->setIntegrityCheck(false)
                    ->from(array('a' => 'avion'), 'immatriculationAvion')
                    ->where('a.idModeleAvion="'.$idModele.'"')
                    ->where('('.new Zend_Db_Expr($reqDate).') = 0');
            $res = $this->fetchAll($req);
            return $res->toArray();
        }
	
        /**
         * Récupère le nom d'un avion
         * @param int $idAvion : L'id de l'avion
         * @return string : Le nom de l'avion
         */
        public function get_immatriculation($idAvion)
        {
            $req = $this->select()->from($this->_name, 'immatriculationAvion')->where('idAvion=?', $idAvion);
            $res = $this->fetchRow($req)->toArray();
            return $res['immatriculationAvion'];
        }
        
        public function getAvions()
        {
            $req = $this->select()->setIntegrityCheck(false)
                    ->from(array('a' => $this->_name))
                    ->join(array('m' => 'modeleavion'), 'm.idModeleAvion=a.idModeleAvion', 'libelleModeleAvion');
            /*
            SELECT a.*, m.libelleModeleAvion
            FROM avion AS a
            INNER JOIN modeleavion m
                ON m.idModeleAvion=a.idModeleAvion
            */
            $res = $this->fetchAll($req)->toArray();
            return $res;
        }
        public function get_lstImmatriculations()
        {
            $reqImmat = $this->select()->setIntegrityCheck(false)
                            ->from($this->_name,'immatriculationAvion')
                            ;
            return $this->fetchAll($reqImmat)->toArray();
        }
        
        public function getModele($p_immatriculationAvion)
        {
            $req = $this->select()->setIntegrityCheck(false)
                    ->from($this->_name,'idModeleAvion')
                    ->where('immatriculationAvion = ?', $p_immatriculationAvion)
                    ;
            return $this->fetchRow($req)->toArray();
        }
    }    
