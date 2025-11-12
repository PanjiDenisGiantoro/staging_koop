<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	komoditi_list.php
 *          Date 		: 	15/04/2017
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "ALL";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$sFileName = '?vw=komoditi_list&mn=944';
$sFileRef  = '?vw=komoditi_edit&mn=944';
$title     = "Daftar Sertifikat Komoditas";
//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sSQL = '';
		$sWhere = "komoditi_ID=" . tosql($pk[$i], "Number");

		$no_sijil = dlookup("komoditi", "no_sijil", $sWhere);

		if ($userID) {
			$sSQL = "DELETE FROM komoditi WHERE " . $sWhere;
			$rs = &$conn->Execute($sSQL);

			$strActivity = $_POST['Submit'] . ' Sijil Komoditi Dihapuskan - ' . $no_sijil;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
		}
	}
}

//--- End   : deletion based on checked box -------------------------------------------------------
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " a.loanID = b.loanID AND a.no_sijil LIKE '%" . $q . "%'";
	}
	if ($by == 2) {
		$sWhere .= " a.loanID = b.loanID AND a.userID LIKE '%" . $q . "%'";
	}
}
$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "";
$sSQL = "SELECT a.*,b.* FROM komoditi a, loans b";
if ($q <> "") {
	$sSQL = $sSQL . $sWhere . ' ORDER BY a.tarikh_beli DESC';
} else {
	$sSQL = $sSQL . ' WHERE a.loanID = b.loanID ORDER BY b.loanNo DESC';
}
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<h5 class="card-title">' . strtoupper($title) . '</h5>
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr valign="top" class="Header"><td align="left" >
	Cari Berdasarkan 
<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nombor Sijil</option>';
else print '<option value="1">Nombor Sijil</option>';
if ($by == 2)	print '<option value="2" selected>Nomor Anggota</option>';
else print '<option value="2">Nomor Anggota</option>';
print '</select>
	<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm">
 	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;';
print '</select>&nbsp;</td></tr><tr valign="top"><td align="left">';
print '</select>';
print '<input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-secondary" value="Cetak" onClick="ITRActionButtonDoc();">&nbsp;';
print '</td></tr><tr valign="top" class="textFont"><td>
<table width="100%">
<tr>
	<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>					
	<td align="right" class="textFont">Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
if ($pg == 5)	print '<option value="5" selected>5</option>';
else print '<option value="5">5</option>';
if ($pg == 10)	print '<option value="10" selected>10</option>';
else print '<option value="10">10</option>';
if ($pg == 50)	print '<option value="50" selected>50</option>';
else print '<option value="50">50</option>';
if ($pg == 100)	print '<option value="100" selected>100</option>';
else print '<option value="100">100</option>';
print '</select> setiap halaman..</td></tr></table></td></tr>';
if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	<tr valign="top" >
	<td valign="top">
	<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm">
	<tr class="table-primary">
	<td nowrap>&nbsp;</td>
	<td nowrap>Nombor Sijil</td>
	<td nowrap align="center">Nomor Anggota</td>
	<td nowrap align="left">Nama Anggota</td>
	<td nowrap align="center">Nomor Rujukan</td>
	<td nowrap align="right">Jumlah Pembelian Komoditi (RP)</td>
	<td nowrap align="center">&nbsp;Sijil Komoditi</td>
	<td nowrap align="center">Tarikh Pembelian Komoditi</td>
</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		print '<tr>
	<td class="Data" align="right">' . $bil . '&nbsp;</td>
	<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(komoditi_ID)) . '">
	<a href="' . $sFileRef . '&pk=' . tohtml($GetMember->fields(komoditi_ID)) . '">
	' . $GetMember->fields(no_sijil) . '</a> 
	<td class="Data" align="center">' . $GetMember->fields(userID) . '</td>
	<td class="Data">' . dlookup("users", "name", "userID=" . tosql($GetMember->fields('userID'), "Number")) . '</td>
	<td class="Data" align="center" >' . $GetMember->fields(loanNo) . '</td>

	<td class="Data" align="right">&nbsp;' . $GetMember->fields(amount) . '</td>';

		if ($GetMember->fields(sijil_komoditi) == NULL) {
			print '<td class="Data">&nbsp;</td>';
		} else {
			print '<td class="Data" align="center"><input type="button" class="btn btn-sm btn-outline-danger" value="Dokumen" onClick=window.open(\'upload_sijilkomoditi/' . $GetMember->fields(sijil_komoditi) . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");><br/>
	<input type="button" class="btn btn-sm btn-secondary" name="GetPicture" value="Muat Naik Semula"  onclick= "Javascript:window.location.href=\'?vw=uploadwinkomoditip&pk=' . $GetMember->fields(komoditi_ID) . '\';">';
		}

		print '
	<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields(tarikh_beli)) . '</td>
	</tr>';
		$cnt++;
		$bil++;
		$GetMember->MoveNext();
	}
	$GetMember->Close();
	print '</table></td></tr><tr><td>';
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
		print '</td></tr></table>';
	}
	print '</td></tr>
<tr><td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td></tr>';
} else {
	if ($q == "") {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print '</table></td></tr></table></div></form>';
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
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
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
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
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
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="memberStatus.php?pk=" + strStatus;
			  }
	        }
	      }
	    }
	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
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
				window.open(\'komoditiPrint.php?action=print&id=\' + pk,\'top=50,left=50,width=850,height=550,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');
			}
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
</script>';
