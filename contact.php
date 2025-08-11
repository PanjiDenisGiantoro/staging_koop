<?

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	contact.php
 *********************************************************************************/
include("common.php");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields(name);
$address1 = $rss->fields(address1);
$address2 = $rss->fields(address2);
$address3 = $rss->fields(address3);
$address4 = $rss->fields(address4);
$noPhone = $rss->fields(noPhone);
$email = $rss->fields(email);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>' . $emaNetis . '</title>
<script>window.status="Sistem Keanggotaan iKOOP versi 1.01";
</script>
<meta name="Keywords" content="' . $siteKeyword . '">
<meta name="Description" content="' . $siteDesc . '">
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<LINK rel="stylesheet" href="" >
</head>
<body>
	<br>
	<div >
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p style="text-align:center;">Jika terdapat sebarang masalah, sila hubungi pejabat <b>[NAMA KOPERASI]</b> seperti tertera di bawah :</p>
	<p style="text-align:center;">	   
	<b>' . $coopName . '</b><br />
	' . $address1 . ',<br />
	' . $address2 . ',<br />
	' . $address3 . ',<br />
	' . $address4 . '.<br />
	TEL: ' . $noPhone . '<br />
	EMEL: ' . $email . '<br />
	</p>
	</div>
</body>
</html>';
