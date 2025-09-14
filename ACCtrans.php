<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCtrans.php
 *          Date 		: 	02/02/2022
 *********************************************************************************/
if (!isset($mm))					$mm = "ALL";
if (!isset($yy))					$yy = date("Y");
if (!isset($StartRec) || $capai)	$StartRec = 1;
if (!isset($pg) || $capai)			$pg = 10;
if (!isset($q) || $capai)			$q = "";
if (!isset($code) || $capai)		$code = "ALL";
if (!isset($filter) || $capai)		$filter = "0";
$yymm = sprintf("%04d%02d", $yy, $mm);

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

if (get_session("Cookie_groupID") == '0') {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = 'ACCtrans.php';
$title     =  "Laporan Urusniaga Akaun";

$IDName = get_session("Cookie_userName");
//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	if (get_session("Cookie_groupID") == 2) {
		for ($i = 0; $i < count($pk); $i++) {
			$sWhere = "ID=" . tosql($pk[$i], "Number");
			$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
			$rs = &$conn->Execute($sSQL);
		}
	} else {
		print '<script>alert("Aktiviti Tidak Dibenarkan !");</script>';
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

//--- Prepare deduct list
$deductList = array();
$deductVal  = array();
$sSQL = "	SELECT B.ID, B.code , B.name 
			FROM transactionacc A, generalacc B
			WHERE A.deductID= B.ID
			AND   A.yrmth = " . tosql($yymm, "Text") . "	
			AND   A.status = " . tosql($filter, "Number") . "	
			GROUP BY A.deductID";
$GetDeduct = &$conn->Execute($sSQL);
if ($GetDeduct->RowCount() <> 0) {
	while (!$GetDeduct->EOF) {
		array_push($deductList, $GetDeduct->fields(code) . ' - ' . $GetDeduct->fields(name));
		array_push($deductVal, $GetDeduct->fields(ID));
		$GetDeduct->MoveNext();
	}
}

$sSQL = "";
$sWhere = " A.deductID = B.ID  ";
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.docNo LIKE '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID LIKE '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND B.name LIKE '%" . $q . "%'";
	}
}
$sWhere .= " AND year(A.createdDate) = " . $yy;
if ($mm <> "ALL") $sWhere .= " AND month( A.createdDate ) =" . $mm;
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	A.*, B.*, A.ID as transID, A.createdDate as transDate
			 FROM 	transactionacc A, generalacc B";
$sSQL = $sSQL . $sWhere . ' ORDER  BY A.createdDate DESC LIMIT ' . ($StartRec - 1) . ' , ' . $pg;
$GetTrans = &$conn->Execute($sSQL);

//--------------------------------------------------------------------
$sSQL = "";
$sWhere = " A.deductID = B.ID";

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.docNo LIKE '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND B.name LIKE '%" . $q . "%'";
	}
}
$sWhere .= " AND year( A.createdDate ) = " . $yy;
if ($mm <> "ALL") $sWhere .= " AND month( A.createdDate ) =" . $mm;
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	A.*, B.*, A.ID as transID, A.createdDate as transDate
			 FROM 	transactionacc A, generalacc B";
$sSQL = $sSQL . $sWhere . ' ORDER  BY A.createdDate DESC';
$GetTransCount = &$conn->Execute($sSQL);

$TotalRec = $GetTransCount->RowCount();
$GetTransCount->Close();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="POST">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<input type="hidden" name="by" value="' . $by . '">
<input type="hidden" name="StartRec" value="' . $StartRec . '">

<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	<tr>
		<td height="50" class="textFont">
			Bulan   : 
			<select name="mm" class="data" onchange="document.MyForm.submit();">
			<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun  : 
			<select name="yy" class="data" onchange="document.MyForm.submit();">';
for ($j = 2000; $j <= 2080; $j++) {
	print '	<option value="' . $j . '"';
	if ($yy == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			<input type="submit" name="capai" value="Capai" class="but">
		</td>
	</tr>
    <tr valign="top" class="Header">
	   	<td align="left" >
			Cari Berdasarkan 
			<select name="by" class="Data">';
if ($by == 1)	print '<option value="1" selected>No Dokumen</option>';
else print '<option value="1">No Dokumen</option>';
if ($by == 3)	print '<option value="3" selected>Urusniaga</option>';
else print '<option value="3">Urusniaga</option>';
print '		</select>
			<input type="text" name="q" value="' . $q . '" maxlength="50" size="20" class="Data">
 			<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;';


if (($IDName == 'superadmin') or ($IDName == 'admin')) {
	print '	<input type="button" class="but" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">
		</td>
	</tr>';
}
if ($GetTrans->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
						<td align="right" class="textFont">
							Tampil <SELECT name="pg" class="Data"  onchange="document.MyForm.submit();">';
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
	if ($pg == 200)	print '<option value="200" selected>200</option>';
	else print '<option value="200">200</option>';
	if ($pg == 300)	print '<option value="300" selected>300</option>';
	else print '<option value="300">300</option>';
	if ($pg == 400)	print '<option value="400" selected>400</option>';
	else print '<option value="400">400</option>';
	if ($pg == 500)	print '<option value="500" selected>500</option>';
	else print '<option value="500">500</option>';
	if ($pg == 1000) print '<option value="1000" selected>1000</option>';
	else print '<option value="1000">1000</option>';
	print '</select>setiap halaman. &nbsp;

			Muka Surat: 
			<select name="StartRec" class="data" onchange="document.MyForm.submit();">';

	if ($TotalRec % $pg == 0) {
		$numPage = $TotalPage;
	} else {
		$numPage = $TotalPage + 1;
	}

	for ($i = 1; $i <= $numPage; $i++) {
		print '	<option value="' . (($i * $pg) + 1 - $pg) . '"';
		if ($StartRec == (($i * $pg) + 1 - $pg)) print 'selected';
		print '>' . $i;
	}
	print '		</select>			
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap>&nbsp;</td>
						<td nowrap>&nbsp;Nombor Dokumen</td>
						<td nowrap>&nbsp;Urusniaga</td>
						<td nowrap>&nbsp;Rujukan</td>
						<td nowrap align="center" width="50">&nbsp;Debit/Kredit</td>
						<td nowrap align="center">&nbsp;Amaun</td>
						<td nowrap align="center">&nbsp;Tanggal</td>
					</tr>';
	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetTrans->EOF) {
		$status = $GetTrans->fields(status);
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		$totalAmt = $GetTrans->fields(pymtAmt) + $GetTrans->fields(cajAmt);
		if ($GetTrans->fields(addminus) == 0) {
			$addMinus = 'Debit';
		} else {
			$addMinus = 'Kredit';
		}
		$transD = toDate("d/m/y", $GetTrans->fields(transDate));
		print ' <tr>
			<td class="Data" align="right">' . $bil . '&nbsp;</td>
			<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetTrans->fields(transID)) . '">';

		print '		&nbsp;' . $GetTrans->fields(docNo) . '</td>
			
			<td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetTrans->fields(deductID), "Number")) . '</td>
			<td class="Data">&nbsp;' . $GetTrans->fields(pymtRefer) . '</td>
			<td class="Data">&nbsp;' . $addMinus . '</td>						
			<td class="Data" align="right">' . number_format($GetTrans->fields(pymtAmt), 2) . '&nbsp;</td>
			<td class="Data" align="center">&nbsp;' . $transD . '</td>
						
			</tr>';
		$cnt++;
		$bil++;
		$GetTrans->MoveNext();
	}

	$GetTrans->Close();
	print '	</table>
			</td>
		</tr>	';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . ' Bagi Bulan/Tahun - ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
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
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak dihapuskan.\');
	        } else {
	          if(confirm(count + \' rekod hendak dihapuskan?\')) {
	            e.action.value = v;
	            e.submit();
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
		}
	}
	

</script>';
