<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	member.php
*          Date 		: 	
*********************************************************************************/
if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 10;
if (!isset($q))			$q="";
if (!isset($by))		$by="0";
if (!isset($filter))	$filter="ALL";
if (!isset($dept))		$dept="";

include("header.php");
include("koperasiQry.php"); 
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCinvestors&mn=$mn";
$sFileRef  = "?vw=ACCinvestors_detail&mn=$mn";
$title	   = "Senarai Syarikat Pelabur";

$IDName = get_session("Cookie_userName");

$sSQL = "";
$sWhere = "category = 'AK'";

if ($q <> "") {
    if ($by == 1) {
        $sWhere .= " AND name like '%" . $q . "%'";
    } else if ($by == 2) {
        $sWhere .= " AND b_pic like '%" . $q . "%'";
    } else if ($by == 3) {
        $sWhere .= " AND b_busreg like '%" . $q . "%'";
    }
}

$sSQL = "SELECT * FROM generalacc";
if (!empty($sWhere)) {
    $sSQL .= " WHERE " . $sWhere;
}
$sSQL .= " ORDER BY ID ASC"; 

$GetInvest = &$conn->Execute($sSQL);

$GetInvest->Move($StartRec-1);
$TotalRec = $GetInvest->RowCount();
$TotalPage = ($TotalRec / $pg);

print '
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<input type="hidden" name="filter" value="'.$filter.'">
<h5 class="card-title">'.strtoupper($title).'</h5>
    
<div class="mb-3 row m-1">
<div>Cari Berdasarkan 
			<select name="by" class="form-select-sm mt-3">'; 
if ($by == 1)	print '<option value="1" selected>Nama Serikat</option>'; 	else print '<option value="1">Nama Serikat</option>';				
if ($by == 2)	print '<option value="2" selected>Person In Charge</option>'; 	else print '<option value="2">Person In Charge</option>';
if ($by == 3)	print '<option value="3" selected>No. Business Registration</option>'; 	else print '<option value="3">No. Business Registration</option>';							
print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm mt-3">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;';	
			
print '		</div></div>
<div class="table-responsive">    
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td align="right" class="textFont">Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
					if ($pg == 5)	print '<option value="5" selected>5</option>'; 	 	else print '<option value="5">5</option>';				
					if ($pg == 10)	print '<option value="10" selected>10</option>'; 	else print '<option value="10">10</option>';				
					if ($pg == 20)	print '<option value="20" selected>20</option>'; 	else print '<option value="20">20</option>';				
					if ($pg == 30)	print '<option value="30" selected>30</option>'; 	else print '<option value="30">30</option>';				
					if ($pg == 40)	print '<option value="40" selected>40</option>'; 	else print '<option value="40">40</option>';				
					if ($pg == 50)	print '<option value="50" selected>50</option>';	else print '<option value="50">50</option>';				
					if ($pg == 100)	print '<option value="100" selected>100</option>';	else print '<option value="100">100</option>';				
	print '				</select> setiap halaman..
					</td>
				</tr>
			</table>
		</td>
	</tr><br/>';	
	if ($GetInvest->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>&nbsp;<b>Nama Serikat</b></td>
						<td nowrap align="left">&nbsp;<b>Person In Charge</b></td>
						<td nowrap align="left">&nbsp;<b>Business Registration No</b></td>
					</tr>';	
		while (!$GetInvest->EOF && $cnt <= $pg) {
		
			print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><a href="'.$sFileRef.'&pk='.tohtml($GetInvest->fields(ID)).'">'.strtoupper($GetInvest->fields(name)).'</a></td>
						<td class="Data" align="left">&nbsp;'.$GetInvest->fields(b_pic).'</td>
						<td class="Data" align="left">&nbsp;'.$GetInvest->fields(b_busreg).'</td>
					</tr>';
				$cnt++;
				$bil++;
			$GetInvest->MoveNext();
		}

		$GetInvest->Close();
		print ' </table>
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
				for	($i=1; $i <= $numPage; $i++) {
					if(is_int($i/10)) print	'<br />';
					print '<A href="'.$sFileName.'&StartRec='.(($i	* $pg) + 1 - $pg).'&pg='.$pg.'&q='.$q.'&by='.$by.'&filter='.$filter.'">';
					print '<b><u>'.(($i	* $pg) - $pg + 1).'-'.($i *	$pg).'</u></b></a>&nbsp;&nbsp;';
				}
				print '</td>
					</tr>
				</table>';
			}
		print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>' . $GetInvest->RowCount() . '</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk '.$title.'  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	}
print ' 
</table></td></tr></table></div>
</form>';

include("footer.php");	

print '
<script	language="JavaScript">
	var	allChecked=false;

	function doListAll() {
		c =	document.forms[\'MyForm\'].pg;
		document.location =	"' . $sFileName	. '&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter='.$filter.'";
	}
</script>';
?>
	