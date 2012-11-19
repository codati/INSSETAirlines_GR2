<?php
    class Table_Vol extends Zend_Db_Table_Abstract
    {
        protected $_name = 'vol';
        protected $_primary = 'idVol';

        //Clés étrangères
        protected $_referenceMap = array(
                'Ligne' => array(
                    'columns' => 'idLigne',
                    'refTableClass' => 'Table_Ligne'
                     ),
                'Avion' => array(
                    'columns' => 'immatriculationAvion',
                    'refTableClass' => 'Table_Avion'
                    ),
            );

    }
?>
