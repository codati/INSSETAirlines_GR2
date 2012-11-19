<?php
    class Table_Assurer extends Zend_Db_Table_Abstract
    {
        protected $_name = 'assurer';
        protected $_primary = array('idVol', 'idPilote');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Pilote' => array(
                    'columns' => 'idPilote',
                    'refTableClass' => 'Table_Pilote'
                    )
            );

    }