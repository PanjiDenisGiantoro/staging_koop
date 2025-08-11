<?
include "common.php";
//include "auth.php";
include "traceLog.php";
$folder = "../picture";
//$conn->debug =true;

$sqlA = "SELECT * FROM helpcontent";
$rsA = $conn->Execute($sqlA);

if ($_POST['action']=="Simpan") {
$msg="";
$msg1="";

 if ($_FILES['userfile']['error']) {
   $msg1 = "Tiada fail imej pertama dibaca";
  } 
  else {
  	if (file_exists($folder."/".$_FILES['userfile']['name'])) {
		if ($_FILES['userfile']['size']>0) 
	    $msg1 = "Nama Fail Imej Pertama telah wujud. Sila gunakan nama lain";
  	} else {
    	if  (is_uploaded_file($_FILES['userfile']['tmp_name'])){
			move_uploaded_file($_FILES['userfile']['tmp_name'], $folder."/".$_FILES['userfile']['name']);
			chmod($folder."/".$_FILES['userfile']['name'], 0755); 
       //		$size = $_FILES['userfile']['size'];
			$picture = $folder."/".$_FILES['userfile']['name'];
			//$now = date("Y-m-j");
		} else { 
			$msg1 =  "Fail Imej Pertama Tidak Berjaya di Muat Naik";	
    	} 
    }
  }
	
	if (!$picture) $picture = $_POST['txtpicture'];
	
	$insert = "INSERT INTO helpcontent (text,picture) 
			   VALUES('".$txtText."','".$picture."')";
				//$conn->debug =true;
				
	if ($conn->Execute($insert)) { 
	$get = "SELECT MAX(ID) AS idhelp FROM helpcontent";
  	$rsGet = $conn->Execute($get);
  	$idhelp = $rsGet->fields(idhelp);
	traceLog($insert, "Masukkan help ID = ".$idhelp); 
  	$msg  = "Data telah disimpan"; 
  	print ' 	<script>
  					window.location = "upload.php?id='.$idhelp.'&msg='.$msg.'&msg1='.$msg1.'";
  				</script>';
	} else {
		$msg = "Data tidak dapat disimpan.";
	}

}

if ($_POST['action']=="Kemaskini"){
		$update = "UPDATE helpcontent SET
				        text = '".$txtText."'";
				   
		if (!$_FILES['userfile']['error']) 
			if (!file_exists($folder."/".$_FILES['userfile']['name'])) {
				if (is_uploaded_file($_FILES['userfile']['tmp_name']))
					if  (move_uploaded_file($_FILES['userfile']['tmp_name'], $folder."/".$_FILES['userfile']['name'])==true) {
						$picture = $folder."/".$_FILES['userfile']['name'];
						$setpicture = " , picture = '".$picture."'";
						}
			} else
			if ($_FILES['userfile']['size']>0) 
			$msg1 = "Nama fail imej pertama telah wujud. Sila gunakan nama lain";
			
	
		$where = " WHERE ID = ".$ID;
		if (!$setpicture){
		//echo "Imej 1:".$_POST['txtImej1'];
		$setpicture = ", picture = '".$_POST['txtpicture']."'";
		}
		
		$update = $update . $setpicture. $where;
		
		traceLog($update, "Kemaskini rekod ID = ".$ID);
		//$conn->debug = true;
		$conn->Execute($update);
		$msg  = "Data telah disimpan"; 
  		print ' 	<script>
  						window.location = "upload.php?id='.$ID.'&msg1='.$msg1.'";
  					</script>';
}

if ($_GET['id']) {
	$sql = "SELECT  * 
	FROM helpcontent
	WHERE ID =".$id;
			//$conn->debug=true;
	$rs = $conn->Execute($sql);
	$txtText = $rs->fields(text);
	$txtpicture = $rs->fields(picture);
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
<form action="upload.php?id=<?=$id?>" method="post" enctype="multipart/form-data" name="frmUpload">
<input type="hidden" name="id" value="<?=$id?>">
  <table width="806" border="0" cellpadding="0" cellspacing="0" class="blackText" align="center">
    <!--DWLayoutTable-->
     <? if ($txtpicture) print'
    <tr> 
      <td height="51" colspan="2" valign="top"><img src="'.$txtpicture.'" alt="'.$txtText.'" border="0" width="600"></td>
    </tr>';
	?>
    <tr bgcolor="#00CC99"> 
      <td height="26" colspan="2" valign="middle"> <div align="left">&nbsp;<strong>MUAT 
          NAIK<font color="#000066"></font></strong></font></div></td>
    </tr>
    <tr> 
      <td height="18" colspan="2" valign="bottom"><div align="center"><font color=red> 
          <? 
	  	if ($msg1) {print $msg1;
		print '<br>'; }
		if ($msg) print $msg;
	 ?>
          </font></div></td>
    </tr>
    <tr> 
      <td width="55" height="170" valign="top"> <div align="right">Text : </div></td>
      <td width="751" valign="top"><textarea name="txText" cols="80" rows="10" class="formObj"><h2></h2><?=$txText?></textarea></td>
    </tr>
    <tr> 
      <td height="15"></td>
      <td></td>
    </tr>
    <tr> 
      <td height="26" valign="top"><div align="right">Picture:</div></td>
      <td valign="top"><input name="txtpicture" type="text" class="formObj" value="<?=$txtpicture?>" readonly size="30"> 
        <input type="button" name="Button" value="Pilih Imej" class="formObj" onClick="open_list('txtpicture')"> 
        <input type="hidden" name="picture" id="picture" value="<?=$txtpicture?>"> 
        <input name="userfile" type="file" class="formObj" id="userfile"  size="60"></td>
    </tr>
    <tr> 
      <td height="15"></td>
      <td></td>
    </tr>
    <tr> 
      <td height="20" colspan="2" valign="top"><div align="center"> 
          <? if ($id) { //print '
          	//<input type="submit" name="action" value="Kemaskini" class="formObj">';
			print '<img src="images/save.gif" onClick="submitForm()" style="cursor:hand">';
			print '<input type="hidden" name="action" value="Kemaskini">';
			}
		 else { print'
		 	<img src="images/save.gif" onClick="submitForm()" style="cursor:hand">';
			print '<input type="hidden" name="action" value="Simpan">';
		   	//<input type="submit" name="action" value="Simpan" class="formObj">';
			}
		?>
        </div></td>
    </tr>
  </table>
</form>
</body>
<script language="JavaScript">
function submitForm()
{
	
	document.frmUpload.submit();
}


function open_list(formObj) {
	window.open("pilih_images.php?fobj="+formObj);
}
</script>
</html>


