
$(document).ready(function() {   
    $('#bt_fermer').click(function () {
        fermer()
    });
    $('#bt_close').click(function () {
        fermer()
    });
    $('.bglayer').click(function() {
       fermer(); 
    });
    $('#bt_co_layout').click(function() {
        $('#erreur_co').remove();
        $('.bglayer').show();
        $('#overlay').fadeIn(1000);
        $('#input_psw').focus(); // remettre #input_user a la fin
    });
    $('#bt_deco_layout').click(function() {
       window.location = "/index/logout"; 
    });
    $('#input_psw').keypress(function(e) {
        if(e.which==13)
            verifConnexion();
    });
    $('#overlay').css('margin','auto');
    $('#overlay').css('margin-top','10%');
    
});

function fermer() {    
    $('#overlay').hide();
    $('#formNouveauPilote').hide();
    $('.bglayer').fadeOut(1000);
}

function verifConnexion() {
    var username = $('#input_user').val();
    var mdp = $('#input_psw').val();
    var radio = $('#bt_rad-element input:checked').val()
    
    if((username == "") || (mdp == ""))
    {
        $('#erreur_co').remove();
        $('#err').after('<span id="erreur_co" class="erreur">Vous n\'avez pas remplis tous les champs<span>');
    }
    else
    {	
        $.post("/index/verifconnexion",{user:username, pass:mdp, rad:radio}, 
            function(data) 
            {
                if(data==1)
                {
                    $("#form_login").submit();
                }
                else
                {
                    $('#erreur_co').remove();
                    $('#err').after('<span id="erreur_co" class="erreur">Echec de connexion : Verifiez votre saisie<span>');
                }
            });
        
    }
}	
