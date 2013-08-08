<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

if (isset($_GET['cod_odc']))
{
    $Cod_Odc = intval(ok($_GET['cod_odc']));
}

$refrescapadre = false;
if (isset($_GET['accion']))
    if ($_GET['accion'] == "abono") {
        $MtoAbn = $_POST['valAbono'];
	$ret=mssql_query("sp_u_pgoodc $Cod_Odc, $MtoAbn", $db);
        $refrescapadre=true;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<link href="../css/layout.css" type="text/css" rel="stylesheet" />
	<link href="../css/clearfix.css" type="text/css" rel="stylesheet" />
	<!-- In head section we should include the style sheet for the grid -->
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqGrid/themes/ui.jqgrid.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqGrid/themes/ui.multiselect.css" />
	<!--<link rel="stylesheet" type="text/css" media="screen" href="js/jqGrid/themes/redmond/jquery-ui-1.8.12.custom.css" />-->
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqGrid/themes/vestmed/jquery-ui-1.8.13.custom.css" />
	
	<!-- Of course we should load the jquery library -->
	<!-- Cargar en este orden -->
	<!-- 1 -->
	<script src="js/jqGrid/jquery-1.5.2.min.js" type="text/javascript"></script>
	<script src="js/jquery.popupWindow.js" type="text/javascript"></script>
	<script src="js/jqGrid/jqDnR.js" type="text/javascript"></script>
	<script src="js/jqGrid/jqModal.js" type="text/javascript"></script>
	<!-- 2 -->
	<script src="js/jqGrid/i18n/grid.locale-es.js" type="text/javascript" charset="utf-8"></script>
	<!-- 3 -->
	<script src="js/jqGrid/jquery.jqGrid.min.js" type="text/javascript"></script>
	<!-- JS QUE SIRVE PARA EDITAR LAS CELDAS DE LA GRILLA -->
	<script src="js/jqGrid/rowedex3.js" type="text/javascript"> </script> 
	
	<!-- Jquery QUE SIRVE PARA Validar Ruts Chilenos -->
	<script src="js/jqGrid/jquery.Rut.js" type="text/javascript"> </script> 	
    
	<title></title>
	<script type="text/javascript">
	
        // We use a document ready jquery function.
        jQuery(document).ready(function(){
        jQuery("#editgrid").jqGrid({
            // the url parameter tells from where to get the data from server
            // adding ?nd='+new Date().getTime() prevent IE caching
            url:"xmlcuenta.php?cod_odc=<?php echo $Cod_Odc;?>", //Indica origen de datos.
            // datatype parameter defines the format of data returned from the server
            // in this case we use a JSON data
            datatype: "xml",

            //****** RESCATO VALOR DE XML DESDE UNA VARIABLE *********
            ////datatype: "xmlstring",
            ////datastr: myVar,
            //****** RESCATO VALOR DE XML DESDE UNA VARIABLE *********
            // colNames parameter is a array in which we describe the names
            // in the columns. This is the text that apper in the head of the grid.
            colNames:['Fecha Movimiento','Debe','Haber','Saldo','comprobante'],
            // colModel array describes the model of the column.
            // name is the name of the column,
            // index is the name passed to the server to sort data
            // note that we can pass here nubers too.
            // width is the width of the column
            // align is the align of the column (default is left)
            // sortable defines if this column can be sorted (default true)
            colModel:[ {name:'fecha',index:'fecha', editrules:{required:true},sortable:true, width:150,editable:false,editoptions:{size:10}},
                       {name:'debe',index:'debe', editrules:{required:true,number:true},sortable:true, width:150,editable:false,editoptions:{size:10}},
                       {name:'haber',index:'haber', editrules:{required:true,number:true}, sortable:true, width:150,editable:true,editoptions:{size:10,dataEvents: [
                          {
                              type: 'keyup',
                              fn: function(e) {
                                      var datrow = jQuery('#editgrid').getRowData(lastsel);
                                      var monto = parseFloat(datrow.debe);
                                      var real = parseFloat($(this).val());
                                      var difer = (monto - real);
                                      jQuery('#editgrid').setCell(lastsel,'diferencia',difer);
                              }
                          }
                          ]}},
                       {name:'diferencia',index:'diferencia', editrules:{required:true},sortable:true, width:150,editable:false,editoptions:{size:10}},
                       {name:'comprobante',index:'comprobante',sortable:false,editable:false,formatter:'showlink',align:'center',editrules:{required: false},formatoptions:{baseLinkUrl:'#'}},                        
                     ],
            // pager parameter define that we want to use a pager bar
            // in this case this must be a valid html element.
            // note that the pager can have a position where you want
            pager: jQuery('#pager'),
            // rowNum parameter describes how many records we want to
            // view in the grid. We use this in example.php to return
            // the needed data.
            rowNum:10,
            // rowList parameter construct a select box element in the pager
            //in wich we can change the number of the visible rows
            rowList:[5,10,20,30],
            // sortname sets the initial sorting column. Can be a name or number.
            // this parameter is added to the url
            sortname: 'id',
            //viewrecords defines the view the total records from the query in the pager
            //bar. The related tag is: records in xml or json definitions.
            viewrecords: true,
            //sets the sorting order. Default is asc. This parameter is added to the url
            sortorder: "desc",
            caption: "Movimientos Cuenta Pagos",
                //imgpath:"themes\redmond\images",
                editurl:"someurl.php", //dummy file.
                height:310,
                width:800,
                onSelectRow: function(id){ 
                        if(id && id!==lastsel){ 
                                jQuery('#editgrid').jqGrid('restoreRow',lastsel); 
                                jQuery('#editgrid').jqGrid('editRow',id,true); 
                                lastsel=id; } 
                        },
                loadComplete: function() {
                    var myGrid = $("#editgrid");
                    var ids = myGrid.getDataIDs();
                    for (var i = 0, idCount = ids.length; i < idCount; i++) {
                        $("#"+ids[i]+" a",myGrid[0]).click(function(e) {
                            var hash=e.currentTarget.hash;// string like "#?id=0"
                            if (hash.substring(0,5) === '#?id=') {
                                var id = hash.substring(5,hash.length);
                                var text = this.textContent || this.innerText;
                                var datrow = jQuery('#editgrid').getRowData(id);
                                if (datrow.comprobante=='Ingresar Comprobante'){
                                    agregarComprobante(datrow.comprobante + '?cot=<?php echo $Cod_Odc;?>&id_row='+id);
                                    recarga_grid();
                                }else{
                                    ver_comprobante(datrow.comprobante);	
                                }

                            }
                            e.preventDefault();
                        });}}
                }).navGrid('#pager',
                        {search:false,edit:false,add:false,del:false});

	        $("form#F1").submit(function(){
	            $.post("AbonoOdc.php",{
	                    param_filter: jQuery("#cod_odc").val(),
	                    param_abn: jQuery("#valAbono").val()
	            }, function() {
	                    recarga_grid();
	            });
                    return false;
                });
                
                $("#bOk").click(function(){ 
                        if (jQuery('#valAbono').val() == "") {
                            alert("Debe ingresar el monto del abono.");
                        }
                        else if (jQuery('#valPass').val() == "") {
                            alert("Debe ingresar la clave para poder ingresar el abono.");
                        } else if (!validaPass(jQuery('#valPass').val(),'<?php echo $passAprobacionPago; ?>')) {
                            alert("Clave incorrecta.");
                        } else {
                            //$.post("agregaAbono.php?cod_odc=<?php echo $Cod_Odc; ?>&accion=abono");
                            $("form#F1").submit();                                                        
                        }                
                });
			 
                        
            function validaPass(passOK, passDigi){
                    if (passOK===passDigi){
                            return true;
                    }else {
                            return false;
                    }

            }
				
            function popwindow(ventana,left,right,ancho,alto){
               window.open(ventana,"PagoAdjunto",'toolbar=0,location=0,scrollbars=yes,resizable=1,left='+left+',top='+right+',width='+ancho+',height='+alto)
            }
			
            function ver_comprobante(comp) {
                popwindow('../adjuntos/' + comp,140,140,600,480);
            }
			
            function agregarComprobante(comp) {
                popwindow(comp,140,140,600,180);
            }

            function recarga_grid(){
                //alert("recarga_grid");
                parent.opener.actualizar_Pago();
                $('#editgrid').setGridParam({url:"xmlcuenta.php?cod_odc=<?php echo $Cod_Odc;?>"}); 
                $("#editgrid").trigger("reloadGrid");
                $("#valAbono").val('');
                $("#valPass").val('');
            }

            function recarga_grid_fromPopUp(){
                //alert("recarga_grid");
                $('#editgrid').setGridParam({url:"xmlcuenta.php?cod_odc=<?php echo $Cod_Odc;?>"}); 
                $("#editgrid").trigger("reloadGrid");
                //parent.opener.
            }
            

	}); 		
	
	</script>
	<script type="text/javascript">
            function okData(form) {
                if (form.valAbono.value == "") {
                    alert("favor ingrese el monto del abono");
                    return false;
                }
                if (form.valPass.value == "") {
                    alert("favor ingrese la clave de supervision");
                    return false;
                }
                if (form.valPass.value != '<?php echo $passAprobacionPago; ?>') {
                    alert("clave incorrecta");
                    return false;
                }
                return true;
            }
	</script>

</head>
<style>
html, body {
    margin: 0;		/* Remove body margin/padding */
    padding: 0;
    overflow: hidden;	/* Remove scroll bars on browser window */	
    font-size: 72%;
}
</style>
<body>
	<div class="recargar">
	</div>
	<table id="editgrid" >
	</table> 
	<div id="pager"></div>
        
        <form id="F1" name="F1" method="POST" action="validaPago.php?cod_odc=<?php echo $Cod_Odc; ?>&accion=abono">
        <table id="datos" width="100%">
            <tr>
                <td>Monto Abono:</td>
                <td><input type="text" id="valAbono" name="valAbono" class="textfield1" width="100px"></input></td>
                <td>Clave:</td>
                <td><input type="password" id="valPass" name="valPass" class="textfield1" width="100px"></input></td>
                <td style="text-align: right">
                    <input type="hidden" id="cod_odc" value="<?php echo $Cod_Odc; ?>" />
                    <input type="button" id="bOk" value="Aceptar" class="btn"/>&nbsp;
                    <input type="button" id="bSalir" value="Salir" class = "btn" onclick="javascript: window.close()" />
                </td>
            </tr>
        </table>                   
        </form>
<?php if ($ActualizaPadre) { ?>        
<script type="text/javascript">
parent.opener.actualizar_Pago();    
</script>
<?php } ?>
</body>
</html>

