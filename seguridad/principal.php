<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
        <link href="css/itunes.css" type="text/css" rel="stylesheet" />
        <title>Seguridad</title>
    </head>
    <body>
        <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="600px" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th scope="column" width="20%" class="tabular">Usuario</th>
                    <th scope="column" width="75%" class="tabular">Nombre</th>
                    <th scope="column" width="5%" class="tabular">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
        <?php
            $result = mssql_query("vm_sel_usrseg",$db);
            while (($row = mssql_fetch_array($result))) {
        ?>
            <tr class="tabular">
                <td class="tabular"><?php echo utf8_encode($row["Usr"]) ?></td>
                <td class="tabular"><?php echo utf8_encode($row["DESCRIPCION"]) ?></td>
                <td class="tabular" style="text-align: center"><a href="setpermisos.php?usr=<?php echo $row["Usr"]; ?>"><img src="../images/lock.png" alt="" title="" /></a></td>
            </tr>
        <?php
            }
        ?>
            </tbody>
        </table>
        <table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="600px" align="center">
            <tr><td align="right" style="padding-top: 10px">
            <form name="salida" action="logout.php" method="POST">
                <input type="submit" value="Salir" name="Salir" style="width: 100px;" />
            </form>
            </td></tr>
        </table>
    </body>
</html>
