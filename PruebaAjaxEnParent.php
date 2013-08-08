<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript">
	var $j = jQuery.noConflict();

        $j(document).ready
	(
            function()
            {
                $j("form#ActualizaSeccion").submit(function(){
                        $j.post("ajax-search.php",{
                                search_type: "test",
                                param_filter: ""
                        }, function(xml) {
                                RefrescarDiv(xml);
                        });
                        return false;
                });
            }
	);
        //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.

        function RefrescarDiv(xml)
        {
            var newdiv = "<div id=\"texto\" style=\"height: 100px\">Chao</div>";

            $j("#texto").replaceWith(newdiv);
        }

        function popwindow(ventana,ancho,altura){
           window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width="+ancho+",height="+altura);
        }

        function searchTest()
        {
             $j("form#ActualizaSeccion").submit();
        }
        </script>
    </head>
    <body>
        <?php
        // put your code here
        ?>
        <form id="ActualizaSeccion" name="ActualizaSeccion">
        <div id="texto" style="height: 100px">Hola</div>
        </form>
        <input type="button" onclick="popwindow('HijoAjax.php',200,100)" value="levantar ventana">
        <input type="button" onclick="searchTest()" value="test">
    </body>
</html>
