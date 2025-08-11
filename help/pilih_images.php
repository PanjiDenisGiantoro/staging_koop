<?
include 'common.php';
$my_file_name_array = array();

if ($_GET['fileName']) {
  $fileName = $_GET['fileName'];
}
$objek = $_GET['fobj'];
echo $objek;



?>
<html>

<head>
  <title>Senarai Imej</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="stylesheet" type="text/css"
    href="../fontStyle.css" />
</head>

<body bgcolor="#FFFFCC">
  <table width="647" border="0" cellpadding="0" cellspacing="0" id="content">
    <!--DWLayoutTable-->
    <tr>
      <td width="28" height="19">&nbsp;</td>
      <td width="206" valign="top"><strong><? echo $fileName; ?></strong></td>
      <td colspan="3" rowspan="2" valign="top"><img src="../picture/<? echo $fileName; ?>" width="92" height="73"></td>
      <td width="29">&nbsp;</td>
      <td width="245">&nbsp;</td>
      <td width="27">&nbsp;</td>
      <td width="20">&nbsp;</td>
    </tr>
    <tr>
      <td height="54"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="4"></td>
      <td></td>
      <td width="41"></td>
      <td width="40"></td>
      <td width="11"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="21"></td>
      <td colspan="3" valign="top" bgcolor="#FFCC33">
        <font color="#000000" size="1" face="Verdana, Poppins, Helvetica, sans-serif">Nama
          Fail</font>
      </td>
      <td></td>
      <td></td>
      <td colspan="2" valign="top" bgcolor="#FFCC33">
        <font size="1" face="Verdana, Poppins, Helvetica, sans-serif">Nama
          Fail</font>
      </td>
      <td></td>
    </tr>
    <?
    /*  $result=mysql_query($sqlSelect) or die("sql error".mysql_error());
  while ($row=mysql_fetch_object($result)) {
  $saiz=$row->fileSize;
  $saiz=$saiz/1000;
  */
    if ($handle = opendir('../picture')) {
      //echo "Directory handle: $handle\n"; 
      //echo "Files:\n"; 
      $bgcolor = "#FFFFFF";
      /* This is the correct way to loop over the directory. */
      $x = 0;
      while (false !== ($file = readdir($handle))) {
        //	$fileSize = filesize($file);
        $file_array[$x] = $file;

        $x = $x + 1;
      }

      closedir($handle);
    }
    rsort($file_array);
    reset($file_array);

    $i = 0;
    $j = 0;
    //while ($file_array[$i]) {
    ?>
    <tr>
      <td height="2"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <?
    $j = $x / 2;
    $d = $j;
    while ($i < $j) {
      //while ($i<$x) {
      $file_name[$i] = $file_array[$i];
      //echo "$i = ".$file_name[$i];
    ?>
      <tr>
        <td height="19"></td>
        <td colspan="2" valign="top" bgcolor="<? echo $bgcolor; ?>">
          <font color="#0033CC" size="1" face="Verdana, Poppins, Helvetica, sans-serif"><a href="pilih_images.php?fileName=<? echo $file_name[$i]; ?>"><? echo $file_name[$i]; ?></a></font>
        </td>
        <td valign="top" bgcolor="<? echo $bgcolor; ?>" style="cursor:hand" onClick="insert('<? echo $file_name[$i]; ?>')">
          <div align="center">
            <font color="#0033CC" size="1" face="Verdana, Poppins, Helvetica, sans-serif">Pilih</font>
          </div>
        </td>
        <td></td>
        <?

        $d = $d + 1;
        //echo $d;
        $file_name[$d] = $file_array[$d];
        ?>
        <td></td>
        <td valign="top" bgcolor="<? echo $bgcolor; ?>">
          <font color="#0033CC" size="1" face="Verdana, Poppins, Helvetica, sans-serif"><a href="pilih_images.php?fileName=<? echo $file_name[$d]; ?>"><? echo $file_name[$d]; ?></a></font>
        </td>
        <td valign="top" bgcolor="<? echo $bgcolor; ?>" style="cursor:hand" onClick="insert('<? echo $file_name[$d]; ?>')">
          <div align="center">
            <font color="#0033CC" size="1" face="Verdana, Poppins, Helvetica, sans-serif">Pilih</font>
          </div>
        </td>
        <td></td>
      </tr>
    <?
      $i = $i + 1;
      //echo "$file\n"; 
      if ($bgcolor == "#FFFFFF")
        $bgcolor = "#EEF7EF";
      else
        $bgcolor = "#FFFFFF";
    }
    ?>
    <tr>
      <td height="4"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
</body>
<?
print '
<script language="JavaScript">
function insert(fileName)
{';
if ($objek == "txtpicture")
  print 'opener.frmUpload.txtpicture.value="../picture/"+fileName;';


print 'window.close();
}
</script>';
?>

</html>