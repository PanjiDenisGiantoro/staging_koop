<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: accprintresit.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields('name');
$address1 = $rss->fields('address1');
$address2 = $rss->fields('address2');
$address3 = $rss->fields('address3');
$address4 = $rss->fields('address4');
$noPhone = $rss->fields('noPhone');
$email = $rss->fields('email');
$koperasiID = $rss->fields('koperasiID');

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$header =
    '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
    . '<html>'
    . '<head>'
    . '<title>' . $emaNetis . '</title>'
    . '<meta name="GENERATOR" content="' . $yVZcSz2OuGE8U . '">'
    . '<meta http-equiv="pragma" content="no-cache">'
    . '<meta http-equiv="expires" content="0">'
    . '<meta http-equiv="cache-control" content="no-cache">'
    . '<LINK rel="stylesheet" href="images/mail.css" >'
    . '</head>'
    . '<body>';

if ($id) {
    $sql = "SELECT * FROM bauceracc WHERE no_baucer = '" . $id . "'";
    $rs = $conn->Execute($sql);

    $newIC          = dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Number"));
    $no_baucer         = $rs->fields(no_baucer);
    $tarikh_baucer     = toDate("d/m/y", $rs->fields(tarikh_baucer));
    $Cheque         = $rs->fields(Cheque);
    $name             = $rs->fields(name);
    $bayaran_kpd    = $rs->fields(bayaran_kpd);
    $cara_bayar     = $rs->fields(cara_bayar);
    $Ncara_bayar     = dlookup("general", "name", "ID=" . tosql($cara_bayar, "Text"));
    $keterangan        = $rs->fields(keterangan);
    $disedia        = $rs->fields(disedia);
    $disedia1        = dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
    $sedia             = strtoupper(strip_tags($disedia1));
    $kod_bank         = $rs->fields(kod_bank);
    $nama_caw       = dlookup("generalacc", "b_Baddress", "ID=" . $k_bank);
    $k_bank            = dlookup("generalacc", "name", "ID=" . $kod_bank);

    $sqltotal = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE addminus IN (0) AND docNo = '" . $id . "'";
    $rstotal = $conn->Execute($sqltotal);
    $jumlah = $rstotal->fields(tot);

    $sql2 = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = " . tosql($no_baucer, "Text") . " ORDER BY ID";
    $rsDetail = $conn->Execute($sql2);
}

$codeproject = $rsDetail->fields(kod_project);
$codejabatan = $rsDetail->fields(kod_jabatan);

$kodprojek    = dlookup("generalacc", "name", "ID=" . $codeproject);
$kodjabatan    = dlookup("generalacc", "name", "ID=" . $codejabatan);

print '
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }

        .Mainlogo {
            position: absolute;
        }

        .AlamatPengirim {
            margin-left: 17%;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }

        .resit-statement {
            float: right;
            text-wrap: nowrap;
            text-align: center;
            margin-top: -130px;
            border-collapse: separate;
            border-spacing: 10px;
            width: 20%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }
        .tr-space{
            background: #d3d3d3;
        }
        .tr-kod-rujukan{
            font-weight: bold;
            word-spacing: 5px;
        }
        .word-trans{
            text-wrap: nowrap;
            text-align: center;
            font-weight: bold;
            margin-top: 2%;
            margin-bottom: 2%;
        }
        .date-stylish{
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            margin-top: 4%;
            float: right;
        }
        .stylish-date{
            float:right;
            margin-top: 5.5%;
        }
        .bor-penerima {
            margin-top:5%;
            margin-bottom:5%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }
        .line-trans{
            border: groove 2px;
            margin-bottom: 3%;
        }
        .header-border{
            margin-top:2%;
            border: solid 2px;
        }
        .bayar-style{
            margin-top: 2%;
            margin-right: 65%;
        }
        .no-siri{
            margin-left:  40%;
            margin-top: -20px;
            margin-right: 30%;
        }
        .date-bayar-stylish{
            float: right;
            margin-top: -20px;
        }
        .stylish-catat{
            margin-top: 3%;
        }
        .td-thick-font{
            font-weight: bold:
        }
        .bottom {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
';

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));
$Gambar = "upload_images/" . $pic;

print '
<body>
<div class="form-container">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img id="elImage" src="' . $Gambar . '" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <table class="AlamatPengirim">
        <tr><td>' . $coopName . '</td></tr>
        <tr><td>' . ucwords(strtolower($address1)) . '</td></tr>
        <tr><td>' . ucwords(strtolower($address2)) . '</td></tr>
        <tr><td>' . ucwords(strtolower($address3)) . '</td></tr>
        <tr><td>' . ucwords(strtolower($address4)) . '</td></tr>
        <tr><td>TEL: ' . $noPhone . ' </td></tr>
        <tr><td>EMEL: ' . $email . '</td></tr>
        </table>
    <table class="resit-statement">
        <tr class="tr-space"><td>Voucher Bayaran</td></tr>
        <tr class="tr-kod-rujukan"><td>' . $no_baucer . '</td></tr>
    </table>
    <table class="stylish-date">
        <tr>
            <td><b>TARIKH</b></td>
            <td>:&nbsp;</td>
            <td>' . $tarikh_baucer . '</td>
        </tr>
        <tr>
        <td><b>NO BAUCER</b></td>
        <td>:&nbsp;</td>
        <td>' . $no_baucer . '</td>
        </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
        <td>BAYAR KEPADA: </td>
        <td>:&nbsp;</td>
        <td>' . ucwords(strtolower($bayaran_kpd)) . '</td>
    </tr>
    <tr>
        <td>KETERANGAN</td>
        <td>:&nbsp;</td>
        <td>' . ucwords(strtolower($keterangan)) . '</td>
    </tr>
    <tr>
        <td>BANK</td>
        <td>:&nbsp;</td>
        <td>' . ucwords(strtolower($k_bank)) . '</td>
    </tr>
    <tr>
		<td>CHEQUE NO</td>
		<td>:</td>
		<td>' . $Cheque . '</td>
	</tr>
    <tr>
		<td>CARA BAYARAN</td>
		<td>:</td>
		<td>' . $Ncara_bayar . '</td>
	</tr>
    <tr>
		<td>JABATAN</td>
		<td>:</td>
		<td>' . ucwords(strtolower($kodjabatan)) . '</td>
	</tr>
    <tr>
		<td>PROJEK</td>
		<td>:</td>
		<td>' . ucwords(strtolower($kodprojek)) . '</td>
	</tr>     
    </table>';

print '
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>A/C Keterangan</b></td>
                <td nowrap="nowrap"><b>Keterangan</b></td>
                <td nowrap="nowrap" align="right"><b>Jumlah</b></td>
            </tr>
    </thead>';

if ($rsDetail->RowCount() <> 0) {
    $i = 1;
    while (!$rsDetail->EOF) {

        $accNom     = $rsDetail->fields(deductID);
        $accN         = $rsDetail->fields(deductID);
        $desc_akaun = $rsDetail->fields(desc_akaun);
        $accNombor     = dlookup("generalacc", "code", "ID=" . $accN);
        $accdet     = dlookup("generalacc", "name", "ID=" . $accNom);
        $tarikh = $rsDetail->fields(createdDate);
        $tarikh = substr($tarikh, 8, 2) . "/" . substr($tarikh, 5, 2) . "/" . substr($tarikh, 0, 4);
        $addminus = $rsDetail->fields(addminus);
        $totPymt4 = $rsDetail->fields(pymtAmt);

        print
            '<tr>
				<td nowrap="nowrap" align="left">' . $accNombor . '&nbsp;-&nbsp;' . $accdet . '</td>
				<td nowrap="nowrap" align="left">' . $desc_akaun . '</td>
				<td nowrap="nowrap" align="right">' . number_format($totPymt4, 2) . '</td>
			</tr>';

        $jumlah1 += $totPymt4;
        $rsDetail->MoveNext();
    }
    if ($jumlah1 <> 0) {
        $clsRM->setValue($jumlah1);
        $strTotal1 = strtoupper($clsRM->getValue()) . ' RINGGIT SAHAJA.';
    }
}

print '
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
		<td nowrap="nowrap" align="left"></td>
    	<td nowrap="nowrap" align="right">CUKAI</td>
    	<td nowrap="nowrap" align="right">0.00</td>
	</tr>
    <tr>
    <td nowrap="nowrap" align="left"></td>
    <td nowrap="nowrap" align="right"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RM ' . number_format($jumlah1, 2) . '</b></td> 
	</tr>
    </table>
    
    <!-----------Disediakan Oleh/Disemak Oleh/Disahkan Oleh------------->
    <table cellpadding="0" cellspacing="0" width="100%" >
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
        <td nowrap="nowrap">&nbsp;</td>
        <td nowrap="nowrap" align="center"><table><tr><td align="center"><b>' . $sedia . '</b><br />DISEDIAKAN OLEH</td></tr></table></td>
        <td nowrap="nowrap" align="center"><table><tr><td align="center">_____________________________<br />DILULUSKAN OLEH</td></tr></table></td>
        <td nowrap="nowrap" align="center"><table><tr><td align="center">_____________________________<br />DISAHKAN OLEH</td></tr></table></td>
        </tr>
        <tr><td colspan="4"><hr size="1px" /></td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap" align="center"><table><tr><td align="center">_____________________________<br />Pengerusi/Timbalan Pengerusi</td></tr></table></td>
            <td nowrap="nowrap" align="center"><table><tr><td align="center">_____________________________<br />Setiausaha/Timbalan Setiausaha</td></tr></table></td>
            <td nowrap="nowrap" align="center"><table><tr><td align="center">_____________________________<br />Bendehari/Timbalan Bendehari</td></tr></table></td>
        </tr>
        <tr><td colspan="4"><hr size="1px" /></td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
        <td colspan="4"><b>DISEDIAKAN OLEH :</b> ' . $sedia . '</td>
        </tr>
	</table>
    <center>
        <div class="bottom"><hr size="1px">
            <b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
        </div>
    </center>
    </div>
    </body>
</html>

<script>window.print();</script>
</body></html>';
