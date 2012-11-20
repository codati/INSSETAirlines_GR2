<?php
    class Table_Deservir extends Zend_Db_Table_Abstract
    {
        protected $_name = 'deservir';
        protected $_primary = array('trigrammeAeroport', 'idVille');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Aeroport' => array(
                    'columns' => 'trigrammeAeroport',
                    'refTableClass' => 'Table_Aeroport'
                     ),
                'Ville' => array(
                    'columns' => 'idVille',
                    'refTableClass' => 'Table_Ville'
                    )
            );

    }