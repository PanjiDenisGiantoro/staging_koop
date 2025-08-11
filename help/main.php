<?
$Kandungan = "Kandungan";
$month = date("m");;
$year = date("Y");
$conn->debug =true;

if ($_GET['jenis']) {
	$jenis;
	$month = $bulan;
	$year = $tahun;
	
if ($jenis=="kandungan") 
$kandungan = "<b>Kandungan</b>";
	
$sqlkandungan ="SELECT * FROM kandungan";
$conn->Execute($sqlkandungan)or die("$sqlkandungan error :".mysql_error());

$sqltopik ="SELECT * FROM topik";
$conn->Execute($sqltopik)or die("$sqltopik error :".mysql_error());

$sqlhelp ="SELECT * FROM helpcontent";
$conn->Execute($sqlhelp)or die("$sqlhelp error :".mysql_error());



?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?
  print '
  <tr> 
    <td height="306" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="" background="">
 	 <tr class="blackTextS"> 
          <td height="10" valign="top"></td>
     </tr>';
 
  if ($jenis=="kandungan"){
  	if ($sub) $kad_sql = " WHERE menuID = ".$sub;
  	$sqlSumber = "SELECT * FROM kandungan ".$kad_sql." ORDER BY menu ";
	$rsSumber = $conn->Execute($sqlSumber);
	while (!$rsSumber->EOF) {
		print '
        <tr class="blackTextS"> 
          <td valign="top" colspan="2"> <font color=white><b>'.$rsSumber->fields(menu).'</b></font><br><br></td>
		</tr>';
		   $select = "SELECT a.*,b.*,c.* FROM ";
			//$conn->debug = true;
  			$rs = $conn->Execute($sql);
			print '<tr class="blackTextS"><td width="10">&nbsp;</td><td >';
		  if ($rs->RecordCount()>0) {
		  while (!$rs->EOF) {
		  	print  '<font color=white>&#149; <a href="page1.php?id='.$rs->fields(menuID).'" target="mainFrame">'.$rs->fields(menu).' </a> </font>';
		  	$rs->MoveNext();
			print '<br><br>';
		  }
		  } else {
		  	print '<font color=white>Tiada rekod</font>';
			print '<br><br>';
		  }
		  print '</td></tr>';
		$rsSumber->MoveNext();
	}
  }
  
  print '
    </table></td>
  </tr>'; 
  ?>
</body>
<script>
function papar(jenis, sub_jenis){
	document.frmSelect.action="left_admin.php?jenis="+jenis+"&sub="+sub_jenis;
	document.frmSelect.submit();
}
</script>

</html>
