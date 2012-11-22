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
            
            $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');
            
            $immat = $espaceSession->ImmatAvion;
            $modele = $espaceSession->modeleAvion;
            
            // creer un objet formulaire
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            $monform->setAttrib('id','formtest');
            
            $monform->setAction($this->view->baseUrl().'/maintenance/ajoutsql');

            $eImmatAvion = new Zend_Form_Element_Text('ImmatAvion');
            $eImmatAvion->setValue($immat);
            $eImmatAvion->setLabel('Immatriculation de l\'avion : ');
            $eImmatAvion->setAttrib('required', 'required');

            $eModeleAvion = new Zend_Form_Element_Select('ModeleAvion');
            $eModeleAvion->setValue($modele);
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
            
            $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');
            $espaceSession->ImmatAvion = $immat['ImmatAvion'];
            $espaceSession->modeleAvion = $modele['modeleAvion'];
            
            $immatUp = strtoupper($immat);

            if((($immatUp != null) or ($modele != null)) AND (preg_match('#^[A-Z0-9\-]+$#', $immatUp)))
            {          
                $ajoutsql = $tabAvion->Ajouter($immatUp, $modele);

                if($ajoutsql == true)
                {
                    $message = '<h3 class="reussi">Ajout réussi</h3>';
                }
                else
                {
                    $message = '<h3 class="echoue">Ajout échoué</h3>';
                }
            }
            else
            {
                $message = '<h3 class="echoue">Ajout échoué, saisie invalide</h3>';
            }
            $this->view->message = $message;
        }
    }