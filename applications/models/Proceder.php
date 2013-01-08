<?php
    class Table_Proceder extends Zend_Db_Table_Abstract
    {
        protected $_name = 'proceder';
        
        protected $_primary = array('numeroIntervention', 'matriculeTechnicien');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Intervention' => array(
                    'columns' => 'numeroIntervention',
                    'refTableClass' => 'Table_Intervention'
                     ),
                'Technicien' => array(
                    'columns' => 'matriculeTechnicien',
                    'refTableClass' => 'Table_Technicien'
                    )
            );
        
        public function creer($matriculeTechnicien, $numIntervention)
        {
            $this->insert(array('numeroIntervention' => $numIntervention, 'matriculeTechnicien' => $matriculeTechnicien));
        }

    }