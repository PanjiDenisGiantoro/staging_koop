<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberProfil.php
 *          Date 		: 	26/03/2006
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "1";
if (!isset($dept))		$dept = "";
if (!isset($active))	$active = "1";
if (!isset($yr)) $yr	= date("Y");
if ($filter == 1)	$active = "1";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$IDName = get_session("Cookie_userName");

$sSQL2 = "	SELECT *
			FROM users WHERE loginID = '" . $IDName . "'";
$rs2 = &$conn->Execute($sSQL2);

$IDGroup = $rs2->fields(groupID);

$sFileName = 'memberProfilListHL.php';
$sFileRef  = 'accountHL2.php';
$title     = "Profil Anggota Hutang Macet";



//--- Begin : reset based on checked box -------------------------------------------------------
if ($action == "reset") {
	$sSQL = '';
	$sWhere = "";
	$sWhere = ' userID = ' . tosql($pk[0], "Text");
	$sSQL	= ' UPDATE users SET ' .
		' password=' . tosql(strtoupper(md5("koperasi123")), "Text");
	$sSQL .= ' WHERE ' . $sWhere;
	$rs = &$conn->Execute($sSQL);
	print '<script>alert("Kata sandi anggota ini telah direset menjadi \"koperasi123\"\nSilakan informasikan kepada anggota tersebut agar mengganti kata sandinya.");</script>';
}
//--- End   : reser based on checked box -------------------------------------------------------

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


$sSQL = "";
$sWhere = " a.userID = b.userID AND statusHL = 1 ";
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

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
$sSQL = "SELECT	DISTINCT a.*, b.*
		 FROM 	users a, userdetails b";
$sSQL = $sSQL . $sWhere;
$sSQL = $sSQL . "order by CAST( b.memberID AS SIGNED INTEGER ) desc";
$GetMember = &$conn->Execute($sSQL);
//$GetMember = ctMemberStatusDeptA($q,$by,$filter,$dept,$active);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print
	'<form name="MyForm" action=' . $sFileName . ' method="post">'
	. '<input type="hidden" name="action">'
	. '<input type="hidden" name="pk" value="<?=$pk?>">'
	. '<input type="hidden" name="filter" value="' . $filter . '">'
	. '<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">'
	. '<tr>'
	. '<td><b class="maroonText">' . strtoupper($title) . '</b></td>'
	. '</tr>';

print
	'<tr valign="top" class="Header">'
	. '<td align="left" >'
	. 'Cari Berdasarkan'
	. '<select name="by" class="Data">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>No KTP Baru</option>';
else print '<option value="3">No KTP Baru</option>';
print
	'</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="Data">
 			<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;		

		</td>
	</tr>
	<tr valign="top">
</td>
	</tr>
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>';
print
	'<td class="textFont">&nbsp;</td><td align="right" class="textFont">';
print
	'&nbsp;&nbsp;Tampil
					<SELECT name="pg" class="Data" onchange="doListAll();">';
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
print
	'</select>&nbsp;&nbsp;setiap halaman.
					</td>
				</tr>
			</table>
		</td>
	</tr>';

if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print
		'<tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap>&nbsp;</td>
						<td nowrap>&nbsp;No anggota/Nama</td>
						<td nowrap align ="right">&nbsp;Yuran (RP)</td>
						<td nowrap align="right">Bayaran Bulanan (RP)</td>
						<!--td nowrap>&nbsp;Jabatan/Cawangan</td-->
						<td nowrap align="right">&nbsp;Sisa Utang(RP)</td>
						<td nowrap align="center">&nbsp;Tanggal Keanggotaan</td>
					</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		$totalFees = number_format(getFees($GetMember->fields(userID), $yr), 2);
		$status = dlookup("userdetails", "status", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$colorStatus = "Data";
		$colorStatus = "redText";
		print '<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data">';
		//if($filter == 3) {
		print '<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(userID)) . '">';
		//}
		print '	<a href="' . $sFileRef . '?userID=' . tohtml($GetMember->fields(userID)) . '">
							' . $GetMember->fields(memberID) . '- ' . $GetMember->fields(name) . '</a></td>
											
						<td class="Data" align ="right">&nbsp;' . $totalFees . '</td>
						<td class="Data" align ="right">&nbsp;' . $GetMember->fields('monthFee') . '</td>	
						<!--td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetMember->fields('departmentID'), "Number")) . '</td-->
						<td class="Data" align ="right">&nbsp;<font class="' . $colorStatus . '">' . number_format($GetMember->fields(DebtBalance), 2) . '</font></td>
						<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetMember->fields(approvedDate)) . '</td>
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
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '&filter=' . $filter . '">';
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
	if ($q == '') {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size="1"></td></tr>';
	} else {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size="1"></td></tr>';
	}
}
print ' 
</table></td></tr></table>
</form>';

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
			alert(\'Pastikan nama formulir sudah dibuat.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' data yang ingin di \' + v + \'?\')) {
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
			alert(\'Pastikan nama formulir sudah dibuat.!\');
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
	          alert(\'Silakan pilih data yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' data yang ingin di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="memberAktif.php?pk=" + strStatus;
			  }
	        }
	      }
	    }


	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Pastikan nama formulir sudah dibuat.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Pilih hanya satu data untuk update status\');
			} else {
				window.location.href = "memberAktif.php?pk=" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

    function ITRActionButtonReset() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Pastikan nama formulir sudah dibuat.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk reset kata sandi\');
			} else {
	          if(confirm(\' Kata sandi untuk akun ini akan direset?\')) {
	            e.action.value = \'reset\';
				e.dept.value = "' . $dept . '";
				e.by.value = "' . $by . '";
				e.q.value = "' . $q . '";
	            e.submit();
	          }
			}
		}
	}


</script>';
