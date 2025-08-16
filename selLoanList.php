<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selLoan.php
 *          Date 		: 	19-7-06
 *		   Used By		:	loanUpdate.php
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

//$conn->debug =1;
$sqlLoan = "select * from loans where isApproved = 0";
$GetLoan = $conn->Execute($sqlLoan);

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
	function selPinjaman(lid,uid,mid,id,code,name,caj,period,amt)
	{	
		window.opener.document.MyForm.loanID.value	= lid;	
		window.opener.document.MyForm.userID.value = uid;	
		window.opener.document.MyForm.memberID.value = mid;	
		window.opener.document.MyForm.loanType.value	= id;	
		window.opener.document.MyForm.loanCode.value	= code;	
		window.opener.document.MyForm.loanName.value	= name;	
		window.opener.document.MyForm.loanCaj.value 	= caj;	
		window.opener.document.MyForm.loanPeriod.value 	= period;	
		window.opener.document.MyForm.loanAmt.value 	= amt;
		window.close();
	}
</script>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Label" colspan="2">Klik pada kod  ATAU jenis Pembiayaan untuk pilihan.</b></td>
				</tr>';
if ($GetLoan->RowCount() <> 0) {
	print '		<tr>
					<td class="Data">
						<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
							<tr>
								<td class="header">&nbsp;Nombor Rujukan</td>
								<td class="header">&nbsp;Nombor anggota</td>
								<td class="header">&nbsp;Jenis Pembiayaan</td>
								<td class="header">&nbsp;caj Pembiayaan(%)</td>
								<td class="header" align="center">&nbsp;Jumlah</td>
								<td class="header" align="center">&nbsp;Tempoh</td>
							</tr>';
	//loanID loanNo loanType kadar_u loanAmt loanPeriod dlookup("general", "c_Maksimum", "ID=" . tosql($GetLoanDet->fields(loanType), "Number"));
	while (!$GetLoan->EOF) {
		$loanID = $GetLoan->fields(loanID);
		$userID = $GetLoan->fields(userID);
		$memberID =  dlookup("userdetails", "memberID", "userID=" . $userID);
		$loanNo = $GetLoan->fields(loanNo);
		$loanType = $GetLoan->fields(loanType);
		$kadar_u = $GetLoan->fields(kadar_u);
		$loanAmt = $GetLoan->fields(loanAmt);
		$loanPeriod = $GetLoan->fields(loanPeriod);
		//$c_deduct = dlookup("general", "c_deduct", "ID=" .$loanType);
		if ($loanType <> "") {
			$getKod = "SELECT code, name FROM general WHERE ID = " . $loanType;
			$rsKod = $conn->Execute($getKod);
			$kod_potongan = $rsKod->fields(code);
			$name = $rsKod->fields(name);
		} else {
			$kod_potongan = "";
		}
		print '				<tr>
								<td class="Data">&nbsp;<a href="javascript:selPinjaman(\'' . $loanID . '\',\'' . $userID . '\',\'' . $memberID . '\',\'' . $loanType . '\',\'' . $kod_potongan . '\',\'' . $name . '\',\'' . $kadar_u . '\',\'' . $loanPeriod . '\',\'' . $loanAmt . '\');">' . $loanNo . '</a></td>
								<td class="Data" align="left">&nbsp;' . $memberID . '</td>
								<td class="Data" align="left">&nbsp;' . $kod_potongan . '/' . $name . '</td>
								<td class="Data" align="left">&nbsp;' . $kadar_u . '</td>
								<td class="Data" align="right">' . $loanAmt . '&nbsp;</td>
								<td class="Data" align="center">&nbsp;' . $loanPeriod . '</td>
							</tr>';
		$GetLoan->MoveNext();
	}
	print '				</table>	
					</td>
				</tr>
				<tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Pembiayaan : <b>' . $GetLoan->RowCount() . '</b></i></li></td></tr>';
} else {
	print '
				<tr><td	class="Label" align="center">
					<hr size="1"><b>- Tiada rekod mengenai Pembiayaan  -</b><hr size="1">
				</td></tr>';
}
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
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
