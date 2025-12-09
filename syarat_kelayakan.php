<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	syarat)kelayakan.php
 *          Date 		: 	22/11/2023
 *********************************************************************************/
//include("header.php");
require_once("common.php");
include("koperasiQry.php");

date_default_timezone_set("Asia/Jakarta");
setlocale(LC_TIME, 'ms_MY');

//$sFileName		= "?vw=syarat_kelayakan&mn=$mn";

// if ($_SERVER['QUERY_STRING'] == '' OR ($screen <> 'view' AND $screen <> 'edit')) {
// 	if (get_session('Cookie_groupID') == '') {
//                                     $strRedirect = 'login&error='.$error;
// 	} else {
// 		$strRedirect = 'main';
// 	} 
//         print '<script>window.location="'.$_SERVER['PHP_SELF'].'?screen='.$strRedirect.'";</script>';
// 	exit;
// } else {  
// 	if (get_session('Cookie_groupID') <> '' AND $screen <> 'view' AND $screen <> 'edit') {
// 		$strRedirect = 'main'; 
// 		print '<script>window.location="'.$_SERVER['PHP_SELF'].'?screen='.$strRedirect.'";</script>';
// 		exit;
// 	} else if (get_session('Cookie_koperasiID') <> 0 AND get_session('Cookie_groupID') <> '' ) {
// 		$strRedirect = 'login&error='.$error; 
//                                     print '<script>window.location="?vw=main&screen='.$strRedirect.'";</script>';
// 		exit;
// 	}
// }
if ($screen == 'edit') {
	if ($id <> 999) {
		$strTitles = array('Laman Utama', 'Kemaskini Buletin');
	} else {
		$strTitles = array('Laman Utama', 'Kemaskini Syarat Kelayakan Anggota');
	}
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else if ($screen == 'view') {
	if ($id <> 999) {
		$strTitles = array('Laman Utama', 'Kandungan Buletin');
	} else {
		$strTitles = array('Laman Utama', 'Syarat Kelayakan Anggota');
	}
	$strPages = array($_SERVER['PHP_SELF'], '');
	$align = 'left';
} else {
	$strTitles = array('');
	$strPages = array('');
	$align = 'center';
}
$strTemp .=
	'<div><h5 class="card-title">Kemaskini Syarat Kelayakan Anggota</h5></div>' .
	'<div style="width: 100%; text-align:left">';


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

	if ($screen == 'view' or $screen == 'edit') {

		if (!isset($topic))		$topic = '';
		if (!isset($content))	$content = '';

		if ($id == '') {
			$sSQL_ = 'SELECT MAX(ID) as num FROM syarat';
			$GetData = &$conn->Execute($sSQL_);

			$id = $GetData->fields('num');
		}
		if (isset($_POST['Submit'])) {
			$bPass = false;


			if ($_POST['Submit'] == 'Kemaskini' and $id <> '' and $id === '999') {

				$updatedDate = date("Y-m-d H:i:s");
				$sSQL = "";
				$sWhere = "";
				$sWhere = " ID= '" . $id . "' ";
				$sWhere = " WHERE (" . $sWhere . ") ";
				$sSQL =
					"UPDATE syarat SET "
					. " tajuk = '" . $topic . "' ,"
					. " kandungan = '" . CheckQuotes($content) . "' ,"
					. " postedDate =  '" . $updatedDate . "' ,"
					. " postedBy = '" . get_session('Cookie_fullName') . "' ";

				$sSQL = $sSQL . $sWhere;
				$conn->Execute($sSQL);

				$bPass = true;
				$screen = 'view';
			}

			if ($bPass) {
				$strActivity = $_POST['Submit'] . ' Syarat Kelayakan Anggota';
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 9);
				print '<script>window.location="' . $_SERVER['PHP_SELF'] . '?vw=syarat_kelayakan&screen=' . $screen . '&id=' . $id . '";</script>';
				exit;
			}
		}

		if ($screen == 'view' or $screen == 'edit') {
			if ($id <> 999) {
				$sSQL = 'SELECT * from syarat WHERE ID = ' . $id;
			} else {
				$sSQL = 'SELECT * from syarat WHERE ID = ' . $id;
			}
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

		$strActionPage = PageLink($_SERVER['PHP_SELF'], $screen, $page_id, $rec_per_page, $sort, $id);

		$strTemp .=
			'<form id="content-form" name="MyForm" action="' . $strActionPage . '&vw=syarat_kelayakan" method="post">'
			. '<table width="100%" cellping="3" cellspacing="0">';

		if ($screen == 'edit' or $screen == 'view' and $id == '999') {
			$strNameList = array('Ubah', 'Papar');
			$strPageList = array('edit', 'view');
		}
		if ($screen == 'view') {
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
				. PageSelection($screen, $strTopicText, $strNameList, $strPageList)
				. '</table>';
		} else {
			$strTemp .= $strTopicText;
		}

		$strTemp .=
			''
			. 'Oleh : ' . $strPostedBy
			. '</td>'
			. '</tr>';

		if ($screen == 'view') {
			$strTemp .= '<tr><td><p>' . $strContent . '</p></td></tr>';
			if (get_session('Cookie_groupID') <> 0 and $id <> 999) {
				$strTemp .= '<tr><td><input type="submit" class="btn btn-sm btn-danger" name="Submit" value="Hapus" onclick="return ConfirmDelete();"></td></tr>';
			}
		} else {
			#$strTemp .= '<tr><td><textarea name="content" class="form-control"  rows="20">'.$strContent.'</textarea></td></tr>';

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
			$strTemp .= '<input type="submit" class="btn btn-sm btn-primary" name="Submit" value="Kemaskini">';


			if (get_session('Cookie_groupID') <> 0 and $id <> 999) {
				$strTemp .= '&nbsp;<input type="submit" class="btn btn-sm btn-danger" name="Submit" value="Hapus" onclick="return ConfirmDelete();">';
			}

			$strTemp .= '</td></tr>';
		}
		$strTemp .=
			'</table>'
			. '</form>';
	}
}

print $strTemp;

include("footer.php");

function PageSelection($page_, $strExtra_, $strNameList_, $strPageList_)
{
	global $id;



	$strTemp_ .=
		'<td align="right" colspan=2>'
		. '<div class="btn-group btn-group-sm mt-2 mb-2" role="group" aria-label="Basic example">';

	for ($i = 0; $i < count($strNameList_); $i++) {

		if ($page_ == $strPageList_[$i]) {
			$strTemp_ .= '<button type="button" class="btn btn-dark"><font class="redText">' . $strNameList_[$i] . '</font></button>';
		} else {
			$strTemp_ .= '<button type="button" class="btn btn-dark"><a href="' . $_SERVER['PHP_SELF'] . '?vw=syarat_kelayakan&mn=901&screen=' . $strPageList_[$i];
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
	$strTemp_ = $strPath_ . '?&mn=901&screen=' . $page_;
	// if ($page_ == 'main' OR $page_ == 'list') {
	// 	$strTemp_ .= '&page_id='.$page_id_.'&rec_per_page='.$rec_per_page_.'&sort='.$sort_;
	// }
	if ($page_ == 'view' or $page_ == 'edit') {
		$strTemp_ .= '&id=' . $id_;
	}

	return $strTemp_;
}
