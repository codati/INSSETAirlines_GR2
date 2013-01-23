<?php
    class VolsController extends Zend_Controller_Action
    {
        public function init() {$this->headStyleScript = array();}
	
        public function indexAction()
        {
            $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        }

        public function consulterAction()
        {
            $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
            
            /*On récupère, puis renvoie l'id de la ligne passé dans le lien*/
            $idLigne = $this->_getParam('idligne');
            $this->view->idLigne = $idLigne;
            
            /*On récupère puis renvoie les infos de la ligne via son id*/
            $tableLigne = new Table_Ligne;
            $ligne = $tableLigne->find($idLigne)->current();
            $this->view->ligne = $ligne;
            
            /*Récupération et envoie des informations des aéroports*/
            $tableAeroport = new Table_Aeroport;
            $aeroportDepart = $tableAeroport->find($ligne->trigrammeAeroportDepart)->current();
            $aeroportArrivee = $tableAeroport->find($ligne->trigrammeAeroportArrivee)->current();
            $this->view->aeroportDepart = $aeroportDepart;
            $this->view->aeroportArrivee = $aeroportArrivee;
            
            /*On envoie les infos des vols de la ligne*/
            $tableVol = new Table_Vol;
            $lesVols = $tableVol->get_InfosVolsLigne($idLigne);
            $this->view->lesVols = $lesVols;
            
            
            /*On met dans un tableau tous les tarifs des vols de la ligne avec l'id du vol en indice*/
            $tableValoir = new Table_Valoir;
            foreach ($lesVols as $unVol)
            {
                $lesTarifs[$unVol['idVol']] = $tableValoir->getTarifsVol($unVol['idVol']);
            }
           $this->view->lesTarifs = $lesTarifs;
           
           /*On récupérer les escales des vols via id*/
           $tableEscale = new Table_Escale;
           foreach ($lesVols as $unVol)
            {
               $lesEscales[$unVol['idVol']] = $tableEscale->get_InfosEscales($unVol['idVol']);
            }
            $this->view->lesEscales = $lesEscales;
        }
    }
?>
