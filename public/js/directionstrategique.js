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
		window.location.href = "/directionstrategique/modifVol/vol/"+id;
	});
	
	$(".copyVol").click(function()
	{
		id = $(this).parents('tr').attr('id');
		window.location.href = "/directionstrategique/copyVol/vol/"+id;
	});
	
	$("#AjouterVol").click(function()
	{
		id = $("#idLigne").val();
		window.location.href = "/directionstrategique/ajouterVol/ligne/"+id;
	});
});