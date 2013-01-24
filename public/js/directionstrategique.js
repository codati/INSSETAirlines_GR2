$(document).ready(function()
{
	$(".viewLigne").click(function()
	{
		id = $(this).parent().attr('id');
		console.log(id);
		window.location.href = "/directionstrategique/voirligne/ligne/"+id;
	});
	
	$("#AjouterLigne").click(function() {window.location.href = "/directionstrategique/ajouterligne";});
});