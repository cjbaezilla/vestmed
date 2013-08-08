window.addEvent('domready', function() {
    $$('#ul-faq li').each(function(li){
		li.addEvent('click', function(e){				 
			$('detalle_' + li.id).setStyle('display', 'block');
			$('cerrar_' + li.id).addEvent('click', function(e){
				$('detalle_' + li.id).setStyle('display', 'none');
			});
		});
	});
});