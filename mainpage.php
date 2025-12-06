<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	index.php
 *          Date 		: 	12/09/2003
 *********************************************************************************/
require_once("common.php");
include("koperasiQry.php");

date_default_timezone_set("Asia/Jakarta");
setlocale(LC_TIME, 'ms_MY');

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if ($_SERVER['QUERY_STRING'] == '' or ($page <> 'main' and $page <> 'list' and $page <> 'view' and $page <> 'add' and $page <> 'edit' and $page <> 'login' and $page <> 'contact_us')) {
	if (get_session('Cookie_groupID') == '') {
		$strRedirect = 'login&error=' . $error;
	} else {
		$strRedirect = 'main';
	}
	print '<script>window.location="' . $_SERVER['PHP_SELF'] . '?page=' . $strRedirect . '";</script>';
	exit;
} else {
	if (get_session('Cookie_groupID') <> '' and $page <> 'main' and $page <> 'list' and $page <> 'view' and $page <> 'add' and $page <> 'edit' and $page <> 'contact_us') {
		$strRedirect = 'main';
		print '<script>window.location="' . $_SERVER['PHP_SELF'] . '?page=' . $strRedirect . '";</script>';
		exit;
	} else if (get_session('Cookie_koperasiID') <> $koperasiID and get_session('Cookie_groupID') <> '' and $page <> 'login' and $page <> 'contact_us') {
		$strRedirect = 'login&error=' . $error;
		print '<script>window.location="?vw=main&page=' . $strRedirect . '";</script>';
		exit;
	}
}

if ($page == 'main') {
	$strTitles = array('HALAMAN UTAMA');
	$strPages = array('');
	$align = 'left';
} else if ($page == 'list') {
	$strTitles = array('Halaman Utama', 'Senarai Buletin');
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else if ($page == 'add') {
	$strTitles = array('Halaman Utama', 'Tambah Buletin');
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else if ($page == 'edit') {
	if ($id <> '999') {
		$strTitles = array('Halaman Utama', 'Kemaskini Buletin');
	} else {
		$strTitles = array('Halaman Utama', 'Kemaskini Syarat & Kelayakan');
	}
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else if ($page == 'view') {
	if ($id <> '999') {
		$strTitles = array('Halaman Utama', 'Kandungan Buletin');
	} else {
		$strTitles = array('Halaman Utama', 'Syarat & Kelayakan');
	}
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else if ($page == 'contact_us') {
	if (get_session('Cookie_groupID') == '') {
		$strTitles = array('Halaman Utama', 'Hubungi Kami');
	} else {
		$strTitles = array('[NAMA KOPERASI]', 'Hubungi Kami');
	}
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else {
	$strTitles = array('');
	$strPages = array('');
	$align = 'center';
}

$strTemp .=
	'<div><h5 class="card-title">' . HeaderLink($strTitles, $strPages) . '</h5></div>'
	. '<div style="width: 100%; text-align:left">';

if (get_session('Cookie_groupID') <> '') {
	if (!isset($page_id))		$page_id = 1;
	if (!isset($page_range))	$page_range = 10;
	if (!isset($page_total))	$page_total = 1;
	if (!isset($page_start))	$page_start = 1;
	if (!isset($page_end))		$page_end = 1;
	if (!isset($rec_start))		$rec_start = 1;
	if (!isset($rec_end))		$rec_end = 1;
	if (!isset($rec_total))		$rec_total = 0;
	if (!isset($sort))			$sort = 'DESC';
	if (!isset($id))			$id = '';

	if ($page == 'main' or $page == 'list') {
		if ($page == 'main') {
			if (!isset($rec_per_page))	$rec_per_page = 10;

			$sSQL = 'SELECT MIN(ID) as num FROM kandungan';
			$GetMinID = &$conn->Execute($sSQL);

			$sSQL = 'SELECT * FROM kandungan WHERE ID=' . $GetMinID->fields('num');
			$GetFirstInfo = &$conn->Execute($sSQL);
		} else {
			if (!isset($rec_per_page))	$rec_per_page = 50;
		}

		$sSQL = 'SELECT * FROM kandungan';
		$sSQL .= ' WHERE 1';
		$sSQL .= ' ORDER BY postedDate ' . $sort;
		$GetData = &$conn->Execute($sSQL);

		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_id, $rec_per_page, $sort, $id);

		$strTemp .=
			'<div class="table-responsive">'
			. '<form id="content-form" name="MyForm" action="' . $strActionPage . '" method="post">'
			. '<div class="DEMO">'
			. '<table class="table" border="0" cellspacing="6" cellpadding="6" width="100%" align="center" style="background_image: ">';

		if ($page == 'main') {
			$strTemp .=
				'<div class="card bg-light">'
				. '<div class="card-body text-dark"><b>' . $GetFirstInfo->fields('tajuk') . '</b></div>'
				. '<div class="card-body text-dark">' . insertspace($GetFirstInfo->fields('kandungan')) . '</p>'
				. '</div>';

			if (get_session('Cookie_groupID') == '1' or get_session('Cookie_groupID') == '2') {

				$sSQLi = "";
				$sSQLi	= "SELECT count( userID ) AS bil FROM `userdetails` WHERE STATUS =0";
				$rsi = &$conn->Execute($sSQLi);
				$bil = $rsi->fields('bil');

				$sSQLj = "";
				$sSQLj	= "SELECT count( loanID ) AS loan FROM loans WHERE STATUS =0";
				$rsj = &$conn->Execute($sSQLj);
				$loan = $rsj->fields('loan');

				$sqlget =	"SELECT DISTINCT a.*, b.*, c.* FROM users a, userdetails b, userloandetails c"
					. " WHERE ( a.userID = b.userID AND b.userID = c.userID AND a.isActive = 1 and c.isApply = 1)"
					. " ORDER BY c.applyDate DESC";
				$GetMember = &$conn->Execute($sqlget);

				$penama = $GetMember->RowCount();

				// $strTemp .=
				// 	'<tr>'
				// 	. '<td>'
				// 	. '<div class="card-group">'
				// 	. '<div class="card bg-soft-info">'
				// 	. '<center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/follow.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Permohonan Anggota</h5>'
				// 	. '<h3 class="card-text" align="center"><font color="black">' . $bil . '</font></h3>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				// 	. '<div class="card bg-soft-success">'
				// 	. '<center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/change.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Permohonan Penama</h5>'
				// 	. '<h3 class="card-text" align="center"><font color="black">' . $penama . '</font></h3>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				// 	. '<div class="card bg-soft-info">'
				// 	. '<center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/signing.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Permohonan Pembiayaan</h5>'
				// 	. '<h3 class="card-text" align="center"><font color="black">' . $loan . '</font></h3>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				// 	. '<div class="card bg-soft-success">'
				// 	. '<center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/agreement.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Pengesahan Pembiayaan</h5>'
				// 	. '<h3 class="card-text" align="center"><font color="black">' . $loan . '</font></h3>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '</td>'
				// 	. '</tr><br/>'

				// 	//second line
				// 	. '<tr>'
				// 	. '<td>'
				// 	. '<div class="card-group">'
				// 	. '<div class="card bg-soft-danger">'
				// 	// .'<center><img class="card-img-top mt-3" style="width: 5rem; height: 5rem;" src="images/fee.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Pendapatan Terkini</h5>'
				// 	. '<h2 class="card-text" align="center"><font color="black">RP&nbsp;' . $bil . '</font></h2>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				// 	. '<div class="card bg-soft-warning">'
				// 	//   .'<center><img class="card-img-top mt-3" style="width: 5rem; height: 5rem;" src="images/saving.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Perbelanjaan Terkini</h5>'
				// 	. '<h2 class="card-text" align="center"><font color="black">RP&nbsp;' . $penama . '</font></h2>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				// 	. '<div class="card bg-soft-danger">'
				// 	//   .'<center><img class="card-img-top mt-3" style="width: 5rem; height: 5rem;" src="images/saving.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Aset Terkini</h5>'
				// 	. '<h2 class="card-text" align="center"><font color="black">RP&nbsp;' . $penama . '</font></h2>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				// 	. '<div class="card bg-soft-warning">'
				// 	//   .'<center><img class="card-img-top mt-3" style="width: 5rem; height: 5rem;" src="images/saving.png" alt="Picture is missing"></center>'
				// 	. '<div class="card-body">'
				// 	. '<h5 class="card-title" align="center">Equiti Terkini</h5>'
				// 	. '<h2 class="card-text" align="center"><font color="black">RP&nbsp;' . $penama . '</font></h2>'
				// 	. '</div>'
				// 	. '</div>'
				// 	. '</td>'
				// 	. '</tr>'
				// . '</table>';
			}
		}

		if ($page == 'main') {
			$strArchieveLink = '<a href="' . PageLink($_SERVER['PHP_SELF'], 'list', 1, 50, $sort, 0) . '">Arkib Buletin</a>';
			$strTemp .=
				'<table class="table" border="0" cellspacing="0" cellpadding="3" width="100%" align="center" style="background_image: ">
			<tr>'
				. '<td><h5 class="card-title"><i class="mdi mdi-bulletin-board"></i>&nbsp;BULETIN KOPERASI</h5></td>'
				. '</tr>';
		} else {
			$strArchieveLink = '';
			$strTemp .=
				'<tr>'
				. '<td colspan="2">'
				. '<table class="table" cellpadding="0" cellspacing="6">'
				. '<tr>'
				. '<td align="right"><b>Katakunci</b></td>'
				. '<td>'
				. '<input name="keyword" type="text" value="' . $keyword . '" maxlength="50" size="30">&nbsp;'
				. '<input name="submit" type="submit" value="Cari" onclick="PageRefresh();">'
				. '</td>'
				. '</tr>'
				. '</table>'
				. '</td>'
				. '</tr>';
			$strTemp .=
				'<tr>'
				. '<td><font class="maroonText"></td>'
				. '</tr>';
		}

		if ($GetData->RowCount() <> 0) {
			$strFieldNameList = array('&nbsp;', '<b>Perkara</b>', '<b>Tanggal</b>', '<b>Oleh</b>');
			$strFieldWidthList = array('15', '', '10%', '10%');
			$strFieldAlignList = array('right', 'left', 'center', 'center');

			$strRecordCountList = array(5, 10, 20, 30, 40, 50, 100);
			$strSQLSortList = array('DESC', 'ASC');
			$strTemp_del =
				'<tr valign="top" class="textFont">'
				. '<td>'
				. '<table class="table" width="100%">'
				. '<tr class="table-success">'
				. '<td>' . $strArchieveLink . '</td>'
				. '<td align="right">Paparan&nbsp;'
				. SelectForm('rec_per_page', $rec_per_page, $strRecordCountList, $strRecordCountList, 'onchange="PageRefresh();"')
				. '&nbsp;setiap mukasurat.&nbsp;'
				. SelectForm('sort', $sort, $strSQLSortList, $strSQLSortList, 'onchange="PageRefresh();"')
				. '</td>'
				. '</tr>'
				. '</table>'
				. '</td>'
				. '</tr>';

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
				'<tr class="table-light" valign="top" >'
				. '<td valign="top">';

			$strTemp .= BeginDataField($strFieldNameList, $strFieldWidthList, $strFieldAlignList);

			$countDisplayed = 0; // Initialize a counter for displayed records

			while (!$GetData->EOF and $nCount < $rec_end) {
				if ($GetData->fields('ID') != 999) {
					$strBulletin = '<a href="' . PageLink($_SERVER['PHP_SELF'], 'view', $page_id, $rec_per_page, $sort, $GetData->fields('ID')) . '">' . $GetData->fields('tajuk') . '</a>';

					$strFieldDataList = array(
						($countDisplayed + 1), // Use the new counter for displayed records
						$strBulletin,
						todate('/', $GetData->fields('postedDate')),
						$GetData->fields('postedBy')
					);

					$strMainTitle = $GetData->fields('tajuk');

					if (($countDisplayed % 2) == 0) {
						$strClass = '';
					} else {
						$strClass = 'Data';
					}
					$strTemp .= ContentDataField($strClass, $strFieldDataList, $strFieldAlignList);

					$countDisplayed += 1; // Increment displayed records counter
				}

				$nCount += 1;
				$GetData->MoveNext();
			}


			$strTemp .= EndDataField();

			$strTemp .=
				'</td>
				</tr>';

			$strPageTemp = '';

			if ($page_id > 1) {
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, 1, $rec_per_page, $sort, $id);
				$strPageTemp .= '<a href="' . $strActionPage . '"></a>';
				$strPageTemp .= '&nbsp;';
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, ($page_id - 1), $rec_per_page, $sort, $id);
				$strPageTemp .= '<a href="' . $strActionPage . '">Prev</a>';
				$strPageTemp .= '&nbsp;&nbsp;';
			}

			for ($i = $page_start; $i <= $page_end; $i++) {
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $i, $rec_per_page, $sort, $id);

				if ($i == $page_id) {
					$strPageTemp .= '<font class="redText">' . (($i - 1) * $rec_per_page + 1) . '-';
					if ($i <> $page_end) {
						$strPageTemp .= ($i * $rec_per_page);
					} else {
						$strPageTemp .= ($rec_total) - 1;
					}
					$strPageTemp .= '</font>';
				} else {
					$strPageTemp .= '<a href="' . $strActionPage . '"><u>' . (($i - 1) * $rec_per_page + 1) . '-';
					if ($i <> $page_end) {
						$strPageTemp .= ($i * $rec_per_page);
					} else {
						$strPageTemp .= ($rec_total) - 1;
					}
					$strPageTemp .= '</u></b></a>';
				}

				if ($i <> $page_end) {
					$strPageTemp .= '&nbsp;&nbsp;';
				}
			}

			if ($page_id < $page_total) {
				$strPageTemp .= '&nbsp;&nbsp;';
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, ($page_id + 1), $rec_per_page, $sort, $id);
				$strPageTemp .= '<a href="' . $strActionPage . '">Next</a>';
				$strPageTemp .= '&nbsp;';
				$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_total, $rec_per_page, $sort, $id);
				$strPageTemp .= '<a href="' . $strActionPage . '"></a>';
			}

			$strTemp .=
				'<tr>'
				. '<td align="left" colspan="' . count($strFieldDataList) . '">' . $strPageTemp . '</td>'
				. '</tr>';

			$strTemp .=
				'<tr>'
				. '<td class="textFont">Jumlah Data : <font class="redText">' . ($GetData->RowCount() - 1) . '</font></td>'
				. '</tr>';
		} else {
			if ($id == "") {
				$strTemp .= '<tr><td align="center"><b class="textFont">- Tidak Ada Data Untuk ' . $strTitle . '  -</b></td></tr>';
			} else {
				$strTemp .= '<tr><td align="center"><b class="textFont">- Carian rekod "' . $id . '" tidak jumpa  -</b></td></tr>';
			}
		}

		$strTemp .=
			'</table>'
			. '</div>'
			. '</form>'
			. '</div>';

		$strTemp .=
			'<script language="JavaScript"> '
			. 'function PageRefresh() { '
			. 'frm = document.MyForm; '
			. 'document.location = "' . $_SERVER['PHP_SELF'] . '?page=' . $page . '&page_id=1'
			. '&rec_per_page=" + frm.rec_per_page.options[frm.rec_per_page.selectedIndex].value + "'
			. '&sort=" + frm.sort.options[frm.sort.selectedIndex].value; '
			. '} '
			. '</script>';
	} else if ($page == 'view' or $page == 'add' or $page == 'edit') {

		if (!isset($topic))		$topic = '';
		if (!isset($content))	$content = '';
		$content = str_replace('class=\"ql-font-serif\"', 'style=\"font-family:serif\"', $content);
		$content = str_replace('class=\"ql-font-monospace\"', 'style=\"font-family:monospace\"', $content);
		$content = str_replace('class=\"ql-align-center\"', 'style=\"text-align:center\"', $content);
		$content = str_replace('class=\"ql-align-right\"', 'style=\"text-align:right\"', $content);

		if ($id == '') {
			$sSQL_ = 'SELECT MAX(ID) as num FROM kandungan';
			$GetData = &$conn->Execute($sSQL_);

			$id = $GetData->fields('num');
		}
		if (isset($_POST['Submit'])) {
			$bPass = false;

			if ($_POST['Submit'] == 'Simpan') {
				$sSQL_ = 'SELECT MAX(ID) as num FROM kandungan';
				$GetData = &$conn->Execute($sSQL_);

				$id = $GetData->fields('num') + 1;

				$sSQL = 'INSERT INTO kandungan 
						(ID, 
						tajuk, 
						kandungan, 
						postedDate, 
						postedBy) 
						VALUES ('
					. $id . ', \''
					. $topic . '\', \''
					. CheckQuotes($content)
					. '\', \''
					. date("Y-m-d H:i:s")
					. '\', \''
					. get_session('Cookie_fullName') . '\')';
				$conn->Execute($sSQL);

				$bPass = true;
				$page = 'view';
			} else if ($_POST['Submit'] == 'Kemaskini' and $id <> '') {

				$updatedDate = date("Y-m-d H:i:s");
				$sSQL = "";
				$sWhere = "";
				$sWhere = " ID= '" . $id . "' ";
				$sWhere = " WHERE (" . $sWhere . ") ";
				$sSQL =
					"UPDATE kandungan SET "
					. " tajuk = '" . $topic . "' ,"
					. " kandungan = '" . CheckQuotes($content) . "' ,"
					. " postedDate =  '" . $updatedDate . "' ,"
					. " postedBy = '" . get_session('Cookie_fullName') . "' ";

				$sSQL = $sSQL . $sWhere;
				$conn->Execute($sSQL);

				$bPass = true;
				$page = 'view';
			} else if ($_POST['Submit'] == 'Hapus' and $id <> '') {
				$sSQL = "DELETE FROM kandungan WHERE ID = " . $id;
				$conn->Execute($sSQL);

				$sSQL_ = 'SELECT MAX(ID) as num FROM kandungan';
				$GetData = &$conn->Execute($sSQL_);

				$id = $GetData->fields('num');

				$bPass = true;
				$page = 'main';
			}

			if ($bPass) {
				$strActivity = $_POST['Submit'] . ' Kandungan Buletin';
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 9);
				print '<script>window.location="' . $_SERVER['PHP_SELF'] . '?page=' . $page . '&id=' . $id . '";</script>';
				exit;
			}
		}

		if ($page == 'view' or $page == 'edit') {

			$sSQL = 'SELECT * from kandungan WHERE ID = ' . $id;
			$GetData = $conn->Execute($sSQL);
			$strTopic = $GetData->fields('tajuk');
			$strContent = $GetData->fields('kandungan');
			$strPostedBy = $GetData->fields('postedBy');
			$strPostedDate = $GetData->fields('postedDate');
		} else {
			$strTopic = '';
			$strContent = '';
			$strPostedBy = get_session('Cookie_fullName');
			$strPostedDate = '';
		}

		$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_id, $rec_per_page, $sort, $id);

		$strTemp .=
			'<form id="content-form" name="MyForm" action="' . $strActionPage . '&mn=901" method="post">'
			. '<table width="100%" cellpadding="3" cellspacing="0">';

		if ($page == 'edit' or $page == 'view' and $id == '999') {
			$strNameList = array('Ubah', 'Papar');
			$strPageList = array('edit', 'view');
		} else {
			$strNameList = array('Tambah', 'Ubah', 'Papar');
			$strPageList = array('add', 'edit', 'view');
		}
		if ($page == 'view') {
			$strTopicText = '<font class="maroonText">' . $strTopic . '</font>';
		} else {
			$strTopicText = '<input name="topic" class="form-control" value="' . $strTopic . '" size="80" maxlength="256" />';
		}

		$strTemp .=
			'<tr>'
			. '<td>';

		if (get_session('Cookie_groupID') <> 0) {
			$strTemp .=
				'<table width="100%" cellpadding="0" cellspacing="0">'
				. PageSelection($page, $strTopicText, $strNameList, $strPageList)
				. '</table>';
		} else {
			$strTemp .= $strTopicText;
		}

		$strTemp .=
			''
			. 'Oleh : ' . $strPostedBy
			. '</td>'
			. '</tr>';

		if ($page == 'view') {
			$strTemp .= '<tr><td><p>' . $strContent . '</p></td></tr>';
			if (get_session('Cookie_groupID') <> 0 and $page <> 'add' and $id <> '999') {
				$strTemp .= '<tr><td><input type="submit" class="btn btn-sm btn-danger" name="Submit" value="Hapus" onclick="return ConfirmDelete();"></td></tr>';
			}
		} else {
			$strTemp .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.9/jodit.min.css" />
						<script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.9/jodit.min.js"></script>';
			// $strTemp .= '<tr><td><textarea name="content" class="form-control"  rows="20">'.$strContent.'</textarea></td></tr>';
			$strTemp .= '<tr><td><textarea id="editor" name="content">' . $strContent . '</textarea>
			<script>
				const editor = Jodit.make("#editor", {
					"useSearch": false,
					"uploader": {
					"insertImageAsBase64URI": true
					},
					"showCharsCounter": false,
					"showWordsCounter": false,
					"showXPathInStatusbar": false,
					"buttons": "bold,italic,underline,strikethrough,ol,ul,font,fontsize,paragraph,lineHeight,superscript,subscript,image,hr,table,link,indent,outdent,left,center,right,brush,source"
				});
		  </script>';

			$strTemp .= '<tr><td>';
			if ($page == 'add') {
				$strTemp .= '<input type="submit" class="btn btn-sm btn-primary" name="Submit" value="Simpan">';
			} else {
				$strTemp .= '<input type="submit" class="btn btn-sm btn-primary" name="Submit" value="Kemaskini">';
			}

			if (get_session('Cookie_groupID') <> 0 and $page <> 'add' and $id <> '999') {
				$strTemp .= '&nbsp;<input type="submit" class="btn btn-sm btn-danger" name="Submit" value="Hapus" onclick="return ConfirmDelete();">';
			}

			$strTemp .= '</td></tr>';
		}
		$strTemp .=
			'</table>'
			. '</form>';

		if (get_session('Cookie_groupID') <> 0) {
			$strTemp .=
				'<script language="JavaScript"> '
				. 'function ConfirmDelete() { '
				. 'if (confirm(\'Anda Pasti Kandungan Buletin Ini Ingin Dihapuskan?\')) {'
				. 'return true;'
				. '} else {'
				. 'return false;'
				. '}'
				. '}'
				. '</script>';
		}
	}
}

if (get_session('Cookie_groupID') == '' and $page == 'login') {
	if (!isset($error)) $error = 0;

	if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));
	$Gambar = "upload_images/" . $pic;

	$strTemp .=
		'                	<center>
                        	<img id="elImage" src="' . $Gambar . '" style="width: 400px; max-width:100%;" alt="Logo Koperasi">
						</center>
						<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-3 mb-3 border-bottom"></div>';

	$strTemp .=
		'    
	<form action="?vw=login" method="post">
                        
	<input type="hidden" name="continue" value="' . $continue . '">
	<!--h4 class="card-title" align="center">LOG MASUK</h4-->		
					
	<div align="center" class="mb-1"><i class="fas fa-user text-primary"></i>&nbsp;
		<input type="text" class="form-controlx" name="username" placeholder="Id Pengguna" size="20" maxlength="20">
	</div>
	<div align="center" class="mb-2"><i class="mdi mdi-key text-primary"></i>&nbsp;
		<div style="position: relative; display: inline-block;">
			<input id="password" type="password" class="form-controlx" placeholder="Kata Sandi" name="password" size="20" maxlength="20">
			<div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
				<a id="eyeIcon" href="#" onclick="togglePassword()">
					<i id="eyeIconInner" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
				</a>
			</div>
		</div>    
	</div>
	<div align="center"><a href="?vw=lostPassword">&nbsp;Lupa Kata Sandi dan id pengguna?<a/></div>
	<div align="center" class="mt-3">                                                          
		<input type="submit" name="action" class="btn w-lg btn-primary mb-3" value="MASUK">                                                                                
	</div>';

	if ($error <> 0) {
		/*
		$strTemp .=
		'<div>&nbsp;</div>
		<div style="width: 500px; text-align:left">
		<table class="lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
			<tr>
				<td class="borderallred" align="center" valign="middle"><div class="headerred"><b>RALAT</b></div></td>
			</tr>
			<tr>
				<td class="borderleftrightbottomred">
					<table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">
						<tr>
							<td class="red" height="30" align="center">'; */
		switch ($error) {
			case 1:
				//$strTemp .= 'ID Pengguna tidak aktif!';
				alert("ID Pengguna tidak aktif!");
				break;
			case 2:
				//$strTemp .= 'ID Pengguna atau Katalaluan tidak sah!';
				alert("ID Pengguna atau Katalaluan tidak sah!");
				break;
			case 3:
				//$strTemp .= 'Tiada ID Pengguna atau Katalaluan dimasukkan!';
				alert("Tiada ID Pengguna atau Katalaluan dimasukkan!");
				break;
		}
		/*
		$strTemp .=
							'</td>
						</tr>			
					</table>
				</td>
			</tr>
		</table>
		</div>'; */
	}

	$strTemp .=
		'
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-1 border-bottom"></div>
          
                <p align="center">Tertarik menjadi anggota?<br/>
                        <a href="?vw=checkIC"><b>&nbsp;DAFTAR ANGGOTA BARU</b></font></a></p>

                        <p align="center">Cek status keanggotaan?<br/>                         
                        <a href="?vw=checkICinfo"><b>STATUS KEANGGOTAAN</b></font></a> <br/><br/>						
						
						<a align="center" class="text-secondary" href="">Terma & Syarat</a>&nbsp;|
			  			<a class="text-secondary" href="">Privasi</a>&nbsp;|
						<a class="text-secondary" href="AMLA.pdf">AMLA</a>&nbsp;|
						<a class="text-secondary" href="">iKOOP Mobile</a>			

						
                    	</p>
               </form>
			   
        ';
}

if ($page == 'contact_us') {

	$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email FROM setup
        WHERE setupID = 1";
	$rss = &$conn->Execute($ssSQL);

	$coopName = $rss->fields(name);
	$address1 = $rss->fields(address1);
	$address2 = $rss->fields(address2);
	$address3 = $rss->fields(address3);
	$address4 = $rss->fields(address4);
	$noPhone = $rss->fields(noPhone);
	$email = $rss->fields(email);

	$strTemp .=
		'<div class="row">
	<p>Jika terdapat sebarang masalah, sila hubungi pejabat [NAMA KOPERASI] seperti tertera di bawah :</p>
	<p>	   
	<b>' . $coopName . '</b><br />
	' . $address1 . ',<br />
	' . $address2 . ',<br />
	' . $address3 . ',<br />
	' . $address4 . '.<br />
	TEL: ' . $noPhone . '<br />
	EMEL: ' . $email . '<br />
	</p>
	</div>';
}

print $strTemp;

include("footer.php");

function BeginDataField($strFieldNameList_, $strFieldWidthList_, $strFieldAlignList_)
{
	$strTemp_ .=
		'<table border="0" cellspacing="1" cellpadding="4" width="100%" class="lineBG">'
		. '<tr class="header">';

	for ($i = 0; $i < count($strFieldNameList_); $i++) {
		$strTemp_ .= '<td nowrap align="' . $strFieldAlignList_[$i] . '" width="' . $strFieldWidthList_[$i] . '">' . $strFieldNameList_[$i] . '</td>';
	}

	$strTemp_ .=	'</tr>';

	return $strTemp_;
}

function EndDataField()
{
	$strTemp_ .= ' </table>';

	return $strTemp_;
}

function ContentDataField($strClass_, $strFieldDataList_, $strFieldAlignList_)
{
	$strTemp_ = '<tr>';

	for ($i = 0; $i < count($strFieldDataList_); $i++) {
		$strTemp_ .= '<td align="' . $strFieldAlignList_[$i] . '" valign="top"';
		if ($strClass_ == '') {
			$strTemp_ .= ' class="Data"';
		} else {
			$strTemp_ .= ' class="' . $strClass_ . '"';
		}
		if ($i <> 3) {
			$strTemp_ .= ' nowrap="nowrap"';
		}
		$strTemp_ .= '>';
		$strTemp_ .= $strFieldDataList_[$i];
		$strTemp_ .= '</td>';
	}

	$strTemp_ .= '</tr>';

	return $strTemp_;
}

function PageSelection($page_, $strExtra_, $strNameList_, $strPageList_)
{
	global $id;
	/*
	$strTemp_ .=
		'<tr>'
			.'<td align="left">'.$strExtra_.'</td>'
			.'<td align="right">';

	for ($i = 0; $i < count($strNameList_); $i++) {
		if ($i <> 0) {
			$strTemp_ .= '&nbsp;|&nbsp;';
		}

		if ($page_ == $strPageList_[$i]) {
			$strTemp_ .= '<font class="redText">'.$strNameList_[$i].'</font>';
		} else {
			$strTemp_ .= '<a href="'.$_SERVER['PHP_SELF'].'?page='.$strPageList_[$i];
			$strTemp_ .= '&id='.$id;
			$strTemp_ .= '">'.$strNameList_[$i].'</a>';
		}
	}

	$strTemp_ .=
			'&nbsp;</td>'
		.'</tr>';
*/



	$strTemp_ .=
		'<td align="right" colspan=2>'
		. '<div class="btn-group btn-group-sm mt-2 mb-2" role="group" aria-label="Basic example">';

	for ($i = 0; $i < count($strNameList_); $i++) {
		//		if ($i <> 0) {
		//			$strTemp_ .= '&nbsp;|&nbsp;';
		//		}

		if ($page_ == $strPageList_[$i]) {
			$strTemp_ .= '<button type="button" class="btn btn-dark"><font class="redText">' . $strNameList_[$i] . '</font></button>';
		} else {
			$strTemp_ .= '<button type="button" class="btn btn-dark"><a href="' . $_SERVER['PHP_SELF'] . '?mn=901&page=' . $strPageList_[$i];
			$strTemp_ .= '&id=' . $id;
			$strTemp_ .= '">' . $strNameList_[$i] . '</a></button>';
		}
	}

	$strTemp_ .=
		'&nbsp;</div></td>'
		. '</tr>
                                    <tr><td colspan=2>' . $strExtra_ . '</td></tr>
                                    <tr>';


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

function HeaderLink($strNameList_, $strPageList_)
{
	$strTemp_ = '';
	for ($i = 0; $i < count($strNameList_); $i++) {
		if ($i <> 0) {
			$strTemp_ .= '&nbsp;>&nbsp;';
		}
		if ($strPageList_[$i] <> '') {
			$strTemp_ .= '<a class="maroon" href="' . $strPageList_[$i] . '">';
			$strTemp_ .= ($strNameList_[$i]);
			$strTemp_ .= '</a>';
		} else {
			$strTemp_ .= ($strNameList_[$i]);
		}
	}
	return $strTemp_;
}

function PageLink($strPath_, $page_, $page_id_, $rec_per_page_, $sort_, $id_)
{
	$strTemp_ = $strPath_ . '?&mn=901&page=' . $page_;
	if ($page_ == 'main' or $page_ == 'list') {
		$strTemp_ .= '&page_id=' . $page_id_ . '&rec_per_page=' . $rec_per_page_ . '&sort=' . $sort_;
	}
	if ($page_ == 'view' or $page_ == 'edit') {
		$strTemp_ .= '&id=' . $id_;
	}

	return $strTemp_;
}


print '
<script language="JavaScript">
    function togglePassword() {
        var passwordInput = document.getElementById("password");
        var eyeIconInner = document.getElementById("eyeIconInner");

        // Toggle the password field visibility
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIconInner.classList.remove("mdi-eye-off-outline");
            eyeIconInner.classList.add("mdi-eye-outline");
        } else {
            passwordInput.type = "password";
            eyeIconInner.classList.remove("mdi-eye-outline");
            eyeIconInner.classList.add("mdi-eye-off-outline");
        }
    }
</script>';
