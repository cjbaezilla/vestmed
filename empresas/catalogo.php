<?php

ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$Cod_Nvt = $_POST['dfCodNtaVta'];
$Cod_Clt = $_POST['dfCodCltVta'];

$query = "vm_cli_s ".$Cod_Clt;
$result = mssql_query($query, $db) or die ('error en sql (1001)<br>'.$query);
if (($row = mssql_fetch_array($result))) $Rut_Clt = $row['Num_Doc'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Usuarios Nota de Venta</title>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
        <link href="css/itunes.css" type="text/css" rel="stylesheet" />
        
        <!-- Lytebox Includes //-->
        <script type="text/javascript" src="../lytebox/lytebox.js"></script>
        <link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
        <!-- Lytebox Includes //-->
        
	<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
	<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
        
        <script type="text/javascript">
            function AgregarUsr() {
                $("form#frmAgregar").attr('action', 'usuarios.php');
                $("form#frmAgregar").submit();
            }
            
            function SiguientePaso() {
                $("form#frmAgregar").attr('action', 'facturacion.php');
                $("form#frmAgregar").submit();
            }
        </script>
    </head>
    <body>
        <div style="padding: 50px; text-align: center">
            Aquí va el catalago similar al actual pero sólo con los productos del kit
        </div>
        <div style="padding: 50px; text-align: right">
            <form name="frmAgregar" id="frmAgregar" action="kit.php" method="POST">
                <input type="hidden" name="dfCodNtaVta" id="dfCodNtaVta" value="<?php echo $Cod_Nvt ?>" />
                <input type="hidden" name="dfCodCltVta" id="dfCodCltVta" value="<?php echo $Cod_Clt ?>" />
                <input type="button" value="Agregar Usuario" name="AgregarUsr" onclick="AgregarUsr();"/>
                <input type="button" value="Continuar" name="Continuar" onclick="SiguientePaso();"/>
                <input type="submit" value="Volver" name="volver" />
            </form>        
        </div>
    </body>
</html>
