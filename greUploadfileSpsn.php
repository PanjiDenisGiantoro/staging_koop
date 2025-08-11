<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	greuploadfileSpsn.php
*          Date 		: 	13/06/2006
*********************************************************************************/
if (!isset($action)) $action = "";
if (!isset($dir)) 	 $dir = "";
if (!isset($msg)) 	 $msg = "";

$ListThisDir = $PATH_TRANSLATED;
$ListThisDir = str_replace(basename($PATH_TRANSLATED), "", $ListThisDir);
$ListThisDir .= $dir;

if ($action <> "") {
	$uploadFile =  $HTTP_POST_FILES['uploadFile']['tmp_name'];
//	$toFile = $ListThisDir.'\\'.$uploadFile_name;  
	$toFile = $ListThisDir.'/'.$uploadFile_name;   
	if ($uploadFile <> "none"){
		if (!copy( $uploadFile, $toFile ) ) 
             $msg =  'File could not be uploaded'; 
	} else
		$msg = 'File cannot be empty';
	print '<script>';
	if ($msg <> "") 
		print 'alert("'.$msg.'");';
	else {
		print ' opener.document.location = "greFilePreviewSpsn.php";
				window.close();';
	}
	print '</script>';
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Upload File</title>
<style>
  body, .button{background: buttonface; font-family:"MS Sans Serif";font-size:9pt;margin:5px;}
  .text {font-family:"MS Sans Serif";font-size:9pt;padding:5px 0px 5px 0px;}
  input{font-family:"MS Sans Serif";font-size:9pt;margin:0px;}
  .controls{width:100%;text-align:right;margin-top:5px;margin-right:3px}
</style>
</head>

<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" scroll="no" onload="self.focus();">

<form action="greUploadfileSpsn.php?dir='.$dir.'" method="post" enctype="multipart/form-data">
<fieldset title="File Upload"><legend>File Upload</legend>
<table>
	<tr>
	  <td class="text">File:</td>
	  <td class="inputs"><input type="file" name="uploadFile" size="35" value=""></td>  
	</tr>
</table>
</fieldset>
<div class="controls">
	<input name="action" class="button" type="submit" value="     OK     "> 
	<input class="button" type="button" value="  Cancel  " onclick="window.close();">
</div>
</form>

</body>
</html>';

?>