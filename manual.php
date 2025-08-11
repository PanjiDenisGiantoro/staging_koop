<?php

/*********************************************************************************
 *          Project		:	iKOOP
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
include("header.php");

$sFileName = 'manual.php';
$sFileRef  = 'manual.php';
$title     = "Manual Pengguna";

if ($type == 'member') {
	$file = "manual_member.pdf";
	$title .= " - KEANGGOTAAN";
} elseif ($type == 'loan') {
	$file = "manual_loan.pdf";
	$title .= " - PEMBIAYAAN";
} elseif ($type == 'akaun') {
	$file = "manual_akaun.pdf";
	$title .= " - AKAUN";
} elseif ($type == 'admin') {
	$file = "manual_admin.pdf";
	$title .= " - PENGURUS";
} elseif ($type == 'dividen') {
	$file = "manual_dividen.pdf";
	$title .= " - DIVIDEN";
} elseif ($type == 'insuran') {
	$file = "manual_insuran.pdf";
	$title .= " - INSURAN";
} elseif ($type == 'potong') {
	$file = "manual_potonganGaji.pdf";
	$title .= " - POTONGAN GAJI";
} elseif ($type == 'kebajikan') {
	$file = "manual_kebajikan.pdf";
	$title .= " - KEBAJIKAN";
} else {
	$file = "manual.pdf";
}

print '
<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><h5 class="card-title"><i class="mdi mdi-book"></i>&nbsp;' . strtoupper($title) . ' &nbsp;</h5><p class="wrapURL" align="center"><a href="#" onclick="selectPop(\'' . $file . '\');"><strong>Buka dipaparan baru</strong></a></td>
	</tr>
	<tr>
		<td>Tidak dapat melihat paparan manual. Dapatkan Adobe Acrobat terbaru. <strong> <a href="http://ardownload.adobe.com/pub/adobe/reader/win/8.x/8.0/enu/pase30_rdr80_DLM_en_US.exe">Muat turun klik disini.</a> </strong></p></td>
	</tr>	
	';

print '
    <tr valign="top" >
		<td valign="top">
			<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG"><tr><td>
			<iframe id="ContentFrame" name="ContentFrame" scrolling="auto" frameborder="no" src="' . $file . '"  	onLoad="window.setTimeout(\'iFrameHeight(this)\',50);" style="width: 100%; height: 600px;" marginwidth="0" marginheight="0">
			</iframe>
			</td></tr></table></td></tr>';
print '</table></form></div>';
include("footer.php");
print '
<script>
function selectPop(rpt) {
	window.open(rpt,"pop","top=100, left=100, width=900, height=500, scrollbars=yes, resizable=yes, toolbars=no, location=no, menubar=no");	
}</script>';
