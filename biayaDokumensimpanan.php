<?php
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>' . $emaNetis . '</title>
<script>
window.status="Sistem Keanggotaan Koperasi";
</script>
<meta name="Keywords"  content="' . $siteKeyword . '">
<meta name="Description" content="' . $siteDesc . '">
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />	
';
?>

    <style type="text/css">
        #tabcontentcontainer {
            width: 95%;
            /*width of 2nd level content*/
            height: 1.5em;
            /*height of 2nd level content. Set to largest's content height to avoid jittering.*/
        }

        .tabcontent {
            display: none;
        }
    </style>


    <script type="text/javascript">
        /***********************************************
         * DD Tab Menu script- � Dynamic Drive DHTML code library (www.dynamicdrive.com)
         * This notice MUST stay intact for legal use
         * Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
         ***********************************************/

            //Set tab to intially be selected when page loads:
            //[which tab (1=first tab), ID of tab content to display (or "" if no corresponding tab content)]:
        var initialtab = [1, "sc1"]

        //Turn menu into single level image tabs (completely hides 2nd level)?
        var turntosingle = 0 //0 for no (default), 1 for yes

        //Disable hyperlinks in 1st level tab images?
        var disabletablinks = 0 //0 for no (default), 1 for yes


        ////////Stop editting////////////////

        var previoustab = ""

        if (turntosingle == 1)
            document.write('<style type="text/css">\n#tabcontentcontainer{display: none;}\n</style>')

        function expandcontent(cid, aobject) {
            if (disabletablinks == 1)
                aobject.onclick = new Function("return false")
            if (document.getElementById && turntosingle == 0) {
                highlighttab(aobject)
                if (previoustab != "")
                    document.getElementById(previoustab).style.display = "none"
                if (cid != "") {
                    document.getElementById(cid).style.display = "block"
                    previoustab = cid
                }
            }
        }

        function highlighttab(aobject) {
            if (typeof tabobjlinks == "undefined")
                collectddtabs()
            for (i = 0; i < tabobjlinks.length; i++)
                tabobjlinks[i].className = ""
            aobject.className = "current"
        }

        function collectddtabs() {
            var tabobj = document.getElementById("ddtabs")
            tabobjlinks = tabobj.getElementsByTagName("A")
        }

        function do_onload() {
            collectddtabs()
            expandcontent(initialtab[1], tabobjlinks[initialtab[0] - 1])
        }

        if (window.addEventListener)
            window.addEventListener("load", do_onload, false)
        else if (window.attachEvent)
            window.attachEvent("onload", do_onload)
        else if (document.getElementById)
            window.onload = do_onload
    </script>
    </head>

    <div class="table-responsive">
        <table border="0" cellspacing="1" cellpadding="3" width="99%" align="right">
            <tr>
                <td>
                    <?php
                    /*********************************************************************************
                    Project		:	iKOOP.com.my
                    Filename	: 	biayadokumen
                    Date 		: 	26/6/2006
                     *********************************************************************************/
                    //include("common.php");
                    include("koperasiQry.php");
                    date_default_timezone_set("Asia/Jakarta");

                    //check this loan had to be guarantor
                    $loList = array();
                    $sSQL = "SELECT *
		FROM `general`
		WHERE category = 'C'
		AND c_gurrantor =1";
                    $rs = &$conn->Execute($sSQL);
                    if ($rs->RowCount() <> 0) {
                        while (!$rs->EOF) {
                            array_push($loList, $rs->fields(ID));
                            $rs->MoveNext();
                        }
                    }
                    //print_r($_POST);
                    //$conn->debug =1;
                    $rem = "";

                    if (!$prepare && !$review && !$ajkStat1 && !$ajkStat2) {
                        $ctlprepare = '';
                        $ctlreview = 'disabled';
                        $ctlajkStat1 = 'disabled';
                        $ctlajkStat2 = 'disabled';
                        $ctlDisediakanB = 'disabled';
                        $ctlDisahkanB  = 'disabled';
                        $rem = "Status: Dokumen masih belum disediakan dan diproses.";
                    }

                    $applyDate = dlookup("loans", "applyDate", "loanID=" . $pk);

                    if (!isset($totalA15)) $totalA15 = "0";
                    if (!isset($totalFee)) $totalFee = "0";
                    if (!isset($total)) $total = "0";
                    if (!isset($totalFeePA1)) $totalFeePA1 = "0";
                    if (!isset($totalPB1)) $totalPB1 = "0";
                    if (!isset($balPA1)) $balPA1 = "0";
                    if (!isset($totalFeePA2)) $totalFeePA2 = "0";
                    if (!isset($totalPB2)) $totalPB2 = "0";
                    if (!isset($balPA2)) $balPA2 = "0";
                    if (!isset($totalFeePA3)) $totalFeePA3 = "0";
                    if (!isset($totalPB3)) $totalPB3 = "0";
                    if (!isset($balPA3)) $balPA3 = "0";
                    if (!isset($jamin80yuran)) $jamin80yuran = "0";
                    if (!isset($jaminTot)) $jaminTot = "0";
                    if (!isset($biaya)) $biaya = "0";
                    if (!isset($biayayuran)) $biayayuran = "0";
                    if (!isset($biayaTot)) $biayaTot = "0";
                    if (!isset($gajiTot)) $gajiTot = "0";
                    if (!isset($gajiPot)) $gajiPot = "0";
                    if (!isset($gajiPotB)) $gajiPotB = "0";
                    if (!isset($gajiBersih)) $gajiBersih = "0";
                    if (!isset($potBenar)) $potBenar = "0";
                    if (!isset($potBaru)) $potBaru = "0";
                    if (!isset($btindih)) $btindih = "0";
                    if (!isset($btindihUntung)) $btindihUntung = "0";
                    if (!isset($btindihCaj)) $btindihCaj = "0";
                    if (!isset($btindihBal)) $btindihBal = "0";
                    if (!isset($newWajibVal)) $newWajibVal = "0";
                    if (!isset($yuranSedia)) $yuranSedia = "0";
                    if (!isset($status)) $status = "0";
                    if (!isset($lpotAsal)) $lpotAsal = "0";
                    if (!isset($lpotUntung)) $lpotUntung = "0";
                    if (!isset($lpotBiaya)) $lpotBiaya = "0";
                    if (!isset($lpotBulan)) $lpotBulan = "0";
                    if (!isset($lpotAsalM)) $lpotAsalM = "0";
                    if (!isset($lpotUntungM)) $lpotUntungM = "0";
                    if (!isset($lpotBiayaM)) $lpotBiayaM = "0";
                    if (!isset($lpotBulanM)) $lpotBulanM = "0";
                    if (!isset($lpotAsalN)) $lpotAsalN = "0";
                    if (!isset($lpotUntungN)) $lpotUntungN = "0";
                    if (!isset($lpotBiayaN)) $lpotBiayaN = "0";
                    if (!isset($lpotBulanN)) $lpotBulanN = "0";
                    if (!isset($remark)) $remark = "0";



                    if (isset($pk)) { //$pk = $loanID; //get loan id
                        if ($SubmitForm <> "Disediakan" && $SubmitForm <> "Disemak") {
                            $sSQL = "SELECT a1, b1, c1, a2, b2, c2, a3, b3, c3, a4, b4, c4, yuran, 
		rnoBaucer, rnoBond, rcreatedDate, rpreparedby, approvedBy, prepare, review, remarkPrepare, remarkReview,
		ajk1, ajk2, ajk3, ajkDate1, ajkDate2, ajkStat1, ajkStat2, result, yuranBul, yuranSedia,	
		createDate, createdBy, prepare,	prepareDate, prepareBy, review, reviewDate, reviewBy, remarkajk1, remarkajk2
		FROM loandocs WHERE  loanID='" . $pk . "'";
                            $rs = &$conn->Execute($sSQL);


                            //print_r($rs);
                            $a1 = $rs->fields('a1');
                            $b1 = $rs->fields('b1');
                            $c1 = $rs->fields('c1');
                            $a2 = $rs->fields('a2');
                            $b2 = $rs->fields('b2');
                            $c2 = $rs->fields('c2');
                            $a3 = $rs->fields('a3');
                            $b3 = $rs->fields('b3');
                            $c3 = $rs->fields('c3');
                            $a4 = $rs->fields('a4');
                            $b4 = $rs->fields('b4');
                            $c4 = $rs->fields('c4');
                            $yuran = $rs->fields('yuran');

                            //----------- for check guarrantor update date ---------------
                            $createDate = $rs->fields('createDate');
                            $createdBy = $rs->fields('createdBy');
                            $prepare = $rs->fields('prepare');
                            $prepareDate = $rs->fields('prepareDate');
                            $prepareBy = $rs->fields('prepareBy');
                            $review = $rs->fields('review');
                            $reviewDate = $rs->fields('reviewDate');
                            $reviewBy = $rs->fields('reviewBy');
                            //------------- end ----------------

                            //echo "check value $a1- $b1- $c1- $b2 -$b2 - $b2 -$c1- $c2- $c3";
                            if ($rs->fields(rcreatedDate)) {
                                $rnoBaucer = $rs->fields(rnoBaucer);
                                $idSedia = dlookup("vauchers", "disediakan", "no_baucer ='" . $rnoBaucer . "'");
                                $rpreparedby = dlookup("users", "name", "userID='" . $idSedia . "'");
                                $rcreatedDate = toDate("d/m/y", $rs->fields(rcreatedDate));
                                //$rpreparedby = $rs->fields(rpreparedby);
                            }

                            if ($rs->fields(ajkStat2)) {
                                $idSah = dlookup("vauchers", "disahkan", "no_baucer ='" . $rnoBaucer . "'");
                                $rnoBond = $rs->fields(rnoBond);
                                $approvedBy = dlookup("users", "name", "userID='" . $idSah . "'");
                                //$approvedBy = $rs->fields(approvedBy);
                            }

                            //if($rs->fields(ajk1)){
                            $ajk1 = $rs->fields(ajk1);
                            $ajk2 = $rs->fields(ajk2);
                            $ajk3 = $rs->fields(ajk3);
                            $ajkDate1 = toDate("d/m/y", $rs->fields(ajkDate1));
                            $ajkDate2 = toDate("d/m/y", $rs->fields(ajkDate2));
                            $lulusb = ($rs->fields(result) == "lulus" ? "checked" : "");
                            $tolakb = ($rs->fields(result) == "tolak" ? "checked" : "");
                            $prepare = $rs->fields(prepare);
                            $review = $rs->fields(review);
                            $ajkStat1 = $rs->fields(ajkStat1);
                            $ajkStat2 = $rs->fields(ajkStat2);
                            $yuranBul = $rs->fields('yuranBul');
                            $yuranSedia = $rs->fields('yuranSedia');
                            if ($review) $prepared = true;

                            if ($prepare && !$review && !$ajkStat1 && !$ajkStat2) {
                                $ctlprepare = 'disabled';
                                $ctlreview = '';
                                $ctlajkStat1 = 'disabled';
                                $ctlajkStat2 = 'disabled';
                                $rem = "Status: Dokumen sudah diproses dan perlu diperiksa.";
                                $remStat = 0;
                            } elseif ($prepare && $review && !$ajkStat1 && !$ajkStat2) {
                                $ctlprepare = 'disabled';
                                $ctlreview = 'disabled';
                                $ctlajkStat1 = '';
                                $ctlajkStat2 = 'disabled';
                                $rem = "Status: Dokumen memerlukan persetujuan komite 1";
                                $remStat = 1;
                            } elseif ($prepare && $review && $ajkStat1 && !$ajkStat2) {
                                $ctlprepare = 'disabled';
                                $ctlreview = 'disabled';
                                $ctlajkStat1 = 'disabled';
                                $ctlajkStat2 = '';
                                $rem = "Status: Dokumen memerlukan persetujuan komite 2";
                                $remStat = 1;
                            } elseif ($prepare && $review && $ajkStat1 && $ajkStat2) {
                                $ctlprepare = 'disabled';
                                $ctlreview = 'disabled';
                                $ctlajkStat1 = 'disabled';
                                $ctlajkStat2 = 'disabled';
                                $ctlDisediakanB = '';
                                $ctlDisahkanB  = '';
                                $rem = "Status: Dokumen disetujui dan siap untuk pengeluaran voucher.";
                                $remStat = 1;
                            }
                            if ($rs->fields(result) == "tolak" && $ajkStat2) $rem = "Status: Dokumen permohonan simpanan ditolak";
                            //}
                        }
                    }

                    $sFileName		= "?vw=biayaDokumen&mn=906&pk=" . $pk;
                    $sActionFileName = "?vw=biayaDokumen&mn=906";
                    $title     		= "Pemeriksaan Komite";

                    $koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

                    if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
                        print  '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
                    }

                    //************************
                    // jumlah semua pinjaman oleh anggota yg diluluskan
                    if ($pk) $userID = dlookup("loans", "userID", "loanID='" . $pk . "'"); //get user id
                    $loanNo = dlookup("loans", "loanNo", "loanID='" . $pk . "'");

                    $updatedBy 	= get_session("Cookie_userName");
                    $updatedDate = date("Y-m-d H:i:s");
                    if ($_POST) {
                        $debug = false;
                        if ($SubmitForm == "Disediakan") {
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID=" . $pk;
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loandocs SET " .
                                "a1 = '" . $a1 . "'" .
                                ", b1 = '" . $b1 . "'" .
                                ", c1 = '" . $c1 . "'" .
                                ", a2 = '" . $a2 . "'" .
                                ", b2 = '" . $b2 . "'" .
                                ", c2 = '" . $c2 . "'" .
                                ", a3 = '" . $a3 . "'" .
                                ", b3 = '" . $b3 . "'" .
                                ", c3 = '" . $c3 . "'" .
                                ", a4 = '" . $a4 . "'" .
                                ", b4 = '" . $b4 . "'" .
                                ", c4 = '" . $c4 . "'" .
                                ", totalA15 = '" . floatval($totalA15) . "'" .
                                ", totalFee = '" . floatval($totalFee) . "'" .
                                ", total = '" . floatval($total) . "'" .
                                ", totalFeePA1 = '" . floatval($totalFeePA1) . "'" .
                                ", totalPB1 = '" . floatval($totalPB1) . "'" .
                                ", balPA1 = '" . floatval($balPA1) . "'" .
                                ", totalFeePA2 = '" . floatval($totalFeePA2) . "'" .
                                ", totalPB2 = '" . floatval($totalPB2) . "'" .
                                ", balPA2 = '" . floatval($balPA2) . "'" .
                                ", totalFeePA3 = '" . floatval($totalFeePA3) . "'" .
                                ", totalPB3 = '" . floatval($totalPB3) . "'" .
                                ", balPA3 = '" . floatval($balPA3) . "'" .
                                ", jamin80yuran = '" . floatval($jamin80yuran) . "'" .
                                ", jaminTot = '" . floatval($jaminTot) . "'" .
                                ", biaya = '" . floatval($biaya) . "'" .
                                ", biayayuran = '" . floatval($biayayuran) . "'" .
                                ", biayaTot = '" . floatval($biayaTot) . "'" .
                                ", gajiTot = '" . floatval($gajiTot) . "'" .
                                ", gajiPot = '" . floatval($gajiPot) . "'" .
                                ", gajiPotB = '" . floatval($gajiPotB) . "'" .
                                ", gajiBersih = '" . floatval($gajiBersih) . "'" .
                                ", potBenar = '" . floatval($potBenar) . "'" .
                                ", potBaru = '" . floatval($potBaru) . "'" .
                                ", btindih = '" . floatval($btindih) . "'" .
                                ", btindihUntung = '" . floatval($btindihUntung) . "'" .
                                ", btindihCaj = '" . floatval($btindihCaj) . "'" .
                                ", btindihBal = '" . floatval($btindihBal) . "'" .
                                ", yuranBul = '" . floatval($yuranBul) . "'" .
                                ", yuranSedia = '" . floatval($yuranSedia) . "'" .
                                ", yuran = '" . $yuran . "'" .
                                ", status = '" . 0 . "'" .
                                ", prepare = '" . 1 . "'" .
                                ", prepareDate = '" . $updatedDate . "'" .
                                ", prepareBy = '" . $updatedBy . "'" .
                                ", lpotAsal = '" . floatval($lpotAsal) . "'" .
                                ", lpotUntung = '" . floatval($lpotUntung) . "'" .
                                ", lpotBiaya = '" . floatval($lpotBiaya) . "'" .
                                ", lpotBulan = '" . floatval($lpotBulan) . "'" .
                                ", lpotAsalM = '" . floatval($lpotAsalM) . "'" .
                                ", lpotUntungM = '" . floatval($lpotUntungM) . "'" .
                                ", lpotBiayaM = '" . floatval($lpotBiayaM) . "'" .
                                ", lpotBulanM = '" . floatval($lpotBulanM) . "'" .
                                ", lpotAsalN = '" . floatval($lpotAsalN) . "'" .
                                ", lpotUntungN = '" . floatval($lpotUntungN) . "'" .
                                ", lpotBiayaN = '" . floatval($lpotBiayaN) . "'" .
                                ", lpotBulanN = '" . floatval($lpotBulanN) . "'" .
                                ", updatedBy = '" . $updatedBy . "'" .
                                ", updatedDate = '" . $updatedDate . "'" .
                                ", remarkPrepare = '" . $remarkPrepare . "'" .
                                ", remark = '" . $remark . "'";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);

                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loans SET " .
                                " status='" . 1 . "'" .
                                ", updatedDate='" . $updatedDate . "'" .
                                ", updatedBy='" . $updatedBy . "'";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);

                            $sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
                                " VALUES ('Simpanan Disiapkan - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
                            $rs = &$conn->Execute($sqlAct);
                        } elseif ($SubmitForm == "Disemak") {
                            $updatedBy 	= get_session("Cookie_userName");
                            $updatedDate = date("Y-m-d H:i:s");
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID=" . $pk;
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loandocs SET " .
                                "a1 = '" . $a1 . "'" .
                                ", b1 = '" . $b1 . "'" .
                                ", c1 = '" . $c1 . "'" .
                                ", a2 = '" . $a2 . "'" .
                                ", b2 = '" . $b2 . "'" .
                                ", c2 = '" . $c2 . "'" .
                                ", a3 = '" . $a3 . "'" .
                                ", b3 = '" . $b3 . "'" .
                                ", c3 = '" . $c3 . "'" .
                                ", a4 = '" . $a4 . "'" .
                                ", b4 = '" . $b4 . "'" .
                                ", c4 = '" . $c4 . "'" .
                                ", totalA15 = '" . floatval($totalA15) . "'" .
                                ", totalFee = '" . floatval($totalFee) . "'" .
                                ", total = '" . floatval($total) . "'" .
                                ", totalFeePA1 = '" . floatval($totalFeePA1) . "'" .
                                ", totalPB1 = '" . floatval($totalPB1) . "'" .
                                ", balPA1 = '" . floatval($balPA1) . "'" .
                                ", totalFeePA2 = '" . floatval($totalFeePA2) . "'" .
                                ", totalPB2 = '" . floatval($totalPB2) . "'" .
                                ", balPA2 = '" . floatval($balPA2) . "'" .
                                ", totalFeePA3 = '" . floatval($totalFeePA3) . "'" .
                                ", totalPB3 = '" . floatval($totalPB3) . "'" .
                                ", balPA3 = '" . floatval($balPA3) . "'" .
                                ", jamin80yuran = '" . floatval($jamin80yuran) . "'" .
                                ", jaminTot = '" . floatval($jaminTot) . "'" .
                                ", biaya = '" . floatval($biaya) . "'" .
                                ", biayayuran = '" . floatval($biayayuran) . "'" .
                                ", biayaTot = '" . floatval($biayaTot) . "'" .
                                ", gajiTot = '" . floatval($gajiTot) . "'" .
                                ", gajiPot = '" . floatval($gajiPot) . "'" .
                                ", gajiPotB = '" . floatval($gajiPotB) . "'" .
                                ", gajiBersih = '" . floatval($gajiBersih) . "'" .
                                ", potBenar = '" . floatval($potBenar) . "'" .
                                ", potBaru = '" . floatval($potBaru) . "'" .
                                ", btindih = '" . floatval($btindih) . "'" .
                                ", btindihUntung = '" . floatval($btindihUntung) . "'" .
                                ", btindihCaj = '" . floatval($btindihCaj) . "'" .
                                ", btindihBal = '" . floatval($btindihBal) . "'" .
                                ", yuranBul = '" . floatval($yuranBul) . "'" .
                                ", yuranSedia = '" . floatval($yuranSedia) . "'" .
                                ", yuran = '" . $yuran . "'" .
                                ", status = '" . 1 . "'" .
                                ", review = '" . 1 . "'" .
                                ", reviewDate = '" . $updatedDate . "'" .
                                ", reviewBy = '" . $updatedBy . "'" .
                                ", lpotAsal = '" . floatval($lpotAsal) . "'" .
                                ", lpotUntung = '" . floatval($lpotUntung) . "'" .
                                ", lpotBiaya = '" . floatval($lpotBiaya) . "'" .
                                ", lpotBulan = '" . floatval($lpotBulan) . "'" .
                                ", lpotAsalM = '" . floatval($lpotAsalM) . "'" .
                                ", lpotUntungM = '" . floatval($lpotUntungM) . "'" .
                                ", lpotBiayaM = '" . floatval($lpotBiayaM) . "'" .
                                ", lpotBulanM = '" . floatval($lpotBulanM) . "'" .
                                ", lpotAsalN = '" . floatval($lpotAsalN) . "'" .
                                ", lpotUntungN = '" . floatval($lpotUntungN) . "'" .
                                ", lpotBiayaN = '" . floatval($lpotBiayaN) . "'" .
                                ", lpotBulanN = '" . floatval($lpotBulanN) . "'" .
                                ", updatedBy = '" . $updatedBy . "'" .
                                ", updatedDate = '" . $updatedDate . "'" .
                                ", remarkReview = '" . $remarkReview . "'" .
                                ", remark = '" . $remark . "'";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);

                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loans SET " .
                                " status='" . 2 . "'" .
                                ", updatedDate='" . $updatedDate . "'" .
                                ", updatedBy='" . $updatedBy . "'";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);

                            $sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
                                " VALUES ('Simpanan Diperiksa - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
                            $rs = &$conn->Execute($sqlAct);
                        } elseif ($DisediakanB) {
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loandocs SET " .
                                " rnoBaucer = '" . $rnoBaucer . "'" .
                                ", rcreatedDate = '" . saveDateDb($rcreatedDate) . "'" .
                                ", rpreparedby = '" . get_session("Cookie_userName") . "'";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);
                        } elseif ($DisahkanB) {
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loandocs SET " .
                                " approvedBy = '" . get_session("Cookie_userName") . "'";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);
                        } elseif ($ajwk1) {
                            $updatedBy 	= get_session("Cookie_userName");
                            $updatedDate = date("Y-m-d H:i:s");
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loandocs SET " .
                                " ajk1='" . $updatedBy . "'" .
                                ", ajkDate1='" . $updatedDate . "'" .
                                ", ajkStat1='" . 1 . "'" .
                                ", remarkajk1='" . $remarkajk1 . "'";
                            if ($lulus)  $sSQL .=  ", result= 'lulus' ";
                            if ($tolak)  $sSQL .=  ", result= 'tolak' ";
                            if (!$tolak && !$lulus)  $sSQL .=  ", result= '' ";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);
                            $sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
                                " VALUES ('Simpanan Disahkan - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
                            $rs = &$conn->Execute($sqlAct);
                        } elseif ($ajwk2) {
                            $updatedBy 	= get_session("Cookie_userName");
                            $updatedDate = date("Y-m-d H:i:s");

                            $loan				= dlookup("loans", "loanType", "loanID= '" . $pk . "'");
                            $codegroup			= dlookup("general", "parentID", "ID= '" . $loan . "'");
                            $prefix				= dlookup("general", "code", "ID= '" . $codegroup . "'");

                            //rnoBond
                            $len = strlen($prefix);

                            $getNo = "SELECT MAX(CAST(right(  rnoBond  , 5 ) AS SIGNED INTEGER )) AS nombor FROM loandocs where rnoBond like '%" . $prefix . "%'";
                            $rsNo = $conn->Execute($getNo);
                            if ($rsNo) {
                                $nombor = intval($rsNo->fields(nombor)) + 1;
                                $nombor = sprintf("%05s",  $nombor);
                                $no_bond = $prefix . $nombor;
                            } else {
                                $no_bond = $prefix . '00001';
                            }

                            if (dlookup("loandocs", "result", "loanID=" . $pk) == "tolak") $no_bond = '';
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loandocs SET " .
                                " ajk2='" . $updatedBy . "'" .
                                ", rnoBond='" . $no_bond . "'" .
                                ", ajkDate2='" . $updatedDate . "'" .
                                ", ajkStat2='" . 1 . "'" .
                                ", remarkajk2='" . $remarkajk2 . "'";
                            if ($lulus)  $sSQL .=  ", result= 'lulus' ";
                            if ($tolak)  $sSQL .=  ", result= 'tolak' ";
                            if (!$tolak && !$lulus)  $sSQL .=  ", result= '' ";
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);
                            $sSQL = "";
                            $sWhere = "";
                            $sWhere = "loanID='" . $pk . "'";
                            $sWhere = " WHERE (" . $sWhere . ")";
                            $sSQL	= "UPDATE loans SET " .
                                //" status='" . 3 . "'".
                                " updatedDate='" . $updatedDate . "'" .
                                ", updatedBy='" . $updatedBy . "'";
                            if ($lulus) {
                                $sSQL .= ", startPymtDate='" . $updatedDate . "'";

                                $sSQL .= ", stat_agree='" . 1 . "'";

                                $sSQL .= ", status='" . 3 . "'";
                                $sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
                                    " VALUES ('Simpanan Disetujui - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
                                $rs = &$conn->Execute($sqlAct);
                            }
                            if ($tolak) {
                                $sSQL .= ", isRejected='" . 1 . "'";
                                $sSQL .= ", rejectedDate='" . $updatedDate . "'";
                                $sSQL .=  ", status='" . 4 . "'";
                                $sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
                                    " VALUES ('Simpanan Ditolak - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
                                $rs = &$conn->Execute($sqlAct);
                            }
                            $sSQL = $sSQL . $sWhere;
                            if ($debug) print '<br>' . $sSQL;
                            else $rs = &$conn->Execute($sSQL);
                            //}

                            //newWajibVal yuranSedia
                            if ($yuranBul > $yuranSedia) {
                                $sSQL = "";
                                $sWhere = "";
                                $sWhere = "userID='" . dlookup("loans", "userID", "loanID='" . $pk . "'") . "'";
                                $sWhere = " WHERE (" . $sWhere . ")";
                                $sSQL	= "UPDATE userdetails SET " .
                                    " monthFee='" . $yuranBul . "'";
                                $sSQL = $sSQL . $sWhere;
                                if ($debug) print '<br>' . $sSQL;
                                else $rs = &$conn->Execute($sSQL);
                            }
                        }
                        //refresh page
                        if (!$debug) print '<script>window.location="?vw=biayaDokumen&pk=' . $pk . '&mn=906";</script>';
                    }

                    if ($a1 == "on") $a1 = "checked";
                    if ($a2 == "on") $a2 = "checked";
                    if ($a3 == "on") $a3 = "checked";
                    $a4 = ($a4 == "on" ? "checked" : "");
                    $b1 = ($b1 == "on" ? "checked" : "");
                    $b2 = ($b2 == "on" ? "checked" : "");
                    $b3 = ($b3 == "on" ? "checked" : "");
                    $b4 = ($b4 == "on" ? "checked" : "");
                    $c1 = ($c1 == "on" ? "checked" : "");
                    $c2 = ($c2 == "on" ? "checked" : "");
                    $c3 = ($c3 == "on" ? "checked" : "");
                    $c4 = ($c4 == "on" ? "checked" : "");
                    if ($yuran == "yurana") {
                        $yurana = "checked";
                        $potyuran = "20.00";
                    } elseif ($yuran == "yuranb") {
                        $yuranb = "checked";
                        $potyuran = "30.00";
                    } elseif ($yuran == "yuranc") {
                        $yuranc = "checked";
                        $potyuran = "50.00";
                    } elseif ($yuran == "yurand") {
                        $yurand = "checked";
                        $potyuran = "80.00";
                    } elseif ($yuran == "yurane") {
                        $yurane = "checked";
                        $potyuran = "100.00";
                    }

                    if ($userID) {
                        $sqlGet = "select sum(amt) as amt from userstates where userID = '" . $userID . "' and payType = 'A'";
                        $GettotA =  &$conn->Execute($sqlGet);
                        $totalA = $GettotA->fields(amt); //get total debit
                        if (!$totalA > 0) $totalA = 0;
                        $totalA15 = $totalA * 15;

                        $sqlGet = "select sum(amt) as amt from userstates where userID = '" . $userID . "' and payType = 'B'";
                        $GettotB =  &$conn->Execute($sqlGet);
                        $totalB = $GettotB->fields(amt); //get total kredit
                        if (!$totalB > 0) $totalB = 0;

                        $sqlGet = "SELECT sum(loanAmt) as totalLoan FROM `loans` where userID = '" . $userID . "' and isApproved = 1";
                        $GettotLoan =  &$conn->Execute($sqlGet);
                        $totalLoan = $GettotLoan->fields(totalLoan); //get total loan maded
                        //--------------------
                        $totalFee = getTotFees($userID, date("Y"));
                        $total = $totalFee +  $totalA15;
                        //1. $totalA15, $totalFee, $total,

                        //$jamin80 = $totalFee * 0.8;
                        $jamin80yuran = $totalFee;
                        $jaminTot = $totalFee * 0.8;
                        //3. jamin80, jamin80yuran, jaminTot,

                        $biaya = dlookup("loans", "loanAmt", "loanID='" . $pk . "'");
                        $biayayuran = $jaminTot;
                        $biayaTot = $biaya - $biayayuran;
                        //4. biaya, biayayuran, biayaTot,

                        //5. gajiTot, gajiPot, gajiPotB, gajiBersih,
                        $gajiTot = $totalA;
                        $gajiPot = $totalB; //$Get->fields(b) + $Get->fields(c);
                        $gajiPotB = dlookup("loans", "pokok", "loanID='" . $pk . "'") + dlookup("loans", "untung", "loanID='" . $pk . "'");
                        $gajiBersih = $gajiTot - ($gajiPot + $gajiPotB);

                        //6.potBenar, potBaru,
                        if (dlookup("loans", "houseLoan", "loanID='" . $pk . "'") == 1) {
                            $psen = 75;
                        } else {
                            $psen = 50;
                        }
                        $potBenar = $totalA * $psen / 100;
                        $potBaru = $gajiPot + $gajiPotB;

                        $sql = "SELECT *, month(startPymtDate) as m, year(startPymtDate) as y FROM `loans` where status = 3 and loanID <> " . $pk . " and userID = '" . $userID . "' order by approvedDate desc";
                        $Get =  &$conn->Execute($sql);
                        if ($Get->RowCount() <> 0) {
                            $btindih = $Get->fields(loanAmt);
                            $btindihUntung = $Get->fields(untung);
                            $mth = $Get->fields(m);
                            $yr = $Get->fields(y);
                            //$btindihCaj = 0.00;//$Get->fields(c);
                            $btindihBal = $Get->fields(outstandingAmt);
                        } else {
                            $btindih = 0;
                            $btindihUntung = 0;
                            $mth = 0;
                            $yr = 0;
                            $btindihBal = 0;
                        }

                        if ($mth && $yr) {
                            $tempohLoan = dlookup("loans", "loanPeriod", "loanID='" . $pk . "'");

                            $yrmthend1 = getYrMth($yr, $mth, $tempohLoan); //last month pay
                            $yrmthend2 = getYrMth($yr, $mth, $tempohLoan / 2); //half of period
                            $yrmthend3 = getYrMth($yr, $mth, 12); //a year after start pays
                            $ynow = date('Y');
                            $mnow = date('n');
                            $yrmthnow = sprintf("%04d%02d", $ynow, $mnow);

                            if ($yrmthnow < $yrmthend3) {
                                $btindihCaj = 100.00;
                            } elseif ($yrmthnow < $yrmthend2) {
                                $btindihCaj = 50.00;
                            } elseif ($yrmthnow >= $yrmthend2) {
                                $btindihCaj = 10.00;
                            }
                        } else {
                            $btindihCaj = 0.0;
                        }

                        //8.yuranBul, yuranSedia,
                        $yuranBul  = $potyuran;
                        $yuranSedia = dlookup("userdetails", "monthFee", "userID='" . $userID . "'");
                        $sql = "SELECT loanPeriod,pokok,untung,pokokAkhir,untungAkhir FROM `loans` 
			where loanID = '" . $pk . "' and userID = '" . $userID . "'";
                        $Get =  &$conn->Execute($sql);
                        if ($Get->RowCount() <> 0) {
                            $period = $Get->fields(loanPeriod);
                            $lpotAsal = $Get->fields(pokok);
                            $lpotAsalM = $Get->fields(pokokAkhir);
                            $lpotAsalN = ($lpotAsal * ($period - 1)) + $lpotAsalM;
                            $lpotUntung = $Get->fields(untung);
                            $lpotUntungM = $Get->fields(untungAkhir);
                            $lpotUntungN = ($lpotUntung * ($period - 1)) + $lpotUntungM;
                            $lpotBiaya = $lpotAsal + $lpotUntung;
                            $lpotBiayaM = $lpotAsalM + $lpotUntungM;
                            $lpotBiayaN = ($lpotBiaya * ($period - 1)) + $lpotBiayaM;
                            $lpotBulan = $lpotBiaya + $yuranSedia;
                            $lpotBulanM = $lpotBiayaM + $yuranSedia;
                            $lpotBulanN = ($lpotBulan * ($period - 1)) + $lpotBulanM;
                        } else {
                            $period = 0;
                            $lpotAsal = 0;
                            $lpotAsalM = 0;
                            $lpotAsalN = 0;
                            $lpotUntung = 0;
                            $lpotUntungM = 0;
                            $lpotUntungN = ($lpotUntung * ($period - 1)) + $lpotUntungM;
                            $lpotBiaya = $lpotAsal + $lpotUntung;
                            $lpotBiayaM = $lpotAsalM + $lpotUntungM;
                            $lpotBiayaN = ($lpotBiaya * ($period - 1)) + $lpotBiayaM;
                            $lpotBulan = $lpotBiaya + $yuranSedia;
                            $lpotBulanM = $lpotBiayaM + $yuranSedia;
                            $lpotBulanN = ($lpotBulan * ($period - 1)) + $lpotBulanM;
                        }
                        //rnoBaucer, rnoBond, rcreatedDate, rpreparedby, approvedBy

                        //find who this member gurranty
                        $bList = array(); //gurranteed loans
                        $biayaTotal = array(); //person loan
                        $sSQL = "SELECT A.loanID, A.loanAmt FROM loans A, userdetails B WHERE ( A.userID = B.userID AND ( A.penjaminID1 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "' OR A.penjaminID3 = '" . $userID . "')) ORDER BY A.applyDate";
                        $rs = &$conn->Execute($sSQL);
                        $j = 0;
                        $memberIDy = dlookup("userdetails", "memberID", "userID='" . $userID . "'"); //get user member id
                        if ($rs->RowCount() <> 0) {
                            while (!$rs->EOF) {
                                $loID = $rs->fields(loanID);
                                $loAmt = $rs->fields(loanAmt);
                                if ($pid = dlookup("loans", "penjaminID1", "loanID='" . $loID . "'")) { //compare get gurrentee id
                                    if ($pid == $memberIDy) $field = 1;
                                    $bList[$j] = $loID;
                                    $biayaTotal[$j] = $loAmt;
                                }
                                if ($pid = dlookup("loans", "penjaminID2", "loanID='" . $loID . "'")) {
                                    if ($pid == $memberIDy) $field = 2;
                                    $bList[$j] = $loID;
                                    $biayaTotal[$j] = $loAmt;
                                }
                                if ($pid = dlookup("loans", "penjaminID3", "loanID='" . $loID . "'")) {
                                    if ($pid == $memberIDy)	$field = 3;
                                    $bList[$j] = $loID;
                                    $biayaTotal[$j] = $loAmt;
                                }
                                $j++;
                                $rs->MoveNext();
                            }
                        }
                    } //end debit kredit and get gurrantor list

                    function getGroup($loan)
                    {
                        $deductID = dlookup("general", "c_Deduct", "ID='" . $loan . "'");
                        $code = dlookup("general", "code", "ID='" . $deductID . "'");
                        $group = dlookup("codegroup", "groupNo", "codeNo='" . $code . "'");
                        return $group;
                    }

                    $group1 = getGroup(dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number")));


                    //get gurrantor debit and kredit
                    if ($pid1 = dlookup("loans", "penjaminID1", "loanID='" . $pk . "'")) {
                        $sqlGet = "select sum(amt) as amt from userstates where userID = '" . $pid1 . "' and payType = 'A'";
                        $GettotA =  &$conn->Execute($sqlGet);
                        $totalPA1 = $GettotA->fields(amt);
                        $totalTA1 = $totalPA1 * 15;

                        $yr = date("Y");
                        $totalFee1 = getFees($pid1, $yr);
                        $totalShare1 = getShares($pid1, $yr);

                        $totalFeePA1 = $totalTA1 + $totalFee1 + $totalShare1;

                        //$totalFeePA1 = $totalTA1 + dlookup("userdetails", "totalFee", "userID=" . $pid1) + dlookup("userdetails", "totalShare", "userID=" . $pid1);

                        //$sqlGet = "select sum(amt) as amt from userstates where userID = '".$pid1."' and payType = 'B'";
                        //$GettotPB1 =  &$conn->Execute($sqlGet);
                        //$totalPB1 = $GettotPB1->fields(amt);
                        $chkloanType1 = array();
                        $chkuserID1 = array();
                        $chkloanType1[] = dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number"));
                        $chkuserID1[] = $userID;	//get all loan that being guaranted

                        //get just loan apply
                        $sqlGet = "SELECT a.loanID, a.loanType, a.loanAmt, a.userID, b.ajkDate2 "
                            . " FROM loans a, loandocs b "
                            . " WHERE a.loanID = b.loanID and "
                            . " (a.penjaminID1 = '" . $pid1 . "' OR a.penjaminID2 = '" . $pid1 . "' OR a.penjaminID3 = '" . $pid1 . "') "
                            . " AND a.status = 3 AND a.loanID <> " . $pk . " AND b.ajkDate2 < '" . $applyDate . "' ORDER BY applyDate DESC";

                        $GetLoan =  &$conn->Execute($sqlGet); //ctLoanStatusDept($q,$by,$filter,$dept);
                        $tot1 = 0;
                        if ($GetLoan->RowCount() <> 0) {
                            while (!$GetLoan->EOF) {

                                //if($GetLoan->fields(userID) == $chkuserID1 && $GetLoan->fields(loanType) == $chkloanType1) {
                                //	$GetLoan->MoveNext();
                                //	continue;
                                //}

                                if (in_array($GetLoan->fields(userID), $chkuserID1) && in_array($GetLoan->fields(loanType), $chkloanType1)) {
                                    $GetLoan->MoveNext();
                                    continue;
                                } else {
                                    $chkloanType1[] = $GetLoan->fields(loanType);
                                    $chkuserID1[] = $GetLoan->fields(userID);
                                }

                                //------------------- actively check balance from transaction ---------------------
                                $sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
						FROM loans where loanID = '" . $GetLoan->fields(loanID) . "'";
                                $Get =  &$conn->Execute($sqlLoan);

                                if ($Get->RowCount() > 0) {
                                    $loanAmt = $Get->fields(loanAmt);
                                    $totUntung = $Get->fields(totUntung);
                                    $loanType = $Get->fields(loanType);
                                }

                                $sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
                                $Get =  &$conn->Execute($sql);
                                if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

                                $sql = "select rnoBond FROM loandocs where loanID = '" . $GetLoan->fields(loanID) . "'";
                                $Get =  &$conn->Execute($sql);
                                if ($Get->RowCount() > 0) $nobond = $Get->fields(rnoBond);

                                $getOpen = "SELECT 
					SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
					SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
					FROM transaction
					WHERE
					pymtRefer = '" . $nobond . "'
					AND deductID = '" . $c_Deduct . "' 
					AND month(createdDate) <= " . date("m") . "
					AND year(createdDate) <= " . date("Y") . "
					GROUP BY pymtRefer";
                                $rsOpen = $conn->Execute($getOpen);
                                if ($rsOpen->RowCount() == 1) $bakiPkk =  $loanAmt - $rsOpen->fields(yuranKt); //$bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
                                else   $bakiPkk =  $loanAmt;

                                $loanBalance = $bakiPkk;
                                $monthFee = dlookup("userdetails", "monthFee", "userID='" . $GetLoan->fields(userID) . "'");
                                $tot =  $loanBalance - (0.8 * $monthFee);
                                $tot1 = $tot1 + $tot;
                                //----
                                $crtuserID = $GetLoan->fields(userID);
                                //$nama = dlookup("users", "name", "userID='".$userID."'");
                                //$nobond = dlookup("loandocs", "rnoBond", "loanID='".$GetLoan->fields(loanID)."'");
                                $totYrnShm =  getTotFees($crtuserID, date("Y"));
                                //$totYrnShm = dlookup("userdetails", "totalFee", "userID=" . $userID) + dlookup("userdetails", "totalShare", "userID=" . $userID);
                                $tot80 = $totYrnShm * 0.8;
                                //$bal = dlookup("userdetails", "totalFee", "userID=" . $userID) - $tot80;
                                //$bal = getFees($userID, date("Y")) - $tot80;
                                $bal = $loanBalance - $tot80;
                                $bal1 = $bal1 + $bal;

                                $GetLoan->MoveNext();
                            }
                        }

                        $totalPB1 = $bal1;
                        $balPA1 = $totalFeePA1 - $totalPB1;
                        //$totalPB1 = $tot1;
                        //$balPA1 = $totalFeePA1 - $tot1;

                        //$totalPB1;
                        //a, b, a-b : $totalFeePA1,$totalPB1,$balPA1
                    }

                    if ($pid2 = dlookup("loans", "penjaminID2", "loanID='" . $pk . "'")) {
                        $sqlGet = "select sum(amt) as amt from userstates where userID = '" . $pid2 . "' and payType = 'A'";
                        $GettotA =  &$conn->Execute($sqlGet);
                        $totalPA2 = $GettotA->fields(amt);
                        $totalTA2 = $totalPA2 * 15;

                        //$sqlGet = "select sum(amt) as amt from userstates where userID = '".$pid2."' and payType = 'B'";
                        //$GettotPB2 =  &$conn->Execute($sqlGet);
                        //$totalPB2 = $GettotPB2->fields(amt);

                        $yr = date("Y");
                        $totalFee2 = getFees($pid2, $yr);
                        $totalShare2 = getShares($pid2, $yr);

                        $totalFeePA2 = $totalTA2 + $totalFee2 + $totalShare2;

                        //$totalFeePA2 = $totalTA2 + dlookup("userdetails", "totalFee", "userID=" . $pid2) + dlookup("userdetails", "totalShare", "userID=" . $pid1);
                        $chkloanType2 = array();
                        $chkuserID2 = array();
                        $chkloanType2[] = dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number"));
                        $chkuserID2[] = $userID;	//get all loan that being guaranted

                        //get all loan that being guaranted
                        //get just loan apply
                        $sqlGet = "SELECT a.loanID, a.loanType, a.loanAmt, a.userID, b.ajkDate2 "
                            . " FROM loans a, loandocs b "
                            . " WHERE a.loanID = b.loanID and "
                            . " (a.penjaminID1 = '" . $pid2 . "' OR a.penjaminID2 = '" . $pid2 . "' OR a.penjaminID3 = '" . $pid2 . "') "
                            . " AND a.status = 3 AND a.loanID <> " . $pk . " AND b.ajkDate2 < '" . $applyDate . "' ORDER BY applyDate DESC";
                        $GetLoan =  &$conn->Execute($sqlGet); //ctLoanStatusDept($q,$by,$filter,$dept);

                        $tot2 = 0;
                        if ($GetLoan->RowCount() <> 0) {
                            while (!$GetLoan->EOF) {

                                //if($GetLoan->fields(userID) == $userID && $GetLoan->fields(loanType) == $chkloanType) {
                                //	$GetLoan->MoveNext();
                                //	continue;
                                //}
                                if (in_array($GetLoan->fields(userID), $chkuserID2) && in_array($GetLoan->fields(loanType), $chkloanType2)) {
                                    $GetLoan->MoveNext();
                                    continue;
                                } else {
                                    $chkloanType2[] = $GetLoan->fields(loanType);
                                    $chkuserID2[] = $GetLoan->fields(userID);
                                }

                                //------------------- actively check balance from transaction ---------------------
                                $sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
						FROM loans where loanID = '" . $GetLoan->fields(loanID) . "'";
                                $Get =  &$conn->Execute($sqlLoan);

                                if ($Get->RowCount() > 0) {
                                    $loanAmt = $Get->fields(loanAmt);
                                    $totUntung = $Get->fields(totUntung);
                                    $loanType = $Get->fields(loanType);
                                }

                                $sql = "SELECT c_Deduct FROM general where ID = '" . $loanType . "'";
                                $Get =  &$conn->Execute($sql);
                                if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

                                $sql = "select rnoBond FROM loandocs where loanID = '" . $GetLoan->fields(loanID) . "'";
                                $Get =  &$conn->Execute($sql);
                                if ($Get->RowCount() > 0) $nobond = $Get->fields(rnoBond);

                                $getOpen = "SELECT 
					SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
					SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
					FROM transaction
					WHERE
					pymtRefer = '" . $nobond . "'
					AND deductID = '" . $c_Deduct . "' 
					AND month(createdDate) <= " . date("m") . "
					AND year(createdDate) <= " . date("Y") . "
					GROUP BY pymtRefer";
                                $rsOpen = $conn->Execute($getOpen);
                                if ($rsOpen->RowCount() == 1) $bakiPkk =  $loanAmt - $rsOpen->fields(yuranKt); //$bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
                                else   $bakiPkk =  $loanAmt;

                                $loanBalance = $bakiPkk;
                                $monthFee = dlookup("userdetails", "monthFee", "userID='" . $GetLoan->fields(userID) . "'");
                                $tot =  $loanBalance - (0.8 * $monthFee);
                                $tot2 = $tot2 + $tot;
                                //----
                                $crtuserID = $GetLoan->fields(userID);
                                //$nama = dlookup("users", "name", "userID='".$userID."'");
                                //$nobond = dlookup("loandocs", "rnoBond", "loanID='".$GetLoan->fields(loanID)."'");
                                $totYrnShm =  getTotFees($crtuserID, date("Y"));
                                //$totYrnShm = dlookup("userdetails", "totalFee", "userID=" . $userID) + dlookup("userdetails", "totalShare", "userID=" . $userID);
                                $tot80 = $totYrnShm * 0.8;
                                //$bal = dlookup("userdetails", "totalFee", "userID=" . $userID) - $tot80;
                                //$bal = getFees($userID, date("Y")) - $tot80;
                                $bal = $loanBalance - $tot80;
                                $bal2 = $bal2 + $bal;
                                $GetLoan->MoveNext();
                            }
                        }

                        $totalPB2 = $bal2;
                        $balPA2 = $totalFeePA2 - $totalPB2;
                    }

                    if ($pid3 = dlookup("loans", "penjaminID3", "loanID='" . $pk . "'")) {
                        $sqlGet = "SELECT sum(amt) as amt FROM userstates where userID = '" . $pid3 . "' and payType = 'A'";
                        $GettotA =  &$conn->Execute($sqlGet);
                        $totalPA3 = $GettotA->fields(amt);
                        $totalTA3 = $totalPA3 * 15;

                        //$sqlGet = "select sum(amt) as amt from userstates where userID = '".$pid3."' and payType = 'B'";
                        //$GettotPB3 =  &$conn->Execute($sqlGet);
                        //$totalPB3 = $GettotPB3->fields(amt);

                        $yr = date("Y");
                        $totalFee3 = getFees($pid3, $yr);
                        $totalShare3 = getShares($pid3, $yr);

                        $totalFeePA3 = $totalTA3 + $totalFee3 + $totalShare3;

                        //$totalFeePA3 = $totalTA3 + dlookup("userdetails", "totalFee", "userID=" . $pid3) + dlookup("userdetails", "totalShare", "userID=" . $pid1);
                        $chkloanType3 = array();
                        $chkuserID3 = array();
                        $chkloanType3[] = dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number"));
                        $chkuserID3[] = $userID;	//get all loan that being guaranted

                        //get all loan that being guaranted
                        //get just loan apply
                        $sqlGet = "SELECT a.loanID, a.loanType, a.loanAmt, a.userID, b.ajkDate2 "
                            . " FROM loans a, loandocs b "
                            . " WHERE a.loanID = b.loanID and "
                            . " (a.penjaminID1 = '" . $pid3 . "' OR a.penjaminID2 = '" . $pid3 . "' OR a.penjaminID3 = '" . $pid3 . "') "
                            . " AND a.status = 3 AND a.loanID <> " . $pk . " AND b.ajkDate2 < '" . $applyDate . "' ORDER BY applyDate DESC";
                        $GetLoan =  &$conn->Execute($sqlGet); //ctLoanStatusDept($q,$by,$filter,$dept);

                        $tot3 = 0;
                        if ($GetLoan->RowCount() <> 0) {
                            while (!$GetLoan->EOF) {

                                //if($GetLoan->fields(userID) == $userID && $GetLoan->fields(loanType) == $chkloanType) {
                                //	$GetLoan->MoveNext();
                                //	continue;
                                //}

                                if (in_array($GetLoan->fields(userID), $chkuserID3) && in_array($GetLoan->fields(loanType), $chkloanType3)) {
                                    $GetLoan->MoveNext();
                                    continue;
                                } else {
                                    $chkloanType3[] = $GetLoan->fields(loanType);
                                    $chkuserID3[] = $GetLoan->fields(userID);
                                }

                                //------------------- actively check balance from transaction ---------------------
                                $sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
						FROM loans where loanID = '" . $GetLoan->fields(loanID) . "'";
                                $Get =  &$conn->Execute($sqlLoan);

                                if ($Get->RowCount() > 0) {
                                    $loanAmt = $Get->fields(loanAmt);
                                    $totUntung = $Get->fields(totUntung);
                                    $loanType = $Get->fields(loanType);
                                }

                                $sql = "SELECT c_Deduct FROM general where ID = '" . $loanType . "'";
                                $Get =  &$conn->Execute($sql);
                                if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

                                $sql = "SELECT rnoBond FROM loandocs where loanID = '" . $GetLoan->fields(loanID) . "'";
                                $Get =  &$conn->Execute($sql);
                                if ($Get->RowCount() > 0) $nobond = $Get->fields(rnoBond);

                                $getOpen = "SELECT 
					SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
					SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
					FROM transaction
					WHERE
					pymtRefer = '" . $nobond . "'
					AND deductID = '" . $c_Deduct . "' 
					AND month(createdDate) <= " . date("m") . "
					AND year(createdDate) <= " . date("Y") . "
					GROUP BY pymtRefer";
                                $rsOpen = $conn->Execute($getOpen);
                                if ($rsOpen->RowCount() == 1) $bakiPkk =  $loanAmt - $rsOpen->fields(yuranKt); //$bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
                                else   $bakiPkk =  $loanAmt;

                                $loanBalance = $bakiPkk;
                                $monthFee = dlookup("userdetails", "monthFee", "userID='" . $GetLoan->fields(userID) . "'");
                                $tot =  $loanBalance - (0.8 * $monthFee);
                                $tot3 = $tot3 + $tot;
                                //----
                                $crtuserID = $GetLoan->fields(userID);
                                //$nama = dlookup("users", "name", "userID='".$userID."'");
                                //$nobond = dlookup("loandocs", "rnoBond", "loanID='".$GetLoan->fields(loanID)."'");
                                $totYrnShm =  getTotFees($crtuserID, date("Y"));
                                //$totYrnShm = dlookup("userdetails", "totalFee", "userID=" . $userID) + dlookup("userdetails", "totalShare", "userID=" . $userID);
                                $tot80 = $totYrnShm * 0.8;
                                //$bal = dlookup("userdetails", "totalFee", "userID=" . $userID) - $tot80;
                                //$bal = getFees($userID, date("Y")) - $tot80;
                                $bal = $loanBalance - $tot80;
                                $bal3 = $bal3 + $bal;


                                $GetLoan->MoveNext();
                            }
                        }

                        $totalPB3 = $bal3;
                        $balPA3 = $totalFeePA3 - $totalPB3;
                    }

                    $loanType		= dlookup("loans", "loanType", "loanID=" . $pk); //get this loan need guarantor or not
                    $pid = 1; //end able link for guarantor detail
                    if (!(in_array($loanType, $loList))) {
                        //$a1= null;
                        //$b1= null;
                        //$c1= null;
                        $a2 = null;
                        $b2 = null;
                        $c2 = null;
                        $a3 = null;
                        $b3 = null;
                        $c3 = null;
                        $a4 = null;
                        $b4 = null;
                        $c4 = null;
                        $chkgurrantor = "true";
                        $totalFeePA1 = 0;
                        $totalPB1 = 0;
                        $balPA1 = 0;
                        $totalFeePA2 = 0;;
                        $totalPB2 = 0;
                        $balPA2 = 0;
                        $totalFeePA3 = 0;;
                        $totalPB3 = 0;
                        $balPA3 = 0;
                        $pid = 0; //disable link for guarantor
                    }


                    //************************
                    // MODUL PEMBIAYAAN
                    $memberID       = $userID;
                    $memberNo		= $userID;
                    $memberName		= '<a href="?vw=biaya&mn=906&pk=' . $pk . '&userID=' . $userID . '">' . dlookup("users", "name", "userID=" . $userID) . '</a>';
                    $loanPeriod		= dlookup("loans", "loanPeriod", "loanID=" . tosql($pk, "Number"));
                    $amtLoan		= dlookup("loans", "loanAmt", "loanID=" . tosql($pk, "Number"));
                    $loanTypeID		= dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number"));
                    $loanTypeCode	= dlookup("general", "code", "ID=" . tosql($loanTypeID, "Number"));
                    $loanTypeCodeID	= dlookup("general", "c_Deduct", "ID=" . tosql($loanTypeID, "Number"));
                    $loanType		= dlookup("general", "name", "ID=" . tosql($loanTypeCodeID, "Text"));
                    // FORPULIR PEMERIKSAAN KOMITE SIMPANAN

                    $strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=loan">DAFTAR</a><b>&nbsp;>&nbsp;DOKUMEN PROSES SIMPANAN</b>';

                    print '<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
                        //.'<div style="width: 100%; text-align:left">'
                        . '<div>&nbsp;</div>';
                    if (@$tabb == '') {
                        $tabb = 1;
                    }
                    ?>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="<?php echo $sFileName; ?>&tabb=1" class="nav-link <?php if (@$tabb == 1) {
                                echo "active";
                            } ?>" id="home-tab" aria-controls="home" aria-selected="true">DOKUMEN PROSES</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="<?php echo $sFileName; ?>&tabb=2" class="nav-link <?php if (@$tabb == 2) {
                                echo "active";
                            } ?>" id="profile-tab" aria-controls="profile" aria-selected="false">PERSETUJUAN KOMITE</a>
                        </li>
                    </ul>
                    <br>

                    <!--<div id="ddtabs" class="basictab">
<ul>
<li><a href="?vw=biayaDokumen&pk=<?= $pk ?>&mn=906" onMouseover="expandcontent('sc1', this)">DOKUMEN PROSES</a></li>
<li><a href="?vw=biayaDokumen&view=AJK&pk=<?= $pk ?>&mn=906" onMouseover="expandcontent('sc2', this)">PERSETUJUAN KOMITE</a></li>
</ul>
</div>-->

                    <?php
                    print "<span class='text-danger card-title'>$rem</span>";

                    print '';

                    if ($prepared) { //start prepared

                        if (isset($pk)) { //$pk = $loanID; //get loan id
                            $sSQL = "SELECT *FROM loandocs WHERE  loanID='" . $pk . "'";
                            $rs = &$conn->Execute($sSQL);
                            $arrloandoc = array(
                                'loanID',
                                'userID',
                                'a1',
                                'b1',
                                'c1',
                                'a2',
                                'b2',
                                'c2',
                                'a3',
                                'b3',
                                'c3',
                                'a4',
                                'b4',
                                'c4',
                                'totalA15',
                                'totalFee',
                                'total',
                                'totalFeePA1',
                                'totalPB1',
                                'balPA1',
                                'totalFeePA2',
                                'totalPB2',
                                'balPA2',
                                'totalFeePA3',
                                'totalPB3',
                                'balPA3',
                                'jamin80',
                                'jamin80yuran',
                                'jaminTot',
                                'biaya',
                                'biayayuran',
                                'biayaTot',
                                'gajiTot',
                                'gajiPot',
                                'gajiPotB',
                                'gajiBersih',
                                'potBenar',
                                'potBaru',
                                'btindih',
                                'btindihUntung',
                                'btindihCaj',
                                'btindihBal',
                                'yuranBul',
                                'yuranSedia',
                                'yuran',
                                'status',
                                'createDate',
                                'createdBy',
                                'prepare',
                                'prepareDate',
                                'prepareBy',
                                'review',
                                'reviewDate',
                                'reviewBy',
                                'lpotAsal',
                                'lpotUntung',
                                'lpotBiaya',
                                'lpotBulan',
                                'lpotAsalM',
                                'lpotUntungM',
                                'lpotBiayaM',
                                'lpotBulanM',
                                'lpotAsalN',
                                'lpotUntungN',
                                'lpotBiayaN',
                                'lpotBulanN',
                                'rnoBaucer',
                                'rnoBond',
                                'rcreatedDate',
                                'rpreparedby',
                                'approvedBy',
                                'updatedBy',
                                'updatedDate',
                                'remark',
                                'ajk1',
                                'ajkDate1',
                                'ajkStat1',
                                'remarkajk1',
                                'ajk2',
                                'ajkDate2',
                                'ajkStat2',
                                'remarkajk2',
                                'ajk3',
                                'ajkDate3',
                                'ajkStat3',
                                'result',
                                'remarkPrepare',
                                'remarkReview'
                            );
                            foreach ($arrloandoc as $value) {
                                ${$value} = $rs->fields($value);
                            }

                            $rcreatedDate = toDate("d/m/y", $rcreatedDate);
                            $lulusb = ($result == "lulus" ? "checked" : "");
                            $tolakb = ($result == "tolak" ? "checked" : "");
                        }

                        if ($a1 == "on") $a1 = "checked";
                        if ($a2 == "on") $a2 = "checked";
                        if ($a3 == "on") $a3 = "checked";
                        $a4 = ($a4 == "on" ? "checked" : "");
                        $b1 = ($b1 == "on" ? "checked" : "");
                        $b2 = ($b2 == "on" ? "checked" : "");
                        $b3 = ($b3 == "on" ? "checked" : "");
                        $b4 = ($b4 == "on" ? "checked" : "");
                        $c1 = ($c1 == "on" ? "checked" : "");
                        $c2 = ($c2 == "on" ? "checked" : "");
                        $c3 = ($c3 == "on" ? "checked" : "");
                        $c4 = ($c4 == "on" ? "checked" : "");

                        if ($yuran == "yurana") {
                            $yurana = "checked";
                            $potyuran = "20.00";
                        } elseif ($yuran == "yuranb") {
                            $yuranb = "checked";
                            $potyuran = "30.00";
                        } elseif ($yuran == "yuranc") {
                            $yuranc = "checked";
                            $potyuran = "50.00";
                        } elseif ($yuran == "yurand") {
                            $yurand = "checked";
                            $potyuran = "80.00";
                        } elseif ($yuran == "yurane") {
                            $yurane = "checked";
                            $potyuran = "100.00";
                        }
                        if (dlookup("loans", "houseLoan", "loanID='" . $pk . "'") == 1) {
                            $psen = 75;
                        } else {
                            $psen = 50;
                        }
                        $memberID       = $userID;
                        $memberNo		= $userID;
                        $memberName		= dlookup("users", "name", "userID=" . $userID);
                        $loanPeriod		= dlookup("loans", "loanPeriod", "loanID=" . tosql($pk, "Number"));
                        $amtLoan		= dlookup("loans", "loanAmt", "loanID=" . tosql($pk, "Number"));
                        $loanTypeID		= dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number"));
                        $loanTypeCode	= dlookup("general", "code", "ID=" . tosql($loanTypeID, "Number"));
                        $loanTypeCodeID	= dlookup("general", "c_Deduct", "ID=" . tosql($loanTypeID, "Number"));
                        $loanType		= dlookup("general", "name", "ID=" . tosql($loanTypeCodeID, "Text"));
                    } // end prepared

                    if ($ajkStat2) $pid = 0; //disable link for guarantor when loan approved
                    ?>

                    <div class="tab-content p-3" id="myTabContent">
                        <div class="tab-pane fade <?php if (@$tabb == 1) {
                            echo "active show";
                        } ?>" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <p class="mb-0">
                                <?php
                                print '<div class="card-header mt-2"><b>Formulir Dokumen Proses Simpanan</b></div><BR>';
                                include "biayaDokumenMain.php";
                                print '<br>';
                                ?>
                            </p>
                        </div>
                        <div class="tab-pane fade <?php if (@$tabb == 2) {
                            echo "active show";
                        } ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <p class="mb-0">
                                <?php
                                //module persetujuan komite simpanan
                                //$now = date("Y-m-d");
                                $part = substr(dlookup("userdetails", "approvedDate", "userID=" . tosql($userID, "Text")), 0, 10);
                                $days = (strtotime(date("Y-m-d")) - strtotime($part)); // / (60 * 60 * 24);
                                $now = date("Y-m-d", $days);
                                $part = "1970-01-01";
                                $days = (substr($now, 0, 4) - substr($part, 0, 4)) . ' tahun ' . (substr($now, 5, 2) - substr($part, 5, 2)) . ' bulan ' . (substr($now, 8, 2) - substr($part, 8, 2)) . ' hari ';

                                print 	'
			<div class="card-header mt-2"><b>Formulir Persetujuan Dokumen Simpanan</b></div><br><br>
			<table cellpadding="0" cellspacing="0" width="100%" align="center" bgcolor="">

			<tr><td class="padding1" colspan="2"><u><b>Informasi Anggota</b></u></td></tr>
			<tr>
				<td valign="top" width="60%">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>Nama</td>
							<td>&nbsp;:&nbsp;</td>
							<td>' . $memberName . '</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>Tanggal Keanggotaan</td>
							<td>&nbsp;:&nbsp;</td>
							<td>' . todate("d/m/y", dlookup("userdetails", "approvedDate", "userID=" . tosql($userID, "Text"))) . '</td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3"><u><b>Informasi Simpanan Baru</b></u>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td>Nomor Rekening&nbsp;</td>
							<td>&nbsp;:&nbsp;</td>
							<td>' . dlookup("depositoracc", "accountNumber", "id=" . tosql($pk, "Text")) . '</td>
						</tr>
						<tr>
							<td>Nominal Simpanan:&nbsp;</td>
							<td>&nbsp;:&nbsp;</td>
							<td>Rp ' . number_format(dlookup("depositoracc", "nominal_simpanan", "id=" . tosql($pk, "Text")), 2, ',', '.') . '</td>
						</tr>
					</table>
				</td>
				<td align="left" valign="top" width="40%">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>Nomor Anggota</td>
							<td>&nbsp;:&nbsp;</td>
							<td>' . $memberNo . '</td>
						</tr>
						<tr>
							<td>Saldo Simpanan</td>
							<td>&nbsp;:&nbsp;</td>
							<td>Rp ' . number_format(dlookup("depositoracc", "balance", "id=" . tosql($pk, "Text")), 2, ',', '.') . '</td>
						</tr>
						<tr>
							<td>Lama Menjadi Anggota</td>
							<td>&nbsp;:&nbsp;</td>
							<td>' . $days . '</td>
						</tr>
						<tr rowspan="2">
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;&nbsp;</td>
							 <td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>Sumber Dana&nbsp;</td>
							<td>&nbsp;:&nbsp;</td>
							<td>' . dlookup("depositoracc", "sumber_dana", "id=" . tosql($pk, "Text")) . '</td>
						</tr>
					</table>
				</td>
			</tr>';

                                $sql = "SELECT * FROM `loans` where status = 1 and loanID <> " . $pk . " and userID = '" . $userID . "'";
                                $Get =  &$conn->Execute($sql);

                                print  '<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
			
			
			<form name="MyForm3" action=' . $sFileName . ' method=post onSubmit="return validate(lulus,tolak);">			
			<tr>
				<td align ="center" valign="top" width="50%" colspan="2"><br><u><b>KEPUTUSAN</b></u><br>
				</td>
			</tr>
			<tr>
				<td align ="center" valign="top" width="50%" colspan="2"><u><b><input class="form-check-input" type="checkbox" name="lulus" ' . $lulusb . '> DISETUJUI / <input class="form-check-input" type="checkbox" name="tolak" ' . $tolakb . '> DITOLAK</b></u>
				</td>
			</tr>';
                                $ajkDate1 = toDate("d/m/y", $ajkDate1);
                                $ajkDate2 = toDate("d/m/y", $ajkDate2);

                                print '		<tr>
				<td colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>						
						<tr>
						<td><b>Catatan :</b></td>
						<td><b>Catatan :</b></td>
						</tr><tr>';
                                if ($ajkStat1 == 0) {
                                    print '		
							<td><textarea name="remarkajk1" cols="40" rows="4" class="form-control-sm">' . $remarkajk1 . '</textarea></td>';
                                } else {
                                    print '		
							<td><textarea name="remarkajk1" cols="40" rows="4" class="form-control-sm" readonly>' . $remarkajk1 . '</textarea></td>';
                                }
                                if ($ajkStat2 == 0) {
                                    print '		
							<td><textarea name="remarkajk2" cols="40" rows="4" class="form-control-sm">' . $remarkajk2 . '</textarea></td>';
                                } else {
                                    print '		
							<td><textarea name="remarkajk2" cols="40" rows="4" class="form-control-sm" readonly>' . $remarkajk2 . '</textarea></td>';
                                }

                                print '</tr>
						<tr>
							<td width="34%">Komite 1: ' . $ajk1 . '</td>
							<td width="33%">Komite 2: ' . $ajk2 . '</td>
							<td width="33%">&nbsp;</td>
						</tr>
						<tr>
							<td width="34%">Tanggal : ' . $ajkDate1 . '</td>
							<td width="33%">Tanggal : ' . $ajkDate2 . '</td>
							<td width="33%">&nbsp;</td>
						</tr>						<tr>
							<td width="34%"><input type="Submit" name="ajwk1" class="btn btn-sm btn-secondary" value="Disahkan 1" ' . $ctlajkStat1 . '></td>
							<td width="33%"><input type="Submit" name="ajwk2" class="btn btn-sm btn-secondary" value="Disahkan 2" ' . $ctlajkStat2 . '></td>
							<td width="33%"><!--input type="Submit" name="ajwk3" class="btn btn-sm btn-secondary" value="Disahkan 3"-->&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			</form>
			</table>';

                                ?>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<?php

function getYrMth($year, $month, $period, $log = 0)
{
    $yrDif = intval($period / 12);
    $mthDif = bcmod($period, '12');

    $dif = $month + $mthDif;
    if ($dif > 12) {
        $mthend = $dif - 12;
        $yrend = $year + $yrDif + 1;
    } else {
        $mthend = $dif;
        $yrend = $year + $yrDif;
    }

    $yrmthend = sprintf("%04d%02d", $yrend, $mthend);
    if ($log) echo "<br>getyrmth $year,$month,$period-'$yrDif'-'$mthDif'-'$dif'-'$mthend'-'$yrend'";
    return $yrmthend;
}

print '

<script language=javascript>
function validate(chk1,chk2){
  if (chk1.checked == 1 && chk2.checked == 1){
    alert("Tidak boleh tanda kedua-dua bahagian");
    chk1.checked = 0; 
    chk2.checked = 0;
	return false;
  }else if (chk1.checked == 0 && chk2.checked == 0){
    alert("Sila pilih keputusan pembiayaan!");
	return false;  
  }else
    //alert("You didn\'t check it! Let me check it for you.")
    //chk.checked = 1; 
	return true;
}
</script>

';
/*
function validate(chk1,chk2){
  if (chk.checked == 1)
    alert("Thank You");
  else
    alert("You didn\'t check it! Let me check it for you.")
    chk.checked = 1;
}

print '
<input type="button" name="action" value="testttt" class="but" onclick="CheckField()">

<script language="JavaScript">
	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");
	}

	function CheckField() {
	    e = document.MyForm;
		count = 0;
		for(c=0; c<e.elements.length; c++) {
		  //if(!e.debit2.value == \'\') alert(e.nama_anggota.value);

		  if(e.elements[c].name=="lulus" && e.elements[c].checked==true) {
			alert(\'Ruang rujukan perlu diisi!\');
            count++;
		  }

		  if(e.elements[c].name=="tolak" && e.elements[c].value==true) {
			alert(\'Ruang amaun perlu diisi!\');
            count++;
		  }

		}

		if(count==0) {
			e.submit();
		}

	}
</script>
'; */


include("footer.php");
?>