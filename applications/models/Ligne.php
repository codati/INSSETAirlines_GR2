<?php
    class Table_Ligne extends Zend_Db_Table_Abstract
    {
        protected $_name = 'ligne';
        protected $_primary = 'idLigne';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'AeroportDepart' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'TAeroport',
                     ),
                'AeroportArrivee' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'TAeroport',
                    ),
                'Periodicite' => array(
                    'columns' => 'idPeriodicite',
                    'refTableClass' => 'TPeriodicite',
                    ),
            );

    }
?>
