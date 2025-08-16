<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCResitViewCustomer.php
 *			Date 		: 27/7/2006
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
    . '<meta name="GENERATOR" content="' . $yVZcSz2OuGE8U . '">'
    . '<meta http-equiv="pragma" content="no-cache">'
    . '<meta http-equiv="expires" content="0">'
    . '<meta http-equiv="cache-control" content="no-cache">'
    . '<LINK rel="stylesheet" href="images/mail.css" >'
    . '</head>'
    . '<body>';

if ($id) {
    $sql = "SELECT * FROM  resitacc WHERE no_resit = '" . $id . "'";
    $rs = $conn->Execute($sql);

    $no_resit         = $rs->fields(no_resit);
    $tarikh_resit     = toDate("d/m/y", $rs->fields(tarikh_resit));
    $tarikh         = toDate("d/m/y", $rs->fields(tarikh));
    $bayar_kod         = $rs->fields(bayar_kod);
    $bayar_nama     = $rs->fields(name);
    $no_anggota     = $rs->fields(memberID);
    $cara_bayar     = $rs->fields(cara_bayar);
    $Ncara_bayar     = dlookup("general", "name", "ID=" . tosql($cara_bayar, "Text"));
    $Cheque            = $rs->fields(Cheque);
    $akaun_bank     = $rs->fields(akaun_bank);
    $keterangan        = $rs->fields(keterangan);
    $diterima_drpd    = $rs->fields(diterima_drpd);
    $catatan         = $rs->fields(maklumat);

    $kerani            = $rs->fields(kerani);

    $master         = $rs->fields(masteraccount);
    $masterA         = dlookup("generalacc", "name", "ID=" . $master);

    $kod_bank         = $rs->fields(kod_bank);
    $kod_bankA         = dlookup("generalacc", "name", "ID=" . $kod_bank);

    $sqltotal = "SELECT sum(pymtAmt) as tot FROM transactionacc WHERE addminus IN (1) AND docNo = '" . $id . "'";
    $rstotal = $conn->Execute($sqltotal);
    $jumlah = $rstotal->fields(tot);

    $sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = " . tosql($no_resit, "Text") . " ORDER BY ID";
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
            <td><b>NO RESIT</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $no_resit . '</td>
        </tr>
        <tr>
            <td><b>TARIKH RESIT</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $tarikh_resit . '</td>
        </tr>
        <tr>
            <td><b>TARIKH TERIMAAN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $tarikh . '</td>
        </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
        <td nowrap="nowrap">DITERIMA DARIPADA: </td>
        <td>&nbsp;:&nbsp;</td>
        <td>' . ucwords(strtolower($diterima_drpd)) . '</td>
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
if ($kod_bankA <> "") {
    print '
        <tr>
            <td nowrap="nowrap">NAMA BANK</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . ucwords(strtolower($kod_bankA)) . '</td>
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

if ($rsDetail->RowCount() <> 0) {
    $i = 1;
    while (!$rsDetail->EOF) {

        $desc_akaun = $rsDetail->fields(desc_akaun);
        $totPymt = number_format($rsDetail->fields(pymtAmt), 2);

        print
            '<tr>
				<td nowrap="nowrap">' . $i . '</td>
                <td style="text-align: justify;">' . $desc_akaun . '</td>
				<td nowrap="nowrap" align="right">' . $totPymt . '</td>
			</tr>';
        $jumlah1 += $rsDetail->fields(pymtAmt);
        $i++;
        $rsDetail->MoveNext();
    }
    if ($jumlah1 <> 0) {
        $clsRM->setValue($jumlah1);
        $strTotal1 = ucwords($clsRM->getValue());
    }
}

print '
        <tr><td colspan="6">&nbsp;</td></tr>
        <tr>				
            <td nowrap="nowrap" align="right" colspan="2"><b>JUMLAH</b></td>
            <td nowrap="nowrap" align="right"><b>RM ' . number_format($jumlah1, 2) . '</b></td>
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
                </tr>
        
            <!-----------Kerani/Akaun/Nama Bank------------->
                <tr>
                    <td><b>DISEDIAKAN OLEH</b></td>
                    <td>&nbsp;:&nbsp;</b></td>
                    <td>' . ucwords(strtolower($kerani)) . '</td>
                </tr>
            </table>

    </div>
    </body>
</html>

</body></html>';
