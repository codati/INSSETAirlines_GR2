<?php
    class Table_Valoir extends Zend_Db_Table_Abstract
    {
        protected $_name = 'valoir';
        protected $_primary = array('idVol', 'idClasse');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Classe' => array(
                    'columns' => 'idClasse',
                    'refTableClass' => 'Table_Classe'
                    )
            );

    }