<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	carumanList.php
 *          Date 		: 	22/3/2024
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "";
if (!isset($dept))		$dept = "";
if (!isset($carum))		$carum = "";
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$IDName = get_session("Cookie_userName");

$sFileName = '?vw=carumanList&mn=905';
$sFileRef2 = "?vw=baucer&mn=$mn"; // urusniaga anggota - baucer
$title     = "Senarai Pengeluaran Caruman";

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$deleteSuccess = true; // Assume success unless proven otherwise
	$partialSuccess = false; // Track if any deletion failed

	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "ID=" . tosql($pk[$i], "Text");
		$sSQL = "DELETE FROM usercaruman WHERE " . $sWhere;

		if (!$conn->Execute($sSQL)) {
			$deleteSuccess = false; // Mark failure if any deletion fails
			$partialSuccess = true; // If at least one deletion fails, mark partial success
		}
	}

	// Set the message based on the deletion outcome
	if ($deleteSuccess) {
		$message = "Penghapusan Berjaya"; // All deletions succeeded
	} elseif ($partialSuccess) {
		$message = "Sebahagian Penghapusan Berjaya, Sebahagian Gagal"; // Partial success
	} else {
		$message = "Penghapusan Gagal"; // All deletions failed
	}

	// Show the message and redirect
	echo "
    <script>
        alert('$message');
        window.location = '?vw=carumanList&mn=905';
    </script>";
}

//--- End   : deletion based on checked box -------------------------------------------------------

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

//--- Prepare caruman type
$carumanList = array();
$carumanVal  = array();
$carumanPerkaraVal  = array();
$GetCaruman = "SELECT name AS carumanName, ID AS carumanID FROM general WHERE category = 'J' AND j_Aktif IN (1) ORDER BY ID ASC";
$rsCaruman = &$conn->Execute($GetCaruman);
if ($rsCaruman->RowCount() <> 0) {
	while (!$rsCaruman->EOF) {
		$id = $rsCaruman->fields(carumanID);
		$index = ($id == 1595) ? "1" : "2";
		if ($id == 1595 || $id == 1596) {
			array_push($carumanList, $rsCaruman->fields(carumanName));
			array_push($carumanVal, $index);
			array_push($carumanPerkaraVal, $id);
		}
		$rsCaruman->MoveNext();
	}
}

$sSQL = "";
$sWhere = " a.userID = b.userID AND a.userID=c.userID ";
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}
if ($filter <> "") {
	$sWhere .= " and c.status = " . tosql($filter, "Number");
}
if ($carum <> "") {
	$sWhere .= " and c.type = " . tosql($carum, "Number");
}
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID like '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '" . $q . "'";
	}
}
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*,c.*, c.ID as carumanID
			 FROM 	users a, userdetails b, usercaruman c";
$sSQL = $sSQL . $sWhere . ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ), c.applyDate DESC';

$GetMember = &$conn->Execute($sSQL);

$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="filter" value="' . $filter . '">
<input type="hidden" name="carum" value="' . $carum . '">
<div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Mohon Baru" onClick="window.location.href=\'?vw=carumanApply&mn=905\'"/>
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';
print '<div class="mb-3">
<div>
			Carian Melalui 
			<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kad Pengenalan</option>';
else print '<option value="3">Kad Pengenalan</option>';
print '		</select>
			<input type="text" name="q" value="" class="form-control-sm" maxlength="50" size="20" class="Data">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;		
			Cawangan/Zon
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}

print '	</select>
        </div>
</div>
	<div class="mb-3">
    <div>
			Jenis
			<select name="carum" class="form-select-sm" onchange="document.MyForm.submit();">';
print '<option value="">- Semua -';
for ($i = 0; $i < count($carumanList); $i++) {
	if ($carumanVal[$i] < 3) {
		print '	<option value="' . $carumanVal[$i] . '" ';
		if ($carum == $carumanVal[$i]) print ' selected';
		print '>' . $carumanList[$i];
	}
}
print '		</select>&nbsp;';

print '&nbsp;
			Status
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
print '<option value="">- Semua -';
for ($i = 0; $i < count($carumanStatusList); $i++) {
	if ($carumanStatusVal[$i] < 4) {
		print '	<option value="' . $carumanStatusVal[$i] . '" ';
		if ($filter == $carumanStatusVal[$i]) print ' selected';
		print '>' . $carumanStatusList[$i];
	}
}
print '		</select>&nbsp;';

if (($IDName == 'superadmin') or ($IDName == 'admin')) {
	if ($filter == 0) print '<input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">
';
}
print '
<input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">';

print '</div>
</div>

			<table width="100%">
				<tr>
<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td>					<td align="right" class="textFont">
						Paparan <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
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
print '				</select>setiap mukasurat.
					</td>
				</tr>
			</table>
		';
if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	   
				<div class="table-responsive">    
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap style="text-align: center; vertical-align: bottom;">&nbsp;</td>
						<td nowrap align="center">&nbsp;</td>
						<td nowrap style="text-align: left; vertical-align: bottom;">Nombor - Nama Anggota</td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Jenis</td>
						<td nowrap style="text-align: right; vertical-align: bottom;">Amaun (RM)</td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Status</td>
                        <td nowrap align="center"><div style="white-space: nowrap;">Tarikh<br>Mohon</div></td>
                        <td nowrap align="center"><div style="white-space: nowrap;">Tarikh<br>Lulus</div></td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Baucer</td>
                        <td nowrap align="center"><div style="white-space: nowrap;">Tarikh<br>Ditolak</div></td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Catatan</td>
					</tr>';

	print '
<style>
.bayar-btn {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    font-size: 15px;
    border-color: #eff2f7;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.35);
}

.bayar-btn i {
    font-size: 1.5rem;
}
</style>
';

	print '
<!-- Boxicons CSS -->
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
@import url(\'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@500&display=swap\');

.button{
	display: flex;
	height: 30px;
	padding: 0;
	background:rgb(70, 151, 232);
	border: none;
	outline: none;
	border-radius: 5px;
	overflow: hidden;
	font-family: \'Poppins\',
	sans-serif;
	font-size: 13px;
	font-weight: 500;
	cursor: pointer;
	box-shadow: rgba(0,0,0,0.19)
	0px 10px 20px,
	rgba(0,0,0,0.23) 0px 6px 6px;
	transition: all 160ms ease-in;
}

button:hover{
	background: #1b82ec;
}

button:active{
	background: #006e58;
	transform: scale(.95);
}

.button__text,
.button__icon{
	display: flex;
	align-items: center;
	height: 100%;
	padding: 0 6px;
	color: #d6e8fc;
}

.button__icon{
	font-size: 1.5em;
	background: rgb(0,0,0,0.08);
}
</style>
';

	while (!$GetMember->EOF && $cnt <= $pg) {
		$userID = $GetMember->fields(userID);
		$carumanID = $GetMember->fields('carumanID');
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$totYr = getFees($GetMember->fields(userID));
		$totSh = getSharesterkini($GetMember->fields(userID), $yr);
		// $totSk = getDepoKhasAll($GetMember->fields(userID), $yr);
		$withdrawAmt = $GetMember->fields('withdrawAmt');
		$type = $GetMember->fields(type);
		$voucherNo = $GetMember->fields(voucherNo);
		$remark = $GetMember->fields(remark);

		if ($type == 1) $perkaraCarum = '1595'; //yuran
		elseif ($type == 2) $perkaraCarum = '1596'; //syer
		else $perkaraCarum = null;

		if ($type == 1) $tot = $totYr;
		elseif ($type == 2) $tot = $totSh;

		$status = $GetMember->fields(status);

		$bayar = '';
		if ($status == 1) $bayar = '<i class="bx bxs-dollar-circle text-info" title="bayar" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'?vw=baucer&action=new&no_anggota=' . $userID . '&carumanID=' . $carumanID . '\')"></i>';
		if ($status == 1) $bayar = '<button class="button" 
			onclick="if (confirm(\'Disburse?\')) { open_(\'?vw=baucer&action=new&no_anggota=' . $userID . '&carumanID=' . $carumanID . '\'); }">
				<span class="button__text">Bayaran</span>
				<span class="button__icon">
					<i class="bx bxs-dollar-circle"></i>
				</span>
			</button>';

		if ($status == 0) $colorStatus = '<span class="badge badge-soft-dark"><b>' . $carumanStatusList[$status] . '</b></span>'; //proses semakan
		if ($status == 1) $colorStatus = '<span class="badge badge-soft-success"><b>' . $carumanStatusList[$status] . '</b></span>'; //lulus & proses baucer
		if ($status == 2) $colorStatus = '<span class="badge badge-soft-danger"><b>' . $carumanStatusList[$status] . '</b></span>'; //ditolak
		if ($status == 3) $colorStatus = '<span class="badge badge-soft-primary"><b>' . $carumanStatusList[$status] . '</b></span>'; //selesai
		// Status : Proses Semakan (kelabu) > Lulus & Proses Baucer (biru)> Selesai (hijau).
		// Status alternatif: Proses Semakan (kelabu) > Ditolak (merah)

		print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $carumanID . '" data-status="' . $status . '"></td>
						<td class="Data">' . $GetMember->fields('memberID') . ' - ' . $GetMember->fields('name') . '</td>
						<td class="Data" align="center">' . $carumanTypeList[$type] . '<br><div style="white-space: nowrap;">(Terkumpul: RM' . $tot . ')</div></td>
						<td class="Data" align="right">' . number_format($withdrawAmt, 2) . '</td>
						<td class="Data" align="center">' . $colorStatus . '</td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields('applyDate')) . '</td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields('approvedDate')) . '</td>
						<td class="Data" align="center">' . $bayar . '<a href="' . $sFileRef2 . '&action=view&no_baucer=' . tohtml($voucherNo) . '&yy=' . $yy . '&mm=' . $mm . '">' . $voucherNo . '</td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields('rejectedDate')) . '</td>
						<td class="Data" align="center">' . $remark . '</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetMember->MoveNext();
	}
	$GetMember->Close();

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
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&filter=' . $filter . '">';
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
			<td class="textFont">Jumlah Rekod : <b>' . $GetMember->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b>- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b>- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form>';

include("footer.php");

print '
<script language="JavaScript">

	function open_(url) {
		const width = screen.availWidth;
		const height = screen.availHeight;
		window.open(url, "pop", `top=0,left=0,width=${width},height=${height},scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no`);
	}

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
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }
		
function ITRActionButtonClickStatus(v) {
    var strStatus = "";
    var e = document.MyForm;
    if (e == null) {
        alert(\'Sila pastikan nama form diwujudkan!\');
        return;
    }

    var count = 0;
	var firstStatus = null;          // Will store the status of first checked item
	var hasDifferentStatus = false;  // Flag to indicate if we found any different status

    // First pass: check if all selected items have the same status
    for (var c = 0; c < e.elements.length; c++) {
        if (e.elements[c].name == "pk[]" && e.elements[c].checked) {

			// Get status of current checkbox
            var currentStatus = e.elements[c].getAttribute(\'data-status\');
            
            if (firstStatus === null) {
				// If this is the first checked checkbox we found,
				// set its status as our reference status
                firstStatus = currentStatus;
            } else if (currentStatus !== firstStatus) {
				// If we find any subsequent checkbox with a different status,
				// set the flag to true and break the loop
                hasDifferentStatus = true;
                break;
            }
            count++;
        }
    }

    if (count == 0) {
        alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
        return;
    }

    if (hasDifferentStatus) {
        alert(\'Maaf, anda hanya boleh memproses data yang mempunyai status yang sama sahaja.\');
        return;
    }

    // If all statuses are the same, proceed with processing
    if (confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
        // Build the status string
        for (var c = 0; c < e.elements.length; c++) {
            if (e.elements[c].name == "pk[]" && e.elements[c].checked) {
                strStatus += ":" + e.elements[c].value;
            }
        }
        window.location.href = "?vw=carumanStatus&mn=905&pk=" + strStatus;
    }
}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
