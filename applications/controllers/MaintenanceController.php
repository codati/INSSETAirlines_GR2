<?php
class MaintenanceController extends Zend_Controller_Action
{
    public function init() {
        $this->headStyleScript = array(
            'css'=>'planif',
            'js'=>'gestionrevision'
            );             
        
        if(!session_encours())
        {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
        if(!Services_verifAcces('Maintenance')) 
        {
            throw new Zend_Controller_Action_Exception('',403);
        }
    }
    public function indexAction() 
    {   
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }
    public function applimaintenanceAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript)); 
    }

    public function gestionavionAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tableAvions = new Table_Avion;
        $lesAvions = $tableAvions->getAvions();      

        $this->view->lesAvions = $lesAvions;
    }

    public function ajouteravionAction() 
    {   
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

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
        $monform->setAttrib('class','form');

        $monform->setAction($this->view->baseUrl().'/maintenance/ajoutavionsql');

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
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tableAvion = new Table_Avion;

        $avionImmat = $this->getRequest()->getPost('ImmatAvion');
        $avionModele = $this->getRequest()->getPost('ModeleAvion');

        $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');
        $espaceSession->ImmatAvion = $avionImmat;
        $espaceSession->ModeleAvion = $avionModele; 
        $espaceSession->VerifAjoutAvion = false;

        $immatUp = strtoupper($avionImmat);

        if((($immatUp != "") OR ($avionModele != "")) OR (preg_match('#^[A-Z0-9\-]+$#', $immatUp)))
        {          
            $ajoutsql = $tableAvion->Ajouter($immatUp, $avionModele);

            if($ajoutsql == true)
            {
                $espaceSession->ImmatAvion = "";
                $espaceSession->ModeleAvion = ""; 
                $espaceSession->VerifAjoutAvion = true; 

                $message = '<h3 class="reussi">Ajout réussi</h3>';
            }
            else
            {                  
                $message = '<h3 class="erreur">Ajout échoué</h3>';
            }
        }
        else
        {                
            $message = '<h3 class="erreur">Ajout échoué, saisie invalide<br><br>Il faut remplir tous les champs de manière correcte</h3>';
        }
        $this->view->message = $message;
    }

    public function modifieravionAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

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
        $monform->setAttrib('class','form');

        $monform->setAction($this->view->baseUrl().'/maintenance/modifavionsql');

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
    public function modifavionsqlAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tableAvion = new Table_Avion;

        $newAvionImmat = $this->getRequest()->getPost('ImmatAvion');
        $avionModele = $this->getRequest()->getPost('ModeleAvion');

        $newImmatUp = strtoupper($newAvionImmat);

        $espaceSession = new Zend_Session_Namespace('ModifAvionChoisi');
        $avionImmat = $espaceSession->oldImmat;
        $espaceSession->ImmatAvion = $newImmatUp;
        $espaceSession->ModeleAvion = $avionModele;
        $espaceSession->VerifModifAvion = false;

        if((($newImmatUp != "") AND ($avionModele != "")) AND (preg_match('#^[A-Z0-9\-]+$#', $newImmatUp)))
        {          
            $ajoutSql = $tableAvion->Modifier($avionImmat, $newImmatUp, $avionModele);
            if($ajoutSql == true)
            {
                $espaceSession->ImmatAvion = "";
                $espaceSession->ModeleAvion = ""; 
                $espaceSession->VerifModifAvion = true; 

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
    
    // Abandon de la fonction
    public function supprimeravionAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

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
    //  viens de service maintenance controller   // 
    public function planificationAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tabImmat = new Table_Avion;
        $lesImmats = $tabImmat->get_lstImmatriculations();

        $optionsImmat = array();
        foreach($lesImmats as $uneImmat)
        {
            $optionsImmat[$uneImmat["immatriculationAvion"]] = $uneImmat["immatriculationAvion"];            
        }
        
        $tabTech = new Table_Technicien;
        $lesTechs = $tabTech->getTechs();
        
        $nomTech = array();
        foreach($lesTechs as $unTech)
        {
            $nomTech[$unTech['matriculeTechnicien']] = $unTech['nomTechnicien'].' '.$unTech['prenomTechnicien'];
        }

        $formPlanif = new Zend_Form();
        $formPlanif->setMethod('post');
        $formPlanif->setAction('/maintenance/ajoutintervention');
        $formPlanif->setAttrib('id','formplanif');

        $eNomTech = new Zend_Form_Element_MultiCheckbox('sel_nomTech');
        $eNomTech->addMultiOptions($nomTech);
        $eNomTech->setLabel('Technicien(s) : ');
        $eNomTech->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label',
            array('HtmlTag', array('tag'=>'div', 'id'=>'techs'))
        ));
        
        $eImmatAvion = new Zend_Form_Element_Select('immatAvion');
        $eImmatAvion->addMultiOptions($optionsImmat);
        $eImmatAvion->setLabel('Immatriculation de l\'avion : ');

        $eDateEffective = new Zend_Form_Element_Text('datePrevue');
        $eDateEffective->setAttrib('class','datePick');
        $eDateEffective->setLabel('Date de l\'intervention : ');
        $eDateEffective->setAttrib('readonly',true);

        $eTypeIntervention = new Zend_Form_Element_Select('sel_typeIntervention');
        $eTypeIntervention->addMultiOptions(array('petite'=>'Petite','grande'=>'Grande'));
        $eTypeIntervention->setLabel('Choisir le type de l\'intervetion à effectuer :');
        
        $eTaf = new Zend_Form_Element_Textarea('area_taf');
        $eTaf->setLabel('Decrire le travail à effectuer : ');

        $eSubmit = new Zend_Form_Element_Submit('sub_intervention');
        $eSubmit->setName('Ajouter');
        $eSubmit->setAttrib('class','valider');

        $formPlanif->addElements(array(
            $eNomTech,
            $eImmatAvion,
            $eDateEffective,
            $eTypeIntervention,
            $eTaf,
            $eSubmit
         ));

        $this->view->formPlanif = $formPlanif;

    }
    public function ajoutinterventionAction()            
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $lesTechniciens = $this->getRequest()->getPost('sel_nomTech');
        //Zend_Debug::dump($matriculeTechnicien);exit;
        $immatAvion = $this->getRequest()->getPost('immatAvion');
        // recupere la date et la transforme en format correct pour l'insertion en bdd
        $dateInter = $this->getRequest()->getPost('datePrevue');
        if($dateInter != "")
        {   
            $dateInter = DateFormat_SQL(new Zend_Date(strtolower($dateInter),'EEEE dd MMMM YY'),false);            
        }
        else
        {
            $dateInter = null;
        }
        $typeInter = $this->getRequest()->getPost('sel_typeIntervention');
        $taf = $this->getRequest()->getPost('area_taf');
        
        $tableintervention = new Table_Intervention;
        $ajout = $tableintervention->ajouter($immatAvion, $dateInter, $typeInter,$taf);

        $idDernierIntervention = $tableintervention->dernierAjout();
        // ajouter ligne dans proceder
        $tableProceder = new Table_Proceder;
        if(!empty($lesTechniciens))
        {
            foreach($lesTechniciens as $unTech)
            {
                $tableProceder->creer($unTech, $idDernierIntervention);
            }
        }
        else 
        {
            $ajout['class'] = 'erreur';
            $ajout['message'] = 'Vous n\'avez pas saisi de technicien';
        }
        
        $this->view->ajout = $ajout;
     }
     public function gestionrevisionAction()
     {
         $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
         
        $tabTech = new Table_Technicien;
        $lesTechs = $tabTech->getTechs();
        
        $nomTech = array();
        foreach($lesTechs as $unTech)
        {
            $nomTech[$unTech['matriculeTechnicien']] = $unTech['nomTechnicien'].' '.$unTech['prenomTechnicien'];
        }
         
         // selectionner un technicien
         $form = new Zend_Form;
         $form->setMethod('post');
         $form->setAttrib('class', 'form');
         
         $eTech = new Zend_Form_Element_Select('sel_tech');
         $eTech->addMultiOptions($nomTech);
         $eTech->setLabel('Selectionnez un technicien :');
         
         $form->addElement($eTech);
         $this->view->formChoixTech = $form;
     }
     public function getintertechAction()
     {
         $layout = Zend_Layout::getMvcInstance();
         $layout->setLayout('api');
         
         $idTech = $this->_getParam('tech');
         $nomTech = $this->_getParam('nomTech');
         
         $tableProceder = new Table_Proceder;
         $this->view->lesInters = $tableProceder->getIntersTech($idTech);
         $this->view->idTech = $idTech;
         $this->view->nomTech = $nomTech;
     }
     public function modifierinterventionAction()
     {  
         //echo DateFormat_SQL(Zend_Date::now(),false);exit;
         $numIntervention = $this->_getParam('numInter');
         $idTech = $this->_getParam('idTechnicien');
         $nvTacheEff = $this->_getparam('modifTache');
         $nvRemarque = $this->_getParam('modifRem');

         $donnees = array(
             'numeroIntervention' => $numIntervention,
             'matriculeTechnicien' => $idTech,
             'tacheEffectuee' => $nvTacheEff,
             'remarquesIntervention' => $nvRemarque
         );
         $tableProceder = new Table_Proceder;
         $modif = $tableProceder->modifier($donnees);
         if($modif != 0)
         {
             $tableInter = new Table_Intervention;
             $tableInter->terminer($numIntervention);
             echo '<p class="reussi">Modification réussie !</p>';
         }
         else
         {
             echo '<p class="erreur">Modification échouée... Veuillez réessayer</p>';
         }
         exit;
     }
     
    public function gestionmodeleAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tableModele = new Table_ModeleAvion;
        $lesModeles = $tableModele->getLesModeles();      
        //Zend_Debug::dump($lesModeles);exit;
        $this->view->lesModeles= $lesModeles;
    }
     
     public function ajoutermodeleavionAction()
     {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $espaceSession = new Zend_Session_Namespace('AjoutAvionCourant');

        $libelleModele = $espaceSession->libelleModele;
        $longDecollage = $espaceSession->longDecollage;
        $longAtterrissage = $espaceSession->longAtterrissage;
        $rayonAction = $espaceSession->rayonAction;

        // creer un objet formulaire
        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('class','form');

        $monform->setAction($this->view->baseUrl().'/maintenance/ajoutmodelesql');
        
        $eLibelleModele = new Zend_Form_Element_Text('libelleModeleAvion');
        $eLibelleModele->setValue($libelleModele);
        $eLibelleModele->setLabel('Libellé du modèle : ');
        $eLibelleModele->setAttrib('required', 'required');
        
        $eLongDecollage = new Zend_Form_Element_Text('longueurDecollage');
        $eLongDecollage->setValue($longDecollage);
        $eLongDecollage->setLabel('Longueur afin de décoller : ');
        
        $eLongAtterrissage = new Zend_Form_Element_Text('longueurAtterrissage');
        $eLongAtterrissage->setValue($longAtterrissage);
        $eLongAtterrissage->setLabel('Longueur néccessaire pour atterrir : ');
        
        $eRayonAction = new Zend_Form_Element_Text('rayonAction');
        $eRayonAction->setValue($rayonAction);
        $eRayonAction->setLabel('Rayon d\'action : ');

        $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class','valider');

        $monform->addElement($eLibelleModele);
        $monform->addElement($eLongDecollage);
        $monform->addElement($eLongAtterrissage);
        $monform->addElement($eRayonAction);
        $monform->addElement($eSubmit);       

        $this->view->leform = $monform;
    }

    public function ajoutmodelesqlAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tableModele = new Table_ModeleAvion;

        $libelleModele = $this->getRequest()->getPost('libelleModeleAvion');
        $longDecollage = $this->getRequest()->getPost('longueurDecollage');
        $longAtterrissage = $this->getRequest()->getPost('longueurAtterrissage');
        $rayonAction = $this->getRequest()->getPost('rayonAction');

        $espaceSession = new Zend_Session_Namespace('AjoutModeleAvion');
        $espaceSession->libelleModele = $libelleModele;
        $espaceSession->longDecollage = $longDecollage;
        $espaceSession->longAtterrissage = $longAtterrissage;
        $espaceSession->rayonAction = $rayonAction;
        $espaceSession->VerifAjoutModel = false;
        
        if($libelleModele != "")
        {          
            $ajoutsql = $tableModele->Ajouter($longDecollage, $longAtterrissage, $rayonAction, $libelleModele);

            if($ajoutsql == true)
            {
                $espaceSession->libelleModele = "";
                $espaceSession->longDecollage = "";
                $espaceSession->longAtterrissage = "";
                $espaceSession->rayonAction = "";
                $espaceSession->VerifAjoutModel = true; 

                $message = '<h3 class="reussi">Ajout réussi</h3>';
            }
            else
            {                  
                $message = '<h3 class="erreur">Ajout échoué</h3>';
            }
        }
        else
        {                
            $message = '<h3 class="erreur">Ajout échoué, saisie invalide<br><br>'.$libelleModele.' n\'a pas été saisie</h3>';
        }
        $this->view->message = $message;
    }
    
    public function modifiermodeleAction() 
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $espaceSession = new Zend_Session_Namespace('ModifModeleChoisi');
        
        $tableModele = new Table_ModeleAvion;
        $idModele = $this->_getParam('idModele');
        $espaceSession->idModeleModif = $idModele;
        
        $leModele = $tableModele->getModeleById($idModele);
        //Zend_Debug::dump($leModele);exit;
        //Zend_Debug::dump($espaceSession->verifModif);exit;
        if(($espaceSession->VerifModifModel == true) OR ($espaceSession->VerifModifModel == ""))
        {
            $libelleModele = $leModele["libelleModeleAvion"];
            $longDecollage = $leModele["longueurDecollage"];
            $longAtterrissage = $leModele["longueurAtterrissage"];
            $rayonAction = $leModele["rayonAction"];
        }
        else
        {
            $libelleModele = $espaceSession->libelleModele;
            $longDecollage = $espaceSession->longDecollage;
            $longAtterrissage = $espaceSession->longAtterrissage;
            $rayonAction = $espaceSession->rayonAction;
        }
        
        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('class','form');

        $monform->setAction($this->view->baseUrl().'/maintenance/modifmodelesql');

        $eLibelleModele = new Zend_Form_Element_Text('libelleModeleAvion');
        $eLibelleModele->setValue($libelleModele);
        $eLibelleModele->setLabel('Libellé du modèle : ');
        $eLibelleModele->setAttrib('required', 'required');
        
        $eLongDecollage = new Zend_Form_Element_Text('longueurDecollage');
        $eLongDecollage->setValue($longDecollage);
        $eLongDecollage->setLabel('Longueur afin de décoller : ');
        
        $eLongAtterrissage = new Zend_Form_Element_Text('longueurAtterrissage');
        $eLongAtterrissage->setValue($longAtterrissage);
        $eLongAtterrissage->setLabel('Longueur néccessaire pour atterrir : ');
        
        $eRayonAction = new Zend_Form_Element_Text('rayonAction');
        $eRayonAction->setValue($rayonAction);
        $eRayonAction->setLabel('Rayon d\'action : ');

        $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class','valider');

        $monform->addElement($eLibelleModele);
        $monform->addElement($eLongDecollage);
        $monform->addElement($eLongAtterrissage);
        $monform->addElement($eRayonAction);
        $monform->addElement($eSubmit);       

        $this->view->leform = $monform;
    }

    public function modifmodelesqlAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

        $tableModele = new Table_ModeleAvion;

        $libelleModele = $this->getRequest()->getPost('libelleModeleAvion');
        $longueurModele = $this->getRequest()->getPost('longueurDecollage');
        $longueurAtterrissage = $this->getRequest()->getPost('longueurAtterrissage');
        $rayonAction = $this->getRequest()->getPost('rayonAction');

        $espaceSession = new Zend_Session_Namespace('ModifModeleChoisi');
        $idModele = $espaceSession->idModeleModif;
        $espaceSession->libelleModele = $libelleModele;
        $espaceSession->longDecollage = $longueurModele;
        $espaceSession->longAtterrissage = $longueurAtterrissage;
        $espaceSession->rayonAction = $rayonAction;
        $espaceSession->VerifModifModel = false;

        if($libelleModele != "")
        {          
            $ajoutSql = $tableModele->Modifier($idModele, $longueurModele, $longueurAtterrissage, $rayonAction, $libelleModele);
            if($ajoutSql == true)
            {
                $espaceSession->libelleModele = "";
                $espaceSession->longModele = "";
                $espaceSession->longAtterrissage = "";
                $espaceSession->rayonAction = "";
                $espaceSession->VerifModifModel = true;

                $message = '<h3 class="reussi">Modification réussie</h3>';                    
            }
            else
            {                  
                $message = '<h3 class="echoue">Modification échouée</h3>';
            }
        }
        else
        {                
            $message = '<h3 class="erreur">Modification échouée, saisie invalide<br><br>'.$libelleModele.' ne peut être vide</h3>';
        }
        $this->view->message = $message;
    }
    
    public function dispoavionsAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $tableAvion = new Table_Avion();
        $avionsDispo = $tableAvion->GetAvionsDispo();
        
        $this->view->avionsDispo = $avionsDispo;
    }
}
