<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selYear.php
*		   Description	:	Selection Year Option
*		   Parameter	:   $rpt, $id
*          Date 		: 	15/12/2003
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
	if ($tempoh == "") $msg = "Tiada Tempoh dimasukkan...";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "'.$rpt.'.php?tempoh='.$tempoh.'";
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
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<input type="hidden" name="id" value="'.$id.'">
	<table border="0" cellpadding="3" cellspacing="0" class="contentD" style="padding: 1 0 0 0" height="100" width="100%">
		<tr valign="top">
			<td class="textFont" align="right" width="45%"><b>Tempoh : &nbsp;</b></td>
			<td class="textFont" width="10">
				<input type="text" name="tempoh" size="5" maxlength="4" value="" class="data">&nbsp;
			</td>
			<td class="textFont" align=""><b>&nbsp;bulan</b></td>
		</tr>
		<tr>
			<td colspan="3" align="center"><input type="submit" name="action" value="Jana Laporan" class="but"></td>
		</tr>
	</table>
</form>


</body>
</html>';
?>