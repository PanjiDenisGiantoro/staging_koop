<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: loanVehicleTable.php
 *			Date 		: 12/06/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
?>

<head>
	<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
</head>
<?php
$updatedDate = date("Y-m-d H:i:s");
$updatedBy 	= get_session("Cookie_userName");
$updatedID = get_session("Cookie_groupID");

if (!isset($type))			$type = 'vehicle';
if (!isset($page))			$page = 'list';

if (!isset($page_id))		$page_id = 1;
if (!isset($page_range))	$page_range = 8;
if (!isset($page_total))	$page_total = 1;
if (!isset($page_start))	$page_start = 1;
if (!isset($page_end))		$page_end = 1;

if (!isset($rec_start))		$rec_start = 0;
if (!isset($rec_end))		$rec_end = 0;
if (!isset($rec_per_page))	$rec_per_page = 50;
if (!isset($rec_total))		$rec_total = 0;

if (!isset($sort))			$sort = 'DESC';
if (!isset($id))			$id = '';
if (!isset($loan_code))		$loan_code = '';
if (!isset($by))			$by = '1';
if (!isset($keyword))		$keyword = '';
if (!isset($dept))			$dept = '';

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$idloan  = array();
$sSQL = "SELECT a.ID FROM `general` a, general b WHERE a.c_Deduct = b.ID AND b.code LIKE '51%'";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($idloan, $rs->fields(ID));
		$rs->MoveNext();
	}
}

if ($type == 'vehicle') {
	$strLoanName .= 'Pembiayaan';
	$strLoanTypeList = $idloan; //array(1552, 1550, 1548, 1546, 1538);
}
$strHeaderTitle = '<b>&nbsp;MAKLUMAT PEMBIAYAAN&nbsp;</b>';

if ($page == 'list') {
	$strTypeNameList	= array('Nomor Anggota', 'Nama Anggota', 'No KTP Baru');
	$strTypeValueList	= array(1, 2, 3);
	$strRecordCountList	= array(5, 10, 20, 30, 40, 50, 100);
	$strSQLSortList		= array('DESC', 'ASC');
	$strDeptNameList	= array();
	$strDeptValueList	= array();
	array_push($strDeptNameList, '- semua -');
	array_push($strDeptValueList, '');
	$sSQL = 'SELECT a.departmentID, b.code as deptCode, b.name as deptName'
		. ' FROM userdetails a, general b'
		. ' WHERE a.departmentID = b.ID'
		. ' AND   a.status = 1'
		. ' GROUP BY a.departmentID';
	$GetDeptData = &$conn->Execute($sSQL);
	if ($GetDeptData->RowCount() <> 0) {
		while (!$GetDeptData->EOF) {
			array_push($strDeptNameList, strtoupper($GetDeptData->fields('deptName')));
			array_push($strDeptValueList, $GetDeptData->fields('departmentID'));
			$GetDeptData->MoveNext();
		}
	}
}

$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, $page, $page_id, $rec_per_page, $sort, $id, $keyword, $by, $dept);

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<form name="MyForm" action="' . $strActionPage . '" method="post">'
	. '<table class="table table-bordered table-striped table-sm" style="font-size: 10pt;" border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

if ($page <> 'list') {
	$strTemp .=
		'<tr>'
		. '<td colspan="2">'
		. '<table cellpadding="0" cellspacing="6">';

	$sSQL = 'SELECT * FROM general WHERE category=\'C\'';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strLoanCodeList = array();
		$strLoanNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strLoanCodeList[$nCount] = $GetData->fields('code');
			$strLoanNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}

	$sSQL =
		'SELECT * FROM loans a, loandocs b'
		. ' WHERE a.loanID=' . $id
		. ' AND   a.loanID = b.loanID';
	$GetData = $conn->Execute($sSQL);

	$sSQL2 =
		'SELECT year(b.rcreatedDate) as year, month(rcreatedDate) as month FROM loans a, loandocs b'
		. ' WHERE a.loanID=' . $id
		. ' AND   a.loanID = b.loanID';
	$GetData2 = $conn->Execute($sSQL2);

	$strFieldNameList = array('Nama', 'Nomor Anggota', 'Jenis', 'Jumlah', 'No Loan', 'Tarikh Kelulusan', 'Nombor Bond', 'Tahun Potongan', 'Bulan Potongan');

	if ($GetData->RowCount() <> 0) {
		$strStartDate = toDate('d/m/Y', $GetData->fields('approvedDate'));
		//$approvedDate = $GetData->fields('approvedDate');
		//.... tambahan data 
		$loanType = $GetData->fields('loanNo');
		$loanNo = $GetData->fields('loanType');
		$NoBond = $GetData->fields('rnoBond');
		$startPymentYear =  $GetData2->fields('year');
		$startPymentMonth =  $GetData2->fields('month') + 1;

		if ($GetData2->fields('month') == 12) {
			$startPymentYear = $startPymentYear + 1;
			$startPymentMonth = $GetData2->fields('month') - 11;
		};

		$fLoanAmount	= $GetData->fields('loanAmt');
		$fProfitRate	= $GetData->fields('kadar_u');
		$nLoanMonth		= $GetData->fields('loanPeriod');
		$nLoanYear		= $nLoanMonth / 12;

		// Loan calculation
		if ($type == 'vehicle') {
			$fProcessFeeRate = 0.5;
			$fProfitAmount = ($fProfitRate * 0.01) * $fLoanAmount * $nLoanYear; //5250 = 3.5 * 0.01 * 30,000 * 5
			$fProcessFee = ($fProcessFeeRate * 0.01) * $fLoanAmount; //150=0.5*0.01*30,000
			$fPaymentAmount = $fLoanAmount + $fProfitAmount; //35,250=30,000+5250
			$fMonthPayment = $fPaymentAmount / $nLoanMonth;	//587.5=35,250/60
			$fAllMonthPayment = RoundCurrency($fMonthPayment) * ($nLoanMonth - 1); // Except final month
			//34,662.5 =587.5*(60-1)
			$fFinalMonthPayment = $fPaymentAmount - (RoundCurrency($fMonthPayment) * ($nLoanMonth - 1));
			//587.5=35,250-34,662.5
		} else if ($type == 'item') {
			$fProcessFeeRate = 0.5;
			$fProfitAmount = 0.0;
			$fProcessFee = 0.0;
			$fPaymentAmount = 0.0;
			$fMonthPayment = 0.0;
			$fAllMonthPayment = 0.0;
			$fFinalMonthPayment = 0.0;
		} else if ($type == 'school') {
			$fProcessFeeRate = 0.5;
			$fProfitAmount = 0.0;
			$fProcessFee = 0.0;
			$fPaymentAmount = 0.0;
			$fMonthPayment = 0.0;
			$fAllMonthPayment = 0.0;
			$fFinalMonthPayment = 0.0;
		} else if ($type == 'personal') {
			$fProcessFeeRate = 0.5;
			$fProfitAmount = 0.0;
			$fProcessFee = 0.0;
			$fPaymentAmount = 0.0;
			$fMonthPayment = 0.0;
			$fAllMonthPayment = 0.0;
			$fFinalMonthPayment = 0.0;
		}

		$strFieldDataList = array(
			strtoupper(dlookup("users", "name", "userID=" . tosql($GetData->fields('userID'), "Text"))),
			dlookup("userdetails", "memberID", "userID=" . tosql($GetData->fields('userID'), "Text")),
			$strLoanName,
			'RM&nbsp;' . $fLoanAmount,
			$loanType,
			$strStartDate,
			$NoBond,
			$startPymentYear,
			$startPymentMonth
		);
	} else {
		$strFieldDataList = array('- Tiada -', '- Tiada -', '- Tiada -', '- Tiada -', '- Tiada -');
	}

	for ($i = 0; $i < count($strFieldNameList); $i++) {
		$strTemp .= '<tr><td align="right" valign="top"><b>' . $strFieldNameList[$i] . ' :</b></td><td> ' . $strFieldDataList[$i] . ' </td></tr>';
	}

	$strTemp .=
		'</table>'
		. '</td>'
		. '</tr>';

	if ($type == 'vehicle') {
		$strTemp .=
			'<tr><input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply" value="Potongan Gaji" onclick="PageRefresh();"/>&nbsp;<input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply_pat" value="Potongan Akuan Tabungan" onclick="PageRefresh();"/> </td>';
	}

	$strTemp .=
		'<tr valign="top" class="textFont">'
		. '<td>'
		//.'<table width="100%" cellpadding="0" cellspacing="0">'
		//.PageSelection($_SERVER['PHP_SELF'], $strNameList, $strPageList)
		//.'</table>'
		. '<hr size="1px">'
		. '</td>'
		. '</tr>';
	$strTemp .=
		'<tr valign="top" >'
		. '<td valign="top" colspan="2">';

	$strFieldNameList[0]	= array('&nbsp;<br>&nbsp;', '&nbsp;', '&nbsp;');
	$strFieldWidthList[0]	= array('', '80', '80');

	$strFieldNameList[1]	= array('Tahun', 'Bulan', '&nbsp;', 'Untung Tahunan', 'Untung Bulanan', 'Pokok Tahunan', 'Pokok Bulanan', 'Jumlah Tahunan', 'Jumlah Bulanan');
	$strFieldWidthList[1]	= array('50', '50', '50', '80', '80', '80', '80', '80', '80');

	// Begin Table...
	$tableList[0][0] = array('<div align="left">Jumlah</div>', '', Currency($fLoanAmount));
	$tableList[0][1] = array('<div align="left">Tempoh (Tahun)</div>', '', $nLoanYear);
	$tableList[0][2] = array('<div align="left">Tempoh (Bulanan)</div>', '', $nLoanMonth);
	$tableList[0][3] = array('<div align="left">Kadar Keuntungan</div>', '', $fProfitRate . '%');
	$tableList[0][4] = array('<div align="left">Jumlah Keuntungan</div>', '', Currency($fProfitAmount));
	$tableList[0][5] = array('<div align="left">Bayaran Bulanan</div>', '', Currency($fMonthPayment));
	$tableList[0][6] = array('<div align="left">Cukai LHDN (' . $fProcessFeeRate . '%)</div>', '', Currency($fProcessFee));
	$tableList[0][7] = array('<div align="left">Jumlah + Untung</div>', '', Currency($fPaymentAmount));

	$fMonthPayment = RoundCurrency($fMonthPayment);
	$tableList[0][8] = array('<div align="left">Bulan 1 - ' . ($nLoanMonth - 1) . '</div>', Currency($fMonthPayment), Currency($fAllMonthPayment));
	$tableList[0][9] = array('<div align="left">Bulan ' . $nLoanMonth . '</div>', Currency($fFinalMonthPayment), Currency($fPaymentAmount));
	$tableList[0][10] = array('', '', Currency($fAllMonthPayment + $fFinalMonthPayment));

	for ($i = 0; $i < $nLoanYear; $i++) {
		$nSum1 += $i + 1;
	}
	/* //kiraan jadual pembiayaan kereta by akmal. chanes during uat
	#jumlah bulanan = roundup 50 sen((jumlah pokok + jumlah untung)/(jumlah bulan))
	#jumlah tahunan = jumlah bulanan X 12
	#untung tahun = jumlah untung X rate untung tahun
	#untung bulan = untung tahun / 12
	#pokok bulan = jumlah bulanan - untung bulan
	#pokok tahun = pokok bulan X 12
	*/
	for ($i = 0; $i < $nLoanYear; $i++) {
		$fProfitRateTemp = round((($nLoanYear - $i) / $nSum1), 2);
		$fBasicRateTemp = round((($i + 1) / $nSum1), 2);
		$fProfitTemp = $fProfitAmount * $fProfitRateTemp; //untung tahun
		$fProfitMonthTemp = $fProfitTemp / 12; //untung bulan
		$fBasicMonthTemp = $fMonthPayment - $fProfitMonthTemp; //pokok bulan 
		$fBasicTemp = $fBasicMonthTemp * 12; //pokok tahun
		$tableList[1][$i] = array(
			'<center>' . ($startPymentYear + $i) . '</center>',
			'<center>' . ($startPymentMonth) . '</center>',
			Currency($fProfitRateTemp),
			Currency($fProfitTemp),
			Currency($fProfitMonthTemp),
			Currency($fBasicTemp),
			Currency($fBasicMonthTemp),
			Currency($fMonthPayment * 12),
			Currency($fMonthPayment)
		);

		$fSum0 += $fProfitRateTemp;
		$fSum1 += $fProfitTemp;
		$fSum2 += $fBasicTemp;
		$fSum3 += $fProfitTemp + $fBasicTemp;
	}
	$tableList[1][$nLoanYear] = array('', '<center></center>', '', Currency($fSum1), '', Currency($fSum2), '', Currency($fSum3), '');
	// End Table '.$nSum1.'

	$strTemp .= '<table class="table table-bordered table-striped table-sm" style="font-size: 8pt;" cellpadding="0" cellspacing="0">'
		. '<tr>'
		. '<td valign="top">'
		. BeginDataField($strFieldNameList[0], $strFieldWidthList[0]) . ContentDataTable($tableList[0]) . EndDataField()
		. '</td>'
		. '<td>&nbsp;</td>'
		. '<td valign="top">'
		. BeginDataField($strFieldNameList[1], $strFieldWidthList[1]) . ContentDataTable($tableList[1]) . EndDataField()
		. '</td>'
		. '</tr>'
		. '</table>'
		. '</td>'
		. '</tr>';

	$strTemp .=
		'<tr valign="top" class="textFont">'
		. '<td>'
		. '<hr size="1px">'
		. '</td>'
		. '</tr>';
} else {
	$strTemp .=
		'<tr>'
		. '<td colspan="2">'
		. '<table cellpadding="0" cellspacing="6">';

	$strTemp .=	$strErrMsg
		. '<tr>'
		. '<td align="right" width="150"><b>Carian melalui</b></td>'
		. '<td>'
		. SelectForm('by', $by, $strTypeNameList, $strTypeValueList, '') . '&nbsp;'
		. '<input name="keyword" type="text" value="' . $keyword . '" maxlength="50" size="30">&nbsp;'
		. '<input name="submit" type="submit" value="Cari" onclick="PageRefresh();">'
		. '</td>'
		. '</tr>'
		. '<tr>'
		. '<td align="right"><b>Jabatan/Cawangan</b></td>'
		. '<td>' . SelectForm('dept', $dept, $strDeptNameList, $strDeptValueList, 'onchange="PageRefresh();"') . '</td>'
		. '</tr>';

	$strTemp .=
		'</table>'
		. '</td>'
		. '</tr>';

	$strCheckboxTemp = '<input name="all" type="checkbox" class="form-check-input" onclick="SelectAll()" style="padding:0px;margin:0px" />';

	$GetData = &$conn->Execute(GenerateSQLList());

	$strTemp .= '<tr><td colspan="2"><hr size="1px"></td></tr>';

	if ($GetData->RowCount() <> 0) {
		$strTemp .=
			'<tr valign="top" class="textFont">'
			. '<td>'
			. '<table width="100%">'
			. '<tr>'
			. '<td>&nbsp;</td>'
			. '<td align="right">Paparan&nbsp;'
			. SelectForm('rec_per_page', $rec_per_page, $strRecordCountList, $strRecordCountList, 'onchange="PageRefresh();"')
			. '&nbsp;setiap mukasurat.&nbsp;'
			. SelectForm('sort', $sort, $strSQLSortList, $strSQLSortList, 'onchange="PageRefresh();"')
			. '</td>'
			. '</tr>'
			. '</table>'
			. '</td>'
			. '</tr>';
	}

	if ($GetData->RowCount() <> 0) {
		$strFieldNameList = array($strCheckboxTemp, 'NomborRujukan/Pembiayaan', 'NomborAnggota/Nama', 'Nombor KP Baru', 'Jabatan/Cawangan', 'Jumlah (RM)', 'Tarikh Bayaran');
	} else {
		$strFieldNameList = array('&nbsp;', 'NomborRujukan/Pembiayaan', 'NomborAnggota/Nama', 'Nombor KP Baru', 'Jabatan/Cawangan', 'Jumlah (RM)', 'Tarikh Bayaran');
	}
	$strFieldWidthList = array('15', '', '', '10%', '10%', '10%', '10%');
	$strFieldAlignList = array('right', 'left', 'left', 'left', 'left', 'right', 'right');

	if ($GetData->RowCount() <> 0) {
		if ($page == 'list') {
			$rec_total = $GetData->RowCount();
			$page_total = ceil($rec_total / $rec_per_page);
			$page_start = $page_id - $page_range;
			if ($page_start < 1) {
				$page_start = 1;
			}
			$page_end = $page_id + $page_range;
			if ($page_end > $page_total) {
				$page_end = $page_total;
			}
			$rec_start = ($page_id - 1) * $rec_per_page;
			$rec_end = $page_id * $rec_per_page;

			$GetData->Move($rec_start);

			$nCount = $rec_start;

			$strTemp .=
				'<tr valign="top" >'
				. '<td valign="top" colspan="2">';

			$strTemp .= BeginDataField($strFieldNameList, $strFieldWidthList);

			while (!$GetData->EOF and $nCount < $rec_end) {
				$nCount += 1;

				$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, 'view', 1, $rec_per_page, $sort, $GetData->fields('loanID'), $keyword, $by, $dept);
				$strLoanPageTemp = '<a href="' . $strActionPage . '">' . $GetData->fields('loanNo') . '&nbsp;-&nbsp;' . strtoupper(dlookup("general", "name", "ID=" . tosql($GetData->fields('loanType'), "Number"))) . '</a>';
				$strNameTemp = dlookup("userdetails", "memberID", "userID=" . tosql($GetData->fields('userID'), "Text")) . '&nbsp;-&nbsp;' . strtoupper(dlookup("users", "name", "userID=" . tosql($GetData->fields('userID'), "Text")));

				$strFieldDataList = array(
					$nCount,
					$strLoanPageTemp,
					$strNameTemp,
					convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($GetData->fields('userID'), "Text"))),
					strtoupper(dlookup("general", "name", "ID=" . tosql(dlookup("userdetails", "departmentID", "userID=" . tosql($GetData->fields('userID'), "Text")), "Number"))),
					$GetData->fields('loanAmt'),
					toDate("d/m/Y", $GetData->fields('startPymtDate'))
				);

				$strTemp .= ContentDataField($nCount, $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);

				$GetData->MoveNext();
			}

			$strTemp .=	EndDataField()
				. '</td>'
				. '</tr>';

			$strPageTemp = '';

			if ($page_id > 1) {
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, $page, 1, $rec_per_page, $sort, $id, $keyword, $by, $dept);
				$strPageTemp .= '<a href="' . $strActionPage . '"><<</a>';
				$strPageTemp .= '&nbsp;';
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, $page, ($page_id - 1), $rec_per_page, $sort, $id, $keyword, $by, $dept);
				$strPageTemp .= '<a href="' . $strActionPage . '">Prev</a>';
				$strPageTemp .= '&nbsp;&nbsp;';
			}

			for ($i = $page_start; $i <= $page_end; $i++) {
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, $page, $i, $rec_per_page, $sort, $id, $keyword, $by, $dept);

				if ($i == $page_id) {
					$strPageTemp .= '<font class="redText">' . (($i - 1) * $rec_per_page + 1) . '-';
					if ($i <> $page_total) {
						$strPageTemp .= ($i * $rec_per_page);
					} else {
						$strPageTemp .= $rec_total;
					}
					$strPageTemp .= '</font>';
				} else {
					$strPageTemp .= '<a href="' . $strActionPage . '"><u>' . (($i - 1) * $rec_per_page + 1) . '-';
					if ($i <> $page_total) {
						$strPageTemp .= ($i * $rec_per_page);
					} else {
						$strPageTemp .= $rec_total;
					}
					$strPageTemp .= '</u></b></a>';
				}

				if ($i <> $page_end) {
					$strPageTemp .= '&nbsp;&nbsp;';
				}
			}

			if ($page_id < $page_total) {
				$strPageTemp .= '&nbsp;&nbsp;';
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, $page, ($page_id + 1), $rec_per_page, $sort, $id, $keyword, $by, $dept);
				$strPageTemp .= '<a href="' . $strActionPage . '">Next</a>';
				$strPageTemp .= '&nbsp;';
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $type, $page, $page_total, $rec_per_page, $sort, $id, $keyword, $by, $dept);
				$strPageTemp .= '<a href="' . $strActionPage . '">>></a>';
			}

			$strTemp .=
				'<tr>'
				. '<td align="left" colspan="' . count($strFieldDataList) . '">' . $strPageTemp . '</td>'
				. '</tr>';

			$strTemp .=
				'<tr>'
				. '<td class="textFont">Jumlah Data : <font class="redText">' . $GetData->RowCount() . '</font></td>'
				. '</tr>';
		}
	} else {
		$strTemp .=
			'<tr valign="top">'
			. '<td valign="top" colspan="2">';

		$strTemp .= BeginDataField($strFieldNameList, $strFieldWidthList);

		$strFieldDataList = array($nCount, '<center>- Tiada Rekod -</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>');
		$strTemp .= ContentDataField(-1, $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);

		$strTemp .=	EndDataField()
			. '</td>'
			. '</tr>';
	}
}


//--- insert database
if ($apply) {

	$userID = dlookup("userdetails", "memberID", "userID=" . tosql($GetData->fields('userID'), "Text"));

	//$startPymentMonth = $startPymentMonth;
	$yymm2 = sprintf("%04d%02d", $startPymentYear, $startPymentMonth);
	$monthPlus = $startPymentMonth + 1;
	$mthlast = sprintf("%02d", $monthPlus);
	$lastYr = ($startPymentYear + $nLoanYear) . $mthlast;

	if ($type == 'vehicle') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = ($fProfitRate * 0.01) * $fLoanAmount * $nLoanYear; //5250 = 3.5 * 0.01 * 30,000 * 5
		$fProcessFee = ($fProcessFeeRate * 0.01) * $fLoanAmount; //150=0.5*0.01*30,000
		$fPaymentAmount = $fLoanAmount + $fProfitAmount; //35,250=30,000+5250
		$fMonthPayment = RoundCurrency($fPaymentAmount / $nLoanMonth);	//587.5=35,250/60
		$fAllMonthPayment = RoundCurrency($fMonthPayment) * ($nLoanMonth - 1); // Except final month
		//34,662.5 =587.5*(60-1)
		$fFinalMonthPayment = $fPaymentAmount - (RoundCurrency($fMonthPayment) * ($nLoanMonth - 1));
		//587.5=35,250-34,662.5
	} else if ($type == 'item') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = 0.0;
		$fProcessFee = 0.0;
		$fPaymentAmount = 0.0;
		$fMonthPayment = 0.0;
		$fAllMonthPayment = 0.0;
		$fFinalMonthPayment = 0.0;
	} else if ($type == 'school') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = 0.0;
		$fProcessFee = 0.0;
		$fPaymentAmount = 0.0;
		$fMonthPayment = 0.0;
		$fAllMonthPayment = 0.0;
		$fFinalMonthPayment = 0.0;
	} else if ($type == 'personal') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = 0.0;
		$fProcessFee = 0.0;
		$fPaymentAmount = 0.0;
		$fMonthPayment = 0.0;
		$fAllMonthPayment = 0.0;
		$fFinalMonthPayment = 0.0;
	}

	$loanType2 = $GetData->fields('loanNo');
	$sSQL8 = "SELECT *
		FROM potbulan WHERE loanID = '" . $loanType2 . "'  AND status = 1 ";
	$GetData8 = $conn->Execute($sSQL8);
	if ($GetData8->RowCount() > 0) {
		print '<script>alert("Potongan GAJI TELAH WUJUD  !");</script>';
	} else {

		$sSQL4	= "INSERT INTO potbulan (" .
			"yrmth," .
			"userID," .
			"loanType," .
			"loanID," .
			"bondNo," .
			"userCreated," .
			"CreateDate," .
			"updateDate," .
			"status," .
			"yearStart," .
			"monthStart," .
			"lastPymt," .
			"lastyrmthPymt," .
			"jumBlnP)" .
			" VALUES (" .
			"'" . $yymm2 . "', " .
			"'" . $userID . "', " .
			"'" . $loanNo . "', " .
			"'" . $loanType . "', " .
			"'" . $NoBond . "', " .
			"'" . $updatedID . "', " .
			"'" . $updatedDate . "', " .
			"'" . $updatedDate . "', " .
			"'" . 1 . "', " .
			"'" . $startPymentYear . "', " .
			"'" . $startPymentMonth . "', " .
			"'" . $fFinalMonthPayment . "', " .
			"'" . $lastYr . "', " .
			"'" . $fMonthPayment . "')";

		$rsInstPtg = &$conn->Execute($sSQL4);



		for ($i = 0; $i < $nLoanYear; $i++) {
			$fProfitRateTemp = round((($nLoanYear - $i) / $nSum1), 2);
			$fBasicRateTemp = round((($i + 1) / $nSum1), 2);
			$fProfitTemp = $fProfitAmount * $fProfitRateTemp; //untung tahun
			$fProfitMonthTemp = round(($fProfitTemp / 12), 2); //untung bulan
			$fMonthPayment = RoundCurrency($fMonthPayment);
			$fBasicMonthTemp = $fMonthPayment - $fProfitMonthTemp; //pokok bulan 
			$fBasicTemp = $fBasicMonthTemp * 12; //pokok tahun
			//$yrmthBln =$startPymentYear.$startPymentMonth;
			$yymm4 = sprintf("%04d%02d", $startPymentYear, $startPymentMonth);

			$startPymentYear = $startPymentYear + 1;
			//$startPymentMonth = $startPymentMonth + 1;
			$fSum0 += $fProfitRateTemp;
			$fSum1 += $fProfitTemp;
			$fSum2 += $fBasicTemp;
			$fSum3 += $fProfitMonthTemp + $fBasicMonthTemp;

			$loanType2 = $GetData->fields('loanNo');
			$sSQL7 = "SELECT *
		FROM potbulan WHERE loanID = '" . $loanType2 . "'  AND status = 1 ";
			$GetData7 = $conn->Execute($sSQL7);
			$ID = $GetData7->fields('ID');



			$sSQL5	= "INSERT INTO potbulanlook (" .
				"potID," .
				"userID," .
				"loanType," .
				"loanID," .
				"yrmth," .
				"pokok," .
				"untung," .
				"pokokThn," .
				"createDate," .
				"userIDcreated," .
				"updateDate," .
				"prcent)" .
				" VALUES (" .
				"'" . $ID . "', " .
				"'" . $userID . "', " .
				"'" . $loanNo . "', " .
				"'" . $loanType . "', " .
				"'" . $yymm4 . "', " .
				"'" . $fBasicMonthTemp . "', " .
				"'" . $fProfitMonthTemp . "', " .
				"'" . $fBasicTemp . "', " .
				"'" . $updatedDate . "', " .
				"'" . $updatedID . "', " .
				"'" . $updatedDate . "', " .
				"'" . $fProfitRateTemp . "')";

			$rsInstPtg = &$conn->Execute($sSQL5);
		}

		print '<script>alert("Permohonan Potongan Gaji telah dikemaskini di dalam sistem !");</script>';
	}
	// end insert database
}
if ($apply_pat) {

	$userID = dlookup("userdetails", "memberID", "userID=" . tosql($GetData->fields('userID'), "Text"));

	//$startPymentMonth = $startPymentMonth;
	$yymm2 = sprintf("%04d%02d", $startPymentYear, $startPymentMonth);
	$monthPlus = $startPymentMonth + 1;
	$mthlast = sprintf("%02d", $monthPlus);
	$lastYr = ($startPymentYear + $nLoanYear) . $mthlast;

	if ($type == 'vehicle') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = ($fProfitRate * 0.01) * $fLoanAmount * $nLoanYear; //5250 = 3.5 * 0.01 * 30,000 * 5
		$fProcessFee = ($fProcessFeeRate * 0.01) * $fLoanAmount; //150=0.5*0.01*30,000
		$fPaymentAmount = $fLoanAmount + $fProfitAmount; //35,250=30,000+5250
		$fMonthPayment = RoundCurrency($fPaymentAmount / $nLoanMonth);	//587.5=35,250/60
		$fAllMonthPayment = RoundCurrency($fMonthPayment) * ($nLoanMonth - 1); // Except final month
		//34,662.5 =587.5*(60-1)
		$fFinalMonthPayment = $fPaymentAmount - (RoundCurrency($fMonthPayment) * ($nLoanMonth - 1));
		//587.5=35,250-34,662.5
	} else if ($type == 'item') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = 0.0;
		$fProcessFee = 0.0;
		$fPaymentAmount = 0.0;
		$fMonthPayment = 0.0;
		$fAllMonthPayment = 0.0;
		$fFinalMonthPayment = 0.0;
	} else if ($type == 'school') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = 0.0;
		$fProcessFee = 0.0;
		$fPaymentAmount = 0.0;
		$fMonthPayment = 0.0;
		$fAllMonthPayment = 0.0;
		$fFinalMonthPayment = 0.0;
	} else if ($type == 'personal') {
		$fProcessFeeRate = 0.5;
		$fProfitAmount = 0.0;
		$fProcessFee = 0.0;
		$fPaymentAmount = 0.0;
		$fMonthPayment = 0.0;
		$fAllMonthPayment = 0.0;
		$fFinalMonthPayment = 0.0;
	}

	$loanType2 = $GetData->fields('loanNo');
	$sSQL9 = "SELECT *
		FROM potbulan WHERE loanID = '" . $loanType2 . "'  AND status = 2 ";
	$GetData9 = $conn->Execute($sSQL9);
	if ($GetData9->RowCount() > 0) {
		print '<script>alert("Potongan GAJI TELAH WUJUD !");</script>';
	} else {


		$sSQL4	= "INSERT INTO potbulan (" .
			"yrmth," .
			"userID," .
			"loanType," .
			"loanID," .
			"bondNo," .
			"userCreated," .
			"CreateDate," .
			"updateDate," .
			"status," .
			"yearStart," .
			"monthStart," .
			"lastPymt," .
			"lastyrmthPymt," .
			"jumBlnPAT)" .
			" VALUES (" .
			"'" . $yymm2 . "', " .
			"'" . $userID . "', " .
			"'" . $loanNo . "', " .
			"'" . $loanType . "', " .
			"'" . $NoBond . "', " .
			"'" . $updatedID . "', " .
			"'" . $updatedDate . "', " .
			"'" . $updatedDate . "', " .
			"'" . 2 . "', " .
			"'" . $startPymentYear . "', " .
			"'" . $startPymentMonth . "', " .
			"'" . $fFinalMonthPayment . "', " .
			"'" . $lastYr . "', " .
			"'" . $fMonthPayment . "')";

		$rsInstPtg = &$conn->Execute($sSQL4);



		for ($i = 0; $i < $nLoanYear; $i++) {
			$fProfitRateTemp = round((($nLoanYear - $i) / $nSum1), 2);
			$fBasicRateTemp = round((($i + 1) / $nSum1), 2);
			$fProfitTemp = $fProfitAmount * $fProfitRateTemp; //untung tahun
			$fProfitMonthTemp = round(($fProfitTemp / 12), 2); //untung bulan
			$fMonthPayment = RoundCurrency($fMonthPayment);
			$fBasicMonthTemp = $fMonthPayment - $fProfitMonthTemp; //pokok bulan 
			$fBasicTemp = $fBasicMonthTemp * 12; //pokok tahun
			//$yrmthBln =$startPymentYear.$startPymentMonth;
			$yymm4 = sprintf("%04d%02d", $startPymentYear, $startPymentMonth);

			$startPymentYear = $startPymentYear + 1;
			//$startPymentMonth = $startPymentMonth + 1;
			$fSum0 += $fProfitRateTemp;
			$fSum1 += $fProfitTemp;
			$fSum2 += $fBasicTemp;
			$fSum3 += $fProfitMonthTemp + $fBasicMonthTemp;

			$loanType2 = $GetData->fields('loanNo');
			$sSQL7 = "SELECT *
		FROM potbulan WHERE loanID = '" . $loanType2 . "' AND status = 2 ";
			$GetData7 = $conn->Execute($sSQL7);
			$ID = $GetData7->fields('ID');


			$sSQL5	= "INSERT INTO potbulanlook (" .
				"potID," .
				"userID," .
				"loanType," .
				"loanID," .
				"yrmth," .
				"pokok," .
				"untung," .
				"pokokThn," .
				"createDate," .
				"userIDcreated," .
				"updateDate," .
				"prcent)" .
				" VALUES (" .
				"'" . $ID . "', " .
				"'" . $userID . "', " .
				"'" . $loanNo . "', " .
				"'" . $loanType . "', " .
				"'" . $yymm4 . "', " .
				"'" . $fBasicMonthTemp . "', " .
				"'" . $fProfitMonthTemp . "', " .
				"'" . $fBasicTemp . "', " .
				"'" . $updatedDate . "', " .
				"'" . $updatedID . "', " .
				"'" . $updatedDate . "', " .
				"'" . $fProfitRateTemp . "')";

			$rsInstPtg = &$conn->Execute($sSQL5);
		}

		print '<script>alert("Permohonan Potongan Gaji telah dikemaskini di dalam sistem !");</script>';
	}
}
$strTemp .=
	'</table>'
	. '</form>'
	. '</div>';

$strTemp .=
	'<script language="JavaScript">'
	. 'var checkedAll=false; '
	. 'function SelectCurrent() {
	frm = document.MyForm.elements;
	for (i = 0; i < frm.length; i++) {
		if (frm[i].name=="checkbox_id[]" && frm[i].checked) {
			if (frm[i].checked) {
				frm[i].value = 1;
			} else if (!frm[i].checked) {
				frm[i].value = 0;
			}
		}
	}
} ';

if ($page <> 'list') {
	$strTemp .=
		'function SelectAll() {
		frm = document.MyForm.elements;
		checkedAll = !checkedAll;
		for (i = 0; i < frm.length; i++) {
			if (frm[i].name=="checkbox_id[]")	{
				frm[i].checked = checkedAll;
				if (frm[i].checked) {
					frm[i].value = 1;
				} else if (!frm[i].checked) {
					frm[i].value = 0;
				}
			}
		}
	} ';
} else {
	$strTemp .=
		'function SelectAll() {
		frm = document.MyForm.elements;
		checkedAll = !checkedAll;
		for (i = 0; i < frm.length; i++) {
			if (frm[i].name=="checkbox_id[]")	{
				frm[i].checked = checkedAll;
				if (frm[i].checked) {
					frm[i].value = 1;
				} else if (!frm[i].checked) {
					frm[i].value = 0;
				}
			}
		}
	} ';
}

$strActionPage =
	'"' . $_SERVER['PHP_SELF']
	. '?type=' . $type
	. '&page=' . $page
	. '&page_id=1';
if ($rec_total <> 0) {
	$strActionPage .=
		'&rec_per_page=" + frm.rec_per_page.options[frm.rec_per_page.selectedIndex].value + "'
		. '&sort=" + frm.sort.options[frm.sort.selectedIndex].value + "';
} else {
	$strActionPage .=
		'&rec_per_page=' . $rec_per_page . ''
		. '&sort=' . $sort . '';
}
if ($page <> 'list') {
	$strActionPage .= '&id=' . $id;
} else {
	$strActionPage .=
		'&dept=" + frm.dept.options[frm.dept.selectedIndex].value + "';
}
$strActionPage .= '"';

$strTemp .=
	'function PageRefresh() {
	frm = document.MyForm;
	document.location = ' . $strActionPage . ';
} '
	. 'function ConfirmUpdate() {
	frm = document.MyForm.elements;
	for (i = 0; i < frm.length; i++) {
		if (frm[i].name=="checkbox_id[]") {
			frm[i].checked = true;
		}
	}
} '
	. 'function ConfirmDelete() {
	frm = document.MyForm.elements;
	nCount = 0;

	for (i = 0; i < frm.length; i++) {
		if (frm[i].name=="checkbox_id[]" && frm[i].checked)	{
			nCount++;
		}
	}
	if (nCount == 0) {
	alert(nCount);
		return false;
	} else {
		if (confirm(\'Anda Pasti \' + nCount + \' Rekod Ingin Dihapuskan?\')) {
			for (i = 0; i < frm.length; i++) {
				if (frm[i].name=="checkbox_id[]") {
					frm[i].checked = true;
				}
			}
			return true;
		} else {
			return false;
		}
	}
} '
	. 'function ConfirmSearch() {
	frm = document.MyForm;
} '
	. 'function ChangeAmount() {
	frm = document.MyForm.elements;
	var fAmount = 0.0;
	for (i = 0; i < frm.length; i++) {
		if (frm[i].name=="amount[]") {
			fAmount += parseFloat(frm[i].value);
		}
	}

	var strTemp = fAmount.toString();
	var strAmount = strTemp.split(".");
	if (strAmount.length == 1) {
		frm.overall_amount.value = strAmount[0] + \'.00\';
	} else {
		var strFloat = strAmount[1];

		if (strFloat.length < 2) {
			strFloat = strFloat + \'0\';
		} else {
			strFloat = strFloat.substr(0, 2);
		}
		frm.overall_amount.value = strAmount[0] + \'.\' + strFloat;
	}
}
'
	. '</script>';

print $strTemp;

include("footer.php");

function GenerateSQLList()
{
	global $keyword;
	global $by;
	global $dept;
	global $sort;
	global $strLoanTypeList;

	$sSQL_ = 'SELECT * FROM loans a, users b, userdetails c';
	$sSQL_ .= ' WHERE a.status = 3 and a.userID = b.userID';
	$sSQL_ .= ' AND c.userID = b.userID';
	$sSQL_ .= ' AND (';
	for ($i = 0; $i < count($strLoanTypeList); $i++) {
		if ($i <> 0) {
			$sSQL_ .= ' OR';
		}
		$sSQL_ .= ' a.loanType = ' . $strLoanTypeList[$i];
	}
	$sSQL_ .= ')';
	if ($keyword <> '') {
		if ($by == '1') {
			$sSQL_ .= ' AND b.memberID = \'' . $keyword . '\'';
		} else if ($by == '2') {
			$sSQL_ .= ' AND b.name like \'%' . $keyword . '%\'';
		} else if ($by == '3') {
			$sSQL_ .= ' AND c.newIC = \'' . $keyword . '\'';
		}
	}
	if ($dept <> '') {
		$sSQL_ .= ' AND c.departmentID = ' . $dept;
	}

	$sSQL_ .= ' ORDER BY a.applyDate ' . $sort;

	return $sSQL_;
}

function BeginDataField($strFieldNameList_, $strFieldWidthList_)
{
	$strTemp_ = '<table border="0" cellspacing="1" cellpadding="4" width="100%" class="lineBG">'
		. '<tr class="header">';

	for ($i = 0; $i < count($strFieldNameList_); $i++) {
		$strTemp_ .= '<td nowrap="nowrap" align="center" width="' . $strFieldWidthList_[$i] . '">' . $strFieldNameList_[$i] . '</td>';
	}

	$strTemp_ .= '</tr>';

	return $strTemp_;
}

function EndDataField()
{
	$strTemp_ = '</table>';

	return $strTemp_;
}

function ContentDataTable($strDataList_)
{
	$strTemp_ = '';

	for ($i = 0; $i < count($strDataList_); $i++) {
		$strTemp_ .= '<tr>';
		for ($j = 0; $j < count($strDataList_[$i]); $j++) {
			$strTemp_ .= '<td class="Data" align="right" valign="top" nowrap="nowrap">';
			$strTemp_ .= $strDataList_[$i][$j];
			$strTemp_ .= '</td>';
		}
		$strTemp_ .= '</tr>';
	}

	return $strTemp_;
}

function ContentDataField($strLoanId_, $strFieldDataList_, $strFieldAlignList_, $strInputNameList_, $strInputSizeList_, $strInputMaxLengthList_, $bIsEditable_)
{
	global $strLoanCodeList;
	global $strLoanNameList;

	$strTemp_ = '<tr>';

	for ($i = 0; $i < count($strFieldDataList_); $i++) {
		$strTemp_ .= '<td class="Data" align="' . $strFieldAlignList_[$i] . '" valign="top" nowrap="nowrap">';

		if ($i == 0) {
			if ($strLoanId_ <> -1) {
				$strTemp_ .= '<input name="checkbox_id[]" type="checkbox" class="form-check-input" value="0" style="padding:0px;margin:0px" onclick="SelectCurrent()" />';
			} else {
				$strTemp_ .= '<input name="insert_data[]" type="hidden" value="1" />';
			}
			$strTemp_ .=
				'<input name="loan_id[]" type="hidden" value="' . $strLoanId_ . '" />'
				. '<input name="' . $strInputNameList_[$i] . '" type="hidden" value="' . $strFieldDataList_[$i] . '" />';
		}
		if ($bIsEditable_) {
			if ($i == 1 and $i <> 0) {
				$strTemp_ .= '<select name="' . $strInputNameList_[$i] . '">';
				for ($j = 0; $j < count($strLoanCodeList); $j++) {
					$strTemp_ .= '<option value="' . $strLoanCodeList[$j] . '"';
					if ($strFieldDataList_[$i] == $strLoanCodeList[$j]) {
						$strTemp_ .= ' selected="selected"';
					}
					$strTemp_ .= '>' . $strLoanNameList[$j] . '</option>';
				}
				$strTemp_ .= '</select>';
			} else if ($i <> 0) {
				$strTemp_ .=
					'<input name="' . $strInputNameList_[$i] . '" type="text"'
					. ' value="' . $strFieldDataList_[$i] . '"'
					. ' style="text-align:' . $strFieldAlignList_[$i] . ';"'
					. ' size="' . $strInputSizeList_[$i] . '"'
					. ' maxlength="' . $strInputMaxLengthList_[$i] . '"';
				if ($i == 4) {
					$strTemp_ .= ' onchange="ChangeAmount()"';
				}
				$strTemp_ .= ' />';
			}
		} else {
			if ($i <> 0) {
				$strTemp_ .= $strFieldDataList_[$i];
			}
		}

		$strTemp_ .= '</td>';
	}

	$strTemp_ .= '</tr>';

	return $strTemp_;
}

function PageSelection($page_, $strNameList_, $strPageList_)
{
	global $id;

	$strTemp_ .=
		'<tr>'
		. '<td align="left">&nbsp;</td>'
		. '<td align="right">';

	for ($i = 0; $i < count($strNameList_); $i++) {
		if ($i <> 0) {
			$strTemp_ .= '&nbsp;|&nbsp;';
		}

		if ($page_ == $strPageList_[$i]) {
			$strTemp_ .= '<font class="redText">' . $strNameList_[$i] . '</font>';
		} else {
			$strTemp_ .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=' . $strPageList_[$i] . '&id=' . $id . '">' . $strNameList_[$i] . '</a>';
		}
	}

	$strTemp_ .=
		'&nbsp;</td>'
		. '</tr>';

	return $strTemp_;
}

function SelectForm($strName_, $strValue_, $strNameList_, $strValueList_, $strEtc_)
{
	$strTemp_ = '<select name="' . $strName_ . '"';
	if ($strEtc_ <> '') {
		$strTemp_ .= ' ' . $strEtc_;
	}
	$strTemp_ .= '>';

	for ($i = 0; $i < count($strNameList_); $i++) {
		$strTemp_ .= '<option value="' . $strValueList_[$i] . '"';
		if ($strValue_ == $strValueList_[$i]) {
			$strTemp_ .= ' selected="selected"';
		}
		$strTemp_ .= '>' . $strNameList_[$i] . '</option>';
	}
	$strTemp_ .= '</select>';

	return $strTemp_;
}

function PageLink($strPath_, $type_, $page_, $page_id_, $rec_per_page_, $sort_, $id_, $keyword_, $by_, $dept_)
{
	$strTemp_ =
		$strPath_
		. '?type=' . $type_
		. '&page=' . $page_;

	if ($page_ <> 'list') {
		$strTemp_ .= '&id=' . $id_;
	} else {
		$strTemp_ .=
			'&page_id=' . $page_id_
			. '&rec_per_page=' . $rec_per_page_
			. '&sort=' . $sort_
			. '&by=' . $by_
			. '&dept=' . $dept_;
	}

	return $strTemp_;
}

function RoundCurrency($fValue_)
{
	if ((ceil($fValue_) - $fValue_) >= 0.5) {
		$fTemp_ = floor($fValue_) + 0.5;
	} else {
		$fTemp_ = ceil($fValue_);
	}


	return $fTemp_;
}

function Currency($fValue_)
{
	if ($fValue_ == 0) {
		$strTemp_ = '&nbsp;';
	} else {
		$strTemp_ = number_format($fValue_, 2, '.', ',');
	}

	return $strTemp_;
}
?>