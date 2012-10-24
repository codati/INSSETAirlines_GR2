

$(document).ready(function() {
	$('#bt_fermer').click(function() {
			$('#overlay').hide();
			$('#bglayer').hide();
	});
	$('#bt_co_layout').click(function() {
		$('#overlay').show();
		$('#bglayer').show();
	});
	$('#input_psw').keypress(function(e) {
		if(e.which==13)
			verifConnexion();
	});
});

function verifConnexion() {
	var username = $('#input_user').val();
	var mdp = $('#input_psw').val();
	if((username == "") || (mdp == ""))
        {
		$('#test').after('<br><span id="erreur_co">Vous n\'avez pas remplis tous les champs<span><br>');
        }
        else
	{	
            $("#form_login").submit();  
        }
}	
