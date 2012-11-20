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
            $this->insert($data);
        }
        
        public function Modifier($p_immatriculation, $p_newImmatriculation, $p_modele) {
            $data = array('immatriculationAvion' => $p_newImmatriculation, 'idModeleAvion' => $p_modele);
            $where = $this->getAdapter()->quoteInto('immatriculationAvion = ?', $p_immatriculation);
            $this->update($data, $where);
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
    }    
