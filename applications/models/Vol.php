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
                
        public function get_InfosVol($idVol)
                {
                        //On fait la requ�te pour r�cuperer les infos de la r�servation
                        $reqNbEscales = $this->select()->setIntegrityCheck(false);
                        $reqNbEscales->from('escale', 'COUNT(numeroEscale)')
                                                 ->where('idVol=v.idVol');
                        //echo $reqNbEscales->assemble();
                        
                        $reqInfo_vol = $this->select()->setIntegrityCheck(false);
                        $reqInfo_vol->from(array('v' => 'vol'), array(
                                                        'idVol', 
                                                        'remarqueVol', 
                                                        'dateHeureDepartEffectiveVol',
                                                        'dateHeureDepartPrevueVol',
                                                        'dateHeureArriveeEffectiveVol',
                                                        'dateHeureArriveePrevueVol',
                                                        'nbEscale' => '('.new Zend_Db_Expr($reqNbEscales).')'
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
                                                 
                                                 ->where('v.idLigne='.$idVol);
                        
                        //echo $reqInfo_vol->assemble();exit;
                        
                        try {$resInfo_vol = $this->fetchAll($reqInfo_vol);}
                        catch (Zend_Db_Exception $e) {die ($e->getMessage());}
                        
                        echo '<pre>';print_r($resInfo_vol->toArray());echo '</pre>';exit;
                        return $resInfo_vol->toArray();
                }
    }
?>
