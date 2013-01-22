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
        /**
         * Renvoie des informations sur tous les vols à venir et planifiés
         * @param int $idLigne : L'id de la ligne des vols
         * @return : L'array des infos des vols
         */
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
						
			//echo $reqInfo_vol->assemble();exit;
            try {$resInfo_vol = $this->fetchAll($reqInfo_vol);}
            catch (Zend_Db_Exception $e) {die ($e->getMessage());}

            return $resInfo_vol->toArray();
        }
        
       
                
        public function get_InfosVol($idVol)
            {
                    //On fait la requete pour r�cuperer les infos de la réservation
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
                                            ->join(array('dDep' => 'desservir'), 'dDep.trigrammeAeroport=aeDep.trigrammeAeroport', array('trigrammeAeDepart' => 'trigrammeAeroport'))
                                            ->join(array('vDep' => 'ville'), 'vDep.idVille=dDep.idVille', array('villeDepart' => 'nomVille'))
                                            ->join(array('pDep' => 'pays'), 'pDep.idPays=vDep.idPays', array('paysDepart' => 'nomPays'))

                                            ->join(array('aeArr' => 'aeroport'), 'aeArr.trigrammeAeroport=l.trigrammeAeroportArrivee', array('nomAeroportArrivee' => 'nomAeroport'))
                                            ->join(array('dArr' => 'desservir'), 'dArr.trigrammeAeroport=aeArr.trigrammeAeroport', array('trigrammeAeArrivee' => 'trigrammeAeroport'))
                                            ->join(array('vArr' => 'ville'), 'vArr.idVille=dArr.idVille', array('villeArrivee' => 'nomVille'))
                                            ->join(array('pArr' => 'pays'), 'pArr.idPays=vArr.idPays', array('paysArrivee' => 'nomPays'))

                                            ->where('v.idVol='.$idVol);

                    //echo $reqInfo_vol->assemble();exit;

                    try {$resInfo_vol = $this->fetchRow($reqInfo_vol);}
                    catch (Zend_Db_Exception $e) {die ($e->getMessage());}

                    //echo '<pre>';print_r($resInfo_vol->toArray());echo '</pre>';exit;
                    return $resInfo_vol->toArray();
            }

            /**
             * Récupère la liste des vol non planifié
             * @param int $nb_week : Le nombre de semaines à retourner
             * @return array/bool : L'array des résultats s'il y en a, false sinon
             */
            public function get_LstVolNonPlanifier($nb_week)
            {
                    $date = new Zend_Date();
                    $dateNowSql = DateFormat_SQL($date);
                    $date->addWeek($nb_week);
                    $dateSql = DateFormat_SQL($date);
                    //echo $dateSql;exit;

                    $req = $this->select()->setIntegrityCheck(false)
                                            ->from(array('v' => 'vol'), array(
                                                    'idVol',
                                                    'dateHeureDepartPrevueVol',
                                                    'dateHeureArriveePrevueVol'
                                            ))
                                            ->join(array('l' => 'ligne'), 'l.idLigne=v.idLigne', '')

                                            ->join(array('aeDep' => 'aeroport'), 'aeDep.trigrammeAeroport=l.trigrammeAeroportDepart', array('nomAeroportDepart' => 'nomAeroport'))
                                            ->join(array('aeArr' => 'aeroport'), 'aeArr.trigrammeAeroport=l.trigrammeAeroportArrivee', array('nomAeroportArrivee' => 'nomAeroport'))

                                            ->where('v.matriculeAvion IS NULL')
                                            ->where('v.dateHeureDepartPrevueVol >= "'.$dateNowSql.'"')
                                            ->where('v.dateHeureDepartPrevueVol <= "'.$dateSql.'"');
                    //echo $req->assemble();exit;

                    try {$res = $this->fetchAll($req);}
                    catch (Zend_Db_Exception $e) {die ($e->getMessage());}

                    //echo '<pre>';print_r($res->toArray());echo '</pre>';exit;
                    return $res->toArray();
            }

            /**
             * Récupère la liste des vols que fait un pilote entre deux dates
             * @param int $idPilote : L'id du pilote
             * @param string $dateDeb : La date de début de recherche
             * @param string $dateFin : La date de fin de recherche
             * @return array : La liste des vols du pilotes.
             */
            public function get_LstVolPilote_entreDate($idPilote, $dateDeb, $dateFin)
            {
                $req = $this->select()->setIntegrityCheck(false)
                                        ->from(array('v' => 'vol'), array('dateHeureDepartEffectiveVol', 'dateHeureDepartPrevueVol', 'dateHeureArriveeEffectiveVol', 'dateHeureArriveePrevueVol'))
                                        ->join(array('a' => 'assurer'), 'a.idVol=v.idVol')
                                        ->where('a.idPilote="'.$idPilote.'"')
                                        ->where('(v.dateHeureDepartEffectiveVol IS NOT NULL AND v.dateHeureDepartEffectiveVol >= "'.$dateDeb.'") OR (v.dateHeureDepartEffectiveVol IS NULL AND v.dateHeureDepartPrevueVol >= "'.$dateDeb.'")')
                                        ->where('(v.dateHeureArriveeEffectiveVol IS NOT NULL AND v.dateHeureArriveeEffectiveVol <= "'.$dateFin.'") OR (v.dateHeureArriveeEffectiveVol IS NULL AND v.dateHeureArriveePrevueVol <= "'.$dateFin.'")')
                                        ->order('v.dateHeureDepartPrevueVol ASC');
                //echo $req->assemble().'<br/>';

                $res = $this->fetchAll($req);
                return $res->toArray();
            }

            /**
             * Change l'immatriculation de l'avion d'un vol
             * @param int $idVol : L'id du vol
             * @param string $newImmatriculation : La nouvelle immatriculation
             */
            public function changeImmatriculation($idVol, $newImmatriculation)
            {
                $data = array('matriculeAvion' => $newImmatriculation);
                $where = $this->getAdapter()->quoteInto('idVol = ?', $idVol);
                $this->update($data, $where);
            }

            /**
             * Retourne la liste des vols d'une ligne
             * @param int $idLigne
             * @param bool $orderDESC
             */
            public function get_lstVol_EncoursEtAVenir_ligne($idLigne, $orderDESC)
            {
                $dateAct = new Zend_Date;
                $dateActSql = DateFormat_SQL($dateAct);
                $date24h = $dateAct->subHour(24);
                $date24Sql = DateFormat_SQL($dateAct);

                $req = $this->select()
                                        ->from($this->_name)
                                        ->where('idLigne=?', $idLigne)
                                        ->where('IF(dateHeureDepartEffectiveVol = "", dateHeureDepartPrevueVol >= "'.$date24Sql.'", dateHeureDepartPrevueVol >= "'.$dateActSql.'")');

                if($orderDESC == true) {$req->order('dateHeureDepartPrevueVol DESC');}
                else {$req->order('dateHeureDepartPrevueVol ASC');}

                return $this->fetchAll($req)->toArray();
            }

            public function editRemarque($idVol, $remarque)
            {
                $data = array('remarqueVol' => $remarque);
                $where = $this->getAdapter()->quoteInto('idVol=?', $idVol);
                $this->update($data, $where);
            }
            
            public function GetVolRetardataire()
            {
                $req = $this->select()->setIntegrityCheck(false)
                        ->from($this->_name)
                        ->where('dateHeureArriveeEffectiveVol > dateHeureArriveePrevueVol')
                        ;
                
                return $this->fetchAll($req)->toArray();
            }
    }
?>
