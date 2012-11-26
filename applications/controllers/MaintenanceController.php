<?php
    class MaintenanceController extends Zend_Controller_Action
    {
        public function indexAction() 
        {   
            $this->_helper->actionStack('header','index','default',array());
        }
        
        public function gestionavionAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $Avions = new Table_Avion;
            $lesAvions = $Avions->getAvions();      
            
            $this->view->lesAvions = $lesAvions;
        }
        
        public function ajouteravionAction() 
        {   
            $this->_helper->actionStack('header','index','default',array());

            $tabModele = new Table_ModeleAvion;
            
            $lesModeles = $tabModele->GetListLibelle();
            
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
            $eSubmit->setAttrib('class','valider');
            
            $monform->addElement($eImmatAvion);
            $monform->addElement($eModeleAvion);
            $monform->addElement($eSubmit);       

            $this->view->leform = $monform;
        }
        
        public function ajoutsqlAction()
        {
            
            $this->_helper->actionStack('header','index','default',array());
            
            $tabAvion = new Table_Avion;
            
            $AvionImmat = $this->getRequest()->getPost('ImmatAvion');
            $AvionModele = $this->getRequest()->getPost('ModeleAvion');
            
            $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');
            $espaceSession->ImmatAvion = $AvionImmat;
            $espaceSession->modeleAvion = $AvionModele; 
            
            $immatUp = strtoupper($AvionImmat);

            if((($immatUp != null) or ($AvionModele != null)) AND (preg_match('#^[A-Z0-9\-]+$#', $immatUp)))
            {          
                $ajoutsql = $tabAvion->Ajouter($immatUp, $AvionModele);

                if($ajoutsql == true)
                {
                    $espaceSession->ImmatAvion = "";
                    $espaceSession->modeleAvion = ""; 
                    
                    $message = '<h3 class="reussi">Ajout réussi</h3>';
                }
                else
                {                  
                    $message = '<h3 class="echoue">Ajout échoué</h3>';
                }
            }
            else
            {                
                $message = '<h3 class="echoue">Ajout échoué, saisie invalide<br><br>'.$AvionImmat.' n\'est pas une valeur valide</h3>';
            }
            $this->view->message = $message;
        }
    }