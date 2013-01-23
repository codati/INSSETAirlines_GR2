$(document).ready(function() {
    $('.td_img img').click(function() {
       id = $(this).parents('tr').attr('id');
       nbPlaces = $('#resa_'+id).val();
       classe = $('.sel_classe_resa_'+id+' option:selected').val();

       leftDiv = $(this).offset().left + $(this).parent().width() + 10;
       topDiv = $(this).offset().top;
       //console.log("Left:"+leftDiv);
       //console.log("Top:"+topDiv);
       
       leftForm = $(this).offset().left - $('form#ChoixLigne select').width() - 50;
       topForm = $('form#ChoixLigne').offset().top - 5;
       
       //console.log("leftform = "+ leftForm);
       //console.log("topForm = "+ topForm);
       
       $("body").append('<div id="msg_resa" style="top:'+topForm+'px; left:'+leftForm+'px;"></div>')
       $("body").append('<div class="res_places" style="top:'+topDiv+'px;left:'+leftDiv+'px;"></div>');
       $.get('/agence/reserver', {idVol:id, nbPlaces:nbPlaces, classe:classe}, function(data) {
           $('.res_places').html('<img src="/img/asterisk_yellow.png" alt="asterisk"/>');
           $('#msg_resa').html(data).show();
           setTimeout("$('#msg_resa').fadeOut(1000);", 2000);
           setTimeout("$('.res_places').fadeOut(1000);", 2000);/**/
       });
    });
});

function confirmerResa(idResa) {
   haut = $('#menu table').offset().top + 5;
   gauche = $('#menu table').offset().left + $('#menu table').width() + 100;
   $.get('/agence/confirmer',{idReservation:idResa}, function(data) {
      location.reload(true);
      $("body").append('<div id="confirmResa" style="position:absolute;top:'+haut+'px; left:'+gauche+'px;">'+data+'</div>');
      setTimeout("$('#msg_resa').fadeOut(1000);", 2000);
   });
}
function premodifResa(idResa) {
    nbDepart = $('#transform_'+idResa).text();
    $('#transform_'+idResa).empty();
    $('#transform_'+idResa).html('<input id="test_'+idResa+'" class="input_nbPlaces" type="text" name="modif_nbplace" value="'+nbDepart+'"/><input class="bt_nbPlaces" onclick="modifier('+idResa+')" type="button" name="valid_nbPlace" value="OK" />');
    $('#transform_'+idResa+' input[type="text"]').focus();
}
function modifier(idResa) {
    nvNbPlaces = $('#test_'+idResa).val();
    $.get('/agence/modifier/', {idReservation:idResa, nvNbPlaces:nvNbPlaces}, function(data) {
        $('div.tableau').append('<br>'+data);
        location.reload(true);
    });
}
function supprimerResa(idResa) {
    $.get('/agence/supprimer/',{idReservation:idResa}, function(data) {
       $('div.tableau').append('<br>'+data);
       location.reload(true);
    });
}