<?php

/*********************************************************************************
 *		   Project		:	iKOOP.com.my
 *		   Filename		:	insuranListNotActive.php
 *		   Date			:	01/01/2005
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
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

$sFileName = "?vw=insuranList&mn=$mn";
$sFileRef  = "?vw=insuranEdit&mn=$mn";
$sFileRefRenew  = "?vw=insuranRenew&mn=$mn";
$title	   = "Senarai Tamat Tempoh Insuran Kenderaan";

//$conn->debug=1;
//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if ($action	== "delete") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckIns = ctInsuran("", $pk[$i]);
		if ($CheckIns->RowCount() == 1) {
			$sWhere	= "ID="	. tosql($pk[$i], "Number");
			$sSQL =	"DELETE	FROM insuranKenderaan WHERE " . $sWhere;
			$rs	= &$conn->Execute($sSQL);
			//print $sSQL;		
		}
	}
}
//--- End	: deletion based on	checked	box	-------------------------------------------------------

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

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " NoAnggota like '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " Nama like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " NoKP like '%" . $q . "%'";
	} else if ($by == 4) {
		$sWhere .= " NoKenderaan like '%" . $q . "%'";
	}
}
$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "";
$sSQL = "SELECT * ,DATEDIFF(TarikhTamatInsuran,CURDATE()) as days FROM 	insuranKenderaan  ";
if ($q <> "") {
	$sSQL = $sSQL . $sWhere . ' and status=\'1\' AND DATEDIFF(TarikhTamatInsuran,CURDATE()) < 60 ORDER BY applyDate DESC';
} else {
	$sSQL = $sSQL . ' where status=\'1\' AND DATEDIFF(TarikhTamatInsuran,CURDATE()) < 60 ORDER BY applyDate DESC';
}
$GetListIns = &$conn->Execute($sSQL);
$GetListIns->Move($StartRec - 1);




$TotalRec =	$GetListIns->RowCount();
$TotalPage =  ($TotalRec / $pg);


if ($_POST['Submit'] == 'Hantar Email') {
	$id = '';
	for ($i = 0; $i < count($user_id); $i++) {
		if ($i <> 0) {
			$id .= ':';
		}
		$id .= $user_id[$i];
	}
}



print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
 <div clas="row">
 Carian Melalui
                    <select	name="by" class="form-select-sm">';
if ($by == 1) print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2) print '<option value="2" selected>Nama</option>';
else print '<option value="2">Nama</option>';
if ($by == 3) print '<option value="3" selected>Kad Pengenalan</option>';
else print '<option value="3">Kad Pengenalan</option>';
if ($by == 4) print '<option value="4" selected>Nombor Kenderaan</option>';
else print '<option value="4">Nombor Kenderaan</option>';

print '	</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-controlx form-control-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
 </div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr valign="top" class="Header">
		<td align="left" >';
/*
echo ' 
			Carian melalui
			<select	name="by" class="Data">';
		if ($by	== 1)	print '<option value="1" selected>Nombor Anggota</option>';		else print '<option	value="1">Nombor Anggota</option>';
		if ($by	== 2)	print '<option value="2" selected>Nama</option>';	else print '<option	value="2">Nama</option>';
		if ($by	== 3)	print '<option value="3" selected>No Kad Pengenalan</option>';		else print '<option	value="3">No Kad Pengenalan</option>';
		if ($by	== 4)	print '<option value="4" selected>No Kenderaan</option>';		else print '<option	value="4">No Kenderaan</option>';
		
	print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="20" class="Data">
			<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;
			<!--Jabatan
			<select	name="dept"	class="Data" onchange="document.MyForm.submit();">
				<option	value="">- Semua -';
			for	($i	= 0; $i	< count($deptList);	$i++) {
				print '	<option	value="'.$deptVal[$i].'" ';
				if ($dept == $deptVal[$i]) print ' selected';
				print '>'.$deptList[$i];
			}
	print '	</select>-->'; */
print '</td>
	</tr>
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;';

if (($IDName == 'superadmin') or ($IDName == 'admin')) {

	if ($filter < 1) print 'Tandakan rekod untuk hapus <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">
					';
}
if ($filter == 3) print '&nbsp;&nbsp;Cetak dokumen proses :&nbsp;<input type="button" class="but" value="Cetak" onClick="ITRActionButtonDoc();">&nbsp;';

if ($filter	== 4) print 'Ubah ke proses kembali &nbsp;<input type="button" class="but" value="Ubah"	onClick="ITRActionButtonUbah();">';

print '</td>
					<td	align="right" class="textFont">

					<!--input 4ype="button" class="but" value="Status" onClick="ITBActionButtonStatus();"-->';
echo papar_ms($pg);
print '</td>
				</tr>';
if (get_session("Cookie_groupID") == 2 && $filter == 3) {
	print '<tr>
			<td	 class="textFont" align ="left">Batal Kelulusan :&nbsp;<input type="button" class="but" value="Batal" onClick="ITRActionButtonClick(\'batal\');">&nbsp;Sebab:&nbsp;<input type="text" name="sebab" value="" maxlength="60" size="50" class="Data"></td>
			</tr>';
}
print '	</table>
		</td>
	</tr>';
if ($GetListIns->RowCount() <>	0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr	valign="top" >
			<td	valign="top">
				<table border="0" cellspacing="1" cellpadding="2" class="table table-sm table-striped">
					<tr class="table-primary">
						<td	nowrap>&nbsp;</td>	
						<td	nowrap>&nbsp;</td>	
						<td	nowrap><b>Nombor Rujukan</b></td>
						<td	nowrap align="center"><b>Tahun Perlindungan</b></td>
						<td	nowrap align="center"><b>Nombor Anggota</b></td>
						<td	nowrap><b>Nama</b></td>
						<td	nowrap align="center"><b>Nombor Kenderaan</b></td>
						  <td	nowrap align="right"><b>Jumlah Premium (RM)</b></td>
						  <td	nowrap align="right"><b>Jumlah Premium Kasar (RM)</b></td>
						  <td	nowrap align="right"><b>Jumlah Premium Bersih (RM)</b></td>
				    <td	nowrap align="right"><b>Jumlah Insuran (RM)</b></td>			
						<td	nowrap align="center"><b>Tarikh Mula Insuran</b></td>
						<td	nowrap align="center"><b>Tarikh Tamat Insuran</b></td>						
						<td	nowrap align="center"><b>Tarikh Mohon</b></td>
					</tr>';
	$amtLoan = 0;
	while (!$GetListIns->EOF && $cnt <= $pg) {
		$insuranID = tohtml($GetListIns->fields(ID));
		$yearcover = tohtml($GetListIns->fields(insuranYear));
		$noruj = tohtml($GetListIns->fields(insuranNo));
		$anggota = tohtml($GetListIns->fields(NoAnggota));
		$nama = tohtml($GetListIns->fields(Nama));
		$nokp = tohtml($GetListIns->fields(NoKP));
		$JumPremium = tohtml($GetListIns->fields(JumlahPremium));
		$JumPreKasar = tohtml($GetListIns->fields(Jum_Pre_Kasar));
		$JumPreBersih = tohtml($GetListIns->fields(Jum_Pre_Bersih));
		//$Cover_Note = tohtml($GetListIns->fields(Cover_Note ));
		$JumInsuran = tohtml($GetListIns->fields(JumlahPerlindungan));
		$nokenderaan = tohtml($GetListIns->fields(NoKenderaan));
		$status = tohtml($GetListIns->fields(Status));
		$days = tohtml($GetListIns->fields(days));
		$dateStartIns = toDate("d/m/yy", $GetListIns->fields(Tkh_Mula));
		$dateEndIns = toDate("d/m/yy", $GetListIns->fields(TarikhTamatInsuran));
		$dateApply = toDate("d/m/yy", $GetListIns->fields(applyDate));
		print '	<tr>
						<td	class="Data" align="right">' . $bil	. '&nbsp;</td>	
						<td	class="Data" align="right"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetListIns->fields(NoAnggota)) . '"></td>	
						<td	class="Data"><a href="' . $sFileRef . '&pk=' . $insuranID . '">' . $noruj . '</a></td>';
		print '
						<td	class="Data" align="center">' . $yearcover . '&nbsp;</td>
						<td	class="Data" align="center">' . $anggota . '&nbsp;</td>	
						<td	class="Data" align="left">' . $nama . '&nbsp;</td>	
						<td	class="Data" align="center">' . $nokenderaan . '</td>
						<td	class="Data" align="right">' . $JumPremium . '</td>
						<td	class="Data" align="right">' . $JumPreKasar . '</td>
						<td	class="Data" align="right">' . $JumPreBersih . '</td>
						<td	class="Data" align="right">' . $JumInsuran . '</td>
						<td	class="Data" align="center">' . $dateStartIns . '</td>
						<td	class="Data" align="center">' . $dateEndIns . '</td>
						<td	class="Data" align="center">' . $dateApply . '</td>
						</tr>';
		$cnt++;
		$bil++;
		$GetListIns->MoveNext();
	}
	$GetListIns->Close();
	print '		
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
		print '<tr><td class="textFont"	valign="top" align="left">Rekod	Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			if (is_int($i / 10)) print	'<br />';
			print '<A href="' . $sFileName . '&StartRec=' . (($i	* $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&filter=' . $filter . '">';
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
			<td	class="textFont">Jumlah	Rekod :	<b>' . $GetListIns->RowCount()	. '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr	size=1"></td></tr>';
	} else {
		print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Carian	rekod "' . $q . '" tidak jumpa	-</b><hr size=1"></td></tr>';
	}
}
print '
</table>
</form></div>';

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
			  alert(\'Sila pilih rekod yang hendak di\'	+ v	+\'kan.\');
			} else {
			  if(confirm(count + \'	rekod hendak di\' +	v +\'kan. Adakah anda pasti?\')) {
				e.action.value = v;
				e.submit();
			  }
			}
		  }
		}


function SendData(v) {'
	. 'frm = document.MyForm;'
	. 'if (frm==null) {'
	. 'alert(\'Sila pastikan nama form diwujudkan.!\');'
	. 'return false;'
	. '} else {'
	. 'count=0;'
	. 'for (c=0; c<frm.elements.length; c++) {'
	. 'if (frm.elements[c].name=="pk[]" && frm.elements[c].checked) {'
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
	. 'alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');'
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
	. '}



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
		document.location =	"' . $sFileName	. '&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter=' . $filter . '";
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

</script>';
