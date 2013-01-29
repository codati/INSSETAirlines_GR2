<?php

class AgenceController extends Zend_Controller_Action
{
    private $img = '';
        
    public function init()
    {
            $this->headStyleScript = array(
                'css' => 'service_commercial',
                'js' => array('agence','service_exploitation')
            );
            $this->img = '<img class="img_fermer" width="16px" height="16px" src="'.$this->view->baseUrl('/img/close.png').'" alt="close" title="Fermer" onclick="fermerP()" />';
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
    //public function reserverAction()
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
                echo '<p class="erreur rel">Ce modele ne contient que '.$nbPlacesTotales.' places pour cette classe.'.$this->img.'</p>';
                exit;
            }
            else
            {
                // get la reservation avec le vol et la classe
                $tableResa = new Table_Reservation;
                $idResaVol = $tableResa->getIdResaVol($idVol, $classe);
                //Zend_Debug::dump($idResaVol);exit;
                                
                $tableClasse = new Table_Classe;
                $nomClasse = $tableClasse->getLibelle($classe);
                
                if($idResaVol) // on a trouvé une reservation
                {
                    // get le nombre de places deja reservées (en attente ou definitives)
                    $tableDemander = new Table_Demander;
                    $nbPlacesReservees = $tableDemander->getNbPlacesReservee($idResaVol);
                    //echo $nbPlacesReservees;exit;

                    if($passe)
                    {
                        $nbPlacesDispo = $nbPlacesTotales - $nbPlacesReservees;
                    }
                    else
                    {
                        $nbPlacesDispo = $nbPlacesTotales;
                    }
                    if($nbPlaces > $nbPlacesDispo) // plus ou pas assez de places dispos
                    {
                        echo '<p class="erreur rel">Impossible. Il reste '.$nbPlacesDispo.' places dans cette classe.'.$this->img;
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
                            $this->creerDemande($idAgence, $idResaVol, $nbPlaces);
                        }
                        // ne pas virer le "1", tres important pour le JS
                        echo '1<p class="reussi rel">Vous avez réservé '.$nbPlaces.' place(s) pour le vol n°'.$idVol.' en '.strtolower($nomClasse).'.<br>
                            Réservation N° : '.$idResaVol.$this->img;
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
                    $this->creerDemande($idAgence, $idResa, $nbPlaces);
                    // ne pas virer le "1", tres important pour le JS
                    echo '1<p class="reussi rel">Vous avez réservé '.$nbPlaces.' place(s) pour le vol n°'.$idVol.' en '.strtolower($nomClasse).'.<br>
                            Réservation N° : '.$idResa.$this->img.'
                        </p>';
                }
            }
            exit;
        }
        else
        {
            echo '<p class="erreur rel">Seuls les nombres positifs sont acceptés'.$this->img;
            exit;
        }
    }
    /**
     * créé la demande de reservation de place pour un vol
     * @param int $idAgence : id de l'agence
     * @param int $idResaVol : id de la reservation
     * @param int $nbPlaces : le nombre de places a ajouter
     */
    private function creerDemande($idAgence, $idResaVol, $nbPlaces)
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
    public function gererresasAction($reussi =null, $msg=null)
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
        
        $this->view->msg = $msg;
        $this->view->reussi = $reussi;
        $this->view->img = $this->img;
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
            // balise p pas fermée : normal
            $msg = '<p class="reussi rel">Vos places ont étés confirmées à tant !';            
        }
        else
        {
            echo '<p class="erreur rel">Erreur lors de la requête. Veuillez réessayer';
        }
        // va rechercher l'action gererresas pour reparcourir les résas et donc recuperer les modifs effectuées
        $this->gererresasAction(true,$msg);
        // affiche le contenu de la vue
        echo $this->render('gererresas');
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

        $tablereservation = new Table_Reservation;
        $volClasseRepas = $tablereservation->getVolClasseTypeRepas($idResa);
         
        
        $this->reserverAction($volClasseRepas['idVol'], $nbPlaces, $volClasseRepas['idClasse'], $volClasseRepas['idTypeRepas'], false);      
        exit;
    }
    public function remettreattenteAction()
    {
        $idResa = $this->_getParam('idReservation');
        $espaceAgence = new Zend_Session_Namespace('agenceCourante');
        $idAgence = $espaceAgence->idAgence;
        // remettre etat a en attente
        $tableDemander = new Table_Demander;
        $tableDemander->setEnAttente($idResa, $idAgence); 
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
        
        echo '<p class="reussi rel">Réservation supprimée'.$this->img.'</p>';
        exit;
    }
}