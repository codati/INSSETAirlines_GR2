<?php
    class Table_Escale extends Zend_Db_Table_Abstract
    {
        protected $_name = 'escale';
        
        protected $_primary = 'numeroEscale';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Aeroport' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'Table_Aeroport'
                    )
            );

    }