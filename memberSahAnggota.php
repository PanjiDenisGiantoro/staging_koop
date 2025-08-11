<?php

/*******************************************************************************
 *          Project		: iKOOP.com.my
 *          Filename		: memberSahAnggota.php	
 *          Date 		: 05/06/06	
 *******************************************************************************/
include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 0 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=memberSahAnggota&mn=1';
$sFileRef  = '?vw=memberSaksiSah&mn=1';
$title     = "Pengesahan Saksi Keanggotaan";

$pid = get_session('Cookie_userID');
$pk = dlookup("userdetails", "userID", "userID=" . $pid);

$sqlGet = "SELECT * FROM userdetails WHERE saksi1 = '" . $pk . "' ORDER BY approvedSaksi1 ASC";
$GetSaksi =  &$conn->Execute($sqlGet);

$sqlGet = "SELECT * FROM userdetails WHERE saksi2 = '" . $pk . "' ORDER BY approvedSaksi2 ASC";
$GetSaksi2 =  &$conn->Execute($sqlGet);

print '
<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><h5 class="card-title"><i class="typcn typcn-tick"></i>&nbsp;' . strtoupper($title) . '</h5></td>
	</tr>';
if ($GetSaksi->RowCount() <> 0 || $GetSaksi2->RowCount() <> 0) {
	$bil = 1;
	print '
	    <tr valign="top">
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="header table-primary">
						<td nowrap></td>
						<td nowrap align="center">Nombor Anggota</td>
						<td nowrap>Nama</td>
						<td nowrap align="center">Kad Pengenalan</td>
						<td nowrap>Cawangan/Zon</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="left">Sahkan Saksi</td>
					</tr>';
	while (!$GetSaksi->EOF) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetSaksi->fields(userID), "Text"));
		$status = $GetSaksi->fields(status);
		$ic1 = convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($GetSaksi->fields(userID), "Text")));


		$statusah = $GetSaksi->fields(approvedSaksi1);
		$colorStatus1 = "Data";
		if ($statusah <> 0) $colorStatus1 = "greenText";
		if ($statusah <> 1) $colorStatus1 = "redText";
		$colorStatus = "Data";
		if ($statusah <> 0) $colorStatus = "greenText";
		if ($statusah <> 1) $colorStatus = "redText";


		print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>';

		if ($statusah <> 1) {
			print '
	<td class="Data" align="center">
	<a href="' . $sFileRef . '&pk=' . tohtml($GetSaksi->fields(memberID)) . '&no=1">'
				. dlookup("userdetails", "memberID", "userID=" . tosql($GetSaksi->fields(userID), "Text")) . '</td>
	<td class="Data">'
				. dlookup("users", "name", "userID=" . tosql($GetSaksi->fields(userID), "Text")) . '</a></td>';
		} else {
			print '
	<td class="Data" align="center">'
				. dlookup("userdetails", "memberID", "userID=" . tosql($GetSaksi->fields(userID), "Text")) . '</td>
	<td class="Data">'
				. dlookup("users", "name", "userID=" . tosql($GetSaksi->fields(userID), "Text")) . '</td>';
		}

		print '
	<td class="Data" align="center">' . $ic1 . '</td>
	<td class="Data" align="left">' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . '</td>
	<td class="Data" align="center"><font class="' . $colorStatus . '">' . $statusList[$status] . '</td>';

		if ($statusah <> 0) {
			print '<td class="Data" align="left"><i class="fas fa-check text-primary"></i>&nbsp;<font class="' . $colorStatus1 . '"><b>Pengesahan Telah Dibuat</b></font></td>';
		} else {
			print '<td class="Data" align="left"><i class="mdi mdi-close text-danger"></i>&nbsp;<font class="' . $colorStatus1 . '"><b>Pengesahan Belum Dibuat</b></font></td>';
		}
		print '</tr>';
		$bil++;
		$GetSaksi->MoveNext();
	}

	while (!$GetSaksi2->EOF) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetSaksi2->fields(userID), "Text"));
		$status = $GetSaksi2->fields(status);
		$ic2 = convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($GetSaksi2->fields(userID), "Text")));
		$statusah = $GetSaksi2->fields(approvedSaksi2);

		print ' <tr>
			<td class="Data" align="right">' . $bil . '&nbsp;</td>';

		if ($statusah <> 1) {
			print '
	<td class="Data"><a href="' . $sFileRef . '&pk=' . tohtml($GetSaksi2->fields(memberID)) . '&no=1">'
				. dlookup("userdetails", "memberID", "userID=" . tosql($GetSaksi2->fields(userID), "Text")) . '&nbsp;-&nbsp;'
				. dlookup("users", "name", "userID=" . tosql($GetSaksi2->fields(userID), "Text")) . '</a></td>';
		} else {
			print '
	<td class="Data">&nbsp;'
				. dlookup("userdetails", "memberID", "userID=" . tosql($GetSaksi2->fields(userID), "Text")) . '&nbsp;-&nbsp;'
				. dlookup("users", "name", "userID=" . tosql($GetSaksi2->fields(userID), "Text")) . '</td>';
		}

		print '
	<td class="Data" align="center">&nbsp;' . $ic2 . '</td>
	<td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . '</td>
	<td class="Data" align="center">&nbsp;<font class="' . $colorStatus . '">' . $statusList[$status] . '</font></td>';

		if ($statusah <> 0) {
			print '<td class="Data" align="center"><font class="' . $colorStatus . '"><b>Pengesahan Telah Dibuat</b></font></td>';
		} else {
			print '<td class="Data" align="center"><font class="' . $colorStatus . '"><b>Pengesahan Belum Dibuat</b></font></td>';
		}
		print '</tr>';
		$bil++;
		$GetSaksi2->MoveNext();
	}


	print '</table></td></tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian Rekod "' . $q . '" Tidak Jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form>
</div>';
include("footer.php");
