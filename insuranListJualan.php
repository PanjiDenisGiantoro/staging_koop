<?php

/*********************************************************************************
 *		   Project		:	iKOOP.com.my
 *		   Filename		:	insuranListJualan
 *		   Date			:	05/05/2016
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($filter))	$filter = "ALL";
if (!isset($dept))		$dept = "";
if (!isset($mm))	$mm = date("n");
if (!isset($yy))	$yy = date("Y");

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <>	2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = "?vw=insuranListJualan&mn=$mn";
$sFileRef  = "?vw=insuranListJualan&mn=$mn";
$sFileRefRenew  = "?vw=insuranListJualan&mn=$mn";
$title	   = "Jualan Insuran Kenderaan";

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
$status = $filter;

if ($mm != "" && $yy != "") {

	$sSQL = "";
	$sSQL = "SELECT * FROM 	insurankenderaan ";

	if ($q <> "") {
		$sSQL = $sSQL . ' where status = 1 ORDER BY applyDate DESC';
	} else if ($mm == "ALL") {
		$sSQL = $sSQL . ' where status =1 and year( Tkh_Mula ) = " ' . $yy . '" ORDER BY applyDate DESC';
	} else {
		$sSQL = $sSQL . ' where status =1 and year( Tkh_Mula ) = " ' . $yy . '" and   month( Tkh_Mula ) = " ' . $mm . '" ORDER BY applyDate DESC';
	};



	$GetListIns = &$conn->Execute($sSQL);
	$GetListIns->Move($StartRec - 1);

	$TotalRec =	$GetListIns->RowCount();
	$TotalPage =  ($TotalRec / $pg);
}

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
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
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;';

if ($filter == 3) print '&nbsp;&nbsp;Cetak dokumen proses :&nbsp;<input type="button" class="btn btn-sm btn-secondary" value="Cetak" onClick="ITRActionButtonDoc();">&nbsp;';

if ($filter	== 4) print 'Ubah ke proses kembali &nbsp;<input type="button" class="btn btn-sm btn-primary" value="Ubah"	onClick="ITRActionButtonUbah();">';

print '</td>
					<td	align="right" class="textFont">

					<!--input 4ype="button" class="btn btn-secondary" value="Status" onClick="ITBActionButtonStatus();"-->';
echo papar_ms($pg);
print '</td>
				</tr>';
if (get_session("Cookie_groupID") == 2 && $filter == 3) {
	print '<tr>
			<td	 class="textFont" align ="left">Batal Kelulusan :&nbsp;<input type="button" class="btn btn-danger" value="Batal" onClick="ITRActionButtonClick(\'batal\');">&nbsp;Sebab:&nbsp;<input type="text" name="sebab" value="" maxlength="60" size="50" class="Data"></td>
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
						<td	nowrap><b>Bil</b></td>											
						<td	width="20" nowrap><b>Cover Note (C.N)</b></td>					
						<td width="20" nowrap><b>Tarikh Keluar (C.N)</b></td>
						<td	nowrap><b>Nama Peserta</b></td>
						<td	nowrap><b>Nombor Pendafaran</b></td>
						<td	nowrap align="right"><b>Jumlah Premium (RP)</b></td>
					    <td	nowrap align="center"><b>Stamp Duty</b></td>						
						<td	nowrap align="center"><b>Premium<br>Tolak Stamp Duty</b></td>
						<td	nowrap align="center"><b>Komisen 10%</b></td>
						<td	nowrap align="right"><b>Jumlah Premium (RP)<br>- Tolak Komisen</b></td>
						<td	nowrap align="right"><b>Jumlah Bersih (RP)<br>+ Stamp Duty</b></td>						
						<td	nowrap align="right"><b>Jumlah (RP)<br>Diskaun Diberi</b></td>
						<td	nowrap align="center"><b>Bayaran Ke <br>Takaful Ikhlas</b></td>
						<td	nowrap align="center"><b>Jumlah<br>Bersih Komisen</b></td>
					</tr>
						<tr	class="primary">
						<td	nowrap>&nbsp;</td>											
						<td	nowrap></td>					
						<td	nowrap></td>
						<td	nowrap></td>
						<td	nowrap></td>
						<td	nowrap align="center">A</td>
					    <td	nowrap align="center">B</td>						
						<td	nowrap align="center">C=A-B</td>
						<td	nowrap align="center">D=C*10%</td>
						<td	nowrap align="center">E=C-D</td>
						<td	nowrap align="center">F=E+B</td>						
						<td	nowrap align="center">G</td>
						<td	nowrap align="center">H=F</td>
						<td	nowrap align="center">I=D-G</td>
					</tr>';
	$amtLoan = 0;
	while (!$GetListIns->EOF && $cnt <= $pg) {
		$insuranID2 = $GetListIns->fields(ID);
		$insuranID = $GetListIns->fields(ID);
		$yearcover = $GetListIns->fields(insuranYear);
		$noruj = $GetListIns->fields(insuranNo);
		$nama = $GetListIns->fields(Nama);
		$nokp = $GetListIns->fields(NoKP);
		$Jum_Pre_Kasar = $GetListIns->fields(Jum_Pre_Kasar);
		$Jum_Pre_Bersih = $GetListIns->fields(Jum_Pre_Bersih);
		$A = $GetListIns->fields(JumlahPremium);
		$Jum_A = $Jum_A + $A;
		$B = 10.00;
		$Jum_B = $Jum_B + $B;
		$C = $A - $B;
		$Jum_C = $Jum_C + $C;
		$D = $C * 0.10;
		$Jum_D = $Jum_D + $D;
		$E = $C - $D;
		$Jum_E = $Jum_E + $E;
		$F = $E + $B;
		$Jum_F = $Jum_F + $F;
		$G = $C * 0.05;
		$Jum_G = $Jum_G + $G;
		$H = $F;
		$Jum_H = $Jum_H + $H;
		$I = $D - $G;
		$Jum_I = $Jum_I + $I;
		$Cover_Note = $GetListIns->fields(Cover_Note);
		$Tkh_Mula = toDate("d/m/yy", $GetListIns->fields(Tkh_Mula));
		$nokenderaan = $GetListIns->fields(NoKenderaan);
		$status = $GetListIns->fields(Status);

		print '	<tr>
						<td	class="Data" align="center">' . $bil	. '</td>
					    <td	class="Data">' . $Cover_Note . '</td>
					    <td	class="Data" align="center">' . $Tkh_Mula . '</td>						
						<td	class="Data" align="left">' . $nama . '</td>
						<td	class="Data" align="center">' . $nokenderaan . '</td>	
						<td	class="Data">' . $A . '</td>
						<td	class="Data" align="center">' . $B . '</td>
						<td	class="Data" align="center">' . $C . '</td>	
						<td	class="Data" align="center">' . $D . '</td>						
						<td	class="Data" align="right">' . $E . '</td>
						<td	class="Data" align="right">' . $F . '</td>
						<td	class="Data" align="right">' . $G . '</td>
						<td	class="Data" align="center">' . $H . '</td>	
						<td	class="Data" align="center">' . $I . '</td>							
						</tr>';
		$cnt++;
		$bil++;
		$GetListIns->MoveNext();
	}
	$GetListIns->Close();
	print '	<tr>
						<td	class="Data" colspan="5"></td>	
						<td	class="Data"><b>' . $Jum_A . '</b></td>
						<td	class="Data"><b>' . $Jum_B . '</b></td>
						<td	class="Data"><b>' . $Jum_C . '</b></td>	
						<td	class="Data"><b>' . $Jum_D . '</b></td>						
						<td	class="Data"><b>' . $Jum_E . '</b></td>
						<td	class="Data"><b>' . $Jum_F . '</b></td>
						<td	class="Data"><b>' . $Jum_G . '</b></td>
						<td	class="Data"><b>' . $Jum_H . '</b></td>	
						<td	class="Data"><b>' . $Jum_I . '</b></td>							
						</tr>
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
