<?php

class LogistiquecommercialeController extends Zend_Controller_Action
{
    public function init() 
    {
        $this->headStyleScript = array(
                    'js' => 'logistiqueCommerciale',
                    'css' => 'logistiqueCommerciale'
            
        );
    
        if(!session_encours())
        {
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl());  
        }
    }
    
    public function infosvolAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $espaceSession = new Zend_Session_Namespace('RetourTest');
        echo $espaceSession->messageErreur;
        $espaceSession->messageErreur = "";
        
        $monform = new Zend_Form;

        // parametrer le formulaire
        $monform->setMethod('post');
        $monform->setAttrib('class','form');

        $monform->setAction($this->view->baseUrl().'/logistiquecommerciale/infosduvol');

        $eIdVol = new Zend_Form_Element_Text('idVol');
        $eIdVol->setLabel('Numero de vol : ');
        $eIdVol->setAttrib('required', 'required');

        $eSubmit = new Zend_Form_Element_Submit('bt_sub');    
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class','valider');

        $monform->addElement($eIdVol);
        $monform->addElement($eSubmit);       

        $this->view->leform = $monform;
    }
    
    public function infosduvolAction()
    {
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        
        $idVol = $this->getRequest()->getPost('idVol');
        
        $tableVol = new Table_Vol();
        $tableEscale = new Table_Escale();
        $tableReservation = new Table_Reservation();
        
        if((preg_match('#^[0-9\-]+$#', $idVol)) AND ($tableVol->existeVol($idVol)))
        {        
            $infosVol = $tableVol->get_InfosVol($idVol);
            $infosEscale = $tableEscale->get_InfosEscales($idVol);
            $infosRepas = $tableReservation->GetNbTypeRepasParReservationEtParVol($idVol);

            //Zend_Debug::dump($infosRepas);exit;
            $this->view->infosVol = $infosVol;
            $this->view->infosEscale = $infosEscale;
            $this->view->infosRepas = $infosRepas;
        }
        else
        {
            $espaceSession = new Zend_Session_Namespace('RetourTest');
            $espaceSession->messageErreur = '<h3 class="erreur">Saisie invalide.</h3>';
            
            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->gotoUrl($this->view->baseUrl('/logistiquecommerciale/infosvol'));
        }
    }
    
    //Fab
    public function gererpromosAction()
    {
          $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
          
          /*
           * Récupérer tous les vols dont : 
           * le départ < à un mois
           * qui n'ont pas encore de promo 
           */
          $tVol = new Table_Vol();
          $dateDebut = DateFormat_SQL(Zend_Date::now());
          $dateFin = DateFormat_SQL(Zend_Date::now()->addMonth(1));

          $lesVolsAVenir = $tVol->getVolsPlanifiesEntreDate($dateDebut, $dateFin);
          
          $this->view->lesVolsAVenir = $lesVolsAVenir;
                  
          
    }
    
    public function tamereAction()
    {
         $idVol = $this->_getParam('idVol');   
         $tValoir = new Table_Valoir();
         $lesClasses = $tValoir->getClassesVol($idVol);
         
         //Créer le formulaire
         $formAjoutPromo = new Zend_Form();
         // parametrer le formulaire
         $formAjoutPromo->setMethod('post');
         $formAjoutPromo->setAttrib('id','formAjoutPromo');
         
         $tabPourcent = array();
         for($i = 0 ; $i <= 75; $i+=5)
         {
             $tabPourcent[$i] = $i;
         }
         foreach ($lesClasses as $uneClasse)
         {
              $ePourcentagePromo = new Zend_Form_Element_Select('sel_pourcent_'.$uneClasse['idClasse']);
              $ePourcentagePromo->setLabel('% de remise sur la '.$uneClasse['nomClasse'].'');
              $ePourcentagePromo->addMultiOptions($tabPourcent);
              $formAjoutPromo->addElement($ePourcentagePromo);
              
         }
         $eSubmit = new Zend_Form_Element_Button('sub_submit');
         $eSubmit->setLabel('Valider');
         $eSubmit->setAttrib('onclick', "return test2($idVol);");
         $formAjoutPromo->addElement($eSubmit);
         echo $formAjoutPromo;
         exit;
    }
    public function nvpromoAction()
    {
         $tVol = new Table_Vol();
         $tValoir = new Table_Valoir();
         
         $idVol = $this->_getParam('idVol');
         $leVol = $tVol->find($idVol)->toArray();
        // Zend_Debug::dump($leVol);exit;
         $rPC = $this->_getParam('rPC', 0);//2
         $rCE = $this->_getParam('rCE', 0);//1
         $rCA = $this->_getParam('rCA', 0);//3
         $dateFinPromo = $leVol[0]['dateHeureDepartPrevueVol'];
         
         $test = 'infos ';
         if ($rPC != 0)
         {
               $donneesValoir = array(
                            'dateFinPromo' => $dateFinPromo,
                            'dateDebutPromo' => DateFormat_SQL(Zend_Date::now()),
                            'pourcentagePromo' => $rPC
                     );
               $where[] = $tValoir->getAdapter()->quoteInto('idVol = ?', $idVol);
               $where[] = $tValoir->getAdapter()->quoteInto('idClasse = ?', 2);
               $test = $tValoir->update($donneesValoir, $where);
         }
         if ($rCE != 0)
         {
               $donneesValoir = array(
                            'dateFinPromo' => $dateFinPromo,
                            'dateDebutPromo' => DateFormat_SQL(Zend_Date::now()),
                            'pourcentagePromo' => $rCE
                     );
               $where[] = $tValoir->getAdapter()->quoteInto('idVol = ?', $idVol);
               $where[] = $tValoir->getAdapter()->quoteInto('idClasse = ?', 1);
               $test .= ' '.$tValoir->update($donneesValoir, $where);
         }
         if ($rCA != 0)
         {
               $donneesValoir = array(
                            'dateFinPromo' => $dateFinPromo,
                            'dateDebutPromo' => DateFormat_SQL(Zend_Date::now()),
                            'pourcentagePromo' => $rPC
                     );
               $where[] = $tValoir->getAdapter()->quoteInto('idVol = ?', $idVol);
               $where[] = $tValoir->getAdapter()->quoteInto('idClasse = ?', 3);
               $test .= ' '.$tValoir->update($donneesValoir, $where);
         }         
         echo $test;
         exit;
    }
}
