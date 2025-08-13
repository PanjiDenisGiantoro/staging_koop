<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	leftpanel.php
 *********************************************************************************/
$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
$status = dlookup("userdetails", "status", "userID=" . tosql(get_session("Cookie_userID"), "Text"));

if (get_session("Cookie_koperasiID") == $koperasiID) {
    if (get_session("Cookie_groupID") == '1' or get_session("Cookie_groupID") == '2' or get_session("Cookie_groupID") == '3') {
        // Admin/Manager

        if (@$mn == 900) {
            $mn900 = "mm-collapse mm-show";
            $mu900 = "mm-active";
        } else {
            $mn900 = '';
            $mu900 = '';
        }
        echo '<li class="' . $mu900 . '">';
        TitleBarBlue("DASHBOARD", 'mdi mdi-chart-pie');
        echo '<ul class="sub-menu ' . $mn900 . '" aria-expanded="false">';
        MenuLink("dashboard.php", "Dashboard Koperasi", 900, @$_REQUEST['vw']);

        echo '</ul></li>';



        if (get_session("Cookie_groupID") == '2') {
            if (@$mn == 903) {
                $mn903 = "mm-collapse mm-show";
                $mu903 = "mm-active";
            } else {
                $mn903 = '';
                $mu903 = '';
            }
            echo '<li class="' . $mu903 . '">';
            TitleBarBlue("INFORMASI ASAS", 'mdi mdi-book-information-variant');
            echo '<ul class="sub-menu ' . $mn903 . '" aria-expanded="false">';
?>
            <li>

                <form name="MyNetForm" method="post" action="?vw=general&mn=903" style="margin-left: 3.5em;">
                    <select name="selCode" class="button btn-light form-select-sm" onchange="this.form.submit()">
                        <?php
                        for ($i = 0; $i < count($basicList); $i++) {
                            if (@$_REQUEST['selCode'] == $basicVal[$i]) {
                                $sele = "selected";
                            } else {
                                $sele = '';
                            }
                            print '	<option ' . $sele . ' value="' . $basicVal[$i] . '" >' . $basicList[$i];
                        }
                        ?>
                    </select>
                </form>
            </li>
        <?php
            echo '</ul></li>';
        }

        if ((get_session("Cookie_userName") == 'superadmin') or (get_session("Cookie_userName") == 'admin') or (get_session("Cookie_groupID") == '3')) {

            if (@$mn == 904) {
                $mn904 = "mm-collapse mm-show";
                $mu904 = "mm-active";
            } else {
                $mn904 = '';
                $mu904 = '';
            }
            echo '<li class="' . $mu904 . '">';
            TitleBarBlue("INFORMASI AKAUN", 'mdi mdi-chart-areaspline');

            echo '<ul class="sub-menu ' . $mn904 . '" aria-expanded="false">';
        ?>
            <li>

                <form method="post" action="?vw=generalACC&mn=904" style="margin-left: 3.5em;">


                    <select name="selCodeACC" class="button btn-light form-select-sm" onchange="this.form.submit()">
                        <?php
                        for ($i = 0; $i < count($basicListACC); $i++) {
                            if (@$_REQUEST['selCodeACC'] == $basicValACC[$i]) {
                                $seleACC = "selected";
                            } else {
                                $seleACC = '';
                            }

                            print '	<option ' . $seleACC . ' value="' . $basicValACC[$i] . '" >' . $basicListACC[$i];
                        }
                        ?>
                    </select>
                </form>
            </li>
<?php
            echo '</ul></li>';
        }
        if (@$mn == 905) {
            $mn905 = "mm-collapse mm-show";
            $mu905 = "mm-active";
        } else {
            $mn905 = '';
            $mu905 = '';
        }
        echo '<li class="' . $mu905 . '">';
        TitleBarBlue("ANGGOTA", 'mdi mdi-contacts-outline');
        echo '<ul class="sub-menu ' . $mn905 . '" aria-expanded="false">';
        MenuLink("member.php", "Daftar Pengajuan", 905, @$_REQUEST['vw']);
        MenuLink("memberProfil.php", "Profil Anggota", 905, @$_REQUEST['vw']);
        MenuLink("memberT.php", "Senarai Berhenti", 905, @$_REQUEST['vw']);
        MenuLink("carumanList.php", "Senarai Pengeluaran Caruman", 905, @$_REQUEST['vw']);
        MenuLink("memberStmtA.php", "Penyata", 905, @$_REQUEST['vw']);
        echo '<li>';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
        MenuLink("rAllFeesShare.php", "Keseluruhan Yuran & Syer", 905, @$_REQUEST['vw']);
        echo '</ul></li>';
        echo '</ul></li>';

        if (@$mn == 921) {
            $mn921 = "mm-collapse mm-show";
            $mu921 = "mm-active";
        } else {
            $mn921 = '';
            $mu921 = '';
        }
        echo '<li class="' . $mu921 . '">';
        TitleBarBlue("KEBAJIKAN", 'mdi mdi-charity');
        echo '<ul class="sub-menu ' . $mn921 . '" aria-expanded="false">';
        MenuLink("welfare.php", "Daftar Pengajuan", 921, @$_REQUEST['vw']);
        MenuLink("welfareTableS.php", "Permohonan Selesai", 921, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 906) {
            $mn906 = "mm-collapse mm-show";
            $mu906 = "mm-active";
        } else {
            $mn906 = '';
            $mu906 = '';
        }
        echo '<li class="' . $mu906 . '">';
        TitleBarBlue("PEMBIAYAAN", 'mdi mdi-book-clock-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="false">';
        MenuLink("loan.php", "Daftar Pengajuan", 906, @$_REQUEST['vw']);
        MenuLink("penjamin.php", "Senarai Penjamin", 906, @$_REQUEST['vw']);
        MenuLink("loanTable.php", "Diluluskan", 906, @$_REQUEST['vw']);
        MenuLink("loanTableS.php", "Selesai", 906, @$_REQUEST['vw']);
        MenuLink("memberStmtP.php", "Penyata", 906, @$_REQUEST['vw']);
        echo '<li>';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
        MenuLink("rloanApproved.php", "Kelulusan Pembiayaan", 906, @$_REQUEST['vw']);
        MenuLink("rptAgingLoan.php", "Aging", 906, @$_REQUEST['vw']);
        echo '</ul></li>';
        echo '</ul></li>';

        // if (@$mn == 922) {
        // 	$mn922 = "mm-collapse mm-show";
        // 	$mu922 = "mm-active";
        // } else {
        // 	$mn922 = '';
        // 	$mu922 = '';
        // }
        // echo '<li class="' . $mu922 . '">';
        // TitleBarBlue("HIRE PURCHASE", 'mdi mdi-book-clock-outline');
        // echo '<ul class="sub-menu ' . $mn922 . '" aria-expanded="false">';
        // MenuLink("hpApply.php", "Mohon Baru", 922, @$_REQUEST['vw']);
        // MenuLink("hirePurchase.php", "Daftar Pengajuan", 922, @$_REQUEST['vw']);
        // MenuLink("hpTable.php", "Diluluskan", 922, @$_REQUEST['vw']);
        // MenuLink("hpActive.php", "Hire Purchase Aktif", 922, @$_REQUEST['vw']);
        // MenuLink("hpNPL.php", "Senarai NPL", 922, @$_REQUEST['vw']);
        // MenuLink("hpFS.php", "Full Settlements", 922, @$_REQUEST['vw']);
        // MenuLink("hpTableS.php", "Selesai", 922, @$_REQUEST['vw']);
        // // MenuLink("loanTablePawal.php", "Penjelasan Awal",922, @$_REQUEST['vw']);
        // echo '</ul></li>';

        //------------------------------------------Advance Payment Module-----------------------------------------------------
        if (@$mn == 923) {
            $mn923 = "mm-collapse mm-show";
            $mu923 = "mm-active";
        } else {
            $mn923 = '';
            $mu923 = '';
        }
        echo '<li class="' . $mu923 . '">';
        TitleBarBlue("ADVANCE PAYMENT", 'fas fa-sort-amount-up');
        echo '<ul class="sub-menu ' . $mn923 . '" aria-expanded="false">';
        MenuLink("AdvanSenarai.php", "senarai Permohonan", 923, @$_REQUEST['vw']);
        MenuLink("AdvanAdLulus.php", "Senarai Diluluskan", 923, @$_REQUEST['vw']);
        MenuLink("AdvanListSelesai.php", "Senarai Selesai", 923, @$_REQUEST['vw']);
        MenuLink("reportAP.php", "Laporan", 923, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 907) {
            $mn907 = "mm-collapse mm-show";
            $mu907 = "mm-active";
        } else {
            $mn907 = '';
            $mu907 = '';
        }
        echo '<li class="' . $mu907 . '">';
        TitleBarBlue("KOMODITI", 'mdi mdi-shape-plus');
        echo '<ul class="sub-menu ' . $mn907 . '" aria-expanded="false">';
        MenuLink("komoditi_add.php", "Permohonan Sijil Komoditi", 907, @$_REQUEST['vw']);
        MenuLink("komoditi_list.php", "Senarai Sijil Komoditi", 907, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 908) {
            $mn908 = "mm-collapse mm-show";
            $mu908 = "mm-active";
        } else {
            $mn908 = '';
            $mu908 = '';
        }
        echo '<li class="' . $mu908 . '">';
        TitleBarBlue("URUSNIAGA ANGGOTA", 'mdi mdi-shopping-search');
        echo '<ul class="sub-menu ' . $mn908 . '" aria-expanded="false">';
        MenuLink("resitList.php", "Resit Keanggotaan", 908, @$_REQUEST['vw']);
        MenuLink("vouchersList.php", "Baucer Keanggotaan", 908, @$_REQUEST['vw']);
        MenuLink("journalsTransferList.php", "Jurnal Pindahan", 908, @$_REQUEST['vw']);
        MenuLink("paymentsList.php", "Auto Pay", 908, @$_REQUEST['vw']);
        MenuLink("importPot.php", "Import Fail Potongan", 908, @$_REQUEST['vw']);
        MenuLink("memberStmtEdit.php", "Edit Import Fail", 908, @$_REQUEST['vw']);
        MenuLink("memberStmtU.php", "Penyata", 908, @$_REQUEST['vw']);
        echo '</ul></li>';

        //.................
        if (@$mn == 911) {
            $mn911 = "mm-collapse mm-show";
            $mu911 = "mm-active";
        } else {
            $mn911 = '';
            $mu911 = '';
        }
        echo '<li class="' . $mu911 . '">';
        TitleBarBlue("POTONGAN GAJI", 'mdi mdi-account-multiple-minus-outline');
        echo '<ul class="sub-menu ' . $mn911 . '" aria-expanded="false">';
        MenuLink("memberPotonganALL.php", "Senarai Potongan Bulanan", 911, @$_REQUEST['vw']);
        echo '<li>';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
        MenuLink("reportsPGB.php", "Laporan Umum", 911, @$_REQUEST['vw']);
        MenuLink("reportsPGBbaki.php", "Laporan Baki", 911, @$_REQUEST['vw']);
        echo '</ul></li>';
        echo '</ul></li>';


        /// HUTANG LAPUK.........................
        if (@$mn == 910) {
            $mn910 = "mm-collapse mm-show";
            $mu910 = "mm-active";
        } else {
            $mn910 = '';
            $mu910 = '';
        }
        echo '<li class="' . $mu910 . '">';
        TitleBarBlue("HUTANG LAPUK", 'mdi mdi-bag-personal-off-outline');
        echo '<ul class="sub-menu ' . $mn910 . '" aria-expanded="false">';
        MenuLink("memberProfilHL.php", "Profile Anggota", 910, @$_REQUEST['vw']);
        MenuLink("loanTableHL.php", "Pengurusan Pembiayaan", 910, @$_REQUEST['vw']);
        MenuLink("resitListHL.php", "Resit", 910, @$_REQUEST['vw']);
        MenuLink("memberStmtHL.php", "Penyata", 910, @$_REQUEST['vw']);
        echo '<li>';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn910 . '" aria-expanded="true">';
        MenuLink("rpthutanglapuk.php", "Hutang Lapuk < 12 Bulan", 910, @$_REQUEST['vw']);
        MenuLink("rpthutanglapuk1.php", "Hutang Lapuk > 12 Bulan", 910, @$_REQUEST['vw']);
        echo '</ul></li>';
        echo '</ul></li>';

        if (@$mn == 909) {
            $mn909 = "mm-collapse mm-show";
            $mu909 = "mm-active";
        } else {
            $mn909 = '';
            $mu909 = '';
        }
        echo '<li class="' . $mu909 . '">';
        TitleBarBlue("KIRAAN DIVIDEN", 'mdi mdi-sprout-outline');
        echo '<ul class="sub-menu ' . $mn909 . '" aria-expanded="false">';
        MenuLink("dividenPeratusBlnKhd.php", "Kiraan Dividen", 909, @$_REQUEST['vw']);
        MenuLink("dividenList.php", "Senarai Dividen", 909, @$_REQUEST['vw']);
        MenuLink("reportsDIV.php", "Laporan Dividen", 909, @$_REQUEST['vw']);
        echo '</ul></li>';


        if (@$mn == 913) {
            $mn913 = "mm-collapse mm-show";
            $mu913 = "mm-active";
        } else {
            $mn913 = '';
            $mu913 = '';
        }
        echo '<li class="' . $mu913 . '">';
        TitleBarBlue("LEJER UTAMA", 'mdi mdi-archive-outline');
        echo '<ul class="sub-menu ' . $mn913 . '" aria-expanded="false">';
        MenuLink("ACClejerList.php", "Pembuka Akaun", 913, @$_REQUEST['vw']);
        MenuLink("ACCSingleEntryList.php", "Jurnal Entry", 913, @$_REQUEST['vw']);
        MenuLink("ACCGeneralejer.php", "General Lejer", 913, @$_REQUEST['vw']);
        if (@$mn == '918a') {
            $mn918a = "mm-collapse mm-show";
            $mu918a = "mm-active";
        } else {
            $mn918a = '';
            $mu918a = '';
        }
        echo '<li class="' . $mu918a . '">';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $isLaporanAkaunActive . '" aria-expanded="true">';
        MenuLink("rTrialBal.php", "Imbangan Duga (Trial Balance)", '918a', @$_REQUEST['vw']);
        MenuLink("rTrialBalDetail.php", "Imbangan Duga Terperinci (Trial Balance Detailed)", '918a', @$_REQUEST['vw']);
        MenuLink("rProfitLoss.php", "Profit Loss", '918a', @$_REQUEST['vw']);
        MenuLink("rBalanceSheet.php", "Balance Sheet", '918a', @$_REQUEST['vw']);
        MenuLink("rLejer.php", "Penyata Lejer Am", '918a', @$_REQUEST['vw']);
        MenuLink("rLejerCarta.php", "Penyata Ledger Mengikut Carta Akaun", '918a', @$_REQUEST['vw']);
        echo '</ul></li>';
        echo '</ul></li>';

        if (@$mn == 914) {
            $mn914 = "mm-collapse mm-show";
            $mu914 = "mm-active";
        } else {
            $mn914 = '';
            $mu914 = '';
        }
        echo '<li class="' . $mu914 . '">';
        TitleBarBlue("BUKU TUNAI", 'mdi mdi-badge-account-horizontal-outline');
        echo '<ul class="sub-menu ' . $mn914 . '" aria-expanded="false">';
        MenuLink("ACCvouchersList.php", "Pembayaran (Baucer)", 914, @$_REQUEST['vw']);
        MenuLink("ACCresitList.php", "Penerimaan (Resit)", 914, @$_REQUEST['vw']);
        MenuLink("journalsList.php", "Baucer Jurnal (Anggota)", 914, @$_REQUEST['vw']);
        MenuLink("ACCbankrecon.php", "Bank Rekonsilasi", 914, @$_REQUEST['vw']);
        echo '</ul></li>';


        if (@$mn == 921) {
            $mn921 = "mm-collapse mm-show";
            $mu921 = "mm-active";
        } else {
            $mn921 = '';
            $mu921 = '';
        }
        echo '<li class="' . $mu921 . '">';
        TitleBarBlue("LHDN e-invoice", 'mdi mdi-badge-account-horizontal-outline');
        echo '<ul class="sub-menu ' . $mn921 . '" aria-expanded="false">';
        MenuLink("ACCselfBillList.php", "Self-Billed Document", 921, @$_REQUEST['vw']);
        MenuLink("ACCconsolidateList.php", "Bulk e-invoice", 921, @$_REQUEST['vw']);
        echo '</ul></li>';


        if (@$mn == 915) {
            $mn915 = "mm-collapse mm-show";
            $mu915 = "mm-active";
        } else {
            $mn915 = '';
            $mu915 = '';
        }
        echo '<li class="' . $mu915 . '">';
        TitleBarBlue("PENGHUTANG", 'mdi mdi-account-cash');
        echo '<ul class="sub-menu ' . $mn915 . '" aria-expanded="false">';

        MenuLink("ACCquotationList.php", "Sebut Harga", 915, @$_REQUEST['vw']);
        MenuLink("ACCinvoiceList.php", "Invois", 915, @$_REQUEST['vw']);
        MenuLink("ACCDebtorList.php", "Terima Bayaran (Invois)", 915, @$_REQUEST['vw']);
        MenuLink("ACCDebtorBulkList.php", "Terima Bayaran Bulk", 915, @$_REQUEST['vw']);
        MenuLink("ACCcreditNoteList.php", "Nota Kredit", 915, @$_REQUEST['vw']);
        MenuLink("reportDebtor.php", "Laporan Penghutang", 915, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 916) {
            $mn916 = "mm-collapse mm-show";
            $mu916 = "mm-active";
        } else {
            $mn916 = '';
            $mu916 = '';
        }
        echo '<li class="' . $mu916 . '">';
        TitleBarBlue("PEMIUTANG", 'mdi mdi-account-details');
        echo '<ul class="sub-menu ' . $mn916 . '" aria-expanded="false">';
        MenuLink("ACCpurchaseList.php", "Purchase Order", 916, @$_REQUEST['vw']);
        MenuLink("ACCpurchaseInvoiceList.php", "Purchase Invois", 916, @$_REQUEST['vw']);
        MenuLink("ACCbillList.php", "Bayaran Bil", 916, @$_REQUEST['vw']);
        MenuLink("ACCdebitNoteList.php", "Nota Debit", 916, @$_REQUEST['vw']);
        MenuLink("reportCreditor.php", "Laporan Pemiutang", 916, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 922) {
            $mn922 = "mm-collapse mm-show";
            $mu922 = "mm-active";
        } else {
            $mn922 = '';
            $mu922 = '';
        }
        echo '<li class="' . $mu922 . '">';
        TitleBarBlue("STOK", 'mdi mdi-account-details');
        echo '<ul class="sub-menu ' . $mn922 . '" aria-expanded="false">';
		MenuLink("productServiceList.php", "Senarai Produk/Servis",922, @$_REQUEST['vw']);
        MenuLink("stockAdjustmentList.php", "Pelarasan Stok", 922, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 920) {
            $mn920 = "mm-collapse mm-show";
            $mu920 = "mm-active";
        } else {
            $mn920 = '';
            $mu920 = '';
        }
        echo '<li class="' . $mu920 . '">';
        TitleBarBlue("PELABURAN", 'ti ti-money');
        echo '<ul class="sub-menu ' . $mn920 . '" aria-expanded="false">';
        MenuLink("ACCinvestors.php", "Projek Pelaburan", 920, @$_REQUEST['vw']);
        MenuLink("ACCinvestList.php", "Invois Pelaburan", 920, @$_REQUEST['vw']);
        MenuLink("ACCvouchersProjectsList.php", "Baucer (Pembayaran)", 920, @$_REQUEST['vw']);
        MenuLink("ACCInvDebtorList.php", "Resit (Penerimaan)", 920, @$_REQUEST['vw']);
        MenuLink("investorReports.php", "Laporan Pelaburan", 920, @$_REQUEST['vw']);
        echo '</ul></li>';

                if(@$mn==912){$mn912="mm-collapse mm-show";$mu912="mm-active";} else {$mn912='';$mu912='';}
                                    echo '<li class="'.$mu912.'">';
        TitleBarBlue("Takaful",'mdi mdi-ballot-outline');
                echo '<ul class="sub-menu '.$mn912.'" aria-expanded="false">';
        MenuLink("insuranApply.php", "Permohonan Takaful",912, @$_REQUEST['vw']);
        MenuLink("insuranListNewReg.php", "Pengesahan Takaful",912, @$_REQUEST['vw']);
        MenuLink("insuranList.php", "Senarai Takaful",912, @$_REQUEST['vw']);
        MenuLink("insuranListNotActive.php", "Senarai Tamat Takaful",912, @$_REQUEST['vw']);
        MenuLink("insuranListJualan.php", "Laporan Takaful",912, @$_REQUEST['vw']);
        echo '</ul></li>';

        


        if (@$mn == 917) {
            $mn917 = "mm-collapse mm-show";
            $mu917 = "mm-active";
        } else {
            $mn917 = '';
            $mu917 = '';
        }
        echo '<li class="' . $mu917 . '">';
        TitleBarBlue("SURAT & EMEL", 'mdi mdi-ballot-recount-outline');
        echo '<ul class="sub-menu ' . $mn917 . '" aria-expanded="false">';
        MenuLink("memberLetter.php?page=add&group=&code=", "Tambah Kandungan", 917, @$_REQUEST['vw']);
        MenuLink("memberLetter.php", "Senarai Surat/Emel", 917, @$_REQUEST['vw']);
        MenuLink("minit.php", "Senarai Minit Mesyuarat", 917, @$_REQUEST['vw']);
        MenuLink("agm.php", "Senarai Dokumen AGM", 917, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (@$mn == 918) {
            $mn918 = "mm-collapse mm-show";
            $mu918 = "mm-active";
        } else {
            $mn918 = '';
            $mu918 = '';
        }
        echo '<li class="' . $mu918 . '">';
        TitleBarBlue("LAPORAN", 'mdi mdi-clipboard-text-multiple-outline');
        echo '<ul class="sub-menu ' . $mn918 . '" aria-expanded="false">';
        MenuLink("reports.php?cat=A", "Laporan Anggota", 918, @$_REQUEST['vw']);
        MenuLink("reports.php?cat=B", "Laporan Pembiayaan", 918, @$_REQUEST['vw']);
        MenuLink("reports.php?cat=D", "Laporan Kewangan", 918, @$_REQUEST['vw']);
        echo '</ul></li>';

        ///////////////////////////////////// HR MODULE (ADMIN) //////////////////////////////////////////
        if (@$mn == 933) {
            $mn933 = "mm-collapse mm-show";
            $mu933 = "mm-active";
        } else {
            $mn933 = '';
            $mu933 = '';
        }
        echo '<li class="' . $mu933 . '">';
        TitleBarBlue("Sumber Manusia", 'mdi mdi-calculator-variant-outline');
        echo '<ul class="sub-menu ' . $mn933 . '" aria-expanded="false">';
        MenuLink("staff.php", "Senarai Staf Koperasi", 933, @$_REQUEST['vw']);
        MenuLink("leave.php", "Pelepasan Cuti", 933, @$_REQUEST['vw']);
        echo '</ul></li>';



        if (@$mn == 902) {
            $mn902 = "mm-collapse mm-show";
            $mu902 = "mm-active";
        } else {
            $mn902 = '';
            $mu902 = '';
        }
        echo '<li class="' . $mu902 . '">';
        TitleBarBlue("PANDUAN PENGGUNA", 'mdi mdi-account-tie-outline');
        echo '<ul class="sub-menu ' . $mn902 . '" aria-expanded="false">';
        MenuLink("manual.php?type=member", "Keanggotaan", 902, @$_REQUEST['vw']);
        MenuLink("manual.php?type=loan", "Pembiayaan", 902, @$_REQUEST['vw']);
        MenuLink("manual.php?type=akaun", "Akaun", 902, @$_REQUEST['vw']);
        if ((get_session("Cookie_groupID") == '1') || (get_session("Cookie_groupID") == '2')) {
            MenuLink("manual.php?type=dividen", "Dividen", 902, @$_REQUEST['vw']);
        }
        if ((get_session("Cookie_groupID") == '1') || (get_session("Cookie_groupID") == '2')) {
            MenuLink("manual.php?type=potong", "Potongan Gaji", 902, @$_REQUEST['vw']);
        }
        if ((get_session("Cookie_groupID") == '1') || (get_session("Cookie_groupID") == '2')) {
            MenuLink("manual.php?type=insuran", "Takaful", 902, @$_REQUEST['vw']);
        }
        if ((get_session("Cookie_groupID") == '1') || (get_session("Cookie_groupID") == '2')) {
            MenuLink("manual.php?type=Kebajikan", "Kebajikan", 902, @$_REQUEST['vw']);
        }
        if (get_session("Cookie_groupID") == '1')
            echo '</ul></li>';
        if (get_session("Cookie_groupID") == '2') {
            MenuLink("manual.php?type=admin", "Pengurusan", 902, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        $strModul = 'MODUL ' . strtoupper(get_session("Cookie_groupName"));
        if ((get_session("Cookie_userName") == 'superadmin') or (get_session("Cookie_userName") == 'admin')) {
            $strModul = '';
            $strModul = 'TETAPAN ADMIN';
        }
        if (@$mn == 901) {
            $mn901 = "mm-collapse mm-show";
            $mu901 = "mm-active";
        } else {
            $mn901 = '';
            $mu901 = '';
        }
        echo '<li class="' . $mu901 . '">';
        TitleBarBlue($strModul, 'mdi mdi-shield-home-outline');
        echo '<ul class="sub-menu ' . $mn901 . '" aria-expanded="false">';
        MenuLink("mainpage.php", "Laman Utama", 901, @$_REQUEST['vw']);
        MenuLink("mainpage.php?page=add&id=0", "Buletin", 901, @$_REQUEST['vw']);

        if (get_session("Cookie_groupID") == '2') {
            if ((get_session("Cookie_userName") == 'superadmin') or (get_session("Cookie_userName") == 'admin')) {
                MenuLink("admin.php", "Senarai Kakitangan", 901, @$_REQUEST['vw']);
            }
        }
        MenuLink("syarat_kelayakan.php?screen=view&id=999", "Kelayakan Anggota ", 901, @$_REQUEST['vw']);
        MenuLink("caraBayar.php?screen=view&id=998", "Pembayaran Anggota ", 901, @$_REQUEST['vw']);
        if (get_session("Cookie_groupID") == '2') {
            if ((get_session("Cookie_userName") == 'superadmin') or (get_session("Cookie_userName") == 'admin')) {
                MenuLink("settingcoop.php", "Tetapan Koperasi", 901, @$_REQUEST['vw']);
            }
        }
        MenuLink("profile.php", "Tukar Katalaluan", 901, @$_REQUEST['vw']);
        MenuLink("aktivitiLog.php", "Log Aktiviti", 901, @$_REQUEST['vw']);
        echo '</ul></li>';


        ////////////////////////////// ANNUAL GENERAL MEETING////////////////////////////////
        // 		if(@$mn==920){$mn920="mm-collapse mm-show";$mu920="mm-active";} else {$mn920='';$mu920='';}
        //                             echo '<li class="'.$mu920.'">';
        // TitleBarBlue("ANNUAL GENERAL MEETING",'mdi mdi-vote-outline');
        //         echo '<ul class="sub-menu '.$mn920.'" aria-expanded="false">';
        // MenuLink("calon.php", "Senarai Calon",920, @$_REQUEST['vw']);
        // MenuLink("electionResult.php", "Keputusan Undian",920, @$_REQUEST['vw']);
        //         echo '</ul></li>';

    } else if (get_session("Cookie_groupID") == '0') {

        $berhenti = 0;
        $sqlterm = "SELECT * FROM userdetails WHERE STATUS = 3 and userID = '" . get_session("Cookie_userID") . "'";
        $rs = &$conn->Execute($sqlterm);
        if ($rs->RowCount() <> 0) {
            $berhenti = 1;
        }

        $strModul = 'PROFIL PENGGUNA';
        if (@$mn == 4) {
            $mn4 = "mm-collapse mm-show";
            $mu4 = "mm-active";
        } else {
            $mn4 = '';
            $mu4 = '';
        }
        echo '<li class="' . $mu4 . '">';
        TitleBarBlue($strModul, 'mdi mdi-account-reactivate-outline');
        echo '<ul class="sub-menu ' . $mn4 . '" aria-expanded="false">';
        MenuLink("main", "Laman Utama", 4, @$_REQUEST['vw']);
        if (!$berhenti) {
            if ($status != 0) {
                MenuLink("profile.php", "Tukar Katalaluan", 4, @$_REQUEST['vw']);
                MenuLink("pin.php", "PIN Keselamatan", 4, @$_REQUEST['vw']);
            }
            MenuLink("memberUpdate.php", "Profil", 4, @$_REQUEST['vw']);
        }

        MenuLink("manual.php", "Panduan Anggota", 4, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (!$berhenti && $status != 0) {
            if (@$mn == 1) {
                $mn1 = "mm-collapse mm-show";
                $mu1 = "mm-active";
            } else {
                $mn1 = '';
                $mu1 = '';
            }
            echo '<li class="' . $mu1 . '">';
            TitleBarBlue("ANGGOTA", 'mdi mdi-account-box');
            echo '<ul class="sub-menu ' . $mn1 . '" aria-expanded="false">';

            MenuLink("memberSahAnggota.php", "Saksi Keanggotaan", 1, @$_REQUEST['vw']);
            MenuLink("memberApplyT.php", "Mohon Berhenti", 1, @$_REQUEST['vw']);
            MenuLink("memberStatusT.php", "Status Berhenti", 1, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        //simpanan khas
        if (!$berhenti && $status != 0) {
            if (@$mn == 7) {
                $mn7 = "mm-collapse mm-show";
                $mu7 = "mm-active";
            } else {
                $mn7 = '';
                $mu7 = '';
            }
            echo '<li class="' . $mu7 . '">';
            TitleBarBlue("SIMPANAN", 'mdi mdi-bank');
            echo '<ul class="sub-menu ' . $mn7 . '" aria-expanded="false">';
            MenuLink("listSimpanan.php", "Senarai", 7, @$_REQUEST['vw']);
            MenuLink("carumanApply.php", "Mohon Pengeluaran", 7, @$_REQUEST['vw']);
            MenuLink("carumanStatus.php", "Status Pengeluaran", 7, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        if (@$mn == 3) {
            $mn3 = "mm-collapse mm-show";
            $mu3 = "mm-active";
        } else {
            $mn3 = '';
            $mu3 = '';
        }
        echo '<li class="' . $mu3 . '">';
        TitleBarBlue("PEMBIAYAAN", 'mdi mdi-alarm-panel');
        echo '<ul class="sub-menu ' . $mn3 . '" aria-expanded="false">';
        if (!$berhenti) {
            if ($status != 0) {
                MenuLink("biayaEdit.php", "Info Gaji", 3, @$_REQUEST['vw']);
            }
            MenuLink("loanApply2.php", "Mohon Baru", 3, @$_REQUEST['vw']);
        }
        // MenuLink("loanView.php", "Senarai Pembiayaan");
        MenuLink("loanInProcess.php", "Dalam Proses", 3, @$_REQUEST['vw']);
        MenuLink("loanApproved.php", "Diluluskan", 3, @$_REQUEST['vw']);
        MenuLink("loanOthers.php", "Lain-Lain Status", 3, @$_REQUEST['vw']);
        MenuLink("memberStmtLoan.php", "Penyata", 3, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (!$berhenti && $status != 0) {
            if (@$mn == 5) {
                $mn5 = "mm-collapse mm-show";
                $mu5 = "mm-active";
            } else {
                $mn5 = '';
                $mu5 = '';
            }
            echo '<li class="' . $mu5 . '">';
            TitleBarBlue("PENJAMIN", 'mdi mdi-buffer');
            echo '<ul class="sub-menu ' . $mn5 . '" aria-expanded="false">';
            MenuLink("biayaMember.php", "Permohonan", 5, @$_REQUEST['vw']);
            MenuLink("biayaSahMember.php", "Pengesahan", 5, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        //---------------------------------------Advance payment------------------------------------------------------------	
        if (!$berhenti && $status != 0) {
            if (@$mn == 12) {
                $mn12 = "mm-collapse mm-show";
                $mu12 = "mm-active";
            } else {
                $mn12 = '';
                $mu12 = '';
            }
            echo '<li class="' . $mu12 . '">';
            TitleBarBlue("Advance Payment", 'mdi mdi-ballot-outline');
            echo '<ul class="sub-menu ' . $mn12 . '" aria-expanded="false">';
            MenuLink("AdvanMohon.php", "Mohon Baru", 12, @$_REQUEST['vw']);
            MenuLink("AdvanInProses.php", "Dalam Proses", 12, @$_REQUEST['vw']); // change file name
            MenuLink("AdvanLulus.php", "Lain-lain Status", 12, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        if (!$berhenti && $status != 0) {
            if (@$mn == 6) {
                $mn6 = "mm-collapse mm-show";
                $mu6 = "mm-active";
            } else {
                $mn6 = '';
                $mu6 = '';
            }
            echo '<li class="' . $mu6 . '">';
            TitleBarBlue("KEBAJIKAN", 'mdi mdi-charity');
            echo '<ul class="sub-menu ' . $mn6 . '" aria-expanded="false">';
            MenuLink("welfareApply.php", "Mohon", 6, @$_REQUEST['vw']);
            MenuLink("welfareInProcess.php", "Dalam Proses", 6, @$_REQUEST['vw']);
            MenuLink("welfareApproved.php", "Diluluskan", 6, @$_REQUEST['vw']);
            MenuLink("welfareOthers.php", "Lain-Lain Status", 6, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        if (!$berhenti) {
            if (@$mn == 9) {
                $mn9 = "mm-collapse mm-show";
                $mu9 = "mm-active";
            } else {
                $mn9 = '';
                $mu9 = '';
            }
            echo '<li class="' . $mu9 . '">';
            TitleBarBlue("PEMBAYARAN", 'mdi mdi-clipboard-check-outline');
            echo '<ul class="sub-menu ' . $mn9 . '" aria-expanded="false">';
            MenuLink("bayaranOnline.php", "Bayaran Atas Talian", 9, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        if (@$mn == 10) {
            $mn10 = "mm-collapse mm-show";
            $mu10 = "mm-active";
        } else {
            $mn10 = '';
            $mu10 = '';
        }
        echo '<li class="' . $mu10 . '">';
        TitleBarBlue("PENYATA ANGGOTA", 'mdi mdi-book-open-outline');
        echo '<ul class="sub-menu ' . $mn10 . '" aria-expanded="false">';
        MenuLink("memberStmtN.php", "Senarai Penyata", 10, @$_REQUEST['vw']);
        echo '</ul></li>';
    } ///////////  Kumpulan Pengurusan: Tiada  /////////////////////////
    else if (get_session("Cookie_groupID") == '99') {

        $berhenti = 0;
        $sqlterm = "SELECT * FROM userdetails WHERE STATUS = 3 and userID = '" . get_session("Cookie_userID") . "'";
        $rs = &$conn->Execute($sqlterm);
        if ($rs->RowCount() <> 0) {
            $berhenti = 1;
        }

        $strModul = 'PROFIL PENGGUNA';
        if (@$mn == 4) {
            $mn4 = "mm-collapse mm-show";
            $mu4 = "mm-active";
        } else {
            $mn4 = '';
            $mu4 = '';
        }
        echo '<li class="' . $mu4 . '">';
        TitleBarBlue($strModul, 'mdi mdi-account-reactivate-outline');
        echo '<ul class="sub-menu ' . $mn4 . '" aria-expanded="false">';
        //MenuLink("mainpage.php", "Laman Utama/Buletin",4, @$_REQUEST['vw']);
        MenuLink("main", "Laman Utama/Buletin", 4, @$_REQUEST['vw']);
        if (!$berhenti) {

            MenuLink("profile.php", "Tukar Katalaluan", 4, @$_REQUEST['vw']);

            MenuLink("memberUpdate.php", "Kemaskini Profil", 4, @$_REQUEST['vw']);
        }

        MenuLink("manual.php", "Manual Bantuan", 4, @$_REQUEST['vw']);
        echo '</ul></li>';

        if (!$berhenti && $status != 0) {
            if (@$mn == 1) {
                $mn1 = "mm-collapse mm-show";
                $mu1 = "mm-active";
            } else {
                $mn1 = '';
                $mu1 = '';
            }
            echo '<li class="' . $mu1 . '">';
            TitleBarBlue("ANGGOTA", 'mdi mdi-account-box');
            echo '<ul class="sub-menu ' . $mn1 . '" aria-expanded="false">';

            // MenuLink("memberPenama.php", "Tukar Penama",1, @$_REQUEST['vw']);
            // MenuLink("memberSahBank.php", "Pengesahan Pengeluaran Dividen",1, @$_REQUEST['vw']);
            MenuLink("memberSahAnggota.php", "Saksi Keanggotaan", 1, @$_REQUEST['vw']);
            MenuLink("carumanApply.php", "Mohon Pengeluaran Caruman", 1, @$_REQUEST['vw']);
            MenuLink("carumanStatus.php", "Status Pengeluaran Caruman", 1, @$_REQUEST['vw']);
            MenuLink("memberApplyT.php", "Mohon Berhenti", 1, @$_REQUEST['vw']);
            MenuLink("memberStatusT.php", "Status Berhenti", 1, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        // 		if(@$mn==3){$mn3="mm-collapse mm-show";$mu3="mm-active";} else {$mn3='';$mu3='';}
        // 		echo '<li class="'.$mu3.'">';
        // TitleBarBlue("PEMBIAYAAN",'mdi mdi-alarm-panel');
        // 		echo '<ul class="sub-menu '.$mn3.'" aria-expanded="false">';
        // 		if (!$berhenti) {
        // 		if ($status != 0) {
        // MenuLink("biayaEdit.php", "Info Gaji",3, @$_REQUEST['vw']);			
        // 		}			
        // MenuLink("loanApply.php", "Mohon Baru",3, @$_REQUEST['vw']);	
        // 		}	
        // // MenuLink("loanView.php", "Senarai Pembiayaan");
        // MenuLink("loanInProcess.php", "Dalam Proses",3, @$_REQUEST['vw']);
        // MenuLink("loanApproved.php", "Diluluskan",3, @$_REQUEST['vw']);
        // MenuLink("loanOthers.php", "Lain-Lain Status",3, @$_REQUEST['vw']);

        // 		echo '</ul></li>';			

        // echo '</ul></li>';

        //---------------------------------------Advance payment------------------------------------------------------------	
        if (!$berhenti && $status != 0) {
            if (@$mn == 12) {
                $mn12 = "mm-collapse mm-show";
                $mu12 = "mm-active";
            } else {
                $mn12 = '';
                $mu12 = '';
            }
            echo '<li class="' . $mu12 . '">';
            TitleBarBlue("Advance Payment", 'mdi mdi-ballot-outline');
            echo '<ul class="sub-menu ' . $mn12 . '" aria-expanded="false">';
            MenuLink("AdvanMohon.php", "Mohon Baru", 12, @$_REQUEST['vw']);
            MenuLink("AdvanInProses.php", "Dalam Proses", 12, @$_REQUEST['vw']); // change file name
            MenuLink("AdvanLulus.php", "Lain-lain Status", 12, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        if (!$berhenti && $status != 0) {
            if (@$mn == 5) {
                $mn5 = "mm-collapse mm-show";
                $mu5 = "mm-active";
            } else {
                $mn5 = '';
                $mu5 = '';
            }
            echo '<li class="' . $mu5 . '">';
            TitleBarBlue("PENJAMIN", 'mdi mdi-buffer');
            echo '<ul class="sub-menu ' . $mn5 . '" aria-expanded="false">';
            MenuLink("biayaMember.php", "Permohonan", 5, @$_REQUEST['vw']);
            MenuLink("biayaSahMember.php", "Pengesahan", 5, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        if (!$berhenti && $status != 0) {
            if (@$mn == 6) {
                $mn6 = "mm-collapse mm-show";
                $mu6 = "mm-active";
            } else {
                $mn6 = '';
                $mu6 = '';
            }
            echo '<li class="' . $mu6 . '">';
            TitleBarBlue("KEBAJIKAN", 'mdi mdi-charity');
            echo '<ul class="sub-menu ' . $mn6 . '" aria-expanded="false">';
            MenuLink("welfareApply.php", "Mohon", 6, @$_REQUEST['vw']);
            MenuLink("welfareInProcess.php", "Dalam Proses", 6, @$_REQUEST['vw']);
            MenuLink("welfareApproved.php", "Diluluskan", 6, @$_REQUEST['vw']);
            MenuLink("welfareOthers.php", "Lain-Lain Status", 6, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        // 		if (!$berhenti) {
        // 		if(@$mn==9){$mn9="mm-collapse mm-show";$mu9="mm-active";} else {$mn9='';$mu9='';}
        // 		echo '<li class="'.$mu9.'">';
        // TitleBarBlue("PEMBAYARAN",'mdi mdi-clipboard-check-outline');
        // 		echo '<ul class="sub-menu '.$mn9.'" aria-expanded="false">';
        // MenuLink("bayaranOnline.php", "Bayaran Atas Talian",9, @$_REQUEST['vw']);
        // 		echo '</ul></li>';
        // 		}

        if (@$mn == 10) {
            $mn10 = "mm-collapse mm-show";
            $mu10 = "mm-active";
        } else {
            $mn10 = '';
            $mu10 = '';
        }
        echo '<li class="' . $mu10 . '">';
        // TitleBarBlue("PENYATA ANGGOTA",'mdi mdi-book-open-outline');
        // 		echo '<ul class="sub-menu '.$mn10.'" aria-expanded="false">';
        // MenuLink("memberStmtN.php", "Senarai Penyata",10, @$_REQUEST['vw']);
        // 		echo '</ul></li>';

        if ($status != 0) {
            if (@$mn == 11) {
                $mn10 = "mm-collapse mm-show";
                $mu10 = "mm-active";
            } else {
                $mn10 = '';
                $mu10 = '';
            }
            echo '<li class="' . $mu10 . '">';
            TitleBarBlue("DIVIDEN", 'dripicons dripicons-graph-pie');
            echo '<ul class="sub-menu ' . $mn10 . '" aria-expanded="false">';
            MenuLink("reportsDIVuser.php", "Senarai Dividen", 11, @$_REQUEST['vw']);
            echo '</ul></li>';
        }

        ///////////////////////////////////// HR MODULE (STAFF) //////////////////////////////////////////
        $strModul = 'Sumber Manusia';
        if (@$mn == 7) {
            $mn7 = "mm-collapse mm-show";
            $mu7 = "mm-active";
        } else {
            $mn7 = '';
            $mu7 = '';
        }
        echo '<li class="' . $mu7 . '">';
        TitleBarBlue($strModul, 'mdi mdi-account-reactivate-outline');
        echo '<ul class="sub-menu ' . $mn7 . '" aria-expanded="false">';
        //MenuLink("mainpage.php", "Laman Utama/Buletin",7, @$_REQUEST['vw']);
        MenuLink("profileHR.php", "Profil", 7, @$_REQUEST['vw']);
        MenuLink("leaveApply.php", "Pemohonan Cuti", 7, @$_REQUEST['vw']);
        MenuLink("leaveDetail.php", "Status Cuti", 7, @$_REQUEST['vw']);
        MenuLink("staffIncome.php", "Maklumat Gaji", 7, @$_REQUEST['vw']);
    } else {
        $strModul = 'PELAWAT';
        $mn11 = "mm-collapse mm-show";
        echo '<li class="' . $mu11 . '">';
        TitleBarBlue($strModul . 'mdi mdi-badge-account-outline');
        echo '<ul class="sub-menu ' . $mn11 . '" aria-expanded="false">';
        MenuLink("mainpage.php", "Login", 11, @$_REQUEST['vw']);
        MenuLink("checkIC.php", "Daftar/Semakan", 11, @$_REQUEST['vw']);
        echo '</ul></li>';
    }
} ?>

<?php

function TitleBarBlue($strTitle, $class = 'mdi mdi-email')
{

    echo '<a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="' . $class . '"></i>
                                    <span>' . ucwords(strtolower($strTitle)) . '</span>
                                </a>';
    /*
	$strImgLink1 = "images/shade-bkrm-03.gif";
	print
	'<tr>'.'<td>'
	.'<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#008080">'
	.'<tr>'
	.'<td width="14%">'
	.'&nbsp;<!--img src="'.$strImgLink2.'" width="28" height="24"-->'
	.'</td>'
	.'<td width="86%" valign="middle">'
	.'<div class="headerteal" style="width:160px;">'.strtoupper($strTitle).'</div>'
	.'</td>'
	.'</tr>'
	.'</table>'
	.'</td>'
	.'</tr>';
     * 
     */
}
function TitleBarOrange($strTitle)
{
    $strImgLink1 = "images/shade-bkrm-04.gif";
    $strImgLink2 = "images/shade-logo-bkrm-04.gif";
    print
        '<table width="100%" cellspacing="0" cellpadding="0">'
        . '<tr>'
        . '<td width="14%">'
        . '<img src="' . $strImgLink2 . '" width="28" height="24">'
        . '</td>'
        . '<td width="86%">'
        . '<div class="headerorange" style="width:160px;">' . $strTitle . '</div>'
        . '</td>'
        . '</tr>'
        . '</table>';
}

function MenuLink($strLink, $strTitle, $mnu = '', $aktif = '')
{

    if ($aktif . ".php" == $strLink) {
        $akt = "active";
    } else {
        $akt = '';
    }

    if (@$mnu != '') {
        $lnk = "&mn=$mnu";
    } else {
        $lnk = '';
    }
    //echo '<li><a href="'.$strLink.'">'.ucwords(strtolower($strTitle)).'</a></li>';
    $strLink = str_replace('?', '&', $strLink);
    $strLink = str_replace('.php', '', $strLink);
    echo '<li><a class="' . $akt . '" href="?vw=' . $strLink . $lnk . '">' . ucwords(strtolower($strTitle)) . '</a></li>';
    /*
	print
	'<tr>'
		.'<td>'
			.'<table width="100%" cellspacing="0" cellpadding="0">'
				.'<tr>'
					.'<td width="2%">'
						.'<div class="nav"><img src="images/sym-tick-red-bkrm-01.gif" width="20" height="20"></div>'
					.'</td>'
					.'<td>'
						.'<div class="nav"><a href="'.$strLink.'" target="mainFrame">'.$strTitle.'</a></div>'
					.'</td>'
				.'</tr>'
			.'</table>'
		.'</td>'
	.'</tr>';
     
     */
}

function MenuLogout()
{
    print
        '<tr>'
        . '<td>'
        . '<table width="100%" cellspacing="0" cellpadding="0">'
        . '<tr>'
        . '<td width="2%">'
        . '<div class="nav"><img src="images/sym-tick-red-bkrm-01.gif" width="20" height="20"></div>'
        . '</td>'
        . '<td>'
        . '<div class="nav"><a href="logout.php" onClick="return confirm(\'Adakah anda Pasti?\')">Keluar</a></div>'
        . '</td>'
        . '</tr>'
        . '</table>'
        . '</td>'
        . '</tr>';
}

function MenuLinkPopup($strPopup, $strTitle)
{
    print
        '<tr>'
        . '<td>'
        . '<table width="100%" cellspacing="0" cellpadding="0">'
        . '<tr>'
        . '<td width="2%">'
        . '<div class="nav">'
        . '<img src="images/sym-tick-red-bkrm-01.gif" width="20" height="20">'
        . '</div>'
        . '</td>'
        . '<td>'
        . '<div class="nav">';
    print '<a href="' . $strPopup . '" target="_blank" title="' . $strPopup . '">' . $strTitle . '</a>';
    print                    '</div>'
        . '</td>'
        . '</tr>'
        . '</table>'
        . '</td>'
        . '</tr>';
}

function MenuLinkSmallPopup($strPopup, $strTitle)
{
    print
        '<tr>'
        . '<td>'
        . '<table width="100%" cellspacing="0" cellpadding="0">'
        . '<tr>'
        . '<td width="2%">'
        . '<div class="nav">'
        . '<img src="images/sym-tick-red-bkrm-01.gif" width="20" height="20">'
        . '</div>'
        . '</td>'
        . '<td>'
        . '<div class="nav">'
        . '<a href="#" onclick="window.open(\''
        . $strPopup
        . '\',\'pop\',\'top=100, left=100, width=500, height=100, scrollbars=no, resizable=no, toolbars=no, location=no, menubar=no\');">'
        . $strTitle . '</a>'
        . '</div>'
        . '</td>'
        . '</tr>'
        . '</table>'
        . '</td>'
        . '</tr>';
}

function MenuManualPopup($strPopup, $strTitle)
{
    print
        '<tr>'
        . '<td>'
        . '<table width="100%" cellspacing="0" cellpadding="0">'
        . '<tr>'
        . '<td width="2%">'
        . '<div class="nav">'
        . '<img src="images/sym-tick-red-bkrm-01.gif" width="20" height="20">'
        . '</div>'
        . '</td>'
        . '<td>'
        . '<div class="nav">'
        . '<a href="#" onclick="window.open(\''
        . $strPopup
        . '\',\'pop\',\'top=100, left=100, width=900, height=500, scrollbars=yes, resizable=yes, toolbars=no, location=no, menubar=no\');">'
        . $strTitle . '</a>'
        . '</div>'
        . '</td>'
        . '</tr>'
        . '</table>'
        . '</td>'
        . '</tr>';
}
?>
<script>
    function selectCode() {
        c = document.forms['MyNetForm'].selCode;
        parent.mainFrame.location = "general.php?cat=" + c.options[c.selectedIndex].value;
    }

    function selectCodeACC() {
        c = document.forms['MyNetForm'].selCodeACC;
        parent.mainFrame.location = "generalACC.php?cat=" + c.options[c.selectedIndex].value;
    }

    function selectLap() {
        c = document.forms['MyNetForm'].selLap;
        document.location = "reports.php?cat=" + c.options[c.selectedIndex].value;
    }

    function selectSurat() {
        c = document.forms['MyNetForm'].selSurat;
        s = "memberList.php";
        if (c.options[c.selectedIndex].value == "C" || c.options[c.selectedIndex].value == "H") {
            s = "loanList.php";
        }
        if (c.options[c.selectedIndex].value == "D" || c.options[c.selectedIndex].value == "E") {
            s = "memberListT.php";
        }
        if (c.options[c.selectedIndex].value == "F") {
            s = "dividenList.php";
        }
        document.location = s + "?code=" + c.options[c.selectedIndex].value;
    }

    function selectPop(rpt) {
        window.open(rpt + ".php", "pop", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");
    }

    function selectAnggota(rpt) {
        if (rpt == "rptA4" || rpt == "rptA5" || rpt == "rptA6" || rpt == "rptA7" || rpt == "rptA8" || rpt == "rptA9" ||
            rpt == "rptA10" || rpt == "rptA11" || rpt == "rptA12" || rpt == "rptA13" || rpt == "rptA14" || rpt == "rptA15" || rpt == "rptDaftarAng") {
            window.open(rpt + ".php", "pop", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");
        } else {
            s = "selDateOpt.php";
            url = s + "?rpt=" + rpt;
            window.open(url, "pop", "top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");
        }
    }
</script>