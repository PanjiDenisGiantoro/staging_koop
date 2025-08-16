<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selExt.php
*          Date 		: 	06/10/2003
*		   Used By		:	loanApply.php
*********************************************************************************/
include ("common.php");	
include("koperasiQry.php");	

$GetExt = ctGeneral("","P");

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
	function selPinjaman(id,code,name)
	{	
		window.opener.document.MyForm.id2.value	= id;	
		window.opener.document.MyForm.code2.value	= code;	
		window.opener.document.MyForm.name2.value	= name;	
		window.close();
	}
</script>
<body leftmargin="5" topmargin="5" class="bodyBG">';

$pse = "Pendapatan";
print '
<form name="MyForm" action=' .$PHP_SELF . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Label" colspan="2">Klik pada kod  ATAU jenis '.$pse.' untuk pilihan.</b></td>
				</tr>';
if ($GetExt->RowCount() <> 0) {  
	print '		<tr>
					<td class="Data">
						<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
							<tr>
								<td class="header" width="20%">&nbsp;Kod '.$pse.'</td>
								<td class="header">&nbsp;Jenis '.$pse.'</td>							
							</tr>';
	while (!$GetExt->EOF) {
		$id		= $GetExt->fields(ID);
		$exist = 0;
		if ($userID) {
			$checkExt = "SELECT COUNT(ID) AS exist FROM userstates 
							WHERE payID = ".$id." 
							AND userID = ".toSQL($userID,"Text");
			$rsCheckExt = $conn->Execute($checkExt);
			if ($rsCheckExt->fields(exist) > 0)
				$exist = 1;
		}
		$code	= $GetExt->fields(code);
		$c_deduct = $GetExt->fields(c_Deduct);
		if ($c_deduct<>"") {
			$getKod = "SELECT code, name FROM general WHERE ID = ".$c_deduct;
			$rsKod = $conn->Execute($getKod);
			$kod_potongan = $rsKod->fields(code);
		} else {
			$kod_potongan = "";
		}
		$name	= $GetExt->fields(name);			
		$caj	= $GetExt->fields(c_Caj);
		$period	= $GetExt->fields(c_Period);
		$amt	= $GetExt->fields(c_Maksimum);
		if ($exist==0) {
			print '				<tr>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$name.'\');">'.$code.'</a></td>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$name.'\');">'.$name.'</a></td>
							</tr>';
		} else {
			print '				<tr>
								<td class="Data">&nbsp;'.$code.'</td>
								<td class="Data">&nbsp;'.$name.'</td>
							</tr>';
		}
		$GetExt->MoveNext();
	}				
	print '				</table>	
					</td>
				</tr>
				<tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Pembiayaan : <b>'.$GetExt->RowCount().'</b></i></li></td></tr>';
} else { 
	print '
				<tr><td	class="Label" align="center">
					<hr size="1"><b>- Tiada rekod mengenai Jenis '.$pse.'  -</b><hr size="1">
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

print '
<SCRIPT TYPE="TEXT/JAVASCRIPT" LANGUAGE="JAVASCRIPT">
<!-- Hide script from older browsers
	function toggleMenu(currMenu) {
		if (document.getElementById) {
			thisMenu = document.getElementById(currMenu).style
			if (thisMenu.display == "block") {
				thisMenu.display = "none"
			}
			else {
				thisMenu.display = "block"
			}
			return false
		}
		else {
			return true
		}
	}

// End hiding script -->
</SCRIPT>';
?>

