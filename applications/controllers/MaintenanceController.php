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
            
            $tableAvions = new Table_Avion;
            $lesAvions = $tableAvions->getAvions();      
            
            $this->view->lesAvions = $lesAvions;
        }
        
        public function ajouteravionAction() 
        {   
            $this->_helper->actionStack('header','index','default',array());

            $tableModele = new Table_ModeleAvion;
            
            $lesModeles = $tableModele->GetListLibelle();
            
            $lesOptions = array();
            
            foreach($lesModeles as $unModele)
            {
                $lesOptions[$unModele['idModeleAvion']] = $unModele['libelleModeleAvion'];
            }
            
            $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');
            
            $immat = $espaceSession->ImmatAvion;
            $modele = $espaceSession->ModeleAvion;
            
            // creer un objet formulaire
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            $monform->setAttrib('id','formAjout');
            
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
            
            $tableAvion = new Table_Avion;
            
            $avionImmat = $this->getRequest()->getPost('ImmatAvion');
            $avionModele = $this->getRequest()->getPost('ModeleAvion');
            $verifAjout = false;
            
            $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');
            $espaceSession->ImmatAvion = $avionImmat;
            $espaceSession->ModeleAvion = $avionModele; 
            $espaceSession->VerifAjout = $verifAjout;
            
            $immatUp = strtoupper($avionImmat);

            if((($immatUp != null) or ($avionModele != null)) AND (preg_match('#^[A-Z0-9\-]+$#', $immatUp)))
            {          
                $ajoutsql = $tableAvion->Ajouter($immatUp, $avionModele);

                if($ajoutsql == true)
                {
                    $espaceSession->ImmatAvion = "";
                    $espaceSession->ModeleAvion = ""; 
                    $espaceSession->VerifAjout = true; 
                    
                    $message = '<h3 class="reussi">Ajout réussi</h3>';
                }
                else
                {                  
                    $message = '<h3 class="erreur">Ajout échoué</h3>';
                }
            }
            else
            {                
                $message = '<h3 class="erreur">Ajout échoué, saisie invalide<br><br>'.$avionImmat.' n\'est pas une valeur valide</h3>';
            }
            $this->view->message = $message;
        }
        
        public function modifieravionAction() 
        {
            $this->_helper->actionStack('header','index','default',array());

            $tableAvion = new Table_Avion;
            $tableModele = new Table_ModeleAvion;
            
            $lesModeles = $tableModele->GetListLibelle();
            
            $lesOptions = array();
            
            foreach($lesModeles as $unModele)
            {
                $lesOptions[$unModele['idModeleAvion']] = $unModele['libelleModeleAvion'];
            }
            
            $espaceSession = new Zend_Session_Namespace('ModifAvionChoisi');
            
            $immatAvion = $this->_getParam('immat');
            
            $modele = $tableAvion->getModele($immatAvion);
            $espaceSession->oldImmat = $immatAvion;
            
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            $monform->setAttrib('id','formModif');
            
            $monform->setAction($this->view->baseUrl().'/maintenance/modifsql');
            
            $eImmatAvion = new Zend_Form_Element_Text('ImmatAvion');
            $eImmatAvion->setValue($immatAvion);
            $eImmatAvion->setLabel('Immatriculation de l\'avion : ');
            $eImmatAvion->setAttrib('readonly', 'readonly');

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
        
        public function modifsqlAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableAvion = new Table_Avion;

            $newAvionImmat = $this->getRequest()->getPost('ImmatAvion');
            $avionModele = $this->getRequest()->getPost('ModeleAvion');
            $verifModif = false;
            
            $newImmatUp = strtoupper($newAvionImmat);
            
            $espaceSession = new Zend_Session_Namespace('ModifAvionChoisi');
            $avionImmat = $espaceSession->oldImmat;
            $espaceSession->ImmatAvion = $newImmatUp;
            $espaceSession->ModeleAvion = $avionModele;
            $espaceSession->VerifModif = $verifModif;

            if((($newImmatUp != null) or ($avionModele != null)) AND (preg_match('#^[A-Z0-9\-]+$#', $newImmatUp)))
            {          
                $ajoutSql = $tableAvion->Modifier($avionImmat, $newImmatUp, $avionModele);
                if($ajoutSql == true)
                {
                    $espaceSession->ImmatAvion = "";
                    $espaceSession->ModeleAvion = ""; 
                    $espaceSession->VerifModif = true; 
                    
                    $message = '<h3 class="reussi">Modification réussie</h3>';                    
                }
                else
                {                  
                    $message = '<h3 class="echoue">Modification échouée</h3>';
                }
            }
            else
            {                
                $message = '<h3 class="erreur">Modification échouée, saisie invalide<br><br>'.$newAvionImmat.' n\'est pas une valeur valide</h3>';
            }
            $this->view->message = $message;
        }
        
        public function supprimeravionAction() 
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableAvion = new Table_Avion;
            
            $immatAvion = $this->_getParam('immat');
            
            $supprSql = $tableAvion->Supprimer($immatAvion);
            
            if($supprSql == true)
            {
                $this->_helper->redirector('gestionavion', 'Maintenance', null, array());
            }
            else
            {                  
                $message = '<h3 class="erreur">Suppression échouée</h3>';
                $this->view->message = $message;
                
            }
        }
    }