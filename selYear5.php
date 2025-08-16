<?php
/*********************************************************************************
*          Project        :  iKOOP.com.my
*          Filename       :  selYear5.php
*          Description    :  Selection Month/Year Option and Creditor Companies Option
*          Parameter      :  $rpt, $id
*          Date           :  15/12/2003
*          Updated        :  09/08/2024
*********************************************************************************/
include("common.php");    
include("koperasiQry.php");    
date_default_timezone_set("Asia/Kuala Lumpur");
$today = date("F j, Y, g:i a");                 

if ($rpt == "") {
    print ' <script>
                alert ("Pengguna tidak boleh akses mukasurat ini...!");
                window.close();
            </script>';
    exit;
}

if (!isset($mthFrom)) $mthFrom 	= date("n");                 
if (!isset($yrFrom)) $yrFrom    = date("Y");                 
if (!isset($mthTo)) $mthTo    	= date("n");                 
if (!isset($yrTo)) $yrTo   		= date("Y");                 

if ($action == "Jana Laporan") {
    $msg    	= "";
    $kod_bank 	= $kod_bank;
    
    if ($mthFrom == "" || $yrFrom == "" || $mthTo == "" || $yrTo == "") {
        $msg = "Tiada Bulan/Tahun dimasukkan...!";
    }
    if ($kod_bank == "") {
        $msg = "Tiada Syarikat Dipilih...";
    }
    if ($msg <> "") {
        print '<script>alert("'.$msg.'");</script>';
    } else {
        print '
        <script>
            var rptURL;
            rptURL = "'.$rpt.'.php?mthFrom='.$mthFrom.'&yrFrom='.$yrFrom.'&mthTo='.$mthTo.'&yrTo='.$yrTo.'&id='.$kod_bank.'&cr=1";
            window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
            window.close();
        </script>    ';
    }
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>'.$emaNetis.'</title>
    <!--LINK rel="stylesheet" href="images/default.css" -->    
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />    
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';

$sqlYears = "SELECT DISTINCT YEAR(tarikh_doc) AS year 
            FROM transactionacc 
            WHERE tarikh_doc IS NOT NULL 
            AND tarikh_doc != '' 
            AND tarikh_doc != 0 
            ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

print '
<center>
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
    <input type="hidden" name="rpt" value="'.$rpt.'">
    <input type="hidden" name="kod_bank" value="'.$kod_bank.'">
    
    <div style="display: flex; justify-content: center; align-items: center; width: 90%; margin-top: 20px; gap: 20px; font-size:9pt;">
        
        <!-- Bulan/Tahun Dari -->
        <div>
            <label for="mthFrom"><b>Bulan/Tahun Dari</b>&nbsp;</label>
            <select name="mthFrom" class="form-select-xs" id="mthFrom">';
            for ($j = 1; $j < 13; $j++) {
                print '<option value="'.$j.'"';
                if ($mthFrom == $j) print 'selected';
                print '>'.$j.'</option>';
            }
print '        </select>
            /
            <select name="yrFrom" class="form-select-xs">';
            while (!$rsYears->EOF) {
                $year = $rsYears->fields['year'];
                print '<option value="'.$year.'"';
                if ($yrFrom == $year) print 'selected';
                print '>'.$year.'</option>';
                $rsYears->MoveNext();
            }
print '        </select>
        </div>

        <!-- Bulan/Tahun Hingga -->
        <div>
            <label for="mthTo"><b>Bulan/Tahun Hingga</b>&nbsp;</label>
            <select name="mthTo" class="form-select-xs" id="mthTo">';
            for ($j = 1; $j < 13; $j++) {
                print '<option value="'.$j.'"';
                if ($mthTo == $j) print 'selected';
                print '>'.$j.'</option>';
            }
print '        </select>
            /
            <select name="yrTo" class="form-select-xs">';
            $rsYears->MoveFirst();
            while (!$rsYears->EOF) {
                $year = $rsYears->fields['year'];
                print '<option value="'.$year.'"';
                if ($yrTo == $year) print 'selected';
                print '>'.$year.'</option>';
                $rsYears->MoveNext();
            }
print '        </select>
        </div>

        <!-- Pilih Syarikat -->
        <div>
            <label for="kod_bank"><b>Pilih Syarikat</b>&nbsp;</label>';
            print selectsyarikatAB($kod_bank,'kod_bank');
print '   </div>

        <!-- Jana Laporan -->
        <div>
            <input type="submit" name="action" value="Jana Laporan" class="btn btn-primary" style="margin-left: 10px;">
        </div>
    </div>
</form>
</center>

</body>
</html>';
?>