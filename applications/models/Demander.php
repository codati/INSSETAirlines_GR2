<?php
    class Table_Demander extends Zend_Db_Table_Abstract
    {
        protected $_name = 'demander';
        protected $_primary = array('idAgence', 'idReservation');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Agence' => array(
                    'columns' => 'idAgence',
                    'refTableClass' => 'Table_Agence'
                     ),
                'Reservation' => array(
                    'columns' => 'idReservation',
                    'refTableClass' => 'Table_Reservation'
                    )
            );

    }