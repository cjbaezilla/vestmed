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
	if (form.dfNombre.value == "")
	{
		alert("\n Por favor ingrese la Razon Social ...");
		form.dfNombre.focus();
		form.dfNombre.select();
		return false;
	}
	//
	if (form.dfDireccion.value == "")
	{
		alert("\n Por favor ingrese la Direccion de la Empresa ...");
		form.dfDireccion.focus();
		form.dfDireccion.select();
		return false;
	}
	//
	if (form.dfCiudad.value == "")
	{
		alert("\n Por favor ingrese la Ciudad ...");
		form.dfCiudad.focus();
		form.dfCiudad.select();
		return false;
	}
	//
	if (form.dfTelefono.value == "" && form.dfFax.value == "") 
	{
	  	alert("\n" + "Por favor, ingrese algun Telefono o FAX" );
		form.dfTelefono.focus();
		form.dfTelefono.select();
		return false;
	} 
	//
	if (form.dfRutRep1.value == "") 
	{
		alert("\n" + "Por favor, ingrese D.N.I. del Representante 1" );
		form.dfRutRep1.focus();
		form.dfRutRep1.select();
		return false;
	} 
	//
	if (form.dfRep1.value == "")
	{
		alert("\n Por favor ingrese en Nombre del Representante 1 ...");
		form.dfRep1.focus();
  		form.dfRep1.select();
		return false;
	}
	//
	if (form.dfRutRep2.value != "") 
	{
      		if (form.dfRutRep2.value == "") 
		{
		       alert("\n" + "Por favor, ingrese D.N.I. del Representante 2 " );
		       form.dfRutRep2.focus();
		       form.dfRutRep2.select();
		       return false;
		} 
		//
		//	
		if (form.dfRep2.value == "")
		{
		     alert("\n Por favor ingrese en Nombre del Representante 2 ...");
		     form.dfRep2.focus();
  		     form.dfRep2.select();
		     return false;
		}
	}

	return true;	
}

function checkDataUsuario (form)
{
	if (form.dfRutUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese D.N.I del Usuario" );
		form.dfRutUsr.focus();
		form.dfRutUsr.select();
		return false;
	} 
	//
	//	

	if (form.dfAppUsr.value == "")
	{
		alert("\n Por favor ingrese los Appellidos del Usuario ...");
		form.dfAppUsr.focus();
  		form.dfAppUsr.select();
		return false;
	}
	//
	if (form.dfNomUsr.value == "")
	{
		alert("\n Por favor ingrese los Nombres del Usuario ...");
		form.dfNomUsr.focus();
	  	form.dfNomUsr.select();
		return false;
	}
	//
  	if (form.dfTelefonoUsr.value == "" && form.dfFaxUsr.value == "" && form.dfMovilUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese algun Telefono, FAX o Movil del Usuario" );
		form.dfTelefonoUsr.focus();
		form.dfTelefonoUsr.select();
		return false;
	} 
	//
	if (form.dfemail.value == "")
	{
		alert("\n Por favor ingrese el e-mail del Usuario...");
		form.dfemail.focus();
  		form.dfemail.select();
		return false;
	}
	//
	if (form.dfidusr.value == "")
	{
		alert("\n Por favor ingrese el Id del usuario...");
		form.dfidusr.focus();
  		form.dfidusr.select();
		return false;
	}
	//
	if (form.dfPassword.value == "")
	{
		alert("\n Por favor ingrese la Contraseña de acceso...");
		form.dfPassword.focus();
  		form.dfPassword.select();
		return false;
	}
	//
	strPass = form.dfPassword.value;
	if (strPass.length < 6 || strPass.length > 30)
	{
		alert("\n La Contraseña debe tener entre 6 y 30 caracteres...");
		form.dfPassword.focus();
  		form.dfPassword.select();
		return false;
	}
	//
	if (form.dfPassword2.value == "")
	{
		alert("\n Por favor repita la Contraseña de acceso...");
		form.dfPassword2.focus();
  		form.dfPassword2.select();
		return false;
	}
	//
	if (form.dfPassword.value != form.dfPassword2.value)
	{
		alert("\n Ambas Contraseñas de acceso no son iguales ...");
		form.dfPassword.focus();
  		form.dfPassword.select();
		return false;
	}

	return true;	
}

function checkDataUsuarioAdm (form)
{
	if (form.dfRutUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese D.N.I del Usuario" );
		form.dfRutUsr.focus();
		form.dfRutUsr.select();
		return false;
	} 
	//
	//	

	if (form.dfAppUsr.value == "")
	{
		alert("\n Por favor ingrese los Appellidos del Usuario ...");
		form.dfAppUsr.focus();
  		form.dfAppUsr.select();
		return false;
	}
	//
	if (form.dfNomUsr.value == "")
	{
		alert("\n Por favor ingrese los Nombres del Usuario ...");
		form.dfNomUsr.focus();
	  	form.dfNomUsr.select();
		return false;
	}
	//
  	if (form.dfTelefonoUsr.value == "" && form.dfFaxUsr.value == "" && form.dfMovilUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese algun Telefono, FAX o Movil del Usuario" );
		form.dfTelefonoUsr.focus();
		form.dfTelefonoUsr.select();
		return false;
	} 
	//
	if (form.dfemail.value == "")
	{
		alert("\n Por favor ingrese el e-mail del Usuario...");
		form.dfemail.focus();
  		form.dfemail.select();
		return false;
	}
	//
	if (form.dfidusr.value == "")
	{
		alert("\n Por favor ingrese el Id del usuario...");
		form.dfidusr.focus();
  		form.dfidusr.select();
		return false;
	}
	//
	if (form.cmbPerfil.value == 0)
	{
		alert("\n Por favor ingrese perfil del usuario...");
		return false;
	}

	return true;	
}

function checkDataEnvioClave(form)
{
	if (form.dfRutClt.value == "") 
	{
		alert("\n" + "Por favor, ingrese RUC Cliente" );
		form.dfRutClt.focus();
		form.dfRutClt.select();
		return false;
	} 
	//
	//	
	if (form.dfRutUsr.value == "") 
	{
		alert("\n" + "Por favor, ingrese D.N.I Usuario" );
		form.dfRutUsr.focus();
		form.dfRutUsr.select();
		return false;
	} 
	//
	//	
	if (form.dfUsuario.value == "")
	{
		alert("\n Por favor ingrese el Id del usuario...");
	   	form.dfUsuario.focus();
  	   	form.dfUsuario.select();
	   	return false;
	}
	//
	//
  	if (form.dfemail.value == "")
	{
	   	alert("\n Por favor ingrese el e-mail del Usuario...");
	   	form.dfemail.focus();
  	   	form.dfemail.select();
	   	return false;
	}

	submitted = true;
		
	return true;
		
}
function checkDataFichaUsr(form)
{
	if (form.dfRutClt.value == "") 
	{
		alert("\n" + "Por favor, ingrese RUC Cliente" );
		form.dfRutClt.focus();
		form.dfRutClt.select();
		return false;
	} 
	//
	//	
	//alert ("Valida Cliente");
	if (!checkDataCliente (form)) return false;

	//alert ("Valida Usuario");
        if (!checkDataUsuario (form)) return false;		

	return true;
	
}

function checkDataLoginUsr(form)
{

	if (form.dfUsuario.value == "" || form.dfClave.value == "") 
	{
		alert("\n" + "Debe ingresar el Usuario y la Clave ..." );
		form.dfUsuario.focus();
		form.dfUsuario.select();
		return false;
	} 
	//
	//			
	return true;
}

function checkDataProveedor(form)
{
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == "seleccionado[]")
   		if (form.elements[i].checked)
   		   return true;
   }

   alert ("Debe seleccionar un proveedor ...");
   return false;
}

function checkDataComprador(form)
{
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == "seleccionado[]")
   		if (form.elements[i].checked)
   		   return true;
   }

   alert ("Debe seleccionar un comprador ...");
   return false;
}


function checkDataRubro(form)
{
   for (i=0; i<form.elements.length; i++) {
//   	alert (form.elements[i].name);
   	if (form.elements[i].name == "seleccionado[]")
   		if (form.elements[i].checked)
   		   return true;
   }

   alert ("Debe seleccionar un rubro ...");
   return false;
}

function checkDataEliminar()
{
   return confirm("Confirma Eliminación ?");
}

function checkDataAdjudicar(form) {
   aProductos = new Array(numItem);

   for (i=0; i<numItem; i++) aProductos[i] = 0;
   
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name.substring(0,13) == "seleccionado_")
   	   if (form.elements[i].checked) {
   	   	objeto = form.elements[i].name;
   	   	aToken = objeto.split("_");
   	   	aProductos[parseInt(aToken[1])-1]++;
   	   }
   }	
   itemFaltantes = 0;
   ultimoItem = 0;
   for (i=0; i<numItem; i++) 
      if (aProductos[i] == 0) {
      	itemFaltantes++;
      	ultimoItem = i;
      }
   
   if (itemFaltantes > 0) {
   	if (itemFaltantes == 1) {
   	   msgerror = "Favor indique los proveedores seleccionados en el Item ";
           for (i=0; i<numItem; i++) 
             if (aProductos[i] == 0) {
             	msgerror+=(i+1);
             	break;
             }
   	}
   	else {
   	   msgerror = "Favor indique los proveedores seleccionados para los Item ";
           itemAgregados=0;
           for (i=0; i<numItem; i++) {
             if (aProductos[i] == 0) 
             	if (i == ultimoItem) {
             	   msgerror+=" y "+(i+1);
             	   break;
             	}
             	else {
             	   if (itemAgregados++ == 0)
             	      msgerror+=(i+1);
             	   else
             	      msgerror+=", "+(i+1);
                }
           }
   	}
        alert (msgerror);
        return false;
   }
   return true;
}

function checkDataChangePwd(form)
{
	var strPass;
	
	if (form.dfClave1.value == "") 
	{
		alert("\n" + "Debe ingresar la Clave Antigua ..." );
		form.dfClave1.focus();
		form.dfClave1.select();
		return false;
	} 
	//
	//			
	if (form.dfClave2.value == "") 
	{
		alert("\n" + "Debe ingresar la Clave Nueva ..." );
		form.dfClave2.focus();
		form.dfClave2.select();
		return false;
	} 
	//
	//			
	strPass = form.dfClave2.value;
	if (strPass.length < 6 || strPass.length > 30)
	{
		alert("\n La Contraseña debe tener entre 6 y 30 caracteres...");
		form.dfClave2.focus();
  		form.dfClave2.select();
		return false;
	}
	//
	//			
	if (form.dfClave3.value == "") 
	{
		alert("\n" + "Debe ingresar la Repeticion de la Clave Nueva ..." );
		form.dfClave3.focus();
		form.dfClave3.select();
		return false;
	} 
	//
	//			
	if (form.dfClave2.value != form.dfClave3.value) 
	{
		alert("\n" + "Clave nueva con su repeticion no son iguales ..." );
		form.dfClave3.focus();
		form.dfClave3.select();
		return false;
	} 

	return true;
}


function Habilitar ()
{
	if (f1.cbExisteClt.checked)
	{
		f1.dfNombre.value = "";
		f1.dfNombreFantasia.value = "";
		f1.dfDireccion.value = "";
		f1.dfCiudad.value = "";
		f1.dfTelefono.value = "";
		f1.dfFax.value = "";
		f1.dfWeb.value = "";
		f1.dfRutRep1.value = "";
		f1.dfRep1.value = "";
		f1.dfRutRep2.value = "";
		f1.dfRep2.value = "";

		f1.dfNombre.readOnly = true;
		f1.dfNombreFantasia.readOnly = true;
		f1.dfDireccion.readOnly = true;
		f1.dfCiudad.readOnly = true;
		f1.dfTelefono.readOnly = true;
		f1.dfFax.readOnly = true;
		f1.dfWeb.readOnly = true;
		f1.dfRutRep1.readOnly = true;
		f1.dfRep1.readOnly = true;
		f1.dfRutRep2.readOnly = true;
		f1.dfRep2.readOnly = true;		
	}
	else
	{
		f1.dfNombre.readOnly = false;
		f1.dfNombreFantasia.readOnly = false;
		f1.dfDireccion.readOnly = false;
		f1.dfCiudad.readOnly = false;
		f1.dfTelefono.readOnly = false;
		f1.dfFax.readOnly = false;
		f1.dfWeb.readOnly = false;
		f1.dfRutRep1.readOnly = false;
		f1.dfRep1.readOnly = false;
		f1.dfRutRep2.readOnly = false;
		f1.dfRep2.readOnly = false;		
	}
}

function checkDataProveedoresSel(form)
{
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == "seleccionadof[]" || form.elements[i].name == "seleccionadop[]")
   		if (form.elements[i].checked)
   		   return true;
   }

   alert ("Debe seleccionar un proveedor ...");
   return false;
}

function MarcarTodos(form,nombrecheckbox) {
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == nombrecheckbox)
   		form.elements[i].checked = true;
   }	
}

function DesMarcarTodos(form,nombrecheckbox) {
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == nombrecheckbox)
   		form.elements[i].checked = false;
   }	
}

function MarcarSeleccionados(form,nombrecheckbox,arreglo) {
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == nombrecheckbox) 
   	   for (j=0; j<arreglo.length; j++)
   	      if (form.elements[i].value == arreglo[j])
   		   form.elements[i].checked = true;
   }	
}

function checkDataAceptarCL(form)
{
   if (form.acepto.checked) return true;

   alert ("Si no acepta las condiciones legales favor anular la cotización"); 
     
   return false;
}

function checkDataConsultar(form)
{
   if (form.dfConsulta.value == "") {
      alert ("Favor Ingrese su consulta"); 
      form.dfConsulta.focus();
      form.dfConsulta.select();
      return false;
   }

   return true;
}

function checkDataRespuesta(form)
{
   if (form.dfRespuesta.value == "") {
      alert ("Favor Ingrese su respuesta"); 
      form.dfRespuesta.focus();
      form.dfRespuesta.select();
      return false;
   }

   return true;
}

function checkDataResponderCot (form)
{
	
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name.substring(0,8) == "cantidad") 
   	      if (form.elements[i].value == "") {
   		   alert ("Falta ingresar una Cantidad");
   		   form.elements[i].focus();
   		   form.elements[i].select();
      		   return false;
              }
   }	

   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name.substring(0,5) == "valor")
   	      if (form.elements[i].value == "") {
   		   alert ("Falta ingresar un Valor Total Ofertado");
   		   form.elements[i].focus();
   		   form.elements[i].select();
      		   return false;
              }
   }	

   if (form.validez.value == "") {
      alert ("Favor Ingrese validez de la Cotización"); 
      form.validez.focus();
      form.validez.select();
      return false;
   }

   return true;
}

function anularCot(sid) {
   if (confirm("Confirma Anulación de la Cotización")) {
      paginaIni = "actualizar.php?sid="+sid+"&caso=16";
      targ='window';
      eval(targ+".location='"+paginaIni+"'");
   }
}

function anularCotPro(sid) {
   if (confirm("Confirma Anulación de la Cotización")) {
      paginaIni= "actualizar.php?sid="+sid+"&caso=55";
      targ='window';
      eval(targ+".location='"+paginaIni+"'");
   }
}

function checkDataCotizacion1(form)
{
	if (form.cmbProductos.value == 0) {
		alert ("Debe ingresar algún rubro ...");
		return false;
	}
	
	//if (form.cmbDia.value == 0 || form.cmbMes.value == 0 || form.cmbAno.value == 0) {
	//	alert ("Ingresar Fecha límite recepción de ofertas correctamente ...");
	//	return false;
	//}

	if (form.fecha.value == "") {
		alert ("Ingresar Fecha límite recepción de ofertas correctamente ...");
		return false;
	}

	if (form.cmbMoneda.value == 0) {
		alert ("Debe ingresar la moneda de cotización ...");
		return false;
	}

        return true;
}

function checkDataCotizacion2(form)
{
	criterios=0;
	valores=0;
	for (i=0; i<form.elements.length; i++) {
   	   if (form.elements[i].name.substring(0,8) == "criterio")
   	      if (form.elements[i].value != "") criterios++;
   	   if (form.elements[i].name.substring(0,5) == "valor")
   	      if (form.elements[i].value != "") valores++;
	}
	
	if (valores == 0 && criterios == 0) {
		alert ("Ingrese algun criterio para Adjudicar la cotizacion");
		return false;
	}

	if (valores == criterios) {
		return true;
	}

	if (valores > criterios) {
		alert ("Faltan criterios para los valores ingresados");
		return false;
	}

	if (valores < criterios) {
		alert ("Faltan valores para los criterios ingresados");
		return false;
	}
}

function MarcarDesmarcarTodos(form,obj,nombrecheckbox) {
   
   if (obj.value == "Marcar Todos") {
      valor = true;
      obj.value = " Quitar Todos";
   }
   else {
      valor = false;
      obj.value = "Marcar Todos";
   }
      
   for (i=0; i<form.elements.length; i++) {
   	if (form.elements[i].name == nombrecheckbox)
   		form.elements[i].checked = valor;
   }	
}

function checkDataIncripcionOnLine(form) {
	if (!form.rbTamano[0].checked && !form.rbTamano[1].checked && !form.rbTamano[2].checked) {
		alert ("Favor ingrese el tamaño de su Empresa ...");
		form.rbTamano[0].focus();
		form.rbTamano[0].select();
		return false;
	}

	if (!form.rbTipo[0].checked && !form.rbTipo[1].checked) {
		alert ("Favor ingrese el tipo de Cliente ...");
		form.rbTipo[0].focus();
		form.rbTipo[0].select();
		return false;
	}
}

function checkDataTipoContrato(form) {
	if (!form.rbTipoContrato[0].checked && !form.rbTipoContrato[1].checked) {
		alert ("Favor ingrese el tipo de contrato ...");
		form.rbTipoContrato[0].focus();
		form.rbTipoContrato[0].select();
		return false;
	}
}
