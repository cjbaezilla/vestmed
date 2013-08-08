<?php
ini_set('display_errors', '0');
session_start();
include("config.php");

$file = $_GET['archivo'];
?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Vestmed</title>
    </head>
    <body>
        <table border="0" width="100%">
                <tr>
                        <td width="100%" height="425px">
                            <iframe style="width: 100%; height: 100%" src="<?php echo $pathadjuntos.$file; ?>" name="adjunto"></iframe>
                        </td>
                </tr>
                <tr>
                <td>
                    <input type="button" value="Cerrar" onclick="javascript:window.close()" >      
                </td>
                </tr>
        </table>
    </body>
</html>
