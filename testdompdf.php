<?php
/* Incluimos el archivo de configuracion */
require_once("dompdf/dompdf_config.inc.php");

$html = '<html><head>
<title>Prueba 2</title>
<style type="text/css">
body{
margin: 15px 40px;
}
table th{
background-color:#333333;
color:#CCCCCC;
}
table{
border-collapse:collapse;
border:#c0c0c0 solid 1px;
}
table td{
text-align:center;
}
p{
text-align:justify;
}
h2{
color:#003366;
border-bottom:#003366 solid 3px;
}
</style>
</head>
<body><h2>Prueba 2</h2>
<br /><br />
<table border="0" align="center" >
  <tr><th>header1</td><th>header 2 </td><th>header3</td></tr>
  <tr><td>1</td><td>2</td><td>2</td></tr>
  <tr><td>3</td><td>5</td><td>5</td></tr>
  <tr><td>6</td><td>5</td><td>8</td></tr>
  <tr><td>4</td><td>8</td><td>5</td></tr>
  <tr><td>6</td><td>8</td><td>9</td></tr>
  <tr><td>1</td><td>2</td><td>3</td></tr>
</table>
<br />
<p>Lorem ipsum dolor sit amet, consectetuer
adipiscing elit.  Phasellus nec leo. Pellentesque
ac diam quis urna elementum dignissim.
Maecenas sit amet risus. Aliquam orci. Vivamus
lacinia mauris ac ipsum. Nullam sagittis odio non
mi. Praesent dictum arcu vel nisi. Praesent urna
dolor, aliquet ut, pulvinar nec, eleifend vel, nisi.
Sed eget odio imperdiet dolor aliquam cursus.
Ut facilisis auctor est. Nam dictum sagittis orci.
Praesent dapibus tempus dui. Donec aliquet,
risus at vulputate dictum, dui eros congue elit,
quis hendrerit elit purus et ligula. Curabitur
condimentum. Nunc semper dolor laoreet purus.
Fusce urna nunc, scelerisque eu, sagittis sit
amet, pellentesque malesuada, magna.
Vestibulum magna mauris, rutrum quis,
fringilla id, varius a, lectus.</p>
</body>
</html>
';

/*creamos un nuevo objeto */
$dompdf = new DOMPDF();
/*Con el método "load_html" cargamos nuestro código HTML */
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("prueba.pdf");

?>
