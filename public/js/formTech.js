/**
 * Vérifie le format de la date
 */
function verifFormatDate()
{
    val = $("#dateNaissTech").val(); //On récupère la valeur du champ
    $("#bt_sub-label").html(""); //On vide le contenu de l'élément #bt_sub-label afin d'enlever l'erreur s'il y en a une
    var contenuOk= "<p class='reussi'>Format correct</p>" ;
    if(val.length == 10) //Si la contenu de la valeur du champ fait 10 caractères
    {
        /*
        Appel ajax :
            On appelle une page (comme si on était dans un navigateur)
            On lui met passe des paramètres get/post
            On récupère le contenu de la page appelé dans la variable data
                Et on exécute le code qu'il y a dans la fonction
            /!\ L'appel est asynchrone, la fonction dans laquel on est continue pendant que l'ajax s'exécute
        */

        link = '/drh/api/'; //Le lien de la page à appeler
        /*
         * 2nd paramètre de la fonction : (les paramètre envoyé en get)
         * $_GET['date'] = var (la variable js)
         */
        //data contient le contenu de la page appeler (par exemple: html/body etc)
        $.get(link, {date:val}, function(data)
        {
            console.log(data);
            if(data != '1') //Si la page à pour contenu autre chose que "1"
            {
                //On affiche une erreur
                //On ajoute le champ p avec son texte d'erreur DANS l'élément #bt_sub_label
                $("#bt_sub-label").html("<p class='erreur'>Format incorrect</p>");
            }
            else
            {
                $("#bt_sub-label").html(contenuOk);
            }
        });
    }
    else //Sinon, si la date ne fait pas 10 caractères
    {
        //On ajoute le champ p avec son texte d'erreur DANS l'élément #bt_sub_label
        $("#bt_sub-label").html("<p class='erreur'>Votre saisie de date est incorrect</p>");
    }
}

//Quand la page a fini de charger
$(document).ready(function()
{
    //Quand le champ #dateNaissTech perd le focus, on vérifie la date
   $("#dateNaissTech").blur(function() {verifFormatDate();});
   
   //Quand le formulaire #formAjout est validé
   $("#formAjout").submit(function()
   {
       //On part du principe que la date a déjà été vérifiée
       test = $("#bt_sub-label").html(); //On récupère le contenu de l'endroit où on met l'erreur
       if(test == contenuOk) {return true;} //Si c'est vide (pas d'erreur), on renvoi true ce qui valide le formulaire
       else {return false;} //Si l'élément a du contenu, il y a une erreur, donc on renvoi false ce qui ne valide pas le formulaire
   })
});