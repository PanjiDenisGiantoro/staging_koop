<?
if(!isset($_POST['upload'])) $_POST['upload']='';
if($_POST['upload']){
$msg1 = 'test';
$folder ="images";
 if ($_FILES['userfile']['error']) {
   $msg1 = "Tiada fail imej pertama dibaca";
  } 
  else {
  	if (file_exists($folder."/".$_FILES['userfile']['name'])) {
		if ($_FILES['userfile']['size']>0) 
	    $msg1 = "Nama Fail Imej telah wujud. Sila gunakan nama lain";
  	} else {
    	if  (is_uploaded_file($_FILES['userfile']['tmp_name'])){
			move_uploaded_file($_FILES['userfile']['tmp_name'], $folder."/".$_FILES['userfile']['name']);
			chmod($folder."/".$_FILES['userfile']['name'], 0777); 
       //		$size = $_FILES['userfile']['size'];
			$picture = $folder."/".$_FILES['userfile']['name'];
			//$now = date("Y-m-j");
			$msg1 =  "Fail Imej Berjaya di Muat Naik";
		} else { 
			$msg1 =  "Fail Imej Tidak Berjaya di Muat Naik";	
    	} 
    }
	print '<script language="javascript" type="text/javascript">
		alert(\''.$msg1.'\');
		</script>';
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="default.css">
</head>

<body bgcolor="#CCFFFF">
<form action="upload_images.php" method="post" enctype="multipart/form-data" name="frmUpload">
  <table width="806" border="0" cellpadding="0" cellspacing="0" class="blackText" align="center">
    <!--DWLayoutTable-->
    <tr bgcolor="#00CC99"> 
      <td height="26" colspan="2" valign="middle"> <div align="left">&nbsp;<strong>MUAT 
          NAIK<font color="#000066"></font></strong></font></div></td>
    </tr>

	<tr>
      <td height="26" valign="top"><div align="right">Picture:</div></td>
      <td valign="top">
        <input name="userfile" type="file" class="formObj" size="60"></td>
    </tr>
    <tr> 
      <td height="15"></td>
      <td></td>
    </tr>
    <tr> 
      <td height="20" colspan="2" valign="top"><div align="center"> 
          <? print'
		 	<input type="submit" name="upload" value="   Upload  " style="cursor:hand" class="formObj" >';
			print '<input type="hidden" name="action" value="Simpan">';
		   	//<input type="submit" name="action" value="Simpan" class="formObj">';
		?>
        </div></td>
    </tr>
  </table>
</form>
</body>
</html>


