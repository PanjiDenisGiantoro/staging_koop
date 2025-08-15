<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: accprintresit.php
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
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$header =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
.'<html>'
.'<head>'
.'<title>'.$emaNetis.'</title>'
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

$footer = '
<script>window.print();</script>
</body></html>';

if($id){
	$sql = "SELECT *
			FROM  resitacc 
			WHERE no_resit = '" . $id ."'";
	
	$rs = $conn->Execute($sql);
	$no_resit = $rs->fields(no_resit);
	$tarikh_resit = toDate("d/m/y",$rs->fields(tarikh_resit));
	$bayar_kod = $rs->fields(bayar_kod);
	$bayar_nama = $rs->fields(name);
	$no_anggota = $rs->fields(memberID);
	$cara_bayar = $rs->fields(cara_bayar);
	$tarikh = toDate("d/m/y",$rs->fields(tarikh));
	$akaun_bank = $rs->fields(akaun_bank);
	
	$bayaran_untuk = $rs->fields(bayaran_untuk);
	$catatan = $rs->fields(catatan);

	$master = $rs->fields(masteraccount);
	$masterA = dlookup("generalacc", "name", "ID=".$master);

	$sqltotal = "SELECT sum(pymtAmt) as tot FROM transactionacc WHERE docNo = '".$id."'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);
	
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = ".tosql($no_resit, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
}

$header .=
'<div align="right">RESIT RASMI</div>'
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
if($jumlah<>0){
	$clsRM->setValue($jumlah);
	$strTotal = ucwords($clsRM->getValue()).' Ringgit Sahaja.';
}
$jumlah = number_format($jumlah,2);

print 
'<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td colspan="5">&nbsp;</td></tr>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td>
				<td valign="top">:</td>
				<td><input class="Data" name="tarikh_resit" value="'.$tarikh_resit.'" type="text" size="20" maxlength="10" readonly/></td>
			</tr>
			<tr>
				<td valign="top" align="right">No Rujukan</td>
				<td valign="top">:</td>
				<td><input class="Data" name="no_resit" value="'.$no_resit.'" type="text" size="20" maxlength="10" readonly/></td>
			</tr>
		</table>
	</td>

	<tr><td colspan="5">&nbsp;</td></tr>

	<tr>
		<td nowrap="nowrap" align="left">DITERIMA DARIPADA	: '.$masterA.'&nbsp;</td>
	</tr>

	<tr><td  colspan="5"><br></td></tr>

	<tr>
		<td nowrap="nowrap" align="left">BAYARAN UNTUK	: '.$bayaran_untuk.'&nbsp;</td>
	</tr>

	<tr><td  colspan="5"><br></td></tr>

	<tr>
		<td nowrap="nowrap" align="left">JUMLAH :'.$strTotal.'</td>
	</tr>
	



	<tr><td colspan="5"><hr size="2px"></td></tr>


		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap" align="center">&nbsp;NO AKAUN&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;AKAUN DETAIL&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;KETERANGAN&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;AMAUN (RM)&nbsp;</td>
			</tr>
		<tr><td colspan="5"><hr size="2px" /></td></tr>';

// $jumlah = 0;
	if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields(deductID);
		$accN 	= $rsDetail->fields(deductID);
		//$keterangan_resit 	= $rsDetail->fields(keterangan);


		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		//$resitdesc = dlookup("resitacc", "keterangan", "ID=".$keterangan_resit);
		
		$totPymt = number_format($rsDetail->fields(pymtAmt),2);
		print
			'<tr>
				<td nowrap="nowrap" align="center">&nbsp;'.$accNombor.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$accdet.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$rs->fields(keterangan).'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$totPymt.'&nbsp;</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
			$rsDetail->MoveNext();
			}
			if($jumlah<>0){
			$clsRM->setValue($jumlah);
			$strTotal = ucwords($clsRM->getValue());
			}
		}

print '
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td colspan="5"><hr size="1px" /></td></tr>
			<tr>
				<td nowrap="nowrap" valign="top" align="left"><b>&nbsp;JUMLAH: RM&nbsp;</b></td>
				<td nowrap="nowrap" align="left">&nbsp;</td>
				<td nowrap="nowrap" align="left">&nbsp;</td>
				<td nowrap="nowrap" align="center"><b>&nbsp;'.number_format($jumlah1,2).'&nbsp;</b></td>
			</tr>
			
		</table>
	</td></tr>
	<tr><td colspan="5"><hr size="1px" /></td></tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5" align="right"></td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3" align="center">&nbsp;&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">
	<br></br><br></br><br></br>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap"><table cellpadding="0" cellspacing="0"><tr><td align="center">RM'.number_format($jumlah1,2).'<br />Tunai/Cheque</td></tr></table></td>
				<td nowrap="nowrap">&nbsp;</td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Disemak</td></tr></table></td>
			</tr>
		</table>
	</td></tr>
</table>'; 

print $footer;
?>