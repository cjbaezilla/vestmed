<HTML>
<HEAD>
<META NAME="GENERATOR" Content="Microsoft Visual Studio 6.0">
<TITLE></TITLE>
</HEAD>
<BODY>

<P>
<form ID="F1" name="F1" method="post" ACTION="reponer_ins.php?filter=7">
    <input name="dfListOdr" type="hidden" id="dfListOdr" value="" />
</form>
</P>

<script language="javascript">
	var f1;	
	f1 = document.F1;	

	f1.dfListOdr.value = parent.opener.document.searchList.dfListOdr.value;
	f1.submit();
</script>

</BODY>
</HTML>
