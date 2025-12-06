<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	cmsView.php
*          Date 		: 	29/03/2006
*********************************************************************************/
include("header.php");	
include("koperasiQry.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2  OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

if(!isset($pk)) $pk = 0;
$title     = 'Laman Utama';
$sql = "SELECT * FROM kandungan WHERE ID = ".$id;
$rs = $conn->Execute($sql);

?>
<div class="maroon"><a class="maroon" href="mainpage.php"><?=strtoupper($title)?></a><b>&nbsp;>&nbsp;<?=strtoupper('Buletin');?></b></div>
<div>&nbsp;</div>
<div class="maroon"><b>TOPIK : <?=strtoupper($rs->fields(tajuk))?>&nbsp;</b></div>
<hr size="1" />
<!--div class="blue" align="right">
<b>
<?
  if (get_session("Cookie_groupID") == 1 OR get_session("Cookie_groupID") == 2) {
  	print '[<a href="cmsMain.php"><b>baru</b></a>|<a href="cmsMain.php?id='.$rs->fields(ID).'"><b>ubah</b></a>|<a href="cmsMain.php?del='.$rs->fields(ID).'&action=delete" onClick="return confirm(\'Anda pasti padam kandungan ini?\')"><b>padam</b></a>]';
  } else {
  	print '&nbsp;';
  }
?>
</b>
</div-->
<div>&nbsp;</div>
<div style="width: 700px; text-align:left">
<p><?=$rs->fields(kandungan);?></p>
<p>
<table cellpadding="0" cellspacing="0">
	<tr><td><b>Oleh</b></td><td>&nbsp;:&nbsp;</td><td><?=$rs->fields(postedBy);?></td></tr>
	<tr><td><b>Tanggal</b></td><td>&nbsp;:&nbsp;</td><td><?=todate('/',$rs->fields(postedDate));?></td></tr>
</table>
</p>
</div>
<hr size="1" />
<!--div class="blue" align="right"><b>
<?
  if (get_session("Cookie_groupID") == 1 OR get_session("Cookie_groupID") == 2) {
  	print '[<a href="cmsMain.php"><b>baru</b></a>|<a href="cmsMain.php?id='.$rs->fields(ID).'"><b>ubah</b></a>|<a href="kandunganList.php?del='.$rs->fields(ID).'&action=delete" onClick="return confirm(\'Anda pasti padam kandungan ini?\')"><b>padam</b></a>]';
  } else {
  	print '&nbsp;';
  }
?>
</b></div-->
<?
include("footer.php");
?>	