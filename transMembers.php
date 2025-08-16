<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
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

$strTitle = 'Jadual Potongan';
$strTitle = 'Senarai ' . $strTitle;
$strHeaderTitle = strtoupper($strTitle);

$strHeaderTitle = '<a class="maroon" href="index.php">LAMAN UTAMA</a><b>' . '&nbsp;>&nbsp;' . $strHeaderTitle . '</b>';

$strTypeNameList	= array('Nombor Anggota', 'Nama Anggota', 'No KP Baru');
$strTypeValueList	= array(1, 2, 3);
$strStatusNameList	= array($statusList[1], $statusList[3],  $statusList[4]);
$strStatusValueList	= array($statusVal[1], $statusVal[3], $statusVal[4]);
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

$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_id, $rec_per_page, $sort, $id, $keyword, $by, $dept, $status);

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="' . $strActionPage . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">'
	. '<tr>'
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
	. '</tr>'
	. '<tr>'
	. '<td align="right"><b>Status</b></td>'
	. '<td>' . SelectForm('status', $status, $strStatusNameList, $strStatusValueList, 'onchange="PageRefresh();"') . '</td>'
	. '</tr>';

$strTemp .=	'</table>'
	. '</td>'
	. '</tr>';

//$strCheckboxTemp = '<input name="all" type="checkbox" onclick="SelectAll()" style="padding:0px;margin:0px" />';

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

if ($GetData->RowCount() <> 0) {
	$strFieldNameList = array($strCheckboxTemp, 'NomborAnggota/Nama', 'Nombor KP Baru', 'Jabatan/Cawangan', 'Tarikh Anggota', 'Bil. Rekod', 'Jumlah (RM)');
} else {
	$strFieldNameList = array('&nbsp;', 'NomborAnggota/Nama', 'Nombor KP Baru', 'Jabatan/Cawangan', 'Tarikh Anggota', 'Bil. Rekod', 'Jumlah (RM)');
}
$strFieldWidthList = array('15', '', '10%', '20%', '10%', '10%', '8%');
$strFieldAlignList = array('right', 'left', 'left', 'left', 'center', 'right', 'right');

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

if ($GetData->RowCount() <> 0) {
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

		$strActionPage = PageLink('transMembersDeduct.php', 'view', 1, $rec_per_page, $sort, $GetData->fields('userID'), $keyword, $by, $dept, $status);
		$strPageTemp = '<a href="' . $strActionPage . '">' . $GetData->fields('memberID') . '&nbsp;-&nbsp;' . strtoupper($GetData->fields('name')) . '</a>';

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
		$nDeductAmount = number_format($nDeductAmount, 2);
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

	$strFieldDataList = array($nCount, '<center>- Tiada Rekod -</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>', '<center>-</center>');
	$strTemp .= ContentDataField('', $strFieldDataList, $strFieldAlignList, NULL, NULL, NULL, false);

	$strTemp .=	EndDataField()
		. '</td>'
		. '</tr>';
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
	static $j = 0;
	$strTemp_ = '<tr>';
	$j++;
	for ($i = 0; $i < count($strFieldDataList_); $i++) {
		$strTemp_ .= '<td class="Data" align="' . $strFieldAlignList_[$i] . '" valign="top" nowrap="nowrap">';
		if ($i == 0) {
			if ($strDeductId_ <> '') {
				//$strTemp_ .= '<input name="checkbox_id[]" type="checkbox" value="0" style="padding:0px;margin:0px" onclick="SelectCurrent()" />';
				$strTemp_ .= $j;
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
