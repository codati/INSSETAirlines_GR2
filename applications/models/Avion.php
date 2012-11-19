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
            $db = Zend_Registry::get('db');
            
            $data = array('immatriculationAvion' => $p_immatriculation, 'idModeleAvion' => $p_modele);
            $db->insert('avion', $data);
        }
    }
