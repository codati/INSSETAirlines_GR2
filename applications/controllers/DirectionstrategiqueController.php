<?php
/**
 * Contrôleur de direction stratégique
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Kevin Verschaeve <kevin.verschaeve@live.fr>
 * @author   Maxime Vermeulen <bulton.fr@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /DirectionStrategique
 */

/**
 * Classe du contrôleur direction stratégique
 * 
 * @category INSSET
 * @package  Airline
 * @author   Kevin Verschaeve <kevin.verschaeve@live.fr>
 * @author   Maxime Vermeulen <bulton.fr@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /DirectionStrategique
 */
class DirectionstrategiqueController extends Zend_Controller_Action
{
    /**
	 * Méthode d'initialisation du contrôleur.
	 * Permet de déclarer les css & js à utiliser.
	 * 
	 * @return null
	 */
    public function init() 
    {
        $this->headStyleScript = array(
        	'css' => 'directionstrategique', 
        	'js' => 'directionstrategique'
		);
    
        if (!session_encours()) {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }        
        if (!Services_verifAcces('Direction strategique')) {
            throw new Zend_Controller_Action_Exception('', 403);
        }
    }
	
    /**
     * Action index. Renvoi automatiquement vers l'action volscatalogue
     * 
     * @return null
     */
    public function indexAction()
    {
    	$this->_helper->redirector('volscatalogue', 'directionstrategique');
    }
	
    /**
     * Liste des lignes du catalogue
     * 
     * @return null
     */
    public function volscatalogueAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tableLigne = new Table_Ligne;
        $lignes = $tableLigne->getLignes();
        $this->view->lignes = $lignes;

        $nbVolsLigne = array();
        foreach ($lignes as $ligne) {
            $nbVolsLigne[$ligne['idLigne']] = $tableLigne->getNbVolsDisponibles($ligne['idLigne']);
        }
        $this->view->nbVolsLigne = $nbVolsLigne;
    }

    /**
     * Ajoute une ligne (formulaire)
     * 
     * @return null
     */
    public function ajouterligneAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('test'=>true, 'head' => $this->headStyleScript));  
        
        $this->view->message = $this->_helper->FlashMessenger->getMessages();
        
        $tableAero = new Table_Aeroport();
        $trigs = $tableAero->getAeroports();
        
        $aeros = array();
        foreach ($trigs as $trig) {
            $aeros[$trig['trigrammeAeroport']] = $trig['nomAeroport'];
        }        
        
        $tablePeriodicite = new Table_Periodicite();
        $periodicites = $tablePeriodicite->getPeriodicites();
        
        $newPeriodicites = array();
        foreach ($periodicites as $periodicite) {
            $newPeriodicites[$periodicite['idPeriode']] = $periodicite['nomPeriode'];
        }
        
        $form = new Zend_Form;
        $form->setMethod('post');
        $form->setAction('/lignes/ajouter');
        $form->setAttrib('class', 'form');
        
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
        $eSubmit->setAttrib('class', 'ajouter');
        
        $form->addElements(
        	array(
	            $eTrigDepart,
	            $eTrigArrivee,
	            $ePeriod,
	            $eSubmit
	        )
		);
        
        $this->view->formajoutligne = $form;
    }
	
	/**
	 * Modifie une ligne (formulaire)
	 * 
	 * @return null
	 */
	public function modifierligneAction()
	{
		$idLigne = (int) $this->_getParam('ligne', null);
		if ($idLigne != null) {
			$this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
	        
	        $this->view->message = $this->_helper->FlashMessenger->getMessages();
	        
	        $tableLigne = new Table_Ligne;
	        $tablePeriodicite = new Table_Periodicite;
	        $tableAero = new Table_Aeroport();
	        
			$infosLigne = $tableLigne->getUneLigne($idLigne);
	        $trigs = $tableAero->getAeroports();
	        
	        $aeros = array();
	        foreach ($trigs as $trig) {
	        	$aeros[$trig['trigrammeAeroport']] = $trig['nomAeroport'];
			}
			
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
			foreach ($periodicites as $periodicite) {
				$newPeriodicites[$periodicite['idPeriode']] = $periodicite['nomPeriode'];
			}
			
			$form = new Zend_Form;
			$form->setMethod('post');
			$form->setAction('/lignes/modifier/idligne/'.$idLigne);
            $form->setAttrib('class', 'form');
			
			$ePeriod = new Zend_Form_Element_Select('periodicite');
			$ePeriod->setLabel('Periodicité :');
			$ePeriod->addMultiOptions($newPeriodicites);
			$ePeriod->setValue($infosLigne['idPeriodicite']);
			
			$eSubmit = new Zend_Form_Element_Submit('sub_ligne');
			$eSubmit->setName('Modifier');
			$eSubmit->setAttrib('class', 'valider');
			
			$form->addElements(
				array(
		            $eTrigDepart,
		            $eTrigArrivee,
					$ePeriod,
					$eSubmit
				)
			);
			
			$infosLigne = $tableLigne->getInfoLigne($idLigne);
			$this->view->laLigne = $infosLigne;
			$this->view->formModif = $form;
		} else {
			$this->_helper->redirector('index', 'directionstrategique');
		}
	}
	
	/**
	 * Liste les vols d'une ligne
	 * 
	 * @return null
	 */
	public function voirvolsAction()
	{
		$this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));  
        
        $idLigne = (int) $this->_getParam('ligne', null);
		
		if ($idLigne != null) {
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
			foreach ($paginator as $val) {
				$departPrevu = $departEffectif = $arrivePrevu = $arriveEffectif = '';
				
				if (!empty($val->dateHeureDepartPrevueVol)) {
					$departPrevu = DateFormat_View(new Zend_Date($val->dateHeureDepartPrevueVol), true, false);
				}
	
	            if (!empty($val->dateHeureDepartEffectiveVol)) {
	            	$departEffectif = DateFormat_View(new Zend_Date($val->dateHeureDepartEffectiveVol), true, false);
				}
	            
	            if (!empty($val->dateHeureArriveePrevueVol)) {
	            	$arrivePrevu = DateFormat_View(new Zend_Date($val->dateHeureArriveePrevueVol), true, false);
				}
	
	            if (!empty($val->dateHeureArriveeEffectiveVol)) {
	            	$arriveEffectif = DateFormat_View(new Zend_Date($val->dateHeureArriveeEffectiveVol), true, false);
				}
				
				$escales = $TableEscale->get_InfosEscales($val->idVol);
				foreach ($escales as $cle => $escale) {
					$Esc_departPrevu = $Esc_departEffectif = $Esc_arrivePrevu = $Esc_arriveEffectif = '';
					
					if (!empty($escale['datehDepartPrevueEscale'])) {
						$Esc_departPrevu = DateFormat_View(new Zend_Date($escale['datehDepartPrevueEscale']), true, false);
					}
		
		            if (!empty($escale['datehDepartEffectiveEscale'])) {
		            	$Esc_departEffectif = DateFormat_View(new Zend_Date($escale['datehDepartEffectiveEscale']), true, false);
					}
		
		            if (!empty($escale['datehArriveePrevueEscale'])) {
		            	$Esc_arrivePrevu = DateFormat_View(new Zend_Date($escale['datehArriveePrevueEscale']), true, false);
					}
		
		            if (!empty($escale['datehArriveeEffectiveEscale'])) {
		            	$Esc_arriveEffectif = DateFormat_View(new Zend_Date($escale['datehArriveeEffectiveEscale']), true, false);
					}
					
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
		
			$msg = $this->_helper->FlashMessenger->getMessages();
			if ($msg != '') {
				$this->view->message = $msg;
			}
		}
	}

	/**
	 * Ajoute un vol
	 * 
	 * @return null
	 */
	public function ajoutervolAction()
	{
		$idLigne = $this->_getParam('ligne', null);
		
		if ($idLigne != null) {
			$this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
			
			$tableLigne = new Table_Ligne;
			$tableAero = new Table_Aeroport;
			
			$trigAeroLigne = $tableLigne->getTrigAeroLigne($idLigne);
			
			/*
			echo '<pre>';print_r($_POST);echo '</pre>';
			Array
			(
			    [dateDep] => 01/29/2013 11:26
			    [dateArr] => 01/30/2013 11:26
			    [Ajouter] => Ajouter
			    [nbEscale] => 1
			    [escaleOrder] => escale_0
			    [aeroEscDep] => AKL
			    [aeroEscArr] => AKL
			    [escale_0_DepAero] => BER
			    [escale_0_DepDate] => 01/29/2013 11:26
			    [escale_0_ArrAero] => AKL
			    [escale_0_ArrDate] => 01/30/2013 00:00
			)
			*/
			
			$validForm = $this->_getParam('Ajouter', null);
			$affForm = true;
			$erreurForm = '';
			
			if ($validForm != null) {
				$dateDep = $this->_getParam('dateDep');
				$dateArr = $this->_getParam('dateArr');
				$nbEscale = $this->_getParam('nbEscale');
				$escaleOrder = $this->_getParam('escaleOrder');
				
				$erreurEscale = false;
				$escale = array();
				
				if ($nbEscale > 0) {
					$orderEx = explode('.', $escaleOrder);
					
					for ($i=0;$i<=$nbEscale;$i++) {
						if (in_array('escale_'.$i, $orderEx)) {
							$depaero = $this->_getParam('escale_'.$i.'_DepAero');
							$depdate = $this->_getParam('escale_'.$i.'_DepDate');
							$arraero = $this->_getParam('escale_'.$i.'_ArrAero');
							$arrdate = $this->_getParam('escale_'.$i.'_ArrDate');
							
							$depaerotxt = $tableAero->getNomAeroport($depaero);
							$arraerotxt = $tableAero->getNomAeroport($arraero);
							
							$escale[$i]['depaero'] = $depaero;
							$escale[$i]['depaerotxt'] = $depaerotxt['nomAeroport'];
							$escale[$i]['depdate'] = $depdate;
							$escale[$i]['arraero'] = $arraero;
							$escale[$i]['arraerotxt'] = $arraerotxt['nomAeroport'];
							$escale[$i]['arrdate'] = $arrdate;
							
							if ($depaero == null || $depdate == null || $arraero == null || $arrdate == null) {
								$erreurEscale = true;
							}
						}
					}
				}
				
				$nbEscaleReel = count($escale);
				
				if ($erreurEscale == false && $dateDep != null && $dateArr != null) {
					$affForm = false;
				} else {
					if ($erreurEscale == true) {
						$erreurForm = 'Un champ n\'a pas été rempli dans les escales.';
					} elseif ($dateDep == null) {
						$erreurForm = 'La date de départ du vol n\'est pas indiquée.';
					} elseif ($dateArr == null) {
						$erreurForm = 'La date d\'arrivée du vol n\'est pas indiquée.';
					}
				}
			} else {
				$dateDep = $dateArr = $escaleOrder = '';
				$nbEscale = 0;
				$escale = array();
			}
			
			if ($affForm == false) {
				//Création du vol en bdd.
				
				/**
				Récap des variables
					$dateDep
					$dateArr
					$nbEscale
					$nbEscaleReel
					$escaleOrder
					$escale -> array
						(
							array
							(
								depaero
								depaerotxt
								depdate
								arraero
								arraerotxt
								arrdate
							)
						)
				*/
				
				$dateDep = new Zend_Date($dateDep);
				$dateDepSql = DateFormat_SQL($dateDep);
				
				$dateArr = new Zend_Date($dateArr);
				$dateArrSql = DateFormat_SQL($dateArr);
				
				$tableVol = new Table_Vol;
				$idVol = $tableVol->ajouter($idLigne, $dateDepSql, $dateArrSql);
				
				if ($nbEscaleReel > 0) {
					$tableEscale = new Table_Escale;
					$i = 1;
					
					foreach ($escale as $val) {
						if ($trigAeroLigne['trigArrivee'] != $val['arraero']) {
							$escDep = new Zend_Date($val['depdate']);
							$escDepSql = DateFormat_SQL($escDep);
							
							$escArr = new Zend_Date($val['arrdate']);
							$escArrSql = DateFormat_SQL($escArr);
							
							$tableEscale->ajouter($idVol, $i, $escDepSql, $escArrSql, $val['arraero']);
							$i++;
						}
					}
				}
				
				$message = '<div class="reussi">Le vol a été créé.</div>';
		        $this->_helper->FlashMessenger($message);
		        $redirector = $this->_helper->getHelper('Redirector');
		        $redirector->gotoUrl($this->view->baseUrl('/directionstrategique/voirvols/ligne/'.$idLigne));
			} else {
				/*
					Création du formulaire à la main et non via Zend car 
					j'ai besoin de pouvoir placer les balises form où je le veux.
				*/
				
				//Gestion de l'erreur.
				if ($erreurForm != '') {
					$this->view->errorForm = $erreurForm;
				}
				
				$this->view->trigLigne = $trigAeroLigne;
				$this->view->lstAero = $tableAero->getAeroports();
				$this->view->infosLigne = $tableLigne->getUneLigne($idLigne);
				
				//Envoi à la vue des infos du form pour reremplir si déjà rempli
				$this->view->dateDep = $dateDep;
				$this->view->dateArr = $dateArr;
				$this->view->escaleOrder = $escaleOrder;
				$this->view->nbEscale = $nbEscale;
				$this->view->escale = $escale;
			}
		} else {
			$this->_helper->redirector('index', 'directionstrategique');
		}
	}
	
	/**
	 * Modifier un vol
	 * 
	 * @return null
	 */
	public function modifiervolAction()
	{
		
		$idVol = $this->_getParam('vol', null);
		$idLigne = $this->_getParam('ligne', null);
		
		if ($idVol != null && $idLigne != null) {
			$this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
			
			$tableVol = new Table_Vol;
			$tableEscale = new Table_Escale;
			$tableLigne = new Table_Ligne;
			$tableAero = new Table_Aeroport;
			
			$volExist = $tableVol->existeVol($idVol);
			
			if ($volExist) {
				$infosLigne = $tableLigne->getUneLigne($idLigne);
				$infosVol = $tableVol->get_InfosVol($idVol);
				
				$trigAeroLigne = $tableLigne->getTrigAeroLigne($idLigne);
				
				/*
				echo '<pre>';print_r($_POST);echo '</pre>';
				Array
				(
				    [dateDep] => 01/29/2013 11:26
				    [dateArr] => 01/30/2013 11:26
				    [Ajouter] => Ajouter
				    [nbEscale] => 1
				    [escaleOrder] => escale_0
				    [aeroEscDep] => AKL
				    [aeroEscArr] => AKL
				    [escale_0_DepAero] => BER
				    [escale_0_DepDate] => 01/29/2013 11:26
				    [escale_0_ArrAero] => AKL
				    [escale_0_ArrDate] => 01/30/2013 00:00
				)
				*/
				
				$validForm = $this->_getParam('Modifier', null);
				$affForm = true;
				$erreurForm = '';
				
				if ($validForm != null) {
					$dateDep = $this->_getParam('dateDep');
					$dateArr = $this->_getParam('dateArr');
					$nbEscale = $this->_getParam('nbEscale');
					$escaleOrder = $this->_getParam('escaleOrder');
					
					$erreurEscale = false;
					$escale = array();
					
					if ($nbEscale > 0) {
						$orderEx = explode('.', $escaleOrder);
						
						for ($i=0;$i<=$nbEscale;$i++) {
							if (in_array('escale_'.$i, $orderEx)) {
								$depaero = $this->_getParam('escale_'.$i.'_DepAero');
								$depdate = $this->_getParam('escale_'.$i.'_DepDate');
								$arraero = $this->_getParam('escale_'.$i.'_ArrAero');
								$arrdate = $this->_getParam('escale_'.$i.'_ArrDate');
								
								$depaerotxt = $tableAero->getNomAeroport($depaero);
								$arraerotxt = $tableAero->getNomAeroport($arraero);
								
								$escale[$i]['depaero'] = $depaero;
								$escale[$i]['depaerotxt'] = $depaerotxt['nomAeroport'];
								$escale[$i]['depdate'] = $depdate;
								$escale[$i]['arraero'] = $arraero;
								$escale[$i]['arraerotxt'] = $arraerotxt['nomAeroport'];
								$escale[$i]['arrdate'] = $arrdate;
								
								if ($depaero == null || $depdate == null || $arraero == null || $arrdate == null) {
									$erreurEscale = true;
								}
							}
						}
					}
					
					$nbEscaleReel = count($escale);
					
					if ($erreurEscale == false && $dateDep != null && $dateArr != null) {
						$affForm = false;
					} else {
						if ($erreurEscale == true) {
							$erreurForm = 'Un champ n\'a pas été rempli dans les escales.';
						} elseif ($dateDep == null) {
							$erreurForm = 'La date de départ du vol n\'est pas indiquée.';
						} elseif ($dateArr == null) {
							$erreurForm = 'La date d\'arrivée du vol n\'est pas indiquée.';
						}
					}
				} else {
					//$infosVol
					$dateDep = $infosVol['dateHeureDepartPrevueVol'];
					$dateArr = $infosVol['dateHeureArriveePrevueVol'];
					$nbEscale = $infosVol['nbEscale'];
					
					//get_InfosEscales
					if ($nbEscale > 0) {
						$infosEscales = $tableEscale->get_InfosEscales($idVol);
						
						$escaleDepAero = $trigAeroLigne['trigDepart'];
						$escaleDepAeroTxt = stripslashes($infosLigne['depart']);
						$escaleDepDate = $infosVol['dateHeureDepartPrevueVol'];
						
						$escaleOrder = '';
						$escale = array();
						foreach ($infosEscales as $escaleItem) {
							$num = $escaleItem['numeroEscale'];
							
							if ($escaleOrder != '') {
								$escaleOrder .= '.';
							}
							$escaleOrder .= 'escale_'.$num;
							
							$escaleArrAero = $escaleItem['trigrammeAeroport'];
							$escaleArrAeroTxt = $escaleItem['nomAeroport'];
							$escaleArrDate = $escaleItem['datehArriveePrevueEscale'];
							
							$escale[$num] = array(
								'depaero' => $escaleDepAero,
								'depaerotxt' => $escaleDepAeroTxt,
								'depdate' => $escaleDepDate,
								'arraero' => $escaleArrAero,
								'arraerotxt' => $escaleArrAeroTxt,
								'arrdate' => $escaleArrDate
							);
							
							$escaleDepAero = $escaleArrAero;
							$escaleDepAeroTxt = $escaleArrAeroTxt;
							$escaleDepDate = $escaleArrDate;
						}
						
						$num++;
						$escale[$num] = array(
							'depaero' => $escaleDepAero,
							'depaerotxt' => $escaleDepAeroTxt,
							'depdate' => $escaleDepDate,
							'arraero' => $trigAeroLigne['trigArrivee'],
							'arraerotxt' => stripslashes($infosLigne['arrivee']),
							'arrdate' => $infosVol['dateHeureArriveePrevueVol']
						);
					} else {
						$escaleOrder = '';
						$escale = array();
					}
				}
				
				if ($affForm == false) {
					//Création du vol en bdd.
					
					/**
					Récap des variables
						$dateDep
						$dateArr
						$nbEscale
						$nbEscaleReel
						$escaleOrder
						$escale -> array
							(
								array
								(
									depaero
									depaerotxt
									depdate
									arraero
									arraerotxt
									arrdate
								)
							)
					*/
					
					$dateDep = new Zend_Date($dateDep);
					$dateDepSql = DateFormat_SQL($dateDep);
					
					$dateArr = new Zend_Date($dateArr);
					$dateArrSql = DateFormat_SQL($dateArr);
					
					$tableVol = new Table_Vol;
					$idVol = $tableVol->modifier($idVol, $dateDepSql, $dateArrSql);
					$tableEscale->supprAllEscale($idVol);
					
					if ($nbEscaleReel > 0) {
						$i = 1;
						foreach ($escale as $val) {
							if ($trigAeroLigne['trigArrivee'] != $val['arraero']) {
								$escDep = new Zend_Date($val['depdate']);
								$escDepSql = DateFormat_SQL($escDep);
								
								$escArr = new Zend_Date($val['arrdate']);
								$escArrSql = DateFormat_SQL($escArr);
								
								$tableEscale->ajouter($idVol, $i, $escDepSql, $escArrSql, $val['arraero']);
								$i++;
							}
						}
					}
					
					$message = '<div class="reussi">Le vol a été modifié.</div>';
			        $this->_helper->FlashMessenger($message);
			        $redirector = $this->_helper->getHelper('Redirector');
			        $redirector->gotoUrl($this->view->baseUrl('/directionstrategique/voirvols/ligne/'.$idLigne));
				} else {
					/*
						Création du formulaire à la main et non via Zend car 
						j'ai besoin de pouvoir placer les balises form où je le veux.
					*/
					
					//Gestion de l'erreur.
					if ($erreurForm != '') {
						$this->view->errorForm = $erreurForm;
					}
					
					$this->view->trigLigne = $trigAeroLigne;
					$this->view->lstAero = $tableAero->getAeroports();
					$this->view->infosLigne = $infosLigne;
					
					//Envoi à la vue des infos du form pour reremplir si déjà rempli
					$this->view->dateDep = $dateDep;
					$this->view->dateArr = $dateArr;
					$this->view->escaleOrder = $escaleOrder;
					$this->view->nbEscale = $nbEscale;
					$this->view->escale = $escale;
				}
			} else {
				$this->_helper->redirector('index', 'directionstrategique');
			}
		} else {
			$this->_helper->redirector('index', 'directionstrategique');
		}
	}
	
	/**
	 * Duplique un vol
	 * 
	 * @return null
	 */
	public function copyvolAction()
	{
		$idRefVol = $this->_getParam('vol', null);
		$idLigne = $this->_getParam('ligne', null);
		
		if ($idRefVol != null && $idLigne != null) {
			$tableVol = new Table_Vol;
			$tableVol->copy($idRefVol);
			$this->_helper->FlashMessenger('<div class="reussi">Copie du vol réussie.</div>');
			$this->_helper->redirector('voirvols', 'directionstrategique', null, array('copy' => true, 'ligne' => $idLigne));
		} else {
			$this->_helper->redirector('index', 'directionstrategique');
		}
	}
}

?>
