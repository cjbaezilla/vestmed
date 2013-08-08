<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link href="css/base.css" type="text/css" rel="stylesheet" />
        <title>Seguridad</title>
        <script language="JavaScript" src="../Include/ValidarDataInput.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/SoloNumeros.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/validarRut.js" type="text/javascript" ></script>
        <script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript">
            var $j = jQuery.noConflict();
            $j(document).ready(function(){
                $j("form#consulta").submit(function(){
                    $j.post("../ajax-search-per.php",{
                            search_type: "usr",
                            param_filter: $j("#IdUsr").val(),
                            param_clave: $j("#PwdUsr").val()
                        }, function(xml) {
                        ValidarUser(xml);
                    });
                    return false;
                });
            });
            
            function ValidarUser(xml) {
                var estado = 0;
                $j("filter",xml).each(
                    function(id) {
                        filter=$j("filter",xml).get(id);
                        estado = $j("estado",filter).text();
                    }
		);
                if (estado == 0) {
                    alert ('Usuario No existe');
                    return;
                }
                if (estado == 2) {
                    alert ('Clave incorrecta');
                    return;
                }
                else {
                    $j("permiso",xml).each(
                        function(id) {
                            permiso=$j("permiso",xml).get(id);
                            modulo = $j("modulo",permiso).text();
                            opcion = $j("opcion",permiso).text();
                            //alert(modulo + " - " + opcion);
                            if (parseInt(modulo) == 10 && parseInt(opcion) == 1) estado = 99;
                        }
                    );
                    if (estado != 99) {
                        alert ('Usuario sin permiso para acceder al M\u00f3dulo de Seguridad');
                        return;
                    }
                }
                $j("#usuario").val($j("#IdUsr").val());
                $j("form#seguridad").submit();                
            }
            
            function CheckData() {
                var form = document.getElementById('consulta');
                if (form.IdUsr.value == "" || form.PwdUsr.value == "") {
                    alert ('Favor ingrese su Usuario y Clave');
                    form.IdUsr.focus();
                    return false;
                }
                $j("form#consulta").submit();                
            }
            
            function Home() {
                var form = document.getElementById('consulta');
                form.action = "index.php";
                form.submit();
            }
        </script>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" border="0" width="400px" align="center" valign="middle">
        <tr>
            <td valign="top"><img src="../images/box/1.gif" alt="" /></td>
            <td valign="top" bgcolor="#f2f2f2"><img src="../images/box/blank.gif" width="1" height="1" alt="" /></td>
            <td valign="top" style="width: 25px"><img src="../images/box/3.gif" alt="" /></td>
        </tr>

        <tr>
            <td valign="top" style="background-image: url(../images/box/5.gif); background-repeat: repeat-y">
                <img src="../images/box/blank.gif" width="1" height="1" alt="" /></td>
            <td valign="top" bgcolor="#f2f2f2" width="100%">
                <form id="consulta" method="POST">                
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" style="text-align: center;">
                        <tr>
                            <td class="top-titulo" width="100%">Login</td>
                        </tr>
                        <tr>
                        <td>
                        <table width="80%" cellpadding="5" cellspacing="1" align="center">
                            <tr>
                                <td colspan="2" style="height: 3px;">
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"
                                    style="padding-top: 6px; text-align: right; padding-right: 3px; vertical-align: middle;" 
                                    width="100">
                                    Usuario</td>
                                <td align="left">
                                    <input name="IdUsr" id="IdUsr" tabIndex="1" size="15" />
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" 
                                    style="padding-top: 6px; text-align: right; padding-right: 3px; vertical-align: middle;" 
                                    width="100">
                                    Clave</td>
                                <td align="left">
                                    <input type="password" name="PwdUsr" id="PwdUsr" value="" size="15" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 10px; text-align: right;" valign="top">
                                    <input type="button" class="btn" value="Consultar" name="enviar" onclick="CheckData()" style="width: 100px" />
                                    <input type="reset" value="Limpiar" class="btn" name="limpiar" style="width: 100px" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 3px; text-align: left;" valign="top">
                                    <span id="alerta_error"></span>
                                </td>
                            </tr>
                        </table>
                        </td>
                        </tr>
                    </table>
                </form>
                <form id="seguridad" method="POST" action="principal.php">
                        <input type="hidden" name="usuario" id="usuario" />
                </form>
                <script type="text/javascript">
                    var IdUsr = document.getElementById('IdUsr');
                    IdUsr.focus();
                </script>
            </td>
            <td valign="top" style="background-image: url(../images/box/7.gif); background-repeat: repeat-y;">
                <img src="../images/box/blank.gif" width="1" height="1" alt="" /></td>
        </tr>

        <tr>
            <td valign="top" height="1"><img src="../images/box/2.gif" alt="" /></td>
            <td valign="top" height="1" style="background-image: url(../images/box/6.gif); background-repeat: repeat-x;">
                <img src="../images/box/blank.gif" width="1" height="1" alt="" />
            </td>
            <td valign="top" height="1"><img src="../images/box/4.gif" alt="" /></td>
        </tr>
        </table>    
    </body>
</html>
