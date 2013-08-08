<BODY onload="Print()">

<OBJECT ID="RSClientPrint" CLASSID="CLSID:FA91DF8D-53AB-455D-AB20-F2F023E498D3" CODEBASE="<URL to the .CAB file>#Version=<your application version information>" VIEWASTEXT></OBJECT>

<script language="javascript">

function Print()

{

RSClientPrint.MarginLeft = 12.7;
RSClientPrint.MarginTop = 12.7;
RSClientPrint.MarginRight = 12.7;
RSClientPrint.MarginBottom = 12.7;
RSClientPrint.Culture = 1033;
RSClientPrint.UICulture = 9;
RSClientPrint.Print('http://localhost/vestmed/prueba.php', 'prueba', 'Employee_Sales_Summary')

}

</script>

</BODY>