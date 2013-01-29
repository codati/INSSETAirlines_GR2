function sizeBulle()
{
	widthTableau = $(".tableau").width();
	widthAutour = $(".tableau").parent().width();
	
	widthDispo = widthAutour - widthTableau - 15;
	$(".bulle_escale").width(widthDispo);
}

$(document).ready(function()
{
	$(".viewLigne").click(function()
	{
		id = $(this).parent().attr('id');
		window.location.href = "/directionstrategique/voirvols/ligne/"+id;
	});
	
	$(".modifLigne").click(function()
	{
		id = $(this).parent().attr('id');
		window.location.href = "/directionstrategique/modifierligne/ligne/"+id;
	});
	
	$("#AjouterLigne").click(function() {window.location.href = "/directionstrategique/ajouterligne";});
	
	var idDerBulle = null;
	$(".lstvol").mouseover(function()
	{
		sizeBulle();
		id = $(this).attr('id');
		
		if(idDerBulle != null)
		{
			$("#"+idDerBulle).fadeOut("fast");
			idDerBulle = null;
		}
		
		if($("#escale_"+id).length > 0)
		{
			display = $("#escale_"+id).css('display');
			if(display == "none")
			{
				offset = $(this).offset();
				left = $(".tableau").width() + 10;
				
				$("#escale_"+id).css("left", left);
				$("#escale_"+id).css("top", offset.top);
				
				$("#escale_"+id).fadeIn("slow");
				idDerBulle = "escale_"+id;
			}
		}
	});
	
	$(".modifVol").click(function()
	{
		id = $(this).parents('tr').attr('id');
		window.location.href = "/directionstrategique/modifvol/vol/"+id;
	});
	
	$(".copyVol").click(function()
	{
		idL = $("#idLigne").val();
		idV = $(this).parents('tr').attr('id');
		window.location.href = "/directionstrategique/copyvol/vol/"+idV+"/ligne/"+idL;
	});
	
	$("#AjouterVol").click(function()
	{
		id = $("#idLigne").val();
		window.location.href = "/directionstrategique/ajoutervol/ligne/"+id;
	});
	
	$("#list-escales").sortable( // initialisation de Sortable sur #list-photos
	{
		placeholder: 'highlight', // classe à ajouter à l'élément fantome
		update: function() // callback quand l'ordre de la liste est changé
		{
			var order = $('#list-escales').sortable('toArray'); // récupération des données à envoyer
			//$.post('ajax.php',order); // appel ajax au fichier ajax.php avec l'ordre des photos
			//console.log(order);
			$("#escaleOrder").val(order);
		}
	});
	
	$("#list-escales").disableSelection(); // on désactive la possibilité au navigateur de faire des sélections
	
	$(".AddEscale div.addEscaleClick").click(function()
	{
		nbEscale = parseInt($("#nbEscale").val());
		aeroDepVal = $("#aeroDep option:selected").val();
		aeroArrVal = $("#aeroArr option:selected").val();
		
		aeroDep = $("#aeroDep option:selected").text();
		aeroArr = $("#aeroArr option:selected").text();
		
		dateEscDep = $("#dateEscDep").val();
		dateEscArr = $("#dateEscArr").val();
		
		html = '<li id="escale_'+nbEscale+'" style="display: none;">';
			html += '<div class="left">';
				html += '<strong>Départ</strong>';
				html += '<div class="EscaleAeroWidth">Aéroport :&nbsp;<span id="escale_'+nbEscale+'_aeroDepText">'+aeroDep+'</span></div>';
				html += '&nbsp;&nbsp;&nbsp;Date :&nbsp;<span id="escale_'+nbEscale+'_dateDepText">'+dateEscDep+'</span>';
				html += '<br/>';
				html += '<strong>Arrivée</strong>';
				html += '<div class="EscaleAeroWidth">Aéroport :&nbsp;<span id="escale_'+nbEscale+'_aeroArrText">'+aeroArr+'</span></div>';
				html += '&nbsp;&nbsp;&nbsp;Date :&nbsp;<span id="escale_'+nbEscale+'_dateArrText">'+dateEscArr+'</span>';
				
				html += '<input type="hidden" name="escale_'+nbEscale+'_DepAero" id="escale_'+nbEscale+'_DepAero" value="'+aeroDepVal+'" />';
				html += '<input type="hidden" name="escale_'+nbEscale+'_DepDate" id="escale_'+nbEscale+'_DepDate" value="'+dateEscDep+'" />';
				html += '<input type="hidden" name="escale_'+nbEscale+'_ArrAero" id="escale_'+nbEscale+'_ArrAero" value="'+aeroArrVal+'" />';
				html += '<input type="hidden" name="escale_'+nbEscale+'_ArrDate" id="escale_'+nbEscale+'_ArrDate" value="'+dateEscArr+'" />';
			html += '</div>';
			html += '<div class="right escalesIcons">';
				html += '<img src="/img/pencil.png" alt="modifier" title="Modifier" id="editEscale" />';
				html += '<img src="/img/cross.png" alt="supprimer" title="Supprimer" id="supprEscale" />';
			html += '</div>';
			html += '<div class="end_float"></div>';
		html += '</li>';
		
		$("#list-escales").append(html);
		$("#escale_"+nbEscale).fadeIn("slow");
		
		Order = $("#escaleOrder").val();
		if(Order != "") {Order += ".";}
		Order += "escale_"+nbEscale;
		$("#escaleOrder").val(Order);
		
		nbEscale = nbEscale+1;
		$("#nbEscale").val(nbEscale);
		
		$("#aeroDep").val(aeroArrVal);
		$("#dateEscDep").val(dateEscArr);
		$("#dateEscArr").val("");
	});
	
	$("#dateDep").blur(function()
	{
		console.log('DateDep Blur.');
		nbEscale = $("#nbEscale").val();
		valDepDate = $(this).val();
		
		if(valDepDate != "" && nbEscale == "0") {$("#dateEscDep").val(valDepDate);}
	});
	
	$("#aeroArr").change(function()
	{
		trigArr = $("#trigArrivee").val();
		sel = $("#aeroArr option:selected").val();
		
		if(sel == trigArr) {$("#dateEscArr").val($("#dateArr").val());}
	});
	
	$("#supprEscale").live('click', function()
	{
		id = $(this).parents('li').attr('id');
		order = $("#escaleOrder").val();
		
		newOrder = '';
		reg = new RegExp("[.]+", "g");
		ex = order.split(reg);
		
		for(i=0; i<ex.length; i++)
		{
			if(ex[i] != id)
			{
				if(newOrder != '') {newOrder += '.';}
				newOrder += ex[i];
			}
		}
		
		$("#escaleOrder").val(newOrder);
		$("#"+id).fadeOut("slow");
	});
	
	$("#editEscale").live('click', function()
	{
		id = $(this).parents('li').attr('id');
		$("#idEscaleEdit").val(id);
		
		$("#aeroDep").val($("#"+id+"_DepAero").val());
		$("#aeroArr").val($("#"+id+"_ArrAero").val());
		
		$("#dateEscDep").val($("#"+id+"_DepDate").val());
		$("#dateEscArr").val($("#"+id+"_ArrDate").val());
		
		$(".addEscaleClick").hide();
		$(".editEscaleClick").show();
	});
	
	$(".AddEscale div.editEscaleClick").click(function()
	{
		id = $("#idEscaleEdit").val();
		
		aeroDepVal = $("#aeroDep option:selected").val();
		aeroArrVal = $("#aeroArr option:selected").val();
		
		aeroDep = $("#aeroDep option:selected").text();
		aeroArr = $("#aeroArr option:selected").text();
		
		dateEscDep = $("#dateEscDep").val();
		dateEscArr = $("#dateEscArr").val();
		
		$("#aeroDep").val("");
		$("#aeroArr").val("");
		$("#dateEscDep").val("");
		$("#dateEscArr").val("");
		
		$("#"+id+"_aeroDepText").text(aeroDep);
		$("#"+id+"_DepAero").val(aeroDepVal);
		
		$("#"+id+"_dateDepText").text(dateEscDep);
		$("#"+id+"_DepDate").val(dateEscDep);
		
		$("#"+id+"_aeroArrText").text(aeroArr);
		$("#"+id+"_ArrAero").val(aeroArrVal);
		
		$("#"+id+"_dateArrText").text(dateEscArr);
		$("#"+id+"_ArrDate").val(dateEscArr);
		
		$(".editEscaleClick").hide();
		$(".addEscaleClick").show();
	});
});