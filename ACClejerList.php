<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACClejerList.php
 *          Date 		: 	
 *********************************************************************************/
session_start();
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg  	  = 500;
if (!isset($q))			$q   	  = "";
if (!isset($by))		$by  	  = "1";
if (!isset($dept))		$dept	  = "";
if (!isset($mth))		$mth 	  = date("n");
if (!isset($yr)) 		$yr		  = date("Y");
if (!isset($mm))		$mm 	  = date("m");
if (!isset($yy))		$yy		  = date("Y");

$yrmth = sprintf("%04d%02d", $yy, $mm);

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$updatedDate = date("Y-m-d H:i:s");
$sFileName = "?vw=ACClejerList&mn=$mn";
// $sFileRef  = "?vw=ACClejerListEdit&mn=$mn";
$sFileRef  = "ACClejerListEdit.php?";
$title     = "Pengurusan Carta Akaun (Balance Sheet)";
$IDName = get_session("Cookie_userName");

$sSQL = "";
$sWhere = " a_KodKump NOT IN (0,36) AND ID NOT IN (8,10,11,12,13,885) ";

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND name like '%" . $q . "%'";
	}
}
$sWhere = " WHERE (" . $sWhere . " AND name != BINARY UPPER(name))";
$sSQL = "SELECT	DISTINCT * FROM generalacc";
$sSQL = $sSQL . $sWhere . " order by CAST( code AS SIGNED INTEGER ) ASC";
$GetMember = &$conn->Execute($sSQL);

$GetMember->Move($StartRec - 1);
$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="ID" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
   
                    Carian Melalui 
                    <select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nama Carta Akaun</option>';
else print '<option value="1">Nama Carta Akaun</option>';
print '</select>
            <input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm">
                    <input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;</div>                        
                        
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';


print '    <tr valign="top" class="textFont">
	   	<td align="left">
			<table>
				<tr>
				<td class="textFont">Pilihan Bulan & Tahun</td>
				<td class="textFont"><i class="mdi mdi-arrow-right"></i> &nbsp;
	Bulan   
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">';
if ($mm == "ALL") print 'selected';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '</select>
						Tahun 
						<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
for ($j = 2013; $j <= 2030; $j++) {
	print '	<option value="' . $j . '"';
	if ($yy == $j) print 'selected';
	print '>' . $j;
}
print '</select></td>
				</tr>
			</table>
		</td>
	</tr>';

print '	
<tr valign="top" class="textFont">
	<td>
		<table width="100%">
		<tr>
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
if ($pg == 500)	print '<option value="500" selected>500</option>';
else print '<option value="500">500</option>';
print '				</select> setiap mukasurat.
				</td>
			</tr>
		</table>
	</td>
</tr>';
if ($GetMember->RowCount() <> 0) {
	$cnt = 1;
	$groupedRecords = array();

	while (!$GetMember->EOF) {
		$core = $GetMember->fields['coreID'];
		if ($core == 348) {
			$header = "ASSET BUKAN SEMASA";
		} else if ($core == 379) {
			$header = "ASSET SEMASA";
		} elseif ($core == 500) {
			$header = "LIABILITI BUKAN SEMASA";
		} elseif ($core == 508) {
			$header = "LIABILITI SEMASA";
		} else if ($core == 10) {
			$header = "EKUITI";
		}
		// if ($core == 348 || $core == 379) {
		// 	$header = "ASSET";
		// } elseif ($core == 500 || $core == 508) {
		// 	$header = "LIABILITI";
		// } else {
		// 	$header = "EKUITI";
		// }
		if (!isset($groupedRecords[$header])) {
			$groupedRecords[$header] = array();
		}

		array_push($groupedRecords[$header], $GetMember->fields);

		$GetMember->MoveNext();
	}

	foreach ($groupedRecords as $header => $records) {

		// Reset totals for the new group
		$totaldebit = 0;
		$totalkredit = 0;

		$bil = $StartRec;

		print '<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">';

		print '<tr><td style="font-size: 14pt; border-top: 1px solid #c2d6d0; background-color: #fbfbfb;" colspan=1></td><td style="font-size: 14pt; border-top: 1px solid #c2d6d0; background-color: #fbfbfb;" colspan="3" align="left"><b><u>' . $header . '</u></b></td>';

		print ' 	<tr class="table-primary">
						<td nowrap align="center">&nbsp;</td>
						<td nowrap align="left">Kod Akaun - Nama</td>
						<td nowrap align="right">Debit (RM)</td>
						<td nowrap align="right">Kredit (RM)</td>
					</tr>';

		foreach ($records as $record) {

			$Kodkump 	= dlookup("generalacc", "name", "ID=" . $record['a_Kodkump']);
			$rsOpn 		= getListOpenAccount($record['ID'], $yrmth);
			$addminus 	= $rsOpn->fields('addminus');
			print '<tr>';
			print '<td class="Data" align="center">' . $bil . '&nbsp;</td>';
			print '<td class="Data"><a href=# onClick="window.open(\'' . $sFileRef . '&ID=' . tohtml($record['ID']) . '&yrmth=' . $yrmth . '\', \'NewWindow\', \'top=100,left=200,height=500,width=1200\'); return false;")">' . $record['code'] . ' - ' . $record['name'] . '</a>';

			// print'
			// 	<td class="Data" align="center">&nbsp;'.$Kodkump.'</td>';

			if ($addminus == 0) {
				$amaund = $rsOpn->fields('pymtAmt');
				print '<td align="right">' . number_format($amaund, 2) . '</td>';
				print '<td align="right">0.00</td>';
				$totaldebit += $amaund;
			} elseif ($addminus == 1) {
				$amaunk = $rsOpn->fields('pymtAmt');
				print '<td align="right">0.00</td>';
				print '<td align="right">' . number_format($amaunk, 2) . '</td>';
				$totalkredit += $amaunk;
			}
			print '</tr>';

			$bil++;
		}

		$totaldebitAll += $totaldebit;
		$totalkreditAll += $totalkredit;

		print '<tr><td colspan="4"></td>';
		print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">';
		print '<tr><td colspan="2" align="right"><b>JUMLAH (RM) </b></td>';
		print '<td align="right">&nbsp;' . number_format($totaldebit, 2) . '</td>';
		print '<td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td></tr>';
	}
	print '<tr><td colspan="3"></td>';
	print ' 	<tr class="table-primary">
					<td nowrap align="center">&nbsp;</td>
					<td nowrap align="left"></td>
					<td nowrap align="right">Debit (RM)</td>
					<td nowrap align="right">Kredit (RM)</td>
		</tr>';
	print '<tr><td colspan="2" align="right"><b>JUMLAH KESELURUHAN (RM) </b></td>';
	print '<td align="right">&nbsp;' . number_format($totaldebitAll, 2) . '</td>';
	print '<td align="right">&nbsp;' . number_format($totalkreditAll, 2) . '</td></tr>';

	$debTkre = ($totaldebitAll - $totalkreditAll);
	print '<tr><td colspan="2" align="right"><b>BAKI (RM) </b></td>';
	print '	<td width="5%"></td>';
	print '	<td width="5%" align="right">' . number_format($debTkre, 2) . '</td></tr>';

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
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
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
			<td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
		</tr>
		<tr>
			<td><input type="button" class="btn btn-sm btn-secondary" value="Cetak" onclick="window.print();"></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table></td></tr></table>
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
		document.location = "' . $sFileName . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
</script>';
