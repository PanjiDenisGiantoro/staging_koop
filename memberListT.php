<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberListT.php
 *          Date 		: 	10/12/2003
 *********************************************************************************/
if (!isset($StartRec))		$StartRec = 1;
if (!isset($pg))			$pg = 50;
if (!isset($code))			$code = 'A';
if (!isset($q))				$q = '';
if (!isset($by))			$by = '0';
if (!isset($filter))		$filter = '0';
if (!isset($dept))			$dept = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}
$code = strtoupper($code);
if (!(in_array($code, $suratVal))) {
	print '	<script>
				alert ("' . $code . ' - Code is not exist...!");
				window.location = "index.php";
			</script>';
}

$title     = 'Senarai Anggota Berhenti Bersara';

//--- Begin : Surat & Email
if ($action == "surat" or $action == "email") {
	for ($i = 0; $i < count($pk); $i++) {
		$pkID[$i] = $pk[$i];
	}
	$pkID = implode(":", $pkID);
	print '	<script>window.open("letter.php?code=' . $code . '&pk=' . $pkID . '&action=' . $action . '&letterheadcode=' . $letterheadcode . '","pop","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");</script>';
}
//--- End   : Surat & Email

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

$GetMemberT = ctMemberTerminateStatus($q, $by, $filter, $dept);
$GetMemberT->Move($StartRec - 1);

$TotalRec = $GetMemberT->RowCount();
$TotalPage =  ($TotalRec / $pg);

?>
<div class="maroon" align="left"><a class="maroon" href="index.php">LAMAN UTAMA</a><b>&nbsp;>&nbsp;<a class="maroon" href="mails.php">SURAT/EMAIL</a>&nbsp;>&nbsp;<? print strtoupper($title); ?></b></div>
<div style="width: 100%; text-align:left">
	<div>&nbsp;</div>
	<form name="MyForm" action=<?= $_SERVER['PHP_SELF'] ?> method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="letterheadcode">
		<input type="hidden" name="code" value="<?= $code ?>">
		<input type="hidden" name="filter" value="<?= $filter ?>">
		<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="6">
						<tr>
							<td align="right" width="150"><b>Carian melalui</b></td>
							<td>
								<select name="by">
									<?
									if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
									else print '<option value="1">Nombor Anggota</option>';
									if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
									else print '<option value="2">Nama Anggota</option>';
									if ($by == 3)	print '<option value="3" selected>No KP Baru</option>';
									else print '<option value="3">No KP Baru</option>';
									?>
								</select>
								<input type="text" name="q" value="<?= $q ?>" maxlength="50" size="30">
								<input type="submit" value="Cari">
							</td>
						</tr>
						<tr>
							<td align="right"><b>Jabatan/Cawangan</b></td>
							<td>
								<select name="dept" onchange="document.MyForm.submit();">
									<option value="">- Semua -</option>
									<?
									for ($i = 0; $i < count($deptList); $i++) {
										print '	<option value="' . $deptVal[$i] . '" ';
										if ($dept == $deptVal[$i]) print ' selected';
										print '>' . $deptList[$i] . '</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right"><b>Status</b></td>
							<td>
								<select name="filter" onchange="document.MyForm.submit();">
									<?
									for ($i = 0; $i < 3; $i++) {
										print '	<option value="' . $statusVal[$i] . '" ';
										if ($filter == $statusVal[$i]) print ' selected';
										print '>' . $statusList[$i] . '</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<!--tr>
					<td>&nbsp;</td>
					<td>
						<? if ($filter == 0) { ?>
						<input type="button" class="but" value="Hapus" onClick="ITRActionButtonClick('delete');">            
						<? } ?>
						<input type="button" class="but" value="Status" onClick="ITRActionButtonStatus();">
					</td>
				</tr-->
					</table>
					<hr size="1px">
					<table cellpadding="0" cellspacing="6">
						<tr>
							<td align="right" width="150"><b>Surat/Email</b></td>
							<td>
								<select name="code">
									<?
									if ($filter == 0) {
										for ($i = 2; $i < 3; $i++) {
											print '	<option value="' . $suratVal[$i] . '" ';
											if ($code == $suratVal[$i]) print ' selected';
											print '>' . $suratList[$i] . '</option>';
										}
									} else if ($filter == 1) {
										for ($i = 2; $i < 3; $i++) {
											print '	<option value="' . $suratVal[$i] . '" ';
											if ($code == $suratVal[$i]) print ' selected';
											print '>' . $suratList[$i] . '</option>';
										}
									} else if ($filter == 2) {
										for ($i = 2; $i < 3; $i++) {
											print '	<option value="' . $suratVal[$i] . '" ';
											if ($code == $suratVal[$i]) print ' selected';
											print '>' . $suratList[$i] . '</option>';
										}
									}
									?>
								</select>
								<input type="button" value="Cetak Surat" onClick="ITRActionButtonClick('surat');">
								<input type="button" value="Hantar Email" onClick="ITRActionButtonClick('email');">
							</td>
						</tr>
						<? if ($filter == 0) { ?>
							<!--tr>
					<td>&nbsp;</td>
					<td>
						<input type="button" value="Semak" onClick="ITRActionButtonClick('semak');">
					</td>
				</tr-->
						<? } ?>
					</table>
					<? if ($GetMemberT->RowCount() <> 0) { ?>
						<hr size="1px"> <? } ?>
				</td>
			</tr>
			<?
			if ($GetMemberT->RowCount() <> 0) {
				$bil = $StartRec;
				$cnt = 1;
				$temp =
					'<tr valign="top" class="textFont">'
					. '<td>'
					. '<table width="100%">'
					. '<tr>'
					. '<td  class="textFont">'
					. '<input type="checkbox" class="form-check-input" onClick="ITRViewSelectAll()">&nbsp;Select All&nbsp;'
					. '<input type="checkbox" class="form-check-input" name="letterhead" checked="checked">&nbsp;With Letterhead&nbsp;'
					. '</td>'
					. '<td align="right" class="textFont">'
					. 'Paparan&nbsp;<SELECT name="pg" onchange="doListAll();">';
				print $temp;
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

				$temp =				'</select>&nbsp;setiap mukasurat.'
					. '</td>'
					. '</tr>'
					. '</table>'
					. '</td>'
					. '</tr>'
					. '<tr valign="top" >'
					. '<td valign="top">'
					. '<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">'
					. '<tr class="header">'
					. '<td nowrap>&nbsp;</td>'
					. '<td nowrap>&nbsp;Nombor Anggota/Nama Anggota&nbsp;</td>'
					. '<td nowrap align="center">&nbsp;Nombor KP Baru&nbsp;</td>'
					. '<td nowrap align="center">&nbsp;Jabatan/Cawangan&nbsp;</td>'
					. '<td nowrap align="center">&nbsp;Status&nbsp;</td>'
					. '<td nowrap align="center">&nbsp;Tarikh Memohon&nbsp;</td>';
				if ($filter == 1) {
					$temp .=	'<td nowrap align="center">Tarikh Kelulusan&nbsp;</td>';
				}
				$temp .=	'</tr>';

				print $temp;

				while (!$GetMemberT->EOF && $cnt <= $pg) {
					$status = $GetMemberT->fields(status);
					$colorStatus = "Data";
					if ($status == 1) $colorStatus = "greenText";
					if ($status == 2) $colorStatus = "redText";
					$temp = '<tr>'
						. '<td class="Data" align="right">&nbsp;' . $bil . '&nbsp;</td>'
						. '<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMemberT->fields(userID)) . '">'
						. '<a href="memberEdit.php?pk=' . tohtml($GetMemberT->fields(userID)) . '">' . $GetMemberT->fields(memberID) . '&nbsp;-&nbsp;' . strtoupper($GetMemberT->fields(name)) . '</a></td>'
						. '<td class="Data" align="center">&nbsp;' . convertNewIC($GetMemberT->fields(newIC)) . '</td>'
						. '<td class="Data" align="center">&nbsp;' . strtoupper(dlookup("general", "name", "ID=" . tosql($GetMemberT->fields('departmentID'), "Number"))) . '&nbsp;</td>'
						. '<td class="Data" align="center">&nbsp;<font class="' . $colorStatus . '">' . $statusList[$status] . '</font></td>'
						. '<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetMemberT->fields(applyDate)) . '&nbsp;</td>';
					if ($filter == 1) {
						$temp .= '<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetMember->fields(approvedDate)) . '&nbsp;</td>';
					}
					$temp .= '</tr>';
					print $temp;
					$cnt++;
					$bil++;
					$GetMemberT->MoveNext();
				}
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
					print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
					for ($i = 1; $i <= $numPage; $i++) {
						print
							'<a href="' . $_SERVER['PHP_SELF'] . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg
							. '&by=' . $by . '&q=' . $q . '&filter=' . $filter . '&code=' . $code . '">';
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
			<td class="textFont">Jumlah Rekod : <b>' . $GetMemberT->RowCount() . '</b></td>
		</tr>';
			} else {
				if ($q == "") {
					print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
				} else {
					print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
				}
			}
			?>
		</table>
	</form>
	<?
	include("footer.php");
	print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
		e = document.MyForm.elements;
		allChecked = !allChecked;
		for(c=0; c< e.length; c++) {
			if(e[c].type=="checkbox" && e[c].name!="all" && e[c].name!="letterhead") {
				e[c].checked = allChecked;
			}
	    }
	}
	
	function ITRActionButtonClick(v) {
		e = document.MyForm;
		if (e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {

			count=0;

			for (c=0; c<e.elements.length; c++) {
				if (e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
				}
				if (e.elements[c].name=="letterhead") {
					if (e.elements[c].checked) {
						e.letterheadcode.value = 1;
					} else {
						e.letterheadcode.value = 0;
					}
				}
			}
	        
			if (count==0) {
				alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
			} else {
				if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
					e.action.value = v;
					e.submit();
				}
			}
		}
	}	   

	function ITRActionButtonStatus() {
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
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.location.href = "memberStatusT.php?pk=" + pk;
			}
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $_SERVER['PHP_SELF'] . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';

	?>