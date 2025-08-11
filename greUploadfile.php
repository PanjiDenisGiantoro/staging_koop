<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	greuploadfile.php
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
		print ' opener.document.location = "greFilePreview.php";
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
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />   	
</head>

<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" scroll="no" onload="self.focus();">

<form action="greUploadfile.php?dir='.$dir.'" method="post" enctype="multipart/form-data">
<h5 class="card-title">File Upload</h5>

<table border="0" cellspacing="1" cellpadding="3" align="center" class="table table-sm table-striped" style="font-size: 10pt;width: 95%;">                
                <tr class="table-primary">
	  <td>File</td>
	  <td class="inputs"><input type="file" class="form-controlx" name="uploadFile" size="35" value=""></td>  
	</tr>
        <tr class="table-light" align="right">
	  <td class="text" colspan=2><input name="action" class="btn btn-sm btn-primary" type="submit" value="     OK     "> 
	<input class="btn btn-sm btn-danger" type="button" value="  Cancel  " onclick="window.close();"></td>
	</tr>
</table>


</form>

</body>
</html>';

?>