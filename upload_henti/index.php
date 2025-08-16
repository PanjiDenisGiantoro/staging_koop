<?
/*********************************************************************************
*          Project		:	iKOOP.com.my
********************************************************************************/
include("common.php");	
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>'.$emaNetis.'</title>
<script>';

if (get_session("Cookie_groupID") == '') print 'history.forward();';

print '
window.status="Sistem Keanggotaan Koperasi versi 1.01";
</script>
<meta name="Keywords"  content="'.$siteKeyword.'">
<meta name="Description" content="'.$siteDesc.'">
<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<LINK rel="stylesheet" href="images/default.css" >
</head>
';
if (get_session("Cookie_groupID") == ''){
print'
<frameset rows="82,*,22" cols="*" frameborder="NO" border="0" framespacing="0">
<frame src="header2.php" name="topFrame" scrolling="NO" noresize title="topFrame" >
<frameset cols="1600,*" frameborder="no" border="3" framespacing="0">';
print '<frame src="mainpage.php" name="mainFrame"  title="mainFrame">';
}
if (get_session("Cookie_groupID") !== '') {
print'
<frameset rows="82,*,22" cols="*" frameborder="NO" border="0" framespacing="0">
<frame src="header2.php" name="topFrame" scrolling="NO" noresize title="topFrame" >
<frameset cols="230,*" frameborder="no" border="3" framespacing="0">
<frame src="leftpanel.php" name="leftFrame"  noresize title="leftFrame">';
print '<frame src="mainpage.php" name="mainFrame"  title="mainFrame">';
}
print '
</frameset>
<frame src="footer2.php" name="footerFrame" scrolling="NO" noresize title="footerFrame" >
</frameset>
</html>';
?>