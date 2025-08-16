<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selYear.php
 *		   Description	:	Selection Year Option with distinct year from transactionacc and transaction
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

if (!isset($yr)) $yr	= date("Y");

if ($action == "Jana Laporan") {
	$msg	= "";
	if ($yr == "") $msg = "Tiada Tahun dimasukkan...";
	if ($msg <> "") {
		print '<script>alert("' . $msg . '");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "' . $rpt . '.php?yr=' . $yr . '&pk=' . $id . '&id=' . $id . '";
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
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">

';

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
<div class="container text-center">
<form name="FrmSelection" action="' . $PHP_SELF . '" method="post">
	<input type="hidden" name="rpt" value="' . $rpt . '">
	<input type="hidden" name="id" value="' . $id . '">
	
        <div class="row justify-content-center">
            <div class="col-auto">
                <div class="mb-3">
                    <label class="form-label"><b>Tahun</b>&nbsp;</label>
                    <select name="yr" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yr == $year) print ' selected';
	print '>' . $year . '</option>';
	$rsYears->MoveNext();
}
print '             </select>
                </div>
                <div class="mb-3">
                    <input type="submit" name="action" value="Jana Laporan" class="btn btn-primary">
                </div>
            </div>
        </div>
    </form>
</div>

</body>
</html>';
