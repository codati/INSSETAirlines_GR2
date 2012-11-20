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
			$tableResa = new Table_Reservation();
			$resInfo_resa = $tableResa->getInfosResa($id_resa);
			
			if($resInfo_resa)
			{
				//S'il y a des escales ont récupère les infos dessus
				if($resInfo_resa['nbEscale'] > 0)
				{
					$escales = array();
					
					$tableEscale = new Table_Escale();
					$resInfosEscales = $tableEscale->get_InfosEscales($resInfo_resa['idVol']);
					
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
		
		//Passage au format JSON
		$json = Zend_Json::encode($InfosRetour);
		
		//Envoi à la vue
		$this->view->json = $json;
	}
}
