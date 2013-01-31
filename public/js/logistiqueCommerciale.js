function ajouterPromo(idVol)
{
          $.get('/logistiquecommerciale/tamere', {idVol:idVol}, function(data){
             //alert(data);
             $('#vide_'+idVol).html('<td colspan="4">'+data+'</td>');
             /*$('#formAjoutPromo').remove();
             $('#tableau').append(data);*/
          });
}
function test2(idVol) {
     
     if(confirm('Confirmer ?'))
     {
          rPC = $('#vide_'+idVol+' select#sel_pourcent_2 option:selected').val()
          rCA = $('#vide_'+idVol+' select#sel_pourcent_3 option:selected').val()
          rCE = $('#vide_'+idVol+' select#sel_pourcent_1 option:selected').val()
     
          $.get('/logistiquecommerciale/nvpromo',{idVol:idVol, rPC:rPC, rCA:rCA, rCE:rCE}, function(data){
               $('#degage').remove();
               $('h1').prepend(data);
          });
     }
     else{return false;}
     return false;
}