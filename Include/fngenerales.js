// *******************************
// * Funcion fecha      	 *
// *******************************
function fecha()
{
        today = new Date();
        day = today.getDay();

        if ( day == 0 ) { document.write("Domingo "); }
        if ( day == 1 ) { document.write("Lunes "); }
        if ( day == 2 ) { document.write("Martes "); }
        if ( day == 3 ) { document.write("Mi&eacute;rcoles "); }
        if ( day == 4 ) { document.write("Jueves "); }
        if ( day == 5 ) { document.write("Viernes "); }
        if ( day == 6 ) { document.write("S&aacute;bado "); }

        today = new Date();
        year = today.getYear();
		if ( year <= 200) {year = year + 1900}
        if ( today.getMonth() == 0 ) { month = "Enero" }
        if ( today.getMonth() == 1 ) { month = "Febrero" }
        if ( today.getMonth() == 2 ) { month = "Marzo" }
        if ( today.getMonth() == 3 ) { month = "Abril" }
        if ( today.getMonth() == 4 ) { month = "Mayo" }
        if ( today.getMonth() == 5 ) { month = "Junio" }
        if ( today.getMonth() == 6 ) { month = "Julio" }
        if ( today.getMonth() == 7 ) { month = "Agosto" }
        if ( today.getMonth() == 8 ) { month = "Septiembre" }
        if ( today.getMonth() == 9 ) { month = "Octubre" }
        if ( today.getMonth() == 10 ) { month = "Noviembre" }
        if ( today.getMonth() == 11 ) { month = "Diciembre" }

       document.write( today.getDate(), " de ", month, " ", year);
}

function FormatNumero (cNumeroIn) {
    var i = 0;
    var j = 0;
    var cNumeroFmt = "";
    var cNumero = ""+cNumeroIn;
    for (i = cNumero.length-1; i >= 0; --i) {
       cNumeroFmt = cNumero.substring(i,i+1) + cNumeroFmt;
       if (++j == 3) {
           cNumeroFmt = "." + cNumeroFmt;
           j = 0;
       }
    }
    if (cNumeroFmt.substring(0,1) == ".")
	cNumeroFmt = cNumeroFmt.substring (1,cNumeroFmt.length);

    return cNumeroFmt;
}

function ExtraerNumDecimales (cNumeroIn) {
	var cNumero = ""+cNumeroIn;
	var sToken1 = cNumero.split(cDecimales);
	
	if (sToken1.length > 1)
	   return sToken1[1].length;
	return 0;
}
	
function FormatReal (cNumeroIn,decimales) {
    var i = 0;
    var j = 0;
    var cNumeroFmt = "";
    var delta=0.5;
        
    for (i=0; i<decimales; i++) delta = delta / 10.0;
    cNumeroIn = cNumeroIn+delta;
    
    var cNumero = ""+cNumeroIn;
    
    var sToken1 = cNumero.split(".");

    for (i = sToken1[0].length-1; i >= 0; --i) {
       cNumeroFmt = sToken1[0].substring(i,i+1) + cNumeroFmt;
       if (++j == 3) {
           cNumeroFmt = cMiles + cNumeroFmt;
           j = 0;
       }
    }
    if (cNumeroFmt.substring(0,1) == cMiles)
       cNumeroFmt = cNumeroFmt.substring(1,cNumeroFmt.length);
       
    if (decimales > 0) {
       if (sToken1.length > 1){
       	  cNumeroFmt = cNumeroFmt + cDecimales + sToken1[1].substring(0,decimales);
       }
       else {
       	  zeros = "000000000000000000000000000000000000000";
	  cNumeroFmt = cNumeroFmt + cDecimales + zeros.substring(0,decimales);
       }
    }
    return cNumeroFmt;
}

function DelFormat (cNumero) {
    var cNumeroFmt;
    var i = 0;
    var j = 0;
	
    cNumeroFmt = "";
    for (i = cNumero.length-1; i >= 0; --i) {
       if (cNumero.substring(i,i+1) != ".") 
          cNumeroFmt = cNumero.substring(i,i+1) + cNumeroFmt;       
    }

    return cNumeroFmt;
}
	

function FormatRut (rut,dv) {
    var rutfmt;

    rutfmt = FormatNumero(rut) + "-" + dv;
    
    document.writeln (rutfmt);
}

function volverMain(paginaIni) {
    document.F2.action = paginaIni;
    document.F2.submit();
}

function Porcentaje (valor) {
   
   return FormatReal(valor,2)+"%";
}

function minimo(a,b) {
	return (a <= b) ? a : b;
}

function maximo(a,b) {
	return (a >= b) ? a : b;
}

function valorNumero (valor) {
   var sValor = valor+"";
   var i=0;
   var j=1;
   var fValor=0.0;

   if (sValor.substring(0,1) == cDecimales) sValor = "0" + sValor;
   sToken1 = sValor.split(cDecimales);
   sToken2 = sToken1[0].split(cMiles);
   
   fValor = 0.0;
   j = 1;
   for (i=sToken2.length-1; i>=0; i--) {
   	fValor = fValor + parseInt(sToken2[i])*j;
   	j*=1000;
   }   
   if (sToken1.length > 1) {
   	j=1;
   	for (i=0; i<sToken1[1].length; i++) j*=10;
   	fValor = fValor + parseFloat(sToken1[1]) / j;
   }
   
   return fValor;   
   
}

function valorAhorro (valor) {
   var sValor = valor+"";

   sValor = sValor.substring(0,sValor.length-1);

   return valorNumero (sValor);
   
}

