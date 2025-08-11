<?
class MyList {

function setPageName($pageName) {
	$this->page = $pageName;
}

function setOrder($defaultSusun) {
	$this->defaultSusun = $defaultSusun;
}

function setDefaultSQL($SQL_asal , $defaultSQLPaging) {
	$this->SQL_asal  = $SQL_asal;
	$this->defaultSQLPaging = $defaultSQLPaging;
}

function setCarian($carianSQL, $carianSQLPaging) {
	$this->carianSQL = $carianSQL;
	$this->carianSQLPaging = $carianSQLPaging;
}

function setTajuk($tajuk) {
	$this->tajuk = $tajuk;
}

function setTable($width="800", $bgcolor="#CCCCCC", $header_bgcolor = "#C9C9B8", $color1="#FFFFFF", $color2="#F2F2F2") {
	$this->tblbgcolor = $bgcolor;
	$this->tblwidth = $width;
	$this->color1 = $color1;
	$this->color2 = $color2;
	$this->header_bgcolor = $header_bgcolor;
}

function setFieldCarian($fieldCarian, $labelCarian) {
	$this->field_cari = $fieldCarian;
	$this->label_cari = $labelCarian;
}

function setColumnHeader($column_field, $column_label, $column_width, $column_align) {
	$this->label_column = $column_label;
	$this->field_column = $column_field;
	$this->width_column = $column_width;
	$this->alignColumn = $column_align;
}

function setColumnLink($link, $target, $keyColumn) {
	$this->linkColumn = $link;
	$this->linkTarget = $target;
	$this->keyColumn = $keyColumn;
}

function setColumnType($type) {
	$this->columnType = $type;
}

function setDefaultSort($sortby) {
	$this->default_sort = $sortby;
}

function setButtonAdd($btnLabel, $btnLink) {
	$this->button_label = $btnLabel;
	$this->button_link = $btnLink;
}

function setSubmit($btnLabel, $action) {
	$this->submit_label = $btnLabel;
	$this->form_action = $action;
}

function delete_function($sql) {
	$this->deleteSQL = $sql;
}

function displayTableTop() {
global $conn;

foreach($_POST as $key=>$val){ $$key = $val; }
foreach($_GET as $key=>$val){ $$key = $val; }
//$conn->debug=true;

$carianSQL = $this->carianSQL . " WHERE ".$selectB." LIKE '%".$txtCari."%'";
$carianSQLPaging = $this->carianSQLPaging . " WHERE ".$selectB." LIKE '%".$txtCari."%'";
$defaultSusun = $this->defaultSusun;
$defaultSQL = $this->SQL_asal ;
$defaultSQLPaging = $this->defaultSQLPaging;
$page = $this->page;

//---------if carian-------------------------
if ($_POST['txtCari']) {
	$sql = $carianSQL;
	$sqlPaging = $carianSQLPaging;
} else {
	$sql = stripslashes($sql);
	$sqlPaging = stripslashes($sqlPaging);
}
//-------------------------------------------
//------------if delete----------------------
if ($_GET['del']) {
	$delete = $this->deleteSQL." = ".$del;
	$conn->Execute($delete);
}
//------------------------------------------
include "sql1.php";
include "paging1.php";
//require_once 'function.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><? echo $this->tajuk; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="styles.css" rel="stylesheet" type="text/css">
</head>

<body style="padding: 10px;">
<div class="maroon" align="left"><b><?=$this->tajuk;?></b></div>
<div>&nbsp;</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td width="745" height="25" valign="top"> <form action="<? echo $PHP_SELF; ?>" method="post">
              Cari 
              <input type="text" name="txtCari" class="inputbox">
              di bahagian 
              <select name="selectB" class="inputbox">
			  <?
			  $i=0;
			  foreach ($this->field_cari as $carian) {
			  	print '<option value="'.$this->field_cari[$i].'">'.$this->label_cari[$i].'</option>';
				$i++;
			  }
			  ?>
              </select>
              <input type="submit" name="Submit" value="Cari" class="butt">
            </form></td>
        </tr>
      </table></td>
  </tr>
  <tr><td width="800">&nbsp;</td></tr>
  <form action="<? echo $this->form_action; ?>" method="post">
  <tr> 
    <td valign="top"><table width="<? echo $this->tblwidth; ?>" border="0" cellpadding="1" cellspacing="1" bgcolor="<? echo $this->tblbgcolor; ?>">
        <!--DWLayoutTable-->
        <tr> 
		<? 
		$i=0;
		foreach ($this->label_column as $column) { ?>
          <td width="<? echo $this->width_column[$i]; ?>" height="30" valign="top" bgcolor="<? echo $this->header_bgcolor; ?>"><? echo $this->label_column[$i]; ?>
            <? if ($this->columnType[$i]=='TEXT') sort_image($this->field_column[$i], $pagenr); 
			   else print '';
			?>
          </td>
		 <? 
		 $i++;
		 } 
		 ?>
		</tr>
        <? while (!$rs->EOF) { 
		 if ($bgcolor == $this->color1)//#CBE0C7
		 	$bgcolor = $this->color2;
		 else
		 	$bgcolor = $this->color1;
		?>
        <tr> 
		<? 
		$x=0;
		foreach ($this->field_column as $column) { 		
			if ($this->columnType[$x]=='TEXT') {
        		if ($this->linkColumn[$x]<>'') 
					print '<td valign="top" bgcolor="'.$bgcolor.'" align="'.$this->alignColumn[$x].'">&nbsp;<a href="'.$this->linkColumn[$x].'?id='.$rs->fields($this->keyColumn[$x]).'" target="'.$this->linkTarget[$x].'">'.$rs->fields($column).'</a></td>';
				else
					print '<td valign="top" bgcolor="'.$bgcolor.'" align="'.$this->alignColumn[$x].'">&nbsp;'.$rs->fields($column).'</td>';
			} else if ($this->columnType[$x]=='DELETE') {
				if ($this->linkColumn[$x]<>'') 
					print '<td valign="top" bgcolor="'.$bgcolor.'" align="'.$this->alignColumn[$x].'">&nbsp;<a href="'.$this->linkColumn[$x].'?del='.$rs->fields($this->keyColumn[$x]).'" target="'.$this->linkTarget[$x].'" onClick="return confirm(\'Adakah anda pasti untuk memadam rekod?\')">Padam</a></td>';
				else
					print '<td valign="top" bgcolor="'.$bgcolor.'" align="'.$this->alignColumn[$x].'">&nbsp;Padam</td>';
			} else if ($this->columnType[$x]=='CHECKBOX') {
				print '<td valign="top" bgcolor="'.$bgcolor.'" align="'.$this->alignColumn[$x].'">&nbsp;<input type="checkbox" name="checkbox[]" value="'.$rs->fields($this->keyColumn[$x]).'"></td>';
			}
		$x++;
		} 
		?> 
	</tr>
	<?	
	$rs->MoveNext();
	} 
	?>
      </table></td>
  </tr>
  <tr>
    <td valign="middle">Halaman : 
      <?
	  if (!$fieldsort) $fieldsort=$this->default_sort;
	  include "paging2.php";
     ?>
    </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr> 
    <td valign="top">
	<? if ($this->submit_label<>"") print '<input type="submit" name="submit" value="'.$this->submit_label.'" class="butt">'; ?>
	<input type="button" name="Submit2" value="<? echo $this->button_label; ?>" onClick="add_new()" class="butt"></td>
    </tr>
  </form>
</table>
</body>
<? include "frmsort.php"; 
print '
<script>
function add_new() {
window.location = "'.$this->button_link.'";
}
</script>
</html>';
}

}
?>