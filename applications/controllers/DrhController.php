<?php
class DrhController extends Zend_Controller_Action
{
        public function indexAction()
        {
            $this->_helper->actionStack('header','index','default',array());
        }
        
        public function gestionAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableTech = new Table_Technicien;
            $lesTechs = $tableTech->getTechs();      
            
            $this->view->lesTechs = $lesTechs;
        }
        
        public function ajouterAction() 
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $espaceSession = new Zend_Session_Namespace('AjoutTechCourant');
            
            $nom = $espaceSession->nom;
            $pnom = $espaceSession->pnom;
            $adresse = $espaceSession->adresse;
            $date = $espaceSession->date;
            
            // creer un objet formulaire
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            $monform->setAttrib('id','formAjout');
            
            $monform->setAction($this->view->baseUrl().'/technicien/ajoutsqltechnicien');

            $eNomTech = new Zend_Form_Element_Text('NomTech');
            $eNomTech->setValue($nom);
            $eNomTech->setLabel('Nom du technicien : ');
            
            $ePrenomTech = new Zend_Form_Element_Text('PrenomTech');
            $ePrenomTech->setValue($pnom);
            $ePrenomTech->setLabel('Premom du technicien : ');
            
            $eAdresseTech = new Zend_Form_Element_Text('AdresseTech');
            $eAdresseTech->setValue($adresse);
            $eAdresseTech->setLabel('Adresse du technicien : ');
            
            $eDateTech = new Zend_Form_Element_Text('dateNaissTech');
            $eDateTech->setValue($date);
            $eDateTech->setLabel('Date de naissance : ');
            
            $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
            $eSubmit->setLabel('Valider');
            $eSubmit->setAttrib('class','valider');
            
            $monform->addElement($eNomTech);
            $monform->addElement($ePrenomTech);
            $monform->addElement($eAdresseTech);
            $monform->addElement($eDateTech);
            $monform->addElement($eSubmit);       

            $this->view->leform = $monform;
        }
        
        public function ajoutsqlAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableTech = new Table_Technicien;
            
            $techNom = $this->getRequest()->getPost('NomTech');
            $techPrenom = $this->getRequest()->getPost('PrenomTech');
            $techAdresse = $this->getRequest()->getPost('AdresseTech');
            $techDate = $this->getRequest()->getPost('dateNaissTech');
            $verifAjout = false;
            
            $espaceSession = new Zend_Session_Namespace('AjoutTechCourant');
            $espaceSession->nom = $techNom;
            $espaceSession->pnom = $techPrenom; 
            $espaceSession->adresse = $techAdresse;
            $espaceSession->date = $techDate; 
            $espaceSession->VerifAjout = $verifAjout;   

            if(($techNom != null) or ($techPrenom != null))
            {          
                $ajoutsql = $tableTech->Ajouter($techNom, $techPrenom, $techAdresse, $techDate);

                if($ajoutsql == true)
                {
                    $espaceSession->nom = "";
                    $espaceSession->pnom = ""; 
                    $espaceSession->adresse = "";
                    $espaceSession->date = ""; 
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
                $message = '<h3 class="erreur">Ajout échoué, saisie invalide<br><br>Le nom et le prénom doivent être saisis</h3>';
            }
            $this->view->message = $message;
        }
        
        public function modifiertechnicienAction() 
        {
            $this->_helper->actionStack('header','index','default',array());

            $espaceSession = new Zend_Session_Namespace('ModifTechnicienChoisi');
            
            $nom = $espaceSession->nom;
            $pnom = $espaceSession->pnom;
            $adresse = $espaceSession->adresse;
            $date = $espaceSession->date;
            
            $matricule = $this->_getParam('matricule');
            
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            $monform->setAttrib('id','formModif');
            
            $monform->setAction($this->view->baseUrl().'/maintenance/modifsqltechnicien');
            
            $eNomTech = new Zend_Form_Element_Text('NomTech');
            $eNomTech->setValue($nom);
            $eNomTech->setLabel('Nom du technicien : ');
            
            $ePrenomTech = new Zend_Form_Element_Text('PrenomTech');
            $ePrenomTech->setValue($pnom);
            $ePrenomTech->setLabel('Premom du technicien : ');
            
            $eAdresseTech = new Zend_Form_Element_Text('AdresseTech');
            $eAdresseTech->setValue($adresse);
            $eAdresseTech->setLabel('Adresse du technicien : ');
            
            $eDateTech = new Zend_Form_Element_Text('dateNaissTech');
            $eDateTech->setValue($date);
            $eDateTech->setLabel('Date de naissance : ');
            
            $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
            $eSubmit->setLabel('Valider');
            $eSubmit->setAttrib('class','valider');
            
            $monform->addElement($eNomTech);
            $monform->addElement($ePrenomTech);
            $monform->addElement($eAdresseTech);
            $monform->addElement($eDateTech);
            $monform->addElement($eSubmit);        

            $this->view->leform = $monform;
        }
        
        public function modifsqltechnicienAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableAvion = new Table_Avion;
            
            $avionImmat = $this->getRequest()->getPost('ImmatAvion');
            $newAvionImmat = $this->getRequest()->getPost('NewImmatAvion');
            $avionModele = $this->getRequest()->getPost('ModeleAvion');
            $verifModif = false;
            
            $newImmatUp = strtoupper($newAvionImmat);
            
            $espaceSession = new Zend_Session_Namespace('ModifAvionChoisi');
            $espaceSession->ImmatAvion = $avionImmat;
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
        
        public function supprimertechnicienAction() 
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
        
        public function habilitationAction() 
        {   
            $this->_helper->actionStack('header','index','default',array());

            if(Services_verifAcces('Planning'))
            {

            }
            else
            {
                echo "<div class='erreur'>
                            Erreur !<br />
                            Vous n'avez pas accès à cette page, veuillez vous identifier.<br />
                            <a href=\"/\">Retour</a>
                      </div>";
            }
        }

}

