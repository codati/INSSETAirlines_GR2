<?php
class ApiController extends Zend_Controller_Action
{
	public function init()
	{
		//On change de layout
		$layout = Zend_Layout::getMvcInstance();
		$layout->setLayout('api');
	}
	
	public function indexAction() {echo 'index'; exit;}
	
	public function infosresajsonAction()
	{
		//Préparation pour le cas d'une erreur
		$erreur = 0;
		$data = '';
		
		//On récupère le get contenant l'id de la réservation
		$id_resa = $this->_getParam('idReservation', 0);
		
		if($id_resa > 0)
		{
			$db = Zend_Registry::get('db');
			
			//On fait la requête pour récuperer les infos de la réservation
			$reqNbEscales = $db->select();
			$reqNbEscales->from('escale', 'COUNT(numeroEscale)')
						 ->where('idVol=v.idVol');
			//echo $reqNbEscales->assemble();
			
			$reqInfo_resa = $db->select();
			$reqInfo_resa->from(array('r' => 'reservation'), array(
							'etatReservation' => 'etatReservation',
							'nbEscale' => '('.new Zend_Db_Expr($reqNbEscales).')'
						 ))
						 ->join(array('c' => 'classe'), 'c.idClasse=r.idClasse', 'nomClasse')
						 ->join(array('rep' => 'typerepas'), 'rep.idTypeRepas=r.idTypeRepas', 'nomTypeRepas')
						 
						 ->join(array('v' => 'vol'), 'v.idVol=r.idVol', array(
						 	'idVol', 
						 	'remarqueVol', 
						 	'dateHeureDepartEffectiveVol',
						 	'dateHeureDepartPrevueVol',
						 	'dateHeureArriveeEffectiveVol',
						 	'dateHeureArriveePrevueVol'
						 ))
						 ->join(array('l' => 'ligne'), 'l.idLigne=v.idLigne', '')
						 
						 ->join(array('aeDep' => 'aeroport'), 'aeDep.trigrammeAeroport=l.trigrammeAeroportDepart', array('nomAeroportDepart' => 'nomAeroport'))
						 ->join(array('dDep' => 'desservir'), 'dDep.trigrammeAeroport=aeDep.trigrammeAeroport', '')
						 ->join(array('vDep' => 'ville'), 'vDep.idVille=dDep.idVille', array('villeDepart' => 'nomVille'))
						 ->join(array('pDep' => 'pays'), 'pDep.idPays=vDep.idPays', array('paysDepart' => 'nomPays'))
						 
						 ->join(array('aeArr' => 'aeroport'), 'aeArr.trigrammeAeroport=l.trigrammeAeroportArrivee', array('nomAeroportArrivee' => 'nomAeroport'))
						 ->join(array('dArr' => 'desservir'), 'dArr.trigrammeAeroport=aeArr.trigrammeAeroport', '')
						 ->join(array('vArr' => 'ville'), 'vArr.idVille=dArr.idVille', array('villeArrivee' => 'nomVille'))
						 ->join(array('pArr' => 'pays'), 'pArr.idPays=vArr.idPays', array('paysArrivee' => 'nomPays'))
						 
						 ->where('r.idReservation='.$id_resa);
			
			//echo $reqInfo_resa->assemble();
			//exit;
			
			try {$resInfo_resa = $db->fetchRow($reqInfo_resa);}
			catch (Zend_Db_Exception $e) {die ($e->getMessage());}
			
			if($resInfo_resa)
			{
				//S'il y a des escales ont récupère les infos dessus
				if($resInfo_resa['nbEscale'] > 0)
				{
					$escales = array();
					
					$reqInfosEscales = $db->select();
					$reqInfosEscales->from(array('e' => 'escale'), array(
										'datehArriveeEffectiveEscale',
										'datehArriveePrevueEscale',
										'datehDepartEffectiveEscale',
										'datehDepartPrevueEscale'
									))
									->join(array('ae' => 'aeroport'), 'ae.trigrammeAeroport=e.trigrammeAeroport', 'nomAeroport')
									->join(array('d' => 'desservir'), 'd.trigrammeAeroport=e.trigrammeAeroport', '')
									->join(array('v' => 'ville'), 'v.idVille=d.idVille', 'nomVille')
									->join(array('p' => 'pays'), 'p.idPays=v.idPays', 'nomPays')
									->where('e.idVol='.$resInfo_resa['idVol']);
					
					//echo $reqInfosEscales->assemble();
					//exit;
					
					try {$resInfosEscales = $db->fetchAll($reqInfosEscales);}
					catch (Zend_Db_Exception $e) {die ($e->getMessage());}
					
					foreach($resInfosEscales as $i => $InfosEscales)
					{
						//On passe les dates des escales en timestamp (demandé pour l'appli android)
						$escales[$i] = $InfosEscales;
						
						if($InfosEscales['datehArriveeEffectiveEscale'] != null)
						{
							$dateDepEffectiveEscale = new Zend_Date($InfosEscales['datehArriveeEffectiveEscale']);
							$escales[$i]['datehArriveeEffectiveEscale'] = (int) $dateDepEffectiveEscale->getTimestamp();
						}
						else {$escales[$i]['datehArriveeEffectiveEscale'] = 0;}
						
						if($InfosEscales['datehArriveePrevueEscale'] != null)
						{
							$dateDepPrevueEscale = new Zend_Date($InfosEscales['datehArriveePrevueEscale']);
							$escales[$i]['datehArriveePrevueEscale'] = (int) $dateDepPrevueEscale->getTimestamp();
						}
						else {$escales[$i]['datehArriveePrevueEscale'] = 0;}
						
						if($InfosEscales['datehDepartEffectiveEscale'] != null)
						{
							$dateArrEffectiveEscale = new Zend_Date($InfosEscales['datehDepartEffectiveEscale']);
							$escales[$i]['datehDepartEffectiveEscale'] = (int) $dateArrEffectiveEscale->getTimestamp();
						}
						else {$escales[$i]['datehDepartEffectiveEscale'] = 0;}
						
						if($InfosEscales['datehDepartPrevueEscale'] != null)
						{
							$dateArrPrevueEscale = new Zend_Date($InfosEscales['datehDepartPrevueEscale']);
							$escales[$i]['datehDepartPrevueEscale'] = (int) $dateArrPrevueEscale->getTimestamp();
						}
						else {$escales[$i]['datehDepartPrevueEscale'] = 0;}
					}
				}
				else {$escales = null;}
				
				//On réunis les infos
				$data = $resInfo_resa;
				$data['escales'] = $escales;
				$erreur = 0;
				
				//On enlève idVol des informations retourné
				unset($data['idVol']);
				
				//On passe les dates des infos de la réservation (1ere requête) en timestamp (demandé pour l'appli android)
				if($resInfo_resa['dateHeureDepartEffectiveVol'] != null)
				{
					$dateDepEffectiveVol = new Zend_Date($resInfo_resa['dateHeureDepartEffectiveVol']);
					$data['dateHeureDepartEffectiveVol'] = intval($dateDepEffectiveVol->getTimestamp());
				}
				else {$data['dateHeureDepartEffectiveVol'] = 0;}
				
				if($resInfo_resa['dateHeureDepartPrevueVol'] != null)
				{
					$dateDepPrevueVol = new Zend_Date($resInfo_resa['dateHeureDepartPrevueVol']);
					$data['dateHeureDepartPrevueVol'] = (int) $dateDepPrevueVol->getTimestamp();
				}
				else {$data['dateHeureDepartPrevueVol'] = 0;}
				
				if($resInfo_resa['dateHeureArriveeEffectiveVol'] != null)
				{
					$dateArrEffectiveVol = new Zend_Date($resInfo_resa['dateHeureArriveeEffectiveVol']);
					$data['dateHeureArriveeEffectiveVol'] = (int) $dateArrEffectiveVol->getTimestamp();
				}
				else {$data['dateHeureArriveeEffectiveVol'] = 0;}
				
				if($resInfo_resa['dateHeureArriveePrevueVol'] != null)
				{
					$dateArrPrevueVol = new Zend_Date($resInfo_resa['dateHeureArriveePrevueVol']);
					$data['dateHeureArriveePrevueVol'] = (int) $dateArrPrevueVol->getTimestamp();
				}
				else {$data['dateHeureArriveePrevueVol'] = 0;}
			}
			else
			{
				$erreur = 1;
				$data = 'La réservation n\'a pas été trouvée.';
			}
		}
		else
		{
			$erreur = 1;
			$data = 'Il y a un problème avec votre numéro de réservation.';
		}
		
		//On réunis les informations et le code d'erreur
		$InfosRetour['data'] = $data;
		$InfosRetour['erreur'] = $erreur;
		
		//Passage en utf8 des valeurs pour éviter des mises à null.
		
		//Pas besoin du code tant que la ligne ci-dessous est dans l'application.ini
			// database.params.charset = "utf8"
		/*
		foreach($InfosRetour['data'] as $key => $val)
		{
			if(!is_array($val)) {if(!is_int($val)) {$InfosRetour['data'][$key] = utf8_encode($val);}}
			else
			{
				foreach($InfosRetour['data'][$key] as $key2 => $val2)
				{
					if(!is_array($val2)) {if(!is_int($val2)) {$InfosRetour['data'][$key][$key2] = utf8_encode($val2);}}
					else
					{
						foreach($InfosRetour['data'][$key][$key2] as $key3 => $val3)
						{
							if(!is_array($val3) && !is_int($val3)) {$InfosRetour['data'][$key][$key2][$key3] = utf8_encode($val3);}
						}
					}
				}
			}
		}
		*/
		//echo '<pre>';print_r($InfosRetour);echo '</pre>';
		
		//Passage au format JSON
		$json = Zend_Json::encode($InfosRetour);
		
		//Envoi à la vue
		$this->view->json = $json;
	}
}
