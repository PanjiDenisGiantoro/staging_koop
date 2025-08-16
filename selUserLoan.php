<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selUserLoan.php
*          Date 		: 	27/04/2004
*		   Used By		:	loanpymtEdit.php
*********************************************************************************/
include ("common.php");

$sSQL = "";
$sWhere = "";		
$sWhere .= " userID = " . tosql($pk,"Text"). " AND status = '1'";
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	 * FROM loans";
$sSQL = $sSQL . $sWhere;
$GetLoan = &$conn->Execute($sSQL);

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
	function selPinjaman(id,code,name,no)
	{	
		window.opener.document.MyForm.loanID.value	= id;	
		window.opener.document.MyForm.loanCode.value	= code;	
		window.opener.document.MyForm.loanName.value	= name;	
		window.opener.document.MyForm.loanNo.value		= no;	
		window.close();
	}
</script>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="MyForm" action=' .$PHP_SELF . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Label" colspan="2">Klik pada Pinjaman/ No Rujukan untuk pilihan.</b></td>
				</tr>';
if ($GetLoan->RowCount() <> 0) {  
	print '		<tr>
					<td class="Data">
						<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
							<tr>
								<td class="header">&nbsp;Pinjaman-No Rujukan</td>
								<td class="header">&nbsp;No Pinjaman</td>
								<td class="header" align="center">&nbsp;Jumlah</td>
							</tr>';
	while (!$GetLoan->EOF) {
		$id		= $GetLoan->fields(loanID);
		$code	= dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")).'/'.sprintf("%010d", $GetLoan->fields(loanID));
		$name	= dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		$no		= $GetLoan->fields(loanNo);
		$amt	= dlookup("general", "c_Maksimum", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		print '				<tr>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$name.'\',\''.$no.'\');">'
								.dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")).'-'
								.sprintf("%010d", $GetLoan->fields(loanID)).'</a></td>
								<td class="Data">&nbsp;'.$GetLoan->fields(loanNo).'</td>
								<td class="Data" align="right">'.$amt.'&nbsp;</td>
							</tr>';
		$GetLoan->MoveNext();
	}				
	print '				</table>	
					</td>
				</tr>
				<tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Pinjaman : <b>'.$GetLoan->RowCount().'</b></i></li></td></tr>';
} else { 
	print '
				<tr><td	class="Label" align="center">
					<hr size="1"><b>- Tiada rekod mengenai Jenis Pinjaman  -</b><hr size="1">
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

