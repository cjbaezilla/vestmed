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
        <title>Ingreso de Usuarios</title>
        <script type="text/javascript">
            function Send(caso) {
                var form = document.getElementById('consulta');
                form.tipovta.value = caso;
                switch (caso) {
                    case 1:
                    case 2:
                    case 3:
                        form.action = "usuarios.php";
                        break;
                    case 4:
                        form.action = "logout.php";
                        break;
                }
                form.submit();
            }
        </script>
    </head>
    <body>
        <form id="consulta" method="POST">
            <table border="0" width="300" align="center" cellpadding="3px" cellspacing="3">
                <tbody>
                    <tr>
                        <td>
                            <input type="button" value="Venta Mayorista" name="btnMayorista" style="width: 200px" onclick="Send(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" value="Venta Retail" name="btnRetail" style="width: 200px" onclick="Send(2)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" value="Venta Web" name="btnWeb" style="width: 200px" onclick="Send(3)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" value="Salir" name="btnSalir" style="width: 200px" onclick="Send(4)" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" id="tipovta" name="tipovta" />
        </form>
    </body>
</html>
