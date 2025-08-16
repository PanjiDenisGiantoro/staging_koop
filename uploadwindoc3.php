<?php
/*********************************************************************************
*          Project		:	KPF2 MODUL PELABURAN
*          Filename		: 	uploadwindoc3.php
*          Date 		: 	26/09/2023
*********************************************************************************/
//include("common.php");	
include("koperasiQry.php");

//if(!isset($pk)) $pk = 0;
//$pk = get_session('Cookie_userID');
//if(!isset($no_resit)) $no_resit = 0;


$title     = 'Muat Naik Dokumen';
$max_size = "1048576"; // Max size in BYTES (1MB)

if ($action == 'upload')
{
	$filename = $_FILES["filename"]["name"];
	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	$filesize = $_FILES["filename"]["size"];
	$allowed_file_types = array('.doc','.docx','.rtf','.pdf','.jpg','.png','.gif','.rar', '.zip');	

	if (in_array($file_ext,$allowed_file_types) && ($filesize < 1048576))
	{	
		// Rename file
		$newfilename = md5($file_basename) . $file_ext;
		if (file_exists("upload_doc/" . $newfilename))
		{
			// file already exists error
			echo '<font color="red">Anda telah pun memuat naik fail ini.</font>';
		}
		else
		{		
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_doc/" . $newfilename);
			
		$sSQL = "";
		$sWhere = "";		
	    // $sWhere = "compID=" . tosql($pk, "Text");
		$sWhere = "ID=" . tosql($ID, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";		
		$sSQL	= "UPDATE investors SET " .
		         " doc3= '" . $newfilename . "' " ;
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
			echo "File $pk uploaded successfully.";	
			print '<script language="javascript">';
	if($pk) {
		print 'window.location.href="?vw=ACCinvestors_detail&mn=921&pk='.$pk.'&pic='.$newfilename.'&action=view"';
	//}
	}else {print 'window.location.href="?vw=ACCinvestors_detail&mn=921&pk='.$pk.'&pic='.$newfilename.'&action=view"';}
	print '</script>';	
		}
	}
	elseif (empty($file_basename))
	{	
		// file selection error
		echo '<font color="red">Sila pilih fail untuk dimuat naik.</font>';
	} 
	elseif ($filesize > 1048576)
	{	
		// file size error
		echo '<font color="red">Fail yang anda cuba muat naik terlalu besar.</font>';
	}
	else
	{
		// file type error
		echo '<font color="red">Hanya fail jenis ini dibenarkan untuk dimuat naik: ' . implode(', ',$allowed_file_types);'</font>';
		unlink($_FILES["file"]["tmp_name"]);
	}
}




print '
    <h4 class="card-title"><i class="fas fa-upload"></i>&nbsp;' . strtoupper($title) . '</h4>
                            <hr class="hr1 text-secondary">

    <input type="hidden" name="action">

    <div class="table-responsive">
                                                
        <table class="table mb-3">
                <tr class="table-primary">
                    <td class=Header><h6 class="card-subtitle">Sila Masukkan Dokumen Anda: '.$no_resit.'</h6></td>
                </tr>
                <tr class="table-light">
                        <td class="Data">
					
                                    <form action="?vw=uploadwindoc3&mn=921&pk='.$pk.'&ID='.$ID.'\'action=upload" method=post  enctype="multipart/form-data">
                                    File (max size: '.$max_size.' bytes/'.($max_size/1024).' kb):<br>
                                    <input type="file" class="form-control" name="filename"><br>
                                    <input type="hidden" name="action" value="upload">
                                    <input type="hidden" name="pk" value="'.$pk.'">
                                    <input type="hidden" name="update" value="'.$up.'">
									<center>
									<input type="button" class="btn btn-secondary waves-effect waves-light" value="<<" onClick="window.location.href=\'?vw=ACCinvestors_detail&mn=921\';">
                                    <input type="submit" class="btn btn-primary w-md waves-effect waves-light" value="Muat Naik Fail">                                    
									</center>
									</form>					
						
                        </td>
                </tr>
        </table></div>		
		';

print $detail;

?>	