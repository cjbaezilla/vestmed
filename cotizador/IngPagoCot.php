<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$cod_cot = 0;
$status = "";
$id_row = 0;
if (isset($_GET['cot'])) $cod_cot = intval($_GET['cot']);
if (isset($_GET['id_row'])) $id_row = intval($_GET['id_row']);
if (isset($_GET['status'])) $status = $_GET['status'];
if (isset($_POST['montoCmp'])) $montoCpt = $_POST['montoCmp'];
//echo $montoCpt;
if ($status == "ok"){
    $archivo = $_FILES['documento']['name'];
    if ($archivo != "") {
        for ($i = strlen($archivo)-1; $i > 0; $i--)
            if (substr($archivo, $i, 1) == ".") break;

        $ext = substr($archivo, $i, strlen($archivo));
        $archivo_adj = "archivo".$ext;
        $result = mssql_query("vm_getfolio 'ADJ'");

	if (($row = mssql_fetch_array($result)))
		$archivo_adj = "adjunto".sprintf("%06d", $row['Tbl_fol']).$ext;
        $fileupload = '../'.$pathadjuntos.$archivo_adj;
        //$fileupload = $pathadjuntos.$archivo;
        if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
           echo $_FILES['documento']['tmp_name']."<BR>".$fileupload."<BR>";
           echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
           exit(0);
        }
    }
    try{
            $ret=mssql_query("sp_u_adjpgoodc $cod_cot, '$archivo_adj', $montoCpt, $id_row",$db);
    }catch (Exception $exc) {
            echo $exc->getMessage();
    }
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/fngenerales.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script>
	function check_file(){
		if ($('#documento').val()=='' || $('#montoCmp').val()==''){
			alert('Debe indicar el comprobante a adjuntar e ingresar el valor del comprobante.');
			return false			
		}else{
			return true
		}

	}
        
    function ActualizaPadre()
    {
        parent.opener.recarga_grid_fromPopUp();
		//parent.opener.recarga_grid();
        window.close();
    }
        
</script>
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
<div style="overflow:auto;">
<form ID="F2" method="POST" name="F2" action="IngPagoCot.php?cot=<?php echo $cod_cot; ?>&id_row=<?php echo $id_row;?>&status=ok" onsubmit="return check_file()" enctype="multipart/form-data">
    <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
    <tr>
	<td width="100%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid; border-left: goldenrod 1px solid;">
	Selecciones su Banco:
	<select id="Bco" name="Bco" class="textfieldv2">
	<option selected value="_NONE">Seleccione Banco</option>
	<?php //Seleccionar las ciudades
	$sp = mssql_query("vm_bco_s",$db);
	while($row = mssql_fetch_array($sp))
	{
		?>
		<option value="<?php echo utf8_encode($row['Nom_Bco']) ?>"><?php echo utf8_encode($row['Nom_Bco']) ?></option>
		<?php
	}
	?>
        </select>
	</td>
    </tr>
    <tr>
	<td width="100%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	Comprobante <span>
                        <input class="file-contacto" type="file" name="documento" id="documento" size="28" onchange="fichero.value = this.value"/>
                        <input type="hidden" name="fichero" id="fichero"/> &nbsp; PDF, doc, docx, xls, xlsx, jpg, gif
                    </span>
	</td>
    </tr>
    <tr>
	<td width="100%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	Monto Transferencia <span>
                        <input class="file-textfieldv2" type="text" name="montoCmp" id="montoCmp" size="10" onKeyPress="return SoloNumeros(event)" />
                    </span>
	</td>
    </tr>
    <tr>
	<td width="100%" style="text-align: right; padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
                <input type="submit" class="btn" name="enviar_arch" value="Enviar" />
                <input type="button" class="btn" value="Salir" onclick="javascript: window.close()" />
	</td>
    </tr>
    </table>
    </form>
</div>
    <script type="text/javascript">
        <?php if ($status == "ok") { ?>
        ActualizaPadre();
        <?php } ?>

</script>
</body>
</html>
