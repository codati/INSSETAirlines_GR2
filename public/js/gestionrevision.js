$(document).ready(function() {
    $("#sel_tech").change(function() {      
        getInterventions();
    });
    getInterventions();
    //numTechnicien = $('#matriculeTech').val();
});

function getInterventions() {
        id = $("#sel_tech option:selected").val();
        nom = $("#sel_tech option:selected").text();

        $.get('/maintenance/getintertech', {tech:id, nomTech:nom}, function(data)
        {
            $('.tableau').html(data);
        });
}

function modifierIntervention(numIntervention, idTech) {
    if($('#int_'+numIntervention+' td.tacheEff').text() == "")
    {
        defautTache = $('#recupTaf_'+numIntervention+' td.taff').text();
    }
    else
    {
        defautTache = $('#int_'+numIntervention+' td.tacheEff').text();
    }
    $('#int_'+numIntervention+' td.tacheEff').html('<input id="focus_'+numIntervention+'" type="text" name="modifiertache" value="'+defautTache+'" />');
    $('#int_'+numIntervention+' td.remarques').html('<input type="text" name="modifierremarque" value="'+$('#int_'+numIntervention+' td.remarques').text()+'" />');
    
    $('#int_'+numIntervention+' td.changeImg').empty();
    $('#int_'+numIntervention+' td.changeImg').html('<img id="disk_'+numIntervention+'" src="/img/disk.png" alt="disk" title="Enregistrer" onclick="enregistrer('+numIntervention+','+idTech+')" />');     
    $('#focus_'+numIntervention).select();
}
function enregistrer(numIntervention, idTech) {
    nvTacheEff = $('#int_'+numIntervention+' td.tacheEff input').val();
    nvRemarques = $('#int_'+numIntervention+' td.remarques input').val();
    
    $.get('/maintenance/modifierintervention', {numInter:numIntervention, idTechnicien:idTech, modifTache:nvTacheEff, modifRem:nvRemarques}, function(data) {
        $('#div_msg').html(data);
    });
}