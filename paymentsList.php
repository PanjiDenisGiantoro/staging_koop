<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	paymentslist.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
if (!isset($mm))	$mm = "ALL";
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=paymentsList&mn=908';
$sFileRef  = '?vw=payments&mn=908';
$title     =  "Auto Pay";

$IDName = get_session("Cookie_userName");

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "no_bayar=" . tosql($pk[$i], "Text");

		$no_bayar = dlookup("bayar", "no_bayar", $sWhere);

		$sSQL = "DELETE FROM bayar WHERE " . $sWhere;
		//print $sSQL.'<br />';
		$rs = &$conn->Execute($sSQL);
		$sSQL = "DELETE FROM bayar_detail WHERE " . $sWhere;
		//print $sSQL.'<br />';
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . ' Auto Pay Dihapuskan - ' . $no_bayar;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

//--- Prepare deduct list
$deductList = array();
$deductVal  = array();
$sSQL = "	SELECT B.ID, B.code , B.name 
			FROM transaction A, general B
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

/*if ($code <> "ALL")  {
	$Getbayars = ctbayarsactionCode($q,$yymm,$filter,$code);
} else {
	$Getbayars = ctbayarsaction($q,$yymm,$filter);
}*/
//$conn->debug =1;
if ($q) $getQ = " AND no_bayar like '%" . $q . "%'";
else $getQ = '';
$sSQL = "SELECT * FROM `bayar`
		WHERE year( tarikh_bayar ) = " . $yy;
if ($mm <> "ALL") $sSQL .= " AND month( tarikh_bayar ) =" . $mm;
$sSQL .= $getQ . " order by tarikh_bayar desc";

//		WHERE month( tarikh_bayar ) =" .tosql($mm,"Text")."
//		AND year( tarikh_bayar ) =" .tosql($yy,"Text").$getQ." order by no_bayar desc";
$Getbayars = &$conn->Execute($sSQL);
$Getbayars->Move($StartRec - 1);

$TotalRec = $Getbayars->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	
	<tr>
		<td height="50" class="textFont">
			Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
			<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
for ($j = 1989; $j <= 2079; $j++) {
	print '	<option value="' . $j . '"';
	if ($yy == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		</td>
	</tr>
    <tr valign="top" class="Header">
	   	<td align="left" >
		Cari Berdasarkan <input type="text" name="q" value="" maxlength="100" size="20" class="form-control-sm">
           	 <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
			&nbsp;&nbsp;			
			<!--Kod Potongan
			<select name="code" class="Data" onchange="document.MyForm.submit();">
				<option value="ALL">- Semua -';
for ($i = 0; $i < count($deductList); $i++) {
	print '	<option value="' . $deductVal[$i] . '" ';
	if ($code == $deductVal[$i]) print ' selected';
	print '>' . $deductList[$i];
}
print '		</select>&nbsp;
			Status
			<select name="filter" class="Data" onchange="document.MyForm.submit();">';
for ($i = 0; $i < count($statusList); $i++) {
	if ($statusVal[$i] < 3) {
		print '	<option value="' . $statusVal[$i] . '" ';
		if ($filter == $statusVal[$i]) print ' selected';
		print '>' . $statusList[$i];
	}
}
print '	</select-->&nbsp;&nbsp;	<br/><br/>
	    <input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\'' . $sFileRef . '&action=new\';">';


if (($IDName == 'admin') or ($IDName == 'superadmin')) {
	print ' &nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}
print '<!--input type="button" class="but" value="Status" onClick="ITRActionButtonStatus();"-->
		</td>
	</tr>';
if ($Getbayars->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
						<td align="right" class="textFont">
							Tampil <SELECT name="pg" class="form-select-sm" onchange="doListAll();">';
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
	print '				</select>setiap halaman.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-striped table-sm">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nombor Bayar</td>
						<td nowrap align="center">Tarikh</td>
						<td nowrap align="center">Bil. Data</td>
						<!--td nowrap>Nombor Siri</td-->
						<td nowrap align="right" width="50">Jumlah (RM)</td>
					</tr>';
	while (!$Getbayars->EOF && $cnt <= $pg) {
		$sql = "SELECT sum( amount ) AS tot, count(ID) as totID FROM bayar_detail WHERE no_bayar = '" . $Getbayars->fields(no_bayar) . "'";
		$rsSum = $conn->Execute($sql);
		$jumlah = $rsSum->fields(tot);
		$totID = $rsSum->fields(totID);
		print ' <tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($Getbayars->fields(no_bayar)) . '">
						<a href="' . $sFileRef . '&action=view&no_bayar=' . tohtml($Getbayars->fields(no_bayar)) . '&yy=' . $yy . '&mm=' . $mm . '">
							' . $Getbayars->fields(no_bayar) . '</td>
						<td class="Data" align="center">' . toDate("d/m/y", $Getbayars->fields(tarikh_bayar)) . '</td>
						<td class="Data" align="center">' . $totID . '</td>
						<!--td class="Data"></td-->
						<td class="Data" align="right">' . number_format($jumlah, 2) . '</td>						
					</tr>';
		$cnt++;
		$bil++;
		$Getbayars->MoveNext();
	}
	$Getbayars->Close();

	print '	</table>
			</td>
		</tr>	
		<!--tr>
			<td class="textFont" align="right">
			<b>Debit&nbsp;:&nbsp;' . number_format($DRTotal, 2, '.', ',') . '&nbsp;&nbsp;&nbsp;
			Kredit&nbsp;:&nbsp;' . number_format($CRTotal, 2, '.', ',') . '&nbsp;&nbsp;&nbsp;</b>
			</td>
		</tr-->	
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
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah bayar : <b>' . $Getbayars->RowCount() . '</b></td>
		</tr>';
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
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.open(\'transStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
