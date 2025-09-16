<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: 
 *			Date 		: 
 *			Parameter	:-
 *********************************************************************************/
//include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>' . $emaNetis . '</title>
<meta name="Keywords"  content="' . $siteKeyword . '">
<meta name="Description" content="' . $siteDesc . '">
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<!--LINK rel="stylesheet" href="images/default.css" -->
</head>

<body>

';

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
//list - 450
//$conn->debug =1;
if (!isset($page))			$page = 'main';
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
if (!isset($group))			$group = '';
if (!isset($code))			$code = '';
if (!isset($id))			$id = '';
if (!isset($by))			$by = '1';
if (!isset($keyword))		$keyword = '';
if (!isset($dept))			$dept = '';
if (!isset($status))		$status = '1';
if (!isset($month))			$month = 0;
if (!isset($year))			$year = date("Y");
if (!isset($group))			$group = 0;
if (!isset($code_name))		$code_name = '';
if (!isset($sort_num))		$sort_num = 1;
if (!isset($title))			$title = '';

if (!isset($subject))		$subject = '';
if (!isset($header))		$header = '';
if (!isset($content))		$content = '';
$content = trim(preg_replace('/\s+/', ' ', $content));
$content = str_replace("border-color:", "border:1px solid", $content);
if (!isset($footer))		$footer = '';

//if (get_session("Cookie_groupID") <> 2 AND ($page == 'add' OR $page == 'edit')) {
//	print '<script>;window.location="'.$_SERVER['PHP_SELF'].'";
//}

$strTypeNameList	= array();
$strLoanTypeList	= array();

$strTypeNameList[]	= 'Surat [NAMA KOPERASI] Kepada Anggota';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Pembiayaan';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Tawaran Pembiayaan';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Dividen';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Anggota Kepada [NAMA KOPERASI]';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Anggota Hutang Lapuk';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Insurans';
$strLoanTypeList[]	= array();
$strTypeNameList[]	= 'Surat Tawaran Tawwaruq';
$strLoanTypeList[]	= array();

//$strTypeNameList[]	= 'Surat-surat Lain';
//$strLoanTypeList[]	= array();

for ($i = 0; $i < count($strTypeNameList); $i++) {
	$strTypeValueList[$i] = $i;
}

$strMonthNameList	= array('- Semua -', 'Jan', 'Feb', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'Sept', 'Okt', 'Nov', 'Dis');
$strMonthValueList	= array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

$strTitle = 'Daftar Surat/Email';
$currPage = "?vw=memberLetter&mn=$mn";

if ($page == 'main') {
	$strHeaderTitle = strtoupper($strTitle);
} else if ($page == 'list') {
	$strTitle = $strTitle;
	$strHeaderTitle =
		'<a class="maroon" href="' . $currPage . '">' . strtoupper($strTitle) . '</a>&nbsp;>&nbsp;'
		. strtoupper('Daftar ' . $strTitle);
} else if ($page == 'add' or $page == 'edit' or $page == 'view') {
	$strTitle = $strTitle;
	$strHeaderTitle =
		'<a class="maroon" href="' . $currPage . '">' . strtoupper($strTitle) . '</a>&nbsp;>&nbsp;';
	if ($page == 'add') {
		$strHeaderTitle .= strtoupper('Tambah ' . $strTitle);
	} else if ($page == 'edit') {
		$strHeaderTitle .= strtoupper('Ubah ' . $strTitle);
	} else if ($page == 'view') {
		$strHeaderTitle .= strtoupper('Papar ' . $strTitle);
	}
}

if (isset($_POST['Submit'])) {
	if ($page == 'add' or $page == 'edit' or $page == 'view') {
		$bPass = false;

		if ($_POST['Submit'] == 'Simpan' or $_POST['Submit'] == 'Duplicate') {
			$sSQL_ = 'SELECT MAX(ID) as num FROM letters';
			$GetData = &$conn->Execute($sSQL_);

			if ($GetData->RowCount() <> 0) {
				$code = $GetData->fields('num') + 1;
			} else {
				$code = 1;
			}

			$sSQL = 'INSERT INTO letters (ID, groupID, codeName, sortNum, title, subject, header, content, footer, createDate, createBy, updateDate, updateBy) VALUES ('
				. $code . ', ' . $group . ', \'' . $code_name . '\', ' . $sort_num . ', \'' . $title . '\', \'' . $subject . '\', \'' . CheckQuotes($header) . '\', \'' . CheckQuotes($content) . '\', \'' . CheckQuotes($footer) . '\', \'' . date("Y-m-d H:i:s") . '\', \'' . get_session('Cookie_fullName') . '\', \'' . date("Y-m-d H:i:s") . '\', \'' . get_session('Cookie_fullName') . '\')';
			$conn->Execute($sSQL);

			$bPass = true;
			$page = 'view';
		} else if ($_POST['Submit'] == 'Kemaskini' and $code <> '') {
			$sSQL =
				'UPDATE letters'
				. ' SET'
				. ' groupID = ' . $group . ','
				. ' codeName = \'' . $code_name . '\','
				. ' sortNum = ' . $sort_num . ','
				. ' title = \'' . $title . '\','
				. ' subject = \'' . $subject . '\','
				. ' header = \'' . CheckQuotes($header) . '\','
				. ' content = \'' . CheckQuotes($content) . '\','
				. ' footer = \'' . CheckQuotes($footer) . '\','
				. ' updateDate = \'' . date("Y-m-d H:i:s") . '\','
				. ' updateBy = \'' . get_session('Cookie_fullName') . '\''
				. ' WHERE ID = ' . $code;
			$conn->Execute($sSQL);

			$bPass = true;
			$page = 'view';
		} else if ($_POST['Submit'] == 'Hapus' and $code <> '') {
			$sSQL = "DELETE FROM letters WHERE ID = " . $code;
			$conn->Execute($sSQL);

			$sSQL_ = 'SELECT MAX(ID) as num FROM letters';
			$GetData = &$conn->Execute($sSQL_);

			$code = $GetData->fields('num');

			$bPass = true;
			$page = 'main';
		}

		if ($bPass) {
			$strActivity = $_POST['Submit'] . ' ' . $strTitle;
			//activityLog(stip_tags($sSQL), $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'));
			print '<script>window.location="' . $currPage . '&page=' . $page . '&group=' . $group . '&code=' . $code . '";</script>';
			exit;
		}
	} else if ($page == 'list') {
		if ($_POST['Submit'] == 'Cetak Surat' || $_POST['Submit'] == 'Hantar Email') {
			$id = '';
			for ($i = 0; $i < count($user_id); $i++) {
				if ($i <> 0) {
					$id .= ':';
				}
				$id .= $user_id[$i];
			}
			print
				'<script>'
				//				.'alert("letter.php?group='.$group.'&code='.$code.'&id='.$id.'&type='.$type.'&head='.$head.'");'
				. 'window.open("letter.php?group=' . $group . '&code=' . $code . '&id=' . $id . '&type=' . $type . '&head=' . $head . '","pop", "top=100, left=100, width=700, height=400, scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");'
				. '</script>';
		}
	}
}


$strHeaderTitle = '&nbsp;' . $strHeaderTitle;
//$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, $page_id, $rec_per_page, $sort, $group, $code, $id, $keyword, $by, $dept, $status, $month, $year);
$strActionPage = PageLink($currPage, $page, $page_id, $rec_per_page, $sort, $group, $code, $id, $keyword, $by, $dept, $status, $month, $year);


$strHtml .= '
<div class="table-responsive">
<div class="maroon" align="left"><b>' . $strHeaderTitle . '</b></div>
<form name="MyForm" action="' . $strActionPage . '" method="post">
<table border="0" cellspacing="1" cellpadding="3" width="99%" align="left">
';

if ($page <> 'list') {
	if ($page == 'add' or $page == 'edit' or $page == 'view') {
		if ($code == '') {
			$sSQL_ = 'SELECT MAX(ID) as num FROM letters';
			$GetData = &$conn->Execute($sSQL_);

			$code = $GetData->fields('num');
		}

		$sSQL = 'SELECT * FROM letters';
		$sSQL .= ' WHERE ID=' . $code;

		$GetData = $conn->Execute($sSQL);

		if ($GetData->RowCount() <> 0 and $page <> 'add') {
			$strNameList = array('Tambah', 'Lihat', 'Ubah');
			$strPageList = array('add', 'view', 'edit');

			$group		= intval($GetData->fields('groupID'));
			$code_name	= $GetData->fields('codeName');
			$sort_num	= intval($GetData->fields('sortNum'));
			$title		= $GetData->fields('title');
			$header		= $GetData->fields('header');
			$subject	= $GetData->fields('subject');
			$content	= $GetData->fields('content');
			$footer		= $GetData->fields('footer');
		} else {
			$strNameList = array('Tambah');
			$strPageList = array('add');

			if ($page <> 'add') {
				$page = 'view';
			}
		}

		//if (get_session("Cookie_groupID") == 2) {
		$strHtml .=
			'<tr valign="top" class="textFont">'
			. '<td>'
			. PageSelection($page, $strNameList, $strPageList)
			. '<hr size="1px">'
			. '</td>'
			. '</tr>';


		if ($page == 'view') {
			if ($GetData->RowCount() <> 0) {
				$strHtml .=
					'<tr>
				<td>
				<table cellpadding="0" cellspacing="3" class="table table-sm table-striped">';
				$strHtml .= '
			<tr><td>Jenis Surat</td><td>&nbsp;&nbsp;</td><td>' . $strTypeNameList[$group] . '</td></tr>';

				$strHtml .= '
			<tr><td>Kod Surat</td><td>&nbsp;&nbsp;</td><td>' . $code_name . '</td></tr>';

				$strHtml .= '
			<tr><td>Nombor Susunan</td><td>&nbsp;&nbsp;</td><td>' . $sort_num . '</td></tr>';

				$strHtml .= '
			<tr><td>Nama Surat</td><td>&nbsp;&nbsp;</td><td>' . $title . '</td></tr>';
				$strHtml .=
					'</table>
				</td>
				</tr>';
				$strHtml .= '
			<tr><td colspan="3"><hr size="1px"></td></tr>
			';
				$strHtml .=
					'<tr>
			<td>
				<table width="600" cellpadding="0" cellspacing="3">
				<tr>
				<td colspan="3">
				<p>' . CheckLineBreak($header) . '</p>
				<u><b>' . CheckLineBreak(strtoupper($subject)) . '</b></u>
				<p>' . CheckLineBreak($content) . '</p>
				<p>&nbsp;</p>
				<p>' . CheckLineBreak($footer) . '</p>
				</td>
				</tr>
				</table>
			</td>
			</tr>';
			} else {
				$strHtml .= '
				<tr><td>tiada</td></tr>
					';
			}
		} else {
			$strHtml .=
				'<tr>
				<td>
				<table cellpadding="3" cellspacing="3">';
			$strHtml .= '
			<tr>
			<td>Jenis Surat</td>
			<td>&nbsp;&nbsp;</td>
			<td>' . SelectForm('group', $group, $strTypeNameList, $strTypeValueList, 'onchange="document.MyForm.submit();"') . '</td>
			</tr>';
			$strHtml .= '
			<tr>
				<td>Kod Surat</td>
				<td>&nbsp;&nbsp;</td>
				<td><input name="code_name" class="form-control-sm" type="text" size="20" maxlength="50" value="' . $code_name . '"></td>
			</tr>';
			$strHtml .= '
			<tr>
				<td>Nombor Susunan</td>
				<td>&nbsp;&nbsp;</td>
				<td><input name="sort_num" class="form-control-sm" type="text" size="11" maxlength="11" value="' . $sort_num . '"></td>
			</tr>';
			$strHtml .= '
			<tr>
				<td>Nama Surat</td>
				<td>&nbsp;&nbsp;</td>
				<td><input name="title" class="form-control-sm" type="text" size="50" maxlength="50" value="' . $title . '"></td>
			</tr>';
			$strHtml .=
				'
				</table>
				</td>
			</tr>';

			function selectSyntax($name, $group, $code = 0)
			{

				$arrSyntax = array('[no_rujukan]', '[alamat]', '[alamat_perjanjian]', '[alamat_majikan]', '[tuan/puan]', '[title]', '[no_anggota]', '[nama]', '[kp_baru]', '[kp_lama]', '[jab/caw]', '[tarikh]', '[hari]', '[bulan]', '[tahun]', '[sesi]', '[tarikh_diterima]', '[tarikh_lulus]', '[tarikh_ditolak]', '[tarikh_diterimaT]', '[tarikh_lulusT]', '[tarikh_ditolakT]', '[no_akaun_tabungan]', '[no_pekerja]', '[yuran_bulan]', '[jum_yuran_terkumpul]', '[jum_yuran_terkumpul_akhir]', '[jum_dividen]', '[dividen_bonus]', '[peratus_dividen]', '[TarikhTamatInsuran]', '[NoKenderaan]', '[no_sijil]', '[kadar_u]', '[itemType]');

				$arrSyntaxName = array('Nomor Rujukan', 'Alamat', 'Alamat Perjanjian', 'Alamat Surat', 'Tuan/Puan', 'Title', 'Nomor Anggota', 'Nama', 'Kp. Baru', 'Kp. Lama', 'Jab/Caw', 'Tarikh', 'Hari', 'Bulan', 'Tahun', 'Sesi', 'Tarikh. Diterima', 'Tarikh. Lulus', 'Tarikh. Ditolak', 'Tkh. Diterima Berhenti', 'Tkh. Lulus Berhenti', 'Tkh. Ditolak Berhenti', 'Nombor Akaun. Tabungan', 'Nomor Karyawan', 'Yuran Bulan', 'Jum. Yuran Terkumpul', 'Jum. Yuran Tkumpul Bakhir', 'Jum. Dividen', 'Dividen Bonus', 'Persentase Dividen', 'Tarikh Tamat Insuran', 'No Plat Kenderaan', 'No Sijil', 'Untung Loan', 'Barang Komoditi');

				$arrLoanSyntax = array('[tarikh_diterimaL]', '[tarikh_lulusL]', '[tarikh_ditolakL]', '[no_rujukan]', '[no_bon_biaya]', '[nama_biaya]', '[tempoh_biaya]', '[tempoh_bulan_biaya]', '[tempoh_bulan_biaya-1]', '[jum_biaya]', '[kadar_untung]', '[jum_biayauntung]', '[jum_biaya_kata]', '[jum_jualan_kata]', '[bayar_bulan]', '[bayar_bulan_akhir]', '[bayar_faedah]', '[bayar_faedah_akhir]', '[jumlah_bayarbln]', '[jumlah_bayarbln_akhir]', '[bayar_bulanStr]', '[bayar_bulan_akhirStr]', '[bayar_faedah_akhirStr]', '[jum_bayar_balik]', '[baki_biaya]', '[baki_biaya_akhir]', '[bertindih_caj]', '[nama_penjamin1]', '[kp_penjamin1]', '[no_anggota_penjamin1]', '[nama_penjamin2]', '[kp_penjamin2]', '[no_anggota_penjamin2]', '[nama_penjamin3]', '[kp_penjamin3]', '[no_anggota_penjamin3]', '[no_sijil]', '[kadar_u]', '[itemType]');

				$arrLoanSyntaxName = array('Tkh. Diterima PBiaya.', 'Tkh. Lulus PBiaya.', 'Tkh. Ditolak PBiaya.', 'Nombor ruj. Biaya', 'Nombor Bond Biaya', 'Nama biaya', 'Tph. Tahun Biaya', 'Tempoh Bulan Biaya', 'Tempoh Sebelum Akhir', 'Jum. PBiaya', 'Kadar Untung', 'Jualan Tertangguh', 'Biaya Perkataan', 'Jualan Perkataan', 'Bayar Bulan', 'Bayar Bulan Akhir', 'Untung Sebulan', 'Untung Akhir', 'Jumlah Bulanan', 'Jumlah Bulan Akhir', 'Bayar Bulan Kata', 'Bayar Bulan Akhir Kata', 'Untung Sebulan', 'Untung Sebulan Akhir Kata', 'Jumlah Dibayar', 'Baki PBiaya', 'Baki PBiaya Bakhir', 'Bayar Khidmat', 'Nama Penjamin1', 'Kp. Penjamin1', 'Nombor Ang. Penjamin1', 'Nama Penjamin2', 'Kp. Penjamin2', 'Nombor Ang. Penjamin2', 'Nama Penjamin3', 'Kp. Penjamin3', 'Nombor Ang. Penjamin3', 'No Sijil', 'Untung Loan', 'Barang Komoditi');

				if ($group == 1 || $group == 2 || $group == 5 || $group == 6 || $group == 7) {
					$arrSyntax = array_merge($arrSyntax, $arrLoanSyntax);
					$arrSyntaxName =   array_merge($arrSyntaxName, $arrLoanSyntaxName);
				}

				$strSelect = '
				<select name="' . $name . '" size="37" multiple="multiple" class="form-control-sm">
				<option value="">- Pilihan Data -';
				for ($i = 0; $i < count($arrSyntax); $i++) {
					$strSelect .= '	<option value="' . $arrSyntax[$i] . '" ';
					//if ($code == $arrSyntax[$i]) $strSelect .= ' selected';
					$strSelect .=  '>' . $arrSyntaxName[$i];
				}
				$strSelect .= '</select>';
				return $strSelect;
			}
			$strHtml .=
				'<tr>
				<td>
				<table cellpadding="0" cellspacing="3">';
			$strHtml .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.9/jodit.min.css" />
									<script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.9/jodit.min.js"></script>
								<tr>
									<td><textarea id="editor" name="content">' . $content . '</textarea></td>
									<script>
										const editor = Jodit.make("#editor", {
											"useSearch": false,
											"uploader": {
											"insertImageAsBase64URI": true
											},
											"allowResizeY": false,
											"showCharsCounter": false,
											"showWordsCounter": false,
											"showXPathInStatusbar": false,
											"height": 600,
											"width":900,
											"buttons": "bold,italic,underline,strikethrough,ol,ul,font,fontsize,paragraph,lineHeight,superscript,subscript,image,hr,table,link,indent,outdent,left,center,right,brush,source"
										});
									</script>';

			#$strHtml .= ' <td><textarea name="content" cols="80" class="form-control-sm" rows="32">'.htmlspecialchars($content).'</textarea></td>';

			$strHtml .= ' 	<td valign="middle"><input name="iPilihC" value="&lt;&lt;" onclick="insertHeader(\'C\')" title="Insert" type="button" class="btn btn-sm btn-primary"></td>
									<td valign="top">' . selectSyntax("dPilihC", $group, 0) . '</td>
								</tr>';
			$strHtml .=
				'
				</table>			
				</td>
			</tr>';
		}

		// Begin Submit Button For Operation
		$strHtml .=
			'
			<tr>
				<td>&nbsp;<br>';
		if ($page == 'add') {
			$strHtml .= '<input name="Submit" type="submit" value="Simpan" class="btn btn-primary" />&nbsp;';
		} else if ($page == 'edit') {
			$strHtml .= '<input name="Submit" type="submit" value="Kemaskini" class="btn btn-primary" />&nbsp;';
		}
		// if ($page == 'edit') {
		// 	$strHtml .= '<input name="Submit" type="submit" value="Duplicate" class="btn btn-primary" onclick="return ConfirmDuplicate();" />&nbsp;';
		// }
		if ($page <> 'add') {
			$strHtml .= '<input name="Submit" type="submit" value="Hapus" class="btn btn-danger" onclick="return ConfirmDelete();" />';
		}
		$strHtml .=
			'
				</td>
			</tr>';
		// End Submit Button
	} else {

		$sSQL = 'SELECT * FROM letters ORDER BY groupID, sortNum';

		$GetData = $conn->Execute($sSQL);

		$strNameList = array('Tambah');
		$strPageList = array('add');

		//if (get_session("Cookie_groupID") == 2) {
		$strHtml .=
			'
			<tr valign="top" class="textFont">
				<td>'
			. PageSelection($page, $strNameList, $strPageList)
			. '<hr size="1px">
				</td>
			</tr>';
		//}
		if ($GetData->RowCount() <> 0) {
			$i = 1;
			$j = -1;
			$strHtml .=
				'
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="1" class="table table-sm table-striped">';
			while (!$GetData->EOF) {
				$getVal  = array();
				$sSQL = "SELECT ID,sortNum  FROM `letters` where groupID = " . $GetData->fields('groupID') . " order by sortNum";
				///print '<br>'.$sSQL;
				$rs = &$conn->Execute($sSQL);
				if ($rs->RowCount() <> 0) {
					while (!$rs->EOF) {
						array_push($getVal, $rs->fields(ID));
						$rs->MoveNext();
					}
				}
				$getVal = implode(":", $getVal);

				if (intval($GetData->fields('groupID')) <> $j) {
					$j = intval($GetData->fields('groupID'));
					if ($GetData->fields('groupID') <> 2) $getVal = $GetData->fields('ID');
					$strHtml .=
						'
					<tr class="table-primary">
						<td width="15"><img src="images/images.jpg" height="11" width="15"><!--input name="group_id[]" type="checkbox"-->&nbsp;</td>
						<td colspan="2"><a href="">'
						. LinkTo('<font class="text-success"><u><b>' . strtoupper($strTypeNameList[$j]) . '</u></b></font>', PageLink($currPage, 'list', $page_id, $rec_per_page, $sort, $GetData->fields('groupID'), $getVal, $id, $keyword, $by, $dept, $status, $month, $year))
						. ' </a>
						</td>
					<tr>';
				}

				$strHtml .=
					'
				<tr>
					<td>&nbsp;</td>
					<td width="10"><!--input name="code_id[]" type="checkbox" class="form-check-input" value="' . $group . '"-->&nbsp;</td>
					<td nowrap="nowrap">'
					. LinkTo($GetData->fields('codeName') . '&nbsp;-&nbsp;' . $GetData->fields('title'), PageLink($currPage, 'list', $page_id, $rec_per_page, $sort, $GetData->fields('groupID'), $GetData->fields('ID'), $id, $keyword, $by, $dept, $status, $month, $year))
					. ' - '
					. LinkTo('lihat', PageLink($currPage, 'view', $page_id, $rec_per_page, $sort, $GetData->fields('groupID'), $GetData->fields('ID'), $id, $keyword, $by, $dept, $status, $month, $year));
				//if (get_session("Cookie_groupID") == 2) {
				$strHtml .=
					'
					,&nbsp;
					'
					. LinkTo('ubah', PageLink($currPage, 'edit', $page_id, $rec_per_page, $sort, $GetData->fields('groupID'), $GetData->fields('ID'), $id, $keyword, $by, $dept, $status, $month, $year));
				//}
				$strHtml .=
					'
					</td></tr>
					';
				$GetData->MoveNext();

				if (intval($GetData->fields('groupID')) <> $j) {
					$strHtml .= '
					<tr><td colspan="3"><hr size=1></td></tr>
					';
				}
				$i++;
			}
			$strHtml .=
				'</table>
				</td>
			</tr>';
		} else {
			$strHtml .= '
			<tr><td>Tiada</td></tr>
			';
		}
	}
} else if ($page == 'list') {
	//form - member list by group ready to select for print 
	$strTypeNameList	= array();
	$strTypeValueList	= array();
	$strStatusNameList	= array();
	$strStatusValueList	= array();
	$strRecordCountList	= array();
	$strSQLSortList		= array();
	GenerateVarList();
	$strDeptNameList	= array();
	$strDeptValueList	= array();
	array_push($strDeptNameList, '- Semua -');
	array_push($strDeptValueList, '');
	$sSQL = 'SELECT a.departmentID, b.code as deptCode, b.name as deptName'
		. ' FROM userdetails a, general b'
		. ' WHERE a.departmentID = b.ID'
		. ' AND a.status IN (1,4)'
		. ' GROUP BY a.departmentID';
	$GetDeptData = &$conn->Execute($sSQL);
	if ($GetDeptData->RowCount() <> 0) {
		while (!$GetDeptData->EOF) {
			array_push($strDeptNameList, strtoupper($GetDeptData->fields('deptName')));
			array_push($strDeptValueList, $GetDeptData->fields('departmentID'));
			$GetDeptData->MoveNext();
		}
	}

	$strHtml .= '
	<tr valign="top" class="textFont">
		<td>
			<input name="type" type="hidden" />
			<table cellpadding="0" cellspacing="6">';

	$strHtml .=	$strErrMsg . '
				<tr>
					<td align="right" width="150">Carian melalui&nbsp;</td>
					<td>'
		. SelectForm('by', $by, $strTypeNameList, $strTypeValueList, '') . '&nbsp;
					<input name="keyword" type="text" value="" class="form-control-sm" maxlength="50" size="30">&nbsp;
					<input name="submit" class="btn btn-sm btn-secondary" type="submit" value="Cari">
					</td>
				</tr>
				<tr>
					<td align="right">Cabang/Zona&nbsp;</td>
					<td>' . SelectForm('dept', $dept, $strDeptNameList, $strDeptValueList, 'onchange="PageRefresh();"') . '</td>
				</tr>';

	//if ($group <> 2) {
	$strHtml .=	'
				<tr>
					<td align="right">Status&nbsp;</td>
					<td>' . SelectForm('status', $status, $strStatusNameList, $strStatusValueList, 'onchange="PageRefresh();"') . '</td>
				</tr>';
	//}	

	//if ($group == 2) {
	/*$strHtml .=
				'<tr>'
					.'<td align="right"><b>Bulan/Tahun</b></td>'
					.'<td>'
					.SelectForm('month', $month, $strMonthNameList, $strMonthValueList, 'onchange="PageRefresh();"').'&nbsp;/&nbsp;'
					.'<input name="year" type="text" size="5" maxlength="5" value="'.$year.'">'
					.'</td>'
				.'</tr>'; */
	//}

	$strHtml .= '
				<tr>
					<td align="right">&nbsp;</td>
					<td><input name="Submit" class="btn btn-sm btn-secondary" type="submit" value="Cetak Surat" onClick="return SendData(\'surat\');">&nbsp;
					<input name="Submit" class="btn btn-sm btn-secondary" type="submit" value="Hantar Email" onClick="return SendData(\'email\');"></td>
				</tr>';

	$strHtml .=
		'
			</table>
		</td>
	</tr>';

	$GetData = $conn->Execute(GenerateSQLList());

	$strHtml .= '
		<tr><td><hr size="1px"></td></tr>
		';

	if ($GetData->RowCount() <> 0) {
		$strHtml .= '
			<tr valign="top" class="textFont">
			<td>
				<table width="100%">
				<tr>
				<td><input type="checkbox" class="form-check-input" name="head" checked="checked">&nbsp;With Letterhead&nbsp;</td>
				<td align="right">Paparan&nbsp;'
			. SelectForm('rec_per_page', $rec_per_page, $strRecordCountList, $strRecordCountList, 'onchange="PageRefresh();"')
			. '&nbsp;setiap mukasurat.&nbsp;'
			//.SelectForm('sort', $sort, $strSQLSortList, $strSQLSortList, 'onchange="PageRefresh();"')
			. '</td>
				</tr>
				</table>
			</td>
			</tr>
			';
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

		$strHtml .= '
	    <tr valign="top" >
			<td valign="top">';
		$strHtml .= BeginDataField(true);
		while (!$GetData->EOF and $nCount < $rec_end) {
			$nCount += 1;
			$strHtml .= ContentDataField(true);
			$GetData->MoveNext();
		}
		$strHtml .=	EndDataField()
			. '
			</td>
		</tr>';

		$strPageTemp = '';

		/*if ($page_id > 1) {
			$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, 1, $rec_per_page, $rec_per_page,
				$sort, $group, $code, $id, $keyword, $by, $dept, $status, $month, $year);
			$strPageTemp .= '<a href="'.$strActionPage.'"><<</a>';
			$strPageTemp .= '&nbsp;';
			$strActionPage = PageLink($_SERVER['PHP_SELF'], $page, ($page_id - 1), $rec_per_page,
				$sort, $group, $code, $id, $keyword, $by, $dept, $status, $month, $year);
			$strPageTemp .= '<a href="'.$strActionPage.'">Prev</a>';
			$strPageTemp .= '&nbsp;&nbsp;';
		}*/
		if ($page_id > 1) {
			$strPageTemp .= '&nbsp;&nbsp;';
			$strActionPage = PageLink(
				$currPage,
				$page,
				1,
				$rec_per_page,
				$sort,
				$group,
				$code,
				$id,
				$keyword,
				$by,
				$dept,
				$status,
				$month,
				$year
			);
			$strPageTemp .= '<a href="' . $strActionPage . '"> << </a>';
			$strPageTemp .= '&nbsp;';
			$strActionPage = PageLink(
				$currPage,
				$page,
				($page_id - 1),
				$rec_per_page,
				$sort,
				$group,
				$code,
				$id,
				$keyword,
				$by,
				$dept,
				$status,
				$month,
				$year
			);
			$strPageTemp .= '<a href="' . $strActionPage . '"> Prev </a>';
		}

		for ($i = $page_start; $i <= $page_end; $i++) {
			$strActionPage = PageLink($currPage, $page, $i, $rec_per_page, $sort, $group, $code, $id, $keyword, $by, $dept, $status, $month, $year);

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
			$strActionPage = PageLink(
				$currPage,
				$page,
				($page_id + 1),
				$rec_per_page,
				$sort,
				$group,
				$code,
				$id,
				$keyword,
				$by,
				$dept,
				$status,
				$month,
				$year
			);
			$strPageTemp .= '<a href="' . $strActionPage . '">Next</a>';
			$strPageTemp .= '&nbsp;';
			$strActionPage = PageLink(
				$currPage,
				$page,
				$page_total,
				$rec_per_page,
				$sort,
				$group,
				$code,
				$id,
				$keyword,
				$by,
				$dept,
				$status,
				$month,
				$year
			);
			$strPageTemp .= '<a href="' . $strActionPage . '">>></a>';
		}

		$strHtml .= '
		<tr>
			<td align="left">' . $strPageTemp . '</td>
		</tr>';

		$strHtml .= '
		<tr>
			<td class="textFont">Jumlah Data : <font class="redText">' . $GetData->RowCount() . '</font></td>
		</tr>
		';
	} else {
		$strHtml .=
			'
		<tr valign="top">
			<td valign="top">'
			. BeginDataField(false)
			. ContentDataField(false)
			. EndDataField()
			. '</td>
		</tr>
		';
	}
}
//end display letter content and member printed list
$strHtml .=
	'
	</table>
</form>
</div>
';

print $strHtml;
//include("footer.php");
print '</div>
</body>
</html>
';

$strHtml = '
<script language="JavaScript">

function ConfirmSearch() {'
	. 'var frm = document.MyForm;'
	. '} '
	. '

function ConfirmDuplicate() { '
	. 'if (confirm(\'Anda Pasti Template Surat Ini Ingin Di"Duplicate"kan?\')) {'
	. 'return true;'
	. '} else {'
	. 'return false;'
	. '}'
	. '}'
	. '

function ConfirmDelete() { '
	. 'if (confirm(\'Anda Pasti Template Surat Ini Ingin Dihapuskan?\')) {'
	. 'return true;'
	. '} else {'
	. 'return false;'
	. '}'
	. '}

	function insertHeader(type) {
    
	if(type==\'C\'){
	var myQuery = document.MyForm.content;
    var myListBox = document.MyForm.dPilihC;
    var myGetBox = document.MyForm.iPilihC;
	}
	
	if(myListBox.options.length > 0) {
        var chaineAj = "";
        var NbSelect = 0;
        for(var i=0; i<myListBox.options.length; i++) {
            if (myListBox.options[i].selected){
                NbSelect++;
                if (NbSelect > 1)
                    chaineAj += ", ";
                chaineAj += myListBox.options[i].value;
            }
        }

        //IE support
        if (document.selection) {
            myQuery.focus();
            sel = document.selection.createRange();
            sel.text = chaineAj;
            myGetBox.focus();
        }
        //MOZILLA/NETSCAPE support
        else if (myQuery.selectionStart || myQuery.selectionStart == "0") {
            var startPos = myQuery.selectionStart;
            var endPos = myQuery.selectionEnd;
            var chaineSql = myQuery.value;

            myQuery.value = chaineSql.substring(0, startPos) + chaineAj + chaineSql.substring(endPos, chaineSql.length);
        } else {
            myQuery.value += chaineAj;
        }
    }

	editor.selection.insertHTML(chaineAj);

}';

if ($page == 'list') {
	$strActionPage =
		'"' . $currPage . "&" . $_SERVER['QUERY_STRING']
		. '&page=' . $page
		. '&page_id=1';
	if ($rec_total <> 0) {
		$strActionPage .=
			'&rec_per_page=" + frm.rec_per_page.options[frm.rec_per_page.selectedIndex].value + "';
		//.'&sort=" + frm.sort.options[frm.sort.selectedIndex].value + "';
	} else {
		$strActionPage .=
			'&rec_per_page=' . $rec_per_page . ''
			. '&sort=' . $sort . '';
	}

	$strActionPage .=
		'&group=' . $group
		. '&code=' . $code
		. '&dept=" + frm.dept.options[frm.dept.selectedIndex].value + "'
		. '&status=" + frm.status.options[frm.status.selectedIndex].value';

	/*if ($group == 1 OR $group == 2) {
		$strActionPage .= '&month=" + frm.month.options[frm.month.selectedIndex].value + "';
	} else {
		$strActionPage .= '&month='.$month.'';
	} 

	$strActionPage .= '&year='.$year.'"';*/

	$strHtml .=
		'
	
	function PageRefresh() {
		var frm = document.MyForm;
		document.location = ' . $strActionPage . ';
	}';

	$strHtml .=
		/*'var allChecked=false;
	function SelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all" && e[c].name!="head") {
	        e[c].checked = allChecked;
	      }
	    }
	}'*/

		'var allChecked=false;
	
	function SelectAll(strValue) {
		
		var frm = document.MyForm.elements;
		var a = strValue;
		allChecked = !allChecked;
		for (i = 0; i < frm.length; i++) {
			if (frm[i].name== a && frm[i].name!="head")	{
				frm[i].checked = allChecked;
			}
		}
	} '
		. '
	
	function SelectCurrent() {'
		. 'frm = document.MyForm.elements;'
		. 'for (i = 0; i < frm.length; i++) {'
		. 'if (frm[i].name=="checkbox_id[]" && frm[i].checked) {'
		. 'if (frm[i].checked) {'
		. 'frm[i].value = 1;'
		. '} else if (!frm[i].checked) {'
		. 'frm[i].value = 0;'
		. '}'
		. '}'
		. '}'
		. '}'
		. '
	
	function SendData(v) {'
		. 'frm = document.MyForm;'
		. 'if (frm==null) {'
		. 'alert(\'Silakan pastikan nama form dibuat/tersedia.!\');'
		. 'return false;'
		. '} else {'
		. 'count=0;'
		. 'for (c=0; c<frm.elements.length; c++) {'
		. 'if (frm.elements[c].name=="user_id[]" && frm.elements[c].checked) {'
		. 'count++;'
		. '}'
		. 'if (frm.elements[c].name=="head") {'
		. 'if (frm.elements[c].checked) {'
		. 'frm.head.value = 1;'
		. '} else {'
		. 'frm.head.value = 0;'
		. '}'
		. '}'
		. '}'
		. 'if (count==0) {'
		. 'alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');'
		. 'return false;'
		. '} else {'
		. 'if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {'
		. 'frm.type.value = v;'
		. 'return true;'
		. '} else {'
		. 'return false;'
		. '}'
		. '}'
		. '}'
		. '}';
}

$strHtml .=
	'
</script>
';

print $strHtml;


//--------------------------------------------------------------------
function GenerateVarList()
{
	global $group;
	global $statusList;
	global $statusVal;
	global $biayaList;
	global $biayaVal;
	global $strTypeNameList;
	global $strTypeValueList;
	global $strStatusNameList;
	global $strStatusValueList;
	global $strRecordCountList;
	global $strSQLSortList;

	$strStatusNameList[] = '- Semua -';
	$strStatusValueList[] = '';
	switch ($group) {
		case 0:
		case 4:
			$strTypeNameList[] = 'Nomor Anggota';
			$strTypeNameList[] = 'Nama Anggota';
			$strTypeNameList[] = 'No KTP Baru';
			$strTypeValueList[]	= 1;
			$strTypeValueList[]	= 2;
			$strTypeValueList[]	= 3;
			$strStatusNameList[] = $statusList[0];
			$strStatusValueList[] = $statusVal[0];
			$strStatusNameList[] = $statusList[1];
			$strStatusValueList[] = $statusVal[1];
			$strStatusNameList[] = $statusList[2];
			$strStatusValueList[] = $statusVal[2];
			break;
		case 1:
		case 2:
			$strTypeNameList[] = 'Nomor Anggota';
			$strTypeNameList[] = 'Nama Anggota';
			$strTypeNameList[] = 'No KTP Baru';
			$strTypeValueList[]	= 1;
			$strTypeValueList[]	= 2;
			$strTypeValueList[]	= 3;
			$strStatusNameList[] = $biayaList[0];
			$strStatusValueList[] = $biayaVal[0];
			$strStatusNameList[] = $biayaList[1];
			$strStatusValueList[] = $biayaVal[1];
			$strStatusNameList[] = $biayaList[2];
			$strStatusValueList[] = $biayaVal[2];
			$strStatusNameList[] = $biayaList[3];
			$strStatusValueList[] = $biayaVal[3];
			break;
		case 6:
			$strTypeNameList[] = 'Nomor Anggota';
			$strTypeNameList[] = 'Nama Anggota';
			$strTypeNameList[] = 'No KTP Baru';
			$strTypeValueList[]	= 1;
			$strTypeValueList[]	= 2;
			$strTypeValueList[]	= 3;
			$strStatusNameList[] = $biayaList[0];
			$strStatusValueList[] = $biayaVal[0];
			$strStatusNameList[] = $biayaList[1];
			$strStatusValueList[] = $biayaVal[1];
			$strStatusNameList[] = $biayaList[2];
			$strStatusValueList[] = $biayaVal[2];
			$strStatusNameList[] = $biayaList[3];
			$strStatusValueList[] = $biayaVal[3];
			break;
		case 7:
			$strTypeNameList[] = 'Nomor Anggota';
			$strTypeNameList[] = 'Nama Anggota';
			$strTypeNameList[] = 'No KTP Baru';
			$strTypeValueList[]	= 1;
			$strTypeValueList[]	= 2;
			$strTypeValueList[]	= 3;
			$strStatusNameList[] = $biayaList[0];
			$strStatusValueList[] = $biayaVal[0];
			$strStatusNameList[] = $biayaList[1];
			$strStatusValueList[] = $biayaVal[1];
			$strStatusNameList[] = $biayaList[2];
			$strStatusValueList[] = $biayaVal[2];
			$strStatusNameList[] = $biayaList[3];
			$strStatusValueList[] = $biayaVal[3];
			break;
		default:
			$strTypeNameList[] = 'Nomor Anggota';
			$strTypeNameList[] = 'Nama Anggota';
			$strTypeNameList[] = 'No KTP Baru';
			$strTypeValueList[]	= 1;
			$strTypeValueList[]	= 2;
			$strTypeValueList[]	= 3;
			$strStatusNameList[] = $statusList[0];
			$strStatusValueList[] = $statusVal[0];
			$strStatusNameList[] = $statusList[1];
			$strStatusValueList[] = $statusVal[1];
			$strStatusNameList[] = $statusList[2];
			$strStatusValueList[] = $statusVal[2];
			$strStatusNameList[] = $statusList[3];
			$strStatusValueList[] = $statusVal[3];
			$strStatusNameList[] = $statusList[4];
			$strStatusValueList[] = $statusVal[4];
			$strStatusNameList[] = $statusList[5];
			$strStatusValueList[] = $statusVal[5];
			break;
	}

	$strRecordCountList[] = 5;
	$strRecordCountList[] = 10;
	$strRecordCountList[] = 20;
	$strRecordCountList[] = 30;
	$strRecordCountList[] = 40;
	$strRecordCountList[] = 50;
	$strRecordCountList[] = 100;
	$strSQLSortList[] = 'DESC';
	$strSQLSortList[] = 'ASC';
}

//get data form database
function GenerateSQLList()
{
	global $group;
	global $keyword;
	global $by;
	global $dept;
	global $status;
	global $month;
	global $year;
	global $sort;
	global $strTypeNameList;
	global $strLoanTypeList;

	switch ($group) {
		case 0: // Anggota
		case 1:

		case 4:
			$sSQL_ =
				'SELECT a.userID, a.name, b.memberID, b.newIC, b.departmentID, b.approvedDate, b.status, a.applyDate FROM users a, userdetails b'
				. ' WHERE a.userID = b.userID';
			if ($keyword <> '') {
				if ($by == '1') {
					$sSQL_ .= ' AND b.memberID like \'%' . $keyword . '%\'';
				} else if ($by == '2') {
					$sSQL_ .= ' AND a.name like \'%' . $keyword . '%\'';
				} else if ($by == '3') {
					$sSQL_ .= ' AND b.newIC like \'%' . $keyword . '%\'';
				}
			}
			if ($dept <> '') {
				$sSQL_ .= ' AND b.departmentID = ' . $dept;
			}
			if ($status <> '') {
				$sSQL_ .= ' AND b.status = ' . $status;
			}

			$sSQL_ .= ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ) desc';
			break;

		case 2:
			$sSQL_ =
				'SELECT a.*, a.status as loanstatus, b.*, c.* FROM loans a, users b, userdetails c'
				. ' WHERE a.userID = b.userID'
				. ' AND c.userID = b.userID ';

			if ($keyword <> '') {
				if ($by == '1') {
					$sSQL_ .= ' AND a.userID like \'%' . $keyword . '%\'';
				} else if ($by == '2') {
					$sSQL_ .= ' AND b.name like \'%' . $keyword . '%\'';
				} else if ($by == '3') {
					$sSQL_ .= ' AND c.newIC like \'%' . $keyword . '%\'';
				}
			}

			if ($dept <> '') {
				$sSQL_ .= ' AND c.departmentID = ' . $dept;
			}

			//list out just approved
			if ($status <> '') {
				$sSQL_ .= ' AND a.status = ' . $status;
			}

			$sSQL_ .= ' ORDER BY a.applyDate ';
			break;
		case 3:
			$sSQL_ =
				'SELECT a.userID, a.name, b.memberID, b.newIC, b.departmentID, b.approvedDate, b.status, a.applyDate FROM users a, userdetails b'
				. ' WHERE a.userID = b.userID';
			if ($keyword <> '') {
				if ($by == '1') {
					$sSQL_ .= ' AND b.memberID like \'%' . $keyword . '%\'';
				} else if ($by == '2') {
					$sSQL_ .= ' AND a.name like \'%' . $keyword . '%\'';
				} else if ($by == '3') {
					$sSQL_ .= ' AND b.newIC like \'%' . $keyword . '%\'';
				}
			}
			if ($dept <> '') {
				$sSQL_ .= ' AND b.departmentID = ' . $dept;
			}
			if ($status <> '') {
				$sSQL_ .= ' AND b.status = ' . $status;
			}

			$sSQL_ .= ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ) desc';
			break;

		case 5:
			$sSQL_ =
				'SELECT a.userID, a.name, b.memberID, b.newIC, b.departmentID, b.approvedDate, b.status, a.applyDate FROM users a, userdetails b'
				. ' WHERE a.userID = b.userID AND b.statusHL = 1';
			if ($keyword <> '') {
				if ($by == '1') {
					$sSQL_ .= ' AND b.memberID like \'%' . $keyword . '%\'';
				} else if ($by == '2') {
					$sSQL_ .= ' AND a.name like \'%' . $keyword . '%\'';
				} else if ($by == '3') {
					$sSQL_ .= ' AND b.newIC like \'%' . $keyword . '%\'';
				}
			}
			if ($dept <> '') {
				$sSQL_ .= ' AND b.departmentID = ' . $dept;
			}
			if ($status <> '') {
				$sSQL_ .= ' AND b.status = ' . $status;
			}

			$sSQL_ .= ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ) desc';
			break;
		case 7:
			$sSQL_ =
				'SELECT a.*, a.status as loanstatus, b.*, c.* FROM loans a, users b, userdetails c'
				. ' WHERE a.userID = b.userID'
				. ' AND c.userID = b.userID ';

			if ($keyword <> '') {
				if ($by == '1') {
					$sSQL_ .= ' AND a.userID like \'%' . $keyword . '%\'';
				} else if ($by == '2') {
					$sSQL_ .= ' AND b.name like \'%' . $keyword . '%\'';
				} else if ($by == '3') {
					$sSQL_ .= ' AND c.newIC like \'%' . $keyword . '%\'';
				}
			}

			if ($dept <> '') {
				$sSQL_ .= ' AND c.departmentID = ' . $dept;
			}

			//list out just approved
			if ($status <> '') {
				$sSQL_ .= ' AND a.status = ' . $status;
			}

			$sSQL_ .= ' ORDER BY a.applyDate ';
			break;
		case 6:
			$sSQL_ = 'SELECT * FROM insurankenderaan WHERE status = 1 ORDER BY CAST( ID AS SIGNED INTEGER ) desc';

			break;
		default:
			$sSQL_ =
				'SELECT a.userID, a.name, b.memberID, b.newIC, b.departmentID, b.approvedDate, b.status, a.applyDate FROM users a, userdetails b'
				. ' WHERE a.userID = b.userID';
			if ($keyword <> '') {
				if ($by == '1') {
					$sSQL_ .= ' AND b.memberID like \'%' . $keyword . '%\'';
				} else if ($by == '2') {
					$sSQL_ .= ' AND a.name like \'%' . $keyword . '%\'';
				} else if ($by == '3') {
					$sSQL_ .= ' AND b.newIC like \'%' . $keyword . '%\'';
				}
			}
			if ($dept <> '') {
				$sSQL_ .= ' AND b.departmentID = ' . $dept;
			}
			if ($status <> '') {
				$sSQL_ .= ' AND b.status = ' . $status;
			}

			$sSQL_ .= ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ) desc';
			break;
	}

	return $sSQL_;
}

//open table form
function BeginDataField($bIsDataAvalaible_)
{
	global $group;

	if ($bIsDataAvalaible_) {
		$strCheckboxTemp_ = '<input name="all" type="checkbox" class="form-check-input" onclick="SelectAll(\'user_id[]\')" style="padding:0px;margin:0px" />';
	} else {
		$strCheckboxTemp_ = '&nbsp;';
	}
	switch ($group) {
		case 0:
		case 1:
		case 4:
			$strNameList_ = array($strCheckboxTemp_, '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Tarikh Mohon</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center');
			break;
		case 2:
			$strNameList_ = array($strCheckboxTemp_, '<b>NomborRujukan/Pembiayaan</b>', '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center');
			break;
		case 3:
			$strNameList_ = array($strCheckboxTemp_, '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Tarikh Mohon</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center');
			break;
		case 5:
			$strNameList_ = array($strCheckboxTemp_, '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Tarikh Mohon</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center');
			break;
		case 7:
			$strNameList_ = array($strCheckboxTemp_, '<b>NomborRujukan/Pembiayaan</b>', '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center');
			break;
		case 6:
			$strNameList_ = array($strCheckboxTemp_, '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Tarikh Tamat Insuran</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center', 'center');
			break;
		default:
			$strNameList_ = array($strCheckboxTemp_, '<b>Nomor Anggota/Nama</b>', '<b>Kartu Identitas</b>', '<b>Tarikh Mohon</b>', '<b>Status</b>');
			$strWidthList_ = array('15', '', '', '', '');
			$strAlignList_ = array('center', 'left', 'center', 'center', 'center');
			break;
	}

	$strHtml_ = '
	<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table table-sm table-striped">
		<tr class="table-primary">';

	for ($i = 0; $i < count($strNameList_); $i++) {
		$strHtml_ .= '
		<td nowrap="nowrap" align="' . $strAlignList_[$i] . '">' . $strNameList_[$i] . '</td>
		';
	}

	$strHtml_ .= '
		</tr>';

	return $strHtml_;
}

//close table form
function EndDataField()
{
	$strHtml_ = '
	</table>';

	return $strHtml_;
}

function ContentDataField($bIsDataAvalaible_)
{
	global $group;
	global $GetData;
	global $statusList;
	global $biayaList;

	if ($bIsDataAvalaible_) {
		switch ($group) {
			case 0:
			case 1:
			case 4:
				$strPageTemp_ = $GetData->fields('memberID') . '&nbsp;-&nbsp;' . strtoupper($GetData->fields('name'));

				$strDataList_ = array(
					$GetData->fields('userID'),
					$strPageTemp_,
					convertNewIC($GetData->fields('newIC')),
					toDate("d/m/Y", $GetData->fields('applyDate')),

					$statusList[$GetData->fields('status')]
				);
				$strAlignList_ = array('right', 'left', 'center', 'center', 'center');
				break;
			case 2:

				$strLoanPageTemp_ = $GetData->fields('loanNo')
					. ' - '
					. strtoupper(dlookup("general", "name", "ID=" . tosql($GetData->fields('loanType'), "Number")));

				$strNameTemp_ = dlookup("userdetails", "memberID", "userID=" . tosql($GetData->fields('userID'), "Text"))
					. ' - '
					. strtoupper(dlookup("users", "name", "userID=" . tosql($GetData->fields('userID'), "Text")));

				$strDataList_ = array(
					$GetData->fields('loanID'),
					$strLoanPageTemp_,
					$strNameTemp_,
					convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($GetData->fields('userID'), "Text"))),

					$biayaList[$GetData->fields('loanstatus')]
				);
				$strAlignList_ = array('right', 'left', 'left', 'left');
				break;
			case 3:
				$strPageTemp_ = $GetData->fields('memberID') . '&nbsp;-&nbsp;' . strtoupper($GetData->fields('name'));

				$strDataList_ = array(
					$GetData->fields('userID'),
					$strPageTemp_,
					convertNewIC($GetData->fields('newIC')),
					toDate("d/m/Y", $GetData->fields('applyDate')),


					$statusList[$GetData->fields('status')]
				);
				$strAlignList_ = array('right', 'left', 'center', 'center', 'center');
				break;
			case 7:

				$strLoanPageTemp_ = $GetData->fields('loanNo')
					. ' - '
					. strtoupper(dlookup("general", "name", "ID=" . tosql($GetData->fields('loanType'), "Number")));

				$strNameTemp_ = dlookup("userdetails", "memberID", "userID=" . tosql($GetData->fields('userID'), "Text"))
					. ' - '
					. strtoupper(dlookup("users", "name", "userID=" . tosql($GetData->fields('userID'), "Text")));

				$strDataList_ = array(
					$GetData->fields('loanID'),
					$strLoanPageTemp_,
					$strNameTemp_,
					convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($GetData->fields('userID'), "Text"))),

					$biayaList[$GetData->fields('loanstatus')]
				);
				$strAlignList_ = array('right', 'left', 'left', 'left');
				break;

			case 6:
				$strPageTemp_ = $GetData->fields('NoAnggota') . '&nbsp;-&nbsp;' . strtoupper($GetData->fields('Nama'));

				$strDataList_ = array(
					$GetData->fields('NoAnggota'),
					$strPageTemp_,
					convertNewIC($GetData->fields('NoKP')),
					toDate("d/m/Y", $GetData->fields('TarikhTamatInsuran')),


					$statusList[$GetData->fields('status')]
				);
				$strAlignList_ = array('right', 'left', 'center', 'center', 'left', 'center');
				break;



			default:
				$strPageTemp_ = $GetData->fields('memberID') . '&nbsp;-&nbsp;' . strtoupper($GetData->fields('name'));

				$strDataList_ = array(
					$GetData->fields('userID'),
					$strPageTemp_,
					convertNewIC($GetData->fields('newIC')),
					toDate("d/m/Y", $GetData->fields('applyDate')),
					$statusList[$GetData->fields('status')]
				);
				$strAlignList_ = array('right', 'left', 'center', 'center', 'left');
				break;
		}
	} else {
		switch ($group) {

			case 0:
			case 1:
			case 4:
				$strDataList_ = array('&nbsp;', '- Tiada Rekod -', '-', '-', '-');
				$strAlignList_ = array('center', 'center', 'center', 'center', 'center');
				break;
			case 2:
				$strDataList_ = array('&nbsp;', '- Tiada Rekod -', '-', '-', '-');
				$strAlignList_ = array('center', 'center', 'center', 'center', 'center');
				break;
			case 3:
				$strDataList_ = array('&nbsp;', '- Tiada Rekod -', '-', '-', '-');
				$strAlignList_ = array('center', 'center', 'center', 'center', 'center');
				break;
			case 7:
				$strDataList_ = array('&nbsp;', '- Tiada Rekod -', '-', '-', '-');
				$strAlignList_ = array('center', 'center', 'center', 'center', 'center');
				break;
			default:
				$strDataList_ = array('&nbsp;', '- Tiada Rekod -', '-', '-', '-');
				$strAlignList_ = array('center', 'center', 'center', 'center', 'center');
				break;
		}
	}

	$strHtml_ = '
	<tr>
		';

	for ($i = 0; $i < count($strDataList_); $i++) {

		if ($i == 0 and $bIsDataAvalaible_) {
			$strHtml_ .= '
		<td class="Data" align="center" valign="top" nowrap="nowrap">
		<input name="user_id[]" type="checkbox" class="form-check-input" style="padding:0px;margin:0px" value="' . $strDataList_[$i] . '" />
		';
		} else {
			$strHtml_ .= '
		<td class="Data" align="' . $strAlignList_[$i] . '" valign="top" nowrap="nowrap">' . $strDataList_[$i];
		}

		$strHtml_ .= '
		</td>
		';
	}

	$strHtml_ .= '
	</tr>
	';

	return $strHtml_;
}

//return - form page and html get value- form type page = add, edit, view
function PageSelection($page_, $strNameList_, $strPageList_)
{
	global $group;
	global $code;
	$currPage = "?vw=memberLetter&mn=917";

	$strHtml_ .=
		'
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
		<td align="left">&nbsp;</td>
		<td align="right"><button type="button" class="btn btn-sm btn-dark text-light">
		';

	for ($i = 0; $i < count($strNameList_); $i++) {
		if ($i <> 0) {
			$strHtml_ .= '
			&nbsp; | &nbsp;
			';
		}

		if ($page_ == $strPageList_[$i]) {
			$strHtml_ .= '
			' . $strNameList_[$i] . '
			';
		} else {
			$strHtml_ .= '
			<a href= "' . $currPage . '&page=' . $strPageList_[$i] . '&group=' . $group . '&code=' . $code . '"> ' . $strNameList_[$i] . ' </a> ';
		}
	}

	$strHtml_ .=
		'
			&nbsp;</button></td>
			</tr>
		</table>';

	return $strHtml_;
}

//return - form page and html get value- form type page = link
function PageLink($strPath_, $page_, $page_id_, $rec_per_page_, $sort_, $group_, $code_, $id_, $keyword_, $by_, $dept_, $status, $month_, $year_)
{
	$strHtml_ =
		$strPath_
		. '&page=' . $page_;

	if ($page_ == 'edit' or $page_ == 'view') {
		$strHtml_ .=
			'&group=' . $group_
			. '&code=' . $code_;
	}

	if ($page_ == 'list') {
		$strHtml_ .=
			'&page_id=' . $page_id_
			. '&rec_per_page=' . $rec_per_page_
			. '&sort=' . $sort_
			. '&group=' . $group_
			. '&code=' . $code_
			. '&keyword=' . $keyword_
			. '&by=' . $by_
			. '&dept=' . $dept_
			. '&status=' . $status_
			. '&month=' . $month_
			. '&year=' . $year_;
	}

	return $strHtml_;
}

//html combo box
function SelectForm($strName_, $strValue_, $strNameList_, $strValueList_, $strEtc_)
{
	$strHtml_ = '<select class="form-select-sm" name="' . $strName_ . '"';
	if ($strEtc_ <> '') {
		$strHtml_ .= ' ' . $strEtc_;
	}
	$strHtml_ .= '>';

	for ($i = 0; $i < count($strNameList_); $i++) {
		$strHtml_ .= '<option value="' . $strValueList_[$i] . '"';
		if ($strValue_ == $strValueList_[$i]) {
			$strHtml_ .= ' selected="selected"';
		}
		$strHtml_ .= '>' . $strNameList_[$i] . '</option>';
	}
	$strHtml_ .= '</select>';

	return $strHtml_;
}

//get html anchor
function LinkTo($strTitle_, $strPath_)
{
	$strHtml_ = '<a href="' . $strPath_ . '">' . $strTitle_ . '</a>';
	return $strHtml_;
}

//replace newline to zero
function CheckLineBreak($strData_)
{
	$strHtml_ = $strData_;

	$strHtml_ = nl2br($strHtml_);

	$strSyntaxLibList = array();
	$strDataLibList = array();

	$strSyntaxLibList[] = '><br>';
	$strDataLibList[] = '>';
	$strSyntaxLibList[] = '><br/>';
	$strDataLibList[] = '>';
	$strSyntaxLibList[] = '><br />';
	$strDataLibList[] = '>';

	for ($i = 0; $i < count($strSyntaxLibList); $i++) {
		$strHtml_ = str_replace($strSyntaxLibList[$i], $strDataLibList[$i], $strHtml_);
	}

	return $strHtml_;
}
