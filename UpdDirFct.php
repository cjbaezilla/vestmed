<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$cod_cot = 0;
if (isset($_GET['cot'])) $cod_cot = intval($_GET['cot']);

if ($cod_cot > 0) {
    $result = mssql_query("vm_s_cothdr $cod_cot",$db);
    if (($row = mssql_fetch_array($result))) {
            $num_cot = $row['Num_Cot'];
            $fec_cot = $row['Fec_Cot'];
            $tip_cnl = $row['Tip_Cnl'];
            $cod_odc = $row['Cod_Odc'];
            $cod_clt = $row['Cod_Clt'];
            $cod_sucfct = $row['Cod_SucFct'];
            $num_doc    = $row['Num_Doc'];
            $cod_suc    = $row['Cod_Suc'];

    }

    if (isset($_GET['trn'])) {
        $cod_trn    = intval(ok($_GET['trn']));
        $cod_sucfct = intval(ok($_POST['rbSucFct']));
        if ($cod_trn == 100) {
            //print "vm_u_sucfct_cot $cod_clt, $cod_cot, $cod_sucfct";
            //$query = "vm_u_sucfct_cot $cod_clt, $cod_cot, $cod_sucfct";
            $result = mssql_query("vm_u_sucfct_cot $cod_clt, $cod_cot, $cod_sucfct", $db);
        }
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="Include/estilos.css" type="text/css" rel="stylesheet" />
    <link href="css/layout.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
        function ActualizaPadre()
        {
            parent.opener.ActualizarDirFac();
            //window.close();
        }

		function popwindow2(ventana,altura){
		   window.open(ventana,"NewDir","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
		}		
		function NuevaSuc(numdoc) {
			popwindow2("cotizador/registrar_suc.php?clt="+numdoc+"&xis=1&acc=newsuc&ret=2&page=refresh",600);
			//popwindow2("cotizador/registrar_suc.php?clt="+numdoc+"&xis=1&acc=newsuc&ret=2",600);
		}		
    </script>
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
<div style="height:170px; overflow:auto;">
<form ID="F1" method="post" name="F1" action="<?php echo $_SERVER['PHP_SELF'] ?>?cot=<?php echo $cod_cot; ?>&trn=100">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1">
<?php
        $j = 1;
        $iTotPrd1 = 1;
        $result = mssql_query("vm_suc_s $cod_clt", $db);
        while ($row = mssql_fetch_array($result)) {
?>
            <tr>
               <td style="TEXT-ALIGN: center" width="3%">
               <input id="rbSucFct" name="rbSucFct" type="radio" style="border:none" value="<?php echo $row['Cod_Suc'] ?>"  <?php if ($cod_sucfct == $row['Cod_Suc']) echo "checked" ?> /></td>
                <td style="TEXT-ALIGN: left; PADDING-LEFT:5px"><?php echo utf8_encode($row['Dir_Suc']); ?> (<?php echo utf8_encode($row['Nom_Cdd']); ?>)</td>
            </tr>
<?php
            $j = 1 - $j;
            $iTotPrd1++;
        }
        mssql_free_result($result);
?>
        <tr>
	        <td colspan="2" style="PADDING-TOP: 10px; PADDING-BOTTOM: 3px; TEXT-ALIGN: left" class="label_top">
				<input type="button" class="btn2" value="Agregar Direcci&oacute;n" onclick="NuevaSuc('<?php echo $num_doc; ?>');" />
	            <input type="submit" class="btn2" value="Actualizar Direcci&oacute;n" />
	            <input type="button" class="btn" value="Salir" onclick="javascript: window.close()" />
	        </td>
        </tr>
</table>
</form>
</div>
<div>
<!--input type="button" style="float:left;" class="btn" value="Continuar" onclick="parent.location.href='catalogo.php'" /-->
<!--input type="button" style="float:left;" class="btn" id="btn_check" value="CheckOut" onclick="parent.location.href='detalle-cotizacion.php'" /-->
<!---<input type="button" style="float:right;" class="btn" value="Volver" onclick="location.href=''" /-->
</div>
    <script type="text/javascript">
	var f1;

	f1 = document.F1;

        <?php if ($cod_trn == 100) { ?>
        ActualizaPadre();
        <?php } ?>

</script>
</body>
</html>
