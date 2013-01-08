<?php
    class Table_Technicien extends Zend_Db_Table_Abstract
    {
        protected $_name = 'technicien';
        protected $_primary = 'matriculeTechnicien';
        
        public function getTechs()
        {
            $req = $this->select()->setIntegrityCheck(false)
                    ->from($this->_name, '*')
                    ;
            
            return $this->fetchAll($req)->toArray();
        }
        
        public function Ajouter($p_nomTech, $p_prenomTech, $p_adresseTech, $p_dateNaissTech) 
        {     
            $data = array('nomTechnicien' => $p_nomTech, 'prenomTechnicien' => $p_prenomTech, 'adresseTechnicien' => $p_adresseTech, 'dateNaissanceTechnicien' => $p_dateNaissTech);
            try {                
                $this->insert($data);
            }
            catch (Exception $e)
            {
                return false;
            }
            return true;
        }
        
        public function Modifier($p_matricule, $p_nomTech, $p_prenomTech, $p_adresseTech, $p_dateNaissTech) 
        {     
            $data = array('nomTechnicien' => $p_nomTech, 'prenomTechnicien' => $p_prenomTech, 'adresseTechnicien' => $p_adresseTech, 'dateNaissanceTechnicien' => $p_dateNaissTech);
            $where = $this->getAdapter()->quoteInto('matriculeTechnicien = ?', $p_matricule);
            try {   
               $this->update($data, $where); 
            }
            catch (Exception $e)
            {
                Zend_Debug::dump($e);exit;
                return false;
            }
            return true;
        }
        
        // Abandon de la fonction
        public function Supprimer($p_matricule)
        {   
            $where = $this->getAdapter()->quoteInto('matriculeTechnicien = ?', $p_matricule);
            try {   
               $this->delete($where); 
            }
            catch (Exception $e)
            {
                Zend_Debug::dump($e);exit;
                return false;
            }
            return true;          
        }
        
        public function getInfos($p_matricule)
        {
            $req = $this->select()->setIntegrityCheck(false)
                    ->from($this->_name,'*')
                    ->where('matriculeTechnicien = ?', $p_matricule)
                    ;
            return $this->fetchRow($req)->toArray();
        }
    }