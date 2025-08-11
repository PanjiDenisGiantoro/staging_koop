<?
require_once 'class_file/class_list.php';
include "common.php"; 
//$conn->debug = true; 

if ($_GET['page'] == 'add') {
	$saveMode = "Simpan";
} else if ($_GET['page'] == 'update') {
	$saveMode = "Kemaskini";
}

$id = $_POST['id'];

if ($_POST) {
	$Menu = $_POST["txtmenu"];
	$parentMenuID = $_POST["txtparent"];
	$seqNB = $_POST["txtseqNB"];
	$httpLink = $_POST["txtlink"];
	$image = $_POST["txtimage"];
	$title = $_POST["txttitle"];
	$text = $_POST["txttext"];
	if ($_POST['simpan'] == "Simpan") {
		$query = "INSERT INTO kandungan(Menu,parentMenuID,httpLink,seqNB,title,image,text)".
			 "VALUES ('$Menu',$parentMenuID,'$httpLink',$seqNB,'$title', '$image','$text')";
	} else if ($_POST['simpan'] == "Kemaskini") {
		$query = "UPDATE kandungan SET Menu= '$Menu', parentMenuID = $parentMenuID, seqNB = $seqNB, httpLink = '$httpLink', title ='$title', image = '$image', text='$text' WHERE MenuID= ".$_POST['id'];	
	} else if ($_POST['simpan'] == "Duplicate") {
		$query = "INSERT INTO kandungan(Menu,parentMenuID,httpLink,seqNB,title,image,text)".
			 "VALUES ('$Menu',$parentMenuID,'$httpLink',$seqNB,'$title', '$image','$text')";
	}
	$conn->Execute($query) or die("error :".mysql_error());
	print "<script> window.location.href = 'page.php?idmenu=".$_POST['id']."'; </script>";
}

if ($_GET['id'] <> '') {
	$id = $_GET['id']; 
	$sSQL = "SELECT * FROM kandungan WHERE MenuID = ".$_GET['id'];
	$rs = $conn->Execute($sSQL);
	$MenuID = $rs->fields('MenuID'); 
	$Menu = $rs->fields('Menu');
	$parentMenuID = $rs->fields('parentMenuID');
	$httpLink = $rs->fields('httpLink');
	$seqNB = $rs->fields('seqNB');
	$title = $rs->fields('title');
	$image = $rs->fields('image');
	$text = $rs->fields('text');
//	$tambah = 2; 	
}

$root = '/demo/sekatarakyat/help/screenshots';
$strImgList = ScanImgDir($_SERVER['DOCUMENT_ROOT'].$root, $keyword, 1);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>usermanual management</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../images/default.css">
</head>
<body>
<form name="form1" method="post" action="savemenu.php">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="tambah" value="1">
<p align=center>
  <table height="70" border="0" align=center cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td width="20%" height="33" align="right" valign="top"><b>MenuID :</b></td>
	  <td width="1%"></td>
      <td width="*" valign="top"> <?=$MenuID?></td>
    </tr>
    <tr> 
      <td width="20%" height="33" align="right" valign="top"><b>Menu :</b></td>
	  <td width="1%"></td>
      <td width="*" valign="top"> <input name="txtmenu" type="text" id="txtmenu" value="<?=$Menu?>" size="40" maxlength="40"></td>
    </tr>
    <tr> 
      <td width="20%" height="35" align="right" valign="top"><b>Parent Menu ID :</b></td>
 	  <td width="1%"></td>
      <td width="*" valign="top"><input name="txtparent" type="text" id="txtparent" value="<?=$parentMenuID?>" size="10" maxlength="10"></td>
    </tr>
    <tr> 
      <td width="20%" height="35" align="right" valign="top"><b>Sequence Number :</b></td>
 	  <td width="1%"></td>
      <td width="*" valign="top"><input name="txtseqNB" type="text" id="txtseqNB" value="<?=$seqNB?>" size="10" maxlength="10"></td>
    </tr>
    <tr> 
      <td width="20%" height="35" align="right" valign="top"><b>http Link :</b></td>
 	  <td width="1%"></td>
      <td width="*" valign="top"><input name="txtlink" type="text" id="txtlink" value="<?=$httpLink?>" size="40" maxlength="40"></td>
    </tr>
    <tr> 
      <td width="20%" height="35" align="right" valign="top"><b>Title :</b></td>
 	  <td width="1%"></td>
      <td width="*" valign="top"><input name="txttitle" type="text" id="txttitle" value="<?=$title?>" size="60" maxlength="128"></td>
    </tr>
    <tr> 
      <td width="20%" height="35" align="right" valign="top"><b>image/page :</b></td>
 	  <td width="1%"></td>
      <td width="*" valign="top"><?=SelectForm('txtimage', $image, $strImgList, $strImgList, '')?></td>
    </tr>
    <tr> 
      <td width="20%" height="35" align="right" valign="top"><b>content :</b></td>
 	  <td width="1%"></td>
      <td width="*" valign="top"><textarea name="txttext" cols="60" rows="20"><?=$text?> </textarea></td>
    </tr>
  </table>
	<p align= center>
	<input name="simpan" type="submit" id="simpan" value="<?=$saveMode?>"></font>
	<input name="simpan" type="submit" id="simpan" value="Duplicate"></font>
	</p>
</p>
</form>
</body>
</html>
<?
function SelectForm($strName_, $strValue_, $strNameList_, $strValueList_, $strEtc_) {
	$strTemp_ = '<select name="'.$strName_.'"';
	if ($strEtc_ <> '') {
		$strTemp_ .= ' '.$strEtc_;
	}
	$strTemp_ .= '>';

	for ($i = 0; $i < count($strNameList_); $i++){
		$strTemp_ .= '<option value="'.$strValueList_[$i].'"';
		if ($strValue_ == $strValueList_[$i]) {
			$strTemp_ .= ' selected="selected"';
		}
		$strTemp_ .= '>'.$strNameList_[$i].'</option>';
	}
	$strTemp_ .= '</select>';

	return $strTemp_;
}

function ScanImgDir($dir_, $keyword_, $sortBy_) {
	$dh  = opendir($dir_);
	$strFiles_[] = '';
	while (false !== ($strFileName = readdir($dh))) {
		if (($strFileName <> '.' AND $strFileName <> '..') AND ($keyword_ == '' OR ereg(strtolower($keyword_), strtolower($strFileName)))) {
			if (!is_dir($strFileName)) {
				$strFiles_[] = $strFileName;
			}
		}
	}

	switch ($sortBy_) {
		case 1 : sort($strFiles_); break;
		case 2 : asort($strFiles_); break;
		case 3 : ksort($strFiles_); break;
		case 4 : krsort($strFiles_); break;
		case 5 : rsort($strFiles_); break;
		case 6 : usort($strFiles_); break;
		case 7 : uksort($strFiles_); break;
		case 8 : natsort($strFiles_); break;
		default : natcasesort($strFiles_); break;
	}

	$strResult_ = $strFiles_;
	return $strResult_;
}
?>