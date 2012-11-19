<?php
    class Table_Avion extends Zend_Db_Table_Abstract
    {
        protected $_name = 'avion';
        protected $_primary = 'immatriculationAvion';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'ModeleAvion' => array(
                    'columns' => 'idModeleAvion',
                    'refTableClass' => 'Table_ModeleAvion'
                    )
            );

    }
