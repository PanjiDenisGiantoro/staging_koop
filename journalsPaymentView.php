<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: journalsPaymentView.php
 *			Date 		: 04/08/2006
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
    . '<body>';

if ($id) {
    $sql = "SELECT a.*,b.* FROM jurnal a, userdetails b WHERE a.no_jurnal = '" . $id . "'";
    $rs = $conn->Execute($sql);

    $no_jurnal         = $rs->fields(no_jurnal);
    $userID            = $rs->fields(no_anggota);
    $tarikh_jurnal     = toDate("d/m/y", $rs->fields(tarikh_jurnal));
    $deptID         = $rs->fields(departmentID);
    $departmentname    = dlookup("general", "name", "ID=" . tosql($deptID, "Number"));
    $departmentAdd    = dlookup("general", "b_Address", "ID=" . tosql($deptID, "Text"));
    $alamat         = strtoupper(strip_tags($departmentAdd));
    //-------------------------------

    //---------------------------------
    $name             =  dlookup("users", "name", "userID=" . tosql($userID, "Number"));
    $newIC          =  dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Number"));
    $accTabungan    =  dlookup("userdetails", "accTabungan", "userID=" . tosql($userID, "Number"));

    $bankID            =  dlookup("userdetails", "bankID", "userID=" . tosql($userID, "Number"));
    $bankname        =  dlookup("general", "name", "ID=" . $bankID);

    $departmentAdd    =  dlookup("userdetails", "address", "userID=" . tosql($userID, "Number"));
    //$alamat 		=  strtoupper(strip_tags($departmentAdd));
    $catatan        =  $rs->fields('keterangan');
    //-----------------

    $sql2 = "SELECT * FROM transactionacc WHERE docNo = " . tosql($no_jurnal, "Text") . " ORDER BY ID";
    $rsDetail = $conn->Execute($sql2);

    $sedia        = $rs->fields(disediakan);
    $semak        = $rs->fields(disemak);
    $semak        = dlookup("users", "name", "userID=" . tosql($semak, "Text"));
    $sah        = $rs->fields(disahkan);
    $sah        = dlookup("users", "name", "userID=" . tosql($sah, "Text"));

    $no_bond    = $rs->fields(no_bond);
    $kod_caw    = $rs->fields(kod_caw);
    $no_siri    = $rs->fields(no_siri);

    if ($rs->fields(tarikh_bank) == null) {
        $tarikh_bank     = toDate("d/m/y", $rs->fields(tarikh_bank));
    }
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
        }
        .bor-penerima {
            margin-top: 5%;
            margin-bottom: 3%;
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
            margin-top:7%;
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
            <td><b>NO JURNAL</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $no_jurnal . '</td>
        </tr>
        <tr>
            <td><b>TARIKH</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $tarikh_jurnal . '</td>
        </tr>
    ';
if ($kod_caw <> "" || $no_siri <> "" || $tarikh_bank <> "") {
    print '
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr><td><b>MAKLUMAT DARI SLIP BANK: </b></td></tr>
    ';
}
if ($kod_caw <> "") {
    print '
        <tr>
            <td>KOD CAWANGAN</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $kod_caw . '</td>
        </tr>
        ';
}
if ($no_siri <> "") {
    print '
        <tr>
            <td>NOMBOR SIRI</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $no_siri . '</td>
        </tr>
        ';
}
if ($tarikh_bank <> "") {
    print '
        <tr>
            <td>TARIKH DIKEMASKINI</td>
            <td>&nbsp;:&nbsp;</td>
            <td>' . $tarikh_bank . '</td>
        </tr>
        ';
}
print '
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr><td><b>BAYARAN KEPADA: </b></td></tr>
    <tr><td>' . ucwords(strtolower($name)) . '&nbsp;(' . $userID . ')</td></td>
    <tr><td>' . $newIC . '</td></td>
    <tr><td>' . ucwords(strtolower($departmentname)) . '</td></tr>
    <tr><td>' . ucwords(strtolower($alamat)) . '</td><tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr><td><b>AKAUN BANK: </b></td></tr>
    <tr><td>' . $accTabungan . '(' . ucwords(strtolower($bankname)) . ')</td></tr>
    </table>';

print '
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
				<td nowrap="nowrap"><b>BIL</b></td>
                <td nowrap="nowrap"><b>KETERANGAN</b></td>
                <td nowrap="nowrap"><b>NO BOND</b></td>
                <td nowrap="nowrap" align="right"><b>DEBIT (RM)</b></td>
                <td nowrap="nowrap" align="right"><b>KREDIT (RM)</b></td>
            </tr>
    </thead>';

if ($rsDetail->RowCount() <> 0) {
    $i = 1;
    while (!$rsDetail->EOF) {

        $deductID     = $rsDetail->fields(deductID);
        $desc         = dlookup("general", "name", "ID=" . $deductID);
        $totPymt     = number_format($rsDetail->fields(pymtAmt), 2);
        $accNombor     = dlookup("general", "code", "ID=" . $deductID);
        $accdet     = dlookup("general", "name", "ID=" . $deductID);
        $desc_akaun = $rsDetail->fields(desc_akaun);
        $bondNo        = $rsDetail->fields(pymtRefer);

        if ($rsDetail->fields(addminus)) {
            $kredit     = $rsDetail->fields(pymtAmt);
            $jumlahKrt += $rsDetail->fields(pymtAmt);
        } else {
            $debit         = $rsDetail->fields(pymtAmt);
            $jumlahDbt += $rsDetail->fields(pymtAmt);
        }

        print '
			<tr>
				<td nowrap="nowrap">' . $i . '</td>
				<td style="text-align: justify;">' . $accdet . '</td>
                <td nowrap="nowrap">' . $bondNo . '</td>
				<td nowrap="nowrap" align="right">&nbsp;';
        if ($debit <> 0) print number_format($debit, 2);
        print '&nbsp;</td>
				<td nowrap="nowrap" align="right">&nbsp;';
        if ($kredit <> 0) print number_format($kredit, 2);
        print '&nbsp;</td>
			</tr>';

        $jumlah += $rsDetail->fields(pymtAmt);
        $debit = '';
        $kredit = '';
        $i++;
        $rsDetail->MoveNext();
    }
    if ($jumlah <> 0) {
        $clsRM->setValue($jumlah);
        $strTotal = ucwords($clsRM->getValue());
    }
}
print '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="3"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RM ' . number_format($jumlahDbt, 2) . '</b></td>
    <td nowrap="nowrap" align="right"><b>RM ' . number_format($jumlahKrt, 2) . '</b></td>
</tr>
</table>
<tr><td colspan="5">&nbsp;</td></tr>

    <!-----------Catatan/Description------------->
        <table class="stylish-kerani">
        <tr>
            <td nowrap="nowrap"><b>CATATAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($catatan)) . '</td>
        </tr>
    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td nowrap="nowrap"><b>DISEDIAKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($sedia)) . '</td>
        </tr>
        <tr>
            <td nowrap="nowrap"><b>DISEMAK OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($semak)) . '</td>
        </tr>
        <tr>
            <td nowrap="nowrap"><b>DISAHKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($sah)) . '</td>
        </tr>
    </table>

    </div>
    </body>

</body></html>';
