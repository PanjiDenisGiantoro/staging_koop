<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	ACCinvestList.php
*          Date 		: 	04/8/2006
*********************************************************************************/
if (!isset($mm))	$mm="ALL";//date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($code))		$code="ALL";
if (!isset($filter))	$filter="0";

include("header.php");	
include("koperasiQry.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}


$sFileName = "?vw=ACCinvestList&mn=$mn";//file name
$sFileRef  = "?vw=ACCinvestdebtor&mn=$mn";// file ni pergi mane
$title     =  "Invois Pelaburan";//Title 

$IDName = get_session("Cookie_userName");

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "investNo=" . tosql($pk[$i], "Text");
		$sSQL = "DELETE FROM pb_invoice WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
		$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
	
		$rs = &$conn->Execute($sSQL);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sSQL = "";
	$sWhere = "  YEAR(tarikh_invest) = ".$yy;
	
	if ($q <> "") 	{
		if ($by == 1) {
			$sWhere .= " AND A.batchNo = B.ID";
			$sWhere .= " AND B.name like '%".$q."%'";			
		} else if ($by == 2) {
			$sWhere .= " AND A.investNo like '%".$q."%'";
		} else if ($by == 3) {
			$sWhere .= " AND A.companyID = B.ID";
			$sWhere .= " AND B.name like '%".$q."%'";		
		}
	}

	$sWhere = " WHERE (".$sWhere.")";
	
	if ($q <> "") 	{
		if ($by == 1 OR $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM pb_invoice A, generalacc B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM pb_invoice A";
		}
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM pb_invoice A ";
	}
	//if($mm <> "ALL") $sWhere .= " AND month( A.createdDate ) =" .$mm;
	if($mm <> "ALL") $sWhere .= " AND MONTH(A.tarikh_invest) =".$mm;
	$sSQL = $sSQL.$sWhere. ' ORDER BY A.investNo DESC';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec-1);

$TotalRec = $GetVouchers->RowCount();
$TotalPage =  ($TotalRec/$pg);

$sqlYears = "SELECT DISTINCT YEAR(tarikh_invest) AS year FROM pb_invoice WHERE tarikh_invest IS NOT NULL AND tarikh_invest != '' AND tarikh_invest != 0 ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

print '<div class="table-responsive">
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="'.$code.'">
<input type="hidden" name="filter" value="'.$filter.'">
<h5 class="card-title">'.strtoupper($title).' &nbsp;</h5>
<div clas="row">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
				if ($mm == "ALL") print 'selected';
				print '>- Semua -';
			for ($j = 1; $j < 13; $j++) {
				print '	<option value="'.$j.'"';
				if ($mm == $j) print 'selected';
				print '>'.$j;
			}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
			while (!$rsYears->EOF) {
				$year = $rsYears->fields['year'];
				print '	<option value="'.$year.'"';
				if ($yy == $year) print 'selected';
				print '>'.$year;
				$rsYears->MoveNext();
			}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div><br/>
<div clas="row">
    Cari Berdasarkan
				<select name="by" class="form-select-sm">'; 
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>'; else print '<option value="1">Nama Batch</option>';		

if ($by == 2)	print '<option value="2" selected>No Invoice</option>'; 	else print '<option value="2">No Invoice</option>';			
if ($by == 3)	print '<option value="3" selected>Nama Syarikat</option>'; 	else print '<option value="3">Nama Syarikat</option>';				
				
print '		</select>
				<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
           	 <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
			&nbsp;&nbsp;			
			<!--Kod Potongan
			<select name="code" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL">- Semua -';
			for ($i = 0; $i < count($deductList); $i++) {
				print '	<option value="'.$deductVal[$i].'" ';
				if ($code == $deductVal[$i]) print ' selected';
				print '>'.$deductList[$i];

			}
print '		</select>&nbsp;
			Status
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
			for ($i = 0; $i < count($statusList); $i++) {
				if ($statusVal[$i] < 3) {
					print '	<option value="'.$statusVal[$i].'" ';
					if ($filter == $statusVal[$i]) print ' selected';
					print '>'.$statusList[$i];
				}
			}
	print '	</select-->&nbsp;&nbsp;';

print '</select> &nbsp;&nbsp;

		<input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\''.$sFileRef.'&action=new&jenis='.$jenis.'\';">';
if (($IDName == 'admin') OR ($IDName == 'superadmin')){

print'&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
} 

print'
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">

    <tr valign="top" class="Header">
	   	<td align="left" >
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
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
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
						<td nowrap>&nbsp;</td>
						<td nowrap><b>No. Invois</b></td>
						<td nowrap align="center"><b>Tarikh</b></td>
						<td nowrap><b>Nama Syarikat</b></td>
						<td nowrap><b>Nama Projek</b></td>
						<td nowrap align="center"><b>Catatan</b></td>
						<td nowrap align="right"><b>Jumlah Invois (RP)</b></td>
					</tr>';	
		$DRTotal = 0;
		$CRTotal = 0;
		while (!$GetVouchers->EOF && $cnt <= $pg) {
			 $jumlah = 0;						

			$namacomp = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(companyID), "Text"));
			$description = $GetVouchers->fields(description);
			$tarikh_invest = toDate("d/m/y",$GetVouchers->fields(tarikh_invest));


			$projectName = dlookup("investors", "nameproject", "ID=" .$GetVouchers->fields(kod_project));

		$sSQL2 = "SELECT g_lockstat FROM generalacc WHERE ID = ".$GetVouchers->fields(batchNo)." ORDER BY ID";
		$rsDetail =&$conn->Execute($sSQL2);

		print ' <tr><td class="Data" align="center">'.$bil.'</td>';
		
		if ($rsDetail->fields(g_lockstat) == 1) {
		print '
		<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetVouchers->fields(investNo)).'">
		'.$GetVouchers->fields(investNo).'</td>';
	}else{
		print '
		<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetVouchers->fields(investNo)).'">
		<a href="'.$sFileRef.'&action=view&investNo='.tohtml($GetVouchers->fields(investNo)).'&yy='.$yy.'&mm='.$mm.'">
		'.$GetVouchers->fields(investNo).'</td>';
	}
		print'
		<td class="Data" align="center">'.$tarikh_invest.'</td>
		<td class="Data" width="20%">'.$namacomp.'</td>
		<td class="Data" width="20%">'.$projectName.'</td>
		<td class="Data" width="20%" align="left">'.$description.'</td>
		<td class="Data" align="right">'.number_format($GetVouchers->fields(outstandingbalance),2).'</td>
		</tr>';
		$cnt++;
		$bil++;
		$GetVouchers->MoveNext();
	}
		$GetVouchers->Close();

	print '	</table>
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
					for ($i=1; $i <= $numPage; $i++) {
						if(is_int($i/10)) print '<br />';
						print '<A href="'.$sFileName.'&yy='.$yy.'&mm='.$mm.'&code='.$code.'&filter='.$filter.'&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a>&nbsp;&nbsp;';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Voucher : <b>'.$GetVouchers->RowCount().'</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk '.$title.' Bagi Bulan/Tahun - '.$mm.'/'.$yy.' -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
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
		document.location = "' . $sFileName . '&yy='.$yy.'&mm='.$mm.'&code='.$code.'&filter='.$filter.'&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
</script>';
?>