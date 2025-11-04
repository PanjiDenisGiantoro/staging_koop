<?php
/***************************
*          Project		:	iKOOP.com.my
*          Filename		: 	productServiceList.php
*          Date 		: 	16/7/2024
***************************/

if (!isset($mm))    $mm="ALL";
if (!isset($yy))    $yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta"); 

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($code))		$code="ALL";
if (!isset($filter))	$filter="0";

include("header.php");	
include("koperasiQry.php");	

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';//dari mana file ni
}

$sFileName = "?vw=productServiceList&mn=$mn";//file name
$sFileRef  = "?vw=product&mn=$mn";// file ni pergi mane
$title     =  "Produk & Service";//Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "name=" . tosql($pk[$i], "Text");
		$sSQL = "DELETE FROM stok WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
	
		$rs = &$conn->Execute($sSQL);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sSQL = "";
	$sWhere = "";

	// if ($q <> "") 	{
	// 	if ($by == 1) {
	// 		$sWhere .= " AND name like '%".$q."%'";
	// 	}
	// }

	// // $sWhere = " WHERE (".$sWhere.")";

	
	// if ($q <> "") 	{
	// 	if ($by == 1) {
	// 		$sSQL = "SELECT * FROM stok";
	// 	}
	// } else {
	// 	$sSQL = "SELECT * FROM stok ";
	// }
	//if($mm <> "ALL") $sWhere .= " AND month( A.createdDate ) =" .$mm;
	// $sSQL .= $sWhere . ' ORDER BY name DESC';
	$sSQL = 'SELECT * FROM stok ORDER BY name DESC';

	echo $sSQL;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$GetBaucers = &$conn->Execute($sSQL);

if (!$GetBaucers) {
    die('Error executing SQL query: ' . $conn->ErrorMsg());
}
$GetBaucers->Move($StartRec-1);

$TotalRec = $GetBaucers->RowCount();
$TotalPage =  ($TotalRec/$pg);


print '<div class="table-responsive">
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="'.$code.'">
<input type="hidden" name="filter" value="'.$filter.'">
<h5 class="card-title">'.strtoupper($title).' &nbsp;</h5>
<div clas="row">
		
<div clas="row">
    Cari Berdasarkan
				<select name="by" class="form-select-sm">'; 
if ($by == 1)	print '<option value="1" selected>Nama </option>'; else print '<option value="1">Nama</option>';			
				
				
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


	<br><br>	<input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\''.$sFileRef.'&action=new&jenis='.$jenis.'\';">';
if (($IDName == 'admin') OR ($IDName == 'superadmin')){

print'&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
print'&nbsp; <input type="button" class="btn btn-sm btn-warning" value="NyahAktif" onClick="ITRActionButtonDeactivate(\'active\');">';
} 

print'
      </div>
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">

    <tr valign="top" class="Header">
	   	<td align="left" >
	 </td>
	</tr>';
	if ($GetBaucers->RowCount() <> 0) {  
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
						<td nowrap><b>Nama</b></td>
						<td nowrap align="center"><b>SKU</b></td>
						<td nowrap><b>Akaun Pendapatan</b></td>
						<td nowrap><b>Catatan</b></td>
						<td nowrap><b>Kumpulan</b></td>
						<td nowrap><b>Klasifikasi</b></td>
						<td nowrap align="center"><b>Harga Jual(RP)</b></td>
						<td nowrap><b>Akaun Jualan</b></td>
						<td nowrap align="right"><b>Harga Beli (RP)</b></td>
						<td nowrap><b>Kuantiti</b></td>
					</tr>';	
		$DRTotal = 0;
		$CRTotal = 0;
		while (!$GetBaucers->EOF && $cnt <= $pg) {
			 $jumlah = 0;						

			// $namacomp = dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields(companyID), "Text"));
			// $nama = dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields(batchNo), "Text"));
			$name = $GetBaucers->fields(name);
			$sku = $GetBaucers->fields(sku);
			$quantity = $GetBaucers->fields(quantity);
			$s_price = $GetBaucers->fields(s_price);
			$b_price = $GetBaucers->fields(b_price);

			// $tarikh_inv = toDate("d/m/y",$GetBaucers->fields(tarikh_inv));

		// $sSQL2 = "SELECT g_lockstat FROM generalacc WHERE ID = ".$GetBaucers->fields(batchNo)." ORDER BY ID";
		// $rsDetail =&$conn->Execute($sSQL2);

		print ' <tr><td class="Data" align="center">'.$bil.'</td>';
		
	// 	if ($rsDetail->fields(g_lockstat) == 1) {
	// 	print '
	// 	<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetBaucers->fields(name)).'">
	// 	'.$GetBaucers->fields(name).'</td>';
	// }else{
		print '
		<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetBaucers->fields(name)).'">
		<a href="'.$sFileRef.'&action=view&name='.tohtml($GetBaucers->fields(name)).'">
		'.$GetBaucers->fields(name).'</td>';
	// }
		print'
		<td class="Data" align="center">'.$sku.'</td>
		<td class="Data" align="center"></td>
		<td class="Data" align="center"></td>
		<td class="Data" align="center"></td>
		<td class="Data" align="center"></td>
		<td class="Data" align="center"></td>
		<td class="Data" align="center">'.$s_price.'</td>
		<td class="Data" align="center">'.$b_price.'</td>
		<td class="Data" align="center">'.$quantity.'</td>
		</tr>';
		$cnt++;
		$bil++;
		$GetBaucers->MoveNext();
	}
		$GetBaucers->Close();

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
			<td class="textFont">Jumlah Data : <b>'.$GetBaucers->RowCount().'</b></td>
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
    function ITRActionButtonDeactivate(v) {
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
        alert(\'Sila pilih rekod yang hendak dinyahaktifkan.\');
    } else {
        if(confirm(count + \' rekod hendak dinyahaktifkan?\')) {
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
		document.location = "' . $sFileName . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
</script>';
?>