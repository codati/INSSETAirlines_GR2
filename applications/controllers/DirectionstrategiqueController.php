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
	
	public function modifierligneAction()
	{
		$idLigne = (int) $this->_getParam('ligne', null);
		if($idLigne != null)
		{
			$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
	        
	        $this->view->message = $this->_helper->FlashMessenger->getMessages();
	        
	        $tableLigne = new Table_Ligne;
	        $tablePeriodicite = new Table_Periodicite;
	        $tableAero = new Table_Aeroport();
	        
			$infosLigne = $tableLigne->getUneLigne($idLigne);
	        $trigs = $tableAero->getAeroports();
	        
	        $aeros = array();
	        foreach($trigs as $trig) {$aeros[$trig['trigrammeAeroport']] = $trig['nomAeroport'];}
			
	        $eTrigDepart = new Zend_Form_Element_Select('trigDepart');
		    $eTrigDepart->setLabel('Choississez un aéroport de départ : ');             
	        $eTrigDepart->addMultiOptions($aeros);
			$eTrigDepart->setValue($infosLigne['trigrammeAeroportDepart']);
	        
	        $eTrigArrivee = new Zend_Form_Element_Select('trigArrivee');
	        $eTrigArrivee->setLabel('Choississez un aéroport d\'arrivée : ');
	        $eTrigArrivee->addMultiOptions($aeros);
			$eTrigArrivee->setValue($infosLigne['trigrammeAeroportArrivee']);
	        
	        $periodLigne = $tableLigne->getPeriodiciteLigne($idLigne);
	        $periodicites = $tablePeriodicite->getPeriodicites();        
	        $newPeriodicites = array();
			
	        foreach($periodicites as $periodicite) {$newPeriodicites[$periodicite['idPeriode']] = $periodicite['nomPeriode'];}
	        $form = new Zend_Form;
	        $form->setMethod('post');
	        $form->setAction('/lignes/modifier/idligne/'.$idLigne);
	        
	        $ePeriode = new Zend_Form_Element_Select('sel_periode');
	        $ePeriode->setLabel('Changer la periodicité :');
	        $ePeriode->addMultiOptions($newPeriodicites);
	        $ePeriode->setValue($periodLigne);
	        
	        $eSubmit = new Zend_Form_Element_Submit('sub_modifLigne');
	        $eSubmit->setName('Modifier');
	        $eSubmit->setAttrib('class', 'valider');
	        
	        $form->addElements(array(
	            $eTrigDepart,
	            $eTrigArrivee,
	            $ePeriode,
	            $eSubmit
	        ));
	        $this->view->laLigne = $infosLigne;
	        $this->view->formModif = $form;
		}
		else {$this->_helper->redirector('index', 'directionstrategique');}
	}
	
	public function voirvolsAction()
	{
		$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));  
        
        $idLigne = (int) $this->_getParam('ligne', null);
		
		if($idLigne != null)
		{
			$TableLigne = new Table_Ligne;
			$TableVol = new Table_Vol;
			$TableEscale = new Table_Escale;
			
			$infosLigne = $TableLigne->getInfoLigne($idLigne);
			$this->view->infosLigne = $infosLigne;
			
			$reqLstVol = $TableVol->get_AllVol_PourLigne($idLigne, array(), true);
			
			//On créer la pagination
			$paginator = Zend_Paginator::factory($reqLstVol);
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page', 1));
			Zend_Paginator::setDefaultScrollingStyle('Elastic');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
			
			$lstVol = array();
			$key = 0;
			foreach($paginator as $val)
			{
				if(!empty($val->dateHeureDepartPrevueVol)) {$departPrevu = DateFormat_View(new Zend_Date($val->dateHeureDepartPrevueVol), true, false);}
	            else {$departPrevu = '';}
	
	            if(!empty($val->dateHeureDepartEffectiveVol)) {$departEffectif = DateFormat_View(new Zend_Date($val->dateHeureDepartEffectiveVol), true, false);}
	            else {$departEffectif = '';}
	
	            if(!empty($val->dateHeureArriveePrevueVol)) {$arrivePrevu = DateFormat_View(new Zend_Date($val->dateHeureArriveePrevueVol), true, false);}
	            else {$arrivePrevu = '';}
	
	            if(!empty($val->dateHeureArriveeEffectiveVol)) {$arriveEffectif = DateFormat_View(new Zend_Date($val->dateHeureArriveeEffectiveVol), true, false);}
	            else {$arriveEffectif = '';}
				
				$escales = $TableEscale->get_InfosEscales($val->idVol);
				foreach($escales as $cle => $escale)
				{
					if(!empty($escale['datehDepartPrevueEscale'])) {$Esc_departPrevu = DateFormat_View(new Zend_Date($escale['datehDepartPrevueEscale']), true, false);}
		            else {$Esc_departPrevu = '';}
		
		            if(!empty($escale['datehDepartEffectiveEscale'])) {$Esc_departEffectif = DateFormat_View(new Zend_Date($escale['datehDepartEffectiveEscale']), true, false);}
		            else {$Esc_departEffectif = '';}
		
		            if(!empty($escale['datehArriveePrevueEscale'])) {$Esc_arrivePrevu = DateFormat_View(new Zend_Date($escale['datehArriveePrevueEscale']), true, false);}
		            else {$Esc_arrivePrevu = '';}
		
		            if(!empty($escale['datehArriveeEffectiveEscale'])) {$Esc_arriveEffectif = DateFormat_View(new Zend_Date($escale['datehArriveeEffectiveEscale']), true, false);}
		            else {$Esc_arriveEffectif = '';}
					
            		$escales[$cle]['datehDepartPrevueEscale'] = $Esc_departPrevu;
					$escales[$cle]['datehDepartEffectiveEscale'] = $Esc_departEffectif;
					$escales[$cle]['datehArriveePrevueEscale'] = $Esc_arrivePrevu;
					$escales[$cle]['datehArriveeEffectiveEscale'] = $Esc_arriveEffectif;
				}
				
				$lstVol[$key]['idVol'] = $val->idVol;
				$lstVol[$key]['nbEscale'] = $val->nbEscale;
	            $lstVol[$key]['DepartPrevu'] = $departPrevu;
	            $lstVol[$key]['DepartEffectif'] = $departEffectif;
	            $lstVol[$key]['ArriveePrevu'] = $arrivePrevu;
	            $lstVol[$key]['ArriveeEffectif'] = $arriveEffectif;
				$lstVol[$key]['infosEscale'] = $escales;
				$key++;
			}
			
			$this->view->lstVol = $lstVol;
			$this->view->paginator = $paginator;
		}
	}
	
	public function ajoutervolAction()
	{
		
	}
	
	public function modifiervolAction()
	{
		
	}
	
	public function copyvolAction()
	{
		
	}
}

?>
