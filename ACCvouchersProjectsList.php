<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	vouchersprojectslist.php
*          Date 		: 	05/02/2024
*********************************************************************************/
if (!isset($mm))	$mm="ALL";//date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($q))			$q="";
if (!isset($code))		$code="ALL";
if (!isset($filter))	$filter="0";

include("header.php");	
include("koperasiQry.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCvouchersProjectsList&mn=$mn";//file name
$sFileRef  = "?vw=ACCbaucerprojek&mn=$mn";// file ni pergi mane
$title     =  "Voucher Pembayaran Projek (Buku Tunai)";//Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "no_baucer=" . tosql($pk[$i], "Text");
		$sSQL = "DELETE FROM baucerprojekacc WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
		$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
	
		$rs = &$conn->Execute($sSQL);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------



if ($q <> "") 	{
	if ($by == 1) {
		$getQ .= " AND b.name = '".$q."'";			
	} else if ($by == 2) {
		$getQ .= " AND a.no_baucer like '%".$q."%'";
	}  else if ($by == 3) {
		$getQ .= " AND a.keterangan like '%".$q."%'";
	} 
}
// sql select dari table mana 
$sSQL = "SELECT a.*,b.*, b.g_lockstat
		FROM baucerprojekacc a, generalacc b
		WHERE  a.batchNo=b.ID AND YEAR(a.tarikh_baucer) = " . $yy;

if($mm <> "ALL") $sSQL .= " AND MONTH(a.tarikh_baucer) =" .$mm;
$sSQL .= $getQ." ORDER BY a.no_baucer DESC";


$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec-1);

$TotalRec = $GetVouchers->RowCount();
$TotalPage =  ($TotalRec/$pg);

print '<div class="table-responsive">
<form name="MyForm" action=' .$sFileName . ' method="post">
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
			for ($j = 2020; $j <= 2030; $j++) {
				print '	<option value="'.$j.'"';
				if ($yy == $j) print 'selected';
				print '>'.$j;
			}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
    </div><br/>
     <div clas="row">
     Cari Berdasarkan
				<select name="by" class="form-select-sm">'; 
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>'; 	else print '<option value="1">Nama Batch</option>';				
if ($by == 2)	print '<option value="2" selected>No. Voucher</option>'; 	else print '<option value="2">No. Voucher</option>';	
if ($by == 3)	print '<option value="3" selected>Keterangan</option>'; 	else print '<option value="3">Keterangan</option>';				
				
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

if (($IDName == 'superadmin') OR ($IDName == 'admin')){

print'&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
} 

print' <!--input type="button" class="but" value="Status" onClick="ITRActionButtonStatus();"-->
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
						<td nowrap align="center"><b>Bil</b></td>
						<td nowrap align="left"><b>No. Voucher</b></td>
						<td nowrap align="center"><b>Tanggal</b></td>
						<td nowrap align="left"><b>Bank</b></td>
						<td nowrap align="left"><b>Keterangan</b></td>
						<td nowrap align="right"><b>Jumlah (RP)</b></td>					
						<td nowrap align="right"><b>Nama Serikat</b></td>					
						<td nowrap align="right"><b>Nama Projek</b></td>					
					</tr>';	

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetVouchers->EOF && $cnt <= $pg) {
		$jumlah = 0;
		
		$tarikh_baucer = toDate("d/m/y",$GetVouchers->fields(tarikh_baucer));
		$bank = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(kod_bank), "Text"));
		// $batchName = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(batchNo), "Text"));
		$amount = $GetVouchers->fields(pymtAmt);
		$companyName = $GetVouchers->fields(bayaran_kpd);
		$projectName = dlookup("investors", "nameproject", "ID=" .$GetVouchers->fields(kod_project));

		print ' <tr>
		<td class="Data" align="center">'.$bil.'</td>';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////d//////////////////////////////////////////////////////////////////////////////////////
	if ($GetVouchers->fields(g_lockstat) == 1)
	{
		print '<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetVouchers->fields(no_baucer)).'">
		'.$GetVouchers->fields(no_baucer).'</td>';
	}else {
		print '<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetVouchers->fields(no_baucer)).'">
		<a href="'.$sFileRef.'&action=view&no_baucer='.tohtml($GetVouchers->fields(no_baucer)).'&yy='.$yy.'&mm='.$mm.'">'.$GetVouchers->fields(no_baucer).'</td>';
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	print'	<td class="Data" align="center">'.$tarikh_baucer.'</td>
			<td class="Data" width="15%">'.$bank.'</td>
			<td class="Data" width="15%">'.$GetVouchers->fields(keterangan).'</td>		
			<td class="Data" align="right">'.number_format($amount,2).'</td>	
			<td class="Data" align="right" width="15%">'.$companyName.'</td>
			<td class="Data" align="right" width="15%">'.$projectName.'</td>
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
			<td class="textFont">Jumlah Rujukan : <b>' . $GetVouchers->RowCount() . '</b></td>
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
