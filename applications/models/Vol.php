<?php
    class Table_Vol extends Zend_Db_Table_Abstract
    {
        protected $_name = 'vol';
        protected $_primary = 'idVol';

        //Clés étrangères
        protected $_referenceMap = array(
                'Ligne' => array(
                    'columns' => 'idLigne',
                    'refTableClass' => 'Table_Ligne'
                     ),
                'Avion' => array(
                    'columns' => 'immatriculationAvion',
                    'refTableClass' => 'Table_Avion'
                    ),
            );
        /*************** Fonctions ***************/ 
        //Renvoie des informations sur tous les vols à venir et planifiés d'une ligne
        public function get_InfosVolsLigne($idLigne)
        {
            $date = Zend_Date::now(); // date actuelle
            $reqNbEscales = $this->select()->setIntegrityCheck(false);
            $reqNbEscales->from('escale', array('COUNT(numeroEscale)'))
                                     ->where('idVol=v.idVol');
            $imbrique = $this->select()->setIntegrityCheck(false)
                            ->from(array('va'=>'valoir'),'va.idVol');
            
            $reqInfo_vol = $this->select()->distinct()->setIntegrityCheck(false);
            $reqInfo_vol->from(array('v' => 'vol'), array(
                                            'idVol', 
                                            'remarqueVol', 
                                            'dateHeureDepartEffectiveVol',
                                            'dateHeureDepartPrevueVol',
                                            'dateHeureArriveeEffectiveVol',
                                            'dateHeureArriveePrevueVol',
                                            'nbEscales' => '('.new Zend_Db_Expr($reqNbEscales).')'
                                     ))
                                     ->join(array('l' => 'ligne'), 'l.idLigne=v.idLigne', '')

                                     ->join(array('aeDep' => 'aeroport'), 'aeDep.trigrammeAeroport=l.trigrammeAeroportDepart', array('nomAeroportDepart' => 'nomAeroport'))
                                     ->join(array('dDep' => 'desservir'), 'dDep.trigrammeAeroport=aeDep.trigrammeAeroport', '')
                                     ->join(array('vDep' => 'ville'), 'vDep.idVille=dDep.idVille', array('villeDepart' => 'nomVille'))
                                     ->join(array('pDep' => 'pays'), 'pDep.idPays=vDep.idPays', array('paysDepart' => 'nomPays'))

                                     ->join(array('aeArr' => 'aeroport'), 'aeArr.trigrammeAeroport=l.trigrammeAeroportArrivee', array('nomAeroportArrivee' => 'nomAeroport'))
                                     ->join(array('dArr' => 'desservir'), 'dArr.trigrammeAeroport=aeArr.trigrammeAeroport', '')
                                     ->join(array('vArr' => 'ville'), 'vArr.idVille=dArr.idVille', array('villeArrivee' => 'nomVille'))
                                     ->join(array('pArr' => 'pays'), 'pArr.idPays=vArr.idPays', array('paysArrivee' => 'nomPays'))
                                     
                                     ->where("v.idVol IN ($imbrique)")
                                     ->where('v.idLigne='.$idLigne)
                                     ->where('v.dateHeureDepartPrevueVol > ?', $date->getIso());

                          //   echo $reqInfo_vol->assemble();exit;
            try {$resInfo_vol = $this->fetchAll($reqInfo_vol);}
            catch (Zend_Db_Exception $e) {die ($e->getMessage());}

            return $resInfo_vol->toArray();
        }
        
       
    }
?>
