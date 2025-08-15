<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	voucherslist.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$sFileName = 'receiptsList.php';
$sFileRef  = 'receipts.php';
$title     =  "Resit";

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "no_resit=" . tosql($pk[$i], "Text");
		$sSQL = "DELETE FROM resit WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
		$sSQL = "DELETE FROM transaction WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
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

if ($q) $getQ = " AND no_resit like '%" . $q . "%'";
else $getQ = '';
$sSQL = "SELECT *
		FROM resit
		WHERE month( tarikh_resit ) =" . tosql($mm, "Text") . "
		AND year( tarikh_resit ) =" . tosql($yy, "Text") . $getQ . " order by no_resit desc";
$GetReceipts = &$conn->Execute($sSQL);
$GetReceipts->Move($StartRec - 1);

$TotalRec = $GetReceipts->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	<tr>
		<td height="50" class="textFont">
			Bulan   : 
			<select name="mm" class="data">';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun  : 
			<select name="yy" class="data">';
for ($j = 1989; $j <= 2079; $j++) {
	print '	<option value="' . $j . '"';
	if ($yy == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="but">
		</td>
	</tr>
    <tr valign="top" class="Header">
	   	<td align="left" >
		Carian <input type="text" name="q" value="" maxlength="100" size="20" class="Data">
           	 <input type="submit" class="but" value="Cari">
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
print '	</select-->&nbsp;&nbsp;	
	        <input type="button" class="but" value="Tambah" onClick="location.href=\'' . $sFileRef . '?action=add\';">
	        <!--input type="button" class="but" value="Hapus" onClick="ITRActionButtonClick(\'delete\');"-->            
			<!--input type="button" class="but" value="Status" onClick="ITRActionButtonStatus();"-->
		</td>
	</tr>';
if ($GetReceipts->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td>
						<td align="right" class="textFont">
							Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
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
	print '				</select>setiap mukasurat.
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
						<td nowrap>&nbsp;Nombor larian</td>
						<td nowrap>&nbsp;Bayar nama</td>
						<td nowrap>&nbsp;Tarikh</td>
						<td nowrap>&nbsp;Bayar Kod</td>
						<td nowrap align="center" width="50">&nbsp;Jumlah</td>
					</tr>';
	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetReceipts->EOF && $cnt <= $pg) {
		$totalAmt = $GetReceipts->fields(pymtAmt) + $GetReceipts->fields(cajAmt);
		print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetReceipts->fields(no_resit)) . '"> 
						<a href="' . $sFileRef . '?action=view&ID=' . tohtml($GetReceipts->fields(transID)) . '&yy=' . $yy . '&mm=' . $mm . '">
							&nbsp;' . $GetReceipts->fields(no_resit) . '</td>
						<td class="Data">&nbsp;' . $GetReceipts->fields(bayar_nama) . '</td>
						<td class="Data">&nbsp;' . toDate("d/m/y", $GetReceipts->fields(tarikh_resit)) . '</td>
						<td class="Data">&nbsp;' . $GetReceipts->fields(bayar_kod) . '</td>
						<td class="Data">&nbsp;' . $GetReceipts->fields(kod_siri) . '</td>						
					</tr>';
		$cnt++;
		$bil++;
		$GetReceipts->MoveNext();
	}
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
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
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
			<td class="textFont">Jumlah Baucer : <b>' . $GetReceipts->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . ' Bagi Bulan/Tahun - ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
				window.open(\'transStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
</script>';
