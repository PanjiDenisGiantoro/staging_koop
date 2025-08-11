<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	
*          Date 		: 	
*********************************************************************************/

if (!isset($StartRec))	$StartRec= 1; 

include("header.php");	

$sFileName = 'manual.php';
$sFileRef  = 'manual.php';
$title     = "Manual Pengguna";

if($type=='member'){
	$file = "manual_tawaransah.pdf";
	$title .= " - Pengesahan Surat Tawaran";
}
else{
	$file = "manual_tawaransah.pdf";
}

print '
<form name="MyForm" action=' .$sFileName . ' method="post">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b><p class="wrapURL" align="center"><a href="#" onclick="selectPop(\''.$file.'\');"><strong>Buka dipaparan baru</strong></a></td>
	</tr>
	<tr>
		<td>Tidak dapat melihat paparan manual. Dapatkan Adobe Acrobat terbaru. <strong> <a href="http://ardownload.adobe.com/pub/adobe/reader/win/8.x/8.0/enu/pase30_rdr80_DLM_en_US.exe">Muat turun klik disini.</a> </strong></p></td>
	</tr>	
	';



print '
    <tr valign="top" >
		<td valign="top">
			<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG"><tr><td>
			<iframe id="ContentFrame" name="ContentFrame" scrolling="auto" frameborder="no" src="'.$file.'"  	onLoad="window.setTimeout(\'iFrameHeight(this)\',50);" style="width: 100%; height: 600px;" marginwidth="0" marginheight="0">
			</iframe>
			</td></tr></table>
		</td>
	</tr>';

print ' 
</table>
</form>';

include("footer.php");	

print '
<script>
function selectPop(rpt) {
	window.open(rpt,"pop","top=100, left=100, width=900, height=500, scrollbars=yes, resizable=yes, toolbars=no, location=no, menubar=no");	
}
</script>
';
?>