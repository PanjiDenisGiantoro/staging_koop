<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selDateOptAD.php
 *		   Description	:	Selection Date Option with year distinct in transactionacc & Pilihan Akaun
 *		   Parameter	:   $rpt - represent report program name
 *          Date 		: 	04/03/2003
 *          Updated Date : 	2024
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y, g:i a");

if ($rpt == "") {
	print '	<script>
				alert ("' . $rpt . ' - Nama laporan ini tidak wujud...!");
				window.close();
			</script>';
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
	$kod_akaun2 = $kod_akaun2;

	if ($dtFrom > $dtTo) $msg = "Tanggal Pada tidak boleh  melebihi dari Tanggal Hingga";
	if ($msg <> "") {
		print '<script>alert("' . $msg . '");</script>';
	} else {
		$rptURL = $rpt . '.php?dtFrom=' . $dtFrom . '&dtTo=' . $dtTo . '&kodAkaun=' . $kod_akaun2;
		print '
		<script>
			var rptUrl;
			window.open ("' . $rptURL . '", "rpt","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
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

$strDeductIDList = deductListb2(1);
$strDeductCodeList = deductListb2(2);
$strDeductNameList = deductListb2(3);
$strSelect = '<select id="selectDeduct" class="form-select-xs">
			 <option value="">- Pilih -';

for ($i = 0; $i < count($strDeductIDList); $i++) {
	$strSelect .= '    <option value="' . $strDeductIDList[$i] . '">' . $strDeductCodeList[$i] . '&nbsp;&nbsp;' . $strDeductNameList[$i] . '</option>';
}
$strSelect .= '</select>';

$sqlYears = "SELECT DISTINCT YEAR(tarikh_doc) AS year 
			FROM transactionacc 
			WHERE tarikh_doc IS NOT NULL 
			AND tarikh_doc != '' 
			AND tarikh_doc != 0 
			ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

print '
<form name="FrmSelection" action="' . $PHP_SELF . '" method="post">
	<input type="hidden" name="rpt" value="' . $rpt . '">
	<table border="0" cellpadding="3" cellspacing="0" class="table table-sm table-striped" style="padding: 1 0 0 0" height="100" width="100%">
		<tr valign="top">
			<td class="textFont" align="right"><b>Tanggal Pada</b></td>
			<td class="textFont">
				<select name="ddFrom" class="form-select-xs">';
for ($i = 1; $i < 32; $i++) {
	print '			<option value="' . $i . '"';
	if ($ddFrom == $i) print 'selected';
	print 			'>' . $i;
}
print '			</select> 
				<select name="mmFrom" class="form-select-xs">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="' . $j . '"';
	if ($mmFrom == $j) print 'selected';
	print 			'>' . $j;
}
print '			</select>
			<select name="yyFrom" class="form-select-xs">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yyFrom == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
$rsYears->MoveFirst();
print '		</select>
			</td>
			<td class="textFont" align="right"><b>Tanggal Hingga</b></td>
			<td class="textFont">
				<select name="ddTo" class="form-select-xs">';
for ($i = 1; $i < 32; $i++) {
	print '			<option value="' . $i . '"';
	if ($ddTo == $i) print 'selected';
	print 			'>' . $i;
}
print '			</select> 
				<select name="mmTo" class="form-select-xs">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="' . $j . '"';
	if ($mmTo == $j) print 'selected';
	print 			'>' . $j;
}
print '			</select>
							<select name="yyTo" class="form-select-xs">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yyTo == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
</td>

		</tr>
		<tr> 
			<td align="right" ><b>Pilih Akaun</b></td>
			<td >' . $strSelect . ' 
				<input type="hidden" id="kod_akaun2" name="kod_akaun2"  size="10" maxlength="10" value="' . $kod_akaun2 . '" class="form-control-sm">

		</td>';

print '			
		</tr>

		<tr>
			<td colspan="4" align="center"><input type="submit" name="action" value="Jana Laporan" class="btn btn-primary"></td>
		</tr>

	</table>
</form>

<script>
    // JavaScript to update the value of kod_akaun2 when a different option is selected
    document.getElementById("selectDeduct").addEventListener("change", function() {
        var select = document.getElementById("selectDeduct");
        var selectedOption = select.options[select.selectedIndex];
        document.getElementById("kod_akaun2").value = selectedOption.value;
    });
</script>

</body>
</html>';
