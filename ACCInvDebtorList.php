<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	AccInvDebtorList.php
*          Date 		: 	04/8/2006
*********************************************************************************/ 
if (!isset($mm))	$mm="ALL";//date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($code))		$code="ALL";
if (!isset($filter))	$filter="0";
if (!isset($jenis_cari))	$jenis_cari="";

include("header.php");	
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCInvDebtorList&mn=$mn";//file name
$sFileRef  = "?vw=ACCInvDebtorPayment&mn=$mn";// file ni pergi mane
$title     =  "Pembayaran Penghutang";//Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "PBNo=" . tosql($pk[$i], "Text");
		$sSQL = "DELETE FROM pb_payments WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
		$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
	
		$rs = &$conn->Execute($sSQL);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------



if ($q <> "") 	{
	if ($by == 1) {
		$getQ .= " AND b.name = '" .$q ."'";			
	} else if ($by == 2) {
		$getQ .= " AND a.PBNo like '%" . $q. "%'";
	} 
}
// sql select dari table mana 
$sSQL = "SELECT *, b.name, b.g_lockstat 
		FROM pb_payments a, generalacc b
		WHERE  a.batchNo = b.ID and year( tarikh_PB ) = " . $yy;

if($mm <> "ALL") $sSQL .= " AND month( tarikh_PB ) =" .$mm;
$sSQL .= $getQ." order by PBNo desc";


$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec-1);

$TotalRec = $GetVouchers->RowCount();
$TotalPage =  ($TotalRec/$pg);
$jenisList_cari = array('Penghutang');
$jenisVal_cari = array(2);
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
			for ($j = 2005; $j <= 2030; $j++) {
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
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>'; else print '<option value="1">Nama Batch</option>';				
if ($by == 2)	print '<option value="2" selected>No. PB</option>'; 	else print '<option value="2">No. PB</option>';				
				
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

print' <!--input type="button" class="but" value="Status" onClick="ITRActionButtonStatus();"-->
		
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';
	if ($GetVouchers->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%"><br>
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
						<td nowrap align="center">&nbsp;</td>
						<td nowrap align="left"><b>No. Bayaran</b></td>
						<td nowrap align="center"><b>Tarikh</b></td>
						<td nowrap align="left"><b>Nama Serikat</b></td>						
						<td nowrap align="center"><b>No. Invois</b></td>
						<td nowrap align="right"><b>Amaun Invois (RP)</b></td>
						<td nowrap align="right"><b>Jumlah Bayaran (RP)</b></td>
						<td nowrap align="right"><b>Saldo (RP)</b></td>

					</tr>';	

		$DRTotal = 0;
		$CRTotal = 0;
		while (!$GetVouchers->EOF && $cnt <= $pg) {
			 $jumlah = 0;

			$bank = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(kod_bank), "Text"));
			$namakp = dlookup ("generalacc", "name", "ID=" . tosql($GetVouchers->fields(companyID), "Text"));
			$nama = $GetVouchers->fields(name);
			$tarikh_PB = toDate("d/m/y",$GetVouchers->fields(tarikh_PB));

			$amaun 		= $GetVouchers->fields(outstandingbalance); 
			$balance 	= $GetVouchers->fields(balance); 
			$bayaran 	= $amaun - $balance;

	print ' <tr>
	<td class="Data" align="center">'.$bil.'</td>';
				
	if ($GetVouchers->fields(g_lockstat) == 1) {
	print '
	<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetVouchers->fields(PBNo)).'">
	'.$GetVouchers->fields(PBNo).'</td>';
}else{
	print '
	<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetVouchers->fields(PBNo)).'">
	<a href="'.$sFileRef.'&action=view&PBNo='.tohtml($GetVouchers->fields(PBNo)).'&yy='.$yy.'&mm='.$mm.'">
	'.$GetVouchers->fields(PBNo).'</td>';						
}
	print'	
	<td class="Data" align="center">'.$tarikh_PB.'</td>
	<td class="Data">'.$namakp.'</td>
	<td class="Data" align="center">'.$GetVouchers->fields(investNo).'</td>
	<td class="Data" align="right">'.number_format($amaun,2).'</td>
	<td class="Data" align="right">'.number_format($bayaran,2).'</td>
	<td class="Data" align="right">'.number_format($balance,2).'</td>
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
			<td class="textFont">Jumlah Voucher : <b>' . $GetVouchers->RowCount() . '</b></td>
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
