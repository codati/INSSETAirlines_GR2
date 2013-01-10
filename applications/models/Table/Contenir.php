<?php
    class Table_Contenir extends Zend_Db_Table_Abstract
    {
        protected $_name = 'contenir';
        protected $_primary = array('idModeleAvion', 'idClasse');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'ModeleAvion' => array(
                    'columns' => 'idModeleAvion',
                    'refTableClass' => 'Table_ModeleAvion'
                     ),
                'Classe' => array(
                    'columns' => 'idClasse',
                    'refTableClass' => 'Table_Classe'
                    )
            );

    }