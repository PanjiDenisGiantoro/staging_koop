<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	loanpembiayaan1.php
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "";
if (!isset($dept))		$dept = "";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=loanpembiayaan1&mn=904';
$sFileRef = '?vw=biayaDokumen&mn=904';
$title = "Rekening Pembiayaan";

$IDName = get_session("Cookie_userName");

$pembiayaanStatusList = array('Dalam Proses', 'Diluluskan', 'Aktif', 'Ditolak', 'Dibatalkan', 'Suspended');
$pembiayaanStatusVal  = array('1', '3', 'aktif', '7', '9', '11');

//--- Begin : proses (bulk status update) -----------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] == "proses" && isset($_POST['pk']) && is_array($_POST['pk'])) {
    $newStatus = isset($_POST['jenis_proses']) ? (int)$_POST['jenis_proses'] : null;
    if ($newStatus !== null && in_array($newStatus, array(3, 7))) {
        foreach ($_POST['pk'] as $loanID) {
            $loanID = (int)$loanID;
            if ($loanID > 0) {
                $sSQL = "UPDATE loans SET status=" . tosql($newStatus, 'Number') .
                        " WHERE loanID=" . tosql($loanID, 'Number');
                $conn->Execute($sSQL);
                $label = ($newStatus == 3) ? 'Luluskan' : 'Tolak';
                $strActivity = $label . ' pembiayaan ID - ' . $loanID;
                activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
            }
        }
        $labelMsg = ($newStatus == 3) ? 'diluluskan' : 'ditolak';
        echo '<script>alert("Status berhasil ' . $labelMsg . '."); window.location.href="' . $sFileName . '";</script>';
        exit;
    }
}
//--- End   : proses (bulk status update) -----------------------------------------------------------

//--- Begin : deletion based on checked box -------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['pk'])) {
    $pk = $_GET['pk'];
    if (is_numeric($pk)) {
        $CheckLoan = ctLoan("", $pk);
        if ($CheckLoan->RowCount() == 1) {
            if ($CheckLoan->fields['status'] < 3) {
                $sWhere = "loanID=" . tosql($pk, "Number");
                $loanNo = dlookup("loans", "loanNo", $sWhere);
                $sSQL = "DELETE FROM loans WHERE " . $sWhere;
                $rs = &$conn->Execute($sSQL);
                $sSQL = "DELETE FROM loandocs WHERE " . $sWhere;
                $rs = &$conn->Execute($sSQL);
                $strActivity = 'Hapus Pembiayaan ' . $loanNo;
                activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
            } else {
                print '<script>alert("Hanya permohonan belum siap proses boleh dihapus!");</script>';
            }
        }
    }
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

//--- Build query
$sSQL = "";
$sWhere = " A.loanID IS NOT NULL ";

if ($dept <> "") {
    $sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
    $sWhere .= " AND A.userID = B.userID ";
}

if ($filter !== "" && $filter !== "ALL") {
    if ($filter === "aktif") {
        $sWhere .= " AND A.status = 3 AND A.outstandingAmt > 0 ";
    } else {
        $sWhere .= " AND A.status = " . tosql($filter, "Number");
    }
}

if ($q <> "") {
    if ($by == 1) {
        $sWhere .= " AND A.userID = B.userID ";
        $sWhere .= " AND B.memberID like '%" . $q . "%'";
    } else if ($by == 2) {
        $sWhere .= " AND A.userID = C.userID ";
        $sWhere .= " AND C.name like '%" . $q . "%'";
    } else if ($by == 3) {
        $sWhere .= " AND A.userID = B.userID ";
        $sWhere .= " AND B.newIC like '%" . $q . "%'";
    }
}

$sWhere = " WHERE (" . $sWhere . ")";

if ($q <> "") {
    if ($by == 1 or $by == 3) {
        $sSQL = "SELECT DISTINCT A.* FROM loans A, userdetails B";
    } else if ($by == 2) {
        $sSQL = "SELECT DISTINCT A.* FROM loans A, userdetails B, users C";
    }
} else {
    if ($dept <> "") {
        $sSQL = "SELECT DISTINCT A.* FROM loans A, userdetails B";
    } else {
        $sSQL = "SELECT DISTINCT A.* FROM loans A";
    }
}

$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=loanApply&mn=904\'"/>
</div>';

// Fetch loan counts by status
$dalam_Proses = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE status = 1")->fields['count'];
$diluluskan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE status = 3")->fields['count'];
$aktif = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE status = 3 AND outstandingAmt > 0")->fields['count'];
$ditolak = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE status = 7")->fields['count'];
$dibatalkan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE status = 9")->fields['count'];
$suspended = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE status = 11")->fields['count'];

$totalEntries = $dalam_Proses + $diluluskan + $aktif + $ditolak + $dibatalkan + $suspended;

$entries = array(
    'Dalam Proses' => array('amount' => $dalam_Proses, 'count' => $dalam_Proses, 'color' => '#2196F3'),
    'Diluluskan'   => array('amount' => $diluluskan, 'count' => $diluluskan, 'color' => '#4caf50'),
    'Aktif'        => array('amount' => $aktif, 'count' => $aktif, 'color' => '#00bcd4'),
    'Ditolak'      => array('amount' => $ditolak, 'count' => $ditolak, 'color' => '#ff9800'),
    'Dibatalkan'   => array('amount' => $dibatalkan, 'count' => $dibatalkan, 'color' => '#f44336'),
    'Suspended'    => array('amount' => $suspended, 'count' => $suspended, 'color' => '#9e9e9e'),
);
?>

    <head>
        <title>Rekening Pembiayaan</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            body {
                font-family: Poppins, sans-serif;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .container {
                display: flex;
                flex-direction: column;
                gap: 20px;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 12px;
                width: 100%;
                box-sizing: border-box;
                margin: 0 auto;
            }

            @media (min-width: 768px) {
                .container {
                    flex-direction: row;
                    align-items: stretch;
                }

                .chart-container {
                    flex: 1;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .summary {
                    flex: 1;
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 20px;
                }
            }

            .chart-container {
                width: 100%;
                max-width: 400px;
                height: auto;
                margin: 0 auto;
            }

            .summary {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }

            .summary-box {
                padding: 10px;
                border-radius: 8px;
                background: #fafafa;
            }

            .summary-box strong {
                display: block;
                margin-bottom: 8px;
            }

            .amount {
                font-size: 1.5em;
                margin: 5px 0;
            }

            hr {
                border: none;
                border-top: 1px solid #ddd;
                margin: 10px 0;
            }

            .header {
                display: flex;
                justify-content: space-between;
                padding: 10px 20px;
                box-sizing: border-box;
                flex-wrap: wrap;
            }

            .header h2 {
                margin: 0;
            }
        </style>
    </head>

    <body>
<div class="header">
    <h5>Ringkasan</h5>
</div>

<div class="container">
    <div class="chart-container">
        <canvas id="paymentChart"></canvas>
    </div>

    <div class="summary">
        <?php foreach ($entries as $key => $data): ?>
            <div class="summary-box">
                <strong><?php echo $key; ?></strong>
                <hr>
                <div class="amount" style="color: <?php echo $data['color']; ?>;"><?php echo $data['amount']; ?></div>
                <div><?php echo $data['count']; ?> data (<?php echo $totalEntries > 0 ? round(($data['count'] / $totalEntries) * 100) : 0; ?>%)</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?

print '<div class="mb-3 row m-1 mt-4">
<div>Pencarian Berdasarkan
			<select name="by" class="form-select-sm mt-3">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';
print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm mt-3">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
			Cabang/Zona
			<select name="dept" class="form-select-sm mt-3" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
    print '	<option value="' . $deptVal[$i] . '" ';
    if ($dept == $deptVal[$i]) print ' selected';
    print '>' . $deptList[$i];
}
print '		</select>&nbsp;</div>
</div>

<div class="mb-3 row m-1">
<div>
Filter Status
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -</option>';

for ($i = 0; $i < count($pembiayaanStatusList); $i++) {
    print '<option value="' . $pembiayaanStatusVal[$i] . '"';
    if ((string)$filter === (string)$pembiayaanStatusVal[$i]) print ' selected';
    print '>' . $pembiayaanStatusList[$i] . '</option>';
}

print '	</select>
&nbsp;&nbsp;&nbsp; Jenis Proses
			<select name="jenis_proses" class="form-select-sm">
				<option value="3">Luluskan</option>
				<option value="7">Tolak</option>
			</select>&nbsp;
			<input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');"></div>
</div>';


print '
<div class="table-responsive">
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
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
print '				</select> setiap halaman.
					</td>
				</tr>
			</table>
		</td>
	</tr>';
if ($GetLoan->RowCount() <> 0) {
    $bil = $StartRec;
    $cnt = 1;
    print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nama Anggota</td>
						<td nowrap align="center">Nomor Anggota</td>
						<td nowrap align="center">Kartu Identitas</td>
						<td nowrap align="left">Nama Pembiayaan</td>
						<td nowrap align="right">Jumlah Pembiayaan</td>
						<td nowrap align="center">Jangka Waktu</td>
						<td nowrap align="right">Bayaran Bulanan</td>
						<td nowrap align="right">Baki</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="center">Tanggal Pengajuan</td>';
    if (($IDName == 'superadmin') or ($IDName == 'admin')) {
        if ($filter == '' || $filter == '1') {
            print '<td colspan="2" nowrap align="center">&nbsp;</td>';
        }
    }

    print '</tr>';
    while (!$GetLoan->EOF && $cnt <= $pg) {
        $status = $GetLoan->fields('status');
        $colorStatus = "Data";
        if ($status == 1) $colorStatus = "text-warning";
        if ($status == 3) $colorStatus = "text-success";
        if ($status == 7) $colorStatus = "text-danger";
        if ($status == 9) $colorStatus = "text-secondary";
        if ($status == 11) $colorStatus = "text-info";

        $loanType = $GetLoan->fields('loanType');
        $sql  = "SELECT name FROM general WHERE ID = " . tosql($loanType, "Number");
        $rs   = $conn->Execute($sql);
        $namaPembiayaan = ($rs && !$rs->EOF) ? $rs->fields['name'] : '';

        $memberID = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields('userID'), "Text"));
        $memberName = dlookup("users", "name", "userID=" . tosql($GetLoan->fields('userID'), "Text"));
        $newIC = dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields('userID'), "Text"));
        $blackList = dlookup("userdetails", "BlackListID", "userID=" . tosql($GetLoan->fields('userID'), "Text"));

        $loanAmt = number_format($GetLoan->fields('loanAmt'), 2, ',', '.');
        $monthlyPymt = number_format($GetLoan->fields('monthlyPymt'), 2, ',', '.');
        $outstandingAmt = number_format($GetLoan->fields('outstandingAmt'), 2, ',', '.');

        $statusLabel = '';
        if ($status == 1) $statusLabel = 'Dalam Proses';
        if ($status == 3 && $GetLoan->fields('outstandingAmt') > 0) $statusLabel = 'Aktif';
        elseif ($status == 3) $statusLabel = 'Diluluskan';
        if ($status == 7) $statusLabel = 'Ditolak';
        if ($status == 9) $statusLabel = 'Dibatalkan';
        if ($status == 11) $statusLabel = 'Suspended';

        print ' <tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetLoan->fields('loanID')) . '">
							<a href="' . $sFileRef . '&pk=' . tohtml($GetLoan->fields('loanID')) . '">
							' . strtoupper($memberName) . '</a>';
        if ($blackList == 1) {
            print '<img src="images/delete.jpg" width="15" height="15">';
        }

        print '</td>
						<td class="Data" align="center">' . $memberID . '</td>
						<td class="Data" align="center">' . convertNewIC($newIC) . '</td>
						<td class="Data" align="left">' . $namaPembiayaan . '</td>
						<td class="Data" align="right">Rp ' . $loanAmt . '</td>
						<td class="Data" align="center">' . $GetLoan->fields('loanPeriod') . ' bulan</td>
						<td class="Data" align="right">Rp ' . $monthlyPymt . '</td>
						<td class="Data" align="right">Rp ' . $outstandingAmt . '</td>
						<td class="Data" align="center"><font class="' . $colorStatus . '">' . $statusLabel . '</font></td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetLoan->fields('applyDate')) . '</td>';
        if (($IDName == 'superadmin') or ($IDName == 'admin')) {
            if ($filter == '' || $filter == '1') {
                print '<td>
						<a href="' . $sFileRef . '&pk=' . $GetLoan->fields['loanID'] . '" title="Perbarui">
							<i class="mdi mdi-lead-pencil text-warning" style="font-size: 19px; margin-top: 9px;"></i>
						</a>
					</td>';

                print '<td>
							<a href="' . $sFileName . '&action=delete&pk=' . $GetLoan->fields['loanID'] . '" onClick="return confirm(\'Adakah anda pasti untuk hapus data ini?\')" title="Hapus">
								<i class="fas fa-trash-alt text-danger" style="font-size: 19px; margin-top: 9px;"></i>
							</a>
						</td>';
            }
        }
        print '
					</tr>';
        $cnt++;
        $bil++;
        $GetLoan->MoveNext();
    }

    $GetLoan->Close();
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
			<td class="textFont">Jumlah Data : <b>' . $GetLoan->RowCount() . '</b></td>
		</tr>';
} else {
    if ($q == "") {
        print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
    } else {
        print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian Data "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
    }
}
print '
</table></td></tr></table></div>
</form>';
?>
<script>
    const ctx = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Dalam Proses', 'Diluluskan', 'Aktif', 'Ditolak', 'Dibatalkan', 'Suspended'],
            datasets: [{
                data: [<?php echo $dalam_Proses; ?>, <?php echo $diluluskan; ?>, <?php echo $aktif; ?>, <?php echo $ditolak; ?>, <?php echo $dibatalkan; ?>, <?php echo $suspended; ?>],
                backgroundColor: ['#2196F3', '#4caf50', '#00bcd4', '#ff9800', '#f44336', '#9e9e9e']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
</script>
<?
include("footer.php");

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

	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form sudah dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }

	        if(count==0) {
	          alert(\'Tolong pilih data yang mau di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' data dipilih ingin di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }

	function ITRActionButtonClickStatus(v) {
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Tolong pastikan nama form dibuat.!\');
	      } else {
	        count=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				count++;
	          }
	        }

	        if(count==0) {
	          alert(\'Tolong pilih data yang mau di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' data dipilih ingin di\' + v + \'kan?\')) {
	          	e.action.value = v;
	          	e.submit();
			  }
	        }
	      }
	    }

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
