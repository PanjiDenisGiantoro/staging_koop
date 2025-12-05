<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	member.php
 *          Date 		:
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "0";
//if (!isset($filter))	$filter="0";
if (!isset($dept))		$dept = "";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}


$sFileName = '?vw=loansimpanan1&mn=902';
$sFileRef = '?vw=biayaDokumensimpanan&mn=902';
$title = "Rekening Simpanan";


$IDName = get_session("Cookie_userName");
//--- Begin : deletion based on checked box -------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['pk'])) {
    $pk = $_GET['pk']; // Get the primary key from the URL
    $sWhere = "";



    function ctMemberDetail($id)
    {
        global $conn;
        $sSQL = "";
        $sWhere = "";
        $sWhere = " a.userID = " . tosql($id, "Text");
        $sWhere .= " and a.userID = b.userID ";
        $sWhere = " WHERE (" . $sWhere . ")";
        $sSQL = "
SELECT
    a.userID,
    a.loginID,
    a.name,
    a.applyDate,
    a.groupID,
    a.email,
    a.isActive,
    b.memberID,
    b.memberDate,
    b.newIC,
    b.oldIC,
    b.placeBirth,
    b.dateBirth,
    b.staftNo,
    b.job,
    b.slipNo,
    b.dateStarted,
    b.grossPay,
    b.netPay,
    b.address,
    b.stateID,
    b.employerName,
    b.departmentID,
    b.billingInd,
    b.officeNo,
    b.homeNo,
    b.mobileNo,
    b.society1,
    b.societyNo1,
    b.society2,
    b.societyNo2,
    b.society3,
    b.societyNo3,
    -- b.w_name3, b.w_ic3, b.w_relation3, b.w_address3, b.w_contact3,
    b.unitShare,
    b.paymentID,
    b.bankName,
    b.bankBranch,
    b.bankAccount,
    b.status      AS userdetails_status,
    b.approvedDate,
    b.rejectedDate,
    b.updatedDate,
    b.updatedBy,
    b.remark,
    c.id          AS depositorID,
    c.accountNumber,
    c.Code_simpanan,
    c.balance,
    c.nominal_simpanan,
    c.sumber_dana,
    c.status      AS depositor_status,
    c.dateApply,
    c.updateUserID,
    c.dateUpdateUserID
FROM users a
         JOIN userdetails b
              ON a.userID COLLATE latin1_general_ci = b.userID COLLATE latin1_general_ci
         JOIN depositoracc c
              ON c.UserID COLLATE latin1_general_ci = a.userID COLLATE latin1_general_ci";
        $sSQL = $sSQL . $sWhere;
        $rs = &$conn->Execute($sSQL);
        return $rs;
    }
    // Assuming ctMemberDetail returns a valid result object
    $CheckUser = ctMemberDetail($pk);

    // Check if user exists and row count is 1
    if ($CheckUser->RowCount() == 1) {
        // Check if the user is inactive (status == 0)
        if ($CheckUser->fields['status'] == 0) {
            $sWhere = "id=" . tosql($pk, "Text");

            // Delete from users table
            $sSQL = "DELETE FROM depositoracc WHERE " . $sWhere;
            $rs = $conn->Execute($sSQL);


            // Log activity (deletion)
            $strActivity = $_POST['Submit'] . ' Hapus Rekening Anggota - ' . $CheckUser->fields['id'];
            activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
        } else {
            // If user status is not 0, show an alert that deletion is not allowed
            print '<script>alert("Pengguna ' . $CheckUser->fields['name'] . ' - tidak boleh dihapuskan...!");</script>';
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
array_push($deptList, "Bersara");
array_push($deptVal, "BSR");
if ($dept == "BSR") {
    $filter = 4;
    $dept = "";
}

//$GetMember = ctMemberStatusDept($q,$by,$filter,$dept);
//	global $conn;
//function ctMemberStatusDept($q,$by,$status,$dept) {
$sSQL = "";
//	$sWhere = " a.userID = b.userID AND b.status = " . tosql($filter,"Number");
$sWhere = " a.userID = b.userID ";

if ($dept <> "") {
    $sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($filter <> "ALL") $sWhere ;

if ($q <> "") {
    if ($by == 1) {
        $sWhere .= " AND b.memberID like '%" . $q . "%'";
    } else if ($by == 2) {
        $sWhere .= " AND a.name like '%" . $q . "%'";
    } else if ($by == 3) {
        $sWhere .= " AND b.newIC like '%" . $q . "%'";
    }
}
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "
SELECT
    a.userID,
    a.loginID,
    a.name,
    a.applyDate,
    a.groupID,
    a.email,
    a.isActive,
    b.memberID,
    b.memberDate,
    b.newIC,
    b.oldIC,
    b.placeBirth,
    b.dateBirth,
    b.staftNo,
    b.job,
    b.slipNo,
    b.dateStarted,
    b.grossPay,
    b.netPay,
    b.address,
    b.stateID,
    b.employerName,
    b.departmentID,
    b.billingInd,
    b.officeNo,
    b.homeNo,
    b.mobileNo,
    b.society1,
    b.societyNo1,
    b.society2,
    b.societyNo2,
    b.society3,
    b.societyNo3,
    -- b.w_name3, b.w_ic3, b.w_relation3, b.w_address3, b.w_contact3,
    b.unitShare,
    b.paymentID,
    b.bankName,
    b.bankBranch,
    b.bankAccount,
    b.status      AS userdetails_status,
    b.approvedDate,
    b.rejectedDate,
    b.updatedDate,
    b.updatedBy,
    b.remark,
    c.id          AS depositorID,
    c.accountNumber,
    c.Code_simpanan,
    c.balance,
    c.nominal_simpanan,
    c.sumber_dana,
    c.status      AS depositor_status,
    c.dateApply,
    c.updateUserID,
    c.dateUpdateUserID
FROM users a
         JOIN userdetails b
              ON a.userID COLLATE latin1_general_ci = b.userID COLLATE latin1_general_ci
         JOIN depositoracc c
              ON c.UserID COLLATE latin1_general_ci = a.userID COLLATE latin1_general_ci";
//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) desc";
$GetMember = &$conn->Execute($sSQL);

$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=loanApplysimpanan&mn=902\'"/>
</div>';

// Fetch users counts by status
$dalam_Proses = $conn->Execute("SELECT COUNT(userID) AS count FROM depositoracc WHERE STATUS = 0")->fields['count'];
$diluluskan = $conn->Execute("SELECT COUNT(userID) AS count FROM depositoracc WHERE STATUS = 1")->fields['count'];
$ditolak = $conn->Execute("SELECT COUNT(userID) AS count FROM depositoracc WHERE STATUS = 2")->fields['count'];

// Calculate total entries dynamically
$totalEntries = $dalam_Proses + $diluluskan + $ditolak ;

// Define the entries array
$entries = array(
    'Dalam Proses' => array('amount' => $dalam_Proses, 'count' => $dalam_Proses, 'color' => '#2196F3'),
    'Disetujui'   => array('amount' => $diluluskan, 'count' => $diluluskan, 'color' => '#4caf50'),
    'Ditolak'      => array('amount' => $ditolak, 'count' => $ditolak, 'color' => '#ff9800'),
);
?>

    <head>
        <title>Payment Summary</title>
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
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

            /* .btn { background: #f1f1f1; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; } */
        </style>
    </head>

    <body>
<div class="header">
    <h5>Ringkasan</h5>
    <!-- <button class="btn">Last 12 months</button> -->
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
Jenis
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
//print '<option value="ALL">Semua';
for ($i = 0; $i < count($statusList); $i++) {
    if ($i == 0 || $i == 2) {
        if ($statusVal[$i] < 5) {
            print '	<option value="' . $statusVal[$i] . '" ';
            if ($filter == $statusVal[$i]) print ' selected';
            print '>' . $statusList[$i];
        }
    }
}
print '	</select>&nbsp;';

print '          
			<!--input type="button" class="btn btn-sm btn-primary" value="Status" onClick="ITRActionButtonStatus();"-->
			 <input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');"></div>
</div>';


print '
<div class="table-responsive">    
<!--table border="1" cellspacing="1" cellpadding="3" width="100%" align="center" class="table"-->
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
if ($GetMember->RowCount() <> 0) {
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
						<td nowrap align="left">Nomor Account</td>
						<td nowrap align="left">Nama Simpanan</td>
						<td nowrap align="right">Nominal Simpanan</td>
						<td nowrap align="left">Sumber Dana</td>
						<td nowrap align="right">Saldo</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="center">Tanggal Pengajuan</td>';
    if (($IDName == 'superadmin') or ($IDName == 'admin')) {
        if ($filter == 0) {
            print '<td colspan="2" nowrap align="center">&nbsp;</td>';
        }
    }

    print '</tr>';
    while (!$GetMember->EOF && $cnt <= $pg) {
        $status = $GetMember->fields(depositor_status);
        $colorStatus = "Data";
        if ($status == 0) $colorStatus = "text-warning";
        if ($status == 1) $colorStatus = "text-success";
        if ($status == 2) $colorStatus = "text-danger";

        $code = $GetMember->fields['Code_simpanan'];
        $sql  = "SELECT name FROM general WHERE ID = " . tosql($code, "Number");
        $rs   = $conn->Execute($sql);
        $namaSimpanan = ($rs && !$rs->EOF) ? $rs->fields['name'] : '';

        print ' <tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(userID)) . '">
							<a href="' . $sFileRef . '&pk=' . tohtml($GetMember->fields(userID)) . '">
							' . strtoupper($GetMember->fields(name)) . '</a>';
        if ($GetMember->fields(BlackListID) == 1) {
            print '<img src="images/delete.jpg" width="15" height="15"> </td>';
        }

        $nominalSimpanan = number_format($GetMember->fields('nominal_simpanan'), 2, ',', '.');
        $sumberDana = $GetMember->fields('sumber_dana') ? $GetMember->fields('sumber_dana') : '-';
        $saldo = number_format($GetMember->fields(balance), 2, ',', '.');

        print '
						<td class="Data" align="center">' . $GetMember->fields(memberID) . '</td>
						<td class="Data" align="center">' . convertNewIC($GetMember->fields(newIC)) . '</td>
						<td class="Data" align="left">' . $GetMember->fields(accountNumber) . '</td>
						<td class="Data" align="left">' . $namaSimpanan. '</td>
						<td class="Data" align="right">Rp ' . $nominalSimpanan . '</td>
						<td class="Data" align="left">' . $sumberDana . '</td>
						<td class="Data" align="right">Rp ' . $saldo . '</td>
						<td class="Data" align="center"><font class="' . $colorStatus . '">' . $statusList[$status] . '</font></td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields(applyDate)) . '</td>';
        if (($IDName == 'superadmin') or ($IDName == 'admin')) {
            if ($filter == 0) {
                if ($verified == 1) {
                    print '<td>						
								<i class="mdi mdi-account-check text-primary" style="font-size: 25px;" title="Telah Disahkan"></i>
							</td>';
                } else {
                    print '<td>
						<a href="javascript:void(0);"" onClick="openPopup(this);" title="Verifikasi Anggota" data-pk="' . $GetMember->fields(depositorID) . '">
								<i class="mdi mdi-account-check text-warning" style="font-size: 25px;"></i>
							</a>
							</td>';
                }

                print '<td>
							<a href="?vw=deleteloansimpanan&mn=902&ID=' . $GetMember->fields['depositorID'] . '" onClick="return confirm(\'Adakah anda pasti untuk hapus anggota ini?\')" title="Hapus">
								<i class="fas fa-trash-alt text-danger" style="font-size: 19px; margin-top: 9px;"></i>
							</a>
						</td>';
            }
        }
        print '
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
			<td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
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
            labels: ['Dalam Proses', 'Disetujui', 'Ditolak'],
            datasets: [{
                data: [<?php echo $dalam_Proses; ?>, <?php echo $diluluskan; ?>, <?php echo $ditolak; ?>],
                backgroundColor: ['#2196F3', '#4caf50', '#ff9800']
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
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
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
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Tolong pastikan nama form dibuat.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				pk = e.elements[c].value;
				strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Tolong pilih data yang mau di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' data dipilih ingin di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="?vw=memberStatus&pk=" + strStatus;
			  }
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Tolong pastikan nama form dibuat.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Tolong pilih satu data saja untuk update status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}

	function openPopup(linkElement) {
		// Get the pk value from the clicked links data-pk attribute
		let pk = linkElement.getAttribute(\'data-pk\');
		
		console.log(\'Selected PK:\', pk); // Debugging: Log the pk value to ensure its correct

		if (pk) {
			// Get screen width and height
			var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
			var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

			// Set popup size
			var width = 600;
			var height = 400;

			// Calculate the position to center the window
			var left = (screenWidth - width) / 2;
			var top = (screenHeight - height) / 2;

			// Create the popup window with the pk value in the URL
			var url = \'verify_simpanan.php?pk=\' + encodeURIComponent(pk);
			console.log(\'Popup URL:\', url); // Debugging: Log the URL to be opened

			// Open the popup window
			var popup = window.open(url, \'popupWindow\', \'width=\' + width + \',height=\' + height + \',left=\' + left + \',top=\' + top + \',scrollbars=yes\');

			// Check if popup is blocked
			if (!popup) {
				alert(\'Popup was blocked. Please allow popups for this site.\');
			}
		} else {
			// If no pk value is found, show an alert
			alert(\'No valid userID (pk) found.\');
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
