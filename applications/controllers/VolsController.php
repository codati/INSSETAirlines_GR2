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
        }
    }
?>
