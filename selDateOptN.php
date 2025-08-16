<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	rptDateOpt.php
*		   Description	:	Selection Date Option
*		   Parameter	:   $rpt - represent report program name
*          Date 		: 	04/03/2003
*********************************************************************************/
include ("common.php");	
$today = date("F j, Y, g:i a");                 

if ($rpt == "") {
	print '	<script>
				alert ("'.$rpt.' - Nama laporan ini tidak wujud...!");
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
	//$dtTo	= sprintf("%04d-%02d-%02d", $yyTo, $mmTo, $ddTo);
	//if ($dtFrom > $dtTo) $msg = "Tarikh Pada tidak boleh  melebihi dari Tarikh Hingga";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		$rptURL = $rpt.'.php?dtFrom='.$dtFrom;
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
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<table border="0" cellpadding="3" cellspacing="0" class="contentD" style="padding: 1 0 0 0" height="100" width="100%">
		<tr valign="top">
			<td class="textFont"><b>Tarikh Pada :</b></td>
			<td class="textFont">
				<select name="ddFrom" class="data">';
for ($i = 1; $i < 32; $i++) {
	print '			<option value="'.$i.'"';
	if ($ddFrom == $i) print 'selected';
	print 			'>'.$i;
}
print '			</select> 
				<select name="mmFrom" class="data">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="'.$j.'"';
	if ($mmFrom == $j) print 'selected';
	print 			'>'.$j;
}
print '			</select>
				<input type="text" name="yyFrom" size="5" maxlength="4" value="'.$yyFrom.'" class="data">
			</td>
			
			
		</tr>
		<tr>
			<td colspan="4" align="center"><input type="submit" name="action" value="Jana Laporan" class="but"></td>
		</tr>
	</table>
</form>


</body>
</html>';
?>