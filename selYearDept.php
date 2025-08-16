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
	if ($yr == "") $msg = "Tiada Tahun dimasukkan...";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "'.$rpt.'.php?yr='.$yr.'&id='.$id.'";
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
			<td class="textFont" align="right"><b>Tahun :&nbsp;</b></td>
			<td class="textFont">
				<input type="text" name="yr" size="5" maxlength="4" value="'.$yr.'" class="data">
				<p class="textFont">Pilihan Jabatan
					<select name="dept" class="textFont" onchange="document.MyForm.submit();">
						<option value="ALL">- Sila pilih Jabatan -';
								for ($i = 0; $i < count($deptList); $i++) {
								print '	<option value="'.$deptVal[$i].'" ';
								if ($dept == $deptVal[$i]) print ' selected';
								print '>'.$deptList[$i];
							}
				print '	</select>
	  			</p>
			</td>
		</tr>
		

		<tr>
			<td colspan="2" align="center"><input type="submit" name="action" value="Jana Laporan" class="but"></td>
		</tr>
	</table>
</form>


</body>
</html>';
?>