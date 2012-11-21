<?php
    class MaintenanceController extends Zend_Controller_Action
    {
        public function indexAction() 
        {   
            $this->_helper->actionStack('header','index','default',array());
        }
        
        public function ajouteravionAction() 
        {   
            $this->_helper->actionStack('header','index','default',array());

            $tabModele = new Table_ModeleAvion;
            
            $lesModeles = $tabModele->GetListLibelle();
            //Zend_Debug::dump($lesModeles);exit;
            
            $lesOptions = array();
            
            foreach($lesModeles as $unModele)
            {
                $lesOptions[$unModele['idModeleAvion']] = $unModele['libelleModeleAvion'];
            }

            // creer un objet formulaire
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            
            $monform->setAction($this->view->baseUrl().'/maintenance/ajoutsql');

            $eImmatAvion = new Zend_Form_Element_Text('ImmatAvion');
            $eImmatAvion->setLabel('Immatriculation de l\'avion : ');
            
            $eModeleAvion = new Zend_Form_Element_Select('ModeleAvion');
            $eModeleAvion->addMultiOptions($lesOptions);
            $eModeleAvion->setLabel('Modèle de l\'avion : ');
            
            $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
            $eSubmit->setLabel('Valider');
            
            $monform->addElement($eImmatAvion);
            $monform->addElement($eModeleAvion);
            $monform->addElement($eSubmit);       

            $this->view->leform = $monform;
        }
        
        public function ajoutsqlAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tabAvion = new Table_Avion;
            
            $immat = $this->getRequest()->getPost('ImmatAvion');
            $modele = $this->getRequest()->getPost('modeleAvion');
            
            $tabAvion->Ajouter($immat, $modele);
        }
    }