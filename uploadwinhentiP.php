<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	checkIC.php
*          Date 		: 	29/03/2004
*********************************************************************************/
// include("common.php");	
include("setupinfo.php");	
if(!isset($pk)) $pk = 0;

$pk = get_session('Cookie_userID');
$title     = 'Informasi anggota';
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
		if (file_exists("upload_henti/" . $newfilename))
		{
			// file already exists error
			echo "You have already uploaded this file.";
		}
		else
		{		
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_henti/".$newfilename);
		echo "File uploaded successfully.";	

		print '<script language="javascript">';
	
if (get_session("Cookie_groupID") == 0){
	if($pk) {
		print 'window.location.href="?vw=memberApplyTP&mn=3&pk='.$pk.'&pic='.$newfilename.'&action=view"';
	}
}

if (get_session("Cookie_groupID") == 1 OR get_session("Cookie_groupID") == 2) {
	if($pk) {
		print 'window.location.href="?vw=memberApplyTP&mn=905&pk='.$pk.'&pic='.$newfilename.'&action=view"';
	}
}


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

print '<h4 class="card-title"><i class="fas fa-upload"></i>&nbsp;' . strtoupper($title) . '</h4>
<hr class="hr1 text-secondary">

<input type="hidden" name="action">
<div class="table-responsive">
                                                
            <table class="table mb-3">
                <tr class="table-primary">
                        <td class=Header><h6 class="card-subtitle">Sila Masukkan Dokumen Berhenti / Bersara Anda: '.$no_resit.'</h6></td>
                    </tr>
                    <tr class="table-light">
					<td class="Data">';
					if (get_session("Cookie_groupID") == 0) {
							print '<form action="?vw=uploadwinhentiP&mn=2&action=upload" method=post  enctype="multipart/form-data">
											File (max size: '.$max_size.' bytes/'.($max_size/1024).' kb):<br>
											<input type="file" class="form-control" name="filename"><br>
											<input type="hidden" name="action" value="upload">
											<input type="hidden" name="pk" value="'.$pk.'">
											<input type="hidden" name="update" value="'.$up.'">
											<center>
											<input type="button" class="btn btn-secondary waves-effect waves-light" value="<<" onClick="window.location.href=\'?vw=memberApplyTP&mn=2\';">
											<input type="submit" class="btn btn-primary w-md waves-effect waves-light" value="Muat Naik Fail">                                    
											</center>
											</form>';
					} else {
							print '<form action="?vw=uploadwinhentiP&mn=905&action=upload" method=post  enctype="multipart/form-data">
											File (max size: '.$max_size.' bytes/'.($max_size/1024).' kb):<br>
											<input type="file" class="form-control" name="filename"><br>
											<input type="hidden" name="action" value="upload">
											<input type="hidden" name="pk" value="'.$pk.'">
											<input type="hidden" name="update" value="'.$up.'">
											<center>
											<input type="button" class="btn btn-secondary waves-effect waves-light" value="<<" onClick="window.location.href=\'?vw=memberApplyTP&mn=905\';">
											<input type="submit" class="btn btn-primary w-md waves-effect waves-light" value="Muat Naik Fail">                                    
											</center>
											</form>';
					}
		print '</td>
			</tr>
		</table>		
		</div>';

print $detail;
include("footer.php");

?>	