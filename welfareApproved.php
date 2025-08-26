<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	welfareUser.php
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

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=welfareUser&mn=6';
$sFileRef  = '?vw=welfareUser&mn=6';
$title     = "Daftar Pengajuan Diluluskan (Aktif)";

$sSQL = "SELECT	a.*,b.* FROM welfares a,general b
		 WHERE a.welfareType = b.ID AND 
		 a.status IN (1) AND a.userID = '" . get_session('Cookie_userID') . "' ORDER BY a.applyDate DESC";
$GetWelfare = &$conn->Execute($sSQL);
$GetWelfare->Move($StartRec - 1);
$TotalRec = $GetWelfare->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td><h5 class="card-title"><i class="typcn typcn-tick-outline"></i>&nbsp;' . strtoupper($title) . '</h5></td></tr>';

if ($GetWelfare->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">                             
					<tr class="table-primary">
					<td nowrap>&nbsp;</td>
					<td nowrap>Nombor Rujukan/Kebajikan</td>
					<td	nowrap align="center">Status</td>
					<td nowrap align="center">Tarikh Mohon</td>
                    <td nowrap align="center">Tarikh Kelulusan</td>
					</tr>';
	while (!$GetWelfare->EOF && $cnt <= $pg) {
		$welfareName = dlookup("general", "name", "ID=" . tosql($GetWelfare->fields(welfareType), "Text"));
		$applyDate =  dlookup("welfares", "applyDate", "ID=" . tosql($GetWelfare->fields(applyDate), "Text"));
		$name = dlookup("users", "name", "userID=" . tosql($GetWelfare->fields(userID), "Text"));

		$status = $GetWelfare->fields(status);


		$colorStatus = "Data";
		if ($status == 0) $colorStatus = "text-success";
		if ($status == 1) $colorStatus = "text-primary";
		if ($status == 9) $colorStatus = "text-primary";
		if ($status == 2) $colorStatus = "text-danger";

		$baucer = $GetWelfare->fields(status);
		if ($baucer == '1' || $baucer == '9') {
			$baucer = toDate("d/m/Y", $GetWelfare->fields(approvedDate));
		} else {
			$baucer = " - ";
		}


		print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetWelfare->fields(ID)) . '">
						' . $GetWelfare->fields(welfareNo) . '-  ' . strtoupper($welfareName) . '</td>';

		if ($status == 9) {
			print '<td class="Data" align="center"><font class="' . $colorStatus . '">' . Selesai . '</font></td>';
		}
		if ($status == 1 || $status == 2) {
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
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
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
