<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	transDeduct.php
 *          Date 		: 	12/06/2006
 * comment : deduction member list and deduction details
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

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
if (!isset($sort))			$sort = 'ASC';
if (!isset($id))			$id = '';
if (!isset($deduct_code))	$deduct_code = '';
if (!isset($by))			$by = '1';
if (!isset($keyword))		$keyword = '';
if (!isset($dept))			$dept = '';
if (!isset($status))		$status = '1';

//title
$strTitle = 'Jadual Potongan';

if ($page <> 'list') {
	$strTitle = 'Senarai ' . $strTitle;
	$strHeaderTitle = strtoupper($strTitle);
} else {
	$strHeaderTitle = '<a class="maroon" href="' . $_SERVER['PHP_SELF'] . '">' . strtoupper('Senarai ' . $strTitle) . '</a>&nbsp;>&nbsp;' . strtoupper($strTitle . ' Anggota');
	$strTitle = $strTitle . ' Anggota';
}

$strHeaderTitle = '<a class="maroon" href="index.php">LAMAN UTAMA</a><b>' . '&nbsp;>&nbsp;' . $strHeaderTitle . '</b>';

if (isset($_POST['submit'])) {
	if ($_POST['submit'] == 'Kemaskini') {
		for ($i = 0; $i < count($deduct_id); $i++) {
			if ($checkbox_id[$i]) {
				$sSQL =
					'UPDATE deduction SET'
					. ' deductCode=\'' . $deduct_code[$i] . '\','
					. ' startDate=\'' . saveDate($start_date[$i]) . '\','
					. ' endDate=\'' . saveDate($end_date[$i]) . '\','
					. ' amount=' . $amount[$i] . ','
					. ' caj=' . $charge[$i] . ','
					. ' updateDate=\'' . date('Y-m-d H:i:s') . '\','
					. ' updateBy=\'' . get_session("Cookie_userName") . '\','
					. ' seqID=' . $seq_id[$i]
					. ' WHERE userID=\'' . $id . '\''
					. ' AND ID=\'' . $deduct_id[$i] . '\'';
				$SetData = $conn->Execute($sSQL);

				$strActivity = $_POST['submit'] . ' Rekod Ke' . ' ' . $strTitle;
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
			}
		}
	} else if ($_POST['submit'] == 'Simpan') {
		for ($i = 0; $i < count($deduct_id); $i++) {
			if ($insert_data[$i]) {
				$sSQL =
					'INSERT INTO deduction '
					. '(userID,'
					. ' deductCode,'
					. ' startDate,'
					. ' endDate,'
					. ' amount,'
					. ' caj,'
					. ' createdDate,'
					. ' createdBy,'
					. ' updateDate,'
					. ' updateBy,'
					. ' seqID'
					. ') VALUES ('
					. ' \'' . $id . '\','
					. ' \'' . $deduct_code[$i] . '\','
					. ' \'' . saveDate($start_date[$i]) . '\','
					. ' \'' . saveDate($end_date[$i]) . '\','
					. ' ' . $amount[$i] . ','
					. ' ' . $charge[$i] . ','
					. ' \'' . date('Y-m-d H:i:s') . '\','
					. ' \'' . get_session("Cookie_userName") . '\','
					. ' \'' . date('Y-m-d H:i:s') . '\','
					. ' \'' . get_session("Cookie_userName") . '\','
					. ' ' . $seq_id[$i] . ')';
				$SetData = $conn->Execute($sSQL);

				$strActivity = $_POST['submit'] . ' Rekod Ke' . ' ' . $strTitle;
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
			}
		}
	} else if ($_POST['submit'] == 'Hapus') {
		for ($i = 0; $i < count($deduct_id); $i++) {
			if ($checkbox_id[$i]) {
				$sSQL =
					'DELETE FROM deduction '
					. ' WHERE userID=\'' . $id . '\''
					. ' AND ID=\'' . $deduct_id[$i] . '\'';
				$SetData = $conn->Execute($sSQL);

				$strActivity = $_POST['submit'] . ' Rekod Dari' . ' ' . $strTitle;
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
			}
		}
	}
}

//set combo box value in member list 
//carian by value
$strTypeNameList	= array('Nombor Anggota', 'Nama Anggota', 'No KP Baru');
$strTypeValueList	= array(1, 2, 3);
//member status lulus berhenti bersara
$strStatusNameList	= array($statusList[1], $statusList[3],  $statusList[4]);
$strStatusValueList	= array($statusVal[1], $statusVal[3], $statusVal[4]);
//number selection to be list
$strRecordCountList	= array(5, 10, 20, 30, 40, 50, 100);
$strSQLSortList		= array('DESC', 'ASC');
//dept name and id
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
//end value

//set action page to current
$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_id, $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);

//default header
$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="' . $strActionPage . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">'
	. '<tr>'
	. '<td colspan="2">'
	. '<table cellpadding="0" cellspacing="6">';

if ($page <> 'list') {
	//member detail content
	//write table row and table data
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM general WHERE category=\'J\'';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list

	//get member detail by id
	$sSQL =
		'SELECT a.name, b.memberID, b.newIC, b.departmentID, b.approvedDate FROM users a, userdetails b'
		. ' WHERE a.userID=\'' . $id . '\''
		. ' AND a.userID = b.userID';
	$GetData = $conn->Execute($sSQL);

	//get current date
	$strStartDate = date('d/m/Y');
	$strCurrentDate = date('d/m/Y');

	//set member detail header
	$strFieldNameList = array('Nama', 'Nombor Anggota', 'Nombor KP', 'Jabatan/Cawangan', 'Tarikh Keanggotaan');

	if ($GetData->RowCount() <> 0) {

		$strStartDate = toDate('d/m/y', $GetData->fields('approvedDate'));
		$strFieldDataList = array(
			strtoupper($GetData->fields('name')),
			$GetData->fields('memberID'),
			convertNewIC($GetData->fields('newIC')),
			strtoupper(dlookup('general', 'name', 'ID=' . tosql($GetData->fields('departmentID'), 'Number'))),
			$strStartDate
		);
	} else {
		$strFieldDataList = array('- Tiada -', '- Tiada -', '- Tiada -', '- Tiada -', '- Tiada -');
	}

	//write member detail header
	for ($i = 0; $i < count($strFieldNameList); $i++) {
		$strTemp .= '<tr><td align="right" valign="top"><b>' . $strFieldNameList[$i] . ' :</b></td><td>' . $strFieldDataList[$i] . '</td></tr>';
	}
	//end handle member detail 
} else {
	// write table row and data
	// member list
	/*	if ($keyword <> '') {
		if ($by <> '2' AND !(is_int($keyword))) {
			$strErrMsg = '<tr><td align="right"><font class="redText">Ralat</font></td><td><font class="redText">: Sila masukan nombor sahaja!</font></td></tr>';
			$keyword = '';
		} else {
			$strErrMsg = '';
		}
	}*/
	// handle member list
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
		. '</tr>'
		. '<tr>'
		. '<td align="right"><b>Status</b></td>'
		. '<td>' . SelectForm('status', $status, $strStatusNameList, $strStatusValueList, 'onchange="PageRefresh();"') . '</td>'
		. '</tr>';
} //end hande member list

//close header 
$strTemp .=	'</table>'
	. '</td>'
	. '</tr>';

//set select all check box 
$strCheckboxTemp = '<input name="all" type="checkbox" class="form-check-input" onclick="SelectAll()" style="padding:0px;margin:0px" />';

if ($page <> 'list') {
	// prepare member deduction detail
	//get member deduction by id
	$sSQL = 'SELECT * FROM deduction WHERE userID=\'' . $id . '\' ORDER BY seqID ASC';
	$GetData = $conn->Execute($sSQL);

	if ($GetData->RowCount() <> 0) {
		//set deduction action if there is deduction
		$strNameList = array('Tambah', 'Ubah', 'Papar');
		$strPageList = array('add', 'edit', 'view');
	} else {
		//set deduction action if there is no deduction
		$strNameList = array('Tambah', 'Papar');
		$strPageList = array('add', 'view');
		//change page type if add new deduction
		if ($page == 'edit') $page = 'view';
	}

	//write member deduction action link
	$strTemp .=	'<tr valign="top" class="textFont">'
		. '<td>'
		. PageSelection($_SERVER['PHP_SELF'], $strNameList, $strPageList)
		. '<hr size="1px">'
		. '</td>'
		. '</tr>';

	//deduction header 
	//deduction table variable
	if ($GetData->RowCount() <> 0) {
		$strFieldNameList	= array($strCheckboxTemp, 'Jenis Potongan', 'Tarikh Mula', 'Tarikh Akhir', 'Jumlah', 'Caj');
	} else {
		$strFieldNameList	= array('&nbsp;', 'Jenis Potongan', 'Tarikh Mula', 'Tarikh Akhir', 'Jumlah', 'Caj');
	}
	$strFieldWidthList	= array('15', '', '10%', '10%', '10%', '10%');
	$strFieldAlignList	= array('center', 'left', 'right', 'right', 'right', 'right');
	$strInputNameList	= array('seq_id[]', 'deduct_code[]', 'start_date[]', 'end_date[]', 'amount[]', 'charge[]');
	$strInputSizeList	= array('', '50', '15', '15', '15', '15');
	$strInputMaxLengthList = array('', '50', '10', '10', '10', '10');
} else {
	//prepare member list table

	//get all member list
	$sSQL = 'SELECT a.userID, a.name, b.memberID, b.newIC, b.departmentID, b.approvedDate, b.status FROM users a, userdetails b';
	$sSQL .= ' WHERE a.userID = b.userID';
	if ($keyword <> '') {
		if ($by == '1') {
			$sSQL .= ' AND b.memberID = \'' . $keyword . '\'';
		} else if ($by == '2') {
			$sSQL .= ' AND a.name like \'%' . $keyword . '%\'';
		} else if ($by == '3') {
			$sSQL .= ' AND b.newIC = \'' . $keyword . '\'';
		}
	}
	if ($dept <> '') {
		$sSQL .= ' AND b.departmentID = ' . $dept;
	}
	if ($status <> '') {
		$sSQL .= ' AND b.status = ' . $status;
	}

	$sSQL .= ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ) ' . $sort;
	$GetData = $conn->Execute($sSQL);

	//set list header 
	if ($GetData->RowCount() <> 0) {
		$strFieldNameList = array($strCheckboxTemp, 'NomborAnggota/Nama', 'Nombor KP Baru', 'Jabatan/Cawangan', 'Tarikh Anggota', 'Bil. Rekod', 'Jumlah (RM)');
	} else {
		$strFieldNameList = array('&nbsp;', 'NomborAnggota/Nama', 'Nombor KP Baru', 'Jabatan/Cawangan', 'Tarikh Anggota', 'Bil. Rekod', 'Jumlah (RM)');
	}
	$strFieldWidthList = array('15', '', '10%', '20%', '10%', '10%', '8%');
	$strFieldAlignList = array('right', 'left', 'left', 'left', 'center', 'right', 'right');

	//write page selection 
	if ($GetData->RowCount() <> 0) {
		$strTemp .= '<tr><td colspan="2"><hr size="1px"></td></tr>';
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
}

//prepare paging detail from member list or member deduction detail
if ($GetData->RowCount() <> 0) {
	//common calculation
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
	//end calculation

	$strTemp .=
		'<tr valign="top" >'
		. '<td valign="top" colspan="2">';

	//open table
	$strTemp .= BeginDataField($strFieldNameList, $strFieldWidthList);

	if ($page <> 'list') {
		//read all data for deduction 
		while (!$GetData->EOF and $nCount <= $rec_end) {
			$nCount += 1;
			//get current id
			$strDeductId = $GetData->fields('ID');

			//differentiate current data for edit data and view data
			//prepare data
			if ($page == 'view' or $page == 'add') {
				$strFieldDataList = array(
					$nCount,
					dlookup('general', 'name', 'code=' . tosql($GetData->fields('deductCode'), 'Text')),
					toDate('', $GetData->fields('startDate')),
					toDate('', $GetData->fields('endDate')),
					sprintf('%.2f', $GetData->fields('amount')),
					$GetData->fields('caj') . '%'
				);
			} else if ($page == 'edit') {
				$strFieldDataList = array(
					$nCount,
					$GetData->fields('deductCode'),
					toDate('', $GetData->fields('startDate')),
					toDate('', $GetData->fields('endDate')),
					sprintf('%.2f', $GetData->fields('amount')),
					$GetData->fields('caj')
				);
			}

			$overallAmount += $GetData->fields('amount');

			//write table data
			if ($page == 'view' or $page == 'add') {
				$strTemp .= ContentDataField($strDeductId, $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);
			} else if ($page == 'edit') {
				$strTemp .= ContentDataField($strDeductId, $strFieldDataList, $strFieldAlignList, $strInputNameList, $strInputSizeList, $strInputMaxLengthList, true);
			}

			$GetData->MoveNext();
		} //end read deduction

		//write data for new deduction
		if ($page == 'add') {
			$nCount += 1;
			$strFieldDataList = array($nCount, $strDeductCodeList[0], $strStartDate, $strCurrentDate, sprintf('%.2f', 0), 0);

			$strTemp .= ContentDataField('', $strFieldDataList, $strFieldAlignList, $strInputNameList, $strInputSizeList, $strInputMaxLengthList, true);
		}

		//write last table data 
		$strTemp .=
			'<tr>'
			. '<td class="Data" align="right" colspan="4">Jumlah Keseluruhan</td>';

		if ($page == 'view' or $page == 'add') {
			$strTemp .=
				'<td class="Data" align="right">'
				. sprintf('%.2f', $overallAmount)
				. '</td>';
		} else if ($page == 'edit') {
			$strTemp .=
				'<td class="Data" align="center">'
				. '<input name="overall_amount" type="text" disabled="disabled" value="' . sprintf('%.2f', $overallAmount) . '" style="text-align:right;" size="15" maxlength="10" />'
				. '</td>';
		}

		$strTemp .=
			'<td class="Data" colspan="2">&nbsp;</td>'
			. '</tr>'; //end last table data
	} else {
		//read all member list data
		while (!$GetData->EOF and $nCount < $rec_end) {
			$nCount += 1;
			//set action page 
			$strActionPage = PageLink($_SERVER['PHP_SELF'], 'view', 1, $rec_per_page, $sort, $GetData->fields('userID'), $keyword, $by, $dept, $status);
			//page link
			$strPageTemp = '<a href="' . $strActionPage . '">' . $GetData->fields('memberID') . '&nbsp;-&nbsp;' . strtoupper($GetData->fields('name')) . '</a>';

			//get sum deduction value for current member
			$sSQL = 'SELECT * FROM deduction WHERE userID=\'' . $GetData->fields('userID') . '\' ORDER BY seqID ASC';
			$GetMiniData = $conn->Execute($sSQL);

			if ($GetMiniData->RowCount() <> 0) {
				$nDecuctCount = $GetMiniData->RowCount();
				$nDeductAmount = 0;
				$GetMiniData->Move(0);

				while (!$GetMiniData->EOF) {
					$nDeductCount += 1;
					$nDeductAmount += $GetMiniData->fields('amount');
					$GetMiniData->MoveNext();
				}
			} else {
				$nDecuctCount = 0;
				$nDeductAmount = 0;
			}

			$strFieldDataList = array(
				$nCount,
				$strPageTemp,
				convertNewIC($GetData->fields('newIC')),
				strtoupper(dlookup('general', 'name', 'ID=' . tosql($GetData->fields('departmentID'), 'Number'))),
				toDate('', $GetData->fields('approvedDate')),
				$nDecuctCount,
				$nDeductAmount
			);

			$strTemp .= ContentDataField($nCount, $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);

			$GetData->MoveNext();
		}
	}

	$strTemp .=	EndDataField()
		. '</td>'
		. '</tr>';

	$strPageTemp = '';

	if ($page_id > 1) {
		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, 1, $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);
		$strPageTemp .= '<a href="' . $strActionPage . '"><<</a>';
		$strPageTemp .= '&nbsp;';
		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, ($page_id - 1), $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);
		$strPageTemp .= '<a href="' . $strActionPage . '">Prev</a>';
		$strPageTemp .= '&nbsp;&nbsp;';
	}

	for ($i = $page_start; $i <= $page_end; $i++) {
		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $i, $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);

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
		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, ($page_id + 1), $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);
		$strPageTemp .= '<a href="' . $strActionPage . '">Next</a>';
		$strPageTemp .= '&nbsp;';
		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_total, $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);
		$strPageTemp .= '<a href="' . $strActionPage . '">>></a>';
	}

	$strTemp .=
		'<tr>'
		. '<td align="left" colspan="' . count($strFieldDataList) . '">' . $strPageTemp . '</td>'
		. '</tr>';

	$strTemp .=
		'<tr>'
		. '<td class="textFont">Jumlah Rekod : <font class="redText">' . $GetData->RowCount() . '</font></td>'
		. '</tr>';
} else {
	$strTemp .=
		'<tr valign="top">'
		. '<td valign="top" colspan="2">';

	$strTemp .= BeginDataField($strFieldNameList, $strFieldWidthList);

	$nCount = 1;

	if ($page <> 'list') {
		if ($page == 'view') {
			$strFieldDataList = array($nCount, '<center>- Tiada Rekod -</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>');
			$strTemp .= ContentDataField('', $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);
		} else if ($page == 'add') {
			$strFieldDataList = array($nCount, $strDeductCodeList[0], $strStartDate, $strCurrentDate, sprintf('%.2f', 0), 0);
			$strTemp .= ContentDataField('', $strFieldDataList, $strFieldAlignList, $strInputNameList, $strInputSizeList, $strInputMaxLengthList, true);
		}
	} else {
		$strFieldDataList = array($nCount, '<center>- Tiada Rekod -</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>');
		$strTemp .= ContentDataField('', $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);
	}

	$strTemp .=	EndDataField()
		. '</td>'
		. '</tr>';
}

if ($page <> 'list') {
	// Begin Submit Button For Operation
	$strTemp .=
		'<tr>'
		. '<td align="left" colspan="2">';

	if ($page == 'add') {
		$strTemp .= '<input name="submit" type="submit" value="Simpan" />&nbsp;';
	} else if ($page == 'edit' and $nCount > 1) {
		$strTemp .= '<input name="submit" type="submit" value="Kemaskini" onclick="ConfirmUpdate()" />&nbsp;';
	}

	if ($nCount > 1) {
		$strTemp .= '<input name="submit" type="submit" value="Hapus" onclick="return ConfirmDelete()" />';
	}

	$strTemp .=
		'</td>'
		. '</tr>';
	// End Submit Button
}

$strTemp .=
	'</table>'
	. '</form>';

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
	. '?page=' . $page
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
		'&dept=" + frm.dept.options[frm.dept.selectedIndex].value + "'
		. '&status=" + frm.status.options[frm.status.selectedIndex].value + "';
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

function ContentDataField($strDeductId_, $strFieldDataList_, $strFieldAlignList_, $strInputNameList_, $strInputSizeList_, $strInputMaxLengthList_, $bIsEditable_)
{
	global $strDeductCodeList;
	global $strDeductNameList;

	$strTemp_ = '<tr>';

	for ($i = 0; $i < count($strFieldDataList_); $i++) {
		$strTemp_ .= '<td class="Data" align="' . $strFieldAlignList_[$i] . '" valign="top" nowrap="nowrap">';

		if ($i == 0) {
			if ($strDeductId_ <> '') {
				$strTemp_ .= '<input name="checkbox_id[]" type="checkbox" value="0" style="padding:0px;margin:0px" onclick="SelectCurrent()" />';
			} else {
				$strTemp_ .= '<input name="insert_data[]" type="hidden" value="1" />';
			}
			$strTemp_ .=
				'<input name="deduct_id[]" type="hidden" value="' . $strDeductId_ . '" />'
				. '<input name="' . $strInputNameList_[$i] . '" type="hidden" value="' . $strFieldDataList_[$i] . '" />';
		}
		if ($bIsEditable_) {
			if ($i == 1 and $i <> 0) {
				$strTemp_ .= '<select name="' . $strInputNameList_[$i] . '">';
				for ($j = 0; $j < count($strDeductCodeList); $j++) {
					$strTemp_ .= '<option value="' . $strDeductCodeList[$j] . '"';
					if ($strFieldDataList_[$i] == $strDeductCodeList[$j]) {
						$strTemp_ .= ' selected="selected"';
					}
					$strTemp_ .= '>' . $strDeductNameList[$j] . '</option>';
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
		'<table width="100%" cellpadding="0" cellspacing="0">'
		. '<tr>'
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
		. '</tr>'
		. '</table>';

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

function PageLink($strPath_, $page_, $page_id_, $rec_per_page_, $sort_, $id_, $keyword_, $by_, $dept_, $status_)
{
	$strTemp_ =
		$strPath_
		. '?page=' . $page_
		. '&page_id=' . $page_id_
		. '&rec_per_page=' . $rec_per_page_;

	if ($page_ <> 'list') {
		$strTemp_ .= '&id=' . $id_;
	} else {
		$strTemp_ .= '&sort=' . $sort_;
		$strTemp_ .= '&by=' . $by_;
		$strTemp_ .= '&dept=' . $dept_;
		$strTemp_ .= '&status=' . $status_;
	}


	return $strTemp_;
}
