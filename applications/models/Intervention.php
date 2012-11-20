<?php
    class Table_Intervention extends Zend_Db_Table_Abstract
    {
        protected $_name = 'intervention';
        
        protected $_primary = 'numeroIntervention';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Avion' => array(
                    'columns' => 'immatriculationAvion',
                    'refTableClass' => 'Table_Avion'
                     )
            );
        
        public function Ajouter($p_numeroIntervention, $p_immatriculationAvion, $p_datePrevue, $p_dateEffective, $p_typeIntervention) {
            $db = Zend_Registry::get('db');
            
            $data = array('numeroIntervention' => $p_numeroIntervention,
                'immatriculationAvion' => $p_immatriculationAvion, 
                'datePrevueIntervention' => $p_datePrevue, 
                'dateEffectiveIntervention' => $p_dateEffective, 
                'typeIntervention' => $p_typeIntervention);
            $db->insert('intervention', $data);
        }
    }