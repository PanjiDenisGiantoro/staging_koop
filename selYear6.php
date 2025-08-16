<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selYear6.php
*		   Description	:	Selection Year Option with Distinct Years
*		   Parameter	:   $rpt, $id
*          Date 		: 	15/12/2003
*          Updated      :   09/08/2024
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

if (!isset($yr)) $yr= date("Y");                 		
if ($action == "Jana Laporan") {
	$msg	= "";

	if ($yr == "") $msg = "Tiada Tahun Dimasukkan...";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "'.$rpt.'.php?yr='.$yr.'";
			window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
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

$sqlYears = "SELECT DISTINCT YEAR(tarikh_doc) AS year 
            FROM transactionacc 
            WHERE tarikh_doc IS NOT NULL 
            AND tarikh_doc != '' 
            AND tarikh_doc != 0 
            ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

print '
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<table border="0" cellpadding="3" cellspacing="0" class="table table-striped table-sm" style="padding: 1 0 0 0" height="100" width="100%">
		<tr valign="top">
			<td class="textFont" align="right"><b>Tahun&nbsp;</b></td>
			<td class="textFont">
				<select name="yr" class="form-select-xs" onchange="document.MyForm.submit();">';
				while (!$rsYears->EOF) {
					$year = $rsYears->fields['year'];
					print '    <option value="'.$year.'"';
					if ($yr == $year) print 'selected';
					print '>'.$year;
					$rsYears->MoveNext();
				}
print '        	</select>			
			</td>
		</tr>

		<tr>
			<td colspan="2" align="center"><input type="submit" name="action" value="Jana Laporan" class="btn btn-primary"></td>
		</tr>
	</table>
</form>

</body>
</html>';
?>