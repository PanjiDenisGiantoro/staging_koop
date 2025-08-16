<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selMthYear.php
 *		   Description	:	Selection Month/Year Option with distinct year from transactionacc and transaction
 *		   Parameter	:   $rpt, $id 
 *          Date 		: 	15/12/2003
 *          Date 		: 	2024
 *********************************************************************************/
include("common.php");


$today = date("F j, Y, g:i a");

if ($rpt == "") {
	print '	<script>
				alert ("Pengguna tidak boleh akses mukasurat ini...!");
				window.close();
			</script>';
	exit;
}

if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");

if ($action == "Jana Laporan") {
	$msg	= "";
	$yrmth = sprintf("%04d%02d", $yr, $mth);
	if ($yrmth == "") $msg = "Tiada Bulan/Tahun dimasukkan...!";
	if ($msg <> "") {
		print '<script>alert("' . $msg . '");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "' . $rpt . '.php?yrmth=' . $yrmth . '&id=' . $id . '&mth=' . $mth . '&yr=' . $yr . '";
			window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			window.close();
		</script>	';
	}
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<!--LINK rel="stylesheet" href="images/default.css" -->	
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />	
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';

$sqlYears = "
    SELECT DISTINCT YEAR(tarikh_doc) AS year 
    FROM transactionacc 
    WHERE tarikh_doc IS NOT NULL 
    AND tarikh_doc != '' 
    AND tarikh_doc != 0 
    
    UNION 
    
    SELECT DISTINCT YEAR(createdDate) AS year 
    FROM transaction 
    WHERE createdDate IS NOT NULL 
    AND createdDate != '' 
    AND createdDate != 0 

    ORDER BY year ASC
";
$rsYears = $conn->Execute($sqlYears);

print '
<form name="FrmSelection" action="' . $PHP_SELF . '" method="post">
	<input type="hidden" name="rpt" value="' . $rpt . '">
	<input type="hidden" name="id" value="' . $id . '">
	<table border="0" cellpadding="3" cellspacing="0" class="table table-sm table-striped" style="padding: 1 0 0 0;font-size:9pt" height="100" width="100%">
		<tr valign="top">
			<td class="textFont" align="right"><b>Bulan/Tahun Pada </b>&nbsp;</td>
			<td class="textFont">
				<select name="mth" class="form-select-xs">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="' . $j . '"';
	if ($mth == $j) print 'selected';
	print 			'>' . $j;
}
print '			</select>/
				<select name="yr" class="form-select-xs">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yr == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '			</select>
</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><input type="submit" name="action" value="Jana Laporan" class="btn btn-primary"></td>
		</tr>
	</table>
</form>


</body>
</html>';
