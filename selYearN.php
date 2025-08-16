<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selYear.php
*		   Description	:	Selection Year Option
*		   Parameter	:   $rpt, $id
*          Date 		: 	15/12/2003
*********************************************************************************/
//include("common.php");	
	

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
		print '<script>alert("'.$msg.'");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "?vw='.$rpt.'&mn=3&yr='.$yr.'&pk='.$id.'&id='.$id.'";
			window.location.href = rptURL;
			//window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			window.close();
		</script>	';
	}
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
	
</head>
<body leftmargin="5" topmargin="5">';

print '
<form name="FrmSelection" action="" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<input type="hidden" name="id" value="'.$id.'">
	<table cellpadding="3" cellspacing="0" class="table table-striped" style="padding: 1 0 0 0" >
		<tr class="table-primary" valign="top">
			<td align="right" width="50%"><b>Tahun</b></td>
			<td width="50%">
						<select name="yr" class="form-selectx btn-light" onchange="document.MyForm.submit();">';
						for ($j = 2018; $j <= 2050; $j++) {
							print '	<option value="'.$j.'"';
							if ($yy == $j) print 'selected';
							print '>'.$j;
						}
							print '</select>			
			</td>
		</tr>
		<tr class="table-light">
			<td colspan="2" align="center">
			<input type="button" class="btn btn-secondary w-sm waves-effect waves-light" value="<<" onClick="window.location.href=\'?vw=memberStmtLoan&mn=3\';">
			<input type="submit" name="action" value="Jana Laporan" class="btn btn-primary w-md waves-effect waves-light">
			</td>
		</tr>
	</table>
</form>


</body>
</html>';
?>