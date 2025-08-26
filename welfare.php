<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	welfare.php
 *          Date 		: 	
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($yr)) $yr	= date("Y");
if (!isset($yy)) $yy	= date("Y");
if (!isset($mm)) $mm	= date("m");
if (!isset($filter))	$filter = "ALL";
if (!isset($dept))		$dept = "";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=welfare&mn=$mn";
$sFileRef  = "?vw=welfareEdit&mn=$mn";
$title     = "Daftar Pengajuan Bantuan Kebajikan";

$IDName = get_session("Cookie_userName");

//--- End -------------------------------------------------------

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action	== "delete") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckWelfare = ctWelfare("", $pk[$i]);
		if ($CheckWelfare->RowCount() == 1) {
			if ($CheckWelfare->fields(status) == 0) {
				$sWhere	= "ID="	. tosql($pk[$i], "Number");
				$sSQL =	"DELETE	FROM welfares WHERE " . $sWhere;
				$rs	= &$conn->Execute($sSQL);
			} else {
				print '<script>alert("Hanya permohonan dalam proses yang boleh dihapus!");</script>';
			}
		}
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

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
$sSQL = "";
$sWhere = " a.userID ";


if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . $dept;
	$sWhere .= " AND A.userID = B.userID";
}

if ($filter <> "ALL") $sWhere .= "  AND a.status = " . $filter;

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND a.userID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND b.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}
$sWhere = " WHERE (" . $sWhere . ") ";
$sSQL = "SELECT a.* FROM welfares a";
if ($q <> "") {
	if ($by == 2) {
		$sSQL .= " JOIN users b ON a.userID=b.userID ";
	} else if ($by == 3) {
		$sSQL .= " JOIN userdetails b ON a.userID=b.userID ";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM welfares A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM welfares A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY a.applyDate DESC';

$GetWelfare = &$conn->Execute($sSQL);
$GetWelfare->Move($StartRec - 1);

$TotalRec = $GetWelfare->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
  
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=welfareApply&mn=921\'"/>
</div>


<div class="mb-3 row m-1">
<div>Cari Berdasarkan 
			<select name="by" class="form-select-sm mt-3">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';
print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm mt-3">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;		
			Cabang/Zona
			<select name="dept" class="form-select-sm mt-3" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '		</select>&nbsp;</div>
</div>

<div class="mb-3 row m-1">
<div>
Jenis
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
print '<option value="ALL">Semua';
for ($i = 0; $i < count($bajikanList); $i++) {
	if ($bajikanVal[$i] < 6) {
		print '	<option value="' . $bajikanVal[$i] . '" ';
		if ($filter == $bajikanVal[$i]) print ' selected';
		print '>' . $bajikanList[$i];
	}
}
// }
print '	</select>&nbsp;';

if (($IDName == 'superadmin') or ($IDName == 'admin')) {

	if ($filter == 0) print '<input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}
print '&nbsp;<input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">';


print '
<div class="table-responsive">    
<!--table border="1" cellspacing="1" cellpadding="3" width="100%" align="center" class="table"-->
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td align="right" class="textFont">Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
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
print '				</select> setiap halaman..
					</td>
				</tr>
						</table>
		</td>
	</tr>';

if ($GetWelfare->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nomor Rujukan - Bantuan Sosial</td>
						<td nowrap align="left">Nomor - Nama Anggota</td>
						<td nowrap align="center">Kartu Identitas</td>
						<!--td nowrap align="center">Dokumen</td-->
						<td	nowrap align="center">Status</td>
						<td nowrap align="center">Tarikh Mohon</td>
						<td nowrap align="center">Tarikh Kelulusan</td>
					</tr>';
	while (!$GetWelfare->EOF && $cnt <= $pg) {
		// $welfareNo = dlookup("userdetails", "status", "userID=" . tosql($GetWelfare->fields(userID), "Text"));


		$welfareName = dlookup("general", "name", "ID=" . tosql($GetWelfare->fields(welfareType), "Text"));
		$applyDate =  dlookup("welfares", "applyDate", "ID=" . tosql($GetWelfare->fields(applyDate), "Text"));
		$name = dlookup("users", "name", "userID=" . tosql($GetWelfare->fields(userID), "Text"));

		$status = $GetWelfare->fields(status);
		$colorStatus = "Data";
		if ($status == 0) $colorStatus = "text-success";
		if ($status == 1) $colorStatus = "text-primary";
		if ($status == 2) $colorStatus = "text-danger";

		$baucer = $GetWelfare->fields(status);
		if ($baucer == '1' || $baucer == '9') {
			$baucer = toDate("d/m/Y", $GetWelfare->fields(approvedDate));
		} else {
			$baucer = "-";
		}

		print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetWelfare->fields(ID)) . '">
						' . $GetWelfare->fields(welfareNo) . ' - ' . strtoupper($welfareName) . '</td>';

		print '
			<td	class="Data">' . dlookup("userdetails",	"memberID",	"userID=" .	tosql($GetWelfare->fields(userID),	"Text")) . ' - ' . dlookup("users", "name", "userID="	. tosql($GetWelfare->fields(userID), "Text")) . '</td>
													
			
			<td	class="Data" align="center">' . dlookup("userdetails", "newIC",	"userID=" .	tosql($GetWelfare->fields(userID),	"Text")) . '&nbsp;
			</td>';

		// if (dlookup("welfares", "welfare_img", "ID=" .	tosql($GetWelfare->fields(ID),	"Text")) == NULL) {
		// 	print '<td class="Data">&nbsp;</td>';
		// } else {
		// 	print '<td class="Data" align="center"><input type="button" class="btn btn-sm btn-outline-danger" value="Dokumen" onClick=window.open(\'upload_welfare/' . dlookup("welfares", "welfare_img", "ID=" .	tosql($GetWelfare->fields(ID),	"Text")) . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");><br/></td>';
		// }

		if ($status == 9) {
			print '<td class="Data" align="center"><font class="' . $colorStatus . '">Selesai</font></td>';
		} else {
			print '<td class="Data" align="center"><font class="' . $colorStatus . '">' . $bajikanList[$status] . '</font></td>';
		}
		print '
                    

					<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetWelfare->fields(applyDate)) . '</td>
 
				    <td class="Data" align="center">&nbsp;' . $baucer . '</td>
						
					</tr>';
		$cnt++;
		$bil++;
		$GetWelfare->MoveNext();
	}

	$GetWelfare->Close();
	print ' </table>
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
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '&filter=' . $filter . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>' . $GetWelfare->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table></td></tr></table></div>
</form>';



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
			alert(\'Sila pilih rekod yang	hendak di\'	+ v	+ \'kan.\');
		  } else {
			if(confirm(count + \'	rekod hendak di\' +	v + \'kan. Adakah anda pasti?\')) {
			  e.action.value = v;
			  e.submit();
			}
		  }
		}
	  }
		
	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				pk = e.elements[c].value;
				strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	       if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk proses\');
			} else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          window.location.href ="?vw=welfareStatus&pk=" + strStatus;
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
				window.location.href = "welfareStatus.php?pk=" + pk;
			}
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	function ITRActionButtonFinish(v) {
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
			alert(\'Sila pilih rekod yang hendak diselesaikan.\');
		  } else {
			if(confirm(count + \' rekod hendak diselesaikan?\')) {
			  e.action.value = v;
			  e.submit();
			}
		  }
		}
	  }	 

	  

</script>';
include("footer.php");
