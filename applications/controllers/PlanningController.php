<?php
class PlanningController extends Zend_Controller_Action
{
	public function init()
	{
		$this->headStyleScript = array(
			'css' => 'planning',
			'js' => 'planning'
		);
                
                if(!session_encours())
                {
                    $redirector = $this->_helper->getHelper('Redirector');
                    $redirector->gotoUrl($this->view->baseUrl());  
                }
	}
	
    public function indexAction() {header('Location: /planning/planifier');}

	public function planifierAction()
	{
		if(Services_verifAcces('Planning'))
		{
			$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
			
			$tableVol = new Table_Vol();
			$ListeVol = $tableVol->get_LstVolNonPlanifier(4); //Les vols non planifiés des 4 dernières semaines
			
			if(count($ListeVol) > 0)
			{
				foreach($ListeVol as $key => $val)
				{
					$dateDepart = new Zend_Date($val['dateHeureDepartPrevueVol']);
					$ListeVol[$key]['dateHeureDepartPrevueVol'] = DateFormat_View($dateDepart);
					
					$dateArriver = new Zend_Date($val['dateHeureArriveePrevueVol']);
					$ListeVol[$key]['dateHeureArriveePrevueVol'] = DateFormat_View($dateArriver);
				}
			}
			
			//echo '<pre>';print_r($ListeVol);echo '</pre>';exit;
			$this->view->ListeVol = $ListeVol;
		}
		else {header('Location: /');}
	}
	
	public function planifiervolAction()
	{
		if(Services_verifAcces('Planning'))
		{
			$idVol = $this->_getParam('idVol', null);
			
			if($idVol != null)
			{
				$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
				$this->view->idVol = $idVol;
				
				$tableVol = new Table_Vol();
				$InfosVol = $tableVol->get_InfosVol($idVol);
				//echo '<pre>';print_r($InfosVol);echo '</pre>';exit;
				
				$dateDepartSQL = $InfosVol['dateHeureDepartPrevueVol'];
				$dateArriveeSQL = $InfosVol['dateHeureArriveePrevueVol'];
				$this->view->dateDepart = $dateDepartSQL;
				$this->view->dateArrivee = $dateArriveeSQL;
				
				$InfosVol['dateHeureDepartPrevueVol'] = new Zend_Date($InfosVol['dateHeureDepartPrevueVol']);
				$InfosVol['dateHeureArriveePrevueVol'] = new Zend_Date($InfosVol['dateHeureArriveePrevueVol']);
				
				$this->view->InfosVol = $InfosVol;
				
				$tableModeleAvion = new Table_ModeleAvion();
				$LstModeleDepartSQL = $tableModeleAvion->get_NomModeles_PourAeroport($InfosVol['trigrammeAeDepart']);
				$LstModeleArriveeSQL = $tableModeleAvion->get_NomModeles_PourAeroport($InfosVol['trigrammeAeArrivee']);
				
				foreach($LstModeleDepartSQL as $val) {$LstModeleDepart[$val['idModeleAvion']] = $val['libelleModeleAvion'];}
				foreach($LstModeleArriveeSQL as $val) {$LstModeleArrivee[$val['idModeleAvion']] = $val['libelleModeleAvion'];}
				
				$LstModele = array_uintersect($LstModeleDepart, $LstModeleArrivee, 'strcasecmp');
				$this->view->ListeModele = $LstModele;
				
				reset($LstModele);
				$idModele = key($LstModele);
				
				$LstAvionDispo = $this->lstavionAction($idModele, $dateDepartSQL, $dateArriveeSQL);
				$this->view->ListeAvion = $LstAvionDispo;
				
				$LstPiloteDispo = $this->lstpiloteAction($idModele, $dateDepartSQL, $dateArriveeSQL);
				$this->view->ListePilote = $LstPiloteDispo;
				
				reset($LstPiloteDispo);
				$idPilote = key($LstPiloteDispo);
				//$LstCoPiloteDispo = $this->lstpiloteAction($idModele, $dateDepartSQL, $dateArriveeSQL, $idPilote);
				$LstCoPiloteDispo = $LstPiloteDispo;
				unset($LstCoPiloteDispo[$idPilote]);
				$this->view->ListeCoPilote = $LstCoPiloteDispo;
			}
			else {header('Location: '.$this->view->baseUrl().'/planning/planifier');}
		}
		else {header('Location: /');}
	}
	
	public function recapvolAction()
	{
		if(Services_verifAcces('Planning'))
		{
			$this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
			$idVol = $this->_getParam('idVol', null);
		
			if($idVol != null)
			{
				$tableVol = new Table_Vol();
				$infosVol = $tableVol->fetchRow('idVol="'.$idVol.'"')->toArray();
				
				if($infosVol['matriculeAvion'] == null)
				{
					$this->view->dejaPlanifier = false;
					
					$modeleAvion = $this->_getParam('modele_avion', null);
					$avion = $this->_getParam('avion', null);
					$pilote = $this->_getParam('pilote', null);
					$copilote = $this->_getParam('copilote', null);
					
					if($modeleAvion == null || $avion == null || $pilote == null || $copilote == null) {exit;}
					
					$InfosVol = $tableVol->get_InfosVol($idVol);
					
					$dateDepartSQL = $InfosVol['dateHeureDepartPrevueVol'];
					$dateArriveeSQL = $InfosVol['dateHeureArriveePrevueVol'];
					$InfosVol['dateHeureDepartPrevueVol'] = new Zend_Date($InfosVol['dateHeureDepartPrevueVol']);
					$InfosVol['dateHeureArriveePrevueVol'] = new Zend_Date($InfosVol['dateHeureArriveePrevueVol']);
					
					$tableModele = new Table_ModeleAvion();
					$tablePilote = new Table_Pilote();
					$tableClasse = new Table_Classe();
					
					//Vérifications
					$LstModeleDepartSQL = $tableModele->get_NomModeles_PourAeroport($InfosVol['trigrammeAeDepart']);
					$LstModeleArriveeSQL = $tableModele->get_NomModeles_PourAeroport($InfosVol['trigrammeAeArrivee']);
					
					foreach($LstModeleDepartSQL as $val) {$LstModeleDepart[$val['idModeleAvion']] = $val['libelleModeleAvion'];}
					foreach($LstModeleArriveeSQL as $val) {$LstModeleArrivee[$val['idModeleAvion']] = $val['libelleModeleAvion'];}
					
					$LstModele = array_uintersect($LstModeleDepart, $LstModeleArrivee, 'strcasecmp');
					$LstAvionDispo = $this->lstavionAction($modeleAvion, $dateDepartSQL, $dateArriveeSQL);
					$LstPiloteDispo = $this->lstpiloteAction($modeleAvion, $dateDepartSQL, $dateArriveeSQL, -1);
					$LstClasses = $tableClasse->get_LstClasses_PourModele($modeleAvion);
					
					$classesOk = true;
					foreach($LstClasses as $key => $val)
					{
						$LstClasses[$key]['value'] = $value = $this->_getParam('class_'.$val['idClasse'], null);
						if($value == null) {$classesOk = false;}
					}
					
					/*
					echo '<pre>';
						var_dump($modeleAvion);
						var_dump($LstModele);
						var_dump($avion);
						var_dump($LstAvionDispo);
						var_dump($pilote);
						var_dump($copilote);
						var_dump($LstPiloteDispo);
						
						var_dump(array_key_exists($modeleAvion, $LstModele));
						var_dump(array_key_exists($avion, $LstAvionDispo));
						var_dump(array_key_exists($pilote, $LstPiloteDispo));
						var_dump(array_key_exists($copilote, $LstPiloteDispo));
						var_dump($pilote != $copilote);
						var_dump($classesOk != false);
					echo '</pre>';
					exit;
					*/
					
					if(
						in_array($modeleAvion, $LstModele) AND
						in_array($avion, $LstAvionDispo) AND
						in_array($pilote, $LstPiloteDispo) AND
						in_array($copilote, $LstPiloteDispo) AND
						($pilote != $copilote) AND
						($classesOk != false)
					)
					{header('Location: /planning/planifiervol/idVol/'.$idVol);}
					
					//Récapitulatif
					$infosModele = $tableModele->get_libelle($modeleAvion);
					$infosPilote = $tablePilote->get_NomPrenom($pilote);
					$infosCoPilote = $tablePilote->get_NomPrenom($copilote);
					
					$this->view->InfosVol = $InfosVol;
					
					$this->view->modeleAvion = $infosModele;
					$this->view->avion = $avion;
					$this->view->pilote = $infosPilote['nomPilote'].' '.$infosPilote['prenomPilote'];
					$this->view->copilote = $infosCoPilote['nomPilote'].' '.$infosCoPilote['prenomPilote'];
					
					$this->view->idModele = $modeleAvion;
					$this->view->idPilote = $pilote;
					$this->view->idCoPilote = $copilote;
					$this->view->lstClasses = $LstClasses;
				}
				else {$this->view->dejaPlanifier = true;}
			}
			else {header('Location: '.$this->view->baseUrl().'/planning/planifier');}
		}
		else {header('Location: /');}
	}
	
	public function validrecapAction()
	{
		if(Services_verifAcces('Planning'))
		{
			$idVol = $this->_getParam('idVol', null);
		
			if($idVol != null)
			{
				$tableVol = new Table_Vol();
				$infosVol = $tableVol->fetchRow('idVol="'.$idVol.'"')->toArray();
				
				if($infosVol['matriculeAvion'] == null)
				{
					$modeleAvion = $this->_getParam('idModeleAvion', null);
					$avion = $this->_getParam('immaAvion', null);
					$pilote = $this->_getParam('idPilote', null);
					$copilote = $this->_getParam('idCoPilote', null);
					
					if($modeleAvion == null || $avion == null || $pilote == null || $copilote == null) {exit;}
					
					$tableAssurer = new Table_Assurer();
					$tableValoir = new Table_Valoir();
					$tableClasse = new Table_Classe();
					
					$tableAssurer->insertPilote($idVol, $pilote);
					$tableAssurer->insertCoPilote($idVol, $copilote);
					$tableVol->changeImmatriculation($idVol, $avion);
					
					$LstClasses = $tableClasse->get_LstClasses_PourModele($modeleAvion);
					foreach($LstClasses as $val)
					{
						$prix = $this->_getParam('class_'.$val['idClasse'], null);
						if($prix != null) {$tableValoir->insertPrixVol($idVol, $val['idClasse'], $prix);}
					}
					
					echo '1';
				}
			}
		}
		
		exit;
	}
	
	public function lstavionAction($idModele=0, $dateDepart=0, $dateArrivee=0)
	{
		if(Services_verifAcces('Planning'))
		{
			$get = $this->_getParam('get', null); //Permet de savoir si on vient en get sur l'action, et pour la fin de savoir si on renvoi en json ou non.
			
			if($idModele == 0) {$idModele = $this->_getParam('idModele', null);}
			if($dateDepart == 0) {$dateDepart = $this->_getParam('dateDepart', null);}
			if($dateArrivee == 0) {$dateArrivee = $this->_getParam('dateArrivee', null);}
			
			if($idModele == null || $dateDepart == null || $dateArrivee == null) {exit;} //On vérifie qu'on a bien tout, sinon fin.
			
			$tableAvion = new Table_Avion();
			$lstAvionSQL = $tableAvion->get_LstAvionsDispo_PourModeleEtDate($idModele, $dateDepart, $dateArrivee);
			//echo '<pre>';print_r($lstAvionSQL);echo '</pre>';
			
			$lstAvion = array();
			foreach($lstAvionSQL as $val) {$lstAvion[$val['immatriculationAvion']] = $val['immatriculationAvion'];}
			//echo '<pre>';print_r($lstAvion);echo '</pre>';
			
			if($get == null) {return $lstAvion;}
			else
			{
				$json = Zend_Json::encode($lstAvion);
				echo $json;exit;
			}
		}
	}
	
	public function lstpiloteAction($idModele=0, $dateDepart=0, $dateArrivee=0, $pilote=null)
	{
		if(Services_verifAcces('Planning'))
		{
			$get = $this->_getParam('get', null); //Permet de savoir si on vient en get sur l'action, et pour la fin de savoir si on renvoi en json ou non.
			
			if($idModele == 0) {$idModele = $this->_getParam('idModele', null);}
			if($dateDepart == 0) {$dateDepart = $this->_getParam('dateDepart', null);}
			if($dateArrivee == 0) {$dateArrivee = $this->_getParam('dateArrivee', null);}
			if($pilote == null && $pilote != -1) {$pilote = $this->_getParam('pilote', null);}
			
			if($idModele == null || $dateDepart == null || $dateArrivee == null) {exit;} //On vérifie qu'on a bien tout, sinon fin.
			
			$tablePilote = new Table_Pilote();
			$lstPiloteSQL = $tablePilote->get_LstPiloteDispo_PourModele($idModele, $dateDepart, $dateArrivee);
			
			//array('idPilote', 'nomPilote', 'prenomPilote')
			$lstPilote = array();
			foreach($lstPiloteSQL as $val)
			{
				//echo $val['idPilote'].'<br/>';
				if($pilote == null || ($pilote != null && $pilote != $val['idPilote']))
				{
					$lstPilote[$val['idPilote']] = $val['nomPilote'].' '.$val['prenomPilote'];
				}
			}
			//var_dump($lstPilote);
			
			if($get == null) {return $lstPilote;}
			else
			{
				$json = Zend_Json::encode($lstPilote);
				echo $json;exit;
			}
		}
	}
	
	public function calendrierpiloteAction()
	{
		if(Services_verifAcces('Planning'))
		{
			//On change de layout pour pas avoir le header etc
			$layout = Zend_Layout::getMvcInstance();
			$layout->setLayout('api');
			
			
			$idPilote = $this->_getParam('pilote', null);
			$dateDepart = $this->_getParam('dateDepart', null);
			$dateArrivee = $this->_getParam('dateArrivee', null);
			
			if($idPilote == null || $dateDepart == null || $dateArrivee == null) {exit;}
			
			$dateDepParam = new Zend_Date($dateDepart);
			$dateArrParam = new Zend_Date($dateArrivee);
			
			$dateDeb = new Zend_Date($dateDepart);
			$dateDeb->setHour(0);
			$dateDeb->setMinute(0);
			$dateDeb->setSecond(0);
			
			$arr_subDate = array(
				'lundi' => 6,
				'mardi' => 5,
				'mercredi' => 4,
				'jeudi' => 3,
				'vendredi' => 2,
				'samedi' => 1,
				'dimanche' => 0
			);
			$dateDeb->addDay($arr_subDate[$dateDeb->toString('EEEE')]);
			
			$retour = array();
			for($i=1;$i<=4;$i++)
			{
				$dateDeb->addDay(1);
				$dateDeb->subMinute(1);
				$date_fin_semaine = DateFormat_SQL($dateDeb);
				$dateArrView = DateFormat_View($dateDeb, false);
				$dateDeb->addMinute(1);
				
				$dateDeb->subWeek(1);
				//$dateDeb->addDay(1);
				$date_deb_semaine = DateFormat_SQL($dateDeb);
				$dateDepView = DateFormat_View($dateDeb, false);
				$dateDepZF_U = $dateDeb->toString('U'); //Zend_Date
				$dateDeb->subDay(1);
				
				$tableVol = new Table_Vol();
				$lst_dateSQL = $tableVol->get_LstVolPilote_entreDate($idPilote, $date_deb_semaine, $date_fin_semaine);
				
				$lst_date = array();
				if(count($lst_dateSQL) > 0)
				{
					foreach($lst_dateSQL as $key => $val)
					{
						if($val['dateHeureDepartEffectiveVol'] == null) {$lst_date[$key]['dep'] = $val['dateHeureDepartPrevueVol'];}
						else {$lst_date[$key]['dep'] = $val['dateHeureDepartEffectiveVol'];}
					
						if($val['dateHeureArriveeEffectiveVol'] == null) {$lst_date[$key]['arr'] = $val['dateHeureArriveePrevueVol'];}
						else {$lst_date[$key]['arr'] = $val['dateHeureArriveeEffectiveVol'];}
						
						$dep = new Zend_Date($lst_date[$key]['dep']);
						$arr = new Zend_Date($lst_date[$key]['arr']);
						
						$calc1 = $dep->toString('U') - $dateDepZF_U;
						$calc2 = $arr->toString('U') - $dateDepZF_U;
						
						/*
						echo '
							$dateDepZF_U : '.$dateDepZF_U.'<br/>
							$dep->toString(\'U\') : '.$dep->toString('U').'<br/>
							$arr->toString(\'U\') : '.$arr->toString('U').'<br/><br/>
							$calc1 : '.$calc1.'<br/>
							$calc2 : '.$calc2.'<br/>
							<br/><br/>
						';
						*/
						
						$nbHoursBeforeDep = $calc1 / 60 / 60;
						$nbHoursBeforeArr = $calc2 / 60 / 60;
						$lst_date[$key]['nbHoursBeforeDep'] = floor($nbHoursBeforeDep);
						$lst_date[$key]['nbHoursBeforeArr'] = floor($nbHoursBeforeArr);
					}
				}
				
				$retour[] = array(
					'date_dep' => $dateDepView,
					'date_arr' => $dateArrView,
					'dateDepSQL' => $date_deb_semaine,
					'lst_date' => $lst_date
				);
				
				if($i == 1)
				{
					$calc1 = $dateDepParam->toString('U') - $dateDepZF_U;
					$calc2 = $dateArrParam->toString('U') - $dateDepZF_U;
					$nbHoursBeforeDep = $calc1 / 60 / 60;
					$nbHoursBeforeArr = $calc2 / 60 / 60;
					$this->view->nbHoursBeforeDep = floor($nbHoursBeforeDep);
					$this->view->nbHoursBeforeArr = floor($nbHoursBeforeArr);
				}
			}
			
			$table_pilote = new Table_Pilote();
			$infosPilote = $table_pilote->fetchRow('idPilote="'.$idPilote.'"')->toArray();
			//echo '<pre>';print_r($retour);echo '</pre>';
			
			$this->view->pilote = $infosPilote['nomPilote'].' '.$infosPilote['prenomPilote'];
			$this->view->tableaux = $retour;
		}
	}
	
	public function formprixclasseAction()
	{
		if(Services_verifAcces('Planning'))
		{
			//On change de layout pour pas avoir le header etc
			$layout = Zend_Layout::getMvcInstance();
			$layout->setLayout('api');
			
			$idModele = $this->_getParam('idModele', null);
			if($idModele != null)
			{
				$tableClasse = new Table_Classe();
				$lstClasses = $tableClasse->get_LstClasses_PourModele($idModele);
				$this->view->LstClasses = $lstClasses;
			}
		}
	}
        
        public function retardAction()
        {
            $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
            
            $tableVol = new Table_Vol();
            $retards = $tableVol->GetVolRetardataire();
            
            $this->view->retards = $retards;
        }
}