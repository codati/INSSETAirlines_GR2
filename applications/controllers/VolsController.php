<?php

class VolsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->actionStack('header','index','default',array());
    }

    public function consulterAction()
    {
        $this->_helper->actionStack('header','index','default',array());
        
        //Recupération de la base de données
        $db = Zend_Registry::get('db');
        
        $lignesReq = $db->select()
                        ->from(array('l' => 'ligne'), array('*'))                         
                        ->join(array('p'=>'periodicite'),'l.idPeriodicite = p.idPeriode', 'nomPeriode')
                        ->join(array('a'=>'aeroport'), 'l.trigrammeAeroportDepart = a.trigrammeAeroport', 'nomAeroport as aeroportDepart')
                        ->join(array('ae'=>'aeroport'), 'l.trigrammeAeroportArrivee = ae.trigrammeAeroport', 'nomAeroport as aeroportArrivee')
                         ;
        $lignes = $db->fetchAll($lignesReq);

        $this->view->lignes = $lignes;
        
    }
}
?>
