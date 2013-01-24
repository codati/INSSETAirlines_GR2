<?php

class AgenceController extends Zend_Controller_Action
{
    public function init()
    {
            $this->headStyleScript = array(
                'css' => 'service_commercial',
                'js' => array('agence','service_exploitation')
            );
    }	    
    /**
     * appelle l'action de serviceexploitation controller en passant un parametre qui permet la modif et l'ajout de code
     */
    public function reservationsAction()
    {
       $this->_helper->actionStack('gestionvols','serviceexploitation','default',array('head' => $this->headStyleScript,'reservationPlace'=>true));
    }
    /**
     * 
     * @param int $idVol : id du vol
     * @param int $nbPlaces : nombre de places a jouter, modifier
     * @param int $classe : classe pour laquelle modifier
     * @param int $typeRepas : type de repas
     * @param bool $passe : si false, on vient de modifierAction()
     * @return message de confirmation ou d'erreur
     */
    public function reserverAction($idVol =0, $nbPlaces = 0, $classe =0,$typeRepas =7, $passe =true)
    { 
        if($passe)
        {
            $idVol = $this->_getParam('idVol');
            $nbPlaces = $this->_getParam('nbPlaces');
            $classe = $this->_getParam('classe');
            $typeRepas = $this->_getParam('typeRepas');
        }
        $espaceAgence = new Zend_Session_Namespace('agenceCourante');
        $idAgence = $espaceAgence->idAgence;
        
        //$idAgence = 1;  // pour les tests
        // verifie que l'on saisi bien un nombre, et qu'il est superieur ou egal à 0
        if(is_numeric($nbPlaces) && $nbPlaces > 0)
        {                
            // force en int pour reserver un nombre entier de places
            $nbPlaces = (int)$nbPlaces;

            // get le matricule de l'avion pour le vol
            $tableVol = new Table_Vol;            
            $matAvion = $tableVol->getMatriculeAvionVol($idVol);

            // get le modele de l'avion
            $tableAvion = new Table_Avion;
            $modeleAvion = $tableAvion->getModele($matAvion);            

            // get le nombre de place dans l'avion en fonction du modele et de la classe
            $tableContenir = new Table_Contenir;                
            $nbPlacesTotales = $tableContenir->getNbPlacesTotales($modeleAvion, $classe);
            //Zend_Debug::dump($nbPlacesTotales);

            if($nbPlaces > $nbPlacesTotales) // on demande plus de places que la classe ne contient
            {
                echo '<p class="erreur">Ce modele ne contient que '.$nbPlacesTotales.' places pour cette classe.</p>';
                exit;
            }
            else
            {
                // get la reservation avec le vol et la classe
                $tableResa = new Table_Reservation;
                $idResaVol = $tableResa->getIdResaVol($idVol, $classe, $typeRepas);
                
                if($classe == 2) // premiere classe, avec id 2...
                {
                    $libClasse = 'premiere classe';
                }
                else // autre classe, on get son nom pour laffichage
                {
                    $tableClasse = new Table_Classe;
                    $nomClasse = $tableClasse->getLibelle($classe);
                    $libClasse = 'classe '.$nomClasse;
                }
                if($idResaVol) // on a trouvé une reservation
                {
                    // get le nombre de places deja reservées (en attente ou definitives)
                    $tableDemander = new Table_Demander;
                    $nbPlacesReservees = $tableDemander->getNbPlacesReservee($idResaVol);
                    //echo $nbPlacesReservees;exit;

                    $nbPlacesDispo = 9999;
                    if($passe)
                    {
                        $nbPlacesDispo = $nbPlacesTotales - $nbPlacesReservees;
                    }
                    if($nbPlaces > $nbPlacesDispo) // plus ou pas assez de places dispos
                    {
                        echo '<p class="erreur">Impossible. Il reste '.$nbPlacesDispo.' places dans cette classe.</p>';
                        exit;
                    }
                    else    // assez de places dispos
                    {
                        
                        //chercher demande de l'agence
                        if($tableDemander->existeDemande($idAgence, $idResaVol))
                        {
                            $tableDemander->modifier($idAgence, $idResaVol, $nbPlaces, $passe);
                        }
                        else
                        {
                            // la demande n'existe pas, on la créée
                            $this->creerDemande($idAgence, $idResaVol, $nbPlaces, $typeRepas);
                        }
                        echo '<p class="reussi">Vous avez réservé '.$nbPlaces.' place(s) pour le vol n°'.$idVol.' en '.$libClasse.'.<br>
                            Réservation N° : '.$idResaVol.'
                        </p>';
                        exit;
                    }
                }
                else // aucune reservation, il faut en creer une
                {
                    $donnees = array(
                       'idClasse' => $classe,
                       'idVol' => $idVol,
                       'idTypeRepas' => $typeRepas
                    );
                    $tableResa->nouvelleResa($donnees); // créé une reservation

                    // recup l'id de la derniere reservation ajoutée
                    $idResa = $tableResa->getAdapter()->lastInsertId();

                    // créé la demande de place sur cette reservation
                    $this->creerDemande($idAgence, $idResa, $nbPlaces, $typeRepas);
                    echo '<p class="reussi">Vous avez réservé '.$nbPlaces.' place(s) pour le vol n°'.$idVol.' en '.$libClasse.'.<br>
                            Réservation N° : '.$idResa.'
                        </p>';
                }
            }
            exit;
        }
        else
        {
            echo '<p class="erreur">Seuls les nombres positifs sont acceptés</p>';
            exit;
        }
    }
    /**
     * créé la demande de reservation de place pour un vol
     * @param int $idAgence : id de l'agence
     * @param int $idResaVol : id de la reservation
     * @param int $nbPlaces : le nombre de places a ajouter
     */
    private function creerDemande($idAgence, $idResaVol, $nbPlaces, $typeRepas)
    {
        $donnees =array(
            'idAgence' => $idAgence, // changer lagence 
            'idReservation' => $idResaVol,
            'nbPlacesReservees' => $nbPlaces,
            'dateDemande' => DateFormat_SQL(new Zend_Date(Zend_Date::now()))
        );
        $tableDemander = new Table_Demander;
        $tableDemander->reserver($donnees);
    }
    /**
     * recupere, expire ou non une reservation et les affiches
     * @param int $idAgence : id de l'agence
     */
    public function gererresasAction()
    {        
        $this->_helper->actionStack('header','index','default',array('head' => $this->headStyleScript));
        $espaceAgence = new Zend_Session_Namespace('agenceCourante');
        $idAgence = $espaceAgence->idAgence;        
        
        // on va chercher toutes les reservations de l'agence
        $tableDemander = new Table_Demander;
        $mesResas = $tableDemander->getResasAgence($idAgence);

        $i=0;
        foreach ($mesResas as $uneResa)
        {
            // obligé de refaire un now() DANS le foreach
            // car modifié au moment du sub(), on le reinitialise donc
            $maintenant = Zend_Date::now(); 
            $dateTest = new Zend_Date($uneResa['dateDemande']);
            $ecart = $maintenant->sub($dateTest)->toValue(); // temps ecoulé en secondes
            $ecartHeure = floor(($ecart/60)/60); // division par 3600 pour obtenir le temps en heure
            // si l'agence revient apres le delai de validation
            if($ecartHeure >= 2)
            {
                // on modifie l'etat de la demande, pour la passer en "Expirée"
                $tableDemander->expirer($uneResa['idReservation'], $idAgence);
                $i++;   // on compte le nombre de modifs
            }
        }
        if($i > 0)  // au moins une réservation a expirée
        {
            //on retourne chercher les reservations avec les modifs prisent en compte
            $mesResas = $tableDemander->getResasAgence($idAgence);
        }
        
        $this->view->resasAgence = $mesResas;
    }
    /**
     * passe une demande a l'etat "Validée"
     * @param int $idReservation : id de reservation
     * @param int $idAgence : id de l'agence
     * @return un message de reussite ou d'erreur
     */
    public function confirmerAction()
    {
        $idResa = $this->_getParam('idReservation');
        $espaceAgence = new Zend_Session_Namespace('agenceCourante');
        $idAgence = $espaceAgence->idAgence;
        
        $tableDemander = new Table_Demander;
        $res = $tableDemander->confirmer($idResa, $idAgence);
        if($res == 1)
        {
            echo '<p class="reussi">Vos places ont étés confirmées à tant !</p>';
        }
        else
        {
            echo '<p class="erreur">Erreur lors de la requête. Veuillez réessayer</p>';
        }
        exit;
    }
    /**
     * meme but que reserverAction(), sauf qu'ici on doit d'abord recuperer le vol et la classe
     * @param int $idReservation : id de la reservation
     * @param int $nvNbPlaces : le nombre de places (modifié)
     * @param int $idAgence : id de l'agence
     */
    public function modifierAction()
    {
        $idResa = $this->_getParam('idReservation');
        $nbPlaces = $this->_getParam('nvNbPlaces');
        $espaceAgence = new Zend_Session_Namespace('agenceCourante');
        $idAgence = $espaceAgence->idAgence;
        
        $tablereservation = new Table_Reservation;
        $vol_classe = $tablereservation->getVolEtClasse($idResa);
        
        // remettre etat a en attente
        $tableDemander = new Table_Demander;
        $tableDemander->setEnAttente($idResa, $idAgence);
        
        // appel a l'action reserver, pour la verif de saisie, nombre de places ; enregistrement, modifs ; erreurs
        $this->reserverAction($vol_classe['idVol'], $nbPlaces, $vol_classe['idClasse'], false);
        
        exit;
    }
    /**
     * Supprime une reservation, quelque soit son etat
     * @param int $idReservation : id de la reservation
     * @return un message de reussite
     */
    public function supprimerAction()
    {
        $idResa = $this->_getParam('idReservation');
        
        $espaceAgence = new Zend_Session_Namespace('agenceCourante');
        $idAgence = $espaceAgence->idAgence;
        $tableDemander = new Table_Demander;
        $tableDemander->supprimerDemande($idResa, $idAgence);
        
        $tableResa = new Table_Reservation;
        $tableResa->supprimerReservation($idResa);
        echo '<p class="reussi">Réservation supprimée</p>';
        exit;
    }
}