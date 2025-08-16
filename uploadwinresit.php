<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	checkIC.php
*          Date 		: 	29/03/2004
*********************************************************************************/
//include("common.php");	
include("koperasiQry.php");

if(!isset($pk)) $pk = 0;
if(!isset($no_resit)) $no_resit = 0;

$title     = 'Upload Resit';
$max_size = "1048576"; // Max size in BYTES (1MB)
/*
if ($action == 'upload')
{
	$strFile = $_FILES["filename"]["name"];
	if($strFile == ''){
		print '<script>
					alert ("Tiada fail dimasukkan.");
					window.location.href="uploadwinresit.php";
				</script>';
	}
	if ($_FILES["filename"]["size"] > $max_size) die ("<b>File Terlalu Besar!  Sila cuba lagi...</b>");
	copy($_FILES["filename"]["tmp_name"],"upload_images/".$_FILES["filename"]["name"]) or die("<b>Unknown error!</b>");
	echo "<b>Fail Sudah Diterima.</b>"; 
	$strF = "upload_images/".$strFile;
	print '<script language="javascript">';
	if(!$no_resit) {
		print 'window.location.href="resit.php?no_resit='.$no_resit.'"';
	}else{
		if($update<>1) print 'window.location.href="resit.php?no_resit='.$no_resit.'&pic='.$strFile.'"&action=view'; else print 'window.location.href="resit.php?pic='.$strFile.'"';
	}
	print '</script>';

}
*/

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
		if (file_exists("upload_resit/" . $newfilename))
		{
			// file already exists error
			echo "You have already uploaded this file.";
		}
		else
		{		
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_resit/" . $newfilename);
			//copy($_FILES["filename"]["tmp_name"],"upload_resit/".$_FILES["filename"]["name"]) or die("<b>Unknown error!</b>");
			echo "File uploaded successfully.";	
			print '<script language="javascript">';
	if($no_resit) {
		print 'window.location.href="?vw=resit&mn=908&no_resit='.$no_resit.'&pic='.$newfilename.'&action=view"';
	//}
	}else {print 'window.location.href="?vw=resit&mn=908&no_resit='.$no_resit.'&pic='.$newfilename.'&action=view"';}
	print '</script>';	
		}
	}
	elseif (empty($file_basename))
	{	
		// file selection error
		//echo "Please select a file to upload.";
                                echo '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    Please select a file to upload.
                                                </div>';
	} 
	elseif ($filesize > 1048576)
	{	
		// file size error
		//echo "The file you are trying to upload is too large.";
                                echo '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    The file you are trying to upload is too large.
                                                </div>';
	}
	else
	{
		// file type error
		//echo "Only these file typs are allowed for upload: " . implode(', ',$allowed_file_types);
                                echo '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    Only these file typs are allowed for upload: '. implode(', ',$allowed_file_types).' 
                                                </div>';
		unlink($_FILES["file"]["tmp_name"]);
	}
}




print '
<input type="hidden" name="action">
<h4 class="card-title">' . strtoupper($title) . '</h4>
                            <hr class="hr1">
                                <div class="table-responsive">
		<table class="table mb-3">
			<tr class="table-success">
				<td class=Header>Sila Masukkan Foto anda: '.$no_resit.'</td>
			</tr>
			<tr class="table-light">
				<td class="Data">
					
                                                                            <form action="?vw=uploadwinresit&mn=908&action=upload" method=post  enctype="multipart/form-data">
                                                                            File (max size: '.$max_size.' bytes/'.($max_size/1024).' kb):<br>
                                                                            <input type="file" class="form-controlx" name="filename"><br>
                                                                            <input type="hidden" name="action" value="upload">
                                                                            <input type="hidden" name="pk" value="'.$pk.'">
                                                                            <input type="hidden" name="update" value="'.$up.'">
                                                                            <input type="hidden" name="no_resit" value="'.$no_resit.'">
                                                                            <input type="submit" class="btn btn-sm btn-primary mt-2" value="Upload File">
                                                                            </form>					
						
				</td>
			</tr>
		</table></div>		
		';

print $detail;
include("footer.php");

?>	