//--------------------------------------
//function digitoVerificador
//Objetivo: Retornar el Digito verificador de un RUT.
//Parametro(s):(input)String de ingreso de rut (incluyendo el DV).
//(output) DV obtenido
//Uso: desde fnc. verificarRut
//Requiere:  
//--------------------------------------
function digitoVerificador(strRut) {
    var Largo, LargoN, i, Total;
    var Numero="", Verif, Carac, CaracVal;
    var tmpRut,intTmp;
    
    tmpRut = strRut;
    Largo = tmpRut.length;
    LargoN = 0;
    for(i=0;i<Largo;i++) {
        Carac = parseInt(tmpRut.charAt(i),10);
        if(Carac >=0 && Carac <=9) {
			Numero+=tmpRut.charAt(i);
            LargoN++;
	 	}
    }
	Total=0;
    for(i=LargoN-1;i>=0;i--) {
		if((LargoN - i) < 7) {
		   intTmp=LargoN - i + 1;
		} else {
		   intTmp=LargoN - i - 5;
		}
        Total+= parseInt(Numero.charAt(i),10) * intTmp 
    }
    
    CaracVal = 11 - (Total % 11)
    
    if(CaracVal==10) {
       return('K');
	}
	
	if(CaracVal >=0 && CaracVal <=9) {
       return(CaracVal);
	}
	
	if(CaracVal==11) {
	   return(0);
    }
}

function checkDataCliente (form)
{
	if (form.dfRutClt.value == "") 
	{
		alert("\n" + "Por favor, ingrese RUT de la Institucion" );
		//form.dfRutUsr.focus();
		//form.dfRutUsr.select();
		return false;
	} 
	
	if (!ValidarRut(form.dfRutClt)) return false;

	if (form.dfNombre.value == "")
	{
		alert("\n Por favor ingrese la Razon Social ...");
		//form.dfNombre.focus();
		//form.dfNombre.select();
		return false;
	}
	return true;	
}

function checkDataUsuario (form)
{
	if (form.dfRutUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese RUT del Usuario" );
		//form.dfRutUsr.focus();
		//form.dfRutUsr.select();
		return false;
	} 
	//
	//	
	if (!ValidarRut(form.dfRutUsr)) return false;

	if (form.dfAppPat.value == "")
	{
		alert("\n Por favor ingrese el Apellido Paterno del Usuario ...");
		//form.dfAppPat.focus();
  		//form.dfAppPat.select();
		return false;
	}
	//
	if (form.dfNomUsr.value == "")
	{
		alert("\n Por favor ingrese el Nombre del Usuario ...");
		//form.dfNomUsr.focus();
	  	//form.dfNomUsr.select();
		return false;
	}
	//
	if (form.codcmn.value == "_NONE" || form.codcmn.value == "")
	{
		alert("\n Por favor ingrese una Comuna...");
		//form.dfCiudad.focus();
  		//form.dfCiudad.select();
		return false;
	}
	//
	if (form.codcdd.value == "_NONE" || form.codcdd.value == "")
	{
		alert("\n Por favor ingrese una Ciudad...");
		//form.dfCiudad.focus();
  		//form.dfCiudad.select();
		return false;
	}
	//
	if (form.codpro.value == "_NONE" || form.codpro.value == "")
	{
		alert("\n Por favor ingrese una Profesion...");
		//form.dfCiudad.focus();
  		//form.dfCiudad.select();
		return false;
	}
	//
	if (form.codesp.value == "_NONE" || form.codesp.value == "")
	{
		alert("\n Por favor ingrese una Especialidad...");
		//form.dfCiudad.focus();
  		//form.dfCiudad.select();
		return false;
	}
	//
	if (form.rbSexo.value == "")
	{
		alert("\n Por favor ingrese el Sexo...");
		//form.dfDireccion.focus();
  		//form.dfDireccion.select();
		return false;
	}
	//
	if (form.dfDireccion.value == "")
	{
		alert("\n Por favor ingrese alguna direccion...");
		//form.dfDireccion.focus();
  		//form.dfDireccion.select();
		return false;
	}
	//
  	if (form.dfTelefonoUsr.value == "" && form.dfFaxUsr.value == "" && form.dfMovilUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese algun Telefono, FAX o Movil del Usuario" );
		//form.dfTelefonoUsr.focus();
		//form.dfTelefonoUsr.select();
		return false;
	} 
	//
	if (form.dfemail.value == "")
	{
		alert("\n Por favor ingrese el e-mail del Usuario...");
		//form.dfemail.focus();
  		//form.dfemail.select();
		return false;
	}
	//
	aMail = form.dfemail.value.split("@");
	if (aMail.length != 2)
	{
		alert("\n El mail ingresado no es valido...");
		//form.dfemail.focus();
		//form.dfemail.select();
		return false;
	}
	//
	if (form.dfPassword.value == "")
	{
		alert("\n Por favor ingrese la Clave de acceso...");
		//form.dfPassword.focus();
  		//form.dfPassword.select();
		return false;
	}
	//
	strPass = form.dfPassword.value;
	if (strPass.length < 6 || strPass.length > 30)
	{
		alert("\n La clave debe tener entre 6 y 30 caracteres...");
		//form.dfPassword.focus();
  		//form.dfPassword.select();
		return false;
	}
	//
	if (form.dfPassword2.value == "")
	{
		alert("\n Por favor repita la clave de acceso...");
		//form.dfPassword2.focus();
  		//form.dfPassword2.select();
		return false;
	}
	//
	if (form.dfPassword.value != form.dfPassword2.value)
	{
		alert("\n Ambas claves de acceso no son iguales ...");
		//form.dfPassword.focus();
  		//form.dfPassword.select();
		return false;
	}

	return true;	
}

function checkDataFichaUsr(form,tipper)
{
	if (tipper == 2) 
  	   if (!checkDataCliente (form)) return false;

	if (typeof form.dfAppPat == "undefined") {
		if (form.dfemail.value == "")
		{
			alert("\n Por favor ingrese un e-mail ...");
			form.dfemail.focus();
			form.dfemail.select();
			return false;
		}
		aMail = form.dfemail.value.split("@");
		if (aMail.length != 2)
		{
			alert("\n El mail ingresado no es valido...");
			form.dfemail.focus();
			form.dfemail.select();
			return false;
		}
	}
    else if (!checkDataUsuario (form)) return false;		

	return true;
	
}

function checkDataInscripcion(form)
{
	var i;
	var ok = false;
	
	for (i=0; i<form.elements.length; i++) {
   	  if (form.elements[i].name == "rbTipoClt")
   		 if (form.elements[i].checked) {
			tipo = form.elements[i].value;
   		    ok = true;
		}
    }

	if (!ok) 
	{
		alert("\n Debe indicar el tipo de cliente ...");
		return false;
	}

	if (form.dfRutClt.value == "")
	{
		alert("\n Por favor ingrese un Rut ...");
		form.dfRutClt.focus();
		return false;
	}
	
	aRut = form.dfRutClt.value.toUpperCase().split("-");
	if (aRut.length != 2)
	{
		alert("\n El rut no tiene el digito verificador ...");
		form.dfRutClt.focus();
		return false;
	}
	
	dv = digitoVerificador(aRut[0]);
	if (dv != aRut[1])
	{
		alert("\n Rut incorrecto. Intente nuevamente ...");
		form.dfRutClt.focus();
		return false;
	}
	
	if (tipo == 1 && parseInt(aRut[0]) < 50000000)
	{
		alert("\n Rut incorrecto para una persona Institucional ...");
		form.dfRutClt.focus();
		return false;
	}
	
	if (tipo == 2 && parseInt(aRut[0]) > 50000000)
	{
		alert("\n Rut incorrecto para una persona Institucional ...");
		form.dfRutClt.focus();
		return false;
	}
	
	return true;
	
}

function ResfrescarEliminados (form) {
	var buffer = "";

	for (i=0; i<form.elements.length; i++) {
   	  if (form.elements[i].name == "seleccionadof[]")
		if (form.elements[i].checked)
			buffer = buffer + form.elements[i].value + ";";
    }

	return buffer;
}

function EnviarClave() {
   f1.action = "aviso.php?idmsg=20";
   f1.submit();
}

function checkRut(form) {
	selTipo = false;
    for (i=0; i<form.elements.length; i++) {
   	    if (form.elements[i].name == "rbTipoClt")
			if (form.elements[i].checked) {
				selTipo = true;
				TipSel = form.elements[i].value;
			}
    }
	if (!selTipo)
	{
		alert("\n Debe seleccionar el tipo de cliente...");
		form.dfRutUsr.focus();
		return false;
	}
	
	if (TipSel == 1) {
		if (form.dfRutCltUsrIn.value == "")
		{
			alert("\n Debe ingresar el rut de la Institucion ...");
			form.dfRutCltUsrIn.focus();
			return false;
		}
		
		aRut = form.dfRutCltUsrIn.value.toUpperCase().split("-");
		if (aRut.length != 2)
		{
			alert("\n El rut de la Institucion no tiene el digito verificador ...");
			form.dfRutCltUsrIn.focus();
			return false;
		}
		
		dv = digitoVerificador(aRut[0]);
		if (dv != aRut[1])
		{
			alert("\n Rut Institucion incorrecto. Intente nuevamente ...");
			form.dfRutCltUsrIn.focus();
			return false;
		}
	}
	
	if (form.dfRutUsrIn.value == "")
	{
		alert("\n Debe ingresar el rut del Usuario ...");
		form.dfRutUsr.focus();
		return false;
	}
	
	aRut = form.dfRutUsr.value.toUpperCase().split("-");
	if (aRut.length != 2)
	{
		alert("\n El rut del Usuario no tiene el digito verificador ...");
		form.dfRutUsr.focus();
		return false;
	}
	
	dv = digitoVerificador(aRut[0]);
	if (dv != aRut[1])
	{
		alert("\n Rut Usuario incorrecto. Intente nuevamente ...");
		form.dfRutUsr.focus();
		return false;
	}

	if (TipSel == 2) form.dfRutClt.value = form.dfRutUsr.value;
	
	return true;
}

function ValidarRut (obj) {
	var sRut = obj.value;
	
	aRut = sRut.split("-");
	if (aRut.length != 2)
	{
		alert("\n El rut no tiene el digito verificador ...");
		//obj.focus();
		return false;
	}
	aRut[1] = aRut[1].toUpperCase();
	dv = digitoVerificador(aRut[0]);
	if (dv != aRut[1])
	{
		alert("\n Rut incorrecto ...");
		//obj.focus();
		return false;
	}

	return true;
}

function ValidarLogin() {
   if (f1.dfrut.value == "")
   {
		alert("\n Favor ingrese ru Rut ...");
		f1.dfrut.focus();
		return;
   }
   if (f1.dfclave.value == "")
   {
		alert("\n favor ingrese su clave ...");
		f1.dfclave.focus();
		return;
   }
   f1.action = "validalogin.php?form=index";
   f1.submit();
}

function ValidarUsuario() {
   if (f1.dfusr.value == "")
   {
		alert("\n Favor ingrese su UserId ...");
		f1.dfusr.focus();
		return;
   }
   if (f1.dfclave.value == "")
   {
		alert("\n favor ingrese su clave ...");
		f1.dfclave.focus();
		return;
   }
   f1.action = "validalogin.php?form=mdiario";
   f1.submit();
}

function CerrarLogin() {
   f1.action = "cerrarlogin.php";
   f1.submit();
}

function ValidarLogin2(producto,title) {
   //alert("ValidarLogin2");
   if (f1.dfrut.value == "")
   {
		alert("\n Favor ingrese ru Rut ...");
		f1.dfrut.focus();
		return;
   }
   if (f1.dfclave.value == "")
   {
		alert("\n favor ingrese su clave ...");
		f1.dfclave.focus();
		return;
   }
   f1.action = "validalogin.php?form=detalle-producto&parametros=producto@"+producto+"$title@"+title;
   f1.submit();
}


function EnviarFormCttWeb() {
   var sexo=0;
   
   if (f1.dfrut.value == "")
   {
		alert("\n Falta ingresar su rut ...");
		f1.rut.focus();
		return false;
   }
   if (!ValidarRut(f1.dfrut)) return false;
   for (i=0; i<f1.elements.length; i++) {
   	  if (f1.elements[i].name == "sexo")
		if (f1.elements[i].checked)
			sexo = f1.elements[i].value;
   }
   if (sexo == 0)
   {
		alert("\n Falta ingresar el Sexo de la Persona ...");
		//f1.nombre.focus();
		return false;
   }
   if (f1.nombre.value == "")
   {
		alert("\n Falta ingresar su nombre ...");
		f1.nombre.focus();
		return false;
   }
   if (f1.email.value == "")
   {
		alert("\n Falta ingresar el e-mail ...");
		f1.email.focus();
		return false;		
   }
   if (f1.fono.value == "")
   {
		alert("\n Falta ingresar un fono de contacto ...");
		f1.focos.focus();
		return false;		
   }
   if (f1.comentarios.value == "")
   {
		alert("\n Falta ingresar sus Comentarios ...");
		f1.comentarios.focus();
		return false;		
   }
   f1.enviar.disabled = true;
   f1.enviar.value = " enviando ";
   f1.action = "enviarcttweb.php";
   f1.submit();
}

function ValidarLogin3() {
   if (f2.dfrutusr.value == "")
   {
		alert("\n Favor ingrese ru Rut ...");
		f2.dfrutusr.focus();
		return;
   }
   if (f2.dfclave.value == "")
   {
		alert("\n favor ingrese su clave ...");
		f2.dfclave.focus();
		return;
   }
   f2.action = "validalogin.php?form=detalle-producto&login=on";
   f2.submit();
}

function IrAInscripcion () {
   if (!checkDataInscripcion(f2)) return false;
   f2.action = "busqueda-cliente.php";
   f2.submit();
}

function PaginaUsuario () {
   f1.action = "micuenta.php";
   f1.submit();
}

function checkDataClave(form) {
    if (form.dfPasswordOld.value == "")
    {
		alert("\n Falta ingresar la clave actual ...");
		form.dfPasswordOld.focus();
		form.dfPasswordOld.select();
		return false;		
    }
	//
	if (form.dfPasswordNew1.value == "")
	{
		alert("\n Por favor ingrese la Clave de acceso...");
		form.dfPasswordNew1.focus();
  		form.dfPasswordNew1.select();
		return false;
	}
	//
	strPass = form.dfPasswordNew1.value;
	if (strPass.length < 6 || strPass.length > 30)
	{
		alert("\n La clave debe tener entre 6 y 30 caracteres...");
		form.dfPasswordNew1.focus();
  		form.dfPasswordNew1.select();
		return false;
	}
	//
	if (form.dfPasswordNew2.value == "")
	{
		alert("\n Por favor repita la clave de acceso...");
		form.dfPasswordNew1.focus();
  		form.dfPasswordNew1.select();
		return false;
	}
	//
	if (form.dfPasswordNew1.value != form.dfPasswordNew2.value)
	{
		alert("\n Ambas claves de acceso no son iguales ...");
		form.dfPasswordNew1.focus();
  		form.dfPasswordNew1.select();
		return false;
	}

	return true;	
}

function checkDataFichaOdr (form,caso) {
	if (caso == 1) {
		for (i=0; i<form.elements.length; i++) {
		  if (form.elements[i].name == "seleccionadof[]")
			if (form.elements[i].checked)
				return true;
		}
		alert("\nDebe seleccionar algun producto para agregar a la Orden de Compra");
		return false;
	}
	else {
		if (form.dfCod_Sty.value == "") {
			alert("\n Falta ingresar el codigo el Style ...");
			return false;		
		}
		if (form.dfCod_Pat.value == "") {
			alert("\n Falta ingresar el codigo de Patron ...");
			return false;		
		}
		if (form.dfCod_Mca.value == "") {
			alert("\n Falta ingresar La Marca ...");
			return false;		
		}
	}
	return true;
}

function checkDataFichaOdc (form) {
	for (i=0; i<form.elements.length; i++) {
	  if (form.elements[i].name == "seleccionadoc[]")
		if (form.elements[i].checked)
			return true;
	}
	alert("\nNo ha seleccionado ningun producto con orden de compra");
	return false;
}

function checkDataSucFct(form)
{
	//alert("dfTipSvc=" + form.dfTipSvc.value);
	//alert("dfValTipSvc=" + form.dfValTipSvc.value);
	if (form.dfFlgTer.value == 0) {
		if (form.dfPesoPer.value == "0") {
			alert("Favor indique el peso de la persona");
			return false;
		}
		if (form.dfEstaturaPer.value == "0") {
			alert("Favor indique la estatura de la persona");
			return false;
		}
	}
	if (form.dfSucFct.value == "0") {
		alert("Debe ingresar direccion de facturacion");
		return false;
	}
	if (form.dfSuc.value != "0")
	    if (form.dfCrr.value == "_NONE" || form.dfCrrSvc.value == "_NONE") {
		   alert("Debe ingresar el Carrier y Servicio");
		   return false;
   	    }
	if (form.dfComentario.value.length > 1000) {
		alert ("Comentario no puede tener mas de 1.000 caracteres");
		return false;
	}
	if (form.dfTipSvc.value == "VISIBLE" && form.dfValTipSvc.value == "_NONE") {
		alert ("Favor indique si el despacho sera a Domicilio o a Sucursal del Carrier");
		return false;
	}
	if (form.is_dsp.value == "1")
		if (form.dfCostoDspPrd.value == "0") {
			alert ("Favor indiqueeee otro tipo servicio en el despacho pues el servicio seleccionado\nno est\u00e1 disponible en su regi\u00f3n");
			return false;            
		}
	form.enviar.disabled = true;
	form.enviar.value = "Enviando";
	
	return true;
}

function validarDataCompra (form)
{
	if (form.email.value == "" && form.fono.value == "") {
		alert("Favor ingrese un fono o e-mail para poder contactarlo");
		return false;
	}
	if (form.email.value != "") {
		aMail = form.email.value.split("@");
		if (aMail.length != 2)
		{
			alert("\n El mail ingresado no es valido...");
			form.email.focus();
			form.email.select();
			return false;
		}
	}
	
	return true;
}

function ValidaDataFct (form)
{
	if (form.dfTipPer.value == "") {
		alert("Debe ingresar un rut de Empresa");
		return false;
	}
	
	if (form.dfTipPer.value == "1") {
		alert("Solo se permiten rut de empresas");
		return false;
	}
	return true;
}

 