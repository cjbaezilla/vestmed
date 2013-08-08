/* FUNCIONES INICIALES ***************/
function Inicializa() {
	var fav=$('favoritos');
	if(fav){
		$('favoritos').fade(0,0);
		$('fadeInFavoritos').addEvent('mouseover', function(){$('favoritos').fade(1); });
		$('fadeInFavoritos').addEvent('mouseout', function(){$('favoritos').fade(0); });
	}
	
	//caja-ayuda
	var ayu=$('caja-ayuda');
	if(ayu){
		$('caja-ayuda').fade(0,0);
		$('btn_ayuda').addEvent('click', function(){ $('caja-ayuda').fade(1); });
		$('btn_cerrar_ayuda').addEvent('click', function(){ $('caja-ayuda').fade(0); });
	}
	
	//caja-comparte
	var com=$('caja-comparte');
	if(com){
		$('caja-comparte').fade(0,0);
		$('btn_comparte').addEvent('click', function(){ $('caja-comparte').fade(1); });
		$('btn_cerrar_comparte').addEvent('click', function(){ $('caja-comparte').fade(0); });
		cambiarTab('pestana2','tab2');
	}
}

function addEvent(obj, evType, fn) { 
	if(obj.addEventListener) { 
		obj.addEventListener(evType, fn, false); 
		return true; 
	} else if(obj.attachEvent) { 
		var r = obj.attachEvent("on"+evType, fn); 
		return r; 
	} else { 
		return false; 
	} 
}

addEvent(window,'load', function() {
	document.getElementById("submit").disabled = false;								 
});	
	
/*************************************/

/* TABS CAJA COMPARTE ****************/
function cambiarTab(pestana,tab){
	var tabs=['tab1','tab2'];
	var pestanas=['pestana1','pestana2'];
	for(i=0;i<tabs.length;i++){
		document.getElementById(tabs[i]).style.display="none";
		document.getElementById(pestanas[i]).className="";
	}
	document.getElementById(tab).style.display="block";
	document.getElementById(pestana).className="activa";
}
/*************************************/

/* BUSCADOR CABECERA *****************/
function limpiarBusqueda(origen){
	casilla=origen.parentNode;
	if(origen.value=="") {casilla.className="ocupado";}
}
function restaurarBusqueda(origen){
	casilla=origen.parentNode;
	if(origen.value=="") {casilla.className="vacio";}
}
/*************************************/

/* VALIDACION ************************/
function solicitarClave() {
	document.getElementById('FRMLOGIN').action = "http://mipcs.entelpcs.com/mipcs2/comunes/solicitarClave/enviarClaveMovil.do";
	document.getElementById('FRMLOGIN').submit();
}

function validaLoginMovil() {
	var errores = new Array();	
	
	limpiarCasillas(["Movil"],'text_login_large');
	if($("Movil").value!="" && !validacionNumerica("Movil","8")){ errores.push(["Movil","El número de tu PCS debe ser de 8 dígitos."]); }
	
	if(errores.length > 0) {
		mostrarErrores(errores);
		//$('Movil').focus(); /* No funciona en IE */
		return false;
	} else {
		$('alerta_error').fade(0); //requiere mootools
		return true;
	}
}

function validaLoginRUT() {
	var errores = new Array();
	formatearRut('Rut');
	
	limpiarCasillas(["Rut"],'text_login_large');
	if($("Rut").value!="" && !validarRutCompleto("Rut","10")){ errores.push(["Rut","Debes ingresar un RUT válido."]); }
	
	if(errores.length > 0) {
		mostrarErrores(errores);
		//$('Rut').focus(); /* No funciona en IE */
		return false;
	} else {
		$('alerta_error').fade(0); //requiere mootools
		return true;
	}
}

function validaLoginPIN() {
	var errores = new Array();
	
	limpiarCasillas(["PIN"],'text_login_large');
	if($("PIN").value==""){ errores.push(["PIN","La Clave de tu PCS debe ser de 4 dígitos."]); }

	if(errores.length > 0) {
		mostrarErrores(errores);
		//$('PIN').focus(); /* No funciona en IE */
		return false;
	} else {
		$('alerta_error').fade(0); //requiere mootools
		return true;
	}
}

function validaFormulario(){
	var ok=1;
	var errores = new Array();

	//validacion
	formatearRut('Rut');
	limpiarCasillas(["Movil","Rut","PIN"],'text_login_large');
	
	if(document.getElementById("Movil").value!="" && !validacionNumerica("Movil","8")){ ok=0; errores.push(["Movil","El número de tu PCS debe ser de 8 dígitos."]); }
	if(document.getElementById("Rut").value!="" && !validarRutCompleto("Rut","10")){ ok=0; errores.push(["Rut","Debes ingresar un RUT válido."]); }
	if(!validacionNumerica("PIN","4")){ ok=0; errores.push(["PIN","La Clave de tu PCS debe ser de 4 dígitos."]); }
	
	//respuesta
	if(ok==0){
		mostrarErrores(errores);
		return false;
	}
	else{
		$('alerta_error').fade(0); //requiere mootools
		return true;
	}
}

function enviarFormulario(){
	var errores = new Array();
	
	if(document.getElementById("Movil").value!="" && !validacionNumerica("Movil","8")){ errores.push(["Movil","Debes ingresar el número de tu PCS."]); }
	if(document.getElementById("Rut").value!="" && !validarRutCompleto("Rut","10")){ errores.push(["Rut","Debes ingresar tu RUT."]); }
	if(!validacionNumerica("PIN","4")){ errores.push(["PIN","Debes ingresar la clave de tu PCS."]); }

	if(errores.length) { 
		mostrarErrores(errores); 
	} else{
		if(validaFormulario()){
			
			if(!document.getElementById("submit").disabled) {
				
				document.getElementById("submit").src="/archivos_home/img/home/btn_login.gif";
				document.getElementById("submit").disabled = true;
				
				setTimeout(function() {
					document.getElementById("FRMLOGIN").action="http://www.entelpcs.cl/login/valida_ws.iws?origen=home";
					document.getElementById("FRMLOGIN").submit();
				}, 500);
			}
		}
	}
}

function limpiarCasillas(casillas,estilo){
	for(i=0;i<casillas.length;i++){
		document.getElementById(casillas[i]).className=estilo;
	}
}

function enviarComparte(){
	var errores = new Array();
	limpiarCasillas(["para","nombre","email"],'');
	
	if(!validarMail("para")){ ok=0; errores.push(["para","Debes ingresar el e-mail del destinatario."]); }
	if(!validacionAlfabetica("nombre","3")){ ok=0; errores.push(["nombre","Debes ingresar tu nombre."]); }
	if(!validarMail("email")){ ok=0; errores.push(["email","Debes ingresar tu e-mail."]); }

	if(errores.length){
		alertarErrores(errores);
	}
	else{
		var str = "para="+document.getElementById('para').value+"&";
		str += "nombre="+document.getElementById('nombre').value+"&";
		str += "email="+document.getElementById('email').value;
		$('frm-comparte').fade(1,0);
		$('ajax-enviando').fade(0,1);
		makePOSTRequest('/envio_mail/envio_social.iws', str, 'ajax-enviando', 'ajax-sent', 'frm-comparte');
	}
}
/*************************************/

/* FIX PARA EXPLORER 6 *******************/
function PNG_loader() {
   for(var i=0; i<document.images.length; i++) {
      var img = document.images[i];
      var imgName = img.src.toUpperCase();
      if (imgName.substring(imgName.length-3, imgName.length) == "PNG") {
         var imgID = (img.id) ? "id='" + img.id + "' " : "";
         var imgClass = (img.className) ? "class='" + img.className + "' " : "";
         var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' ";
         var imgStyle = "display:inline-block;" + img.style.cssText;
         if (img.align == "left") imgStyle += "float:left;";
         if (img.align == "right") imgStyle += "float:right;";
         if (img.parentElement.href) imgStyle += "cursor:hand;";
         var strNewHTML = "<span " + imgID + imgClass + imgTitle
            + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
            + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
            + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>";
         img.outerHTML = strNewHTML;
         i--;
      }
   }
}
if(window.attachEvent) window.attachEvent("onload",PNG_loader);

/*****************************************/

/* AJAX **********************************/
var http_request = false;
function makePOSTRequest(url, parameters, ocultarAlEnviar, mostrarAlEnviar, casillaInicial) {
  http_request = false;
  casillaOcultar=ocultarAlEnviar;
  casillaMostrar=mostrarAlEnviar;
  if (window.XMLHttpRequest) {
	 http_request = new XMLHttpRequest();
	 if (http_request.overrideMimeType) {
		http_request.overrideMimeType('text/html');
	 }
  } else if (window.ActiveXObject) { // IE
	 try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
		try {
		   http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	 }
  }
  if (!http_request) {
	 alert('Cannot create XMLHTTP instance');
	 return false;
  }
  
  http_request.onreadystatechange = function(){
	  if (http_request.readyState == 4) {
		 if (http_request.status == 200) {
			//result = http_request.responseText;
			$(ocultarAlEnviar).fade(1,0); //requiere mootools
			$(mostrarAlEnviar).fade(0,1); //requiere mootools
			if(casillaInicial) { setTimeout(function(){ $(mostrarAlEnviar).fade(1,0); $(casillaInicial).fade(0,1); },2000); } //requiere mootools
		 } else {
			alert('Ha ocurrido un error. Por favor, inténtalo más tarde.');
		 }
	  }
  }
  http_request.open('POST', url, true);
  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http_request.setRequestHeader("Content-length", parameters.length);
  http_request.setRequestHeader("Connection", "close");
  http_request.send(parameters);
}
/*****************************************/