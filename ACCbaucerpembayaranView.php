<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCbaucerpembayaranView.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

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
    $tarikh         = toDate("d/m/y", $rs->fields(tarikh_bayar));
    $Cheque         = $rs->fields(Cheque);
    $name             = $rs->fields(name);
    $bayaran_kpd    = $rs->fields(bayaran_kpd);
    $cara_bayar     = $rs->fields(cara_bayar);
    $Ncara_bayar     = dlookup("general", "name", "ID=" . tosql($cara_bayar, "Text"));
    $keterangan        = $rs->fields(keterangan);
    $catatan         = $rs->fields(catatan);

    $disedia        = $rs->fields(disedia);
    $disedia1        = dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
    $sedia             = strtoupper(strip_tags($disedia1));
    $disahkan        = $rs->fields(disahkan);
    $disahkan1        = dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
    $sah             = strtoupper(strip_tags($disahkan1));
    $kod_bank         = $rs->fields(kod_bank);
    $nama_caw       = dlookup("generalacc", "b_Baddress", "ID=" . $k_bank);
    $k_bank            = dlookup("generalacc", "name", "ID=" . $kod_bank);

    $sqltotal = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE addminus IN (0) AND docNo = '" . $id . "'";
    $rstotal = $conn->Execute($sqltotal);
    $jumlah = $rstotal->fields(tot);

    $sql2 = "SELECT * FROM transactionacc WHERE docNo = " . tosql($no_baucer, "Text") . " AND addminus IN (0) ORDER BY ID";
    $rsDetail = $conn->Execute($sql2);
    $namaBatch         = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batchNo), "Text"));

    if ($rs->fields(kod_project))
        $namaProjek = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_project), "Text"));
    else
        $namaProjek = "-";

    if ($rs->fields(kod_jabatan))
        $namaJabatan = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_jabatan), "Text"));
    else
        $namaJabatan = "-";
}
print '
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }
        .stylish-date{
            float:right;
            margin-top: 5.5%;
        }
        .bor-penerima {
            margin-top: 5%;
            margin-bottom: 3%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            max-width: 50%;
            table-layout: fixed; /* Ensures a fixed layout */
        }
        .bor-penerima td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
            text-align: justify; /* Apply justification */
        }
        .header-border{
            margin-top:2%;
            border: solid 2px;
        }
        .stylish-kerani{
            margin-top:1%;
            margin-bottom:3%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            table-layout: fixed;
        }
        .stylish-kerani td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
            text-align: justify; /* Apply justification */
        }
    </style>
</head>
';

print '
<body>
<div class="form-container">
    <!---------Doc/Date-------->
    <table class="stylish-date">
        <tr>
            <td><b>NO BAUCER</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $no_baucer . '</td>
        </tr>
        <tr>
            <td><b>TARIKH BAUCER</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $tarikh_baucer . '</td>
        </tr>
        <tr>
            <td><b>TARIKH BAYARAN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $tarikh . '</td>
        </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
        <td nowrap="nowrap">BAYAR KEPADA: </td>
        <td>&nbsp;:&nbsp;</td>
        <td>' . ucwords(strtolower($bayaran_kpd)) . '</td>
    </tr>
    ';
if ($keterangan <> "") {
    print '
        <tr>
            <td nowrap="nowrap">KETERANGAN</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($keterangan)) . '</td>
        </tr>
        ';
}
if ($namaBatch <> "") {
    print '
        <tr>
            <td nowrap="nowrap">NAMA BATCH</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($namaBatch)) . '</td>
        </tr>
        ';
}
if ($k_bank <> "") {
    print '
        <tr>
            <td nowrap="nowrap">NAMA BANK</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($k_bank)) . '</td>
        </tr>     
        ';
}
if ($namaProjek <> "") {
    print '
        <tr>
            <td nowrap="nowrap">NAMA PROJEK</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($namaProjek)) . '</td>
        </tr>     
        ';
}
if ($namaJabatan <> "") {
    print '
        <tr>
            <td nowrap="nowrap">NAMA JABATAN</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($namaJabatan)) . '</td>
        </tr>     
        ';
}
if ($Cheque <> "") {
    print '
        <tr>
            <td nowrap="nowrap">CHEQUE NO</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($Cheque)) . '</td>
        </tr>
        ';
}
if ($Ncara_bayar <> "") {
    print '
        <tr>
            <td nowrap="nowrap">CARA BAYARAN</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($Ncara_bayar)) . '</td>
        </tr>
        ';
}
print '
    </table>';

print '
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>BIL</b></td>
                <td><b>KETERANGAN</b></td>
				<td nowrap="nowrap" align="right"><b>AMAUN (RM)</b></td>
            </tr>
    </thead>';

$jumlah = 0;

if ($rsDetail->RowCount() <> 0) {
    $i = 1;
    while (!$rsDetail->EOF) {

        $accNom     = $rsDetail->fields(deductID);
        $accN         = $rsDetail->fields(deductID);
        $desc_akaun = $rsDetail->fields(desc_akaun);
        $accNombor    = dlookup("generalacc", "code", "ID=" . $accN);
        $accdet     = dlookup("generalacc", "name", "ID=" . $accNom);
        $tarikh     = $rsDetail->fields(createdDate);
        $tarikh     = substr($tarikh, 8, 2) . "/" . substr($tarikh, 5, 2) . "/" . substr($tarikh, 0, 4);
        $addminus     = $rsDetail->fields(addminus);

        $totPymt4 = $rsDetail->fields(pymtAmt);

        print
            '<tr>
				<td nowrap="nowrap">' . $i . '</td>
                <td style="text-align: justify;">' . $desc_akaun . '</td>
				<td nowrap="nowrap" align="right">' . number_format($totPymt4, 2) . '</td>
			</tr>';
        $jumlah += $totPymt4;
        $i++;
        $rsDetail->MoveNext();
    }
    if ($jumlah <> 0) {
        $clsRM->setValue($jumlah);
        $strTotal1 = ucwords($clsRM->getValue());
    }
}

print '
    <tr><td colspan="4">&nbsp;</td></tr>
    <tr>
        <td></td>
		<td nowrap="nowrap" align="right"><b>JUMLAH<b></td>
		<td nowrap="nowrap" align="right"><b>RM ' . number_format($jumlah, 2) . '</b></td>	
	</tr>
    </table>
<tr><td colspan="5">&nbsp;</td></tr>

    <!-----------Jumlah Dalam Perkataan------------->
    <table class="stylish-kerani">
        <tr>
            <td nowrap="nowrap"><b>JUMLAH DALAM PERKATAAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($strTotal1)) . ' Ringgit Malaysia Sahaja.</td>
                 </tr>

            <!-----------Catatan/Description------------->
                <tr>
                    <td nowrap="nowrap"><b>CATATAN</b></td>
                    <td>&nbsp;:&nbsp;</b></td>
                    <td>' . ucwords(strtolower($catatan)) . '</td>
    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td><b>DISEDIAKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($sedia)) . '</td>
        </tr>
        <tr>
            <td><b>DISEMAK OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($sah)) . '</td>
        </tr>
    </table>

    </div>
    </body>
</html>

</body></html>';
