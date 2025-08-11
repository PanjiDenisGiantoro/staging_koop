<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanTable.php
 *          Date 		: 	06/06/2016
 *********************************************************************************/

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($dept))		$dept = "";
if (!isset($yr)) $yr	= date("Y");
if (!isset($yy)) $yy	= date("Y");
if (!isset($mm)) $mm	= date("m");

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
$IDName = get_session("Cookie_userName");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=loanTable&mn=906';
$sFileRef  = '?vw=loanEdit&mn=906';
$title     = "Pengurusan Pembiayaan Diluluskan";

//----print penyata tahunan Pembiayaan
if ($action <> "") {
	print '	<script>';
	if ($action == "Penyata") {
		print ' rptURL = "loanYearly.php?yr=' . $yr . '&loanID=' . $pk[0] . '";';
		print ' window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");';
	}
	print ' </script>';
}

if ($action	== "finish") {

	for ($i = 0; $i < count($pk); $i++) {

		$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
			FROM loans where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sqlLoan);
		if ($Get->RowCount() > 0) {
			$loanAmt = $Get->fields(loanAmt);
			$totUntung = $Get->fields(totUntung);
			$loanType = $Get->fields(loanType);
			//$loanNo = $Get->fields(loanNo);
		}


		$rs->Close();

		$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

		$sql = "select rnoBond FROM loandocs where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $bond = $Get->fields(rnoBond);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID = '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID <> '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);

		//if($bakiPkk <=0 && $bakiUnt >= $totUntung){
		//r01 $str = implode("," ,$pk	);
		$userID = dlookup("loans", "userID", "loanID=" . tosql($pk[$i], "Text"));
		$updatedBy	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL =	'';
		$sWhere	= '';
		$sWhere	= '	loanID	in (' . $str	. ')';
		$sWhere	= '	loanID	= ' . $pk[$i];
		$sSQL	= '	UPDATE loans ';
		$sSQL	.= ' SET ' .
			' status	=' . tosql(9, "Text") .
			' ,selesaiBy	=' . tosql($updatedBy, "Text") .
			' ,selesaiDate='	. tosql($updatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		//print '<br>'.$sSQL;
		$rs	= &$conn->Execute($sSQL);

		$loanNo = dlookup("loans", "loanNo", "loanID=" . tosql($pk[$i], "Text"));
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Pembiayaan Diluluskan Dipindahkan Ke Pembiayaan Selesai - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "', '" . $updatedDate . "', '" . $updatedBy . "', '2')";
		$rs = &$conn->Execute($sqlAct);

		//penama database
		$GetGurrantor = "SELECT * FROM general WHERE c_gurrantor IN (0,1)";
		$rSs	= &$conn->Execute($GetGurrantor);
		if ($rSs) {
			$gurrantorRow = $rSs->FetchRow();

			$c_gurrantorValue = $gurrantorRow['c_gurrantor'];
			$gtr = $c_gurrantorValue;
		}


		// status update potongan gaji
		$sqlLoan2 = "SELECT * FROM loans where loanID = '" . $pk[$i] . "'";
		$rs2	= &$conn->Execute($sqlLoan2);
		$loanNo = $rs2->fields(loanNo);

		$sqlLoan3 = "SELECT * FROM potbulan where loanID = '" . $loanNo . "'";
		$rs3	= &$conn->Execute($sqlLoan3);
		//$lDpot = $rs3->fields(ID);
		if ($rs3->RowCount() > 0) {
			$sSQL =	'';
			$sWhere	= '';
			$sWhere	= '	ID	= ' . $rs3->fields(ID);
			$sSQL	= '	UPDATE potbulan ';
			$sSQL	.= ' SET ' .
				' status	=' . tosql(9, "Text") .
				' ,isSettleStat	=' . tosql(1, "Text") .
				' ,selesaiBy	=' . tosql($updatedBy, "Text") .
				' ,selesaiDate='	. tosql($updatedDate, "Text");
			$sSQL .= ' WHERE ' . $sWhere;
			//print '<br>'.$sSQL;
			$rs	= &$conn->Execute($sSQL);
		}
		//}else{ 
	} //for close
	print '<script>alert("PEMBIAYAAN TELAH SELESAI");</script>';
	//} test sekjap

}
//--- End -------------------------------------------------------
//--- Prepare department list

if ($action	== "Lapuk") {

	for ($i = 0; $i < count($pk); $i++) {

		$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
			FROM loans where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sqlLoan);
		if ($Get->RowCount() > 0) {
			$loanAmt = $Get->fields(loanAmt);
			$totUntung = $Get->fields(totUntung);
			$loanType = $Get->fields(loanType);
			//$loanNo = $Get->fields(loanNo);
		}

		$sql = "SELECT c_Deduct,c_gurrantor FROM general where ID = '" . $loanType . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

		$sql = "SELECT rnoBond FROM loandocs where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $bond = $Get->fields(rnoBond);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID = '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID <> '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);

		//if($bakiPkk <=0 && $bakiUnt >= $totUntung){
		//r01 $str = implode("," ,$pk	);
		$userID = dlookup("loans", "userID", "loanID=" . tosql($pk[$i], "Text"));
		$updatedBy	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL =	'';
		$sWhere	= '';
		$sWhere	= '	loanID	in (' . $str	. ')';
		$sWhere	= '	loanID	= ' . $pk[$i];
		$sSQL	= '	UPDATE loans ';
		$sSQL	.= ' SET ' .
			' status	=' . tosql(7, "Text") .
			' ,selesaiBy	=' . tosql($updatedBy, "Text") .
			' ,selesaiDate='	. tosql($updatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		//print '<br>'.$sSQL;
		$rs	= &$conn->Execute($sSQL);

		$loanNo = dlookup("loans", "loanNo", "loanID=" . tosql($pk[$i], "Text"));
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			"VALUES ('Pembiayaan Diluluskan Dipindahkan Ke Hutang Lapuk - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
		$rs = &$conn->Execute($sqlAct);

		// status update potongan gaji
		$sqlLoan2 = "SELECT * FROM loans where loanID = '" . $pk[$i] . "'";
		$rs2	= &$conn->Execute($sqlLoan2);
		$loanNo = $rs2->fields(loanNo);

		$sqlLoan3 = "SELECT * FROM potbulan where loanID = '" . $loanNo . "'";
		$rs3	= &$conn->Execute($sqlLoan3);
		//$lDpot = $rs3->fields(ID);
		if ($rs3->RowCount() > 0) {
			$sSQL =	'';
			$sWhere	= '';
			$sWhere	= '	ID	= ' . $rs3->fields(ID);
			$sSQL	= '	UPDATE potbulan ';
			$sSQL	.= ' SET ' .
				' status	=' . tosql(7, "Text") .
				' ,selesaiBy	=' . tosql($updatedBy, "Text") .
				' ,selesaiDate='	. tosql($updatedDate, "Text");
			$sSQL .= ' WHERE ' . $sWhere;
			//print '<br>'.$sSQL;
			$rs	= &$conn->Execute($sSQL);
		}
		//}else{ 
	} //for close
	print '<script>alert("PEMBIAYAAN HUTANG LAPUK TELAH DIAKTIFKAN");</script>';
	//} test sekjap

}


$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status IN (1,4) 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}


$idloan  = array();
// to exclude vehicle $sSQL = "SELECT a.ID FROM `general` a, general b WHERE a.c_Deduct = b.ID AND b.code NOT LIKE '51%'";
$sSQL = "SELECT a.ID FROM `general` a, general b WHERE a.c_Deduct = b.ID AND b.code NOT LIKE '51%'";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($idloan, $rs->fields(ID));
		$rs->MoveNext();
	}
	$idloan = implode(",", $idloan);
}

//$GetLoan = ctLoanStatusDept($q,$by,"3",$dept,$idloan);
// used by : loan.php, loanTable.php, loanList.php
//function ctLoanStatusDept($q,$by,$status,$dept,$id = 0) {
//	global $conn;
$status = 3;

$sSQL = "";
$sWhere = " A.status = " . tosql($status, "Number");
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
	$sWhere .= " AND A.userID = B.userID ";
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND B.memberID like '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID = C.userID ";
		$sWhere .= " AND C.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND A.loanNo = '" . $q . "'";
	}
}

$sWhere = " WHERE (" . $sWhere . ")";
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A ";
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
<input type="hidden" name="filter" value="' . $filter . '">
<div class="table-responsive">
<h5 class="card-title">' . strtoupper($title) . '</h5>';


// Fetch loan counts by status
$pengesahanD = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE isApproved = 1 AND status = 3")->fields['count'];
$pengesahanB = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE isApproved = 0 AND status = 3")->fields['count'];

$pgbAktif = array();
$pgbTidakAktif = array();

while (!$GetLoan->EOF && $cnt <= $pg) {
    $bond = dlookup("loandocs", "rnobond", "loanID=" . tosql($GetLoan->fields("loanID"), "Text"));
    $PGB = dlookup("potbulan", "status", "bondNo='" . $bond . "'");

    if ($PGB <> "") {
        array_push($pgbAktif, $bond);
    } else {
        array_push($pgbTidakAktif, $bond);
    }

    $GetLoan->MoveNext();
}


// Calculate total entries dynamically
$totalEntries = $pengesahanD + $pengesahanB + count($pgbAktif) + count($pgbTidakAktif);

// Define the entries array
$entries = array(
	'Pengesahan Dibuat'  => array('amount' => $pengesahanD, 'count' => $pengesahanD, 'color' => '#2196F3'),
	'Tiada Pengesahan'   => array('amount' => $pengesahanB, 'count' => $pengesahanB, 'color' => '#ff9800'),
	'PGB Aktif'          => array('amount' => count($pgbAktif), 'count' => count($pgbAktif), 'color' => '#4caf50'),
	'PGB Tidak Aktif'    => array('amount' => count($pgbTidakAktif), 'count' => count($pgbTidakAktif), 'color' => '#f44336')
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

	<div class="container mb-4">
		<div class="chart-container">
			<canvas id="paymentChart"></canvas>
		</div>

		<div class="summary">
			<?php foreach ($entries as $key => $data): ?>
				<div class="summary-box">
					<strong><?php echo $key; ?></strong>
					<hr>
					<div class="amount" style="color: <?php echo $data['color']; ?>;"><?php echo $data['amount']; ?></div>
					<div><?php echo $data['count']; ?> entri (<?php echo $totalEntries > 0 ? round(($data['count'] / $totalEntries) * 100) : 0; ?>%)</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?

	print '<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" >	
    <tr valign="top">
	   	<td align="left" >
			Carian Melalui 
			<select name="by" class="form-select-sm">';
	if ($by	== 1)	print '<option value="1" selected>Nombor Anggota</option>';
	else print '<option	value="1">Nombor Anggota</option>';
	if ($by	== 2)	print '<option value="2" selected>Nama Anggota</option>';
	else print '<option	value="2">Nama Anggota</option>';
	if ($by	== 3)	print '<option value="3" selected>Nombor Rujukan</option>';
	else print '<option	value="3">Nombor Rujukan</option>';

	print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">	
			Cawangan/Zon
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
	for ($i = 0; $i < count($deptList); $i++) {
		print '	<option value="' . $deptVal[$i] . '" ';
		if ($dept == $deptVal[$i]) print ' selected';
		print '>' . $deptList[$i];
	}
	print '		</select>&nbsp;&nbsp;           
		</td>
	</tr>';

	$status = 3;

$sSQL = "";
$sWhere = " A.status = " . tosql($status, "Number");
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
	$sWhere .= " AND A.userID = B.userID ";
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND B.memberID like '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID = C.userID ";
		$sWhere .= " AND C.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND A.loanNo = '" . $q . "'";
	}
}

$sWhere = " WHERE (" . $sWhere . ")";
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

	if ($GetLoan->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '
		<tr valign="top" class="textFont">
			<td>
				<br/><table width="100%">
					<tr>
						<td  class="textFont">
			<input type="button" class="btn btn-sm btn-primary" value="Lejer" onClick="ITRActionButtonClick(\'loanYearly\');">';

		if (($IDName == 'superadmin') or ($IDName == 'admin')) {
			print ' 
			<input type="button" class="btn btn-sm btn-success" value="Pengesahan Pembiayaan Selesai" onClick="ITRActionButtonFinish(\'finish\');">';
		}

		if (($IDName == 'superadmin') or ($IDName == 'admin')) {
			print ' 
			
			<input type="button" value="Kemaskini Penjamin" class="btn btn-warning btn-sm w-md waves-effect waves-light" onClick="ITRActionButtonClick2(\'ubah\')">';
		}

		print '
			<!--input type="button" class="btn btn-sm btn-danger" value="Hutang Lapuk" onClick="ITRActionButtonFinish(\'Lapuk\');"-->
			<input type="button" class="btn btn-sm btn-info" value="Penjelasan Awal" onClick="ITRActionButtonClick(\'loanJadualFS\');">
			
			</td>
						<td align="right" class="textFont">
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
		if ($pg == 200)	print '<option value="200" selected>200</option>';
		else print '<option value="200">200</option>';
		if ($pg == 300)	print '<option value="300" selected>300</option>';
		else print '<option value="300">300</option>';
		if ($pg == 400)	print '<option value="400" selected>400</option>';
		else print '<option value="400">400</option>';
		if ($pg == 500)	print '<option value="500" selected>500</option>';
		else print '<option value="500">500</option>';
		if ($pg == 1000) print '<option value="1000" selected>1000</option>';
		else print '<option value="1000">1000</option>';
		print '				</select> setiap mukasurat.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-striped table-sm">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap><b>Nama Pembiayaan</b></td>
						<td nowrap><b>Nombor - Nama Anggota</b></td>
						<td nowrap align="right"><b>Jumlah Permohonan (RM)</b></td>
						<td nowrap align="right"><b>Bayaran Bulanan (RM)</b></td>
						<td nowrap><b>Surat Tawaran</b></td>						
						<td nowrap align="center"><b>Tarikh Baucer</b></td>
						<td nowrap align="center"><b>Status PGB</b></td>
						<td nowrap colspan="3" align="center"><b>&nbsp;</b></td>
					</tr>';
		$amtLoan = 0;
		while (!$GetLoan->EOF && $cnt <= $pg) {
			$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
			$amt = $GetLoan->fields('loanAmt');
			$amtLoan += $amt;
			$status = $GetLoan->fields(status);
			$colorStatus = "Data";
			if ($status == 1) $colorStatus = "greenText";
			if ($status == 2) $colorStatus = "redText";

			$startPymtDate = dlookup("loandocs", "rcreatedDate", "loanID=" . $GetLoan->fields(loanID));

			if ($startPymtDate != "") {
				$startPymtDate = toDate("d/m/Y", $startPymtDate);
			} else {
				$startPymtDate = "Proses Baucer";
			}


			//--------------
			$loanType				= $GetLoan->fields('loanType');
			$codegroup				= dlookup("general", "parentID", "ID=" . $loanType);

			$colorPen = "Data";
			if ($GetLoan->fields(isApproved) == 1) {
				$colorPen = "greenText";
				$pengesahan = "Pengesahan Dibuat";
			} else {
				$colorPen = "redText";
				$pengesahan = "Tiada Pengesahan";
			}

			$colorPen1 = "Data";
			if ($GetLoan->fields(opsyen_sah) == 1) {
				$colorPen1 = "blackText";
				$barang = "Tunai";
			} else if ($GetLoan->fields(opsyen_sah) == 2) {
				$colorPen1 = "blackText";
				$barang = "Komoditi";
			}

			$gtr = dlookup("general", "c_gurrantor", " ID = " . $loanType);
			$bond = dlookup("loandocs", "rnobond", "loanID=" . tosql($GetLoan->fields(loanID), "Text"));

			$PGB = dlookup("potbulan", "status", "bondNo='" . $bond . "'");

			$colorPenPGB = "Data";
			if ($PGB == 1 or $PGB == 3) {
				$colorPenPGB = "greenText";
				$pengesahanPGB = "Ada";
			} else {
				$colorPenPGB = "redText";
				$pengesahanPGB = "Tiada";
			}

			if ($bond == '') $bond = 'AJK';

			if ($codegroup <> 1638) {
				$table  = "?vw=loanJadual&mn=906&id=" . $GetLoan->fields(loanID);
			} else {
				$table = "?vw=loanJadual78NEW&mn=906&type=vehicle&page=view&id=" . $GetLoan->fields(loanID);
			}

			$rowID = "row-" . $GetLoan->fields('loanID');

			print '         <tr class="table-light">
						<td class="Data" align="right" height="25">' . $bil . '</td>
						<td class="Data">
						<input type="checkbox" class="form-check-input" name="pk[]" value="' . $GetLoan->fields('loanID') . '">&nbsp;'
				. '<input type="hidden" id="' . $GetLoan->fields('loanID') . '" value="' . $gtr . '">'
				. '<a href="' . $table . '">'
				. dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"))
				. '</a>'
				. '</td>
						<td class="Data">'
				. dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '-'
				. dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '</td>
						<td class="Data" align="right">' . number_format($GetLoan->fields(loanAmt), 2) . '</td>
                            <td class="Data" align="right">' . number_format($GetLoan->fields(monthlyPymt), 2) . '</td>
                            <td class="Data"><font class="' . $colorPen . '">' . $pengesahan . '&nbsp;</font>' . toDate("d/m/Y", $GetLoan->fields(approvedDate)) . '</td>';
			print '<td align="center">' . $startPymtDate . '</td>';
			if (dlookup("potbulan", "status", "bondNo='" . $bond . "'") <> "") {
				print '<td class="text-primary" align="center">Aktif</td>';
			} else {
				print '<td class="text-danger" align="center">Tidak Aktif</td>';
			}

			if ($GetLoan->fields(isApproved) == 1) {
				print '<td><i 
					 title="view contract" style="font-size: 1.1rem; cursor: pointer;" onClick="window.location.href=\'?vw=tawaranSah2tawarruq&ID=' . $GetLoan->fields('loanID') . ';"></i></td>
                     <td><i class="mdi mdi-printer text-primary" title="Cetak" style="font-size: 1.4rem; cursor: pointer;" onClick=window.open("tawaranSah2tawarruq.php?ID=' . $GetLoan->fields(loanID) . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");></i></td>';
			} else {
				print '
                    <td colspan="2">&nbsp;</td>';
			}

			print '<td>
                            <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#' . $rowID . '" aria-expanded="false" onclick="toggleArrow(this)" style="padding-top: 5px; padding-right: 0; padding-bottom: 0; padding-left: 0; font-size: 1.2rem; box-shadow: none; outline: none; display: flex; align-items: center;">
                                <i class="fas fa-chevron-down text-secondary"></i>
                            </button>
                       </td>
                </tr>
                <tr class="collapse" id="' . $rowID . '">
                    <td colspan="11" class="Data">
                        <div class="alert alert-secondary mt-2">
                            <ul>
                                <li>Nombor Rujukan : ' . $GetLoan->fields(loanNo) . '</li>
                                <li>Rate : ' . $GetLoan->fields(kadar_u) . ' %</li>
                                <li>Tempoh : ' . $GetLoan->fields(loanPeriod) . ' Bulan</li>
                                <li>Nombor Bond : ' . $bond . '</li>
                            </ul>
                        </div>
                    </td>
                </tr>';
			$cnt++;
			$bil++;
			$GetLoan->MoveNext();
		}
		$GetLoan->Close();

		print '	</table>
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
			print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : ' . $TotalRec . '<br>';
			for ($i = 1; $i <= $numPage; $i++) {
				if (is_int($i / 10)) print '<br />';
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}

		print '
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Rekod : <b>' . $TotalRec . '</b></td>
		</tr-->';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	}
	print ' 
</table></div>
</form>';

	?>
	<script>
		const ctx = document.getElementById('paymentChart').getContext('2d');
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ['Pengesahan Dibuat', 'Tiada Pengesahan', 'PGB Aktif', 'PGB Tidak Aktif'],
				datasets: [{
					data: [<?php echo $pengesahanD; ?>, <?php echo $pengesahanB; ?>, <?php echo count($pgbAktif); ?>, <?php echo count($pgbTidakAktif); ?>],
					backgroundColor: ['#2196F3', '#ff9800', '#4caf50', '#f44336']
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

	print '
<script language="JavaScript">
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	
	function ITRActionButtonClick(rpt) {
	e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu pembiayaan !\');
			} else {
				if (rpt == "loanYearly")  {
					url = "loanYearly.php?pk="+ pk +"&yr=' . $yy . '";
				} else if (rpt == "loanJadualFS") {
					url = "loanJadualFS.php?pk="+ pk +"&yr=' . $yy . '";
				}
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			
			} 
		}
	}

	function ITRActionButtonClick2(rpt) {
		var e = document.MyForm;
	
		if (e == null) {
			alert(\'Sila pastikan nama form diwujudkan!\');
		} else {
			var count = 0;
			var pk;
			var loanType;
			
			
			for (var c = 0; c < e.elements.length; c++) {
				if (e.elements[c].name == "pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
					console.log(pk);
					const element = document.getElementById(pk).value;
					if(element == 0){
						alert(\'Pembiayaan ini tidak memerlukan penjamin!\');
						rpt = "error";
					}
				}
			}

			if (count !== 1) {
				alert(\'Sila pilih rekod pembiayaan yang hendak dikemaskini!\');
			} else {
				if (rpt === "ubah") {
					window.location.href = "?vw=biayaMohonJaminan&mn=906&pk=" + pk;
				}
			}
		}
	}


	function ITRActionButtonClick_o(v) {
		e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			
			if(count != 1) {
				alert(\'Sila pilih satu pembiayaan sahaja \');
			} else {
	            e.action.value = v;
	            e.submit();
			}
		}
	}

	
	function ITRActionButtonFinish(v) {
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
	          alert(\'Sila pilih rekod yang hendak Dilakukan.\');
	        } else {
	          if(confirm(count + \' rekod hendak Dilakukan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
			  }
	    }	   

	function toggleArrow(button) {
            const icon = button.querySelector(\'i\');
            if (icon.classList.contains(\'fa-chevron-down\')) {
                icon.classList.remove(\'fa-chevron-down\');
                icon.classList.add(\'fa-chevron-up\');
            } else {
                icon.classList.remove(\'fa-chevron-up\');
                icon.classList.add(\'fa-chevron-down\');
	      }
	    }	   
	
</script>';

	include("footer.php");
