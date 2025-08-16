<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	checkIC.php
*          Date 		: 	29/03/2004
*********************************************************************************/
//include("common.php");	
include("koperasiQry.php");

if(!isset($pk)) $pk = 0;
$pk = get_session('Cookie_userID');
//if(!isset($no_resit)) $no_resit = 0;

$title     = 'Upload Gaji';
$max_size = "1048576"; // Max size in BYTES (1MB)

if ($action == 'upload')
{
	$filename = $_FILES["filename"]["name"];
	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	$filesize = $_FILES["filename"]["size"];
	$allowed_file_types = array('.doc','.docx','.rtf','.pdf','.jpg','.png','.gif');	

	if (in_array($file_ext,$allowed_file_types) && ($filesize < 1048576))
	{	
		// Rename file
		$newfilename = md5($file_basename) . $file_ext;
		if (file_exists("upload_gaji/" . $newfilename))
		{
			// file already exists error
			echo "You have already uploaded this file.";
		}
		else
		{		
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_gaji/" . $newfilename);
			
		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";		
		$sSQL	= "UPDATE userloandetails SET " .
		         " gaji_img= '" . $newfilename . "' " ;
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
				
			//copy($_FILES["filename"]["tmp_name"],"upload_resit/".$_FILES["filename"]["name"]) or die("<b>Unknown error!</b>");
			echo "File uploaded successfully.";	
			print '<script language="javascript">';
	if($pk) {
		print 'window.location.href="?vw=biayaSahMember"';
	//}
	}else {print 'window.location.href="?vw=biayaSahMember"';}
	print '</script>';	
		}
	}
	elseif (empty($file_basename))
	{	
		// file selection error
		echo "Please select a file to upload.";
	} 
	elseif ($filesize > 1048576)
	{	
		// file size error
		echo "The file you are trying to upload is too large.";
	}
	else
	{
		// file type error
		echo "Only these file typs are allowed for upload: " . implode(', ',$allowed_file_types);
		unlink($_FILES["file"]["tmp_name"]);
	}
}




print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">

<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td	 colspan="2"><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	<tr>
		<td><br>
		<table border="0" cellpadding="3" cellspacing="1" width="100%" align="center" class="lineBG">
			<tr>
				<td class=Header>Sila Masukkan file Gaji Anda: '.$no_resit.'</td>
			</tr>
			<tr>
				<td class="Data">
					<table width="100%">
						<tr>
							<form action="?vw=uploadwingajiP&action=upload" method=post  enctype="multipart/form-data">
							File (max size: '.$max_size.' bytes/'.($max_size/1024).' kb):<br>
							<input type="file" name="filename"><br>
							<input type="hidden" name="action" value="upload">
							<input type="hidden" name="pk" value="'.$pk.'">
							<input type="hidden" name="update" value="'.$up.'">
							<input type="submit" value="Upload File">
							</form>					
						</tr>			
					</table>
				</td>
			</tr>
		</table>		
		</td>
	</tr>
</table>';

print $detail;
include("footer.php");

?>	