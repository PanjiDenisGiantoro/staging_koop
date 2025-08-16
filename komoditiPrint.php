<?php

/*********************************************************************************
 *			Project		:iKOOP.com.my
 *			Filename	: komoditiPrint.php
 *			Date 		: 19/2/2024
 *********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$header =
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
	. '<html>'
	. '<head>'
	. '<title>' . $emaNetis . '</title>'
	. '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
	. '<meta http-equiv="pragma" content="no-cache">'
	. '<meta http-equiv="expires" content="0">'
	. '<meta http-equiv="cache-control" content="no-cache">'
	. '<LINK rel="stylesheet" href="images/mail.css" >'
	. '</head>'
	. '<body>'
	. '<div class="page-container">
';

$footer = '
<script>window.print();</script>
</body></html>';

print $header;

$coopName = "AS-SIDQ";
$address1 = "Level 11 Kelana Parkview Tower";
$address2 = "Jalan SS6/2, 47301 Petaling Jaya";
$address3 = "Selangor";
$address4 = "&nbsp;";
$noPhone = "Tel: 03-7880 2001 &nbsp; &nbsp; &nbsp; &nbsp; Fax: 03-78806001";

if ($id) {
	$strkomoditi = "SELECT * FROM komoditi WHERE komoditi_ID = '" . $id . "'";
	$Getkomoditi = &$conn->Execute($strkomoditi);

	$sijilNo 			= $Getkomoditi->fields(no_sijil);
	$sijilNo 			= strtoupper(strip_tags($sijilNo));
	$tarikhBeli 		= toDate("d/m/Y", $Getkomoditi->fields(tarikh_beli));
	$masaBeli           = date('H:i:s', strtotime($Getkomoditi->fields(masa_beli)));
	$masaBeli           .= date(' A', strtotime($masaBeli));
	$amount 			= $Getkomoditi->fields(amount);
	$jumlah 			+= $amount;

	// $userID 			= $Getkomoditi->fields(userID);
	$nama 				= dlookup("users", "name", "userID=" . tosql($Getkomoditi->fields(userID), "Number"));
}

print '
<style>
.page-container {
    margin-top: 30px;
    margin-bottom: 20px;
}
.boxGray {
	padding: 7px;
	width: 110px;
	background-color: lightgray;
	word-wrap: break-word;
	}
	.box {
	padding: 7px;
	width: 110px; 
	}
	.boxTitle {
	padding: 5px;
	font-size: 20px;
	width: fit-content;
	height: fit-content;
	border: 1px solid black;
	}
	.bottom {
		position: fixed;
		bottom: 0px;
		text-align: center;
		width: 100%;
	}
	.borderRight {
		border-right: 1px solid;
	}
	.borderSide {
		border-right: 1px solid;
		border-left: 1px solid;
	}
	.borderLeft {
		border-left: 1px solid;
	}
	.borderRightTitle {
		border-right: 1px solid;
		border-bottom: 1px solid;
		border-top: 1px solid;
	}
	.borderSideTitle {
		border: 1px solid;
	}
	.borderLeftTitle {
		border-left: 1px solid;
		border-bottom: 1px solid;
		border-top: 1px solid;
	}
	.borderTop {
		border-top: 1px solid;
	}
    .page-break {
        page-break-after: always;
    }
</style>

<table width="100%" cellpadding="0" cellspacing="0" class="textFont">
	<tr>
	<td align="left" style="padding-left: 10px;">
		<img src="images/assidq.png" alt="AS-SIDQ Logo" width="100">
	</td>
		<td colspan="4" align="right">		
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="midle" class="textFont">
					<tr><td align="left" valign="midle" class="textFont">
					' . $address1 . ',<br />
					' . $address2 . ',<br />
					' . $address3 . '<br />
					' . $address4 . '<br />
					' . $noPhone . '<br />
					</td>
				</tr>
			</table>&nbsp;
		</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="textFont">
	<tr><td colspan="4"></td></tr>
	<tr><td  colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>

	<tr>
		<td colspan="3"><b>AS SIDQ TRADING CERTIFICATE C [' . $sijilNo . ']</b>&nbsp;</td>
	</tr>

	<tr><td  colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>

	<table width="100%" cellpadding="2" cellspacing="0" class="textFont">
    <tr>
    	<td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Seller&nbsp;</b></td>
		<td align="left" class="borderRightTitle">&nbsp;' . $nama . '&nbsp;</td>	
	</tr>
    
    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Buyer&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;ASIANA KENCANA SDN BHD&nbsp;</td>	
    </tr>

    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Issue Time/Date&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;' . $tarikhBeli . '&nbsp;&nbsp;</td>	
    </tr>

    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Product Code Description&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;AIRTIME&nbsp;</td>	
    </tr>

    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Currency&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;MYR&nbsp;</td>	
    </tr>

    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Buying Price&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;' . $amount . '&nbsp;</td>	
    </tr>

    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Value&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;' . $amount . '&nbsp;</td>	
    </tr>
    
    <tr>
        <td align="right" class="borderSideTitle" style="width: 40%;"><b>&nbsp;Unit&nbsp;</b></td>
        <td align="left" class="borderRightTitle">&nbsp;1 Unit (Various Denominations)&nbsp;</td>	
    </tr>
    </table>
    </table>
    ';

print '<div class="page-break"></div>';

print
	'
    <table width="100%" cellpadding="0" cellspacing="0" class="textFont">
	<tr><td colspan="4"></td></tr>

	<tr><td  colspan="4"><br></td></tr>

	<table width="100%" cellpadding="2" cellspacing="0" class="textFont">
    <tr>
    	<td align="center" class="borderLeftTitle"><b>&nbsp;Description&nbsp;</b></td>
		<td align="center" class="borderSideTitle">&nbsp;Nombor&nbsp;</td>	
        <td align="center" class="borderRightTitle"><b>&nbsp;Serial Nombor&nbsp;</b></td>
    	<td align="center" class="borderRightTitle"><b>&nbsp;Amount (RM)&nbsp;</b></td>
	</tr>
    
	<tr>
		<td align="left" style="padding: 0 0 300px 0; width: 40%;" class="borderLeftTitle">&nbsp;Airtime credit (RM ' . $amount . ')-1 pcs&nbsp;</td>
		<td align="center" style="padding: 0 0 300px 0; width: 20%;" class="borderSideTitle">&nbsp;1&nbsp;</td>
		<td align="center" style="padding: 0 0 300px 0; width: 30%;" class="borderRightTitle">&nbsp;FRH67O20240115-0029989807&nbsp;</td>
		<td align="center" style="padding: 0 0 300px 0; width: 10%;" class="borderRightTitle">&nbsp;' . $amount . '&nbsp;</td>
	</tr>

	<tr><td  colspan="4"><br></td></tr>

	<tr>
	<td nowrap="nowrap" align="right" colspan="3" style="padding: 10px 40px 0 40px width: 90%;" class="borderSideTitle"><b>TOTAL AMOUNT (RM):&nbsp;</b></td>
	<td nowrap="nowrap" align="right" style="padding: 10px 40px 0 40px width: 10%;" class="borderRightTitle"><b>RM&nbsp;' . number_format($jumlah, 2) . '&nbsp;</b></td>
</tr>
    </table>
    </table>
	<table width="100%" cellpadding="2" cellspacing="0" class="textFont">

	</table>
    ';

print
	'
<center>
<div class="bottom"><hr size="1px">
  <b>This document is computer generated and does not require a signature</b>
</div> 
</center>
</div>
	';
print $footer;
