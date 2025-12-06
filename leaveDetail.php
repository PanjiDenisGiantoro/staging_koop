<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	leaveDetail.php
 *          Date 		: 	27/11/2024
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($yr)) $yr	= date("Y");
if (!isset($yy)) $yy	= date("Y");
if (!isset($mm)) $mm	= date("m");
if (!isset($filter))	$filter = "ALL";
if (!isset($dept))		$dept = "";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");

$db_koperasiID = dlookup("setup", "koperasiID", "1=1");

if (get_session('Cookie_userID') == "" or $db_koperasiID <> 0) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
	exit;
}


$sFileName = '?vw=leaveDetail&mn=7';
$sFileRef  = '?vw=leaveDetail&mn=7';
$title     = "Senarai Permohonan Cuti";

$sSQL = "SELECT l.leaveType, l.startLeave, l.endLeave, l.status, l.userID, l.applyDate, l.remark
         FROM sleave l   
         WHERE l.userID = '" . get_session('Cookie_userID') . "'
         ORDER BY l.applyDate DESC";

$GetLeave = &$conn->Execute($sSQL);
$GetLeave->Move($StartRec - 1);
$TotalRec = $GetLeave->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td><h5 class="card-title"><i class="mdi mdi-progress-alert"></i>&nbsp;' . strtoupper($title) . '</h5></td></tr>';

if ($GetLeave->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">                             
					<tr class="table-primary">
					<td nowrap align="center"><b>Bil</b></td>
					<td nowrap>&nbsp;<b>Jenis Cuti</b></td>
					<td nowrap align="center"><b>Tarikh Mula</b></td>
					<td	nowrap align="center"><b>Tarikh Tamat</b></td>
                    <td	nowrap align="center"><b>Status</b></td>
					<td nowrap align="center">&nbsp;<b>Tarikh Mohon</b></td>
					</tr>';
	while (!$GetLeave->EOF && $cnt <= $pg) {
		$leaveName    = dlookup("general", "name", "ID=" . tosql($GetLeave->fields(leaveType), "Text"));
		$name         = dlookup("users", "name", "userID=" . tosql($GetLeave->fields(userID), "Text"));

		$status = $GetLeave->fields(status);
		$colorStatus = "Data";
		if ($status	== 0) $colorStatus = "text-success";
		if ($status	== 1) $colorStatus = "greenText";
		if ($status	== 2) $colorStatus = "redText";
		if ($status	== 3) $colorStatus = "blackText";
		if ($status == 4) $colorStatus = "text-info";
		print ' <tr>
			<td class="Data" align="center">' . $bil . '&nbsp;</td>
			<td class="Data">' . strtoupper($leaveName) . '</td>';

		print '								
			<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetLeave->fields(startLeave)) . '</td>
            <td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetLeave->fields(endLeave)) . '</td>
            ';
		print '
            <td align="center"><span class="' . $colorStatus . '">' . $cutiList[$status] . '</span></td>
            ';
		print '
            <td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetLeave->fields(applyDate)) . '</td>
            </tr>';
		$cnt++;
		$bil++;
		$GetLeave->MoveNext();
	}

	$GetLeave->Close();
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
		print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '&filter=' . $filter . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>' . $GetLeave->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table></td></tr></table></div>
</form>';

print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	  
</script>';
include("footer.php");
