<?php
    class Table_Intervention extends Zend_Db_Table_Abstract
    {
        protected $_name = 'intervention';
        
        protected $_primary = 'numeroIntervention';
        
        //Clés étrangères
        protected $_referenceMap = array(
                'Avion' => array(
                    'columns' => 'immatriculationAvion',
                    'refTableClass' => 'Table_Avion'
                     )
            );
        /*
        public function Ajouter($p_numeroIntervention, $p_immatriculationAvion, $p_datePrevue, $p_dateEffective, $p_typeIntervention) {
           // $db = Zend_Registry::get('db');
            
            $data = array('numeroIntervention' => $p_numeroIntervention,
                'immatriculationAvion' => $p_immatriculationAvion, 
                'datePrevueIntervention' => $p_datePrevue, 
                'dateEffectiveIntervention' => $p_dateEffective, 
                'typeIntervention' => $p_typeIntervention);
            $this->insert($data);
        }*/
        // creer une intervention de type $typeInter sur l'avion $immatAvion a la date Prevue $dateInter
        public function ajouter($immatAvion, $dateInter,$typeInter, $taf)
        {
            $ajout=array();
            try {
                if($dateInter != null)
                {                    
                    $data = array(
                        'immatriculationAvion' => $immatAvion,
                        'datePrevueIntervention' => $dateInter,
                        'typeIntervention' => $typeInter,
                        'taf' => $taf
                    );
                    $this->insert($data);
                    $ajout['message'] = 'Intervention créée';
                    $ajout['class'] = 'reussi';
                }
                else
                {
                    $ajout['message'] = 'Vous n\'avez pas correctement saisi la date';
                    $ajout['class'] = 'erreur';
                }
            }
            catch (Exception $e)
            {
                $ajout['message'] = 'Erreur lors de la création';
                $ajout['class'] = 'erreur';
            }
            return $ajout;
        }
        public function getLesInterventions($matriculeTech)
        {
            try {
            $reqInter = $this->select()
                            ->setIntegrityCheck(false)
                            ->from($this->_name, '*')
                            ->join(array('p'=>'proceder'),'p.numeroIntervention = intervention.numeroIntervention')
                            ->where('p.matriculeTechnicien = ?', $matriculeTech)
                            ;
            $lesInters = $this->fetchAll($reqInter)->toArray();
            Zend_Debug::dump($lesInters);
            exit;
            }
            catch (Exception $e)
            {
                 Zend_Debug::dump($e);exit;
            }
            return $lesInters;
        }
        public function dernierAjout()
        {
            return $this->getAdapter()->lastInsertId();
        }
    }
