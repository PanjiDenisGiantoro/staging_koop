<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	cmsMain.php
*          Date 		: 	12/12/2008
*********************************************************************************/
include ("header.php");	
include ("koperasiQry.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2  OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$postDate = date("Y-m-d H:i:s");     
$postBy 	= get_session("Cookie_userName");
	
if ($_POST['Submit'] == "Simpan") {
	if ($_POST['id_q']) {
		$update = 	"UPDATE kandungan"
					." SET"
					." tajuk = '".$txtTajuk."',"
					." kandungan = '".CheckQuotes($txtkandungan)."',"
					." postedBy = '".$postBy ."'"
					." WHERE id = ".$id_q;

		$conn->Execute($update);
		print '<script>
		window.location = "cmsView.php?id='.$id_q.'";
		</script>';
		exit;
	} else {
		$insert = "INSERT INTO kandungan (tajuk, kandungan, postedDate, postedBy) VALUES ('".
		$txtTajuk."', '".CheckQuotes($txtkandungan)."', '".$postDate."', '".$postBy."')";
			
		print $insert;
		$rs = &$conn->Execute($insert);
		$getMax = "SELECT MAX(ID) as id FROM kandungan";
		$rsMax = $conn->Execute($getMax);
		$max = $rsMax->fields(id);
		print '<script>
		window.location = "cmsView.php?id='.$max.'";
		</script>';
		exit;
	}
}

if ($_GET['id']) {
	$id = $_GET['id'];
	$sql = "SELECT * FROM  kandungan WHERE ID = ".$id;
	$rs=$conn->Execute($sql);
	$tajuk = $rs->fields(tajuk);
	$kandungan = $rs->fields(kandungan);
}

$title     = 'Tambah Buletin';
?>
<!--- Begin  ------------------------------------------------------------------------------------------>
<div class="maroon" align="left"><b>&nbsp;<?=strtoupper($title)?></b></div>
<div style="width: 600px; text-align:left">
<div>&nbsp;</div>
<form action="cmsMain.php?id=<?=$id?>&action=save" method="post" name="frmInput">
<table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">
    <input type="hidden" name="id_q" value="<?=$id?>">
	<tr> 
      <td width="10%" height="18" align="right"><b>Tajuk</b></td>
      <td width="90%"><input type="text" name="txtTajuk" value="<?=$tajuk?>" size="80" maxlength="256"></td>
    </tr>
	<tr> 
      <td align="right" valign="top"><b>Kandungan</b></td>
      <td align="left"><textarea name="txtkandungan" cols="80" rows="40"><?=htmlspecialchars($kandungan);?></textarea></td>
    </tr>
	<tr> 
      <td height="20" colspan="2" valign="top"><div align="center"> 
		<?
		if($id) {
		?>
			<input type="submit" name="Submit" value="Simpan">
			<input type="button" name="Submit4" value="Papar" onClick="print1('cmsView.php?id=<?=$id?>')">
		<?
		} else {
		?>
			<input type="submit" name="Submit" value="Simpan">
		<?
		}
		?>
        </div></td>
    </tr>
</table>						
&nbsp;
</form>
</div>
<!--- End --------------------------------------------------------------------------------------------->
<?
print '<script>function print1(url) {	window.location = url;	}</script>';
include("footer.php");	
?>