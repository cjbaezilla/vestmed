<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("../config.php");

$p_grpprd = ok($_GET['producto']);
$p_pat = isset($_POST['dfCodPat']) ? $_POST['dfCodPat'] : "";
$p_sze = isset($_POST['dfCodSze']) ? $_POST['dfCodSze'] : "";

echo "<!--\npat=".$p_pat."\nSze=".$p_sze."\n-->\n";

$result = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
if (($row = mssql_fetch_array($result))) {
    $cod_sty = $row['style'];
    $cod_mca = $row['marca'];
    $cod_grppat = $row['Cod_GrpPat'];    
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Detalle de Producto - Vestmed Vestuario M&eacute;dico</title>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        
	<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
	<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
        
        <style type="text/css" title="currentStyle">
                @import "../DataTables-1.9.4/media/css/demo_page.css";
                @import "../DataTables-1.9.4/media/css/jquery.dataTables_themeroller.css";
                @import "../DataTables-1.9.4/examples/examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css";
        </style>
        <script type="text/javascript" language="javascript" src="../DataTables-1.9.4/media/js/jquery.dataTables.js"></script>
        
        
        <script language="javascript">
            $(function() {
                $( '#productos' ).dataTable({  
                    "aLengthMenu": [[15, 50, -1], [15, 50, "All"]],
                    "iDisplayLength" : 15,
                    "oLanguage": {      
                        "oPaginate": {        
                            "sFirst": "Primera",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior",
                            "sLast": "Ultima"
                        },
                        "sInfo": "Registros: _TOTAL_. Viendo del _START_ al _END_",
                        "sEmptyTable": "No existen registros a mostrar",
                        "sSearch": "Filtro de Busqueda:",
                        "sLengthMenu": "Mostrando _MENU_ registros por página",
                        "sInfoFiltered": "(filtrados desde _MAX_ registros)"
                    },
                    "bJQueryUI": true,
                    "sPaginationType": "full_numbers"
                });
            });
            function ReCargar()
            {
                $("#dfCodPat").val($("#codpat").val());
                $("#dfCodSze").val($("#codsze").val());                
                $("form#F1").submit();
            }
        </script>
    </head>
    <body>
    <form ID="F1" method="POST" name="F1" action="lista-precios.php?producto=<?php echo $p_grpprd; ?>">
        <table border="1" cellpading="0" cellspacing="0" align="center" width="100%" id="productos">
            <thead>
                <tr>
                    <th>Bodega</th>
                    <th>StockP</th>
                    <th>producto</th>
                    <th>Style</th>
                    <th>Pat</th>
                    <th>Sze</th>
                    <th>glosa</th>
                    <th>StockMin</th>
                    <th>StockMax</th>
                    <th>Precio</th>
                    <th>Costo</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>

        <?php
            $hoy = date('Ymd');
            //echo "sp_getstock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy', '$p_pat','$p_sze'";
            $result = mssql_query ("sp_getstock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy', '$p_pat', '$p_sze'", $db) 
                           or die ("No pudo obtener datos del STOCK");
            while (($row = mssql_fetch_array($result))) {
        ?>
                <tr>
                    <td align="center"><?php echo $row['Bodega'] ?></td>
                    <td align="center"><?php echo number_format($row['StockP'], 0, ",", ".") ?></td>
                    <td><?php echo $row['producto'] ?></td>
                    <td><?php echo $row['Cod_Sty'] ?></td>
                    <td><?php echo $row['Key_Pat'] ?></td>
                    <td><?php echo $row['Val_Sze'] ?></td>
                    <td><?php echo $row['glosa'] ?></td>
                    <td><?php echo number_format($row['StockMinimo'], 0, ",", "."); ?></td>
                    <td><?php echo number_format($row['StockMaximo'], 0, ",", "."); ?></td>
                    <td><?php echo number_format($row['precio'], 0, ",", "."); ?></td>
                    <td><?php echo number_format($row['Costo'], 0, ",", "."); ?></td>
                    <td align="center"><?php echo $row['unidad'] ?></td>
                </tr>                
        <?php                
            }
        ?>
            </tbody>
        </table>
        <input type="hidden" id="dfCodPat" name="dfCodPat" />
        <input type="hidden" id="dfCodSze" name="dfCodSze" />
    </form>
    </body>
</html>
