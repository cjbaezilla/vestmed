	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=930,height="+altura);
	}
	
	function BuscarCliente(contexto) {
		//popwindow("busqueda.php?contexto="+contexto,600)
		f1.action = "escritorio_bus.php";
		f1.submit();
   	}
	
	function NuevoCliente(contexto) {
		popwindow("registrarse.php?contexto="+contexto,600)
	}
			
	function Editar() {
		popwindow("busqueda.php?contexto=mnu",600)
	}	
	
	function Historico() {
		//popwindow("busqueda.php?contexto=mnu",600)
		alert ("Debe seleccionar un cliente ...");
	}
	
	function mnuCliente(contexto) {
		f1.action = "escritorio_cot.php?opc=clt";
		f1.submit();
	}
	
	function mnuVestmed() {
		f1.action = "escritorio_cot.php?opc=";
		f1.submit();
	}
	
	function mnuMensajes() {
		f1.action = "mismensajes.php";
		f1.submit();
	}
	
	function volver(numdoc) {
		f1.action = "escritorio_edtclt.php?clt="+numdoc;
		f1.submit();
	}
