<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selToMember.php
 *          Date 		: 	06/10/2003
 *		   Amended		:	31/03/2004 - Change to list all member
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if ((get_session("Cookie_koperasiID") <> $koperasiID){
	print '<script>parent.location.href = "index.php";</script>';
}


if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 25;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status in (1, 3, 4)
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		//		array_push ($deptList, $rs->fields(deptCode));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

/*/--- Prepare department type
$deptList = Array();
$deptVal  = Array();
$GetDept = ctGeneral("","B");
if ($GetDept->RowCount() <> 0){
	while (!$GetDept->EOF) {
		array_push ($deptList, $GetDept->fields(name));
		array_push ($deptVal, $GetDept->fields(ID));
		$GetDept->MoveNext();
	}
}	
*/
$GetMember = ctMemberStatusDept($q, $by, "1, 3, 4", $dept);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
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
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />        
</head>
<script language="JavaScript">
	function selAnggota(userid,memberid,name,tyuran,tsaham)
	{
		window.opener.document.MyForm.sellUserID.value = userid;	
		window.opener.document.MyForm.sellMemberID.value = memberid;	
		window.opener.document.MyForm.sellUserName.value = name;	
		window.opener.document.MyForm.tyuran.value = tyuran;	
		window.opener.document.MyForm.tsaham.value = tsaham;	
		window.close();
	}

	function selAnggotaD(memberid,name)
	{
		window.opener.document.MyForm.sellMemberID.value = memberid;	
		window.opener.document.MyForm.sellUserName.value = name;	
		window.close();
	}

	function selAnggotaE(memberid,name)
	{
		window.opener.document.MyForm.bayar_kod.value = memberid;	
		window.opener.document.MyForm.bayar_nama.value = name;	
		window.close();
	}

	function selSetAnggota(memberid,name,type,ic,acc ) 
	{
		if(type == "f"){
		window.opener.document.MyForm.no_anggota.value = memberid;	
		window.opener.document.MyForm.nama_anggota.value = name;	
		window.close();
		}
		if(type == "g"){
		window.opener.document.MyForm.userID.value = memberid;	
		window.opener.document.MyForm.nokp2.value = ic;	
		window.opener.document.MyForm.nama2.value = name;	
		window.opener.document.MyForm.acc2.value = acc;	
		window.close();
		//window.opener.document.MyForm.submit();
		}
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action="' . $PHP_SELF . '?refer=' . $refer . '" method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table class="table table-sm" border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="headerteal" colspan="2">Senarai Anggota</b></td>
				</tr>
				<tr>
					<td class="Data">
						Carian melalui 
						<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kad Pengenalan (Baru)</option>';
else print '<option value="3">Kad Pengenalan (Baru)</option>';
print '		</select>
						<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
			           	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
						Cawangan/Zon
						<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
							<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '			</select>
					</td>
				</tr>';
if ($GetMember->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Tiada sebarang maklumat anggota.  -</b>
				</td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-bordered table-striped table-sm" style="font-size: 9pt;">
					<tr class="table-primary">
						<td class="headerteal" nowrap>&nbsp;</td>
						<td class="headerteal" ><b>Nombor Anggota</b></td>
						<td class="headerteal" ><b>Nama</b></td>
						<td class="headerteal" ><b>Kad Pengenalan (Baru)</b></td>
						<td class="headerteal" ><b>Kad Pengenalan (Lama)</b></td>
						<td class="headerteal" ><b>Cawangan/Zon</b></td>
					</tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name = str_replace("'", "", $GetMember->fields(name));
			$tyuran = getFees($GetMember->fields(userID), date("Y")); //$GetMember->fields(totalFee);
			$tsaham = getShares($GetMember->fields(userID), date("Y")); //$GetMember->fields(totalShare);
			$newic		= $GetMember->fields(newIC);
			$oldic		= $GetMember->fields(oldIC);
			$jabatan 	= $GetMember->fields(departmentID);
			$acc 	= $GetMember->fields(accTabungan);
			print '
					<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data">';
			if ($refer == "d") {
				print '<a href="javascript:selAnggotaD(\'' . $memberid . '\',\'' . $name . '\');">' . $memberid . '</a>';
			} elseif ($refer == "e") {
				print '<a href="javascript:selAnggotaE(\'' . $memberid . '\',\'' . $name . '\');">' . $memberid . '</a>';
			} else {
				print '<a href="javascript:selSetAnggota(\'' . $memberid . '\',\'' . $name . '\',\'' . $refer . '\',\'' . $newic . '\',\'' . $acc . '\');">' . $memberid . '</a>';
			} /*else{
						print '<a href="javascript:selAnggota(\''.$userid.'\',\''.$memberid.'\',\''.$name.'\',\''.$tyuran.'\',\''.$tsaham.'\',\''.$newic.'\',\''.$oldic.'\',\''.$jumlahUnit.'\');">'.$memberid.'</a>';
						} */
			print '</td>
						<td class="Data">';
			if ($refer == "d") {
				print '<a href="javascript:selAnggotaD(\'' . $memberid . '\',\'' . $name . '\');">' . $name . '</a>';
			} elseif ($refer == "e") {
				print '<a href="javascript:selAnggotaE(\'' . $memberid . '\',\'' . $name . '\');">' . $name . '</a>';
			} else {
				print '<a href="javascript:selSetAnggota(\'' . $memberid . '\',\'' . $name . '\',\'' . $refer . '\',\'' .  $newic .  '\',\'' . $acc . '\');">' . $name . '</a>';
			} /*else{
						print '<a href="javascript:selAnggota(\''.$userid.'\',\''.$memberid.'\',\''.$name.'\',\''.$tyuran.'\',\''.$tsaham.'\',\''.$newic.'\',\''.$oldic.'\',\''.$jumlahUnit.'\');">'.$memberid.'</a>';
						} */
			print '</td>
						<td class="Data" align="left">' . $newic . '</td>
						<td class="Data" align="left">' . $oldic . '</td>
						<td class="Data" align="left">' . dlookup("general", "name", "ID=" . $jabatan) . '</td>
					</tr>';
			$cnt++;
			$bil++;
			$GetMember->MoveNext();
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
			if ($refer) $rfr = '&refer=' . $refer;
			else $rfr = '';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . $rfr . '">';
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
						<b>- Tiada rekod mengenai anggota  -</b>
					</td></tr>';
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
