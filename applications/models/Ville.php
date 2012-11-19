<?php
    class Table_Ville extends Zend_Db_Table_Abstract
    {
        protected $_name = 'ville';
        protected $_primary = 'idVille';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Pays' => array(
                    'columns' => 'idPays',
                    'refTableClass' => 'Table_Pays'
                     )
            );

    }