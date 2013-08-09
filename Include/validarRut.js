
/* funciones *************************************************/
/*
Descripci�n: Funcion para validar el rut, cuando existen dos casillas: rut y d�gito verificador.
Requisitos: Se le deben enviar los ID de las casillas de rut y la del d�gito verificador.
Devuelve: 1, si est� correcto. 0 si existe alg�n error.
*/
function validarRutSeparado(rut,dv){
	var ok=0;
	var rut=document.getElementById(rut).value;
	var dv=document.getElementById(dv).value;

	var largo=rut.length;
	var suma=0;
	var mult=2;
	largo--;
	
	while(largo>=0) {
		suma=suma+(rut.charAt(largo)*mult);
		if(mult>6) { mult=2; }
		else { mult++; }
		largo--;
	}

	var resto=suma%11;
	var digito=11-resto;
	
	if(digito==10) { digito="k"; }
	if(digito==11) { digito=0; }
	
	if(!rut || !dv) { ok=0; }
	else if(digito!=dv) { ok=0; }
	else { ok=1; }
	
	return ok;
}


/*
Descripci�n: Funcion para validar el rut, cuando existen una sola casilla que contiene el rut y d�gito verificador.
Requisitos: Se le debe enviar el ID de la casilla  que contiene el rut.
Devuelve: 1, si est� correcto. 0 si existe alg�n error.
*/
function validarRutCompleto(rut){
	var ok=0;
	rut=document.getElementById(rut).value.toUpperCase();
	if(rut.substr(rut.length-1,1)!="K"){
		var dv=rut.substr(rut.length-1,1);
		rut=rut.substr(0,rut.length-1);
	}
	else{ dv="K"; }
	rut=rut=rut.replace(/\D/g,"");

	var largo=rut.length;
	var suma=0;
	var mult=2;
	largo--;
	
	while(largo>=0) {
		suma=suma+(rut.charAt(largo)*mult);
		if(mult>6) { mult=2; }
		else { mult++; }
		largo--;
	}

	var resto=suma%11;
	var digito=11-resto;
	
	if(digito==10) { digito="K"; }
	if(digito==11) { digito=0; }
	
	if(!rut || !dv) { ok=0; }
	else if(digito!=dv) { ok=0; }
	else { ok=1; }
	
	return ok;
}


/*
Descripci�n: Funcion para formatear el rut, cuando existen una sola casilla que contiene el rut y d�gito verificador.
Requisitos: Se le debe enviar el ID de la casilla.
Devuelve: Retorna el rut formateado a la casilla.
*/
//function formatearRut(casilla){
//	var casillaRut=document.getElementById(casilla);
//	var rut=casillaRut.value;
//	var ultimoDigito=rut.substr(rut.length-1,1);
//	if(ultimoDigito.toLowerCase()=="k"){ var terminaEnK=1; }
//	else{ var terminaEnK=0; }
//	rut=rut.replace(/\D/g,"");
//	rutSinFormato=rut;
//	var dv=rut.substr(rut.length-1,1);
//	if(!terminaEnK){ rut=rut.substr(0,rut.length-1); }
//	else{ dv="K"; }
//	if(rut && dv) {
//		casillaRut.value=formatearMillones(rut)+"-"+dv;
//		document.getElementById('buic_rutdv').value=rutSinFormato;
//	}
//}


function formatearRut(casilla,casillaout){
    var casillaRut=document.getElementById(casilla);
    
    var rut=casillaRut.value;
    var ultimoDigito=rut.substr(rut.length-1,1);
    var terminaEnK = (ultimoDigito.toLowerCase()=="k");
    rutSinFormato=rut.replace(/\W/g,"");
    rut=rut.replace(/\D/g,"");
    var dv=rut.substr(rut.length-1,1);
    if(!terminaEnK){ rut=rut.substr(0,rut.length-1); }
    else{ dv="K"; }
    if(rut && dv) {
        casillaRut.value=formatearMillones(rut)+"-"+dv;
        document.getElementById(casillaout).value=rutSinFormato.substr(0,rutSinFormato.length-1)+"-"+rutSinFormato.substr(rutSinFormato.length-1,1);
    }
}




/*
Descripci�n: Funcion para formatear millones.
Requisitos: Se le debe enviar el n�mero a formatear.
Devuelve: Retorna el n�mero formateado.
*/
function formatearMillones(nNmb){
	var sRes = "";
	for (var j, i = nNmb.length - 1, j = 0; i >= 0; i--, j++)
	 sRes = nNmb.charAt(i) + ((j > 0) && (j % 3 == 0)? ".": "") + sRes;
	return sRes;
}


/*
Descripci�n: Funcion para validar que una casilla tenga un m�nimo de d�gitos.
Requisitos: Se le debe enviar el ID de la casilla a verificar, y el m�nimo de d�gitos que se necesitan.
Devuelve: 1, si est� correcto. 0 si existe alg�n error.
*/
function validacionSimple(id,min_digitos){
	var ok=1;
	casilla=document.getElementById(id);
	
	if(min_digitos!=""){
		if(casilla.value.length<min_digitos) { ok=0; }
	}
	else{
		if(casilla.value.length<1) { ok=0; }
	}
	
	return ok;
}


/*
Descripci�n: Funcion para validar que una casilla contenga s�lo numeros.
Requisitos: Se le debe enviar el ID de la casilla a verificar, y el m�nimo de d�gitos que se necesitan.
Devuelve: 1, si est� correcto. 0 si existe alg�n error.
*/
function validacionNumerica(id,min_digitos){
	var ok=1;
	var patron=/\D/;
	casilla=document.getElementById(id);
	
	if(min_digitos!=""){
		if(casilla.value.length<min_digitos) { ok=0; }
	}
	if(casilla.value.length<1) { ok=0; }
	if(patron.test(casilla.value)) { ok=0; }
	
	return ok;
}


/*
Descripci�n: Funcion para validar que una casilla contenga s�lo letras.
Requisitos: Se le debe enviar el ID de la casilla a verificar, y el m�nimo de d�gitos que se necesitan.
Devuelve: 1, si est� correcto. 0 si existe alg�n error.
*/
function validacionAlfabetica(id,min_digitos){
	var ok=1;
	var patron=/[^a-zA-Z \-������������]/;
	casilla=document.getElementById(id);
	txt=casilla.value;
	
	if(min_digitos!=""){
		if(casilla.value.length<min_digitos) { ok=0; }
	}
	if(casilla.value.length<1) { ok=0; }
	if(patron.test(txt)) { ok=0; }
	return ok;
}

function rutBlur(casilla,casillaout) {
    var casillaRut=document.getElementById(casilla);
	
	if(casillaRut.value != '') {
		validaLoginRUT(casilla,casillaout);
	} else {	
		$('dfclave').value = "";
		$('dfclave').readOnly = true;
		$('alerta_error').fade(0);
	}
}

function validaLoginRUT(casilla,casillaout) {
	var errores = new Array();
    var casillaRut=document.getElementById(casilla);
	formatearRut(casilla,casillaout);
	
	if(casillaRut.value!="" && !validarRutCompleto(casilla)){ 
	  errores.push(["Rut","Debe ingresar un RUT v&aacute;lido."]); 
	}
	
	if(errores.length > 0) {
		mostrarErrores(errores);
		$('dfclave').readOnly = true;
		//$('Rut').focus(); /* No funciona en IE */
		return false;
	} else {
		$('alerta_error').fade(0); //requiere mootools
		$('dfclave').readOnly = false;
		return true;
	}
}

/*
Descripci�n: Funcion para mostrar una alerta con todos los errores encontrados.
Requisitos: Se le debe enviar el arreglo con los errores.
Devuelve: Muestra en pantalla una ventana de alerta con los errores encontrados.
*/
function mostrarErrores(error){
	casilla=document.getElementById(error[0][0]);
	//casilla.className="text_login_large error";
	document.getElementById('alerta_error').innerHTML=error[0][1];
	$('alerta_error').fade(1); //requiere mootools
}


/*
Descripci�n: Funcion para mostrar un alert con todos los errores encontrados.
Requisitos: Se le debe enviar el arreglo con los errores.
Devuelve: Muestra en pantalla una ventana de alerta con los errores encontrados.
*/
function alertarErrores(error){
	casilla=document.getElementById(error[0][0]);
	casilla.focus();
	casilla.className="error";
	alert(error[0][1]);
}
