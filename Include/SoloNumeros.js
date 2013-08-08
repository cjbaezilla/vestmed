/*
	Creada por  : Marcos Sepulveda
	Fecha		: 15-05-2000
	Objetivo	: Permite Controlar el ingreso de caracteres en un campo de solo numeros
	Notas 		: Se debe agregar los dos metodos en conjunto onKeyUp y OnKeyPress
*/
var cMiles     = ",";
var cDecimales = ".";

function SoloNumeros(e)
{
    //if (oObjeto.value.substring(oObjeto.value.length-1,oObjeto.value.length) >= 0 ) {}
    //else { oObjeto.value = oObjeto.value.substring(0,oObjeto.value.length-1); }	
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8 || tecla == 0) return true; // 3
    patron =/\d/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

function SoloReal(oObjeto)
{
	if (oObjeto.value.substring(oObjeto.value.length-1,oObjeto.value.length) >= 0) {}
	else if (oObjeto.value.substring(oObjeto.value.length-1,oObjeto.value.length) == cDecimales) {}
	else if (oObjeto.value.substring(oObjeto.value.length-1,oObjeto.value.length) == cMiles) 
	   { oObjeto.value = oObjeto.value.substring(0,oObjeto.value.length-1)+cDecimales}
	else 
	   { oObjeto.value = oObjeto.value.substring(0,oObjeto.value.length-1); }	
}

function soloRUT(evt) {
	var key = evt.keyCode ? evt.keyCode : evt.which ;
	return (key <= 46 || (key >= 48 && key <= 57) || key == 75 || key == 107); 
}

function EsNumeroEnteroPositivo (sNumero)
{
	for (i=0; i < sNumero.length; i++)
	   if (sNumero.charAt(i) < '0' || sNumero.charAt(i) > '9')
	      return false;
	return true;
}