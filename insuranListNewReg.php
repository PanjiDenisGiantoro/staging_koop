<?php

/*********************************************************************************
 *		   Project		:	iKOOP.com.my
 *		   Filename		:	insuranListNewReg
 *		   Date			:	05/05/2016
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
$IDUserLog = get_session("Cookie_userID");

if ($pgview == "viewsah") {
	$sFileName = "?vw=insuranListNewReg&pgview=viewsah&mn=$mn";
} else {
	$sFileName = "?vw=insuranListNewReg&mn=$mn";
}
$sActionFileName = "?vw=insuranListNewReg&mn=$mn&pgview=viewsah";
$sFileRef  = "?vw=insuranEdit&mn=$mn";
$sFileRefRenew  = "?vw=insuranRenew&mn=$mn";
$title	   = "Senarai Permohonan Pengesahan Insuran Kenderaan";
$ID = $_REQUEST['ID'];
$code = $_REQUEST['code'];
$edit = $_POST['edit'];
if ($edit) {
	$action = "edit";
}

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
elseif ($action	== "sah") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckIns = ctInsuran("", $pk[$i]);
		$disahkan = $IDUserLog;
		$disahkanDate = date("Y-m-d H:i:s");
		if ($CheckIns->RowCount() == 1) {
			$sWhere	= "ID="	. tosql($pk[$i], "Number");
			$sSQL =	"Update	insuranKenderaan set " .
				" status=1 " .
				", isApprovedBy='" . $disahkan . "'" .
				", isApprovedDate='" . $disahkanDate . "'" .
				" WHERE " . $sWhere;
			$rs	= &$conn->Execute($sSQL);
			//print $sSQL;		
		}
	}
	print '<script>
					alert ("Rekod Insuran kenderaan telah direkodkan sah dah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
} else if ($action == "edit") {
	$disediakanDate = date("Y-m-d H:i:s");
	$insId = $_POST['IDedit'];
	$Jum_Pre_Kasar = $_POST['Jum_Pre_Kasar'];
	$Jum_Pre_Bersih = $_POST['Jum_Pre_Bersih'];
	$Cover_Note = $_POST['Cover_Note'];
	//DateFormat:
	$Tkh_Mula =  $_POST['Tkh_Mula'];
	$Tkh_Mula_Ins = explode("/", $Tkh_Mula);
	$dbTkh_Mula_Ins = $Tkh_Mula_Ins[2] . '/' . sprintf("%02s",  $Tkh_Mula_Ins[1]) . '/' . sprintf("%02s",  $Tkh_Mula_Ins[0]);

	$Tkh_Tamat =  $_POST['Tkh_Tamat'];
	$Tkh_Tamat_Ins = explode("/", $Tkh_Tamat);
	$dbTkh_Tamat_Ins = $Tkh_Tamat_Ins[2] . '/' . sprintf("%02s",  $Tkh_Tamat_Ins[1]) . '/' . sprintf("%02s",  $Tkh_Tamat_Ins[0]);
	$sWhere	= "ID='" . $insId . "'";
	$sSQL =	"Update insuranKenderaan set " .
		"Jum_Pre_Kasar = '" . $Jum_Pre_Kasar . "'" .
		",Jum_Pre_Bersih = '" . $Jum_Pre_Bersih . "'" .
		",Cover_Note = '" . $Cover_Note . "'" .
		",Tkh_Mula = '" . $dbTkh_Mula_Ins . "'" .
		",Tkh_Tamat = '" . $dbTkh_Tamat_Ins . "'" .
		",isPrepared='" . $IDUserLog . "'" .
		",isPreparedDate='" . $disediakanDate . "'" .
		" WHERE " . $sWhere;
	$rs	= &$conn->Execute($sSQL);
	//	print $sSQL;
	if ($pgview == "viewsah") {
		$actFName = $sActionFileName;
	} else {
		$actFName = $sFileName;
	}

	print '<script>
				alert ("Rekod Insuran kenderaan telah dikemaskini ke dalam sistem.");
				window.location.href = "' . $actFName . '";			
				</script>';
} elseif ($action == "hantarpengesahan") {
	$sWhere	= "";
	$disediakan = $kerani;
	$disediakanDate = date("Y-m-d H:i:s");
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckIns = ctInsuran("", $pk[$i]);
		if ($CheckIns->RowCount() == 1) {
			$sWhere	= "ID="	. tosql($pk[$i], "Number");
			$sSQL =	"Update	insuranKenderaan set " .
				" status=1 " .
				",isPrepared='" . $disediakan . "'" .
				",isPreparedDate='" . $disediakanDate . "'" .

				" WHERE " . $sWhere;
			$rs	= &$conn->Execute($sSQL);
			//print $sSQL;		
		}
	}
	/*
	//$insId = "";
	$IDins="";		
	for	($i	= 0; $i	< count($pk); $i++)	{
		$CheckIns = ctInsuran("",$pk[$i]);
		if ($CheckIns->RowCount() == 1) {	
		$insId  = $pk[$i];
		$IDins = $IDins."-".$insId;
		$Jum_Pre_Kasar = $Jum_Pre_Kasar[$insId];
		$Jum_Pre_Bersih = $Jum_Pre_Bersih[$insId];
		$Cover_Note= $Cover_Note[$insId];
		
		//DateFormat:
		$Tkh_Mula= $Tkh_Mula[$insId];			
		$Tkh_Mula_Ins = explode("/", $Tkh_Mula); 
		$dbTkh_Mula_Ins= $Tkh_Mula_Ins[2].'/'.sprintf("%02s",  $Tkh_Mula_Ins[1]).'/'.sprintf("%02s",  $Tkh_Mula_Ins[0]);
		
		$Tkh_Tamat= $Tkh_Tamat[$insId];
		$Tkh_Tamat_Ins = explode("/", $Tkh_Tamat); 
		$dbTkh_Tamat_Ins= $Tkh_Tamat_Ins[2].'/'.sprintf("%02s",  $Tkh_Tamat_Ins[1]).'/'.sprintf("%02s",  $Tkh_Tamat_Ins[0]);
		
		$disediakan=$kerani;
		$disediakanDate = date("Y-m-d H:i:s");          
		//Query: 
		$sWhere	= "ID='".$insId."'";		
		$sSQL =	"Update insuranKenderaan set ".
			"Jum_Pre_Kasar = '".$Jum_Pre_Kasar."'".
			",Jum_Pre_Bersih = '".$Jum_Pre_Bersih."'".
			",Cover_Note = '".$Cover_Note."'".
			",Tkh_Mula = '".$dbTkh_Mula_Ins."'".
			",Tkh_Tamat = '".$dbTkh_Tamat_Ins."'".
			",isPrepared='".$disediakan."'".
			",isPreparedDate='".$disediakanDate."'".
			" WHERE " . $sWhere;
		$rs	= &$conn->Execute($sSQL);	
print $sSQL;		
		}	
	}
	//print $IDins ;
	*/
}


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
		$sWhere .= " NoAnggota like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " Nama like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " NoKP like '%" . $q . "%'";
	} else if ($by == 4) {
		$sWhere .= " NoKenderaan like '%" . $q . "%'";
	} else if ($by == 5) {
		$sWhere .= "DATEDIFF(TarikhTamatInsuran,CURDATE()) <" . $q . "";
	}
}


$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "";
$sSQL = "SELECT * ,DATEDIFF(TarikhTamatInsuran,CURDATE()) as days FROM 	insuranKenderaan ";
if ($q <> "") {
	$sSQL = $sSQL . $sWhere . ' and (status =0 or status is null) ORDER BY applyDate DESC';
} else {
	$sSQL = $sSQL . ' where (status =0 or status is null)  ORDER BY applyDate DESC';
}
$GetListIns = &$conn->Execute($sSQL);
$GetListIns->Move($StartRec - 1);

$TotalRec =	$GetListIns->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">

	<tr	valign="top" class="Header">
		<td	align="left" >';

echo '<div clas="row">Cari Berdasarkan
			<select name="by" class="form-select-sm">';
if ($by	== 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option	value="1">Nomor Anggota</option>';
if ($by	== 2)	print '<option value="2" selected>Nama</option>';
else print '<option	value="2">Nama</option>';
if ($by	== 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option	value="3">Kartu Identitas</option>';
if ($by	== 4)	print '<option value="4" selected>Nombor Kenderaan</option>';
else print '<option	value="4">Nombor Kenderaan</option>';
//	if ($by	== 5)	print '<option value="5" selected>Jum.Hari Tempoh Tamat Insuran</option>';		else print '<option	value="5">Jum.Hari Tempoh Tamat Insuran</option>';
print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="20" class="form-control-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
			<!--Jabatan
			<select	name="dept"	class="form-select-sm" onchange="document.MyForm.submit();">
				<option	value="">- Semua -';
for ($i	= 0; $i	< count($deptList); $i++) {
	print '	<option	value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '	</select>-->';
print '</div>
		</td>
	</tr>
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;';
//	Pilihan Proses : 
//	<select	name="filter" class="Data" onchange="document.MyForm.submit();">';

/*	print '<option value="ALL">Semua';
					for	($i	= 0; $i	< count($biayaList); $i++) {
					//if($i	<> 3 ||	$i<>4 ){
					print '	<option	value="'.$biayaVal[$i].'" ';
					if ($filter	== $biayaVal[$i]) print	' selected';
					print '>'.$biayaList[$i];
					//}
					}
					print '</select>&nbsp;';*/

if (($IDName == 'superadmin') or ($IDName == 'admin')) {

	if ($filter < 1) {
		if ($pgview == "viewsah") {
			print 'Tandakan rekod untuk sahkan rekod';
		} else {
			print 'Tandakan rekod untuk hapus <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
		}
	}
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
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td	nowrap>&nbsp;</td>	
						<td	nowrap>&nbsp;</td>							
						<td	nowrap><b>Nomor Rujukan</b></td>					
						<td	nowrap><b>Nama</b></td>
						<td	nowrap align="center"><b>Kartu Identitas</b></td>
						<td	nowrap align="center"><b>Nombor Kenderaan</b></td>
					    <td	nowrap align="right"><b>Jumlah Premium Kasar (RP)</b></td>						
						<td	nowrap align="right"><b>Jumlah Premium Bersih (RP)</b></td>
						<td nowrap align="center"><b>Tarikh Mula Insuran</b></td>
						<td	nowrap align="center"><b>Tarikh Tamat Insuran</b></td>						
						<td	nowrap align="center"><b>Tarikh Mohon</b></td>
						<td	nowrap align="right"><b>Edit</b></td>
						<td	nowrap><b>&nbsp;</b></td>
					</tr>';
	$amtLoan = 0;
	while (!$GetListIns->EOF && $cnt <= $pg) {
		$insuranID2 = $GetListIns->fields(ID);
		$insuranID = tohtml($GetListIns->fields(ID));
		$yearcover = tohtml($GetListIns->fields(insuranYear));
		$noruj = tohtml($GetListIns->fields(insuranNo));
		$nama = tohtml($GetListIns->fields(Nama));
		$nokp = tohtml($GetListIns->fields(NoKP));
		$JumPremium = tohtml($GetListIns->fields(JumlahPremium));
		$JumInsuran = tohtml($GetListIns->fields(JumlahPerlindungan));
		$Jum_Pre_Kasar = tohtml($GetListIns->fields(Jum_Pre_Kasar));
		$Jum_Pre_Bersih = tohtml($GetListIns->fields(Jum_Pre_Bersih));
		$Cover_Note = tohtml($GetListIns->fields(Cover_Note));
		$Tkh_Mula = toDate("d/m/yy", $GetListIns->fields(Tkh_Mula));
		$Tkh_Tamat = toDate("d/m/yy", $GetListIns->fields(TarikhTamatInsuran));
		$nokenderaan = tohtml($GetListIns->fields(NoKenderaan));
		$status = tohtml($GetListIns->fields(Status));
		$days = tohtml($GetListIns->fields(days));
		$dateEndIns = toDate("d/m/yy", $GetListIns->fields(TarikhTamatInsuran));
		$dateApply = toDate("d/m/yy", $GetListIns->fields(applyDate));
		$idSedia = tohtml($GetListIns->fields(isPrepared));
		$rpreparedby = dlookup("users", "name", "userID='" . $idSedia . "'");
		$idApprovedBy = tohtml($GetListIns->fields(isApprovedBy));
		$rApprovedBy = dlookup("users", "name", "userID='" . $idApprovedBy . "'");
		$idSediaDate = toDate("d/m/yy", $GetListIns->fields(isPreparedDate));

		print '	<tr>
						<td	class="Data" align="center">' . $bil	. '&nbsp;</td>
						<td	class="Data" align="right"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetListIns->fields(ID)) . '"></td>
					    <td	class="Data"><a href="' . $sFileRef . '&pk=' . $insuranID . '">' . $noruj . '</a></td>';
		print '
					    <td	class="Data" align="left">' . $nama . '&nbsp;</td>						
						<td	class="Data" align="center">' . $nokp . '&nbsp;</td>
						<td	class="Data" align="center">' . $nokenderaan . '</td>';

		/*if($pgview=="viewsah"){
						print '
						<td	class="Data">'.$Jum_Pre_Kasar.'</td>
						<td	class="Data">'.$Jum_Pre_Bersih.'</td>
						<td	class="Data">'.$Cover_Note.'</td>
						<td	class="Data">'.$Tkh_Mula.'</td>
						<td	class="Data">'.$Tkh_Tamat.'</td>';
						}else */
		if ($code == '2' && $ID == $insuranID) {
			print '
						<td	class="Data" align="right"><input type="text" name="Jum_Pre_Kasar" class="form-control-sm" value="' . $Jum_Pre_Kasar . '" size="15" ></td>
						<td	class="Data" align="right"><input type="text" name="Jum_Pre_Bersih" class="form-control-sm" value="' . $Jum_Pre_Bersih . '" size="15"></td>
						<td	class="Data" align="center"><input type="text" name="Tkh_Mula" class="form-control-sm" value="' . $Tkh_Mula . '" size="15" placeholder="dd/mm/yyyy"></td>
						<td	class="Data" align="center"><input type="text" name="Tkh_Tamat" class="form-control-sm" value="' . $Tkh_Tamat . '" size="15"  placeholder="dd/mm/yyyy"></td>';
		} else {
			print '
						<td	class="Data" align="right">' . $Jum_Pre_Kasar . '</td>
						<td	class="Data" align="right">' . $Jum_Pre_Bersih . '</td>
						<td	class="Data" align="center">' . $Tkh_Mula . '</td>
						<td	class="Data" align="center">' . $Tkh_Tamat . '</td>';
		}
		print '
						<td	class="Data" align="center">' . $dateApply . '</td>
						  <td class="Data" align="center">&nbsp;';
		if ($code == '2' && $ID == $insuranID) {
		} else {
			if ($pgview != "viewsah") {
				$sFileName = $sFileName . "&";
			} else {
				$sFileName = $sFileName . "&";
			}
			print '<a href="' . $sFileName . 'ID=' . $insuranID . '&code=2" title="kemaskini"><i class="dripicons dripicons-document-edit"></i></a> <input size="7" type="hidden" name="ID" value="' . $insuranID . '" >';
		}
		print '  </td>
						  <td class="Data" align="center">';
		if ($code == '2' && $ID == $insuranID) {
			print '<input size="7" type="hidden" name="IDedit" value="' . $insuranID . '" ><input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini rekod ini?\')) {return false} else {window.MyForm.submit();};" name="edit" class="btn btn-sm btn-primary" value="edit" />';
		}
		print ' 
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
		print '<tr><td class="textFont"	valign="top" align="left">Data Dari : <br>';
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
			<td	class="textFont">Jumlah Data :	<b>' . $GetListIns->RowCount()	. '</b></td>
		</tr>';
	//Disediakan -Disahkan:
	print '
		<tr>
	<td valign="top"><table  width="100%" border="0" cellspacing="0" cellpadding="0" ><tr><td width="50%">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr><td nowrap="nowrap" colspan="3">Disahkan Oleh</td></tr>';
	if ($pgview == "viewsah") {
		print '
			<tr><td nowrap="nowrap">Pegawai Insuran</td><td valign="top"></td><td>' . $rpreparedby . '</td></tr>
			<tr><td nowrap="nowrap">Tanggal</td><td valign="top"></td><td>' . $idSediaDate . '</td></tr>';
	} else {
		print '
			<tr><td nowrap="nowrap">Pegawai Insuran</td><td valign="top"></td><td>' . selectAdmin($kerani, 'kerani') . '</td></tr>
			<tr><td colspan="3"><input type="button" value="Hantar Pengesahan" class="btn btn-md btn-primary" onclick="CheckField(\'HantarPengesahan\')"></td></tr>';
	}
	print '</table>
			</td><td>';
	if ($pgview == "viewsah") {
		print '
			<table border="0" cellspacing="1" cellpadding="3">
			<tr><td nowrap="nowrap" colspan="3">Disahkan Oleh</td></tr>		
			<tr><td nowrap="nowrap">Pegawai Insuran</td><td valign="top"></td><td>' . dlookup("users", "name", "userID='" . $IDUserLog . "'") . '</td></tr>
			<tr><td colspan="3"><input type="button" class="but" value="Sah" onClick="ITRActionButtonClick(\'sah\');"></td></tr>
			</table>';
	} else {
		print '	<table border="0" cellspacing="1" cellpadding="3">
			<tr><td nowrap="nowrap" colspan="3"></td></tr>		
			<tr><td nowrap="nowrap"></td><td valign="top"></td><td>' . $rApprovedBy . '</td></tr>
			<tr><td colspan="3"></td></tr>
			</table>';
	}
	print '
			</td></tr></table>
	</td>
</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr	size=1"></td></tr>';
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
		
		function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		 	  
		  if( e.elements[c].value==\'\') {		
            count++;
		  }
		  }		

		//if(count==0) {
			e.action.value = \'hantarpengesahan\';
			e.submit();
		//}else{
		//		alert(\'Ruang amaun perlu diisi!\');
		//}

	}

</script>';
