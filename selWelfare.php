<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selWelfare.php
*		   Used By		:	welfareApply.php
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	

$q = "";
$cat = "S";
$sSQL = "";
$sWhere = "";
$sWhere = "category = ".tosql($cat,"Text")." AND s_Aktif IN (1) ";
if ($q <> "") $sWhere .= " and name like ".tosql($q."%","Text");
$sWhere = " WHERE (".$sWhere.")";
$sSQL = "SELECT * FROM general";
$sSQL = $sSQL . $sWhere . ' ORDER BY code,ID';
$GetWelfare = &$conn->Execute($sSQL);

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
	function selBantuan(id,code,name)
	{	
		window.opener.document.MyForm.welfareType.value	= id;	
		window.opener.document.MyForm.welfareCode.value	= code;	
		window.opener.document.MyForm.welfareName.value	= name;	
		// window.opener.document.MyForm.welfareAmt.value 	= amt;
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
if ($GetWelfare->RowCount() <> 0) {  
	print '		<tr>
		<td class="Data">
		<table cellpadding="2" cellspacing="1" width="100%" class="table table-bordered table-striped table-sm" style="font-size: 10pt;">
                <thead>
		<tr class="table-primary">
			<td class="headerteal"><b>Kode Kebajikan</b></td>
			<td class="headerteal"><b>Jenis Kebajikan</b></td>
		</tr></thead>';
	while (!$GetWelfare->EOF) {
		$id		= $GetWelfare->fields(ID);
		$welfareApplied = 0;
		$code	= $GetWelfare->fields(code);
		

		$name		= $GetWelfare->fields(name);	
		// $amt		= $GetWelfare->fields(s_Maksimum);

		if ($welfareApplied==0) {
		print '<tr>
			<td class="Data"><a href="javascript:selBantuan(\''.$id.'\',\''.$code.'\',\''.$name.'\');">'.$code.'</a></td>
			<td class="Data">'.$name.'</td>';


		print '
		</tr>';
		} else {
		print '	<tr>
			<td class="Data">'.$code.'</td>
			<td class="Data">'.$name.'</td>';

		print '	<td class="Data" align="right">'.$amt.'</td>
		</tr>';
		}
		$GetWelfare->MoveNext();
	}				
	print '</table></td></tr>
	<tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Kebajikan : <b>'.$GetWelfare->RowCount().'</b></i></li></td></tr>';
} else { 
	print '
	<tr><td	class="Label" align="center">
	<hr size="1"><b>- Tiada Rekod Jenis Kebajikan Yang Aktif -</b><hr size="1">
	</td></tr>';
}
print '</table></div></form>
<p align="center" class="footer">'.$retooFetis.'</p>
</body></html>';
?>