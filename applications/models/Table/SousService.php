<?php
    
class Table_SousService extends Zend_Db_Table_Abstract
{
    protected $_name = 'sousservice';
    protected $_primary = 'idSousService';
    
    //Clés étrangères
    protected $_referenceMap = array(
        'Service' => array(
            'columns' => 'idService',
            'refTableClass' => 'Table_Service'
         ));
    
    public function getLesSousServices($idService)
    {
         $reqSousServices = $this->select()
                            ->from(array('ss' => 'sousservice'),array('*'))
                            ->where('ss.idService = ?', $idService)
                            ;
         
        $lesSousServices = $this->fetchAll($reqSousServices);
         
        $tabSousServices = array();
       foreach($lesSousServices as $unSousService)
       {
           $tabSousServices[] = $unSousService->toArray();
       }
        // Zend_Debug::dump($tabSousServices);exit;
        return $tabSousServices;
    }
    
}

?>
