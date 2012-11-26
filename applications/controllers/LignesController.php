<?php

class LignesController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->actionStack('header','index','default',array());
    }

    public function consulterAction()
    {
        $this->_helper->actionStack('header','index','default',array());
        
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
}
?>