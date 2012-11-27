<?php
class ServicemaintenanceController extends Zend_Controller_Action
{
    public function indexAction()
    {
        
    }
    public function planificationAction()
    {
        $this->_helper->actionStack('header','index','default',array());
        
        $tabImmat = new Table_Avion;
        $lesImmats = $tabImmat->get_lstImmatriculations();
        
        $optionsImmat = array();
        foreach($lesImmats as $uneImmat)
        {
            $optionsImmat[$uneImmat["immatriculationAvion"]] = $uneImmat["immatriculationAvion"];            
        }
        
        $formPlanif = new Zend_Form();
        $formPlanif->setMethod('post');
        $formPlanif->setAction('/servicemaintenance/ajoutintervention');
        $formPlanif->setAttrib('id','formplanif');
        
        $eImmatAvion = new Zend_Form_Element_Select('immatAvion');
        $eImmatAvion->addMultiOptions($optionsImmat);
        $eImmatAvion->setLabel('Immatriculation de l\'avion : ');
        
        $eDateEffective = new Zend_Form_Element_Text('datePrevue');
        $eDateEffective->setAttrib('class','datePick');
        $eDateEffective->setLabel('Date de l\'intervention : ');
        $eDateEffective->setAttrib('class','datePick');
        
        $eTypeIntervention = new Zend_Form_Element_Select('sel_typeIntervention');
        $eTypeIntervention->addMultiOptions(array('petite'=>'Petite','grande'=>'Grande'));
        $eTypeIntervention->setLabel('Choisir le type de l\'intervetion à effectuer :');
        
        $eSubmit = new Zend_Form_Element_Submit('sub_intervention');
        $eSubmit->setLabel('Valider');
        $eSubmit->setAttrib('class','valider');
        
        $formPlanif->addElements(array(
            $eImmatAvion,
            $eDateEffective,
            $eTypeIntervention,
            $eSubmit
         ));
        
        $this->view->formPlanif = $formPlanif;

    }
    public function ajoutinterventionAction()            
    {
        $this->_helper->actionStack('header','index','default',array());
        
        $immatAvion = $this->getRequest()->getPost('immatAvion');
        // recupere la date et la transforme en format correct pour l'insertion en bdd
        $dateInter = $this->getRequest()->getPost('datePrevue');
        //
        if($dateInter != "")
        {
            $dateInter = DateFormat_SQL(new Zend_Date($dateInter),false);
        }
        else
        {
            $dateInter = null;
        }
        $typeInter = $this->getRequest()->getPost('sel_typeIntervention');
        
        $tableintervention = new Table_Intervention;
        $ajout = $tableintervention->ajouter($immatAvion, $dateInter, $typeInter);
        
        $this->view->ajout = $ajout;
     }
}
?>
