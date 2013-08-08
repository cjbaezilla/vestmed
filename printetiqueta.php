<?php
include("config.php");

$paso = isset($_GET['paso']) ? intval($_GET['paso']) : 0;
if ($paso == 1 || $paso == 3) {
   $cod_sty = isset ($_POST['style']) ? strtoupper($_POST['style']) : strtoupper($_GET['style']);
   $cod_sze = isset ($_POST['style']) ? strtoupper($_POST['size']) : strtoupper($_GET['size']);
   $cod_pat = isset ($_POST['style']) ? strtoupper($_POST['color']) : strtoupper($_GET['color']);
   
   $sql = " select Cod_Prd
            from   Prd, Dsg, Pat, Sze
            where  Dsg.Cod_Dsg = Prd.Cod_Dsg
            and    Dsg.Cod_Sty = '$cod_sty'
            and    Pat.Cod_Pat = Prd.Cod_Pat
            and    Pat.Key_Pat = '$cod_pat'
            and    Sze.Cod_Sze = Prd.Cod_Sze
            and    Sze.Val_Sze = '$cod_sze'"; 
    $result = mssql_query($sql, $db);
    if (($row = mssql_fetch_array($result))) $Cod_Prd = $row['Cod_Prd'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript">
        function popwindow(ventana,left,right,ancho,alto){
           popupActive = window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left='+left+',top='+right+',width='+ancho+',height='+alto)
        }
        </script>
    </head>
    <body>
        <?php
        if ($paso == 1 || $paso == 3) {
        ?>
        <form method="POST" action="printetiqueta.php">
        <div style="border: solid 1px; border-color: black; width: 7cm; height: 2.5cm;">
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; padding: 2px">
            <tr>
                <td style="text-align: left; width: 33%"><font face="Arial" size="5"><?php echo $cod_sty; ?></font></td>
                <td style="text-align: center"><font face="Arial" size="5"><?php echo $cod_sze; ?></font></td>
                <td style="text-align: right; width: 33%"><font face="Arial" size="5"><?php echo $cod_pat; ?></font></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center">
                    <font face="MW6 Code39L" size="2"><?php echo $Cod_Prd; ?></font>
                </td>
            </tr>
        </table>
        </div>
        <?php if ($paso == 1) { ?>
        <br />
        <input type="submit" name="volver" value="volver" />&nbsp;
        <input type="button" name="print" value="print" onclick="javascript:popwindow('printetiqueta.php?paso=3&style=<?php echo $cod_sty; ?>&size=<?php echo $cod_sze; ?>&color=<?php echo $cod_pat; ?>',100,100,280,120);" />
        <?php } else { ?>
        <script type="text/javascript">
            window.print();
            window.close();
        </script>
        <?php } ?>
        </form>
        <?php
        } else if ($paso == 0) {
        ?>
        <form method="POST" id="frmMain" action="printetiqueta.php?paso=1">
        <table border="0" cellspacing="0" cellpadding="0" style="width: 180px">
            <tr>
                <td>Style</td>
                <td><input type="text" size="5" name="style" value="" /></td>
            </tr>
            <tr>
                <td>Size</td>
                <td><input type="text" size="5" name="size" value="" /></td>
            </tr>
            <tr>
                <td>Color</td>
                <td><input type="text" size="5" name="color" value="" /></td>
            </tr>
            <tr><td colspan="2"><input type="submit" name="enviar" value="enviar" /></td></tr>
        </table>
        </form>
        <script type="text/javascript">
            var $j = jQuery.noConflict();
            
            $j("input:text:visible:first").focus();
        </script>
        <?php
        }
        ?>
    </body>
</html>
