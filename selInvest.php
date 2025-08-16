<?php
/*********************************************************************************
*          Project		:	KPF2 Modul Pelaburan
*          Filename		: 	selInvest.php
*		   Description	:	Selection Syarikat
*          Date 		: 	06/10/2023
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
                		
if ($action == "Jana Laporan") {
	$msg	= "";
	$kod_bank = $kod_bank;

	if ($kod_bank == "") $msg = "Tiada Syarikat Dipilih...";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "'.$rpt.'.php?id='.$kod_bank.'";
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

print '
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<table border="0" cellpadding="3" cellspacing="0" class="table table-striped table-sm" style="padding: 1 0 0 0" height="100" width="100%">
		
		<tr> 
			<td class="textFont" align="right"><b>Pilih Syarikat</b></td>
			<td align="left">'.selectinvestors($kod_bank,'kod_bank').'</td>
		</tr>

		<tr>
			<td colspan="2" align="center"><input type="submit" name="action" value="Jana Laporan" class="btn btn-primary"></td>
		</tr>
	</table>
</form>

</body>
</html>';
?>