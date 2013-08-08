$(document).ready(function(){


	$("#faq li > h3").each(function(){
		$(this).siblings().wrapAll('<div>');
	});
	
	$("#faq li > *:not(h3)").hide();	
	
	
	$("#faq h3").css("cursor","pointer").click(function (){
		var domanda = $(this);
		//Immagine di sfondo: meno
		var minus = {
			'background-image':'url(img/minus.gif)'
		};
		//Immagine di sfondo: piÃ¹
		var plus = {
			'background-image':'url(img/plus.gif)'
		};
		//selezioniamo tutte le risposte
		var risposta = domanda.siblings();
		//se la risposta Ã¨ nascosta mettiamo come sfondo
		//il piÃ¹, altrimenti il meno
		if (risposta.is(':hidden')){
			domanda.css(minus);
		}
		else{
			domanda.css(plus);
		}
		//mostriamo/nascondiamo le risposte
		risposta.slideToggle("slow");

                ProcesoLocal($(this).attr('id'));
	});
	
});
