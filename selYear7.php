<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selYear7.php
*		   Description	:	Selection Year Option with Distinct Years and Debtor Companies Option
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
	$kod_bank = $kod_bank;

	if ($yr == "") $msg = "Tiada Tahun Dimasukkan...";
	if ($kod_bank == "") $msg = "Tiada Syarikat Dipilih...";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		//appended cr=1 for SOA pemiutang report
		print '
		<script>
			var rptURL;
			rptURL = "'.$rpt.'.php?yr='.$yr.'&id='.$kod_bank.'";
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
    
    <!-- Using Flexbox for compact and aligned layout -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 20px; width: 90%; margin-top: 20px;">
        
        <!-- Tahun Selection -->
        <div style="display: flex; align-items: center;">
            <label for="yr" style="margin-right: 10px;"><b>Tahun&nbsp;</b></label>
            <select name="yr" class="form-select-xs" onchange="document.MyForm.submit();">';
            while (!$rsYears->EOF) {
                $year = $rsYears->fields['year'];
                print '<option value="'.$year.'"';
                if ($yr == $year) print 'selected';
                print '>'.$year.'</option>';
                $rsYears->MoveNext();
            }
print '   </select>
        </div>

        <!-- Pilih Syarikat -->
        <div style="display: flex; align-items: center;">
            <label for="kod_bank" style="margin-right: 10px;"><b>Pilih Syarikat&nbsp;</b></label>';
            print selectsyarikatAC($kod_bank,'kod_bank');
print '   </div>

        <!-- Submit Button -->
        <div>
            <input type="submit" name="action" value="Jana Laporan" class="btn btn-primary" style="margin-left: 10px;">
        </div>
    </div>
</form>

</body>
</html>';
?>