<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	grefilePreview.php
 *          Date 		: 	13/06/2006
 *********************************************************************************/
if (!isset($fld)) 	 $fld = "";

include("common.php");

$dir = 'importd/';
if (!isset($_SERVER["PATH_TRANSLATED"]))
	$ServerDir = '';
else
	$ServerDir = $_SERVER["PATH_TRANSLATED"];
$ServerDir = str_replace(basename($ServerDir), "", $ServerDir);
$ServerDir = $ServerDir . $dir;

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == 'delete')
	$pk[] = $HTTP_POST_VARS["pk[]"];
for ($i = 0; $i < count($pk); $i++) {
	$deleteFile = $ServerDir . '/' . $pk[$i];
	unlink($deleteFile);
}
//--- End   : deletion based on checked box -------------------------------------------------------

$IDName = get_session("Cookie_userName");

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
<SCRIPT language="javascript">
	var sImagesPath  = "' . $dir . '";
	var sActiveImage = "" ;

	function ok(sActiveImage)	{	
		window.opener.document.FrmImport.fileimport.value = sActiveImage;	
		window.close() ;
	}
	
    function ADFActionButtonClick(v) {
	      e = document.ADFViewResults;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak dihapuskan.\');
	        } else {
	          if(confirm(count + \' rekod hendak dihapuskan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }	   
			
</SCRIPT>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />   	
</head>
<body leftmargin="5" rightmargin="5" topmargin="10" bottommargin="10" class="bodyBG">

<form name="ADFViewResults" action="' . $PHP_SELF . '" enctype="multipart/form-data" method="post">
<table border="0" cellspacing="1" cellpadding="3" width="95%" align="center" class="table table-sm">
	<h5 class="card-title">Senarai fail potongan anggota</h5>
	<tr class="Data">
		<td valign="top" height="100%" align="center">
			<table cellpadding="1" cellspacing="0" width="100%" class="table table-sm table-striped" style="font-size: 10pt;">
				<tr class="">
					<td colspan="4" align="center" height="30">
					<input type="button" class="btn btn-sm btn-secondary" value="Muat Naik Fail" onclick="Javascript:open(\'greUploadfile.php?dir=' . $dir . '\', \'upload\', \'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=0,width=450,height=100,left=200,top=200\');">';

// if ($IDName == 'admin'){
print '&nbsp; <input type="button" value="Hapus" onClick=Javascript:ADFActionButtonClick("delete") class="btn btn-sm btn-danger"> &nbsp;';
// }
print '<!--input type="submit" class="btn btn-sm btn-success" value="Refresh"-->
					</td>				
				</tr>
				<tr class="table-primary">
					<td align="center">&nbsp;</td>
					<td align="left"><b>Nama</b></td>
					<td align="left" align="center"><b>Saiz</b></td>
					<td align="left" align="center"><b>Tarikh Terkini</b></td>
				</tr>';
$cnt = 0;
$ThisDir = opendir($dir);
while (false !== ($file = readdir($ThisDir))) {
	if ($file != "." && $file != "..") {
		$filename = $dir . '/' . $file;
		$cnt++;
		if (!is_dir($filename)) {
			print	'
				<tr class="data">
					<td>' . $cnt . '</td>
					<td align="left"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $file . '">
						<a href=# onclick=Javascript:ok("' . $file . '")>' . $file . '</a>
					</td>
					<td align="left">' . filesize($filename) . '</td>
					<td align="left">' . date("M d Y H:i:s a", filemtime($filename)) . '</td>
				</tr>';
		}
	}
} //	while (false !== ($file = readdir($ThisDir))) { 
closedir($ThisDir);
if ($cnt == 0) {
	print ' <tr class="textFont"><td align="center" colspan="4" height="30">- < Tiada Rekod > - </td></tr>';
}
print '		</table>
		</td>
	</tr>';

print '
</table>
</form>
</body>
</html>';
