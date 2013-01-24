<?php
class ApiController extends Zend_Controller_Action
{
	public function init()
	{
		//On change de layout
		$layout = Zend_Layout::getMvcInstance();
		$layout->setLayout('api');
	}
	
	public function indexAction() {echo ''; exit;}
	
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
				$escales = array();
				
				//S'il y a des escales ont récupère les infos dessus
				if($resInfo_resa['nbEscale'] > 0)
				{
					$tableEscale = new Table_Escale();
					$resInfosEscales = $tableEscale->get_InfosEscales($resInfo_resa['idVol']);
					
					foreach($resInfosEscales as $i => $InfosEscales)
					{
						//On passe les dates des escales en timestamp (demandé pour l'appli android)
						$escales[$i] = $InfosEscales;
						
						if($InfosEscales['datehArriveeEffectiveEscale'] != null)
						{
							$dateDepEffectiveEscale = new Zend_Date($InfosEscales['datehArriveeEffectiveEscale']);
							$escales[$i]['datehArriveeEffectiveEscale'] = ((int) $dateDepEffectiveEscale->getTimestamp())*1000;
						}
						else {$escales[$i]['datehArriveeEffectiveEscale'] = 0;}
						
						if($InfosEscales['datehArriveePrevueEscale'] != null)
						{
							$dateDepPrevueEscale = new Zend_Date($InfosEscales['datehArriveePrevueEscale']);
							$escales[$i]['datehArriveePrevueEscale'] = ((int) $dateDepPrevueEscale->getTimestamp())*1000;
						}
						else {$escales[$i]['datehArriveePrevueEscale'] = 0;}
						
						if($InfosEscales['datehDepartEffectiveEscale'] != null)
						{
							$dateArrEffectiveEscale = new Zend_Date($InfosEscales['datehDepartEffectiveEscale']);
							$escales[$i]['datehDepartEffectiveEscale'] = ((int) $dateArrEffectiveEscale->getTimestamp())*1000;
						}
						else {$escales[$i]['datehDepartEffectiveEscale'] = 0;}
						
						if($InfosEscales['datehDepartPrevueEscale'] != null)
						{
							$dateArrPrevueEscale = new Zend_Date($InfosEscales['datehDepartPrevueEscale']);
							$escales[$i]['datehDepartPrevueEscale'] = ((int) $dateArrPrevueEscale->getTimestamp())*1000;
						}
						else {$escales[$i]['datehDepartPrevueEscale'] = 0;}
					}
				}
				
				//On réunis les infos
				$data = $resInfo_resa;
				$data['escales'] = $escales;
				$erreur = 0;
				
				$data['nbEscale'] = (int) $resInfo_resa['nbEscale'];
				
				//On enlève idVol des informations retourné
				unset($data['idVol']);
				
				//On passe les dates des infos de la réservation (1ere requête) en timestamp (demandé pour l'appli android)
				if($resInfo_resa['dateHeureDepartEffectiveVol'] != null)
				{
					$dateDepEffectiveVol = new Zend_Date($resInfo_resa['dateHeureDepartEffectiveVol']);
					$data['dateHeureDepartEffectiveVol'] = ((int) $dateDepEffectiveVol->getTimestamp())*1000;
				}
				else {$data['dateHeureDepartEffectiveVol'] = 0;}
				
				if($resInfo_resa['dateHeureDepartPrevueVol'] != null)
				{
					$dateDepPrevueVol = new Zend_Date($resInfo_resa['dateHeureDepartPrevueVol']);
					$data['dateHeureDepartPrevueVol'] = ((int) $dateDepPrevueVol->getTimestamp())*1000;
				}
				else {$data['dateHeureDepartPrevueVol'] = 0;}
				
				if($resInfo_resa['dateHeureArriveeEffectiveVol'] != null)
				{
					$dateArrEffectiveVol = new Zend_Date($resInfo_resa['dateHeureArriveeEffectiveVol']);
					$data['dateHeureArriveeEffectiveVol'] = ((int) $dateArrEffectiveVol->getTimestamp())*1000;
				}
				else {$data['dateHeureArriveeEffectiveVol'] = 0;}
				
				if($resInfo_resa['dateHeureArriveePrevueVol'] != null)
				{
					$dateArrPrevueVol = new Zend_Date($resInfo_resa['dateHeureArriveePrevueVol']);
					$data['dateHeureArriveePrevueVol'] = ((int) $dateArrPrevueVol->getTimestamp())*1000;
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
        public function renvoicodeAction()
        {
            $user = $this->getRequest()->getPost('user');
            $pass = $this->getRequest()->getPost('pass');
            
            $tableTech = new Table_Technicien;
            //$tech  = $tableTech->login($user, $pass);
            $tech  = $tableTech->login('lefebvre-catherine', md5('lefebvre'));
            
            if(!is_null($tech))
            {       
                $sessionTechnicien = new Zend_Session_Namespace('technicien');
                $sessionTechnicien->matriculeTech = $tech;
                $InfosRetour['data'] = Zend_Session::getId();
                $InfosRetour['erreur'] = 0;  
            }
            else 
            {
                $InfosRetour['data'] = 'Erreur de saisie';
                $InfosRetour['erreur'] = 1;
            }
            $json = Zend_Json::encode($InfosRetour);

            //Envoi à la vue
            $this->view->json = $json;
        }
        public function getinterventionsAction()
        {
            $idSession = $this->_getParam('idSession', 0);
            Zend_Session::setId($idSession);
            
            $sessionTechnicien = new Zend_Session_Namespace('technicien');
            
            if(!is_null($sessionTechnicien->matriculeTech))
            {
                $tableIntervention = new Table_Intervention;
                $InfosRetour['data'] = $tableIntervention->getLesInterventions($sessionTechnicien->matriculeTech);                
                $InfosRetour['erreur'] = 0;                
            }
            else 
            {
                $InfosRetour['data'] = 'Session invalide';
                $InfosRetour['erreur'] = 1;
            }
            $this->view->infosInterventions = Zend_Json::encode($InfosRetour);
        }
        public function modifierAction()
        {
            $idSession = $this->_getParam('idSession', 0);
            $numIntervention = $this->_getParam('numeroIntervention', 0);
            $tacheEff = $this->_getParam('tacheEff', 0);
            $remarques = $this->_getParam('remarques', 0);
            Zend_Session::setId($idSession);            
            
            $sessionTechnicien = new Zend_Session_Namespace('technicien');
            
            if(!is_null($sessionTechnicien->matriculeTech))
            {
                $tableProceder = new Table_Proceder;
                $donnees = array(
                    'numeroIntervention' => $numIntervention,
                    'matriculeTechnicien' => $sessionTechnicien->matriculeTech,
                    'tacheEffectuee' => $tacheEff,
                    'remarquesIntervention' => $remarques
                );
                $tableProceder->modifier($donnees);
                $InfosRetour['erreur'] = 0;  
            }
            else 
            {
                $InfosRetour['erreur'] = 1;
            }
            $this->view->infosInterventions = Zend_Json::encode($InfosRetour);
        }
}
