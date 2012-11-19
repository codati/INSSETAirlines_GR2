<?php
class TTravailler extends Zend_Db_Table_Abstract
{
    protected $_name = 'travailler';
    protected $_primary = array('idService','idUtilisateur');
    
}
?>
