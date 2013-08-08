var txt_Movil = 'Nº PCS';
var txt_Rut = 'RUT Usuario';
var txt_PIN = '';
var expressInstall = "archivos_home/swf/expressInstall.swf";

/* FUNCIONES INICIALES ****************/
window.addEvent('domready', function() {								 
	//mensaje de error
	if($('alerta_error')) {
		$('alerta_error').fade(0,0);
	}
	
	if($('Movil')) $('Movil').addEvent('keypress', enterForm);
	if($('Rut')) $('Rut').addEvent('keypress', enterForm);
	if($('PIN')) $('PIN').addEvent('keypress', enterForm);
	if($('submit')) $('submit').addEvent('keypress', enterForm);

	Inicializa();
});

function enterForm(event) {
	if(event.code == 13 && validaFormulario() && $('PIN').value.length==4) enviarFormulario()
}
/*************************************/
function movilFocus() {
	if($('Movil').value == txt_Movil) {
		$('Movil').value = '';
	}
}
function movilBlur() {
	if($('Movil').value != '') {
		//validaFormulario(); 
		validaLoginMovil();
	} else {		
		$('Movil').value = txt_Movil;
		limpiarCasillas(['Movil'],'text_login_large');
		
		if($('Rut').value == txt_Rut && $('PIN').value == txt_PIN) {
			$('alerta_error').fade(0);
		}
	}
}
function rutFocus() {
	if($('Rut').value.substr(0,3) == txt_Rut.substr(0,3)) {
		$('Rut').value='';
	}
}
function rutBlur() {
	if($('Rut').value != '') {
		//validaFormulario(); 
		validaLoginRUT();
	} else {	
		$('Rut').value = txt_Rut;
		limpiarCasillas(['Rut'],'text_login_large');
		
		if($('Movil').value == txt_Movil && $('PIN').value == txt_PIN) {
			$('alerta_error').fade(0);
		}
	}
}
function labelClaveClick() {
	$('labelClave').style.display = 'none';
	$('PIN').focus();	
}

function pinFocus() {
	if($('PIN').value == txt_PIN) {
		$('PIN').value='';
		$('labelClave').style.display = 'none';
	}
}
function pinBlur() {
	if($('PIN').value != '') {
		//validaFormulario(); 
		validaLoginPIN();
	} else {			
		$('PIN').value = txt_PIN;
		$('labelClave').style.display = 'block';
		
		limpiarCasillas(['PIN'],'text_login_large');
		if($('Movil').value == txt_Movil && $('Rut').value == txt_Rut) {
			$('alerta_error').fade(0);
		}
	}
}
function pinKeyUp() {
	if($('PIN').value.length==4) { 
		//validaFormulario(); 
		validaLoginPIN();
	}	
}


function soloRUT(evt) {
	var key = evt.keyCode ? evt.keyCode : evt.which ;
	return (key <= 31 || (key >= 48 && key <= 57) || key == 75 || key == 107); 
}

function soloNumeros(evt){
	var key = evt.keyCode ? evt.keyCode : evt.which ;
	return (key <= 31 || (key >= 48 && key <= 57)); 
}

//************************
function ingresaWapPush() {
	$('wp_portada').style.display = 'none';
	$('wp_openx').style.display = 'block';
	ajaxGet('wp_home.html','listado_lista');
	return false;
}
//************************
//   GET: ajaxGet(url, div)
//************************
function ajaxGet(url, div) {
	ajax = ajaxobj();
	ajax.open("GET", url, true);

	ajax.onreadystatechange = function() {
		switch(ajax.readyState) {
			case 1: 
				document.getElementById(div).innerHTML = '<img src="archivos_home/img/home/ajax.gif" border="0" />Cargando...';
				break;
				
			case 4: 
				document.getElementById(div).innerHTML = ajax.responseText;
				break;
		}
	}
	ajax.send(null);
}
//************************
//    AJAX: OBJETO
//************************
function ajaxobj() {
	try	{
		_ajaxobj = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			_ajaxobj = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E)	{
			_ajaxobj = false;
		}
	}
	if(!_ajaxobj && typeof XMLHttpRequest!='undefined') {
		_ajaxobj = new XMLHttpRequest();
	}
	
	return _ajaxobj;
}

function addSwfObjects() {
	var flashvars = {
        xml:"http://medios.entelpcs.cl/www/xml/banner_home_new.xml"
	};
	var params = {
		allowScriptAccess: "always", 
		wmode:"transparent"
	};
	var attributes_banner = {
		allowScriptAccess: "always",
		id: "banner_swf"
	};	
    var attributes_planes_personas = {
		allowScriptAccess: "always",
		id: "recomendador_planes_personas_swf"
	};	
	var attributes_equipos_personas = {
		allowScriptAccess: "always",
		id: "recomendador_equipos_personas_swf"
	};	
	 var attributes_carrusel = {
		allowScriptAccess: "always",
		id: "contenedor_carrusel_swf"
	};	
    var flashvars_slider = {
        xml:"/archivos_home/swf/slider.xml"
	};
		
	swfobject.embedSWF("/archivos_home/swf/banner_principal.swf", "banner_swf", "756", "229", "9", expressInstall, flashvars, params, attributes_banner);
	swfobject.embedSWF("/archivos_home/swf/recomendador_planes_personas.swf", "recomendador_planes_personas_swf", "282", "199", "9", expressInstall, null, params, attributes_planes_personas);
	swfobject.embedSWF("/archivos_home/swf/recomendador_equipos_personas.swf", "recomendador_equipos_personas_swf", "282", "199", "9", expressInstall, null, params, attributes_equipos_personas);
	swfobject.embedSWF("/archivos_home/swf/slider.swf", "contenedor_carrusel_swf", "761", "159", "9", expressInstall, flashvars_slider, params, attributes_carrusel);
}
