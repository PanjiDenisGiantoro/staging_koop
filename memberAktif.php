<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	member.php
 *		   Description	:   Update member status
 *          Date 		: 	24/03/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("koperasiList.php");
if (!isset($strDate))	$strDate = date("d/m/Y");
if ($action == 'Kemaskini') {
	$pk = explode(":", $pk);
	$str = array();
	foreach ($pk as $val) {
		$str[] = "'" . $val . "'";
	}
	$pk = implode(",", $str);
	if (isset($selAktif)) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = '';
		$sWhere = '';
		$sWhere = ' userID  in (' . $pk . ')';
		$sSQL	= ' UPDATE users ';
		$sSQL	.= ' SET ' .
			' isActive =' . tosql($selAktif, "Text") .
			' ,updatedBy =' . tosql($updatedBy, "Text") .
			' ,updatedDate=' . tosql($updatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		$rs = &$conn->Execute($sSQL);
		print 	'
		<script>
			window.location = "?vw=memberProfil&mn=901";
		</script>';
		//exit;
	}
}

if (isset($pk)) $pkall = explode(":", $pk);
unset($pk);
/*
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
	
</head>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';
*/
print '
<div class="table-responsive">
<form name="MyForm" action="" method="post">
<input type="hidden" name="action">


<table border="0" cellspacing="1" cellpadding="0" width="" align="left" >
	<tr>
		<td class="Data">
			<table border="0" cellspacing="4" cellpadding="3" width="100%" align="center" class="table bg-grey">
				<tr class="table-primary">
					<td colspan="2">Profil Login sistem.</b></td>
				</tr>';
for ($s = 0; $s < count($pkall); $s++) {
	//foreach($pkall as $pk) {
	if ($s > 0) {
		$pk = $pkall[$s];

		if ($s == 1) {
			$strpk = $pk;
		} else {
			$strpk = $strpk . ":" . $pk;
		}
		$GetUser = ctMember("", $pk);
		if ($GetUser->RowCount() == 0) {
			print '		<tr class="table-light">
					<td class="Data" colspan="2" align="center" height="50" valign="middle">- Tiada Maklumat Mengenai Anggota -</b></td>
				</tr>';
			//exit;
		}
		$status			= dlookup("userdetails", "status", "userID=" . tosql($pk, "Text"));
		$memberID		= dlookup("userdetails", "memberID", "userID=" . tosql($pk, "Text"));
		$approvedDate	= dlookup("userdetails", "approvedDate", "userID=" . tosql($pk, "Text"));
		$rejectedDate	= dlookup("userdetails", "rejectedDate", "userID=" . tosql($pk, "Text"));
		$remark			= dlookup("userdetails", "remark", "userID=" . tosql($pk, "Text"));
		print '		<tr class="table-light">
					<td width="40%">Nomor Anggota</td>
					<td width="60%">' . $memberID . '</td>
				</tr>
				<tr class="table-light">
					<td>Nama Anggota</td>
					<td>' . $GetUser->fields(name) . '</td>
				</tr>								
				<tr class="table-light">
					<td>Tanggal Pengajuan</td>
					<td>' . toDate("d/m/Y", $GetUser->fields(applyDate)) . '</td>
				</tr>';
	} //end if
} //end foreach
//------------------------

print ' <tr class="table-light">
					<td>Ubah status login sistem kepada</td><td>
					<select name="selAktif" class="form-select-sm">';
for ($i = 0; $i < count($activeList); $i++) {
	if ($activeVal[$i] <> 3 and $activeVal[$i] <> 4)
		print '		<option value="' . $activeVal[$i] . '">' . $activeList[$i];
}
print '		</select>
		        	</td>
				</tr>	
		
				<tr class="table-light">
					<td colspan="2" align="center">
					<input type="hidden" name="pk" value="' . $strpk . '">
					<input type="submit" name="action" value="Kemaskini" class="btn btn-primary">&nbsp;<input type="button" name="batal" value="Batal" class="btn btn-danger"  onclick= "Javascript:(window.location.href=\'?vw=memberProfil&mn=901\')"></td>
				</tr>';



print '		</table>
		</td>
	</tr>
</table>';

print '</form></div>';
include("footer.php");
/*
print '</body>
</html>';
*/
