<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	ACClaporanBAList.php
*          Date 		: 	17/4/2020
*********************************************************************************/
if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";
if (!isset($mth)) 		$mth=date("n");                 		
if (!isset($yr)) 		$yr=date("Y");
if (!isset($mm))		$mm=date("m");
if (!isset($yy))		$yy=date("Y");

include("header.php");	
include("koperasiQry.php");	
include("koperasiList.php");
date_default_timezone_set("Asia/Jakarta");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 0 AND get_session("Cookie_groupID") <> 1 
AND get_session("Cookie_groupID") <> 2 
OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=ACClaporanPembekal&mn=$mn"; 
$sFileRef  = "?vw=ACClaporanPembekal&mn=$mn";
$title     = "Senarai Penyata Invoice Pemiutang";

if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}


$sSQL = "";
$sWhere = " a.ID = b.companyID AND a.category='AB'";

if ($q <> "") 	{
	if ($by == 1) {
		$sWhere .= " AND a.code like '%" .$q ."%'";			
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q. "%'";
	} 
}
if($ID) {
	$sWhere .= " AND a.ID = " . tosql($ID,"Text");
}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.* FROM generalacc a, cb_purchase b";
	$sSQL = $sSQL . $sWhere . " order by CAST(a.ID AS SIGNED INTEGER) ";
	$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec-1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec/$pg);

print 'div class="table-responsive">
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="'.$StartRec.'">
<input type="hidden" name="by" value="'.$by.'">
<h5 class="card-title">'.strtoupper($title).' &nbsp;</h5>';

if (get_session("Cookie_groupID") > 0) {
echo '<div clas="row" style="background-color: #efefef;padding:6px;border-radius: 0.15rem;border: 1px solid #e4e4e4;">
    Carian melalui <select name="by" class="form-select-sm">'; 
if ($by == 1)	print '<option value="1" selected>No Syarikat</option>'; 	else print '<option value="1">No Syarikat</option>';				
if ($by == 2)	print '<option value="2" selected>Nama Syarikat</option>'; 	else print '<option value="2">Nama Syarikat</option>';				
print '</select>
		<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
 		<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp';		
	

print '</select>';

echo '</div> 
    <div clas="row" style="background-color: #efefef;padding:6px;border-radius: 0.15rem;border: 1px solid #e4e4e4;">
    Pilihan Bulan/Tahun
    :&nbsp;
	Bulan   : 
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
			<option value="ALL"';
				if ($mm == "ALL") print 'selected';
				for ($j = 1; $j < 13; $j++) {
				print '	<option value="'.$j.'"';
				if ($mm == $j) print 'selected';
					print '>'.$j;
						}
				print '</select>
				Tahun  : 
				<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
				for ($j = 1989; $j <= 2079; $j++) {
					print '	<option value="'.$j.'"';
					if ($yy == $j) print 'selected';
					print '>'.$j;
				}
				print '</select>
    </div>';
}
echo '<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';

if (get_session("Cookie_groupID") > 0) {

print'<tr valign="top" class="textFont"><td align="left"><table>

				   
				<tr>
					<td class="textFont">Penyata Syarikat</td>
					<td class="textFont">:&nbsp; 
			        <input type="button" class="btn btn-sm btn-secondary" value="Bulanan" onClick="ITRActionButtonClick(\'ACCinvoisMonth\');" style="width:100px;">
					<input type="button" class="btn btn-sm btn-secondary" value="Tahunan" onClick="ITRActionButtonClick(\'ACCinvoisYear\');" style="width:100px;">
					<input type="button" class="btn btn-sm btn-secondary" value="Keseluruhan" onClick="ITRActionButtonClick(\'ACCinvoisAll\');" style="width:100px;">            
					</td>
				</tr>
			</table>
		</td>
	</tr>';
if ($q == "" AND $dept == "ALL") {
	print '		
	<tr><td	class="Label" align="center" height=50 valign=middle>
		<hr size="1"><b>- Sila masukkan No / Nama Anggota ATAU pilih Jabatan  -</b><hr size="1">
	</td></tr>';
} else {					
	if ($GetMember->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;

		//if (get_session("Cookie_groupID") > 0) { //just for staf
		print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">&nbsp;</td>
						<td align="right" class="textFont">';
                                                                                            echo papar_ms($pg);
                                                                    print '</td>
					</tr>
				</table>
			</td>
		</tr>';
		//}

print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="header">
						<td nowrap colspan="3" height="20" align="center">&nbsp;Simpanan</td>
					</tr>
					<tr class="table-success">
						<td nowrap rowspan="1" height="20">&nbsp;</td>
						<td nowrap>&nbsp;No/Nama Syarikat</td>
						<td nowrap align="center">&nbsp;Jumlah</td>
					</tr>';	
		$totalFee = 0;
		$totalShare = 0;					
		while (!$GetMember->EOF && $cnt <= $pg) {
			$totalFees = number_format(getFeess($GetMember->fields(ID), $yr),2);

			print ' <tr>
			<td class="Data" align="right">' . $bil . '&nbsp;</td>

			<td class="Data">
				<input type="checkbox" name="pk[]" class="form-check-input" value="'.tohtml($GetMember->fields(ID)).' " '.$objchk.'>
					<a href="'.$sFileRef.'&ID='.tohtml($GetMember->fields(ID)).'">'.$GetMember->fields(code).' - '.$GetMember->fields(name).'</a>
			</td>
	
			<td class="Data" align="right">'.$totalFees.'&nbsp;</td>
			
			</tr>';
			$cnt++;
			$bil++;
			//$totalFee += $GetMember->fields('totalFee');
			//$totalShare += $GetMember->fields('totalShare');				
			$GetMember->MoveNext();
		}
		$GetMember->Close();

		print '</table></td></tr><tr><td>';
				if ($TotalRec > $pg) {
					print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
					if ($TotalRec % $pg == 0) {
						$numPage = $TotalPage;
					} else {
						$numPage = $TotalPage + 1;
					}
					print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
					for ($i=1; $i <= $numPage; $i++) {
						print '<A href="'.$sFileName.'&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.$q.'&by='.$by.'&dept='.$dept.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a> &nbsp; &nbsp;';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
		</tr-->';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk '.$title.'  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")

}else{
	
print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'ACCinvoisMonth\')">Penyata Yuran Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'ACCinvoisYear\')">Penyata Yuran Tahunan</a>
		</td>
	</tr>
    ';


}
print ' 
</table>
</form></div>';

include("footer.php");	

print '
<script language="JavaScript">
	function ITRActionButtonClick(rpt) {
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
				alert(\'Sila pilih satu anggota sahaja \');
			} else {
				if (rpt == "memberMonthly" )  {
					url = "memberMonthly.php?yrmth='.$yy.$mm.'&id=" + pk;
				} else if (rpt == "memberYearly" )  {
					url = "memberYearly.php?yr='.$yy.'&id=" + pk;
				} else if (rpt == "memberLoan" )  {
					url = "memberLoan.php?pk=" + pk;
				} else if (rpt == "loanUserYearly" )  {
					url = "loanUserYearly.php?pk="+ pk +"&yr='.$yy.'";
				} else if (rpt == "shareMonthly" )  {
					url = "shareMonthly.php?yrmth='.$yy.$mm.'&id=" + pk;
				} else if (rpt == "shareYearly" )  {
					url = "shareYearly.php?yr='.$yy.'&id=" + pk;
				} else if (rpt == "ACCinvoisMonth" )  {
					url = "ACCinvoisMonth.php?yrmth='.$yy.$mm.'&id=" + pk;
				} else if (rpt == "ACCinvoisYear" )  {
					url = "ACCinvoisYear.php?yr='.$yy.'&id=" + pk;
				}else if (rpt == "memberPenyataYearly" )  {
					url = "memberPenyataYearly.php?pk="+ pk +"&yr='.$yy.'&id=" + pk;
				} else if (rpt == "ACCinvoisAll" )  {
					url = "ACCinvoisAll.php?yr='.$yy.'&id=" + pk;
				}
				
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			}
		}
	}

	function selectPenyata(rpt) {
		if (rpt == "ACCinvoisMonth" || rpt == "shareMonthly" || rpt == "memberMonthly") {
			url = "selMthYear.php?rpt="+rpt+"&id='.$ID.'";
		} else if (rpt == "rptG2Dept") {
			url = "selYear.php?rpt="+rpt+"&id='.$ID.'";
		} else {
			url = "selYear.php?rpt="+rpt+"&id='.$ID.'";
		}
		
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "'.$sFileName.'&StartRec=1&pg=" + c.options[c.selectedIndex].value + "&dept='.$dept.'";
	}

	function selectPop(rpt) {
		if (rpt == "greImportBul") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  

	function ITRActionButtonClick_old(rpt) {
		if (rpt == "BulananU") {
			url = "memberMonthly.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		} else if (rpt == "TahunanU") {
			url = "memberYearly.php?yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "SenaraiP") {
			url = "memberLoan.php?pk='.$pk[0].'";
		} else if (rpt == "TahunanP") {
			url = "loanUserYearly.php?pk='.$pk[0].'&yr='.$yrS.'";
		} else if (rpt == "BulananS") {
			url = "shareMonthly.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		} else if (rpt == "TahunanS") {
			url = "shareYearly.php?yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "TahunanY") {
			url = "ACCinvoisYear.php?yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "PenyataTahunan") {
			url = "memberPenyataYearly.php?pk='.$pk[0].'&yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "Import") {
			url = "greImportPot.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		} else if (rpt == "Eksport") {
			url = "greEksportPot.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		}
		window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
	}
</script>';

?>