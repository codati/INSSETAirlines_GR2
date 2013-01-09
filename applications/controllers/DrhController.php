<?php
class DrhController extends Zend_Controller_Action
{
    public function init()
    {
    	$this->headStyleScript = array(
			'js' => 'formTech'
		);
	}
	
    	public function indexAction()
        {
            $this->_helper->actionStack('header','index','default',array());
        }
        
        public function gestiontechnicienAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableTech = new Table_Technicien;
            $lesTechs = $tableTech->getTechs();      
            
            $this->view->lesTechs = $lesTechs;
        }
        
        public function apiAction()
        {
            //On change de layout : pour ne pas avoir les balide body/head etc
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('api');
            
            //$_GET['date']
            $techDate = $this->_getParam('date');
            //$techDate = $this->getRequest()->getGet('date');
            //echo $techDate;
            $testDate = (Zend_Date::isDate($techDate, 'YYYY-MM-dd') ? true : false);
            
            if($testDate == true) 
            {
                echo '1';
            }
            else 
            {
                echo '0';
            }
            exit;
        }
        
        public function ajoutertechnicienAction() 
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
            
            $monform->setAction($this->view->baseUrl().'/drh/ajoutsqltechnicien');

            $eNomTech = new Zend_Form_Element_Text('NomTech');
            $eNomTech->setValue($nom);
            $eNomTech->setLabel('Nom du technicien: ');
            
            $ePrenomTech = new Zend_Form_Element_Text('PrenomTech');
            $ePrenomTech->setValue($pnom);
            $ePrenomTech->setLabel('Premom du technicien: ');
            
            $eAdresseTech = new Zend_Form_Element_Text('AdresseTech');
            $eAdresseTech->setValue($adresse);
            $eAdresseTech->setLabel('Adresse du technicien: ');
            
            $eDateTech = new Zend_Form_Element_Text('dateNaissTech');
            $eDateTech->setValue($date);
            $eDateTech->setLabel('Date de naissance (AAAA-MM-JJ): ');
            
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
        
        public function ajoutsqltechnicienAction()
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableTech = new Table_Technicien;
            
            $techNom = $this->getRequest()->getPost('NomTech');
            $techPrenom = $this->getRequest()->getPost('PrenomTech');
            $techAdresse = $this->getRequest()->getPost('AdresseTech');
            $techDate = $this->getRequest()->getPost('dateNaissTech');
            $verifAjout = false;
            
            //$techDate = DateFormat_SQL(new Zend_Date(strtolower($techDate),'EEEE dd MMMM YY'),false);
            
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

            $tableTech = new Table_Technicien;
            
            $espaceSession = new Zend_Session_Namespace('ModifTechnicienChoisi');
            $test = $espaceSession->test;
            $matricule = $this->_getParam('matricule');
            $espaceSession->matricule = $matricule;
            $infosTech = $tableTech->getInfos($matricule);  
            
            if($test != "echoue")
            {
                $nom = $infosTech['nomTechnicien'];
                $pnom = $infosTech['prenomTechnicien'];
                $adresse = $infosTech['adresseTechnicien'];
                $date = $infosTech['dateNaissanceTechnicien'];     
            }
            else
            { 
                $nom = $espaceSession->nom;
                $pnom = $espaceSession->pnom;
                $adresse = $espaceSession->adresse;
                $date = $espaceSession->date;
            }
            
            $monform = new Zend_Form;

            // parametrer le formulaire
            $monform->setMethod('post');
            $monform->setAttrib('id','formModif');
            
            $monform->setAction($this->view->baseUrl().'/drh/modifsqltechnicien');
            
            $eNomTech = new Zend_Form_Element_Text('NomTech');
            $eNomTech->setValue($nom);
            $eNomTech->setLabel('Nom du technicien: ');
            
            $ePrenomTech = new Zend_Form_Element_Text('PrenomTech');
            $ePrenomTech->setValue($pnom);
            $ePrenomTech->setLabel('Premom du technicien : ');
            
            $eAdresseTech = new Zend_Form_Element_Text('AdresseTech');
            $eAdresseTech->setValue($adresse);
            $eAdresseTech->setLabel('Adresse du technicien: ');
            
            $eDateTech = new Zend_Form_Element_Text('dateNaissTech');
            $eDateTech->setValue($date);
            $eDateTech->setLabel('Date de naissance (AAAA-MM-JJ): ');
            
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
            
            $tableTech = new Table_Technicien;
            
            $techNom = $this->getRequest()->getPost('NomTech');
            $techPrenom = $this->getRequest()->getPost('PrenomTech');
            $techAdresse = $this->getRequest()->getPost('AdresseTech');
            $techDate = $this->getRequest()->getPost('dateNaissTech');
            $verifModif = false;
            
            $espaceSession = new Zend_Session_Namespace('ModifTechnicienChoisi');
            $matricule = $espaceSession->matricule;
            
            if(($techNom != null) or ($techPrenom != null))
            {          
                if($matricule != "")
                {
                    $modifSql = $tableTech->Modifier($matricule, $techNom, $techPrenom, $techAdresse, $techDate);
                    if($modifSql == true)
                    {
                        $espaceSession->matricule = "";
                        $espaceSession->nom = "";
                        $espaceSession->pnom = "";
                        $espaceSession->adresse = "";
                        $espaceSession->date = "";
                        $espaceSession->verifModif = true; 
                        $espaceSession->test = "reussi";

                        $message = '<h3 class="reussi">Modification réussie</h3>';                    
                    }
                }
                else
                {      
                    $espaceSession->matricule = $matricule;
                    $espaceSession->nom = $techNom;
                    $espaceSession->pnom = $techPrenom;
                    $espaceSession->adresse = $techAdresse;
                    $espaceSession->date = $techDate;
                    $espaceSession->verifModif = $verifModif;
                    $espaceSession->test = "echoue";
                    
                    $message = '<h3 class="erreur">Modification échouée</h3>';
                }
            }
            else
            {                
                $message = '<h3 class="erreur">Modification échouée, saisie invalide<br><br>Les valeurs saisies ne sont pas valides</h3>';
            }
            $this->view->message = $message;
        }
        
        // Abandon de la fonction
        public function supprimertechnicienAction() 
        {
            $this->_helper->actionStack('header','index','default',array());
            
            $tableTech = new Table_Technicien;
            
            $matricule = $this->_getParam('matricule');
            
            $supprSql = $tableTech->Supprimer($matricule);
            
            if($supprSql == true)
            {
                $this->_helper->redirector('gestiontechnicien', 'drh', null, array());
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

            if(Services_verifAcces('DRH'))
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

