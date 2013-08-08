<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Incluir la clase que contiene el método a llamar
require_once('Calculadora.php');
// Crear servidor de SOAP
$server = new SoapServer(null, // No utilizar WSDL
                         array('uri' => 'urn:webservices') // Se debe especificar el URI
);
// Asignar la clase al servicio
$server->setClass('Calculadora');
// Atender los llamados al webservice
$server->handle();

?>
