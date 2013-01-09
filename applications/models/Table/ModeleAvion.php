<?php
    class Table_ModeleAvion extends Zend_Db_Table_Abstract
    {
        protected $_name = 'modeleavion';
        protected $_primary = 'idModeleAvion';

        /**
         * Récupère la liste de tous les noms de modèle d'avions pour aéroport
         * @param string $TriAeroport : Le trigramme de l'aéroport
         * @return array : Le résultat de la requête
         */
        public function get_NomModeles_PourAeroport($TriAeroport)
        {
            $req = $this->select()
                    ->from(array('ma' => $this->_name), array('idModeleAvion', 'libelleModeleAvion'))
                    ->where('longueurDecollage <= (SELECT ae1.longueurPisteMax FROM aeroport ae1 WHERE ae1.trigrammeAeroport="'.$TriAeroport.'")')
                    ->where('longueurAtterrissage <= (SELECT ae1.longueurPisteMax FROM aeroport ae1 WHERE ae1.trigrammeAeroport="'.$TriAeroport.'")');

            $res = $this->fetchAll($req);
            return $res->toArray();
        }

        /**
         * Récupère le nom du modèle d'un avion
         * @param int $idModele : L'id du modèle
         * @return string : Le nom du modèle
         */
        public function get_libelle($idModele)
        {
            $req = $this->select()->from($this->_name, 'libelleModeleAvion')->where('idModeleAvion=?', $idModele);
            $res = $this->fetchRow($req)->toArray();
            return $res['libelleModeleAvion'];
        }

        public function GetListLibelle()
        {
            $laListe = $this->select()->from(array('modeleavion'), array('idModeleAvion','libelleModeleAvion'));

            return $this->fetchAll($laListe)->toArray();
        }
    }