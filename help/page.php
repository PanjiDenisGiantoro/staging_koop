<?
include "common.php";

$idmenu = $_GET['idmenu'];

$id = "";
$httpLink = ""; 
$title = ""; 
$text = "";
$img = "";
$menu = "";

if (!$_GET['idmenu'])
{
	$idmenu = '72';
}

$sql = "SELECT * FROM kandungan WHERE MenuID = ".$idmenu;
$rs = $conn->Execute($sql);
$id = $idmenu;
$httpLink = $rs->fields(httpLink);
$title =$rs->fields(title);
$text = $rs->fields(text);
$img = $rs->fields(image);
$menu = $rs->fields(Menu);

$strTemp =
'<html>
<head>
<title>Help Content</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../images/default.css">
</head>
<body style="padding: 10px;">
<div class="maroon" align="left"><b>'.$title.'</b></div>';
$strTemp .= '<div>';
if ($title <> '') {
	$strTemp .= '<a href="savemenu.php?page=add">Tambah</a>&nbsp;|&nbsp;<a href="savemenu.php?page=update&id='.$id.'">Kemaskini</a>';
} else {
	$strTemp .= '&nbsp;';
}
$strTemp .= '</div>';
$strTemp .=
'<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr><td>&nbsp;</td></tr>
  <tr> 
    <td>';
	if ($httpLink <> '') {
		$strTemp .= '<div style="width: 100%; height: 400px;">';
		$strTemp .= '<!--div style="position: absolute; z-index: 1; width: 100%; height: 100%;">&nbsp;</div-->';
		$strTemp .= '<iframe width="100%" height="100%" frameborder="0" src="screenshots/'.$httpLink.'" style="border: 1px solid #000000"></iframe>';
		$strTemp .= '</div>';
	} else if ($img <> '') {
		$strTemp .= '<img src="screenshots/'.$img.'"';
		if ($_SERVER['QUERY_STRING'] <> '') {
			$strTemp .= ' border="1"';
		}
		$strTemp .= ' />';
//	} else {
//		$strTemp .= '<center><img src="../images/SEKATARAKYAT.gif" /></center>';
	}
$strTemp .= '
    </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr> 
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr>
          <td>'.$text.'</td>
        </tr>
      </table>
  </tr>
</table>
</body>
</html>';

print $strTemp;