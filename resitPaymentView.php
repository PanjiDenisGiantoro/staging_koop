<?php
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	resitPaymentPrint.php
 *          Date 		: 	24/05/2024
 *********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");	
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

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
</body></html>';

if($ID){
	$sql = "SELECT a.*,b.newIC,b.memberID,b.address, b.city, b.postcode, b.stateID, b.departmentID, c.name FROM  resit a, userdetails b,users c WHERE b.userID = c.userID and a.bayar_nama = b.memberID and no_resit = ".tosql($ID, "Text");
	$rs = $conn->Execute($sql);

	$no_resit 		= $rs->fields('no_resit');
	$tarikh_resit 	= toDate("d/m/y",$rs->fields('tarikh_resit'));
	$bayar_kod 		= $rs->fields('bayar_kod');
	$bayar_nama 	= $rs->fields('name');
    $noIC           = $rs->fields('newIC');
	$no_anggota 	= $rs->fields('memberID');
	$deptID			=  $rs->fields('departmentID');
	$departmentAdd	= dlookup("general", "b_Address", "ID=" . tosql($deptID, "Number"));
	$alamat 		= strtoupper(strip_tags($departmentAdd));
    $departmentname	= dlookup("general", "name", "ID=" . tosql($deptID, "Number"));
	//-----------------
	$cara_bayar 	= $rs->fields('cara_bayar');
	$kod_siri 		= $rs->fields('kod_siri');
	$tarikh 		= toDate("d/m/y",$rs->fields('tarikh'));
	$akaun_bank 	= $rs->fields('akaun_bank');
	$kerani 		= $rs->fields('kerani');
	$catatan 		= $rs->fields('catatan');

	$accTabungan	=  dlookup("userdetails", "accTabungan", "userID=" . tosql($no_anggota, "Number"));
	$bankID			=  dlookup("userdetails", "bankID", "userID=" . tosql($no_anggota, "Number"));
	$bankname		=  dlookup("general", "name", "ID=" . $bankID);

	$sqltotal 	= "SELECT SUM(pymtAmt) AS tot FROM transaction WHERE docNo = '".$ID."'";
	$rstotal 	= $conn->Execute($sqltotal);
	$jumlah 	= $rstotal->fields('tot');
	
	$sql2 = "SELECT * FROM transaction WHERE docNo = ".tosql($ID, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);

    // if ($rsDetail === false) {
    //     // Debugging if the query execution fails
    //     print '<pre>Error in SQL: ' . $conn->ErrorMsg() . '</pre>';
    // } else {
    //     // Display the records in the recordset for debugging
    //     print '<pre>';
    //     while (!$rsDetail->EOF) {
    //         print_r($rsDetail->fields); // Print current row fields
    //         $rsDetail->MoveNext();     // Move to the next record
    //     }
    //     print '</pre>';
    // }
}


print'
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .stylish-date{
            float:right;
        }
        .bor-penerima {
            margin-top: 5%;
            margin-bottom: 3%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
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
            font-family: Arial, Helvetica, sans-serif;
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

print'
<body>
<div class="form-container">
    <!---------Doc/Date-------->
    <table class="stylish-date">
        <tr>
            <td><b>No. Resit</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$no_resit.'</td>
        </tr>
    <tr>
        <td><b>Tarikh Resit</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$tarikh_resit.'</td>
    </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr><td><b>Diterima Daripada:</b></td></tr>

    <tr><td>'.$bayar_nama.'('.$no_anggota.')</td></tr>
    <tr><td>'.$noIC.'</td></tr>
    <tr><td>'.ucwords(strtolower($departmentname)).'</td></tr>';
    
    /*--------------Alamat Department----------------- */
    $addressLines = explode(',', $alamat);
        foreach ($addressLines as $line) {
    $formattedLine = ucwords(strtolower(trim($line)));
        print '<tr><td>' . $formattedLine . '</td></tr>';
    }

            /*----------Call Word total Ringgit-----------*/
            if($jumlah<>0){
                $clsRM->setValue($jumlah);
                $strTotal = ucwords($clsRM->getValue()).' Sahaja.';
            }
            $jumlah = number_format($jumlah,2);            
    print'
    </table>
    <!-------Bank Anggota ------->
    <div class="body-acc-num"><b>Bank Anggota: </b>'.$accTabungan.' ('.$bankname.')</div>
    <div class="body-acc-num">Sebanyak RM <u><b>'.$jumlah.'</u></b> Ringgit <u><b>'.$strTotal.'</u></b></div>';

    print'
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td><b>Bil</b></td>
                <td><b>Perkara</b></td>
                <td colspan="2" align="right"><b>Jumlah (RM)</b></td>
            </tr>
    </thead>';

    $jumlah = 0;
    if ($rsDetail->RowCount() <> 0){
    $i=0;
	    while (!$rsDetail->EOF) {
	    $code = dlookup("general", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
	    $name = dlookup("general", "name", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
			print '
			<tr>
				<td nowrap="nowrap" align="left">('.++$i.')</td>
				<td nowrap="nowrap" align="left">'.$name.'</td>
				<td nowrap="nowrap" align="right" colspan="2">'; print  number_format($rsDetail->fields('pymtAmt'),2); print'</td>
			</tr>';
	$jumlah += $rsDetail->fields('pymtAmt');
	$rsDetail->MoveNext();
	}
}

print'
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr>     
		<td nowrap="nowrap" colspan="2"></td>
        <td nowrap="nowrap"align="right">CUKAI</td>
		<td valign="top" align="right">0.00</td>
	</tr>
    <tr>
		<td nowrap="nowrap" colspan="2"></td>
		<td nowrap="nowrap" align="right"><b>JUMLAH</b></td>
		<td nowrap="nowrap" align="right">'; print number_format($jumlah,2); print '</td>
	</tr>
    </table>
    <!-----------Cara Bayar/No. Siri/Tarikh Bayaran------------->
    <div class="bayar-style">Cara Bayaran : '.$cara_bayar.'</div>
    <div class="no-siri">Kod & No. Siri : '.$kod_siri.'</div>
    <div class="date-bayar-stylish">Tarikh Bayaran : '.$tarikh.'</div>
    <div class="stylish-catat">Catatan :'.$catatan.'</div>
    </div>';

print $footer;
?>