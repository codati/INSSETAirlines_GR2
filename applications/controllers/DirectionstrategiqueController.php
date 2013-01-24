<?php
class DirectionstrategiqueController extends Zend_Controller_Action 
{
    public function init() 
    {
        $this->headStyleScript = array('css' => 'directionstrategique', 'js' => 'directionstrategique');
    
        if(!session_encours())
        {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
    }
	
    public function indexAction() {$this->_helper->redirector('volscatalogue','directionstrategique');}
	
	public function volscatalogueAction()
	{
		$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
		
		$tableLigne = new Table_Ligne;
		$lignes = $tableLigne->getLignes();
		$this->view->lignes = $lignes;
		
		$nbVolsLigne = array();
		foreach ($lignes as $ligne) {$nbVolsLigne[$ligne['idLigne']] = $tableLigne->getNbVolsDisponibles($ligne['idLigne']);}
		$this->view->nbVolsLigne = $nbVolsLigne;
	}
	
	public function ajouterligneAction()
    {
        $this->_helper->actionStack('header','index','default',array('test'=>true, 'head' => $this->headStyleScript));  
        
        $this->view->message = $this->_helper->FlashMessenger->getMessages();
        
        $tableAero = new Table_Aeroport();
        $trigs = $tableAero->getAeroports();
        
        $aeros = array();
        foreach($trigs as $trig)
        {
            $aeros[$trig['trigrammeAeroport']] = $trig['nomAeroport'];
        }
        //Zend_Debug::dump($aeros);exit;        
        
        $tablePeriodicite = new Table_Periodicite();
        $periodicites = $tablePeriodicite->getPeriodicites();
        
        $newPeriodicites = array();
        foreach($periodicites as $periodicite)
        {
            $newPeriodicites[$periodicite['idPeriode']] = $periodicite['nomPeriode'];
        }
        
        $form = new Zend_Form;
        $form->setMethod('post');
        $form->setAction('/lignes/ajouter');
        
        $eTrigDepart = new Zend_Form_Element_Select('trigDepart');
        $eTrigDepart->setLabel('Choississez un aéroport de départ : ');             
        $eTrigDepart->addMultiOptions($aeros);
        
        $eTrigArrivee = new Zend_Form_Element_Select('trigArrivee');
        $eTrigArrivee->setLabel('Choississez un aéroport d\'arrivée : ');
        $eTrigArrivee->addMultiOptions($aeros);
        
        $ePeriod = new Zend_Form_Element_Select('periodicite');
        $ePeriod->setLabel('Periodicité :');
        $ePeriod->addMultiOptions($newPeriodicites);
        
        $eSubmit = new Zend_Form_Element_Submit('sub_ligne');
        $eSubmit->setName('Ajouter');
        $eSubmit->setAttrib('class','ajouter');
        
        $form->addElements(array(
            $eTrigDepart,
            $eTrigArrivee,
            $ePeriod,
            $eSubmit
        ));
        
        $this->view->formajoutligne = $form;
       // $form = new ajoutligne;
       // $form->setTrigrammes($trigs);
       // $form->setPeriodicite($newPeriodicites);
       // $form->init();
    }
	
	public function voirligneAction()
	{
		$idLigne = $this->_getParam('ligne', null);
		
		if($idLigne != null)
		{
			
		}
	}
	
	public function modifierligneAction()
	{
		
	}
	
	public function supprimerligneAction()
	{
		
	}
}

?>
