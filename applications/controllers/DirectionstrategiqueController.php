<?php
class DirectionstrategiqueController extends Zend_Controller_Action 
{
    public function init() {$this->headStyleScript = array();}
	
    public function indexAction()
    {
    	$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }	
    public function ajouterligneAction()
    {
        $this->_helper->actionStack('header','index','default',array('test'=>true, 'head' => $this->headStyleScript));  
        
        $tableAero = new Table_Aeroport();
        $trigs = $tableAero->getTrigrammes();
        $trigs = array_combine($trigs, $trigs);
        //Zend_Debug::dump($trigs);exit;
        
        $tablePeriodicite = new Table_Periodicite();
        $periodicites = $tablePeriodicite->getPeriodicites();
        
        $newPeriodicites = array();
        foreach($periodicites as $periodicite)
        {
            $newPeriodicites[$periodicite['idPeriode']] = $periodicite['nomPeriode'];
        }
        
        $form = new ajoutligne();
        $form->setTrigrammes($trigs);
        $form->setPeriodicite($newPeriodicites);
        $form->init();
        
        $this->view->formajoutligne = $form;
        
    }
}

?>
