<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selLoan.php
*		   Used By		:	loanApply.php
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	

$q = "";
$cat = "C";
$sSQL = "";
$sWhere = "";
$sWhere = "category = ".tosql($cat,"Text")." AND parentID <> 0 and Status_IDCode IS NULL AND c_Aktif IN (1)";
if ($q <> "") $sWhere .= " and name like ".tosql($q."%","Text");
$sWhere = " WHERE (".$sWhere.")";
$sSQL = "SELECT * FROM general";
$sSQL = $sSQL . $sWhere . ' ORDER BY code,ID';
$GetLoan = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />        
	
</head>
<script language="JavaScript">
	function selPinjaman(id,code,name,caj,period,amt)
	{	
		window.opener.document.MyForm.loanType.value	= id;	
		window.opener.document.MyForm.loanCode.value	= code;	
		window.opener.document.MyForm.loanName.value	= name;	
		window.opener.document.MyForm.loanCaj.value 	= caj;	
		window.opener.document.MyForm.loanPeriod.value 	= period;	
		window.opener.document.MyForm.loanAmt.value 	= amt;
		window.close();
	}
</script>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="MyForm" action='.$PHP_SELF.' method="post">
    <div class="table-responsive">
<input type="hidden" name="action">

			<table class="table" border="0" cellspacing="1" cellpadding="3" width="95%" align="center">
				<tr>
					<td	class="Label" colspan="2"><b>Klik Pada Kod Untuk Pilihan.</b></td>
				</tr>';
if ($GetLoan->RowCount() <> 0) {  
	print '		<tr>
		<td class="Data">
		<table cellpadding="2" cellspacing="1" width="100%" class="table table-bordered table-striped table-sm" style="font-size: 10pt;">
                <thead>
		<tr class="table-primary">
			<td class="headerteal"><b>Kod Pembiayaan</b></td>
			<td class="headerteal"><b>Jenis Pembiayaan</b></td>
			<td class="headerteal"><b>Kod Potongan</b></td>
			<td class="headerteal"><b>Penjamin</b></td>
			<td class="headerteal" align="right"><b>Jumlah Maksimum (RP)</b></td>
			<td class="headerteal" align="center"><b>Tempoh Bulan Maksimum</b></td>
			<td class="headerteal" align="center"><b>Caj Pembiayaan (%)</b></td>
		</tr></thead>';
	while (!$GetLoan->EOF) {
		$id		= $GetLoan->fields(ID);
		$loanApplied = 0;
		$code	= $GetLoan->fields(code);
		$c_deduct = $GetLoan->fields(c_Deduct);
		
		if ($c_deduct<>"") {
			$getKod = "SELECT code, name FROM general WHERE ID = ".$c_deduct;
			$rsKod = $conn->Execute($getKod);
			$kod_potongan = $rsKod->fields(code);
		} else {
			$kod_potongan = "";
		}

		$name		= $GetLoan->fields(name);			
		$caj		= $GetLoan->fields(c_Caj);
		$period		= $GetLoan->fields(c_Period);
		$amt		= $GetLoan->fields(c_Maksimum);
		$penjamin	= $GetLoan->fields(c_gurrantor);

		$colorStatus1 = "Data";
			if ($penjamin <> 0) $colorStatus1 = "greenText";
			if ($penjamin <> 1) $colorStatus1 = "redText";

		if ($loanApplied==0) {
		print '<tr>
			<td class="Data"><a href="javascript:selPinjaman(\''.$id.'\',\''.$code.'\',\''.$name.'\',\''.$caj.'\',\''.$period.'\',\''.$amt.'\');">'.$code.'</a></td>
			<td class="Data">'.$name.'</td>
			<td class="Data" align="left">'.$kod_potongan.'</td>';

			if($penjamin == 1)
			{
				print '<td class="Data" align="left"><font class="'.$colorStatus1.'"><b>PERLU PENJAMIN</b></font></td>';
			} else {
				print '<td class="Data" align="left"><font class="'.$colorStatus1.'"><b>TIADA PENJAMIN</b></font></td>';
			}

		print '<td class="Data" align="right">'.$amt.'</td>
			<td class="Data" align="center">'.$period.'</td>
			<td class="Data" align="center">'.$caj.'</td>
		</tr>';
		} else {
		print '	<tr>
			<td class="Data">'.$code.'</td>
			<td class="Data">'.$name.'</td>
			<td class="Data" align="left">'.$kod_potongan.'</td>';

			if($penjamin == 1)
			{
				print '<td class="Data" align="left"><font class="'.$colorStatus1.'"><b>PERLU PENJAMIN</b></font></td>';
			} else {
				print '<td class="Data" align="left"><font class="'.$colorStatus1.'"><b>TIADA PENJAMIN</b></font></td>';
			}

		print '	<td class="Data" align="right">'.$amt.'</td>
			<td class="Data" align="center">'.$period.'</td>
			<td class="Data" align="center">'.$caj.'</td>
		</tr>';
		}
		$GetLoan->MoveNext();
	}				
	print '</table></td></tr>
	<tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Pembiayaan : <b>'.$GetLoan->RowCount().'</b></i></li></td></tr>';
} else { 
	print '
	<tr><td	class="Label" align="center">
	<hr size="1"><b>- Tiada Rekod Mengenai Jenis Pembiayaan  -</b><hr size="1">
	</td></tr>';
}
print '</table></div></form>
<p align="center" class="footer">'.$retooFetis.'</p>
</body></html>';
?>