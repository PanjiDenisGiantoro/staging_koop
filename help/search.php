<?
include "common.php";

if ($txtKey) {
$sqlKey =	" WHERE Menu LIKE '%".$txtKey."%' or text LIKE '%".$txtKey."%' ";
}

$search = "SELECT * FROM kandungan ".$sqlKey." ";
			
//$conn->debug = true;
$rsSearch = $conn->Execute($search);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../images/default.css">
</head>

<body style="padding: 10px;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="blackText">
  <!--DWLayoutTable-->
    <?
	if ($_POST) {
		?>
		<tr> 
		  <td height="23" colspan="2" valign="top" class="blueText" bgcolor="99FFFF"><p><img src="images/atb_search.gif" width="15" height="15"><font color="#006633">&nbsp;&nbsp;&nbsp;<strong>Hasil carian bagi ' <? echo $txtKey; ?> ' 
			. 
			<? if ($rsSearch->RecordCount()==0 && $rsSearch->RecordCount()==0) print '<font color=red>Tiada rekod ditemui</font>'; ?>
			 </strong></font> </p></td>
		  <br>
		</tr>
		<tr> 
		  <td height="20" colspan="2" valign="top" class="blueText"></td>
		</tr>
		<!--DWLayoutTable-->
	   <?
		if ($rsSearch->RecordCount()>0) {
			$bil = 1;
			
			while (!$rsSearch->EOF) { 
				if ($txtKey) {
					$text = stri_replace($txtKey, "<strong>".$txtKey."</strong>", '<a href="page.php?idmenu='.$rsSearch->fields(MenuID).'"><u>'.$rsSearch->fields(Menu)).'</u></a>'.stri_replace($txtKey, "<strong>".$txtKey."</strong>", $rsSearch->fields(text));
				} else {
					$text = '<a href="page.php?idmenu='.$rsSearch->fields(MenuID).'"><u>'.$rsSearch->fields(Menu).'</u></a>'.$rsSearch->fields(text);
				}
				?>
				<tr> 
				  <td width="28" valign="top"><?=$bil?>	. </td>
				  <td width="693" valign="top"><?=$text?></td>
				</tr>
				<tr>
				  <td height="12" colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
				</tr>
				<?
				$bil++;
				$rsSearch->MoveNext();
			}
		}
	}
	?>
</table>
</body>
</html>
