<?php
class TService extends Zend_Db_Table_Abstract
{
    protected $_name = 'service';
    protected $_primary = 'idService';
    
    protected $_referenceMap = array(
        
        
    );
    public function getLesServices($idUtilisateur)
    {
        $trav = new TTravailler;
        $trav2 = $trav->fetchAll();
        //Zend_Debug::dump($trav2);
        exit;
        $reqService = $this->select()
                     ->from(array('s' => 'service'), array('*'))
                     ->join(array('t' => $trav2),'t.idService = s.idService', array('*'))
                     ->where('t.idUtilisateur = ?', $idUtilisateur)
                     ;
        
        $lesServices = $this->fetchAll($reqService); 
        return $lesServices;
        Zend_Debug::dump($lesServices);
        exit;
    }
   
}
?>
