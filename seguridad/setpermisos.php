<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
        <link href="css/itunes.css" type="text/css" rel="stylesheet" />
        <title>Seguridad</title>
        <script type="text/javascript">
            function Principal() {
                var form = document.getElementById('formulario');
                
                form.action = "principal.php";
                form.submit();
            }
            
            function Logout() {
                var form = document.getElementById('formulario');
                
                form.action = "logout.php";
                form.submit();
            }
            
        </script>
    </head>
    <body>
        <?php
        $usuario = $_GET["usr"];
        ?>
        <form name="formulario" id="formulario" action="guardar.php" method="POST" onsubmit="return confirm('Confirma accesos otorgados ?')">
        <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="600px" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="45%" class="tabular">M&oacute;dulo</th>
                    <th scope="column" width="45%" class="tabular">Opci&oacute;n</th>
                    <th scope="column" width="10%" class="tabular" style="text-align: center">Acceso</th>
                </tr>
            </thead>
            <tbody>
        <?php
            $result = mssql_query("vm_seg_usr_opcmodweb '$usuario'",$db);
            while (($row = mssql_fetch_array($result))) {
        ?>
            <tr class="tabular">
                <td class="tabular"><?php echo utf8_encode($row["NomMod"]) ?></td>
                <td class="tabular"><?php echo utf8_encode($row["NomOpc"]) ?></td>
                <td class="tabular" style="text-align: center"><input type="checkbox" <?php if ($row["CodUsr"] != ' ') echo 'checked ';?>name="permiso[]" value="<?php echo $row["Id_Mod"]."_".$row["ID_Opc"]; ?>" /></td>
            </tr>
        <?php
            }
        ?>
            </tbody>
        </table>
        <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="600px" align="center">
            <tr><td align="right" style="padding-top: 10px">
                <input type="hidden" value="<?php echo $usuario ?>" name="dfusr" />
                <input type="submit" value="Aceptar" name="Aceptar" style="width: 100px;" />
                <input type="button" value="Volver" name="Volver" style="width: 100px;" onclick="Principal()" />
                <input type="button" value="Salir" name="Salir" style="width: 100px;" onclick="Logout()" />
            </td></tr>
        </table>
        </form>
    </body>
</html>
