<?php
    class Table_Aeroport extends Zend_Db_Table_Abstract
    {
        protected $_name = 'aeroport';
        protected $_primary = 'trigrammeAeroport';
    }