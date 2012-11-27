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

