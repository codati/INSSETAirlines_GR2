<?php
    class Table_Reservation extends Zend_Db_Table_Abstract
    {
        protected $_name = 'reservation';
        
        protected $_primary = 'idReservation';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Vol' => array(
                    'columns' => 'idVol',
                    'refTableClass' => 'Table_Vol'
                     ),
                'Classe' => array(
                    'columns' => 'idClasse',
                    'refTableClass' => 'Table_Classe'
                     ),
                'TypeRepas' => array(
                    'columns' => 'idTypeRepas',
                    'refTableClass' => 'Table_TypeRepas'
                    )
            );

    }