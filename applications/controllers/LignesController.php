<?php

class LignesController extends Zend_Controller_Action
{
    public function init() 
    {
        $this->headStyleScript = array();
    
        if(!session_encours())
        {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
    }
	
    public function indexAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
    }

    public function consulterAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $tableLigne = new Table_Ligne;
        $lignes = $tableLigne->getLignes();
        $this->view->lignes= $lignes;
        
        $nbVolsLigne = array();
        foreach ($lignes as $ligne)
        {
           $nbVolsLigne[$ligne['idLigne']] = $tableLigne->getNbVolsDisponibles($ligne['idLigne']);
        }
        $this->view->nbVolsLigne = $nbVolsLigne;

    }
    public function ajouterAction()
    {
        $trigDepart = $this->getRequest()->getPost('trigDepart');
        $trigArrivee = $this->getRequest()->getPost('trigArrivee');
        $periode = $this->getRequest()->getPost('periodicite');
        
        if($trigDepart == $trigArrivee)
        {
            $message = "<div class=\"erreur\">L'aéroport de départ doit être différent de l'aéroport d'arrivée</div>";
        }
        else
        {
            $tableLigne = new Table_Ligne;
            $existe = $tableLigne->existeLigne($trigDepart, $trigArrivee);
            if(!$existe)
            {
                //Zend_Debug::dump($existe);exit;
                $donnees = array(
                    'trigrammeAeroportDepart' => $trigDepart,
                    'trigrammeAeroportArrivee' => $trigArrivee,
                    'idPeriodicite' => $periode
                );                
                $tableLigne->ajouter($donnees);
                $message = '<div class="reussi">Ligne créée !</div>';
            }
            else 
            {
                //Zend_Debug::dump($existe);exit;
                $message = '<div class="information">Cette ligne existe déja !<br />
                    Souhaitez vous <a href="'.$this->view->baseUrl('/lignes/premodif/idligne/'.$existe['idLigne']).'">la modifier</a> ?</div>';
            }
        }
        $this->_helper->FlashMessenger($message);
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoUrl($this->view->baseUrl('/directionstrategique/ajouterligne'));
    }    
    public function premodifAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        $idLigne = $this->_getParam('idligne');
        
        $this->view->message = $this->_helper->FlashMessenger->getMessages();
        
        $tableLigne = new Table_Ligne;
        $tablePeriodicite = new Table_Periodicite;
        
        $periodLigne = $tableLigne->getPeriodiciteLigne($idLigne);
        $periodicites = $tablePeriodicite->getPeriodicites();        
        $newPeriodicites = array();
        foreach($periodicites as $periodicite)
        {
            $newPeriodicites[$periodicite['idPeriode']] = $periodicite['nomPeriode'];
        }
        $form = new Zend_Form;
        $form->setMethod('post');
        $form->setAction('/lignes/modifier/idligne/'.$idLigne);
        
        $ePeriode = new Zend_Form_Element_Select('sel_periode');
        $ePeriode->setLabel('Changer la periodicité :');
        $ePeriode->addMultiOptions($newPeriodicites);
        $ePeriode->setValue($periodLigne);
        
        $eSubmit = new Zend_Form_Element_Submit('sub_modifLigne');
        $eSubmit->setName('Modifier');
        $eSubmit->setAttrib('class', 'ajouter');
        
        $form->addElements(array(
            $ePeriode,
            $eSubmit
        ));
        $this->view->laLigne = $tableLigne->getUneLigne($idLigne);
        $this->view->formModif = $form;   
    }
    public function modifierAction()
    {
        $idPeriode = $this->getRequest()->getPost('sel_periode');
        $idLigne = $this->_getParam('idligne');
       // echo $idPeriode;exit;
        try{
        $tableLigne = new Table_Ligne;
        $tableLigne->modifier($idPeriode, $idLigne);
        }catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
        $this->_helper->FlashMessenger('<div class="reussi">Modification terminée</div>');
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoUrl($this->view->baseUrl('/lignes/premodif/idligne/'.$idLigne));
    }
}
?>