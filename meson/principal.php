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
        <title>Consulta Histórico Compras</title>
        <script type="text/javascript">
            function Send(caso) {
                var form = document.getElementById('consulta');
                
                switch (caso) {
                    case 1:
                        form.action = "cnabyrut.php";
                        break;
                    case 2:
                        form.action = "cnabyname.php";        
                        break;
                    case 3:
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
                            <input type="button" value="B&uacute;squeda por RUT" name="btnRut" style="width: 200px" onclick="Send(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" value="B&uacute;squeda por Nombre" name="btnNombre" style="width: 200px" onclick="Send(2)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" value="Salir" name="btnSalir" style="width: 200px" onclick="Send(3)" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </body>
</html>
