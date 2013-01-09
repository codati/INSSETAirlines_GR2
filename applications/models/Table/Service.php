<?php
class Table_Service extends Zend_Db_Table_Abstract
{
    protected $_name = 'service';
    protected $_primary = 'idService';
    protected $_dependantTables = array('travailler');
    
 /*   protected $_referenceMap = array(
        'service' => array (
            'columns' => 'idService',
            'refTableClass' => 'travailler',
            'refColumns' => 'idService'
        )
    );*/
    
    //retourne les service d'un utilisateur
    public function getLesServices($idUtilisateur)
    {
        
        $reqService = $this->select()
                     ->setIntegrityCheck(false)
                     ->from(array('s' => 'service'), array('*'))
                     ->join(array('t' => 'travailler'),'t.idService = s.idService', array('*'))
                     ->where('t.idUtilisateur = ?', $idUtilisateur)
                     ;

        $lesServices = $this->fetchAll($reqService);
       
       
       $tabServices = array();
       foreach($lesServices as $unService)
       {
           $tabServices[] = $unService->toArray();
       }
       
       //Zend_Debug::dump($tabServices);
       
       return $tabServices;
    }
   
}
?>
