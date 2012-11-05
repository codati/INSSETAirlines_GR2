

$(document).ready(function() {
    $('#bt_fermer').click(function() {
                    $('#overlay').hide();
                    $('#bglayer').hide();
    });
    $('#bt_co_layout').click(function() {
            $('#bglayer').show();
            $('#overlay').fadeIn(1000);
    });
    $('#input_psw').keypress(function(e) {
        if(e.which==13)
            verifConnexion();
    });
    $('#overlay').css('margin','auto');
    $('#overlay').css('margin-top','10%');
});

function verifConnexion() {
    var username = $('#input_user').val();
    var mdp = $('#input_psw').val();
    if((username == "") || (mdp == ""))
    {
        $('#erreur_co').remove();
        $('#err').after('<span id="erreur_co">Vous n\'avez pas remplis tous les champs<span>');
    }
    else
    {	
        $("#form_login").submit();  
    }
}	
