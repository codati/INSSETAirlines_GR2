<?php
    class Table_Breveter extends Zend_Db_Table_Abstract
    {
        protected $_name = 'breveter';
        protected $_primary = array('idModeleAvion', 'idPilote');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'ModeleAvion' => array(
                    'columns' => 'idModeleAvion',
                    'refTableClass' => 'Table_ModeleAvion'
                     ),
                'Pilote' => array(
                    'columns' => 'idPilote',
                    'refTableClass' => 'Table_Pilote'
                    )
            );

    }