<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 30;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";
if (!isset($filter))	$filter = "ALL";
//--- Prepare department list
$sSQL = "";
$sWhere = "  A.category = 'AB'";
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.name like '%" . $q . "%'";
	}
}
if ($companyID) $sWhere .= " AND A.ID in (" . $companyID . ") ";

$sWhere = " WHERE (" . $sWhere . ")";

if ($q <> "") {
	if ($by == 1) {
		$sSQL = "SELECT	DISTINCT A.* FROM generalacc A";
	}
} else {
	$sSQL = "SELECT	A.ID AS companyID,A.* FROM generalacc A ";
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.createdDate ASC';
$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);
$TotalRec =	$GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

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
	function selPinjaman(companyID,code,name,b_Baddress)
	{
		window.opener.document.MyForm.companyID.value = companyID;
		window.opener.document.MyForm.code.value = code;
		window.opener.document.MyForm.name.value = name;
		window.opener.document.MyForm.b_Baddress.value = b_Baddress;
		window.close();
	}

</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Header" colspan="2">Senarai Pemiutang</b></td>
				</tr>
	<tr	valign="top" class="Header">
		<td	align="left" >
			Carian melalui
			<select	name="by" class="Data">';
if ($by	== 1) print '<option value="1" selected>Nama Syarikat</option>';
else print '<option	value="1">Nama Syarikat</option>';

print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="20" class="Data">
			<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp';


print '	</select>
		</td>
	</tr>
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;</td>
					<td	align="right" class="textFont">

					Paparan	<SELECT	name="pg" class="Data" onchange="doListAll();">';
if ($pg	== 5)	print '<option value="5" selected>5</option>';
else print '<option	value="5">5</option>';
if ($pg	== 10)	print '<option value="10" selected>10</option>';
else print '<option	value="10">10</option>';
if ($pg	== 20)	print '<option value="20" selected>20</option>';
else print '<option	value="20">20</option>';
if ($pg	== 30)	print '<option value="30" selected>30</option>';
else print '<option	value="30">30</option>';
if ($pg	== 40)	print '<option value="40" selected>40</option>';
else print '<option	value="40">40</option>';
if ($pg	== 50)	print '<option value="50" selected>50</option>';
else print '<option	value="50">50</option>';
if ($pg	== 100)	print '<option value="100" selected>100</option>';
else print '<option	value="100">100</option>';
if ($pg	== 200)	print '<option value="200" selected>200</option>';
else print '<option	value="200">200</option>';
if ($pg	== 300)	print '<option value="300" selected>300</option>';
else print '<option	value="300">300</option>';
if ($pg	== 400)	print '<option value="400" selected>400</option>';
else print '<option	value="400">400</option>';
if ($pg	== 500)	print '<option value="500" selected>500</option>';
else print '<option	value="500">500</option>';
if ($pg	== 1000) print '<option	value="1000" selected>1000</option>';
else print '<option	value="1000">1000</option>';
print '				</select>setiap	mukasurat.
					</td>
				</tr>';
print '	</table>
		</td>
	</tr>';

if ($GetLoan->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan No / Nama Syarikat -</b>
				</td></tr>';
} else {
	if ($GetLoan->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
					<tr	class="header">
						<td	nowrap>&nbsp;</td>
						<td	nowrap>&nbsp;Nombor Syarikat</td>
						<td	nowrap>&nbsp;Nama Syarikat</td>
						<td	nowrap align="center">&nbsp;Alamat Billing</td>
					</tr>';
		$amtLoan = 0;
		while (!$GetLoan->EOF && $cnt <= $pg) {
			//-------------------
			$companyID				= $GetLoan->fields(companyID);
			$code			= $GetLoan->fields(code);
			$name			= $GetLoan->fields(name);
			$b_Baddress		= $GetLoan->fields(b_Baddress);

			print '	<tr>
						<td	class="Data" align="right">' . $bil . '&nbsp;</td>
<td	class="Data">&nbsp;<a href="javascript:selPinjaman(\'' . $companyID . '\',\'' . $code . '\',\'' . $name . '\',\'' . $b_Baddress . '\');">
' . $code . '</a></td>

						<td	class="Data">&nbsp;' . $name . '</td>
						<td	class="Data">&nbsp;' . $b_Baddress . '</td>
					</tr>';
			$cnt++;
			$bil++;
			$GetLoan->MoveNext();
		}
		print ' </table>
			</td>
		</tr>		
		<tr>
			<td>';
		if ($TotalRec > $pg) {
			print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
			if ($TotalRec % $pg == 0) {
				$numPage = $TotalPage;
			} else {
				$numPage = $TotalPage + 1;
			}
			print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}
		print '
			</td>
		</tr>';

		print '
				</td>
			</tr>
				</table>
				
						</td>
					</tr>';
	} else {
		print '
					<tr><td	class="Label" align="center" height=50 valign=middle>
						<b>- Tiada rekod mengenai syarikat  -</b>
					</td></tr>';
	} // end of ($GetLoan->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
