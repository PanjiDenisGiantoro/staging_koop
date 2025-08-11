<? 
require_once 'class_file/class_list.php';
include "common.php"; 

//$conn->debug=true;

if ($_POST['tambah']==1) {

	if($_POST['id']=='')
	{
	$Menu = $_POST["txtmenu"];
	
	$query = "INSERT INTO kandungan(Menu)".
			 "VALUES ('$Menu')";
	$conn->Execute($query) or die("error :".mysql_error());
	
	}
	
	if($_POST['id']<>'')
	{
	$Menu = $_POST["txtmenu"];
	
	$query = "UPDATE kandungan SET Menu= '$Menu', WHERE MenuID= ".$_POST['id'];
	$conn->Execute($query) or die("error :".mysql_error());
	
	}
}

$defaultSusun = " ORDER BY MenuID";
$SQL_asal = "SELECT * FROM kandungan ";
$defaultSQLPaging = "SELECT COUNT(MenuID) AS bil_rekod FROM kandungan ";

$carianSQL = "SELECT * FROM kandungan ";
$carianSQLPaging = "SELECT COUNT(MenuID) AS bil_rekod FROM kandungan ";


$fieldCarian = array("Menu");
$labelCarian = array("Menu");

$column_field = array("Menu","");
$column_label = array("Menu","");

$column_width = array(200, 100);
$column_align = array("left", "left");
$columnType = array("TEXT","DELETE");
$link = array("kandungan1.php","kandungan1.php");
$target = array("_self","_self");
$keyColumn = array("MenuID","MenuID");
$sortby = "MenuID";
$btnLabel = "Tambah";
$btnLink = "kandungan1.php?add=1";
$btnSubmitLabel = "";
$formAction = "";
$sqlDelete = "DELETE FROM kandungan WHERE MenuID ";

$table = new MyList();
$table->setPageName("kandungan1.php");
$table->setOrder($defaultSusun);
$table->setDefaultSQL ($SQL_asal , $defaultSQLPaging);
$table->setCarian($carianSQL, $carianSQLPaging);
$table->setTajuk("Kandungan");
$table->setTable("100%");
$table->setFieldCarian($fieldCarian, $labelCarian);
$table->setColumnHeader($column_field, $column_label, $column_width, $column_align);
$table->setColumnType($columnType);
$table->setColumnLink($link, $target, $keyColumn);
$table->setDefaultSort($sortby);
$table->setButtonAdd($btnLabel, $btnLink);
$table->setSubmit($btnSubmitLabel, $formAction);
$table->delete_function($sqlDelete);
$table->displayTableTop();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>KandunganID</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<hr>
<?
if ($_GET['add']<>'' || $_GET['id']<>'') {
if ($_GET['id']<>'') {
	$sql = "SELECT * FROM kandungan WHERE MenuID = ".$_GET['id'];
	$rs = $conn->Execute($sql);
	$Menu = $rs->fields(Menu);	
}
?>
<form name="form1" method="post" action="kandungan1.php">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="tambah" value="1">

  <p align=center></p>
  <table width="536" height="34" border="1" align=center cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <br>
    <p></p>
    <!--tr> 
      <td height="97"><b>MenuID</b></td>
      <td valign="top"><label> 
        <textarea name="txtmenuid" rows="5" cols="40" value="<?=$MenuID?>"></textarea>
        </label></td>
      <td></td>
    </tr-->
    
    <tr> 
      <td height="32"><div align="center"><strong>Menu</strong></div></td>
      <td valign="top"><input name="txtmenu" type="text" id="txtmenu" value="<?=$Menu?>" size="40" maxlength="40"></td>
      <td></td>
    </tr>
    <p> 
  </table>
	  <p align= center>
      <input name="simpan" type="submit" value="Simpan">
	  <!--input name="baru" type="reset" value="Tambah tahap baru" -->
</p>
</form>

<?  } ?>

</body>

</html>
