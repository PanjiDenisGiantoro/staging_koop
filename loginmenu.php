<?
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	loginmenu.php
*          Date 		: 	12/09/2003
*********************************************************************************/
include("common.php");

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<script>
history.forward();
window.status="Sistem Keanggotaan Koperasi versi 1.01";
</script>
<meta name="Keywords"  content="'.$siteKeyword.'">
<meta name="Description" content="'.$siteDesc.'">
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U .'">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<LINK rel="stylesheet" href="images/default.css" >
</head>
<body>
<form name="MyNetForm"">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#f0f0f0">';

	$strModul = 'MENU PELAWAT';
	TitleBarBlue($strModul);
	MenuLink("mainpage.php", "Login");
	MenuLink("checkIC.php", "Daftar/Semakan");

print '
</table>
</form>
</body>
</html>';



function TitleBarBlue($strTitle) {
	$strImgLink1 = "images/shade-bkrm-03.gif";
	//$strImgLink2 = "images/shade-logo-bkrm-03.gif";
	print
	'<tr>'
		.'<td>'
			.'<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#0c479d">'
				.'<tr>'
					.'<td width="14%">'
						.'&nbsp;<!--img src="'.$strImgLink2.'" width="28" height="24"-->'
					.'</td>'
					.'<td width="86%" valign="middle">'
						.'<div class="headerblue" style="width:160px;">'.strtoupper($strTitle).'</div>'
					.'</td>'
				.'</tr>'
			.'</table>'
		.'</td>'
	.'</tr>';
}

function MenuLink($strLink, $strTitle) {
	print
	'<tr>'
		.'<td>'
			.'<table width="100%" cellspacing="0" cellpadding="0">'
				.'<tr>'
					.'<td width="2%">'
						.'<div class="nav"><img src="images/sym-tick-red-bkrm-01.gif" width="20" height="20"></div>'
					.'</td>'
					.'<td>'
						.'<div class="nav"><a href="'.$strLink.'" target="mainFrame">'.$strTitle.'</a></div>'
					.'</td>'
				.'</tr>'
			.'</table>'
		.'</td>'
	.'</tr>';
}
?>