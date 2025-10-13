<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	ACCidpemiutangPI.php
*          Date 		: 	16/5/2024 - list all PO of creditors with their amounts
*********************************************************************************/
include ("common.php");
	
include("koperasiQry.php");	
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";
if (!isset($filter))	$filter="ALL";

$sFileName = "ACCidpemiutangPI.php";//file name

//--- Prepare department list
$deptList 	= Array();
$deptVal  	= Array();
$sSQL 		= " SELECT a.departmentID, b.code as deptCode, b.name as deptName
				FROM userdetails a,	general	b
				WHERE a.departmentID = b.ID
				AND	  a.status = 1
				GROUP BY a.departmentID";
$rs			= &$conn->Execute($sSQL);
if ($rs->RowCount()	<> 0){
	while (!$rs->EOF) {
		array_push ($deptList, $rs->fields(deptName));
		array_push ($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

	$sSQL 	= "";
	$sWhere = "  A.purcNo is not null ";
	//where statements
	if ($dept <> "") 	{
		$sWhere .= " AND B.departmentID = " . $dept;
		$sWhere .= " AND A.userID = B.userID ";
	}

	if ($q <> "") 	{
		if ($by == 1) {
			$sWhere .= " AND A.purcNo like '%" .$q ."%'";			
		} else if ($by == 2) {
			$sWhere .= " AND A.companyID = B.ID ";
			$sWhere .= " AND B.name like '%" . $q. "%'";
		}
	}

	if ($id) $sWhere .= " AND A.companyID in (".$id.") ";

	$sWhere = " WHERE (" . $sWhere . ")";
	
	//fields selection
	if ($q <> "") 	{
		if ($by == 1 OR $by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	cb_purchase A, generalacc B";
		} else if ($by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") 	{
			$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM cb_purchase A ";
		}
	}
	$sSQL 		= $sSQL . $sWhere . ' ORDER BY A.tarikh_purc DESC';
	$GetLoan 	= &$conn->Execute($sSQL);

$GetLoan->Move($StartRec-1);

$TotalRec =	$GetLoan->RowCount();
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
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />	
</head>
<script language="JavaScript">
	function selPinjaman(id,code,purcNo,nama,b_Baddress,amt,kodGL)
	{
window.opener.document.MyForm.companyID.value = id;
window.opener.document.MyForm.code.value = code;
window.opener.document.MyForm.purcNo.value = purcNo;
window.opener.document.MyForm.nama_anggota.value = nama;
window.opener.document.MyForm.b_Baddress.value = b_Baddress;
window.opener.document.MyForm.amt.value = amt;
window.opener.document.MyForm.kodGL.value = kodGL;
window.close();
}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' .$PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="'.$by.'">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" style="font-size: 9pt;">
				<h5 class="card-title">Senarai Purchase Order</h5>
	<tr	valign="top" class="Header">
		<td	align="left" >
			Cari Berdasarkan
			<select name="by"  class="form-select-sm">';
	if ($by	== 1)	print '<option value="1" selected>No. PO</option>';		else print '<option	value="1">No. PO</option>';
	if ($by	== 2)	print '<option value="2" selected>Nama Syarikat</option>';	else print '<option	value="2">Nama Syarikat</option>';

	print '	</select>
	<input type="text" name="q"	value="" maxlength="50"	size="20" class="form-control-sm">
	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';
			
			
	print '	</select>
		</td>
	</tr>
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%" style="font-size: 9pt;">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;</td>
					<td	align="right" class="textFont">

					Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
					if ($pg	== 5)	print '<option value="5" selected>5</option>';		else print '<option	value="5">5</option>';
					if ($pg	== 10)	print '<option value="10" selected>10</option>';	else print '<option	value="10">10</option>';
					if ($pg	== 20)	print '<option value="20" selected>20</option>';	else print '<option	value="20">20</option>';
					if ($pg	== 30)	print '<option value="30" selected>30</option>';	else print '<option	value="30">30</option>';
					if ($pg	== 40)	print '<option value="40" selected>40</option>';	else print '<option	value="40">40</option>';
					if ($pg	== 50)	print '<option value="50" selected>50</option>';	else print '<option	value="50">50</option>';
					if ($pg	== 100)	print '<option value="100" selected>100</option>';	else print '<option	value="100">100</option>';
					if ($pg	== 200)	print '<option value="200" selected>200</option>';	else print '<option	value="200">200</option>';
					if ($pg	== 300)	print '<option value="300" selected>300</option>';	else print '<option	value="300">300</option>';
					if ($pg	== 400)	print '<option value="400" selected>400</option>';	else print '<option	value="400">400</option>';
					if ($pg	== 500)	print '<option value="500" selected>500</option>';	else print '<option	value="500">500</option>';
					if ($pg	== 1000) print '<option	value="1000" selected>1000</option>';	else print '<option	value="1000">1000</option>';

	print '				</select>setiap halaman.
					</td>
				</tr>';
	print '	</table>
		</td>
	</tr>';

if ($GetLoan->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan No / Nama Syarikat  -</b>
				</td></tr>';
} else {				
	if ($GetLoan->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
					<tr	class="table-primary">
						<td	nowrap>&nbsp;</td>
						<td	nowrap><b>No. Purchase Order</b></td>
						<td	nowrap><b>No/Nama Syarikat</b></td>
						<td	nowrap align="left"><b>Alamat Billing</b></td>
						<td	nowrap align="right"><b>Jumlah (RP)</b></td>
						<td	nowrap align="center"><b>Tarikh PO</b></td>
					</tr>';
		$amtLoan = 0;
		while (!$GetLoan->EOF && $cnt <= $pg) {
			//$jabatan = dlookup("userdetails", "departmentID", "userID="	. tosql($GetLoan->fields(userID), "Text"));
			$purchaseNumber = $GetLoan->fields(purcNo);
			$sSQL1 			= "SELECT pymtAmt FROM cb_purchaseinf WHERE docNo = '" . $purchaseNumber . "' AND addminus = '1'";
			$rs1			= &$conn->Execute($sSQL1);
			$amt 			= $rs1->fields(pymtAmt);			$amtLoan = $amtLoan	+ tosql($GetLoan->fields(loanAmt), "Number");
		
//-------------------
		$id		= $GetLoan->fields(companyID);
		$code	= dlookup("generalacc", "code", "ID=" . tosql($GetLoan->fields(companyID), "Text"));;

		$nama	= str_replace("'","", dlookup("generalacc", "name", "ID=" . tosql($GetLoan->fields(companyID), "Text")));
		
		$b_Baddress	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($GetLoan->fields(companyID), "Text"));

		//------------
		$purcNo = $GetLoan->fields(purcNo);
		$kodGL 	= $GetLoan->fields(kodGL);
		

			print '	<tr>
						<td	class="Data" align="center">' . $bil	. '&nbsp;</td>


						<td	class="Data">&nbsp;<a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$purcNo.'\',\''.$nama.'\',\''.$b_Baddress.'\',\''.$amt.'\',\''.$kodGL.'\');">'.$GetLoan->fields(purcNo).'</a></td>


						<td	class="Data">&nbsp;'.dlookup("generalacc",	"code",	"ID=" .	tosql($GetLoan->fields(companyID),	"Text")).'-'.dlookup("generalacc", "name", "ID="	. tosql($GetLoan->fields(companyID), "Text")).'</td>


						<td	class="Data">&nbsp;'.dlookup("generalacc",	"b_Baddress", "ID=" . tosql($GetLoan->fields(companyID),	"Text")).'</td>


						<td	class="Data" align="right">'.number_format($amt,2).'&nbsp;</td>

						<td	class="Data" align="center">&nbsp;'.toDate("d/m/yy",$GetLoan->fields(tarikh_purc)).'</td>
					</tr>';
				$cnt++;
				$bil++;
			$GetLoan->MoveNext();
		}	
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
					for ($i=1; $i <= $numPage; $i++) {
						print '<A href="'.$sFileName.'?&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.$q.'&by='.$by.'&dept='.$dept.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a> &nbsp; &nbsp;';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>';
				
		print '
				</td>
			</tr>
				</table>
				
						</td>
					</tr>';
	} else { 
		print '
					<tr><td	class="Label" align="center" height=50 valign=middle>
						<b>- Tiada rekod mengenai syarikat  -</b>
					</td></tr>';
	} // end of ($GetLoan->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">'.$retooFetis.'</p>
</body>
</html>';

print '
<script language="JavaScript">

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter='.$filter.'";
	}

</script>';
?>