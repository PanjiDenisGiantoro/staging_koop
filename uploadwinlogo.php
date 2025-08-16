<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	uploadwinlogo.php
*          Date 		: 	29/03/2004
*********************************************************************************/
//include("common.php");	
include("koperasiQry.php");

$pk = get_session('Cookie_userID');
$title     = 'Muat Naik Gaji';
$max_size = "1048576"; // Max size in BYTES (1MB)

if (isset($_GET['action']) && $_GET['action'] == 'upload')
{
	$filename = $_FILES["filename"]["name"];
	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	$filesize = $_FILES["filename"]["size"];
	$allowed_file_types = array('.doc','.docx','.rtf','.pdf','.jpg','.png','.gif');	

	if (in_array($file_ext, $allowed_file_types) && ($filesize < $max_size))
	{	
		// Rename file
		$newfilename = md5($file_basename) . $file_ext;
		if (file_exists("upload_images/" . $newfilename))
		{
			// file already exists error
			echo '<font color="red">Anda telah pun memuat naik fail ini.</font>';
		}
		else
		{		
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_images/" . $newfilename);
			
			$sSQL = "";
			$sWhere = "";		
			$sWhere = "setupID=" . tosql(1, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";		
			$sSQL = "UPDATE setup SET logo= '".$newfilename."' ";
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);
			echo "File uploaded successfully.";
			
			// Redirect to the specified page
			echo '<script language="javascript">';
			echo 'window.location.href="?vw=settingcoop&mn=901";';
			echo '</script>';
			exit(); // Ensure the script stops here after redirection
		}
	}
	elseif (empty($file_basename))
	{	
		// file selection error
		echo '<font color="red">Sila pilih fail untuk dimuat naik.</font>';
	} 
	elseif ($filesize > $max_size)
	{	
		// file size error
		echo '<font color="red">Fail yang anda cuba muat naik terlalu besar.</font>';
	}
	else
	{
		// file type error
		echo '<font color="red">Hanya fail jenis ini dibenarkan untuk dimuat naik: ' . implode(', ', $allowed_file_types) . '</font>';
		unlink($_FILES["file"]["tmp_name"]);
	}
}

print '<h4 class="card-title"><i class="fas fa-upload"></i>&nbsp;' . strtoupper($title) . '</h4>
                            <hr class="hr1 text-secondary">
            <input type="hidden" name="action">

            <div class="table-responsive">
                                                
                        <table class="table mb-3">
                            <tr class="table-primary">
                                <td class=Header><h6 class="card-subtitle">Sila Masukkan Logo Koperasi Anda:</h6></td>
                            </tr>
			
                        <tr class="table-light">
                            <td class="Data">
					
                                                    <form action="?vw=uploadwinlogo&mn=901&action=upload" method="post" enctype="multipart/form-data">
                                                    File (max size: '.$max_size.' bytes/'.($max_size/1024).' kb):<br>
                                                    <input type="file" class="form-control" name="filename"><br>
                                                    <input type="hidden" name="action" value="upload">
                                                    <input type="hidden" name="update" value="'.$up.'">
													<center>
													<input type="button" class="btn btn-secondary waves-effect waves-light" value="<<" onClick="window.location.href=\'?vw=settingcoop&mn=901\';">
                                                    <input type="submit" class="btn btn-primary w-md waves-effect waves-light" value="Muat Naik Fail">													
                                                    </center>
													</form>					
						
                            </td>
                    </tr>
            </table></div>		
            ';

print $detail;
include("footer.php");

?>	
