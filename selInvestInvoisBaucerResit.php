<?php
/*********************************************************************************
*          Project		:	KPF2 Modul Pelaburan
*          Filename		: 	selInvestInvoisBaucerResit.php
*		   Description	:	Selection Invois,Baucer,Resit Pelaburan
*          Date 		: 	28/02/2024
*********************************************************************************/
include("common.php");
include("koperasiQry.php");	

date_default_timezone_set("Asia/Kuala Lumpur");
$today = date("F j, Y, g:i a");                 

if ($rpt == "") {
	print '	<script>
				alert ("Pengguna tidak boleh akses mukasurat ini...!");
				window.close();
			</script>';
			exit;
}
       
if (!isset($ddFrom)) $ddFrom	= 1;                 		
if (!isset($mmFrom)) $mmFrom	= date("n");                 		
if (!isset($yyFrom)) $yyFrom	= date("Y");                 		
if (!isset($ddTo)) 	 $ddTo  	= date("j");                 		
if (!isset($mmTo)) 	 $mmTo  	= date("n");                 		
if (!isset($yyTo)) 	 $yyTo  	= date("Y");                 		
if ($action == "Jana Laporan") {
	$msg	= "";
	$dtFrom = sprintf("%04d-%02d-%02d", $yyFrom, $mmFrom, $ddFrom);
	$dtTo	= sprintf("%04d-%02d-%02d", $yyTo, $mmTo, $ddTo);
	if ($dtFrom > $dtTo) $msg = "Tanggal Pada tidak boleh  melebihi dari Tanggal Hingga";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		$rptURL = $rpt.'.php?dtFrom='.$dtFrom.'&dtTo='.$dtTo;
		print '
		<script>
			var rptUrl;
			window.open ("'.$rptURL.'", "rpt","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			window.close();
		</script>	';
	}
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
	<!--LINK rel="stylesheet" href="images/default.css" -->	
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />        
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<table border="0" cellpadding="3" cellspacing="0" class="table table-sm table-striped" style="padding: 1 0 0 0;font-size:9pt" height="100" width="100%">
		<tr valign="top">
			<td class=""><b>Tanggal Pada</b></td>
			<td class="">
				<select name="ddFrom" class="form-select-xs">';
for ($i = 1; $i < 32; $i++) {
	print '			<option value="'.$i.'"';
	if ($ddFrom == $i) print 'selected';
	print 			'>'.$i;
}
print '			</select> 
				<select name="mmFrom" class="form-select-xs">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="'.$j.'"';
	if ($mmFrom == $j) print 'selected';
	print 			'>'.$j;
}
print '			</select>
				<input type="text" name="yyFrom" size="3" maxlength="4" value="'.$yyFrom.'" class="form-select-xs">
			</td>
			<td class="textFont"><b>Tanggal Hingga</b></td>
			<td class="textFont">
				<select name="ddTo" class="form-select-xs">';
for ($i = 1; $i < 32; $i++) {
	print '			<option value="'.$i.'"';
	if ($ddTo == $i) print 'selected';
	print 			'>'.$i;
}
print '			</select> 
				<select name="mmTo" class="form-select-xs">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="'.$j.'"';
	if ($mmTo == $j) print 'selected';
	print 			'>'.$j;
}
print '			</select>
				<input type="text" name="yyTo" size="3" maxlength="4" value="'.$yyTo.'" class="form-select-xs">
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><input type="submit" name="action" value="Jana Laporan" class="btn btn-primary"></td>
		</tr>
	</table>
</form>

</body>
</html>';
?>