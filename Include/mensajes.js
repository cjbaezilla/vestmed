	var $j = jQuery.noConflict();
    $j(document).ready
	(
		//$j(":input:first").focus();
		
		function()
		{
	        $j("form#searchMsgCot").submit(function(){
				$j.post("ajax-search.php",{
					search_type: "msgcot",
					param_clt: $j("#cod_clt").val(),
					param_cot: $j("#last_cot").val(),
					param_bus: $j("#tipo_bus_cot").val(),
					param_ord: $j("#orden").val()
				}, function(xml) {
					listMsgCot(xml);
				});
				return false;
		    });
			
	        $j("form#searchMsgCtt").submit(function(){
				$j.post("ajax-search.php",{
					search_type: "msgctt",
					param_clt: $j("#cod_clt").val(),
					param_fol: $j("#last_folio").val(),
					param_bus: $j("#tipo_bus_folio").val(),
					param_ord: $j("#ordenfolio").val()
				}, function(xml) {
					listMsgCtt(xml);
				});
				return false;
		    });
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
	function listMsgCot (xml)
	{
		var tot_filas = 0;
		var atrascot = 0;
		
		options="<TABLE BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" width=\"630\" ALIGN=\"center\" id=\"tblMsgCot\">\n";
		if ($j("#tipo_bus_cot").val() == "P")
		   options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:todoscot()\">TODOS</a> | PENDIENTES</td></tr>\n";
		else
		   options+="<tr><td colspan=\"7\" align=\"right\">TODOS | <a href=\"javascript:pendientescot()\">PENDIENTES</a></td></tr>\n";
        options+="<TR>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Fecha</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Grupo</TD>\n";
		options+="<TD width=\"40%\" VALIGN=\"TOP\" ALIGN=\"left\" class=\"titulo_tabla\">#Cot</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Cant.Msg</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Historial</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Resp</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nuevo</TD>\n";
		
		options+="</TR>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert ($j("code",filter).text()+"="+$j("value",filter).text());
				options+= "<tr>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("fecfmtcot",filter).text()+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("codcot",filter).text()+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">Cotizaci\u00f3n # "+$j("numcot",filter).text()+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("ctd",filter).text()+"</td>\n";
					
				lite = "<td align=\"center\" valign=\"top\">\n";
				lite+= "<a href=\"javascript:popwindow('historico_msj.php?cot="+$j("codcot",filter).text()+"')\">\n";
				lite+= "<img src=\"images/001_38.gif\" width=\"16px\" height=\"16px\"></a>\n";
				lite+= "</td>\n";
				
				options+=lite;
				options+="<td align=\"center\" valign=\"top\">";
				if ($j("tnepdt",filter).text() == "S")
				   options+="<a href=\"mismensajes2.php?cot="+$j("codcot",filter).text()+"\"><img src=\"images/mail.png\" width=\"24px\" height=\"16px\"></a>";
				else
				   options+="&nbsp;";
				options+="</td>\n";
				
				options+="<td align=\"center\" valign=\"top\">";
			    options+="<a href=\"javascript:Nuevo_Msg(2,"+$j("codcot",filter).text()+")\"><img src=\"images/folder_feed.png\" width=\"16px\" height=\"16px\"></a>";
				options+="</td>\n";
				
				options+= "</tr>\n";
				codcot = $j("codcot",filter).text();
				if (atrascot == 0) atrascot = codcot;
				if ($j("#primera_cot").val() == "0") $j("#primera_cot").val($j("codcot",filter).text());
				tot_filas++;
	        }
		);
		
		options+="<td style=\"padding-top: 5px\" colspan=\"7\" align=\"right\">\n";
		options+="<input type=\"hidden\" id=\"cod_clt\" value=\"<?php echo $Cod_Clt; ?>\">\n";
		if (tot_filas >= 18) {
			options+="<input type=\"hidden\" id=\"last_cot\" value=\""+codcot+"\">\n"
		}
		else {
			options+="<input type=\"hidden\" id=\"last_cot\" value=\"_NONE\">\n"
		}	
		
        options+="</TABLE>";
        $j("#tblMsgCot").replaceWith(options);
		$j("#atras_cot").val(atrascot);
	}

	function listMsgCtt (xml)
	{
		var tot_filas = 0;
		var atrasfol = 0;
		var arrTipCna = ["","Informaci\u00f3n del Producto","Reclamos","Contacto Comercial","Solicitud de Catalogos","Informaci\u00f3n de sus Ordenes","Otro"]; 
		
		options="<TABLE BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" width=\"630\" ALIGN=\"center\" id=\"tblMsgCtt\">\n";
		if ($j("#tipo_bus_folio").val() == "P")
		   options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:todosctt()\">TODOS</a> | PENDIENTES</td></tr>\n";
		else
		   options+="<tr><td colspan=\"7\" align=\"right\">TODOS | <a href=\"javascript:pendientesctt()\">PENDIENTES</a></td></tr>\n";
        options+="<TR>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Fecha</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Grupo</TD>\n";
		options+="<TD width=\"40%\" VALIGN=\"TOP\" ALIGN=\"left\" class=\"titulo_tabla\">#Caso</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Cant.Msg</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Historial</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Resp</TD>\n";
		options+="<TD width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nuevo</TD>\n";
		options+="</TR>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert ($j("code",filter).text()+"="+$j("value",filter).text());
				options+= "<tr>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("feccna",filter).text()+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("folctt",filter).text()+"</td>\n";
	            options+= "<td align=\"left\" valign=\"top\">"+arrTipCna[$j("tipcna",filter).text()]+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("ctd",filter).text()+"</td>\n";
					
				lite = "<td align=\"center\" valign=\"top\">\n";
				lite+= "<a href=\"javascript:popwindow('historico_msj.php?folctt="+$j("folctt",filter).text()+"&clt=<?php echo $Cod_Clt; ?>')\">\n";
				lite+= "<img src=\"images/001_38.gif\" width=\"16px\" height=\"16px\"></a>\n";
				lite+= "</td>\n";
				
				options+=lite;
				options+="<td align=\"center\" valign=\"top\">";
				if ($j("tnepdt",filter).text() == "S")
				   options+="<a href=\"#\"><img src=\"images/mail.png\" width=\"24px\" height=\"16px\"></a>";
				else
				   options+="&nbsp;";
				options+="</td>\n";
				
				options+="<td align=\"center\" valign=\"top\">";
			    options+="<a href=\"javascript:Nuevo_Msg(1,"+$j("folctt",filter).text()+")\"><img src=\"images/folder_feed.png\" width=\"16px\" height=\"16px\"></a>";
				options+="</td>\n";


				options+= "</tr>\n";
				folctt = $j("folctt",filter).text();
				if (atrasfol == 0) atrasfol = folctt;
				if ($j("#primer_folio").val() == "0") $j("#primer_folio").val($j("folctt",filter).text());

				tot_filas++;
	        }
		);
		
		options+="<td style=\"padding-top: 5px\" colspan=\"6\" align=\"right\">\n";
		options+="<input type=\"hidden\" id=\"cod_clt\" value=\"<?php echo $Cod_Clt; ?>\">\n";
		if (tot_filas >= 18) {
			options+="<input type=\"hidden\" id=\"last_folio\" value=\""+folctt+"\">\n"
		}
		else {
			options+="<input type=\"hidden\" id=\"last_folio\" value=\"_NONE\">\n"
		}	
		
        options+="</TABLE>";
        $j("#tblMsgCtt").replaceWith(options);
		$j("#atras_folio").val(atrasfol);
	}
	
	function Next_MsgCtt() {
		$j("#ordenfolio").val("1");
		if ($j("#last_folio").val() == "_NONE") 
			alert ("No existen mas mensajes que mostrar");
		else
			$j("form#searchMsgCtt").submit();
	}
	
	function Previus_MsgCtt() {
	    $j("#ordenfolio").val("2");
		if ($j("#atras_folio").val() == $j("#primer_folio").val())		
			alert ("No existen mas mensajes que mostrar");
		else {
		    $j("#last_folio").val($j("#atras_folio").val());
			$j("form#searchMsgCtt").submit();
		}
	}
	
	function Next_MsgCot() {
		$j("#orden").val("1");
		if ($j("#last_cot").val() == "_NONE") 
			alert ("No existen mas mensajes que mostrar");
		else
			$j("form#searchMsgCot").submit();
	}
	
	function Previus_MsgCot() {
	    $j("#orden").val("2");
		if ($j("#atras_cot").val() == $j("#primera_cot").val())		
			alert ("No existen mas mensajes que mostrar");
		else {
		    $j("#last_cot").val($j("#atras_cot").val());
			$j("form#searchMsgCot").submit();
		}
	}
	
	function todoscot() {
		$j("#orden").val("1");
		$j("#last_cot").val("10000000");
		$j("#tipo_bus_cot").val("T");
		$j("#primera_cot").val("0");
		$j("form#searchMsgCot").submit();
	}
	
	function pendientescot() {
		$j("#orden").val("1");
		$j("#last_cot").val("10000000");
		$j("#tipo_bus_cot").val("P");
		$j("#primera_cot").val("0");
		$j("form#searchMsgCot").submit();
	}
	
	function todosctt() {
		$j("#ordenfolio").val("1");
		$j("#last_folio").val("10000000");
		$j("#tipo_bus_folio").val("T");
		$j("#primer_folio").val("0");
		$j("form#searchMsgCtt").submit();
	}
	
	function pendientesctt() {
		$j("#ordenfolio").val("1");
		$j("#last_folio").val("10000000");
		$j("#tipo_bus_folio").val("P");
		$j("#primer_folio").val("0");
		$j("form#searchMsgCtt").submit();
	}
