<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selLoanApprove.php
*          Date 		: 	
*********************************************************************************/
include ("common.php");

$sSQL = "";
$sWhere = "";		
$sWhere .= " status = '3'";
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	 * FROM loans";
$sSQL = $sSQL . $sWhere.'  order by loanNo desc';
$GetLoan = &$conn->Execute($sSQL);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}

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
	function selPinjaman(memberID, name, loan_no, bond_no)
	{	
		window.opener.document.MyForm.no_anggota.value = memberID;	
		window.opener.document.MyForm.nama_anggota.value = name;	
		window.opener.document.MyForm.loan_no.value = loan_no;	
		window.opener.document.MyForm.no_bond.value = bond_no;	
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
								<td class="header">&nbsp;No Ruj. Pembiayaan</td>
								<td class="header">&nbsp;Pinjaman</td>
								<td class="header" align="center">&nbsp;Jumlah</td>
							</tr>';
	while (!$GetLoan->EOF) {
		$id		= $GetLoan->fields(loanID);
		$code	= dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")).'/'.sprintf("%010d", $GetLoan->fields(loanID));
		$name	= dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		$no		= $GetLoan->fields(loanNo);
		$amt	= dlookup("general", "c_Maksimum", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		//------------
		$memberID = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Number"));
		$nama_anggota = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Number"));
		$bond	= dlookup("loandocs", "rnoBond", "loanID=" . $id );
		
		print '				<tr>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$memberID.'\',\''.$nama_anggota.'\',\''.$no.'\',\''.$bond.'\');">'.$GetLoan->fields(loanNo).'</a></td>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$memberID.'\',\''.$nama_anggota.'\',\''.$no.'\',\''.$bond.'\');">'
								.dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")).'-'
								.$name.'</a></td>
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

