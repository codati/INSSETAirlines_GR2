<?php
class ajoutligne extends Zend_Form
{
    protected $trigs;
    protected $periodicite;
    
    public function init()
    {
        
        if($this->trigs != null) 
        {
            $optionsT = $this->getTrigrammes();
        }
        else
        {
            $optionsT = array();
        }
        if($this->periodicite != null)
        {
            $optionsP = $this->getPeriodicite();
           // Zend_Debug::dump($optionsP);exit;
        }
        else
        {
            $optionsP = array();
        }   
        
        //Zend_Debug::dump($this);exit;
        $this->setMethod('post');
        $this->setAction('/lignes/ajouter');
        
        $eTrigDepart = new Zend_Form_Element_Select('trigDepart');
        $eTrigDepart->setLabel('Choississez un aéroport de départ : ');             
        $eTrigDepart->addMultiOptions(array());
        
        $eTrigArrivee = new Zend_Form_Element_Select('trigArrivee');
        $eTrigArrivee->setLabel('Choississez un aéroport d\'arrivée : ');
        $eTrigArrivee->addMultiOptions(array());
        
        $ePeriod = new Zend_Form_Element_Select('periodicite');
        $ePeriod->setLabel('Periodicité :');
        $ePeriod->addMultiOptions(array());
        
        $this->addElements(array(
            $eTrigDepart,
            $eTrigArrivee,
            $ePeriod
        ));
    }
    
    public function setTrigrammes($p_trigs)
    {
        $this->trigs = $p_trigs;
    }
    public function getTrigrammes()
    {
        return $this->trigs;
    }
    
    public function setPeriodicite($p_periodicite)
    {
        $this->periodicite = $p_periodicite;
    }
    public function getPeriodicite()
    {
        return $this->periodicite;
    }
}
?>
