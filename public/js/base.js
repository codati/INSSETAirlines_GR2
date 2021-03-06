$(document).ready(function()
{
	// effacement du message deco apres 2000 
	setTimeout("$('#message_deco').fadeOut(1000);", 2000);     
	
	// ajoute un date picker a tous les elements dont la classe est datePick
	if( $(".datePick").attr("dateFormat") )
	{
		$(".datePick").datepicker(
		{	
			  showButtonPanel: true
			, dateFormat: $(".datePick").attr("dateFormat")
			, dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']     			
			, dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
			, monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Août','Sep','Oct','Nov','Déc']
			, monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']
			, prevText: 'Mois précédent'
			, nextText: 'Mois suivant'
			, closeText: 'OK'
			, currentText: "Aujourd'hui"
		});
	}
	else
	{
		$(".datePick").datepicker(
		{	
			  showButtonPanel: true
			, dateFormat: 'DD dd MM yy'
			, dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']     			
			, dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
			, monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Août','Sep','Oct','Nov','Déc']
			, monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']
			, prevText: 'Mois précédent'
			, nextText: 'Mois suivant'
			, closeText: 'OK'
			, currentText: "Aujourd'hui"
		});  
	}
	
	if($(".timePicker").attr("dateFormat"))
	{
		$(".timePicker").timepicker(
		{
			dateFormat: $(".timePicker").attr("dateFormat"),
			hourGrid: 4,
			minuteGrid: 10
			, dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']     			
			, dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
			, monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Août','Sep','Oct','Nov','Déc']
			, monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']
			, prevText: 'Mois précédent'
			, nextText: 'Mois suivant'
			, closeText: 'OK'
			, currentText: "Aujourd'hui"
		});
	}
	else
	{
		$(".timePicker").timepicker(
		{
			dateFormat: 'DD dd MM yy',
			hourGrid: 4,
			minuteGrid: 10
			, dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']     			
			, dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
			, monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Août','Sep','Oct','Nov','Déc']
			, monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']
			, prevText: 'Mois précédent'
			, nextText: 'Mois suivant'
			, closeText: 'OK'
			, currentText: "Aujourd'hui"
		});
	}
	
	if($(".datetimePicker").attr("dateFormat"))
	{
		console.log($(".datetimePicker").attr("dateFormat"));
		$(".datetimePicker").datetimepicker(
		{
			dateFormat: $(".datetimePicker").attr("dateFormat"),
			hourGrid: 4,
			minuteGrid: 10
			, dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']     			
			, dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
			, monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Août','Sep','Oct','Nov','Déc']
			, monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']
			, prevText: 'Mois précédent'
			, nextText: 'Mois suivant'
			, closeText: 'OK'
			, currentText: "Aujourd'hui"
		});
	}
	else
	{
		$(".datetimePicker").datetimepicker(
		{
			dateFormat: 'DD dd MM yy à',
			hourGrid: 4,
			minuteGrid: 10
			, dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']     			
			, dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
			, monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Août','Sep','Oct','Nov','Déc']
			, monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']
			, prevText: 'Mois précédent'
			, nextText: 'Mois suivant'
			, closeText: 'OK'
			, currentText: "Aujourd'hui"
		});
	}
});