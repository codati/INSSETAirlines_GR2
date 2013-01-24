<?php
class DrhController extends Zend_Controller_Action
{
    public function init()
    {
    	$this->headStyleScript = array(
               'css' => 'personaviguant',
			'js' => 'formTech'
		);
        
        if(!session_encours())
        {
            $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
            
            $tableTech = new Table_Technicien;
            $lesTechs = $tableTech->getTechs();      
            
            $this->view->lesTechs = $lesTechs;
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
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
//            Zend_Debug::dump($espaceSession->matricule);exit;
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

        $espaceSession = new Zend_Session_Namespace('ModifTechnicienChoisi');
        $matricule = $espaceSession->matricule;
//            Zend_Debug::dump($matricule);exit;
        if(!empty($techNom) &&  !empty($techPrenom))
        {          
            $modifSql = $tableTech->Modifier($matricule, $techNom, $techPrenom, $techAdresse, $techDate);
           // Zend_Debug::dump($modifSql);exit;
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
            else
            {      
                $espaceSession->nom = $techNom;
                $espaceSession->pnom = $techPrenom;
                $espaceSession->adresse = $techAdresse;
                $espaceSession->date = $techDate;

                $espaceSession->test = "echoue";

                $message = '<h3 class="echoue">Modification échouée</h3>';
            }
        }
        else
        {            
             $espaceSession->verifModif = false;   
            $message = '<h3 class="erreur">Modification échouée, saisie invalide<br><br>Les valeurs saisies ne sont pas valides</h3>';
        }
        $this->view->message = $message;
    }

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

    //Fab
    public function habilitationAction() 
    {   
      $this->_helper->actionStack('header','index','default',array());

      $this->view->message = $this->_helper->FlashMessenger->getMessages();

      $formHabiliter = new Zend_Form();
      // parametrer le formulaire
      $formHabiliter->setMethod('post');
      $formHabiliter->setAttrib('id','formHabiliter');
      $formHabiliter->setAction($this->view->baseUrl().'/drh/habiliter');

      //On récupère tous les pilotes
      $tPilote = new Table_Pilote;
      $pilotes = $tPilote->fetchAll()->toArray();
      foreach ($pilotes as $unPilote)
      {
           $lesPilotes[$unPilote['idPilote']] = $unPilote['nomPilote'].' '.$unPilote['prenomPilote'];
      }
      //Zend_Debug::dump($lePilote);exit;
      $ePilote = new Zend_Form_Element_Select('sel_pilote');
      $ePilote->addMultiOptions($lesPilotes);
      $ePilote->setLabel('Pilote :');

      //On récupère tous les modèles d'avion
      $tModeleAvion = new Table_ModeleAvion;
      $modelesavion = $tModeleAvion->fetchAll()->toArray();
      foreach ($modelesavion as $unModele)
      {
           $lesModeles[$unModele['idModeleAvion']] = $unModele['libelleModeleAvion'];
      }
      //Zend_Debug::dump($lesModeles);exit;
      $eModele = new Zend_Form_Element_Select('sel_modele');
      $eModele->addMultiOptions($lesModeles);
      $eModele->setLabel('Modèle d\'avion :');

      $eDate = new Zend_Form_Element_Text('date');
      $eDate->setAttrib('class', 'datePick');
      $eDate->setLabel('Date de validité du brevet :');
      $eDate->setAttrib('readonly', true);

      $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
      $eSubmit->setLabel('Valider');
      $eSubmit->setAttrib('class','valider');

      $formHabiliter->addElements(array ($ePilote, $eModele, $eDate, $eSubmit));
      $this->view->formHabiliter = $formHabiliter;

    }

    //Fab
    public function habiliterAction()
    {
         $this->_helper->actionStack('header','index','default',array());

         //on récupère les données du formulaire
         $idPilote = $this->getRequest()->getPost('sel_pilote');
         $idModeleAvion = $this->getRequest()->getPost('sel_modele');
         $dateValidite = $this->getRequest()->getPost('date');

         if (empty($dateValidite))
         {
              $message='<div class="erreur">Erreur ! Vous n\'avez pas saisi de date.</div>';


         }
         else
         {
                //Mise au format sql de la date
                $dateValidite = DateFormat_SQL(new Zend_Date(strtolower($dateValidite),'EEEE dd MMMM YY'),false);

                $tBreveter = new Table_Breveter;
                //Si le brevet existe, on met a jour sa date de validite
                if ($tBreveter->existeBrevet($idPilote, $idModeleAvion))
                {
                     $donneesBrevet = array(
                            'dateValiditeBrevet' => $dateValidite
                     );
                     $where[] = $tBreveter->getAdapter()->quoteInto('idPilote = ?', $idPilote);
                     $where[] = $tBreveter->getAdapter()->quoteInto('idModeleAvion = ?', $idModeleAvion);
                     $tBreveter->update($donneesBrevet, $where);

                     $message = '<div class="reussi">La date de validité du brevet de ce pilote a bien été modifiée.</div>';
                }
                //Sinon, on créer une nouvelle ligne dans la table breveter
                else
                {
                     $donneesBreveter = array(
                         'idPilote' => $idPilote,
                         'idModeleAvion' => $idModeleAvion,
                         'dateValiditeBrevet' => $dateValidite
                     );

                     $tBreveter->insert($donneesBreveter);
                     $message = '<div class="reussi">Le brevet de ce pilote a bien été créé.</div>';
                }             
         }
           $this->_helper->FlashMessenger($message);
           $redirector = $this->_helper->getHelper('Redirector');
           $redirector->gotoUrl($this->view->baseUrl('/drh/habilitation'));             
    }

    //Fab
    public function personaviguantAction()
    {
          $this->_helper->actionStack('header','index','default',array());

          //On récupère tous les pilotes
          $tPilote = new Table_Pilote;
          $lesPilotes = $tPilote->fetchAll()->toArray();
          //envoi des pilotes a la vue
          $this->view->lesPilotes = $lesPilotes;

          //formulaire d'ajout de pilote
          $formNouveauPilote = new Zend_Form();
          // parametrer le formulaire
          $formNouveauPilote->setMethod('post');
          $formNouveauPilote->setAction($this->view->baseUrl().'/drh/ajouterpilote');

          $eNom = new Zend_Form_Element_Text('NomPilote');
          $eNom->setLabel('Nom du pilote : ');

          $ePrenom = new Zend_Form_Element_Text('PrenomPilote');
          $ePrenom->setLabel('Premom du pilote : ');

          $eAdresse = new Zend_Form_Element_Text('AdressePilote');
          $eAdresse->setLabel('Adresse du pilote : ');

          $eDate = new Zend_Form_Element_Text('dateNaissPilote');
          $eDate->setLabel('Date de naissance (AAAA-MM-JJ) : ');

          $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
          $eSubmit->setLabel('Valider');
          $eSubmit->setAttrib('class','valider');

          $formNouveauPilote->addElements(array ($eNom, $ePrenom, $eAdresse, $eDate, $eSubmit));
          $this->view->formNouveauPilote = $formNouveauPilote;
    }
    //Fab
    public function ajouterpiloteAction()
    {
         $this->_helper->actionStack('header','index','default',array());

         $tPilote = new Table_Pilote();
         
         $nom = $this->getRequest()->getPost('NomPilote');
         $prenom = $this->getRequest()->getPost('PrenomPilote');
         $adresse = $this->getRequest()->getPost('AdressePilote');
         $dateNais = $this->getRequest()->getPost('dateNaissPilote');
         
         if (empty($nom) && empty($prenom) && empty($adresse) && empty($dateNais))
         {
              $message = '<div class="erreur">Erreur ! Veuillez remplir tous les champs.</div>';
         }
         else
         {
              $donneesPilote = array(
                  'prenomPilote' => $prenom,
                  'nomPilote' => $nom,
                  'adressePilote' => $adresse,
                  'dateNaissancePilote' => $dateNais                                
              );
              
              $tPilote->insert($donneesPilote);
              $message = '<div class="reussi">Le pilote a bien été créé.</div>';
         }
         $this->view->message = $message;
    }
}

