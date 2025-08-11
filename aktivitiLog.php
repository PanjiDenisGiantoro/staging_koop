<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	aktivitiLog.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2  or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=aktivitiLog&mn=901';
$sFileRef  = '?vw=aktivitiLog&mn=901';
$title     = "Log aktiviti";
$sSQL = "";
$sWhere = " ID is not null and (byID > 0 or byID like 'a%') and report <> ''";
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND byID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND activityBy like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND report like '%" . $q . "%'";
	}
}
if ($filter != "") {
	if ($filter == 1) {
		$sWhere .= " AND status = 1";
	} else if ($filter == 2) {
		$sWhere .= " AND status = 2";
	} else if ($filter == 3) {
		$sWhere .= " AND status = 3";
	} else if ($filter == 9) {
		$sWhere .= " AND status = 9";
	}
}
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT * FROM `activitylog`";
$sSQL = $sSQL . $sWhere . " order by activityDate desc";
$GetMember = &$conn->Execute($sSQL);

$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="table-responsive">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
    <tr valign="top" class="headerteal">
	   	<td align="left" >
			Carian melalui 
			<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>ID Anggota</option>';
else print '<option value="2">ID Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Aktiviti</option>';
else print '<option value="3">Aktiviti</option>';
print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;		
		&nbsp;

		</select>
			Filter
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
if ($filter == 0)	print '<option value="0" selected>Semua</option>';
else print '<option value="0">Semua</option>';
if ($filter == 1)	print '<option value="1" selected>Anggota</option>';
else print '<option value="1">Anggota</option>';
if ($filter == 2)	print '<option value="2" selected>Pembiayaan</option>';
else print '<option value="2">Pembiayaan</option>';
if ($filter == 3)	print '<option value="3" selected>Akaun</option>';
else print '<option value="3">Akaun</option>';
if ($filter == 9)	print '<option value="9" selected>Lain-Lain</option>';
else print '<option value="9">Lain-Lain</option>';
print '		</select>
		</td>
	</tr>
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td  class="textFont">&nbsp;</td>					
					<td align="right" class="textFont">Paparan <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
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
print '				</select> setiap mukasurat.
					</td>
				</tr>
			</table>
		</td>
	</tr>';
if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap align="center">Bil</td>
						<td nowrap>Aktiviti</td>
						<td nowrap align="center">Tarikh</td>
						<td nowrap align="center">Masa</td>
						<td nowrap align="center">Nombor anggota</td>
						<td nowrap>Id Pengguna</td>
					</tr>';
	//Full Texts  	ID 	report 	sqlType 	sql 	byID 	activityDate 	activityBy
	while (!$GetMember->EOF && $cnt <= $pg) {
		$status = dlookup("userdetails", "status", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		$dateTime = explode(" ", $GetMember->fields(activityDate));
		$date = $dateTime[0];
		$time = $dateTime[1];

		$dateParts = split("-", $date);
		if ($dateParts[1] == NULL) {
			$dateParts = split("/", $varDate);
		}
		$day = $dateParts[2];
		$month = $dateParts[1];
		$year = $dateParts[0];
		$day = trim($day);
		$month = trim($month);
		$year = trim($year);
		$convertDate = $day . "-" . $month . "-" . $year;

		print ' <tr class="table-light">
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data">' . $GetMember->fields(report) . '</td>
						<td class="Data" align="center">' . $convertDate . '</td>
						<td class="Data" align="center">' . $time . '</td>
						<td class="Data" align="center">' . $GetMember->fields(byID) . '</td>
						<td class="Data">' . $GetMember->fields(activityBy) . '</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetMember->MoveNext();
	}
	print ' </table>
			</td>
		</tr>		
		<tr>
			<td>';
	if ($TotalRec > $pg) {
?>
		<select style="font-size:11px" class="form-select-sm" name="page" onChange="this.form.submit()">
			<?php
			if ($TotalRec % $pg == 0) {
				$numPage = $TotalPage;
			} else {
				$numPage = $TotalPage + 1;
			}
			for ($x = 1; $x <= $numPage; $x++) {
			?>
				<option <?php if ($x == @$page) { ?> selected="selected" <?php } ?> value="<?php echo $x ?>"><?php echo (($x * $pg) - $pg + 1) . '-' . ($x * $pg); ?></option>
			<?php
			}
			?>
		</select>
<?php
		print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
		if ($TotalRec % $pg == 0) {
			$numPage = $TotalPage;
		} else {
			$numPage = $TotalPage + 1;
		}
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '&filter=' . $filter . '">';
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
			<td class="textFont">Jumlah Rekod : <b>' . $GetMember->RowCount() . '</b></td>
		</tr>';
	$GetMember->Close();
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' </table></td></tr></table></div></form>';
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
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="memberStatus.php?pk=" + strStatus;
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
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
</script>';
?>