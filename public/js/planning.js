function disabled_and_loading(elem)
{
	$("#"+elem).attr("disabled", "disabled");
	$("#load-"+elem).html('<img src="/img/loading_squares.gif" alt="Loading" />');
}

function enabled_and_EndLoading(elem)
{
	$("#"+elem).removeAttr("disabled");
	$("#load-"+elem).html('');
}

$(document).ready(function()
{
	//Page planification du vol.
	$("#form_planifier #avion-element").append('<span id="load-avion"></span>');
	$("#form_planifier #pilote-element").append('<span id="load-pilote"></span><span id="detail_pilote">Détails des horaires du pilote</span><span id="load-detail_pilote"></span>');
	$("#form_planifier #copilote-element").append('<span id="load-copilote"></span><span id="detail_copilote">Détails des horaires du co-pilote</span><span id="load-detail_copilote"></span>');

	$("#form_planifier #modele_avion").change(function()
	{
		//On désactive les champs
		disabled_and_loading("avion");
		disabled_and_loading("pilote");
		disabled_and_loading("copilote");

		idmodeleavion = $("#modele_avion option:selected").val();
		datedep = $("#dateDepart").val();
		datearr = $("#dateArrivee").val();

		//console.log(idmodeleavion);
		//console.log(datedep);
		//console.log(datearr);
		
		//On récupère la nouvelle liste d'avions
		$.getJSON('/planning/lstavion', {idModele:idmodeleavion, dateDepart:datedep, dateArrivee:datearr, get:"ok"}, function(data)
		{
			//console.log(data);
			option_avion = '';
			for(i in data) {option_avion += '<option label="'+data[i]+'" value="'+i+'">'+data[i]+'</option>';}

			$("#avion").html(option_avion);
			enabled_and_EndLoading("avion");
		});

		//On récupère la nouvelle liste de pilote
		$.getJSON('/planning/lstpilote', {idModele:idmodeleavion, dateDepart:datedep, dateArrivee:datearr, get:"ok"}, function(data)
		{
			//console.log(data);
			option_pilote = '';
			for(i in data) {option_pilote += '<option label="'+data[i]+'" value="'+i+'">'+data[i]+'</option>';}

			$("#pilote").html(option_pilote);
			enabled_and_EndLoading("pilote");

			piloteid = $("#pilote option:selected").val();
			//console.log(piloteid);

			//On récupère la nouvelle liste de co-pilote
			$.getJSON('/planning/lstpilote', {idModele:idmodeleavion, dateDepart:datedep, dateArrivee:datearr, pilote:piloteid, get:"ok"}, function(data)
			{
				//console.log(data);
				option_copilote = '';
				for(i in data) {option_copilote += '<option label="'+data[i]+'" value="'+i+'">'+data[i]+'</option>';}

				$("#copilote").html(option_copilote);
				enabled_and_EndLoading("copilote");
			});
		});
	});

	$("#form_planifier #pilote").change(function()
	{
		disabled_and_loading("copilote");

		piloteid = $("#pilote option:selected").val();
		idmodeleavion = $("#modele_avion option:selected").val();
		datedep = $("#dateDepart").val();
		datearr = $("#dateArrivee").val();

		//On récupère la nouvelle liste de co-pilote
		$.getJSON('/planning/lstpilote', {idModele:idmodeleavion, dateDepart:datedep, dateArrivee:datearr, pilote:piloteid, get:"ok"}, function(data)
		{
			//console.log(data);
			option_copilote = '';
			for(i in data) {option_copilote += '<option label="'+data[i]+'" value="'+i+'">'+data[i]+'</option>';}

			$("#copilote").html(option_copilote);
			enabled_and_EndLoading("copilote");
		});
	});

	$("#form_planifier #copilote").change(function()
	{
		disabled_and_loading("pilote");

		copiloteid = $("#copilote option:selected").val();
		idmodeleavion = $("#modele_avion option:selected").val();
		datedep = $("#dateDepart").val();
		datearr = $("#dateArrivee").val();

		//On récupère la nouvelle liste de co-pilote
		$.getJSON('/planning/lstpilote', {idModele:idmodeleavion, dateDepart:datedep, dateArrivee:datearr, pilote:copiloteid, get:"ok"}, function(data)
		{
			//console.log(data);
			option_pilote = '';
			for(i in data) {option_pilote += '<option label="'+data[i]+'" value="'+i+'">'+data[i]+'</option>';}

			$("#pilote").html(option_pilote);
			enabled_and_EndLoading("pilote");
		});
	});
	
	$("#form_planifier #detail_pilote").click(function()
	{
		piloteid = $("#pilote option:selected").val();
		datedep = $("#dateDepart").val();
		datearr = $("#dateArrivee").val();
		
		$("#planning").html('');
		$("#load-detail_pilote").html('<img src="/img/loading_squares.gif" alt="Loading" />');
		
		$.get('/planning/calendrierpilote', {pilote:piloteid, dateDepart:datedep, dateArrivee:datearr}, function(data)
		{
			$("#load-detail_pilote").html('');
			$("#planning").html(data);
		});
	});
	
	$("#form_planifier #detail_copilote").click(function()
	{
		copiloteid = $("#copilote option:selected").val();
		datedep = $("#dateDepart").val();
		datearr = $("#dateArrivee").val();
		
		$("#planning").html('');
		$("#load-detail_copilote").html('<img src="/img/loading_squares.gif" alt="Loading" />');
		
		$.get('/planning/calendrierpilote', {pilote:copiloteid, dateDepart:datedep, dateArrivee:datearr}, function(data)
		{
			$("#load-detail_copilote").html('');
			$("#planning").html(data);
		});
	});
	
	//Page Récapitulation du vol.
	$("#form_RecapPlanifier").submit(function()
	{
		$("#formRecapPlanifier_load-valid").html('<img src="/img/loading_squares.gif" alt="Loading" />');
		
		idvol = $("#idVol").val();
		modeleavion = $("#modele").val();
		avion = $("#avion").val();
		pilote = $("#pilote").val();
		copilote = $("#copilote").val();
		
		$.post('/planning/validrecap', 
			{idVol:idvol, idModeleAvion:modeleavion, immaAvion:avion, idPilote:pilote, idCoPilote:copilote},
			function(data)
			{
				$("#formRecapPlanifier_load-valid").html('');
				if(data == '1')
				{
					$("#form_RecapPlanifier").css("display", "none");
					$("#error-formRecapPlanifier").css("display", "none");
					$("#valid-formRecapPlanifier").show();
				}
				else {$("#error-formRecapPlanifier").show();}
			}
		);
		
		return false;
	});
	
	$("#form_RecapPlanifier button").click(function() {$(location).attr('href', '/planning/planifier');});
});