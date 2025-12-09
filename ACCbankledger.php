<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCbankrecon.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
if (!isset($mm))	$mm = "ALL"; //date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.deductID,b.name as deptName 
			FROM transactionacc a, generalacc b
			WHERE a.deductID = b.ID AND b.a_class IN (132) GROUP BY a.deductID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(deductID));
		$rs->MoveNext();
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "ID=" . tosql($pk[$i], "Text"); //new 
		$sSQL = "UPDATE transactionacc SET checkstatus = 1 WHERE " . $sWhere;

		$rs = &$conn->Execute($sSQL);
		print '<script>alert("Penyata kewangan telah disahkan.");</script>';
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$sFileName = "?vw=ACCbankledger&mn=$mn"; //file name
$title     =  "Lejer Bank"; //Title 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($dept <> "") {
	$getQ .= " AND A.deductID = '" . $dept . "'";
} else {
	$getQ .= " AND B.a_class IN (132)";
}

$sSQL = "SELECT DISTINCT A.*,B.ID,A.ID as transID FROM transactionacc A, generalacc B WHERE A.deductID=B.ID AND docID NOT IN (0,15) AND YEAR(A.tarikh_doc) = " . $yy;

if ($mm <> "ALL") $sSQL .= " AND MONTH(A.tarikh_doc) =" . $mm;
$sSQL .= $getQ . " ORDER BY A.tarikh_doc DESC";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);
$TotalRec = $GetVouchers->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<div clas="row">
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
for ($j = 2005; $j <= 2030; $j++) {
	print '	<option value="' . $j . '"';
	if ($yy == $j) print 'selected';
	print '>' . $j;
}
print '		</select>

		<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
    </div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
    <tr valign="top" class="Header">
	   	<td align="left" >';

print '	<br/>

			Bank
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '		</select>&nbsp;&nbsp; 
			 <input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

print ' 
		</td>
	</tr>';

if ($GetVouchers->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">
							
						</td>

				<td >';

	print '</td>
						<td align="right" class="textFont">';
	echo papar_ms($pg);
	print '</td>
					</tr>
				</table>
			</td>
		</tr>';
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap align ="center"><b>Bil</b></td>
						<td nowrap align ="center"><b>Nomor Rujukan</b></td>
						<td nowrap><b>Bank</b></td>
						<td nowrap align ="center"><b>Tanggal</b></td>
						<td nowrap align ="right"><b>Debit (RP)</b></td>
						<td nowrap align ="right"><b>Kredit (RP)</b></td>
						<td nowrap align ="right"><b>Saldo (RP)</b></td>
											
					</tr>';

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetVouchers->EOF && $cnt <= $pg) {
		$jumlah = 0;
		$tarikh_baucer = toDate("d/m/y", $GetVouchers->fields(tarikh_doc));

		$bankname = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(deductID), "Text"));

		print ' <tr>
			<td class="Data" align="center">' . $bil . '</td>';

		print '	<td class="Data" align ="center">' . $GetVouchers->fields(docNo) . '</td>';
		print '	<td class="Data">' . $bankname . '</td>';
		print '	<td class="Data" align ="center">' . $tarikh_baucer . '</td>';

		if ($GetVouchers->fields(addminus) == 0) {

			$debit = $GetVouchers->fields(pymtAmt);

			print '	<td class="Data" align ="right">' . $debit . '</td>';
			print '	<td class="Data" align ="right">0.00</td>';

			$totalDb += $debit;
		}
		if ($GetVouchers->fields(addminus) == 1) {

			$kredit = $GetVouchers->fields(pymtAmt);
			print '	<td class="Data" align ="right">0.00</td>';
			print '	<td class="Data" align ="right">' . $kredit . '</td>';

			$totalKt += $kredit;
		}
		print '	<td class="Data">&nbsp;</td>';

		print '	</tr>';
		$cnt++;
		$bil++;


		$GetVouchers->MoveNext();
	}
	$GetVouchers->Close();

	print '<tr>
				<td class="Data" colspan="4" align="right"><b>JUMLAH (RP)</b></td>
				<td class="Data" align="right"><b>' . number_format($totalDb, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>' . number_format($totalBank, 2) . '&nbsp;</b></td>
			</tr>';



	print '	</table>
</td></tr><tr><td>';

	if ($TotalRec > $pg) {
		print ' <table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
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
		print '</td></tr></table>';
	}
	print '</td></tr><tr>
			<td class="textFont">Jumlah Rujukan : <b>' . $GetVouchers->RowCount() . '</b></td>
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
</form></div>';

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
	          alert(\'Sila pilih rekod yang hendak disahkan.\');
	        } else {
	          if(confirm(count + \' rekod hendak disahkan?\')) {
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
