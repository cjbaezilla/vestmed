<?php 
    include("../config.php");

    $EsInstitucional = false;
    $doc_id = 1;
    $ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
    if (isset($_POST['dfRutClt']) && trim($_POST['dfRutClt']) != "") {  
        $RutClt  = ok($_POST['dfRutClt']);
        $Cod_Suc = ok($_POST['dfCodSuc']);

        $result = mssql_query ("vm_s_per_tipdoc $doc_id, '$RutClt'", $db)
                                        or die ("No se pudo leer datos del Cliente");
        if (($row = mssql_fetch_array($result))) {
                $Cod_Per = $row['Cod_Per'];
                $Cod_Clt = $row['Cod_Clt'];

                if ($Cod_Clt == null) {
                        $result = mssql_query("vm_getfolio 'CLT'", $db);
                        if (($row = mssql_fetch_array($result))) {
                                $Cod_Clt = $row['Tbl_fol'];
                                $result = mssql_query ("vm_clt_i $Cod_Per, '$Cod_Clt'", $db) or die ("No se pudo crear Cliente");
                        }
                }

                foreach ($_POST as $key => $value) {
                        //echo $key." --> ".$value."<BR>";
                        if ($key == "dfNomSuc")      $NomSuc    = str_replace("\'", "''", strtoupper(utf8_decode($value)));
                        if ($key == "dfDireccion")   $Direccion = str_replace("\'", "''", strtoupper(utf8_decode($value)));
                        if ($key == "dfTelefonoSuc") $Telefono  = $value;
                        if ($key == "dfFaxSuc")      $Fax       = $value;
                        if ($key == "codcmn") 	     $Cod_Cmn   = intval($value);
                        if ($key == "codcdd") 	     $Cod_Cdd   = intval($value);
                }

                if ($Cod_Suc == 0) {
                        $result = mssql_query("vm_getfolio 'SUC'", $db);
                        if (($row = mssql_fetch_array($result))) {
                            $Cod_Suc = $row['Tbl_fol'];
                            $result = mssql_query("vm_suc_i $Cod_Suc, $Cod_Clt, '$NomSuc', '$Direccion',
                                                            $Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax', 0", $db)
                                                                      or die ("No se pudo ingresar datos de la Sucursal");

                            $result = mssql_query("vm_usrweb_ctt_s $Cod_Per, $Cod_Clt", $db)
                                                                      or die ("No se pudo leer datos del contacto");
                            if (($row = mssql_fetch_array($result))) {
                                $Cel_Ctt = $row['Cel_Ctt'];
                                $Mail_Ctt = $row['Mail_Ctt'];
                                $Cgo_Ctt = $row['Cgo_Ctt'];
                                $Cod_TipCtt = $row['Cod_TipCtt'];

                                $result = mssql_query("vm_ctt_i $Cod_Clt, $Cod_Suc, $Cod_Per, '$Telefono', '$Cel_Ctt', '$Mail_Ctt', '$Cgo_Ctt', NULL, $Cod_TipCtt", $db)
                                                                          or die ("No se pudo ingresar datos del Contacto");
                            }
                        }
                }
                else
                        $result = mssql_query("vm_suc_u $Cod_Suc, $Cod_Clt, '$NomSuc', '$Direccion',
                                                        $Cod_Cmn, $Cod_Cdd, '$Telefono', '$Fax'", $db)
                                                                  or die ("No se pudo actualizar datos de la Sucursal");
	}
    }
	
    mssql_close ($db);
    if ($ret == 0)
        //header("Location: registrarse.php?accion=close&clt=".$RutClt."&xis=1&suc=".$Cod_Suc);
        header("Location: editar.php?accion=close&clt=".$RutClt."&xis=1&suc=".$Cod_Suc);
    else if ($ret == 1)
        header("Location: registrarse.php?accion=closeedt&clt=".$RutClt."&xis=1&suc=".$Cod_Suc);
    else if ($ret == 2)
        header("Location: registrar_suc.php?accion=closeing");

?>
