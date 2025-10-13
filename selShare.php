<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selShare.php
*          Date 		: 	06/10/2003
*		   Used By		:	loanApply.php
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	

$GetShare = ctGeneral("","G");

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<script language="JavaScript">
	function selPinjaman(id,code,name,price)
	{	
		window.opener.document.MyForm.shareType.value	= id;	
		window.opener.document.MyForm.shareCode.value	= code;	
		window.opener.document.MyForm.shareName.value	= name;	
		window.opener.document.MyForm.sharePrice.value 	= price;	
		window.close();
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' .$PHP_SELF . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Label" colspan="2">Klik pada Kod ATAU Pokok untuk pilihan.</b></td>
				</tr>';
if ($GetShare->RowCount() <> 0) {  
	print '		<tr>
					<td class="Data">
						<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
							<tr>
								<td class="header">&nbsp;Kod</td>
								<td class="header">&nbsp;Syer</td>
								<td class="header" align="center">&nbsp;Harga Seunit (RP)</td>
							</tr>';
	while (!$GetShare->EOF) {
		$id		= $GetShare->fields(ID);
		$code	= $GetShare->fields(code);
		$name	= $GetShare->fields(name);			
		$price	= $GetShare->fields(g_Price);
		print '				<tr>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$name.'\',\''.$price.'\');">'.$code.'</a></td>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$name.'\',\''.$price.'\');">'.$name.'</a></td>
								<td class="Data" align="right" width="150">'.$price.'&nbsp;</td>
							</tr>';
		$GetShare->MoveNext();
	}				
	print '				</table>	
					</td>
				</tr>
				<tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Pokok : <b>'.$GetShare->RowCount().'</b></i></li></td></tr>';
} else { 
	print '
				<tr><td	class="Label" align="center">
					<hr size="1"><b>- Tiada rekod mengenai Kod Pokok  -</b><hr size="1">
				</td></tr>';
}
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">'.$retooFetis.'</p>
</body>
</html>';
?>

