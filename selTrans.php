<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selMember.php
*          Date 		: 	06/10/2003
*		   Amended		:	31/03/2004 - Change to list all member
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2) {
	print '<script>alert("'.$errPage.'");top.location="index.php";</script>';
}

if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($q))			$q="";
if (!isset($code))		$code="ALL";
if (!isset($filter))	$filter="1";

$sFileName = 'selTrans.php';
//$sFileRef  = 'transAddUpdate.php';
$title     =  "Urusniaga";

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$CheckTrans = ctTransactionID($pk[$i]);
		if ($CheckTrans->RowCount() == 1) {
			if ($CheckTrans->fields(status) <> 1) {
			    $sWhere = "ID=" . tosql($pk[$i], "Number");
				$sSQL = "DELETE FROM transaction WHERE " . $sWhere;
				$rs = &$conn->Execute($sSQL);
			} else {
				print '<script>alert("ID  '.$CheckTrans->fields(ID).' - tidak boleh dihapuskan...!");</script>';
			}
		}
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

//--- Prepare deduct list
$deductList = Array();
$deductVal  = Array();
$sSQL = "	SELECT B.ID, B.code , B.name 
			FROM transaction A, general B
			WHERE A.deductID= B.ID
			AND   A.yrmth = " .tosql($yymm,"Text")."	
			AND   A.status = " .tosql($filter,"Number")."	
			GROUP BY A.deductID";
$GetDeduct = &$conn->Execute($sSQL);
if ($GetDeduct->RowCount() <> 0){
	while (!$GetDeduct->EOF) {
		array_push ($deductList, $GetDeduct->fields(code).' - '.$GetDeduct->fields(name));
		array_push ($deductVal, $GetDeduct->fields(ID));
		$GetDeduct->MoveNext();
	}
}		

if ($code <> "ALL")  {
	$GetTrans = ctTransactionCode($q,$yymm,$filter,$code);
} else {
	$GetTrans = ctTransaction($q,$yymm,$filter);
}
$GetTrans->Move($StartRec-1);

$TotalRec = $GetTrans->RowCount();
$TotalPage =  ($TotalRec/$pg);

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 25;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";

//--- Prepare department list
$deptList = Array();
$deptVal  = Array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0){
	while (!$rs->EOF) {
		array_push ($deptList, $rs->fields(deptName));
		array_push ($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$GetMember = ctMemberStatusDept($q,$by,"1",$dept);
$GetMember->Move($StartRec-1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec/$pg);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<script language="JavaScript">
	function selTran(a,b,c,d,e)
	{	
		window.opener.document.MyForm.ID.value = a;	
		window.opener.document.MyForm.kod.value = b;	
		window.opener.document.MyForm.keterangan.value = c;	
		window.opener.document.MyForm.akaun.value = d;	
		window.opener.document.MyForm.jumlah.value = e;	
		window.close();
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' .$sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="'.$code.'">
<input type="hidden" name="filter" value="'.$filter.'">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
    <tr valign="top" class="Header">
	   	<td align="left" >
			Bulan   : 
			<select name="mm" class="data">';
			for ($j = 1; $j < 13; $j++) {
				print '	<option value="'.$j.'"';
				if ($mm == $j) print 'selected';
				print '>'.$j;
			}
print '		</select>
			Tahun  : 
			<select name="yy" class="data">';
			for ($j = 1989; $j <= 2079; $j++) {
				print '	<option value="'.$j.'"';
				if ($yy == $j) print 'selected';
				print '>'.$j;
			}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="but">
		<!--br>Carian melalui No Larian <input type="text" name="q" value="" maxlength="100" size="20" class="Data">
           	 <input type="submit" class="but" value="Cari">
			&nbsp;&nbsp;			
			Kod Potongan
			<select name="code" class="Data" onchange="document.MyForm.submit();">
				<option value="ALL">- Semua -';
			for ($i = 0; $i < count($deductList); $i++) {
				print '	<option value="'.$deductVal[$i].'" ';
				if ($code == $deductVal[$i]) print ' selected';
				print '>'.$deductList[$i];
			}
print '		</select>&nbsp;
			Status
			<select name="filter" class="Data" onchange="document.MyForm.submit();">';
			for ($i = 0; $i < count($statusList); $i++) {
				if ($statusVal[$i] < 3) {
					print '	<option value="'.$statusVal[$i].'" ';
					if ($filter == $statusVal[$i]) print ' selected';
					print '>'.$statusList[$i];
				}
			}
	print '	</select-->&nbsp;&nbsp;	
		</td>
	</tr>';
	if ($GetTrans->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print '
		<!--tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td>
						<td align="right" class="textFont">
							Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
						if ($pg == 5)	print '<option value="5" selected>5</option>'; 	 	else print '<option value="5">5</option>';				
						if ($pg == 10)	print '<option value="10" selected>10</option>'; 	else print '<option value="10">10</option>';				
						if ($pg == 20)	print '<option value="20" selected>20</option>'; 	else print '<option value="20">20</option>';				
						if ($pg == 30)	print '<option value="30" selected>30</option>'; 	else print '<option value="30">30</option>';				
						if ($pg == 40)	print '<option value="40" selected>40</option>'; 	else print '<option value="40">40</option>';				
						if ($pg == 50)	print '<option value="50" selected>50</option>';	else print '<option value="50">50</option>';				
						if ($pg == 100)	print '<option value="100" selected>100</option>';	else print '<option value="100">100</option>';				
						if ($pg == 200)	print '<option value="200" selected>200</option>';	else print '<option value="200">200</option>';				
						if ($pg == 300)	print '<option value="300" selected>300</option>';	else print '<option value="300">300</option>';				
						if ($pg == 400)	print '<option value="400" selected>400</option>';	else print '<option value="400">400</option>';				
						if ($pg == 500)	print '<option value="500" selected>500</option>';	else print '<option value="500">500</option>';				
						if ($pg == 1000) print '<option value="1000" selected>1000</option>';	else print '<option value="1000">1000</option>';				
		print '				</select>setiap mukasurat.
						</td>
					</tr>
				</table>
			</td>
		</tr-->
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap>&nbsp;</td>
						<td nowrap>&nbsp;ID - No Dokumen/Larian </td>
						<td nowrap width="50">&nbsp;Anggota</td>
						<td nowrap>&nbsp;Kod</td>
						<td nowrap>&nbsp;Rujukan</td>
						<td nowrap align="center" width="50">&nbsp;Debit/Kredit</td>
						<td nowrap align="center">&nbsp;Bayaran</td>						
						<td nowrap align="center">&nbsp;Caj</td>
						<td nowrap align="center">&nbsp;Jumlah</td>
						<td nowrap align="center" width="50">&nbsp;TAHUN,BULAN</td>
						<td nowrap align="center">&nbsp;Status</td>
					</tr>';	
		$DRTotal = 0;
		$CRTotal = 0;
		while (!$GetTrans->EOF && $cnt <= $pg) {
			$status = $GetTrans->fields(status);
			$colorStatus = "Data";
			if ($status == 1) $colorStatus = "greenText";
			if ($status == 2) $colorStatus = "redText";
			$totalAmt = $GetTrans->fields(pymtAmt) + $GetTrans->fields(cajAmt);
			if ($GetTrans->fields(addminus) == 0) {
				$addMinus = 'Debit';
				$DRTotal += $totalAmt;
			} else {
				$addMinus = 'Kredit';
				$CRTotal += $totalAmt;
			}
			$deductID = $GetTrans->fields(deductID);
			$code = dlookup("general", "code", "ID=" . $deductID);
			$keterangan = dlookup("general", "name", "ID=" . $deductID);
			$akaun = dlookup("general", "c_Panel", "ID=" . $deductID);
			print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><!--input type="checkbox" class="form-check-input" name="pk[]" value="'.tohtml($GetTrans->fields(ID)).'"-->
						<a href="javascript:selTran(\''.$GetTrans->fields(ID).'\',\''.$code.'\',\''.$keterangan.'\',\''.$akaun.'\',\''.$GetTrans->fields(pymtAmt).'\');">'.$GetTrans->fields(docNo).'</a>
						&nbsp;</td>
						<td class="Data">&nbsp;'.dlookup("userdetails", "memberID", "userID=" . tosql($GetTrans->fields(userID), "Text")).'</td>
						<td class="Data">&nbsp;'.dlookup("general", "code", "ID=" . tosql($GetTrans->fields(deductID), "Number")).'</td>
						<td class="Data">&nbsp;'.$GetTrans->fields(pymtRefer).'</td>
						<td class="Data">&nbsp;'.$addMinus.'</td>						
						<td class="Data" align="right">'.$GetTrans->fields(pymtAmt).'&nbsp;</td>
						<td class="Data" align="right">'.$GetTrans->fields(cajAmt).'&nbsp;</td>
						<td class="Data" align="right">'.number_format($totalAmt, 2, '.', '').'&nbsp;</td>
						<td class="Data" align="center">&nbsp;'.$GetTrans->fields(yrmth).'</td>
						<td class="Data">&nbsp;<font class="'.$colorStatus.'">'.$statusList[$status].'</font></td>
					</tr>';
				$cnt++;
				$bil++;
			$GetTrans->MoveNext();
		}
		print '	</table>
			</td>
		</tr>	
		<tr>
			<td class="textFont" align="right">
			<b>Debit&nbsp;:&nbsp;'.number_format($DRTotal, 2, '.', ',').'&nbsp;&nbsp;&nbsp;
			Kredit&nbsp;:&nbsp;'.number_format($CRTotal, 2, '.', ',').'&nbsp;&nbsp;&nbsp;</b>
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
					for ($i=1; $i <= $numPage; $i++) {
						if(is_int($i/10)) print '<br />';
						print '<A href="'.$sFileName.'?yy='.$yy.'&mm='.$mm.'&code='.$code.'&filter='.$filter.'&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'">';
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
			<td class="textFont">Jumlah Transaksi : <b>' . $GetTrans->RowCount() . '</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk '.$title.' Bagi Bulan/Tahun - '.$mm.'/'.$yy.' -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	}
print ' 
</table>
</form>
<p align="center" class="footer">'.$retooFetis.'</p>
</body>
</html>';
?>

