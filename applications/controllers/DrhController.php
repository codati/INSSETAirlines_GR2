<?php
class DrhController extends Zend_Controller_Action
{
    public function indexAction() 
    {   
        $this->_helper->actionStack('header','index','default',array());
    }
    
    public function habilitationAction() 
    {   
        $this->_helper->actionStack('header','index','default',array());
        
        if(Services_verifAcces('Planning'))
        {
            echo "<div class='reussi'>OUAIS TU KIFFE</div>";
        }
        else
        {
            echo "<div class='erreur'>Erreur !<br />Vous n'avez pas accès à cette page, veuillez vous identifier.<br /></div>";
        }
        
        
    }

}

