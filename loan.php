<?php

/*********************************************************************************
 *		   Project		:	iKOOP.com.my
 *		   Filename		:	loan.php
 *		   Date			:	06/12/2015
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($filter))	$filter = "ALL";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <>	2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=loan&mn=906';
$sFileRef  = '?vw=biayaDokumen&mn=906';
$title	   = "Daftar Pengajuan Pembiayaan";

//$conn->debug=1;
//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['pk'])) {
	$pk = $_GET['pk'];

	// Ensure pk is a number
	if (is_numeric($pk)) {
		$CheckLoan = ctLoan("", $pk);
		if ($CheckLoan->RowCount() == 1) {
			// Check loan status
			if ($CheckLoan->fields['status'] < 3) {
				$sWhere = "loanID=" . tosql($pk, "Number");

				// Get loan number
				$loanNo = dlookup("loans", "loanNo", $sWhere);

				// Delete loan record
				$sSQL = "DELETE FROM loans WHERE " . $sWhere;
				$rs = &$conn->Execute($sSQL);

				// Delete related loan docs
				$sSQL = "DELETE FROM loandocs WHERE " . $sWhere;
				$rs = &$conn->Execute($sSQL);

				// Log the activity
				$userID = dlookup("loans", "userID", "loanID=" . $pk);
				$strActivity = $_POST['Submit'] . ' Permohonan Pembiayaan ' . $loanNo . ' Telah Dihapuskan Bagi Anggota - ' . $userID;
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
			} else {
				print '<script>alert("Hanya permohonan belum siap proses boleh dihapus!");</script>';
			}
		}
	}
}
//--- End	: deletion based on	checked	box	-------------------------------------------------------

if ($action	== "batal") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckLoan = ctLoan("", $pk[$i]);
		if ($CheckLoan->RowCount() == 1) {
			$statusloan = $CheckLoan->fields(status);
			//	if ($CheckLoan->fields(status) == 3) {
			$updatedBy	= get_session("Cookie_userName");
			$updatedDate = date("Y-m-d H:i:s");
			$sSQL =	'';
			$sWhere	= '';
			$sWhere	= '	loanID	= ' . $pk[$i];
			$sSQL	= '	UPDATE loans ';
			$sSQL	.= ' SET ' . //isCancel 	cancelBy 	cancelDate 	cancelNote
				' status	= 5 ' .
				' ,isCancel	= 1 ' .
				' ,cancelNote =' . tosql($sebab, "Text") .
				' ,cancelBy	=' . tosql($updatedBy, "Text") .
				' ,cancelDate='	. tosql($updatedDate, "Text");
			$sSQL .= ' WHERE ' . $sWhere;
			//print '<br>'.$sSQL;
			$rs	= &$conn->Execute($sSQL);

			$loanNo	= dlookup("loans", "loanNo", "loanID=" . $pk[$i]);

			$strActivity = $_POST['Submit'] . ' Pembiayaan Telah Dibatalkan - ' . $loanNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
			//	} else {
			/*
				print '<script>alert("Hanya permohonan yang sudah diluluskan boleh dibatalkan!");</script>';
			//}*/
		}
	}
}

//--- Begin	: change application status	-------------------------------------------------------
if ($action	== "ubah") {
	//r01 $str = implode("," ,$pk	);
	$updatedBy	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$sSQL =	'';
	$sWhere	= '';
	$sWhere	= '	loanID	in (' . $str	. ')';
	$sWhere	= '	loanID	= ' . $pk[0];
	$sSQL	= '	UPDATE loans ';
	$sSQL	.= ' SET ' .
		' status	=' . tosql(0, "Text") .
		' ,updatedBy	=' . tosql($updatedBy, "Text") .
		' ,updatedDate='	. tosql($updatedDate, "Text");
	$sSQL .= ' WHERE ' . $sWhere;
	//print '<br>'.$sSQL;
	$rs	= &$conn->Execute($sSQL);

	$sWhere	= '	loanID	= ' . $pk[0];
	$sSQL =	"DELETE	FROM loandocs WHERE " . $sWhere;
	//print '<br>'.$sSQL;
	$rs	= &$conn->Execute($sSQL);

	$userID		= dlookup("loans", "userID", "loanID=" . $pk[0]);
	$sSQL	= "INSERT INTO loandocs (" .
		"loanID," .
		"userID)" .
		" VALUES (" .
		"'" . $pk[0] . "'," .
		"'" . $userID . "')";
	//print '<br>'.$sSQL;
	$rs = &$conn->Execute($sSQL);

	$loanNo		= dlookup("loans", "loanNo", "loanID=" . $pk[0]);

	$strActivity = $_POST['Submit'] . ' Pembiayaan Tukar Status Menjadi Dalam Proses - ' . $loanNo;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
}
//--- End -------------------------------------------------------

//--- Prepare department list
$deptList =	array();
$deptVal  =	array();
$sSQL =	"	SELECT a.departmentID, b.code as deptCode, b.name as deptName
			FROM userdetails a,	general	b
			WHERE a.departmentID = b.ID
			AND	  a.status = 1
			GROUP BY a.departmentID";
$rs	= &$conn->Execute($sSQL);
if ($rs->RowCount()	<> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

//$GetLoan = ctLoanStatusDept($q,$by,$filter,$dept);

//function ctLoanStatusDept($q,$by,$status,$dept,$id = 0) {
$status = $filter;

$sSQL = "";
$sWhere = "  loanID is not null";
//where statements
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . $dept;
	$sWhere .= " AND A.userID = B.userID";
}

if ($status <> "ALL") $sWhere .= " AND A.status = " . $status;

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.userID = B.userID";
		$sWhere .= " AND B.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID = C.userID";
		$sWhere .= " AND C.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.userID = B.userID";
		$sWhere .= " AND B.newIC like '%" . $q . "%'";
	}
}

if ($id) $sWhere .= " AND A.loanType in (" . $id . ") ";

$sWhere = " WHERE (" . $sWhere . ")";

//fields selection
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec =	$GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);


print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=loanApply&mn=906\'"/>
</div>';

// Fetch loan counts by status
$dalam_Proses = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 0")->fields['count'];
$disediakan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 1")->fields['count'];
$disemak = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 2")->fields['count'];
$ditolak = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 4")->fields['count'];

// Calculate total entries dynamically
$totalEntries = $dalam_Proses + $disediakan + $disemak + $ditolak;

// Define the entries array
$entries = array(
	'Dalam Proses' => array('amount' => $dalam_Proses, 'count' => $dalam_Proses, 'color' => '#2196F3'),
	'Disediakan'   => array('amount' => $disediakan, 'count' => $disediakan, 'color' => '#ff9800'),
	'Disemak'      => array('amount' => $disemak, 'count' => $disemak, 'color' => '#4caf50'),
	'Ditolak'      => array('amount' => $ditolak, 'count' => $ditolak, 'color' => '#f44336')
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
					<div><?php echo $data['count']; ?> entri (<?php echo $totalEntries > 0 ? round(($data['count'] / $totalEntries) * 100) : 0; ?>%)</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?

	print '
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">

	<div class="mb-3 row m-1 mt-5">
            <div>
			Cari Berdasarkan
			<select name="by" class="form-select-sm">';
	if ($by	== 1)	print '<option value="1" selected>Nomor Anggota</option>';
	else print '<option	value="1">Nomor Anggota</option>';
	// if ($by	== 2)	print '<option value="2" selected>Nama Anggota</option>';	else print '<option	value="2">Nama Anggota</option>';
	if ($by	== 3)	print '<option value="3" selected>Kartu Identitas</option>';
	else print '<option	value="3">Kartu Identitas</option>';
	print '	</select>
			<input type="text" name="q"	value="" maxlength="50" size="20" class="form-control-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
			Cabang/Zona
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option	value="">- Semua -';
	for ($i	= 0; $i	< count($deptList); $i++) {
		print '	<option	value="' . $deptVal[$i] . '" ';
		if ($dept == $deptVal[$i]) print ' selected';
		print '>' . $deptList[$i];
	}
	print '	</select>
		</div>
	</div>
	<div class="mb-3 row m-1">
                        <div class="col-md-8">
					Pilihan Proses 
					<select	name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
	print '<option value="ALL">Semua';
	for ($i	= 0; $i	< count($biayaList); $i++) {
		//if($i	<> 3 ||	$i<>4 ){
		print '	<option	value="' . $biayaVal[$i] . '" ';
		if ($filter	== $biayaVal[$i]) print	' selected';
		print '>' . $biayaList[$i];
		//}
	}
	print '</select>&nbsp;';

	if ($filter == 3) print '&nbsp;&nbsp;Cetak dokumen proses :&nbsp;<input type="button" class="btn btn-sm btn-secondary" value="Cetak" onClick="ITRActionButtonDoc();">&nbsp;';

	if ($filter	== 4) print 'Ubah ke proses kembali &nbsp;<input type="button" class="btn btn-sm btn-primary" value="Ubah"	onClick="ITRActionButtonUbah();">';

	print '</div>
					<div class="col-md-4 pull-right" style="align:right !important;">
					<!--input 4ype="button" class="but" value="Status" onClick="ITBActionButtonStatus();"-->
					Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
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

	print '				</select>setiap halaman.
					</div>
				</div>';

	if (get_session("Cookie_groupID") == 2 && $filter == 3) {
		print '<div>
			Batal Kelulusan :&nbsp;<input type="button" class="btn btn-sm btn-secondary" value="Batal" onClick="ITRActionButtonClick(\'batal\');">&nbsp;Sebab:&nbsp;<input type="textx" name="sebab" value="" maxlength="60" size="50" class="Data form-controlx">
			</div>&nbsp;';
	}
	print '	</table>
		</td>
	</tr>';
	if ($GetLoan->RowCount() <>	0) {
		$bil = $StartRec;
		$cnt = 1;
		print '
		<tr	valign="top" >
			<td	valign="top">
				<table border="0" cellspacing="1" cellpadding="2" class="table table-sm table-striped" width="100%">
					<tr class="table-primary">
						<td	nowrap></td>
						<td	nowrap>Nama Pembiayaan</td>
						<td	nowrap>Nomor - Nama Anggota</td>
						<td	nowrap align="right">Jumlah (RM)</td>
						<td	nowrap align="center">Jangka Waktu (Bulan)</td>
						<td	nowrap align="right">Bayaran Bulanan (RM)</td>
						<td	nowrap align="center">Tanggal ';
		if ($filter == "ALL" || $filter == "0") {
			print 'Pengajuan';
		} else {
			// Dynamic lookup for status name
			$statusName = 'Status Tidak Dikenali'; // Default in case status not found
			$index = array_search($status, $biayaVal);  // Find index in $biayaVal
			if ($index !== false) {  // If status is found in $biayaVal
				$statusName = $biayaList[$index];  // Get corresponding status name from $biayaList
			}
			print $statusName;  // Print the status name
		}
		print ' </td>
	<td	nowrap align="center">Status</td>		
					<td	nowrap align="center" colspan="5"></td>	
					</tr>';
		$amtLoan = 0;
		while (!$GetLoan->EOF && $cnt <= $pg) {
			$jabatan = dlookup("userdetails", "departmentID", "userID="	. tosql($GetLoan->fields(userID), "Text"));
			$blackList = dlookup("userdetails", "BlackListID", "userID="	. tosql($GetLoan->fields(userID), "Text"));

			//$amt = dlookup("general",	"c_Maksimum", "ID="	. tosql($GetLoan->fields(loanType),	"Number"));
			// new amount
			$amt =	number_format(tosql($GetLoan->fields(loanAmt), "Number"), 2);
			$amtLoan = $amtLoan	+ tosql($GetLoan->fields(loanAmt), "Number");
			$status	= $GetLoan->fields(status);
			$colorStatus = "Data";
			if ($status	== 0) $colorStatus = "text-success";
			if ($status	== 1 || $status == 2) $colorStatus = "text-info";
			if ($status	== 3) $colorStatus = "greenText";
			if ($status	== 4) $colorStatus = "redText";
			if ($status	== 7) $colorStatus = "text-warning";
			if ($status	== 9) $colorStatus = "text-body";

			//------------------------------------------------------------------------------------
			$sSQL = "";
			$sSQL = "SELECT	* FROM loans 
			where loanID = '" . $GetLoan->fields(loanID) . "'";
			$rs = &$conn->Execute($sSQL);
			$userID = $rs->fields(userID);
			$houseLoan = $rs->fields(houseLoan);
			$AnsuranBaru = $rs->fields(monthlyPymt);
			$loanAmt = $rs->fields(loanAmt);

			$key = "A";
			$keyB = "B";
			$payID = "1553";
			$payIDSEWA = '1845';
			$payIDOthers = '1847';
			$payIDdiv = '1846';
			$payIDKWSP = '1563';
			$payIDSOC = '1564';
			$payIDCCRIS = '1839';
			$payIDCCRISPAT = '1848';

			$checkStatesJ = "SELECT SUM(loanAmt)as Loan FROM loans 
	WHERE userID = '" . $userID . "' AND status IN (3)";
			$rscheckStatesJ = $conn->Execute($checkStatesJ);
			$Loan = $rscheckStatesJ->fields(Loan);


			$checkStatesA = "SELECT SUM(amt)as Jum FROM userstates 
	WHERE userID = '" . $userID . "' AND payType ='A'";
			$rscheckStatesA = $conn->Execute($checkStatesA);
			$AmtPendapatan = $rscheckStatesA->fields(Jum);

			$checkStatesAL = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDSEWA . "'"; //sewa
			$rscheckStatesAL = $conn->Execute($checkStatesAL);
			$sewa = $rscheckStatesAL->fields(amt);

			$checkStatesOTH = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDOthers . "'"; //sewa
			$rscheckStatesOTH = $conn->Execute($checkStatesOTH);
			$Others = $rscheckStatesOTH->fields(amt);

			$checkStatesALDIV = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDdiv . "'"; //sewa
			$rscheckStatesALDIV = $conn->Execute($checkStatesALDIV);
			$DIV = $rscheckStatesALDIV->fields(amt);

			$checkStatesKWSP = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDKWSP . "'"; //sewa
			$rscheckStatesALKWSP = $conn->Execute($checkStatesKWSP);
			$KWSP = $rscheckStatesALKWSP->fields(amt);

			$checkStatesSOC = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDSOC . "'"; //sewa
			$rscheckStatesSOC = $conn->Execute($checkStatesSOC);
			$SOCSO = $rscheckStatesSOC->fields(amt);

			$checkStatesCCRISPAT = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDCCRISPAT . "'"; //sewa
			$rscheckStatesCCRISPAT = $conn->Execute($checkStatesCCRISPAT);
			$CCRISPAT = $rscheckStatesCCRISPAT->fields(amt);


			$checkStatesCCRIS = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDCCRIS . "'"; //sewa
			$rscheckStatesCCRIS = $conn->Execute($checkStatesCCRIS);
			$CCRIS = $rscheckStatesCCRIS->fields(amt);

			$checkStatesAB = "SELECT * FROM userstates 
	WHERE payID = '" . $payID . "'
	AND userID =  '" . $userID . "'";
			$rscheckStatesAB = $conn->Execute($checkStatesAB);


			$checkStates = "SELECT SUM(amt)as Jum FROM userstates 
	WHERE payType = 'B' 
	AND userID = '" . $userID . "'";
			$rscheckStates = $conn->Execute($checkStates);

			$gajiKasar = $AmtPendapatan + $sewa + $DIV + $Others;
			$JumKWSPSCO = $KWSP + $SOCSO;
			$AmtPotongan = $rscheckStates->fields(Jum);
			$AmtPendapatanPokok = $rscheckStatesAB->fields(amt);
			$jumlahPotNew = $AmtPotongan + $AnsuranBaru;
			$Elaun = $AmtPendapatan - $AmtPendapatanPokok;
			$GajiBersih = $AmtPendapatan - $jumlahPotNew;
			$gug = $jumlahPotNew + $CCRISPAT;
			$STUTORI  = $gug - ($JumKWSPSCO + $CCRIS);
			$pendBersih = $gajiKasar - $JumKWSPSCO;
			$hadPeratus = $STUTORI / $pendBersih * 100;

			$rowID = "row-" . $GetLoan->fields('loanID');
			//-------------------------------------------------------------------------------
			print '	<tr>
						<td	class="Data" align="right">' . $bil	. '&nbsp;</td>';
			if ($filter == "3" || $filter == "4") {
				print '<td	class="Data"><input	type="checkbox" class="form-check-input" name="pk[]"	value="' . tohtml($GetLoan->fields(loanID)) . '">
						<a href="' . $sFileRef . '&pk=' . tohtml($GetLoan->fields(loanID)) . '">&nbsp;';
			} else {
				print '<td><a href="' . $sFileRef . '&pk=' . tohtml($GetLoan->fields(loanID)) . '">';
			}

			if ($GetLoan->fields(status) <> 5)
				print $adfdf = dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
			else
				print $GetLoan->fields(cancelNote);

			print '</td>
						<!--td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetLoan->fields(loanID)) . '">
						<a href="' . $sFileRef . '?pk=' . tohtml($GetLoan->fields(loanID)) . '">'
				. dlookup("general",	"code",	"ID=" .	tosql($GetLoan->fields(loanType), "Number")) . '-'
				. sprintf("%010d", $GetLoan->fields(loanID)) . '</td-->
						<td	class="Data">'
				. dlookup("userdetails",	"memberID",	"userID=" .	tosql($GetLoan->fields(userID),	"Text")) . '-'
				. dlookup("users", "name", "userID="	. tosql($GetLoan->fields(userID), "Text")) . '';

			if ($blackList == 1) {
				print '<img src="images/delete.jpg" width="15" height="15"> </td>';
			}


			print '
						<!--td	class="Data" align="center">' . dlookup("general",	"name",	"ID=" .	tosql($jabatan,	"Number")) . '</td-->
						<td	class="Data" align="right">' . $amt . '</td>
						<td	class="Data" align="center">' . $GetLoan->fields(loanPeriod) . '</td>
							<td	class="Data" align="right">' . number_format($GetLoan->fields(monthlyPymt), 2) . '</td>';
			print '	<td	class="Data" align="center">';
			if ($filter == "ALL" || $filter == 0) {
				print toDate("d/m/yy", $GetLoan->fields(applyDate));
			} elseif ($filter == 1) {

				$sql = "select prepareDate FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
				$Get =  &$conn->Execute($sql);
				if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(prepareDate));
			} elseif ($filter == 2) {

				$sql = "select reviewDate FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
				$Get =  &$conn->Execute($sql);
				if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(reviewDate));
			} elseif ($filter == 3) {

				$sql = "select ajkDate2 FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
				$Get =  &$conn->Execute($sql);
				if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(ajkDate2));
			} elseif ($filter == 4) {

				$sql = "select ajkDate2 FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
				$Get =  &$conn->Execute($sql);
				if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(ajkDate2));
			} elseif ($filter == 5) {

				//$sql = "select prepareDate FROM `loandocs` where loanID = '".$GetLoan->fields(loanID)."'"; 	
				//$Get =  &$conn->Execute($sql);
				//if ($Get->RowCount() > 0) print toDate("d/m/yy",$Get->fields(prepareDate));
				print toDate("d/m/yy", $GetLoan->fields(cancelDate));
			}

			print '</td>
				<td class="Data" align="center"><font class="' . $colorStatus . '">';

			// Display status using the dynamic lookup
			$statusName = 'Status Tidak Dikenali'; // Default in case status not found
			$index = array_search($status, $biayaVal);  // Find index in $biayaVal
			if ($index !== false) {  // If status is found in $biayaVal
				$statusName = $biayaList[$index];  // Get corresponding status name from $biayaList
			}
			print $statusName;  // Print the status name dynamically

			print '</font></td>';

			print '<td class="Data" align="center">';
			if ($filter == 0) {
				print '<i class="fas fa-file-alt text-info" title="lihat DSR" style="font-size: 1.1rem; cursor: pointer; margin-top: 10px;" onClick=window.open("DSRCetak.php?loanID=' . $GetLoan->fields(loanID) . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");></i></td>
							&nbsp;<td class="Data" align="center"><button 
								 style="border: none; background-color: transparent; cursor: pointer;" 
								 onclick="printContent(\'?vw=biayaDokumen&mn=906&pk=\' + encodeURIComponent(\'' . $GetLoan->fields(loanID) . '\'));">
								 <i class="mdi mdi-printer text-primary" style="font-size: 24px;"></i>
							 </button></td>
							&nbsp;<td class="Data" align="center"><i class="mdi mdi-lead-pencil text-warning" title="kemaskini" style="font-size: 1.4rem; cursor: pointer;" onClick="window.location.href=\'?vw=biayaDokumen&mn=906&pk=' . $GetLoan->fields(loanID) . '\'"></i></td>';
				if (($IDName == 'superadmin') or ($IDName == 'admin')) {
					print '<td align="center">
							<a href="?vw=loan&mn=906&action=delete&pk=' . $GetLoan->fields['loanID'] . '" onClick="return confirm(\'Adakah anda pasti untuk hapus anggota ini?\')" title="Hapus">
								<i class="fas fa-trash-alt fa-lg text-danger" style="display: inline-flex; align-items: center; justify-content: center; margin-top: 10px;"></i>
							</a>
							</td>';
				}
			}
			print '<td>
						 <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#' . $rowID . '" aria-expanded="false" onclick="toggleArrow(this)" style="padding-top: 5px; padding-right: 0; padding-bottom: 0; padding-left: 0; font-size: 1.2rem; box-shadow: none; outline: none; display: flex; align-items: center;">
							 <i class="fas fa-chevron-down text-secondary"></i>
						 </button>
					</td>
			 </tr>
			 <tr class="collapse" id="' . $rowID . '">
				 <td colspan="13" class="Data">
					 <div class="alert alert-secondary mt-2">
						 <ul>
							 <li>Nombor Rujukan : ' . $GetLoan->fields(loanNo) . '</li>
							 <li>Kartu Identitas : ' . dlookup("userdetails", "newIC",	"userID=" .	tosql($GetLoan->fields(userID),	"Text")) . '</li>
							 <li>Rate : ' . $GetLoan->fields(kadar_u) . ' %</li>
							 <li>DSR : ' . number_format($hadPeratus, 2) . ' %</li>
						 </ul>
					 </div>
				 </td>
			</tr>';
			$cnt++;
			$bil++;
			$GetLoan->MoveNext();
		}

		$GetLoan->Close();
		print '		<!--tr>
						<td	class="DataB" align="right"	colspan="5"	height="20">Jumlah Pinjaman&nbsp;</td>
						<td	class="DataB" align="right">' . number_format($amtLoan, 2, '.', ',') . '&nbsp;</td>
						<td	class="DataB" colspan="2">&nbsp;</td>
					</tr-->
				</table>
			</td>
		</tr>
		<tr>
			<td>';
		if ($TotalRec >	$pg) {
			print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont"	width="100%">';
			if ($TotalRec %	$pg	== 0) {
				$numPage = $TotalPage;
			} else {
				$numPage = $TotalPage +	1;
			}
			print '<tr><td class="textFont"	valign="top" align="left">Data Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				if (is_int($i / 10)) print	'<br />';
				print '<A href="' . $sFileName . '?&StartRec=' . (($i	* $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&filter=' . $filter . '">';
				print '<b><u>' . (($i	* $pg) - $pg + 1) . '-' . ($i *	$pg) . '</u></b></a>&nbsp;&nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}
		print '
			</td>
		</tr>
		<tr>
			<td	class="textFont">Jumlah Data :	<b>' . $GetLoan->RowCount()	. '</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr	size=1"></td></tr>';
		} else {
			print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Carian	rekod "' . $q . '" tidak jumpa	-</b><hr size=1"></td></tr>';
		}
	}
	print '
</table>
</form></div>';
	?>
	<script>
		const ctx = document.getElementById('paymentChart').getContext('2d');
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ['Dalam Proses', 'Disediakan', 'Disemak', 'Ditolak'],
				datasets: [{
					data: [<?php echo $dalam_Proses; ?>, <?php echo $disediakan; ?>, <?php echo $disemak; ?>, <?php echo $ditolak; ?>],
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
	include("footer.php");

	print '
<script	language="JavaScript">
	var	allChecked=false;
	function ITRViewSelectAll()	{
		e =	document.MyForm.elements;
		allChecked = !allChecked;
		for(c=0; c<	e.length; c++) {
		  if(e[c].type=="checkbox" && e[c].name!="all")	{
			e[c].checked = allChecked;
		  }
		}
	}

	function ITRActionButtonClick(v) {
		  e	= document.MyForm;
		  if(e==null) {
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		  }	else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
			  if(e.elements[c].name=="pk[]"	&& e.elements[c].checked) {
				count++;
			  }
			}

			if(count==0) {
			  alert(\'Sila pilih rekod yang	hendak di\'	+ v	+\'kan.\');
			} else {
			  if(confirm(count + \'	rekod hendak di\' +	v +\'kan. Adakah anda pasti?\')) {
				e.action.value = v;
				e.submit();
			  }
			}
		  }
		}

	function ITRActionButtonStatus() {
		e =	document.MyForm;
		if(e==null)	{
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked)	{
					count++;
					pk = e.elements[c].value;
				}
			}

			if(count !=	1) {
				alert(\'Sila pilih satu	rekod sahaja untuk kemaskini status\');
			} else {
				window.open(\'loanStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');
			}
		}
	}

	function ITRActionButtonUbah() {
		e =	document.MyForm;
		if(e==null)	{
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked)	{
					count++;
					pk = e.elements[c].value;
				}
			}

			if(count !=	1) {
				alert(\'Sila pilih satu	rekod sahaja untuk proses kembali\');
			} else {
				e.action.value = \'ubah\';
				e.submit();
			}
		}
	}
	
	function ITRActionButtonDoc() {
		e =	document.MyForm;
		if(e==null)	{
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked)	{
					count++;
					pk = e.elements[c].value;
				}
			}

			if(count !=	1) {
				alert(\'Sila pilih satu	rekod cetakan dokumen proses!\');
			} else {
				window.open(\'biayaDokumenPrint.php?action=print&pk=\' + pk,\'status\',\'top=50,left=50,width=850,height=550,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');
			}
		}
	}

	function doListAll() {
		c =	document.forms[\'MyForm\'].pg;
		document.location =	"' . $sFileName	. '?&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter=' . $filter . '";
	}

	function ITRActionButtonClickStatus(v) {
		  var strStatus="";
		  e	= document.MyForm;
		  if(e==null) {
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		  }	else {
			count=0;
			j=0;
			for(c=0; c<e.elements.length; c++) {
			  if(e.elements[c].name=="pk[]"	&& e.elements[c].checked) {
				pk = e.elements[c].value;
				strStatus =	strStatus +	":"	+ pk;
				count++;
			  }
			}

			if(count==0) {
			  alert(\'Sila pilih rekod yang	hendak di\'	+ v	+ \'kan.\');
			} else {
			  if(confirm(count + \'	rekod hendak di\' +	v +	\'kan?\')) {
			  //e.submit();
			  window.location.href ="memberAktif.php?pk=" +	strStatus;
			  }
			}
			    }
		}
	
	function printContent(url) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                var printWindow = window.open("", "_blank");
                printWindow.document.open();
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.print();
            })
            .catch(error => {
                console.error("Error fetching content:", error);
            });
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
