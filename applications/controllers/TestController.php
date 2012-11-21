<?php
    class TestController extends Zend_Controller_Action
    {
        public function indexAction() 
        {   
            $nAvion = new Table_Avion;
            echo 'lollll!';

            //$nAvion->Ajouter('TEST4', '2');
            $nAvion->Modifier('TEST', 'newTest', '2');
        }
    }
