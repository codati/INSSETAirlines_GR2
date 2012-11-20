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
            
            $idLigne = $this->_getParam('idligne');
            $this->view->idLigne = $idLigne;
            
            
            $tableLigne = new Table_Ligne;
            $ligne = $tableLigne->find($idLigne)->current();
            $this->view->ligne = $ligne;
            
            $tableAeroport = new Table_Aeroport;
            $aeroportDepart = $tableAeroport->find($ligne->trigrammeAeroportDepart)->current();
            $aeroportArrivee = $tableAeroport->find($ligne->trigrammeAeroportArrivee)->current();
            $this->view->aeroportDepart = $aeroportDepart;
            $this->view->aeroportArrivee = $aeroportArrivee;
            
            $tableVol = new Table_Vol;
            $lesVols = $tableVol->get_InfosVol($idLigne);
            Zend_Debug::dump($lesVols);exit;
            
     
            
            

        }
    }
?>
