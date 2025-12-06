<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	agm.php
 *          Date 		:	5/5/2025 	
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "0";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=agm&mn='.$mn;
$title     = "Dokumen AGM";

$IDName = get_session("Cookie_userName");
//--- Begin : deletion based on checked box -------------------------------------------------------
// Check if action is 'delete' and ID is provided in the URL
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['pk'])) {

    $updatedBy    = get_session("Cookie_userName");
    $updatedDate  = date("Y-m-d H:i:s");

    $pk = $_GET['pk'];

    // Gunakan tosql dengan jenis yang betul
    $sWhere = "ID=" . tosql($pk, "Number");

    $docNo = dlookup("agm", "agmNo", $sWhere); // Pastikan ini dapat nilai

    // Teruskan jika $docNo berjaya diambil
    $sSQL = "UPDATE agm SET status = 1 WHERE " . $sWhere;
    $rs = &$conn->Execute($sSQL);

    $sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`) VALUES 
    ('Dokumen AGM Telah Dihapuskan - $docNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "', '" . $updatedDate . "', '" . $updatedBy . "', '9')";
    $rs = &$conn->Execute($sqlAct);

    if ($rs) {
        print '<script>alert("Dokumen AGM Telah Dihapuskan.");</script>';
    }
}


//--- End   : deletion based on checked box -------------------------------------------------------
$sSQL = "";
$sWhere = "status = 0";

if ($q <> "") {
    if ($by == 1) {
        $sWhere .= "AND title like '%" . $q . "%'";
    }
}

if ($sWhere != "") { 
    $sWhere = " WHERE (" . $sWhere . ")";
}

$sSQL = "SELECT * FROM agm" . $sWhere . " ORDER BY ID DESC";

$GetAgm = &$conn->Execute($sSQL);

$GetAgm->Move($StartRec - 1);

$TotalRec = $GetAgm->RowCount();
$TotalPage = ceil($TotalRec / $pg);


print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="ID" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=agmApply&mn='.$mn.'\'"/>
</div>';

	print '
<div class="table-responsive">    
<!--table border="1" cellspacing="1" cellpadding="3" width="100%" align="center" class="table"-->
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
			<tr><td>&nbsp;</td></tr>
				<tr>
					<!--td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td-->
					<td align="right" class="textFont">Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
	if ($pg == 5)	print '<option value="5" selected>5</option>';
	else print '<option value="5">5</option>';
	if ($pg == 10)	print '<option value="10" selected>10</option>';
	else print '<option value="10">10</option>';
	if ($pg == 20)	print '<option value="20" selected>20</option>';
	else print '<option value="20">20</option>';
	if ($pg == 30)	print '<option value="30" selected>30</option>';
	else print '<option value="30">30</option>';
	if ($pg == 40)	print '<option value="40" selected>40</option>';
	else print '<option value="40">40</option>';
	if ($pg == 50)	print '<option value="50" selected>50</option>';
	else print '<option value="50">50</option>';
	if ($pg == 100)	print '<option value="100" selected>100</option>';
	else print '<option value="100">100</option>';
	print '				</select> setiap halaman..
					</td>
				</tr>
			</table>
		</td>
	</tr>';

	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap align="left"><b>Nomor Rujukan AGM</b></td>
						<td nowrap><b>Tajuk</b></td>
						<td nowrap align="left"><b>Kandungan</b></td>
						<td nowrap align="center"><b>Dokumen</b></td>
						<td nowrap align="left"><b>Disediakan Oleh</b></td>
						<td nowrap align="center"><b>Tanggal Rapat</b></td>';
			if (($IDName == 'superadmin') or ($IDName == 'admin')) {
			if ($filter == 0) {
				print '<td nowrap align="center">&nbsp;<b>&nbsp;</b></td>';
			}
		}

		print '</tr>';
		
	if ($GetAgm->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		
		while (!$GetAgm->EOF && $cnt <= $pg) {
		
			print ' <tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data" align="left">' . $GetAgm->fields(agmNo) . '</td>
						<td class="Data" align="left">' . $GetAgm->fields(title) . '</td>
						<td class="Data" align="left">' . $GetAgm->fields(content) . '</td>
						<td class="Data" align="center">';
							if ($GetAgm->fields(agm_img) == NULL) {
								print '&nbsp;';
							} else {
								print '<input type="button" class="btn btn-sm btn-outline-danger" value="Dokumen" onClick=window.open(\'upload_agm/' . $GetAgm->fields(agm_img) . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
							}
						print '
						<td class="Data" align="left">' . $GetAgm->fields(updatedBy) . '</td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetAgm->fields(applyDate)) . '</td>';
						if (($IDName == 'superadmin') or ($IDName == 'admin')) {
							if ($filter == 0) {
								print '<td>
										<a href="?vw=agm&mn='.$mn.'&action=delete&pk=' . $GetAgm->fields[ID] . '" onClick="return confirm(\'Adakah anda pasti untuk hapus dokumen agm ini?\')" title="Hapus">
											<i class="fas fa-trash-alt text-danger" style="font-size: 19px; margin-top: 9px;"></i>
										</a>
									</td>';
							}
						}
						
			print '
					</tr>';
			$cnt++;
			$bil++;
			$GetAgm->MoveNext();
		}

		$GetAgm->Close();
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
			<td class="textFont">Jumlah Data : <b>' . $GetAgm->RowCount() . '</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center" colspan="8"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b></td></tr>';
		} else {
			print '
			<tr><td align="center"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b></td></tr>';
		}
	}
	print ' 
</table></td></tr></table></div>
</form>';
include("footer.php");
