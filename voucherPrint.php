<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: voucherPaymentPrint.php
*			Date 		: 27/7/2006
*********************************************************************************/
include("common.php");
include("koperasiQry.php"); 
date_default_timezone_set("Asia/Jakarta");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields(name);
$address1 = $rss->fields(address1);
$address2 = $rss->fields(address2);
$address3 = $rss->fields(address3);
$address4 = $rss->fields(address4);
$noPhone = $rss->fields(noPhone);
$email = $rss->fields(email);
$koperasiID = $rss->fields(koperasiID);

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");top.location="index.php";</script>';
}

$header =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
.'<html>'
.'<head>'
.'<title>'.$emaNetis.'></title>'
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

$footer = '</body></html>';

$header .=
'<div align="right">BAUCER BAYARAN</div>'
.'<div align="right">NO. PV2124</div>'
.'<table border="0" cellspacing="0" cellpadding="0" width="100%">'
	.'<tr>'
		.'<td align="center" valign="middle" class="textFont">'
    . $coopName.'<br />'
    . $address1.',<br />'
		. $address2.',<br />'
		. $address3.',<br />'
		. $address4.'.<br />'
		. 'TEL: '.$noPhone.'<br />'
		. 'EMEL: '.$email.'<br />'
		.'</td>'
	.'</tr>'
.'</table>';

print $header;

?>

<table width="650" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td width="155" height="17"></td>
    <td width="131"></td>
    <td width="155"></td>
    <td width="209"></td>
  </tr>
  <tr> 
    <td height="115" valign="top"><img src="logoAB.gif" width="135" height="94"></td>
    <td colspan="2"><div align="center"><strong><font color="#999999" size="6">QUOTATION</font></strong></div></td>
    <td rowspan="2" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td width="220" height="127" valign="top"><p><strong>NetBase Enterprise 
              </strong><br>
              <font size="1">(001362385-V)</font><br>
              26, Jalan Semarak 5,<br>
              Taman Semarak,<br>
              43000 Kajang<br>
              Selangor Darul Ehsan<br>
              Tel : 03-87376510<br>
              <font color="#0000FF" size="2">http://www.mynetbase.net </font></p></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="33"></td>
    <td></td>
    <td></td>
  </tr>
  <tr> 
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" valign="top"><div align="left"><strong>Rujukan Quotation : 
        <?=$rs->fields(rujukan)?>
        </strong></div></td>
  </tr>
  <tr> 
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
    <!--td colspan="2" valign="top"><strong>Rujukan Invoice :</strong></td-->
  </tr>
  <tr> 
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
    <!--td colspan="2" valign="top"><strong>Rujukan Surat Akuan Terima :</strong></td-->
  </tr>
  <tr> 
    <td height="29" colspan="4" valign="top"><strong>TARIKH : <? echo substr($rs->fields(tarikh),8,2)." "; echo displayBulan(substr($rs->fields(tarikh),5,2))." "; echo substr($rs->fields(tarikh),0,4); ?> 
      </strong></td>
  </tr>
  <tr> 
    <td height="21">&nbsp;</td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td height="50" colspan="4" valign="top"><strong> 
      <?=$rs->fields(nama)?>
      </strong><br> <? echo $rs->fields(alamat); ?></td>
  </tr>
  <tr> 
    <td height="38" colspan="4" valign="top"><strong><br>
      U/P : 
      <?=$rs->fields(untukP)?>
      </strong></td>
  </tr>
  <tr> 
    <td height="26">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td height="18" colspan="4" valign="top"><b><u><? echo $rs->fields(tajuk); ?></u></b></td>
  </tr>
  <tr> 
    <td height="17"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr> 
    <td height="130" colspan="4" valign="top"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#999999" style="border-collapse: collapse">
        <!--DWLayoutTable-->
        <tr> 
          <td width="30" height="40" valign="top" bgcolor="#CCCCCC"><strong>NO</strong></td>
          <td width="328" valign="top" bgcolor="#CCCCCC"><strong>PERKARA</strong></td>
          <td width="78" valign="top" bgcolor="#CCCCCC"><div align="center"><strong>KUANTITI</strong></div></td>
          <td width="103" valign="top" bgcolor="#CCCCCC"><div align="center"><strong>HARGA 
              SEUNIT (RP)</strong></div></td>
          <td width="95" valign="top" bgcolor="#CCCCCC"><div align="center"><strong>HARGA 
              (RP) </strong></div></td>
        </tr>
        <?
		$getD = "SELECT * FROM my_q_details WHERE q_id = ".$rs->fields(id)." ORDER BY d_id";
		$rsD = $conn->Execute($getD);
		$jum=0;
		$no=1;
		while (!$rsD->EOF) {
		?>
        <tr> 
          <td height="24" valign="top" bgcolor="#FFFFFF"><div align="right"> 
              <?=$no?>
              .&nbsp;</div></td>
          <td valign="top" bgcolor="#FFFFFF"><? echo $rsD->fields(perkara); ?> 
            <p></p></td>
          <td valign="top" bgcolor="#FFFFFF"><div align="right"><? echo $rsD->fields(kuantiti); ?>&nbsp;&nbsp;&nbsp;</div></td>
          <td valign="top" bgcolor="#FFFFFF"><div align="right"><? echo number_format($rsD->fields(harga), 2, '.', ','); ?>&nbsp;&nbsp;&nbsp;</div></td>
          <td valign="top" bgcolor="#FFFFFF"><div align="right"> 
              <? $jumHarga = $rsD->fields(kuantiti) * $rsD->fields(harga); echo number_format($jumHarga, 2, '.', ','); ?>
              &nbsp;&nbsp;&nbsp;</div></td>
        </tr>
        <?
		$jum = $jum+$jumHarga;
		$no++;
		$rsD->MoveNext();
		} 
		?>
        <tr> 
          <td height="21" colspan="5" valign="top" bgcolor="#FFFFFF"><!--DWLayoutEmptyCell-->&nbsp;</td>
        </tr>
        <tr> 
          <td height="25" colspan="4" valign="top" bgcolor="#FFFFFF"><div align="right"><strong>JUMLAH</strong></div></td>
          <td valign="top" bgcolor="#FFFFFF"><div align="right"><? echo number_format($jum, 2, '.', ','); ?>&nbsp;&nbsp;&nbsp;</div></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="16"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr> 
    <td height="20" colspan="4" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="21" colspan="4" valign="top">* Harga adalah sah sehingga 30 hari 
      dari tarikh sebutharga</td>
  </tr>
  <tr> 
    <td height="29" colspan="4" valign="top"><strong>** Catatan : </strong><? echo $rs->fields(catatan); ?></td>
  </tr>
</table>
</body>
</html>

<?
print $footer;
?>
