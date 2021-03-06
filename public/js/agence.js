$(document).ready(function() {
   prepare();
});
function prepare() { 
    $('.td_img img').click(function() { 
       id = $(this).parents('tr').attr('id');      
       idNum = id.substr('7');
       nbPlaces = $('#resa_'+idNum).val();
       classe = $('.sel_classe_resa_'+idNum+' option:selected').val();
       typeRepas = $('.sel_repas_resa_'+idNum+' option:selected').val();

       leftDiv = $(this).offset().left + $(this).parent().width() - 10;
       topDiv = $(this).offset().top;
       //console.log("Left:"+leftDiv);
       //console.log("Top:"+topDiv);
       
       leftForm = $(this).offset().left - $('form#ChoixLigne select').width() - 20;
       topForm = $('form#ChoixLigne').offset().top - 5;
       
       //console.log("leftform = "+ leftForm);
       //console.log("topForm = "+ topForm);
       
       $("body").append('<div id="msg_resa" style="top:'+topForm+'px; left:'+leftForm+'px;"></div>')
       $("body").append('<div class="res_places" style="top:'+topDiv+'px;left:'+leftDiv+'px;"></div>');
       $.get('/agence/reserver', {idVol:idNum, nbPlaces:nbPlaces, classe:classe, typeRepas:typeRepas}, function(data) {
           if(data[0]== 1) { // data est un message de reussite  
             data = data.substr(1);
           }
           $('.res_places').html('<img src="/img/asterisk_yellow.png" alt="asterisk"/>');
           $('#msg_resa').html(data).show();
           $('#resa_'+idNum).val('0');
           /*setTimeout("$('#msg_resa').fadeOut(1000);", 2000); */
           setTimeout("$('.res_places').fadeOut(1000);", 2000);
       });
    });
     $('.show').click(function() {
       id = $(this).parents('tr').attr('id');
       display = $("#lst_vol tr#cached_"+id).css('display');

       if(display == 'none') {$("#lst_vol tr#cached_"+id).css('display', 'table-row');}
       else {$("#lst_vol tr#cached_"+id).css('display', 'none');}
    });
}
function confirmerResa(idResa) {
   haut = $('#menu table').offset().top + 5;
   gauche = $('#menu table').offset().left + $('#menu table').width() + 100;
   $.get('/agence/confirmer',{idReservation:idResa}, function(data) {
       //$('#prepend').prepend(data);
      $('section').empty();     // on vide le contenu de la page
      $('section').html(data);  // ici data == contenu de gererresas.phtml
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
        if(data[0]== 1) { // data est un message de reussite      
            attente(idResa);
            data = data.substr(1);
        }
        fermerP();
        $('#prepend').prepend(data);
        $('#transform_'+idResa).empty();
        $('#transform_'+idResa).html(nvNbPlaces);        
    });
}
function attente(idResa) {
    $.get('/agence/remettreattente', {idReservation:idResa});
}
function supprimerResa(idResa) {
    $.get('/agence/supprimer/',{idReservation:idResa}, function(data) {
       fermerP();
       $('#prepend').prepend(data);
       tr = $('#transform_'+idResa).parents('tr');
       $(tr).remove();
    });
}
function fermerP() {
    $('.rel').remove();
}