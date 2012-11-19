<?php
    class Table_Travailler extends Zend_Db_Table_Abstract
    {
        protected $_name = 'travailler';
        protected $_primary = array('idService', 'idUtilisateur');
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Service' => array(
                    'columns' => 'idService',
                    'refTableClass' => 'Table_Service'
                     ),
                'Utilisateur' => array(
                    'columns' => 'Utilisateur',
                    'refTableClass' => 'Table_Utilisateur'
                    )
            );

    }