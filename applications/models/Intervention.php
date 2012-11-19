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

    }