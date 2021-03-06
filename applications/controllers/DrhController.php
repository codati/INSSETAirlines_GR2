<?php
/**
 * Contrôleur de direction stratégique
 * 
 * PHP version 5
 * 
 * @category INSSET
 * @package  Airline
 * @author   Elie DHERVILLE <eliedherville@hotmail.com>
 * @author   Fabien Piercourt <fabien.piercourt@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Drh
 */

/**
 * Classe du contrôleur direction stratégique
 * 
 * @category INSSET
 * @package  Airline
 * @author   Elie DHERVILLE <eliedherville@hotmail.com>
 * @author   Fabien Piercourt <fabien.piercourt@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     /Drh
 */
class DrhController extends Zend_Controller_Action
{
    /**
	 * Méthode d'initialisation du contrôleur.
	 * Permet de déclarer les css & js à utiliser.
	 * 
	 * @return null
	 */
    public function init()
    {
    	$this->headStyleScript = array(
            'css' => 'personaviguant',
			'js' => array('formTech', 'personaviguant')
		);
        
        if (!session_encours()) {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
        if (!Services_verifAcces('DRH')) {
            throw new Zend_Controller_Action_Exception('', 403);
        }
    }

    /**
     * Page index
     * 
     * @return null
     */
    public function indexAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
    }

    /**
     * Gestion des techniciens
     * 
     * @return null
     */
    public function gestiontechnicienAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tableTech = new Table_Technicien;
        $lesTechs = $tableTech->getTechs();      

        $this->view->lesTechs = $lesTechs;
    }

    /**
     * page api
     * 
     * @return null
     */
    public function apiAction()
    {
        //On change de layout : pour ne pas avoir les balises body/head etc
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('api');

        //$_GET['date']
        $techDate = $this->_getParam('date');
        //$techDate = $this->getRequest()->getGet('date');
        //echo $techDate;
        $testDate = (Zend_Date::isDate($techDate, 'YYYY-MM-dd') ? true : false);

        if ($testDate == true) {
        	echo '1';
        } else {
            echo '0';
        }
        exit;
    }

    /**
     * Ajouter technicien (formulaire)
     * 
     * @return null
     */
    public function ajoutertechnicienAction() 
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $espaceSession = new Zend_Session_Namespace('AjoutTechCourant');

        $nom = $espaceSession->nom;
        $pnom = $espaceSession->pnom;
        $adresse = $espaceSession->adresse;
        $date = $espaceSession->date;

        // creer un objet formulaire
        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('class', 'form');

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
        $eSubmit->setAttrib('class', 'valider');

        $monform->addElement($eNomTech);
        $monform->addElement($ePrenomTech);
        $monform->addElement($eAdresseTech);
        $monform->addElement($eDateTech);
        $monform->addElement($eSubmit);       

        $this->view->leform = $monform;
    }

    /**
     * Ajout d'un technicien (validation sql)
     * 
     * @return null
     */
    public function ajoutsqltechnicienAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

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

        if (($techNom != null) or ($techPrenom != null)) {          
            $ajoutsql = $tableTech->Ajouter($techNom, $techPrenom, $techAdresse, $techDate);

            if ($ajoutsql == true) {
                $espaceSession->nom = "";
                $espaceSession->pnom = ""; 
                $espaceSession->adresse = "";
                $espaceSession->date = ""; 
                $espaceSession->VerifAjout = true; 

                $message = '<h3 class="reussi">Ajout réussi</h3>';
            } else {                  
                $message = '<h3 class="erreur">Ajout échoué</h3>';
            }
        } else {
            $message = '<h3 class="erreur">Ajout échoué, saisie invalide<br><br>Le nom et le prénom doivent être saisis</h3>';
        }
        $this->view->message = $message;
    }

    /**
     * Modification du technicien (formulaire)
     * 
     * @return null
     */
    public function modifiertechnicienAction() 
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tableTech = new Table_Technicien;

        $espaceSession = new Zend_Session_Namespace('ModifTechnicienChoisi');
        $test = $espaceSession->test;
        $matricule = $this->_getParam('matricule');
        $espaceSession->matricule = $matricule;
        
        $infosTech = $tableTech->getInfos($matricule);  

        if ($test != "echoue") {
            $nom = $infosTech['nomTechnicien'];
            $pnom = $infosTech['prenomTechnicien'];
            $adresse = $infosTech['adresseTechnicien'];
            $date = $infosTech['dateNaissanceTechnicien'];     
        } else { 
            $nom = $espaceSession->nom;
            $pnom = $espaceSession->pnom;
            $adresse = $espaceSession->adresse;
            $date = $espaceSession->date;
        }

        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('class', 'form');

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
        $eSubmit->setAttrib('class', 'valider');

        $monform->addElement($eNomTech);
        $monform->addElement($ePrenomTech);
        $monform->addElement($eAdresseTech);
        $monform->addElement($eDateTech);
        $monform->addElement($eSubmit);        

        $this->view->leform = $monform;
    }

    /**
     * Modification du technicien (sql)
     * 
     * @return null
     */
    public function modifsqltechnicienAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tableTech = new Table_Technicien;

        $techNom = $this->getRequest()->getPost('NomTech');
        $techPrenom = $this->getRequest()->getPost('PrenomTech');
        $techAdresse = $this->getRequest()->getPost('AdresseTech');
        $techDate = $this->getRequest()->getPost('dateNaissTech');

        $espaceSession = new Zend_Session_Namespace('ModifTechnicienChoisi');
        $matricule = $espaceSession->matricule;

        if (!empty($techNom) &&  !empty($techPrenom)) {
            $modifSql = $tableTech->Modifier($matricule, $techNom, $techPrenom, $techAdresse, $techDate);

            if ($modifSql == true) {
                $espaceSession->matricule = "";
                $espaceSession->nom = "";
                $espaceSession->pnom = "";
                $espaceSession->adresse = "";
                $espaceSession->date = "";
                $espaceSession->verifModif = true; 
                $espaceSession->test = "reussi";

                $message = '<h3 class="reussi">Modification réussie</h3>';                    
            } else {
                $espaceSession->nom = $techNom;
                $espaceSession->pnom = $techPrenom;
                $espaceSession->adresse = $techAdresse;
                $espaceSession->date = $techDate;

                $espaceSession->test = "echoue";

                $message = '<h3 class="echoue">Modification échouée</h3>';
            }
        } else {            
             $espaceSession->verifModif = false;   
            $message = '<h3 class="erreur">Modification échouée, saisie invalide<br><br>Les valeurs saisies ne sont pas valides</h3>';
        }
        $this->view->message = $message;
    }

    /**
     * Supprimer le technicien
     * 
     * @return null
     */
    public function supprimertechnicienAction() 
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tableTech = new Table_Technicien;

        $matricule = $this->_getParam('matricule');

        $supprSql = $tableTech->Supprimer($matricule);

        if ($supprSql == true) {
            $this->_helper->redirector('gestiontechnicien', 'drh', null, array());
        } else {
            $message = '<h3 class="erreur">Suppression échouée</h3>';
            $this->view->message = $message;
        }
    }

    /**
     * Habilitation du technicien (form)
     *
     * @author Fabien Piercourt <fabien.piercourt@gmail.com>
     * 
     * @return null
     */
    public function habilitationAction() 
    {   
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $this->view->message = $this->_helper->FlashMessenger->getMessages();

        $formHabiliter = new Zend_Form();
        // parametrer le formulaire
        $formHabiliter->setMethod('post');
        $formHabiliter->setAttrib('class', 'form');
        $formHabiliter->setAction($this->view->baseUrl().'/drh/habiliter');

        //On récupère tous les pilotes
        $tPilote = new Table_Pilote;
        $pilotes = $tPilote->fetchAll()->toArray();
        foreach ($pilotes as $unPilote) {
             $lesPilotes[$unPilote['idPilote']] = $unPilote['nomPilote'].' '.$unPilote['prenomPilote'];
        }
        //Zend_Debug::dump($lePilote);exit;
        $ePilote = new Zend_Form_Element_Select('sel_pilote');
        $ePilote->addMultiOptions($lesPilotes);
        $ePilote->setLabel('Pilote :');

        //On récupère tous les modèles d'avion
        $tModeleAvion = new Table_ModeleAvion;
        $modelesavion = $tModeleAvion->fetchAll()->toArray();
        foreach ($modelesavion as $unModele) {
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
        $eSubmit->setAttrib('class', 'valider');

        $formHabiliter->addElements(array ($ePilote, $eModele, $eDate, $eSubmit));
        $this->view->formHabiliter = $formHabiliter;
    }

    /**
     * Habilitation technicien (sql)
     *
     * @author Fabien Piercourt <fabien.piercourt@gmail.com>
     * 
     * @return null
     */
    public function habiliterAction()
    {
		$this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
		
		//on récupère les données du formulaire
		$idPilote = $this->getRequest()->getPost('sel_pilote');
		$idModeleAvion = $this->getRequest()->getPost('sel_modele');
		$dateValidite = $this->getRequest()->getPost('date');
		
		if (empty($dateValidite)) {
                    $message='<div class="erreur">Erreur ! Vous n\'avez pas saisi de date.</div>';
		} else {
                    //Mise au format sql de la date
                    $dateValidite = DateFormat_SQL(new Zend_Date(strtolower($dateValidite), 'EEEE dd MMMM YY'), false);

                    $tBreveter = new Table_Breveter;
                    //Si le brevet existe, on met a jour sa date de validite
                    if ($tBreveter->existeBrevet($idPilote, $idModeleAvion)) {
                        $donneesBrevet = array(
                            'dateValiditeBrevet' => $dateValidite
                        );

                        $where[] = $tBreveter->getAdapter()->quoteInto('idPilote = ?', $idPilote);
                        $where[] = $tBreveter->getAdapter()->quoteInto('idModeleAvion = ?', $idModeleAvion);
                        $tBreveter->update($donneesBrevet, $where);
                        $message = '<div class="reussi">La date de validité du brevet de ce pilote a bien été modifiée.</div>';
                    } else { //Sinon, on créer une nouvelle ligne dans la table breveter
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

    /**
     * Personnel naviguant (formulaire)
     *
     * @author Fabien Piercourt <fabien.piercourt@gmail.com>
     * 
     * @return null
     */
    public function personaviguantAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        //On récupère tous les pilotes
        $tPilote = new Table_Pilote;
        $lesPilotes = $tPilote->fetchAll()->toArray();
        //envoi des pilotes a la vue
        $this->view->lesPilotes = $lesPilotes;

        //formulaire d'ajout de pilote
        $formNouveauPilote = new Zend_Form();
        // parametrer le formulaire
        $formNouveauPilote->setMethod('post');
        $formNouveauPilote->setAttrib('class', 'form');
        $formNouveauPilote->setAction($this->view->baseUrl().'/drh/ajouterpilote');

        $eNom = new Zend_Form_Element_Text('NomPilote');
        $eNom->setLabel('Nom du pilote : ');
        $eNom->setAttrib('required', 'required');

        $ePrenom = new Zend_Form_Element_Text('PrenomPilote');
        $ePrenom->setLabel('Premom du pilote : ');
        $ePrenom->setAttrib('required', 'required');

        $eAdresse = new Zend_Form_Element_Text('AdressePilote');
        $eAdresse->setLabel('Adresse du pilote : ');
        $eAdresse->setAttrib("required", "required");

        $eDate = new Zend_Form_Element_Text('dateNaissPilote');
        $eDate->setLabel('Date de naissance (AAAA-MM-JJ) : ');
        $eDate->setAttrib("required", "required");
        $eDate->setAttrib("dateFormat", "yy-mm-dd");
        $eDate->setAttrib('class', 'datePick');

        $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class', 'valider');

        $formNouveauPilote->addElements(array ($eNom, $ePrenom, $eAdresse, $eDate, $eSubmit));
        $this->view->formNouveauPilote = $formNouveauPilote;
    }

    /**
     * Ajout d'un pilote (sql)
     *
     * @author Fabien Piercourt <fabien.piercourt@gmail.com>
     * 
     * @return null
     */
    public function ajouterpiloteAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));

        $tPilote = new Table_Pilote();

        $nom = $this->getRequest()->getPost('NomPilote');
        $prenom = $this->getRequest()->getPost('PrenomPilote');
        $adresse = $this->getRequest()->getPost('AdressePilote');
        $dateNais = $this->getRequest()->getPost('dateNaissPilote');

        if (empty($nom) && empty($prenom) && empty($adresse) && empty($dateNais)) {
            $message = '<div class="erreur">Erreur ! Veuillez remplir tous les champs.</div>';
        } else {
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

    /**
     * Modification d'un pilote (formulaire)
     *
     * @author Fabien Piercourt <fabien.piercourt@gmail.com>
     * 
     * @return null
     */
    public function modifierpiloteAction()
    {
        $this->_helper->actionStack('header', 'index', 'default', array('head' => $this->headStyleScript));
        $tPilote = new Table_Pilote(); 
        $idPilote = $this->_getParam('id');
        $nom = $this->getRequest()->getPost('NomPilote');
        $prenom = $this->getRequest()->getPost('PrenomPilote');
        $adresse = $this->getRequest()->getPost('AdressePilote');
        $dateNais = $this->getRequest()->getPost('dateNaissPilote');

        if (empty($nom) && empty($prenom) && empty($adresse) && empty($dateNais)) {
            $message = '<div class="information">Remplissez le formulaire ci-dessous.</div>'; 
        } else {
            $donneesPilote = array(
                'prenomPilote' => $prenom,
                'nomPilote' => $nom,
                'adressePilote' => $adresse,
                'dateNaissancePilote' => $dateNais                                
            );

            try {
                $where = $tPilote->getAdapter()->quoteInto('idPilote = ?', $idPilote); 
                $tPilote->update($donneesPilote, $where);           
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }


            $message = '<div class="reussi">Le pilote a bien été modifié.</div>';
        }
        $lePilote = $tPilote->find($idPilote)->toArray();
        $lePilote = $lePilote[0];
        //Zend_Debug::dump($lePilote);exit;


        $formPilote = new Zend_Form();
        // parametrer le formulaire
        $formPilote->setMethod('post');
        $formPilote->setAction($this->view->baseUrl().'/drh/modifierpilote/id/'.$idPilote);
        $formPilote->setAttrib('class', 'form');

        $eNom = new Zend_Form_Element_Text('NomPilote');
        $eNom->setLabel('Nom du pilote : ');
        $eNom->setValue($lePilote['nomPilote']);
        $eNom->setAttrib("required", "required");

        $ePrenom = new Zend_Form_Element_Text('PrenomPilote');
        $ePrenom->setLabel('Premom du pilote : ');
        $ePrenom->setValue($lePilote['prenomPilote']);
        $ePrenom->setAttrib("required", "required");

        $eAdresse = new Zend_Form_Element_Text('AdressePilote');
        $eAdresse->setLabel('Adresse du pilote : ');
        $eAdresse->setValue($lePilote['adressePilote']);    
        $eAdresse->setAttrib("required", "required");

        $eDate = new Zend_Form_Element_Text('dateNaissPilote');
        $eDate->setLabel('Date de naissance (AAAA-MM-JJ) : ');
        $eDate->setValue($lePilote['dateNaissancePilote']);
        $eDate->setAttrib("required", "required");
        $eDate->setAttrib("dateFormat", "yy-mm-dd");
        $eDate->setAttrib('class', 'datePick');

        $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class', 'valider');
        $formPilote->addElements(array ($eNom, $ePrenom, $eAdresse, $eDate, $eSubmit));

        $this->view->formPilote = $formPilote;     
        $this->view->message = $message;     
    }
}
