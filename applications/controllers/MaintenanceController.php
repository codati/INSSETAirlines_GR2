<?php
class MaintenanceController extends Zend_Controller_Action
{
    public function init() {
        $this->headStyleScript = array('css'=>'planif');
        
    }
    public function indexAction() 
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
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

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
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));

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
}